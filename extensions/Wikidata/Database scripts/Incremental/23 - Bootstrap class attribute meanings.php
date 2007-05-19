<?php

//
// Script create several defined meanings, which are used to set class attribute levels
// CAUTION: Script is updated to correct definitions and create an extra table to store the
// bootstrapped meanings. Re-run wil create duplicate meanings.
//

define('MEDIAWIKI', true );

require_once("../../../../LocalSettings.php");
require_once("../../../../includes/Defines.php");
require_once("Setup.php");
require_once("../../php-tools/ProgressBar.php");
require_once("../../OmegaWiki/WikiDataAPI.php");
require_once("../../OmegaWiki/Transaction.php");
require_once("../../OmegaWiki/WikiDataBootstrappedMeanings.php");

ob_end_flush();

global
	$beginTime, $wgCommandLineMode, $wgUser, $numberOfBytes,
	$definedMeaningMeaningName, $definitionMeaningName,
	$relationMeaningName, $synTransMeaningName,
	$annotationMeaningName;

$beginTime = time();
$wgCommandLineMode = true;

$dbr =& wfGetDB(DB_MASTER);
$timestamp = wfTimestampNow();

$dbr->query('DROP TABLE `bootstrapped_defined_meanings`;');


$dbr->query('CREATE TABLE `bootstrapped_defined_meanings` (
			`name` VARCHAR(255) NOT NULL ,
			`defined_meaning_id` INT NOT NULL);');

$userID = 1;
$wgUser->setID($userID);
startNewTransaction($userID, 0, "Script bootstrap class attribute meanings");

$languageId = 85;
$collectionId = bootstrapCollection("Class attribute levels", $languageId, "LEVL");
$meanings = array();
$meanings[$definedMeaningMeaningName] = bootstrapDefinedMeaning($definedMeaningMeaningName, $languageId, "The combination of an expression and definition in one language defining a concept.");
$meanings[$definitionMeaningName] = bootstrapDefinedMeaning($definitionMeaningName, $languageId, "A paraphrase describing a concept.");
$meanings[$synTransMeaningName] = bootstrapDefinedMeaning($synTransMeaningName, $languageId, "A translation or a synonym that is equal or near equal to the concept defined by the defined meaning.");
$meanings[$relationMeaningName] = bootstrapDefinedMeaning($relationMeaningName, $languageId, "The association of two defined meanings through a specific relation type.");
$meanings[$annotationMeaningName] = bootstrapDefinedMeaning($annotationMeaningName, $languageId, "Characteristic information of a concept.");

foreach($meanings as $internalName => $meaningId) {
	addDefinedMeaningToCollection($meaningId, $collectionId, $internalName);
	
	$dbr->query('INSERT INTO `bootstrapped_defined_meanings` (name, defined_meaning_id) ' . 
				'VALUES (' . $dbr->addQuotes($internalName) . ', ' . $meaningId . ')');
}

$dbr->query('INSERT INTO script_log (time, script_name, comment) ' .
		    'VALUES ('. $timestamp . ',' . $dbr->addQuotes('23 - Bootstrap class attribute meanings.php') .  ',' . $dbr->addQuotes('create bootstrap class attribute meanings') . ')');

$endTime = time();
echo "\n\nTime elapsed: " . durationToString($endTime - $beginTime); 

function bootstrapDefinedMeaning($spelling, $languageId, $definition) {
	$expression = findOrCreateExpression($spelling, $languageId); 
	$definedMeaningId = createNewDefinedMeaning($expression->id, $languageId, $definition);

	return $definedMeaningId;
}

?>
