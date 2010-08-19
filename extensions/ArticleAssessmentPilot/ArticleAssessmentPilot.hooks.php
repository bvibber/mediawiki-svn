<?php

/**
 * Hooks for ArticleAssessmentPilot
 *
 * @file
 * @ingroup Extensions
 */

class ArticleAssessmentPilotHooks {

	/* Static Functions */
	public static function schema() {
		global $wgExtNewTables;

		$wgExtNewTables[] = array(
			'article_assessment',
			dirname( __FILE__ ) . '/ArticleAssessmentPilot.sql'
		);
		
		return true;
	} //schema
	
	/**
	 * Make sure the table exists for parser tests
	 * @param $tables
	 * @return unknown_type
	 */
	public static function parserTestTables( &$tables ) {
		$tables[] = 'article_assessment';
		$tables[] = 'article_assessment_pages';
		return true;
	}
	
	public static function addCode(){
		//check if this page should have the form
		
		//write the form
		
		//if user has no cookie, set cookie
		
		return true;
	}

}