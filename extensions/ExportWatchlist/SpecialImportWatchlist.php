<?php

/**
 * Special page bulk-adds items from a list to the
 * current user's watchlist
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */
class SpecialImportWatchlist extends SpecialPage {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'ImportWatchlist' );
	}
	
	/**
	 * Main execution point
	 *
	 * @param mixed $par Parameter passed to the page
	 */
	public function execute( $par = false ) {
		global $wgUser, $wgRequest, $wgOut, $wgLang;
		$this->setHeaders();
		
		if( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}
		
		$wgOut->addHtml( wfMsgExt( 'importwatchlist-header', 'parse' ) );
		$wgOut->addHtml( $this->buildForm() );
		
		if( $wgRequest->wasPosted() ) {
			$list = $this->extractTitles( $wgRequest->getText( 'titles' ) );
			if( ( $count = count( $list ) ) > 0 ) {
				$count = $wgLang->formatNum( $count );
				$this->bulkWatch( $wgUser, $list );
				$wgOut->addHtml( wfMsgExt( 'importwatchlist-success', 'parse', $count ) );
				$this->showTitles( $wgOut, $wgUser->getSkin(), $list );
			}
		}
	}
	
	/**
	 * Explode a blob of text into a list of titles
	 *
	 * @param string $text List of titles
	 * @return array
	 */
	private function extractTitles( $text ) {
		$titles = explode( "\n", $text );
		for( $i = 0; $i < count( $titles ); $i++ ) {
			$titles[$i] = Title::newFromText( $titles[$i] );
			if( !$titles[$i] instanceof Title )
				unset( $titles[$i] );
		}
		return $titles;
	}
	
	/**
	 * Bulk-insert titles into a specified user's watchlist
	 *
	 * @param User $user User to insert for
	 * @param array $titles Titles to watch
	 */
	private function bulkWatch( $user, $titles ) {
		$dbw = wfGetDB( DB_MASTER );
		$values = array();
		foreach( $titles as $title ) {
			$values[] = array(
				'wl_user' => $user->getId(),
				'wl_namespace' => ( $title->getNamespace() & ~1 ),
				'wl_title' => $title->getDBkey(),
				'wl_notificationtimestamp' => null,
			);
			$values[] = array(
				'wl_user' => $user->getId(),
				'wl_namespace' => ( $title->getNamespace() | 1 ),
				'wl_title' => $title->getDBkey(),
				'wl_notificationtimestamp' => null,
			);
		}
		$dbw->insert(
			'watchlist',
			$values,
			__METHOD__,
			'IGNORE'
		);
		$user->invalidateCache();
	}
	
	/**
	 * Build the title input form
	 *
	 * @return string
	 */
	private function buildForm() {
		$form  = Xml::openElement( 'form', array( 'method' => 'post', 
			'action' => $this->getTitle()->getLocalUrl() ) );
		$form .= '<fieldset><legend>' . wfMsgHtml( 'importwatchlist-legend' ) . '</legend>';
		$form .= Xml::label( wfMsg( 'importwatchlist-titles' ), 'titles' ) . '<br />';
		$form .= Xml::openElement( 'textarea', array( 'id' => 'titles', 'name' => 'titles',
			'rows' => 8, 'cols' => 80 ) ) . Xml::closeElement( 'textarea' );
		$form .= '<p>' . Xml::submitButton( wfMsg( 'importwatchlist-submit' ) ) . '</p>';
		$form .= '</fieldset></form>';
		return $form;
	}
	
	/**
	 * Print out a list of titles
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @param array $titles
	 */
	private function showTitles( $out, $skin, $titles ) {
		$batch = new LinkBatch();
		foreach( $titles as $title )
			$batch->addObj( $title );
		$batch->execute();
		$out->addHtml( '<ul>' );
		foreach( $titles as $title )
			$out->addHtml( '<li>' . $skin->makeLinkObj( $title ) . '</li>' );
		$out->addHtml( '</ul>' );
	}
	
	/**
	 * Show a "please log in" page
	 *
	 * @param OutputPage $out
	 * @param User $user
	 */
	private function showLoginPage( $out, $user ) {
		$link = $user->getSkin()->makeKnownLinkObj(
			SpecialPage::getTitleFor( 'Userlogin' ),
			wfMsgHtml( 'exportwatchlist-login-link' ),
			'returnto=' . $this->getTitle()->getPrefixedUrl()
		);
		$out->addHtml( wfMsgHtml( 'importwatchlist-login', $link ) );
	}

}