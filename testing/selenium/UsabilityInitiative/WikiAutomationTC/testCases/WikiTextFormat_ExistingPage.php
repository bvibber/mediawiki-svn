<?php
require_once 'WikiTextFormat_TC.php';

/**
 * Description of WikiNewExistingTextFormat
 *
 * @author bhagyag
 */
class WikiTextFormat_ExistingPage extends WikiTextFormat_TC {

    // Set up the testing environment
    function setup(){
        parent::setUp();
    }

    //Open a random page
    function openRandomPage(){
       parent::doOpenLink();
       parent::doLogin();
       parent::doAccessRandomPage();
       parent::doEditPage();
    }

    // Mark text Bold and verify output
     function testTextBold(){
       $this->openRandomPage();
       parent::verifyTextBold();
       parent::doLogout();
     }

     // Mark text Italic and verify output
    function testTextItalic(){
       $this->openRandomPage();
       parent::verifyTextItalic();
       parent::doLogout();
    }

       // Mark text Italic & Bold and verify output
    function testTextItalicandBold(){
       $this->openRandomPage();
       parent::verifyTextItalicandBold();
       parent::doLogout();
    }


     // Use Bullet Item function and verify output
    function testBulletItem(){
       $this->openRandomPage();
       parent::verifyBulletItem();
       parent::doLogout();
    }

    // Use Numbered Item function and verify output
    function testNumberedItem(){
       $this->openRandomPage();
       parent::verifyNumberedItem();
       parent::doLogout();
    }

    // Mark text as Nowiki and verify output
    function testNoWiki(){
       $this->openRandomPage();
       parent::verifyNoWiki();
       parent::doLogout();
    }

    // Create a line break and verify output
    function testLineBreak(){
       $this->openRandomPage();
       parent::verifyLineBreak();
       parent::doLogout();
    }

    // Mark text as Big and verify output
    function testTextBig(){
       $this->openRandomPage();
       parent::verifyTextBig();
       parent::doLogout();
    }

     // Mark text as Small and verify output
    function testTextSmall(){
       $this->openRandomPage();
       parent::verifyTextSmall();
       parent::doLogout();
    }

    // Mark text as Super Script and verify output
     function testTextSuperscript(){
       $this->openRandomPage();
       parent::verifyTextSuperscript();
       parent::doLogout();
     }

     // Mark text as Sub Script and verify output
     function testTextSubscript(){
       $this->openRandomPage();
       parent::verifyTextSubscript();
       parent::doLogout();
     }
}
?>
