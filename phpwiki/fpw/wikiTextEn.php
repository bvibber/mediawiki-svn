<?
# ATTENTION:
# To fit your local settings, PLEASE edit wikiSettings.php and wikiLocalSettings.php ONLY!
# Change settings here ONLY if they're to become global in all wikipedias!


# This contains the strings for international/specialized wikis
#---------------------------------------------------------------

# Basic character set and locale settings
$wikiCharset = "iso-8859-1" ;
$wikiUpperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ";
$wikiLowerChars = "abcdefghijklmnopqrstuvwxyzàáâãäåæçèéêëìíîïðñòóôõöøùúûüúþ";

# Misc stuff
$wikiMainPage = "Main Page" ; # This is actual the title of the article in the database
$wikiErrorPageTitle = "Yikes! An error!" ;
$wikiErrorMessage = "<h2>$1!</h2>Return to the [[:Main Page|Main Page]]!" ;
$wikiAllowedSpecialPages = array("userlogin","userlogout","newpages","recentchanges","upload","statistics","lonelypages","popularpages","wantedpages","allpages","randompage","shortpages","longpages","listusers","watchlist","special_pages","editusersettings","deletepage","protectpage","contributions","whatlinkshere");
$wikiRecentChangesText = "Track the most recent changes to Wikipedia on this page. [[Welcome, newcomers]]! Please have a look at these pages: [[Wikipedia FAQ]], [[Wikipedia policy]] (especially [[naming conventions]] and [[neutral point of view]]), and [[most common Wikipedia faux pas]].<br>If you want to see the Wikipedia succeed, it's very important that you don't add material restricted by others' [[copyright]]s. The legal liability could really hurt the project, so please don't do it.<br>See also the [http://meta.wikipedia.com/wiki.phtml?title=special:RecentChanges recent meta discussion]" ;
$wikiMetaDescription = "$1... Read more here at wikipedia, the free encyclopedia!" ;

# Used in header/footer
$wikiMainPageTitle = "Wikipedia : The free encyclopedia" ; # This is the title that is displayed on the main page
$wikiArticleSubtitle = "From Wikipedia, the free encyclopedia." ;
$wikiPrintable = "Printable version" ;
$wikiWatch = "Watch this article for me" ;
$wikiNoWatch = "Stop watching this article for me" ;
$wikiTitleTag = "$1: encyclopedia article from Wikipedia" ;
$wikiLogIn = "Log in" ;
$wikiLogOut = "Log out" ;
$wikiHelp = "Help" ;
$wikiHelpLink = "Help" ; # "special:" is added automatically
$wikiPreferences = "Preferences" ;
$wikiWhatLinksHere = "Pages that link here" ;
$wikiPrintLinksMarkup = "i" ; # will be used as <$wikiPrintLinksMarkup> and </$wikiPrintLinksMarkup>
#$wikiAllowedNamespaces = array ( "wikipedia" , "talk" , "user" , "" , "wikipedia talk" , "user talk" ) ;
$wikiTalk = "talk" ;
$wikiUser = "user" ;
$wikiNamespaceTalk = "$1 Talk" ;
$wikiAllowedNamespaces = array ( "wikipedia" , $wikiTalk , $wikiUser , "" , "wikipedia $wikiTalk" , "$wikiUser $wikiTalk" ) ;

# Editing
$wikiEditTitle = "Editing $1" ;
$wikiCannotEditPage = "<h3>You cannot edit this page!</h3>" ;
$wikiEditConflictMessage = "<h1>Edit conflict!</h1>\n<b>Someone saved this page after you started editing. The top textbox contains the saved text. Only the text in the top textbox will be saved.</b><br>\nScroll down to see your edited text.<br>\n" ;
$wikiPreviewAppend = "<hr>\n<h2>Preview :</h2>\n$1<hr><h3>Remember, this is only a preview and not yet saved!</h3>" ;
$wikiSummary = "Summary:" ;
$wikiMinorEdit = "This is a minor edit." ;
$wikiCopyrightNotice = "Please notice that all contributions to Wikipedia are considered to be released under the GNU Free Documentation License. If you don't want your writing to be edited mercilessly and redistributed at will, then don't hit submit. You are also promising us that you wrote this yourself, or copied it from a public domain resource. <b>DO NOT USE COPYRIGHTED WORK WITHOUT PERMISSION!</b>" ;
$wikiSave = "Save" ;
$wikiPreview = "Preview" ;
$wikiReset = "Reset" ;
$wikiDontSaveChanges = "<i>Cancel</i>" ;
$wikiDescribePage = "Describe the new page here." ;

# wikiUser
$wikiNoSuchUser = "<font color=red>Unknown user $1!</font>" ;
$wikiWrongPassword = "<font color=red>Wrong password for user $1!</font>" ;
$wikiYouAreLoggedIn = "$1, you are logged in!" ; 
$wikiUserError = "Error with \"$1\"" ;

# wikiPage
$wikiNoSuchSpecialPage = "<h1>No such special page \"$1\"!</h1>" ;
$wikiOldVersion = "<br><font size=\"-1\">This is the old version #$1; see the <a href=\"$THESCRIPT?title=$2\">current version</a></font>" ;
$wikiRedirectFrom = "(redirected from $1)" ;
$wikiRecentChanges = "Recent Changes" ;
$wikiRecentChangesLink = "RecentChanges" ; # "special:" is added automatically
$wikiSpecialPagesLink = "Special_pages" ; # "special:" is added automatically
$wikiEditThisPage = "Edit this page" ;
$wikiDeleteThisPage = "Delete this page" ;
$wikiUpload = "Upload files" ;
$wikiHistory = "History" ;
$wikiRandomPage = "Random Page" ;
$wikiSpecialPages = "Special Pages" ;
$wikiEditHelp = "<i>Editing Help</i>" ;
$wikiStatistics = "Statistics" ;
$wikiNewPages = "New pages" ;
$wikiOrphans = "Orphans" ;
$wikiMostWanted = "Most wanted" ;
$wikiAllPages = "All pages" ;
$wikiStubs = "Stub articles" ;
$wikiLongPages = "Long articles" ;
$wikiListUsers = "List users" ;
$wikiMyWatchlist = "My watchlist" ;
$wikiBeginDiff = "BEGIN DIFF" ;
$wikiEndDiff = "END DIFF" ;
$wikiDiffLegend = "<font color=#2AAA2A>Green text</font> was added or changed, <font color=#AAAA00>yellow text</font> was changed or deleted." ;
$wikiDiffFirstVersion = "This is the first version of this article. All text is new!<br>\n" ;
$wikiDiffImpossible = "This is the first version of this article. All text is new!<br>\n" ;
$wikiSearch = "Search" ;
$wikiOtherNamespaces = "<b>Other namespaces :</b> " ;
$wikiCategories = "<b>Categories :</b> " ;
$wikiThisCategory = "Articles in this category" ;
$wikiCounter = "This page has been accessed $1 times." ;

# Interwiki links
$wikiInterwiki = array ( "w"=>"http://www.wikipedia.com/wiki/$1" , "m"=>"http://meta.wikipedia.com/wiki.phtml?title=$1" ) ;

# Language interwiki links
$wikiOtherLanguages = array (
    "ar"=>"http://ar.wikipedia.com/wiki/$1" ,
    "ca"=>"http://ca.wikipedia.com/wiki/$1" ,
    "zh"=>"http://zh.wikipedia.com/wiki/$1" ,
    "dk"=>"http://dk.wikipedia.com/wiki/$1" ,
    "nl"=>"http://nl.wikipedia.com/wiki/$1" ,
    "de"=>"http://de.wikipedia.com/wiki/$1" ,
    "eo"=>"http://eo.wikipedia.com/wiki/$1" ,
    "fr"=>"http://fr.wikipedia.com/wiki/$1" ,
    "he"=>"http://he.wikipedia.com/wiki/$1" ,
    "hu"=>"http://hu.wikipedia.com/wiki/$1" ,
    "it"=>"http://it.wikipedia.com/wiki/$1" ,
    "ja"=>"http://ja.wikipedia.com/wiki/$1" ,
    "pl"=>"http://pl.wikipedia.com/wiki/$1" ,
    "pt"=>"http://pt.wikipedia.com/wiki/$1" ,
    "ru"=>"http://ru.wikipedia.com/wiki/$1" ,
    "simple"=>"http://simple.wikipedia.com/wiki/$1" ,
    "es"=>"http://es.wikipedia.com/wiki/$1" ,
    "sv"=>"http://sv.wikipedia.com/wiki/$1" ,
    "en"=>"http://www.wikipedia.com/wiki/$1"
    ) ;

# Language interwiki names
$wikiLanguageNames = array (
    "ar"=>"Arabic (Araby)" ,
    "ca"=>"Catalan (Catala)" ,
    "zh"=>"Chinese (Hanyu)" ,
    "dk"=>"Danish (Dansk)" ,
    "nl"=>"Dutch (Nederlands)" ,
    "de"=>"German (Deutsch)" ,
    "eo"=>"Esperanto" ,
    "fr"=>"French (Francais)" ,
    "he"=>"Hebrew (Ivrit)" ,
    "hu"=>"Hungarian (Magyar)" ,
    "it"=>"Italian (Italiano)" ,
    "ja"=>"Japanese (Nihongo)" ,
    "pl"=>"Polish (Polska)" ,
    "pt"=>"Portuguese (Portuguese)" ,
    "ru"=>"Russian (Russkiy)" ,
    "simple"=>"Simplified English" ,
    "es"=>"Spanish (Castellano)" ,
    "sv"=>"Swedish (Svenska)" ,
    "en"=>"English"
    ) ;
$wikiOtherLanguagesText = "Other languages : $1" ;

# date
$wikiDate = array () ;
$wikiDate["monday"] = "Monday" ;
$wikiDate["tuesday"] = "Tuesday" ;
$wikiDate["wednesday"] = "Wednesday" ;
$wikiDate["thursday"] = "Thursday" ;
$wikiDate["friday"] = "Friday" ;
$wikiDate["saturday"] = "Saturday" ;
$wikiDate["sunday"] = "Sunday" ;
$wikiDate["january"] = "January" ;
$wikiDate["february"] = "February" ;
$wikiDate["march"] = "March" ;
$wikiDate["april"] = "April" ;
$wikiDate["may"] = "May" ;
$wikiDate["june"] = "June" ;
$wikiDate["july"] = "July" ;
$wikiDate["august"] = "August" ;
$wikiDate["september"] = "September" ;
$wikiDate["october"] = "October" ;
$wikiDate["november"] = "November" ;
$wikiDate["december"] = "December" ;

#-----------------------------------------------------------------------
## strings for specialFunctions.php

# Log out / log in
$wikiGoodbye = "<h1>Goodbye, $1!</h1>" ;
$wikiWelcomeCreation = "<h1>Welcome, $1!</h1><font color=red>Don't forget to personalize your wikipedia preferences!</font><br>Your account has been created. Please press \"Log in\" once more to log in!" ; ;
$wikiLoginPageTitle = "User login" ;
$wikiYourName = "Your user name&nbsp; : " ;
$wikiYourPassword = "Your password&nbsp;&nbsp; : " ;
$wikiYourPasswordAgain = "Retype password : " ;
$wikiNewUsersOnly = " (new users only)" ;
$wikiRememberMyPassword = "Remember my password (as a cookie)." ;
$wikiLoginProblem = "<b>There has been a problem with your login.</b><br>Try again!" ;
$wikiAlreadyLoggedIn = "<font color=red><b>User $1, you are already logged in!</b></font><br>\n" ;
$wikiPleaseLogIn = "<h1>Please log in:</h1>\n" ;
$wikiAreYouNew = "If you are new to wikipedia and want to get a user account, enter a user name, type and re-type a password.<br>\n" ;
$wikiLogIn = "Log in" ;

# User preferences
$wikiUserSettings = "User Settings" ;
$wikiUserSettingsError = "You are not logged in! [[special:userLogin|Log in]] or go to the [[:Main Page|Main Page]]" ;
$wikiUserSettingsNewPasswordError = "<h1><font color=red>The new passwords didn't match. PASSWORD UNCHANGED!</font></h1>\n" ;
$wikiUserSettingsSaved = "<font color=red size=\"+1\">Your settings have been saved!</font>" ;
$wikiLoggedInAs = "<b>You are logged in as [[user:$1|$1]]. ";
$wikiID_Help = "Your internal ID is $1.</b> You can get help [[wikipedia:Help/User preferences|here]]." ;
$wikiQuickBarSettings = "QuickBar Settings :" ;
$wikiSettingsStandard = "standard" ;
$wikiSettingsNone = "None" ;
$wikiSettingsLeft = "Left" ;
$wikiSettingsRight = "Right" ;
$wikiOldPassword = "Old password&nbsp; &nbsp; : " ;
$wikiNewPassword = "New password&nbsp; &nbsp; : " ;
$wikiSkin = "Skin :" ;
$wikiStarTrek = "Star Trek" ;
$wikiShowHoverBox = "Show hoverbox over wiki links" ;
$wikiUnderlineLinks = "Underline links" ;
$wikiNewTopicsRed = "Show new topics in red" ;
$wikiJustifyParagraphs = "Justify paragraphs" ;
$wikiShowRecentChangesTable = "Show <i>Recent changes</i> as a table" ;
$wikiHideMinorEdits = "Hide minor edits in <i>Recent Changes</i>" ;
$wikiDoNumberHeadings = "Automatically number headings" ;
$wikiViewWithFrames = "View pages using frames <i>(experimental, Konqueror ONLY!)</i>" ;
$wikiTurnedOn = "yes" ;
$wikiTurnedOff = "no" ;
$wikiTextboxDimensions = "Textbox dimensions :" ;
$wikiCols = "Cols : " ;
$wikiRows = "Rows : " ;
$wikiYourEmail = "Your email : " ;
$wikiResultsPerPage = "Return up to $1 search results per page" ;
$wikiTimeDiff = "Time diff. : $1 hours" ;
$wikiViewRecentChanges = "See the last $1 changes on ''Recent Changes''" ;
$wikiOutputEncoding = "Output encoding : ";

# Search Page
$wikiSearchTitle = "Search" ;
$wikiSearchedVoid = "As you were searching for the void, you just found it." ;
$wikiNoSearchResult = "Sorry, we were unable to find an article matching the query \"$1\" in the title or body." ;
$wikiFoundHeading = "Wikipedia articles" ;
$wikiFoundText = "The query ''$2'' resulted in $1 articles. For each article, you can see its first paragraph and the next paragraph that contains one of the search words in the query." ;
# keywords used for boolean search operators
# note: these must consist of character, no symbols allowed
$and = "and";
$or = "or";
$not = "not";
# syntax errors for parser of boolean search queries
$srchSyntErr = array (
                    "SYNTAX ERROR: missing '$1'; inserted",
                    "SYNTAX ERROR: unexpected '$1'; ignored",
                    "SYNTAX ERROR: illegal symbol '$1'; ignored",
                    "SYNTAX ERROR: the word '$1' is too short, the index requires at least $2 characters",
                    "SYNTAX ERROR: missing search word; inserted"
               ) ;   
$wikiSearchError = "Sorry, your boolean search query contains the following errors: " ;

# Misc
$wikiLonelyPagesTitle = "The Orphans" ;
$wikiLonelyPagesText = "'''These articles exist, but no articles link to them!'''<br>''Talk: pages, empty pages and #REDIRECTs are '''not''' listed here.''\n\n" ;
$wikiAllPagesTitle = "All Pages Index" ;
$wikiAllPagesText = "'''These are all the articles in the database!'''\n\n" ;
$wikiUnsuccessfulSearch = "Unsuccessful search for $1" ;
$wikiUnsuccessfulSearches = "wikipedia:Unsuccessful searches ($1)" ; # $1 becomes "YEAR-MONTH"
$wikiUserlistTitle = "User List" ;
$wikiUserlistText = "'''These are all wikipedia users (that have an account)!'''" ;
$wikiRecentChangesTitle = "Recent Changes" ;
$wikiRecentChangesLastDays = "These are the last <b>$1</b> of the changes made on Wikipedia in the last <b>$2</b> days." ;
$wikiRecentChangesSince = "These are the last <b>$1</b> of the changes made on Wikipedia since <b>$2</b>." ;
$wikiViewLastDays = "View the last $1 days" ;
$wikiViewMaxNum = "View the last $1 changes" ;
$wikiListOnlyNewChanges = "List only new changes" ;
$wikiNewPagesTitle = "New pages" ;
$wikiNewPagesText = "These are the last <b>$1</b> new pages on Wikipedia in the last <b>$2</b> days." ;
$wikiRCLegend = " <b>Legend :</b> $1=Minor edit ; $2=New article." ;
$wikiDiff = "(diff)" ;
$wikiChange = "change" ;
$wikiChanges = "changes" ;
$wikiWatchYes = "\"$1\" was added to your [[special:WatchList|watch list]]." ;
$wikiWatchNo = "\"$1\" was removed from your [[special:WatchList|watch list]]." ;
$wikiWatchlistTitle = "Watch list" ;
$wikiWatchlistText = "'''Currently, you are watching the following articles :''' " ;

# Statistics
$wikiStatisticsTitle = "Article statistics" ;
$wikiStatTotalPages = "There are $1 pages in the database." ;
$wikiStatTalkPages = "There are $1 '''Talk''' pages." ;
$wikiStatCommaPages = "There are $1 pages with a comma that are ''not'' '''Talk''' pages." ;
$wikiStatWikipediaNoTalk = "There are $1 that have \"ikipedia\" in the title and are ''not'' '''Talk''' pages." ;
$wikiStatSubNoTalk = "There are $1 subpages that are ''not'' '''Talk''' pages." ;
$wikiStatNoTalk = "That means there are about $1 articles, including subpages (except '''Talk''')." ;
$wikiStatArticles = "Or, there are about $1 articles, not counting any subpages!" ;
$wikiStatJunk = "Finally, there are about $1 junk pages :-(" ;
$wikiStatOld = "And, there are $1 old page versions in the database, giving an average of $2 old pages on every active page." ;
$wikiUserStatistics = "User statistics" ;
$wikiStatUsers = "There are currently $1 [[special:ListUsers|users]] signed up." ;
$wikiStatSysops = "$1 of them have sysop status." ;

# Upload
$wikiUploadTitle = "Upload page" ;
$wikiUploadDenied = "You are neither an editor nor a sysop. Return to the <a href=\"$THESCRIPT?action=upload\">Upload page</a>" ;
$wikiUploadDeleted = "File <b>$1</b> deleted!" ;
$wikiUploadDelMsg1 = "*On $now, [[user:$1|$1]] deleted file '''$2'''\n" ;
$wikiUploadDelMsg2 = "Deletion of file $1" ;
$wikiUploadAffirm = "<nowiki>You need to affirm that the file is not violating copygights. Return to the <a href=\"$THESCRIPT?title=special:upload\">Upload page</a></nowiki>" ;
$wikiUploadFull = "Sorry, we are almost out of disk space. We can't let you upload any files right now." ;
$wikiUploadSuccess = "File <b>$1</b> was successfully uploaded!" ;
$wikiUploadSuccess1 = "*On $1, $2 uploaded file '''$3'''\n" ;
$wikiUploadSuccess2 = "Upload of file $1" ;
#$wikiUploadText = "<h2>Instructions:</h2><ul>\n" ;
$wikiUploadText .= "<li><strong>Use this form to upload various files</strong></li>\n";
$wikiUploadText .= "<li>To replace a previously-uploaded file (e.g., a\n";
$wikiUploadText .= "new version of the article), simply re-upload the\n";
$wikiUploadText .= "same file. But first look below and make sure you\n";
$wikiUploadText .= "haven't changed the name.</li>\n";
$wikiUploadText .= "<li><strong>Here's how to upload your file. </strong>Click\n";
$wikiUploadText .= "&quot;Browse...&quot; to your find the file you\n";
$wikiUploadText .= "want to upload on your hard drive. This will open\n";
$wikiUploadText .= "a &quot;Choose file&quot; dialogue window.</li>\n";
$wikiUploadText .= "<li>When you've found the file, click &quot;Open.&quot;\n";
$wikiUploadText .= "This will select the file and close the &quot;Choose\n";
$wikiUploadText .= "file&quot; dialogue window.</li>\n";
$wikiUploadText .= "<li>Don't forget to check the copyright statement!</li>\n";
$wikiUploadText .= "<li>Then click &quot;Upload.&quot; The file will start uploading. This may take some time, if it's\n";
$wikiUploadText .= "a big file and you have a slow Internet connection.</li>\n";
$wikiUploadText .= "<li>A message will tell you when the file has successfully uploaded.</li>\n";
$wikiUploadText .= "<li>You can upload as many files you like. Please don't try to crash our server, ha ha.</li>\n";
$wikiUploadText .= "<li>All uploads and deletions are logged in the <a href=\"$THESCRIPT?title=Log:Uploads\">uploads log</a>.</li>\n";
$wikiUploadText .= "</ul>\n";
$wikiUploadAffirmText = "I hereby affirm that this file is <b>not copyrighted</b>, or that I own the copyright for this file and place it under GFDL licence." ;
$wikiUploadButton = "Upload" ;
$wikiUploadPrev = "Previously-uploaded files:" ;
$wikiUploadSize = "Size (byte)" ;
$wikiFileRemoval = "File removal" ;
$wikiUploadRemove = "Click here to remove $1." ;

# Misc
$wikiHistoryTitle = "History of $1" ;
$wikiHistoryHeader = "This is the history of <a href=\"$THESCRIPT?title=$1\">$2</a>" ;
$wikiCurrentVersion = "current" ;
$wikiSpecialTitle = "Special Pages" ;
$wikiSpecialText = "<b>This is a list of special pages.</b> Some of them are only available if you are logged in. If you are logged in, you can have this list automatically displayed on the right or left of each page as a QuickBar.<br><br>" ;
$wikiStubTitle = "Short 'stub' articles" ;
$wikiLongPagesTitle = "The longest articles" ;
$wikiStubText = "'''These are all the articles in the database, sorted by length, shortest first.'''<br>''#REDIRECT pages and pages within a namespace (like Talk:) are '''not''' listed here!''\n\n" ;
$wikiLongPagesText = "'''These are all the articles in the database, sorted by length, longest first.'''<br>''#REDIRECT pages and pages within a namespace (like Talk:) are '''not''' listed here!''\n\n" ;
$wikiStubChars = "$1 chars" ;
$wikiAskSQL = "MySQL access" ;
$wikiAskSQLtext = "This function is available for sysops only.<br>''Please, do not press return/enter, '''always''' click on the ask button!''" ;
$wikiStubDelete = "<b>Delete this page!</b>" ;
$wikiStubLinkHere = "$1 articles link here." ;
$wikiStubShowLinks = "Toggle \"show links\" option" ;
$wikiShowLinks = "Show pages that link to $1" ;
$wikiDeleteTitle = "Deleting article '$1'" ;
$wikiDeleteDenied = "<font size=\"+3\">You are not allowed to delete this page!</font>" ;
$wikiDeleteSuccess = "'$1' has been removed." ;
$wikiDeleteMsg1 = "*On $1, [[user:$2|$2]] permanently deleted page '''$3'''\n" ;
$wikiDeleteMsg2 = "Permanent deletion of $1" ;
$wikiDeleteAsk = "You are about to delete the article \"$1\" and its complete history!<br>\nIf you are absolutely sure you want to do this, <a href=\"$2&iamsure=yes\">click here</a>." ;
$wikiProtectTitle = "Protecting article '$1'" ;
$wikiProtectDenied = "<font size=\"+3\">You are not allowed to protect this page!</font>" ;
$wikiProtectNow = "Page '$1' is now protected as $2." ;
$wikiProtectText = "<font size=\"+2\">You can now edit the protection for '$target'</font><br><i>For example, use \"is_sysop\" to prevent anyone but sysops from editing that page. Separate several allowances by \",\"</i>" ;
$wikiProtectCurrent = "Current protection : " ;
$wikiContribTitle = "Contributions of $1" ;
$wikiContribText = "<h1>$1's contributions :</h1>\n(With the exception of ''talk'' and ''log'' pages)" ;
$wikiContribDenied = "State a user name!" ;
$wikiLinkhereTitle = "Pages that link to $1" ;
$wikiLinkhereBacklink = "These articles are linked back from [[$1]]:" ;
$wikiLinkhereNoBacklink = "These articles are ''not'' linked back from [[$1]]:" ;
$wikiBacklinkNolink = "There are no articles that link to [[$1]]!" ;
$wikiBacklinkFollowing = "The following articles link to [[$1]]:" ;
$wikiWantedText = "'''These articles don't exist, but other articles link to them!''' (the top 50)<br>\n" ;
$wikiWantedToggleNumbers = "Click here to toggle the display of page titles starting with numbers (currently $1)" ;
$wikiWantedLine = "$1 is wanted by <b>$2</b> articles <nowiki>(see the <a href=\"$3\">pages that link to \"$4\"</a></nowiki>)" ;
$wikiLastChange = "Last edited $1" ;
$wikiPopularPages = "Most popular" ;
$wikiRefreshThisPage = "Refresh this page" ;
$wikiResourcesWarning = "(Please, do this only when necessary, as this function uses a high amount of system resources)" ;
$wikiNoRefresh = "(The page was last refreshed just $1 minutes ago; please wait another $2 minutes and try again.)" ;
$wikiLastRefreshed = "Last refreshed $1" ;
$wikiValidate = "Validate this page" ;

#---------------------------
#Functions
function wikiGetDateEn ( $x ) { # Used in RecentChangesLayout in special_functions.php
    global $wikiDate ;
    $dayName = $wikiDate [ strtolower ( date ( "l" , $x ) ) ];
    $monthName = $wikiDate [ strtolower ( date ( "F" , $x ) ) ];
    $dayNumber = date ( "j" , $x ) ;
    $year = date ( "Y" , $x ) ;
    return "$dayName, $monthName $dayNumber, $year" ;
    }
$wikiGetDate = wikiGetDateEn;

# In theory, this could be expanded to allow general conversion of the
# character encoding used in the database to another encoding optionally
# used on the browser end.

# Define these arrays if you need to set up conversion.
# $wikiEncodingCharsets = array("iso-8859-1");
# $wikiEncodingNames = array("Latin-1"); # Localised names

function wikiRecodeOutputEn($text) {
  # Stub
  # global $user;  # $user->options["encoding"] is an index into the above arrays
  return $text;
}

function wikiRecodeInputEn($text) {
  # Stub
  return $text;
}

$wikiRecodeOutput = wikiRecodeOutputEn;
$wikiRecodeInput = wikiRecodeInputEn;
?>
