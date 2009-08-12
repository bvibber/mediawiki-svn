<?php
/**
 * Internationalisation file for extension Negref.
 *
 * @addtogroup Extensions
 */

require_once( dirname(__FILE__) . '/Negref.i18n.magic.php' );

$messages = array();

/** English
 * @author Daniel Friesen
 */
$messages['en'] = array(
	'negref-desc'      => 'Provides a tag to negotiate the location of any <nowiki><ref/></nowiki> tags inside of input text to fix some template use cases',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'negref-desc' => 'Biedt een tag om de plaats van de tag <nowiki><ref/></nowiki> te bepalen binnen invoertekst om bepaald gebruik van sjablonen te repareren',
);

