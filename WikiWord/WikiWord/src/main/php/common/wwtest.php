<?php

$IP = dirname(__FILE__);

require_once("$IP/config.php");
require_once("$IP/wwutils.php");

$utils = new WWUtils();
$utils->connect($wwDBServer, $wwDBUser, $wwDBPassword, $wwDBDatabase);

$utils->debug = true;

if (!isset($argv[1])) die("usage: wwtest <id>\n");

$id = $argv[1];
$max = @$argv[2];

$images = $utils->getImagesAbout($id, $max);

print_r($images);