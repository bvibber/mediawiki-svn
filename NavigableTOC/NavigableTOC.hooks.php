<?php
/**
 * Hooks for Usability Initiative NavigableTOC extension
 *
 * @file
 * @ingroup Extensions
 */

class NavigableTOCHooks {

	/* Static Functions */

	/**
	 * EditPage::showEditForm::initial hook
	 * Adds the TOC to the edit form
	 */
	 public static function addTOC( &$ep ) {
	 	global $wgNavigableTOCStyleVersion, $wgParser, $wgUser;
		global $wgEnableParserCache;

		// Adds script to document
		UsabilityInitiativeHooks::addScript(
			'NavigableTOC/NavigableTOC.js', $wgNavigableTOCStyleVersion
		);

		// Try the parser cache first
		$pcache = ParserCache::singleton();
		$popts = new ParserOptions();
		$articleObj = new Article( $ep->mTitle );
		$p_result = false;
		if ( $wgEnableParserCache ) {
			$p_result = $pcache->get( $articleObj, $wgUser );
			// The ParserOutput in cache could be too old to have
			// byte offsets. In that case, reparse
			$sections = $p_result->getSections();
			if ( isset( $sections[0] ) && !isset( $sections[0]['byteoffset'] ) ) {
				$p_result = $wgParser->parse( $articleObj->getContent(),
					$ep->mTitle, $popts );
				$pcache->save( $p_result, $articleObj, $popts );
			}
		}
		if ( !$p_result ) {
			$p_result = $wgParser->parse( $articleObj->getContent(),
				$ep->mTitle, $popts );
			if ( $wgEnableParserCache )
				$pcache->save( $p_result, $articleObj, $popts );
		}

		$js = "\$.section = '" . Xml::escapeJsString( $ep->section ) . "';";
		$js .= "\$.sectionOffsets = [";
		$targetLevel = false;
		foreach ( $p_result->getSections() as $section )
			if ( !is_null( $section['byteoffset'] ) ) {
				if ( $ep->section != '' ) {
					// Only get offsets for the section
					// being edited and its descendants
					if ( $section['index'] < $ep->section )
						continue;
					else if ( $section['index'] == $ep->section )
						$targetLevel = $section['level'];
					else if ( $section['level'] <= $targetLevel )
						break;
				}
				$js .= intval( $section['byteoffset'] ) . ',';
			}
		$js .= '];';
		$jsTag = Xml::element( 'script', array(), $js );

		$ep->editFormTextTop .= $p_result->getTOCHTML() . $jsTag;
		return true;
	 }
}
