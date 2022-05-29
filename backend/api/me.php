<?php
	include_once "../helpers/Responses.php";
	include_once "../config/Database.php";
	include_once "../middleware/Auth.php";

	header("Access-Control-Allow-Origin: *");
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: GET, OPTIONS");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

	$conn = Database::get_connection();

	$request_method = $_SERVER["REQUEST_METHOD"];

	switch($request_method)
	{
		case 'GET':
			Auth::auth('get_method');
			break;

		case 'OPTIONS':
			Responses::success();
			break;

		default:
			Responses::method_not_allowed();
			break;
	}

	function get_method() {
		try {
			$user = Auth::$user;
            unset($user["password"]);

            Responses::success($user);
		}
		catch(Exception $e) {
			Responses::server_error();
		}
	}

	
?>