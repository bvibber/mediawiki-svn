<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the skin file directly.
if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/TemplateLink/TemplateLink.setup.php" );
EOT;
        exit( 1 );
}
 
$wgAutoloadClasses['TemplateLink'] = dirname(__FILE__) . '/TemplateLink.body.php'; # Tell MediaWiki to load the extension body.
$wgSpecialPages['TemplateLink'] = 'TemplateLink'; # Let MediaWiki know about your new special page.
$wgHooks['LoadAllMessages'][] = 'TemplateLink::loadMessages'; # Load the internationalization messages for your special page.
$wgHooks['LanguageGetSpecialPageAliases'][] = 'TemplateLinkLocalizedPageName'; # Add any aliases for the special page.
 
function TemplateLinkLocalizedPageName(&$specialPageArray, $code) {
  # The localized title of the special page is among the messages of the extension:
  TemplateLink::loadMessages();
  $text = wfMsg('TemplateLink');
 
  # Convert from title in text form to DBKey and put it into the alias array:
  $title = Title::newFromText($text);
  $specialPageArray['TemplateLink'][] = $title->getDBKey();
 
  return true;
}
