<?php

/* Copyright (C) 2006 by Charta Software
 *   http://www.charta.org/
 */ 

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

function project($tuple, $heading) {
	$result = new ArrayTuple($heading);
	
	foreach ($heading->attributes as $attribute)
		$result->setAttributeValue($attribute, $tuple->getAttributeValue($attribute));
		
	return $result;
}

function equalTuples($heading, $lhs, $rhs) {
	$result = true;
	$attributes = $heading->attributes;
	$i = 0;
	
	while($result && $i < count($attributes)) {
		$attribute = $attributes[$i];
		$type = $attribute->type;
		$lhsValue = $lhs->getAttributeValue($attribute);
		$rhsValue = $rhs->getAttributeValue($attribute);
		
		if (is_a($type, TupleType))
			$result = equalTuples($type->getHeading(), $lhsValue, $rhsValue);
		else
			$result = $lhsValue == $rhsValue;
			
		$i++;
	}
	
	return $result;
}

class TupleStack {
	protected $stack = array();
	
	public function push($tuple) {
		$this->stack[] = $tuple;
	}
	
	public function pop() {
		return array_pop($this->stack);
	}
	
	public function peek($level) {
		return $this->stack[count($this->stack) - $level - 1];
	}
}

?>
