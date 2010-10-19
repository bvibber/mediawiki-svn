<?php
/**
 * RSS-Feed MediaWiki extension
 *
 * @file
 * @ingroup Extensions
 * @version 1.7
 * @author mutante, Daniel Kinzler, Rdb, Mafs, Alxndr, Chris Reigrut, K001
 * @author Kellan Elliott-McCrea <kellan@protest.net> -- author of MagpieRSS
 * @author Jeroen De Dauw
 * @author Jack Phoenix <jack@countervandalism.net>
 * @copyright © Kellan Elliott-McCrea <kellan@protest.net>
 * @copyright © mutante, Daniel Kinzler, Rdb, Mafs, Alxndr, Chris Reigrut, K001
 * @link http://www.mediawiki.org/wiki/Extension:RSS Documentation
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( "This is not a valid entry point.\n" );
}

// Extension credits that will show up on Special:Version
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'RSS feed',
	'author' => array(
		'Kellan Elliott-McCrea',
		'mutante',
		'Daniel Kinzler',
		'Rdb',
		'Mafs',
		'Alxndr',
		'Wikinaut',
		'Chris Reigrut',
		'K001',
		'Jack Phoenix',
		'Jeroen De Dauw',
		'Mark A. Hershberger'
	),
	'version' => '1.8',
	'url' => 'http://www.mediawiki.org/wiki/Extension:RSS',
	'descriptionmsg' => 'rss-desc',
);

// Internationalization file and autoloadable classes
$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['RSS'] = $dir . 'RSS.i18n.php';
$wgAutoloadClasses['RSSData'] = $dir . 'RSSData.php';
$wgAutoloadClasses['RSSCache'] = $dir . 'RSSCache.php';

$wgHooks['ParserFirstCallInit'][] = 'wfRssExtension';

# Extension hook callback function
function wfRssExtension( &$parser ) {
	# Install parser hook for <rss> tags
	$parser->setHook( 'rss', 'renderRss' );
	return true;
}

# Parser hook callback function
function renderRss( $input, $args, $parser, $frame ) {
	global $wgOutputEncoding;

	// Kill parser cache
	$parser->disableCache();

	if ( !$input ) {
		return ''; # if <rss>-section is empty, return nothing
	}

	# Parse fields in rss section
	$url = $input;

	# Get charset from argument array
	if( isset( $args['charset'] ) ) {
		$charset = $args['charset'];
	} else {
		$charset = $wgOutputEncoding;
	}

	# Get max number of headlines from argument-array
	if( isset( $args['max'] ) ) {
		$maxheads = $args['max'];
	} else {
		$maxheads = 32;
	}

	# Get short flag from argument array
	# If short is set, no description text is printed
	if( isset( $args['short'] ) ) {
		$short = true;
	} else {
		$short = false;
	}

	# Get reverse flag from argument array
	if( isset( $args['reverse'] ) ) {
		$rss->items = array_reverse( $rss->items );
	}

	# Get date format from argument array
	if ( isset( $args['date'] ) ) {
		$date = $args['date'];
	} else {
		$date = 'd M Y H:i';
	}

	# Get highlight terms from argument array
	if( isset( $args['highlight'] ) ) {
		$rssHighlight = $args['highlight'];
		$rssHighlight = str_replace( '  ', ' ', $rssHighlight );
		$rssHighlight = explode( ' ', trim( $rssHighlight ) );
	} else {
		$rssHighlight = false;
	}

	# Get filter terms from argument array
	if( isset( $args['filter'] ) ) {
		$rssFilter = $args['filter'];
		$rssFilter = str_replace( '  ', ' ', $rssFilter );
		$rssFilter = explode( ' ', trim( $rssFilter ) );
	} else {
		$rssFilter = false;
	}

	# Filterout terms
	if( isset( $args['filterout'] ) ) {
		$rssFilterout = $args['filterout'];
		$rssFilterout = str_replace( '  ', ' ', $rssFilterout );
		$rssFilterout = explode( ' ', trim( $rssFilterout ) );
	} else {
		$rssFilterout = false;
	}

	if( isset( $args['template'] ) ) {
		$template = 'Template:'.$args['template'];
	} else {
		$template = wfMsgNoTrans("rss-item");
	}

	$headcnt = 0;

	# Fetch RSS. May be cached locally.
	# Refer to the documentation of MagpieRSS for details.
	if ( !function_exists( 'fetch_rss' ) ) {
		include( dirname( __FILE__ ) . '/RSSFetch.php' ); // provides fetch_rss() function
	}
	$rss = fetch_rss( $url );

	# Check for errors.
	if ( empty( $rss ) ) {
		return wfMsg( 'rss-empty', $url );
	}

	if ( $rss->ERROR ) {
		return '<div>' . wfMsg( 'rss-error', $url, $rss->ERROR ) . '</div>';
	}

	if ( !is_array( $rss->items ) ) {
		return '<div>' . wfMsg( 'rss-empty', $url ) . '</div>';
	}

	$output = '';

	/* This would be better served by preg_replace_callback, but
	 * I can't create a callback that carries $item in PHP < 5.3
	 */
	if ( $template ) {
		$headcnt = 0;
		foreach( $rss->items as $item ) {
			if($maxheads > 0 && $headcnt >= $maxheads) continue;

			$decision = true;
			foreach(array('title', 'author', 'description', 'category') as $check) {
				if( isset( $item[$check] ) ) {
					$decision &= wfRssFilter($item[$check], $rssFilter) & wfRssFilterout($item[$check], $rssFilterout);
					if( !$decision ) continue 2;

					$item[$check] = wfRssHighlight( $item[$check], $rssHighlight );
				}

			}

			$rssTemp = "";

			foreach(explode("|", $template) as $bit) {
				$bits = explode("=", $bit);
				if( count($bits) == 2 ) {
					$left = trim($bits[0]);

					if( isset( $item[$left] ) ) {
						$right = $item[$left];
					}

					$rssTemp .= implode( " = ", array($left, $right) );
				} else {
					$rssTemp .= $bit;
				}
				$rssTemp .= "|";
			}
			$rssTemp .= "}}";

			$output .= $parser->recursiveTagParse( $rssTemp, $frame );
			$headcnt++;
		}
	}
	return $output;
}

function wfRssFilter( $text, $rssFilter ) {
	$display = true;
	if ( is_array( $rssFilter ) ) {
		foreach( $rssFilter as $term ) {
			if ( $term ) {
				$display = false;
				if ( preg_match( "|$term|i", $text, $a ) ) {
					$display = true;
					return $display;
				}
			}
			if ( $display ) {
				break;
			}
		}
	}
	return $display;
}

function wfRssFilterout( $text, $rssFilterout ) {
	$display = true;
	if ( is_array( $rssFilterout ) ) {
		foreach ( $rssFilterout as $term ) {
			if ( $term ) {
				if ( preg_match( "|$term|i", $text, $a ) ) {
					$display = false;
					return $display;
				}
			}
		}
	}
	return $display;
}

function wfRssHighlight( $text, $rssHighlight ) {
	$i = 0;
	$starttag = 'v8x5u3t3u8h';
	$endtag = 'q8n4f6n4n4x';

	$color[] = 'coral';
	$color[] = 'greenyellow';
	$color[] = 'lightskyblue';
	$color[] = 'gold';
	$color[] = 'violet';
	$count_color = count( $color );

	if ( is_array( $rssHighlight ) ) {
		foreach( $rssHighlight as $term ) {
			if ( $term ) {
				$text = preg_replace( "|\b(\w*?" . $term . "\w*?)\b|i", "$starttag" . "_" . $i . "\\1$endtag", $text );
				$i++;
				if ( $i == $count_color ) {
					$i = 0;
				}
			}
		}
	}

	# To avoid trouble should someone wants to highlight the terms "span", "style", …
	for ( $i = 0; $i < 5; $i++ ) {
		$text = preg_replace( "|$starttag" . "_" . $i . "|", "<span style=\"background-color:" . $color[$i] . "; font-weight: bold;\">", $text );
		$text = preg_replace( "|$endtag|", '</span>', $text );
	}

	return $text;
}
