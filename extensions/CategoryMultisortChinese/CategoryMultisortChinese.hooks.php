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
		
		$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'mandarin-pinyin',
			$this->onCategoryMultisortSortkeys_buildMandarinPinyinSortkey( $data, $text )
		);
		$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'cantonese-jyutping',
			$this->onCategoryMultisortSortkeys_buildCantoneseJyutpingSortkey( $data, $text )
		);
		$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'stroke',
			$this->onCategoryMultisortSortkeys_buildStrokeSortkey( $data, $text )
		);
		$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'radical',
			$this->onCategoryMultisortSortkeys_buildRadicalSortkey( $data, $text )
		);
		
		$conv = $wgContLang->autoConvertToAllVariants( $text );
		
		if ( array_key_exists( 'zh-hans', $conv ) ) {
			$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'stroke-s',
				$this->onCategoryMultisortSortkeys_buildStrokeSortkey( $data, $conv['zh-hans'] )
			);
			$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'radical-s',
				$this->onCategoryMultisortSortkeys_buildRadicalSortkey( $data, $conv['zh-hans'] )
			);
		}
		
		if ( array_key_exists( 'zh-hant', $conv ) ) {
			$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'stroke-t',
				$this->onCategoryMultisortSortkeys_buildStrokeSortkey( $data, $conv['zh-hant'] )
			);
			$this->onCategoryMultisortSortkeys_setDefaultSortkey( $categoryMultisorts, 'radical-t',
				$this->onCategoryMultisortSortkeys_buildRadicalSortkey( $data, $conv['zh-hant'] )
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
	
	function onCategoryMultisortSortkeys_splitString( $str ) {
		global $wgContLang;
		
		$result = array();
		while ( $str ) {
			$fc = $wgContLang->firstChar( $str );
			$result[] = $fc;
			$str = substr( $str, strlen( $fc ) );
		}
		return $result;
	}
	
	function onCategoryMultisortSortkeys_buildMandarinPinyinSortkey( $data, $str ) {
		$result = '';
		foreach ( $this->onCategoryMultisortSortkeys_splitString( $str ) as $ch ) {
			# One UTF-8 character can have 4 bytes max.
			$c = str_pad( $ch, 4 );
			$chcp = utf8ToCodepoint( $ch );
			# One Mandarin entry can have 7 bytes max.
			$md = str_pad( array_key_exists( $chcp, $data->mandarin ) ? $data->mandarin[$chcp] : '', 7 );
			$result .= $md . $c;
		}
		return $result;
	}
	
	function onCategoryMultisortSortkeys_buildCantoneseJyutpingSortkey( $data, $str ) {
		$result = '';
		foreach ( $this->onCategoryMultisortSortkeys_splitString( $str ) as $ch ) {
			# One UTF-8 character can have 4 bytes max.
			$c = str_pad( $ch, 4 );
			$chcp = utf8ToCodepoint( $ch );
			# One Cantonese entry can have 7 bytes max ([a-z]{1,6}[1-6]).
			$md = str_pad( array_key_exists( $chcp, $data->cantonese ) ? $data->cantonese[$chcp] : '', 7 );
			$result .= $md . $c;
		}
		return $result;
	}
	
	function onCategoryMultisortSortkeys_buildStrokeSortkey( $data, $str ) {
		$result = '';
		foreach ( $this->onCategoryMultisortSortkeys_splitString( $str ) as $ch ) {
			# One UTF-8 character can have 4 bytes max.
			$c = str_pad( $ch, 4 );
			$chcp = utf8ToCodepoint( $ch );
			# One stroke entry always has 3 bytes, or blank if unavailable.
			$s = str_pad( $this->onCategoryMultisortSortkeys_getStroke( $data, $chcp ), 3 );
			$result .= $s . $c;
		}
		return $result;
	}
	
	function onCategoryMultisortSortkeys_buildRadicalSortkey( $data, $str ) {
		$result = '';
		foreach ( $this->onCategoryMultisortSortkeys_splitString( $str ) as $ch ) {
			# One UTF-8 character can have 4 bytes max.
			$c = str_pad( $ch, 4 );
			$chcp = utf8ToCodepoint( $ch );
			# One radical-stroke entry always has 3 (radical) + 3 (stroke) = 6 bytes, or blank if unavailable.
			$r = str_pad( $this->onCategoryMultisortSortkeys_getRadical( $data, $chcp ), 6 );
			$result .= $r . $c;
		}
		return $result;
	}
}
