<?php
require_once( dirname( __FILE__ ) . "/wwutils.php" );

class WWClient {
    var $api;

    function __construct( $api ) {
	$this->api = $api;
    }

    function query( $params ) {
	$url = $this->api . '?format=phps';

	foreach ( $params as $k => $v ) {
	    $url .= '&';
	    $url .= urlencode( $k );
	    $url .= '=';
	    $url .= urlencode( $v );
	}

	$data = file_get_contents( $url ); //TODO: CURL
	if ( !$data ) throw new Exception("failed to fetch data from $url");

	$data = unserialize($data);
	if ( !$data ) throw new Exception("failed to unserialize data from $url");

	if ( @$data['error'] ) throw new Exception("API returned error ".$data['error']['code'].": ".$data['error']['message']);
	return $data;
    }

    function getLocalConcepts($id) { //NOTE: deprecated alias for backward compat
	return getPagesForConcept($id);
    }

    function getPagesForConcept( $id, $lang = null ) {
	$p = $this->getConceptProperties( $id, 'pages', $lang );
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
    }

    function getConceptInfo( $id, $lang = null ) {
	if ( $lang ) return $this->getConceptProperties( "definition,broader,narrower,related" );
	else return $this->getConceptProperties( "broader,narrower,related" );
    }

    function getConceptProperties( $id, $props, $lang = null ) {
	$param = array(
		'query' => 'properties',
		'props' => ( is_array($props) ? join('|', $props) : $props ),
		'gcid' => $id,
	);

	if ( $lang ) $param['lang'] = $lang;

	$rs = $this->query( $param );

	return $rs;
    }

    function getConceptsForTerm( $lang, $term ) {
	$param = array(
		'query' => 'concepts',
		'lang' => $lang,
		'term' => $term,
	);

	$rs = $this->query( $param );

	return $rs['concepts'];
    }

    function getConceptsForPage( $lang, $page ) {
	$param = array(
		'query' => 'concepts',
		'lang' => $lang,
		'page' => $page,
	);

	$rs = $this->query( $param );

	return $rs['concepts'];
    }

}
