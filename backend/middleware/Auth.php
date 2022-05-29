<?php

include_once "../helpers/Responses.php";
include_once "../models/Tokens.php";

class Auth {

    public static $user = null;
    public static $token = null;

    private static function get_authorization_header(){
        try {
            $headers = null;
            if (isset($_SERVER['Authorization'])) {
                $headers = trim($_SERVER["Authorization"]);
            }
            else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
                $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
            } elseif (function_exists('apache_request_headers')) {
                $requestHeaders = apache_request_headers();
                // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
                $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
                //print_r($requestHeaders);
                if (isset($requestHeaders['Authorization'])) {
                    $headers = trim($requestHeaders['Authorization']);
                }
            }
            return $headers;
        }
        catch(Exception $e) {
            Responses::server_error();
            return null;
        }
    }


    private static function get_bearer_token() {
        try {
            $headers = Auth::get_authorization_header();
            if (!empty($headers)) {
                if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                    return $matches[1];
                }
            }
            return null;
        }
        catch(Exception $e) {
            Responses::server_error();
            return null;
        }
    }

    public static function auth($callback) {
        try {
            Auth::$token = Auth::get_bearer_token();

            if (!Auth::$token) {
                Responses::unauthorized();
                return;
            }

            Auth::$user = Tokens::validate(Auth::$token);

            if (is_null(Auth::$user)) {
                Responses::unauthorized();
                return;
            }

            if($callback)
                call_user_func($callback);
        }
        catch(Exception $e) {
            Responses::server_error();
        }
    }
}

?>