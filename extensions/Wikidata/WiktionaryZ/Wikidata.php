<?php

require_once("forms.php");

interface WikidataApplication {
	public function view();
	public function edit();
	public function history();
}

class DefaultWikidataApplication implements WikidataApplication {
	protected $showRecordLifeSpan;
	protected $transaction;
	protected $queryTransactionInformation;

	public function __construct() {
		global 
			$wgMessageCache;
		
		$wgMessageCache->addMessages(
			array(
				'wz_uilang'=>'Your user interface language: $1',
				'wz_uilang_set'=>'Set your preferences',
				'wz_save' => 'Save',
				'wz_history' => 'History'
			)
		);
	}

	function getLanguageSelector() {
		global 
			$wgUser;
		
		$userlang=$wgUser->getOption('language');
		$skin = $wgUser->getSkin();
		
		return wfMsg('wz_uilang',"<b>$userlang</b>").  " &mdash; " . $skin->makeLink("Special:Preferences", wfMsg('wz_uilang_set'));
	}

	public function view() {
		global
			$wgOut;
			
		$wgOut->addHTML($this->getLanguageSelector());
	}
	
	public function edit() {
		global
			$wgOut;
			
		$wgOut->addHTML($this->getLanguageSelector());
	}
	
	public function history() {
		global
			$wgOut, $wgTitle;
			
		if (isset($_GET['show'])) {
			$this->showRecordLifeSpan = isset($_GET["show-record-life-span"]);
			$this->transaction = (int) $_GET["transaction"];
		}	
		else {
			$this->showRecordLifeSpan = true;
			$this->transaction = 0;
		}
		
		if ($this->transaction == 0)
			$this->queryTransactionInformation = new QueryHistoryTransactionInformation();
		else
			$this->queryTransactionInformation = new QueryAtTransactionInformation($this->transaction);
			
		$wgOut->addHTML($this->getLanguageSelector());
		$wgOut->addHTML(
			'<div class="option-panel">' .
				'<form method="" action="">'.
					'<input type="hidden" name="title" value="'. $wgTitle->getNsText() . ':' . $wgTitle->getText() .'"/>'.
					'<input type="hidden" name="action" value="history"/>'.
					'<table cellpadding="0" cellspacing="0">'.
						'<tr><th>Transaction:</th><td class="option-field">'. getSuggest("transaction", "transaction", $_GET["transaction"]) . '</td></tr>'.
						'<tr><th>Show record life span:</th><td class="option-field">'. getCheckBox("show-record-life-span", $this->showRecordLifeSpan) . '</td></tr>'.
//						'<tr><th>Show most recent version only:</th><td class="option-field">'. getCheckBox("show-most-recent-version-only", isset($_GET["show-most-recent-version-only"])) . '</td></tr>'.
						'<tr><th/><td>'. getSubmitButton("show", "Show"). '</td></tr>'.
					'</table>'.
				'</form>'.
			'</div>'
		);
	}
	
	protected function outputEditHeader() {
		global
			$wgOut;
			
		$wgOut->addHTML('<form method="post" action="">');
	}
	
	protected function outputEditFooter() {
		global
			$wgOut, $wgTitle;
		
		$wgOut->addHTML('<div class="option-panel">');
			$wgOut->addHTML('<table cellpadding="0" cellspacing="0"><tr><th>' . wfMsg('summary') . ': </th><td class="option-field">' . getTextBox("summary") .'</td></tr></table>');
			$wgOut->addHTML(getSubmitButton("save", wfMsg('wz_save')));
		$wgOut->addHTML('</div>');
		$wgOut->addHTML('</form>');
		$wgOut->addHTML(DefaultEditor::getExpansionCss());
		$wgOut->addHTML("<script language='javascript'><!--\nexpandEditors();\n--></script>");

		$titleArray = $wgTitle->getTitleArray();
		$titleArray["actionprefix"] = wfMsg('editing');
		$wgOut->setPageTitleArray($titleArray);
	}
}

?>
