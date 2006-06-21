<?php

require_once('type.php');
require_once('attribute.php');

interface Converter {
	public function convert($value);
	public function getAttributes();
}

class IdentityConverter {
	protected $attribute;
	
	public function __construct($attribute) {
		$this->attribute = $attribute;
	}
	
	public function convert($tuple) {
		return array($tuple[$this->attribute->id]);
	}
	
	public function getAttributes() {
		return array($this->attribute);
	}
}

class DefaultConverter implements Converter {
	protected $attribute;
	
	public function __construct($attribute) {
		$this->attribute = $attribute;
	}
	
	public function convert($tuple) {
		return array(convertToHTML($tuple[$this->attribute->id], $this->attribute->type));
	}
	
	public function getAttributes() {
		return array($this->attribute);
	}
}

class DefiningExpressionConverter extends DefaultConverter {
	public function convert($tuple) {
		return array(definingExpressionAsLink($tuple[$this->attribute->id]));
	}
}

class ExpressionConverter extends DefaultConverter {
	protected $attributes = array();
	
	public function __construct($attribute) {
		parent::__construct($attribute);
		$this->attributes[] = new Attribute("language", "Language", "language"); 
		$this->attributes[] = new Attribute("spelling", "Spelling", "spelling");
	}
	
	public function getAttributes() {
		return $this->attributes;
	}
	
	public function convert($tuple) {
		$dbr =& wfGetDB(DB_SLAVE);
		$expressionId = $tuple[$this->attribute->id];
		$queryResult = $dbr->query("SELECT language_id, spelling from uw_expression_ns WHERE expression_id=$expressionId");
		$expression = $dbr->fetchObject($queryResult); 
	
		return array(languageIdAsText($expression->language_id), spellingAsLink($expression->spelling));
	}
}

?>
