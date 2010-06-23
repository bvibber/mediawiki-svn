<?php
require_once 'WikiCommonFunction_TC.php';
include 'Config.php';
/**
 * This test case will be handling the Wiki Tool bar Dialog functions
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
class WikiDialogs_TC extends WikiCommonFunction_TC {

    // Add a internal link and verify
    function verifyInternalLink(){
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_ADDLINK);
        $this->type(TEXT_LINKNAME, (WIKI_INTERNAL_LINK));
        $this->assertTrue($this->isElementPresent(ICON_PAGEEXISTS));
        $this->assertEquals("on", $this->getValue(OPT_INTERNAL));
        $this->click(BUTTON_INSERTLINK); 
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals((WIKI_INTERNAL_LINK), $this->getText(LINK_START . WIKI_INTERNAL_LINK));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click(LINK_START. WIKI_INTERNAL_LINK);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertTrue($this->isTextPresent(WIKI_INTERNAL_LINK), $this->getText(TEXT_PAGE_HEADING));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a internal link with different display text and verify
    function verifyInternalLinkWithDisplayText(){
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_ADDLINK);
        $this->type(TEXT_LINKNAME, (WIKI_INTERNAL_LINK));
        $this->type(TEXT_LINKDISPLAYNAME, (WIKI_INTERNAL_LINK) . TEXT_LINKDISPLAYNAME_APPENDTEXT);
        $this->assertTrue($this->isElementPresent(ICON_PAGEEXISTS));
        $this->assertEquals("on", $this->getValue(OPT_INTERNAL));
        $this->click(BUTTON_INSERTLINK);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertEquals((WIKI_INTERNAL_LINK).TEXT_LINKDISPLAYNAME_APPENDTEXT, $this->getText(LINK_START .(WIKI_INTERNAL_LINK) .TEXT_LINKDISPLAYNAME_APPENDTEXT));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click(LINK_START .(WIKI_INTERNAL_LINK).TEXT_LINKDISPLAYNAME_APPENDTEXT);
        $this->waitForPageToLoad(WIKI_TEST_WAIT_TIME);
        try {
            $this->assertTrue($this->isTextPresent((WIKI_INTERNAL_LINK)), $this->getText(TEXT_PAGE_HEADING));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a internal link with blank display text and verify
    function verifyInternalLinkWithBlankDisplayText(){
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_ADDLINK);
        $this->type(TEXT_LINKNAME, (WIKI_INTERNAL_LINK));
        $this->type(TEXT_LINKDISPLAYNAME, "");
        $this->assertTrue($this->isElementPresent(ICON_PAGEEXISTS));
        $this->assertEquals("on", $this->getValue(OPT_INTERNAL));
        $this->click(BUTTON_INSERTLINK);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals((WIKI_INTERNAL_LINK), $this->getText(LINK_START.(WIKI_INTERNAL_LINK)));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click(LINK_START.(WIKI_INTERNAL_LINK));
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals((WIKI_INTERNAL_LINK), $this->getText(TEXT_PAGE_HEADING));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add external link and verify
    function verifyExternalLink(){
        $this->type(BUTTON_PREVIEW, "");
        $this->click(LINK_ADDLINK);
        $this->type(TEXT_LINKNAME, WIKI_EXTERNAL_LINK);
        try {
        $this->assertTrue($this->isElementPresent(ICON_PAGEEXTERNAL));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->assertEquals("on", $this->getValue(OPT_EXTERNAL));
        $this->click(BUTTON_INSERTLINK);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
         try {
            $this->assertEquals((WIKI_EXTERNAL_LINK), $this->getText(LINK_START.(WIKI_EXTERNAL_LINK)));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click(LINK_START.(WIKI_EXTERNAL_LINK));
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals((WIKI_EXTERNAL_LINK_TITLE), $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add external link with different display text and verify
    function verifyExternalLinkWithDisplayText(){
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_ADDLINK);
        $this->type(TEXT_LINKNAME, (WIKI_EXTERNAL_LINK));
        $this->type(TEXT_LINKDISPLAYNAME, (WIKI_EXTERNAL_LINK_TITLE));
        try {
        $this->assertTrue($this->isElementPresent(ICON_PAGEEXTERNAL));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->assertEquals("on", $this->getValue(OPT_EXTERNAL));
        $this->click(BUTTON_INSERTLINK);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals((WIKI_EXTERNAL_LINK_TITLE), $this->getText(LINK_START.(WIKI_EXTERNAL_LINK_TITLE)));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click(LINK_START.(WIKI_EXTERNAL_LINK_TITLE));
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals((WIKI_EXTERNAL_LINK_TITLE), $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add external link with Blank display text and verify
    function verifyExternalLinkWithBlankDisplayText(){
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_ADDLINK);
        $this->type(TEXT_LINKNAME, (WIKI_EXTERNAL_LINK));
        $this->type(TEXT_LINKDISPLAYNAME, "");
        try {
        $this->assertTrue($this->isElementPresent(ICON_PAGEEXTERNAL));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->assertEquals("on", $this->getValue(OPT_EXTERNAL));
        $this->click(BUTTON_INSERTLINK);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
        $this->assertEquals("[1]", $this->getText(LINK_START ."[1]"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        $this->click(LINK_START . "[1]");
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals((WIKI_EXTERNAL_LINK_TITLE), $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a table and verify
    function verifyCreateTable(){
        $WIKI_TABLE_ROW = 2;
        $WIKI_TABLE_COL = "5";
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_ADDTABLE);
        $this->click(CHK_SORT);
        $this->type(TEXT_ROW, $WIKI_TABLE_ROW);
        $this->type(TEXT_COL, $WIKI_TABLE_COL);
        $this->click(BUTTON_INSERTABLE);
        $this->click(CHK_SORT);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $WIKI_TABLE_ROW = $WIKI_TABLE_ROW+1;
            $this->assertTrue($this->isElementPresent(TEXT_TABLEID_OTHER .
                    TEXT_VALIDATE_TABLE_PART1 . $WIKI_TABLE_ROW .
                    TEXT_VALIDATE_TABLE_PART2 .  $WIKI_TABLE_COL .
                    TEXT_VALIDATE_TABLE_PART3 ));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a table and verify only with head row
    function verifyCreateTableWithHeadRow(){
        $WIKI_TABLE_ROW = 3;
        $WIKI_TABLE_COL = "4";
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_ADDTABLE);
        $this->click(CHK_BOARDER);
        $this->type(TEXT_ROW, $WIKI_TABLE_ROW);
        $this->type(TEXT_COL, $WIKI_TABLE_COL);
        $this->click(BUTTON_INSERTABLE);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $WIKI_TABLE_ROW = $WIKI_TABLE_ROW+1;
            $this->assertTrue($this->isElementPresent(TEXT_TABLEID_OTHER .
                    TEXT_VALIDATE_TABLE_PART1 . $WIKI_TABLE_ROW .
                    TEXT_VALIDATE_TABLE_PART2 . $WIKI_TABLE_COL .
                    TEXT_VALIDATE_TABLE_PART3));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a table and verify only with borders
    function verifyCreateTableWithBorders(){
        $WIKI_TABLE_ROW = "4";
        $WIKI_TABLE_COL = "6";
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_ADDTABLE);
        $this->click(CHK_HEADER);
        $this->type(TEXT_ROW,$WIKI_TABLE_ROW);
        $this->type(TEXT_COL,$WIKI_TABLE_COL);
        $this->click(BUTTON_INSERTABLE);
        $this->click(CHK_HEADER);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertTrue($this->isElementPresent(TEXT_TABLEID_OTHER . 
                    TEXT_VALIDATE_TABLE_PART1 . $WIKI_TABLE_ROW .
                    TEXT_VALIDATE_TABLE_PART2 . $WIKI_TABLE_COL .
                    TEXT_VALIDATE_TABLE_PART3));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a table and verify only with sort row
    function verifyCreateTableWithSortRow(){
        $WIKI_TABLE_ROW = "2";
        $WIKI_TABLE_COL = "5";
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_ADDTABLE);
        $this->click(CHK_HEADER);
        $this->click(CHK_BOARDER);
        $this->click(CHK_SORT);
        $this->type(TEXT_ROW, $WIKI_TABLE_ROW);
        $this->type(TEXT_COL, $WIKI_TABLE_COL);
        $this->click(BUTTON_INSERTABLE);
        $this->click(CHK_HEADER);
        $this->click(CHK_BOARDER);
        $this->click(CHK_SORT);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertTrue($this->isElementPresent(TEXT_TABLEID_WITHALLFEATURES . 
                    TEXT_VALIDATE_TABLE_PART1 . $WIKI_TABLE_ROW .
                    TEXT_VALIDATE_TABLE_PART2 . $WIKI_TABLE_COL .
                    TEXT_VALIDATE_TABLE_PART3));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Add a table without headers,borders and sort rows
    function verifyCreateTableWithNoSpecialEffects(){
        $WIKI_TABLE_ROW = "6";
        $WIKI_TABLE_COL = "2";
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_ADDTABLE);
        $this->click(CHK_BOARDER);
        $this->click(CHK_HEADER);
        $this->type(TEXT_ROW, $WIKI_TABLE_ROW);
        $this->type(TEXT_COL, $WIKI_TABLE_COL);
        $this->click(BUTTON_INSERTABLE);
        $this->click(CHK_BOARDER);
        $this->click(CHK_HEADER);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertTrue($this->isElementPresent(TEXT_TABLEID_OTHER . 
                    TEXT_VALIDATE_TABLE_PART1 . $WIKI_TABLE_ROW .
                    TEXT_VALIDATE_TABLE_PART2 . $WIKI_TABLE_COL .
                    TEXT_VALIDATE_TABLE_PART3));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

     // Add a table with headers,borders and sort rows
    function verifyCreateTableWithAllSpecialEffects(){
        $WIKI_TABLE_ROW = 6;
        $WIKI_TABLE_COL = "2";
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_ADDTABLE);
        $this->click(CHK_SORT);
        $this->type(TEXT_ROW, $WIKI_TABLE_ROW);
        $this->type(TEXT_COL, $WIKI_TABLE_COL);
        $this->click(BUTTON_INSERTABLE);
        $this->click(CHK_SORT);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $WIKI_TABLE_ROW = $WIKI_TABLE_ROW+1;
            $this->assertTrue($this->isElementPresent(TEXT_TABLEID_WITHALLFEATURES . 
                    TEXT_VALIDATE_TABLE_PART1 . $WIKI_TABLE_ROW .
                    TEXT_VALIDATE_TABLE_PART2 . $WIKI_TABLE_COL .
                    TEXT_VALIDATE_TABLE_PART3));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

     // Verify the replace all function on Search and Replace
     function verifyTextSearchReplaceAll(){
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_SEARCH);
        $this->type(TEXT_EDITOR, (TEXT_SAMPLE));
        $this->type(INPUT_SEARCH, (TEXT_SEARCH));
        $this->type(INPUT_REPLACE, (TEXT_REPLACE));
        $this->click(BUTTON_REPLACEALL);
        $this->click(BUTTON_CANCEL);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals((TEXT_REPLACE), $this->getText(TEXT_PREVIEW_TEXT1));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals((TEXT_REPLACE), $this->getText(TEXT_PREVIEW_TEXT2));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals((TEXT_REPLACE), $this->getText(TEXT_PREVIEW_TEXT3));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    // Verify the replace next function on Search and Replace
    function verifyTextSearchReplaceNext(){
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_SEARCH);
        $this->type(TEXT_EDITOR, (TEXT_SAMPLE));
        $this->type(INPUT_SEARCH, (TEXT_SEARCH));
        $this->type(INPUT_REPLACE, (TEXT_REPLACE));
        $this->click(BUTTON_REPLACENEXT);
        $this->click(BUTTON_REPLACENEXT);
        $this->click(BUTTON_CANCEL);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals((TEXT_REPLACE), $this->getText(TEXT_PREVIEW_TEXT1));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals((TEXT_REPLACE), $this->getText(TEXT_PREVIEW_TEXT2));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals((TEXT_SEARCH), $this->getText(TEXT_PREVIEW_TEXT3));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

    /*/Verify the find next function on Search and Replace
    // When user click on find, text highlight on back which is not captured in Selenium directly.
    function verifyTextSearchFindNext(){
        parent::doExpandAdvanceSection();
        $this->type("wpTextbox1", "");
        $this->type("wpTextbox1", "test qa\ntest qa\ntest qa");
        $this->click("link=Search and replace");
        $this->type("wikieditor-toolbar-replace-search", "Test");

    }*/
    

    // Verify Match Case option with Replace All on Search and Replace
    function verifyTextMatchCase(){
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_SEARCH);
        $this->type(TEXT_EDITOR, (TEXT_SAMPLE_CASE));
        $this->type(INPUT_SEARCH, (TEXT_SEARCH_CASE));
        $this->type(INPUT_REPLACE, (TEXT_REPLACE));
        $this->click(CHK_MATCHCASE);
        $this->click(BUTTON_REPLACEALL);
        $this->click(BUTTON_CANCEL);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals((TEXT_SEARCH), $this->getText(TEXT_PREVIEW_TEXT1));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals((TEXT_REPLACE), $this->getText(TEXT_PREVIEW_TEXT2));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals((TEXT_REPLACE), $this->getText(TEXT_PREVIEW_TEXT3));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }

    }

     // Verify Regular expression option with Replace All on Search and Replace
    function verifyRegEx(){
        parent::doExpandAdvanceSection();
        $this->type(TEXT_EDITOR, "");
        $this->click(LINK_SEARCH);
        $this->type(TEXT_EDITOR, (TEXT_SAMPLE_REGEX));
        $this->type(INPUT_SEARCH, (TEXT_SEARCH_REGEX));
        $this->type(INPUT_REPLACE, (TEXT_REPLACE_REGEX));
        $this->click(CHK_REGEX);
        $this->click(BUTTON_REPLACEALL);
        $this->click(BUTTON_CANCEL);
        $this->click(BUTTON_PREVIEW);
        $this->waitForPageToLoad((WIKI_TEST_WAIT_TIME));
        try {
            $this->assertEquals((TEXT_REGEX_PREVIEW), $this->getText(TEXT_PREVIEW_TEXT1));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals((TEXT_REGEX_PREVIEW), $this->getText(TEXT_PREVIEW_TEXT2));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals((TEXT_REGEX_PREVIEW), $this->getText(TEXT_PREVIEW_TEXT3));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            parent::doCreateScreenShot(__FUNCTION__);
            array_push($this->verificationErrors, $e->toString());
        }
    }

}
?>