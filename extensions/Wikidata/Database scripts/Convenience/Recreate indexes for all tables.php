<?php

define('MEDIAWIKI', true );

require_once("../../../../StartProfiler.php");
require_once("../../../../LocalSettings.php");
require_once("../../php-tools/ProgressBar.php");
require_once("DatabaseUtilities.php");
require_once("Setup.php");

ob_end_flush();

function createIndexesForTable($dc,$tableName) {
	$handle = fopen("Create uw_" . $tableName . " indices.sql", "r");
	$sql = "";
	
	while (!feof($handle)) {
		$line = fgets($handle);
		
		if (substr($line, 0, 2) != "--")	
			$sql .= $line;
	}
	
	$sql = str_replace("%dc%", $dc, $sql);
	
	$dbr =& wfGetDB(DB_MASTER);
	$queryResult = $dbr->query($sql);
	
	fclose($handle);
}

function recreateIndexesForTable($dc, $tableName) {
	echo "Dropping indices from table " . $dc . "_" . $tableName . ".\n";
	dropAllIndicesFromTable($dc . "_" . $tableName);
	echo "Creating new indices for table " . $dc . "_" . $tableName . ".\n";
	createIndexesForTable($dc,$tableName);
}

function recreateIndexesForTables($dc, $tableNames) {
	foreach ($tableNames as $tableName){
		recreateIndexesForTable($dc,$tableName);
	}
}

global
	$beginTime, $wgCommandLineMode;

$beginTime = time();
$wgCommandLineMode = true;
$dc = "sp";

recreateIndexesForTables( "sp",
	array(
		"bootstrapped_defined_meanings",
		"transactions",
		"translated_content",
		"alt_meaningtexts",
		"class_attributes",
		"class_membership",
		"collection_contents",
		"collection_ns",
		"defined_meaning",
		"expression_ns",
		"meaning_relations",
		"option_attribute_options",
		"option_attribute_values",
		"syntrans",
		"text_attribute_values",
		"translated_content_attribute_values",
		"url_attribute_values"
	)
);

$endTime = time();
echo("\n\nTime elapsed: " . durationToString($endTime - $beginTime)); 

?>
