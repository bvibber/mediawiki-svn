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
$semanticNetworkSemanticTypesCollectionId = bootstrapCollection("Semantic Network 2005AC Semantic Types", $languageId, "ATTR");
addDefinedMeaningToCollection(getCollectionMeaningId($semanticNetworkSemanticTypesCollectionId), $umlsCollectionId, "STY");
$semanticNetworkRelationTypesCollectionId = bootstrapCollection("Semantic Network 2005AC Relation Types", $languageId, "RELT");
addDefinedMeaningToCollection(getCollectionMeaningId($semanticNetworkRelationTypesCollectionId), $umlsCollectionId, "RL");

echo "Load source abbreviations and languages\n";
$sourceAbbreviations = loadSourceAbbreviations();
$isoLanguages = loadIsoLanguages();

echo "Import UMLS terms\n";
foreach ($sourceAbbreviations as $sab => $source) {
//	if ((strcmp($sab, "ICPC") == 0) || (strcmp($sab, "SRC") == 0)) {
		echo "$source\n";
		$collectionId = bootstrapCollection($source, $languageId, "");
		importUMLSTerms($sab, $umlsCollectionId, $collectionId, $languageId, $isoLanguages);			
//	}
}

echo "Import UMLS relation and attributes types\n";
importUMLSRelationTypes($relationCollectionId, $languageId);
importUMLSRelationAttributes($relationAttributesCollectionId, $languageId);

echo "Import UMLS relations\n";
$relationCollection = getCollectionContents($relationCollectionId);
$relationAttributesCollection = getCollectionContents($relationAttributesCollectionId);
foreach ($sourceAbbreviations as $sab => $source) {
//	if (strcmp($sab, "ICPC") == 0) {
		echo "$source\n";
		$query = "select cui1, cui2, rel from MRREL where sab like '$sab'";
		importUMLSRelations($umlsCollectionId , $relationCollection, $query);
		$query = "select cui1, cui2, rela from MRREL where sab like '$sab' and rela!=''";
		importUMLSRelations($umlsCollectionId , $relationAttributesCollection, $query);
//	}
}

echo "Import semantic network types\n";
importSNTypes($semanticNetworkSemanticTypesCollectionId, "SELECT semtypeab,type,definition FROM srdef WHERE type='STY'", $languageId);
importSNTypes($semanticNetworkRelationTypesCollectionId, "SELECT semtypeab,type,definition FROM srdef WHERE type='RL'", $languageId);

echo "Import semantic network relations\n";
importSemanticTypeRelations($semanticNetworkSemanticTypesCollectionId, $relationCollection, "SELECT SEMTYPE1, RELATION, SEMTYPE2 from semtypehier");
importSemanticTypeRelations($semanticNetworkRelationTypesCollectionId, $relationCollection, "SELECT RELTYPE1, RELATION, RELTYPE2 from semrelhier");

echo "Import UMLS semantic types\n";
$attributeTypes = getCollectionContents($semanticNetworkSemanticTypesCollectionId);
foreach ($sourceAbbreviations as $sab => $source) {
//	if (strcmp($sab, "ICPC") == 0) {
		echo "$source\n";
		importUMLSSemanticTypes($sab, $umlsCollectionId, $attributeTypes);		
//	}
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

function importSNTypes($collectionId, $query, $languageId) {
	global
		$db;
	
	$queryResult = mysql_query($query, $db);
	while ($semanticNetworkType = mysql_fetch_object($queryResult)) {
		$expressionText = $semanticNetworkType->semtypeab;
		$expressionText = strtolower(str_replace("_", " ", $expressionText));
		$definedMeaningId = getDefinedMeaningFromCollection($collectionId, $semanticNetworkType->semtypeab);
		$expression = findOrCreateExpression($expressionText, $languageId);
		if(!$definedMeaningId) {
			$definedMeaningId = createNewDefinedMeaning($expression->id, $languageId, $semanticNetworkType->definition);
			addDefinedMeaningToCollection($definedMeaningId, $collectionId, $semanticNetworkType->semtypeab);		
		}
	}
	
	mysql_free_result($queryResult);  	
}

function importSemanticTypeRelations($collectionId, $relationCollectionContents, $query) {
	global
		$db;

	$queryResult = mysql_query($query, $db);
	while ($relation = mysql_fetch_row($queryResult)) {
		$relationType = $relation[1];
		
		$definedMeaningId1 = getDefinedMeaningFromCollection($collectionId, $relation[0]);
		$definedMeaningId2 = getDefinedMeaningFromCollection($collectionId, $relation[2]);
		$relationMeaningId = $relationCollectionContents[$relationType];
		
		if(!$relationMeaningId){
			echo "Unkown relation $relationType\n";
			print_r($relationCollectionContents);
			print_r($relation);
		}
		if(!$definedMeaningId1){
			echo "Unkown semantic type $relation[0]\n";
			print_r($relation);
		}
		if(!$definedMeaningId2){
			echo "Unkown semantic type $relation[2]\n";
			print_r($relation);
		}
		addRelation($definedMeaningId2, $relationMeaningId, $definedMeaningId1);		
	}	
}

function importUMLSSemanticTypes($sab, $collectionId, $attributeTypes) {
	global
		$db;

	$query = "SELECT MRSTY.CUI, MRSTY.STY FROM MRCONSO,MRSTY where MRCONSO.SAB like '$sab' and MRCONSO.CUI=MRSTY.CUI";
	$queryResult = mysql_query($query, $db);
	while ($attribute = mysql_fetch_object($queryResult)) {
		$definedMeaningId = getDefinedMeaningFromCollection($collectionId, $attribute->CUI);
		$attributeMeaningId = $attributeTypes[$attribute->STY];

		if(!$definedMeaningId){
			echo "Unkown cui $attribute->CUI\n";
			print_r($attribute);
		}
		if(!$attributeMeaningId){
			echo "Unkown attribute $$attribute->STY\n";
			print_r($attribute);
		}
		addRelation($definedMeaningId, 0, $attributeMeaningId);
	}	
}
?>
