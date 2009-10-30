<?php
/**
 * A Special Page extension to retrieve and display a page
 * from a specified external WMF site, with optional year, 
 * project
 * and language parameters
 *
 * @addtogroup Extensions
 *
 * @author Ariel Glenn <ariel@wikimedia.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 3.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
To install the ExternalPages extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/SpecialExternalPages.php" );
EOT;
	exit(1);
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'ExternalPages',
	'version' => '0.1',
	'author' => 'Ariel Glenn',
	'url' => 'http://www.mediawiki.org/wiki/Extension:ExternalPages',
	'description' => 'Retrieve and display page from a remote WMF site',
	'descriptionmsg' => 'externalpages-desc',
);

$dir = dirname( __FILE__ ) . '/';
						   
$wgExtensionMessagesFiles['ExternalPages'] = $dir . 'ExternalPages.i18n.php';
$wgExtensionAliasesFiles['ExternalPages'] = $dir . 'ExternalPages.alias.php';

$wgAutoloadClasses['ExternalPages'] = $dir . 'ExternalPages_body.php';

$wgSpecialPages['ExternalPages'] = 'ExternalPages';
$wgSpecialPageGroups['ExternalPages'] = 'users';
$wgHooks['LanguageGetSpecialPageAliases'][] = 'externalPagesLocalizedPageName';

function externalPagesLocalizedPageName( &$specialPageArray, $code ) {
	wfLoadExtensionMessages( 'ExternalPages' );
	$text = wfMsg( 'externalpages' );
 
	# Convert from title in text form to DBKey and put it into the alias array:
	$title = Title::newFromText( $text );
	$specialPageArray['ExternalPages'][] = $title->getDBKey();
	return true;
}

?>
