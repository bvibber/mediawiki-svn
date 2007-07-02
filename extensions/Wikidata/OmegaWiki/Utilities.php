<?php

function implodeFixed($values, $separator = ", ", $prefix = '"', $suffix = '"') {
	$result = $prefix . $values[0] . $suffix;
	
	for ($i = 1; $i < count($values); $i++)
		$result .= $separator . $prefix . $values[$i] . $suffix;
		
	return $result;
}

?>
