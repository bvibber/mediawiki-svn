<?php
require_once 'WikiCommonFunction_TC.php';
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
    }

    
    // Add header level 3 and verify Header output
    function verifyHeaderLevel3(){
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
    }

    // Add header level 4 and verify Header output
    function verifyHeaderLevel4(){
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
    }

    // Add header level 5 and verify Header output
    function verifyHeaderLevel5(){
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
    }
    
     // Add header level 2 & 3 and verify Header output
    function verifyHeaderLevel2and3(){
        parent::doExpandAdvanceSection();
        $this->click("link=Heading");
        $this->click("link=Level 2");
        $this->click("link=Heading");
        $this->click("link=Level 3");
        $this->type("wpTextbox1", "==Heading text==\n===Heading text===");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Heading text", $this->getText("//*[@id='wikiPreview']/h2"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
         try {
            $this->assertEquals("Heading text", $this->getText("//*[@id='wikiPreview']/h3"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
    }

 // Add header level 2 , 3 & 4 and verify Header output
    function verifyHeaderLevel23and4(){
        parent::doExpandAdvanceSection();
        $this->click("link=Heading");
        $this->click("link=Level 2");
        $this->click("link=Heading");
        $this->click("link=Level 3");
        $this->click("link=Heading");
        $this->click("link=Level 4");
        $this->type("wpTextbox1", "==Heading text==\n===Heading text===\n====Heading text====");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Heading text", $this->getText("//*[@id='wikiPreview']/h2"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
         try {
            $this->assertEquals("Heading text", $this->getText("//*[@id='wikiPreview']/h3"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("Heading text", $this->getText("//*[@id='wikiPreview']/h4"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add header level 2 , 3 , 4 & 5 and verify Header output
    function verifyHeaderLevel234and5(){
        parent::doExpandAdvanceSection();
        $this->click("link=Heading");
        $this->click("link=Level 2");
        $this->click("link=Heading");
        $this->click("link=Level 3");
        $this->click("link=Heading");
        $this->click("link=Level 4");
        $this->click("link=Heading");
        $this->click("link=Level 5");
        $this->type("wpTextbox1", "==Heading text==\n===Heading text===\n====Heading text====\n=====Heading text=====");
        $this->click("wpPreview");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertEquals("Heading text", $this->getText("//*[@id='wikiPreview']/h2"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
         try {
            $this->assertEquals("Heading text", $this->getText("//*[@id='wikiPreview']/h3"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("Heading text", $this->getText("//*[@id='wikiPreview']/h4"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("Heading text", $this->getText("//*[@id='wikiPreview']/h5"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
    } 
}
?>
