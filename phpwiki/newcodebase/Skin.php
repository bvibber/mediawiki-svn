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
		global $wgUser, $wgOut, $wgStyleSheetPath;

		$qb = $wgUser->getOption( "quickbar" );
		if ( "" == $qb ) { $qb = 0; }

		if ( $qb < 3 ) {
			$s = "<script language='javascript' type='text/javascript' " .
			  "src='{$wgStyleSheetPath}/sticky.js'></script>\n";
		}
		$s .= "<style type='text/css' media='screen'><!--\n";
		if ( 1 == $wgUser->getOption( "underline" ) ) {
			$s .= "a { text-decoration: underline; }\n";
		} else {
			$s .= "a { text-decoration: none; }\n";
		}
		if ( 1 == $wgUser->getOption( "highlightbroken" ) ) {
			$s .= "a.new { color: white; background: blue; }\n";
		}
		if ( 2 == $qb || 4 == $qb ) {
			$s .= "#quickbar { position: absolute; top: 4px; right: 4px; " .
			  "visibility: visible; z-index: 99;}\n";
		} else {
			$s .= "#quickbar { position: absolute; top: 4px; left: 4px; " .
			  "visibility: visible; z-index: 99;}\n";
		}
		if ( ( 0 == $qb ) || $wgOut->isQuickbarSupressed() ) {
			$s .= "#topbar { margin-left: 4px; margin-right: 4px; }\n" .
			  "#article { margin-left: 4px; margin-right: 4px; }\n" .
			  "#footer { margin-left: 4px; margin-right: 4px; }\n";
		} else if ( 2 == $qb || 4 == $qb ) {
			$s .= "#topbar { margin-left: 4px; margin-right: 156px; }\n" .
			  "#article { margin-left: 4px; margin-right: 156px; }\n" .
			  "#footer { margin-left: 4px; margin-right: 156px; }\n";
		}
		$s .= "//--></style>\n";
		if ( $qb < 3 ) {
			$s .= "<style type='text/css' media='screen'>\n" .
			  "@import '{$wgStyleSheetPath}/quickbar.css';\n</style>\n";
		}
		return $s;
	}

	function getBodyOptions()
	{
		global $wgUser, $wgTitle, $wgNamespaceBackgrounds;

		$ns = Namespace::getName( $wgTitle->getNamespace() );

		if ( "" != $ns && array_key_exists( $ns, $wgNamespaceBackgrounds ) ) {
			$a = array( "bgcolor" => $wgNamespaceBackgrounds[$ns] );
		}
		else $a = array( "bgcolor" => "#FFFFFF" );

		$qb = $wgUser->getOption( "quickbar" );
		if ( "" == $qb ) { $qb = 0; }

		if ( $qb < 3 ) {
			$a["onload"] = "setup(\"quickbar\")";
		}
		return $a;
	}

	function getExternalLinkAttributes( $link, $text )
	{
		global $wgUser;

		$link = urldecode( $link );
		$link = str_replace( "_", " ", $link );

		$r = " class=\"external\"";
		if ( 1 == $wgUser->getOption( "hover" ) ) {
			$r .= " title=\"$link\"";
		}
		return $r;
	}

	function getInternalLinkAttributes( $link, $text, $broken = false )
	{
		global $wgUser;

		$link = urldecode( $link );
		$link = str_replace( "_", " ", $link );

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
			$s .= "\n<div class=\"bodytext\">";
			return $s;
		}
		return $this->doBeforeContent();
	}

	function doBeforeContent()
	{
		global $wgUser, $wgOut, $wgTitle;

		$s = "\n<div id='contentbox'>\n";
		$s .= "<div id='topbar'><table border=0><tr>" .
		  "<td valign=top align=left>";

		$qb = $wgUser->getOption( "quickbar" );
		if ( "" == $qb ) { $qb = 0; }

		if ( 0 == $qb ) {
			$s .= $this->logoText() .
			  "</td><td align=left valign=top>";
		}
		$s .= $this->topLinks() . "\n<br>";
		$s .= $this->pageTitleLinks();

		$s .= "</td>\n<td valign=top align=right width=200 nowrap>";
		$s .= $this->nameAndLogin();
		$s .= "\n<br>" . $this->searchForm();

		$s .= "</td></tr></table>\n</div>\n";

		$s .= "\n<div id='article'>";

		$s .= $this->pageTitle();
		$s .= $this->pageSubtitle() . "\n<p>";
		return $s;
	}

	# This gets called immediately before the </body> tag.
	#
	function afterContent()
	{
		global $wgUser, $wgOut;

		if ( $wgOut->isPrintable() ) {
			$s = "\n</div>\n";
			return $s;
		}
		return $this->doAfterContent();
	}

	function doAfterContent()
	{
		global $wgUser, $wgOut;

		$s = "\n</div><br clear=all>\n";

		$s .= "\n<div id='footer'>";
		$s .= $this->bottomLinks();

		$s .= "\n<br>" . $this->pageStats();
		$s .= "\n<br>" . $this->searchForm();
		$s .= "\n</div>\n</div>\n";

		$qb = $wgUser->getOption( "quickbar" );
		if ( "" == $qb ) { $qb = 0; }

		if ( ( ! $wgOut->isQuickbarSupressed() ) && ( 0 != $qb ) ) {
			$s .= $this->quickBar();
		}
		return $s;
	}

	function pageTitleLinks()
	{
		global $wgOut, $wgTitle, $oldid, $action;

		if ( "history" == $action ) { $q = "action=history&amp;"; }
		else { $q = ""; }

		$s = "<p class=\"subtitle\">"
		  . $this->makeKnownLink( $wgTitle->getPrefixedText(),
		  WfMsg( "printableversion" ), "{$q}printable=yes" );

		if ( $wgOut->isArticle() ) {
			if ( $oldid ) {
				$s .= " | " . $this->makeKnownLink( $wgTitle->getPrefixedText(),
				  wfMsg( "currentrev" ) );
			}
			if ( $wgTitle->getNamespace() == Namespace::getIndex( "Image" ) ) {
				$name = $wgTitle->getDBkey();
				$s .= " | <a href=\"" . wfImageUrl( $name ) . "\">{$name}</a>";
			}
			$s .= $this->otherLanguages();
		}
		if ( "history" == $action ) {
			$s .= " | " . $this->makeKnownLink( $wgTitle->getPrefixedText(),
			  wfMsg( "currentrev" ) );
		}
		return $s;
	}

	function pageTitle()
	{
		global $wgOut, $wgTitle;

		$s = "<h1 class=\"pagetitle\">" . $wgOut->getPageTitle() . "</h1>";
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
		$sep = " |\n";

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
		global $wgOut, $wgUploadPath;

		$s = "<a href=\"http://validator.w3.org/check/referer\">" . 
		  "<img align=right border=0 height=31 width=88 alt=\"Valid HTML\" " .
		  "src=\"$wgUploadPath/valid-html401.png\"></a>";
		$s .= $this->topLinks();

		if ( $wgOut->isArticle() ) {
			$s .= " |\n" . $this->talkLink();
		}
		$s .= $this->otherLanguages();
		return $s;
	}

	function pageStats()
	{
		global $wgOut, $wgLang, $wgArticle;
		global $oldid, $diff;

		if ( ! $wgOut->isArticle() ) { return ""; }
		if ( isset( $oldid ) || isset( $diff ) ) { return ""; }
		if ( 0 == $wgArticle->getID() ) { return ""; }

		$count = $wgArticle->getCount();
		$s = str_replace( "$1", $count, wfMsg( "viewcount" ) );

		$d = $wgLang->timeanddate( $wgArticle->getTimestamp() );
		$s .= " " . str_replace( "$1", $d, wfMsg( "lastmodified" ) );
		return $s;
	}

	function logoText()
	{
		$mp = wfMsg( "mainpage" );
		$s = "<a href='" . wfLocalUrl( $mp ) . "'><img border=0 src='" .
		  $this->getLogo() . "' alt='" . "[$mp]'></a>";
		return $s;
	}

	function quickBar()
	{
		global $wgOut, $wgTitle, $wgUser;

		$mp = wfMsg( "mainpage" );
		$s = "\n<div id='quickbar'>";
		$s .= "\n" . $this->logoText() . "\n<hr>";

		$sep = "\n<br>";
		$s .= $this->mainPageLink()
		  . $sep . $this->specialLink( "recentchanges" )
		  . $sep . $this->specialLink( "randompage" ) 
		  . $sep . $this->dateLink() . "\n<hr>";

		if ( $wgOut->isArticle() ) {
			$s .= $this->editThisPage();

			if ( $wgUser->isSysop() ) {
				$s .= $sep . $this->deleteThisPage();
			}
			$s .= $sep . $this->talkLink()
			  . $sep . $this->historyLink()
			  . $sep . $this->whatLinksHere()
			  . $sep . $this->watchPageLinksLink();

			if ( Namespace::getIndex( "User" ) == $wgTitle->getNamespace() ) {
				$s .= $sep . $this->userContribsLink();
			}
			$s .= "\n<hr>";
		}
		$s .= $this->specialLink( "upload" )
		  . $sep . $this->specialLink( "imagelist" )
		  . $sep . $this->specialLink( "listusers" )
		  . $sep . $this->specialLink( "statistics" )
		  . $sep
		  . $sep . $this->specialLink( "newpages" )
		  . $sep . $this->specialLink( "lonelypages" )
		  . $sep . $this->specialLink( "wantedpages" )
		  . $sep . $this->specialLink( "popularpages" )
		  . $sep . $this->specialLink( "shortpages" )
		  . $sep . $this->specialLink( "longpages" );

		$s .= $sep . $this->bugReportsLink();

		$s .= "\n</div>\n";
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
		global $wgOut, $wgTitle, $oldid, $redirect, $diff;

		if ( ! $wgOut->isArticle() || $diff ) {
			$s = wfMsg( "protectedpage" );
		} else if ( $wgTitle->userCanEdit() ) {
			$n = $wgTitle->getPrefixedText();
			$t = wfMsg( "editthispage" );
			$oid = $red = "";

			if ( $redirect ) { $red = "&amp;redirect={$redirect}"; }
			if ( $oldid && ! isset( $diff ) ) {
				$oid = "&amp;oldid={$oldid}";
			}
			$s = $this->makeKnownLink( $n, $t, "action=edit{$oid}{$red}" );
		} else {
			$s = wfMsg( "protectedpage" );
		}
		return $s;
	}

	function deleteThisPage()
	{
		global $wgUser, $wgOut, $wgTitle, $diff;

		if ( $wgOut->isArticle() && ( ! $diff ) && $wgUser->isSysop() ) {
			$n = $wgTitle->getPrefixedText();
			$t = wfMsg( "deletethispage" );

			$s = $this->makeKnownLink( $n, $t, "action=delete" );
		} else {
			$s = wfMsg( "protectedpage" );
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

	function whatLinksHere()
	{
		global $wgTitle;

		$s = $this->makeKnownLink( "Special:Whatlinkshere",
		  wfMsg( "whatlinkshere" ), "target=" . $wgTitle->getPrefixedURL() );
		return $s;
	}

	function userContribsLink()
	{
		global $wgTitle;

		$s = $this->makeKnownLink( "Special:Contributions",
		  wfMsg( "contributions" ), "target=" . $wgTitle->getURL() );
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

		if ( 0 == $nt->getNamespace() && "" == $nt->getText() ) {
			return $this->makeKnownLink( $title, $text, $query, $trail );
		}
		if ( ( -1 == $nt->getNamespace() ) ||
          ( Namespace::getIndex( "Image" ) == $nt->getNamespace() ) ) {
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

		if ( "" == $link ) {
			$u = "";
			if ( "" == $text ) { $text = $nt->getFragment(); }
		} else if ( "" == $query ) {
			$u = str_replace( "$1", $link, $wgArticlePath );
		} else {
			$u = "$wgServer$wgScript?title=$link&amp;$query";
		}
		if ( "" != $nt->getFragment() ) {
			$u .= "#" . $nt->getFragment();
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

	function makeImage( $url, $alt = "" )
	{
		global $wgOut;

		if ( "" == $alt ) { $alt = "[Image]"; }
		if ( $wgOut->isPrintable() ) { return $alt; }
		$s = "<img src=\"$url\" alt=\"$alt\">";
		return $s;
	}

	function makeImageLink( $name, $url, $alt = "" )
	{
		global $wgOut, $wgServer, $wgScript, $wgArticlePath, $wgTitle;

		$nt = Title::newFromText( "Image:{$name}" );
		$link = $nt->getPrefixedURL();

		if ( "" == $alt ) { $alt = $name; }

		$u = str_replace( "$1", $link, $wgArticlePath );
		$s = "<a href=\"{$u}\" class=\"image\" title=\"{$alt}\">" .
		  "<img border=0 src=\"{$url}\" alt=\"{$alt}\"></a>";
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

	function beginImageHistoryList()
	{
		$s = "\n<h2>" . wfMsg( "imghistory" ) . "</h2>\n" .
		  "<p>" . wfMsg( "imghistlegend" ) . "\n<ul>";
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

	function endImageHistoryList()
	{
		$s = "</ul>\n";
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
		$dt = $wgLang->timeanddate( $ts );

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

		if ( "" != $c && "*" != $c ) { $s .= " <em>({$c})</em>"; }
		$s .= "</li>\n";

		$this->lastline = $s;
		return $ret;
	}

	function recentChangesLine( $ts, $u, $ut, $ns, $ttl, $c, $isminor )
	{
		global $wgTitle, $wgLang, $wgUser;

		$d = $wgLang->date( $ts );
		$s = "";
		if ( $d != $this->lastdate ) {
			if ( "" != $this->lastdate ) { $s .= "</ul>\n"; }
			$s .= "<h4>{$d}</h4>\n<ul>";
			$this->lastdate = $d;
		}
		$h = $wgLang->time( $ts );
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

		if ( ( 0 == $u ) && $wgUser->isSysop() ) {
			$blink = $this->makeKnownLink( "Special:Blockip",
			  wfMsg( "blocklink" ), "ip={$ut}" );
			$s .= " ({$blink})";
		}

		if ( "" != $c && "*" != $c ) { $s .= " <em>({$c})</em>"; }
		$s .= "</li>\n";

		return $s;
	}

	function imageHistoryLine( $iscur, $ts, $img, $u, $ut, $size, $c )
	{
		global $wgUser, $wgLang, $wgTitle;
		global $wgServer, $wgScript;

		$dt = $wgLang->timeanddate( $ts );
		$del = wfMsg( "deleteimg" );
		$cur = wfMsg( "cur" );

		if ( $iscur ) {
			$url = wfImageUrl( $img );
			$rlink = $cur;
			if ( $wgUser->isSysop() ) {
				$dlink = "<a href=\"$wgServer$wgScript?image=" .
				  $wgTitle->getURL() . "&amp;action=delete\">{$del}</a>";
			} else {
				$dlink = $del;
			}
		} else {
			$url = wfImageArchiveUrl( $img );
			$rlink = $this->makeKnownLink( $wgTitle->getPrefixedText(),
			  wfMsg( "revertimg" ), "action=revert&amp;oldimage=" .
			  urlencode( $img ) );
			$dlink = $this->makeKnownLink( $wgTitle->getPrefixedText(),
			  $del, "action=delete&amp;oldimage=" . urlencode( $img ) );
		}
		if ( 0 == $u ) { $ul = $ut; }
		else { $ul = $this->makeLink( "User:{$ut}", $ut ); }

		$s = "<li> ({$dlink}) ({$rlink}) <a href=\"{$url}\">{$dt}</a> . . " .
		  "{$ut} ({$size} bytes)";

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
