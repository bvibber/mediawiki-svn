<?php
/**
 * API extension for Storyboard.
 * 
 * @file ApiStoryboard.php
 * @ingroup Storyboard
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
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	// Eclipse helper - will be ignored in production
	require_once ( "ApiBase.php" );
}

/**
 * This action returns the html for Stories to be displayed in a storyboard.
 *
 * @ingroup Storyboard
 */
class ApiStoryExists extends ApiBase {
	
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	public function execute() {
		$params = $this->extractRequestParams();
		
		if ( !isset( $params['storyname'] ) ) {
			$this->dieUsageMsg( array( 'missingparam', 'storyname' ) );
		}		
					
		$dbr = wfGetDB( DB_SLAVE );
		
		$story = $dbr->selectRow(
			'storyboard',
			array( 'story_id' ),
			array( 'story_title' => str_replace( '_', ' ', $params['storyname'] ) )
		);
		
		$result = array(
			'exists' => $story != false
		);
		
		$this->getResult()->setIndexedTagName( $result, 'story' );
		$this->getResult()->addValue( null, $this->getModuleName(), $result );			
	}
	
	public function getAllowedParams() {
		return array(
			'storyname' => array(
				ApiBase :: PARAM_TYPE => 'string',
			),
		);
	}
	
	public function getParamDescription() {
		return array(
			'storyname' => 'The name of the story to check for.'
		);
	}
	
	public function getDescription() {
		return array(
			'Enables determining if a story exists already'
		);	
	}
		
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'missingparam', 'storyname' ),
		) );
	}

	protected function getExamples() {
		return array(
			'api.php?action=storyexists&storyname=oHai there!',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id: ApiStoryExists.php 63775 2010-03-15 16:35:22Z jeroendedauw $';
	}	
}