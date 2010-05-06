<?php
session_start();
require_once 'WikiCommonFunction_TC.php';
require_once 'Config.php';
/**
 * This test case will be handling the Wiki Tool bar Dialog functions
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiDialogs_TC extends WikiCommonFunction_TC {

    // Add a internal link and verify
    function verifyInternalLink(){
        $this->type("wpTextbox1", "");
        $this->click("link=Link");
        $this->type("wikieditor-toolbar-link-int-target", $_SESSION["WIKI_INTERNAL_LINK"]);
        $this->assertTrue($this->isElementPresent("wikieditor-toolbar-link-int-target-status-exists"));
        $this->assertEquals("on", $this->getValue("wikieditor-toolbar-link-type-int"));
        $this->click("//div[13]/div[11]/button[1]");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals($_SESSION["WIKI_INTERNAL_LINK"], $this->getText("link=" . $_SESSION["WIKI_INTERNAL_LINK"]));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=Daimler-Chrysler");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertTrue($this->isTextPresent($_SESSION["WIKI_INTERNAL_LINK"]), $this->getText("firstHeading"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a internal link with different display text and verify
    function verifyInternalLinkWithDisplayText(){
        $this->type("wpTextbox1", "");
        $this->click("link=Link");
        $this->type("wpTextbox1", "");
        $this->type("wikieditor-toolbar-link-int-target", $_SESSION["WIKI_INTERNAL_LINK"]);
        $this->type("wikieditor-toolbar-link-int-text", $_SESSION["WIKI_INTERNAL_LINK"] . " Test");
        $this->assertTrue($this->isElementPresent("wikieditor-toolbar-link-int-target-status-exists"));
        $this->assertEquals("on", $this->getValue("wikieditor-toolbar-link-type-int"));
        $this->click("//div[13]/div[11]/button[1]");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals($_SESSION["WIKI_INTERNAL_LINK"]." Test", $this->getText("link=" .$_SESSION["WIKI_INTERNAL_LINK"] ." Test"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=" .$_SESSION["WIKI_INTERNAL_LINK"]." Test");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertTrue($this->isTextPresent($_SESSION["WIKI_INTERNAL_LINK"]), $this->getText("firstHeading"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a internal link with blank display text and verify
    function verifyInternalLinkWithBlankDisplayText(){
        $this->click("link=Link");
        $this->type("wikieditor-toolbar-link-int-target", $_SESSION["WIKI_INTERNAL_LINK"]);
        $this->type("wikieditor-toolbar-link-int-text", "");
        $this->assertTrue($this->isElementPresent("wikieditor-toolbar-link-int-target-status-exists"));
        $this->assertEquals("on", $this->getValue("wikieditor-toolbar-link-type-int"));
        $this->click("//div[13]/div[11]/button[1]");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals($_SESSION["WIKI_INTERNAL_LINK"], $this->getText("link=".$_SESSION["WIKI_INTERNAL_LINK"]));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=".$_SESSION["WIKI_INTERNAL_LINK"]);
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals($_SESSION["WIKI_INTERNAL_LINK"], $this->getText("firstHeading"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add external link and verify
    function verifyExternalLink(){
        $this->type("wpTextbox1", "");
        $this->click("link=Link");
        $this->type("wikieditor-toolbar-link-int-target", "www.google.com");
        try {
            $this->assertEquals("External link", $this->getText("wikieditor-toolbar-link-int-target-status-external"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->assertEquals("on", $this->getValue("wikieditor-toolbar-link-type-ext"));
        $this->click("//div[13]/div[11]/button[1]");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
         try {
            $this->assertEquals($_SESSION["WIKI_EXTERNAL_LINK"], $this->getText("link=".$_SESSION["WIKI_EXTERNAL_LINK"]));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=".$_SESSION["WIKI_EXTERNAL_LINK"]);
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals($_SESSION["WIKI_EXTERNAL_LINK_TITLE"], $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add external link with different display text and verify
    function verifyExternalLinkWithDisplayText(){
        $this->type("wpTextbox1", "");
        $this->click("link=Link");
        $this->type("wikieditor-toolbar-link-int-target", $_SESSION["WIKI_EXTERNAL_LINK"]);
        $this->type("wikieditor-toolbar-link-int-text", $_SESSION["WIKI_EXTERNAL_LINK_TITLE"]);
        try {
            $this->assertEquals("External link", $this->getText("wikieditor-toolbar-link-int-target-status-external"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->assertEquals("on", $this->getValue("wikieditor-toolbar-link-type-ext"));
        $this->click("//div[13]/div[11]/button[1]");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals($_SESSION["WIKI_EXTERNAL_LINK_TITLE"], $this->getText("link=".$_SESSION["WIKI_EXTERNAL_LINK_TITLE"]));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=".$_SESSION["WIKI_EXTERNAL_LINK_TITLE"]);
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals($_SESSION["WIKI_EXTERNAL_LINK_TITLE"], $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }

    }

    // Add external link with Blank display text and verify
    function verifyExternalLinkWithBlankDisplayText(){
        $this->type("wpTextbox1", "");
        $this->click("link=Link");
        $this->type("wikieditor-toolbar-link-int-target", $_SESSION["WIKI_EXTERNAL_LINK"]);
        $this->type("wikieditor-toolbar-link-int-text", "");
        try {
            $this->assertEquals("External link", $this->getText("wikieditor-toolbar-link-int-target-status-external"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->assertEquals("on", $this->getValue("wikieditor-toolbar-link-type-ext"));
        $this->click("//div[13]/div[11]/button[1]");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
        $this->assertEquals("[1]", $this->getText("link=[1]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=[1]");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals($_SESSION["WIKI_EXTERNAL_LINK_TITLE"], $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a table and verify
    function verifyCreateTable(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Table");
        $this->type("wpTextbox1", "");
        $this->click("wikieditor-toolbar-table-sortable");
        $this->click("//div[3]/button[1]");
        $this->click("wikieditor-toolbar-table-sortable");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("Header text", $this->getText("//table[@id='sortable_table_id_0']/tbody/tr[1]/th[3]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a table and verify only with head row
    function verifyCreateTableWithHeadRow(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Table");
        $this->click("wikieditor-toolbar-table-wikitable");
        $this->type("wikieditor-toolbar-table-dimensions-rows", "4");
        $this->type("wikieditor-toolbar-table-dimensions-columns", "4");
        $this->click("//div[3]/button[1]");
        $this->click("wikieditor-toolbar-table-wikitable");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("Header text", $this->getTable("//div[@id='wikiPreview']/table.0.0"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a table and verify only with borders
    function verifyCreateTableWithBorders(){
        $this->type("wpTextbox1", "");
        $this->click("link=Table");
        $this->click("wikieditor-toolbar-table-dimensions-header");
        $this->type("wikieditor-toolbar-table-dimensions-columns", "5");
        $this->click("//div[3]/button[1]");
        $this->click("wikieditor-toolbar-table-dimensions-header");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("Example", $this->getTable("//div[@id='wikiPreview']/table.1.3"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a table and verify only with sort row
    function verifyCreateTableWithSortRow(){
        $this->type("wpTextbox1", "");
        $this->click("link=Table");
        $this->click("wikieditor-toolbar-table-dimensions-header");
        $this->click("wikieditor-toolbar-table-wikitable");
        $this->click("wikieditor-toolbar-table-sortable");
        $this->type("wikieditor-toolbar-table-dimensions-rows", "2");
        $this->type("wikieditor-toolbar-table-dimensions-columns", "5");
        $this->click("//div[3]/button[1]");
        $this->click("wikieditor-toolbar-table-dimensions-header");
        $this->click("wikieditor-toolbar-table-wikitable");
        $this->click("wikieditor-toolbar-table-sortable");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("Example", $this->getTable("sortable_table_id_0.0.0"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a table without headers,borders and sort rows
    function verifyCreateTableWithNoSpecialEffects(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Table");
        $this->click("wikieditor-toolbar-table-wikitable");
        $this->click("wikieditor-toolbar-table-dimensions-header");
        $this->type("wikieditor-toolbar-table-dimensions-rows", "6");
        $this->type("wikieditor-toolbar-table-dimensions-columns", "2");
        $this->click("//div[3]/button[1]");
        $this->click("wikieditor-toolbar-table-dimensions-header");
        $this->click("wikieditor-toolbar-table-wikitable");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("Example", $this->getTable("//div[@id='wikiPreview']/table.0.0"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }
       
     // Verify the replace all function on Search and Replace
     function verifyTextSearchReplaceAll(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Search and replace");
        $this->type("wpTextbox1", $_SESSION["WIKI_SAMPLE_TEXT"]);
        $this->type("wikieditor-toolbar-replace-search", $_SESSION["WIKI_SEARCH_TEXT"]);
        $this->type("wikieditor-toolbar-replace-replace", $_SESSION["WIKI_REPLACE_TEXT"]);
        $this->click("//button[3]");
        $this->click("//button[4]");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals($_SESSION["WIKI_REPLACE_TEXT"], $this->getText("//div[@id='wikiPreview']/p[1]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals($_SESSION["WIKI_REPLACE_TEXT"], $this->getText("//div[@id='wikiPreview']/p[2]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals($_SESSION["WIKI_REPLACE_TEXT"], $this->getText("//div[@id='wikiPreview']/p[3]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Verify the replace next function on Search and Replace
    function verifyTextSearchReplaceNext(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Search and replace");
        $this->type("wpTextbox1", $_SESSION["WIKI_SAMPLE_TEXT"]);
        $this->click("link=Search and replace");
        $this->type("wpTextbox1", $_SESSION["WIKI_SAMPLE_TEXT"]);
        $this->type("wikieditor-toolbar-replace-search", $_SESSION["WIKI_SEARCH_TEXT"]);
        $this->type("wikieditor-toolbar-replace-replace", $_SESSION["WIKI_REPLACE_TEXT"]);
        $this->click("//div[13]/div[11]/button[2]");
        $this->click("//div[13]/div[11]/button[2]");
        $this->click("//button[4]");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals($_SESSION["WIKI_REPLACE_TEXT"], $this->getText("//div[@id='wikiPreview']/p[1]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals($_SESSION["WIKI_REPLACE_TEXT"], $this->getText("//div[@id='wikiPreview']/p[2]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals($_SESSION["WIKI_SEARCH_TEXT"], $this->getText("//div[@id='wikiPreview']/p[3]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

   /*
    * // When user click on find, text highlight on back which is not captured in Selenium directly.
    function verifyTextSearchFindNext(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->type("wpTextbox1", "test qa\ntest qa\ntest qa");
        $this->click("link=Search and replace");
        $this->type("wikieditor-toolbar-replace-search", "Test");

    }
    */
}
?>
