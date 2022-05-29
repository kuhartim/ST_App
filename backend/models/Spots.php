<?php

include_once "../helpers/Responses.php";
include_once "../helpers/Functions.php";

class Spots {

    private static $tableName = "spots";
    private static $tableNameImages = "images";
    private static $tableNameImagesSpots = "spots_images";

    public static function create($userId, $lon, $lat, $title, $description){
        try {
            global $conn;
            
            // spots
            $stmt = $conn->prepare("
                INSERT INTO ". Spots::$tableName ."(`publisher`, `lon`, `lat`, `title`, `description`)
                VALUES(?, ?, ?, ?, ?)");

            if($stmt === false) {
                Responses::server_error();
                return null;
            }
            
            $title = htmlspecialchars(strip_tags($title));
            $description = htmlspecialchars(strip_tags($description));
            
            $bind_param_exec = $stmt->bind_param("iddss", $userId, $lon, $lat, $title, $description);

            if($bind_param_exec === false) {
                Responses::server_error();
                return null;
            }
            
            $execute_exec = $stmt->execute();
            
            if($execute_exec === false) {
                Responses::server_error();
                return null;
            }

            $spot_id = $stmt -> insert_id;
            // spots

            // images
            if(!Spots::save_images_from_files($spot_id, true)) return null;
           

            // images


            $spot = array(
                "publisher" => $userId,
                "lon" => $lon,
                "lat" => $lat,
                "title" => $title,
                "description" => $description,
                "id" => $spot_id
            );

            return $spot;
        }
        catch(Exception $e) {
            Responses::server_error();
            return null;
        }
    }

    public static function update($user_id, $data){
        try {
            $removed_images = $data["removed_images"];
            unset($data["removed_images"]);

            $spot_id = $data["spot_id"];
            unset($data["spot_id"]);

            $existing_spot = Spots::find_by_id($spot_id);
            if(is_null($existing_spot)) {
                return null;
            }

            if ($existing_spot["publisher_id"] != $user_id) {
                return null;
            }

            global $conn;
            
            // spots
            $stmt = $conn->prepare("UPDATE `". Spots::$tableName ."` SET `".implode("` = ?, `", array_keys($data))."` = ? WHERE `id` = ?");

            if($stmt === false) {
                Responses::server_error();
                return null;
            }
            

            $vals = "";
            foreach($data as $key => $value) {
                $vals .= Functions::type_to_char(gettype($value));
            }

            $vals .= "i";
            array_push($data, $spot_id);
            
            $bind_param_exec = $stmt->bind_param($vals, ...array_values($data));

            if($bind_param_exec === false) {
                Responses::server_error();
                return null;
            }
            
            $execute_exec = $stmt->execute();

            if($execute_exec === false) {
                Responses::server_error();
                return null;
            }
            // spots

            // images
            foreach($removed_images as $value) {
                if(!Spots::delete_image($value, $spot_id)) {
                    return null;
                }
            }

            // images
            if(!Spots::save_images_from_files($spot_id, false)) return null;

            $data["id"] = $data[0];
            unset($data[0]);
            $spot = $data;

            return $spot;
        }
        catch(Exception $e) {
            Responses::server_error();
            return null;
        }
    }

    private static function save_images_from_files($spot_id, $is_POST) {
        try {
            global $conn;

            if ( isset( $_FILES["images"] ) && !empty( $_FILES["images"]["name"] ) ) {
                $is_ok = true;
                foreach($_FILES["images"]["tmp_name"] as $file_name) {
                    if (!realpath($file_name)) {
                        $is_ok = false;
                    }
                }
                foreach($_FILES["images"]["error"] as $file_error) {
                    if ($file_error !== 0) $is_ok = false;
                }
                if ($is_ok) {
                    // everything okay, do process
                    $myFile = $_FILES['images'];
                    $fileCount = count($myFile["name"]);
                    for ($i = 0; $i < $fileCount; $i++) {
                        $image_url = Spots::upload_image($i, $is_POST);

                        if (is_null($image_url)) {
                            return false;
                        }

                        $stmt = $conn->prepare("
                        INSERT INTO ". Spots::$tableNameImages ."(`url`)
                        VALUES(?)");
        
                        if($stmt === false) {
                            Responses::server_error();
                            return false;
                        }
                        
                        $image = htmlspecialchars(strip_tags($image_url));
                        
                        $bind_param_exec = $stmt->bind_param("s", $image);
        
                        if($bind_param_exec === false) {
                            Responses::server_error();
                            return false;
                        }
                        
                        $execute_exec = $stmt->execute();
                        
                        if($execute_exec === false) {
                            Responses::server_error();
                            return false;
                        }
        
                        $image_id = $stmt -> insert_id;
        
                        $stmt = $conn->prepare("
                        INSERT INTO ". Spots::$tableNameImagesSpots ."(`image`, `spot`)
                        VALUES(?, ?)");
        
                        if($stmt === false) {
                            Responses::server_error();
                            return false;
                        }
                        
                        $bind_param_exec = $stmt->bind_param("ii", $image_id, $spot_id);
        
                        if($bind_param_exec === false) {
                            Responses::server_error();
                            return false;
                        }
                        
                        $execute_exec = $stmt->execute();
                        
                        if($execute_exec === false) {
                            Responses::server_error();
                            return false;
                        }
                    }
                }
                else {
                    Responses::Server_error();
                    return null;
                }
            }

            return true;
        }
        catch(Exception $e) {
            Responses::server_error();
            return false;
        }
    }

    private static function delete_image($id, $spot_id) {
        try {

            //SELECT url FROM `images` WHERE id = 7
            global $conn;
            $stmt = $conn->prepare("SELECT `spot` as spot_id FROM ". Spots::$tableNameImagesSpots ." WHERE `image` = ?");

            if($stmt === false) {
                Responses::server_error();
                return false;
            }

            $id = htmlspecialchars(strip_tags($id));

            $bind_param_exec = $stmt->bind_param("i", $id);

            if($bind_param_exec === false) {
                Responses::server_error();
                return false;
            }
            
            $execute_exec = $stmt->execute();
            
            if($execute_exec === false) {
                Responses::server_error();
                return false;
            }

            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if(empty($data)) {
                return true;
            }

            if($data["spot_id"] != $spot_id) {
                Responses::unauthorized();
                return null;
            }
            //
            
            $stmt = $conn->prepare("SELECT `url` FROM ". Spots::$tableNameImages ." WHERE `id` = ?");

            if($stmt === false) {
                Responses::server_error();
                return false;
            }

            $id = htmlspecialchars(strip_tags($id));

            $bind_param_exec = $stmt->bind_param("i", $id);

            if($bind_param_exec === false) {
                Responses::server_error();
                return false;
            }
            
            $execute_exec = $stmt->execute();
            
            if($execute_exec === false) {
                Responses::server_error();
                return false;
            }

            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if(empty($data)) {
                return true;
            }

            $image_url = $data["url"];
            
            if(!unlink($image_url)) {
                Responses::server_error();
                return false;
            }

            $stmt = $conn->prepare("DELETE FROM " . Spots::$tableNameImages . " WHERE `id` = ?");

            if($stmt === false) {
                Responses::server_error();
                return false;
            }

            $bind_param_exec = $stmt->bind_param("i", $id);

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

    public static function delete($user_id, $spot_id) {
        try {
            $existing_spot = Spots::find_by_id($spot_id);
            if(is_null($existing_spot)) {
                return 0;
            }

            if ($existing_spot["publisher_id"] != $user_id) {
                return 1;
            }

            global $conn;

            $stmt = $conn->prepare("SELECT i.id FROM `spots_images` si INNER JOIN `images` i ON i.id = si.image WHERE si.spot = ?");

            if($stmt === false) {
                Responses::server_error();
                return 2;
            }

            $bind_param_exec = $stmt->bind_param("i", $spot_id);

            if($bind_param_exec === false) {
                Responses::server_error();
                return 2;
            }
            
            $execute_exec = $stmt->execute();
            
            if($execute_exec === false) {
                Responses::server_error();
                return 2;
            }

            $result = $stmt->get_result();

            while($row = $result->fetch_assoc()) {
                if(!Spots::delete_image(intval($row["id"]), $spot_id)) {
                    Responses::server_error();
                    return 2;
                }
            }
            //
            
            $stmt = $conn->prepare("DELETE FROM " . Spots::$tableName . " WHERE `id` = ?");

            if($stmt === false) {
                Responses::server_error();
                return 2;
            }

            $spot_id = intval(htmlspecialchars(strip_tags($spot_id)));

            $bind_param_exec = $stmt->bind_param("i", $spot_id);

            if($bind_param_exec === false) {
                Responses::server_error();
                return 2;
            }
            
            $execute_exec = $stmt->execute();
            
            if($execute_exec === false) {
                Responses::server_error();
                return 2;
            }

            return 3;
        }
        catch(Exception $e) {
            Responses::server_error();
            return 2;
        }
    }

    private static function upload_image($index, $is_POST) {
        try {
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($_FILES["images"]["name"][$index]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $target_file = Functions::str_lreplace("." . $imageFileType, "_" . strval(time()) . "." . $imageFileType, $target_file);

            //. strval(time())

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["images"]["tmp_name"][$index]);
            if($check == false) {
                Responses::bad_request("File is not an image");
                return null;
            }
            

            // Check file size
            if ($_FILES["images"]["size"][$index] > 500000) {
                Responses::bad_request("File is too large [max size is 500KB]");
                return null;
            }

            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                Responses::bad_request("Only 'png', 'jpg', 'jpeg' formats are allowed");
                return null;
            }

            if (!file_exists($target_dir)) {
    
                // Create a new file or direcotry
                mkdir($target_dir, 0777, true);
            }

            

            if($is_POST) {
                if (move_uploaded_file($_FILES["images"]["tmp_name"][$index], $target_file)) {
                    return $target_file;
                } else {
                    Responses::server_error();
                    return null;
                }
            }
            else {
                /* Open a file for writing */
                $fp = fopen($target_file, "w");
                $fp_tmp = fopen($_FILES["images"]["tmp_name"][$index], "r");

                /* Read the data 1 KB at a time
                and write to the file */
                while ($file_data = fread($fp_tmp, 1024))
                    fwrite($fp, $file_data);

                /* Close the streams */
                fclose($fp);
                return $target_file;
            }
        }
        catch(Exception $e) {
            Responses::server_error();
            return null;
        }
        
    }

    public static function find_by_id($id) {
        try {
            global $conn;
            
            $stmt = $conn->prepare("SELECT s.id, s.lat, s.lon, s.title, s.description, s.created, u.username as publisher, s.publisher as publisher_id FROM `spots` s INNER JOIN `users` u ON s.publisher = u.id WHERE s.id = ?");

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
            
            $stmt = $conn->prepare("SELECT i.url, i.id FROM `spots_images` si INNER JOIN `images` i ON i.id = si.image WHERE si.spot = ?");

            if($stmt === false) {
                Responses::server_error();
                return null;
            }

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

            $images = array();

            while($row = $result->fetch_assoc()) {
                array_push($images, ["image" => Spots::get_image_base64($row["url"]), "id" => $row["id"]]);
            }

           $data["images"] = $images;

            return $data;
        }
        catch(Exception $e) {
            Responses::server_error();
            return null;
        }
    }

    private static function get_image_base64($path) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public static function get_all($page=1, $per_page=10, $sort_by="title", $sort_type="asc", $search="", $user_id=null) {
        try {
            global $conn;

            $sort_type = htmlspecialchars(strip_tags($sort_type));
            $sort_by = htmlspecialchars(strip_tags($sort_by));
            
            $stmt = $conn->prepare("SELECT s.lat, s.lon, s.title, s.description, s.id, s.created, u.username as publisher FROM `spots` s INNER JOIN `users` u ON s.publisher = u.id WHERE s.title LIKE ?" . ($user_id ? " AND s.publisher = ? " : " ") . "ORDER BY $sort_by $sort_type LIMIT ? OFFSET ?");

            if($stmt === false) {
                Responses::server_error();
                return null;
            }

            $page = intval(htmlspecialchars(strip_tags($page)));
            $per_page = intval(htmlspecialchars(strip_tags($per_page)));
            $user_id = $user_id ? intval(htmlspecialchars(strip_tags($user_id))) : null;
            $search = htmlspecialchars(strip_tags($search));
            

            $offset = ($page - 1) * $per_page;

            $searchString = "%" . $search . "%";

            $data_for_bind = [$searchString, $per_page, $offset];
            if($user_id) {
                $data_for_bind = [$searchString, $user_id, $per_page, $offset];
            }


            $bind_param_exec = $stmt->bind_param("s". ($user_id ? "i" : "") ."ii", ...$data_for_bind);

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

            $spots = array();

            while($data = $result->fetch_assoc()) {
                array_push($spots, $data);
            }

            $stmt = $conn->prepare("SELECT COUNT(*) as num_spots FROM `spots` WHERE title LIKE ? " . ($user_id ? "AND publisher = ?" : ""));

            if($stmt === false) {
                Responses::server_error();
                return null;
            }

            $data_for_bind = [$searchString];
            if($user_id) {
                $data_for_bind = [...$data_for_bind, $user_id];
            }

            $bind_param_exec = $stmt->bind_param("s" . ($user_id ? "i" : "") , ...$data_for_bind);

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

            $num_spots = $data["num_spots"];
            $max_page = ceil($num_spots / $per_page);
            
            return ["spots" => $spots, "page" => $page, "per_page" => $per_page, "sort_by" => $sort_by, "sort_type" => $sort_type, "max_page" => $max_page ? $max_page : 1];
        }
        catch(Exception $e) {
            echo $e;
            Responses::server_error();
            return null;
        }
    }

}

?>