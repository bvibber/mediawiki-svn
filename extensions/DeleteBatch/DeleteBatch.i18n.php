<?php
/**
 * Internationalization file for the DeleteBatch extension.
 *
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Bartek Łapiński
 */
$messages['en'] = array(
	'deletebatch' => 'Delete batch of pages',
	'deletebatch-desc' => '[[Special:DeleteBatch|Delete a batch of pages]]',
	'deletebatch-button' => 'DELETE', /* make it an irritably big button, on purpose, of course... */
	'deletebatch-here' => '<b>here</b>',
	'deletebatch-help' => 'Delete a batch of pages. You can either perform a single delete, or delete pages listed in a file.
Choose a user that will be shown in deletion logs.
Uploaded file should contain page name and optional reason separated by a "|" character in each line.',
	'deletebatch-caption' => 'Page list',
	'deletebatch-title' => 'Delete batch',
	'deletebatch-link-back' => 'Go back to the special page ',
	'deletebatch-as' => 'Run the script as',
	'deletebatch-both-modes' => 'Please choose either one specified page or a given list of pages.',
	'deletebatch-or' => '<b>OR</b>',
	'deletebatch-page' => 'Pages to be deleted',
	'deletebatch-reason' => 'Reason for deletion',
	'deletebatch-processing' => 'deleting pages ',
	'deletebatch-from-file' => 'from file list',
	'deletebatch-from-form' => 'from form',
	'deletebatch-success-subtitle' => 'for $1',
	'deletebatch-link-back' => 'You can go back to the extension ',
	'deletebatch-omitting-nonexistant' => 'Omitting non-existing page $1.',
	'deletebatch-omitting-invalid' => 'Omitting invalid page $1.',
	'deletebatch-file-bad-format' => 'The file should be plain text',
	'deletebatch-file-missing' => 'Unable to read given file',
	'deletebatch-select-script' => 'delete page script',
	'deletebatch-select-yourself' => 'you',
	'deletebatch-no-page' => 'Please specify at least one page to delete OR choose a file containing page list.',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'deletebatch-button'           => 'ИЗТРИВАНЕ',
	'deletebatch-here'             => '<b>тук</b>',
	'deletebatch-as'               => 'Стартиране на скрипта като',
	'deletebatch-or'               => '<b>ИЛИ</b>',
	'deletebatch-success-subtitle' => 'за $1',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'deletebatch-button'           => 'FORIGI',
	'deletebatch-here'             => '<b>ĉi tie</b>',
	'deletebatch-caption'          => 'Paĝlisto',
	'deletebatch-or'               => '<b>AŬ</b>',
	'deletebatch-from-file'        => 'de dosierlisto',
	'deletebatch-from-form'        => 'de paĝo',
	'deletebatch-success-subtitle' => 'por $1',
	'deletebatch-select-yourself'  => 'vi',
);

/** Finnish (Suomi)
 * @author Jack Phoenix
 */
$messages['fi'] = array(
	'deletebatch' => 'Poista useita sivuja',
	'deletebatch-button' => 'POISTA', /* make it an irritably big button, on purpose, of course... */
	'deletebatch-here' => '<b>täällä</b>',
	'deletebatch-help' => 'Poista useita sivuja. Voit joko tehdä yhden poiston tai poistaa tiedostossa listatut sivut. Valitse käyttäjä, joka näytetään poistolokeissa. Tallennetun tiedoston tulisi sisältää sivun nimi ja vapaaehtoinen syy | -merkin erottamina joka rivillä.',
	'deletebatch-caption' => 'Sivulista',
	'deletebatch-title' => 'Poista useita sivuja',
	'deletebatch-link-back' => 'Palaa toimintosivulle ',
	'deletebatch-as' => 'Suorita skripti käyttäjänä',
	'deletebatch-both-modes' => 'Valitse joko määritelty sivu tai annettu lista sivuista.',
	'deletebatch-or' => '<b>TAI</b>',
	'deletebatch-page' => 'Poistettavat sivut',
	'deletebatch-reason' => 'Poiston syy',
	'deletebatch-processing' => 'poistetaan sivuja ',
	'deletebatch-from-file' => 'tiedostolistasta',
	'deletebatch-from-form' => 'lomakkeesta',
	'deletebatch-link-back' => 'Voit palata lisäosaan ',
	'deletebatch-omitting-nonexistant' => 'Ohitetaan olematon sivu $1.',
	'deletebatch-omitting-invalid' => 'Ohitetaan kelpaamaton sivu $1.',
	'deletebatch-file-bad-format' => 'Tiedoston tulisi olla raakatekstiä',
	'deletebatch-file-missing' => 'Ei voi lukea annettua tiedostoa',
	'deletebatch-select-script' => 'sivunpoistoskripti',
	'deletebatch-select-yourself' => 'sinä',
	'deletebatch-no-page' => 'Määrittele ainakin yksi poistettava sivu TAI valitse tiedosto, joka sisältää sivulistan.',
);

/** French (Français)
 * @author Grondin
 */
$messages['fr'] = array(
	'deletebatch'                      => 'Lot de suppression des pages',
	'deletebatch-desc'                 => '[[Special:DeleteBatch|Supprime un lot de pages]]',
	'deletebatch-button'               => 'SUPPRIMER',
	'deletebatch-here'                 => '<b>ici</b>',
	'deletebatch-help'                 => 'Supprime un lot de pages. Vous pouvez soit lancer une simple suppression, soit supprimer des pages listées dans un fichier.
Choisissez un utilisateur qui sera affiché dans le journal des suppressions.
Un fichier importé pourra contenir un nom de la page et un motif facultatif séparé par un « | » dans chaque ligne.',
	'deletebatch-caption'              => 'Liste de la page',
	'deletebatch-title'                => 'Supprimer en lot',
	'deletebatch-link-back'            => 'Vous pouvez revenir à l’extension',
	'deletebatch-as'                   => 'Lancer le script comme',
	'deletebatch-both-modes'           => 'Veuillez choisir, soit une des pages indiquées, soit une liste donnée de pages.',
	'deletebatch-or'                   => '<b>OU</b>',
	'deletebatch-page'                 => 'Pages à supprimer',
	'deletebatch-reason'               => 'Motif de la suppression',
	'deletebatch-processing'           => 'suppression des pages',
	'deletebatch-from-file'            => 'depuis la liste d’un fichier',
	'deletebatch-from-form'            => 'à partir du formulaire',
	'deletebatch-success-subtitle'     => 'pour « $1 »',
	'deletebatch-omitting-nonexistant' => 'Omission de la page « $1 » inexistante.',
	'deletebatch-omitting-invalid'     => 'Omission de la page « $1 » incorrecte.',
	'deletebatch-file-bad-format'      => 'Le fichier doit être en texte simple',
	'deletebatch-file-missing'         => 'Impossible de lire le fichier donné',
	'deletebatch-select-script'        => 'supprimer le script de la page',
	'deletebatch-select-yourself'      => 'vous',
	'deletebatch-no-page'              => 'Veuillez indiquer au moins une page à supprimer OU un fichier donné contenant une liste de pages.',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'deletebatch'                      => 'Borrar un conxunto de páxinas',
	'deletebatch-desc'                 => '[[Special:DeleteBatch|Borrar un conxunto de páxinas]]',
	'deletebatch-button'               => 'BORRAR',
	'deletebatch-here'                 => '<b>aquí</b>',
	'deletebatch-help'                 => 'Borrar un conxunto de páxinas. Pode levar a cabo un borrado único ou borrar as páxinas listadas nun ficheiro.
Escolla o usuario que será amosado nos rexistros de borrado.
O ficheiro cargado debería conter o nome da páxina e unha razón opcional separados por un carácter de barra vertical ("|") en cada liña.',
	'deletebatch-caption'              => 'Lista da páxina',
	'deletebatch-title'                => 'Borrar un conxunto',
	'deletebatch-link-back'            => 'Pode voltar á extensión',
	'deletebatch-as'                   => 'Executar o guión como',
	'deletebatch-both-modes'           => 'Por favor, escolla unha páxina específica ou unha lista de páxinas dadas.',
	'deletebatch-or'                   => '<b>OU</b>',
	'deletebatch-page'                 => 'Páxinas para ser borradas',
	'deletebatch-reason'               => 'Razón para o borrado',
	'deletebatch-processing'           => 'borrando a páxina',
	'deletebatch-from-file'            => 'da lista de ficheiros',
	'deletebatch-from-form'            => 'do formulario',
	'deletebatch-success-subtitle'     => 'de $1',
	'deletebatch-omitting-nonexistant' => 'Omitindo a páxina $1, que non existe.',
	'deletebatch-omitting-invalid'     => 'Omitindo a páxina inválida $1.',
	'deletebatch-file-bad-format'      => 'O ficheiro debería ser un texto sinxelo',
	'deletebatch-file-missing'         => 'Non se pode ler o ficheiro dado',
	'deletebatch-select-script'        => 'borrar o guión dunha páxina',
	'deletebatch-select-yourself'      => 'vostede',
	'deletebatch-no-page'              => 'Por favor, especifique, polo menos, unha páxina para borrar OU escolla un ficheiro que conteña unha lista de páxinas.',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'deletebatch'                      => 'Rei vu Säite läschen',
	'deletebatch-desc'                 => '[[Special:DeleteBatch|Läscht eng Rei Säiten]]',
	'deletebatch-button'               => 'LÄSCHEN',
	'deletebatch-here'                 => '<b>hei</b>',
	'deletebatch-caption'              => 'Lëscht vun der Säit',
	'deletebatch-title'                => 'Zesumme läschen',
	'deletebatch-link-back'            => "Dir kënnt op d'Erweiderung zréckgoen",
	'deletebatch-or'                   => '<b>ODER</b>',
	'deletebatch-page'                 => 'Säite fir ze läschen',
	'deletebatch-reason'               => 'Grond fir ze läschen',
	'deletebatch-processing'           => "d'Säite gi geläscht",
	'deletebatch-from-file'            => 'vun der Lëscht vun engem Fichier',
	'deletebatch-from-form'            => 'vum Formulaire',
	'deletebatch-success-subtitle'     => 'fir $1',
	'deletebatch-omitting-nonexistant' => "D'Säit $1 déi et net gëtt iwwersprangen.",
	'deletebatch-omitting-invalid'     => 'Déi ongëlteg Säit $1 iwwersprangen.',
	'deletebatch-select-script'        => 'de Script vun der Säit läschen',
	'deletebatch-select-yourself'      => 'Dir',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'deletebatch'                      => 'Paginareeks verwijderen',
	'deletebatch-desc'                 => '[[Special:DeleteBatch|Paginareeks verwijderen]]',
	'deletebatch-button'               => 'VERWIJDEREN',
	'deletebatch-here'                 => '<b>hier</b>',
	'deletebatch-help'                 => 'Een lijst pagina\'s verwijderen.
U kunt een enkele pagina verwijderen of een lijst van pagina\'s in een bestand.
Kies een gebruiker die in het verwijderlogboek wordt genoemd.
Het bestand dat u uploadt moet op iedere regel een paginanaam en een reden bevatten (optioneel), gescheiden door het karakter "|".',
	'deletebatch-caption'              => 'Paginalijst',
	'deletebatch-title'                => 'Reeks verwijderen',
	'deletebatch-link-back'            => 'Teruggaan naar de uitbreiding',
	'deletebatch-as'                   => 'Script uitvoeren als',
	'deletebatch-both-modes'           => "Kies een bepaalde pagina of geef een list met pagina's op.",
	'deletebatch-or'                   => '<b>OF</b>',
	'deletebatch-page'                 => "Te verwijderen pagina's",
	'deletebatch-reason'               => 'Reden voor verwijderen',
	'deletebatch-processing'           => "bezig met het verwijderen van pagina's",
	'deletebatch-from-file'            => 'van een lijst uit een bestand',
	'deletebatch-from-form'            => 'uit het formulier',
	'deletebatch-success-subtitle'     => 'voor $1',
	'deletebatch-omitting-nonexistant' => 'Niet-bestaande pagina $1 is overgeslagen.',
	'deletebatch-omitting-invalid'     => 'Ongeldige paginanaam $1 is overgeslagen.',
	'deletebatch-file-bad-format'      => 'Het bestand moet platte tekst bevatten',
	'deletebatch-file-missing'         => 'Het bestnad kan niet gelezen worden',
	'deletebatch-select-script'        => "script pagina's verwijderen",
	'deletebatch-select-yourself'      => 'u',
	'deletebatch-no-page'              => "Geef tenminste één te verwijderen pagina op of kies een bestand dat de lijst met pagina's bevat.",
);

/** Polish (Polski)
 * @author Airwolf
 */
$messages['pl'] = array(
	'deletebatch-here'            => '<b>tutaj</b>',
	'deletebatch-select-yourself' => 'ty',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'deletebatch'                      => 'Zmazanie viacerých stránok',
	'deletebatch-desc'                 => '[[Special:DeleteBatch|Zmazanie viacerých stránok]]',
	'deletebatch-button'               => 'ZMAZAŤ',
	'deletebatch-here'                 => '<b>tu</b>',
	'deletebatch-help'                 => 'Zmazanie viacerých stránok. Môžete buď vykonať jedno zmazanie alebo zmazať stránky uvedené v súbore.
Vyberte, ktorý používateľ sa zobrazí v záznamoch zmazania.
Nahraný súbor by mal na každom riadku obsahovať názov stránky a nepovinne aj dôvod zmazania oddelený znakom „|”.',
	'deletebatch-caption'              => 'Zoznam stránok',
	'deletebatch-title'                => 'Zmazať dávku',
	'deletebatch-link-back'            => 'Môžete sa vrátiť späť na rozšírenie',
	'deletebatch-as'                   => 'Spustiť skript ako',
	'deletebatch-both-modes'           => 'Prosím, vyberte buď zadanú stránku alebo zadaý zoznam stránok.',
	'deletebatch-or'                   => '<b>ALEBO</b>',
	'deletebatch-page'                 => 'Stránky, ktoré budú zmazané',
	'deletebatch-reason'               => 'Dôvod zmazania',
	'deletebatch-processing'           => 'mažú sa stránky',
	'deletebatch-from-file'            => 'zo zoznamu v súbore',
	'deletebatch-from-form'            => 'z formulára',
	'deletebatch-success-subtitle'     => 'z $1',
	'deletebatch-omitting-nonexistant' => 'Vynecháva sa neexistujúca stránka $1.',
	'deletebatch-omitting-invalid'     => 'Vynecháva sa neplatná stránka $1.',
	'deletebatch-file-bad-format'      => 'Súbor by mal byť textovom formáte',
	'deletebatch-file-missing'         => 'Nebolo možné prečítať zadaný súbor',
	'deletebatch-select-script'        => 'skript na zmazanie stránok',
	'deletebatch-select-yourself'      => 'vy',
	'deletebatch-no-page'              => 'Prosím, zadajte aspoň jednu stránku, ktorá sa má zmazať ALEBO súbor obsahujúci zoznam stránok.',
);

/** Swedish (Svenska)
 * @author M.M.S.
 */
$messages['sv'] = array(
	'deletebatch'                      => 'Radera serier av sidor',
	'deletebatch-desc'                 => '[[Special:DeleteBatch|Radera en serie av sidor]]',
	'deletebatch-button'               => 'RADERA',
	'deletebatch-here'                 => '<b>här</b>',
	'deletebatch-help'                 => 'Radera en serie av sidor. Du kan också utföra en ensam radering, eller radera sidor listade i en fil.
Välj en användare som kommer att visas i raderingsloggen.
En uppladdad fil ska innehålla sidnamn och en valfri anledning separerade med ett "|"-tecken på varje rad.',
	'deletebatch-caption'              => 'Sidlista',
	'deletebatch-title'                => 'Radera serie',
	'deletebatch-link-back'            => 'Du kan gå tillbaka till tillägget',
	'deletebatch-as'                   => 'Kör skriptet som',
	'deletebatch-both-modes'           => 'Var god välj antingen en specifierad sida eller en lista över sidor.',
	'deletebatch-or'                   => '<b>ELLER</b>',
	'deletebatch-page'                 => 'Sidor som ska raderas',
	'deletebatch-reason'               => 'Anledning för radering',
	'deletebatch-processing'           => 'raderar sidor',
	'deletebatch-from-file'            => 'från fillistan',
	'deletebatch-from-form'            => 'från formulär',
	'deletebatch-success-subtitle'     => 'för $1',
	'deletebatch-omitting-nonexistant' => 'Utelämna ej existerande sida $1.',
	'deletebatch-omitting-invalid'     => 'Utelämna ogiltig sida $1.',
	'deletebatch-file-bad-format'      => 'Filen ska innehålla ren text',
	'deletebatch-file-missing'         => 'Kan inte läsa filen',
	'deletebatch-select-script'        => 'radera sidskript',
	'deletebatch-select-yourself'      => 'du',
	'deletebatch-no-page'              => 'Var god specifiera minst en sida för att radera ELLER välj en fil innehållande en sidlista.',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'deletebatch-caption' => 'పేజీల జాబితా',
	'deletebatch-or'      => '<b>లేదా</b>',
	'deletebatch-page'    => 'తొలగించాల్సిన పేజీలు',
	'deletebatch-reason'  => 'తొలగింపునకు కారణం',
);

