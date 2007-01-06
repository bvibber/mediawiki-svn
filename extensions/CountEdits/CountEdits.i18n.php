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
'countedits-warning' => "'''Warning:''' Do not judge a book by its cover. Do not judge a contributor by their edit count.",
'countedits-username' => 'Username:',
'countedits-ok' => 'OK',
'countedits-nosuchuser' => 'There is no user with the name $1.',
'countedits-resultheader' => 'Results for $1',
'countedits-resulttext' => '$1 has made $2 edits',
'countedits-mostactive' => 'Most active contributors',
'countedits-nocontribs' => 'There have been no contributions to this wiki.',
),

/* German (Raymond) */
'de' => array(
'countedits' => 'Beitragszähler',
'countedits-warning' => 'Hinweis: Sie beurteilen ein Buch nicht nur nach seinem Umschlag, beurteilen Sie einen Autor daher auch nicht nur nach seinem Beitragszähler.',
'countedits-username' => 'Benutzername:',
'countedits-ok' => 'OK',
'countedits-nosuchuser' => 'Es gibt keinen Benutzer mit dem Namen $1.',
'countedits-resultheader' => 'Ergebnis für $1',
'countedits-resulttext' => '$1 hat $2 Bearbeitungen',
'countedits-mostactive' => 'Die aktivsten Benutzer',
'countedits-nocontribs' => 'In {{ns:project}} sind keine Bearbeitungen vorhanden.',
),

/* Finnish (Niklas Laxström) */
'fi' => array(
'countedits' => 'Muokkausmäärälaskuri',
'countedits-warning' => 'Älä arvioi kirjaa kannen perusteella. Älä arvioi käyttäjää muokkausten lukumäärän perusteella.',
'countedits-username' => 'Käyttäjä:',
'countedits-ok' => 'Hae',
'countedits-nosuchuser' => 'Käyttäjää $1 ei ole.',
'countedits-resultheader' => 'Tulos:',
'countedits-resulttext' => '$1 on tehnyt $2 muokkausta.',
'countedits-mostactive' => 'Aktiivisimmat käyttäjät',
'countedits-nocontribs' => 'Tätä wikiä ei ole muokattu.',
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
'countedits-mostactive' => 'Contributeurs les plus actifs',
'countedits-nocontribs' => 'Ils n\'ont eu aucune contribution sur ce wiki.',
),

/* Indonesian (Ivan Lanin) */
'id' => array(
'countedits' => 'Jumlah suntingan',
'countedits-warning' => 'Peringatan: Jangan menilai suatu buku dari sampulnya. Jangan menilai seorang kontributor berdasarkan jumlah suntingannya.',
'countedits-username' => 'Nama pengguna:',
'countedits-ok' => 'OK',
'countedits-nosuchuser' => 'Tidak ada pengguna dengan nama $1.',
'countedits-resultheader' => 'Hasil untuk $1',
'countedits-resulttext' => '$1 telah membuat $2 suntingan',
'countedits-mostactive' => 'Kontributor paling aktif',
'countedits-nocontribs' => 'Belum ada kontribusi untuk wiki ini.',
),

	);
	return $single ? $messages['en'] : $messages;
}

?>
