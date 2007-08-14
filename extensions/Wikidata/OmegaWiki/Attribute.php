<?php

class Attribute {
	public $id = null;	
	public $name = "";
	public $type = "";

	/**
	 * @param $id   (String) or null if 
 	 * @param $name (String) 
	 * @param $type (String or Structure) 
	 *  If String, can be "language", "spelling", "boolean",
         *  "defined-meaning", "defining-expression", "relation-type", "attribute",
	 *  "collection", "short-text", "text"
	 *
	 *  If Structure, see below.
 	 */
	public function __construct($id, $name, $type) {
		$this->id = $id;
		$this->name = $name;
		$this->setAttributeType($type);
	}

	public function setAttributeType($type) {
		# Copy the structure since we might modify it
		if($type instanceof Structure) {
			$this->type=clone $type;
		} else {
			$this->type=$type;
		}

		// Since the attribute is a structure and unnamed, we use 
		// the default label associated with it.
		if(is_null($this->id) && ($this->type instanceof Structure)) {
			$this->id = $this->type->getStructureType();
		// Override structure label with a more specific one
		} elseif(!is_null($this->id) && ($this->type instanceof Structure)) {
			$this->type->setStructureType($this->id);
		}
	}

}

class Structure {
	private $structure; 
	private $type; 
	private $attributeIds;
	
	public function getAttributes() {
		return $this->structure;
	}

	public function addAttribute($attribute) {
		$this->structure[]=$attribute;
		$this->attributeIds[] = $attribute->id;
	}

	public function getStructureType() {
		return $this->type;
	}

	public function setStructureType($type) {
		$this->type=$type;
	}


	/**
	 * Construct named Structure which contains Attribute objects
	 *
	 * @param $type (String)  Identifying string that describes the structure.
	 *                        Optional; if not specified, will be considered
	 *                        'anonymous-structure' unless there is only a
	 *                        a single Attribute object, in which case the structure
	 *                        will inherit its ID. Do not pass null.
	 * @param $structure (Array or Parameter list) One or more Attribute objects. 
	 *
	 */
	public function __construct($argumentList) {
		# We're trying to be clever.
		$args=func_get_args();
		$this->structure=null;
		
		if($args[0] instanceof Attribute) {
			$this->structure=$args; 
		} elseif(is_array($args[0])) {
			$this->structure=$args[0];
		}

		if(is_array($this->structure)) {
			# We don't know what to call an unnamed
			# structure with multiple attributes.
			if(sizeof($this->structure)>1) {
				$this->type='anonymous-structure';			
			# Meh, just one Attribute. Let's eat it.
			} elseif(sizeof($this->structure)==1) {
				$this->type=$this->structure[0]->id;
			} else {
				$this->type='empty-structure';
			}

		# First parameter is the structure's name.
		} elseif(is_string($args[0]) && !empty($args[0])) {
			$this->type=$args[0];
			if(is_array($args[1])) {
				$this->structure=$args[1];
			} else {
				array_shift($args);
				$this->structure=$args;
			}
		} else {
			# WTF?
			throw new Exception("Invalid structure constructor: ".print_r($args,true));
		}
		
		$this->attributeIds = array();
		
		foreach ($this->structure as $attribute)
			$this->attributeIds[] = $attribute->id;
	}
	
	public function __toString() {
		return "Structure(" . implode(", ", $this->attributeIds) . ")";
	}
	
	public function supportsAttributeId($attributeId) {
		return true;	
//	return in_array($attributeId, $this->attributeIds);
	}
}

class AttributeSet {
	protected $attributes = array();

	protected function __set($key, $value) {
		$attributes=&$this->attributes;
		$attributes[$key] = $value;
	
		if ($value instanceof Attribute) 
			$GLOBALS[$key . "Attribute"] = $value;
		else
			$GLOBALS[$key] = $value;
	}
	
	public function __get($key) {
		$attributes=&$this->attributes;
		
		if (!array_key_exists($key, $attributes)) 
			throw new Exception("Key does not exist: " . $key);
		
		return $attributes[$key];
	}	
}
