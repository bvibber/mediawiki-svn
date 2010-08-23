<?php

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "SiteMatrix extension\n";
    exit( 1 );
}

/**
 * Query module to get site matrix
 * @ingroup API
 */
class ApiQuerySiteMatrix extends ApiQueryBase {

	public function __construct($query, $moduleName) {
		parent :: __construct($query, $moduleName, 'sm');
	}

	public function execute() {
		global $wgLanguageNames;
		$result = $this->getResult();
		$matrix = new SiteMatrix();

		$matrix_out = array(
			'count' => $matrix->getCount(),
		);

		if( class_exists( 'LanguageNames' ) ) {
			global $wgLang;
			$localLanguageNames = LanguageNames::getNames( $wgLang->getCode() );
		} else {
			$localLanguageNames = array();
		}

		foreach ( $matrix->getLangList() as $lang ) {
			$langhost = str_replace( '_', '-', $lang );
			$language = array(
				'code' => $langhost,
				'name' => $wgLanguageNames[$lang],
				'site' => array(),
			);
			if( isset( $localLanguageNames[$lang] ) ) {
				$language['localname'] = $localLanguageNames[$lang];
			}

			foreach ( $matrix->getSites() as $site ) {
				if ( $matrix->exist( $lang, $site ) ) {
					$url = $matrix->getUrl( $lang, $site );
					$site_out = array(
						'url' => $url,
						'code' => $site,
					);
					if( $matrix->isClosed( $lang, $site ) )
						$site_out['closed'] = '';
					$language['site'][] = $site_out;
				}
			}

			$result->setIndexedTagName($language['site'], 'site');
			$matrix_out[] = $language;
		}
		$result->setIndexedTagName($matrix_out, 'language');
		$result->addValue(null, "sitematrix", $matrix_out);

		$specials = array();
		foreach ( $matrix->getSpecials() as $special ){
			list( $lang, $site ) = $special;
			$url = $matrix->getUrl( $lang, $site );

			$wiki = array();
			$wiki['url'] = $url;
			$wiki['code'] = str_replace( '_', '-', $lang ) . ( $site != 'wiki' ? $site : '' );

			if( $matrix->isPrivate( $lang . $site ) )
				$wiki['private'] = '';
			if( $matrix->isFishbowl( $lang . $site ) )
				$wiki['fishbowl'] = '';
			if( $matrix->isClosed( $lang, $site ) )
				$wiki['closed'] = '';

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
