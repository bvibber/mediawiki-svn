<?php

if ( !defined( 'MEDIAWIKI' ) )
	die( "This is a MediaWiki extension definition file; it is not a valid entry point." );

/**#@+
 * Provides integration with Project Honey Pot for MediaWiki sites.
 * Requires
 * @addtogroup Extensions
 *
 *
 * @author Andrew Garrett <andrew@werdn.us>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$dir = dirname( __FILE__ );
$wgExtensionCredits['other'][] = array(
	'name'           => 'HoneypotIntegration',
	'author'         => 'Andrew Garrett',
	'svn-date'       => '$LastChangedDate$',
	'svn-revision'   => '$LastChangedRevision$',
	'description'    => 'Provides integration with Project Honey Pot for MediaWiki sites',
	'descriptionmsg' => 'honeypot-desc',
);

$wgExtensionMessagesFiles['HoneypotIntegration'] =  "$dir/HoneypotIntegration.i18n.php";
$wgAutoloadClasses[ 'HoneypotIntegration' ] = "$dir/HoneypotIntegration.class.php";

$wgHooks['AbuseFilter-filterAction'][] = 'HoneypotIntegration::onAbuseFilterFilterAction';
$wgHooks['AbuseFilter-builder'][] = 'HoneypotIntegration::onAbuseFilterBuilder';
$wgHooks['EditPage::showEditForm:fields'][] = 'HoneypotIntegration::onShowEditForm';

$wgHoneypotURLs = array( 'http://www.google.com' );
$wgHoneypotTemplates = array(
	'<a href="honeypoturl"><!-- randomtext --></a>',
);