<?php

/**
 * Hooks for Usability Initiative UserDailyContribs extension
 *
 * @file
 * @ingroup Extensions
 */

class UserDailyContribsHooks {
	
	public static function schema() {
		global $wgExtNewTables;

		$wgExtNewTables[] = array(
			'user_daily_contribs',
			dirname( __FILE__ ) . '/UserDailyContribs.sql'
		);

		return true;
	}
	
	
	/**
	 * Stores a new contribution
	 * @return true
	 */
	public static function storeNewContrib(){
		global $wgUser;
		$today = gmdate( 'Ymd', time() );
		$dbw = wfGetDB( DB_MASTER );
		/*
		//there seems no way to set contribs to contribs+1 in a reasonably fast manner in this framework
		try{
			$dbw->insert("user_daily_contribs", array("user_id" => $wgUser->getId(), "day" => $today, "contribs" => 1), __METHOD__); 
		}
		catch(Exception $e){
			$dbw->update( "user_daily_contribs", array( "contribs" => "contribs+1"), array("user_id" => $wgUser->getId(), "day" => $today), __METHOD__);
		}
		*/
		$sql = 
		"INSERT INTO user_daily_contribs (user_id,day,contribs) VALUES ({$wgUser->getId()},$today,1) ON DUPLICATE KEY UPDATE contribs=contribs+1;";
		$dbw->query($sql, __METHOD__);
		return true;
	}
	
}