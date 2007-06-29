<?php

define('MEDIAWIKI', true );

# do we seriously need ALL of thesE?
require_once("../../../StartProfiler.php");
require_once("../../../LocalSettings.php");
require_once("../php-tools/ProgressBar.php");
#require_once("DatabaseUtilities.php");
require_once("Setup.php");
require_once("DefinedMeaningModel.php");
require_once("Transaction.php");


global
$beginTime, $wgCommandLineMode;

$beginTime = time();
$wgCommandLineMode = true;
$dc = "uw";

/* insert code here */
$definedMeaningId=1087; # education
$filterLanguageId=0; # ??? What does this do ???
$possiblySynonymousRelationTypeId=0; # ??? What does this do ???
$queryTransactionInformation= new QueryLatestTransactionInformation();

$model=new DefinedMeaningModel($definedMeaningId, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation);
$record=$model->getRecord();
echo $record;


$endTime = time();
echo("\n\nTime elapsed: " . durationToString($endTime - $beginTime)); 


