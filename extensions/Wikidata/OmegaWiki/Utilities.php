<?php
require_once("Wikidata.php");

function implodeFixed($values, $separator = ", ", $prefix = '"', $suffix = '"') {
	$result = $prefix . $values[0] . $suffix;
	
	for ($i = 1; $i < count($values); $i++)
		$result .= $separator . $prefix . $values[$i] . $suffix;
		
	return $result;
}


function wfMsg_sc($message) {
	$args=func_get_args();
	array_shift($args);
	global 
		$SiteContext;
	return wfMsgReal("${SiteContext}_${message}", $args, true);
}


?>
