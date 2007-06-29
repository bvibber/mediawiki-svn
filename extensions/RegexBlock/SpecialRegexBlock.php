<?php

/**#@+
*	A special page with the interface for blocking, viewing and unblocking 
	user names and IP addresses
*
* @addtogroup SpecialPage
*
* @author Bartek
* @copyright Copyright © 2007, Wikia Inc.
* @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
*/

if(!defined('MEDIAWIKI'))
   die();

$wgAvailableRights[] = 'regexblock';
$wgGroupPermissions['staff']['regexblock'] = true;

$wgExtensionFunctions[] = 'wfRegexBlockSetup';
$wgExtensionCredits['specialpage'][] = array(
   'name' => 'Regular Expression Name Block',
   'author' => 'Bartek',
   'description' => 'alternate user block (by given name, using regular expressions)'
);

/* special page init */
function wfRegexBlockSetup() {
   global $IP, $wgMessageCache;
   if (!wfSimplifiedRegexCheckSharedDB())
   	return ;
   require_once($IP. '/includes/SpecialPage.php');
   SpecialPage::addPage(new SpecialPage('Regexblock', 'regexblock', true, 'wfRegexBlockSpecial', false));
   $wgMessageCache->addMessage('regexblock', 'regexBlock');
}

/* wrapper for GET values */
function wfGetListBits () {
	global $wgRequest ;
        $pieces = array() ;
        list( $limit, $offset ) = $wgRequest->getLimitOffset() ;
        $pieces[] = 'limit=' . $limit ;
        $pieces[] = 'offset=' . $offset ;
	$pieces[] = 'filter=' . urlencode ($wgRequest->getVal ('filter') );
        $bits = implode( '&', $pieces ) ;
        return $bits ;
}

/* draws one option for select */
function wfRegexBlockMakeOption ($name, $value, $current) {
	global $wgOut ;
	if ($value == $current) {
		$wgOut->addHTML ("<option selected=\"selected\" value=\"{$value}\">{$name}</option>") ;	
	} else {
		$wgOut->addHTML ("<option value=\"{$value}\">{$name}</option>") ;		
	}
}

/* the core */
function wfRegexBlockSpecial( $par ) {
	global $wgOut, $wgUser, $wgRequest ;
   	$wgOut->setPageTitle("Regular Expression Name Block");
	$rBS = new regexBlockForm ($par) ;
	$rBL = new regexBlockList ($par) ;

   	$action = $wgRequest->getVal( 'action' );
   	if ( 'success_block' == $action ) {
   		$rBS->showSuccess() ;
		$rBS->showForm ('') ;
	} else if ( 'success_unblock' == $action ) {
   		$rBL->showSuccess() ;
		$rBS->showForm ('') ;
	} else if ( 'failure_unblock' == $action ) {
		$ip = $wgRequest->getVal ('ip') ;
		$rBS->showForm ("Error unblocking \"{$ip}\". Probably there is no such user.") ;
   	} else if ( $wgRequest->wasPosted() && 'submit' == $action &&
   		$wgUser->matchEditToken( $wgRequest->getVal ('wpEditToken') ) ) {
        	$rBS->doSubmit () ;
   	} else if ('delete' == $action) {
		$rBL->deleteFromRegexBlockList () ;
	} else {
   		$rBS->showForm ('') ;
   	}
		$rBL->showList ('', $offset ) ;
}

/* useful for cleaning the memcached keys */
function wfRegexBlockUnsetKeys ($blocker, $username) {
	global $wgMemc, $wgSharedDB ;
	$wgMemc->delete ("$wgSharedDB:regexBlockSpecial:numResults") ;
	$wgMemc->delete ("$wgSharedDB:regexBlockCore:".REGEXBLOCK_MODE_NAMES.":blocker:$blocker") ;
	$wgMemc->delete ("$wgSharedDB:regexBlockCore:".REGEXBLOCK_MODE_IPS.":blocker:$blocker") ;
	$wgMemc->delete ("$wgSharedDB:regexBlockCore:blockers") ;
	$wgMemc->delete ("$wgSharedDB:regexBlockCore:blocked:$username") ;
}

/* the list of blocked names/addresses */
class regexBlockList {
	var $mRegexUnblockedAddress ;
	var $numResults = 0 ;

	/* constructor */
	function regexBlockList ( $par ) {
	}
	
	/* output list */
	function showList ( $err ) {
		global $wgOut, $wgRequest, $wgMemc, $wgLang, $wgUser ;

		/* on error, display error */
		if ( "" != $err ) {
			$wgOut->addHTML ("<p class='error'>{$err}</p>\n") ;
		}
                $titleObj = Title::makeTitle( NS_SPECIAL, 'Regexblock' );
                $action = $titleObj->escapeLocalURL("") ."?".wfGetListBits() ;
		$action_unblock = $titleObj->escapeLocalURL("action=delete") ."&".wfGetListBits() ;
		list( $limit, $offset ) = $wgRequest->getLimitOffset() ;

		$wgOut->addWikiText ("<br/><b>Currently blocked addresses:</b>") ;
		$this->fetchNumResults () ;
		$this->showPrevNext ($wgOut) ;	
	
		$wgOut->addHTML ("<form name=\"regexlist\" method=\"get\" action=\"{$action}\">") ;

		/* allow display by specific blockers only */
		$wgOut->addHTML ("View blocked by: <select name=\"filter\"><option value=\"\">All</option>") ;
		$blockers =  $this->fetchBlockers () ;
		$wgOut->addHTML("</select>&#160;<input type=\"submit\" value=\"Go\"><br/><br/>
		") ;	
		$current = $wgRequest->getVal ('filter') ;

		if ($blockers) {
			/* get data and play with data */
		        $dbr = &wfGetDB (DB_SLAVE) ;
			$query = "SELECT * FROM ".wfRegexBlockGetTable() ;
			if ('' != $current) {
				$query .= " WHERE blckby_blocker = {$dbr->addQuotes($current)}" ;
			}
			/* righto, order by name */
			$query .= " order by blckby_timestamp DESC" ;
		
			$query = $dbr->limitResult ($query, $limit, $offset) ;
			$res = $dbr->query ($query) ;
			$wgOut->addHTML ("<ul>") ;

			/* output a single row*/
		        while ( $row = $dbr->fetchObject( $res ) ) {
				$ublock_ip = urlencode ($row->blckby_name) ;
				$ublock_blocker = urlencode ($row->blckby_blocker) ;

				$row->blckby_exact ? $exact_match = "(exact match)" : $exact_match = '(regex match)' ;
				$row->blckby_create ? $create_block = "(account creation block)" : $create_block = '' ;
				$row->blckby_reason ? $reason = "<i>reason: {$row->blckby_reason}</i>" : $reason = '<i>generic reason</i>' ;

				$time = $wgLang->timeanddate( wfTimestamp( TS_MW, $row->blckby_timestamp ), true ) ;

				/* if this block already expired, show it */
				$expiry = $row->blckby_expire ;
				if ( (wfTimestampNow () <= $expiry) || ('infinite' == $expiry) ) {
					$expiry == 'infinite' ? $expires = 'permanent block' : $expires = "expires on ".$wgLang->timeanddate( wfTimestamp( TS_MW, $expiry ), true ) ; 
				} else {
                                	$expires = "<span style=\"color: #ff0000\">EXPIRED on {$wgLang->timeanddate( wfTimestamp( TS_MW, $expiry ), true )}</span>" ;
				}
				$sk = $wgUser->getSkin () ;
				$stats_link = $sk->makeKnownLinkObj( Title::makeTitle( NS_SPECIAL, 'Regexblockstats' ), '(stats)', 'target=' . urlencode($row->blckby_name)) ;
				$wgOut->addHTML ("
					<li><b>{$row->blckby_name} {$exact_match} {$create_block}</b> (blocked by: <b>{$row->blckby_blocker}</b>, {$reason}) on {$time} (<a href=\"{$action_unblock}&ip={$ublock_ip}&blocker={$ublock_blocker}\">unblock</a>) {$expires} {$stats_link}</li>
				") ;	
	       		}

	        $dbr->freeResult ($res) ;
		$wgOut->addHTML ("</ul></form>") ;
		} else { /* empty list */
			$wgOut->addHTML ("The list of blocked names and addresses is empty.<br/><br/>") ;
		}
		$this->showPrevNext ($wgOut) ;	
	}

        /* a plain html link wrapper */
        function produceLink ($url, $link, $text) {
                return $html_link = ("<a href=\"$url$link\">$text</a>") ;
        }

	/* remove name or address from list - without confirmation */
	function deleteFromRegexBlockList () {
		global $wgOut, $wgRequest, $wgMemc, $wgUser ;
		$ip = $wgRequest->getVal('ip');
		$blocker = $wgRequest->getVal('blocker') ;
		/* delete */
                $dbw =& wfGetDB( DB_MASTER );
		$query = "DELETE FROM ".wfRegexBlockGetTable()." WHERE blckby_name = ".$dbw->addQuotes($ip) ;
		$dbw->query ($query) ;
	        $titleObj = Title::makeTitle( NS_SPECIAL, 'Regexblock' ) ;
		if ( $dbw->affectedRows() ) {
			/* success  */
			wfRegexBlockUnsetKeys ($blocker, $ip) ;
                	$wgOut->redirect( $titleObj->getFullURL( 'action=success_unblock&ip='.urlencode($ip).'&'.wfGetListBits() ) ) ;
		} else {
			$wgOut->redirect( $titleObj->getFullURL( 'action=failure_unblock&ip='.urlencode($ip).'&'.wfGetListBits() ) ) ;
		}
	}	

	/* fetch names of all blockers and write them into select's options */
	function fetchBlockers () {
		global $wgOut, $wgRequest, $wgMemc, $wgSharedDB ;
		/* memcached */
        	$key = "$wgSharedDB:regexBlockCore:blockers" ;
		$current = $wgRequest->getVal ('filter') ;
		$cached = $wgMemc->get ($key) ;
		$fetched = 0 ;
		if (!is_array($cached)) {
			/* get from database */
			$blockers_array = array () ;
		        $dbr =& wfGetDB (DB_SLAVE);
	        	$query = "SELECT blckby_blocker FROM ".wfRegexBlockGetTable() ;
			$query .= " GROUP BY blckby_blocker" ;
		        $res = $dbr->query($query) ;
		        while ( $row = $dbr->fetchObject( $res ) ) {
				wfRegexBlockmakeOption ($row->blckby_blocker, $row->blckby_blocker, $current) ;
				array_push ($blockers_array, $row->blckby_blocker) ;
		        }
			$fetched = $dbr->numRows ($res) ; 
        		$dbr->freeResult ($res) ;
			$wgMemc->set ($key, $blockers_array) ;
		} else {
			/* get from memcached */
			foreach ($cached as $blocker) {
				wfRegexBlockmakeOption ($blocker, $blocker, $current) ;
				$fetched++ ;
			}
		}
			return $fetched ;
	}

	/* fetch number of all rows */
	function fetchNumResults () {
		global $wgMemc, $wgSharedDB ;

                /* we use memcached here */
		$key = "$wgSharedDB:regexBlockSpecial:numResults" ;
		$cached = $wgMemc->get ($key) ;
                if (is_null ($cached)) {
		        $dbr = &wfGetDB (DB_SLAVE) ;	
			$query_count = "SELECT COUNT(*) as n FROM ".wfRegexBlockGetTable() ;
			$res_count = $dbr->query($query_count) ;
			$row_count = $dbr->fetchObject ($res_count);
			$this->numResults = $row_count->n ;
                	$wgMemc->set ($key, $this->numResults, REGEXBLOCK_EXPIRE) ;
			$dbr->freeResult ($res_count) ;
		} else {
			$this->numResults = $cached ;
		}
	}

	/* on success */
	function showSuccess () {
		global $wgOut, $wgRequest ;
		$wgOut->setPageTitle('Block address using regular expressions') ;
		$wgOut->setSubTitle('Unblock succedeed') ;	
		$wgOut->addWikiText('User name or IP address <b>'.htmlspecialchars($wgRequest->getVal('ip', $par)).'</b> has been unblocked.') ;
	}

	/* init for showprevnext */
        function showPrevNext( &$out ) {
                global $wgContLang,$wgRequest;
                list( $limit, $offset ) = $wgRequest->getLimitOffset();
		$filter = 'filter=' . urlencode ( $wgRequest->getVal ('filter') ) ;
                $html = wfViewPrevNext( 
				$offset,
				$limit,
                        	$wgContLang->specialpage( 'Regexblock' ),
                        	$filter,
				($this->numResults - $offset) <= $limit
			);
                $out->addHTML( '<p>' . $html . '</p>' );
        }
}

/* the form for blocking names and addresses */
class regexBlockForm {
	var $mRegexBlockedAddress, $mRegexBlockedExact, $mRegexBlockedCreation, $mRegexBlockedExpire ;

	/* constructor */
	function regexBlockForm ( $par ) {
		global $wgRequest ;
		$this->mRegexBlockedAddress = $wgRequest->getVal( 'wpRegexBlockedAddress',  $wgRequest->getVal( 'ip', $par ) );
		$this->mRegexBlockedExact = $wgRequest->getInt ('wpRegexBlockedExact') ;
		$this->mRegexBlockedCreation = $wgRequest->getInt ('wpRegexBlockedCreation') ;
		$this->mRegexBlockedExpire = $wgRequest->getVal ('wpRegexBlockedExpire') ;
		$this->mRegexBlockedReason = $wgRequest->getVal ('wpRegexBlockedReason') ;
	}

	/* output */
	function showForm ( $err ) {
		global $wgOut, $wgUser, $wgRequest ;
	
		$token = htmlspecialchars( $wgUser->editToken() );
		$titleObj = Title::makeTitle( NS_SPECIAL, 'Regexblock' );
		$action = $titleObj->escapeLocalURL( "action=submit" )."&".wfGetListBits() ;

                if ( "" != $err ) {
                        $wgOut->setSubtitle( wfMsgHtml( 'formerror' ) );
                        $wgOut->addHTML( "<p class='error'>{$err}</p>\n" );
                }
	
		$wgOut->addWikiText (REGEXBLOCK_HELP) ;

		if ( 'submit' == $wgRequest->getVal( 'action' )) {
			$scRegexBlockedAddress = htmlspecialchars ($this->mRegexBlockedAddress) ;
			$scRegexBlockedExpire = htmlspecialchars ($this->mRegexBlockedExpire) ;
			$scRegexBlockedReason = htmlspecialchars ($this->mRegexBlockedReason) ;
			$this->mRegexBlockedExact ? $checked_ex = "checked=\"checked\"" : $checked_ex = "" ;
			$this->mRegexBlockedCreation ? $checked_cr = "checked=\"checked\"" : $checked_cr = "" ;
		} else {
			$scRegexBlockedAddress = '' ;
			$checked_ex = '' ;
			$checked_cr = '' ;
		}

   		$wgOut->addHtml("
<form name=\"regexblock\" method=\"post\" action=\"{$action}\">
	<table border=\"0\">
		<tr>
			<td align=\"right\">IP Adress or username:</td>
			<td align=\"left\">
				<input tabindex=\"1\" name=\"wpRegexBlockedAddress\" size=\"40\" value=\"{$scRegexBlockedAddress}\" />
			</td>
		</tr>
		<tr>
			<td align=\"right\">Reason:</td>
			<td align=\"left\">
				<input tabindex=\"2\" name=\"wpRegexBlockedReason\" size=\"40\" value=\"{$scRegexBlockedReason}\" />
			</td>
		</tr>
                <tr>
                        <td align=\"right\">Expiry:&#160;</td>
                        <td align=\"left\">
                                <select name=\"wpRegexBlockedExpire\" tabindex=\"3\">");
			$expiries = array (
					'1 hour',
					'2 hours',
					'4 hours',
					'6 hours',
					'1 day',
					'3 days',
					'1 week',
					'2 weeks',
					'1 month',
					'3 months',
					'1 year',
					'infinite'					
					) ;                               
			foreach ($expiries as $duration) {
                         	wfRegexBlockMakeOption ($duration, $duration, $scRegexBlockedExpire) ;
			}
		      $wgOut->addHTML("</select>
                        </td>
                </tr>
                <tr>
                        <td align=\"right\">&#160;</td>
                        <td align=\"left\">
                                <input type=\"checkbox\" tabindex=\"4\" name=\"wpRegexBlockedExact\" id=\"wpRegexBlockedExact\" value=\"1\" $checked_ex />
                                <label for=\"wpRegexBlockedExact\">Exact match</label>
                        </td>
                </tr>
                <tr>
                        <td align=\"right\">&#160;</td>
                        <td align=\"left\">
                                <input type=\"checkbox\" tabindex=\"5\" name=\"wpRegexBlockedCreation\" id=\"wpRegexBlockedCreation\" value=\"1\" $checked_cr />
                                <label for=\"wpRegexBlockedCreation\">Block creation of new accounts</label>
                        </td>
                </tr>
                <tr>
                        <td align=\"right\">&#160;</td>
                        <td align=\"left\">
                                <input tabindex=\"6\" name=\"wpRegexBlockedSubmit\" type=\"submit\" value=\"Block this user\" />
                        </td>
                </tr>
	</table>
	<input type='hidden' name='wpEditToken' value=\"{$token}\" />
</form>");
	}

	/* on success */
	function showSuccess () {
		global $wgOut ;
		$wgOut->setPageTitle ('Block address using regular expressions') ;
		$wgOut->setSubTitle ('Block succedeed') ;	

		$wgOut->addWikiText ('User name or IP address <b>'.htmlspecialchars($this->mRegexBlockedAddress).'</b> has been blocked.') ;
	}

	/* on submit */
	function doSubmit () {
		global $wgOut, $wgUser, $wgMemc ;

		/* empty name */
		if ( strlen($this->mRegexBlockedAddress) == 0 ) {
			$this->showForm ("Give a user name or an IP address to block.") ;	
			return ;
		}

		/* castrate regexes */
		if (!$simple_regex = wfValidRegex ($this->mRegexBlockedAddress) ) {
			/* now, very generic comment - should the conditions change, this should too */
			$this->showForm ("Invalid regular expression.") ;       			
	       		return ;
		}
        
		/* check expiry */
		if ( strlen ($this->mRegexBlockedExpire) == 0 ) {
			$this->showForm ("Please specify an expiration period.") ;	
			return ;
		}

		/* TODO - check infinite */			
		if ($this->mRegexBlockedExpire != 'infinite') {
  		$expiry = strtotime( $this->mRegexBlockedExpire );
                        if ( $expiry < 0 || $expiry === false ) {
                                $this->showForm( wfMsg( 'ipb_expiry_invalid' ) );
                                return;
                        }
                $expiry = wfTimestamp( TS_MW, $expiry );
		} else {
			$expiry = $this->mRegexBlockedExpire ;	
		}

		/* make insert */
		$dbw =& wfGetDB( DB_MASTER );
		$name = $wgUser->getName () ;
		$timestamp =  wfTimestampNow() ;	

		$query = "INSERT IGNORE INTO ".wfRegexBlockGetTable()." 
			  (blckby_id, blckby_name, blckby_blocker, blckby_timestamp, blckby_expire, blckby_exact, blckby_create, blckby_reason) 
			  VALUES (null,
				  {$dbw->addQuotes($this->mRegexBlockedAddress)},
				  {$dbw->addQuotes($name)}, 
				  '{$timestamp}', 
				  '{$expiry}', 
				  {$this->mRegexBlockedExact}, 
				  {$this->mRegexBlockedCreation}, 
				  {$dbw->addQuotes($this->mRegexBlockedReason)}
				 )" ;
		$dbw->query ($query) ;
		/* duplicate entry */
		if (!$dbw->affectedRows()) {
			$this->showForm ( "\"".htmlspecialchars($this->mRegexBlockedAddress)."\" is already blocked." ) ;
			return ;
		}

		wfRegexBlockUnsetKeys ($name, $this->mRegexBlockedAddress) ;
		
		/* redirect */
        	$titleObj = Title::makeTitle( NS_SPECIAL, 'Regexblock' ) ;
              	$wgOut->redirect( $titleObj->getFullURL( 'action=success_block&ip=' .urlencode( $this->mRegexBlockedAddress )."&".wfGetListBits() ) ) ;
	}
}


