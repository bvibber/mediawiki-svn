<?php

$wwDBServer = "localhost";
$wwDBUser = "wikiword";
$wwDBPassword = "xxxxxxxxxx";
$wwDBDatabase = "wikiword";

$wwLanguages = array("en" => "Englisch", "de" => "German", "fr" => "French");
$wwTablePrefix = "full";
$wwThesaurusDataset = "thesaurus";

#error_reporting(E_ALL);

$wwMaxPreviewImages = 8;
$wwThumbSize = 120;
$wwThumbnailURL = "http://toolserver.org/tsthumb/tsthumb?f={name}&domain=commons.wikimedia.org&w={width}&h={height}";
$wwImagePageURL = "http://commons.wikimedia.org/wiki/File:{name}";

$wwFakeCommonsConcepts = true;
$wwFakeCommonsPlural = true;
$wwCommonsTablePrefix = "commonswiki_p.";

$wwWikiInfoTable = "toolserver.wiki";
$wwWikiDbName = "{lang}wiki_p";
$wwWikiServerName = "sql-s{num}";

$wwCommonsServerName = null;

$wwTagScores = array(
  'Category:Featured_pictures_on_Wikimedia_Commons' => 3, 
  'Category:Featured_pictures_on_Wikipedia,_German' => 3,

  'Template:Former_featured_picture' => 2.8,

  'Template:Media_of_the_day' => 2.5,
  'Template:Picture_of_the_day' => 2.5,

  'Category:Quality_images' => 2.0,

  'Category:Valued_image' => 1.4,
  'Category:Former_valued_images' => 1.3,
  'Category:Images_used_in_valued_image_sets' => 1.2,
);