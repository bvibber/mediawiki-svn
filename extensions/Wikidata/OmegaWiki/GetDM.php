<?php

define('MEDIAWIKI', true );

# do we seriously need ALL of these?
require_once("../../../StartProfiler.php");
require_once("../../../LocalSettings.php");
require_once("../php-tools/ProgressBar.php");
#require_once("DatabaseUtilities.php");
require_once("Setup.php");
require_once("DefinedMeaningModel.php");
require_once("Transaction.php");


global
$beginTime, $wgCommandLineMode, $dc;

$beginTime = time();
$wgCommandLineMode = true;
# $dc = "uw"; < can't modify from here

/* insert code here */
$definedMeaningId=663665; # UnitTest 

$viewInformation = new ViewInformation();
$viewInformation->queryTransactionInformation = new QueryLatestTransactionInformation();

$model=new DefinedMeaningModel($definedMeaningId, $viewInformation);
$record=$model->getRecord();
$record->finish("DefinedMeaning");
echo $record;


$endTime = time();
echo("\n\nTime elapsed: " . durationToString($endTime - $beginTime)); 


