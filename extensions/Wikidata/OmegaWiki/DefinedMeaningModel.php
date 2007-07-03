<?php

require_once('OmegaWikiAttributes.php');
require_once('OmegaWikiRecordSets.php');
require_once('OmegaWikiAttributes.php');
require_once("Transaction.php");
require_once("WikiDataAPI.php");


class DefinedMeaningModel {

	protected $record=null;
	protected $definedMeaningID=null;

	public function __construct($definedMeaningId, $viewInformation) {

		wfDebug("definedMeaningId:$definedMeaningId, filterLanguageId:$viewInformation->filterLanguageId, possiblySynonymousRelationTypeId:$viewInformation->possiblySynonymousRelationTypeId, queryTransactionInformation:$viewInformation->queryTransactionInformation\n");
		global
			$definedMeaningAttribute, $definitionAttribute, $classAttributesAttribute, 
			$alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute,
			$relationsAttribute, $reciprocalRelationsAttribute,
			$classMembershipAttribute, $collectionMembershipAttribute, $definedMeaningAttributesAttribute,
			$possiblySynonymousAttribute;
	
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
	public function save() {
		initializeOmegaWikiAttributes($this->filterLanguageId != 0, false);	
		initializeObjectAttributeEditors($this->filterLanguageId, false);
		$definedMeaningId = $this->getDefinedMeaningID();
		
		getDefinedMeaningEditor($this->filterLanguageId, $this->possiblySynonymousRelationTypeId, false, false)->save(
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

