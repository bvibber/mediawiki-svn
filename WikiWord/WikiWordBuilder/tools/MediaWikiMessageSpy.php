<?php
$mwdir= $argv[1];
$lang= @$argv[2];
$msg= @$argv[3];

$options = array(  );

require_once( "$mwdir/maintenance/commandLine.inc" );
require_once( "MediaWikiSpyTools.php" );

if ($msg) {
  $m = $translations->getMessage($msg);
  print "$m\n";
} else {
  $messages = $translations->getAllMessages();
  $messages["linktrail"] = $translations->linkTrail();

  foreach ($messages as $key => $text) {
      $s = escape($text);
      print "$key=$s\n";
  }
}
