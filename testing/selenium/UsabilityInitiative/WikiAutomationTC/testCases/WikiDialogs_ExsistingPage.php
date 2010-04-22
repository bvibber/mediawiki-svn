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

     // Add a internal link and verify
    function testInternalLink(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyInternalLink();
       parent::doLogout();
    }

     // Add a internal link with different display text and verify
    function testInternalLinkWithDisplayText(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyInternalLinkWithDisplayText();
       parent::doLogout();
    }

     // Add a internal link with blank display text and verify
    function testInternalLinkWithBlankDisplayText(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyInternalLinkWithBlankDisplayText();
       parent::doLogout();
    }

    // Add external link and verify
    function testExternalLink(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyExternalLink();
       parent::doLogout();
    }

     // Add external link with different display text and verify
    function testExternalLinkWithDisplayText(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyExternalLinkWithDisplayText();
       parent::doLogout();
    }

     // Add external link with Blank display text and verify
    function testExternalLinkWithBlankDisplayText(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyExternalLinkWithBlankDisplayText();
       parent::doLogout();
    }

    // Add a table and verify
    function testCreateTable(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyCreateTable();
       parent::doLogout();
    }

    // Add a table and verify only with head row
    function testCreateTableWithHeadRow(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyCreateTableWithHeadRow();
       parent::doLogout();
    }

    // Add a table and verify only with borders
    function testCreateTableWithBorders(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyCreateTableWithBorders();
       parent::doLogout();
    }

    // Add a table and verify only with sort row
    function testCreateTableWithSortRow(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyCreateTableWithSortRow();
       parent::doLogout();
    }

    // Add a table without headers,borders and sort rows
    function testCreateTableWithNoSpecialEffects(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyCreateTableWithNoSpecialEffects();
       parent::doLogout();
    }

     // Verify the replace all function on Search and Replace
     function testTextSearchReplaceAll(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyTextSearchReplaceAll();
       parent::doLogout();
     }

      // Verify the replace next function on Search and Replace
    function testTextSearchReplaceNext(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyTextSearchReplaceNext();
       parent::doLogout();
    }

    // When user click on find, text highlight on back which is not captured in Selenium directly. */
   /* function testTextSearchFindNext(){
       parent::doOpenLink();
       parent::doLogin();
       $this->open("/deployment-en/Main_Page");
       $this->waitForPageToLoad("30000");
       parent::doAccessRandomPage();
       parent::verifyTextSearchFindNext();
       parent::doLogout();
    }*/
}
?>
