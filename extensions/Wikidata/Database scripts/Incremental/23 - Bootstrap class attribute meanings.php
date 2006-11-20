<?php

//
// Script create several defined meanings, which are used to set class attribute levels
// CAUTION: Namespace::getIndexForName() does not seem to work from a script, 
// therfore replace these by hardcoded indices where pages are created (probably expression.php)  
//

define('MEDIAWIKI', true );
require_once("../../../../LocalSettings.php");
require_once("Setup.php");
require_once("../../WP/ProgressBar.php");
require_once("../../WiktionaryZ/Expression.php");
require_once("../../WiktionaryZ/Transaction.php");

ob_end_flush();

global
	$beginTime, $wgCommandLineMode, $wgUser, $numberOfBytes;

$beginTime = time();
$wgCommandLineMode = true;

$userID = 1;
$wgUser->setID($userID);
startNewTransaction($userID, 0, "Script bootstrap class attribute meanings");

$languageId = 85;
$collectionId = bootstrapCollection("Class attribute levels", $languageId, "LEVL");
$meanings = array();
$meanings["Defined meaning"] = bootstrapDefinedMeaning("Defined meaning", $languageId, "A concept defined by a definition.");
$meanings["Definition"] = bootstrapDefinedMeaning("Definition", $languageId, "A paraphrase describing a concept.");
$meanings["Synonym"] = bootstrapDefinedMeaning("Synonym", $languageId, "A word with a spelling in a language that can expres a concept.");
$meanings["Relation"] = bootstrapDefinedMeaning("Relation", $languageId, "An association of one concept to another of a certain type.");
$meanings["Annotation"] = bootstrapDefinedMeaning("Annotation", $languageId, "Characteristic information of a concept.");

foreach($meanings as $internalId => $meaning) {
	addDefinedMeaningToCollection($meaning, $collectionId, $internalId);	
}

$dbr =& wfGetDB(DB_MASTER);
$timestamp = wfTimestampNow();

$dbr->query('INSERT INTO script_log (time, script_name) ' .
		    'VALUES ('. $timestamp . ',' . $dbr->addQuotes('23 - Bootstrap class attribute meanings.php') . ')');

$endTime = time();
echo "\n\nTime elapsed: " . durationToString($endTime - $beginTime); 

function bootstrapDefinedMeaning($spelling, $languageId, $definition) {
	$expression = findOrCreateExpression($spelling, $languageId); 
	$definedMeaningId = createNewDefinedMeaning($expression->id, $languageId, $definition);
	return $definedMeaningId;
}

?>
