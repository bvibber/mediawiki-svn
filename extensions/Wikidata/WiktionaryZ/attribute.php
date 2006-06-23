<?php

class Attribute {
	public $id = "";	
	public $name = "";
	public $type = "";
	
	public function __construct($id, $name, $type) {
		$this->id = $id;	
		$this->name = $name;
		$this->type = $type;
	}
}

class Heading {
	public $attributes;
	
	public function __construct($attributes) {
		$this->attributes = $attributes;
	}
}

global
	$languageAttribute, $spellingAttribute, $textAttribute, $identicalMeaningAttribute, $internalIdAttribute, 
	$collectionAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute, $expressionAttribute, $attributeAttribute;

$expressionAttribute = new Attribute("expression", "Expression", "expression");
$languageAttribute = new Attribute("language", "Language", "language");
$spellingAttribute = new Attribute("spelling", "Spelling", "spelling");
$textAttribute = new Attribute("text", "Text", "text");

$identicalMeaningAttribute = new Attribute("endemic-meaning", "Identical meaning?", "boolean");
$collectionAttribute = new Attribute("collection", "Collection", "collection");
$internalIdAttribute = new Attribute("internal-id", "Internal ID", "short-text"); 

$attributeAttribute = new Attribute("attribute", "Attribute", "attribute");
$relationTypeAttribute = new Attribute("relation-type", "Relation type", "relation-type"); 
$otherDefinedMeaningAttribute = new Attribute("other-defined-meaning", "Other defined meaning", "defining-expression");

?>
