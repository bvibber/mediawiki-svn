<?php

class PropertyToColumnFilter {
	public $identifier; 	// The identifier of the attribute that will be created
	public $caption;   	 	// The caption of the attribute that will be created
	public $attributeIDs;   // Array containing the defined meaning ids of the attributes that should be filtered
	
	public function __construct($identifier, $caption, array $attributeIDs) {
		$this->identifier = $identifier;
		$this->caption = $caption;
		$this->attributeIDs = $attributeIDs;
	} 
}

?>
