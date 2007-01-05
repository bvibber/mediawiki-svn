<?php

require_once('Wikidata.php');
require_once('WiktionaryZRecordSets.php');
require_once('WiktionaryZEditors.php');

class DefinedMeaning extends DefaultWikidataApplication {
	public function view() {
		global
			$wgOut, $wgTitle;

		parent::view();

		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
		
		$wgOut->addHTML(
			getDefinedMeaningEditor($this->filterLanguageId, false, $this->shouldShowAuthorities)->view(
				$this->getIdStack($definedMeaningId), 
				getDefinedMeaningRecord($definedMeaningId, $this->filterLanguageId, $this->viewQueryTransactionInformation)
			)
		);
		
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");
	}
	
	public function edit() {
		global
			$wgOut, $wgTitle;

		parent::edit();

		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());

		$this->outputEditHeader();
		$wgOut->addHTML(
			getDefinedMeaningEditor($this->filterLanguageId, false, false)->edit(
				$this->getIdStack($definedMeaningId), 
				getDefinedMeaningRecord($definedMeaningId, $this->filterLanguageId, new QueryLatestTransactionInformation())
			)
		);
		$this->outputEditFooter();
	}
	
	public function history() {
		global
			$wgOut, $wgTitle;

		parent::history();

		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
		$wgOut->addHTML(
			getDefinedMeaningEditor($this->filterLanguageId, $this->showRecordLifeSpan, false)->view(
				new IdStack("defined-meaning"), 
				getDefinedMeaningRecord($definedMeaningId, $this->filterLanguageId, $this->queryTransactionInformation)
			)
		);
		
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");
	}

	protected function save($referenceTransaction) {
		global
			$wgTitle;

		parent::save($referenceTransaction);

		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
		getDefinedMeaningEditor($this->filterLanguageId, false, false)->save(
			$this->getIdStack($definedMeaningId), 
			getDefinedMeaningRecord($definedMeaningId, $this->filterLanguageId, $referenceTransaction)
		);
	}
	
	protected function getIdStack($definedMeaningId) {
		global
			$definedMeaningIdAttribute;
			
		$definedMeaningIdStructure = new Structure($definedMeaningIdAttribute);
		$definedMeaningIdRecord = new ArrayRecord($definedMeaningIdStructure, $definedMeaningIdStructure);
		$definedMeaningIdRecord->setAttributeValue($definedMeaningIdAttribute, $definedMeaningId);	
		
		$idStack = new IdStack("defined-meaning");
		$idStack->pushKey($definedMeaningIdRecord);
		
		return $idStack;
	}
	
	protected function getDefinedMeaningIdFromTitle($title) {
		// get id from title: DefinedMeaning:expression (id)
		$bracketPosition = strrpos($title, "(");
		$definedMeaningId = substr($title, $bracketPosition + 1, strlen($title) - $bracketPosition - 2);
		return $definedMeaningId;
	}	
	
	public function getTitle() {
		global	
			$wgTitle;
		
		return definedMeaningExpression($this->getDefinedMeaningIdFromTitle($wgTitle->getText()));
	}
}

?>
