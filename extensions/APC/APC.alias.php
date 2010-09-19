<?php
/**
 * Aliases for special pages
 *
 * @file
 * @ingroup Extensions
 */

/** English (English) */
$specialPageAliases['en'] = array(
	'APC' => array( 'APC' ),
);

/** Arabic (العربية) */
$specialPageAliases['ar'] = array(
	'APC' => array( 'إيه_بي_سي', 'عرض_إيه_بي_سي' ),
);

/** Egyptian Spoken Arabic (مصرى) */
$specialPageAliases['arz'] = array(
	'APC' => array( 'عرض_إيه_بى_سى' ),
);

/** Sanskrit (संस्कृत) */
$specialPageAliases['sa'] = array(
	'APC' => array( 'एपीसिपश्यति' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;