<?php

/**
 * Internationalisation file for CountEdits extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */
 
function efCountEditsMessages( $single = false ) {
	$messages = array(
	
/* English (Rob Church) */
'en' => array(
'countedits' => 'Count edits',
'countedits-warning' => 'Warning: Do not judge a book by its cover. Do not judge a contributor by their edit count.',
'countedits-username' => 'Username:',
'countedits-ok' => 'OK',
'countedits-nosuchuser' => 'There is no user with the name $1.',
'countedits-resultheader' => 'Results for $1',
'countedits-resulttext' => '$1 has made $2 edits',
'countedits-mostactive' => 'Most active contributors',
'countedits-nocontribs' => 'There have been no contributions to this wiki.',
),

/* French (Bertrand Grondin) */
'fr' => array(
'countedits' => 'Compteur d\'éditions',
'countedits-warning' => 'Avertissement : ne jugez pas un livre par sa couverture. Ne jugez pas non plus un utilisateur en fonction du nombre de ses contributions.',
'countedits-username' => 'Utilisateur',
'countedits-ok' => 'OK',
'countedits-nosuchuser' => 'Il n\'y a aucun utilisateur correspondant à $1',
'countedits-resultheader' => 'Resultats pour $1',
'countedits-resulttext' => '$1 a fait $2 éditions',
'countedits-userpage' => 'Page Utilisateur',
'countedits-usertalk' => 'Page de discussion',
'countedits-contribs' => 'Contributions',
'countedits-mostactive' => 'Contributeurs les plus actifs',
),
	
	);
	return $single ? $messages['en'] : $messages;
}

?>
