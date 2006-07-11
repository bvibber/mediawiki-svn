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
	$expressionAttribute, $visibleExpressionAttribute, $definitionIdAttribute, $alternativeDefinitionAttribute,
	
	$definitionAttribute, $definedMeaningIdAttribute, $definedMeaningAttribute, $alternativeDefinitionsAttribute, 
	$synonymsAndTranslationsAttribute, $relationsAttribute, $attributesAttribute, $collectionMembershipAttribute,
	$expressionMeaningsAttribute, $expressionsAttribute;

$expressionIdAttribute = new Attribute("expression-id", "Expression Id", "expression-id");
$languageAttribute = new Attribute("language", "Language", "language");
$spellingAttribute = new Attribute("spelling", "Spelling", "spelling");
$expressionAttribute = new Attribute("expression", "Expression", new TupleType(new Heading($languageAttribute, $spellingAttribute)));

$textAttribute = new Attribute("text", "Text", "text");
$identicalMeaningAttribute = new Attribute("indentical-meaning", "Identical meaning?", "boolean");
$collectionAttribute = new Attribute("collection", "Collection", "collection");
$sourceIdentifierAttribute = new Attribute("source-identifier", "Source identifier", "short-text"); 

$attributeAttribute = new Attribute("attribute", "Class", "attribute");
$relationTypeAttribute = new Attribute("relation-type", "Relation type", "relation-type"); 
$otherDefinedMeaningAttribute = new Attribute("other-defined-meaning", "Other defined meaning", "defining-expression");
$definitionIdAttribute = new Attribute("definition-id", "Definition identifier", "integer");
$alternativeDefinitionAttribute = new Attribute("alternative-definition", "Alternative definition", new RelationType($alternativeDefinitionHeading));

$definedMeaningIdAttribute = new Attribute("defined-meaning-id", "Defined meaning identifier", "defined-meaning-id");
$definitionAttribute = new Attribute("definition", "Definition", new RelationType(new Heading($languageAttribute, $textId)));
$alternativeDefinitionsAttribute = new Attribute("alternative-definitions", "Alternative definitions", new RelationType(new Heading($definitionIdAttribute, $alternativeDefinitionAttribute)));
$synonymsAndTranslationsAttribute = new Attribute("synonyms-translations", "Synonyms and translations", new RelationType(new Heading($expressionIdAttribute, $expressionAttribute, $identicalMeaningAttribute)));
$relationsAttribute = new Attribute("relations", "Relations", new RelationType(new Heading($relationTypeAttribute, $otherDefinedMeaningAttribute)));
$attributesAttribute = new Attribute("attributes", "Class membership", new RelationType(new Heading($attributeAttribute)));
$collectionMembershipAttribute = new Attribute("collection-membership", "Collection membership", new RelationType(new Heading($collectionAttribute, $sourceIdentifierAttribute)));
$definedMeaningAttribute = new Attribute("defined-meaning", "Defined meaning", new TupleType(new Heading($definitionAttribute, $alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute)));
$expressionMeaningsAttribute = new Attribute("expression-meanings", "Defined meanings", new RelationType(new Heading($definedMeaningIdAttribute, $definedMeaningAttribute)));
$expressionsAttribute = new Attribute("expressions", "Expressions", new RelationType(new Heading($expressionIdAttribute, $expressionAttribute, $expressionMeaningsAttribute)));

?>
