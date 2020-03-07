<?php

function XML2Array(SimpleXMLElement $parent){
    $array = array();

    foreach ($parent as $name => $element) {
        ($node = & $array[$name])
            && (1 === count($node) ? $node = array($node) : 1)
            && $node = & $node[];

        $node = $element->count() ? XML2Array($element) : trim($element);
    }

    return $array;
}

/* HOW TO USE: 

	** 	LOAD THIS FILE USING "require_once"
	** 	LOAD THE XML IN A VARIABLE, USING file_get_contents, AS: 
	**	**	$properties = file_get_contents(example.properties);
	** 	CALL THE FUNCTION XML2Array();
	**	**	PUT THE VARIABLE INTO PARAMNS
	**	**	**	XML2Array($properties);
	**	RETURN IS AN ARRAY
	**	** print_r(XML2Array($properties)); SHOW THE XML INTO ARRAY
	**	**	** ALSO IS POSSIBLE PUT THE ARRAY IN A VARIABLE, AS: $array = XML2Array($properties);
	
	
	
	/* EXAMPLE 
	
	
	$buffer = file_get_contents("root/properties/database.properties");
	$properties   = simplexml_load_string($buffer);
	$array = XML2Array($properties);
	$array = array($properties->getName() => $array);
	
	print_r($array);
	
	
	
	
	
	
	
	
	*/

?>