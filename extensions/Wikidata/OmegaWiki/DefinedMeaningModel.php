<?php

require_once('OmegaWikiRecordSets.php');

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

	public function save() {

	}

	public function getRecord() {
		return $this->record;
	}

}
?>
