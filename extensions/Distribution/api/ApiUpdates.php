<?php

/**
 * API extension for Distribution that provides information about MediaWiki and extension updates.
 * 
 * @file ApiUpdates.php
 * @ingroup Distribution
 * @ingroup API
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

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Provides information about MediaWiki and extension updates.
 * 
 * @since 0.1
 *
 * @ingroup Distribution
 * @ingroup API
 */
class ApiUpdates extends ApiBase {
	
	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 * 
	 * @param $main
	 * @param $action
	 */
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}
	
	/**
	 * Main method.
	 * 
	 * @since 0.1
	 */
	public function execute() {
		$params = $this->extractRequestParams();
		
		// TODO
	}	
	
	/**
	 * @see ApiBase::getAllowedParams
	 * 
	 * @since 0.1
	 */	
	public function getAllowedParams() {
		return array(
			'mediawiki' => array(
				ApiBase::PARAM_TYPE => 'string',
			),
			'extensions' => array(
				ApiBase::PARAM_TYPE => 'string',
			),
			'state' => array(
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_TYPE => DistributionRelease::getStates(),
				ApiBase::PARAM_DFLT => DistributionRelease::getDefaultState(),
			),
		);
	}
	
	/**
	 * @see ApiBase::getParamDescription
	 * 
	 * @since 0.1
	 */	
	public function getParamDescription() {
		return array(
			'mediawiki'  => 'The installed version of MediaWiki',
			'extensions' => 'A |-seperated list of extensions and their version, seperated by a semicolon',
			'state'      => 'A list of allowed release states'
		);
	}
	
	/**
	 * @see ApiBase::getDescription
	 * 
	 * @since 0.1
	 */	
	public function getDescription() {
		return array(
			'Provides information about MediaWiki and extension updates'
		);
	}
		
	/**
	 * @see ApiBase::getPossibleErrors
	 * 
	 * @since 0.1
	 */	
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
		) );
	}

	/**
	 * @see ApiBase::getExamples
	 * 
	 * @since 0.1
	 */
	protected function getExamples() {
		return array(
		);
	}	
	
	/**
	 * @see ApiBase::getVersion
	 * 
	 * @since 0.1
	 */
	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
	
}