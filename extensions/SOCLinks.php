<?
if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This file is a MediaWiki extension it is not a valid entry point.\n";
	exit( 1 );
}
/**
 * Makes discussion tabs point to the LQT pages.
 *
 * @author Charlie Huggard 
 * @copyright Copyright © 2006, Charlie Huggard
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionCredits['other'][] = array(
        'name' => 'SOCLinks',
 'description' => 'Fixes discussion links on the main namespace to point to threads. Based on HideLinks',
      'author' => 'Charlie Huggard',
         'url' => "http://cirl.missouri.edu/wiki/HideLinks"
);


$wgExtensionFunctions[] = "efSOCLinks";

function DiscTalkLinks (&$content_actions) {
	global $wgTitle;
	if($wgTitle->getNamespace()==NS_MAIN) {
		$discTitle = Title::newFromText( $wgTitle->getText(), 98 ); 
		$content_actions['talk']['href'] = $discTitle->getLocalUrl( '' );
		$content_actions['talk']['class']= implode( ' ', array() );
	}

	return true;
}

function efSOCLinks () {
   global $wgHooks;
   $wgHooks['SkinTemplateContentActions'][] = 'DiscTalkLinks';
}

?>
