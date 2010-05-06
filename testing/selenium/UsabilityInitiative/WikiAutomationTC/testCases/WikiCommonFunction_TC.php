<?php
session_start();
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
require_once 'Config.php';
/**
 * This test case will be handling the common functions on test.
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiCommonFunction_TC extends PHPUnit_Extensions_SeleniumTestCase{
    /** Wiki server environment details.This array should be commented if the test
     * run on local browsers.  
    
     public static $browsers = array(
     array(
        'name'    => 'Safari',
        'browser' => '*safari',
        'host'    => 'raskin.usability.wikimedia.org',
        'port'    => 4444,
        'timeout' => 30000,
          )
        ); **/

    // Setup the browser URL and local browser
    function setUp() {
        // Setting the local browser. this should be disabled if the test run in Wiki environment.
        $this->setBrowser("*firefox");
        // Main link to be connected
         $this->setBrowserUrl($_SESSION["WIKI_WEB_URL"]);

    }

    // Open the page.
    function doOpenLink(){
        $this->open($_SESSION["WIKI_OPEN_PAGE"]);
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
    }

    // Login to the application
    function doLogin() {  
        if (!$this->isTextPresent("Log out")) {
            $this->click("link=Log in / create account");
            $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
            $this->type("wpName1", $_SESSION["WIKI_USER_NAME"]);
            $this->type("wpPassword1", $_SESSION["WIKI_USER_PASSWORD"]);
            $this->click("wpLoginAttempt");
            $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
            try {
                $this->assertEquals($_SESSION["WIKI_USER_DISPLAY_NAME"], $this->getText("link=" . $_SESSION["WIKI_USER_DISPLAY_NAME"]));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                array_push($this->verificationErrors, $e->toString());
            }
            $this->open($_SESSION["WIKI_OPEN_PAGE"]);
            $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        }
    }

    // Log out from the application
    function doLogout() {
        $this->open($_SESSION["WIKI_OPEN_PAGE"]);
         if ($this->isTextPresent("Log out")) {
            $this->click("link=Log out");
            $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
            try {
                $this->assertEquals("Log in / create account", $this->getText("link=Log in / create account"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                array_push($this->verificationErrors, $e->toString());
            }
            $this->open($_SESSION["WIKI_OPEN_PAGE"]);
            $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
         }
    }

    //Expand advance tool bar section if its not
    function doExpandAdvanceSection() {
        if (!$this->isTextPresent("Heading")){
            $this->click("link=Advanced");
        }
    }

    //Create a temporary new page
    function doCreateNewPageTemporary() {
       $this->type("//*[@id='searchInput']", "TestWikiPaget");
       $this->click("//*[@id='searchButton']");
       $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
       $this->click("link=TestWikiPaget");
       $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
    }

    //Access a random page
    function doAccessRandomPage() {
       $this->click("link=Random article");
       $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
    }

    //Access a random page
    function doEditPage() {
       $this->click("//li[@id='ca-edit']/a/span");
       $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
    }

    //Screenshot create on error
    function doCreateScreenShot($file_name){
         $this->captureEntirePageScreenshot($_SESSION["WIKI_CODE_PATH"] . "\/". $_SESSION["WIKI_SCREENSHOTS_PATH"] ."\/". $file_name . "_error" . date("Y_m_d") . "." . $_SESSION["WIKI_SCREENSHOTS_TYPE"] , "");
    }

}
?>