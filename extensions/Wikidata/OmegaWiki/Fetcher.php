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
		if ($keyPath->peek($this->attributeLevel)->getStructure()->supportsAttribute($this->attribute))
			return $keyPath->peek($this->attributeLevel)->getAttributeValue($this->attribute); 
		else
			return null; # FIXME: Should not happen, check should leave	when the reason of the attribute not being support by the record is determined 		
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


