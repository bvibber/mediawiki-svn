<?php
/**
 * UI Class for DataCenter extension
 *
 * @file
 * @ingroup Extensions
 */

class DataCenterWidgetDifference extends DataCenterWidget {

	/* Private Static Members */

	private static $defaultParameters = array(
		/**
		 * XML id attribute
		 * @datatype	string
		 */
		'id' => 'difference',
		/**
		 * XML class attribute
		 * @datatype	string
		 */
		'class' => 'widget-difference',
		/**
		 * Data Source: previous state of row
		 * @datatype	DataCenterDBRow
		 */
		'previous' => null,
		/**
		 * Data Source: current state of row
		 * @datatype	DataCenterDBRow
		 */
		'current' => null,
	);

	/* Functions */

	public static function render(
		array $parameters
	) {
		// Sets defaults
		$parameters = array_merge( self::$defaultParameters, $parameters );
		// Begins widget
		$xmlOutput = parent::begin( $parameters['class'] );
		// Begins table
		$xmlOutput .= DataCenterXml::open( 'table' );
		// Checks that an array of fields and valid rows were given
		if (
			is_array( $parameters['previous'] ) &&
			is_array( $parameters['current'] )
		) {
			// Loops over each field
			foreach ( $parameters['current'] as $field => $value ) {
				// Detects changed field
				$different = ( $value !== $parameters['previous'][$field] );
				// Adds row
				$xmlOutput .= DataCenterXml::row(
					DataCenterXml::cell(
						array( 'class' => 'label' ),
						DataCenterUI::message( 'field', $field )
					),
					DataCenterXml::cell(
						array( 'class' => 'previous' ),
						DataCenterXml::span(
							array(
								'class' => $different ? 'different' : 'same'
							),
							$parameters['previous'][$field]
						)
					),
					DataCenterXml::cell(
						array( 'class' => 'current' ),
						DataCenterXml::span(
							array(
								'class' => $different ? 'different' : 'same'
							),
							$value
						)
					)
				);
			}
		}
		// Ends table
		$xmlOutput .= DataCenterXml::close( 'table' );
		// Ends widget
		$xmlOutput .= parent::end();
		// Returns results
		return $xmlOutput;
	}
}