<?php

/**
 * Log formatting functions for all built-in logs
 * which need them
 *
 * @addtogroup Logging
 * @author Rob Church <robchur@gmail.com>
 */
class CoreLogFormatter {

	/**
	 * Build the action text for a move log item
	 *
	 * @param LogItem $item Log item to format
	 * @param Skin $skin Skin to use for link building, etc.
	 * @return string
	 */
	public static function formatMove( $item, $skin ) {
		$data = $item->getParameters();
		$params[] = $skin->makeLinkObj( $item->getTarget(), '', 'redirect=no' );
		$params[] = $skin->makeLinkObj( Title::newFromText( $data[0] ) );
		return LogFormatter::getActionText( $item, $params );		
	}

	/**
	 * Build the action text for a user rights log item
	 *
	 * @param LogItem $item Log item to format
	 * @param Skin $skin Skin to use for link building, etc.
	 * @return string
	 */
	public static function formatRights( $item, $skin ) {
		global $wgContLang;
		$target = $wgContLang->ucfirst( $item->getTarget()->getText() );
		$data = $item->getParameters();
		
		$params[] = $skin->userLink( 1, $target )
			. $skin->userToolLinks( 1, $target );
		$params[] = ( isset( $data[0] ) && trim( $data[0] ) !== '' )
			? $data[0]
			: wfMsg( 'rightsnone' );
		$params[] = ( isset( $data[1] ) && trim( $data[1] ) !== '' )
			? $data[1]
			: wfMsg( 'rightsnone' );
		
		return LogFormatter::getActionText( $item, $params );
	}

}

?>