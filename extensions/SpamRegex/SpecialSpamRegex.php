<?php
/**
 * Protect against register_globals vulnerabilities.
 * This line must be present before any global variable is referenced.
 */
if(!defined('MEDIAWIKI'))
	die();

class SpamRegex extends SpecialPage {

	/**
	* Constructor
	*/
	function __construct() {
		parent::__construct( 'SpamRegex', 'spamregex', true, 'execute', false );
		wfLoadExtensionMessages('SpamRegex');
	}

	/**
	 * Show the special page
	 *
	 * @param $par Mixed: parameter passed to the page or null
	 */
	function execute( $par ) {
		global $wgOut, $wgUser, $wgRequest;

		if ( $wgUser->isBlocked() ) {
			$wgOut->blockedPage();
			return;
		}        
		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();     
			return;
		}
		if ( !$wgUser->isAllowed( 'spamregex' ) ) {
			$this->displayRestrictionError();
			return;
		}

		$wgOut->setPageTitle(wfMsgHtml('spamregex-page-title'));
		$sRF = new spamRegexForm($par);
		$sRL = new spamRegexList($par);

		$action = $wgRequest->getVal( 'action' );
		if ( 'success_block' == $action ) {
			$sRF->showSuccess();
			$sRF->showForm('');
		} else if ( 'success_unblock' == $action ) {
			$sRL->showSuccess();
			$sRF->showForm('');
		} else if ( 'failure_unblock' == $action ) {
			$text = htmlspecialchars ($wgRequest->getVal('text'));
			$sRF->showForm( wfMsg('spamregex-error-unblocking', $text) );
		} else if ( $wgRequest->wasPosted() && 'submit' == $action &&
			$wgUser->matchEditToken( $wgRequest->getVal('wpEditToken') ) ) {
			$sRF->doSubmit();
		} else if ('delete' == $action) {
			$sRL->deleteFromList();
		} else {
			$sRF->showForm('');
		}
		$sRL->showList('', $offset );
	}
}

/* the list of blocked phrases */
class spamRegexList {
	var $mRegexUnblockedAddress;
	var $numResults = 0;

	/* constructor */
	function regexBlockList ( $par ) {
	}

	/* wrapper for GET values */
	function wfSpamRegexGetListBits() {
		global $wgRequest;
		$pieces = array();
		list( $limit, $offset ) = $wgRequest->getLimitOffset();
		$pieces[] = 'limit=' . $limit;
		$pieces[] = 'offset=' . $offset;
		$bits = implode( '&', $pieces );
		return $bits;
	}

	/* useful for cleaning the memcached keys */
	function wfSpamRegexUnsetKeys() {
		global $wgMemc, $wgSharedDB;
		$wgMemc->delete("$wgSharedDB:spamRegexCore:spamRegex:Textbox");
		$wgMemc->delete("$wgSharedDB:spamRegexCore:spamRegex:Summary");
		$wgMemc->delete("$wgSharedDB:spamRegexCore:numResults");
	}

	/**
	 * Output list of blocked expressions
	 *
	 * @param $err string: error message
	 */
	function showList( $err ) {
		wfProfileIn( __METHOD__ );
		global $wgOut, $wgRequest, $wgMemc, $wgLang;

		/* on error, display error */
		if ( "" != $err ) {
			$wgOut->addHTML("<p class='error'>{$err}</p>\n");
		}
		$titleObj = Title::makeTitle( NS_SPECIAL, 'SpamRegex' );
		$action = $titleObj->escapeLocalURL("") ."?".$this->wfSpamRegexGetListBits();
		$action_unblock = $titleObj->escapeLocalURL("action=delete") ."&".$this->wfSpamRegexGetListBits();
		list( $limit, $offset ) = $wgRequest->getLimitOffset();

		$wgOut->addWikiText('<br /><br />'.wfMsg('spamregex-currently-blocked'));
		$this->showPrevNext($wgOut);

		if ( 0 == $this->fetchNumResults() ) {
			$wgOut->addWikiText('<br />'.wfMsg('spamregex-no-currently-blocked').'<br /><br />');
		}

		$wgOut->addHTML("<form name=\"spamregexlist\" method=\"get\" action=\"{$action}\">");
		/* get data and play with data */
		$dbr = wfGetDB(DB_SLAVE);
		$query = "SELECT * FROM ".wfSpamRegexGetTable();
		$query .= " order by spam_text";

		$query = $dbr->limitResult($query, $limit, $offset);
		$res = $dbr->query($query);
		while ( $row = $dbr->fetchObject( $res ) ) {
			$time = $wgLang->timeanddate( wfTimestamp( TS_MW, $row->spam_timestamp ), true );
			$ublock_ip = urlencode($row->spam_text);
			$desc = "";
			if ($row->spam_textbox == 1) {
				$desc .= wfMsg('spamregex-text');
			}
			if ($row->spam_summary == 1) {
				if ($row->spam_textbox == 1) {
					$desc .= " ";
				}
				$desc .= wfMsg('spamregex-summary-log');
			}
			$wgOut->addHTML("<ul>");
			$wgOut->addWikiText(wfMsg('spamregex-log-1', $row->spam_text, $desc, $action_unblock, $ublock_ip) .$row->spam_user . wfMsg('spamregex-log-2', $time));
			$wgOut->addHTML("</ul>");
		}
		$dbr->freeResult($res);
		$wgOut->addHTML("</form>");
		$this->showPrevNext($wgOut);
		wfProfileOut( __METHOD__ );
	}

	/* remove from list - without confirmation */
	function deleteFromList() {
		wfProfileIn( __METHOD__ );
		global $wgOut, $wgRequest, $wgMemc, $wgUser;
		$text = urldecode( $wgRequest->getVal('text') );
		/* delete */
		$dbw = wfGetDB( DB_MASTER );
		$query = "DELETE FROM ".wfSpamRegexGetTable()." WHERE spam_text = ".$dbw->addQuotes($text);
		$dbw->query($query);
		$titleObj = Title::makeTitle( NS_SPECIAL, 'SpamRegex' );
		if ( $dbw->affectedRows() ) {
			/* success  */
			$this->wfSpamRegexUnsetKeys();
			wfProfileOut( __METHOD__ );
			$wgOut->redirect( $titleObj->getFullURL( 'action=success_unblock&text='.urlencode($text).'&'.$this->wfSpamRegexGetListBits() ) );
		} else {
			wfProfileOut( __METHOD__ );
			$wgOut->redirect( $titleObj->getFullURL( 'action=failure_unblock&text='.urlencode($text).'&'.$this->wfSpamRegexGetListBits() ) );
		}
	}

	/* fetch number of all rows */
	function fetchNumResults() {
		wfProfileIn( __METHOD__ );
		global $wgMemc, $wgSharedDB;

		/* we use memcached here */
		$key = wfSpamRegexGetMemcDB() . ":spamRegexCore:numResults";
		$cached = $wgMemc->get($key);
		$results = 0;
		if ( is_null($cached) ) {
			$dbr = wfGetDB(DB_SLAVE);
			$query_count = "SELECT COUNT(*) as n FROM ".wfSpamRegexGetTable();
			$res_count = $dbr->query($query_count);
			$row_count = $dbr->fetchObject($res_count);
			$results = $row_count->n;
			$wgMemc->set($key, $results, REGEXBLOCK_EXPIRE);
			$dbr->freeResult($res_count);
		} else {
			$results = $cached;
		}
		$this->numResults = $results;
		wfProfileOut( __METHOD__ );
		return $results;
	}

	/* draws one option for select */
	function makeOption($blocker, $current) {
		global $wgOut;
		if ($blocker == $current) {
			$wgOut->addHTML("<option selected=\"selected\" value=\"{$blocker}\">{$blocker}</option>");
		} else {
			$wgOut->addHTML("<option value=\"{$blocker}\">{$blocker}</option>");
		}
	}

	/* on success */
	function showSuccess() {
		global $wgOut, $wgRequest;
		$wgOut->setPageTitle(wfMsg('spamregex-page-title-1'));
		$wgOut->setSubTitle(wfMsg('spamregex-unblock-success'));
		$wgOut->addWikiText(wfMsg('spamregex-unblock-message', $wgRequest->getVal('text', $par)));
	}

	/* init for showprevnext */
	function showPrevNext( &$out ) {
		global $wgContLang, $wgRequest;
		list( $limit, $offset ) = $wgRequest->getLimitOffset();
		$html = wfViewPrevNext(
				$offset,
				$limit,
				$wgContLang->specialpage( 'SpamRegex' ),
				'',
				($this->numResults - $offset) <= $limit
			);
		$out->addHTML( '<p>' . $html . '</p>' );
	}
}

/* the form for blocking phrases */
class spamRegexForm {
	var $mBlockedPhrase;
	var $mBlockedText;
	var $mBlockedSummary;

	/* constructor */
	function spamRegexForm( $par ) {
		global $wgRequest;
		$this->mBlockedPhrase = $wgRequest->getVal( 'wpBlockedPhrase',  $wgRequest->getVal( 'text', $par ) );
		($wgRequest->getVal('wpBlockedTextbox') ) ? $this->mBlockedTextbox = 1 : $this->mBlockedTextbox = 0;
		($wgRequest->getVal('wpBlockedSummary') ) ? $this->mBlockedSummary = 1 : $this->mBlockedSummary = 0;
	}

	/* output */
	function showForm( $err ) {
		global $wgOut, $wgUser, $wgRequest;

		$token = htmlspecialchars( $wgUser->editToken() );
		$titleObj = Title::makeTitle( NS_SPECIAL, 'SpamRegex' );
		$action = $titleObj->escapeLocalURL( "action=submit" )."&".spamRegexList::wfSpamRegexGetListBits();

		if ( "" != $err ) {
			$wgOut->setSubtitle( wfMsgHtml( 'formerror' ) );
			$wgOut->addHTML( "<p class='error'>{$err}</p>\n" );
		}

		$wgOut->addWikiMsg('spamregex-intro');

		( 'submit' == $wgRequest->getVal( 'action' )) ? $scBlockedPhrase = htmlspecialchars ($this->mBlockedPhrase) : $scBlockedPhrase = '';

		$wgOut->addScript("
			<script type=\"text/javascript\">
				function SpamRegexEnhanceControls() {
					var SRTextboxControl = document.getElementById('wpBlockedTextbox');
					var SRSummaryControl = document.getElementById('wpBlockedSummary');

					SRTextboxControl.onclick = function () {
						if (!SRTextboxControl.checked) {
							if (!SRSummaryControl.checked) {
								SRSummaryControl.checked = true;
							}
						}
					}

					SRSummaryControl.onclick = function () {
						if (!SRSummaryControl.checked) {
							if (!SRTextboxControl.checked) {
								SRTextboxControl.checked = true;
							}
						}
					}
				}

				addOnloadHook (SpamRegexEnhanceControls);
			</script>"
		);
		$phraseblock = wfMsg('spamregex-phrase-block');
		$phraseblocktext = wfMsg('spamregex-phrase-block-text');
		$phraseblocksummary = wfMsg('spamregex-phrase-block-summary');
		$blockphrase = wfMsg('spamregex-block-submit');
		$wgOut->addHTML("
<form name=\"spamregex\" method=\"post\" action=\"{$action}\">
	<table border=\"0\">
		<tr>
			<td align=\"right\">{$phraseblock}</td>
			<td align=\"left\">
				<input tabindex=\"1\" name=\"wpBlockedPhrase\" value=\"{$scBlockedPhrase}\" />
			</td>
		</tr>
		<tr>
			<td align=\"right\">&#160;</td>
			<td align=\"left\">
			<input type=\"checkbox\" tabindex=\"2\" name=\"wpBlockedTextbox\" id=\"wpBlockedTextbox\" value=\"1\" checked=\"checked\" />
			<label for=\"wpBlockedTextbox\">{$phraseblocktext}</label>
			</td>
		</tr>
		<tr>
			<td align=\"right\">&#160;</td>
			<td align=\"left\">
			<input type=\"checkbox\" tabindex=\"3\" name=\"wpBlockedSummary\" id=\"wpBlockedSummary\" value=\"1\" />
			<label for=\"wpBlockedSummary\">{$phraseblocksummary}</label>
			</td>
		</tr>
		<tr>
			<td align=\"right\">&#160;</td>
			<td align=\"left\">
				<input tabindex=\"4\" name=\"wpSpamRegexBlockedSubmit\" type=\"submit\" value=\"{$blockphrase}\" />
			</td>
		</tr>
	</table>
	<input type='hidden' name='wpEditToken' value=\"{$token}\" />
</form>");
	}

	/* on success */
	function showSuccess() {
		global $wgOut, $wgRequest;
		$wgOut->setPageTitle(wfMsg('spamregex-page-title-2'));
		$wgOut->setSubTitle(wfMsg('spamregex-block-success'));

		$wgOut->addWikiMsg('spamregex-block-message', $this->mBlockedPhrase);
	}

	/* on submit */
	function doSubmit() {
		wfProfileIn( __METHOD__ );
		global $wgOut, $wgUser, $wgMemc;

		/* empty name */
		if ( strlen($this->mBlockedPhrase) == 0 ) {
			$this->showForm(wfMsgHtml('spamregex-warning-1'));
			wfProfileOut( __METHOD__ );
			return;
		}
		/* validate expression */
		if ( !$simple_regex = wfValidRegex($this->mBlockedPhrase) ) {
			$this->showForm(wfMsgHtml('spamregex-error-1'));
			wfProfileOut( __METHOD__ );
			return;
		}

		/* make insert */
		$dbw = wfGetDB( DB_MASTER );
		$name = $wgUser->getName();
		$timestamp = wfTimestampNow();

		/* we need at least one block mode specified... we can have them both, of course */
		if ( ($this->mBlockedTextbox == 0) && ($this->mBlockedSummary == 0) ) {
			$this->showForm(wfMsgHtml('spamregex-warning-2'));
			wfProfileOut( __METHOD__ );
			return;
		}

		$query = "INSERT IGNORE INTO ".wfSpamRegexGetTable()
			  ." (spam_id, spam_text, spam_timestamp, spam_user, spam_textbox, spam_summary)
			  VALUES (null,
			  	  {$dbw->addQuotes($this->mBlockedPhrase)},
				  {$timestamp},
				  {$dbw->addQuotes($name)},
				  {$this->mBlockedTextbox},
				  {$this->mBlockedSummary}
				 )";
		$dbw->query($query);

		/* duplicate entry */
		if (!$dbw->affectedRows()) {
			$this->showForm(wfMsgHtml('spamregex-already-blocked', $this->mBlockedPhrase));
			wfProfileOut( __METHOD__ );
			return;
		}
		spamRegexList::wfSpamRegexUnsetKeys($name);
		/* redirect */
		$titleObj = Title::makeTitle( NS_SPECIAL, 'SpamRegex' );
		wfProfileOut( __METHOD__ );
		$wgOut->redirect( $titleObj->getFullURL( 'action=success_block&text=' .urlencode( $this->mBlockedPhrase )."&".spamRegexList::wfSpamRegexGetListBits() ) );
	}
}