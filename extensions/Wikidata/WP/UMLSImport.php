<?php
define('MEDIAWIKI', true );
require_once("../../../LocalSettings.php");
require_once("../WiktionaryZ/Expression.php");
require_once("Setup.php");

ob_end_flush();
$beginTime = time();

global
	$wgCommandLineMode;
	
$wgCommandLineMode = true;
openDatabase("localhost", "umls", "root", "");

$languageId = 85;
$umlsCollectionId = bootstrapCollection("UMLS", $languageId, "");
$sourceAbbreviations = loadSourceAbbreviations();
$isoLanguages = loadIsoLanguages();

foreach ($sourceAbbreviations as $sab => $source) {
//	echo "$sab: $source\n";
	if (strcmp($source, "ICPC, Dutch Translation, 1993") == 0) {
		$collectionId = bootstrapCollection($source, $languageId, "");
		importUMLSTerms($sab, $umlsCollectionId, $collectionId, $languageId, $isoLanguages);			
	}
}

$endTime = time();
echo "Time elapsed: " . ($endTime - $beginTime); 

function openDatabase($server, $databaseName, $userName, $password) {
	global
		$db;
	
	if ($db = mysql_connect($server, $userName, $password, true))
		mysql_select_db($databaseName, $db);
}

function loadIsoLanguages(){
	$dbr = &wfGetDB(DB_SLAVE);
	$sql = "select language_id, iso639_2 from language";

	$languages = array();
	
	$queryResult = $dbr->query($sql);
	while ($language = $dbr->fetchObject($queryResult)) {
	   $languages[$language->iso639_2] = $language->language_id;
	}

	return $languages;  	
}

function loadSourceAbbreviations() {
	global
		$db;
	
	$sourceAbbreviations = array();	
	$queryResult = mysql_query("select RSAB, SON from mrsab", $db);
	
	while ($sab = mysql_fetch_object($queryResult)) {
	   $sourceAbbreviations[$sab->RSAB] = $sab->SON;
	}
		
	mysql_free_result($queryResult);
	
	return $sourceAbbreviations;  
}

function importUMLSTerms($sab, $umlsCollectionId, $collectionId, $languageId, $isoLanguages) {
	global
		$db;
	
	echo "select str,cui,lat from MRCONSO where sab like '$sab'\n";
	$queryResult = mysql_query("select str, cui, lat, code from MRCONSO where sab like '$sab'", $db);
	$c1 = 0;
	while ($umlsTerm = mysql_fetch_object($queryResult)) {
		echo ++$c1 . "\n";
		$definedMeaningId = getDefinedMeaningFromCollection($umlsCollectionId, $umlsTerm->cui);
		$expression = findOrCreateExpression($umlsTerm->str, $isoLanguages[strtolower($umlsTerm->lat)]);
		if (!$definedMeaningId) {
	   		$definitionQueryResult = mysql_query("select def from MRDEF where cui='$umlsTerm->cui'", $db);
			$definition = mysql_fetch_object($definitionQueryResult);
			
			if (!$definition->def) {
				$definedMeaningId = addDefinedMeaning($expression->id);				
			}
			else {
				$definedMeaningId = createNewDefinedMeaning($expression->id, $languageId, $definition->def);
				addDefinedMeaningAlternativeDefinition($definedMeaningId, $languageId, $definition->def);
			}
		
			addDefinedMeaningToCollection($definedMeaningId, $umlsCollectionId, $umlsTerm->cui);
			addDefinedMeaningToCollection($definedMeaningId, $collectionId, $umlsTerm->code);
					
			while ($definition = mysql_fetch_object($definitionQueryResult)) {
				addDefinedMeaningAlternativeDefinition($definedMeaningId, $languageId, $definition->def);
			}
			mysql_free_result($definitionQueryResult);  		
	   	}
	   	else {
			createSynonymOrTranslation($definedMeaningId, $expression->id, true);
	   	}
	}
		
	mysql_free_result($queryResult);  
}

?>
