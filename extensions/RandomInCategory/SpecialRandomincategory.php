<?php

/**
 * Special page to direct the user to a random page in specified category
 *
 * @addtogroup SpecialPage
 * @author VasilievVV <vasilvv@gmail.com>, based on SpecialRandompage.php code
 * @license GNU General Public Licence 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	exit(1);
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Random in category',
	'author' => 'VasilievVV',
	'description' => 'Special page to get a random page in category',
);

$wgExtensionMessagesFiles['RandomInCategory'] = dirname(__FILE__) . '/SpecialRandomincategory.i18n.php';

$wgSpecialPages['Randomincategory'] = 'RandomPageInCategory';
$wgAutoloadClasses['RandomPageInCategory'] = dirname( __FILE__ ) . '/SpecialRandomincategory.body.php';
