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

     //Create a new page temporary
    function createNewPage(){
       parent::doOpenLink();
       parent::doLogin();
       parent::doCreateNewPageTemporary();
    }

    // Mark text Bold and verify output
     function testTextBold(){
       $this->createNewPage();
       parent::verifyTextBold();
       parent::doLogout();
     }

    // Mark text Italic and verify output
    function testTextItalic(){
       $this->createNewPage();
       parent::verifyTextItalic();
       parent::doLogout();
    }

      // Mark text Italic & Bold and verify output
    function testTextItalicandBold(){
       $this->createNewPage();
       parent::verifyTextItalicandBold();
       parent::doLogout();
    }

     // Use Bullet Item function and verify output
    function testBulletItem(){
       $this->createNewPage();
       parent::verifyBulletItem();
       parent::doLogout();
    }

    // Use Numbered Item function and verify output
    function testNumberedItem(){
       $this->createNewPage();
       parent::verifyNumberedItem();
       parent::doLogout();
    }

    // Mark text as Nowiki and verify output
    function testNoWiki(){
       $this->createNewPage();
       parent::verifyNoWiki();
       parent::doLogout();
    }

    // Create a line break and verify output
    function testLineBreak(){
       $this->createNewPage();
       parent::verifyLineBreak();
       parent::doLogout();
    }

    // Mark text as Big and verify output
    function testTextBig(){
       $this->createNewPage();
       parent::verifyTextBig();
       parent::doLogout();
    }

     // Mark text as Small and verify output
    function testTextSmall(){
       $this->createNewPage();
       parent::verifyTextSmall();
       parent::doLogout();
    }

    // Mark text as Super Script and verify output
     function testTextSuperscript(){
       $this->createNewPage();
       parent::verifyTextSuperscript();
       parent::doLogout();
     }

     // Mark text as Sub Script and verify output
     function testTextSubscript(){
       $this->createNewPage();
       parent::verifyTextSubscript();
       parent::doLogout();
     }
}
?>
