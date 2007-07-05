<?php

require_once('Attribute.php');

class PropertyToColumnFilter {
	public $attributeIDs;   // Array containing the defined meaning ids of the attributes that should be filtered
	protected $attribute;   // Attribute
	
	public function __construct($identifier, $caption, array $attributeIDs) {
		$this->attributeIDs = $attributeIDs;
		$this->attribute = new Attribute($identifier, $caption, "will-be-specified-later");		
	}
	
	public function getAttribute() {
		return $this->attribute;
	} 
}

?>
