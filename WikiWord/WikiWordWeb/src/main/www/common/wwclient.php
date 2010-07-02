<?php
require_once( dirname( __FILE__ ) . "/wwutils.php" );

class WWClient {
    var $api;
    var $debug = false;

    function __construct( $api ) {
	$this->api = $api;
    }

    function query( $params ) {
	$url = $this->api . '?format=phps';

	foreach ( $params as $k => $v ) {
	    if ($v===null) continue;
	    if ($v===false) $v = "";

	    if (is_array($v)) {
		#   if (is_array($v[0])) {
		#	  print_r($v);
		#	  throw new Exception("foo");
		#    }
		    $v = implode("|", $v);
	    }

	    $url .= '&';
	    $url .= urlencode( $k );
	    $url .= '=';
	    $url .= urlencode( $v );
	}

	if ($this->debug) {
	    $t = microtime(true);
	    print "\n<span class='debug'>[fetching " .  htmlspecialchars($url) . "]</span>\n";
	    flush();
	}

	$data = file_get_contents( $url ); //TODO: CURL

	if ( !$data ) throw new Exception("failed to fetch data from $url");

	$data = unserialize($data);
	if ( !$data ) throw new Exception("failed to unserialize data from $url");

	if ( @$data['error'] ) throw new Exception("API returned error ".$data['error']['code'].": ".$data['error']['message']."; url: $url");

	if ($this->debug) {
	    $t = microtime(true) - $t;
	    print "\n<span class='debug'>[took " .  $t . " sec]</span>\n";
	    flush();
	}

	return $data;
    }

    function getPagesForConcept( $id, $lang = null ) {
	$p = $this->getConceptInfo( $id, $lang );
	return $p['pages'];
    }

    /*
    function getLocalConcepts($id) { //NOTE: deprecated alias for backward compat
	return getPagesForConcept($id);
    }

    function getConcept( $id, $lang = null ) {
	$p = $this->getConceptProperties( $id, '', $lang );getConceptInfo
	return $p['pages'];
    }

    function getRelatedForConcept( $id, $lang = null ) {
	$p = $this->getConceptProperties( $id, 'related', $lang );
	return $p['related'];
    }

    function getBroaderForConcept( $id, $lang = null ) {
	$p = $this->getConceptProperties( $id, 'broader', $lang );
	return $p['broader'];
    }

    function getNarrowerForConcept( $id, $lang = null ) {
	$p = $this->getConceptProperties( $id, 'narrower', $lang );
	return $p['narrower'];
    }

    function getTermsForConcept( $id, $lang = null ) {
	$p = $this->getConceptProperties( $id, 'terms', $lang );
	return $p['terms'];
    }

    function getDefinitionForConcept( $id, $lang = null ) {
	$p = $this->getConceptProperties( $id, 'definition', $lang );
	return $p['definition'];
    }

    function getReferencesForConcept( $id, $lang = null ) {
	$p = $this->getConceptProperties( $id, 'links', $lang );
	return $p['references'];
    }

    function getLinksForConcept( $id, $lang = null ) {
	$p = $this->getConceptProperties( $id, 'links', $lang );
	return $p['links'];
    }

    function getScoresForConcept( $id, $lang = null ) {
	$p = $this->getConceptProperties( $id, 'scores', $lang );
	return $p['scores'];
    }*/

    function getConceptInfo( $id, $lang = null, $fields = null, $rclang = null ) {
	$param = array(
		'query' => 'info',
		'gcid' => $id,
		'lang' => $lang,
		'fields' => $fields,
		'rclang' => $rclang
	);

	$rs = $this->query( $param );

	if (!$rs) return $rs;
	if (!isset($rs['concept'])) throw new Exception("bad response, missing concept");

	$rs = $rs['concept'];

	if (!isset($rs['id'])) $rs['id'] = $id;
	if (!isset($rs['lang'])) $rs['lang'] = $lang;

	return $rs;
    }

    /*
    function getConceptProperties( $id, $props, $lang =$props null ) {
	$param = array(
		'query' => 'properties',
		'props' => ( is_array($props) ? join('|', $props) : $props ),
		'gcid' => $id,
	);

	if ( $lang ) $param['lang'] = $lang;

	$rs = $this->query( $param );

	if (!isset($rs['id'])) $rs['id'] = $id;
	if (!isset($rs['lang'])) $rs['lang'] = $lang;

	return $rs;
    }*/

    function getConceptsForTerm( $qlang, $term, $languages, $norm = 1, $rclang = null, $limit = 100 ) {
	if ( is_array( $languages ) ) $languages = implode('|', $languages);

	$param = array(
		'query' => 'concepts',
		'qlang' => $qlang,
		'rclang' => $rclang,
		'lang' => $languages,
		'norm' => $norm,
		'term' => $term,
		'limit' => $limit,
	);

	$rs = $this->query( $param );

	return $rs['concepts'];
    }

    /*
    function getConceptsForPage( $lang, $page ) {
	$param = array(
		'query' => 'concepts',
		'lang' => $lang,
		'page' => $page,
	);

	$rs = $this->query( $param );

	return $rs['concepts'];
    }
    */
}
