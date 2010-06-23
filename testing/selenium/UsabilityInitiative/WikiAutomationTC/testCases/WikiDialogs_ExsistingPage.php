<?php
require_once 'WikiDialogs_TC.php';
/**
 * Description of WikiExsistingPageDialogs
 *
 * @author bhagyag
 */
class WikiDialogs_ExsistingPage  extends WikiDialogs_TC  {
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
     // Add a internal link and verify
    function testInternalLink(){
       $this->openRandomPage();
       parent::verifyInternalLink();
       parent::doLogout();
    }

    
    // Add a internal link with different display text and verify
    function testInternalLinkWithDisplayText(){
       $this->openRandomPage();
       parent::verifyInternalLinkWithDisplayText();
       parent::doLogout();
    }

     // Add a internal link with blank display text and verify
    function testInternalLinkWithBlankDisplayText(){
       $this->openRandomPage();
       parent::verifyInternalLinkWithBlankDisplayText();
       parent::doLogout();
    }

    // Add external link and verify
    function testExternalLink(){
       $this->openRandomPage();
       parent::verifyExternalLink();
       parent::doLogout();
    }

     // Add external link with different display text and verify
    function testExternalLinkWithDisplayText(){
       $this->openRandomPage();
       parent::verifyExternalLinkWithDisplayText();
       parent::doLogout();
    }

     // Add external link with Blank display text and verify
    function testExternalLinkWithBlankDisplayText(){
       $this->openRandomPage();
       parent::verifyExternalLinkWithBlankDisplayText();
       parent::doLogout();
    }

    // Add a table and verify
    function testCreateTable(){
       $this->openRandomPage();
       parent::verifyCreateTable();
       parent::doLogout();
    }

    // Add a table and verify only with head row
    function testCreateTableWithHeadRow(){
       $this->openRandomPage();
       parent::verifyCreateTableWithHeadRow();
       parent::doLogout();
    }

    // Add a table and verify only with borders
    function testCreateTableWithBorders(){
       $this->openRandomPage();
       parent::verifyCreateTableWithBorders();
       parent::doLogout();
    }

    // Add a table and verify only with sort row
    function testCreateTableWithSortRow(){
       $this->openRandomPage();
       parent::verifyCreateTableWithSortRow();
       parent::doLogout();
    }

    // Add a table without headers,borders and sort rows
    function testCreateTableWithNoSpecialEffects(){
       $this->openRandomPage();
       parent::verifyCreateTableWithNoSpecialEffects();
       parent::doLogout();
    }

    // Add a table with headers,borders and sort rows
    function testCreateTableWithAllSpecialEffects(){
       $this->openRandomPage();
       parent::verifyCreateTableWithAllSpecialEffects();
       parent::doLogout();
    }
     // Verify the replace all function on Search and Replace
     function testTextSearchReplaceAll(){
       $this->openRandomPage();
       parent::verifyTextSearchReplaceAll();
       parent::doLogout();
     }

      // Verify the replace next function on Search and Replace
    function testTextSearchReplaceNext(){
       $this->openRandomPage();
       parent::verifyTextSearchReplaceNext();
       parent::doLogout();
    }

    // When user click on find, text highlight on back which is not captured in Selenium directly. 
    function testTextSearchFindNext(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyTextSearchFindNext();
       parent::doLogout();
    }

     // Verify the Match Case option with Replace All on Search and Replace
    function testTextMatchCase(){
       $this->openRandomPage();
       parent::verifyTextMatchCase();
       parent::doLogout();
    }

     //Verify Regular expression option with Replace All on Search and Replace
    function testRegEx(){
       $this->openRandomPage();
       parent::verifyRegEx();
       parent::doLogout();
    } 
}
?>
