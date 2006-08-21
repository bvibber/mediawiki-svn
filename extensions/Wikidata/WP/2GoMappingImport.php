<?php
 
function importEC2GoMapping($fileHandle) {
	$mapping=array();

	while (!feof($fileHandle)) {
		$buffer = fgets($fileHandle);
	    $buffer = rtrim($buffer,"\n");
	    $currentPrefix = substr($buffer, 0, 1);
	    if(($currentPrefix != "!") and (substr($buffer, 0, 2) == "EC")) {
			$startPositionEC = strpos($buffer, "EC:");
			$endPositionEC = strpos($buffer, ">");
			$startPositionGO = strpos($buffer, "; GO:");
			$endPositionGO = strlen($buffer);
			$EC = substr($buffer, $startPositionEC + 3, $endPositionEC - $startPositionEC - 4);
			$GO = substr($buffer, $startPositionGO + 5, $endPositionGO - $startPositionGO - 5);

			$mapping[$EC] = $GO;   	
	    }
 	 }
	
//	foreach ($mapping as $EC => $GO) {
//		echo "$EC: $GO\n";
//	}

	return $mapping;	
}

function importSwissProtKeyWord2GoMapping($fileHandle) {
	$mapping=array();

	while (!feof($fileHandle)) {
		$buffer = fgets($fileHandle);
	    $buffer = rtrim($buffer,"\n");
	    $currentPrefix = substr($buffer, 0, 1);
	    if(($currentPrefix != "!") and (substr($buffer, 0, 5) == "SP_KW")) {
			$startPositionSP_KW = strpos($buffer, "SP_KW:");
			$endPositionSP_KW = strpos($buffer, ">");
			$startPositionGO = strpos($buffer, "; GO:");
			$endPositionGO = strlen($buffer);
			$SP_KW = substr($buffer, $startPositionSP_KW + 6, $endPositionSP_KW - $startPositionSP_KW - 7);
//			just keep 7 digit code (without description):
			$SP_KW = substr($SP_KW, 0, 7);
			$GO = substr($buffer, $startPositionGO + 5, $endPositionGO - $startPositionGO - 5);

			$mapping[$SP_KW] = $GO;   	
	    }
 	 }
	
//	foreach ($mapping as $SP_KW => $GO) {
//		echo "$SP_KW: $GO\n";
//	}

	return $mapping;	
}
?>
