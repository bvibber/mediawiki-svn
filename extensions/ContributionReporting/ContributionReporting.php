<?php

# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/ContributionReporting/ContributionReporting.php" );
EOT;
        exit( 1 );
}
 
$dir = dirname(__FILE__) . '/';
 
$wgHooks['LanguageGetSpecialPageAliases'][] = 'contributionReportingLocalizedPageName'; # Add any aliases for the special page.

$wgExtensionMessagesFiles['ContributionHistory'] = $dir . 'ContributionHistory.i18n.php';
$wgAutoloadClasses['ContributionHistory'] = $dir . 'ContributionHistory_body.php'; # Tell MediaWiki to load the extension body.
$wgSpecialPages['ContributionHistory'] = 'ContributionHistory'; # Let MediaWiki know about your new special page.

$wgExtensionMessagesFiles['ContributionTotal'] = $dir . 'ContributionTotal.i18n.php';
$wgAutoloadClasses['ContributionTotal'] = $dir . 'ContributionTotal_body.php'; # Tell MediaWiki to load the extension body.
$wgSpecialPages['ContributionTotal'] = 'ContributionTotal'; # Let MediaWiki know about your new special page.

function contributionReportingLocalizedPageName(&$specialPageArray, $code) {
  # The localized title of the special page is among the messages of the extension:
  wfLoadExtensionMessages('ContributionHistory');
  $text = wfMsg('contributionhistory');
 
  # Convert from title in text form to DBKey and put it into the alias array:
  $title = Title::newFromText($text);
  $specialPageArray['ContributionHistory'][] = $title->getDBKey();

  wfLoadExtensionMessages('ContributionTotal');
  $text = wfMsg('contributiontotal');
  $title = Title::newFromText($text);
  $specialPageArray['ContributionTotal'][] = $title->getDBKey();
 
  return true;
}

function contributionReportingConnection() {
  global $wgContributionReportingDBserver, $wgContributionReportingDBname;
  global $wgContributionReportingDBuser, $wgContributionReportingDBpassword;

  static $db;

  if (!$db) {
    $db = new DatabaseMysql($wgContributionReportingDBserver, $wgContributionReportingDBuser, $wgContributionReportingDBpassword, $wgContributionReportingDBname);
  }
  
  return $db;
}
