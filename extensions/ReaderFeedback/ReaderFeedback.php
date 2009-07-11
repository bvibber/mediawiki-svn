<?php
/*
 (c) Aaron Schulz 2007-2009 GPL
 
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along
 with this program; if not, write to the Free Software Foundation, Inc.,
 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 http://www.gnu.org/copyleft/gpl.html
*/

if( !defined('MEDIAWIKI') ) {
	echo "ReaderFeedback extension\n";
	exit( 1 );
}

# Number of recent reviews to be a decent sample size
if( !defined('READER_FEEDBACK_SIZE') )
	define('READER_FEEDBACK_SIZE',15);

$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'Reader Feedback',
	'author'         => array( 'Aaron Schulz' ),
	'url'            => 'http://www.mediawiki.org/wiki/Extension:ReaderFeedback',
	'descriptionmsg' => 'readerfeedback-desc',
);

#########
# IMPORTANT:
# When configuring globals, add them to localsettings.php and edit them THERE

# Users that can use the feedback form.
$wgGroupPermissions['*']['feedback'] = true;

# Allow readers to rate pages in these namespaces
$wgFeedbackNamespaces = array();
#$wgFeedbackNamespaces = array( NS_MAIN );
# Reader feedback tags, positive and negative. [a-zA-Z] tag names only.
# Each tag has five levels, which 3 being average. The tag names are
# mapped to their weight. This is used to determine the "worst"/"best" pages.
$wgFeedbackTags = array(
	'reliability'  => 3,
	'completeness' => 2,
	'npov'         => 2,
	'presentation' => 1
);
# How many seconds back should the average rating for a page be based on?
$wgFeedbackAge = 7 * 24 * 3600;
# How long before stats page is updated?
$wgFeedbackStatsAge = 2 * 3600; // 2 hours

# End of configuration variables.
#########

# Bump this number every time you change readerfeedback.css/readerfeedback.js
$wgFeedbackStyleVersion = 60;

$dir = dirname(__FILE__) . '/';
$langDir = $dir . 'language/';

$wgSvgGraphDir = $dir . 'svggraph';
$wgPHPlotDir = $dir . 'phplot-5.0.5';

$wgAutoloadClasses['ReaderFeedback'] = $dir.'ReaderFeedback.class.php';
$wgAutoloadClasses['ReaderFeedbackHooks'] = $dir.'ReaderFeedback.hooks.php';

# Load reader feedback UI
$wgAutoloadClasses['ReaderFeedback'] = $dir . 'specialpages/ReaderFeedback_body.php';

# Page rating history
$wgAutoloadClasses['RatingHistory'] = $dir . 'specialpages/RatingHistory_body.php';
$wgExtensionMessagesFiles['RatingHistory'] = $langDir . 'RatingHistory.i18n.php';

# To list ill-recieved pages
$wgAutoloadClasses['ProblemPages'] = $dir . 'specialpages/ProblemPages_body.php';
$wgExtensionMessagesFiles['ProblemPages'] = $langDir . 'ProblemPages.i18n.php';
$wgSpecialPageGroups['ProblemPages'] = 'quality';
# To list well-recieved pages
$wgAutoloadClasses['LikedPages'] = $dir . 'specialpages/LikedPages_body.php';
$wgExtensionMessagesFiles['LikedPages'] = $langDir . 'LikedPages.i18n.php';
$wgSpecialPageGroups['LikedPages'] = 'quality';

######### Hook attachments #########

# Add review form and visiblity settings link
$wgHooks['SkinAfterContent'][] = 'ReaderFeedbackHooks::onSkinAfterContent';

# Add CSS/JS as needed
$wgHooks['BeforePageDisplay'][] = 'ReaderFeedbackHooks::injectStyleAndJS';

# Duplicate flagged* tables in parserTests.php
$wgHooks['ParserTestTables'][] = 'ReaderFeedbackHooks::onParserTestTables';

# Actually register special pages
$wgHooks['SpecialPage_initList'][] = 'efLoadReaderFeedbackSpecialPages';

#########

/* 
 * Register ReaderFeedback special pages as needed. 
 * Also sets $wgSpecialPages just to be consistent.
 */
function efLoadReaderFeedbackSpecialPages( &$list ) {
	global $wgSpecialPages, $wgFeedbackNamespaces;
	if( !empty($wgFeedbackNamespaces) ) {
		$list['ReaderFeedback'] = $wgSpecialPages['ReaderFeedback'] = 'ReaderFeedback';
		$list['RatingHistory'] = $wgSpecialPages['RatingHistory'] = 'RatingHistory';
		$list['ProblemPages'] = $wgSpecialPages['ProblemPages'] = 'ProblemPages';
		$list['LikedPages'] = $wgSpecialPages['LikedPages'] = 'LikedPages';
	}
	return true;
}

# AJAX functions
$wgAjaxExportList[] = 'ReaderFeedback::AjaxReview';

# Schema changes
$wgHooks['LoadExtensionSchemaUpdates'][] = 'efReaderFeedbackSchemaUpdates';

function efReaderFeedbackSchemaUpdates() {
	global $wgDBtype, $wgExtNewFields, $wgExtPGNewFields, $wgExtNewIndexes, $wgExtNewTables;
	$base = dirname(__FILE__);
	return true;
}
