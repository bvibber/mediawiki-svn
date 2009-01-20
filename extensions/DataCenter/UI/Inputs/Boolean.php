<?php
/**
 * UI Class for DataCenter extension
 *
 * @file
 * @ingroup Extensions
 */

class DataCenterInputBoolean extends DataCenterInput {

	/* Private Static Members */

	private static $defaultParameters = array(
		/**
		 * XML id attribute
		 * @datatype	string
		 */
		'id' => 'boolean',
		/**
		 * XML name attribute
		 * @datatype	string
		 */
		'name' => 'boolean',
		/**
		 * XML class attribute
		 * @datatype	string
		 */
		'class' => 'input-boolean',
	);

	/* Functions */

	public static function render(
		array $parameters
	) {
		// Sets defaults
		$parameters = array_merge( self::$defaultParameters, $parameters );
		// Begins input
		$xmlOutput = parent::begin( $parameters['class'] );
		// Adds button
		$xmlOutput .= DataCenterXml::tag(
			'input',
			array_merge(
				array(
					'type' => 'checkbox',
					'id' => $parameters['id'],
					'name' => $parameters['name'],
					'class' => 'button',
				),
				( $parameters['value'] ? array( 'checked' => 'checked' ) : array() )
			)
		);
		// Ends input
		$xmlOutput .= parent::end();
		// Returns XML
		return $xmlOutput;
	}
}