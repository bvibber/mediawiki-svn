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
	 * Build the action text for a user rights log item
	 *
	 * @param LogItem $item Item to format
	 * @return string
	 */
	public static function formatRights( $item ) {
		global $wgUser, $wgContLang;
		$skin = $wgUser->getSkin();
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