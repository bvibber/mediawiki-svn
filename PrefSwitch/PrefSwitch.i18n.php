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
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'prefswitch-survey-false' => 'Не',
	'prefswitch-survey-submit-off' => 'Выключыць новыя магчымасьці',
	'prefswitch-survey-cancel-off' => 'Калі Вы жадаеце працягваць выкарыстаньне новых магчымасьцяў, Вы можаце вярнуцца да $1.',
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

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'prefswitch' => 'Baskulo por preferencoj de Uzebleco Initiato',
	'prefswitch-desc' => 'Permesi al uzantoj ŝanĝi arojn da preferencoj',
	'prefswitch-survey-true' => 'Jes',
	'prefswitch-survey-false' => 'Ne',
	'prefswitch-survey-submit-off' => 'Malŝalti novajn funkciojn',
	'prefswitch-survey-cancel-off' => 'Se vi volus daŭri uzante la novajn funkciojn, vi povus reiri al $1.',
	'prefswitch-survey-submit-feedback' => 'Sendi komenton',
	'prefswitch-survey-cancel-feedback' => 'Se vi ne volas doni komenton, vi povas reiri al $1.',
	'prefswitch-survey-question-likedislike' => 'Kio plaĉas al vi pri la novaj funkcioj?
Kio malplaĉas al vi pri la novaj funkcioj?',
	'prefswitch-survey-question-whyoff' => 'Kial vi malŝaltas la novajn funkciojn?
Bonvolu elekti ĉiujn taŭgaĵojn.',
	'prefswitch-survey-answer-whyoff-hard' => 'Ĝi estis tro malfacila uzi.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Ĝi ne funkciis ĝuste.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Ĝi ne funkciis laŭnorme.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'La aspekto de la interfaco ne plaĉas al mi.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'La novaj etikedoj kaj dizajno ne plaĉas al mi.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'La nova ilobreto ne plaĉas al mi.',
	'prefswitch-survey-answer-whyoff-other' => 'Alia kialo:',
	'prefswitch-survey-question-browser' => 'Kiun retumilon vi uzas?',
	'prefswitch-survey-answer-browser-other' => 'Alia retumilo:',
	'prefswitch-survey-question-os' => 'Kiun operaciumon vi uzas?',
	'prefswitch-survey-answer-os-other' => 'Alia operaciumo:',
	'prefswitch-survey-question-res' => 'Kio estas la distingivo de via ekrano?',
	'prefswitch-title-on' => 'Kio estas nova?',
	'prefswitch-title-switched-on' => 'Ĝuu!',
	'prefswitch-title-off' => 'Malŝalti novajn funkciojn',
	'prefswitch-title-switched-off' => 'Dankon',
	'prefswitch-title-feedback' => 'Komentoj',
	'prefswitch-success-on' => 'Novaj funkcioj nun estas ŝalta.',
	'prefswitch-success-off' => 'Novaj funkcioj nun estas malŝalta.',
	'prefswitch-success-feedback' => 'Viaj komento estis sendita.',
	'prefswitch-return' => '<hr style="clear:both">
Reiri al <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => 'Ni penis faciligi aferojn por niaj uzantoj. Ni ĝojas provizi iujn novajn plibonigojn, inkluzivante novan aspekton kaj simpligitan redaktilaron. Plibonigado de la uzebleco estas prioritato de la Fondaĵo Wikimedia kaj ni sendos pluajn ĝisdatigojn enstontece.

[[File:UsabilityNavigation.png|right|link=|Ekrankapto de la nova navigilo]]
[[File:UsabilityToolbar.png|right|link=|Ekrankapto de la nova progresa redakto-ilobreto]]
[[File:UsabilityDialogs.png|right|link=|Ekrankapto de la nova enhav-generadaj dialogujoj]]

===Jen kion ni ŝanĝis===
* Navigado: Ni plibonigis la navigadon por legi kaj redakti paĝojn. Nun la etikedoj ĉe la supreno de ĉiu paĝo pli klare difini ĉu vi vidas la paĝon aŭ diskuto-paĝo, kaj ĉu vi legas aŭ redaktas la paĝon.
* Redakta ilobreto: Ni reorganizis la redaktan ilobreto por simpligi ĝin. Nun, formataj paĝoj estas pli simpla kaj pli intuicia.
* Ligila asistanto: Facila ilo por aldoni ligilojn al aliaj paĝoj de Vikipedio kaj ligiloj al eksteraj retejoj.
* Serĉaj plibonigoj: Ni plibonigis serĉsugestojn direkti vin al la paĝo kiun vi serĉas pli rapide.
* Aliaj novaj funkcioj: Ni ankaŭ aldonis tabela asistanto, por faciligi kreadon de tabeloj, kaj funkcio anstataŭigi tekston en paĝoj.',
	'prefswitch-main-on' => '===Ĉu komenti?===
Ni bonvenus viajn opinion. Bonvolu viziti nian <span class="plainlinks">[$1 komento-paĝon] </span>.
===Malŝaltu nun!===
Se vi volus malŝalti la novajn funkciojn, bonvolu <span class="plainlinks">[$2 klaki ĉi tie]</span>.',
	'prefswitch-main-off' => '===Trovu ĝin nun!===
Se vi volus ŝalti la novajn funkciojn, bonvolu <span class="plainlinks">[$1 klaki ĉi tie]</span>.',
	'prefswitch-survey-intro-feedback' => 'Ni bonvenus vian opinion.
Bonvolu plenumi la jenan malnepran enketon.',
	'prefswitch-survey-intro-off' => 'Dankon pro provante niajn novajn funkciojn.
Helpi nin por plibonigi ilin, bonvolu plenumi la jenan malnepran enketon.',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'prefswitch-survey-true' => 'Haj',
	'prefswitch-survey-false' => 'Ně',
	'prefswitch-survey-submit-off' => 'Nowe funkcije wupinyć',
	'prefswitch-survey-cancel-off' => 'Jeli by rady nowe funkcije dale wužiwał, móžeš so k $1 wróćić.',
	'prefswitch-survey-question-likedislike' => 'Što so ći na nowych funkcijach lubi?
Što so ći na nowych funkcijach njelubi?',
	'prefswitch-survey-question-whyoff' => 'Čehodla wupinaš nowe funkcije?
Prošu wubjer wšě, kotrež maja so nałožić.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Njeje porjadnje fungowało.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Nowe rajtarki a wuhotowanje njejsu so mi lubili.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Nowa gratowa lajsta njeje so mi njespodobała.',
	'prefswitch-survey-answer-whyoff-other' => 'Druha přičina:',
	'prefswitch-survey-question-browser' => 'Kotry wobhladowak wužiwaš?',
	'prefswitch-survey-answer-browser-other' => 'Druhi wobhladowak:',
	'prefswitch-survey-question-os' => 'Kotry dźěłowy system wužiwaš?',
	'prefswitch-survey-answer-os-other' => 'Druhi dźěłowy system:',
	'prefswitch-survey-question-res' => 'Kotre je rozeznaće twojeje wobrazowki?',
	'prefswitch-title-on' => 'Što je nowe?',
	'prefswitch-title-switched-on' => 'Wjesel so!',
	'prefswitch-title-off' => 'Nowe funkcije wupinyć',
	'prefswitch-title-switched-off' => 'Dźakujemy so',
	'prefswitch-success-on' => 'Nowe funkcije su nětko zapinjene.',
	'prefswitch-success-off' => 'Nowe funkcije su wupinjene.',
	'prefswitch-return' => '<hr style="clear:both">
Wróćo do <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main-off' => '===Wupruwuj je!===
Jeli chceš nowe funkcije zapinyć,  <span class="plainlinks">[$1 klikń prošu tu]</span>.',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'prefswitch' => 'Activation e disactivation del Initiativa de Usabilitate',
	'prefswitch-desc' => 'Permitter le usatores de cambiar inter gruppos de preferentias',
	'prefswitch-survey-true' => 'Si',
	'prefswitch-survey-false' => 'No',
	'prefswitch-survey-submit-off' => 'Disactivar le nove functiones',
	'prefswitch-survey-cancel-off' => 'Si tu vole continuar a usar le nove functiones, tu pote retornar a $1.',
	'prefswitch-survey-submit-feedback' => 'Dar nos tu opinion',
	'prefswitch-survey-cancel-feedback' => 'Si tu non vole dar nos tu opinion, tu pote retornar a $1.',
	'prefswitch-survey-question-likedislike' => 'Que te placeva del nove functiones?
Que non te placeva de illos?',
	'prefswitch-survey-question-whyoff' => 'Proque disactiva tu le nove functiones?
Per favor selige tote le motivos applicabile.',
	'prefswitch-survey-answer-whyoff-hard' => 'Esseva troppo difficile de usar.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Non functionava correctemente.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Non functionava de modo previsibile.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Non me placeva le aspecto.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Non me placeva le nove schedas e disposition.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Non me placeva le nove barra de instrumentos.',
	'prefswitch-survey-answer-whyoff-other' => 'Altere motivo:',
	'prefswitch-survey-question-browser' => 'Qual navigator usa tu?',
	'prefswitch-survey-answer-browser-other' => 'Altere navigator:',
	'prefswitch-survey-question-os' => 'Qual systema de operation usa tu?',
	'prefswitch-survey-answer-os-other' => 'Altere systema de operation:',
	'prefswitch-survey-question-res' => 'Qual es le resolution de tu schermo?',
	'prefswitch-title-on' => 'Que ha il de nove?',
	'prefswitch-title-switched-on' => 'Bon divertimento!',
	'prefswitch-title-off' => 'Disactivar le nove functiones',
	'prefswitch-title-switched-off' => 'Gratias',
	'prefswitch-title-feedback' => 'Tu opinion',
	'prefswitch-success-on' => 'Le nove functiones es ora active.',
	'prefswitch-success-off' => 'Le nove functiones non es plus active.',
	'prefswitch-success-feedback' => 'Tu opinion ha essite inviate.',
	'prefswitch-return' => '<hr style="clear:both">
Retornar a <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => 'Nos ha laborate durmente pro render le cosas plus facile pro nostre usatores. Nos es enthusiasta de demonstrar alcun meliorationes, como un nove apparentia e functiones de modification simplificate. Meliorar le usabilitate de nostre projectos es un prioritate del Fundation Wikimedia e nos monstrara altere actualisationes in le futuro.

[[File:UsabilityNavigation.png|right|link=|Captura de schermo del nove systema de navigation]]
[[File:UsabilityToolbar.png|right|link=|Captura de schermo del instrumentario de modification meliorate]]
[[File:UsabilityDialogs.png|right|link=|Captura de schermo del nove dialogos pro generar contento]]
===Ecce lo que nos ha cambiate===
* Navigation: Nos ha meliorate le systema de navigation pro leger e modificar paginas. Ora, le schedas in alto de cata pagina indica plus clarmente si tu vide le articulo o su pagina de discussion, e si tu lege o modifica le pagina.
* Meliorationes del instrumentario de modification:  Nos ha reorganisate le barra de instrumentos de modification pro render lo plus facile de usar. Ora, formatar paginas es plus simple e intuitive.
* Assistente pro ligamines: Un instrumento simple permitte adder ligamines a altere paginas de Wikipedia e ligamines a sitos externe.
* Meliorationes de recerca: Nos ha meliorate le suggestiones de recerca pro portar te plus rapidemente al pagina que tu cerca.
* Altere nove functiones: Nos ha etiam introducite un assistente pro tabellas pro facilitar le creation de tabellas, e un function de cercar e reimplaciar pro simplificar le modification de paginas.',
	'prefswitch-main-on' => '===Commentarios?===
Nos vole audir te de. Per favor visita nostre <span class="plainlinks">[$1 pagina pro dar tu opinion]</span>.
===Porta me retro!===
Si tu vole disactivar le nove functiones, per favor <span class="plainlinks">[$2 clicca hic]</span>.',
	'prefswitch-main-off' => '===Proba los!===
Si tu vole activar le nove functiones, per favor <span class="plainlinks">[$1 clicca hic]</span>.',
	'prefswitch-survey-intro-feedback' => 'Nos vole audir de te.
Per favor completa le questionario facultative hic infra.',
	'prefswitch-survey-intro-off' => 'Gratias pro haber probate le nove functiones.
Pro adjutar nos a meliorar los, per favor completa le questionario facultative hic infra.',
);

/** Italian (Italiano)
 * @author Una giornata uggiosa '94
 */
$messages['it'] = array(
	'prefswitch' => "Cambiamento delle preferenze dell'iniziativa per l'usabilità",
	'prefswitch-desc' => 'Permetti agli utenti di cambiare set di preferenze',
	'prefswitch-survey-true' => 'Sì',
	'prefswitch-survey-false' => 'No',
	'prefswitch-survey-submit-off' => 'Disattiva le nuove funzioni',
	'prefswitch-survey-cancel-off' => 'Se vuoi continuare ad usare le nuove funzioni, puoi tornare a $1.',
	'prefswitch-survey-submit-feedback' => 'Invia feedback',
	'prefswitch-survey-cancel-feedback' => 'Se non vuoi fornire un feedback, puoi tornare a $1.',
	'prefswitch-survey-question-likedislike' => 'Cosa ti è piaciuto delle nuove funzioni?
Cosa non ti è piaciuto delle nuove funzioni?',
	'prefswitch-survey-question-whyoff' => 'Perché stai disattivando le nuove funzioni?
Si prega di selezionare tutte le motivazioni pertinenti.',
	'prefswitch-survey-answer-whyoff-hard' => 'Era troppo difficile da usare.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Non funzionava correttamente.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Non si comportava in modo coerente.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => "Non mi piaceva l'aspetto.",
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Non mi piacevano le nuove schede e il layout.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Non mi piaceva la nuova barra degli strumenti.',
	'prefswitch-survey-answer-whyoff-other' => 'Altra motivazione:',
	'prefswitch-survey-question-browser' => 'Quale browser usi?',
	'prefswitch-survey-answer-browser-other' => 'Altro browser:',
	'prefswitch-survey-question-os' => 'Quale sistema operativo usi?',
	'prefswitch-survey-answer-os-other' => 'Altro sistema operativo:',
	'prefswitch-survey-question-res' => 'Qual è la risoluzione del tuo schermo?',
	'prefswitch-title-on' => "Cosa c'è di nuovo?",
	'prefswitch-title-switched-on' => 'Buon divertimento!',
	'prefswitch-title-off' => 'Disattiva le nuove funzioni',
	'prefswitch-title-switched-off' => 'Grazie',
	'prefswitch-title-feedback' => 'Feedback',
	'prefswitch-success-on' => 'Le nuove funzioni sono state attivate.',
	'prefswitch-success-off' => 'Le nuove funzioni sono state disattivate.',
	'prefswitch-success-feedback' => 'Il tuo feedback è stato inviato.',
	'prefswitch-return' => '<hr style="clear:both">
Torna a <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => 'Abbiamo lavorato duramente per rendere le cose più semplici ai nostri utenti. Siamo entusiasti di condividere alcuni miglioramenti, tra cui un nuovo aspetto grafico e delle funzioni di modifica semplificate.  Migliorare l\'usabilità dei progetti wiki è una priorità della fondazione Wikimedia, e condivideremo altri aggiornamenti in futuro.

[[File:UsabilityNavigation.png|right|link=|Screenshot del nuovo sistema di navigazione]]
[[File:UsabilityToolbar.png|right|link=|Screenshot della nuova barra degli strumenti]]
[[File:UsabilityDialogs.png|right|link=|Screenshot delle nuove finestre di dialogo per la generazione del contenuto]]
===Ecco cosa abbiamo cambiato===
* Navigazione: Abbiamo migliorato il sistema di navigazione per leggere e modificare voci. Adesso, le schede nella parte superiore di ogni voce indicano più chiaramente se stai visualizzando la voce o la pagina di discussione, e se stai leggendo o modificando una voce.
* Miglioramenti alla barra degli strumenti:  Abbiamo riorganizzato la barra degli strumenti di modifica per renderla più semplice da usare.  Adesso, formattare le voci è più semplice e intuitivo.
* Procedura guidata per i link:  Uno strumento semplice da utilizzare ti permette di aggiungere link ad altre pagine di Wikipedia e link a siti esterni.
* Miglioramenti alla ricerca: Abbiamo migliorato i suggerimenti della ricerca per portarti più velocemente alla pagina che stai cercando.
* Altre nuove funzioni:  Abbiamo introdotto anche una procedura guidata per le tabelle per rendere la loro creazione più semplice e una funzione "trova e sostituisci" per semplificare la modifica delle pagine.',
	'prefswitch-main-on' => '===Feedback?===
Ci piacerebbe ascoltarvi.  Per favore visita la nostra <span class="plainlinks">[$1 pagina di feedback]</span>.
===Riportami indietro!===
Se vuoi disattivare le nuove funzioni, <span class="plainlinks">[$2 clicca qui]</span>.',
	'prefswitch-main-off' => '===Provale!===
Se vuoi attivare le nuove funzioni, <span class="plainlinks">[$1 clicca qui]</span>.',
	'prefswitch-survey-intro-feedback' => 'Ci piacerebbe ascoltarvi.
Per favore riempi il seguente questionario facoltativo.',
	'prefswitch-survey-intro-off' => 'Grazie per aver provato le nostre nuove funzioni.
Per aiutarci a migliorarle, per favore riempi il seguente questionario facoltativo.',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'prefswitch' => "Benotzerfrëndlechkeet's-Initiative: Ëmschalte vun den Astellungen",
	'prefswitch-desc' => 'De Benotzer erlaben tësche Gruppe vun Astellungen ëmzeschalten',
	'prefswitch-survey-true' => 'Jo',
	'prefswitch-survey-false' => 'Neen',
	'prefswitch-survey-submit-off' => 'Déi nei Fonctiounen ausschalten',
	'prefswitch-survey-cancel-off' => 'Wann Dir déi nei Fonctioune weiderbenotze wëllt, kënnt Dir op $1 zeréckgoen.',
	'prefswitch-survey-submit-feedback' => 'Schéckt eis Är Meenung',
	'prefswitch-survey-cancel-feedback' => 'Wann Dir Är Reaktioun net wëllt matdeelen da kënnt dir op $1 zeréckgoen.',
	'prefswitch-survey-question-likedislike' => 'Wat huet Iech bäi den neie Fonctioune gefall?
Wat huet Iech bäi den neie Fonctioune net gefall?',
	'prefswitch-survey-question-whyoff' => 'Firwat schalt dir déi nei Fonctiounen aus?
Wielt w.e.g.alles aus wat zoutrëfft.',
	'prefswitch-survey-answer-whyoff-hard' => 'Et war ze komplizéiert fir ze benotzen.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Et huet net uerdentlech fonctionéiert.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Et huet net esou fonctionnéiert wéi  virgesinn.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Et huet mir net gefall wéi et ausgesäit.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Ech hat déi nei Ongleten an den neie  Layout net gär.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Ech hunn déi nei Toolbar net gär.',
	'prefswitch-survey-answer-whyoff-other' => 'Anere Grond:',
	'prefswitch-survey-question-browser' => 'Watfir e Browser benotzt Dir?',
	'prefswitch-survey-answer-browser-other' => 'Anere Browser:',
	'prefswitch-survey-question-os' => 'Wafir e Betriibssystem benotzt Dir?',
	'prefswitch-survey-answer-os-other' => 'Anere Betriibssystem:',
	'prefswitch-survey-question-res' => "Wéi ass d'Opléisung vun ärem Ecran?",
	'prefswitch-title-on' => 'Wat ass nie?',
	'prefswitch-title-switched-on' => 'Vill Freed!',
	'prefswitch-title-off' => 'Déi nei Fonctiounen ausschalten',
	'prefswitch-title-switched-off' => 'Merci',
	'prefswitch-title-feedback' => 'Är Reaktioun',
	'prefswitch-success-on' => 'Nei Fonctioune sinn elo agechalt.',
	'prefswitch-success-off' => 'Nei Fonctioune sinn elo ageschalt',
	'prefswitch-success-feedback' => 'Är Reaktioun gouf geschéckt.',
	'prefswitch-return' => '<hr style="clear:both">
Zréck op <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "Mir hu vill geschafft fir et méi einfach vir eis Benotzer ze maachen. Mir si frou fir e puer Verbesserungen ëmzesetzen besonnesch e neie ''look and feel'' a vereinfacht Ännerungsfonctiounen. Verbessere vun der Benotzerfrëndlechkeet vun eise Projete ass eng Prioritéit vun der Wikimedia Foundation a mir wäerte weider Verbesserungen an der Zukunft ëmsetzen.

[[File:UsabilityNavigation.png|right|link=|Screenshot vun der neier Navigatioun]][[File:UsabilityToolbar.png|right|link=|Screenshot vun der erweiderter Ännerungstoolbar]][[File:UsabilityDialogs.png|right|link=|Screenshot vun den neien Dialogen beim generéieren vun Inhalter]]
===Hei ass dat wat geännert huet===
* Navigatioun: Mir hunn d'Navigatioun fir Säiten ze liesen an z'änneren verbessert. D'Ongleten uewen op all Säit definéiere méi kloer ob een eng Säit oder eng Diskussiounssäit kuckt an ob een eng Säit kuckt oder ännert.
* Verbesserunge vun der Ännerungstoolbar: Mir hunn d'Ännerungstoolbar reorganiséiert fir se méi einfach kënnen ze benotzen. Elo ass d'Formatéiere vu Säiten méi einfach a méi intuitiv.
* Linkwizard: En Tool den einfach ze benotzen ass fir Linken op aner Wikipedia-Säiten a Linken op aner Siten dobäizesetzen.
* Verbesserunge bei der Sich: Mir hunn d'Virschléi bei der Sich verbessert fir datt Dir déi Säit no där Dir sicht méi séier fannt.
* Aner nei Fonctiounen: Mir hunn och e Wizard fir Tabellen agefouert deen e méi einfach mécht fir Tabellen unzeleën an eng Sich- an Ersetzungs-Fonctioun fir d'Ännere vu Säiten ze vereinfachen.",
	'prefswitch-main-on' => '===Är Reaktioun?===
Mir wiere frou vun Iech ze héieren. Gitt w.e.g. op eis <span  class="plainlinks">[$1 Är Reaktioun Säit]</span>.
===Bréngt mech zréck!===
Wann Dir déi nei Fonctiounen ausschalte wëllt, da <span  class="plainlinks">[$2 klickt w.e.g. hei]</span>.',
	'prefswitch-main-off' => '===Probéiert se aus!===
Wann Dir déi nei Fonctiounen ausprobéiere wëllt, da <span  class="plainlinks">[$1 klickt w.e.g. hei]</span>.',
	'prefswitch-survey-intro-feedback' => 'Mir wiere frou vun Iech ze héieren.
Fëllt w.e.g. déi fakultativ Ëmfro hei ënnendrënner aus.',
	'prefswitch-survey-intro-off' => 'Merci datt dir déi nei Fonctiounen ausprobéiert hutt.
Fir eis ze hëllefen besser ze ginn, fëllt w.e.g. déi fakultativ Ëmfro hei ënnendrënner aus.',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'prefswitch' => 'Превклучување на нагодувања на Иницијативата за употребливост',
	'prefswitch-desc' => 'Овозможува корисниците да превклучуваат групи нагодувања',
	'prefswitch-survey-true' => 'Да',
	'prefswitch-survey-false' => 'Не',
	'prefswitch-survey-submit-off' => 'Исклучи нови функции',
	'prefswitch-survey-cancel-off' => 'Ако сакате да продолжите со користење на новите функции, можете да се вратите на $1.',
	'prefswitch-survey-submit-feedback' => 'Ваши примедби',
	'prefswitch-survey-cancel-feedback' => 'Ако не сакате да искажете примедби, можете да се вратите на $1.',
	'prefswitch-survey-question-likedislike' => 'Што ви се допадна кај новите фунции?
А што не ви се допадна?',
	'prefswitch-survey-question-whyoff' => 'Зошто ги исклучувате новите функции?
Одберете било колку одговори.',
	'prefswitch-survey-answer-whyoff-hard' => 'Беше премногу тешко за користење.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Не функционираше како што треба.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Работеше непредвидливо.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Не ми се допадна изгледот.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Не ми се допаднаа новите менија и распоредот.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Не ми се допадна новиот алатник.',
	'prefswitch-survey-answer-whyoff-other' => 'Друга причина:',
	'prefswitch-survey-question-browser' => 'Кој прелистувач го користите?',
	'prefswitch-survey-answer-browser-other' => 'Друг прелистувач:',
	'prefswitch-survey-question-os' => 'Кој оперативен систем го користите?',
	'prefswitch-survey-answer-os-other' => 'Друг оперативен систем',
	'prefswitch-survey-question-res' => 'Која ви е резолуцијата на екранот?',
	'prefswitch-title-on' => 'Што има ново?',
	'prefswitch-title-switched-on' => 'Уживајте!',
	'prefswitch-title-off' => 'Исклучи нови функции',
	'prefswitch-title-switched-off' => 'Ви благодариме',
	'prefswitch-title-feedback' => 'Примедби',
	'prefswitch-success-on' => 'Новите функции се вклучени.',
	'prefswitch-success-off' => 'Новите функции се исклучени.',
	'prefswitch-success-feedback' => 'Вашите примедби се испратени.',
	'prefswitch-return' => '<hr style="clear:both">
Назад кон <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => 'Работевме напорно за да им олесниме на корисниците. Со задоволство ви ги претставуваме направените подобрувања, меѓу кои се и новиот изглед и чувство и упростените функции за уредување.  Подобрувањето на употребливоста на проектите е голем приоритет на фондацијата Викимедија, и во иднина очекувајте да ве известуваме и за други унапредувања.

[[File:UsabilityNavigation.png|right|link=|Новата навигација]]
[[File:UsabilityToolbar.png|right|link=|Подобрениот алатник за уредување]]
[[File:UsabilityDialogs.png|right|link=|Новите дијалози за создавање содржини]]
===Еве што променивме===
* Навигација: Ја подобривме навигацијата за читање и уредување на страниците. Сега менијата над секоја страница појасно укажуваат на тоа дали ја гледате страницата или страница за разговор, и дали ја читате или уредувате страницата.
* Подобрен алатник за уредување:  Го реорганизиравме алатникот за полесна употреба.  Сега форматирањето на страниците е упростено и поинтуитивно.
* Помошник за врски:  Оваа проста алатка ви овозможува да додавате врски до други страници на Википедија и до надворешни веб-страници.
* Подобрено пребарување: Ги подобривме предлозите при пребарување за што побрзо да ви ја најдеме страницата што ја барате.
* Други нови функции:  Воведовме и помошник за табели со чија помош табелите се прават полесно, а има и можност за пронаоѓање и заменување, со што се упростува уредувањето на страницата.',
	'prefswitch-main-on' => '===Примедби?===
Со задоволство го очекуваме вашето мислење.  Посетете ја нашата <span class="plainlinks">[$1 страница за примедби]</span>.
===Врати ме!===
Ако сакате да ги исклучите новите функции, тогаш <span class="plainlinks">[$2 кликнете тука]</span>.',
	'prefswitch-main-off' => '===Испробајте ги!===
Ако сакате да ги вклучите новите функции, тогаш <span class="plainlinks">[$1 кликнете тука]</span>.',
	'prefswitch-survey-intro-feedback' => 'Со задоволство го очекуваме вашето мислење.
Пополнете ја анкетата подолу. (незадолжително)',
	'prefswitch-survey-intro-off' => 'Ви благодариме што ги испробавте новите функции.
Пополнете ја анкетата подолу за да ни помогнете да ги подобриме. (незадолжително)',
);

/** Malayalam (മലയാളം)
 * @author Praveenp
 */
$messages['ml'] = array(
	'prefswitch' => 'യൂസബിലിറ്റി ഇനിഷ്യേറ്റീവ് ക്രമീകരണങ്ങൾ മാറുക',
	'prefswitch-desc' => 'ക്രമീകരണങ്ങൾ കൂട്ടത്തോടെ മാറാൻ ഉപയോക്താക്കളെ അനുവദിക്കുന്നു.',
	'prefswitch-survey-true' => 'അതെ',
	'prefswitch-survey-false' => 'വേണ്ട',
	'prefswitch-survey-submit-off' => 'പുതിയ സവിശേഷതകൾ പ്രവർത്തനരഹിതമാക്കുക',
	'prefswitch-survey-cancel-off' => 'പുതിയ സവിശേഷതകൾ തുടർന്നും ഉപയോഗിക്കാൻ ആഗ്രഹിക്കുന്നെങ്കിൽ, $1 എന്ന താളിലേയ്ക്ക് മടങ്ങാം.',
	'prefswitch-survey-submit-feedback' => 'അഭിപ്രായം അറിയിക്കുക',
	'prefswitch-survey-cancel-feedback' => 'അഭിപ്രായങ്ങൾ പങ്ക് വെയ്ക്കാനാഗ്രഹമില്ലെങ്കിൽ, താങ്കൾക്ക് $1 എന്ന താളിലേയ്ക്ക് മടങ്ങാം.',
	'prefswitch-survey-question-likedislike' => 'പുതിയ സവിശേഷതകളിൽ താങ്കൾക്ക് എന്തൊക്കെ ഇഷ്ടപ്പെട്ടു?
പുതിയ സവിശേഷതകളിൽ എന്തൊക്കെ ഇഷ്ടപ്പെട്ടില്ല?',
	'prefswitch-survey-question-whyoff' => 'പുതിയ സവിശേഷതകൾ എന്തുകൊണ്ടാണ് താങ്കൾ വേണ്ടന്നു വെയ്ക്കുന്നത്?
ബാധകമാകുന്ന എല്ലാം തിരഞ്ഞെടുക്കുക.',
	'prefswitch-survey-answer-whyoff-hard' => 'ഇത് ഉപയോഗിക്കാൻ ഏറെ ബുദ്ധിമുട്ടായിരുന്നു.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'ഇത് ശരിയായ വിധത്തിൽ പ്രവർത്തിച്ചില്ല.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'ഇത് വിചാരിക്കുന്നതുപോലെയല്ല പ്രവർത്തിക്കുന്നത്.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'ഇത് കാണാൻ ഒരു രസമില്ല.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'പുതിയ റ്റാബുകളും ദൃശ്യവിന്യാസവും എനിക്കിഷ്ടപ്പെട്ടില്ല.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'പുതിയ റ്റൂൾബാർ എനിക്കിഷ്ടപ്പെട്ടില്ല.',
	'prefswitch-survey-answer-whyoff-other' => 'മറ്റ് കാരണം:',
	'prefswitch-survey-question-browser' => 'ഏതു ബ്രൌസറാണ് താങ്കള്‍ ഉപയോഗിക്കുന്നത്?',
	'prefswitch-survey-answer-browser-other' => 'Other browser:',
	'prefswitch-survey-question-os' => 'ഏത് ഓപറേറ്റിങ്ങ് സിസ്റ്റമാണ് താങ്കള്‍ ഉപയോഗിക്കുന്നത്?',
	'prefswitch-survey-answer-os-other' => 'മറ്റ് ഓപറേറ്റിങ് സിസ്റ്റം:',
	'prefswitch-survey-question-res' => 'താങ്കളുടെ സ്ക്രീന്‍ റെസ‌ല്യൂഷന്‍ എന്താണ്?',
	'prefswitch-title-on' => 'എന്താണ് പുതിയത്?',
	'prefswitch-title-switched-on' => 'ആസ്വദിക്കൂ!',
	'prefswitch-title-off' => 'പുതിയ സവിശേഷതകൾ പ്രവർത്തനരഹിതമാക്കുക',
	'prefswitch-title-switched-off' => 'നന്ദി',
	'prefswitch-title-feedback' => 'അഭിപ്രായങ്ങൾ',
	'prefswitch-success-on' => 'പുതിയ സവിശേഷതകൾ ഇപ്പോൾ പ്രവർത്തനസജ്ജമാണ്.',
	'prefswitch-success-off' => 'പുതിയ സവിശേഷതകൾ ഇപ്പോൾ പ്രവർത്തനരഹിതമാണ്.',
	'prefswitch-success-feedback' => 'താങ്കളുടെ അഭിപ്രായങ്ങൾ അയച്ചുകഴിഞ്ഞു.',
	'prefswitch-return' => '<hr style="clear:both">
<span class="plainlinks">[$1 $2]</span> എന്നതിലേയ്ക്ക് മടങ്ങുക.',
	'prefswitch-main' => 'ഞങ്ങൾ ഞങ്ങളുടെ ഉപയോക്താക്കൾക്ക് കാര്യങ്ങൾ എളുപ്പമാക്കി നൽകാൻ കഠിനാധ്വാനം ചെയ്യുന്നുണ്ട്. പുതുമയുള്ള ദൃശ്യാനുഭവും ലളിതമാക്കിയ തിരുത്തൽ സൗകര്യവുമടക്കമുള്ള ചില മെച്ചപ്പെടുത്തലുകൾ ഞങ്ങൾ ആഹ്ലാദത്തോടെ പങ്ക് വെയ്ക്കുന്നു. നമ്മുടെ സംരംഭങ്ങളുടെ മെച്ചപ്പെട്ട ഉപയോഗ്യത വിക്കിമീഡിയ ഫൗണ്ടേഷന്റെ ലക്ഷ്യമാണ്, കൂടുതൽ മെച്ചപ്പെടുത്തലുകൾ ഭാവിയിൽ വരാനിരിക്കുന്നു.

[[File:UsabilityNavigation.png|right|link=|പുതിയ നാവിഗേഷന്റെ സ്ക്രീൻഷോട്ട്]]
[[File:UsabilityToolbar.png|right|link=|വിപുലമാക്കിയ റ്റൂൾബാറിന്റെ സ്ക്രീൻഷോട്ട്]]
[[File:UsabilityDialogs.png|right|link=|ഉള്ളടക്കം ചേർക്കാനുള്ള സൗകര്യങ്ങളുടെ സ്ക്രീൻഷോട്ട്]]
===ഞങ്ങൾ മാറ്റം വരുത്തിയവ===
*നാവിഗേഷൻ: താളുകൾ വായിക്കാനും തിരുത്തുവാനുമുള്ള സൗകര്യം മെച്ചപ്പെടുത്തി. ഇപ്പോൾ ഓരോ താളിന്റേയും മുകളിലുള്ള റ്റാബുകൾ താങ്കൾ താളാണോ സംവാദം താളാണോ കാണുന്നത് എന്നും, താങ്കൾ തിരുത്തുകയാണോ വായിക്കുകയാണോ എന്നും വ്യക്തമായി കാണിക്കുന്നു.
*തിരുത്തൽ ടൂൾബാർ മെച്ചപ്പെടുത്തലുകൾ: ലളിതമായി ഉപയോഗിക്കാവുന്ന വിധത്തിൽ ഞങ്ങൾ തിരുത്തൽ ടൂൾബാർ പുനഃക്രമീകരിച്ചിരിക്കുന്നു. ഇപ്പോൾ താൾ ശരിയായ വിധത്തിൽ വിന്യസിക്കുന്നത് ലളിതവും സ്വാഭാവികവുമായിരിക്കും.
*കണ്ണി ചേർക്കൽ: ലളിതമായി ഉപയോഗിക്കാവുന്ന ഉപകരണം കൊണ്ട് മറ്റ് വിക്കിപീഡിയ താളുകളിലേയ്ക്കോ പുറത്തുള്ള സൈറ്റുകളിലേയ്ക്കോ കണ്ണികൾ ചേർക്കാൻ താങ്കളെ സഹായിക്കുന്നു.
*തിരച്ചിൽ മെച്ചപ്പെടുത്തലുകൾ: താങ്കൾ തിരയുന്ന താളിലേയ്ക്ക് പെട്ടെന്ന് എത്തിച്ചേരാവുന്ന വിധത്തിൽ മെച്ചപ്പെടുത്തിയ തിരച്ചിൽ നിർദ്ദേശങ്ങൾ ഉൾപ്പെടുത്തിയിരിക്കുന്നു.
*മറ്റ് പുതിയ സവിശേഷതകൾ: പട്ടികകൾ ചേർക്കാനായി ഒരു സഹായിയും താൾ തിരുത്തൽ ലളിതമാക്കാൻ വാക്കുകളും മറ്റും കണ്ടെത്തി മാറ്റിച്ചേർക്കാനുള്ള സൗകര്യവും ഉൾപ്പെടുത്തിയിരിക്കുന്നു.',
	'prefswitch-main-on' => '===അഭിപ്രായങ്ങൾ?===
താങ്കളുടെ അഭിപ്രായം ഏറെ വിലയേറിയതാണ്. ദയവായി ഞങ്ങളുടെ <span class="plainlinks">[$1 അഭിപ്രായങ്ങൾക്കുള്ള താൾ]</span> കാണുക.
===എനിക്കു പഴയതു മതി!===
പുതിയ സവിശേഷതകൾ വേണ്ട എന്നാണ് താങ്കളാഗ്രഹിക്കുന്നതെങ്കിൽ, ദയവായി <span class="plainlinks">[$2 ഇവിടെ ഞെക്കുക]</span>.',
	'prefswitch-main-off' => '===അവ പരീക്ഷിച്ചു നോക്കൂ!===
പുതിയ സവിശേഷതകൾ പരീക്ഷിച്ചു നോക്കാൻ താങ്കളാഗ്രഹിക്കുന്നുവെങ്കിൽ, ദയവായി <span class="plainlinks">[$1 ഇവിടെ ഞെക്കുക]</span>.',
	'prefswitch-survey-intro-feedback' => 'താങ്കളുടെ അഭിപ്രായമെന്തെന്നറിയാൻ ഞങ്ങൾക്കതിയായ ആഗ്രഹമുണ്ട്.
ദയവായി താഴെ കൊടുത്തിരിക്കുന്ന ഐച്ഛിക സർവേ പൂരിപ്പിക്കുക.',
	'prefswitch-survey-intro-off' => 'പുതിയ സവിശേഷതകൾ പരീക്ഷിച്ചതിനു നന്ദി.
അവ മെച്ചപ്പെടുത്തുവാൻ ഞങ്ങളെ സഹായിക്കുന്നതിനായി, ദയവായി താഴെ നൽകിയിരിക്കുന്ന ഐച്ഛിക സർവേ പൂരിപ്പിക്കുക.',
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
	'prefswitch-main' => 'Bardzo staramy się ułatwić użytkownikom korzystanie z {{GRAMMAR:D.lp|{{SITENAME}}}}. Udostępniliśmy nowe funkcjonalności, w tym nowy wygląd i wygodniejsze narzędzia edycyjne. Poprawa użyteczności projektów jest jednym z priorytetów Fundacji Wikimedia; będziemy informować o udostępnieniu następnych aktualizacji.

[[File:UsabilityNavigation.png|right|link=|Nowy sposób nawigacji]]
[[File:UsabilityToolbar.png|right|link=|Roszerzony pasek narzędzi edycyjnych]]
[[File:UsabilityDialogs.png|right|link=|Nowe dialogi generowania treści]]
===Co zostało zmienione===
* Nawigacja — rozbudowano nawigację służącą do czytania i edycji stron. Obecnie zakładki na górze strony czytelniej określają czy widoczna jest strona czy jest strona dyskusji oraz czy strona jest w trybie odczytu czy edycji.
* Pasek narzędzi edycyjnych — przeorganizowano pasek narzędzi edycyjnych aby uczynić do prostszym w użyciu. Obecnie formatowanie jest prostsze i bardziej intuicyjne.
* Kreator linków — proste w użyciu narzędzie pozwala na dodawanie linków do stron {{GRAMMAR:D.lp|{{SITENAME}}}} oraz innych stron w Internecie.
* Wyszukiwanie — rozbudowano podpowiedzi aby przyspieszyć odnalezienie poszukiwanej strony
* Inne nowe funkcjonalności — dodano kreatora tabel oraz narzędzie wyszukiwania i zastępowania aby ułatwić edytowanie stron.',
	'prefswitch-main-on' => '===Opinia?===
Chcielibyśmy poznać Twoją opinię. Odwiedź <span class="plainlinks">[$1 stronę przesyłania opinii]</span>.
===Chcę to wyłączyć!===
Jeśli chcesz wyłączyć nowe funkcjonalności po prostu <span class="plainlinks">[$2 kliknij tutaj]</span>.',
	'prefswitch-main-off' => '===Wypróbuj!===
Jeśli chcesz przetestować nowe funkcjonalności po prostu <span class="plainlinks">[$1 kliknij tutaj]</span>.',
	'prefswitch-survey-intro-feedback' => 'Chcielibyśmy poznać Twoją opinię.
Będziemy wdzięczni za wypełnienie poniższej ankiety.',
	'prefswitch-survey-intro-off' => 'Dziękujemy za wypróbowanie nowych funkcjonalności.
Jeśli chcesz nam pomóc je udoskonalić wypełnij poniższą ankietę.',
);

/** Piedmontese (Piemontèis)
 * @author Dragonòt
 */
$messages['pms'] = array(
	'prefswitch' => 'Cangiament dij gust ëd la Usability Initiative',
	'prefswitch-desc' => "Përmëtt a j'utent ëd cangé j'ansema dij gust",
	'prefswitch-survey-true' => 'É',
	'prefswitch-survey-false' => 'Nò',
	'prefswitch-survey-submit-off' => 'Disativa le funsionalità neuve',
	'prefswitch-survey-cancel-off' => "S'it veule continué a dovré le possibilità neuve, it deuve torné a $1.",
	'prefswitch-survey-submit-feedback' => 'Manda feedback',
);

/** Russian (Русский)
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'prefswitch' => 'Переключатель настроек Инициативы юзабилити',
	'prefswitch-desc' => 'Позволяет участникам переключать наборы настроек',
	'prefswitch-survey-true' => 'Да',
	'prefswitch-survey-false' => 'Нет',
	'prefswitch-survey-submit-off' => 'Выключить новые возможности',
	'prefswitch-survey-cancel-off' => 'Если вы хотите продолжить использовать новые возможности, вы можете вернуться к $1.',
	'prefswitch-survey-submit-feedback' => 'Отправить отзыв',
	'prefswitch-survey-cancel-feedback' => 'Если вы не хотите оставить отзыв о прототипе, вы можете вернуться к $1.',
	'prefswitch-survey-question-likedislike' => 'Что вам нравится в новых возможностях?
Что вам не нравится в них?',
	'prefswitch-survey-question-whyoff' => 'Почему вы отключаете новые возможности?
Пожалуйста, выберите все подходящие варианты.',
	'prefswitch-survey-answer-whyoff-hard' => 'Слишком сложны в использовании.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Не работают должным образом.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Работают непредсказуемо.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Мне не нравится как они выглядят.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Мне не понравились новые вкладки и вёрстка.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Мне не понравилась новая панель редактирования.',
	'prefswitch-survey-answer-whyoff-other' => 'Другая причина:',
	'prefswitch-survey-question-browser' => 'Какой браузер вы используете?',
	'prefswitch-survey-answer-browser-other' => 'Другой браузер:',
	'prefswitch-survey-question-os' => 'Какую операционную систему вы используете?',
	'prefswitch-survey-answer-os-other' => 'Другая операционная система:',
	'prefswitch-survey-question-res' => 'Каково разрешение вашего экрана?',
	'prefswitch-title-on' => 'Что нового?',
	'prefswitch-title-switched-on' => 'Наслаждайтесь!',
	'prefswitch-title-off' => 'Выключить новые возможности',
	'prefswitch-title-switched-off' => 'Спасибо',
	'prefswitch-title-feedback' => 'Обратная связь',
	'prefswitch-success-on' => 'Новые возможности сейчас включены.',
	'prefswitch-success-off' => 'Новые возможности сейчас отключены.',
	'prefswitch-success-feedback' => 'Ваш отзыв отправлен.',
	'prefswitch-return' => '<hr style="clear:both">
Вернуться к <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => 'Мы прилагаем все усилия, чтобы упростить работу наших пользователей. Мы рады поделиться некоторыми улучшениями, в том числе новым внешним видом и упрощенными функциями редактирования. Улучшение удобства использования наших проектов является приоритетным для Фонда Викимедиа, в будущем мы продолжим делиться обновлениями.

[[File:UsabilityNavigation.png|right|link=|Снимок новой навигации]]
[[File:UsabilityToolbar.png|right|link=|Снимок улучшенной панели редактирования]]
[[File:UsabilityDialogs.png|right|link=|Снимог нового диалога создания]]
=== Что мы изменили === 
* Навигация. Мы улучшили навигацию для чтения и редактирования страниц. Теперь вкладки в верхней части каждой страницы, позволяют более чётко определить чем вы сейчас занимаетесь: просматриваете страницу или её обсуждение, читаете или правите страницу.
* Улучшение панели редактирования. Мы переделали панель инструментов редактирования, чтобы сделать её проще в использовании. Форматирование страниц стало более простым и интуитивным.
* Мастер ссылок. Простой в использовании инструмент позволяет добавлять ссылки как на страницы Википедии, так и на внешние сайты.
* Улучшение поиска. Мы улучшили поисковые подсказки, чтобы быстрее показать вам требуемую страницу.
* Другие новые функции. Мы также сделали мастер таблиц, позволяющий легко создавать таблицы, и функцию поиска и замены, упрощающую редактирование.',
	'prefswitch-main-on' => '=== Обратная связь? ===
Мы хотели бы услышать ваши отзывы. Пожалуйста, посетите нашу <span class="plainlinks">[$1 страницу обратной связи].</span>
=== Верните всё обратно! ===
Если вы хотите отключить новые возможности, пожалуйста, <span class="plainlinks">[$2 нажмите здесь].</span>',
	'prefswitch-main-off' => '=== Опробуйте их! ===
Если вы хотите включить новые возможности, пожалуйста, <span class="plainlinks">[$1 нажмите здесь].</span>',
	'prefswitch-survey-intro-feedback' => 'Мы хотели бы услышать отзывы.
Пожалуйста, ответьте на несколько вопросов ниже.',
	'prefswitch-survey-intro-off' => 'Спасибо за то, опробовали новые возможности.
Чтобы помочь нам улучшить их, пожалуйста, ответьте на несколько вопросов, приведённых ниже.',
);

/** Turkish (Türkçe)
 * @author Joseph
 */
$messages['tr'] = array(
	'prefswitch' => 'Kullanılabilirlik Girişimi tercih anahtarı',
	'prefswitch-desc' => 'Kullanıcıların tercih kümelerini değiştirmesine izin ver',
	'prefswitch-survey-true' => 'Evet',
	'prefswitch-survey-false' => 'Hayır',
	'prefswitch-survey-submit-off' => 'Yeni özellikleri kapat',
	'prefswitch-survey-cancel-off' => 'Yeni özellikleri kullanmaya devam etmek isterseniz, $1 sayfasına geri dönebilirsiniz.',
	'prefswitch-survey-submit-feedback' => 'Geribildirim verin',
	'prefswitch-survey-cancel-feedback' => 'Eğer geribildirim vermek istemiyorsanız, $1 sayfasına geri dönebilirsiniz.',
	'prefswitch-survey-question-likedislike' => 'Yeni özelliklerin beğendiğiniz yönleri nelerdir?
Özelliklerin sevmediğiniz yönleri nelerdir?',
	'prefswitch-survey-question-whyoff' => 'Neden yeni özellikleri kapatıyorsunuz?
Lütfen uygun olanları seçin.',
	'prefswitch-survey-answer-whyoff-hard' => 'Kullanımı çok zor.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Düzgün çalışmadı.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Tahmin edilebilir şekilde çalışmadı.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Görünümünü beğenmedim.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Yeni sekmeleri ve düzeni beğenmedim.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Yeni araç çubuğunu beğenmedim.',
	'prefswitch-survey-answer-whyoff-other' => 'Diğer sebep:',
	'prefswitch-survey-question-browser' => 'Hangi tarayıcıyı kullanıyorsunuz?',
	'prefswitch-survey-answer-browser-other' => 'Diğer tarayıcı:',
	'prefswitch-survey-question-os' => 'Hangi işletim sistemini kullanıyorsunuz?',
	'prefswitch-survey-answer-os-other' => 'Diğer işletim sistemi:',
	'prefswitch-survey-question-res' => 'Ekran çözünürlüğünüz nedir?',
	'prefswitch-title-on' => 'Neler yeni?',
	'prefswitch-title-switched-on' => 'Tadını çıkarın!',
	'prefswitch-title-off' => 'Yeni özellikleri kapat',
	'prefswitch-title-switched-off' => 'Teşekkürler',
	'prefswitch-title-feedback' => 'Geribildirim',
	'prefswitch-success-on' => 'Yeni özellikler şimdi açıldı.',
	'prefswitch-success-off' => 'Yeni özellikler şimdi kapalı.',
	'prefswitch-success-feedback' => 'Geribildiriminiz gönderildi.',
	'prefswitch-return' => '<hr style="clear:both">
<span class="plainlinks">[$1 $2]</span> sayfasına geri dön.',
);

