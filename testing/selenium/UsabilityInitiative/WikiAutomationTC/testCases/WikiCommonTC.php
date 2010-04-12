<?php
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
/**
 * This test case will be handling the common functions on test.
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiCommonTC extends PHPUnit_Extensions_SeleniumTestCase {
    /** Wiki server environment details.This array should be commented if the test
     * run on local browsers.  **/
    
     public static $browsers = array(
     array(
        'name'    => 'Safari',
        'browser' => '*safari',
        'host'    => 'raskin.usability.wikimedia.org',
        'port'    => 4444,
        'timeout' => 30000,
          )
        ); 

    // Setup the browser URL and local browser
    function setUp() {
        // Setting the local browser. this should be disabled if the test run in Wiki environment.
       //  $this->setBrowser("*firefox");
        // Main link to be connected
         $this->setBrowserUrl("http://prototype.wikimedia.org");
    }

    // Open the page.
    function doOpenLink(){
        $this->open("/deployment-en/Main_Page");
    }

    // Login to the application
    function doLogin() {  
        if (!$this->isTextPresent("Log out")) {
            $this->click("link=Log in / create account");
            $this->waitForPageToLoad("30000");
            $this->type("wpName1", "bhagya_qa");
            $this->type("wpPassword1", "test");
            $this->click("wpLoginAttempt");
            $this->waitForPageToLoad("30000");
            try {
                $this->assertEquals("Bhagya qa", $this->getText("link=Bhagya qa"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                array_push($this->verificationErrors, $e->toString());
            }
        }
    }

    // Log out from the application
    function doLogout() {
        $this->open("/deployment-en/Main_Page");
         if ($this->isTextPresent("Log out")) {
            $this->click("link=Log out");
            $this->waitForPageToLoad("30000");
            try {
                $this->assertEquals("Log in / create account", $this->getText("link=Log in / create account"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                array_push($this->verificationErrors, $e->toString());
            }
         }
    }
}
?>