<?php

define('MEDIAWIKI', true );
require_once("../../../LocalSettings.php");
require_once("Setup.php");
require_once("../WiktionaryZ/Expression.php");
require_once("../WiktionaryZ/Transaction.php");
require_once('SwissProtImport.php');
require_once('XMLImport.php');
require_once('2GoMappingImport.php');
require_once("UMLSImport.php");

ob_end_flush();

global
	$beginTime, $wgCommandLineMode, $wgUser, $numberOfBytes;

$beginTime = time();
$wgCommandLineMode = true;

/*
 * User IDs to use during the import of both UMLS and Swiss-Prot
 */
$nlmUserID = 8;
$sibUserID = 10;
//$nlmUserID = 1;
//$sibUserID = 1;

//$linkEC2GoFileName = "LinksEC2Go.txt";
//$linkSwissProtKeyWord2GoFileName = "LinksSP2Go.txt";
//$swissProtXMLFileName =  "uniprot_sprot.xml";
$swissProtXMLFileName =  "100000lines.xml";

$wgUser->setID($nlmUserID);
startNewTransaction($nlmUserID, 0, "UMLS Import");
echo "Importing UMLS\n";
//$umlsImport = importUMLSFromDatabase("localhost", "umls", "root", "nicheGod");//, array("NCI", "GO"));
$umlsImport = importUMLSFromDatabase("localhost", "umls", "root", "nicheGod", array("GO", "SRC", "NCI", "HUGO"));
//$umlsImport = importUMLSFromDatabase("localhost", "umls", "root", NULL, array("GO", "SRC", "NCI", "HUGO"));

//$EC2GoMapping = loadEC2GoMapping($linkEC2GoFileName);
//$SP2GoMapping = loadSwissProtKeyWord2GoMapping($linkSwissProtKeyWord2GoFileName);

$wgUser->setID($sibUserID);
startNewTransaction($sibUserID, 0, "Swiss-Prot Import");
echo "\nImporting Swiss-Prot\n";
//$umlsImport = new UMLSImportResult;
//$umlsImport->umlsCollectionId = 5;
//$umlsImport->sourceAbbreviations['GO'] = 30; 
//$umlsImport->sourceAbbreviations['HUGO'] = 69912;

importSwissProt($swissProtXMLFileName, $umlsImport->umlsCollectionId, $umlsImport->sourceAbbreviations['GO'], $umlsImport->sourceAbbreviations['HUGO'], $EC2GoMapping, $SP2GoMapping);
//importSwissProt($swissProtXMLFileName);

$endTime = time();
echo "\n\nTime elapsed: " . durationToString($endTime - $beginTime); 

//function echoNofLines($fileHandle, $numberOfLines) {
//	$i = 0;
//	do {
//		$buffer = fgets($fileHandle);
//		$buffer = rtrim($buffer,"\n");
//		echo $buffer;
//		$i += 1;
//	} while($i < $numberOfLines || strpos($buffer, '</entry>') === false);
//	echo "</uniprot>";
//}
//
//function echoLinesUntilText($fileHandle, $text) {
//	$found = false;
//	do {
//		$buffer = fgets($fileHandle);
//		$buffer = rtrim($buffer,"\n");
//		echo $buffer;
//		$found = strpos($buffer, $text) !== false;		
//	} while(!$found || strpos($buffer, '</entry>') === false);
//	echo "</uniprot>";
//}

?>
