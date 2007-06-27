<?php

/**
 * Manages formatting functions for individual logs
 *
 * @addtogroup Logging
 * @author Rob Church <robchur@gmail.com>
 */
class LogFormatter {

	/**
	 * Formatting flags
	 */
	const NO_DATE = 1;

	/**
	 * Skin to use for building UI elements
	 */
	protected static $skin = null;

	/**
	 * Format a log item, returning a string containing a
	 * complete list element
	 *
	 * @param LogItem $item
	 * @param int $flags
	 * @return string
	 */
	public static function format( $item, $flags ) {
		return call_user_func(
			self::getFormatter( $item ),
			$item,
			$flags
		);
	}
	
	/**
	 * Default formatter; all the standard bits, using custom
	 * action formatter if set
	 *
	 * @param LogItem $item
	 * @param int $flags
	 * @return string
	 */
	protected static function formatDefault( $item, $flags ) {
		global $wgUser, $wgLang, $wgLogActions;
		$skin = $wgUser->getSkin();
		
		# Time
		$parts[] = $flags & self::NO_DATE
			? $wgLang->time( $item->getTimestamp() )
			: $wgLang->timeAndDate( $item->getTimestamp() );
		# User
		$parts[] = $skin->userLink( $item->getUser()->getId(), $item->getUser()->getName() )
			. $skin->userToolLinks( $item->getUser()->getId(), $item->getUser()->getName() );
		# Action
		if( ( $callback = self::getActionCallback( $item ) ) !== false ) {
			# Custom action text callback
			$parts[] = call_user_func( $callback, $item );
		} else {
			# Use the message
			$params = $item->getParameters();
			array_unshift( $params, $skin->makeLinkObj( $item->getTarget() ) );
			$parts[] = self::getActionText( $item, $params );
		}
		# Comment
		$parts[] = $skin->commentBlock( $item->getComment() );
		
		return "<li>" . implode( ' ', $parts ) . "</li>\n";
	}
	
	/**
	 * Get the action text for a particular log item
	 *
	 * @param LogItem $item
	 * @param array $params
	 * @return string
	 */
	public static function getActionText( $item, $params ) {
		global $wgLogActions;
		return wfMsgReal( $wgLogActions[ $item->getActionKey() ], $params );
	}
	
	/**
	 * Get the LogFormatter::format()-compliant callback
	 * to use to format a specified log item
	 *
	 * @param LogItem $item
	 * @return callback
	 */
	private static function getFormatter( $item ) {
		global $wgLogFormatters;
		return isset( $wgLogFormatters[ $item->getType() ] )
			? $wgLogFormatters[ $item->getType() ]
			: array( __CLASS__, 'formatDefault' );
	}
	
	/**
	 * Get the callback to build action text for the
	 * specified log item, if there is one
	 *
	 * @param LogItem $item
	 * @return mixed
	 */
	private static function getActionCallback( $item ) {
		global $wgLogActionCallbacks;
		return isset( $wgLogActionCallbacks[ $item->getType() ] )
			? $wgLogActionCallbacks[ $item->getType() ]
			: false;
	}
	
}

?>
