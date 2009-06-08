<?php
/**
 * @file
 * @ingroup SpecialPage
 */
 
/**
 * Special page lists templates with a large number of
 * transclusion links, i.e. "most used" templates
 *
 * @ingroup SpecialPage
 * @author Rob Church <robchur@gmail.com>
 */
class MostlinkedTemplatesPage extends QueryPage {
	public function __construct() {
		SpecialPage::__construct( 'Mostlinkedtemplates' );
	}

	public function isExpensive() {
		return true;
	}

	public function isSyndicated() {
		return false;
	}

	public function sortDescending() {
		return true;
	}

	public function getQueryInfo() {
		return array (
			'tables' => array ( 'templatelinks' ),
			'fields' => array ( 'tl_namespace AS namespace',
					'tl_title AS title',
					'COUNT(*) AS value' ),
			'conds' => array ( 'tl_namespace' => NS_TEMPLATE ),
			'options' => array( 'GROUP BY' => 'tl_title' )
		);
	}

	/**
	 * Pre-cache page existence to speed up link generation
	 *
	 * @param Database $dbr Database connection
	 * @param int $res Result pointer
	 */
	public function preprocessResults( $db, $res ) {
		$batch = new LinkBatch();
		while( $row = $db->fetchObject( $res ) ) {
			$batch->add( $row->namespace, $row->title );
		}
		$batch->execute();
		if( $db->numRows( $res ) > 0 )
			$db->dataSeek( $res, 0 );
	}

	public function formatResult( $skin, $result ) {
		$title = Title::makeTitle( $result->namespace, $result->title );

		$skin->link( $title );
		return wfSpecialList(
			$skin->link( $title ),
			$this->makeWlhLink( $title, $skin, $result )
		);
	}

	/**
	 * Make a "what links here" link for a given title
	 *
	 * @param Title $title Title to make the link for
	 * @param Skin $skin Skin to use
	 * @param object $result Result row
	 * @return string
	 */
	private function makeWlhLink( $title, $skin, $result ) {
		global $wgLang;
		$wlh = SpecialPage::getTitleFor( 'Whatlinkshere' );
		$label = wfMsgExt( 'nlinks', array( 'parsemag', 'escape' ),
		$wgLang->formatNum( $result->value ) );
		return $skin->link( $wlh, $label, array(), array( 'target' => $title->getPrefixedText() ) );
	}
}
