<?php
/**
 * Internationalisation file for YouTubeAuthSub extension.
 *
 * @addtogroup Extensions
 */

$messages = array();

/** English
 * @author Travis Derouin
 */
$messages['en'] = array(
	'youtubeauthsub'                     => 'Upload YouTube Video',
	'youtubeauthsub-desc'                => 'Allows users to [[Special:YouTubeAuthSub|upload videos]] directly to YouTube',
	'youtubeauthsub_info'                => "To upload a video to YouTube to include on a page, fill out the following information:",
	'youtubeauthsub_title'               => 'Title',
	'youtubeauthsub_description'         => 'Description',
	'youtubeauthsub_password'            => "YouTube Passsword",
	'youtubeauthsub_username'            => "YouTube Username",
	'youtubeauthsub_keywords'            => 'Keywords',
	'youtubeauthsub_category'            => 'Category',
	'youtubeauthsub_submit'              => 'Submit',
	'youtubeauthsub_clickhere'           => 'Click here to log in to YouTube',
	'youtubeauthsub_tokenerror'          => 'Error generating authorization token, try refreshing.',
	'youtubeauthsub_success'             => "Congratulations!
Your video is uploaded.
To view your video click <a href='http://www.youtube.com/watch?v=$1'>here</a>.
YouTube may require some time to process your video, so it might not be ready just yet.

To include your video in a page on the wiki, insert the following code into a page:
<code>{{&#35;ev:youtube|$1}}</code>",
	'youtubeauthsub_authsubinstructions' => "To upload a video, you will be required to first log in to YouTube.",
	'youtubeauthsub_uploadhere'          => "Upload your video from here:",
	'youtubeauthsub_uploadbutton'        => 'Upload',
	'youtubeauthsub_code'                => '{{#ev:youtube|$1}}.

This video can be viewed [http://www.youtube.com/watch?v=$1 here]',
	'youtubeauthsub_summary'             => 'Uploading YouTube video',
	'youtubeauthsub_uploading'           => 'Your video is being uploaded.
Please be patient.',
	'youtubeauthsub_viewpage'            => 'Alternatively, you can view your video [[$1|here]].',
	'youtubeauthsub_jserror_nokeywords'  => 'Please enter 1 or more keywords.',
	'youtubeauthsub_jserror_notitle'     => 'Please enter a title for the video.',
	'youtubeauthsub_jserror_nodesc'      => 'Please enter a description for the video.',
);

/** French (Français)
 * @author Grondin
 */
$messages['fr'] = array(
	'youtubeauthsub'                     => 'Importer une vidéo YouTube',
	'youtubeauthsub-desc'                => "Permet aux utilisateurs de [[Special:YouTubeAuthSub|d'importer des vidéos]] directement sur YouTube",
	'youtubeauthsub_info'                => "Pour importer une vidéo sur YouTube pour l'incorporer dans une page, renseignez les informations suivantes :",
	'youtubeauthsub_title'               => 'Titre',
	'youtubeauthsub_description'         => 'Description',
	'youtubeauthsub_password'            => 'Mot de passe sur YouTube',
	'youtubeauthsub_username'            => 'Nom d’utilisateur sur YouTube',
	'youtubeauthsub_keywords'            => 'Mots clefs',
	'youtubeauthsub_category'            => 'Catégorie',
	'youtubeauthsub_submit'              => 'Soumettre',
	'youtubeauthsub_clickhere'           => 'Cliquez ici pour vous connecter sur YouTube',
	'youtubeauthsub_tokenerror'          => 'Erreur dans la prise de la création d’autorisation, essayer de raffraichir la page.',
	'youtubeauthsub_success'             => "Félicitations :
Votre vidéo est importée.
Pour visionner votre vidéo cliquez <a href='http://www.youtube.com/watch?v=$1'>ici</a>.
YouTube peut demander un laps de temps pour prendre en compte votre vidéo, aussi il peut ne pas être encore prêt.

Pour incorporer votre vidéo dans une page du wiki, insérer le code suivant dans celle-ci :
<code>{{&#35;ev:youtube|$1}}</code>",
	'youtubeauthsub_authsubinstructions' => 'Pour importer une vidéo, il vous sera demandé de vous connecter d’abord sur YouTube.',
	'youtubeauthsub_uploadhere'          => 'Importer votre vidéo depuis ici :',
	'youtubeauthsub_uploadbutton'        => 'Importer',
	'youtubeauthsub_code'                => '{{#ev:youtube|$1}}.

Cette vidéo peut être visionnée [http://www.youtube.com/watch?v=$1 ici].',
	'youtubeauthsub_summary'             => 'Importer une vidéo YouTube',
	'youtubeauthsub_uploading'           => 'Votre vidéo est en cours d’importation.
Soyez patient.',
	'youtubeauthsub_viewpage'            => 'Sinon, vous pouvez visionner votre vidéo [[$1|ici]].',
	'youtubeauthsub_jserror_nokeywords'  => 'Vous être prié d’entrer un ou plusieurs mots clefs.',
	'youtubeauthsub_jserror_notitle'     => 'Vous être prié d’entrer un titre pour la vidéo.',
	'youtubeauthsub_jserror_nodesc'      => 'Veuiller entrer une description pour la vidéo.',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'youtubeauthsub'                     => 'YouTube-video uploaden',
	'youtubeauthsub-desc'                => "Laat gebruikers direct [[Special:YouTubeAuthSub|video's uploaden]] naar YouTube",
	'youtubeauthsub_info'                => 'Geef de volgende informatie op om een video naar YouTube te uploaden om die later aan een pagina te kunnen toevoegen:',
	'youtubeauthsub_title'               => 'Naam',
	'youtubeauthsub_description'         => 'Beschrijving',
	'youtubeauthsub_password'            => 'Wachtwoord YouTube',
	'youtubeauthsub_username'            => 'Gebruiker YouTube',
	'youtubeauthsub_keywords'            => 'Trefwoorden',
	'youtubeauthsub_category'            => 'Categorie',
	'youtubeauthsub_submit'              => 'Uploaden',
	'youtubeauthsub_clickhere'           => 'Klik hier om aan te melden bij YouTube',
	'youtubeauthsub_tokenerror'          => 'Fout bij het maken van het autorisatietoken. Vernieuw de pagina.',
	'youtubeauthsub_success'             => "Gefeliciteerd!
Uw video is geüpload.
Klik <a href='http://www.youtube.com/watch?v=$1'>hier</a> om uw video te bekijken.
Het komt voor dat YouTube enige tijd nodig heeft om uw video te verwerken, dus wellicht is die nog niet beschikbaar.

Voeg de volgende code toe om uw video in een pagina op te nemen:
<code>{{&#35;ev:youtube|$1}}</code>",
	'youtubeauthsub_authsubinstructions' => "Meld u eerst aan bij YouTube voordat u video's gaat uploaden.",
	'youtubeauthsub_uploadhere'          => 'Uw video van hier uploaden:',
	'youtubeauthsub_uploadbutton'        => 'Uploaden',
	'youtubeauthsub_code'                => '{{#ev:youtube|$1}}.

U kunt deze video [http://www.youtube.com/watch?v=$1 hier] bekijken',
	'youtubeauthsub_summary'             => 'Bezig met uploaden van de YouTube video',
	'youtubeauthsub_uploading'           => 'Uw video wordt geüpload.
Even geduld alstublieft.',
	'youtubeauthsub_viewpage'            => 'U kunt uw video ook [[$1|hier]] bekijken.',
	'youtubeauthsub_jserror_nokeywords'  => 'Geef alstublieft een of meer trefwoorden op.',
	'youtubeauthsub_jserror_notitle'     => 'Geef alstublieft een naam voor de video op.',
	'youtubeauthsub_jserror_nodesc'      => 'Geef alstublieft een beschrijving voor de video op.',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'youtubeauthsub'                     => 'Importar una vidèo YouTube',
	'youtubeauthsub-desc'                => "Permet als utilizaires de [[Special:YouTubeAuthSub|d'importar de vidèos]] dirèctament sus YouTube",
	'youtubeauthsub_info'                => "Per importar una vidèo sus YouTube per l'incorporar dins una pagina, entresenhatz las informacions seguentas :",
	'youtubeauthsub_title'               => 'Títol',
	'youtubeauthsub_description'         => 'Descripcion',
	'youtubeauthsub_password'            => 'Senhal sus YouTube',
	'youtubeauthsub_username'            => 'Nom d’utilizaire sus YouTube',
	'youtubeauthsub_keywords'            => 'Mots claus',
	'youtubeauthsub_category'            => 'Categoria',
	'youtubeauthsub_submit'              => 'Sometre',
	'youtubeauthsub_clickhere'           => 'Clicatz aicí per vos connectar sus YouTube',
	'youtubeauthsub_tokenerror'          => 'Error dins la creacion de la presa d’autorizacion, ensajatz de refrescar la pagina.',
	'youtubeauthsub_success'             => "Felicitacions :
Vòstra vidèo es importada.
Per visionar vòstra vidèo clicatz <a href='http://www.youtube.com/watch?v=$1'>aicí</a>.
YouTube pòt demandar un brieu de temps per prendre en compte vòstra vidèo, tanben, pòt èsser pas encara prèst.

Per incorporar vòstra vidèo dins una pagina del wiki, inserissètz lo còde seguent dins aquesta :
<code>{{&#35;ev:youtube|$1}}</code>",
	'youtubeauthsub_authsubinstructions' => 'Per importar una vidèo, vos serà demandat de vos connectar d’en primièr sus YouTube.',
	'youtubeauthsub_uploadhere'          => 'Importar vòstra vidèo dempuèi aicí :',
	'youtubeauthsub_uploadbutton'        => 'Importar',
	'youtubeauthsub_code'                => '{{#ev:youtube|$1}}.

Aquesta vidèo pòt èsser visionada [http://www.youtube.com/watch?v=$1 aicí].',
	'youtubeauthsub_summary'             => 'Importar una vidèo YouTube',
	'youtubeauthsub_uploading'           => 'Vòstra vidèo es en cors d’importacion.
Siatz pacient.',
	'youtubeauthsub_viewpage'            => 'Siquenon, podètz visionar vòstra vidèo [[$1|aicí]].',
	'youtubeauthsub_jserror_nokeywords'  => 'Mercés de picar un o mantuns mots claus.',
	'youtubeauthsub_jserror_notitle'     => 'Mercés de picar un títol per la vidèo.',
	'youtubeauthsub_jserror_nodesc'      => 'Picatz una descripcion per la vidèo.',
);

/** Polish (Polski)
 * @author Wpedzich
 */
$messages['pl'] = array(
	'youtubeauthsub_keywords'            => 'Słowa kluczowe',
	'youtubeauthsub_category'            => 'Kategoria',
	'youtubeauthsub_submit'              => 'Prześlij',
	'youtubeauthsub_clickhere'           => 'Kliknij, by zalogować się do serwisu YouTube',
	'youtubeauthsub_tokenerror'          => 'Podczas generowania tokenu uwierzytelniającego wystąpił błąd. Spróbuj załadować stronę jeszcze raz.',
	'youtubeauthsub_success'             => "Gratulacje!
Twój plik wideo został przesłany.
Jeśli chcesz obejrzeć przesłany materiał wideo, kliknij <a href='http://www.youtube.com/watch?v=$1'>tutaj</a>.
Serwis YouTube może potrzebować na przetworzenie Twojego pliku nieco czasu, więc materiał może nie być jeszcze dostępny.

Jeśli chcesz dołączyć przesłany plik wideo do materiału w serwisie wiki, wstaw na żądaną stronę kod <code>{{&#35;ev:youtube|$1}}</code>.",
	'youtubeauthsub_authsubinstructions' => 'Jeśli chcesz przesłać plik, najpierw musisz zalogować sie do serwisu YouTube.',
	'youtubeauthsub_uploadhere'          => 'Plik wideo możesz przesłać z następującej lokalizacji:',
	'youtubeauthsub_uploadbutton'        => 'Prześlij',
	'youtubeauthsub_code'                => '{{#ev:youtube|$1}}.

Ten plik wideo można obejrzeć [http://www.youtube.com/watch?v=$1 tutaj].',
	'youtubeauthsub_summary'             => 'Przesyłanie pliku wideo YouTube',
	'youtubeauthsub_uploading'           => 'Pliki wideo są przesyłane.
Czekaj.',
	'youtubeauthsub_viewpage'            => 'Opcjonalnie plik wideo można zobaczyć [[$1|tutaj]].',
	'youtubeauthsub_jserror_nokeywords'  => 'Wprowadź jedno lub więcej słów kluczowych.',
	'youtubeauthsub_jserror_notitle'     => 'Wprowadź tytuł materiału wideo.',
	'youtubeauthsub_jserror_nodesc'      => 'Wprowadź opis materiału wideo.',
);
