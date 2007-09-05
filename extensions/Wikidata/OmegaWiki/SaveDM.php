<?php

# let's see...
# start out with a test skeleton, get a record from one dataset
# then save it to the other. Once this works, we can wrap it up nicely, and 
# apply it to our actual code.

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
function getDM($definedMeaningId,$dc="uw") {
	global 
		$wdCurrentContext;
	$wdCurrentContext=$dc;
	#$viewInformation = new ViewInformation();
	#$viewInformation->queryTransactionInformation= new QueryLatestTransactionInformation();
	$model=new DefinedMeaningModel($definedMeaningId);
	$record=$model->getRecord();
	#$record->finish("DefinedMeaning");
	return $model;
}

function saveDM($model,$dc="uw") {
	global 
		$wdCurrentContext;
	$wdCurrentContext=$dc;
	#echo $model->getRecord();
	$model->saveWithinTransaction();
}

global
$beginTime, $wgCommandLineMode, $dc;

$beginTime = time();
$wgCommandLineMode = true;
# $dc = "uw"; < can't modify from here

/* insert code here */

$model=getDM(663674,"tt");

$record=$model->getRecord();
echo $record;
$defexp=$record->getValue("defined-meaning-complete-defining-expression");
echo $defexp."\n";
#$expid=$record->getAttributeValue(new Attribute("expression-id"));
$id=$defexp->getValue("expression-id");
$spelling=$defexp->getValue("defined-meaning-defining-expression");
$language=$defexp->getValue("language");

echo "id: $id, spelling:$spelling, language:$language";

saveDM($model,"uw");

$endTime = time();
echo("\n\nTime elapsed: " . durationToString($endTime - $beginTime)); 


