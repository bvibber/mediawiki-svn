<?php
require_once 'WikiToolBarOther_TC.php';

/**
 * Description of WikiExistingPageOther
 *
 * @author bhagyag
 */
class WikiToolBarOther_ExistingPage  extends WikiToolBarOther_TC {
    
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

     // Click on Embedded file function and verify the output
    function testEmbeddedFile(){
       $this->openRandomPage();
       parent::verifyEmbeddedFile();
       parent::doLogout();
    }

    //Create a reference and verify the output
    function testReferenceLink(){
       $this->openRandomPage();
       parent::verifyReference();
       parent::doLogout();
    }


    // Click on Picture Gallery function and verify the output
    function testPictureGallery(){
       $this->openRandomPage();
       parent::verifyPictureGallery();
       parent::doLogout();
    }
}
?>
