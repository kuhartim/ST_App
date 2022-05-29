<?php

include_once "../helpers/Responses.php";
include_once "../models/Users.php";

class Tokens {

    private static $secret = "secret";
    private static $tableName = "tokens";

    public static function create($id){
        try {
            
            // Create token header as a JSON string
            $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

            // Create token payload as a JSON string
            $payload = json_encode([
                'user_id' => intval($id), // user id
                'iat' => time(), // issued at time
                'exp' => 1 * 24 * 60 * 60 // expiration time
            ]);

            // Encode Header to Base64Url String
            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

            // Encode Payload to Base64Url String
            $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

            // Create Signature Hash
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, Tokens::$secret, true);

            // Encode Signature to Base64Url String
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            // Create JWT
            $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

            return $jwt;
        }
        catch(Exception $e) {
            Responses::server_error();
            return;
        }
    }

    private static function is_valid_format($token) {
        try {

            $steps = explode('.', $token);

            if (count($steps) != 3) {
                return false;
            }

            $step_one = $steps[1];

            $replace_minus = str_replace('-','+', $step_one);

            $replace_underscore = str_replace('_', '/', $replace_minus);

            $base64_decode = base64_decode($replace_underscore);

            $payload = json_decode($base64_decode, true);

            if (isset($payload["exp"])) {
                return true;
            }
            else {
                return false;
            }
        }
        catch(Exception $e) {
            return false;
        }
    }

    public static function validate($token) {
        try {

            if (!Tokens::is_valid_format($token)) {
                return null;
            }

            if (Tokens::is_in_blacklist($token)) {
                return null;
            }

            $sections = explode(".", $token);

            $payload = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))), true);

            if (intval($payload['iat']) + intval($payload['exp']) < time()) {
                return null;
            }

            $data_signature = str_replace(['+', '/', '='],['-', '_', ''], base64_encode(hash_hmac('sha256', $sections[0] . "." . $sections[1], Tokens::$secret, true)));

            if ($sections[2] === $data_signature) {
                return Users::find_user_by_id(intval($payload["user_id"]));
            }

            return null;
        }
        catch(Exception $e) {
            return false;
        }
    }

    private static function is_in_blacklist($token) {
        try {
            global $conn;
            
            $stmt = $conn->prepare("SELECT token FROM " . Tokens::$tableName . " WHERE token = '". $token ."'");

            if($stmt === false) {
                return true;
            }
            
            $execute_exec = $stmt->execute();
            
            if($execute_exec === false) {
                return true;
            }

            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            return !empty($data);
        }
        catch(Exception $e) {
            Responses::server_error();
        }
    }

    public static function add_to_blacklist($token) {
        try {
            if (!Tokens::is_valid_format($token)) {
                return false;
            }

            if (Tokens::is_in_blacklist(Auth::$token)) {
                return false;
            }

            global $conn;
            
            $stmt = $conn->prepare("
                INSERT INTO ". Tokens::$tableName ."(`token`, `expire`)
                VALUES(?, ?)");

            if($stmt === false) {
                Responses::server_error();
                return false;
            }
            
            $payload = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))), true);

            $timestamp = date("Y-m-d H:i:s", intval($payload['iat']) + intval($payload['exp']));
            
            $bind_param_exec = $stmt->bind_param("ss", $token, $timestamp);

            if($bind_param_exec === false) {
                Responses::server_error();
                return false;
            }
            
            $execute_exec = $stmt->execute();
            
            if($execute_exec === false) {
                Responses::server_error();
                return false;
            }

            return true;
        }
        catch(Exception $e) {
            Responses::server_error();
            return false;
        }
    }

}

?>