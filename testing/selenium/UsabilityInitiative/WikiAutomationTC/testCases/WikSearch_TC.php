<?php
session_start();
require_once('log4php/Logger.php');
Logger::configure('log4php.properties');
require_once 'WikiCommonFunction_TC.php';
require_once 'Config.php';

/**
 * This test case will be handling the search function.
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiSearch_TC extends WikiCommonFunction_TC {
    private $logger;
    
    // Set up the testing environment
    function setup(){
        parent::setUp();
        $this->logger = Logger::getLogger(__CLASS__);
    }

    
    // Search for a Wiki Page. Search result should be directed to the page itself
    function testSearchPage(){
        $this->logger->info('Test Search Page - Start');
        parent::doOpenLink();
        parent::doLogin();
        $this->click("link=Main page");
        $this->type("//*[@id='searchInput']", $_SESSION["WIKI_SEARCH_PAGE"]);
        $this->click("//*[@id='searchButton']");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals($_SESSION["WIKI_SEARCH_PAGE"], $this->getText("//*[@id='firstHeading']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=Main page");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        parent::doLogout();
        $this->logger->info('Test Search Page - End');
    } 

    
    // Search for a text. Search result should display links which contain the search text
    function testSearchText(){  
        parent::doOpenLink();
        parent::doLogin();
        $this->click("link=Main page");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        $this->type("//*[@id='searchInput']",$_SESSION["WIKI_SEARCH_TEXT"]);
        $this->click("searchButton");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("Search results", $this->getText("firstHeading"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals($_SESSION["WIKI_SEARCH_TEXT"] . " - Search results - Wikipedia, the free encyclopedia", $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
       parent::doLogout();
    }
}
?>
