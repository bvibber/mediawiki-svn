<?php

/**
 * Formatting functions for the block log
 *
 * @addtogroup Logging
 * @author Rob Church <robchur@gmail.com>
 */
class BlockLogFormatter {

	/**
	 * Tool link constants
	 */
	const LINK_BLOCK = 1;
	const LINK_UNBLOCK = 2;

	/**
	 * Format a complete block log item
	 *
	 * @param LogItem $item
	 * @param int $flags
	 * @return string
	 */
	public static function formatBlock( $item, $flags ) {
		global $wgUser, $wgLang, $wgLogActions;
		$skin = $wgUser->getSkin();
		$data = $item->getParameters();
		
		$parts[] = $flags & LogFormatter::NO_DATE
			? $wgLang->time( $item->getTimestamp() )
			: $wgLang->timeAndDate( $item->getTimestamp() );
		$parts[] = $skin->userLink( $item->getUser()->getId(), $item->getUser()->getName() )
			. $skin->userToolLinks( $item->getUser()->getId(), $item->getUser()->getName() );
		
		# Target link
		$link = $item->getAction() == 'block' ? self::LINK_BLOCK : self::LINK_UNBLOCK;
		$params[] = self::formatTarget( $item->getTarget(), $link );
		 
		# Format block flags, etc. if applicable
		if( $item->getAction() == 'block' ) {
			$params[] = $wgLang->translateBlockExpiry( $data[0] );
			$params[] = ( isset( $data[1] ) ? self::formatBlockFlags( $data[1] ) : '' );
		}
		
		$parts[] = wfMsgReal( $wgLogActions[ $item->getActionKey() ], $params );
		return "<li>" . implode( ' ', $parts ) . "</li>\n";
	}
	
	/**
	 * Format the block target; link to the user page, standard tool
	 * links and an unblock link (if required)
	 *
	 * @param Title $target
	 * @param int $link
	 * @return string
	 */
	private static function formatTarget( $target, $link ) {
		global $wgUser;
		$skin = $wgUser->getSkin();
		
		$tools[] = $skin->makeLinkObj( $target->getTalkPage(), wfMsgHtml( 'talkpagelinktext' ) );
		$tools[] = $skin->makeKnownLinkObj(
			SpecialPage::getTitleFor( 'Contributions', $target->getText() ), wfMsgHtml( 'contribslink' ) );

		if( $wgUser->isAllowed( 'block' ) ) {
			if( $link == self::LINK_BLOCK ) {
				$tools[] = $skin->makeKnownLinkObj(
					SpecialPage::getTitleFor( 'Blockip', $target->getText() ), wfMsgHtml( 'blocklink' ) );
			} else {
				$tools[] = $skin->makeKnownLinkObj(
					SpecialPage::getTitleFor( 'Ipblocklist', wfMsgHtml( 'unblocklink' ),
					'action=unblock&ip=' . $target->getPartialUrl() ) );
			}
		}
		
		return $skin->makeLinkObj( $target, htmlspecialchars( $target->getText() ) )
			. ' (' . implode( ' | ', $tools ) . ')';
	}

	/**
	 * Convert a comma-delimited list of block log flags
	 * into a more readable (and translated) form
	 *
	 * @param $flags Flags to format
	 * @return string
	 */
	private static function formatBlockFlags( $flags ) {
		$flags = explode( ',', trim( $flags ) );
		if( count( $flags ) > 0 ) {
			for( $i = 0; $i < count( $flags ); $i++ )
				$flags[$i] = self::formatBlockFlag( $flags[$i] );
			return '(' . implode( ', ', $flags ) . ')';
		} else {
			return '';
		}
	}
	
	/**
	 * Translate a block log flag if possible
	 *
	 * @param $flag Flag to translate
	 * @return string
	 */
	private static function formatBlockFlag( $flag ) {
		static $messages = array();
		if( !isset( $messages[$flag] ) ) {
			$k = 'block-log-flags-' . $flag;
			$msg = wfMsg( $k );
			$messages[$flag] = htmlspecialchars( wfEmptyMsg( $k, $msg ) ? $flag : $msg );
		}
		return $messages[$flag];
	}

}

?>