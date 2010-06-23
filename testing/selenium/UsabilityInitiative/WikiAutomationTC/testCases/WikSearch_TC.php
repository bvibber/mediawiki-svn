<?php
require_once 'WikiCommonFunction_TC.php';
include 'Config.php';

/**
 * This test case will be handling the search function.
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiSearch_TC extends WikiCommonFunction_TC {
    
    // Set up the testing environment
    function setup(){
        parent::setUp();
    }

    // Search for a Wiki Page. Search result should be directed to the page itself
    function testSearchPage(){
        parent::doOpenLink();
        parent::doLogin();
        $this->type(INPUT_SEARCH_BOX,(WIKI_SEARCH_PAGE));
        $this->click(BUTTON_SEARCH);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals((WIKI_SEARCH_PAGE), $this->getText(TEXT_PAGE_HEADING));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        parent::doLogout();
    } 

    // Search for a text. Search result should display links which contain the search text
    function testSearchText(){  
        parent::doOpenLink();
        parent::doLogin();
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        $this->type(INPUT_SEARCH_BOX,WIKI_TEXT_SEARCH);
        $this->click(BUTTON_SEARCH);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals("Search results", $this->getText(TEXT_PAGE_HEADING));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals((WIKI_TEXT_SEARCH) . TEXT_SEARCH_RESULT_HEADING, $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
       parent::doLogout();
    }
}
?>
