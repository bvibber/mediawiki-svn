#!/usr/bin/php
<?php

if(php_sapi_name() !== "cli") {
	echo "You need to run this from the command line.\n";
	exit;
}

if( !isset( $argv[1] ) ) {
	echo "Please pass a directory.\n";
	exit;
}

$dir=$argv[1];

if( !is_dir( $dir ) ) {
	echo "Directory ($dir) doesn't exit.\n";
	exit;
}

$doc = new SimpleXMLElement("<userinfo/>");
$iter = new DirectoryIterator( $dir );

foreach( $iter as $file ) {
	if( $file->isDir() ) continue;
	$commitId = $file->getFilename();

	$text = file_get_contents( $file->getPathname() );
	if( $text === false ) {
		continue;
	}

	$committer = $doc->addChild( "committer" );
	$committer->addAttribute( "id", $commitId );

	$rows = preg_split( "#\n#m", $text );
	foreach( $rows as $line ) {
		$bit =
			preg_split( "#[[:space:]]*:[[:space:]]*#", $line, 2 );

		if( !isset( $bit[1] ) ) continue;

		$name = preg_replace( "#[_ ]#", "-", strtolower( trim( $bit[0] ) ) );

		$committer->addChild( $name, trim( $bit[1] ) );
	}
}

echo $doc->asXML();
echo "\n";
