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

     // Click on Embedded file function and verify the output
    function testEmbeddedFile(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doCreateNewPageTemporary();
       parent::verifyEmbeddedFile();
       parent::doLogout();
    }

    // Click on Picture Gallery function and verify the output
    function testPictureGallery(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doCreateNewPageTemporary();
       parent::verifyPictureGallery();
       parent::doLogout();
    }
}
?>
