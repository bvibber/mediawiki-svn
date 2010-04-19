<?php
   require_once 'PHPUnit/Framework.php';
   require_once 'PHPUnit/Util/Filter.php';
   require_once 'PHPUnit/Util/Printer.php';
   require_once 'PHPUnit/Util/Test.php';

   //PHPUnit to exclude it from code coverage things.
   PHPUnit_Util_Filter::addFileToFilter(__FILE__, "PHPUNIT");

   class WikiListener extends PHPUnit_Util_Printer implements PHPUnit_Framework_TestListener {
        protected $currentTestSuiteName = "";
        protected $currentTestName = "";
        protected $currentTestPass = TRUE;
      /**
        * Triggered when an error occurs on the test case
        */
       public function addError(PHPUnit_Framework_Test $test, Exception $e, $time) {
           $this->writeCase(
             'error',
             $time,
             $e->getMessage()
           );

           $this->currentTestPass = FALSE;
       }
       /**
        * Triggered when one of the unit tests fails.
        */
       public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {
           $this->writeCase(
             'fail',
             $time,
             $e->getMessage()
           );
           $this->currentTestPass = FALSE;
       }
       /**
        * Triggered when an incomplete test is encountered.
        */
       public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
           $this->writeCase('error', $time, array(), 'Incomplete Test');
          $this->currentTestPass = FALSE;
       }
       /**
        * Triggered when an incomplete test is encountered.
        */
       public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
           $this->writeCase('error', $time, array(), 'Skipped Test');
           $this->currentTestPass = FALSE;
       }
       /**
        * Triggered when a testsuite is started
        */
       public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {
           $this->currentTestSuiteName = $suite->getName();
           $this->currentTestName      = '';
           $this->write("Started Suite: " . $this->currentTestSuiteName . " (" . count($suite) . " tests)\n");
       }
       /**
        * Triggered when a test suite ends.
        */
       public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {
           $this->currentTestSuiteName = '';
           $this->currentTestName      = '';
       }
       /**
        * Triggered when a testcase starts
        */
       public function startTest(PHPUnit_Framework_Test $test) {
           $this->currentTestName = PHPUnit_Util_Test::describe($test);
           $this->currentTestPass = TRUE;
       }
       /**
        * Triggered when a testcase ends
        */
       public function endTest(PHPUnit_Framework_Test $test, $time) {
           if ($this->currentTestPass) {
               $this->writeCase('pass', $time);
           }
       }
       /**
        * To avoide duplicity
        */
       protected function writeCase($status, $time, $message = '') {
           $m = "Test: " . $this->currentTestName . " - Status: " . $status . " - Time: " . $time . ($message ? " - Message: " . $message: "") . "\n";
           $this->write($m);
       }
   }
?>
