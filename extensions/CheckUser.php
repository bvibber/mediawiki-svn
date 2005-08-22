<?php


# Not a valid entry point, skip unless MEDIAWIKI is defined
if (defined('MEDIAWIKI')) {
$wgExtensionFunctions[] = "wfCheckUser";

$wgAvailableRights[] = 'checkuser';
$wgGroupPermissions['checkuser']['checkuser'] = true;

$wgCheckUserLog = "/home/wikipedia/logs/checkuser.log";

function wfCheckUser() {
global $IP;
require_once( "$IP/includes/SpecialPage.php" );
require_once( "$IP/includes/ChangesList.php" );

class CheckUser extends UnlistedSpecialPage
{
	function CheckUser() {
		UnlistedSpecialPage::UnlistedSpecialPage("CheckUser");
	}

	function execute( $par ) {
		global $wgRequest, $wgOut, $wgTitle, $wgUser;
		
		if ( !in_array( 'checkuser', $wgUser->getRights() ) ) {
			$wgOut->setArticleRelated( false );
			$wgOut->setRobotpolicy( "noindex,follow" );
			$wgOut->errorpage( "nosuchspecialpage", "nospecialpagetext" );
			return;
		}

		$this->setHeaders();

		$ip = $wgRequest->getText( 'ip' );
		$user = $wgRequest->getText( 'user' );
		$subip = $wgRequest->getBool( 'subip' );
		$subuser = $wgRequest->getBool( 'subuser' );

		$this->doTop( $ip, $user );
		if ( $subip ) {
			$this->doIPRequest( $ip );
		} else if ( $subuser ) {
			$this->doUserRequest( $user );
		} else {
			$this->showLog();
		}
	}

	function doTop( $ip, $user ) {
		global $wgOut, $wgTitle;

		$action = $wgTitle->escapeLocalUrl();

		$wgOut->addHTML( <<<EOT
<form name="checkuser" action="$action" method=post>
<table border=0 cellpadding=5><tr><td>
	IP: 
</td><td>
	<input type="text" name="ip" value="$ip" width=50 /> <input type="submit" name="subip" value="OK" />
</td></tr><tr><td>
	User:
</td><td>
	<input type="text" name="user" value="$user" width=50 /> <input type="submit" name="subuser" value="OK" />
</td></tr></table>
EOT
		);
	}

	function doIPRequest( $ip ) {
		global $wgUser, $wgOut, $wgLang;
		$fname = "CheckUser::doIPRequest";

		if ( !$this->addLogEntry( $wgLang->timeanddate( wfTimestampNow() ) . ' ' .
		  $wgUser->getName() . " got edits for " . htmlspecialchars( $ip ) )) 
		{
			$wgOut->addHTML( "<p>Unable to add log entry</p>" );
		}

		$dbr =& wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'recentchanges', array( '*' ), array( 'rc_ip' => $ip ), $fname );
		if ( !$dbr->numRows( $res ) ) {
			$s =  "No results\n";
		} else {
			$sk = $wgUser->getSkin();
			$list = new ChangesList( $sk );
			$s = $list->beginRecentChangesList();
			$counter = 1;
			while ( ($row = $dbr->fetchObject( $res ) ) != false ) {
				$rc = RecentChange::newFromRow( $row );
				$rc->counter = $counter++;
				$s .= $list->recentChangesLine( $rc, false );
			}
			$s .= $list->endRecentChangesList();
		}
		$wgOut->addHTML( $s );
		$dbr->freeResult( $res );
	}

	function doUserRequest( $user ) {
		global $wgOut, $wgTitle, $wgLang, $wgUser;
		$fname = "CheckUser::doUserRequest";

		if ( !$this->addLogEntry( $wgLang->timeanddate( wfTimestampNow() ) . ' ' .
		  $wgUser->getName() . " got IPs for " . htmlspecialchars( $user ) ) ) 
		{
			$wgOut->addHTML( "<p>Unable to add log entry</p>" );
		}

		$dbr =& wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'recentchanges', array( 'DISTINCT rc_ip' ), array( 'rc_user_text' => $user ), $fname );
		if ( !$dbr->numRows( $res ) ) {
			$s =  "No results\n";
		} else {
			$s = '<ul>';
			while ( ($row = $dbr->fetchObject( $res ) ) != false ) {
				$s .= '<li><a href="' . $wgTitle->escapeLocalURL( 'ip=' . urlencode( $row->rc_ip ) ) . '">' .
					htmlspecialchars( $row->rc_ip ) . '</a></li>';
			}
			$s .= '</ul>';
		}
		$wgOut->addHTML( $s );
	}

	function showLog() {
		global $wgOut, $wgCheckUserLog;

		$wgOut->addHTML( '<ul>' . file_get_contents( $wgCheckUserLog ) . '</ul>' );
	}

	function addLogEntry( $entry ) {
		global $wgUser, $wgCheckUserLog;

		$f = fopen( $wgCheckUserLog, 'a' );
		if ( !$f ) {
			return false;
		}
		if ( !fwrite( $f, "<li>$entry</li>\n" ) ) {
			return false;
		}
		fclose( $f );
		return true;
	}
}

global $wgMessageCache;
SpecialPage::addPage( new CheckUser );
$wgMessageCache->addMessage( "checkuser", "Check user" );

} # End of extension function
} # End of invocation guard
?>
