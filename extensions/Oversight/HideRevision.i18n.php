<?php

/**
 * Internationalisation file for the Oversight extension
 */

function hrHideRevisionMessages() {
	return array(
	
/* English (Brion Vibber) */
'en' => array(
'hiderevision' => 'Permanently hide revisions',
// Empty form
'hiderevision-prompt' => 'Revision number to remove:',
'hiderevision-continue' => 'Continue',
// Confirmation form
'hiderevision-text' =>
"This should '''only''' be used for the following cases:
* Inappropriate personal information
*: ''home addresses and telephone numbers, social security numbers, etc''

'''Abuse of this system will result in loss of privileges.'''

Removed items will not be visible to anyone through the web site,
but the deletions are logged and can be restored manually by a
database administrator if you make a mistake.",
'hiderevision-reason' => 'Reason (will be logged privately):',
'hiderevision-submit' => 'Hide this data permanently',
// Tab displayed to allowed users on old revision display
'hiderevision-tab' => 'Hide revision',
// Status & errors on action
'hiderevision-norevisions' => 'No revisions specified to delete.',
'hiderevision-noreason' => 'You must decribe the reason for this removal.',
'hiderevision-status' => 'Revision $1: $2',
'hiderevision-success' => 'Archived and deleted successfully.',
'hiderevision-error-missing' => 'Not found in database.',
'hiderevision-error-current' => 'Cannot delete the latest edit to a page. Revert this change first.',
'hiderevision-error-delete' => 'Could not archive; was it previously deleted?',
'hiderevision-archive-status' => 'Deleted revision from $1: $2',
// Logging
'oversight-log-hiderev' => 'removed an edit from $1',
// Oversight review page
'oversight' => 'Oversight',
'oversight-view' => 'details',
),

/* French (Bertrand Grondin) */
'fr' => array(
'hiderevision' => 'Cacher définitivement les révisions',
// Empty form
'hiderevision-prompt' => 'Numéro d\'édition à supprimer:',
'hiderevision-continue' => 'Continuer',
// Confirmation form
'hiderevision-text' =>
"Cette fonctionnalité doit être utilisée '''uniquement''' pour les cas suivants :
* Information personnelle inappropriée,
*: ''Adresse personnelle et numéro de téléphone, numéro de sécurité sociale, etc...''

''' L'abus de cette fonctionnalité impliquera la perte de ces privilèges.

Les articles effacés ne sont plus visible dans ce système, mais ces suppression sont journalisées et peuvent être restaurées manuellement par un administrateur de la base de donnée si vous avez fait une erreur.",
'hiderevision-reason' => 'Motif (Sera journalisé séparément):',
'hiderevision-submit' => 'Cacher cette donnée de manière permanente',
// Tab displayed to allowed users on old revision display
'hiderevision-tab' => 'Cacher la modification',
// Status & errors on action
'hiderevision-norevisions' => 'Aucune modification indiquée à supprimer.',
'hiderevision-noreason' => 'Vous devez indiquer la raison précise de cette suppression.',
'hiderevision-status' => 'Modification $1: $2',
'hiderevision-success' => 'Archivé et supprimé avec succès.',
'hiderevision-error-missing' => 'Non trouvé dans la base de donnée.',
'hiderevision-error-current' => 'Ne peux supprimer la dernière révision dans une page. Faites une annulation d\'édition auparavant.',
'hiderevision-error-delete' => 'Ne peut être archivé ; A-t-elle été déjà supprimée ?',
'hiderevision-archive-status' => 'Modification supprimée de $1: $2',
// Logging
'oversight-log-hiderev' => 'a supprimé une édition de $1',
// Oversight review page
'oversight' => 'Oversight',
'oversight-view' => 'détails',
),
	
	);
}

?>