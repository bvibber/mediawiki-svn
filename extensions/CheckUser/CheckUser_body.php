<?php

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "CheckUser extension\n";
    exit( 1 );
}

# Add messages
global $wgMessageCache, $wgCheckUserMessages;
foreach( $wgCheckUserMessages as $key => $value ) {
	$wgMessageCache->addMessages( $wgCheckUserMessages[$key], $key );
}

class CheckUser extends SpecialPage
{
	function CheckUser() {
		SpecialPage::SpecialPage('CheckUser', 'checkuser');
	}

	function execute( $par ) {
		global $wgRequest, $wgOut, $wgTitle, $wgUser;
		
		if( !$wgUser->isAllowed( 'checkuser' ) ) {
			$wgOut->permissionRequired( 'checkuser' );
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
		$encIp = htmlspecialchars( $ip );
		$encUser = htmlspecialchars( $user );

		$wgOut->addHTML( <<<EOT
<table border=0 cellpadding=5>
<form name="checkuser" action="$action" method=post>
<tr><td>
	IP: 
</td><td>
	<input type="text" name="ip" value="$encIp" width=50 /> <input type="submit" name="subip" value="OK" />
</td></tr>
</form>

<form name="checkuser" action="$action" method=post>
<tr><td>
	User:
</td><td>
	<input type="text" name="user" value="$encUser" width=50 /> <input type="submit" name="subuser" value="OK" />
</td></tr>
</form>
</table>
EOT
		);
	}

	function doIPRequest( $ip ) {
		global $wgUser, $wgOut, $wgLang, $wgDBname;
		$fname = 'CheckUser::doIPRequest';

		$this->addLogEntry( $ip );

		$dbr =& wfGetDB( DB_SLAVE );
		$conds = $this->getIpConds( $dbr, $ip );
		if ( $conds === false ) {
			$wgOut->addWikiText( wfMsg( 'checkuser_invalid_ip' ) );
			return;
		}
		$pager = new CheckUserPager( $conds );
		$s = $pager->getBody() .
			$pager->getNavigationBar();
		$wgOut->addHTML( $s );
		/*
		$res = $dbr->select( 'cuc_changes', array( '*' ), $conds, $fname, 
	   		array( 'ORDER BY' => 'cuc_timestamp DESC' ) );
		if ( !$dbr->numRows( $res ) ) {
			$s =  wfMsg( 'checkuser_no_results' );
		} else {
			global $IP;
			require_once( $IP.'/includes/RecentChange.php' );
			require_once( $IP.'/includes/ChangesList.php' );
			
			if ( in_array( 'newfromuser', array_map( 'strtolower', get_class_methods( 'ChangesList' ) ) ) ) {
				// MW >= 1.6
				$list = ChangesList::newFromUser( $wgUser );
			} else {
				// MW < 1.6
				$sk =& $wgUser->getSkin();
				$list = new ChangesList( $sk );
			}
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
		 */
	}
	
	/**
	 * Get conditions for an IP or IP range
	 * @param Database $db
	 * @param string $ip
	 * @return array conditions
	 */
	function getIpConds( $db, $ip ) {
		list( $start, $end ) = IP::parseRange( $ip );
		if ( $start === false ) { 
			return false;
		} elseif ( $start == $end ) {
			return array( 'cuc_ip' => $start );
		} else {
			return array( 'cuc_ip BETWEEN ' . $db->addQuotes( $start ) . ' AND ' . $db->addQuotes( $end ) );
		}
	}

	function doUserRequest( $user ) {
		global $wgOut, $wgTitle, $wgLang, $wgUser, $wgDBname;
		$fname = 'CheckUser::doUserRequest';

		$pager = new CheckUserPager( array( 'cuc_user_text' => $user ) );
		$wgOut->addHTML( $pager->getBody() . $pager->getNavigationBar() );
		

		/*
		$userTitle = Title::newFromText( $user, NS_USER );
		if( !is_null( $userTitle ) ) {
			// normalize the username
			$user = $userTitle->getText();
		}

		if ( !$this->addLogEntry( $wgLang->timeanddate( wfTimestampNow() ) . ' ' .
		  $wgUser->getName() . ' got IPs for ' . htmlspecialchars( $user ) . ' on ' . $wgDBname ) ) 
		{
			$wgOut->addHTML( '<p>Unable to add log entry</p>' );
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
		 */
	}

	function showLog() {
		global $wgOut;
		$pager = new CheckUserLogPager;
		$wgOut->addHTML( 
			$pager->getNavigationBar() .
			$pager->getBody() .
			$pager->getNavigationBar() 
		);
		
		/*
		global $wgCheckUserLog;
		
		if( $wgCheckUserLog === false || !file_exists( $wgCheckUserLog ) ) {
			# No log
			return;
		} else {
			global $wgRequest, $wgOut;
			
			if( $wgRequest->getVal( 'log' ) == 1 ) {
				# Show the log
				list( $limit, $offset ) = wfCheckLimits();
				$log = $this->tail( $wgCheckUserLog, $limit, $offset );
				if( !!$log ) {

					$scroller = wfViewPrevNext( $offset, $limit,
						Title::makeTitle( NS_SPECIAL, 'CheckUser' ),
						'log=1',
						count( $log ) < $limit );
					
					$output = implode( "\n", $log );
					$wgOut->addHTML( "$scroller\n<ul>$output</ul>\n$scroller\n" );
				} else {
					$wgOut->addHTML( "<p>The log contains no items.</p>" );
				}
			} else {
				# Hide the log, show a link
				global $wgTitle, $wgUser;
				$skin = $wgUser->getSkin();
				$link = $skin->makeKnownLinkObj( $wgTitle, 'Show log', 'log=1' );
				$wgOut->addHTML( "<p>$link</p>" );
			}
		}*/
	}
	
	function tail( $filename, $limit, $offset ) {
		//wfSuppressWarnings();
		$file = fopen( $filename, "rt" );
		//wfRestoreWarnings();
		
		if( $file === false ) {
			return false;
		}
		
		$filePosition = filesize( $filename );
		if( $filePosition == 0 ) {
			return array();
		}
		
		$lines = array();
		$bufSize = 1024;
		$lineCount = 0;
		$total = $offset + $limit;
		$leftover = '';
		do {
			if( $filePosition < $bufSize ) {
				$bufSize = $filePosition;
			}
			$filePosition -= $bufSize;
			fseek( $file, $filePosition );
			$buffer = fread( $file, $bufSize );
			
			$parts = explode( "\n", $buffer );
			$num = count( $parts );
			
			if( $num > 0 ) {
				if( $lineCount++ > $offset ) {
					$lines[] = $parts[$num - 1] . $leftover;
					if( $lineCount > $total ) {
						return $lines;
					}
				}
			}
			for( $i = $num - 2; $i > 0; $i-- ) {
				if( $lineCount++ > $offset ) {
					$lines[] = $parts[$i];
					if( $lineCount > $total ) {
						fclose( $file );
						return $lines;
					}
				}
			}
			if( $num > 1 ) {
				$leftover = $parts[0];
			} else {
				$leftover = '';
				break;
			}
		} while( $filePosition > 0 );
		
		if( $lineCount++ > $offset ) {
			$lines[] = $leftover;
		}
		fclose( $file );
		return $lines;
	}

	function addLogEntry( $targetText, $targetUser = 0 ) {
		global $wgUser, $wgCheckUserLog;
		$dbw =& wfGetDB( DB_MASTER );
		$dbw->insert( 'cu_log', 
			array(
				'cul_id' => $dbw->nextSequenceValue( 'cu_log_cul_id' ),
				'cul_timestamp' => $dbw->timestamp(),
				'cul_user' => $wgUser->getID(),
				'cul_user_text' => $wgUser->getName(),
				'cul_target_user' => $targetUser,
				'cul_target_text' => $targetText,
			), __METHOD__
		);
		return $dbw->insertId();
	}

	/**
	 * Hook function for RecentChange_save
	 * Saves user data into the cu_change table
	 */
	function onChange( $rc ) {
		$dbw =& wfGetDB( DB_MASTER );
		extract( $rc->mAttribs );
		$ip = IP::toHex( wfGetIP() );
		$xff = isset( $_SERVER['X_FORWARDED_FOR'] ) ? $_SERVER['X_FORWARDED_FOR'] : '';

		if ( !isset( $rc_log_id ) ) {
			$rc_log_id = $rc_type == RC_LOG ? -1 : 0;
		}
		
		$dbw->insert( 'cu_change', 
			array(
				'cuc_rev_id' => $rc_this_oldid,
				'cuc_log_id' => $rc_type == RC_LOG ? -1 : 0,
				'cuc_timestamp' => $rc_timestamp,
				'cuc_ip' => $ip,
				'cuc_user' => $rc_user,
				'cuc_xff' => $xff,
			), __METHOD__
		);
	}
}

class CheckUserPager extends TablePager {
	var $mConds, $mFieldNames;

	function __construct( $conds ) {
		$this->mConds = $conds;
		parent::__construct();
	}

	function getQueryInfo() {
		extract( $this->mDb->tableNames( 'cu_changes', 'revision', 'logging', 'page' ) );
		return array(
			'tables' => "$cu_changes " .
			            "LEFT JOIN $revision ON cuc_rev_id=rev_id " .
						"LEFT JOIN $page ON page_id=rev_page " .
						"LEFT JOIN $logging ON cuc_log_id=log_id",
			'fields' => array( 
				'cuc_timestamp', 'cuc_ip', 'cuc_xff', 'cuc_user', 'cuc_user_text', 	'cuc_rev_id', 'cuc_log_id',
				'rev_comment', 
				'page_namespace', 'page_title',
				'log_type', 'log_action', 'log_namespace', 'log_title', 'log_comment', 'log_params'
			),
			'conds' => $this->mConds
		);
	}

	function isFieldSortable( $field ) {
		static $sortable = array(
			'cuc_ip',
			'cuc_user_text',
			'cuc_timestamp'
		);
		return in_array( $field, $sortable );
	}

	function getFieldNames() {
		if ( !$this->mFieldNames ) {
			$fields = array( 'link', 'cuc_timestamp', 'cuc_ip', 'cuc_xff', 
				'cuc_user_text', 'title', 'comment' );
			foreach ( $fields as $field ) {
				$this->mFieldNames[$field] = wfMsg( "checkuser_$field" );
			}
		}
		return $this->mFieldNames;
	}

	function formatValue( $field, $value, $row ) {
		switch ( $field ) {
			case 'link':
				return $this->getLinkFromRow( $row );
			case 'cuc_timestamp':
				global $wgLang;
				return $this->timeanddate( $value );
			case 'cuc_ip':
			case 'cuc_xff':
				return $value;
			case 'cuc_user_text':
				return $this->getSkin()->userLink( $row->cuc_user, $value ) . 
					$this->getSkin()->userToolLinks( $row->cuc_user, $value );
			case 'title':
				$title = $this->getTitleFromRow( $row );
				if ( $title ) {
					return $this->getSkin()->makeLinkObj( $title );
				} else {
					return '';
				}
			case 'comment':
				if ( !is_null( $row->rev_comment ) ) {
					return $this->getSkin()->commentBlock( $row->rev_comment );
				} elseif ( !is_null( $row->log_comment ) ) {
					return 
						LogPage::actionText( 
							$row->log_type, $row->log_action, $title, $this->getSkin(), 
							$this->log_params, true, true 
						) . ' ' . $this->getSkin()->commentBlock( $row->rev_comment );
				}
		}
	}

	function getTitleFromRow( $row ) {
		if ( !is_null( $row->page_namespace ) ) {
			$title = Title::makeTitle( $row->page_namespace, $row->page_title );
		} elseif ( !is_null( $row->log_namespace ) ) {
			$title = Title::makeTitle( $row->log_namespace, $row->log_title );
		} else {
			$title = null;
		}
		return $title;
	}

	function getLinkFromRow( $row ) {
		if ( !is_null( $row->log_id ) ) {
			$title = Title::makeTitle( NS_SPECIAL, 'Log' );
			$text = LogPage::logName( $row->log_action );
			$link = $this->getSkin()->makeKnownLinkObj( $title, $text, 'id=' . $row->log_id );
		} elseif ( !is_null( $row->rev_id ) ) {
			$title = $this->getTitleFromRow( $row );
			$text = wfMsg( 'diff' );
			$query = "dir=prev&diff={$this->rev_id}";
			$link = $this->getSkin()->makeLinkObj( $title, $text );
		} else {
			$link = '';
		}
		return $link;
	}

	function getDefaultSort() {
		return 'cuc_timestamp';
	}
}

class CheckUserLogPager extends ReverseChronologicalPager {
	function getQueryInfo() {
		return array(
			'tables' => 'cu_log',
			'fields' => '*',
		);
	}

	function getIndexField() {
		return 'cul_timestamp';
	}
	
	function formatRow( $row ) {
		global $wgLang;
		return wfMsg( 'checkuser_log_entry', 
			$wgLang->timeanddate( $row->cul_timestamp ), 
			$row->cul_user_text,
			$row->cul_target_text );
	}
}

?>
