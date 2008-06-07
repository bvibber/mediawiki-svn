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
