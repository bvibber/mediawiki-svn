<?php
require_once 'WikiCommonFunction_TC.php';
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
        $this->click("link=Main page");
        $this->type("//*[@id='searchInput']", "Hair (musical)");
        $this->click("//*[@id='searchButton']");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Hair (musical)", $this->getText("//*[@id='firstHeading']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=Main page");
        $this->waitForPageToLoad("30000");
        parent::doLogout();
    }

    // Search for a text. Search result should display links which contain the search text
    function testSearchText(){  
        parent::doOpenLink();
        parent::doLogin();
        $this->click("link=Main page");
        $this->waitForPageToLoad("30000");
        $this->type("//*[@id='searchInput']", "TV");
        $this->click("searchButton");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Search results", $this->getText("firstHeading"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("TV - Search results - Wikipedia, the free encyclopedia", $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
       parent::doLogout();
    }

}
?>
