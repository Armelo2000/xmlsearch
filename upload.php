<?php
    /*
    *   Dateiupload
    */

    header('Access-Control-Allow-Origin: *');

    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    
    header("Access-Control-Allow-Headers: X-Requested-With, content-type");

    $uploadPath = "xmls/";

    if(isset($_POST)) {
		echo "TEST";
		//exit();
        if(isset($_FILES)) {
            //echo ini_get('upload_max_filesize');
            if($_FILES['xml']['size'] > 0) {
                if(!file_exists($uploadPath)) {
                    mkdir($uploadPath);
                }
                if(file_exists($uploadPath.$_FILES['xml']['name']) && !isset($_POST['approved']) || (isset($_POST['approved']) && $_POST['approved'] == false)) {
                    exit(json_encode(array("error" => 0)));
                }
                move_uploaded_file($_FILES['xml']['tmp_name'], $uploadPath . basename($_FILES['xml']['name']));
                exit(json_encode(array("fileName" => $_FILES['xml']['name'])));
                echo "Datei hochgeladen.";
                //header("Location: ../");
            } else {
                die("Die hochgeladene Datei ist leer.");
            }
        }
    }
?>