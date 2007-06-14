<?php

require_once('forms.php');
require_once('converter.php');
require_once('Attribute.php');
require_once('Record.php');

abstract class RecordSet {
	public abstract function getStructure();
	public abstract function getKey();
	public abstract function getRecordCount();
	public abstract function getRecord($index);
	# public function save(); # <- we first need to implement, then uncomment

	/**
	 * @return a string representation of this object
	 */
	public function __tostring() {
		return $this->_tostring_indent();
	}
	
	/**
	 * Replacement for the __tostring contract, with support for indentation.
	 * Splitting structures out over multiple lines and using indentation
	 * helps a lot! 
	 *  
	 * Uses duck-typing to discover if an entity supports _tostring_indent,
	 * else uses the original/normal php string-conversion.
	 *
	 * lots of shared code, so might be nice to refactor if we use it a lot.
	 */
	public function _tostring_indent($depth=0,$key="",$myname="RecordSet") {
		$rv="\n".str_pad("",$depth*8);
		$rv.="$key:$myname {";
		$rv2=$rv;
		foreach ($this->records as $value) {
			$rv=$rv2;
			$methods=get_class_methods(get_class($value));
			if (!is_null($methods)) {
				if (in_array("_tostring_indent",$methods)) {
					$value=$value->_tostring_indent($depth+1);
				}
			}
			$rv.="$value";

			$rv2=$rv;
			$rv2.=", ";
		}
		$rv.="}";

		return $rv;
	}


}

class ArrayRecordSet extends RecordSet {
	protected $structure;
	protected $key;
	protected $records = array();
	
	public function __construct($structure, $key) {
		$this->structure = $structure;
		$this->key = $key;
	}
	
	public function add($record) {
		$this->records[] = $record;
	}

	public function addRecord($values, $type="") {
		$record = new ArrayRecord($this->structure, $type);
		$record->setAttributeValuesByOrder($values);

		$this->records[] = $record;				
	}
	
	public function getStructure() {
		return $this->structure;
	}
	
	public function getKey() {
		return $this->key;	
	}
	
	public function getRecordCount() {
		return count($this->records);
	}
	
	public function getRecord($index) {
		return $this->records[$index];
	}
	
	public function tostring_indent($depth=0,$key="",$myname="") {
		return parent::tostring_indent($depth,$key,$myname."_ArrayRecordSet");
	}
	
}

class ConvertingRecordSet extends RecordSet {
	protected $relation;
	protected $converters;
	protected $structure;
	
	public function __construct($relation, $converters) {
		$this->relation = $relation;
		$this->converters = $converters;
		$this->structure = $this->determineStructure();
	}

	public function getStructure() {
		return $this->structure;
	}
	
	public function getKey() {
		return $this->relation->getKey();	
	}
	
	public function getRecordCount() {
		return $this->relation->getRecordCount();	
	}
	
	public function getRecord($index) {
		$record = $this->relation->getRecord($index);
		$result = new ArrayRecord($this->structure);
		
		foreach ($this->converters as $converter) 
			$result->setSubRecord($converter->convert($record));
			
		return $result;
	}
	
	protected function determineStructure() {
		$attributes = array();

		foreach ($this->converters as $converter) 
			$attributes = array_merge($attributes, $converter->getStructure()->attributes);
			
		return new Structure($attributes);
	}

	public function tostring_indent($depth=0,$key="",$myname="") {
		return parent::tostring_indent($depth,$key,$myname."_ConvertingRecordSet");
	}
}

function getRelationAsHTMLList($relation) {
	$structure = $relation->getStructure();

	$result = getStructureAsListStructure($structure);
	$result .= '<ul class="wiki-data-unordered-list">';
	
	for($i = 0; $i < $relation->getRecordCount(); $i++) {
		$record = $relation->getRecord($i);
		$result .= '<li>';
		$result .= getRecordAsListItem($structure, $record);
		$result .= '</li>';
	}
	
	$result .='</ul>';
	return $result;
}

function getStructureAsListStructure($structure) {
	$result = '<h5>';
	
	foreach($structure->attributes as $attribute) {
		$result .= getAttributeAsText($attribute);
		$result .= ' - ';
	}
	
	$result = rtrim($result, ' - ') . '</h5>';
	return $result;
}

function getAttributeAsText($attribute){
	$type = $attribute->type;
	if (is_a($type, RecordType)) {
		$structure = $type->getStructure();
		foreach($structure->attributes as $innerAttribute) {
			$result .= getAttributeAsText($innerAttribute);
			$result .= ' - ';
		}
		$result = rtrim($result, ' - ');
	}
	else {
		$result = $attribute->name;
	}
	return $result;
}

function getRecordAsListItem($structure, $record) {
	$result = '';
	
	foreach($structure->attributes as $attribute) {
		$type = $attribute->type;
		$value = $record->getAttributeValue($attribute);
		
		if (is_a($type, RecordType)) {
			$result .= getRecordAsListItem($type->getStructure(), $value);	
		}
		else {
			$result .= convertToHTML($value, $type);			
		}
		$result .= ' - ';
	}
	$result = rtrim($result, ' - ');		
	
	return $result;
}

function getRecordKeyName($record, $key) {
	$ids = array();
	
	foreach($key->attributes as $attribute)
		$ids[] = $record->getAttributeValue($attribute);
	
	return implode("-", $ids);
}

function splitRecordSet($recordSet, $groupAttribute) {
	$result = array();
	$structure = $recordSet->getStructure();
	$key = $recordSet->getKey();
	
	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) {
		$record = $recordSet->getRecord($i);
		$groupAttributeValue = $record->getAttributeValue($groupAttribute);
		$groupRecordSet = $result[$groupAttributeValue];
		
		if ($groupRecordSet == null) {
			$groupRecordSet = new ArrayRecordSet($structure, $key);
			$result[$groupAttributeValue] = $groupRecordSet;
		}
		
		$groupRecordSet->add($record);
	}
	
	return $result; 
}

?>
