<?php

// Quickie remote proxy for Subversion accessor for CodeReview extension.
// Wikimedia's web servers currently don't have direct access to our
// Subversion server, which is in a separate data center.

ini_set( 'display_errors', false);
header('Content-type: application/x-php-serialized');

$allowedBases = array(
	'http://svn.wikimedia.org/svnroot/mediawiki',
	'http://svn.wikimedia.org/svnroot/pywikipedia',
	'http://svn.wikimedia.org/svnroot/mysql',
);

$base = inputStr( 'base', $allowedBases[0] );
if ( !in_array( $base, $allowedBases ) ) {
	echo serialize( false );
	exit;
}

$data = runAction( $base );
echo serialize( $data );

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
			return new SubversionShell( $repo );
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

/**
 * Using the thingy-bobber
 */
class SubversionShell extends SubversionAdaptor {
	function getFile( $path, $rev=null ) {
		if( $rev )
			$path .= "@$rev";
		$command = sprintf(
			"LC_ALL=en_US.utf-8 svn cat %s",
			escapeshellarg( $this->mRepo . $path ) );

		return shell_exec( $command );
	}

	function getDiff( $path, $rev1, $rev2 ) {
		$command = sprintf(
			"LC_ALL=en_US.utf-8 svn diff -r%d:%d %s",
			intval( $rev1 ),
			intval( $rev2 ),
			escapeshellarg( $this->mRepo . $path ) );

		return shell_exec( $command );
	}

	function getLog( $path, $startRev=null, $endRev=null ) {
		$command = sprintf(
			"LC_ALL=en_US.utf-8 svn log -v -r%s:%s --non-interactive %s",
			escapeshellarg( $this->_rev( $startRev, 'BASE' ) ),
			escapeshellarg( $this->_rev( $endRev, 'HEAD' ) ),
			escapeshellarg( $this->mRepo . $path ) );

		$lines = explode( "\n", shell_exec( $command ) );
		$out = array();

		$divider = str_repeat( '-', 72 );
		$formats = array(
			'rev' => '/^r(\d+)$/',
			'author' => '/^(.*)$/',
			'date' => '/^(.*?) \(.*\)$/',
			'lines' => '/^(\d+) lines?$/',
		);
		$state = "start";
		foreach( $lines as $line ) {
			$line = rtrim( $line );

			switch( $state ) {
			case "start":
				if( $line == $divider ) {
					$state = "revdata";
					break;
				} else {
					return $out;
					#throw new Exception( "Unexpected start line: $line" );
				}
			case "revdata":
				if( $line == "" ) {
					$state = "done";
					break;
				}
				$data = array();
				$bits = explode( " | ", $line );
				$i = 0;
				foreach( $formats as $key => $regex ) {
					$text = $bits[$i++];
					if( preg_match( $regex, $text, $matches ) ) {
						$data[$key] = $matches[1];
					} else {
						throw new Exception(
							"Unexpected format for $key in '$text'" );
					}
				}
				$data['msg'] = '';
				$data['paths'] = array();
				$state = 'changedpaths';
				break;
			case "changedpaths":
				if( $line == "Changed paths:" ) {
					$state = "path";
				} elseif( $line == "" ) {
					// No changed paths?
					$state = "msg";
				} else {
					throw new Exception(
						"Expected 'Changed paths:' or '', got '$line'" );
				}
				break;
			case "path":
				if( $line == "" ) {
					// Out of paths. Move on to the message...
					$state = 'msg';
				} else {
					if( preg_match( '/^   (.) (.*)$/', $line, $matches ) ) {
						$data['paths'][] = array(
							'action' => $matches[1],
							'path' => $matches[2] );
					}
				}
				break;
			case "msg":
				$data['msg'] .= $line;
				if( --$data['lines'] ) {
					$data['msg'] .= "\n";
				} else {
					unset( $data['lines'] );
					$out[] = $data;
					$state = "start";
				}
				break;
			case "done":
				throw new Exception( "Unexpected input after end: $line" );
			default:
				throw new Exception( "Invalid state '$state'" );
			}
		}

		return $out;
	}
}
