<?php
/**
 * This will contain all the common values for the test suite.
 * Date : Apr - 2010
 * @author : BhagyaG - Calcey
 */
// Common variables

define ('WIKI_WEB_URL', "http://prototype.wikimedia.org/"); // Domain name
define ('WIKI_OPEN_PAGE',"/en.wikipedia.org/Main_Page"); // Main page URL

define ('WIKI_TEST_WAIT_TIME', "30000"); // Waiting time
define ('WIKI_USER_NAME', "bhagya_ca"); // User name
define ('WIKI_USER_PASSWORD', "test"); // Password
define ('WIKI_USER_DISPLAY_NAME', "Bhagya ca"); // Display name of the user
define ('WIKI_SEARCH_PAGE',"Hair (musical)"); // Page name to search
define ('WIKI_TEXT_SEARCH',"TV"); // Text to search
define ('WIKI_INTERNAL_LINK',"Daimler-Chrysler"); // Exisiting page name to add as an internal tag
define ('WIKI_EXTERNAL_LINK',"www.google.com"); // External web site name
define ('WIKI_EXTERNAL_LINK_TITLE',"Google"); // Page title of the external web site name
define ('WIKI_CODE_PATH',getcwd()); // get the current path of the program
define ('WIKI_SCREENSHOTS_PATH',"screenshots"); // the folder the error screen shots will be saved
define ('WIKI_SCREENSHOTS_TYPE',"png"); // screen print type
define ('WIKI_TEMP_NEWPAGE',"TestWikiPage"); // temporary creating new page name


// tool bar, buttons , links
// commonly using links
define ('LINK_MAIN_PAGE',"link=Main page");
define ('LINK_RANDOM_PAGE',"link=Random article");
define ('TEXT_PAGE_HEADING',"firstHeading");
define ('LINK_START',"link=");
define ('LINK_EDITPAGE',"//li[@id='ca-edit']/a/span");
define ('TEXT_EDITOR',"wpTextbox1");
define ('BUTTON_PREVIEW',"wpPreview");

// for WikiSearch_TC
define ('INPUT_SEARCH_BOX', "searchInput");
define ('BUTTON_SEARCH',"searchButton");
define ('TEXT_SEARCH_RESULT_HEADING'," - Search results - Wikipedia, the free encyclopedia");

// for WikiWatchUnWatch_TC
define ('LINK_WATCH_PAGE',"link=Watch");
define ('LINK_WATCH_LIST',"link=My watchlist");
define ('LINK_WATCH_EDIT',"link=View and edit watchlist");
define ('LINK_UNWATCH',"link=Unwatch");
define ('BUTTON_WATCH',"wpWatchthis");
define ('BUTTON_SAVE_WATCH',"wpSave");
define ('TEXT_WATCH',"Watch");
define ('TEXT_UNWATCH',"Unwatch");

// for WikiCommonFunction_TC
define ('TEXT_LOGOUT',"Log out");
define ('LINK_LOGOUT',"link=Log out");
define ('LINK_LOGIN',"link=Log in / create account");
define ('TEXT_LOGOUT_CONFIRM',"Log in / create account");
define ('INPUT_USER_NAME', "wpName1");
define ('INPUT_PASSWD', "wpPassword1");
define ('BUTTON_LOGIN',"wpLoginAttempt");
define ('TEXT_HEADING',"Heading");
define ('LINK_ADVANCED',"link=Advanced");

// for WikiDialogs_TC
define ('LINK_ADDLINK',"//div[@id='wikiEditor-ui-toolbar']/div[1]/div[2]/span[1]");
define ('TEXT_LINKNAME',"wikieditor-toolbar-link-int-target");
define ('TEXT_LINKDISPLAYNAME',"wikieditor-toolbar-link-int-text");
define ('TEXT_LINKDISPLAYNAME_APPENDTEXT'," Test");
define ('ICON_PAGEEXISTS',"wikieditor-toolbar-link-int-target-status-exists");
define ('ICON_PAGEEXTERNAL',"wikieditor-toolbar-link-int-target-status-external");
define ('OPT_INTERNAL',"wikieditor-toolbar-link-type-int");
define ('OPT_EXTERNAL',"wikieditor-toolbar-link-type-ext");
define ('BUTTON_INSERTLINK',"//div[10]/div[11]/button[1]");
define ('LINK_ADDTABLE',"//div[@id='wikiEditor-ui-toolbar']/div[3]/div[1]/div[4]/span[2]");
define ('CHK_HEADER',"wikieditor-toolbar-table-dimensions-header");
define ('CHK_BOARDER',"wikieditor-toolbar-table-wikitable");
define ('CHK_SORT',"wikieditor-toolbar-table-sortable");
define ('TEXT_ROW',"wikieditor-toolbar-table-dimensions-rows");
define ('TEXT_COL',"wikieditor-toolbar-table-dimensions-columns");
define ('BUTTON_INSERTABLE',"//div[3]/button[1]"); 
define ('TEXT_HEADTABLE_TEXT',"Header text");
define ('TEXT_TABLEID_WITHALLFEATURES', "//table[@id='sortable_table_id_0']/tbody/" );
define ('TEXT_TABLEID_OTHER', "//div[@id='wikiPreview']/table/tbody/" );
define ('TEXT_VALIDATE_TABLE_PART1', "tr[");
define ('TEXT_VALIDATE_TABLE_PART2',"]/td[");
define ('TEXT_VALIDATE_TABLE_PART3',"]");
define ('LINK_SEARCH',"//div[@id='wikiEditor-ui-toolbar']/div[3]/div[1]/div[5]/span");
define ('INPUT_SEARCH',"wikieditor-toolbar-replace-search");
define ('INPUT_REPLACE',"wikieditor-toolbar-replace-replace");
define ('BUTTON_REPLACEALL',"//button[3]");
define ('BUTTON_REPLACENEXT',"//button[2]");
define ('BUTTON_CANCEL',"//button[4]");
define ('TEXT_PREVIEW_TEXT1',"//div[@id='wikiPreview']/p[1]");
define ('TEXT_PREVIEW_TEXT2',"//div[@id='wikiPreview']/p[2]");
define ('TEXT_PREVIEW_TEXT3',"//div[@id='wikiPreview']/p[3]");
define ('TEXT_SAMPLE',"calcey qa\n\ncalcey qa\n\ncalcey qa");
define ('TEXT_SAMPLE_CASE',"calcey qa\n\nCalcey qa\n\nCalcey qa");
define ('TEXT_SAMPLE_REGEX',"testing Plan\n\ntest Plan\n\ntestQA Plan");
define ('TEXT_SEARCH',"calcey qa");
define ('TEXT_SEARCH_CASE',"Calcey qa");
define ('TEXT_SEARCH_REGEX',"test[^ ]*");
define ('TEXT_REPLACE',"test team") ;
define ('TEXT_REPLACE_REGEX',"QA") ;
define ('TEXT_REGEX_PREVIEW',"QA Plan") ;
define ('CHK_MATCHCASE',"wikieditor-toolbar-replace-case");
define ('CHK_REGEX',"wikieditor-toolbar-replace-regex");


// For WikiTextFormat_TC
define ('LINK_BOLD',"//*[@id='wikiEditor-ui-toolbar']/div[1]/div[1]/span[1]");
define ('TEXT_BOLD',"'''Bold''' text");
define ('TEXT_VALIDATE_BOLD',"Bold");
define ('TEXT_VALIDATE_BOLDTEXT',"//div[@id='wikiPreview']/p/b");
define ('LINK_ITALIC',"//*[@id='wikiEditor-ui-toolbar']/div[1]/div[1]/span[2]");
define ('TEXT_ITALIC',"''Italian'' text");
define ('TEXT_VALIDATE_ITALIC',"Italian");
define ('TEXT_VALIDATE_ITALICTEXT',"//div[@id='wikiPreview']/p/i");
define ('TEXT_ITALIC_BOLD',"Text '''''Italic & Bold'''''");
define ('TEXT_VALIDATE_ITALICBOLD',"Italic & Bold");
define ('TEXT_VALIDATE_ITALICBOLDTEXT',"//div[@id='wikiPreview']/p/i/b");
define ('LINK_BULLET',"//*[@id='wikiEditor-ui-toolbar']/div[3]/div[1]/div[2]/span[1]");
define ('TEXT_BULLET',"* Bulleted list item\n* Bulleted list item\n* Bulleted list item");
define ('TEXT_BULLET_TEXT',"Bulleted list item");
define ('TEXT_BULLET_1',"//div[@id='wikiPreview']/ul/li[1]");
define ('TEXT_BULLET_2',"//div[@id='wikiPreview']/ul/li[2]");
define ('TEXT_BULLET_3',"//div[@id='wikiPreview']/ul/li[3]");
define ('LINK_NUMBERED',"//*[@id='wikiEditor-ui-toolbar']/div[3]/div[1]/div[2]/span[2]");
define ('TEXT_NUMBERED',"# Numbered list item\n# Numbered list item\n# Numbered list item");
define ('TEXT_NUMBERED_TEXT',"Numbered list item");
define ('TEXT_NUMBERED_1',"//div[@id='wikiPreview']/ol/li[1]");
define ('TEXT_NUMBERED_2',"//div[@id='wikiPreview']/ol/li[2]");
define ('TEXT_NUMBERED_3',"//div[@id='wikiPreview']/ol/li[3]");
define ('TEXT_NOWIKI',"<nowiki>==Heading text==</nowiki>");
define ('TEXT_NOWIKI_TEXT',"==Heading text==");
define ('TEXT_NOWIKI_VALIDATE',"//div[@id='wikiPreview']/p");
define ('TEXT_LINEBREAK',"this is a test text to check the line\n break.");
define ('TEXT_LINEBREAK_TEXT',"this is a test text to check the line\n break.");
define ('TEXT_LINEBREAK_VALIDATE',"//div[@id='wikiPreview']/p");
define ('LINK_TEXTBIG',"//*[@id='wikiEditor-ui-toolbar']/div[3]/div[1]/div[3]/span[1]");
define ('TEXT_TEXTBIG',"<big>This</big> text");
define ('TEXT_TEXTBIG_TEXT',"This");
define ('TEXT_TEXTBIG_VALIDATE',"//div[@id='wikiPreview']/p/big");
define ('LINK_TEXTSMALL',"//*[@id='wikiEditor-ui-toolbar']/div[3]/div[1]/div[3]/span[2]");
define ('TEXT_TEXTSMALL',"<small>This</small> text\n");
define ('TEXT_TEXTSMALL_TEXT',"This");
define ('TEXT_TEXTSMALL_VALIDATE',"//div[@id='wikiPreview']/p/small");
define ('LINK_TEXTSUPER',"//*[@id='wikiEditor-ui-toolbar']/div[3]/div[1]/div[3]/span[3]");
define ('TEXT_TEXTSUPER',"<sup>This</sup> text\n");
define ('TEXT_TEXTSUPER_TEXT',"This");
define ('TEXT_TEXTSUPER_VALIDATE',"//div[@id='wikiPreview']/p/sup");
define ('LINK_TEXTSUB',"//*[@id='wikiEditor-ui-toolbar']/div[3]/div[1]/div[3]/span[4]");
define ('TEXT_TEXTSUB', "<sub>This</sub> text\n");
define ('TEXT_TEXTSUB_TEXT',"This");
define ('TEXT_TEXTSUB_VALIDATE',"//div[@id='wikiPreview']/p/sub");

// for WikiToolBarOther_TC

define ('LINK_TEXTEMBEDDED',"//*[@id='wikiEditor-ui-toolbar']/div[1]/div[2]/span[2]");
define ('TEXT_TEXTEMBEDDED',"[[File:Example.jpg]]");
define ('TEXT_TEXTEMBEDDED_VALIDATE',"//img[@alt='Example.jpg']");

define ('LINK_TEXTREFERENCE',"//*[@id='wikiEditor-ui-toolbar']/div[1]/div[2]/span[3]");
define ('INPUT_TEXTREFERENCE', "wikieditor-toolbar-reference-text");
define ('TEXT_TEXTREFERENCE',  "Test Reference");
define ('BUTTON_REFERENCE', "//button[@type='button']");
define ('TEXT_TEXTREFERENCE_EDITOR',"This is a text <ref>Test Reference</ref>\n\n\n{{reflist}}");
define ('TEXT_REFERENCEID',  "[1]");
define ('TEXT_REFERENCELINK',  "link=[1]");
define ('TEXT_REFERENCE',  "^ Test Reference");
define ('TEXT_PICTURELINK',  "//*[@id='wikiEditor-ui-toolbar']/div[3]/div[1]/div[4]/span[1]");
define ('IMAGE_EXAMPLE1',"//img[contains(@src,'http://upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Example.jpg/116px-Example.jpg')]");
define ('TEXT_IMG1CAPTION',"Caption1");
define ('TABLE_CAPTION1',"//div[@id='wikiPreview']/table.0.0");
define ('IMAGE_EXAMPLE2',"//div[@id='wikiPreview']/table/tbody/tr/td[2]/div/div[1]/div/a/img");
define ('TEXT_IMG2CAPTION',"Caption2");
define ('TABLE_CAPTION2',"//div[@id='wikiPreview']/table.0.1");


// for WikiNTOC_TC
define ('LINK_HEADER',"link=Heading");
define ('TEXT_HEADER',"Heading text");
define ('LINK_LEVEL2HEADER',"link=Level 2");
define ('TEXT_LEVEL2HEADER',"==Heading text==");
define ('TEXT_LEVEL2HEADER_SIZE',"//*[@id='wikiPreview']/h2");
define ('LINK_LEVEL3HEADER',"link=Level 3");
define ('TEXT_LEVEL3HEADER',"===Heading text===");
define ('TEXT_LEVEL3HEADER_SIZE',"//*[@id='wikiPreview']/h3");
define ('LINK_LEVEL4HEADER',"link=Level 4");
define ('TEXT_LEVEL4HEADER',"====Heading text====");
define ('TEXT_LEVEL4HEADER_SIZE',"//*[@id='wikiPreview']/h4");
define ('LINK_LEVEL5HEADER',"link=Level 5");
define ('TEXT_LEVEL5HEADER',"=====Heading text=====");
define ('TEXT_LEVEL5HEADER_SIZE',"//*[@id='wikiPreview']/h5");
?>
