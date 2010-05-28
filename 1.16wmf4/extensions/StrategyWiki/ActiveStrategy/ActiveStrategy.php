<?php

/* Registration */
$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'ActiveStrategy',
	'author'         => array( 'Tim Starling', 'Andrew Garrett' ),
	'url'            => 'http://www.mediawiki.org/wiki/Extension:ActiveStrategy',
	'descriptionmsg' => 'active-strategy-desc',
);

$dir = dirname( __FILE__ ) . '/';

$wgHooks['ParserFirstCallInit'][] = 'ActiveStrategyPF::setup';

$wgExtensionMessagesFiles['ActiveStrategy'] = $dir . 'ActiveStrategy.i18n.php';

$wgAutoloadClasses['ActiveStrategy']  = $dir . 'ActiveStrategy_body.php';
$wgAutoloadClasses['ActiveStrategyPF'] = $dir."ParserFunctions.php";

/**
 * Period for edit counts, in seconds
 */
$wgActiveStrategyPeriod = 7 * 86400;
