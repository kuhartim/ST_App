<?php
	include_once "../helpers/Responses.php";
	include_once "../config/Database.php";
	include_once "../models/Spots.php";
	include_once "../middleware/Auth.php";
	include_once "../helpers/Functions.php";

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
            get_method();
			break;

		case 'POST':
			Auth::auth('post_method');
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
        try {
			if (!isset($_GET["id"])) {
				if(!isset($_GET["page"])) {
					Responses::bad_request("'page' is required");
					return;
				}
				if(!isset($_GET["per_page"])) {
					Responses::bad_request("'per_page' is required");
					return;
				}
				if(!isset($_GET["sort_by"])) {
					Responses::bad_request("'sort_by' is required");
					return;
				}
				else {
					if(!in_array($_GET["sort_by"], ["title", "description", "created"])) {
						Responses::bad_request("'sort_by' must be one of 'title', 'description', 'created'");
						return;
					}
				}
				if(!isset($_GET["sort_type"])) {
					Responses::bad_request("'sort_type' is required");
					return;
				}
				else {
					if(!in_array($_GET["sort_type"], ["asc", "desc", "ASC", "DESC"])) {
						Responses::bad_request("'sort_type' must be one of 'desc', 'asc'");
						return;
					}
				}
				$page = $_GET["page"];
				$per_page = $_GET["per_page"];
				$sort_by = $_GET["sort_by"];
				$sort_type = $_GET["sort_type"];
				$search = isset($_GET["search"]) ? $_GET["search"] : "";
				$user_id = isset($_GET["user_id"]) ? $_GET["user_id"] : "";

				if($user_id == "current") {
					Auth::auth(null);
					if(http_response_code() != 200 || is_null(Auth::$user)) {
						return;
					}
					$user_id = Auth::$user["id"];
				}

				$spots = Spots::get_all($page, $per_page, $sort_by, $sort_type, $search, $user_id);
				if (!is_null($spots)){
					Responses::success($spots);
				}
			}
			else {
				$spot = Spots::find_by_id($_GET["id"]);
				if (!is_null($spot))
					Responses::success($spot);
			}
			
		}
		catch(Exception $e) {
			Responses::server_error();
		}
	}

	function delete_method() {
		$data = json_decode(file_get_contents('php://input'), true);

		if(!isset($data["spot_id"])) {
			Responses::bad_request("'spot_id' is required");
			return;
		}

		$spot = Spots::delete(Auth::$user['id'], $data["spot_id"]);

		if(http_response_code() != 500) {
			if($spot == 1) {
				Responses::unauthorized();
			}
			else if($spot == 0) {
				Responses::not_found();
			}
			else {
				Responses::success(["message" => "Spot successfully deleted"]);
			}
		}
	}

	function put_method() {
		// TO JE SAMO COPY PASTE IZ CREATE, NRED UPDATE TOREJ UPDATE SLIK TUD (TREBA BO GET METODO MAU PREDELAT D SE ID SLIKE VRNE)
		try {

			global $_PUT;

			Functions::parse_put();

			if(!isset($_PUT["spot_id"])) {
				Responses::bad_request("'spot_id' is required");
				return;
			}
			
			$removed_images = [];

			if(isset($_PUT["removed_images"])) {
				$removed_images_json = json_decode($_PUT["removed_images"]);
				if(is_array($removed_images_json) && !empty($removed_images_json)) {
					$removed_images = $removed_images_json;
				}
				else {
					$removed_images = [];
				}
			}

			$valid_keys = ["title", "description", "spot_id"];

			foreach($_PUT as $key => $value) {
				if(!in_array($key, $valid_keys)) {
					unset($_PUT[$key]);
				}
			}

			if (isset($_PUT["title"])) {
				if (strlen($_PUT["title"]) > 255) {
					Responses::bad_request("max size for 'title' is 255");
					return;
				}
			}

			if (isset($_PUT["description"])) {
				if (strlen($_PUT["description"]) > 1024) {
					Responses::bad_request("max size for 'description' is 1024");
					return;
				}
			}

			$_PUT["removed_images"] = $removed_images;

			$user = Auth::$user;

			$_PUT["spot_id"] = intval($_PUT["spot_id"]);
			
			$spot = Spots::update($user["id"], $_PUT);
			if(http_response_code() != 500) {
				if(is_null($spot)) {
					Responses::unauthorized();
				}
				else {
					Responses::success($spot);
				}
			}
		}
		catch(Exception $e) {
			Responses::server_error();
		}
	}

	function post_method() {
		try {
			if (!isset($_POST["lon"])) {
				Responses::bad_request("'lon' is required");
				return;
			}

            if (!is_numeric($_POST["lon"])) {
				Responses::bad_request("'lon' must be double");
				return;
			}

			if (!isset($_POST["lat"])) {
				Responses::bad_request("'lat' is required");
				return;
			}

            if (!is_numeric($_POST["lat"])) {
				Responses::bad_request("'lat' must be double");
				return;
			}

			if (!isset($_POST["title"])) {
				Responses::bad_request("'title' is required");
				return;
			}

            if (!isset($_POST["description"])) {
				Responses::bad_request("'description' is required");
				return;
			}

			$title = $_POST["title"];

			if (strlen($title) > 255) {
				Responses::bad_request("max size for 'title' is 255");
				return;
			}

			$description = $_POST["description"];

			if (strlen($description) > 1024) {
				Responses::bad_request("max size for 'description' is 1024");
				return;
			}

            $user = Auth::$user;
			
			$spot = Spots::create($user["id"], doubleval($_POST["lon"]), doubleval($_POST["lat"]), $_POST["title"], $_POST["description"]);
			if (!is_null($spot))
				Responses::success($spot);
			
		}
		catch(Exception $e) {
			Responses::server_error();
		}
	}

	
?>