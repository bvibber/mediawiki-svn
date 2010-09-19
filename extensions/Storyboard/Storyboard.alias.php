<?php
/**
 * Aliases for the special pages of the Storyboard extension.
 *
 * @file Storyboard.alias.php
 * @ingroup Storyboard
 *
 * @author Jeroen De Dauw
 */

$specialPageAliases = array();

/** English
 * @author Jeroen De Dauw
 */
$specialPageAliases['en'] = array(
	'StoryReview' => array( 'StoryReview' ),
	'Story' => array( 'Story' ),
	'StorySubmission' => array( 'StorySubmission' ),
);

/** Arabic (العربية) */
$specialPageAliases['ar'] = array(
	'StoryReview' => array( 'مراجعة_القصة' ),
	'Story' => array( 'قصة' ),
	'StorySubmission' => array( 'إرسال_القصة' ),
);

/** Breton (Brezhoneg) */
$specialPageAliases['br'] = array(
	'Story' => array( 'Istor' ),
);

/** Luxembourgish (Lëtzebuergesch) */
$specialPageAliases['lb'] = array(
	'StoryReview' => array( 'Annekdot_nokucken' ),
	'Story' => array( 'Annekdot' ),
	'StorySubmission' => array( 'Annekdot_aschécken' ),
);

/** Malayalam (മലയാളം) */
$specialPageAliases['ml'] = array(
	'StoryReview' => array( 'കഥസംശോധനം' ),
	'Story' => array( 'കഥ' ),
	'StorySubmission' => array( 'കഥസമർപ്പിക്കൽ' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'StoryReview' => array( 'Verhaalcontrole' ),
	'Story' => array( 'Verhaal' ),
	'StorySubmission' => array( 'VerhaalInzenden' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;