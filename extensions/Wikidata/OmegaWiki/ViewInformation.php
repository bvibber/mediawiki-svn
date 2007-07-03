<?php

class ViewInformation {
	public $filterLanguageId;
	public $possiblySynonymousRelationTypeId;
	public $queryTransactionInformation;
	public $showRecordLifeSpan; 
	public $showAuthority;
	
	public function __construct() {
		$this->filterLanguageId = 0;
		$this->possiblySynonymousRelationTypeId = 0;
		$this->queryTransactionInformation;
		$this->showRecordLifeSpan = false; 
		$this->showAuthority = false;
	}
}

?>
