<?php
	
	//Edit("AnixePlus.de", "Demo", "id");
	function Edit($filename, $oldValue, $newValue, $attr = ""){
		// Das ist das erste element von XML
		$xmlroot = simplexml_load_file($filename); //"DummyFile.xml"

		foreach($xmlroot as $childOfRoot){ //Loop over all Child of root (channel - programme)
			writeAttribute($childOfRoot, $attr, $oldValue, $newValue);
			
			foreach($childOfRoot->children() as $child){
				writeAttribute($childOfRoot, $attr, $oldValue, $newValue);
				if(count($child->children()) == 0){
					//echo $child[0].'<br>';
					if($child[0] == $oldValue){
						$child[0] = $newValue;
					}
				}else{
					foreach($child->children() as $childOfChild){ //Loop over credits element
						writeAttribute($childOfRoot, $attr, $oldValue, $newValue);
						if(!$childOfChild->hasChildren()){
							if($childOfChild == $oldValue){
								$childOfChild[0] = $newValue;
							}
						}
					}
				}
				
			}

		}

		file_put_contents("result.xml", $xmlroot->saveXML());

		return;
	}

	function writeAttribute($node, $attributeName, $oldvalue, $newvalue) {
		if($attributeName == "") return;
		//check first if the Attribute exist
		if(isset($node[$attributeName]) && ($node[$attributeName] == $oldvalue)){
			//The attribute name exist 
			$node[$attributeName] = $newvalue;
		}
	}

	function prematch($pattern, $search){
		
		preg_match_all($pattern, $search, $match, PREG_OFFSET_CAPTURE);
		print_r($match);
		
	}

	function replace_regEx($inputFile, $pattern, $newStr, $outputFile="replace.xml"){

		$xml = file_get_contents($inputFile);
	
		$newXml = preg_replace($pattern, $newStr, $xml);

		file_put_contents($outputFile, $newXml);
		
	}

/*
** $element_name - the name of the elements to search for;
** $xml - the XML document to search through;
** $content_only - if true, the tags enclosing the named element are
**     discarded. If false, the whole pattern match is returned, and
**     the enclosing tags are preserved. Defaults to false.
*/
function element_set($element_name, $xml, $content_only = false) {
    if ($xml == false) {
        return false;
    }
    $found = preg_match_all('#<'.$element_name.'(?:\s+[^>]+)?>' .
            '(.*?)</'.$element_name.'>#s',
            $xml, $matches, PREG_PATTERN_ORDER);
    if ($found != false) {
        if ($content_only) {
            return $matches[1];  //ignore the enlosing tags
        } else {
            return $matches[0];  //return the full pattern match
        }
    }
    // No match found: return false.
    return false;
}
	function value_in($element_name, $xml, $content_only = true) {
		if ($xml == false) {
			return false;
		}
		$found = preg_match('#<'.$element_name.'(?:\s+[^>]+)?>(.*?)'.
				'</'.$element_name.'>#s', $xml, $matches);
		if ($found != false) {
			if ($content_only) {
				return $matches[1];  //ignore the enclosing tags
			} else {
				return $matches[0];  //return the full pattern match
			}
		}
		// No match found: return false.
		return false;
	}

/*
** $element_name - the name of the element to extract the attributes
**     from;
** $xml - the XML sample to search for the named element. 
*/
function element_attributes($element_name, $xml) {
    if ($xml == false) {
        return false;
    }
    // Grab the string of attributes inside an element tag.
    $found = preg_match_all('#<'.$element_name.
            '\s+([^>]+(?:"|\'))\s?/?>#',
            $xml, $matches);
			
    if ($found != 0) {
        $attribute_array = array();
        $attribute_string = $matches[1];
		//print_r($attribute_string);
		$i = 0;
		foreach($matches[1] as $key=>$attribute_string){
			// Match attribute-name attribute-value pairs.
			$found = preg_match_all(
					'#([^\s=]+)\s*=\s*(\'[^<\']*\'|"[^<"]*")#',
					$attribute_string, $matches, PREG_SET_ORDER);
			if ($found != 0) {
				// Create an associative array that matches attribute
				// names to attribute values.
				foreach ($matches as $attribute) {
					$attribute_array[$i++] =
							substr($attribute[2], 1, -1);
				}
				//return $attribute_array;
			}
		}
		return $attribute_array;
    }
    // Attributes either weren't found, or couldn't be extracted
    // by the regular expression.
    return false;
}
	$xml = file_get_contents("DummyFile.xml");
	$xmlcontent = file_get_contents("DummyFile.xml");
	$result = value_in("country", $xml, true);
	$attr = element_attributes('channel', $xmlcontent);
	//print_r($attr);
	foreach($attr as $key=>$val){
		//echo 'Key: '.$key.' - Value: '.$val;
	}
	//replace_regEx("DummyFile.xml", '/(<country>G[\s\S]+?<\/country>)/', "<country>Angleterre</country>");

	$str = '/(<country>G[\s\S]+?<\/country>)/';
	$element = "";
	$Strfound = preg_match_all($str, '#<'.$element.'(?:\s+[^>]+)?>' .
            '(.*?)</'.$element.'>#s',
             $matches);
	
	print_r($matches);

	exit();

    $f = array();
	$tagname = array();

	$allTags = simplexml_load_file("DummyFile.xml");
	//print_r($allTags);
	/*
    foreach($allTags as $tag){
		$tag[] = $tag->getName();
    $f[] = "%(<$tag.*?>)(.*?)(<\/$tag.*?>)%is";
    } */

	$tags = array();
	foreach($allTags as $tag){
		$temp = $tag;
		$tags[] = $tag->getName();
		$childCount = count($tag->children());
		for($i=0; $i<$childCount; $i++){
			while(count($temp->children()) != 0){
				$temp = $temp->children();
				$tags[] = $temp->getName();
			}
		}

    }
	//print_r($tags);
    //if(sizeof($f)) $str = preg_replace($f, ($stripContent ? '' : '${2}'), $str);

	libxml_use_internal_errors(TRUE);
	$objXmlDocument = simplexml_load_file("DummyFile.xml");
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
	//print_r($arrOutput);
	print_r($objJsonDocument);
 
	

	 
?>





