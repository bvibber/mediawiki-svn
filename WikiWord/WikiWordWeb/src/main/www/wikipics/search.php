<?php
define("WIKIPICS", 1);
define("WIKIWORD", 1);
$IP = dirname( dirname(__FILE__) );

define('WIKIPICS_VERSION', '<span style="color:red">WikiPics 0.2&alpha; (experimental)</span>');

require_once("$IP/config.php");
require_once("$IP/common/wwimages.php");

if ($wwAPI) require_once("$IP/common/wwclient.php");
else require_once("$IP/common/wwthesaurus.php");

function getImagesAbout($concept, $max) {
    global $utils, $profiling;

    $t = microtime(true);
    $pics = $utils->getImagesAbout($concept, $max);
    $profiling['pics'] += (microtime(true) - $t);

    return $pics;
}

function getImageInfo($img) {
	if (empty($img['meta'])) return false;

	$info = array();
	extract($img['meta']);

	$info[] = htmlspecialchars("$img_minor_mime");

	if ( $img_media_type == "BITMAP" ) {
	    $info[] = htmlspecialchars("{$img_width}x{$img_height}");
	}

	if ( $img_size > 1024*1024 ) $info[] = htmlspecialchars(sprintf("%1.0fM", $img_size / (1024.0*1024.0)));
	else if ( $img_size > 1024 ) $info[] = htmlspecialchars(sprintf("%1.0fK", $img_size / 1024.0));
	else $info[] = htmlspecialchars(sprintf("%dB", $img_size));

	return $info;
}

function getImageLabels($img) {
	global $wwLabelPatterns;

	if (!$wwLabelPatterns || empty($img['tags'])) return false;

	$labels = array();

	foreach ( $img['tags'] as $tag ) {
	    foreach ( $wwLabelPatterns as $pattern => $label ) {
		if ( preg_match($pattern, $tag) ) {
		    $labels[$label] = "<span title=\"".htmlspecialchars($tag)."\">".htmlspecialchars($label)."</span>";
		    break;
		}
	    }
	}

	$labels = array_values($labels);
	return $labels;
}

function getConceptDetailsURL($langs, $concept) {
    global $wwSelf;

    if ( is_array($langs) ) $langs = implode('|', $langs);

    return "$wwSelf?id=" . urlencode($concept['id']) . "&lang=" . urlencode($langs); 
}

function pickPage( $pages ) {
    if (!$pages) return false;

    foreach ( $pages as $page => $type ) {
	if ($type == 10) return $page;
    }

    return $pages[0];
}

function getConceptPageURLs($lang, $concept) {
    if (!isset($concept['pages'][$lang]) || !$concept['pages'][$lang]) return false;

    if ($lang == 'commons') $domain = 'commons.wikimedia.org';
    else $domain = "$lang.wikipedia.org";

    $urls = array();
    foreach ($concept['pages'][$lang] as $page => $type) {
	$u = "http://$domain/wiki/" . urlencode($page); 
	$urls[$page] = $u;
    }

    return $urls;
}

function array_key_diff($base, $other) {
    $keys = array_keys($other);
    foreach ($keys as $k) {
	unset($base[$k]);
    }

    return $base;
}

function getRelatedConceptList( $concept ) {
    $related = array();
    if ( @$concept['similar'] ) $related += $concept['similar'];
    if ( @$concept['related'] ) $related += $concept['related'];

    if (isset($concept['broader'])) $related = array_key_diff($related, $concept['broader']);
    if (isset($concept['narrower'])) $related = array_key_diff($related, $concept['narrower']);

    sortConceptList($related);
    return $related;
}
function stripSections(&$concepts) {
    foreach ($concepts as $k => $c) {
	  foreach ($c['name'] as $l => $n) {
		if (preg_match('/#/', $n)) {
			unset($concepts[$k]);
			break;
		}
	  }
    }
}

function compareConceptScoreAndName($a, $b) {
    if (isset($a['score']) && isset($b['score']) && $a['score'] != $b['score']) {
	if ( $a['score'] > $b['score'] ) return 1;
	else return -1;
    } else {
	if ( $a['name'] > $b['name'] ) return 1; //XXX: unicode collation??
	else return -1;
    }

    return 0;
}

function sortConceptList(&$concepts) {
    usort($concepts, 'compareConceptScoreAndName');
}

function mangleConcept(&$concept) {
    stripSections($concept['narrower']);

    sortConceptList($concept['narrower']);
    sortConceptList($concept['related']);
    sortConceptList($concept['similar']);
    sortConceptList($concept['broader']);
}

$conceptId = @$_REQUEST['id'];
$term = @$_REQUEST['term'];
$lang = @$_REQUEST['lang'];
$format = @$_REQUEST['format'];

if ( $term===null ) {
	$term = @$_SERVER['PATH_INFO'];
	$term = preg_replace('!^/!', '', $term);
}

if ($lang===null && $term!==null && preg_match('/^\s*(.+)[:](.+)\s*$/', $term, $m)) {
	$lang = $m[1];
    $term = $m[2];
}

if ($conceptId===null && $term!==null && preg_match('/^\s*[#$](\d+)\s*$/', $term, $m)) {
    $conceptId = $m[1];
    $term = NULL;
}

if (!isset($wwSelf)) {
    if ( isset( $_SERVER["SCRIPT_NAME"] ) ) $wwSelf = $_SERVER["SCRIPT_NAME"];
    else if ( isset( $_SERVER["PHP_SELF"] ) ) $wwSelf = $_SERVER["PHP_SELF"];
    else './search.php';
}

if (!isset($scriptPath)) $scriptPath = dirname($wwSelf);
if (!isset($skinPath)) $skinPath = "$scriptPath/../skin/";

$error = NULL;

if ($lang) {
    $ll = preg_split('![,;/|+]!', $lang);
    foreach ($ll as $l) {
	if (!isset($wwLanguages[$l]) && $l != "commons") {
	    $error = "bad language code: $l";
	    $lang = NULL;
	}
    }
}

if ($wwAPI) $thesaurus = new WWClient($wwAPI);
else {
  $thesaurus = new WWThesaurus();
  $thesaurus->connect($wwDBServer, $wwDBUser, $wwDBPassword, $wwDBDatabase);
}

if (@$wwFakeImages) $utils = new WWFakeImages( $thesaurus );
else $utils = new WWImages( $thesaurus );


if ( !$utils->db ) $utils->connect($wwDBServer, $wwDBUser, $wwDBPassword, $wwDBDatabase);

if (@$_REQUEST['debug']) $utils->debug = true;

$limit = 20;
$norm = 1;

$mode = NULL;
$result = NULL;

$fallback_languages = array( "en" ); #TODO: make the user define this list

if ( $lang ) {
    $languages = preg_split('![,;/|+]!', $lang);
    $languages = array_merge( $languages, $fallback_languages ); 
    $languages = array_unique( $languages );
} else {
    $languages = $fallback_languages;
}

$lang = $languages[0];

$allLanguages = array_keys($wwLanguages);

$profiling['thesaurus'] = 0;
$profiling['pics'] = 0;

if (!$error) {
  $t = microtime(true);
  try {
      if ($lang && $conceptId) {
	  $mode = "concept";
	  $result = $thesaurus->getConceptInfo($conceptId, $lang, null, $allLanguages);
	  if ( $result ) $result = array( $result ); //hack
      } else if ($lang && $term) {
		  $mode = "term";
		  $result = $thesaurus->getConceptsForTerm($lang, $term, $languages, $norm, $allLanguages, $limit);
      } 
  } catch (Exception $e) {
      $error = $e->getMessage();
  }
  $profiling['thesaurus'] += (microtime(true) - $t);
}

/*if ( $format == "atom" || $format == "xml" || $format == "opensearch" ) include("response.atom.php"); 
else*/ 
include("response.html.php"); 

$utils->close();
