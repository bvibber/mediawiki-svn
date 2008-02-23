<?php

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation, version 2
of the License.
 
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if (!defined('MEDIAWIKI')) die();
/**
 * A Special Page extension to manage contractor access white lists
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Paul Grinberg <gri6507@yahoo.com>
 * @author Mike Sullivan <ms-mediawiki@umich.edu>
 * @copyright Copyright © 2008, Paul Grinberg, Mike Sullivan
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionCredits['specialpage'][] = array(
        'name'        => 'WhiteListEdit',
        'version'     => 'v0.7.1',
        'author'      => 'Paul Grinberg, Mike Sullivan',
        'email'       => 'gri6507 at yahoo dot com, ms-mediawiki AT umich DOT edu',
        'description' => 'Edit the access permissions of restricted users',
        'url'         => 'http://www.mediawiki.org/wiki/Extension:WhiteList',
);

# these are the groups and the rights used within this extension
$wgWhiteListRestrictedGroup = 'restricted';
$wgWhiteListManagerGroup = 'manager';
$wgWhiteListRestrictedRight = 'restricttowhitelist';
$wgWhiteListManagerRight = 'editwhitelist';

# Define groups and rights
$wgGroupPermissions['*']['usewhitelist'] = false;
$wgGroupPermissions[$wgWhiteListRestrictedGroup]['edit'] = true;
$wgGroupPermissions[$wgWhiteListRestrictedGroup][$wgWhiteListRestrictedRight] = true;
$wgGroupPermissions['*'][$wgWhiteListManagerRight] = false;
$wgGroupPermissions[$wgWhiteListManagerGroup][$wgWhiteListManagerRight] = true;

# This extension requires the Extension:Usage_Statistics
# NOTE: you don't actually need the gnuplot extension for the functinoality needed by this extension
#require_once(dirname(__FILE__) . '/SpecialUserStats.php');
require_once(dirname(__FILE__) . '/WhitelistAuth.php');
$wgAutoloadClasses['WhitelistEdit'] = dirname(__FILE__) . '/SpecialWhitelistEdit_body.php';
$wgSpecialPages['WhitelistEdit'] = 'WhitelistEdit';
$wgHooks['LoadAllMessages'][] = 'WhitelistEdit::loadMessages';
$wgSpecialPages['WhiteList'] = 'WhiteList';
$wgHooks['LoadAllMessages'][] = 'WhiteList::loadMessages';

require_once(dirname(__FILE__) . '/SpecialWhitelistEdit_body.php');

$wgHooks['PersonalUrls'][] = 'wfAddRestrictedPagesTab';
$wgHooks['userCan'][] = 'wfCheckWhitelist';

?>
