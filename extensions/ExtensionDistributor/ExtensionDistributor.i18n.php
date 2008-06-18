<?php

$messages = array();

$messages['en'] = array(
		'extensiondistributor' => 'Download MediaWiki extension',
		'extdist-desc' => 'Extension for distributing snapshot archives of extensions',
		'extdist-not-configured' => 'Please configure $wgExtDistTarDir and $wgExtDistWorkingCopy',
		'extdist-wc-missing' => 'The configured working copy directory does not exist!',
		'extdist-no-such-extension' => 'No such extension "$1"',
		'extdist-no-such-version' => 'The extension "$1" does not exist in the version "$2".',
		'extdist-choose-extension' => 'Select which extension you want to download:',
		'extdist-wc-empty' => 'The configured working copy directory has no distributable extensions!',
		'extdist-submit-extension' => 'Continue',
		'extdist-current-version' => 'Current version (trunk)',
		'extdist-choose-version' => '
<big>You are downloading the <b>$1</b> extension.</big>

Select your MediaWiki version. 

Most extensions work across multiple versions of MediaWiki, so if your MediaWiki version is not here, or if you have a need for the latest extension features, try using the current version.',
		'extdist-no-versions' => 'The selected extension ($1) is not available in any version!',
		'extdist-submit-version' => 'Continue',
		'extdist-no-remote' => 'Unable to contact remote subversion client.',
		'extdist-remote-error' => 'Error from remote subversion client: <pre>$1</pre>',
		'extdist-remote-invalid-response' => 'Invalid response from remote subversion client.',
		'extdist-svn-error' => 'Subversion encountered an error: <pre>$1</pre>',
		'extdist-svn-parse-error' => 'Unable to process the XML from "svn info": <pre>$1</pre>',
		'extdist-tar-error' => 'Tar returned exit code $1:',
		'extdist-created' => "A snapshot of version <b>$2</b> of the <b>$1</b> extension for MediaWiki <b>$3</b> has been created. Your download should start automatically in 5 seconds. 

The URL for this snapshot is:
:$4
It may be used for immediate download to a server, but please do not bookmark it, since the contents will not be updated, and it may be deleted at a later date.

The tar archive should be extracted into your extensions directory. For example, on a unix-like OS:

<pre>
tar -xzf $5 -C /var/www/mediawiki/extensions
</pre>

On Windows, you can use [http://www.7-zip.org/ 7-zip] to extract the files.

If your wiki is on a remote server, extract the files to a temporary directory on your local computer, and then upload '''all''' of the extracted files to the extensions directory on the server.

Note that some extensions need a file called ExtensionFunctions.php, located at <tt>extensions/ExtensionFunctions.php</tt>, that is, in the ''parent'' directory of this particular extension's directory. The snapshot for these extensions contains this file as a tarbomb, extracted to ./ExtensionFunctions.php. Do not neglect to upload this file to your remote server.

After you have extracted the files, you will need to register the extension in LocalSettings.php. The extension documentation should have instructions on how to do this.

If you have any questions about this extension distribution system, please go to [[Extension talk:ExtensionDistributor]].
",
		'extdist-want-more' => 'Get another extension',
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
		'extensiondistributor'            => 'Herunterladen von MediaWiki-Erweiterung',
		'extdist-desc'                    => 'Erweiterung für die Verteilung von Schnappschuss-Archiven von Erweiterungen',
		'extdist-not-configured'          => 'Bitte konfiguriere $wgExtDistTarDir und $wgExtDistWorkingCopy',
		'extdist-wc-missing'              => 'Das konfigurierte Kopien-Arbeitsverzeichnis ist nicht vorhanden!',
		'extdist-no-such-extension'       => 'Erweiterung „$1“ ist nicht vorhanden',
		'extdist-no-such-version'         => 'Die Erweiterung „$1“ gibt es nicht in der Version „$2“.',
		'extdist-choose-extension'        => 'Bitte wähle eine Erweiterung zum Herunterladen aus:',
		'extdist-wc-empty'                => 'Das konfigurierte Kopien-Arbeitsverzeichnis enthält keine zu verteilenden Erweiterungen!',
		'extdist-submit-extension'        => 'Weiter',
		'extdist-current-version'         => 'Aktuelle Version (trunk)',
		'extdist-choose-version'          => '
<big>Du lädst du <b>$1</b>-Erweiterung herunter.</big>

Bitte wähle deine MediaWiki-Version.

Die meisten Erweiterungen arbeiten mit vielen MediaWiki-Versionen zusammen. Wenn deine MediaWiki-Version hier nicht aufgeführt ist oder du die neuesten Fähigkeiten der Erweiterung nutzen möchtest, versuche es mit der aktuellen Version.',
		'extdist-no-versions'             => 'Die gewählte Erweiterung ($1) ist nicht in der allen Versionen verfügbar!',
		'extdist-submit-version'          => 'Weiter',
		'extdist-no-remote'               => 'Der ferngesteuerte Subversion-Client ist nicht erreichbar.',
		'extdist-remote-error'            => 'Fehlermeldung des ferngesteuerten Subversion-Client: <pre>$1</pre>',
		'extdist-remote-invalid-response' => 'Ungültige Antwort vom ferngesteuerten Subversion-Client.',
		'extdist-svn-error'               => 'Subversion hat einen Fehler gemeldet: <pre>$1</pre>',
		'extdist-svn-parse-error'         => 'XML-Daten von „svn info“ können nicht verarbeitet werden: <pre>$1</pre>',
		'extdist-tar-error'               => 'Das Tar-Programm lieferte den Beendigungscode $1:',
		'extdist-created'                 => "Ein Schnappschuss der Version <b>$2</b> der MediaWiki-Erweiterung <b>$1</b> wurde erstellt. Der Download startet automatisch in 5 Sekunden.

Die URL für den Schnappschuss lautet:
:$4
Die URL ist nur zum sofortigen Download gedacht, bitte speichere sie nicht als Lesezeichen ab, da der Dateiinhalt nicht aktualisiert wird und zu einem späteren Zeitpunkt gelöscht werden kann.

Das Tar-Archiv sollte in das Erweiterungs-Verzeichnis entpackt werden. Auf einem Unix-ähnlichen Betriebssystem mit:

<pre>
tar -xzf $5 -C /var/www/mediawiki/extensions
</pre>

Unter Windows kannst du das Programm [http://www.7-zip.org/ 7-zip] zum Entpacken der Dateien verwenden.

Wenn dein Wiki auf einem entfernten Server läuft, entpacke die Dateien in ein temporäres Verzeichnis auf deinem lokalen Computer und lade dann '''alle''' entpackten Dateien auf den entfernten Server hoch.

Bitte beachte, dass einige Erweiterungen die Datei <tt>ExtensionFunctions.php</tt> benötigen. Sie liegt unter  <tt>extensions/ExtensionFunctions.php</tt>, dem Heimatverzeichnis der Erweiterungen. Der Schnappschuss dieser Erweiterung enthält diese Datei als tarbomb, entpackt nach <tt>./ExtensionFunctions.php</tt>. Vergiss nicht, auch diese Datei auf deinen entfernten Server hochzuladen.

Nachdem du die Dateien entpackt hast, musst du die Erweiterung in der <tt>LocalSettings.php</tt> registrieren. Die Dokumenation zur Erweiterung sollte eine Anleitung dazu enthalten.

Wenn du Fragen zu diesem Erweiterungs-Verteil-System hast, gehe bitte zur Seite [[Extension talk:ExtensionDistributor]].",
		'extdist-want-more'               => 'Eine weitere Erweiterung holen.',
);

/** French (Français)
 * @author Grondin
 */
$messages['fr'] = array(
	'extensiondistributor'            => 'Télécharger l’extension Mediawiki',
	'extdist-desc'                    => 'Extension pour la distribution des archives photographiques des extensions',
	'extdist-not-configured'          => 'Veuillez configurer $wgExtDistTarDir et $wgExtDistWorkingCopy',
	'extdist-wc-missing'              => 'La répertoire de la copie de travail configurée n’existe pas !',
	'extdist-no-such-extension'       => 'Aucune extension « $1 »',
	'extdist-no-such-version'         => 'L’extension « $1 » n’existe pas dans la version « $2 ».',
	'extdist-choose-extension'        => 'Sélectionnez l’extension que vous voulez télécharger :',
	'extdist-wc-empty'                => 'Le répertoire de la copie de travail configurée n’a aucune extension distribuable !',
	'extdist-submit-extension'        => 'Continuer',
	'extdist-current-version'         => 'Version courante (trunk)',
	'extdist-choose-version'          => '<big>Vous êtes en train de télécharger l’extension <b>$1</b>.</big>

Sélectionnez votre version Mediawiki.

La plupart des extensions tourne sur différentes versions de MediaWiki. Aussi, si votre version n’est pas présente ici, ou si vous avez besoin des dernières fonctionnalités de l’extension, essayez d’utiliser la version courante.',
	'extdist-no-versions'             => 'L’extension sélectionnée ($1) est indisponible dans plusieurs versions !',
	'extdist-submit-version'          => 'Continuer',
	'extdist-no-remote'               => 'Impossible de contacter le client subversion distant.',
	'extdist-remote-error'            => 'Erreur du client subversion distant : <pre>$1</pre>',
	'extdist-remote-invalid-response' => 'Réponse incorrecte depuis le client subversion distant.',
	'extdist-svn-error'               => 'Subversion a rencontré une erreur : <pre>$1</pre>',
	'extdist-svn-parse-error'         => 'Impossible de traiter le XML à partir de « svn info » : <pre>$1</pre>',
	'extdist-tar-error'               => 'Tar a retourné le code de sortie $1 :',
	'extdist-created'                 => "Une photo de la version <b>$2</b> de l’extension <b>$1</b> pour MediaWiki <b>$3</b> a été créée. Votre téléchargement devrait commencer automatiquement dans 5 secondes.

L'adresse de cette photo est :
:$4
Il peut être utilisé pour un téléchargement immédiat vers un serveur, mais évitez de l’inscrire dans vos signets, dès lors le contenu ne sera pas mis à jour, et peut être effacé à une date ultérieure.

L’archive tar devrait être extraite dans votre répertoire extensions, aussi, suivez la documentation de l’extension pour l’activer dans MediaWiki.",
	'extdist-want-more'               => 'Obtenir une autre extension',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'extensiondistributor'      => 'MediaWiki Erweiderung eroflueden',
	'extdist-no-such-extension' => 'Et gëtt keng Erweiderung "$1"',
	'extdist-no-such-version'   => 'D\'Erweiderung "$1" gëtt et net an der Versioun "$2".',
	'extdist-no-versions'       => 'Déi gewielten Erweiderung ($1) ass a kenger Versioun disponibel!',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'extensiondistributor'            => 'Last ned utvidelser til MediaWiki',
	'extdist-desc'                    => 'Utvidelse for distribusjon av andre utvidelser',
	'extdist-not-configured'          => 'Still inn $wgExtDistTarDir og $wgExtDistWorkingCopy',
	'extdist-wc-missing'              => 'Mappen med arbeidskopien finnes ikke.',
	'extdist-no-such-extension'       => 'Ingen utvidelse ved navn «$1»',
	'extdist-no-such-version'         => 'Versjon «$2» av «$1» finnes ikke',
	'extdist-choose-extension'        => 'Velg hvilken utvidelse du ønsker å laste ned:',
	'extdist-wc-empty'                => 'Mappen med arbeidskopien har ingen distribuerbare utvidelser.',
	'extdist-submit-extension'        => 'Fortsett',
	'extdist-current-version'         => 'Nåværende versjon (trunk)',
	'extdist-choose-version'          => '<big>Du laster ned utvidelsen <b>$1</b>.</big>

Angi hvilken MediaWiki-versjon du bruker.

De fleste utvidelser fungerer på flere versjoner av MediaWiki, så om versjonen du bruker ikke listes opp her, kan du prøve å velge den nyeste versjonen.',
	'extdist-no-versions'             => 'Den valgte utvidelsen ($¡) er ikke tilgjengelig i noen versjon.',
	'extdist-submit-version'          => 'Fortsett',
	'extdist-no-remote'               => 'Kunne ikke kontakte ekstern SVN-klient.',
	'extdist-remote-error'            => 'Feil fra ekstern SVN-klient: <pre>$1</pre>',
	'extdist-remote-invalid-response' => 'Ugyldig svar fra ekstern SVN-klient.',
	'extdist-svn-error'               => 'SVN fant en feil: <pre>$1</pre>',
	'extdist-svn-parse-error'         => 'Kunne ikke prosessere XML fra «svn info»: <pre>$1</pre>',
	'extdist-tar-error'               => 'Tar ga utgangsfeilen $1:',
	'extdist-created'                 => 'Et øyeblikksbilde av versjon <b>$2</b> av utvidelsen <b>$1</b> for MediaWiki <b>$3</b> har blitt opprettet. Nedlastingen vil begynne automatisk om 5&nbsp;sekunder.

Adressen til dette øyeblikksbildet er:
:$4
Adressen kan brukes for nedlasting til tjeneren, men ikke legg den til som bokmerke, for innholdet vil ikke bli oppdatert, og den kan slettes senere.

Tar-arkivet burde pakkes ut i din utvidelsesmappe; følg deretter utvidelsens egen dokumentasjon for å slå den på i MediaWiki.',
	'extdist-want-more'               => 'Hent flere utvidelser',
);

/** Swedish (Svenska)
 * @author M.M.S.
 */
$messages['sv'] = array(
	'extensiondistributor'      => 'Ladda ner tillägg till MediaWiki',
	'extdist-desc'              => 'Tillägg för distribution av övriga tillägg',
	'extdist-not-configured'    => 'Var god bekräfta $wgExtDistTarDir och $wgExtDistWorkingCopy',
	'extdist-wc-missing'        => 'Mappen med arbetskopian finns inte!',
	'extdist-no-such-extension' => 'Ingen sådant tillägg "$1"',
);

