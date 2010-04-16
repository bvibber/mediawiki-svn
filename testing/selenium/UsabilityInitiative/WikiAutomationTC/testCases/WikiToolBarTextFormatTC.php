<?php
require_once 'WikiCommonTC.php';
/**
 * This test case will be handling the text formatting related functions via tool bar
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiToolBarTextFormatTC extends WikiCommonTC {
     // Set up the testing environment
     function setup(){
        parent::setUp();
    }

    // Mark text Bold and verify output
     function testMakeTextBold(){

        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        $this->type("wpTextbox1", "");
        $this->click("link=Bold");
        $this->type("wpTextbox1", "'''Bold''' text");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Bold", $this->getText("//div[@id='wikiPreview']/p/b"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();

    }

     // Mark text Italic and verify output
    function testMakeTextItalic(){

        parent::doOpenLink();
        parent::doLogin();
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        $this->type("wpTextbox1", "");
        $this->click("link=Italic");
        $this->type("wpTextbox1", "''Italian'' text");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("Italian", $this->getText("//div[@id='wikiPreview']/p/i"));
        parent::doLogout();

    }

    // Use Bullet Item function and verify output
    function testBulletItem(){

        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Bulleted list");
        $this->click("link=Bulleted list");
        $this->click("link=Bulleted list");
        $this->type("wpTextbox1", "* Bulleted list item\n* Bulleted list item\n* Bulleted list item");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Bulleted list item", $this->getText("//div[@id='wikiPreview']/ul/li[1]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("Bulleted list item", $this->getText("//div[@id='wikiPreview']/ul/li[2]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("Bulleted list item", $this->getText("//div[@id='wikiPreview']/ul/li[3]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();

    }

    // Use Numbered Item function and verify output
    function testNumberedItem(){

        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Numbered list");
        $this->click("link=Numbered list");
        $this->click("link=Numbered list");
        $this->type("wpTextbox1", "# Numbered list item\n# Numbered list item\n# Numbered list item");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Numbered list item", $this->getText("//div[@id='wikiPreview']/ol/li[1]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("Numbered list item", $this->getText("//div[@id='wikiPreview']/ol/li[2]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("Numbered list item", $this->getText("//div[@id='wikiPreview']/ol/li[3]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
       parent::doLogout();

    }

      // Mark text as Nowiki and verify output
    function testNoWiki(){

        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->type("wpTextbox1", "<nowiki>==Heading text==</nowiki>");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("==Heading text==", $this->getText("//div[@id='wikiPreview']/p"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
         parent::doLogout();

    }

     // Create a line break and verify output
    function testLineBreak(){

        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->type("wpTextbox1", "this is a test text to check the line\n break.");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("this is a test text to check the line\n break.", $this->getText("//div[@id='wikiPreview']/p"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        parent::doLogout();

    }

     // Mark text as Big and verify output
    function testTextBig(){

        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Big");
        $this->type("wpTextbox1", "<big>This</big> text");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("This", $this->getText("//div[@id='wikiPreview']/p/big"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();

    }

     // Mark text as Small and verify output
    function testTextSmall(){

        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Small");
        $this->type("wpTextbox1", "<small>This</small> text\n");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("This", $this->getText("//div[@id='wikiPreview']/p/small"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

         parent::doLogout();

    }

     // Mark text as Super Script and verify output
     function testTextSuperscript(){

        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Superscript");
        $this->type("wpTextbox1", "<sup>This</sup> text\n");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("This", $this->getText("//div[@id='wikiPreview']/p/sup"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

         parent::doLogout();

    }

     // Mark text as Sub Script and verify output
     function testTextSubscript(){

        parent::doOpenLink();
         parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Subscript");
        $this->type("wpTextbox1", "<sub>This</sub> text\n");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("This", $this->getText("//div[@id='wikiPreview']/p/sub"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

         parent::doLogout();

    }
    
}
?>
