<?php

# This is a simple example of a special page module
# Given a string in UTF-8, it converts it to HTML entities suitable for 
# an ISO 8859-1 web page.

$wgExtensionFunctions[] = "wfFindSpam";

function wfFindSpam() {

require_once( "SpecialPage.php" );

class FindSpamPage extends SpecialPage
{
	function FindSpamPage() {
		SpecialPage::SpecialPage("FindSpam", "sysop");
	}

	function execute( $par ) {
		global $wgRequest, $wgOut, $wgTitle, $wgLocalDatabases, $wgUser;
		global $conf, $wgCanonicalNamespaceNames, $wgLang;

		$this->setHeaders();
		if ( !$this->userCanExecute( $wgUser ) ) {
			$this->displayRestrictionError();
			return;
		}

		$ip = trim( $wgRequest->getText( 'ip' ) );
		$encQ = htmlspecialchars( $ip );
		$action = $wgTitle->getLocalUrl();
		$ok = wfMsg( "ok" );

		$wgOut->addHTML( "
<form name=ucf method=post action=\"$action\">
<label> IP: 
<input type=text width=50 name=ip value=\"$encQ\" />
</label>
<input type=submit name=submit value=\"$ok\"><br /><br />
</form>" );

		if ( $ip ) {
			$dbr =& wfGetDB( DB_READ );
			$s  = '';

			foreach ( $wgLocalDatabases as $db ) {
				$sql = "SELECT rc_namespace,rc_title,rc_timestamp,rc_user_text,rc_last_oldid FROM $db.recentchanges WHERE rc_ip='" . wfStrencode( $ip ) . 
				  "' AND rc_this_oldid=0";
				$res = $dbr->query( $sql, "findspam.php" );
				list( $site, $lang ) = $conf->siteFromDB( $db );
				if ( $lang == 'meta' ) {
					$baseUrl = "http://meta.wikimedia.org";
				} else {
					$baseUrl = "http://$lang.$site.org";
				}

				if ( $dbr->numRows( $res ) ) {
					$s .= "\n$db\n";
					while ( $row = $dbr->fetchObject( $res ) ) {
								
						if ( $row->rc_namespace == 0 ){
							$title = $row->rc_title;
						} else {
							$title = $wgCanonicalNamespaceNames[$row->rc_namespace] . ':' .$row->rc_title;
						}
						$encTitle = urlencode( $title );
						$url = "$baseUrl/wiki/$encTitle";
						$user = urlencode( $row->rc_user_text );
						#$rollbackText = wfMsg( 'rollback' );
						$diffText = wfMsg( 'diff' );
						#$rollbackUrl = "$baseUrl/w/wiki.phtml?title=$encTitle&action=rollback&from=$user";
						$diffUrl = "$baseUrl/w/wiki.phtml?title=$encTitle&diff=0&oldid=0";
						if ( $row->rc_last_oldid ) {
							$lastLink = "[$baseUrl/w/wiki.phtml?title=$encTitle&oldid={$row->rc_last_oldid}&action=edit last]";
						}

						$date = $wgLang->timeanddate( $row->rc_timestamp );
						#$s .= "* $date [$url $title] ([$rollbackUrl $rollbackText] | [$diffUrl $diffText])\n";
						$s .= "* $date [$url $title] ($lastLink | [$diffUrl $diffText])\n";
					}
				}
			}
			if ( $s == '' ) {
				$s = "No contributions found\n";
			}
			$wgOut->addWikiText( $s );
		}
	}
}

global $wgMessageCache;
SpecialPage::addPage( new FindSpamPage );
$wgMessageCache->addMessage( "findspam", "Find spam" );

} # End of extension function
?>
