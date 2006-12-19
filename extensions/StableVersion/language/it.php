<?php
/**
 * Italian language file for the 'StableVersion' extension
 */

// We will add messages to the global cache
global $wgMessageCache;

// Add messages
$wgMessageCache->addMessages(
	array(
			'stableversion_this_is_stable' => 'Questa  la versione consolidata della voce.  possibile consultarne la <a href="$1">bozza pi recente</a>.',
			'stableversion_this_is_stable_nourl' => 'Questa  la versione consolidata della voce.',
			'stableversion_this_is_draft_no_stable' => 'Questa  una bozza della voce. Al momento non  disponibile una versione consolidata.',
			'stableversion_this_is_draft' => 'Questa  una bozza della voce.  possibile consultarne la <a href="$1">versione consolidata</a>.',
			'stableversion_this_is_old' => 'Questa  una vecchia versione della voce.  possibile consultarne la <a href="$1">versione consolidata</a> e la <a href="$2">bozza pi recente</a>.',
			'stableversion_reset_stable_version' => 'Fare clic <a href="$1">qui</a> per rimuovere l\'indicazione di versione consolidata.',
			'stableversion_set_stable_version' => 'Fare clic <a href="$1">qui</a> per impostare l\'indicazione di versione consolidata.',
			'stableversion_set_ok' => 'L\'indicazione di versione consolidata  stata impostata.',
			'stableversion_reset_ok' => 'L\'indicazione di versione consolidata  stata rimossa. La voce  al momento priva di versione consolidata.',
			'stableversion_return' => 'Ritorna a <a href="$1">$2</a>',
			
			'stableversion_reset_log' => 'L\'indicazione di versione consolidata  stata eliminata.',
			'stableversion_logpage' => 'Registro delle versioni consolidate',
			'stableversion_logpagetext' => 'Di seguito viene presentato il registro delle modifiche alle versioni consolidate',
			'stableversion_logentry' => '',
			'stableversion_log' => 'L\'indicazione di versione consolidata  stata impostata alla revisione #$1.',
			'stableversion_before_no' => 'Nessuna versione era indicata come consolidata in precedenza.',
			'stableversion_before_yes' => 'La precedente versione indicata come consolidata era #$1.',
			'stableversion_this_is_stable_and_current' => 'Questa  la versione consolidata e anche la pi recente.',
			'stableversion_noset_directional' => '(Impossibile modificare l\'impostazione nella cronologia direzionale)',
	)
);

?>
