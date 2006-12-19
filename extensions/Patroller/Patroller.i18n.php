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

function efPatrollerMessages() {
	$messages = array(

/* English (Rob Church) */
'en' => array(
'patrol' => 'Patrol edits',
'patrol-endorse' => 'Endorse',
'patrol-revert' => 'Revert',
'patrol-revert-reason' => 'Reason:',
'patrol-skip' => 'Skip',
'patrol-reverting' => 'Reverting: $1',	
'patrol-nonefound' => 'No suitable edits could be found for patrolling.',
'patrol-endorsed-ok' => 'The edit was marked patrolled.',
'patrol-endorsed-failed' => 'The edit could not be marked patrolled.',
'patrol-reverted-ok' => 'The edit was reverted.',
'patrol-reverted-failed' => 'The edit could not be reverted.',
'patrol-skipped-ok' => 'Ignoring edit.',
'patrol-reasons' => "* Simple vandalism\n* Newbie test\n* See talk page",
'patrol-another' => 'Show another edit, if available.',
'patrol-stopped' => 'You have opted not to patrol another edit. $1',
'patrol-resume' => 'Click here to resume.',
),

/* Italian (BrokenArrow) */
'it' => array(
'patrol' => 'Verifica delle modifiche',
'patrol-endorse' => 'Approva',
'patrol-revert' => 'Ripristina',
'patrol-revert-reason' => 'Motivo:',
'patrol-skip' => 'Salta',
'patrol-reverting' => 'Ripristino: $1',	
'patrol-nonefound' => 'Non vi sono modifiche da verificare.',
'patrol-endorsed-ok' => 'La modifica è stata segnata come verificata.',
'patrol-endorsed-failed' => 'Impossibile segnare la modifica come verificata.',
'patrol-reverted-ok' => 'La modifica è stata annullata.',
'patrol-reverted-failed' => 'Impossibile annullare la modifica.',
'patrol-skipped-ok' => 'Modifica ignorata.',
'patrol-reasons' => "* Vandalismo semplice\n* Prova di nuovo utente\n* Vedi pagina di discussione",
'patrol-another' => 'Mostra un\'altra modifica, se disponibile.',
'patrol-stopped' => 'Si è scelto di non verificare altre modifiche. $1',
'patrol-resume' => 'Fare clic qui per riprendere.',
),
	
	);
	return $messages;
}

?>