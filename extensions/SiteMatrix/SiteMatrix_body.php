<?php

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "SiteMatrix extension\n";
    exit( 1 );
}

# Add messages
global $wgMessageCache, $wgSiteMatrixMessages;
foreach( $wgSiteMatrixMessages as $key => $value ) {
	$wgMessageCache->addMessages( $wgSiteMatrixMessages[$key], $key );
}

global $IP;
require_once( $IP.'/languages/Names.php' );

class SiteMatrixPage extends SpecialPage {

	function SiteMatrixPage() {
		SpecialPage::SpecialPage('SiteMatrix');
	}

	function execute( $par ) {
		global $wgOut, $wgLocalDatabases;
		$this->setHeaders();

		$langlist = array_map( 'trim', file( '/home/wikipedia/common/langlist' ) );
		sort( $langlist );
		$xLanglist = array_flip( $langlist );

		$sites = array(
			'wiki',
			'wiktionary',
			'wikibooks',
			'wikinews',
			'wikisource',
			'wikiquote',
			'wikiversity',
		);
		$names = array( 
			'wiki' => 'Wikipedia<br />w',
			'wiktionary' => 'Wiktionary<br />wikt',
			'wikibooks' => 'Wikibooks<br />b',
			'wikinews' => 'Wikinews<br />n',
			'wikiquote' => 'Wikiquote<br />q',
			'wikisource' => 'Wikisource<br />s',
			'wikiversity' => 'Wikiversity<br />v',
		);
		$hosts = array(
			'wiki' => 'wikipedia.org',
			'wiktionary' => 'wiktionary.org',
			'wikibooks' => 'wikibooks.org',
			'wikinews' => 'wikinews.org',
			'wikisource' => 'wikisource.org',
			'wikiquote' => 'wikiquote.org',
			'wikiversity' => 'wikiversity.org',
		);

		# Special wikis that should point to wikiPedia, not wikiMedia
		$wikipediaSpecial = array(
			'dk', 'sources', 'species', 'test',
		);

		# Some internal databases for other domains.
		$hidden = array(
			'foundation', 'mediawiki',
		);

		# Tabulate the matrix
		$specials = array();
		$matrix = array();
		foreach( $wgLocalDatabases as $db ) {
			# Find suffix
			foreach ( $sites as $site ) {
				$m = array();
				if ( preg_match( "/(.*)$site\$/", $db, $m ) ) {
					$lang =  str_replace( '_', '-', $m[1] );
					if ( !isset( $xLanglist[$lang] ) && $site == 'wiki' ) {
						$specials[] = $lang;
					} else {
						$matrix[$site][$lang] = 1;
					}
					break;
				}
			}
		}

		# Construct the HTML

		# Header row
		$s = '<table>
			<tr>
				<th>' . wfMsg( 'sitematrix-language' ) . '</th>
				<th colspan="' . count( $sites ) . '">' . wfMsg( 'sitematrix-project' ) . '</th>
			</tr><tr>
				<th>&nbsp;</th>';
				foreach ( $names as $name ) {
					$s .= '<th>' . $name . '</th>';
				}
		$s .= "</tr>\n";

		global $wgLanguageNames;
		# Bulk of table
		foreach ( $langlist as $lang ) {
			$anchor = strtolower( '<a id="' . htmlspecialchars( $lang ) . '" name="' . htmlspecialchars( $lang ) . '"></a>' );
			$s .= '<tr>';
			$s .= '<td>' . $anchor . '<strong>' . $wgLanguageNames[$lang] . '</strong></td>';
			$langhost = str_replace( '_', '-', $lang );
			foreach ( $names as $site => $name ) {
				$url = "http://$langhost." . $hosts[$site] . '/';
				if ( empty( $matrix[$site][$lang] ) ) {
					# Non-existent wiki
					$s .= '<td><a href="' . $url . '" class="new">' . $lang . '</a></td>';
				} else {
					# Wiki exists
					$s .= '<td><a href="' . $url . '">' . $lang . '</a></td>';
				}
			}
			$s .= "</tr>\n";
		}
		$s .= "</table>\n";

		# Specials
		$s .= '<h2>' . wfMsg( 'sitematrix-others' ) . '</h2>';
		$s .= '<ul>';
		foreach ( $specials as $lang ) {

			# Skip "hidden" databases:
			if( in_array($lang, $hidden) ) {
				continue;
			}

			$langhost = str_replace( '_', '-', $lang );

			# Handle special wikipedia projects:
			if( in_array($lang, $wikipediaSpecial) ) {
				$domain = '.wikipedia.org';
			} else{
				$domain = '.wikimedia.org';
			}
			$s .= '<li><a href="http://' . $langhost . $domain . '/">' . $lang . "</a></li>\n";
		}
		$s .= '</ul>';
		$wgOut->addHTML( $s );
	}
}
