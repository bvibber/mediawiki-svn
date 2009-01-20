<?php
/**
 * Overview UI Class for DataCenter extension
 *
 * @file
 * @ingroup Extensions
 */

class DataCenterViewOverview extends DataCenterView {

	/* Functions */

	public function main(
		$path
	) {
		return DataCenterUI::renderLayout(
			'columns',
			array(
				DataCenterUI::renderLayout(
					'columns',
					array(
						DataCenterUI::renderWidget(
							'body',
							array(
								'message' => 'welcome',
								'type' => 'important'
							)
						)
					)
				)
			)
		);
	}
}