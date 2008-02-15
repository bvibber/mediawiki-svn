<?php
/**
 * Internationalisation file for extension CrosswikiBlock.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	# Special page
	'crosswikiblock-desc'       => 'Allows to block users on other wikis using a [[Special:Crosswikiblock|special page]]',
	'crosswikiblock'            => 'Block user on other wiki',
	'crosswikiblock-header'     => 'This page allows to block user on other wiki.
Please check if you are allowed to act on this wiki and your actions match all policies.',
	'crosswikiblock-target'     => 'IP address or username and destination wiki:',
	'crosswikiblock-expiry'     => 'Expiry:',
	'crosswikiblock-reason'     => 'Reason:',
	'crosswikiblock-submit'     => 'Block this user',
	'crosswikiblock-anononly'   => 'Block anonymous users only',
	'crosswikiblock-nocreate'   => 'Prevent account creation',
	'crosswikiblock-autoblock'  => 'Automatically block the last IP address used by this user, and any subsequent IPs they try to edit from',
	'crosswikiblock-noemail'    => 'Prevent user from sending e-mail',

	# Errors and success message
	'crosswikiblock-nousername'     => 'No username was inputed',
	'crosswikiblock-local'          => 'Local blocks are not supported via this interface. Use [[Special:Blockip]]',
	'crosswikiblock-dbnotfound'     => 'Database $1 doesn\'t exist',
	'crosswikiblock-noname'         => '"$1" isn\'t a valid username.',
	'crosswikiblock-nouser'         => 'User "$3" is not found.',
	'crosswikiblock-noexpiry'       => 'Invalid expiry: $1.',
	'crosswikiblock-noreason'       => 'No reason specified.',
	'crosswikiblock-notoken'        => 'Invalid edit token.',
	'crosswikiblock-alreadyblocked' => 'User $3 is already blocked.',
	'crosswikiblock-success'        => "User '''$3''' blocked successfully.

Return to:
* [[Special:CrosswikiBlock|Block form]]
* [[$4]]",
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	# Special page
	'crosswikiblock-desc'       => 'Erlaubt die Sperre von Benutzern in anderen Wikis über eine [[Special:Crosswikiblock|Spezialseite]]',
	'crosswikiblock'            => 'Sperre Benutzer in einem anderen Wiki',
	'crosswikiblock-header'     => 'Diese Spezialseite erlaubt die Sperre eines Benutzers in einem anderen Wiki.
	Bitte prüfe, ob du die Befugnis hast, in diesem anderen Wiki zu sperren und ob deine Aktion deren Richtlinien entspricht.',
	'crosswikiblock-target'     => 'IP-Adresse oder Benutzername und Zielwiki:',
	'crosswikiblock-expiry'     => 'Sperrdauer:',
	'crosswikiblock-reason'     => 'Begründung:',
	'crosswikiblock-submit'     => 'IP-Adresse/Benutzer sperren',
	'crosswikiblock-anononly'   => 'Sperre nur anonyme Benutzer (angemeldete Benutzer mit dieser IP-Adresse werden nicht gesperrt). In vielen Fällen empfehlenswert.',
	'crosswikiblock-nocreate'   => 'Erstellung von Benutzerkonten verhindern',
	'crosswikiblock-autoblock'  => 'Sperre die aktuell von diesem Benutzer genutzte IP-Adresse sowie automatisch alle folgenden, von denen aus er Bearbeitungen oder das Anlegen von Benutzeraccounts versucht.',
	'crosswikiblock-noemail'    => 'E-Mail-Versand sperren',

	# Errors and success message
	'crosswikiblock-nousername'     => 'Es wurde kein Benutzername eingegeben',
	'crosswikiblock-local'          => 'Lokale Sperren werden durch dieses Interface nicht unterstützt. Benutze [[{{#special:Blockip}}]]',
	'crosswikiblock-dbnotfound'     => 'Datenbank $1 ist nicht vorhanden',
	'crosswikiblock-noname'         => '„$1“ ist kein gültiger Benutzername.',
	'crosswikiblock-nouser'         => 'Benutzer „$3“ nicht gefunden.',
	'crosswikiblock-noexpiry'       => 'Ungültige Sperrdauer: $1.',
	'crosswikiblock-noreason'       => 'Begründung fehlt.',
	'crosswikiblock-notoken'        => 'Ungültiges Bearbeitungs-Token.',
	'crosswikiblock-alreadyblocked' => 'Benutzer „$3“ ist bereits gesperrt.',
	'crosswikiblock-success'        => "Benutzer '''„$3“''' erfolgreich gesperrt.

Zurück zu:
* [[Special:CrosswikiBlock|Sperrformular]]
* [[$4]]",
);

/** Dutch (Nederlands)
 * @author SPQRobin
 */
$messages['nl'] = array(
	'crosswikiblock-desc'           => 'Laat toe om gebruikers op andere wikis te blokkeren via een [[Special:Crosswikiblock|speciale pagina]]',
	'crosswikiblock'                => 'Gebruiker blokkeren op een andere wiki',
	'crosswikiblock-header'         => 'Deze pagina laat toe om gebruikers te blokkeren op een andere wiki.
Gelieve te controleren of u de juiste rechten hebt op deze wiki en of uw acties het beleid volgt.',
	'crosswikiblock-target'         => 'IP-adres of gebruikersnaam en bestemmingswiki:',
	'crosswikiblock-expiry'         => 'Duur:',
	'crosswikiblock-reason'         => 'Reden:',
	'crosswikiblock-submit'         => 'Deze gebruiker blokkeren',
	'crosswikiblock-anononly'       => 'Alleen anonieme gebruikers blokkeren',
	'crosswikiblock-nocreate'       => 'Gebruiker aanmaken voorkomen',
	'crosswikiblock-autoblock'      => "Automatisch het laatste IP-adres gebruikt door deze gebruiker blokkeren, en elke volgende IP's waarmee ze proberen te bewerken",
	'crosswikiblock-noemail'        => 'Het verzenden van e-mails door deze gebruiker voorkomen',
	'crosswikiblock-nousername'     => 'Er werd geen gebruikersnaam opgegeven',
	'crosswikiblock-local'          => 'Plaatselijke blokkades worden niet ondersteund door dit formulier. Gebruik daarvoor [[Special:Blockip]].',
	'crosswikiblock-dbnotfound'     => 'Database $1 bestaat niet',
	'crosswikiblock-noname'         => '"$1" is geen geldige gebruikersnaam.',
	'crosswikiblock-nouser'         => 'Gebruiker "$3" is niet gevonden.',
	'crosswikiblock-noexpiry'       => 'Ongeldige duur: $1.',
	'crosswikiblock-noreason'       => 'Geen reden opgegeven.',
	'crosswikiblock-alreadyblocked' => 'Gebruiker $3 is al geblokkeerd.',
	'crosswikiblock-success'        => "Gebruiker '''$3''' succesvol geblokkeerd.

Teruggaan naar:
* [[Special:CrosswikiBlock|Blokkeerformulier]]
* [[$4]]",
);

/** Polish (Polski)
 * @author Equadus
 */
$messages['pl'] = array(
	'crosswikiblock-dbnotfound' => 'Baza $1 nie istnieje',
);

