<?
# This contains the strings for international/specialized wikis

# Misc stuff
$wikiMainPage = "Main Page" ; # This is actual the title of the article in the database
$wikiErrorPageTitle = "Yikes! An error!" ;
$wikiErrorMessage = "<h2>$1!</h2>Return to the [[:Main Page|Main Page]]!" ;

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
$wikiPrintLinksMarkup = "i" ; # will be used as <$wikiPrintLinksMarkup> and </$wikiPrintLinksMarkup>
$wikiAllowedNamespaces = array ( "wikipedia" , "talk" , "user" , "" , "wikipedia talk" , "user talk" ) ;

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
$wikiDontSaveChanges = "<i>Return without saving changes</i>" ;
$wikiDescribePage = "Describe the new page here." ;

# wikiUser
$wikiNoSuchUser = "<font color=red>Unknown user $1!</font>" ;
$wikiWrongPassword = "<font color=red>Wrong password for user $1!</font>" ;
$wikiYouAreLoggedIn = "$1, you are logged in!" ; 
$wikiUserError = "Error with \"$1\"" ;

# wikiPage
$wikiNoSuchSpecialPage = "<h1>No such special page \"$1\"!</h1>" ;
$wikiOldVersion = "<br><font size=-1>This is the old version #$1; see the <a href=\"$THESCRIPT?title=$2\">current version</a></font>" ;
$wikiRedirectFrom = "(redirected from <a href=\"$THESCRIPT?action=edit&title=$1\">$2</a>)" ;
$wikiRecentChanges = "Recent Changes" ;
$wikiRecentChangesLink = "RecentChanges" ; # "special:" is added automatically
$wikiSpecialPagesLink = "Special_pages" ; # "special:" is added automatically
$wikiEditThisPage = "Edit this page" ;
$wikiDeleteThisPage = "Delete this page" ;
$wikiUpload = "Upload files" ;
$wikiHistory = "History" ;
$wikiRandomPage = "Random Page" ;
$wikiSpecialPages = "Special Pages" ;
$wikiEditHelp = "<font size=+3>Editing <b>".$t."</b></font><br>Your changes will not be committed until you hit the <b>Save</b> button.<br>\nYou can get help <a href=\"$THESCRIPT?title=wikipedia:help/edit\">here</a>." ;
$wikiStatistics = "Statistics" ;
$wikiNewPages = "New pages" ;
$wikiOrphans = "Orphans" ;
$wikiMostWanted = "Most wanted" ;
$wikiAllPages = "All pages" ;
$wikiStubs = "Stub articles" ;
$wikiListUsers = "List users" ;
$wikiMyWatchlist = "My watchlist" ;
$wikiBeginDiff = "BEGIN DIFF" ;
$wikiEndDiff = "END DIFF" ;
$wikiDiffLegend = "<font color=#2AAA2A>Green text</font> was added or changed, <font color=#AAAA00>yellow text</font> was changed or deleted." ;
$wikiDiffFirstVersion = "This is the first version of this article. All text is new!<br>\n" ;
$wikiDiffImpossible = "This is the first version of this article. All text is new!<br>\n" ;
$wikiSearch = "Search" ;
$wikiCurrentServer = "http://wikipedia.sourceforge.net" ;
$wikiArticleSource = "$wikiCurrentServer/fpw/wiki.phtml?title=$1" ;
$wikiInterwiki = array ( "w"=>"http://www.wikipedia.com/wiki/$1" ) ;
$wikiOtherLanguages = array ( "de"=>"http://de.wikipedia.com/wiki/$1" ) ;
$wikiLanguageNames = array ( "de"=>"Deutsch" ) ;
$wikiRecentChangesText = "Track the most recent changes to Wikipedia on this page. [[Welcome, newcomers]]! Please have a look at these pages: [[Wikipedia FAQ]], [[Wikipedia policy]] (especially [[naming conventions]] and [[neutral point of view]]), and [[most common Wikipedia faux pas]].<br>If you want to see the Wikipedia succeed, it's very important that you don't add material restricted by others' [[copyright]]s. The legal liability could really hurt the project, so please don't do it.<br>See also the [http://meta.wikipedia.com/wiki.phtml?title=special:RecentChanges recent meta discussion]" ;



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

?>