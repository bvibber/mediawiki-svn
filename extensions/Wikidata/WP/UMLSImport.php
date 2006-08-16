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
echo "Create collections\n";
$umlsCollectionId = bootstrapCollection("UMLS", $languageId, "");
$relationCollectionId = bootstrapCollection("UMLS Relation Types 2005", $languageId, "RELT");
addDefinedMeaningToCollection(getCollectionMeaningId($relationCollectionId), $umlsCollectionId, "rel");
$relationAttributesCollectionId = bootstrapCollection("UMLS Relation Attributes 2005", $languageId, "RELT");
addDefinedMeaningToCollection($relationAttributesCollectionId, $umlsCollectionId, "rela");

echo "Load source abbreviations and languages\n";
$sourceAbbreviations = loadSourceAbbreviations();
$isoLanguages = loadIsoLanguages();

echo "Import UMLS terms\n";
foreach ($sourceAbbreviations as $sab => $source) {
//	if (strpos($sab, "ICPC") !== false) {
	if ((strcmp($sab, "ICPC") == 0) || (strcmp($sab, "SRC") == 0)) {
		echo "$source\n";
		$collectionId = bootstrapCollection($source, $languageId, "");
		importUMLSTerms($sab, $umlsCollectionId, $collectionId, $languageId, $isoLanguages);			
	}
}

echo "Import UMLS relation and attributes types\n";
importUMLSRelationTypes($relationCollectionId, $languageId);
importUMLSRelationAttributes($relationAttributesCollectionId, $languageId);

echo "Import UMLS relations\n";
$relationCollection = getCollectionContents($relationCollectionId);
$relationAttributesCollection = getCollectionContents($relationAttributesCollectionId);
foreach ($sourceAbbreviations as $sab => $source) {
//	if (strpos($sab, "ICPC") !== false) {
	if (strcmp($sab, "ICPC") == 0) {
		echo "$source\n";
		$query = "select cui1, cui2, rel from MRREL where sab like '$sab'";
		importUMLSRelations($umlsCollectionId , $relationCollection, $query);
		$query = "select cui1, cui2, rela from MRREL where sab like '$sab' and rela!=''";
		importUMLSRelations($umlsCollectionId , $relationAttributesCollection, $query);
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
	
	$queryResult = mysql_query("select str, cui, lat, code from MRCONSO where sab like '$sab'", $db);
	while ($umlsTerm = mysql_fetch_object($queryResult)) {
		$definedMeaningId = getDefinedMeaningFromCollection($umlsCollectionId, $umlsTerm->cui);
		$expression = findOrCreateExpression(trim($umlsTerm->str), $isoLanguages[strtolower($umlsTerm->lat)]);
		if (!$definedMeaningId) {
	   		$definitionQueryResult = mysql_query("select def from MRDEF where cui='$umlsTerm->cui'", $db);
			$definition = mysql_fetch_object($definitionQueryResult);
			
			if (!$definition->def) {
				$definedMeaningId = addDefinedMeaning($expression->id);
				createSynonymOrTranslation($definedMeaningId, $expression->id, true);				
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

function importUMLSRelationTypes($relationCollectionId, $languageId) {
	global
		$db;
		
	$queryResult = mysql_query("select ABBREV, FULL from rel where ABBREV!='CHD' and ABBREV!='PAR' and ABBREV!='SUBX'", $db);
	while ($relationType = mysql_fetch_object($queryResult)) {
		$definedMeaningId = getDefinedMeaningFromCollection($relationCollectionId, $relationType->ABBREV);
		$expression = findOrCreateExpression(trim($relationType->FULL), $languageId);
		if(!$definedMeaningId) {
			$definedMeaningId = addDefinedMeaning($expression->id);
			createSynonymOrTranslation($definedMeaningId, $expression->id, true);
			addDefinedMeaningToCollection($definedMeaningId, $relationCollectionId, $relationType->ABBREV);		
		}
	}
	
	mysql_free_result($queryResult);  
}

function importUMLSRelationAttributes($relationAttributesCollectionId, $languageId) {
	global
		$db;
		
	$queryResult = mysql_query("select ABBREV, FULL from rela", $db);
	while ($relationType = mysql_fetch_object($queryResult)) {
		$definedMeaningId = getDefinedMeaningFromCollection($relationAttributesCollectionId, $relationType->ABBREV);
		$expression = findOrCreateExpression(trim($relationType->FULL), $languageId);
		if(!$definedMeaningId) {
			$definedMeaningId = addDefinedMeaning($expression->id);
			createSynonymOrTranslation($definedMeaningId, $expression->id, true);
			addDefinedMeaningToCollection($definedMeaningId, $relationAttributesCollectionId, $relationType->ABBREV);		
		}
	}
	
	mysql_free_result($queryResult);  	
}

function importUMLSRelations($umlsCollectionId, $relationCollectionContents, $query) {
	global
		$db;

	$queryResult = mysql_query($query, $db);
	while ($relation = mysql_fetch_row($queryResult)) {
		$relationType = $relation[2];
		if(strcmp($relationType, 'CHD') == 0) {
			$relationType='RN';			
		}
		elseif(strcmp($relationType, 'PAR') == 0) {
			$relationType='RB';
		}
		
		$definedMeaningId1 = getDefinedMeaningFromCollection($umlsCollectionId, $relation[0]);
		$definedMeaningId2 = getDefinedMeaningFromCollection($umlsCollectionId, $relation[1]);
		$relationMeaningId = $relationCollectionContents[$relationType];
		if(!$definedMeaningId1){
			echo "Unkown cui $relation[0]\n";
			print_r($relation);
		}
		if(!$definedMeaningId2){
			echo "Unkown cui $relation[1]\n";
			print_r($relation);
		}
		if(!$relationMeaningId){
			echo "Unkown relation $relationType\n";
			print_r($relationCollectionContents);
			print_r($relation);
		}
		addRelation($definedMeaningId2, $relationMeaningId, $definedMeaningId1);		
	}	
}

?>
