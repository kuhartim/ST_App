<?php

class Database {
	private static $host  = 'localhost';
    private static $user  = 'root';
    private static $password   = "";
    private static $database  = "st"; 
    
    public static function get_connection() {
        try {		
            $conn = new mysqli(Database::$host, Database::$user, Database::$password, Database::$database);
            if($conn->connect_error) {
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            } else {
                return $conn;
            }
        }
        catch(Exception $e) {
            Responses::server_error();
        }
    }
}

?>