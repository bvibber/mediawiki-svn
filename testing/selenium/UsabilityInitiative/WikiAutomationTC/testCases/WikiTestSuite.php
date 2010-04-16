<?php
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';


require_once 'WikiAcaiSearchTC.php';

$suite = new PHPUnit_Framework_TestSuite('Wiki Tests');

require_once('WikiListener.php');
$wLis = new WikiListener();
$result = new PHPUnit_Framework_TestResult();
$suite->addTestSuite("WikiAcaiSearchTC"); //Add your test case to the test suite
$result->addListener($wLis); //Define your listener which the test result will use to give output
$suite->run($result); //And of course run this tests

?>
