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
function getDM($definedMeaningId,$dc="uw") {
	global 
		$wdCurrentContext;
	$wdCurrentContext=$dc;
	$viewInformation = new ViewInformation();
	$viewInformation->queryTransactionInformation= new QueryLatestTransactionInformation();
	$model=new DefinedMeaningModel($definedMeaningId, $viewInformation);
	$record=$model->getRecord();
	$record->finish("DefinedMeaning");
	return $model;
}

function saveDM($model,$dc="uw") {
	global 
		$wdCurrentContext;
	$wdCurrentContext=$dc;
	#echo $model->getRecord();
	$model->save();
}

global
$beginTime, $wgCommandLineMode, $dc;

$beginTime = time();
$wgCommandLineMode = true;
# $dc = "uw"; < can't modify from here

/* insert code here */

$model=getDM(663672,"uw");

#$record=$model->getRecord();
#echo $record;

SaveDM($model,"tt");


$endTime = time();
echo("\n\nTime elapsed: " . durationToString($endTime - $beginTime)); 


