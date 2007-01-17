<?php

/**
 * Internationalisation file for the Book Information extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */
 
function efBookInformationMessages() {
	return array(

/* English (Rob Church) */
'en' => array(
'bookinfo-header' => 'Book information',
'bookinfo-result-title' => 'Title:',
'bookinfo-result-author' => 'Author:',
'bookinfo-result-publisher' => 'Publisher:',
'bookinfo-result-year' => 'Year:',
'bookinfo-error-invalidisbn' => 'Invalid ISBN entered.',
'bookinfo-error-nodriver' => 'Unable to initialise an appropriate Book Information Driver.',
'bookinfo-error-noresponse' => 'No response or request timed out.',
'bookinfo-purchase' => 'Purchase this book from $1',
'bookinfo-provider' => 'Data provider: $1',
),
	
	);
}

?>