<?php
/**
 * Internationalisation file for extension EmailArticle.
 *
 * @addtogroup Extensions
*/

$messages = array();

/** English
 * @author Nad
 */
$messages['en'] = array(
	'emailarticle'        => 'Email article',
	'ea-desc'             => 'Send rendered HTML article to an email address or list of addresses using [http://phpmailer.sourceforge.net phpmailer].',
	'ea-heading'          => "=== Emailing [[$1]] article ===",
	'ea-noarticle'        => "Please specify an article to send, for example [[Special:EmailArticle/Main Page]].",
	'ea-norecipients'     => "No valid email addresses found!",
	'ea-listrecipients'   => "=== List of $1 {{PLURAL:$1|recipient|recipients}} ===",
	'ea-error'            => "'''Error sending [[$1]]:''' ''$2''",
	'ea-sent'             => "Article [[$1]] sent successfully to '''$2''' {{PLURAL:$2|recipient|recipients}} by [[User:$3|$3]].",
	'ea-selectrecipients' => 'Select recipients',
	'ea-compose'          => 'Compose content',
	'ea-selectlist'       => "Additional recipients as article titles or email addresses
*''separate items with , ; * \\n
*''list can contain templates and parser-functions''",
	'ea-show'             => 'Show recipients',
	'ea-send'             => 'Send!',
	'ea-subject'          => 'Enter a subject line for the email',
	'ea-header'           => 'Prepend content with optional message (wikitext)',
	'ea-selectcss'        => 'Select a CSS stylesheet',
);
