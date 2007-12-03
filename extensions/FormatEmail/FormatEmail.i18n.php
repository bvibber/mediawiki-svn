<?php
/**
 * Internationalisation file for extension FormatEmail.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'email_header' => '',
	'email_footer' => '

----------------------------------------------------------------------------
This email has been sent to you through the {{SITENAME}} email system by $1.

$2',
);

$messages ['fr'] = array (
	'email_footer' => '

----------------------------------------------------------------------------
Ce courrier a vous a été envoyé grâce au systeme de messagerie de {{SITENAME}} par $1.

$2',
);

$messages['no'] = array(
	'email_footer' => '

----------------------------------------------------------------------------
Denne e-posten har blitt sendt deg fra $1 via {{SITENAME}}s e-postsystem.',
);

$messages['sk'] = array(
	'email_footer' => '

----------------------------------------------------------------------------
Tento email vám poslal $1 pomocou emailového systému {{GRAMMAR:genitív|{{SITENAME}}}}.

$2',
);
