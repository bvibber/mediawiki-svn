<?php

// Quick and dirty script to recover files from the
// Internet Archive's Wayback Machine.
//
// Pass URLs in as parameters; they'll be saved into
// local dirs with the hostname as first-level dir.
//
// Note there's not good error handling or anything. :)
//
// brion@pobox.com
// 2009-07-17

class Wayback {
	function __construct( $url ) {
		$this->target = $url;
		$waybackUrl = 'http://web.archive.org/web/*/' . $url;
		$waybackData = $this->fetchUrl( $waybackUrl );
		if ($waybackData) {
			$this->versions = $this->parse( $waybackData );
		} else {
			$this->versions = array();
		}
		$this->exists = count($this->versions) > 0;
	}
	
	function fetchLatestNonZero() {
		$available = array_keys( $this->versions );
		sort( $available );
		while( count( $available ) ) {
			$latest = array_pop( $available );
			$data = $this->fetch( $latest );
			if( strlen( $data ) > 0 ) {
				return $data;
			} else {
				return false;
			}
		}
	}
	
	function fetch( $version ) {
		$url = $this->versions[$version];
		return $this->fetchUrl( $url );
	}
	
	function fetchUrl( $url ) {
		return file_get_contents( $url );
	}
	
	function parse( $html ) {
		$versions = array();
		$dom = new DOMDocument();
		$ok = @$dom->loadHTML($html);
		if (!$ok) {
			die( "HTML error!\n" );
		}
		$xpath = new DOMXpath( $dom );
		$nodes = $xpath->query( "*/table/tr/td[@class='mainBody']/a/@href" );
		foreach( $nodes as $node ) {
			$href = strval($node->value);
			if( preg_match( '!^http://web.archive.org/web/(\d+)/!', $href, $matches ) ) {
				$timestamp = $matches[1];
				$versions[$timestamp] = $href;
				//print "$timestamp $href\n";
			}
		}
		return $versions;
	}
}

$args = $_SERVER['argv'];
array_shift($args);
foreach( $args as $arg ) {
	$arg = trim( $arg );
	if( $arg ) {
		$url = $arg;
		$localFilename = preg_replace( '!^.*?:/+!', '', $url );
		$localPath = dirname($localFilename);
		$filename = basename($localFilename);
		echo "$localFilename";
		
		$wayback = new Wayback( $url );
		if( $wayback->exists ) {
			echo " fetching! ";
			$data = $wayback->fetchLatestNonZero();
			if ($data) {
				if( !file_exists( $localPath ) ) {
					mkdir( $localPath, 0755, true );
				}
				$ok = file_put_contents( $localFilename, $data );
				if( $ok ) {
					echo "OK -> $localFilename\n";
				} else {
					echo "FAILED to write -> $localFilename\n";
				}
			} else {
				echo "FAILED to fetch.\n";
			}
		} else {
			echo " missing.\n";
		}
	}
}

#$wayback = new Wayback( "http://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Yes_check.svg/20px-Yes_check.svg.png" );
#if( $wayback->exists ) {
#	echo "YAY\n";
#} else {
#	echo "BOO\n";
#}
