<?php
require_once 'WikiCommonFunction_TC.php';
include 'Config.php';
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
        parent::doAccessRandomPage();
        $randompage = $this->getText(TEXT_PAGE_HEADING);
        $this->click(LINK_WATCH_PAGE);
        $this->click(LINK_WATCH_LIST);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        $this->click(LINK_WATCH_EDIT);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertTrue($this->isTextPresent($randompage));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();
    }


    // Mark a page as watch and mark the same page as unwatch and verify the My Watch list
    function testUnWatch(){
        parent::doOpenLink();
        parent::doLogin();
        parent::doAccessRandomPage();
        $randompage = $this->getText(TEXT_PAGE_HEADING);
        $this->click(LINK_WATCH_PAGE);
        $this->click(LINK_WATCH_LIST);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        $this->click(LINK_WATCH_EDIT);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        $this->assertTrue($this->isTextPresent($randompage));
        $this->click(LINK_START . $randompage);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        $this->click(LINK_UNWATCH);
        $this->click(LINK_WATCH_LIST);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        $this->click(LINK_WATCH_EDIT);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertFalse($this->isTextPresent($randompage));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        parent::doLogout();

    }

    // Mark a page as watch on page edit and verify the My Watch list
    function testPageWatchonEdit(){
        parent::doOpenLink();
        parent::doLogin();
        parent::doAccessRandomPage();
        $randompage = $this->getText(TEXT_PAGE_HEADING);
        parent::doEditPage();
        try {
        $this->assertEquals(TEXT_WATCH, $this->getText(LINK_WATCH_PAGE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click(BUTTON_WATCH);
        $this->click(BUTTON_SAVE_WATCH);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals(TEXT_UNWATCH, $this->getText(LINK_UNWATCH));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click(LINK_WATCH_LIST);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        $this->click(LINK_WATCH_EDIT);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        $this->assertTrue($this->isTextPresent($randompage));

        parent::doLogout();
    }
    
}
?>
