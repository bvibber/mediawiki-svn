<?
# See skin.doc

class SkinCologneBlue extends Skin {

	function initPage()
	{
		global $wgOut, $wgStyleSheetPath;

		$wgOut->addLink( "stylesheet", "",
		  "{$wgStyleSheetPath}/cologneblue.css" );
	}

	function doBeforeContent()
	{
		global $wgUser, $wgOut, $wgTitle;

		$s = "";
		$qb = $this->qbSetting();
		if ( 0 != $qb ) { $s .= $this->quickBar(); }

		$s .= "\n<div id='content'>\n<div id='topbar'>" .
		  "<table width='98%' border=0 cellspacing=0 cellpadding=8><tr>";

		$s .= "<td class='top' align=left valign=center nowrap>";
		$s .= "<a href=\"" . wfLocalUrlE( wfMsg( "mainpage" ) ) . "\">";
		$s .= "<font color=white size='+5'><span id='sitetitle'>" .
		  wfMsg( "sitetitle" ) . "</span></font></a>";

		$s .= "</td><td class='top' align=right valign=bottom width='100%'>";
		$s .= $this->sysLinks();
		$s .= "</td></tr>" ;
		$s .= "<tr background=white><td>" ;

		$s .= "<font size='-1'><span id='sitesub'>";
		$s .= wfMsg( "sitesubtitle" ) . "</span></font>";
		$s .= "</td><td align=right>" ;

		$s .= "<font size='-1'><span id='langlinks'>" ;
		$s .= str_replace ( "<br>" , "" , $this->otherLanguages() );
		$s .= "</span></font>";

		$s .= "</td></tr></table>\n";

		$s .= "\n</div>\n<div id='article'>";

		$s .= $this->pageTitle();
		$s .= $this->pageSubtitle() . "\n<p>";
		return $s;
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
		$s .= "<td class='bottom' align=center valign=top>";

		$s .= $this->makeKnownLink( wfMsg( "mainpage" ), wfMsg( "mainpage" ) )
		  . " | " . $this->makeKnownLink( wfMsg( "aboutpage" ),
		  wfMsg( "aboutwikipedia" ) ) . " | " .
		  $this->searchForm();
		$s .= "\n<br>" . $this->pageStats();

		$s .= "</td>";
		if ( 2 == $qb ) { # Right
			$s .= $this->getQuickbarCompensator();
		}
		$s .= "</tr></table>\n</div>\n</div>\n";

		return $s;
	}
	function doGetUserStyles()
	{
		global $wgUser, $wgOut, $wgStyleSheetPath;

		$s = parent::doGetUserStyles();
		$qb = $this->qbSetting();

		if ( 2 == $qb ) { # Right
			$s .= "#quickbar { position: absolute; right: 4px; }\n" .
			  "#article { margin-left: 4px; margin-right: 148px; }\n";
		} else if ( 1 == $qb || 3 == $qb ) {
			$s .= "#quickbar { position: absolute; left: 4px; }\n" .
			  "#article { margin-left: 148px; margin-right: 4px; }\n";
		}
		return $s;
	}
	function sysLinks()
	{
		$s = "" .
		  $this->makeKnownLink( wfMsg( "mainpage" ), wfMsg( "mainpage" ) )
		  . " | " .
		  $this->makeKnownLink( wfMsg( "aboutpage" ), wfMsg( "about" ) )
		  . " | " .
		  $this->makeKnownLink( wfMsg( "helppage" ), wfMsg( "help" ) )
		  . " | " .
		  $this->makeKnownLink( wfMsg( "faqpage" ), wfMsg("faq") )
		  . " | " .
		  $this->specialLink( "specialpages" ) . " | " .
		  $this->specialLink( "userlogout" );
		return $s;
	}

	function quickBar()
	{
		global $wgOut, $wgTitle, $wgUser;

		$s = "\n<div id='quickbar'>";

		$sep = "<br>";
		$s .= $this->menuHead( "qbfind" );
		$s .= $this->searchForm();

		$s .= $this->menuHead( "qbbrowse" )
		  . $this->mainPageLink()
		  . $sep . $this->specialLink( "recentchanges" )
		  . $sep . $this->specialLink( "randompage" ) 
		  . $sep . $this->specialLink( "newpages" ) 
		  . $sep . $this->specialLink( "imagelist" ) 
		  . $sep . $this->specialLink( "statistics" ) 
		  . $sep . $this->specialLink( "specialpages" ) 
		  . $sep . $this->bugReportsLink()
		  . $sep . $this->makeKnownLink( wfMsg( "currentevents" ), "" )
		  . $sep; # . $this->dateLink() . $sep;

		if ( $wgOut->isArticle() ) {
			$s .= $this->menuHead( "qbedit" );
			$s .= "<strong>" . $this->editThisPage() . "</strong>"
			  . $sep . $this->makeKnownLink( wfMsg( "edithelppage" ),
			  wfMsg( "edithelp" ) )
			  . $sep . $this->specialLink( "upload" );

			if ( $wgUser->isSysop() ) {
				$s .= $sep . $this->deleteThisPage() .
				$sep . $this->protectThisPage() .
				$sep . $this->moveThisPage();
			}
			$s .= $sep;

			$s .= $this->menuHead( "qbpageoptions" );
			$s .= $this->talkLink()
			  . $sep . $this->printableLink();
			if ( 0 != $wgUser->getID() ) {
				$s .= $sep . $this->watchThisPage();
			}
			$s .= $sep;

			$s .= $this->menuHead( "qbpageinfo" )
			  . $this->historyLink()
			  . $sep . $this->whatLinksHere()
			  . $sep . $this->watchPageLinksLink();

			if ( Namespace::getIndex( "User" ) == $wgTitle->getNamespace() ) {
				$s .= $sep . $this->userContribsLink();
			}
			$s .= $sep;
		}
		$s .= $this->menuHead( "qbmyoptions" );
		if ( 0 != $wgUser->getID() ) {
			$name = $wgUser->getName();
			$s .= $this->makeKnownLink( "User:{$name}",
			  wfMsg( "mypage" ) )
			  . $sep . $this->specialLink( "watchlist" )
		  	  . $sep . $this->specialLink( "preferences" )
		  	  . $sep . $this->specialLink( "userlogout" );
		} else {
			$s .= $this->specialLink( "userlogin" );
		}
		$s .= $sep . "\n</div>\n";
		return $s;
	}

	function menuHead( $key )
	{
		$s = "\n<h6>" . wfMsg( $key ) . "</h6>";
		return $s;
	}

	function searchForm()
	{
		$s = "<form method=get action=\"" . wfLocalUrlE( "" ) . "\">"
		  . "<input type=text name=\"search\" size=10 value=\"\">"
		  . "<input type=submit value=\"" . wfMsg( "ok" ) . "\"></form>";

		return $s;
	}
}

?>
