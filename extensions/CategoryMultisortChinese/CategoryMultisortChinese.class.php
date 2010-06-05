<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die();
}

class CategoryMultisortChineseHooks {

	function __construct() {
		global $wgHooks;
		
		foreach ( array(
			'CategoryMultisortSortkeys',
		) as $hook ) {
			$wgHooks[$hook][] = $this;
		}
	}
	
	function onCategoryMultisortSortkeys( $parser, $category, &$categoryMultisorts ) {
		global $wgContLang;
		
		$data = CategoryMultisortChineseData::getCategoryMultisortChineseData();
		
		$title = $parser->getTitle();
		$text = $title->getText();
		$fc = utf8ToCodepoint( $wgContLang->firstChar( $text ) );
		
		if ( !array_key_exists( 'mandarin', $categoryMultisorts ) ) {
			if ( !array_key_exists( $fc, $data->mandarin ) ) {
				$categoryMultisorts['mandarin'] = '';
			} else {
				$categoryMultisorts['mandarin'] = $data->mandarin[$fc];
			}
		}
		
		if ( !array_key_exists( 'stroke', $categoryMultisorts ) ) {
			if ( !array_key_exists( $fc, $data->totalStrokes ) ) {
				$categoryMultisorts['stroke'] = '';
			} else {
				$categoryMultisorts['stroke'] = sprintf( '%03d', $data->totalStrokes[$fc] );
			}
		}
		
		if ( !array_key_exists( 'radical', $categoryMultisorts ) ) {
			if ( !array_key_exists( $fc, $data->radicalStrokeCounts ) ) {
				$categoryMultisorts['radical'] = '';
			} else {
				list( $radicalId, $rest ) = $data->radicalStrokeCounts[$fc];
				$radicalCp = $data->radicals[$radicalId];
				$categoryMultisorts['radical'] = sprintf( '%s%03d', codepointToUtf8( $radicalCp ), $rest );
			}
		}
		
		return true;
	}
}
