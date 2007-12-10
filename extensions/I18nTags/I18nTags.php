<?php

/**
 * Some tags to access i18n function in language files
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Niklas Laxström
 */

if (!defined('MEDIAWIKI')) die();

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'Parser i18n tags',
	'url' => 'http://www.mediawiki.org/wiki/Extension:I18nTags',
	'description' => 'Access the i18n functions for number formatting, ' .
		'grammar and plural in any available language',
	'version' => '2.2',
	'author' => 'Niklas Laxström',
);

$dir = dirname(__FILE__) . '/';
$wgAutoloadClasses['I18nTags'] = $dir . 'I18nTags_body.php';
$wgExtensionFunctions[] = 'efI18nTagsInit';
$wgExtensionMessagesFiles['I18nTags'] = $dir . 'I18nTags.i18n.php';

function efI18nTagsInit() {
	global $wgParser;
	$class = 'I18nTags';
	$wgParser->setHook( 'formatnum', array($class, 'formatNumber')  );
	$wgParser->setHook( 'grammar',   array($class, 'grammar') );
	$wgParser->setHook( 'plural',    array($class, 'plural') );
	$wgParser->setHook( 'linktrail', array($class, 'linktrail') );
	wfLoadExtensionMessages( 'I18nTags' );
	$wgParser->setFunctionHook( 'languagename',  array($class, 'languageName' ) );
}
