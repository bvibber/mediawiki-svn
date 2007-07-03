<?php

require_once('Attribute.php');
require_once('RecordHelper.php');

interface Record {
	public function getStructure();
	public function getAttributeValue(Attribute $attribute);
	public function project(Structure $structure);
}

class ArrayRecord implements Record {
	protected $structure;
	protected $values = array();
	protected $helper=null;
	
	public function __construct(Structure $structure) {
		$this->structure = $structure;
		$this->helper=RecordHelperFactory::getRecordHelper($this);		
	}
	
	public function getStructure() {
		return $this->structure;
	}
	
	public function getAttributeValue(Attribute $attribute) {
		#FIXME: check if valid
		return @$this->values[$attribute->id];
	}
	

	public function project(Structure $structure) {
		$result = project($this, $structure);
		return $result;
	}

	public function setAttributeValue(Attribute $attribute, $value) {
		#FIXME: check if valid
		@$this->values[$attribute->id] = $value;
	}
	
	/**
	 *
	 * @param $values Array to write into the record, by order of the structure
	 *
	 */
	public function setAttributeValuesByOrder($values) {
		$atts=$this->structure->getAttributes();
		for ($i = 0; $i < count($atts); $i++)
			$this->values[$atts[$i]->id] = $values[$i];
	}
	
	/*
	 *
	 * @param $record Another record object whose values get written into this one
	 *
	 */
	public function setSubRecord(Record $record) {
		foreach($record->getStructure()->getAttributes() as $attribute)
			$this->values[$attribute->id] = $record->getAttributeValue($attribute);
	}

	/** 
	 * @return comma-separated values
	 */
	public function __tostring() {
		return $this->tostring_indent();
	}
	
	public function tostring_indent($depth=0,$key="") {
		$rv="\n".str_pad("",$depth*8);	
		$str=$this->getStructure();
		$type=$str->getStructureType();
		$rv.="$key:ArrayRecord(..., $type) {";
		$rv2=$rv;
		foreach ($this->values as $key=>$value) {
			$rv=$rv2;
			$methods=get_class_methods(get_class($value));
			$repr="$key:$value";
			if (!is_null($methods)) {
				if (in_array("tostring_indent",$methods)) {
					$repr=$value->tostring_indent($depth+1,$key);
				} 
			}
			$rv.=$repr;

			$rv2=$rv;
			$rv2.=", ";
		}
		$rv.="}";
		return $rv;
	}

}

function project(Record $record, Structure $structure) {
	$result = new ArrayRecord($structure);
	
	foreach ($structure->getAttributes() as $attribute) {
		$type = $attribute->type;
		$value = $record->getAttributeValue($attribute);
		
		if ($type instanceof Structure)
			$result->setAttributeValue($attribute, project($record, $type->getStructure()));
		else
			$result->setAttributeValue($attribute, $value);
	}
		
	return $result;
}

function equalRecords(Structure $structure, Record $lhs, Record $rhs) {
	$result = true;
	$attributes = $structure->getAttributes();
	$i = 0;
	
	while ($result && $i < count($attributes)) {
		$attribute = $attributes[$i];
		$type = $attribute->type;
		$lhsValue = $lhs->getAttributeValue($attribute);
		$rhsValue = $rhs->getAttributeValue($attribute);
		
		if ($type instanceof Structure)
			$result = $lhsValue instanceof Record && $rhsValue instanceof Record && equalRecords($type, $lhsValue, $rhsValue);
		else
			$result = $lhsValue == $rhsValue;
			
		$i++;
	}
	
	return $result;
}

class RecordStack {
	protected $stack = array();
	
	public function push(Record $record) {
		$this->stack[] = $record;
	}
	
	public function pop() {
		return array_pop($this->stack);
	}
	
	public function peek($level) {
		return $this->stack[count($this->stack) - $level - 1];
	}
}


