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
	
	/**
	 * Get the title
	 *
	 * @return mixed Title string or false if unavailable
	 */
	public function getTitle();
	
	/**
	 * Get the author
	 *
	 * @return mixed Author string or false if unavailable
	 */
	public function getAuthor();
	
	/**
	 * Get the publisher
	 *
	 * @return mixed Publisher string or false if unavailable
	 */
	public function getPublisher();
	
	/**
	 * Get the year
	 *
	 * @return mixed Year string or false if unavailable
	 */
	public function getYear();
	
	/**
	 * Get a link allow users to purchase the item
	 *
	 * @return mixed Purchase link or false if not applicable
	 */
	public function getPurchaseLink();
	
	/**
	 * Get a link to the provider's information page
	 *
	 * @return string Provider link
	 */
	public function getProviderLink();
	
}

?>
