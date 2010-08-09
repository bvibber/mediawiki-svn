<?php

/**
 * API extension for Distribution that allows for the querieng of extensions in the repository.
 * 
 * @file ApiQueryExtensions.php
 * @ingroup Distribution
 * 
 * @author Jeroen De Dauw
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
 * API class for the querieng of extensions in the repository.
 *
 * @ingroup Distribution
 */
class ApiQueryExtensions extends ApiQueryBase {
	public function __construct( $main, $action ) {
		parent :: __construct( $main, $action, 'dst' );
	}

	/**
	 * @since 0.1
	 */
	public function execute() {
		// Get the requests parameters.
		$params = $this->extractRequestParams();
		
		// TODO
	}
	
	/**
	 * @see includes/api/ApiBase#getAllowedParams()
	 * 
	 * @since 0.1
	 */
	public function getAllowedParams() {
		return array (
			'limit' => array(
				ApiBase :: PARAM_DFLT => 10,
				ApiBase :: PARAM_TYPE => 'limit',
				ApiBase :: PARAM_MIN => 1,
				ApiBase :: PARAM_MAX => ApiBase :: LIMIT_BIG1,
				ApiBase :: PARAM_MAX2 => ApiBase :: LIMIT_BIG2
			),
			'continue' => null,		
		);
	}

	/**
	 * @see includes/api/ApiBase#getParamDescription()
	 * 
	 * @since 0.1
	 */
	public function getParamDescription() {
		return array (
			'continue' => 'Number of the first story to return',
			'limit'   => 'Amount of stories to return',		
		);
	}

	/**
	 * @see includes/api/ApiBase#getDescription()
	 * 
	 * @since 0.1
	 */
	public function getDescription() {
		return 'Provides release information about MediaWiki extensions and packages.';
	}
	
	/**
	 * @see includes/api/ApiBase#getPossibleErrors()
	 * 
	 * @since 0.1
	 */
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
		) );
	}	
	
	/**
	 * @see includes/api/ApiBase#getExamples()
	 * 
	 * @since 0.1
	 */
	protected function getExamples() {
		return array (
			'api.php?action=query&list=extensions',
			'api.php?action=query&list=extensions&dstlimit=42',
		);
	}

	/**
	 * @since 0.1
	 */
	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
	
}