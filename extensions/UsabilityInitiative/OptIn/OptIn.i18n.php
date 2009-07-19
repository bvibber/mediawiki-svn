<?php
/**
 * Internationalisation for Usability Initiative OptIn extension
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Roan Kattouw
 */
$messages['en'] = array(
	'optin' => 'Usability Initiative opt-in',
	'optin-desc' => 'Allow users to try Beta, the Usability Initiative\'s latest usability enhancements.',
	'optin-title-optedout' => 'Try Beta',
	'optin-title-optedin' => 'Leave Beta',
	'optin-title-justoptedin' => 'Welcome to Beta',
	'optin-title-justoptedout' => 'Thank you for trying Beta',
	'optin-title-feedback' => 'Give feedback',
	'optin-needlogin' => 'You need to [$1 log in] to try Beta.',
	'optin-intro' => 'The Wikipedia Usability Initiative has been working hard to improve the usability of Wikipedia.
Would you like to try Beta?',
	'optin-feedback-intro' => "Thank you for trying Beta.
We would like to know what you think of our new interface, so we would appreciate it if you filled out the optional survey below.",
	'optin-feedback-back' => 'If you do not want to fill out the survey, you can return to $1.',
	'optin-success-in' => 'You are now trying out Beta.
You can opt back out at any time by clicking the "{{int:optin-leave}}" link on the top of the screen.',
	'optin-leave-cancel' => 'If you would like to continue using Beta, you can return to $1.',
	'optin-success-out' => 'You have successfully left Beta.
You can try it out again at anytime by clicking the "{{int:optin-try}}" link on the top right of the screen.',
	'optin-success-feedback' => 'Thank you for your feedback!',
	'optin-accept-short' => 'Let\'s do it!',
	'optin-accept-long' => 'Try out Beta',
	'optin-accept-long-anon' => 'Log in and try out Beta',
	'optin-deny-short' => 'No thank you',
	'optin-deny-long' => 'Return to previous page',
	'optin-submit-out' => 'Leave Beta',
	'optin-submit-feedback' => 'Give feedback',
	'optin-try' => 'Try Beta',
	'optin-leave' => 'Leave Beta',
	'optin-feedback' => 'Give feedback',
	'optin-survey-yes' => 'Yes',
	'optin-survey-no' => 'No',
	'optin-survey-intro' => "Thank you for trying Beta.
We would like to know what you think about our new interface, so we would appreciate it if you filled out the optional survey below before clicking \"[[#leave|{{int:optin-submit-out}}]]\".",
	'optin-survey-question-likedislike' => 'What did you like about Beta? What did you dislike about Beta?',
	'optin-survey-question-whyoptout' => 'Why are you leaving Beta? (Please select all that apply.) ',
	'optin-survey-answer-whyoptout-hard' => 'It was too hard to use.',
	'optin-survey-answer-whyoptout-didntwork' => 'It did not function properly.',
	'optin-survey-answer-whyoptout-notpredictable' => 'It did not perform predictably.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'I did not like the way it looked.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'I did not like the new tabs and layout.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'I did not like the new toolbar.',
	'optin-survey-answer-whyoptout-other' => 'Other reason:',
	'optin-survey-question-explain' => 'Please explain or elaborate on your answer above:',
	'optin-survey-question-techfail' => 'Did you experience any technical failures while using Beta?',
	'optin-survey-question-techfail-ifyes' => 'If yes, please elaborate or explain:',
	'optin-survey-question-usedtoolbar' => 'Did you use the new expandable toolbar to edit a page?',
	'optin-survey-question-usedtoolbar-ifyes' => 'If yes, what did you like about it? What did you dislike about it?',
	'optin-survey-question-usedtoolbar-ifno' => 'If no, why not? Will you in the future? Please explain.',
	'optin-survey-question-different' => 'What could we have done differently to keep you using our Beta?',
	'optin-survey-question-feedback' => 'Please let us know about any other comments, concerns or thoughts you have for us - all feedback is useful!:',
	'optin-survey-question-browser' => 'Which browser do you use?',
	'optin-survey-answer-browser-ie5' => 'Internet Explorer 5',
	'optin-survey-answer-browser-ie6' => 'Internet Explorer 6',
	'optin-survey-answer-browser-ie7' => 'Internet Explorer 7',
	'optin-survey-answer-browser-ie8' => 'Internet Explorer 8',
	'optin-survey-answer-browser-ff1' => 'Firefox 1',
	'optin-survey-answer-browser-ff2' => 'Firefox 2',
	'optin-survey-answer-browser-ff3' => 'Firefox 3',
	'optin-survey-answer-browser-cb' => 'Google Chrome Beta',
	'optin-survey-answer-browser-c1' => 'Google Chrome 1',
	'optin-survey-answer-browser-c2' => 'Google Chrome 2',
	'optin-survey-answer-browser-s3' => 'Safari 3',
	'optin-survey-answer-browser-s4' => 'Safari 4',
	'optin-survey-answer-browser-o9' => 'Opera 9',
	'optin-survey-answer-browser-o9.5' => 'Opera 9.5',
	'optin-survey-answer-browser-o10' => 'Opera 10',
	'optin-survey-answer-browser-other' => 'Other browser:',
	'optin-survey-question-os' => 'Which operating system do you use?',
	'optin-survey-answer-os-windows' => 'Windows',
	'optin-survey-answer-os-macos' => 'Mac OS',
	'optin-survey-answer-os-linux' => 'Linux',
	'optin-survey-answer-os-other' => 'Other operating system:',
	'optin-survey-question-res' => 'What is the resolution of your screen?',
	'optin-survey-question-changes' => 'What changes would you like to see in the Beta in the future (select up to three)?',
	'optin-survey-answer-changes-nav' => 'Make the overall navigation of the site easier to use.',
	'optin-survey-answer-changes-edittools' => 'Increase and enhance the editing tools.',
	'optin-survey-answer-changes-upload' => 'Improve content and media uploads.',
	'optin-survey-answer-changes-richtext' => 'Develop editing tools similar to rich-text editors.',
	'optin-survey-answer-changes-lookfeel' => 'Update the "look and feel" of the site.',
	'optin-survey-answer-changes-predictability' => 'Improve predictability of performance.',
	'optin-survey-answer-changes-compat' => 'Improve compatibility with local wiki.',
	'optin-survey-answer-changes-other' => 'Other:',
	'optin-improvements' => "== What has been improved? ==
[[File:UsabilityNavigation.png|left]]
;Enhanced navigation
The new navigation system makes it easier to see where you are and what you are doing, as well as where you can go and how to get there. With the search box located in the top right corner, the search feature is easier to find and quicker to use.
<div style=\"clear:both;\"></div>

[[File:UsabilityToolbar.png|left]]
;Edit toolbar improvements
New-and-improved icons make it clearer what action each tool performs. Expandable sections reduce clutter while keeping infrequently used tools just a click away.
The help section provides quick access to a reference for common uses of wiki-markup.
<div style=\"clear:both;\"></div>

[[File:UsabilitySearch.png|left]]
;Improved search interface
When using search, title matches are shown first, helping you to find the most relevant articles quickly. Search profiles make searching for different kinds of content easier, while the new advanced search interface makes light work of complex searches.
<div style=\"clear:both;\"></div>",
);

/** Message documentation (Message documentation)
 * @author EugeneZelenko
 * @author Fryed-peach
 * @author Purodha
 */
$messages['qqq'] = array(
	'optin-desc' => 'Açai is the name of the release, do not translate. In this context, "Beta" is a stage in software development.',
	'optin-title-optedout' => 'Title of Special:UsabilityOptIn when the user is opted out',
	'optin-title-optedin' => 'Title of Special:UsabilityOptIn when the user is opted in',
	'optin-title-justoptedin' => 'Title of Special:UsabilityOptIn when the user has just opted in',
	'optin-title-justoptedout' => 'Title of Special:UsabilityOptIn when the user has just opted out',
	'optin-accept-short' => 'Upper half of caption for Yes button on opt-in form',
	'optin-accept-long' => 'Bottom half of caption for Yes button on opt-in form',
	'optin-deny-short' => 'Upper half of caption for No button on opt-in form',
	'optin-deny-long' => 'Bottom half of caption for No button on opt-in form for logged-in users',
	'optin-submit-out' => 'Caption for submit button on opt-out/survey form',
	'optin-try' => 'Caption for the link left of the user tools with which people can opt in',
	'optin-leave' => 'Caption for the link left of the user tools with which people can opt out',
	'optin-survey-yes' => '{{Identical|Yes}}',
	'optin-survey-no' => '{{Identical|No}}',
	'optin-survey-intro' => 'Do not translate #leave',
	'optin-survey-answer-whyoptout-notpredictable' => '"not predictably" means somehing like "randomly". Results of clicks are not alway the same.',
	'optin-survey-answer-whyoptout-other' => '{{Identical|Other reason}}',
	'optin-survey-answer-os-other' => '',
	'optin-survey-answer-changes-other' => '{{Identical|Other}}',
	'optin-improvements' => 'A few paragraphs of wikitext describing the improvements made in Beta. The referenced images have been uploaded to Commons',
);

/** Aramaic (ܐܪܡܝܐ)
 * @author Basharh
 */
$messages['arc'] = array(
	'optin-survey-answer-os-other' => '',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'optin' => 'Удзел у Ініцыятыве па паляпшэньню зручнасьці і прастаты выкарыстаньня',
	'optin-desc' => 'Дазваляе ўдзельнікам паспрабаваць Beta, апошняе паляпшэньне, зробленае ў рамках ініцыятывы па паляпшэньню зручнасьці і прастаты выкарыстаньня',
	'optin-title-optedout' => 'Паспрабаваць Beta',
	'optin-title-optedin' => 'Пакінуць Beta',
	'optin-title-justoptedin' => 'Вітаем у Beta',
	'optin-title-justoptedout' => 'Дзякуй за спробу выкарыстаньня Beta',
	'optin-title-feedback' => 'Даць зваротную інфармацыю',
	'optin-needlogin' => 'Вам неабходна [$1 увайсьці ў сыстэму] каб далучыцца да Beta',
	'optin-intro' => 'У рамках ініцыятывы па паляпшэньню зручнасьці і прастаты выкарыстаньня Вікіпэдыі была праведзеная буйная праца па паляпшэньню зручнасьці і прастаты выкарыстаньня Вікіпэдыі. Вы жадаеце паспрабаваць Beta?',
	'optin-feedback-intro' => 'Дзякуй за выкарыстаньне Beta.
Нам цікава ведаць, што Вы мяркуеце пра наш новы інтэрфэйс, таму мы былі б удзячныя, калі Вы запоўнілі неабавязковае апытаньне ўнізе.',
	'optin-feedback-back' => 'Калі Вы не жадаеце запаўняць апытаньне, Вы можаце вярнуцца да $1.',
	'optin-success-in' => 'Цяпер Вы прымаеце ўдзел у выпрабаваньні Beta.
Вы можаце пакінуць выпрабаваньне ў любы час націснуўшы кнопку «{{int:optin-leave}}» у верхяй частцы экрана.',
	'optin-leave-cancel' => 'Калі Вы жадаеце працягваць выкарыстаньне Beta, Вы можаце вярнуцца да $1',
	'optin-success-out' => 'Вы пасьпяхова пакінулі Beta.
Вы можаце зноў паспрабаваць яе націснуўшы кнопку «{{int:optin-try}}» у правым верхнім куце экрана.',
	'optin-accept-short' => 'Зрабіць гэта!',
	'optin-accept-long' => 'Паспрабаваць Beta',
	'optin-accept-long-anon' => 'Увайсьці ў сыстэму і паспрабаваць Beta',
	'optin-deny-short' => 'Не, дзякуй',
	'optin-deny-long' => 'вярнуцца да папярэдняй старонкі',
	'optin-submit-out' => 'Пакінуць Beta',
	'optin-try' => 'Паспрабаваць Beta',
	'optin-leave' => 'Пакінуць Beta',
	'optin-survey-yes' => 'Так',
	'optin-survey-no' => 'Не',
	'optin-survey-intro' => 'Дзякуй за выпрабаваньне Beta.

Мы жадаем ведаць, што Вы мяркуеце пра новы інтэрфэйс, з-за гэтага мы былі б удзячныя, калі б Вы прынялі ўдзел у неабавязковым апытаньні, перад тым як Вы націсьніце «[[#leave|{{int:optin-submit-out}}]]».',
	'optin-survey-question-likedislike' => 'Што Вам спадабалася ў Beta? Што Вам не спадабалася ў Beta?',
	'optin-survey-question-whyoptout' => 'Чаму Вы вырашылі пакінуць Beta? (калі ласка, выберыце ўсе прычыны)',
	'optin-survey-answer-whyoptout-hard' => 'Занадта складаны ў выкарыстаньні.',
	'optin-survey-answer-whyoptout-didntwork' => 'Ён не працуе належным чынам.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Ён працуе не як чакалася.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Мне не спадабаўся зьнешні выгляд.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Мне не спадабаліся новыя закладкі і кампаноўка.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Мне не спадабалася новая панэль інструмэнтаў.',
	'optin-survey-answer-whyoptout-other' => 'Іншая прычына:',
	'optin-survey-question-explain' => 'Калі ласка, растлумачце ці ўдакладніце Ваш адказ ніжэй:',
	'optin-survey-question-techfail' => 'Вы сутыкнуліся з якімі-небудзь тэхнічнымі праблемамі пад час выкарыстаньня Beta?',
	'optin-survey-question-techfail-ifyes' => 'Калі так, калі ласка ўдакладніце ці растлумачце:',
	'optin-survey-question-usedtoolbar' => 'Вы выкарыстоўвалі новую панэль інструмэнтаў пры рэдагаваньні старонкі?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Калі так, што Вам у ёй спадабалася? Што не спадабалася?',
	'optin-survey-question-usedtoolbar-ifno' => 'Калі не, чаму? Ці Вы запоўніце яе ў будучыні? Калі ласка, растлумачце.',
	'optin-survey-question-different' => 'Што нам можна зрабіць інакш, каб Вы працягвалі карыстацца нашай Beta?',
	'optin-survey-question-feedback' => 'Калі ласка, паведаміце нам Вашае меркаваньне:',
	'optin-survey-question-browser' => 'Якім браўзэрам Вы карыстаецеся?',
	'optin-survey-answer-browser-other' => 'Іншы браўзэр:',
	'optin-survey-question-os' => 'Якой апэрацыйнай сыстэмай Вы карыстаецеся?',
	'optin-survey-answer-os-other' => 'Іншая апэрацыйная сыстэма:',
	'optin-survey-question-res' => 'Якое разрозьненьне Вашага манітора?',
	'optin-survey-question-changes' => 'Якія зьмены Вы жадаеце бачыць у Beta ў будучыці (выберыце не болей трох)?',
	'optin-survey-answer-changes-nav' => 'Зрабіць навігацыю па сайту больш простай у выкарыстаньні.',
	'optin-survey-answer-changes-edittools' => 'Пашырыць і палепшыць інструмэнты рэдагаваньня.',
	'optin-survey-answer-changes-upload' => 'Палепшыць загрузку зьместу і мэдыя-файлаў.',
	'optin-survey-answer-changes-richtext' => 'Распрацаваць прылады рэдагаваньня, падобныя на тэкставыя рэдактары.',
	'optin-survey-answer-changes-lookfeel' => 'Абнавіць «зьнешні выгляд і запаўненьне» сайта.',
	'optin-survey-answer-changes-predictability' => 'Палепшыць прадказальнасьць хуткадзеяньня.',
	'optin-survey-answer-changes-compat' => 'Палепшыць сумяшчальнасьць з лякальнай вікі.',
	'optin-survey-answer-changes-other' => 'Іншыя:',
	'optin-improvements' => '== Што было палепшана? ==
[[File:UsabilityNavigation.png|left]]
;Паляпшэньне навігацыі
Новая навігацыйная сыстэма дазвяляе лягчэй зразумець дзе Вы знаходзіцеся і што робіце, а так сама куды Вы можаце перайсьці і як гэта зрабіць. Поле пошуку перанесена з левай навігацыйнай панэлі ў верхні правы вугал для палягчэньня і паскарэньня пошуку.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
;Паляпшэньне панэлі рэдагаваньняў
Новыя і палепшаныя значкі дазваляюць лягчэй зразумець якія дзеяньні выконвае кожная прылада. Адчыняемыя сэктары зьмяньшаюць тлум, захоўваючы рэдка выкарыстоўваемыя прылады для кліку мышкай. Дапаможны падзел забясьпечвае хуткі доступ да прыкладаў па найбольш выкарыстоўваемым тыпам вікі-метак.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Паляпшэньне інтэрфэйсу пошуку
Калі Вы карыстаецеся пошукам, супадзеньні з назвамі старонак будуць адлюстроўвацца ў першую чаргу, што дапаможа Вам хутчэй знайсьці самыя адпаведныя артыкулы. Профілі пошуку палягчаюць пошук па розных відаў інфармацыі, а новы інтэрфэйс пошуку палягчае працу са складанымі запытамі. 
<div style="clear:both;"></div>',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'optin' => 'Prijava na Inicijativu upotrebljivosti',
	'optin-desc' => 'Omogućuje korisnicima da isprobaju Beta, posljednje poboljšanje upotrebljivosti putem Inicijative upotrebljivosti.',
	'optin-title-optedout' => 'Isprobaj Beta',
	'optin-title-optedin' => 'Napusti Beta',
	'optin-title-justoptedin' => 'Dobrodošli na Beta',
	'optin-title-justoptedout' => 'Hvala što isprobavate Beta',
	'optin-needlogin' => 'Morate se [$1 prijaviti] da biste mogli isprobati Beta.',
	'optin-intro' => 'Wikipedia Inicijativa upotrebljivosti radi na poboljšanju upotrebljivosti Wikipedije. Da li želite isprobati Beta?',
	'optin-success-in' => 'Sada možete isprobati Beta.
Možete se bilo kada odjaviti putem klikanja na link "{{int:optin-leave}}" u gornjem dijelu ekrana.',
	'optin-leave-cancel' => 'Ako biste željeli nastaviti koristiti Beta, možete se vratiti na $1.',
	'optin-success-out' => 'Uspješno ste napustili Beta.
Možete se bilo kada ponovno prijaviti putem klika na link "{{int:optin-try}}" u gornjem desnom uglu ekrana.',
	'optin-accept-short' => 'Započni!',
	'optin-accept-long' => 'Isprobajte Beta',
	'optin-deny-short' => 'Ne hvala',
	'optin-deny-long' => 'nazad na prethodnu stranicu',
	'optin-submit-out' => 'Napusti Beta',
	'optin-try' => 'Isprobajte Beta',
	'optin-leave' => 'Napusti Beta',
	'optin-survey-yes' => 'Da',
	'optin-survey-no' => 'Ne',
	'optin-survey-intro' => 'Hvala Vam za isprobavanje Beta.

Željeli bismo da nam opišete šta mislite o našem novom interfejsu, tako Vas ljubazno molimo da popunite neobavezni upitnik koji se nalazi ispod prije nego što kliknete na "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Šta Vam se sviđa u Beti? Šta Vam se ne sviđa u Beti?',
	'optin-survey-question-whyoptout' => 'Zašto se odjavljujete s ovog Beta? (molimo odaberite sve što se može primijeniti)',
	'optin-survey-answer-whyoptout-hard' => 'Isuviše je teško korištenje.',
	'optin-survey-answer-whyoptout-didntwork' => 'Nije pravilno funkcioniralo.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Nije se ponašao kako je uobičajeno.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Nije mi se svidjelo kako izgleda.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Ne sviđaju mi se novi jezičci i dizajn.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Ne sviđa mi se nova alatna traka.',
	'optin-survey-answer-whyoptout-other' => 'Ostali preglednici:',
	'optin-survey-question-explain' => 'Molimo objasnite Vaš odgovor odozgo:',
	'optin-survey-question-techfail' => 'Da li ste primijetili neke tehničke pogreške dok ste koristili Beta?',
	'optin-survey-question-techfail-ifyes' => 'Ako da, molimo objasnite:',
	'optin-survey-question-usedtoolbar' => 'Da li ste koristili novu proširivu alatnu traku za uređivanje stranica?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Ako da, šta Vam se sviđa kod toga? Šta Vam se u tome ne sviđa?',
	'optin-survey-question-different' => 'Šta bi morali napraviti drugačije da bi se Vi odlučili nastaviti koristiti Betu?',
	'optin-survey-question-feedback' => 'Molimo pošaljite nam Vaš odgovor:',
	'optin-survey-question-browser' => 'Koji preglednik koristite?',
	'optin-survey-answer-browser-other' => 'Ostali preglednici:',
	'optin-survey-question-os' => 'Koji operativni sistem koristite?',
	'optin-survey-answer-os-other' => 'Drugi operativni sistemi:',
	'optin-survey-question-res' => 'Koja je rezolucija Vašeg monitora?',
	'optin-improvements' => '== Šta je poboljšano? ==
[[File:UsabilityNavigation.png|left]]
;Poboljšana navigacija
Jasna naznaka stanja "čitanja" i "uređivanja" kada ste na stranici članka ili stranici za razgovor. Kutija za pretaživanje je uklonjena iz navigacione trake sa lijeve strane u gornji desni ugao zbog lakšeg pristupa i bržeg traženja.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
;Poboljšanja trake za uređivanje
Traka za uređivanje koja se može sakriti može sakriti ikone alata koji se ne upotrebljavaju često. Potvrđeni korisnici mogu proširiti alatnu traku i pristupiti posebnim ikonama.
Pomoćne informacije (prečice) su ugrađene u alatnu traku.
Ikone alatne trake su nanovo dizajnirane koristeći ikone Tango i Gnome.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Poboljšan interfejs pretrage
Pošto je stranica pretrage vrlo često put pristupa nekom članku, naslovi rezultata su istaknuti s poboljšanom preglednošću, a nepotrebne i manje korištene informacije su smanjene.
<div style="clear:both;"></div>',
);

/** Sorani (Arabic script) (‫کوردی (عەرەبی)‬)
 * @author Marmzok
 */
$messages['ckb-arab'] = array(
	'optin-title-optedout' => 'تاقی‌کردنەوەی بێتا',
	'optin-title-optedin' => 'وازهێنان لە بێتا',
	'optin-title-justoptedin' => 'بەخێرهاتی بۆ بێتا',
	'optin-title-justoptedout' => 'سوپاس بۆ تاقی‌کردنەوەی بێتا',
	'optin-leave-cancel' => 'گەر دەتەوێ لە کەڵک وەرگرتنی بێتا بەردەوام بی، دەتوانی بگەرێیتەوە بۆ $1.',
	'optin-accept-short' => 'با ئەنجامی دەین!',
	'optin-deny-short' => 'سوپاس، نەخێر',
	'optin-deny-long' => 'گەڕانەوە بۆ لاپەڕەی پێشوو',
	'optin-submit-out' => 'وازهێنان لە بێتا',
	'optin-survey-yes' => 'بەڵێ',
	'optin-survey-no' => 'نەخێر',
	'optin-survey-question-likedislike' => 'کاتی بەکارهێنان سەبارەت بە بێتا چیت بەدڵ بوو؟ چیت بە دڵ نەبوو؟',
	'optin-survey-answer-whyoptout-hard' => 'کەڵک وەرگرتنی زۆر زەحمەت بوو.',
	'optin-survey-answer-whyoptout-other' => 'هۆکاری دیکە:',
	'optin-survey-question-browser' => 'کام گەڕۆک بەکار دەهێنی؟',
	'optin-survey-answer-browser-other' => 'گەڕۆکی دیکە:',
);

/** German (Deutsch)
 * @author Als-Holder
 * @author Benzen
 * @author Metalhead64
 * @author Pill
 */
$messages['de'] = array(
	'optin' => 'Anmeldung bei der Benutzerfreundlichkeitsinitiative',
	'optin-desc' => 'Erlaubt Benutzern, Beta auszuprobieren, aktuellste Benutzerfreundlichkeitsverbesserungen der Benutzerfreundlichkeitsinitiative.',
	'optin-title-optedout' => 'Beta ausprobieren',
	'optin-title-optedin' => 'Beta verlassen',
	'optin-title-justoptedin' => 'Willkommen bei Beta',
	'optin-title-justoptedout' => 'Vielen Dank, dass du Beta ausprobierst',
	'optin-title-feedback' => 'Feedback geben',
	'optin-needlogin' => 'Du musst dich [$1 anmelden], um Beta auszuprobieren',
	'optin-intro' => 'Die Wikipedia-Benutzerfreundlichkeitsinitiative hat hart daran gearbeitet, die Benutzerfreundlichkeit von Wikipedia zu verbessern.
Möchtest du Beta ausprobieren?',
	'optin-feedback-intro' => 'Danke, dass du Beta ausprobierst.
Wir möchten gerne erfahren, was du über unsere neue Benutzeroberfläche denkst und würden uns freuen, wenn du an der folgenden, freiwilligen Umfrage teilnimmst',
	'optin-feedback-back' => 'Wenn du die Umfrage nicht ausfüllen willst, kannst du zu $1 zurückkehren.',
	'optin-success-in' => 'Du probierst jetzt Beta aus.
Du kannst es jederzeit verlassen, indem du den Link „{{int:optin-leave}}“ oben anklickst.',
	'optin-leave-cancel' => 'Wenn du Beta weiter verwenden willst, kannst du zu $1 zurückkehren',
	'optin-success-out' => 'Du hast Beta erfolgreich verlassen.
Du kannst es jederzeit wieder ausprobieren, indem du den Link „{{int:optin-try}}“ oben rechts anklickst.',
	'optin-success-feedback' => 'Danke für dein Feedback!',
	'optin-accept-short' => 'Los!',
	'optin-accept-long' => 'Beta ausprobieren',
	'optin-accept-long-anon' => 'Anmelden und Beta ausprobieren',
	'optin-deny-short' => 'Nein, danke',
	'optin-deny-long' => 'Zurück zur letzten Seite',
	'optin-submit-out' => 'Beta verlassen',
	'optin-submit-feedback' => 'Feedback geben',
	'optin-try' => 'Beta ausprobieren',
	'optin-leave' => 'Beta verlassen',
	'optin-feedback' => 'Feedback geben',
	'optin-survey-yes' => 'Ja',
	'optin-survey-no' => 'Nein',
	'optin-survey-intro' => 'Danke, dass du Beta ausprobierst.
Wir möchten gerne erfahren, was du über unsere neue Schnittstelle denkst. Wir begrüßen es, wenn du die optionale Umfrage unten beantwortest, bevor du auf „[[#leave|{{int:optin-submit-out}}]]“ klickst.',
	'optin-survey-question-likedislike' => 'Was magst du an Beta? Was magst du nicht an Beta?',
	'optin-survey-question-whyoptout' => 'Warum verlässt du diesen Betatest? (bitte alles Zutreffende auswählen)',
	'optin-survey-answer-whyoptout-hard' => 'Die Verwendung war zu kompliziert.',
	'optin-survey-answer-whyoptout-didntwork' => 'Es funktioniert nicht einwandfrei.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Es funktioniert nicht in vorhersehbarer Weise.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Ich mag das Aussehen nicht.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Ich mag die neuen Tabs und das Layout nicht.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Ich mag die neue Werkzeugleiste nicht.',
	'optin-survey-answer-whyoptout-other' => 'Andere Begründung:',
	'optin-survey-question-explain' => 'Bitte erkläre deine obige Antwort oder arbeite sie aus:',
	'optin-survey-question-techfail' => 'Hast du beim Ausprobieren von Beta irgendwelche technischen Fehler entdeckt?',
	'optin-survey-question-techfail-ifyes' => 'Falls ja, bitte ausarbeiten oder erklären:',
	'optin-survey-question-usedtoolbar' => 'Verwendest du die neue, expandierbare Bearbeitungs-Werkzeugleiste?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Falls ja, was magst du daran? Was magst du nicht daran?',
	'optin-survey-question-usedtoolbar-ifno' => 'Falls nicht, warum? Wirst du das in Zukunft tun? Bitte erkläre es.',
	'optin-survey-question-different' => 'Was können wir anders machen, dass du unseren Betatest weiter verwendest?',
	'optin-survey-question-feedback' => 'Bitte gib uns dein Feedback:',
	'optin-survey-question-browser' => 'Welchen Browser verwendest du?',
	'optin-survey-answer-browser-other' => 'Anderer Browser:',
	'optin-survey-question-os' => 'Welches Betriebssystem verwendest du?',
	'optin-survey-answer-os-other' => 'Anderes Betriebssystem:',
	'optin-survey-question-res' => 'Was ist deine Bildschirmauflösung?',
	'optin-survey-question-changes' => 'Welche Änderungen an der Beta würdest du gerne in Zukunft sehen (wähle bis zu drei aus)?',
	'optin-survey-answer-changes-nav' => 'Die Gesamtnavigation der Site einfacher machen.',
	'optin-survey-answer-changes-edittools' => 'Bearbeitungswerkzeuge erweitern und verbessern.',
	'optin-survey-answer-changes-upload' => 'Inhalt und Medienuploads verbessern.',
	'optin-survey-answer-changes-richtext' => 'Bearbeitungswerkzeuge ähnlich wie bei Rich-Text-Editoren entwickeln.',
	'optin-survey-answer-changes-lookfeel' => 'Erscheinungsbild der Site aktualisieren.',
	'optin-survey-answer-changes-predictability' => 'Vorhersehbarkeit der Leistung verbessern.',
	'optin-survey-answer-changes-compat' => 'Kompatibilität mit dem lokalen Wiki verbessern.',
	'optin-survey-answer-changes-other' => 'Andere:',
	'optin-improvements' => '== Was wurde verbessert? ==
[[File:UsabilityNavigation.png|left]]
;Erweiterte Navigation
Das neue Navigationssystem macht es nicht nur einfacher zu sehen, wo du bist und was du tust, sondern auch wohin du wie gehen kannst. Mit der Suchbox in der oberen rechten Ecke wird das Finden einfacher und schneller.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
;Verbesserungen der Bearbeiten-Werkzeugleiste
Neue und verbesserte Symbole machen es klarer, welche Aktion hinter jedem Tool steckt. Expandierbare Abschnitte reduzieren Verwirrungen, während selten verwendete Tools nur einen Klick entfernt sind.
Der Hilfeabschnitt sorgt für schnellen Zugriff auf häufig verwendete Wikisyntax.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Verbesserte Suchoberfläche
Wenn du die Suche verwendest, werden die Titeltreffer zuerst angezeigt. Sie hilft dir, die relevantesten Artikel schnell zu finden. Suchprofile machen das Suchen für verschiedene Arten von Inhalt einfacher, während die neue erweiterte Suchoberflächen komplexe Suchen einfacher macht.
<div style="clear:both;"></div>',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'optin' => 'Iniciatiwa wužywajobnosći wubraśe',
	'optin-desc' => 'Zmóžnja wužywarjam pólěpšenja wužywajobnosći iniciatiwy wužywajobnosći wubraś',
	'optin-needlogin' => 'Musyš se [$1 pśizjawiś], aby pólěpšenja wužywajobnosći iniciatiwy wužywajobnosći wubrał.',
	'optin-intro' => 'Iniciatiwa wužywajobnosći Wikipedije jo nowu suknju a nowu wobźěłowańsku kšomu wuwył, aby wužywajobnosć Wikipedije pólěpšyła. Toś te pólěpšenja hyšći njejsu za wšych wužywarjow zmóznjone, ale móžoš je pśez kliknjenje na "{{int:optin-submit-in}}" dołojce wubraś.',
	'optin-success-in' => 'Sy wuspěšnje pólěpšenja wužywajobnosći iniciatiwy wužywajobnosći wubrał.
Móžoš je kuždy cas pśez kliknjenje na "{{int:optin-submit-out}}" dołojce wótwóliś.',
	'optin-success-out' => 'Sy wuspěšnje pólěpšenja wužywajobnosći iniciatiwy wužywajobnosći wótwólił.
Móžoš je kuždy cas pśez kliknjenje na "{{int:optin-submit-in}}" dołojce wubraś.',
	'optin-submit-out' => 'Wótwóliś',
	'optin-survey-intro' => 'Źěkujomy se za wuproběrowanje pólěpšenjow wužywajobnosći iniciatiwy wužywajobnosći.
Móžoš je pśez kliknjenje na "{{int:optin-submit-out}}" dołojce na boku wótwóliś.

My by rady wěźeli, co mysliš wó našem nowem wužywarskem pówjerchu, togodla my by se wjaselili, jolic ty by wupołnił opcionalny napšašnik, nježli až kliknjoš na "{{int:optin-submit-out}}".',
	'optin-survey-question-whyoptout' => 'Cogodla wótwólujoš toś ten wužywarski pówjerch?',
	'optin-survey-answer-whyoptout-hard' => 'Wobźěłowanje boka jo pśeśěžko było.',
	'optin-survey-answer-whyoptout-didntwork' => 'Njejo pórědnje funkcioněrowało.',
	'optin-survey-answer-whyoptout-other' => 'Druga pśicyna:',
	'optin-survey-question-feedback' => 'Pšosym daj nam swóje měnjenja k wěsći:',
	'optin-survey-question-browser' => 'Kótary wobglědowak wužywaš?',
	'optin-survey-answer-browser-other' => 'Drugi wobglědowak:',
	'optin-survey-question-os' => 'Kótary źěłowy system wužywaš?',
	'optin-survey-answer-os-other' => 'Drugi:',
	'optin-survey-question-res' => 'Kótare jo rozeznaśe twójeje wobrazowki?',
);

/** Greek (Ελληνικά)
 * @author Crazymadlover
 * @author Omnipaedista
 */
$messages['el'] = array(
	'optin-title-optedout' => 'Δοκιμάστε την Beta',
	'optin-title-optedin' => 'Εγκαταλείψτε την Beta',
	'optin-title-justoptedin' => 'Καλωσήρθατε στην Beta',
	'optin-needlogin' => 'Πρέπει να [$1 συνδεθείς] για να δοκιμάσεις την Beta',
	'optin-accept-short' => 'Ας το κάνουμε!',
	'optin-accept-long' => 'Δοκιμάστε την Beta',
	'optin-deny-short' => 'Όχι, ευχαριστώ.',
	'optin-deny-long' => 'Επιστροφή στην προηγούμενη σελίδα',
	'optin-submit-out' => 'Εγκαταλείψτε την Beta',
	'optin-try' => 'Δοκιμάστε την Beta',
	'optin-leave' => 'Εγκαταλείψτε την Beta',
	'optin-survey-yes' => 'Ναι',
	'optin-survey-no' => 'Όχι',
	'optin-survey-answer-whyoptout-hard' => 'Ήταν δύσχρηστο.',
	'optin-survey-answer-whyoptout-didntwork' => 'Δεν λειτουργούσε σωστά.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Δεν ανταποκρινόταν προβλέψιμα.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Δεν μου άρεσε η εμφάνισή του.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Δεν μου άρεσαν οι νέες καρτέλες και η γενική σχεδιαστική διάταξη.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Δεν μου άρεσε η νέα μπάρα εργαλείων.',
	'optin-survey-answer-whyoptout-other' => 'Άλλος λόγος:',
	'optin-survey-question-explain' => 'Παρακαλώ δώστε μία πληρέστερη εξήγηση της παραπάνω απάντησής σας:',
	'optin-survey-question-techfail-ifyes' => 'Εάν ναι, παρακαλώ δώστε μία πληρέστερη εξήγηση:',
	'optin-survey-question-usedtoolbar-ifyes' => 'Εάν ναι, τι σας άρεσε και τι δεν σας άρεσε σε αυτό;',
	'optin-survey-question-browser' => 'Ποιον πλοηγό (browser) χρησιμοποιείτε;',
	'optin-survey-answer-browser-ie5' => 'Internet Explorer 5',
	'optin-survey-answer-browser-ie6' => 'Internet Explorer 6',
	'optin-survey-answer-browser-ie7' => 'Internet Explorer 7',
	'optin-survey-answer-browser-ie8' => 'Internet Explorer 8',
	'optin-survey-answer-browser-ff1' => 'Firefox 1',
	'optin-survey-answer-browser-ff2' => 'Firefox 2',
	'optin-survey-answer-browser-ff3' => 'Firefox 3',
	'optin-survey-answer-browser-c1' => 'Google Chrome 1',
	'optin-survey-answer-browser-c2' => 'Google Chrome 2',
	'optin-survey-answer-browser-s3' => 'Safari 3',
	'optin-survey-answer-browser-s4' => 'Safari 4',
	'optin-survey-answer-browser-o9' => 'Opera 9',
	'optin-survey-answer-browser-o9.5' => 'Opera 9.5',
	'optin-survey-answer-browser-o10' => 'Opera 10',
	'optin-survey-answer-browser-other' => 'Άλλος περιηγητής:',
	'optin-survey-question-os' => 'Ποιο λειτιυργικό σύστημα χρησιμοποιείτε;',
	'optin-survey-answer-os-windows' => 'Windows',
	'optin-survey-answer-os-macos' => 'Mac OS',
	'optin-survey-answer-os-linux' => 'Linux',
	'optin-survey-answer-os-other' => 'Άλλο λειτουργικό σύστημα:',
	'optin-survey-question-res' => 'Ποια είναι η ανάλυση της οθόνης σας;',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'optin-title-optedout' => 'Provi betan version de Açai',
	'optin-title-justoptedin' => 'Bonvenon al Beta',
	'optin-needlogin' => 'Vi devas [$1 ensaluti] por provi Beta-n.',
	'optin-accept-short' => 'Ek!',
	'optin-deny-short' => 'Ne, dankon.',
	'optin-deny-long' => 'Reiri al antaŭa paĝo',
	'optin-submit-out' => 'Forlasu Beta-n',
	'optin-survey-yes' => 'Jes',
	'optin-survey-no' => 'Ne',
	'optin-survey-question-whyoptout' => 'Kial vi forlasas ĉi tiun beta-version? (bv. elekti ĉion taŭgan)',
	'optin-survey-answer-whyoptout-hard' => 'Ĝi estis tro malfacila uzi.',
	'optin-survey-answer-whyoptout-didntwork' => 'Ĝi ne funkciis ĝuste.',
	'optin-survey-answer-whyoptout-other' => 'Alia kialo:',
	'optin-survey-question-feedback' => 'Bonvolu diri al ni viajn rimarkojn:',
	'optin-survey-question-browser' => 'Kiun retumilon vi uzas?',
	'optin-survey-answer-browser-other' => 'Alia retumilo:',
	'optin-survey-question-os' => 'Kiun operaciumon vi uzas?',
	'optin-survey-answer-os-other' => 'Alia operaciumo:',
	'optin-survey-question-res' => 'Kio estas la distingivo de via ekrano?',
);

/** Spanish (Español)
 * @author Crazymadlover
 * @author Locos epraix
 * @author Toliño
 */
$messages['es'] = array(
	'optin' => 'Formar parte de la Iniciativa de usabilidad',
	'optin-desc' => 'Permite a los usuarios probar Beta, los últimos mejoramientos de usabilidad de la iniciativa de Usabilidad.',
	'optin-title-optedout' => 'Probar Beta',
	'optin-title-optedin' => 'Abandonar Beta',
	'optin-title-justoptedin' => 'Bienvenido a Beta',
	'optin-title-justoptedout' => 'Gracias por probar Beta',
	'optin-needlogin' => 'Necesitas [$1 iniciar sesión] para probar Beta',
	'optin-intro' => 'La Iniciativa de usabilidad de Wikipedia ha estado trabajando duro para mejorar la usabilidad de Wikipedia.
Te gustaría probar Beta?',
	'optin-success-in' => 'Estás probando ahora Beta.
Puedes volver a dejar de formar parte en cualquier momento haciendo click en el vínculo "{{int:optin-leave}}" en la parte superior derecha de la pantalla.',
	'optin-leave-cancel' => 'Si te gustaría continuar usando Beta, puedes regresar a $1',
	'optin-success-out' => 'Has abandonado exitosamente Beta.
Puedes probarlo nuevamente en cualquier momento haciendo click en el vínculo "{{int:optin-try}}" en la parte superior derecha de la pantalla.',
	'optin-accept-short' => 'Hagámoslo!',
	'optin-accept-long' => 'Probar Beta',
	'optin-deny-short' => 'No gracias',
	'optin-deny-long' => 'regresar a la página anterior',
	'optin-submit-out' => 'Abandonar Beta',
	'optin-try' => 'Probar Beta',
	'optin-leave' => 'Abandonar Beta',
	'optin-survey-yes' => 'Sí',
	'optin-survey-no' => 'No',
	'optin-survey-intro' => 'Gracias por probar Beta.
Nos gustaría conocer que opinas acerca de la nueva interfaz, apreciaríamos si llenas la encuesta opcional de abajo antes de que hagas click en "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Qué te gustó de Beta? Qué no te gustó de Beta?',
	'optin-survey-question-whyoptout' => 'Porqué estás dejando esta Beta? (por favor selecciona todo lo que aplique)',
	'optin-survey-answer-whyoptout-hard' => 'Fue muy difícil de usar.',
	'optin-survey-answer-whyoptout-didntwork' => 'No funcionó apropiadamente.',
	'optin-survey-answer-whyoptout-notpredictable' => 'No funcionó de manera predecible.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'No me gustó la manera como lució.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'No me gustó las nuevas pestañas y diseño.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'No me gustó la nueva barra de herramientas.',
	'optin-survey-answer-whyoptout-other' => 'Otra razón:',
	'optin-survey-question-explain' => 'Por favor explica o desarrolla tu respuesta arriba:',
	'optin-survey-question-techfail' => 'Experimentaste alguna dificultad técnica cuando usaste Beta?',
	'optin-survey-question-techfail-ifyes' => 'Si es sí, por favor desarrolla o explica:',
	'optin-survey-question-usedtoolbar' => 'Usaste la nueva barra de herramientas expandible para editar una página?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Si es sí, qué te gustó de él? qué te disgustó de él?',
	'optin-survey-question-different' => 'Qué podríamos haber hecho diferente para que te mantuvieses usando nuestra Beta?',
	'optin-survey-question-feedback' => 'Por favor déjanos saber acercar de algunos otros comentarios, inquietudes u opiniones que tengas para nosotros - toda retroalimentación es útil!:',
	'optin-survey-question-browser' => 'Cuál navegador usas?',
	'optin-survey-answer-browser-other' => 'Otro navegador:',
	'optin-survey-question-os' => 'Cuál sistema operativo usas?',
	'optin-survey-answer-os-other' => 'Otro sistema operativo:',
	'optin-survey-question-res' => 'Cuál es la resolución de tu pantalla?',
	'optin-improvements' => '== Qué ha sido mejorado? ==
[[File:UsabilityNavigation.png|left]]
;Navegación mejorada
El nuevo sistema de navegación hace más fácil ver en donde estás y qué estás haciendo, también como dónde puedes ir y cómo ir. Con la tabla de búsqueda localizada en la esquina superior izquierda la búsqueda es más fácil de encontrar y más rápida de usar.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
;Mejoramientos de la barra de herramientas de edición
Nuevos-y-mejorados íconos hacen más claro qué acción cada herramienta realiza. Secciones expandibles reducen abarrotamiento mientras mantienen herramientas usuadas infrecuentemente solo a un clic de distancia.
La sección de ayuda provee acceso rápido a una referencia para usos comunes del lenguaje wiki.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Interfaz de búsqueda mejorada
Cuando use la búsqueda, Coincidencias de título son mostradas primero, Ayudándote a encontrar los artículos más relevantes rápidamente. Buscar perfiles hace la búsqueda de diferentes tipos de contenido más fácil, Mientras la nueva interfaz de búsqueda avanzada facilita las búsquedas complejas.
<div style="clear:both;"></div>',
);

/** Finnish (Suomi)
 * @author Cimon Avaro
 * @author Crt
 * @author Nike
 * @author Str4nd
 */
$messages['fi'] = array(
	'optin' => 'Käytettävyyshankkeen valinnanvarainen käyttöönotto',
	'optin-desc' => 'Salli käyttäjien kokeilla Betaa, käytettävyyshankkeen uusimpia käytettävyyttä parantavia lisäyksiä.',
	'optin-title-optedout' => 'Kokeile Betaa',
	'optin-title-optedin' => 'Jätä Beta',
	'optin-title-justoptedin' => 'Tervetuloa Betaan',
	'optin-title-justoptedout' => 'Kiitos Betan kokeilusta',
	'optin-title-feedback' => 'Anna palautetta',
	'optin-needlogin' => 'Sinun tulee [$1 kirjautua sisään] kokeillaksesi Betaa.',
	'optin-intro' => 'Wikipedian käytettävyyshanke on ahkeroinut parantaakseen Wikipedian käytettävyyttä. 
Haluatko kokeilla Betaa?',
	'optin-feedback-intro' => 'Kiitos Betan kokeilusta.
Haluaisimme tietää, mitä mieltä olet uudesta käyttöliittymästä, joten arvostaisimme jos täyttäisit alapuolelta löytyvän valinnaisen kyselyn.',
	'optin-success-in' => 'Kokeilet nyt Betaa.
Voit poistaa valinnan käytöstä milloin tahansa napsauttamalla ”{{int:optin-leave}}” -painiketta ruudun yläreunassa.',
	'optin-leave-cancel' => 'Jos haluat edelleen käyttää Betaa, voit palata kohteeseen $1',
	'optin-success-out' => 'Olet onnistuneesti jättänyt Betan.
Voit milloin tahansa kokeilla sitä uudelleen ”{{int:optin-submit-in}}” -painikkeesta ruudun oikeasta yläkulmasta.',
	'optin-success-feedback' => 'Kiitos palautteestasi.',
	'optin-accept-short' => 'Tehdään niin!',
	'optin-accept-long' => 'Kokeile Betaa',
	'optin-accept-long-anon' => 'Kirjaudu sisään ja kokeile Betaa',
	'optin-deny-short' => 'Ei kiitos',
	'optin-deny-long' => 'Palaa edelliselle sivulle',
	'optin-submit-out' => 'Poistu Betasta',
	'optin-submit-feedback' => 'Anna palautetta',
	'optin-try' => 'Kokeile Betaa',
	'optin-leave' => 'Jätä Beta',
	'optin-feedback' => 'Anna palautetta',
	'optin-survey-yes' => 'Kyllä',
	'optin-survey-no' => 'Ei',
	'optin-survey-intro' => 'Kiitos Betan kokeilusta.
Haluaisimme tietää, mitä mieltä olet uudesta käyttöliittymästä, joten arvostaisimme jos täyttäisit valinnaisen kyselyn ennen ”[[#leave|{{int:optin-submit-out}}]]” -painikkeen napsautusta.',
	'optin-survey-question-likedislike' => 'Mistä pidit Betassa? Mistä et pitänyt Betassa?',
	'optin-survey-question-whyoptout' => 'Miksi olet poistumassa Betasta? (Valitse kaikki sopivat syyt.)',
	'optin-survey-answer-whyoptout-hard' => 'Se oli liian vaikeakäyttöinen.',
	'optin-survey-answer-whyoptout-didntwork' => 'Se ei toiminut oikein.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Se ei toiminut ennustettavalla tavalla.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'En pitänyt sen ulkonäöstä.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'En pitänyt uusista välilehdistä ja asettelusta.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'En pitänyt uudesta työkalurivistä.',
	'optin-survey-answer-whyoptout-other' => 'Muu syy',
	'optin-survey-question-explain' => 'Olisitteko ystävällinen ja selittäisitte tai selventäisitte edelläolevaa vastaustanne:',
	'optin-survey-question-techfail' => 'Koitko teknisiä häiriöitä käyttäessäsi Betaa?',
	'optin-survey-question-techfail-ifyes' => 'Jos kyllä, olisitko ystävällinen ja selventäisit tai selittäisit:',
	'optin-survey-question-usedtoolbar' => 'Käytitkö uutta laajennettavaa työkaluriviä sivun muokkauksessa?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Jos kyllä, mistä sinä pidit siinä? Et pitänyt siinä?',
	'optin-survey-question-usedtoolbar-ifno' => 'Jos et, miksi? Aiotko tulevaisuudessa? Ole hyvä ja perustele.',
	'optin-survey-question-different' => 'Mitä olisimme voineet tehdä toisin, jotta olisit jatkanut Betan käyttämistä?',
	'optin-survey-question-feedback' => 'Anna meille palautetta',
	'optin-survey-question-browser' => 'Mitä selainta käytät?',
	'optin-survey-answer-browser-other' => 'Muu selain',
	'optin-survey-question-os' => 'Mitä käyttöjärjestelmää käytät?',
	'optin-survey-answer-os-other' => 'Muu käyttöjärjestelmä',
	'optin-survey-question-res' => 'Mikä on näyttösi resoluutio?',
	'optin-survey-question-changes' => 'Mitä muutoksia haluaisit nähdä Betassa tulevaisuudessa (valitse kolme)?',
	'optin-survey-answer-changes-edittools' => 'Lisää ja tehosta mukkaustyökaluita.',
	'optin-survey-answer-changes-upload' => 'Paranna sisällön ja median lähettämistä.',
	'optin-survey-answer-changes-richtext' => 'Kehitä muokkaustyökaluita tekstinkäsittelyohjelmia muistuttavaksi.',
	'optin-survey-answer-changes-lookfeel' => 'Päivitä sivuston ulkonäköä ja käytettävyyttä.',
	'optin-survey-answer-changes-predictability' => 'Paranna esityksen ennustettavuutta.',
	'optin-survey-answer-changes-compat' => 'Paranna yhteensopivuutta paikallisen wikin kanssa.',
	'optin-survey-answer-changes-other' => 'Muu',
);

/** French (Français)
 * @author IAlex
 * @author PieRRoMaN
 */
$messages['fr'] = array(
	'optin' => "Participer à l'initiative d'utilisabilité",
	'optin-desc' => "Permet aux utilisateurs d'essayer la bêta Açai, les améliorations de l'initiative d'utilisabilité",
	'optin-title-optedout' => 'Essayer la bêta Açai',
	'optin-title-optedin' => 'Quitter la bêta Açai',
	'optin-title-justoptedin' => 'Bienvenue à la bêta Açai',
	'optin-title-justoptedout' => 'Merci pour votre participation à la bêta Açai',
	'optin-title-feedback' => 'Donnez votre avis',
	'optin-needlogin' => 'Vous devez vous [$1 connecter] pour essayer la bêta Açai',
	'optin-intro' => "L'initiative d'utilisabilité de Wikipédia a travaillé dur pour améliorer l'utilisabilité de Wikipédia.
Voulez-vous essayer la bêta Açai ?",
	'optin-feedback-intro' => "Merci d'essayer la bêta.
Nous aimerions avoir ce que vous pensez de notre nouvelle interface, nous apprécierions si vous remplissiez le questionnaire ci-dessous.",
	'optin-feedback-back' => 'Si vous ne voulez pas remplir le questionnaire, vous pouvez revenir à $1.',
	'optin-success-in' => "Vous êtes en train de tester Beta.
Vous pouvez interrompre la session à tout moment en cliquant sur le lien « {{int:optin-leave}} » en haut à droite de l'écran.",
	'optin-leave-cancel' => 'Si vous voulez continuer à utiliser la bêta Acai, vous pouvez revenir à $1',
	'optin-success-out' => "Vous avez quitté Beta.
Vous pouvez de nouveau participer à tout moment en cliquant sur le lien « {{int:optin-try}} » en haut à droite de l'écran.",
	'optin-success-feedback' => 'Merci pour votre avis !',
	'optin-accept-short' => 'Allons-y !',
	'optin-accept-long' => 'Essayer la bêta Açai',
	'optin-accept-long-anon' => 'Connectez-vous et essayez la bêta',
	'optin-deny-short' => 'Non merci',
	'optin-deny-long' => 'Revenir à la page précédente',
	'optin-submit-out' => 'Quitter la bêta Açai',
	'optin-submit-feedback' => 'Donnez votre avis',
	'optin-try' => 'Essayer la bêta Açai',
	'optin-leave' => 'Quitter la bêta Açai',
	'optin-feedback' => 'Donnez votre avis',
	'optin-survey-yes' => 'Oui',
	'optin-survey-no' => 'Non',
	'optin-survey-intro' => 'Merci pour essayer la bêta Acai.

Nous aimerions savoir ce que vous pensez de notre nouvelle interface, nous apprécierions si vous remplissiez le sondage optionnel avant de cliquer sur « [[#leave|{{int:optin-submit-out}}]] ».',
	'optin-survey-question-likedislike' => "Qu'avez-vous aimé dans la bêta Acai ? Qu'est-ce que vous n'avez pas aimé dans la bêta Acai ?",
	'optin-survey-question-whyoptout' => 'Pourquoi quittez-vous la bêta ? (sélectionnez tout ce qui convient)',
	'optin-survey-answer-whyoptout-hard' => "Il était trop difficile de l'utiliser.",
	'optin-survey-answer-whyoptout-didntwork' => 'Ça ne fonctionne pas correctement.',
	'optin-survey-answer-whyoptout-notpredictable' => "Cela ne s'est pas passé comme prévu.",
	'optin-survey-answer-whyoptout-didntlike-look' => "Je n'ai pas aimé son aspect.",
	'optin-survey-answer-whyoptout-didntlike-layout' => "Je n'ai pas aimé les nouveaux onglets et la nouvelle disposition.",
	'optin-survey-answer-whyoptout-didntlike-toolbar' => "Je n'ai pas aimé la nouvelle barre d'outils.",
	'optin-survey-answer-whyoptout-other' => 'Autre raison :',
	'optin-survey-question-explain' => 'Expliquez ou élaborez votre réponse ci-dessus :',
	'optin-survey-question-techfail' => 'Avez-vous rencontré des problèmes techniques en utilisant la bêta Açai ?',
	'optin-survey-question-techfail-ifyes' => 'Si oui, expliquez ou élaborez :',
	'optin-survey-question-usedtoolbar' => "Avez-vous utilisé la nouvelle barre d'outils déroulable pour modifier une page ?",
	'optin-survey-question-usedtoolbar-ifyes' => "Si oui, qu'avez vous aimé et que n'avez-vous pas aimé à son propos ?",
	'optin-survey-question-usedtoolbar-ifno' => "Si non, pourquoi ? L'utiliserez-vous dans le future ? expliquez.",
	'optin-survey-question-different' => "Qu'est-ce qui aurait dû être différent pour vous continuiez à utiliser notre bêta ?",
	'optin-survey-question-feedback' => 'Donnez-nous votre réaction :',
	'optin-survey-question-browser' => 'Quel navigateur utilisez-vous ?',
	'optin-survey-answer-browser-other' => 'Autre navigateur :',
	'optin-survey-question-os' => "Quel système d'exploitation utilisez-vous ?",
	'optin-survey-answer-os-other' => "Autre système d'exploitation :",
	'optin-survey-question-res' => 'Quelle est la résolution de votre écran ?',
	'optin-survey-question-changes' => "Qu'aimeriez-vous voir dans la bêta dans le futur (au maximum 3) ?",
	'optin-survey-answer-changes-nav' => 'Fait que la navigation générale du site est plus simple à utiliser.',
	'optin-survey-answer-changes-edittools' => "Améliore et augmente les outils d'édition.",
	'optin-survey-answer-changes-upload' => "Améliore l'import de contenu et de média.",
	'optin-survey-answer-changes-richtext' => 'Développe des outils de modifications similaires aux éditeurs en texte enrichi.',
	'optin-survey-answer-changes-lookfeel' => "Change l'apparence du site.",
	'optin-survey-answer-changes-predictability' => 'Améliore la prévisibilité des performances.',
	'optin-survey-answer-changes-compat' => 'Améliore la compatibilité avec les wikis locaux.',
	'optin-survey-answer-changes-other' => 'Autre :',
	'optin-improvements' => "== Qu'est-ce qui a été amélioré ? ==
[[File:UsabilityNavigation.png|left]]
;Navigation enrichie
Le nouveau système de navigation vous permet de voir plus facilement où vous êtes et ce que vous faîtes, de même que où vous pouvez aller et comment vous y rendre. Avec la boîte de recherche située dans le coin supérieur droit, la fonctionnalité de recherche est plus facile à trouver et plus rapide à utiliser.
<div style=\"clear:both;\"></div>

[[File:UsabilityToolbar.png|left]]
;Améliorations de la barre d'outils
De toutes nouvelles icônes clarifient l'action effectuée par chaque outil. Des sections expansibles réduisent le désordre tout en gardant l'accès aux outils plus rarement utilisés à seulement un clic.
La section d'aide fournit un accès rapide à la documentation sur l'utilisation courante des balises wiki.
<div style=\"clear:both;\"></div>

[[File:UsabilitySearch.png|left]]
;Interface de recherche améliorée
Lorsque vous effectuez une recherche, les correspondances dans les titres sont affichés en premier lieu, pour vous aider à trouver rapidement les articles les plus pertinents. Les profiles de recherche donnent la possibilité de rechercher différents types de contenus plus facilement, et la nouvelle interface avancée de recherche permet d'effectuer facilement des recherches complexes.
<div style=\"clear:both;\"></div>",
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'optin' => 'Participar na Iniciativa de usabilidade',
	'optin-desc' => 'Permite que os usuarios proben Beta, as últimas melloras da Iniciativa de usabilidade.',
	'optin-title-optedout' => 'Probar Beta',
	'optin-title-optedin' => 'Deixar Beta',
	'optin-title-justoptedin' => 'Benvido a Beta',
	'optin-title-justoptedout' => 'Grazas por probar Beta',
	'optin-title-feedback' => 'Dea a súa opinión',
	'optin-needlogin' => 'Ten que [$1 acceder ao sistema] para probar Beta.',
	'optin-intro' => 'A Iniciativa de usabilidade da Wikipedia traballou duro para mellorar a usabilidade da Wikipedia.
Quere probar Beta?',
	'optin-feedback-intro' => 'Grazas por probar Beta.
Queremos saber que pensas da nosa nova interface, polo que lle agradeceriamos que enchese a enquisa opcional que aparece a continuación.',
	'optin-feedback-back' => 'Se non quere encher a enquisa, pode voltar a "$1".',
	'optin-success-in' => 'Xa está probando Beta.
Pode saír en calquera momento facendo clic na ligazón "{{int:optin-leave}}" que aparecerá na parte superior da pantalla.',
	'optin-leave-cancel' => 'Se quere seguir usando Beta, pode voltar a "$1".',
	'optin-success-out' => 'Deixou de probar Beta.
Pode participar nas probas en calquera momento facendo clic na ligazón "{{int:optin-try}}" que aparecerá no canto superior dereito da pantalla.',
	'optin-success-feedback' => 'Grazas polos seus comentarios!',
	'optin-accept-short' => 'Fagámolo!',
	'optin-accept-long' => 'Probar Beta',
	'optin-accept-long-anon' => 'Acceda ao sistema e probe Beta',
	'optin-deny-short' => 'Non, grazas',
	'optin-deny-long' => 'voltar á páxina anterior',
	'optin-submit-out' => 'Deixar Beta',
	'optin-submit-feedback' => 'Enviar a opinión',
	'optin-try' => 'Probar Beta',
	'optin-leave' => 'Deixar Beta',
	'optin-feedback' => 'Dea a súa opinión',
	'optin-survey-yes' => 'Si',
	'optin-survey-no' => 'Non',
	'optin-survey-intro' => 'Grazas por probar Beta.
Gustaríanos saber o que pensa sobre a nosa nova interface, de forma que lle agradeceriamos que enchese a enquisa opcional de embaixo antes de premer en "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Que é o que lle gusta de Beta? E que non?',
	'optin-survey-question-whyoptout' => 'Por que está a saír desta beta? (por favor, seleccione o que sexa conveniente)',
	'optin-survey-answer-whyoptout-hard' => 'Foi moi difícil de usar.',
	'optin-survey-answer-whyoptout-didntwork' => 'Non funcionou correctamente.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Non funcionou de modo predicible.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Non me gustou o seu aspecto.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Non me gustaron as novas lapelas e a distribución.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Non me gustou a nova barra de ferramentas.',
	'optin-survey-answer-whyoptout-other' => 'Outro motivo:',
	'optin-survey-question-explain' => 'Por favor, explique ou elabore as súas respostas anteriores:',
	'optin-survey-question-techfail' => 'Atopouse con algún fallo técnico durante a utilización de Beta?',
	'optin-survey-question-techfail-ifyes' => 'Se a resposta é si, por favor, descríbao ou explíqueo a continuación:',
	'optin-survey-question-usedtoolbar' => 'Usou a nova barra de ferramentas expansible para editar unha páxina?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Se a resposta é si, que é o que lle gustou dela? E que non?',
	'optin-survey-question-usedtoolbar-ifno' => 'Se a resposta é non, por que non? Farao no futuro? Por favor, explíqueo.',
	'optin-survey-question-different' => 'Que poderiamos ter feito diferente para que seguise usando Beta?',
	'optin-survey-question-feedback' => 'Por favor, déixenos a súa opinión:',
	'optin-survey-question-browser' => 'Que navegador usa?',
	'optin-survey-answer-browser-other' => 'Outro navegador:',
	'optin-survey-question-os' => 'Que sistema operativo usa?',
	'optin-survey-answer-os-other' => 'Outros sistemas operativos:',
	'optin-survey-question-res' => 'Cal é a resolución da súa pantalla?',
	'optin-survey-question-changes' => 'Que cambios lle gustaría ver en Beta no futuro (seleccione ata tres opcións)?',
	'optin-survey-answer-changes-nav' => 'Facer a navegación xeral do sitio máis doada de usar.',
	'optin-survey-answer-changes-edittools' => 'Aumentar e mellorar as ferramentas de edición.',
	'optin-survey-answer-changes-upload' => 'Mellorar o contido e as cargas multimedia.',
	'optin-survey-answer-changes-richtext' => 'Desenvolver ferramentas de edición similares aos editores de texto.',
	'optin-survey-answer-changes-lookfeel' => 'Actualizar o aspecto e apariencia do sitio.',
	'optin-survey-answer-changes-predictability' => 'Mellorar a previsibilidade dos resultados.',
	'optin-survey-answer-changes-compat' => 'Mellorar a compatibilidade cos wikis locais.',
	'optin-survey-answer-changes-other' => 'Outros:',
	'optin-improvements' => '== Que se mellorou? ==
[[File:UsabilityNavigation.png|left]]
;Navegación realzada
O novo sistema de navegación fai máis doado ver onde se está e que se está facendo, así como onde se pode ir e como chegar ata alí. Co cadro de procuras localizado no canto superior dereito obtense un acceso máis fácil para así poder realizar unha busca máis rápida.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
;Melloras na barra de ferramentas de edición
As novas e melloradas iconas fan máis clara a identificación da acción que realiza cada unha. As seccións expansibles reducen a desorde á vez que manteñen as ferramentas menos usadas a só un clic de distancia.
A sección de axuda proporciona un acceso rápido a unha referencia cos usos comúns do formato wiki.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Interface de busca mellorada
Ao usar a procura, as coincidencias cos títulos de páxinas aparecen primeiro, axudando a atopar rapidamente os artigos máis relevantes. Os tipos de procura fan máis doadas as buscas de diferentes tipos de contido, mentres que a nova interface avanzada facilita tamén as buscas complexas.
<div style="clear:both;"></div>',
);

/** Ancient Greek (Ἀρχαία ἑλληνικὴ)
 * @author Omnipaedista
 */
$messages['grc'] = array(
	'optin-survey-yes' => 'Ναί',
	'optin-survey-no' => 'Οὐ',
	'optin-survey-answer-whyoptout-other' => 'Αἰτία ἄλλη:',
	'optin-survey-answer-os-other' => 'Ἄλλον λειτουργικὸν σύστημα:',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'optin' => 'Benutzerfrejndligkeits-Initiative uuswehle',
	'optin-desc' => 'Benutzer erlaube Beta z uuszprobiere, di nejschte Verbesserige vu dr Benutzerfrejndligkeits-Initiative.',
	'optin-title-optedout' => 'Beta uusprobiere',
	'optin-title-optedin' => 'Beta verloo',
	'optin-title-justoptedin' => 'Willchuu bi Beta',
	'optin-title-justoptedout' => 'Dankschen, ass du Beta uusprobiersch',
	'optin-title-feedback' => 'Ruckmäldig gee',
	'optin-needlogin' => 'Du muesch Di [$1 aamälde] go Beta uuszprobiere',
	'optin-intro' => 'D Wikipedia-Benutzerfrejndligkeits-Initiative het zimli draa gschafft go d Benutzerfrejndligkeits vu dr Wikipedia z verbessere. 
Witt Beta uusprobiere?',
	'optin-feedback-intro' => 'Dankschen, ass Du Beta uusprobiersch.
Mir wette gärn erfahre, was Du iber unseri nej Benutzeroberflächi dänksch un deeten is freie, wänn Du an däre, frekwillige Umfrog mitmachsch.',
	'optin-feedback-back' => 'Wänn Du die Umfrog nit witt uusfille, no chasch zue $1 zruckgoh.',
	'optin-success-in' => 'Du probiersch jetz Beta uus.
Du chasch s wider ruckgängig mache dur e Klick uf „{{int:optin-leave}}“ obe uf dr Benutzeroberflächi.',
	'optin-leave-cancel' => 'Wänn du Beta wyter witt verwände, chasch zruck goh zue $1',
	'optin-success-out' => 'Du hesch Beta erfolgrych verloo.
Du chasch s wider uusprobiere dur e Klick uf „{{int:optin-try}}“ obe uf dr Benutzeroberflächi.',
	'optin-success-feedback' => 'Dankschen fir Dyy Ruckmäldig!',
	'optin-accept-short' => 'Los goht s!',
	'optin-accept-long' => 'Beta uusprobiere',
	'optin-accept-long-anon' => 'Aamälden un Beta uusprobiere',
	'optin-deny-short' => 'Nei dankschen',
	'optin-deny-long' => 'zruck zue dr letschte Syte',
	'optin-submit-out' => 'Beta verloo',
	'optin-submit-feedback' => 'Ruckmäldig gee',
	'optin-try' => 'Beta uusprobiere',
	'optin-leave' => 'Beta verloo',
	'optin-feedback' => 'Ruckmäldig gee',
	'optin-survey-yes' => 'Jo',
	'optin-survey-no' => 'Nei',
	'optin-survey-intro' => 'Dankschen, ass Du Beta uusprobiert hesch.

Mir wisste gärn, was Du iber di nej Benutzeroberflächi dänksch. Wäge däm deete mer is freie, wänn Du di frejwillig Umfrog deetsch uusfille, voreb Du uf „[[#leave|{{int:optin-submit-out}}]]“ klicksch.',
	'optin-survey-question-likedislike' => 'Was het dir an Beta gfalle? Was het dir an Beta nit gfalle?',
	'optin-survey-question-whyoptout' => 'Wurum verlosch Du des Beta? (bitte alles uuswehle, was zuetrifft)',
	'optin-survey-answer-whyoptout-hard' => 'D Verwändig isch z schwirig gsi.',
	'optin-survey-answer-whyoptout-didntwork' => 'Si het nit rächt funktioniert.',
	'optin-survey-answer-whyoptout-notpredictable' => 'S het nit eso funktioniert, wie s gheisse het.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Mir het s nit gfalle, wie s uussiht.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Mir gfalle di neje Tabs un s Layout nit.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Mir gfallt s nej Wärchzygchäschtli nit.',
	'optin-survey-answer-whyoptout-other' => 'Andere Grund:',
	'optin-survey-question-explain' => 'Bit erklär Dyyni Antwort dobe oder fier si e weng uus:',
	'optin-survey-question-techfail' => 'Hesch du tächnischi Fähler feschtgstellt, wu du Beta brucht hesch?',
	'optin-survey-question-techfail-ifyes' => 'Wänn jo, bitte uusfiere oder erkläre:',
	'optin-survey-question-usedtoolbar' => 'Hesch Du s nej uusklappbar Wärchzygchäschtli brucht go ne Syte  bearbeite?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Wänn jo, was gfallt dir draa? Was het Dir nit draa gfalle?',
	'optin-survey-question-usedtoolbar-ifno' => 'Wänn imfall nit, wurum? Machsch des in Zuechumpft? Bitte erklär s.',
	'optin-survey-question-different' => 'Was hätte mit chenne anderscht mache, ass du unser Beta wyter bruucht hättsch?',
	'optin-survey-question-feedback' => 'Gib is bitte e Ruckmäldig:',
	'optin-survey-question-browser' => 'Wele Browser bruchsch Du?',
	'optin-survey-answer-browser-other' => 'Àndere Browser:',
	'optin-survey-question-os' => 'Wel Betribssyschtem bruchsch Du?',
	'optin-survey-answer-os-other' => 'Anders Betribssyschtem:',
	'optin-survey-question-res' => 'Was fir e Uflesig het Dyy Bildschirm?',
	'optin-survey-question-changes' => 'Weli Änderige wottsch gärn säh bi Beta in Zuechumpft (wehl bis zue drej uus)?',
	'optin-survey-answer-changes-nav' => 'D Gsamtnavigation vu dr Site eifacher mache.',
	'optin-survey-answer-changes-edittools' => 'Bearbeitigswärchzyyg erwytere un verbessere.',
	'optin-survey-answer-changes-upload' => 'Inhalt un s Uffelade vu Medie verbessere.',
	'optin-survey-answer-changes-richtext' => 'Bearbeitigswärchzyyg entwickle, wu ähnlig sin wie d Rich-Text-Editore.',
	'optin-survey-answer-changes-lookfeel' => 'Erschynigsbild vu dr Site aktualisiere.',
	'optin-survey-answer-changes-predictability' => 'Vorhärsähbarkeit vu dr Leischtig verbessere.',
	'optin-survey-answer-changes-compat' => 'Kompatibilität mit em lokale Wiki verbessere.',
	'optin-survey-answer-changes-other' => 'Anderi:',
	'optin-improvements' => '== Was isch verbesseret wore? ==
[[File:UsabilityNavigation.png|left]]
;Verbessereti Navigation
Di nej Navigation macht s eifacher z säh, wu du grad bisch un was du machsch un au wu Du chasch ane goh un wie du dert ane chunnsch. S Suechfäld isch jetz im obere rächte Ecke un eso hscnäller z finde un eifacher z bruche.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
;Verbesserige am Wärchzygchäschtli

Fortgschritteni Benutzer chenne s Wärchzygchäschtli uusklappe go speziälleri Wärchzyg uuswehle.
Hilfe (Spickzettel) sin in s Wärchzygchäschtli dryygsetzt wore.
D Grafike vum Wärchzygchäschtli sin nej designed wore mit Hilf vu Tango- un Gnome-Grafike.
Neji un verbessereti Grafike mache s klarer, was du chasch mache mit dr einzelne Wärchzyg. Uusklappbari Abschnitt verringere s Durenander, di wenig bruchte Wärchzyg sin aber nume ei Klick ewäg.
D Hilf-Sektion stellt e schnölle zuegang z Verfiegig zuen ere Quälle vu gängige Aawändige vu Wiki-Zueschläg 
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Verbessereti Suechoberflächi
Wänn Du d Suechi bruchsch, no wäre d Titel zerscht aazeigt go Dir hälfe bim schnälle Finde vu dr relevanteschte Artikel. Suechprofil mache s Sueche vu verschidene Inhalt eifacher, derwylscht di nej Suechoberflächi kompläxeri Suechine eifacher macht.
<div style="clear:both;"></div>',
);

/** Hebrew (עברית)
 * @author Rotem Liss
 */
$messages['he'] = array(
	'optin' => 'הרשמה ליוזמת השימושיות',
	'optin-desc' => 'אפשרות למשתמשים להירשם כדי לקבל את שיפורי השימושיות של יוזמת השימושיות',
	'optin-needlogin' => 'עליכם [$1 להיכנס לחשבון] כדי להירשם לקבלת שיפורי השימושיות של יוזמת השימושיות.',
	'optin-intro' => 'יוזמת השימושיות של ויקיפדיה פיתחה עיצוב חדש וסרגל עריכה חדש כדי לשפר את השימושיות של ויקיפדיה. שיפורים אלה טרם הופעלו לכל המשתמשים, אבל באפשרותכם להירשם אליהם באמצעות לחיצה על הכפתור "{{int:optin-submit-in}}" שלהלן.',
	'optin-success-in' => 'נרשמתם בהצלחה לקבלת שיפורי השימושיות של יוזמת השימושיות.
באפשרותכם לבטל את הרשמתכם בכל זמן באמצעות לחיצה על הכפתור "{{int:optin-submit-out}}" שלהלן.',
	'optin-success-out' => 'ביטלתם בהצלחה את הרשמתכם לקבלת שיפורי השימושיות של יוזמת השימושיות.
באפשרותכם להירשם שוב בכל זמן באמצעות לחיצה על הכפתור "{{int:optin-submit-out}}" שלהלן.',
	'optin-submit-out' => 'ביטול הרשמה',
	'optin-survey-intro' => 'תודה לכם על שאתם מנסים את שיפורי השימושיות של יוזמת השימושיות.
באפשרותכם לבטל את הרשמתכם באמצעות לחיצה על הכפתור "{{int:optin-submit-out}}" בתחתית הדף.

נרצה לדעת מה אתם חושבים על הממשק החדש, לכן נעריך זאת אם תמלאו את הסקר האופציונלי שלהלן לפני לחיצה על הכפתור "{{int:optin-submit-out}}".',
	'optin-survey-question-whyoptout' => 'למה אתם מבטלים את הרשמתכם לממשק זה?',
	'optin-survey-answer-whyoptout-hard' => 'היה קשה מדי לערוך דף.',
	'optin-survey-answer-whyoptout-didntwork' => 'הוא לא פעל כפי שצריך.',
	'optin-survey-answer-whyoptout-other' => 'סיבה אחרת:',
	'optin-survey-question-feedback' => 'אנא העבירו לנו משוב:',
	'optin-survey-question-browser' => 'באיזה דפדפן אתם משתמשים?',
	'optin-survey-answer-browser-other' => 'דפדפן אחר:',
	'optin-survey-question-os' => 'באיזו מערכת הפעלה אתם משתמשים?',
	'optin-survey-answer-os-other' => 'אחרת:',
	'optin-survey-question-res' => 'מהי רזולוציית המסך שלכם?',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'optin' => 'Iniciatiwa wužiwajomnosće wuzwolenje',
	'optin-desc' => 'Zmóžnja wužiwarjam Beta spytać, najnowše polěpšenja wužiwajomnosće iniciatiwy wužiwajomnosće.',
	'optin-title-optedout' => 'Beta spytać',
	'optin-title-optedin' => 'Beta wopušćić',
	'optin-title-justoptedin' => 'Witaj do Beta',
	'optin-title-justoptedout' => 'Dźakujemy so, zo sy Beta wupruwował',
	'optin-needlogin' => 'Dyrbiš so [$1 přizjewić], zo by Beta wupruwował',
	'optin-intro' => 'Iniciatiwa wužiwajomnosće Wikipedije je nowy šat a nowu wobdźěłowansku gratowu lajstu wuwiła, kotrejž stej za to myslenej, wužiwajomnosć Wikipedije polěpšić. Tute polěpšenja hišće za wšěch wužiwarjow zmóžnjene njejsu, ale móžeš je přez kliknjenje na "{{int:optin-submit-in}}" deleka wuzwolić.',
	'optin-success-in' => 'Wupruwuješ nětko Betu. Móžeš ju kóždy čas přez kliknjenja na wotkaz  kóždy čas přez kliknjenje na "{{int:optin-leave}}" horjeka na wobrazowce wopušćić.',
	'optin-leave-cancel' => 'Jeli rady chceš Beta dale wužiwać, móžeš so k $1 wróćić.',
	'optin-success-out' => 'Sy Betu wuspěšnje wopušćił. Móžeš ju kóždy čas přez kliknjenje na wotkaz "{{int:optin-try}}" horjeka naprawo na wobrazowce wupruwować.',
	'optin-accept-short' => 'Čińmy to!',
	'optin-accept-long' => 'Betu wupruwować',
	'optin-deny-short' => 'Ně, dźakujemy so',
	'optin-deny-long' => 'wróćo k předchadnej stronje',
	'optin-submit-out' => 'Beta wopušćić',
	'optin-try' => 'Betu wupruwować',
	'optin-leave' => 'Beta wopušćić',
	'optin-survey-yes' => 'Haj',
	'optin-survey-no' => 'Ně',
	'optin-survey-intro' => 'Dźakujemy so za wuprobowanje polěpšenjow wužiwajomnosće iniciatiwy wužiwajomnosće. Móžeš je přez kliknjenje na "{{int:optin-submit-out}}" deleka na stronje wotwolić.

Bychmy rady wědźeli, što mysliš wo našim nowym wužiwarskim powjerchu, bychmy so wjeselili, jeli by opcionelny naprašnik deleka wupjelnił, prjedy hač kliknješ na  "{{int:optin-submit-out}}".',
	'optin-survey-question-whyoptout' => 'Čehodla wopušćeš tutu betu? (prošu wubjer wšo prawe)',
	'optin-survey-answer-whyoptout-hard' => 'Wužiwanje je překomplikowane.',
	'optin-survey-answer-whyoptout-didntwork' => 'To njeje porjadnje fungowało.',
	'optin-survey-answer-whyoptout-other' => 'Druha přičina:',
	'optin-survey-question-feedback' => 'Zdźěl nam prošu swoje měnjenja:',
	'optin-survey-question-browser' => 'Kotry wobhladowak wužiwaš?',
	'optin-survey-answer-browser-other' => 'Druhi wobhladowak:',
	'optin-survey-question-os' => 'Kotry dźěłowy system wužiwaš?',
	'optin-survey-answer-os-other' => 'Druhi dźěłowy system:',
	'optin-survey-question-res' => 'Kotre je rozeznaće twojeje wobrazowki?',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'optin' => 'Activation del Initiativa de Usabilitate',
	'optin-desc' => 'Permitte al usatores de probar Beta, le ultime meliorationes del Initiativa de Usabilitate.',
	'optin-title-optedout' => 'Probar Beta',
	'optin-title-optedin' => 'Quitar Beta',
	'optin-title-justoptedin' => 'Benvenite a Beta',
	'optin-title-justoptedout' => 'Gratias pro probar Beta',
	'optin-needlogin' => 'Tu debe [$1 aperir un session] pro probar Beta',
	'optin-intro' => 'Le Initiativa de Usabilitate de Wikipedia ha laborate duro pro augmentar le usabilitate de Wikipedia.
Vole tu probar Beta?',
	'optin-success-in' => 'Tu ha ora comenciate a probar Beta.
Tu pote disactivar lo a omne tempore con le ligamine "{{int:optin-leave}}" in alto a derecta del schermo.',
	'optin-leave-cancel' => 'Si tu vole continuar a usar Beta, tu pote retornar a $1',
	'optin-success-out' => 'Tu ha con successo quitate Beta.
Tu pote reactivar lo a omne tempore con le ligamine "{{int:optin-try}}" in alto a derecta del schermo.',
	'optin-accept-short' => 'In avante!',
	'optin-accept-long' => 'Probar Beta',
	'optin-deny-short' => 'No, gratias',
	'optin-deny-long' => 'retornar al previe pagina',
	'optin-submit-out' => 'Quitar Beta',
	'optin-try' => 'Probar Beta',
	'optin-leave' => 'Quitar Beta',
	'optin-survey-yes' => 'Si',
	'optin-survey-no' => 'No',
	'optin-survey-intro' => 'Gratias pro essayar Beta.
Nos volerea saper lo que tu pensa super nostre nove interfacie, e nos appreciarea si tu completa le sequente questionario optional ante de cliccar super "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Que te placeva in Beta? Que non te placeva in Beta?',
	'optin-survey-question-whyoptout' => 'Proque quita tu iste Beta? (per favor selige tote le optiones applicabile)',
	'optin-survey-answer-whyoptout-hard' => 'Esseva troppo difficile de usar.',
	'optin-survey-answer-whyoptout-didntwork' => 'Non functionava correctemente.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Non functionava de modo previsibile.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Non me placeva le aspecto.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Non me placeva le nove schedas e disposition.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Non me placeva le nove barra de instrumentos.',
	'optin-survey-answer-whyoptout-other' => 'Altere motivo:',
	'optin-survey-question-explain' => 'Per favor explica o elabora tu responsa de ci supra:',
	'optin-survey-question-techfail' => 'Experimentava tu alcun fallimentos technic durante le uso de Beta?',
	'optin-survey-question-techfail-ifyes' => 'In caso de si, per favor elabora o explica:',
	'optin-survey-question-usedtoolbar' => 'Usava tu le nove instrumentario expansibile pro modificar un pagina?',
	'optin-survey-question-usedtoolbar-ifyes' => 'In caso de si, que te placeva in illo? Que non te placeva?',
	'optin-survey-question-different' => 'Que poteva nos haber facite alteremente a fin que tu continuarea a usar nostre Beta?',
	'optin-survey-question-feedback' => 'Da nos tu reaction:',
	'optin-survey-question-browser' => 'Qual navigator usa tu?',
	'optin-survey-answer-browser-other' => 'Altere navigator:',
	'optin-survey-question-os' => 'Qual systema de operation usa tu?',
	'optin-survey-answer-os-other' => 'Altere systema de operation:',
	'optin-survey-question-res' => 'Qual es le resolution de tu schermo?',
	'optin-improvements' => '== Que ha essite meliorate? ==
[[File:UsabilityNavigation.png|left]]
;Navigation plus efficace
Le nove systema de navigation rende plus facile vider ubi tu es e lo que tu face, e tamben a ubi tu pote ir e como arrivar. Con le quadro de recerca situate in alto a derecta, le facilitate de recerca es plus facile de trovar e plus rapide de usar.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
;Meliorationes del instrumentario de modification
Le icones nove e melior clarifica le action que cata instrumento exeque. Le sectiones expansibile simplifica le apparentia, durante que le instrumentos usate infrequentemente resta disponibile a solo un clic de distantia.
Le section de adjuta provide accesso rapide a un referentia pro usos commun del marcation wiki.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Interfacie de recerca meliorate
Quando tu usa le recerca, le correspondentias de titulo es monstrate primo, adjutante te a trovar le articulos plus relevante rapidemente. Con profilos de recerca, il es plus facile cercar differente typos de contento. E con le nove interfacie de recerca avantiate, il es un mer divertimento facer cercas complexe.
<div style="clear:both;"></div>',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 * @author Rex
 */
$messages['id'] = array(
	'optin' => 'Bergabung dengan Proyek Inisiatif Kebergunaan',
	'optin-desc' => 'Memungkinkan pengguna untuk bergabung dengan peningkatan kebergunaan dalam Proyek Inisiatif Kebergunaan',
	'optin-needlogin' => 'Anda harus [$1 masuk log] untuk dapat bergabung dengan peningkatan kebergunaan dari Proyek Inisiatif Kebergunaan.',
	'optin-intro' => 'Proyek Inisiatif Kebergunaan Wikipedia telah mengembangkan sebuah skin baru dan batang alat penyuntingan baru yang ditujukan untuk meningkatkan kebergunaan Wikipedia. Peningkatan ini belum diaktifkan untuk semua pengguna, tapi Anda dapat bergabung dengan Proyek ini dengan mengklik "{{int:optin-submit-in}}" di bawah ini.',
	'optin-success-in' => 'Anda telah berhasil bergabung dengan peningkatan kebergunaan dari Proyek Inisiatif Kebergunaan.
Anda dapat keluar kapan saja dengan mengklik "{{int:optin-submit-out}}" di bawah ini.',
	'optin-success-out' => 'Anda telah berhasil keluar dari Proyek Inisiatif Kebergunaan.
Anda dapat bergabung kembali kapan saja dengan mengklik "{{int:optin-submit-in}}" di bawah ini.',
	'optin-submit-out' => 'Keluar',
	'optin-survey-yes' => 'Ya',
	'optin-survey-no' => 'Tidak',
	'optin-survey-intro' => 'Terima kasih untuk menguji coba peningkatan kebergunaan dari Proyek Inisiatif Kebergunaan.
Anda dapat keluar dengan mengklik "{{int:optin-submit-out}}" pada bagian bawah halaman ini.

Kami ingin mengetahui bagaimana pendapat Anda mengenai antarmuka baru kami, karenanya kami akan sangat menghargai jika Anda mengisi survei opsional berikut sebelum mengklik "{{int:optin-submit-out}}".',
	'optin-survey-question-whyoptout' => 'Mengapa Anda keluar dari antarmuka ini?',
	'optin-survey-answer-whyoptout-hard' => 'Terlalu sulit untuk menyunting halaman.',
	'optin-survey-answer-whyoptout-didntwork' => 'Tidak berfungsi dengan baik.',
	'optin-survey-answer-whyoptout-other' => 'Alasan lain:',
	'optin-survey-question-feedback' => 'Beritahukan kami tanggapan Anda:',
	'optin-survey-question-browser' => 'Penjelajah web apa yang Anda gunakan?',
	'optin-survey-answer-browser-other' => 'Penjelajah web lainnya:',
	'optin-survey-question-os' => 'Sistem operasi apa yang Anda gunakan?',
	'optin-survey-answer-os-other' => 'Lain-lain:',
	'optin-survey-question-res' => 'Berapa besar resolusi layar Anda?',
);

/** Italian (Italiano)
 * @author Darth Kule
 */
$messages['it'] = array(
	'optin-desc' => "Consente agli utenti di provare Beta, gli ultimi miglioramenti dell'iniziativa di usabilità.",
	'optin-title-optedout' => 'Prova Beta',
	'optin-title-optedin' => 'Lascia Beta',
	'optin-title-justoptedin' => 'Benvenuto in Beta',
	'optin-title-justoptedout' => 'Grazie per aver provato Beta',
	'optin-needlogin' => "È necessario [$1 effettuare l'accesso] per provare Beta",
	'optin-intro' => "L'iniziativa di usabilità di Wikipedia sta lavorando intensamente per migliorare l'usabilità di Wikipedia.
Si desidera provare Beta?",
	'optin-leave-cancel' => 'Se si desidera continuare a usare Beta è possibile tornare a $1',
	'optin-success-out' => 'Beta è stato lasciato con successo.
È possibile provarlo nuovamente in qualsiasi momento facendo clic sul collegamento "{{int:optin-try}}" in alto a destra dello schermo.',
	'optin-deny-long' => 'Torna alla pagina precedente',
	'optin-submit-out' => 'Lascia Beta',
	'optin-try' => 'Prova Beta',
	'optin-leave' => 'Lascia Beta',
	'optin-survey-yes' => 'Sì',
	'optin-survey-no' => 'No',
	'optin-survey-answer-whyoptout-hard' => 'Era troppo difficile da usare.',
	'optin-survey-answer-whyoptout-didntwork' => 'Non funzionava correttamente.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Non si comportava come previsto.',
	'optin-survey-answer-whyoptout-didntlike-look' => "Non mi piaceva l'aspetto.",
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Non mi piacevano le nuove schede e il layout.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Non mi piaceva la nuova barra degli strumenti.',
	'optin-survey-answer-whyoptout-other' => 'Altra motivazione:',
	'optin-survey-question-explain' => 'Si prega di spiegare o approfondire la sua risposta di cui sopra:',
	'optin-survey-question-techfail' => 'Si sono verificati problemi tecnici usando Beta?',
	'optin-survey-question-techfail-ifyes' => 'In caso affermativo, si prega di elaborare o spiegare:',
	'optin-survey-question-usedtoolbar' => 'La nuova barra degli strumenti espandibile è stata usata per modificare una pagina?',
);

/** Japanese (日本語)
 * @author Aotake
 * @author Fryed-peach
 * @author Hosiryuhosi
 * @author 青子守歌
 */
$messages['ja'] = array(
	'optin' => 'ユーザビリティー改善への参加',
	'optin-desc' => 'ユーザビリティ改善プロジェクトによる最新のユーザビリティー改善案のベータ版の試用を可能にする',
	'optin-title-optedout' => 'ベータ版を試す',
	'optin-title-optedin' => 'ベータ版をやめる',
	'optin-title-justoptedin' => 'ベータ版にようこそ',
	'optin-title-justoptedout' => 'ベータ版をお試しいただきありがとうございます',
	'optin-title-feedback' => 'フィードバックしてください',
	'optin-needlogin' => 'ベータ版を試すには[$1 ログイン]する必要があります。',
	'optin-intro' => 'ウィキペディア・ユーザビリティ改善プロジェクトでは、ウィキペディアの使いやすさを改善するために活動しています。ベータ版を試してみませんか？',
	'optin-feedback-intro' => 'ベータ版をお試しいただきありがとうございます。よろしければこの新しいインタフェースについてご意見をお聞かせください。以下の任意調査にご協力いただければ幸いです。',
	'optin-feedback-back' => '調査表への記入を望まない場合は、$1 へお戻りください。',
	'optin-success-in' => 'ベータ版を試用しています。画面上部の「{{int:optin-leave}}」のリンクをクリックすると、いつでも試用をやめることができます。',
	'optin-leave-cancel' => 'ベータ版の試用を続ける場合、$1に戻ることができます。',
	'optin-success-out' => 'ベータ版を停止しました。画面右上の「{{int:optin-try}}」のリンクをクリックすると、いつでもまた試用することができます。',
	'optin-success-feedback' => 'フィードバックをご送信いただきありがとうございます！',
	'optin-accept-short' => 'はじめましょう！',
	'optin-accept-long' => 'ベータ版を試す',
	'optin-accept-long-anon' => 'ログインしてベータ版を試す',
	'optin-deny-short' => 'いりません',
	'optin-deny-long' => '前のページに戻る',
	'optin-submit-out' => 'ベータ版をやめる',
	'optin-submit-feedback' => 'フィードバックを送信',
	'optin-try' => 'ベータ版を試す',
	'optin-leave' => 'ベータ版をやめる',
	'optin-feedback' => 'フィードバックしてください',
	'optin-survey-yes' => 'はい',
	'optin-survey-no' => 'いいえ',
	'optin-survey-intro' => 'ベータ版をお試しいただきありがとうございます。よろしければこの新しいインタフェースについてご意見をお聞かせください。 [[#leave|{{int:optin-submit-out}}]]をクリックする前に以下の任意調査にご協力いただければ幸いです。',
	'optin-survey-question-likedislike' => 'ベータ版のどのような点が気に入りましたか。どのような点が気に入りませんでしたか。',
	'optin-survey-question-whyoptout' => 'なぜベータ版の試用をおやめになるのですか（あてはまるものをすべてお選びください）。',
	'optin-survey-answer-whyoptout-hard' => '使いこなせませんでした。',
	'optin-survey-answer-whyoptout-didntwork' => '正常に機能しませんでした。',
	'optin-survey-answer-whyoptout-notpredictable' => '動作が不安定、予測不能でした。',
	'optin-survey-answer-whyoptout-didntlike-look' => '見た目が好きではありませんでした。',
	'optin-survey-answer-whyoptout-didntlike-layout' => '新しいタブやレイアウトが好きではありませんでした。',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => '新しいツールバーが好きではありませんでした。',
	'optin-survey-answer-whyoptout-other' => 'その他の理由:',
	'optin-survey-question-explain' => '上で選んだ答えについて詳しく説明してください。',
	'optin-survey-question-techfail' => 'ベータ版の使用中に技術的なトラブルがありましたか。',
	'optin-survey-question-techfail-ifyes' => 'もしそうならば、詳しく説明してください。',
	'optin-survey-question-usedtoolbar' => '表示・非表示を切り替えられる新しいページ編集用ツールバーを使ってみましたか？',
	'optin-survey-question-usedtoolbar-ifyes' => 'もしそうならば、どこが気に入りましたか？あるいは気に入りませんでしたか？',
	'optin-survey-question-usedtoolbar-ifno' => 'いいえの場合、それはなぜですか？将来的には利用する予定がありますか？詳しくお答えください。',
	'optin-survey-question-different' => 'どのような点を改善すれば、このベータを引き続きお使いいただけたでしょうか。',
	'optin-survey-question-feedback' => 'その他のご意見、ご要望、気になる点などをぜひお知らせください。どのようなご意見も貴重です:',
	'optin-survey-question-browser' => '利用しているブラウザはどれですか？',
	'optin-survey-answer-browser-cb' => 'Google Chrome ベータ',
	'optin-survey-answer-browser-other' => 'その他のブラウザ:',
	'optin-survey-question-os' => '利用しているOSはどれですか？',
	'optin-survey-answer-os-other' => 'その他のオペレーティングシステム:',
	'optin-survey-question-res' => '画面の解像度はいくつですか？',
	'optin-survey-question-changes' => '今後、ベータ版にどのような点の改善を望みますか？（3つまで選択してください）',
	'optin-survey-answer-changes-nav' => 'サイト全体の操作方法をより使いやすくしてほしい。',
	'optin-survey-answer-changes-edittools' => '編集ツールを増やし、機能強化してほしい。',
	'optin-survey-answer-changes-upload' => 'コンテンツやメディアアップロードを改善してほしい。',
	'optin-survey-answer-changes-richtext' => 'リッチテキストエディタのような編集ツールを開発してほしい。',
	'optin-survey-answer-changes-lookfeel' => 'サイトの「見た感じ」を更新してほしい。',
	'optin-survey-answer-changes-predictability' => '動作の安定性を改善してほしい。',
	'optin-survey-answer-changes-compat' => 'ローカルウィキとの互換性を改善してほしい。',
	'optin-survey-answer-changes-other' => 'その他:',
	'optin-improvements' => '== 何が改良されたのですか？ ==
[[File:UsabilityNavigation.png|left]]
;ナビゲーションの向上
新しいナビゲーションシステムでは、現在見ているページがどこか、現在行っている作業なにかがわかりやすくなりました。また、どのページにアクセスでき、どうやってそれらのページにアクセスすればよいかも分かりやすくなりました。画面右上に検索ボックスを置くことで、検索バーを見つけやすく、より素早く利用できるようになりました。
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
;編集ツールバーの改良
新しく改良されたアイコンにより、各ツールの機能をより明確に表現できるようになりました。編集ツールバーの各部分について表示・非表示を切り替えられるようにすることで、ツールバーのデザインを簡潔に保ちつつ、散発的に使用される機能にもすぐアクセスできるようにしました。ツールバーにヘルプを組み込むことで、一般的なウィキ・マークアップの説明を素早く参照できるようにしました。
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;検索インタフェースの改良
検索機能では、まずページ名との一致を表示することで、検索語と関連の深い記事を素早く見つけられるようにしました。検索画面の表示を改善してページの種類ごとの検索が簡単にできるようにすると同時に、「高度な検索」の画面では複雑な検索を素早く行えるようにしました。
<div style="clear:both;"></div>',
);

/** Georgian (ქართული)
 * @author გიორგიმელა
 */
$messages['ka'] = array(
	'optin' => 'იუზაბილითის ინიციატივაში მონაწილეობა',
	'optin-desc' => 'აძლევს მომხმარებლებს უფლებას გამოიყენონ Beta, იუზაბილითის ინიციატივის ბოლო ხელსაწყო',
	'optin-title-optedout' => 'Beta ტესტირება',
	'optin-title-optedin' => 'Beta-ს გათიშვა',
	'optin-title-justoptedin' => 'მოგესალმებით Beta-ში',
	'optin-title-justoptedout' => 'მადლობას გიხდით Beta-ს შემოწმებისთვის',
	'optin-needlogin' => 'თქვენ უნდა [$1 წარადგინოთ თავი] Beta-ს გამოყენებისთვის',
	'optin-intro' => 'იუზაბილითის ინიციატივის ფარგლებში განხორციელდა ვიკიპედიის გამოყენების გამარტივებასთან დაკავშირებული სამუშაო.
გსურთ Beta-ს ტესტირება?',
	'optin-success-in' => 'ახლა თქვენ შეგიძლიათ მიიღოთ მონაწილეობა Beta-ს ტესტირებაში.
თქვენ შეგიძიათ შეწყვიტოთ მონაწილეობის მიღება "Beta-ს დატოვება"-ს ღილაკზე დაჭერისას.',
	'optin-leave-cancel' => 'თუ თქვენ გსურთ Beta-ს გამოყენება, შეგიძლიათ დაბრუნდეთ  $1-ზე.',
	'optin-success-out' => 'თქვენ წარმატებით გათიშეთ Beta.
თქვენ კიდევ ერთხელ შეგიძლიათ ამ სისტემის ტესტირება ბმულის დაჭერით «Beta-ს ჩართვა» ეკრანის მარჯვენა კუთხეში.',
	'optin-accept-short' => 'წავედით!',
	'optin-accept-long' => 'Beta-ს გასინჯვა',
	'optin-deny-short' => 'არა, გმადლობთ',
	'optin-deny-long' => 'წინა გვერდზე დაბრუნება',
	'optin-submit-out' => 'Beta-ს გათიშვა',
	'optin-try' => 'Beta-ს გასინჯვა',
	'optin-leave' => 'Beta-ს გათიშვა',
	'optin-survey-yes' => 'ჰო',
	'optin-survey-no' => 'არა',
	'optin-survey-intro' => 'გმადლობთ Beta-ს შემოწმებისთვის.
ჩვენ გვაინტერესებს, რას ფიქრობთ ახალ ინტერფეისზე და მადლობელნი ვიქნებით თუ თქვენ მიღიებთ მონაწილეობას გამოკითხვაში მანამ, სანამ დააჭერთ «[[#leave|{{int:optin-submit-out}}]]».',
	'optin-survey-question-likedislike' => 'რა მოგწონთ Beta-ში? რა არა?',
	'optin-survey-question-whyoptout' => 'რატომ ტოვებთ ამ ბეტას? (გთხოვთ აირჩიოთ შესაბამისი მნიშვნელობა)',
	'optin-survey-answer-whyoptout-hard' => 'იგი ძალზედ რთულია გამოყენებისას.',
	'optin-survey-answer-whyoptout-didntwork' => 'ის არ ფუნქციონირებს მართობულად.',
	'optin-survey-answer-whyoptout-notpredictable' => 'იგი მოულოდნელად მოქმედებს.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'მე არ მომწონს როგორც მუშაობს',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'მე არ მომეწონა ახალი თაბები და კოდი.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'მე არ მომეწონა რედაქტირების პანელი.',
	'optin-survey-answer-whyoptout-other' => 'სხვა მიზეზი',
	'optin-survey-question-explain' => 'გთხოვთ დააკონკრეტოთ თქვენი პასუხი ზემოთ:',
	'optin-survey-question-techfail' => 'თქვენ დაესწარით ტექნიკურ პრობლემებს Beta-ს ტესტირებისას?',
	'optin-survey-question-techfail-ifyes' => 'თუ კი, გთხოვთ აღწეროთ ან დააკონკრეტოთ:',
	'optin-survey-question-usedtoolbar' => 'თქვენ გამოიყენეთ ახალი ჩაკეცვადი ინსტრუმენტების პანელი?',
	'optin-survey-question-usedtoolbar-ifyes' => 'თუ კი, რა მოგეწონათ და რა არა.',
	'optin-survey-question-different' => 'რას გადააკეთებდით ბეტას გამოყენების გაგრძელების შემთხვევაში?',
	'optin-survey-question-feedback' => 'გთხოვთ მოგვაწოდოთ თქვენი აზრი:',
	'optin-survey-question-browser' => 'რომელ ბროუზერს იყენებთ?',
	'optin-survey-answer-browser-other' => 'სხვა ბროუზერი:',
	'optin-survey-question-os' => 'რომელ ოპერაციულ სისტემას იყენებთ?',
	'optin-survey-answer-os-other' => 'სხვა:',
	'optin-survey-question-res' => 'თქვენი მონიტორის გაფართოება:',
	'optin-improvements' => '== რა გაუმჯობესდა? ==
[[File:UsabilityNavigation.png|left]]
; ნავიგაციის გაუმჯობესება
ახალი ნავიგაციის სისტემა გაძლევთ საშალებას უფრო მარტივად გაერკვეთ სად ხართ, სად შეგიძიათ გადახვიდეთ და როგორ. ძიების ვეით, რომელიც ეკრანის კუთხეშია განლაგებული, ძიება უფრო მარტივი და ჩქარია.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
; რედაქტირების პანელის გაუმჯობესება
ახალი და გაუმჯობესებული ხატულები უფრო ზუსტად აჩვენეენ რა ინსტრუმენტები რა ფუნქციას ატარებენ. გახსნადი ნაწილები აკლებენ  Expandable sections reduce clutter while keeping infrequently used tools just a click away.
The help section provides quick access to a reference for common uses of wiki-markup.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Improved search interface
When using search, title matches are shown first, helping you to find the most relevant articles quickly. Search profiles make searching for different kinds of content easier, while the new advanced search interface makes light-work of complex searches.
<div style="clear:both;"></div>',
);

/** Korean (한국어)
 * @author Kwj2772
 */
$messages['ko'] = array(
	'optin-survey-answer-whyoptout-other' => '다른 이유:',
	'optin-survey-answer-browser-other' => '다른 브라우저:',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'optin' => 'Op de <i lang="en">Usability Initiative</i> ier Verschöönerunge jon.',
	'optin-desc' => 'Määt et müjjelesch för de Metmaacher, <i lang="en">Beta</i>, de <i lang="en">Usability Initiative</i> ier neuste Verschöönerunge ußzeprobeere.',
	'optin-title-optedout' => '<i lang="en">Beta</i> ußprobeere',
	'optin-title-optedin' => '<i lang="en">Beta</i> verlohße',
	'optin-title-justoptedin' => 'Wellkumme bei <i lang="en">Beta</i>',
	'optin-title-justoptedout' => 'Merßi, datt De <i lang="en">Beta</i> ußprobeere deihß.',
	'optin-title-feedback' => 'Röckmäldunge',
	'optin-needlogin' => 'Do mööts alld [$1 enjelogg senn], öm <i lang="en">Beta</i> för Desch enschallde ze künne.',
	'optin-intro' => 'De <i lang="en">Usability Initiative</i> vun de Wikipedia hät kräftesch jet draan jeärbeit, öm de Wikipedia besser zom Bedeene ze maache för de Metmaacher.
Wells De <i lang="en">Beta</i> ußprobeere?',
	'optin-feedback-intro' => 'Merci för et Ußprobeere.
Mer wolle jähn weße, wat De vun uns Beta häls,
un mer sin Der dangkbaa, wann De op di Froore heh
e paa Antwoote jävve dääts. Et koß nix, un verflich zoh nix.',
	'optin-feedback-back' => 'Wann De nix ußfölle wells, kanns De och noh $1 zerök jonn.',
	'optin-success-in' => 'Do bes jäz de <i lang="en">Beta</i> am ußprobeere.
Do kanns doh emmer wider eruß kumme, endämm dat De op „{{int:optin-leave}}“ klecks.',
	'optin-leave-cancel' => 'Wann De <i lang="en">Beta</i> wigger bruche wells, kanns De noh  $1 retuur jonn.',
	'optin-success-out' => 'Do häß <i lang="en">Beta</i> verlohße.
Do kanns emmer wider zerök kumme, endämm dat De op „{{int:optin-try}}“ klecks.',
	'optin-success-feedback' => 'Häzlijje Dangk för Ding Rökmäldung.',
	'optin-accept-short' => 'Lom_mer dat donn!',
	'optin-accept-long' => '<i lang="en">Beta</i> ußprobeere',
	'optin-accept-long-anon' => 'Enlogge un de Beta ußprobeere',
	'optin-deny-short' => 'Nää, dat wulle mer nit',
	'optin-deny-long' => 'retuur op de vörijje Sigg',
	'optin-submit-out' => '<i lang="en">Beta</i> verlohße',
	'optin-submit-feedback' => 'Rökmäldunge jävve',
	'optin-try' => '<i lang="en">Beta</i> ußprobeere',
	'optin-leave' => '<i lang="en">Beta</i> verlohße',
	'optin-feedback' => 'Rökmäldunge jävve',
	'optin-survey-yes' => 'Joh',
	'optin-survey-no' => 'Nää',
	'optin-survey-intro' => 'Mer bedangke uns doför, dat De <i lang="en">Beta</i> ußprobeere deihß.

Mer dääte jähn weße wat De övver uns neu Ovverflääsch meins. Dröm dääte mer uns freue, wann De unge op e paa kleine Froore antwoote dääts, ih dat De op „[[#leave|{{int:optin-submit-out}}]]“ klecks.',
	'optin-survey-question-likedislike' => 'Wat hät Der aam <i lang="en">Beta</i> jefalle?
Wat hät Der aam <i lang="en">Beta</i> nit jefalle?',
	'optin-survey-question-whyoptout' => 'Woröm deihß De <i lang="en">Beta</i> nit (mieh) bruche? (Beß esu joot, un jif alles aan, wat för Desch shtemme deiht)',
	'optin-survey-answer-whyoptout-hard' => 'Et woh mer ze schwiiresch met ömzejonn.',
	'optin-survey-answer-whyoptout-didntwork' => 'Et hät nit öhndlesch jeflup, wi et sullt.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Wat eruß kütt, kam_mer förheer nit weßße.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Wi dat ußsooch, hät mer nit jevalle.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Mer jefeele di neue Knöppe nit, un och nit wi dä Beldscherrem opjedeijlt wohr.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Mer jefeel de neue Reih met Wärkzüsch nit.',
	'optin-survey-answer-whyoptout-other' => 'Ene andere Jrond:',
	'optin-survey-question-explain' => 'Bes esu joot, un donn Ding Antwoot vun heh drövver e beßje ußföhlescher expleziere:',
	'optin-survey-question-techfail' => 'Haß De beim Ußprobeere Fähler en de Täschnek jehat?',
	'optin-survey-question-techfail-ifyes' => 'Wann Joh, donn dat jenou opschriive udder expleziere:',
	'optin-survey-question-usedtoolbar' => 'Deihß De de neue, ußklappbaa Wärkzüschreih beim Sigge Änndere bruche?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Wann Joh, wat hät Der doh draan jefalle? Wat nit?',
	'optin-survey-question-usedtoolbar-ifno' => 'Wann nää, woröm dat? Weeß De_t en de Zohkunnf donn? Beß esu joot, un donn dat jet jenouer expleziere.',
	'optin-survey-question-different' => 'Wat künnte mer anders maache, domet De uns <i lang="en">Beta</i> wigger bruche wells?',
	'optin-survey-question-feedback' => 'Beß esu joot un loß uns Ding Röckmeldunge han:',
	'optin-survey-question-browser' => 'Wat för ene Brauser bes De am bruche?',
	'optin-survey-answer-browser-ie5' => 'De Version 5 vum <i lang="en">Internet Explorer</i>',
	'optin-survey-answer-browser-ie6' => 'De Version 6 vum <i lang="en">Internet Explorer</i>',
	'optin-survey-answer-browser-ie7' => 'De Version 7 vum <i lang="en">Internet Explorer</i>',
	'optin-survey-answer-browser-ie8' => 'De Version 8 vum <i lang="en">Internet Explorer</i>',
	'optin-survey-answer-browser-ff1' => 'De Version 1 vum <i lang="en">Firefox</i>',
	'optin-survey-answer-browser-ff2' => 'De Version 2 vum <i lang="en">Firefox</i>',
	'optin-survey-answer-browser-ff3' => 'De Version 3 vum <i lang="en">Firefox</i>',
	'optin-survey-answer-browser-cb' => 'De Betta-Version vum <i lang="en">Google Chrome</i>',
	'optin-survey-answer-browser-c1' => 'De Version 1 vum <i lang="en">Google Chrome</i>',
	'optin-survey-answer-browser-c2' => 'De Version 2 vum <i lang="en">Google Chrome</i>',
	'optin-survey-answer-browser-s3' => 'De Version 3 vum <i lang="en">Safari</i>',
	'optin-survey-answer-browser-s4' => 'De Version 4 vum <i lang="en">Safari</i>',
	'optin-survey-answer-browser-o9' => 'De Version 9 vum <i lang="en">Opera</i>',
	'optin-survey-answer-browser-o9.5' => 'De Version 9.5 vum <i lang="en">Opera</i>',
	'optin-survey-answer-browser-o10' => 'De Version 10 vum <i lang="en">Opera</i>',
	'optin-survey-answer-browser-other' => 'ene andere Brauser:',
	'optin-survey-question-os' => 'Wat förr en Zoot Bedriifß_Süßteem beß De aam bruche?',
	'optin-survey-answer-os-windows' => '<i lang="en">Windows</i>',
	'optin-survey-answer-os-macos' => '<i lang="en">Mac OS</i>',
	'optin-survey-answer-os-linux' => '<i lang="en">Linux</i>',
	'optin-survey-answer-os-other' => 'En ander Zoot Bedriifßsüßtehm:',
	'optin-survey-question-res' => 'Wie es Dingem Beldscherrem sing Oplüüsung?',
	'optin-survey-question-changes' => 'Wat för Änderunge dääts De en De Zohkunnf jähn en de Bata hann? Söhk Der drei uß, udder winnijer.',
	'optin-survey-answer-changes-nav' => 'Maat et eröm Navvijeere en heh dä Web_ßait eijfaacher.',
	'optin-survey-answer-changes-edittools' => 'Maat mieh Wärkzüsch för et Beärbeide, un maat et beßer.',
	'optin-survey-answer-changes-upload' => 'Maat et Huhlaade, för Meedije un Enhallde, beßer.',
	'optin-survey-answer-changes-richtext' => 'Doht Wärkzüsh en dä Aat vun <i lang="en">„rich-text“ editors</i> för et Täxte-Ändere entweckele.',
	'optin-survey-answer-changes-lookfeel' => 'Doht Ußsinn un de Jeschtalldung vun dä Web-ßait neuer maache.',
	'optin-survey-answer-changes-predictability' => 'Maat de Leistung mieh afseebaar.',
	'optin-survey-answer-changes-compat' => 'Maat de Verdrääschleschkeit met heh däm Wiki bäßer.',
	'optin-survey-answer-changes-other' => 'Söns jet:',
	'optin-improvements' => '== Wat es bäßer jemaat woode? ==
[[File:UsabilityNavigation.png|left]]
; verbäßert Eöm_jonn
Kloh Aanzeije vöm Zohshtand vum „Lässe“ udder „Ändere“, un of De op enem Atikel singe Sigg udder op en Klaafsigg bes.
Dä Kaßte zom Söhke es vun däm lengke Käßte noh bove räähß verschovve för dä bäßer ze fenge un flöcker ze Söhke.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
; bäßer Wärkzüsch zom Ändere
En verfüüßerbaa Wärkzüsch_Leß vershtish de sällde jebruch Wärkzüsch ier Mini-Beldsche.
Metmaacher künne de Leß verjrüüßere, öm op de mieh spezjälle Sache zohzejriife.
Bezösh op Hölp — Spekzäddel — sin doh ennjebout.
Wärkzüsh Minnibeldscher sin neujemoohlt woode un bruche <i lang="en">Tango</i> un <i lang="en">Gnome</i> Belldscher.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
; bäßer Söhke
De Sigg, di beij em Söhke eruß kütt es öff der Wääsch noh enem Atikel,
de Tittelle sin esu verdeijlt, dat se joot ze sin sin, un de winnisch jebruchte Ennfommazjuhne sin kleijner.
<div style="clear:both;"></div>',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Purodha
 * @author Robby
 */
$messages['lb'] = array(
	'optin' => 'Benotzerfrëndlechkeets-Initiative matmaachen',
	'optin-desc' => 'Erlaabt et de Benotzer fir Beta auszeprobéieren, déi lescht Verbesserunge vun der Benotzerfrëndlechkeets-Initiative.',
	'optin-title-optedout' => 'Probéiert Beta aus',
	'optin-title-optedin' => 'Beta verlossen',
	'optin-title-justoptedin' => 'Wëllkomm bäi Beta',
	'optin-title-justoptedout' => 'Merci datt Dir Beta ausprobéiert',
	'optin-title-feedback' => 'Gitt eis Är Meenung',
	'optin-needlogin' => 'Dir musst Iech [$1 alogge] fir Beta auszeprobéieren.',
	'optin-intro' => "D'Benotzerfrëndlechkeets-Initiative vu Wikipedia huet vill geschaft fir d'Benotzerfrëndlechkeet vu Wikipedia ze verbesseren.
Wëllt Dir Beta ausprobéieren?",
	'optin-feedback-intro' => 'Merci datt Dir Beta ausprobéiert hutt.
Mir wëlle wëssen wat Dir vun dem neien Interface halt, dofir wiere mir frou wann Dir bei der fräiwëlleger Ëmfro hei ënnedrënner matmaache géift.',
	'optin-feedback-back' => "Wann Dir d'Ëmfro net ausfëlle wëllt, da kënnt dir op $1 zréckgoen.",
	'optin-success-in' => 'Dir probéiert elo Beta aus.
Dir kënnt dës zu jidder Zäit nees ausschalten andeem datt Dir op de Link  "{{int:optin-leave}}"  uewen um Ecran klickt.',
	'optin-leave-cancel' => 'Wann Dir Beta weiderbenotze wëllt, kënnt Dir op $1 zréckgoen',
	'optin-success-out' => 'Dir hutt Beta verlooss.
Dir kënnt dës zu jidder Zäit nees aschalten andeem datt Dir op de Link "{{int:optin-try}}" ueweriets um Schierm klickt.',
	'optin-success-feedback' => 'Merci fir Är Meenung!',
	'optin-accept-short' => 'Kommt mir maachen et!',
	'optin-accept-long' => 'Probéiert Beta aus',
	'optin-accept-long-anon' => 'Aloggen a Beta ausprobéieren',
	'optin-deny-short' => 'Kee Merci',
	'optin-deny-long' => 'Zréck op déi Säit virdrun',
	'optin-submit-out' => 'Beta verloossen',
	'optin-submit-feedback' => 'Gitt eis Är Meenung',
	'optin-try' => 'Probéiert Beta aus',
	'optin-leave' => 'Beta verloosen',
	'optin-feedback' => 'Gitt eis Är Meenung',
	'optin-survey-yes' => 'Jo',
	'optin-survey-no' => 'Neen',
	'optin-survey-intro' => 'Merci datt Dir Beta ausprobéiert.
Mir wiere frou fir ze wëssen wat Dir vun deem neien Interface denkt, dofir wiere mir frou wann Dir déi fräiwëlleg Ëmfro hei ënnendrënner géift ausfëllen ier Dir op "[[#leave|{{int:optin-submit-out}}]]" klickt.',
	'optin-survey-question-likedislike' => 'Wat hutt dir bäi Beta gutt finnt? Wat huet Iech un Beta net gefall?',
	'optin-survey-question-whyoptout' => 'Firwat Verloost Dir dës Beta? (wielt w.e.g. alles aus wat zoutrëfft)',
	'optin-survey-answer-whyoptout-hard' => 'Et war ze komplizéiert fir ze benotzen.',
	'optin-survey-answer-whyoptout-didntwork' => 'Et huet net richteg fonctionnéiert.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Et huet net esou fonctionnéiert wéi virgesinn.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Et huet mir net gefall wéi et ausgesäit.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Ech hat déi nei Ongleten an den neie Layout net gär.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Ech hunn déi nei Toolbar net gär.',
	'optin-survey-answer-whyoptout-other' => 'Anere Grond:',
	'optin-survey-question-explain' => 'Erklärt oder preziséiert Är Äntwert w.e.g.:',
	'optin-survey-question-techfail' => 'Hat Dir technesch Schwieregkeete beim Benotze vun Beta?',
	'optin-survey-question-techfail-ifyes' => 'Wa jo, da begrënnt oder erklärt et:',
	'optin-survey-question-usedtoolbar' => "Hutt Dir déi nei erweiderbar Toolbar benotzt fir eng Säit z'änneren?",
	'optin-survey-question-usedtoolbar-ifyes' => 'Wa jo, wat hutt Dir doru gutt fonnt? Wat huet Iech net esougutt gefall?',
	'optin-survey-question-different' => 'Wat kënne mir anescht maache fir datt Dir eis Beta-Versioun weider benotzt?',
	'optin-survey-question-feedback' => 'Sot eis w.e.g. Är Meenung:',
	'optin-survey-question-browser' => 'Watfir e Browser benotzt Dir?',
	'optin-survey-answer-browser-other' => 'Anere Browser:',
	'optin-survey-question-os' => 'Wafir e Betriibssystem benotzt Dir?',
	'optin-survey-answer-os-other' => 'Anere Betriibssystem:',
	'optin-survey-question-res' => "Wéi ass d'Opléisung vun ärem Ecran?",
	'optin-survey-question-changes' => 'Watfir Ännerunge géif Dir am léifsten an der Beta an Zukunft gesinn (sicht der bis zu dräi eraus)?',
	'optin-survey-answer-changes-nav' => 'Déi allgemeng Navigatioun vum Site méi einfach maachen.',
	'optin-survey-answer-changes-lookfeel' => 'Ausgesi vum Site aktualiséieren.',
	'optin-survey-answer-changes-predictability' => 'Zouverléissegkeet vun der Previsioun vun der Performance verbesseren.',
	'optin-survey-answer-changes-compat' => 'Compatibilitéit mat der lokale Wiki verbesseren.',
	'optin-survey-answer-changes-other' => 'Anerer:',
	'optin-improvements' => '== Wat gouf verbessert? ==
[[File:UsabilityNavigation.png|left]]
;Erweidert Navigatioun
Dat neit Navigatiounssystem mécht et net nëmme méi einfacher fir ze gesinn, wou een ass a wat ee mécht, mee e weist och wuer ee goe kann. Mat der Sichbox uewe riets am Eck gëtt d\'Fanne méi einfach a méi séier.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
;Verbesserunge vun der Ännerungs-Toolbar
Nei a verbessert Symboler maachen et méi kloer, watfireng Aktioun hanner all Tool stécht. Abschnitter déi opgeklappt kënne ginn reduzéieren den Duercherneen, während sele benotzten Toolen nëmmen ee Klick ewech sinn.
Den Abschnitt mat der Hëllef erlaabt e séieren Zougrëff mat déi am dackste benotzte Wikisyntax.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Verbessert Sichuewerfläch
Wann Dir d\'Sich benotzt, ginn all Titeltreffer fir d\'éischt gewisen. Dat hëlleft, déi relevantesten Artikel séier ze fannen. Sichprofiler maachen d\'Siche fir verschidden Zorte vun Inhalt méi einfach, während déi nei erweidert Sichuewerfläche komplext Siche méi einfach mécht.
<div style="clear:both;"></div>',
);

/** Lithuanian (Lietuvių)
 * @author Matasg
 */
$messages['lt'] = array(
	'optin' => 'Įjungti naudojimo iniciatyvą',
	'optin-desc' => 'Leisti vartotojams pabandyti Beta, naudojimo iniciatyvos didinimą.',
	'optin-title-optedout' => 'Pabandykite Beta',
	'optin-title-optedin' => 'Palikite Beta',
	'optin-title-justoptedin' => 'Sveiki atvykę į Beta',
	'optin-title-justoptedout' => 'Dėkojame, kad bandote Beta',
	'optin-needlogin' => 'Jums reikia [$1 prisijungti], norint bandyti Beta',
	'optin-intro' => 'Vikipedija Naudojimo iniciatyva dirbo siekiant pagerinti Vikipedijos naudojimą.
Norite pabandyti Açai beta versiją?',
	'optin-success-in' => 'Dabar bandote Beta. 
Galite grįžti bet kuriuo metu, paspaudę "{{int:optin-leave}}" viršutiniame dešiniajame ekrano kampe.',
	'optin-leave-cancel' => 'Jei norite toliau naudoti Beta, galite grįžti į $1',
	'optin-success-out' => 'Jūs sėkmingai paliko Beta.
Galite išmėginti Beta vėl, bet kuriuo metu paspaudę "{{int:optin-try}}" viršutiniame dešiniajame ekrano kampe.',
	'optin-accept-short' => 'Padarykime tai!',
	'optin-accept-long' => 'Išbandykite Beta',
	'optin-deny-short' => 'Ne, ačiū',
	'optin-deny-long' => 'Grįžti į ankstesnį puslapį',
	'optin-submit-out' => 'Palikti Beta',
	'optin-try' => 'Pabandyti Beta',
	'optin-leave' => 'Palikti Beta',
	'optin-survey-yes' => 'Taip',
	'optin-survey-no' => 'Ne',
	'optin-survey-intro' => 'Dėkojame, kad bandote Beta.
Mes norėtume sužinoti, ką manote apie mūsų naująją vartotojo sąsają, todėl būtume labai dėkingi, jei užpildyti neprivalomą tyrimo apklausą žemiau, prieš paspausdami "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Kas jums patiko Betoje? Kas jums nepatiko Betoje?',
	'optin-survey-question-whyoptout' => 'Kodėl paliekate Beta? (Pasirinkite visus, kurie tinka).',
	'optin-survey-answer-whyoptout-hard' => 'Buvo pernelyg sunku redaguoti puslapį.',
	'optin-survey-answer-whyoptout-didntwork' => 'Ji nefunkcionavo tinkamai.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Neatitiko to, ko tikėjausi.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Man nepatiko, kaip atrodė.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Man nepatiko nauji skirtukai ir išdėstymas.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Man nepatiko nauja įrankių juosta.',
	'optin-survey-answer-whyoptout-other' => 'Kita priežastis:',
	'optin-survey-question-explain' => 'Prašome paaiškinti, ar detalizuoti savo atsakymą:',
	'optin-survey-question-techfail' => 'Ar patyrėte techninių gedimų naudojant Beta?',
	'optin-survey-question-techfail-ifyes' => 'Jei taip, prašome paaiškinti:',
	'optin-survey-question-usedtoolbar' => 'Ar naudojote naują įrankių juostą puslapių redagavimui?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Jei taip, ką norite apie ją? Kas jums nepatinka joje?',
	'optin-survey-question-different' => 'Ką galėtume daryti kitaip, kad jūs naudotumėte Beta?',
	'optin-survey-question-feedback' => 'Leiskite mums sužinoti jūsų nuomonę:',
	'optin-survey-question-browser' => 'Kurią naršyklę naudojate?',
	'optin-survey-answer-browser-other' => 'Kita naršyklė:',
	'optin-survey-question-os' => 'Kurią operacinę sistemą naudojate?',
	'optin-survey-answer-os-other' => 'Kita operacinė sistema:',
	'optin-survey-question-res' => 'Kokia Jūsų ekrano rezoliucija?',
);

/** Nedersaksisch (Nedersaksisch)
 * @author Servien
 */
$messages['nds-nl'] = array(
	'optin-survey-answer-whyoptout-other' => 'Aandere rejen:',
	'optin-survey-answer-os-other' => 'Aanders:',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'optin' => 'Opt-in bruikbaarheidsinitiatief',
	'optin-desc' => 'Maakt het mogelijk voor gebruikers om Beta, de verbeteringen van het Bruikbaarheidsinitiatief, in te schakelen',
	'optin-title-optedout' => 'Beta uitproberen',
	'optin-title-optedin' => 'Beta uitschakelen',
	'optin-title-justoptedin' => 'Welkom bij Beta',
	'optin-title-justoptedout' => 'Bedankt voor het uitproberen van Beta',
	'optin-title-feedback' => 'Terugkoppeling geven',
	'optin-needlogin' => 'U moet [$1 aanmelden] voordat u Beta kunt inschakelen.',
	'optin-intro' => 'Het Wikipedia Bruikbaarheidsinitiatief heeft een nieuwe vormgeving en nieuwe bewerkingshulpmiddelen ontwikkeld om de bruikbaarheid van Wikipedia te verbeteren.
Wilt u Beta uitproberen?',
	'optin-feedback-intro' => 'Bedankt voor het uitproberen van Beta.
We willen graag weten hoe u denkt over de nieuwe interface, dus we zouden het op prijs stellen als u de enquête hieronder invult.',
	'optin-feedback-back' => 'Als u de enquête niet wilt invullen kunt u terugkeren naar $1.',
	'optin-success-in' => 'U maakt nu gebruik van Beta.
U kunt deze instellingen op elk gewenst moment weer verwijderen door op de knop "{{int:optin-submit-out}}" te klikken.',
	'optin-leave-cancel' => 'Als u Beta wilt blijven gebruiken, kunt u terugkeren naar $1',
	'optin-success-out' => 'U hebt Beta uitgeschakeld.
U kunt deze functionaliteit op elk gewenst moment weer inschakelen door op de knop {{int:optin-submit-in}} te klikken.',
	'optin-success-feedback' => 'Dank u voor uw terugkoppeling!',
	'optin-accept-short' => 'Inschakelen',
	'optin-accept-long' => 'Beta inschakelen',
	'optin-accept-long-anon' => 'Aanmelden en Beta uitproberen',
	'optin-deny-short' => 'Nee, bedankt',
	'optin-deny-long' => 'Terug naar de vorige pagina',
	'optin-submit-out' => 'Beta uitschakelen',
	'optin-submit-feedback' => 'Terugkoppeling geven',
	'optin-try' => 'Beta inschakelen',
	'optin-leave' => 'Beta uitschakelen',
	'optin-feedback' => 'Terugkoppeling geven',
	'optin-survey-yes' => 'Ja',
	'optin-survey-no' => 'Nee',
	'optin-survey-intro' => 'Dank u wel voor het uitproberen van Beta.
We willen graag weten wat u vindt van deze nieuwe interface, dus wilt u alstublieft de korte vragenlijst hieronder invullen voordat u op "[[#leave|{{int:optin-submit-out}}]]" klikt?',
	'optin-survey-question-likedislike' => 'Wat vond u goed aan Beta?
Wat vond u niet goed aan Beta?',
	'optin-survey-question-whyoptout' => 'Waarom wilt u deze interface weer uitschakelen?
Selecteer alstublieft alle redenen die van toepassing zijn.',
	'optin-survey-answer-whyoptout-hard' => 'Het gebruik was te moeilijk.',
	'optin-survey-answer-whyoptout-didntwork' => 'De wijzigingen functioneerden niet correct.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Het reageerde niet voorspelbaar.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Het zag er niet zo uit als ik wilde.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Ik vond de nieuwe tabbladen en het uiterlijk niet prettig.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Ik vond de nieuwe werkbalk niet prettig werken.',
	'optin-survey-answer-whyoptout-other' => 'Andere reden:',
	'optin-survey-question-explain' => 'Geef alstublieft een toelichting op uw antwoord hierboven:',
	'optin-survey-question-techfail' => 'Hebt u technische fouten ondervonden bij het gebruik van Beta?',
	'optin-survey-question-techfail-ifyes' => 'Zo ja, geef hierop dan alstublieft een toelichting:',
	'optin-survey-question-usedtoolbar' => 'Hebt u de nieuwe uitklapbare werkbalk gebruikt bij het bewerken van een pagina?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Zo ja, wat vond u goed er aan, en wat vond u er niet goed aan?',
	'optin-survey-question-usedtoolbar-ifno' => 'Zo nee, waarom niet?
Wellicht in de toekomst?
Geef alstublieft een toelichting.',
	'optin-survey-question-different' => 'Wat had er anders gedaan moeten worden zodat u Beta was blijven gebruiken?',
	'optin-survey-question-feedback' => 'Laat ons alstublieft weten wat u vindt:',
	'optin-survey-question-browser' => 'Welke browser gebruikt u?',
	'optin-survey-answer-browser-other' => 'Andere browser:',
	'optin-survey-question-os' => 'Welk besturingssysteem gebruikt u?',
	'optin-survey-answer-os-other' => 'Ander besturingssysteem:',
	'optin-survey-question-res' => 'Wat is uw beeldschermresolutie?',
	'optin-survey-question-changes' => 'Welke wijzigingen wilt u in Beta in de toekomst zien (selecteer maximaal drie mogelijkheden)?',
	'optin-survey-answer-changes-nav' => 'Maak de navigatie van de site eenvoudiger.',
	'optin-survey-answer-changes-edittools' => 'Meer en betere bewerkingshulpmiddelen.',
	'optin-survey-answer-changes-upload' => 'Verbeter het toevoegen van inhoud en mediauploads.',
	'optin-survey-answer-changes-richtext' => 'Ontwikkel bewerkingshulpmiddelen vergelijkbaar met uitgebreide tekstverwerkers.',
	'optin-survey-answer-changes-lookfeel' => 'Werkt het uiterlijk en de werking van de bij naar de laatste inzichten.',
	'optin-survey-answer-changes-predictability' => 'Verbeter de voorspelbaarheid van de prestaties.',
	'optin-survey-answer-changes-compat' => 'Verbeter de compatibiliteit met de lokale wiki.',
	'optin-survey-answer-changes-other' => 'Overig:',
	'optin-improvements' => '== Wat is er verbeterd?==
[[File:UsabilityNavigation.png|left]]
;Verbeterde navigatie
Het nieuwe navigatiesysteem maakt het makkelijker om te zien waar u bent en op wat u doet, als ook waar u heen kunt gaan en hoe er te komen.
Met het zoekvak in de rechterbovenhoek is de zoekfunctie makkelijker te vinden en sneller te gebruiken.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
;Verbeterde bewerkingswerkbalk
Nieuwe en verbeterde pictogrammen maken het duidelijker wat ieder hulpmiddel doet.
Uitklapbare onderdelen verminderen een rommelig uiterlijk, terwijl minder vaak gebruikte functies wel bereikbaar blijven.
De hulpfunctie biedt snelle toegang tot uitleg over veelgebruikte functies van de wikiopmaaktaal.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Verbeterde zoekinterface
Bij het gebruiken van de zoekfunctie worden gevonden pagina\'s eerst weergegeven, zodat u relevante pagina\'s eerst ziet.
Via zoekprofielen is het vinden van verschillende soorten inhoud eenvoudiger, terwijl via de gevorderde zoekinterface complexe zoekdrachten eenvoudig te maken zijn.
<div style="clear:both;"></div>',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Gunnernett
 * @author Harald Khan
 */
$messages['nn'] = array(
	'optin-needlogin' => 'Du må [$1 logga inn] for å ta i bruk "Usability Initiative" sine utvidingar.',
	'optin-survey-question-whyoptout' => 'Kvifor vel du å ikkje nytta dette oppsettet?',
	'optin-survey-answer-whyoptout-hard' => 'Det var for vanskeleg å redigera ei side.',
	'optin-survey-answer-whyoptout-didntwork' => 'Det fungerte ikkje på rett vis.',
	'optin-survey-answer-whyoptout-other' => 'Anna årsak:',
	'optin-survey-question-feedback' => 'Ver venleg og send ei tilbakemelding:',
	'optin-survey-question-browser' => 'Kva for ein nettlesar nyttar du?',
	'optin-survey-answer-browser-other' => 'Annan nettlesar:',
	'optin-survey-question-os' => 'Kva for operativsystem nyttar du?',
	'optin-survey-answer-os-other' => 'Anna:',
	'optin-survey-question-res' => 'Kva er oppløysinga på skjermen din?',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Nghtwlkr
 */
$messages['no'] = array(
	'optin-survey-answer-whyoptout-hard' => 'Det var for vanskelig å redigere en side.',
	'optin-survey-answer-whyoptout-didntwork' => 'Den virket ikke ordentlig.',
	'optin-survey-answer-whyoptout-other' => 'Annen årsak:',
	'optin-survey-question-feedback' => 'Vennligst gi oss dine tilbakemeldinger:',
	'optin-survey-question-browser' => 'Hvilken nettleser bruker du?',
	'optin-survey-answer-browser-other' => 'Annen nettleser:',
	'optin-survey-question-os' => 'Hvilket operativsystem bruker du?',
	'optin-survey-answer-os-other' => 'Annet:',
	'optin-survey-question-res' => 'Hva er skjermoppløsningen din?',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'optin' => "Participar a l'iniciativa d'utilizabilitat",
	'optin-desc' => "Permet als utilizaires d'ensajar la Beta, los melhoraments de l'iniciativa d'utilizabilitat.",
	'optin-title-optedout' => 'Ensajar la Beta',
	'optin-title-optedin' => 'Quitar la Beta',
	'optin-title-justoptedin' => 'Benvenguda dins la Beta',
	'optin-title-justoptedout' => 'Mercés per vòstra participacion a la Beta',
	'optin-title-feedback' => 'Balhatz vòstre vejaire',
	'optin-needlogin' => 'Vos cal vos [$1 connectar] per ensajar la Beta.',
	'optin-intro' => "L'iniciativa d'utilizabilitat de Wikipèdia a trabalhat dur per melhorar l'utilizabilitat de Wikipèdia.
Volètz ensajar la Beta ?",
	'optin-feedback-intro' => "Mercés d'ensajar la beta.
Nos agradariá saber çò que pensatz de nòstra interfàcia novèla, nos agradariá qu'emplenèssetz lo questionari çaijós.",
	'optin-feedback-back' => 'Se volètz pas emplenar lo questionari, podètz tornar a $1.',
	'optin-success-in' => "Sètz a testar Beta.
Podètz interrompre la sesilha a tot moment en clicant sul ligam « {{int:optin-leave}} » en naut a drecha de l'ecran.",
	'optin-leave-cancel' => 'Se volètz contunhar a utilizar la Beta, podètz tornar a $1.',
	'optin-success-out' => "Avètz quitat Beta.
Podètz participar tornamai a tot moment en clicant sul ligam « {{int:optin-try}} » en naut a drecha de l'ecran.",
	'optin-success-feedback' => 'Mercés per vòstre vejaire !',
	'optin-accept-short' => 'Anem !',
	'optin-accept-long' => 'Ensajar la Beta',
	'optin-accept-long-anon' => 'Connectatz-vos e ensajatz la beta',
	'optin-deny-short' => 'Non mercés',
	'optin-deny-long' => 'Tornar a la pagina precedenta',
	'optin-submit-out' => 'Quitar Beta',
	'optin-submit-feedback' => 'Balhatz vòstre vejaire',
	'optin-try' => 'Ensajar la Beta',
	'optin-leave' => 'Quitar la Beta',
	'optin-feedback' => 'Balhatz vòstre vejaire',
	'optin-survey-yes' => 'Òc',
	'optin-survey-no' => 'Non',
	'optin-survey-intro' => "Mercés per ensajar la Beta.

Nos agradariá de saber çò que pensatz de nòstra interfàcia novèla, nos agradariá s'emplenèssetz l'escandalhatge opcional abans de clicar sus « [[#leave|{{int:optin-submit-out}}]] ».",
	'optin-survey-question-likedislike' => 'Qué vos a agradat dins la Beta ? Qué vos a pas agradat dins la Beta ?',
	'optin-survey-question-whyoptout' => 'Perqué quitatz la beta ? (seleccionatz tot çò que conven)',
	'optin-survey-answer-whyoptout-hard' => "Èra tròp complicat d'utilizar.",
	'optin-survey-answer-whyoptout-didntwork' => 'Aquò fonciona pas corrèctament.',
	'optin-survey-answer-whyoptout-notpredictable' => "Aquò s'es pas passat coma previst.",
	'optin-survey-answer-whyoptout-didntlike-look' => "Son aspècte m'a pas agradat.",
	'optin-survey-answer-whyoptout-didntlike-layout' => "Los onglets novèls e la disposicion novèla m'an pas agradat.",
	'optin-survey-answer-whyoptout-didntlike-toolbar' => "La barra d'espleches novèla m'a pas agradat.",
	'optin-survey-answer-whyoptout-other' => 'Autra rason :',
	'optin-survey-question-explain' => 'Explicatz o elaboratz vòstra responsa çaisús :',
	'optin-survey-question-techfail' => 'Avètz rencontrat de problèmas tecnics en utilizant la Beta ?',
	'optin-survey-question-techfail-ifyes' => 'Se òc, explicatz o elaboratz :',
	'optin-survey-question-usedtoolbar' => "Avètz utilizat la barra d'espleches desenrotlabla novèla per modificar una pagina ?",
	'optin-survey-question-usedtoolbar-ifyes' => 'Se òc, qué vos a agradat e qué vos a pas agradat a son prepaus ?',
	'optin-survey-question-usedtoolbar-ifno' => "Se non, perqué ? L'utilizaretz dins lo futur ? Explicatz-vos.",
	'optin-survey-question-different' => "Qué auriá degut èsser diferent per que continhuèssetz d'utilizar nòstra beta ?",
	'optin-survey-question-feedback' => 'Balhatz-nos vòstra reaccion :',
	'optin-survey-question-browser' => 'Quin navigador utilizatz ?',
	'optin-survey-answer-browser-other' => 'Autre navigador :',
	'optin-survey-question-os' => 'Quin sistèma operatiu utilizatz ?',
	'optin-survey-answer-os-other' => 'Autre sistèma operatiu :',
	'optin-survey-question-res' => 'Quina es la resolucion de vòstre ecran ?',
	'optin-survey-question-changes' => 'Qué vos agradariá veire dins la Beta dins lo futur (al maximum 3) ?',
	'optin-survey-answer-changes-nav' => "Fa que la navigacion generala del site es mai simpla d'utilizar.",
	'optin-survey-answer-changes-edittools' => "Melhora e augmenta los espleches d'edicion.",
	'optin-survey-answer-changes-upload' => "Melhora l'impòrt de contengut e de mèdia.",
	'optin-survey-answer-changes-richtext' => "Desvolopa d'espleches de modificacions similars als editors en tèxte enriquit.",
	'optin-survey-answer-changes-lookfeel' => "Cambia l'aparéncia del site.",
	'optin-survey-answer-changes-predictability' => 'Melhora la previzibilitat de las performàncias.',
	'optin-survey-answer-changes-compat' => 'Melhora la compatibilitat amb los wikis locals.',
	'optin-survey-answer-changes-other' => 'Autre :',
	'optin-improvements' => "== De qué es es estat melhorat ? ==
[[File:UsabilityNavigation.png|left]]
;Navigacion enriquida
Lo sistèma de navigacion novèl vos permet de veire mau aisidament ont sètz e çò que fasètz, e mai ont podètz anar e cossí vos i rendre. Amb la boita de recèrca situada dins lo canton superior drech, la foncionalitat de recèrca es de melhor trobar e de melhor utilizar.
<div style=\"clear:both;\"></div>

[[File:UsabilityToolbar.png|left]]
;Melhoraments de la barra d'espleches
D'icòna tota novèlas clarifican l'accion efectuada per cada esplech. De seccions extensibles redusisson lo desòrdre tot en gardant l'accès als espleches utilizats mai raremant en un sol clic.
La seccion d'ajuda provesís un accès rapid a la documentacion sus l'utilizacion correnta de las balisas wiki.
<div style=\"clear:both;\"></div>

[[File:UsabilitySearch.png|left]]
;Interfàcia de recèrca melhorada
Quora efectuatz una recèrca, las correspondéncias dins los títols son afichadas en primièr, per vos ajudar a trobar rapidament los articles mai pertinents. Los perfils de recèrca balhan la possibilitat de recercar diferents tipes de contenguts mai aisidament, e la novèla interfàcia avançada de recèrca permet d'efectuar aisidament de recèrcas complèxas.
<div style=\"clear:both;\"></div>",
);

/** Deitsch (Deitsch)
 * @author Xqt
 */
$messages['pdc'] = array(
	'optin-title-justoptedin' => 'Wilkum zu Beta',
	'optin-deny-long' => 'Zerick zum letscht Blatt',
	'optin-survey-yes' => 'Ya',
	'optin-survey-no' => 'Nee',
);

/** Polish (Polski)
 * @author Leinad
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'optin' => 'Włączenie Inicjatywy Użyteczności',
	'optin-desc' => 'Pozwala użytkownikom włączyć ulepszenia zrealizowane w ramach Inicjatywy Użyteczności',
	'optin-needlogin' => 'Musisz [$1 zalogować się] by móc włączyć ulepszenia zrealizowane w ramach Inicjatywy Użyteczności.',
	'optin-intro' => 'Zamierzając poprawić użyteczność Wikipedii utworzono nowy wygląd strony oraz nowy pasek z narzędziami edycyjnymi. Te ulepszenia jeszcze nie są dostępne dla wszystkich użytkowników, ale Ty możesz je włączyć poprzez kliknięcie poniżej „{{int:optin-submit-in}}”.',
	'optin-success-in' => 'Udało Ci się włączyć ulepszenia zrealizowane w ramach Inicjatywy Użyteczności.
W każdej chwili możesz je wyłączyć poprzez kliknięcie poniżej „{{int:optin-submit-out}}”.',
	'optin-success-out' => 'Udało Ci się wyłączyć ulepszenia zrealizowane w ramach Inicjatywy Użyteczności.
W każdej chwili możesz je przywrócić poprzez kliknięcie poniżej „{{int:optin-submit-in}}”.',
	'optin-accept-short' => 'Zrób to!',
	'optin-deny-short' => 'Nie, dziękuję',
	'optin-deny-long' => 'powrót do poprzedniej strony',
	'optin-submit-out' => 'Wyłącz',
	'optin-survey-intro' => 'Dziękujemy za wypróbowanie ulepszeń zrealizowanych w ramach Inicjatywy Użyteczności.
Możesz wyłączyć ulepszenia poprzez kliknięcie na dole strony „{{int:optin-submit-out}}”.

Chcielibyśmy poznać Twoją opinię dotyczącą nowego interfejsu, w związku z tym będziemy wdzięczni za wypełnienie poniższej nieobowiązkowej ankiety, zanim klikniesz „{{int:optin-submit-out}}”.',
	'optin-survey-question-whyoptout' => 'Dlaczego wyłączasz ten interfejs?',
	'optin-survey-answer-whyoptout-hard' => 'Edytowanie strony było zbyt trudne.',
	'optin-survey-answer-whyoptout-didntwork' => 'Nie działał poprawnie.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Nie podoba mi się nowy pasek narzędzi.',
	'optin-survey-answer-whyoptout-other' => 'Inny powód',
	'optin-survey-question-feedback' => 'Napisz nam swoją opinię',
	'optin-survey-question-browser' => 'Z jakiej korzystasz przeglądarki?',
	'optin-survey-answer-browser-ie5' => 'Internet Explorer 5',
	'optin-survey-answer-browser-ie6' => 'Internet Explorer 6',
	'optin-survey-answer-browser-ie7' => 'Internet Explorer 7',
	'optin-survey-answer-browser-ie8' => 'Internet Explorer 8',
	'optin-survey-answer-browser-ff1' => 'Firefox 1',
	'optin-survey-answer-browser-ff2' => 'Firefox 2',
	'optin-survey-answer-browser-ff3' => 'Firefox 3',
	'optin-survey-answer-browser-cb' => 'Google Chrome Beta',
	'optin-survey-answer-browser-c1' => 'Google Chrome 1',
	'optin-survey-answer-browser-c2' => 'Google Chrome 2',
	'optin-survey-answer-browser-s3' => 'Safari 3',
	'optin-survey-answer-browser-s4' => 'Safari 4',
	'optin-survey-answer-browser-o9' => 'Opera 9',
	'optin-survey-answer-browser-o9.5' => 'Opera 9.5',
	'optin-survey-answer-browser-o10' => 'Opera 10',
	'optin-survey-answer-browser-other' => 'Inna przeglądarka',
	'optin-survey-question-os' => 'Którego systemu operacyjnego używasz?',
	'optin-survey-answer-os-windows' => 'Windows',
	'optin-survey-answer-os-macos' => 'Mac OS',
	'optin-survey-answer-os-linux' => 'Linux',
	'optin-survey-answer-os-other' => 'Inny system operacyjny',
	'optin-survey-question-res' => 'Z jakiej rozdzielczości ekranu korzystasz?',
	'optin-improvements' => '== Co zostało poprawione? ==
[[File:UsabilityNavigation.png|left]]
;Ulepszono nawigację
Wyraźne wskazanie stanu „czytanie” czy „pisanie” na stronie artykułu oraz dyskusji.
Pole wyszukiwania jest przeniesione z lewego paska nawigacyjnego do prawego górnego rogu aby ułatwić i przyspieszyć dostęp do wyszukiwania.
<div style="clear:both;"></div> 

[[File:UsabilityToolbar.png|left]]
;Zmodyfikowano pasek narzędzi edycyjnych
Rozszerzalny pasek narzędzi ukrywa ikony rzadko wykorzystywanych narzędzi.
Doświadczeni użytkownicy mogą rozwinąć pasek, aby uzyskać dostęp do ikon narzędzi specjalistycznych.
Odniesienia do stron pomocy są wbudowane w pasek narzędzi.
Jako ikony paska narzędzi zostały wykorzystane ikony środowisk Tango oraz Gnome.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Ulepszono interfejs wyszukiwania
Jako wynik wyszukiwania zazwyczaj wyświetlany jest artykuł; tytuły wyników ułożone są w kolejności zwiększającej przejrzystość, a mniej przydatne informacje są niżej umieszczane.
<div style="clear:both;"></div>',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'optin-survey-answer-whyoptout-other' => 'بل سبب:',
	'optin-survey-question-browser' => 'تاسې کوم کتنمل کاروۍ؟',
	'optin-survey-answer-browser-other' => 'بل کتنمل:',
	'optin-survey-question-os' => 'تاسې کوم چليز غونډال کاروۍ؟',
);

/** Portuguese (Português)
 * @author Malafaya
 * @author Waldir
 */
$messages['pt'] = array(
	'optin' => 'Participar na Iniciativa de Usabilidade',
	'optin-desc' => 'Permitir que os utilizadores experimentem o Beta, as melhorias mais recentes da Iniciativa de Usabilidade.',
	'optin-title-optedout' => 'Experimentar o Beta',
	'optin-title-optedin' => 'Sair do Beta',
	'optin-title-justoptedin' => 'Bem-vindo ao Beta',
	'optin-title-justoptedout' => 'Obrigado por experimentar o Beta',
	'optin-title-feedback' => 'Enviar comentários',
	'optin-needlogin' => 'Você precisa [$1 autenticar-se] para experimentar o Beta.',
	'optin-intro' => 'A Wikipedia Usability Initiative tem vindo a trabalhar arduamente para melhorar a usabilidade da Wikipédia.
Gostaria de experimentar o Beta?',
	'optin-feedback-intro' => 'Obrigado por experimentar Beta.
Gostaríamos de saber o que você acha da nossa nova interface, por isso agradecíamos que preenchesse o inquérito opcional abaixo.',
	'optin-feedback-back' => 'Se você não quiser preencher o inquérito, pode regressar a $1.',
	'optin-success-in' => 'Você está agora a experimentar o Beta.
Você pode optar por sair a qualquer momento, clicando no link "{{int:optin-leave}}" no topo do ecrã.',
	'optin-leave-cancel' => 'Se você quiser continuar a usar o Beta, você pode voltar para $1.',
	'optin-success-out' => 'Você saiu do Beta.
Você pode experimentá-lo novamente a qualquer momento, clicando no link "{{int:optin-try}}" na parte superior direita do ecrã.',
	'optin-success-feedback' => 'Obrigado pelos seus comentários!',
	'optin-accept-short' => 'Ok, vamos fazê-lo!',
	'optin-accept-long' => 'Experimente o Beta',
	'optin-accept-long-anon' => 'Entre e experimente o Beta',
	'optin-deny-short' => 'Não, obrigado',
	'optin-deny-long' => 'Regressar à página anterior',
	'optin-submit-out' => 'Sair do Beta',
	'optin-submit-feedback' => 'Enviar comentários',
	'optin-try' => 'Experimente o Beta',
	'optin-leave' => 'Sair do Beta',
	'optin-feedback' => 'Enviar comentários',
	'optin-survey-yes' => 'Sim',
	'optin-survey-no' => 'Não',
	'optin-survey-intro' => 'Obrigado por experimentar o Beta.
Gostaríamos de saber o que você acha da nossa nova interface, por isso agradecíamos que preenchesse o inquérito opcional abaixo, antes de clicar em "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'De que você gostou no Beta? De que você não gostou no Beta?',
	'optin-survey-question-whyoptout' => 'Por que você está a sair do Beta? (Selecione todas as opções que se aplicam.)',
	'optin-survey-answer-whyoptout-hard' => 'Foi muito difícil de usar.',
	'optin-survey-answer-whyoptout-didntwork' => 'Não funcionou corretamente.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Não funcionou de forma consistente.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Eu não gostei do seu aspecto.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Eu não gostei dos novos separadores e design.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Eu não gostei da nova barra de ferramentas.',
	'optin-survey-answer-whyoptout-other' => 'Outro motivo:',
	'optin-survey-question-explain' => 'Por favor explique ou elabore a sua resposta anterior:',
	'optin-survey-question-techfail' => 'Você sofreu alguma falha técnica ao usar o Beta?',
	'optin-survey-question-techfail-ifyes' => 'Se sim, por favor descreva ou explique:',
	'optin-survey-question-usedtoolbar' => 'Você usou a nova barra de ferramentas expansível para editar uma página?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Se sim, de que você gostou nela? De que você não gostou nela?',
	'optin-survey-question-usedtoolbar-ifno' => 'Se não, porque não? Irá no futuro? Por favor, elabore.',
	'optin-survey-question-different' => 'O que poderíamos ter feito de forma diferente para que você continuasse a usar o nosso Beta?',
	'optin-survey-question-feedback' => 'Por favor, indique-nos quaisquer outros comentários, críticas ou sugestões que você tenha para nós - todas as opiniões são úteis!:',
	'optin-survey-question-browser' => 'Que navegador utiliza?',
	'optin-survey-answer-browser-other' => 'Outro navegador:',
	'optin-survey-question-os' => 'Que sistema operativo utiliza?',
	'optin-survey-answer-os-other' => 'Outro sistema operativo:',
	'optin-survey-question-res' => 'Qual é a resolução do seu ecrã?',
	'optin-survey-question-changes' => 'Que mudanças você gostaria de ver no Beta no futuro (selecione até três)?',
	'optin-survey-answer-changes-nav' => 'Tornar a navegação geral do site mais fácil de usar.',
	'optin-survey-answer-changes-edittools' => 'Criar mais e melhores ferramentas de edição.',
	'optin-survey-answer-changes-upload' => 'Melhorar o carregamento de conteúdo e multimédia.',
	'optin-survey-answer-changes-richtext' => 'Desenvolver ferramentas de edição semelhantes a editores avançados de texto.',
	'optin-survey-answer-changes-lookfeel' => 'Renovar o aspecto geral do site.',
	'optin-survey-answer-changes-predictability' => 'Melhorar a consistência dos resultados.',
	'optin-survey-answer-changes-compat' => 'Melhorar a compatibilidade com a wiki local.',
	'optin-survey-answer-changes-other' => 'Outros:',
	'optin-improvements' => '== O que foi melhorado? ==
[[File:UsabilityNavigation.png|left]]
;Melhorada a navegação
O novo sistema de navegação torna mais fácil ver onde você está e o que você está a fazer, bem como onde pode ir e como chegar lá. Com a caixa de pesquisa localizada no canto superior direito, a funcionalidade de pesquisa é mais fácil de encontrar e mais rápida de usar.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
;Melhoramentos na barra de ferramentas de edição
Novos e melhorados ícones tornam mais claro que acção que cada ferramenta realiza. Secções ampliáveis reduzem o empilhamento de botões, continuando a manter ferramentas menos utilizadas a apenas um clique de distância.
A secção de ajuda fornece acesso rápido a uma referência para usos comuns de código wiki.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Melhor interface de pesquisa
Ao usar a pesquisa, correspondências de título são mostradas em primeiro lugar, ajudando-o a encontrar rapidamente os artigos  mais relevantes. Perfis de pesquisa tornam a pesquisa de diferentes tipos de conteúdos mais simples, enquanto que a nova interface de pesquisa avançada torna mais fácil executar pesquisas complexas.
<div style="clear:both;"></div>',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 */
$messages['ro'] = array(
	'optin-survey-answer-whyoptout-other' => 'Alt motiv:',
	'optin-survey-answer-browser-other' => 'Alt browser:',
);

/** Russian (Русский)
 * @author Ferrer
 * @author Lockal
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'optin' => 'Участие в Инициативе юзабилити',
	'optin-desc' => 'Позволяет участникам опробовать Beta, последнее улучшение, сделанное в рамках Инициативы юзабилити',
	'optin-title-optedout' => 'Опробовать Beta',
	'optin-title-optedin' => 'Покинуть Beta',
	'optin-title-justoptedin' => 'Добро пожаловать в Beta',
	'optin-title-justoptedout' => 'Благодарим вас за проверку Beta',
	'optin-title-feedback' => 'Отправить фидбек',
	'optin-needlogin' => 'Вам необходимо [$1 представиться], чтобы опробовать Beta.',
	'optin-intro' => 'В рамках Инициативы юзабилити была проведена работа по улучшению удобства использования Википедии.
Вы хотите опробовать Beta?',
	'optin-success-in' => 'Теперь вы принимаете участие в опробовании Beta.
В можете отключиться от программы в любое время, нажав  на ссылку «{{int:optin-leave}}» в верхней части экрана.',
	'optin-leave-cancel' => 'Если вы хотите завершить использовать Beta, вы можете вернуться к $1.',
	'optin-success-out' => 'Вы успешно покинули Beta.
Вы можете ещё раз попробовать эту систему, нажав ссылку «{{int:optin-try}}» в правом верхнем углу экрана.',
	'optin-success-feedback' => 'Спасибо вам за фидбек!',
	'optin-accept-short' => 'Поехали!',
	'optin-accept-long' => 'Опробовать Beta',
	'optin-accept-long-anon' => 'Представиться системе и попробовать Beta',
	'optin-deny-short' => 'Нет, спасибо',
	'optin-deny-long' => 'вернуться на предыдущую страницу',
	'optin-submit-out' => 'Покинуть Beta',
	'optin-submit-feedback' => 'Отправить фидбек',
	'optin-try' => 'Опробовать Beta',
	'optin-leave' => 'Покинуть Beta',
	'optin-feedback' => 'Отправить фидбек',
	'optin-survey-yes' => 'Да',
	'optin-survey-no' => 'Нет',
	'optin-survey-intro' => 'Благодарим вас за опробование Beta.
Нам бы хотелось знать что вы думаете о новом интерфейсе, поэтому мы были бы признательны, если бы вы приняли участие в приведённом ниже опросе, перед тем, как нажмёте «[[#leave|{{int:optin-submit-out}}]]».',
	'optin-survey-question-likedislike' => 'Что вам нравится в Beta? Что вам не нравится в Beta?',
	'optin-survey-question-whyoptout' => 'Почему вы покидаете Beta? (пожалуйста, выберите все подходящие значения)',
	'optin-survey-answer-whyoptout-hard' => 'Он слишком сложен в использовании.',
	'optin-survey-answer-whyoptout-didntwork' => 'Он не работает должным образом.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Она работает непредсказуемо.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Мне не нравится как она выглядит.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Мне не понравились новые вкладки и вёрстка.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Мне не понравилась новая панель редактирования.',
	'optin-survey-answer-whyoptout-other' => 'Другая причина:',
	'optin-survey-question-explain' => 'Пожалуйста, объясните или уточните ваш ответ выше:',
	'optin-survey-question-techfail' => 'Вы столкнулись с какими-либо техническими сбоями при использовании Beta?',
	'optin-survey-question-techfail-ifyes' => 'Если да, то пожалуйста уточните или объясните:',
	'optin-survey-question-usedtoolbar' => 'Вы использовали новую разворачивающуюся панель инструментов для редактирования страниц?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Если да, то, что вам в ней понравилось? Что не понравилось?',
	'optin-survey-question-different' => 'Что мы могли бы переделать, чтобы вы продолжили использовать нашу Beta?',
	'optin-survey-question-feedback' => 'Пожалуйста, сообщите нам своё мнение:',
	'optin-survey-question-browser' => 'Какой браузер вы используете?',
	'optin-survey-answer-browser-ie5' => 'Internet Explorer 5',
	'optin-survey-answer-browser-ie6' => 'Internet Explorer 6',
	'optin-survey-answer-browser-ie7' => 'Internet Explorer 7',
	'optin-survey-answer-browser-ie8' => 'Internet Explorer 8',
	'optin-survey-answer-browser-ff1' => 'Firefox 1',
	'optin-survey-answer-browser-ff2' => 'Firefox 2',
	'optin-survey-answer-browser-ff3' => 'Firefox 3',
	'optin-survey-answer-browser-cb' => 'Google Chrome Beta',
	'optin-survey-answer-browser-c1' => 'Google Chrome 1',
	'optin-survey-answer-browser-c2' => 'Google Chrome 2',
	'optin-survey-answer-browser-s3' => 'Safari 3',
	'optin-survey-answer-browser-s4' => 'Safari 4',
	'optin-survey-answer-browser-o9' => 'Opera 9',
	'optin-survey-answer-browser-o9.5' => 'Opera 9.5',
	'optin-survey-answer-browser-o10' => 'Opera 10',
	'optin-survey-answer-browser-other' => 'Другой браузер:',
	'optin-survey-question-os' => 'Какую операционную систему вы используете?',
	'optin-survey-answer-os-windows' => 'Windows',
	'optin-survey-answer-os-macos' => 'Mac OS',
	'optin-survey-answer-os-linux' => 'Linux',
	'optin-survey-answer-os-other' => 'Другая операционная система:',
	'optin-survey-question-res' => 'Каково разрешение вашего монитора?',
	'optin-survey-question-changes' => 'Какие изменения вы хотите увидеть в Beta в будущем (выберите до трёх) ?',
	'optin-survey-answer-changes-nav' => 'Сделать навигацию по всему сайту более простой в использовании.',
	'optin-survey-answer-changes-edittools' => 'Расширить и усовершенствовать инструменты редактирования.',
	'optin-survey-answer-changes-upload' => 'Усовершенствовать загрузку содержания и медиа.',
	'optin-survey-answer-changes-richtext' => 'Разработать инструменты редактирования, аналогичные текстовым редакторам.',
	'optin-survey-answer-changes-lookfeel' => 'Обновить «внешний вид» сайта.',
	'optin-survey-answer-changes-compat' => 'Усовершенствовать совместимость с локальной вики.',
	'optin-survey-answer-changes-other' => 'Другие:',
	'optin-improvements' => '== Что было улучшено? ==
[[File:UsabilityNavigation.png|left]]
; Усовершенствование навигации 
Новая навигационная система позволяет проще понять где вы находитесь и что делаете, а также куда вы можете перейти и как туда попасть. С полем поиска, расположенным в верхнем правом углу, искать становится проще и быстрее.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
; Улучшение панели редактирования
Новые и улучшенные значки более точно показывают какие инструменты какие действия выполняют. Раскрываемые разделы уменьшают беспорядок, сохраняя при этом редко используемые инструменты на расстоянии щелчка мыши. Справочный раздел обеспечивает быстрый доступ к примерам по наиболее используемым типам вики-разметки.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
; Улучшение интерфейса поиска
В результатах поиска совпадения с названиями страниц будут показаны вверху, что поможет вам быстро найти подходящие статьи. Профили поиска делают проще поиск различных видов информации, а новый интерфейс расширенного поиска упрощает работу со сложными запросами.
<div style="clear:both;"></div>',
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'optin-submit-out' => 'Арахсарга',
	'optin-survey-question-whyoptout' => 'Тоҕо бу туруорууну араарарга сананныҥ?',
	'optin-survey-answer-whyoptout-hard' => 'Сирэйдэри уларытарга наһаа ыарахан.',
	'optin-survey-answer-whyoptout-didntwork' => 'Сатаан үлэлээбэт.',
	'optin-survey-answer-whyoptout-other' => 'Атын төрүөт:',
	'optin-survey-question-feedback' => 'Бука диэн, бэйэҥ санааҕын эт эрэ:',
	'optin-survey-question-browser' => 'Ханнык браузерынан туһанаҕын?',
	'optin-survey-answer-browser-other' => 'Атын браузер:',
	'optin-survey-question-os' => 'Ханнык операционнай систиэмэни туһанаҕын?',
	'optin-survey-answer-os-other' => 'Атын ОС:',
	'optin-survey-question-res' => 'Эн мониторуҥ разрешениета төһөнүй?',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'optin' => 'Prihlásenie do Iniciatívy použiteľnosti',
	'optin-desc' => 'Umožňuje používateľom skúsiť Beta, najnovšie rozšírenia použiteľnosti Iniciatívy použiteľnosti',
	'optin-title-optedout' => 'Skúsiť Beta',
	'optin-title-optedin' => 'Opustiť Beta',
	'optin-title-justoptedin' => 'Vitajte v Beta',
	'optin-title-justoptedout' => 'Ďakujeme, že ste skúsili Beta',
	'optin-needlogin' => 'Ak sa chcete skúsiť Beta, musíte sa najskôr [$1 prihlásiť].',
	'optin-intro' => 'Iniciatíva použiteľnosti projektu Wikipédia pracuje s cieľom vylepšiť použiteľnosť Wikipédie. Chcete skúsiť Beta?',
	'optin-success-in' => 'Teraz skúšate Beta.
Kedykoľvek môžete skúšanie opustiť kliknutím na „{{int:optin-leave}}“ v pravom hornom rohu.',
	'optin-leave-cancel' => 'Ak si želáte pokračovať v používaní Beta, môžete sa vrátiť späť na $1',
	'optin-success-out' => 'Úspešne ste opustili Beta.
Kedykoľvek ho môžete znova vyskúšať kliknutím na „{{int:optin-try}}“ v pravom hornom rohu.',
	'optin-accept-short' => 'Urobme to!',
	'optin-accept-long' => 'Vyskúšať Beta',
	'optin-deny-short' => 'Nie, ďakujem',
	'optin-deny-long' => 'späť na predošlú stránku',
	'optin-submit-out' => 'Opustiť Beta',
	'optin-try' => 'Skúsiť Beta',
	'optin-leave' => 'Opustiť Beta',
	'optin-survey-yes' => 'Áno',
	'optin-survey-no' => 'Nie',
	'optin-survey-intro' => 'Ďakujeme, že ste skúsili Beta.
Chceli by sme vedieť, čo si myslíte o novom rozhraní, takže by sme ocenili keby ste vyplnili tento nepovinný dotazník predtým, než kliknete na „[[#leave|{{int:optin-submit-out}}]]“ nižšie.',
	'optin-survey-question-likedislike' => 'Čo sa vám na Beta páčilo? Čo sa vám nepáčilo?',
	'optin-survey-question-whyoptout' => 'Prečo opúšťate skúšanie tohto testovacieho rozhrania? (prosím, označte všetky relevantné dôvody)',
	'optin-survey-answer-whyoptout-hard' => 'Bolo príliš ťažké ho používať.',
	'optin-survey-answer-whyoptout-didntwork' => 'Nefungovalo správne.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Nesprávalo sa predvídateľne.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Nepáčilo sa mi ako vyzará.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Nepáčili sa mi nové záložky a rozloženie.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Nepáčil sa mi nový panel nástrojov.',
	'optin-survey-answer-whyoptout-other' => 'Iný dôvod:',
	'optin-survey-question-explain' => 'Prosím, vysvetlite alebo rozveďte svoju odpoveď, ktorú ste uviedli vyššie:',
	'optin-survey-question-techfail' => 'Nastali u vás nejaké technické problémy súvisiace s používaním Beta?',
	'optin-survey-question-techfail-ifyes' => 'Ak áno, rozveďte alebo vysvetlite ich, prosím:',
	'optin-survey-question-usedtoolbar' => 'Používali ste nový rozšíriteľný panel nástrojov na upravovanie stránky?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Ak áno, čo sa vám na ňom páčilo? Čo sa vám nepáčilo?',
	'optin-survey-question-different' => 'Čo sme mohli spraviť inak aby ste neprestali používať testovacie rozhranie Beta?',
	'optin-survey-question-feedback' => 'Ak máte ďalšie komentáre, napíšte ich prosím:',
	'optin-survey-question-browser' => 'Ktorý prehliadač používate?',
	'optin-survey-answer-browser-other' => 'Iný prehliadač:',
	'optin-survey-question-os' => 'Ktorý operačný systém používate?',
	'optin-survey-answer-os-other' => 'Iný operačný systém:',
	'optin-survey-question-res' => 'Aké je rozlíšenie vašej obrazovky?',
	'optin-improvements' => '== Čo bolo zlepšené? ==
[[File:UsabilityNavigation.png|left]]
;Rozšírená navigácia
Vďaka novému navigačnému systému je jednoduchšie rozlíšiť, kde sa nachádzate a tiež kam a ako sa môžete dostať. Vďaka vyhľadávaciemu poľu v pravom hornom rohu je prístup jednoduchší a vyhľadávanie rýchlejšie.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
;Vylepšenia panelu úprav
Nové a vylepšené ikony vyjasňujú čo ktorý nástroj robí.
Rozbaliteľné časti skrývajú zriedka používané nástroje, pričom sú stále len o jedno kliknutie ďalej.
Časť Pomocník poskytuje rýchly prístup do referenčnej dokumentácie bežného ppoužitia wiki syntaxe.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Vylepšené vyhľadávacie rozhranie
Vo výsledkoch vyhľadávania sa ako prvé zobrazujú zhody v názvoch článkov, čo pomáha rýchlejšie nájsť najrelevantnejšie výsledky.
Vyhľadávacie profily uľahčujú vyhľadávanie rôznych druhov obsahu.
Vďaka rozhraniu pokročilého vyhľadávania je jednoduché tvoriť komplexné vyhľadávacie požiadavky.
<div style="clear:both;"></div>',
);

/** Telugu (తెలుగు)
 * @author Kiranmayee
 * @author Veeven
 */
$messages['te'] = array(
	'optin-survey-yes' => 'అవును',
	'optin-survey-no' => 'కాదు',
	'optin-survey-answer-whyoptout-hard' => 'వాడడానికి చాలా కష్టంగా ఉంది.',
	'optin-survey-answer-whyoptout-other' => 'ఇతర కారణం:',
	'optin-survey-question-browser' => 'మీరు ఏ విహారిణిని వాడుతున్నారు?',
	'optin-survey-answer-browser-other' => 'ఇతర విహారిణి:',
	'optin-survey-question-os' => 'మీరు వాడుతున్న నిర్వాహక వ్యవస్థ ఏది?',
	'optin-survey-answer-os-other' => 'ఇతర నిర్వాహక వ్యవస్థలు:',
);

/** Turkish (Türkçe)
 * @author Joseph
 */
$messages['tr'] = array(
	'optin' => 'Kullanılabilirlik Girişimi katılım',
	'optin-desc' => "Kullanıcıların Beta'yı, Kullanılabilirlik Girişimi'nin en son kullanılabilirlik geliştirmelerini, denemelerine izin verir",
	'optin-title-optedout' => 'Beta Deneyin',
	'optin-title-optedin' => "Beta'dan Çık",
	'optin-title-justoptedin' => "Beta'ya Hoşgeldiniz",
	'optin-title-justoptedout' => "Beta'yı denediğiniz için teşekkür ederiz",
	'optin-title-feedback' => 'Geribildirim verin',
	'optin-needlogin' => "Beta'yı denemek için [$1 giriş yapmalısınız].",
	'optin-intro' => "Vikipedi Kullanılabilirlik Girişimi, Vikipedi'nin kullanılabilirliğini geliştirmek amacıyla çok yoğun çalışıyor.
Beta'yı denemek ister misiniz?",
	'optin-feedback-intro' => "Beta'yı denediğiniz için teşekkürler.
Yeni arayüzümüz hakkında ne düşündüğünüzü bilmek isteriz, eğer aşağıdaki isteğe bağlı anketimizi doldurursanız memnun kalırız.",
	'optin-feedback-back' => 'Eğer anketi doldurmak istemiyorsanız, $1 sayfasına dönebilirsiniz.',
	'optin-success-in' => 'Şu anda Beta\'yı deniyorsunuz.
Ekranın en üstündeki "{{int:optin-leave}}" bağlantısına tıklayarak istediğiniz zaman ayrılabilirsiniz.',
	'optin-leave-cancel' => "Eğer Beta'yı kullanmaya devam etmek isterseniz, $1 sayfasına geri dönebilirsiniz",
	'optin-success-out' => 'Beta\'dan başarıyla ayrıldınız.
Ekranın sağ üst kısmındaki "{{int:optin-try}}" bağlantısına tıklayarak istediğiniz zaman tekrar deneyebilirsiniz.',
	'optin-success-feedback' => 'Geribildiriminiz için teşekkür ederiz!',
	'optin-accept-short' => 'Hadi yapalım!',
	'optin-accept-long' => "Acai Beta'yı Dene",
	'optin-accept-long-anon' => "Giriş yapın ve Beta'yı deneyin",
	'optin-deny-short' => 'Hayır teşekkürler',
	'optin-deny-long' => 'Önceki sayfaya dön',
	'optin-submit-out' => "Beta'dan Çık",
	'optin-submit-feedback' => 'Geribildirim verin',
	'optin-try' => "Acai Beta'yı Dene",
	'optin-leave' => "Acai Beta'dan çıkın",
	'optin-feedback' => 'Geribildirim verin',
	'optin-survey-yes' => 'Evet',
	'optin-survey-no' => 'Hayır',
	'optin-survey-intro' => 'Beta\'yı denediğiniz için teşekkürler.
Yeni arayüzümüz konusunda ne düşündüğünüzü bilmek isteriz, o yüzden "[[#leave|{{int:optin-submit-out}}]]" düğmesine tıklamadan önce isteğe bağlı anketimizi doldurursanız memnun kalırız.',
	'optin-survey-question-likedislike' => "Beta'nın beğendiğiniz yönleri nelerdir? Beta'nın sevmediğiniz yönleri nelerdir?",
	'optin-survey-question-whyoptout' => "Neden bu Beta'dan çıkıyorsunuz? (lütfen uygun olanların hepsini seçin)",
	'optin-survey-answer-whyoptout-hard' => 'Kullanmak çok zor.',
	'optin-survey-answer-whyoptout-didntwork' => 'Düzgün çalışmadı.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Tahmin edilebilir şekilde çalışmadı.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Görünümünü beğenmedim.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Yeni sekmeleri ve düzeni beğenmedim.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Yeni araç çubuğunu beğenmedim.',
	'optin-survey-answer-whyoptout-other' => 'Diğer sebep:',
	'optin-survey-question-explain' => 'Lütfen yukarıdaki cevabınızı açıklayın ya da detaylandırın:',
	'optin-survey-question-techfail' => "Beta'yı kullanırken herhangi bir teknik arıza ile karşılaştınız mı?",
	'optin-survey-question-techfail-ifyes' => 'Evet ise, lütfen ayrıntılandırın veya açıklayınız:',
	'optin-survey-question-usedtoolbar' => 'Bir sayfayı değiştirmek için yeni genişletilebilir araç çubuğunu kullandınız mı?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Evet ise, neleri sevdiniz? Nelerden hoşlanmadınız?',
	'optin-survey-question-usedtoolbar-ifno' => 'Eğer hayırsa, neden? Gelecekte kullanacak mısınız? Lütfen açıklayın.',
	'optin-survey-question-different' => "Beta'mızı kullanmaya devam etmeniz için farklı olarak ne yapabilirdik?",
	'optin-survey-question-feedback' => 'Lütfen geribeslemenizi bildirin:',
	'optin-survey-question-browser' => 'Hangi tarayıcıyı kullanıyorsunuz?',
	'optin-survey-answer-browser-other' => 'Diğer tarayıcı:',
	'optin-survey-question-os' => 'Hangi işletim sistemini kullanıyorsunuz?',
	'optin-survey-answer-os-other' => 'Diğer işletim sistemi:',
	'optin-survey-question-res' => 'Ekran çözünürlüğünüz nedir?',
	'optin-survey-question-changes' => "Beta'da gelecekte hangi değişiklikleri görmek istersiniz (üç taneye kadar seçin)?",
	'optin-survey-answer-changes-nav' => 'Sitenin genel dolaşımını kullanımı kolay yapın.',
	'optin-survey-answer-changes-edittools' => 'Düzenleme araçlarını çoğaltın ve geliştirin.',
	'optin-survey-answer-changes-upload' => 'Ortam yüklemelerini ve içeriği geliştirin.',
	'optin-survey-answer-changes-richtext' => 'Zengin-metin editörlerine benzer değiştirme araçlarını geliştirin.',
	'optin-survey-answer-changes-lookfeel' => 'Sitenin "görünüm ve hissini" güncelleyin.',
	'optin-survey-answer-changes-predictability' => 'Öngörülebilirlik performansını geliştirin.',
	'optin-survey-answer-changes-compat' => 'Yerel viki ile uyumluluğunu geliştirin.',
	'optin-survey-answer-changes-other' => 'Diğer:',
	'optin-improvements' => '== Neler geliştirildi? ==
[[File:UsabilityNavigation.png|left]]
;Geliştirilmiş dolaşım
Yeni dolaşım sistemi, nerede olduğunuzu ve ne yaptığınızı görmenizi, ayrıca nereye gidebileceğinizi ve oraya nasıl ulaşacağınızı kolaylaştırır. Sağ üst köşede yer alan yeni arama kutusu ile bulmak daha kolay ve kullanmak daha hızlı.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
;Değiştirme araç çubuğu geliştirmeleri
Yeni-ve-geliştirilmiş simgeler her aracın ne eylemi yaptığını daha netleştirir. Genişletilebilir bölümler dağınıklığı azaltırken az kullanılan araçları sadece bir tık ötesinde saklar.
Yardım bölümü, viki-biçimlendirmesinin genel kullanımı için bir referansa kolay erişim sağlar.
<div style="clear:both;"></div>

[[File:UsabilitySearch.png|left]]
;Geliştirilmiş arama arayüzü
Arama kullanılırken, eşleşen başlıklar önce gösterilir, en alakalı maddeleri hızlıca bulmanıza yardımcı olur. Yeni gelişmiş arama arayüzü karmaşık aramaları kolay bir iş yaparken, arama profilleri farklı türdeki içerikleri aramayı kolaylaştırır.
<div style="clear:both;"></div>',
);

/** Ukrainian (Українська)
 * @author AS
 */
$messages['uk'] = array(
	'optin-survey-question-whyoptout' => 'Чому ви вирішили відмовитися від цього інтерфейсу?',
	'optin-survey-answer-whyoptout-hard' => 'Надто складно редагувати сторінки.',
	'optin-survey-answer-whyoptout-didntwork' => 'Він не працює належним чином.',
	'optin-survey-answer-whyoptout-other' => 'Інша причина:',
	'optin-survey-question-feedback' => 'Будь ласка, висловіть своє судження:',
	'optin-survey-question-browser' => 'Яким оглядачем ви користуєтесь?',
	'optin-survey-answer-browser-other' => 'Інший:',
	'optin-survey-question-os' => 'Якою операційною системою ви користуєтесь?',
	'optin-survey-answer-os-other' => 'Інша:',
	'optin-survey-question-res' => 'Яка роздільність вашого монітора?',
);

/** Vèneto (Vèneto)
 * @author Candalua
 */
$messages['vec'] = array(
	'optin-survey-yes' => 'Sì',
	'optin-survey-no' => 'No',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'optin-survey-answer-whyoptout-hard' => 'Khó sửa đổi trang quá.',
	'optin-survey-answer-whyoptout-didntwork' => 'Nó bị trục trặc.',
	'optin-survey-answer-whyoptout-other' => 'Lý do khác:',
	'optin-survey-question-browser' => 'Bạn sử dụng trình duyệt nào?',
	'optin-survey-answer-browser-other' => 'Trình duyệt khác:',
	'optin-survey-question-os' => 'Bạn sử dụng hệ điều hành nào?',
	'optin-survey-answer-os-other' => 'Khác:',
	'optin-survey-question-res' => 'Màn hình của bạn có độ phân tích nào?',
);

/** Yue (粵語)
 * @author Shinjiman
 */
$messages['yue'] = array(
	'optin' => '可用性倡議選入',
	'optin-desc' => '容許用戶選擇性去加入可用性倡議嘅可用性加強',
	'optin-needlogin' => '你需要[$1 登入]去加入可用性倡議嘅可用性加強。',
	'optin-intro' => '維基百科可用性小組開發咗一個新皮同埋一個新編輯工具去加強維基百科嘅可用性。呢啲加強重未響全部用戶開晒，但係你可以撳下面嘅"{{int:optin-submit-in}}"選入。',
	'optin-success-in' => '你已經成功噉選入到可用性小組嘅可用性加強。
你可以響任何時間撳下面嘅"{{int:optin-submit-out}}"去選出番去。',
	'optin-success-out' => '你已經成功噉選出到可用性小組嘅可用性加強。
你可以響任何時間撳下面嘅"{{int:optin-submit-in}}"去選入番去。',
	'optin-submit-out' => '選出',
	'optin-survey-intro' => '多謝你去試用可用性小組嘅可用性加強。
你可以響任何時間響呢版下面撳"{{int:optin-submit-out}}"去選出。

我哋想知道你對呢個新界面嘅諗法，因此我哋會好高興如果你撳"{{int:optin-submit-out}}"之前完成一個可選嘅調查。',
	'optin-survey-question-whyoptout' => '點解你會選出呢個界面？',
	'optin-survey-answer-whyoptout-hard' => '太難去編輯一版。',
	'optin-survey-answer-whyoptout-didntwork' => '唔能夠正確噉操作。',
	'optin-survey-answer-whyoptout-other' => '其它原因：',
	'optin-survey-question-feedback' => '請畀我哋知道你嘅反饋：',
	'optin-survey-question-browser' => '你用邊隻瀏覽器？',
	'optin-survey-answer-browser-other' => '其它瀏覽器：',
	'optin-survey-question-os' => '你用邊套操作系統？',
	'optin-survey-answer-os-other' => '其它：',
	'optin-survey-question-res' => '你個螢光幕嘅解像度有幾大？',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Shinjiman
 */
$messages['zh-hans'] = array(
	'optin' => '可用性倡议加入',
	'optin-desc' => '容许用户选择性去加入可用性倡议的可用性增强',
	'optin-needlogin' => '您需要[$1 登入]去加入可用性倡议的可用性增强。',
	'optin-intro' => '维基百科可用性小组开发了一个新皮肤和一个新的编辑工具去加强维基百科的可用性。这些增强还未对全部用户开放，但是您可以点击下面的"{{int:optin-submit-in}}"加入。',
	'optin-success-in' => '您已经成功地加入到可用性小组的可用性增强。
您可以随时点击下面的"{{int:optin-submit-out}}"去退出。',
	'optin-success-out' => '您已经成功地退出自可用性小组的可用性增强。
您可以随时点击下面的"{{int:optin-submit-in}}"去加入。',
	'optin-submit-out' => '退出',
	'optin-survey-intro' => '多谢您去试用可用性小组的可用性增强。
您可以随时点击下面的"{{int:optin-submit-out}}"去退出。

我们想知道您对这个新接口的想法，因此我们会好高兴如果您在点击"{{int:optin-submit-out}}"之前完成一个可选的调查。',
	'optin-survey-question-whyoptout' => '为什么您会退出这个接口？',
	'optin-survey-answer-whyoptout-hard' => '太难去编辑一个页面。',
	'optin-survey-answer-whyoptout-didntwork' => '不能正确地操作。',
	'optin-survey-answer-whyoptout-other' => '其它原因：',
	'optin-survey-question-feedback' => '请给我们知道您的反馈：',
	'optin-survey-question-browser' => '您用那款浏览器？',
	'optin-survey-answer-browser-other' => '其它浏览器：',
	'optin-survey-question-os' => '您用哪套操作系统？',
	'optin-survey-answer-os-other' => '其它：',
	'optin-survey-question-res' => '您的屏幕解像度之大小有多大？',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Shinjiman
 */
$messages['zh-hant'] = array(
	'optin' => '可用性倡議加入',
	'optin-desc' => '容許用戶選擇性去加入可用性倡議的可用性增強',
	'optin-needlogin' => '您需要[$1 登入]去加入可用性倡議的可用性增強。',
	'optin-intro' => '維基百科可用性小組開發了一個新皮膚和一個新的編輯工具去加強維基百科的可用性。這些增強還未對全部用戶開放，但是您可以點擊下面的"{{int:optin-submit-in}}"加入。',
	'optin-success-in' => '您已經成功地加入到可用性小組的可用性增強。
您可以隨時點擊下面的"{{int:optin-submit-out}}"去退出。',
	'optin-success-out' => '您已經成功地退出自可用性小組的可用性增強。
您可以隨時點擊下面的"{{int:optin-submit-in}}"去加入。',
	'optin-submit-out' => '退出',
	'optin-survey-intro' => '多謝您去試用可用性小組的可用性增強。
您可以隨時點擊下面的"{{int:optin-submit-out}}"去退出。

我們想知道您對這個新界面的想法，因此我們會好高興如果您在點擊"{{int:optin-submit-out}}"之前完成一個可選的調查。',
	'optin-survey-question-whyoptout' => '為什麼您會退出這個界面？',
	'optin-survey-answer-whyoptout-hard' => '太難去編輯一個頁面。',
	'optin-survey-answer-whyoptout-didntwork' => '不能正確地操作。',
	'optin-survey-answer-whyoptout-other' => '其它原因：',
	'optin-survey-question-feedback' => '請給我們知道您的反饋：',
	'optin-survey-question-browser' => '您用那款瀏覽器？',
	'optin-survey-answer-browser-other' => '其它瀏覽器：',
	'optin-survey-question-os' => '您用哪套操作系統？',
	'optin-survey-answer-os-other' => '其它：',
	'optin-survey-question-res' => '您的屏幕解像度之大小有多大？',
);

