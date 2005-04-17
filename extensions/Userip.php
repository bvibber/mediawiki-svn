<?php
/**
 * A Special:Userip extension, useful for wiki-espionage
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 */

if (defined('MEDIAWIKI')) {

$wgExtensionFunctions[] = 'wfUserip';

function wfUserip() {
	global $IP;
	
	require_once( "$IP/includes/SpecialPage.php" );

	class Userip extends SpecialPage {
		function Userip() {
			SpecialPage::SpecialPage('Userip', 'block');
		}

		function execute( $par ) {
			global $wgRequest, $wgOut, $wgTitle, $wgLang, $wgPutIPinRC, $wgUser;
			
			if ( ! $wgUser->isAllowed('block') ) {
				$wgOut->sysopRequired();
				return;
			}
			
			if ( ! $wgPutIPinRC ) {
				$wgOut->fatalError('You must set $wgPutIPinRC to true in LocalSettings.php in order for this page to work.');
				return;
			}

			$this->setHeaders();

			$action = $wgTitle->escapeLocalUrl();
			$username = $wgRequest->getText( 'user' );
			
			$wgOut->addHTML( "
<form id='userip' method='post' action=\"$action\">
	<table border='0'>
		<tr>
			<td align='right'>" . wfMsg('specialloguserlabel') . "</td>
			<td align='left'>
				<input tabindex='1' type='text' size='20' name='user' value=\"" . htmlspecialchars($username) . "\" />
			</td>
		</tr>
		<tr>
			<td align='right'>&nbsp;</td>
			<td align='left'>
				<input type='submit' name='submit' value=\"" . wfMsg('go') . "\" />
			</td>
		</tr>
	</table>
</form>");
			if ( !is_null( $username ) && $username !== 0 ) {
				$dbr =& wfGetDB( DB_READ );
				
				$recentchanges = $dbr->tableName( 'recentchanges' );
				$sql = "SELECT rc_ip,rc_timestamp FROM $recentchanges
					WHERE rc_user_text = " . $dbr->addQuotes( $username ) .
					"AND rc_ip != '' GROUP BY rc_ip";
#					"AND rc_ip != ''";

				$res = $dbr->query( $sql, 'wfUserip' );

				if (mysql_num_rows($res) > 0 ) {
					$skin = $wgUser->getSkin();
					
					$wgOut->addHTML( '<hr /><ul>' );
					#$wgOut->addHTML( User::idFromName( $username ) );
					while ( $row = $dbr->fetchObject( $res ) ) {
						$time = $wgLang->timeanddate( $row->rc_timestamp );
						$link = $skin->makeKnownLinkObj(
							Title::makeTitle( NS_SPECIAL, 'Contributions' ),
							$row->rc_ip,
							'target=' . $row->rc_ip
						);
						$wgOut->addHTML( "<li>$time: $link</li>");
					}
					$wgOut->addHTML( '</ul>' );
				}
			}
		}
	}

	global $wgMessageCache;
	SpecialPage::addPage( new Userip );
	$wgMessageCache->addMessage( "userip", "User ip" );

	} # End of extension function
} # End of invocation guard
?>
