<?php
/**
 * Racks Page Class for DataCenter extension
 *
 * @file
 * @ingroup Extensions
 */

class DataCenterControllerAssets extends DataCenterController {

	/* Members */

	public $types = array(
		'rack' => array( 'page' => 'assets', 'type' => 'rack' ),
		'object' => array( 'page' => 'assets', 'type' => 'object' ),
	);

	/* Functions */

	public function __construct(
		array $path
	) {
		// Actions
		if ( $path['id'] ) {
			$this->actions['manage'] = array(
				'page' => 'assets',
				'type' => $path['type'],
				'action' => 'manage',
				'id' => $path['id'],
			);
			$this->actions['history'] = array(
				'page' => 'assets',
				'type' => $path['type'],
				'action' => 'history',
				'id' => $path['id'],
			);
			$this->actions['view'] = array(
				'page' => 'assets',
				'type' => $path['type'],
				'action' => 'view',
				'id' => $path['id'],
			);
		}
	}

	public function save(
		array $data,
		$type
	) {
		$asset = DataCenterDBAsset::newFromType( $type, $data );
		$asset->save();
		$change = DataCenterDBChange::newFromComponent( $asset );
		$change->save();
		return true;
	}
}