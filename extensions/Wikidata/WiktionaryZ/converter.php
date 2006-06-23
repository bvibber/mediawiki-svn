<?php

require_once('type.php');
require_once('attribute.php');

interface Converter {
	public function convert($value);
	public function getHeading();
}

class IdentityConverter {
	protected $attribute;
	protected $heading;
	
	public function __construct($attribute) {
		$this->attribute = $attribute;
		$this->heading = new Heading(array($attribute));
	}
	
	public function convert($tuple) {
		$result = new ArrayTuple($this->heading);
		$result->setAttributeValue($this->attribute, $tuple->getAttributeValue($this->attribute));
		
		return $result;
	}
	
	public function getHeading() {
		return $this->heading;
	}
}

class DefaultConverter implements Converter {
	protected $attribute;
	protected $heading;
	
	public function __construct($attribute) {
		$this->attribute = $attribute;
		$this->heading = new Heading(array($attribute));
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

class DefiningExpressionConverter extends DefaultConverter {
	public function convert($tuple) {
		$result = new ArrayTuple($this->heading);
		$result->setAttributeValue($this->attribute, definingExpressionAsLink($tuple->getAttributeValue($this->attribute)));
		
		return $result;
	}
}

class ExpressionConverter extends DefaultConverter {
	protected $attributes = array();
	
	public function __construct($attribute) {
		global 
			$languageAttribute, $spellingAttribute;
			
		parent::__construct($attribute);
		$this->heading = new Heading(array($languageAttribute, $spellingAttribute));
	}
	
	public function getHeading() {
		return $this->heading;
	}
	
	public function convert($tuple) {
		global
			$languageAttribute, $spellingAttribute;
		
		$dbr =& wfGetDB(DB_SLAVE);
		$expressionId = $tuple->getAttributeValue($this->attribute);
		$queryResult = $dbr->query("SELECT language_id, spelling from uw_expression_ns WHERE expression_id=$expressionId");
		$expression = $dbr->fetchObject($queryResult); 

		$result = new ArrayTuple($this->heading);
		$result->setAttributeValue($languageAttribute, languageIdAsText($expression->language_id));
		$result->setAttributeValue($spellingAttribute, spellingAsLink($expression->spelling));
	
		return $result;
	}
}

?>
