<?php
	include_once "../helpers/Responses.php";
	include_once "../config/Database.php";
	include_once "../models/Users.php";
    include_once "../models/Tokens.php";
	include_once "../middleware/Auth.php";

	header("Access-Control-Allow-Origin: *");
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST, OPTIONS");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

	$conn = Database::get_connection();

	$request_method = $_SERVER["REQUEST_METHOD"];

	switch($request_method)
	{
		case 'POST':
			Auth::auth('post_method');
			break;

		case 'OPTIONS':
			Responses::success();
			break;

		default:
			Responses::method_not_allowed();
			break;
	}

	function post_method() {
        try {
            if (Tokens::add_to_blacklist(Auth::$token)) {
                Responses::success(["message" => "logged out"]);
            }
            else if(http_response_code() != 500) {
                Responses::bad_request("not in 'jwt' format or already logged out");
            }
        }
        catch(Exception $e) {
            Responses::server_error();
        }
	}

	
?>