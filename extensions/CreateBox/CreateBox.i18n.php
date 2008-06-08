<?php
/**
 * Internationalisation file for the CreateBox extension.
 *
 * @ingroup Extensions
 * @author Ross McClure
 */

$messages = array();

/** English
 * @author Ross McClure
 */
$messages['en'] = array(
	'createbox-desc'   => 'Specialised inputbox for page creation',
	'createbox-create' => 'Create',
	'createbox-exists' => "Sorry, \"'''{{FULLPAGENAME}}'''\" already " .
			"exists.\n\nYou cannot create this page, but you can " .
			"[{{fullurl:{{FULLPAGENAME}}|action=edit}} edit it], " .
			"[{{fullurl:{{FULLPAGENAME}}}} read it], or choose to " .
			"create a different page using the box below.\n\n" .
			"<createbox>break=no</createbox>",
);

/** Finnish (Suomi)
 * @author Jack Phoenix
 */
$messages['fi'] = array (
	'createbox-create' => 'Luo',
	'createbox-exists' => "Pahoittelut, \"'''{{FULLPAGENAME}}'''\" on jo " .
			"olemassa.\n\nEt voi luoda tätä sivua, mutta voit " .
			"[{{fullurl:{{FULLPAGENAME}}|action=edit}} muokata sitä], " .
			"[{{fullurl:{{FULLPAGENAME}}}} lukea sitä], tai luoda " .
			"erilaisen sivun allaolevaa laatikkoa käyttäen.\n\n" .
			"<createbox>break=no</createbox>",
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'createbox-create' => 'Maachen',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'createbox-desc'   => "Aangepast invoerveld voor het aanmaken van nieuwe pagina's",
	'createbox-create' => 'Aanmaken',
	'createbox-exists' => "\"'''{{FULLPAGENAME}}'''\" bestaat al.

U kunt deze pagina niet aanmaken, maar u kunt deze [{{fullurl:{{FULLPAGENAME}}|action=edit}} bewerken], [{{fullurl:{{FULLPAGENAME}}}} bekijken], of een andere pagina aanmaken via het onderstaande formulier.

<createbox>break=no</createbox>",
);

/** Swedish (Svenska)
 * @author M.M.S.
 */
$messages['sv'] = array(
	'createbox-desc'   => 'Specialiserad formulärbox för sidskapning',
	'createbox-create' => 'Skapa',
	'createbox-exists' => "Beklagar, \"'''{{FULLPAGENAME}}'''\" existerar redan.

Du kan inte skapa den här sidan, men du kan [{{fullurl:{{FULLPAGENAME}}|action=edit}} redigera den], [{{fullurl:{{FULLPAGENAME}}}} läsa den], eller välja att skapa en annan sida genom att använda boxen nedan.

<createbox>break=no</createbox>",
);

