<?php

define('MEDIAWIKI', true );
require_once("../../../includes/ProfilerStub.php");
require_once("../../../LocalSettings.php");
echo "DataImport #0";
require_once("Setup.php");
echo "DataImport #1";
require_once("../OmegaWiki/WikiDataAPI.php");
echo "DataImport #2";
require_once("../OmegaWiki/Transaction.php");
echo "DataImport #3";
require_once('SwissProtImport.php');
echo "DataImport #4";
require_once('XMLImport.php');
echo "DataImport #5";
require_once('2GoMappingImport.php');
echo "DataImport #6";
require_once("UMLSImport.php");
echo "DataImport #7";
require_once("../../../includes/Namespace.php");
require_once("../../../includes/Defines.php");

ob_end_flush();

global
	$beginTime, $wgCommandLineMode, $wgUser, $numberOfBytes, $wdDefaultViewDataSet;

$beginTime = time();
$wgCommandLineMode = true;
$wdDefaultViewDataSet = 'sp';

/*
 * User IDs to use during the import of both UMLS and Swiss-Prot
 */
//$nlmUserID = 8;
// check the user ids as provided in the database
$sibUserID = 2;
//$nlmUserID = 1;
//$sibUserID = 1;

//$linkEC2GoFileName = "LinksEC2Go.txt";
//$linkSwissProtKeyWord2GoFileName = "LinksSP2Go.txt";
//$swissProtXMLFileName =  "C:\Documents and Settings\mulligen\Bureaublad\uniprot_sprot.xml";
//$swissProtXMLFileName =  "100000lines.xml";
$swissProtXMLFileName =  "C:\Documents and Settings\mulligen\Bureaublad\SPentriesForWPTest.xml";

//$wgUser->setID($nlmUserID);
//startNewTransaction($nlmUserID, 0, "UMLS Import");
//echo "Importing UMLS\n";
//$umlsImport = importUMLSFromDatabase("localhost", "umls", "root", "nicheGod");//, array("NCI", "GO"));
//$umlsImport = importUMLSFromDatabase("localhost", "umls", "root", "nicheGod", array("GO", "SRC", "NCI", "HUGO"));
//$umlsImport = importUMLSFromDatabase("localhost", "umls", "root", NULL, array("GO", "SRC", "NCI", "HUGO"));

//$EC2GoMapping = loadEC2GoMapping($linkEC2GoFileName);
//$SP2GoMapping = loadSwissProtKeyWord2GoMapping($linkSwissProtKeyWord2GoFileName);

$wgUser->setID($sibUserID);
startNewTransaction($sibUserID, 0, "Swiss-Prot Import");
#echo "\nImporting Swiss-Prot\n";
#$nsstore=wfGetNamespaceStore();
#print_r($nsstore->nsarray);
#"Namespace id for expression=" . Namespace::getIndexForName('expression');

//$umlsImport = new UMLSImportResult;
//$umlsImport->umlsCollectionId = 5;
//$umlsImport->sourceAbbreviations['GO'] = 30; 
//$umlsImport->sourceAbbreviations['HUGO'] = 69912;

//importSwissProt($swissProtXMLFileName, $umlsImport->umlsCollectionId, $umlsImport->sourceAbbreviations['GO'], $umlsImport->sourceAbbreviations['HUGO'], $EC2GoMapping, $SP2GoMapping);
importSwissProt($swissProtXMLFileName);

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
