<?php
/**
 * WebIRC
 *
 * make a web irc client in to a special page
 *
 * @link http://www.mediawiki.org/wiki/Extension:WebIRC
 *
 * @author Devunt <devunt@devunt.kr>
 * @authorlink http://www.mediawiki.org/wiki/User:Devunt
 * @copyright Copyright © 2010 Devunt (Bae June Hyeon).
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
 
// If this is run directly from the web die as this is not a valid entry point.
if ( !defined( 'MEDIAWIKI' ) ) die('define error!');
 
// Extension credits.
$wgExtensionCredits[ 'specialpage' ][] = array(
    'name'           => 'WebIRC',
    'author'         => 'Devunt (Bae June Hyeon)',
    'url'            => 'http://www.mediawiki.org/wiki/Extension:WebIRC',
    'description'    => 'webirc-desc',
    'descriptionmsg' => 'webirc-desc',
    'version'        => '0.7.1',
);
 
$dir = dirname( __FILE__ ) . '/';
 
$wgSpecialPages['WebIRC'] = 'WebIRC';
$wgSpecialPageGroups['WebIRC'] = 'wiki';
$wgAutoloadClasses['WebIRC'] = $dir.'WebIRC_body.php';
$wgExtensionMessagesFiles['WebIRC'] =  $dir.'WebIRC.i18n.php';
$wgExtensionAliasesFiles['WebIRC'] =  $dir.'WebIRC.alias.php';
 
$wgAvailableRights[] = 'webirc';
