<?php
session_start();
require_once 'WikiCommonFunction_TC.php';
require_once 'Config.php';
/**
 * This test case will be handling the page watch functions.
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiWatchUnWatch_TC  extends WikiCommonFunction_TC {
    // Set up the testing environment
    function setup(){
        parent::setUp();
    }

    // Mark a page as watch and verify the My Watch list
    function testWatch(){
        parent::doOpenLink();
        parent::doLogin();
        $this->click("link=Random article");      
        $this->waitForPageToLoad("30000");
        $randompage = $this->getText("firstHeading");
        $this->click("link=Watch");
        $this->click("link=My watchlist");
        $this->waitForPageToLoad("30000");
        $this->click("link=View and edit watchlist");
        $this->waitForPageToLoad("30000");
        
        $this->assertTrue($this->isTextPresent($randompage));
        parent::doLogout();
    }


    // Mark a page as watch and mark the same page as unwatch and verify the My Watch list
    function testUnWatch(){

       parent::doOpenLink();
        parent::doLogin();
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $randompage = $this->getText("firstHeading");
        $this->click("link=Watch");
        $this->click("link=My watchlist");
        $this->waitForPageToLoad("30000");
        $this->click("link=View and edit watchlist");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isTextPresent($randompage));
        $this->click("link=" . $randompage);
        $this->waitForPageToLoad("30000");
        $this->click("link=Unwatch");
        $this->click("link=My watchlist");
        $this->waitForPageToLoad("30000");
        $this->click("link=View and edit watchlist");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertFalse($this->isTextPresent($randompage));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        parent::doLogout();

    }

    // Mark a page as watch on page edit and verify the My Watch list
    function testPageWatchonEdit(){
        parent::doOpenLink();
        parent::doLogin();
        parent::doAccessRandomPage();
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        $randompage = $this->getText("firstHeading");
        parent::doEditPage();
        try {
        $this->assertEquals("Watch", $this->getText("link=Watch"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("wpWatchthis");
        $this->click("wpSave");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("Unwatch", $this->getText("link=Unwatch"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=My watchlist");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        $this->click("link=View and edit watchlist");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        $this->assertTrue($this->isTextPresent($randompage));

        parent::doLogout();
    }
    
}
?>
