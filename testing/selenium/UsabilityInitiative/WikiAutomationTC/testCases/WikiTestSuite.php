<?php
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'WikSearch_TC.php';
require_once 'WikiWatchUnWatch_TC.php';

require_once 'WikiNTOC_ ExistingPage.php';
require_once 'WikiDialogs_ExsistingPage.php';
require_once 'WikiToolBarOther_ExistingPage.php';
require_once 'WikiTextFormat_ExistingPage.php';

require_once 'WikiNTOC_NewPage.php';
require_once 'WikiDialogs_NewPage.php';
require_once 'WikiToolBarOther_NewPage.php';
require_once 'WikiTextFormat_NewPage.php';


$suite = new PHPUnit_Framework_TestSuite('Wiki Tests');

require_once('WikiListener.php');
$wLis = new WikiListener();
$result = new PHPUnit_Framework_TestResult();
//Add test case to the test suite
$suite->addTestSuite("WikSearch_TC");
$suite->addTestSuite("WikiWatchUnWatch_TC");

$suite->addTestSuite("WikiNTOC_ ExistingPage");
$suite->addTestSuite("WikiDialogs_ExsistingPage");
$suite->addTestSuite("WikiToolBarOther_ExistingPage");
$suite->addTestSuite("WikiTextFormat_ExistingPage");

$suite->addTestSuite("WikiNTOC_NewPage");
$suite->addTestSuite("WikiDialogs_NewPage");
$suite->addTestSuite("WikiToolBarOther_NewPage");
$suite->addTestSuite("WikiTextFormat_NewPage");

//$result->addListener($wLis); //Define your listener which the test result will use to give output
$suite->run($result); //And of course run this tests

?>
