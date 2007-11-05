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

class SiteMatrix
{
	public $langlist, $sites, $names, $hosts;
	public $wikipediaSpecial, $hidden, $specials, $matrix;
	
	public function __construct()
	{
		global $wgLocalDatabases, $IP;
	
		$this->langlist = array_map( 'trim', file( '/home/wikipedia/common/langlist' ) );
		sort( $this->langlist );
		$xLanglist = array_flip( $this->langlist );
		
		$this->sites = array(
			'wiki',
			'wiktionary',
			'wikibooks',
			'wikinews',
			'wikisource',
			'wikiquote',
			'wikiversity',
		);
		$this->names = array( 
			'wiki' => 'Wikipedia<br />w',
			'wiktionary' => 'Wiktionary<br />wikt',
			'wikibooks' => 'Wikibooks<br />b',
			'wikinews' => 'Wikinews<br />n',
			'wikiquote' => 'Wikiquote<br />q',
			'wikisource' => 'Wikisource<br />s',
			'wikiversity' => 'Wikiversity<br />v',
		);
		$this->hosts = array(
			'wiki' => 'wikipedia.org',
			'wiktionary' => 'wiktionary.org',
			'wikibooks' => 'wikibooks.org',
			'wikinews' => 'wikinews.org',
			'wikisource' => 'wikisource.org',
			'wikiquote' => 'wikiquote.org',
			'wikiversity' => 'wikiversity.org',
		);

		# Special wikis that should point to wikiPedia, not wikiMedia
		$this->wikipediaSpecial = array(
			'dk', 'sources', 'species', 'test',
		);

		# Some internal databases for other domains.
		$this->hidden = array(
			'foundation', 'mediawiki',
		);
		
		# Tabulate the matrix
		$this->specials = array();
		$this->matrix = array();
		foreach( $wgLocalDatabases as $db ) {
			# Find suffix
			foreach ( $this->sites as $site ) {
				$m = array();
				if ( preg_match( "/(.*)$site\$/", $db, $m ) ) {
					$lang =  str_replace( '_', '-', $m[1] );
					if ( !isset( $xLanglist[$lang] ) && $site == 'wiki' ) {
						$this->specials[] = $lang;
					} else {
						$this->matrix[$site][$lang] = 1;
					}
					break;
				}
			}
		}
	}
}

class SiteMatrixPage extends SpecialPage {

	function SiteMatrixPage() {
		SpecialPage::SpecialPage('SiteMatrix');
	}

	function execute( $par ) {
		global $wgOut, $wgRequest, $wgLanguageNames;
		$this->setHeaders();
		
		$matrix = new SiteMatrix();

		if ($wgRequest->getVal( 'action' ) == "raw")
		{
			$wgOut->disable();
			header("Content-Type: text/xml; charset=utf-8");
			echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
			echo "<sitematrix>\n";
			echo "\t<matrix>\n";
			foreach ( $matrix->langlist as $lang ) {
				$langhost = str_replace("_", "-", $lang);
				echo "\t\t<language code=\"{$langhost}\" name=\"".htmlspecialchars($wgLanguageNames[$lang])."\">\n";
				foreach ( $matrix->sites as $site ) {
					if ( isset($matrix->matrix[$site][$lang]) ) {
						$url = "http://{$langhost}.{$matrix->hosts[$site]}/";
						echo "\t\t\t<site code=\"{$site}\" url=\"{$url}\" />\n";
					}
				}
				echo "\t\t</language>\n";
			}
			echo "\t</matrix>\n";
			echo "\t<specials>\n";
			foreach ( $matrix->specials as $lang ) {
				if ( in_array($lang, $matrix->hidden) ) {
					continue;
				}
				
				$langhost = str_replace("_", "-", $lang);
				$domain = in_array($lang, $matrix->wikipediaSpecial) ? ".wikipedia.org" : ".wikimedia.org";
				$url = "http://{$langhost}{$domain}/";
				
				echo "\t\t<special code=\"{$langhost}\" url=\"{$url}\" />\n";
			}
			echo "\t</specials>\n";
			echo "</sitematrix>";
			return;
		}

		# Construct the HTML

		# Header row
		$s = '<table>
			<tr>
				<th>' . wfMsg( 'sitematrix-language' ) . '</th>
				<th colspan="' . count( $matrix->sites ) . '">' . wfMsg( 'sitematrix-project' ) . '</th>
			</tr><tr>
				<th>&nbsp;</th>';
				foreach ( $matrix->names as $name ) {
					$s .= '<th>' . $name . '</th>';
				}
		$s .= "</tr>\n";

		# Bulk of table
		foreach ( $matrix->langlist as $lang ) {
			$anchor = strtolower( '<a id="' . htmlspecialchars( $lang ) . '" name="' . htmlspecialchars( $lang ) . '"></a>' );
			$s .= '<tr>';
			$s .= '<td>' . $anchor . '<strong>' . $wgLanguageNames[$lang] . '</strong></td>';
			$langhost = str_replace( '_', '-', $lang );
			foreach ( $matrix->names as $site => $name ) {
				$url = "http://$langhost." . $matrix->hosts[$site] . '/';
				if ( empty( $matrix->matrix[$site][$lang] ) ) {
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
		foreach ( $matrix->specials as $lang ) {

			# Skip "hidden" databases:
			if( in_array($lang, $matrix->hidden) ) {
				continue;
			}

			$langhost = str_replace( '_', '-', $lang );

			# Handle special wikipedia projects:
			if( in_array($lang, $matrix->wikipediaSpecial) ) {
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

/**
 * Query module to get site matrix
 * @addtogroup API
 */
class ApiQuerySiteMatrix extends ApiQueryBase {

	public function __construct($query, $moduleName) {
		parent :: __construct($query, $moduleName, 'sm');
	}

	public function execute() {
		global $wgLanguageNames;
		$params = $this->extractRequestParams();
		$result = $this->getResult();
		$matrix = new SiteMatrix();

		$matrix_out = array();
		foreach ( $matrix->langlist as $lang ) {
			$langhost = str_replace("_", "-", $lang);
			$language = array(
				'code' => $langhost,
				'name' => $wgLanguageNames[$lang],
				'site' => array(),
			);

			foreach ( $matrix->sites as $site ) {
				if (isset($matrix->matrix[$site][$lang])) {
					$url = "http://{$langhost}.{$matrix->hosts[$site]}/";
					$site_out = array(
						'url' => $url,
						'code' => $site,
					);
					$language['site'][] = $site_out;
				}
			}

			$result->setIndexedTagName($language['site'], 'site');
			$matrix_out[] = $language;
		}
		$result->setIndexedTagName($matrix_out, 'language');
		$result->addValue(null, "sitematrix", $matrix_out);

		$specials = array();
		foreach ( $matrix->specials as $lang )
		{
			if ( in_array($lang, $matrix->hidden) ) continue;

			$langhost = str_replace( '_', '-', $lang );
			$domain = in_array($lang, $matrix->wikipediaSpecial) ? ".wikipedia.org" : ".wikimedia.org";
			$url = "http://{$langhost}{$domain}/";

			$wiki = array();
			$wiki['url'] = $url;
			$wiki['code'] = $langhost;
			$specials[] = $wiki;
		}

		$result->setIndexedTagName($specials, 'special');
		$result->addValue(null, "specials", $specials);
	}

	protected function getAllowedParams() {
		return array (
		);
	}

	protected function getParamDescription() {
		return array (
		);
	}

	protected function getDescription() {
		return 'Get Wikimedia sites list';
	}

	protected function getExamples() {
		return array (
			'api.php?action=sitematrix',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}
