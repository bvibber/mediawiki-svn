<?php
require_once 'WikiDialogs_TC.php';
/**
 * Description of WikiNewPageDialogs
 *
 * @author bhagyag
 */
class WikiDialogs_NewPage extends WikiDialogs_TC {
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

     // Add a internal link and verify
    function testInternalLink(){
       $this->createNewPage();
       parent::verifyInternalLink();
       parent::doLogout();
    }

     // Add a internal link with different display text and verify
    function testInternalLinkWithDisplayText(){
       $this->createNewPage();
       parent::verifyInternalLinkWithDisplayText();
       parent::doLogout();
    }

     // Add a internal link with blank display text and verify
    function testInternalLinkWithBlankDisplayText(){
       $this->createNewPage();
       parent::verifyInternalLinkWithBlankDisplayText();
       parent::doLogout();
    }

    // Add external link and verify
    function testExternalLink(){
       $this->createNewPage();
       parent::verifyExternalLink();
       parent::doLogout();
    }

     // Add external link with different display text and verify
    function testExternalLinkWithDisplayText(){
       $this->createNewPage();
       parent::verifyExternalLinkWithDisplayText();
       parent::doLogout();
    }

     // Add external link with Blank display text and verify
    function testExternalLinkWithBlankDisplayText(){
       $this->createNewPage();
       parent::verifyExternalLinkWithBlankDisplayText();
       parent::doLogout();
    }

   // Add a table and verify
    function testCreateTable(){
       $this->createNewPage();
       parent::verifyCreateTable();
       parent::doLogout();
    }

    // Add a table and verify only with head row
    function testCreateTableWithHeadRow(){
       $this->createNewPage();;
       parent::verifyCreateTableWithHeadRow();
       parent::doLogout();
    }

    // Add a table and verify only with borders
    function testCreateTableWithBorders(){
       $this->createNewPage();
       parent::verifyCreateTableWithBorders();
       parent::doLogout();
    }

    // Add a table and verify only with sort row
    function testCreateTableWithSortRow(){
       $this->createNewPage();
       parent::verifyCreateTableWithSortRow();
       parent::doLogout();
    }

    // Add a table without headers,borders and sort rows
    function testCreateTableWithNoSpecialEffects(){
       $this->createNewPage();
       parent::verifyCreateTableWithNoSpecialEffects();
       parent::doLogout();
    }

     // Add a table with headers,borders and sort rows
    function testCreateTableWithAllSpecialEffects(){
        $this->createNewPage();
       parent::verifyCreateTableWithAllSpecialEffects();
       parent::doLogout();
    }

     // Verify the replace all function on Search and Replace
     function testTextSearchReplaceAll(){
       $this->createNewPage();
       parent::verifyTextSearchReplaceAll();
       parent::doLogout();
     }

      // Verify the replace next function on Search and Replace
    function testTextSearchReplaceNext(){
       $this->createNewPage();
       parent::verifyTextSearchReplaceNext();
       parent::doLogout();
    } 

    /*// When user click on find, text highlight on back which is not captured in Selenium directly.
    function testTextSearchFindNext(){
       $this->createNewPage();
       parent::verifyTextSearchFindNext();
       parent::doLogout();
    }*/

    // Verify the Match Case option with Replace All on Search and Replace
    function testTextMatchCase(){
       $this->createNewPage();
       parent::verifyTextMatchCase();
       parent::doLogout();
    }

     //Verify Regular expression option with Replace All on Search and Replace
    function testRegEx(){
       $this->createNewPage();
       parent::verifyRegEx();
       parent::doLogout();
    }

}
?>
