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
					'patrol-instructions' => 'Use this page to do evil patrolling stuff.',
				);
	$cache->addMessages( $messages );
}

?>