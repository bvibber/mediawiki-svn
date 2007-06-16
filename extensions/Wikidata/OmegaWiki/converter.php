<?php

require_once('type.php');
require_once('Attribute.php');
require_once('Transaction.php');

require_once("Wikidata.php");
interface Converter {
	public function getStructure();
	public function convert($record);
}

class ProjectConverter implements Converter {
	protected $structure;
	
	public function __construct($structure) {
		$this->structure = $structure;
	} 
	
	public function getStructure() {
		return $this->structure;
	}
	
	public function convert($record) {
		$result = new ArrayRecord($this->structure);
		
		foreach($this->structure->getStructure() as $attribute)
			$result->setAttributeValue($attribute, $record->getAttributeValue($attribute));
			
		return $result;
	}
}

class DefaultConverter implements Converter {
	protected $attribute;
	protected $structure;
	
	public function __construct($attribute) {
		$this->attribute = $attribute;
		$this->structure = new Structure($attribute);
	}
	
	public function convert($record) {
		$result = new ArrayRecord($this->structure);
		$result->setAttributeValue($this->attribute, convertToHTML($record->getAttributeValue($this->attribute), $this->attribute->type));
		
		return $result;
	}
	
	public function getStructure() {
		return $this->structure;
	}
}

class ExpressionIdConverter extends DefaultConverter {
	protected $attributes = array();
	
	public function __construct($attribute) {
		global 
			$expressionAttribute;
			
		parent::__construct($attribute);
		$this->structure = new Structure($expressionAttribute);
	}
	
	public function getStructure() {
		return $this->structure;
	}
	
	public function convert($record) {
		$dc=wdGetDataSetContext();

		global
			$expressionAttribute, $expressionIdAttribute, $languageAttribute, $spellingAttribute;
		
		$dbr =& wfGetDB(DB_SLAVE);
		$expressionId = $record->getAttributeValue($this->attribute);
		$queryResult = $dbr->query("SELECT language_id, spelling from {$dc}_expression_ns WHERE expression_id=$expressionId" .
									" AND ". getLatestTransactionRestriction("{$dc}_expression_ns"));
		$expression = $dbr->fetchObject($queryResult); 

		$expressionRecord = new ArrayRecord(new Structure($languageAttribute, $spellingAttribute));
		$expressionRecord->setAttributeValue($languageAttribute, $expression->language_id);
		$expressionRecord->setAttributeValue($spellingAttribute, $expression->spelling);

		$result = new ArrayRecord($this->structure);
		$result->setAttributeValue($expressionAttribute, $expressionRecord);
	
		return $result;
	}
}

?>
