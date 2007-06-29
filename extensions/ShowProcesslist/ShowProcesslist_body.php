<?php

class ShowProcesslistPage extends UnlistedSpecialPage {
	function ShowProcesslistPage() {
		UnlistedSpecialPage::UnlistedSpecialPage('ShowProcesslist');
	}

	function execute( $par ) {
		global $wgOut, $wgUser;
		
		$this->setHeaders();
		if ( !$wgUser->isAllowed( 'siteadmin' ) ) {
			$wgOut->permissionRequired( 'siteadmin' );
			return;
		}

		$dbr =& wfGetDB( DB_SLAVE );
		$res = $dbr->query( 'SHOW FULL PROCESSLIST' );
		$output = array();
		$output = '<table border="1" cellspacing="0">'."\n";
		$output .= '<tr><th>Id</th><th>User</th><th>Host</th><th>db</th><th>Command</th><th>Time</th><th>State</th><th>Info</th>'."\n";
		while ( $row = $dbr->fetchObject($res) ) {
			$output .= '<tr>';
			$fields = get_object_vars($row);
			foreach ($fields as $value ) {
				$output .= '<td>' . htmlspecialchars( $value ) . '</td>';
			}
			$output .= "</tr>\n";
		}
		$output .= '</table>';
		$wgOut->addHTML( $output );
	}
}


