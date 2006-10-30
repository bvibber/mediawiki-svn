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
		$wgOut->addHTML(getExpressionsEditor($spelling, false)->view(new IdStack("expression"), getExpressionsRecordSet($spelling, new QueryLatestTransactionInformation())));
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");

		# We may later want to disable the regular page component
		# $wgOut->setPageTitleArray($this->mTitle->getTitleArray());
	}

	public function history() {
		global
			$wgOut, $wgTitle;

		parent::history();

		$spelling = $wgTitle->getText();
		$wgOut->addHTML(getExpressionsEditor($spelling, $this->showRecordLifeSpan)->view(new IdStack("expression"), getExpressionsRecordSet($spelling, $this->queryTransactionInformation)));
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");

		$titleArray = $wgTitle->getTitleArray();
		$titleArray["actionprefix"] = wfMsg('wz_history');
		$wgOut->setPageTitleArray($titleArray);
	}

	protected function save($referenceTransaction) {
		global
			$wgTitle;

		$spelling = $wgTitle->getText();
		getExpressionsEditor($spelling, false)->save(new IdStack("expression"), getExpressionsRecordSet($spelling, $referenceTransaction));
	}

	public function edit() {
		global
			$wgOut, $wgTitle, $wgUser;

		parent::edit();

		$spelling = $wgTitle->getText();

		$this->outputEditHeader();
		$wgOut->addHTML(getExpressionsEditor($spelling, false)->edit(new IdStack("expression"), getExpressionsRecordSet($spelling, new QueryLatestTransactionInformation())));
		$this->outputEditFooter();
	}
}

?>
