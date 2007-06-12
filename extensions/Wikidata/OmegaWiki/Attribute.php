<?php

class ScalarType {
	protected $id;
	
	public function __construct($id) {
		$this->id = $id;
	}
}

class RecordType {
	protected $structure;	
	
	public function __construct($structure) {
		$this->structure = $structure;
	}
	
	public function getStructure() {
		return $this->structure;
	}
}

class RecordSetType {
	protected $structure;

	public function __construct($structure) {
		$this->structure = $structure;
	}
	
	public function getStructure() {
		return $this->structure;
	}
}

class Attribute {
	public $id = "";	
	public $name = "";
	public $type = "";

	/**
 	 * @param $name (String)
	 * @param $id   (String)
	 * @param $type (String) "language", "spelling", "boolean", "defined-meaning", 
	 *                       "defining-expression", "relation-type", "attribute", "collection", "short-text", 
	 *               	 "text"..?
 	 */
	public function __construct($id, $name, $type) {
		$this->id = $id;	
		$this->name = $name;
		$this->type = $type;
	}

}

class Structure {
	public $attributes;
	
	public function __construct($attributes) {
		if (is_array($attributes))
			$this->attributes = $attributes;
		else
			$this->attributes = func_get_args();
	}
}

?>
