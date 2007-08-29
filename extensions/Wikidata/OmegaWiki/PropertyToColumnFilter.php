<?php

require_once('Attribute.php');

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
}

?>
