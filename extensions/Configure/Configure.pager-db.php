<?php

/**
 * Class used to list versions at Special:ViewConfig when using a database
 * handler
 *
 * @ingroup Extensions
 * @author Alexandre Emsenhuber
 */
class ConfigurationPagerDb extends ReverseChronologicalPager {
	protected $mHandler, $mCallback, $mCounter;

	function __construct( ConfigureHandlerDb $handler ) {
		parent::__construct();
		$this->mHandler = $handler;
		$this->mDb = $handler->getSlaveDB();
	}

	function getQueryInfo() {
		$queryInfo = array(
			'tables'  => array( 'config_version' ),
			'fields'  => array( '*' ),
			'conds'   => array(),
			'options' => array()
		);
		return $queryInfo;
	}

	function getIndexField() {
		return 'cv_timestamp';
	}

	function setFormatCallback( $callback ) {
		$this->mCallback = $callback;
	}

	function formatRow( $row ) {
		if( !is_callable( $this->mCallback ) )
			throw new MWException( 'ConfigurationPagerDb::$mCallback not callable' );
		$this->mCounter++;
		$info = array(
			'timestamp' => $row->cv_timestamp,
			'wikis' => array( $row->cv_wiki ),
			'count' => $this->mCounter,
		);
		return call_user_func( $this->mCallback, $info );
	}

	function getStartBody() {
		$this->mCounter = 0;
		return "<ul>\n";
	}

	function getEndBody() {
		return "</ul>\n";
	}
}
