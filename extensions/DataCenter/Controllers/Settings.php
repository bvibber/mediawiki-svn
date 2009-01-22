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
		'field' => array( 'page' => 'settings', 'type' => 'field' ),
	);

	/* Functions */

	public function __construct(
		array $path
	) {
		// Actions
		if ( $path['id'] && $path['type'] == 'field' ) {
			$this->actions['remove'] = array(
				'page' => 'settings',
				'type' => $path['type'],
				'action' => 'remove',
				'id' => $path['id']
			);
			$this->actions['configure'] = array(
				'page' => 'settings',
				'type' => $path['type'],
				'action' => 'configure',
				'id' => $path['id']
			);
			$this->actions['view'] = array(
				'page' => 'settings',
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
			case 'field':
				$field = DataCenterDBMetaField::newFromValues( $data['row'] );
				$field->save();
				return true;
		}
		return false;
	}

	public function remove(
		array $data,
		$type
	) {
		switch ( $type ) {
			case 'field':
				$field = DataCenterDBMetaField::newFromValues( $data['row'] );
				$values = $field->getValues();
				foreach ( $values as $value ) {
					$value->delete();
				}
				$field->delete();
				return true;
		}
		return false;
	}

	public function saveFieldLinks(
		array $data,
		$type
	) {
		DataCenterWidgetFieldLinks::saveFieldLinks( $data );
		return true;
	}
}