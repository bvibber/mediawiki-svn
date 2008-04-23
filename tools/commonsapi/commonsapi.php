<?PHP
/*
  Wikimedia Commons image API
  (c) 2008 by Magnus Manske
  Released under GPL
  RUns at http://tools.wikimedia.de/~magnus/commonsapi.php
*/

// Version
$api_version = '0.9' ;

// License detail data
$license_data = array (
  'GFDL' => array (
    'full_name' => 'GNU Free Documentation License',
    'attach_full_license_text' => '1',
    'attribute_author' => '1',
    'keep_under_same_license' => '1',
    'keep_under_similar_license' => '0',
    'license_logo_url' => 'http://upload.wikimedia.org/wikipedia/commons/thumb/2/22/Heckert_GNU_white.svg/64px-Heckert_GNU_white.svg.png',
    'license_info_url' => 'http://www.gnu.org/copyleft/fdl.html',
    'license_text_url' => 'http://www.gnu.org/licenses/fdl.txt',
  ) ,
  'CC-BY-2.5' => array (
    'full_name' => 'Creative Commons Attribution V2.5',
    'attach_full_license_text' => '0',
    'attribute_author' => '1',
    'keep_under_same_license' => '0',
    'keep_under_similar_license' => '0',
    'license_logo_url' => 'http://upload.wikimedia.org/wikipedia/commons/thumb/7/79/CC_some_rights_reserved.svg/90px-CC_some_rights_reserved.svg.png',
    'license_info_url' => 'http://creativecommons.org/licenses/by/2.5/',
    'license_text_url' => 'http://creativecommons.org/licenses/by/2.5/legalcode',
  ) ,
  'PD' => array (
    'full_name' => 'Public domain',
    'attach_full_license_text' => '0',
    'attribute_author' => '0',
    'keep_under_same_license' => '0',
    'keep_under_similar_license' => '0',
    'license_logo_url' => 'http://upload.wikimedia.org/wikipedia/commons/thumb/6/62/PD-icon.svg/64px-PD-icon.svg.png',
  ) ,
) ;

$license_data['Public domain'] = $license_data['PD'] ;
$license_data['Author died more than 70 years ago public domain images'] = $license_data['PD'] ;
$license_data['Author died more than 70 years ago public domain images']['full_name'] = 'Public domain (author died more than 70 years ago)' ;

// Do not include these categories in the list
$ignore_categories = array (
  'Media with locations',
) ;




// Function to convert text in attributes and between tags to XML by changing some charactes to entities
function myurlencode ( $text ) {
  $text = str_replace ( '<a href="/w' , '<a href="http://commons.wikimedia.org/w' , $text ) ;
	return str_replace ( array ( '&', '"', "'", '<', '>', "'" ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;', '&apos;' ), $text );
}


// End with XML error
function end_run ( $text ) {
  global $api_version ;
  header('Content-type: text/xml; charset=utf-8');
  print '<?xml version="1.0" encoding="UTF-8"?>' ;
  print '<response version="' . $api_version . '">' ;
  print "<error>$text</error>" ;
  print '</response>' ;
  exit ;
}

function wiki2html ( &$wiki ) {
  global $force_html , $img ;
  if ( !$force_html ) return ;
  if ( $wiki == '' ) return ;
  if ( false === strpos ( $wiki , '[[' ) && false === strpos ( $wiki , '{|' ) && false === strpos ( $wiki , '{{' ) && false === strpos ( $wiki , "''" ) ) return ;
  
  $wiki = trim ( $wiki ) ;
  $url = "http://commons.wikimedia.org/w/api.php?format=php&action=parse&text=" . urlencode ( $wiki ) . "&title=" . urlencode ( $img ) ;
  $wiki = unserialize ( file_get_contents ( $url ) ) ;
  $wiki = $wiki['parse']['text']['*'] ;
}


// Fake user agent
ini_set('user_agent','Commons API;');

/* USEFUL TEST IMAGES
	ChathamHDY0016.JPG	                has location
	Sa-warthog.jpg                      has many languages, featured
	Gesammelte_Werke_(Thoma)_1_307.jpg  non-{{Information}} template
*/

// Get parameters
$testing = isset ( $_REQUEST['test'] ) ;
$img = $_REQUEST['image'] ;
$force_html = isset ( $_REQUEST['forcehtml'] ) ;
$thumb_width = $_REQUEST['thumbwidth'] ;
$thumb_height = $_REQUEST['thumbheight'] ;
if ( !$thumb_height || !$thumb_width ) $thumb_height = 0 ;
if ( !$thumb_width ) $thumb_width = 0 ;

// Die with explanation if no image given
if ( !isset ( $img ) ) {
	print "<h1>Wikimedia Commons API</h1>
<hr/>
Usage : <b>commonsapi.php?image=IMAGENAME</b><br/>
IMAGENAME must not have an Image: prefix <br/>
Optional parameters:
<ul>
<li><b>&thumbwidth=XX</b> returns a URL to a thumbnail maximal XX pixel wide</li>
<li><b>&thumbheight=YY</b> returns a URL to a thumbnail maximal YY pixel high (requires <b>thumbwidth</b>)</li>
<li><b>&forcehtml</b> converts wiki text into HTML (slower)</li>
</ul>
Example : <a href='commonsapi.php?image=Sa-warthog.jpg&thumbwidth=150&thumbheight=150'>Sa-warthog.jpg</a><br/>
<i>Note:</i> All returned attributes and texts are entity-encoded.
" ;
	exit ;
}

// Prepare and read rendered image page
$img = str_replace ( ' ' , '_' , $img ) ;
$url = "http://commons.wikimedia.org/wiki/Image:$img" ;
srand(time()) ;
$text = file_get_contents ( $url."?".rand() ) ;

// get file data via "normal" API
// &iiurlwidth=100
$ii_url = "http://commons.wikimedia.org/w/api.php?format=php&action=query&prop=imageinfo&iilimit=500&iiprop=timestamp|user|url|size|sha1|metadata&titles=Image:" . $img ;
if ( $thumb_width != 0 ) $ii_url .= '&iiurlwidth=' . $thumb_width ;
if ( $thumb_height != 0 ) $ii_url .= '&iiurlheight=' . $thumb_height ;

$data = unserialize ( file_get_contents ( $ii_url ) ) ;
$data = array_shift ( $data['query']['pages'] ) ;
$data = $data['imageinfo'] ;
$file_url = $data[0]['url'] ;
if ( isset ( $data[0]['thumburl'] ) ) $thumb_url = $data[0]['thumburl'] ;
else $thumb_url = '' ;
if ( isset ( $data[0]['metadata'] ) ) $metadata = $data[0]['metadata'] ;
else $metadata = array () ;
$filedata = array () ;
if ( !isset ( $data ) ) {
  end_run ( 'File does not exist' ) ;
}
foreach ( $data AS $k => $v ) {
  $fd = $v ;
  if ( isset ( $fd['metadata'] ) ) unset ( $fd['metadata'] ) ;
  if ( isset ( $fd['url'] ) ) unset ( $fd['url'] ) ;
  if ( isset ( $fd['comment'] ) ) unset ( $fd['comment'] ) ;
  $filedata[$k] = $fd ;
}
if ( !isset ( $filedata[0] ) ) $filedata[0] = array () ;
if ( !isset ( $filedata[0]['width'] ) ) $filedata[0]['width'] = 0 ;
if ( !isset ( $filedata[0]['height'] ) ) $filedata[0]['height'] = 0 ;
if ( !isset ( $filedata[0]['size'] ) ) $filedata[0]['size'] = 0 ;


// Info table
$matches = array () ;
preg_match_all ( '/span\s+id="field-[^"]+"/' , $text , &$matches ) ;
$matches = $matches[0] ;
$titles = array () ;
foreach ( $matches AS $m ) {
	$t = array_pop ( explode ( $m , $text , 2 ) ) ;
	$title = array () ;
	preg_match ( '/\s+title="[^"]*"/' , $t , &$title ) ;
	$title = $title[0] ;
	$title = array_pop ( explode ( 'title="' , $title , 2 ) ) ;
	$title = substr ( $title , 0 , -1 ) ;
	$title = urldecode ( $title ) ;
	
	$k = explode ( 'field-' , $m ) ;
	$k = str_replace ( '"' , '' , $k[1] ) ;
	$titles[$k] = $title ;
}
wiki2html ( $titles['description'] ) ;
wiki2html ( $titles['date'] ) ;
wiki2html ( $titles['author'] ) ;
wiki2html ( $titles['source'] ) ;
wiki2html ( $titles['permission'] ) ;
wiki2html ( $titles['otherversions'] ) ;

// Set default description
$desc = array () ;
if ( isset ( $titles['description'] ) ) $desc['default'] = $titles['description'] ;
unset ( $titles['description'] ) ;


// Detect descriptions in many languages
$language_names = array () ;
$matches = array () ;
preg_match_all ( '/div\s+class="description [a-z-]+"/' , $text , &$matches ) ;
$matches = $matches[0] ;
foreach ( $matches AS $m ) {
	$tx = explode ( $m , $text ) ;
	array_shift ( $tx ) ;

	$m = explode ( ' ' , $m ) ;
	$m = array_pop ( $m ) ;
	$m = substr ( $m , 0 , -1 ) ;

	$t = $tx[0] ;
	$t = explode ( '</span>' , $t , 2 ) ;
	
	$ln = array_shift ( $t ) ;
	$ln = array_pop ( explode ( '<b>' , $ln ) ) ;
	$ln = array_shift ( explode ( '</b>' , $ln ) ) ;
	$ln = str_replace ( ':' , '' , $ln ) ;
	$language_names[$m] = $ln ;
	
	$t = array_pop ( $t ) ;
	$tx = explode ( '</div>' , $t ) ;
	$t = "" ;
	while ( count ( $tx ) > 0 ) { // Hackish; works only if there's no <div> in the description. FIXME!
		$t2 = array_shift ( $tx ) ;
		$t .= $t2 ;
		break ;
	}
	
	if ( !isset ( $desc[$m] ) ) $desc[$m] = $t ;
	else $desc[$m] .= "<br/>\n$t" ;
}
if ( !isset ( $desc['default'] ) && isset ( $desc['en'] ) ) $desc['default'] = $desc['en'] ;


// Categories
$self_made = '' ;
$cats = array () ;
preg_match_all ( '/ title="Category:[^"]+"\s*>/' , $text , &$matches ) ;
$matches = $matches[0] ;
foreach ( $matches AS $m ) {
	$m = array_pop ( explode ( ':' , $m , 2 ) ) ;
	$m = explode ( '"' , $m ) ;
	array_pop ( $m ) ;
	$m = implode ( '"' , $m ) ;
	if ( in_array ( $m , $ignore_categories ) ) continue ;
	if ( $m == 'Quality images' ) {
    $titles['qualityimage'] = 1 ; // Just to make sure...
    continue ;
	}
	if ( substr ( $m , 0 , 19 ) == 'Pictures of the day' ) {
    if ( !isset ( $titles['potd'] ) ) $titles['potd'] = trim ( substr ( $m , 21 , 4 ) ) . "0000" ;
    continue ;
	}
	if ( $m == 'Self-published work' ) {
    $self_made = ' selfmade="1"' ;
    continue ;
	}
	$cats[$m] = $m ;
}
ksort ( $cats ) ;

// Licenses (extracted from categories)
$licenses = array () ;
foreach ( $cats AS $cat ) {
	$c = strtolower ( $cat ) ;
	$lic = false ;
	if ( substr ( $c , 0 , 2 ) == 'pd' ) $lic = true ;
	else if ( substr ( $c , 0 , 4 ) == 'gfdl' ) $lic = true ;
	else if ( substr ( $c , 0 , 3 ) == 'cc-' ) $lic = true ;
	else if ( isset ( $license_data[$cat] ) ) $lic = true ;
	
	if ( $lic && substr ( $c , -5 ) == '-self' ) { 
    $self_made = ' selfmade="1"' ;
    $cat = substr ( $cat , 0 , -5 ) ;
  }
	if ( $lic ) {
		$licenses[$cat] = $cat ;
		unset ( $cats[$cat] ) ;
	}
}


// Location
$location = '' ;
$matches = array () ;
preg_match_all ( '/<span\s+class="latitude">([0-9\.]+)<\/span>([^<]*)/' , $text , &$matches ) ;
if ( count ( $matches ) == 3 ) {
	$lat = $matches[1][0] . $matches[2][0] ;
	$matches = array () ;
	preg_match_all ( '/<span\s+class="longitude">([0-9\.]+)<\/span>([^<]*)/' , $text , &$matches ) ;
	if ( count ( $matches ) == 3 ) {
		$lon = $matches[1][0] . $matches[2][0] ;
		if ( $lat != '' && $lon != '' )
			$location = '<location><lat>' . $lat . '</lat><lon>' . $lon . '</lon></location>' ;
	}
}

// Fixes
$justbools = array ( 'featuredpicture' , 'qualityimage' ) ;
foreach ( $justbools AS $b ) {
	if ( isset ( $titles[$b] ) ) $titles[$b] = '1' ;
}

// Test mode output
if ( $testing ) {
	print "<pre>" ;
	print_r ( $data ) ;
/*	print_r ( $titles ) ;
	print_r ( $desc ) ;
	print_r ( $cats ) ;
	print_r ( $licenses ) ;*/
	print "</pre>" ;
	exit ;
}

// Now print this!
header('Content-type: text/xml; charset=utf-8');
print '<?xml version="1.0" encoding="UTF-8"?>' ;
print '<response version="' . $api_version . '">' ;


print '<file>' ;
print '<name>' . myurlencode ( $img ) . '</name>' ;
print '<title>' . myurlencode ( "Image:$img" ) . '</title>' ;
print '<urls>' ;
print '<file>' . $file_url . '</file>' ;
print '<description>' . myurlencode ( $url ) . '</description>' ;
if ( $thumb_url != '' ) print '<thumbnail>' . myurlencode ( $thumb_url ) . '</thumbnail>' ;
print '</urls>' ;
print '<size>' . myurlencode ( $filedata[0]['size'] ) . '</size>' ;
print '<width>' . myurlencode ( $filedata[0]['width'] ) . '</width>' ;
print '<height>' . myurlencode ( $filedata[0]['height'] ) . '</height>' ;
print $location ;
foreach ( $titles AS $k => $v ) {
  if ( $k == 'potd' ) {
    $k = 'pictureoftheday' ;
    $v = substr ( $v , 0 , 4 ) . '-' . substr ( $v , 4 , 2 ) . '-' . substr ( $v , 6 , 2 ) ;
  }
	print '<' . $k . '>' . myurlencode ( $v ) . '</' . $k . '>' ;
}
print '</file>' ;


if ( count ( $metadata ) > 0 ) {
  print '<meta>' ;
  foreach ( $metadata AS $k => $v ) {
    $k = strtolower ( $k ) ;
    print '<' . $k . '>' . myurlencode ( $v ) . '</' . $k . '>' ;
  }
  print '</meta>' ;
}


print '<description>' ;
foreach ( $desc AS $k => $v ) {
	print '<language code="' . $k . '"' ;
	if ( isset ( $language_names[$k] ) ) {
		print ' name="' . myurlencode ( $language_names[$k] ) . '"' ;
	}
	print '>' . myurlencode ( $v ) . '</language>' ;
}
print '</description>' ;


print '<categories>' ;
foreach ( $cats AS $v ) {
	print '<category>' . myurlencode ( $v ) . '</category>' ;
}
print '</categories>' ;


print '<licenses' . $self_made . '>' ;
foreach ( $licenses AS $l ) {
	print '<license>' ;
	print '<name>' . myurlencode ( $l ) . '</name>' ;
	if ( isset ( $license_data[$l] ) ) {
    foreach ( $license_data[$l] AS $k => $v ) {
      print '<' . $k . '>' . myurlencode ( $v ) . '</' . $k . '>' ;
    }
	}
	print '</license>' ;
}
print '</licenses>' ;


print '<versions>' ;
foreach ( $filedata AS $fd ) {
  print '<version>' ;
  foreach ( $fd AS $k => $v ) {
    if ( $k == 'metadata' ) continue ;
    print '<' . $k . '>' . myurlencode ( $v ) . '</' . $k . '>' ;
  }
  print '</version>' ;
}
print '</versions>' ;


print '</response>' ;

?>