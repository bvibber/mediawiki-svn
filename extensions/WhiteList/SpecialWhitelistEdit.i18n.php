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
	'whitelistedit'               => 'Whitelist access editor',
	'whitelist'                   => 'Whitelist pages',
	'mywhitelistpages'            => 'My pages',
	'whitelistfor'                => "<center>Current information for <b>$1</b></center>",
	'whitelisttablemodify'        => 'Modify',
	'whitelisttablemodifyall'     => 'All',
	'whitelisttablemodifynone'    => 'None',
	'whitelisttablepage'          => 'Wiki page',
	'whitelisttabletype'          => 'Access type',
	'whitelisttableexpires'       => 'Expires on',
	'whitelisttablemodby'         => 'Last modified by',
	'whitelisttablemodon'         => 'Last modified on',
	'whitelisttableedit'          => 'Edit',
	'whitelisttableview'          => 'View',
	'whitelisttablenewdate'       => 'New date:',
	'whitelisttablechangedate'    => 'Change expiry date',
	'whitelisttablesetedit'       => 'Set to edit',
	'whitelisttablesetview'       => 'Set to view',
	'whitelisttableremove'        => 'Remove',
	'whitelistnewpagesfor'        => "Add new pages to <b>$1's</b> white list<br />
Use either * or % as wildcard character",
	'whitelistnewtabledate'       => 'Expiry date:',
	'whitelistnewtableedit'       => 'Set to edit',
	'whitelistnewtableview'       => 'Set to view',
	'whitelistnewtableprocess'    => 'Process',
	'whitelistnewtablereview'     => 'Review',
	'whitelistselectrestricted'   => '== Select restricted user name ==',
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

/** Message documentation (Message documentation)
 * @author Jon Harald Søby
 * @author Purodha
 */
$allMessages['qqq'] = array(
	'whitelist-desc' => 'Short description of the White List extension, shown on [[Special:Version]]. Do not translate or change links.',
	'mywhitelistpages' => '{{Identical|My pages}}',
	'whitelisttablemodifyall' => '{{Identical|All}}',
	'whitelisttablemodifynone' => '{{Identical|None}}',
	'whitelisttableexpires' => '{{Identical|Expires on}}',
	'whitelisttableedit' => '{{Identical|Edit}}',
	'whitelisttablesetedit' => '{{Identical|Set to edit}}',
	'whitelisttablesetview' => '{{Identical|Set to view}}',
	'whitelisttableremove' => '{{Identical|Remove}}',
	'whitelistnewtableedit' => '{{Identical|Set to edit}}',
	'whitelistnewtableview' => '{{Identical|Set to view}}',
	'whitelistnewtableprocess' => '{{Identical|Process}}',
	'whitelistnewtablereview' => '{{Identical|Review}}',
	'whitelistbadtitle' => '{{Identical|Bad title}}',
	'whitelistnever' => '{{Identical|Never}}',
);

/** Faeag Rotuma (Faeag Rotuma)
 * @author Jose77
 */
$allMessages['rtm'] = array(
	'whitelisttableedit' => "A'tū'ạki",
);

/** Eastern Mari (Олык Марий)
 * @author Сай
 */
$allMessages['mhr'] = array(
	'mywhitelistpages' => 'Мыйын лаштык-влак',
	'whitelistnever' => 'нигунам',
);

/** Niuean (ko e vagahau Niuē)
 * @author Jose77
 */
$allMessages['niu'] = array(
	'whitelisttableedit' => 'Fakahakohako',
);

/** Afrikaans (Afrikaans)
 * @author Arnobarnard
 */
$allMessages['af'] = array(
	'whitelisttablemodifyall' => 'Alle',
	'whitelisttablemodifynone' => 'Geen',
	'whitelisttableedit' => 'Wysig',
	'whitelisttableremove' => 'Skrap',
	'whitelistnever' => 'nooit',
);

/** Arabic (العربية)
 * @author Alnokta
 * @author Meno25
 * @author OsamaK
 */
$allMessages['ar'] = array(
	'whitelist-desc' => 'عدل سماحات الوصول للمستخدمين المحددين',
	'whitelistedit' => 'محرر وصول القائمة البيضاء',
	'whitelist' => 'صفحات القائمة البيضاء',
	'mywhitelistpages' => 'صفحاتي',
	'whitelistfor' => '<center>المعلومات الحالية ل<b>$1</b></center>',
	'whitelisttablemodify' => 'تعديل',
	'whitelisttablemodifyall' => 'الكل',
	'whitelisttablemodifynone' => 'لا شيء',
	'whitelisttablepage' => 'صفحة ويكي',
	'whitelisttabletype' => 'نوع الدخول',
	'whitelisttableexpires' => 'ينتهي في',
	'whitelisttablemodby' => 'آخر تعديل بواسطة',
	'whitelisttablemodon' => 'آخر تعديل في',
	'whitelisttableedit' => 'عدل',
	'whitelisttableview' => 'عرض',
	'whitelisttablenewdate' => 'تاريخ جديد:',
	'whitelisttablechangedate' => 'تغيير تاريخ الانتهاء',
	'whitelisttablesetedit' => 'ضبط للتعديل',
	'whitelisttablesetview' => 'ضبط للعرض',
	'whitelisttableremove' => 'إزالة',
	'whitelistnewpagesfor' => 'أضف صفحات جديدة إلى <b>$1</b القائمة البيضاء ل<br />
استخدم إما * أو % كحرف خاص',
	'whitelistnewtabledate' => 'تاريخ الانتهاء:',
	'whitelistnewtableedit' => 'ضبط للتعديل',
	'whitelistnewtableview' => 'ضبط للعرض',
	'whitelistnewtableprocess' => 'عملية',
	'whitelistnewtablereview' => 'مراجعة',
	'whitelistselectrestricted' => '== اختر اسم المستخدم المحدد ==',
	'whitelistpagelist' => 'صفحات {{SITENAME}} ل$1',
	'whitelistnocalendar' => "<font color='red' size=3>يبدو أن [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics]، متطلب لهذه الامتداد، لم يتم تركيبه بشكل صحيح!</font>",
	'whitelistbadtitle' => 'عنوان سيء -',
	'whitelistoverview' => '== مراجعة التغييرات ل$1 ==',
	'whitelistoverviewcd' => "* تغيير التاريخ إلى '''$1''' ل[[:$2|$2]]",
	'whitelistoverviewsa' => "* ضبط الدخول إلى '''$1''' ل[[:$2|$2]]",
	'whitelistoverviewrm' => '* إزالة الوصول إلى [[:$1|$1]]',
	'whitelistoverviewna' => "* إضافة [[:$1|$1]] إلى القائمة البيضاء بوصول '''$2''' و '''$3''' تاريخ انتهاء",
	'whitelistrequest' => 'طلب السماح لمزيد من الصفحات',
	'whitelistrequestmsg' => '$1 طلب الوصول إلى الصفحات التالية:

$2',
	'whitelistrequestconf' => 'الطلب للصفحات الجديدة تم إرساله إلى $1',
	'whitelistnonrestricted' => "المستخدم '''$1''' ليس مستخدما محددا.
هذه الصفحة مطبقة فقط على المستخدمين المحددين",
	'whitelistnever' => 'أبدا',
	'whitelistnummatches' => '  - $1 مطابقة',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 */
$allMessages['be-tarask'] = array(
	'whitelisttableedit' => 'Рэдагаваць',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$allMessages['bg'] = array(
	'mywhitelistpages' => 'Моите страници',
	'whitelistfor' => '<center>Текуща информация за <b>$1</b></center>',
	'whitelisttablemodify' => 'Промяна',
	'whitelisttablemodifyall' => 'Всички',
	'whitelisttablemodifynone' => 'Няма',
	'whitelisttablepage' => 'Уики страница',
	'whitelisttabletype' => 'Вид достъп',
	'whitelisttableexpires' => 'Изтича на',
	'whitelisttablemodby' => 'Последна промяна от',
	'whitelisttablemodon' => 'Последна промяна на',
	'whitelisttableedit' => 'Редактиране',
	'whitelisttableview' => 'Преглед',
	'whitelisttablenewdate' => 'Нова дата:',
	'whitelisttablechangedate' => 'Промяна срока на валидност',
	'whitelisttableremove' => 'Премахване',
	'whitelistnewtabledate' => 'Дата на изтичане:',
	'whitelistpagelist' => 'Страници за $1 в {{SITENAME}}',
	'whitelistnocalendar' => "<font color='red' size=3>Изглежда разширението [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], което е необходимо, не е инсталирано както трябва!</font>",
	'whitelistbadtitle' => 'Грешно заглавие -',
	'whitelistoverviewcd' => "* Промяна на датата за [[:$2|$2]] на '''$1'''",
	'whitelistoverviewrm' => '* Премахване на достъпа до [[:$1|$1]]',
	'whitelistrequest' => 'Поискване на достъп до още страници',
	'whitelistrequestmsg' => '$1 пожела достъп до следните страници:

$2',
	'whitelistrequestconf' => 'Заявка за нови страници беше изпратена на $1',
	'whitelistnever' => 'никога',
	'whitelistnummatches' => ' - $1 съвпадения',
);

/** Catalan (Català)
 * @author Jordi Roqué
 * @author SMP
 */
$allMessages['ca'] = array(
	'whitelisttablemodifynone' => 'Cap',
	'whitelisttableedit' => 'Edita',
	'whitelistnever' => 'mai',
);

/** Chamorro (Chamoru)
 * @author Jatrobat
 */
$allMessages['ch'] = array(
	'whitelisttableedit' => 'Tulaika',
);

/** Welsh (Cymraeg)
 * @author Lloffiwr
 */
$allMessages['cy'] = array(
	'whitelisttablemodifyall' => 'Oll',
	'whitelisttableedit' => 'Golygu',
);

/** Danish (Dansk)
 * @author Jon Harald Søby
 */
$allMessages['da'] = array(
	'whitelisttablemodifynone' => 'Ingen',
	'whitelisttableedit' => 'Redigér',
	'whitelistnever' => 'aldrig',
);

/** German (Deutsch)
 * @author Liam Rosen
 */
$allMessages['de'] = array(
	'whitelist-desc' => 'Zugriffsrechte von beschränkten Benutzern bearbeiten',
	'whitelistedit' => 'Whitelist-Zugriff-Editor',
	'whitelist' => 'Whitelist-Seiten',
	'mywhitelistpages' => 'Meine Seiten',
	'whitelistfor' => '<center>Aktuelle Information für <b>$1</b></center>',
	'whitelisttablemodify' => 'Modifizieren',
	'whitelisttablemodifyall' => 'Alles modifizieren',
	'whitelisttablemodifynone' => 'Nichts modifizieren',
	'whitelisttablepage' => 'Seite',
	'whitelisttabletype' => 'Zugriffstyp',
	'whitelisttableexpires' => 'Ablauf am',
	'whitelisttablemodby' => 'Zuletz modifiziert von',
	'whitelisttablemodon' => 'Zuletzt modifiziert am',
	'whitelisttableedit' => 'Bearbeiten',
	'whitelisttableview' => 'Anschauen',
	'whitelisttablenewdate' => 'Neues Datum:',
	'whitelisttablechangedate' => 'Ablaufsdatum ändern',
	'whitelisttablesetedit' => 'Bearbeiten',
	'whitelisttablesetview' => 'Anschauen',
	'whitelisttableremove' => 'Entfernen',
	'whitelistnewpagesfor' => "Neue Seiten zu <b>$1's</b> Whitelist hinzufügen<br />
Entweder * oder % als Maskenzeichen benutzen",
	'whitelistnewtabledate' => 'Ablaufdatum:',
	'whitelistnewtableedit' => 'Bearbeiten',
	'whitelistnewtableview' => 'Anschauen',
	'whitelistnewtableprocess' => 'Bearbeiten',
	'whitelistnewtablereview' => 'Überprüfen',
	'whitelistselectrestricted' => '== Beschränkter Benutzername selektieren ==',
	'whitelistpagelist' => '{{SITENAME}} Seiten für $1',
	'whitelistnocalendar' => "<font color='red' size=3>[http://www.mediawiki.org/wiki/Extension:Usage_Statistics Die Extension:UsageStatistics], eine Vorraussetzung für dieses Extension, wurde nicht installiert oder kann nicht gefunden werden!</font>",
	'whitelistbadtitle' => 'Titel inkompatibel -',
	'whitelistoverview' => '== Änderungsübersicht für $1 ==',
	'whitelistoverviewcd' => "* Datum '''($1)''' für [[:$2|$2]] wird geändert",
	'whitelistoverviewsa' => "* Zugriff '''$1''' für [[:$2|$2]] wird angewendet",
	'whitelistoverviewrm' => '* Zugriff auf [[:$1|$1]] wird entfernt',
	'whitelistrequest' => 'Weiteren Zugriff beantragen',
	'whitelistrequestmsg' => '$1 hat Zugriff auf die folgenden Seiten beantragt:
$2',
	'whitelistrequestconf' => 'Beantragung an $1 geschickt',
	'whitelistnonrestricted' => "'''$1''' ist kein beschränkter Benutzer.
Diese Seite gilt nur für beschränkte Bentzer.",
	'whitelistnever' => 'niemals',
	'whitelistnummatches' => ' - $1 Übereinstimmungen',
);

/** Zazaki (Zazaki)
 * @author Belekvor
 */
$allMessages['diq'] = array(
	'whitelisttablemodifynone' => 'çino',
);

/** Ewe (Eʋegbe)
 * @author Natsubee
 */
$allMessages['ee'] = array(
	'whitelistnever' => 'gbeɖe',
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
	'whitelist' => 'Blanklisto Paĝoj',
	'mywhitelistpages' => 'Miaj Paĝoj',
	'whitelistfor' => '<center>Nuna informo por <b>$1</b></center>',
	'whitelisttablemodify' => 'Modifi',
	'whitelisttablemodifyall' => 'Ĉiuj',
	'whitelisttablemodifynone' => 'Neniu',
	'whitelisttablepage' => 'Vikia Paĝo',
	'whitelisttableexpires' => 'Finas je',
	'whitelisttablemodby' => 'Laste modifita de',
	'whitelisttablemodon' => 'Laste modifita je',
	'whitelisttableedit' => 'Redakti',
	'whitelisttableview' => 'Rigardu',
	'whitelisttablenewdate' => 'Nova Dato:',
	'whitelisttablechangedate' => 'Ŝanĝu Findaton',
	'whitelisttableremove' => 'Forigi',
	'whitelistnewtabledate' => 'Findato:',
	'whitelistnewtableprocess' => 'Procezi',
	'whitelistnewtablereview' => 'Kontrolu',
	'whitelistselectrestricted' => '== Selektu Limigitan Salutnomon ==',
	'whitelistpagelist' => '{{SITENAME}} paĝoj por $1',
	'whitelistbadtitle' => 'Fuŝa titolo -',
	'whitelistoverview' => '== Resumo de ŝanĝoj por $1 ==',
	'whitelistoverviewcd' => "* Ŝanĝante daton al '''$1''' por [[:$2|$2]]",
	'whitelistrequest' => 'Petu atingon por pliaj paĝoj',
	'whitelistrequestmsg' => '$1 petis atingon al la jenaj paĝoj:

$2',
	'whitelistrequestconf' => 'Peto por novaj paĝoj estis sendita al $1',
	'whitelistnever' => 'neniam',
	'whitelistnummatches' => '- $1 pafoj',
);

/** Spanish (Español)
 * @author Piolinfax
 * @author Sanbec
 */
$allMessages['es'] = array(
	'whitelisttablemodifyall' => 'Todos',
	'whitelisttablemodifynone' => 'Ninguno',
	'whitelisttableedit' => 'Editar',
);

/** French (Français)
 * @author Grondin
 * @author Zetud
 */
$allMessages['fr'] = array(
	'whitelist-desc' => 'Modifie les permissions d’accès des utilisateurs à pouvoirs restreints',
	'whitelistedit' => 'Éditeur de la liste blanche des accès',
	'whitelist' => 'Pages de listes blanches',
	'mywhitelistpages' => 'Mes pages',
	'whitelistfor' => '<center>Informations actuelles pour <b>$1</b></center>',
	'whitelisttablemodify' => 'Modifier',
	'whitelisttablemodifyall' => 'Tout',
	'whitelisttablemodifynone' => 'Néant',
	'whitelisttablepage' => 'Page wiki',
	'whitelisttabletype' => 'Mode d’accès',
	'whitelisttableexpires' => 'Expire le',
	'whitelisttablemodby' => 'Modifié en dernier par',
	'whitelisttablemodon' => 'Modifié en dernier le',
	'whitelisttableedit' => 'Modifier',
	'whitelisttableview' => 'Afficher',
	'whitelisttablenewdate' => 'Nouvelle date :',
	'whitelisttablechangedate' => 'Changer la date d’expiration',
	'whitelisttablesetedit' => 'Paramètres pour l’édition',
	'whitelisttablesetview' => 'Paramètres pour visionner',
	'whitelisttableremove' => 'Retirer',
	'whitelistnewpagesfor' => 'Ajoute de nouvelles pages à la liste blanche de <b>$1</b><br />
Utiliser soit le caractère * soit %',
	'whitelistnewtabledate' => 'Date d’expiration :',
	'whitelistnewtableedit' => 'Paramètres d‘édition',
	'whitelistnewtableview' => 'Paramètres pour visionner',
	'whitelistnewtableprocess' => 'Traiter',
	'whitelistnewtablereview' => 'Réviser',
	'whitelistselectrestricted' => '== Sélectionner un nom d’utilisateur à accès restreint ==',
	'whitelistpagelist' => 'Pages de {{SITENAME}} pour $1',
	'whitelistnocalendar' => "<font color='red' size=3>Il semble que le module [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], une extension prérequise, n’ait pas été installée convenablement !</font>",
	'whitelistbadtitle' => 'Titre incorrect ‑',
	'whitelistoverview' => '== Vue générale des changements pour $1 ==',
	'whitelistoverviewcd' => "Modification de la date de '''$1''' pour [[:$2|$2]]",
	'whitelistoverviewsa' => "* configurer l'accès de '''$1''' pour [[:$2|$2]]",
	'whitelistoverviewrm' => '* Retrait de l’accès à [[:$1|$1]]',
	'whitelistoverviewna' => "* Ajoute [[:$1|$1]] à la liste blanche avec les droits de '''$2''' avec pour date d’expiration le '''$3'''",
	'whitelistrequest' => 'Demande d’accès à plus de pages',
	'whitelistrequestmsg' => '$1 a demandé l’accès aux pages suivantes :

$2',
	'whitelistrequestconf' => 'Une demande d’accès pour de nouvelles pages a été envoyée à $1',
	'whitelistnonrestricted' => "L'utilisateur  '''$1''' n’est pas avec des droits restreints.
Cette page ne s’applique qu’aux utilisateurs disposant de droits restreints.",
	'whitelistnever' => 'jamais',
	'whitelistnummatches' => ' - $1 {{PLURAL:$1|occurence|occurences}}',
);

/** Western Frisian (Frysk)
 * @author Snakesteuben
 */
$allMessages['fy'] = array(
	'whitelisttablemodifyall' => 'Alle',
	'whitelisttablemodifynone' => 'Gjin',
	'whitelisttableedit' => 'Wizigje',
);

/** Galician (Galego)
 * @author Toliño
 */
$allMessages['gl'] = array(
	'whitelist-desc' => 'Editar os permisos de acceso dos usuarios restrinxidos',
	'whitelistedit' => 'Editor de acceso da listaxe branca (whitelist)',
	'whitelist' => 'Páxinas da listaxe branca',
	'mywhitelistpages' => 'As miñas páxinas',
	'whitelistfor' => '<center>Información actual para <b>$1</b></center>',
	'whitelisttablemodify' => 'Modificar',
	'whitelisttablemodifyall' => 'Todo',
	'whitelisttablemodifynone' => 'Ningún',
	'whitelisttablepage' => 'Páxina do wiki',
	'whitelisttabletype' => 'Tipo de acceso',
	'whitelisttableexpires' => 'Expira o',
	'whitelisttablemodby' => 'Modificado por última vez por',
	'whitelisttablemodon' => 'Modificado por última o',
	'whitelisttableedit' => 'Editar',
	'whitelisttableview' => 'Ver',
	'whitelisttablenewdate' => 'Nova data:',
	'whitelisttablechangedate' => 'Cambiar a data de remate',
	'whitelisttablesetedit' => 'Preparar para editar',
	'whitelisttablesetview' => 'Preparar para ver',
	'whitelisttableremove' => 'Eliminar',
	'whitelistnewpagesfor' => 'Engada novas páxinas á listaxe branca de <b>$1</b><br />
Pode usar * ou %, como tamén o carácter "comodín"',
	'whitelistnewtabledate' => 'Data de expiración:',
	'whitelistnewtableedit' => 'Preparar para editar',
	'whitelistnewtableview' => 'Preparar para ver',
	'whitelistnewtableprocess' => 'Proceso',
	'whitelistnewtablereview' => 'Revisar',
	'whitelistselectrestricted' => '== Seleccionar un nome de usuario restrinxido ==',
	'whitelistpagelist' => 'Páxinas de {{SITENAME}} para $1',
	'whitelistnocalendar' => "<font color='red' size=3>Parece que [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], un requirimento previo para esta extensión, non foi instalada adecuadamente!</font>",
	'whitelistbadtitle' => 'Título incorrecto -',
	'whitelistoverview' => '== Visión xeral dos cambios para $1 ==',
	'whitelistoverviewcd' => "* Cambiando a data a '''$1''' para [[:$2|$2]]",
	'whitelistoverviewsa' => "* Configurando o acceso a '''$1''' para [[:$2|$2]]",
	'whitelistoverviewrm' => '* Eliminando o acceso a [[:$1|$1]]',
	'whitelistoverviewna' => "* Engadindo [[:$1|$1]] á listaxe branca (whitelist) con acceso a '''$2''' e data de remate '''$3'''",
	'whitelistrequest' => 'Solicitar acceso a máis páxinas',
	'whitelistrequestmsg' => '$1 solicitou ter acceso ás seguintes páxinas:

$2',
	'whitelistrequestconf' => 'A solicitude para páxinas novas foi enviada a $1',
	'whitelistnonrestricted' => "O usuario '''$1''' non é un usuario limitado.
Esta páxina só é aplicable aos usuarios limitados",
	'whitelistnever' => 'nunca',
	'whitelistnummatches' => '  - $1 coincidencias',
);

/** Gothic (������������������������������������)
 * @author Jocke Pirat
 */
$allMessages['got'] = array(
	'whitelisttableedit' => 'Máidjan',
);

/** Hakka (Hak-kâ-fa)
 * @author Hakka
 */
$allMessages['hak'] = array(
	'whitelisttableedit' => 'Phiên-chho',
);

/** Hawaiian (Hawai`i)
 * @author Kalani
 * @author Singularity
 */
$allMessages['haw'] = array(
	'mywhitelistpages' => 'Ka‘u mau ‘ao‘ao',
	'whitelisttablemodifyall' => 'Apau',
	'whitelisttableedit' => 'E ho‘opololei',
	'whitelisttableremove' => 'Kāpae',
	'whitelistbadtitle' => 'Inoa ‘ino -',
);

/** Hindi (हिन्दी)
 * @author Kaustubh
 */
$allMessages['hi'] = array(
	'whitelisttablemodifyall' => 'सभी',
	'whitelisttablemodifynone' => 'बिल्कुल नहीं',
	'whitelisttableexpires' => 'समाप्ती',
	'whitelisttableedit' => 'संपादन',
	'whitelisttableremove' => 'हटायें',
	'whitelistnewtableprocess' => 'कार्य',
	'whitelistnewtablereview' => 'अवलोकन',
);

/** Hiligaynon (Ilonggo)
 * @author Jose77
 */
$allMessages['hil'] = array(
	'whitelisttableedit' => 'Ilisan',
);

/** Croatian (Hrvatski)
 * @author Dalibor Bosits
 */
$allMessages['hr'] = array(
	'whitelisttableremove' => 'Ukloni',
	'whitelistnever' => 'nikad',
);

/** Hungarian (Magyar)
 * @author Dani
 */
$allMessages['hu'] = array(
	'whitelisttablemodifynone' => 'Nincs',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$allMessages['ia'] = array(
	'mywhitelistpages' => 'Mi paginas',
	'whitelisttablemodifyall' => 'Totes',
	'whitelisttableedit' => 'Modificar',
	'whitelistnever' => 'nunquam',
);

/** Indonesian (Bahasa Indonesia)
 * @author Rex
 */
$allMessages['id'] = array(
	'whitelisttablemodifyall' => 'Semua',
	'whitelisttablemodifynone' => 'Tidak ada',
);

/** Icelandic (Íslenska)
 * @author S.Örvarr.S
 */
$allMessages['is'] = array(
	'whitelistnever' => 'aldrei',
);

/** Italian (Italiano)
 * @author Darth Kule
 */
$allMessages['it'] = array(
	'whitelisttableedit' => 'Modifica',
);

/** Javanese (Basa Jawa)
 * @author Meursault2004
 */
$allMessages['jv'] = array(
	'whitelistedit' => 'Editor Aksès Daftar Putih',
	'whitelist' => 'Kaca-kaca Daftar Putih',
	'mywhitelistpages' => 'Kaca-kacaku',
	'whitelistfor' => '<center>Informasi saiki kanggo <b>$1</b></center>',
	'whitelisttablemodify' => 'Modifikasi',
	'whitelisttablemodifyall' => 'Kabèh',
	'whitelisttablemodifynone' => 'Ora ana',
	'whitelisttablepage' => 'Kaca Wiki',
	'whitelisttabletype' => 'Jenis Aksès',
	'whitelisttableexpires' => 'Kadaluwarsa Ing',
	'whitelisttablemodby' => 'Pungkasan dimodifikasi déning',
	'whitelisttablemodon' => 'Pungkasan dimodifikasi ing',
	'whitelisttableedit' => 'Sunting',
	'whitelisttableview' => 'Ndeleng',
	'whitelisttablenewdate' => 'Tanggal Anyar:',
	'whitelisttablechangedate' => 'Ganti Tanggal Kadaluwarsa',
	'whitelisttablesetedit' => 'Sèt kanggo Nyunting',
	'whitelisttablesetview' => 'Sèt kanggo Ndeleng',
	'whitelisttableremove' => 'Busak',
	'whitelistnewtabledate' => 'Tanggal kadaluwarsa:',
	'whitelistnewtableedit' => 'Set kanggo Nyunting',
	'whitelistnewtableview' => 'Set kanggo Ndeleng',
	'whitelistnewtableprocess' => 'Prosès',
	'whitelistselectrestricted' => '== Sèlèksi Jeneng Panganggo Sing Diwatesi ==',
	'whitelistpagelist' => 'Kaca-kaca {{SITENAME}} kanggo $1',
	'whitelistbadtitle' => 'Judhul ala -',
	'whitelistoverview' => '== Paninjoan amba owah-owahan kanggo $1 ==',
	'whitelistoverviewcd' => "* Ngowahi tanggal menyang '''$1''' kanggo [[:$2|$2]]",
	'whitelistoverviewrm' => '* Ngilangi aksès kanggo [[:$1|$1]]',
	'whitelistrequest' => 'Nyuwun aksès ing luwih akèh kaca',
	'whitelistrequestmsg' => '$1 nyuwun aksès ing kaca-kaca iki:

$2',
	'whitelistrequestconf' => 'Panyuwunan kaca-kaca anyar dikirimaké menyang $1',
	'whitelistnever' => 'ora tau',
	'whitelistnummatches' => '- pituwas $1 sing cocog',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Lovekhmer
 * @author គីមស៊្រុន
 */
$allMessages['km'] = array(
	'mywhitelistpages' => 'ទំព័ររបស់ខ្ញុំ',
	'whitelisttablemodify' => 'កែសំរួល',
	'whitelisttablemodifyall' => 'ទាំងអស់',
	'whitelisttablemodifynone' => 'ទទេ',
	'whitelisttablepage' => 'ទំព័រវិគី',
	'whitelisttableexpires' => 'ផុតកំនត់នៅថ្ងៃទី',
	'whitelisttablemodby' => 'កែសំរួលចុងក្រោយដោយ',
	'whitelisttablemodon' => 'កែសំរួលចុងក្រោយនៅ',
	'whitelisttableedit' => 'កែប្រែ',
	'whitelisttableview' => 'មើល',
	'whitelisttablenewdate' => 'កាលបរិច្ឆេទថ្មី៖',
	'whitelisttablechangedate' => 'ផ្លាស់ប្តូរកាលបរិច្ឆេទផុតកំណត់',
	'whitelisttableremove' => 'ដកចេញ',
	'whitelistnewtabledate' => 'កាលបរិច្ឆេទផុតកំណត់៖',
	'whitelistbadtitle' => 'ចំនងជើងមិនត្រឹមត្រូវ -',
	'whitelistnever' => 'មិនដែល',
);

/** Kinaray-a (Kinaray-a)
 * @author Jose77
 */
$allMessages['krj'] = array(
	'whitelisttableedit' => 'Iislan',
	'whitelistnever' => 'Indi gid',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$allMessages['ksh'] = array(
	'whitelist-desc' => 'De Zohjangs-Rääschte fun beschrängkte Metmaachere Ändere.',
	'whitelistedit' => '<i lang="en">whitelist</i> Zohjang Ändere',
	'whitelist' => '<i lang="en">whitelist</i> Sigge',
	'mywhitelistpages' => 'Ming Sigge',
	'whitelistfor' => '<center>Aktoelle Enfomazjuhne för <b>$1</b></center>',
	'whitelisttablemodify' => 'Ändere',
	'whitelisttablemodifyall' => 'All Ändere',
	'whitelisttablemodifynone' => 'Nix Ändere',
	'whitelisttablepage' => 'Sigg em Wiki',
	'whitelisttabletype' => 'Zohjangs-Aat',
	'whitelisttableexpires' => 'Läuf us am',
	'whitelisttablemodby' => 'Zoletz jändert fum',
	'whitelisttablemodon' => 'Zoletz jändert aam',
	'whitelisttableedit' => 'Ändere',
	'whitelisttableview' => 'Aanloore',
	'whitelisttablenewdate' => 'Neu Dattum:',
	'whitelisttablechangedate' => 'Ußlouf-Dattum ändere',
	'whitelisttablesetedit' => 'Beärrbeide',
	'whitelisttablesetview' => 'Aanlore',
	'whitelisttableremove' => 'Fottnämme',
	'whitelistnewpagesfor' => 'Neu Sigge en däm „<b>$1</b>“ sing <i lang="en">whitelist</i> erin don<br />
Donn entweder <b>*</b> udder <b>%</b> als en Platzhallder nämme för „<i>mer weße nit wi fill, un mer weße nit, wat för Zeiche</i>“',
	'whitelistnewtabledate' => 'Ußloufdattum:',
	'whitelistnewtableedit' => 'Beärbeide',
	'whitelistnewtableview' => 'Aanloore',
	'whitelistnewtableprocess' => 'Beärbeide',
	'whitelistnewtablereview' => 'Övverpröfe',
	'whitelistselectrestricted' => '== Enjeschränkte Metmaacher-Name ußsöke ==',
	'whitelistpagelist' => '{{SITENAME}} Sigge för $1',
	'whitelistnocalendar' => '<font color=\'red\' size=3>Dä [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Zosatz <i lang="en">UsageStatistics</i>], weed för der [http://www.mediawiki.org/wiki/Extension:WhiteList Zosatz  <i lang="en">WhiteList</i>] jebruch, eß ävver nit enstalleett, udder wood nit jefonge!</font>',
	'whitelistbadtitle' => 'Dä Titel paß nit -',
	'whitelistoverview' => '== Änderunge — Övverseech för $1 ==',
	'whitelistoverviewcd' => "* Änder dat Dattum för [[:$2|$2]] op '''$1'''",
	'whitelistoverviewsa' => "* Änder der Zojreff för [[:$2|$2]] op '''$1'''",
	'whitelistoverviewrm' => '* Dä Zojreff för [[:$1|$1]] flüch eruß',
	'whitelistoverviewna' => "* Donn [[:\$1|\$1]] en de <i lang=\"en\">whitelist</i> met Zojreff '''\$2''' un Ußlouf-Dattum '''\$3'''",
	'whitelistrequest' => 'Noh em Zojreff op mieh Sigge froore',
	'whitelistrequestmsg' => '$1 hät noh em Zohjang jefrooch för de Sigge:

$2',
	'whitelistrequestconf' => 'De Aanfroch fun wäje dä neu Sigge wood aan dä $1 jescheck',
	'whitelistnonrestricted' => "Dä Metmaacher '''$1''' es nit beschränk.
Di Sigg hee is nor för beschränkte Metmaacher ze bruche.",
	'whitelistnever' => 'nimohls',
	'whitelistnummatches' => ' - {{PLURAL:$1|ein zopaß Sigg|$1 zopaß Sigge|keine zopaß Sigg}}',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$allMessages['lb'] = array(
	'whitelist-desc' => "Ännert d'Rechter vu Benotzer mat limitéierte Rechter",
	'whitelist' => "''Whiteliste''-Säiten",
	'mywhitelistpages' => 'Meng Säiten',
	'whitelistfor' => '<center>Aktuell Informatioun fir <b>$1</b></center>',
	'whitelisttablemodify' => 'Änneren',
	'whitelisttablemodifyall' => 'All',
	'whitelisttablemodifynone' => 'Näischt',
	'whitelisttablepage' => 'Wiki Säit',
	'whitelisttablemodby' => "Fir d'läscht geännert vum",
	'whitelisttablemodon' => "Fir d'läscht geännert de(n)",
	'whitelisttableedit' => 'Änneren',
	'whitelisttableview' => 'Weisen',
	'whitelisttablenewdate' => 'Neien Datum:',
	'whitelisttablesetedit' => 'Ännerungsparameter',
	'whitelisttableremove' => 'Zréckzéien',
	'whitelistnewtableedit' => 'Ännerungsparameter',
	'whitelistnewtablereview' => 'Nokucken',
	'whitelistpagelist' => 'Säite vu(n) {{SITENAME}} fir $1',
	'whitelistbadtitle' => 'Schlechten Titel -',
	'whitelistoverview' => '== Iwwersiicht vun den Ännerunge vun $1 ==',
	'whitelistoverviewcd' => "* Datum vun '''$1''' ännere  fir [[:$2|$2]]",
	'whitelistoverviewsa' => "* Autorisatioun vum '''$1''' op [[:$2|$2]] astellen",
	'whitelistoverviewrm' => '* Autorisatioun fir [[:$1|$1]] gët ewechgeholl',
	'whitelistrequest' => 'Zougang zu méi Säite froen',
	'whitelistrequestmsg' => '$1 huet Accès op dës Säite gfrot:

$2',
	'whitelistrequestconf' => "D'Ufro fir nei Säite gouf geschéckt un $1",
	'whitelistnever' => 'nie',
	'whitelistnummatches' => '- $1 {{PLURAL:$1|Resultat|Resultater}}',
);

/** Malayalam (മലയാളം)
 * @author Shijualex
 */
$allMessages['ml'] = array(
	'mywhitelistpages' => 'എന്റെ താളുകള്‍',
	'whitelisttablemodify' => 'തിരുത്തുക',
	'whitelisttablemodifyall' => 'എല്ലാം',
	'whitelisttablemodifynone' => 'ഒന്നുമില്ല',
	'whitelisttablepage' => 'വിക്കി താള്‍',
	'whitelisttableexpires' => 'കാലാവധി തീരുന്നത്',
	'whitelisttablemodby' => 'അവസാനമായി മാറ്റങ്ങള്‍ വരുത്തിയത്',
	'whitelisttablemodon' => 'അവസാനമായി മാറ്റങ്ങള്‍ വരുത്തിയ സമയം',
	'whitelisttableedit' => 'തിരുത്തുക',
	'whitelisttableview' => 'കാണുക',
	'whitelisttablenewdate' => 'പുതിയ തീയ്യതി:',
	'whitelisttablechangedate' => 'കാലാവധിയില്‍ മാറ്റം വരുത്തുക',
	'whitelisttablesetedit' => 'തിരുത്താനായി സജ്ജീകരിക്കുക',
	'whitelisttablesetview' => 'കാണാനായി സജ്ജീകരിക്കുക',
	'whitelisttableremove' => 'നീക്കം ചെയ്യുക',
	'whitelistnewtabledate' => 'കാലാവധി തീരുന്ന തീയ്യതി:',
	'whitelistnewtableedit' => 'തിരുത്താനായി സജ്ജീകരിക്കുക',
	'whitelistnewtableview' => 'കാണാനായി സജ്ജീകരിക്കുക',
	'whitelistnewtableprocess' => 'പ്രക്രിയ',
	'whitelistnewtablereview' => 'സം‌ശോധനം',
	'whitelistpagelist' => '{{SITENAME}} സം‌രംഭത്തില്‍ $1ന്റെ താളുകള്‍',
	'whitelistbadtitle' => 'അസാധുവായ തലക്കെട്ട്',
	'whitelistnever' => 'ഒരിക്കലും അരുത്:',
	'whitelistnummatches' => '- $1 യോജിച്ച ഫലങ്ങള്‍',
);

/** Marathi (मराठी)
 * @author Kaustubh
 * @author Mahitgar
 */
$allMessages['mr'] = array(
	'mywhitelistpages' => 'माझी पाने',
	'whitelistfor' => '<center><b>$1</b>बद्दलची सध्याची माहिती</center>',
	'whitelisttablemodify' => 'बदला',
	'whitelisttablemodifyall' => 'सर्व',
	'whitelisttablemodifynone' => 'काहीही नाही',
	'whitelisttablepage' => 'विकि पान',
	'whitelisttabletype' => 'ऍक्सेस प्रकार',
	'whitelisttableexpires' => 'समाप्ती',
	'whitelisttableedit' => 'संपादन',
	'whitelisttableview' => 'पहा',
	'whitelisttablenewdate' => 'नवीन तारीख:',
	'whitelisttablechangedate' => 'समाप्तीची तारीख बदला',
	'whitelisttableremove' => 'काढा',
	'whitelistnewtabledate' => 'समाप्तीची तारीख:',
	'whitelistnewtableprocess' => 'कार्य',
	'whitelistnewtablereview' => 'समीक्षण',
	'whitelistpagelist' => '{{SITENAME}} पाने $1 साठीची',
	'whitelistbadtitle' => 'चुकीचे शीर्षक -',
	'whitelistrequest' => 'अधिक पानांकरिता उपलब्धता सुसाध्य करून मागा',
	'whitelistrequestmsg' => '$1ने निम्ननिर्देशित पानांकरिता सुलभमार्ग सुसाध्य करून मागितला आहे:

$2',
	'whitelistrequestconf' => 'नवीन पानांची मागणी $1 ला पाठविलेली आहे',
	'whitelistnonrestricted' => "सदस्य '''$1''' हा प्रतिबंधित सदस्य नाही.
हे पान फक्त प्रतिबंधित सदस्यांसाठीच आहे",
	'whitelistnever' => 'कधीही नाही',
	'whitelistnummatches' => ' - $1 जुळण्या',
);

/** Nahuatl (Nāhuatl)
 * @author Fluence
 */
$allMessages['nah'] = array(
	'whitelisttablemodifyall' => 'Mochīntīn',
	'whitelisttablemodifynone' => 'Ahtlein',
	'whitelisttableedit' => 'Ticpatlāz',
	'whitelistnewtablereview' => 'Ticceppahuīz',
	'whitelistbadtitle' => 'Ahcualli tōcāitl -',
	'whitelistnever' => 'aīcmah',
);

/** Low German (Plattdüütsch)
 * @author Slomox
 */
$allMessages['nds'] = array(
	'whitelisttablemodify' => 'Ännern',
	'whitelisttablemodifyall' => 'All',
	'whitelisttablemodifynone' => 'Keen',
	'whitelisttablepage' => 'Wikisied',
	'whitelisttableedit' => 'Ännern',
	'whitelistnever' => 'nie',
);

/** Nedersaksisch (Nedersaksisch)
 * @author Servien
 */
$allMessages['nds-nl'] = array(
	'whitelisttableedit' => 'Bewark',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$allMessages['nl'] = array(
	'whitelist-desc' => 'Toegangsrechten voor gebruikers met beperkte rechten bewerken',
	'whitelistedit' => 'Toegang via witte lijst',
	'whitelist' => "Pagina's op de witte lijst",
	'mywhitelistpages' => "Mijn pagina's",
	'whitelistfor' => '<center>Huidige informatie voor <b>$1<b></center>',
	'whitelisttablemodify' => 'Bewerken',
	'whitelisttablemodifyall' => 'Alle',
	'whitelisttablemodifynone' => 'Geen',
	'whitelisttablepage' => 'Wikipagina',
	'whitelisttabletype' => 'Toegangstype',
	'whitelisttableexpires' => 'Verloopt op',
	'whitelisttablemodby' => 'Laatste bewerking door',
	'whitelisttablemodon' => 'Laatste wijziging op',
	'whitelisttableedit' => 'Bewerken',
	'whitelisttableview' => 'Bekijken',
	'whitelisttablenewdate' => 'Nieuwe datum:',
	'whitelisttablechangedate' => 'Verloopdatum bewerken',
	'whitelisttablesetedit' => 'Op bewerken instellen',
	'whitelisttablesetview' => 'Op bekijken instellen',
	'whitelisttableremove' => 'Verwijderen',
	'whitelistnewpagesfor' => "Nieuwe pagina's aan de witte lijst voor <b>$1</b> toevoegen<br />
Gebruik * of % als wildcard",
	'whitelistnewtabledate' => 'Verloopdatum:',
	'whitelistnewtableedit' => 'Op bewerken instellen',
	'whitelistnewtableview' => 'Op bekijken instellen',
	'whitelistnewtableprocess' => 'Verwerken',
	'whitelistnewtablereview' => 'Controleren',
	'whitelistselectrestricted' => '== Gebruiker met beperkingen selecteren ==',
	'whitelistpagelist' => "{{SITENAME}} pagina's voor $1",
	'whitelistnocalendar' => "<font color='red' size=3>[http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], een voorwaarde voor deze uitbreiding, lijkt niet juist geïnstalleerd!</font>",
	'whitelistbadtitle' => 'Onjuiste naam -',
	'whitelistoverview' => '== Overzicht van wijzigingen voor $1 ==',
	'whitelistoverviewcd' => "* verloopdatum gewijzigd naar '''$1''' voor [[:$2|$2]]",
	'whitelistoverviewsa' => "* toegangstype '''$1''' ingesteld voor [[:$2|$2]]",
	'whitelistoverviewrm' => '* toegang voor [[:$1|$1]] wordt verwijderd',
	'whitelistoverviewna' => "* [[:$1|$1]] wordt toegevoegd aan de witte lijst met toegangstype '''$2''' en verloopdatum '''$3'''",
	'whitelistrequest' => "Toegang tot meer pagina's vragen",
	'whitelistrequestmsg' => "$1 heeft toegang gevraagd tot de volgende pagina's:

$2",
	'whitelistrequestconf' => "Het verzoek voor nieuwe pagina's is verzonden naar $1",
	'whitelistnonrestricted' => "Gebruiker '''$1''' is geen gebruiker met beperkte rechten.
Deze pagina is alleen van toepassing op gebruikers met beperkte rechten.",
	'whitelistnever' => 'nooit',
	'whitelistnummatches' => '- $1 resultaten',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Jon Harald Søby
 */
$allMessages['nn'] = array(
	'whitelisttablemodifynone' => 'Ingen',
	'whitelisttableedit' => 'Endre',
	'whitelisttableremove' => 'Fjern',
	'whitelistnever' => 'aldri',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$allMessages['no'] = array(
	'whitelist-desc' => 'Redigering av tilgangsrettigheter for begrensede brukere',
	'whitelistedit' => 'Rettighetsredigering for hvitliste',
	'whitelist' => 'Hvitelistede sider',
	'mywhitelistpages' => 'Mine sider',
	'whitelistfor' => '<center>Nåværende informasjon for <b>$1</b></center>',
	'whitelisttablemodify' => 'Endre',
	'whitelisttablemodifyall' => 'Alle',
	'whitelisttablemodifynone' => 'Ingen',
	'whitelisttablepage' => 'Wikiside',
	'whitelisttabletype' => 'Tilgangstype',
	'whitelisttableexpires' => 'Utgår',
	'whitelisttablemodby' => 'Sist endret av',
	'whitelisttablemodon' => 'Sist endret',
	'whitelisttableedit' => 'Rediger',
	'whitelisttableview' => 'Vis',
	'whitelisttablenewdate' => 'Ny dato:',
	'whitelisttablechangedate' => 'Endre utgangsdato',
	'whitelisttablesetedit' => 'Sett til redigering',
	'whitelisttablesetview' => 'Sett til visning',
	'whitelisttableremove' => 'Fjern',
	'whitelistnewpagesfor' => 'Legg til nye sider på hvitelisten til <b>$1</b><br />Bruk enten * eller % som jokertegn',
	'whitelistnewtabledate' => 'Utgangsdato:',
	'whitelistnewtableedit' => 'Sett til redigering',
	'whitelistnewtableview' => 'Sett til visning',
	'whitelistnewtableprocess' => 'Prosess',
	'whitelistnewtablereview' => 'Gå gjennom',
	'whitelistselectrestricted' => '== ANgi navn på begrenset bruker ==',
	'whitelistpagelist' => '{{SITENAME}}-sider for $1',
	'whitelistnocalendar' => '<font color="red" size="3">Det virker som om [http://mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], en forutsetning for denne utvidelsen, ikke har blitt installert ordentlig.</font>',
	'whitelistbadtitle' => 'Ugyldig tittel -',
	'whitelistoverview' => '== Oversikt over endringer for $1 ==',
	'whitelistoverviewcd' => "* Endrer dato for [[:$2|$2]] til '''$1'''",
	'whitelistoverviewsa' => "* Setter tilgang for [[:$2|$2]] til '''$1'''",
	'whitelistoverviewrm' => '* Fjerner tilgang til [[:$1|$1]]',
	'whitelistoverviewna' => "* Legger til [[:$1|$1]] til hviteliste med tilgang '''$2''' og utløpsdato '''$3'''.",
	'whitelistrequest' => 'Etterspør tilgang til flere sider',
	'whitelistrequestmsg' => '$1 har etterspurt tilgang til følgende sider:

$2',
	'whitelistrequestconf' => 'Etterspørsel om nye sider har blitt sendt til $1',
	'whitelistnonrestricted' => "'''$1''' er ikke en begrenset bruker.
Denne siden kan kun brukes på begrensede brukere.",
	'whitelistnever' => 'aldri',
	'whitelistnummatches' => '  - $1 treff',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$allMessages['oc'] = array(
	'whitelist-desc' => 'Modifica las permissions d’accès dels utilizaires de poders restrenches',
	'whitelistedit' => 'Editor de la lista blanca dels accèsses',
	'whitelist' => 'Paginas de listas blancas',
	'mywhitelistpages' => 'Mas paginas',
	'whitelistfor' => '<center>Entresenhas actualas per <b>$1</b></center>',
	'whitelisttablemodify' => 'Modificar',
	'whitelisttablemodifyall' => 'Tot',
	'whitelisttablemodifynone' => 'Nonrés',
	'whitelisttablepage' => 'Pagina wiki',
	'whitelisttabletype' => 'Mòde d’accès',
	'whitelisttableexpires' => 'Expira lo',
	'whitelisttablemodby' => 'Modificat en darrièr per',
	'whitelisttablemodon' => 'Modificat en darrièr lo',
	'whitelisttableedit' => 'Modificar',
	'whitelisttableview' => 'Afichar',
	'whitelisttablenewdate' => 'Data novèla :',
	'whitelisttablechangedate' => 'Cambiar la data d’expiracion',
	'whitelisttablesetedit' => 'Paramètres per l’edicion',
	'whitelisttablesetview' => 'Paramètres per visionar',
	'whitelisttableremove' => 'Levar',
	'whitelistnewpagesfor' => 'Apondís de paginas novèlas a la lista blanca de <b>$1</b><br />
Utilizatz siá lo caractèr * siá %',
	'whitelistnewtabledate' => 'Data d’expiracion :',
	'whitelistnewtableedit' => "Paramètres d'edicion",
	'whitelistnewtableview' => 'Paramètres per visionar',
	'whitelistnewtableprocess' => 'Tractar',
	'whitelistnewtablereview' => 'Revisar',
	'whitelistselectrestricted' => "== Seleccionatz un nom d’utilizaire d'accès restrench ==",
	'whitelistpagelist' => 'Paginas de {{SITENAME}} per $1',
	'whitelistnocalendar' => "<font color='red' size=3>Sembla que lo modul [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], una extension prerequesa, siá pas estada installada coma caliá !</font>",
	'whitelistbadtitle' => 'Títol incorrècte ‑',
	'whitelistoverview' => '== Vista generala dels cambiaments per $1 ==',
	'whitelistoverviewcd' => "Modificacion de la data de '''$1''' per [[:$2|$2]]",
	'whitelistoverviewsa' => "* configurar l'accès de '''$1''' per [[:$2|$2]]",
	'whitelistoverviewrm' => '* Retirament de l’accès a [[:$1|$1]]',
	'whitelistoverviewna' => "* Apondís [[:$1|$1]] a la lista blanca amb los dreches de '''$2''' amb per data d’expiracion lo '''$3'''",
	'whitelistrequest' => 'Demanda d’accès a mai de paginas',
	'whitelistrequestmsg' => '$1 a demandat l’accès a las paginas seguentas :

$2',
	'whitelistrequestconf' => 'Una demanda d’accès per de paginas novèlas es estada mandada a $1',
	'whitelistnonrestricted' => "L'utilizaire  '''$1''' es pas amb de dreches restrenches.
Aquesta pagina s’aplica pas qu’als utilizaires disposant de dreches restrenches.",
	'whitelistnever' => 'jamai',
	'whitelistnummatches' => ' - $1 {{PLURAL:$1|ocuréncia|ocuréncias}}',
);

/** Ossetic (Иронау)
 * @author Amikeco
 */
$allMessages['os'] = array(
	'whitelisttableedit' => 'Баив æй',
	'whitelistbadtitle' => 'Æнæмбæлон сæргонд —',
	'whitelistnever' => 'никуы',
);

/** Punjabi (ਪੰਜਾਬੀ)
 * @author Gman124
 */
$allMessages['pa'] = array(
	'whitelisttablemodifyall' => 'ਸਭ',
	'whitelisttableedit' => 'ਬਦਲੋ',
	'whitelisttableview' => 'ਵੇਖੋ',
);

/** Polish (Polski)
 * @author Sp5uhe
 * @author Wpedzich
 */
$allMessages['pl'] = array(
	'whitelist-desc' => 'Umożliwianie dostępu użytkownikom z ograniczeniami',
	'whitelistedit' => 'Edytor listy stron ogólnie dostępnych',
	'whitelist' => 'Strony z listy ogólnie dostępnych',
	'mywhitelistpages' => 'Strony użytkownika',
	'whitelistfor' => '<center>Aktualne informacje na temat <b>$1<b></center>',
	'whitelisttablemodify' => 'Zmodyfikuj',
	'whitelisttablemodifyall' => 'Wszystkie',
	'whitelisttablemodifynone' => 'Żadna',
	'whitelisttablepage' => 'Strona wiki:',
	'whitelisttabletype' => 'Typ dostępu:',
	'whitelisttableexpires' => 'Wygasa:',
	'whitelisttablemodby' => 'Ostatnio zmodyfikowany przez:',
	'whitelisttablemodon' => 'Data ostatniej modyfikacji:',
	'whitelisttableedit' => 'Edytuj',
	'whitelisttableview' => 'Podgląd',
	'whitelisttablenewdate' => 'Nowa data:',
	'whitelisttablechangedate' => 'Zmień datę wygaśnięcia:',
	'whitelisttablesetedit' => 'Przełącz na edycję',
	'whitelisttablesetview' => 'Przełącz na podgląd',
	'whitelisttableremove' => 'Usuń',
	'whitelistnewpagesfor' => 'Dodaj nowe strony do listy stron ogólnie dostępnych <b>$1</b><br />
Można stosować symbole wieloznaczne * i %',
	'whitelistnewtabledate' => 'Wygasa:',
	'whitelistnewtableedit' => 'Przełącz na edycję',
	'whitelistnewtableview' => 'Przełącz na podgląd',
	'whitelistnewtableprocess' => 'Przetwórz',
	'whitelistnewtablereview' => 'Przejrzyj',
	'whitelistselectrestricted' => '== Wybierz nazwę użytkownika z ograniczeniami ==',
	'whitelistpagelist' => 'Strony $1 w serwisie {{SITENAME}}',
	'whitelistnocalendar' => "<font color='red' size=3>Prawdopodobnie, wymagane do pracy tego modułu rozszerzenie [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics] nie zostało poprawnie zainstalowane.</font>",
	'whitelistbadtitle' => 'Nieprawidłowa nazwa -',
	'whitelistoverview' => '== Przegląd zmian dla elementu $1 ==',
	'whitelistoverviewcd' => "* Zmiana daty ograniczenia na '''$1''' w odniesieniu do elementu [[:$2|$2]]",
	'whitelistoverviewsa' => "* Ustalanie dostępu dla elementu '''$1''' do elementu [[:$2|$2]]",
	'whitelistoverviewrm' => '* Usuwanie dostępu do [[:$1|$1]]',
	'whitelistoverviewna' => "* Dodawanie elementu [[:$1|$1]] do listy dostępu – dostęp dla '''$2''', data wygaśnięcia '''$3'''",
	'whitelistrequest' => 'Zażądaj dostępu do większej liczby stron',
	'whitelistrequestmsg' => 'Użytkownik $1 zażądał dostępu do następujących stron:

$2',
	'whitelistrequestconf' => 'Żądanie utworzenia nowych stron zostało przesłane do $1',
	'whitelistnonrestricted' => "Na użytkownika '''$1''' nie nałożono ograniczeń.
Ta strona ma zastosowanie tylko do użytkowników na których zostały narzucone ograniczenia.",
	'whitelistnever' => 'nigdy',
	'whitelistnummatches' => 'wyników: $1',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$allMessages['ps'] = array(
	'mywhitelistpages' => 'زما پاڼې',
	'whitelisttablemodifyall' => 'ټول',
	'whitelisttablemodifynone' => 'هېڅ',
	'whitelisttablepage' => 'ويکي مخ',
	'whitelisttabletype' => 'د لاسرسۍ ډول',
	'whitelisttablenewdate' => 'نوې نېټه:',
	'whitelisttableremove' => 'غورځول',
	'whitelistnewtabledate' => 'د پای نېټه:',
	'whitelistnewtablereview' => 'مخکتنه',
	'whitelistbadtitle' => 'ناسم سرليک -',
	'whitelistrequestconf' => '$1 ته د نوي مخونو غوښتنه ولېږل شوه',
	'whitelistnever' => 'هېڅکله',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$allMessages['pt'] = array(
	'mywhitelistpages' => 'Minhas Páginas',
	'whitelisttablemodify' => 'Modificar',
	'whitelisttableedit' => 'Editar',
	'whitelisttableview' => 'Ver',
	'whitelisttablenewdate' => 'Nova Data:',
	'whitelistbadtitle' => 'Titulo inválido -',
	'whitelistnever' => 'nunca',
	'whitelistnummatches' => '  - $1 resultados',
);

/** Tarifit (Tarifit)
 * @author Jose77
 */
$allMessages['rif'] = array(
	'whitelisttablemodifyall' => 'Maṛṛa',
	'whitelisttableedit' => 'Arri',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 * @author Mihai
 */
$allMessages['ro'] = array(
	'whitelisttablemodifynone' => 'Nimic',
	'whitelisttableedit' => 'Modifică',
	'whitelisttableremove' => 'Elimină',
	'whitelistnever' => 'niciodată',
);

/** Russian (Русский)
 * @author Innv
 * @author Александр Сигачёв
 */
$allMessages['ru'] = array(
	'whitelisttableedit' => 'Править',
	'whitelisttableview' => 'Просмотр',
	'whitelistnewtableprocess' => 'Процесс',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$allMessages['sk'] = array(
	'whitelist-desc' => 'Upraviť oprávnenia prístupu používateľov',
	'whitelistedit' => 'Editor bielej listiny prístupu',
	'whitelist' => 'Dať stránky na bielu listinu',
	'mywhitelistpages' => 'Moje stránky',
	'whitelistfor' => '<center>Aktuálne informácie pre <b>$1<b></center>',
	'whitelisttablemodify' => 'Zmeniť',
	'whitelisttablemodifyall' => 'Všetky',
	'whitelisttablemodifynone' => 'Žiadne',
	'whitelisttablepage' => 'Wiki stránka',
	'whitelisttabletype' => 'Typ prístupu',
	'whitelisttableexpires' => 'Vyprší',
	'whitelisttablemodby' => 'Naspoledy zmenil',
	'whitelisttablemodon' => 'Naposledy zmenené',
	'whitelisttableedit' => 'Upraviť',
	'whitelisttableview' => 'Zobraziť',
	'whitelisttablenewdate' => 'Nový dátum:',
	'whitelisttablechangedate' => 'Zmeniť dátum vypršania',
	'whitelisttablesetedit' => 'Nastaviť na Upraviť',
	'whitelisttablesetview' => 'Nastaviť na Zobrazenie',
	'whitelisttableremove' => 'Odstrániť',
	'whitelistnewpagesfor' => 'Pridať nové stránky na bielu listinu <b>$1</b><br />
Ako zástupný znak použite buď * alebo %',
	'whitelistnewtabledate' => 'Dátum vypršania:',
	'whitelistnewtableedit' => 'Nastaviť na Upraviť',
	'whitelistnewtableview' => 'Nastaviť na Zobraziť',
	'whitelistnewtableprocess' => 'Spracovať',
	'whitelistnewtablereview' => 'Skontrolovať',
	'whitelistselectrestricted' => '== Vyberte meno používateľa ==',
	'whitelistpagelist' => 'stránky {{GRAMMAR:genitív|{{SITENAME}}}} pre $1',
	'whitelistnocalendar' => "<font color='red' size=3>Zdá sa, že nie je správne nainštalované rozšírenie [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], ktoré toto rozšírenie vyžaduje.</font>",
	'whitelistbadtitle' => 'Chybný názov -',
	'whitelistoverview' => '== Prehľad zmien $1 ==',
	'whitelistoverviewcd' => "* Zmena dátumu [[:$2|$2]] na '''$1'''",
	'whitelistoverviewsa' => "* Nastavenie prístupu [[:$2|$2]] na '''$1'''",
	'whitelistoverviewrm' => '* Odstránenie prístupu na [[:$1|$1]]',
	'whitelistoverviewna' => "* Pridanie prístupu [[:$1|$1]] na bielu listinu s prístupom '''$2''' a vypršaním '''$3'''",
	'whitelistrequest' => 'Požiadať o prístup k viacerým stránkam',
	'whitelistrequestmsg' => '$1 požiadal o prístup k nasledovným stránkam:

$2',
	'whitelistrequestconf' => 'Žiadosť o nové stránky bola odoslaná $1',
	'whitelistnonrestricted' => "Používateľ '''$1''' nie je obmedzený používateľ.
Táto stránka sa týka iba obmedzneých používateľov.",
	'whitelistnever' => 'nikdy',
	'whitelistnummatches' => '  - $1 výsledkov',
);

/** Serbian Cyrillic ekavian (ћирилица)
 * @author Sasa Stefanovic
 */
$allMessages['sr-ec'] = array(
	'whitelisttablemodifynone' => 'Нема',
	'whitelisttableedit' => 'Уреди',
	'whitelisttableremove' => 'Уклони',
);

/** Sundanese (Basa Sunda)
 * @author Irwangatot
 */
$allMessages['su'] = array(
	'whitelisttableedit' => 'Édit',
);

/** Swedish (Svenska)
 * @author M.M.S.
 * @author Sannab
 */
$allMessages['sv'] = array(
	'whitelist-desc' => 'Redigera åtkomsträttigheter för begränsade användare',
	'whitelistedit' => 'Rättighetsredigerare för vitlista',
	'whitelist' => 'Vitlistade sidor',
	'mywhitelistpages' => 'Mina sidor',
	'whitelistfor' => '<center>Nuvarande information för <b>$1<b></center>',
	'whitelisttablemodify' => 'Ändra',
	'whitelisttablemodifyall' => 'Alla',
	'whitelisttablemodifynone' => 'Ingen',
	'whitelisttablepage' => 'Wikisida',
	'whitelisttabletype' => 'Åtkomsttyp',
	'whitelisttableexpires' => 'Utgår',
	'whitelisttablemodby' => 'Senast ändrad av',
	'whitelisttablemodon' => 'Senast ändrad på',
	'whitelisttableedit' => 'Redigera',
	'whitelisttableview' => 'Visa',
	'whitelisttablenewdate' => 'Nytt datum:',
	'whitelisttablechangedate' => 'Ändra utgångsdatum',
	'whitelisttablesetedit' => 'Ange att redigera',
	'whitelisttablesetview' => 'Ange att visa',
	'whitelisttableremove' => 'Radera',
	'whitelistnewpagesfor' => 'Lägg till nya sidor till <b>$1s</b> vitlista<br />
Använd hellre * eller % som jokertecken',
	'whitelistnewtabledate' => 'Utgångsdatum:',
	'whitelistnewtableedit' => 'Ange att redigera',
	'whitelistnewtableview' => 'Ange att visa',
	'whitelistnewtableprocess' => 'Behandla',
	'whitelistnewtablereview' => 'Granska',
	'whitelistselectrestricted' => '== Ange begränsad användares namn ==',
	'whitelistpagelist' => '{{SITENAME}} sidor för $1',
	'whitelistnocalendar' => "<font color='red' size=3>Det verkar som [http://www.mediawiki.org/wiki/Extension:Usage_Statistics Extension:UsageStatistics], en förutsättning för detta programtillägg, inte har installerats ordentligt!</font>",
	'whitelistbadtitle' => 'Dålig titel -',
	'whitelistoverview' => '== Översikt av ändringar för $1 ==',
	'whitelistoverviewcd' => "* Ändrar datum till '''$1''' för [[:$2|$2]]",
	'whitelistoverviewsa' => "* Anger åtkomst till '''$1''' för [[:$2|$2]]",
	'whitelistoverviewrm' => '* Raderar åtkomst till [[:$1|$1]]',
	'whitelistoverviewna' => "* Lägger till [[:$1|$1]] till vitlista med åtkomst '''$2''' och '''$3''' utgångsdatum",
	'whitelistrequest' => 'Efterfråga åtkomst till mer sidor',
	'whitelistrequestmsg' => '$1 har efterfrågat åtkomst till följande sidor:

$2',
	'whitelistrequestconf' => 'Efterfrågan för nya sidor har sänts till $1',
	'whitelistnonrestricted' => "Användare '''$1''' är inte en begränsad användare.
Denna sida är endast tillämpbar på begränsade användare",
	'whitelistnever' => 'aldrig',
	'whitelistnummatches' => ' - $1 träffar',
);

/** Silesian (Ślůnski)
 * @author Herr Kriss
 * @author Pimke
 */
$allMessages['szl'] = array(
	'whitelisttableedit' => 'Sprowjéj',
	'whitelistbadtitle' => 'Zuy titel',
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
	'mywhitelistpages' => 'నా పేజీలు',
	'whitelisttablemodifyall' => 'అన్నీ',
	'whitelisttablepage' => 'వికీ పేజీ',
	'whitelisttableview' => 'చూడండి',
	'whitelisttablenewdate' => 'కొత్త తేదీ:',
	'whitelisttableremove' => 'తొలగించు',
	'whitelistnewtabledate' => 'కాల పరిమితి:',
	'whitelistnewtablereview' => 'సమీక్షించు',
	'whitelistpagelist' => '$1 కై {{SITENAME}} పేజీలు',
	'whitelistnummatches' => '  - $1 పోలికలు',
);

/** Tetum (Tetun)
 * @author MF-Warburg
 */
$allMessages['tet'] = array(
	'mywhitelistpages' => "Ha'u-nia pájina sira",
	'whitelisttablemodifyall' => 'Hotu',
	'whitelisttableedit' => 'Edita',
);

/** Tajik (Cyrillic) (Тоҷикӣ (Cyrillic))
 * @author Ibrahim
 */
$allMessages['tg-cyrl'] = array(
	'whitelist-desc' => 'Иҷозаҳои дастрасии корбарони маҳдудшударо вироиш кунед',
	'whitelist' => 'Саҳифаҳои Феҳристи сафед',
	'mywhitelistpages' => 'Саҳифаҳои Ман',
	'whitelistfor' => '<center>Иттилооти кунунӣ барои <b>$1</b></center>',
	'whitelisttablemodify' => 'Пиростан',
	'whitelisttablemodifyall' => 'Ҳама',
	'whitelisttablemodifynone' => 'Ҳеҷ',
	'whitelisttablepage' => 'Саҳифаи Вики',
	'whitelisttabletype' => 'Навъи Дастрасӣ',
	'whitelisttableexpires' => 'Сипарӣ мешавад дар',
	'whitelisttablemodby' => 'Охирин маротиба пироста шуда буд тавассути',
	'whitelisttablemodon' => 'Охирин маротиба пироста шуда буд дар',
	'whitelisttableedit' => 'Вироиш',
	'whitelisttableview' => 'Дидан',
	'whitelisttablenewdate' => 'Таърихи Нав:',
	'whitelisttablechangedate' => 'Тағйири Таърихи Баинтиҳорасӣ',
	'whitelisttableremove' => 'Ҳазф',
	'whitelistnewtabledate' => 'Таърихи Баинтиҳорасӣ:',
	'whitelistnewtableprocess' => 'Раванд',
	'whitelistnewtablereview' => 'Пешнамоиш',
	'whitelistbadtitle' => 'Унвони номуносиб -',
	'whitelistrequest' => 'Ба саҳифаҳои бештар дастрасиро дархост кунед',
	'whitelistrequestmsg' => '$1 дастрасиро барои саҳифаҳои зерин дархост кард:

$2',
	'whitelistrequestconf' => 'Дархост барои саҳифаҳои ҷадид ба $1 фиристода шуд',
	'whitelistnever' => 'ҳеҷгоҳ',
	'whitelistnummatches' => ' - $1 мутобиқат мекунад',
);

/** Turkish (Türkçe)
 * @author Karduelis
 */
$allMessages['tr'] = array(
	'mywhitelistpages' => 'Sayfalarım',
	'whitelisttablemodifyall' => 'Hepsi',
	'whitelisttablemodifynone' => 'Hiçbiri',
	'whitelisttableedit' => 'Değiştir',
	'whitelisttableremove' => 'Kaldır',
	'whitelistbadtitle' => 'Geçersiz başlık -',
	'whitelistnever' => 'asla',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$allMessages['vi'] = array(
	'whitelisttablemodifyall' => 'Tất cả',
	'whitelisttablemodifynone' => 'Không có',
	'whitelisttableedit' => 'Sửa',
	'whitelistbadtitle' => 'Tựa trang sai –',
	'whitelistnever' => 'không bao giờ',
);

/** Taiwan Chinese (‪中文(台灣)‬)
 * @author Roc michael
 */
$allMessages['zh-tw'] = array(
	'whitelist-desc' => '設定受限制用戶的存取權',
	'whitelistedit' => '授權名單內的編輯用戶',
	'whitelist' => '授權清單頁面',
	'mywhitelistpages' => '我的頁面',
	'whitelistfor' => '<center><b>$1</b>的當今訊息</center>',
	'whitelisttablemodify' => '修訂',
	'whitelisttablemodifyall' => '全部',
	'whitelisttablemodifynone' => '無',
	'whitelisttablepage' => 'wiki頁面',
	'whitelisttabletype' => '存取型態',
	'whitelisttableexpires' => '到期日',
	'whitelisttablemodby' => '最後編輯者',
	'whitelisttablemodon' => '最後編輯時間',
	'whitelisttableedit' => '編輯',
	'whitelisttableview' => '查看',
	'whitelisttablenewdate' => '新日期：',
	'whitelisttablechangedate' => '更改到期日',
	'whitelisttablesetedit' => '設為可編輯',
	'whitelisttablesetview' => '設為可查看',
	'whitelisttableremove' => '刪除',
	'whitelistnewpagesfor' => '增加頁面於<b>$1</b>的編輯清單<br />
請用* 或 % 做為萬用字元。',
	'whitelistnewtabledate' => '到期日：',
	'whitelistnewtableedit' => '設為可編輯',
	'whitelistnewtableview' => '設為可查看',
	'whitelistselectrestricted' => '== 選取受限制用戶姓名 ==',
	'whitelistnummatches' => '-$1筆相符',
);

