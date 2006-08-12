<?php

define('MEDIAWIKI', true );
require_once("../../../LocalSettings.php");
require_once("../WiktionaryZ/Expression.php");
require_once("Setup.php");
require_once('SwissProtImport.php');
require_once('XMLImport.php');

ob_end_flush();
$beginTime = time();

global
	$wgCommandLineMode;
	
$wgCommandLineMode = true;

//$file = "OneEntry.xml";
//$file = "10000lines.xml";
$file = "uniprot_sprot.xml";
//$file = "uniprot_sprot.dat";

$fileHandle = fopen($file, "r");

importEntriesFromXMLFile($fileHandle);

//echoNofLines($fileHandle, 10000);
//echoLinesUntilText($fileHandle, ")-");
fclose($fileHandle);

$endTime = time();
echo "Time elapsed: " . ($endTime - $beginTime); 

function echoNofLines($fileHandle, $numberOfLines) {
	$i = 0;
	do {
		$buffer = fgets($fileHandle);
		$buffer = rtrim($buffer,"\n");
		echo $buffer;
		$i += 1;
	} while($i < $numberOfLines || strpos($buffer, '</entry>') === false);
	echo "</uniprot>";
}

function echoLinesUntilText($fileHandle, $text) {
	$found = false;
	do {
		$buffer = fgets($fileHandle);
		$buffer = rtrim($buffer,"\n");
		echo $buffer;
		$found = strpos($buffer, $text) !== false;		
	} while(!$found || strpos($buffer, '</entry>') === false);
	echo "</uniprot>";
}

function	importSwissProtEntries($fileHandle) {
//	$selectLanguageId = 'SELECT language_id FROM language_names WHERE language_name ="English"';
//	$dbr =& wfGetDB(DB_MASTER);
//	$queryResult = $dbr->query($selectLanguageId);
//	
//	if ($languageIdObject = $dbr->fetchObject($queryResult)){
//		$languageId = $languageIdObject->language_id;
//	}

	$languageId = 85;
	$collectionId = bootstrapCollection("Swiss-Prot", $languageId);

//	while (!feof($fileHandle)) {
	for ($i = 1; $i <= 1000; $i++)  {
		$entry = new SwissProtImportEntry;
		$entry->import($fileHandle);
		$entry->echoEntry();
		$identifier = $entry->getIdentifier();

		$descriptionAttribute = $entry->getDescriptionAttribute();
		print_r($descriptionAttribute);
		$expression = findExpression($descriptionAttribute->protein->name, $languageId);
		if (!$expression) {
			$expression = createExpression($descriptionAttribute->protein->name, $languageId);
			$definedMeaningId = createNewDefinedMeaning($expression->id, $languageId, $descriptionAttribute->protein->name);

			addDefinedMeaningToCollection($definedMeaningId, $collectionId, $descriptionAttribute->protein->name);
		}
	}
}

function getPrefixAnalysis($fileHandle){
	$prefixArray=array();

	while (!feof($fileHandle)) {
	    $buffer = fgets($fileHandle);
	    $buffer = rtrim($buffer,"\n");
	    $currentPrefix = substr($buffer, 0, 2);
			
			if ($currentPrefix != ""){
	 	    if (!array_key_exists($currentPrefix, $prefixArray)) {
	 	    	$prefixArray[$currentPrefix]=1;	
	 	    }
	 	    else {
	 	    	$prefixArray[$currentPrefix]+=1;
	 	    }
			} 
 	 }
	
	echo "Number of prefixes: " . count($prefixArray) . "\n";
	foreach ($prefixArray as $prefix => $value) {
		echo $prefix . ": $value\n";
	}
}

?>
