<?php

require_once("forms.php");
require_once("Transaction.php");
require_once("OmegaWikiAttributes.php");

class DefaultWikidataApplication {
	protected $showRecordLifeSpan;
	protected $transaction;
	protected $queryTransactionInformation;
	protected $viewQueryTransactionInformation;
	protected $shouldShowAuthorities;
	protected $showCommunityContribution;
	protected $authoritiesToShow;
	
	// The following member variables control some application specific preferences
	protected $availableAuthorities = array();				// A map containing (userId => displayName) combination for authoritative contribution view
	protected $filterLanguageId = 0;						// Filter pages on this languageId, set to 0 to show all languages
	protected $showLanguageSelector = true;					// Show language selector at the top of each wiki data page
	protected $showClassicPageTitles = true;				// Show classic page titles instead of prettier page titles
	protected $possiblySynonymousRelationTypeId = 0;		// Put this relation type in a special section "Possibly synonymous"


	public function __construct() {
		global 
			$wgMessageCache;
		
		$wgMessageCache->addMessages(
			array(
				'ow_uilang'=>'Your user interface language: $1',
				'ow_uilang_set'=>'Set your preferences',
				'ow_save' => 'Save',
				'ow_history' => 'History'
			)
		);
		
		global
			$wgAvailableAuthorities, $wgFilterLanguageId, $wgShowLanguageSelector, 
			$wgShowClassicPageTitles, $wgPossiblySynonymousRelationTypeId,
			$wdDataSetContext;
		
		if (isset($wgAvailableAuthorities))
			$this->availableAuthorities = $wgAvailableAuthorities;
			
		if (isset($wgFilterLanguageId))
			$this->filterLanguageId = $wgFilterLanguageId;
			
		if (isset($wgShowLanguageSelector))
			$this->showLanguageSelector = $wgShowLanguageSelector;
			
		if (isset($wgShowClassicPageTitles))
			$this->showClassicPageTitles = $wgShowClassicPageTitles;
			
		if (isset($wgPossiblySynonymousRelationTypeId))
			$this->possiblySynonymousRelationTypeId = $wgPossiblySynonymousRelationTypeId; 

		$wdDataSetContext=$this->getDataSetContext();

	}

	function getLanguageSelector() {
		global 
			$wgUser;
		
		$userlang=$wgUser->getOption('language');
		$skin = $wgUser->getSkin();
			
		return wfMsg('ow_uilang',"<b>$userlang</b>").  " &mdash; " . $skin->makeLink("Special:Preferences", wfMsg('ow_uilang_set'));
	}

	protected function outputAuthoritativeContributionPanel() {
		global
			$wgOut;
		
		if (count($this->availableAuthorities) > 0) {
			$authorityOptions = array(
				"Show contribution by the community" => getCheckBox('authority-community', $this->showCommunityContribution)
			);
			
			foreach ($this->availableAuthorities as $authorityId => $authorityName) 
				$authorityOptions["Show contribution by " . $authorityName] = 
					getCheckBox(
						'authority-' . $authorityId, 
						in_array($authorityId, $this->authoritiesToShow)
					);
	
			$wgOut->addHTML(getOptionPanel($authorityOptions));
		}
	}

	protected function outputViewHeader() {
		global
			$wgOut, $wgShowAuthoritativeContributionPanelAtTop;
		
		if ($this->showLanguageSelector)
			$wgOut->addHTML($this->getLanguageSelector());
		
		if ($wgShowAuthoritativeContributionPanelAtTop)
			$this->outputAuthoritativeContributionPanel();
	}

	protected function outputViewFooter() {
		global
			$wgOut, $wgShowAuthoritativeContributionPanelAtBottom;
		
		if ($wgShowAuthoritativeContributionPanelAtBottom)	
			$this->outputAuthoritativeContributionPanel();

		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");
	} 
	
	public function view() {
		global
			$wgOut, $wgTitle;
			
		$wgOut->enableClientCache(false);

		$title = $wgTitle->getPrefixedText();

		if (!$this->showClassicPageTitles) 
			$title = $this->getTitle();

		$wgOut->setPageTitle($title);
		
		if (count($this->availableAuthorities) > 0) {
			$this->showCommunityContribution = isset($_GET['authority-community']);
			$this->authoritiesToShow = array();
			
			foreach ($this->availableAuthorities as $authorityId => $authorityName) 
				if (isset($_GET['authority-' . $authorityId]))
					$this->authoritiesToShow[] = $authorityId;
		}
		else
			$this->showCommunityContribution = false;
		
		$this->shouldShowAuthorities = count($this->authoritiesToShow) > 0 || $this->showCommunityContribution;
		initializeOmegaWikiAttributes($this->filterLanguageId != 0, $this->shouldShowAuthorities);	
		initializeObjectAttributeEditors($this->filterLanguageId, false, $this->shouldShowAuthorities);
		
		if ($this->shouldShowAuthorities) 
			$this->viewQueryTransactionInformation = new QueryAuthoritativeContributorTransactionInformation($this->availableAuthorities, $this->authoritiesToShow, $this->showCommunityContribution);
		else
			$this->viewQueryTransactionInformation = new QueryLatestTransactionInformation();
	}
	
	protected function save($referenceTransaction) {
		initializeOmegaWikiAttributes($this->filterLanguageId != 0, false);	
		initializeObjectAttributeEditors($this->filterLanguageId, false, false);
	}
	
	public function saveWithinTransaction() {
		global
			$wgTitle, $wgUser, $wgRequest;

		$summary = $wgRequest->getText('summary');

		startNewTransaction($wgUser->getID(), wfGetIP(), $summary);
		$this->save(new QueryAtTransactionInformation($wgRequest->getInt('transaction'), false));

		Title::touchArray(array($wgTitle));
		$now = wfTimestampNow();
		RecentChange::notifyEdit($now, $wgTitle, false, $wgUser, $summary, 0, $now, false, '', 0, 0, 0);
	}
	
	public function edit() {
		global
			$wgOut, $wgRequest;
			
		if ($wgRequest->getText('save') != '') 
			$this->saveWithinTransaction();

		if ($this->showLanguageSelector)
			$wgOut->addHTML($this->getLanguageSelector());
			
		initializeOmegaWikiAttributes($this->filterLanguageId != 0, false);	
		initializeObjectAttributeEditors($this->filterLanguageId, false, false);
	}
	
	public function history() {
		global
			$wgOut, $wgTitle, $wgRequest;
			

		$title = $wgTitle->getPrefixedText();

		if (!$this->showClassicPageTitles) 
			$title = $this->getTitle();

		$wgOut->setPageTitle(wfMsg('ow_history',$title));

		
		if (isset($_GET['show'])) {
			$this->showRecordLifeSpan = isset($_GET["show-record-life-span"]);
			$this->transaction = (int) $_GET["transaction"];
		}	
		else {
			$this->showRecordLifeSpan = true;
			$this->transaction = 0;
		}
		
		if ($this->transaction == 0)
			$this->queryTransactionInformation = new QueryHistoryTransactionInformation();
		else
			$this->queryTransactionInformation = new QueryAtTransactionInformation($this->transaction, $this->showRecordLifeSpan);
			
		$transactionId = $wgRequest->getInt('transaction');

		if ($this->showLanguageSelector)
			$wgOut->addHTML($this->getLanguageSelector());
			
		$wgOut->addHTML(getOptionPanel(
			array(
				'Transaction' => getSuggest('transaction','transaction', array(), $transactionId, getTransactionLabel($transactionId), array(0, 2, 3)),
				'Show record life span' => getCheckBox('show-record-life-span',$this->showRecordLifeSpan)
			),
			'history'
		));

		initializeOmegaWikiAttributes($this->filterLanguageId != 0, true);	
		initializeObjectAttributeEditors($this->filterLanguageId, $this->showRecordLifeSpan, false);
	}
	
	protected function outputEditHeader() {
		global
			$wgOut, $wgTitle;
			
		$title = $wgTitle->getPrefixedText();

		if (!$this->showClassicPageTitles) 
			$title = $this->getTitle();

		$wgOut->setPageTitle($title);
		$wgOut->setPageTitle(wfMsg('editing',$title));

		$wgOut->addHTML(
			'<form method="post" action="">' .
				'<input type="hidden" name="transaction" value="'. getLatestTransactionId() .'"/>'
		);
	}
	
	protected function outputEditFooter() {
		global
			$wgOut;
		
		$wgOut->addHTML(
			'<div class="option-panel">'.
				'<table cellpadding="0" cellspacing="0"><tr>' .
					'<th>' . wfMsg('summary') . ': </th>' .
					'<td class="option-field">' . getTextBox("summary") .'</td>' .
				'</tr></table>' .
				getSubmitButton("save", wfMsg('ow_save')).
			'</div>'
		);
		
		$wgOut->addHTML('</form>');
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");
	}
	
	public function getTitle() {
		global
			$wgTitle;
			
		return $wgTitle->getText();
	}

	/**
	 * The data set context defines which set of Wikidata
	 * tables should be used for all queries except
	 * for those relating to MediaWiki tables. It is a
	 * prefix defined in the 'wikidata_sets' tables
	 * and associated there with a string or a DMID.
	 *
	 * @return prefix (without underscore)
	**/
	public static function getDataSetContext() {
		return 'uw';
	}

}

?>
