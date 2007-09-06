<?php

require_once('Attribute.php');

interface AttributeIDFilter {
	public function filter(array $attributeIDs);
}

class IncludeAttributeIDsFilter implements AttributeIDFilter {
	protected $attributeIDsToInclude;
	
	public function __construct($attributeIDsToInclude) {
		$this->attributeIDsToInclude = $attributeIDsToInclude;
	}

	public function filter(array $attributeIDs) {
		$result = array();
		
		foreach ($attributeIDs as $attributeID) 
			if (in_array($attributeID, $this->attributeIDsToInclude))
				$result[] = $attributeID;
			
		return $result;
	}
}

class ExcludeAttributeIDsFilter implements AttributeIDFilter {
	protected $attributeIDsToExclude;
	
	public function __construct($attributeIDsToExclude) {
		$this->attributeIDsToExclude = $attributeIDsToExclude;
	}

	public function filter(array $attributeIDs) {
		$result = array();
		
		foreach ($attributeIDs as $attributeID) 
			if (!in_array($attributeID, $this->attributeIDsToExclude))
				$result[] = $attributeID;
			
		return $result;
	}
}

class PropertyToColumnFilter {
	public $attributeIDs;   	// Array containing the defined meaning ids of the attributes that should be filtered
	protected $attribute;   	// Attribute
	protected $propertyCaption; // Caption of the first column
	
        public function __construct($identifier, $propertyCaption, array $attributeIDs) {
        	$this->attributeIDs = $attributeIDs;
                $this->attribute = new Attribute($identifier, $propertyCaption, "will-be-specified-later");
                $this->propertyCaption = $propertyCaption;
	}                                                                
                                                                	
	public function getAttribute() {
		return $this->attribute;
	} 
	
	public function getPropertyCaption() {
		return $this->propertyCaption;
	}
	
	public function getAttributeIDFilter() {
		return new IncludeAttributeIDsFilter($this->attributeIDs);
	}
}

?>
