<?php

require_once('attribute.php');

interface Tuple {
	public function getHeading();
	public function getAttributeValue($attribute);
	public function project($heading);
}

class ArrayTuple implements Tuple {
	protected $heading;
	protected $values = array();
	
	public function __construct($heading) {
		$this->heading = $heading;
	}
	
	public function getHeading() {
		return $this->heading;
	}
	
	public function getAttributeValue($attribute) {
		return $this->values[$attribute->id];
	}
	

	public function project($heading) {
		$result = new ArrayTuple($heading);
		
		foreach($heading->attributes as $attribute)
			$result->setAttributeValue($attribute, $this->getAttributeValue($attribute));		
	}

	public function setAttributeValue($attribute, $value) {
		$this->values[$attribute->id] = $value;
	}
	
	public function setAttributeValuesByOrder($values) {
		for ($i = 0; $i < count($this->heading->attributes); $i++)
			$this->values[$this->heading->attributes[$i]->id] = $values[$i];
	}
	
	public function setSubTuple($tuple) {
		foreach($tuple->getHeading()->attributes as $attribute)
			$this->values[$attribute->id] = $tuple->getAttributeValue($attribute);
	}
}

?>
