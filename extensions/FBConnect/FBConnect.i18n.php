<?php
/*
 * Copyright © 2008-2010 Garrett Brown <http://www.mediawiki.org/wiki/User:Gbruin>
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program. If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * FBConnect.i18n.php
 * 
 * Internationalization file for FBConnect.
 */


$messages = array();

/** English */
$messages['en'] = array(
// Extension name
	'fbconnect'               => 'Facebook Connect',
	'fbconnect-desc'          => 'Enables users to [[Special:Connect|Connect]] with their [http://www.facebook.com Facebook] accounts.
Offers authentification based on Facebook groups and the use of FBML in wiki text',
// Group containing Facebook Connect users
	'group-fb-user'           => 'Facebook Connect users',
	'group-fb-user-member'    => 'Facebook Connect user',
	'grouppage-fb-user'       => '{{ns:project}}:Facebook Connect users',
	// Group for Facebook Connect users beloning to the group specified by $wgFbUserRightsFromGroup
	'group-fb-groupie'        => 'Group members',
	'group-fb-groupie-member' => 'Group member',
	'grouppage-fb-groupie'    => '{{ns:project}}:Group members',
	// Officers of the Facebook group
	'group-fb-officer'        => 'Group officers',
	'group-fb-officer-member' => 'Group officer',
	'grouppage-fb-officer'    => '{{ns:project}}:Group officers',
	// Admins of the Facebook group
	'group-fb-admin'          => 'Group admins',
	'group-fb-admin-member'   => 'Group administrator',
	'grouppage-fb-admin'      => '{{ns:project}}:Group admins',
	// Personal toolbar
	'fbconnect-connect'  => 'Log in with Facebook Connect',
	'fbconnect-convert'  => 'Connect this account with Facebook',
	'fbconnect-logout'   => 'Logout of Facebook',
	'fbconnect-link'     => 'Back to facebook.com',
	
	// Special:Connect
	'fbconnect-title'    => 'Connect account with Facebook',
	'fbconnect-intro'    => 'This wiki is enabled with Facebook Connect, the next evolution of Facebook platform.
This means that when you are connected, in addition to the normal [[Wikipedia:Help:Logging in#Why log in?|benefits]] you see when logging in, you will be able to take advantage of some extra features...',
	'fbconnect-click-to-login' => 'Click this button to login to this site via Facebook',
	'fbconnect-click-to-connect-existing' => 'Click this button to connect your Facebook account to $1',
	'fbconnect-conv'     => 'Convenience',
	'fbconnect-convdesc' => 'Connected users are automatically logged you in.
If permission is given, then this wiki can even use Facebook as an e-mail proxy so you can continue to receive important notifications without revealing your e-mail address.',
	'fbconnect-fbml'     => 'Facebook markup manguage',
	'fbconnect-fbmldesc' => 'Facebook has provided a bunch of built-in tags that will render dynamic data.
Many of these tags can be included in wiki text, and will be rendered differently depending on which connected user they are being viewed by.',
	'fbconnect-comm'     => 'Communication',
	'fbconnect-commdesc' => 'Facebook cnnect ushers in a whole new level of networking.
See which of your friends are using the wiki, and optionally share your actions with your friends through the Facebook news feed.',
	'fbconnect-welcome'  => 'Welcome, Facebook Connect user!',
	'fbconnect-loginbox' => "Or '''login''' with Facebook:
	
$1",
	'fbconnect-merge'    => 'Merge your wiki account with your Facebook ID',
	'fbconnect-mergebox' => 'This feature has not yet been implemented.
Accounts can be merged manually with [[Special:Renameuser]] if it is installed.
For more information, please visit [[MediaWikiWiki:Extension:Renameuser|Extension:Renameuser]].

$1

Note: This can be undone by a sysop.',
	'fbconnect-logoutbox'=> '$1
	
This will also log you out of Facebook and all connected sites, including this wiki.',
	'fbconnect-listusers-header' => '$1 and $2 privileges are automatically transfered from the officer and admin titles of the Facebook group $3.

For more info, please contact the group creator $4.',
	// Prefix to use for automatically-generated usernames
	'fbconnect-usernameprefix' => 'FacebookUser',
	// Special:Connect
	'fbconnect-error' => 'Verification error',
	'fbconnect-errortext' => 'An error occured during verification with Facebook Connect.',
	'fbconnect-cancel' => 'Action cancelled',
	'fbconnect-canceltext' => 'The previous action was cancelled by the user.',
	'fbconnect-invalid' => 'Invalid option',
	'fbconnect-invalidtext' => 'The selection made on the previous page was invalid.',
	'fbconnect-success' => 'Facebook verification succeeded',
	'fbconnect-successtext' => 'You have been successfully logged in with Facebook Connect.',
	#'fbconnect-optional' => 'Optional',
	#'fbconnect-required' => 'Required',
	'fbconnect-nickname' => 'Nickname',
	'fbconnect-fullname' => 'Fullname',
	'fbconnect-email' => 'E-mail address',
	'fbconnect-language' => 'Language',
	'fbconnect-timecorrection' => 'Time zone correction (hours)',
	'fbconnect-chooselegend' => 'Username choice',
	'fbconnect-chooseinstructions' => 'All users need a nickname; you can choose one from the options below.',
	'fbconnect-invalidname' => 'The nickname you chose is already taken or not a valid nickname.
Please chose a different one.',
	'fbconnect-choosenick' => 'Your Facebook profile name ($1)',
	'fbconnect-choosefirst' => 'Your first name ($1)',
	'fbconnect-choosefull' => 'Your full name ($1)',
	'fbconnect-chooseauto' => 'An auto-generated name ($1)',
	'fbconnect-choosemanual' => 'A name of your choice:',
	'fbconnect-chooseexisting' => 'An existing account on this wiki',
	'fbconnect-chooseusername' => 'Username:',
	'fbconnect-choosepassword' => 'Password:',
	'fbconnect-updateuserinfo' => 'Update the following personal information:',
	'fbconnect-alreadyloggedin' => "'''You are already logged in, $1!'''
	
If you want to use Facebook Connect to log in in the future, you can [[Special:Connect/Convert|convert your account to use Facebook Connect]].",
	/*
	'fbconnect-convertinstructions' => 'This form lets you change your user account to use an OpenID URL or add more OpenID URLs',
	'fbconnect-convertoraddmoreids' => 'Convert to OpenID or add another OpenID URL',
	'fbconnect-convertsuccess' => 'Successfully converted to OpenID',
	'fbconnect-convertsuccesstext' => 'You have successfully converted your OpenID to $1.',
	'fbconnect-convertyourstext' => 'That is already your OpenID.',
	'fbconnect-convertothertext' => 'That is someone else\'s OpenID.',
	*/
	
	'fbconnect-error-creating-user' => 'Error creating the user in the local database.',
	'fbconnect-error-user-creation-hook-aborted' => 'A hook (extension) aborted the account creation with the message: $1',

	'fbconnect-prefstext' => 'Facebook Connect',
	'fbconnect-link-to-profile' => 'Facebook profile',
	'fbconnect-prefsheader' => "To control which events will push an item to your Facebook news feed, <a id='fbConnectPushEventBar_show' href='#'>show preferences</a> <a id='fbConnectPushEventBar_hide' href='#' style='display:none'>hide preferences</a>",
	'fbconnect-prefs-can-be-updated' => 'You can update these any time by visiting the "$1" tab of your preferences page.',
);

/** Message documentation (Message documentation)
 * @author Garrett Brown
 */
$messages['qqq'] = array(
	'fbconnect-desc' => 'Short description of the FBConnect extension, shown in [[Special:Version]]. Do not translate or change links.',
	'fbconnect-email' => '{{Identical|E-mail address}}',
	'fbconnect-language' => '{{Identical|Language}}',
	'fbconnect-choosepassword' => '{{Identical|Password}}',
	'fbconnect-alreadyloggedin' => '$1 is a user name.',
	'fbconnect-prefstext' => 'FBConnect preferences tab text above the list of preferences',
	'fbconnect-link-to-profile' => 'Appears next to the user\\s name in their Preferences page and this text is made into link to the profile of that user if they are connected.',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'fbconnect-link' => 'Terug na facebook.com',
	'fbconnect-comm' => 'Kommunikasie',
	'fbconnect-error' => 'Verifikasiefout',
	'fbconnect-invalid' => 'Ongeldige opsie',
	'fbconnect-nickname' => 'Bynaam',
	'fbconnect-fullname' => 'Volle naam',
	'fbconnect-email' => 'E-posadres',
	'fbconnect-language' => 'Taal',
	'fbconnect-choosefirst' => 'U eerste naam ($1)',
	'fbconnect-choosefull' => 'U volledige naam ($1)',
	'fbconnect-chooseauto' => "'n Outomaties gegenereerde naam ($1)",
	'fbconnect-choosemanual' => "'n Naam van u keuse:",
	'fbconnect-chooseexisting' => "'n Bestaande gebruiker op hierdie wiki:",
	'fbconnect-chooseusername' => 'Gebruikersnaam:',
	'fbconnect-choosepassword' => 'Wagwoord:',
	'fbconnect-link-to-profile' => 'Facebook-profiel',
);

/** German (Deutsch)
 * @author Kghbln
 */
$messages['de'] = array(
	'fbconnect' => 'Facebook Connect',
	'fbconnect-desc' => 'Stellt eine [[Special:Connect|Spezialseite]] bereit mit der Benutzer eine Verbindung mit ihrem [http://de-de.facebook.com/ Facebook-Konten] herstellen können.
Zudem wird die Authentifizierung basierend auf Facebook-Gruppen und der Einsatz von FBML in Wikitext ermöglicht.',
	'group-fb-user' => 'Facebook-Connect-Benutzer',
	'group-fb-user-member' => 'Facebook-Connect-Benutzer',
	'grouppage-fb-user' => '{{ns:project}}:Facebook-Connect-Benutzer',
	'group-fb-groupie' => 'Gruppenmitglieder',
	'group-fb-groupie-member' => 'Gruppenmitglied',
	'grouppage-fb-groupie' => '{{ns:project}}:Gruppenmitglieder',
	'group-fb-officer' => 'Gruppenrechteverwalter',
	'group-fb-officer-member' => 'Gruppenrechteverwalter',
	'grouppage-fb-officer' => '{{ns:project}}:Gruppenrechteverwalter',
	'group-fb-admin' => 'Gruppenadministratoren',
	'group-fb-admin-member' => 'Gruppenadministrator',
	'grouppage-fb-admin' => '{{ns:project}}:Gruppenadministratoren',
	'fbconnect-connect' => 'Anmelden mit Facebook Connect',
	'fbconnect-convert' => 'Dieses Konto mit Facebook verknüpfen',
	'fbconnect-logout' => 'Aus Facebook abmelden',
	'fbconnect-link' => 'Zurück zu de-de.facebook.com',
	'fbconnect-title' => 'Konto mit Facebook verknüpfen',
	'fbconnect-intro' => 'Dieses Wiki hat Facebook Connect, die nächsten Weiterentwicklung der Plattform Facebook, aktiviert.
Dies bedeutet, dass man, sofern man angemeldet ist, zusätzlich zu den herkömmlichen [[Wikipedia:Help:Logging in#Why log in?|Vorteilen]] einer Anmeldung, weitere zusätzliche Funktionen nutzen kann...',
	'fbconnect-click-to-login' => 'Auf diese Schaltfläche klicken, um sich auf diesem Wiki via Facebook anzumelden',
	'fbconnect-click-to-connect-existing' => 'Auf diese Schaltfläche klicken, um das Facebook-Konto mit $1 zu verknüpfen',
	'fbconnect-conv' => 'Bequemlichkeit',
	'fbconnect-convdesc' => 'Verknüpfte Benutzer werden automatisch angemeldet.
Sofern die Erlaubnis vorliegt, kann dieses Wiki sogar Facebook als Kommunikationsschnittstelle für E-Mails nutzen, so dass man weiterhin wichtige Nachrichten erhalten kann, ohne hierzu die E-Mail-Adresse offenlegen zu müssen.',
	'fbconnect-fbml' => 'Facebook Auszeichnungssprache',
	'fbconnect-fbmldesc' => 'Facebook stellt ein Bündel integrierter Tags bereit, die dynamisch erzeugte Daten verarbeiten können.
Viele dieser Tags können in Wikitext einbezogen werden. Sie werden, je nach auf dem Wiki angemeldeten Benutzer, individuell mit Daten versehen und ausgegeben.',
	'fbconnect-comm' => 'Kommunikation',
	'fbconnect-commdesc' => 'Facebook Connect ermöglicht eine vollkommen neuartige Möglichkeit des Netzwerkens.
Man kann sehen welche der eigenen Freunde das Wiki nutzen und, sofern gewünscht, ihnen die eigenen Aktionen über den eigenen Facebook-Newsfeed ausgeben lassen.',
	'fbconnect-welcome' => 'Willkommen, Facebook-Connect-Benutzer!',
	'fbconnect-loginbox' => "Oder via Facebook '''anmelden''':

$1",
	'fbconnect-merge' => 'Das Wikikonto mit der Facebook-ID verknüpfen',
	'fbconnect-mergebox' => 'Diese Funktion ist noch nicht vorhanden.
Konten können manuell über die Spezialseite [[Special:Renameuser|Benutzer umbenennen]] verknüpft werden, sofern sie auf diesem Wiki verfügbar ist.
Für weitere Informationen kann man die Seite [[MediaWikiWiki:Extension:Renameuser|Extension:Renameuser]] aufsuchen.

$1

Hinweis: Dies kann durch einen Gruppenadministrator rückgängig gemacht werden.',
	'fbconnect-logoutbox' => '$1

Dies führt zu einer Abmeldung von Facebook und allen verknüpften Websites, einschließlich dieses Wikis.',
	'fbconnect-listusers-header' => 'Die Privilegien $1 und $2 werden automatisch von denen des Gruppenrechteverwalters und Gruppenadministrators der Facebook-Gruppe $3 übertragen.

Für weitere Informationen kann man den Gruppenersteller $4 kontaktieren.',
	'fbconnect-usernameprefix' => 'Facebook-Benutzer',
	'fbconnect-error' => 'Überprüfungsfehler',
	'fbconnect-errortext' => 'Ein Fehler trat während der Überprüfung mit Facebook Connect auf.',
	'fbconnect-cancel' => 'Aktion abgebrochen',
	'fbconnect-canceltext' => 'Die vorherige Aktion wurde vom Benutzer abgebrochen.',
	'fbconnect-invalid' => 'Ungültige Option',
	'fbconnect-invalidtext' => 'Die Auswahl, die auf der vorherigen Seite getroffen wurde, ist ungültig.',
	'fbconnect-success' => 'Facebook Connect-Überprüfung erfolgreich',
	'fbconnect-successtext' => 'Die Anmeldung via Facebook Connect war erfolgreich.',
	'fbconnect-nickname' => 'Benutzername',
	'fbconnect-fullname' => 'Vollständiger Name',
	'fbconnect-email' => 'E-Mail-Adresse',
	'fbconnect-language' => 'Sprache',
	'fbconnect-timecorrection' => 'Zeitzonenkorrektur (Stunden)',
	'fbconnect-chooselegend' => 'Wahl des Benutzernamens',
	'fbconnect-chooseinstructions' => 'Alle Benutzer benötigen einen Benutzernamen. Es kann einer aus der untenstehenden Liste ausgewählt werden.',
	'fbconnect-invalidname' => 'Der ausgewählte Benutzername wurde bereits vergeben oder ist nicht zulässig.
Bitte einen anderen auswählen.',
	'fbconnect-choosenick' => 'Der Profilname auf Facebook ($1)',
	'fbconnect-choosefirst' => 'Vorname ($1)',
	'fbconnect-choosefull' => 'Vollständiger Name ($1)',
	'fbconnect-chooseauto' => 'Ein automatisch erzeugter Name ($1)',
	'fbconnect-choosemanual' => 'Ein Name der Wahl:',
	'fbconnect-chooseexisting' => 'Ein bestehendes Benutzerkonto in diesem Wiki',
	'fbconnect-chooseusername' => 'Benutzername:',
	'fbconnect-choosepassword' => 'Passwort:',
	'fbconnect-updateuserinfo' => 'Die folgenden persönlichen Angaben müssen aktualisiert werden:',
	'fbconnect-alreadyloggedin' => "'''Du bist bereits angemeldet, $1!'''

Sofern OpenID für künftige Anmeldevorgänge genutzt werden soll, kann das [[Special:Connect/Convert|Benutzerkonto für die Nutzung durch Facebook Connect eingerichtet werden]].",
	'fbconnect-error-creating-user' => 'Fehler beim Erstellen des Benutzers in der lokalen Datenbank.',
	'fbconnect-error-user-creation-hook-aborted' => 'Die Schnittstelle einer Softwareerweiterung hat die Benutzerkontoerstellung mit folgender Nachricht abgebrochen: $1',
	'fbconnect-prefstext' => 'Facebook Connect',
	'fbconnect-link-to-profile' => 'Facebook-Profil',
	'fbconnect-prefsheader' => "Einstellungen zu den Aktionen, die über den eigenen Facebook-Newsfeed ausgegeben werden sollen: <a id='fbConnectPushEventBar_show' href='#'>Einstellungen anzeigen</a> <a id='fbConnectPushEventBar_hide' href='#' style='display:none'>Einstellungen ausblenden</a>",
	'fbconnect-prefs-can-be-updated' => 'Sie können jederzeit aktualisiert werden, indem man sie unter der Registerkarte „$1“ auf der Seite Einstellungen ändert.',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'fbconnect' => 'Facebook Connect',
	'fbconnect-desc' => 'Permitte al usatores de [[Special:Connect|connecter se]] con lor contos de [http://www.facebook.com Facebook].
Offere authentication a base de gruppos de Facebook e le uso de FBML in texto wiki.',
	'group-fb-user' => 'Usatores de Facebook Connect',
	'group-fb-user-member' => 'Usator de Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Usatores de Facebook Connect',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'fbconnect' => 'Facebook Connect',
	'group-fb-user' => 'Facebook Connect Benotzer',
	'group-fb-user-member' => 'Facebook-Connect-Benotzer',
	'grouppage-fb-user' => '{{ns:project}}:Facebook-Connect-Benotzer',
	'fbconnect-convert' => 'Dëse Kont mat Facebook verbannen',
	'fbconnect-link' => 'Zréck op facebook.com',
	'fbconnect-title' => 'Kont mat Facebook verbannen',
	'fbconnect-conv' => 'Bequemlechkeet',
	'fbconnect-comm' => 'Kommunikatioun',
	'fbconnect-welcome' => 'Wëllkomm, Facebook-Connect-Benotzer!',
	'fbconnect-merge' => 'Verbannt Äre Wiki-Kont mat Ärer Facebook-ID',
	'fbconnect-usernameprefix' => 'Facebook-Benotzer',
	'fbconnect-nickname' => 'Spëtznumm',
	'fbconnect-fullname' => 'Ganzen Numm',
	'fbconnect-language' => 'Sprooch',
	'fbconnect-choosefirst' => 'Äre Virnumm ($1)',
	'fbconnect-choosefull' => 'Äre ganzen Numm ($1)',
	'fbconnect-choosemanual' => 'En Numm vun Ärer Wiel:',
	'fbconnect-chooseusername' => 'Benotzernumm:',
	'fbconnect-choosepassword' => 'Passwuert:',
	'fbconnect-link-to-profile' => 'Facebook-Profil',
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'group-fb-user' => 'Użytkownicy Facebook Connect',
	'group-fb-user-member' => 'Użytkownik Facebook Connect',
	'grouppage-fb-user' => '{{ns:project}}:Użytkownicy Facebook Connect',
	'group-fb-groupie' => 'Członkowie grupy',
	'group-fb-groupie-member' => 'Członek grupy',
	'grouppage-fb-groupie' => '{{ns:project}}:Członkowie grupy',
	'group-fb-officer' => 'Przywódcy grupy',
	'group-fb-officer-member' => 'Przywódca grupy',
	'grouppage-fb-officer' => '{{ns:project}}:Przywódcy grupy',
	'group-fb-admin' => 'Administratorzy grupy',
	'group-fb-admin-member' => 'Administrator grupy',
	'grouppage-fb-admin' => '{{ns:project}}:Administratorzy grupy',
	'fbconnect-connect' => 'Zaloguj przy pomocy Facebook Connect',
	'fbconnect-convert' => 'Połącz to konto z Facebookiem',
	'fbconnect-logout' => 'Wyloguj się z Facebooka',
	'fbconnect-link' => 'Powrót na facebook.com',
	'fbconnect-title' => 'Połącz konto z Facebookiem',
);

/** Russian (Русский)
 * @author Eleferen
 */
$messages['ru'] = array(
	'fbconnect-choosepassword' => 'Пароль:',
);

