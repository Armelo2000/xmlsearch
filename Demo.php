<?php
/*
libxml_use_internal_errors(TRUE);
$objXmlDocument = simplexml_load_file("Demo.xml");
if ($objXmlDocument === FALSE) {
    echo "There were errors parsing the XML file.\n";
    foreach(libxml_get_errors() as $error) {
        echo $error->message;
    }
    exit;
}
$objJsonDocument = json_encode($objXmlDocument);
$arrOutput = json_decode($objJsonDocument, TRUE);
echo "<pre>";
print_r($arrOutput);
*/

$doc = new DOMDocument();

//$yourXmlString = simplexml_load_file("Demo.xml");
$yourXmlString = file_get_contents("Demo.xml");

$doc->loadXML( $yourXmlString ); // or:
//$doc->load( $yourXmlUrl );

$xpath = new DOMXpath( $doc );
$nodes = $xpath->query( '//*' );

$nodeNames = array();
foreach( $nodes as $node )
{
    $nodeNames[ $node->nodeName ] = $node->nodeName;
}

var_dump( $nodeNames );
?>