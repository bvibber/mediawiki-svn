<?php

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'IndexFunction',
	'author' =>'Alex Zaddach', 
	'url' => 'http://www.mediawiki.org/wiki/Extension:IndexFunction',  
	'descriptionmsg' => 'indexfunc-desc',
	'description' => 'Parser function to create automatic redirects and disambiguation pages'
);

$dir = dirname(__FILE__) . '/';

# Register function 
$wgHooks['ParserFirstCallInit'][] = 'efIndexSetup';
$wgHooks['LanguageGetMagic'][] = 'IndexFunctionHooks::addIndexFunction';
# Add to database
$wgHooks['OutputPageParserOutput'][] = 'IndexFunctionHooks::doIndexes'; 
# Make links to indexes blue
$wgHooks['LinkEnd'][] = 'IndexFunctionHooks::blueLinkIndexes'; 
# Make links to indexes redirect
$wgHooks['InitializeArticleMaybeRedirect'][] = 'IndexFunctionHooks::doRedirect';
# Make "go" searches for indexes redirect
$wgHooks['SearchGetNearMatch'][] = 'IndexFunctionHooks::redirectSearch';
# Remove things from the index table when a page is deleted
$wgHooks['ArticleDeleteComplete'][] = 'IndexFunctionHooks::onDelete';
# Remove things from the index table when creating a new page
$wgHooks['ArticleInsertComplete'][] = 'IndexFunctionHooks::onCreate';
# Show a warning when editing an index title
$wgHooks['EditPage::showEditForm:initial'][] = 'IndexFunctionHooks::editWarning';
# Show a warning after page move, and do some cleanup
$wgHooks['SpecialMovepageAfterMove'][] = 'IndexFunctionHooks::afterMove';

$wgHooks['LoadExtensionSchemaUpdates'][] = 'efIndexUpdateSchema';

# Setup the special page
$wgSpecialPages['Index'] = 'SpecialIndex';
$wgSpecialPageGroups['Index'] = 'pages';
$wgExtensionAliasesFiles['IndexFunction'] = $dir . 'IndexFunction.alias.php';
$wgAutoloadClasses['SpecialIndex'] = $dir . 'SpecialIndex.php';

$wgExtensionMessagesFiles['IndexFunction'] = $dir . 'IndexFunction.i18n.php';
$wgAutoloadClasses['IndexFunctionHooks'] = $dir . 'IndexFunction_body.php';
$wgAutoloadClasses['IndexFunction'] = $dir . 'IndexFunction_body.php';
$wgAutoloadClasses['IndexAbstracts'] = $dir . 'IndexAbstracts.php';

/*
 * Used to set the context given on Special:Index auto-disambig pages
 * Can be 1 of 2 options:
 * 'extract' (default) - Show an extract from the start of the article
 * 'categories' - Show a comma-separated list of categories the article is in
*/
$wgSpecialIndexContext = 'extract';

function efIndexSetup( &$parser ) {
	$parser->setFunctionHook( 'index-func', array( 'IndexFunctionHooks', 'indexRender' ) );
	return true;
}

function efIndexUpdateSchema() {
	global $wgExtNewTables;
	$wgExtNewTables[] = array(
		'indexes',
		dirname( __FILE__ ) . '/indexes.sql' );
	return true;
}

