<?
# See skin.doc

# These are the INTERNAL names, which get mapped
# directly to class names.  For display purposes, the
# Language class has internationalized names
#
/* private */ $wgValidSkinNames = array(
	"Standard", "Nostalgia", "CologneBlue"
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

	function qbSetting()
	{
		global $wgOut, $wgUser;

		if ( $wgOut->isQuickbarSupressed() ) { return 0; }
		$q = $wgUser->getOption( "quickbar" );
		if ( "" == $q ) { $q = 0; }
		return $q;
	}

	function initPage()
	{
		global $wgOut, $wgStyleSheetPath;

		if ( $wgOut->isPrintable() ) { $ss = "wikiprintable.css"; }
		else { $ss = $this->getStylesheet(); }
		$wgOut->addLink( "stylesheet", "", "{$wgStyleSheetPath}/{$ss}" );
	}

	function getHeadScripts() { return ""; }

	function getUserStyles()
	{
		$s = "<style type='text/css' media='screen'><!--\n";
		$s .= $this->doGetUserStyles();
		$s .= "//--></style>\n";
		return $s;
	}

	function doGetUserStyles()
	{
		global $wgUser;

		$s = "";
		if ( 1 == $wgUser->getOption( "underline" ) ) {
			$s .= "a.new, a.internal, a.external { " .
			  "text-decoration: underline; }\n";
		} else {
			$s .= "a.new, a.internal, a.external { " .
			  "text-decoration: none; }\n";
		}
		if ( 1 == $wgUser->getOption( "highlightbroken" ) ) {
			$s .= "a.new { color: #880000; }\n";
		}
		if ( 1 == $wgUser->getOption( "justify" ) ) {
			$s .= "#article { text-align: justify; }\n";
		}
		return $s;
	}

	function getBodyOptions()
	{
		global $wgUser, $wgTitle, $wgNamespaceBackgrounds;

		if ( 0 != $wgTitle->getNamespace() ) {
			$a = array( "bgcolor" => "#FFFFDD" );
		}
		else $a = array( "bgcolor" => "#FFFFFF" );
		return $a;
	}

	function getExternalLinkAttributes( $link, $text )
	{
		global $wgUser, $wgOut;

		$link = urldecode( $link );
		$link = str_replace( "_", " ", $link );

		if ( $wgOut->isPrintable() ) { $r = " class='printable'"; }
		else { $r = " class='external'"; }

		if ( 1 == $wgUser->getOption( "hover" ) ) {
			$r .= " title=\"{$link}\"";
		}
		return $r;
	}

	function getInternalLinkAttributes( $link, $text, $broken = false )
	{
		global $wgUser, $wgOut;

		$link = urldecode( $link );
		$link = str_replace( "_", " ", $link );

		if ( $wgOut->isPrintable() ) { $r = " class='printable'"; }
		else if ( $broken ) { $r = " class='new'"; }
		else { $r = " class='internal'"; }

		if ( 1 == $wgUser->getOption( "hover" ) ) {
			$r .= " title=\"{$link}\"";
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
			$s = $this->pageTitle() . $this->pageSubtitle() . "\n";
			$s .= "\n<div class='bodytext'>";
			return $s;
		}
		return $this->doBeforeContent();
	}

	function doBeforeContent()
	{
		global $wgUser, $wgOut, $wgTitle;

		$s = "";
		$qb = $this->qbSetting();
		if ( 0 != $qb ) { $s .= $this->quickBar(); }

		$s .= "\n<div id='content'>\n<div id='topbar'>" .
		  "<table width='98%' border=0 cellspacing=0><tr>";

		if ( 0 == $qb ) {
			$s .= "<td class='top' align=left valign=top>" .
			  $this->logoText() . "</td>";
		} else if ( 1 == $qb || 3 == $qb ) { # Left
			$s .= $this->getQuickbarCompensator();
		}
		$s .= "<td class='top' align=left valign=top>";

		$s .= $this->topLinks() ; # . "\n<br>";
		$s .= $this->pageTitleLinks();

		$s .= "</td>\n<td class='top' valign=top align=right width=200 nowrap>";
		$s .= $this->nameAndLogin();
		$s .= "\n<br>" . $this->searchForm() . "</td>";

		if ( 2 == $qb ) { # Right
			$s .= $this->getQuickbarCompensator();
		}
		$s .= "</tr></table>\n</div>\n";
		$s .= "\n<div id='article'>";

		$s .= $this->pageTitle();
		$s .= $this->pageSubtitle() . "\n<p>";
		return $s;
	}

	function getQuickbarCompensator()
	{
		return "<td width='152'>&nbsp;</td>";
	}

	# This gets called immediately before the </body> tag.
	#
	function afterContent()
	{
		global $wgUser, $wgOut, $wgServer, $HTTP_SERVER_VARS;

		if ( $wgOut->isPrintable() ) {
			$s = "\n</div>\n";

			$u = $wgServer . $HTTP_SERVER_VARS['REQUEST_URI'];
			$rf = str_replace( "$1", $u, wfMsg( "retrievedfrom" ) );

			$s .= "<p><em>{$rf}</em>\n";
			return $s;
		}
		return $this->doAfterContent();
	}

	function doAfterContent()
	{
		global $wgUser, $wgOut;

		$s = "\n</div><br clear=all>\n";

		$s .= "\n<div id='footer'>";
		$s .= "<table width='98%' border=0 cellspacing=0><tr>";

		$qb = $this->qbSetting();
		if ( 1 == $qb || 3 == $qb ) { # Left
			$s .= $this->getQuickbarCompensator();
		}
		$s .= "<td class='bottom' align=left valign=top>";

		$s .= $this->bottomLinks();
		$s .= "\n<br>" . $this->pageStats();
		$s .= "\n<br>" . $this->searchForm();

		$s .= "</td>";
		if ( 2 == $qb ) { # Right
			$s .= $this->getQuickbarCompensator();
		}
		$s .= "</tr></table>\n</div>\n</div>\n";

		return $s;
	}

	function pageTitleLinks()
	{
		global $wgOut, $wgTitle, $oldid, $action;

		$s = "<p class='subtitle'>" . $this->printableLink();

		if ( $wgOut->isArticle() ) {
			if ( $oldid ) {
				$s .= " | " . $this->makeKnownLink( $wgTitle->getPrefixedText(),
				  wfMsg( "currentrev" ) );
			}
			if ( $wgTitle->getNamespace() == Namespace::getIndex( "Image" ) ) {
				$name = $wgTitle->getDBkey();
				$s .= " | <a href=\"" . wfImageUrl( $name ) . "\">{$name}</a>";
			}
			$s .= "<br>" . $this->otherLanguages();
		}
		if ( "history" == $action ) {
			$s .= " | " . $this->makeKnownLink( $wgTitle->getPrefixedText(),
			  wfMsg( "currentrev" ) );
		}
		return $s;
	}

	function printableLink()
	{
		global $wgOut, $wgTitle, $oldid, $action;

		if ( "history" == $action ) { $q = "action=history&"; }
		else { $q = ""; }

		$s = $this->makeKnownLink( $wgTitle->getPrefixedText(),
		  WfMsg( "printableversion" ), "{$q}printable=yes" );
		return $s;
	}

	function pageTitle()
	{
		global $wgOut, $wgTitle;

		$s = "<h1 class='pagetitle'>" . $wgOut->getPageTitle() . "</h1>";
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
		$s = "<form method=get action=\"" . wfLocalUrl( "" ) . "\">"
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
		$s .= $sep . $this->specialPagesList();

		return $s;
	}

	function bottomLinks()
	{ 
		global $wgOut, $wgUser;
		$sep = " |\n";

		$s = $this->mainPageLink() . $sep
		  . $this->specialLink( "recentchanges" );

		if ( $wgOut->isArticle() ) {
			$s .=  $sep . $this->editThisPage()
			  . $sep . $this->historyLink();
		}
		if ( $wgOut->isArticle() ) {
			$s .= $sep . $this->talkLink();

			if ( $wgUser->isSysop() ) {
				$s .= $sep . $this->deleteThisPage() .
				  $sep . $this->protectThisPage() .
				  $sep . $this->moveThisPage();
			}
		}
		$s .= "<br>" . $this->otherLanguages();
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

		$d = $wgLang->timeanddate( $wgArticle->getTimestamp() ) .
		  " (" . date( "T" ) . ")";
		$s .= " " . str_replace( "$1", $d, wfMsg( "lastmodified" ) );
		return "<span id='pagestats'>{$s}</span>";
	}

	function logoText( $align = "" )
	{
		if ( "" != $align ) { $a = " align='{$align}'"; }
		else { $a = ""; }

		$mp = wfMsg( "mainpage" );
		$s = "<a href=\"" . wfLocalUrl( $mp ) . "\"><img{$a} border=0 src=\"" .
		  $this->getLogo() . "\" alt=\"" . "[{$mp}]\"></a>";
		return $s;
	}

	function quickBar()
	{
		global $wgOut, $wgTitle, $wgUser;

		$s = "\n<div id='quickbar'>";
		$s .= "\n" . $this->logoText() . "\n<hr>";

		$sep = "\n<br>";
		$s .= $this->mainPageLink()
		  . $sep . $this->specialLink( "recentchanges" )
		  . $sep . $this->specialLink( "randompage" ) 
		  . $sep . $this->specialLink( "watchlist" )
		  . $sep . $this->makeKnownLink( wfMsg( "currentevents" ), "" )
		  # . $sep . $this->dateLink()
		  . "\n<hr>";

		if ( $wgOut->isArticle() ) {
			$s .= "<strong>" . $this->editThisPage() . "</strong>";
			if ( 0 != $wgUser->getID() ) {
				$s .= $sep . $this->watchThisPage();
			}
			if ( $wgUser->isSysop() ) {
				$s .= $sep . $this->deleteThisPage() .
				$sep . $this->protectThisPage() .
				$sep . $this->moveThisPage();
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
		  . $sep . $this->bugReportsLink()
		  . $sep . $this->specialLink( "specialpages" );

		$s .= "\n</div>\n";
		return $s;
	}

	function specialPagesList()
	{
		global $wgUser, $wgOut, $wgLang, $wgServer, $wgRedirectScript;
		$a = array ();

		$validSP = $wgLang->getValidSpecialPages();

		foreach ( $validSP as $name => $desc ) {
			if ( "" == $desc ) { continue; }
			$a[$name] = $desc;
		}
		if ( $wgUser->isSysop() )
		{ 
			$sysopSP = $wgLang->getSysopSpecialPages();

			foreach ( $sysopSP as $name => $desc ) {
				if ( "" == $desc ) { continue; }
				$a[$name] = $desc ;
			}
		}
		if ( $wgUser->isDeveloper() )
		{ 
			$devSP = $wgLang->getDeveloperSpecialPages();

			foreach ( $devSP as $name => $desc ) {
				if ( "" == $desc ) { continue; }
				$a[$name] = $desc ;
			}
		}
		$go = wfMsg( "go" );
		$sp = wfMsg( "specialpages" );

		$s = "<form method=get id='specialform' " .
		  "action=\"{$wgServer}{$wgRedirectScript}\">\n";
		$s .= "<select name='wpDropdown'>\n";
		$s .= "<option value=\"Special:Specialpages\">{$sp}</option>\n";

		foreach ( $a as $name => $desc ) {
			$s .= "<option value=\"Special:{$name}\">{$desc}</option>\n";
		}
		$s .= "</select>\n";
		$s .= "<input type=submit value=\"{$go}\" name=redirect>\n";
		$s .= "</form>\n";
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

			if ( $redirect ) { $red = "&redirect={$redirect}"; }
			if ( $oldid && ! isset( $diff ) ) {
				$oid = "&oldid={$oldid}";
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
			$s = wfMsg( "error" );
		}
		return $s;
	}

	function protectThisPage()
	{
		global $wgUser, $wgOut, $wgTitle, $diff;

		if ( $wgOut->isArticle() && ( ! $diff ) && $wgUser->isSysop() ) {
			$n = $wgTitle->getPrefixedText();

			if ( $wgTitle->isProtected() ) {
				$t = wfMsg( "unprotectthispage" );
				$q = "action=unprotect";
			} else {
				$t = wfMsg( "protectthispage" );
				$q = "action=protect";
			}
			$s = $this->makeKnownLink( $n, $t, $q );
		} else {
			$s = wfMsg( "error" );
		}
		return $s;
	}

	function watchThisPage()
	{
		global $wgUser, $wgOut, $wgTitle, $diff;

		if ( $wgOut->isArticle() && ( ! $diff ) ) {
			$n = $wgTitle->getPrefixedText();

			if ( $wgTitle->userIsWatching() ) {
				$t = wfMsg( "unwatchthispage" );
				$q = "action=unwatch";
			} else {
				$t = wfMsg( "watchthispage" );
				$q = "action=watch";
			}
			$s = $this->makeKnownLink( $n, $t, $q );
		} else {
			$s = wfMsg( "notanarticle" );
		}
		return $s;
	}

	function moveThisPage()
	{
		global $wgTitle;

		$s = $this->makeKnownLink( "Special:Movepage",
		  wfMsg( "movethispage" ), "target=" . $wgTitle->getPrefixedURL() );
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

		$s = wfMsg( "otherlanguages" ) . ": ";
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

		$nt = Title::newFromText( $title );

		if ( $nt->isExternal() ) {
			$u = $nt->getFullURL();
			if ( "" == $text ) { $text = $nt->getPrefixedText(); }
			$style = $this->getExternalLinkAttributes( $link, $text );

			$inside = "";
			if ( "" != $trail ) {
				if ( preg_match( "/^([a-z]+)(.*)$$/sD", $trail, $m ) ) {
					$inside = $m[1];
					$trail = $m[2];
				}
			}
			return "<a href=\"$u\"$style>$text$inside</a>$trail";
		}
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
		global $wgOut, $wgTitle;

		$nt = Title::newFromText( $title );
		$link = $nt->getPrefixedURL();

		if ( "" == $link ) {
			$u = "";
			if ( "" == $text ) { $text = $nt->getFragment(); }
		} else {
			$u = wfLocalUrlE( $link, $query );
		}
		if ( "" != $nt->getFragment() ) {
			$u .= "#" . wfEscapeHTML( $nt->getFragment() );
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
		$r = "<a href=\"{$u}\"{$style}>{$text}{$inside}</a>{$trail}";
		return $r;
	}

	function makeBrokenLink( $title, $text = "" )
	{
		global $wgOut, $wgUser;

		$nt = Title::newFromText( $title );
		$link = $nt->getEditURL();
		$u = wfEscapeHTML( $link );

		if ( "" == $text ) { $text = $nt->getPrefixedText(); }
		$style = $this->getInternalLinkAttributes( $link, $text, true );

		if ( $wgOut->isPrintable() ||
		  ( 1 == $wgUser->getOption( "highlightbroken" ) ) ) {
			$s = "<a href=\"{$u}\"{$style}>{$text}</a>";
		} else {
			$s = "{$text}<a href=\"{$u}\"{$style}>?</a>";
		}
		return $s;
	}

	function fnamePart( $url )
	{
		$basename = strrchr( $url, "/" );
		if ( false === $basename ) { $basename = $url; }
		else { $basename = substr( $basename, 1 ); }
		return wfEscapeHTML( $basename );
	}

	function makeImage( $url, $alt = "" )
	{
		global $wgOut;

		if ( "" == $alt ) { $alt = $this->fnamePart( $url ); }
		$s = "<img src=\"{$url}\" alt=\"{$alt}\">";
		return $s;
	}

	function makeImageLink( $name, $url, $alt = "" )
	{
		global $wgOut, $wgTitle;

		$nt = Title::newFromText( "Image:{$name}" );
		$link = $nt->getPrefixedURL();
		if ( "" == $alt ) { $alt = $name; }

		$u = wfLocalUrlE( $link );
		$s = "<a href=\"{$u}\" class='image' title=\"{$alt}\">" .
		  "<img border=0 src=\"{$url}\" alt=\"{$alt}\"></a>";
		return $s;
	}

	function makeMediaLink( $name, $url, $alt = "" )
	{
		global $wgOut, $wgTitle;

		if ( "" == $alt ) { $alt = $name; }
		$u = wfEscapeHTML( $url );
		$s = "<a href=\"{$u}\" class='media' title=\"{$alt}\">{$alt}</a>";
		return $s;
	}

	function specialLink( $name, $key = "" )
	{
		if ( "" == $key ) { $key = strtolower( $name ); }
		$pn = ucfirst( $name );
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
			  $artname, $last, "diff=\\1&oldid={$oid}" ), $this->lastline );
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
			  "diff=0&oldid={$oid}" );
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

	function recentChangesLine( $ts, $u, $ut, $ns, $ttl, $c, $isminor, $isnew )
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

		if ( 0 != $wgUser->getID() ) {
			$nt = Title::newFromText( $t );
			if ( $nt->userIsWatching() ) {
				$clink = "<strong>{$clink}</strong>";
			}
		}
		$hlink = $this->makeKnownLink( $t, wfMsg( "hist" ), "action=history" );
		if ( $isnew ) { $dlink = wfMsg( "diff" ); }
		else { $dlink = $this->makeKnownLink( $t, wfMsg( "diff" ), "diff=0&oldid=0" ); }

		if ( 0 == $u ) { $ul = $ut; }
		else { $ul = $this->makeLink( "User:{$ut}", $ut ); }
		$cr = wfMsg( "currentrev" );

		$s .= "<li> ({$dlink}) ({$hlink}) . .";
		if ( $isminor ) { $s .= " <strong>M</strong>"; }
		if ( $isnew ) { $s .= "<strong>N</strong>"; }
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

		$dt = $wgLang->timeanddate( $ts );
		$del = wfMsg( "deleteimg" );
		$cur = wfMsg( "cur" );

		if ( $iscur ) {
			$url = wfImageUrl( $img );
			$rlink = $cur;
			if ( $wgUser->isSysop() ) {
				$dlink = "<a href=\"" . wfLocalUrlE( "", "image=" .
				  $wgTitle->getURL() . "&action=delete" ) . "\">{$del}</a>";
			} else {
				$dlink = $del;
			}
		} else {
			$url = wfImageArchiveUrl( $img );
			$rlink = $this->makeKnownLink( $wgTitle->getPrefixedText(),
			  wfMsg( "revertimg" ), "action=revert&oldimage=" .
			  urlencode( $img ) );
			$dlink = $this->makeKnownLink( $wgTitle->getPrefixedText(),
			  $del, "action=delete&oldimage=" . urlencode( $img ) );
		}
		if ( 0 == $u ) { $ul = $ut; }
		else { $ul = $this->makeLink( "User:{$ut}", $ut ); }

		$nb = str_replace( "$1", $size, wfMsg( "nbytes" ) );
		$s = "<li> ({$dlink}) ({$rlink}) <a href=\"{$url}\">{$dt}</a> . . " .
		  "{$ul} ({$nb})";

		if ( "" != $c && "*" != $c ) { $s .= " <em>({$c})</em>"; }
		$s .= "</li>\n";
		return $s;
	}
}

include_once( "SkinStandard.php" );
include_once( "SkinNostalgia.php" );
include_once( "SkinCologneBlue.php" );

?>
