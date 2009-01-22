<?php
/**
 * Configuration Page Class for DataCenter extension
 *
 * @file
 * @ingroup Extensions
 */

class DataCenterControllerSettings extends DataCenterController {

	/* Members */

	public $types = array(
		'meta' => array( 'page' => 'settings', 'type' => 'meta' ),
	);

	/* Functions */

	public function __construct(
		array $path
	) {
		// Actions
	}

	public function add(
		array $data,
		$type
	) {
		$setting = DataCenterMeta::newFromValues( $type, $data['row'] );
		return $setting->save();
	}

	public function edit(
		array $data,
		$type
	) {
		$setting = DataCenterMeta::newFromValues( $type, $data['row'] );
		return $setting->save();
	}
}