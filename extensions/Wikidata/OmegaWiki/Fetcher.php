<?php

interface Fetcher {
	public function __construct($attributeLevel, $attribute);
	public function fetch($keyPath);	
}

class DefaultFetcher implements Fetcher {
	protected $attributeLevel;
	protected $attribute;
	
	public function __construct($attributeLevel, $attribute) {
		$this->attributeLevel = $attributeLevel;
		$this->attribute = $attribute;
	}
	public function fetch($keyPath) {
		return $keyPath->peek($this->attributeLevel)->getAttributeValue($this->attribute);			
	}	
}

class ObjectIdFetcher extends DefaultFetcher {
//	protected $objectIdAttributeLevel;
//	protected $objectIdAttribute;
//	
//	public function __construct($objectIdAttributeLevel, $objectIdAttribute) {
//		$this->objectIdAttributeLevel = $objectIdAttributeLevel;
//		$this->objectIdAttribute = $objectIdAttribute;
//	}
//	public function fetch($keyPath, $record) {
//		return $keyPath->peek($this->objectIdAttributeLevel)->getAttributeValue($this->objectIdAttribute);			
//	}
}

class DefinitionObjectIdFetcher extends DefaultFetcher {
	public function fetch($keyPath) {
		$definedMeaningId = $keyPath->peek($this->attributeLevel)->getAttributeValue($this->attribute);
		return getDefinedMeaningDefinitionId($definedMeaningId);
	}	
}

?>
