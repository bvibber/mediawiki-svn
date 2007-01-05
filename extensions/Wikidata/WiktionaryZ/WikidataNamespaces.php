<?php

global
	$expressionNameSpaceId, $definedMeaningNameSpaceId;
	
$expressionNameSpaceId = getNameSpaceIDByName('WiktionaryZ');	
$definedMeaningNameSpaceId = getNameSpaceIDByName('DefinedMeaning');	
	
function getNameSpaceIDByName($name) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT ns_id " .
		" FROM namespace_names " .
		" WHERE ns_name=". $dbr->addQuotes($name)
	);
	
	if ($row = $dbr->fetchObject($queryResult))
		return $row->ns_id;
	else
		return -1; 
}

?>
