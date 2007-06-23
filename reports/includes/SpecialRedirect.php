<?php

/**
 * Special page which redirects the user to a
 * different special page, appending a set
 * request string
 *
 * @addtogroup SpecialPage
 * @author Rob Church <robchur@gmail.com>
 */
class SpecialRedirect extends UnlistedSpecialPage {

	/**
	 * Target
	 */
	private $target = null;

	/**
	 * Request string components
	 */
	private $request = array();
	
	/**
	 * Constructor
	 *
	 * @param string $name
	 * @param array $request
	 */
	public function __construct( $target, $request ) {
		parent::__construct( $target );
		$this->target = SpecialPage::getTitleFor( $target );
		$this->request = $request;
	}
	
	/**
	 * Main execution function
	 */
	public function execute( $par = false ) {
		global $wgOut;
		$q = array();
		foreach( $this->request as $name => $value )
			$q[] = $name . '=' . $value;
		$q = implode( '&', $q );
		$wgOut->redirect( $this->target->getFullUrl( $q ) );
	}

}

?>