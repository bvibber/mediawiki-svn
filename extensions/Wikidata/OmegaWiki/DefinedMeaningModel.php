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

	/**
	 *Construct a new DefinedMeaningModel for a particular defined meaning
 	 * will fetch the appropriate record for the provided definedMeaningId
	 * you can't use this to construct a new DM from scratch (yet)
	 * you can't (yet) provide a dataset-context ($dc) 
	 * @param $definedMeaningId	the database ID of the DM 
	 * @param $viewInformation	Specify specific ViewInformation, if needed.
         */
	public function __construct($definedMeaningId, $viewInformation=null) {

		global
			$definedMeaningAttribute, $definitionAttribute, $classAttributesAttribute, 
			$alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute,
			$relationsAttribute, $reciprocalRelationsAttribute,
			$classMembershipAttribute, $collectionMembershipAttribute, $definedMeaningAttributesAttribute,
			$possiblySynonymousAttribute;
		
		if (is_null($viewInformation)) {	
			$viewInformation = new ViewInformation();
			$viewInformation->queryTransactionInformation= new QueryLatestTransactionInformation();
		}
	
		#wfDebug("definedMeaningId:$definedMeaningId, filterLanguageId:$viewInformation->filterLanguageId, possiblySynonymousRelationTypeId:$viewInformation->possiblySynonymousRelationTypeId, queryTransactionInformation:$viewInformation->queryTransactionInformation\n");
		$this->setDefinedMeaningID($definedMeaningId);
		$record = new ArrayRecord($definedMeaningAttribute->type);
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
	public function save() {
		initializeOmegaWikiAttributes($this->viewInformation);	
		initializeObjectAttributeEditors($this->viewInformation);

		$definedMeaningId = $this->getDefinedMeaningID();
		
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

	public function getRecord() {
		return $this->record;
	}

	public function setDefinedMeaningID($definedMeaningID) {
		$this->definedMeaningID=$definedMeaningID;
	}

	public function getDefinedMeaningID() {
		return $this->definedMeaningID;
	}

}

