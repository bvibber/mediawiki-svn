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

/**
 * A file for the WhiteList extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Paul Grinberg <gri6507@yahoo.com>
 * @author Mike Sullivan <ms-mediawiki@umich.edu>
 * @copyright Copyright © 2008, Paul Grinberg, Mike Sullivan
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$allMessages = array();

/** English
 * @author Paul Grinberg <gri6507@yahoo.com>
 * @author Mike Sullivan <ms-mediawiki@umich.edu>
 */
$allMessages['en'] = array(
	'whitelist-desc'              => 'Edit the access permissions of restricted users',
	'whitelistedit'               => 'Whitelist Access Editor',
	'whitelist'                   => 'Whitelist Pages',
	'mywhitelistpages'            => 'My Pages',
	'whitelistfor'                => "<center>Current information for <b>$1<b></center>",
	'whitelisttablemodify'        => 'Modify',
	'whitelisttablemodifyall'     => 'All',
	'whitelisttablemodifynone'    => 'None',
	'whitelisttablepage'          => 'Wiki Page',
	'whitelisttabletype'          => 'Access Type',
	'whitelisttableexpires'       => 'Expires On',
	'whitelisttablemodby'         => 'Last modified By',
	'whitelisttablemodon'         => 'Last modified On',
	'whitelisttableedit'          => 'Edit',
	'whitelisttableview'          => 'View',
	'whitelisttablenewdate'       => 'New Date:',
	'whitelisttablechangedate'    => 'Change Expiry Date',
	'whitelisttablesetedit'       => 'Set to Edit',
	'whitelisttablesetview'       => 'Set to View',
	'whitelisttableremove'        => 'Remove',
	'whitelistnewpagesfor'        => "Add new pages to <b>$1's</b> white list<br />
Use either * or % as wildcard character<br />",
	'whitelistnewtabledate'       => 'Expiry Date:',
	'whitelistnewtableedit'       => 'Set to Edit',
	'whitelistnewtableview'       => 'Set to View',
	'whitelistnewtableprocess'    => 'Process',
	'whitelistnewtablereview'     => 'Review',
	'whitelistselectrestricted'   => '== Select Restricted User Name ==',
	'whitelistpagelist'           => "{{SITENAME}} pages for $1",
	'whitelistnocalendar'         => "<font color='red' size=3>It looks like [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], a prerequisite for this extension, was not installed properly!</font>",
	'whitelistbadtitle'           => 'Bad title - ',
	'whitelistoverview'           => "== Overview of changes for $1 ==",
	'whitelistoverviewcd'         => "* Changing date to '''$1''' for [[:$2|$2]]",
	'whitelistoverviewsa'         => "* Setting access to '''$1''' for [[:$2|$2]]",
	'whitelistoverviewrm'         => "* Removing access to [[:$1|$1]]",
	'whitelistoverviewna'         => "* Adding [[:$1|$1]] to whitelist with access '''$2''' and '''$3''' expiry date",
	'whitelistrequest'            => "Request access to more pages",
	'whitelistrequestmsg'         => "$1 has requested access to the following pages:\n\n$2",
	'whitelistrequestconf'        => "Request for new pages was sent to $1",
	'whitelistnonrestricted'      => "User '''$1''' is not a restricted user.
This page is only applicable to restricted users",
	'whitelistnever'              => 'never',
	'whitelistnummatches'         => " - $1 matches",
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$allMessages['nl'] = array(
	'whitelist-desc'           => 'Toegangsrechten voor gebruikers met beperkte rechten bewerken',
	'mywhitelistpages'         => "Mijn pagina's",
	'whitelisttablemodify'     => 'Bewerken',
	'whitelisttablemodifyall'  => 'Alle',
	'whitelisttablemodifynone' => 'Geen',
	'whitelisttablepage'       => 'Wikipagina',
	'whitelisttabletype'       => 'Toegangstype',
	'whitelisttableexpires'    => 'Verloopt op',
	'whitelisttablemodby'      => 'Laatste bewerking door',
	'whitelisttablemodon'      => 'Laatste wijziging op',
	'whitelisttableedit'       => 'Bewerken',
	'whitelisttableview'       => 'Bekijken',
	'whitelisttablenewdate'    => 'Nieuwe datum:',
	'whitelisttablechangedate' => 'Verloopdatum bewerken',
	'whitelisttablesetedit'    => 'Op bewerken instellen',
	'whitelisttablesetview'    => 'Op bekijken instellen',
	'whitelisttableremove'     => 'Verwijderen',
	'whitelistnewtabledate'    => 'Verloopdatum:',
	'whitelistnewtableedit'    => 'Op bewerken instellen',
	'whitelistnewtableview'    => 'Op bekijken instellen',
	'whitelistnewtableprocess' => 'Verwerken',
	'whitelistnever'           => 'nooit',
);

/** Swedish (Svenska)
 * @author M.M.S.
 */
$allMessages['sv'] = array(
	'mywhitelistpages'        => 'Mina sidor',
	'whitelistfor'            => '<center>Nuvarande information för <b>$1<b></center>',
	'whitelisttablemodifyall' => 'Alla',
);

