<?php

include_once "../helpers/Responses.php";

class Users {

    private static $tableName = "users";

    public static function create($username, $password){
        try {
            global $conn;

            $existing_user = Users::find_user_by_username($username);
            if(!is_null($existing_user)) {
                return null;
            }
            
            $stmt = $conn->prepare("
                INSERT INTO ". Users::$tableName ."(`username`, `password`)
                VALUES(?, ?)");

            if($stmt === false) {
                Responses::server_error();
                return null;
            }
            
            $username = htmlspecialchars(strip_tags($username));
            $password = htmlspecialchars(strip_tags($password));
            $password_hash = password_hash($password,  PASSWORD_DEFAULT);
            
            $bind_param_exec = $stmt->bind_param("ss", $username, $password_hash);

            if($bind_param_exec === false) {
                Responses::server_error();
                return null;
            }
            
            $execute_exec = $stmt->execute();
            
            if($execute_exec === false) {
                Responses::server_error();
                return null;
            }

            $user = array(
                "username" => $username
            );

            return $user;
        }
        catch(Exception $e) {
            Responses::server_error();
            return null;
        }
    }

    public static function update($id, $password){
        try {
            global $conn;

            $stmt = $conn->prepare("UPDATE ". Users::$tableName . " SET `password` = ? WHERE `id` = ?");

            if($stmt === false) {
                Responses::server_error();
                return null;
            }
            
            $password = htmlspecialchars(strip_tags($password));
            $id = htmlspecialchars(strip_tags($id));
            $password_hash = password_hash($password,  PASSWORD_DEFAULT);
            
            $bind_param_exec = $stmt->bind_param("si", $password_hash, $id);

            if($bind_param_exec === false) {
                Responses::server_error();
                return null;
            }
            
            $execute_exec = $stmt->execute();
            
            if($execute_exec === false) {
                Responses::server_error();
                return null;
            }

            $user = array(
                "id" => $id
            );

            return $user;
        }
        catch(Exception $e) {
            Responses::server_error();
            return null;
        }
    }

    public static function delete($id) {
        try {
            $existing_user = Users::find_user_by_id($id);
            if(is_null($existing_user)) {
                return null;
            }

            global $conn;
            
            $stmt = $conn->prepare("DELETE FROM `users` WHERE `id` = ?");

            if($stmt === false) {
                Responses::server_error();
                return null;
            }

            $id = htmlspecialchars(strip_tags($id));

            $bind_param_exec = $stmt->bind_param("i", $id);

            if($bind_param_exec === false) {
                Responses::server_error();
                return null;
            }
            
            $execute_exec = $stmt->execute();
            
            if($execute_exec === false) {
                Responses::server_error();
                return null;
            }

            return true;
        }
        catch(Exception $e) {
            Responses::server_error();
            return null;
        }
    }

    public static function find_user_by_username($username) {
        try {
            global $conn;
            
            $stmt = $conn->prepare("SELECT * FROM " . Users::$tableName . " WHERE username = ?");

            if($stmt === false) {
                Responses::server_error();
                return null;
            }

            $username = htmlspecialchars(strip_tags($username));

            $bind_param_exec = $stmt->bind_param("s", $username);

            if($bind_param_exec === false) {
                Responses::server_error();
                return null;
            }
            
            $execute_exec = $stmt->execute();
            
            if($execute_exec === false) {
                Responses::server_error();
                return null;
            }

            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if(empty($data)) {
                return null;
            }

            $data["id"] = intval($data["id"]);

            return $data;
        }
        catch(Exception $e) {
            Responses::server_error();
            return null;
        }
    }

    public static function find_user_by_id($id) {
        try {
            global $conn;
            
            $stmt = $conn->prepare("SELECT * FROM " . Users::$tableName . " WHERE id = ?");

            if($stmt === false) {
                Responses::server_error();
                return null;
            }

            $id = htmlspecialchars(strip_tags($id));

            $bind_param_exec = $stmt->bind_param("i", $id);

            if($bind_param_exec === false) {
                Responses::server_error();
                return null;
            }
            
            $execute_exec = $stmt->execute();
            
            if($execute_exec === false) {
                Responses::server_error();
                return null;
            }

            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if(empty($data)) {
                return null;
            }

            $data["id"] = intval($data["id"]);

            return $data;
        }
        catch(Exception $e) {
            Responses::server_error();
            return null;
        }
    }

}

?>