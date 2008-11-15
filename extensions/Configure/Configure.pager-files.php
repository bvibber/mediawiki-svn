<?php

/**
 * Class used to list versions at Special:ViewConfig when using a files handler
 *
 * @ingroup Extensions
 * @author Alexandre Emsenhuber
 */
class ConfigurationPagerFiles implements Pager {
	protected $mHandler, $mCallback;

	function __construct( ConfigureHandler $handler ) {
		$this->mHandler = $handler;
	}

	function getBody() {
		$versions = $this->mHandler->listArchiveVersions();
		if( empty( $versions ) ){
			return wfMsgExt( 'configure-no-old', array( 'parse' ) );
		}

		$text = "<ul>\n";
		$count = 0;
		foreach( $versions as $version ){
			$count++;
			$wikis = array_keys( $this->mHandler->getOldSettings( $version ) );
			$info = array(
				'timestamp' => $version,
				'wikis' => $wikis,
				'count' => $count,
			);
			$text .= $this->formatRow( $info );
		}
		$text .= "</ul>\n";
		return $text;
	}

	function getNumRows() {
		return count( $this->mHandler->listArchiveVersions() );
	}

	function getNavigationBar() {
		return '';
	}

	function setFormatCallback( $callback ) {
		$this->mCallback = $callback;
	}

	function formatRow( $info ) {
		if( !is_callable( $this->mCallback ) )
			throw new MWException( 'ConfigurationPagerFiles::$mCallback not callable' );
		return call_user_func( $this->mCallback, $info );
	}
}
