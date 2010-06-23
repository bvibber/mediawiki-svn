<?php
require_once 'WikiCommonFunction_TC.php';
require_once 'Config.php';
/**
 * This test case will be handling the general tool bar functions
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiToolBarOther_TC extends WikiCommonFunction_TC {
  
    // Add Embedded file function and verify the output
    function verifyEmbeddedFile(){
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_TEXTEMBEDDED);
        $this->type(TEXT_EDITOR, TEXT_TEXTEMBEDDED);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
        $this->assertTrue($this->isElementPresent(TEXT_TEXTEMBEDDED_VALIDATE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    } 

    //Add Reference file function and verify the output
    function verifyReference(){
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_TEXTREFERENCE);
        $this->type(INPUT_TEXTREFERENCE,TEXT_TEXTREFERENCE);
        $this->click(BUTTON_REFERENCE);
        $this->type(TEXT_EDITOR, TEXT_TEXTREFERENCE_EDITOR);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals(TEXT_REFERENCEID, $this->getText(TEXT_REFERENCELINK));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertTrue($this->isTextPresent(TEXT_REFERENCE));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();
    }

    // Add Picture Gallery function and verify the output
    function verifyPictureGallery(){
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(TEXT_PICTURELINK);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        $this->assertTrue($this->isElementPresent(IMAGE_EXAMPLE1));
        try {
            $this->assertEquals(TEXT_IMG1CAPTION, $this->getTable(TABLE_CAPTION1));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->assertTrue($this->isElementPresent(IMAGE_EXAMPLE2));
        try {
            $this->assertEquals(TEXT_IMG2CAPTION, $this->getTable(TABLE_CAPTION2));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    
}
?>
