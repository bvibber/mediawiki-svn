<?php

/**
 * UI Class for DataCenter extension
 *
 * @file
 * @ingroup Extensions
 */

class DataCenterWidgetSearchResults extends DataCenterWidget {

	/* Private Static Members */

	private static $defaultParameters = array(
		/**
		 * XML ID attribute of widget
		 * @datatype	string
		 */
		'id' => 'searchresults',
		/**
		 * CSS class of widget
		 * @datatype	string
		 */
		'class' => 'widget-searchresults',
		/**
		 * Search terms
		 * @datatype	string
		 */
		'query' => null,
	);

	private static $targets = array(
		array(
			'category' => 'asset',
			'type' => 'rack',
			'fields' => array( 'serial', 'asset' )
		),
		array(
			'category' => 'asset',
			'type' => 'object',
			'fields' => array( 'serial', 'asset' )
		)
	);

	/* Static Functions */

	public static function render(
		array $parameters
	) {
		global $wgUser;
		// Gets current path
		$path = DataCenterPage::getPath();
		// Sets Defaults
		$parameters = array_merge( self::$defaultParameters, $parameters );
		// Begins widget
		$xmlOutput = parent::begin( $parameters['class'] );
		// Gets search results
		$results = DataCenterDB::getSearchResults(
			self::$targets, $parameters['query']
		);
		// Adds results
		$xmlOutput .= DataCenterXml::open( 'div', array( 'class' => 'results' ) );
		foreach ( $results as $result ) {
			$xmlOutput .= DataCenterXml::tag(
				'pre', array(), var_export( $result->get(), true )
			);
		}
		$xmlOutput .= DataCenterXml::close( 'div' );
		// Ends widget
		$xmlOutput .= parent::end();
		// Returns results
		return $xmlOutput;
	}
}