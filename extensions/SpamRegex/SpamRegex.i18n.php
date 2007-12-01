<?php
/**
 * Internationalisation file for extension SpamRegex.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'spamregex' => 'SpamRegex',
	'spamregex_summary' => 'The text was found in the article\'s summary.',
	'spamregex-intro' => 'Use this form to effectively block expressions from saving into a page\'s text. If the text contains the given expression, change would not be saved and an explanation will be displayed to user that tried to save the page. Caution advised, expressions should not be too short or too common.',
	'spamregex-page-title' => 'Spam Regex Unwanted Expressions Block',
	'spamregex-currently-blocked' => "'''Currently blocked phrases:'''",
	'spamregex-no-currently-blocked' => "'''There are no blocked phrases.'''",
	'spamregex-log-1' => '* \'\'\'$1\'\'\' $2 ([{{SERVER}}$3&text=$4 remove]) added by ',
	'spamregex-log-2' => ' on $1',
	'spamregex-page-title-1' => 'Block phrase using regular expressions',
	'spamregex-unblock-success' => 'Unblock succedeed',
	'spamregex-unblock-message' => 'Phrase \'\'\'$1\'\'\' has been unblocked from editing.',
	'spamregex-page-title-2' => 'Block phrases from saving using regular expressions',
	'spamregex-block-success' => 'Block succedeed',
	'spamregex-block-message' => 'Phrase \'\'\'$1\'\'\' has been blocked.',
	'spamregex-warning-1' => 'Give a phrase to block.',
	'spamregex-error-1' =>'Invalid regular expression.',
	'spamregex-warning-2' => 'Please check at least one blocking mode.',
	'spamregex-already-blocked' => '"$1" is already blocked',
	'spamregex-phrase-block' => 'Phrase to block:',
	'spamregex-phrase-block-text' => 'block phrase in article text',
	'spamregex-phrase-block-summary' => 'block phrase in summary',
	'spamregex-block-submit' => 'Block&nbsp;this&nbsp;phrase',
	'spamregex-text' => '(Text)',
	'spamregex-summary-log' => '(Summary)',
);

$messages ['fr'] = array(
	'spamregex' => 'Expressions régulières de Spams',
	'spamregex_summary' => 'Le texte en question a été détecté dans le commentaire de la page.',
	'spamregex-intro' => 'Utilisez ce formulaire pour bloquer effectivement les expressions pouvant être sauvegardées dans une page texte. Si le texte contient les expressions définies, les changements ne pourront être sauvegardés et un motif explicatif sera affiché à l’utilisateur qui a voulu sauvegarder la page. Il est important de prendre en considération que les expressions ne devront être ni trop longues ni trop courantes.',
	'spamregex-page-title' => 'Blocage des expressions régulières de spams',
	'spamregex-currently-blocked' => "'''Phrases actuellement bloquées :'''",
	'spamregex-no-currently-blocked' => "'''Il n’y a aucune phrase bloquée.'''",
	'spamregex-log-1' => '* \'\'\'$1\'\'\' $2 ([{{SERVER}}$3&text=$4 supprimer]) ajouté par ',
	'spamregex-log-2' => ' le $1',
	'spamregex-page-title-1' => 'Blocage d’une phrase utilisant des expressions régulières',
	'spamregex-unblock-success' => 'Le déblocage a réussi',
	'spamregex-unblock-message' => 'La phrase \'\'\'$1\'\'\' a été débloquée à l’édition.',
	'spamregex-page-title-2' => 'Blocage des phrases en utilisant des expression régulières',
	'spamregex-block-success' => 'Le blocage a réussi',
	'spamregex-block-message' => 'La phrase \'\'\'$1\'\'\' a été bloquée.',
	'spamregex-warning-1' => 'Indiquez une phrase à bloquer.',
	'spamregex-error-1' =>'Expression régulière invalide.',
	'spamregex-warning-2' => 'Choisissez au moins un mode de blocage.',
	'spamregex-already-blocked' => '« $1 » est déjà bloqué',
	'spamregex-phrase-block' => 'Phrase à bloquer :',
	'spamregex-phrase-block-text' => 'bloquer la phrase dans le texte de l’article',
	'spamregex-phrase-block-summary' => 'bloquer la phrase dans le commentaire',
	'spamregex-block-submit' => 'Bloquer&nbsp;cette&nbsp;phrase',
	'spamregex-text' => '(Texte)',
	'spamregex-summary-log' => '(Commentaire)',
);
