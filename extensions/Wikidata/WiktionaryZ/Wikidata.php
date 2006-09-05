<?php

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
}

?>
