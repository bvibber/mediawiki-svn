<?
# See skin.doc

# These are the INTERNAL names, which get mapped
# directly to class names.  For display purposes, the
# Language class has internationalized names
#
/* private */ $wgValidSkinNames = array(
	"Standard", "StarTrek", "Nostalgia", "CologneBlue"
);

/* private */ $wgNamespaceBackgrounds = array(
	"Talk" => "#eeffff", "User_talk" => "#eeffff",
	"Wikipedia_talk" => "#eeffff", "User" => "#ffeeee",
	"Wikipedia" => "#eeffee", "Special" => "#eeeeee"
);

class Skin {

	/* private */ var $lastdate, $lastline;

	function Skin()
	{
	}

	function getSkinNames()
	{
		global $wgValidSkinNames;
		return $wgValidSkinNames;
	}

	function getStylesheet()
	{
		return "wikistandard.css";
	}

	function initPage()
	{
		global $wgOut, $wgStyleSheetPath;

		if ( $wgOut->isPrintable() ) { $ss = "wikiprintable.css"; }
		else { $ss = $this->getStylesheet(); }
		$wgOut->addLink( "stylesheet", "", "$wgStyleSheetPath/{$ss}" );
	}

	function getUserStyles()
	{
		global $wgUser;

		$s = "<style type=\"text/css\"><!--\n";
		if ( 1 == $wgUser->getOption( "underline" ) ) {
			$s .= "a { text-decoration: underline; }\n";
		} else {
			$s .= "a { text-decoration: none; }\n";
		}
		if ( 1 == $wgUser->getOption( "highlightbroken" ) ) {
			$s .= "a.new { color: white; background: blue; }\n";
		}
		$s .= "//--></style>\n";
		return $s;
	}

	function getBodyOptions()
	{
		global $wgTitle, $wgNamespaceBackgrounds;

		$ns = Namespace::getName( $wgTitle->getNamespace() );

		if ( "" != $ns && array_key_exists( $ns, $wgNamespaceBackgrounds ) ) {
			return array( "bgcolor" => $wgNamespaceBackgrounds[$ns] );
		}
		else return array( "bgcolor" => "#ffffff" );
	}

	function getExternalLinkAttributes( $link, $text )
	{
		global $wgUser;

		$r = " class=\"external\"";
		if ( 1 == $wgUser->getOption( "hover" ) ) {
			$r .= " title=\"$link\"";
		}
		return $r;
	}

	function getInternalLinkAttributes( $link, $text, $broken = false )
	{
		global $wgUser;

		if ( $broken ) { $r = " class=\"new\""; }
		else { $r = " class=\"internal\""; }

		if ( 1 == $wgUser->getOption( "hover" ) ) {
			$r .= " title=\"$link\"";
		}
		return $r;
	}

	function getLogo()
	{
		global $wgLogo;
		return $wgLogo;
	}

	# This will be called immediately after the <body> tag.  Split into
	# two functions to make it easier to subclass.
	#
	function beforeContent()
	{
		global $wgUser, $wgOut;

		if ( $wgOut->isPrintable() ) {
			$s = "<h1 class=\"pagetitle\">" . $wgOut->getPageTitle() . "</h1>";
			$s .= $this->pageSubtitle();
			$s .= "<div class=\"bodytext\">";
			return $s;
		}
		return $this->doBeforeContent();
	}

	function doBeforeContent()
	{
		global $wgUser, $wgOut, $wgTitle;

		$s = "<table width=\"100%\" class=\"topbar\" "
		  . "cellspacing=0><tr><td valign=top height=1>";
		$s .= $this->pageTitle();

		$s .= "</td>\n<td valign=top width=200 rowspan=2 nowrap>";
		$s .= $this->nameAndLogin();
		$s .= "\n<br>" . $this->searchForm();

		$mp = wfMsg( "mainpage" );
		$s .= "</td>\n<td rowspan=2 width=1><a href=\"" . wfLocalLink( $mp )
		  . "\"><img border=0 src=\"" . $this->getLogo() . "\" alt=\""
		  . "[$mp]\"></a></td></tr>\n";

		$s .= "<tr><td valign=bottom>" . $this->topLinks()
		  . "</td></tr></table>\n";

		$s .= "<table width=\"100%\" class=\"middle\" cellpadding=2 "
		  . "cellspacing=0><tr>";

		$q = $wgUser->getOption( "quickbar" );
		if ( $wgOut->isQuickbarSupressed() ) { $q = 0; }

		if ( 0 == $q || "" == $q ) { # "None"
			$s .= "<td colspan=2 valign=top>\n";
		} else if ( 1 == $q ) { # "Left"
			$s .= "<td class=\"quickbar\" width=110 valign=top nowrap>";
			$s .= $this->quickBar();
			$s .= "</td><td valign=top>\n";
		} else { # Right, default
			$s .= "<td valign=top>\n";
		}
		$s .= "<div class=\"bodytext\">";
		return $s;
	}

	# This gets called immediately before the </body> tag.
	#
	function afterContent()
	{
		global $wgUser, $wgOut;

		if ( $wgOut->isPrintable() ) {
			$s = "</div>\n";
			return $s;
		}
		return $this->doAfterContent();
	}

	function doAfterContent()
	{
		global $wgUser, $wgOut, $_SERVER;

		$s = "</div></td>";
		$q = $wgUser->getOption( "quickbar" );
		if ( $wgOut->isQuickbarSupressed() ) { $q = 0; }

		if ( ( ! $this->quickBarOff ) && "" != $q && 0 != $q && 1 != $q ) {
			$s .= "<td class=\"quickbar\" width=110 valign=top nowrap>";
			$s .= $this->quickBar() . "</td>";
		}
		$s .= "</tr></table>\n";
		$s .= "<table width=\"100%\" class=\"footer\" cellspacing=0><tr><td>";
		$s .= $this->bottomLinks() . "</td></tr></table>";

		$s .= "\n" . $this->pageStats();
		$s .= "\n" . $this->searchForm();
		return $s;
	}

	function pageTitle()
	{
		global $wgOut, $wgTitle, $oldid, $action;

		$s = "<h1 class=\"pagetitle\">" . $wgOut->getPageTitle() . "</h1>";
		$s .= $this->pageSubtitle();

		if ( "history" == $action ) { $q = "action=history&amp;"; }
		else { $q = ""; }

		$s .= "<p class=\"subtitle\">"
		  . $this->makeKnownLink( $wgTitle->getPrefixedText(),
		  WfMsg( "printableversion" ), "{$q}printable=yes" );

		if ( $wgOut->isArticle() ) {
			$s .= " | " . $this->makeKnownLink( "Special:Whatlinkshere",
			  wfMsg( "whatlinkshere" ), "target=" . $wgTitle->getPrefixedURL() );

			if ( $oldid ) {
				$s .= " | " . $this->makeKnownLink( $wgTitle->getPrefixedText(),
				  wfMsg( "currentrev" ) );
			}
			$s .= $this->otherLanguages();
		}
		if ( "history" == $action ) {
			$s .= " | " . $this->makeKnownLink( $wgTitle->getPrefixedText(),
			  wfMsg( "currentrev" ) );
		}
		return $s;
	}

	function pageSubtitle()
	{
		global $wgOut;

		$sub = $wgOut->getSubtitle();
		if ( "" == $sub ) { $sub = wfMsg( "fromwikipedia" ); }
		$s = "<p class=\"subtitle\"><strong>{$sub}</strong>\n";
		return $s;
	}

	function nameAndLogin()
	{
		global $wgUser, $wgTitle;

		$s = "";
		if ( 0 == $wgUser->getID() ) {
			$n = getenv( "REMOTE_ADDR" );
			$rt = $wgTitle->getPrefixedURL();
			if ( 0 == strcasecmp( urlencode("Special:Userlogout"), $rt ) ) {
				$q = "";
			} else { $q = "returnto={$rt}"; }

			$s .= $n . "\n<br>" . $this->makeKnownLink( "Special:Userlogin",
			  wfMsg( "login" ), $q );
		} else {
			$n = $wgUser->getName();
			$rt = $wgTitle->getPrefixedURL();
			$s .= $this->makeKnownLink( "User:$n", $n ) . "<br>" .
			  $this->makeKnownLink( "Special:Userlogout",
			  wfMsg( "logout" ), "returnto={$rt}" ) . " | " .
			  $this->specialLink( "preferences" );
		}
		$s .= " | " . $this->makeKnownLink( "Wikipedia:Help", wfMsg( "help" ) ); 

		return $s;
	}

	function searchForm()
	{
		global $wgServer, $wgScript;

		$s = "<form method=get action=\"$wgServer$wgScript\">"
		  . "<input type=text name=\"search\" size=16 value=\"\">\n"
		  . "<input type=submit value=\"" . wfMsg( "search" )
		  . "\"></form>";

		return $s;
	}

	function topLinks()
	{
		global $wgOut;
		$sep = " | ";

		$s = $this->mainPageLink() . $sep
		  . $this->specialLink( "recentchanges" );
		if ( $wgOut->isArticle() ) {
			$s .=  $sep . $this->editThisPage()
			  . $sep . $this->historyLink();
		}
		$s .= $sep . $this->specialLink( "randompage" )
		  . $sep . $this->specialLink( "specialpages" );

		return $s;
	}

	function bottomLinks()
	{ 
		global $wgOut, $wgServer, $wgUploadPath;

		$s = "<a href=\"http://validator.w3.org/check/referer\">" . 
		  "<img align=right border=0 height=31 width=88 alt=\"Valid HTML\" " .
		  "src=\"$wgServer$wgUploadPath/valid-html401.png\"></a>";
		$s .= $this->topLinks();

		if ( $wgOut->isArticle() ) {
			$s .= " | " . $this->talkLink();
		}
		$s .= $this->otherLanguages();
		return $s;
	}

	function pageStats()
	{
		global $wgOut, $wgLang, $wgArticle;

		if ( ! $wgOut->isArticle() ) { return ""; }

		$count = $wgArticle->getCount();
		$s = str_replace( "$1", $count, wfMsg( "viewcount" ) );

		$d = $wgLang->timeanddate( $wgArticle->getTimestamp() );
		$s .= " " . str_replace( "$1", $d, wfMsg( "lastmodified" ) );
		return $s;
	}

	function quickBar()
	{
		global $wgOut, $wgTitle;

		$sep = "\n<br>";
		$s = $this->mainPageLink()
		  . $sep . $this->specialLink( "recentchanges" );
		if ( $wgOut->isArticle() ) {
			$s .= $sep . $this->watchPageLinksLink()
		  . $sep . $this->editThisPage()
		  . $sep . $this->historyLink();
		}
		$s .= $sep . $this->specialLink( "upload" )
		  . $sep . $this->specialLink( "imagelist" )
		  . $sep . "<hr>" . $this->specialLink( "statistics" )
		  . $sep . $this->specialLink( "newpages" )
		  . $sep . $this->specialLink( "lonelypages" )
		  . $sep . $this->specialLink( "wantedpages" )
		  . $sep . $this->specialLink( "popularpages" )
		  . $sep . $this->specialLink( "randompage" )
		  . $sep . $this->specialLink( "shortpages" )
		  . $sep . $this->specialLink( "longpages" )
		  . $sep . $this->specialLink( "listusers" )
		  . $sep . $this->bugReportsLink()
		  . $sep . $this->dateLink() . "\n<hr>";

		if ( $wgOut->isArticle() ) { $s .= $this->talkLink(); }
		return $s;
	}

	function mainPageLink()
	{
		$mp = wfMsg( "mainpage" );
		$s = $this->makeKnownLink( $mp, $mp );
		return $s;
	}

	function editThisPage()
	{
		global $wgOut, $wgTitle, $oldid, $redirect;

		if ( ! $wgOut->isArticle() ) {
			$s = "(Special page)";
		} else if ( $wgTitle->userCanEdit() ) {
			$n = $wgTitle->getPrefixedText();
			$t = wfMsg( "editthispage" );
			$oid = $red = "";

			if ( $oldid ) { $oid = "&amp;oldid={$oldid}"; }
			if ( $redirect ) { $red = "&amp;redirect={$redirect}"; }
			$s = $this->makeKnownLink( $n, $t, "action=edit{$oid}{$red}" );
		} else {
			$s = "Protected page";
		}
		return $s;
	}

	function historyLink()
	{
		global $wgTitle;

		$s = $this->makeKnownLink( $wgTitle->getPrefixedText(),
		  wfMsg( "history" ), "action=history" );
		return $s;
	}

	function watchPageLinksLink()
	{
		global $wgOut, $wgTitle;

		if ( ! $wgOut->isArticle() ) {
			$s = "(Special page)";
		} else {
			$s = $this->makeKnownLink( "Special:Recentchangeslinked",
			  wfMsg( "recentchangeslinked" ), "target=" . $wgTitle->getPrefixedURL() );
		}
		return $s;
	}

	function otherLanguages()
	{
		global $wgOut, $wgLang;

		$a = $wgOut->getLanguageLinks();
		if ( 0 == count( $a ) ) { return ""; }

		$s = "<br>" . wfMsg( "otherlanguages" ) . ": ";
		$first = true;
		foreach( $a as $l ) {
			if ( ! $first ) { $s .= " | "; }
			$first = false;

			preg_match( "/([a-z]+):([^\|]+)/", $l, $m );
			$urltemp = Title::getInterwikiLink( $m[1] );
			$link = $m[2];
			$text = $wgLang->getLanguageName( $m[1] );
			$url = str_replace( "$1", $link, $urltemp );

			if ( "" == $text ) { $text = $link; }
			$style = $this->getExternalLinkAttributes( $link, $text );
			$s .= "<a href=\"$url\"$style>$text</a>";
		}
		return $s . "\n";
	}

	function bugReportsLink()
	{
		$s = $this->makeKnownLink( "Wikipedia:Bug_reports", "Bug reports" );
		return $s;
	}

	function dateLink()
	{
		global $wgLinkCache;
		$t1 = Title::newFromText( date( "F j" ) );
		$t2 = Title::newFromText( date( "Y" ) );

		$wgLinkCache->suspend();
		$id = $t1->getArticleID();
		$wgLinkCache->resume();

		if ( 0 == $id ) {
			$s = $this->makeBrokenLink( $t1->getText() );
		} else {
			$s = $this->makeKnownLink( $t1->getText() );
		}
		$s .= ", ";

		$wgLinkCache->suspend();
		$id = $t2->getArticleID();
		$wgLinkCache->resume();

		if ( 0 == $id ) {
			$s .= $this->makeBrokenLink( $t2->getText() );
		} else {
			$s .= $this->makeKnownLink( $t2->getText() );
		}
		return $s;
	}

	function talkLink()
	{
		global $wgTitle, $wgLinkCache;

		$tns = $wgTitle->getNamespace();
		if ( -1 == $tns ) { return ""; }

		$pn = $wgTitle->getText();
		$tp = wfMsg( "talkpage" );
		$sp = wfMsg( "subjectpage" );

		if ( Namespace::isTalk( $tns ) ) {
			$lns = Namespace::getSubject( $tns );
			$text = $sp;
		} else {
			$lns = Namespace::getTalk( $tns );
			$text = $tp;
		}
		$n = Namespace::getName( $lns );
		if ( "" == $n ) { $link = $pn; }
		else { $link = "$n:$pn"; }

		$wgLinkCache->suspend();
		$s = $this->makeLink( $link, $text );
		$wgLinkCache->resume();

		return $s;
	}

	# After all the page content is transformed into HTML, it makes
	# a final pass through here for things like table backgrounds.
	#
	function transformContent( $text )
 	{
		return $text;
	}

	# Note: This function MUST call getArticleID() on the link,
	# otherwise the cache won't get updated properly.  See LINKCACHE.DOC.
	#
	function makeLink( $title, $text= "", $query = "", $trail = "" )
	{
		global $wgOut, $wgUser;

		if ( $wgOut->isPrintable() ) {
			return $this->makePrintableLink( $title, $text, $query, $trail );
		}
		$nt = Title::newFromText( $title );
		if ( -1 == $nt->getNamespace() ) {
			return $this->makeKnownLink( $title, $text, $query, $trail );
		}
		if ( 0 == $nt->getArticleID() ) {
			return $this->makeBrokenLink( $title, $text ) . $trail;
		} else {
			return $this->makeKnownLink( $title, $text, $query, $trail );
		}
	}

	function makeKnownLink( $title, $text = "", $query = "", $trail = "" )
	{
		global $wgOut, $wgServer, $wgScript, $wgArticlePath, $wgTitle;

		if ( $wgOut->isPrintable() ) {
			return $this->makePrintableLink( $title, $text, $query, $trail );
		}
		$nt = Title::newFromText( $title );
		$link = $nt->getPrefixedURL();

		if ( "" == $query ) {
			$u = str_replace( "$1", $link, $wgArticlePath );
		} else {
			$u = "$wgServer$wgScript?title=$link&amp;$query";
		}
		if ( "" == $text ) { $text = $nt->getPrefixedText(); }
		$style = $this->getInternalLinkAttributes( $link, $text );

		$inside = "";
		if ( "" != $trail ) {
			if ( preg_match( "/^([a-z]+)(.*)$$/sD", $trail, $m ) ) {
				$inside = $m[1];
				$trail = $m[2];
			}
		}
		$r = "<a href=\"$u\"$style>$text$inside</a>$trail";
		return $r;
	}

	function makeBrokenLink( $title, $text = "" )
	{
		global $wgOut, $wgUser;

		if ( $wgOut->isPrintable() ) {
			return $this->makePrintableLink( $title, $text, $query, $trail );
		}
		$nt = Title::newFromText( $title );
		$link = $nt->getEditURL();

		if ( "" == $text ) { $text = $nt->getPrefixedText(); }
		$style = $this->getInternalLinkAttributes( $link, $text, true );

		if ( 1 == $wgUser->getOption( "highlightbroken" ) ) {
			$s = "<a href=\"$link\"$style>$text</a>";
		} else {
			$s = "$text<a href=\"$link\"$style>?</a>";
		}
		return $s;
	}

	function makePrintableLink( $title, $text = "", $query = "", $trail = "" )
	{
		global $wgOut, $wgUser;

		if ( "" == $text ) {
			$nt = Title::newFromText( $title );
			$text = $nt->getPrefixedText();
		}
		if ( 1 == $wgUser->getOption( "underline" ) ) { $tag = "u"; }
		else { $tag = "i"; }

		$inside = "";
		if ( "" != $trail ) {
			if ( preg_match( "/^([a-z]+)(.*)$$/sD", $trail, $m ) ) {
				$inside = $m[1];
				$trail = $m[2];
			}
		}
		$r = "<{$tag} class=\"link\">$text$inside</{$tag}>$trail";
		return $r;
	}

	function makeImageLink( $url, $alt = "" )
	{
		global $wgOut;

		if ( "" == $alt ) { $alt = "[Image]"; }
		if ( $wgOut->isPrintable() ) { return $alt; }
		$s = "<img src=\"$url\" alt=\"$alt\">";
		return $s;
	}

	function specialLink( $name )
	{
		$key = strtolower( $name );
		$pn = ucfirst( $key);
		return $this->makeKnownLink( "Special:$pn", wfMsg( $key ) );
	}

	# Called by history lists and recent changes
	#

	function beginRecentChangesList()
	{
		$this->lastdate = "";
		return "";
	}

	function beginHistoryList()
	{
		$this->lastdate = $this->lastline = "";
		$s = "\n<p>" . wfMsg( "histlegend" ) . "\n<ul>";
		return $s;
	}

	function endRecentChangesList()
	{
		$s = "</ul>\n";
		return $s;
	}

	function endHistoryList()
	{
		$last = wfMsg( "last" );

		$s = preg_replace( "/!OLDID![0-9]+!/", $last, $this->lastline );
		$s .= "</ul>\n";
		return $s;
	}

	function historyLine( $ts, $u, $ut, $ns, $ttl, $oid, $c, $isminor )
	{
		global $wgLang;

		$artname = Title::makeName( $ns, $ttl );
		$last = wfMsg( "last" );
		$cur = wfMsg( "cur" );
		$cr = wfMsg( "currentrev" );

		if ( $oid && $this->lastline ) {
			$ret = preg_replace( "/!OLDID!([0-9]+)!/", $this->makeKnownLink(
			  $artname, $last, "diff=\\1&amp;oldid={$oid}" ), $this->lastline );
		} else {
			$ret = "";
		}
		$m = $wgLang->getMonthAbbreviation( substr( $ts, 4, 2 ) );
		$d = 0 + substr( $ts, 6, 2 );
		$h = substr( $ts, 8, 2 ) . ":" . substr( $ts, 10, 2 );
		$dt = "{$m} {$d} {$h}";

		if ( $oid ) { $q = "oldid={$oid}"; }
		else { $q = ""; }
		$link = $this->makeKnownLink( $artname, $dt, $q );

		if ( 0 == $u ) { $ul = $ut; }
		else { $ul = $this->makeLink( "User:{$ut}", $ut ); }

		$s = "<li>";
		if ( $oid ) {
			$curlink = $this->makeKnownLink( $artname, $cur,
			  "diff=0&amp;oldid={$oid}" );
		} else {
			$curlink = $cur;
		}
		$s .= "({$curlink}) (!OLDID!{$oid}!) . .";

		if ( $isminor ) { $s .= " <strong>M</strong>"; }
		$s .= " {$link} . . {$ul}";

		if ( "" != $c && "*" != $c ) {
			$s .= " <em>({$c})</em>";
		}
		$s .= "</li>\n";

		$this->lastline = $s;
		return $ret;
	}

	function recentChangesLine( $ts, $u, $ut, $ns, $ttl, $c, $isminor )
	{
		global $wgTitle, $wgLang;

		$d = $wgLang->date( $ts );
		$s = "";
		if ( $d != $this->lastdate ) {
			if ( "" != $this->lastdate ) { $s .= "</ul>\n"; }
			$s .= "<h4>{$d}</h4>\n<ul>";
			$this->lastdate = $d;
		}
		$h = substr( $ts, 8, 2 ) . ":" . substr( $ts, 10, 2 );
		$t = Title::makeName( $ns, $ttl );
		$clink = $this->makeKnownLink( $t, "" );
		$hlink = $this->makeKnownLink( $t, wfMsg( "hist" ), "action=history" );
		$dlink = $this->makeKnownLink( $t, wfMsg( "diff" ), "diff=0&amp;oldid=0" );

		if ( 0 == $u ) { $ul = $ut; }
		else { $ul = $this->makeLink( "User:{$ut}", $ut ); }
		$cr = wfMsg( "currentrev" );

		$s .= "<li> ({$dlink}) ({$hlink}) . .";
		if ( $isminor ) { $s .= " <strong>M</strong>"; }
		$s .= " {$clink}; {$h} . . {$ul}";
		if ( "" != $c && "*" != $c ) { $s .= " <em>({$c})</em>"; }
		$s .= "</li>\n";

		return $s;
	}
}

include_once( "SkinStandard.php" );
include_once( "SkinStarTrek.php" );
include_once( "SkinNostalgia.php" );
include_once( "SkinCologneBlue.php" );

?>
