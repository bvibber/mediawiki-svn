<?php
if ( ! defined( 'MEDIAWIKI' ) )
	die();
/**
 * Allow users in the Bot group to edit many articles in one go by applying
 * regular expressions to a list of pages.
 *
 * @addtogroup SpecialPage
 *
 * @link http://www.mediawiki.org/wiki/Extension:MassEditRegex Documentation
 *
 * @author Adam Nielsen <malvineous@shikadi.net>
 * @copyright Copyright © 2009 Adam Nielsen
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

// Maximum number of pages/diffs to display when previewing the changes
define('MER_MAX_PREVIEW_DIFFS', 10);

/** Main class that define a new special page*/
class MassEditRegex extends SpecialPage {
	private $aPageList;
	private $aMatch;
	private $aReplace;
	private $strReplace; // keep to avoid having to re-escape again
	private $strSummary;
	private $sk;

	function __construct() {
		parent::__construct( 'MassEditRegex', 'bot' );
	}

	function execute( $par ) {
		global $wgUser, $wgRequest, $wgOut;

		wfLoadExtensionMessages('MassEditRegex');

		$this->setHeaders();

		#if ( !$wgUser->isAllowed( 'bot' ) ) {
		#	$wgOut->permissionRequired( 'bot' );
		#	return;
		#}

		$this->outputHeader();

		$strPageList = $wgRequest->getText( 'wpPageList', 'Sandbox' );
		$strMatch = $wgRequest->getText( 'wpMatch', '/hello (.*)\n/' );
		$strReplace = $wgRequest->getText( 'wpReplace', 'goodbye \1' );
		$strSummary = $wgRequest->getText( 'wpSummary', '' );

		$this->aPageList = explode("\n", trim($strPageList));
		//print_r($this->aPages);
		//if (count($this->aPages) == 0) $this->aPages[0] = $this->aPages;
		$this->aMatch = explode("\n", trim($strMatch));
		$this->strReplace = $strReplace;
		$this->aReplace = explode("\n", $strReplace);
		$this->strSummary = $strSummary;

		$this->sk = $wgUser->getSkin();

		// Replace \n in the match with an actual newline (since a newline can't
		// be typed in, it'll act as the splitter for the next regex)
		foreach ( $this->aReplace as &$str ) {
			// Convert \n into a newline, \\n into \n, \\\n into \<newline>, etc.
			$str = preg_replace(
				array(
					'/(^|[^\\\\])((\\\\)*)(\2)\\\\n/',
					'/(^|[^\\\\])((\\\\)*)(\2)n/'
				), array(
					"\\1\\2\n",
					"\\1\\2n"
				), $str);
		}

		if ( $wgRequest->wasPosted() ) {
			if ($wgRequest->getCheck( 'wpPreviewBtn' ) ) {
				$this->showPreview();
			} else if ( $wgRequest->getCheck('wpExecuteBtn') ) {
				$this->perform();
			}
		} else {
			$this->showForm();
			$this->showHints();
		}

	}

	function showForm( $err = '' ) {
		global $wgOut;

		if ( $err ) {
			$wgOut->addHTML('<div class="wikierror">' . htmlspecialchars($err) . '</div>');
		}

		$wgOut->addWikiMsg( 'masseditregextext' );

		$txtPageList = wfMsg( 'masseditregex-pagelisttxt' );
		$txtMatch = wfMsg( 'masseditregex-matchtxt' );
		$txtReplace = wfMsg( 'masseditregex-replacetxt' );
		$txtPreviewBtn = wfMsg( 'showpreview' );
		$txtExecuteBtn = wfMsg( 'masseditregex-executebtn' );
		
		$txtEditSummary = wfMsg( 'summary' );
		$txtSummaryPreview = wfMsg( 'summary-preview' );
		
		$titleObj = SpecialPage::getTitle( 'MassEditRegex' );
		$action = $titleObj->escapeLocalURL('action=submit');

		$htmlPageList = htmlspecialchars( join( "\n", $this->aPageList ) );
		$htmlMatch = htmlspecialchars( join( "\n", $this->aMatch ) );
		$htmlReplace = htmlspecialchars( $this->strReplace ); // use original value
		$htmlSummary = htmlspecialchars( $this->strSummary );
		$htmlSummaryPreview = $this->sk->commentBlock( $this->strSummary, $titleObj );

		$mainForm = <<<ENDFORM
<form id="masseditregex" method="post" action="{$action}">
<p>{$txtPageList}</p>
<!-- Newlines are important here - one after <textarea> but none
     before </textarea>, otherwise leading blank lines get cut
     off, or trailing newlines get added!  Tested FF3 -->
<textarea name="wpPageList" cols="80" rows="4" tabindex="1" style="width:100%;">
{$htmlPageList}</textarea>

<table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
<tr><td>
<p>{$txtMatch}</p>
<textarea name="wpMatch" cols="80" rows="4" tabindex="1" style="width:95%;">
{$htmlMatch}</textarea>
</td><td>
<p>{$txtReplace}</p>
<textarea name="wpReplace" cols="80" rows="4" tabindex="1" style="width:100%;">
{$htmlReplace}</textarea>
</td></tr>
</table>
<p></p>
<div class="editOptions">
<span id="wpSummaryLabel"><label for="wpSummary">{$txtEditSummary}</label></span>
<input type="text" value="$htmlSummary" name="wpSummary" id="wpSummary"
maxlength="200" size="60" /><br />

<div class="mw-summary-preview">
$txtSummaryPreview
$htmlSummaryPreview
</div>
</div>

<p>
	<input type="submit" name="wpPreviewBtn" value="{$txtPreviewBtn}" />
	<input type="submit" name="wpExecuteBtn" value="{$txtExecuteBtn}" />
</p>
</form>
ENDFORM;
		$wgOut->addHTML( $mainForm );
	}

	function showHints() {
		global $wgOut;
		$hintIntro = wfMsg( 'masseditregex-hint-intro' );
		$hintMatch = wfMsg( 'masseditregex-hint-headmatch' );
		$hintReplace = wfMsg( 'masseditregex-hint-headreplace' );
		$hintEffect = wfMsg( 'masseditregex-hint-headeffect' );
		$hintToAppend = wfMsg( 'masseditregex-hint-toappend' );
		$hintRemove = wfMsg( 'masseditregex-hint-remove' );
		$hintRemoveCat = wfMsg( 'masseditregex-hint-removecat' );
	
		$hints = <<<ENDHINTS
<p>{$hintIntro}</p>
<table border="1" cellspacing="0" cellpadding="2" class="wikitable">
<thead><tr>
	<th style="width: 12em;">{$hintMatch}</th>
	<th style="width: 12em;">{$hintReplace}</th>
	<th>{$hintEffect}</th>
</tr></thead>
<tbody>
	<tr>
		<td>/$/<br/>/$/</td><td>abc<br/>\\n[[Category:New]]</td><td>{$hintToAppend}</td>
	</tr><tr>
		<td>{{OldTemplate}}</td><td></td><td>{$hintRemove}</td>
	</tr><tr>
		<td>\\[\\[Category:[^]]+\]\]</td><td></td><td>{$hintRemoveCat}</td>
	</tr>
</tbody>
</table>
ENDHINTS;
		$wgOut->addHTML( $hints );
	}
	
	function showPreview() {
		$this->perform( false );
		return;
	}
	
	function getPages() {
		if ( !count( $this->aPageList ) ) return NULL;
		$req = new FauxRequest( array(
			'action' => 'query',
			'titles' => join( '|', $this->aPageList ),
			'prop' => 'info|revisions',
			'intoken' => 'edit',
			'rvprop' => 'content',
			//'rvlimit' => 1  // most recent revision only
		), false );
		$processor = new ApiMain( $req, true );
		$processor->execute();
		$aPages = $processor->getResultData();
		if ( empty( $aPages ) ) return NULL; // no pages match the titles given
		return $aPages['query']['pages'];
	}

	function perform( $bPerformEdits = true ) {
		global $wgOut, $wgUser, $wgTitle;

		$aPages = $this->getPages();
		if ( $aPages === NULL ) {
			$this->showForm( wfMsg( 'err-nopages' ) );
			return;
		}
		
		// Show the form again ready for further editing if we're just previewing
		if (!$bPerformEdits) $this->showForm();
		
		$diff = new DifferenceEngine();
		$diff->showDiffStyle(); // send CSS link to the browser for diff colours

		// Save the state until the MW Edit API does it for us
		if ( $bPerformEdits ) {
			$wgOut->addHTML( '<ul>' );
			$o_wgOut = clone $wgOut; // need to do a deep copy here
			$wgOut->disable(); // not strictly necessary, but might speed things up
			$o_wgTitle = $wgTitle;
		}

		$iArticleCount = 0;
		foreach ( $aPages as $p ) {
			$iArticleCount++;
			if ( !isset( $p['revisions'] ) ) {
				if ( $bPerformEdits ) {
					$o_wgOut->addHTML( '<li>' );
					$o_wgOut->addWikiMsg( 'masseditregex-page-not-exists', $p['title'] );
					$o_wgOut->addHTML( '</li>' );
				} else {
					$wgOut->addWikiMsg( 'masseditregex-page-not-exists', $p['title'] );
				}
				continue; // empty page
			}
			$curContent = $p['revisions'][0]['*'];
			$iCount = 0;
			$newContent = @preg_replace( $this->aMatch, $this->aReplace, $curContent, -1, $iCount );

			if ( $bPerformEdits ) {
				// Not in preview mode, make the edits
				// print_r( $p );
				$o_wgOut->addHTML( '<li>' );
				$o_wgOut->addWikiMsg( 'masseditregex-num-changes', $p['title'], $iCount );
				$o_wgOut->addHTML( '</li>' );
				
				$req = new FauxRequest( array(
					'action' => 'edit',
					'bot' => true,
					'token' => $p['edittoken'],
					'title' => $p['title'],
					'summary' => $this->strSummary,
					'text' => $newContent,
					'basetimestamp' => $p['starttimestamp']
				), true );
				$processor = new ApiMain( $req, true );
				try {
					$processor->execute();
				} catch ( UsageException $e ) {
					$o_wgOut->addHTML('<li><ul><li>Edit failed: ' . $e . '</li></ul></li>');
				}
			} else {
				// In preview mode, display the first few diffs
				$diff->setText( $curContent, $newContent );
				$dtxt = $diff->getDiff( '<b>' . $p['title'] . ' - ' . wfMsg('masseditregex-before') . '</b>',
					'<b>' . wfMsg('masseditregex-after') . '</b>' );
				$wgOut->addHTML($dtxt);

				if ( $iArticleCount >= MER_MAX_PREVIEW_DIFFS ) {
					$wgOut->addWikiMsg( 'max-preview-diffs', MER_MAX_PREVIEW_DIFFS );
					break;
				}
			}

		}
		// Restore the state after the Edit API has messed with it
		if ( $bPerformEdits ) {
			$wgTitle = $o_wgTitle;
			$wgOut = $o_wgOut;
		}

		if ( $bPerformEdits ) {
			$wgOut->addHTML( '</ul>' );
			$wgOut->addWikiMsg( 'masseditregex-num-articles-changed', $iArticleCount );
			$wgOut->addHTML( 
				$this->sk->makeKnownLinkObj(
					SpecialPage::getSafeTitleFor( 'Contributions', $wgUser->getName() ),
					wfMsgHtml( 'masseditregex-view-full-summary' )
				)
			);
		}
	}
}

