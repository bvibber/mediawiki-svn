<?php
/**
 * Racks Page Class for DataCenter extension
 *
 * @file
 * @ingroup Extensions
 */

class DataCenterControllerModels extends DataCenterController {

	/* Members */

	public $types = array(
		'rack' => array( 'page' => 'models', 'type' => 'rack' ),
		'object' => array( 'page' => 'models', 'type' => 'object' ),
		'port' => array( 'page' => 'models', 'type' => 'port' ),
	);

	/* Functions */

	public function __construct(
		array $path
	) {
		// Actions
		if ( $path['id'] && isset( $this->types[$path['type']] ) ) {
			$this->actions['modify'] = array(
				'page' => 'models',
				'type' => $path['type'],
				'action' => 'modify',
				'id' => $path['id']
			);
			$this->actions['history'] = array(
				'page' => 'models',
				'type' => $path['type'],
				'action' => 'history',
				'id' => $path['id']
			);
			$this->actions['view'] = array(
				'page' => 'models',
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
		$model = DataCenterDBModel::newFromType( $type, $data );
		$model->save();
		$change = DataCenterDBChange::newFromComponent( $model );
		$change->save();
		return true;
	}

	public function link(
		array $data,
		$type
	) {
		$link = DataCenterDBModelLink::newFromValues( $data );
		if ( $link->get( 'quantity' ) == 0 ) {
			$link->delete();
		} else {
			$link->save();
		}
		return true;
	}
}