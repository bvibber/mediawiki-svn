<?php
/* vim: noet ts=4 sw=4
 * http://www.mediawiki.org/wiki/Extension:Uniwiki_Auto_Create_Category_Pages
 * http://www.gnu.org/licenses/gpl-3.0.txt */

if ( !defined( 'MEDIAWIKI' ) )
	die();

/* ---- CREDITS ---- */
$wgExtensionCredits['other'][] = array(
	'name'           => 'AutoCreateCategoryPages',
	'author'         => 'Merrick Schaefer, Mark Johnston, Evan Wheeler and Adam Mckaig (at UNICEF)',
	'description'    => 'Create stub Category pages automatically',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:Uniwiki_Auto_Create_Category_Pages',
	'svn-date'       => '$LastChangedDate$',
	'svn-revision'   => '$LastChangedRevision$',
	'descriptionmsg' => 'autocreatecategorypages-desc',
);

$wgExtensionMessagesFiles['AutoCreateCategoryPages'] = dirname( __FILE__ ) . '/AutoCreateCategoryPages.i18n.php';

/* ---- HOOKS ---- */
$wgHooks['ArticleSaveComplete'][] = "UW_AutoCreateCategoryPages_Save";

function UW_AutoCreateCategoryPages_Save ( &$article, &$user, &$text, &$summary, &$minoredit,
	&$watchthis, &$sectionanchor, &$flags, $revision ) {
	global $wgDBprefix;

	/* after the page is saved, get all the categories
	 * and see if they exists as "proper" pages; if not
	 * then create a simple page for them automatically */

	// Extract the categories on this page
	//
	// FIXME: this obviously only works for the English namespaces
	//
	$regex = "/\[\[category:(.+?)(?:\|.*)?\]\]/i";
	preg_match_all ( $regex, $text, $matches );

	// array of the categories on the page (in db form)
	$on_page = array();
	foreach ( $matches[1] as $cat )
		$on_page[] = Title::newFromText ( $cat )->getDBkey();

	// array of the categories in the db
	$db = wfGetDB ( DB_MASTER );
	$results = $db->resultObject ( $db->query(
		"select distinct page_title from {$wgDBprefix}page " .
		"where page_namespace = '" . NS_CATEGORY . "'" ) );

	$in_db = array();
	while ( $r = $results->next() )
		$in_db[] = $r->page_title;

	/* loop through the categories in the page and
	 * see if they already exist as a category page */
	foreach ( $on_page as $db_key ) {
		if ( !in_array( $db_key, $in_db ) ) {

			wfLoadExtensionMessages( 'AutoCreateCategoryPages' );

			// if it doesn't exist, then create it here
			$page_title = Title::newFromDBkey ( $db_key )->getText();
			$stub = wfMsgForContent ( 'autocreatecategorypages-stub', $page_title );
			$summary = wfMsgForContent ( 'autocreatecategorypages-createdby' );
			$article = new Article ( Title::newFromDBkey( "Category:$db_key" ) );

			try {
				$article->doEdit ( $stub, $summary, EDIT_NEW & EDIT_SUPPRESS_RC );

			} catch ( MWException $e ) {
				/* fail silently...
				 * todo: what can go wrong here? */
			}
		}
	}

	return true;
}
