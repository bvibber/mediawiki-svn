<?php
session_start();
require_once 'WikiCommonFunction_TC.php';
require_once 'Config.php';
/**
 * This test case will be handling the text formatting related functions via tool bar
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiTextFormat_TC extends WikiCommonFunction_TC {

    // Mark text Bold and verify output
     function verifyTextBold(){
        $this->type("wpTextbox1", "");
        $this->click("link=Bold");
        $this->type("wpTextbox1", "'''Bold''' text");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("Bold", $this->getText("//div[@id='wikiPreview']/p/b"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

     // Mark text Italic and verify output
    function verifyTextItalic(){
        $this->type("wpTextbox1", "");
        $this->click("link=Italic");
        $this->type("wpTextbox1", "''Italian'' text");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("Italian", $this->getText("//div[@id='wikiPreview']/p/i"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Mark text Bold and Italic and verify output
    function verifyTextItalicandBold(){
        $this->type("wpTextbox1", "");
        $this->click("link=Italic");
	$this->click("link=Bold");
        $this->type("wpTextbox1", "Text '''''Italic & Bold'''''");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("Italic & Bold", $this->getText("//div[@id='wikiPreview']/p/i/b"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }


    // Use Bullet Item function and verify output
    function verifyBulletItem(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Bulleted list");
        $this->click("link=Bulleted list");
        $this->click("link=Bulleted list");
        $this->type("wpTextbox1", "* Bulleted list item\n* Bulleted list item\n* Bulleted list item");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("Bulleted list item", $this->getText("//div[@id='wikiPreview']/ul/li[1]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("Bulleted list item", $this->getText("//div[@id='wikiPreview']/ul/li[2]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("Bulleted list item", $this->getText("//div[@id='wikiPreview']/ul/li[3]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Use Numbered Item function and verify output
    function verifyNumberedItem(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Numbered list");
        $this->click("link=Numbered list");
        $this->click("link=Numbered list");
        $this->type("wpTextbox1", "# Numbered list item\n# Numbered list item\n# Numbered list item");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("Numbered list item", $this->getText("//div[@id='wikiPreview']/ol/li[1]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("Numbered list item", $this->getText("//div[@id='wikiPreview']/ol/li[2]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("Numbered list item", $this->getText("//div[@id='wikiPreview']/ol/li[3]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

      // Mark text as Nowiki and verify output
    function verifyNoWiki(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->type("wpTextbox1", "<nowiki>==Heading text==</nowiki>");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("==Heading text==", $this->getText("//div[@id='wikiPreview']/p"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

     // Create a line break and verify output
    function verifyLineBreak(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->type("wpTextbox1", "this is a test text to check the line\n break.");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("this is a test text to check the line\n break.", $this->getText("//div[@id='wikiPreview']/p"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

     // Mark text as Big and verify output
    function verifyTextBig(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Big");
        $this->type("wpTextbox1", "<big>This</big> text");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("This", $this->getText("//div[@id='wikiPreview']/p/big"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

     // Mark text as Small and verify output
    function verifyTextSmall(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Small");
        $this->type("wpTextbox1", "<small>This</small> text\n");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("This", $this->getText("//div[@id='wikiPreview']/p/small"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

     // Mark text as Super Script and verify output
     function verifyTextSuperscript(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Superscript");
        $this->type("wpTextbox1", "<sup>This</sup> text\n");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("This", $this->getText("//div[@id='wikiPreview']/p/sup"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

     // Mark text as Sub Script and verify output
     function verifyTextSubscript(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->click("link=Subscript");
        $this->type("wpTextbox1", "<sub>This</sub> text\n");
        $this->click("wpPreview");
        $this->waitForPageToLoad($_SESSION["WIKI_TEST_WAIT_TIME"]);
        try {
            $this->assertEquals("This", $this->getText("//div[@id='wikiPreview']/p/sub"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    } 
}
?>
