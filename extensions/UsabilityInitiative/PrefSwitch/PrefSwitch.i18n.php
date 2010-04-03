<?php
/**
 * Internationalisation for Usability Initiative PrefSwitch extension
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Roan Kattouw
 */
$messages['en'] = array(
	'prefswitch' => 'Usability Initiative preference switch',
	'prefswitch-desc' => 'Allow users to switch sets of preferences',
	'prefswitch-survey-true' => 'Yes',
	'prefswitch-survey-false' => 'No',
	'prefswitch-survey-submit-off' => 'Turn new features off',
	'prefswitch-survey-cancel-off' => 'If you would like to continue using the new features, you can return to $1.',
	'prefswitch-survey-submit-feedback' => 'Send feedback',
	'prefswitch-survey-cancel-feedback' => 'If you do not want to provide feedback, you can return to $1.',
	'prefswitch-survey-question-likedislike' => 'What did you like about the new features?
What did you dislike about the features?',
	'prefswitch-survey-question-whyoff' => 'Why are you turning off the new features?
Please select all that apply.',
	'prefswitch-survey-answer-whyoff-hard' => 'It was too hard to use.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'It did not function properly.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'It did not perform predictably.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'I did not like the way it looked.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'I did not like the new tabs and layout.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'I did not like the new toolbar.',
	'prefswitch-survey-answer-whyoff-other' => 'Other reason:',
	'prefswitch-survey-question-browser' => 'Which browser do you use?',
	'prefswitch-survey-answer-browser-ie5' => 'Internet Explorer 5',
	'prefswitch-survey-answer-browser-ie6' => 'Internet Explorer 6',
	'prefswitch-survey-answer-browser-ie7' => 'Internet Explorer 7',
	'prefswitch-survey-answer-browser-ie8' => 'Internet Explorer 8',
	'prefswitch-survey-answer-browser-ff1' => 'Firefox 1',
	'prefswitch-survey-answer-browser-ff2' => 'Firefox 2',
	'prefswitch-survey-answer-browser-ff3' => 'Firefox 3',
	'prefswitch-survey-answer-browser-cb' => 'Google Chrome Beta',
	'prefswitch-survey-answer-browser-c1' => 'Google Chrome 1',
	'prefswitch-survey-answer-browser-c2' => 'Google Chrome 2',
	'prefswitch-survey-answer-browser-c3' => 'Google Chrome 3',
	'prefswitch-survey-answer-browser-c4' => 'Google Chrome 4',
	'prefswitch-survey-answer-browser-s3' => 'Safari 3',
	'prefswitch-survey-answer-browser-s4' => 'Safari 4',
	'prefswitch-survey-answer-browser-o9' => 'Opera 9',
	'prefswitch-survey-answer-browser-o9.5' => 'Opera 9.5',
	'prefswitch-survey-answer-browser-o10' => 'Opera 10',
	'prefswitch-survey-answer-browser-other' => 'Other browser:',
	'prefswitch-survey-question-os' => 'Which operating system do you use?',
	'prefswitch-survey-answer-os-windows' => 'Windows',
	'prefswitch-survey-answer-os-windowsmobile' => 'Windows Mobile',
	'prefswitch-survey-answer-os-macos' => 'Mac OS',
	'prefswitch-survey-answer-os-iphoneos' => 'iPhone OS',
	'prefswitch-survey-answer-os-linux' => 'Linux',
	'prefswitch-survey-answer-os-other' => 'Other operating system:',
	'prefswitch-survey-question-res' => 'What is the resolution of your screen?',
	'prefswitch-title-on' => 'What\'s new?',
	'prefswitch-title-switched-on' => 'Enjoy!',
	'prefswitch-title-off' => 'Turn new features off',
	'prefswitch-title-switched-off' => 'Thanks',
	'prefswitch-title-feedback' => 'Feedback',
	'prefswitch-success-on' => 'New features are now turned on.',
	'prefswitch-success-off' => 'New features are now turned off.',
	'prefswitch-success-feedback' => 'Your feedback has been sent.',
	'prefswitch-return' => '<hr style="clear:both">
Return to <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "We have been working hard to make things easier for our users. We are excited to share some improvements, including a new look and feel and simplified editing features.  Improving the usability of our projects is a priority of the Wikimedia Foundation and we will be sharing more updates in the future.

[[File:UsabilityNavigation.png|right|link=|Screenshot of the new navigation]]
[[File:UsabilityToolbar.png|right|link=|Screenshot of the enhanced edit toolbar]]
[[File:UsabilityDialogs.png|right|link=|Screenshot of the new content generation dialogs]]
===Here's what we have changed===
* Navigation: We have improved the navigation for reading and editing pages. Now, the tabs at the top of each page more clearly define whether you are viewing the page or discussion page, and whether you are reading or editing a page.
* Editing toolbar improvements:  We have reorganized the editing toolbar to make easier to use.  Now, formatting pages is simpler and more intuitive.
* Link wizard:  An easy-to-use tool allows you to add links to other Wikipedia pages as well as links to external sites.
* Search improvements: We have improved search suggestions to get you to the page you are looking for more quickly.
* Other new features:  We have also introduced a table wizard to make creating tables easier and find and a replace feature to simplify page editing.",
	'prefswitch-main-on' => "===Feedback?===
We would love to hear from you.  Please visit our <span class=\"plainlinks\">[$1 feedback page]</span>.
===Take me back!===
If you would like to turn off the new features, please <span class=\"plainlinks\">[$2 click here]</span>.",
	'prefswitch-main-off' => "===Try them out!===
If you would like to turn on the new features, please <span class=\"plainlinks\">[$1 click here]</span>.",
	'prefswitch-survey-intro-feedback' => "We would love to hear from you.
Please fill out the optional survey below.",
	'prefswitch-survey-intro-off' => 'Thanks for trying out our new features.
To help us improve them, please fill out the optional survey below.',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'prefswitch-survey-false' => 'Не',
	'prefswitch-survey-submit-off' => 'Выключыць новыя мажлівасьці',
	'prefswitch-survey-cancel-off' => 'Калі Вы жадаеце працягваць выкарыстаньне новых магчымасьцяў, Вы можаце вярнуцца да $1',
);

/** Breton (Brezhoneg)
 * @author Y-M D
 */
$messages['br'] = array(
	'prefswitch-survey-true' => 'Ya',
	'prefswitch-survey-false' => 'Nann',
	'prefswitch-survey-submit-feedback' => 'Roit ho soñj',
	'prefswitch-survey-answer-whyoff-hard' => 'Start e oa da embreger.',
	'prefswitch-survey-answer-whyoff-didntwork' => "Ne'z ae ket plaen en-dro.",
	'prefswitch-survey-answer-whyoff-notpredictable' => "Ne'z ae ket en-dro en un doare poellek.",
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Ne blije ket din an tres anezhañ.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => "N'on ket bet plijet gant an ivinelloù nevez hag an aozadur nevez.",
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Ne blije ket din ar varrenn ostilhoù nevez.',
	'prefswitch-survey-answer-whyoff-other' => 'Abeg all :',
	'prefswitch-survey-question-browser' => 'Peseurt merdeer a rit gantañ ?',
	'prefswitch-survey-answer-browser-other' => 'Merdeer all :',
	'prefswitch-survey-question-os' => 'Peseurt reizhiad korvoiñ a rit gantañ ?',
	'prefswitch-survey-answer-os-other' => 'Reizhiad korvoiñ all :',
	'prefswitch-survey-question-res' => 'Petra eo spisder ho skramm ?',
	'prefswitch-title-on' => "Petra 'zo da heul ?",
	'prefswitch-title-switched-off' => 'Trugarez',
	'prefswitch-return' => '<hr style="clear:both">
Distreiñ da <span class="plainlinks">[$1 $2]</span>.',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'prefswitch' => 'Voorkeuren voor Bruikbaarheidsinitiatief wisselen',
	'prefswitch-desc' => 'Maakt het mogelijk om een groep instellingen te wijzigen',
	'prefswitch-survey-true' => 'Ja',
	'prefswitch-survey-false' => 'Nee',
	'prefswitch-survey-submit-off' => 'Nieuwe mogelijkheden uitschakelen',
	'prefswitch-survey-cancel-off' => 'Als u de nieuwe mogelijkheden wilt blijven gebruiken, kunt u terugkeren naar $1',
	'prefswitch-survey-submit-feedback' => 'Terugkoppeling geven',
	'prefswitch-survey-cancel-feedback' => 'Als u geen terugkoppeling wilt geven, kunt u teruggaan naar $1.',
	'prefswitch-survey-question-likedislike' => 'Wat vond u goed aan de nieuwe mogelijkheden?
Wat vond u niet goed aan de mogelijkheden?',
	'prefswitch-survey-question-whyoff' => 'Waarom wilt u de nieuwe mogelijkheden uitschakelen?
Vink alstublieft alle mogelijkheden die van toepassing zijn aan.',
	'prefswitch-survey-answer-whyoff-hard' => 'Het gebruik was te moeilijk.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'De wijzigingen functioneerden niet correct.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Het reageerde niet voorspelbaar.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Het zag er niet zo uit als ik wilde.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Ik vond de nieuwe tabbladen en het uiterlijk niet prettig.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Ik vond de nieuwe werkbalk niet prettig werken.',
	'prefswitch-survey-answer-whyoff-other' => 'Andere reden:',
	'prefswitch-survey-question-browser' => 'Welke browser gebruikt u?',
	'prefswitch-survey-answer-browser-other' => 'Andere browser:',
	'prefswitch-survey-question-os' => 'Welk besturingssysteem gebruikt u?',
	'prefswitch-survey-answer-os-other' => 'Ander besturingssysteem:',
	'prefswitch-survey-question-res' => 'Wat is uw beeldschermresolutie?',
	'prefswitch-title-on' => 'Wat is er nieuw?',
	'prefswitch-title-switched-on' => 'Geniet ervan!',
	'prefswitch-title-off' => 'Nieuwe mogelijkheden uitschakelen',
	'prefswitch-title-switched-off' => 'Bedankt',
	'prefswitch-title-feedback' => 'Terugkoppeling',
	'prefswitch-success-on' => 'De nieuwe mogelijkheden zijn nu ingeschakeld.',
	'prefswitch-success-off' => 'De nieuwe mogelijkheden zijn nu uitgeschakeld.',
	'prefswitch-success-feedback' => 'Uw terugkoppeling is verzonden.',
	'prefswitch-return' => '<hr style="clear:both">
Terug naar <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "We hebben hard gewerkt om dingen makkelijker te maken voor onze gebruikers.
We zijn verheugd om een aantal verbeteringen met u te delen, inclusief een nieuw uiterlijk en een vereenvoudigde manier om pagina's te bewerken.
Het verbeteren van de gebruiksvriendelijkheid van onze projecten is een prioriteit van de Wikimedia Foundation en we verwachten in de toekomst nog verder te kunnen gaan.

[[File:UsabilityNavigation.png|right|link=|Schermafbeelding van de nieuwe navigatie]]
[[File:UsabilityToolbar.png|right|link=|Schermafbeelding van de uitgebreide bewerkingswerkbalk]]
[[File:UsabilityDialogs.png|right|link=|Schermafbeelding van de nieuwe inhoudsbewerkingsdialogen]]

=== Hier is wat we hebben veranderd ===
* Navigatie : We hebben de navigatie voor het lezen en bewerken van pagina's verbeterd.
De tabbladen bovenaan de pagina geven beter aan of u een pagina bekijkt of een overlegpagina, en of u een pagina aan het bekijken of aan het bewerken bent.
* Verbeteringen aan de werkbalk: We hebben de werkbalk volledig herontworpen zodat deze eenvoudiger te gebruiken is. Nu is het bewerken van pagina's eenvoudiger en intuïtiever.
* Hulp bij verwijzingen: een eenvoudig hulpmiddel bij het toevoegen van verwijzingen naar andere pagina's in Wikipedia en externe websites.
* Verbeteringen in het zoeken: we hebben zoeksuggesties verbeterd zodat u de pagina die u zoekt sneller vindt.
* Andere nieuwe mogelijkheden: Wij hebben ook een tabelhulpmiddel toegevoegd om het maken van tabellen te vereenvoudigen en een hulpmiddel voor zoeken en vervangen om het bewerken van pagina's te vereenvoudigen.",
	'prefswitch-main-on' => '=== Terugkoppeling? ===
Wij horen graag van u.
Bezoek onze <span class="plainlinks">[$1 pagina voor terugkoppeling].</span>

=== Nee, bedankt! ===
<span class="plainlinks">[$2 Klik om de nieuwe mogelijkheden uit te schakelen]</span>.',
	'prefswitch-main-off' => '===Uitproberen!===
<span class="plainlinks">[$1 Klik hier om de nieuwe mogelijkheden in te schakelen]</span>.',
	'prefswitch-survey-intro-feedback' => 'We horen graag van u.
Vul de optionele vragenlijst hieronder in.',
	'prefswitch-survey-intro-off' => 'Bedankt voor het uitproberen van onze nieuwe mogelijkheden.
Vul de onderstaande vragenlijst in om ons te helpen ze verder te verbeteren.',
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'prefswitch' => 'Włącznik preferencji inicjatywy użyteczności',
	'prefswitch-desc' => 'Pozwala użytkownikom przełączać zestawy preferencji',
	'prefswitch-survey-true' => 'Tak',
	'prefswitch-survey-false' => 'Nie',
	'prefswitch-survey-submit-off' => 'Wyłącz nowe funkcjonalności',
	'prefswitch-survey-cancel-off' => 'Jeśli chcesz nadal korzystać z nowych funkcjonalności, możesz powrócić do $1.',
	'prefswitch-survey-submit-feedback' => 'Wyślij opinię',
	'prefswitch-survey-cancel-feedback' => 'Jeśli nie chcesz przesłać swojej opinii, możesz powrócić do $1.',
	'prefswitch-survey-question-likedislike' => 'Które nowe funkcjonalności Ci się podobają?
Co Ci się w nich nie spodobało?',
	'prefswitch-survey-question-whyoff' => 'Dlaczego rezygnujesz z korzystania z nowych funkcjonalności?
Należy wybrać wszystkie pasujące odpowiedzi.',
	'prefswitch-survey-answer-whyoff-hard' => 'Korzystanie było zbyt trudne.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Nie działało poprawnie.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Działało w sposób nieprzewidywalny.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Nie podoba mi się wygląd.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Nie podobają mi się nowe zakładki i układ.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Nie podoba mi się nowy pasek narzędzi.',
	'prefswitch-survey-answer-whyoff-other' => 'Inna przyczyna',
	'prefswitch-survey-question-browser' => 'Z jakiej korzystasz przeglądarki?',
	'prefswitch-survey-answer-browser-other' => 'Inna przeglądarka',
	'prefswitch-survey-question-os' => 'Z jakiego systemu operacyjnego korzystasz?',
	'prefswitch-survey-answer-os-other' => 'Inny system operacyjny',
	'prefswitch-survey-question-res' => 'Z ekranu o jakiej rozdzielczości korzystasz?',
	'prefswitch-title-on' => 'Co nowego?',
	'prefswitch-title-switched-on' => 'Super!',
	'prefswitch-title-off' => 'Wyłącz nowe funkcjonalności',
	'prefswitch-title-switched-off' => 'Dziękujemy',
	'prefswitch-title-feedback' => 'Opinia',
	'prefswitch-success-on' => 'Nowe funkcjonalności są obecnie włączone.',
	'prefswitch-success-off' => 'Nowe funkcjonalności są obecnie wyłączone.',
	'prefswitch-success-feedback' => 'Twoja opinia została przesłana.',
	'prefswitch-return' => '<hr style="clear:both">
Powrót do <span class="plainlinks">[$1 $2]</span>.',
);

