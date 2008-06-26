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

/** Arabic (العربية)
 * @author OsamaK
 */
$messages['ar'] = array(
	'extdist-want-more' => 'الحصول على امتداد آخر',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'extdist-not-configured'          => 'Необходимо е да се настроят $wgExtDistTarDir и $wgExtDistWorkingCopy',
	'extdist-no-such-extension'       => 'Няма такова разширение „$1“',
	'extdist-choose-extension'        => 'Изберете разширение, което желаете да свалите:',
	'extdist-submit-extension'        => 'Продължаване',
	'extdist-current-version'         => 'Текуща версия (trunk)',
	'extdist-submit-version'          => 'Продължаване',
	'extdist-remote-invalid-response' => 'Невалиден отговор от отдалечения Subversion клиент.',
	'extdist-svn-error'               => 'Възникна грешка в Subversion: <pre>$1</pre>',
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
<big>Du lädst die <b>$1</b>-Erweiterung herunter.</big>

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
 * @author IAlex
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

L’archive tar devrait être extraite dans votre répertoire extensions. À titre d’exemple, sur un système basé sur UNIX :

<pre>
tar -xzf $5 -C /var/www/mediawiki/extensions
</pre>

Sous Windows, vous pouvez utiliser [http://www.7-zip.org/ 7-zip] pour extraire les fichiers.

Si votre wiki se trouve sur un serveur distant, extrayez les fichiers dans un fichier temporaire sur votre ordinateur local, et ensuite téléversez les '''tous''' dans le répertoire extensions du serveur.

Notez bien que quelques extensions nécessite un fichier dénommé ExtensionFunctions.php, localisé sur  <tt>extensions/ExtensionFunctions.php</tt>, qui est dans le répertoire ''parent'' du répertoire particulier de ladite extension. L’image de ces extensions contiennent ce fichier dans l’archive tar lequel sera extrait sous ./ExtensionFunctions.php. Ne négligez pas de le téléverser aussi sur le serveur.

Une fois l’extraction faite, vous aurez besoin d’enregistrer l’extension dans LocalSettings.php. Celle-ci devrait posséder un mode opératoire pour cela.

Si vous avez des questions concernant ce système de distribution des extensions, veuillez aller sur [[Extension talk:ExtensionDistributor]].",
	'extdist-want-more'               => 'Obtenir une autre extension',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'extensiondistributor'      => 'Descargar a extensión MediaWiki',
	'extdist-desc'              => 'Extensión para distribuír arquivos fotográficos de extensións',
	'extdist-not-configured'    => 'Por favor, configure $wgExtDistTarDir e $wgExtDistWorkingCopy',
	'extdist-wc-missing'        => 'O directorio da copia en funcionamento configurada non existe!',
	'extdist-no-such-extension' => 'Non existe a extensión "$1"',
	'extdist-no-such-version'   => 'A extensión "$1" non existe na versión "$2".',
	'extdist-choose-extension'  => 'Seleccione a extensión que queira descargar:',
	'extdist-wc-empty'          => 'A copia configurada do directorio que funciona non ten extensións que se poidan distribuír!',
	'extdist-submit-extension'  => 'Continuar',
	'extdist-current-version'   => 'Versión actual (trunk)',
	'extdist-choose-version'    => '<big>Está descargando a extensión <b>$1</b>.</big>

Seleccione a súa versión MediaWiki.  

A maioría das extensións traballan con múltiples versións de MediaWiki, polo que se a súa versión de MediaWiki non está aquí, ou se precisa características da última extensión, probe a usar a versión actual.',
	'extdist-no-versions'       => 'A extensión seleccionada ($1) non está dispoñible en ningunha versión!',
	'extdist-submit-version'    => 'Continuar',
	'extdist-svn-error'         => 'A subversión atopou un erro: <pre>$1</pre>',
	'extdist-svn-parse-error'   => 'Non se pode procesar o XML de "svn info": <pre>$1</pre>',
	'extdist-tar-error'         => 'Tar devolveu o código de saída $1:',
	'extdist-created'           => "Unha fotografía da versión <b>$2</b> da extensión <b>$1</b> de MediaWiki <b>$3</b> foi creada. A súa descarga debería comezar automaticamente en 5 segundos.  

A dirección URL desta fotografía é:
:$4
Poderá ser usada para descargala inmediatamente a un servidor, pero, por favor, non a engada á listaxe dos seus favoritos mentres o contido non é actualizado. Poderá tamén ser eliminada nuns días.

O arquivo tar deberá ser extraído no seu directorio de extensións. Por exemplo, nun sistema beseado no UNIX:

<pre>
tar -xzf $5 -C /var/www/mediawiki/extensions
</pre>

No Windows, pode usar [http://www.7-zip.org/ 7-zip] para extraer os ficheiros.

Se o seu wiki está nun servidor remoto, extraia os ficheiros nun directorio temporal no seu computador e logo cargue '''todos''' os ficheiros extraídos no directorio de extensións do servidor.

Déase de conta de que algunhas extensións precisan dun ficheiro chamado ExtensionFunctions.php, localizado en <tt>extensions/ExtensionFunctions.php</tt>, que está no directorio ''parente'' deste directorio particular da extensión. A fotografía destas extensións contén este ficheiro como un tarbomb, extraído en ./ExtensionFunctions.php. Non se descoide ao cargar este ficheiro no seu servidor remoto.

Despois de extraer os ficheiros, necesitará rexistrar a extensión en LocalSettings.php. A documentación da extensión deberá ter instrucións de como facer isto.

Se ten algunha dúbida ou pregunta acerca do sistema de distribución das extensións, por favor, vaia a [[Extension talk:ExtensionDistributor]].",
	'extdist-want-more'         => 'Obter outra extensión',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'extensiondistributor'      => 'MediaWiki Erweiderung eroflueden',
	'extdist-no-such-extension' => 'Et gëtt keng Erweiderung "$1"',
	'extdist-no-such-version'   => 'D\'Erweiderung "$1" gëtt et net an der Versioun "$2".',
	'extdist-submit-extension'  => 'Viru fueren',
	'extdist-no-versions'       => 'Déi gewielten Erweiderung ($1) ass a kenger Versioun disponibel!',
	'extdist-submit-version'    => 'Viru fueren',
	'extdist-want-more'         => 'Eng aner Erweiderung benotzen',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'extensiondistributor'            => 'MediaWiki-extensie downloaden',
	'extdist-desc'                    => 'Extensie voor het distribueren van extensies',
	'extdist-not-configured'          => 'Maak alstublieft de instellingen voor $wgExtDistTarDir en $wgExtDistWorkingCopy',
	'extdist-wc-missing'              => 'De instelde werkmap bestaat niet!',
	'extdist-no-such-extension'       => 'De extensie "$1" bestaat niet',
	'extdist-no-such-version'         => 'De extensie "$1" bestaat niet in de versie "$2".',
	'extdist-choose-extension'        => 'Selecteer de extensie die u wilt downloaden:',
	'extdist-wc-empty'                => 'De ingestelde werkmap bevat geen te distribueren extensies!',
	'extdist-submit-extension'        => 'Doorgaan',
	'extdist-current-version'         => 'Huidige versie (trunk)',
	'extdist-choose-version'          => '<big>U bent de extensie <b>$1</b> aan het downloaden.</big>

Selecteer uw versie van MediaWiki.

De meeste extensies werken met meerdere versies van MediaWiki, dus als uw versie niet in de lijst staat, of als u behoefte hebt aan de nieuwste mogelijkheden van de extensie, gebruik dan de huidige versie.',
	'extdist-no-versions'             => 'De geselecteerde extensie ($1) is in geen enkele versie beschikbaar!',
	'extdist-submit-version'          => 'Doorgaan',
	'extdist-no-remote'               => 'Het was niet mogelijk de externe subversionclient te benaderen.',
	'extdist-remote-error'            => 'Fout van de externe subversionclient: <pre>$1</pre>',
	'extdist-remote-invalid-response' => 'Ongeldig antwoord van de externe subversionclient.',
	'extdist-svn-error'               => 'Subversion geeft de volgende foutmelding: <pre>$1</pre>',
	'extdist-svn-parse-error'         => 'Het was niet mogelijk de XML van "svn info" te verwerken: <pre>$1</pre>',
	'extdist-tar-error'               => 'Tat gaf de volgende exitcode $1:',
	'extdist-created'                 => 'De snapshot voor versie <b>$2</b> voor de extensie <b>$1</b> voor MediaWiki <b>$3</b> is aangemaakt. Uw download start automatisch over 5 seconden.

De URL voor de snapshot is:
:$4
Deze verwijzing kan gebruikt worden door het direct downloaden van de server, maar maak alstublieft geen bladwijzers aan, omdat de inhoud bijgewerkt kan worden, of de snapshot op een later moment verwijderd kan worden.

Pak het tararchief uit in uw map "extensions/". Op een UNIX-achtig besturingssysteem gaat dat als volgt:

<pre>
tar -xzf $5 -C /var/www/mediawiki/extensions
</pre>

Op Windows kunt u [http://www.7-zip.org/ 7-zip] gebruiken om de bestanden uit te pakken.

Als uw wiki op een op afstand beheerde server staat, pak de bestanden dan uit in een tijdelijke map op uw computer. Upload daarna \'\'\'alle\'\'\' uitgepakte bestanden naar de map "extensions/" op de server.

Een aantal extensies hebben het bestand ExtensionFunctions.php nodig, <tt>extensions/ExtensionFunctions.php</tt>, dat in de map direct boven de map met de naam van de extensie hoort te staan. De snapshots voor deze extensies bevatten dit bestand als tarbomb. Het wordt uitgepakt als ./ExtensionFunctions.php. Vergeet dit bestand niet te uploaden naar uw server.

Nadat u de bestanden hebt uitgepkt en op de juiste plaatst hebt neergezet, moet u de extensie registreren in LocalSettings.php. In de documentatie van de extensie treft u de instructies aan.

Als u vragen hebt over dit distributiesysteem voor extensies, ga dan naar [[Extension talk:ExtensionDistributor]].',
	'extdist-want-more'               => 'Nog een extensie downloaden',
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

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'extensiondistributor'            => 'Telecargar l’extension Mediawiki',
	'extdist-desc'                    => 'Extension per la distribucion dels archius fotografics de las extensions',
	'extdist-not-configured'          => 'Configuratz $wgExtDistTarDir e $wgExtDistWorkingCopy',
	'extdist-wc-missing'              => 'Lo repertòri de la còpia de trabalh configurada existís pas !',
	'extdist-no-such-extension'       => "Pas cap d'extension « $1 »",
	'extdist-no-such-version'         => 'L’extension « $1 » existís pas dins la version « $2 ».',
	'extdist-choose-extension'        => 'Seleccionatz l’extension que volètz telecargar :',
	'extdist-wc-empty'                => "Lo repertòri de la còpia de trabalh configurada a pas cap d'extension distribuibla !",
	'extdist-submit-extension'        => 'Contunhar',
	'extdist-current-version'         => 'Version correnta (trunk)',
	'extdist-choose-version'          => "<big>Sètz a telecargar l’extension <b>$1</b>.</big>

Seleccionatz vòstra version Mediawiki.

La màger part de las extensions vira sus diferentas versions de MediaWiki. Atal, se vòstra version es pas presenta aicí, o s'avètz besonh de las darrièras foncionalitats de l’extension, ensajatz d’utilizar la version correnta.",
	'extdist-no-versions'             => 'L’extension seleccionada ($1) es indisponibla dins mantuna version !',
	'extdist-submit-version'          => 'Contunhar',
	'extdist-no-remote'               => 'Impossible de contactar lo client subversion distant.',
	'extdist-remote-error'            => 'Error del client subversion distant : <pre>$1</pre>',
	'extdist-remote-invalid-response' => 'Responsa incorrècta dempuèi lo client subversion distant.',
	'extdist-svn-error'               => 'Subversion a rencontrat una error : <pre>$1</pre>',
	'extdist-svn-parse-error'         => 'Impossible de tractar lo XML a partir de « svn info » : <pre>$1</pre>',
	'extdist-tar-error'               => 'Tar a tornat lo còde de sortida $1 :',
	'extdist-created'                 => "Una fòto de la version <b>$2</b> de l’extension <b>$1</b> per MediaWiki <b>$3</b> es estada creada. Vòstre telecargament deuriá començar automaticament dins 5 segondas.

L'adreça d'aquesta fòto es :
:$4
Pòt èsser utilizat per un telecargament immediat cap a un servidor, mas evitatz de l’inscriure dins vòstres signets, tre alara lo contengut serà pas mes a jorn, e poirà èsser escafat a una data ulteriora.

L’archiu tar deuriá èsser extracha dins vòstre repertòri d'extensions. A títol d’exemple, sus un sistèma basat sus UNIX :

<pre>
tar -xzf $5 -C /var/www/mediawiki/extensions
</pre>

Jos Windows, podètz utilizar [http://www.7-zip.org/ 7-zip] per extraire los fichièrs.

Se vòstre wiki se tròba sus un servidor distant, extractatz los fichièrs dins un fichièr temporari sus vòstre ordenador local, e en seguida televersatz los '''totes''' dins lo repertòri d'extensions del servidor.

Notatz plan que qualques extensions necessitan un fichièr nomenat ExtensionFunctions.php, localizat sus  <tt>extensions/ExtensionFunctions.php</tt>, qu'es dins lo repertòri ''parent'' del repertòri particular de ladicha extension. L’imatge d'aquestas extensions contenon aqueste fichièr dins l’archiu tar loqual serà extrach jos ./ExtensionFunctions.php. Neglijatz pas de le televersar tanben sul servidor.

Un còp l’extraccion facha, aurètz besonh d’enregistrar l’extension dins LocalSettings.php. Aquesta deuriá aver un mòde operatòri per aquò.

S'avètz de questions a prepaus d'aqueste sistèma de distribucion de las extensions, anatz sus [[Extension talk:ExtensionDistributor]].",
	'extdist-want-more'               => 'Obténer una autra extension',
);

/** Russian (Русский)
 * @author MaxSem
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'extensiondistributor'      => 'Скачать расширения MediaWiki',
	'extdist-desc'              => 'Расширение для скачивания дистрибутивов с расширениями',
	'extdist-not-configured'    => 'Пожалуйста, задайте $wgExtDistTarDir и $wgExtDistWorkingCopy',
	'extdist-wc-missing'        => 'Заданная в настройках директория с рабочей копией не существует!',
	'extdist-no-such-extension' => 'Расширение «$1» не найдено',
	'extdist-no-such-version'   => 'Версия $2 расширения «$1» не найдена.',
	'extdist-choose-extension'  => 'Выберите расширение для скачивания:',
	'extdist-wc-empty'          => 'Заданная в настройках директория с рабочей копией не имеет расширений для распространения!',
	'extdist-submit-extension'  => 'Продолжить',
	'extdist-current-version'   => 'Текущая версия (trunk)',
	'extdist-choose-version'    => '<big>Вы скачиваете расширение <b>«$1»</b>.</big>

Выберите свою версию MediaWiki.  

Большинство расширений работают с несколькими версиями MediaWiki, поэтому если установленная у вас версия здесь не приведена, или вам требуются возможности последней версии расширения — попробуйте последнюю версию.',
	'extdist-no-versions'       => 'Выбранное расширение («$1») не доступно ни в одной версии!',
	'extdist-submit-version'    => 'Продолжить',
	'extdist-no-remote'         => 'Не получилось связаться с удалённым клиентом Subversion.',
	'extdist-remote-error'      => 'Ошибка удалённого клиента Subversion: <pre>$1</pre>',
	'extdist-svn-error'         => 'Ошибка Subversion: <pre>$1</pre>',
	'extdist-svn-parse-error'   => 'Ошибка обработки XML, возвращённого командой «svn info»: <pre>$1</pre>',
	'extdist-tar-error'         => 'Tar вернул код ошибки $1:',
	'extdist-want-more'         => 'Скачать другое расширение',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'extensiondistributor'            => 'Stiahnuť rozšírenie MediaWiki',
	'extdist-desc'                    => 'Rozšírenie na distribúciu archívov rozšírení',
	'extdist-not-configured'          => 'Prosím, nastavte $wgExtDistTarDir a $wgExtDistWorkingCopy',
	'extdist-wc-missing'              => 'Nastavený adresár pre pracovnú kópiu neexistuje!',
	'extdist-no-such-extension'       => 'Rozšírenie „$1” neexistuje',
	'extdist-no-such-version'         => 'Rozšírenie „$1” neexistuje vo verzii „$2”',
	'extdist-choose-extension'        => 'Vyberte, ktoré rozšírenie chcete stiahnuť:',
	'extdist-wc-empty'                => 'Nastavená pracovná kópia nemá rozšírenia, ktoré je možné distribuovať!',
	'extdist-submit-extension'        => 'Pokračovať',
	'extdist-current-version'         => 'Aktuálna verzia (trunk)',
	'extdist-choose-version'          => '<big>Sťahujete rozšírenie <b>$1</b>.</big>

Vyberte vašu verziu MediaWiki.

Väčšina rozšírení funguje na viacerých verziách MediaWiki, takže ak tu nie je vaša verzia MediaWiki uvedená alebo potrebujete najnovšiu vývojovú verziu rozšírenia, pokúste sa použiť aktuálnu verziu.',
	'extdist-no-versions'             => 'Zvolené rozšírenie ($1) nie je dostupné v žiadnej verzii!',
	'extdist-submit-version'          => 'Pokračovať',
	'extdist-no-remote'               => 'Nepodarilo sa kontaktovať vzdialeného klienta Subversion.',
	'extdist-remote-error'            => 'Chyba od vzdialeného klienta Subversion: <pre>$1</pre>',
	'extdist-remote-invalid-response' => 'Neplatná odpoveď od vzdialeného klienta Subversion.',
	'extdist-svn-error'               => 'Subversion narazil na chybu: <pre>$1</pre>',
	'extdist-svn-parse-error'         => 'Nebolo možné spracovať XML z výstupu „svn info”: <pre>$1</pre>',
	'extdist-tar-error'               => 'Tar skončil s návratovým kódom $1:',
	'extdist-created'                 => "Obraz verzie <b>$2</b> rozšírenia <b>$1</b> pre MediaWiki <b>$3</b> bol stiahnutý. Sťahovanie by malo začať automaticky do 5 sekúnd.

URL tohto obrazu je:
:$4
Je možné ho použiť na okamžité stiahnutie na server, ale prosím neukladajte ho ako záložku, pretože jeho obsah sa nebude aktualizovať a neskôr môže byť zmazaný.

Tar archív by ste mali rozbaliť do vášho adresára s rozšíreniami. Príkad pre unixové systémy:

<pre>
tar -xzf $5 -C /var/www/mediawiki/extensions
</pre>

Na Windows môžete na rozbalenie súborov použiť [http://www.7-zip.org/ 7-zip].

Ak je vaša wiki na vzdialenom serveri, rozbaľte súbory do dočasného adresára na vašom lokálnom počítači a potom nahrajte '''všetky''' rozbalené súbory do adresára pre rozšírenia na serveri.

Všimnite si, že niektoré rozšírenia potrebujú nájsť súbor s názvom ExtensionFunctions.php v <tt>extensions/ExtensionFunctions.php</tt>, t.j. v ''nadradenom'' adresári adresára tohto konkrétneho rozšírenia. Snímka týchto rozšírení obsahuje tento súbor, ktorý sa rozbalí do ./ExtensionFunctions.php. Nezanedbajte nahrať tento súbor na vzdialený serer.

Po rozbalení súborov budete musieť rozšírenie zaregistrovať v LocalSettings.php. Dokumentácia k rozšíreniu by mala obsahovať informácie ako to spraviť.

Ak máte otázky týkajúce sa tohto systému distribúcie rozšírení, navštívte [[Extension talk:ExtensionDistributor]].",
	'extdist-want-more'               => 'Stiahnuť iné rozšírenie',
);

/** Swedish (Svenska)
 * @author M.M.S.
 */
$messages['sv'] = array(
	'extensiondistributor'            => 'Ladda ner tillägg till MediaWiki',
	'extdist-desc'                    => 'Tillägg för distribution av övriga tillägg',
	'extdist-not-configured'          => 'Var god bekräfta $wgExtDistTarDir och $wgExtDistWorkingCopy',
	'extdist-wc-missing'              => 'Mappen med arbetskopian finns inte!',
	'extdist-no-such-extension'       => 'Ingen sådant tillägg "$1"',
	'extdist-no-such-version'         => 'Tillägget "$1" finns inte i versionen "$2".',
	'extdist-choose-extension'        => 'Välj vilket tillägg du vill ladda ner:',
	'extdist-wc-empty'                => 'Mappen med arbetskopian har inga distribuerbara tillägg!',
	'extdist-submit-extension'        => 'Fortsätt',
	'extdist-current-version'         => 'Nuvarande version (trunk)',
	'extdist-choose-version'          => '
<big>Du laddar ner tillägget <b>$1</b>.</big>

Ange vilken version av MediaWiki du använder.

De flesta tilläggen fungerar på flera versioner av MediaWiki, så om versionen du använder inte listas upp här, kan du pröva att välja den nyaste versionen.',
	'extdist-no-versions'             => 'Det valda tillägget ($1) är inte tillgängligt i någon version!',
	'extdist-submit-version'          => 'Fortsätt',
	'extdist-no-remote'               => 'Kunde inte kontakta extern SVN-klient.',
	'extdist-remote-error'            => 'Fel från extern SVN-klient: <pre>$1</pre>',
	'extdist-remote-invalid-response' => 'Ogiltigt svar från extern SVN-klient.',
	'extdist-svn-error'               => 'SVN hittade ett fel: <pre>$1</pre>',
	'extdist-svn-parse-error'         => 'Kunde inte processera XML från "svn info": <pre>$1</pre>',
	'extdist-tar-error'               => 'Tar returnerade utgångskod $1:',
	'extdist-want-more'               => 'Hämta andra tillägg',
);

