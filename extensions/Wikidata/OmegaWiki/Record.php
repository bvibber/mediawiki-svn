<?php

require_once('Attribute.php');

interface Record {
	public function getStructure();
	public function getAttributeValue($attribute);
	public function project($structure);
}

class ArrayRecord implements Record {
	protected $structure;
	protected $values = array();
	protected $type=""; # a string defining what kind of record this is
			    # (added when definin save behaviour)
			    # determines the kind of RecordHelper we get
			    # from a RecordHelperFactory...
	
	public function __construct($structure,$type="") {
		$this->structure = $structure;
		$this->setType($type);
	}
	
	public function getStructure() {
		return $this->structure;
	}
	
	public function getType() {
		return $this->type;
	}

	public function setType($type) {
		$this->type=$type;
	}

	public function getAttributeValue($attribute) {
		#FIXME: check if valid
		return @$this->values[$attribute->id];
	}
	

	public function project($structure) {
		$result = project($this, $structure);
		return $result;
	}

	public function setAttributeValue($attribute, $value) {
		$this->values[$attribute->id] = $value;
	}
	
	/**
	 *
	 * @param $values Array to write into the record, by order of the structure
	 *
	 */
	public function setAttributeValuesByOrder($values) {
		for ($i = 0; $i < count($this->structure->attributes); $i++)
			$this->values[$this->structure->attributes[$i]->id] = $values[$i];
	}
	
	/*
	 *
	 * @param $record Another record object whose values get written into this one
	 *
	 */
	public function setSubRecord($record) {
		foreach($record->getStructure()->attributes as $attribute)
			$this->values[$attribute->id] = $record->getAttributeValue($attribute);
	}

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
	public function _tostring_indent($depth=0,$key="") {
		$rv="\n".str_pad("",$depth*8);	
		
		$type=$this->getType();
		$rv.="$key:ArrayRecord(..., $type) {";

		$rv2=$rv; # algorithm: variant of implode() using foreach loop
		foreach ($this->values as $key=>$value) {
			$rv=$rv2; 

			$repr="$key:$value"; # we do normal php string conversion ...
			$methods=get_class_methods(get_class($value));
			if (!is_null($methods)) {
				if (in_array("_tostring_indent",$methods)) {
					//... unless we can do _tostring_indent
					$repr=$value->_tostring_indent($depth+1,$key);
					
					
				} 
			}
			$rv.=$repr; 

			$rv2=$rv; 
			$rv2.=", "; # we only remember $rv, so comma gets lost on last iteration
		}
		$rv.="}";
		return $rv;
	}

}

function project($record, $structure) {
	$result = new ArrayRecord($structure);
	
	foreach ($structure->attributes as $attribute) {
		$type = $attribute->type;
		$value = $record->getAttributeValue($attribute);
		
		if ($type instanceof RecordType)
			$result->setAttributeValue($attribute, project($record, $type->getStructure()));
		else
			$result->setAttributeValue($attribute, $value);
	}
		
	return $result;
}

function equalRecords($structure, $lhs, $rhs) {
	$result = true;
	$attributes = $structure->attributes;
	$i = 0;
	
	while($result && $i < count($attributes)) {
		$attribute = $attributes[$i];
		$type = $attribute->type;
		$lhsValue = $lhs->getAttributeValue($attribute);
		$rhsValue = $rhs->getAttributeValue($attribute);
		
		if ($type instanceof RecordType)
			$result = equalRecords($type->getStructure(), $lhsValue, $rhsValue);
		else
			$result = $lhsValue == $rhsValue;
			
		$i++;
	}
	
	return $result;
}

class RecordStack {
	protected $stack = array();
	
	public function push($record) {
		$this->stack[] = $record;
	}
	
	public function pop() {
		return array_pop($this->stack);
	}
	
	public function peek($level) {
		return $this->stack[count($this->stack) - $level - 1];
	}
}

?>
