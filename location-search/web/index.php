<?php

/**
 *
 *
 * Graphics sources...
 *
 * Text clear icon based on http://commons.wikimedia.org/wiki/Image:Cancel.png
 * Image from the Silk icon theme by Mark James
 * '''Source:''' http://www.famfamfam.com/lab/icons/silk/ {{cc-by-2.5}}
 */

header('Content-Type: text/html; charset=UTF-8' );
mb_internal_encoding('UTF-8');
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=320, user-scalable=no" />
<title>Wikipedia Location Search</title>
<style type="text/css">
body {
	font-family: "Helvetica";
	font-size: 16px;
	background: white;
	margin: 0;
	padding: 0;
	
	width: 320px;
}
h1, h2, .filter {
	background-color: #708099;
	color: white;
	font-weight: bold;
	text-align: center;
	text-shadow: 0px -1px 0px #222;
	margin: 0;
	padding: 4px;
}
h1 {
	font-size: 20px;
}
h2 {
	font-size: 18px;
}
input {
	font-size: 16px;
	text-shadow: none
}
.searchfield {
	background: white;
	color: black;
	padding: 4px;
	padding-top: 2px;
	
	xborder: solid 1px black;
	-webkit-border-radius:4px;
	-moz-border-radius:4px;
	height: 22px;
}
.searchfield input {
	border: none;
	background: none;
	-webkit-box-shadow: none;
}
ul {
	margin: 0;
	padding: 0;
}
li {
	margin: 0;
	padding: 0;
	list-style: none;
}
li a {
	height: 56px;
	border-bottom: solid 1px #ccc;
	text-decoration: none;
	font-weight: bold;
	color: black;
	display: block;
	vertical-align: middle;
	overflow: hidden;
}
.dist {
	font-weight: normal;
	color: #808080;
}
img {
	border: none;
	vertical-align: middle
}
a:active {
	background: blue;
	color: white;
}

</style>
<script>
function freshSearch() {
	var search=document.getElementById('search');
	search.value='';
	search.focus();
}
</script>
</head>
<body>
<?php

class GeoSearch {
	/**
	 * @param $filter string
	 * @param $lat float Latitutde in degrees
	 * @param $lon float Longitude in degrees
	 * @param $dist float max distance to search in kilometers
	 * @return array of GeoResult objects
	 */
	function lookup( $filter, $lang, $lat, $lon, $dist=2.0 ) {
		$this->profile( 'start' );
		
		$data = $this->rawLookup( $filter, $lang, $lat, $lon, $dist );
		$this->profile( 'data-lookup' );
		
		$this->batchThumbs( $data );
		#$this->profile( 'thumb-lookup' );
		
		return $data;
	}
	
	function profile( $point ) {
		$now = microtime( true );
		if( $point != 'start' ) {
			$this->profiling[$point] = $now - $this->profilingStart;
		}
		$this->profilingStart = $now;
	}
	
	function getProfiling() {
		$list = array();
		foreach( $this->profiling as $point => $seconds ) {
			$ms = number_format( $seconds * 1000.0 );
			$list[] = htmlspecialchars( $point ) . ": $ms";
		}
		$out = implode( ', ', $list );
		return "<!-- $out -->\n";
		#return "<p>$out</p>";
	}
	
	function rawLookup( $filter, $lang, $lat, $lon, $dist=2 ) {
		mysql_connect( 'localhost', 'maps', '' );
		mysql_select_db( 'maps' );
		
		// Really dirty!
		// Approximate kilometers into degrees...
		// This is wildly inaccurate. :)
		$distDeg = $dist * (360.0 / 20000.0);
		
		$encMinLat = mysql_real_escape_string( $lat - $distDeg );
		$encMaxLat = mysql_real_escape_string( $lat + $distDeg );
		$encMinLon = mysql_real_escape_string( $lon - $distDeg );
		$encMaxLon = mysql_real_escape_string( $lon + $distDeg );
		
		$filter = trim( $filter );
		$hasFilter = ($filter !== '');
		if( $hasFilter ) {
			$encFilter = mysql_real_escape_string(
				str_replace(
					array( '%', '_' ),
					array( '\%', '\_' ),
					$filter ) );
		}
		
		$sql = "select lat,lon,titel_$lang AS title,type,style,lang,image," .
			$this->distFunc( $lat, $lon ) .
			" from pub_C_geo_id" .
			" where lat between $encMinLat and $encMaxLat" .
			" and lon between $encMinLon and $encMaxLon" .
			($hasFilter
				? " AND UCASE(titel_$lang) LIKE UCASE('%$encFilter%')"
				: " AND titel_$lang IS NOT NULL") .
			" ORDER BY dist LIMIT 25";
		#echo "<!-- $sql -->\n";
		$result = mysql_query( $sql );
		if( $result === false ) {
			throw new Exception( mysql_error() );
		}
		
		$data = array();
		while( $row = mysql_fetch_object( $result ) ) {
			$data[] = new GeoResult( $lang, $row );
		}
		mysql_free_result( $result );
		
		return $data;
	}
	
	/**
	 * A scary little SQL function to calculate distance :D
	 * Over long distance this won't be accurate as it's
	 * a linear distance and would tunnel through the earth,
	 * but it'll do fine for ordering.
	 */
	function distFunc( $lat, $lon ) {
		$radius = 6371; // Approx mean radius of Earth in km
		
		$x = $radius * sin(deg2rad($lat)) * cos(deg2rad($lon));
		$y = $radius * sin(deg2rad($lat)) * sin(deg2rad($lon));
		$z = $radius * cos(deg2rad($lat));
		
		$encRadius = mysql_real_escape_string($radius);
		$encX = mysql_real_escape_string($x);
		$encY = mysql_real_escape_string($y);
		$encZ = mysql_real_escape_string($z);
		
		$sql = "SQRT(
			POW($encX-$encRadius*SIN(RADIANS(lat))*COS(RADIANS(lon)),2)+
			POW($encY-$encRadius*SIN(RADIANS(lat))*SIN(RADIANS(lon)),2)+
			POW($encZ-$encRadius*COS(RADIANS(lat)),2)) AS dist";
		return $sql;

	}
	
	function batchThumbs( $data, $size=56 ) {
		// fixme lookup on separate sites
		$lookups = array();
		foreach( $data as $item ) {
			$base = $item->getThumbBase();
			if( $item->getThumbName() ) {
				$file = 'Image:' . $item->getThumbName();
				@$lookups[$base][$file] = $item;
			}
		}
		
		foreach( $lookups as $base => $items ) {
			$encTitles = array();
			foreach( array_keys( $items ) as $filename ) {
				if( $filename ) {
					$encTitles[] = urlencode( $filename );
				}
			}
			$encWidth = urlencode( $size * 1.5 ); // most wider than tall
			$encHeight = urlencode( $size );
			$url = "$base?action=query" .
				"&titles=" . implode( '|', $encTitles ) .
				"&prop=imageinfo" .
				"&iiprop=url" .
				"&iiurlwidth=$encWidth" .
				"&iiurlheight=$encHeight" .
				"&format=php";
			$raw = unserialize( file_get_contents( $url ) );
			#echo "<pre>";
			#echo "$url\n";
			#var_dump( $raw );
			#echo "</pre>";
			
			// First, get a mapping from our raw names to canonical names.
			$nameMapping = array();
			if( isset( $raw['query']['normalized'] ) ) {
				foreach( $raw['query']['normalized'] as $pair ) {
					$nameMapping[$pair['to']] = $pair['from'];
				}
			}
			
			// Now for each thumb we got data on, grab and store the URL
			foreach( $raw['query']['pages'] as $page ) {
				// Find the result item this was requested for
				if( isset( $nameMapping[$page['title']] ) ) {
					$rawName = $nameMapping[$page['title']];
				} else {
					$rawName = $page['title'];
				}
				$item = $items[$rawName];
				
				// Save the thumb info!
				if( isset( $page['imageinfo'][0]['thumburl'] ) ) {
					#echo "for '$rawName' '{$page['imageinfo'][0]['thumburl']}'\n";
					$item->setThumbData( $page['imageinfo'][0]['thumburl'] );
				
					// fixme... 'thumbwidth', 'thumbheight', and 'thumburl' seem useless o_O
					#echo "<pre>";
					#var_dump( $page );
					#die();
				}
			}
			
			$this->profile( "thumb-lookup-$base" );
		}
	}
}


class GeoResult {
	var $thumbUrl = false;
	
	function __construct( $lang, $row ) {
		$this->lat = $row->lat;
		$this->lon = $row->lon;
		
		// Article
		$this->lang = $lang;
		$this->title = html_entity_decode( $row->title, ENT_QUOTES, 'UTF-8' ); // sigh
		
		// For thumbnail...
		$this->imageLang = $row->lang;
		$this->image = html_entity_decode( $row->image, ENT_QUOTES, 'UTF-8' ); // sigh
		
		// For styling or icons...
		$this->type = $row->type;
		$this->style = $row->style;
		
		// For ordering...
		$this->dist = $row->dist;
	}
	
	function getThumb( $size ) {
		$url = $this->getThumbUrl( $size );
		return squareThumb( $url, $size );
	}
	
	function getThumbBase() {
		$encLang = urlencode( $this->imageLang );
		return "http://$encLang.wikipedia.org/w/api.php";
	}
	
	function getThumbName() {
		return $this->image;
	}
	
	function setThumbData( $url ) {
		$this->thumbUrl = $url;
	}
	
	function getThumbUrl() {
		if( $this->thumbUrl ) {
			return $this->thumbUrl;
		} else {
			//return 'images/icon.png';
			return 'http://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Crystal_document2.png/56px-Crystal_document2.png';
		}
	}
	
	/**
	 * fixme... batch queries
	 */
	/*
	function getThumbUrl( $size=20 ) {
		// todo
		if( $this->image ) {
			$data = $this->imageInfo( $this->getThumbBase(),
				$this->image, $size );
			
			//echo "<pre>";
			//var_dump( $data );

			if( isset( $data['query']['pages'] ) ) {
				$pages = array_values( $data['query']['pages'] );
				if( isset( $pages[0]['imageinfo'][0]['thumburl'] ) ) {
					return $pages[0]['imageinfo'][0]['thumburl'];
				} else {
					//echo "no thumb url\n";
				}
			} else {
				//echo "No query pages.\n";
			}
			
			//die();
		}
		return '';
	}
	
	function imageInfo( $api, $name, $size ) {
		$encName = urlencode( $name );
		$encSize = urlencode( intval( $size ) );
		$url = "$api?action=query" .
			"&titles=Image:$encName" .
			"&prop=imageinfo" .
			"&iiprop=url" .
			"&iiurlwidth=$encSize" .
			"&iiurlheight=$encSize" .
			"&format=php";
		
		return unserialize( file_get_contents( $url ) );
	}
	*/
	
	function getUrl() {
		$encLang = urlencode( str_replace( '_', '-', $this->lang ) );
		$wap = true;
		if( $wap ) {
			return "http://$encLang.wap.wikipedia.org/transcode.php?go=" .
				urlencode( $this->title );
		} else {
			return "http://$encLang.wikipedia.org/wiki/" .
				urlencode( str_replace( ' ', '_', $this->title ) );
		}
	}
	
	function getLink( $html ) {
		return '<a href="' .
			htmlspecialchars( $this->getUrl() ) .
			'">' .
			$html .
			'</a>';
	}
	
	function getTitle() {
		return htmlspecialchars(
			$this->trim(
				$this->title,
				22 ));
	}
	
	function getDistance() {
		return number_format( $this->dist, 1 ) . "km";
	}
	
	function trim( $text, $len ) {
		if( mb_strlen( $text ) > $len ) {
			$text = mb_substr( $text, 0, $len ) . '…';
		}
		return $text;
	}
	
	function format() {
		return
			$this->getLink(
				$this->getThumb( 56 ) .
				'&nbsp;' .
				'&nbsp;' .
				"<span class='dist'>" .
				$this->getDistance() .
				"</span>" .
				'&nbsp;' .
				$this->getTitle()
				);
	}
}

function squareThumb( $url, $size=56 ) {
	return '<img src="' .
		htmlspecialchars( $url ) .
		"\" width=\"$size\" height=\"$size\" />";
}

$samples = array(
	'San Francisco (Golden Gate)' => array( 37.817597, -122.4783,
		'http://upload.wikimedia.org/wikipedia/commons/thumb/5/5a/GoldenGate01.JPG/75px-GoldenGate01.JPG' ),
	'San Francisco (downtown)' => array( 37.795192, -122.402786,
		'http://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/SF_Transamerica_full_CA.jpg/56px-SF_Transamerica_full_CA.jpg'),
	'San Francisco (Castro)' => array( 37.762, -122.435,
		'http://upload.wikimedia.org/wikipedia/en/thumb/c/c9/Castro_Theater_San_Francisc.jpg/56px-Castro_Theater_San_Francisc.jpg' ),
	'New York (Empire State)' => array( 40.748433, -73.985694,
		'http://upload.wikimedia.org/wikipedia/commons/thumb/d/d2/Manhattan_at_Dusk_by_slonecker.jpg/56px-Manhattan_at_Dusk_by_slonecker.jpg' ),
	'Berlin (Alexanderplatz)' => array( 52.521944, 13.413056,
		'http://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Berlin_Alexanderplatz_Fernsehturm.JPG/75px-Berlin_Alexanderplatz_Fernsehturm.JPG' ),
);
	

$supportedLanguages = array(
	'en','de','es','fr','it','ja','nl','pl','pt','ru','sv',
	'fi','no','eo','sk','da','cs','tr','zh','ca','is' );

$search = isset( $_GET['search'] ) && is_string( $_GET['search'] ) ? $_GET['search'] : '';
$lang = isset( $_GET['lang'] )  && is_string( $_GET['lang'] )
	&& preg_match( '/^[a-z]+(-[a-z]+)*$/', $_GET['lang'] )
	&& in_array( $_GET['lang'], $supportedLanguages ) ? $_GET['lang'] : 'en';
$lat = isset( $_GET['lat'] ) && is_string( $_GET['lat'] ) ? floatval( $_GET['lat'] ) : null;
$lon = isset( $_GET['lon'] ) && is_string( $_GET['lon'] ) ? floatval( $_GET['lon'] ) : null;

if( isset( $lat ) && isset( $lon ) ) {
	$encSearch = htmlspecialchars( $search );
	print "<form class='filter'>\n";
	print "Filter:&nbsp;";
	
	print "<span class='searchfield'>";
	print "<input id='search' name='search' value=\"$encSearch\" autocorrect=\"on\" />";
	print "<img src='images/cancel.png' width='16' height='16' onclick='freshSearch()'/>";
	print "</span>\n";
	
	print "<input type='hidden' name='lat' value='$lat' />\n";
	print "<input type='hidden' name='lon' value='$lon' />\n";
	print "<input type='hidden' name='lang' value='$lang' />\n";
	//print "<input type='submit' value='Go' />\n";
	
	print "</form>\n";
	
	$geo = new GeoSearch();
	$results = $geo->lookup( $search, $lang, $lat, $lon, 10 );
	
	print "<ul>";
	if( $results ) {
		foreach( $results as $item ) {
			print '<li>' . $item->format() . "</li>\n";
		}
	} else {
		print "<li>No results.</li>\n";
	}
	print "</ul>\n";
	print $geo->getProfiling();
} else {
	print "<h1>Wikipedia Local Search</h1>\n";
	print "<h2>Sample locations:</h2>\n";
	print "<ul>\n";
	foreach( $samples as $name => $coords ) {
		list( $lat, $lon, $thumb ) = $coords;
		print "<li><a href=\"?lat=$lat&lon=$lon&lang=$lang\">";
		print squareThumb( $thumb );
		print "&nbsp;&nbsp;";
		print htmlspecialchars( $name );
		print "</a></li>\n";
	}
	print "</ul>\n";
}

?>
</body>
</html>
