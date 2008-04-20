<?php

class SiteMatrixMagicI18n {
	static function getMagic( &$magicWords, $lang ) {
		foreach( self::getMagicForLanguage( $lang ) as $word => $trans )
			$magicWords[$word] = $trans;
		return true;
	}

	static function getMagicForLanguage( $lang ) {
		$i18n = self::getI18n();
		if( isset( $i18n[$lang] ) ) 
			return array_merge( $i18n['en'], $i18n[$lang] );
		else
			return $i18n['en'];
	}

	static function getI18n() {
		return array(
			'en' => array(
				'ifsiteexists' => array( 0, 'ifsiteexists' ),
				'siteurl' => array( 0, 'siteurl' ),
			),
		);
	}
}