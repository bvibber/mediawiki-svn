<?php

/**
 * Special page exports the current user's watchlist
 * into list format
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */
class SpecialExportWatchlist extends SpecialPage {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'ExportWatchlist' );
	}
	
	/**
	 * Main execution point
	 *
	 * @param mixed $par Parameter passed to the page
	 */
	public function execute( $par = false ) {
		global $wgUser, $wgRequest, $wgOut;
		$this->setHeaders();
		$namespace = $wgRequest->getIntOrNull( 'namespace' );
		
		if( !$wgUser->isLoggedIn() ) {
			$this->showLoginPage( $wgOut, $wgUser );
			return;
		}

		$wgOut->addHtml( wfMsgExt( 'exportwatchlist-header', 'parse' ) );
		$wgOut->addHtml( $this->buildForm( $namespace ) );
		
		if( $wgRequest->getCheck( 'export' ) ) {
			$list = $this->getWatchlist( $wgUser, $namespace );
			if( $list->numRows() > 0 ) {
				$wgOut->addHtml( '<pre>' );
				while( $row = $list->fetchObject() ) {
					$title = Title::makeTitleSafe( $row->wl_namespace, $row->wl_title );
					if( $title instanceof Title ) {
						if( !$title->isTalkPage() )
							$wgOut->addHtml( htmlspecialchars( $title->getPrefixedText() ) . "\n" );
					} else {
						$text = htmlspecialchars( $row->wl_title );
						$wgOut->addHtml( "<!-- Invalid title: {$row->wl_namespace}, '{$text}' -->\n" );
					}
				}
				$wgOut->addHtml( '</pre>' );
			} else {
				$msg = $namespace === false
					? 'exportwatchlist-none'
					: 'exportwatchlist-none-ns';
				$wgOut->addHtml( wfMsgExt( $msg, 'parse' ) );
			}
		}
		
	}
	
	/**
	 * Get all items on a user's watchlist
	 *
	 * @param User $user User to fetch for
	 * @param mixed $namespace Namespace to fetch
	 * @return ResultWrapper
	 */
	private function getWatchlist( $user, $namespace ) {
		$dbr = wfGetDB( DB_SLAVE );
		$conds = array( 'wl_user' => $user->getId() );
		if( !is_null( $namespace ) )
			$conds['wl_namespace'] = $namespace;
		$res = $dbr->select(
			'watchlist',
			'*',
			$conds,
			__METHOD__
		);
		return new ResultWrapper( $dbr, $res );		
	}
	
	/**
	 * Build the options form
	 *
	 * @param mixed $namespace Pre-select namespace
	 * @return string
	 */
	private function buildForm( $namespace ) {
		global $wgScript;
		$form  = Xml::openElement( 'form', array( 'method' => 'get', 'action' => $wgScript ) );
		$form .= Xml::hidden( 'title', $this->getTitle()->getPrefixedUrl() );
		$form .= Xml::hidden( 'export', 1 );
		$form .= '<fieldset><legend>' . wfMsgHtml( 'exportwatchlist-legend' ) . '</legend>';
		$form .= '<table><tr><td>' . Xml::label( wfMsg( 'exportwatchlist-namespace' ), 'namespace' ) . '</td>';
		$form .= '<td>' . $this->buildNamespaceSelector( $namespace ) . '</td></tr>';
		$form .= '<tr><td></td><td>' .  Xml::submitButton( wfMsg( 'exportwatchlist-submit' ) ) . '</td></tr></table>';
		$form .= '</fieldset>';
		$form .= Xml::closeElement( 'form' );
		return $form;
	}
	
	/**
	 * Build a namespace selector which omits discussion namespaces
	 *
	 * @param mixed $namespace Pre-select namespace
	 * @return string
	 */
	private function buildNamespaceSelector( $namespace ) {
		global $wgContLang;
		$options[] = Xml::option( wfMsg( 'namespacesall' ), '' );
		foreach( $wgContLang->getFormattedNamespaces() as $index => $name ) {
			if( $index < 0 || Namespace::isTalk( $index ) )
				continue;
			if( $index == NS_MAIN )
				$name = wfMsg( 'blanknamespace' );
			$options[] = Xml::option( $name, $index, $index === $namespace );
		}
		return Xml::openElement( 'select', array( 'id' => 'namespace', 'name' => 'namespace' ) )
			. implode( "\n", $options ) . Xml::closeElement( 'select' );
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
		$out->addHtml( wfMsgHtml( 'exportwatchlist-login', $link ) );
	}

}

?>