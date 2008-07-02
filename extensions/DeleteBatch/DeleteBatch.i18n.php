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
