<?php

/**
 * Hooks for ArticleAssessmentPilot
 *
 * @file
 * @ingroup Extensions
 */
class ArticleAssessmentPilotHooks {
	private static $styleFiles = array(
		array( 'src' => 'css/ArticleAssessment.css', 'version' => 1 ),
	);

	private static $scriptFiles = array(
		array( 'src' => 'js/ArticleAssessment.js', 'version' => 1 ),
		array( 'src' => 'js/jquery.cookie.js', 'version' => 1 ),
		array( 'src' => 'js/jquery.tipsy.js', 'version' => 1 ),
	);

	/* Static Functions */
	public static function schema() {
		global $wgExtNewTables;

		$wgExtNewTables[] = array(
			'article_assessment',
			dirname( __FILE__ ) . '/ArticleAssessmentPilot.sql'
		);

		return true;
	}

	/**
	 * Make sure the table exists for parser tests
	 * @param $tables
	 * @return bool
	 */
	public static function parserTestTables( &$tables ) {
		$tables[] = 'article_assessment';
		$tables[] = 'article_assessment_pages';
		$tables[] = 'article_assessment_ratings';
		return true;
	}

	public static function addResources( $out ) {
		global $wgExtensionAssetsPath;

		foreach ( self::$scriptFiles as $script ) {
			$out->addScriptFile( $wgExtensionAssetsPath .
				"/ArticleAssessmentPilot/{$script['src']}", $script['version']
			);
		}

		foreach(self::$styleFiles as $style) {
			$out->addExtensionStyle( $wgExtensionAssetsPath .
				"/ArticleAssessmentPilot/{$style['src']}?{$style['version']}"
			);
		}

		return true;
	}

	public static function addCode( &$data, $skin ) {
		$title = $skin->getTitle();

		// check if this page should have the form

		// Chances are we only want to be rating Mainspace, right?
		if ( $title->getNamespace() !== NS_MAIN ) {
			return true;
		}

		// write the form

		// if user has no cookie, set cookie

		return true;
	}
}