<?php

/**
 * Book information driver interface
 *
 * A book information driver is a class which handles the work
 * of obtaining information about a given ISBN; see
 * docs/driver-info.htm for more details
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */
interface BookInformationDriver {

	/**
	 * Submit a request to the information source and
	 * store the results in the object's state
	 *
	 * @param string $isbn ISBN to obtain information for
	 * @return bool Success
	 */
	public function submitRequest( $isbn );
	
	public function getTitle();
	
	public function getAuthor();
	
	public function getPublisher();
	
	public function getYear();
	
	public function getPurchaseLink();
	
	public function getProviderLink();
	
}

?>