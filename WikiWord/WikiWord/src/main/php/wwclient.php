<?php
require_once( dirname( __FILE__ ) . "/wwutils.php" );

class WWClient {
    var $api;

    function __construct( $api ) {
	$this->api = $api;
    }

    function query( $param ) {
	$url = $this->api . '?format=phps';

	for ( $params as $k => $v ) {
	    $url .= '&';
	    $url .= urlencode( $k );
	    $url .= '=';
	    $url .= urlencode( $v );
	}

	$data = file_get_contents( $url ); //TODO: CURL
	if ( !$data ) throw new Exception("failed to fetch data from $url");

	$data = unserialize($data);
	if ( !$data ) throw new Exception("failed to unserialize data from $url");

	if ( $data['error'] ) throw new Exception("API returned error ".$data['error']['code'].": ".$data['error']['message']);
	return $data;
    }

    function getWikiPages( $id ) {
	$p = $this->getConceptProperties( $id, 'pages' );

	return $p['pages'];
    }

    function getConceptProperties( $id, $props, $lang = NUL L) {
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

}
