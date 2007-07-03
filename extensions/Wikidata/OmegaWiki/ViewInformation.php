<?php

/**
 * ViewInformation is used to capture various settings that influence the way a page will be viewed
 * depending on different use case scenarios. For instance, by specifying a filterLanguageId, a page
 * will be filtered entirely on one language, collapsing record sets to records where appropiate.
 * 
 * A ViewInformation can be constructed based on various conditions. The language filtering for instance
 * could be an application wide setting, or a setting that can be controlled by the user. Functions that
 * use ViewInformation do not care about this. They are supposed to respect the settings provided wherever
 * possible.  
 */

class ViewInformation {
	public $filterLanguageId;
	public $possiblySynonymousRelationTypeId;
	public $queryTransactionInformation;
	public $showRecordLifeSpan; 

	public function __construct() {
		$this->filterLanguageId = 0;
		$this->possiblySynonymousRelationTypeId = 0;
		$this->queryTransactionInformation;
		$this->showRecordLifeSpan = false; 
	}
	
	public function hasMetaDataAttributes() {
		return $this->showRecordLifeSpan;
	}
	
	public function filterOnLanguage() {
		return $this->filterLanguageId != 0;
	}
}

?>
