<?php

// Special:Code/MediaWiki/status
class CodeStatusListView extends CodeView {
	function __construct( $repoName ) {
		parent::__construct();
		$this->mRepo = CodeRepository::newFromName( $repoName );
	}

	function execute() {
		global $wgOut;
		$name = $this->mRepo->getName();
		$states = CodeRevision::getPossibleStates();
		$wgOut->addWikiText( "== " . wfMsg ( "code-field-status" ) . " ==\n" );

		$table_rows = '';
		foreach ( $states as $state ) {
			$link = $this->mSkin->link(
				SpecialPage::getTitleFor( 'Code', $name . "/status/$state" ),
				wfMsg( "code-status-".$state )
			);
			$table_rows .= "<tr><td class=\"mw-codereview-status-$state\">$link</td>"
				. "<td>" . wfMsg( "code-status-desc-" . $state ) . "</td></tr>\n" ;
		}
		$wgOut->addHTML( '<table class="TablePager">'
			. '<tr><th>toto</th><th>tata</th></tr>'
			. $table_rows
			. '</table>'   
		);
	}
}
