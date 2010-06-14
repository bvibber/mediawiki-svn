<?php
/**
 * Translations of Translate extension.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$messages = array();

/** English
 * @author Nike
 * @author Siebrand
 */
$messages['en'] = array(
	'firststeps' => 'First steps',
	'firststeps-desc' => '[[Special:FirstSteps|Special page]] for getting users started on a wiki using the Translate extension',
	'translate-fs-pagetitle-done' => ' - done!',
	'translate-fs-pagetitle' => 'Getting started wizard - $1',
	'translate-fs-signup-title' => 'Sign up',
	'translate-fs-settings-title' => 'Configure your preferences',
	'translate-fs-userpage-title' => 'Create your user page',
	'translate-fs-permissions-title' => 'Request translator permissions',
	'translate-fs-target-title' => 'Start translating!',
	'translate-fs-email-title' => 'Confirm your e-mail address',

	'translate-fs-intro' => "Welcome to the {{SITENAME}} first steps wizard.
You will be guided trough the process of becoming a translator step by step.
In the end you will be able to translate ''interface messages'' of all supported projects at {{SITENAME}}.",

	'translate-fs-signup-text' => '[[Image:HowToStart1CreateAccount.png|frame]]

In the first step you must sign up.

Credits for your translations are attributed to your user name.
The image on the right shows how to fill the fields.

If you have already signed up, $1log in$2 instead.
Once you are signed up, please return to this page.

$3Sign up$4',
	'translate-fs-settings-text' => 'You should now go to your preferences and
at least change your interface language to the language you are going to translate to.

Your interface language is used as the default target language.
It is easy to forget to change the language to the correct one, so setting it now is highly recommended.

While you are there, you can also request the software to display translations in other languages you know.
This setting can be found under tab "{{int:prefs-editing}}".
Feel free to explore other settings, too.

Go to your [[Special:Preferences|preferences page]] now and then return back to this page.',
	'translate-fs-settings-skip' => "I'm done.
Let me proceed.",
	'translate-fs-userpage-text' => 'Now you need to create an user page.

Please write something about yourself; who you are and what you do.
This will help the {{SITENAME}} community to work together.
At {{SITENAME}} there are people from all around the world working on different languages and projects.

In the prefilled box above in the very first line you see <nowiki>{{#babel:en-2}}</nowiki>.
Please complete it with your language knowledge.
The number behind the language code describes how well you know the language.
The alternatives are:
* 1 - a little
* 2 - basic knowledge
* 3 - good knowledge
* 4 - native speaker level
* 5 - you use the language professionally, for example you are a professional translator.

If you are a native speaker of a language, leave the skill level out, and only use the language code.
Example: if you speak Tamil natively, English well, and little Swahili, you would write:
<code><nowiki>{{#babel:ta|en-3|sw-1}}</nowiki></code>

If you do not know the language code of a language, now is good time to look it up.
You can use the list below.',
	'translate-fs-userpage-submit' => 'Create my userpage',
	'translate-fs-userpage-done' => 'Well done! You now have an user page.',
	'translate-fs-permissions-text' => 'Now you need to place a request to be added to the translator group.

Until we fix the code, please go to [[Project:Translator]] and follow the instructions.
Then come back to this page.

After you have submitted your request, one of the volunteer staff members will check your request and approve it as soon as possible.
Please be patient.

<del>Check that the following request is correctly filled and then press the request button.</del>',

	'translate-fs-target-text' => 'Congratulations!
You can now start translating.

Do not be afraid if still feels new and confusing to you.
At [[Project list]] there is an overview of projects you can contribute translations to.
Most of the projects have a short description page with a "\'\'Translate this project\'\'" link, that will take you to a page which lists all untranslated messages.
A list of all message groups with the [[Special:LanguageStats|current translation status for a language]] is also available.

If you feel that you need to understand more before you start translating, you can read the [[FAQ|Frequently asked questions]].
Unfortanely documentation can be out of date sometimes.
If there is something that you think you should be able to do, but cannot find out how, do not hesitate to ask it at the [[Support|support page]].

You can also contact fellow translators of the same language at [[Portal:$1|your language portal]].
The portal links to your current [[Special:Preferences|language preference]].
Please change it if needed.',

	'translate-fs-email-text' => 'Please provide your e-mail address in [[Special:Preferences|your preferences]] and confirm it from the e-mail that is sent to you.

This allows other users to contact you by e-mail.
You will also receive newsletters at most once a month.
If you do not want receive newsletters, you can opt-out in the tab "{{int:prefs-misc}}" of your [[Special:Preferences|preferences]].',
);

/** Message documentation (Message documentation)
 * @author Lloffiwr
 */
$messages['qqq'] = array(
	'translate-fs-permissions-text' => 'Synonym for "filed" is "submitted".',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author Wizardist
 */
$messages['be-tarask'] = array(
	'firststeps' => 'Першыя крокі',
	'firststeps-desc' => '[[Special:FirstSteps|Спэцыяльная старонка]] для пачатку працы з пашырэньнем Translate',
	'translate-fs-pagetitle-done' => ' — зроблена!',
);

/** Breton (Brezhoneg)
 * @author Y-M D
 */
$messages['br'] = array(
	'firststeps' => 'Pazenn gentañ',
	'translate-fs-pagetitle-done' => '↓  - graet !',
	'translate-fs-signup-title' => 'En em enskrivañ',
);

/** German (Deutsch)
 * @author The Evil IP address
 */
$messages['de'] = array(
	'firststeps' => 'Erste Schritte',
	'firststeps-desc' => '[[Special:FirstSteps|Spezialseite]] zur Starterleichterung auf Wikis mit der „Translate“-Extension',
	'translate-fs-pagetitle-done' => '- erledigt!',
	'translate-fs-pagetitle' => 'Startsassistent - $1',
	'translate-fs-signup-title' => 'Registrieren',
	'translate-fs-settings-title' => 'Deine Einstellungen anpassen',
	'translate-fs-userpage-title' => 'Deine Benutzerseite erstellen',
	'translate-fs-permissions-title' => 'Übersetzerrechte beantragen',
	'translate-fs-target-title' => 'Übersetzen!',
	'translate-fs-email-title' => 'Deine E-Mail-Adresse bestätigen',
	'translate-fs-intro' => "Willkommen bei dem {{SITENAME}}-Startassistenten.
Dir wird gezeigt, wie du Schritt für Schritt ein Übersetzer wirst.
Am Ende wirst du alle ''Oberflächen-Nachrichten'' der von {{SITENAME}} unterstützten Projekte übersetzen können.",
	'translate-fs-signup-text' => '[[Image:HowToStart1CreateAccount.png|frame]]

Als erstes musst du dir ein Benutzerkonto anlegen.

Dein Benutzername wird in den Autorenlisten für deine Übersetzungen genannt.
Das Bild rechts zeigt, wie du die Felder ausfüllen musst.

Wenn du dich bereits registriert hast, $1logge dich stattdessen ein$2.
Wenn du eingeloggt bist, kehre auf diese Seite zurück.

$3Benutzerkonto anlegen$4',
	'translate-fs-settings-text' => 'Gehe nun zu deinen Einstellungen und ändere zumindest deine Oberflächensprache in die Sprache, die du übersetzen wirst.

Deine Oberflächensprache wird als deine Standardsprache benutzt.
Man vergisst leicht, die Sprache in die Richtige zu verändern, daher ist es empfohlen, dies sofort zu tun.

Wenn du dabei bist, kannst du die Software auch bitten, Übersetzungen in anderen Sprachen anzuzeigen, die du kennst.
Diese Einstellung findest du unter dem Tab „{{int:prefs-editing}}“.
Guck dir auch ruhig die anderen Einstellungsmöglichkeiten an.

Gehe jetzt in deine [[Special:Preferences|Einstellungen]] und kehre dann auf diese Seite zurück.',
	'translate-fs-settings-skip' => 'Fertig.
Nächster Schritt.',
	'translate-fs-userpage-text' => 'Jetzt musst du eine Benutzerseite erstellen.

Bitte schreibe etwas über dich, wer du bist und was du machst.
Dies hilft der {{SITENAME}}-Gemeinschaft bei der Zusammenarbeit.
Auf {{SITENAME}} gibt es Leute aus der ganzen Welt, die an verschiedenen Sprachen und Projekten arbeiten.

In der ausgefüllten Box oben siehst du in der ersten Zeile <nowiki>{{#babel:en-2}}</nowiki>.
Bitte fülle es mit deinen Sprachkenntnissen aus.
Die Zahl hinter dem Sprachcode beschreibt wie gut du die Sprache kannst.
Die Möglichkeiten sind:
*1 - ein bisschen
*2 - Basiswissen
*3 - fließend
*4 - nahezu Muttersprachler
*5 - professionell, z.B. wenn du ein professioneller Übersetzer bist.

Wenn du ein Muttersprachler bist, lasse die Zahl aus und benutze nur den Sprachcode.
Beispiel: Wenn du Tamil als Muttersprache, Englisch gut und ein wenig Swahili könntest du Folgendes schreiben:
<code><nowiki>{{#babel:ta|en-3|sw-1}}</nowiki></code>

Wenn du den Sprachcode einer Sprache nicht kennst, dann guck ihn jetzt nach.
Du kannst die Liste unten benutzen.',
	'translate-fs-userpage-submit' => 'Benutzerseite erstellen',
	'translate-fs-userpage-done' => 'Gut gemacht! Du hast nun eine Benutzerseite',
	'translate-fs-permissions-text' => 'Jetzt musst du einen Antrag stellen, um zur Übersetzergruppe hinzugefügt zu werden.

Bis wir den Code korrigieren, gehe auf [[Project:Translator]] und folge den Anweisungen.
Kehre danach zurück auf diese Seite.

Nachdem du den Antrag abgeschickt hast, wird ein freiwilliger Mitarbeiter deinen Antrag prüfen und ihn so bald wie möglich akzeptieren.
Bitte habe dabei etwas Geduld.

<del>Stelle sicher, dass der folgende Antrag korrekt ausgefüllt und und drücke dann den Button.</del>',
	'translate-fs-target-text' => "Glückwunsch!
Du kannst nun übersetzen.

Sei nicht verwirrt, wenn es dir noch neu und verwirrend verkommt.
Unter [[Project list]] gibt es eine Übersicht der Projekte, die du übersetzen kannst.
Die meisten Projekte haben eine kurze Beschreibungsseite mit einem „''Übersetzen''“- Link, der dich auf eine Seite mit nicht-übersetzten Nachrichten bringt.
Eine Liste aller Nachrichtengruppen mit dem [[Special:LanguageStats|momentanen Status einer Sprache]] gibt es auch.

Wenn du mehr hiervon verstehen möchtest, kannst du die [[FAQ|häufig gestellten Fragen]] lesen.
Leider kann die Dokumentation zeitweise veraltet sein.
Wenn du etwas tun möchtest, jedoch nicht weißt wie, zögere nicht auf der [[Support|Hilfeseite]] zu fragen.

Du kannst auch Übersetzer deiner Sprache auf [[Portal:$1|dem Sprachportal]] kontaktieren.
Das Portal verlinkt auf deine momentane [[Special:Preferences|Spracheinstellung]].
Bitte ändere sie falls nötig.",
	'translate-fs-email-text' => 'Bitte gebe deine E-Mail-Adresse in [[Special:Preferences|deinen Einstellungen]] ein und bestätige die an dich versandte E-Mail.

Dies gibt anderen die Möglichkeit, dich über E-Mail zu erreichen.
Du erhälst außerdem bis zu einmal im Monat einen Newsletter.
Wenn du keinen erhalten möchtest, kannst du dich im Tab „{{int:prefs-misc}}“ in deinen [[Special:Preferences|Einstellungen]] austragen.
Wenn du keinen Newsletter haben möchtest, kannst du dich im Tab Translate-fs-target-text',
);

/** Spanish (Español)
 * @author Crazymadlover
 * @author Diego Grez
 */
$messages['es'] = array(
	'firststeps' => 'Primeros pasos',
	'firststeps-desc' => '[[Special:FirstSteps|Página especial]] para que los usuarios comiencen en un wiki usando la extensión de traducción',
	'translate-fs-pagetitle-done' => '- hecho!',
	'translate-fs-pagetitle' => 'Guía de inicio - $1',
	'translate-fs-signup-title' => 'Registrarse',
	'translate-fs-settings-title' => 'Configurar tus preferencias',
	'translate-fs-userpage-title' => 'Crear tu página de usuario',
	'translate-fs-permissions-title' => 'Solicitar permisos de traducción',
	'translate-fs-target-title' => 'Comenzar a traducir!',
	'translate-fs-email-title' => 'Confirmar tu dirección de correo electrónico',
	'translate-fs-intro' => "Bienvenido al asistente de los primeros pasos en {{SITENAME}}.
Serás guíado a través del proceso de convertirte en un traductor pasa a paso.
Al final serás capaz de traducir los ''mensajes de interfaz'' de todos los proyectos soportados en {{SITENAME}}",
	'translate-fs-signup-text' => '[[Image:HowToStart1CreateAccount.png|marco]]

El primer paso es que debes registrarte.

Los créditos por tu traducción se atribuyen a tu nombre de usuario.
La imagen de la derecha muestra como llenar los campos.

Si ya te has registrado, $1inicia sesión$2 entonces.
Una vez que te has registrado, por favor regresa a esta página.

$3Registrarse$4',
	'translate-fs-settings-skip' => 'He terminado.
Déjenme continuar.',
	'translate-fs-userpage-submit' => 'Crear mi página de usuario',
	'translate-fs-userpage-done' => 'Bien hecho! Ahora tienes una página de usuario.',
);

/** French (Français)
 * @author Peter17
 */
$messages['fr'] = array(
	'firststeps' => 'Premiers pas',
	'firststeps-desc' => '[[Special:FirstSteps|Page spéciale]] pour guider les utilisateurs sur un wiki utilisant l’extension Translate',
	'translate-fs-pagetitle-done' => ' - fait !',
	'translate-fs-pagetitle' => 'Guide de démarrage - $1',
	'translate-fs-signup-title' => 'Inscrivez-vous',
	'translate-fs-settings-title' => 'Configurez vos préférences',
	'translate-fs-userpage-title' => 'Créez votre page utilisateur',
	'translate-fs-permissions-title' => 'Demandez les permissions de traducteur',
	'translate-fs-target-title' => 'Commencez à traduire !',
	'translate-fs-email-title' => 'Confirmez votre adresse électronique',
	'translate-fs-intro' => "Bienvenue sur l’assistant premiers pas de {{SITENAME}}.
Nous allons vous guider étape par étape pour devenir un traducteur.
À la fin du processus, vous pourrez traduire les ''messages des interfaces'' de tous les projets gérés par {{SITENAME}}.",
	'translate-fs-signup-text' => '[[Image:HowToStart1CreateAccount.png|cadre]]

La première étape consiste à s’inscrire.

Les traductions que vous effectuerez seront créditées à votre nom d’utilisateur.
L’image sur la droite montre comment remplir les champs.

Si vous vous êtes déjà inscrit, veuillez $1vous identifier$2.
Une fois inscrit, veuillez revenir vers cette page.

$3Inscrivez-vous$4',
	'translate-fs-settings-text' => 'Vous devez à présent vous rendre dans vos préférences et au moins choisir comme langue d’interface celle dans laquelle vous voulez traduire.

La langue choisie pour l’interface est utilisée comme langue par défaut pour les traductions.
Il est facile d’oublier de changer cette préférence et donc hautement recommandé de le faire maintenant.

Tant que vous y êtes, vous pouvez aussi demander au logiciel d’afficher les traductions dans les autres langues que vous connaissez.
Cette préférence se trouve sous l’onglet « {{int:prefs-editing}} ».
N’hésitez pas à parcourir également les autres préférences.

Allez maintenant à votre [[Special:Preferences|page de préférences]] puis revenez à cette page.',
	'translate-fs-settings-skip' => 'J’ai fini. Laissez-moi continuer.',
	'translate-fs-userpage-text' => 'Vous devez maintenant créer une page utilisateur.

Veuillez écrire quelque chose à propos de vous : qui vous êtes et ce que vous faites.
Cela aidera la communauté de {{SITENAME}} à travailler ensemble.
Sur {{SITENAME}}, il y a des gens de tous les coins du monde qui travaillent sur différentes langues et projets.

Dans la boîte pré-remplie ci-dessus, dans la toute première ligne, vous voyez <nowiki>{{#babel:en-2}}</nowiki>.
Veuillez la compléter avec votre connaissance des langues.
Le nombre qui suit le code de la langue décrit comment vous maîtrisez cette langue.
Les valeurs possibles sont :
* 1 - un peu
* 2 - connaissances de base
* 3 - bonnes connaissances
* 4 - niveau bilingue
* 5 - vous utilisez cette langue de manière professionnelle, par exemple en tant que traducteur professionnel.

Pour votre langue maternelle, ignorez le niveau et n’utilisez que le code de la langue.
Exemple : si votre langue maternelle est le tamoul et que vous parlez bien l’anglais et un peu le swahili, écrivez :
<code><nowiki>{{#babel:ta|en-3|sw-1}}</nowiki></code>

Si vous ne connaissez pas le code d’une langue donnée, vous pouvez le chercher maintenant dans la liste ci-dessous.',
	'translate-fs-userpage-submit' => 'Créer ma page utilisateur',
	'translate-fs-userpage-done' => 'Bien joué ! Vous avez à présent une page utilisateur.',
	'translate-fs-permissions-text' => 'Vous devez faire une demande pour être ajouté au groupe des traducteurs.

Jusqu’à ce que nous ayons réparé le code, merci d’aller sur [[Project:Translator]] et de suivre les instructions.
Revenez ensuite à cette page.

Quand vous aurez rempli votre demande, un des membre de l’équipe de volontaires la vérifiera et l’approuvera dès que possible.
Merci d’être patient.

<del>Veuillez vérifier que la demande suivante est correctement remplie puis cliquez sur le bouton de demande.</del>',
	'translate-fs-target-text' => "Félicitations !
Vous pouvez maintenant commencer à traduire.

Ne vous inquiétez pas si cela vous paraît un peu nouveau et étrange.
Sur la [[Project list|liste des projets]] se trouve une vue d’ensemble des projets que vous pouvez contribuer à traduire.
Ces projets possèdent, pour la plupart, une page contenant une courte description et un lien « ''Traduire ce projet'' » qui vous mènera vers une page listant tous les messages non traduits.
Une liste de tous les groupes de messages avec l’[[Special:LanguageStats|état actuel de la traduction pour une langue donnée]] est aussi disponible.

Si vous sentez que vous avez besoin de plus d’informations avant de commencer à traduire, vous pouvez lire la [[FAQ|foire aux questions]].
La documentation peut malheureusement être périmée de temps à autres.
Si vous pensez que vous devriez pouvoir faire quelque chose, sans parvenir à trouver comment, n’hésitez pas à poser la question sur la [[Support|page support]].

Vous pouvez aussi contacter les autres traducteurs de la même langue sur [[Portal:$1|le portail de votre langue]].
Le portail lié est celui qui correspond à votre [[Special:Preferences|préférence de langue]] actuelle.
Veuillez la changer si nécessaire.",
	'translate-fs-email-text' => 'Merci de bien vouloir saisir votre adresse électronique dans [[Special:Preferences|vos préférences]] et la confirmer grâce au message qui vous sera envoyé.

Cela permettra aux autres utilisateurs de vous contacter par courrier électronique.
Vous recevrez aussi un courrier d’informations au plus une fois par mois.
Si vous ne souhaitez pas recevoir ce courrier d’informations, vous pouvez le désactiver dans l’onglet « {{int:prefs-misc}} » de vos [[Special:Preferences|préférences]].',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'firststeps' => 'Primeiros pasos',
	'firststeps-desc' => '[[Special:FirstSteps|Páxina especial]] para iniciar aos usuarios no uso da extensión Translate',
	'translate-fs-pagetitle-done' => '; feito!',
	'translate-fs-pagetitle' => 'Asistente para dar os primeiros pasos: $1',
	'translate-fs-signup-title' => 'Rexístrese',
	'translate-fs-settings-title' => 'Configure as súas preferencias',
	'translate-fs-userpage-title' => 'Cree a súa páxina de usuario',
	'translate-fs-permissions-title' => 'Solicite permisos de tradutor',
	'translate-fs-target-title' => 'Comece a traducir!',
	'translate-fs-email-title' => 'Confirme o seu enderezo de correo electrónico',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'firststeps' => 'Prěnje kroki',
	'firststeps-desc' => '[[Special:FirstSteps|Specialna strona]] za startowu pomoc na wikiju, kotryž rozšěrjenje Translate wužiwa',
	'translate-fs-pagetitle-done' => '- sčinjene!',
	'translate-fs-pagetitle' => 'Startowy asistent - $1',
	'translate-fs-signup-title' => 'Registrować',
	'translate-fs-settings-title' => 'Konfiguruj swoje nastajenja',
	'translate-fs-userpage-title' => 'Wutwor swoju wužiwarsku stronu',
	'translate-fs-permissions-title' => 'Wo přełožowanske prawa prosyć',
	'translate-fs-target-title' => 'Započń přełožk!',
	'translate-fs-email-title' => 'Wobkruć swoju e-mejlowu adresu',
	'translate-fs-intro' => "Witaj do startoweho asistenta projekta {{SITENAME}}.
Dóstanješ nawod krok po kroku, kak so z přełožowarjom stanješ.
Na kóncu móžeš ''zdźělenki programoweho powjercha'' wšěch podpěrowanych projektow na {{SITENAME}} přełožić.",
	'translate-fs-signup-text' => '[[Image:HowToStart1CreateAccount.png|frame]]

W prěnim kroku dyrbiš so registrować, t. r. wužiwarske konto wutworić.

Přełožki, kotrež sy sčinił, budu so twojemu wužiwarskemu mjenu připokazować.
Wobraz naprawo ći pokazuje, kak dyrbiš pola wupjelnić.

Jeli sy hižo zregistrowany, $1přizjew so$2 město toho.
Hdyž sy zregistrowany, wróć so k tutej stronje.

$3Registrować$4',
	'translate-fs-settings-text' => 'Dźi nětko do swojich nastajenjow a změń znajmjeńša swoju powjerchowu rěc do rěče, do kotrejež chceš přełožić.

Twoja powjerchowa rěč wužiwa so jako standardna cilowa rěč.
Zabywa so lochko, rěč do praweje rěče změnić, tohodla so jara poručuje, ju nětko nastajić.

Hdyž sy jónu tu, móžeš tež softwaru prosyć, přełožki tež w druhich rěčach zwobraznić, kotrež rozumiš.
Tute nastajenje namakaš pod rajtarkom "{{int:prefs-editing}}".
Wobhladaj sej woměrje tež druhe nastajenja.

Dźi nětko k swojej [[Special:Preferences|stronje nastajenjow]] a wróć so potom k tutej stronje.',
	'translate-fs-settings-skip' => 'Sym hotowy.
Daj mi pokročować.',
	'translate-fs-userpage-submit' => 'Moju wužiwarsku stronu wutworić',
	'translate-fs-userpage-done' => 'Gratulacija! Maš nětko wužiwarsku stronu.',
	'translate-fs-email-text' => 'Prošu podaj swoju e-mejlowu adresu w [[Special:Preferences|swojich nastajenjach]] a wobkruć ju přez e-mejl, kotraž so ći sćele. 

To dowola druhim wužiwarjam, so z tobu přez e-mejl do zwisk stajić.
Dóstanješ tež powěsćowe listy, zwjetša jónkróć wob měsać.
Jeli nochceš powěsćowe listy dóstać, móžeš tutu opciju na rajtarku "{{int:prefs-misc}}" swojich [[Special:Preferences|preferencow]] znjemóžnić.',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'firststeps' => 'Prime passos',
	'firststeps-desc' => '[[Special:FirstSteps|Pagina special]] pro familiarisar le usatores de un wiki con le extension Translate',
	'translate-fs-pagetitle-done' => ' - finite!',
	'translate-fs-pagetitle' => 'Assistente de initiation - $1',
	'translate-fs-signup-title' => 'Crear un conto',
	'translate-fs-settings-title' => 'Configurar tu preferentias',
	'translate-fs-userpage-title' => 'Crear tu pagina de usator',
	'translate-fs-permissions-title' => 'Requestar permissiones de traductor',
	'translate-fs-target-title' => 'Comenciar a traducer!',
	'translate-fs-email-title' => 'Confirmar tu adresse de e-mail',
	'translate-fs-intro' => "Benvenite al assistente de initiation de {{SITENAME}}.
Tu essera guidate passo a passo trans le processo de devenir traductor.
Al fin tu potera traducer le ''messages de interfacie'' de tote le projectos supportate in {{SITENAME}}.",
	'translate-fs-signup-text' => '[[Image:HowToStart1CreateAccount.png|frame]]

In le prime passo tu debe crear un conto.

Le traductiones que tu facera essera attribuite a tu nomine de usator.
Le imagine al dextra demonstra como completar le formulario.

Si tu possede jam un conto in le sito, $1aperi un session$2.
Quando tu ha create un conto, per favor retorna a iste pagina.

$3Crear un conto$4',
	'translate-fs-settings-text' => 'Tu deberea ora visitar tu preferentias e,
al minus, cambiar le lingua de interfacie al lingua in le qual tu vole traducer.

Tu lingua de interfacie es usate automaticamente como lingua in le qual traducer.
Il es facile oblidar de cambiar al lingua correcte, dunque il es altemente recommendate de facer lo ora.

Intertanto, tu pote etiam demandar que le software presenta traductiones existente in altere linguas que tu cognosce.
Iste preferentia se trova sub le scheda "{{int:prefs-editing}}".
Sia libere de explorar etiam le altere preferentias.

Visita ora tu [[Special:Preferences|pagina de preferentias]] e postea retorna a iste pagina.',
	'translate-fs-settings-skip' => 'Io ha finite. Lassa me continuar.',
	'translate-fs-userpage-text' => 'Ora, tu debe crear un pagina de usator.

Per favor scribe alique super te; qui tu es e lo que tu face.
Isto adjutara le communitate de {{SITENAME}} a collaborar.
In {{SITENAME}} il ha personas de tote le mundo laborante a diverse linguas e projectos.

In le quadro precompletate hic supra, in le primissime linea, tu vide <nowiki>{{#babel:en-2}}</nowiki>.
Per favor completa isto con tu cognoscentia linguistic.
Le numero post le codice de lingua describe tu nivello de maestria del lingua.
Le optiones es:
* 1 - un poco
* 2 - cognoscentia de base
* 3 - bon cognoscentia
* 4 - nivello de parlante native
* 5 - tu usa le lingua professionalmente, per exemplo tu es traductor professional.

Si tu es un parlante native de un lingua, omitte le nivello de cognoscentia, usante solmente le codice de lingua.
Per exemplo: si tu parla tamil nativemente, anglese ben, e un poco de swahili, tu scriberea:
<code><nowiki>{{#babel:ta|en-3|sw-1}}</nowiki></code>

Si tu non cognosce le codice de un lingua, ora es un bon tempore pro cercar lo. Tu pote usar le lista hic infra.',
	'translate-fs-userpage-submit' => 'Crear mi pagina de usator',
	'translate-fs-userpage-done' => 'Ben facite! Tu ha ora un pagina de usator.',
	'translate-fs-permissions-text' => 'Ora, tu debe facer un requesta pro esser addite al gruppo de traductores.

Nos non ha ancora automatisate isto; pro le momento, per favor visita [[Project:Translator]] e seque le instructiones.
Postea, retorna a iste pagina.

Post que tu ha submittite tu requesta, un del membros del personal voluntari verificara tu requesta e lo approbara si tosto como possibile.
Per favor sia patiente.

<del>Verifica que le sequente requesta es correcte e complete, postea clicca super le button de requesta.</del>',
	'translate-fs-target-text' => "Felicitationes!
Tu pote ora comenciar a traducer.

Non te inquieta si isto te pare ancora nove e confundente.
In le pagina [[Project list]] il ha un summario del projectos al quales tu pote contribuer traductiones.
Le major parte del projectos ha un curte pagina de description con un ligamine \"''Traducer iste projecto''\", le qual te portara a un pagina que lista tote le messages non traducite.
Un lista de tote le gruppos de messages con le [[Special:LanguageStats|stato de traduction actual pro un lingua]] es etiam disponibile.

Si tu senti que tu ha besonio de comprender plus ante de traducer, tu pote leger le [[FAQ|folio a questiones]].
Infelicemente le documentation pote a vices esser obsolete.
Si il ah un cosa que tu pensa que tu deberea poter facer, ma non pote trovar como facer lo, non hesita a poner le question in le [[Support|pagina de supporto]].

Tu pote etiam contactar altere traductores del mesme lingua in [[Portal:\$1|le portal de tu lingua]].
Le portal liga a tu [[Special:Preferences|preferentia de lingua]] actual.
Per favor cambia lo si necessari.",
	'translate-fs-email-text' => 'Per favor entra tu adresse de e-mail in [[Special:Preferences|tu preferentias]] e confirma lo per medio del e-mail que te essera inviate.

Isto permitte que altere usatores te contacta via e-mail.
Tu recipera anque bulletines de novas al plus un vice per mense.
Si tu non vole reciper bulletines de novas, tu pote disactivar los in le scheda "{{int:prefs-misc}}" de tu [[Special:Preferences|preferentias]].',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'firststeps' => 'Éischt Schrëtt',
	'firststeps-desc' => "[[Special:FirstSteps|Spezialsäit]] fir datt Benotzer besser ukommen fir d'Erweiderung 'Translate' op enger Wiki ze benotzen",
	'translate-fs-pagetitle-done' => ' - fäerdeg!',
	'translate-fs-pagetitle' => 'Assistent fir unzefänken - $1',
	'translate-fs-signup-title' => 'Schreift Iech an',
	'translate-fs-settings-title' => 'Är Astellunge festleeën',
	'translate-fs-target-title' => 'Ufänke mat iwwersetzen!',
	'translate-fs-email-title' => 'Confirméiert är E-Mailadress',
	'translate-fs-settings-skip' => 'Ech si fäerdeg.
Loosst mech weidermaachen.',
	'translate-fs-userpage-done' => 'Gutt gemaach! dir hutt elo eng Benotzersäit.',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'firststeps' => 'Први чекори',
	'firststeps-desc' => '[[Special:FirstSteps|Специјална страница]] за помош со првите чекори на вики што го користи додатокот Преведување (Translate)',
	'translate-fs-pagetitle-done' => '- завршено!',
	'translate-fs-pagetitle' => 'Помошник „Како да започнете“ - $1',
	'translate-fs-signup-title' => 'Регистрација',
	'translate-fs-settings-title' => 'Поставете ги вашите нагодувања',
	'translate-fs-userpage-title' => 'Создајте своја корисничка страница',
	'translate-fs-permissions-title' => 'Барање на дозвола за преведување',
	'translate-fs-target-title' => 'Почнете со преведување!',
	'translate-fs-email-title' => 'Потврдете ја вашата е-пошта',
	'translate-fs-intro' => "Добредојдовте на помошникот за први чекори на {{SITENAME}}.
Овој помошник постепено ќе води низ постапката за станување преведувач.
Потоа ќе можете да преведувате ''посреднички (интерфејс) пораки'' за сите поддржани проекти на {{SITENAME}}.",
	'translate-fs-signup-text' => '[[Image:HowToStart1CreateAccount.png|frame]]
Најпрвин мора да се регистрирате.

Заслугите за преводите ќе се припишуваат на вашето корисничко име.
Сликата десно покажува како треба да се пополнат полињата.

Ако сте веќе регистрирани, сега $1најавете се$2.
Откога ќе се регистрирате, вратете се на оваа страница.

$3Регистрација$4',
	'translate-fs-settings-text' => 'Сега одете во вашите нагодувања и
барем сменете го јазикот на посредникот (интерфејсот) во јазикот на којшто ќе преведувате.

Јазикот на посредникот ќе се смета за ваш матичен целен јазик.
Може лесно да заборавите да го смените јазикот на исправниот, па затоа поставете го сега.

Додека сте тука, можете да побарате програмот да ги прикажува напревените преводи на други јазици.
Оваа функција ќе ја најдете во јазичето „{{int:prefs-editing}}“.
Најслободно истражувајте ги и другите поставки и можности.

Сега одете на [[Special:Preferences|вашите нагодувања]], па вратете се пак на оваа страница.',
	'translate-fs-settings-skip' => 'Завршив. Одиме понатаму.',
	'translate-fs-userpage-text' => 'Сега ќе треба да направите корисничка страница.

Напишете нешто за вас; кој сте и со што се занимавате.
Така заедницата на {{SITENAME}} ќе може да работи подобро.
На {{SITENAME}} има луѓе од целиот свет кои работат на различни јазици и проекти.

Во подготвената кутија горе, на најпрвиот ред ќе видите <nowiki>{{#babel:en-2}}</nowiki>.
Пополнете ја со јазикот или јазиците од кои имате познавања.
Бројката до јазичната кратенка го означува нивото на кое го владеете јазикот.
Еве ги можностите:
* 1 - малку
* 2 - основни познавања
* 3 - солидни познавања
* 4 - на ниво на мајчин
* 5 - го користите јазикот професионално, на пр. сте професионален преведувач.

Ако јазикот е ваш мајчин јазик, тогаш изоставете го нивото, и ставете го само јазичниот код (кратенка).
Пример: ако зборувате македонски од раѓање, англиски добро, и малку шпански, ќе внесете:
<code><nowiki>{{#babel:mk|en-3|es-1}}</nowiki></code>

Ако не го знаете јазичниот код на некој јазик, сега имате добра можност да го дознаете. Погледајте на списокот подолу.',
	'translate-fs-userpage-submit' => 'Создај корисничка страница',
	'translate-fs-userpage-done' => 'Одлично! Сега имате корисничка страница.',
	'translate-fs-permissions-text' => 'Сега ќе треба да поднесете барање за да ве стават во групата на преведувачи.

Додека не го поправиме овој код, одете на [[Project:Translator]] и проследете ги напатствијата.
Потоа вратете се на страницава.

Откако ќе го пополните барањето, доброволец од персоналот ќе го провери и одобри во најкраток можен рок.
Бидете трпеливи.

<del>Проверете дали следново барање е правилно пополнето, а потоа притиснете го копчето за поднесување на барањето.</del>',
	'translate-fs-target-text' => "Честитаме!
Сега можете да почнете со преведување.

Не плашете се ако сето ова сè уште ви изгледа ново и збунително.
Списокот [[Project list]] дава преглед на проектите каде можете да придонесувате со ваши преводи.
Највеќето проекти имаат страница со краток опис и врска „''Преведи го проектов''“, која ќе ве одвете до страница со сите непреведени пораки за тој проект.
Има и список на сите групи на пораки со [[Special:LanguageStats|тековниот статус на преведеност за даден јазик]].

Ако мислите дека треба да осознаете повеќе пред да почнете со преведување, тогаш прочитајте ги [[FAQ|често поставуваните прашања]].
Нажалост документацијата напати знае да биде застарена.
Ако има нешто што мислите дека би требало да можете да го правите, но не можете да дознаете како, најслободно поставете го прашањето на [[Support|страницата за поддршка]].

Можете и да се обратите кај вашите колеги што преведуваат на истиот јазик на [[Portal:$1|вашиот јазичен портал]].
На порталот се наведени тековните [[Special:Preferences|јазични нагодувања]].
Сменете ги ако се јави потреба.",
	'translate-fs-email-text' => 'Наведете ја вашата е-пошта во [[Special:Preferences|нагодувањата]] и потврдете ја преку пораката испратена на неа.

Ова им овозможува на корисниците да ве контактираат преку е-пошта.
На таа адреса ќе добивате и билтени со новости, највеќе еднаш месечно.
Ако не сакате да добиват билтени, можете да се отпишете преку јазичето „{{int:prefs-misc}}“ во вашите [[Special:Preferences|нагодувања]].',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'firststeps' => 'Eerste stappen',
	'firststeps-desc' => '[[Special:FirstSteps|Speciale pagina]] voor het op gang helpen van gebruikers op een wiki met de uitbreiding Translate',
	'translate-fs-pagetitle-done' => ' - afgerond!',
	'translate-fs-pagetitle' => 'Aan de slag - $1',
	'translate-fs-signup-title' => 'Registreren',
	'translate-fs-settings-title' => 'Uw voorkeuren instellen',
	'translate-fs-userpage-title' => 'Uw gebruikerspagina aanmaken',
	'translate-fs-permissions-title' => 'Vertaalrechten aanvragen',
	'translate-fs-target-title' => 'Beginnen met vertalen!',
	'translate-fs-email-title' => 'Uw e-mailadres bevestigen',
	'translate-fs-intro' => 'Welkom bij de wizard Aan de slag van {{SITENAME}}.
We loodsen u stap voor stap door het proces van vertaler worden.
Aan het einde kunt u alle door {{SITENAME}} ondersteunde projecten vertalen.',
	'translate-fs-signup-text' => '[[Image:HowToStart1CreateAccount.png|frame]]

In de eerste stap moet u registreren.

Uw gebruikersnaam wordt gebruikt als naamsvermelding voor uw vertalingen.
De afbeelding rechts geeft aan hoe u de velden moet invullen.

Als u al bent geregistreerd, dan kunt u zich $1aanmelden$2.
Kom terug naar deze pagina als u bent aangemeld.

$3Registreren$4',
	'translate-fs-settings-text' => 'Ga nu naar uw voorkeuren en wijzig tenminste de interfacetaal naar de taal waarin u gaat vertalen.

Uw interfacetaal wordt gebruikt als de standaardtaal waarin u gaat vertalen.
Het is makkelijk te vergeten de taal te wijzigen, dus maak die instelling vooral nu.

Als u toch uw instellingen aan het wijzigen bent, kunt u ook een instelling maken om vertalingen in andere talen als hulpje weer te geven.
Deze instellingen is te vinden in het tabblad "{{int:prefs-editing}}".
Voel u vrij om ook andere instellingen aan te passen.

Ga nu naar uw [[Special:Preferences|voorkeuren]] en kom na het wijzigen terug naar deze pagina.',
	'translate-fs-settings-skip' => 'Ik ben klaar en wil doorgaan.',
	'translate-fs-userpage-text' => 'Maak nu uw eigen gebruikerspagina aan.

Schrijf alstublieft iets over uzelf; wie u bent en wat u doet.
Dit helpt de gemeenschap van {{SITENAME}} samen te werken.
Op {{SITENAME}} werken mensen van over de hele wereld samen aan verschillende talen en projecten.

In het ingevulde formulier boven de eerste regel ziet u <nowiki>{{#babel:en-2}}</nowiki>.
Vul dit aan met uw eigen talenkennis.
Het getal achter de taalcode beschrijft hoe goed u een taal in schrift beheerst.
De mogelijkheden zijn:
* 1 - elementair niveau
* 2 - basisniveau
* 3 - gevorderd niveau
* 4 - moedertaalniveau
* 5 - u gebruikt de taal professioneel, bijvoorbeeld als professioneel vertaler.

Als u een taal als moedertaal spreekt, laat het niveau dan weg, en gebruik alleen de taalcode.
Bijvoorbeeld: uw moedertaal is Nederlands, u beheerst het Engels op gevorderd niveau, en Swahili op elementair niveau. Noteer dan:
<code><nowiki>{{#babel:nl|en-3|sw-1}}</nowiki></code>

Als u de taalcode van een taal niet kent, dan is dit een goed moment.
U kunt de lijst hieronder gebruiken.',
	'translate-fs-userpage-submit' => 'Mijn gebruikerspagina aanmaken',
	'translate-fs-userpage-done' => 'Goed gedaan!
U hebt nu een gebruikerspagina.',
	'translate-fs-permissions-text' => 'Nu moet u een verzoek doen om vertaalrechten te krijgen.

Totdat we de code wijzigen, moet u naar [[Project:Translator]] en daar de instructies volgen.
Kom daarna terug naar deze pagina.

Nadat u uw aanvraag hebt ingediend, controleert een medewerker zo snel mogelijk uw aanvraag.
Heb even geduld, alstublieft.

<del>Controleer of de onderstaande aanvraag correct is ingevuld en klik vervolgens op de knop.</del>',
	'translate-fs-target-text' => "Gefeliciteerd! 
U kunt nu beginnen met vertalen. 

Wees niet bang als het nog wat verwarrend aanvoelt.
In de [[Project list|Projectenlijst]] vindt u een overzicht van projecten waar u vertalingen aan kunt bijdragen.
Het merendeel van de projecten heeft een korte beschrijvingspagina met een verwijzing \"''Dit project vertalen''\", die u naar een pagina leidt waarop alle onvertaalde berichten worden weergegeven.
Er is ook een lijst met alle berichtengroepen beschikbaar met de [[Special:LanguageStats|huidige status van de vertalingen voor een taal]].

Als u denkt dat u meer informatie nodig hebt voordat u kunt beginnen met vertalen, lees dan de [[FAQ|Veel gestelde vragen]].
Helaas kan de documentatie soms verouderd zijn.
Als er iets is waarvan u denkt dat het mogelijk moet zijn, maar u weet niet hoe, aarzel dan niet om het te vragen op de [[Support|pagina voor ondersteuning]].

U kunt ook contact opnemen met collegavertalers van dezelfde taal op [[Portal:\$1|uw taalportaal]].
Deze verwijzing verwijst naar het portaal voor de taal die u hebt ingesteld als uw [[Special:Preferences|voorkeurstaal]].
Wijzig deze als nodig.",
	'translate-fs-email-text' => 'Geef uw e-mail adres in in [[Special:Preferences|uw voorkeuren]] en bevestig het via de e-mail die naar u verzonden is.

Dit makt het mogelijk dat andere gebruikers contact met u opnemen per e-mail.
U ontvangt dan ook maximaal een keer per maand de nieuwsbrief.
Als u geen nieuwsbrieven wilt ontvangen, dan kunt u dit aangeven in het tabblad "{{int:prefs-misc}}" van uw [[Special:Preferences|voorkeuren]].',
);

/** Portuguese (Português)
 * @author Hamilton Abreu
 */
$messages['pt'] = array(
	'firststeps' => 'Primeiros passos',
	'firststeps-desc' => '[[Special:FirstSteps|Página especial]] para familiarizar os utilizadores com o uso da extensão Translate numa wiki',
	'translate-fs-pagetitle-done' => ' - terminado!',
	'translate-fs-pagetitle' => 'Assistente de iniciação - $1',
	'translate-fs-signup-title' => 'Registe-se',
	'translate-fs-settings-title' => 'Configure as suas preferências',
	'translate-fs-userpage-title' => 'Crie a sua página de utilizador',
	'translate-fs-permissions-title' => 'Solicite permissões de tradutor',
	'translate-fs-target-title' => 'Comece a traduzir!',
	'translate-fs-email-title' => 'Confirme o seu endereço de correio electrónico',
	'translate-fs-intro' => "Bem-vindo ao assistente de iniciação da {{SITENAME}}.
Será conduzido passo a passo através do processo necessário para se tornar um tradutor.
No fim, será capaz de traduzir as ''mensagens da interface'' de todos os projectos suportados na {{SITENAME}}.",
	'translate-fs-signup-text' => '[[Image:HowToStart1CreateAccount-pt.png|frame]]

No primeiro passo precisa de se registar.

A autoria das suas contribuições é atribuída ao seu nome de utilizador.
A imagem à direita mostra como deve preencher os campos.

Se já se registou antes, então $1autentique-se$2.
Depois de estar registado, volte a esta página, por favor.

$3Registar$4',
	'translate-fs-settings-text' => 'Agora deve ir até as suas preferências e, pelo menos, configurar na língua da interface a língua para a qual vai traduzir.

Por padrão, a sua língua da interface é usada como a língua de destino das traduções.
É fácil esquecer-se de alterar a língua para a correcta, por isso é altamente recomendado que a configure agora.

Enquanto está nas preferências, pode também pedir ao software que apresente as traduções noutras línguas que também conheça.
Esta configuração pode ser encontrada no separador «{{int:prefs-editing}}».
Esteja à vontade para explorar também as restantes configurações.

Vá agora à sua [[Special:Preferences|página de preferências]] e depois volte a esta página.',
	'translate-fs-settings-skip' => 'Terminei.
Passar ao seguinte.',
	'translate-fs-userpage-text' => 'Agora precisa de criar uma página de utilizador.

Escreva qualquer coisa sobre si, por favor; descreva quem é e o que faz.
Isto ajudará a comunidade da {{SITENAME}} a trabalhar em conjunto.
Na {{SITENAME}} existem pessoas de todo o mundo a trabalhar emlínguas e projectos diferentes.

Na caixa que foi introduzida acima, verá na primeira linha <nowiki>{{#babel:en-2}}</nowiki>.
Preencha-a com o seu conhecimento de línguas.
O número a seguir ao código da língua descreve o seu grau de conhecimento dessa língua.
As alternativas são:
* 1 - nível básico
* 2 - nível médio
* 3 - nível avançado
* 4 - nível quase nativo
* 5 - nível profissional (usa a língua profissionalmente, por exemplo, é um tradutor profissional).

Se a língua é a sua língua materna, não coloque nenhum número e use somente o código da língua.
Por exemplo: se o português é a sua língua materna, fala bem inglês e um pouco de francês, deve escrever: <tt><nowiki>{{#babel:pt|en-3|fr-1}}</nowiki></tt>

Se desconhece o código de língua de uma língua, esta é uma boa altura para descobri-lo.
Pode usar a lista abaixo.',
	'translate-fs-userpage-submit' => 'Criar a minha página de utilizador',
	'translate-fs-userpage-done' => 'Bom trabalho! Agora tem uma página de utilizador.',
	'translate-fs-permissions-text' => 'Agora precisa de criar um pedido para ser adicionado ao grupo dos tradutores.

Até termos corrigido o software, vá a [[Project:Translator]] e siga as instruções, por favor.
Depois volte a esta página.

Após ter submetido o pedido, um dos membros da equipa de voluntários irá verificar o seu pedido e aprová-lo logo que possível.
Tenha alguma paciência, por favor.

<del>Verifique que o seguinte pedido está preenchido correctamente e depois clique o botão.</del>',
	'translate-fs-target-text' => 'Parabéns!
Agora pode começar a traduzir.

Não se amedronte se tudo lhe parece ainda novo e confuso.
Na [[Project list|lista de projectos]] há um resumo dos projectos para os quais pode contribuir com traduções.
A maioria dos projectos tem uma página de descrição breve com um link «Traduza este projecto», que o leva a uma página com todas as mensagens ainda por traduzir.
Também está disponível uma lista de todos os grupos de mensagens com o [[Special:LanguageStats|estado presente de tradução para uma língua]].

Se acredita que precisa de compreender melhor antes de começar a traduzir, pode ler as [[FAQ|perguntas frequentes]].
Infelizmente a documentação pode, por vezes, estar desactualizada.
Se há alguma coisa que acha que devia poder fazer, mas não consegue descobrir como, não hesite em perguntar na [[Support|página de suporte]].

Pode também contactar os outros tradutores da mesma língua no [[Portal:$1|portal da sua língua]].
O portal leva-o à [[Special:Preferences|língua seleccionada nas suas preferências]].
Altere-a se for necessário, por favor.',
	'translate-fs-email-text' => 'Forneça o seu endereço de correio electrónico nas [[Special:Preferences|suas preferências]] e confirme-o a partir da mensagem que lhe será enviada.

Isto permite que os outros utilizadores o contactem por correio electrónico.
Também receberá newsletters, no máximo uma vez por mês.
Se não deseja receber as newsletters, pode optar por não recebê-las no separador "{{int:prefs-misc}}" das suas [[Special:Preferences|preferências]].',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Giro720
 */
$messages['pt-br'] = array(
	'translate-fs-signup-text' => '[[Image:HowToStart1CreateAccount-pt.png|frame]]

No primeiro passo, você precisa se registar.

A autoria das suas contribuições é atribuída ao seu nome de usuário.
A imagem à direita mostra como deve preencher os campos.

Se já você já se registou, então $1autentique-se$2.
Depois de estar registado, volte a esta página, por favor.

$3Registar$4',
);

/** Russian (Русский)
 * @author G0rn
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'firststeps' => 'Первые шаги',
	'firststeps-desc' => '[[Special:FirstSteps|Служебная страница]] для новых пользователей вики с установленным расширением перевода',
	'translate-fs-pagetitle-done' => '— сделано!',
	'translate-fs-pagetitle' => 'Программа начального обучения — $1',
	'translate-fs-signup-title' => 'Зарегистрируйтесь',
	'translate-fs-settings-title' => 'Произведите настройку',
	'translate-fs-userpage-title' => 'Создайте свою страницу участника',
	'translate-fs-permissions-title' => 'Запросите права переводчика',
	'translate-fs-target-title' => 'Начните переводить!',
	'translate-fs-email-title' => 'Подтвердите ваш адрес электронной почты',
	'translate-fs-intro' => 'Добро пожаловать в программу начального обучения проекта {{SITENAME}}.
Шаг за шагом вы будете проведены по обучающей программе переводчиков.
По окончанию обучения вы сможете переводить интерфейсные сообщения всех поддерживаемых проектов {{SITENAME}}.',
	'translate-fs-signup-text' => '[[Image:HowToStart1CreateAccount.png|frame]]

Для начала вам необходимо зарегистрироваться.

Авторство ваших переводов будет присваиваться имени вашей учётной записи.
Изображение справа показывает, как надо заполнять поля.

Если вы уже зарегистрированы, то вместо этого $1представьтесь$2.
После регистрации, пожалуйста, вернитесь на эту страницу.

$3Зарегистрироваться$4',
	'translate-fs-settings-text' => 'Теперь вам надо пройти в настройки и
изменить язык интерфейса на язык, на который вы собираетесь переводить.

Ваш язык интерфейса будет использоваться как язык для перевода по умолчанию.
Поскольку легко забыть изменить язык на правильный, установка его сейчас крайне рекомендуется.

Пока вы там, вы также можете включить отображение переводов на другие языки, которые вы знаете.
Эта опция находится во вкладке «{{int:prefs-editing}}».
Вы также можете изучить и другие настройки.

Сейчас пройдите на свою [[Special:Preferences|страницу настроек]], а потом вернитесь на эту страницу.',
	'translate-fs-settings-skip' => 'Готово. Перейти далее.',
	'translate-fs-userpage-text' => 'Теперь вам надо создать свою страницу участника.

Пожалуйста, напишите что-нибудь о себе; кто вы и чем вы занимаетесь.
Это поможет сообществу {{SITENAME}} работать вместе.
На {{SITENAME}} собираются люди со всего мира для работы над различными языками и проектами.

В предварительно заполненной форме наверху в самой первой строке указано <nowiki>{{#babel:en-2}}</nowiki>.
Пожалуйста, заполните этот блок в соответствии с вашим знанием языка.
Номер после кода языка показывает, насколько хорошо вы знаете этот язык.
Возможные варианты:
* 1 — небольшое знание
* 2 — базовое знание
* 3 — хорошее знание
* 4 — владение на уровне родного языка
* 5 — вы используете язык профессионально, например, если вы профессиональный переводчик.

Если этот язык является вашим родным, то уберите цифру и дефис, оставьте только код языка.
Пример: если тамильский язык является вашим родным, а также у вас есть хорошее знание английского и небольшое знание суахили, то вам нужно написать:
<code><nowiki>{{#babel:ta|en-3|sw-1}}</nowiki></code>

Если вы не знаете код языка, то сейчас самое время его узнать. Вы можете использовать список ниже.',
	'translate-fs-userpage-submit' => 'Создать мою страницу участника',
	'translate-fs-userpage-done' => 'Отлично! Теперь у вас есть страница участника.',
	'translate-fs-permissions-text' => 'Теперь вам необходимо подать запрос на добавление в группу переводчиков.

Пока мы не исправим код, пожалуйста, пройдите на страницу [[Project:Translator]] и следуйте инструкциями, а после этого вернитесь сюда.

После того, как вы подали запрос, один из волонтёров из команды сайта проверит его и одобрит как можно скорее.
Пожалуйста, будьте терпеливы.

<del>Убедитесь, что следующий запрос корректно заполнен и нажмите кнопку отправки.</del>',
	'translate-fs-target-text' => "Поздравляем! 
Теперь вы можете начать переводить.

Не бойтесь, если что-то до сих пор кажется новым и запутанным для вас.
В [[Project list|списке проектов]] находится обзор проектов, для которых вы можете осуществлять перевод.
Большинство проектов имеют небольшую страницу с описанием и ссылкой \"''Translate this project''\", которая ведёт на страницу со списком всех непереведённых сообщений.
Также имеется список всех групп сообщений с [[Special:LanguageStats|текущим статусом перевода для языка]].

Если вам кажется, что необходимо узнать больше перед началом перевода, то вы можете прочитать [[FAQ|часто задаваемые вопросы]].
К сожалению, документация иногда может быть устаревшей.
Если есть что-то, что по вашему мнению вы можете сделать, но не знаете как, то не стесняйтесь спросить об этом на [[Support|странице поддержки]].

Вы также можете связаться с переводчиками на тот же язык на [[Portal:\$1|портале вашего языка]].
Ссылка ведёт на портал языка, указанного в ваших [[Special:Preferences|настройках]].
Пожалуйста, измените его, если это необходимо.",
	'translate-fs-email-text' => 'Пожалуйста, укажите ваш адрес электронной почты в [[Special:Preferences|настройках]] и подтвердите его из письма, которое вам будет отправлено.

Это позволяет другим участникам связываться с вами по электронной почте.
Вы также будете получать новостную рассылку раз в месяц.
Если вы не хотите получать рассылку, то вы можете отказаться от неё на вкладке «{{int:prefs-misc}}» ваших [[Special:Preferences|настроек]].',
);
