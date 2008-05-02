<?php
if( php_sapi_name() !== 'cli' ) die("no");
?><h1>Handy bookmarklets</h1>
<ul>
<?php

$filenames = $_SERVER['argv'];
array_shift( $filenames );
foreach( $filenames as $filename ) {
	$name = preg_replace( '/\.js$/', '', $filename );
	$file = file_get_contents($filename);
	
	$url = 'javascript:' . trim( preg_replace("/[\t\n\r]+/", " ", $file ) );
	
	print "<li><a href=\"" .
		htmlspecialchars( $url ) .
		"\">$name</a></li>\n";
}

?>
</ul>
