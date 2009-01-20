<?php
/**
 * History UI Class for DataCenter extension
 *
 * @file
 * @ingroup Extensions
 */

class DataCenterViewHistory extends DataCenterView {

	/* Functions */

	public function main(
		$path
	) {
		$changes = DataCenterDB::getChanges();
		return DataCenterUI::renderLayout(
			'columns',
			array(
				DataCenterUI::renderWidget( 'table',
					array(
						'rows' => $changes,
						'fields' => array(
							'date' => array(
								'field' => 'timestamp',
								'format' => 'date'
							),
							'username',
							'type',
							'component' => array(
								'fields' => array(
									'component_category',
									'component_type',
									'component_id'
								),
								'glue' => ' / '
							),
							'note'
						),
						'link' => array(
							'page' => 'history',
							'type' => 'change',
							'action' => 'view'
						)
					)
				)
			)
		);
	}

	public function view(
		$path
	) {
		// Checks if the user did not provide enough information
		if ( !$path['id'] ) {
			// Returns error message
			return DataCenterUI::message( 'error', 'insufficient-data' );
		}
		$change = DataCenterDB::getChange( $path['id'] );
		$component = DataCenterDB::getRow(
			'DataCenterComponent',
			$change->get( 'component_category' ),
			$change->get( 'component_type' ),
			$change->get( 'component_id' )
		);
		return DataCenterUI::renderLayout(
			'columns',
			array(
				DataCenterUI::renderWidget(
					'difference',
					array(
						'row-a' => $component->get(),
						'row-b' => unserialize( $change->get( 'state' ) )
					)
				)
			)
		);
	}

	/* Static Functions */

	public static function typeHistory(
		$path,
		$component
	) {
		$changes = $component->getChanges();
		return DataCenterUI::renderWidget( 'table',
			array(
				'rows' => $changes,
				'fields' => array(
					'date' => array(
						'field' => 'timestamp',
						'format' => 'date'
					),
					'username',
					'type',
					'note'
				),
				'link' => array(
					'page' => 'history',
					'type' => 'change',
					'action' => 'view'
				)
			)
		);
	}
}