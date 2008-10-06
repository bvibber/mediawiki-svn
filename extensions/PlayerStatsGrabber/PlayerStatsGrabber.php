<?php
/*
 * simple stats output and gather for oggPlay and a "sample page" 
 */

//add SpecialPlayerStats Output page: 
$wgSpecialPages['PlayerStats']      = array('SpecialPlayerStatsPage',
											'PlayerStats', 
											'mwExecutePlayerStats',
											 dirname( __FILE__ ) . '/specials/Statistics/SMW_SpecialStatistics.php', true, '');
$wgSpecialPageGroups['PlayerStats'] = 'wiki'; // like Special:Statistics

//add ajax hook to accept the status input: 
$wgAjaxExportList[] = 'mw_push_player_stats';

$wgExtensionCredits['media'][] = array(
	'name'           => 'PlayerStats',
	'author'         => 'Michael Dale',
	'svn-date' 		 => '$LastChangedDate: 2008-08-06 07:18:43 -0700 (Wed, 06 Aug 2008) $',
	'svn-revision' 	 => '$LastChangedRevision: 38715 $',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:PlayerStats',
	'description'    => 'PlayerStats and survey for monitoring theora support relative to flash'	
);


/*
 * does a player stats request.. returns the "db key"
 *  (lets people fill out survay after playing clip) 
 *  or 
 *  (ties survay page data to detection) 
 */
function mw_push_player_stats(){
	global $wgRequest;
	//do the insert into the userPlayerStats table:
	$dbw =& wfGetDB( DB_WRITE );
	//print_r($wgRequest);
	
}
?>