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
					'patrol-reverting' => 'Reverting',	
					'patrol-nonefound' => 'No suitable edits could be found for patrolling.',
					'patrol-endorsed-ok' => 'The edit was marked patrolled.',
					'patrol-reverted-ok' => 'The edit was reverted.',
					'patrol-skipped-ok' => 'Ignoring edit.',
				);
	$cache->addMessages( $messages );
}

?>