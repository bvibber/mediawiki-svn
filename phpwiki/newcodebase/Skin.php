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

	function getSkinNames() { return $wgValidSkinNames; }

	function initPage()
	{
		global $wgOut, $wgStyleSheetPath;

		$wgOut->addLink( "stylesheet", "",
		  "$wgStyleSheetPath/wikistandard.css" );
	}

	function getUserStyles()
	{
		global $wgUser;

		$s = "<style type=\"text/css\"><!--\n";
		if ( 1 == $wgUser->getOption( "underlineLinks" ) ) {
			$s .= "a { text-decoration: underline; }\n";
		} else {
			$s .= "a { text-decoration: none; }\n";
		}
		if ( 1 == $wgUser->getOption( "markupNewTopics" ) ) {
			$s .= "a.new { color: white; background: blue; }\n";
		}
		$s .= "//--></style>\n";
		return $s;
	}

	function getBodyOptions()
	{
		global $wgTitle, $wgNamespaceBackgrounds;

		$ns = Namespace::getName( $wgTitle->getNamespace() );

		if ( "" != $ns && key_exists( $ns, $wgNamespaceBackgrounds ) ) {
			return array( "bgcolor" => $wgNamespaceBackgrounds[$ns] );
		}
		else return array( "bgcolor" => "#ffffff" );
	}

	function getExternalLinkAttributes( $link, $text )
	{
		global $wgUser;

		$r = " class=\"external\"";
		if ( 1 == $wgUser->getOption( "showHover" ) ) {
			$r .= " title=\"$link\"";
		}
		return $r;
	}

	function getInternalLinkAttributes( $link, $text, $broken = false )
	{
		global $wgUser;

		if ( $broken ) { $r = " class=\"new\""; }
		else { $r = " class=\"internal\""; }

		if ( 1 == $wgUser->getOption( "showHover" ) ) {
			$r .= " title=\"$link\"";
		}
		return $r;
	}

	function getLogo()
	{
		global $wgLogo;
		return $wgLogo;
	}

	# This will be called immediately after the <body> tag.
	#
	function beforeContent()
	{
		global $wgUser, $wgOut, $wgTitle;

		$mp = wfMsg( "mainpage" );

		$s = "<table width=\"100%\" class=\"topbar\" "
		  . "cellspacing=0><tr><td valign=top height=1>";
		$s .= $this->pageTitle();

		$s .= "</td>\n<td valign=top width=200 rowspan=2 nowrap>";
		$s .= $this->nameAndLogin();
		$s .= "\n<br>" . $this->searchForm();

		$s .= "</td>\n<td rowspan=2 width=1><a href=\"" . wfLocalLink( $mp )
		  . "\"><img border=0 src=\"" . $this->getLogo() . "\" alt=\""
		  . "[$mp]\"></a></td></tr>\n";

		$s .= "<tr><td valign=bottom>" . $this->topLinks()
		  . "</td></tr></table>\n";

		$s .= "<table width=\"100%\" class=\"middle\" cellpadding=2 "
		  . "cellspacing=0><tr>";

		$q = $wgUser->getOption( "quickBar" );

		if ( 1 == $q ) { # "Left"
			$s .= "<td class=\"quickbar\" width=110 valign=top nowrap>";
			$s .= $this->quickBar();
			$s .= "</td><td valign=top>\n";
		} else if ( 0 == $q || "" == $q ) { # "None"
			$s .= "<td colspan=2 valign=top>\n";
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
		global $wgUser;

		$s = "</div></td>";
		$q = $wgUser->getOption( "quickBar" );

		if ( "" != $q && 0 != $q && 1 != $q ) {
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
		global $wgOut, $wgTitle;

		$s = "<h1 class=\"pagetitle\">" . $wgOut->getPageTitle() . "</h1>";
		if ( $wgOut->isArticle() ) {
			$s .= "<p class=\"subtitle\"><small>" . wfMsg( "fromwikipedia" )
			  . "</small>\n";

			if ( $wgOut->isArticle() ) {
				$s .= "<p class=\"subtitle\">"
				  . $this->makeLink( wfMsg( "mainpage" ),
				  WfMsg( "printableversion" ), "action=print" )
				  . " | " . $this->makeLink( "Special:Whatlinkshere",
				  wfMsg( "whatlinkshere" ), "target=" . $wgTitle->getPrefixedURL() );

				$s .= $this->otherLanguages();
			}
		}
		return $s;
	}

	function nameAndLogin()
	{
		global $wgUser, $wgTitle;

		if ( 0 == $wgUser->getID() ) {
			$n = getenv( "REMOTE_ADDR" );
			$s .= $n . "\n<br>" . $this->makeLink( "Special:Userlogin",
			  wfMsg( "login" ), "returnto=" . $wgTitle->getPrefixedURL() );
		} else {
			$n = $wgUser->getName();
			$s .= $this->makeLink( "User:$n", $n ) . "<br>" .
			  $this->makeLink( "Special:Userlogout", wfMsg( "logout" ),
			  "returnto=" . $wgTitle->getPrefixedURL() ) . " | " .
			  $this->specialLink( "preferences" );
		}
		$s .= " | " . $this->makeLink( "Wikipedia:Help", wfMsg( "help" ) ); 

		return $s;
	}

	function searchForm()
	{
		global $wgServer, $wgScript;

		$s .= "<form method=get action=\"$wgServer$wgScript\">"
		  . "<input type=text name=\"search\" size=16 value=\"\">\n"
		  . "<input type=submit value=\"" . wfMsg( "search" )
		  . "\"></form>";

		return $s;
	}

	function topLinks()
	{
		global $wgOut;
		$sep = " | ";

		$s .= $this->mainPageLink() . $sep
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
		global $wgOut;
		$s = $this->topLinks();

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
		$s = "This article has been accessed $count times.\n";

		$d = $wgLang->dateFromTimestamp( $wgArticle->getTimestamp() );
		$s .= "Last modified $d.\n";

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
		$s = $this->makeLink( $mp, $mp );
		return $s;
	}

	function editThisPage()
	{
		global $wgOut, $wgTitle;

		if ( ! $wgOut->isArticle() ) {
			$s = "(Special page)";
		} else if ( $wgTitle->userCanEdit() ) {
			$s = $this->makeLink( $wgTitle->getPrefixedText(),
			  wfMsg( "editthispage" ), "action=edit" );
		} else {
			$s = "Protected page";
		}
		return $s;
	}

	function historyLink()
	{
		global $wgTitle;

		$s = $this->makeLink( $wgTitle->getPrefixedText(),
		  wfMsg( "history" ), "action=history" );
		return $s;
	}

	function watchPageLinksLink()
	{
		global $wgOut, $wgTitle;

		if ( ! $wgOut->isArticle() ) {
			$s = "(Special page)";
		} else {
			$s = $this->makeLink( "Special:Recentchangeslinked",
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
		$s = $this->makeLink( "Wikipedia:Bug_reports", "Bug reports" );
		return $s;
	}

	function dateLink()
	{
		$t1 = Title::newFromText( date( "F j" ) );
		$t2 = Title::newFromText( date( "Y" ) );

		if ( 0 == $t1->getArticleID() ) {
			$s = $this->makeBrokenLink( $t1->getText() );
		} else {
			$s = $this->makeLink( $t1->getText() );
		}
		$s .= ", ";
		if ( 0 == $t2->getArticleID() ) {
			$s .= $this->makeBrokenLink( $t2->getText() );
		} else {
			$s .= $this->makeLink( $t2->getText() );
		}
		return $s;
	}

	function talkLink()
	{
		global $wgTitle;

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

		$s = $this->makeInternalLink( $link, $text );
		return $s;
	}

	# After all the page content is transformed into HTML, it makes
	# a final pass through here for things like table backgrounds.
	#
	function transformContent( $text )
	{
		return $text;
	}

	function makeInternalLink( $title, $text= "", $query = "", $trail = "" )
	{
		$nt = Title::newFromText( $title );
		if ( -1 == $nt->getNamespace() ) {
			return $this->makeLink( $title, $text, $query, $trail );
		}
		if ( 0 == $nt->getArticleID() ) {
			return $this->makeBrokenLink( $title, $text ) . $trail;
		} else {
			return $this->makeLink( $title, $text, $query, $trail );
		}
	}

	function makeLink( $title, $text = "", $query = "", $trail = "" )
	{
		global $wgServer, $wgScript, $wgArticlePath, $wgTitle;

		$nt = Title::newFromText( $title );
		$link = $nt->getPrefixedURL();

		if ( "" == $query ) {
			$u = str_replace( "$1", $link, $wgArticlePath );
		} else {
			$u = "$wgServer$wgScript?title=$link&$query";
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
		global $wgUser;

		$nt = Title::newFromText( $title );
		$link = $nt->getEditURL();

		if ( "" == $text ) { $text = $nt->getPrefixedText(); }
		$style = $this->getInternalLinkAttributes( $link, $text, true );

		if ( 1 == $wgUser->getOption( "markupNewTopics" ) ) {
			$s = "<a href=\"$link\"$style>$text</a>";
		} else {
			$s = "$text<a href=\"$link\"$style>?</a>";
		}
		return $s;
	}

	function makeImageLink( $url, $alt = "" )
	{
		if ( "" == $alt ) { $alt = "[Image]"; }
		$s = "<img src=\"$url\" alt=\"$alt\">";
		return $s;
	}

	function specialLink( $name )
	{
		$key = strtolower( $name );
		$pn = ucfirst( $key);
		return $this->makeLink( "Special:$pn", wfMsg( $key ) );
	}
}

include_once( "SkinStandard.php" );
include_once( "SkinStarTrek.php" );
include_once( "SkinNostalgia.php" );
include_once( "SkinCologneBlue.php" );

?>
