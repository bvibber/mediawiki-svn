<?php

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
	
$transactionsTable = new Table('transactions', false, array('transaction_id'));
$meaningRelationsTable = new Table('uw_meaning_relations', true, array('relation_id'));
$classMembershipsTable = new Table('uw_class_membership', true, array('class_membership_id'));
$collectionMembershipsTable = new Table('uw_collection_contents', true, array('collection_id', 'member_mid'));
$syntransTable = new Table('uw_syntrans', true, array('syntrans_sid'));
$translatedContentTable = new Table('translated_content', true, array('translated_content_id', 'language_id'));
$alternativeDefinitionsTable = new Table('uw_alt_meaningtexts', true, array('meaning_mid', 'meaning_text_tcid'));
$classAttributesTable = new Table('uw_class_attributes', true, array('object_id'));

$translatedContentAttributeValuesTable = new Table('uw_translated_content_attribute_values', true, array('value_id'));
$textAttributeValuesTable = new Table('uw_text_attribute_values', true, array('value_id'));
$urlAttributeValuesTable = new Table('uw_url_attribute_values', true, array('value_id'));
$optionAttributeOptionsTable = new Table('uw_option_attribute_options', true, array('attribute_id', 'option_mid'));
$optionAttributeValuesTable = new Table('uw_option_attribute_values', true, array('value_id'));
$urlAttributeValuesTable = new Table('uw_url_attribute_values', true, array('value_id'));

?>
