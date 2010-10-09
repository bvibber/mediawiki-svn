<?php
/**
 * Aliases for special pages
 *
 * @file
 * @ingroup Extensions
 */

$specialPageAliases = array();

/** English (English) */
$specialPageAliases['en'] = array(
	'SpecialWikiAtHome' => array( 'WikiAtHome' ),
);

/** Arabic (العربية) */
$specialPageAliases['ar'] = array(
	'SpecialWikiAtHome' => array( 'الويكي_في_المنزل' ),
);

/** Egyptian Spoken Arabic (مصرى) */
$specialPageAliases['arz'] = array(
	'SpecialWikiAtHome' => array( 'الويكى_فى_البيت' ),
);

/** Azerbaijani (Azərbaycanca) */
$specialPageAliases['az'] = array(
	'SpecialWikiAtHome' => array( 'VikiEvdə' ),
);

/** Bosnian (Bosanski) */
$specialPageAliases['bs'] = array(
	'SpecialWikiAtHome' => array( 'WikiKodKuce' ),
);

/** Korean (한국어) */
$specialPageAliases['ko'] = array(
	'SpecialWikiAtHome' => array( '위키 홈' ),
);

/** Luxembourgish (Lëtzebuergesch) */
$specialPageAliases['lb'] = array(
	'SpecialWikiAtHome' => array( 'WikiDoheem' ),
);

/** Malayalam (മലയാളം) */
$specialPageAliases['ml'] = array(
	'SpecialWikiAtHome' => array( 'വീട്ടിലെ വിക്കി' ),
);

/** Marathi (मराठी) */
$specialPageAliases['mr'] = array(
	'SpecialWikiAtHome' => array( 'घरचाविकि' ),
);

/** Nedersaksisch (Nedersaksisch) */
$specialPageAliases['nds-nl'] = array(
	'SpecialWikiAtHome' => array( 'Wiki_thuus' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'SpecialWikiAtHome' => array( 'WikiThuis' ),
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬) */
$specialPageAliases['no'] = array(
	'SpecialWikiAtHome' => array( 'Hjemmewiki' ),
);

/** Turkish (Türkçe) */
$specialPageAliases['tr'] = array(
	'SpecialWikiAtHome' => array( 'EvdeViki' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;