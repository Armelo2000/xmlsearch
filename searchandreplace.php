<?php
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

header("Access-Control-Allow-Headers: X-Requested-With, content-type");
$replace = array();
$search = array();
$dir = "xmls/";

$debug = false; //Nur auf false benutzen. Bei true funktioniert die Weitergabe der geschriebenen Datei nicht mehr

$replacedFileName = "../../../Brot1.xml"; //Ergibt den Dateinamen nach dem Ersetzen. Kann geändert werden.
//Momentan liegt die Datei in xmls/Brot1.xml wenn sie den Namen der Datei nun so abändern: "../Brot1.xml" wird die Datei in das Verzeichnis verschoben in dem der Ordner xmls ist also eins nach vorn. das nutzen von ../ können sie beliebig oft wiederholen.

if(isset($_POST)) {
    $json = file_get_contents('php://input');
    $array = json_decode($json, true);
    //var_dump($array);

    if(!isset($array['replace']) & !isset($array['search'])) {
        exit(json_encode(array("error" => 1)));
    }

    $fileName = (isset($array['fileName'])) ? $array['fileName'] : scandir($dir, 1)[0];
    $fileName = $dir.$fileName;
    $replace = $array['replace'];
    $search = $array['search'];
	$choosed = $array['choosed'];

    $from = date("d.m.Y", strtotime($array['from_date']));
    $to = date("d.m.Y", strtotime($array['to_date']));
    $dates = array();
    array_push($dates ,date("d.m.Y", strtotime($from.' -2 day')));
    array_push($dates, date("d.m.Y", strtotime($from.' -1 day')));
    //array_push($from);
    //array_push($to);
    array_push($dates, date("d.m.Y", strtotime($to.' +1 day')));
    array_push($dates, date("d.m.Y", strtotime($to.' +2 day')));
    array_push($dates, date("d.m.Y", strtotime($to.' +3 day')));
    array_push($dates, date("d.m.Y", strtotime($to.' +4 day')));
    array_push($dates, date("d.m.Y", strtotime($to.' +5 day')));

    //$xml = fopen($fileName, "r+");
    $rows = "";
    if($debug)echo "$fileName";
	if($debug)echo "$replacedFileName";
    if(file_exists($fileName)) {
        /*while(($buffer = fgets($xml)) !== false) {
            if($buffer !== null && $buffer !== "") {
                array_push($rows, $buffer);
            }
        }*/
        if($debug)var_dump("EXISTS");
        $rows = file_get_contents($fileName);
      //  var_dump($rows);
        /*if(!feof($xml)) {
            exit("Unerwarteter Fehler");
        }*/
        //fclose($xml);
    }

    //Suchen und ersetzen beginnen
    //var_dump($rows);
	$rowsTop = "";
    if(!empty($rows)) {
        //var_dump($search);
        //var_dump($replace);
        if($debug)echo "NOT EMPTY";
		
		if(isset($array['searchFrom']) && !empty($array['searchFrom'])) {
				$index = strpos($rows, $array['searchFrom']);
				if($index != false) {
					$rowsTop = substr($rows, 0, ($index + strlen($array['searchFrom'])));
					$rows = substr($rows, ($index + strlen($array['searchFrom'])));
				}
		}
		
        $irMax = 1;
        foreach($dates as $d) {
            if($debug)echo "$d";
            $is = 0;
			
			if(isset($search[0])) {
				$s = $search[0];
				if(trim($s) == "" || $s === "" || $s == " " || $s == "   "|| empty($s)) continue;
                //echo "$s";
                $searchPat = preg_replace("/#year#/", date("y", strtotime($d)), $s);
                //echo "$searchPat";
                $searchPat = preg_replace("/#month#/", date("m", strtotime($d)), $searchPat);
				//echo "$searchPat";
                $searchPat = preg_replace("/#day#/", date("d", strtotime($d)), $searchPat);
                //echo "$irMax $is $searchPat";
                //$replaced = preg_replace( "/\n/", "\\n", $replace[$is]);
                //$replaced = preg_replace( "/\t/", "\\t", $replaced);
				$searchPat = "/".trim($searchPat)."/";
				if($debug)echo "$searchPat";
				
				if($debug)preg_match( $searchPat , $rows, $matches, PREG_OFFSET_CAPTURE);
				if($debug)var_dump($matches);
				
				if(isset($choosed[0]) && $choosed[0] == true) {
					$rowsTop = preg_replace($searchPat, $replace[0], $rowsTop); 
				} else {
					$rows = preg_replace($searchPat, $replace[0], $rows);
				}
				//$rows = preg_replace("\n", "\n", $rows);
			}


        }
		
		foreach($search as $s) {
                //if($is >= $irMax) break;
				if($is == 0) {
					$is++;
					continue;
				}
				if(trim($s) == "" || $s === "" || $s == " " || $s == "   "|| empty($s)) continue;
                //echo "$s";
                //echo "$irMax $is $searchPat";
                //$replaced = preg_replace( "/\n/", "\\n", $replace[$is]);
                //$replaced = preg_replace( "/\t/", "\\t", $replaced);
				$searchPat = "/".trim($s)."/";
				if($debug)echo "$searchPat";
				
				if($debug)preg_match( $searchPat , $rows, $matches, PREG_OFFSET_CAPTURE);
				if($debug)var_dump($matches);
				
				if(isset($choosed[$is]) && $choosed[$is] == true) {
					$rowsTop = preg_replace($searchPat, $replace[$is], $rowsTop);	
				} else {
					$rows = preg_replace($searchPat, $replace[$is], $rows);
				}
                
				//$rows = preg_replace("\n", "\n", $rows);
                $is++;
        }
		$rows = $rowsTop.$rows;
    } else {
        if($debug)echo "EMPTY";
    }
	
	
    if($debug)var_dump($rows);
	file_put_contents($dir.$replacedFileName, trim($rows));
    exit(json_encode(array("fileName" => $replacedFileName)));
}
//echo json_encode("HI");
?>