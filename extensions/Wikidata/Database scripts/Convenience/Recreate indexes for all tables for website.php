<?php

define('MEDIAWIKI', true );

require_once("../../../../StartProfiler.php");
require_once("../../../../LocalSettings.php");
require_once("../../php-tools/ProgressBar.php");
require_once("DatabaseUtilities.php");
require_once("Setup.php");
require_once("../../OmegaWiki/WikiDataTables.php");

ob_end_flush();

/*
 * This function wil retrieve a list of the data sets defined in this
 * database and return it as an array
 */
function retrieve_datasets(){
	$prefixes = array();
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("select set_prefix from wikidata_sets");
	while ($datasetRecord = $dbr->fetchObject($queryResult) ) {
		array_push( $prefixes, $datasetRecord->set_prefix );
	}
	return $prefixes;
}

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
	createIndexesForTable($dc, $tableName);
}

function addIndexesForTable($table, $purpose) {
	$tableIndexes = $table->getIndexes($purpose);
	$indexes = array();
	
	foreach ($tableIndexes as $tableIndex) {
		$index = array();
		
		foreach ($tableIndex->getColumns() as $column) {
			$indexColumn = '`' . $column->getIdentifier() . '`';
			$length = $column->getLength();
			
			if ($length != null)
				$indexColumn .= " (" . $length . ")";
				
			$index[] = $indexColumn; 
		}
		
		$indexes[$tableIndex->getName()] = $index;
	}
	
	addIndexes($table->getIdentifier(), $indexes);	
}

function recreateIndexesForTableNew(Table $table, $purpose) {
	echo "Dropping indices from table " . $table->getIdentifier() . ".\n";
	dropAllIndicesFromTable($table->getIdentifier());

	echo "Creating new indices for table " . $table->getIdentifier() . ".\n";
	addIndexesForTable($table, $purpose);	
}

function recreateIndexesForTables($dc, $tableNames) {
	foreach ($tableNames as $tableName)
		recreateIndexesForTable($dc, $tableName);
}

global
	$beginTime, $wgCommandLineMode;

$beginTime = time();
$wgCommandLineMode = true;
$dc = "uw";

$tables = array(
	"translated_content",
	"alt_meaningtexts",
	"class_attributes",
	"class_membership",
	"collection_contents",
	"collection",
	"defined_meaning",
	"meaning_relations",
	"option_attribute_options",
	"option_attribute_values",
	"syntrans",
	"text_attribute_values",
	"translated_content_attribute_values",
	"url_attribute_values"
);
					
$purpose = "WebSite";
$prefixes = retrieve_datasets();

foreach($prefixes as $prefix) {
	$dataSet = new WikiDataSet($prefix);
	recreateIndexesForTableNew($dataSet->bootstrappedDefinedMeanings, $purpose);
	recreateIndexesForTableNew($dataSet->expression, $purpose);
	recreateIndexesForTableNew($dataSet->transactions, $purpose);
	
	recreateIndexesForTables($prefix, $tables);
}

$endTime = time();
echo("\n\nTime elapsed: " . durationToString($endTime - $beginTime)); 


