<?php

class ScalarType {
	protected $id;
	
	public function __construct($id) {
		$this->id = $id;
	}
}

class TupleType {
	protected $heading;	
	
	public function __construct($heading) {
		$this->heading = $heading;
	}
	
	public function getHeading() {
		return $this->heading;
	}
}

class RelationType {
	protected $heading;

	public function __construct($heading) {
		$this->heading = $heading;
	}
	
	public function getHeading() {
		return $this->heading;
	}
}

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
		if (is_array($attributes))
			$this->attributes = $attributes;
		else
			$this->attributes = func_get_args();
	}
}

global
	$languageAttribute, $spellingAttribute, $textAttribute, $identicalMeaningAttribute, $sourceIdentifierAttribute, 
	$collectionAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute, $expressionIdAttribute, $attributeAttribute,
	$expressionAttribute, $visibleExpressionAttribute, $definitionIdAttribute, $alternativeDefinitionAttribute;

$expressionIdAttribute = new Attribute("expression-id", "Expression Id", "expression-id");
$languageAttribute = new Attribute("language", "Language", "language");
$spellingAttribute = new Attribute("spelling", "Spelling", "spelling");
$expressionAttribute = new Attribute("expression", "Expression", new TupleType(new Heading($languageAttribute, $spellingAttribute)));

$textAttribute = new Attribute("text", "Text", "text");
$identicalMeaningAttribute = new Attribute("indentical-meaning", "Identical meaning?", "boolean");
$collectionAttribute = new Attribute("collection", "Collection", "collection");
$sourceIdentifierAttribute = new Attribute("source-identifier", "Source identifier", "short-text"); 

$attributeAttribute = new Attribute("attribute", "Attribute", "attribute");
$relationTypeAttribute = new Attribute("relation-type", "Relation type", "relation-type"); 
$otherDefinedMeaningAttribute = new Attribute("other-defined-meaning", "Other defined meaning", "defining-expression");
$definitionIdAttribute = new Attribute("definition-id", "Definition identifier", "integer");
$alternativeDefinitionAttribute = new Attribute("alternative-definition", "Alternative definition", new RelationType($alternativeDefinitionHeading));

?>
