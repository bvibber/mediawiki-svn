<?php

define('MEDIAWIKI', true );

require_once("../../../../StartProfiler.php");
require_once("../../../../LocalSettings.php");
require_once("../../php-tools/ProgressBar.php");
require_once("DatabaseUtilities.php");
require_once("Setup.php");

ob_end_flush();

function createIndexesForTable($tableName) {
	$handle = fopen("Create " . $tableName . " indices.sql", "r");
	$sql = "";
	
	while (!feof($handle)) {
		$line = fgets($handle);
		
		if (substr($line, 0, 2) != "--")	
			$sql .= $line;
	}
	
	$dbr =& wfGetDB(DB_MASTER);
	$queryResult = $dbr->query($sql);
	
	fclose($handle);
}

function recreateIndexesForTable($tableName) {
	echo "Dropping indices from table $tableName.\n";
	dropAllIndicesFromTable($tableName);
	echo "Creating new indices for table $tableName.\n";
	createIndexesForTable($tableName);
}

function recreateIndexesForTables($tableNames) {
	foreach ($tableNames as $tableName)
		recreateIndexesForTable($tableName);
}

global
	$beginTime, $wgCommandLineMode;

$beginTime = time();
$wgCommandLineMode = true;

recreateIndexesForTables(
	array(
		"bootstrapped_defined_meanings",
		"transactions",
		"translated_content",
		"uw_alt_meaningtexts",
		"uw_class_attributes",
		"uw_class_membership",
		"uw_collection_contents",
		"uw_collection_ns",
		"uw_defined_meaning",
		"uw_expression_ns",
		"uw_meaning_relations",
		"uw_option_attribute_options",
		"uw_option_attribute_values",
		"uw_syntrans",
		"uw_text_attribute_values",
		"uw_translated_content_attribute_values",
		"uw_url_attribute_values"
	)
);

$endTime = time();
echo("\n\nTime elapsed: " . durationToString($endTime - $beginTime)); 

?>
