<?php
require_once 'WikiCommonTC.php';
/**
 * This test case will be handling the Wiki Tool bar Dialog functions
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiToolBarDialogsTC extends WikiCommonTC {
    // Set up the testing environment
    function setup(){
        parent::setUp();
    }

    
    // Add a internal link and verify
    function testInternalLink(){
 
        parent::doOpenLink();
        parent::doLogin();
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        $this->type("wpTextbox1", "");
        $this->click("link=Link");
        $this->type("wikieditor-toolbar-link-int-target", "Daimler-Chrysler");
        $this->assertTrue($this->isElementPresent("wikieditor-toolbar-link-int-target-status-exists"));
        $this->assertEquals("on", $this->getValue("wikieditor-toolbar-link-type-int"));
        $this->click("//div[13]/div[11]/button[1]");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Daimler-Chrysler", $this->getText("link=Daimler-Chrysler"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=Daimler-Chrysler");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertTrue($this->isTextPresent("Daimler-Chrysler"), $this->getText("firstHeading"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        parent::doLogout();

    }

    // Add a internal link with different display text and verify
    function testInternalLinkWithDisplayText(){

        parent::doOpenLink();
        parent::doLogin();
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        $this->type("wpTextbox1", "");
        $this->click("link=Link");
        $this->type("wpTextbox1", "");
        $this->type("wikieditor-toolbar-link-int-target", "Fashion Island");
        $this->type("wikieditor-toolbar-link-int-text", "Fashion Island Test");
        $this->assertTrue($this->isElementPresent("wikieditor-toolbar-link-int-target-status-exists"));
        $this->assertEquals("on", $this->getValue("wikieditor-toolbar-link-type-int"));
        $this->click("//div[13]/div[11]/button[1]");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Fashion Island Test", $this->getText("link=Fashion Island Test"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=Fashion Island Test");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertTrue($this->isTextPresent("Fashion Island"), $this->getText("firstHeading"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();

    }

    // Add a internal link with blank display text and verify
    function testInternalLinkWithBlankDisplayText(){

        parent::doOpenLink();
        parent::doLogin();
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        $this->click("link=Link");
        $this->type("wikieditor-toolbar-link-int-target", "Magical Mystery Tour (film)");
        $this->type("wikieditor-toolbar-link-int-text", "");
        $this->assertTrue($this->isElementPresent("wikieditor-toolbar-link-int-target-status-exists"));
        $this->assertEquals("on", $this->getValue("wikieditor-toolbar-link-type-int"));
        $this->click("//div[13]/div[11]/button[1]");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Magical Mystery Tour (film)", $this->getText("link=Magical Mystery Tour (film)"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=Magical Mystery Tour (film)");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Magical Mystery Tour (film)", $this->getText("firstHeading"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        
        parent::doLogout();

    }

    // Add external link and verify
    function testExternalLink(){

        parent::doOpenLink();
        parent::doLogin();
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        $this->type("wpTextbox1", "");
        $this->click("link=Link");
        $this->type("wikieditor-toolbar-link-int-target", "www.google.com");
        try {
            $this->assertEquals("External link", $this->getText("wikieditor-toolbar-link-int-target-status-external"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        $this->assertEquals("on", $this->getValue("wikieditor-toolbar-link-type-ext"));
        $this->click("//div[13]/div[11]/button[1]");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
         try {
            $this->assertEquals("www.google.com", $this->getText("link=www.google.com"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=www.google.com");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Google", $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        parent::doLogout();
    }

    // Add external link with different display text and verify
    function testExternalLinkWithDisplayText(){

        parent::doOpenLink();
        parent::doLogin();
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        $this->type("wpTextbox1", "");
        $this->click("link=Link");
        $this->type("wikieditor-toolbar-link-int-target", "www.google.com");
        $this->type("wikieditor-toolbar-link-int-text", "Google");
        try {
            $this->assertEquals("External link", $this->getText("wikieditor-toolbar-link-int-target-status-external"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        $this->assertEquals("on", $this->getValue("wikieditor-toolbar-link-type-ext"));
        $this->click("//div[13]/div[11]/button[1]");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Google", $this->getText("link=Google"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=Google");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Google", $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        
        parent::doLogout();

    }

    // Add external link with Blank display text and verify
    function testExternalLinkWithBlankDisplayText(){

        parent::doOpenLink();
        parent::doLogin();
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        $this->type("wpTextbox1", "");
        $this->click("link=Link");
        $this->type("wikieditor-toolbar-link-int-target", "www.google.com");
        $this->type("wikieditor-toolbar-link-int-text", "");
        try {
            $this->assertEquals("External link", $this->getText("wikieditor-toolbar-link-int-target-status-external"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        $this->assertEquals("on", $this->getValue("wikieditor-toolbar-link-type-ext"));
        $this->click("//div[13]/div[11]/button[1]");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
        $this->assertEquals("[1]", $this->getText("link=[1]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click("link=[1]");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Google", $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();

    }

    // Add a table and verify
    function testCreateTable(){
        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Table");
        $this->type("wpTextbox1", "");
        $this->click("wikieditor-toolbar-table-sortable");
        $this->click("//div[3]/button[1]");
        $this->click("wikieditor-toolbar-table-sortable");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Header text", $this->getText("//table[@id='sortable_table_id_0']/tbody/tr[1]/th[3]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        parent::doLogout();
    }

    // Add a table and verify only with head row
    function testCreateTableWithHeadRow(){
        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Table");
        $this->click("wikieditor-toolbar-table-wikitable");
        $this->type("wikieditor-toolbar-table-dimensions-rows", "4");
        $this->type("wikieditor-toolbar-table-dimensions-columns", "4");
        $this->click("//div[3]/button[1]");
        $this->click("wikieditor-toolbar-table-wikitable");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Header text", $this->getTable("//div[@id='wikiPreview']/table.0.0"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        
        parent::doLogout();
    }

    // Add a table and verify only with borders
    function testCreateTableWithBorders(){
        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Table");
        $this->click("wikieditor-toolbar-table-dimensions-header");
        $this->type("wikieditor-toolbar-table-dimensions-columns", "5");
        $this->click("//div[3]/button[1]");
        $this->click("wikieditor-toolbar-table-dimensions-header");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Example", $this->getTable("//div[@id='wikiPreview']/table.1.3"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();
    }

    // Add a table and verify only with sort row
    function testCreateTableWithSortRow(){
        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
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
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Example", $this->getTable("sortable_table_id_0.0.0"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        parent::doLogout();
    }

      // Add a table without headers,borders and sort rows
    function testCreateTableWithNoSpecialEffects(){
        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
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
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Example", $this->getTable("//div[@id='wikiPreview']/table.0.0"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        parent::doLogout();
    }
       
    // Verify the replace all function on Search and Replace
     function testTextSearchReplaceAll(){

        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Search and replace");
        $this->type("wpTextbox1", "calcey qa\n\ncalcey qa\n\ncalcey qa");
        $this->type("wikieditor-toolbar-replace-search", "calcey");
        $this->type("wikieditor-toolbar-replace-search", "calcey qa");
        $this->type("wikieditor-toolbar-replace-replace", "test team");
        $this->click("//button[3]");
        $this->click("//button[4]");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("test team", $this->getText("//div[@id='wikiPreview']/p[1]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("test team", $this->getText("//div[@id='wikiPreview']/p[2]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("test team", $this->getText("//div[@id='wikiPreview']/p[3]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();

    }

    // Verify the replace next function on Search and Replace
    function testTextSearchReplaceNext(){

        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Search and replace");
        $this->type("wpTextbox1", "calcey qa\n\ncalcey qa\n\ncalcey qa");
        $this->click("link=Search and replace");
        $this->type("wpTextbox1", "calcey qa\n\ncalcey qa\n\ncalcey qa");
        $this->type("wikieditor-toolbar-replace-search", "calcey qa");
        $this->type("wikieditor-toolbar-replace-replace", "test team");
        $this->click("//div[13]/div[11]/button[2]");
        $this->click("//div[13]/div[11]/button[2]");
        $this->click("//button[4]");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("test team", $this->getText("//div[@id='wikiPreview']/p[1]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("test team", $this->getText("//div[@id='wikiPreview']/p[2]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("calcey qa", $this->getText("//div[@id='wikiPreview']/p[3]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        parent::doLogout();

    }

    /** When user click on find, text highlight on back which is not captured in Selenium directly. 
    function testTextSearchFindNext(){
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Search and replace");



        parent::doLogout();
    }*/
}
?>
