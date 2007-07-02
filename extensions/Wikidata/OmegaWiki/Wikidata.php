<?php

require_once("forms.php");
require_once("Transaction.php");
require_once("OmegaWikiAttributes.php");
require_once("WikiDataAPI.php");

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


	protected function outputViewHeader() {
		global
			$wgOut;
		
		if ($this->showLanguageSelector)
			$wgOut->addHTML($this->getLanguageSelector());
		
		if($this->showDataSetPanel) {
			$wgOut->addHTML($this->getDataSetPanel());
		}
	}

	protected function outputViewFooter() {
		global
			$wgOut;
		
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
		
		initializeOmegaWikiAttributes($this->filterLanguageId != 0);	
		initializeObjectAttributeEditors($this->filterLanguageId, false);		
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
		initializeObjectAttributeEditors($this->filterLanguageId, false);
	}
	
	public function saveWithinTransaction() {
		global
			$wgTitle, $wgUser, $wgRequest;

		$summary = $wgRequest->getText('summary');

		// Insert transaction information into the DB
		startNewTransaction($wgUser->getID(), wfGetIP(), $summary);

		// Perform regular save
		$this->save(new QueryAtTransactionInformation($wgRequest->getInt('transaction'), false));

		// Update page caches
		Title::touchArray(array($wgTitle));

		// Add change to RC log
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

		# Plain filter for the lifespan info about each record
		if (isset($_GET['show'])) {
			$this->showRecordLifeSpan = isset($_GET["show-record-life-span"]);
			$this->transaction = (int) $_GET["transaction"];
		}	
		else {
			$this->showRecordLifeSpan = true;
			$this->transaction = 0;
		}
		
		# Up to which transaction to view the data
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
 * @param $dc	optional, for convenience.
 *		if the dataset context is already set, will
		return that value, else will find the relevant value
 * @return prefix (without underscore)
**/
function wdGetDataSetContext($dc=null) {

	if (!is_null($dc)) 
		return $dc; 
	global $wgRequest, $wdDefaultViewDataSet, $wdGroupDefaultView, $wgUser;
	$datasets=wdGetDataSets();
	$groups=$wgUser->getGroups();
	$dbs=wfGetDB(DB_SLAVE);
	$pref=$wgUser->getOption('ow_uipref_datasets');

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
	private $dmId=0; # the dmId of the dataset name

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

/**
 * A representation and easy access to all defined-meaning related data in one handy spot
 * or would be. Currently only holds data  that is really needed. Please expand and
 * use to replace WiKiDataAPI.
 * Sometimes a getter or setter will query the database and/or attempt to deduce additional
 * information based on what it already knows, but don't count on that (yet).
 */

class DefinedMeaningData {
	private $languageId=null; # 85 = English, a pretty safe default.
	private $languageCode=null; #the associated wikiId
	private $spelling=null;
	private $id=null;
	private $dataset=null;
	private $title=null;

	/** return spelling of associated expression in particular langauge
	 *  not nescesarily the correct language. 
	 */
	public function getSpelling() {
		if ($this->spelling==null) {

			$id=$this->getId();
			if ($id==null) 
				return null;
			
			$languageCode=$this->getLanguageCode();
			if ($languageCode==null) 
				return null; # this should probably never happen
			
			$dataset=$this->getDataset();
			if ($dataset==null) 
				return null;
			$this->spelling=getSpellingForLanguage($id, $languageCode, "en", $dataset);
		} 
		return $this->spelling;
	}

	public function makeLinkObj() {
		global 
			$wgUser;

		$skin=$wgUser->getSkin();
		if ($skin==null) 
			return null; # This is a bit of a guess
			
		$title=$this->getTitle();
		if ($title==null)
			return null;
		
		$dataset=$this->getDataset();
		if ($dataset==null)
			return null;
		
		$prefix=$dataset->getPrefix();
		$name=$this->getSpelling();
		
		$skin->makeLinkObj($title, $name , "dataset=$prefix");
	}
	
	/** returns the page title associated with this defined meaning (as a Title object)
	 * First time from db lookup. Subsequently from cache 
	 */
	public function getTitle() {
		$title=$this->title;
		if ($title==null) {
			$name=$this->getSpelling();
			$id=$this->getId();
			
			if (is_null($name) or is_null($id)) 
				return null;

			$text="DefinedMeaning:".$name."_($id)";
			$title=Title::newFromText($text);
			$this->title=$title;
		}
		return $title;
	}
	
	/** set the title (and associated ID) from text representation
	 * This is partially copied from DefinedMeaning.getDefinedMeaningIdFromTitle
	 * which is slightly less usable (and hence should be deprecated)
	 * 
	 * Also note the traditionally very weak error-checking, th$this->title=Title::newFromText($titleText);
		$bracketPosition = strrpos($titleText, "(");
		if ($bracketPosition==false) 
			return; # we accept that we may have a someis may need
	 * updating. Canonicalize helps a bit. 
	 *
	 * Will gladly eat invalid titles (in which case object state
	 * may become somewhat undefined) 
	 */
	public function setTitleText($titleText){
		// get id from title: DefinedMeaning:expression (id)
		$this->title=Title::newFromText($titleText);
		$bracketPosition = strrpos($titleText, "(");
		if ($bracketPosition==false) 
			return; # Defined Meaning ID is missing from title string
		$definedMeaningId = substr($titleText, $bracketPosition + 1, strlen($titleText) - $bracketPosition - 2);
		$this->setId($definedMeaningId);
	}
	
	/**set the title (and associated ID) from mediawiki Title object*/
	public function setTitle(&$title){
		$this->setTitleText($title->getFullText());
	}
	
	/**retturn full text representation of title*/
	public function getTitleText(){
		$title=$this->getTitle();
		return $title->getFullText();
	}
	/** 
	 * Look up defined meaning id in db,
	 * and attempt to get defined meaning into 
	 * canonical form, with correct spelling, etc.
	 * 
	 * use canonicalize anytime you take user input.
	 * note that the defined meaning must already 
	 * be in the database for this to work.
	 *
	 * example(s):
	 * For any user supplied defined meaning,
	 * "traditionally" we have only looked at the part 
	 * between parens.
	 * For instance, for
	 *	DefinedMeaning:Jump (6684)
	 *
	 * We only really look at (6684), and discard the rest.
	 * This can lead to funny situations...
	 *
	 * If a user were to look for DefinedMeaning:YellowBus (6684)
	 * they would get a page back with that title, but with 
	 * the contents of DefinedMeaning:Jump (6684)... very confusing!
	 *
	 * This kind of odd behaviour (and anything else we might come across later)
	 * gets corrected here.
	 *
	 * @return true on success (page (already) exists in db, title now updated);
	 *         false on failure (page not (yet?) in db, or id was never set, 
	 *	   or not enough info to perform lookup (need at least id or something with id in it:
	 *         a horribly misformed title will work, as long as the id is correct :-P )
	 */
	public function canonicalize(){
		$oldtitle=$this->title;
		$oldspelling=$this->spelling;
	
		$this->title=null; #    } clear cached values to force db fetch.
		$this->spelling=null; # }
		$this->title=$this->getTitle(); # will fetch from db!
		
		if ($this->title==null) { # db lookup failure
			$this->title=$oldtitle; 
			$this->spelling=$oldspelling;
			return false;
		}

		return true;
	}

	/** returns true if a database entry already exists for this dmid, and an expression is present in this langauge in this dataset, otherwise returns false. */
	public function exists() {
		/*reusing getSpelling for now as a hack. Probably better
		 *to write a dedicated exists in WikiDataAPI, or here
		 */
		 if ($this->getSpelling()!=null) 
		 	return true;
		return false;
	}
	
	/** sets id*/
	public function setId($id) {
		$this->id=$id;
		$this->canonicalize();
	}
	
	public function getId() {
		return $this->id;
	}

	public function setDataset(&$dataset) {
		$this->dataset=$dataset;
	}

	public function &getDataset() {
		if ($this->dataset==null) {
			$this->dataset=wdGetDataSetContext();
		}
		return $this->dataset;
	}

	public function setLanguageId($languageId) {
		$this->languageId = $languageId;	
	}

	public function getLanguageId() {
		return $this->languageId;
	}

	public function setLanguageCode($languageCode) {
		return $this->langaugeCode;
	}

	public function getLanguageCode() {
		if ($this->languageCode==null) {
			global 
				$wgUser;
			$this->languageCode=$wgUser->getOption('language');
		}
		return $this->languageCode;
	}
}
	

