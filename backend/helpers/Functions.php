<?php

class Functions {
    public static function str_lreplace($search, $replace, $subject) {
        $pos = strrpos($subject, $search);
    
        if($pos !== false)
        {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }
    
        return $subject;
    }

    public static function type_to_char($str) {
        switch($str) {
            case "string": return "s";
            case "integer": return "i";
            case "float": return "f";
            case "double": return "d";
            case "boolean": return "b";
        }
    }

    public static function parse_put() {
        global $_PUT;

        /* PUT data comes in on the stdin stream */
        $putdata = fopen("php://input", "r");

        /* Open a file for writing */
        // $fp = fopen("myputfile.ext", "w");

        $raw_data = '';

        /* Read the data 1 KB at a time
        and write to the file */
        while ($chunk = fread($putdata, 1024))
            $raw_data .= $chunk;

        /* Close the streams */
        fclose($putdata);

        // Fetch content and determine boundary
        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

        if(empty($boundary)){
            parse_str($raw_data,$data);
            $GLOBALS[ '_PUT' ] = $data;
            return;
        }

        // Fetch each part
        $parts = array_slice(explode($boundary, $raw_data), 1);
        $data = array();

        foreach ($parts as $part) {
            // If this is the last part, break
            if ($part == "--\r\n") break;

            // Separate content from headers
            $part = ltrim($part, "\r\n");
            list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

            // Parse the headers list
            $raw_headers = explode("\r\n", $raw_headers);
            $headers = array();
            foreach ($raw_headers as $header) {
                list($name, $value) = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }

            // Parse the Content-Disposition to get the field name, etc.
            if (isset($headers['content-disposition'])) {
                $filename = null;
                $tmp_name = null;
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                list(, $type, $name) = $matches;

                //Parse File
                if( isset($matches[4]) )
                {
                    //if labeled the same as previous, skip
                    if( isset( $_FILES[ $matches[ 2 ] ] ) )
                    {
                        continue;
                    }

                    //get filename
                    $filename = $matches[4];

                    //get tmp name
                    $filename_parts = pathinfo( $filename );
                    $tmp_name = tempnam( ini_get('upload_tmp_dir'), $filename_parts['filename']);

                    //populate $_FILES with information, size may be off in multibyte situation
                    
                    $lst_two = substr($matches[2], -2);
                    if (strcmp($lst_two, "[]") == 0) {
                        $att_name = substr($matches[2], 0, -2);
                        if (!isset($_FILES[$att_name])) {
                            $_FILES[$att_name] = array(
                                "error" => array(),
                                "name" => array(),
                                "tmp_name" => array(),
                                "size" => array(),
                                "type" => array()
                            );
                        }

                        $file_data = array(
                            'error'=>0,
                            'name'=>$filename,
                            'tmp_name'=>$tmp_name,
                            'size'=>strlen( $body ),
                            'type'=>$value
                        );

                        array_push($_FILES[$att_name]["error"], $file_data["error"]);
                        array_push($_FILES[$att_name]["name"], $file_data["name"]);
                        array_push($_FILES[$att_name]["tmp_name"], $file_data["tmp_name"]);
                        array_push($_FILES[$att_name]["size"], $file_data["size"]);
                        array_push($_FILES[$att_name]["type"], $file_data["type"]);
                    }
                    else {
                        $_FILES[ $matches[ 2 ] ] = array(
                            'error'=>0,
                            'name'=>$filename,
                            'tmp_name'=>$tmp_name,
                            'size'=>strlen( $body ),
                            'type'=>$value
                        );
                    }

                    //place in temporary directory
                    file_put_contents($tmp_name, $body);
                }
                //Parse Field
                else
                {
                    $data[$name] = substr($body, 0, strlen($body) - 2);
                }
            }

        }
        $GLOBALS[ '_PUT' ] = $data;
        return;
    }
}

?>