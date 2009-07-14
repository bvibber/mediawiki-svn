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
$wgHooks['LanguageGetMagic'][] = 'IndexFunction::addIndexFunction';
# Add to database
$wgHooks['OutputPageParserOutput'][] = 'IndexFunction::doIndexes'; 
# Make links to indexes blue
$wgHooks['LinkEnd'][] = 'IndexFunction::blueLinkIndexes'; 
# Make links to indexes redirect
$wgHooks['InitializeArticleMaybeRedirect'][] = 'IndexFunction::doRedirect';
# Make "go" searches for indexes redirect
$wgHooks['SearchGetNearMatch'][] = 'IndexFunction::redirectSearch';
# Remove things from the index table when a page is deleted
$wgHooks['ArticleDeleteComplete'][] = 'IndexFunction::onDelete';
# Remove things from the index table when creating a new page
$wgHooks['ArticleInsertComplete'][] = 'IndexFunction::onCreate';
# Show a warning when editing an index title
$wgHooks['EditPage::showEditForm:initial'][] = 'IndexFunction::editWarning';

$wgHooks['LoadExtensionSchemaUpdates'][] = 'efIndexUpdateSchema';

# Setup the special page
$wgSpecialPages['Index'] = 'SpecialIndex';
$wgSpecialPageGroups['Index'] = 'maintenance';
$wgExtensionAliasesFiles['IndexFunction'] = $dir . 'IndexFunction.alias.php';
$wgAutoloadClasses['SpecialIndex'] = $dir . 'SpecialIndex.php';

$wgExtensionMessagesFiles['IndexFunction'] = $dir . 'IndexFunction.i18n.php';
$wgAutoloadClasses['IndexFunction'] = $dir . 'IndexFunction_body.php';

function efIndexSetup( &$parser ) {
	$parser->setFunctionHook( 'index-func', array( 'IndexFunction', 'indexRender' ) );
	return true;
}

function efIndexUpdateSchema() {
	global $wgExtNewTables;
	$wgExtNewTables[] = array(
		'indexes',
		dirname( __FILE__ ) . '/indexes.sql' );
	return true;
}

