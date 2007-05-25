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

	// Show a panel to select expressions from available data-sets
	protected $showDataSetPanel=true;


	public function __construct() {
		
		global
			$wgAvailableAuthorities, $wgFilterLanguageId, $wgShowLanguageSelector, 
			$wgShowClassicPageTitles, $wgPossiblySynonymousRelationTypeId;
					
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
		if($this->showDataSetPanel) {
			$wgOut->addHTML($this->getDataSetPanel());
		}
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
			$wgOut, $wgTitle, $wgUser;

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
	
	protected function getDataSetPanel() {
		global $wgTitle, $wgUser;
		$dc=wdGetDataSetContext();
		$ow_datasets=wfMsg('ow_datasets');
		$html="<div class=\"dataset-panel\">";;
		$html.="<table border=\"0\"><tr><th class=\"dataset-panel-heading\">$ow_datasets</th></tr>";
		$dataSets=wdGetDataSets();
		$sk=$wgUser->getSkin();
		foreach ($dataSets as $dataset) {
			$active=($dataset->getPrefix()==$dc->getPrefix());
			$name=$dataset->fetchName();
			$prefix=$dataset->getPrefix();

			$class= $active ? 'dataset-panel-active' : 'dataset-panel-inactive';
			$slot = $active ? "$name" : $sk->makeLinkObj($wgTitle,$name,"dataset=$prefix");
			$html.="<tr><td class=\"$class\">$slot</td></tr>";
		}
		$html.="</table>";
		$html.="</div>";
		return $html;

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

	/**
	 * @return true if permission to edit, false if not
	**/
	public function edit() {
		global
			$wgOut, $wgRequest, $wgUser;
			
		$dc=wdGetDataSetContext();
 		if(!$wgUser->isAllowed('editwikidata-'.$dc)) {
 			$wgOut->addWikiText(wfMsg('ow_noedit',$dc->fetchName()));
			$wgOut->setPageTitle(wfMsg('ow_noedit_title'));
 			return false;
 		}

		if ($wgRequest->getText('save') != '') 
			$this->saveWithinTransaction();

		if ($this->showLanguageSelector)
			$wgOut->addHTML($this->getLanguageSelector());
			
		initializeOmegaWikiAttributes($this->filterLanguageId != 0, false);	
		initializeObjectAttributeEditors($this->filterLanguageId, false, false);
		return true;
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

}

/**
 * A Wikidata application can manage multiple data sets.
 * The current "context" is dependent on multiple factors:
 * - the URL can have a dataset parameter
 * - there is a global default
 * - there can be defaults for different user groups
 *
 * @return prefix (without underscore)
**/
function wdGetDataSetContext() {

	global $wgRequest, $wdDefaultViewDataSet, $wdGroupDefaultView, $wgUser;
	$datasets=wdGetDataSets();
	$groups=$wgUser->getGroups();
	$dbs=wfGetDB(DB_SLAVE);
	$pref=$wgUser->getOption('ow_uipref_context');

	$trydefault='';
	foreach($groups as $group) {
		if(isset($wdGroupDefaultView[$group])) {
			# We don't know yet if this prefix is valid.
			$trydefault=$wdGroupDefaultView[$group];
		}
	}

	# URL parameter takes precedence over all else
	if( ($ds=$wgRequest->getText('dataset')) && array_key_exists($ds,$datasets) && $dbs->tableExists($ds."_transactions") ) {
		return $datasets[$ds];
	# User preference
	} elseif(!empty($pref) && array_key_exists($pref,$datasets)) {
		return $datasets[$pref];
	}
	# Group preference
	 elseif(!empty($trydefault) && array_key_exists($trydefault,$datasets)) {
		return $datasets[$trydefault];
	} else {
		return $datasets[$wdDefaultViewDataSet];
	}
}


/**
 * Load dataset definitions from the database if necessary.
 *
 * @return an array of all available datasets
**/
function &wdGetDataSets() {

	static $datasets, $wgGroupPermissions;
	if(empty($datasets)) {
		// Load defs from the DB
		$dbs =& wfGetDB(DB_SLAVE);
		$res=$dbs->select('wikidata_sets', array('set_prefix'));

		while($row=$dbs->fetchObject($res)) {

			$dc=new DataSet();
			$dc->setPrefix($row->set_prefix);
			if($dc->isValidPrefix()) {
				$datasets[$row->set_prefix]=$dc;
				wfDebug("Imported data set: ".$dc->fetchName()."\n");
			} else {
				wfDebug($row->set_prefix . " does not appear to be a valid dataset!\n");
			}
		}
	}
	return $datasets;
}

class DataSet {

	private $dataSetPrefix;
	private $isValidPrefix=false;
	private $fallbackName='';
	private $dmId=0;

	public function getPrefix() {
		return $this->dataSetPrefix;
	}

	public function isValidPrefix() {
		return $this->isValidPrefix;
	}

	public function setDefinedMeaningId($dmid) {
		$this->dmId=$dmid;
	}
	public function getDefinedMeaningId() {
		return $this->dmId;
	}

	public function setValidPrefix($isValid=true) {
		$this->isValidPrefix=$isValid;
	}

	public function setPrefix($cp) {

		$fname="DataSet::setPrefix";

		$dbs =& wfGetDB(DB_SLAVE);
		$this->dataSetPrefix=$cp;
		$sql="select * from wikidata_sets where set_prefix=".$dbs->addQuotes($cp);
		$res=$dbs->query($sql);
		$row=$dbs->fetchObject($res);
		if($row->set_prefix) {
			$this->setValidPrefix();
			$this->setDefinedMeaningId($row->set_dmid);
			$this->setFallbackName($row->set_fallback_name);
		} else {
			$this->setValidPrefix(false);
		}
	}

	// Fetch!
	function fetchName() {
		global $wgUser, $wdTermDBDataSet;
		if($wdTermDBDataSet) {
			$userLanguage=$wgUser->getOption('language');
			$spelling=getSpellingForLanguage($this->dmId, $userLanguage, 'en',$wdTermDBDataSet);
			if($spelling) return $spelling;
		}
		return $this->getFallbackName();
	}

	public function getFallbackName() {
		return $this->fallbackName;
	}

	public function setFallbackName($name) {
		$this->fallbackName=$name;
	}

	function __toString() {
		return $this->getPrefix();
	}

}

?>
