<?php

# $_SERVER['REQUEST_URI'] has two different definitions depending on PHP version
if ( preg_match( '!^([a-z]*://)([a-z.]*)(/.*)$!', $_SERVER['REQUEST_URI'], $matches ) ) {
	$prot = $matches[1];
	$serv = $matches[2];
	$loc = $matches[3];
} else {
	$prot = "http://";
	$serv = strlen($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
	$loc = $_SERVER["REQUEST_URI"];
}
$encUrl = htmlspecialchars( $prot . $serv . $loc );

header( 'HTTP/1.1 404 Not Found' );

$standard_404=<<<ENDTEXT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
    <title>Wikimedia page not found: $encUrl</title>
    <link rel="shortcut icon" href="/favicon.ico" />
</head>
<body>
<h1><a href="http://en.wikipedia.org/wiki/HTTP_404">404 error</a>: File not found</h1>
<p>
    The <a href="http://en.wikipedia.org/wiki/Uniform_Resource_Locator">URL</a>
    you requested was not found. Maybe you would like to look at:
</p>
<ul>
    <li><a href="/">The main page</a></li>
    <li><a href="http://download.wikimedia.org">The list of Wikimedia downloads</a></li>
</ul>
<hr noshade="noshade" />
<p>
<i>A project of the <a href="http://wikimediafoundation.org/">Wikimedia
foundation</a></i>.
</p>
</body>
</html>
ENDTEXT;

print $standard_404;
/*
if( preg_match("|%2f|i", $loc) ||
preg_match("|^/upload/.*?|i",$loc) || preg_match("|^/style/.*?|i",$loc) ||
preg_match("|^/wiki/.*?|i",$loc) || preg_match("|^/w/.*?|i",$loc) ||
preg_match("|^/extensions/.*?|i",$loc) ) {
	print ($standard_404);
} 

else {
if( in_array( $loc, array(
	'/Broccoli',
	'/Romanesco',
	'/Mandelbrot_set',
	'/Mandelbrotmenge' ) ) ) {
	# HACKHACKHACK
	# Special case for broken URLs which somebody
	# put into a print ad. Why??!!?!??!?!?!?!
	if (headers_sent()) return false;
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ".$prot.$serv."/wiki".$loc);
	header("X-Wikimedia-Debug: prot=$prot serv=$serv loc=$loc");
}
	$target = $prot . $serv . "/wiki" . $loc;
	$encTarget = htmlspecialchars( $target );
$redirect_404=<<<END
<html>
<head>
<title>
Wikimedia page not found: $encUrl
</title>
<link rel="shortcut icon" href="/favicon.ico" />
<meta http-equiv="Refresh" content="5; URL=$encTarget">
</head>
<body>
<h1><a href="http://en.wikipedia.org/wiki/404_error">404 error</a>: File not found</h1>
The <a href="http://en.wikipedia.org/wiki/Uniform_resource_locator">URL</a>
you requested was not found.

<p><b>Did you mean to type <a href="$encTarget">$encTarget</a>?</b>
You will be automatically redirected there in five seconds.</p>

<p> Maybe you would like to look at:
<P>
<UL><LI><a href="/">The main page</A>
    <li><a href="http://download.wikimedia.org">The list of Wikimedia downloads</A>
</UL>
<P>
<hr noshade/>
<p><i>A project of the <a href="http://wikimediafoundation.org/">Wikimedia
foundation</a></i>.</p>
</body>
</html>
END;
	print $redirect_404;
}
*/
?>
