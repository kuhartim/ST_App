<?php

class Responses {
    public static function server_error() {
        http_response_code(500);        
        echo json_encode(array("error" => "Internal Server error"));
    }

    public static function success($data=array()) {
        if(empty($data))
            $data = array("message" => "Success");

        http_response_code(200);        
        echo json_encode($data);
    }

    public static function unauthorized() {
        http_response_code(403);        
        echo json_encode(array("message" => "Unauthorized"));
    }

    public static function not_found() {
        http_response_code(404);        
        echo json_encode(array("message" => "Not Found"));
    }

    public static function bad_request($message="Bad Request") {
        http_response_code(400);        
        echo json_encode(array("message" => $message));
    }

    public static function method_not_allowed() {
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(array("message" => "Method Not Allowed"));
    }
}

?>