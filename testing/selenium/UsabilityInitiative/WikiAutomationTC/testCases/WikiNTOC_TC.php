<?php
require_once 'WikiCommonFunction_TC.php';
include 'Config.php';
/*
 * This test case will be handling the NTOC related functions.
 * Adding different header levels via tool bar and verify the output
 * This will be used by both WikiExistingPageNTOC and WikiNewPageNTOC
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiNTOC_TC extends WikiCommonFunction_TC {
      
    // Add header level 2 and verify Header output
     function verifyHeaderLevel2(){
        parent::doExpandAdvanceSection();
        $this->click(LINK_HEADER);
        $this->click(LINK_LEVEL2HEADER);
        $this->type(TEXT_EDITOR, TEXT_LEVEL2HEADER);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals(TEXT_HEADER, $this->getText(TEXT_LEVEL2HEADER_SIZE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    
    // Add header level 3 and verify Header output
    function verifyHeaderLevel3(){
        parent::doExpandAdvanceSection();
        $this->click(LINK_HEADER);
        $this->click(LINK_LEVEL3HEADER);
        $this->type(TEXT_EDITOR, TEXT_LEVEL3HEADER);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals(TEXT_HEADER, $this->getText(TEXT_LEVEL3HEADER_SIZE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add header level 4 and verify Header output
    function verifyHeaderLevel4(){
        parent::doExpandAdvanceSection();
        $this->click(LINK_HEADER);
        $this->click(LINK_LEVEL4HEADER);
        $this->type(TEXT_EDITOR, TEXT_LEVEL4HEADER);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals(TEXT_HEADER, $this->getText(TEXT_LEVEL4HEADER_SIZE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add header level 5 and verify Header output
    function verifyHeaderLevel5(){
        parent::doExpandAdvanceSection();
        $this->click(LINK_HEADER);
        $this->click(LINK_LEVEL5HEADER);
        $this->type(TEXT_EDITOR, TEXT_LEVEL5HEADER);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals(TEXT_HEADER, $this->getText(TEXT_LEVEL5HEADER_SIZE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }
    
     // Add header level 2 & 3 and verify Header output
    function verifyHeaderLevel2and3(){
        parent::doExpandAdvanceSection();
        $this->click(LINK_HEADER);
        $this->click(LINK_LEVEL2HEADER);
        $this->click(LINK_HEADER);
        $this->click(LINK_LEVEL3HEADER);
        $this->type(TEXT_EDITOR, TEXT_LEVEL2HEADER ."\n". TEXT_LEVEL3HEADER );
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals(TEXT_HEADER, $this->getText(TEXT_LEVEL2HEADER_SIZE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
         try {
            $this->assertEquals(TEXT_HEADER, $this->getText(TEXT_LEVEL3HEADER_SIZE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

 // Add header level 2 , 3 & 4 and verify Header output
    function verifyHeaderLevel23and4(){
        parent::doExpandAdvanceSection();
        $this->click(LINK_HEADER);
        $this->click(LINK_LEVEL2HEADER);
        $this->click(LINK_HEADER);
        $this->click(LINK_LEVEL3HEADER);
        $this->click(LINK_HEADER);
        $this->click(LINK_LEVEL4HEADER);
        $this->type(TEXT_EDITOR, TEXT_LEVEL2HEADER ."\n". TEXT_LEVEL3HEADER ."\n". TEXT_LEVEL4HEADER );
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals(TEXT_HEADER, $this->getText(TEXT_LEVEL2HEADER_SIZE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
         try {
            $this->assertEquals(TEXT_HEADER, $this->getText(TEXT_LEVEL3HEADER_SIZE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals(TEXT_HEADER, $this->getText(TEXT_LEVEL4HEADER_SIZE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add header level 2 , 3 , 4 & 5 and verify Header output
    function verifyHeaderLevel234and5(){
        parent::doExpandAdvanceSection();
        $this->click(LINK_HEADER);
        $this->click(LINK_LEVEL2HEADER);
        $this->click(LINK_HEADER);
        $this->click(LINK_LEVEL3HEADER);
        $this->click(LINK_HEADER);
        $this->click(LINK_LEVEL4HEADER);
        $this->click(LINK_HEADER);
        $this->click(LINK_LEVEL5HEADER);
        $this->type(TEXT_EDITOR, TEXT_LEVEL2HEADER ."\n". TEXT_LEVEL3HEADER ."\n". TEXT_LEVEL4HEADER ."\n". TEXT_LEVEL5HEADER);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals(TEXT_HEADER, $this->getText(TEXT_LEVEL2HEADER_SIZE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
         try {
            $this->assertEquals(TEXT_HEADER, $this->getText(TEXT_LEVEL3HEADER_SIZE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals(TEXT_HEADER, $this->getText(TEXT_LEVEL4HEADER_SIZE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals(TEXT_HEADER, $this->getText(TEXT_LEVEL5HEADER_SIZE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    } 
}
?>
