<?php

/*
 * Checks a number of syntax conventions on variables from a valid PHP file.
 *
 * Run as:
 *  find phase3/ \( -name \*.php -or -name \*.inc \) -not \( -name importUseModWiki.php -o -name diffLanguage.php \) -exec php tools/code-utils/check-vars.php \{\} +
 */

require_once( dirname( __FILE__ ) . "/../../phase3/includes/Defines.php" ); # Faster than parsing
require_once( dirname( __FILE__ ) . "/../../phase3/includes/AutoLoader.php" );

$mwDeprecatedFunctions = false;
@include( dirname( __FILE__ ) . "/deprecated.functions" );

if ( !extension_loaded( 'sockets' ) ) dl( 'sockets.so' );
if ( !extension_loaded( 'PDO' ) ) dl( 'pdo.so' );

$wgAutoloadLocalClasses += array(
		'DBAccessError' => 'LBFactory',
		'Maintenance' => 'Maintenance.php',
		'MessageGroup' => 'Translate extension interface',
		'MessageGroups' => 'Translate extension',
		'PremadeMediawikiExtensionGroups' => 'Translate extension',
		'languages' => 'maintenance/language/languages.inc',
		'extensionLanguages' => 'maintenance/language/languages.inc',
		'MessageWriter' => 'maintenance/language/writeMessagesArray.inc',
		'tidy' => 'pecl tidy',
		'PEAR' => 'pear',
		'Normalizer' => 'pecl intl',
		'Mail' => 'pear Mail',

		'UserDupes' => 'maintenance/userDupes.inc',
		'DeleteDefaultMessages' => 'maintenance/deleteDefaultMessages.php',
		'PopulateCategory' => 'maintenance/populateCategory.php',
		'PopulateParentId' => 'maintenance/populateParentId.php',
		'PopulateRevisionLength' => 'maintenance/populateRevisionLength.php',
		'PopulateLogSearch' => 'maintenance/populateLogSearch.php',
		'BaseDump' => 'maintenance/backupPrefetch.inc',
		'ExportProgressFilter' => 'maintenance/backup.inc'
	);

class CheckVars {
	var $mDebug = false;
	static $mDefaultSettingsGlobals = null;

	static $constantIgnorePrefixes = array( "PGSQL_", "OCI_", "SQLT_BLOB", "DB2_", "XMLREADER_" ); # Ignore constants with these prefixes
	protected $generateDeprecatedList = false;

	/* Values for status */
	const WAITING_FUNCTION = 0;
	const IN_FUNCTION_NAME = 1;
	const IN_FUNCTION = 2;
	const IN_GLOBAL = 3;
	const IN_INTERFACE = 4;

	/* Token specializations */
	const CLASS_NAME = -4;
	const CLASS_MEMBER = -5;
	const FUNCTION_NAME = -6;
	const FUNCTION_DEFINITION = -7;

	/* Function attribute */
	const FUNCTION_DEPRECATED = -8;

	function __construct() {
		if ( self::$mDefaultSettingsGlobals == null ) {
			$this->load( dirname( dirname( dirname( __FILE__ ) ) ) . "/phase3/includes/DefaultSettings.php", false );

			if ( count( $this->mTokens ) > 0 ) {
				$globals = array (
					'$wgArticle', # Setup.php
					'$wgAutoloadLocalClasses', # AutoLoader.php, a couple of readers
					'$wgBlobCache', # HistoryBlob.php
					'$wgCaches', # ObjectCache.php
					'$wgCanonicalNamespaceNames', # Namespace.php
					'$wgContLang', # Setup.php

					'$wgContLanguageCode', # Should probably be removed
					'$wgDatabase', # For update scripts
					'$wgDBcataloged', # http://www.mediawiki.org/wiki/Special:Code/MediaWiki/45755#c7954
					'$wgDeferredUpdateList', # Setup.php
					'$wgExternalBlobCache', # ExternalStoreDB.php

					'$wgExtModifiedFields', '$wgExtNewFields', '$wgExtNewIndexes', '$wgExtNewTables', # Updates

					'$wgFeedClasses', # Defines.php, many uses
					'$wgFullyInitialised', # Set by Setup.php, read by Exception
					'$wgHtmlEntities', '$wgHtmlEntityAliases', # Sanitizer.php
					'$wgIP', # Setup.php
					'$wgLang', # Setup.php
					'$wgLanguageNames', # Language.php, read by others
					'$wgMemc', # Setup.php
					'$wgMessageCache', # Setup.php

					'$wgNoDBParam', # maintenance, serialized
					'$wgOut', # Setup.php
					'$wgParser', # Setup.php
					'$wgPostCommitUpdateList', # Initialised in Setup.php, should be removed
					'$wgProfiler', # StartProfiler.php
					'$wgProfiling', # Profiler.php
					'$wgQueryPages', # QueryPage.php
					'$wgRequest', # Setup.php
					'$wgRequestTime', # WebStart.php
					'$wgRUstart', # WebStart.php, for Profiler stuff
					'$wgTitle', # index.php
					'$wgUpdates', # updaters
					'$wgUseEnotif', # Setup.php
					'$wgUseNormalUser', # maintenance
					'$wgUser', # Setup.php
					'$wgWikiFarm', # maintenance, to be removed
				);

				foreach ( $this->mTokens as $token ) {
					if ( is_array( $token ) && ( $token[0] == T_VARIABLE ) && ( substr( $token[1], 0, 3 ) == '$wg' ) ) {
						$globals[] = $token[1];
					}
				}
				self::$mDefaultSettingsGlobals = array_unique( $globals );
				$this->mTokens = array(); # Free
			}
		}
	}

	function setGenerateDeprecatedList( $bool = true ) {
		$this->generateDeprecatedList = $bool;
	}
	function getGenerateDeprecatedList() {
		return $this->generateDeprecatedList;
	}
	function saveDeprecatedList( $filename ) {
		file_put_contents( $filename, "<?php\n\$mwDeprecatedFunctions = array( " . implode( ",\n\t", $this->mDeprecatedFunctionList ) . "\n);\n\n" );
	}


	function load( $file, $shortcircuit = true ) {
		$this->mProblemCount = 0;
		$this->mFilename = $file;
		$source = file_get_contents( $file );
		if ( substr( $source, 0, 3 ) == "\xEF\xBB\xBF" ) {
			$this->warning( "$file has an UTF-8 BOM" );
		}
		$source = rtrim( $source );
		if ( substr( $source, -2 ) == '?>' ) {
			$this->warning( "?> at end of file is deprecated in MediaWiki code" );
		}
		if ( $shortcircuit && !preg_match( "/^[^'\"#*]*function [^\"']*\$/m", $source ) ) {
			$this->mTokens = array();
			return;
		}
		$this->mTokens = token_get_all( $source );
		$this->mStatus = self::WAITING_FUNCTION;
		$this->mFunctionQualifiers = array();
		$this->mKnownFileClasses = array();
		$this->mUnknownClasses = array();


		$this->mConstants = array( 'PARSEKIT_SIMPLE', 'UNORM_NFC', # Extensions
			/* Defined in Title.php and GlobalFunctions.php */
			'GAID_FOR_UPDATE', 'TC_MYSQL', 'TS_UNIX', 'TS_MW', 'TS_DB', 'TS_RFC2822', 'TS_ISO_8601', 'TS_EXIF', 'TS_ORACLE', 'TS_POSTGRES', 'TS_DB2' ) ;
	}

	static $functionQualifiers = array( T_ABSTRACT, T_PRIVATE, T_PUBLIC, T_PROTECTED, T_STATIC );

	function execute() {
		$currentToken = null;

		foreach ( $this->mTokens as $token ) {
			if ( self::isMeaningfulToken( $currentToken ) )
				$lastMeaningfulToken = $currentToken;
			$currentToken = $token;

			if ( $lastMeaningfulToken[0] == T_OPEN_TAG && $token[0] == T_OPEN_TAG ) {
				# See r69767
				$this->warning( "$token[1] in line $token[2] after $lastMeaningfulToken[1] in line $lastMeaningfulToken[2]" );
			}

			if ( $lastMeaningfulToken[0] == T_DECLARE && $token[0] == T_STRING ) {
				$currentToken[0] = T_WHITESPACE; # Ignore the ticks or encoding
				continue;
			}

			if ( is_array( $token ) && ( $token[0] == T_CONSTANT_ENCAPSED_STRING ) && is_array( $lastMeaningfulToken )
				&& ( ( $lastMeaningfulToken[0] == T_STRING ) || ( $lastMeaningfulToken[0] == self::FUNCTION_NAME ) )
				&& ( $lastMeaningfulToken[1] == 'define' ) ) {

				// Mark as defined
				$this->mConstants[] = trim( $token[1], "'\"" );
			}

			if ( is_array( $token ) && ( $token[0] == T_CONSTANT_ENCAPSED_STRING ) && is_array( $lastMeaningfulToken )
				&& ( ( $lastMeaningfulToken[0] == T_STRING ) || ( $lastMeaningfulToken[0] == self::FUNCTION_NAME ) )
				&& ( $lastMeaningfulToken[1] == 'defined' ) ) {

				// FIXME: Should be marked as defined only inside this T_IF
				$this->mConstants[] = trim( $token[1], "'\"" );
			}

			switch ( $this->mStatus ) {
				case self::WAITING_FUNCTION:
					if ( $token == ';' )
						$this->mFunctionQualifiers = array();

					if ( $token[0] == T_DOC_COMMENT ) {
						if ( strpos( $token[1], '@deprecated' ) !== false ) {
							$this->mFunctionQualifiers[] = self::FUNCTION_DEPRECATED;
						}
					}
					if ( in_array( $token[0], self::$functionQualifiers ) ) {
						$this->mFunctionQualifiers[] = $token[0];
					}
					if ( $token[0] == T_INTERFACE ) {
						$this->mStatus = self::IN_INTERFACE;
					}

					if ( ( $lastMeaningfulToken[0] == T_CLASS ) && ( $token[0] == T_STRING ) ) {
						$this->mKnownFileClasses[] = $token[1];
						$this->mClass = $token[1];
					}

					if ( $token[0] != T_FUNCTION )
						continue;
					$this->mStatus = self::IN_FUNCTION_NAME;
					break;

				case self::IN_FUNCTION_NAME:
					if ( ( $token == '&' ) || ( $token[0] == T_WHITESPACE ) )
						continue;
					if ( $token[0] == T_STRING ) {
						$this->mFunction = $token[1];
						$this->mStatus = self::IN_FUNCTION;
						$this->mBraces = 0;
						$this->mFunctionGlobals = array();
						$currentToken[0] = self::FUNCTION_DEFINITION;

						if ( $this->generateDeprecatedList && in_array( self::FUNCTION_DEPRECATED, $this->mFunctionQualifiers ) ) {
							if ( ( substr( $this->mFunction, 0, 2 ) != "__" ) && $this->mClass != 'Image' ) {
								$this->mDeprecatedFunctionList[] = "/*$this->mClass::*/'$this->mFunction'";
							}
						}

						$this->debug( "Entering into function {$token[1]} at line {$token[2]} " );
						continue;
					}

					$this->error( $token );

				case self::IN_FUNCTION:
					if ( ( $token == ';' ) && ( $this->mBraces == 0 ) ) {
						if ( !in_array( T_ABSTRACT, $this->mFunctionQualifiers ) ) {
							$this->error( $token );
						}
						// abstract function
						$this->mStatus = self::WAITING_FUNCTION;
						continue;
					}
					if ( $token == '{' ) {
						$this->mBraces++;
					} elseif ( $token == '}' ) {
						$this->mBraces--;
						$this->purgeGlobals();
						if ( ! $this->mBraces ) {
							$this->mStatus = self::WAITING_FUNCTION;
							$this->mFunctionQualifiers = array();
						}
					} elseif ( is_array ( $token ) ) {
						if ( $token[0] == T_GLOBAL ) {
							$this->mStatus = self::IN_GLOBAL;
						} elseif ( ( $token[0] == T_CURLY_OPEN ) || ( $token[0] == T_DOLLAR_OPEN_CURLY_BRACES ) ) {
							// {$ and ${ and  All these three end in }, so we need to open an extra brace to balance
							// T_STRING_VARNAME is documented as ${a but it's the text inside the braces
							$this->mBraces++;
						}
						if ( $token[0] == T_STRING_VARNAME ) {
							$token[0] = T_VARIABLE;
							$token[1] = '$' . $token[1];
						}
						if ( $token[0] == T_VARIABLE ) {
							# $this->debug( "Found variable $token[1]" );

							if ( ( $token[1] == '$this' ) && in_array( T_STATIC, $this->mFunctionQualifiers ) ) {
								$this->warning( "Use of \$this in static method function {$this->mFunction} in line $token[2]" );
							}

							if ( $lastMeaningfulToken[0] == T_PAAMAYIM_NEKUDOTAYIM ) {
								/* Class variable. No check for now */
							} else {
								if ( isset( $this->mFunctionGlobals[ $token[1] ] ) ) {
										$this->mFunctionGlobals[ $token[1] ][0] ++;
								} elseif ( $this->shouldBeGlobal( $token[1] ) ) {
									$this->warning( "{$token[1]} is used as local variable in line $token[2], function {$this->mFunction}" );
								}
							}
						} elseif ( $token[0] == T_FUNCTION ) {
							$this->warning( "Uh? Function inside function? A lamda function?" );
							$this->error( $token );
						} elseif ( ( $token[0] == T_PAAMAYIM_NEKUDOTAYIM ) && is_array( $lastMeaningfulToken ) && ( $lastMeaningfulToken[0] == T_VARIABLE ) ) {
							if ( ( $lastMeaningfulToken[1] == '$self' ) || ( $lastMeaningfulToken[1] == '$parent' ) ) {
								# Bug of r69904
								$this->warning( "$lastMeaningfulToken[1]:: used in line $lastMeaningfulToken[2] It probably should have been " . substr( $lastMeaningfulToken[1], 1 ) . "::" );
							}
						} elseif ( ( $token[0] == T_STRING ) && ( is_array( $lastMeaningfulToken )
								&& in_array( $lastMeaningfulToken[0], array( T_OBJECT_OPERATOR, T_PAAMAYIM_NEKUDOTAYIM ) ) ) ) {
							# Class member or class constant
							$currentToken[0] = self::CLASS_MEMBER;
						} elseif ( $token[0] == T_STRING && is_array( $lastMeaningfulToken ) &&
							( in_array( $lastMeaningfulToken[0], array( T_INSTANCEOF, T_NEW ) ) ) ) {

							$this->checkClassName( $token );
							$currentToken[0] = self::CLASS_NAME;
						}
					}

					if ( ( $token == '(' ) && is_array( $lastMeaningfulToken ) ) {
						if ( $lastMeaningfulToken[0] == T_STRING ) {
							$lastMeaningfulToken[0] = self::FUNCTION_NAME;
							$this->checkDeprecation( $lastMeaningfulToken );
						} else if ( $lastMeaningfulToken[0] == self::CLASS_MEMBER ) {
							$this->checkDeprecation( $lastMeaningfulToken );
						}
					}

					/* Detect constants */
					if ( self::isMeaningfulToken( $token ) && is_array( $lastMeaningfulToken ) &&
							( $lastMeaningfulToken[0] == T_STRING ) && !self::isPhpConstant( $lastMeaningfulToken[1] ) ) {

						if ( in_array( $token[0], array( T_PAAMAYIM_NEKUDOTAYIM, T_VARIABLE, T_INSTANCEOF ) ) ) {
							$this->checkClassName( $lastMeaningfulToken );
						} else {

							if ( !defined( $lastMeaningfulToken[1] ) && !in_array( $lastMeaningfulToken[1], $this->mConstants ) && !self::isIgnoreConstant( $lastMeaningfulToken[1] ) ) {
								$this->warning( "Use of undefined constant $lastMeaningfulToken[1] in line $lastMeaningfulToken[2]" );
							}
						}
					}
					continue;

				case self::IN_GLOBAL:
					if ( $token == ',' )
						continue;
					if ( $token == ';' ) {
						$this->mStatus = self::IN_FUNCTION;
						continue;
					}
					if ( !self::isMeaningfulToken( $token ) )
						continue;

					if ( is_array( $token ) ) {
						if ( $token[0] == T_VARIABLE ) {
							if ( !$this->shouldBeGlobal( $token[1] ) && !$this->canBeGlobal( $token[1] ) ) {
								$this->warning( "Global variable {$token[1]} in line $token[2], function {$this->mFunction} does not follow coding conventions" );
							}
							if ( isset( $this->mFunctionGlobals[ $token[1] ] ) ) {
								$this->warning( $token[1] . " marked as global again in line $token[2], function {$this->mFunction}" );
							} else {
								$this->checkGlobalName( $token[1] );
								$this->mFunctionGlobals[ $token[1] ] = array( 0, $this->mBraces, $token[2] );
							}
							continue;
						}
					}
					$this->error( $token );

				case self::IN_INTERFACE:
					if ( $lastMeaningfulToken[0] == T_INTERFACE )
						$this->mKnownFileClasses[] = $token[1];

					if ( $token == '{' ) {
						$this->mBraces++;
					} elseif ( $token == '}' ) {
						$this->mBraces--;
						if ( !$this->mBraces )
							$this->mStatus = self::WAITING_FUNCTION;
					}
					continue;
			}
		}

		$this->checkPendingClasses();
	}

	function checkDeprecation( $token ) {
		global $mwDeprecatedFunctions;
		if ( $mwDeprecatedFunctions && !in_array( self::FUNCTION_DEPRECATED, $this->mFunctionQualifiers ) && in_array( $token[1], $mwDeprecatedFunctions ) ) {
			$this->warning( "Non deprecated function $this->mFunction calls deprecated function {$token[1]} in line {$token[2]}" );
		}
	}

	function error( $token ) {
		$msg = "Unexpected token " . ( is_string( $token ) ? $token : token_name( $token[0] ) ) ;
		if ( is_array( $token ) && isset( $token[2] ) ) {
			$msg .= " in line $token[2]";
		}
		$msg .= "\n";
		$this->warning( $msg );
		die( 1 );
	}

	function warning( $msg ) {
		if ( !$this->mProblemCount ) {
			echo "Problems in {$this->mFilename}:\n";
		}
		$this->mProblemCount++;
		echo " $msg\n";
	}

	function debug( $msg ) {
		if ( $this->mDebug ) {
			echo "$msg\n";
		}
	}

	# Is this the name of a global variable?
	function shouldBeGlobal( $name ) {
		static $specialGlobals = array( '$IP', '$parserMemc', '$messageMemc', '$hackwhere', '$haveProctitle' );
		static $nonGlobals = array(	'$wgOptionalMessages', '$wgIgnoredMessages', '$wgEXIFMessages', # Used by Translate extension, read from maintenance/languages/messageTypes.inc
									'$wgMessageStructure', '$wgBlockComments' ); # Used by Translate extension and maintenance/language/writeMessagesArray.inc, read from maintenance/languages/messages.inc

		return ( ( substr( $name, 0, 3 ) == '$wg' ) || ( substr( $name, 0, 3 ) == '$eg' ) || in_array( $name, $specialGlobals ) ) && !in_array( $name, $nonGlobals );
	}

	# Variables that can be used as global, but also as locals
	function canBeGlobal( $name ) {
		return  $name == '$argv'; /* Used as global by maintenance scripts, but also common as function var */
	}

	private function purgeGlobals() {
		foreach ( $this->mFunctionGlobals as $globalName => $globalData ) {
			if ( $globalData[1] <= $this->mBraces )
				continue; # In scope

			#  global $x  still affects the variable after the endo of the
			# conditional, but only if the condition was true.
			#  We keep in the safe side and only consider it defined inside
			# the if block (see r69883).

			if ( $globalData[0] == 0 ) {
				$this->warning( "Unused global $globalName in function {$this->mFunction} line $globalData[2]" );
			}
			unset( $this->mFunctionGlobals[$globalName] );
		}
	}

	# Look for typos in the globals names
	protected function checkGlobalName( $name ) {
		if ( substr( $name, 0, 3 ) == '$wg' ) {
			if ( ( self::$mDefaultSettingsGlobals != null ) && !in_array( $name, self::$mDefaultSettingsGlobals ) ) {
				$this->warning( "Global variable $name is not present in DefaultSettings" );
			}
		}
	}

	static function isMeaningfulToken( $token ) {
		if ( is_array( $token ) ) {
			return ( $token[0] != T_WHITESPACE && $token[0] != T_COMMENT );
		} else {
			return strpos( '(&', $token ) === false ;
		}
	}

	# Constants defined by php
	static function isPhpConstant( $name ) {
		return in_array( $name, array( 'false', 'true', 'self', 'parent', 'null' ) );
	}

	function checkClassName( $token, $warn = false ) {
		global $wgAutoloadLocalClasses;

		if ( ( $token[1] == 'self' ) || ( $token[1] == 'parent' ) )
			return;

		if ( class_exists( $token[1], false ) ) return; # Provided by an extension
		if ( substr( $token[1], 0, 8 ) == "PHPUnit_" ) return;
		if ( substr( $token[1], 0, 12 ) == "Net_Gearman_" ) return; # phase3/maintenance/gearman/gearman.inc

		if ( !isset( $wgAutoloadLocalClasses[$token[1]] ) && !in_array( $token[1], $this->mKnownFileClasses ) ) {
			if ( $warn ) {
				$this->warning( "Use of unknown class $token[1] in line $token[2]" );
			} else {
				// Defer to the end of the file
				$this->mUnknownClasses[] = $token;
			}
		}
	}

	function checkPendingClasses() {
		foreach ( $this->mUnknownClasses as $classToken ) {
			$this->checkClassName( $classToken, true );
		}
	}

	static function isIgnoreConstant( $name ) {
		foreach ( self::$constantIgnorePrefixes as $prefix ) {
			if ( substr( $name, 0, strlen( $prefix ) ) == $prefix )
				return true;
		}
		return false;
	}
}

$cv = new CheckVars();
// $cv->mDebug = true;

array_shift( $argv );
if ( $argv[0] == '--generate-deprecated-list' ) {
	$cv->setGenerateDeprecatedList( true );
	array_shift( $argv );
}
foreach ( $argv as $arg ) {
	$cv->load( $arg );
	$cv->execute();
}
if ( $cv->getGenerateDeprecatedList( ) ) {
	$cv->saveDeprecatedList( dirname( __FILE__ ) . "/deprecated.functions" );
}
