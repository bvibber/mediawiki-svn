<?php

# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/ContributionTracking/ContributionTracking.php" );
EOT;
        exit( 1 );
}
 
$dir = dirname(__FILE__) . '/';
 
$wgHooks['LanguageGetSpecialPageAliases'][] = 'contributionTrackingLocalizedPageName'; # Add any aliases for the special page.

$wgExtensionMessagesFiles['ContributionTracking'] = $dir . 'ContributionTracking.i18n.php';
$wgAutoloadClasses['ContributionTracking'] = $dir . 'ContributionTracking_body.php'; # Tell MediaWiki to load the extension body.
$wgSpecialPages['ContributionTracking'] = 'ContributionTracking'; # Let MediaWiki know about your new special page.

function contributionTrackingLocalizedPageName(&$specialPageArray, $code) {
  # The localized title of the special page is among the messages of the extension:
  wfLoadExtensionMessages('ContributionTracking');
  $text = wfMsg('contributiontracking');
 
  # Convert from title in text form to DBKey and put it into the alias array:
  $title = Title::newFromText($text);
  $specialPageArray['ContributionHistory'][] = $title->getDBKey();

  return true;
}

function contributionTrackingConnection() {
  global $wgContributionTrackingDBserver, $wgContributionTrackingDBname;
  global $wgContributionTrackingDBuser, $wgContributionTrackingDBpassword;

  static $db;

  if (!$db) {
    $db = new DatabaseMysql($wgContributionTrackingDBserver, $wgContributionTrackingDBuser, $wgContributionTrackingDBpassword, $wgContributionTrackingDBname);
  }
  
  return $db;
}
