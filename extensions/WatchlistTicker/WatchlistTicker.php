<?php

/**
 * An evil extension that provides a ticker-like watchlist in a
 * sidebar on every page.
 *
 * @addtogroup Extensions
 * @author Leon Weber <leon@vserver152.masterssystems.com>
 * @copyright © 2007 by Leon Weber
 * @licence GNU General Public Licence 2.0 or later
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die();
}

/**
 * Register extension setup hook and credits:
 */
$wgExtensionCredits['parserhook'][] = array(
	'name'		=> 'WatchlistTicker',
	'author'	=> 'Leon Weber',
	'url'		=> 'http://www.mediawiki.org/wiki/Extension:WatchlistTicker',
	'description'	=> 'Adds a ticker-like watchlist in a sidebar on every page.'
);
$wgExtensionFunctions[] = 'efWatchlistTicker';

/**
 * Setup hooks.
 *
 * Unfortunately, we can't do the anon check here,
 * as $wgUser is just a stub object at this point.
 * So every function has to check on its own.
 */
function efWatchlistTicker() {
	global $wgHooks;

	// this one print the html of the wlt box at the desired place.
	$wgHooks['MonoBookTemplateAboveColumnContent'][] = 'efWatchlistTickerAddText';

	// this one injects our css style information in the <style> header
	// to sabotage the skin.
	$wgHooks['SkinTemplateSetupPageCss'  	     ][] = 'efWatchlistTickerSetStyle';

	require_once( dirname( __FILE__ ) . '/TickerList.php' );
}

/**
 * Print the htm of the wlt box.
 * Will be injected at the right place, as we're in the
 * output buffer.
 */
function efWatchlistTickerAddText( &$outObj ) {
	global $wgUser;

	// no watchlist ticker for anons.
	if( $wgUser->isAnon() ) {
		return;
	}

	$list = efWatchlistTickerMakeList();

	print(
		"	<div id='column-right'>\n" .
		"		<div id='mw-ext-wlt-box'>\n" .
		"			<h1 class='firstHeading'>Watchlist</h1>\n"
	);

	// keep the indention.
	foreach( explode( "\n", $list ) as $a ) {
		print( "			$a\n" );
	}

	print(
		"		</div>\n" .
		"	</div>\n"
	);
}

/**
 * Makes the list. Code from /includes/SpecialWatchlist.php,
 * customized to our needs.
 *
 * @return array 
 */
function efWatchlistTickerMakeList() {
	$fname = 'efWatchlistTickerMakeList';

	global $wgUser, $wgOut;

	$dbr = wfGetDB( DB_SLAVE );
	list( $page, $watchlist, $recentchanges ) = $dbr->tableNamesN( 'page', 'watchlist', 'recentchanges' );

	$uid = $wgUser->getId();

	$sql = "SELECT COUNT(*) AS n FROM $watchlist WHERE wl_user = $uid";
	$res = $dbr->query( $sql, $fname );
	$s = $dbr->fetchObject( $res );
	$dbr->freeResult( $res );

	if( $s->n == 0 ) {
		return 'Watchlist is empty.';
	}

	$sql =  "SELECT * " .
		"FROM $watchlist,$recentchanges,$page " .
		"WHERE wl_user=$uid " .
		"AND wl_namespace=rc_namespace " .
		"AND wl_title=rc_title " .
		"AND rc_cur_id=page_id " .
		"ORDER BY rc_timestamp DESC " .
		"LIMIT 15";

	$res = $dbr->query( $sql, $fname );
	$numRows = $dbr->numRows( $res );

	$dbr->dataSeek( $res, 0 );

	$list = new TickerList( $wgUser );
	$s = '';

	$counter = 1;
	while ( $obj = $dbr->fetchObject( $res ) ) {
		# Make RC entry
		$rc = RecentChange::newFromRow( $obj );
		$rc->counter = $counter++;

		$s .= "\t" . $list->recentChangesLine( $rc, $updated );
	}

	$dbr->freeResult( $res );
	return $s;

}


/**
 * Sets the css style information in the <style> header.
 */
function efWatchlistTickerSetStyle( &$style ) {
	global $wgUser;

	// no watchlist ticker for anons.
	if( $wgUser->isAnon() ) {
		return;
	}

	$style .= "\n" .
		  "/* WatchlistTicker extension style information */\n" .
		  "div#mw-ext-wlt-box {\n" .
		  "	float: right;\n" .
		  "	border: 1px solid #aaa;\n" .
		  "	width: 21.2em;\n" .
		  "	position: relative;\n" .
		  "	margin: 2.8em 0 0 .6em;\n" .
		  "	padding: 0 1em 1.5em 1em;\n" .
		  "}\n" .
		  "div#content {\n" .
		  "	border-right: 1px solid #aaa;\n" .
		  "}\n" .
		  "div#column-one {\n" .
		  "	width: 75%;\n" .
		  "}\n" .
		  "div#column-content {\n" .
		  "	width: 75%;\n" .
		  "}\n" .
		  "#p-cactions {\n" .
		  "	width: 51%;\n" .
		  "}\n" .
		  "\n/* END WatchlistTicker */\n";
}

?>
