<?php
 
class LastUserLogin extends SpecialPage {
 
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'LastUserLogin'/*class*/, 'lastlogin'/*restriction*/ );
	}
 
	/**
	 * Show the special page
	 *
	 * @param $par Mixed: parameter passed to the page or null
	 */
	public function execute( $par ) {
		global $wgUser, $wgOut, $wgLang;
		wfLoadExtensionMessages( 'LastUserLogin' );
 
		# If user is blocked, s/he doesn't need to access this page
		if ( $wgUser->isBlocked() ) {
			$wgOut->blockedPage();
			return;
		}
 
		# Show a message if the database is in read-only mode
		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}
 
		# If the user doesn't have the required 'lastlogin' permission, display an error
		if( !$wgUser->isAllowed( 'lastlogin' ) ) {
			$wgOut->permissionRequired( 'lastlogin' );
			return;
		}
 
		$this->setHeaders();
		$skin = $wgUser->getSkin();
 
		$wgOut->setPageTitle( wfMsg( 'lastuserlogin' ) );
 
		$dbr = wfGetDB( DB_SLAVE );
		$style = 'style="border:1px solid #000;text-align:left;"';
		$fields = array(
			'user_name' => 'lastuserlogin_userid',
			'user_real_name' => 'lastuserlogin_username',
			'user_email' => 'lastuserlogin_useremail',
			'user_touched' => 'lastuserlogin_lastlogin'
		);
 
		// Get order by and check it
		if( isset( $_REQUEST['order_by'] ) ){
			if( isset( $fields[$_REQUEST['order_by']] ) ){
				$orderby = $_REQUEST['order_by'];
			} else {
				$orderby = 'user_name';
			}
		} else {
			$orderby = 'user_name';
		}	   
 
		// Get order type and check it
		if( isset( $_REQUEST['order_type'] ) ){
			if( $_REQUEST['order_type'] == 'DESC' ){
				$ordertype = $_REQUEST['order_type'];
			} else {
				$ordertype = 'ASC';
			}
		} else {
			$ordertype = 'ASC';
		}	   
 
		$query = "SELECT user_name, user_real_name, user_email, user_touched FROM ".$dbr->tableName('user')." ORDER BY ".$orderby." ".$ordertype;
		$ordertype = $ordertype == 'ASC' ? 'DESC' : 'ASC';
 
		if( $result = $dbr->doQuery($query) ) {
			$out = '<table width="100%" cellpadding="3" '.$style.'><tr>';
 
			foreach( $fields as $key => $value ){
				$out .= '<th '.$style.'><a href="?order_by='.$key.'&order_type='.$ordertype.'">'.wfMsg( $value ).'</a></th>';
			}
 
			$out .= "<th $style>".wfMsg( 'lastuserlogin_daysago' )."</th>";
			$out .= '</tr>';
 
			while( $row = $dbr->fetchRow($result) ) {
				$out .= '<tr>';
					foreach( $fields as $key => $value ){
 
						if( $key == 'user_touched' ) {
							$style = 'style="border:1px solid #000"';
							$out .= "<td $style>".$wgLang->timeanddate( wfTimestamp( TS_MW, $row[$key] ), true ).
									'</td><td style="border: 1px solid #000; text-align:right;">'.
									$wgLang->formatNum( round( ( mktime() - wfTimestamp( TS_UNIX, $row[$key] ) ) /3600/24, 2 ), 2 )."</td>";
						} else {
							if( $key == 'user_name' ) {
								$userPage = Title::makeTitle( NS_USER, htmlspecialchars( $row[$key] ) );
								$name = $skin->makeLinkObj( $userPage, htmlspecialchars( $userPage->getText() ) );
								$out .= '<td '.$style.'>'.$name.'</a></td>';
							} else { 
								$out .= '<td '.$style.'>'.htmlspecialchars($row[$key]).'&nbsp;</td>';
							}
						}
					}
				$out .= '</tr>';
			}
		}
 
	$out .= '</table>';
	$wgOut->addHTML( $out );
 
	}
}

