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
 * @since 0.1
 *
 * @ingroup Distribution
 */
class ApiQueryExtensions extends ApiQueryBase {
	
	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 * 
	 * @param $main
	 * @param $action
	 */
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action, 'dst' );
	}

	/**
	 * Main method.
	 * 
	 * @since 0.1
	 */
	public function execute() {
		// Get the requests parameters.
		$params = $this->extractRequestParams();
		
		$this->addTables( 'distribution_units' );
		
		$this->addFields( array(
			'unit_id',
			'unit_name',
			'unit_current',
			'current_version_nr',
			'current_desc',
			'current_authors',
			'current_url'
		) );
		
		$this->addOption( 'LIMIT', $params['limit'] + 1 );
		$this->addOption( 'ORDER BY', 'unit_id' );		
		
		// Handle the continue parameter when it's provided.
		if ( !is_null( $params['continue'] ) ) {
			// TODO
		}	

		$count = 0;
		$extensions = $this->select( __METHOD__ );
		
		while ( $extension = $extensions->fetchObject() ) {
			if ( ++$count > $params['limit'] ) {
				// We've reached the one extra which shows that
				// there are additional pages to be had. Stop here...
				// TODO
				//$this->setContinueEnumParameter( 'continue', '' );
				break;
			}

			$result = array(
				//TODO
			);
			
			$this->getResult()->addValue( array( 'query', $this->getModuleName() ), null, $result );			
		}
		
		$this->getResult()->setIndexedTagName_internal( array( 'query', $this->getModuleName() ), 'extension' );
	}
	
	/**
	 * @see includes/api/ApiBase#getAllowedParams()
	 * 
	 * @since 0.1
	 */
	public function getAllowedParams() {
		return array (
			'limit' => array(
				ApiBase::PARAM_DFLT => 10,
				ApiBase::PARAM_TYPE => 'limit',
				ApiBase::PARAM_MIN => 1,
				ApiBase::PARAM_MAX => ApiBase::LIMIT_BIG1,
				ApiBase::PARAM_MAX2 => ApiBase::LIMIT_BIG2
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
			'continue' => 'Number of the first extension to return',
			'limit'   => 'Amount of extensions to return',		
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