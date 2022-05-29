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
			post_method();
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
			$data = json_decode(file_get_contents('php://input'), true);
			
			if (!isset($data["username"])) {
				Responses::bad_request("'username' is required");
				return;
			}

			if (!isset($data["password"])) {
				Responses::bad_request("'password' is required");
				return;
			}

			$username = $data["username"];

			$user = Users::find_user_by_username($username);

			if (is_null($user)) {
				Responses::not_found();
				return;
			}

			if (password_verify($data["password"], $user["password"])) {
				$token = Tokens::create($user["id"]);

				if (http_response_code() != 500) {
					Responses::success([
						"token" => $token,
						"expire" => 1 * 24 * 60 * 60
					]);
					return;
				}
				else {
					Responses::server_error();
					return;
				}
			}
			else {
				Responses::unauthorized();
				return;
			}

			Responses::not_found();
		}
		catch(Exception $e) {
			Responses::server_error();
		}
	}

	
?>