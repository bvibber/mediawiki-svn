<?php
/**
 * Objects UI Class for DataCenter extension
 *
 * @file
 * @ingroup Extensions
 */

class DataCenterViewPlansObject extends DataCenterView {

	/* Functions */

	public function view(
		$path
	) {
		// Checks if the user did not provide enough information
		if ( !$path['id'] ) {
			// Returns error message
			return DataCenterUI::message( 'error', 'insufficient-data' );
		}
		// Gets link from database
		$objectLink = DataCenterDB::getAssetLink( $path['id'] );
		// Extracts object from object link
		$object = $objectLink->getAsset();
		// Gets plan from database
		$plan = DataCenterDB::getPlan( $objectLink->get( 'plan' ) );
		// Gets rack link object is linked to
		$rackLink = DataCenterDB::getAssetLink(
			$objectLink->get( 'parent_link' )
		);
		// Extracts rack from rack link
		$rack = $rackLink->getAsset();
		// Gets structure of plan
		$structure = $plan->getStructure(
			DataCenterDB::buildSort(
				'link', 'asset', array( 'orientation', 'z DESC' )
			)
		);
		// Returns single columm layout with a table
		return DataCenterUI::renderLayout(
			'columns',
			array(
				DataCenterUI::renderLayout(
					'rows',
					array(
						DataCenterUI::renderWidget(
							'heading', array( 'message' => 'configuration' )
						),
						DataCenterUI::renderWidget(
							'details',
							array(
								'row' => $objectLink,
								'fields' => array(
									'position' => array(
										'field' => 'z'
									),
									'side' => array(
										'field' => 'orientation',
										'format' => 'side'
									)
								),
							)
						),
						DataCenterUI::renderWidget(
							'heading', array( 'message' => 'asset' )
						),
						DataCenterUI::renderWidget(
							'details',
							array(
								'row' => $object,
								'fields' => array( 'serial', 'asset' ),
							)
						),
					)
				),
				DataCenterUI::renderWidget(
					'plan', array(
						'plan' => $plan,
						'current-rack' => $rack->getId(),
						'look-at-rack' => $rack->getId(),
						'current-object' => $object->getId()
					)
				),
			)
		);
	}

	public function select(
		$path
	) {
		// Checks if...
		if (
			// No rack asset was specified
			!$path['id'] &&
			// Parameters were given
			is_array( $path['parameter'] ) &&
			// At least 3 parameters were given
			count( $path['parameter'] ) >= 2 &&
			// The deployment target is a location
			( $path['parameter'][0] == 'rack' )
		) {
			$rackLink = DataCenterDB::getAssetLink( $path['parameter'][1] );
			$plan = DataCenterDB::getPlan( $rackLink->get( 'plan' ) );
			$space = $plan->getSpace();
			// Gets object links which are linked to this rack
			$objectLinks = DataCenterDB::getAssetLinks(
				array_merge_recursive(
					DataCenterDB::buildCondition(
						'link', 'asset', 'plan', $plan->getId()
					),
					DataCenterDB::buildCondition(
						'link', 'asset', 'parent_link', $rackLink->getId()
					),
					DataCenterDB::buildCondition(
						'link', 'asset', 'asset_type', 'object'
					)
				)
			);
			$existsTable = array();
			foreach ( $objectLinks as $objectLink ) {
				$existsTable[$objectLink->get( 'asset_id' )] = true;
			}
			// Gets objects from database in two varieties, local and remote
			$objects = array(
				'local' => DataCenterDB::getAssets(
					'object',
					DataCenterDB::buildCondition(
						'asset', 'object', 'location', $space->get( 'location' )
					)
				),
				'remote' => DataCenterDB::getAssets(
					'object',
					DataCenterDB::buildCondition(
						'asset',
						'object',
						'location',
						$space->get( 'location' ),
						'!='
					)
				)
			);
			$tabs = array();
			foreach ( $objects as $groupName => $objectGroup ) {
				foreach( $objectGroup as $key => $object ) {
					if (
						$object->get( 'tense' ) == 'past' ||
						isset( $existsTable[$object->getId()] )
					) {
						unset( $objectGroup[$key] );
					} else {
						$objectModel = $object->getModel();
						$object->set(
							$objectModel->get( array( 'name', 'manufacturer' ) )
						);
					}
				}
				if ( count( $objectGroup ) > 0 ) {
					$tabs[$groupName] = DataCenterUI::renderWidget(
						'table',
						array(
							'rows' => $objectGroup,
							'fields' => array(
								'manufacturer',
								'model' => array( 'field' => 'name' ),
								'serial',
								'asset'
							),
							'link' => array(
								'page' => 'plans',
								'type' => 'object',
								'id' => $path['id'],
								'action' => 'attach',
								'parameter' => array(
									'rack', $path['parameter'][1], '#id'
								),
							),
						)
					);
				} else {
					$tabs[$groupName] = null;
				}
			}
			// Returns 2 columm layout with a form and a scene
			return DataCenterUI::renderLayout(
				'columns',
				array(
					DataCenterUI::renderLayout(
						'rows',
						array(
							DataCenterUI::renderWidget(
								'heading',
								array(
									'message' => 'select-type',
									'type' => 'object'
								)
							),
							DataCenterUI::renderLayout( 'tabs', $tabs )
						)
					),
				)
			);
		}
	}

	public function remove(
		$path
	) {
		// Gets link from database
		$objectLink = DataCenterDB::getAssetLink( $path['id'] );
		// Extracts object from link
		$object = $objectLink->getAsset();
		// Gets rack link from database
		$rackLink = DataCenterDB::getAssetLink(
			$objectLink->get( 'parent_link' )
		);
		// Gets rack from database
		$rack = $rackLink->getAsset();
		// Gets plan from database
		$plan = DataCenterDB::getPlan( $objectLink->get( 'plan' ) );
		// Gets links to object
		$links = $objectLink->getLinks();
		// Returns 2 columm layout with a form and a scene
		return DataCenterUI::renderLayout(
			'columns',
			array(
				DataCenterUI::renderLayout(
					'rows',
					array(
						DataCenterUI::renderWidget(
							'heading',
							array(
								'message' => 'remove-type',
								'type' => 'object'
							)
						),
						DataCenterUI::renderWidget(
							'body',
							array(
								'message' => 'confirm-remove-type',
								'subject' => $path['type'],
								'type' => 'notice',
							)
						),
						DataCenterUI::renderWidget(
							'table',
							array(
								'rows' => $links,
								'fields' => array(
									'name',
									'type' => array(
										'field' => 'asset_type',
										'format' => 'type'
									)
								)
							)
						),
						DataCenterUI::renderWidget(
							'form',
							array(
								'do' => 'remove',
								'label' => 'remove',
								'hidden' => array( 'id' ),
								'success' => array(
									'page' => 'plans',
									'type' => 'rack',
									'action' => 'view',
									'id' => $rack->getId(),
								),
								'failure' => $path,
								'cancellation' => array(
									'page' => 'plans',
									'type' => 'object',
									'action' => 'view',
									'id' => $path['id'],
								),
								'row' => $objectLink,
								'action' => array(
									'page' => 'plans',
									'type' => 'object'
								),
								'fields' => array()
							)
						)
					)
				),
				DataCenterUI::renderWidget(
					'plan', array(
						'plan' => $plan,
						'look-at-rack' => $rack->getId(),
						'current-rack' => $rack->getId(),
						'current-object' => $object->getId(),
					)
				)
			)
		);
	}

	public function attach(
		$path
	) {
		return $this->configure( $path );
	}

	public function configure(
		$path
	) {
		// Checks if...
		if (
			// No rack asset was specified
			!$path['id'] &&
			// Parameters were given
			is_array( $path['parameter'] ) &&
			// At least 2 parameters were given
			count( $path['parameter'] ) >= 3 &&
			// The deployment target is...
			(
				( $path['parameter'][0] == 'rack' ) ||
				( $path['parameter'][0] == 'object' ) // NOT READY
			)
		) {
			if ( $path['parameter'][0] == 'rack' ) {
				$rackLink = DataCenterDB::getAssetLink( $path['parameter'][1] );
				$rack = $rackLink->getAsset();
				$plan = DataCenterDB::getPlan( $rackLink->get( 'plan' ) );
				$object = DataCenterDB::getAsset(
					'object', $path['parameter'][2]
				);
				$objectLink = DataCenterDBAssetLink::newFromValues(
					array(
						'name' => DataCenterUI::message( 'type', 'object' ),
						'plan' => $plan->getId(),
						'parent_link' => $rackLink->getId(),
						'asset_type' => 'object',
						'asset_id' => $object->getId(),
						'z' => 1,
						'orientation' => 0,
					)
				);
				// Sets action specific parameters
				$formParameters = array(
					'label' => 'attach',
					'hidden' => array(
						'plan', 'parent_link', 'asset_type', 'asset_id'
					),
					'success' => array(
						'page' => 'plans',
						'type' => 'rack',
						'action' => 'view',
						'id' => $path['parameter'][1],
					),
				);
				$headingParameters = array(
					'message' => 'attaching-type',
					'type' => 'object',
				);
			} else {
				throw new MWException(
					'Invalid parameters'
				);
			}
		} else {
			// Gets asset from database
			$objectLink = DataCenterDB::getAssetLink( $path['id'] );
			// Gets object that object link links to
			$object = $objectLink->getAsset();
			// Gets plan from database
			$plan = DataCenterDB::getPlan( $objectLink->get( 'plan' ) );
			// Gets rack link object is linked to
			$rackLink = DataCenterDB::getAssetLink(
				$objectLink->get( 'parent_link' )
			);
			// Extracts rack from rack link
			$rack = $rackLink->getAsset();
			// Sets action specific parameters
			$formParameters = array(
				'label' => 'save',
				'hidden' => array( 'id' ),
				'success' => array(
					'page' => 'plans',
					'type' => 'object',
					'action' => 'view',
					'id' => $path['id'],
				),
			);
			$headingParameters = array(
				'message' => 'configuring-type',
				'type' => 'object',
			);
		}
		// Builds javascript that references the renderable asset
		$target = array(
			'dataCenter.renderer.getTarget' => array(
				DataCenterJs::toScalar( 'plan' )
			),
			'getModule'
		);
		$rackId = $rack->getId();
		$rackModel = $rack->getModel();
		$objectModel = $object->getModel();
		$objectId = $object->getId();
		$maxZ = (
			( $rackModel->get( 'units' ) - $objectModel->get( 'units' ) ) + 1
		);
		// Builds form parameters
		$formParameters = array_merge(
			$formParameters,
			array(
				'do' => 'save',
				'failure' => $path,
				'action' => array(
					'page' => 'plans',
					'type' => 'object'
				),
				'row' => $objectLink,
				'fields' => array(
					'name' => array( 'type' => 'string' ),
					'position' => array(
						'type' => 'number',
						'field' => 'z',
						'min' => 1,
						'max' => $maxZ,
						'effect' => DataCenterJs::chain(
							array_merge(
								$target,
								array(
									'setObjectPosition' => array(
										DataCenterJs::toScalar( $rackId ),
										DataCenterJs::toScalar( $objectId ),
										'{this}.value',
										DataCenterJs::toScalar( true )
									)
								)
							),
							false
						),
					),
					'orientation' => array(
						'type' => 'number',
						'min' => 0,
						'max' => 1,
						'effect' => DataCenterJs::chain(
							array_merge(
								$target,
								array(
									'setObjectOrientation' => array(
										DataCenterJs::toScalar( $rackId ),
										DataCenterJs::toScalar( $objectId ),
										'{this}.value',
										DataCenterJs::toScalar( true )
									)
								)
							),
							false
						),
					),
				)
			)
		);
		// Returns 2 columm layout with a form and a scene
		return DataCenterUI::renderLayout(
			'columns',
			array(
				DataCenterUI::renderLayout(
					'rows',
					array(
						DataCenterUI::renderWidget(
							'heading', $headingParameters
						),
						DataCenterUI::renderWidget( 'form', $formParameters ),
					)
				),
				DataCenterUI::renderWidget(
					'plan', array(
						'plan' => $plan,
						'current-rack' => $rack->getId(),
						'look-at-rack' => $rack->getId(),
						'current-object' => $object->getId(),
						'include' => ( !$path['id'] ? $objectLink : null ),
					)
				),
			)
		);
	}
}