<?php

/**
 * Special page listing all protected pages in the wiki
 *
 * @addtogroup SpecialPage
 */
class SpecialProtectedPages extends SpecialPage {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'Protectedpages' );
	}
	
	/**
	 * Special page execution function
	 *
	 * @param mixed $par Parameters passed to the page
	 */
	public function execute( $par = false ) {
		$this->setHeaders();
		$this->showList();
	}

	/**
	 * Show the list of protected pages
	 *
	 * @param string $msg Optional subtitle
	 */
	private function showList( $msg = '' ) {
		global $wgOut, $wgRequest;
		if( $msg != '' )
			$wgOut->setSubtitle( $msg );

		// Purge expired entries on one in every 10 queries
		if ( !mt_rand( 0, 10 ) ) {
			Title::purgeExpiredRestrictions();
		}

		$type = $wgRequest->getVal( 'type', 'edit' );
		$level = $wgRequest->getVal( 'level' );
		$sizetype = $wgRequest->getVal( 'sizetype' );
		$size = $wgRequest->getIntOrNull( 'size' );
		$NS = $wgRequest->getIntOrNull( 'namespace' );

		$pager = new ProtectedPagesPager( $type, $level, $NS, $sizetype, $size );	

		$wgOut->addHTML( $this->showOptions( $NS, $type, $level, $sizetype, $size ) );

		if( $pager->getNumRows() > 0 ) {
			$wgOut->addHtml(
				$pager->getNavigationBar()
				. '<ul>' . $pager->getBody() . '</ul>'
				. $pager->getNavigationBar()
			);
		} else {
			$wgOut->addHtml( wfMsgExt( 'protectedpagesempty', 'parse' ) );
		}
	}

	/**
	 * Build the filtering option panel
	 *
	 * @param int $namespace Pre-select namespace
	 * @param string $type Pre-select type
	 * @param string $level Pre-select level
	 * @param string $sizetype Pre-select size bound
	 * @param string $size Pre-fill size
	 * @return string
	 */
	function showOptions( $namespace, $type = 'edit', $level, $sizetype, $size ) {
		global $wgScript;
		$self = SpecialPage::getTitleFor( 'Protectedpages' );
		return
			Xml::openElement( 'form', array( 'method' => 'get', 'action' => $wgScript ) )
			. Xml::hidden( 'title', $self->getPrefixedUrl() )
			. '<fieldset><legend>' . wfMsgHtml( 'protectedpages-legend' ). '</legend>'
			. '<table>'
			. '<tr>' . $this->getNamespaceMenu( $namespace ) . '</tr>'
			. '<tr>' . $this->getTypeMenu( $type ) . '</tr>'
			. '<tr>' . $this->getLevelMenu( $level ) . '</tr>'
			. '<tr>' . $this->getSizeLimit( $sizetype, $size ) . '</tr>'
			. '<tr><td></td><td>' . Xml::submitButton( wfMsg( 'protectedpages-submit' ) ) . '</td></tr>'
			. '</table></fieldset></form>';
	}
	
	/**
	 * Build a namespace selector
	 *
	 * @param int $select Pre-selected namespace
	 * @return string
	 */
	private function getNamespaceMenu( $select = null ) {
		return
			'<td><label for="namespace">' . wfMsgHtml( 'namespace' ) . '</label></td>'
			. '<td>' . Xml::namespaceSelector( $select, '' ) . '</td>';
	}

	/**
	 * Build a type selection list
	 *
	 * @param string $select Pre-selected type
	 * @return string
	 */
	private function getTypeMenu( $select ) {
		global $wgRestrictionTypes;
		$options = array();
		foreach( $wgRestrictionTypes as $type ) {
			$options[] = Xml::option(
				wfMsg( 'restriction-' . $type ),
				$type,
				$type == $select
			);
		}
		return
			'<td><label for="type">' . wfMsgHtml( 'restriction-type' ) . '</label></td>'
			. '<td>' . Xml::tags( 'select', array( 'id' => 'type', 'name' => 'type' ),
				implode( '', $options ) ) . '</td>';	
	}

	/**
	 * Build a level selection menu
	 *
	 * @param int $select Pre-selected level
	 * @return string
	 */
	private function getLevelMenu( $select ) {
		global $wgRestrictionLevels;
		$options = array();
		$options[] = Xml::option(
			wfMsg( 'restriction-level-all' ),
			0,
			$select == 0
		);
		foreach( $wgRestrictionLevels as $level ) {
			if( $level != '' && $level != '*' ) {
				$options[] = Xml::option(
					wfMsg( 'restriction-level-' . $level ),
					$level,
					$level == $select
				);
			}
		}
		return
			'<td><label for="level">' . wfMsgHtml( 'restriction-level' ) . '</label></td>'
			. '<td>' . Xml::tags( 'select', array( 'id' => 'level', 'name' => 'level' ),
				implode( '', $options ) ); 
	}


	/**
	 * Build a size limit box
	 *
	 * @param int $select Pre-select min or max
	 * @param int $size Pre-fill size value
	 * @return string
	 */
	private function getSizeLimit( $select, $size ) {
		$radios = array();
		foreach( array( 'min', 'max' ) as $bound ) {
			$radios[] = Xml::radioLabel(
				wfMsg( 'protectedpages-size-' . $bound ),
				'sizetype',
				$bound,
				'wpSize' . $bound,
				$bound == $select
			);
		}
		return
			'<tr><td><label for="wpSize">' . wfMsgHtml( 'protectedpages-size' ) . '</label></td>'
			. '<td>' . Xml::input( 'size', 9, $size, array( 'id' => 'wpSize' ) )
			. ' ' . wfMsgHtml( 'pagesize' ) . '</td></tr><tr><td></td><td>'
			. implode( '&nbsp;', $radios ) . '</td>';
	}
	
}


?>
