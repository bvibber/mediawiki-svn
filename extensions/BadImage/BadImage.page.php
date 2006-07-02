<?php

/**
 * Class provides a special page to manage the bad image list
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence Copyright holder allows use of the code for any purpose
 */

class BadImageManipulator extends SpecialPage {

	function __construct() {
		parent::__construct( 'Badimages' );
	}
	
	function execute() {
		global $wgOut;
		$this->setHeaders();
		$wgOut->addWikiText( "'''Boo!'''" );
	}

}

?>