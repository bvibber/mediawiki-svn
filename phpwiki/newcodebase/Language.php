<?

/* private */ $wgValidNamespacesEn = array(
	"Special", "Talk", "User", "User_talk", 
	"Wikipedia", "Wikipedia_talk"
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

/* private */ $wgLanguageNamesEn= array(
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

# Main script and global functions
#
"nosuchaction"	=> "No such action",
"nosuchactiontext" => "The action specified by the URL is not
recognized by the Wikipedia software",
"nosuchspecialpage" => "No such special page",
"nospecialpagetext" => "You have requested a special page that is not
recognized by the Wikipedia software",

# Database errors
#
"databaseerror" => "Database error",
"dberrortext"	=> "An unrecoverable database error has occurred.
Please report this to an administrator.
The operation being attempted was: $1.",
"noconnect"		=> "Could not connect to DB on $1",
"nodb"			=> "Could not select database $1",
"updatingarticle" => "updating an article",
"readonly"		=> "Database locked",
"readonlytext"	=> "The Wikipedia database is currently locked to
new entries, probably because of database maintenance.
Please try again later.\n",

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
"emailforlost"	=> "If you forget your password, you can have it mailed to your e-mail address.",

"loginerror"	=> "Login error",
"noname"		=> "You must specify a user name.",
"loginsuccesstitle" => "Login successful",
"loginsuccess"	=> "You are now logged in to Wikipedia as \"$1\".",
"nosuchuser"	=> "There is no user by the name \"$1\".
Check your spelling, or use the form below to create a new user account.",
"wrongpassword"	=> "The password you entered is incorrect. Please try again.",
"mailmypassword" => "Mail me my password",
"passwordremindertitle" => "Password reminder from Wikipedia",

"passwordremindertext" => "Someone (probably you) requested that we send
you a reminder of your Wikipedia login password.  The password for
user \"$1\" is \"$2\".
If you did not make this request, you may want to change your password now,
as there may have been an attempt to learn your password by intercepting
this mail.",

"noemail"		=> "There is no e-mail address recorded for user \"$1\".",
"passwordsent"	=> "A password has been sent to the e-mail address
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
"newarticle"	=> "(New) $1",
"newarticletext" => "Describe the new page here.",
"updated"		=> "(Updated) $1",
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

# Preferences page
#
"preferences"	=> "Preferences",
"prefsnologin" => "Not logged in",
"prefsnologintext"	=> "You must be <a href=\"" .
  wfLocalLink( "Special:Userlogin" ) . "\">logged in</a>
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

# New pages
#
"newpages"		=> "New pages",

# Upload
#
"upload"		=> "Upload",

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

# Random page
#
"randompage"	=> "Random page",

# Short pages
#
"shortpages"	=> "Short pages",

# Long pages
#
"longpages"		=> "Long pages",

# List users
#
"listusers"		=> "User list",

# Watchlist
#
"watchlist"		=> "Watch list",

# Special pages
#
"specialpages"	=> "Special pages",

# Delete page
#
"deletepage"	=> "Delete page",

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
"recentchangeslinked" => "Watch page links",

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
		return $wgLanguageNamesEn[$code];
	}

	function getMonthName( $key )
	{
		return ucfirst( $key );
	}

	function getWeekdayName( $key )
	{
		return ucfirst( $key );
	}

	function dateFromTimestamp( $ts )
	{
		$d = substr( $ts, 0, 4 ) . "-" .
		  substr( $ts, 4, 2 ) . "-" .
		  substr( $ts, 6, 2 );
		return $d;
	}

	function getMessage( $key )
	{
		global $wgAllMessagesEn;

		if ( "" == $wgAllMessagesEn[$key] ) {
			wfDebug( "Couldn't find message \"$key\"\n" );
		}
		return $wgAllMessagesEn[$key];
	}
}

include_once( "Language" . ucfirst( $wgLanguageCode ) . ".php" );

?>
