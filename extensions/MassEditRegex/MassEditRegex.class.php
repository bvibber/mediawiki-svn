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

// Maximum number of pages to edit *for each name in page list*.  No more than
// 500 (5000 for bots) is allowed according to MW API docs.
define('MER_MAX_EXECUTE_PAGES', 1000);

/** Main class that define a new special page*/
class MassEditRegex extends SpecialPage {
	private $aPageList;
	private $strPageListType;
	private $iNamespace;
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
		$this->aPageList = explode("\n", trim($strPageList));
		$this->strPageListType = $wgRequest->getText( 'wpPageListType', 'pagenames' );
		
		$this->iNamespace = $wgRequest->getInt( 'namespace', NS_MAIN );
		
		$strMatch = $wgRequest->getText( 'wpMatch', '/hello (.*)\n/' );
		$this->aMatch = explode("\n", trim($strMatch));

		$this->strReplace = $wgRequest->getText( 'wpReplace', 'goodbye \1' );
		$this->aReplace = explode("\n", $this->strReplace);

		$this->strSummary = $wgRequest->getText( 'wpSummary', '' );

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
		$keyPreviewBtn = wfMsg( 'accesskey-preview' );
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

		$txtListOf = wfMsg( 'masseditregex-listtype-intro' );
		$txtType = array();
		$selected = array();
		foreach (array('pagenames', 'pagename-prefixes', 'categories', 'backlinks') as $t) {
			$txtType[$t] = wfMsg( 'masseditregex-listtype-' . $t );
			$selected[$t] = '';
		}

		$selected[$this->strPageListType] = 'checked="true"';
		
		$txtNamespace = wfMsg( 'masseditregex-namespace-intro' );
		$htmlNamespaceList = Xml::namespaceSelector( $this->iNamespace, null );
		
		// Generate HTML for the radio buttons (one for each list type)
		$htmlChoices = '';
		foreach ($txtType as $strChoice => $strText) {
			$htmlChoices .= <<<ENDCHOICE
<li><input type="radio" name="wpPageListType" id="masseditregex-radio-{$strChoice}"
value="{$strChoice}" {$selected[$strChoice]} />
<label for="masseditregex-radio-{$strChoice}">{$strText}</label></li>
ENDCHOICE;
		}

		$mainForm = <<<ENDFORM
<form id="masseditregex" method="post" action="{$action}">
<p>{$txtPageList}</p>
<!-- Newlines are important here - one after <textarea> but none
     before </textarea>, otherwise leading blank lines get cut
     off, or trailing newlines get added!  Tested FF3 -->
<textarea name="wpPageList" cols="80" rows="4" tabindex="1" style="width:100%;">
{$htmlPageList}</textarea>
{$txtNamespace} {$htmlNamespaceList}
<br/>
{$txtListOf} <ul style="list-style: none;">{$htmlChoices}</ul>
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
	<input type="submit" name="wpPreviewBtn" value="{$txtPreviewBtn}" accesskey="{$keyPreviewBtn}" />
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

	// Run a single request and return the page data
	function runRequest($aRequestVars)
	{
		$req = new FauxRequest( $aRequestVars, false );
		$processor = new ApiMain( $req, true );
		$processor->execute();
		$aPages = $processor->getResultData();
		if ( empty( $aPages ) ) return NULL; // no pages match the titles given
		return $aPages['query']['pages'];
	}

	// Run a bunch of requests (changing the $strValue parameter to each value
	// of $aValues in turn) and return the combined page data.
	function runMultiRequest($aRequestVars, $strVariable, $aValues, &$aErrors)
	{
		$aPageData = array();
		foreach ($aValues as $strValue) {
			$aRequestVars[$strVariable] = $strValue;
			$aMoreData = $this->runRequest($aRequestVars);
			if ($aMoreData)
				$aPageData = array_merge($aPageData, $aMoreData);
			else
				$aErrors[] = htmlspecialchars( wfMsg( 'masseditregex-exprnomatch', $strValue ) );
		}
		return $aPageData;
	}

	function getPages(&$aErrors, $iMaxPerCriterion) {
		global $wgContLang; // for mapping namespace numbers to localised name
		if ( !count( $this->aPageList ) ) return NULL;

		// Default vars for all page list types
		$aRequestVars = array(
			'action' => 'query',
			'prop' => 'info|revisions',
			'intoken' => 'edit',
			'rvprop' => 'content',
			//'rvlimit' => 1  // most recent revision only
		);
		switch ($this->strPageListType) {
			case 'pagenames': // Can do this in one hit
				$strNamespace = $wgContLang->getNsText($this->iNamespace) . ':';
				$aRequestVars['titles'] = $strNamespace . join( '|' . $strNamespace, $this->aPageList );
				return $this->runRequest($aRequestVars);
			case 'pagename-prefixes':
				$aRequestVars['generator'] = 'allpages';
				$aRequestVars['gapnamespace'] = $this->iNamespace;
				$aRequestVars['gaplimit'] = $iMaxPerCriterion;
				return $this->runMultiRequest($aRequestVars, 'gapprefix', $this->aPageList, $aErrors);
				//$aRequestVars['gapprefix'] = $this->aPageList[0];
			case 'categories':
				$aRequestVars['generator'] = 'categorymembers';
				$aRequestVars['gcmlimit'] = $iMaxPerCriterion;
				// This generator must have "Category:" on the start of each category
				// name, so append it to all the pages we've been given if it's missing
				$strNamespace = $wgContLang->getNsText( NS_CATEGORY ) . ':';
				$iLen = strlen($strNamespace);
				foreach ($this->aPageList as &$p) {
					if (substr($p, 0, $iLen) != $strNamespace)
						$p = $strNamespace . $p;
				}
				$retVar = $this->runMultiRequest($aRequestVars, 'gcmtitle', $this->aPageList, $aErrors);
				// Remove all the 'Category:' prefixes again for consistency
				foreach ($this->aPageList as &$p) $p = substr($p, $iLen);
				return $retVar;
			case 'backlinks':
				$aRequestVars['generator'] = 'backlinks';
				$aRequestVars['gblnamespace'] = $this->iNamespace;
				$aRequestVars['gbllimit'] = $iMaxPerCriterion;
				return $this->runMultiRequest($aRequestVars, 'gbltitle', $this->aPageList, $aErrors);
		}
		return NULL;
	}

	function perform( $bPerformEdits = true ) {
		global $wgOut, $wgUser, $wgTitle;

		$iMaxPerCriterion = $bPerformEdits ? MER_MAX_EXECUTE_PAGES : MER_MAX_PREVIEW_DIFFS;
		$aErrors = array();
		$aPages = $this->getPages($aErrors, $iMaxPerCriterion);
		if ( $aPages === NULL ) {
			$this->showForm( wfMsg( 'masseditregex-err-nopages' ) );
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
		
		if (count($aErrors)) {
			if ( $bPerformEdits ) {
				$o_wgOut->addHTML( '<li>' . join( '</li><li> ', $aErrors) . '</li>' );
			} else {
				$wgOut->addHTML( '<ul><li>' . join( '</li><li> ', $aErrors) . '</li></ul>' );
			}
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
					$wgOut->addWikiMsg( 'masseditregex-max-preview-diffs', MER_MAX_PREVIEW_DIFFS );
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

