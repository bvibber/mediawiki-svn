<?php

// Special:Code/MediaWiki/stats
class CodeRepoStatsView extends CodeView {

	function __construct( $repoName ) {
		parent::__construct();
		$this->mRepo = CodeRepository::newFromName( $repoName );
	}

	function execute() {
		global $wgOut, $wgLang, $wgSkin;

		$stats = RepoStats::newFromRepo( $this->mRepo );
		$repoName = $this->mRepo->getName();
		$wgOut->wrapWikiMsg( '<h2 id="stats-main">$1</h2>', array( 'code-stats-header', $repoName ) );
		$wgOut->addWikiMsg( 'code-stats-main',
			$wgLang->timeanddate( $stats->time, true ),
			$wgLang->formatNum( $stats->revisions ),
			$repoName,
			$wgLang->formatNum( $stats->authors )
		);

		if ( !empty( $stats->states ) ) {
			$wgOut->wrapWikiMsg( '<h3 id="stats-revisions">$1</h3>', 'code-stats-staus-breakdown' );
			$wgOut->addHTML( '<table class="TablePager">'
				. '<tr><th>' . wfMsgHtml( 'code-field-status' ) . '</th><th>'
				. wfMsgHtml( 'code-stats-count' ) . '</th></tr>' );
			foreach ( CodeRevision::getPossibleStates() as $state ) {
				$count = isset( $stats->states[$state] ) ? $stats->states[$state] : 0;
				$count = htmlspecialchars( $wgLang->formatNum( $count ) );
				$link = $this->mSkin->link(
					SpecialPage::getTitleFor( 'Code', $repoName . '/status/' . $state ),
					htmlspecialchars( $this->statusDesc( $state ) )
				);
				$wgOut->addHTML( "<tr class=\"mw-codereview-status-$state\"><td>$link</td>"
					. "<td>$count</td></tr>" );
			}
			$wgOut->addHTML( '</table>' );
		}

		if ( !empty( $stats->fixmes ) ) {
			$wgOut->wrapWikiMsg( '<h3 id="stats-fixme">$1</h3>', 'code-stats-fixme-breakdown' );
			$wgOut->addHTML( '<table class="TablePager">'
				. '<tr><th>' . wfMsgHtml( 'code-field-author' ) . '</th><th>'
				. wfMsgHtml( 'code-stats-count' ) . '</th></tr>' );
			foreach ( $stats->fixmes as $user => $count ) {
				$count = htmlspecialchars( $wgLang->formatNum( $count ) );
				$link = $this->mSkin->link(
					SpecialPage::getTitleFor( 'Code', $repoName . '/author/' . $user ),
					htmlspecialchars( $user )
				);
				$wgOut->addHTML( "<tr><td>$link</td>"
					. "<td>$count</td></tr>" );
			}
			$wgOut->addHTML( '</table>' );
		}
	}
}