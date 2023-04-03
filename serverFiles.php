<?php
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With, content-type");

$dir = "xmls/";
$files = scandir($dir);
$returnFiles = array();

foreach($files as $f) {
    if(strcmp($f, '.') === 0 || strcmp($f, '..') === 0) continue;
    array_push($returnFiles, $f);
}

echo json_encode($returnFiles);

?>