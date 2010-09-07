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
		array( 'src' => 'js/jquery.stars.js', 'version' => 1 ),
	);

	private static $messages = array();
	private static $scripts = array();

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
	 * Make sure the tables exist for parser tests
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
		$title = $out->getTitle();

		// Chances are we only want to be rating Mainspace, right?
		if ( $title->getNamespace() !== NS_MAIN ) {
			return true;
		}

		global $wgArticleAssessmentCategory;

		// check if this page should have the form
		if ( $wgArticleAssessmentCategory === ''
				|| !self::isInCategory( $title->getArticleId(), $wgArticleAssessmentCategory ) ) {
			return true;
		}

		global $wgExtensionAssetsPath;

		foreach ( self::$scriptFiles as $script ) {
			$out->addScriptFile( $wgExtensionAssetsPath .
				"/ArticleAssessmentPilot/{$script['src']}", $script['version']
			);
		}

		foreach ( self::$styleFiles as $style ) {
			$out->addExtensionStyle( $wgExtensionAssetsPath .
				"/ArticleAssessmentPilot/{$style['src']}?{$style['version']}"
			);
		}

		// Transforms messages into javascript object members
		self::$messages = array(
			'articleassessment',
			'articleassessment-desc',
			'articleassessment-yourfeedback',
			'articleassessment-pleaserate',
			'articleassessment-submit',
			'articleassessment-rating-wellsourced',
			'articleassessment-rating-neutrality',
			'articleassessment-rating-completeness',
			'articleassessment-rating-readability',
			'articleassessment-rating-wellsourced-tooltip',
			'articleassessment-rating-neutrality-tooltip',
			'articleassessment-rating-completeness-tooltip',
			'articleassessment-rating-readability-tooltip',
			'articleassessment-error',
			'articleassessment-thanks',
			'articleassessment-articlerating',
			'articleassessment-featurefeedback',
			'articleassessment-noratings',
			'articleassessment-stalemessage-revisioncount',
			'articleassessment-stalemessage-norevisioncount',
			'articleassessment-results-show',
			'articleassessment-results-hide',
			);

		foreach ( self::$messages as $i => $message ) {
			$escapedMessageValue = Xml::escapeJsString( wfMsg( $message ) );
			$escapedMessageKey = Xml::escapeJsString( $message );
			self::$messages[$i] =
				"'{$escapedMessageKey}':'{$escapedMessageValue}'";
		}
		// Add javascript to document
		if ( count( self::$messages ) > 0 ) {
			$out->addScript( Html::inlineScript(
				'$j.ArticleAssessment.fn.addMessages({' . implode( ',', self::$messages ) . '});'
			) );
		}

		return true;
	}

	/**
	 * Returns whether an article is in the specified category
	 *
	 * @param $articleId Integer: Article ID
	 * @param $category String: The category name (without Category: prefix, with underscores)
	 *
	 * @return bool
	 */
	private static function isInCategory( $articleId, $category ) {
		$dbr = wfGetDB( DB_SLAVE );
		return (bool)$dbr->selectRow( 'categorylinks', '1',
			array(
				'cl_from' => $articleId,
				'cl_to' => $category,
			),
			__METHOD__
		);
	}

	/**
	 * Adds a reference to a javascript file to the head of the document
	 * @param string $src Path to the file relative to this extension's folder
	 * @param object $version Version number of the file
	 */
	public static function addScript( $src, $version = '' ) {
		// The key is Andrew's snarky 20-character way of stopping multiple inclusion of the same file.
		self::$scripts["$src?$version"] = array( 'src' => $src, 'version' => $version );
	}

	/**
	 * Adds internationalized message definitions to the document for access
	 * via javascript using the mw.usability.getMsg() function
	 * @param array $messages Key names of messages to load
	 */
	public static function addMessages( $messages ) {
		self::$messages = array_merge( self::$messages, $messages );
	}
}
