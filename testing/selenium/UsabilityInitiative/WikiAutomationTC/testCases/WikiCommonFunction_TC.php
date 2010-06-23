<?php
session_start();
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
include 'Config.php';

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
        ); */

    // Setup the browser URL and local browser
    function setUp() {
        // Setting the local browser. this should be disabled if the test run in Wiki environment.
        $this->setBrowser("*firefox");
        //$this->setBrowser("*iexplore");
        //$this->setBrowser("*safari");
        //$this->setBrowser("*opera");
        //$this->setBrowser("*googlechrome");
        // Main link to be connected
        $this->setBrowserUrl(WIKI_WEB_URL);
    }

    // Open the page.
    function doOpenLink(){
        $this->open(WIKI_OPEN_PAGE);
        /*try {
        $this->assertTrue($this->isTextPresent("Welcome to Wikipedia"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }*/
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
    }

    // Login to the application
    function doLogin() {  
        if (!$this->isTextPresent(TEXT_LOGOUT)) {
            $this->click(LINK_LOGIN);
            $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
            $this->type(INPUT_USER_NAME, WIKI_USER_NAME);
            $this->type(INPUT_PASSWD,WIKI_USER_PASSWORD);
            $this->click(BUTTON_LOGIN);
            $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
            try {
                $this->assertEquals(WIKI_USER_DISPLAY_NAME, $this->getText(LINK_START . WIKI_USER_DISPLAY_NAME));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                parent::doCreateScreenShot(__FUNCTION__);
                array_push($this->verificationErrors, $e->toString());
            }
            $this->click(LINK_MAIN_PAGE);
            $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        }
    }

    // Log out from the application
    function doLogout() {
        $this->open(WIKI_OPEN_PAGE);
         if ($this->isTextPresent(TEXT_LOGOUT)) {
            $this->click(LINK_LOGOUT);
            $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
            try {
                $this->assertEquals(TEXT_LOGOUT_CONFIRM, $this->getText(LINK_LOGIN));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                parent::doCreateScreenShot(__FUNCTION__);
                array_push($this->verificationErrors, $e->toString());
            }
            $this->open(WIKI_OPEN_PAGE);
            $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
         }
    }

    //Expand advance tool bar section if its not
    function doExpandAdvanceSection() {
        if (!$this->isTextPresent(TEXT_HEADING)){
            $this->click(LINK_ADVANCED);
        }
    }

    //Create a temporary new page
    function doCreateNewPageTemporary() {
       $this->type(INPUT_SEARCH_BOX,  WIKI_TEMP_NEWPAGE);
       $this->click(BUTTON_SEARCH);
       $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
       $this->click(LINK_START . WIKI_TEMP_NEWPAGE);
       $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
    }

    //Access a random page
    function doAccessRandomPage() {
       $this->click(LINK_RANDOM_PAGE);
       $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
    }

    //Access a random page
    function doEditPage() {
       $this->click(LINK_EDITPAGE);
       $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
    }

    //Screenshot create on error
    function doCreateScreenShot($file_name){
        $this->windowFocus();
        $this->windowMaximize();
        $this->captureEntirePageScreenshot(WIKI_CODE_PATH . "//". WIKI_SCREENSHOTS_PATH ."//". $file_name . "_error" . date("Y_m_d") . "." .  WIKI_SCREENSHOTS_TYPE , "");
    }
}
?>