<?php

require_once('OmegaWikiRecordSets.php');
require_once('OmegaWikiAttributes.php');
require_once("Transaction.php");
require_once("WikiDataAPI.php");


class DefinedMeaningModel {

	protected $record=null;

	public function __construct($definedMeaningId, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation) {

		wfDebug("definedMeaningId:$definedMeaningId, filterLanguageId:$filterLanguageId, possiblySynonymousRelationTypeId:$possiblySynonymousRelationTypeId, queryTransactionInformation:$queryTransactionInformation\n");
		global
			$definedMeaningAttribute, $definitionAttribute, $classAttributesAttribute, 
			$alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute,
			$relationsAttribute, $reciprocalRelationsAttribute,
			$classMembershipAttribute, $collectionMembershipAttribute, $definedMeaningAttributesAttribute,
			$possiblySynonymousAttribute;
	
		$record = new ArrayRecord($definedMeaningAttribute->type->getAttributes());
		$record->setAttributeValue($definitionAttribute, getDefinedMeaningDefinitionRecord($definedMeaningId, $filterLanguageId, $queryTransactionInformation));
		$record->setAttributeValue($classAttributesAttribute, getClassAttributesRecordSet($definedMeaningId, $queryTransactionInformation));
		$record->setAttributeValue($alternativeDefinitionsAttribute, getAlternativeDefinitionsRecordSet($definedMeaningId, $filterLanguageId, $queryTransactionInformation));
		$record->setAttributeValue($synonymsAndTranslationsAttribute, getSynonymAndTranslationRecordSet($definedMeaningId, $filterLanguageId, $queryTransactionInformation));
		
		$filterRelationTypes = array();
	
		if ($possiblySynonymousRelationTypeId != 0) {
			$record->setAttributeValue($possiblySynonymousAttribute, getPossiblySynonymousRecordSet($definedMeaningId, $filterLanguageId, $possiblySynonymousRelationTypeId, $queryTransactionInformation));
			$filterRelationTypes[] = $possiblySynonymousRelationTypeId;
		}
		
		$record->setAttributeValue($relationsAttribute, getDefinedMeaningRelationsRecordSet($definedMeaningId, $filterLanguageId, $filterRelationTypes, $queryTransactionInformation));
		$record->setAttributeValue($reciprocalRelationsAttribute, getDefinedMeaningReciprocalRelationsRecordSet($definedMeaningId, $filterLanguageId, $queryTransactionInformation));
		$record->setAttributeValue($classMembershipAttribute, getDefinedMeaningClassMembershipRecordSet($definedMeaningId, $queryTransactionInformation));
		$record->setAttributeValue($collectionMembershipAttribute, getDefinedMeaningCollectionMembershipRecordSet($definedMeaningId, $queryTransactionInformation));
		$record->setAttributeValue($definedMeaningAttributesAttribute, getObjectAttributesRecord($definedMeaningId, $filterLanguageId, $queryTransactionInformation));
		$this->record=$record;
	
	}

	/*horrible cannibalised hack. Use at own risk*/
	public function save() {
		initializeOmegaWikiAttributes($this->filterLanguageId != 0, false);	
		initializeObjectAttributeEditors($this->filterLanguageId, false);
	global
		$wgTitle;

		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
		
		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
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

}

