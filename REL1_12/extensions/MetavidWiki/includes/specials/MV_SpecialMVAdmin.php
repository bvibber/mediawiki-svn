<?php
/*
 * MV_SpecialMVAdmin.php Created on Apr 24, 2007
 *
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 * 
 * This special page for MediaWiki provides an administrative interface 
 * that allows to execute certain functions related to the maintenance 
 * of the metavid database. It is restricted to users with siteadmin status.
 */

if (!defined('MEDIAWIKI')) die();

// TODO: should these be messages?
global $wgMessageCache;
$wgMessageCache->addMessages(array('mvadmin' => 'Admin functions for Semantic MediaWiki'));

SpecialPage::addPage( new SpecialPage('MVAdmin','delete',true,'doSpecialMVAdmin',false) );

function doSpecialMVAdmin($par = null) {
	global $IP, $smwgIP;
	//require_once($smwgIP . '/includes/SMW_Storage.php');
	require_once($IP . '/includes/SpecialPage.php' );
	require_once($IP . '/includes/Title.php' );

	global $wgOut, $wgRequest;
	global $wgServer; // "http://www.yourserver.org"
						// (should be equal to 'http://'.$_SERVER['SERVER_NAME'])
	global $wgScript;   // "/subdirectory/of/wiki/index.php"
	global $wgUser;

	if ( ! $wgUser->isAllowed('delete') ) {
		$wgOut->sysopRequired();
		return;
	}

	/**** Execute actions if any ****/

	$action = $wgRequest->getText( 'action' );
	$message='';
	if ( $action=='updatetables' ) {
		$sure = $wgRequest->getText( 'udsure' );
		if ($sure == 'yes') {
			//@@todo install metavid index tables
			//$message = smwfGetStore()->setup();
			if ($message === true) {
				$message  = 'The database was set up successfully.';
			}
		}
	}

	/**** Output ****/

	$wgOut->setPageTitle(wfMsg('smwadmin'));

	// only report success/failure after an action
	if ( $message!='' ) {
		$html = $message;
		$html .= '<p> Return to <a href="' . $wgServer . $wgScript . '/Special:SMWAdmin">Special:SMWAdmin</p>';
		$wgOut->addHTML($html);
		return true;
	}

	$html = '<p>This special page helps you during installation and upgrade of 
				Metavid MediaWiki. Remember to backup valuable data before 
				executing administrative functions.</p>' . "\n";
	// creating tables and converting contents from older versions
	//@@TODO still needs work ;P
	$html .= '<form name="buildtables" action="" method="POST">' . "\n" .
			'<input type="hidden" name="action" value="updatetables" />' . "\n";
	$html .= '<h2>Preparing database for Semantic MediaWiki</h2>' . "\n" .
			'<p>Metavid MediaWiki requires some minor extensions to the MediaWiki database in 
			order to store indexed media data. The below function ensures that your database is
			set up properly. The changes made in this step do not affect the rest of the 
			MediaWiki database, and can easily be undone if desired. This setup function
			can be executed multiple times without doing any harm, but it is needed only once on
			installation or upgrade.<p/>' . "\n";
	$html .= '<p>If the operation fails with obscure SQL errors, the database user employed 
			by your wiki (check your LocalSettings.php) probably does not have sufficient 
			permissions. Either grant this user additional permissions to create and delete 
			tables, or temporarily enter the login of your database root in LocalSettings.php.<p/>' .
			"\n" . '<input type="hidden" name="udsure" value="yes"/>' .
			'<input type="submit" value="Initialize or upgrade tables"/>' . "\n";

	$html .= '<h2>Getting support</h2>' . "\n" . 
	         '<p>Various resources might help you in case of problems:</p>
	         <ul>
	           <li> If you experience problems with your installation, start by checking the guidelines in the <a href="http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticMediaWiki/INSTALL">INSTALL file</a>.</li>
	           <li>The complete user documentation to Semantic MediaWiki is at <a href="http://ontoworld.org/wiki/Help:Semantics">ontoworld.org</a>.</li>
	           <li>Bugs can be reported to <a href="http://bugzilla.wikimedia.org/">MediaZilla</a>.</li>
	           <li>If you have further questions or suggestions, join the discussion on <a href="mailto:semediawiki-user@lists.sourceforge.net">semediawiki-user@lists.sourceforge.net</a>.</li>
	           <ul/>' . "\n";
	$html .= '</form>';
	
	$wgOut->addHTML($html);
	return true;
}

?>

