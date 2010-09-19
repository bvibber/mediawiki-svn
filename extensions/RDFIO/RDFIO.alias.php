<?php
/**
 * Aliases for special pages
 *
 * @file
 * @ingroup Extensions
 */

$specialPageAliases = array();

/** English */
$specialPageAliases['en'] = array(
	'SPARQLEndpoint'   => array( 'SPARQLEndpoint' ),
	'SpecialARC2Admin' => array( 'ARC2Admin' ),
	'RDFImport'        => array( 'RDFImport' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;
