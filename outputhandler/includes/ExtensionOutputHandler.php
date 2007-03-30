<?php

/**
 * Interface for all extension output handlers
 *
 * @package MediaWiki
 * @author Rob Church <robchur@gmail.com>
 */
interface ExtensionOutputHandler {

	/**
	 * Apply changes encapsulated in this handler to the supplied
	 * OutputPage
	 *
	 * @param OutputPage $output
	 */
	public function apply( $output );

}

?>