<?php
require_once 'WikiTextFormat_TC.php';
/**
 * Description of WikiNewPageTextFormat
 *
 * @author bhagyag
 */
class WikiTextFormat_NewPage extends WikiTextFormat_TC {

    // Set up the testing environment
    function setup(){
        parent::setUp();
    }

    // Mark text Bold and verify output
     function testTextBold(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doCreateNewPageTemporary();
       parent::verifyTextBold();
       parent::doLogout();
     }

    // Mark text Italic and verify output
    function testTextItalic(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doCreateNewPageTemporary();
       parent::verifyTextItalic();
       parent::doLogout();
    }

      // Mark text Italic & Bold and verify output
    function testTextItalicandBold(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doCreateNewPageTemporary();
       parent::verifyTextItalicandBold();
       parent::doLogout();
    }

     // Use Bullet Item function and verify output
    function testBulletItem(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doCreateNewPageTemporary();
       parent::verifyBulletItem();
       parent::doLogout();
    }

    // Use Numbered Item function and verify output
    function testNumberedItem(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doCreateNewPageTemporary();
       parent::verifyNumberedItem();
       parent::doLogout();
    }

    // Mark text as Nowiki and verify output
    function testNoWiki(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doCreateNewPageTemporary();
       parent::verifyNoWiki();
       parent::doLogout();
    }

    // Create a line break and verify output
    function testLineBreak(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doCreateNewPageTemporary();
       parent::verifyLineBreak();
       parent::doLogout();
    }

    // Mark text as Big and verify output
    function testTextBig(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doCreateNewPageTemporary();
       parent::verifyTextBig();
       parent::doLogout();
    }

     // Mark text as Small and verify output
    function testTextSmall(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doCreateNewPageTemporary();
       parent::verifyTextSmall();
       parent::doLogout();
    }

    // Mark text as Super Script and verify output
     function testTextSuperscript(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doCreateNewPageTemporary();
       parent::verifyTextSuperscript();
       parent::doLogout();
     }

     // Mark text as Sub Script and verify output
     function testTextSubscript(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doCreateNewPageTemporary();
       parent::verifyTextSubscript();
       parent::doLogout();
     }
}
?>
