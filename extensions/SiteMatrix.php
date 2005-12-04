<?php

# Make an HTML table showing all the wikis on the site

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (defined('MEDIAWIKI')) {
	
$wgExtensionFunctions[] = 'wfSiteMatrix';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'SiteMatrix',
	'description' => 'display a list of wikimedia wikis'
);

function wfSiteMatrix() {
global $IP;
require_once( $IP.'/includes/SpecialPage.php' );
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

		$sites = array( 'wiki', 'wiktionary', 'wikibooks', 'wikinews', 'wikisource', 'wikiquote' );
		$names = array( 
			'wiki' => 'Wikipedia<br />w',
			'wiktionary' => 'Wiktionary<br />wikt',
			'wikibooks' => 'Wikibooks<br />b',
			'wikinews' => 'Wikinews<br />n',
			'wikiquote' => 'Wikiquote<br />q',
			'wikisource' => 'Wikisource<br />s',
		);
		$hosts = array(
			'wiki' => 'wikipedia.org',
			'wiktionary' => 'wiktionary.org',
			'wikibooks' => 'wikibooks.org',
			'wikinews' => 'wikinews.org',
			'wikisource' => 'wikisource.org',
			'wikiquote' => 'wikiquote.org',
		);
		
		# Tabulate the matrix
		$specials = array();
		$matrix = array();
		foreach( $wgLocalDatabases as $db ) {
			# Find suffix
			foreach ( $sites as $site ) {
				$m = array();
				if ( preg_match( "/(.*)$site\$/", $db, $m ) ) {
					$lang =  $m[1];
					if ( empty( $xLanglist[$lang] ) && $site == 'wiki' ) {
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
		$s = '<table><tr>';
		$s .= '<th>Language</th>';
		foreach ( $names as $name ) {
			$s .= '<th>' . $name . '</th>';
		}
		$s .= "</tr>\n";

		global $wgLanguageNames;
		# Bulk of table
		foreach ( $langlist as $lang ) {
			$s .= '<tr>';
			$s .= '<td><strong>' . $wgLanguageNames[$lang] . '</strong></td>';
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
		$s .= '<ul>';
		foreach ( $specials as $lang ) {
			$langhost = str_replace( '_', '-', $lang );
			$s .= '<li><a href="http://' . $langhost . '.wikipedia.org/">' . $lang . "</a></li>\n";
		}
		$s .= '</ul>';
		$wgOut->addHTML( $s );
	}
}

SpecialPage::addPage( new SiteMatrixPage );
global $wgMessageCache;
$wgMessageCache->addMessage( 'sitematrix', 'List of Wikimedia wikis' );

} # End of extension function
} # End of invocation protection
?>
