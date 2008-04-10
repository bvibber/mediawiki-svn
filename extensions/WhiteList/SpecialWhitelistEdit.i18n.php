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
	'whitelistfor'                => "<center>Current information for <b>$1</b></center>",
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
Use either * or % as wildcard character",
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
	'whitelistrequestmsg'         => "$1 has requested access to the following pages:

$2",
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
	'mywhitelistpages'         => 'Моите страници',
	'whitelistfor'             => '<center>Текуща информация за <b>$1</b></center>',
	'whitelisttablemodify'     => 'Промяна',
	'whitelisttablemodifyall'  => 'Всички',
	'whitelisttablemodifynone' => 'Няма',
	'whitelisttableexpires'    => 'Изтича на',
	'whitelisttablemodby'      => 'Последна промяна от',
	'whitelisttablemodon'      => 'Последна промяна на',
	'whitelisttableedit'       => 'Редактиране',
	'whitelisttableview'       => 'Преглед',
	'whitelisttablenewdate'    => 'Нова дата:',
	'whitelisttableremove'     => 'Премахване',
	'whitelistpagelist'        => 'Страници за $1 в {{SITENAME}}',
	'whitelistbadtitle'        => 'Грешно заглавие -',
	'whitelistoverviewcd'      => "* Промяна на датата за [[:$2|$2]] на '''$1'''",
	'whitelistoverviewrm'      => '* Премахване на достъпа до [[:$1|$1]]',
	'whitelistrequest'         => 'Поискване на достъп до още страници',
	'whitelistrequestmsg'      => '$1 пожела достъп до следните страници:

$2',
	'whitelistrequestconf'     => 'Заявка за нови страници беше изпратена на $1',
	'whitelistnever'           => 'никога',
	'whitelistnummatches'      => ' - $1 съвпадения',
);

/** Catalan (Català)
 * @author SMP
 */
$allMessages['ca'] = array(
	'whitelisttableedit' => 'Edita',
);

/** Welsh (Cymraeg)
 * @author Lloffiwr
 */
$allMessages['cy'] = array(
	'whitelisttablemodifyall' => 'Oll',
	'whitelisttableedit'      => 'Golygu',
);

/** Danish (Dansk)
 * @author Jon Harald Søby
 */
$allMessages['da'] = array(
	'whitelisttablemodifynone' => 'Ingen',
	'whitelisttableedit'       => 'Redigér',
	'whitelistnever'           => 'aldrig',
);

/** German (Deutsch)
 * @author Liam Rosen
 */
$allMessages['de'] = array(
    'whitelist-desc'              => 'Zugriffrechte von beschr�kten Benutzern bearbeiten',
    'whitelistedit'               => 'Whitelist Zugriff Editor',
    'whitelist'                   => 'Whitelist Seiten',
    'mywhitelistpages'            => 'Meine Seiten',
    'whitelistfor'                => "<center>Aktuelle Information fr <b>$1</b></center>",
    'whitelisttablemodify'        => 'Modifizieren',
    'whitelisttablemodifyall'     => 'Alles modifizieren',
    'whitelisttablemodifynone'    => 'Nichts modifizieren',
    'whitelisttablepage'          => 'Seite',
    'whitelisttabletype'          => 'Zugriff Typ',
    'whitelisttableexpires'       => 'Abl�ft am',
    'whitelisttablemodby'         => 'Zuletz modifiziert von',
    'whitelisttablemodon'         => 'Zuletzt modifiziert am',
    'whitelisttableedit'          => 'Beiarbeiten',
    'whitelisttableview'          => 'Anschauen',
    'whitelisttablenewdate'       => 'Neues Datum:',
    'whitelisttablechangedate'    => 'Ablaufsdatum �dern',
    'whitelisttablesetedit'       => 'Beiarbeiten',
    'whitelisttablesetview'       => 'Anschauen',
    'whitelisttableremove'        => 'Entfernen',
    'whitelistnewpagesfor'        => "Neue Seiten zu <b>$1's</b> white list hinzufgen<br />
Entweder * oder % als Maskenzeichen benutzen",
    'whitelistnewtabledate'       => 'Ablaufsdatum:',
    'whitelistnewtableedit'       => 'Bearbeiten',
    'whitelistnewtableview'       => 'Anschauen',
    'whitelistnewtableprocess'    => 'Bearbeiten',
    'whitelistnewtablereview'     => '�erprfen',
    'whitelistselectrestricted'   => '== Beschr�kter Benutzername selektieren ==',
    'whitelistpagelist'           => "{{SITENAME}} Seiten fr $1",
    'whitelistnocalendar'         => "<font color='red' size=3>[http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], eine Vorraussetzung fr dieses Extension, wurde nicht installiert oder kann nicht gefunden werden!</font>",
    'whitelistbadtitle'           => 'Titel inkompatibel - ',
    'whitelistoverview'           => "== �derungsbersicht fr $1 ==",
    'whitelistoverviewcd'         => "* Datum ('''$1''') fr [[:$2|$2]] wird ge�dert",
    'whitelistoverviewsa'         => "* Zugriff '''$1''' fr [[:$2|$2]] wird angewendet",
    'whitelistoverviewrm'         => "* Zugriff auf [[:$1|$1]] wird entfernt",
    'whitelistoverviewna'         => "* Adding [[:$1|$1]] to whitelist with access '''$2''' and '''$3''' expiry date",
    'whitelistrequest'            => "Weiterer Zugriff beantragen",
    'whitelistrequestmsg'         => "$1 hat Zugriff auf die folgenden Seiten beantragt:
$2",
    'whitelistrequestconf'        => "Beantragung an $1 geschickt",
    'whitelistnonrestricted'      => "'''$1''' ist nicht ein beschr�kter Benutzer.
Diese Seite gilt nur fr beschr�kte Bentzer.",
    'whitelistnever'              => 'niemals',
    'whitelistnummatches'         => " - $1 �ereinstimmungen",
);

/** Greek (Ελληνικά)
 * @author Consta
 */
$allMessages['el'] = array(
	'mywhitelistpages' => 'Οι Σελίδες μου',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$allMessages['eo'] = array(
	'mywhitelistpages'         => 'Miaj Paĝoj',
	'whitelisttablemodifynone' => 'Neniu',
	'whitelisttableedit'       => 'Redaktu',
	'whitelistnewtablereview'  => 'Kontrolu',
	'whitelistbadtitle'        => 'Fuŝa titolo -',
	'whitelistnever'           => 'neniam',
	'whitelistnummatches'      => '- $1 pafoj',
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
Utiliser soit le caractère * soit %',
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

/** Galician (Galego)
 * @author Toliño
 */
$allMessages['gl'] = array(
	'whitelist'                => 'Páxinas da listaxe branca',
	'mywhitelistpages'         => 'As miñas páxinas',
	'whitelistfor'             => '<center>Información actual para <b>$1</b></center>',
	'whitelisttablemodify'     => 'Modificar',
	'whitelisttablemodifyall'  => 'Todo',
	'whitelisttablemodifynone' => 'Ningún',
	'whitelisttablepage'       => 'Páxina do wiki',
	'whitelisttableexpires'    => 'Expira o',
	'whitelisttableedit'       => 'Editar',
	'whitelisttableview'       => 'Ver',
	'whitelisttablenewdate'    => 'Nova data:',
	'whitelisttableremove'     => 'Eliminar',
	'whitelistnewpagesfor'     => 'Engada novas páxinas á listaxe branca de <b>$1</b><br />
Pode usar * ou %, como tamén o carácter "comodín"',
	'whitelistnewtabledate'    => 'Data de expiración:',
	'whitelistnewtableprocess' => 'Proceso',
	'whitelistbadtitle'        => 'Título incorrecto -',
	'whitelistrequestmsg'      => '$1 solicitou ter acceso ás seguintes páxinas:

$2',
	'whitelistnever'           => 'nunca',
);

/** Hindi (हिन्दी)
 * @author Kaustubh
 */
$allMessages['hi'] = array(
	'whitelisttablemodifynone' => 'बिल्कुल नहीं',
	'whitelisttableremove'     => 'हटायें',
	'whitelistnewtablereview'  => 'अवलोकन',
);

/** Hungarian (Magyar)
 * @author Dani
 */
$allMessages['hu'] = array(
	'whitelisttablemodifynone' => 'Nincs',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Lovekhmer
 * @author គីមស៊្រុន
 */
$allMessages['km'] = array(
	'mywhitelistpages'         => 'ទំព័ររបស់ខ្ញុំ',
	'whitelisttablemodify'     => 'កែសំរួល',
	'whitelisttablemodifyall'  => 'ទាំងអស់',
	'whitelisttablemodifynone' => 'ទទេ',
	'whitelisttablepage'       => 'ទំព័រវិគី',
	'whitelisttableexpires'    => 'ផុតកំនត់នៅថ្ងៃទី',
	'whitelisttablemodby'      => 'កែសំរួលចុងក្រោយដោយ',
	'whitelisttablemodon'      => 'កែសំរួលចុងក្រោយនៅ',
	'whitelisttableedit'       => 'កែប្រែ',
	'whitelisttableview'       => 'មើល',
	'whitelisttablenewdate'    => 'កាលបរិច្ឆេទថ្មី៖',
	'whitelisttablechangedate' => 'ផ្លាស់ប្តូរកាលបរិច្ឆេទផុតកំណត់',
	'whitelisttableremove'     => 'ដកចេញ',
	'whitelistnewtabledate'    => 'កាលបរិច្ឆេទផុតកំណត់៖',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$allMessages['lb'] = array(
	'whitelist'                => "''Whiteliste''-Säiten",
	'mywhitelistpages'         => 'Meng Säiten',
	'whitelisttablemodify'     => 'Änneren',
	'whitelisttablemodifyall'  => 'All',
	'whitelisttablemodifynone' => 'Näischt',
	'whitelisttablepage'       => 'Wiki Säit',
	'whitelisttablemodby'      => "Fir d'läscht geännert vum",
	'whitelisttablemodon'      => "Fir d'läscht geännert de(n)",
	'whitelisttableedit'       => 'Änneren',
	'whitelisttableview'       => 'Weisen',
	'whitelisttablenewdate'    => 'Neien Datum:',
	'whitelisttableremove'     => 'Zréckzéien',
	'whitelistpagelist'        => 'Säite vu(n) {{SITENAME}} fir $1',
	'whitelistbadtitle'        => 'Schlechten Titel -',
	'whitelistoverview'        => '== Iwwersiicht vun den Ännerunge vun $1 ==',
	'whitelistoverviewcd'      => "* Datum vun '''$1''' ännere  fir [[:$2|$2]]",
	'whitelistrequestconf'     => "D'Ufro fir nei Säite gouf geschéckt un $1",
	'whitelistnever'           => 'nie',
	'whitelistnummatches'      => '- $1 {{PLURAL:$1|Resultat|Resultater}}',
);

/** Malayalam (മലയാളം)
 * @author Shijualex
 */
$allMessages['ml'] = array(
	'whitelisttablemodifyall' => 'എല്ലാം',
);

/** Marathi (मराठी)
 * @author Kaustubh
 */
$allMessages['mr'] = array(
	'whitelisttablemodifynone' => 'काहीही नाही',
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
Gebruik * of % als wildcard",
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
	'whitelistoverviewna'       => "* [[:$1|$1]] wordt toegevoegd aan de witte lijst met toegangstype '''$2''' en verloopdatum '''$3'''",
	'whitelistrequest'          => "Toegang tot meer pagina's vragen",
	'whitelistrequestmsg'       => "$1 heeft toegang gevraagd tot de volgende pagina's:

$2",
	'whitelistrequestconf'      => "Het verzoek voor nieuwe pagina's is verzonden naar $1",
	'whitelistnonrestricted'    => "Gebruiker '''$1''' is geen gebruiker met beperkte rechten.
Deze pagina is alleen van toepassing op gebruikers met beperkte rechten.",
	'whitelistnever'            => 'nooit',
	'whitelistnummatches'       => '- $1 resultaten',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Jon Harald Søby
 */
$allMessages['nn'] = array(
	'whitelisttablemodifynone' => 'Ingen',
	'whitelisttableedit'       => 'Endre',
	'whitelisttableremove'     => 'Fjern',
	'whitelistnever'           => 'aldri',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 * @author Siebrand
 */
$allMessages['no'] = array(
	'whitelist-desc'            => 'Redigering av tilgangsrettigheter for begrensede brukere',
	'whitelistedit'             => 'Rettighetsredigering for hvitliste',
	'whitelist'                 => 'Hvitelistede sider',
	'mywhitelistpages'          => 'Mine sider',
	'whitelistfor'              => '<center>Nåværende informasjon for <b>$1</b></center>',
	'whitelisttablemodify'      => 'Endre',
	'whitelisttablemodifyall'   => 'Alle',
	'whitelisttablemodifynone'  => 'Ingen',
	'whitelisttablepage'        => 'Wikiside',
	'whitelisttabletype'        => 'Tilgangstype',
	'whitelisttableexpires'     => 'Utgår',
	'whitelisttablemodby'       => 'Sist endret av',
	'whitelisttablemodon'       => 'Sist endret',
	'whitelisttableedit'        => 'Rediger',
	'whitelisttableview'        => 'Vis',
	'whitelisttablenewdate'     => 'Ny dato:',
	'whitelisttablechangedate'  => 'Endre utgangsdato',
	'whitelisttablesetedit'     => 'Sett til redigering',
	'whitelisttablesetview'     => 'Sett til visning',
	'whitelisttableremove'      => 'Fjern',
	'whitelistnewpagesfor'      => 'Legg til nye sider på hvitelisten til <b>$1</b><br />Bruk enten * eller % som jokertegn',
	'whitelistnewtabledate'     => 'Utgangsdato:',
	'whitelistnewtableedit'     => 'Sett til redigering',
	'whitelistnewtableview'     => 'Sett til visning',
	'whitelistnewtableprocess'  => 'Prosess',
	'whitelistnewtablereview'   => 'Gå gjennom',
	'whitelistselectrestricted' => '== ANgi navn på begrenset bruker ==',
	'whitelistpagelist'         => '{{SITENAME}}-sider for $1',
	'whitelistnocalendar'       => '<font color="red" size="3">Det virker som om [http://mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], en forutsetning for denne utvidelsen, ikke har blitt installert ordentlig.</font>',
	'whitelistbadtitle'         => 'Ugyldig tittel -',
	'whitelistoverview'         => '== Oversikt over endringer for $1 ==',
	'whitelistoverviewcd'       => "* Endrer dato for [[:$2|$2]] til '''$1'''",
	'whitelistoverviewsa'       => "* Setter tilgang for [[:$2|$2]] til '''$1'''",
	'whitelistoverviewrm'       => '* Fjerner tilgang til [[:$1|$1]]',
	'whitelistoverviewna'       => "* Legger til [[:$1|$1]] til hviteliste med tilgang '''$2''' og utløpsdato '''$3'''.",
	'whitelistrequest'          => 'Etterspør tilgang til flere sider',
	'whitelistrequestmsg'       => '$1 har etterspurt tilgang til følgende sider:

$2',
	'whitelistrequestconf'      => 'Etterspørsel om nye sider har blitt sendt til $1',
	'whitelistnonrestricted'    => "'''$1''' er ikke en begrenset bruker.
Denne siden kan kun brukes på begrensede brukere.",
	'whitelistnever'            => 'aldri',
	'whitelistnummatches'       => '  - $1 treff',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$allMessages['oc'] = array(
	'whitelist-desc'            => 'Modifica las permissions d’accès dels utilizaires de poders restrenches',
	'whitelistedit'             => 'Editor de la lista blanca dels accèsses',
	'whitelist'                 => 'Paginas de listas blancas',
	'mywhitelistpages'          => 'Mas paginas',
	'whitelistfor'              => '<center>Entresenhas actualas per <b>$1</b></center>',
	'whitelisttablemodify'      => 'Modificar',
	'whitelisttablemodifyall'   => 'Tot',
	'whitelisttablemodifynone'  => 'Nonrés',
	'whitelisttablepage'        => 'Pagina wiki',
	'whitelisttabletype'        => 'Mòde d’accès',
	'whitelisttableexpires'     => 'Expira lo',
	'whitelisttablemodby'       => 'Modificat en darrièr per',
	'whitelisttablemodon'       => 'Modificat en darrièr lo',
	'whitelisttableedit'        => 'Modificar',
	'whitelisttableview'        => 'Afichar',
	'whitelisttablenewdate'     => 'Data novèla :',
	'whitelisttablechangedate'  => 'Cambiar la data d’expiracion',
	'whitelisttablesetedit'     => 'Paramètres per l’edicion',
	'whitelisttablesetview'     => 'Paramètres per visionar',
	'whitelisttableremove'      => 'Levar',
	'whitelistnewpagesfor'      => 'Ajusta de paginas novèlas a la lista blanca de <b>$1</b><br />
Utilizatz siá lo caractèr * siá %',
	'whitelistnewtabledate'     => 'Data d’expiracion :',
	'whitelistnewtableedit'     => "Paramètres d'edicion",
	'whitelistnewtableview'     => 'Paramètres per visionar',
	'whitelistnewtableprocess'  => 'Tractar',
	'whitelistnewtablereview'   => 'Revisar',
	'whitelistselectrestricted' => "== Seleccionatz un nom d’utilizaire d'accès restrench ==",
	'whitelistpagelist'         => 'Paginas de {{SITENAME}} per $1',
	'whitelistnocalendar'       => "<font color='red' size=3>Sembla que lo modul [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], una extension prerequesa, siá pas estada installada coma caliá !</font>",
	'whitelistbadtitle'         => 'Títol incorrècte ‑',
	'whitelistoverview'         => '== Vista generala dels cambiaments per $1 ==',
	'whitelistoverviewcd'       => "Modificacion de la data de '''$1''' per [[:$2|$2]]",
	'whitelistoverviewsa'       => "* configurar l'accès de '''$1''' per [[:$2|$2]]",
	'whitelistoverviewrm'       => '* Retirament de l’accès a [[:$1|$1]]',
	'whitelistoverviewna'       => "* Ajusta [[:$1|$1]] a la lista blanca amb los dreches de '''$2''' amb per data d’expiracion lo '''$3'''",
	'whitelistrequest'          => 'Demanda d’accès a mai de paginas',
	'whitelistrequestmsg'       => '$1 a demandat l’accès a las paginas seguentas :

$2',
	'whitelistrequestconf'      => 'Una demanda d’accès per de paginas novèlas es estada mandada a $1',
	'whitelistnonrestricted'    => "L'utilizaire  '''$1''' es pas amb de dreches restrenches.
Aquesta pagina s’aplica pas qu’als utilizaires disposant de dreches restrenches.",
	'whitelistnever'            => 'jamai',
	'whitelistnummatches'       => ' - $1 {{PLURAL:$1|ocuréncia|ocuréncias}}',
);

/** Polish (Polski)
 * @author Wpedzich
 * @author Sp5uhe
 */
$allMessages['pl'] = array(
	'whitelist-desc'            => 'Edytuj możliwość dostępu dla użytkowników z ograniczeniami',
	'whitelistedit'             => 'Edytor listy stron ogólnie dostępnych',
	'whitelist'                 => 'Strony z listy ogólnie dostępnych',
	'mywhitelistpages'          => 'Strony użytkownika',
	'whitelistfor'              => '<center>Aktualne informacje na temat <b>$1<b></center>',
	'whitelisttablemodify'      => 'Zmodyfikuj',
	'whitelisttablemodifyall'   => 'Wszystkie',
	'whitelisttablemodifynone'  => 'Żadna',
	'whitelisttablepage'        => 'Strona wiki:',
	'whitelisttabletype'        => 'Typ dostępu:',
	'whitelisttableexpires'     => 'Wygasa:',
	'whitelisttablemodby'       => 'Ostatnio zmodyfikowany przez:',
	'whitelisttablemodon'       => 'Data ostatniej modyfikacji:',
	'whitelisttableedit'        => 'Edytuj',
	'whitelisttableview'        => 'Podgląd',
	'whitelisttablenewdate'     => 'Nowa data:',
	'whitelisttablechangedate'  => 'Zmień datę wygaśnięcia:',
	'whitelisttablesetedit'     => 'Przełącz na edycję',
	'whitelisttablesetview'     => 'Przełącz na podgląd',
	'whitelisttableremove'      => 'Usuń',
	'whitelistnewpagesfor'      => 'Dodaj nowe strony do listy stron ogólnie dostępnych <b>$1</b><br />
Można stosować symbole wieloznaczne * i %',
	'whitelistnewtabledate'     => 'Wygasa:',
	'whitelistnewtableedit'     => 'Przełącz na edycję',
	'whitelistnewtableview'     => 'Przełącz na podgląd',
	'whitelistnewtableprocess'  => 'Przetwórz',
	'whitelistnewtablereview'   => 'Przejrzyj',
	'whitelistselectrestricted' => '== Wybierz nazwę użytkownika z ograniczeniami ==',
	'whitelistpagelist'         => 'Strony $1 w serwisie {{SITENAME}}',
	'whitelistnocalendar'       => "<font color='red' size=3>Prawdopodobnie, wymagane do pracy tego modułu rozszerzenie [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics] nie zostało poprawnie zainstalowane.</font>",
	'whitelistbadtitle'         => 'Nieprawidłowa nazwa -',
	'whitelistoverview'         => '== Przegląd zmian dla elementu $1 ==',
	'whitelistoverviewcd'       => "* Zmiana daty ograniczenia na '''$1''' w odniesieniu do elementu [[:$2:$2]]",
	'whitelistoverviewsa'       => "* Ustalanie dostępu dla elementu '''$1''' do elementu [[:$2|$2]]",
	'whitelistoverviewrm'       => '* Usuwanie dostępu do [[:$1|$1]]',
	'whitelistoverviewna'       => "* Dodawanie elementu [[:$1|$1]] do listy dostępu - dostęp dla '''$2''', data wygaśnięcia '''$3'''",
	'whitelistrequest'          => 'Zażądaj dostępu do większej ilości stron',
	'whitelistrequestmsg'       => 'Użytkownik $1 zażądał dostępu do następujących stron:

$2',
	'whitelistrequestconf'      => 'Żądanie utworzenia nowych stron zostało przesłane do $1',
	'whitelistnonrestricted'    => "Na użytkownika '''$1''' nie nałożono ograniczeń.
Ta strona ma zastosowanie tylko do użytkowników na których zostały narzucone ograniczenia.",
	'whitelistnever'            => 'nigdy',
	'whitelistnummatches'       => 'wyników: $1',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$allMessages['ps'] = array(
	'mywhitelistpages'         => 'زما پاڼې',
	'whitelisttablemodifyall'  => 'ټول',
	'whitelisttablemodifynone' => 'هېڅ',
	'whitelisttablepage'       => 'ويکي مخ',
	'whitelisttabletype'       => 'د لاسرسۍ ډول',
	'whitelisttablenewdate'    => 'نوې نېټه:',
	'whitelistnewtabledate'    => 'د پای نېټه:',
	'whitelistbadtitle'        => 'ناسم سرليک -',
	'whitelistrequestconf'     => '$1 ته د نوي مخونو غوښتنه ولېږل شوه',
	'whitelistnever'           => 'هېڅکله',
);

/** Russian (Русский)
 * @author .:Ajvol:.
 */
$allMessages['ru'] = array(
	'whitelistnewtableprocess' => 'Процесс',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$allMessages['sk'] = array(
	'whitelist-desc'            => 'Upraviť oprávnenia prístupu používateľov',
	'whitelistedit'             => 'Editor bielej listiny prístupu',
	'whitelist'                 => 'Dať stránky na bielu listinu',
	'mywhitelistpages'          => 'Moje stránky',
	'whitelistfor'              => '<center>Aktuálne informácie pre <b>$1<b></center>',
	'whitelisttablemodify'      => 'Zmeniť',
	'whitelisttablemodifyall'   => 'Všetky',
	'whitelisttablemodifynone'  => 'Žiadne',
	'whitelisttablepage'        => 'Wiki stránka',
	'whitelisttabletype'        => 'Typ prístupu',
	'whitelisttableexpires'     => 'Vyprší',
	'whitelisttablemodby'       => 'Naspoledy zmenil',
	'whitelisttablemodon'       => 'Naposledy zmenené',
	'whitelisttableedit'        => 'Upraviť',
	'whitelisttableview'        => 'Zobraziť',
	'whitelisttablenewdate'     => 'Nový dátum:',
	'whitelisttablechangedate'  => 'Zmeniť dátum vypršania',
	'whitelisttablesetedit'     => 'Nastaviť na Upraviť',
	'whitelisttablesetview'     => 'Nastaviť na Zobrazenie',
	'whitelisttableremove'      => 'Odstrániť',
	'whitelistnewpagesfor'      => 'Pridať nové stránky na bielu listinu <b>$1</b><br />
Ako zástupný znak použite buď * alebo %',
	'whitelistnewtabledate'     => 'Dátum vypršania:',
	'whitelistnewtableedit'     => 'Nastaviť na Upraviť',
	'whitelistnewtableview'     => 'Nastaviť na Zobraziť',
	'whitelistnewtableprocess'  => 'Spracovať',
	'whitelistnewtablereview'   => 'Skontrolovať',
	'whitelistselectrestricted' => '== Vyberte meno používateľa ==',
	'whitelistpagelist'         => 'stránky {{GRAMMAR:genitív|{{SITENAME}}}} pre $1',
	'whitelistnocalendar'       => "<font color='red' size=3>Zdá sa, že nie je správne nainštalované rozšírenie [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], ktoré toto rozšírenie vyžaduje.</font>",
	'whitelistbadtitle'         => 'Chybný názov -',
	'whitelistoverview'         => '== Prehľad zmien $1 ==',
	'whitelistoverviewcd'       => "* Zmena dátumu [[:$2|$2]] na '''$1'''",
	'whitelistoverviewsa'       => "* Nastavenie prístupu [[:$2|$2]] na '''$1'''",
	'whitelistoverviewrm'       => "* Odstránenie prístupu [[:$2|$2]] na '''$1'''",
	'whitelistoverviewna'       => "* Pridanie prístupu [[:$1|$1]] na bielu listinu s prístupom '''$2''' a vypršaním '''$3'''",
	'whitelistrequest'          => 'Požiadať o prístup k viacerým stránkam',
	'whitelistrequestmsg'       => '$1 požiadal o prístup k nasledovným stránkam:

$2',
	'whitelistrequestconf'      => 'Žiadosť o nové stránky bola odoslaná $1',
	'whitelistnonrestricted'    => "Používateľ '''$1''' nie je obmedzený používateľ.
Táto stránka sa týka iba obmedzneých používateľov.",
	'whitelistnever'            => 'nikdy',
	'whitelistnummatches'       => '  - $1 výsledkov',
);

/** Serbian Cyrillic ekavian (ћирилица)
 * @author Sasa Stefanovic
 */
$allMessages['sr-ec'] = array(
	'whitelisttablemodifynone' => 'Нема',
	'whitelisttableedit'       => 'Уреди',
	'whitelisttableremove'     => 'Уклони',
);

/** Swedish (Svenska)
 * @author M.M.S.
 * @author Sannab
 */
$allMessages['sv'] = array(
	'whitelist-desc'            => 'Redigera åtkomsträttigheter för begränsade användare',
	'whitelistedit'             => 'Rättighetsredigerare för vitlista',
	'whitelist'                 => 'Vitlistade sidor',
	'mywhitelistpages'          => 'Mina sidor',
	'whitelistfor'              => '<center>Nuvarande information för <b>$1<b></center>',
	'whitelisttablemodify'      => 'Ändra',
	'whitelisttablemodifyall'   => 'Alla',
	'whitelisttablemodifynone'  => 'Ingen',
	'whitelisttablepage'        => 'Wikisida',
	'whitelisttabletype'        => 'Åtkomsttyp',
	'whitelisttableexpires'     => 'Utgår',
	'whitelisttablemodby'       => 'Senast ändrad av',
	'whitelisttablemodon'       => 'Senast ändrad på',
	'whitelisttableedit'        => 'Redigera',
	'whitelisttableview'        => 'Visa',
	'whitelisttablenewdate'     => 'Nytt datum:',
	'whitelisttablechangedate'  => 'Ändra utgångsdatum',
	'whitelisttablesetedit'     => 'Ange att redigera',
	'whitelisttablesetview'     => 'Ange att visa',
	'whitelisttableremove'      => 'Radera',
	'whitelistnewpagesfor'      => 'Lägg till nya sidor till <b>$1s</b> vitlista<br />
Använd hellre * eller % som jokertecken',
	'whitelistnewtabledate'     => 'Utgångsdatum:',
	'whitelistnewtableedit'     => 'Ange att redigera',
	'whitelistnewtableview'     => 'Ange att visa',
	'whitelistnewtableprocess'  => 'Behandla',
	'whitelistnewtablereview'   => 'Granska',
	'whitelistselectrestricted' => '== Ange begränsad användares namn ==',
	'whitelistpagelist'         => '{{SITENAME}} sidor för $1',
	'whitelistnocalendar'       => "<font color='red' size=3>Det verkar som [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], en förutsättning för detta programtillägg, inte har installerats ordentligt!</font>",
	'whitelistbadtitle'         => 'Dålig titel -',
	'whitelistoverview'         => '== Översikt av ändringar för $1 ==',
	'whitelistoverviewcd'       => "* Ändrar datum till '''$1''' för [[:$2|$2]]",
	'whitelistoverviewsa'       => "* Anger åtkomst till '''$1''' för [[:$2|$2]]",
	'whitelistoverviewrm'       => '* Raderar åtkomst till [[:$1|$1]]',
	'whitelistoverviewna'       => "* Lägger till [[:$1|$1]] till vitlista med åtkomst '''$2''' och '''$3''' utgångsdatum",
	'whitelistrequest'          => 'Efterfråga åtkomst till mer sidor',
	'whitelistrequestmsg'       => '$1 har efterfrågat åtkomst till följande sidor:

$2',
	'whitelistrequestconf'      => 'Efterfrågan för nya sidor har sänts till $1',
	'whitelistnonrestricted'    => "Användare '''$1''' är inte en begränsad användare.
Denna sida är endast tillämpbar på begränsade användare",
	'whitelistnever'            => 'aldrig',
	'whitelistnummatches'       => ' - $1 träffar',
);

/** ślůnski (ślůnski)
 * @author Herr Kriss
 * @author Pimke
 */
$allMessages['szl'] = array(
	'whitelisttableedit' => 'Sprowjéj',
	'whitelistbadtitle'  => 'Zuy titel',
);

/** Tamil (தமிழ்)
 * @author Trengarasu
 */
$allMessages['ta'] = array(
	'whitelisttablemodifyall' => 'அனைத்து',
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

/** Tetum (Tetun)
 * @author MF-Warburg
 */
$allMessages['tet'] = array(
	'mywhitelistpages'        => "Ha'u-nia pájina sira",
	'whitelisttablemodifyall' => 'Hotu',
	'whitelisttableedit'      => 'Edita',
);

/** Tajik (Cyrillic) (Тоҷикӣ/tojikī (Cyrillic))
 * @author Ibrahim
 */
$allMessages['tg-cyrl'] = array(
	'whitelist-desc'           => 'Иҷозаҳои дастрасии корбарони маҳдудшударо вироиш кунед',
	'whitelist'                => 'Саҳифаҳои Феҳристи сафед',
	'mywhitelistpages'         => 'Саҳифаҳои Ман',
	'whitelistfor'             => '<center>Иттилооти кунунӣ барои <b>$1</b></center>',
	'whitelisttablemodify'     => 'Пиростан',
	'whitelisttablemodifyall'  => 'Ҳама',
	'whitelisttablemodifynone' => 'Ҳеҷ',
	'whitelisttablepage'       => 'Саҳифаи Вики',
	'whitelisttabletype'       => 'Навъи Дастрасӣ',
	'whitelisttableexpires'    => 'Сипарӣ мешавад дар',
	'whitelisttablemodby'      => 'Охирин маротиба пироста шуда буд тавассути',
	'whitelisttablemodon'      => 'Охирин маротиба пироста шуда буд дар',
	'whitelisttableedit'       => 'Вироиш',
	'whitelisttableview'       => 'Дидан',
	'whitelisttablenewdate'    => 'Таърихи Нав:',
	'whitelisttablechangedate' => 'Тағйири Таърихи Баинтиҳорасӣ',
	'whitelisttableremove'     => 'Ҳазф',
	'whitelistnewtabledate'    => 'Таърихи Баинтиҳорасӣ:',
	'whitelistnewtableprocess' => 'Раванд',
	'whitelistnewtablereview'  => 'Пешнамоиш',
	'whitelistbadtitle'        => 'Унвони номуносиб -',
	'whitelistrequest'         => 'Ба саҳифаҳои бештар дастрасиро дархост кунед',
	'whitelistrequestmsg'      => '$1 дастрасиро барои саҳифаҳои зерин дархост кард:

$2',
	'whitelistrequestconf'     => 'Дархост барои саҳифаҳои ҷадид ба $1 фиристода шуд',
	'whitelistnever'           => 'ҳеҷгоҳ',
	'whitelistnummatches'      => ' - $1 мутобиқат мекунад',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$allMessages['vi'] = array(
	'whitelisttablemodifyall'  => 'Tất cả',
	'whitelisttablemodifynone' => 'Không có',
	'whitelisttableedit'       => 'Sửa',
	'whitelistbadtitle'        => 'Tựa trang sai –',
);

