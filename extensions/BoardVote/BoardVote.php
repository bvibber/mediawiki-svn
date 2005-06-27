<?php

# Wikimedia Foundation Board of Trustees Election

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
	die( "Not a valid entry point\n" );
}

# Register extension
$wgExtensionFunctions[] = "wfBoardvoteSetup";

# This extension acts as a special page

# Default settings
$wgBoardVoteDB = "boardvote";
$wgBoardCandidates = array();
$wgGPGCommand = "gpg";
$wgGPGRecipient = "boardvote";
$wgGPGHomedir = false;
$wgGPGPubKey = "C:\\Program Files\\gpg\\pub.txt";
$wgBoardVoteEditCount = 400;
$wgBoardVoteCountDate = '20050530000000';

function wfBoardvoteSetup()
{
# Look out, freaky indenting
global $IP;
require_once( "$IP/includes/SpecialPage.php" );

class BoardVotePage extends SpecialPage {
	var $mPosted, $mVotedFor, $mDBname, $mUserDays, $mUserEdits;
	var $mHasVoted, $mAction, $mUserKey, $mId, $mFinished;

	function BoardVotePage() {
		SpecialPage::SpecialPage( "Boardvote" );
	}

	function execute( $par ) {
		global $wgUser, $wgDBname, $wgInputEncoding, $wgRequest, $wgBoardVoteDB, $wgBoardVoteEditCount;

		$this->mUserKey = iconv( $wgInputEncoding, "UTF-8", $wgUser->getName() ) . "@$wgDBname";
		$this->mPosted = $wgRequest->wasPosted();
		$this->mVotedFor = $wgRequest->getArray( "votedfor", array() );
		$this->mId = $wgRequest->getInt( "id", 0 );
		
		$this->mDBname = $wgBoardVoteDB;
		$this->mHasVoted = $this->hasVoted( $wgUser );
		
		if ( $par ) {
			$this->mAction = $par;
		} else {
			$this->mAction = $wgRequest->getText( "action" );
		}

		$this->setHeaders();

		//if ( time() > 1087084800 ) {
		//	$this->mFinished = true; 
		//} else {
			$this->mFinished = false;
		//}

		if ( $this->mAction == "list" ) {
			$this->displayList();
		} elseif ( $this->mAction == "dump" ) {
			$this->dump();
		} elseif ( $this->mAction == "strike" ) {
			$this->strike( $this->mId, false );
		} elseif ( $this->mAction == "unstrike" ) {
			$this->strike( $this->mId, true );
		} elseif( $this->mAction == "vote" && !$this->mFinished ) {
			if ( !$wgUser->getID() ) {
				$this->notLoggedIn();
			} else {
				$this->getQualifications( $wgUser );
				if ( $this->mUserEdits < $wgBoardVoteEditCount ) {
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

	function hasVoted( &$user ) {
		global $wgDBname;
		$dbr =& wfGetDB( DB_SLAVE );
		$row = $dbr->selectRow( "`{$this->mDBname}`.log", array( "1" ), 
		  array( "log_user_key" => $this->mUserKey ), "BoardVotePage::getUserVote" );
		if ( $row === false ) {
			return false;
		} else {
			return true;
		}
	}

	function logVote() {
		global $wgUser, $wgDBname, $wgIP, $wgOut, $wgGPGPubKey;
		$fname = "BoardVotePage::logVote";
		
		$now = wfTimestampNow();
		$record = $this->getRecord();
		$encrypted = $this->encrypt( $record );
		$gpgKey = file_get_contents( $wgGPGPubKey );
		$dbw =& wfGetDB( DB_MASTER );
		$log = $dbw->tableName( "`{$this->mDBname}`.log" );

		# Mark previous votes as old
		$encKey = $dbw->strencode( $this->mUserKey );
		$sql = "UPDATE $log SET log_current=0 WHERE log_user_key='$encKey'";
		$dbw->query( $sql, $fname );

		# Add vote to log
		$xff = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		if ( !$xff ) {
			$xff = '';
		}
		
		$dbw->insert( $log, array(
			"log_user" => $wgUser->getID(),
			"log_user_text" => $wgUser->getName(),
			"log_user_key" => $this->mUserKey,
			"log_wiki" => $wgDBname,
			"log_edits" => $this->mUserEdits,
			"log_record" => $encrypted,
			"log_ip" => $wgIP,
			"log_xff" => $xff,
			"log_ua" => $_SERVER['HTTP_USER_AGENT'],
			"log_timestamp" => $now,
			"log_current" => 1
		), $fname );

		$wgOut->addWikiText( wfMsg( "boardvote_entered", $record, $gpgKey, $encrypted ) );
	}
	
	function displayVote() {
		global $wgBoardCandidates, $wgOut;
		
		$thisTitle = Title::makeTitle( NS_SPECIAL, "Boardvote" );
		$action = $thisTitle->getLocalURL( "action=vote" );
		if ( $this->mHasVoted ) {
			$intro = wfMsg( "boardvote_intro_change" );
		} else {
			$intro = wfMsg( "boardvote_intro" );
		}

		$ok = wfMsg( "ok" );
		
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
		
		$text .= "<tr><td>&nbsp;</td><td>
		  <input name=\"submit\" type=\"submit\" value=\"$ok\">
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
		global $wgOut, $wgBoardVoteEditCount, $wgBoardVoteCountDate, $wgLang;
		$wgOut->addWikiText( wfMsg( "boardvote_notloggedin", $wgBoardVoteEditCount, 
   			$wgLang->timeanddate( $wgBoardVoteCountDate ) ) );
	}
	
	function notQualified() {
		global $wgOut, $wgBoardVoteEditCount, $wgBoardVoteCountDate, $wgLang;
		$wgOut->addWikiText( wfMsg( "boardvote_notqualified", $this->mUserEdits, 
   			$wgLang->timeanddate( $wgBoardVoteCountDate ), $wgBoardVoteEditCount ) );
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
		$command = wfEscapeShellArg( $wgGPGCommand ) . " --batch --yes -ear " . 
			wfEscapeShellArg( $wgGPGRecipient ) . " -o " . wfEscapeShellArg( $output );
		if ( $wgGPGHomedir ) {
			$command .= " --homedir " . wfEscapeShellArg( $wgGPGHomedir );
		} 
		$command .= " " . wfEscapeShellArg( $input ) . " 2>&1";

		$error = shell_exec( $command );

		# Read result
		$result = file_get_contents( $output );

		if ( !$result ) {
			//$result = "Command: $command\nError: $error";
			$result = "Error\n";
		}

		# Delete temporary files
		unlink( $input );
		unlink( $output );
		
		return $result;
	}

	function getQualifications( &$user ) {
		global $wgBoardVoteEditCount, $wgBoardVoteCountDate, $wgVersion;
		
		$dbr =& wfGetDB( DB_SLAVE );

		# Count contributions before $wgBoardVoteCountDate
		
		$id = $user->getID();
		if ( !$id ) {
			$this->mUserEdits = 0;
			return;
		}
		$date = $dbr->addQuotes( $wgBoardVoteCountDate );

		if ( version_compare( $wgVersion, '1.5alpha1' ) >= 0 ) {
			# New schema
			extract( $dbr->tableNames( 'revision' ) );
			$sql = "SELECT COUNT(*) as n FROM $revision WHERE rev_timestamp<=$date AND rev_user=$id";
			$res = $dbr->query( $sql, "BoardVotePage::getQualifications" );
			$row = $dbr->fetchObject( $res );
			$this->mUserEdits = $row->n;
		} else {
			# Old schema
			extract( $dbr->tableNames( 'cur', 'old' ) );
			# First cur
			$sql = "SELECT COUNT(*) as n FROM $cur WHERE cur_timestamp<=$date AND cur_user=$id";
			$res = $dbr->query( $sql, "BoardVotePage::getQualifications" );
			$cur = $dbr->fetchObject( $res );
			$dbr->freeResult( $res );

			# If the user has stacks of contributions, don't check old as well			
			if ( $cur->n > $wgBoardVoteEditCount * 2 ) {
				$this->mUserEdits = 0x7fffffff;
				return;
			}

			# Now check old
			$sql = "SELECT COUNT(*) as n FROM $old WHERE old_timestamp<=$date AND old_user=$id";
			$res = $dbr->query( $sql, DB_SLAVE, "BoardVotePage::getQualifications" );
			$old = $dbr->fetchObject( $res );
			$dbr->freeResult( $res );
			
			$this->mUserEdits = $cur->n + $old->n;
		}
	}
	
	function displayList() {
		global $wgOut, $wgOutputEncoding, $wgLang, $wgUser;

		$userRights = $wgUser->getRights();
		$admin = $this->isAdmin();
		$dbr =& wfGetDB( DB_SLAVE );
		$log = $dbr->tableName( "`{$this->mDBname}`.log" );

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
		$hEdits = wfMsg( "boardvote_edits" );
		$hIp = wfMsg( "boardvote_ip" );
		$hUa = wfMsg( "boardvote_ua" );

		$s = "$intro <table border=1><tr><th>
			$hUser
		  </th><th>
			$hTime
		  </th><th>
			$hEdits
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
			if ( $wgOutputEncoding != "utf-8" ) {
				$user = wfUtf8ToHTML( $row->log_user_key );
			} else {
				$user = $row->log_user_key;
			}
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
			$edits = $row->log_edits == 0x7fffffff ? "many" : $row->log_edits;
			$s .= "<tr>$cellOpen
				  $user
				{$cellClose}{$cellOpen}
				  $time
				{$cellClose}{$cellOpen}
				  $edits
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
		global $wgOut, $wgOutputEncoding, $wgLang, $wgUser;
		$dbr =& wfGetDB( DB_SLAVE );
		$log = $dbr->tableName( "`{$this->mDBname}`.log" );

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
		
		$dbw =& wfGetDB( DB_MASTER );
		$log = $dbw->tableName( "`{$this->mDBname}`.log" );

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

SpecialPage::addPage( new BoardVotePage );

global $wgMessageCache;
$wgMessageCache->addMessages( array(

"boardvote"               => "Wikimedia Board of Trustees election",
"boardvote_entry"         => 
"* [[Special:Boardvote/vote|Vote]]
* [[Special:Boardvote/list|List votes to date]]
* [[Special:Boardvote/dump|Dump encrypted election record]]",
"boardvote_intro"         => "
<p>Welcome to the second elections for the Wikimedia Board of Trustees. We are
voting for two people to represent the community of users on the various
Wikimedia projects. They will help to determine the future direction
that the Wikimedia projects will take, individually and as a group, and
represent <em>your</em> interests and concerns to the Board of Trustees. They will
decide on ways to generate income and the allocation of moneys raised.</p>

<p>Please read the candidates' statements and responses to queries carefully
before voting. Each of the candidates is a respected user, who has contributed
considerable time and effort to making these projects a welcoming environment
committed to the pursuit and free distribution of human knowledge.</p>

<p>You may vote for as many candidates as you want. The
candidate with the most votes in each position will be declared the winner of that
position. In the event of a tie, a run-off election will be held.</p>

<p>For more information, see:</p>
<ul><li><a href=\"http://meta.wikipedia.org/wiki/Election_FAQ_2005\" class=\"external\">Election FAQ</a></li>
<li><a href=\"http://meta.wikipedia.org/wiki/Election_Candidates_2005\" class=\"external\">Candidates</a></li></ul>
",
"boardvote_intro_change"  => "<p>You have voted before. However you may change 
your vote using the form below. Please check the boxes next to each candidate whom 
you approve of.</p>",
"boardvote_footer"        => "&nbsp;",
"boardvote_entered"       => "Thank you, your vote has been recorded.

If you wish, you may record the following details. Your voting record is:

<pre>$1</pre>

It has been encrypted with the public key of the Election Administrators:

<pre>$2</pre>

The resulting encrypted version follows. It will be displayed publicly on [[Special:Boardvote/dump]]. 

<pre>$3</pre>

[[Special:Boardvote/entry|Back]]",
"boardvote_notloggedin"   => "You are not logged in. To vote, you must use an account
with at least $1 contributions before $2.",
"boardvote_notqualified"  => "Sorry, you made only $1 edits before $2. You 
need at least $3 to be able to vote.",
"boardvote_novotes"       => "Nobody has voted yet.",
"boardvote_time"          => "Time",
"boardvote_user"          => "User",
"boardvote_edits"         => "Edits",
"boardvote_days"          => "Days",
"boardvote_ip"            => "IP",
"boardvote_ua"            => "User agent",
"boardvote_listintro"     => "<p>This is a list of all votes which have been recorded 
to date. $1 for the encrypted data.</p>",
"boardvote_dumplink"      => "Click here",
"boardvote_strike"        => "Strike",
"boardvote_unstrike"      => "Unstrike",
"boardvote_needadmin"     => "Only election administrators can perform this operation.",
"boardvote_sitenotice"    => "<a href=\"{{localurle:Special:Boardvote/vote}}\">Wikimedia Board Elections</a>:  Vote open until July 11",
));
/*
global $wgSiteNotice, $wgUser;

$title = Title::makeTitle( NS_SPECIAL, "Boardvote/vote" );

if ( $wgUser->getID() != 0 ) {
	$wgSiteNotice = wfMsg( "boardvote_sitenotice" );
}
*/

} # End of extension function

?>
