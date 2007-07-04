<?php

# testing what we learned in SaveDM
# (possibly files like this should go in a subdir
# and/or be replaced by proper unit testing)

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
	$model=new DefinedMeaningModel($definedMeaningId);
	return $model;
}

global
$beginTime, $wgCommandLineMode, $dc;

$beginTime = time();
$wgCommandLineMode = true;
# $dc = "uw"; < can't modify from here

/* insert code here */

$model=getDM(663678,"tt");

$record=$model->getRecord();
echo $record;

$model->copyTo("uw");

$endTime = time();
echo("\n\nTime elapsed: " . durationToString($endTime - $beginTime)); 


