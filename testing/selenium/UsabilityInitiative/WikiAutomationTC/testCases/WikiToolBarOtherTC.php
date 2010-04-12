<?php
require_once 'WikiCommonTC.php';
/**
 * This test case will be handling the general tool bar functions
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiToolBarOtherTC extends WikiCommonTC {
    // Set up the testing environment
    function setup(){
        parent::setUp();
    }

    // Click on Embedded file function and verify the output
    function testEmbeddedFile(){
        parent::doOpenLink();
        parent::doLogin();
        
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        $this->click("link=Embedded file");
        $this->type("wpTextbox1", "\" \"");
        $this->type("wpTextbox1", "[[File:Example.jpg]]");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("", $this->getText("//img[@alt='Example.jpg']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        parent::doLogout();
    }

    /** Reference link is not directing to the given link.
     * For example if I add www.google.com as the reference link,
     * on preview the link is not directing to google site.
    function testReference(){
        parent::doOpenLink();
        parent::doLogin();

        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        $this->type("wpTextbox1", "");
        $this->click("link=Reference");
        $this->type("wpTextbox1", "");
        $this->type("wikieditor-toolbar-reference-text", "www.google.com");
        $this->click("//div[13]/div[11]/button[1]");
        $this->click("wpPreview");

        parent::doLogout();
    }**/

    // Click on Picture Gallery function and verify the output
    function testPictureGallery(){
        parent::doOpenLink();
        parent::doLogin();

        $this->open("/deployment-en/Main_Page");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        $this->type("wpTextbox1", "");
        $this->click("link=Picture gallery");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("", $this->getText("//div[@id='wikiPreview']/table/tbody/tr/td[1]/div/div[1]/div"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();
    }

    
}
?>
