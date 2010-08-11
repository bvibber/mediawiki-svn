<?php
if (!defined('MEDIAWIKI')) {
	echo "XMLRC extension";
	exit(1);
}

class XMLRC {
  var $main;
  var $query;
  var $format;
  var $transport;
  var $filter;
  var $props;

  public function __construct( $transportConfig, $props = null ) {
      $this->transportConfig = $transportConfig;
      $this->props = $props;
  }

  public static function RecentChange_save( $rc ) {
    global $wgXMLRCTransport, $wgXMLRCProperties;

    if ( !empty( $GLOBALS['wgXMLRCTransport'] ) ) {
	$xmlrc = new XMLRC( $wgXMLRCTransport, $wgXMLRCProperties );
	$xmlrc->processRecentChange( $rc );
    }

    return true; //continue processing normally
  }

  public function processRecentChange( $rc ) {
    $xml = $this->formatRecentChange( $rc );
    $this->sendRecentChangeXML( $xml );
  }

  private function getTransport() {
	if ( $this->transport != null ) return $this->transport;

	$class = $this->transportConfig['class'];
	$this->transport = new $class( $this->transportConfig );

	return $this->transport;
  }

  private function getMainModule() {
	if ( $this->main != null ) return $this->main;

	$req = new FauxRequest( array() );
	$this->main = new ApiMain( $req );

	return $this->main;
  }

  private function getFormatModule() {
	if ( $this->format != null ) return $this->format;

	$main = $this->getMainModule();
	$this->format = new ApiFormatXml( $main, "xml" );

	return $this->format;
  }

  private function getQueryModule() {
	if ( $this->query != null ) return $this->query;

	$main = $this->getMainModule();
	$this->query = new ApiQueryRecentChanges( $main, "recentchanges" );

	$prop = $this->props;

	if ( !$prop ) $prop = "title|timestamp|ids"; //default taken from the API 
	
	if ( is_string( $prop ) ) {
	    $prop = preg_split( '\\s*[,;/|+]\\s*', $prop );
	}

	foreach ( $prop as $k => $v ) {
	    if ( is_int( $k ) ) {
		unset( $prop[ $k ] );
		$prop[ $v ] = true;
	    }
	}

	unset( $prop[ 'patrolled' ] ); //restricted info, don't publish. API denies it.

	$this->query->initProperties( $prop );

	return $this->query;
  }

  public static function array2object($data) 
  {
      $obj = new stdClass();

      foreach ($data as $key => $value) {
	  $obj->$key = $value;
      }

      return $obj;
  }

  public function formatRecentChange( $rc ) {
    $query = $this->getQueryModule();
    $format = $this->getFormatModule();

    $row = $rc->getAttributes();
    $row = XMLRC::array2object( $row );

    #wfDebug( "XMLRC: got attribute row: " . preg_replace('/\s+/', ' ', var_export($row, true)) . "\n" );

    $info = $query->extractRowInfo( $row );
    $info['wikiid'] = wfWikiID();

    #wfDebug( "XMLRC: got info: " . preg_replace('/\s+/', ' ', var_export($info, true)) . "\n" );

    $xml = $format->recXmlPrint( "rc", $info, "" );
    $xml = trim( $xml );

    return $xml;
  }

  public function sendRecentChangeXML( $xml ) {
    if ( !is_string( $xml ) ) wfDebugDieBacktrace( "XMLRC: parameter xml must be a string\n" );
    else wfDebug( "XMLRC: sending xml\n" );

    $transport = $this->getTransport();
    $transport->send( $xml ); 
  }
}

abstract class XMLRC_Filter {
  abstract function matches( $rc );
}

abstract class XMLRC_Transport {
  abstract function send( $xml );
}
