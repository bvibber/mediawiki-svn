<?

/* private */ $wgValidNamespacesEn = array(
	"Special", "Talk", "User", "User_talk", 
	"Wikipedia", "Wikipedia_talk", "Image",
	"Image_talk"
);

/* private */ $wgQuickbarSettingsEn = array(
	"None", "Left", "Right"
);

/* private */ $wgSkinNamesEn = array(
	"Standard", "StarTrek", "Nostalgia", "CologneBlue"
);

/* private */ $wgUserTogglesEn = array(
	"hover"	=> "Show hoverbox over wiki links",
	"underline" => "Underline links",
	"highlightbroken" => "Highlight links to empty topics",
	"justify"	=> "Justify paragraphs",
	"hideminor" => "Hide minor edits in recent changes",
	"numberheadings" => "Auto-number headings",
	"rememberpassword" => "Remember password across sessions"
);

/* private */ $wgLanguageNamesEn = array(
	"w" => "English", "af" => "Afrikaans", "ar" => "Arabic",
	"ca" => "Catalan", "zh" => "Chinese", "dk" => "Danish",
	"nl" => "Dutch", "de" => "German", "eo" => "Esperanto",
	"fr" => "French", "he" => "Hebrew", "hu" => "Hungarian",
	"it" => "Italian", "ja" => "Japanese", "no" => "Norwegian",
	"pl" => "Polish", "pt" => "Portugese", "ru" => "Russian",
	"sh" => "Serbocroatian", "si" => "Slovene",
	"simple" => "Simple English", "es" => "Spanish",
	"sv" => "Swedish", "en" => "English"
);

/* private */ $wgWeekdayNamesEn = array(
	"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday",
	"Friday", "Saturday"
);

/* private */ $wgMonthNamesEn = array(
	"January", "February", "March", "April", "May", "June",
	"July", "August", "September", "October", "November",
	"December"
);

/* private */ $wgMonthAbbreviationsEn = array(
	"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug",
	"Sep", "Oct", "Nov", "Dec"
);

/* private */ $wgAllMessagesEn = array(

# Bits of text used by many pages:
#
"mainpage"		=> "Main Page",
"errorpagetitle" => "Erorr",
"returntomain"	=> "Return to the [[Main Page]].",
"returnto"		=> "Return to [[$1]].",
"fromwikipedia"	=> "From Wikipedia, the free encyclopedia.",
"whatlinkshere"	=> "Pages that link here",
"help"			=> "Help",
"search"		=> "Search",
"history"		=> "History",
"printableversion" => "Printable version",
"editthispage"	=> "Edit this page",
"talkpage"		=> "Talk page",
"subjectpage"	=> "Subject page",
"otherlanguages" => "Other languages",
"redirectedfrom" => "(Redirected from $1)",
"lastmodified"	=> "The page was last modified $1.",
"viewcount"		=> "This page has been accessed $1 times.",
"printsubtitle" => "(From http://www.wikipedia.com)",
"protectedpage" => "Protected page",
"sysoptitle"	=> "Sysop access required",
"sysoptext"		=> "The action you have requested can only be
performed by users with \"sysop\" status.
See [[Wikipedia:Administrators]].",

# Main script and global functions
#
"nosuchaction"	=> "No such action",
"nosuchactiontext" => "The action specified by the URL is not
recognized by the Wikipedia software",
"nosuchspecialpage" => "No such special page",
"nospecialpagetext" => "You have requested a special page that is not
recognized by the Wikipedia software",

# General errors
#
"databaseerror" => "Database error",
"dberrortext"	=> "An unrecoverable database error has occurred.
Please report this to an administrator.
The last attempted database query was:
<blockquote><tt>$1</tt></blockquote>
from within function \"<tt>$2</tt>\".
MySQL returned error \"<tt>$3: $4</tt>\".",
"noconnect"		=> "Could not connect to DB on $1",
"nodb"			=> "Could not select database $1",
"readonly"		=> "Database locked",
"readonlytext"	=> "The Wikipedia database is currently locked to
new entries, probably because of database maintenance.
Please try again later.\n",
"missingarticle" => "The database did not find the text of an article
that it should have found, named \"$1\".
This is not a database error, but likely a bug in the software.
Please report this to an administrator, making note of the URL.",
"internalerror" => "Internal error",
"filecopyerror" => "Could not copy file \"$1\" to \"$2\".",
"filerenameerror" => "Could not rename file \"$1\" to \"$2\".",
"filedeleteerror" => "Could not delete file \"$1\".",
"filenotfound"	=> "Could not find file \"$1\".",
"unexpected"	=> "Unexpected value: \"$1\"=\"$2\".",

# Login and logout pages
#
"logouttitle"	=> "User logout",
"logouttext"	=> "You are now logged out.
You can continue to use Wikipedia anonymously, or you can log in
again as the same or as a different user.\n",

"welcomecreation" => "<h2>Welcome, $1!</h2><p>Your account has been created.
Don't forget to personalize your wikipedia preferences.",

"loginpagetitle" => "User login",
"yourname"		=> "Your user name",
"yourpassword"	=> "Your password",
"yourpasswordagain" => "Retype password",
"newusersonly"	=> " (new users only)",
"remembermypassword" => "Remember my password across sessions.",
"loginproblem"	=> "<b>There has been a problem with your login.</b><br>Try again!",
"alreadyloggedin" => "<font color=red><b>User $1, you are already logged in!</b></font><br>\n",

"areyounew"		=> "If you are new to Wikipedia and want to get a user account,
enter a user name, then type and re-type a password.
Your e-mail address is optional; if you lose your password you can request
that it be to the address you give.<br>\n",

"login"			=> "Log in",
"userlogin"		=> "Log in",
"logout"		=> "Log out",
"userlogout"	=> "Log out",
"createaccount"	=> "Create new account",
"badretype"		=> "The passwords you entered do not match.",
"userexists"	=> "The user name you entered is already in use. Please choosea different name.",
"youremail"		=> "Your e-mail",
"yournick"		=> "Your nickname (for signatures)",
"emailforlost"	=> "If you forget your password, you can have a new one mailed to your e-mail address.",
"loginerror"	=> "Login error",
"noname"		=> "You must specify a user name.",
"loginsuccesstitle" => "Login successful",
"loginsuccess"	=> "You are now logged in to Wikipedia as \"$1\".",
"nosuchuser"	=> "There is no user by the name \"$1\".
Check your spelling, or use the form below to create a new user account.",
"wrongpassword"	=> "The password you entered is incorrect. Please try again.",
"mailmypassword" => "Mail me a new password",
"passwordremindertitle" => "Password reminder from Wikipedia",
"passwordremindertext" => "Someone (probably you, from IP address $1)
requested that we send you a new Wikipedia login password.
The password for user \"$2\" is now \"$3\".
You should log in and change your password now.",
"noemail"		=> "There is no e-mail address recorded for user \"$1\".",
"passwordsent"	=> "A new password has been sent to the e-mail address
registered for \"$1\".
Please log in again after you receive it.",

# Edit pages
#
"summary"		=> "Summary",
"minoredit"		=> "This is a minor edit.",
"savearticle"	=> "Save article",
"preview"		=> "Preview",
"showpreview"	=> "Show preview",
"blockedtitle"	=> "User is blocked",
"blockedtext"	=> "Your user name or IP address has been blocked by $1.
The reason given is this:<br>$2<p>You may contact the administrator to
discuss the block.",
"newarticle"	=> "(New)",
"newarticletext" => "Put your text for the new article here.",
"noarticletext" => "(There is currently no text in this article)",
"updated"		=> "(Updated)",
"note"			=> "<strong>Note:</strong> ",
"previewnote"	=> "Remember that this is only a preview, and has not yet been saved!",
"previewconflict" => "This preview reflects the text in the upper
text editing area as it will appear if you choose to save.",
"editing"		=> "Editing $1",
"editconflict"	=> "Edit conflict: $1",
"explainconflict" => "Someone else has changed this article since you
started editing it.
The upper text area contains the article text as it currently exists.
Your changes are shown in the lower text area.
You will have to merge your changes into the existing text.
<b>Only</b> the text in the upper text area will be saved when you
press \"Save\".\n<p>",
"yourtext"		=> "Your text",
"storedversion" => "Stored version",
"editingold"	=> "<strong>WARNING: You are editing an out-of-date
revision of this article.
If you save it, any changes made since this revision will be lost.</strong>\n",
"yourdiff"		=> "Differences",

# History pages
#
"revhistory"	=> "Revision history",
"nohistory"		=> "There is no edit history for this page.",
"revnotfound"	=> "Revision not found",
"revnotfoundtext" => "The old revision of the page you asked for could not be found.
Please check the URL you used to access this page.\n",
"loadhist"		=> "Loading article history",
"currentrev"	=> "Current revision",
"revisionasof"	=> "Revision as of $1",
"cur"			=> "cur",
"next"			=> "next",
"last"			=> "last",
"orig"			=> "orig",
"histlegend"	=> "Legend: (cur) = difference with current version,
(last) = difference with preceding version, M = minor edit",

# Diffs
#
"difference"	=> "(Difference between revisions)",
"loadingrev"	=> "loading revision for diff",
"lineno"		=> "Line $1:",
"editcurrent"	=> "Edit the current version of this page",

# Search results
#
"searchresults" => "Search results",
"searchquery"	=> "For query \"$1\"",
"badquery"		=> "Badly formed search query",
"badquerytext"	=> "We could not process your query.
This is probably because you have attempted to search for a
word fewer than four letters long, which is not yet supported.
It could also be that you have mistyped the expression, for
example \"fish and and scales\".
Please try another query.",
"matchtotals"	=> "The query \"$1\" matched $2 article titles
and the text of $3 articles.",
"titlematches"	=> "Article title matches",
"notitlematches" => "No article title matches",
"textmatches"	=> "Article text matches",
"notextmatches"	=> "No article text matches",
"searchprev"	=> "previous $1",
"searchnext"	=> "next $1",
"searchlinks"	=> "View ($1) ($2) search results.",
"showingmatches" => "Showing below <b>$1</b> matches starting with #<b>$2</b>.",
"nonefound"		=> "<strong>Note</strong>: an unsuccessful search may be
caused by searching for common words like \"have\" and \"from\" that are
not indexed.",

# Preferences page
#
"preferences"	=> "Preferences",
"prefsnologin" => "Not logged in",
"prefsnologintext"	=> "You must be <a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">logged in</a>
to set user preferences.",
"prefsreset"	=> "Preferences have been reset from storage.",
"qbsettings"	=> "Quickbar settings", 
"changepassword" => "Change password",
"skin"			=> "Skin",
"saveprefs"		=> "Save preferences",
"resetprefs"	=> "Reset preferences",
"oldpassword"	=> "Old password",
"newpassword"	=> "New password",
"retypenew"		=> "Retype new password",
"textboxsize"	=> "Textbox dimensions",
"rows"			=> "Rows",
"columns"		=> "Columns",
"resultsperpage" => "Number of search results per page",
"recentchangescount" => "Number of titles in recent changes",
"savedprefs"	=> "Your preferences have been saved.",

# Recent changes
#
"recentchanges" => "Recent changes",
"recentchangestext" => "Track the most recent changes to Wikipedia on this page.
[[Wikipedia:Welcome,_newcomers|Welcome, newcomers]]!
Please have a look at these pages: [[wikipedia:FAQ|Wikipedia FAQ]],
[[Wikipedia:Policies and guidelines|Wikipedia policy]]
(especially [[wikipedia:Naming conventions|naming conventions]],
[[wikipedia:Neutral point of view|neutral point of view]]),
and [[wikipedia:Most common Wikipedia faux pas|most common Wikipedia faux pas]].

If you want to see Wikipedia succeed, it's very important that you don't add
material restricted by others' [[wikipedia:Copyrights|copyrights]].
The legal liability could really hurt the project, so please don't do it.
See also the [http://meta.wikipedia.com/wiki.phtml?title=Special:RecentChanges recent meta discussion].",
"rcloaderr"		=> "Loading recent changes",
"rcnote"		=> "Below are the last <b>$1</b> changes in the last <b>$2</b> days.",
"rclinks"		=> "View the last $1 changes; view the last $2 days.",
"diff"			=> "diff",
"hist"			=> "hist",

# New pages
#
"newpages"		=> "New pages",

# Upload
#
"upload"		=> "Upload",
"reupload"		=> "Re-upload",
"reuploaddesc"	=> "Return to the upload form.",
"uploadfile"	=> "Upload file",
"uploaderror"	=> "Upload error",
"uploadtext"	=> "Use the form below to upload image files for
use in illustrating your articles.
On most browsers, you will see a \"Browse...\" button, which will
bring up your operating system's standard file open dialog.
Choosing a file will fill the name of that file into the text
field next to the button.
You must also check the box affirming that you are not
violating any copyrights by uploading the file.
Press the \"Upload\" button to finish the upload.
This may take some time if you have a slow internet connection.
<p> The preferred formats are JPEG for photographic images and PNG
for drawings and other iconic images.
To include the image in an article, use a link in the form
<b>[[image:file.jpg]]</b> or <b>[[image:file.png|alt text]]</b>.",
"uploadlog"		=> "upload log",
"uploadlogtext" => "Uploads and deletions are logged on the $1",
"filename"		=> "Filename",
"filedesc"		=> "Summary",
"affirmation"	=> "I affirm that the copyright holder of this file
agrees to license it under the terms of the $1.",
"copyrightpage" => "Wikipedia:Copyrights",
"copyrightpagename" => "Wikipedia copyright",
"uploadedfiles"	=> "Uploaded files",
"noaffirmation" => "You must affirm that your upload does not violate
any copyrights.",
"ignorewarning"	=> "Ignore warning and save file anyway.",
"minlength"		=> "Image names must be at least three letters.",
"badfilename"	=> "Image name has been changed to \"$1\".",
"badfiletype"	=> "\".$1\" is not a recommended image file format.",
"largefile"		=> "It is recommended that images not exceed 100k in size.",
"successfulupload" => "Successful upload",
"fileuploaded"	=> "File \"$1\" uploaded successfully.",
"uploadwarning" => "Upload warning",
"savefile"		=> "Save file",

# Image list
#
"imagelist"		=> "Image list",
"imagelisttext"	=> "Below is a list of $1 images sorted $2.",
"getimagelist"	=> "fetching image list",
"showall"		=> "Show all images sorted $1.",
"showlast"		=> "Show last $1 images sorted $2.",
"all"			=> "all",
"byname"		=> "by name",
"bydate"		=> "by date",
"bysize"		=> "by size",
"imgdelete"		=> "del",
"imgdesc"		=> "desc",
"imglegend"		=> "Legend: (del) = delete image, (desc) = show/edit image description.",
"imghistory"	=> "Image history",
"revertimg"		=> "rev",
"deleteimg"		=> "del",
"imghistlegend" => "Legend: (cur) = this is the current image, (del) = delete
this old version, (rev) = revert to this old version.",
"imagelinks"	=> "Image links",
"linkstoimage"	=> "The following articles link to this image:",
"nolinkstoimage" => "There are no articles that link to this image.",

# Statistics
#
"statistics"	=> "Statistics",

# Orphans
#
"orphans"		=> "Orphaned pages",
"lonelypages"	=> "Orphaned pages",

# Popular pages
#
"popularpages"	=> "Popular pages",

# Wanted pages
#
"wantedpages"	=> "Wanted pages",

# All pages
#
"allpages"		=> "All pages",
"allpagestext"	=> "Below is a list of every article currently in
the Wikipedia database.",
"getarticlelist" => "fetching article list",

# Random page
#
"randompage"	=> "Random page",
"findrandom"	=> "looking for random article",

# Short pages
#
"shortpages"	=> "Short pages",

# Long pages
#
"longpages"		=> "Long pages",

# List users
#
"listusers"		=> "User list",
"getuserlist"	=> "fetching user list",
"userlisttext"	=> "Below is a list of all registered users.",

# Watchlist
#
"watchlist"		=> "Watch list",

# Special pages
#
"specialpages"	=> "Special pages",

# Delete page
#
"deletepage"	=> "Delete page",
"confirm"		=> "Confirm",
"confirmdelete" => "Confirm delete",
"deletesub"		=> "(Deleting \"$1\")",
"confirmdeletetext" => "You are about to permanently delete an article
or image along with all of its history from the database.
Please confirm that you intend to do this, that you understand the
consequences, and that you are doing this in accordance with
[[Wikipedia:Policy]].",
"confirmcheck"	=> "Yes, I really want to delete this.",
"actioncomplete" => "Action complete",
"deletedtext"	=> "\"$1\" has been deleted.",
"deletedarticle" => "deleted \"$1\"",

# Move page
#
"movepage"		=> "Move page",

# Protect page
#
"protectpage"	=> "Protect page",

# Contributions
#
"contributions"	=> "Contributions",

# What links here
#
"whatlinkshere"	=> "What links here",

# Recent changes linked
#
"recentchangeslinked" => "Watch links",

# SQL dump
#
"sqldump"		=> "SQL dump",

# Vote
#
"vote"			=> "Vote for page",

# Asksql
#
"asksql"		=> "SQL query",

# Block IP
#
"blockip"		=> "Block IP",

# Debug page
#
"debug"			=> "Debug",

);

class Language {

	function getNamespaces() {
		global $wgValidNamespacesEn;
		return $wgValidNamespacesEn;
	}

	function getQuickbarSettings() {
		global $wgQuickbarSettingsEn;
		return $wgQuickbarSettingsEn;
	}

	function getSkinNames() {
		global $wgSkinNamesEn;
		return $wgSkinNamesEn;
	}

	function getUserToggles() {
		global $wgUserTogglesEn;
		return $wgUserTogglesEn;
	}

	function getLanguageName( $code ) {
		global $wgLanguageNamesEn;
		if ( ! array_key_exists( $code, $wgLanguageNamesEn ) ) {
			return "";
		}
		return $wgLanguageNamesEn[$code];
	}

	function getMonthName( $key )
	{
		global $wgMonthNamesEn;
		return $wgMonthNamesEn[$key-1];
	}

	function getMonthAbbreviation( $key )
	{
		global $wgMonthAbbreviationsEn;
		return $wgMonthAbbreviationsEn[$key-1];
	}

	function getWeekdayName( $key )
	{
		global $wgWeekdayNamesEn;
		return $wgWeekdayNamesEn[$key-1];
	}

	function date( $ts )
	{
		$d = $this->getMonthAbbreviation( substr( $ts, 4, 2 ) ) .
		  " " . (0 + substr( $ts, 6, 2 )) . ", " .
		  substr( $ts, 0, 4 );
		return $d;
	}

	function time( $ts )
	{
		$t = substr( $ts, 8, 2 ) . ":" . substr( $ts, 10, 2 );
		return $t;
	}

	function timeanddate( $ts )
	{
		return $this->time( $ts ) . " " . $this->date( $ts );
	}

	function getMessage( $key )
	{
		global $wgAllMessagesEn;
		return $wgAllMessagesEn[$key];
	}
}

include_once( "Language" . ucfirst( $wgLanguageCode ) . ".php" );

?>
