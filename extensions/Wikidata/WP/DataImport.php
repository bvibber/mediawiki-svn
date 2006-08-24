<?php

define('MEDIAWIKI', true );
require_once("../../../LocalSettings.php");
require_once("../WiktionaryZ/Expression.php");
require_once("Setup.php");
require_once('SwissProtImport.php');
require_once('XMLImport.php');
require_once('2GoMappingImport.php');
require_once("ProgressBar.php");
require_once("UMLSImport.php");

// Uncomment following line for versioning support
require_once("../WiktionaryZ/Transaction.php");

ob_end_flush();

global
	$beginTime, $wgCommandLineMode, $numberOfBytes;

$beginTime = time();
$wgCommandLineMode = true;

$linkEC2GoFileName = "LinksEC2Go.txt";
$linkSwissProtKeyWord2GoFileName = "LinksSP2Go.txt";
$swissProtXMLFileName =  "uniprot_sprot.xml";
//$swissProtXMLFileName =  "100000lines.xml";

$umlsImport = importUMLSFromDatabase("localhost", "umls", "root", "");
$EC2GoMapping = importEC2GoMapping($linkEC2GoFileName);
$SP2GoMapping = importSwissProtKeyWord2GoMapping($linkSwissProtKeyWord2GoFileName);

importSwissProt($swissProtXMLFileName, $umlsImport->umlsCollectionId, $umlsImport->sourceAbbreviations['GO'], $EC2GoMapping, $SP2GoMapping);
//importSwissProt($swissProtXMLFileName, 18, 25, $EC2GoMapping, $SP2GoMapping);

$endTime = time();
echo "\nTime elapsed: " . durationToString($endTime - $beginTime); 

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

function importSwissProtEntries($fileHandle) {
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
