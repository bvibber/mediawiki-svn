<?php
if ( !defined( 'MEDIAWIKI' ) ) {
?>
<p>This is the ExpandTemplates extension. To enable it, put </p>
<pre>require_once("$IP/extensions/ExpandTemplates/ExpandTemplates.php");</pre>
<p>at the bottom of your LocalSettings.php.</p>
<p>This extension also requires
<tt><a href="http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/ExtensionFunctions.php">
ExtensionFunctions.php</a></tt>.</p>
<?php
	exit(1);
}

$wgExtensionCredits['specialpage'][] = array(
	'name'           => 'ExpandTemplates',
	'version'        => '2008-01-09',
	'author'         => 'Tim Starling',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:ExpandTemplates',
	'description'    => 'Expands templates, parser functions and variables to show expanded wikitext and preview rendered page',
	'descriptionmsg' => 'expandtemplates-desc',
);

$wgExtensionMessagesFiles['ExpandTemplates'] = dirname(__FILE__) . '/ExpandTemplates.i18n.php';

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/ExpandTemplates_body.php', 'ExpandTemplates', 'ExpandTemplates' );
