<?PHP
/*
  Wikimedia Commons image API
  (c) 2008 by Magnus Manske
  Released under GPL
  RUns at http://tools.wikimedia.de/~magnus/commonsapi.php
*/


/* USEFUL TEST IMAGES
	ChathamHDY0016.JPG	                has location
	Sa-warthog.jpg                      has many languages, featured
	Gesammelte_Werke_(Thoma)_1_307.jpg  non-{{Information}} template
*/



$api_version = '0.9' ; // Version
$api_data_page_url = 'http://commons.wikimedia.org/w/index.php?title=MediaWiki:Commons_API&action=raw' ; // URL of license data page

$license_data = array () ; // License detail data
$ignore_categories = array () ; // Do not include these categories in the list



# Scans the raw text from http://commons.wikimedia.org/w/index.php?title=MediaWiki:Commons_API
# and uses it as data
function read_from_api_page () {
  global $testing , $license_data , $ignore_categories , $api_data_page_url ;

  $text = file_get_contents ( $api_data_page_url ) ;

  $license_data = array () ;
  $ignore_categories = array () ;
  $h = array () ;
  for ( $a = 0 ; $a < 9 ; $a++ ) $h[$a] = '' ;
  $lines = explode ( "\n" , $text ) ;
  foreach ( $lines AS $l ) {
    $l = trim ( $l ) ;
    if ( $l == '' ) continue ; # Ignore blank lines
    
    if ( substr ( $l , 0 , 1 ) == '=' ) { # Heading
      $cnt = 0 ;
      while ( substr ( $l , 0 , 1 ) == '=' ) {
        $l = substr ( $l , 1 , strlen ( $l ) - 2 ) ;
        $cnt++ ;
      }
      $l = trim ( $l ) ;
      $l = str_replace ( '_' , ' ' , $l ) ;
      if ( $cnt == 2 ) $l = strtolower ( $l ) ;
      $h[$cnt] = $l ;
      for ( $a = $cnt+1 ; $a < 9 ; $a++ ) $h[$a] = '' ;
      continue ;
    }
    
    if ( $h[2] == '' ) continue ; # No section level 2, ignore
    
    if ( $h[2] == 'license data' ) {
      if ( substr ( $l , 0 , 1 ) != ';' ) continue ; # Something else...
      $l = substr ( $l , 1 ) ;
      $l = explode ( ':' , $l , 2 ) ;
      $key = array_shift ( $l ) ;
      $value = array_shift ( $l ) ;
      $key = str_replace ( ' ' , '_' , $key ) ;
      if ( !isset ( $license_data[$h[3]] ) ) $license_data[$h[3]] = array () ;
      
      if ( $key == 'based_on' ) {
        $license_data[$h[3]] = $license_data[$value] ;
      } else $license_data[$h[3]][$key] = $value ;
    } elseif ( $h[2] == 'ignore categories' ) {
      if ( substr ( $l , 0 , 1 ) != '*' ) continue ;
      $l = substr ( $l , 1 ) ;
      $l = str_replace ( '_' , ' ' , $l ) ;
      $ignore_categories[] = trim ( $l ) ;
    }
  }
}



// Function to convert text in attributes and between tags to XML by changing some charactes to entities
function myurlencode ( $text ) {
  $text = str_replace ( '<a href="/w' , '<a href="http://commons.wikimedia.org/w' , $text ) ;
	return str_replace ( array ( '&', '"', "'", '<', '>' ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' ), $text );
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

function print_tag ( $tag , $text ) {
  if ( !isset ( $text ) ) print "<$tag/>" ;
  else print "<$tag>" . myurlencode ( $text ) . "</$tag>" ;
}

function get_same_level_html ( $t ) {
  $cnt = 0 ;
  for ( $pos = 0 ; $pos < strlen ( $t ) ; $pos++ ) {
    if ( $t[$pos] == '<' && $t[$pos+1] == '/' && $cnt == 0 ) return substr ( $t , 0 , $pos - 1 ) ;
    
    if ( $t[$pos] == '<' ) {
      if ( $t[$pos+1] == '/' ) $cnt-- ; // Closing
      else if ( $t[$pos+1] == '!' ) ; // Comment
      else $cnt++ ; // Opening
    } else if ( $t[$pos] == '>' ) {
      if ( $t[$pos-1] == '/' ) $cnt-- ; // Self-closing
    }
    
  }
  return $t ;
}

function try_information_template ( $text ) {
  global $titles ;
  $matches = array () ;
  preg_match_all ( '/span\s+id="field-[^"]+"/' , $text , &$matches ) ;
  $matches = $matches[0] ;
  if ( count ( $matches ) == 0 ) return false ;

  $ret = false ;
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
    $ret = true ;
  }
  wiki2html ( $titles['description'] ) ;
  wiki2html ( $titles['date'] ) ;
  wiki2html ( $titles['author'] ) ;
  wiki2html ( $titles['source'] ) ;
  wiki2html ( $titles['permission'] ) ;
  wiki2html ( $titles['otherversions'] ) ;
  return $ret ;
}

function try_fileinfo_template ( $text ) {
  global $titles ;
  $matches = array () ;
  preg_match_all ( '/th[^>]+id="fileinfotpl_[^"]+"/' , $text , &$matches ) ;
  $matches = $matches[0] ;

  if ( count ( $matches ) == 0 ) return false ;

#  header('Content-type: text/plain; charset=utf-8');
  $ret = false ;
  $t2 = array () ;
  foreach ( $matches AS $m ) {
    $k = array_pop ( explode ( 'fileinfotpl_' , $m , 2 ) ) ;
    $k = array_shift ( explode ( '"' , $k , 2 ) ) ;
    
    $t = array_pop ( explode ( $m , $text , 2 ) ) ;
    $t = array_pop ( explode ( '<td' , $t , 2 ) ) ;
    $t = array_pop ( explode ( '>' , $t , 2 ) ) ;
    
    $t = get_same_level_html ( $t ) ;
    
    if ( substr ( $t , 0 , 3 ) == '<b>' ) $t = substr ( $t , 3 ) ;
    if ( substr ( $t , -4 , 4 ) == '</b>' ) $t = substr ( $t , 0 , -4 ) ;

    if ( isset ( $t2[$k] ) ) {
      if ( $t2[$k] != $t ) $t2[$k] .= "\n$t" ;
    } else $t2[$k] = $t ;
    $ret = true ;
  }
  $titles['description'] = array() ;
  $titles['date'] = array() ;
  $titles['author'] = array() ;
  $titles['source'] = array() ;
  $titles['permission'] = array() ;
  
  foreach ( $t2 AS $k => $v ) {
    if ( $k == 'aut' ) $titles['author'][] = $v ;
    if ( $k == 'desc' ) $titles['description'][] = $v ;
    if ( $k == 'date' ) $titles['date'][] = $v ;
    if ( $k == 'src' ) $titles['source'][] = $v ;
    if ( $k == 'perm' ) $titles['permission'][] = $v ;
  }
  
  foreach ( $titles AS $k => $v ) $titles[$k] = implode ( "\n" , $v ) ;
#  exit;
  return $ret ;
}


function die_with_form () {
	print "<h1>Wikimedia Commons API</h1>
<hr/>
Usage : <b>commonsapi.php?image=IMAGENAME</b><br/>
IMAGENAME must not have an Image: prefix <br/>
Optional parameters:
<ul>
<li><b>&thumbwidth=XX</b> returns a URL to a thumbnail maximal XX pixel wide</li>
<li><b>&thumbheight=YY</b> returns a URL to a thumbnail maximal YY pixel high (requires <b>thumbwidth</b>)</li>
<li><b>&languages=en|de|default</b> returns only the languages for the listed codes; separate with \"|\" (default: all languages)</li>
<li><b>&forcehtml</b> converts wiki text into HTML (slower, but gets rid of annoying wiki markup)</li>
<li><b>&versions</b> adds information about all former versions of the file</li>
<li><b>&meta</b> adds meta information that is stored with the file (e.g., EXIF data)</li>
</ul>
Examples :
<ul>
<li><a href='commonsapi.php?image=Sa-warthog.jpg&thumbwidth=150&thumbheight=150&versions&meta'>Sa-warthog.jpg</a> with thumbnail, version, and meta information</li>
<li><a href='commonsapi.php?image=Yarra_Panorama.jpg&forcehtml'>Yarra_Panorama.jpg</a> (contains geocoding, which resolves to a <tt>&lt;location&gt;</tt> tag)</li>
</ul>
<i>Note:</i> All returned attributes and texts are entity-encoded (<i><tt>\"'<>&</tt></i> are replaced with XML entities).<br/><br/>

(You want to help making this API better? Data source for license information is <a href=\"http://commons.wikimedia.org/wiki/MediaWiki:Commons_API\">here</a>. Bugs and feature requests go to <a href=\"http://commons.wikimedia.org/wiki/User_talk:Magnus_Manske\">this guy</a>.)
" ;
	exit ;
}




# ___________________
# MAIN PROGRAM


// Fake user agent
ini_set('user_agent','Commons API;');

// Get parameters
$testing = isset ( $_REQUEST['test'] ) ;
$versions = isset ( $_REQUEST['versions'] ) ;
$meta = isset ( $_REQUEST['meta'] ) ;
$img = $_REQUEST['image'] ;
$force_html = isset ( $_REQUEST['forcehtml'] ) ;
$get_languages = $_REQUEST['languages'] ;
$thumb_width = $_REQUEST['thumbwidth'] ;
$thumb_height = $_REQUEST['thumbheight'] ;
if ( !$thumb_height || !$thumb_width ) $thumb_height = 0 ;
if ( !$thumb_width ) $thumb_width = 0 ;
if ( !isset ( $get_languages ) ) $get_languages = '' ;

if ( substr ( strtolower ( $img ) , 0 , 6 ) == 'image:' ) $img = substr ( $img , 6 ) ;

// Read in license information
read_from_api_page () ;


// Die with explanation if no image given
if ( !isset ( $img ) ) die_with_form () ;

// Prepare and read rendered image page
$img = str_replace ( ' ' , '_' , $img ) ;
$url = "http://commons.wikimedia.org/wiki/Image:$img" ;
srand(time()) ;
$text = file_get_contents ( $url."?".rand() ) ;

// get file data via "normal" API
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
$titles = array () ;

$found = false ;
if ( !$found ) $found = try_information_template ( $text ) ;
if ( !$found ) $found = try_fileinfo_template ( $text ) ;

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
	else if ( $desc[$m] != $t ) $desc[$m] .= "<br/>\n$t" ;
}
if ( !isset ( $desc['default'] ) && isset ( $desc['en'] ) ) $desc['default'] = $desc['en'] ;

if ( $get_languages != '' ) {
  $gl = explode ( '|' , $get_languages ) ;
  $d2 = array () ;
  foreach ( $gl AS $l ) {
    if ( isset ( $desc[$l] ) ) $d2[$l] = $desc[$l] ;
  }
  $desc = $d2 ;
}


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
$lat = '' ;
$lon = '' ;
$matches = array () ;
preg_match_all ( '/<span\s+class="latitude">([0-9\.]+)<\/span>([^<]*)/' , $text , &$matches ) ;
if ( count ( $matches ) == 3 ) {
	$lat = $matches[1][0] . $matches[2][0] ;
	$matches = array () ;
	preg_match_all ( '/<span\s+class="longitude">([0-9\.]+)<\/span>([^<]*)/' , $text , &$matches ) ;
	if ( count ( $matches ) == 3 ) {
		$lon = $matches[1][0] . $matches[2][0] ;
	}
}

if ( $lat . $lon == '' ) {
  $matches = array () ;
  preg_match_all ( '/<span\s+class="geo"[^>]*>([\-0-9\.]+)\s*;\s*([\-0-9\.]+)<\/span>/' , $text , &$matches ) ;
  if ( count ( $matches ) == 3 ) {
    $lat = $matches[1][0] ;
    $lon = $matches[2][0] ;
  }
}

if ( $lat != '' && $lon != '' ) $location = '<location><lat>' . $lat . '</lat><lon>' . $lon . '</lon></location>' ;

// Fixes
$justbools = array ( 'featuredpicture' , 'qualityimage' ) ;
foreach ( $justbools AS $b ) {
	if ( isset ( $titles[$b] ) ) $titles[$b] = '1' ;
}

// Now print this!
header('Content-type: text/xml; charset=utf-8');
print '<?xml version="1.0" encoding="UTF-8"?>' ;
print '<response version="' . $api_version . '">' ;


print '<file>' ;
print '<name>' . str_replace ( '_' , ' ' , myurlencode ( $img ) ) . '</name>' ;
print '<title>' . myurlencode ( "Image:$img" ) . '</title>' ;
print '<urls>' ;
print '<file>' . $file_url . '</file>' ;
print '<description>' . myurlencode ( $url ) . '</description>' ;
if ( $thumb_url != '' ) print '<thumbnail>' . myurlencode ( $thumb_url ) . '</thumbnail>' ;
print '</urls>' ;

print_tag ( 'size' , $filedata[0]['size'] ) ;
print_tag ( 'width' , $filedata[0]['width'] ) ;
print_tag ( 'height' , $filedata[0]['height'] ) ;
print_tag ( 'uploader' , $filedata[0]['user'] ) ;
print_tag ( 'upload_date' , $filedata[0]['timestamp'] ) ;
print_tag ( 'sha1' , $filedata[0]['sha1'] ) ;

print $location ;
foreach ( $titles AS $k => $v ) {
  if ( $k == 'potd' ) {
    $k = 'pictureoftheday' ;
    $v = substr ( $v , 0 , 4 ) . '-' . substr ( $v , 4 , 2 ) . '-' . substr ( $v , 6 , 2 ) ;
  }
	print '<' . $k . '>' . myurlencode ( $v ) . '</' . $k . '>' ;
}
print '</file>' ;


if ( count ( $metadata ) > 0 and $meta ) {
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

if ( $versions ) {
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
}

print '</response>' ;

?>