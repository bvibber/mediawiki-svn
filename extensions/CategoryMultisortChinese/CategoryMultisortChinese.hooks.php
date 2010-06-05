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
	
	function onCategoryMultisortSortkeys_loadData() {
		$file = dirname( __FILE__ ) . '/CategoryMultisortChinese.dat';
		
		if ( file_exists( $file ) ) {
			$data = unserialize( file_get_contents( $file ) );
			if ( $data ) {
				return $data;
			}
		}
		
		$data = new CategoryMultisortChineseData();
		file_put_contents( $file, serialize( $data ) );
		return $data;
	}
	
	function onCategoryMultisortSortkeys( $parser, $category, &$categoryMultisorts ) {
		global $wgContLang;
		
		static $data = null;
		if ( is_null( $data ) ) {
			$data = $this->onCategoryMultisortSortkeys_loadData();
		}
		
		$title = $parser->getTitle();
		$text = $title->getText();
		$fc = utf8ToCodepoint( $wgContLang->firstChar( $text ) );
		
		$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'mandarin',
			array_key_exists( $fc, $data->mandarin ) ? $data->mandarin[$fc] : ''
		);
		$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'stroke',
			$this->onCategoryMultisortSortkeys_getStroke( $data, $fc )
		);
		$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'radical',
			$this->onCategoryMultisortSortkeys_getRadical( $data, $fc )
		);
		
		$conv = $wgContLang->autoConvertToAllVariants( $text );
		
		if ( array_key_exists( 'zh-hans', $conv ) ) {
			$fc = utf8ToCodepoint( $wgContLang->firstChar( $conv['zh-hans'] ) );
			$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'stroke-s',
				$this->onCategoryMultisortSortkeys_getStroke( $data, $fc )
			);
			$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'radical-s',
				$this->onCategoryMultisortSortkeys_getRadical( $data, $fc )
			);
		}
		
		if ( array_key_exists( 'zh-hant', $conv ) ) {
			$fc = utf8ToCodepoint( $wgContLang->firstChar( $conv['zh-hant'] ) );
			$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'stroke-t',
				$this->onCategoryMultisortSortkeys_getStroke( $data, $fc )
			);
			$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'radical-t',
				$this->onCategoryMultisortSortkeys_getRadical( $data, $fc )
			);
		}
		
		return true;
	}
	
	function onCategoryMultisortSortkeys_setDefaultSortkey( &$categoryMultisorts, $skn, $default ) {
		if ( !array_key_exists( $skn, $categoryMultisorts ) ) {
			$categoryMultisorts[$skn] = $default;
		}
	}
	
	function onCategoryMultisortSortkeys_getStroke( $data, $chcp ) {
		return array_key_exists( $chcp, $data->totalStrokes )
			? sprintf( '%03d', $data->totalStrokes[$chcp] ) : '';
	}
	
	function onCategoryMultisortSortkeys_getRadical( $data, $chcp ) {
		if ( !array_key_exists( $chcp, $data->radicalStrokeCounts ) ) {
			return '';
		} else {
			list( $radicalId, $rest ) = $data->radicalStrokeCounts[$chcp];
			$radicalCp = $data->radicals[$radicalId];
			return sprintf( '%s%03d', codepointToUtf8( $radicalCp ), $rest );
		}
	}
}
