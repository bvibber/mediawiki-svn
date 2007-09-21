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
$dc = "uw";

$purpose = "WebSite";
$prefixes = retrieve_datasets();

foreach ($prefixes as $prefix) {
	$dataSet = new WikiDataSet($prefix);
	recreateIndexesForTable($dataSet->alternativeDefinitions, $purpose);
	recreateIndexesForTable($dataSet->bootstrappedDefinedMeanings, $purpose);
	recreateIndexesForTable($dataSet->classAttributes, $purpose);
	recreateIndexesForTable($dataSet->classMemberships, $purpose);
	recreateIndexesForTable($dataSet->collection, $purpose);
	recreateIndexesForTable($dataSet->collectionMemberships, $purpose);
	recreateIndexesForTable($dataSet->definedMeaning, $purpose);
	recreateIndexesForTable($dataSet->expression, $purpose);
	recreateIndexesForTable($dataSet->linkAttributeValues, $purpose);
	recreateIndexesForTable($dataSet->meaningRelations, $purpose);
	recreateIndexesForTable($dataSet->optionAttributeOptions, $purpose);
	recreateIndexesForTable($dataSet->optionAttributeValues, $purpose);
	recreateIndexesForTable($dataSet->syntrans, $purpose);
	recreateIndexesForTable($dataSet->textAttributeValues, $purpose);
	recreateIndexesForTable($dataSet->translatedContent, $purpose);
	recreateIndexesForTable($dataSet->translatedContentAttributeValues, $purpose);
	recreateIndexesForTable($dataSet->transactions, $purpose);
}

$endTime = time();
echo("\n\nTime elapsed: " . durationToString($endTime - $beginTime)); 


