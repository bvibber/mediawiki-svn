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
	 public static function addTOC(&$ep) {
	 	global $wgNavigableTOCStyleVersion, $wgParser, $wgUser;
		global $wgUseParserCache;

		// Adds script to document
		UsabilityInitiativeHooks::addScript(
			'NavigableTOC/NavigableTOC.js', $wgNavigableTOCStyleVersion
		);
		
		// Try the parser cache first
		$pcache = ParserCache::singleton();
		$articleObj = new Article( $ep->mTitle );
		$p_result = $pcache->get( $articleObj, $wgUser );
		if ( !$p_result )
		{
			$p_result = $wgParser->parse( $articleObj->getContent(), $ep->mTitle, new ParserOptions());
			if( $wgUseParserCache )
				$pcache->save( $p_result, $articleObj, $popts );
		} else {
			// The ParserOutput in cache could be too old to have
			// byte offsets. In that case, reparse
			$sections = $p_result->getSections();
			if ( isset( $sections[0] ) && !isset( $sections[0]['byteoffset'] ) ) {
				$p_result = $wgParser->parse( $articleObj->getContent(), $ep->mTitle, new ParserOptions());
				if( $wgUseParserCache )
					$pcache->save( $p_result, $articleObj, $popts );
			}
		}
		
		$js = "\$.sectionOffsets = [";
		foreach ( $p_result->getSections() as $section )
			if ( !is_null( $section['byteoffset'] ) )
				$js .= intval( $section['byteoffset'] ) . ',';
		$js .= '];';
		$jsTag = Xml::element( 'script', array(), $js );
		
		$ep->editFormTextTop .= $p_result->getTOCHTML() . $jsTag;
		return true;
	 }
}
