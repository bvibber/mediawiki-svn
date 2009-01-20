<?php
/**
 * Facilities UI Class for DataCenter extension
 *
 * @file
 * @ingroup Extensions
 */

class DataCenterViewFacilities extends DataCenterView {

	/* Functions */

	public function main(
		$path
	) {
		return DataCenterUI::renderLayout(
			'columns',
			array(
				__CLASS__,
				__METHOD__
			)
		);
	}
}