<?php
    /*
    *   Dateiupload
    */

    header('Access-Control-Allow-Origin: *');

    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    
    header("Access-Control-Allow-Headers: X-Requested-With, content-type");

    function startsWith( $haystack, $needle ) {
        $length = strlen( $needle );
        return substr( $haystack, 0, $length ) === $needle;
   }

    $uploadPath = "pre.txt";

    $json = file_get_contents('php://input');
    $array = json_decode($json, true);

    if(isset($_POST) && $array !== null) {

    
        if(!isset($array['replace']) & !isset($array['search'])) {
            exit(json_encode(array("error" => 1)));
        }
        $pre = fopen($uploadPath, "w");
        $str = "";
		if(isset($array['searchFrom'])) {
			$str = "SearchFrom:###:".$array['searchFrom']."\n";	
		}
        foreach($array['search'] as $i => $l) {
            $id = (intval($i) < 10) ? "0".$i : $i;
            $str = $str."Search-".$id.":###:".$l."\n";
            $str = $str."Replace-".$id.":###:".$array['replace'][$i]."\n";
			if(isset($array['choosed'])) {
				if(isset($array['choosed'][$i])) $str = $str."Choosed-".$id.":###:".$array['choosed'][$i]."\n";
			}
        }
        $success = fwrite($pre, $str);
        fclose($pre);
        exit(json_encode(array("write_status" => $success)));
    } else {
        $pre = fopen($uploadPath, "r");
        $search = array();
        $replace = array();
		$choosed = array();
		$searchFrom = null;
        if($pre) {
            while(($buffer = fgets($pre, 4096)) !== false) {
                if($buffer !== "") {
					if(startsWith($buffer, "SearchFrom")) {
						$searchFrom = substr(explode(":###:", $buffer)[1], 0);
						$searchFrom = substr($searchFrom, 0, (strlen($searchFrom) - 1));
					}
                    if(startsWith($buffer, "Search-")) {
                        $id = intval(substr(explode("-", $buffer)[1], 0, 2));
                        $search[$id] = substr(explode(":###:", $buffer)[1], 0);
						$search[$id] = substr($search[$id], 0 , (strlen($search[$id])-1));

                    }
                    if(startsWith($buffer, "Replace-")) {
                        $id = intval(substr(explode("-", $buffer)[1], 0, 2));
                        $replace[$id] = substr(explode(":###:", $buffer)[1], 0);
						$replace[$id] = substr($replace[$id], 0 , (strlen($replace[$id])-1));
                    }
					if(startsWith($buffer, "Choosed-")) {
						$id = intval(substr(explode("-", $buffer)[1], 0, 2));
						$choosed[$id] = substr(explode(":###:", $buffer)[1], 0);
						$choosed[$id] = substr($choosed[$id], 0 , (strlen($choosed[$id])-1));
					}
                }
            }
            if(!feof($pre)) {
                exit(json_encode(array("error" => "Endoffile")));
            }
            fclose($pre);
        } else {
            exit(json_encode(array("error" => "File not ethablished")));
        }
        exit(json_encode(array("search" => $search, "replace" => $replace, "searchFrom" => $searchFrom, "choosed" => $choosed))); 
    }

?>