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
	 * EditPage::showEditForm:initial hook
	 * Adds the TOC to the edit form
	 */
	 public static function addTOC( &$ep ) {
		global $wgNavigableTOCStyleVersion, $wgParser, $wgUser;
		global $wgEnableParserCache;

		// Adds script to document
		UsabilityInitiativeHooks::initialize();
		UsabilityInitiativeHooks::addScript(
			'NavigableTOC/NavigableTOC.js', $wgNavigableTOCStyleVersion
		);
		UsabilityInitiativeHooks::addStyle(
			'NavigableTOC/NavigableTOC.css', $wgNavigableTOCStyleVersion
		);

		// Try the parser cache first
		$pcache = ParserCache::singleton();
		$popts = ParserOptions::newFromUser( $wgUser );
		$popts->setTidy( true );
		$popts->enableLimitReport();
		$articleObj = new Article( $ep->mTitle );
		$p_result = $p_result2 = false;
		if ( $ep->preview ) {
			$p_result = $ep->mParserOutput;
			if ( $ep->section != '' ) {
				// Store this result and make sure the
				// ParserOutput for the entire page is
				// grabbed as well
				$p_result2 = $p_result;
				$p_result = false;
			}
		}
		else if ( $wgEnableParserCache ) {
			$p_result = $pcache->get( $articleObj, $popts );
			// The ParserOutput in cache could be too old to have
			// byte offsets. In that case, reparse
			if ( $p_result ) {
				$sections = $p_result->getSections();
				if ( isset( $sections[0] ) && !isset( $sections[0]['byteoffset'] ) ) {
					$p_result = $wgParser->parse( $articleObj->getContent(),
						$ep->mTitle, $popts );
					if ( $p_result )
						$pcache->save( $p_result, $articleObj, $popts );
				}
			}
		}
		if ( !$p_result ) {
			$popts->setIsPreview( $ep->preview || $ep->section != '' );
			$p_result = $wgParser->parse( $articleObj->getContent(),
				$ep->mTitle, $popts );
			if ( $wgEnableParserCache )
				$pcache->save( $p_result, $articleObj,
					$popts );
		}
		
		if( $p_result2 ) {
			// Merge the section trees of the original article and
			// the edited text; this saves us from parsing the
			// entire page with the edited section replaced
			$sectionTree = Parser::mergeSectionTrees(
				$p_result->getSections(),
				$p_result2->getSections(),
				$ep->section, $ep->mTitle, strlen( $ep->textbox1 ) );
			$toc = $wgUser->getSkin()->generateTOC( $sectionTree );
		} else {
			$sectionTree = $p_result->getSections();
			$toc = $p_result->getTOCHTML();
		}

		$js = "\$.section = '" . Xml::escapeJsString( $ep->section ) . "';";
		$js .= "\$.sectionOffsets = [";
		$targetLevel = false;
		$targetSection = false;
		foreach ( $sectionTree as $section )
			if ( !is_null( $section['byteoffset'] ) ) {
				if ( $ep->section != '' ) {
					// Only get offsets for the section
					// being edited and its descendants.
					// In preview mode, sibling sections
					// may have been added, so use the
					// number of sections in $p_result2
					if ( $section['index'] < $ep->section )
						continue;
					else if ( $section['index'] == $ep->section ) {
						if ( $ep->preview )
							$targetSection = $ep->section +
								count( $p_result2->getSections() );
						else
							$targetLevel = $section['level'];
					}
					else if ( ( $ep->preview && $section['index'] >= $targetSection ) ||
							( !$ep->preview && $section['level'] <= $targetLevel ) )
						break;
				}
				$js .= intval( $section['byteoffset'] ) . ',';
			}
		$js .= '];';
		$jsTag = Xml::element( 'script', array(), $js );

		// Terrible hack to prevent two TOCs with the same ID
		// from being displayed
		$toc = str_replace( '<table id="toc"',
			'<table id="navigableTOC"', $toc );
		$ep->editFormTextTop .= $toc . $jsTag;
		return true;
	}
}
