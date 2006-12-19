<?php

/**
 * Internationalisation file for the Duplicator extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */
 
function efDuplicatorMessages() {
	return array(

/* English (Rob Church) */
'en' => array(
'duplicator' => 'Duplicate an article',
'duplicator-header' => 'This page allows the complete duplication of an article, creating independent
copies of all histories. This is useful for article forking, etc.',

'duplicator-options' => 'Options',
'duplicator-source' => 'Source:',
'duplicator-dest' => 'Destination:',
'duplicator-dotalk' => 'Duplicate discussion page (if applicable)',
'duplicator-submit' => 'Duplicate',

'duplicator-summary' => 'Copied from [[$1]]',

'duplicator-success' => "<big>'''[[$1]] was copied to [[$2]].'''</big>\n\n",
'duplicator-success-revisions' => '$1 revisions were copied.',
'duplicator-success-talkcopied' => 'The discussion page was also copied.',
'duplicator-success-talknotcopied' => 'The talk page could not be copied.',
'duplicator-failed' => 'The page could not be duplicated. An unknown error occurred.',

'duplicator-source-invalid' => 'Please provide a valid source title.',
'duplicator-source-notexist' => '[[$1]] does not exist. Please provide the title of a page that exists.',
'duplicator-dest-invalid' => 'Please provide a valid destination title.',
'duplicator-dest-exists' => '[[$1]] already exists. Please provide a destination title which doesn\'t exist.',
'duplicator-toomanyrevisions' => '[[$1]] has too many ($2) revisions and cannot be copied. The current limit is $3.',
),
	
	);
}

?>