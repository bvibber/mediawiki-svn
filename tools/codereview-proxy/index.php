<?php

// Quickie remote proxy for Subversion accessor for CodeReview extension.
// Wikimedia's web servers currently don't have direct access to our
// Subversion server, which is in a separate data center.

ini_set( 'display_errors', false);
header('Content-type: application/x-json');

$data = runAction( "http://svn.wikimedia.org/svnroot/mediawiki" );
echo json_encode( $data );

function inputStr( $key, $default=null ) {
	if( isset( $_GET[$key] ) ) {
		return str_replace( "\n", " ",
			str_replace( "\r", "",
				strval( $_GET[$key] ) ) );
	} else {
		return $default;
	}
}

function inputInt( $key, $default=null ) {
	if( isset( $_GET[$key] ) ) {
		return intval( $_GET[$key] );
	} else {
		return $default;
	}
}

function runAction( $basePath ) {
	$svn = SubversionAdaptor::newFromRepo( $basePath );
	$action = inputStr( 'action' );
	if( $action == 'log' ) {
		$path = inputStr( 'path' );
		$start = inputInt( 'start' );
		$end = inputInt( 'end' );
		return $svn->getLog( $path, $start, $end );
	} elseif( $action == 'diff' ) {
		$path = inputStr( 'path' );
		$rev1 = inputInt( 'rev1' );
		$rev2 = inputInt( 'rev2' );
		return $svn->getDiff( $path, $rev1, $rev2 );
	} else {
		return false;
	}
}

abstract class SubversionAdaptor {
	protected $mRepo;

	public static function newFromRepo( $repo ) {
		if( function_exists( 'svn_log' ) ) {
			return new SubversionPecl( $repo );
		} else {
			throw new Exception("Requires SVN pecl module" );
		}
	}

	function __construct( $repo ) {
		$this->mRepo = $repo;
	}

	abstract function getFile( $path, $rev=null );

	abstract function getDiff( $path, $rev1, $rev2 );

	/*
	  array of array(
		'rev' => 123,
		'author' => 'myname',
		'msg' => 'log message'
		'date' => '8601 date',
		'paths' => array(
			array(
				'action' => one of M, A, D, R
				'path' => repo URL of file,
			),
			...
		)
	  */
	abstract function getLog( $path, $startRev=null, $endRev=null );

	protected function _rev( $rev, $default ) {
		if( $rev === null ) {
			return $default;
		} else {
			return intval( $rev );
		}
	}
}

/**
 * Using the SVN PECL extension...
 * Untested!
 */
class SubversionPecl extends SubversionAdaptor {
	function getFile( $path, $rev=null ) {
		return svn_cat( $this->mRepo . $path, $rev );
	}

	function getDiff( $path, $rev1, $rev2 ) {
		list( $fout, $ferr ) = svn_diff(
			$this->mRepo . $path, $rev1,
			$this->mRepo . $path, $rev2 );
		
		if( $fout ) {
			// We have to read out the file descriptors. :P
			$out = '';
			while( !feof( $fout ) ) {
				$out .= fgets( $fout );
			}
			fclose( $fout );
			fclose( $ferr );
	
			return $out;
		} else {
			return new Exception("Diffing error");
		}
	}

	function getLog( $path, $startRev=null, $endRev=null ) {
		return svn_log( $this->mRepo . $path,
			$this->_rev( $startRev, SVN_REVISION_INTIAL ),
			$this->_rev( $endRev, SVN_REVISION_HEAD ) );
	}
}
