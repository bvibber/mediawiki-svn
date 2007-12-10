<?php

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
	echo "ListChangeArticles extension";
	exit(1);
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'List Changed Articles',
	'version' => '1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:List_Changed_Articles',
	'author' => 'Tim Starling',
	'description' => 'Adds [[Special:ListChangedArticles]]',
);

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/ListChangedArticles_body.php', 'ListChangedArticles', 'ListChangedArticles' );
