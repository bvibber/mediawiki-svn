<?php

/**
 * Messages file for ExportWatchlist extension
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */

/**
 * Prepare extension messages
 *
 * @return array
 */
function efExportWatchlistMessages() {
	$messages = array(

/**
 * English
 */
'en' => array(
	'exportwatchlist' => 'Export watchlist',
	'exportwatchlist-header' => 'Use this page to export [[Special:Watchlist|your watchlist]]
		to a list of titles so you can move it to another account or share it with other users.',
	'exportwatchlist-login' => 'You need to have an account and be $1 to export a watchlist.',
	'exportwatchlist-login-link' => 'logged in',
	'exportwatchlist-legend' => 'Export watchlist',
	'exportwatchlist-namespace' => 'Namespace:',
	'exportwatchlist-submit' => 'Export',
	'exportwatchlist-none' => 'Your watchlist contains no pages.',
	'exportwatchlist-none-ns' => 'Your watchlist contains no pages in this namespace.',
	'importwatchlist' => 'Import watchlist',
	'importwatchlist-header' => 'Use this page to bulk-add a list of titles to
	[[Special:Watchlist|your watchlist]].',
	'importwatchlist-login' => 'You need to have an account and be $1 to import a watchlist.',
	'importwatchlist-legend' => 'Import titles into watchlist',
	'importwatchlist-titles' => 'Enter a list of titles below, one per line:',
	'importwatchlist-submit' => 'Import',
	'importwatchlist-success' => 'Imported $1 item(s) into the watchlist:',
),	
	
	);
	return $messages;
}