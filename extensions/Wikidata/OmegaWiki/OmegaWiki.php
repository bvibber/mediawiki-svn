<?php

require_once('Wikidata.php');
require_once('Transaction.php');
require_once('WikiDataAPI.php');
require_once('forms.php');
require_once('Attribute.php');
require_once('type.php');
require_once('languages.php');
require_once('HTMLtable.php');
require_once('OmegaWikiRecordSets.php');
require_once('OmegaWikiEditors.php');
require_once('WikiDataGlobals.php');

/**
 * Load and modify content in a OmegaWiki-enabled
 * namespace.
 *
 */
class OmegaWiki extends DefaultWikidataApplication {
	public function view() {
		global
			$wgOut, $wgTitle;

		parent::view();

		$this->outputViewHeader();

		$spelling = $wgTitle->getText();
		
		$wgOut->addHTML(
			getExpressionsEditor($spelling, $this->filterLanguageId, $this->possiblySynonymousRelationTypeId, false, $this->shouldShowAuthorities)->view(
				$this->getIdStack(), 
				getExpressionsRecordSet(
					$spelling, 
					$this->filterLanguageId, 
					$this->possiblySynonymousRelationTypeId, 
					$this->viewQueryTransactionInformation
				)
			)
		);
		
		$this->outputViewFooter();
	}

	public function history() {
		global
			$wgOut, $wgTitle;

		parent::history();


		$spelling = $wgTitle->getText();
		
		$wgOut->addHTML(
			getExpressionsEditor($spelling, $this->filterLanguageId, $this->possiblySynonymousRelationTypeId, $this->showRecordLifeSpan, false)->view(
				$this->getIdStack(), 
				getExpressionsRecordSet(
					$spelling, 
					$this->filterLanguageId, 
					$this->possiblySynonymousRelationTypeId, 
					$this->queryTransactionInformation
				)
			)
		);
		
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");
	}

	protected function save($referenceTransaction) {
		global
			$wgTitle;

		parent::save($referenceTransaction);

		$spelling = $wgTitle->getText();
		
		getExpressionsEditor($spelling, $this->filterLanguageId, $this->possiblySynonymousRelationTypeId, false, false)->save(
			$this->getIdStack(), 
			getExpressionsRecordSet(
				$spelling, 
				$this->filterLanguageId, 
				$this->possiblySynonymousRelationTypeId, 
				$referenceTransaction
			)
		);
	}

	public function edit() {
		global
			$wgOut, $wgTitle, $wgUser;

		if(!parent::edit()) return false;
		$this->outputEditHeader();

		$spelling = $wgTitle->getText();

		$wgOut->addHTML(
			getExpressionsEditor($spelling, $this->filterLanguageId, $this->possiblySynonymousRelationTypeId, false, false)->edit(
				$this->getIdStack(), 
				getExpressionsRecordSet(
					$spelling, 
					$this->filterLanguageId, 
					$this->possiblySynonymousRelationTypeId, 
					new QueryLatestTransactionInformation()
				)
			)
		);

		$this->outputEditFooter();
	}
	
	public function getTitle() {
		global
			$wgTitle, $wgExpressionPageTitlePrefix;
	
		if ($wgExpressionPageTitlePrefix != "")
			$prefix = $wgExpressionPageTitlePrefix . ": ";
		else
			$prefix	= "";
					
		return $prefix . $wgTitle->getText();
	}
	
	protected function getIdStack() {
		return new IdStack("expression");
	}
}

?>
