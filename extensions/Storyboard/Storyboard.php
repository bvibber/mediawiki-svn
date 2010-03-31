<?php
/**
 * Initialization file for the Storyboard extension.
 * Extension documentation: http://www.mediawiki.org/wiki/Extension:Storyboard
 *
 * @file Storyboard.php
 * @ingroup Storyboard
 *
 * @author Jeroen De Dauw
 */

/**
 * This documenation group collects source code files belonging to Storyboard.
 *
 * Please do not use this group name for other code.
 *
 * @defgroup Storyboard Storyboard
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define( 'Storyboard_VERSION', '0' );

define( 'Storyboard_TABLE', 'storyboard' );

// TODO: try to get out the hardcoded path.
$egStoryboardScriptPath = $wgScriptPath . '/extensions/Storyboard';
$egStoryboardDir = dirname( __FILE__ ) . '/';
$egStoryboardStyleVersion = $wgStyleVersion . '-' . Storyboard_VERSION;

// Include the settings file.
require_once( $egStoryboardDir . 'Storyboard_Settings.php' );

// Register the initernationalization and aliasing files of Storyboard.
$wgExtensionMessagesFiles['Storyboard'] = $egStoryboardDir . 'Storyboard.i18n.php';
$wgExtensionAliasesFiles['Storyboard'] = $egStoryboardDir . 'Storyboard.alias.php';

// Load classes
$wgAutoloadClasses['StoryboardUtils'] = $egStoryboardDir . 'Storyboard_Utils.php';
$wgAutoloadClasses['SpecialStory'] = $egStoryboardDir . 'specials/Story/Story_body.php';
$wgAutoloadClasses['SpecialStoryReview'] = $egStoryboardDir . 'specials/StoryReview/StoryReview_body.php';
$wgAutoloadClasses['TagStoryboard'] = $egStoryboardDir . 'tags/Storyboard/Storyboard_body.php';
$wgAutoloadClasses['TagStorysubmission'] = $egStoryboardDir . 'tags/Storysubmission/Storysubmission_body.php';

// Load and register the StoryReview special page and register it's group.
$wgSpecialPages['StoryReview'] = 'SpecialStoryReview';
$wgSpecialPageGroups['StoryReview'] = 'contribution';
$wgSpecialPages['Story'] = 'SpecialStory';
$wgSpecialPageGroups['Story'] = 'contribution';

// API
$wgAutoloadClasses['ApiQueryStories'] = "{$egStoryboardDir}api/ApiQueryStories.php";
$wgAPIListModules['stories'] = 'ApiQueryStories';
$wgAutoloadClasses['ApiStoryReview'] = "{$egStoryboardDir}api/ApiStoryReview.php";
$wgAPIModules['storyreview'] = 'ApiStoryReview';

// Hooks
$wgHooks['ParserFirstCallInit'][] = 'efStoryboardParserFirstCallInit';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'efStoryboardSchemaUpdate';
$wgHooks['SkinTemplateTabs'][] = 'efStoryboardAddStoryEditAction';
$wgHooks['SkinTemplateNavigation'][] = 'efStoryboardAddStoryEditActionVector';


/**
 * The 'storyboard' permission key can be given out to users
 * to enable them to review, edit, publish, and hide stories.
 *
 * By default, only sysops will be able to do this.
 */
$wgAvailableRights[] = 'storyreview';
$wgGroupPermissions['sysop']['storyreview'] = true;

$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'Storyboard',
	'version' => Storyboard_VERSION,
	'author' => array( '[http://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]' ),
	'url' => 'http://www.mediawiki.org/wiki/Extension:Storyboard',
	'descriptionmsg' => 'storyboard-desc',
);

function efStoryboardSchemaUpdate() {
	global $wgExtNewTables, $egStoryboardDir;

	$wgExtNewTables[] = array(
		'storyboard',
		$egStoryboardDir . 'storyboard.sql'
	);

	return true;
}

function efStoryboardParserFirstCallInit( &$parser ) {
	$parser->setHook( 'storyboard', array( 'TagStoryboard', 'render' ) );
	$parser->setHook( 'storysubmission', array( 'TagStorysubmission', 'render' ) );
	return true;
}

function efStoryboardAddStoryEditActionVector( &$sktemplate, &$links ) {
	$views_links = $links['views'];
	efStoryboardAddStoryEditAction( $sktemplate, $views_links );
	$links['views'] = $views_links;

	return true;	
}

function efStoryboardAddStoryEditAction( &$sktemplate, &$content_actions ) {
	global $wgRequest, $wgRequest, $wgTitle;

	$action = $wgRequest->getText( 'action' );

	if ( $wgTitle->equals( SpecialPage::getTitleFor( 'story' ) ) ) {
		$content_actions['edit'] = array(
			'class' => $action == 'edit' ? 'selected' : false,
			'text' => wfMsg( 'edit' ),
			'href' => $wgTitle->getLocalUrl( 'action=edit' )
		);
	}

	return true;
}

