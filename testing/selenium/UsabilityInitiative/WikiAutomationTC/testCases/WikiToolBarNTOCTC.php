<?php
require_once 'WikiCommonTC.php';
/**
 * This test case will be handling the NTOC related functions.
 * Adding different header levels via tool bar and verify the output
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiToolBarNTOC extends WikiCommonTC {
    // Set up the testing environment
    function setup(){
        parent::setUp();
    }

    // Add header level 2 and verify Header output
     function testHeaderLevel2(){

        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->waitForPageToLoad("30000");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->click("link=Heading");
        $this->click("link=Heading");
        $this->click("link=Level 2");
        $this->type("wpTextbox1", "==Heading text==");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Heading text", $this->getText("//*[@id='wikiPreview']/h2"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();

    }

    // Add header level 3 and verify Header output
    function testHeaderLevel3(){

        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->waitForPageToLoad("30000");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->click("link=Heading");
        $this->click("link=Heading");
        $this->click("link=Level 3");
        $this->type("wpTextbox1", "===Heading text===");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Heading text", $this->getText("//*[@id='wikiPreview']/h3"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();

    }

    // Add header level 4 and verify Header output
    function testHeaderLevel4(){

        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->waitForPageToLoad("30000");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->click("link=Heading");
        $this->click("link=Heading");
        $this->click("link=Level 4");
        $this->type("wpTextbox1", "====Heading text====");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Heading text", $this->getText("//*[@id='wikiPreview']/h4"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();

    }

    // Add header level 5 and verify Header output
    function testHeaderLevel5(){
 
        parent::doOpenLink();
        parent::doLogin();
        $this->open("/deployment-en/Main_Page");
        $this->waitForPageToLoad("30000");
        $this->click("link=Random article");
        $this->waitForPageToLoad("30000");
        $this->click("//li[@id='ca-edit']/a/span");
        $this->waitForPageToLoad("30000");
        parent::doExpandAdvanceSection();
        $this->click("link=Heading");
        $this->click("link=Heading");
        $this->click("link=Level 5");
        $this->type("wpTextbox1", "=====Heading text=====");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Heading text", $this->getText("//*[@id='wikiPreview']/h5"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }

        parent::doLogout();

    }
}
?>
