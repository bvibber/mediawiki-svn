<?php

require_once('OmegaWikiAttributes.php');
require_once('OmegaWikiRecordSets.php');
require_once('OmegaWikiAttributes.php');
require_once("Transaction.php");
require_once("WikiDataAPI.php");

/** A front end for the database information/ArrayRecord and any other information
 * to do with defined meanings (as per MVC)
 * Will collect code for instantiating and loading and saving DMs here for now.
 */
class DefinedMeaningModel {

	protected $record=null;
	protected $definedMeaningID=null;
	protected $viewInformation=null;

	/**
	 *Construct a new DefinedMeaningModel for a particular defined meaning
 	 * will fetch the appropriate record for the provided definedMeaningId
	 * you can't use this to construct a new DM from scratch (yet)
	 * you can't (yet) provide a dataset-context ($dc) 
	 * @param $definedMeaningId	the database ID of the DM 
	 * @param $viewInformation	Optional: Specify specific ViewInformation, if needed.
         */
	public function __construct($definedMeaningId, $viewInformation=null) {

		global
			$definedMeaningAttribute, $definitionAttribute, $classAttributesAttribute, 
			$alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute,
			$relationsAttribute, $reciprocalRelationsAttribute,
			$classMembershipAttribute, $collectionMembershipAttribute, $definedMeaningAttributesAttribute,
			$possiblySynonymousAttribute, $definedMeaningCompleteDefiningExpressionAttribute;

		if (is_null($viewInformation)) {	
			$viewInformation = new ViewInformation();
			$viewInformation->queryTransactionInformation= new QueryLatestTransactionInformation();
		}
	
		$this->viewInformation=$viewInformation;
		#wfDebug("definedMeaningId:$definedMeaningId, filterLanguageId:$viewInformation->filterLanguageId, possiblySynonymousRelationTypeId:$viewInformation->possiblySynonymousRelationTypeId, queryTransactionInformation:$viewInformation->queryTransactionInformation\n");
		$this->setDefinedMeaningID($definedMeaningId);
		$record = new ArrayRecord($definedMeaningAttribute->type);
		$record->setAttributeValue($definedMeaningCompleteDefiningExpressionAttribute, getDefiningExpressionRecord($definedMeaningId));
		$record->setAttributeValue($definitionAttribute, getDefinedMeaningDefinitionRecord($definedMeaningId, $viewInformation));
		$record->setAttributeValue($classAttributesAttribute, getClassAttributesRecordSet($definedMeaningId, $viewInformation));
		$record->setAttributeValue($alternativeDefinitionsAttribute, getAlternativeDefinitionsRecordSet($definedMeaningId, $viewInformation));
		$record->setAttributeValue($synonymsAndTranslationsAttribute, getSynonymAndTranslationRecordSet($definedMeaningId, $viewInformation));
		
		$filterRelationTypes = array();
	
		if ($viewInformation->possiblySynonymousRelationTypeId != 0) {
			$record->setAttributeValue($possiblySynonymousAttribute, getPossiblySynonymousRecordSet($definedMeaningId, $viewInformation));
			$filterRelationTypes[] = $viewInformation->possiblySynonymousRelationTypeId;
		}
		
		$record->setAttributeValue($relationsAttribute, getDefinedMeaningRelationsRecordSet($definedMeaningId, $filterRelationTypes, $viewInformation));
		$record->setAttributeValue($reciprocalRelationsAttribute, getDefinedMeaningReciprocalRelationsRecordSet($definedMeaningId, $viewInformation));
		$record->setAttributeValue($classMembershipAttribute, getDefinedMeaningClassMembershipRecordSet($definedMeaningId, $viewInformation));
		$record->setAttributeValue($collectionMembershipAttribute, getDefinedMeaningCollectionMembershipRecordSet($definedMeaningId, $viewInformation));
		$record->setAttributeValue($definedMeaningAttributesAttribute, getObjectAttributesRecord($definedMeaningId, $viewInformation));
		$this->record=$record;
	
	}

	/*horrible cannibalised hack. Use at own risk*/
	/* Sorry, don't know what horrible cannibalised hacks are. Therefore I cannot update code properly. 
	 * Please check if it still works correctly. Peter-Jan Roes.  
	 */
	/* You are a wise man! :-D */
	public function save() {
		initializeOmegaWikiAttributes($this->viewInformation);	
		initializeObjectAttributeEditors($this->viewInformation);

		# Nice try sherlock, but we really need to get our DMID from elsewhere
		#$definedMeaningId = $this->getDefinedMeaningID();
		
		#Need 3 steps: copy defining expression, create new dm, then update
		
		$expression=$this->dupDefiningExpression();
		# to make the expression really work, we may need to call
		# more here?
		
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

	/*horrible cannibalised hack. Use at own risk*/
	protected function getIdStack($definedMeaningId) {
		global
			$definedMeaningIdAttribute;
			
		$definedMeaningIdStructure = new Structure($definedMeaningIdAttribute);
		$definedMeaningIdRecord = new ArrayRecord($definedMeaningIdStructure, $definedMeaningIdStructure);
		$definedMeaningIdRecord->setAttributeValue($definedMeaningIdAttribute, $definedMeaningId);	
		
		$idStack = new IdStack("defined-meaning");
		$idStack->pushKey($definedMeaningIdRecord);
		
		return $idStack;
	}

	/*horrible cannibalised hack. Use at own risk*/
	/* this particular function doesn't actually work yet */
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

	public function getRecord() {
		return $this->record;
	}

	public function setDefinedMeaningID($definedMeaningID) {
		$this->definedMeaningID=$definedMeaningID;
	}

	public function getDefinedMeaningID() {
		return $this->definedMeaningID;
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

}

