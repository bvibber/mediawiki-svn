<?php

/*
* 
* It allows the page title to be translated to another language. The page title can be customized into another language, the URL of the page would still be something like .../Special:WikiBhasha, even when the user language is not English.
* 
*/

$specialPageAliases = array();

$specialPageAliases['en'] = array(
	'WikiBhasha' => array( 'WikiBhasha' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;
