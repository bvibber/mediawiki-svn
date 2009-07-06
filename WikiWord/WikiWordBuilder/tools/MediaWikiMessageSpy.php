<?php

$raw_chars = array("\n", "\r", "\t");
$escapewd_chars = array('\\n', '\\r', '\\t');
$hex_chars = "0123456789abcdef";

function hex($n) {
    global $hex_chars;

    $hi = $n / 16;
    $lo = $n % 16;

    return $hex_chars[$hi] . $hex_chars[$lo];
}

function escape($text) {
  global $raw_chars, $escaped_chars;

  $text = str_replace( $raw_chars, $escaped_chars, $text );

  $text = iconv("UTF-8", "UTF-16BE", $text);
  $c = strlen($text);
  $s = '';
  
  for($i = 0; $i<$c; $i+= 2) {
    $hi_ch = $text[$i];
    $lo_ch = $text[$i+1];
    $hi = ord($hi_ch);
    $lo = ord($lo_ch);

    if ($hi == 0) {
	if ($lo<32 || $lo>127) $s .= '\u00' . hex($lo);
	else $s .= $lo_ch;
    } else {
	$s .= '\u';
	$s .= hex($hi);
	$s .= hex($lo);
    }
  }

  return $s;
}

$mwdir= $argv[1];
$lang= @$argv[2];
$msg= @$argv[3];

$options = array(  );

require_once( "$mwdir/maintenance/commandLine.inc" );

$langClass = 'Language' . str_replace( '-', '_', $lang );

wfSuppressWarnings();
include_once("$IP/languages/$langClass.php");
wfRestoreWarnings();

if( ! class_exists( $langClass ) ) {
	# Default to English/UTF-8
	$lc = strtolower(substr($langClass, 8));
	$snip = "
		class $langClass extends Language {
			function getVariants() {
				return array(\"$lc\");
			}

		}";
	eval($snip);
}

$translations= new $langClass();
$translations->initEncoding();
$translations->load();

if ($msg) {
  $m = $translations->getMessage($msg);
  print "$m\n";
} else {
  $messages = $translations->getAllMessages();

  foreach ($messages as $key => $text) {
      $s = escape($text);
      print "$key=$s\n";
  }
}
