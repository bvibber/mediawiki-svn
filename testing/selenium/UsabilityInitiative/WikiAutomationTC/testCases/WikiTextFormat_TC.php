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
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_BOLD);
        $this->type(TEXT_EDITOR,TEXT_BOLD);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(50000);
        try {
            $this->assertEquals(TEXT_VALIDATE_BOLD, $this->getText(TEXT_VALIDATE_BOLDTEXT));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        
    }

     // Mark text Italic and verify output
    function verifyTextItalic(){
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_ITALIC);
        $this->type(TEXT_EDITOR, TEXT_ITALIC);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals(TEXT_VALIDATE_ITALIC, $this->getText(TEXT_VALIDATE_ITALICTEXT));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Mark text Bold and Italic and verify output
    function verifyTextItalicandBold(){
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_ITALIC);
	$this->click(LINK_BOLD);
        $this->type(TEXT_EDITOR, TEXT_ITALIC_BOLD);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals(TEXT_VALIDATE_ITALICBOLD, $this->getText(TEXT_VALIDATE_ITALICBOLDTEXT));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Use Bullet Item function and verify output
    function verifyBulletItem(){
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_BULLET);
        $this->click(LINK_BULLET);
        $this->click(LINK_BULLET);
        $this->type(TEXT_EDITOR, TEXT_BULLET);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals(TEXT_BULLET_TEXT, $this->getText(TEXT_BULLET_1));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals(TEXT_BULLET_TEXT, $this->getText(TEXT_BULLET_2));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals(TEXT_BULLET_TEXT, $this->getText(TEXT_BULLET_3));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Use Numbered Item function and verify output
    function verifyNumberedItem(){
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_NUMBERED);
        $this->click(LINK_NUMBERED);
        $this->click(LINK_NUMBERED);
        $this->type(TEXT_EDITOR, TEXT_NUMBERED);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals(TEXT_NUMBERED_TEXT, $this->getText(TEXT_NUMBERED_1));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals(TEXT_NUMBERED_TEXT, $this->getText(TEXT_NUMBERED_2));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals(TEXT_NUMBERED_TEXT, $this->getText(TEXT_NUMBERED_3));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

      // Mark text as Nowiki and verify output
    function verifyNoWiki(){
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->type(TEXT_EDITOR, TEXT_NOWIKI);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals(TEXT_NOWIKI_TEXT, $this->getText(TEXT_NOWIKI_VALIDATE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

     // Create a line break and verify output
    function verifyLineBreak(){
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->type(TEXT_EDITOR, TEXT_LINEBREAK);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals(TEXT_LINEBREAK_TEXT, $this->getText(TEXT_LINEBREAK_VALIDATE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

     // Mark text as Big and verify output
    function verifyTextBig(){
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_TEXTBIG);
        $this->type(TEXT_EDITOR, TEXT_TEXTBIG);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals(TEXT_TEXTBIG_TEXT, $this->getText(TEXT_TEXTBIG_VALIDATE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

     // Mark text as Small and verify output
    function verifyTextSmall(){
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_TEXTSMALL);
        $this->type(TEXT_EDITOR, TEXT_TEXTSMALL);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals(TEXT_TEXTSMALL_TEXT, $this->getText(TEXT_TEXTSMALL_VALIDATE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

     // Mark text as Super Script and verify output
     function verifyTextSuperscript(){
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_TEXTSUPER);
        $this->type(TEXT_EDITOR,TEXT_TEXTSUPER );
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals(TEXT_TEXTSUPER_TEXT, $this->getText(TEXT_TEXTSUPER_VALIDATE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

     // Mark text as Sub Script and verify output
     function verifyTextSubscript(){
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_TEXTSUB);
        $this->type(TEXT_EDITOR,TEXT_TEXTSUB);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals(TEXT_TEXTSUB_TEXT, $this->getText(TEXT_TEXTSUB_VALIDATE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    } 
}
?>
