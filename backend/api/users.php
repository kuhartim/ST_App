<?php
	include_once "../helpers/Responses.php";
	include_once "../config/Database.php";
	include_once "../models/Users.php";
	include_once "../middleware/Auth.php";
	include_once "../models/Tokens.php";

	header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

	$conn = Database::get_connection();

	$request_method = $_SERVER["REQUEST_METHOD"];

	switch($request_method)
	{
		case 'GET':
			Auth::auth('get_method');
			break;

		case 'POST':
			post_method();
			break;

		case 'PUT':
			Auth::auth('put_method');
			break;
			
		case 'DELETE':
			Auth::auth('delete_method');
			break;

		case 'OPTIONS':
			Responses::success();
			break;

		default:
			Responses::method_not_allowed();
			break;
	}

	function get_method() {
		$user = Auth::$user;
		unset($user["password"]);
		Responses::success($user);
	}

	function delete_method() {
		$user = Users::delete(Auth::$user['id']);

		if(http_response_code() != 500) {
			if(is_null($user)) {
				Responses::not_found();
			}
			else {
				Responses::success(["message" => "User successfully deleted"]);
			}
		}
	}

	function put_method() {
		$data = json_decode(file_get_contents('php://input'), true);
		$valid_keys = ["password", "confirm_password"];

		foreach($data as $key => $value) {
			if(!in_array($key, $valid_keys)) {
				unset($data[$key]);
			}
		}

		if(empty($data)) {
			Responses::bad_request("Request is empty or have unsupported fields. You can only send 'password' and 'confirm_password' fields.");
			return;
		}

		if(isset($data["password"])) {
			if (strlen($data["password"]) > 255) {
				Responses::bad_request("max size for 'password' is 255");
				return;
			}

			if (!isset($data["confirm_password"])) {
				Responses::bad_request("'confirm_password' is required if password is present");
				return;
			}

			if ($data["password"] !== $data["confirm_password"]) {
				Responses::bad_request("'confirm_password' doesn't match 'password'");
				return;
			}
		}
		else {
			Responses::bad_request("'password' is required");
			return;
		}


		$user = Users::update(Auth::$user["id"], $data["password"]);
		if (!is_null($user))
			Responses::success($user);
		
	}

	function post_method() {
		try {
			$data = json_decode(file_get_contents('php://input'), true);
			
			if (!isset($data["username"])) {
				Responses::bad_request("'username' is required");
				return;
			}

			if (!isset($data["password"])) {
				Responses::bad_request("'password' is required");
				return;
			}

			if (!isset($data["confirm_password"])) {
				Responses::bad_request("'confirm_password' is required");
				return;
			}

			if ($data["password"] !== $data["confirm_password"]) {
				Responses::bad_request("confirm_password doesn't match password");
				return;
			}

			$username = $data["username"];

			if (strlen($username) > 255) {
				Responses::bad_request("max size for 'username' is 255");
				return;
			}

			$password = $data["password"];

			if (strlen($password) > 255) {
				Responses::bad_request("max size for 'password' is 255");
				return;
			}
			
			$user = Users::create($username, $password);
			if (!is_null($user))
				Responses::success($user);
			else if (http_response_code() != 500) {
				Responses::bad_request("User with that username already exists");
			}
		}
		catch(Exception $e) {
			Responses::server_error();
		}
	}

	
?>