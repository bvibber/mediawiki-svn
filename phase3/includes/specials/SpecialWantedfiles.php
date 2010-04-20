<?php
/*
 * @file
 * @ingroup SpecialPage
 */

/**
 * Querypage that lists the most wanted files - implements Special:Wantedfiles
 *
 * @ingroup SpecialPage
 *
 * @author Soxred93 <soxred93@gmail.com>
 * @copyright Copyright © 2008, Soxred93
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
class WantedFilesPage extends WantedQueryPage {

	function __construct() {
		SpecialPage::__construct( 'Wantedfiles' );
	}

	function getQueryInfo() {
		return array (
			'tables' => array ( 'imagelinks', 'page' ),
			'fields' => array ( "'" . NS_FILE . "' AS namespace",
					'il_to AS title',
					'COUNT(*) AS value' ),
			'conds' => array ( 'page_title IS NULL' ),
			'options' => array ( 'GROUP BY' => 'il_to' ),
			'join_conds' => array ( 'page' => array ( 'LEFT JOIN',
				array ( 'il_to = page_title',
					'page_namespace' => NS_FILE ) ) )
		);
	}
}
