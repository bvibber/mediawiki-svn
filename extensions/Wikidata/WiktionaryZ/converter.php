<?php

require_once('type.php');
require_once('attribute.php');

interface Converter {
	public function getHeading();
	public function convert($tuple);
}

class ProjectConverter implements Converter {
	protected $heading;
	
	public function __construct($heading) {
		$this->heading = $heading;
	} 
	
	public function getHeading() {
		return $this->heading;
	}
	
	public function convert($tuple) {
		$result = new ArrayTuple($this->heading);
		
		foreach($this->heading->attributes as $attribute)
			$result->setAttributeValue($attribute, $tuple->getAttributeValue($attribute));
			
		return $result;
	}
}

class DefaultConverter implements Converter {
	protected $attribute;
	protected $heading;
	
	public function __construct($attribute) {
		$this->attribute = $attribute;
		$this->heading = new Heading($attribute);
	}
	
	public function convert($tuple) {
		$result = new ArrayTuple($this->heading);
		$result->setAttributeValue($this->attribute, convertToHTML($tuple->getAttributeValue($this->attribute), $this->attribute->type));
		
		return $result;
	}
	
	public function getHeading() {
		return $this->heading;
	}
}

class ExpressionIdConverter extends DefaultConverter {
	protected $attributes = array();
	
	public function __construct($attribute) {
		global 
			$expressionAttribute;
			
		parent::__construct($attribute);
		$this->heading = new Heading($expressionAttribute);
	}
	
	public function getHeading() {
		return $this->heading;
	}
	
	public function convert($tuple) {
		global
			$expressionAttribute, $expressionIdAttribute, $languageAttribute, $spellingAttribute;
		
		$dbr =& wfGetDB(DB_SLAVE);
		$expressionId = $tuple->getAttributeValue($this->attribute);
		$queryResult = $dbr->query("SELECT language_id, spelling from uw_expression_ns WHERE expression_id=$expressionId");
		$expression = $dbr->fetchObject($queryResult); 

		$expressionTuple = new ArrayTuple(new Heading($languageAttribute, $spellingAttribute));
		$expressionTuple->setAttributeValue($languageAttribute, $expression->language_id);
		$expressionTuple->setAttributeValue($spellingAttribute, $expression->spelling);

		$result = new ArrayTuple($this->heading);
		$result->setAttributeValue($expressionAttribute, $expressionTuple);
	
		return $result;
	}
}

?>
