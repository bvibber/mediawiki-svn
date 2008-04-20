<?php

class SiteMatrixParserFunctions {
	private static $instance = null;
	
	static function singleton() {
		if( !self::$instance )
			self::$instance = new SiteMatrixParserFunctions();
		return self::$instance;
	}

	public function registerParser( $parser ) {
		if ( defined( get_class( $parser ) . '::SFH_OBJECT_ARGS' ) ) {
			// These functions accept DOM-style arguments
			$parser->setFunctionHook( 'ifsiteexists', array( &$this, 'ifSiteExistsObj' ), SFH_OBJECT_ARGS );
		} else {
			$parser->setFunctionHook( 'ifsiteexists', array( &$this, 'ifSiteExists' ) );
		}
		$parser->setFunctionHook( 'siteurl', array( &$this, 'siteurl' ) );

		return true;
	}

	public function siteurl( &$parser, $site = '', $title = '' ) {
		$matrix = SiteMatrix::singleton();
		$host = $matrix->getSitenameById( $site );
		if( !$host ) return '';	//Fail. May be caught by {{#if}}
		return $host;
	}

	public function ifSiteExistsObj( $parser, $frame, $args ) {
		$matrix = SiteMatrix::singleton();
		$site = isset( $args[0] ) ? trim( $frame->expand( $args[0] ) ) : '';
		$true = $site && $matrix->siteExists( $site );
		if ( $true ) {
			return isset( $args[1] ) ? trim( $frame->expand( $args[1] ) ) : '';
		} else {
			return isset( $args[2] ) ? trim( $frame->expand( $args[2] ) ) : '';
		}
	}

	public function ifSiteExists( $parser, $site = '', $then = '', $else = '' ) {
		$matrix = SiteMatrix::singleton();
		$true = $site && $matrix->siteExists( $site );
		if ( $true ) {
			return $then;
		} else {
			return $else;
		}
	}
}