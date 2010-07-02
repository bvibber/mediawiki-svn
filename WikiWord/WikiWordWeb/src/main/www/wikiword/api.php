<?php
$IP = dirname( dirname(__FILE__) );

require_once("$IP/config.php");
require_once("$IP/common/wwthesaurus.php");

$query = @$_REQUEST['query'];

if ( $query ) {
    $lang = @$_REQUEST['lang'];
    $qlang = @$_REQUEST['qlang'];
    $rclang = @$_REQUEST['rclang'];
    $format = @$_REQUEST['format'];
    if ( !$format ) $format = 'phps';

    if ($lang) $lang = preg_replace('[^\\w\\d_]', '', $lang);
    if ($qlang) $qlang = preg_replace('[^\\w\\d_]', '', $qlang);
    
    if ($rclang) $rclang = preg_replace('[^\\w\\d_]', '', $rclang);

    if ($lang) {
		$lang = preg_split('![\\s,;|/:]\\s*!', $lang);
		if ( !$qlang ) $qlang = $lang[0];
		if (count($lang) == 1) $lang = $lang[0];
    }

    $result = array( 'query' => $query );
    $start = microtime(true);

    try {
	$thesaurus = new WWThesaurus();
	$db = $thesaurus->connect($wwDBServer, $wwDBUser, $wwDBPassword, $wwDBDatabase);

	if ( !$db ) {
	    $result['error'] = array('code' => 1010, 'message' => "failed to connect to thesaurus database");
	} else if ($query == 'concepts') {
	    $term = @$_REQUEST['term'];
	    $norm = @$_REQUEST['norm'];

	    if ( $norm === null ) $norm = 1;
	    #$page = @$_REQUEST['page'];

	    if ( $qlang === null ) $result['error'] = array('code' => 150, 'message' => "missing parameter qlang");
	    else if ( $term !== null ) {
			$result['concepts'] = $thesaurus->getConceptsForTerm($qlang, $term, $lang, $norm, $rclang); #TODO: limit!
			if ( $result['concepts'] === false || $result['concepts'] === null ) {
			    $result['error'] = array('code' => 210, 'message' => "failed to retrieve concepts for term $langt:$term");
			}
	    } /*else if ( $page !== null ) {
		$result['concepts'] = $thesaurus->getConceptsForPage($lang, $page);
		if ( $result['concepts'] === false || $result['concepts'] === null ) {
		    $result['error'] = array('code' => 250, 'message' => "failed to retrieve concepts for page $langt:$page");
		}
	    } */else {
			$result['error'] = array('code' => 110, 'message' => "missing parameter term");
	    }
	} else if ($query == 'concept' || $query == 'info') {
	    $gcid = @$_REQUEST['gcid'];
	    if (!$gcid) $gcid = @$_REQUEST['id'];

	    if ( $gcid === null ) {
			$result['error'] = array('code' => 120, 'message' => "missing parameter gcid");
	    } else {
		    $fields = null;
			$result['concept'] = $thesaurus->getConceptInfo($gcid, $lang, $fields, $rclang); #TODO: limit!
			if ( $result['concept'] === false || $result['concept'] === null ) {
			    $result['error'] = array('code' => 210, 'message' => "concept not found: $gcid");
			}
	    }
	} else if ($query == 'properties') {
	    $gcid = @$_REQUEST['gcid'];
	    if (!$gcid) $gcid = @$_REQUEST['id'];

	    $props = @$_REQUEST['props'];
	    
	    if ( $gcid === null ) $result['error'] = array('code' => 120, 'message' => "missing parameter gcid");
	    else if ( $props === null ) $result['error'] = array('code' => 130, 'message' => "missing parameter props");
	    else {
		$props = preg_split('![\\s,;|/:]\\s*!', $props);

		$info = $thesaurus->getConcept($gcid, $lang);

		if ( !$info ) $result['error'] = array('code' => 100, 'message' => "concept not found: $gcid");
		else {
		    $result = array_merge($result, $info);

		    foreach ( $props as $p ) {
			$m = "get" . ucfirst($p) . "ForConcept";
			if ( !method_exists($thesaurus, $m) ) {
			    $result['error'] = array('code' => 190, 'message' => "unknown property: $p");
			    break;
			}

			$result[$p] = $thesaurus->$m($gcid, $lang);

			if ( $result[$p] === false || $result[$p] === null ) {
			    $result['error'] = array('code' => 220, 'message' => "failed to retrieve property $p for concept $gcid");
			    break;
			}
		    }

		    if (!isset($result['id'])) $result['id'] = $gcid;
		    if (!isset($result['lang'])) $result['lang'] = $lang;
		}
	    }
	} else {
	    $result['error'] = array('code' => 10, 'message' => "bad query: $query");
	}
    } catch (Exception $e) {
	$result['error'] = array('code' => 1000, 'message' => "unexpected exception: " . $e->getMessage());
    }

    $result['time'] = (microtime(true) - $start) . " sec";

    if ( isset($result['error']) ) {
	#TODO: HTTP error codce would be nice, but causes file_get_contents to swallow the data.
	#      need to use CURL in client!
	#header("Status: 400 Bad Request", true, 400);
    }

    #TODO: JSON (+YAML?), WDDX
    if ($format == 'phps') {
	header("Content-Type: application/vnd.php.serialized"); #as proposed by http://www.alvestrand.no/pipermail/ietf-types/2004-August/001259.html
	$data = serialize($result);
	echo $data;
    } else if ($format == 'php') {
	header("Content-Type: text/php; charset=UTF-8"); 
	var_export($result);
    } else if ($format == 'text') {
	header("Content-Type: text/plain; charset=UTF-8"); 
	print_r($result);
    } else {
	header("Content-Type: text/plain; charset=UTF-8");
	header("Status: 400 Bad Request", true, 400);
	echo "Bad format: $format";
    }

    exit();
}

header("Content-Type: text/plain; charset=UTF-8");
?>
WikiWord REST API