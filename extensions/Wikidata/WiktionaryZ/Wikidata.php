<?php

require_once("forms.php");

interface WikidataApplication {
	public function view();
	public function edit();
	public function history();
}

class DefaultWikidataApplication implements WikidataApplication {
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
			$wgOut;
			
		$wgOut->addHTML($this->getLanguageSelector());
	}
	
	protected function outputEditHeader() {
		global
			$wgOut;
			
		$wgOut->addHTML('<form method="post" action="">');
	}
	
	protected function outputEditFooter() {
		global
			$wgOut, $wgTitle;
		
		$wgOut->addHTML('<div class="save-panel">');
			$wgOut->addHTML('<table cellpadding="0" cellspacing="0"><tr><th>' . wfMsg('summary') . ': </th><td>' . getTextBox("summary") .'</td></tr></table>');
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
