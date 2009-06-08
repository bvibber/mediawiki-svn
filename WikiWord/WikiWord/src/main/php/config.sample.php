<?php

$wwDBServer = "localhost";
$wwDBUser = "wikiword";
$wwDBPassword = "xxxxxxxxxx";
$wwDBDatabase = "wikiword";

$wwLanguages = array("en" => "Englisch", "de" => "German", "fr" => "French");
$wwTablePrefix = "full";
$wwThesaurusDataset = "thesaurus";

#error_reporting(E_ALL);

$wwThumbSite = 90;
$wwThumbnailURL = "http://toolserver.org/tsthumb/tsthumb?f={name}&domain=commons.wikimedia.org&w={width}&h={height}";
$wwImagePageURL = "http://commons.wikimedia.org/wiki/File:{name}";

$wwFakeCommonsConcepts = true;
$wwCommonsTablePrefix = "commonswiki_p.";

$wwWikiInfoTable = "toolserver.wiki";