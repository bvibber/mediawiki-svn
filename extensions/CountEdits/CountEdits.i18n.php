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
'countedits' => 'Compteur d’éditions',
'countedits-warning' => 'Avertissement : ne jugez pas un livre par sa couverture. Ne jugez pas non plus un utilisateur en fonction du nombre de ses contributions.',
'countedits-username' => 'Utilisateur',
'countedits-ok' => 'OK',
'countedits-nosuchuser' => 'Il n’y a aucun utilisateur correspondant à $1',
'countedits-resultheader' => 'Resultats pour $1',
'countedits-resulttext' => '$1 a fait $2 éditions',
'countedits-mostactive' => 'Contributeurs les plus actifs',
'countedits-nocontribs' => 'Aucune contribution sur ce wiki.',
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

/* Portuguese (Lugusto) */
'pt' => array(
'countedits' => 'Contador de edições',
'countedits-warning' => "'''Atenção:''' Não julgue um livro pela sua capa. Não julgue um contribuidor pela contagem de suas edições.",
'countedits-username' => 'Utilizador:',
'countedits-ok' => 'Ok',
'countedits-nosuchuser' => 'Não foi encontrado um utilizador com o nome $1.',
'countedits-resultheader' => 'Resultados para $1',
'countedits-resulttext' => '$1 fez $2 edições',
'countedits-mostactive' => 'Contribuidores mais activos',
'countedits-nocontribs' => 'Não possui contribuições neste wiki.',
),

/* Serbian default (Sasa Stefanovic) */
'sr' => array(
'countedits' => 'Бројач измена',
'countedits-warning' => "'''Упозорење:''' Не судите о књизи по њеном омоту. Не судите о кориснику по његовом броју измена.",
'countedits-username' => 'Корисник:',
'countedits-ok' => 'У реду',
'countedits-nosuchuser' => 'Не постоји корисник са именом $1.',
'countedits-resultheader' => 'Резултати за $1',
'countedits-resulttext' => '$1 има $2 измена',
'countedits-mostactive' => 'Најактивнији корисници',
'countedits-nocontribs' => 'Не постоје прилози на овој вики.',
),

/* Serbian cyrillic (Sasa Stefanovic) */
'sr-ec' => array(
'countedits' => 'Бројач измена',
'countedits-warning' => "'''Упозорење:''' Не судите о књизи по њеном омоту. Не судите о кориснику по његовом броју измена.",
'countedits-username' => 'Корисник:',
'countedits-ok' => 'У реду',
'countedits-nosuchuser' => 'Не постоји корисник са именом $1.',
'countedits-resultheader' => 'Резултати за $1',
'countedits-resulttext' => '$1 има $2 измена',
'countedits-mostactive' => 'Најактивнији корисници',
'countedits-nocontribs' => 'Не постоје прилози на овој вики.',
),

/* Serbian latin (Sasa Stefanovic) */
'sr-el' => array(
'countedits' => 'Brojač izmena',
'countedits-warning' => "'''Upozorenje:''' Ne sudite o knjizi po njenom omotu. Ne sudite o korisniku po njegovom broju izmena.",
'countedits-username' => 'Korisnik:',
'countedits-ok' => 'U redu',
'countedits-nosuchuser' => 'Ne postoji korisnik sa imenom $1.',
'countedits-resultheader' => 'Rezultati za $1',
'countedits-resulttext' => '$1 ima $2 izmena',
'countedits-mostactive' => 'Najaktivniji korisnici',
'countedits-nocontribs' => 'Ne postoje prilozi na ovoj viki.',
),

	);
	return $single ? $messages['en'] : $messages;
}

?>
