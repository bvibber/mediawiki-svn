<?
# ATTENTION:
# To fit your local settings, PLEASE edit wikiSettings.php and wikiLocalSettings.php ONLY!
# Change settings here ONLY if they're to become global in all wikipedias!

$wikiLanguage = "de" ;
include_once ( "utf8Case.php" ) ;

# This contains the strings for international/specialized wikis
#---------------------------------------------------------------

# Basic character set and locale settings
$wikiCharset = "iso-8859-1" ;
$wikiUpperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞÄÖÜ";
$wikiLowerChars = "abcdefghijklmnopqrstuvwxyzàáâãäåæçèéêëìíîïðñòóôõöøùúûüúþäöü";

# Misc stuff
$wikiMainPage = "Hauptseite" ; # This is actual the title of the article in the database
$wikiErrorPageTitle = "Verflixt! Ein Fehler!" ;
$wikiErrorMessage = "<h2>$1!</h2>Zurück zur [[:Hauptseite|Hauptseite]]!" ;
$wikiAllowedSpecialPages = array("userlogin","userlogout","newpages","recentchanges","upload","statistics","lonelypages","popularpages","wantedpages","allpages","randompage","shortpages","longpages","listusers","watchlist","special_pages","editusersettings","deletepage","movepage","protectpage","contributions","whatlinkshere","recentchangeslinked","sqldump","vote");
$wikiRecentChangesText = "Diese Seite wird beim Laden automatisch aktualisiert. Angezeigt werden Seiten, die zuletzt bearbeitet bzw. geändert wurden, sowie die Zeit und der Name des Autors. Falls Du neu hier bist, dann schau Dir bitte die [[Wikipedia Willkommen|Willkommensseite]] der Wikipedia an, dort bekommst Du eine erste Einführung. Falls Du willst, dass Wikipedia zu einem Erfolg wird, dann füge bitte keine Texte hinzu, die dem Urheberrecht anderer unterliegen. Dies könnte dem Projekt sonst schweren Schaden zufügen." ;
$wikiMetaDescription = "$1... Lesen Sie mehr hier auf Wikipedia, der freien Enzyklopädie!" ;

# Used in header/footer
$wikiWikipediaHelp = "wikipedia:Hilfe" ;
$wikiMainPageTitle = "Wikipedia : Die freie Enzyklopädie" ; # This is the title that is displayed on the main page
$wikiHeaderSubtitle = "Die freie Enzykolpädie" ;
$wikiArticleSubtitle = "Von Wikipedia, der freien Enzyklopädie." ;
$wikiPrintable = "Druckversion" ;
$wikiWatch = "Diesen Artikel für mich beobachten." ;
$wikiNoWatch = "Diesen Artikel nicht mehr für mich beobachten." ;
$wikiTitleTag = "$1: Enzykolpädieartikel von Wikipedia" ;
$wikiLogIn = "Einloggen" ;
$wikiLogOut = "Ausloggen" ;
$wikiHelp = "Hilfe" ;
$wikiHelpLink = "wikipedia:Hilfe" ;
$wikiPreferences = "Einstellungen" ;
$wikiWhatLinksHere = "Was linkt hierhin" ;
$wikiPrintLinksMarkup = "i" ; # will be used as <$wikiPrintLinksMarkup> and </$wikiPrintLinksMarkup>
#$wikiAllowedNamespaces = array ( "wikipedia" , "diskussion" , "benutzer" , "" , "wikipedia diskussion" , "benutzer diskussion" ) ;
$wikiTalk = "diskussion" ;
$wikiUser = "benutzer" ;
$wikiNamespaceTalk = "$1 Diskussion" ;
$wikiWikipedia = "wikipedia" ;
$wikiAllowedNamespaces = array ( $wikiWikipedia , $wikiTalk , $wikiUser , "" , "wikipedia $wikiTalk" , "$wikiUser $wikiTalk" ) ;
$wikiSkins = array ( "Normal" => "" , "Star Trek" => "Star Trek" , "Nostalgie" => "Nostalgy" , "Cologne Blue" => "Cologne Blue" ) ;
$wikiMyOptions = "Meine Optionen" ;
$wikiMySettings = "Meine Einstellungen" ;
$wikiMyself = "Meine Seite" ;
$wikiShortPages = "Kurze Seiten" ;
$wikiLongPages = "Lange Seiten" ;
$wikiUserList = "Benutzerverzeichnis" ;
$wikiEditingHistory = "Revisionsgeschichte" ;
$wikiAddToWatchlist = "Beobachten" ;
$wikiEditPage = "Jetzt bearbeiten" ;
$wikiHome = "Hauptseite" ;
$wikiAbout = "Über" ;
$wikiFAQ = "FAQ" ;
$wikiPageInfo = "Seiteninformation" ;
$wikiLinkedPages = "Verlinkte Seiten" ;
$wikiShowDiff = "zeige Unterschiede" ;
$wikiRequests = "Anfragen: $1" ;
$wikiEdit = "Bearbeiten" ;
$wikiPageOptions = "Seitenoptionen" ;
$wikiBrowse = "Blättern" ;
$wikiFind = "Finden" ;
$wikiOK = "OK" ;
$wikiFindMore = "Mehr finden";
$wikiWikipediaHome = "Hauptseite" ;
$wikiAboutWikipedia = "Über Wikipedia" ;
$wikiAutoWikify = "Auto-wikifikation (umsichtig benutzen!)" ;
$wikiTopics = "Themen" ;
$wikiWikipediaFAQ = "wikipedia:FAQ" ;
$wikiVoteForPage = "Abstimmen" ;

# Editing
$wikiEditingHelp = "Hilfe zum Bearbeiten" ;
$wikiWikipediaEditingHelp = "wikipedia:Wie bearbeite ich einen Artikel" ;
$wikiEditTitle = "Bearbeiten von $1" ;
$wikiCannotEditPage = "<h3>Sie können diese Seite nicht bearbeiten!</h3>" ;
$wikiEditConflictMessage = "<h1>Edit conflict!</h1>\n<b>Jemand hat eine neue Version dieser Seite gespeichert, während Sie sie bearbeitet haben. Der obere Kasten enthält die aktuelle Version, der untere Ihre Änderungen. Nur der obere Text wird gespeichert werden!</b><br>\n" ;
$wikiPreviewAppend = "<hr>\n<h2>Voransicht :</h2>\n$1<hr><h3>Achtung: Dies ist nur eine Voransicht, der Text wurde noch nicht gespeichert!</h3>" ;
$wikiSummary = "Zusammenfassung:" ;
$wikiDescription = "Beschreibung der Änderung:" ;
$wikiMinorEdit = "Es wurden nur Kleinigkeiten verändert." ;
$wikiCopyrightNotice = "Bitte beachte, dass alle Beiträge zur Wikipedia unter der \"GNU Freie Dokumentationslizenz\" stehen. Falls Du nicht willst, dass Dein Geschriebenes von anderen verändert und weiterverbreitet wird, dann solltest Du nicht auf \"Speichern\" drücken. Du gibst uns außerdem Deine Zusage, dass Du den Text selbst verfasst hast, oder es von einer Quelle kopiert hast, die sich in öffentlichem Besitz befindet (public domain). <b>BENUTZE KEINE URHEBERRECHTLICH GESCHÜTZTEN WERKE OHNE ERLAUBNIS DES AUTORS!</b>";
$wikiSave = "Speichern" ;
$wikiPreview = "Vorschau" ;
$wikiReset = "Zurücksetzten" ;
$wikiDontSaveChanges = "<i>Abbrechen</i>" ;
$wikiDescribePage = "Beschreibe hier die neue Seite." ;

# wikiUser
$wikiNoSuchUser = "<font color=red>Unbekannter Benutzer '$1'!</font>" ;
$wikiWrongPassword = "<font color=red>Falsches Passwort für Benutzer '$1'!</font>" ;
$wikiYouAreLoggedIn = "$1, sie sind eingeloggt!" ; 
$wikiUserError = "Problem mit '$1'" ;


# ------------------------------------------------------------ BIS HIERHIN ÜBERSETZT! ---------------------------------------------------

# wikiPage
$wikiNoSuchSpecialPage = "<h1>No such special page \"$1\"!</h1>" ;
$wikiOldVersion = "<br><font size=\"-1\">This is the old version #$1; see the <a href=\"$THESCRIPT?title=$2\">current version</a></font>" ;
$wikiRedirectFrom = "(redirected from $1)" ;
$wikiRecentChanges = "Recent Changes" ;
$wikiRecentChangesLink = "RecentChanges" ; # "special:" is added automatically
$wikiRecentLinked = "Watch page links" ;
$wikiRecentLinkedLink = "Recentchangeslinked" ;
$wikiSpecialPagesLink = "Special_pages" ; # "special:" is added automatically
$wikiEditThisPage = "Edit this page" ;
$wikiMoveThisPage = "Move this page" ;
$wikiDeleteThisPage = "Delete this page" ;
$wikiUpload = "Upload files" ;
$wikiHistory = "History" ;
$wikiRandomPage = "Random Page" ;
$wikiSpecialPages = "Special Pages" ;
$wikiEditHelp = "<i>Editing Help</i>" ;
$wikiEditHelpLink = "wikipedia:How_does_one_edit_a_page" ;
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
$wikiBlockIPTitle = "Block an IP (sysops only)" ;
$wikiBlockIPText = "IP $1 was blocked by $2" ;
$wikiIPblocked = "<font color=red size='+1'>Your IP has been blocked! Your edits will not be saved. Please contact a sysop to have the block removed, or try again later.</font>" ;
$wikiBugReports = "Bug reports" ;
$wikiBugReportsLink = "wikipedia:Bug Reports" ;
$wikiPrintFooter = "<hr>This article is from <b>Wikipedia</b> (<a href=\"$1\">$1</a>),
the free online encyclopedia. You can find this article at <a href=\"$2\">$2</a>" ;

# Interwiki links
$wikiInterwiki = array ( "w"=>"http://www.wikipedia.com/wiki/$1" , "m"=>"http://meta.wikipedia.com/wiki.phtml?title=$1" ) ;

# Language interwiki links
$wikiOtherLanguages = array (
    "ar"=>"http://ar.wikipedia.com/wiki.cgi?$1" ,
    "ca"=>"http://ca.wikipedia.com/wiki.cgi?$1" ,
    "zh"=>"http://zh.wikipedia.com/wiki.cgi?$1" ,
    "dk"=>"http://dk.wikipedia.com/wiki.cgi?$1" ,
    "nl"=>"http://nl.wikipedia.com/wiki.cgi?$1" ,
    "de"=>"http://de.wikipedia.com/wiki.cgi?$1" ,
    "eo"=>"http://eo.wikipedia.com/wiki/$1" ,
    "fr"=>"http://fr.wikipedia.com/wiki.cgi?$1" ,
    "he"=>"http://he.wikipedia.com/wiki.cgi?$1" ,
    "hu"=>"http://hu.wikipedia.com/wiki.cgi?$1" ,
    "it"=>"http://it.wikipedia.com/wiki.cgi?$1" ,
    "ja"=>"http://ja.wikipedia.com/wiki/$1" ,
    "pl"=>"http://pl.wikipedia.com/wiki.cgi?$1" ,
    "pt"=>"http://pt.wikipedia.com/wiki.cgi?$1" ,
    "ru"=>"http://ru.wikipedia.com/wiki.cgi?$1" ,
    "simple"=>"http://simple.wikipedia.com/wiki.cgi?$1" ,
    "es"=>"http://es.wikipedia.com/wiki.cgi?$1" ,
    "sv"=>"http://sv.wikipedia.com/wiki.cgi?$1" ,
    "en"=>"http://www.wikipedia.com/wiki/$1"
    ) ;

# Language interwiki names
$wikiLanguageNames = array (
#    "ar"=>"Arabic (Araby)" ,
#    "ca"=>"Catalan (Catala)" ,
#    "zh"=>"Chinese (Hanyu)" ,
#    "dk"=>"Danish (Dansk)" ,
#    "nl"=>"Dutch (Nederlands)" ,
#    "de"=>"German (Deutsch)" ,
#    "eo"=>"Esperanto" ,
#    "fr"=>"French (Francais)" ,
#    "he"=>"Hebrew (Ivrit)" ,
#    "hu"=>"Hungarian (Magyar)" ,
#    "it"=>"Italian (Italiano)" ,
#    "ja"=>"Japanese (Nihongo)" ,
#    "pl"=>"Polish (Polska)" ,
#    "pt"=>"Portuguese (Portuguese)" ,
#    "ru"=>"Russian (Russkiy)" ,
#    "simple"=>"Simplified English" ,
#    "es"=>"Spanish (Castellano)" ,
#    "sv"=>"Swedish (Svenska)" ,
#    "en"=>"English"
	"af" => "Afrikaans",
	"ar" => "Araby",
	"es" => "Castellano",
	"ca" => "Catal&agrave;",
	"sh" => "Croatoserbian",
	"dk" => "Dansk",
	"de" => "Deutsch",
	"en" => "English",
	"simple" => "English (simplified)",
	"eu" => "Euskara",
	"fr" => "Fran&ccedil;ais",
	"zh" => "Hanyu",
	"ia" => "Interlingua",
	"it" => "Italiano",
	"he" => "Ivrit",
	"hu" => "Magyar",
	"nl" => "Nederlands",
	"ja" => "Nihongo",
	"no" => "Norsk",
	"pl" => "Polska",
	"pt" => "Portugu&ecirc;s",
	"ru" => "Russkiy",
	"sv" => "Svensk",
	"vo" => "Volap&uuml;k"
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

# Moving
$wikiMoveRedirectMessage = "Moved to $1" ;
$wikiMoveMoved = "$1 was successfully moved to $2" ;
$wikiMoveRedirected = " A redirect was created." ;
$wikiMoveWarning = "<font color=red><b>'$1' already exists! Please choose another name.</b></font><br><br>\n" ;
$wikiMoveForm = "
<h2>You are about to move '$1' and its history to a new title.</h2>\n
<FORM method=post>\n
New name : <INPUT type=text value='$2' name=newname size=40 maxlength=250><br><br>\n
<INPUT type=checkbox$checked name=doredirect>Create a #REDIRECT from '$1' to the new title<br><br>\n
<INPUT type=submit name=doit value='Move'>\n
</FORM>\n" ;

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
$wikiAreYouNew = "If you are new to wikipedia and want to get a user account, enter a user name, type and re-type a password.
Your e-mail address is optional; if you lose your password you can request a new one to be sent to the address you give.<br>\n" ;
$wikiLogIn = "Log in" ;
$wikiCreateAccount = "Create new account" ;

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
$wikiSearchHelp = "Please see [[wikipedia:Searching|help about searching]]." ;
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
$wikiEditTypes = array ( "0"=>"" , "1"=>"<font color=cyan>M</font>" , "2"=>"<font color=green>N</font>" ) ; #Abbreviations for minor edits (1) and new pages (2) to be used on RecentChanges
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
$wikiWatchlistExistText = "'''Currently, you are watching the following existing articles :'''" ;
$wikiWatchlistNotExistText = "'''Currently, you are watching the following non-existing articles :'''" ;

# Statistics
$wikiStatisticsTitle = "Article statistics" ;
$wikiStatTotalPages = "There are $1 pages in the database." ;
$wikiStatTalkPages = "There are $1 '''Talk''' pages." ;
$wikiStatCommaPages = "There are $1 pages with a comma that are ''not'' '''Talk''' pages." ;
$wikiStatWikipediaNoTalk = "There are $1 that have \"ikipedi\" in the title and are ''not'' '''Talk''' pages." ;
$wikiStatSubNoTalk = "There are $1 subpages that are ''not'' '''Talk''' pages." ;
$wikiStatNoTalk = "That means there are about $1 articles, including subpages (except '''Talk''')." ;
$wikiStatArticles = "Or, there are about $1 articles, not counting any subpages!" ;
$wikiStatRedirect = "There are about $1 #REDIRECT pages." ;
$wikiStatSkin = "<font color=red>$1</font> of them use the \"$2\" skin." ;
$wikiStatJunk = "Finally, there are about $1 junk pages :-(" ;
$wikiStatOld = "And, there are $1 old page versions in the database, giving an average of $2 old pages on every active page." ;
$wikiUserStatistics = "User statistics" ;
$wikiStatUsers = "There are currently $1 [[special:ListUsers|users]] signed up." ;
$wikiStatSysops = "$1 of them have sysop status." ;

# Upload
$wikiUploadTitle = "Upload page" ;
$wikiUploadDenied = "You are neither an editor nor a sysop. Return to the <a href=\"$THESCRIPT?action=upload\">Upload page</a>" ;
$wikiUploadDeleted = "File <b>$1</b> deleted!" ;
$wikiUploadDelMsg1 = "*On $3, [[user:$1|$1]] deleted file '''$2'''\n" ;
$wikiUploadDelMsg2 = "Deletion of file $1" ;
$wikiUploadAffirm = "<nowiki>You need to affirm that the file is not violating copygights. Return to the <a href=\"$THESCRIPT?title=special:upload\">Upload page</a></nowiki>" ;
$wikiUploadRestrictions = "<nowiki>You have to log in to upload a file!</nowiki>" ;
$wikiUploadFull = "Sorry, we are almost out of disk space. We can't let you upload any files right now." ;
$wikiUploadSuccess = "File <b>$1</b> was successfully uploaded!" ;
$wikiUploadSuccess1 = "*On $1, $2 uploaded file '''$3'''$4\n" ;
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

# ------------------------------------------------------- Alles ab hier ist übersetzt ! ------------------------------------

# Misc
$wikiHistoryTitle = "Geschichte von $1" ;
$wikiHistoryHeader = "Dies ist die Geschite der Änderungen von <a href=\"$THESCRIPT?title=$1\">$2</a>" ;
$wikiCurrentVersion = "aktuell" ;
$wikiSpecialTitle = "Spezielle Seiten" ;
$wikiSpecialText = "<b>Dies ist eine Liste spezieller Seiten.</b> Einige von ihnen stehen nur eingeloggten Benutzern zur Verfügung. Wenn Sie eingeloggt sind, können Sie einen Teil dieser Liste stets als Seitenleiste eingeblendet lassen.<br><br>" ;
$wikiStubTitle = "Kurze Srtikel" ;
$wikiLongPagesTitle = "Die längsten Artikel" ;
$wikiStubText = "'''Dies sind alle Artikel in der Datenbank, sortiert nach Länge, mit den kürzesten zuerst.'''<br>''Weitergeleitete (#REDIRECTed) Seiten und Seiten in Namensräumen (z.B. Diskussion:) werden hier '''nicht''' angezeigt!''\n\n" ;
$wikiLongPagesText = "'''Dies sind alle Artikel in der Datenbank, sortiert nach Länge, mit den längsten zuerst.'''<br>''Weitergeleitete (#REDIRECTed) Seiten und Seiten in Namensräumen (z.B. Diskussion:) werden hier '''nicht''' angezeigt!''\n\n" ;
$wikiStubChars = "$1 Zeichen" ;
$wikiAskSQL = "MySQL-Zugang" ;
$wikiAskSQLtext = "Diese Funktion ist nur für Systemadministratoren zugänglich.<br>''Bitte nicht Eingabe drücken, '''immer''' nur mit der Maus auf den Knopf klicken!''" ;
$wikiSQLSafetyMessage = "Da Sie keinen Entwicklerstatus haben, können Sie nur SELECT-Kommandos ausführen." ;
$wikiStubDelete = "<b>Diese Seite löschen!</b>" ;
$wikiStubLinkHere = "$1 Artikel verweisen auf diesen." ;
$wikiStubShowLinks = "Die \"Verweisanzeige\" umschalten" ;
$wikiShowLinks = "Zeige Seiten, die nach \"$1\" verweisen" ;
$wikiRecentChangesLinkedTitle = "Die letzten Änderungen auf Seiten, zu denen '$1' verweist" ;
$wikiDeleteTitle = "Löschen des Artikels '$1'" ;
$wikiDeleteDenied = "<font size=\"+3\">Sie sind nicht berechtigt, diese Seite zu löschen!</font>" ;
$wikiDeleteSuccess = "'$1' wurde gelöscht." ;
$wikiDeleteMsg1 = "*[[user:$2|$2]] hat die Seite '''$3''' permanent gelöscht ($1)\n" ;
$wikiDeleteMsg2 = "Permanente Löschung von '$1'" ;
$wikiDeleteAsk = "Sie sind dabei, den Artikel \"$1\" und seine komplette Geschichte zu löschen!<br>\nWenn Sie genau wissen, was Sie tun, klicken Sie bitte <a href=\"$2&iamsure=yes\">hier</a>." ;
$wikiProtectTitle = "Schützen des Artikels '$1'" ;
$wikiProtectDenied = "<font size=\"+3\">Sie sind nicht berechtigt, diese Seite zu schützen!</font>" ;
$wikiProtectNow = "Die Seite '$1' ist nun geschützt ($2)." ;
$wikiProtectText = "<font size=\"+2\">Sie können nun die Seite '$target' schützen</font><br><i>Zum Bleistift, \"is_sysop\" wird verhindern, dass jemand anderes als ein Systemadministrator diese Seite ändert. Mehrere Berechtigungen werden durch \",\" getrennt.</i>" ;
$wikiProtectCurrent = "Gegenwärtiger Schutz : " ;
$wikiContribTitle = "Beiträge von $1" ;
$wikiContribText = "<h1>Beiträge von $1' :</h1>\n(Ausnahmen: Kleine Veränderungen sowie ''Disskussion'' und ''Log''-Seiten)" ;
$wikiContribDenied = "Geben Sie einen Benutzernamen an!" ;
$wikiLinkhereTitle = "Seiten, die nach '$1' verweisen" ;
$wikiLinkhereBacklink = "[[$1]] verweist zurück auf diese Artikel:" ;
$wikiLinkhereNoBacklink = "[[$1]] verweist ''nicht'' zurück auf diese Artikel:" ;
$wikiBacklinkNolink = "Es gibt keine Artikel, die nach [[$1]] verweisen!" ;
$wikiBacklinkFollowing = "Die folgenden Artikel verweisen auf [[$1]]:" ;
$wikiWantedTitle = "Die am meisten gewünschten Seiten" ;
$wikiWantedText = "'''Diese Artikel existieren noch nicht, aber andere Artikel verweisen auf sie!''' (die \"Top 50\")<br>\n" ;
$wikiWantedToggleNumbers = "Hier klicken, um die Anzeige von Artikeln, die mit Ziffern beginnen, umzuschalten (momentan $1)" ;
$wikiWantedLine = "<b>$2</b> Artikel verweisen auf $1 <nowiki>(<a href=\"$3\">diese Seiten verweisen auf \"$4\"</a></nowiki>)" ;
$wikiLastChange = "Letzte Änderung: $1" ;
$wikiLastChangeCologne = "Letzte Änderung dieser Seite: $1" ;
$wikiShowLastChange = "Die letzten Änderungen" ;
$wikiProtectThisPage = "Diese Seite schützen" ;
$wikiPopularPages = "Populäre Seiten" ;
$wikiPopularTitle = "Die populärsten Seiten" ;
$wikiRefreshThisPage = "Seite neu laden" ;
$wikiResourcesWarning = "(Bitte nur, wenn notwendig, da diese Funktion viel Rechenkapazität benötigt)" ;
$wikiNoRefresh = "(Diese Seite wurde erst von $1 Minuten aufrefrischt; bitte warten sie noch $2 Minuten und versuchen Sie es dann erneut.)" ;
$wikiLastRefreshed = "Zuletzt aufgefrischt: $1" ;
$wikiValidate = "Den HTML-Code dieser Seite überprüfen" ;
$wikiBlockIP = "Diese IP-Adresse blockieren" ;
$wikiNostalgy = "Nostalgie" ;
$wikiCologneBlue = "Cologne Blue" ;

# Vote
$wikiVoteReason = "Grund für diese Stimme : " ;

#---------------------------
#Functions
function wikiGetDateDe ( $x ) { # Used in RecentChangesLayout in special_functions.php
    global $wikiDate ;
    $dayName = $wikiDate [ strtolower ( date ( "l" , $x ) ) ];
    $monthName = $wikiDate [ strtolower ( date ( "F" , $x ) ) ];
    $dayNumber = date ( "j" , $x ) ;
    $year = date ( "Y" , $x ) ;
    return "$dayName, $dayNumber. $monthName, $year" ;
    }
function wikiGetBriefDateDe () { #Brief date for link in sidebar
    global $wikiDate ;
    $monthName = $wikiDate [ strtolower ( date ( "F" ) ) ];
    $dayNumber = date ( "j" ) ;
    $year = date ( "Y" ) ;
    return "$dayNumber. $monthName, $year" ;
    }
$wikiGetDate = wikiGetDateDe;
$wikiGetBriefDate = wikiGetBriefDateDe ;

# In theory, this could be expanded to allow general conversion of the
# character encoding used in the database to another encoding optionally
# used on the browser end.

# Define these arrays if you need to set up conversion.
# $wikiEncodingCharsets = array("iso-8859-1");
# $wikiEncodingNames = array("Latin-1"); # Localised names

function wikiRecodeOutputDe($text) {
  # Stub
  # global $user;  # $user->options["encoding"] is an index into the above arrays
  return $text;
}

function wikiRecodeInputDe($text) {
  # Stub
  return $text;
}

$wikiRecodeOutput = wikiRecodeOutputDe;
$wikiRecodeInput = wikiRecodeInputDe;
?>
