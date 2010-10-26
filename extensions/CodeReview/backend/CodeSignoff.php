<?php
class CodeSignoff {
	public $rev, $user, $flag, $timestamp;
	
	public function __construct( $rev, $user, $flag, $timestamp ) {
		$this->rev = $rev;
		$this->user = $user;
		$this->flag = $flag;
		$this->timestamp = $timestamp;
	}
	
	public static function newFromRow( $rev, $row ) {
		return self::newFromData( $rev, get_object_vars( $row ) );
	}
	
	public static function newFromData( $rev, $data ) {
		return new self( $rev, $data['cs_user_text'], $data['cs_flag'],
			wfTimestamp( TS_MW, $data['cs_timestamp'] )
		);
	}
}
