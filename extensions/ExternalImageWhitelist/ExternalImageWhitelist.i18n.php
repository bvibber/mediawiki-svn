<?php
/**
 * Internationalisation file for extension ExternalImageWhitelist.
 *
 * @addtogroup Extensions
 */

$messages = array();

/** English
 * @author Ryan Schmidt
 */
$messages['en'] = array(
	'externalimagewhitelist' => " #<pre>Leave this line exactly as it is
#Put regular expression fragments (just the part that goes between the //) below
#These will be matched with the URLs of external (hotlinked) images
#Those that match will be displayed as images, otherwise only a link to the image will be shown
#Lines beginning with # are treated as comments

#Put all regex fragments ABOVE this line. Leave this line exactly as it is</pre>";
	'externalimagewhitelist-desc' => 'Only allow external (hotlinked) images to be displayed when they match a regex on an on-wiki whitelist';
);
