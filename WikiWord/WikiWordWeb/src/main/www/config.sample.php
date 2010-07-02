<?php

#$wwAPI = "http://wikiword.wikimedia.de/wikiword/api.php";
$wwAPI = false;
$wwAllowTranslate = false;
$wwImageSearch = null; //NOTE: trinary: true forces image searche, false disables it. null makes it optional.

$wwDBServer = "localhost";
$wwDBUser = "wikiword";
$wwDBPassword = "xxxxxxxxxx";
$wwDBDatabase = "wikiword";

$wwLanguages = array("en" => "Englisch", "de" => "German", "fr" => "French");
$wwTablePrefix = "full";
$wwThesaurusDataset = "thesaurus";

#error_reporting(E_ALL);

$wwMaxPreviewImages = 5;
$wwMaxPreviewLinks = 20;
$wwGalleryColumns = 5;
$wwMaxGalleryImages = 200;
$wwMaxDetailLinks = 1000;
$wwMaxSearchResults = 8;

$wwThumbSize = 120;
$wwThumbnailURL = "http://toolserver.org/tsthumb/tsthumb?f={name}&domain=commons.wikimedia.org&w={width}&h={height}";
$wwImagePageURL = "http://commons.wikimedia.org/wiki/File:{name}";

#$wwFakeCommonsConcepts = false;
#$wwFakeCommonsPlural = false;
$wwCommonsTablePrefix = "commonswiki_p.";

$wwWikiInfoTable = "toolserver.wiki";
$wwWikiDbName = "{lang}wiki_p";
$wwWikiServerName = "sql-s{num}";
$wwWikiFastServerName = "sql-s{num}-fast";

$wwCommonsServerName = null;
$wwCommonsFastServerName = null;

$wwTagsTable = null;
$wwUsageTable = null;

#$wwGilNoiceTable = null;
#$wwGilNoiceThreshold = 10;
$wwFrequentImageThreshold = 10;

$wwTagScores = array(
  'Category:Featured_pictures_on_Wikimedia_Commons' => 10,
  'Category:Featured_pictures_on_Wikipedia,_German' => 10,

  'Template:Former_featured_picture' => 6,

  'Template:Media_of_the_day' => 10,
  'Template:Picture_of_the_day' => 10,

  'Category:Quality_images' => 8,

  'Category:Valued_image' => 8,
  'Category:Former_valued_images' => 6,
  'Category:Images_used_in_valued_image_sets' => 7,

  'assessment:Media_of_the_day' => 10,
  'assessment:Picture_of_the_day' => 10,
  'assessment:Picture_of_the_week' => 15,
  'assessment:Picture_of_the_month' => 20,
  'assessment:Picture_of_the_year' => 25,
  'assessment:Quality_image' => 8,
  'assessment:Featured_picture' => 15,
);


$wwLabelPatterns = array(
  '/^assessment:.*_of_the_day([-_].*|$)/' => "PotD",
  '/^assessment:.*_of_the_week([-_].*|$)/' => "PotW",
  '/^assessment:.*_of_the_month([-_].*|$)/' => "PotM",
  '/^assessment:.*_of_the_year([-_].*|$)/' => "PotY",
  '/^assessment:(Featured|Former_featured_picture).*/' => 'FP',
  '/^assessment:Quiality.*/' => 'QI',
  '/^assessment:(Valued|Former_valued_images|Images_used_in_valued_image_sets).*/' => 'VI',
  '/^license:PD([-_].*|$)/' => 'PD',
  '/^license:GFDL([-_].*|$)/' => 'GFDL',
  '/^license:CC-BY-SA([-_].*|$)/i' => 'CC-BY-SA',
  '/^license:CC-BY([-_].*|$)/i' => 'BY',
);