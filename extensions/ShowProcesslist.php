<?php
# Not a valid entry point, skip unless MEDIAWIKI is defined
if (defined('MEDIAWIKI')) {

$wgExtensionFunctions[] = 'wfShowProcesslist';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'ShowProcesslist',
	'description' => 'display the output of SHOW FULL PROCESSLIST'
);

function wfShowProcesslist() {
global $IP;
require_once( $IP.'/includes/SpecialPage.php' );

class ShowProcesslistPage extends UnlistedSpecialPage {
	function ShowProcesslistPage() {
		UnlistedSpecialPage::UnlistedSpecialPage('ShowProcesslist');
	}

	function execute( $par ) {
		global $wgRequest, $wgOut, $wgTitle, $wgUser;
		
		$this->setHeaders();
		if ( !$wgUser->isDeveloper() ) {
			$wgOut->addWikiText( 'You\'re not allowed, go away' );
			return;
		}

		$res = wfQuery( 'SHOW FULL PROCESSLIST' , DB_READ );
		$output = array();
		$output = '<table border="1" cellspacing="0">'."\n";
		$output .= '<tr><th>Id</th><th>User</th><th>Host</th><th>db</th><th>Command</th><th>Time</th><th>State</th><th>Info</th>'."\n";
		while ( $row = wfFetchObject($res) ) {
			$output .= '<tr>';
			$fields = get_object_vars($row);
			foreach ($fields as $name => $value ) {
				$output .= '<td>' . htmlspecialchars( $value ) . '</td>';
			}
			$output .= "</tr>\n";
		}
		$output .= '</table>';
		$wgOut->addHTML( $output );
	}
}

SpecialPage::addPage( new ShowProcesslistPage );

} # End of extension function
} # End of invocation guard
?>
