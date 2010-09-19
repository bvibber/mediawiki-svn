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
	'CategoryBrowser' => array( 'CategoryBrowser' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'CategoryBrowser' => array( 'CategorieenDoorbladeren' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;