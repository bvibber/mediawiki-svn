<?

class LanguageDe extends Language {

/* private */ $wgValidNamespacesDe = array(
	"Special", "Diskussion", "Benutzer", "Benutzer_diskussion", 
	"Wikipedia", "Wikipedia_diskussion"
);

/* private */ $wgQuickbarSettingsDe = array(
	"None", "Left", "Right"
);

/* private */ $wgSkinNamesDe = array(
	"Normal", "Star Trek", "Nostalgie", "Cologne Blue"
);

/* private */ $wgUserTogglesDe = array(
	"showHover"	=> "Show hoverbox over wiki links",
	"underlineLinks" => "Underline links",
	"markupNewTopics" => "Show links to empty topics in red",
	"justify"	=> "Justify paragraphs",
	"hideMinor" => "Hide minor edits in recent changes",
	"numberHeadings" => "Auto-number headings",
	"rememberPassword" => "Remember my password"
);

/* private */ $wgWeekdaysDe = array(
	"Sontag", "Montag", "Dienstag", "Mittwoch", "Donnerstag",
	"Freitag", "Sonnabend"
);

/* private */ $wgMonthsDe = array(
	"Januar", "Februar", "März", "April", "Mai", "Juni", "Juli",
	"August", "September", "Oktober", "November", "Dezember"
);

/* private */ $wgAllMessagesDe = array(

# Bits of text used by many pages:
#
"mainpage"		=> "Hauptseite",
"errorpagetitle" => "Verflixte! Ein Fehler!",

# Main script and global functions
#

# Database errors
#

# Login and logout pages
#

# Edit pages
#

# Preferences page
#

# Recent changes
#

# New pages
#

# Upload
#

# Statistics
#

# Orphans
#

# Popular pages
#

# Wanted pages
#

# All pages
#

# Random page
#

# Short pages
#

# Long pages
#

# List users
#

# Watchlist
#

# Special pages
#

# Delete page
#

# Move page
#

# Protect page
#

# Contributions
#

# What links here
#

# Recent changes linked
#

# SQL dump
#

# Vote
#

# Asksql
#

# Block IP
#

);

class LanguageDe {

	function getNamespaces() {
		global $wgValidNamespacesDe
		return $wgValidNamespacesDe;
	}

	function getQuickbarSettings() {
		global $wgQuickbarSettingsDe;
		return $wgQuickbarSettingsDe;
	}

	function getSkinNames() {
		global $wgSkinNamesDe;
		return $wgSkinNamesDe;
	}

	function getUserToggles() {
		global $wgUserTogglesDe;
		return $wgUserTogglesDe;
	}

	function getMonthName( $key )
	{
		global $wgMonthsDe;
		return $wgMonthsDe[$key-1];
	}

	function getWeekdayName( $key )
	{
		global $wgWeekDaysDe;
		return $wgWeekDaysDe[$key-1];
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
		global $wgAllMessagesEn, $wgAllMessagesDe;

		if ( "" == $wgAllMessagesDe[$key] ) {
			if ( "" == $wgAllMessagesEn[$key] ) {
				wfDebug( "Couldn't find message \"$key\"\n" );
				return "[$key]";
			} else {
				return $wgAllMessagesEn[$key];
			}
		}
		return $wgAllMessagesDe[$key];
	}
}

?>
