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

		$s .= "<td class='top' align=left valign=center>";
		$s .= "<font color=white size='+5'><span id='sitetitle'>" .
		  wfMsg( "sitetitle" ) . "</span></font>";
		$s .= "<br><font color=white size='-1'><span id='sitesub'>";
		$s .= wfMsg( "sitesubtitle" ) . "</span></font>";

		$s .= "</td><td class='top' align=right valign=bottom>";
		$s .= $this->sysLinks();
		$s .= "</td></tr></table>\n";

		$s .= "\n</div>\n<div id='article'>";

		$s .= $this->pageTitle();
		$s .= $this->pageSubtitle() . "\n<p>";
		return $s;
	}

	function doGetUserStyles()
	{
		global $wgUser, $wgOut, $wgStyleSheetPath;

		$s = parent::doGetUserStyles();
		$qb = $this->qbSetting();

		if ( 2 == $qb ) { # Right
			$s .= "#quickbar { position: absolute; right: 4px; }\n" .
			  "#article { margin-left: 4px; margin-right: 132px; }\n";
		} else if ( 1 == $qb || 3 == $qb ) {
			$s .= "#quickbar { position: absolute; left: 4px; }\n" .
			  "#article { margin-left: 132px; margin-right: 4px; }\n";
		}
		return $s;
	}
	function sysLinks()
	{
		$s = "HOME | ABOUT | HELP | FAQ | SPECIAL PAGES | LOG OUT";
		return $s;
	}

	function quickBar()
	{
		global $wgOut, $wgTitle, $wgUser;

		$mp = wfMsg( "mainpage" );
		$s = "\n<div id='quickbar'>";

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
}

?>
