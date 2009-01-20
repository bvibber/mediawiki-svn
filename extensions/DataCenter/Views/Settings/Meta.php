<?php
/**
 * Connections UI Class for DataCenter extension
 *
 * @file
 * @ingroup Extensions
 */

class DataCenterViewSettingsMeta extends DataCenterView {

	/* Private Static Members */

	private static $componentTypes = array(
		'facility' => array(
			'location',
			'space',
		),
		'asset' => array(
			'rack',
			'object',
		),
		'model' => array(
			'rack',
			'object',
			'port',
		),
	);

	/* Functions */

	public function main(
		$path
	) {
		/*
		$tables = array();
		foreach ( $types as $type => $label ) {
			// Gets all components from database
			$fields = DataCenterDB::getMetaFields(
				DataCenterDB::buildCondition(
					'meta', 'field', 'asset_type', $type
				)
			);
			// Adds table to list of tables
			$tables[$label] = DataCenterUI::renderWidget(
				'table',
				array(
					'heading' => array( 'message' => 'fields' ),
					'rows' => $fields,
					'fields' => array(
						'name', 'format' => array( 'format' => 'option' )
					),
					'link' => array(
						'page' => 'settings',
						'type' => 'field',
						'id' => '#id',
						'action' => 'view',
					),
					'actions' => array(
						'links' => array(
							array(
								'page' => 'settings',
								'type' => 'field',
								'action' => 'add',
								'parameter' => $type,
							),
						),
					),
				)
			);
		}
		// Returns tabbed layout with tables of meta fields for each model type
		return DataCenterUI::renderLayout( 'tabs', $tables );
		*/
		return '[LIST OF META FIELDS]';
	}

	public function add(
		$path
	) {
		return $this->edit( $path );
	}

	public function edit(
		$path
	) {
		// Detects mode
		if ( !$path['id'] ) {
			// Creates new component
			$field = DataCenterMeta::newFromValues(
				'field', array( 'asset_type' => $path['parameter'] )
			);
			// Sets 'do' specific parameters
			$formParameters = array(
				'do' => 'add',
				'label' => 'add',
				'hidden' => array( 'asset_type' ),
				'success' => array(
					'page' => 'settings',
					'type' => 'meta'
				),
			);
		} else {
			// Gets component from database
			$field = DataCenterDB::getMetaField( $path['id'] );
			// Sets 'do' specific parameters
			$formParameters = array(
				'do' => 'edit',
				'label' => 'save',
				'hidden' => array( 'id', 'asset_type' ),
				'success' => array(
					'page' => 'settings',
					'type' => 'meta',
					'action' => 'view',
					'id' => $path['id'],
				),
			);
		}
		// Returns 2 columm layout with a form and a scene
		return DataCenterUI::renderLayout(
			'columns',
			array(
				DataCenterUI::renderWidget(
					'form',
					array_merge(
						$formParameters,
						array(
							'failure' => $path,
							'action' => array(
								'page' => 'settings',
								'type' => 'meta'
							),
							'row' => $field,
							'fields' => array(
								'name' => array( 'type' => 'string' ),
								'format' => array(
									'type' => 'list',
									'enum' => array(
										'category' => 'meta',
										'type' => 'field',
										'field' => 'format',
									),
								),
							),
						)
					)
				),
				'[MODEL VIEWER]',
			)
		);
	}
}