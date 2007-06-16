<?php

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
	
	private $structure; # Array of attributes
	
	public function getAttributes() {
		return $this->structure;
	}

	public function addAttribute($attribute) {
		$this->structure[]=$attribute;
	}

	public function __construct($structure) {
		if (is_array($structure))
			$this->structure = $structure;
		else
			$this->structure = func_get_args();
	}
}

?>
