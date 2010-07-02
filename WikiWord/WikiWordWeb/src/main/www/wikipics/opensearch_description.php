<?php

if (!isset($scriptPath)) $scriptPath = "./";
if (!isset($skinPath)) $skinPath = "$scriptPath/../skin/";

if( @$_GET( 'ctype' ) == 'application/xml' ) {
	// Makes testing tweaks about a billion times easier
	$ctype = 'application/xml';
} else {
	$ctype = 'application/opensearchdescription+xml';
}

header("Content-Type: $ctype; charset=UTF-8");
print '<?xml version="1.0"?>';?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
	<ShortName>WikiPics</ShortName>
	<Description>WikiPics: Multilingual Search for Wikimedia Commons</Description>
	<Image height="16" width="16" type="image/x-icon"><?php print "$skinPath/favicon.ico"; ?></Image>
	<Url type="text/html" method="get" template="<?php print "$scriptPath/search.php?lang={language}&amp;term={searchTerms}"; ?>" />
	<Url type="application/json" method="get" template="<?php print "$scriptPath/search.php?lang={language}&amp;term={searchTerms}&amp;format=json"; ?>" />
	<Url type="application/atom+xml" method="get" template="<?php print "$scriptPath/search.php?lang={language}&amp;term={searchTerms}&amp;format=atom"; ?>" />
	<moz:SearchForm><?php print "$scriptPath/search.php"; ?></moz:SearchForm>
</OpenSearchDescription>