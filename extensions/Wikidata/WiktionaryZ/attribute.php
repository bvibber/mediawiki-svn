<?php

/* Copyright (C) 2006 by Charta Software
 *   http://www.charta.org/
 */ 

class ScalarType {
	protected $id;
	
	public function __construct($id) {
		$this->id = $id;
	}
}

class TupleType {
	protected $heading;	
	
	public function __construct($heading) {
		$this->heading = $heading;
	}
	
	public function getHeading() {
		return $this->heading;
	}
}

class RelationType {
	protected $heading;

	public function __construct($heading) {
		$this->heading = $heading;
	}
	
	public function getHeading() {
		return $this->heading;
	}
}

class Attribute {
	public $id = "";	
	public $name = "";
	public $type = "";
	
	public function __construct($id, $name, $type) {
		$this->id = $id;	
		$this->name = $name;
		$this->type = $type;
	}
}

class Heading {
	public $attributes;
	
	public function __construct($attributes) {
		if (is_array($attributes))
			$this->attributes = $attributes;
		else
			$this->attributes = func_get_args();
	}
}

?>
