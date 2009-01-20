<?php
/**
 * Objects UI Class for DataCenter extension
 *
 * @file
 * @ingroup Extensions
 */

class DataCenterViewPlansPlan extends DataCenterView {

	/* Functions */

	public function view(
		$path
	) {
		// Checks if the user did not provide enough information
		if ( !$path['id'] ) {
			// Returns error message
			return DataCenterUI::message( 'error', 'insufficient-data' );
		}
		// Gets plan from database
		$plan = DataCenterDB::getPlan( $path['id'] );
		// Gets space of plan
		$space = $plan->getSpace();
		// Sets the space name in the plan
		$plan->set( 'space_name', $space->get( 'name' ) );
		// Gets structure of plan from database
		$structure = $plan->getStructure(
			DataCenterDB::buildSort(
				'link', 'asset', array( 'x', 'y' )
			)
		);
		// Builds tables from structure
		$tables = DataCenterDB::buildLookupTable( 'asset_type', $structure );
		// Builds list of rack assets used in plan
		if ( isset( $tables['rack'] ) && is_array( $tables['rack'] ) ) {
			foreach ( $tables['rack'] as $key => $link ) {
				$rack = $link->getAsset();
				$model = $rack->getModel();
				$tables['rack'][$key]->set(
					array( 'model' => $model->get( 'name' ) )
				);
			}
		}
		if ( !isset( $tables['rack'] ) ) {
			$tables['rack'] = array();
		}
		// Builds javascript that references the renderable plan
		$target = array(
			'dataCenter.renderer.getTarget' => array(
				DataCenterJs::toScalar( 'plan' )
			),
			'getModule'
		);
		// Detects if this user came from a zoomed in page
		$refererPath = DataCenterPage::getRefererPath();
		$zoomOptions = array();
		if (
			$refererPath['page'] == $path['page'] &&
			$refererPath['type'] == 'rack' &&
			$refererPath['id'] !== null
		) {
			$rackLink = DataCenterDB::getAssetLink( $refererPath['id'] );
			$rack = $rackLink->getAsset();
			$zoomOptions['zoom-from-rack'] = $rack->getId();
		}
		// Builds table of racks
		$racks = DataCenterUI::renderWidget(
			'table',
			array(
				'rows' => $tables['rack'],
				'fields' => array(
					'name',
					'model',
					'position' => array(
						'fields' => array( 'x', 'y' ),
						'glue' => 'x'
					)
				),
				'link' => array(
					'page' => 'plans',
					'type' => 'rack',
					'action' => 'view',
					'id' => '#id'
				),
				'effects' => array(
					array(
						'event' => 'onmouseover',
						'script' => DataCenterJs::chain(
							array_merge(
								$target,
								array(
									'setRackHighlight' => array(
										'{asset_id}',
										DataCenterJs::toScalar( true ),
									)
								)
							),
							false
						),
					),
					array(
						'event' => 'onmouseout',
						'script' => DataCenterJs::chain(
							array_merge(
								$target,
								array(
									'clearRackHighlight' => array(
										DataCenterJs::toScalar( true ),
									)
								)
							),
							false
						),
					),
				),
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
							'heading',
							array(
								'message' => 'racks-in',
								'subject' => $space->get( 'name' ),
							)
						),
						$racks,
						DataCenterUI::renderWidget(
							'actions',
							array(
								'links' => array(
									array(
										'page' => 'plans',
										'type' => 'rack',
										'action' => 'select',
										'parameter' => array(
											'plan', $path['id']
										)
									),
								),
							)
						),
						DataCenterUI::renderWidget(
							'heading',
							array(
								'message' => 'details-for',
								'subject' => $plan->get( 'name' ),
							)
						),
						DataCenterUI::renderWidget(
							'details',
							array(
								'row' => $plan,
								'fields' => array(
									'name',
									'tense' => array( 'format' => 'option' ),
									'space' => array( 'field' => 'space_name' ),
									'note',
								),
							)
						),
					)
				),
				DataCenterUI::renderWidget(
					'plan',
					array_merge( array( 'plan' => $plan ), $zoomOptions )
				)
			)
		);
	}

	public function remove(
		$path
	) {
		// Gets link from database
		$plan = DataCenterDB::getPlan( $path['id'] );
		// Gets links to plan
		$links = $plan->getLinks();
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
								'message' => 'confirm-remove',
								'subject' => $plan->get( 'name' ),
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
								),
								'failure' => $path,
								'cancellation' => array(
									'page' => 'plans',
									'type' => 'plan',
									'action' => 'view',
									'id' => $path['id'],
								),
								'row' => $plan,
								'action' => array(
									'page' => 'plans',
									'type' => 'plan'
								),
								'fields' => array()
							)
						),
					)
				),
				DataCenterUI::renderWidget( 'plan', array( 'plan' => $plan ) )
			)
		);
	}

	public function add(
		$path
	) {
		return $this->configure( $path );
	}

	public function configure(
		$path
	) {
		// Detects mode
		if ( !$path['id'] ) {
			if (
				is_array( $path['parameter'] ) &&
				( count( $path['parameter'] ) >= 2 ) &&
				$path['parameter'][0] == 'space'
			) {
				// Gets space from database
				$space = DataCenterDB::getSpace( $path['parameter'][1] );
				// Creates new component
				$plan = DataCenterDBPlan::newFromValues(
					array(
						'space' => $path['parameter'][1],
						'tense' => 'present',
						'name' => $space->get( 'name' ),
					)
				);
				// Sets 'do' specific parameters
				$formParameters = array(
					'label' => 'create',
					'hidden' => array( 'space' ),
					'success' => array( 'page' => 'plans' ),
				);
			} else {
				throw new MWException(
					'Invalid parameters. space,# expected.'
				);
			}
		} else {
			// Gets component from database
			$plan = DataCenterDB::getPlan( $path['id'] );
			// Sets 'do' specific parameters
			$formParameters = array(
				'label' => 'save',
				'hidden' => array( 'id' ),
				'success' => array(
					'page' => 'plans',
					'type' => 'plan',
					'action' => 'view',
					'id' => $path['id'],
				),
			);
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
								'message' => 'editing-details-for',
								'subject' => $plan->get( 'name' ),
							)
						),
						DataCenterUI::renderWidget(
							'form',
							array_merge(
								$formParameters,
								array(
									'do' => 'save',
									'failure' => $path,
									'action' => array(
										'page' => 'plans',
										'type' => 'plan'
									),
									'row' => $plan,
									'fields' => array(
										'name' => array( 'type' => 'string' ),
										'tense' => array( 'type' => 'tense' ),
										'note' => array( 'type' => 'text' )
									)
								)
							)
						),
					)
				),
				DataCenterUI::renderWidget(
					'plan', array( 'plan' => $plan )
				)
			)
		);
	}
}