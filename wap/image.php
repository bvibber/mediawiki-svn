<?php # phpinfo();
	error_reporting(E_ALL);
# echo($_SERVER['QUERY_STRING']);
# echo($_SERVER['HTTP_ACCEPT']);

function fatalError($msg) {
	header("HTTP/1.0 500 Internal Server Error");
	echo "$msg\n";
	exit(0);
}

	$lang = strval(@$_GET['language']);
	$name = strval(@$_GET['name']);
	$width = intval(@$_GET['width']);
	
	// Validate...
	if (!preg_match('/^[a-z][a-z_]*[a-z]$/', $lang)
		|| $width < 0
		|| strlen( $name ) == 0
		|| strpos( $name, '|') !== false ) {
		fatalError("Invalid input.");
	}
	
	// Fetch via MediaWiki API
	$queryProps = array(
		'action' => 'query',
		'prop' => 'imageinfo',
		'titles' => 'Image:' . $name,
		'iiprop' => 'url|mime',
		'redirects' => 'true',
		'format' => 'php' );
	if ($width) {
		$queryProps['iiurlwidth'] = $width;
	}
	

	$url = "http://$lang.wikipedia.org/w/api.php?" .
		http_build_query($queryProps, '', '&');
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$curlResultString = curl_exec($ch);
	if (!is_string($curlResultString)) {
		fatalError("Couldn't reach remote image repository.");
	}
	curl_close($ch);

	$data = @unserialize( $curlResultString ); // We better trust the foreign site!
	if (!$data) {
		fatalError("Invalid data from remote image repository.");
	}
	
	if (!empty($data['query']['pages'])) {
		$page = current($data['query']['pages']);
		if (!empty($page['imageinfo'])) {
			$info = current($page['imageinfo']);
			if (!empty($info['thumburl'])) {
				$src = $info['thumburl'];
			} elseif (!empty($info['url'])) {
				$src = $info['url'];
			} else {
				fatalError("Missing image data.");
			}
		} else {
			fatalError("No such image.");
		}
	} else {
		fatalError("API lookup error.");
	}

 	$img = (FALSE);
	$sf = (strtolower(preg_replace('/^.*\./', '', $src)));
	
	if (!function_exists('imagecreatefrompng')) {
		// Crappy pass-through for testing on box w/o GD
		header("Location: $src");
		exit;
	}
	
	switch($sf)
	{
		case 'gif' :
			$img = imagecreatefromgif($src);
			break;
		case 'jpg' :
		case 'jpeg' :
		case 'jfif' :
			$img = imagecreatefromjpeg($src);
			break;
		case 'svg' :
#			$img = imagecreatefromsvg($src);
#			break;
		case 'png' :
			// Reapply onto a white bg,
			// otherwise it ends up on black which is ugly.
			$imgSource = imagecreatefrompng($src);
			$w = imagesx($imgSource);
			$h = imagesy($imgSource);
			$img = imagecreatetruecolor($w, $h);
			$white = imagecolorallocate($img, 255, 255, 255);
			imagefilledrectangle($img, 0, 0, $w, $h, $white);
			imagecopyresampled($img, $imgSource,
				0, 0, // dst
				0, 0, // src
				$w, $h, // dst
				$w, $h); // src
			break;
		case 'bmp' :
			$img = imagecreatefrombmp($src);
			break;
		case 'xbm' :
			$img = imagecreatefromxbm($src);
			break;
		case 'xpm' :
			$img = imagecreatefromxpm($src);
			break;
		default :
			$img = imagecreatefromstring($src);
			break;
	}

	if($img)
	{
		$accept = isset( $_SERVER['HTTP_ACCEPT'] ) ? $_SERVER['HTTP_ACCEPT'] : '';
		$types = array_map( 'trim', explode( ',', $accept ) );
		$mozilla = substr( $_SERVER['HTTP_USER_AGENT'], 0, strlen( 'Mozilla' ) ) == 'Mozilla';
		if ( $mozilla || in_array( 'image/gif', $types ) ) {
			header('Content-Type: image/gif');
			imagegif($img);
		} else {
			header('Content-Type: image/vnd.wap.wbmp');
			imagewbmp($img);
		}
	}
?>
