<?php

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "SiteMatrix extension\n";
    exit( 1 );
}


global $IP;
require_once( $IP.'/languages/Names.php' );

class SiteMatrix {
	public $langlist, $sites, $names, $hosts;
	public $specialRewrites, $hidden, $specials, $matrix, $count, $countPerSite;

	public function __construct(){
		global $wgLocalDatabases, $IP, $wgSiteMatrixFile, $wgConf;

		if( file_exists( "$IP/InitialiseSettings.php" ) ) {
			require_once "$IP/InitialiseSettings.php";
		}

		$this->langlist = array_map( 'trim', file( $wgSiteMatrixFile ) );
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

		# Special wikis that don't are at $lang.wikimedia.org
		$this->specialRewrites = array(
			'arbcom-en' => 'arbcom.en.wikipedia.org',
			'dk' => 'dk.wikipedia.org', # FIXME
			'foundation' => 'wikimediafoundation.org',
			'mediawiki' => 'www.mediawiki.org',
			'nostalgia' => 'nostalgia.wikipedia.org',
			'sources' => 'wikisource.org',
			'species' => 'species.wikipedia.org',
			'test' => 'test.wikipedia.org',
			'wg-en' => 'wg.en.wikipedia.org',
			'beta' => 'beta.wikiversity.org',
		);

		# Some internal databases for other domains.
		$this->hidden = array();

		# Initialize $countPerSite
		$this->countPerSite = array();
		foreach( $this->sites as $site ) {
			$this->countPerSite[$site] = 0;
		}

		# Tabulate the matrix
		$this->specials = array();
		$this->matrix = array();
		foreach( $wgLocalDatabases as $db ) {
			# Find suffix
			foreach ( $this->sites as $site ) {
				$m = array();
				if ( preg_match( "/(.*)$site\$/", $db, $m ) ) {
					$lang =  str_replace( '_', '-', $m[1] );
					if ( !isset( $xLanglist[$lang] ) && ($site == 'wiki' || $site == 'wikiversity') ) {
						$this->specials[] = $lang;
					} else {
						$this->matrix[$site][$lang] = 1;
						$this->countPerSite[$site]++;
					}
					break;
				}
			}
		}
		$this->count = count( $wgLocalDatabases );
	}

	public function isPrivate( $dbname ) {
		global $wmgPrivateWikis;
		return $wmgPrivateWikis ? in_array( "{$dbname}wiki", $wmgPrivateWikis ) : false;
	}

	public function isFishbowl( $dbname ) {
		global $wmgFishbowlWikis;
		return $wmgFishbowlWikis ? in_array( "{$dbname}wiki", $wmgFishbowlWikis ) : false;
	}

	public function isClosed( $dbname ) {
		global $wgConf;
		if( !$wgConf ) return false;
		list( $major, $minor ) = $wgConf->siteFromDB( $dbname );
		if( $wgConf->get( 'wgReadOnly', $dbname, $major, array( 'site' => $major, 'lang' => $minor ) ) )
			return true;
		if( $wgConf->get( 'wgReadOnlyFile', $dbname, $major, array( 'site' => $major, 'lang' => $minor ) ) )
			return true;
		return false;
	}

	public function sitenameById( $name ) {
		return $name == 'wiki' ? 'wikipedia' : $name;
	}
}

class SiteMatrixPage extends SpecialPage {

	function SiteMatrixPage() {
		SpecialPage::SpecialPage('SiteMatrix');
	}

	function execute( $par ) {
		global $wgOut, $wgRequest, $wgLanguageNames;
		wfLoadExtensionMessages( 'SiteMatrix' );
		
		$this->setHeaders();
		$this->outputHeader();

		$matrix = new SiteMatrix();

		if ($wgRequest->getVal( 'action' ) == "raw")
		{
			$wgOut->disable();
			header("Content-Type: text/xml; charset=utf-8");
			echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
			echo "<sitematrix>\n";
			echo "\t<matrix size=\"{$matrix->count}\">\n";
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

				$langhost = str_replace( '_', '-', $lang );
				if( isset( $matrix->specialRewrites[$lang] ) ){
					$domain = $matrix->specialRewrites[$lang];
				} else {
					$domain = $langhost . ".wikimedia.org";
				}
				$url = "http://{$domain}/";

				echo "\t\t<special code=\"{$langhost}\" url=\"{$url}\" />\n";
			}
			echo "\t</specials>\n";
			echo "</sitematrix>";
			return;
		}

		# Construct the HTML

		# Header row
		$s = Xml::openElement( 'table', array( 'id' => 'mw-sitematrix-table' ) ) .
			"<tr>" .
				Xml::element( 'th', null, wfMsg( 'sitematrix-language' ) ) .
				Xml::element( 'th', array( 'colspan' => count( $matrix->sites ) ), wfMsg( 'sitematrix-project' ) ) .
			"</tr>
			<tr>
				<th>&nbsp;</th>";
				foreach ( $matrix->names as $id => $name ) {
					$s .= Xml::tags( 'th', null, '<a href="http://www.' . $matrix->sitenameById($id) . '.org/">' .  $name . '</a>' );
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
					$closed = $matrix->isClosed( "{$lang}{$site}" );
					$s .= "<td>" . ($closed ? "<s>" : '') . "<a href=\"" . $url . '">' . $lang . '</a>' . ($closed ? "</s>" : '') . '</td>';
				}
			}
			$s .= "</tr>\n";
		}

		# Total
		$s .= '<tr style="font-weight: bold"><td><a id="total" name="total"></a>' . wfMsgHtml( 'sitematrix-sitetotal' ) . '</td>';
		foreach( $matrix->names as $site => $name ) {
			$url = "http://{$matrix->hosts[$site]}/";
			$s .= "<td><a href=\"{$url}\">{$matrix->countPerSite[$site]}</a></td>";
		}
		$s .= '</tr>';
		$s .= Xml::closeElement( 'table' ) . "\n";

		# Specials
		$s .= '<h2 id="mw-sitematrix-others">' . wfMsg( 'sitematrix-others' ) . '</h2>';
		$s .= '<ul>';
		foreach ( $matrix->specials as $lang ) {

			# Skip "hidden" databases:
			if( in_array($lang, $matrix->hidden) ) {
				continue;
			}

			$langhost = str_replace( '_', '-', $lang );

			# Handle special wikimedia projects:
			if( isset( $matrix->specialRewrites[$lang] ) ){
				$domain = $matrix->specialRewrites[$lang];
			} else {
				$domain = $langhost . ".wikimedia.org";
			}

			# Handle options
			$flags = array();
			if( $matrix->isPrivate( $lang ) )
				$flags[] = wfMsgHtml( 'sitematrix-private' );
			if( $matrix->isFishbowl( $lang ) )
				$flags[] = wfMsgHtml( 'sitematrix-fishbowl' );
			$flagsStr = implode( ', ', $flags );
			if( $lang == 'beta' ) $lang = 'betawikiversity';	//ugly hack for betawikiversity
			$s .= '<li>' . wfSpecialList( '<a href="http://' . $domain . '/">' . $lang . "</a>", $flagsStr ) . "</li>\n";
		}
		$s .= '</ul>';
		$wgOut->addHTML( $s );
		$wgOut->addHTML( wfMsgWikiHtml( 'sitematrix-total', $matrix->count ) );
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

		$matrix_out = array(
			'count' => $matrix->count,
		);
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
			if( isset( $matrix->specialRewrites[$lang] ) ){
				$domain = $matrix->specialRewrites[$lang];
			} else {
				$domain = $langhost . ".wikimedia.org";
			}
			$url = "http://{$domain}/";

			$wiki = array();
			$wiki['url'] = $url;
			$wiki['code'] = $langhost;
			$specials[] = $wiki;
		}

		$result->setIndexedTagName($specials, 'special');
		$result->addValue("sitematrix", "specials", $specials);
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
