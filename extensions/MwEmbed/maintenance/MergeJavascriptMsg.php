<?php
/**
* Merges in JavaScript json msgs into respective module i18n.php file
*
* If your script uses JSON msg string with the This script helps merge msgs from javascript to php
*
*
* @file
* @ingroup Maintenance
*/

# Abort if called from a web server
if ( isset( $_SERVER ) && array_key_exists( 'REQUEST_METHOD', $_SERVER ) ) {
	print "This script must be run from the command line\n";
	exit();
}
// Change to the core maintenance script directory
require_once( dirname( __FILE__ ) . '/../../../maintenance/Maintenance.php' );


class MergeJavascriptMsg extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Merge Javascript JSON English messages to PHP";
	}

	public function execute() {
		global $wgExtensionJavascriptModules, $IP;

		foreach ( $wgExtensionJavascriptModules as $moduleName => $modulePath ){

			$i18nFilePath = false;
			$moduleAbsoultePath = $IP ."/". $modulePath;

			// Look for the i18n.php file next to the loader
			if ( $handle = opendir( $moduleAbsoultePath  ) ) {
			    // Look for the i18n.php file
			    while (false !== ($file = readdir($handle))) {
			        if( substr( $file, -8 ) == 'i18n.php' ){
			        	$i18nFilePath = $moduleAbsoultePath .'/'. $file;
			        }
			    }
			    if( ! $i18nFilePath ) {
					$this->error( "Could not find i18n file in directory: $moduleAbsoultePath \n" );
					continue;
			    }

			} else {
				$this->error( "Could not read path: $moduleAbsoultePath \n" );
				continue;
			}

			// Clear the local message var
			$this->messages = array();

			// Load up the messages for the i18n file
			require_once( $i18nFilePath );

			//Update the local messages var
			$this->messages = $messages['en'];

			// Recurse on every file in the module directory
			$objects = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $moduleAbsoultePath ), RecursiveIteratorIterator::SELF_FIRST );
			foreach ( $objects as $fname => $object ) {
				$this->processFilePath( $fname );
			}

			// Merge the msg back into the module php file
			$this->mergeModuleMsg( $i18nFilePath );
		}
	}
	/**
	* mergeModuleMsg
	*/
	function mergeModuleMsg( $i18nFilePath ) {
		$mwSTART_MSG_KEY = '$messages[\'en\'] = array(';
		$mwEND_MSG_KEY = ',
);';

		$rawLangFile = file_get_contents( $i18nFilePath );
		$startInx = strpos( $rawLangFile, $mwSTART_MSG_KEY ) + strlen( $mwSTART_MSG_KEY );
		$endInx = strpos( $rawLangFile, $mwEND_MSG_KEY ) + 1;
		if ( $startInx === false || $endInx === false ) {
			$this->error( "Could not find $mwSTART_MSG_KEY or $mwEND_MSG_KEY in mwEmbed.i18n.php\n" );
		}
		$preFile = substr( $rawLangFile, 0, $startInx );
		$postFile = substr( $rawLangFile, $endInx );

		$outPhp="\n";
		foreach($this->messages as $msgKey => $msgValue ){
			$outPhp .= "\t'{$msgKey}' => '" . str_replace( '\'', '\\\'', $msgValue ) . "',\n";
		}
		// update the file and report progress
		file_put_contents( $i18nFilePath, $preFile . "\n\t". trim( $outPhp ) . $postFile );
		$this->output( "Updated File: " . $i18nFilePath . "\n" );
	}

	/**
	 * Process a file path for msgs
	 */
	function processFilePath( $fname ){
		// Only work on javascript files:
		if ( substr( $fname, - 3 ) != '.js' ){
			return false;
		}
		$jsFileText = file_get_contents( $fname );
		// Grab all the javascript msgs
		if ( preg_match( '/mw\.addMessages\s*\(\s*{(.*)}\s*\)\s*/siU',
			$jsFileText,
			$matches ) )
		{
			$jsMsgs = json_decode( '{' . $matches[1] . '}', true );
			// Merge into the English  $messages var
			if( $jsMsgs ){
				foreach( $jsMsgs as $msgKey => $msgValue ){
					if( isset( $this->messages[$msgKey] ) &&
					 	$this->messages[$msgKey] != $msgValue
					 ) {
						$this->output( "Warning:: $msgKey does not match \nphp:" .
							$this->messages[$msgKey] . "\n != \njs:" . $msgValue . "\n\n");

						// NOTE we could force merge from js here
					} else {
						$this->messages[ $msgKey ] = $msgValue;
					}
				}
			} else {
				$this->output( "Error could not decode json in $fname \n");
			}
		} else {
			// skip file no msgs
		}
		// Check against module messages
	}
}

$maintClass = "MergeJavascriptMsg";
require_once( DO_MAINTENANCE );
