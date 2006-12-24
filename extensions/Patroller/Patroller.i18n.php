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

/* French (BrokenArrow) */
'fr' => array(
'patrol' => 'Vérification des modifications',
'patrol-endorse' => 'Accepter',
'patrol-revert' => 'Annuler',
'patrol-revert-reason' => 'Motif:',
'patrol-skip' => 'Sauter',
'patrol-reverting' => 'Annulation: $1',	
'patrol-nonefound' => 'Aucune édition suivie n\'a pu être trouvée pour la vérification.',
'patrol-endorsed-ok' => 'L\'édition a été marquée comme vérifiée.',
'patrol-endorsed-failed' => 'L\'édition n\'a pu être vérifiée.',
'patrol-reverted-ok' => 'L\'édition a été annulée.',
'patrol-reverted-failed' => 'L\'édition n\'a pu être annulée.',
'patrol-skipped-ok' => 'Ignorer l`édition.',
'patrol-reasons' => "* Simple vandalisme\n* Test de débutant\n* Voir page de discussion",
'patrol-another' => 'Voir une nouvelle édition, si elle est disponible.',
'patrol-stopped' => 'Vous avez choisi pour ne plus vérifier une autre édition. $1',
'patrol-resume' => 'Cliquer ici pour reprendre.',
),

/* Spanish (Titoxd) */
'es' => array(
'patrol' => 'Revisar ediciones',
'patrol-endorse' => 'Aprovar',
'patrol-revert' => 'Revertir',
'patrol-revert-reason' => 'Razón:',
'patrol-skip' => 'Omitir',
'patrol-reverting' => 'Revirtiendo: $1',	
'patrol-nonefound' => 'No hay ediciones disponibles para revisar.',
'patrol-endorsed-ok' => 'La edición fue marcada como revisada.',
'patrol-endorsed-failed' => 'La edición no se pudo marcar como revisada.',
'patrol-reverted-ok' => 'The edición fue revertida.',
'patrol-reverted-failed' => 'La edición no pudo ser revertida.',
'patrol-skipped-ok' => 'Ignorando la edición.',
'patrol-reasons' => "*Vandalismo simple\n* Prueba de usuario novato\n* Ver la página de discusión",
'patrol-another' => 'Mostrar otra edición (si disponible).',
'patrol-stopped' => 'Has optado no marcar otra edición como revisada. $1',
'patrol-resume' => 'Haz click aquí para continuar.',
),

	);
	return $messages;
}

?>
