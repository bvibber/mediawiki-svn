<?php
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'WikiAcaiSearchTC.php';
require_once 'WikiWatchUnWatchTC.php';
require_once 'WikiToolBarDialogsTC.php';
require_once 'WikiToolBarNTOC.php';
require_once 'WikiToolBarOtherTC.php';
require_once 'WikiToolBarTextFormatTC.php';

$suite = new PHPUnit_Framework_TestSuite('Wiki Tests');

require_once('WikiListener.php');
$wLis = new WikiListener();
$result = new PHPUnit_Framework_TestResult();
//Add test case to the test suite
$suite->addTestSuite("WikiAcaiSearchTC"); 
$suite->addTestSuite("WikiWatchUnWatchTC");
$suite->addTestSuite("WikiToolBarDialogsTC");
$suite->addTestSuite("WikiToolBarNTOC");
$suite->addTestSuite("WikiToolBarOtherTC");
$suite->addTestSuite("WikiToolBarTextFormatTC");
$result->addListener($wLis); //Define your listener which the test result will use to give output
$suite->run($result); //And of course run this tests

?>
