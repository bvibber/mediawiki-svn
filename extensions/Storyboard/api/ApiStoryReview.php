<?php
/**
 * API extension for Storyboard.
 * 
 * @file ApiStoryReview.php
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
class ApiStoryReview extends ApiBase {

	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	public function execute() {
		global $wgUser;
		
		if ( !$wgUser->isAllowed( 'storyreview' ) || $wgUser->isBlocked() ) {
			$this->dieUsageMsg( array( 'badaccess-groups' ) );
		}
		
		$params = $this->extractRequestParams();
		
		// Check required parameters
		if ( !array_key_exists( 'storyid', $params ) ) {
			$this->dieUsageMsg( array( 'missingparam', 'storyid' ) );
		}
		if ( !array_key_exists( 'storyaction', $params ) ) {
			$this->dieUsageMsg( array( 'missingparam', 'storyaction' ) );
		}
		
		// TODO: test the actions after using them in the storyreview special page
		$dbw = wfGetDB( DB_MASTER );

		if ( $params['storyaction'] == 'delete' ) {
			$dbw->delete( 'storyboard', array( 'story_id' => $dbw->escape( $params['storyid'] ) ) );
		} else {
			$conds = array(
				'story_id' => $params['storyid']
			);
			
			switch( $params['storyaction'] ) {
				case 'hide' :
					$values = array(
						'story_is_hidden' => 1
					);
					break;
				case 'unhide' :
					$values = array(
						'story_is_hidden' => 0
					);
					break;
				case 'publish' :
					$values = array(
						'story_is_published' => 1
					);
					break;
				case 'unpublish' :
					$values = array(
						'story_is_published' => 0
					);
					break;
				case 'hideimage' :
					$values = array(
						'story_image_hidden' => 1
					);
					break;
				case 'showimage' :
					$values = array(
						'story_image_hidden' => 0
					);
					break;
				case 'deleteimage' :
					$values = array(
						'story_author_image' => ''
					);
					break;
			}
			
			$dbw->update( 'storyboard', $values, $conds );
		}
	}
	
	public function getAllowedParams() {
		return array(
			'storyid' => array(
				ApiBase :: PARAM_TYPE => 'integer',
			),
			'storyaction' => array(
				ApiBase::PARAM_TYPE => array(
					'hide',
					'unhide',
					'publish',
					'unpublish',
					'hideimage',
					'showimage',
					'deleteimage',
				)
			),
		);
	}
	
	public function getParamDescription() {
		return array(
			'storyid' => 'The id of the story you want to modify or delete',
			'storyaction' => 'Indicates in what way you want to modify the story',
		);
	}
	
	public function getDescription() {
		return array(
			'Story review actions'
		);
	}
	
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'missingparam', 'storyid' ),
			array( 'missingparam', 'storyaction' ),
		) );
	}

	protected function getExamples() {
		return array(
			'api.php?action=storyreview&storyid=42&storyaction=publish',
			'api.php?action=storyreview&storyid=42&storyaction=hide',
			'api.php?action=storyreview&storyid=42&storyaction=delete',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}