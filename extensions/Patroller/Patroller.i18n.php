<?php

/**
 * Internationalisation file for the Patroller extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0
 */

function efPatrollerAddMessages( &$cache ) {
	$messages = array(
					'patrol' => 'Patrol edits',
					
					'patrol-endorse' => 'Endorse',
					'patrol-revert' => 'Revert',
					'patrol-revert-reason' => 'Reason:',
					'patrol-skip' => 'Skip',
					
					'patrol-instructions' => 'THE POWER OF THE CABAL COMPELS YOU! THE POWER OF THE CABAL COMPELS YOU!',
				);
	$cache->addMessages( $messages );
}

?>