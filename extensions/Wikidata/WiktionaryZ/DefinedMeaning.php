<?php

require_once('Wikidata.php');
require_once('WiktionaryZRecordSets.php');
require_once('WiktionaryZEditors.php');


class DefinedMeaning extends DefaultWikidataApplication {
	function view() {
		global
			$wgOut, $wgTitle;

		parent::view();

		$definedMeaningId = $wgTitle->getText();
		$wgOut->addHTML(getDefinedMeaningEditor()->view(new IdStack("defined-meaning"), getDefinedMeaningRecord($definedMeaningId, 0)));
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");
	}
}


?>
