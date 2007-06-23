<?php

/**
 * Special page listing other special pages and reports
 *
 * @addtogroup SpecialPage
 */
class SpecialSpecialPages extends SpecialPage {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'Specialpages' );
	}
	
	/**
	 * Main execution function
	 *
	 * @param mixed $par Parameters passed to the page
	 */
	public function execute( $par = false ) {
		global $wgOut, $wgUser;
		$this->setHeaders();
		$skin = $wgUser->getSkin();
		# Reports will be mixed in with "regular pages", so
		# we'll fish these out into a separate list
		$reports = array();
		$normal = array();
		foreach( SpecialPage::getRegularPages() as $page ) {
			if( $page instanceof Report ) {
				$reports[] = $page;
			} else {
				$normal[] = $page;
			}
		}
		# Normal pages
		$wgOut->addHtml( $this->buildList( $normal, 'spheading', $skin ) );
		# Reports
		$wgOut->addHtml( $this->buildList( $reports, 'specialpages-reports', $skin ) );
		# Restricted pages
		$wgOut->addHtml( $this->buildList( SpecialPage::getRestrictedPages(), 'restrictedpheading', $skin ) );
	}
	
	/**
	 * Build a sorted list of special pages
	 *
	 * @param array $pages Special pages
	 * @param string $heading Heading message key
	 * @param Skin $skin User skin
	 * @return string
	 */
	private function buildList( $pages, $heading, $skin ) {
		if( count( $pages ) > 0 ) {
			foreach( $pages as $page )
				$list[ $page->getDescription() ] = $skin->makeKnownLinkObj( $page->getTitle(),
					htmlspecialchars( $page->getDescription() ) );
			ksort( $list );
			return "<h2>" . wfMsgHtml( $heading ) . "</h2>\n<ul>\n<li>"
				. implode( "</li>\n<li>", array_values( $list ) ) . "</ul>\n";
		} else {
			return '';
		}
	}

}

?>