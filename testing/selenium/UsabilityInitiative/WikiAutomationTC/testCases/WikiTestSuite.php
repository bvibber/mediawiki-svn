<?php
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/TestSuite.php';

require_once 'WikiListener.php';

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

$suite = new PHPUnit_Framework_TestSuite('ArrayTest');

$result = new PHPUnit_Framework_TestResult;
$result->addListener(new WikiListener);

$suite->addTestSuite('WikiSearch_TC'); // Working in Chrome
$suite->addTestSuite('WikiWatchUnWatch_TC');  // Working in Chrome

$suite->addTestSuite("WikiNTOC_ExistingPage"); // Working in Chrome
$suite->addTestSuite("WikiDialogs_ExsistingPage"); 
$suite->addTestSuite("WikiToolBarOther_ExistingPage");
$suite->addTestSuite("WikiTextFormat_ExistingPage");

$suite->addTestSuite("WikiNTOC_NewPage");
$suite->addTestSuite("WikiDialogs_NewPage");
$suite->addTestSuite("WikiToolBarOther_NewPage");
$suite->addTestSuite("WikiTextFormat_NewPage");

$suite->run($result);
?>