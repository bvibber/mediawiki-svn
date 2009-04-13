<?php

class NssGroup {
	private static $groupsByGid = array();
	public static function nameFromGid( $gid ) {
		if ( isset( self::$groupsByGid( $gid ) ) )
			return self::$groupsByGid( $gid );
		
		global $wgAuth;
		$dbr = $wgAuth->getDB( DB_READ );
		$res = $dbr->select( 'groups', 'grp_name', array( 'grp_gid' => $gid ), __METHOD__ );
		
		$row = $res->fetchObject();
		self::$groupsByGid[$gid] = $row ? $row->grp_name : strval( $gid );
		
		return self::$groupsByGid( $gid );
	}
}