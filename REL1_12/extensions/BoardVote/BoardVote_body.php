<?php

if (!defined('MEDIAWIKI')) {
	die( "Not a valid entry point\n" );
}

wfBoardVoteInitMessages();

class BoardVotePage extends UnlistedSpecialPage {
	var $mPosted, $mVotedFor, $mUserDays, $mUserEdits;
	var $mHasVoted, $mAction, $mUserKey, $mId, $mFinished;
	var $mDb;

	function BoardVotePage() {
		parent::__construct( "Boardvote" );
	}

	function getUserFromRemote( $sid, $db, $site, $lang ) {
		$regex = '/^[\w.-]+$/';
		if ( !preg_match( $regex, $sid ) ||
			!preg_match( $regex, $db ) ||
			!preg_match( $regex, $site ) ||
			!preg_match( $regex, $lang )
		) {
			wfDebug( __METHOD__.": Invalid parameter\n" );
			return array( false, false, false );
		}

		$url = "https://secure.wikimedia.org/$site/$lang/w/query.php?what=userinfo&format=php";
		#$url = "http://$lang.$site.org/w/query.php?what=userinfo&format=php";
		#$url = "http://eva/w2/extensions/BotQuery/query.php?what=userinfo&format=php";
		wfDebug( "Fetching URL $url\n" );
		$c = curl_init( $url );
		#curl_setopt( $c, CURLOPT_CAINFO, dirname( __FILE__ ) . '/cacert-both.crt' );
		curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $c, CURLOPT_COOKIE, "{$db}_session=" . urlencode( $sid ) );
		curl_setopt( $c, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $c, CURLOPT_FAILONERROR, true );
		$value = curl_exec( $c );
		if ( !$value ) {
			wfDebug( __METHOD__.": No response from server\n" );
			$_SESSION['bvCurlError'] = curl_error( $c );
			return array( false, false, false );
		}

		$decoded = unserialize( $value );
		if ( isset( $decoded['meta']['user']['anonymous'] ) ) {
			wfDebug( __METHOD__.": User is not logged in\n" );
			return array( false, false, false );
		}
		if ( !isset( $decoded['meta']['user']['name'] ) ) {
			wfDebug( __METHOD__.": No username in response\n" );
			return array( false, false, false );
		}
		wfDebug( __METHOD__." got response for user {$decoded['meta']['user']['name']}@$db\n" );
		return array(
			$decoded['meta']['user']['name'] . '@' . $db,
			$db,
			isset( $decoded['meta']['user']['blocked'] ),
		);
	}

	function init( $par ) {
		global $wgRequest;
		if( session_id() == '' ) {
			wfSetupSession();
		}

		$this->mRemoteSession = $wgRequest->getVal( 'sid' );
		$this->mRemoteDB = $wgRequest->getVal( 'db' );
		$this->mRemoteSite = $wgRequest->getVal( 'site' );
		$this->mRemoteLanguage = $wgRequest->getVal( 'lang' );

		if ( $this->mRemoteSession ) {
			$info = $this->getUserFromRemote(
				$this->mRemoteSession,
				$this->mRemoteDB,
				$this->mRemoteSite,
				$this->mRemoteLanguage
			);
			list( $this->mUserKey, $this->mDBname, $this->mBlocked ) = $info;
			list( $_SESSION['bvUserKey'], $_SESSION['bvDBname'], $_SESSION['bvBlocked'] ) = $info;
			if ( !is_null( $wgRequest->getVal( 'uselang' ) ) ) {
				$_SESSION['bvLang'] = $wgRequest->getVal( 'uselang' );
			} else {
				$_SESSION['bvLang'] = $this->mRemoteLanguage;
			}
		} elseif ( isset( $_SESSION['bvUserKey'] ) ) {
			$this->mUserKey = $_SESSION['bvUserKey'];
			$this->mDBname = $_SESSION['bvDBname'];
			$this->mBlocked = $_SESSION['bvBlocked'];
		} elseif ( defined( 'BOARDVOTE_ALLOW_LOCAL' ) ) {
			global $wgUser, $wgDBname;
			$this->mUserKey = $wgUser->getName() . "@" . $wgDBname;
			$this->mDBname = $wgDBname;
			$this->mBlocked = $wgUser->isBlocked();
		} else {
			$this->mUserKey = false;
			$this->mDBname = false;
			$this->mBlocked = false;
		}

		$this->mPosted = $wgRequest->wasPosted();
		if ( method_exists( $wgRequest, 'getArray' ) ) {
			$this->mVotedFor = $wgRequest->getArray( "votedfor", array() );
		} else {
			$this->mVotedFor = $wgRequest->getVal( "votedfor", array() );
		}
		$this->mId = $wgRequest->getInt( "id", 0 );

		$this->mHasVoted = $this->hasVoted();

		if ( $par ) {
			$this->mAction = $par;
		} else {
			$this->mAction = $wgRequest->getText( "action" );
		}
	}


	function execute( $par ) {
		global $wgBoardVoteEditCount, $wgBoardVoteEndDate, $wgBoardVoteStartDate,
			$wgBoardVoteFirstEdit, $wgOut;

		$this->init( $par );
		$this->setHeaders();

		if ( $this->mRemoteSession ) {
			$wgOut->redirect( $this->getTitle( $par )->getFullUrl() );
			return;
		}

		if ( $this->mUserKey ) {
			$wgOut->addWikiText( wfMsg( 'boardvote_welcome', wfEscapeWikiText( $this->mUserKey ) ) );
		}

		if ( wfTimestampNow() < $wgBoardVoteStartDate && !$this->isAdmin() ) {
			$wgOut->addWikiText( wfMsg( 'boardvote_notstarted' ) );
			return;
		}

		if ( $this->mBlocked ) {
			$wgOut->addWikiText( wfMsg( 'boardvote_blocked' ) );
			return;
		}

		if ( wfTimestampNow() > $wgBoardVoteEndDate ) {
			$this->mFinished = true;
		} else {
			$this->mFinished = false;
		}


		if ( $this->mFinished ) {
				$wgOut->addWikiText( wfMsg( 'boardvote_closed' ) );
		}

		if ( $this->mAction == "list" ) {
			$this->displayList();
		} elseif ( $this->mAction == "dump" ) {
			$this->dump();
		} elseif ( $this->mAction == "strike" ) {
			$this->strike( $this->mId, false );
		} elseif ( $this->mAction == "unstrike" ) {
			$this->strike( $this->mId, true );
		} elseif( $this->mAction == "vote" && !$this->mFinished ) {
			if ( !$this->mUserKey ) {
				$this->notLoggedIn();
			} else {
				if ( !$this->isQualified( $this->mUserKey ) ) {
					$this->notQualified();
				} elseif ( $this->mPosted ) {
					$this->logVote();
				} else {
					$this->displayVote();
				}
			}
		} else {
			$this->displayEntry();
		}
	}

	function displayEntry() {
		global $wgOut;
		$wgOut->addWikiText( wfMsg( "boardvote_entry" ) );
	}

	function getDB() {
		if ( !$this->mDb ) {
			global $wgBoardVoteDBServer, $wgBoardVoteDB, $wgDBuser, $wgDBpassword;

			$this->mDb = new Database( $wgBoardVoteDBServer, $wgDBuser, $wgDBpassword,
				$wgBoardVoteDB, /*failfn*/false, /*flags*/0, /*prefix*/'' );
			if ( !$this->mDb->isOpen() ) {
					// This should be handled inside the constructor, but we'll check just in case
					throw new MWException( "DB connection failed unexpectedly" );
			}
		}
		return $this->mDb;
	}

	function hasVoted() {
		$dbr =& $this->getDB();
		$row = $dbr->selectRow( 'vote_log', array( "1" ),
		  array( "log_user_key" => $this->mUserKey ), "BoardVotePage::getUserVote" );
		if ( $row === false ) {
			return false;
		} else {
			return true;
		}
	}

	function logVote() {
		global $wgUser, $wgDBname, $wgOut, $wgGPGPubKey, $wgRequest;
		$fname = "BoardVotePage::logVote";

		$now = wfTimestampNow();
		$record = $this->getRecord();
		$encrypted = $this->encrypt( $record );
		$gpgKey = file_get_contents( $wgGPGPubKey );
		$dbw =& $this->getDB();
		$log = $dbw->tableName( "vote_log" );

		# Mark previous votes as old
		$encKey = $dbw->strencode( $this->mUserKey );
		$sql = "UPDATE $log SET log_current=0 WHERE log_user_key='$encKey'";
		$dbw->query( $sql, $fname );

		# Add vote to log
		$xff = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		if ( !$xff ) {
			$xff = '';
		}

		$tokenMatch = $wgUser->matchEditToken( $wgRequest->getVal( 'edit_token' ) );

		$dbw->insert( $log, array(
			"log_user" => 0,
			"log_user_text" => '',
			"log_user_key" => $this->mUserKey,
			"log_wiki" => $wgDBname,
			"log_record" => $encrypted,
			"log_ip" => wfGetIP(),
			"log_xff" => $xff,
			"log_ua" => $_SERVER['HTTP_USER_AGENT'],
			"log_timestamp" => $now,
			"log_current" => 1,
			"log_token_match" => $tokenMatch ? 1 : 0,
		), $fname );

		$wgOut->addWikiText( wfMsg( "boardvote_entered", $record, $gpgKey, $encrypted ) );
	}

	function displayVote() {
		global $wgBoardCandidates, $wgOut;

		$thisTitle = Title::makeTitle( NS_SPECIAL, "Boardvote" );
		$action = $thisTitle->escapeLocalURL( "action=vote" );
		if ( $this->mHasVoted ) {
			$intro = wfMsg( "boardvote_intro_change" );
		} else {
			$intro = wfMsg( "boardvote_intro" );
		}

		$ok = wfMsgHtml( "boardvote_submit" );

		$candidates = array();
		foreach( $wgBoardCandidates as $i => $candidate ) {
			$candidates[] = array( $i, $candidate );
		}

		srand ((float)microtime()*1000000);
		shuffle( $candidates );

		$text = "
		  $intro
		  <form name=\"boardvote\" id=\"boardvote\" method=\"post\" action=\"$action\">
		  <table border='0'>";
		foreach ( $candidates as $candidate ) {
			$text .= $this->voteEntry( $candidate[0], $candidate[1] );
		}

		global $wgUser;
		$token = htmlspecialchars( $wgUser->editToken() );
		$text .= "<tr><td>&nbsp;</td><td>
		  <input name=\"submit\" type=\"submit\" value=\"$ok\">
		  <input type='hidden' name='edit_token' value=\"{$token}\" />
		  </td></tr></table></form>";
		$text .= wfMsg( "boardvote_footer" );
		$wgOut->addHTML( $text );
	}

	function voteEntry( $index, $candidate ) {
		return "
		<tr><td align=\"right\">
		  <input type=\"checkbox\" name=\"votedfor[{$index}]\" value=\"1\">
		</td><td align=\"left\">
		  $candidate
		</td></tr>";
	}

	function notLoggedIn() {
		global $wgOut, $wgBoardVoteEditCount, $wgBoardVoteCountDate, $wgLang, $wgBoardVoteFirstEdit;
		$wgOut->addWikiText( wfMsg( "boardvote_nosession", $wgBoardVoteEditCount,
			$wgLang->timeanddate( $wgBoardVoteCountDate ),
			$wgLang->timeanddate( $wgBoardVoteFirstEdit )
	   	) );
	}

	function notQualified() {
		global $wgOut, $wgBoardVoteEditCount, $wgBoardVoteCountDate, $wgLang, $wgBoardVoteFirstEdit;
		$wgOut->addWikiText( wfMsg( "boardvote_notqualified", '[unknown]',
			$wgLang->timeanddate( $wgBoardVoteCountDate ), $wgBoardVoteEditCount, '[unknown]',
			$wgLang->timeanddate( $wgBoardVoteFirstEdit )
		) );
	}

	function getRecord() {
		global $wgBoardCandidates;

		$record =
		  "I voted for: " . implode( ", ", wfArrayLookup( $wgBoardCandidates, $this->mVotedFor ) ). "\n";

		// Pad it out with spaces to a constant length, so that the encrypted record is secure
		$padLength = array_sum( array_map( 'strlen', $wgBoardCandidates ) ) + count( $wgBoardCandidates ) * 2 + 20;
		//           ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^   ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^  ^^^^
		//               length of the candidate names added together            room for separators           extra

		$record = str_pad( $record, $padLength );
		return $record;
	}

	function encrypt( $record ) {
		global $wgGPGCommand, $wgGPGRecipient, $wgGPGHomedir;
		# Get file names
		$input = tempnam( "/tmp", "gpg_" );
		$output = tempnam( "/tmp", "gpg_" );

		# Write unencrypted record
		$file = fopen( $input, "w" );
		fwrite( $file, $record );
		fclose( $file );

		# Call GPG
		$command = wfEscapeShellArg( $wgGPGCommand ) . " --batch --yes -eaisz0 -r" .
			wfEscapeShellArg( $wgGPGRecipient ) . " -o " . wfEscapeShellArg( $output );
		if ( $wgGPGHomedir ) {
			$command .= " --homedir " . wfEscapeShellArg( $wgGPGHomedir );
		}
		$command .= " " . wfEscapeShellArg( $input ) . " 2>&1";

		$error = wfShellExec( $command );

		# Read result
		$result = file_get_contents( $output );

		if ( !$result ) {
			$result = "Command: $command\nError: $error";
			//$result = "Error\n";
		}

		# Delete temporary files
		unlink( $input );
		unlink( $output );

		return $result;
	}

	function isQualified( $key ) {
		if ( defined( 'BOARDVOTE_QUALIFIED_HACK' ) ) {
			return true;
		}
		$db = $this->getDB();
		$qualified = $db->selectField( 'voters', '1', array( 'user_key' => $key ), __METHOD__ );
		return (bool)$qualified;
	}

	function displayList() {
		global $wgOut, $wgOutputEncoding, $wgLang, $wgUser;

		$userRights = $wgUser->getRights();
		$admin = $this->isAdmin();
		$dbr =& $this->getDB();
		$log = $dbr->tableName( "vote_log" );

		$sql = "SELECT * FROM $log ORDER BY log_user_key";
		$res = $dbr->query( $sql, "BoardVotePage::list" );
		if ( $dbr->numRows( $res ) == 0 ) {
			$wgOut->addWikiText( wfMsg( "boardvote_novotes" ) );
			return;
		}
		$thisTitle = Title::makeTitle( NS_SPECIAL, "Boardvote" );
		$sk = $wgUser->getSkin();
		$dumpLink = $sk->makeKnownLinkObj( $thisTitle, wfMsg( "boardvote_dumplink" ), "action=dump" );

		$intro = wfMsg( "boardvote_listintro", $dumpLink );
		$hTime = wfMsg( "boardvote_time" );
		$hUser = wfMsg( "boardvote_user" );
		$hIp = wfMsg( "boardvote_ip" );
		$hUa = wfMsg( "boardvote_ua" );

		$s = "$intro <table border=1><tr><th>
			$hUser
		  </th><th>
			$hTime
		  </th>";

		if ( $admin ) {
			$s .= "<th>
			    $hIp
			  </th><th>
			    $hUa
			  </th><th>&nbsp;</th>";
		}
		$s .= "</tr>";

		while ( $row = $dbr->fetchObject( $res ) ) {
			$user = $row->log_user_key;
			$time = $wgLang->timeanddate( $row->log_timestamp );
			$cellOpen = "<td>";
			$cellClose = "</td>";
			if ( !$row->log_current ) {
				$cellOpen .= "<font color=\"#666666\">";
				$cellClose = "</font>$cellClose";
			}
			if ( $row->log_strike ) {
				$cellOpen .= "<del>";
				$cellClose = "</del>$cellClose";
			}
			$s .= "<tr>$cellOpen
				  $user
				{$cellClose}{$cellOpen}
				  $time
				{$cellClose}";

			if ( $admin ) {
				if ( $row->log_strike ) {
					$strikeLink = $sk->makeKnownLinkObj( $thisTitle, wfMsg( "boardvote_unstrike" ),
					  "action=unstrike&id={$row->log_id}" );
				} else {
					$strikeLink = $sk->makeKnownLinkObj( $thisTitle, wfMsg( "boardvote_strike" ),
					  "action=strike&id={$row->log_id}" );
				}

				$s .= "{$cellOpen}
				  {$row->log_ip}
				{$cellClose}{$cellOpen}
				  {$row->log_ua}
				{$cellClose}<td>
				  {$strikeLink}
				</td></tr>";
			} else {
				$s .= "</tr>";
			}
		}
		$s .= "</table>";
		$wgOut->addHTML( $s );
	}

	function dump() {
		global $wgOut, $wgOutputEncoding, $wgLang;
		$dbr =& $this->getDB();
		$log = $dbr->tableName( "vote_log" );

		$sql = "SELECT log_record FROM $log WHERE log_current=1 AND log_strike=0";
		$res = $dbr->query( $sql, DB_SLAVE, "BoardVotePage::list" );
		if ( $dbr->numRows( $res ) == 0 ) {
			$wgOut->addWikiText( wfMsg( "boardvote_novotes" ) );
			return;
		}

		$s = "<pre>";
		while ( $row = $dbr->fetchObject( $res ) ) {
			$s .= $row->log_record . "\n\n";
		}
		$s .= "</pre>";
		$wgOut->addHTML( $s );
	}

	function isAdmin() {
		global $wgUser;
		$userRights = $wgUser->getRights();
		if ( in_array( "boardvote", $userRights ) ) {
			return true;
		} else {
			return false;
		}
	}

	function strike( $id, $unstrike ) {
		global $wgOut;

		$dbw =& $this->getDB();
		$log = $dbw->tableName( "vote_log" );

		if ( !$this->isAdmin() ) {
			$wgOut->addWikiText( wfMsg( "boardvote_needadmin" ) );
			return;
		}
		$value = $unstrike ? 0 : 1;
		$sql = "UPDATE $log SET log_strike=$value WHERE log_id=$id";
		$dbw->query( $sql, "BoardVotePage::strike" );

		$title = Title::makeTitle( NS_SPECIAL, "Boardvote" );
		$wgOut->redirect( $title->getFullURL( "action=list" ) );
	}
}
