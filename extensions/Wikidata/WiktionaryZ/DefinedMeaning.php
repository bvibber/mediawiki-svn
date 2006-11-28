<?php

require_once('Wikidata.php');
require_once('WiktionaryZRecordSets.php');
require_once('WiktionaryZEditors.php');

class DefinedMeaning extends DefaultWikidataApplication {
	public function view() {
		global
			$wgOut, $wgTitle;

		parent::view();

//		$definedMeaningId = $wgTitle->getText();
		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
		$wgOut->addHTML(getDefinedMeaningEditor(false)->view($this->getIdStack($definedMeaningId), getDefinedMeaningRecord($definedMeaningId, new QueryLatestTransactionInformation())));
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");
	}
	
	public function edit() {
		global
			$wgOut, $wgTitle;

		parent::edit();

//		$definedMeaningId = $wgTitle->getText();
		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());

		$this->outputEditHeader();
		$wgOut->addHTML(getDefinedMeaningEditor(false)->edit($this->getIdStack($definedMeaningId), getDefinedMeaningRecord($definedMeaningId, new QueryLatestTransactionInformation())));
		$this->outputEditFooter();
	}
	
	public function history() {
		global
			$wgOut, $wgTitle;

		parent::history();

//		$definedMeaningId = $wgTitle->getText();
		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
		$wgOut->addHTML(getDefinedMeaningEditor($this->showRecordLifeSpan)->view(
			new IdStack("defined-meaning"), 
			getDefinedMeaningRecord($definedMeaningId, $this->queryTransactionInformation))
		);
		
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");

		$titleArray = $wgTitle->getTitleArray();
		$titleArray["actionprefix"] = wfMsg('wz_history');
		$wgOut->setPageTitleArray($titleArray);
	}

	protected function save($referenceTransaction) {
		global
			$wgTitle;

//		$definedMeaningId = $wgTitle->getText();
		$definedMeaningId = $this->getDefinedMeaningIdFromTitle($wgTitle->getText());
		getDefinedMeaningEditor(false)->save($this->getIdStack($definedMeaningId), getDefinedMeaningRecord($definedMeaningId, $referenceTransaction));
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
	// get id from title: DefinedMeaning:expression(id)
		$bracketPosition = strrpos($title, "(");
		$definedMeaningId = substr($title, $bracketPosition + 1, strlen($title) - $bracketPosition - 2);
		return $definedMeaningId;
	}	
}

?>
