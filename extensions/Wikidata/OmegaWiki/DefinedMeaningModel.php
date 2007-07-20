<?php

require_once('OmegaWikiAttributes.php');
require_once('OmegaWikiRecordSets.php');
require_once('OmegaWikiAttributes.php');
require_once("Transaction.php");
require_once("WikiDataAPI.php");

/** 
 * A front end for the database information/ArrayRecord and any other information
 * to do with defined meanings (as per MVC)
 * Will collect code for instantiating and loading and saving DMs here for now.
 */
class DefinedMeaningModel {

	protected $record=null;
	protected $recordIsLoaded=false;
	protected $exists=null;
	protected $id=null;
	protected $viewInformation=null;
	protected $definingExpression=null; # String
	protected $dataset=null;
	protected $syntrans=array();
	protected $titleObject=null; 

	/**
	 * Construct a new DefinedMeaningModel for a particular DM.
	 * You need to call loadRecord() to load the actual data.
	 *
	 * @param Integer         the database ID of the DM 
	 * @param ViewInformation optional
	 * @param DataSet	  where to look for the DM by default
         */
	public function __construct($definedMeaningId, $viewInformation=null, DataSet $dc=null) {

		if(!$definedMeaningId) throw new Exception("DM needs at least a DMID!");
		$this->setId($definedMeaningId);
		if (is_null($viewInformation)) {	
			$viewInformation = new ViewInformation();
			$viewInformation->queryTransactionInformation= new QueryLatestTransactionInformation();
		}
		$this->viewInformation=$viewInformation;	
		if(is_null($dc)) {
			$dc=wdGetDataSetContext();
		}
		$this->dataset=$dc;
	}

	/**
	 * Checks for existence of a DM.
	 * If $this->definingExpression is set, it will also check if the spelling 
	 * of the defining expression matches 
	 * 
	 * @param Boolean If true, checks beyond the dataset context and will
	 *                return the first match. Always searches current 
	 *                context first.
	 * @param Boolean Switch dataset context if match outside default is found.
	 *
	 * @return DataSet object in which the DM was found, or null.
	 *
	 */
	public function checkExistence($searchAllDataSets=false, $switchContext=false) {
		
		global $wdCurrentContext;
		$match=$this->checkExistenceInDataSet($this->dataset);
		if(!is_null($match)) {
			$this->exists=true;
			return $match;
		} else {
			$this->exists=false;
			if(!$searchAllDataSets) return null;
		}
		// Continue search
		$datasets=wdGetDataSets();
		foreach($datasets as $currentSet) {
			if($currentSet->getPrefix() != $this->dataset->getPrefix()) {
				$match=$this->checkExistenceInDataSet($currentSet);
				if(!is_null($match)) {
					$this->exists=true;
					if($switchContext) { 
						$wdCurrentContext=$match;
						$this->dataset=$match;
					}
					return $match;
				}
			}
		}
		$this->exists=false;
		return null;

	}

	/**
	 * @param DataSet where to look
	 * @param Integer Defined Meaning Id
	 * @param String  Spelling
	 * @return DataSet or null
	 * @see checkExistence
	 *
	 */
	public function checkExistenceInDataSet(DataSet $dc) {

		$definingExpression=$this->definingExpression;
		$id=$this->getId();
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT defined_meaning_id, expression_id from {$dc}_defined_meaning where defined_meaning_id=".$this->id." AND ".getLatestTransactionRestriction("{$dc}_defined_meaning"));
		$dmRow = $dbr->fetchObject($queryResult);
		if(!$dmRow || !$dmRow->defined_meaning_id) {
			return null;
		}
		if(is_null($definingExpression)) {
			return $dc;
		} else {
			$expid=(int)$dmRow->expression_id;
			$storedExpression = getExpression($expid, $dc);
			if(is_null($storedExpression)) return null;
			if($storedExpression->spelling != $definingExpression) {
				// Defining expression does not match, but check was requested!
				return null;
			} else {
				return $dc;
			}
		} 
	}
	/** 
	 * Load the associated record object.
	 *
	 * @return Boolean indicating success.
	 */
	public function loadRecord() {

		if(is_null($this->exists)) {
			$this->checkExistence();
		}

		if(!$this->exists) {
			return false;
		}

		$id=$this->getId();
		$view=$this->getViewInformation();
		/** FIXME: Records should be loaded using helpers rather than
		  global functions! */
		global 
			$omegaWikiAttributes;
		$o=$omegaWikiAttributes;

		$record = new ArrayRecord($o->definedMeaning->type);
		$record->definedMeaningCompleteDefiningExpression =  getDefiningExpressionRecord($id);
		$record->definition = getDefinedMeaningDefinitionRecord($id, $view);
		$record->classAttributes = getClassAttributesRecordSet($id, $view);
		$record->alternativeDefinitions = getAlternativeDefinitionsRecordSet($id, $view);
		$record->synonymsAndTranslations = getSynonymAndTranslationRecordSet($id, $view);
		$filterRelationTypes = array();
	
		if ($view->possiblySynonymousRelationTypeId != 0) {
			$record->possiblySynonymous = getPossiblySynonymousRecordSet($id, $view);
			$filterRelationTypes[] = $view->possiblySynonymousRelationTypeId;
		}
		
		$record->relations = getDefinedMeaningRelationsRecordSet($id, $filterRelationTypes, $view);
		$record->reciprocalRelations = getDefinedMeaningReciprocalRelationsRecordSet($id, $view);
		$record->classMembership = getDefinedMeaningClassMembershipRecordSet($id, $view);
		$record->collectionMembership= getDefinedMeaningCollectionMembershipRecordSet($id, $view);
		
		$objectAttributesRecord = getObjectAttributesRecord($id, $view);
		$record->definedMeaningAttributes = $objectAttributesRecord;
		applyPropertyToColumnFiltersToRecord($record, $objectAttributesRecord, $view);
		
		$this->record=$record;
		$this->recordIsLoaded=true;
		return true;
	}

	/**  
	 * FIXME - work in progress
	 *
	 */
	public function save() {
		initializeOmegaWikiAttributes($this->viewInformation);	
		initializeObjectAttributeEditors($this->viewInformation);

		# Nice try sherlock, but we really need to get our DMID from elsewhere
		#$definedMeaningId = $this->getId();
		
		#Need 3 steps: copy defining expression, create new dm, then update
		
		$expression=$this->dupDefiningExpression();
		# to make the expression really work, we may need to call
		# more here?
		$expression->createNewInDatabase();
		
		# shouldn't this stuff be protected?
		$expressionId=$expression->id;
		$languageId=$expression->languageId;
		$text="Copied Defined Meaning"; // this might work for now
						// but where to get useful
						// text?

		#here we assume the DM is not there yet.. not entirely wise
		#in the long run.
		echo "id: $expressionId lang: $languageId";
		$definedMeaningId=createNewDefinedMeaning($expressionId, $languageId, $text);
		
		getDefinedMeaningEditor($this->viewInformation)->save(
			$this->getIdStack($definedMeaningId), 
			$this->getRecord()
		);
	}

	/**
	 * FIXME - work in progress
	 */
	protected function getIdStack($definedMeaningId) {
		global
			$omegaWikiAttributes;
		$o=$omegaWikiAttributes;

		$definedMeaningIdStructure = new Structure($o->$definedMeaningId);
		$definedMeaningIdRecord = new ArrayRecord($definedMeaningIdStructure, $definedMeaningIdStructure);
		$definedMeaningIdRecord->definedMeaningId= $definedMeaningId;	
		
		$idStack = new IdStack("defined-meaning");
		$idStack->pushKey($definedMeaningIdRecord);
		
		return $idStack;
	}

	/**
	 * FIXME - work in progress
	 */
	public function saveWithinTransaction() {
		#global
		#	$wgTitle, $wgUser, $wgRequest;

		#$summary = $wgRequest->getText('summary');

		
		// Insert transaction information into the DB
		#startNewTransaction($wgUser->getID(), wfGetIP(), $summary);
		startNewTransaction(0, "0.0.0.0", "copy operation");

		// Perform regular save
		#$this->save(new QueryAtTransactionInformation($wgRequest->getInt('transaction'), false));
		$this->save();

		// Update page caches
		#Title::touchArray(array($wgTitle));

		// Add change to RC log
		#$now = wfTimestampNow();
		#RecentChange::notifyEdit($now, $wgTitle, false, $wgUser, $summary, 0, $now, false, '', 0, 0, 0);
	}

	/**
	 * @return associated record object or null. Loads it if necessary.
	 */
	public function getRecord() {
		if(!$this->recordIsLoaded) {
			$this->loadRecord();
		}
		if(!$this->recordIsLoaded) {
			return null;
		}
		return $this->record;
	}

	public function setViewInformation(ViewInformation $viewInformation) {
		$this->viewInformation=$viewInformation;
	}

	public function getViewInformation() {
		return $this->viewInformation;
	}
	
	/** Attempts to save defining expression if it does not exist "here"
	 * (This works right now because we override the datasetcontext in 
	 * SaveDM.php . dc should be handled more solidly) */
	protected function dupDefiningExpression() {

		$record=$this->getRecord();
		$expression=$record->getValue("defined-meaning-full-defining-expression");
		$spelling=$expression->getValue("defined-meaning-defining-expression");
		$language=$expression->getValue("language");
		return findOrCreateExpression($spelling, $language);
	}
	
	/** Copy this defined meaning to specified dataset-context 
	 * Warning: This is somewhat new  code, which still needs
	 * shoring up. 
	 * @param $dataset	dataset to copy to.
	 * @returns 	defined meaning id in the new dataset
	 */
	public function copyTo($dataset) {
		#$definedMeaningID=$this->getId();
		echo "copy to:$dataset   ";
		#$from_dc=$this->getDataset();
		$to_dc=$dataset;
		# TODO We should actually thoroughly check that everything
		# is present before proceding, and throw some exceptions
		# if not.
		
		global 
			$wdCurrentContext;
		$wdCurrentContext=$to_dc;	# set global override (DIRTY!)
		$this->saveWithinTransaction();
		$wdCurrentContext=null;		# unset override, we probably should
						# use proper OO for this.
	}

	/** 
	 * Return one of the syntrans entries of this defined meaning,
	 * specified by language code. Caches the syntrans records
	 * in an array.
	 * 
	 * @param String Language code of the synonym/translation to look for
	 * @param String Fallback to use if not found
	 * @return Spelling or null if not found at all
	 *
	 * TODO make fallback optional
	 * 
	 */
	public function getSyntransByLanguageCode($languageCode, $fallbackCode="en") {

		if(array_key_exists($languageCode, $this->syntrans))
		  return $this->syntrans[$languageCode];

		$syntrans=getSpellingForLanguage($this->getId(), $languageCode, $fallbackCode, $this->dataset);
		if(!is_null($syntrans)) {
			$this->syntrans[$languageCode]=$syntrans;
		}
		return $syntrans;
	}

	/** 
	 * @return the page title object associated with this defined meaning 
	 * First time from DB lookup. Subsequently from cache 
	 */
	public function getTitleObject() {
		if ($this->titleObject==null) {
			$definingExpression=$this->getDefiningExpression();
			$id=$this->getId();
			
			if (is_null($definingExpression) or is_null($id)) 
				return null;

			$definingExpressionAsTitle=str_replace(" ", "_", $definingExpression);
			$text="DefinedMeaning:".$definingExpressionAsTitle."_($id)";
			$titleObject=Title::newFromText($text);
			$this->titleObject=$titleObject;
		}
		return $this->titleObject;
	}
	

	/** 
	 * @return HTML link including the wrapping tag
	 * @param String Language code of synonym/translation to show
	 * @param String Fallback code
	 * @throws Exception If title object is missing
	 */
	public function getHTMLLink($languageCode, $fallbackCode="en") {
		global $wgUser;
		$skin=$wgUser->getSkin();
		$titleObject=$this->getTitleObject();
		if ($titleObject==null)
			throw new Exception("Need title object to create link");

		$dataset=$this->getDataset();		
		$prefix=$dataset->getPrefix();
		$name=$this->getSyntransByLanguageCode($languageCode, $fallbackCode);
		return $skin->makeLinkObj($title, $name , "dataset=$prefix");
	}

	/** 
	 * 
	 * Splits title of the form "Abc (123)" into text and number
	 * components.
	 *
	 * @param String the title to analyze
	 * @return Array of the two components or null.
	 *
	 */
	public static function splitTitleText($titleText) {
		$bracketPosition = strrpos($titleText, "(");
		if ($bracketPosition==false) 
			return null; # Defined Meaning ID is missing from title string
		$rv=array();
		$definingExpression = substr($titleText, 0, $bracketPosition -1);
		$definingExpression = str_replace("_"," ",$definingExpression);
		$definedMeaningId = substr($titleText, $bracketPosition + 1, strlen($titleText) - $bracketPosition - 2);
		$rv["expression"]=$definingExpression;
		$rv["id"]=(int)$definedMeaningId;
		return $rv;
	}

	/** 
	 * @return full text representation of title
	 */
	public function getTitleText() {
		$title=$this->getTitleObject();
		return $title->getFullText();
	}

	public function setId($id) {
		$this->id=$id;
	}
	
	public function getId() {
		return $this->id;
	}

	/** 
	 * Fetch from DB if necessary
	 *
	 */
	public function getDefiningExpression() {
		if(is_null($this->definingExpression)) {
			return definingExpression($this->getId(),$this->getDataset());
		}
		return $this->definingExpression;
	}

	public function setDefiningExpression($definingExpression) {
		$this->definingExpression=$definingExpression;
	}

	public function setDataset(&$dataset) {
		$this->dataset=$dataset;
	}

	public function getDataset() {
		return $this->dataset;
	}

	public function exists() {
		return $this->exists;
	}

}
