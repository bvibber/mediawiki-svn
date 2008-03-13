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

/** Bulgarian (Български)
 * @author DCLXVI
 */
$allMessages['bg'] = array(
	'mywhitelistpages'        => 'Моите страници',
	'whitelistfor'            => '<center>Текуща информация за <b>$1</b></center>',
	'whitelisttablemodifyall' => 'Всички',
	'whitelisttableedit'      => 'Редактиране',
	'whitelisttableview'      => 'Преглед',
	'whitelisttableremove'    => 'Премахване',
	'whitelistbadtitle'       => 'Грешно заглавие -',
	'whitelistnever'          => 'никога',
	'whitelistnummatches'     => ' - $1 съвпадения',
);

/** French (Français)
 * @author Grondin
 */
$allMessages['fr'] = array(
	'whitelist-desc'            => 'Modifie les permission d’accès des utilisateurs à pouvoirs restreints',
	'whitelistedit'             => 'Éditeur de la liste blanche des accès',
	'whitelist'                 => 'Pages de listes blanches',
	'mywhitelistpages'          => 'Mes pages',
	'whitelistfor'              => '<center>Informations actuelles pour <b>$1</b></center>',
	'whitelisttablemodify'      => 'Modifier',
	'whitelisttablemodifyall'   => 'Tout',
	'whitelisttablemodifynone'  => 'Néant',
	'whitelisttablepage'        => 'Page wiki',
	'whitelisttabletype'        => 'Mode d’accès',
	'whitelisttableexpires'     => 'Expire le',
	'whitelisttablemodby'       => 'Modifié en dernier par',
	'whitelisttablemodon'       => 'Modifié en dernier le',
	'whitelisttableedit'        => 'Modifier',
	'whitelisttableview'        => 'Afficher',
	'whitelisttablenewdate'     => 'Nouvelle date :',
	'whitelisttablechangedate'  => 'Changer la date d’expiration',
	'whitelisttablesetedit'     => 'Paramètres pour l’édition',
	'whitelisttablesetview'     => 'Paramètres pour visionner',
	'whitelisttableremove'      => 'Retirer',
	'whitelistnewpagesfor'      => 'Ajoute de nouvelles pages à la liste blanche de <b>$1</b><br />
Utiliser soit le caractère * soit %<br />',
	'whitelistnewtabledate'     => 'Date d’expiration :',
	'whitelistnewtableedit'     => 'Paramètres d‘édition',
	'whitelistnewtableview'     => 'Paramètres pour visionner',
	'whitelistnewtableprocess'  => 'Traiter',
	'whitelistnewtablereview'   => 'Réviser',
	'whitelistselectrestricted' => '== Sélectionner un nom d’utilisateur à accès restreint ==',
	'whitelistpagelist'         => 'Pages de {{SITENAME}} pour $1',
	'whitelistnocalendar'       => "<font color='red' size=3>Il semble que le module [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], une extension prérequise, n’ait pas été installée convenablement !</font>",
	'whitelistbadtitle'         => 'Titre incorrect ‑',
	'whitelistoverview'         => '== Vue générale des changements pour $1 ==',
	'whitelistoverviewcd'       => "Modification de la date de '''$1''' pour [[:$2|$2]]",
	'whitelistoverviewsa'       => "* configurer l'accès de '''$1''' pour [[:$2|$2]]",
	'whitelistoverviewrm'       => '* Retrait de l’accès à [[:$1|$1]]',
	'whitelistoverviewna'       => "* Ajoute [[:$1|$1]] à la liste blanche avec les droits de '''$2''' avec pour date d’expiration le '''$3'''",
	'whitelistrequest'          => 'Demande d’accès à plus de pages',
	'whitelistrequestmsg'       => '$1 a demandé l’accès aux pages suivantes :

$2',
	'whitelistrequestconf'      => 'Une demande d’accès pour de nouvelles pages a été envoyée à $1',
	'whitelistnonrestricted'    => "L'utilisateur  '''$1''' n’est pas avec des droit restreints.
Cette page ne s’applique qu’aux utilisateurs disposant de droits restreints.",
	'whitelistnever'            => 'jamais',
	'whitelistnummatches'       => ' - $1 {{PLURAL:$1|occurence|occurences}}',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$allMessages['nl'] = array(
	'whitelist-desc'            => 'Toegangsrechten voor gebruikers met beperkte rechten bewerken',
	'whitelistedit'             => 'Toegang via witte lijst',
	'whitelist'                 => "Pagina's op de witte lijst",
	'mywhitelistpages'          => "Mijn pagina's",
	'whitelistfor'              => '<center>Huidige informatie voor <b>$1<b></center>',
	'whitelisttablemodify'      => 'Bewerken',
	'whitelisttablemodifyall'   => 'Alle',
	'whitelisttablemodifynone'  => 'Geen',
	'whitelisttablepage'        => 'Wikipagina',
	'whitelisttabletype'        => 'Toegangstype',
	'whitelisttableexpires'     => 'Verloopt op',
	'whitelisttablemodby'       => 'Laatste bewerking door',
	'whitelisttablemodon'       => 'Laatste wijziging op',
	'whitelisttableedit'        => 'Bewerken',
	'whitelisttableview'        => 'Bekijken',
	'whitelisttablenewdate'     => 'Nieuwe datum:',
	'whitelisttablechangedate'  => 'Verloopdatum bewerken',
	'whitelisttablesetedit'     => 'Op bewerken instellen',
	'whitelisttablesetview'     => 'Op bekijken instellen',
	'whitelisttableremove'      => 'Verwijderen',
	'whitelistnewpagesfor'      => "Nieuwe pagina's aan de witte lijst voor <b>$1</b> toevoegen<br />
Gebruik * of % als wildcard<br />",
	'whitelistnewtabledate'     => 'Verloopdatum:',
	'whitelistnewtableedit'     => 'Op bewerken instellen',
	'whitelistnewtableview'     => 'Op bekijken instellen',
	'whitelistnewtableprocess'  => 'Verwerken',
	'whitelistnewtablereview'   => 'Controleren',
	'whitelistselectrestricted' => '== Gebruiker met beperkingen selecteren ==',
	'whitelistpagelist'         => "{{SITENAME}} pagina's voor $1",
	'whitelistnocalendar'       => "<font color='red' size=3>[http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], een voorwaarde voor deze uitbreiding, lijkt niet juist geïnstalleerd!</font>",
	'whitelistbadtitle'         => 'Onjuiste naam -',
	'whitelistoverview'         => '== Overzicht van wijzigingen voor $1 ==',
	'whitelistoverviewcd'       => "* verloopdatum gewijzigd naar '''$1''' voor [[:$2|$2]]",
	'whitelistoverviewsa'       => "* toegangstype '''$1''' ingesteld voor [[:$2|$2]]",
	'whitelistoverviewrm'       => '* toegang voor [[:$1|$1]] wordt verwijderd',
	'whitelistoverviewna'       => "* [[:$1|$1]] wordt toegevoegd aan de witte lijst met toegangstype '''$1''' en verloopdatum '''$3'''",
	'whitelistrequest'          => "Toegang tot meer pagina's vragen",
	'whitelistrequestmsg'       => "$1 heeft toegang gevraagd tot de volgende pagina's:

$2",
	'whitelistrequestconf'      => "Het verzoek voor nieuwe pagina's is verzonden naar $1",
	'whitelistnonrestricted'    => "Gebruiker '''$1''' is geen gebruiker met beperkte rechten.
Deze pagina is alleen van toepassing op gebruikers met beperkte rechten.",
	'whitelistnever'            => 'nooit',
	'whitelistnummatches'       => '- $1 resultaten',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$allMessages['no'] = array(
	'mywhitelistpages'         => 'Mine sider',
	'whitelisttablemodify'     => 'Endre',
	'whitelisttablemodifyall'  => 'Alle',
	'whitelisttablemodifynone' => 'Ingen',
	'whitelisttablepage'       => 'Wikiside',
	'whitelisttabletype'       => 'Tilgangstype',
	'whitelisttableexpires'    => 'Utgår',
	'whitelistnever'           => 'aldri',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$allMessages['oc'] = array(
	'mywhitelistpages' => 'Mas paginas',
);

/** Swedish (Svenska)
 * @author M.M.S.
 */
$allMessages['sv'] = array(
	'mywhitelistpages'        => 'Mina sidor',
	'whitelistfor'            => '<center>Nuvarande information för <b>$1<b></center>',
	'whitelisttablemodifyall' => 'Alla',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$allMessages['te'] = array(
	'mywhitelistpages'        => 'నా పేజీలు',
	'whitelisttablemodifyall' => 'అన్నీ',
	'whitelisttablepage'      => 'వికీ పేజీ',
	'whitelisttablenewdate'   => 'కొత్త తేదీ:',
	'whitelisttableremove'    => 'తొలగించు',
	'whitelistnewtablereview' => 'సమీక్షించు',
	'whitelistnummatches'     => '  - $1 పోలికలు',
);

