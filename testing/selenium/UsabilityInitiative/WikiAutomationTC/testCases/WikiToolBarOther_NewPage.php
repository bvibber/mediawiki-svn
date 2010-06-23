<?php
require_once 'WikiToolBarOther_TC.php';
/**
 * Description of WikiNewPageOther
 *
 * @author bhagyag
 */
class WikiToolBarOther_NewPage extends WikiToolBarOther_TC {

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

     // Click on Embedded file function and verify the output
    function testEmbeddedFile(){
       $this->createNewPage();
       parent::verifyEmbeddedFile();
       parent::doLogout();
    }

    //Create a reference and verify the output
    function testReferenceLink(){
       $this->createNewPage();
       parent::verifyReference();
       parent::doLogout();
    }

    // Click on Picture Gallery function and verify the output
    function testPictureGallery(){
      $this->createNewPage();
       parent::verifyPictureGallery();
       parent::doLogout();
    }
}
?>
