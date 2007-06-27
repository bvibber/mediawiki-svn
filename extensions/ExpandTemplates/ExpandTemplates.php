<?php
if ( !defined( 'MEDIAWIKI' ) ) {
?>
<p>This is the ExpandTemplates extension. To enable it, put </p>
<pre>require_once("$IP/extensions/ExpandTemplates/ExpandTemplates.php");</pre>
<p>at the bottom of your LocalSettings.php</p>
<?php
	exit(1);
}

# Internationalisation file
require_once( 'ExpandTemplates.i18n.php' );

$wgExtensionCredits['specialpage'][] = array(
	// 'author' => '', Who is the author?
	'name' => 'ExpandTemplates',
	'url' => 'http://www.mediawiki.org/wiki/Extension:ExpandTemplates',
	'description' => 'Expands templates, parser functions and variables to show expanded wikitext and preview rendered page'
);

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/ExpandTemplates_body.php', 'ExpandTemplates', 'ExpandTemplates' );

?>
