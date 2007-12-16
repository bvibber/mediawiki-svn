<?php

/**
 * Sets up the extension.
 */
	
if (!defined('MEDIAWIKI')) die();

/**
 * Adds two special pages, Special:SignDocument and Special:CreateSignDocument, which
 * enable the creation of signable documents. See the README for more information.
 *
 * @addtogroup Extensions
 *
 * @author Daniel Cannon (AmiDaniel)
 * @copyright Copyright © 2007, Daniel Cannon
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
		 

$wgExtensionFunctions[] = 'wfSpecialSignDocument';
$wgExtensionFunctions[] = 'wfSpecialCreateSignDocument';
$wgExtensionFunctions[] = 'wfCreateSignatureLog';

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'SignDocument',
	'author' => 'Daniel Cannon',
	'description' => 'Enables document signing',
);

/* Set up sigadmin permissions. */
$wgGroupPermissions['sigadmin']['sigadmin'] = true;
$wgGroupPermissions['sigadmin']['createsigndocument'] = true;

/**
 * Register Special:SignDocument
 */
function wfSpecialSignDocument() {
	global $IP, $wgMessageCache;

	$GLOBALS['wgAutoloadClasses']['SignDocument'] = dirname( __FILE__ ) .
					        '/SpecialSignDocument.php';

	$GLOBALS['wgSpecialPages']['signdocument'] = array( /*class*/ 'SignDocument',
			/*name*/ 'signdocument', /* permission */'', /*listed*/ true,
			/*function*/ false, /*file*/ false );
													
}

/**
 * Register Special:CreateSignDocument
 */
function wfSpecialCreateSignDocument() {
	# Register special page
	if ( !function_exists( 'extAddSpecialPage' ) ) {
		require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
	}
	extAddSpecialPage( dirname(__FILE__) . '/SpecialCreateSignDocument.php', 
			'createsigndocument', 'CreateSignDocument' );
}

/**
 * Create the Signature log.
 */
function wfCreateSignatureLog() {
	require_once( 'SignDocument.i18n.php' );

	# Add messages
	global $wgMessageCache, $wgNewuserlogMessages;

	foreach( $allMessages as $key => $value ) {
		$wgMessageCache->addMessages( $value, $key );
	}
	
	# Add a new log type
	global $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions;

	$wgLogTypes[]                      = 'signature';
	$wgLogNames['signature']           = 'signaturelogpage';
	$wgLogHeaders['signature']         = 'signaturelogpagetext';
	$wgLogActions['signature/sign']    = 'signaturelogentry';

}

/**
 * Logs the addition of a signature to a document. If it's an anonymous user,
 * it will add it to the logging table but the entry won't display on Special:Log.
 * Currently trying to work out a good way to "fix" this.
 */
function wfLogSignDocumentSignature( $sig ) {
	global $wgUser;
	$log = new LogPage( 'signature' );
	$log->addEntry( 'sign', Title::newFromId( $sig->mForm->getPageId() ), 
		'id=' . $sig->mId );

}
?>
