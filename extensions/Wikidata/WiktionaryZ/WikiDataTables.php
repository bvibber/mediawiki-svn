<?php

class Table {
	public $name;
	public $isVersioned;
	
	public function __construct($name, $isVersioned) {
		$this->name = $name;
		$this->isVersioned = $isVersioned;
	}
}

global
	$tables, $meaningRelationsTable, $classMembershipsTable, $collectionMembershipsTable, $syntransTable, 
	$translatedContentTable, $alternativeDefinitionsTable, $translatedContentAttributeValuesTable, $transactionsTable,
	$textAttributeValuesTable;
	
$transactionsTable = new Table('transactions', false);
$meaningRelationsTable = new Table('uw_meaning_relations', true);
$classMembershipsTable = new Table('uw_class_membership', true);
$collectionMembershipsTable = new Table('uw_collection_contents', true);
$syntransTable = new Table('uw_syntrans', true);
$translatedContentTable = new Table('translated_content', true);
$alternativeDefinitionsTable = new Table('uw_alt_meaningtexts', true);

$translatedContentAttributeValuesTable = new Table('uw_translated_content_attribute_values', true);
$textAttributeValuesTable = new Table('uw_text_attribute_values', true);
$urlAttributeValuesTable = new Table('uw_url_attribute_values', true);

?>
