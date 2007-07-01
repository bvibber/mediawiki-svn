<?php

require_once('Attribute.php');
require_once('RecordHelper.php');

interface Record {
	public function getStructure();
	public function getAttributeValue($attribute);
	public function project($structure);
}

class ArrayRecord implements Record {
	protected $structure;
	protected $values = array();
	protected $type = null;
	protected $helper=null;
	
	public function __construct($structure) {
		$this->structure = $structure;
	}
	
	public function getStructure() {
		return $this->structure;
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
		#FIXME: check if valid
		@$this->values[$attribute->id] = $value;
	}
	
	public function getType() {
		return $this->type;
	}

	public function setType($type) {
		$this->type=$type;
		$this->helper=RecordHelperFactory::getRecordHelper($this);
	}	

	/**only setType if it wasn't set yet.
	*@param $type the type to set
	*@return the type that is actually used now.
	*/ 
	public function suggestType($type) {
		if(is_null($this->type))
			$this->setType($type);
		return $this->getType();
	}

	/** temporary hack to complete an arrayrecord structure
	 * Uses knowlege already present in our Record based structure
	 * to explain to records what they are. (ie, finish completes the
	 * building of the structure, to leave it in a usable state)
	 * @param $type  the type that this record should have.
	 * 		 (if you have no idea, use some random but readily
	 * 		 recognisable string, other records should still get
	 *		 correct types)
	 * The brokenness of the system ends here, and only pretty code
 	 * should run beyond this point. (One day ). Erik Moeller is working
	 * on eliminating this function which would be excellent.
	 */
	public function finish($type) {
		$type=$this->suggestType($type);

		foreach ($this->values as $key=>$value) {
			$methods=get_class_methods(get_class($value));
			if (!is_null($methods)) {
				if (in_array("finish",$methods)) {
					$value->finish($key);
				} 
			}
		}
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
	public function setSubRecord($record) {
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
		$type=$this->type;
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

function project($record, $structure) {
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

function equalRecords($structure, $lhs, $rhs) {
	$result = true;
	$attributes = $structure->getAttributes();
	$i = 0;
	
	while($result && $i < count($attributes)) {
		$attribute = $attributes[$i];
		$type = $attribute->type;
		$lhsValue = $lhs->getAttributeValue($attribute);
		$rhsValue = $rhs->getAttributeValue($attribute);
		
		if ($type instanceof Structure)
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


