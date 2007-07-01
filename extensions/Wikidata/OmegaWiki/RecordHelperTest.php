<?php

define('MEDIAWIKI', true );

if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "RecordHelperTest::main");
}


# do we seriously need ALL of these?
require_once("../../../StartProfiler.php");
require_once("../../../LocalSettings.php");
global $kimwgBLA, $IP;
echo "SHOULD GET\n";
echo $kimwgBLA."    ".$IP;
echo "\n";
require_once("../php-tools/ProgressBar.php");
require_once("Setup.php");
require_once("DefinedMeaningModel.php");
require_once("Transaction.php");

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";
require_once("RecordHelper.php");

class RecordHelperTest extends PHPUnit_Framework_TestCase {

	var $testRecord;

	public static function main() {
		require_once "PHPUnit/TextUI/TestRunner.php";

		$suite  = new PHPUnit_Framework_TestSuite("somethingTest");
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	function setUp() {
			
		global $wgCommandLineMode;
		$wgCommandLineMode = true;
		$dc = "uw";
		$definedMeaningId=663665; # education
		$filterLanguageId=0; # ??? What does this do ???
		$possiblySynonymousRelationTypeId=0; # ??? What does this do ???
		$queryTransactionInformation= new QueryLatestTransactionInformation();

		$model=new DefinedMeaningModel($definedMeaningId, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation);
		$testRecord=$model->getRecord();
	}
	
	function tearDown() {
		$testRecord=NULL;
	}

}

if (PHPUnit_MAIN_METHOD == "RecordHelperTest::main") {
    RecordHelperTest::main();
}

?>
