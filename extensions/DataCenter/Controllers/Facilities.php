<?php
/**
 * Facilities Page Class for DataCenter extension
 *
 * @file
 * @ingroup Extensions
 */

class DataCenterControllerFacilities extends DataCenterController {

	/* Members */

	public $types = array(
		'location' => array( 'page' => 'facilities', 'type' => 'location' ),
		'space' => array( 'page' => 'facilities', 'type' => 'space' ),
	);

	/* Functions */

	public function __construct(
		array $path
	) {
		// Actions
		if ( $path['id'] ) {
			$this->actions['edit'] = array(
				'page' => 'facilities',
				'type' => $path['type'],
				'action' => 'edit',
				'id' => $path['id']
			);
			$this->actions['history'] = array(
				'page' => 'facilities',
				'type' => $path['type'],
				'action' => 'history',
				'id' => $path['id']
			);
			$this->actions['view'] = array(
				'page' => 'facilities',
				'type' => $path['type'],
				'action' => 'view',
				'id' => $path['id']
			);
		}
	}

	public function save(
		array $data,
		$type
	) {
		switch ( $type ) {
			case 'location':
				$component = DataCenterDBLocation::newFromValues( $data );
				break;
			case 'space':
				$component = DataCenterDBSpace::newFromValues( $data );
				break;
		}
		if ( isset( $component ) ) {
			$component->save();
			$change = DataCenterDBChange::newFromComponent( $component );
			$change->save();
			return true;
		}
		return false;
	}
}