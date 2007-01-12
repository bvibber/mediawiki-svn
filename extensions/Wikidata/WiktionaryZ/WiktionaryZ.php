<?php

require_once('Wikidata.php');
require_once('Transaction.php');
require_once('Expression.php');
require_once('forms.php');
require_once('Attribute.php');
require_once('type.php');
require_once('languages.php');
require_once('HTMLtable.php');
require_once('WiktionaryZRecordSets.php');
require_once('WiktionaryZEditors.php');

/**
 * Load and modify content in a WiktionaryZ-enabled
 * namespace.
 *
 * @package MediaWiki
 */
class WiktionaryZ extends DefaultWikidataApplication {
	public function view() {
		global
			$wgOut, $wgTitle;

		parent::view();

		$spelling = $wgTitle->getText();
		$expressionsValue = getExpressionsValue($spelling, $this->filterLanguageId, $this->viewQueryTransactionInformation);
		
		if ($expressionsValue != null) 
			$wgOut->addHTML(
				getExpressionsEditor($spelling, $this->filterLanguageId, false, $this->shouldShowAuthorities)->view(
					$this->getIdStack($spelling, $this->filterLanguageId), 
					$expressionsValue
				)
			);
		
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");
	}

	public function history() {
		global
			$wgOut, $wgTitle;

		parent::history();

		$spelling = $wgTitle->getText();
		$expressionsValue = getExpressionsValue($spelling, $this->filterLanguageId, $this->queryTransactionInformation);
		
		if ($expressionsValue != null)
			$wgOut->addHTML(
				getExpressionsEditor($spelling, $this->filterLanguageId, $this->showRecordLifeSpan, false)->view(
					$this->getIdStack($spelling, $this->filterLanguageId), 
					$expressionsValue
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
		$expressionsValue = getExpressionsValue($spelling, $this->filterLanguageId, $referenceTransaction);
		
		if ($expressionsValue != null)
			getExpressionsEditor($spelling, $this->filterLanguageId, false, false)->save(
				$this->getIdStack($spelling, $this->filterLanguageId), 
				$expressionsValue
			);
	}

	public function edit() {
		global
			$wgOut, $wgTitle, $wgUser;

		parent::edit();
		$this->outputEditHeader();

		$spelling = $wgTitle->getText();
		$expressionsValue = getExpressionsValue($spelling, $this->filterLanguageId, new QueryLatestTransactionInformation());

		if ($expressionsValue != null)
			$wgOut->addHTML(
				getExpressionsEditor($spelling, $this->filterLanguageId, false, false)->edit(
					$this->getIdStack($spelling, $this->filterLanguageId), 
					$expressionsValue
				)
			);

		$this->outputEditFooter();
	}
	
	public function getTitle() {
		global
			$wgTitle;
			
		return "Disambiguation: " . $wgTitle->getText();
	}
	
	protected function getIdStack($spelling, $filterLanguageId) {
		global
			$expressionIdAttribute;
			
		$idStack = new IdStack("expression");

		if ($filterLanguageId != 0) {
			$expressionId = getExpressionIdThatHasSynonyms($spelling, $filterLanguageId);
			
			if ($expressionId != 0) {
				$expressionIdStructure = new Structure($expressionIdAttribute);
				$expressionIdRecord = new ArrayRecord($expressionIdStructure, $expressionIdStructure);
				$expressionIdRecord->setAttributeValue($expressionIdAttribute, $expressionId);
			}	
			
			$idStack->pushKey($expressionIdRecord);
		}
		
		return $idStack;
	}
}

?>
