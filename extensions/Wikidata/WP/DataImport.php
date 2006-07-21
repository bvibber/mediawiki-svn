<?php

define('MEDIAWIKI', true );
require_once("../../../LocalSettings.php");
require_once("../WiktionaryZ/Expression.php");
require_once("Setup.php");
require_once('SwissProtImport.php');
require_once('XMLImport.php');

$beginTime = time();

//$file = "OneEntry.xml";
$file = "10000lines.xml";
//$file = "uniprot_sprot.xml";
//$file = "uniprot_sprot.dat";

$fileHandle = fopen($file, "r");

$dbr =& wfGetDB(DB_SLAVE);	
importEntriesFromXMLFile($fileHandle, $dbr);

//echoLines($fileHandle, 100000);
fclose($fileHandle);

$endTime = time();
echo "Time elapsed: " . ($endTime - $beginTime); 

function echoLines($fileHandle, $numberOfLines) {
	$i = 0;
	do {
		$buffer = fgets($fileHandle);
		$buffer = rtrim($buffer,"\n");
		echo $buffer;
		$i += 1;		
	} while($i < $numberOfLines || strpos($buffer, '</entry>') === false);
	echo "</uniprot>";
}

function	importSwissProtEntries($fileHandle) {
	$selectLanguageId = 'SELECT language_id FROM language_names WHERE language_name ="English"';
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query($selectLanguageId);
	
	if ($languageIdObject = $dbr->fetchObject($queryResult)){
		$languageId = $languageIdObject->language_id;
	}

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
			$definedMeaningId = createNewDefinedMeaning($expression->id, $expression->revisionId, $languageId, $descriptionAttribute->protein->name);

			addDefinedMeaningToCollection($definedMeaningId, $collectionId, $descriptionAttribute->protein->name, $expression->revisionId);
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
