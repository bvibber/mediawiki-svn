<?php

/**
 * Use this script to recreate indexes for all configured wiki data tables. 
 * Recreating indexes is profitable because:
 * 
 * 1) Different indexes are better for different purposes
 * 2) After a while index fragmentation can occur, degrading performance
 * 
 * The script takes two parameters: 
 * 1) dataset: for which dataset should the indexes be create, if ommitted recrate for all datasets
 * 2) purpose: can be either WebSite or MassUpdate
 * 
 * Usage example:
 *   prompt> php "RecreateIndexes.php" --dataset=uw --purpose=WebSite 
 */

define('MEDIAWIKI', true );

require_once("../../../../StartProfiler.php");
require_once("../../../../LocalSettings.php");
require_once("../../php-tools/ProgressBar.php");
require_once("DatabaseUtilities.php");
require_once("Setup.php");
require_once("../../OmegaWiki/WikiDataTables.php");

ob_end_flush();

function parseCommandLine() {
	global
		$argv;
	
	$result = array();
	
	foreach ($argv as $arg) {
		if (substr($arg, 0, 2) == '--') {
			$arg = substr($arg, 2);
			$equalsPosition = strpos($arg, "=");
			
			if ($equalsPosition !== false)
				$result[substr($arg, 0, $equalsPosition)] = substr($arg, $equalsPosition + 1);
			else
				$result[$arg] = null;
		}
	}
	
	return $result;
}

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

function recreateIndexesForTable(Table $table, $purpose) {
	echo "Dropping indices from table " . $table->getIdentifier() . ".\n";
	dropAllIndicesFromTable($table->getIdentifier());

	echo "Creating new indices for table " . $table->getIdentifier() . ".\n";
	addIndexesForTable($table, $purpose);	
}

global
	$beginTime, $wgCommandLineMode;

$beginTime = time();
$wgCommandLineMode = true;

$options = parseCommandLine();

if (isset($options["purpose"]))
	$purpose = $options["purpose"];
else
	die("Missing argument: --purpose\nPossible values: WebSite and MassUpdate");

if (isset($options["dataset"]))
	$prefixes = array($options["dataset"]);
else
	$prefixes = retrieve_datasets();

foreach ($prefixes as $prefix) {
	$dataSet = new WikiDataSet($prefix);
	
	foreach ($dataSet->getAllTables() as $table)
		recreateIndexesForTable($table, $purpose);
}

$endTime = time();
echo("\n\nTime elapsed: " . durationToString($endTime - $beginTime)); 


