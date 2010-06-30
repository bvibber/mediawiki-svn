<?php
/**
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

/**
 * A querypage to list the most wanted templates - implements Special:Wantedtemplates
 * based on SpecialWantedcategories.php by Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 *
 * @file
 * @ingroup SpecialPage
 *
 * @author Danny B.
 * @copyright Copyright © 2008, Danny B.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
class WantedTemplatesPage extends WantedQueryPage {

	function __construct() {
		SpecialPage::__construct( 'Wantedtemplates' );
	}

	function getQueryInfo() {
		return array (
			'tables' => array ( 'templatelinks', 'page' ),
			'fields' => array ( 'tl_namespace AS namespace',
					'tl_title AS title',
					'COUNT(*) AS value' ),
			'conds' => array ( 'page_title IS NULL',
					'tl_namespace' => NS_TEMPLATE ),
			'options' => array (
				'GROUP BY' => 'tl_namespace, tl_title' ),
			'join_conds' => array ( 'page' => array ( 'LEFT JOIN',
					array ( 'page_namespace = tl_namespace',
						'page_title = tl_title' ) ) )
		);
	}
}
