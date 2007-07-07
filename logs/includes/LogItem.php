<?php

/**
 * Represents an individual item in the `logging` table
 * and provides access to properties and formatting
 *
 * @addtogroup Logging
 * @author Rob Church <robchur@gmail.com>
 */
class LogItem {

	/**
	 * Log identifier
	 */
	private $id = 0;
	
	/**
	 * Log type and action
	 */
	private $type = '';
	private $action = '';
	
	/**
	 * Date and time of the event
	 */
	private $timestamp = '';
	
	/**
	 * User performing the action
	 */
	private $user = null;
	
	/**
	 * Target of the action
	 */
	private $target = null;
	
	/**
	 * Log comment
	 */
	private $comment = '';
	
	/**
	 * Additional parameters
	 */
	private $params = array();
	
	/**
	 * Deletion state
	 */
	private $deleted = 0;
	
	/**
	 * Constructor
	 *
	 * Do not instantiate; use either LogItem::newFromRow() or
	 * LogItem::newFromChangesRow()
	 *
	 * @param int $id
	 * @param string $type
	 * @param string $action
	 * @param string $timestamp
	 * @param LogUser $user
	 * @param Title $target
	 * @param string $comment
	 * @param string $params
	 * @param int $deleted
	 */
	private function __construct( $id, $type, $action, $timestamp, $user, $target, $comment, $params, $deleted ) {
		$this->id = $id;
		$this->type = $type;
		$this->action = $action;
		$this->timestamp = wfTimestamp( TS_MW, $timestamp );
		$this->user = $user;
		$this->target = $target;
		$this->comment = $comment;
		$this->params = LogPage::extractParams( $params );
		$this->deleted = $deleted;
	}
	
	/**
	 * Instantiate a LogItem using data from a `logging` table row
	 * (needs to be joined with the `user` table)
	 *
	 * @param object $row
	 * @return LogItem
	 */
	public static function newFromRow( $row ) {
		return new self(
			$row->log_id,
			$row->log_type,
			$row->log_action,
			$row->log_timestamp,
			new LogUser( $row->log_user, $row->user_name ),
			Title::makeTitleSafe( $row->log_namespace, $row->log_title ),
			$row->log_comment,
			$row->log_params,
			$row->log_deleted
		);
	}
	
	/**
	 * Instantiate a LogItem using data from a RecentChange
	 *
	 * @param RecentChange $change
	 * @return LogItem
	 */
	public static function newFromRecentChange( $change ) {
		$attr =& $change->mAttribs;
		return new self(
			$attr['rc_logid'],
			$attr['rc_log_type'],
			$attr['rc_log_action'],
			$attr['rc_timestamp'],
			new LogUser( $attr['rc_user'], $attr['rc_user_text'] ),
			Title::makeTitleSafe( $attr['rc_namespace'], $attr['rc_title'] ),
			$attr['rc_comment'],
			$attr['rc_params'],
			$attr['rc_deleted']
		);
	}
	
	/**
	 * Get the log type
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * Get the log action
	 *
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}
	
	/**
	 * Get the combined key/action
	 *
	 * @return string
	 */
	public function getActionKey() {
		return "{$this->type}/{$this->action}";
	}
	
	/**
	 * Get the event timestamp
	 *
	 * @return string
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}
	
	/**
	 * Get the user performing the action
	 *
	 * @return User
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * Get the target page
	 *
	 * @return Title
	 */
	public function getTarget() {
		return $this->target;
	}
	
	/**
	 * Get the log comment
	 *
	 * @return string
	 */
	public function getComment() {
		return $this->comment;
	}
	
	/**
	 * Get additional parameters
	 *
	 * @return array
	 */
	public function getParameters() {
		return $this->params;
	}
	
	/**
	 * Format this log item as a string, returning
	 * a complete list item
	 *
	 * @param int $flags
	 * @return string
	 */
	public function format( $flags = 0 ) {
		return LogFormatter::format( $this, $flags );
	}

}