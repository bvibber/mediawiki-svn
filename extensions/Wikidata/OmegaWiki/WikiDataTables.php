<?php

require_once("Wikidata.php");
$wdDataSetContext=DefaultWikidataApplication::getDataSetContext();
$dc=$wdDataSetContext; // See? We're keeping this readable :-P

class Table {
	public $name;
	public $isVersioned;
	public $keyFields;
	
	public function __construct($name, $isVersioned, $keyFields) {
		$this->name = $name;
		$this->isVersioned = $isVersioned;
		$this->keyFields = $keyFields;
	}
}

global
	$tables, $meaningRelationsTable, $classMembershipsTable, $collectionMembershipsTable, $syntransTable, 
	$translatedContentTable, $alternativeDefinitionsTable, $translatedContentAttributeValuesTable, $transactionsTable,
	$textAttributeValuesTable, $optionAttributeOptionsTable, $optionAttributeValuesTable, $classAttributesTable,
	$urlAttributeValuesTable;
	
$transactionsTable = new Table("{$dc}_transactions", false, array('transaction_id'));
$meaningRelationsTable = new Table("{$dc}_meaning_relations", true, array('relation_id'));
$classMembershipsTable = new Table("{$dc}_class_membership", true, array('class_membership_id'));
$collectionMembershipsTable = new Table("{$dc}_collection_contents", true, array('collection_id', 'member_mid'));
$syntransTable = new Table("{$dc}_syntrans", true, array('syntrans_sid'));
$translatedContentTable = new Table("{$dc}_translated_content", true, array('translated_content_id', 'language_id'));
$alternativeDefinitionsTable = new Table("{$dc}_alt_meaningtexts", true, array('meaning_mid', 'meaning_text_tcid'));
$classAttributesTable = new Table("{$dc}_class_attributes", true, array('object_id'));

$translatedContentAttributeValuesTable = new Table("{$dc}_translated_content_attribute_values", true, array('value_id'));
$textAttributeValuesTable = new Table("{$dc}_text_attribute_values", true, array('value_id'));
$urlAttributeValuesTable = new Table("{$dc}_url_attribute_values", true, array('value_id'));
$optionAttributeOptionsTable = new Table("{$dc}_option_attribute_options", true, array('attribute_id', 'option_mid'));
$optionAttributeValuesTable = new Table("{$dc}_option_attribute_values", true, array('value_id'));
$urlAttributeValuesTable = new Table("{$dc}_url_attribute_values", true, array('value_id'));

?>
