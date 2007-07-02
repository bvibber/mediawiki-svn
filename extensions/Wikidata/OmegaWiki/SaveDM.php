<?php

# let's see...

define('MEDIAWIKI', true );

# do we seriously need ALL of these?
require_once("../../../StartProfiler.php");
require_once("../../../LocalSettings.php");
require_once("../php-tools/ProgressBar.php");
#require_once("DatabaseUtilities.php");
require_once("Setup.php");
require_once("DefinedMeaningModel.php");
require_once("Transaction.php");

/** Just get a defined meaning */
function getDM($dm_id,$dc="uw") {
	$definedMeaningId=663665; # UnitTest 
	$filterLanguageId=0; # ??? What does this do ???
	$possiblySynonymousRelationTypeId=0; # ??? What does this do ???
	$queryTransactionInformation= new QueryLatestTransactionInformation();
	$model=new DefinedMeaningModel($definedMeaningId, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation);
	$record=$model->getRecord();
	$record->finish("DefinedMeaning");
	return $model;
}

function saveDM($model) {
	$model->saveWithinTransaction();
}

global
$beginTime, $wgCommandLineMode, $dc;

$beginTime = time();
$wgCommandLineMode = true;
# $dc = "uw"; < can't modify from here

/* insert code here */

$model=getDM(663655);
$record=$model->getRecord();
echo $record;


$endTime = time();
echo("\n\nTime elapsed: " . durationToString($endTime - $beginTime)); 


