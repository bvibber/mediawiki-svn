<?php
$aliases = array();

/** English */

/** English (English) */
$specialPageAliases['en'] = array(
	'RefHelper' => array( 'RefHelper' ),
);

/** Arabic (العربية) */
$specialPageAliases['ar'] = array(
	'RefHelper' => array( 'مساعد_المراجع' ),
);

/** Japanese (日本語) */
$specialPageAliases['ja'] = array(
	'RefHelper' => array( '注釈ヘルパー' ),
);

/** Luxembourgish (Lëtzebuergesch) */
$specialPageAliases['lb'] = array(
	'RefHelper' => array( 'Assistent_fir_Referenzen' ),
);

/** Malayalam (മലയാളം) */
$specialPageAliases['ml'] = array(
	'RefHelper' => array( 'അവലംബസഹായി' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'RefHelper' => array( 'RefHulp' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;