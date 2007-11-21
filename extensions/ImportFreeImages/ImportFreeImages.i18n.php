<?php
/*Internationalization of ImportFreeImages extension.*/

function efImportFreeImagesMessages() {
	$messages = array (
		/*English (ravis Derouin)*/
		'en' => array (
			'importfreeimages'                => 'Import Free Images',
			'importfreeimages_description'    => 'This page allows you to search properly licensed photos from flickr and import them into your wiki.',	
			'importfreeimages_noapikey'       => 'You have not configured your Flickr API Key. To do so, please obtain a API key from  [http://www.flickr.com/services/api/misc.api_keys.html here] and set wgFlickrAPIKey in ImportFreeImages.php.',
			'importfreeimages_nophotosfound'  => 'No photos were found for your search criteria \'$1\', please try again.',
			'importfreeimages_owner'          => 'Author',
			'importfreeimages_importthis'     => 'import this',
			'importfreeimages_next'           => 'Next $1',
			'importfreeimages_filefromflickr' => '$1 by user <b>[$2]</b> from flickr. Original URL',
			'importfreeimages_promptuserforfilename' => 'Please enter a destination filename:',
			'importfreeimages_returntoform'   => 'Or, click <a href=\'$1\'>here</a> to return to your search results',
		),

		'de' => array(
			'importfreeimages'                => 'Import freier Bilder',
			'importfreeimages_description'    => 'Diese Seite erlaubt dir, auf Flickr nach Bildern unter einer freien Lizenz zu suchen und diese in dein Wiki zu importieren.',
			'importfreeimages_noapikey'       => 'Du hast noch keinen Flickr-API-Schlüssel konfiguriert. Bitte beantrage ihn [http://www.flickr.com/services/api/misc.api_keys.html hier] und setze ihn in $wgFlickrAPIKey in ImportFreeImages.php ein.',
			'importfreeimages_nophotosfound'  => 'Es wurden keine Fotos mit den Suchkriterien „$1“ gefunden.',
			'importfreeimages_owner'          => 'Autor',
			'importfreeimages_importthis'     => 'importieren',
			'importfreeimages_next'           => 'Nächste $1',
			'importfreeimages_filefromflickr' => '$1 von Benutzer <b>[$2]</b> von flickr. Original URL',
			'importfreeimages_promptuserforfilename' => 'Bitte gebe einen Ziel-Dateinamen ein:',
			'importfreeimages_returntoform'   => 'Oder klicke <a href=\'$1\'>hier</a>, um zu der Seite mit den Suchergebnissen zurückzukommen.',
		),

		/*French (Bertrand GRONDIN)*/
		'fr' => array(
			'importfreeimages'                => 'Importer des Images Libres',
			'importfreeimages_description'    => 'Cette page vous permet de rechercher proprement des images sous licences depuis flickr et de les importer dans votre wiki.',
			'importfreeimages_noapikey'       => 'Vous n’avez pas configuré votre Clef API Flickr. Pour ce faire, vous êtes prié d’obtenir une clef API à partir de [http://www.flickr.com/services/api/misc.api_keys.html ce lien] et de configurer wgFlickrAPIKey dans ImportFreeImages.php.',
			'importfreeimages_nophotosfound'  => 'Aucune photo n’a été trouvée à partir de vos critères de recherches  \'$1\', veuillez essayer à nouveau.',
			'importfreeimages_owner'          => 'Auteur',
			'importfreeimages_importthis'     => 'l’importer',
			'importfreeimages_next'           => ' $1 suivants',
			'importfreeimages_filefromflickr' => '$1 par l’utilisateur <b>[$2]</b> depuis flickr. URL d’origine ',
			'importfreeimages_promptuserforfilename' => 'Veuillez indiquer le nom du fichier de destination : ',
			'importfreeimages_returntoform'   => 'ou, cliquez <a href=\'$1\'>ici</a> pour revenir à votre liste de résultats.',
		),
	);
	return $messages;
}
