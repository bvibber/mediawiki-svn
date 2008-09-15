<?php
if (!defined('MEDIAWIKI')) die();

class CodeComment {
	function __construct( $repo, $row ) {
		$this->repo = $repo;
		$this->id = $row->cc_id;
		$this->text = $row->cc_text; // fixme
		$this->user = $row->cc_user;
		$this->userText = $row->cc_user_text;
		$this->timestamp = wfTimestamp( TS_MW, $row->cc_timestamp );
		$this->review = $row->cc_review;
	}
}
