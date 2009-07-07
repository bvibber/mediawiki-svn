<?php
$mwdir= $argv[1];
$lang= @$argv[2];
$wrd= @$argv[3];

$options = array(  );

require_once( "$mwdir/maintenance/commandLine.inc" );
require_once( "MediaWikiSpyTools.php" );

$magic =  $translations->getMagicWords();

if ($wrd) {
  $e = $magic[$wrd];
  $e = array_slice($e, 1);
  $m = join($e, "|");

  print "$m\n";
} else {
  foreach ($magic as $key => $e) {
      $e = array_slice($e, 1);
      $m = join($e, "|");

      $m = escape($m);
      print "$key=$m\n";
  }
}
