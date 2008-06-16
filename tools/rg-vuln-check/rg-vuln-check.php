#!/usr/bin/env php
<?php

if ( php_sapi_name() != 'cli' ) {
	echo "This script must be run on the command line\n";
	exit( 1 );
}

if ( !defined( 'RGVULN_INC' ) ) {
	$options = array();

	if ( in_array( '-v', $argv ) ) {
		$options['verbose'] = true;
		$argv = array_diff( $argv, array( '-v' ) );
	}
	if ( in_array( '--opcodes', $argv ) ) {
		$options['opcodes'] = true;
		$argv = array_diff( $argv, array( '--opcodes' ) );
	}
	if ( in_array( '--pairs', $argv ) ) {
		$options['combinations'] = 2;
		$argv = array_diff( $argv, array( '--pairs' ) );
	}


	if ( count( $argv ) <= 1 ) {
		echo "Usage: php {$argv[0]} [-v] [--opcodes] [--pairs] <filename> [<filename> ...]\n";
		exit( 1 );
	}

	$confFile = dirname( __FILE__ ) . '/conf.php';
	if ( !file_exists( $confFile ) ) {
		echo "Configuration file not found\n";
		echo "Copy conf.php.sample to conf.php, and change the settings to suit your installation.\n";
		exit( 1 );
	}

	$cvc = new ClassicVulnerabilityCheck( $options );
	$cvc->readConf( $confFile );

	array_shift( $argv );
	$good = true;
	foreach ( $argv as $file ) {
		$good = $good && $cvc->check( $file );
	}

	exit( $good ? 0 : 1 );
}

class ClassicVulnerabilityCheck {
	/**
	 * Set this to the base URL where all the scripts to be tested are kept. It 
	 * should be configured with register_globals, display_errors and open_basedir 
	 * enabled.
	 */
	var $jailDir = '/home/tstarling/src/mediawiki';

	/**
	 * Set this to the local directory corresponding to the URL
	 */
	var $jailUrl = 'http://shimmer/jail';

	/*
	 * Be verbose
	 */
	var $verbose = false;

	/**
	 * Dump parsekit output
	 */
	var $opcodes = false;

	/**
	 * Try all combinations of this many globals
	 */
	var $combinations = 1;

	function __construct( $options = array() ) {
		foreach ( $options as $name => $value ) {
			$this->$name = $value;
		}
	}

	function readConf( $filename ) {
		$jailDir = $this->jailDir;
		$jailUrl = $this->jailUrl;
		include( $filename );
		$this->jailDir = $jailDir;
		$this->jailUrl = $jailUrl;
	}

	function compile( $filename ) {
		// This call needs to be in its own function, otherwise it segfaults
		return array( $parseInfo, $errors );
	}

	function check( $filename ) {
		$errors = false;
		$parseInfo = parsekit_compile_file( $filename, $errors );

		if ( $errors ) {
			echo "Errors encountered:\n";
			print_r( $errors );
			return false;
		}

		if ( $this->opcodes ) {
			var_dump( $parseInfo );
		}

		$globals = array_keys( $this->getGlobalsFromParseInfo( $parseInfo ) );

		if ( !$globals ) {
			if ( $this->verbose ) {
				print "$filename: SECURE (No globals referenced)\n";
			}
			return true;
		}

		if ( $this->verbose ) {
			print "Globals referenced: " . implode( ', ', $globals ) . "\n";
		}

		$filename = realpath( $filename );
		if ( substr( $filename, 0, strlen( $this->jailDir ) + 1 ) !== $this->jailDir . '/' ) {
			echo "The file specified is not in the jail directory\n";
			return false;
		}

		// Need to do each global separately because some globals will 
		// generate a fatal when set to a string, masking the vulnerability
		$ret = true;
		for ( $level = 1; $level <= $this->combinations; $level++ ) {
			if ( !$this->invokeCombinations( $filename, $globals, $level ) ) {
				$ret = false;
			}
		}
		if ( $ret ) {
			if ( $this->verbose ) {
				echo "$filename SECURE\n";
			}
		}
		return $ret;
	}

	function invokeCombinations( $filename, $globals, $level, $fixedGlobals = array() ) {
		$ret = true;
		if ( $level <= 1 ) {
			foreach ( $globals as $global ) {
				if ( !$this->invoke( $filename, array_merge( $fixedGlobals, array( $global ) ) ) ) {
					$ret = false;
				}
			}
		} else {
			foreach ( $globals as $global ) {
				$newFixedGlobals = array_merge( $fixedGlobals, array( $global ) );
				$newGlobals = array_diff( $globals, array( $global ) );
				if ( !$this->invokeCombinations( $filename, $newGlobals, $level - 1, $newFixedGlobals ) ) {
					$ret = false;
				}
			}
		}
		return $ret;
	}

	function invoke( $filename, $globals ) {
		$url = $this->jailUrl . substr( $filename, strlen( $this->jailDir ) );
		$first = true;
		foreach ( $globals as $i => $global ) {
			if ( $first ) {
				$first = false;
				$url .= '?';
			} else {
				$url .= '&';
			}
			$url .= urlencode( $global ) . '=' . urlencode( "/TEST_GLOBAL_$i<>" );
		}
		if ( $this->verbose ) {
			echo "Fetching $url\n";
		}

		$curl = curl_init( $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		$html = curl_exec( $curl );
		$lines = explode( "\n", $html );

		if ( $this->verbose ) {
			echo str_repeat( '-', 76 );
			echo "\n$html\n";
			echo str_repeat( '-', 76 ) . "\n";
		}

		$ret = true;
		$globalString = implode( '+', $globals );
		foreach ( $globals as $i => $global ) {
			if ( strpos( $html, "TEST_GLOBAL_$i<>" ) ) {
				echo "$filename XSS VULNERABILITY $globalString\n";
				$ret = false;
			}
			foreach ( $lines as $line ) {
				if ( preg_match( "/TEST_GLOBAL_$i.*failed to open stream/", $line ) ) {
					echo "$filename INCLUSION VULNERABILITY $globalString\n";
					$ret = false;
				}
			}
		}
		return $ret;
	}

	function getGlobalsFromFunction( $opArray ) {
		$globals = array();
		foreach ( $opArray as $i => $opLine ) {
			// Plain ZEND_FETCH_W
			if ( $opLine['opcode_name'] == 'ZEND_FETCH_W' 
				&& $opLine['op1']['type_name'] == 'IS_CONST' ) 
			{
				$globals[$opLine['op1']['constant']] = true;
			}

			// $GLOBALS[...]
			if ( $opLine['opcode_name'] == 'ZEND_FETCH_R' 
				&& $opLine['op1']['type_name'] == 'IS_CONST'
				&& $opLine['op1']['constant'] == 'GLOBALS'
				&& $opLine['result']['type_name'] == 'IS_VAR'
				&& isset( $opArray[$i+1] )
				&& $opArray[$i+1]['opcode_name'] == 'ZEND_FETCH_DIM_R'
				&& $opArray[$i+1]['op1']['type_name'] == 'IS_VAR'
				&& $opLine['result']['var'] == $opArray[$i+1]['op1']['var']
				&& $opArray[$i+1]['op2']['type_name'] == 'IS_CONST' )
			{
				$globals[$opArray[$i+1]['op2']['constant']] = true;
			}

		}
		return $globals;
	}

	function getGlobalsFromTop( $opArray ) {
		// Start with ZEND_FETCH_W calls
		$globals = $this->getGlobalsFromFunction( $opArray );
		// Now add local variable references
		foreach ( $opArray as $opLine ) {
			if ( isset( $opLine['op1']['varname'] ) ) {
				$globals[$opLine['op1']['varname']] = true;
			}
			if ( isset( $opLine['op2']['varname'] ) ) {
				$globals[$opLine['op2']['varname']] = true;
			}
		}
		return $globals;
	}

	function getGlobalsFromParseInfo( $parseInfo ) {
		// Get globals referenced in the file scope
		$globals = $this->getGlobalsFromTop( $parseInfo['opcodes'] );

		// Get globals referenced in global functions
		if ( isset( $parseInfo['function_table'] ) ) {
			foreach ( $parseInfo['function_table'] as $function ) {
				$globals += $this->getGlobalsFromFunction( $function['opcodes'] );
			}
		}

		// Get globals from class member functions
		if ( isset( $parseInfo['class_table'] ) ) {
			foreach ( $parseInfo['class_table'] as $class ) {
				if ( isset( $class['function_table'] ) ) {
					foreach ( $class['function_table'] as $function ) {
						$globals += $this->getGlobalsFromFunction( $function['opcodes'] );
					}
				}
			}
		}
		return $globals;
	}
}
