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
		'Jeroen De Dauw'
	),
	'version' => '1.7',
	'url' => 'http://www.mediawiki.org/wiki/Extension:RSS',
	'description' => 'Displays an RSS feed on a wiki page',
	'descriptionmsg' => 'rss-desc',
);

// Internationalization file and autoloadable classes
$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['RSS'] = $dir . 'RSS.i18n.php';
$wgAutoloadClasses['MagpieRSS'] = $dir . 'RSSParse.php';
$wgAutoloadClasses['RSSCache'] = $dir . 'RSSCache.php';

$wgHooks['ParserFirstCallInit'][] = 'wfRssExtension';

# Extension hook callback function
function wfRssExtension( &$parser ) {
	# Install parser hook for <rss> tags
	$parser->setHook( 'rss', 'renderRss' );
	return true;
}

# Parser hook callback function
function renderRss( $input ) {
	global $wgOutputEncoding, $wgParser;

	// Kill parser cache
	$wgParser->disableCache();

	if ( !$input ) {
		return ''; # if <rss>-section is empty, return nothing
	}

	# Parse fields in rss section
	$fields = explode( '|', $input );
	wfSuppressWarnings();
	$url = $fields[0];
	wfRestoreWarnings();

	$args = array();
	for ( $i = 1; $i < sizeof( $fields ); $i++ ) {
		$f = $fields[$i];

		if ( strpos( $f, '=' ) === false ) {
			$args[strtolower( trim( $f ) )] = false;
		} else {
			list( $k, $v ) = explode( '=', $f, 2 );
			if ( trim( $v ) == false ) {
				$args[strtolower( trim( $k ) )] = false;
			} else {
				$args[strtolower( trim( $k ) )] = trim( $v );
			}
		}
	}

	# Get charset from argument array
	wfSuppressWarnings();
	$charset = $args['charset'];
	wfRestoreWarnings();
	if( !$charset ) {
		$charset = $wgOutputEncoding;
	}

	# Get max number of headlines from argument-array
	wfSuppressWarnings();
	$maxheads = $args['max'];
	wfRestoreWarnings();
	$headcnt = 0;

	# Get short flag from argument array
	# If short is set, no description text is printed
	if( isset( $args['short'] ) ) {
		$short = true;
	} else {
		$short = false;
	}

	# Get reverse flag from argument array
	if( isset( $args['reverse'] ) ) {
		$reverse = true;
	} else {
		$reverse = false;
	}

	# Get date format from argument array
	if ( isset( $args['date'] ) ) {
		wfSuppressWarnings();
		$date = $args['date'];
		wfRestoreWarnings();
		if ( $date == '' ) {
			$date = 'd M Y H:i';
		}
	} else {
		$date = false;
	}

	# Get highlight terms from argument array
	wfSuppressWarnings();
	$rssHighlight = $args['highlight'];
	wfRestoreWarnings();
	$rssHighlight = str_replace( '  ', ' ', $rssHighlight );
	$rssHighlight = explode( ' ', trim( $rssHighlight ) );

	# Get filter terms from argument array
	wfSuppressWarnings();
	$rssFilter = $args['filter'];
	wfRestoreWarnings();
	$rssFilter = str_replace( '  ', ' ', $rssFilter );
	$rssFilter = explode( ' ', trim( $rssFilter ) );

	# Filterout terms
	wfSuppressWarnings();
	$rssFilterout = $args['filterout'];
	wfRestoreWarnings();
	$rssFilterout = str_replace( '  ', ' ', $rssFilterout );
	$rssFilterout = explode( ' ', trim( $rssFilterout ) );

	# Fetch RSS. May be cached locally.
	# Refer to the documentation of MagpieRSS for details.
	if ( !function_exists( 'fetch_rss' ) ) {
		include( dirname( __FILE__ ) . '/RSSFetch.php' ); // provides fetch_rss() function
	}
	wfSuppressWarnings();
	$rss = fetch_rss( $url );
	wfRestoreWarnings();

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

	# Build title line
	#$title = iconv( $charset, $wgOutputEncoding, $rss->channel['title'] );
	#if( $rss->channel['link'] ) $title = "<a href='" . $rss->channel['link'] . "'>$title</a>";

	$output = '';
	if( $reverse ) {
		$rss->items = array_reverse( $rss->items );
	}
	$description = false;
	foreach ( $rss->items as $item ) {
		if ( isset( $item['description'] ) && $item['description'] ) {
			$description = true;
			break;
		}
	}

	# Build items
	if ( !$short && $description ) { # full item list
		$output .= '<dl>';

		foreach ( $rss->items as $item ) {
			$d_text = true;
			$d_title = true;

			$href = htmlspecialchars( trim( iconv( $charset, $wgOutputEncoding, $item['link'] ) ) );
			$title = htmlspecialchars( trim( iconv( $charset, $wgOutputEncoding, $item['title'] ) ) );

			if ( $date ) {
				$pubdate = trim( iconv( $charset, $wgOutputEncoding, $item['pubdate'] ) );
				$pubdate = date( $date, strtotime( $pubdate ) );
			}

			$d_title = wfRssFilter( $title, $rssFilter );
			$d_title = wfRssFilterout( $title, $rssFilterout );
			$title = wfRssHighlight( $title, $rssHighlight );

			# Build description text if desired
			if ( $item['description'] ) {
				$text = trim( iconv( $charset, $wgOutputEncoding, $item['description'] ) );
				# Avoid <pre> tags
				$text = str_replace( "\r", ' ', $text );
				$text = str_replace( "\n", ' ', $text );
				$text = str_replace( "\t", ' ', $text );
				$text = str_replace( '<br>', '', $text );

				$d_text = wfRssFilter( $text, $rssFilter );
				$d_text = wfRssFilterout( $text, $rssFilterout );
				$text = wfRssHighlight( $text, $rssHighlight );
				$display = $d_text || $d_title;
 			} else {
				$text = '';
				$display = $d_title;
			}
			if ( $display ) {
				$output.= "<dt><a href='$href'><b>$title</b></a></dt>";
				if ( $date ) {
					$output .= " ($pubdate)";
				}
				if ( $text ) {
					$output .= "<dd>$text <b>[<a href='$href'>?</a>]</b></dd>";
				}
			}
			# Cut off output when maxheads is reached:
			if ( ++$headcnt == $maxheads ) {
				break;
			}
		}

		$output .= '</dl>';
	} else { # short item list
		## HACKY HACKY HACKY
		$output .= '<ul>';
		$displayed = array();
		foreach ( $rss->items as $item ) {
			$href = htmlspecialchars( trim( iconv( $charset, $wgOutputEncoding, $item['link'] ) ) );
			$title = htmlspecialchars( trim( iconv( $charset, $wgOutputEncoding, $item['title'] ) ) );
			$d_title = wfRssFilter( $title, $rssFilter ) && wfRssFilterout( $title, $rssFilterout );
			$title = wfRssHighlight( $title, $rssHighlight );
			if ( $date ) {
				$pubdate = isset( $item['pubdate'] ) ? trim( iconv( $charset, $wgOutputEncoding, $item['pubdate'] ) ) : '';
				if ( $pubdate == '' ) {
					$pubdate = trim( iconv( $charset, $wgOutputEncoding, $item['dc']['date'] ) );
				}
				$pubdate = date( $date, strtotime( $pubdate ) );
			}

			if ( $d_title && !in_array( $title, $displayed ) ) {
				// Add date to ouput if specified
				$output .= '<li><a href="' . $href . '" title="' . $title . '">' . $title . '</a>';
				if( $date ) {
					$output .= " ($pubdate)";
				}
				$output .= '</li>';

				$displayed[] = $title;
				# Cut off output when maxheads is reached:
				if ( ++$headcnt == $maxheads ) {
					break;
				}
			}
		}
		$output.= '</ul>';
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