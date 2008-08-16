<?php
if ( ! defined( 'MEDIAWIKI' ) )
	die();

class DeleteQueue {
	static $rowCache;

	/**
	 * Gets the deletion_queue row for an article.
	 * @param $article Object The article object to get status for.
	 * @return object The row.
	 */
	public static function getRow( $article ) {
		throw new MWException( "This method is deprecated" );
	}

	// Shortcut function for
	public static function __call( $function, $arguments ) {
		$article = array_shift($arguments);

		$obj = new DeleteQueueItem( $article );

		call_user_func_array( array( $obj, $function ), $arguments );
	}
}
