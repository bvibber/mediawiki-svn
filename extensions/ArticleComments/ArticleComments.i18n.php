<?php
/**
 * Internationalisation file for ArticleComments extension.
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'article-comments-desc' => 'Enables comment sections on content pages',
	'article-comments-title-string' => 'title',
	'article-comments-name-string' => 'Name',
	'article-comments-name-field' => 'Name (required):',
	'article-comments-url-field' => 'Website:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Comment',
	'article-comments-comment-field' => 'Comment:',
	'article-comments-submit-button' => 'Submit',
	'article-comments-leave-comment-link' => 'Leave a comment ...',
	'article-comments-invalid-field' => 'The $1 provided <nowiki>[$2]</nowiki> is invalid.',
	'article-comments-required-field' => '$1 field is required.',
	'article-comments-submission-failed' => 'Comment submission failed',
	'article-comments-failure-reasons' => 'Sorry, your comment submission failed for the following reason(s):',
	'article-comments-no-comments' => 'Sorry, the page "[[$1]]" is not accepting comments at this time.',
	'article-comments-talk-page-starter' => "<noinclude>Comments on [[$1]]\n<comments />\n----- __NOEDITSECTION__</noinclude>\n",
	'article-comments-commenter-said' => '$1 said ...',
	'article-comments-summary' => 'Comment provided by $1 - via ArticleComments extension',
	'article-comments-submission-succeeded' => 'Comment submission succeeded',
	'article-comments-submission-success' => 'You have successfully submitted a comment for "[[$1]]"',
	'article-comments-submission-view-all' => 'You may view [[$1|all comments on that page]]',
	'article-comments-prefilled-comment-text' => '',
	'article-comments-user-is-blocked' => 'Your user account is currently blocked from editing "[[$1]]".',
	'article-comments-new-comment-heading' => "\n== {{int:article-comments-commenter-said|\$1}} ==\n\n",
	'article-comments-comment-bad-mode' => 'Invalid mode given for comment.
Available ones are "plain", "normal" and "wiki".',
	'article-comments-comment-contents' => "<div class='commentBlock'><small>$4</small>$5--\$3</div>\n",
	'article-comments-comment-missing-name-parameter' => 'Missing name',
	'article-comments-comment-missing-date-parameter' => 'Missing comment date',
	'article-comments-no-spam' => 'At least one of the submitted fields was flagged as spam.',
	'processcomment' => 'Process article comment',
);

/** Message documentation (Message documentation) */
$messages['qqq'] = array(
	'article-comments-invalid-field' => 'Shown as a list below article-comments-failure-reasons. With $1 being article-comments-title-string or article-comments-url-string messages, and $2 the wrong value.',
	'article-comments-required-field' => 'Shown as a list below article-comments-failure-reasons. With $1 being one of article-comments-*-string messages.',
	'article-comments-submission-failed' => 'Page title when there are errors in the comment submission',
	'article-comments-talk-page-starter' => 'Keep the wikisyntax as is.',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'article-comments-desc' => 'Maak kommentaar-afdelings op artikel-bladsye beskikbaar',
	'article-comments-title-string' => 'titel',
	'article-comments-name-string' => 'Naam',
	'article-comments-name-field' => 'Naam (verpligtend):',
	'article-comments-url-field' => 'Webwerf:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Kommentaar',
	'article-comments-comment-field' => 'Kommentaar:',
	'article-comments-submit-button' => 'Dien in',
	'article-comments-leave-comment-link' => "Los 'n opmerking...",
	'article-comments-invalid-field' => 'Die $1 verskafde <nowiki>[$2]</nowiki> is ongeldig.',
	'article-comments-required-field' => 'Die veld $1 is verpligtend.',
	'article-comments-submission-failed' => 'Indien van kommentaar het gefaal',
	'article-comments-failure-reasons' => 'Jammer, u kommentaar was om die volgende rede(s) onsuksesvol:',
	'article-comments-no-comments' => 'Jammer, die artikel "[[$1]]" aanvaar nie tans kommentaar nie.',
	'article-comments-talk-page-starter' => '<noinclude> Kommentaar op [[$1]] 
<comments />
 ----- __NOEDITSECTION__ </noinclude>',
	'article-comments-commenter-said' => '$1 het gesê...',
	'article-comments-summary' => 'Kommentaar deur $1 - via die ArticleComments-uitbreiding',
	'article-comments-submission-succeeded' => 'Indien van kommentaar was suksesvol',
	'article-comments-submission-success' => 'U het suksesvol \'n kommentaar vir "[[$1]]" ingedien',
	'article-comments-submission-view-all' => 'U kan al die antwoorde op hierdie artikel [[$1|hier]] sien',
	'article-comments-user-is-blocked' => 'U gebruiker is tans teen die redigering van "[[$1]]" geblokkeer.',
	'article-comments-comment-bad-mode' => 'Ongeldig modes is vir kommentaar verskaf.
Beskikbaar modusse is: "gewoon", "normaal" en "wiki".',
	'article-comments-comment-missing-name-parameter' => 'Naam ontbreek',
	'article-comments-comment-missing-date-parameter' => 'Geen datum vir kommentaar',
	'article-comments-no-spam' => 'Ten minste een van die voorgelegde velde is as spam gemerk.',
	'processcomment' => 'Verwerk kommentaar op artikel',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'article-comments-desc' => 'Activa os comentarios nas seccións dos artigos',
	'article-comments-title-string' => 'título',
	'article-comments-name-string' => 'Nome',
	'article-comments-name-field' => 'Nome (obrigatorio):',
	'article-comments-url-field' => 'Páxina web:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Comentario',
);

/** Russian (Русский)
 * @author MaxSem
 */
$messages['ru'] = array(
	'article-comments-name-string' => 'Имя',
	'article-comments-comment-string' => 'Комментарий',
	'article-comments-comment-field' => 'Комментарий:',
	'article-comments-leave-comment-link' => 'Написать комментарий...',
);

