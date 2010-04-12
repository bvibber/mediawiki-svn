<?php
require_once 'WikiCommonTC.php';
/**
 * This test case will be handling the page watch functions.
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiWatchUnWatchTC  extends WikiCommonTC {
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

   /** function testEditWatch(){

    }**/
    
}
?>
