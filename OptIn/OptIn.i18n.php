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
	'optin-title-justoptedout' => 'Thank you for trying our Beta',
	'optin-title-feedback' => 'Beta feedback',
	'optin-needlogin' => 'You need to [$1 log in] to try Beta.',
	'optin-intro' => 'The Wikipedia Usability Initiative has been working hard to improve the usability of Wikipedia.
Would you like to try our Beta?',
	'optin-feedback-intro' => "Thank you for trying Beta.
We would like to know what you think of our new interface, so we would appreciate it if you filled out the optional survey below.",
	'optin-feedback-back' => 'If you do not want to fill out the survey, you can return to $1.',
	'optin-success-in' => 'You are now trying out Beta.
You can opt back out at any time by clicking the "{{int:optin-leave}}" link on the top of the page.',
	'optin-leave-cancel' => 'If you would like to continue using Beta, you can return to $1.',
	'optin-success-out' => 'You have successfully left Beta.
You can try it out again at anytime by clicking the "{{int:optin-try}}" link on the top of the page.',
	'optin-success-feedback' => 'Thank you for your feedback!',
	'optin-accept-short' => 'Let\'s do it!',
	'optin-accept-long' => 'Try out Beta',
	'optin-accept-long-anon' => 'Log in and try out Beta',
	'optin-deny-short' => 'No thank you',
	'optin-deny-long' => 'Return to previous page',
	'optin-submit-out' => 'Leave Beta',
	'optin-submit-feedback' => 'Give feedback',
	'optin-survey-yes' => 'Yes',
	'optin-survey-no' => 'No',
	'optin-survey-intro' => "Thank you for trying the Beta.
We would like to know what you think about our new interface, so we would appreciate it if you filled out the optional survey below before clicking \"[[#leave|{{int:optin-submit-out}}]]\".",
	'optin-survey-question-likedislike' => 'What did you like about the Beta? What did you dislike about the Beta?',
	'optin-survey-question-whyoptout' => 'Why are you leaving the Beta? (Please select all that apply.)',
	'optin-survey-answer-whyoptout-hard' => 'It was too hard to use.',
	'optin-survey-answer-whyoptout-didntwork' => 'It did not function properly.',
	'optin-survey-answer-whyoptout-notpredictable' => 'It did not perform predictably.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'I did not like the way it looked.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'I did not like the new tabs and layout.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'I did not like the new toolbar.',
	'optin-survey-answer-whyoptout-other' => 'Other reason:',
	'optin-survey-question-explain' => 'Please explain or elaborate on your answer above:',
	'optin-survey-question-techfail' => 'Have you experienced any technical failures while using the Beta?',
	'optin-survey-question-techfail-ifyes' => 'If yes, please elaborate or explain:',
	'optin-survey-question-usedtoolbar' => 'Have you used the new expandable toolbar to edit a page?',
	'optin-survey-question-usedtoolbar-ifyes' => 'If yes, what did you like about it? What did you dislike about it?',
	'optin-survey-question-usedtoolbar-ifno' => 'If no, why not? Will you in the future? Please explain.',
	'optin-survey-question-different' => 'What could we have done differently to keep you using the Beta?',
	'optin-survey-question-feedback' => 'Please let us know about any other comments, concerns or thoughts you have for us - all feedback is useful:',
	'optin-survey-question-browser' => 'Which browser do you use?',
	'optin-survey-answer-browser-ie5' => 'Internet Explorer 5',
	'optin-survey-answer-browser-ie6' => 'Internet Explorer 6',
	'optin-survey-answer-browser-ie7' => 'Internet Explorer 7',
	'optin-survey-answer-browser-ie8' => 'Internet Explorer 8',
	'optin-survey-answer-browser-ie9' => 'Internet Explorer 9',
	'optin-survey-answer-browser-ff1' => 'Firefox 1',
	'optin-survey-answer-browser-ff2' => 'Firefox 2',
	'optin-survey-answer-browser-ff3' => 'Firefox 3',
	'optin-survey-answer-browser-cb' => 'Google Chrome Beta',
	'optin-survey-answer-browser-c1' => 'Google Chrome 1',
	'optin-survey-answer-browser-c2' => 'Google Chrome 2',
	'optin-survey-answer-browser-c3' => 'Google Chrome 3',
	'optin-survey-answer-browser-c4' => 'Google Chrome 4',
	'optin-survey-answer-browser-c5' => 'Google Chrome 5',
	'optin-survey-answer-browser-s3' => 'Safari 3',
	'optin-survey-answer-browser-s4' => 'Safari 4',
	'optin-survey-answer-browser-s5' => 'Safari 5',
	'optin-survey-answer-browser-o9' => 'Opera 9',
	'optin-survey-answer-browser-o9.5' => 'Opera 9.5',
	'optin-survey-answer-browser-o10' => 'Opera 10',
	'optin-survey-answer-browser-other' => 'Other browser:',
	'optin-survey-question-os' => 'Which operating system do you use?',
	'optin-survey-answer-os-windows' => 'Windows',
	'optin-survey-answer-os-windowsmobile' => 'Windows Mobile',
	'optin-survey-answer-os-macos' => 'Mac OS',
	'optin-survey-answer-os-iphoneos' => 'iPhone OS',
	'optin-survey-answer-os-ios' => 'iOS',
	'optin-survey-answer-os-linux' => 'Linux',
	'optin-survey-answer-os-other' => 'Other operating system:',
	'optin-survey-question-res' => 'What is the resolution of your screen?',
	'optin-survey-question-changes' => 'What changes would you like to see in the Beta in the future (please select all that apply)?',
	'optin-survey-answer-changes-nav' => 'Make the overall navigation of the site easier to use.',
	'optin-survey-answer-changes-edittools' => 'Increase and enhance the editing tools.',
	'optin-survey-answer-changes-upload' => 'Improve content and media uploads.',
	'optin-survey-answer-changes-richtext' => 'Develop editing tools similar to rich-text editors.',
	'optin-survey-answer-changes-lookfeel' => 'Update the "look and feel" of the site.',
	'optin-survey-answer-changes-predictability' => 'Improve predictability of performance.',
	'optin-survey-answer-changes-custom' => 'Allow greater user customization.',
	'optin-survey-answer-changes-other' => 'Other:',
	'optin-improvements2' => "== What has been improved? ==
[[File:UsabilityToolbar.png|left|link=|Screenshot of the enhanced edit toolbar]]
[[File:UsabilityDialogs.png|left|link=|Screenshot of the new content generation dialogs]]
;Enhanced Toolbar
The enhanced toolbar makes it easier to edit articles.
New and improved icons make it clearer what action each tool performs.
Expanded sections reduce clutter while keeping infrequently used tools just a click away.
;Dialog
When creating links or tables, clicking the tool icon now opens a dialog box which helps you to create links and tables by filling out a simple form.
;And More
We have introduced a number of other improvements such as enhanced navigation, cascading tabs, improved search, and more.
== How to get involved ==
To find out more about the Usability Initiative, please visit our [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative homepage].
<div style=\"clear:both\"></div>",
);

/** Message documentation (Message documentation)
 * @author EugeneZelenko
 * @author Fryed-peach
 * @author Lloffiwr
 * @author Mormegil
 * @author Purodha
 */
$messages['qqq'] = array(
	'optin-desc' => 'Açai is the name of the release, do not translate. In this context, "Beta" is a stage in software development.',
	'optin-title-optedout' => 'Title of Special:UsabilityOptIn when the user is opted out',
	'optin-title-optedin' => 'Title of Special:UsabilityOptIn when the user is opted in',
	'optin-title-justoptedin' => 'Title of Special:UsabilityOptIn when the user has just opted in',
	'optin-title-justoptedout' => 'Title of Special:UsabilityOptIn when the user has just opted out',
	'optin-feedback-back' => "Part of the introduction to the special page 'Beta feedback'. 

$1 is the name of the page the user was on when the link to the 'Beta feedback' page was clicked.",
	'optin-leave-cancel' => "Message in the introduction to the Beta feedback form, which appears after a user has clicked the 'Leave Beta' link at the top of the page.

$1 is the name of the page which the user was on when he/she clicked the 'Leave Beta' link at the top of the page.",
	'optin-accept-short' => 'Upper half of caption for Yes button on opt-in form',
	'optin-accept-long' => 'Bottom half of caption for Yes button on opt-in form',
	'optin-deny-short' => 'Upper half of caption for No button on opt-in form',
	'optin-deny-long' => 'Bottom half of caption for No button on opt-in form for logged-in users',
	'optin-submit-out' => 'Caption for submit button on opt-out/survey form',
	'optin-submit-feedback' => 'Caption of the submit button on the Usability Initiative Beta feedback form.',
	'optin-survey-yes' => '{{Identical|Yes}}

Possible answer to Usability feedback questions {{msg|Optin-survey-question-usedtoolbar}} or {{msg|Optin-survey-question-techfail}}.',
	'optin-survey-no' => '{{Identical|No}}

Possible answer to Usability feedback questions {{msg|Optin-survey-question-usedtoolbar}} or {{msg|Optin-survey-question-techfail}}.',
	'optin-survey-intro' => 'Do not translate #leave',
	'optin-survey-answer-whyoptout-notpredictable' => '"not predictably" means somehing like "randomly". Results of clicks are not alway the same.',
	'optin-survey-answer-whyoptout-other' => '{{Identical|Other reason}}',
	'optin-survey-answer-os-other' => '',
	'optin-survey-answer-changes-other' => '{{Identical|Other}}',
);

/** Abkhazian (Аҧсуа)
 * @author Temuri rajavi
 */
$messages['ab'] = array(
	'optin-accept-long' => 'Бета-версиа аҧышәара',
);

/** Afrikaans (Afrikaans)
 * @author Adriaan
 * @author Naudefj
 */
$messages['af'] = array(
	'optin-title-optedout' => 'Probeer Beta',
	'optin-title-optedin' => 'Verlaat Beta',
	'optin-title-justoptedin' => 'Welkom by Beta',
	'optin-title-justoptedout' => 'Baie dankie dat u Beta uitprobeer het',
	'optin-title-feedback' => 'Beta terugvoer',
	'optin-needlogin' => 'U moet [$1 aanteken] voordat u Beta kan gebruik.',
	'optin-intro' => 'Met Wikipedia se bruikbaarheidsinisiatief is hard gewerk om die bruikbaarheid van Wikipedia te verbeter.
Wil u Beta uitprobeer?',
	'optin-success-feedback' => 'Baie dankie vir u terugvoer!',
	'optin-accept-short' => 'Inskakeling',
	'optin-accept-long' => 'Probeer Beta',
	'optin-accept-long-anon' => 'Teken in en probeer Beta',
	'optin-deny-short' => 'Nee dankie',
	'optin-deny-long' => 'Terug na die vorige bladsy',
	'optin-submit-out' => 'Verlaat Beta',
	'optin-submit-feedback' => 'Gee terugvoer',
	'optin-survey-yes' => 'Ja',
	'optin-survey-no' => 'Nee',
	'optin-survey-question-likedislike' => 'Waarvan het u gehou? Waarvan het u nie gehou nie?',
	'optin-survey-question-whyoptout' => 'Hoekom wil u die Beta-koppelvlak afskakel?
Kies asseblief alle toepaslike redes.',
	'optin-survey-answer-whyoptout-hard' => 'Dit was te moeilik om te gebruik.',
	'optin-survey-answer-whyoptout-didntwork' => 'Dit het nie behoorlik gefunksioneer nie.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Dit reageer nie voorspelbaar nie.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Ek hou nie van hoe dit lyk nie.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Ek hou nie van die nuwe oortjies en uitleg nie.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Ek hou nie van die nuwe wysigingsbalk nie.',
	'optin-survey-answer-whyoptout-other' => 'Ander rede:',
	'optin-survey-question-explain' => 'Verduidelik asseblief of brei uit op u antwoord hierbo:',
	'optin-survey-question-techfail' => 'Het u enige tegniese probleme ondervind tydens die gebruik van die Beta?',
	'optin-survey-question-techfail-ifyes' => 'Indien ja, kan u asseblief uitbrei of verduidelik:',
	'optin-survey-question-usedtoolbar-ifyes' => 'Indien ja, waarvan hou u? Waarvan het u glad nie gehou nie?',
	'optin-survey-question-usedtoolbar-ifno' => 'Indien nie, waarom nie? Sal u in die toekoms? Verduidelik asseblief.',
	'optin-survey-question-different' => 'Wat kon ons anders gedoen het sodat u Beta sal aanhou gebruik?',
	'optin-survey-question-browser' => 'Watter webblaaier gebruik u?',
	'optin-survey-answer-browser-other' => 'Ander webblaaier:',
	'optin-survey-question-os' => 'Watter bedryfstelsel gebruik u?',
	'optin-survey-answer-os-other' => 'Ander bedryfstelsel:',
	'optin-survey-question-res' => 'Wat is die resolusie van u skerm?',
	'optin-survey-question-changes' => 'Watter veranderinge sou u graag in die toekoms in die Beta wou sien (kies asseblief almal wat van toepassing is)?',
	'optin-survey-answer-changes-nav' => 'Maak die navigasie van die werf makliker om te gebruik.',
	'optin-survey-answer-changes-edittools' => 'Meer en beter wysigings-hulpmiddelle.',
	'optin-survey-answer-changes-upload' => 'Verbeter die byvoeging van inhoud en oplaai van media.',
	'optin-survey-answer-changes-richtext' => 'Ontwikkel wysigings-hulpmiddelle vergelykbaar met uitgebreide teksverwerkers.',
	'optin-survey-answer-changes-lookfeel' => 'Opdateer die "look and feel" van die werf.',
	'optin-survey-answer-changes-predictability' => 'Verbeter die voorspelbaarheid van die prestasies.',
	'optin-survey-answer-changes-custom' => 'Groter gebruikeraanpassings toelaat.',
	'optin-survey-answer-changes-other' => 'Ander:',
);

/** Gheg Albanian (Gegë)
 * @author Mdupont
 */
$messages['aln'] = array(
	'optin' => 'Nisma e të përdorshmërisë zgjedhë-në',
	'optin-desc' => 'Lejo përdoruesit të provoni Beta, enhancements fundit Iniciativa e përdorshmërisë së përdorshmërisë.',
	'optin-title-optedout' => 'Provo Beta',
	'optin-title-optedin' => 'Lini Beta',
	'optin-title-justoptedin' => 'Mirë se vini në Beta',
	'optin-title-justoptedout' => 'Ju faleminderit për përpjekjen tonë Beta',
	'optin-title-feedback' => 'reagime Beta',
	'optin-needlogin' => "Ju duhet të [$1 hyni brënda] për t'u përpjekur Beta.",
	'optin-intro' => 'Përdorshmërisë Wikipedia Iniciativa ka qenë duke punuar shumë për përmirësimin e përdorshmërisë së Wikipedia. Doni të provoni Beta tonë?',
	'optin-feedback-intro' => 'Ju faleminderit për përpjekjen Beta. Ne do të doja të di çfarë ju mendoni të re për ndërfaqen e tonë, kështu që ne do të vlerësojmë nëse ju mbushet nga sondazhi opsional më poshtë.',
	'optin-feedback-back' => 'Nëse ju nuk dëshironi ta plotësoni këtë anketë, ju mund të ktheheni tek $1.',
	'optin-success-in' => 'Ju jeni tani duke u përpjekur Beta. Ju mund të zgjedhë përsëri në çdo kohë duke klikuar tek "{{int:optin-leave}}" Lidhje në krye të faqes.',
	'optin-leave-cancel' => "Nëse dëshironi të vazhdoni t'i përdorni Beta, ju mund të ktheheni tek $1.",
	'optin-success-out' => 'Ju keni mbetur me sukses Beta. Ju mund të provoni përsëri në çdo kohë duke klikuar tek "{{int:optin-try}}" Lidhje në krye të faqes.',
	'optin-success-feedback' => 'Faleminderit për komentin tuaj!',
	'optin-accept-short' => 'Le të bëjmë atë!',
	'optin-accept-long' => 'Provoni Beta',
	'optin-accept-long-anon' => 'Log in dhe të provoni Beta',
	'optin-deny-short' => 'Jo faleminderit',
	'optin-deny-long' => 'Kthehu tek faqja e mëparshme',
	'optin-submit-out' => 'Lini Beta',
);

/** Aragonese (Aragonés)
 * @author Juanpabl
 */
$messages['an'] = array(
	'optin-survey-yes' => 'Sí',
	'optin-survey-no' => 'No',
	'optin-survey-answer-whyoptout-other' => 'Atra razón:',
	'optin-survey-answer-changes-other' => 'Atros:',
);

/** Arabic (العربية)
 * @author ;Hiba;1
 * @author Meno25
 * @author OsamaK
 */
$messages['ar'] = array(
	'optin' => 'اشتراك مبادرة الاستخدام',
	'optin-desc' => 'إن السماح للمستخدمين بتجربة البيتا آخر تطورات مبادرة الاستخدامية (Usability Initiative).',
	'optin-title-optedout' => 'جرّب البيتا',
	'optin-title-optedin' => 'غادر البيتا',
	'optin-title-justoptedin' => 'أهلا بك في بيتا',
	'optin-title-justoptedout' => 'شكرًا لتجريبك البيتا.',
	'optin-title-feedback' => 'شاركنا ملاحظاتك و اقتراحاتك',
	'optin-needlogin' => 'يجب أن [$1 تسجل دخولك] لتجرّب البيتا.',
	'optin-intro' => 'عملت مبادرة استخدامية ويكيبيديا (Wikipedia Usability Initiative) بجد على تحسين استخدامية ويكيبيديا.
أترغب في تجربة نسختنا البيتا؟',
	'optin-feedback-intro' => 'شكرًا لتجريبك البيتا.
نود الاطلاع على رأيك في الواجهة الجديدة، ولذا فإننا سنقدر ملأك للاستطلاع الاختياري أدناه.',
	'optin-feedback-back' => 'إذا لم ترغب بملء الاستطلاع، يمكنك الرجوع إلى $1.',
	'optin-success-in' => 'تقوم الآن بتجريب البيتا.
تستطيع العودة في أي وقت بنقر وصلة "{{int:optin-leave}}" في أعلى الشاشة.',
	'optin-leave-cancel' => 'إن كنت تريد الاستمرار في استخدام بيتا, فيمكنك الرجوع إلى $1.',
	'optin-success-out' => 'لقد تركت البيتا بنجاح.
تستطيع تجريبها مرة أخرى في أي وقت بنقر وصلة "{{int:optin-try}}" في أعلى يسار الشاشة.',
	'optin-success-feedback' => 'شكرًا لك على ملاحظاتك!',
	'optin-accept-short' => 'لنجربها!',
	'optin-accept-long' => 'جرّب البيتا',
	'optin-accept-long-anon' => 'أنشئ حسابًا وجرّب البيتا',
	'optin-deny-short' => 'لا، شكرًا',
	'optin-deny-long' => 'ارجع إلى الصفحة السابقة',
	'optin-submit-out' => 'غادر البيتا',
	'optin-submit-feedback' => 'أعطِ ملاحظاتك',
	'optin-survey-yes' => 'نعم',
	'optin-survey-no' => 'لا',
	'optin-survey-intro' => 'شكرًا لتجريب البيتا.
نود أن نطلع على رأيك في واجهتنا الجديدة، لذا فإننا سنقدر ملأك الاستطلاع الاختياري أدناه قبل أن تنقر "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'ما الذي أعجبك في البيتا؟ ما الذي لم يعجبك فيها؟',
	'optin-survey-question-whyoptout' => 'لماذا تغادر البيتا؟ (رجاءً حدّد كل ما ينطبق)',
	'optin-survey-answer-whyoptout-hard' => 'كانت صعبة الاستخدام.',
	'optin-survey-answer-whyoptout-didntwork' => 'لم تؤدِ الغرض بشكل ملائم.',
	'optin-survey-answer-whyoptout-notpredictable' => 'لم تعمل حسب ما توقعت.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'لم يعجبني مظهرها.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'لم يعجبني التصميم والألسنة الجديدة.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'لم يعجبني شريط الأدوات الجديد.',
	'optin-survey-answer-whyoptout-other' => 'أسباب أخرى:',
	'optin-survey-question-explain' => 'رجاءً وضّح وفصّل إجابتك أعلاه:',
	'optin-survey-question-techfail' => 'هل واجهت أي فشل تقني أثناء استخدامك البيتا؟',
	'optin-survey-question-techfail-ifyes' => 'إذا كانت الإجابة بنعم، فمن فضلك فصّل ووضَح:',
	'optin-survey-question-usedtoolbar' => 'هل سبق لك أن استخدمت شريط الأدوات الجديد القابل للتمديد لتحرير صفحة؟',
	'optin-survey-question-usedtoolbar-ifyes' => 'إذا كانت الإجابة بنعم، فما الذي أعجبك فيه؟ وما الذي لم يعجبك فيه؟',
	'optin-survey-question-usedtoolbar-ifno' => 'إذا كانت الإجابة بلا، فلم لم تستخدمه؟ أستسخدمه في المستقبل؟ من فضلك وضّح الإجابة.',
	'optin-survey-question-different' => 'ماذا كان بإمكاننا فعله لكي تستمر في استخدام البيتا؟',
	'optin-survey-question-feedback' => 'من فضلك أطلعنا على أي ملاحظات أو مخاوف أو آراء أخرى عن المشروع - كل ملاحظة مفيدة!:',
	'optin-survey-question-browser' => 'ما المتصفح الذي تستخدمه؟',
	'optin-survey-answer-browser-ie5' => 'إنترنت إكسبلورر 5',
	'optin-survey-answer-browser-ie6' => 'إنترنت إكسبلورر 6',
	'optin-survey-answer-browser-ie7' => 'إنترنت إكسبلورر 7',
	'optin-survey-answer-browser-ie8' => 'إنترنت إكسبلورر 8',
	'optin-survey-answer-browser-ff1' => 'فايرفوكس 1',
	'optin-survey-answer-browser-ff2' => 'فايرفوكس 2',
	'optin-survey-answer-browser-ff3' => 'فايرفوكس 3',
	'optin-survey-answer-browser-cb' => 'جوجل كروم بيتا',
	'optin-survey-answer-browser-c1' => 'جوجل كروم 1',
	'optin-survey-answer-browser-c2' => 'جوجل كروم 2',
	'optin-survey-answer-browser-c3' => 'جوجل كروم 3',
	'optin-survey-answer-browser-c4' => 'جوجل كروم 4',
	'optin-survey-answer-browser-s3' => 'سفاري 3',
	'optin-survey-answer-browser-s4' => 'سفاري 4',
	'optin-survey-answer-browser-o9' => 'أوبرا 9',
	'optin-survey-answer-browser-o9.5' => 'أوبرا 9.5',
	'optin-survey-answer-browser-o10' => 'أوبرا 10',
	'optin-survey-answer-browser-other' => 'متصفح آخر:',
	'optin-survey-question-os' => 'ما نظام التشغيل الذي تستخدمه؟',
	'optin-survey-answer-os-windows' => 'ويندوز',
	'optin-survey-answer-os-windowsmobile' => 'ويندوز موبايل',
	'optin-survey-answer-os-macos' => 'ماك أو إس',
	'optin-survey-answer-os-iphoneos' => 'آي فون أو إس',
	'optin-survey-answer-os-linux' => 'لينكس',
	'optin-survey-answer-os-other' => 'نظام تشغيل آخر:',
	'optin-survey-question-res' => 'ما أبعاد شاشتك؟',
	'optin-survey-question-changes' => 'ما التغييرات التي تطمح لرؤيتها في بيتا المستقبل؟ (من فضلك اختر كل ما ينطبق)',
	'optin-survey-answer-changes-nav' => 'جعل تصفح الموقع بشكل عام أسهل.',
	'optin-survey-answer-changes-edittools' => 'زيادة وتحسين أدوات التحرير.',
	'optin-survey-answer-changes-upload' => 'تحسين المحتويات والوسائط المرفوعة.',
	'optin-survey-answer-changes-richtext' => 'تطوير أدوات تحرير شبيهة بمحررات النصوص الغنية (rich-text).',
	'optin-survey-answer-changes-lookfeel' => 'تحديث "منظر وإحساس" الموقع.',
	'optin-survey-answer-changes-predictability' => 'تطوير القدرة على تنبؤ الأداء.',
	'optin-survey-answer-changes-custom' => 'السماح بتخصيص أكبر من المستخدم.',
	'optin-survey-answer-changes-other' => 'غير ذلك:',
	'optin-improvements2' => '== ماذا تحسّن؟ ==
[[File:UsabilityToolbar.png|left|link=|لقطة شاشة لشريط أدوات التحرير المُحسّن]]
[[File:UsabilityDialogs.png|left|link=|لقطة شاشة لحوارات توليد المحتويات الجديدة]]
;شريط الأدوات المُحسّن
يُسهّل شريط الأدوات المُحسّن تحرير المقالات،
وتوضح الأيقونات الجديدة والمُحسّنة عمل كل أداة،
وتقلل الأقسام المُوسّعة الركام، وتجعل -في نفس الوقت- الأدوات قليلة الاستخدام على بعد نقرة منك.
;الحوار
عند إنشاء الوصلات أو الجداول فإن النقر على الأيقونة يفتح صندوق حوار يساعدك على إنشاء الوصلات والجداول عن طريق تعبئة نموذج بسيط.
;وغيرها
لقد أضفنا عددًا من التحسينات الأخرى مثل التصفح المُحسّن والألسنة المتجاورة والبحث المُحسّن وغيرها.
== كيفية الانضمام ==
لتعرف المزيد عن مبادرة الاستخدامية، من فضلك زر [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative صفحتنا].
<div style="clear:both"></div>',
);

/** Aramaic (ܐܪܡܝܐ)
 * @author Basharh
 */
$messages['arc'] = array(
	'optin-title-optedout' => 'ܢܣܝ ܒܝܛܐ',
	'optin-title-optedin' => 'ܫܒܘܩ ܒܝܛܐ',
	'optin-title-justoptedin' => 'ܒܫܝܢܐ ܒܟ ܒܒܝܛܐ',
	'optin-title-justoptedout' => 'ܬܘܕܝ ܠܢܣܝܢܐ ܕܒܝܛܐ',
	'optin-title-feedback' => 'ܡܚܫܚܬ̈ܐ ܘܡܥܝܪ̈ܢܘܬܐ ܥܠ ܒܝܛܐ',
	'optin-success-feedback' => 'ܬܘܕܝ ܥܠ ܡܚܫܚܬ̈ܐ ܘܡܥܝܪ̈ܢܘܬܐ',
	'optin-accept-short' => 'ܢܣܝ',
	'optin-accept-long' => 'ܢܣܝ ܒܝܛܐ',
	'optin-accept-long-anon' => 'ܥܘܠ ܘܢܣܝ ܒܝܛܐ',
	'optin-deny-short' => 'ܠܐ ܬܘܕܝ',
	'optin-deny-long' => 'ܦܢܝ ܠܦܐܬܐ ܩܕܝܡܬܐ',
	'optin-submit-out' => 'ܫܒܘܩ ܒܝܛܐ',
	'optin-submit-feedback' => 'ܗܒ ܡܚܫܚܬ̈ܐ ܘܡܥܝܪ̈ܢܘܬܐ',
	'optin-survey-yes' => 'ܐܝܢ',
	'optin-survey-no' => 'ܠܐ',
	'optin-survey-answer-whyoptout-other' => 'ܥܠܬܐ ܐܚܪܬܐ:',
	'optin-survey-answer-os-other' => '',
	'optin-survey-answer-changes-other' => 'ܐܚܪܢܐ:',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Ghaly
 * @author Meno25
 * @author Ramsis II
 */
$messages['arz'] = array(
	'optin' => 'اشتراك مبادره الاستخدام',
	'optin-desc' => 'إن السماح للمستخدمين بتجربه البيتا آخر تطورات مبادره الاستخداميه (Usability Initiative).',
	'optin-title-optedout' => 'جرّب البيتا',
	'optin-title-optedin' => 'غادر البيتا',
	'optin-title-justoptedin' => 'أهلا بك فى بيتا',
	'optin-title-justoptedout' => 'شكرًا لتجريبك البيتا.',
	'optin-title-feedback' => 'شاركنا ملاحظاتك و اقتراحاتك',
	'optin-needlogin' => 'يجب أن [$1 تسجل دخولك] لتجرّب البيتا.',
	'optin-intro' => 'عملت مبادره استخداميه ويكيبيديا (Wikipedia Usability Initiative) بجد على تحسين استخداميه ويكيبيديا.
أترغب فى تجربه نسختنا البيتا؟',
	'optin-feedback-intro' => 'شكرًا لتجريبك البيتا.
نود الاطلاع على رأيك فى الواجهه الجديده، ولذا فإننا سنقدر ملأك للاستطلاع الاختيارى أدناه.',
	'optin-feedback-back' => 'إذا لم ترغب بملء الاستطلاع، يمكنك الرجوع إلى $1.',
	'optin-success-in' => 'تقوم الآن بتجريب البيتا.
تستطيع العوده فى أى وقت بنقر وصله "{{int:optin-leave}}" فى أعلى الشاشه.',
	'optin-leave-cancel' => 'إن كنت تريد الاستمرار فى استخدام بيتا, فيمكنك الرجوع إلى $1.',
	'optin-success-out' => 'لقد تركت البيتا بنجاح.
تستطيع تجريبها مره أخرى فى أى وقت بنقر وصله "{{int:optin-try}}" فى أعلى يسار الشاشه.',
	'optin-success-feedback' => 'شكرًا لك على ملاحظاتك!',
	'optin-accept-short' => 'لنجربها!',
	'optin-accept-long' => 'جرّب البيتا',
	'optin-accept-long-anon' => 'أنشئ حسابًا وجرّب البيتا',
	'optin-deny-short' => 'لا، شكرًا',
	'optin-deny-long' => 'ارجع إلى الصفحه السابقة',
	'optin-submit-out' => 'غادر البيتا',
	'optin-submit-feedback' => 'أعطِ ملاحظاتك',
	'optin-survey-yes' => 'نعم',
	'optin-survey-no' => 'لا',
	'optin-survey-intro' => 'شكرًا لتجريب البيتا.
نود أن نطلع على رأيك فى واجهتنا الجديده، لذا فإننا سنقدر ملأك الاستطلاع الاختيارى أدناه قبل أن تنقر "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'ما الذى أعجبك فى البيتا؟ ما الذى لم يعجبك فيها؟',
	'optin-survey-question-whyoptout' => 'لماذا تغادر البيتا؟ (رجاءً حدّد كل ما ينطبق)',
	'optin-survey-answer-whyoptout-hard' => 'كانت صعبه الاستخدام.',
	'optin-survey-answer-whyoptout-didntwork' => 'لم تؤدِ الغرض بشكل ملائم.',
	'optin-survey-answer-whyoptout-notpredictable' => 'لم تعمل حسب ما توقعت.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'لم يعجبنى مظهرها.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'لم يعجبنى التصميم والألسنه الجديده.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'لم يعجبنى شريط الأدوات الجديد.',
	'optin-survey-answer-whyoptout-other' => 'أسباب أخرى:',
	'optin-survey-question-explain' => 'رجاءً وضّح وفصّل إجابتك أعلاه:',
	'optin-survey-question-techfail' => 'هل واجهت أى فشل تقنى أثناء استخدامك البيتا؟',
	'optin-survey-question-techfail-ifyes' => 'إذا كانت الإجابه بنعم، فمن فضلك فصّل ووضَح:',
	'optin-survey-question-usedtoolbar' => 'هل سبق لك أن استخدمت شريط الأدوات الجديد القابل للتمديد لتحرير صفحة؟',
	'optin-survey-question-usedtoolbar-ifyes' => 'إذا كانت الإجابه بنعم، فما الذى أعجبك فيه؟ وما الذى لم يعجبك فيه؟',
	'optin-survey-question-usedtoolbar-ifno' => 'إذا كانت الإجابه بلا، فلم لم تستخدمه؟ أستسخدمه فى المستقبل؟ من فضلك وضّح الإجابه.',
	'optin-survey-question-different' => 'ماذا كان بإمكاننا فعله لكى تستمر فى استخدام البيتا؟',
	'optin-survey-question-feedback' => 'من فضلك أطلعنا على أى ملاحظات أو مخاوف أو آراء أخرى عن المشروع - كل ملاحظه مفيدة!:',
	'optin-survey-question-browser' => 'ما المتصفح الذى تستخدمه؟',
	'optin-survey-answer-browser-other' => 'متصفح آخر:',
	'optin-survey-question-os' => 'ما نظام التشغيل الذى تستخدمه؟',
	'optin-survey-answer-os-other' => 'نظام تشغيل آخر:',
	'optin-survey-question-res' => 'ما أبعاد شاشتك؟',
	'optin-survey-question-changes' => 'ما التغييرات التى تطمح لرؤيتها فى بيتا المستقبل؟ (من فضلك اختر كل ما ينطبق)',
	'optin-survey-answer-changes-nav' => 'جعل تصفح الموقع بشكل عام أسهل.',
	'optin-survey-answer-changes-edittools' => 'زياده وتحسين أدوات التحرير.',
	'optin-survey-answer-changes-upload' => 'تحسين المحتويات والوسائط المرفوعه.',
	'optin-survey-answer-changes-richtext' => 'تطوير أدوات تحرير شبيهه بمحررات النصوص الغنيه (rich-text).',
	'optin-survey-answer-changes-lookfeel' => 'تحديث "منظر وإحساس" الموقع.',
	'optin-survey-answer-changes-predictability' => 'تطوير القدره على تنبؤ الأداء.',
	'optin-survey-answer-changes-custom' => 'السماح بتخصيص أكبر من المستخدم.',
	'optin-survey-answer-changes-other' => 'غير ذلك:',
);

/** Samogitian (Žemaitėška)
 * @author Hugo.arg
 */
$messages['bat-smg'] = array(
	'optin-submit-out' => 'Palėktė Beta',
);

/** Belarusian (Беларуская)
 * @author Yury Tarasievich
 */
$messages['be'] = array(
	'optin-improvements2' => "== Што было палепшана? ==
[[File:UsabilityToolbar.png|left|link=|Здымак экрана з палепшанай рэдактарскай стужкай]]
[[File:UsabilityDialogs.png|left|link=|Здымак экрана з новымі дыялогамі складання зместу]]
;Палепшаная стужка прылад
Палепшаная стужка прылад спрашчае рэдагаванне артыкулаў.
Новыя палепшаныя значкі ясней тлумачаць прызначэнне адпаведных прылад.
Пашыраныя секцыі спрыяюць парадку на стужцы, прытым трымаюць рэдка ўжыванае непадалёк.
;Дыялогі
Пры складанні спасылак ці табліц, націсканне адпаведнага значка адкрывае дыялог з дапаможным фармулярам.
;І іншае
Таксама ўведзеныя іншыя паляпшэнні, напрыклад, у руху па вікі, апрацоўцы табуляцый, пошуку і інш.
== Як запісацца ==
Каб даведацца больш пра Ініцыятыву за зручнасць (''Usability Initiative''), наведайце [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative homepage уласную старонку праекту].
<div style=\"clear:both\"></div>",
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 * @author Red Winged Duck
 * @author Wizardist
 * @author Zedlik
 */
$messages['be-tarask'] = array(
	'optin' => 'Удзел у ініцыятыве па паляпшэньні зручнасьці і прастаты выкарыстаньня',
	'optin-desc' => 'Дазваляе ўдзельнікам паспрабаваць Beta, апошняе паляпшэньне, зробленае ў рамках ініцыятывы па паляпшэньню зручнасьці і прастаты выкарыстаньня',
	'optin-title-optedout' => 'Паспрабаваць Beta',
	'optin-title-optedin' => 'Пакінуць Beta',
	'optin-title-justoptedin' => 'Вітаем у Beta',
	'optin-title-justoptedout' => 'Дзякуй за спробу выкарыстаньня Beta',
	'optin-title-feedback' => 'Зваротная сувязь',
	'optin-needlogin' => 'Вам неабходна [$1 ўвайсьці ў сыстэму], каб паспрабаваць бэта-вэрсію.',
	'optin-intro' => 'У межах ініцыятывы па паляпшэньні зручнасьці і прастаты выкарыстаньня Вікіпэдыі была праведзеная значная праца па паляпшэньні зручнасьці і прастаты выкарыстаньня Вікіпэдыі. Ці жадаеце Вы паспрабаваць Beta?',
	'optin-feedback-intro' => 'Дзякуй за выкарыстаньне Beta.
Нам цікава ведаць, што Вы мяркуеце пра наш новы інтэрфэйс, таму мы былі б удзячныя, калі Вы запоўнілі неабавязковае апытаньне ўнізе.',
	'optin-feedback-back' => 'Калі Вы не жадаеце запаўняць апытаньне, Вы можаце вярнуцца да $1.',
	'optin-success-in' => 'Цяпер Вы прымаеце ўдзел у выпрабаваньні Beta.
Вы можаце пакінуць выпрабаваньне ў любы час націснуўшы кнопку «{{int:optin-leave}}» у верхяй частцы экрана.',
	'optin-leave-cancel' => 'Калі Вы жадаеце працягваць выкарыстаньне Beta, Вы можаце вярнуцца да $1',
	'optin-success-out' => 'Вы пасьпяхова пакінулі Beta.
Вы можаце зноў паспрабаваць яе націснуўшы кнопку «{{int:optin-try}}» у правым верхнім куце экрана.',
	'optin-success-feedback' => 'Дзякуй за зваротную сувязь!',
	'optin-accept-short' => 'Зрабіць гэта!',
	'optin-accept-long' => 'Паспрабаваць Beta',
	'optin-accept-long-anon' => 'Увайсьці ў сыстэму і паспрабаваць Beta',
	'optin-deny-short' => 'Не, дзякуй',
	'optin-deny-long' => 'вярнуцца да папярэдняй старонкі',
	'optin-submit-out' => 'Пакінуць Beta',
	'optin-submit-feedback' => 'Даслаць',
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
	'optin-survey-question-usedtoolbar-ifno' => 'Калі не, чаму? Ці будзіце выкарыстоўваць у будучыні? Калі ласка, растлумачце.',
	'optin-survey-question-different' => 'Што нам можна зрабіць інакш, каб Вы працягвалі карыстацца нашай Beta?',
	'optin-survey-question-feedback' => 'Калі ласка, паведаміце нам Вашае меркаваньне:',
	'optin-survey-question-browser' => 'Якім браўзэрам Вы карыстаецеся?',
	'optin-survey-answer-browser-other' => 'Іншы браўзэр:',
	'optin-survey-question-os' => 'Якой апэрацыйнай сыстэмай Вы карыстаецеся?',
	'optin-survey-answer-os-other' => 'Іншая апэрацыйная сыстэма:',
	'optin-survey-question-res' => 'Якое разрозьненьне Вашага манітора?',
	'optin-survey-question-changes' => 'Якія зьмены Вы жадаеце бачыць у Beta ў будучыні (выберыце ўсё, што падыходзіць)?',
	'optin-survey-answer-changes-nav' => 'Зрабіць навігацыю па сайту больш простай у выкарыстаньні.',
	'optin-survey-answer-changes-edittools' => 'Пашырыць і палепшыць інструмэнты рэдагаваньня.',
	'optin-survey-answer-changes-upload' => 'Палепшыць загрузку зьместу і мэдыя-файлаў.',
	'optin-survey-answer-changes-richtext' => 'Распрацаваць прылады рэдагаваньня, падобныя на тэкставыя рэдактары.',
	'optin-survey-answer-changes-lookfeel' => 'Абнавіць «зьнешні выгляд і запаўненьне» сайта.',
	'optin-survey-answer-changes-predictability' => 'Палепшыць прадказальнасьць хуткадзеяньня.',
	'optin-survey-answer-changes-custom' => 'Дазволіць удзельнікам пашыраную настройку.',
	'optin-survey-answer-changes-other' => 'Іншыя:',
	'optin-improvements2' => '== Што было палепшана? ==
[[File:UsabilityToolbar.png|left|link=|Здымак экрану панэлі рэдагаваньня]]
[[File:UsabilityDialogs.png|left|link=|Здымак экрану новыя дыялёгаў утварэньня]]
;Пашыраная панэль інструмэнтаў
Пашыраная панэль інструмэнтаў палягчае рэдагаваньне артыкулаў.
Новыя палепшаныя выявы больш дакладна паказваюць адпаведнасьць інструмэнтаў і іх дзеяньняў.
Пашыраныя падзелы зьмяншаюць перашкоды, захоўваючы ня часта выкарыстоўваемыя інструмэнты на адлегласьці аднаго кліку мышкі.
;Дыялёг
Цяпер, пад час стварэньня спасылак ці табліцаў, можна націснуць на адпаведную выяву і адчыненае дыялёгавае акно дапаможа стварыць спасылку ці табліцу праз запаўненьне простай формы.
;І болей
Мы дадалі некалькі іншых паляпшэньняў, такіх як палепшаная навігацыя, каскадныя табліцы, палепшаны пошук і болей.
== Як уключыцца ==
Каб даведацца болей пра Ініцыятыва па паляпшэньні зручнасьці і прастаты выкарыстаньня, калі ласка, наведайце [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative нашу старонку].
<div style="clear:both"></div>',
);

/** Bulgarian (Български)
 * @author DCLXVI
 * @author Spiritia
 * @author Turin
 */
$messages['bg'] = array(
	'optin-desc' => 'Позволява на потребителите да изпробват Бета, най-новият резултат на Инициативата за подобряване на ползваемостта.',
	'optin-title-optedout' => 'Изпробвайте Бета',
	'optin-title-optedin' => 'Напускане на Бета',
	'optin-title-justoptedin' => 'Добре дошли в Бета',
	'optin-title-justoptedout' => 'Благодарим ви, че изпробвате нашата бета-версия',
	'optin-title-feedback' => 'Отзиви за Бета',
	'optin-needlogin' => 'Трябва да [$1 влезете], за да изпробвате Бета.',
	'optin-intro' => 'Бета е резултат от усилията на инициативата за подобряване на използваемостта на Уикипедия. 
Искате ли да изпробвате Бета?',
	'optin-feedback-intro' => 'Благодарим ви, че изпробвахте Бета. 
Бихме искали да знаем какво мислите за новия ни интерфейс и ще се радваме, ако решите да отговорите на въпросите от проучването по-долу.',
	'optin-feedback-back' => 'Ако не желаете да участвате в проучването, можете да се върнете към $1.',
	'optin-success-in' => 'В момента изпробвате Бета.
Можете по всяко време да се върнете към оригиналния си интерфейс, като щракнете връзката "{{int:optin-leave}}" в горния край на екрана.',
	'optin-leave-cancel' => 'Ако искате да продължите да използвате Бета, можете да се върнете на $1.',
	'optin-success-out' => 'Успешно излязохте от Бета. 
Можете по всяко време да се върнете, като щракнете връзката "{{int:optin-try}}" в горния край на екрана.',
	'optin-success-feedback' => 'Благодарим за отзива!',
	'optin-accept-short' => 'Хайде!',
	'optin-accept-long' => 'Изпробвайте Бета',
	'optin-accept-long-anon' => 'Влезте в системата и изпробвайте Бета',
	'optin-deny-short' => 'Не, благодаря',
	'optin-deny-long' => 'Връщане към предишната страница',
	'optin-submit-out' => 'Напускане на Бета',
	'optin-submit-feedback' => 'Обратна връзка',
	'optin-survey-yes' => 'Да',
	'optin-survey-no' => 'Не',
	'optin-survey-intro' => 'Благодарим ви, че изпробвахте Бета. Бихме искали да знаем какво мислите за новия ни интерфейс и ще се радваме, ако решите да отговорите на въпросите от проучването по-долу, като щракнете на връзката "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Какво ви допадна в Бета? Какво не ви хареса?',
	'optin-survey-question-whyoptout' => 'Защо излизате от Бета? (Моля, изберете всички подходящи отговори.)',
	'optin-survey-answer-whyoptout-hard' => 'Беше много сложно за ползване.',
	'optin-survey-answer-whyoptout-didntwork' => 'Не работеше както трябва.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Държеше се непредсказуемо.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Не ми хареса как изглежда.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Не ми хареса изгледът с новите бутони.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Не ми хареса новата лента за редактиране.',
	'optin-survey-answer-whyoptout-other' => 'Друга причина:',
	'optin-survey-question-explain' => 'Моля, обяснете с повече подробности горния си отговор:',
	'optin-survey-question-techfail' => 'Изпитахте ли някакви технически затруднения докато използвахте Бета?',
	'optin-survey-question-techfail-ifyes' => 'Ако да, можете ли да ги обясните с повече подробности:',
	'optin-survey-question-usedtoolbar' => 'Използвахте ли новата лента с падащи раздели за редактиране на страниците?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Ако да, какво ви хареса в нея? А какво не ви хареса?',
	'optin-survey-question-usedtoolbar-ifno' => 'Ако не, защо? Бихте ли я използвали в бъдеще? Моля, обяснете.',
	'optin-survey-question-different' => 'Какво трябва да се промени, за да продължите да използвате Бета?',
	'optin-survey-question-feedback' => 'Отправете тук своите коментари, забележки и виждания за работата ни - всякакви отзиви ще са добре дошли!',
	'optin-survey-question-browser' => 'Какъв браузър използвате?',
	'optin-survey-answer-browser-other' => 'Друг браузър:',
	'optin-survey-question-os' => 'Каква операционна система използвате?',
	'optin-survey-answer-os-other' => 'Друга операционна система:',
	'optin-survey-question-res' => 'Каква екранна резолюция използвате?',
	'optin-survey-question-changes' => 'Как бихте искали Бета да се променя в бъдеще (моля, изберете всички подходящи отговори)?',
	'optin-survey-answer-changes-nav' => 'Да се улесни цялостната навигация на сайта.',
	'optin-survey-answer-changes-edittools' => 'Да се подобрят и улеснят инструментите за редактиране.',
	'optin-survey-answer-changes-upload' => 'Да се подобри качването на текстово и мултимедийно съдържание.',
	'optin-survey-answer-changes-richtext' => 'Да се разработят инструменти за редактиране, подобни на тези в текстообработващите офис приложения.',
	'optin-survey-answer-changes-lookfeel' => 'Обновяване на външния вид на сайта.',
	'optin-survey-answer-changes-predictability' => 'Да стане по-прозрачно функционирането на сайта.',
	'optin-survey-answer-changes-custom' => 'Позволяване на подробни потребителски настройки.',
	'optin-survey-answer-changes-other' => 'Друго:',
	'optin-improvements2' => '== Какво подобрихме? ==
[[File:UsabilityToolbar.png|left|link=|Екранен кадър на подобрената лента с инструменти за редактиране]]
[[File:UsabilityDialogs.png|left|link=|Екранен кадър на новия диалогов прозорец за въвеждане на препратка]]

; Подобрена лента с инструменти
Подобрената лента с инструменти улеснява редактирането на страниците. Нови и усъвършенствани иконки правят обясняват по-добре кой бутон каква функция изпълнява. Падащите менюта внасят порядък и едновременно с това позволяват не толкова често използваните инструменти да са разположени на разстояние само на едно щракване с мишката.

; Диалогови прозорци
При щракване върху бутона за създаване на препратки и таблици вече се отваря диалогов прозорец, съдържащ лесен за попълване формуляр. 

; И още...
Въведени са и още много други подобрения като улеснена навигация, каскадни менюта, подобрено търсене и т.н.

== Научете повече ==
За да научите повече за Инициативата за подобряване на ползваемостта, моля посетете [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative нейната начална страница].
<div style="clear:both"></div>',
);

/** Bengali (বাংলা)
 * @author Bellayet
 * @author Wikitanvir
 */
$messages['bn'] = array(
	'optin' => 'ইউজাবিলিটি ইনিশিয়েটিভ চালু করুন',
	'optin-desc' => 'ব্যবহারকারীদের বেটা ব্যবহারের সুযোগ দিন, ইউজাবিলিটি ইনিসিয়াটিভ হল সাম্প্রতিকতম ব্যবহারযোগ্যতা বৃদ্ধি।',
	'optin-title-optedout' => 'বেটা চেষ্টা করুন',
	'optin-title-optedin' => 'বেটা ছেড়ে যান',
	'optin-title-justoptedin' => 'বেটায় স্বাগতম',
	'optin-title-justoptedout' => 'বেটা চেষ্টা করার জন্য আপনাকে ধন্যবাদ',
	'optin-title-feedback' => 'বেটা প্রতিক্রিয়া',
	'optin-needlogin' => 'বেটা চেষ্টা করার জন্য আপনাকে [$1 লগইন] করতে হবে।',
	'optin-intro' => 'উইকিপিডিয়ার ব্যবহারযোগ্যতার উন্নয়ন করার জন্য উইকিপিডিয়া ইউজাবিলিটি ইনিসিয়েটিভ কঠর পরিশ্রম করে যাচ্ছে।
আপনি বেটা ব্যবহার করতে চান?',
	'optin-feedback-intro' => 'বেটা ব্যবহার করার জন্য ধন্যবাদ।
নতুন ইন্টারফেস সম্পর্কে আপনি কি ভাবছেন তা আমরা জানতে চাই, যদি আপনি নিচের ঐচ্ছিক জরিপ ফরমটি পূরণ করেন তাহলে আমরা খুবই খুশি হবো।',
	'optin-feedback-back' => 'যদি জরিপ ফরমটি পূরণ করতে না চান, তাহলে আপনি $1 এ ফিরে যেতে পারেন।',
	'optin-success-in' => 'আপনি এখন বেটা ব্যবহার করছেন।
আপনি যে-কোনো সময় পাতার ওপরের "{{int:optin-leave}}" লিংকে ক্লিক করে পূর্বের অবস্থায় ফিরে যেতে পারবেন।',
	'optin-leave-cancel' => 'আপনি যদি নতুন বৈশিষ্ট্যাবলী ব্যবহার চালিয়ে যেতে চান তবে আপনি $1-এ ফিরে যেতে পারেন।',
	'optin-success-out' => 'আপনি সফলভাবে বেটা ত্যাগ করেছেন।
আপনি যে-কোনো সময় পাতার ওপরের "{{int:optin-try}}" লিংকে ক্লিক করে আবার বেটাতে ফিরে যেতে পারবেন।',
	'optin-success-feedback' => 'আপনার ফিডব্যাকের জন্য ধন্যবাদ!',
	'optin-accept-short' => 'চলুন শুরু করি!',
	'optin-accept-long' => 'বেটা চেষ্টা করুন',
	'optin-accept-long-anon' => 'লগইন করুন এবং বেটা চেষ্টা করুন',
	'optin-deny-short' => 'না ধন্যবাদ',
	'optin-deny-long' => 'পূর্বের পাতায় ফিরে যাও',
	'optin-submit-out' => 'বেটা ছেড়ে যাও',
	'optin-submit-feedback' => 'ফিডব্যাক দিন',
	'optin-survey-yes' => 'হ্যাঁ',
	'optin-survey-no' => 'না',
	'optin-survey-intro' => 'বেটা চেষ্টা করার জন্য আপনাকে ধন্যবাদ।
আমাদের নতুন ইন্টারফেস সম্পর্কে আপনার অনুভূতি আমরা জানতে চাই। তাই আমরা আপনাকে উৎসাহ দিচ্ছি যে, "[[#leave|{{int:optin-submit-out}}]]" লিংকে ক্লিক করার পূর্বে নিচের ঐচ্ছিক জরিপ ফর্মটি আপনি পূরণ করবেন।',
	'optin-survey-question-likedislike' => 'বেটায় আপনার কি পছন্দ হয়েছে? বেটায় আপনার কি পছন্দ হয় নাই?',
	'optin-survey-question-whyoptout' => 'আপনি বেটা কেন ছেড়ে যাচ্ছেন? (অনুগ্রহ করে যেগুলো প্রযোজ্য প্রয়োগ করুন)',
	'optin-survey-answer-whyoptout-hard' => 'এতে কাজ করা কঠিন।',
	'optin-survey-answer-whyoptout-didntwork' => 'এটি ঠিক মত কাজ করে না।',
	'optin-survey-answer-whyoptout-notpredictable' => 'এটি আন্দাজ মত কাজ করে না।',
	'optin-survey-answer-whyoptout-didntlike-look' => 'এটা দেখতে যেমন তা আমার পছন্দ নয়।',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'নতুন ট্যাব এবং বিন্যাস আমার পছন্দ হয়নি।',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'নতুন টুলবার আমার পছন্দ হয়নি।',
	'optin-survey-answer-whyoptout-other' => 'অন্য কারণ:',
	'optin-survey-question-explain' => 'উপরে দেওয়া আপনার উত্তর অনুগ্রহ করে বিস্তারিত এবং বুঝিয়ে বলুন:',
	'optin-survey-question-techfail' => 'বেটা ব্যবহারের সময় আপনি কি কোন কারগরি দূর্বলতা লক্ষ্য করেছেন?',
	'optin-survey-question-techfail-ifyes' => 'হ্যাঁ হলে, অনুগ্রহ করে বিস্তারিত লিখুন:',
	'optin-survey-question-usedtoolbar' => 'আপনি কি পাতা সম্পাদনার জন্য নতুন সম্প্রসারণযোগ্য টুলবার ব্যবহার করেছেন?',
	'optin-survey-question-usedtoolbar-ifyes' => 'হ্যাঁ হলে, এটা সম্পর্কে কি পছন্দ হয়েছে? এটা সম্পর্কে কি পছন্দ হয় নাই?',
	'optin-survey-question-usedtoolbar-ifno' => 'না হলে, কেন ব্যবহার করেন নি? ভবিষ্যতে ব্যবহার করবেন? অনুগ্রহ করে বিস্তারিত লিখুন।',
	'optin-survey-question-different' => 'বেটা ব্যবহার করতে আমরা আরও অন্য কি করতে পারি?',
	'optin-survey-question-feedback' => 'অনুগ্রহ করে আমাদের জন্য আপনার আরও কোন মন্তব্য, মতামত, পরামর্শ, চিন্তা থাকলে তা আমাদের জানান - সমস্ত প্রতিক্রিয়াই উপকারী:',
	'optin-survey-question-browser' => 'আপনি কোন ব্রাউজার ব্যবহার করেন?',
	'optin-survey-answer-browser-other' => 'অন্য ব্রাউজার:',
	'optin-survey-question-os' => 'আপনি কোন অপারেটিং সিস্টেম ব্যবহার করেন?',
	'optin-survey-answer-os-other' => 'অন্য অপারেটিং সিস্টেম:',
	'optin-survey-question-res' => 'আপনার পর্দার রেজ্যুলেশন কত?',
	'optin-survey-question-changes' => 'ভবিষ্যতে বেটাতে আর কি কি পরিবর্তন দেখতে চান (প্রযোজ্যসমূহ নির্বাচন করুন)?',
	'optin-survey-answer-changes-nav' => 'সাইটের সার্বিক নেভিগেশন ব্যবহার সহজ করো।',
	'optin-survey-answer-changes-edittools' => 'সম্পাদনা টুল বৃদ্ধি এবং সমৃদ্ধ করা।',
	'optin-survey-answer-changes-upload' => 'কন্টেন্ট এবং মিডিয়া আপলোড ব্যবস্থার উন্নয়ন।',
	'optin-survey-answer-changes-richtext' => 'সমৃদ্ধ-টেক্সট সম্পাদকের মত একই রকম ভাবে সম্পাদনা টুলের উন্নয়ন করো।',
	'optin-survey-answer-changes-lookfeel' => 'সাইটের "লুক এন্ড ফিল" হালনাগাদ করুন।',
	'optin-survey-answer-changes-predictability' => 'আন্দাজ করার ক্ষমতা উন্নয়ন করুন।',
	'optin-survey-answer-changes-custom' => 'আরও বেশি স্বনির্ধারণের সুযোগ',
	'optin-survey-answer-changes-other' => 'অন্য:',
	'optin-improvements2' => '== কি কি উন্নয়ন করা হয়েছে? ==
[[File:UsabilityToolbar.png|left|link=|বর্তমানের সমৃদ্ধ সম্পাদনা টুলবারের একটি স্ক্রিনশট]]
[[File:UsabilityDialogs.png|left|link=|নতুনভাবে ডায়ালগ বক্সের মাধ্যমে লেখা যোগ করার একটি স্ক্রিনশট]]
;সমৃদ্ধ টুলবার
নতুন ও সমৃদ্ধ সম্পাদনা টুলবার নিবন্ধ সম্পাদনাকে আরও সহজ করে তুলেছে।
নতুন এবং উন্নত আইকনসমূহ টুলটি কি কাজ করবে তা আরও সুন্দর ও পরিষ্কারভাবে সংজ্ঞায়িত করেছে।
উচ্চপর্যায়ের সম্পাদনার জন্য নির্ধারিত অংশগুলো ভাঁজ করে রাখা হয়েছে। এর ফলে প্রয়োজনীয় সরঞ্জামগুলো যেমন সহজেই কাছে পাওয়া যাচ্ছে তেমনি উচ্চপর্যায়ের সরঞ্জামগুলোও এক ক্লিকেই বের করে নেওয়া যাচ্ছে।
;ডায়ালগ
যখন কোনো নিবন্ধে কোনো পাতার লিংক সংযোগ বা টেবিল যোগ করছেন, তখন নির্ধারিত আইকনে ক্লিক করলে একটি ডায়ালগ বক্স খুলবে, যা সহজ একটি ফর্ম পূরণের মাধ্যমে নিবন্ধে সংযোগ ও সাধারণ টেবিল যোগ করতে সাহায্য করবে।
;এবং আরও
এছাড়া আমরা আরও কিছু উন্নয়নের সাথে আপনাদের পরিচয় করিয়েছি। তার মধ্যে আছে, সমৃদ্ধ নেভিগেশন, পৃথক ট্যাব, উন্নত অনুসন্ধান ব্যবস্থা, এবং আরও অনেক কিছু।
== যেভাবে যুক্ত হবেন ==
ইউজাবিলিটি ইনিশিয়েটিভ সম্মন্ধে আরও জানতে আমাদের [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative হোমপেজ] পরিদর্শন করুন।
<div style="clear:both"></div>',
);

/** Breton (Brezhoneg)
 * @author Fohanno
 * @author Fulup
 * @author Y-M D
 */
$messages['br'] = array(
	'optin' => 'Kemer perzh en intrudu a implijadusted',
	'optin-desc' => 'Aotren a ra an implijerien da amprouiñ ar Beta amañ, gwellaennoù diwezhañ an intrudu implijadusted',
	'optin-title-optedout' => 'Amprouiñ Beta',
	'optin-title-optedin' => 'Dilezel Beta',
	'optin-title-justoptedin' => 'Degemer mat war Beta',
	'optin-title-justoptedout' => "Trugarez deoc'h da amprouiñ ar Beta",
	'optin-title-feedback' => 'Roit ho soñj war ar Beta',
	'optin-needlogin' => "Ret eo deoc'h bezañ [$1 kevreet] evit ober gant Beta.",
	'optin-intro' => "Labouret start ez eus bet gant Intrudu Implijadusted Wikipedia evit gwellaat implijadusted raktresoù Wikimedia.
Hag amprouiñ Beta a fell deoc'h ober ?",
	'optin-feedback-intro' => "Trugarez deoc'h da amprouiñ Beta.
Ni a garfe gouzout petra a soñjit diwar-benn an etrefas nevez, setu e vefemp laouen ma restpontfec'h d'ar goulennoù dindan.",
	'optin-feedback-back' => "Ma ne fell ket deoc'h leuniañ ar roll goulennoù, e c'hallit distreiñ da $1.",
	'optin-success-in' => "Emaoc'h oc'h amprouiñ Beta.
Gallout a rit distreiñ d'ar stumm boas pa garit en ur glikañ war al liamm \"{{int:optin-leave}}\" e laez ar skramm.",
	'optin-leave-cancel' => "M'hoc'h eus c'hoant da genderc'hel da implijout Beta, e c'hallit distreiñ da $1.",
	'optin-success-out' => "Kuitaet hoc'h eus Beta.
Tu zo deoc'h e amprouiñ en-dro en ur glikañ war al liamm \"{{int:optin-try}}\" en nec'h, a-zehou ar skramm.",
	'optin-success-feedback' => 'Trugarez evit bezañ roet ho soñj !',
	'optin-accept-short' => "Bec'h de'i",
	'optin-accept-long' => 'Amprouiñ Beta',
	'optin-accept-long-anon' => 'Kevreit ha grit un taol-esae gant Beta',
	'optin-deny-short' => 'Ket, trugarez',
	'optin-deny-long' => "Distreiñ d'ar bajenn gent",
	'optin-submit-out' => 'Dilezel Beta',
	'optin-submit-feedback' => 'Roit ho soñj',
	'optin-survey-yes' => 'Ya',
	'optin-survey-no' => 'Ket',
	'optin-survey-intro' => 'Trugarez da amprouiñ ar Beta.
Ni a garfe gouzout petra a soñjit diwar-benn an etrefas nevez, setu e vefemp laouen ma respontfec\'h d\'ar goulennoù dindan a-raok klikañ war "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => "Petra zo plijet deoc'h er Beta ? Petra en deus displijet deoc'h ?",
	'optin-survey-question-whyoptout' => "Perak e fell deoc'h dilezel ar Beta ? (dibabit kement tra a glot gant ho soñj)",
	'optin-survey-answer-whyoptout-hard' => 'Start e oa da embreger.',
	'optin-survey-answer-whyoptout-didntwork' => "Ne'z ae ket plaen en-dro.",
	'optin-survey-answer-whyoptout-notpredictable' => "Ne'z ae ket en-dro en un doare poellek.",
	'optin-survey-answer-whyoptout-didntlike-look' => 'Ne blije ket din an tres anezhañ.',
	'optin-survey-answer-whyoptout-didntlike-layout' => "N'on bet plijet na gant an ivinelloù na gant an tres nevez.",
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Ne blije ket din ar varrenn ostilhoù nevez.',
	'optin-survey-answer-whyoptout-other' => 'Abeg all :',
	'optin-survey-question-explain' => "Mar plij, displegit pelloc'h ho respont a-us :",
	'optin-survey-question-techfail' => "Ha kudennoù teknikel hoc'h eus bet en ur implijout ar Beta ?",
	'optin-survey-question-techfail-ifyes' => "Mard eo ya, displegit pelloc'h, mar plij :",
	'optin-survey-question-usedtoolbar' => "Hag implijet hoc'h eus ar varrenn ostilhoù pak-dispak nevez da zegas kemmoù war ur bajenn ?",
	'optin-survey-question-usedtoolbar-ifyes' => "Mard eo ya ar respont, petra zo plijet deoc'h ? Ha petra n'en deus ket plijet deoc'h ?",
	'optin-survey-question-usedtoolbar-ifno' => "Ma n'oc'h ket bet plijet, perak ? Hag implijout a reot anezhañ en dazont ? Displegit un tamm mar plij.",
	'optin-survey-question-different' => "Petra hor befe ranket ober mod all, deoc'h da chom gant ar Beta ?",
	'optin-survey-question-feedback' => "Kasit deomp hoc'h alioù, pep evezhiadenn zo dedennus da gaout :",
	'optin-survey-question-browser' => 'Peseurt merdeer a rit gantañ ?',
	'optin-survey-answer-browser-other' => 'Merdeer all :',
	'optin-survey-question-os' => 'Peseurt reizhiad korvoiñ a rit gantañ ?',
	'optin-survey-answer-os-other' => 'Reizhiad korvoiñ all :',
	'optin-survey-question-res' => 'Petra eo pizhder ho skramm ?',
	'optin-survey-question-changes' => "Peseurt kemmoù a garfec'h gwelet er Beta en dazont (dibabit kement tra a zegouezh)?",
	'optin-survey-answer-changes-nav' => "Ober ma vo aesoc'h merdeiñ dre al lec'hienn dre-vras.",
	'optin-survey-answer-changes-edittools' => 'Gwellaat ha kreskiñ an ostilhoù aozañ.',
	'optin-survey-answer-changes-upload' => 'Gwellaat an enporzhiañ danvez ha media.',
	'optin-survey-answer-changes-richtext' => "Diorren binvioù aozañ par d'an aozerioù testenn-pinvidikaet",
	'optin-survey-answer-changes-lookfeel' => "Hizivaat tres al lec'hienn.",
	'optin-survey-answer-changes-predictability' => "Gwellaat diawelerezh ar c'hemmoù.",
	'optin-survey-answer-changes-custom' => "Aotren a ra personelaat an etrefas pelloc'h.",
	'optin-survey-answer-changes-other' => 'Traoù all :',
	'optin-improvements2' => "== Petra zo bet gwellaet ? ==
[[File:UsabilityToolbar.png|left|link=|Skeudenn skramm eus ar varrenn ostilhoù araokaet]]
[[File:UsabilityDialogs.png|left|link=|Skeudenn skramm eus ar prenestroù krouiñ danvez]]
;Barrenn ostilhoù araokaet
A-drugarez d'ar varrenn ostilhoù araokaet eo aesoc'h degas kemmoù er pennadoù.
An arlunioù nevez ha gwellaet a aesa kompren da betra servij pep ostilh.
Al lañserioù pak-dispak a zalc'h diac'hub an dachenn labour en ur lezel kement tra na implijt ket alies war-bouez ur c'hlik logodenn diouzh an ezhomm.
;Prenestroù skridaozañ
Pa savit liammoù pe taolennoù, ma klikit war an arlun, e tigoro ur prenestr skridaozañ evit ho skoazellañ da grouiñ liammoù pe taolennoù dre leuniañ ur furmskrid eeun.
;Ha muioc'h zo
Gwellaennoù all zo bet degaset evel ar merdeiñ araokaet, ivinelloù en ur skalierad, ar c'hlask araokaet ha muioc'h c'hoazh.
== Penaos kemer perzh ==
Evit gouzout hiroc'h diwar-benn an Intrudu Implijadusted, kit da welet ar [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative bajenn degemer].
<div style=\"clear:both\"></div>",
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
	'optin-title-feedback' => 'Slanje povratnih informacija',
	'optin-needlogin' => 'Morate se [$1 prijaviti] da biste mogli isprobati Beta.',
	'optin-intro' => 'Wikipedia Inicijativa upotrebljivosti radi na poboljšanju upotrebljivosti Wikipedije. Da li želite isprobati Beta?',
	'optin-feedback-intro' => 'Zahvaljujemo Vam se za isprobavanje Bete.
Željeli bismo saznati šta mislite o našem novom interfejsu, pa Vas molimo da ispunite neobavezni upitnik ispod.',
	'optin-feedback-back' => 'Ako ne želite da popunite upitnik, možete se vratiti na $1.',
	'optin-success-in' => 'Sada možete isprobati Beta.
Možete se bilo kada odjaviti putem klikanja na link "{{int:optin-leave}}" u gornjem dijelu ekrana.',
	'optin-leave-cancel' => 'Ako biste željeli nastaviti koristiti Beta, možete se vratiti na $1.',
	'optin-success-out' => 'Uspješno ste napustili Beta.
Možete se bilo kada ponovno prijaviti putem klika na link "{{int:optin-try}}" u gornjem desnom uglu ekrana.',
	'optin-success-feedback' => 'Hvala Vam za Vaše povratne informacije!',
	'optin-accept-short' => 'Započni!',
	'optin-accept-long' => 'Isprobajte Beta',
	'optin-accept-long-anon' => 'Prijavite se i isprobajte Beta',
	'optin-deny-short' => 'Ne hvala',
	'optin-deny-long' => 'nazad na prethodnu stranicu',
	'optin-submit-out' => 'Napusti Beta',
	'optin-submit-feedback' => 'Pošalji povratnu informaciju',
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
	'optin-survey-answer-whyoptout-other' => 'Ostali razlozi:',
	'optin-survey-question-explain' => 'Molimo objasnite Vaš odgovor odozgo:',
	'optin-survey-question-techfail' => 'Da li ste primijetili neke tehničke pogreške dok ste koristili Beta?',
	'optin-survey-question-techfail-ifyes' => 'Ako da, molimo objasnite:',
	'optin-survey-question-usedtoolbar' => 'Da li ste koristili novu proširivu alatnu traku za uređivanje stranica?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Ako da, šta Vam se sviđa kod toga? Šta Vam se u tome ne sviđa?',
	'optin-survey-question-usedtoolbar-ifno' => 'Ako ne, zašto ne? Da li će te to učiniti u budućnosti? Molimo objasnite.',
	'optin-survey-question-different' => 'Šta bi morali napraviti drugačije da bi se Vi odlučili nastaviti koristiti Betu?',
	'optin-survey-question-feedback' => 'Molimo pošaljite nam Vaš odgovor:',
	'optin-survey-question-browser' => 'Koji preglednik koristite?',
	'optin-survey-answer-browser-other' => 'Ostali preglednici:',
	'optin-survey-question-os' => 'Koji operativni sistem koristite?',
	'optin-survey-answer-os-other' => 'Drugi operativni sistemi:',
	'optin-survey-question-res' => 'Koja je rezolucija Vašeg monitora?',
	'optin-survey-question-changes' => 'Kakve izmjene bi željeli da vidite na Beti u budućnosti (molimo odaberite sve što je primjenjivo)?',
	'optin-survey-answer-changes-nav' => 'Učiniti cjelokupnu navigaciju na stranici jednostavnijom za upotrebu.',
	'optin-survey-answer-changes-edittools' => 'Povećati broj i poboljšati alate za uređivanje.',
	'optin-survey-answer-changes-upload' => 'Poboljšati sadržaj i broj postavljenih datoteka.',
	'optin-survey-answer-changes-richtext' => 'Razvoj alata za uređivanje sličnim naprednim uređivačima.',
	'optin-survey-answer-changes-lookfeel' => 'Ažurirati "izgled i preglednost" stranice.',
	'optin-survey-answer-changes-predictability' => 'Poboljšati predvidivost performansi.',
	'optin-survey-answer-changes-custom' => 'Omogućuje veća korisnička podešavanja.',
	'optin-survey-answer-changes-other' => 'Ostalo:',
);

/** Catalan (Català)
 * @author Paucabot
 * @author SMP
 */
$messages['ca'] = array(
	'optin' => 'Iniciativa de millora de la Usabilitat',
	'optin-desc' => "Permet als usuaris provar Beta, que inclou les darreres millores d'usabilitat de la ''Usability Initiative''.",
	'optin-title-optedout' => 'Provau Beta',
	'optin-title-optedin' => 'Sortiu de Beta',
	'optin-title-justoptedin' => 'Benvinguts a Beta',
	'optin-title-justoptedout' => 'Gràcies per provar Beta',
	'optin-title-feedback' => 'Donau la vostra opinió',
	'optin-needlogin' => "Us heu d'[$1 iniciar la sessió] per provar Beta.",
	'optin-intro' => "La ''Wikipedia Usability Initiative'' ha estat treballant durament per millorar la usabilitat de Wikipedia.
Voleu provar Beta?",
	'optin-feedback-intro' => "Gràcies per provar Beta.
Volem saber que n'opinau de la nova interfície, per això us agrairíem que responguéssiu l'enquesta voluntària que teniu a continuació.",
	'optin-feedback-back' => "Si no voleu respondre l'enquesta, podeu tornar a $1.",
	'optin-success-in' => "Ara estau provant Beta.
Podeu sortir en qualsevol moment clicant sobre l'enllaç «{{int:optin-leave}}» que teniu al cantó superior dret.",
	'optin-leave-cancel' => 'Si voleu continuar usant Beta, podeu tornar a $1.',
	'optin-success-out' => "Heu sortit de Beta.
Podeu provar-la una altra vegada en qualsevol moment clicant sobre l'enllaç «{{int:optin-try}}» que teniu al cantó superior dret de la pantalla.",
	'optin-success-feedback' => 'Gràcies per la vostra opinió!',
	'optin-accept-short' => 'Fem-ho!',
	'optin-accept-long' => 'Provau Beta',
	'optin-accept-long-anon' => 'Iniciau una sessió i provau Beta',
	'optin-deny-short' => 'No, gràcies',
	'optin-deny-long' => 'Torna a la pàgina anterior',
	'optin-submit-out' => 'Surt de Beta',
	'optin-submit-feedback' => 'Donau la vostra opinió',
	'optin-survey-yes' => 'Sí',
	'optin-survey-no' => 'No',
	'optin-survey-intro' => "Gràcies per provar Beta.
Volem saber què n'opinau de la nova interfície, així que us agrairíem que responguéssiu l'enquesta voluntària que teniu a continuació abans de clicar «[[#leave|{{int:optin-submit-out}}]]».",
	'optin-survey-question-likedislike' => 'Què us ha agradat de Beta? Què no us ha agradat de Beta?',
	'optin-survey-question-whyoptout' => 'Perquè deixau Beta? (Seleccionau totes les opcions escaients)',
	'optin-survey-answer-whyoptout-hard' => "Ha estat massa difícil d'usar.",
	'optin-survey-answer-whyoptout-didntwork' => 'No funcionava correctament.',
	'optin-survey-answer-whyoptout-notpredictable' => 'No funcionava de manera predictible.',
	'optin-survey-answer-whyoptout-didntlike-look' => "No m'ha agradat el seu aspecte.",
	'optin-survey-answer-whyoptout-didntlike-layout' => "No m'han agradat les noves pestanyes ni el nou format.",
	'optin-survey-answer-whyoptout-didntlike-toolbar' => "No m'ha agradat la nova barra d'eines.",
	'optin-survey-answer-whyoptout-other' => 'Una altra raó:',
	'optin-survey-question-explain' => 'Per favor, justificau la vostra resposta anterior:',
	'optin-survey-question-techfail' => 'Heu tengut alguna errada tècnica usant Beta?',
	'optin-survey-question-techfail-ifyes' => 'Si és que sí, per favor, justificau-ho:',
	'optin-survey-question-usedtoolbar' => 'Heu vist la nova barra desplegable per editar pàgines?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Si és que sí, que us ha agradat? Què no us ha agradat?',
	'optin-survey-question-usedtoolbar-ifno' => 'Si és que no, perquè no? Ho fareu en el futur? Justificau-ho, per favor.',
	'optin-survey-question-different' => 'Què podríem haver fet diferent per aconseguir que seguíssiu usant la nostra Beta?',
	'optin-survey-question-feedback' => 'Per favor, deixau-nos qualsevol altre tipus de comentaris o idees que tengueu pel projecte, totes les contribucions ens seran de molta utilitat!',
	'optin-survey-question-browser' => 'Quin navegador emprau?',
	'optin-survey-answer-browser-other' => 'Un altre navegador:',
	'optin-survey-question-os' => 'Quin sistema operatiu usau?',
	'optin-survey-answer-os-other' => 'Un altre sistema operatiu:',
	'optin-survey-question-res' => 'Quina és la resolució de la vostra pantalla?',
	'optin-survey-question-changes' => 'Quins canvis us agradaria veure a Beta en el futur (triau-ne els que volgueu)?',
	'optin-survey-answer-changes-nav' => "Fer la navegació del web més fàcil d'usar.",
	'optin-survey-answer-changes-edittools' => "Augmentar i millorar les eines d'edició.",
	'optin-survey-answer-changes-upload' => 'Millorar els continguts i les càrregues de material multimèdia.',
	'optin-survey-answer-changes-richtext' => "Desenvolupar eines d'edició similars als editors de text enriquit.",
	'optin-survey-answer-changes-lookfeel' => "Actualitzar l'aparença del lloc.",
	'optin-survey-answer-changes-predictability' => 'Millorar la predictabilitat del rendiment.',
	'optin-survey-answer-changes-custom' => 'Permet una major personalització.',
	'optin-survey-answer-changes-other' => 'Altres:',
	'optin-improvements2' => "== Què ha millorat? ==
[[File:UsabilityToolbar.png|left|link=|Imatge de la barra d'edició millorada]]
[[File:UsabilityDialogs.png|left|link=|Imatge dels nous quadres de diàleg]]
; Barra d'eines millorada
La nova barra d'eines fa que sigui més fàcil d'editar articles.
Icones noves i millorades fan més clar veure les accions que cada eina realitza.
Les opcions desplegables redueixen el desordre a la vegada que es mantenen eines poc usades només un clic de distància.
; Diàleg
En crear enllaços o taules, fent clic a la icona de l'eina, ara s'obre un quadre de diàleg que us ajuda a crear enllaços i taules omplint un senzill formulari.
; I més
Hem introduït una sèrie d'altres millores com la navegació, les pestanyes en cascada, la cerca millorada i molt més.
== Com col·laborar-hi ==
Per saber més sobre la Iniciativa d'Usabilitat, si us plau, visitau el nostre [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative web].<div style=\"clear:both\"></div>",
);

/** Chechen (Нохчийн)
 * @author Sasan700
 */
$messages['ce'] = array(
	'optin-accept-long' => 'Хьажа жим гlоле-тайпанара муха ю',
	'optin-accept-long-anon' => 'Дlавазлой хьажа муха ю жим гlоле-тайпанар юкъардакъанга',
);

/** Sorani (Arabic script) (‫کوردی (عەرەبی)‬)
 * @author Asoxor
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
	'optin-survey-question-os' => 'کام سیستەمی کارپێکردن بەکاردێنی؟',
	'optin-survey-answer-os-other' => 'سیستەمی بەکارھێنانی دیکە:',
	'optin-survey-question-res' => 'رەزۆلوشنی شاشەکەت چەندە؟',
	'optin-survey-question-changes' => 'حەز دەکەی کام گۆڕانکاریانە لە داھاتوودا لە بیتادا ببینی؟ (تکایە گشت ئەو شتانەی پێت باشن ھەڵبژێرە)',
);

/** Czech (Česky)
 * @author Mormegil
 */
$messages['cs'] = array(
	'optin' => 'Přihlášení se do Iniciativy použitelnosti',
	'optin-desc' => 'Umožňuje uživatelům vyzkoušet Betu, nejnovější vylepšení použitelnosti vytvořená Iniciativou použitelnosti.',
	'optin-title-optedout' => 'Vyzkoušet Betu',
	'optin-title-optedin' => 'Opustit Betu',
	'optin-title-justoptedin' => 'Vítejte v Betě',
	'optin-title-justoptedout' => 'Děkujeme, že jste zkoušeli Betu',
	'optin-title-feedback' => 'Názory na Betu',
	'optin-needlogin' => 'Pokud chcete zkusit Betu, musíte se [$1 přihlásit].',
	'optin-intro' => 'Iniciativa použitelnosti Wikipedie tvrdě pracovala na vylepšení použitelnosti Wikipedie.
Nechcete zkusit naši Betu?',
	'optin-feedback-intro' => 'Děkujeme, že zkoušíte Betu.
Rádi bychom věděli, co si o našem novém rozhraní myslíte, takže oceníme, pokud vyplníte nepovinný průzkum zobrazený níže.',
	'optin-feedback-back' => 'Pokud se nechcete našeho průzkum zúčastnit, můžete se vrátit na stránku $1.',
	'optin-success-in' => 'Nyní se účastníte testování Bety.
Z testování se můžete kdykoli odhlásit kliknutím na odkaz „{{int:optin-leave}}“ v horní části obrazovky.',
	'optin-leave-cancel' => 'Pokud chcete i nadále používat Betu, můžete se vrátit na stránku $1.',
	'optin-success-out' => 'Úspěšně jste opustili Betu.
Kdykoli ji budete moci znovu vyzkoušet kliknutím na odkaz „{{int:optin-try}}“ v horní části obrazovky.',
	'optin-success-feedback' => 'Děkujeme za váš názor!',
	'optin-accept-short' => 'Jdu do toho!',
	'optin-accept-long' => 'Vyzkoušet Betu',
	'optin-accept-long-anon' => 'Přihlásit se a vyzkoušet Betu',
	'optin-deny-short' => 'Ne, díky',
	'optin-deny-long' => 'Vrátit se na předchozí stránku',
	'optin-submit-out' => 'Opustit Betu',
	'optin-submit-feedback' => 'Odeslat názor',
	'optin-survey-yes' => 'Ano',
	'optin-survey-no' => 'Ne',
	'optin-survey-intro' => 'Děkujeme, že zkoušíte Betu.
Rádi bychom věděli, co si o našem novém rozhraní myslíte, takže oceníme, pokud před kliknutím na „[[#leave|{{int:optin-submit-out}}]]“ vyplníte nepovinný průzkum zobrazený níže.',
	'optin-survey-question-likedislike' => 'Co se vám na Betě líbilo? Co se vám na Betě nelíbilo?',
	'optin-survey-question-whyoptout' => 'Proč chcete opustit Betu? (Vyberte všechny relevantní důvody.)',
	'optin-survey-answer-whyoptout-hard' => 'Byla příliš složitá na používání.',
	'optin-survey-answer-whyoptout-didntwork' => 'Nefungovala správně.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Nechovala se předvídatelně.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Nelíbil se mi její vzhled.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Nelíbily se mi nové záložky a rozvržení.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Nelíbil se mi nový panel nástrojů.',
	'optin-survey-answer-whyoptout-other' => 'Jiný důvod:',
	'optin-survey-question-explain' => 'Vysvětlení či doplnění vaší odpovědi na výše uvedenou otázku:',
	'optin-survey-question-techfail' => '{{GRAMMAR:|Zaznamenal|Zaznamenala|Zaznamenali}} jste při používání Bety nějaké technické potíže?',
	'optin-survey-question-techfail-ifyes' => 'Pokud ano, upřesněte:',
	'optin-survey-question-usedtoolbar' => '{{GRAMMAR:|Používal|Používala|Používali}} jste při editaci stránek nový rozbalovací panel nástrojů?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Pokud ano, co se vám na něm líbilo? Co se vám na něm nelíbilo?',
	'optin-survey-question-usedtoolbar-ifno' => 'Pokud ne, proč ne? Zkusíte to někdy v budoucnu? Upřesněte, prosím.',
	'optin-survey-question-different' => 'Co jsme mohli udělat lépe, abyste {{GENDER:|nepřestal|nepřestala|nepřestali}} používat Betu?',
	'optin-survey-question-feedback' => 'Napište nám prosím libovolné další komentáře, starosti či nápady, které nám chcete sdělit – jakákoli zpětná vazba pomůže:',
	'optin-survey-question-browser' => 'Jaký prohlížeč používáte?',
	'optin-survey-answer-browser-other' => 'Jiný prohlížeč:',
	'optin-survey-question-os' => 'Jaký operační systém používáte?',
	'optin-survey-answer-os-other' => 'Jiný operační systém:',
	'optin-survey-question-res' => 'Jaké je rozlišení vaší obrazovky?',
	'optin-survey-question-changes' => 'Jaké změny byste v budoucnu v Betě {{GENDER:|rád viděl|ráda viděla|rádi viděli}} (vyberte všechny relevantní možnosti)?',
	'optin-survey-answer-changes-nav' => 'Celkové zjednodušení navigace po webu',
	'optin-survey-answer-changes-edittools' => 'Rozšíření a vylepšení editačních nástrojů',
	'optin-survey-answer-changes-upload' => 'Vylepšení načítání souborů',
	'optin-survey-answer-changes-richtext' => 'Vytvoření editačních nástrojů podobných textovým editorům',
	'optin-survey-answer-changes-lookfeel' => 'Modernizovat vzhled a chování webu',
	'optin-survey-answer-changes-predictability' => 'Vylepšit předvídatelnost výkonnosti',
	'optin-survey-answer-changes-custom' => 'Umožnění výraznějšího přizpůsobení uživatelům',
	'optin-survey-answer-changes-other' => 'Jiné:',
	'optin-improvements2' => '== Co jsme vylepšili? ==
[[File:UsabilityToolbar.png|left|link=|Snímek obrazovky zobrazující vylepšený editační panel nástrojů]]
[[File:UsabilityDialogs.png|left|link=|Snímek obrazovky nových dialogových oken]]
;Vylepšení panelu nástrojů
Vylepšený panel nástrojů usnadňuje editaci článků.
Nové a vylepšené ikonky zřetelněji označují, co který nástroj dělá.
Díky rozbalovacím částem je panel přehlednější, přitom méně používané nástroje jsou dostupné jediným kliknutím.
;Dialog
Při vytváření odkazů nebo tabulek se kliknutím na ikonku nástroje otevře dialogové okno, které vám prostřednictvím jednoduchého formuláře pomůže s tvorbou odkazů a tabulek.
;A další
Připravili jsme mnoho dalších vylepšení, jako jsou zdokonalená navigace, kaskádové záložky, vylepšené hledání a jiné.
== Jak se zapojit ==
Pokud se chcete o Iniciativě použitelnosti dozvědět více, navštivte [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative náš web].
<div style="clear:both"></div>',
);

/** Church Slavic (Словѣ́ньскъ / ⰔⰎⰑⰂⰡⰐⰠⰔⰍⰟ)
 * @author ОйЛ
 */
$messages['cu'] = array(
	'optin-title-optedout' => '⁖ β ⁖ искоушє́ниѥ',
	'optin-survey-yes' => 'да',
	'optin-survey-no' => 'нѣ́тъ',
);

/** Welsh (Cymraeg)
 * @author Lloffiwr
 */
$messages['cy'] = array(
	'optin' => "Ymuno â'r Cynllun Defnyddioldeb",
	'optin-desc' => "Yn gadael i ddefnyddwyr arbrofi gyda'r fersiwn Beta, sy'n cynnwys gwelliannau diweddaraf y Cynllun Defnyddioldeb.",
	'optin-title-optedout' => 'Rhowch gynnig ar Beta',
	'optin-title-optedin' => 'Gadael Beta',
	'optin-title-justoptedin' => 'Croeso i Beta',
	'optin-title-justoptedout' => 'Diolch ichi am roi cynnig ar Beta',
	'optin-title-feedback' => 'Adborth Beta',
	'optin-needlogin' => 'Rhaid [$1 mewngofnodi] er mwyn rhoi cynnig ar Beta.',
	'optin-intro' => "Y mae Wikipedia Usability Initiative wedi bod yn ymdrechu i wneud Wikipedia'n haws i'w ddefnyddio.
A hoffech chi roi cynnig ar ein fersiwn Beta?",
	'optin-feedback-intro' => "Diolch ichi am roi cynnig ar Beta.
Fe hoffem gael eich barn ar ein rhyngwyneb newydd, a byddem yn ddiolchgar petaech yn cwblhau'r arolwg dewisol isod.",
	'optin-feedback-back' => "Os nad ydych am gwblhau'r arolwg, gallwch ddychwelyd at $1.",
	'optin-success-in' => 'Rydych nawr yn arbrofi gyda Beta.
Gallwch adael y fersiwn Beta unrhyw bryd drwy wasgu\'r cyswllt "{{int:optin-leave}}" ar frig y dudalen.',
	'optin-leave-cancel' => 'Os ydych am barhau i ddefnyddio Beta, gallwch ddychwelyd at y dudalen $1.',
	'optin-success-out' => 'Rydych wedi gadael y fersiwn Beta.
Gallwch arbrofi eto gyda Beta trwy wasgu\'r cyswllt "{{int:optin-try}}" ar frig y dudalen.',
	'optin-success-feedback' => 'Diolch am eich adborth!',
	'optin-accept-short' => 'Awn ati!',
	'optin-accept-long' => 'Rhoi cynnig ar Beta',
	'optin-accept-long-anon' => 'Mewngofnodi ac arbrofi gyda Beta',
	'optin-deny-short' => 'Dim diolch',
	'optin-deny-long' => 'Dychweler at y dudalen gynt',
	'optin-submit-out' => 'Gadael Beta',
	'optin-submit-feedback' => 'Anfoner yr adborth',
	'optin-survey-yes' => 'Ydw',
	'optin-survey-no' => 'Nacydw',
	'optin-survey-intro' => "Diolch ichi am roi cynnig ar Beta.
Hoffem gael eich barn am y rhyngwyneb newydd, a dyma gyfle i chi fynegi'ch barn drwy gwblhau'r holiadur dewisol sy'n dilyn, cyn i chi bwyso'r botwm \"[[#leave|{{int:optin-submit-out}}]]\".",
	'optin-survey-question-whyoptout' => "Pam ydych chi'n gadael Beta? (Dewiswch pob rheswm perthnasol.)",
	'optin-survey-answer-whyoptout-hard' => "Roedd yn rhy annodd i'w ddefnyddio.",
	'optin-survey-answer-whyoptout-didntwork' => "Doedd e ddim yn gweithio'n iawn.",
	'optin-survey-answer-whyoptout-notpredictable' => "Roedd yn gweithredu'n fympwyol.",
	'optin-survey-answer-whyoptout-didntlike-look' => 'Doeddwn i ddim yn hoffi golwg Beta.',
	'optin-survey-answer-whyoptout-didntlike-layout' => "Doeddwn i ddim yn hoffi'r tabiau a'r gosodiad newydd.",
	'optin-survey-answer-whyoptout-didntlike-toolbar' => "Doeddwn i ddim yn hoffi'r bar offer newydd.",
	'optin-survey-answer-whyoptout-other' => 'Rheswm arall:',
	'optin-survey-question-techfail' => 'Ydych chi wedi dioddef unrhyw broblemau technegol wrth ddefnyddio Beta?',
	'optin-survey-question-usedtoolbar' => "Ydych chi wedi defnyddio'r bar offer ehangadwy i olygu tudalen?",
	'optin-survey-question-browser' => "Pa borwr gwe ydych chi'n ei ddefnyddio?",
	'optin-survey-answer-browser-other' => 'Porwr arall:',
	'optin-survey-question-os' => "Pa system weithredu ydych chi'n ei defnyddio?",
	'optin-survey-answer-os-other' => 'System weithredu arall:',
	'optin-survey-question-res' => "Pa gydraniad sydd i'ch sgrin?",
	'optin-survey-question-changes' => "Pa newidiadau hoffech chi weld yn fersiwn Beta'r dyfodol (dewiswch y cwbl yr hoffech eu gweld)?",
	'optin-survey-answer-changes-nav' => "Gwneud yr offer llywio ar y wefan yn haws i'w defnyddio.",
	'optin-survey-answer-changes-edittools' => "Cynyddu a gwella'r offer golygu.",
	'optin-survey-answer-changes-upload' => 'Gwella uwchlwytho ffeiliau lluniau ag ati.',
	'optin-survey-answer-changes-richtext' => "Datblygu offer golygu tebyg i'r offer ''rich-text''.",
	'optin-survey-answer-changes-lookfeel' => 'Diweddaru "naws a steil" y safle.',
	'optin-survey-answer-changes-predictability' => 'Gwneud iddo weithredu yn y modd mwyaf dibynadwy posib.',
	'optin-survey-answer-changes-custom' => "Ehangu'r nifer o ddewisiadau y gall defnyddiwr eu gosod fel ag a myn.",
	'optin-improvements2' => "== Beth sydd wedi gwella? ==
[[File:UsabilityToolbar.png|left|link=|Ciplun o'r bar offer golygu estynedig]]
[[File:UsabilityDialogs.png|left|link=|Ciplun o'r blychau deialog ar gyfer cynhyrchu cynnwys tudalennau]]
;Bar offer golygu estynedig
Mae'n haws golygu gyda'r bar offer golygu estynedig.
Mae eiconau newydd a gwell yn egluro'n well yr hyn mae'r offer yn gwneud.
Mae'r offer wedi eu rhannu rhwng adrannau sydd yn gallu cael eu crebachu a'u ehangu gydag un clic, yn ôl y galw.
;Blychau deialog
Wrth wneud cysylltau neu dablau, gallwch bwyso ar yr eicon offer a bydd blwch deialog yn agor. Gallwch osod cysylltau a thablau drwy lenwi ffurflen syml yn y blwch.
;A Rhagor
Rydym wedi gwneud gwelliannau eraill megis llywio estynedig, tabiau rhaeadrol, gwelliannau wrth chwilio, a rhagor.
== Am gyfrannu? ==
I gael rhagor o wybodaeth am y Cynllun Defnyddioldeb, dewch draw i'n [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative hafan] ni.
<div style=\"clear:both\"></div>",
);

/** Danish (Dansk)
 * @author Byrial
 * @author Masz
 */
$messages['da'] = array(
	'optin' => 'Deltag i initiativet til brugervenlighed',
	'optin-desc' => 'Giver brugere mulighed for at prøve Beta, Wikipedias initiativ til brugervenligheds seneste forbedringer af brugervenligheden.',
	'optin-title-optedout' => 'Prøv Beta',
	'optin-title-optedin' => 'Forlad Beta',
	'optin-title-justoptedin' => 'Velkommen til Beta',
	'optin-title-justoptedout' => 'Tak for at prøve vores Beta',
	'optin-title-feedback' => 'Tilbagemelding om Beta',
	'optin-needlogin' => 'Du skal [$1 logge på] for at prøve Beta.',
	'optin-intro' => 'Wikipedias initiativ til brugervenlighed har arbejdet hårdt for at forbedre Wikipedias brugervenlighed.
Har du lyst til at prøve vores Beta?',
	'optin-feedback-intro' => 'Tak for at du prøver Beta.
Vi vil gerne vide hvad du mener om vores nye grænseflade, så vi vil sætte pris på det hvis du udfylder den frivillige undersøgelse herunder.',
	'optin-feedback-back' => 'Hvis du ikke vil udfylde undersøgelsen, kan du gå tilbage til $1.',
	'optin-success-in' => 'Du prøver nu Beta.
Du kan når som helst vælge at gå tilbage ved at klikke på "{{int:optin-leave}}"-henvisningen øverst på siden.',
	'optin-leave-cancel' => 'Hvis du vil fortsætte med at bruge Beta, kan du gå tilbage til $1.',
	'optin-success-out' => 'Du har nu forladt Beta.
Du kan til hver en tid prøve det igen ved at klikke på "{{int:optin-try}}"-henvisningen øverst på siden.',
	'optin-success-feedback' => 'Tak for din tilbagemelding!',
	'optin-accept-short' => 'Gør det!',
	'optin-accept-long' => 'Prøv Beta',
	'optin-accept-long-anon' => 'Log på og prøv Beta',
	'optin-deny-short' => 'Nej tak',
	'optin-deny-long' => 'Gå tilbage til den forrige side',
	'optin-submit-out' => 'Forlad Beta',
	'optin-submit-feedback' => 'Giv en tilbagemelding',
	'optin-survey-yes' => 'Ja',
	'optin-survey-no' => 'Nej',
	'optin-survey-intro' => 'Tak for at du har prøvet Beta.
Vi vil gerne vide hvad du synes om vores nye grænseflade, så vi vil sætte pris på det hvis du udfylder den frivillige undersøgelse herunder før du trykker på "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Hvad kunne lide ved Beta? Hvad kunne du ikke lide ved Beta?',
	'optin-survey-question-whyoptout' => 'Hvorfor forlader du Beta? (Vælg venligst alle som passer).',
	'optin-survey-answer-whyoptout-hard' => 'Den var for vanskelig at bruge.',
	'optin-survey-answer-whyoptout-didntwork' => 'Den fungerede ikke ordentligt.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Den virkede uforudsigelig.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Jeg kunne ikke lide udseendet.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Jeg kunne ikke lide de nye faneblade og layoutet.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Jeg kunne ikke lide den nye værktøjslinje.',
	'optin-survey-answer-whyoptout-other' => 'Anden grund:',
	'optin-survey-question-explain' => 'Forklar eller uddyb gerne dit svar herover:',
	'optin-survey-question-techfail' => 'Oplevede du nogen tekniske fejl mens du brugte Beta?',
	'optin-survey-question-techfail-ifyes' => 'Hvis ja, uddyb eller forklar venligst:',
	'optin-survey-question-usedtoolbar' => 'Har du brugt den nye udvidelige værktøjslinje til at redigere en side?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Hvis ja, hvad kunne lide ved den? Hvad kunne du ikke lide ved den?',
	'optin-survey-question-usedtoolbar-ifno' => 'Hvis nej, hvorfor ikke? Vil du gøre det på et senere tidspunkt? Forklar veligst.',
	'optin-survey-question-different' => 'Hvad kunne vi have gjort anderledes for at få dig til at fortsætte med at bruge Beta?',
	'optin-survey-question-feedback' => 'Fortæl os venligst alle andre kommentarer, overvejelser eller tanker som du har til os - enhver tilbagemelding er nyttig:',
	'optin-survey-question-browser' => 'Hvilken browser bruger du?',
	'optin-survey-answer-browser-other' => 'Anden browswer:',
	'optin-survey-question-os' => 'Hvilket styresystem bruger du?',
	'optin-survey-answer-os-other' => 'Andet styresystem:',
	'optin-survey-question-res' => 'Hvor stor er din skærmopløsning?',
	'optin-survey-question-changes' => 'Hvilke foranndringer kunne du tænke dig at se i Beta fremover (vælg venligst alle som passer)?',
	'optin-survey-answer-changes-nav' => 'Gør den generelle navigering på websiderne nemmere at bruge.',
	'optin-survey-answer-changes-edittools' => 'Udbyg og forbedr redigeringsværktøjerne.',
	'optin-survey-answer-changes-upload' => 'Forbedr oplægning af indhold og media.',
	'optin-survey-answer-changes-richtext' => 'Udvikl redigeringsværktøjer som ligner tekstbehandlingsprogrammer.',
	'optin-survey-answer-changes-lookfeel' => 'Opdater websidernes udseende og virkemåde.',
	'optin-survey-answer-changes-predictability' => 'Forbedr forudsigeligheden af virkemåden.',
	'optin-survey-answer-changes-custom' => 'Giv mulighed for flere individuelle tilpasninger.',
	'optin-survey-answer-changes-other' => 'Andet:',
	'optin-improvements2' => '== Hvad er blevet forbedret? ==
[[File:UsabilityToolbar.png|left|link=|Skærmbillede af den forbedrede redigeringsværktøjslinje]]
[[File:UsabilityDialogs.png|left|link=|Skærmbillede af den nye indholdsgenereringsdialog]]
;Forbedret værktøjslinje
Den nye, forbedrede værktøjslinje gør det nemmere for dig at redigere artikler.
Nye og forbedrede ikoner gør det mere klart for dig, hvilke ting du udfører.
Udvidede sektioner reducerer rod ved at holde sjældent brugte værktøjer bare ét klik væk.
;Dialoger
Når du tilføjer henvisninger eller tabeller ved at klikke på værktøjsikonen, åbnes der en dialogboks der hjælper dig med at tilføje dem ved bare at udfylde en simpel formular.
;Og mere
Vi har også introduceret en del andre forbedringer som f.eks. forbedret navigation, skjulbare faner, forbedret søgning og mere.
== Hvordan bliver jeg inddraget? ==
For at finde mere om Brugeroplevelsen, besøg [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative vores hjemmeside].
<div style="clear:both"></div>',
);

/** German (Deutsch)
 * @author Als-Holder
 * @author Benzen
 * @author Imre
 * @author Metalhead64
 * @author Pill
 * @author Umherirrender
 */
$messages['de'] = array(
	'optin' => 'Anmeldung bei der Benutzerfreundlichkeitsinitiative',
	'optin-desc' => 'Erlaubt Benutzern, Beta auszuprobieren, aktuellste Benutzerfreundlichkeitsverbesserungen der Benutzerfreundlichkeitsinitiative',
	'optin-title-optedout' => 'Beta ausprobieren',
	'optin-title-optedin' => 'Beta verlassen',
	'optin-title-justoptedin' => 'Willkommen bei Beta',
	'optin-title-justoptedout' => 'Vielen Dank, dass du Beta ausprobierst',
	'optin-title-feedback' => 'Feedback geben',
	'optin-needlogin' => 'Du musst dich [$1 anmelden], um Beta auszuprobieren',
	'optin-intro' => 'Die Wikipedia-Benutzerfreundlichkeitsinitiative hat hart daran gearbeitet, die Benutzerfreundlichkeit von Wikipedia zu verbessern.
Möchtest du jetzt die Betaversion ausprobieren?',
	'optin-feedback-intro' => 'Danke, dass du Beta ausprobierst.
Wir möchten gerne erfahren, was du über unsere neue Benutzeroberfläche denkst und würden uns freuen, wenn du an der folgenden, freiwilligen Umfrage teilnimmst.',
	'optin-feedback-back' => 'Wenn du die Umfrage nicht ausfüllen willst, kannst du zu $1 zurückkehren.',
	'optin-success-in' => 'Du probierst gerade Beta aus.
Du kannst es jederzeit verlassen, indem du den Link „{{int:optin-leave}}“ oben anklickst.',
	'optin-leave-cancel' => 'Wenn du Beta weiter verwenden willst, kannst du zu $1 zurückkehren.',
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
	'optin-survey-yes' => 'Ja',
	'optin-survey-no' => 'Nein',
	'optin-survey-intro' => 'Danke, dass du Beta ausprobierst.
Wir möchten gerne erfahren, was du über unsere neue Benutzeroberfläche denkst. Wir begrüßen es, wenn du die optionale Umfrage unten beantwortest, bevor du auf „[[#leave|{{int:optin-submit-out}}]]“ klickst.',
	'optin-survey-question-likedislike' => 'Was magst du an Beta? Was magst du nicht an Beta?',
	'optin-survey-question-whyoptout' => 'Warum verlässt du diesen Betatest? (bitte alles Zutreffende auswählen)',
	'optin-survey-answer-whyoptout-hard' => 'Die Verwendung war zu kompliziert.',
	'optin-survey-answer-whyoptout-didntwork' => 'Es funktioniert nicht einwandfrei.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Es funktioniert nicht in vorhersehbarer Weise.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Ich mag das Aussehen nicht.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Ich mag die neuen Tabs und das Layout nicht.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Ich mag die neue Werkzeugleiste nicht.',
	'optin-survey-answer-whyoptout-other' => 'Anderer Grund:',
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
	'optin-survey-question-changes' => 'Welche Änderungen an der Beta würdest du gerne in Zukunft sehen (wähle alles Zutreffende aus)?',
	'optin-survey-answer-changes-nav' => 'Die Gesamtnavigation der Site einfacher machen.',
	'optin-survey-answer-changes-edittools' => 'Bearbeitungswerkzeuge erweitern und verbessern.',
	'optin-survey-answer-changes-upload' => 'Inhalt und Medienuploads verbessern.',
	'optin-survey-answer-changes-richtext' => 'Bearbeitungswerkzeuge ähnlich wie bei Rich-Text-Editoren entwickeln.',
	'optin-survey-answer-changes-lookfeel' => 'Erscheinungsbild der Site aktualisieren.',
	'optin-survey-answer-changes-predictability' => 'Vorhersehbarkeit der Leistung verbessern.',
	'optin-survey-answer-changes-custom' => 'Größere Benutzeranpassungen erlauben.',
	'optin-survey-answer-changes-other' => 'Andere:',
	'optin-improvements2' => '== Was wurde verbessert? ==
[[File:UsabilityToolbar.png|left|link=|Screenshot der erweiterten Werkzeugleiste]]
[[File:UsabilityDialogs.png|left|link=|Screenshot der neuen Dialogbox]]
;Erweiterte Werkzeugleiste
Die erweiterte Werkzeugleiste vereinfacht die Bearbeitung von Seiten.
Neue und verbesserte Symbole zeigen deutlicher, welches Symbol für welche Aktion steht.
Ausklappbare Bereiche reduzieren die Unübersichtlichkeit, indem selten benötigte Werkzeuge nur einen Klick entfernt vorhanden sind.
;Dialoge
Bei der Erstellung von Links oder Tabellen wird bei einem Klick auf das Symbol eine Dialogbox geöffnet, die ein einfach ausfüllbares Formular enthält.
;Und noch mehr
Wir haben eine Vielzahl von weiteren Verbesserungen im Bearbeitungsfenster integriert, wie zum Beispiel die erweitere Navigation und die verbesserte Suche.
== Weitere Informationen ==
Besuche die [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative Homepage der Usability Initiative], um noch mehr zu erfahren.',
);

/** German (formal address) (Deutsch (Sie-Form))
 * @author Imre
 * @author Umherirrender
 */
$messages['de-formal'] = array(
	'optin-title-justoptedout' => 'Vielen Dank, dass Sie Beta ausprobieren',
	'optin-needlogin' => 'Sie müssen sich [$1 anmelden], um Beta auszuprobieren',
	'optin-intro' => 'Die Wikipedia-Benutzerfreundlichkeitsinitiative hat hart daran gearbeitet, die Benutzerfreundlichkeit von Wikipedia zu verbessern.
Möchten Sie jetzt die Betaversion ausprobieren?',
	'optin-feedback-intro' => 'Danke, dass Sie Beta ausprobieren.
Wir möchten gerne erfahren, was Sie über unsere neue Benutzeroberfläche denken und würden uns freuen, wenn Sie an der folgenden, freiwilligen Umfrage teilnehmen.',
	'optin-feedback-back' => 'Wenn Sie die Umfrage nicht ausfüllen wollen, können Sie zu $1 zurückkehren.',
	'optin-success-in' => 'Sie probieren gerade Beta aus.
Sie können es jederzeit verlassen, indem Sie den Link „{{int:optin-leave}}“ oben anklicken.',
	'optin-leave-cancel' => 'Wenn Sie Beta weiter verwenden wollen, können Sie zu $1 zurückkehren.',
	'optin-success-out' => 'Sie haben Beta erfolgreich verlassen.
Sie können es jederzeit wieder ausprobieren, indem Sie den Link „{{int:optin-try}}“ oben rechts anklicken.',
	'optin-success-feedback' => 'Danke für Ihr Feedback!',
	'optin-survey-intro' => 'Danke, dass Sie Beta ausprobieren.
Wir möchten gerne erfahren, was Sie über unsere neue Benutzeroberfläche denken. Wir begrüßen es, wenn Sie die optionale Umfrage unten beantworten, bevor Sie auf „[[#leave|{{int:optin-submit-out}}]]“ klicken.',
	'optin-survey-question-likedislike' => 'Was mögen Sie an Beta? Was mögen Sie nicht an Beta?',
	'optin-survey-question-whyoptout' => 'Warum verlassen Sie diesen Betatest? (bitte alles Zutreffende auswählen)',
	'optin-survey-question-explain' => 'Bitte erklären Sie Ihre obige Antwort oder arbeiten Sie sie aus:',
	'optin-survey-question-techfail' => 'Haben Sie beim Ausprobieren von Beta irgendwelche technischen Fehler entdeckt?',
	'optin-survey-question-usedtoolbar' => 'Verwenden Sie die neue, expandierbare Bearbeitungs-Werkzeugleiste?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Falls ja, was mögen Sie daran? Was mögen Sie nicht daran?',
	'optin-survey-question-usedtoolbar-ifno' => 'Falls nicht, warum? Werden Sie es in Zukunft tun? Bitte erklären Sie es.',
	'optin-survey-question-different' => 'Was können wir anders machen, damit Sie unseren Betatest weiter verwenden?',
	'optin-survey-question-feedback' => 'Bitte geben Sie uns Ihr Feedback:',
	'optin-survey-question-browser' => 'Welchen Browser verwenden Sie?',
	'optin-survey-question-os' => 'Welches Betriebssystem verwenden Sie?',
	'optin-survey-question-res' => 'Was ist Ihre Bildschirmauflösung?',
	'optin-survey-question-changes' => 'Welche Änderungen an der Beta würden Sie gerne in Zukunft sehen (wählen Sie alles Zutreffende aus)?',
);

/** Zazaki (Zazaki)
 * @author Aspar
 */
$messages['diq'] = array(
	'optin' => 'Kullanılabilirlik Girişimi katılım',
	'optin-desc' => 'teselkerdışê /cerebnayişê şuxulnayişê betayi re destur dano u verdeno dewlemend kero',
	'optin-title-optedout' => 'Beta tesel bıker/bıcerebın',
	'optin-title-optedin' => 'Beta ra vec',
	'optin-title-justoptedin' => 'şıma xeyr ameyî Betayi',
	'optin-title-justoptedout' => 'şıma Beta tesel kerd/cerebna, ma zaf tşk keni',
	'optin-title-feedback' => 'feedback bıde',
	'optin-needlogin' => "qey cerebnayişê Beta'yi [$1 şıma gani cıkewê].",
	'optin-intro' => 'Wikipediya zaf xebıtyena qey aver şiyayişi.
şıma qayili Beta tesel bıkeri / bıcerebi?',
	'optin-feedback-intro' => 'qey cerebnayişê Betayi ma tşk keni.
Cêr de anketê ma esto, Beta ser çı pêşniyazê şıma  esto ma re bışawe.',
	'optin-feedback-back' => 'eke şıma qayil niyê anket de keri şıma eşkeni agêrê no pel $1',
	'optin-success-in' => 'Şıma nıka Beta cerebneni.
şıma key qayil bi tera bıveci, ser ekran de gureyê "{{int:optin-leave}}"yi bıtıknê u bıvecê.',
	'optin-leave-cancel' => 'eke şıma qayili Beta bışuxulnî tede bımanî, şıma eşkeni agêri no $1 peli',
	'optin-success-out' => 'şıma beta ra bı serkewte veciyê.
serê ekrani de hetê raşt de gıreyê "{{int:optin-try}}" i esto, şıma key qayil bi dekewe Betayi tiya bnıtıknê.',
	'optin-success-feedback' => 'qey feedbacki ma tşk keni',
	'optin-accept-short' => 'Hayde bêrê ma bıkerî',
	'optin-accept-long' => 'Try Beta bıcerebın/tesel bıker',
	'optin-accept-long-anon' => 'cıkewê u Beta ışuxulnê',
	'optin-deny-short' => 'nê tşk kena',
	'optin-deny-long' => 'agêr pelo verin',
	'optin-submit-out' => 'Beta ra vec',
	'optin-submit-feedback' => 'feedback bıde',
	'optin-survey-yes' => 'e/belê/ya/heya',
	'optin-survey-no' => 'nê',
	'optin-survey-intro' => 'qey cerebnayişê Betayi ma tşk keni.
Beta ser çı pêşniyazê şıma  esto ma re bışawe. seba anketi:
 "[[#leave|{{int:optin-submit-out}}]]"',
	'optin-survey-question-likedislike' => 'şıma çıtayê/çiyê Betayi ra hes keni? çıtayê ey ra hes nêkeni?',
	'optin-survey-question-whyoptout' => 'şıma çıra Beta ra veceni? (kerem kerê çıtayê/çiyê ey şıma re weş o, nişane bıkerê)',
	'optin-survey-answer-whyoptout-hard' => 'xebıtnayiş zaf zehmet o',
	'optin-survey-answer-whyoptout-didntwork' => 'raşt/rınd nêxebıtyena',
	'optin-survey-answer-whyoptout-notpredictable' => 'qeydeyek o xebıtyena, kes nêzano texmin bıkero',
	'optin-survey-answer-whyoptout-didntlike-look' => 'aseyişê/esayişê aye rınd niya',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'nizam u fesalê aye rınd niyo',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'mı çuyaya haceti ya neweyi ra hes nêkerd',
	'optin-survey-answer-whyoptout-other' => 'sebebo bin:',
	'optin-survey-question-explain' => 'kerem kerê cewabê xo derg u dıla eşkera kerê:',
	'optin-survey-question-techfail' => 'wexta şıma Beta xebıtnayene çı arızaya tekni bı nê nıbı?',
	'optin-survey-question-techfail-ifyes' => 'eke cewabê şıma e/belê yo, kerem kerê derg u dıla eşkera kerê:',
	'optin-survey-question-usedtoolbar' => 'qey vurnayişê yew peli, şıma çuyaya haceti ya dewlemendbiyayeyi çe şuxulna?',
	'optin-survey-question-usedtoolbar-ifyes' => 'eke e/belê yo, şıma çıta/çı ra heskerd, çı ra hes nêkerd?',
	'optin-survey-question-usedtoolbar-ifno' => 'eke nê yo, çıra? şıma reyna xebıtneni, kerem kerê eşkera bıkerê',
	'optin-survey-question-different' => 'ma se bıkerdene şıma dewamê Betayi kerdenê?',
	'optin-survey-question-feedback' => 'feedback bıde:',
	'optin-survey-question-browser' => 'şıma kam yew/kanci cıgêrayoxi şuxulneni?',
	'optin-survey-answer-browser-other' => 'cıgêrayoxê bini:',
	'optin-survey-question-os' => 'şıma kanci/kam yew sistemê xebıtnayiê kompiteri xebıtnenî?',
	'optin-survey-answer-os-other' => 'sitemê xebıtnayişê kompiteri yê bini',
	'optin-survey-question-res' => 'ramotışê (resolution) ekranê şıma seyin o?',
	'optin-survey-question-changes' => 'Şıma Beta de kam yew/kanci vurnayişani qayili bıvini (çıta yo/çi yo ke şıma re munasib o nişan bıkerê)?',
	'optin-survey-answer-changes-nav' => 'tedegêrayişê keyepeli rehet/asan kerê',
	'optin-survey-answer-changes-edittools' => 'hacetê nizamdayişi ziyed kerê u dewlemend kerê',
	'optin-survey-answer-changes-upload' => 'bakerdışê medyayi dewlemend kerê',
	'optin-survey-answer-changes-richtext' => 'Develop editing tools similar to rich-text editors.',
	'optin-survey-answer-changes-lookfeel' => 'esayişê keyepeli rocane kerê',
	'optin-survey-answer-changes-predictability' => 'performans o ke texmin beno, dewlemend kerê',
	'optin-survey-answer-changes-custom' => 'xususi kerdışê karberani re destur bıdi',
	'optin-survey-answer-changes-other' => 'ey bin:',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'optin' => 'Iniciatiwa wužywajobnosći wubraśe',
	'optin-desc' => 'Wužywarjam dowóliś Betu wopytaś, slědne pólěpšenja wužywajobnosći iniciatiwy wužywajobnosći',
	'optin-title-optedout' => 'Betu wopytaś',
	'optin-title-optedin' => 'Betu spušćiś',
	'optin-title-justoptedin' => 'Witaj do Bety',
	'optin-title-justoptedout' => 'Źěkujomy se, až sy wopytał Betu',
	'optin-title-feedback' => 'Powěsći wó Beśe',
	'optin-needlogin' => 'Musyš se [$1 pśizjawiś], aby Betu wopytał.',
	'optin-intro' => 'Iniciatiwa wužywajobnosći Wikipedije jo južo žurnje źěłała, aby pólěpšyła wužywajobnosć Wikipedije. By ty rady Betu wopytaś?',
	'optin-feedback-intro' => 'Źěkujomy se, až sy Betu wopytał.
My by rady wěźeli, což mysliš wó našem nowem pówjerchu, togodla my by se wjaselili, jolic wupołnjujoš slědujucy dobrowólny formular.',
	'optin-feedback-back' => 'Jolic njocoš formular wupołniś, móžoš se k slědujucemu bokoju wrośiś: $1.',
	'optin-success-in' => 'Wopytujoš něnto Betu.
Móžoš kuždy cas pśez kliknjenje na wótkaz "{{int:optin-leave}}" górjejce na wobrazowce wótwóliś.',
	'optin-leave-cancel' => 'Jolic coš Betu dalej wužywaś, móžoš se k slědujucemu bokoju wrośiś: $1.',
	'optin-success-out' => 'Sy wuspěšnje Betu spušćił.
Móžoš ju hyšći raz kuždy cas pśez kliknjenje na wótkaz "{{int:optin-try}}" górjejce napšawo na wobrazowce wopytaś.',
	'optin-success-feedback' => 'Žěkujomy se za twójo wótegrono!',
	'optin-accept-short' => 'Něnt ale zasobu!',
	'optin-accept-long' => 'Betu wopytaś',
	'optin-accept-long-anon' => 'Pśizjawiś se a Betu wopytaś',
	'optin-deny-short' => 'Ně, źěkujomy se',
	'optin-deny-long' => 'Slědk k slědnemu bokoju',
	'optin-submit-out' => 'Betu spušćiś',
	'optin-submit-feedback' => 'Powěsć daś',
	'optin-survey-yes' => 'Jo',
	'optin-survey-no' => 'Ně',
	'optin-survey-intro' => 'Źěkujomy se za wopytanje Bety.
My by rady wěźeli, co mysliš wó našem nowem wužywarskem pówjerchu, togodla my by se wjaselili, jolic ty by wupołnił opcionalny napšašnik dołojce, nježli až kliknjoš na "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Co se śi na Beśe spódoba? Co se śi na Beśe njespódoba?',
	'optin-survey-question-whyoptout' => 'Cogodla spušćaś Betu? (Pšosym wubjeŕ wšykne, kótarež pśitrjefje.)',
	'optin-survey-answer-whyoptout-hard' => 'Wužywanje jo było pśekomplicěrowane.',
	'optin-survey-answer-whyoptout-didntwork' => 'Njejo pórědnje funkcioněrowało.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Njefunkcioněrujo na pśedwiźony nałog.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Naglěd se mě njespódoba.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Nowe rejtarki a layout se mi njespódobaju.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Nowa rědowa kšoma se mi njespódoba.',
	'optin-survey-answer-whyoptout-other' => 'Druga pśicyna:',
	'optin-survey-question-explain' => 'Pšosym wujasni abo wuźěłaj swójo górjejcne wótegrono:',
	'optin-survey-question-techfail' => 'Sy na techniske zmólki starcył, gaž sy Betu wužył?',
	'optin-survey-question-techfail-ifyes' => 'Jolic jo, pšosym wuźěłaj abo wujasni:',
	'optin-survey-question-usedtoolbar' => 'Sy nowu wócynjajobnu rědowu kšomu wužył, aby bok wobźěłał?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Jolic jo, co se śi na tom spódoba? Co se śi na tom njespódoba?',
	'optin-survey-question-usedtoolbar-ifno' => 'Jolic ně, cogodla nic? Buźoš to w pśichoźe cyniś? Pšosym rozjasni.',
	'optin-survey-question-different' => 'Co by my mógli hyšći hynac cyniś, aby ty dalej wužywaš Betu?',
	'optin-survey-question-feedback' => 'Pšosym daj nam swóje měnjenja k wěsći:',
	'optin-survey-question-browser' => 'Kótary wobglědowak wužywaš?',
	'optin-survey-answer-browser-other' => 'Drugi wobglědowak:',
	'optin-survey-question-os' => 'Kótary źěłowy system wužywaš?',
	'optin-survey-answer-os-other' => 'Drugi źěłowy system:',
	'optin-survey-question-res' => 'Kótare jo rozeznaśe twójeje wobrazowki?',
	'optin-survey-question-changes' => 'Kótare změny by ty rady wiźeł w Beśe w pśichoźe (pšosym wubjeŕ wšykne, kótarež pśitrjefje)?',
	'optin-survey-answer-changes-nav' => 'Cełkownu nawigaciju sedła zjadnoriś.',
	'optin-survey-answer-changes-edittools' => 'Wobźěłowańske rědy rozšyriś a pólěpšyś.',
	'optin-survey-answer-changes-upload' => 'Wopśimjeśe a nagraśa medijow pólěpšyś.',
	'optin-survey-answer-changes-richtext' => 'Wobźěłowańske rědy wuwiś, kótarež su pódobne na editory za rich tekst.',
	'optin-survey-answer-changes-lookfeel' => 'Wenkowny naglěd sedła aktualizěrowaś',
	'optin-survey-answer-changes-predictability' => 'Pśedpowědajobnosć wugbaśa pólěpšyś.',
	'optin-survey-answer-changes-custom' => 'Wětše wužywarske pśiměrjenja dowóliś',
	'optin-survey-answer-changes-other' => 'Druge:',
	'optin-improvements2' => '== Co jo se pólěpšyło? ==
[[File:UsabilityToolbar.png|left|link=|Foto wobrazowki pólěpšoneje wobźěłowańskeje kšomy]]
[[File:UsabilityDialogs.png|left|link=|Foto wobrazowki nwoych dialogow za napóranje wopśijeśa]]
;Pólěpšona rědowa kšoma
Pólěpšona rědowa kšoma wólažcujo wobźěłowanje nastawkow.
Nowe a pólěpšone wobrazowe symbole wujasnjuju lěpjej, kótaru akciju kuždy rěd wuwjedujo.
Rozłožone wótrězki reducěruju barłog, mjaztym až rědko wužywane rědy su jano jadno kliknjenje zdalone.
;Dialog
Gaž se wótkaze abo tabele napóraju, kliknjenje na rědowy wobrazk něnto wocynja dialogowy kašćik, kótaryž śi pomaga, wótkaze a tabele pśez wupołnjowanje jadnorego formulara napóraś.
;A wěcej
Smy zawjadli cełu kopicu drugich pólěpšenjow, ako na pś. pólěpšonu nawigaciju, kaskaděrowace rejtarki, pólěpšone pytanje a wěcej.
== Kak móžoš sobu cyniś ==
Aby wěcej zgónił wó iniciatiwje wužywajobnosći, woglědaj se pšosym naš
[http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative startowy bok].
<div style="clear:both"></div>',
);

/** Ewe (Eʋegbe)
 * @author Natsubee
 */
$messages['ee'] = array(
	'optin-title-optedout' => 'Zã Beta kpɔ',
	'optin-title-optedin' => 'Ɖe asi le Beta ŋu',
	'optin-title-justoptedin' => 'Woezɔ ɖe Beta me loo',
	'optin-title-justoptedout' => 'Akpe na wò Beta zãzã',
	'optin-title-feedback' => 'Wò susuwo le Beta ŋu',
	'optin-success-out' => 'Wò do le Beta me vɔ azɔ̃.<br />
Wò agateŋu azãe ɣesiaɣi fãa ne wò tia "{{int:optin-try}}" si le axa sia tame.',
);

/** Greek (Ελληνικά)
 * @author Assassingr
 * @author Crazymadlover
 * @author Dead3y3
 * @author Geraki
 * @author Omnipaedista
 * @author ZaDiak
 */
$messages['el'] = array(
	'optin' => 'Ενεργοποίηση πρωτοβουλίας χρηστικότηττας',
	'optin-desc' => 'Επιτρέψτε στους χρήστες να δοκιμάσουν τη Beta, τις τελευταίες βελτιώσεις χρηστικότητας της Πρωτοβουλίας Χρηστικότητας.',
	'optin-title-optedout' => 'Δοκιμάστε την Beta',
	'optin-title-optedin' => 'Εγκαταλείψτε την Beta',
	'optin-title-justoptedin' => 'Καλωσήρθατε στην Beta',
	'optin-title-justoptedout' => 'Σας ευχαριστούμε που δοκιμάσατε την Beta μας',
	'optin-title-feedback' => 'Ανάδραση περί της Beta',
	'optin-needlogin' => 'Πρέπει να [$1 συνδεθείς] για να δοκιμάσεις την Beta',
	'optin-intro' => 'Η Πρωτοβουλία Χρηστικότητας της Wikipedia έχει δουλέψει σκληρά για τη βελτίωση της χρηστικότητας της Wikipedia. 
Θα θέλατε να δοκιμάσετε τη Beta;',
	'optin-feedback-intro' => 'Ευχαριστούμε που δοκιμάσατε την Beta.
Θα θέλαμε να μάθουμε τι νομίζετε για την νέα διεπαφή μας. Θα το εκιμούσαμε αν συμπληρώνατε την προαιρετική φόρμα αξιολόγησης παρακάτω.',
	'optin-feedback-back' => 'Αν δεν θες να συμπληρώσεις τη δημοσκόπηση, μπορείς να επιστρέψεις πίσω στο $1.',
	'optin-success-in' => 'Προσπαθείτε τώρα να δοκιμάσετε τη Beta.
Μπορείτε ανά πάσα στιγμή να εγκαταλείψετε τη συνεδρία κάνωντας κλικ στον σύνδεσμο «{{int:optin-leave}}» στο πάνω μέρος της οθόνης.',
	'optin-leave-cancel' => 'Αν θα θέλατε να συνεχίσετε να χρησιμοποιείτε τη Beta, μπορείτε να επιστρέψετε στο $1.',
	'optin-success-out' => 'Εγκαταλείψατε επιτυχώς τη Beta.
Μπορείτε να τη δοκιμάσετε πάλι ανά πάσα στιγμή κάνοντας κλικ στο σύνδεσμο "{{int:optin-try}}" στο πάνω μέρος της οθόνης.',
	'optin-success-feedback' => 'Σας ευχαριστούμε για την γνώμη σας!',
	'optin-accept-short' => 'Ας το κάνουμε!',
	'optin-accept-long' => 'Δοκιμάστε την Beta',
	'optin-accept-long-anon' => 'Συνδεθείτε και δοκιμάστε το Beta',
	'optin-deny-short' => 'Όχι, ευχαριστώ.',
	'optin-deny-long' => 'Επιστροφή στην προηγούμενη σελίδα',
	'optin-submit-out' => 'Εγκαταλείψτε την Beta',
	'optin-submit-feedback' => 'Δώστε ανάδραση',
	'optin-survey-yes' => 'Ναι',
	'optin-survey-no' => 'Όχι',
	'optin-survey-intro' => 'Ευχαριστούμε για την δοκιμή της Beta.
Θα θέλαμε να ξέρουμε τι πιστεύετε για τη νέα μας διεπαφή, ώστε να μπορέσουμε να το εκτιμήσουμε αν συμπληρώσατε την προαιρετική δημοσκόπηση πριν πατήσετε "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Τι σας άρεσε στη Beta; Τι δεν σας άρεσε στη Beta;',
	'optin-survey-question-whyoptout' => 'Γιατί εγκαταλείπετε τη Beta; (Παρακαλώ επιλέξτε όλα όσα εφαρμόζονται.)',
	'optin-survey-answer-whyoptout-hard' => 'Ήταν δύσχρηστο.',
	'optin-survey-answer-whyoptout-didntwork' => 'Δεν λειτουργούσε σωστά.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Δεν ανταποκρινόταν προβλέψιμα.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Δεν μου άρεσε η εμφάνισή του.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Δεν μου άρεσαν οι νέες καρτέλες και η γενική σχεδιαστική διάταξη.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Δεν μου άρεσε η νέα μπάρα εργαλείων.',
	'optin-survey-answer-whyoptout-other' => 'Άλλος λόγος:',
	'optin-survey-question-explain' => 'Παρακαλώ δώστε μία πληρέστερη εξήγηση της παραπάνω απάντησής σας:',
	'optin-survey-question-techfail' => 'Έχετε τεχνικές δυσκολίες ενώ χρησιμοποιείτε τη Beta;',
	'optin-survey-question-techfail-ifyes' => 'Εάν ναι, παρακαλώ δώστε μία πληρέστερη εξήγηση:',
	'optin-survey-question-usedtoolbar' => 'Χρησιμοποιήσατε τη νέα επεκτάσιμη μπάρα εργασιών για να επεξεργαστείτε μια σελίδα;',
	'optin-survey-question-usedtoolbar-ifyes' => 'Εάν ναι, τι σας άρεσε και τι δεν σας άρεσε σε αυτό;',
	'optin-survey-question-usedtoolbar-ifno' => 'Αν όχι, γιατί όχι; Πρόκειται στο μέλλον; Παρακαλώ εξηγήστε.',
	'optin-survey-question-different' => 'Τι θα μπορούσαμε να κάνουμε διαφορετικό για να συνεχίσετε να χρησιμοποιείτε τη Beta;',
	'optin-survey-question-feedback' => 'Παρακαλώ ενημερώστε μας για οποιαδήποτε σχόλια, παρατηρήσεις ή σκέψεις έχετε για μας - οποιαδήποτε ανάδραση είναι χρήσιμη:',
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
	'optin-survey-question-changes' => 'Τι αλλαγές θα θέλατε να δείτε στη Beta στο μέλλον (παρακαλώ επιλέξτε όλα όσα εφαρμόζονται);',
	'optin-survey-answer-changes-nav' => 'Μετατροπή της όλης πλοήγησης στον ιστότοπο πιο εύχρηστη.',
	'optin-survey-answer-changes-edittools' => 'Αυξήστε και ενισχύστε τα εργαλεία επεξεργασίας.',
	'optin-survey-answer-changes-upload' => 'Βελτίωση περιεχομένου και φορτώσεις μέσων.',
	'optin-survey-answer-changes-richtext' => 'Ανάπτυξη εργαλείων επεξεργασίας παρόμοιων με πρόγραμμα διόρθωσης πλούσιου κειμένου.',
	'optin-survey-answer-changes-lookfeel' => 'Αλλαγή της εμφάνισης του ιστοτόπου.',
	'optin-survey-answer-changes-predictability' => 'Βελτίωση προβλεψιμότητας της απόδοσης.',
	'optin-survey-answer-changes-custom' => 'Επέτρεψε μεγαλύτερη προσαρμογή χρήστη.',
	'optin-survey-answer-changes-other' => 'Άλλο:',
	'optin-improvements2' => '== Τι έχει βελτιωθεί; ==
[[File:UsabilityToolbar.png|left|link=|Στιγμιότυπο της ενισχυμένης γραμμής εργαλείων επεξεργασίας]]
[[File:UsabilityDialogs.png|left|link=|Στιγμιότυπο των νέων διαλόγων δημιουργίας περιεχομένου]]
; Ενισχυμένη Γραμμή Εργαλείων
Η ενισχυμένη γραμμή εργαλείων καθιστά ευκολότερο να επεξεργαστείτε άρθρα.
Νέα και βελτιωμένα εικονίδια καταστούν σαφέστερο ποιες ενέργειες κάθε εργαλείο εκτελεί.
Επεκταμένα τμήματα μειώνουν την σύγχυση, διατηρώντας παράλληλα σπάνια χρησιμοποιούμενα εργαλεία μόνο ένα κλικ μακριά.
; Διάλογος
Κατά τη δημιουργία συνδέσμων ή πινάκων, κάνοντας κλικ στο εικονίδιο του εργαλείου ανοίγει τώρα ένα παράθυρο διαλόγου που σας βοηθά να δημιουργήσετε συνδέσμους και πίνακες με τη συμπλήρωση μιας απλής φόρμας.
; Και Περισσότερα
Έχουμε εισαγάγει μια σειρά άλλων βελτιώσεων, όπως η βελτιωμένη πλοήγηση, επικαλυπτόμενες καρτέλες, βελτιωμένη αναζήτηση και περισσότερα.
== Πώς να λάβετε μέρος ==
Για να μάθετε περισσότερα για την Πρωτοβουλία Χρηστικότητας, παρακαλούμε επισκεφθείτε την [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative αρχική σελίδα] μας.
<div style="clear:both"></div>',
);

/** British English (British English)
 * @author Reedy
 */
$messages['en-gb'] = array(
	'optin-survey-answer-changes-custom' => 'Allow greater user customisation.',
);

/** Esperanto (Esperanto)
 * @author ArnoLagrange
 * @author Yekrats
 */
$messages['eo'] = array(
	'optin' => 'Nedeviga iniciato pri uzebleco',
	'optin-desc' => 'Permesi al uzantoj provi la Beta-version, la plej lastaj aldonaĵoj de la Iniciato pri Uzebleco.',
	'optin-title-optedout' => 'Provi betan version de Açai',
	'optin-title-optedin' => 'Forlasi Beta-version',
	'optin-title-justoptedin' => 'Bonvenon al Beta-version',
	'optin-title-justoptedout' => 'Dankon por provante nian Beta-version',
	'optin-title-feedback' => 'Opinioj pri Beta-versio',
	'optin-needlogin' => 'Vi devas [$1 ensaluti] por provi Beta-n.',
	'optin-intro' => 'La Iniciato pri Uzebleco de Vikipedio estis laboranta diliĝente por plibonigi la uzeblecon de Vikipedio. Ĉu vi volas provi nian Beta-version?',
	'optin-feedback-intro' => 'Dankon por provado de Beta-versio.
Ni volus scii vian opinion pri la nova interfaco, do bonvolu respondi la jenan nedevigan enketon.',
	'optin-feedback-back' => 'Se vi ne volas respondi la enketon, vi povas reiri al $1.',
	'optin-success-in' => 'Vi nun provas la Beta-version.
Vi povas reveni al la stabila versio klakante la ligilon "{{int:optin-leave}}" ĉe la supro de la ekrano.',
	'optin-leave-cancel' => 'Se vi deziras plu uzadi Beta-version, vi povas reveni al $1.',
	'optin-success-out' => 'Vi sukcese foriris el Beta-versio.
Vi povas reprovi ĝin iam ajn klakante la ligilon "{{int:optin-try}}" suprer dekstre de la ekrano.',
	'optin-success-feedback' => 'Dankon pro viaj opinioj!',
	'optin-accept-short' => 'Jes, ek!',
	'optin-accept-long' => 'Provi la Beta-version',
	'optin-accept-long-anon' => 'Ensaluti kaj provi la Beta-version',
	'optin-deny-short' => 'Ne, dankon.',
	'optin-deny-long' => 'Reiri al antaŭa paĝo',
	'optin-submit-out' => 'Forlasi Beta-version',
	'optin-submit-feedback' => 'Komenti',
	'optin-survey-yes' => 'Jes',
	'optin-survey-no' => 'Ne',
	'optin-survey-intro' => 'Dankon pro via provado de la Beta-versio.
Ni volus sciigi vian opinion pri la nova interfaco, do ni dankemus se vi plenumus la jenan nedevigan enketon antaŭ klakante "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Kio plaĉas al vi pri la Beta-versio? Kio malplaĉas al vi pri la Beta-versio?',
	'optin-survey-question-whyoptout' => 'Kial vi forlasas ĉi tiun beta-version? (bv. elekti ĉion taŭgan)',
	'optin-survey-answer-whyoptout-hard' => 'Ĝi estis tro malfacila uzi.',
	'optin-survey-answer-whyoptout-didntwork' => 'Ĝi ne funkciis ĝuste.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Ĝi ne funkciis ĝuste.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'La aspekto de la interfaco ne plaĉas al mi.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'La novaj etikedoj kaj dizajno ne plaĉas al mi.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'La nova ilobreto ne plaĉas al mi.',
	'optin-survey-answer-whyoptout-other' => 'Alia kialo:',
	'optin-survey-question-explain' => 'Bonvolu ekspliki aŭ pligrandigi vian supran respondon:',
	'optin-survey-question-techfail' => 'Ĉu vi trovis iujn ajn teknikajn fuŝojn uzante la Beta-version?',
	'optin-survey-question-techfail-ifyes' => 'Se jes, bonvolu priskribi:',
	'optin-survey-question-usedtoolbar' => 'Ĉu vi provis la novan etendeblan ilaron por redakti paĝon ?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Se jes, kion vi ŝatis en ĝi? Kion vi malŝatis en ĝi?',
	'optin-survey-question-usedtoolbar-ifno' => 'Se ne, kial ne? Ĉu vi uzos ĝin estonte? Bonvolu klarigi.',
	'optin-survey-question-different' => 'Kion ni povus fari malsame por ke vi plu uzu Beta-on?',
	'optin-survey-question-feedback' => 'Bonvolu diri al ni viajn rimarkojn:',
	'optin-survey-question-browser' => 'Kiun retumilon vi uzas?',
	'optin-survey-answer-browser-other' => 'Alia retumilo:',
	'optin-survey-question-os' => 'Kiun operaciumon vi uzas?',
	'optin-survey-answer-os-other' => 'Alia operaciumo:',
	'optin-survey-question-res' => 'Kio estas la distingivo de via ekrano?',
	'optin-survey-question-changes' => 'Kiujn ŝanĝojn vi ŝatus vidi en Beta estonte (bonvolu elekti ĉion kio taŭgas)?',
	'optin-survey-answer-changes-nav' => 'Igas la foliumadon ĉien en la retejo pli facila',
	'optin-survey-answer-changes-edittools' => 'Etendu kaj plibonigu redaktajn ilojn',
	'optin-survey-answer-changes-upload' => 'Plibonigu enhavon kaj dosierajn alŝutaĵojn.',
	'optin-survey-answer-changes-richtext' => 'Disvolvu redaktajn ilojn similaj al riĉatekstaj redaktiloj.',
	'optin-survey-answer-changes-lookfeel' => 'Ĝisdatigu la aspekton de la retejo.',
	'optin-survey-answer-changes-predictability' => 'Plibonigu funkciigon sen fuŝoj nek cimoj',
	'optin-survey-answer-changes-custom' => 'Ebligu pli multe da uzulaj preferoj',
	'optin-survey-answer-changes-other' => 'Alia:',
	'optin-improvements2' => '== Kio estis plibonigita? ==
[[File:UsabilityToolbar.png|left|link=|Ekrankapto de la nova progresa redakta ilobreto]]
[[File:UsabilityDialogs.png|left|link=|Ekrankapto de la nova enhavo-generanta dialogujo]]
;Progresa ilobreto
La progresa ilobreto faciliĝas redakti artikolojn.
Novaj kaj pli bonaj ikonoj klarliĝas kiun agon ĉiu ilo faras.
Pligrandigitaj sekcioj reduktas malordon, sed maloftaj iloj apudas per nur unu klako.
;Dialogujo
Kiam kreante ligilojn aŭ tabelojn, klakante la ila ikono nun malfermas dialogujon kiu helpas vin por krei ligilojn kaj tabelojn per simpla formularo.
; Kaj Plu
Ni enkondukis plurajn aliajn plibonigojn kiel progresan navigadon, kaskadajn etikedojn, plibonigitan serĉadon, kaj plu.
== Kiel helpi ==
Lerni plu pri la Uzebleca Initato, bonvolu viziti nian [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative hejmpaĝon].
<div style="clear:both"></div>',
);

/** Spanish (Español)
 * @author Antur
 * @author Barcex
 * @author Crazymadlover
 * @author Dalton2
 * @author Imre
 * @author Locos epraix
 * @author Toliño
 * @author Translationista
 */
$messages['es'] = array(
	'optin' => 'Formar parte de la Iniciativa de usabilidad',
	'optin-desc' => 'Permite a los usuarios probar Beta, las últimas mejoras de usabilidad de la Iniciativa de Usabilidad.',
	'optin-title-optedout' => 'Probar Beta',
	'optin-title-optedin' => 'Abandonar Beta',
	'optin-title-justoptedin' => 'Bienvenido a Beta',
	'optin-title-justoptedout' => 'Gracias por probar Beta',
	'optin-title-feedback' => 'Retroalimentación beta',
	'optin-needlogin' => 'Necesita [$1 iniciar sesión] para probar Beta',
	'optin-intro' => 'La Iniciativa de usabilidad de Wikipedia ha estado trabajando mucho para mejorar la usabilidad de Wikipedia.
¿Le gustaría probar nuestra versión Beta?',
	'optin-feedback-intro' => 'Gracias por probar Beta.
Deseamos saber qué piensa de nuestra interfaz, por lo que le agradeceremos completar la encuesta opcional que se muestra a continuación.',
	'optin-feedback-back' => 'Si no desea llenar la encuesta, puede volver a $1.',
	'optin-success-in' => 'Está usted probando ahora Beta.
Podrá volver a dejar de formar parte en cualquier momento haciendo clic en el vínculo "{{int:optin-leave}}" en la parte superior derecha de la pantalla.',
	'optin-leave-cancel' => 'Si desea continuar usando Beta, puede regresar a $1',
	'optin-success-out' => 'Ha abandonado exitosamente Beta.
Puede probarlo nuevamente en cualquier momento haciendo clic en el vínculo "{{int:optin-try}}" en la parte superior derecha de la pantalla.',
	'optin-success-feedback' => '¡Gracias por sus comentarios!',
	'optin-accept-short' => '¡Hagámoslo!',
	'optin-accept-long' => 'Probar Beta',
	'optin-accept-long-anon' => 'Regístrese y pruebe Beta',
	'optin-deny-short' => 'No, gracias',
	'optin-deny-long' => 'regresar a la página anterior',
	'optin-submit-out' => 'Abandonar Beta',
	'optin-submit-feedback' => 'Haga sus observaciones',
	'optin-survey-yes' => 'Sí',
	'optin-survey-no' => 'No',
	'optin-survey-intro' => 'Gracias por probar Beta.
Nos gustaría saber qué opina acerca de la nueva interfaz; agradeceríamos que rellenase la encuesta opcional de abajo antes de hacer clic en "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => '¿Qué le gustó de Beta? ¿Qué no le gustó de Beta?',
	'optin-survey-question-whyoptout' => '¿Por qué va a abandonar esta Beta? (Por favor, seleccione todo lo que sea pertinente)',
	'optin-survey-answer-whyoptout-hard' => 'Fue muy difícil de usar.',
	'optin-survey-answer-whyoptout-didntwork' => 'No funcionó apropiadamente.',
	'optin-survey-answer-whyoptout-notpredictable' => 'No funcionó de manera predecible.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'No me gustó su apariencia.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'No me gustaron las nuevas pestañas y diseño.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'No me gustó la nueva barra de herramientas.',
	'optin-survey-answer-whyoptout-other' => 'Otra razón:',
	'optin-survey-question-explain' => 'Por favor, explique o desarrolle su respuesta arriba:',
	'optin-survey-question-techfail' => '¿Experimentó usted alguna dificultad técnica cuando usó Beta?',
	'optin-survey-question-techfail-ifyes' => 'Si la respuesta es sí, por favor, desarrolle o explíquelo:',
	'optin-survey-question-usedtoolbar' => '¿Usó usted la nueva barra de herramientas expandible para editar una página?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Si la respuesta es sí, ¿qué le gustó de él?, ¿qué le disgustó?',
	'optin-survey-question-usedtoolbar-ifno' => 'En caso negativo, ¿por qué no?...  ¿Lo haría en el futuro? Por favor, explíquelo.',
	'optin-survey-question-different' => '¿Qué podríamos haber hecho diferente para que continuase usted usando nuestra Beta?',
	'optin-survey-question-feedback' => 'Por favor, háganos conocer cualquier otro comentario, inquietud u opinión que tenga para nosotros - ¡toda retroalimentación es útil!:',
	'optin-survey-question-browser' => '¿Qué navegador utiliza?',
	'optin-survey-answer-browser-other' => 'Otro navegador:',
	'optin-survey-question-os' => '¿Qué sistema operativo utiliza?',
	'optin-survey-answer-os-other' => 'Otro sistema operativo:',
	'optin-survey-question-res' => '¿Cuál es la resolución de su pantalla?',
	'optin-survey-question-changes' => '¿Qué cambios desearía ver en Beta en el futuro? (Por favor, seleccione todo lo que coincida)',
	'optin-survey-answer-changes-nav' => 'Hacer la navegación general del sitio mas fácil de usar.',
	'optin-survey-answer-changes-edittools' => 'Aumentar y mejorar las herramientas de edición.',
	'optin-survey-answer-changes-upload' => 'Mejorar el contenido y las subidas de material multimedia.',
	'optin-survey-answer-changes-richtext' => 'Desarrollar herramientas de edición similares a los editores de texto enriquecido.',
	'optin-survey-answer-changes-lookfeel' => 'Actualizar la apariencia y funcionalidad de la interfaz del sitio.',
	'optin-survey-answer-changes-predictability' => 'Mejorar la predictibilidad del comportamiento.',
	'optin-survey-answer-changes-custom' => 'Permitir una mayor personalización por parte del usuario.',
	'optin-survey-answer-changes-other' => 'Otro:',
	'optin-improvements2' => '== Qué se ha mejorado? ==
[[File:UsabilityToolbar.png|left|link=|Captura de pantalla de la barra de edición mejorada]]
[[File:UsabilityDialogs.png|left|link=|Captura de pantalla de los nuevos diálogos de generació nde contenidos]]
;Barra de herramientas mejorada
La barra de herramientas mejorada hace de la edición de artículos una tarea más sencilla.
Iconos nuevos y mejorados facilitan la identificación de la función de cada herramienta.
Las secciones expansibles reducen el desorden a a vez que mantienen  las herramientas menos usadas a sólo un clic de distancia.
;Diálogos
Para crear vínculos o tablas, basta con llenar el formulario de una caja de diálogo que aparece al apretar sobre el icono de la herramienta.
;Y más
También introducimos muchos otros avances, como la navegación mejorada, las pestañas en cascada y la búsqueda mejorada, entre otras.
== Como involucrarse ==
Para conocer más sobre la Iniciativa de Usabilidad visita nuestra [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative página principal].
<div style="clear:both"></div>',
);

/** Estonian (Eesti)
 * @author Avjoska
 * @author Pikne
 */
$messages['et'] = array(
	'optin' => 'Kasutushõlpsuse algatuses kaasategemine',
	'optin-desc' => 'Võimaldab proovida beetat, viimaseid kasutushõlpsuse täiustusi.',
	'optin-title-optedout' => 'Proovi beetat',
	'optin-title-optedin' => 'Lahku beetaversioonist',
	'optin-title-justoptedin' => 'Tere tulemast beetasse',
	'optin-title-justoptedout' => 'Täname, et proovisid beetaversiooni',
	'optin-title-feedback' => 'Anna tagasisidet',
	'optin-needlogin' => 'Beeta proovimiseks tuleb [$1 sisse logida].',
	'optin-intro' => 'Vikipeedia kasutushõlpsuse algatus on Vikipeedia paremini kasutatavaks muutmise nimel palju tööd teinud. Kas soovid beetat proovida?',
	'optin-feedback-intro' => 'Aitäh beeta katsetamise eest!
Oleme tänulikud, kui avaldad arvamust uue kasutajaliidese kohta ja täidad alljärgneva vabatahtliku küsitluse.',
	'optin-feedback-back' => 'Kui sa ei soovi küsitlust täita, saad naasta leheküljele $1.',
	'optin-success-in' => 'Katsetad nüüd beetaversiooni.
Beetast lahkumiseks saad lehekülje ülaosas ükskõik millal nuppu "{{int:optin-leave}}" klõpsata.',
	'optin-leave-cancel' => 'Kui soovid beetaversiooni kasutamist jätkata, saad naasta leheküljele $1.',
	'optin-success-out' => 'Oled edukalt beetast lahkunud.
Selle taasproovimiseks saad lehekülje ülaosas ükskõik millal nuppu "{{int:optin-try}}" klõpsata.',
	'optin-success-feedback' => 'Aitäh tagasiside eest!',
	'optin-accept-short' => 'Tehkem proovi!',
	'optin-accept-long' => 'Proovi beetaversiooni',
	'optin-accept-long-anon' => 'Logi sisse ja proovi beetat',
	'optin-deny-short' => 'Ei, tänan',
	'optin-deny-long' => 'Naase eelmisele lehele',
	'optin-submit-out' => 'Lahku beetaversioonist',
	'optin-submit-feedback' => 'Anna tagasisidet',
	'optin-survey-yes' => 'Jah',
	'optin-survey-no' => 'Ei',
	'optin-survey-intro' => 'Aitäh beeta katsetamise eest!
Oleme tänulikud, kui avaldad arvamust uue kasutajaliidese kohta ja täidad alljärgneva vabatahtliku küsitluse, enne kui nuppu "[[#leave|{{int:optin-submit-out}}]]" klõpsad.',
	'optin-survey-question-likedislike' => 'Mis meeldis beetas? Mis ei meeldinud beetas?',
	'optin-survey-question-whyoptout' => 'Miks sa lahkud beetaversioonist? (Palun vali kõik sobivad põhjused.)',
	'optin-survey-answer-whyoptout-hard' => 'See oli kasutamiseks liialt keeruline.',
	'optin-survey-answer-whyoptout-didntwork' => 'See ei toiminud korralikult.',
	'optin-survey-answer-whyoptout-notpredictable' => 'See ei toiminud alati sama moodi.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Mulle ei meeldinud selle välimus.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Mulle ei meeldinud uued sakid ega paigutus.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Mulle ei meeldinud uus tööriistariba.',
	'optin-survey-answer-whyoptout-other' => 'Muu põhjus:',
	'optin-survey-question-explain' => 'Palun põhjenda või täpsusta eelnevat vastust:',
	'optin-survey-question-techfail' => 'Kas beeta kasutamises on esinenud tehnilisi tõrkeid?',
	'optin-survey-question-techfail-ifyes' => 'Kui jah, täpsusta või selgita:',
	'optin-survey-question-usedtoolbar' => 'Kas oled lehekülje redigeerimiseks uut lahtilükatavat tööriistariba kasutanud?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Kui jah, siis mis sulle selle juures meeldis? Mis ei meeldinud?',
	'optin-survey-question-usedtoolbar-ifno' => 'Kui ei, siis miks? Kas edaspidi on plaanis? Palun selgita.',
	'optin-survey-question-different' => 'Mida oleksime võinud teha teisiti, et oleksid beetat kasutama jäänud?',
	'optin-survey-question-feedback' => 'Palun jaga meiega muid kommentaare, mureküsimusi või mõtteid – igasugune tagasiside on vajalik:',
	'optin-survey-question-browser' => 'Millist internetilehitsejat kasutad?',
	'optin-survey-answer-browser-other' => 'Muu internetilehitseja:',
	'optin-survey-question-os' => 'Millist operatsioonisüsteemi sa kasutad?',
	'optin-survey-answer-os-other' => 'Muu operatsioonisüsteem:',
	'optin-survey-question-res' => 'Milline on su kuvari eraldusvõime?',
	'optin-survey-question-changes' => 'Milliseid muudatusi soovid näha beetaversioonis tulevikus (vali kõik sobivad)?',
	'optin-survey-answer-changes-nav' => 'Võrgukoha lehitsetavuse üldine lihtsustamine.',
	'optin-survey-answer-changes-edittools' => 'Redigeerimisriistade lisamine ja täiustamine.',
	'optin-survey-answer-changes-upload' => 'Sisu ja failide üleslaadimse täiustamine.',
	'optin-survey-answer-changes-richtext' => 'Rikasteksti redaktoritega sarnaste redigeerimisriistade väljatöötamine.',
	'optin-survey-answer-changes-lookfeel' => 'Võrgukoha väljanägemise uuendamine.',
	'optin-survey-answer-changes-predictability' => 'Funktsioneerimise etteaimatavamaks muutmine.',
	'optin-survey-answer-changes-custom' => 'Enama isikupärastamise lubamine.',
	'optin-survey-answer-changes-other' => 'Muu:',
	'optin-improvements2' => '== Mida on täiustatud? ==
[[File:UsabilityToolbar.png|left|link=|Kuvatõmmis täiustatud tööriistaribast]][[File:UsabilityDialogs.png|left|link=|Kuvatõmmis uutest sisutekitamise dialoogikastidest]]
;Parem tööriistariba
Uue tööriistariba abil on kergem artikleid redigeerida.
Uute ja viimistletud ikoonide abil on selgem, mida iga riist teeb.
Lahtilükatavad ribaosad hoiavad harva kasutatavad riistad peidus ja samas vaid klõpsuga kättesaadavad.
;Dialoog
Kui nüüd linkide ja tabelite lisamisel riista ikooni klõpsata, avaneb dialoogikast, mis aitab luua linke ja tabeleid lihtsa vormi täitmise teel.
;Ja veel
Oleme tarvitusele võtnud teisigi täiustusi, nagu parem navigeerimine, virnastatavad sakid ja viimistletud otsing.

== Kuidas kaasa teha ==
Lisateavet kasutushõlpsuse algatuse kohta leiad meie [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative kodulehelt].
<div style="clear:both"></div>',
);

/** Basque (Euskara)
 * @author An13sa
 * @author Joxemai
 * @author Kobazulo
 * @author Theklan
 * @author Xabier Armendaritz
 */
$messages['eu'] = array(
	'optin' => 'Erabilgarritasun Iniziatibarekin bat egin',
	'optin-desc' => 'Erabiltzaileen Beta frogatzea baimentzen die, Erabilgarritasun Iniziatibaren azkeneko erabilgarritasun hobekuntzekin.',
	'optin-title-optedout' => 'Beta probatu',
	'optin-title-optedin' => 'Beta utzi',
	'optin-title-justoptedin' => 'Ongi etorri Betara',
	'optin-title-justoptedout' => 'Mila esker Beta probatzeagatik',
	'optin-title-feedback' => 'Beta feedbacka',
	'optin-needlogin' => '[$1 saioa hasi] behar duzu Beta probatzeko.',
	'optin-intro' => 'Wikipediaren Erabilgarritasun Iniziatiba lanean aritu da Wikipediaren erabilgarritasuna hobetzeko.
Nahiko al zenuke gure Beta probatu?',
	'optin-feedback-intro' => 'Eskerrik asko Beta erabili izanagatik.
Jakin nahi dugu ea zer deritzozun interfaze berriaren inguruan, beraz asko gustatuko litzaiguke behean duzun hautazko inkesta beteko bazenu.',
	'optin-feedback-back' => 'Gure inkesta bete nahi ez baduzu, beharbada $1 orrira itzuli nahiko duzu.',
	'optin-success-in' => 'Beta bertsioa probatzen ari zara.
Edonoiz atzera egin eta lehengo bertsiora itzul zaitezke, orriaren goialdean "{{int:optin-leave}}" sakatuz.',
	'optin-leave-cancel' => 'Beta erabiltzen jarraitu nahi baduzu, $1-(e)ra itzul zaitezke.',
	'optin-success-out' => 'Betarik arrakastatsuko atera zara.
Nahi duzunean proba dezakezu berriro "{{int:optin-try}}" loturan klik eginda pantailaren goian eskuinaldean.',
	'optin-success-feedback' => 'Milesker erantzuteagatik!',
	'optin-accept-short' => 'Proba dezagun!',
	'optin-accept-long' => 'Beta probatu',
	'optin-accept-long-anon' => 'Saioa hasi eta Beta probatu',
	'optin-deny-short' => 'Ez, mila esker',
	'optin-deny-long' => 'Itzuli aurreko orrialdera',
	'optin-submit-out' => 'Beta utzi',
	'optin-submit-feedback' => 'Eman feedbacka',
	'optin-survey-yes' => 'Bai',
	'optin-survey-no' => 'Ez',
	'optin-survey-intro' => 'Eskerrik asko Beta bertsioa saiatzeagatik.
Gure itxura berriari buruz pentsatzen duzuna nahi genuke jakin.  Eskertuko genuke borondatezko beheko inkesta betetzea "[[#leave|{{int:optin-submit-out}}]]" sakatu baino lehen.',
	'optin-survey-question-likedislike' => 'Zer gustatu zaizu Betatik? Zer ez zaizu gustatu?',
	'optin-survey-question-whyoptout' => 'Zergatik atera zara Betatik? (Mesedez galdera guztiak erantzun.)',
	'optin-survey-answer-whyoptout-hard' => 'Erabiltzea zailegia zen.',
	'optin-survey-answer-whyoptout-didntwork' => 'Ez zebilen behar bezala.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Ez du ondo funtzionatzen.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Ez nuen bere itxura gustuko.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Diseinu berria ez zait gustatu.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Ez zitzaidan tresna-barra berria gustatzen.',
	'optin-survey-answer-whyoptout-other' => 'Beste arrazoirik:',
	'optin-survey-question-explain' => 'Mesedez azaldu zure erantzuna behean:',
	'optin-survey-question-techfail' => 'Arazo teknikorik izan duzu Beta erabiltzean?',
	'optin-survey-question-techfail-ifyes' => 'Hala bada, mesedez azaldu:',
	'optin-survey-question-usedtoolbar' => 'Erabili al duzu tresna-barra tolesgarri berria orriak aldatzeko?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Hala bada, zer gustatu zaizu? Zer ez zaizu gustatu?',
	'optin-survey-question-usedtoolbar-ifno' => 'Hala ez bada, zergatik ez? Erabiliko duzu? Azal iezaiguzu.',
	'optin-survey-question-different' => 'Zer egin genezakeen ezberdina zuk Beta erabiltzen jarraitzeko?',
	'optin-survey-question-feedback' => 'Mesedez jakinarazi gaitzazu bestelako iruzkin, proposamen edo iritzirik baduzu guretzat - feedback guztiak erabilgarriak dira:',
	'optin-survey-question-browser' => 'Zein nabigatzaile erabiltzen duzu?',
	'optin-survey-answer-browser-other' => 'Beste nabigatzailerik:',
	'optin-survey-question-os' => 'Zein sistema eragile erabiltzen duzu?',
	'optin-survey-answer-os-other' => 'Beste sistema eragilerik:',
	'optin-survey-question-res' => 'Zein da zure pantailaren bereizmena?',
	'optin-survey-question-changes' => 'Zer aldaketa ikusi nahi zenituzke Beta bertsioan, aurrera begira? (Aukera itzazu nahi dituzun erantzun guztiak.)',
	'optin-survey-answer-changes-nav' => 'Erraztu Wikipedian nabigatzea.',
	'optin-survey-answer-changes-edittools' => 'Handitu eta indartu editatzeko tresnak.',
	'optin-survey-answer-changes-upload' => 'Hobetu edukiak eta multimedia fitxategiak inportatzeko bideak.',
	'optin-survey-answer-changes-richtext' => 'Testu editore aberastuen antzeko edizio-tresnak garatu.',
	'optin-survey-answer-changes-lookfeel' => 'Gunearen "itxura eta forma" eguneratu.',
	'optin-survey-answer-changes-predictability' => 'Jardueraren iragarpentasuna hobetu.',
	'optin-survey-answer-changes-custom' => 'Lankide pertsonalizazio handiagoa ahalbidetu',
	'optin-survey-answer-changes-other' => 'Bestelakoak:',
	'optin-improvements2' => '== Zer da hobetu dena? ==
[[File:UsabilityToolbar.png|left|link=|Editatzeko tresna-barra berriaren itxura.]]
[[File:UsabilityDialogs.png|left|link=|Edukia sortzeko elkarrizketa berrien itxura.]]
;Hobetutako Tresna-barra
Tresna-barra berriak artikuluak editatzea erraztu egiten du.
Ikono berriak hobeak dira eta argiago erakusten dute tresna bakoitzaren erabilgarritasuna.
Atal zabalduen gainezka-itxura hobetu egiten da, gutxitan erabilitako ikonoak klik bat aparte jarriz.
;Elkarrizketa
Loturak eta taulak sortzean, tresna sakatzean elkarrizketa-koadro bat zabaltzen du, loturak eta taulak zehaztapen zenbait emanez sortzen dituena.
;Eta gehiago
Hobekuntza gehiago sortu ditugu, hala nola, nabigazio berria, mailakatutako tab, bilaketa hobetua, eta abar.
== Nola lagundu ==
Erabilgarritasunerako Ekimenari buruz gehiago jakiteko, bisitatu [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative homepage].
<div style="clear:both"></div>',
);

/** Persian (فارسی)
 * @author Ebraminio
 * @author Huji
 * @author Ladsgroup
 * @author Mardetanha
 */
$messages['fa'] = array(
	'optin' => 'انتخاب‌کردن گام مقدماتی قابلیت استفاده',
	'optin-desc' => 'به کاربران اجازه بده تا نسخهٔ بتا از آخرین پیشرفت‌های آغازهٔ استفاده‌پذیری را بیازمایند.',
	'optin-title-optedout' => 'استفاده از بتا',
	'optin-title-optedin' => 'خروج از بتا',
	'optin-title-justoptedin' => 'به نسخه بتا خوش‌آمدید',
	'optin-title-justoptedout' => 'ممنونیم از اینکه نسخه بتا را امتحان کردید',
	'optin-title-feedback' => 'بازخورد نسخه بتا',
	'optin-needlogin' => 'شما برای استفاده از نسخه بتا باید [$1 وارد شوید].',
	'optin-intro' => 'بخش بهبود کیفیت ویکی‌پدیا در تلاش است تا رابط کاربری ویکی‌پدیا را بهبود ببخشد. آیا مایلید که نسخه رابط کاربری بتا را امتحان کنید؟',
	'optin-feedback-intro' => 'از اینکه نسخهٔ بتا را امتحان می‌کنید متشکریم.
ما مایلیم نظر شما را در مورد نمایش جدید صفحه‌ها بدانیم، در نتیجه اگر در نظرخواهی زیر شرکت کنید ما را یاری می‌کنید.',
	'optin-feedback-back' => 'اگر علاقه‌ای به شرکت در نظرخواهی ندارید، می‌توانید به $1 بازگردید.',
	'optin-success-in' => 'در حال حاضر شما نسخه بتا را آزمایش می‌کنید.
شما می‌توانید هر وقت که خواستید با کلیک بر روی «{{int:optin-leave}}» در بالای صفحه، به حالت قبل برگردید.',
	'optin-leave-cancel' => 'اگر مایل باشید که به استفاده از نسخه بتا ادامه دهید، می‌توانید به $1 باز گردید.',
	'optin-success-out' => 'شما با موفقیت از نسخه بتا خارج شدید.
شما می‌توانید در هر زمان دیگر با کلیک کردن بر روی پیوند «{{int:optin-try}}» در بالای صفحه دوباره آن را امتحان کنید.',
	'optin-success-feedback' => 'از شما به خاطر بازخوردتان متشکریم!',
	'optin-accept-short' => 'بزن بریم!',
	'optin-accept-long' => 'خروج از بتا',
	'optin-accept-long-anon' => 'به سامانه ورود شوید و نسخه بتا را بیازمایید',
	'optin-deny-short' => 'نه ممنون!',
	'optin-deny-long' => 'بازگشت به صفحه قبلی',
	'optin-submit-out' => 'خروج از بتا',
	'optin-submit-feedback' => 'دادن بازخورد',
	'optin-survey-yes' => 'بله',
	'optin-survey-no' => 'نه',
	'optin-survey-question-likedislike' => 'چه چیزی را در مورد نسخهٔ بتا دوست داشتید؟ چه چیزی را در مورد نسخهٔ بتا دوست نداشتید؟',
	'optin-survey-question-whyoptout' => 'چرا استفاده از ویژگی‌های جدید را ترک می‌کنید؟(لطفا انتخاب کنید.تاثیر گذار است)',
	'optin-survey-answer-whyoptout-hard' => 'استفاده از این خیلی سخت است.',
	'optin-survey-answer-whyoptout-didntwork' => 'به اندازه کافی مفید نیست.',
	'optin-survey-answer-whyoptout-notpredictable' => 'ویژگی‌ها آن طور که انتظار می‌رفت نیست.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'من از طرح‌بندی و زبانه‌های جدید خوشم نمی‌آید.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'من از نوار ابزار جدید خوشم نمی‌آید.',
	'optin-survey-answer-whyoptout-other' => 'دلایل دیگر:',
	'optin-survey-question-browser' => 'از کدام مرورگر استفاده می‌کنید؟',
	'optin-survey-answer-browser-other' => 'مرورگر دیگر:',
	'optin-survey-question-os' => 'از کدام سیستم عامل استفاده می‌کنید؟',
	'optin-survey-answer-os-other' => 'سیستم عامل دیگر:',
	'optin-survey-question-res' => 'وضوح صفحه نمایش شما چقدر است؟',
	'optin-survey-answer-changes-lookfeel' => 'به روز رسانی ظاهر وب‌گاه.',
	'optin-survey-answer-changes-other' => 'سایر موارد:',
	'optin-improvements2' => '== چه چیزهایی بهبود یافته؟ ==
[[File:UsabilityToolbar.png|left|link=|Screenshot of the enhanced edit toolbar]]
[[File:UsabilityDialogs.png|left|link=|Screenshot of the new content generation dialogs]]
;نوار ابزار پیشرفته
نوار ابزار پیشرفته ویرایش مقالات را ساده‌تر می‌سازد.
شکلک‌های جدید و بهبود یافته کاری که ابزارها انجام می‌دهند را واضح‌تر می‌کند.
گسترش بخش‌ها، درهم و برهمی را کم ساخته در حالیکه ابزارهایی که به ندرت استفاده می‌شوند فقط با یک کلیک قابل دسترس هستند.
;گفتگو
وقتی که پیوندها یا جدول‌ها را درست می‌کنید، کلیک کردن بر روی نمایه ابزار یک جعبه تعاملی را درست می‌کند که به شما در درست کردن پیوندها و جدول‌ها به وسیله پر کردن یک فرم ساده کمک می‌کند.
;و بیشتر
ما تعداد دیگری از بهسازی‌ها را نظیر ناوبری پیشرفته، زبانه‌های آبشاری و جستجوی بهبود یافته در این نسخه پیاده ساخته‌ایم.

== چگونه می‌توانید در این تغییرات نقش بازی کنید؟ ==
برای اطلاع بیشتر در رابطه با  توانایی استفاده ابتکاری به [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative اینجا] سر بزنید.
<div style="clear:both"></div>',
);

/** Finnish (Suomi)
 * @author Cimon Avaro
 * @author Crt
 * @author Nike
 * @author Silvonen
 * @author Str4nd
 */
$messages['fi'] = array(
	'optin' => 'Käytettävyyshankkeen valinnainen käyttöönotto',
	'optin-desc' => 'Mahdollistaa käyttäjien kokeilla Betaa – käytettävyyshankkeen uusimpia käytettävyyttä parantavia lisäyksiä.',
	'optin-title-optedout' => 'Kokeile Betaa',
	'optin-title-optedin' => 'Poistu Betasta',
	'optin-title-justoptedin' => 'Tervetuloa Betaan',
	'optin-title-justoptedout' => 'Kiitos Betan kokeilusta',
	'optin-title-feedback' => 'Anna palautetta',
	'optin-needlogin' => 'Sinun tulee [$1 kirjautua sisään] kokeillaksesi Betaa.',
	'optin-intro' => 'Wikipedian käytettävyyshanke on ahkeroinut parantaakseen Wikipedian käytettävyyttä. 
Haluatko kokeilla Betaa?',
	'optin-feedback-intro' => 'Kiitos Betan kokeilusta.
Haluaisimme tietää, mitä mieltä olet uudesta käyttöliittymästä, joten arvostaisimme jos täyttäisit alapuolelta löytyvän valinnaisen kyselyn.',
	'optin-feedback-back' => 'Jos et halua täyttää kyselylomaketta, voit palata kohteeseen $1.',
	'optin-success-in' => 'Kokeilet nyt Betaa.
Voit poistaa valinnan käytöstä milloin tahansa napsauttamalla ”{{int:optin-leave}}” -painiketta ruudun yläreunassa.',
	'optin-leave-cancel' => 'Jos haluat edelleen käyttää Betaa, voit palata kohteeseen $1',
	'optin-success-out' => 'Olet onnistuneesti poistunut Betasta.
Voit milloin tahansa kokeilla sitä uudelleen ”{{int:optin-try}}” -painikkeesta ruudun oikeasta yläkulmasta.',
	'optin-success-feedback' => 'Kiitos palautteestasi.',
	'optin-accept-short' => 'Tehdään niin!',
	'optin-accept-long' => 'Kokeile Betaa',
	'optin-accept-long-anon' => 'Kirjaudu sisään ja kokeile Betaa',
	'optin-deny-short' => 'Ei kiitos',
	'optin-deny-long' => 'Palaa edelliselle sivulle',
	'optin-submit-out' => 'Poistu Betasta',
	'optin-submit-feedback' => 'Anna palautetta',
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
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'En pitänyt uudesta työkalupalkista.',
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
	'optin-survey-question-changes' => 'Mitä muutoksia haluaisit nähdä Betassa tulevaisuudessa (valitse kaikki sopivat)?',
	'optin-survey-answer-changes-nav' => 'Helpottaa sivuston navigointia ylipäätään.',
	'optin-survey-answer-changes-edittools' => 'Lisää ja tehosta mukkaustyökaluita.',
	'optin-survey-answer-changes-upload' => 'Paranna sisällön ja median lähettämistä.',
	'optin-survey-answer-changes-richtext' => 'Kehitä muokkaustyökaluita tekstinkäsittelyohjelmia muistuttavaksi.',
	'optin-survey-answer-changes-lookfeel' => 'Päivitä sivuston ulkonäköä ja käytettävyyttä.',
	'optin-survey-answer-changes-predictability' => 'Paranna esityksen ennustettavuutta.',
	'optin-survey-answer-changes-custom' => 'Sallii laajemman käyttäjäkohtaisen muutettavuuden.',
	'optin-survey-answer-changes-other' => 'Muu',
);

/** French (Français)
 * @author IAlex
 * @author Kropotkine 113
 * @author Louperivois
 * @author PieRRoMaN
 * @author Urhixidur
 * @author Y-M D
 */
$messages['fr'] = array(
	'optin' => 'Participer à l’initiative d’utilisabilité',
	'optin-desc' => 'Permet aux utilisateurs d’essayer la bêta, les dernières améliorations de l’initiative d’utilisabilité',
	'optin-title-optedout' => 'Essayer la bêta',
	'optin-title-optedin' => 'Quitter la bêta',
	'optin-title-justoptedin' => 'Bienvenue à la bêta',
	'optin-title-justoptedout' => 'Merci pour votre participation à la bêta',
	'optin-title-feedback' => 'Donnez votre avis',
	'optin-needlogin' => 'Vous devez vous [$1 connecter] pour essayer la bêta.',
	'optin-intro' => 'L’initiative d’utilisabilité de Wikipédia a travaillé dur pour améliorer l’utilisabilité des projets Wikimédia.
Voulez-vous essayer la bêta ?',
	'optin-feedback-intro' => 'Merci d’essayer la bêta.
Nous aimerions savoir ce que vous pensez de notre nouvelle interface, nous apprécierions que vous remplissiez le questionnaire ci-dessous.',
	'optin-feedback-back' => 'Si vous ne voulez pas remplir le questionnaire, vous pouvez revenir à $1.',
	'optin-success-in' => 'Vous êtes en train de tester la bêta.
Vous pouvez interrompre la session à tout moment en cliquant sur le lien « {{int:optin-leave}} » en haut de l’écran.',
	'optin-leave-cancel' => 'Si vous voulez continuer à utiliser la bêta, vous pouvez revenir à $1',
	'optin-success-out' => 'Vous avez quitté la bêta.
Vous pouvez de nouveau participer à tout moment en cliquant sur le lien « {{int:optin-try}} » en haut à droite de l’écran.',
	'optin-success-feedback' => 'Merci pour votre avis !',
	'optin-accept-short' => 'Allons-y !',
	'optin-accept-long' => 'Essayer la bêta',
	'optin-accept-long-anon' => 'Connectez-vous et essayez la bêta',
	'optin-deny-short' => 'Non merci',
	'optin-deny-long' => 'Revenir à la page précédente',
	'optin-submit-out' => 'Quitter la bêta',
	'optin-submit-feedback' => 'Donnez votre avis',
	'optin-survey-yes' => 'Oui',
	'optin-survey-no' => 'Non',
	'optin-survey-intro' => 'Merci pour votre essai de la bêta.
Nous aimerions savoir ce que vous pensez de notre nouvelle interface, nous apprécierions que vous remplissiez le sondage optionnel avant de cliquer sur « [[#leave|{{int:optin-submit-out}}]] ».',
	'optin-survey-question-likedislike' => 'Qu’avez-vous aimé dans la bêta ? Qu’est-ce que vous n’avez pas aimé dans la bêta ?',
	'optin-survey-question-whyoptout' => 'Pourquoi quittez-vous la bêta ? (sélectionnez tout ce qui convient)',
	'optin-survey-answer-whyoptout-hard' => 'Il était trop difficile de l’utiliser.',
	'optin-survey-answer-whyoptout-didntwork' => 'Ça ne fonctionne pas correctement.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Cela ne s’est pas passé comme prévu.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Je n’ai pas aimé son apparence.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Je n’ai pas aimé les nouveaux onglets et la nouvelle disposition.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Je n’ai pas aimé la nouvelle barre d’outils.',
	'optin-survey-answer-whyoptout-other' => 'Autre raison :',
	'optin-survey-question-explain' => 'Expliquez ou élaborez votre réponse ci-dessus :',
	'optin-survey-question-techfail' => 'Avez-vous rencontré des problèmes techniques en utilisant la bêta ?',
	'optin-survey-question-techfail-ifyes' => 'Si oui, expliquez ou élaborez :',
	'optin-survey-question-usedtoolbar' => 'Avez-vous utilisé la nouvelle barre d’outils déroulable pour modifier une page ?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Si oui, qu’avez vous aimé et que n’avez-vous pas aimé à son propos ?',
	'optin-survey-question-usedtoolbar-ifno' => 'Si non, pourquoi ? L’utiliserez-vous dans le futur ? Expliquez.',
	'optin-survey-question-different' => 'Qu’est-ce qui aurait dû être différent pour que vous continuiez à utiliser la bêta ?',
	'optin-survey-question-feedback' => 'Donnez-nous votre réaction :',
	'optin-survey-question-browser' => 'Quel navigateur utilisez-vous ?',
	'optin-survey-answer-browser-other' => 'Autre navigateur :',
	'optin-survey-question-os' => 'Quel système d’exploitation utilisez-vous ?',
	'optin-survey-answer-os-other' => 'Autre système d’exploitation :',
	'optin-survey-question-res' => 'Quelle est la résolution de votre écran ?',
	'optin-survey-question-changes' => 'Qu’aimeriez-vous voir dans la bêta dans le futur (sélectionnez tout ce qui convient) ?',
	'optin-survey-answer-changes-nav' => 'Faire que la navigation générale du site soit plus simple à utiliser.',
	'optin-survey-answer-changes-edittools' => 'Améliorer et augmenter les outils d’édition.',
	'optin-survey-answer-changes-upload' => 'Améliorer l’importation de contenu et de média.',
	'optin-survey-answer-changes-richtext' => 'Développer des outils de modification similaires aux éditeurs en texte enrichi.',
	'optin-survey-answer-changes-lookfeel' => 'Changer l’apparence du site.',
	'optin-survey-answer-changes-predictability' => 'Améliorer la prévisibilité des modifications.',
	'optin-survey-answer-changes-custom' => 'Permettre une plus grande personnalisation de l’interface.',
	'optin-survey-answer-changes-other' => 'Autre :',
	'optin-improvements2' => '== Qu’est-ce qui a été amélioré ? ==
[[File:UsabilityToolbar.png|left|link=|Capture d’écran de la barre d’outils d’édition améliorée]]
[[File:UsabilityDialogs.png|left|link=|Capture d’écran du dialogue de génération de contenu]]
;Barre d’outils améliorée
Avec la barre d’outils améliorée, il est plus facile de modifier des articles. Les icônes ont été améliorées et de nouvelles ont été créées pour rendre plus clair l’action que chaque outil effectue. Les sections déroulantes réduisent l’encombrement tout en conservant les outils que vous utilisez rarement à portée de clic.
; Dialogues
Lors de la création de liens ou de tableaux, cliquez sur l’icône et une boîte de dialogue s’ouvrira pour vous aider à créer des liens et des tables en remplissant un simple formulaire.
; Et plus
Nous avons introduit un certain nombre d’autres améliorations comme la navigation améliorée, les onglets en cascade, la recherche améliorée, et plus encore.
== Comment s’impliquer ==
Pour en savoir plus sur l’initiative d’utilisabilité, visitez notre [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative page d’accueil].
<div style="clear:both"></div>',
);

/** Franco-Provençal (Arpetan)
 * @author Cedric31
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'optin' => 'Participar a l’iniciativa d’utilisabilitât',
	'optin-desc' => 'Pèrmèt ux utilisators d’èprovar la Bèta, les mèlyoracions de l’iniciativa d’utilisabilitât.',
	'optin-title-optedout' => 'Èprovar la Bèta',
	'optin-title-optedin' => 'Quitar la Bèta',
	'optin-title-justoptedin' => 'Benvegnua a la Bèta',
	'optin-title-justoptedout' => 'Grant-marci por voutra participacion a la Bèta',
	'optin-title-feedback' => 'Balyéd voutron avis',
	'optin-needlogin' => 'Vos vos dête [$1 branchiér] por èprovar la Bèta.',
	'optin-intro' => 'L’iniciativa d’utilisabilitât de Vouiquipèdia at travalyê dur por mèlyorar l’utilisabilitât de Vouiquipèdia.
Voléd-vos èprovar la Bèta ?',
	'optin-feedback-intro' => 'Grant-marci d’èprovar la Bèta.
Nos amerians savêr cen que vos pensâd de noutra novèla entèrface, donc nos aprèciyerians que vos rempléd lo quèstionèro u chouèx ce-desot.',
	'optin-feedback-back' => 'Se vos voléd pas remplir lo quèstionèro, vos pouede tornar a $1.',
	'optin-success-in' => 'Vos éte aprés èprovar la Bèta.
Vos pouede arrètar la sèance a tot moment en cliquent sur lo lim « {{int:optin-leave}} » d’amont la pâge.',
	'optin-leave-cancel' => 'Se vos voléd continuar a utilisar la Bèta, vos pouede tornar a $1.',
	'optin-success-out' => 'Vos éd quitâ avouéc reusséta la Bèta.
Vos la pouede tornar èprovar a tot moment en cliquent sur lo lim « {{int:optin-try}} » d’amont la pâge.',
	'optin-success-feedback' => 'Grant-marci por voutron avis !',
	'optin-accept-short' => 'Alens-y !',
	'optin-accept-long' => 'Èprovar la Bèta',
	'optin-accept-long-anon' => 'Branchiéd-vos et pués èprovâd la Bèta',
	'optin-deny-short' => 'Nan, bien grant-marci',
	'optin-deny-long' => 'Tornar a la pâge devant',
	'optin-submit-out' => 'Quitar la Bèta',
	'optin-submit-feedback' => 'Balyéd voutron avis',
	'optin-survey-yes' => 'Ouè',
	'optin-survey-no' => 'Nan',
	'optin-survey-intro' => 'Grant-marci d’èprovar la Bèta.
Nos amerians savêr cen que vos pensâd de noutra novèla entèrface, donc nos aprèciyerians que vos rempléd lo quèstionèro u chouèx ce-desot devant que clicar dessus « [[#leave|{{int:optin-submit-out}}]] ».',
	'optin-survey-question-likedislike' => 'Qu’est-o que vos éd amâ dens la Bèta ? Qu’est-o que vos éd pas amâ dens la Bèta ?',
	'optin-survey-question-whyoptout' => 'Porquè quitâd-vos la Bèta ? (Chouèsésséd tot cen que convint.)',
	'optin-survey-answer-whyoptout-hard' => 'Ére trop mâlésiê de l’utilisar.',
	'optin-survey-answer-whyoptout-didntwork' => 'Cen fonccione pas bien.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Cen s’est pas passâ coment prèvu.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'J’é pas amâ son aparence.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'J’é pas amâ les ongllètes novèles et la novèla disposicion.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'J’é pas amâ la bârra d’outils novèla.',
	'optin-survey-answer-whyoptout-other' => 'Ôtra rêson :',
	'optin-survey-question-explain' => 'Èxplicâd ou ben aprovondésséd voutra rèponsa ce-dessus :',
	'optin-survey-question-techfail' => 'Éd-vos rencontrâ des problèmos tècnicos en utilisent la Bèta ?',
	'optin-survey-question-techfail-ifyes' => 'Se ouè, èxplicâd ou ben aprovondésséd :',
	'optin-survey-question-usedtoolbar' => 'Éd-vos utilisâ la bârra d’outils dèroulâbla novèla por changiér una pâge ?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Se ouè, qu’éd-vos amâ et qu’éd-vos pas amâ a son propôs ?',
	'optin-survey-question-usedtoolbar-ifno' => 'Se nan, porquè ? L’utiliseréd-vos dens l’avegnir ? Èxplicâd.',
	'optin-survey-question-different' => 'Qu’est-o qu’arêt diu étre difèrent por que vos continuâd a utilisar la Bèta ?',
	'optin-survey-question-feedback' => 'Balyéd-nos voutron avis :',
	'optin-survey-question-browser' => 'Quint navigator utilisâd-vos ?',
	'optin-survey-answer-browser-other' => 'Ôtro navigator :',
	'optin-survey-question-os' => 'Quint sistèmo d’èxplouètacion utilisâd-vos ?',
	'optin-survey-answer-os-other' => 'Ôtro sistèmo d’èxplouètacion :',
	'optin-survey-question-res' => 'Quinta est la rèsolucion de voutron ècran ?',
	'optin-survey-question-changes' => 'Qu’ameriâd-vos vêre dens la Bèta dens l’avegnir (chouèsésséd tot cen que convint) ?',
	'optin-survey-answer-changes-nav' => 'Fâre que la navigacion g·ènèrala du seto seye ples simpla a utilisar.',
	'optin-survey-answer-changes-edittools' => 'Ôgmentar et mèlyorar los outils d’èdicion.',
	'optin-survey-answer-changes-upload' => 'Mèlyorar lo tèlèchargement de contegnu et de fichiérs multimèdia.',
	'optin-survey-answer-changes-richtext' => 'Dèvelopar des outils d’èdicion semblâblos ux èditors de tèxto enrechiê.',
	'optin-survey-answer-changes-lookfeel' => 'Changiér l’aparence du seto.',
	'optin-survey-answer-changes-predictability' => 'Mèlyorar la prèvisibilitât de la capacitât.',
	'optin-survey-answer-changes-custom' => 'Pèrmetre una ples granta pèrsonalisacion de l’entèrface.',
	'optin-survey-answer-changes-other' => 'Ôtro :',
);

/** Friulian (Furlan)
 * @author Klenje
 */
$messages['fur'] = array(
	'optin-title-optedout' => 'Prove Beta',
	'optin-survey-yes' => 'Sì',
	'optin-survey-no' => 'No',
);

/** Irish (Gaeilge)
 * @author Kwekubo
 */
$messages['ga'] = array(
	'optin-intro' => 'Tá Tionscnamh Inúsáideachta na Vicipéide ag obair chun inúsáideacht na Vicipéide a fheabhsú.
Ar mhaith leat ár Béite a thriail?',
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
	'optin-intro' => 'A Iniciativa de usabilidade da Wikipedia traballou arreo para mellorar a usabilidade da Wikipedia.
Quere probar Beta?',
	'optin-feedback-intro' => 'Grazas por probar Beta.
Queremos saber que pensa da nosa nova interface, polo que lle agradeceriamos que enchese a enquisa opcional que aparece a continuación.',
	'optin-feedback-back' => 'Se non quere encher a enquisa, pode volver a "$1".',
	'optin-success-in' => 'Xa está probando Beta.
Pode saír en calquera momento facendo clic na ligazón "{{int:optin-leave}}" que aparecerá na parte superior da pantalla.',
	'optin-leave-cancel' => 'Se quere seguir usando Beta, pode volver a "$1".',
	'optin-success-out' => 'Deixou de probar Beta.
Pode participar nas probas en calquera momento facendo clic na ligazón "{{int:optin-try}}" que aparecerá no canto superior dereito da pantalla.',
	'optin-success-feedback' => 'Grazas polos seus comentarios!',
	'optin-accept-short' => 'Fagámolo!',
	'optin-accept-long' => 'Probar Beta',
	'optin-accept-long-anon' => 'Acceda ao sistema e probe Beta',
	'optin-deny-short' => 'Non, grazas',
	'optin-deny-long' => 'Volver á páxina anterior',
	'optin-submit-out' => 'Deixar Beta',
	'optin-submit-feedback' => 'Enviar a opinión',
	'optin-survey-yes' => 'Si',
	'optin-survey-no' => 'Non',
	'optin-survey-intro' => 'Grazas por probar Beta.
Queremos saber que pensa da nosa nova interface, polo que lle agradeceriamos que enchese a enquisa opcional que aparece a continuación antes de premer en "[[#leave|{{int:optin-submit-out}}]]".',
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
	'optin-survey-answer-os-other' => 'Outro sistema operativo:',
	'optin-survey-question-res' => 'Cal é a resolución da súa pantalla?',
	'optin-survey-question-changes' => 'Que cambios lle gustaría ver en Beta no futuro (seleccione todas as que cumpran)?',
	'optin-survey-answer-changes-nav' => 'Facer a navegación xeral do sitio máis doada de usar.',
	'optin-survey-answer-changes-edittools' => 'Aumentar e mellorar as ferramentas de edición.',
	'optin-survey-answer-changes-upload' => 'Mellorar o contido e as cargas multimedia.',
	'optin-survey-answer-changes-richtext' => 'Desenvolver ferramentas de edición similares aos editores de texto.',
	'optin-survey-answer-changes-lookfeel' => 'Actualizar o aspecto e aparencia do sitio.',
	'optin-survey-answer-changes-predictability' => 'Mellorar a previsibilidade dos resultados.',
	'optin-survey-answer-changes-custom' => 'Permitir unha maior personalización por parte dos usuarios.',
	'optin-survey-answer-changes-other' => 'Outros:',
	'optin-improvements2' => '== Que se mellorou? ==
[[File:UsabilityToolbar.png|left|link=|Captura de pantalla da barra de ferramentas de edición mellorada.]]
[[File:UsabilityDialogs.png|left|link=|Captura de pantalla dos novos diálogos de xeración de contidos.]]
;Melloras na barra de ferramentas de edición
A barra de ferramentas de edición mellorada consegue que a edición de artigos sexa máis doada. As novas e melloradas iconas fan máis clara a identificación da acción que realiza cada ferramenta. As seccións expansibles reducen a desorde á vez que manteñen as ferramentas menos usadas a só un clic de distancia.
;Diálogos
Ao crear ligazóns ou táboas, simplemente con premer sobre a icona da ferramenta abonda para acceder a unha caixa de diálogo que axuda a crealas mediante un rápido formulario que cómpre encher.
;E máis
Tamén introducimos un gran número doutras moitas melloras como a navegación realzada, as lapelas en cascada ou a procura mellorada, por dicir algunha.
== Como involucrarse ==
Para coñecer máis sobre a Iniciativa de usabilidade visita a nosa [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative páxina principal].
<div style="clear:both"></div>',
);

/** Ancient Greek (Ἀρχαία ἑλληνικὴ)
 * @author Crazymadlover
 * @author Omnipaedista
 */
$messages['grc'] = array(
	'optin-survey-yes' => 'Ναί',
	'optin-survey-no' => 'Οὐ',
	'optin-survey-answer-whyoptout-other' => 'Αἰτία ἄλλη:',
	'optin-survey-answer-os-other' => 'Ἄλλον λειτουργικὸν σύστημα:',
	'optin-survey-answer-changes-other' => 'Ἄλλον:',
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
	'optin-survey-question-changes' => 'Weli Änderige wottsch gärn säh bi Beta in Zuechumpft (wehl bitte alli uus, wu s zuetrifft)?',
	'optin-survey-answer-changes-nav' => 'D Gsamtnavigation vu dr Site eifacher mache.',
	'optin-survey-answer-changes-edittools' => 'Bearbeitigswärchzyyg erwytere un verbessere.',
	'optin-survey-answer-changes-upload' => 'Inhalt un s Uffelade vu Medie verbessere.',
	'optin-survey-answer-changes-richtext' => 'Bearbeitigswärchzyyg entwickle, wu ähnlig sin wie d Rich-Text-Editore.',
	'optin-survey-answer-changes-lookfeel' => 'Erschynigsbild vu dr Site aktualisiere.',
	'optin-survey-answer-changes-predictability' => 'Vorhärsähbarkeit vu dr Leischtig verbessere.',
	'optin-survey-answer-changes-custom' => 'Greßeri Benutzeraapassige erlaube.',
	'optin-survey-answer-changes-other' => 'Anderi:',
	'optin-improvements2' => '== Was isch verbesseret wore? ==
[[File:UsabilityToolbar.png|left|link=|Bildschirmkopii vum verbesserete Wärchzyygschäschtli]]
[[File:UsabilityDialogs.png|left|link=|Bildschirmkopii vu dr neje Dialogfänschter bim erzyyge vu Inhalt]]
;Verbesseret Wärchzyygschäschtli
S verbesseret Wärchzyygschäschtli macht s eifacher, Artikel z bearbeite. Neji un verbessereti Icons mache s klarer, weli Aktion dur wel Wärchzyyg uusgfiert wird. Uusklappbari Abschnitt verringere s Durenand, wel wenig bruchti Wärchzyyg uusbländet wäre, aber mit eim Klick chenne brucht wäre. 
;Dialogfänschter
Bim Aalege vu Gleicher oder Tabäller, goht jetz bim Klicke uf e Wärchzyyg-Icon e Dialogfänschter uf, wu hilft Dir bim Aalege hilft.
;Un meh
Mir hän au no anderi Verbesserige yygfiert, wie z. B. e verbessereti Navigation, kaskadefermigi Tabälle, e verbesserti suech usw.

== Wie cha mer meh erfahre ==
Go meh erfahre iber d Benutzerfryndligkeit-Initiative gang bitte uf unsri [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative Homepage].
<div style="clear:both"></div>',
);

/** Manx (Gaelg)
 * @author Shimmin Beg
 */
$messages['gv'] = array(
	'optin-title-optedout' => 'Prowal Beta',
	'optin-title-optedin' => 'Faagail Beta',
	'optin-submit-feedback' => 'Cur aaveaghey',
	'optin-survey-yes' => 'Verrym',
	'optin-survey-no' => 'Cha derrym',
);

/** Hebrew (עברית)
 * @author Amire80
 * @author Rotemliss
 * @author YaronSh
 */
$messages['he'] = array(
	'optin' => 'הרשמה למיזם השמישות',
	'optin-desc' => 'אפשרות למשתמשים להירשם לגרסת הבטא, הכוללת את שיפורי השמישות האחרונים של מיזם השמישות.',
	'optin-title-optedout' => 'הרשמה לגרסת הבטא',
	'optin-title-optedin' => 'עזיבת גרסת הבטא',
	'optin-title-justoptedin' => 'ברוכים הבאים לגרסת הבטא',
	'optin-title-justoptedout' => 'תודה לכם על שניסיתם את גרסת הבטא',
	'optin-title-feedback' => 'משוב על גרסת הבטא',
	'optin-needlogin' => 'עליכם [$1 להיכנס לחשבון] כדי להירשם לגרסת הבטא.',
	'optin-intro' => 'מיזם השמישות של ויקיפדיה פעל רבות כדי לשפר את השמישות של ויקיפדיה.
האם תרצו לנסות את גרסת הבטא שלנו?',
	'optin-feedback-intro' => 'תודה לכם על שניסיתם את גרסת הבטא.
נשמח לדעת מה אתם חושבים על הממשק החדש שלנו, לכן נעריך זאת אם תמלאו את השאלון (האופציונלי) שלהלן.',
	'optin-feedback-back' => 'אם אינכם רוצים למלא את השאלון, באפשרותכם לחזור ל$1.',
	'optin-success-in' => 'כעת אתם מנסים את גרסת הבטא.
באפשרותכם לעזוב אותה בכל עת באמצעות לחיצה על הקישור "{{int:optin-leave}}" בראש הדף.',
	'optin-leave-cancel' => 'אם תרצו להמשיך להשתמש בגרסת הבטא, באפשרותכם לחזור ל$1.',
	'optin-success-out' => 'ביטלתם בהצלחה את הרשמתכם לגרסת הבטא.
באפשרותכם לנסות אותה שוב בכל עת באמצעות לחיצה על הקישור "{{int:optin-try}}" בראש הדף.',
	'optin-success-feedback' => 'תודה על המשוב!',
	'optin-accept-short' => 'בואו נעשה את זה!',
	'optin-accept-long' => 'נסו את גרסת הבטא',
	'optin-accept-long-anon' => 'היכנסו לחשבון ונסו את גרסת הבטא',
	'optin-deny-short' => 'לא תודה',
	'optin-deny-long' => 'חזרה לדף הקודם',
	'optin-submit-out' => 'עזיבת גרסת הבטא',
	'optin-submit-feedback' => 'מתן משוב',
	'optin-survey-yes' => 'כן',
	'optin-survey-no' => 'לא',
	'optin-survey-intro' => 'תודה לכם על שניסיתם את גרסת הבטא.
אנו רוצים לדעת מה אתם חושבים על הממשק החדש שלנו, לכן נעריך זאת אם תמלאו את הסקר האופציונלי שלהלן לפני שתלחצו על הכפתור "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'מה אהבתם בגרסת הבטא? מה לא אהבתם בגרסת הבטא?',
	'optin-survey-question-whyoptout' => 'למה אתם עוזבים את גרסת הבטא? (אנא בחרו את כל האפשרויות המתאימות.)',
	'optin-survey-answer-whyoptout-hard' => 'הוא היה קשה מדי לשימוש.',
	'optin-survey-answer-whyoptout-didntwork' => 'הוא לא פעל כפי שצריך.',
	'optin-survey-answer-whyoptout-notpredictable' => 'האתר פעל באופן בלתי צפוי.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'לא אהבתי את איך שהוא נראה.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'לא אהבתי את הפריסה והלשוניות החדשים.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'לא אהבתי את סרגל הכלים החדש.',
	'optin-survey-answer-whyoptout-other' => 'סיבה אחרת:',
	'optin-survey-question-explain' => 'אנא הסבירו או פרטו את תשובתכם לעיל:',
	'optin-survey-question-techfail' => 'האם אירעו תקלות טכניות כלשהן בעת השימוש בגרסת הבטא?',
	'optin-survey-question-techfail-ifyes' => 'אם כן, אנא פרטו או הסבירו:',
	'optin-survey-question-usedtoolbar' => 'האם השתמשתם בסרגל הכלים החדש המתרחב החדש כדי לערוך דף?',
	'optin-survey-question-usedtoolbar-ifyes' => 'אם כן, מה אהבתם לגביו? מה לא אהבתם לגביו?',
	'optin-survey-question-usedtoolbar-ifno' => 'אם לא, למה לא? האם תשתמשו בו בעתיד? אנא הסבירו.',
	'optin-survey-question-different' => 'מה יכולנו לעשות אחרת כדי שתמשיכו להשתמש בגרסת הבטא?',
	'optin-survey-question-feedback' => 'אנא כתבו לנו את כל ההערות האחרות, התהיות או המחשבות שיש לכם בשבילנו - כל משוב הוא שימושי:',
	'optin-survey-question-browser' => 'באיזה דפדפן אתם משתמשים?',
	'optin-survey-answer-browser-other' => 'דפדפן אחר:',
	'optin-survey-question-os' => 'באיזו מערכת הפעלה אתם משתמשים?',
	'optin-survey-answer-os-other' => 'מערכת הפעלה אחרת:',
	'optin-survey-question-res' => 'מהי רזולוציית המסך שלכם?',
	'optin-survey-question-changes' => 'אילו שינויים תרצו לראות בגרסת הבטא בעתיד (אנא בחרו את כל האפשרויות המתאימות)?',
	'optin-survey-answer-changes-nav' => 'הפיכת הניווט הכולל באתר לקל יותר לשימוש.',
	'optin-survey-answer-changes-edittools' => 'הרחבת ושיפור כלי העריכה.',
	'optin-survey-answer-changes-upload' => 'שיפור התוכן והעלאות המדיה.',
	'optin-survey-answer-changes-richtext' => 'פיתוח כלי עריכה הדומים לעורכי טקסט עשיר.',
	'optin-survey-answer-changes-lookfeel' => 'עדכון "המראה והתחושה" של האתר.',
	'optin-survey-answer-changes-predictability' => 'שיפור אפשרויות החיזוי של הביצועים.',
	'optin-survey-answer-changes-custom' => 'הוספת אפשרויות רבות יותר להתאמה אישית.',
	'optin-survey-answer-changes-other' => 'אחר:',
	'optin-improvements2' => '== מה שופר? ==
[[File:UsabilityToolbar.png|left|link=|צילום מסך של סרגל העריכה המשופר]]
[[File:UsabilityDialogs.png|left|link=|צילום מסך של תיבות דו שיח חדשות ליצירת תוכן]]
;סרגל כלים מורחב
הסרגל המורחב מקל על עריכת דפים.
סמלים חדשים ומשופרים מבהירים את הפעולה של כל כלי בסרגל.
חלקים מתרחבים מפחיתים את חוסר הסדר ומשאירים את הכלים שהשימוש בהם פחות תכוף במרחק לחיצה אחת.
;תיבות דו שיח
בעת יצירת קישורים או טבלאות, לחיצה על סמל הכלי פותחת תיבת דו שיח שעוזרת ליצור קישורים וטבלאות באמצעות מילוי טופס פשוט.
;ועוד
הוספנו מספר שיפורים אחרים כגון ניווט מורחב, לשוניות מדורגות, חיפוש משופר ועוד.

== איך להצטרף ==
למידע נוסף על מיזם השמישות, בקרו ב[http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative homepage דף הבית] שלנו.
<div style="clear:both"></div>',
);

/** Hindi (हिन्दी)
 * @author आलोक
 */
$messages['hi'] = array(
	'optin-accept-short' => 'चलो करते हैं!',
	'optin-deny-short' => 'नहीं, शुक्रिया',
	'optin-survey-yes' => 'हाँ',
	'optin-survey-no' => 'नहीं',
	'optin-survey-question-browser' => 'आप कौन से विचरक का इस्तेमाल करते हैं?',
	'optin-survey-answer-browser-other' => 'अन्य विचरक।',
	'optin-survey-question-os' => 'आपक कौन सी प्रचालन प्रणाली का इस्तेमाल करते हैं?',
	'optin-survey-answer-os-other' => 'अन्य प्रचालन प्रणाली:',
);

/** Croatian (Hrvatski)
 * @author Ex13
 */
$messages['hr'] = array(
	'optin' => 'Izbor Inicijative za uporabljivost',
	'optin-desc' => 'Omogućavam suradnicima isprobavanje Beta, najnovijeg poboljšanja upotrebljivosti Inicijative za uporabljivost.',
	'optin-title-optedout' => 'Isprobajte Beta',
	'optin-title-optedin' => 'Izađite iz Beta',
	'optin-title-justoptedin' => 'Dobrodošli na Beta',
	'optin-title-justoptedout' => 'Hvala što ste isprobali našu Beta',
	'optin-title-feedback' => 'Beta komentari',
	'optin-needlogin' => 'Morate se [$1 prijaviti] kako biste isprobali Beta.',
	'optin-intro' => 'Inicijativa za uporabljivost Wikipedije puno je radila na poboljšanju uporabljivosti Wikipedije. Želite li isprobati našu Beta?',
	'optin-feedback-intro' => 'Hvala što ste isprobali Beta. Željeli bismo znati što mislite o našem novom sučelju, stoga bismo cijenili ukoliko biste ispunili našu neobvezatnu anketu koja se nalazi u nastavku.',
	'optin-feedback-back' => 'Ukoliko ne želite ispuniti anketu, možete se vratiti na $1.',
	'optin-success-in' => 'Sada isprobavate Beta. U bilo kojem trenutku možete se odlučiti vratiti nazad klikom na poveznicu "{{int:optin-leave}}" koja se nalazi na vrhu zaslona.',
	'optin-leave-cancel' => 'Ako želite nastaviti koristiti Beta, možete se vratiti na $1.',
	'optin-success-out' => 'Uspješno ste izašli iz Beta. 
Možete je u svakom trenutku ponovno isprobati klikom na poveznicu "{{int:optin-try}} u gornjem desnom dijelu zaslona.',
	'optin-success-feedback' => 'Hvala Vam na Vašem komentaru!',
	'optin-accept-short' => 'Idemo isprobati!',
	'optin-accept-long' => 'Isprobajte Beta',
	'optin-accept-long-anon' => 'Prijavite se i isprobajte Beta',
	'optin-deny-short' => 'Ne hvala',
	'optin-deny-long' => 'Povratak na prethodnu stranicu',
	'optin-submit-out' => 'Izađite iz Beta',
	'optin-submit-feedback' => 'Ostavite komentare',
	'optin-survey-yes' => 'Da',
	'optin-survey-no' => 'Ne',
	'optin-survey-intro' => 'Hvala što ste isprobali Beta.
Željeli bismo znati što mislite o našem novom sučelju, stoga bismo cijenili ukoliko biste ispunili našu neobvezatnu anketu koja se nalazi u nastavku prije nego kliknete na "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Što Vam se svidjelo na Beta? Što Vam se nije svidjelo na Beta?',
	'optin-survey-question-whyoptout' => 'Zašto napuštate Beta? (molimo označite sve što se može primijeniti.)',
	'optin-survey-answer-whyoptout-hard' => 'Bilo je preteško za uporabu.',
	'optin-survey-answer-whyoptout-didntwork' => 'Nije ispravno radilo.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Nije radilo kako je predviđeno.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Ne sviđa mi se kako izgleda.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Nije mi se svidio novi izgled i kartice.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Nije mi se svidjela nova alatna traka.',
	'optin-survey-answer-whyoptout-other' => 'Drugi razlog:',
	'optin-survey-question-explain' => 'Molim objasnite ili pojasnite Vaš odgovor iznad:',
	'optin-survey-question-techfail' => 'Jeste li primjetili tehničke probleme dok ste rabili Beta?',
	'optin-survey-question-techfail-ifyes' => 'Ako da, molimo objasnite ili pojasnite:',
	'optin-survey-question-usedtoolbar' => 'Jeste li koristiti novu proširivu alatnu traku za uređivanje stranice?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Ako da, što Vam se svidjelo? Što Vam se nije svidjelo?',
	'optin-survey-question-usedtoolbar-ifno' => 'Ako ne, zašto ne? Hoćete li u budućnosti? Molimo objasnite.',
	'optin-survey-question-different' => 'Što možemo učiniti drugačije da nastavite rabiti Beta?',
	'optin-survey-question-feedback' => 'Molimo Vas da nas izvjestite o bilo kojim drugim komentarima, mišljenjima i gledištima - sve povratne informacije su korisne!:',
	'optin-survey-question-browser' => 'Koji preglednik rabite?',
	'optin-survey-answer-browser-other' => 'Drugi preglednik:',
	'optin-survey-question-os' => 'Koji operativni sustav rabite?',
	'optin-survey-answer-os-other' => 'Drugi operativni sustav:',
	'optin-survey-question-res' => 'Koja je rezolucija Vašeg zaslona?',
	'optin-survey-question-changes' => 'Koje promjene biste željeli vidjeti u Beta u budućnosti (odaberi sve što se može primijeniti)?',
	'optin-survey-answer-changes-nav' => 'Cjelokupnu navigaciju stranice učiniti jednostavnijom za uporabu.',
	'optin-survey-answer-changes-edittools' => 'Povećati i poboljšati alatnu traku.',
	'optin-survey-answer-changes-upload' => 'Poboljšati postavljanje sadržaja i medija.',
	'optin-survey-answer-changes-richtext' => 'Razviti alatnu traku sličnu naprednijim uređivačima teksta.',
	'optin-survey-answer-changes-lookfeel' => 'Ažurirati "vidi i osjeti" stranice.',
	'optin-survey-answer-changes-predictability' => 'Poboljšati predvidljivost izvedbe.',
	'optin-survey-answer-changes-custom' => 'Omogućiti suradniku veću mogućnost prilagođavanja.',
	'optin-survey-answer-changes-other' => 'Ostalo:',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'optin' => 'Iniciatiwa wužiwajomnosće wuzwolenje',
	'optin-desc' => 'Zmóžnja wužiwarjam Betu wupruwować, najnowše polěpšenja wužiwajomnosće iniciatiwy wužiwajomnosće.',
	'optin-title-optedout' => 'Betu wupruwować',
	'optin-title-optedin' => 'Betu wopušćić',
	'optin-title-justoptedin' => 'Witaj do Bety',
	'optin-title-justoptedout' => 'Dźakujemy so, zo sy Betu wupruwował',
	'optin-title-feedback' => 'Powěsć dać',
	'optin-needlogin' => 'Dyrbiš so [$1 přizjewić], zo by Betu wupruwował',
	'optin-intro' => 'Iniciatiwa wužiwajomnosće Wikipedije intensiwnje na polěpšenju wužiwajomnosće Wikipedije dźěła. By rady Betu wupruwował?',
	'optin-feedback-intro' => 'Dźakujemy so za wupruwowanje Bety.
Bychmy rady wědźeli, što mysliš wo našim nowym wužiwarskim powjerchu, bychmy so wjeselili, jeli by opcionelny naprašnik deleka wupjelnił.',
	'optin-feedback-back' => 'Jeli nochceš formular wupjelnić, móžeš so k slědowacej stronje wróćić: $1.',
	'optin-success-in' => 'Wupruwuješ nětko Betu. Móžeš ju kóždy čas přez kliknjenje na wotkaz "{{int:optin-leave}}" horjeka na wobrazowce wopušćić.',
	'optin-leave-cancel' => 'Jeli rady chceš Betu dale wužiwać, móžeš so k slědowacej stronje wróćić: $1.',
	'optin-success-out' => 'Sy Betu wuspěšnje wopušćił. Móžeš ju kóždy čas přez kliknjenje na wotkaz "{{int:optin-try}}" horjeka naprawo na wobrazowce wupruwować.',
	'optin-success-feedback' => 'Dźakujemy so za twoju powěsć!',
	'optin-accept-short' => 'Nětk pak skoku!',
	'optin-accept-long' => 'Betu wupruwować',
	'optin-accept-long-anon' => 'Přizjewić so a Betu wupruwować',
	'optin-deny-short' => 'Ně, dźakujemy so',
	'optin-deny-long' => 'wróćo k předchadnej stronje',
	'optin-submit-out' => 'Betu wopušćić',
	'optin-submit-feedback' => 'Powěsć dać',
	'optin-survey-yes' => 'Haj',
	'optin-survey-no' => 'Ně',
	'optin-survey-intro' => 'Dźakujemy so za wupruwowanje Bety.

Bychmy rady wědźeli, što mysliš wo našim nowym wužiwarskim powjerchu, bychmy so wjeselili, jeli by opcionelny naprašnik deleka wupjelnił, prjedy hač kliknješ na  "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Što so ći na Beće lubi? Što so ći na Beće njelubi?',
	'optin-survey-question-whyoptout' => 'Čehodla wopušćeš tutu betu? (prošu wubjer wšo prawe)',
	'optin-survey-answer-whyoptout-hard' => 'Wužiwanje je překomplikowane.',
	'optin-survey-answer-whyoptout-didntwork' => 'To njeje porjadnje fungowało.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Njefunguje na předwidźomne wašnje.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Napohlad njeje so mi spodobał.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Njespodobaja so mi nowe rajtarki a wuhotowanje.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Nowa gratowa lajsta njeje so mi njespodobała.',
	'optin-survey-answer-whyoptout-other' => 'Druha přičina:',
	'optin-survey-question-explain' => 'Prošu wujasń abo wudźěłaj swoju hornju wotmołwu:',
	'optin-survey-question-techfail' => 'Sy při wupruwowanju Bety na techniske zmylki {{GENDER:User|storčił|storčiła|storčił}}?',
	'optin-survey-question-techfail-ifyes' => 'Jeli haj, prošu wudźěłaj abo wujasń:',
	'optin-survey-question-usedtoolbar' => 'Sy nowu rozfałdujomnu gratowu lajstu za wobdźěłowanje stronow wupruwował?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Jeli haj, što so ći na tym spodoba? Što so ći na tym njespodoba?',
	'optin-survey-question-usedtoolbar-ifno' => 'Jeli nic, čehodla nic? Budźeš to w přichodźe činić? Prošu wujasń to.',
	'optin-survey-question-different' => 'Što móžemy hišće hinak činić, zo by Betu dale wužiwał?',
	'optin-survey-question-feedback' => 'Zdźěl nam prošu swoje měnjenja:',
	'optin-survey-question-browser' => 'Kotry wobhladowak wužiwaš?',
	'optin-survey-answer-browser-other' => 'Druhi wobhladowak:',
	'optin-survey-question-os' => 'Kotry dźěłowy system wužiwaš?',
	'optin-survey-answer-os-other' => 'Druhi dźěłowy system:',
	'optin-survey-question-res' => 'Kotre je rozeznaće twojeje wobrazowki?',
	'optin-survey-question-changes' => 'Kotre změny by rady w Beće w přichodźe widźał (prošu wubjer wšě, kotrež trjecha)?',
	'optin-survey-answer-changes-nav' => 'Cyłkownu nawigaciju sydła zjednorić.',
	'optin-survey-answer-changes-edittools' => 'Wobdźěłowanske nastroje rozšěrić a polěpšić.',
	'optin-survey-answer-changes-upload' => 'Wobsah a nahraća medijow polěpšić.',
	'optin-survey-answer-changes-richtext' => 'Wobdźěłowanske nastroje podobne tym editorow rich-teksta wuwić.',
	'optin-survey-answer-changes-lookfeel' => 'Zwonkowny napohlad sydła zaktualizować',
	'optin-survey-answer-changes-predictability' => 'Předwidźomnosć wukona polěpšić.',
	'optin-survey-answer-changes-custom' => 'Wjace wužiwarskich přiměrjenjow dowolić',
	'optin-survey-answer-changes-other' => 'Druhe:',
	'optin-improvements2' => '== Što bu polěpšene? ==
[[File:UsabilityToolbar.png|left|link=|Foto wobrazowki polěpšeneje wobdźěłowanskeje lajsty]]
[[File:UsabilityDialogs.png|left|link=|Foto wobrazowki nowych dialogow za wutworjenje wobsaha]]
;Polěpsena lajsta
Polěpšena lajsta wosnadnja wobdźěłowanje nastawkow.
Nowe a polěpšene wobrazowki lěpje znazornjeja, kotru akciju kóždy nastroj wuwjedźe.
Rozfałdowane wotrězki redukuja šmjatk a najebać toho su zrědka wužiwane nastroje jenož jedne kliknjenje zdalene.
;Dialog
Hdyž chceš wotkazy abo tabele wutworić, kliknjenje na nastrojowy wobrazk wočinja nětko dialogowy kašćik, kotryž ći pomha wotkazy a tabele přez wupjelnjenje jednoreho formulara wutworić.
;A wjace
Smy zawjedli ličbu dalšich polěpšenjow, kaž na př. polěpšenu nawigaciju, kaskadowace rajtarki, polěpšene pytanje a wjace.
== Kak móžeš sobu činić ==
Zo by wjace wo iniciatiwje wužiwajomnosce zhonił, wopytaj prošu našu
[http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative startowu stronu].
<div style="clear:both"></div>',
);

/** Hungarian (Magyar)
 * @author Bdamokos
 * @author Dani
 * @author Glanthor Reviol
 * @author Hunyadym
 * @author Tgr
 */
$messages['hu'] = array(
	'optin' => 'Kipróbálom az új felületet',
	'optin-desc' => 'Lehetővé teszi a szerkesztőknek a béta verzió kipróbálását, a használhatóság fejlesztése kezdeményezés legfrissebb fejlesztéseivel',
	'optin-title-optedout' => 'A béta kipróbálása',
	'optin-title-optedin' => 'Kilépés a bétából',
	'optin-title-justoptedin' => 'Üdvözlünk a bétában',
	'optin-title-justoptedout' => 'Köszönjük, hogy kipróbáltad a bétát',
	'optin-title-feedback' => 'Visszajelzés a bétáról',
	'optin-needlogin' => 'A béta kipróbálásához [$1 be kell jelentkezned].',
	'optin-intro' => 'A Wikipedia Usability Initiative keményen dolgozott azon, hogy javítsa a Wikipédia használhatóságát.
Szeretnéd kipróbálni a bétánkat?',
	'optin-feedback-intro' => 'Köszönjük, hogy kipróbáltad a bétát.
Szeretnénk tudni, hogy mit gondolsz az új felületről, így örülnénk neki, ha kitöltenéd az alábbi kérdőívet.',
	'optin-feedback-back' => 'Ha nem szeretnéd kitölteni a kérdőívet, visszatérhetsz a(z) $1 lapra.',
	'optin-success-in' => 'Jelenleg részt veszel a bétatesztben.
Bármikor elhagyhatod a tesztet az oldal tetején található „{{int:optin-leave}}” linkre kattintva.',
	'optin-leave-cancel' => 'Ha továbbra is szeretnéd használni a bétát, akkor visszatérhetsz a(z) $1 lapra.',
	'optin-success-out' => 'Sikeresen elhagytad a bétatesztet.
Bármikor újra kipróbálhatod a bétaváltozatot a lap tetején található „{{int:optin-try}}” linkre kattintva.',
	'optin-success-feedback' => 'Köszönjük a visszajelzésed!',
	'optin-accept-short' => 'Kipróbálom!',
	'optin-accept-long' => 'A béta kipróbálása',
	'optin-accept-long-anon' => 'Jelentkezz be, és próbáld ki a bétát',
	'optin-deny-short' => 'Köszönöm, nem',
	'optin-deny-long' => 'Visszatérés az előző lapra',
	'optin-submit-out' => 'A bétateszt befejezése',
	'optin-submit-feedback' => 'Visszajelzés küldése',
	'optin-survey-yes' => 'Igen',
	'optin-survey-no' => 'Nem',
	'optin-survey-intro' => 'Köszönjük, hogy kipróbálod a bétatesztet!
Szeretnénk tudni, hogy mit gondolsz az új felületről ezért örülnénk, ha kitöltenéd az alábbi nem kötelező kérdőívet mielőtt a „[[#leave|{{int:optin-submit-out}}]]”-ra kattintanál.',
	'optin-survey-question-likedislike' => 'Mi tetszett a bétaváltozatban? Mi nem tetszett a bétaváltozatban?',
	'optin-survey-question-whyoptout' => 'Miért hagyod el a bétatesztet? (Kérlek válaszd ki az összet amely vonatkozik rád.)',
	'optin-survey-answer-whyoptout-hard' => 'Túl nehéz volt használni.',
	'optin-survey-answer-whyoptout-didntwork' => 'Nem működött megfelelően.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Nem működött előrejelezhető módon.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Nem tetszik, ahogy kinéz.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Nem tetszenek az új fülek és az elrendezés.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Nem tetszik az új szerkesztő-eszköztár.',
	'optin-survey-answer-whyoptout-other' => 'Más indok:',
	'optin-survey-question-explain' => 'Kérlek magyarázd meg vagy fejtsd ki a fenti válaszod:',
	'optin-survey-question-techfail' => 'Tapasztaltál műszaki hibákat a bétateszt használata közben?',
	'optin-survey-question-techfail-ifyes' => 'Ha igen, kérjük fejtsd ki őket:',
	'optin-survey-question-usedtoolbar' => 'Használtad az új kinyitható eszköztárat a lapok szerkesztéséhez?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Ha igen, mi tetszett benne? Mi nem tetszett benne?',
	'optin-survey-question-usedtoolbar-ifno' => 'Ha nem, miért nem? Használod majd a jövőben? Kérjük magyarázd el.',
	'optin-survey-question-different' => 'Mit tehettünk volna másképpen, hogy folytasd a bétaváltozat használatát?',
	'optin-survey-question-feedback' => 'Kérlek írd le minden egyéb megjegyzésed, aggodalmad vagy gondolatod számunkra - minden visszajelzés hasznos:',
	'optin-survey-question-browser' => 'Melyik böngészőt használod?',
	'optin-survey-answer-browser-other' => 'Más böngésző:',
	'optin-survey-question-os' => 'Melyik operációs rendszert használod?',
	'optin-survey-answer-os-other' => 'Egyéb operációs rendszer:',
	'optin-survey-question-res' => 'Milyen felbontású a monitorod?',
	'optin-survey-question-changes' => 'Milyen változásokat szeretnél látni a bétaváltozatban a jövőben (válaszd ki az összeset)?',
	'optin-survey-answer-changes-nav' => 'Tegyétek könnyebben használhatóvá az oldalon való általános navigálást.',
	'optin-survey-answer-changes-edittools' => 'Bővítsétek és fejlesszétek a szerkesztési eszközöket.',
	'optin-survey-answer-changes-upload' => 'Tartalom és médiafájlok feltöltésének fejlesztése.',
	'optin-survey-answer-changes-richtext' => 'Fejlesszetek ki a formázott szöveg szerkesztőkhöz hasonló szerkesztési eszközöket.',
	'optin-survey-answer-changes-lookfeel' => 'Frissítsétek az oldal kinézetét.',
	'optin-survey-answer-changes-predictability' => 'Fejlesszétek a teljesítmény előrejelezhetőségét.',
	'optin-survey-answer-changes-custom' => 'Tegyetek lehetővé nagyobb felhasználói szabadságot a beállításokban.',
	'optin-survey-answer-changes-other' => 'Más:',
	'optin-improvements2' => '== Mi fejlődött? ==

[[File:UsabilityToolbar.png|left|link=|Képernyőkép a fejlettebb szerkesztő-eszköztárról]]
[[File:UsabilityDialogs.png|left|link=|Képernyőkép az új tartalomgeneráló párbeszédablakokról]]

;Fejlettebb szerkesztő-eszköztár
Az új szerkesztő-eszköztár könnyebbé teszi a szócikkek szerkesztését.
Új és továbbfejlesztett ikonok teszik egyértelműbbé az egyes eszközök funkcióját.
A kinyitható eszköztárak csökkentik a zsúfoltságot, de a kevésbé gyakran használt eszközök is csak egy kattintásnyira maradnak.

;Párbeszédablakok
Hivatkozások vagy táblázatok készítésénél a megfelelő gombra kattintva ezentúl egy párbeszédablak nyílik meg, ami segít a létrehozásukban egy űrlap kitöltése után.

;És sok más újdonság
Számos egyéb javítást eszközöltünk, mint például a fejlettebb navigáció, legördülő lapfülek, okosabb kereső…

== Mi fejlődött? ==

[[File:UsabilityToolbar.png|left|link=|Képernyőkép a fejlettebb szerkesztő-eszköztárról]]
[[File:UsabilityDialogs.png|left|link=|Képernyőkép az új tartalomgeneráló párbeszédablakokról]]

;Fejlettebb szerkesztő-eszköztár
Az új szerkesztő-eszköztár könnyebbé teszi a szócikkek szerkesztését.
Új és továbbfejlesztett ikonok teszik egyértelműbbé az egyes eszközök funkcióját.
A kinyitható eszköztárak csökkentik a zsúfoltságot, de a kevésbé gyakran használt eszközök is csak egy kattintásnyira maradnak.

;Párbeszédablakok
Hivatkozások vagy táblázatok készítésénél a megfelelő gombra kattintva ezentúl egy párbeszédablak nyílik meg, ami segít a létrehozásukban egy űrlap kitöltése után.

;És sok más újdonság
Számos egyéb javítást eszközöltünk, mint például a fejlettebb navigáció, legördülő lapfülek, okosabb kereső…

== Hogyan vehetek részt benne? ==

Ha szeretnél többet megtudni a Usability Initiative-ről, látogasd meg a [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative honlapunkat].
<div style="clear:both"></div>',
);

/** Armenian (Հայերեն)
 * @author Teak
 * @author Xelgen
 */
$messages['hy'] = array(
	'optin' => 'Օգտագործելիության նախաձեռնության մասնակցություն',
	'optin-title-optedout' => 'Փորձել Բետա տարբերակը',
	'optin-title-optedin' => 'Ելնել Բետայից',
	'optin-title-justoptedin' => 'Բարի Գալուստ Բետա',
	'optin-title-justoptedout' => 'Շնորհակալություն Բետա տարբերակը փորձելու համար',
	'optin-title-feedback' => 'Կարծիք հայտնել Բետայի մասին',
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
	'optin-title-feedback' => 'Da nos tu opinion',
	'optin-needlogin' => 'Tu debe [$1 aperir un session] pro probar Beta',
	'optin-intro' => 'Le Initiativa de Usabilitate de Wikipedia ha laborate duro pro augmentar le usabilitate de Wikipedia.
Vole tu probar Beta?',
	'optin-feedback-intro' => 'Gratias pro essayar Beta.
Nos vole saper lo que tu pensa de nostre nove interfacie, dunque nos appreciarea si tu completa le questionario optional ci infra.',
	'optin-feedback-back' => 'Si tu non vole completar le questionario, tu pote retornar a $1.',
	'optin-success-in' => 'Tu ha ora comenciate a probar Beta.
Tu pote disactivar lo a omne tempore con le ligamine "{{int:optin-leave}}" in alto del schermo.',
	'optin-leave-cancel' => 'Si tu vole continuar a usar Beta, tu pote retornar a $1',
	'optin-success-out' => 'Tu ha quitate Beta con successo.
Tu pote reactivar lo a omne tempore con le ligamine "{{int:optin-try}}" in alto del schermo.',
	'optin-success-feedback' => 'Gratias pro dar nos tu opinion!',
	'optin-accept-short' => 'In avante!',
	'optin-accept-long' => 'Probar Beta',
	'optin-accept-long-anon' => 'Aperir session e essayar Beta',
	'optin-deny-short' => 'No, gratias',
	'optin-deny-long' => 'retornar al previe pagina',
	'optin-submit-out' => 'Quitar Beta',
	'optin-submit-feedback' => 'Dar opinion',
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
	'optin-survey-question-usedtoolbar-ifno' => 'Si non, proque non? Lo usara tu in le futuro? Per favor explica.',
	'optin-survey-question-different' => 'Que poteva nos haber facite alteremente a fin que tu continuarea a usar nostre Beta?',
	'optin-survey-question-feedback' => 'Face nos cognoscer altere commentos, preoccupationes o pensamentos - omne opinion es utile:',
	'optin-survey-question-browser' => 'Qual navigator usa tu?',
	'optin-survey-answer-browser-other' => 'Altere navigator:',
	'optin-survey-question-os' => 'Qual systema de operation usa tu?',
	'optin-survey-answer-os-other' => 'Altere systema de operation:',
	'optin-survey-question-res' => 'Qual es le resolution de tu schermo?',
	'optin-survey-question-changes' => 'Que cambios vole tu vider in le Beta in le futuro (selige tote le optiones applicabile)?',
	'optin-survey-answer-changes-nav' => 'Render le navigation general del sito plus facile de usar.',
	'optin-survey-answer-changes-edittools' => 'Augmentar e meliorar le instrumentos de modification.',
	'optin-survey-answer-changes-upload' => 'Meliora le incargamento de contento e multimedia.',
	'optin-survey-answer-changes-richtext' => 'Disveloppar instrumentos de modification similar al editores de texto inricchite.',
	'optin-survey-answer-changes-lookfeel' => 'Renovar le apparentia general del sito.',
	'optin-survey-answer-changes-predictability' => 'Meliorar le previsibilitate del prestationes.',
	'optin-survey-answer-changes-custom' => 'Permitter un personalisation plus ample al usator.',
	'optin-survey-answer-changes-other' => 'Altere:',
	'optin-improvements2' => '== Que ha essite meliorate? ==
[[File:UsabilityToolbar.png|left|link=|Captura de schermo del instrumentario de modification meliorate]]
[[File:UsabilityDialogs.png|left|link=|Captura de schermo del nove dialogos pro generar contento]]
;Instrumentario meliorate
Le instrumentario meliorate rende plus facile le modification de articulos.
Le icones nove e melior clarifica le action que cata instrumento exeque.
Le sectiones plicabile simplifica le apparentia, mantenente le instrumentos minus usate disponibile a solo un clic de distantia.
;Dialogo
Pro crear ligamines o tabellas, il suffice completar un formulario simple, le qual tu pote facer apparer in un fenestra con un clic super le icone de utensile.
;E plus
Nos ha introducite plure altere meliorationes como le navigation meliorate, schedas in cascada, recerca meliorate, e plus.
== Como participar ==
Pro ulterior informationes super le Initiativa de Usabilitate, visita nostre [http://usability.wikimedia.org sito].
<div style="clear:both"></div>',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 * @author Irwangatot
 * @author IvanLanin
 * @author Kenrick95
 * @author Rex
 */
$messages['id'] = array(
	'optin' => 'Bergabung dengan Proyek Inisiatif Kebergunaan',
	'optin-desc' => 'Memungkinkan pengguna untuk bergabung dengan peningkatan kebergunaan dalam Proyek Inisiatif Kebergunaan versi Beta.',
	'optin-title-optedout' => 'Coba Beta',
	'optin-title-optedin' => 'Tinggalkan Beta',
	'optin-title-justoptedin' => 'Selamat datang di Beta',
	'optin-title-justoptedout' => 'Terima kasih telah mencoba Beta',
	'optin-title-feedback' => 'Umpan balik Beta',
	'optin-needlogin' => 'Anda harus [$1 masuk log] untuk mencoba Beta.',
	'optin-intro' => 'Proyek Inisiatif Kebergunaan Wikipedia berusaha untuk mengembangkan kegunaan dari Wikipedia. Apakah Anda ingin mencoba Beta?',
	'optin-feedback-intro' => 'Terima kasih telah mencoba Beta.
Kami ingin tahu apa pendapat Anda tentang antarmuka baru kami, sehingga kami sangat menghargai jika Anda mengisi jajak pendapat opsional di bawah ini .',
	'optin-feedback-back' => 'Jika Anda tidak ingin mengisi survei, Anda dapat kembali ke $1.',
	'optin-success-in' => 'Anda saat ini sedang mencoba Beta.
Anda dapat keluar kapan saja dengan menekan pranala "{{int:optin-submit-out}}" di bagian atas halaman.',
	'optin-leave-cancel' => 'Jika Anda ingin terus menggunakan Beta, Anda dapat kembali ke $1.',
	'optin-success-out' => 'Anda telah berhasil meninggalkan Beta.
Anda dapat bergabung kembali kapan saja dengan menekan pranala "{{int:optin-submit-in}}" di bagian atas halaman.',
	'optin-success-feedback' => 'Terima kasih atas umpan balik Anda!',
	'optin-accept-short' => 'Ayo lakukan!',
	'optin-accept-long' => 'Coba Beta',
	'optin-accept-long-anon' => 'Masuk log dan coba Beta',
	'optin-deny-short' => 'Tidak, terima kasih',
	'optin-deny-long' => 'Kembali ke halaman sebelum',
	'optin-submit-out' => 'Tinggalkan Beta',
	'optin-submit-feedback' => 'Berikan umpan balik',
	'optin-survey-yes' => 'Ya',
	'optin-survey-no' => 'Tidak',
	'optin-survey-intro' => 'Terima kasih telah mencoba Beta.
Kami ingin mengetahui bagaimana pendapat Anda mengenai antarmuka baru kami, karenanya kami akan sangat menghargai jika Anda mengisi survei di bawah ini sebelum mengklik "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Apa yang Anda sukai tentang Beta? Apa yang Anda tidak sukai tentang Beta?',
	'optin-survey-question-whyoptout' => 'Mengapa Anda ingin meninggalkan Beta? (Pilihlah semua yang Anda anggap benar.)',
	'optin-survey-answer-whyoptout-hard' => 'Terlalu sulit untuk digunakan.',
	'optin-survey-answer-whyoptout-didntwork' => 'Tidak berfungsi dengan baik.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Tidak dapat diprediksi.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Saya tidak suka dengan penampilannya.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Saya tidak suka dengan tab baru dan tampilannya.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Saya tidak menyukai kotak peralatan yang baru.',
	'optin-survey-answer-whyoptout-other' => 'Alasan lain:',
	'optin-survey-question-explain' => 'Harap menjelaskan atau menguraikan jawaban Anda di atas:',
	'optin-survey-question-techfail' => 'Pernahkah anda mengalami kesalahan teknis ketika menggunakan Beta?',
	'optin-survey-question-techfail-ifyes' => 'Jika iya, silakan uraikan atau jabarkan:',
	'optin-survey-question-usedtoolbar' => 'Apakah anda menggunakan kotak peralatan baru yang dapat diperluas untuk menyunting halaman?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Jika iya, apa yang kamu sukai? Apa yang Anda tidak sukai?',
	'optin-survey-question-usedtoolbar-ifno' => 'Jika tidak, mengapa tidak? Apakah akan anda lakukan kemudian? Tolong jelaskan.',
	'optin-survey-question-different' => 'Apa yang bisa kita lakukan dengan cara yang berbeda untuk menjaga Anda menggunakan Beta?',
	'optin-survey-question-feedback' => 'Beritahukan kami tanggapan Anda:',
	'optin-survey-question-browser' => 'Penjelajah web apa yang Anda gunakan?',
	'optin-survey-answer-browser-other' => 'Penjelajah web lainnya:',
	'optin-survey-question-os' => 'Sistem operasi apa yang Anda gunakan?',
	'optin-survey-answer-os-other' => 'Sistem operasi lain:',
	'optin-survey-question-res' => 'Berapa besar resolusi layar Anda?',
	'optin-survey-question-changes' => 'Perubahan apa yang ingin Anda lihat dalam Beta di masa mendatang (silakan pilih yang sesuai)?',
	'optin-survey-answer-changes-nav' => 'Membuat navigasi keseluruhan situs lebih mudah digunakan.',
	'optin-survey-answer-changes-edittools' => 'Menambah dan memperbanyak alat penyuntingan.',
	'optin-survey-answer-changes-upload' => 'Meningkatkan pemuatan konten dan media.',
	'optin-survey-answer-changes-richtext' => 'Mengembangkan perangkat penyuntingan mirip dengan penyuntingan teks biasa.',
	'optin-survey-answer-changes-lookfeel' => 'Memperbarui "tampilan dan nuansa" dari situs.',
	'optin-survey-answer-changes-predictability' => 'Meningkatkan prediktabilitas dari kinerja.',
	'optin-survey-answer-changes-custom' => 'Memperbolehkan Pengguna yang lebih tinggi untuk kustomisasi',
	'optin-survey-answer-changes-other' => 'Lain-lain:',
	'optin-improvements2' => '== Apa yang telah diperbaiki? ==
[[Berkas:UsabilityToolbar.png|left|link=|Screenshot of the enhanced edit toolbar]]
[[Berkas:UsabilityDialogs.png|left|link=|Screenshot of the new content generation dialogs]]',
);

/** Ido (Ido)
 * @author Malafaya
 */
$messages['io'] = array(
	'optin-title-optedout' => 'Probez Beta',
	'optin-title-optedin' => 'Livez Beta',
	'optin-title-justoptedin' => 'Bonveno a Beta',
	'optin-title-justoptedout' => 'Danko pro probar nia Beta',
	'optin-needlogin' => 'Vu mustas [$1 enirar] por probar Beta.',
	'optin-accept-short' => 'Yes, komencez!',
	'optin-accept-long' => 'Probez Beta',
	'optin-accept-long-anon' => 'Enirez e probez Beta',
	'optin-deny-short' => 'No, danko',
	'optin-deny-long' => "Retrovenar a l'antea pagino",
	'optin-submit-out' => 'Livez Beta',
	'optin-survey-yes' => 'Yes',
	'optin-survey-no' => 'No',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Me ne prizis olua aspekto.',
	'optin-survey-answer-whyoptout-other' => 'Altra motivo:',
	'optin-survey-answer-changes-other' => 'Altra:',
);

/** Icelandic (Íslenska)
 * @author Spacebirdy
 */
$messages['is'] = array(
	'optin-title-optedout' => 'Prófa beta-útgáfu',
	'optin-accept-long' => 'Prófa beta-útgáfu',
	'optin-deny-short' => 'Nei takk',
);

/** Italian (Italiano)
 * @author Darth Kule
 * @author Melos
 * @author Nemo bis
 * @author Stefano-c
 * @author Una giornata uggiosa '94
 */
$messages['it'] = array(
	'optin' => 'Adesione al progetto usabilità',
	'optin-desc' => 'Consente agli utenti di provare la beta, gli ultimi miglioramenti del progetto usabilità.',
	'optin-title-optedout' => 'Prova la Beta',
	'optin-title-optedin' => 'Lascia Beta',
	'optin-title-justoptedin' => 'Benvenuto in Beta',
	'optin-title-justoptedout' => 'Grazie per aver provato la beta',
	'optin-title-feedback' => 'Feedback della Beta',
	'optin-needlogin' => "È necessario [$1 effettuare l'accesso] per provare Beta",
	'optin-intro' => "Il progetto usabilità di Wikipedia sta lavorando intensamente per migliorare l'usabilità di Wikipedia.
Vuoi provare la nostra beta?",
	'optin-feedback-intro' => 'Ti ringraziamo per aver provato la Beta.
Vorremmo sapere cosa ne pensi della nostra nuova interfaccia, quindi apprezzeremmo se compilassi il sondaggio facoltativo qui sotto.',
	'optin-feedback-back' => 'Se non si desidera compilare il sondaggio, si può tornare a $1.',
	'optin-success-in' => 'Ora stai provando la Beta.
Puoi uscirne in qualsiasi momento facendo clic sul link "{{int:optin-leave}}" nella parte superiore dello schermo.',
	'optin-leave-cancel' => 'Se si desidera continuare a usare Beta è possibile tornare a $1',
	'optin-success-out' => 'Hai abbandonato la beta.
È possibile provarla nuovamente in qualsiasi momento facendo clic sul collegamento "{{int:optin-try}}" in alto a destra dello schermo.',
	'optin-success-feedback' => 'Grazie per il tuo feedback!',
	'optin-accept-short' => 'Prova!',
	'optin-accept-long' => 'Prova la Beta',
	'optin-accept-long-anon' => 'Accedi e prova la Beta',
	'optin-deny-short' => 'No grazie',
	'optin-deny-long' => 'Torna alla pagina precedente',
	'optin-submit-out' => 'Lascia Beta',
	'optin-submit-feedback' => 'Lascia un feedback',
	'optin-survey-yes' => 'Sì',
	'optin-survey-no' => 'No',
	'optin-survey-intro' => 'Ti ringraziamo per aver provato il Beta.
Vorremmo sapere cosa ne pensi della nostra nuova interfaccia, quindi apprezzeremmo se compilassi il sondaggio facoltativo qui sotto prima di fare clic su "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Cosa ti è piaciuto della Beta? Cosa non ti è piaciuto della Beta?',
	'optin-survey-question-whyoptout' => 'Perché stai lasciando la Beta? (Si prega di selezionare tutte le risposte pertinenti.)',
	'optin-survey-answer-whyoptout-hard' => 'Era troppo difficile da usare.',
	'optin-survey-answer-whyoptout-didntwork' => 'Non funzionava correttamente.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Non si comportava in modo coerente.',
	'optin-survey-answer-whyoptout-didntlike-look' => "Non mi piaceva l'aspetto.",
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Non mi piacevano le nuove schede e il layout.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Non mi piaceva la nuova barra degli strumenti.',
	'optin-survey-answer-whyoptout-other' => 'Altra motivazione:',
	'optin-survey-question-explain' => 'Spiega o approfondisci la tua risposta di cui sopra:',
	'optin-survey-question-techfail' => 'Si sono verificati problemi tecnici usando la beta?',
	'optin-survey-question-techfail-ifyes' => 'In caso affermativo, si prega di elaborare o spiegare:',
	'optin-survey-question-usedtoolbar' => 'Hai usato la nuova barra degli strumenti espandibile per modificare una pagina?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Se sì, cosa ti è piaciuto? Cosa non ti è piaciuto?',
	'optin-survey-question-usedtoolbar-ifno' => 'Se no, perché no? Potresti usarla in futuro? Si prega di spiegare.',
	'optin-survey-question-different' => 'Che cosa avremmo potuto fare diversamente per farti continuare a usare la Beta?',
	'optin-survey-question-feedback' => 'Facci conoscere altre osservazioni, preoccupazioni o pensieri che hai per noi - tutti i feedback sono utili:',
	'optin-survey-question-browser' => 'Quale browser usi?',
	'optin-survey-answer-browser-other' => 'Altri browser:',
	'optin-survey-question-os' => 'Quale sistema operativo usi?',
	'optin-survey-answer-os-other' => 'Altro sistema operativo:',
	'optin-survey-question-res' => 'Qual è la risoluzione dello schermo?',
	'optin-survey-question-changes' => 'Quali cambiamenti vorresti vedere in Beta in futuro (selezionare tutte le risposte pertinenti)?',
	'optin-survey-answer-changes-nav' => 'Rendere la navigazione del sito più semplice da usare.',
	'optin-survey-answer-changes-edittools' => 'Aumentare e migliorare gli strumenti di modifica.',
	'optin-survey-answer-changes-upload' => 'Migliorare il caricamento di contenuti e di multimedia.',
	'optin-survey-answer-changes-richtext' => 'Sviluppare strumenti di modifica simili a un editor rich-text.',
	'optin-survey-answer-changes-lookfeel' => "Aggiornare l'aspetto del sito.",
	'optin-survey-answer-changes-predictability' => 'Migliorare la prevedibilità delle prestazioni.',
	'optin-survey-answer-changes-custom' => "Consentire una maggiore personalizzazione all'utente.",
	'optin-survey-answer-changes-other' => 'Altro:',
	'optin-improvements2' => '== Cosa è stato migliorato? ==
[[File:UsabilityToolbar.png|left|link=|Screenshot della nuova e migliorata toolbar]]
[[File:UsabilityDialogs.png|left|link=|Screenshot delle nuove finestre di dialogo per la generazione del contenuto]]
;Barra degli strumenti migliorata
La nuova barra degli strumenti rende più semplice modificare le voci.
Le icone nuove e migliorate rendono più chiara quale azione è svolta da ogni strumento.
Le sezioni espandibili riducono il disordine, mentre mantengono strumenti poco usati a un solo click di distanza.
;Finestre di dialogo
Nella creazione di link o tabelle, cliccando l\'icona dello strumento adesso si apre una finestra di dialogo che ti aiuta a creare link e tabelle riempendo un semplice modulo.
;E altro ancora
Abbiamo introdotto una serie di altri miglioramenti come una navigazione migliorata, le schede a cascata, una ricerca migliorata, e altro ancora.
== Come partecipare ==
Per sapere altro sul progetto Usabilità, visita il nostro [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative sito]. Oppure, [http://it.wikipedia.org/wiki/Progetto:Coordinamento/Usabilit%C3%A0 qui] puoi fare segnalazioni o dare pareri di ogni genere in italiano. 
<div style="clear:both"></div>',
);

/** Japanese (日本語)
 * @author Aotake
 * @author Fryed-peach
 * @author Hosiryuhosi
 * @author Whym
 * @author 青子守歌
 */
$messages['ja'] = array(
	'optin' => '使用性改善への参加',
	'optin-desc' => '使用性改善プロジェクトによる最新の使用性改善案のベータ版の試用を可能にする',
	'optin-title-optedout' => 'ベータ版を試す',
	'optin-title-optedin' => 'ベータ版をやめる',
	'optin-title-justoptedin' => 'ベータ版にようこそ',
	'optin-title-justoptedout' => 'ベータ版をお試しいただきありがとうございます',
	'optin-title-feedback' => 'フィードバックしてください',
	'optin-needlogin' => 'ベータ版を試すには[$1 ログイン]する必要があります。',
	'optin-intro' => 'Wikipedia Usability Initiativeでは、ウィキペディアの使いやすさを改善するために活動しています。ベータ版を試してみませんか？',
	'optin-feedback-intro' => 'ベータ版をお試しいただきありがとうございます。よろしければこの新しいインタフェースについてご意見をお聞かせください。以下の任意調査にご協力いただければ幸いです。',
	'optin-feedback-back' => '調査表への記入を望まない場合は、$1 へお戻りください。',
	'optin-success-in' => 'ベータ版を試用しています。ページ上部の「{{int:optin-leave}}」のリンクをクリックすると、いつでも試用をやめることができます。',
	'optin-leave-cancel' => 'ベータ版の試用を続ける場合、$1に戻ることができます。',
	'optin-success-out' => 'ベータ版を停止しました。ページ上部の「{{int:optin-try}}」のリンクをクリックすると、いつでもまた試用することができます。',
	'optin-success-feedback' => 'フィードバックをご送信いただきありがとうございます！',
	'optin-accept-short' => 'はじめましょう！',
	'optin-accept-long' => 'ベータ版を試す',
	'optin-accept-long-anon' => 'ログインしてベータ版を試す',
	'optin-deny-short' => 'いりません',
	'optin-deny-long' => '前のページに戻る',
	'optin-submit-out' => 'ベータ版をやめる',
	'optin-submit-feedback' => 'フィードバックを送信',
	'optin-survey-yes' => 'はい',
	'optin-survey-no' => 'いいえ',
	'optin-survey-intro' => 'ベータ版をお試しいただきありがとうございます。よろしければこの新しいインタフェースについてご意見をお聞かせください。 [[#leave|{{int:optin-submit-out}}]]をクリックする前に以下の任意調査にご協力いただければ幸いです。',
	'optin-survey-question-likedislike' => 'ベータ版のどのような点が気に入りましたか。どのような点が気に入りませんでしたか。',
	'optin-survey-question-whyoptout' => 'なぜベータ版の試用をおやめになるのですか(あてはまるものをすべてお選びください)。',
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
	'optin-survey-question-changes' => '今後、ベータ版にどのような点の改善を望みますか？(当てはまるものをすべて選択してください)',
	'optin-survey-answer-changes-nav' => 'サイト全体の操作方法をより使いやすくしてほしい。',
	'optin-survey-answer-changes-edittools' => '編集ツールを増やし、機能強化してほしい。',
	'optin-survey-answer-changes-upload' => 'コンテンツやメディアアップロードを改善してほしい。',
	'optin-survey-answer-changes-richtext' => 'リッチテキストエディタのような編集ツールを開発してほしい。',
	'optin-survey-answer-changes-lookfeel' => 'サイトの「見た感じ」を更新してほしい。',
	'optin-survey-answer-changes-predictability' => '動作の安定性を改善してほしい。',
	'optin-survey-answer-changes-custom' => 'より強力なカスタマイズを可能にしてほしい。',
	'optin-survey-answer-changes-other' => 'その他:',
	'optin-improvements2' => '==改善点について ==
[[File:UsabilityToolbar.png|left|link=|拡張編集ツールバーのスクリーンショット]]
[[File:UsabilityDialogs.png|left|link=|新しいコンテンツ生成ダイアログのスクリーンショット]]
;拡張編集ツールバー
:拡張ツールバーは記事の編集をより簡単にします。
:新しく改善されたアイコンによって、各ツールの機能をよりわかりやすく表現しています。
:表示を開いたり折り畳んだりできるようにしたことで、画面を簡潔に保ちつつ、使用頻度の低い機能にもワンクリックで到達できます。
;ダイアログ
:リンクや表を作成するときには、ツールアイコンをクリックすればダイアログボックスが開き、簡単なフォームに記入することでリンクや表が作れます。
;それだけではありません
:他にも使いやすくなったナビゲーション、縦方向のタブ、検索機能の強化などなど、多くの改善を行ないました。
==改善にご協力ください==
使用性改善について、ぜひ[http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative ホームページ]をご覧ください。 
<div style="clear:both"></div>',
);

/** Georgian (ქართული)
 * @author Alsandro
 * @author გიორგიმელა
 */
$messages['ka'] = array(
	'optin' => 'გამოყენებადობის ინიციატივაში მონაწილეობა',
	'optin-desc' => 'მომხმარებლებისთვის ბეტას, გამოყენებადობის ინიციატივის ჯგუფის შემუშავებული ინტერფეისის, გამოცდის უფლება.',
	'optin-title-optedout' => 'გამოსცადეთ ბეტა',
	'optin-title-optedin' => 'ბეტადან გასვლა',
	'optin-title-justoptedin' => 'მოგესალმებით ბეტაში',
	'optin-title-justoptedout' => 'გმადლობთ ბეტას გამოცდისთვის',
	'optin-title-feedback' => 'გამოხმაურება',
	'optin-needlogin' => 'ბეტას მოსასინჯად უნდა [$1 დარეგისტრირდეთ].',
	'optin-intro' => 'ვიკიპედიის გამოყენებადობის საინიციატივო ჯგუფმა დიდი შრომა გასწია ვიკიპედიის გამოყენებადობის გასაუმჯობესებლად.
ხომ არ გსურთ მოსინჯოთ ჩვენი ბეტა?',
	'optin-feedback-intro' => 'გმადლობთ ბეტას მოსინჯვისთვის.
ჩვენ გვსურს გავიგოთ, თუ რას ფიქრობთ თქვენ ჩვენს ახალ ინტერფეისზე და მადლიერი დაგრჩებოდით, თუ შეავსებდით ქვემოთ მოყვანილ კითხვარს.',
	'optin-feedback-back' => 'თუ არ გსურთ კითხვარის შევსება, შეგიძლიათ დაბრუნდეთ $1-ზე.',
	'optin-success-in' => 'თქვენ ამჟამად ბეტას სცდით.
შეგიძიათ გახვიდეთ ნებისმიერ დროს ეკრანის მარჯვენა ზედა კუთხეში  "{{int:optin-leave}}" ბმულზე დაწკაპუნებით.',
	'optin-leave-cancel' => 'თუ გსურთ გააგრძელოთ ბეტას გამოყენება, შეგიძლიათ დაბრუნდეთ  $1-ზე.',
	'optin-success-out' => 'თქვენ წარმატებით გახვედით ბეტადან.
თქვენ კვლავ შეგიძლიათ მისი მოსინჯვა ნებისმიერ დროს ეკრანის ზედა მარჯვენა კუთხეში "{{int:optin-try}}" ბმულზე დაწკაპუნებით.',
	'optin-success-feedback' => 'გმადლობთ გამოხმაურებისთვის!',
	'optin-accept-short' => 'თანახმა ვარ!',
	'optin-accept-long' => 'მოსინჯეთ ბეტა',
	'optin-accept-long-anon' => 'დარეგისტრირდით და მოსინჯეთ ბეტა',
	'optin-deny-short' => 'არა, გმადლობთ',
	'optin-deny-long' => 'წინა გვერდზე დაბრუნება',
	'optin-submit-out' => 'ბეტადან გასვლა',
	'optin-submit-feedback' => 'გამოგვეხმაურეთ',
	'optin-survey-yes' => 'ჰო',
	'optin-survey-no' => 'არა',
	'optin-survey-intro' => 'გმადლობთ ბეტას გამოცდისთვის.
ჩვენ გვსურს გავიგოთ, რას ფიქრობთ ახალ ინტერფეისზე და მადლიერი ვიქნებით თუ ღილაკზე «[[#leave|{{int:optin-submit-out}}]]» დაწკაპუნებამდე შეავსებთ კითხვარს.',
	'optin-survey-question-likedislike' => 'რა მოგეწონათ ბეტაში? რა არ მოგეწონათ ბეტაში?',
	'optin-survey-question-whyoptout' => 'რატომ გადიხართ ბეტადან? (გთხოვთ მონიშნოთ ყველა შესაბამისი მიზიზი.)',
	'optin-survey-answer-whyoptout-hard' => 'ძალიან რთული იყო გამოსაყენებლად.',
	'optin-survey-answer-whyoptout-didntwork' => 'ის გაუმართავი იყო.',
	'optin-survey-answer-whyoptout-notpredictable' => 'იგი გაუთვალისწინებლად მოქმედებდა.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'არ მომწონდა მისი გარეგნობა.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'არ მომეწონა ახალი ყუები და განლაგება.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'მე არ მომეწონა რედაქტირების პანელი.',
	'optin-survey-answer-whyoptout-other' => 'სხვა მიზეზი:',
	'optin-survey-question-explain' => 'გთხოვთ დააკონკრეტოთ თქვენს მიერ ზემოთ გაცემული პასუხი:',
	'optin-survey-question-techfail' => 'ბეტას გამოყენებისას რაიმე ტექნიკური გაუმართაობა ხომ არ შეგიმჩნევიათ?',
	'optin-survey-question-techfail-ifyes' => 'თუ კი, გთხოვთ აღწეროთ ან დააკონკრეტოთ:',
	'optin-survey-question-usedtoolbar' => 'გამოიყენეთ ახალი გაშლადი ინსტრუმენტების პანელი გვერდის რედაქტირებისთვის?',
	'optin-survey-question-usedtoolbar-ifyes' => 'თუ კი, რა მოგეწონათ მასში? რა არ მოგეწონათ მასში?',
	'optin-survey-question-usedtoolbar-ifno' => 'თუ არა, მაშინ რატომ? მომავალში გამოიყენებთ? გთხოვთ განმარტოთ.',
	'optin-survey-question-different' => 'რისი გაკეთება შეგვეძლო ბეტას გამოყენება რომ გაგეგრძელებინათ?',
	'optin-survey-question-feedback' => 'გთხოვთ გაგვიზიაროთ თქვენი მოსაზრებები:',
	'optin-survey-question-browser' => 'რომელ ბრაუზერს იყენებთ?',
	'optin-survey-answer-browser-other' => 'სხვა ბრაუზერი:',
	'optin-survey-question-os' => 'რომელ ოპერაციულ სისტემას იყენებთ?',
	'optin-survey-answer-os-other' => 'სხვა ოპერაციული სისტემა:',
	'optin-survey-question-res' => 'თქვენი მონიტორის გაფართოება:',
	'optin-survey-question-changes' => 'რა ცვლილებების დანახვა გსურთ მომავალში ბეტაში (გთხოვთ მონიშნოთ ყველა შესაბამისი პასუხი)?',
	'optin-survey-answer-changes-nav' => 'საიტის ზოგადი ნავიგაციის გაადვილება.',
	'optin-survey-answer-changes-edittools' => 'გაზარდეთ და გააუმჯობესეთ რედაქტირების ინსტრუმენტები.',
	'optin-survey-answer-changes-upload' => 'გააუმჯობესეთ შიგთავსისა და მედიის ატვირთვა.',
	'optin-survey-answer-changes-richtext' => 'დაამუშავეთ რედაქტირების ხელსაწყოები რიჩ-ტექსტ რედაქტორების მსგავსად.',
	'optin-survey-answer-changes-lookfeel' => 'განაახლეთ საიტის შესახედაობა.',
	'optin-survey-answer-changes-predictability' => 'შესრულების წინასწარგანსაზღვრის გაუმჯობესება.',
	'optin-survey-answer-changes-custom' => 'კონფიგურაციის მეტი შესაძლებლობის დაშვება.',
	'optin-survey-answer-changes-other' => 'სხვა:',
);

/** Kalaallisut (Kalaallisut)
 * @author Qaqqalik
 */
$messages['kl'] = array(
	'optin-title-optedout' => 'Beta misiliguk',
);

/** Khmer (ភាសាខ្មែរ)
 * @author គីមស៊្រុន
 */
$messages['km'] = array(
	'optin-title-optedout' => 'សាកប្រើស៊េរីបេតា',
	'optin-title-optedin' => 'ចាកចេញពីស៊េរីបេតា',
	'optin-title-justoptedin' => 'បេតាសូមសូមស្វាគមន៍',
	'optin-title-justoptedout' => 'សូមថ្លែងអំណរគុណលោកអ្នកដែលបានសាកល្បងប្រើស៊េរីបេតារបស់យើងខ្ញុំ',
	'optin-title-feedback' => 'មតិយោបល់អំពីស៊េរីបេតា',
	'optin-needlogin' => 'អ្នកត្រូវតែ[$1 ឡុកអ៊ីន]មុននឹងសាកល្បងប្រើស៊េរីបេតា។',
	'optin-intro' => 'ក្រុមការងារវីគីភីឌាកំពុងតែខំប្រឹងប្រែងធ្វើអោយវិគីភីឌាមានភាពងាយស្រួលក្នុងការប្រើប្រាស់។ តើអ្នកចង់សាកល្បងប្រើស៊េរីបេតារបស់យើងខ្ញុំទេ?',
	'optin-feedback-intro' => 'សូមថ្លែងអំណរគុណលោកអ្នកដែលបានសាកល្បងប្រើស៊េរីបេតា។ យើងខ្ញុំចង់ដឹងអំពីយោបល់លោកអ្នកស្ដីអំពីអន្តរមុខថ្មីនេះ។ ហេតុនេះយើងខ្ញុំសង្ឃឹមថាលោកអ្នកនឹងជួយឆ្លើយសំនួរអង្កេតខាងក្រោមនេះ។',
	'optin-feedback-back' => 'ប្រសិនបើលោកអ្នកមិនចង់ឆ្លើយសំនួរអង្កេតនេះទេ អ្នកអាចត្រលប់ទៅកាន់$1វិញក៏បាន។',
	'optin-success-in' => 'អ្នកកំពុងសាកល្បងប្រើស៊េរីបេតា។ អ្នកអាចចាកចេញទៅវិញពេលណាក៏បាន ដោយគ្រាន់តែចុចលើពាក្យ "{{int:optin-leave}}" នៅផ្នែកខាងលើរបស់អេក្រង់។',
	'optin-leave-cancel' => 'ប្រសិនបើអ្នកនៅចង់បន្តប្រើស៊េរីបេតា អ្នកអាចត្រលប់ទៅកាន់$1វិញក៏បាន។',
	'optin-success-out' => 'អ្នកបានចាកចេញពីបេតាដោយជោគជ័យហើត។ អ្នកអាចសាកប្រើវាម្ដងទៀតនៅពេលណាក៏ដោយ ដោយចុចលើពាក្យ "{{int:optin-try}}" នៅផ្នែកខាងលើខាងស្ដាំនៃអេក្រង់របស់អ្នក។',
	'optin-success-feedback' => 'សូមថ្លែងអំណរគុណសំរាប់មតិយោបល់របស់លោកអ្នក!',
	'optin-accept-short' => 'សាកប្រើវា!',
	'optin-accept-long' => 'សាកប្រើស៊េរីបេតា',
	'optin-accept-long-anon' => 'ឡុកអ៊ីនរួចសាកប្រើស៊េរីបេតា',
	'optin-deny-short' => 'ទេ,អរគុណ',
	'optin-deny-long' => 'ត្រលប់ទៅកាន់ទំព័រមុន',
	'optin-submit-out' => 'ចាកចេញពីស៊េរីបេតា',
	'optin-submit-feedback' => 'ដាក់ស្នើមតិយោបល់',
	'optin-survey-yes' => 'បាទ/ចាស៎',
	'optin-survey-no' => 'ទេ',
	'optin-survey-intro' => 'សូមថ្លែងអំណរគុណលោកអ្នកដែលបានសាកល្បងប្រើស៊េរីបេតា។ យើងខ្ញុំចង់ដឹងអំពីយោបល់លោកអ្នកស្ដីអំពីអន្តរមុខថ្មីនេះ។ ហេតុនេះយើងខ្ញុំសង្ឃឹមថាលោកអ្នកនឹងជួយឆ្លើយសំនួរអង្កេតខាងក្រោមនេះ មុននឹងចាកចេញទៅដោយចុចលើ"[[#leave|{{int:optin-submit-out}}]]"។',
	'optin-survey-question-likedislike' => 'តើចំនុចណាខ្លះដែលលោកអ្នកពេញចិត្តនិងមិនពេញចិត្តអំពីស៊េរីបេតានេះ? (សូមឆ្លើយជាភាសាអង់គ្លេស)',
	'optin-survey-question-whyoptout' => 'ហេតុអ្វីក៏លោកអ្នកចង់ចាកចេញពីស៊េរីបេតានេះ? (សូមជ្រើសរើសពីខាងក្រោម)',
	'optin-survey-answer-whyoptout-hard' => 'វាពិបាកប្រើពេក។',
	'optin-survey-answer-whyoptout-didntwork' => 'វាដំនើរការមិនបានល្អ។',
	'optin-survey-answer-whyoptout-notpredictable' => 'វាដំនើរការតែពិះតែពាស។',
	'optin-survey-answer-whyoptout-didntlike-look' => 'ខ្ញុំមិនចូលចិត្តការរចនារបស់វាទេ។',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'ខ្ញុំមិនចូលចិត្តស្លាកនិងប្លង់ថ្មីទេ។',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'ខ្ញុំមិនចូលចិត្តរបារឧបករណ៍ថ្មីទេ។',
	'optin-survey-answer-whyoptout-other' => 'មូលហេតុផ្សេងទៀត៖',
	'optin-survey-question-explain' => 'សូមពន្យល់រៀបរាប់ក្បោះក្បាយថែមទៀតអំពីចំលើយខាងលើរបស់លោកអ្នក៖ (សូមសរសេរជាភាសាអង់គ្លេស)',
	'optin-survey-question-techfail' => 'តើលោកអ្នកមានជួបបញ្ហាបច្ចេកទេសពេលកំពុងប្រើស៊េរីបេតានេះទេ? (សូមឆ្លើយជាភាសាអង់គ្លេស)',
	'optin-survey-question-techfail-ifyes' => 'បើមាន​​​ សូមពន្យល់រៀបរាប់៖ (សូមសរសេរជាភាសាអង់គ្លេស)',
	'optin-survey-question-usedtoolbar' => 'តើអ្នកមានបានប្រើរបារឧបករណ៍កែប្រែថ្មីនៅពេលធ្វើការកែប្រែទំព័រទេ?',
	'optin-survey-question-usedtoolbar-ifyes' => 'បើមាន តើចំនុចណាខ្លះដែលអ្នកពេញចិត្តនិងមិនពេញចិត្តអំពីវា?',
	'optin-survey-question-usedtoolbar-ifno' => 'បើទេ ហេតុអ្វី? តើអ្នកនឹងប្រើវានាពេលអនាគតទេ? សូមពន្យល់។',
	'optin-survey-question-different' => 'តើយើងខ្ញុំអាចធ្វើអ្វីផ្សេងពីនេះទៀត ដើម្បីទាក់ទាញអ្នកអោយនៅប្រើស៊េរីបេតា? (សូមឆ្លើយជាភាសាអង់គ្លេស)',
	'optin-survey-question-feedback' => 'សូមប្រាប់យើងខ្ញុំអោយដឹងពីគំនិត យោបល់ឬកង្វល់របស់អ្នកអំពីយើងខ្ញុំ។ មតិយោបល់របស់លោកអ្នកទាំងអស់មានសារសំខាន់ណាស់!៖ (សូមឆ្លើយជាភាសាអង់គ្លេស)',
	'optin-survey-question-browser' => 'តើអ្នកប្រើកម្មវិធីរាវរកប្រភេទអ្វី?',
	'optin-survey-answer-browser-other' => 'កម្មវិធីរាវរកដទៃ៖',
	'optin-survey-question-os' => 'តើអ្នកកំពុងប្រើប្រព័ន្ធប្រតិបត្តិការប្រភេទអ្វី?',
	'optin-survey-answer-os-other' => 'ប្រព័ន្ធប្រតិបត្តិការដទៃ៖',
	'optin-survey-question-res' => 'តើអេក្រង់របស់អ្នកមានភាពម៉ត់កំរិតណា?',
	'optin-survey-question-changes' => 'តើបំលាស់ប្ដូរណាខ្លះដែលអ្នកចង់ឃើញនៅក្នុងស៊េរីបេតានាពេលអនាគត (សូមជ្រើសយកពីខាងក្រោម)?',
	'optin-survey-answer-changes-nav' => 'ធ្វើអោយការចុចរាវនៅក្នុងវិបសាយទាំងមូលកាន់តែងាយស្រួល។',
	'optin-survey-answer-changes-edittools' => 'បង្កើននិងជំរុញឧបករណ៍កែប្រែអោយកាន់តែប្រសើរ។',
	'optin-survey-answer-changes-upload' => 'បង្កើតចំនួនអត្ថបទនឹងការផ្ទុកឡើងមេឌា។',
	'optin-survey-answer-changes-richtext' => 'អភិវឌ្ឍឧបករណ៍កែប្រែអោយស្រដៀងទៅនឹងឧបករណ៍កែប្រែទំរង់ rich-text ដែរ។',
	'optin-survey-answer-changes-lookfeel' => 'បន្ទាន់សម័យ"ការរចនានិងភាពទាក់ទាញ"របស់វិបសាយ។',
	'optin-survey-answer-changes-predictability' => 'បង្កើនសុក្រិតភាពនៃដំនើរការ។',
	'optin-survey-answer-changes-custom' => 'អនុញ្ញាតអោយមានការលំអតាមចិត្តអោយទូលំទូលាយទៀត។',
	'optin-survey-answer-changes-other' => 'ផ្សេងទៀត៖',
);

/** Kannada (ಕನ್ನಡ)
 * @author Nayvik
 */
$messages['kn'] = array(
	'optin-survey-yes' => 'ಹೌದು',
	'optin-survey-no' => 'ಇಲ್ಲ',
	'optin-survey-answer-whyoptout-other' => 'ಇತರೆ ಕಾರಣ:',
	'optin-survey-answer-changes-other' => 'ಇತರ:',
);

/** Korean (한국어)
 * @author FriedC
 * @author Klutzy
 * @author Kwj2772
 */
$messages['ko'] = array(
	'optin' => 'Usability Initiative 참여',
	'optin-desc' => '사용자들에게 Usability Initiative에서 만든 최신 디자인을 사용할 방법을 제공합니다.',
	'optin-title-optedout' => '베타 버전 사용하기',
	'optin-title-optedin' => '베타 버전 사용 종료',
	'optin-title-justoptedin' => '베타 버전 체험을 시작합니다',
	'optin-title-justoptedout' => '베타 버전을 사용해 주셔서 감사합니다.',
	'optin-title-feedback' => '피드백 남기기',
	'optin-needlogin' => '베타 버전을 사용하려면 [$1 로그인]을 해 주세요.',
	'optin-intro' => '위키백과에서는 사용자들의 편의성을 높이기 위해 새로운 스킨을 만들고 있습니다. 직접 체험해 보세요!',
	'optin-feedback-intro' => '베타 버전을 사용해 주셔서 감사합니다.
아래 설문조사를 작성해 주시면 더 좋은 스킨을 만드는 데에 참고하겠습니다.',
	'optin-feedback-back' => '설문조사를 작성하지 않으려면 바로 $1 문서로 되돌아갈 수 있습니다.',
	'optin-success-in' => '베타 버전을 사용합니다. 사용을 끝내려면 오른쪽 위의 ‘{{int:optin-leave}}’를 눌러 원래 스킨으로 돌아갈 수 있습니다.',
	'optin-leave-cancel' => '베타 스킨을 계속 사용하려면 바로 $1 문서로 되돌아갈 수 있습니다.',
	'optin-success-out' => '베타 버전 사용을 종료합니다. 다시 사용하려면 오른쪽 위의 ‘{{int:optin-try}}’를 누르면 됩니다.',
	'optin-success-feedback' => '의견을 주셔서 감사합니다!',
	'optin-accept-short' => '사용하기',
	'optin-accept-long' => '베타 스킨을 사용합니다.',
	'optin-accept-long-anon' => '로그인하고 베타 스킨을 사용합니다.',
	'optin-deny-short' => '되돌아가기',
	'optin-deny-long' => '이전 문서로 되돌아갑니다.',
	'optin-submit-out' => '베타 버전 사용 종료',
	'optin-submit-feedback' => '피드백 남기기',
	'optin-survey-yes' => '예',
	'optin-survey-no' => '아니오',
	'optin-survey-intro' => '베타 버전을 사용해 주셔서 감사합니다.
아래의 ‘[[#leave|{{int:optin-submit-out}}]]를 누르기 전에, 아래에 이번 인터페이스에 대한 사용 후기를 적어주시면 스킨 개발에 더욱 도움이 됩니다.',
	'optin-survey-question-likedislike' => '어떤 부분이 마음에 들었나요? 그리고 어떤 부분이 마음에 들지 않았나요?',
	'optin-survey-question-whyoptout' => '베타 버전을 그만 사용하려는 이유는 무엇인가요? (해당하는 곳에 모두 선택해주세요)',
	'optin-survey-answer-whyoptout-hard' => '사용하기에 너무 어렵다',
	'optin-survey-answer-whyoptout-didntwork' => '기능이 제대로 동작하지 않는다',
	'optin-survey-answer-whyoptout-notpredictable' => '예상했던 동작과 다르게 돌아간다',
	'optin-survey-answer-whyoptout-didntlike-look' => '전체적으로 스킨이 적응되지 않는다',
	'optin-survey-answer-whyoptout-didntlike-layout' => '레이아웃이 마음에 들지 않는다',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => '툴바가 마음에 들지 않는다',
	'optin-survey-answer-whyoptout-other' => '다른 이유:',
	'optin-survey-question-explain' => '위에서 선택한 이유에 대해 좀 더 자세히 설명해주세요.',
	'optin-survey-question-techfail' => '베타 스킨을 사용하면서 기술적인 문제를 겪었나요?',
	'optin-survey-question-techfail-ifyes' => '해당 사항에 대해 자세하게 설명해주세요.',
	'optin-survey-question-usedtoolbar' => '새로운 툴바 기능을 써 보셨나요?',
	'optin-survey-question-usedtoolbar-ifyes' => '툴바에서 어떤 부분이 마음에 들었나요? 어떤 부분이 마음에 들지 않았나요?',
	'optin-survey-question-usedtoolbar-ifno' => '툴바를 쓰지 않은 이유는 무엇인가요?',
	'optin-survey-question-different' => '베타 스킨을 계속 쓰려면 무엇이 더 개선되어야 할까요?',
	'optin-survey-question-feedback' => '기타 제안할 것이 있다면 자유롭게 써 주세요.',
	'optin-survey-question-browser' => '어떤 웹 브라우저를 사용하고 있나요?',
	'optin-survey-answer-browser-other' => '다른 브라우저:',
	'optin-survey-question-os' => '어떤 운영 체제(OS)를 사용하고 있나요?',
	'optin-survey-answer-os-other' => '다른 운영 체제:',
	'optin-survey-question-res' => '어느 정도의 모니터 해상도를 사용하고 있나요?',
	'optin-survey-question-changes' => '베타 스킨이 앞으로 어떻게 바뀌었으면 좋겠나요? (해당 항목에 모두 선택해주세요)',
	'optin-survey-answer-changes-nav' => '사이트 내부를 이동하기 편하게 만들어주세요.',
	'optin-survey-answer-changes-edittools' => '편집 도구를 더 편리하게 만들어주세요.',
	'optin-survey-answer-changes-upload' => '글이나 파일 자료를 더 쉽게 올릴 수 있도록 만들어주세요.',
	'optin-survey-answer-changes-richtext' => '편집 도구를 워드 프로세서처럼 편리하게 만들어 주세요.',
	'optin-survey-answer-changes-lookfeel' => '스킨의 외관을 더 깔끔하게 고쳐 주세요.',
	'optin-survey-answer-changes-predictability' => '도구를 사용할 때 그 의미를 예상하기 쉽게 만들어주세요.',
	'optin-survey-answer-changes-custom' => '더 많은 부분을 사용자가 설정할 수 있도록 해 주세요.',
	'optin-survey-answer-changes-other' => '기타:',
	'optin-improvements2' => '== 어떤 부분이 향상되었나요? ==
[[File:UsabilityToolbar.png|left|link=|편집 툴바]]
[[File:UsabilityDialogs.png|left|link=|편집 대화상자]]
;향상된 편집 툴바
툴바 버튼이 더 직관적인 모습으로 바뀌었습니다.
툴바 아이콘을 개선하여, 각 아이콘이 어떤 기능을 하는지 더욱 알기 쉬워졌습니다.
자주 쓰지 않는 기능은 숨겨 두었고, 숨겨진 기능은 클릭 한 번으로 꺼낼 수 있습니다.
;편집 대화상자
문서에 링크를 걸거나 표를 만들고 싶을 때, 편집 툴바 아이콘을 누르면 관련 대화상자가 열립니다. 위키문법을 배우지 않아도 몇 가지 값을 입력하는 것만으로 문서를 만들 수 있습니다.
;그 밖의 개선점
이밖에도 둘러보기 메뉴와 검색 기능 등, 여러 부분이 향상되었습니다.

== 참여하기 ==
위키미디어 사용성 향상 프로젝트에 참여하고 싶은 분은 [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative 프로젝트 위키](영어) 문서를 참고해주세요.
<div style="clear:both"></div>',
);

/** Karachay-Balkar (Къарачай-Малкъар)
 * @author Iltever
 */
$messages['krc'] = array(
	'optin-survey-answer-changes-other' => 'Башха:',
);

/** Colognian (Ripoarisch)
 * @author Melancholie
 * @author Purodha
 */
$messages['ksh'] = array(
	'optin' => 'Op de <i lang="en">Usability Initiative</i> ier Verschöönerunge jon.',
	'optin-desc' => 'Määt et müjjelesch för de Metmaacher, de <i lang="en">Usability Initiative</i> ier neuste Verschöönerunge ußzeprobeere.',
	'optin-title-optedout' => 'Verschöönerunge ußprobeere',
	'optin-title-optedin' => 'De Verschöönerunge nimmieh ußprobeere',
	'optin-title-justoptedin' => 'Wellkumme beim Verschöönerunge Ußprobeere',
	'optin-title-justoptedout' => 'Merßi, datt De de Verschöönerunge ußprobeere deihß.',
	'optin-title-feedback' => 'Röckmäldunge',
	'optin-needlogin' => 'Do mööts alld [$1 enjelogg senn], öm de Verschöönerunge zom Ußprobeere för Desch enschallde ze künne.',
	'optin-intro' => 'De <i lang="en">Usability Initiative</i> vun de Wikipedia hät kräftesch jet draan jeärbeit, öm de Wikipedia besser zom Bedeene ze maache för de Metmaacher.
Wells De de Verschöönerunge ußprobeere?',
	'optin-feedback-intro' => 'Merci för et Ußprobeere.
Mer wolle jähn weße, wat De vun uns Verschöönerunge häls,
un mer sin Der dangkbaa, wann De op di Froore heh
e paa Antwoote jävve dääts. Et koß nix, un verflich zoh nix.',
	'optin-feedback-back' => 'Wann De nix ußfölle wells, kanns De och noh $1 zerök jonn.',
	'optin-success-in' => 'Do bes jäz de Verschöönerunge am ußprobeere.
Do kanns doh emmer wider eruß kumme, endämm dat De op „{{int:optin-leave}}“ klecks.',
	'optin-leave-cancel' => 'Wann De de Verschöönerunge wigger bruche wells, kanns De noh $1 retuur jonn.',
	'optin-success-out' => 'Do häß de Verschöönerunge verlohße.
Do kanns emmer wider zerök kumme, endämm dat De op „{{int:optin-try}}“ klecks.',
	'optin-success-feedback' => 'Häzlijje Dangk för Ding Rökmäldung.',
	'optin-accept-short' => 'Lom_mer dat donn!',
	'optin-accept-long' => 'Verschöönerunge ußprobeere',
	'optin-accept-long-anon' => 'Enlogge, un de Verschöönerunge ußprobeere',
	'optin-deny-short' => 'Nää, dat wulle mer nit',
	'optin-deny-long' => 'retuur op de vörijje Sigg',
	'optin-submit-out' => 'De Verschöönerunge zom Ußprobeere verlohße',
	'optin-submit-feedback' => 'Rökmäldunge jävve',
	'optin-survey-yes' => 'Joh',
	'optin-survey-no' => 'Nää',
	'optin-survey-intro' => 'Mer bedangke uns doför, dat De de Verschöönerunge ußprobeere deihß.

Mer dääte jähn weße wat De övver uns neu Ovverflääsch meins. Dröm dääte mer uns freue, wann De unge op e paa kleine Froore antwoote dääts, ih dat De op „[[#leave|{{int:optin-submit-out}}]]“ klecks.',
	'optin-survey-question-likedislike' => 'Wat hät Der aam dä Verschöönerunge zom Ußprobeere jefalle?
Wat hät Der aam dä Verschöönerunge zom Ußprobeere nit jefalle?',
	'optin-survey-question-whyoptout' => 'Woröm deihß De de Verschöönerunge zom Ußprobeere nit (mieh) bruche? (Beß esu joot, un jif alles aan, wat för Desch shtemme deiht)',
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
	'optin-survey-question-different' => 'Wat künnte mer anders maache, domet De uns Verschöönerunge wigger bruche wells?',
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
	'optin-survey-question-changes' => 'Wat för Änderunge dääts De en De Zohkunnf jähn en de Bata hann? Nemm alle, di paße.',
	'optin-survey-answer-changes-nav' => 'Maat et eröm Navvijeere en heh dä Web_ßait eijfaacher.',
	'optin-survey-answer-changes-edittools' => 'Maat mieh Wärkzüsch för et Beärbeide, un maat et beßer.',
	'optin-survey-answer-changes-upload' => 'Maat et Huhlaade, för Meedije un Enhallde, beßer.',
	'optin-survey-answer-changes-richtext' => 'Doht Wärkzüsh en dä Aat vun <i lang="en">„rich-text“ editors</i> för et Täxte-Ändere entweckele.',
	'optin-survey-answer-changes-lookfeel' => 'Doht Ußsinn un de Jeschtalldung vun dä Web-ßait neuer maache.',
	'optin-survey-answer-changes-predictability' => 'Maat de Leistung mieh afseebaar.',
	'optin-survey-answer-changes-custom' => 'Jrüßer Aanpassonge aan der einzelne Metmaacher müjjelesch maache.',
	'optin-survey-answer-changes-other' => 'Söns jet:',
);

/** Cornish (Kernewek)
 * @author Kernoweger
 * @author Kw-Moon
 */
$messages['kw'] = array(
	'optin-title-optedout' => 'Prevy Beta',
	'optin-deny-short' => 'Na, meur ras',
	'optin-survey-yes' => 'Ea',
	'optin-survey-no' => 'Na',
);

/** Latin (Latina)
 * @author Omnipaedista
 * @author UV
 */
$messages['la'] = array(
	'optin-survey-yes' => 'Ita',
	'optin-survey-no' => 'Minime',
	'optin-survey-answer-changes-other' => 'Aliud:',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Les Meloures
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
	'optin-intro' => "D'Benotzerfrëndlechkeets-Initiative vu Wikipedia huet vill geschafft fir d'Benotzerfrëndlechkeet vu Wikipedia ze verbesseren.
Wëllt Dir Beta ausprobéieren?",
	'optin-feedback-intro' => 'Merci datt Dir Beta ausprobéiert hutt.
Mir wëlle wësse wat Dir vun dem neien Interface halt, dofir wiere mir frou wann Dir bei der fräiwëlleger Ëmfro hei ënnedrënner matmaache géift.',
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
	'optin-deny-short' => 'Nee Merci',
	'optin-deny-long' => 'Zréck op déi Säit virdrun',
	'optin-submit-out' => 'Beta verloossen',
	'optin-submit-feedback' => 'Gitt eis Är Meenung',
	'optin-survey-yes' => 'Jo',
	'optin-survey-no' => 'Neen',
	'optin-survey-intro' => 'Merci datt Dir Beta ausprobéiert.
Mir wiere frou fir ze wëssen wat Dir vun deem neien Interface denkt, dofir wiere mir frou wann Dir déi fräiwëlleg Ëmfro hei ënnendrënner géift ausfëllen ier Dir op "[[#leave|{{int:optin-submit-out}}]]" klickt.',
	'optin-survey-question-likedislike' => 'Wat hutt dir bäi Beta gutt finnt? Wat huet Iech u Beta net gefall?',
	'optin-survey-question-whyoptout' => 'Firwat verloost Dir dës Beta? (wielt w.e.g. alles aus wat zoutrëfft)',
	'optin-survey-answer-whyoptout-hard' => 'Et war ze komplizéiert fir ze benotzen.',
	'optin-survey-answer-whyoptout-didntwork' => 'Et huet net richteg fonctionnéiert.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Et huet net esou fonctionnéiert wéi virgesinn.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Et huet mir net gefall wéi et ausgesäit.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Ech hat déi nei Ongleten an den neie Layout net gär.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Ech hunn déi nei Toolbar net gär.',
	'optin-survey-answer-whyoptout-other' => 'Anere Grond:',
	'optin-survey-question-explain' => 'Erkläert oder preziséiert Är Äntwert w.e.g.:',
	'optin-survey-question-techfail' => 'Hat Dir technesch Schwieregkeete beim Benotze vu Beta?',
	'optin-survey-question-techfail-ifyes' => 'Wa jo, da begrënnt oder erkläert et:',
	'optin-survey-question-usedtoolbar' => "Hutt Dir déi nei erweiderbar Toolbar benotzt fir eng Säit z'änneren?",
	'optin-survey-question-usedtoolbar-ifyes' => 'Wa jo, wat hutt Dir doru gutt fonnt? Wat huet Iech net esougutt gefall?',
	'optin-survey-question-usedtoolbar-ifno' => 'Wann neen, firwat? Wäert Dir se an der Zukunft benotzen? Gitt w.e.g. en Erklärung.',
	'optin-survey-question-different' => 'Wat kënne mir anescht maache fir datt Dir eis Beta-Versioun weider benotzt?',
	'optin-survey-question-feedback' => 'Sot eis w.e.g. Är Meenung:',
	'optin-survey-question-browser' => 'Watfir e Browser benotzt Dir?',
	'optin-survey-answer-browser-other' => 'Anere Browser:',
	'optin-survey-question-os' => 'Wafir e Betriibssystem benotzt Dir?',
	'optin-survey-answer-os-other' => 'Anere Betriibssystem:',
	'optin-survey-question-res' => "Wéi ass d'Opléisung vun ärem Ecran?",
	'optin-survey-question-changes' => 'Watfir Ännerunge géif Dir am léifsten an der Beta an Zukunft gesinn (sicht w.e.g. all déi eraus déi Iech gefalen)?',
	'optin-survey-answer-changes-nav' => 'Déi allgemeng Navigatioun vum Site méi einfach maachen.',
	'optin-survey-answer-changes-edittools' => "D'Méiglechkeete fir z'ännere vergréisseren a verbesseren.",
	'optin-survey-answer-changes-upload' => "D'Eropluede vun Inhalt a Medie verbesseren.",
	'optin-survey-answer-changes-richtext' => "Ännerungsméiglechkeeten entwéckelen ähnlech wéi 'rich-text'-Editeuren.",
	'optin-survey-answer-changes-lookfeel' => 'Ausgesi vum Site aktualiséieren.',
	'optin-survey-answer-changes-predictability' => 'Zouverléissegkeet vun der Previsioun vun der Performance verbesseren.',
	'optin-survey-answer-changes-custom' => 'Méi en grouss Benotzer-Upassung erlaben.',
	'optin-survey-answer-changes-other' => 'Anerer:',
	'optin-improvements2' => "== Wat gouf verbessert? ==
[[File:UsabilityToolbar.png|left|link=|Screenshot vun der verbesserter Ännerungs-Toolbar]][[File:UsabilityDialogs.png|left|link=|Screenshot vun den  neien Dialogen fir Inhalt ze generéieren]]
;verbessert Toolbar
Déi verbessert Toolbar mécht et méi einfach fir Artikelen z'änneren.
Nei a verbessert Icone maachen et méi kloer watfir eng Aktioun all Tool duerchféiert.
Erweidert Sektioune reduzéieren d'Plaz an halen net dacks benotzt Toolen just e Klick ewech.
;Dialog
Wann Dir Linken oder Tabellen uleet, a wann Dir op d'icone vun deem Tool klickt geet elo eng Dialogfënster op déi Iech hëlleft Linken an Tabellen unzeleeën andeem dir just en einfache Formulaire ausfëllt.
;And More
Mir hunn eng Rei vu Verbesserungen agefouert esou wéi verbessert Navigatioun, Ongleten a Kaskaden, verbessert Sich a méi.
== Wéi Dir matmaache kënnt ==
Fir méi iwwert d'Benotzerfrëndlechkeetsinitiative erauszefannen gitt w.e.g. op d'[http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative Homepage].<div style=\"clear:both\"></div>",
);

/** Limburgish (Limburgs)
 * @author Pahles
 */
$messages['li'] = array(
	'optin-title-optedout' => 'Perbeer Beta',
	'optin-title-optedin' => 'Verlaot Beta',
	'optin-title-justoptedin' => 'Welkom bij Beta',
	'optin-title-justoptedout' => "Danke veur 't perbere van Beta",
	'optin-title-feedback' => 'Trökkoppeling gaeve',
	'optin-success-feedback' => 'Danke veur de trökkoppeling!',
	'optin-accept-short' => 'Insjakele!',
	'optin-accept-long' => 'Perbeer Beta',
	'optin-accept-long-anon' => 'Aanmelje en Beta perbere',
	'optin-deny-short' => 'Nei, danke',
	'optin-deny-long' => 'Trök nao de veurige pagina',
	'optin-submit-out' => 'Verlaot Beta',
	'optin-submit-feedback' => 'Trökkoppeling gaeve',
	'optin-survey-yes' => 'Jao',
	'optin-survey-no' => 'Nei',
);

/** Lumbaart (Lumbaart)
 * @author Dakrismeno
 */
$messages['lmo'] = array(
	'optin' => "Adesion a l'Iniziativa per l'Usabilità",
	'optin-desc' => "'L dà la pussibilità ai druvadur de pruvà la beta, i ültem mejurament del pruget.",
	'optin-title-optedout' => 'Pröva la Beta',
	'optin-title-optedin' => 'Va fö da Beta',
	'optin-title-justoptedin' => 'Benvegnüü denter a Beta',
	'optin-title-justoptedout' => 'Mersì per avè pruaa la Beta',
	'optin-intro' => "'L pruget d'üsabilità de Wikipedia l'è adree a laurà fort per mejurà la Wikipedia.
A vöret pruvà la nostra beta?",
	'optin-success-in' => 'Te see adree a pruvà la Beta.
Te pö piantà lì quand te vör fasend \'click\' sura al ligamm "{{int:optin-leave}}" in coo a la pagina.',
	'optin-accept-short' => 'Pröva!',
	'optin-accept-long' => 'Pröva la Beta',
	'optin-accept-long-anon' => 'Vegn denter e pröva la Beta',
	'optin-deny-short' => 'No, mersì',
	'optin-deny-long' => 'Va indree a la pagina de prima',
	'optin-submit-out' => 'Va föra da Beta',
	'optin-survey-yes' => 'Sì',
	'optin-survey-no' => 'Nò',
);

/** Lithuanian (Lietuvių)
 * @author Homo
 * @author Matasg
 */
$messages['lt'] = array(
	'optin' => 'Įjungti naudojimo iniciatyvą',
	'optin-desc' => 'Leidžia naudotojams išbandyti Beta, patobulintą naudotojo sąsają.',
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
	'optin-success-out' => 'Jūs sėkmingai palikote Beta.
Galite vėl išmėginti Beta, bet kuriuo metu paspaudę "{{int:optin-try}}" viršutiniame dešiniajame ekrano kampe.',
	'optin-accept-short' => 'Padarykime tai!',
	'optin-accept-long' => 'Išbandykite Beta',
	'optin-deny-short' => 'Ne, ačiū',
	'optin-deny-long' => 'Grįžti į ankstesnį puslapį',
	'optin-submit-out' => 'Palikti Beta',
	'optin-survey-yes' => 'Taip',
	'optin-survey-no' => 'Ne',
	'optin-survey-intro' => 'Dėkojame, kad išbandėte Beta.
Norėtume sužinoti, ką manote apie mūsų naująją vartotojo sąsają, todėl būtume labai dėkingi, jei užpildytumėte žemiau pateiktą neprivalomą tyrimo apklausą, prieš paspausdami "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Kas jums patiko Betoje? Kas jums nepatiko Betoje?',
	'optin-survey-question-whyoptout' => 'Kodėl paliekate Beta? (Pasirinkite visus, kurie tinka).',
	'optin-survey-answer-whyoptout-hard' => 'Buvo pernelyg sunku redaguoti puslapį.',
	'optin-survey-answer-whyoptout-didntwork' => 'Ji nefunkcionavo tinkamai.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Neatitiko to, ko tikėjausi.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Man nepatiko išvaizda.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Man nepatiko nauji skirtukai ir išdėstymas.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Man nepatiko naujoji įrankių juosta.',
	'optin-survey-answer-whyoptout-other' => 'Kita priežastis:',
	'optin-survey-question-explain' => 'Prašome paaiškinti arba detalizuoti savo atsakymą:',
	'optin-survey-question-techfail' => 'Ar naudodamiesi Beta patyrėte techninių nesklandumų?',
	'optin-survey-question-techfail-ifyes' => 'Jei taip, prašome paaiškinti:',
	'optin-survey-question-usedtoolbar' => 'Ar puslapiams redaguoti naudojote naująją įrankių juostą?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Jei taip, kas jums patiko Betoje? Kas nepatiko Betoje?',
	'optin-survey-question-different' => 'Ką galėtume daryti kitaip, kad jūs naudotumėte Beta?',
	'optin-survey-question-feedback' => 'Leiskite mums sužinoti jūsų nuomonę:',
	'optin-survey-question-browser' => 'Kurią naršyklę naudojate?',
	'optin-survey-answer-browser-other' => 'Kita naršyklė:',
	'optin-survey-question-os' => 'Kurią operacinę sistemą naudojate?',
	'optin-survey-answer-os-other' => 'Kita operacinė sistema:',
	'optin-survey-question-res' => 'Kokia Jūsų ekrano rezoliucija?',
);

/** Latvian (Latviešu)
 * @author Marozols
 * @author Papuass
 */
$messages['lv'] = array(
	'optin-title-optedout' => 'Izmēģināt Beta',
	'optin-title-optedin' => 'Pamest Beta',
	'optin-title-feedback' => 'Atsauksmes par Beta',
	'optin-accept-long' => 'Izmēģināt Beta',
	'optin-submit-out' => 'Pamest Beta',
	'optin-survey-answer-whyoptout-other' => 'Cits iemesls:',
	'optin-survey-answer-browser-other' => 'Cits pārlūks:',
	'optin-survey-answer-os-other' => 'Cita operētājsistēma:',
);

/** Lazuri (Lazuri)
 * @author Bombola
 */
$messages['lzz'] = array(
	'optin-deny-short' => 'Var, didi mardi',
	'optin-survey-yes' => 'Ho',
	'optin-survey-no' => 'Var',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'optin' => 'Пристапување кон Иницијативата за употребливост',
	'optin-desc' => 'Дозволи на корисници да ја пробаат Бета, најновите подобрувања на Иницијативата за употребливост.',
	'optin-title-optedout' => 'Пробај ја Бета',
	'optin-title-optedin' => 'Напуштете ја Бета',
	'optin-title-justoptedin' => 'Добредојдовте на Бета',
	'optin-title-justoptedout' => 'Ви благодариме што ја пробавте Бета',
	'optin-title-feedback' => 'Мислења за Бета',
	'optin-needlogin' => 'Треба да [$1 се најавите] за да ја пробате Бета.',
	'optin-intro' => 'Иницијативата за употребливост на Википедија работеше напорно за да ја подобри употребливоста на Википедија.
Дали би сакале да ја пробате нашата Бета?',
	'optin-feedback-intro' => 'Ви благодариме што ја пробавте Бета.
Би сакале да знаеме што мислите за нашиот нов посредник, па затоа би ценеле доколку го пополните прашалникот подолу (незадолжително).',
	'optin-feedback-back' => 'Доколку не сакате да го пополните прашалникот, можете да се вратите на $1.',
	'optin-success-in' => 'Моментално ја испробувате Бета.
Можете да ја напуштите во било кога со кликнување на врската „{{int:optin-leave}}“ на врвот од страницата.',
	'optin-leave-cancel' => 'Доколку сакате да продолжите да ја користите Бета, можете да се вратите на $1.',
	'optin-success-out' => 'Успешно ја напуштивте Бета.
Можете повторно да ја пробате било кога со тоа што ќе кликнете на врската „{{int:optin-try}}“ на врвот од страницата.',
	'optin-success-feedback' => 'Ви благодариме што го искажавте вашето мислење!',
	'optin-accept-short' => 'Ајде!',
	'optin-accept-long' => 'Испробајте ја Бета',
	'optin-accept-long-anon' => 'Најавете се и пробајте ја Бета',
	'optin-deny-short' => 'Не, благодарам',
	'optin-deny-long' => 'Врати ме на претходната страница',
	'optin-submit-out' => 'Напушти ја Бета',
	'optin-submit-feedback' => 'Искажи мислење',
	'optin-survey-yes' => 'Да',
	'optin-survey-no' => 'Не',
	'optin-survey-intro' => 'Ви благодариме што ја пробавте Бета.
Би сакале да знаеме што мислите за нашиот нов посредник, и затоа би ценеле доколку го пополните прашалникот подолу (незадолжително) пред да кликнете на „[[#leave|{{int:optin-submit-out}}]]“.',
	'optin-survey-question-likedislike' => 'Што ви се допаѓаше кај Бета? А што не ви се допадна?',
	'optin-survey-question-whyoptout' => 'Зошто ја напуштате Бета? (Изберете колку што сакате.)',
	'optin-survey-answer-whyoptout-hard' => 'Беше премногу тешка за употреба.',
	'optin-survey-answer-whyoptout-didntwork' => 'Не функционираше како што треба.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Работеше непредвидливо.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Не ми се допадна како изгледа.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Не ми се допаднаа новите менија и распоредот.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Не ми се допадна новиот алатник.',
	'optin-survey-answer-whyoptout-other' => 'Друга причина:',
	'optin-survey-question-explain' => 'Објаснете или елаборирајте го одговорот даден погоре:',
	'optin-survey-question-techfail' => 'Дали ви се случија некакви технички проблеми додека ја користевте Бета?',
	'optin-survey-question-techfail-ifyes' => 'Ако ви се случија, тогаш елаборирајте или објаснете ги:',
	'optin-survey-question-usedtoolbar' => 'Дали го користевте новото мени на отворање за да уредите страница?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Ако да, што ви се допадна таму? А што не ви се допадна?',
	'optin-survey-question-usedtoolbar-ifno' => 'Ако не, тогаш зошто не? Дали би го правеле тоа во иднина? Објаснете.',
	'optin-survey-question-different' => 'Што треба да биде поинаку направено за да сакате да ја користите Бета?',
	'optin-survey-question-feedback' => 'Ве молиме пишете ни ако имате други коментари, проблеми или идеи - сите ваши мислења се полезни!',
	'optin-survey-question-browser' => 'Кој прелистувач го користевте?',
	'optin-survey-answer-browser-other' => 'Друг прелистувач:',
	'optin-survey-question-os' => 'Кој оперативен систем го користевте?',
	'optin-survey-answer-os-other' => 'Друг оперативен систем:',
	'optin-survey-question-res' => 'Која ви е резолуцијата на екранот?',
	'optin-survey-question-changes' => 'Какви промени треба да се направат во Бета? (изберете колку што сакате)',
	'optin-survey-answer-changes-nav' => 'Општата навигација на викито треба да се направи полесна за користење.',
	'optin-survey-answer-changes-edittools' => 'Треба да има повеќе и подобри алатки за уредување.',
	'optin-survey-answer-changes-upload' => 'Подобрете ги подигнувањата на содржини и податотеки.',
	'optin-survey-answer-changes-richtext' => 'Направете алатки за уредување слични на оние за уредување со „богат текст“.',
	'optin-survey-answer-changes-lookfeel' => 'Обновете го „изгледот и чувството“ на викито.',
	'optin-survey-answer-changes-predictability' => 'Подобрете ја предвидливоста на работењето.',
	'optin-survey-answer-changes-custom' => 'Овозможете повеќе кориснички прилагодувања.',
	'optin-survey-answer-changes-other' => 'Друго:',
	'optin-improvements2' => '== Што е подобрено? ==
[[File:UsabilityToolbar.png|left|link=|Screenshot of the enhanced edit toolbar]]
[[File:UsabilityDialogs.png|left|link=|Screenshot of the new content generation dialogs]]
;Подобрен алатник
Подобрениот алатник го олеснува уредувањето. Новите и подобрени икони даваат појасна претстава за намената на секоја алатка. Проширените делови го средуваат неуредниот изглед, но сепак овозможуваат моментен пристап дури и до алатките што ретко се употребуваат.
;Дијалог
Иконите за врски и табели сега отвораат прозорче со дијалог што дава едноставен образец за нивно лесно создавање.
;И повеќе
Воведовме ред други усовршувања како подобрена навигација, каскадни јазичиња, подобрено пребарување и други можности.
== Како да се вклучите ==
За да дознаете повеќе за Иницијативата за употребливост, посетете ја нашата [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative страница].
<div style="clear:both"></div>',
);

/** Malayalam (മലയാളം)
 * @author Junaidpv
 * @author Praveenp
 * @author Sadik Khalid
 */
$messages['ml'] = array(
	'optin' => 'യൂസബിലിറ്റി ഇനിഷ്യേറ്റീവ് ഓപ്റ്റിൻ',
	'optin-desc' => 'ഉപയോക്താക്കളെ യൂസബിലിന്റി ഇനിഷ്യേറ്റീവിന്റെ ഏറ്റവും പുതിയ മെച്ചപ്പെടുത്തലുകൾ അഥവാ ബീറ്റ പരീക്ഷിക്കാനനുവദിക്കുന്നു.',
	'optin-title-optedout' => 'ബീറ്റ പരീക്ഷിക്കൂ',
	'optin-title-optedin' => 'ബീറ്റ ഉപേക്ഷിക്കൂ',
	'optin-title-justoptedin' => 'ബീറ്റയിലേയ്ക്ക് സ്വാഗതം',
	'optin-title-justoptedout' => 'ബീറ്റ പരീക്ഷിച്ചു നോക്കുന്നതിനു നന്ദി',
	'optin-title-feedback' => 'ബീറ്റയെ കുറിച്ചുള്ള അഭിപ്രായം',
	'optin-needlogin' => 'ബീറ്റ പരീക്ഷിക്കാൻ താങ്കൾ [$1 ലോഗിൻ] ചെയ്യേണ്ടതാണ്.',
	'optin-intro' => 'വിക്കിപീഡിയയുടെ ഉപയോഗക്ഷമത വർദ്ധിപ്പിക്കാൻ വിക്കിപീഡിയ യൂസബിലിറ്റി ഇനിഷ്യേറ്റീവ് കഠിനാധ്വാനം ചെയ്യുന്നുണ്ട്.
താങ്കൾ ബീറ്റ പരീക്ഷിച്ചു നോക്കാൻ ആഗ്രഹിക്കുന്നുവോ?',
	'optin-feedback-intro' => 'ബീറ്റ പരീക്ഷിക്കുന്നതിനു നന്ദി.
പുതിയ സമ്പർക്കമുഖത്തെക്കുറിച്ച് താങ്കളെന്താണ് കരുതുന്നതെന്നറിയാൻ ഞങ്ങളാഗ്രഹിക്കുന്നു, അതുകൊണ്ട് ദയവായി താഴെ കൊടുത്തിരിക്കുന്ന സർവേയിൽ പങ്കെടുക്കാൻ താത്പര്യപ്പെടുന്നു.',
	'optin-feedback-back' => 'താങ്കൾക്ക് സർവേയിൽ പങ്കെടുക്കേണ്ടയെങ്കിൽ, $1 താളിലേയ്ക്ക് തിരിച്ചു പോകാവുന്നതാണ്.',
	'optin-success-in' => 'താങ്കൾ ഇപ്പോൾ ബീറ്റ പരീക്ഷിക്കുന്നു.
താളിന്റെ മുകളിലായി കാണുന്ന "{{int:optin-leave}}" എന്ന കണ്ണി ഞെക്കി താങ്കൾക്ക് ഏതൊരു സമയത്തും ഇതൊഴിവാക്കാവുന്നതാണ്.',
	'optin-leave-cancel' => 'താങ്കൾ ബീറ്റ തുടർന്നും ഉപയോഗിക്കാൻ ആഗ്രഹിക്കുന്നുവെങ്കിൽ, $1 എന്ന താളിലേയ്ക്ക് തിരിച്ചു പോകാവുന്നതാണ്.',
	'optin-success-out' => 'താങ്കൾ വിജയകരമായി ബീറ്റയിൽ നിന്ന് പുറത്തുകടന്നിരിക്കുന്നു.
താളിന്റെ മുകളിലായി കൊടുത്തിരിക്കുന്ന "{{int:optin-try}}" എന്ന കണ്ണി ഞെക്കി താങ്കൾക്ക് ഏതൊരു സമയത്തും ബീറ്റ പരീക്ഷിക്കാവുന്നതാണ്.',
	'optin-success-feedback' => 'താങ്കളുടെ അഭിപ്രായം അറിയിച്ചതിനു നന്ദി!',
	'optin-accept-short' => 'അങ്ങനെയാട്ടെ!!',
	'optin-accept-long' => 'ബീറ്റ പരീക്ഷിക്കുക',
	'optin-accept-long-anon' => 'ലോഗിൻ ചെയ്തശേഷം ബീറ്റ പരീക്ഷിക്കുക',
	'optin-deny-short' => 'വേണ്ട, നന്ദി',
	'optin-deny-long' => 'പഴയ താളിലേയ്ക്ക് തിരിച്ചു പോവുക',
	'optin-submit-out' => 'ബീറ്റ ഉപേക്ഷിക്കുക',
	'optin-submit-feedback' => 'അഭിപ്രായം അറിയിക്കുക',
	'optin-survey-yes' => 'ഉണ്ട്',
	'optin-survey-no' => 'ഇല്ല',
	'optin-survey-intro' => 'ബീറ്റ പരീക്ഷിക്കുന്നതിനു നന്ദി.
പുതിയ സമ്പർക്കമുഖത്തെ കുറിച്ച് താങ്കൾ എന്താണു വിചാരിക്കുന്നതെന്നറിയാൻ ഞങ്ങൾക്കാഗ്രഹമുണ്ട്, "[[#leave|{{int:optin-submit-out}}]]" എന്നതു ഞെക്കുന്നതിനു മുമ്പ് താങ്കൾ താഴെ കൊടുത്തിരിക്കുന്ന സർവേയിൽ പങ്കെടുക്കാൻ താത്പര്യപ്പെടുന്നു.',
	'optin-survey-question-likedislike' => 'ബീറ്റയിൽ താങ്കൾക്ക് എന്തൊക്കെ ഇഷ്ടപ്പെട്ടു? എന്തൊക്കെ ഇഷ്ടപ്പെട്ടില്ല?',
	'optin-survey-question-whyoptout' => 'താങ്കൾ എന്തുകൊണ്ട് ബീറ്റ ഉപേക്ഷിക്കുന്നു? (കാരണമാകുന്ന എല്ലാം ദയവായി തിരഞ്ഞെടുക്കുക.)',
	'optin-survey-answer-whyoptout-hard' => 'ഇതുപയോഗിക്കാൻ ഏറെ ബുദ്ധിമുട്ടാണ്.',
	'optin-survey-answer-whyoptout-didntwork' => 'ഇത് ശരിയായി പ്രവർത്തിക്കുന്നില്ല.',
	'optin-survey-answer-whyoptout-notpredictable' => 'ഇതെങ്ങിനെ പ്രവർത്തിക്കുമെന്നു പറയാൻ പറ്റുന്നില്ല.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'ഇത് കാണപ്പെടുന്ന രൂപം എനിക്കിഷ്ടപ്പെടുന്നില്ല.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'പുതിയ റ്റാബുകളും ദൃശ്യവിന്യാസവും എനിക്കിഷ്ടപ്പെട്ടില്ല.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'പുതിയ റ്റൂൾബാർ എനിക്കിഷ്ടപ്പെട്ടില്ല.',
	'optin-survey-answer-whyoptout-other' => 'മറ്റു കാരണം:',
	'optin-survey-question-explain' => 'താങ്കൾ മുകളിൽ നൽകിയ ഉത്തരം ദയവായി വിശദീകരിക്കുകയോ വികസിപ്പിക്കുകയോ ചെയ്യുക:',
	'optin-survey-question-techfail' => 'ബീറ്റ ഉപയോഗിക്കുമ്പോൾ ഏന്തെങ്കിലും സാങ്കേതിക തകരാറുകൾ താങ്കൾക്ക് അനുഭവപ്പെട്ടിട്ടുണ്ടോ?',
	'optin-survey-question-techfail-ifyes' => 'ഉണ്ട് എങ്കിൽ, ദയവായി വികസിപ്പിക്കുകയോ വിശദീകരിക്കുകയോ ചെയ്യുക:',
	'optin-survey-question-usedtoolbar' => 'ഒരു താൾ തിരുത്തുവാൻ വേണ്ടി വിപുലീകരിക്കാവുന്ന ടൂൾബാർ താങ്കൾ ഉപയോഗിച്ചിട്ടുണ്ടോ?',
	'optin-survey-question-usedtoolbar-ifyes' => 'ഉണ്ട് എന്നാണെങ്കിൽ, താങ്കൾക്ക് ഇതിൽ എന്താണ് ഇഷ്ടപ്പെട്ടത്? ഇതിൽ എന്താണ് ഇഷ്ടപ്പെടാതെ പോയത്?',
	'optin-survey-question-usedtoolbar-ifno' => 'ഇല്ല എങ്കിൽ, എന്തുകൊണ്ട് ഇല്ല? ഭാവിയിലോ? ദയവായി വിശദീകരിക്കുക.',
	'optin-survey-question-different' => 'താങ്കളെ ബീറ്റയിൽ തന്നെ പിടിച്ചു നിർത്താൻ എന്തെന്തു മാറ്റങ്ങളായിരുന്നു ഞങ്ങൾ വരുത്തേണ്ടിയിരുന്നത്?',
	'optin-survey-question-feedback' => 'ദയവായി താങ്കളുടെ കുറിപ്പുകളും, ഉത്കണ്ഠകളും, ചിന്തകളും ഞങ്ങളെ അറിയിക്കുക - എല്ലാ അഭിപ്രായങ്ങളും ഉപകാരപ്രദങ്ങളാണ്:',
	'optin-survey-question-browser' => 'ഏതു ബ്രൌസറാണ് താങ്കൾ ഉപയോഗിക്കുന്നത്?',
	'optin-survey-answer-browser-ie5' => 'ഇന്റർനെറ്റ് എക്സ്‌‌പ്ലോറർ 5',
	'optin-survey-answer-browser-ie6' => 'ഇന്റർനെറ്റ് എക്സ്‌‌പ്ലോറർ 6',
	'optin-survey-answer-browser-ie7' => 'ഇന്റർനെറ്റ് എക്സ്‌‌പ്ലോറർ 7',
	'optin-survey-answer-browser-ie8' => 'ഇന്റർനെറ്റ് എക്സ്‌‌പ്ലോറർ 8',
	'optin-survey-answer-browser-ff1' => 'ഫയർഫോക്സ് 1',
	'optin-survey-answer-browser-ff2' => 'ഫയർഫോക്സ് 2',
	'optin-survey-answer-browser-ff3' => 'ഫയർഫോക്സ് 3',
	'optin-survey-answer-browser-cb' => 'ഗൂഗിൾ ക്രോം ബീറ്റ',
	'optin-survey-answer-browser-c1' => 'ഗൂഗിൾ ക്രോം 1',
	'optin-survey-answer-browser-c2' => 'ഗൂഗിൾ ക്രോം 2',
	'optin-survey-answer-browser-c3' => 'ഗൂഗിൾ ക്രോം 3',
	'optin-survey-answer-browser-c4' => 'ഗൂഗിൾ ക്രോം 4',
	'optin-survey-answer-browser-s3' => 'സഫാരി 3',
	'optin-survey-answer-browser-s4' => 'സഫാരി 4',
	'optin-survey-answer-browser-o9' => 'ഓപ്പറ 9',
	'optin-survey-answer-browser-o9.5' => 'ഓപ്പറ 9.5',
	'optin-survey-answer-browser-o10' => 'ഓപ്പറ 10',
	'optin-survey-answer-browser-other' => 'മറ്റ് ബ്രൗസർ:',
	'optin-survey-question-os' => 'ഏത് ഓപറേറ്റിങ്ങ് സിസ്റ്റമാണ് താങ്കൾ ഉപയോഗിക്കുന്നത്?',
	'optin-survey-answer-os-windows' => 'വിൻഡോസ്',
	'optin-survey-answer-os-windowsmobile' => 'വിൻഡോസ് മൊബൈൽ',
	'optin-survey-answer-os-macos' => 'മാക് ഓ.എസ്.',
	'optin-survey-answer-os-linux' => 'ലിനക്സ്',
	'optin-survey-answer-os-other' => 'മറ്റ് ഓപറേറ്റിങ് സിസ്റ്റം:',
	'optin-survey-question-res' => 'താങ്കളുടെ സ്ക്രീൻ റെസ‌ല്യൂഷൻ എന്താണ്?',
	'optin-survey-question-changes' => 'ഭാവിയിൽ ബീറ്റയിൽ എന്തു മാറ്റങ്ങൾ ഉണ്ടായിരിക്കാൻ താങ്കൾ ആഗ്രഹിക്കുന്നു (ബാധകമാകുന്ന എല്ലാം തിരഞ്ഞെടുക്കുക)?',
	'optin-survey-answer-changes-nav' => 'സൈറ്റിലെ ആകെ നാവിഗേഷൻ കൂടുതൽ ലളിതമാക്കുക.',
	'optin-survey-answer-changes-edittools' => 'തിരുത്താനുള്ള ഉപകരണങ്ങൾ വർദ്ധിപ്പിക്കുക, മെച്ചപ്പെടുത്തുക.',
	'optin-survey-answer-changes-upload' => 'ഉള്ളടക്കവും മീഡിയ അപ്‌‌ലോഡുകളും മെച്ചപ്പെടുത്തുക.',
	'optin-survey-answer-changes-richtext' => 'റിച്ച് ടെക്സ്റ്റ് എഡിറ്ററുകളിൽ ലഭ്യമായ വിധത്തിലുള്ള തിരുത്തൽ ഉപകരണങ്ങൾ വികസിപ്പിക്കുക.',
	'optin-survey-answer-changes-lookfeel' => 'സൈറ്റിന്റെ "ദൃശ്യാനുഭവം" കാലാനുസൃതമാക്കുക.',
	'optin-survey-answer-changes-predictability' => "'പ്രതീക്ഷയ്ക്കനുസരിച്ചുള്ള പ്രവർത്തനരീതി പുലർത്തൽ' മെച്ചപ്പെടുത്തുക",
	'optin-survey-answer-changes-custom' => 'ഉപയോക്താവിനു ഏറ്റവും കൂടുതൽ സജ്ജീകരിച്ചെടുക്കാവുന്ന വിധത്തിലാക്കുക.',
	'optin-survey-answer-changes-other' => 'കൂടുതൽ:',
	'optin-improvements2' => '==എന്താണ് മെച്ചപ്പെടുത്തിയത്?==
[[File:UsabilityToolbar.png|left|link=|മെച്ചപ്പെടുത്തിയ തിരുത്തൽ ടൂൾബാറിന്റെ സ്ക്രീൻഷോട്ട്]]
[[File:UsabilityDialogs.png|left|link=|പുതിയ ഉള്ളടക്ക സൃഷ്ടി സൗകര്യത്തിന്റെ സ്ക്രീൻഷോട്ട്]]
;വിപുലമായ ടൂൾബാർ
വിപുലമായ ടൂൾബർ ലേഖനങ്ങൾ തിരുത്തുന്നത് ലളിതമാക്കുന്നു.
പുതിയ മെച്ചപ്പെടുത്തിയ ഐകോണുകൾ ഓരോ ഉപകരണവും എന്തെന്ത് പ്രവൃത്തിയാണ് ചെയ്യുന്നതെന്ന് വിളിച്ചോതുന്നു.
വികസിപ്പിക്കാവുന്ന ഭാഗങ്ങൾ തുടർച്ചയായി ഉപയോഗിക്കാത്ത ഉപകരണങ്ങൾ ഒരു ക്ലിക്ക് അകലത്തിൽ മാറ്റി വെച്ച് അസൗകര്യം ഒഴിവാക്കുന്നു.
;എഴുതാനുള്ള സൗകര്യം
കണ്ണികളും പട്ടികകളും നിർമ്മിക്കാനുള്ള ഉപകരണത്തിന്റെ ഐകോണിൽ ഞെക്കുമ്പോൾ ഒരു ഡയലോഗ് ബോക്സ് തുറക്കുകയും, ഒരു ലളിതമായ ഫോം നിറയ്ക്കുന്നതിലൂടെ അവ സൃഷ്ടിക്കാൻ സഹായിക്കുകയും ചെയ്യുന്നു.
;കൂടുതൽ
ഞങ്ങൾ മെച്ചപ്പെട്ട നാവിഗേഷൻ, നിർഝരിത റ്റാബുകൾ, മെച്ചപ്പെടുത്തിയ തിരച്ചിൽ തുടങ്ങി ഒരുപിടി മറ്റ് നന്നാക്കലുകളും അവതരിപ്പിക്കുന്നു.
==എങ്ങനെ ഭാഗഭാക്കാവാം==
യൂസബിലിറ്റി ഇനിഷ്യേറ്റീവിനെ കുറിച്ച് കൂടുതലറിയാൻ, ദയവായി ഞങ്ങളുടെ [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative പ്രധാനതാൾ] സന്ദർശിക്കുക.
<div style="clear:both"></div>',
);

/** Mongolian (Монгол)
 * @author Chinneeb
 */
$messages['mn'] = array(
	'optin-survey-yes' => 'Тийм',
	'optin-survey-answer-changes-other' => 'Бусад:',
);

/** Malay (Bahasa Melayu)
 * @author Aurora
 * @author Diagramma Della Verita
 * @author Kurniasan
 */
$messages['ms'] = array(
	'optin' => 'Opt-in Inisiatif Kebolehgunaan',
	'optin-desc' => 'Membenarkan para pengguna mencuba Beta, peningkatan kebolehgunaan Inisiatif Kebolehgunaan terbaru.',
	'optin-title-optedout' => 'Cuba Beta',
	'optin-title-optedin' => 'Tinggalkan Beta',
	'optin-title-justoptedin' => 'Selamat datang ke Beta',
	'optin-title-justoptedout' => 'Terima kasih kerana menggunakan versi Beta kami',
	'optin-title-feedback' => 'Maklum balas Beta',
	'optin-needlogin' => 'Anda perlu [$1 log masuk] untuk mencuba versi Beta.',
	'optin-intro' => 'Program Inisiatif Kebolehgunaan Wikipedia telah berusaha keras untuk memperbaiki kebolehgunaan Wikipedia. Sudikah anda mencuba versi Beta kami?',
	'optin-feedback-intro' => 'Terima kasih kerana sudi menggunakan Beta.
Kami ingin mengetahui pendapat anda tentang versi ini. Oleh itu, amat kami hargai sekiranya anda sudi mengisi soal selidik di bawah.',
	'optin-feedback-back' => 'Sekiranya anda tidak ingin mengisi soal selidik di bawah, kembali ke $1.',
	'optin-success-in' => 'Anda sekarang sedang menggunakan versi Beta. 
Sekiranya anda tidak ingin menggunakan versi ini, sila tekan "{{int:optin-leave}}" di atas skrin anda.',
	'optin-leave-cancel' => 'Sekiranya anda ingin terus menggunakan Beta, sila kembali ke $1.',
	'optin-success-out' => 'Anda tidak lagi menggunakan Beta.
Sekiranya anda ingin mencuba versi Beta, sila tekan "{{int:optin-try}}" pada bahagian kiri atas skrin anda.',
	'optin-success-feedback' => 'Terima kasih atas pendapat anda!',
	'optin-accept-short' => 'Lakukan sekarang!',
	'optin-accept-long' => 'Cuba Beta',
	'optin-accept-long-anon' => 'Daftar masuk dan cuba Beta',
	'optin-deny-short' => 'Tidak, terima kasih',
	'optin-deny-long' => 'Kembali ke laman sebelumnya',
	'optin-submit-out' => 'Tinggalkan Beta',
	'optin-submit-feedback' => 'Berikan maklum balas',
	'optin-survey-yes' => 'Ya',
	'optin-survey-no' => 'Tidak',
	'optin-survey-intro' => 'Terima kasih kerana mencuba Beta.
Kami ingin ketahui pandangan anda tentang antara muka baru kami, jadi kami menghargai jika anda mengisi tinjauan pilihan di bawah sebelum mengklik "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Apakah yang anda suka tentang Beta? Apakah yang anda tidak suka tentang Beta?',
	'optin-survey-question-whyoptout' => 'Mengapakah anda meninggalkan Beta? (Sila pilih semua yang sesuai.)',
	'optin-survey-answer-whyoptout-hard' => 'Sangat susah untuk digunakan.',
	'optin-survey-answer-whyoptout-didntwork' => 'Ia tidak berfungsi dengan betul.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Ia tidak terlaksana seperti sepatutnya.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Saya tidak suka dengan penampilannya.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Saya tidak suka dengan tab-tab dan susun atur yang baru.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Saya tidak suka dengan bar peralatan yang baru.',
	'optin-survey-answer-whyoptout-other' => 'Sebab lain:',
	'optin-survey-question-explain' => 'Sila terangkan atau perincikan jawapan anda di atas:',
	'optin-survey-question-techfail' => 'Pernahkah anda mengalami sebarang kegagalan teknikal semasa menggunakan Beta?',
	'optin-survey-question-techfail-ifyes' => 'Jika ya, sila perincikan atau terangkan:',
	'optin-survey-question-usedtoolbar' => 'Pernahkah anda menggunakan bar peralatan boleh kembang yang baru untuk menyunting sesebuah laman?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Jika ya, apa yang anda suka tentangnya? Apa yang anda tidak suka tentangnya?',
	'optin-survey-question-usedtoolbar-ifno' => 'Jika tidak, kenapa tidak? Bagaimana pula di masa akan datang? Sila jelaskan.',
	'optin-survey-question-different' => 'Apakah yang patut kami ubah suai supaya anda kekal menggunakan Beta?',
	'optin-survey-question-feedback' => 'Sila maklumkan kami sebarang komen, kerisauan ataupun pandangan yang anda ada untuk kami - semua maklumbalas adalah berguna:',
	'optin-survey-question-browser' => 'Apakah pelayar web yang anda gunakan?',
	'optin-survey-answer-browser-other' => 'Lain-lain:',
	'optin-survey-question-os' => 'Apakah sistem pengendalian yang anda gunakan?',
	'optin-survey-answer-os-other' => 'Lain-lain:',
	'optin-survey-question-res' => 'Apakah resolusi skrin anda?',
	'optin-survey-question-changes' => 'Perubahan apakah yang anda ingin lihat pada Beta di masa akan datang (sila pilih semua yang sesuai)?',
	'optin-survey-answer-changes-nav' => 'Menjadikan pandu arah keseluruhan tapak lebih mudah digunakan.',
	'optin-survey-answer-changes-edittools' => 'Menambah dan meningkatkan alatan penyuntingan.',
	'optin-survey-answer-changes-upload' => 'Memperbaiki muat naik kandungan dan media.',
	'optin-survey-answer-changes-richtext' => 'Membangun peralatan suntingan serupa dengan penyunting teks kaya.',
	'optin-survey-answer-changes-lookfeel' => 'Menjadikan "rupa dan suasana" tapak ini lebih terkini.',
	'optin-survey-answer-changes-predictability' => 'Memperbaiki kebolehjankaan prestasi.',
	'optin-survey-answer-changes-custom' => 'Benarkan pengubahsuaian penguna yang lebih besar.',
	'optin-survey-answer-changes-other' => 'Lain-lain:',
	'optin-improvements2' => '== Apa yang telah diperbaik? ==
[[File:UsabilityToolbar.png|left|link=|Tampilan skrin palang menyunting tertingkat]]
[[File:UsabilityDialogs.png|left|link=|Tampilan skrin dialog penjanaan kandungan baru]]
;Palang Menyunting Tertingkat
Palang menyunting tertingkat memudahkan lagi menyunting rencana.
Ikon baru dan dipertingkat menjelaskan lagi apa tindakan yang dilakukan setiap alatan.
Bahagian boleh kembang mengurangkan semak sambil meletakkan alat yang jarang dipakai cuma satu klik jauh.
;Dialog
Apabila membuat pautan atau jadual, mengklik ikon alatan kini membuka kotak dialog yang membantu mencipta pautan dan jadual dengan mengisi borang mudah.
;Dan Lagi
Sejumlah perbaikan lain telah diperkenalkan seperti pandu arah tertingkat, tab melata, carian ditingkatkan, dan banyak lagi.
== Bagaimana boleh terlibat ==
Untuk mengetahui lebih lanjut tentang Usability Initiative, sila kunjungi [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative laman] kami.
<div style="clear:both"></div>',
);

/** Maltese (Malti)
 * @author Chrisportelli
 */
$messages['mt'] = array(
	'optin' => "Adeżjoni għal Proġett ta' Użabilità",
	'optin-desc' => "Ħalli 'l-utenti jippruvaw l-Beta, l-aħħar titjib tal-Inizjattiva ta' Użabilità.",
	'optin-title-optedout' => 'Ipprova l-Beta',
	'optin-title-optedin' => 'Ħalli l-Beta',
	'optin-title-justoptedin' => 'Merħba fil-Beta',
	'optin-title-justoptedout' => 'Grazzi talli ppruvajt l-verżjoni Beta',
	'optin-title-feedback' => 'Rispons fuq il-Beta',
	'optin-needlogin' => 'Huwa neċessarju li l-ewwel [$1 tidħol fil-kont tiegħek] qabel ma tipprova l-Beta.',
	'optin-intro' => "L-Inizzjattiva ta' Użabilità tal-Wikipedija qed taħdem biex ittejjeb l-użabilità tal-Wikipedija. Tixtieq tipprova l-Beta?",
	'optin-feedback-intro' => "Grazzi talli għamilt użu mill-Beta.
Nixtiequ nkunu nafu x'taħseb fuq din l-interfaċċa l-ġdida, għalhekk napprezzaw jekk issib ħin biex tikkompila dan is-sondaġġ t'hawn taħt.",
	'optin-feedback-back' => "Jekk ma tridx timla dan is-sondaġġ, tista' tirritorna lura lejn $1.",
	'optin-success-in' => 'Bħalissa qiegħed tipprova l-Beta.
Tista\' toħroġ barra minn din il-verżjoni billi tagħfas il-ħolqa "{{int:optin-leave}}" fil-quċċata tal-iskrin.',
	'optin-leave-cancel' => "Jekk tixtieq tkompli tuża' l-Beta, tista' tirritorna lejn $1.",
	'optin-success-out' => "Ħallejt il-verżjoni Beta b'suċċess.
Huwa possibbli li terġa' tipprova mill-ġdid f'kwalunkwe mument li tixtieq billi tagħfas il-ħolqa \"{{int:optin-try}}\" fin-naħa leminija tal-quċċata tal-iskrin.",
	'optin-success-feedback' => 'Grazzi għall-fehmiet tiegħek!',
	'optin-accept-short' => 'Ippruvaha!',
	'optin-accept-long' => 'Ipprova l-Beta',
	'optin-accept-long-anon' => 'Illogja u ipprova l-Beta',
	'optin-deny-short' => 'Le grazzi',
	'optin-deny-long' => 'Irritorna lejn il-paġna preċedenti',
	'optin-submit-out' => 'Ħalli l-Beta',
	'optin-submit-feedback' => 'Ħalli l-fehmiet tiegħek',
	'optin-survey-yes' => 'Iva',
	'optin-survey-no' => 'Le',
	'optin-survey-intro' => "Nirringrazzjawk talli għamilt użu mill-Beta.
Nixtiequ nkunu nafu x'taħseb fuq din l-interfaċċa l-ġdida, fejn napprezzaw jekk issib ħin biex tikkompila dan is-sondaġġ t'hawn taħt qabel ma' tagħfas fuq \"[[#leave|{{int:optin-submit-out}}]]\".",
	'optin-survey-question-likedislike' => "X'għoġbok mill-Beta? X'ma għoġobkx mill-Beta?",
	'optin-survey-question-whyoptout' => 'Għaliex se tħalli l-Beta? (Agħżel skont dak li jaqbel.)',
	'optin-survey-answer-whyoptout-hard' => 'Kien wisq diffiċli biex tużah.',
	'optin-survey-answer-whyoptout-didntwork' => 'Ma kienx qed jaħdem tajjeb.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Ma kienx qed jaħdem kif mixtieq.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Ma kienx jogħġobni d-dehra tiegħu.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Ma kinux jogħġbuni l-buttuni l-ġodda u t-tqassim tal-paġni.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Ma kinitx togħġobni l-kaxxa tal-għodda l-ġdida.',
	'optin-survey-answer-whyoptout-other' => 'Raġuni oħra:',
	'optin-survey-question-explain' => 'Jekk jogħġbok spjega jew elabora iktar fuq it-tweġiba preċedenti li għamilt:',
	'optin-survey-question-techfail' => "Esperjenzajt ruħek ma' xi problemi tekniċi waqt l-użu tal-Beta?",
	'optin-survey-question-techfail-ifyes' => 'Jekk iva, jekk jogħġbok elabora jew spjega:',
	'optin-survey-question-usedtoolbar' => 'Għamilt użu mill-kaxxa l-ġdida tal-għodda espansiva biex timmodifika paġna?',
	'optin-survey-question-usedtoolbar-ifyes' => "Jekk iva, x'għoġbok minnha? X'ma għoġbokx minnha?",
	'optin-survey-question-usedtoolbar-ifno' => "Jekk le, għala el? Tista' tużaha fil-futur? Jekk jogħġbok spjega.",
	'optin-survey-question-different' => "Xi stajna għamilna differenti biex kieku bqajt tuża' l-Beta?",
	'optin-survey-question-feedback' => "Ħallina nkunu nafu fuq kwalunkwe kumment jew ħsibijiet oħra li għadek għalina - kull tip ta' rispons huwa bżonnjuż!:",
	'optin-survey-question-browser' => "Xi browżer tuża'?",
	'optin-survey-answer-browser-other' => 'Browżer ieħor:',
	'optin-survey-question-os' => "X'sistema operattiva tuża'?",
	'optin-survey-answer-os-other' => 'Sistema operattiva oħra:',
	'optin-survey-question-res' => "X'inhi r-riżoluzzjoni tal-iskrin tiegħek?",
	'optin-survey-question-changes' => "X'tibdil tixtieq tara fil-futur fil-Beta (agħżel skont dak li jaqbel)?",
	'optin-survey-answer-changes-nav' => 'In-navigazzjoni tas-sit isir aktar sempliċi għall-użu.',
	'optin-survey-answer-changes-edittools' => 'Tkabbir u titjib tal-għodda tal-immodifikar.',
	'optin-survey-answer-changes-upload' => "Titjib fit-tlugħ ta' medja u fit-tpoġġija ta' kontenut.",
	'optin-survey-answer-changes-richtext' => "Żvilupp fl-għodda tal-immodifikar simili għal ta' editur ''rich-text''.",
	'optin-survey-answer-changes-lookfeel' => 'Aġġornament tal-aspett tas-sit.',
	'optin-survey-answer-changes-predictability' => 'Titjib fil-prevedibilità tal-prestazzjonijiet.',
	'optin-survey-answer-changes-custom' => "Tingħata ċans ikbar ta' personalizzazzjoni min-naħa tal-utent.",
	'optin-survey-answer-changes-other' => 'Oħrajn:',
	'optin-improvements2' => "== X'ġie mtejjeb? ==
[[File:UsabilityToolbar.png|left|link=|Screenshot tal-kaxxa tal-għodda mtejba]]
[[File:UsabilityDialogs.png|left|link=|Screenshot tat-twieqi l-ġodda ta' djalogu għall-ġenerazzjoni tal-kontenut]]
;Kaxxa tal-għodda tal-immodifikar imtejba
Il-kaxxa tal-għodda mtejba tgħin aktar fl-immodifikar tal-artikli.
Ikoni ġodda u mtejba jagħmlu aktar ċar liema azzjoni kull għodda twettaq.
Sezzjonijiet estiżi jnaqqsu d-diżordni, waqt li jżommu għodod li mhumiex użati b'mod frekwenti klikk 'il bogħod.
;Twieqi ta' djalogu
Meta toħloq ħoloq jew tabelli, meta tagħfas fuq l-ikona tal-għodda rispettiva tiftaħ tieqa ta' djalogu li tgħinek sabiex toħloq ħolqa u tabella billi timla formola sempliċi.
;U iktar
Introduċejna numru ta' aġġornamenti oħra fosthom navigazzjoni mtejba, buttuni li jinżlu, tiftix aħjar, u aktar.
== Kif tiġi involut ==
Biex tkun taf aktar dwar l-Inizjattiva ta' Użabilità, żur is-[http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative sit tagħna].
<div style=\"clear:both\"></div>",
);

/** Erzya (Эрзянь)
 * @author Botuzhaleny-sodamo
 */
$messages['myv'] = array(
	'optin-title-optedout' => 'Варчамс Бета верзиянть',
	'optin-survey-answer-whyoptout-other' => 'Лия тувтал:',
	'optin-survey-answer-browser-other' => 'Лия браузер',
	'optin-survey-answer-changes-other' => 'Лия:',
);

/** Mazanderani (مازِرونی)
 * @author محک
 */
$messages['mzn'] = array(
	'optin-title-justoptedin' => 'بتا دله خِش بمویی',
	'optin-survey-yes' => 'اره',
	'optin-survey-no' => 'نا',
	'optin-survey-intro' => 'بخاطر ایستیفاده از بتا شمجه ممنونمی
اگه بتوندی اینجه شه نظرات ره بنویس تا وه ره ململته هکنیم "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'بتای چچی خار بی‌یه؟ چچی ون دله نخار بی‌یه؟',
	'optin-survey-question-whyoptout' => 'چچی وسّه بتا ره دوستی؟',
	'optin-survey-answer-whyoptout-hard' => 'وه ره ایستیفاده هکردن خله سخته',
	'optin-survey-answer-whyoptout-didntwork' => 'خله خار نی‌یه',
	'optin-survey-answer-whyoptout-notpredictable' => 'اونتی که خاستمه نی‌یه',
	'optin-survey-answer-whyoptout-didntlike-look' => 'مه ره خِش نمو',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'ونه طرح دوستن و زوونه‌ئون جه خِشم نیموئه.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'جدید نوار ابزار جه خِشم نیموئه',
	'optin-survey-answer-whyoptout-other' => 'دیگه چیزا:',
	'optin-survey-question-explain' => 'خواهشا شه دلایل ره بنویسین:',
	'optin-survey-answer-changes-other' => 'دیگه:',
	'optin-improvements2' => '==چچی بهته بیّه==
[[File:UsabilityToolbar.png|left|link=|Screenshot of the enhanced edit toolbar]]
[[File:UsabilityDialogs.png|left|link=|Screenshot of the new content generation dialogs]]
;نوار ابزار پیشرفته
جدید ِنوار دچی‌ین ره آسون هکرده.
عکسا اینجه ره قشنگته هکرده.
دیگه درهم برهم نی‌یه خله راه دکته.
;گپ
بساتن لینک و جدول آسونه
;و بیشته
اما خله چیزا مثل ناوبری پیشرفته، ئوشاری زوونه‌ئون و دگردستن ره بهته هکردمی.

== خانی بدونی چتی توندی کومک هکنی؟ ==
برای اطلاع بیشته درباره رابطه با توانایی ایستفاده ابتکاری [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative اینجه] ره سر بزنین.
<div style="clear:both"></div>',
);

/** Nedersaksisch (Nedersaksisch)
 * @author Servien
 */
$messages['nds-nl'] = array(
	'optin' => "Anmelding bie 't Bruukbaorheidsinitiatief",
	'optin-desc' => "Hierdeur kunnen gebrukers Beta, en de leste verbeteringen van 't Bruukbaorheidsinitiatief uutpreberen.",
	'optin-title-optedout' => 'Beta uutpreberen',
	'optin-title-optedin' => 'Beta uutzetten',
	'optin-title-justoptedin' => 'Welkom bie Beta',
	'optin-title-justoptedout' => "Bedank veur 't uutpreberen van Beta",
	'optin-title-feedback' => "Is 't wat?",
	'optin-needlogin' => 'U mutten [$1 an-emeld ween] veurda-j Beta anzetten kunnen.',
	'optin-intro' => "'t Bruukbaorheidsinitiatief van Wikipedie hef een nieje vormgeving en nieje bewarkingshulpmiddels ontwikkeld um de bruukbaorheid van Wikipedie te verbeteren.
Wi-j Beta uutpreberen?",
	'optin-feedback-intro' => "Bedank veur 't uutpreberen van Beta.
Wie willen grege weten wa-jie van de nieje vormgeving vienen, 't zol dus mooi ween a-j disse vragen effen in willen vullen.",
	'optin-feedback-back' => 'A-j de vragen neet in willen vullen, ku-j weerummegaon naor $1',
	'optin-success-in' => 'Je hemmen Beta noen anstaon.
Je kunnen disse instellingen altied weer uutzetten deur op de knoppe "{{int:optin-submit-out}}" te klikken.',
	'optin-leave-cancel' => 'A-j Beta an willen laoten staon, ku-j weerummegaon naor $1',
	'optin-success-out' => 'Je hemmen Beta uut-ezet.
Je kunnen disse functionaliteit altied weer anzetten deur op de knoppe {{int:optin-submit-in}} te klikken.',
	'optin-success-feedback' => 'Bedank veur joew commetaar',
	'optin-accept-short' => "Lao-w 't mar doon",
	'optin-accept-long' => 'Beta uutpreberen',
	'optin-accept-long-anon' => 'Anmelden en Beta uutpreberen',
	'optin-deny-short' => 'Nee, do mar neet',
	'optin-deny-long' => 'Weerumme naor de veurige pagina',
	'optin-submit-out' => 'Beta uutzetten',
	'optin-submit-feedback' => 'Versturen',
	'optin-survey-yes' => 'Ja',
	'optin-survey-no' => 'Nee',
	'optin-survey-intro' => 'Bedank veur \'t uutpreberen van Beta.
Wie willen grege weten wa-jie van de nieje vormgeving vienen, \'t zol dus mooi ween a-j disse vragen effen in willen vullen veurda-j op "[[#leave|{{int:optin-submit-out}}]]" klikken.',
	'optin-survey-question-likedislike' => 'Wat vu-j goed an Beta en wat neet?',
	'optin-survey-question-whyoptout' => 'Waorumme gao-j Beta uutzetten? (Kies wat van toepassing is.)',
	'optin-survey-answer-whyoptout-hard' => "'t Was te meuilijk um te gebruken.",
	'optin-survey-answer-whyoptout-didntwork' => "'t Warken neet goed.",
	'optin-survey-answer-whyoptout-notpredictable' => "'t Reageren neet veurspelbaor.",
	'optin-survey-answer-whyoptout-didntlike-look' => "'t Zag der neet uut.",
	'optin-survey-answer-whyoptout-didntlike-layout' => "Ik vunne 't mar niks dee nieje tabblaojen en vormgeving.",
	'optin-survey-answer-whyoptout-didntlike-toolbar' => "Ik vunne 't mar niks dee warkbalke.",
	'optin-survey-answer-whyoptout-other' => 'Aandere reden:',
	'optin-survey-question-explain' => 'Wat is der gebeurd dan?',
	'optin-survey-question-techfail' => 'Bi-j fouten tegen-ekeumen mit Beta?',
	'optin-survey-question-techfail-ifyes' => 'Zo ja, wat dan?',
	'optin-survey-question-usedtoolbar' => "He-j de nieje uutklapbaore warkbalke uut-eprebeerd bie 't bewarken van een pagina?",
	'optin-survey-question-usedtoolbar-ifyes' => 'Zo ja, wat vu-j der goed al, en wat neet?',
	'optin-survey-question-usedtoolbar-ifno' => "Zo nee, waorum neet dà? Meschien laoter? Wie heuren grege waorumme of dat 't is.",
	'optin-survey-question-different' => 'Wat konnen we aanders edaon hemmen um derveur te zörgen da-j Beta an-ehuilen hannen?',
	'optin-survey-question-feedback' => 'Laot effen weten wa-j dervan vienen:',
	'optin-survey-question-browser' => 'Welke webkieker he-j?',
	'optin-survey-answer-browser-other' => 'Aandere webkieker:',
	'optin-survey-question-os' => 'Welk besturingssysteem he-j?',
	'optin-survey-answer-os-other' => 'Aander besturingssysteem:',
	'optin-survey-question-res' => 'Wat is joe beeldscharmreselusie?',
	'optin-survey-question-changes' => 'Wat zo-jie aanders willen in de toekomstige Bèta (kies meugelijkheen dee van toepassing bin)?',
	'optin-survey-answer-changes-nav' => 'De navigasie van de webstee makkelijker maken.',
	'optin-survey-answer-changes-edittools' => 'Meer en betere bewarkingshulpmiddels',
	'optin-survey-answer-changes-upload' => "Verbeter 't toevoegen van inhoud en media.",
	'optin-survey-answer-changes-richtext' => 'Ontwikkel bewarkingshulpmiddels vergeliekbaor mit uut-ebreien teksverwarkers.',
	'optin-survey-answer-changes-lookfeel' => 'De uutstraoling en warking van de webstee biewarken.',
	'optin-survey-answer-changes-predictability' => 'Verbeter de veurspelbaorheid van de prestasies',
	'optin-survey-answer-changes-custom' => 'Meer gebrukersanpassingen meugelijk maken.',
	'optin-survey-answer-changes-other' => 'Overig:',
	'optin-improvements2' => "== Wat is der verbeterd? ==
[[File:UsabilityToolbar.png|left|link=|Scharmofdrok van de verbeterde warkbalke]]
[[File:UsabilityDialogs.png|left|link=|Scharmofdrok van de nieje dialogen veur 't bewarken]]
;Verbeterde warkbalke
De verbeterde warkbalke maak 't makkelijker um pagina's te bewarken.
Nieje en verbeterde icoontjes maken 't dudelijker wat of da-j der allemaole mee doon kunnen.
Onderdelen dee-j uutklappen kunnen, maken 't overzichtelijker, terwiel je hulpmiddels dee-j haos nooit gebruken mit een klik op de muusknoppe verbargen kunnen.
;Dialogen
Bie 't maken van verwiezingen of tebellen, geet der noen a-j op 't icoontjen klikken een dialoogvienster los, dat joe zal helpen um verwiezingen en tebellen te maken via 't invullen van een eenvoudig formelier.
;En meer
Wie hemmen nog wat aandere verbeteringen derbie edaon zoas verbeterde navigasie, inklapbaore tabblaojen, een verbeterde zeukfunctie, en meer.
== Hoe ku-j meedoon ==
Um meer te weten te koemen over 't Bruukbaorheidsinitiatief ku-j [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative onze webstee] bezeuken.
<div style=\"clear:both\"></div>",
);

/** Dutch (Nederlands)
 * @author McDutchie
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
	'optin-survey-question-changes' => 'Welke wijzigingen wilt u in de toekomst in Beta zien? (selecteer alle mogelijkheden die van toepassing zijn)',
	'optin-survey-answer-changes-nav' => 'Maak de navigatie van de site eenvoudiger.',
	'optin-survey-answer-changes-edittools' => 'Meer en betere bewerkingshulpmiddelen.',
	'optin-survey-answer-changes-upload' => 'Verbeter het toevoegen van inhoud en mediauploads.',
	'optin-survey-answer-changes-richtext' => 'Ontwikkel bewerkingshulpmiddelen vergelijkbaar met uitgebreide tekstverwerkers.',
	'optin-survey-answer-changes-lookfeel' => 'Werkt het uiterlijk en de werking van de bij naar de laatste inzichten.',
	'optin-survey-answer-changes-predictability' => 'Verbeter de voorspelbaarheid van de prestaties.',
	'optin-survey-answer-changes-custom' => 'Meer gebruikersaanpassingen mogelijk maken.',
	'optin-survey-answer-changes-other' => 'Overig:',
	'optin-improvements2' => '== Wat is er verbeterd? ==
[[File:UsabilityToolbar.png|left|link=|Schermafbeelding van de uitgebreide werkbalk]]
[[File:UsabilityDialogs.png|left|link=|Schermafbeelding van de nieuwe dialogen voor bewerken]]
;Verbeterde werkbalk
De verbeterde werkbalk maakt het makkelijker om pagina\'s te bewerken te bewerken.
Nieuwe en verbeterde pictogrammen maken het duidelijker welke handelingen er met elk gereedschap uit te voeren zijn.
Uit te klappen onderdelen maken het overzichtelijker, terwijl zelden gebruikt gereedschap slechts een muisklik verwijderd is.
;Dialogen
Bij het maken van verwijzingen of tabellen, opent klikken op het pictogram nu een dialoogvenster dat u helpt om verwijzingen en tabellen te maken via het invullen van een eenvoudig formulier.
;En meer
Wij hebben een aantal andere verbeteringen toegevoegd zoals verbeterde navigatie, inklapbare tabbladen
, een verbeterde zoekfunctie, en meer.
== Hoe kunt u meedoen ==
Om meer te weten te komen over het Bruikbaarheidsinitiatief kunt u [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative onze website] bezoeken.
<div style="clear:both"></div>',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Frokor
 * @author Gunnernett
 * @author Harald Khan
 */
$messages['nn'] = array(
	'optin-title-optedout' => 'Prøv betautgåva',
	'optin-title-optedin' => 'Forlat betautgåva',
	'optin-title-justoptedin' => 'Velkommen til betautgåva',
	'optin-title-justoptedout' => 'Takk for at du prøver betautgåva',
	'optin-title-feedback' => 'Tilbakemelding om betautgåva',
	'optin-needlogin' => 'Du må [$1 logga inn] for å prøve betautgåva.',
	'optin-intro' => 'Wikipedia-brukarvenlegheitsinitiativet har arbeitt hardt for å betra brukarvenlegheita til Wikipedia.
Ynskjer du å prøva betaversjonen vår?',
	'optin-feedback-intro' => 'Takk for at du prøver Beta.
Me vil gjerne veta kva du meiner om det nye grensesnittet vårt, og ynskjer at du deltek i den valfrie undersøkinga under.',
	'optin-feedback-back' => 'Om du ikkje ynskjer å fylla ut undersøkinga, kan du gå attende til $1.',
	'optin-success-in' => 'Du prøver no ut Beta.
Du kan trekkja deg frå utprøvinga kva tid som helst ved å trykkja på «{{int:optin-leave}}»-lenkja på øvst på sida.',
	'optin-leave-cancel' => 'Skulle du ynskja å halda fram med å nytta Beta, kan du gå attende til $1.',
	'optin-success-out' => 'Du har forlate Beta.
Du kan prøva han ut att kva tid som helst ved å trykkja på «{{int:optin-try}}» -lenkja øvst på sida.',
	'optin-success-feedback' => 'Takk for tilbakemeldinga di!',
	'optin-accept-short' => 'Lat oss gjera det!',
	'optin-accept-long' => 'Prøv Beta',
	'optin-accept-long-anon' => 'Logg inn og prøv Beta',
	'optin-deny-short' => 'Nei takk',
	'optin-deny-long' => 'Gå attende til førre sida',
	'optin-submit-out' => 'Gå ut av Beta',
	'optin-submit-feedback' => 'Gje tilbakemelding',
	'optin-survey-yes' => 'Ja',
	'optin-survey-no' => 'Nei',
	'optin-survey-intro' => 'Takk for at du prøvde Beta.
Me skulle likt å veta kva du tenkjer om det nye grensesnittet vårt, so me ville sett pris på om du fylte ut den valfrie undersøkinga nedanfor før du trykkjer på  «[[#leave|{{int:optin-submit-out}}]]».',
	'optin-survey-question-likedislike' => 'Kva lika du med Beta? Kva mislika du med Beta?',
	'optin-survey-question-whyoptout' => 'Kvifor går du ut av Beta? (Vel alt som passar.)',
	'optin-survey-answer-whyoptout-hard' => 'Han var for vanskeleg å nytta.',
	'optin-survey-answer-whyoptout-didntwork' => 'Han fungerte ikkje på rett vis.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Han oppførte seg ikkje som ein skulle venta.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Eg likte ikkje utsjånaden.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Eg lika ikkje dei nye fanene og utsjånaden.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Eg lika ikkje den nye verktylina.',
	'optin-survey-answer-whyoptout-other' => 'Anna årsak:',
	'optin-survey-question-explain' => 'Forklår eller utdjup gjerne svaret du gav over:',
	'optin-survey-question-techfail' => 'Møtte du på tekniske feil medan du nytta Beta?',
	'optin-survey-question-techfail-ifyes' => 'Om ja, utdjup eller forklår:',
	'optin-survey-question-usedtoolbar' => 'Har du nytta den nye utvidbare verktylina til å endra ei sida?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Om ja, kva lika du med ho? Kva lika du ikkje med ho?',
	'optin-survey-question-usedtoolbar-ifno' => 'Om nei, kvifor ikkje? Vil du nytta ho i framtida? Utdjup gjerne.',
	'optin-survey-question-different' => 'Kva kunne me ha gjort annleis for at du skulle ha heldt fram med å nytta Beta?',
	'optin-survey-question-feedback' => 'Lat oss gjerne få veta om andre kommentarar, tankar eller anna som du vil ha delt - all tilbakemelding er nyttig:',
	'optin-survey-question-browser' => 'Kva for ein nettlesar nyttar du?',
	'optin-survey-answer-browser-other' => 'Annan nettlesar:',
	'optin-survey-question-os' => 'Kva for operativsystem nyttar du?',
	'optin-survey-answer-os-other' => 'Anna operativsystem:',
	'optin-survey-question-res' => 'Kva er oppløysinga på skjermen din?',
	'optin-survey-question-changes' => 'Kva endringar skulle du ha likt å sett i Beta i framtida (vel alt som passar)?',
	'optin-survey-answer-changes-nav' => 'Gjera det lettare å navigera på denne nettsida.',
	'optin-survey-answer-changes-edittools' => 'Utvida og betra endringsverktya.',
	'optin-survey-answer-changes-upload' => 'Betra systemet for opplasting av innhald og media.',
	'optin-survey-answer-changes-richtext' => 'Utvikla endringsverkty liknande teksthandsamingsprogram.',
	'optin-survey-answer-changes-lookfeel' => 'Oppdatera utsjånad og presentasjon av nettsidene.',
	'optin-survey-answer-changes-custom' => 'Tillata meir brukartilpassing.',
	'optin-survey-answer-changes-other' => 'Andre:',
	'optin-improvements2' => '== Kva er betra? ==
[[File:UsabilityToolbar.png|left|link=|Skjermbilete av den betra verktylina]]
[[File:UsabilityDialogs.png|left|link=|Skjermbilete av dei nye dialogane for innhaldsgenerering]]
;Betra verktylina
Den betra verktylina gjer det enklare å endra artiklar.
Nye og betra ikon gjer det klårare kva handlingar kvart einskild verkty utfører. Utvidbare seksjonar reduserer rot, medan verkty som vert nytta lite stadig berre er eit klikk unna.
;Dialog
Når du opprettar lenkjer eller tabellar vil verktyikonet opna ein dialogboks som hjelper deg gjennom å lata deg fylla ut eit enkelt skjema.
;Meir
Me har introdusert ei rekkja andre betringar, sånn som betra navigasjon, fallande faner, betra søk og meir.

== Koss involvera deg ==
For å læra meir om prosjektet eller for å verta  involvert, sjå [http://usability.wikimedia.org wikien til brukarvenlegheitsinitativet].
<div style="clear:both"></div>',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 * @author Laaknor
 * @author Nghtwlkr
 * @author Simny
 * @author Stigmj
 */
$messages['no'] = array(
	'optin' => 'Deltakelse i brukervennlighetsprosjektet',
	'optin-desc' => 'La brukere prøve betaversjonen, brukervennlighetsprosjektets nyeste forbedringer.',
	'optin-title-optedout' => 'Prøv Beta',
	'optin-title-optedin' => 'Forlat Beta',
	'optin-title-justoptedin' => 'Velkommen til Beta',
	'optin-title-justoptedout' => 'Takk for at du prøver vår Beta',
	'optin-title-feedback' => 'Tilbakemelding om Beta',
	'optin-needlogin' => 'Du må [$1 logge inn] for å prøve Beta',
	'optin-intro' => 'Wikipedias brukervennlighetsprosjekt har jobbet hardt for å forbedre brukervennligheten til Wikipedia.
Ønsker du å prøve betaversjonen?',
	'optin-feedback-intro' => 'Takk for at du prøver Beta.
Vi vil gjerne vite hva du mener om vårt nye grensesnitt, og vil gjerne at du fyller ut den valgfrie undersøkelsen under.',
	'optin-feedback-back' => 'Hvis du ikke vil fylle ut undersøkelsen, kan du gå tilbake til $1.',
	'optin-success-in' => 'Du prøver nå Beta.
Du kan velge å gå tilbake når som helst ved å klikke på "{{int:optin-leave}}"-lenken øverst på siden.',
	'optin-leave-cancel' => 'Hvis du vil fortsette å bruke Beta kan du gå tilbake til $1.',
	'optin-success-out' => 'Du har forlat Beta.
Du kan forsøke det igjen ved å klikke på "{{int:optin-try}}"-lenken på toppen av skjermen.',
	'optin-success-feedback' => 'Takk for din tilbakemelding!',
	'optin-accept-short' => 'La oss gjøre det!',
	'optin-accept-long' => 'Prøv Beta',
	'optin-accept-long-anon' => 'Logg inn og prøv Beta',
	'optin-deny-short' => 'Neitakk',
	'optin-deny-long' => 'Gå tilbake til forrige side',
	'optin-submit-out' => 'Forlat Beta',
	'optin-submit-feedback' => 'Gi tilbakemelding',
	'optin-survey-yes' => 'Ja',
	'optin-survey-no' => 'Nei',
	'optin-survey-intro' => 'Takk for at du prøver Betaen.
Vi vil gjerne vite hva du tenker om vårt nye grensesnitt, så vi vil sette pris på om du fyller ut et valgfritt skjema under, før du trykker på "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Hva likte du med Beta? Hva likte du ikke om Beta?',
	'optin-survey-question-whyoptout' => 'Hvorfor forlater du Beta? (Vær vennlig og velg alle grunner du mener er aktuelle.)',
	'optin-survey-answer-whyoptout-hard' => 'Den var for vanskelig å bruke.',
	'optin-survey-answer-whyoptout-didntwork' => 'Den virket ikke ordentlig.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Den oppførte seg ikke som jeg skulle forvente.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Jeg likte ikke måten det så ut.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Jeg likte ikke de nye fanene og utseende.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Jeg likte ikke den nye menyen.',
	'optin-survey-answer-whyoptout-other' => 'Annen årsak:',
	'optin-survey-question-explain' => 'Forklar eller utdyp gjerne svarene dine over:',
	'optin-survey-question-techfail' => 'Har du opplevd noen tekniske feil mens du brukte Beta?',
	'optin-survey-question-techfail-ifyes' => 'Hvis ja, forklar:',
	'optin-survey-question-usedtoolbar' => 'Har du brukt den nye utvidbare menyen for å redigere en side?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Hvis ja, hva likte du med den? Hva likte du ikke om den?',
	'optin-survey-question-usedtoolbar-ifno' => 'Hvis nei, hvorfor ikke? Vil du gjøre det i fremtiden? Vær så snill og forklar.',
	'optin-survey-question-different' => "Hva kunne vi gjort annerledes for at du skulle fortsatt å bruke denne betaversjonen ''(tilbakemelding på engelsk)''?",
	'optin-survey-question-feedback' => 'Vennligst gi oss dine tilbakemeldinger:',
	'optin-survey-question-browser' => 'Hvilken nettleser bruker du?',
	'optin-survey-answer-browser-other' => 'Annen nettleser:',
	'optin-survey-question-os' => 'Hvilket operativsystem bruker du?',
	'optin-survey-answer-os-other' => 'Annet operativsystem:',
	'optin-survey-question-res' => 'Hva er skjermoppløsningen din?',
	'optin-survey-question-changes' => 'Hvilke forandringer kunne du tenkt deg å se i betaversjonen i framtiden (velg alle som passer)?',
	'optin-survey-answer-changes-nav' => 'Gjør det lettere å navigere på denne nettsiden.',
	'optin-survey-answer-changes-edittools' => 'Utvid og forbedre redigeringsverktøyene.',
	'optin-survey-answer-changes-upload' => 'Forbedre systemet for opplasting av innhold og media.',
	'optin-survey-answer-changes-richtext' => 'Utvikle redigeringsverktøy tilsvarende for vanlige ordbehandlingsprogram.',
	'optin-survey-answer-changes-lookfeel' => 'Oppdatere utseende og presentasjon av nettsidene.',
	'optin-survey-answer-changes-predictability' => 'Forbedre forutsigbarheten av responsen/hastigheten til nettsidene.',
	'optin-survey-answer-changes-custom' => 'Tillat mere skreddersydde innstillinger.',
	'optin-survey-answer-changes-other' => 'Andre ting:',
	'optin-improvements2' => '== Hva har blitt forbedret? ==
[[File:UsabilityToolbar.png|left|link=|Skjermbilde av den forbedrede verktøylinjen]]
[[File:UsabilityDialogs.png|left|link=|Skjermbilde av de nye dialogene for innholdsgenerering]]
;Forbedret verktøylinje
Den forbedrede verktøylinjen gjør det lettere å redigere artikler. Nye og forbedrede ikoner gjør det klarere hvilken handling hvert verktøy utfører. Utvidede seksjoner reduserer rot samtidig som den gjemmer sjeldent brukte verktøy et klikk unna.
;Dialog
Når man oppretter lenker eller tabeller vil klikking på verktøyikonet nå åpne en dialogboks som hjelper deg med å opprette lenker og tabeller ved at du fyller ut et enkelt skjema.
;Og mer
Vi har introdusert en rekke forbedringer som forbedret navigasjon, fallende faner, forbedret søk og mer.
== Hvordan bli involvert ==
For å finne ut mer om Brukervennlighetsinitiativet, besøk vår [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative hjemmeside].
<div style="clear:both"></div>',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'optin' => "Participar a l'iniciativa d'utilizabilitat",
	'optin-desc' => "Permet als utilizaires d'ensajar la Bèta, los melhoraments de l'iniciativa d'utilizabilitat.",
	'optin-title-optedout' => 'Ensajar la Bèta',
	'optin-title-optedin' => 'Quitar la Bèta',
	'optin-title-justoptedin' => 'Benvenguda dins la Bèta',
	'optin-title-justoptedout' => 'Mercés per vòstra participacion a la Bèta',
	'optin-title-feedback' => 'Balhatz vòstre vejaire',
	'optin-needlogin' => 'Vos cal vos [$1 connectar] per ensajar la Bèta.',
	'optin-intro' => "L'iniciativa d'utilizabilitat de Wikipèdia a trabalhat dur per melhorar l'utilizabilitat de Wikipèdia.
Volètz ensajar la Bèta ?",
	'optin-feedback-intro' => "Mercés d'ensajar la Bèta.
Nos agradariá saber çò que pensatz de nòstra interfàcia novèla, nos agradariá qu'emplenèssetz lo questionari çaijós.",
	'optin-feedback-back' => 'Se volètz pas emplenar lo questionari, podètz tornar a $1.',
	'optin-success-in' => "Sètz a testar Bèta.
Podètz interrompre la sesilha a tot moment en clicant sul ligam « {{int:optin-leave}} » en naut a drecha de l'ecran.",
	'optin-leave-cancel' => "Se volètz contunhar d'utilizar la Bèta, podètz tornar a $1.",
	'optin-success-out' => "Avètz quitat Bèta.
Podètz participar tornamai a tot moment en clicant sul ligam « {{int:optin-try}} » en naut a drecha de l'ecran.",
	'optin-success-feedback' => 'Mercés per vòstre vejaire !',
	'optin-accept-short' => 'Anem !',
	'optin-accept-long' => 'Ensajar la Bèta',
	'optin-accept-long-anon' => 'Connectatz-vos e ensajatz la Bèta',
	'optin-deny-short' => 'Non mercés',
	'optin-deny-long' => 'Tornar a la pagina precedenta',
	'optin-submit-out' => 'Quitar la Bèta',
	'optin-submit-feedback' => 'Balhatz vòstre vejaire',
	'optin-survey-yes' => 'Òc',
	'optin-survey-no' => 'Non',
	'optin-survey-intro' => "Mercés per ensajar la Bèta.

Nos agradariá de saber çò que pensatz de nòstra interfàcia novèla, nos agradariá s'emplenèssetz l'escandalhatge opcional abans de clicar sus « [[#leave|{{int:optin-submit-out}}]] ».",
	'optin-survey-question-likedislike' => 'Qué vos a agradat dins la Bèta ? Qué vos a pas agradat dins la Bèta ?',
	'optin-survey-question-whyoptout' => 'Perqué quitatz la Bèta ? (seleccionatz tot çò que vos conven)',
	'optin-survey-answer-whyoptout-hard' => "Èra tròp complicat d'utilizar.",
	'optin-survey-answer-whyoptout-didntwork' => 'Aquò fonciona pas corrèctament.',
	'optin-survey-answer-whyoptout-notpredictable' => "Aquò s'es pas passat coma previst.",
	'optin-survey-answer-whyoptout-didntlike-look' => "Son aspècte m'a pas agradat.",
	'optin-survey-answer-whyoptout-didntlike-layout' => "Los onglets novèls e la disposicion novèla m'an pas agradat.",
	'optin-survey-answer-whyoptout-didntlike-toolbar' => "La barra d'espleches novèla m'a pas agradat.",
	'optin-survey-answer-whyoptout-other' => 'Autra rason :',
	'optin-survey-question-explain' => 'Explicatz o elaboratz vòstra responsa çaisús :',
	'optin-survey-question-techfail' => 'Avètz rencontrat de problèmas tecnics en utilizant la Bèta ?',
	'optin-survey-question-techfail-ifyes' => 'Se òc, explicatz o elaboratz :',
	'optin-survey-question-usedtoolbar' => "Avètz utilizat la barra d'espleches desenrotlabla novèla per modificar una pagina ?",
	'optin-survey-question-usedtoolbar-ifyes' => 'Se òc, qué vos a agradat e qué vos a pas agradat a son prepaus ?',
	'optin-survey-question-usedtoolbar-ifno' => "Se non, perqué ? L'utilizaretz dins lo futur ? Explicatz-vos.",
	'optin-survey-question-different' => "Qué auriá degut èsser diferent per que continhuèssetz d'utilizar nòstra Bèta ?",
	'optin-survey-question-feedback' => 'Balhatz-nos vòstra reaccion :',
	'optin-survey-question-browser' => 'Quin navigador utilizatz ?',
	'optin-survey-answer-browser-other' => 'Autre navigador :',
	'optin-survey-question-os' => 'Quin sistèma operatiu utilizatz ?',
	'optin-survey-answer-os-other' => 'Autre sistèma operatiu :',
	'optin-survey-question-res' => 'Quina es la resolucion de vòstre ecran ?',
	'optin-survey-question-changes' => 'Qué vos agradariá veire dins la Bèta dins lo futur (seleccionatz tot çò que conven) ?',
	'optin-survey-answer-changes-nav' => "Fa que la navigacion generala del site es mai simpla d'utilizar.",
	'optin-survey-answer-changes-edittools' => "Melhora e augmenta los espleches d'edicion.",
	'optin-survey-answer-changes-upload' => "Melhora l'impòrt de contengut e de mèdia.",
	'optin-survey-answer-changes-richtext' => "Desvolopa d'espleches de modificacions similars als editors en tèxte enriquit.",
	'optin-survey-answer-changes-lookfeel' => "Càmbia l'aparéncia del site.",
	'optin-survey-answer-changes-predictability' => 'Melhora la previzibilitat de las performàncias.',
	'optin-survey-answer-changes-custom' => "Permet una màger personalizacion de l'interfàcia.",
	'optin-survey-answer-changes-other' => 'Autre :',
);

/** Deitsch (Deitsch)
 * @author Xqt
 */
$messages['pdc'] = array(
	'optin-title-optedin' => 'Beta verlosse',
	'optin-title-justoptedin' => 'Wilkum zu Beta',
	'optin-deny-short' => 'Nee, ken Interest',
	'optin-deny-long' => 'Zerick zum letscht Blatt',
	'optin-submit-out' => 'Beta verlosse',
	'optin-survey-yes' => 'Ya',
	'optin-survey-no' => 'Nee',
	'optin-survey-answer-whyoptout-other' => 'Annre Grind:',
	'optin-survey-answer-browser-other' => 'Annre Browser:',
	'optin-survey-answer-changes-other' => 'Annre:',
);

/** Polish (Polski)
 * @author Leinad
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'optin' => 'Włączenie Inicjatywy Użyteczności',
	'optin-desc' => 'Pozwala użytkownikom wypróbować wersję testową zrealizowaną w ramach inicjatywy użyteczności.',
	'optin-title-optedout' => 'Wypróbuj wersję testową',
	'optin-title-optedin' => 'Koniec testowania',
	'optin-title-justoptedin' => 'Witaj w wersji testowej',
	'optin-title-justoptedout' => 'Dziękujemy za wypróbowanie wersji testowej',
	'optin-title-feedback' => 'Opinie o wersji testowej',
	'optin-needlogin' => 'Musisz [$1 zalogować się] by wypróbować wersję testową.',
	'optin-intro' => 'Podjęliśmy inicjatywę mającą poprawić użyteczność Wikipedii.
Czy chcesz wypróbować wersję testową?',
	'optin-feedback-intro' => 'Dziękujemy za wypróbowanie wersji testowej.
Chcielibyśmy wiedzieć co myślisz o nowym interfejsie. Będziemy wdzięczni, jeśli wypełnisz poniższą ankietę.',
	'optin-feedback-back' => 'Jeśli nie chcesz wypełnić ankiety, możesz powrócić do $1.',
	'optin-success-in' => 'Rozpocząłeś korzystanie z wersji testowej.
W każdej chwili możesz ją wyłączyć poprzez kliknięcie linku „{{int:optin-submit-out}}” umieszczonego na górze strony.',
	'optin-leave-cancel' => 'Jeśli chcesz nadal korzystać z wersji testowej, powróć do $1.',
	'optin-success-out' => 'Wyłączyłeś wersję testową.
W każdej chwili możesz ją przywrócić poprzez kliknięcie linku „{{int:optin-try}}” znajdującego się na górze, po prawej stronie.',
	'optin-success-feedback' => 'Dziękujemy za przesłaną opinię!',
	'optin-accept-short' => 'Tak, chcę!',
	'optin-accept-long' => 'Wypróbuj wersję testową',
	'optin-accept-long-anon' => 'Zaloguj się i wypróbuj wersję testową',
	'optin-deny-short' => 'Nie, dziękuję',
	'optin-deny-long' => 'Powrót do poprzedniej strony',
	'optin-submit-out' => 'Koniec testowania',
	'optin-submit-feedback' => 'Prześlij opinię',
	'optin-survey-yes' => 'Tak',
	'optin-survey-no' => 'Nie',
	'optin-survey-intro' => 'Dziękujemy za wypróbowanie wersji testowej.

Chcielibyśmy poznać Twoją opinię dotyczącą nowego interfejsu, w związku z tym będziemy wdzięczni za wypełnienie poniższej nieobowiązkowej ankiety, zanim klikniesz „[[#leave|{{int:optin-submit-out}}]]”.',
	'optin-survey-question-likedislike' => 'Co Ci się spodobało w wersji testowej? Co Ci się w niej nie podoba?',
	'optin-survey-question-whyoptout' => 'Dlaczego rezygnujesz z wersji testowej? (zaznacz wszystkie opcje, które pasują)',
	'optin-survey-answer-whyoptout-hard' => 'To było zbyt trudne, aby z tego korzystać.',
	'optin-survey-answer-whyoptout-didntwork' => 'Nie działał poprawnie.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Działa w sposób nieprzewidywalny.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Nie podoba mi się wygląd.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Nie podobają mi się nowe zakładki i układ.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Nie podoba mi się nowy pasek narzędzi.',
	'optin-survey-answer-whyoptout-other' => 'Inny powód',
	'optin-survey-question-explain' => 'Wyjaśnij lub omów swoją powyższą odpowiedź',
	'optin-survey-question-techfail' => 'Czy napotkałeś jakąś awarię techniczną podczas korzystania z wersji testowej?',
	'optin-survey-question-techfail-ifyes' => 'Jeśli tak, opowiedz o tym.',
	'optin-survey-question-usedtoolbar' => 'Czy korzystałeś z nowego rozszerzalnego paska narzędzi, aby edytować stronę?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Jeśli tak, to co Ci się podobało? Co Ci się nie podobało?',
	'optin-survey-question-usedtoolbar-ifno' => 'Jeśli nie, to dlaczego nie? Może jednak kiedyś? Wyjaśnij prosimy.',
	'optin-survey-question-different' => 'Co należy poprawić, aby przekonać Cię do korzystania z wersji testowej?',
	'optin-survey-question-feedback' => 'Podziel się z nami swoją opinią, wątpliwościami lub sugestiami – każdy komentarz jest mile widziany!',
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
	'optin-survey-question-os' => 'Z jakiego systemu operacyjnego korzystasz?',
	'optin-survey-answer-os-windows' => 'Windows',
	'optin-survey-answer-os-macos' => 'Mac OS',
	'optin-survey-answer-os-linux' => 'Linux',
	'optin-survey-answer-os-other' => 'Inny system operacyjny',
	'optin-survey-question-res' => 'Z jakiej rozdzielczości ekranu korzystasz?',
	'optin-survey-question-changes' => 'Jakie zmiany chciałbyś zobaczyć w wersji testowej w przyszłości? Zaznacz wszystkie prawidłowe odpowiedzi.',
	'optin-survey-answer-changes-nav' => 'Poprawienie ogólne nawigacji wewnątrz witryny, aby stała się łatwiejsza w użyciu.',
	'optin-survey-answer-changes-edittools' => 'Zwiększenie liczby i rozszerzenie funkcji narzędzi edycyjnych.',
	'optin-survey-answer-changes-upload' => 'Poprawienie przesyłania treści i plików.',
	'optin-survey-answer-changes-richtext' => 'Opracowanie narzędzi edycyjnych zbliżonych do tych z profesjonalnych edytorów tekstu.',
	'optin-survey-answer-changes-lookfeel' => 'Zaktualizowanie wyglądu i stylu witryny.',
	'optin-survey-answer-changes-predictability' => 'Poprawienie przewidywalności zmian.',
	'optin-survey-answer-changes-custom' => 'Umożliwienie lepszego dostosowania do indywidualnych potrzeb użytkownika.',
	'optin-survey-answer-changes-other' => 'Inne',
	'optin-improvements2' => '== Co jest lepsze? ==
[[File:UsabilityToolbar.png|left|link=|Rozszerzony pasek edycji]]
[[File:UsabilityDialogs.png|left|link=|Dialogi generowania nowej treści]]
;Rozbudowana belka narzędziowa
Bogatsza belka narzędziowa ułatwia edytowanie artykułów.
Nowe ulepszone ikony bardziej intuicyjnie przedstawiają działanie narzędzi.
Rozszerzalne sekcje zmniejszają bałagan udostępniając po kliknięciu rzadko używane narzędzia.
;Dialogi
Tworzenie łącza czy tabeli jest obecnie możliwe poprzez kliknięcie ikony narzędzia, które otwiera w oknie dialogowym prosty formularz.
;Więcej
Wprowadziliśmy szereg innych usprawnień takich jak rozszerzona nawigacji, kaskadowe zakładki, lepsze wyszukiwanie i inne.

== Jak się przyłączyć == 
Więcej na temat nowego interfejsu można znaleźć na stronie [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative Inicjatywy Użyteczności].
<div style="clear:both"></div>',
);

/** Piedmontese (Piemontèis)
 * @author Borichèt
 * @author Dragonòt
 */
$messages['pms'] = array(
	'optin' => "Partissipé a l'inissiativà ëd dovrabilità",
	'optin-desc' => "A përmet a j'utent ëd prové Beta, j'ùltime ameliorassion a l'usagi dl'Inissiativa për n'usagi pì belfé.",
	'optin-title-optedout' => 'Prové Beta',
	'optin-title-optedin' => 'Chité Beta',
	'optin-title-justoptedin' => 'Bin-ëvnù an Beta',
	'optin-title-justoptedout' => 'Mersì ëd prové nòstr Beta',
	'optin-title-feedback' => 'Sugeriment për Beta',
	'optin-needlogin' => 'A dev [$1 rintré ant ël sistema] për prové Beta.',
	'optin-intro' => "L'Inissiativa për n'usagi pì belfé ëd Wikipedia a l'ha travajà dur për rende pì belfé dovré Wikipedia.
Vorërij-lo prové nostr Beta?",
	'optin-feedback-intro' => "Mersì ëd prové Beta.
An farìa gòj conòsse lòn ch'a pensa ëd nòstra neuva antërfassa, donca an farìa piasì s'a rëspondèissa al chestionari opsional sì-sota.",
	'optin-feedback-back' => "S'a veul pa rësponde al chestionari, a peul torné a $1.",
	'optin-success-in' => "A l'é an camin ch'a preuva nòstr Beta.
A peul anterompe a qualsëssìa moment an ësgnacand an sl'anliura «{{int:optin-leave}}» an sima dla pàgina.",
	'optin-leave-cancel' => "S'a veul continué a dovré Beta, a peul torné a $1.",
	'optin-success-out' => "A l'ha chità con sucess Beta.
A peul provelo torna a qualsëssìa moment an ësgnacand an sl'anliura «{{int:optin-try}}» an sima dla pàgina.",
	'optin-success-feedback' => 'Mersì ëd soa opinion!',
	'optin-accept-short' => 'Foma che felo!',
	'optin-accept-long' => 'Prové Beta',
	'optin-accept-long-anon' => 'Rintré ant ël sistema e prové Beta',
	'optin-deny-short' => 'No, mersì',
	'optin-deny-long' => 'Torna a la pàgina ëd prima',
	'optin-submit-out' => 'Chité Beta',
	'optin-submit-feedback' => "Smon-e n'opinion",
	'optin-survey-yes' => 'É',
	'optin-survey-no' => 'Nò',
	'optin-survey-intro' => "Mersì ëd prové Beta.
An farìa gòj savèj lòn ch'a pensa ëd nòstra neuva antërfassa, parèj an farìa piasì s'a rëspondèissa a chestionari opsional sì-sota prima dë sgnaché ansima a «[[#leave|{{int:optin-submit-out}}]]».",
	'optin-survey-question-likedislike' => "Lòn ch'a l'é piasuje ëd Beta? Lòn ch'a l'é nen piasuje ëd Beta?",
	'optin-survey-question-whyoptout' => "Përchè a chita Beta? (Ch'a selession-a tut lòn ch'a-i va)",
	'optin-survey-answer-whyoptout-hard' => "A l'era tròp malfé dovrelo.",
	'optin-survey-answer-whyoptout-didntwork' => 'A marciava nen bin.',
	'optin-survey-answer-whyoptout-notpredictable' => 'A marciava pa coma previst.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Am piasìa nen sò aspet.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'A son nen piasume ij neuv quàder e la neuva disposission.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => "A l'é nen piasume la neuva bara dj'utiss.",
	'optin-survey-answer-whyoptout-other' => 'Àutra rason:',
	'optin-survey-question-explain' => "Për piasì, ch'a spiega o ch'a aprofondissa soa rispòsta si-dzora:",
	'optin-survey-question-techfail' => "A l'ha rancontrà dij problema técnich antramentre ch'a dovrava la Beta?",
	'optin-survey-question-techfail-ifyes' => "Se sì, për piasì ch'a aprofondissa o ch'a spiega:",
	'optin-survey-question-usedtoolbar' => "Ha-lo dovrà la neuva bara dj'utiss slargàbil për modifiché na pàgina?",
	'optin-survey-question-usedtoolbar-ifyes' => "Se sì, lòn ch'a l'é piasuje ëd sòn? Lòn ch'a l'é piasuje nen?",
	'optin-survey-question-usedtoolbar-ifno' => "Si nò, përchè? A lo farà ant l'avnì? Për piasì, ch'a spiega.",
	'optin-survey-question-different' => "Lòn ch'i l'avrìo podù fé 'd diferent përchè chiel a continuèissa a dovré la Beta?",
	'optin-survey-question-feedback' => "Për piasì, ch'an fasa savèj qualsëssìa àutr coment, crussi o pensé ch'a l'abia për nojàutri - tute j'opinion a servo:",
	'optin-survey-question-browser' => "Che navigador ch'a deuvra?",
	'optin-survey-answer-browser-other' => 'Àutr navigador:',
	'optin-survey-question-os' => "Che sistema operativ ch'a deuvra?",
	'optin-survey-answer-os-other' => 'Àutr sistem operativ:',
	'optin-survey-question-res' => "Cola ch'a l'é l'arzolussion ëd tò scren?",
	'optin-survey-question-changes' => "Che cangiament voriës-to vëdde ant la Beta ant l'avnì (për piasì selession-a tut lòn ch'it veule)?",
	'optin-survey-answer-changes-nav' => "Rende l'antrega navigassion dël sit pi bel fé da dovré.",
	'optin-survey-answer-changes-edittools' => "Aumenté e amelioré j'utiss ëd modìfica.",
	'optin-survey-answer-changes-upload' => 'Amelioré ël cariament ëd contnù e ëd multimojen.',
	'optin-survey-answer-changes-richtext' => "Dësvlupé utiss ëd modìfica ch'a smijo a editor ëd test anrichì.",
	'optin-survey-answer-changes-lookfeel' => "Modifiché l'aparensa dël sit.",
	'optin-survey-answer-changes-predictability' => 'Amelioré la prevision ëd le prestassion.',
	'optin-survey-answer-changes-custom' => "Përmëtte pi grande përsonalisassion a l'utent.",
	'optin-survey-answer-changes-other' => 'Àutr:',
	'optin-improvements2' => "== Lòn ch'a l'é stàit ameliorà?==
[[File:UsabilityToolbar.png|left|link=|Scren ëd la bara dj'utiss ëd modìfica ameliorà]]
[[File:UsabilityDialogs.png|left|link=|Scren ëd l'antërfacia ëd generassion contnù neuva]]
;Bara dj'utiss ameliorà
La bara dj'utiss ameliorà a fà pì bel fé modifiché j'artìcoj.
Plance neuve e ameliorà a rendo pi ciàir che assion a fà minca utiss.
Session espandùe a ridùvo ël rabel antant ch'a rendo j'utiss pòch dovrà a portà 'd selession.
;Diàlogh
Quand as creo colegament o tàule, an sgnacand la plancia dl'utiss, adess as deurb na fnestra ëd diàlogh che a-j giuta a creé colegament e tàule an compiland un formolari sempi.
;Ëd pi
I l'oma antrodovù vàire àutre  ameliorassion coma la navigassion ameliorà, liste a ridò, arserca ameliorà, e ëd pi.
== Com esse anteressà ==
Për savèjne ëd pi an sl'Inissiativa për n'usagi belfé, për piasì ch'a vìsita nòsta [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative pàgina d'intrada].
<div style=\"clear:both\"></div>",
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'optin-title-optedout' => 'آزمېښتي بڼه کارول',
	'optin-accept-short' => 'راځه چې وې آزمويو!',
	'optin-deny-short' => 'نه مننه',
	'optin-survey-yes' => 'هو',
	'optin-survey-no' => 'نه',
	'optin-survey-answer-whyoptout-other' => 'بل سبب:',
	'optin-survey-question-browser' => 'تاسې کوم کتنمل کاروۍ؟',
	'optin-survey-answer-browser-other' => 'بل کتنمل:',
	'optin-survey-question-os' => 'تاسې کوم چليز غونډال کاروۍ؟',
	'optin-survey-answer-changes-other' => 'بل:',
);

/** Portuguese (Português)
 * @author Capmo
 * @author Giro720
 * @author Hamilton Abreu
 * @author Malafaya
 * @author Waldir
 */
$messages['pt'] = array(
	'optin' => 'Participar na Iniciativa de Usabilidade',
	'optin-desc' => 'Permitir que os utilizadores experimentem o tema Beta, que tem as melhorias mais recentes da Iniciativa de Usabilidade.',
	'optin-title-optedout' => 'Experimentar o Beta',
	'optin-title-optedin' => 'Sair do Beta',
	'optin-title-justoptedin' => 'Bem-vindo ao Beta',
	'optin-title-justoptedout' => 'Obrigado por experimentar o Beta',
	'optin-title-feedback' => 'Enviar comentários',
	'optin-needlogin' => 'Precisa de [$1 autenticar-se] para experimentar o Beta.',
	'optin-intro' => 'A Iniciativa de Usabilidade da Wikipédia tem trabalhado arduamente para melhorar a usabilidade da Wikipédia.
Gostaria de experimentar a versão Beta?',
	'optin-feedback-intro' => 'Obrigado por ter experimentado o Beta.
Gostaríamos de saber o que acha da nossa nova interface, por isso agradeciamos que preenchesse o questionário opcional abaixo.',
	'optin-feedback-back' => 'Se não quiser preencher o questionário, pode regressar à página $1.',
	'optin-success-in' => 'Está agora a experimentar o Beta.
Pode optar por sair a qualquer momento, clicando o link "{{int:optin-leave}}" no topo da página.',
	'optin-leave-cancel' => 'Se quiser continuar a usar o Beta, pode voltar para $1.',
	'optin-success-out' => 'Saiu do Beta.
Pode experimentá-lo novamente a qualquer momento, clicando o link "{{int:optin-try}}" no topo da página.',
	'optin-success-feedback' => 'Obrigado pelos seus comentários!',
	'optin-accept-short' => 'Ok, vamos fazê-lo!',
	'optin-accept-long' => 'Experimentar o Beta',
	'optin-accept-long-anon' => 'Entre e experimente o Beta',
	'optin-deny-short' => 'Não, obrigado',
	'optin-deny-long' => 'Regressar à página anterior',
	'optin-submit-out' => 'Sair do Beta',
	'optin-submit-feedback' => 'Enviar comentários',
	'optin-survey-yes' => 'Sim',
	'optin-survey-no' => 'Não',
	'optin-survey-intro' => 'Obrigado por experimentar o Beta.
Gostaríamos de saber o que achou da nossa nova interface, por isso agradecíamos que preenchesse o questionário opcional abaixo, antes de clicar "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Do que foi que gostou no Beta? Do que foi que não gostou no Beta?',
	'optin-survey-question-whyoptout' => 'Por que é que está a sair do Beta? (Seleccione todas as opções que se aplicam, por favor.)',
	'optin-survey-answer-whyoptout-hard' => 'Foi muito difícil de usar.',
	'optin-survey-answer-whyoptout-didntwork' => 'Não funcionou correctamente.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Não funcionou de forma consistente.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Não gostei do aspecto.',
	'optin-survey-answer-whyoptout-didntlike-layout' => "Não gostei dos novos separadores e ''design''.",
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Não gostei da nova barra de ferramentas.',
	'optin-survey-answer-whyoptout-other' => 'Outro motivo:',
	'optin-survey-question-explain' => 'Por favor explique ou elabore a sua resposta anterior:',
	'optin-survey-question-techfail' => 'Sofreu alguma falha técnica ao usar o Beta?',
	'optin-survey-question-techfail-ifyes' => 'Se sim, por favor descreva ou explique:',
	'optin-survey-question-usedtoolbar' => 'Usou a nova barra de ferramentas expansível para editar uma página?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Se sim, do que foi que gostou nela? E do que foi que não gostou?',
	'optin-survey-question-usedtoolbar-ifno' => 'Se não, porque não? Irá usar no futuro? Por favor, elabore.',
	'optin-survey-question-different' => 'O que é que poderíamos ter feito de forma diferente para que continuasse a usar o Beta?',
	'optin-survey-question-feedback' => 'Por favor, faça quaisquer outros comentários, críticas ou sugestões que tenha - todas as opiniões são úteis:',
	'optin-survey-question-browser' => 'Que browser utiliza?',
	'optin-survey-answer-browser-other' => 'Outro browser:',
	'optin-survey-question-os' => 'Que sistema operativo utiliza?',
	'optin-survey-answer-os-other' => 'Outro sistema operativo:',
	'optin-survey-question-res' => 'Qual é a resolução do seu monitor?',
	'optin-survey-question-changes' => 'Que mudanças gostaria de ver futuramente no Beta? (Por favor, seleccione todas as opções que se aplicam.)',
	'optin-survey-answer-changes-nav' => 'Tornar a navegação geral do site mais fácil de usar.',
	'optin-survey-answer-changes-edittools' => 'Criar mais e melhores ferramentas de edição.',
	'optin-survey-answer-changes-upload' => 'Melhorar o carregamento de conteúdo e multimédia.',
	'optin-survey-answer-changes-richtext' => 'Desenvolver ferramentas de edição semelhantes aos editores de texto avançados.',
	'optin-survey-answer-changes-lookfeel' => 'Renovar o aspecto geral do site.',
	'optin-survey-answer-changes-predictability' => 'Melhorar a consistência dos resultados.',
	'optin-survey-answer-changes-custom' => 'Permitir um maior grau de personalização pelo utilizador.',
	'optin-survey-answer-changes-other' => 'Outros:',
	'optin-improvements2' => '== O que foi melhorado? ==
[[File:UsabilityToolbar.png|left|link=|Imagem da barra melhorada das ferramentas de edição]]
[[File:UsabilityDialogs.png|left|link=|Imagem dos diálogos de geração de novos conteúdos]]
;Barra de ferramentas melhorada
A barra de ferramentas melhorada facilita a edição de artigos.
Ícones novos e melhorados tornam mais clara a função de cada ferramenta.
Secções expandidas reduzem a confusão ao mesmo tempo que mantêm as ferramentas pouco usadas à distância de um clique.
;Diálogo
Ao criar links ou tabelas, clicar o ícone de ferramentas abre agora um diálogo que auxilia a sua criação preenchendo um formulário simples.
;E mais
Foram introduzidas várias melhorias, como a navegação melhorada, os separadores em cascata, a pesquisa melhorada e outras.
== Como pode envolver-se ==
Para saber mais sobre a Iniciativa de Usabilidade, visite a nossa [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative página].
<div style="clear:both"></div>',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author 555
 * @author Giro720
 * @author Heldergeovane
 */
$messages['pt-br'] = array(
	'optin' => 'Participar na Iniciativa de Usabilidade',
	'optin-desc' => 'Permitir que os usuários experimentem o Beta, as melhorias mais recentes da Iniciativa de Usabilidade.',
	'optin-title-optedout' => 'Experimentar o Beta',
	'optin-title-optedin' => 'Sair do Beta',
	'optin-title-justoptedin' => 'Bem-vindo ao Beta',
	'optin-title-justoptedout' => 'Obrigado por experimentar o Beta',
	'optin-title-feedback' => 'Enviar comentários',
	'optin-needlogin' => 'Você precisa [$1 autenticar-se] para experimentar o Beta.',
	'optin-intro' => 'A Iniciativa de Usabilidade da Wikipédia tem trabalhado arduamente para melhorar a usabilidade da Wikipédia.
Gostaria de experimentar nosso Beta?',
	'optin-feedback-intro' => 'Obrigado por experimentar o Beta.
Gostaríamos de saber o que você acha da nossa nova interface, por isso agradeceríamos se você preenchesse a pesquisa opcional abaixo.',
	'optin-feedback-back' => 'Se você não quiser preencher a pesquisa, pode voltar para $1.',
	'optin-success-in' => 'Agora você está experimentando o Beta.
Você pode optar por sair a qualquer momento, clicando na ligação "{{int:optin-leave}}" no topo da tela.',
	'optin-leave-cancel' => 'Se você quiser continuar usando o Beta, você pode voltar para $1.',
	'optin-success-out' => 'Você saiu do Beta.
Você pode experimentá-lo novamente a qualquer momento, clicando na ligação "{{int:optin-try}}" na parte superior direita da tela.',
	'optin-success-feedback' => 'Obrigado pelos seus comentários!',
	'optin-accept-short' => 'Ok, vamos fazê-lo!',
	'optin-accept-long' => 'Experimente o Beta',
	'optin-accept-long-anon' => 'Entre e experimente o Beta',
	'optin-deny-short' => 'Não, obrigado',
	'optin-deny-long' => 'Voltar à página anterior',
	'optin-submit-out' => 'Sair do Beta',
	'optin-submit-feedback' => 'Enviar comentários',
	'optin-survey-yes' => 'Sim',
	'optin-survey-no' => 'Não',
	'optin-survey-intro' => 'Obrigado por experimentar o Beta.
Gostaríamos de saber o que você acha da nossa nova interface, por isso agradeceríamos se você preenchesse a pesquisa opcional abaixo, antes de clicar em "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'De que você gostou no Beta? De que você não gostou no Beta?',
	'optin-survey-question-whyoptout' => 'Por que você está saindo do Beta? (Por favor, selecione todas as opções que se aplicam.)',
	'optin-survey-answer-whyoptout-hard' => 'Foi muito difícil de usar.',
	'optin-survey-answer-whyoptout-didntwork' => 'Não funcionou corretamente.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Não funcionou como esperado.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Eu não gostei do seu aspecto.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Eu não gostei das novas abas e design.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Eu não gostei da nova barra de ferramentas.',
	'optin-survey-answer-whyoptout-other' => 'Outro motivo:',
	'optin-survey-question-explain' => 'Por favor explique ou elabore a sua resposta anterior:',
	'optin-survey-question-techfail' => 'Você sofreu alguma falha técnica ao usar o Beta?',
	'optin-survey-question-techfail-ifyes' => 'Se sim, por favor descreva ou explique:',
	'optin-survey-question-usedtoolbar' => 'Você usou a nova barra de ferramentas expansível para editar uma página?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Se sim, de que você gostou nela? De que você não gostou nela?',
	'optin-survey-question-usedtoolbar-ifno' => 'Se não, porque não? Irá no futuro? Por favor, explique.',
	'optin-survey-question-different' => 'O que poderíamos ter feito de forma diferente para que você continuasse a usar o Beta?',
	'optin-survey-question-feedback' => 'Por favor, indique-nos quaisquer outros comentários, críticas ou sugestões que você tenha para nós - todas as opiniões são úteis!',
	'optin-survey-question-browser' => 'Que navegador você usa?',
	'optin-survey-answer-browser-other' => 'Outro navegador:',
	'optin-survey-question-os' => 'Que sistema operacional você usa?',
	'optin-survey-answer-os-other' => 'Outro sistema operacional:',
	'optin-survey-question-res' => 'Qual é a resolução do seu monitor?',
	'optin-survey-question-changes' => 'Que mudanças você gostaria de ver futuramente no Beta? (por favor selecione todas as opções pertinentes)',
	'optin-survey-answer-changes-nav' => 'Tornar a navegação geral do site mais fácil de usar.',
	'optin-survey-answer-changes-edittools' => 'Criar mais e melhores ferramentas de edição.',
	'optin-survey-answer-changes-upload' => 'Melhorar o envio de conteúdos e multimídias.',
	'optin-survey-answer-changes-richtext' => 'Desenvolver ferramentas de edição semelhantes a editores avançados de texto.',
	'optin-survey-answer-changes-lookfeel' => 'Renovar o aspecto geral do site.',
	'optin-survey-answer-changes-predictability' => 'Melhorar a previsibilidade dos resultados.',
	'optin-survey-answer-changes-custom' => 'Permitir uma maior personalização para o usuário.',
	'optin-survey-answer-changes-other' => 'Outros:',
	'optin-improvements2' => '== O que foi melhorado? ==
[[File:UsabilityToolbar.png|left|link=|Imagem da barra melhorada das ferramentas de edição]]
[[File:UsabilityDialogs.png|left|link=|Imagem dos diálogos de geração de novos conteúdos]]
;Barra de ferramentas melhorada
A barra de ferramentas melhorada facilita a edição de artigos.
Ícones novos e melhorados tornam mais clara a função de cada ferramenta.
Secções expandidas reduzem a confusão ao mesmo tempo que mantêm as ferramentas pouco usadas à distância de um clique.
;Diálogo
Ao criar links ou tabelas, clicar o ícone de ferramentas abre agora um diálogo que auxilia a sua criação preenchendo um formulário simples.
;E mais
Foram introduzidas várias melhorias, como a navegação melhorada, os separadores em cascata, a pesquisa melhorada e outras.
== Como pode envolver-se ==
Para saber mais sobre a Iniciativa de Usabilidade, visite a nossa [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative página].
<div style="clear:both"></div>',
);

/** Quechua (Runa Simi)
 * @author AlimanRuna
 */
$messages['qu'] = array(
	'optin-title-optedout' => 'Beta nisqata llanchiy',
	'optin-title-optedin' => 'Beta nisqamanta lluqsiy',
	'optin-title-justoptedin' => 'Beta nisqaman allin hamusqayki',
	'optin-title-justoptedout' => 'Añañayki Beta nisqata llanchisqaykimanta',
	'optin-title-feedback' => 'Beta nisqamanta rimanakuy',
	'optin-needlogin' => 'Beta nisqata llanchiykachaspaykiqa, [$1 yaykunaykim] tiyan.',
	'optin-success-feedback' => 'Añañayki qillqamuwasqaykimanta!',
	'optin-accept-short' => 'Rurasunchik!',
	'optin-accept-long' => 'Beta nisqata llanchipuy',
	'optin-accept-long-anon' => 'Yaykuspa Beta nisqata llanchipuy',
	'optin-deny-short' => 'Manam munanichu',
	'optin-deny-long' => "ñawpa p'anqaman kutimuy",
	'optin-submit-out' => 'Beta nisqamanta lluqsiy',
	'optin-submit-feedback' => 'Beta nisqamanta rimanakuy',
	'optin-survey-yes' => 'Arí',
	'optin-survey-no' => 'Ama kachunchu',
);

/** Romanian (Română)
 * @author AdiJapan
 * @author Cin
 * @author Firilacroco
 * @author KlaudiuMihaila
 * @author Minisarm
 * @author Stelistcristi
 */
$messages['ro'] = array(
	'optin' => 'Participați la Inițiativa de Utilizabilitate',
	'optin-desc' => 'Permiteți utilizatorilor să încerce versiunea Beta, ultimele îmbunătățiri de utilizabilitate ale Usability Initiative',
	'optin-title-optedout' => 'Încercați Beta',
	'optin-title-optedin' => 'Părăsire Beta',
	'optin-title-justoptedin' => 'Bine ați venit la Beta',
	'optin-title-justoptedout' => 'Vă mulțumim că ați încercat Beta',
	'optin-title-feedback' => 'Păreri în legătură cu Beta',
	'optin-needlogin' => 'Trebuie să vă [$1 autentificați] pentru a încerca Beta.',
	'optin-intro' => 'Inițiativa de Utilizabilitate a Wikipediei a făcut eforturi pentru a ridica nivelul de utilizabilitate al proiectului.
Doriți să încercați versiunea noastră Beta?',
	'optin-feedback-intro' => 'Vă mulțumim că testați versiunea Beta.
Dorim să aflăm ce părere aveți despre noua noastră interfață. Vă rugăm așadar să completați chestionarul opțional de mai jos.',
	'optin-feedback-back' => 'Dacă nu doriți să participați la sondaj vă puteți întoarce la pagina $1.',
	'optin-success-in' => 'Încercați acum versiunea Beta.
Puteți renunța în orice moment, făcând clic pe legătura „{{int:optin-leave}}” din partea de sus a paginii.',
	'optin-leave-cancel' => 'Dacă doriți să continuați folosirea versiunii Beta, puteți să reveniți la $1.',
	'optin-success-out' => 'Ați părăsit cu succes versiunea Beta.
Puteți să încercați iar în orice moment apăsând clic pe legătura „{{int:optin-try}}” pe partea de sus a paginii.',
	'optin-success-feedback' => 'Vă mulțumim pentru părere!',
	'optin-accept-short' => 'Să vedem!',
	'optin-accept-long' => 'Încercați Beta',
	'optin-accept-long-anon' => 'Autentificați-vă și încercați Beta',
	'optin-deny-short' => 'Nu, mulțumesc',
	'optin-deny-long' => 'Reveniți la pagina anterioară',
	'optin-submit-out' => 'Părăsire Beta',
	'optin-submit-feedback' => 'Trimiteți răspunsurile',
	'optin-survey-yes' => 'Da',
	'optin-survey-no' => 'Nu',
	'optin-survey-intro' => 'Mulțumim că folosiți versiunea Beta.
Ne-am dori să știm ce părere aveți despre noua noastră interfață, așadar am aprecia dacă ați completa sondajul opțional de mai jos înainte de a da clic pe „[[#leave|{{int:optin-submit-out}}]]”.',
	'optin-survey-question-likedislike' => 'Ce v-a plăcut la versiunea Beta ? Ce nu v-a plăcut la versiunea Beta ?',
	'optin-survey-question-whyoptout' => 'De ce părăsiți versiunea Beta ? (Vă rugăm să bifați toate motivele pe care le considerați aplicabile.)',
	'optin-survey-answer-whyoptout-hard' => 'A fost prea greu de folosit.',
	'optin-survey-answer-whyoptout-didntwork' => 'Nu a funcționat cum trebuie.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Nu s-a comportat predictibil.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Nu mi-a plăcut cum arată.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Nu mi-au plăcut filele noi și aspectul.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Nu mi-a plăcut noua bară de unelte.',
	'optin-survey-answer-whyoptout-other' => 'Alt motiv:',
	'optin-survey-question-explain' => 'Vă rugăm explicați sau elaborați răspunsul dumneavoastră mai jos:',
	'optin-survey-question-techfail' => 'Ați găsit vreo defecțiune tehnică în timp ce ați utilizat versiunea Beta ?',
	'optin-survey-question-techfail-ifyes' => 'Dacă da, vă rugăm elaborați sau explicați:',
	'optin-survey-question-usedtoolbar' => 'Ați folosit noua bară de unelte expansibilă pentru a modifica o pagină?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Dacă da, ce ți-a plăcut la ea ? Ce nu ți-a plăcut la ea ?',
	'optin-survey-question-usedtoolbar-ifno' => 'Dacă nu, de ce nu ? Veți dori în viitor ? Vă rugăm explicați.',
	'optin-survey-question-different' => 'Ce am fi putut face diferit pentru a nu părăsi versiunea Beta ?',
	'optin-survey-question-feedback' => 'Vă rugăm înștiințați-ne despre orice alte comentarii, preocupări sau gânduri aveți pentru noi — toate mesajele sunt utile:',
	'optin-survey-question-browser' => 'Ce navigator folosiți?',
	'optin-survey-answer-browser-other' => 'Alt browser:',
	'optin-survey-question-os' => 'Ce sistem de operare folosiți?',
	'optin-survey-answer-os-other' => 'Alt sistem de operare:',
	'optin-survey-question-res' => 'Care este rezoluția ecranului dumneavoastră?',
	'optin-survey-question-changes' => 'Ce modificări v-ar plăcea să vedeți în versiunea Beta în viitor? Alegeți toate răspunsurile potrivite.',
	'optin-survey-answer-changes-nav' => 'Navigarea sitului să fie mai ușoară.',
	'optin-survey-answer-changes-edittools' => 'Uneltele de editare să fie mai bune și mai numeroase.',
	'optin-survey-answer-changes-upload' => 'Trimiterea de conținut și de fișiere să fie îmbunătățită.',
	'optin-survey-answer-changes-richtext' => 'Unelte de editare ca în editoarele „rich-text”.',
	'optin-survey-answer-changes-lookfeel' => 'Aspectul și funcționalitatea sitului să fie actualizate.',
	'optin-survey-answer-changes-predictability' => 'Uneltele să aibă un comportament mai previzibil.',
	'optin-survey-answer-changes-custom' => 'Un grad mai mare de personalizare.',
	'optin-survey-answer-changes-other' => 'Altele:',
	'optin-improvements2' => '== Ce s-a îmbunătățit? ==
[[File:UsabilityToolbar.png|left|link=|Captură de ecran cu noua bară de unelte de editare]]
[[File:UsabilityDialogs.png|left|link=|Captură de ecran cu noile casete de dialog pentru generare de conținut]]
;Bară de unelte îmbunătățită
Noua bară de unelte facilitează editarea articolelor.
Miniaturile noi și îmbunătățite înfățișează mai clar rolul fiecărei unealte.
Secțiunile expandabile aerisesc pagina, dar țin uneltele folosite rar la distanță de un clic.
;Casete de dialog
Cînd creați legături sau tabele, casetele de dialog se deschid printr-un clic pe miniatură și vă ajută să îndepliniți sarcina prin simpla completare a unui formular.
;Și altele
Am operat o mulțime de alte îmbunătățiri, precum navigarea optimizată, file în cascadă, căutare eficientă și multe altele.
==Cum vă puteți implica==
Pentru a afla detalii despre Inițiativa de Utilizabilitate vă rugăm să vizitați [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative această pagină].
<div style="clear:both"></div>',
);

/** Tarandíne (Tarandíne)
 * @author Joetaras
 */
$messages['roa-tara'] = array(
	'optin' => "Pigghie parte a l'iniziative sus a l'usabbilità",
	'optin-desc' => "Permette a l'utinde de pruvà Beta, l'iniziative de Usabbeletà cu l'urteme sviluppe sus a l'usabbeletà.",
	'optin-title-optedout' => 'Pruève Beta',
	'optin-title-optedin' => 'Lasse Beta',
	'optin-title-justoptedin' => "Bovegne jndr'à Beta",
	'optin-title-justoptedout' => 'Grazie ca ste pruève Beta',
	'optin-title-feedback' => 'Segnale a Beta',
	'optin-needlogin' => 'Tu è abbesogne de [$1 trasè] pe pruvà Beta.',
	'optin-intro' => "L'Iniziative de l'Usabbeletà de Uicchipèdie ha state 'na fatìa tosta toste fine a mò pe migliorà l'usabbeletà de Uicchipèdie.<br />
Ue ccù pruève Beta?",
	'optin-feedback-intro' => "Grazie ca stè pruève Beta.<br />
Nuje vulè ccù sapesseme ce pinze de le inderfacce nuève, accussì a nuje ne piacesse ca tu anghisse le riepileghe d'aqquà sotte.",
	'optin-feedback-back' => 'Ce tu non ge vuè ccù ienghie le riepileghe, tu puè turnà a $1.',
	'optin-success-in' => 'Tu stè pruève Beta.
Tu puè lassà sta prove quanne vuè tu cazzanne \'u collegamende "{{int:optin-leave}}" ca stè sus \'a pàgene.',
	'optin-leave-cancel' => 'Ce vuè condinue a ausà Beta, tu puè turnà a $1.',
	'optin-success-out' => "Tu è spicciate de pruvà Beta.<br />
Tu puè pruvà 'n'otra vote quanne vuè Beta cazzanne 'u collegamende \"{{int:optin-try}}\" sus 'a pàgene.",
	'optin-success-feedback' => 'Grazie pa segnalazione!',
	'optin-accept-short' => 'Facimele!',
	'optin-accept-long' => 'Spicce de pruvà Beta',
	'optin-accept-long-anon' => 'Trase e pruève Beta',
	'optin-deny-short' => 'No grazie',
	'optin-deny-long' => "Tuèrne 'a pàgena precedende",
	'optin-submit-out' => 'Lasse Beta',
	'optin-submit-feedback' => "Danne 'na segnalazione",
	'optin-survey-yes' => 'Sìne',
	'optin-survey-no' => 'None',
	'optin-survey-intro' => 'Grazie ca è pruvate Beta.<br />
Nuje vulèsseme canoscere ce ne pinze de le inderfacce nuève, a nuje ne piacesse ca tu anghisse le riepileghe opzionale aqquà sotte apprime de cazzà "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => "Ce t'à piaciute de Beta? Ce no t'à piaciute de Beta?",
	'optin-survey-question-whyoptout' => 'Purcè ste lasse Beta? (Pe piacere scacchie tutte quidde ca vuè.)',
	'optin-survey-answer-whyoptout-hard' => 'Ere assaije comblecate da ausà.',
	'optin-survey-answer-whyoptout-didntwork' => 'Non ge funzione bbuène.',
	'optin-survey-answer-whyoptout-notpredictable' => "Non ge funzione precise, vè 'a cazze de cane.",
	'optin-survey-answer-whyoptout-didntlike-look' => 'Non ge me piace accumme se vede.',
	'optin-survey-answer-whyoptout-didntlike-layout' => "Non ge me piacene le schede nuève e 'a disposizione.",
	'optin-survey-answer-whyoptout-didntlike-toolbar' => "Non ge me piace 'a barra de le struminde nova.",
	'optin-survey-answer-whyoptout-other' => 'Otre mutive:',
	'optin-survey-question-explain' => "Pe piacere spieghe de cchiù pe 'na resposte sus:",
	'optin-survey-question-techfail' => "E' avute quacche esperienze sus a le fallimende tecnice quanne ste ausave Beta?",
	'optin-survey-question-techfail-ifyes' => 'Ce jè sine, pe piacere spieghe de cchiù:',
	'optin-survey-question-usedtoolbar' => "E' ausate 'a nova barre de le struminde espandibbele pe cangià 'na pàgene?",
	'optin-survey-question-usedtoolbar-ifyes' => "Ce sine, ce t'à piaciute sus a ijedde? E ce cose nò t'à piaciute?",
	'optin-survey-question-usedtoolbar-ifno' => "Ce none, purcé none? A vuè jndr'à 'u future? Pe piacere dinne quacchecose de cchiù.",
	'optin-survey-question-different' => 'Ce cose putime fà de differende pe farte condinuà a ausà Beta?',
	'optin-survey-question-feedback' => 'Pe piacere fanne sembre canoscere otre commende, congette e penziere ca tìne - tutte le parere sò utile:',
	'optin-survey-question-browser' => 'Ce browser ause?',
	'optin-survey-answer-browser-other' => 'Otre browser:',
	'optin-survey-question-os' => 'Ce sisteme operative ause?',
	'optin-survey-answer-os-other' => 'Otre sisteme operative:',
	'optin-survey-question-res' => "Ce risoluzione tène 'u scherme tue?",
	'optin-survey-question-changes' => "Ce cangiaminde vuè ccù vide in Beta jndr'à 'u future (pe piacere scacchie quidde ca vuè)?",
	'optin-survey-answer-changes-nav' => "Face cchiù facile 'a navigazione comblete d'u site.",
	'optin-survey-answer-changes-edittools' => 'Ingremende e migliore le struminde pe le cangiaminde.',
	'optin-survey-answer-changes-upload' => 'Ingremende le condenute e le carecaminde de le media.',
	'optin-survey-answer-changes-richtext' => "Sviluppe de le struminde pe le cangiaminde ca s'assomigliane a le editure ''rich-text''.",
	'optin-survey-answer-changes-lookfeel' => "Aggiorne 'u ''vide e sinde'' d'u site.",
	'optin-survey-answer-changes-predictability' => "Ingremende 'a prevedibbeletà de le performance.",
	'optin-survey-answer-changes-custom' => "Permette maggiore personalizzazziune pe l'utinde.",
	'optin-survey-answer-changes-other' => 'Otre:',
	'optin-improvements2' => "== Ce amme migliorate? ==
[[File:UsabilityToolbar.png|left|link=|Schermate d'a barre de le struminde avanzate]]
[[File:UsabilityDialogs.png|left|link=|Schermate d'a generazione de le nuève dialoghe de condenute]]
;Barre de le struminde avanzate<br />
'A barre de le struminde avanzate face cchiù facile 'u cangiamende de le vosce. Icone nuève e migliorate ca chiariscene megghie ce azione ogne strumende face.<br />
Seziune spannute reducene 'u ruète mendre cazze a uecchie le struminde pe 'nu click.<br />
;Dialoghe
Quanne ccreje collegaminde o tabbelle, cazzanne l'icone d'u strumende mò iapre 'na sckatele de dialofhe ca t'aiute a ccrejà collegaminde e tabbelle anghienne 'nu module semblice semblice.
;De cchiù
Nuje amme indrodotte 'nu numere de otre miglioraminde cumme 'na navigazione avanzate, tabbelle a cascate, ricerche migliorate e tanda otre cose.
== Cumme se ponne avè ste cose ==
Pe acchià otre 'mbormaziune sus a l'Iniziative de l'Usabbiletà, pe piacere vè vide 'a nostre [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative homepage].
<div style=\"clear:both\"></div>",
);

/** Russian (Русский)
 * @author AlexSm
 * @author Ferrer
 * @author Kaganer
 * @author Lockal
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'optin' => 'Участие в Инициативе юзабилити',
	'optin-desc' => 'Позволяет участникам опробовать бета-версию интерфейса («Beta») — последнее улучшение, сделанное в рамках Инициативы юзабилити.',
	'optin-title-optedout' => 'Опробование бета-версии интерфейса',
	'optin-title-optedin' => 'Покинуть Beta',
	'optin-title-justoptedin' => 'Добро пожаловать в Beta',
	'optin-title-justoptedout' => 'Благодарим вас за участие в опробовании бета-версии интерфейса',
	'optin-title-feedback' => 'Отправить отзыв',
	'optin-needlogin' => 'Вам необходимо [$1 представиться], чтобы опробовать бета-версию.',
	'optin-intro' => 'В рамках Инициативы юзабилити была проведена работа по улучшению удобства использования Википедии.
Вы хотите опробовать бета-версию?',
	'optin-feedback-intro' => 'Благодарим вас за согласие опробовать бета-версию интерфейса.
Нам хотелось бы знать, что вы думаете о нашем новом интерфейсе, поэтому мы были бы признательны, если бы вы приняли участие в приведённом ниже опросе.',
	'optin-feedback-back' => 'Если вы не хотите участвовать в опросе, вы можете вернуться к $1.',
	'optin-success-in' => 'Теперь вы принимаете участие в опробовании бета-версии интерфейса.
Вы можете отключиться от программы в любое время, нажав  на ссылку «{{int:optin-leave}}» в верхней части экрана.',
	'optin-leave-cancel' => 'Если вы хотите продолжить использовать бета-версию, вы можете вернуться к $1.',
	'optin-success-out' => 'Вы успешно вышли из режима опробования бета-версии интерфейса.
Вы можете ещё раз попробовать эту версию, нажав ссылку «{{int:optin-try}}» в правом верхнем углу экрана.',
	'optin-success-feedback' => 'Спасибо за отзыв!',
	'optin-accept-short' => 'Поехали!',
	'optin-accept-long' => 'Опробовать бета-версию',
	'optin-accept-long-anon' => 'Авторизоваться и опробовать бета-версию интерфейса',
	'optin-deny-short' => 'Нет, спасибо',
	'optin-deny-long' => 'Вернуться на предыдущую страницу',
	'optin-submit-out' => 'Покинуть бета-версию',
	'optin-submit-feedback' => 'Отправить отзыв',
	'optin-survey-yes' => 'Да',
	'optin-survey-no' => 'Нет',
	'optin-survey-intro' => 'Благодарим вас за участие в опробовании бета-версии интерфейса.
Нам бы хотелось знать, что вы думаете о новом интерфейсе, поэтому, перед тем, как вы нажмёте «[[#leave|{{int:optin-submit-out}}]]», мы были бы вам признательны за участие в приведённом ниже опросе.',
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
	'optin-survey-question-usedtoolbar-ifno' => 'Если нет, то почему? Собираетесь ли в будущем? Пожалуйста, объясните.',
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
	'optin-survey-question-changes' => 'Какие изменения вы хотели бы увидеть в будущих бета-версиях (выберите все подходящие пункты)?',
	'optin-survey-answer-changes-nav' => 'Сделать навигацию по всему сайту более простой в использовании.',
	'optin-survey-answer-changes-edittools' => 'Расширить и усовершенствовать инструменты редактирования.',
	'optin-survey-answer-changes-upload' => 'Усовершенствовать загрузку содержания и медиа.',
	'optin-survey-answer-changes-richtext' => 'Разработать инструменты редактирования, аналогичные текстовым редакторам.',
	'optin-survey-answer-changes-lookfeel' => 'Обновить «внешний вид» сайта.',
	'optin-survey-answer-changes-predictability' => 'Улучшить предсказуемость работы.',
	'optin-survey-answer-changes-custom' => 'Позволить производить больше индивидуальных настроек.',
	'optin-survey-answer-changes-other' => 'Иное:',
	'optin-improvements2' => '== Что было улучшено? ==
[[File:UsabilityToolbar.png|left|link=|Снимок расширенной панели редактирования]]
[[File:UsabilityDialogs.png|left|link=|Снимок новых диалогов создания]]
; Расширенная панель инструментов
Расширенная панель инструментов облегчает редактирование статей. Новые и улучшенные значки более точно показывают соответствие инструментов и выполняемых ими действий. Раскрываемые разделы уменьшают беспорядок, сохраняя при этом редко используемые инструменты на расстоянии одного щелчка мыши.
; Окна диалога
Теперь при создании ссылок или таблиц, можно нажать на соответствующий значок, и открывшееся диалоговое окно поможет создать ссылку или таблицу, посредством заполнения простой формы.
; А также
Мы сделали ряд других усовершенствований, среди них улучшение навигации, каскадные вкладки, улучшенный поиск и многое другое.
== Как включиться ==
Чтобы узнать больше об Инициативе юзабилити, пожалуйста, посетите [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative нашу страницу].
<div style="clear:both"></div>',
);

/** Rusyn (русиньскый язык)
 * @author Gazeb
 */
$messages['rue'] = array(
	'optin-survey-answer-changes-other' => 'Інше:',
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'optin' => 'Табыгас Ситиһиитигэр кыттыы',
	'optin-desc' => 'Бета-барылы туһанары хааччыйар, «Beta» — Табыгаһы Ситиһии иһинэн оҥоһуллубут бүтэһик тупсарыы.',
	'optin-title-optedout' => 'Beta-ны тургутуу',
	'optin-title-optedin' => 'Beta-ттан тахсыы',
	'optin-title-justoptedin' => 'Beta-ҕа нөрүөн нөргүй!',
	'optin-title-justoptedout' => 'Beta-ны тургутан көрбүккэр махтал',
	'optin-title-feedback' => 'Сыанабылы ыытарга',
	'optin-needlogin' => 'Beta тургутарга [$1 ааккын билиһиннэриэххин] наада.',
	'optin-intro' => 'Табыгаһы Ситиһии иһинэн оҥоһуллубут үлэ Бикипиэдьийэни туһаныы чэпчэки уонна табыгастаах буоларыгар туһаайыллыбыта.
Beta-ны боруобалаары гынаҕын дуо?',
	'optin-feedback-intro' => 'Beta-ны туттубуккар махтанабыт.
Саҥа интерфейс туһунан туох санаалааххын билиэхпитин баҕарабыт, онон аллара баар ыйытыыларга хоруйдууруҥ буоллар наһаа үөрүө этибит.',
	'optin-feedback-back' => 'Ыйытыкка кыттыаххын баҕарбат буоллаххына, төннүөххүн сөп $1.',
	'optin-success-in' => 'Beta-ны тургутууга кыттан эрэҕин.
Мантан хаһан баҕарар тахсыаххын сөп: экран үөһээ өттүгэр "{{int:optin-leave}}" диэни баттаан.',
	'optin-leave-cancel' => 'Beta-ны туттан бүттэххинэ төннүөххүн сөп $1.',
	'optin-success-out' => 'Beta-ттан таҕыстыҥ.
Өссө боруобалыаххын сөп: экран хаҥас үөһээ өттүгэр "{{int:optin-try}}" диэни баттаан.',
	'optin-success-feedback' => 'Сыанаҥ иһин махтал!',
	'optin-accept-short' => 'Бардыбыт!',
	'optin-accept-long' => 'Beta-ны тургутарга',
	'optin-accept-long-anon' => 'Ааккын билиһиннэрэн баран Beta-ны тургутарга',
	'optin-deny-short' => 'Суох, махтанабын',
	'optin-deny-long' => 'Бу иннинээҕи сирэйгэ төннөргө',
	'optin-submit-out' => 'Beta-ттан тахсарга',
	'optin-submit-feedback' => 'Сыанабылы ыытыы',
	'optin-survey-yes' => 'Сөп',
	'optin-survey-no' => 'Суох',
	'optin-survey-intro' => 'Beta-ны боруобалаабыккар махтал.
Саҥа интерфейс туһунан туох санаалааххын билиэхпитин баҕарабыт, ол иһин "[[#leave|{{int:optin-submit-out}}]]" баттыаҥ иннинэ манна баар ыйытыыларга хоруйдууруҥ буоллар наһаа үөрүө этибит.',
	'optin-survey-question-likedislike' => 'Beta тугун сөбүлээтиҥ? Тугун сөбүлээбэтиҥ?',
	'optin-survey-question-whyoptout' => 'Тоҕо Beta-ттан тахсарга сананныҥ? (сөп түбэһэр хоруйдары тал эрэ)',
	'optin-survey-answer-whyoptout-hard' => 'Туттарга наһаа ыарахан.',
	'optin-survey-answer-whyoptout-didntwork' => 'Сатаан үлэлээбэт.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Хайдах үлэлиирэ оччо "дьэҥкирэ" суох.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Тас көрүҥүн оччо сөбүлээбэтим.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Саҥа кыбытыктары (вкладки) уонна макеты оччо сөбүлээбэтим.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Эрэдээксийэлиир саҥа панелы сөбүлээбэтим.',
	'optin-survey-answer-whyoptout-other' => 'Атын төрүөт:',
	'optin-survey-question-explain' => 'Бука диэн хоруйгун быһаар эбэтэр чуолкайдаа:',
	'optin-survey-question-techfail' => 'Beta-ны туттарга ханнык эмит техника кэмэлдьитин көрүстүҥ дуо?',
	'optin-survey-question-techfail-ifyes' => 'Оннук буоллаҕына, бука диэн, чуолкайдаа эбэтэр быһаар:',
	'optin-survey-question-usedtoolbar' => 'Сирэйдэри эрэдээксийэлииргэр уҥа тэнийэр панелы туһанныҥ дуо?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Оннук буллаҕына, тугун сөбүлээтиҥ? Тугун астымматыҥ?',
	'optin-survey-question-usedtoolbar-ifno' => 'Суох буоллаҕына, тоҕо? Кэлин туттуоҥ дуу? Бука диэн, быһаар эрэ.',
	'optin-survey-question-different' => 'Beta-ны кэлин да туһанарыҥ туһугар тугу оҥоруохпутун сөбүй?',
	'optin-survey-question-feedback' => 'Бука диэн, бэйэҥ санааҕын эт эрэ:',
	'optin-survey-question-browser' => 'Ханнык браузерынан туһанаҕын?',
	'optin-survey-answer-browser-other' => 'Атын браузер:',
	'optin-survey-question-os' => 'Ханнык операционнай систиэмэни туһанаҕын?',
	'optin-survey-answer-os-other' => 'Атын ОС:',
	'optin-survey-question-res' => 'Эн мониторуҥ разрешениета төһөнүй?',
	'optin-survey-question-changes' => 'Beta-ҕа кэлин ханнык уларытыылары киллэриэххэ сөбүй? (барытын талыаххын сөп)',
	'optin-survey-answer-changes-nav' => 'Саайт навигациятын туттарга арыый судургу гыныахха.',
	'optin-survey-answer-changes-edittools' => 'Эрэдээксийэлиир үнүстүрүмүөннэри элбэтэргэ уонна тупсарарга.',
	'optin-survey-answer-changes-upload' => 'Иһинээҕитин уонна медиатын хачайдыыры тупсарарга.',
	'optin-survey-answer-changes-richtext' => 'Тиэкис эрэдээктэрдэригэр баар үнүстурүмүөннэргэ майгынныыр үнүстүрүмүөннэри эбэргэ.',
	'optin-survey-answer-changes-lookfeel' => 'Саайт тас көстүүтүн уларытарга.',
	'optin-survey-answer-changes-predictability' => 'Үлэлиирэ "дьэҥкирин" ситиһэргэ.',
	'optin-survey-answer-changes-custom' => 'Тус туруоруулары өссө киэҥник уларытары көҥүллүүргэ.',
	'optin-survey-answer-changes-other' => 'Атын:',
);

/** Sardinian (Sardu)
 * @author Andria
 */
$messages['sc'] = array(
	'optin-title-optedout' => 'Proa Beta',
	'optin-title-optedin' => 'Lassa Beta',
	'optin-title-justoptedin' => 'Bene bènnidu a sa Beta',
	'optin-title-justoptedout' => 'Gràtzias pro àer proadu sa nostra Beta',
	'optin-title-feedback' => 'Feedback pro sa Beta',
	'optin-needlogin' => 'Depes èsser [$1 identificadu (log in)] pro proare sa Beta.',
	'optin-accept-short' => 'Proa·dda!',
	'optin-accept-long' => 'Proare sa Beta',
	'optin-accept-long-anon' => 'Identìfica·ti e proa sa Beta',
	'optin-deny-short' => 'No gràtzias',
	'optin-deny-long' => 'Torra a sa pàgina de prima',
	'optin-submit-out' => 'Lassa sa Beta',
	'optin-submit-feedback' => 'Lassa unu feedback',
	'optin-survey-yes' => 'Eja',
	'optin-survey-question-likedislike' => 'Ite as agradadu de sa Beta? Ite no as agradadu de sa Beta?',
	'optin-survey-question-whyoptout' => 'Pro ite ses lassende sa Beta? (Seletziona totu is respostas pertinentes)',
	'optin-survey-answer-whyoptout-hard' => "Fiat tropu difìtzile a dd'usare.",
	'optin-survey-answer-whyoptout-other' => 'Àteru motivu:',
	'optin-survey-answer-browser-other' => 'Àteru browser:',
	'optin-survey-answer-changes-other' => 'Àteru:',
);

/** Sicilian (Sicilianu)
 * @author Gmelfi
 * @author Melos
 */
$messages['scn'] = array(
	'optin' => 'Adisioni ô priggettu usabbilità',
	'optin-accept-short' => 'Prova!',
	'optin-accept-long' => 'Prova a Beta',
	'optin-accept-long-anon' => 'Accedi e prova a Beta',
);

/** Sinhala (සිංහල)
 * @author Calcey
 * @author Jiro Ono
 * @author චතුනි අලහප්පෙරුම
 */
$messages['si'] = array(
	'optin-title-optedout' => 'බීටා අනුවාදය අත්හදා බලන්න',
	'optin-title-optedin' => 'බීටා අනුවාදය වෙතින් ඉවත් වන්න',
	'optin-title-justoptedin' => 'බීටා අනුවාදය ට පිලිගනිමු',
	'optin-title-justoptedout' => 'බීටා අනුවාදය අත්හදා බැලීම ගැන තුති',
	'optin-title-feedback' => 'බීටා ප්‍රතිචාරය',
	'optin-feedback-intro' => 'බීටා අනුවාදය අත්හදා බැලීම පිළිබඳ ස්තූතියි. 
අපගේ නව අතුරු-මුහුණත පිළිබඳ ඔබ සිතන දේ දැනගැනීමට අප සතුටු වන අතර, පහත වෛකල්පික සමීක්ෂණය පුරවා දමන්නේ නම් කෘතඥ වෙමු.',
	'optin-feedback-back' => 'ඔබට සමීක්ෂණය පිරවීමට අවශ්‍ය නොවේ නම්,ඔබට නැවත $1 වෙත යා හැක.',
	'optin-success-in' => 'ඔබ දැන් බීටා අත්හදා බලමින් සිටියි.
ඔබට පිටුවේ ඉහළ ඇති "{{int:optin-leave}}" සබැඳිය ක්ලික් කිරීමෙන් ඕනෑම මොහොතක ආපසු හැරී යා හැක.',
	'optin-success-feedback' => 'ඔබේ ප්‍රතිචාරයට ස්තුතියි!',
	'optin-accept-short' => 'උත්සාහ කර බලමු!',
	'optin-accept-long' => 'බීටා අනුවාදය අත්හදා බලන්න',
	'optin-accept-long-anon' => 'ප්‍රවිෂ්ට වී බීටා අනුවාදය අත්හදා බලන්න',
	'optin-deny-short' => 'ස්තුතියි. බැහැ',
	'optin-deny-long' => 'පෙර පිටුවට නැවත යන්න',
	'optin-submit-out' => 'බීටා අනුවාදයෙන් ඉවත් වන්න',
	'optin-submit-feedback' => 'ප්‍රතිපෝෂණය ලබාදෙන්න',
	'optin-survey-yes' => 'ඔව්',
	'optin-survey-no' => 'නැත',
	'optin-survey-intro' => 'බීටා අනුවාදය අත්හදා බැලීම පිළිබඳ ස්තූතියි.
අපගේ නව අතුරු-මුහුණත පිළිබඳ ඔබ සිතන දේ දැනගැනීමට අප සතුටු වන අතර, "[[#leave|{{int:optin-submit-out}}]]" ක්ලික් කිරීමට පෙර,  පහත වෛකල්පික සමීක්ෂණය පුරවා දමන්නේ නම් කෘතඥ වෙමු.',
	'optin-survey-question-likedislike' => 'බීටා අනුවාදය පිළිබඳ ඔබ සිත්ගත් දෑ කුමක්ද? බීටා අනුවාදය පිළිබඳ ඔබ සිත්නොගත් දෑ කුමක්ද?',
	'optin-survey-question-whyoptout' => 'ඔබ බීටා අනුවාදයෙන් ඉවත් වන්නේ ඇයි? (ගැලපෙන සියල්ල තෝරාගැනුමට කාරුණික වන්න.)',
	'optin-survey-answer-whyoptout-hard' => 'එය භාවිතය ඉතා දුෂ්කර විය.',
	'optin-survey-answer-whyoptout-didntwork' => 'එය හරි හැටි ක්‍රියාත්මක නොවීය.',
	'optin-survey-answer-whyoptout-notpredictable' => 'එය බලාපොරොත්තු වූ ලෙසින් ක්‍රියා නොකලේය.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'එහි පෙනුම මා සිත් ගත්තේ නැත.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'නව ටැබ හා වින්‍යාස සැලැස්ම මා සිත් ගත්තේ නැත.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'නව මෙවලම්-පටිය මා සිත් ගත්තේ නැත.',
	'optin-survey-answer-whyoptout-other' => 'වෙනත් හේතුවක්:',
	'optin-survey-question-explain' => 'කරුණාකර ඔබගේ ඉහත පිළිතුර විස්තර හෝ පැහැදිලි කිරීම කරන්න:',
	'optin-survey-question-techfail' => 'ඔබ බීටා භාවිතා කිරීමේදී යම් තාක්ෂණික ඇනහිටීමක් අත්දුටුවාද ?',
	'optin-survey-question-techfail-ifyes' => 'ඔව් නම්,කරුණාකර පැහැදිලි හෝ විස්තර කරන්න:',
	'optin-survey-question-usedtoolbar' => 'පිටුවක් සංස්කරණය කිරීමට ඔබ විස්තීරණය කළ හැකි නව මෙවලම් තීරුව භාවිතා කර තිබේද ?',
	'optin-survey-question-usedtoolbar-ifyes' => 'ඔව් නම්,ඔබ එය ගැන කැමැත්තක් දක්වන්නේ කුමකට ද? ඔබ එය ගැන අකමැත්තක් දක්වන්නේ කුමකට ද?',
	'optin-survey-question-usedtoolbar-ifno' => 'නැත නම්,නොවන්නේ ඇයි ? අනාගතයේදී හෝ ? කරුණාකර පැහැදිලි කරන්න.',
	'optin-survey-question-feedback' => 'කරුණාකර ඔබට අප සඳහා ඇති තවත් විචාර,සැලකිලි හෝ සිතුවිලි අප වෙත දන්වන්න - සියලුම ප්‍රතිපෝෂණ ප්‍රයෝජනවත් වේ:',
	'optin-survey-question-browser' => 'ඔබ භාවිතා කරන්නේ කුමන බ්‍රවුසරයද?',
	'optin-survey-answer-browser-other' => 'අනෙක් බ්‍රවුසරය:',
	'optin-survey-question-os' => 'ඔබ භාවිතා කරනු ලබන්නේ කුමන මෙහෙයුම් පද්ධතිය ද?',
	'optin-survey-answer-os-other' => 'අනෙක් මෙහෙයුම් පද්ධතිය:',
	'optin-survey-question-res' => 'ඔබගේ තිරයේ විභේදනය කුමක් ද?',
	'optin-survey-question-changes' => 'අනාගතයේදී බීටාහි ඔබ දකින්නට කැමති කුමන වෙනස්කම් ( කරුණාකර ඉල්ලුම් කරන සියල්ල තෝරන්න) ද ?',
	'optin-survey-answer-changes-nav' => 'අඩවියේ සමස්ත සංචාලනය භාවිතා කිරීමට වඩා පහසු කරවන්න.',
	'optin-survey-answer-changes-edittools' => 'සංස්කරණ මෙවලම් වැඩිකිරීම හා උසස් කිරීම සිදු කරන්න.',
	'optin-survey-answer-changes-upload' => 'අන්තර්ගතය හා මාධ්‍ය උඩුගත කිරීම් දියුණු කරන්න.',
	'optin-survey-answer-changes-richtext' => 'සංස්කරණ මෙවලම් සරු-පෙළ සංස්කාරකයන්ට සමානවන ලෙස සංවර්ධනය කරන්න.',
	'optin-survey-answer-changes-lookfeel' => 'මෙම අඩවියේ "බලන්න හා විඳින්න" යාවත්කාලීන කරන්න.',
	'optin-survey-answer-changes-other' => 'වෙනත්:',
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
	'optin-title-feedback' => 'Vaše komentáre',
	'optin-needlogin' => 'Ak sa chcete skúsiť Beta, musíte sa najskôr [$1 prihlásiť].',
	'optin-intro' => 'Iniciatíva použiteľnosti projektu Wikipédia pracuje s cieľom vylepšiť použiteľnosť Wikipédie. Chcete skúsiť Beta?',
	'optin-feedback-intro' => 'Ďakujeme, že ste skúsili Beta.
Radi by sme vedeli, čo si o našom novom rozhraní myslíte, preto by sme ocenili, keby ste vyplnili nasledovný nepovinný dotazník.',
	'optin-feedback-back' => 'Ak nechcete dotazník vyplniť, môžete sa vrátiť na $1.',
	'optin-success-in' => 'Teraz skúšate Beta.
Kedykoľvek môžete skúšanie opustiť kliknutím na „{{int:optin-leave}}“ v pravom hornom rohu.',
	'optin-leave-cancel' => 'Ak si želáte pokračovať v používaní Beta, môžete sa vrátiť späť na $1',
	'optin-success-out' => 'Úspešne ste opustili Beta.
Kedykoľvek ho môžete znova vyskúšať kliknutím na „{{int:optin-try}}“ v pravom hornom rohu.',
	'optin-success-feedback' => 'Ďakujeme za vaše komentáre!',
	'optin-accept-short' => 'Urobme to!',
	'optin-accept-long' => 'Vyskúšať Beta',
	'optin-accept-long-anon' => 'Prihlásiť sa a skúsiť Beta',
	'optin-deny-short' => 'Nie, ďakujem',
	'optin-deny-long' => 'späť na predošlú stránku',
	'optin-submit-out' => 'Opustiť Beta',
	'optin-submit-feedback' => 'Vaše komentáre',
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
	'optin-survey-question-usedtoolbar-ifno' => 'Ak nie, prečo? Skúsite v budúcnosti? Vysvetlite, prosím.',
	'optin-survey-question-different' => 'Čo sme mohli spraviť inak aby ste neprestali používať testovacie rozhranie Beta?',
	'optin-survey-question-feedback' => 'Ak máte ďalšie komentáre, napíšte ich prosím:',
	'optin-survey-question-browser' => 'Ktorý prehliadač používate?',
	'optin-survey-answer-browser-other' => 'Iný prehliadač:',
	'optin-survey-question-os' => 'Ktorý operačný systém používate?',
	'optin-survey-answer-os-other' => 'Iný operačný systém:',
	'optin-survey-question-res' => 'Aké je rozlíšenie vašej obrazovky?',
	'optin-survey-question-changes' => 'Aké zmeny by ste radi videli v Beta v budúcnosti (prosím, vyberte všetky, ktoré chcete)?',
	'optin-survey-answer-changes-nav' => 'Celkovo uľahčiť navigáciu po webovej lokalite.',
	'optin-survey-answer-changes-edittools' => 'Zväčšiť počet a vylepšiť nástroje na úpravy.',
	'optin-survey-answer-changes-upload' => 'Zlepšiť obsah a nahrávanie multimediálnych súborov.',
	'optin-survey-answer-changes-richtext' => 'Vyvinúť nástroje na úpravu podobné editorom obohateného textu.',
	'optin-survey-answer-changes-lookfeel' => 'Aktualizovať „vzhľad a správanie“ webovej lokality.',
	'optin-survey-answer-changes-predictability' => 'Zlepšiť predpovedateľnosť výkonnosti.',
	'optin-survey-answer-changes-custom' => 'Umožniť používateľom väčšiu prispôsobiteľnosť',
	'optin-survey-answer-changes-other' => 'Iné:',
);

/** Slovenian (Slovenščina)
 * @author Dbc334
 * @author Smihael
 */
$messages['sl'] = array(
	'optin' => 'Vtičnik Usability Initiative (Pobuda za uporabnost)',
	'optin-desc' => 'Omogoča uporabnikom, da preizkusijo betarazličico spletišča, ki vsebuje najnovejše izboljšave k uporabnosti.',
	'optin-title-optedout' => 'Preizkusite betarazličico',
	'optin-title-optedin' => 'Zapusti betarazličico',
	'optin-title-justoptedin' => 'Dobrodošli v betarazličici',
	'optin-title-justoptedout' => 'Hvala, ker ste preizkusili našo betarazličico',
	'optin-title-feedback' => 'Povratne informacije betarazličice',
	'optin-needlogin' => 'Za preizkus betarazličice je potrebna [$1 prijava].',
	'optin-intro' => 'Wikipedijina Pobuda za uporabnost si prizadeva za izboljšanje uporabnosti Wikipedije. Želite preizkusite našo betarazličico?',
	'optin-feedback-intro' => 'Zahvaljujemo se vam za preizkušanje betarazličice.
Ker bi radi vedeli, kaj mislite o našem novem vmesniku, bi cenili, če izpolnite spodnjo (neobvezno) anketo.',
	'optin-feedback-back' => 'Če ne želite izpolniti ankete, se lahko vrnete v $1.',
	'optin-success-in' => 'Sedaj preizkušate betarazličico.
Kadar koli se lahko vrnete s klikom na povezavo »{{int:optin-leave}}« na vrhu strani.',
	'optin-leave-cancel' => 'Če želite nadaljevati z uporabo betarazličice, se lahko vrnete na $1.',
	'optin-success-out' => 'Uspešno ste zapustili betarazličico. 
Lahko jo poskusite znova kadar koli s klikom na povezavo »{{int:optin-try}}« na vrhu strani.',
	'optin-success-feedback' => 'Zahvaljujemo se vam za povratne informacije!',
	'optin-accept-short' => 'Seveda!',
	'optin-accept-long' => 'Preizkusite betarazličico',
	'optin-accept-long-anon' => 'Prijavite se in preizkusite betarazličico',
	'optin-deny-short' => 'Ne, hvala',
	'optin-deny-long' => 'Vrni se na prejšnjo stran',
	'optin-submit-out' => 'Zapusti betarazličico',
	'optin-submit-feedback' => 'Pošlji povratne informacije',
	'optin-survey-yes' => 'Da',
	'optin-survey-no' => 'Ne',
	'optin-survey-intro' => 'Zahvaljujemo se vam za preizkušanje betarazličice.

Želimo vedeti, kaj mislite o našem novem vmesniku, zato bi cenili, če boste izpolnili neobvezno anketo preden kliknete »[[#leave|{{int:optin-submit-out}}]]«.',
	'optin-survey-question-likedislike' => 'Kaj vam je bilo pri betarazličici všeč? Česa niste marali?',
	'optin-survey-question-whyoptout' => 'Zakaj zapuščate betarazličico? (Označite lahko več možnosti.)',
	'optin-survey-answer-whyoptout-hard' => 'Prezapletena uporaba.',
	'optin-survey-answer-whyoptout-didntwork' => 'Nepravilno delovanje.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Nepredvidljivo delovanje.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Izgled mi ni všeč.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Postavitev in novi zavihki mi niso všeč.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Nova orodna vrstica mi ni všeč.',
	'optin-survey-answer-whyoptout-other' => 'Drug razlog:',
	'optin-survey-question-explain' => 'Prosimo, pojasnite vaš zgornji odgovor:',
	'optin-survey-question-techfail' => 'Ste med uporabo betarazličice naleteli na kakšno tehnično napako?',
	'optin-survey-question-techfail-ifyes' => 'Če je odgovor pritrdilen, ga prosimo pojasnite:',
	'optin-survey-question-usedtoolbar' => 'Ali ste uporabili novo razširljivo orodno vrstico za urejanje strani?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Če je odgovor pritrdilen, kaj vam je bilo všeč? Česa niste marali?',
	'optin-survey-question-usedtoolbar-ifno' => 'Če je odgovor nikalen, zakaj ne? Jo boste preizkusili v prihodnje? Prosimo, pojasnite.',
	'optin-survey-question-different' => 'Kaj bi lahko naredili drugače, da bi nadaljevali z uporabo betarazličice?',
	'optin-survey-question-feedback' => 'V primeru kakršnih koli drugih pripomb, idej ali misli o betarazličice, vas prosimo, da nam jih zaupate – vse povratne informacije so koristne:',
	'optin-survey-question-browser' => 'Kateri brskalnik uporabljate?',
	'optin-survey-answer-browser-other' => 'Drug brskalnik:',
	'optin-survey-question-os' => 'Kateri operacijski sistem uporabljate?',
	'optin-survey-answer-os-other' => 'Drug operacijski sistem:',
	'optin-survey-question-res' => 'Kakšna je ločljivost vašega zaslona?',
	'optin-survey-question-changes' => 'Kakšne spremembe želite videti v betarazličici v prihodnosti (izberete lahko več možnosti)?',
	'optin-survey-answer-changes-nav' => 'Poenostavite navigacijo.',
	'optin-survey-answer-changes-edittools' => 'Izboljšajte orodja za urejanje strani.',
	'optin-survey-answer-changes-upload' => 'Izboljšanje dodajanje vsebine in nalaganje večpredstavnostnih datotek.',
	'optin-survey-answer-changes-richtext' => 'Razvijte orodja za urejanje strani tako, da bodo bolj podobna vizualnim urejevalnikom besedila.',
	'optin-survey-answer-changes-lookfeel' => 'Posodobite izgled strani.',
	'optin-survey-answer-changes-predictability' => 'Izboljšajte predvidljivost delovanja.',
	'optin-survey-answer-changes-custom' => 'Omogočite uporabniku večjo prilagoditev.',
	'optin-survey-answer-changes-other' => 'Drugo:',
	'optin-improvements2' => '== Kaj je bilo izboljšano? ==
[[File:UsabilityToolbar.png|left|link=|Zaslonski posnetek izboljšane urejevalne vrstice]]
[[File:UsabilityDialogs.png|left|link=|Zaslonski posnetek novih pogovornih oken za ustvarjanje vsebine]]
;Izboljšana orodna vrstica
Z izboljšano orodno vrstico je urejanje člankov lažje.
Nove in izpopolnjene ikone jasneje kažejo, katero dejanje orodje izvede.
Razširljivi razdelki zmanjšajo nered, medtem ko ohranjajo vsa redko uporabljana orodja samo klik proč.
;Pogovorna okna
Ko ustvarjate povezave ali preglednice, klik na ikono orodja sedaj odpre pogovorno okno, ki vam pomaga ustvariti povezave in preglednice z izpolnitvijo preprostega obrazca.
;In več
Uvedli smo število drugih izboljšav, kot je izpopolnjena navigacija, padajoči zavihki, izboljšano iskanje in več.
== Kako sodelujem ==
Da izveste več o Pobudi za uporabnost, si, prosimo, oglejte našo [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative domačo stran].
<div style="clear:both"></div>',
);

/** Serbian Cyrillic ekavian (Српски (ћирилица))
 * @author Михајло Анђелковић
 */
$messages['sr-ec'] = array(
	'optin' => 'Прикључивање Иницијативи за употребљивост',
	'optin-desc' => 'Омогућава корисницима да се опробају са бета-верзијом тј. последњим побољшањима Иницијативе за употребљивост.',
	'optin-title-optedout' => 'Пробај бета-верзију',
	'optin-title-optedin' => 'Напусти бета-верзију',
	'optin-title-justoptedin' => 'Добро дошли на бета-верзију',
	'optin-title-justoptedout' => 'Хвала Вам за испробавање бета-верзије',
	'optin-title-feedback' => 'Повратна информација у вези са бета-верзијом',
	'optin-needlogin' => 'Морате се [$1 улоговати] да бисте пробали бета-верзију.',
	'optin-intro' => 'Википедијина Иницијатива за употребљивост је вредно радила да би побољшала употребљивост Википедије.
Да ли бисте желели да пробате нашу бета-верзију?',
	'optin-feedback-intro' => 'Хвала вам за испробавање бета-верзије.
Волели бисмо да знамо шта мислите о нашем новом интерфејсу, тако да би нам било драго када бисте испунили необавезну анкету испод.',
	'optin-feedback-back' => 'Ако не желите да учествујете у анкети, можете се вратити на $1.',
	'optin-success-in' => 'Сада испробавате бета-верзију.
У свако доба се можете вратити на претходну верзују, кликом на „{{int:optin-leave}}“, у левом горњем делу стране.',
	'optin-leave-cancel' => 'Ако бисте желели да продужите са коришћењем бета-верзије, можете се вратити на $1.',
	'optin-success-out' => 'Успешно сте напустили бета-верзију.
Можете опет прећи на њу у свако доба, кликом на „{{int:optin-try}}“ везу у горњем десном углу стране.',
	'optin-success-feedback' => 'Хвала Вам за повратну информацију!',
	'optin-accept-short' => 'Хајде да то урадимо!',
	'optin-accept-long' => 'Пробајте бета-верзију',
	'optin-accept-long-anon' => 'Улогујте се и пробајте бета-верзију',
	'optin-deny-short' => 'Не, хвала',
	'optin-deny-long' => 'Повратак на претходну страну',
	'optin-submit-out' => 'Напусти бета-верзију',
	'optin-submit-feedback' => 'Пошаљи коментар',
	'optin-survey-yes' => 'Да',
	'optin-survey-no' => 'Не',
	'optin-survey-intro' => 'Хвала Вам за испробавање бета-верзије.
Волели бисмо да знамо шта мислите о нашем новом интерфејсу, тако да би нам значило да испуните необавезну анкету испод пре него што кликнете „[[#leave|{{int:optin-submit-out}}]]“.',
	'optin-survey-question-likedislike' => 'Шта Вам се свидело на бета-верзији? А шта вам се није свидело?',
	'optin-survey-question-whyoptout' => 'Зашто напуштате бета-верзију? (Молимо Вас да означите све што је на то утицало.)',
	'optin-survey-answer-whyoptout-hard' => 'Био је претежак за коришћење.',
	'optin-survey-answer-whyoptout-didntwork' => 'Није радио како треба.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Није радио предвидиво.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Није ми се свидело како изгледа.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Нису ми се свидели нови табови и изглед.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Није ми се свидео нови тулбар.',
	'optin-survey-answer-whyoptout-other' => 'Други разлог:',
	'optin-survey-question-explain' => 'Молимо Вас да објасните или проширите Ваш одговор изнад:',
	'optin-survey-question-techfail' => 'Да ли сте се сусрели са техничким грешкама док сте користили бета-верзију?',
	'optin-survey-question-techfail-ifyes' => 'Ако да, молимо Вас да нам о томе кажете нешто опширније:',
	'optin-survey-question-usedtoolbar' => 'Да ли сте користили нов раширив тулбар за измену стране?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Ако јесте, шта Вам се свидело у вези са њим? А шта Вам се није свидело?',
	'optin-survey-question-usedtoolbar-ifno' => 'Ако не, зашто нисте? Да ли ћете? Молимо Вас за објашњење.',
	'optin-survey-question-different' => 'Шта бисмо могли да урадимо, да бисте наставили да користите бета-верзију?',
	'optin-survey-question-feedback' => 'Слободно нам пишите, ако имате било каквих других коментара, прмедби или размишљања — свака повратна информација је добродошла!',
	'optin-survey-question-browser' => 'Који браузер користите?',
	'optin-survey-answer-browser-other' => 'Други браузер:',
	'optin-survey-question-os' => 'Који оперативни систем користите?',
	'optin-survey-answer-os-other' => 'Други оперативни систем:',
	'optin-survey-question-res' => 'Која је резолуција Вашег екрана?',
	'optin-survey-question-changes' => 'Које измене бисте волели да видите на бета-верзији у убудћности (молимо Вас да изаберете све што одговара)?',
	'optin-survey-answer-changes-nav' => 'Поједностављење целокупне навигације сајтом.',
	'optin-survey-answer-changes-edittools' => 'Проширење и побољшавање алата за измену чланка.',
	'optin-survey-answer-changes-upload' => 'Побољшање интерфејса за слање садржаја и медија фајлова.',
	'optin-survey-answer-changes-richtext' => 'Развој алата за измену чланака у смеру едитора за богати текст.',
	'optin-survey-answer-changes-lookfeel' => 'Измена изгледа сајта.',
	'optin-survey-answer-changes-predictability' => 'Побољшање предвидивости перформанси.',
	'optin-survey-answer-changes-custom' => 'Омогућавање веће прилагодљивости кориснику.',
	'optin-survey-answer-changes-other' => 'Друго:',
);

/** Serbian Latin ekavian (Srpski (latinica))
 * @author Liangent
 * @author Michaello
 */
$messages['sr-el'] = array(
	'optin' => 'Priključivanje Inicijativi za upotrebljivost',
	'optin-desc' => 'Omogućava korisnicima da se oprobaju sa beta-verzijom tj. poslednjim poboljšanjima Inicijative za upotrebljivost.',
	'optin-title-optedout' => 'Probaj beta-verziju',
	'optin-title-optedin' => 'Napusti beta-verziju',
	'optin-title-justoptedin' => 'Dobro došli na beta-verziju',
	'optin-title-justoptedout' => 'Hvala Vam za isprobavanje beta-verzije',
	'optin-title-feedback' => 'Povratna informacija u vezi sa beta-verzijom',
	'optin-needlogin' => 'Morate se [$1 ulogovati] da biste probali beta-verziju.',
	'optin-intro' => 'Vikipedijina Inicijativa za upotrebljivost je vredno radila da bi poboljšala upotrebljivost Vikipedije.
Da li biste želeli da probate našu beta-verziju?',
	'optin-feedback-intro' => 'Hvala vam za isprobavanje beta-verzije.
Voleli bismo da znamo šta mislite o našem novom interfejsu, tako da bi nam bilo drago kada biste ispunili neobaveznu anketu ispod.',
	'optin-feedback-back' => 'Ako ne želite da učestvujete u anketi, možete se vratiti na $1.',
	'optin-success-in' => 'Sada isprobavate beta-verziju.
U svako doba se možete vratiti na prethodnu verzuju, klikom na „{{int:optin-leave}}“, u levom gornjem delu strane.',
	'optin-leave-cancel' => 'Ako biste želeli da produžite sa korišćenjem beta-verzije, možete se vratiti na $1.',
	'optin-success-out' => 'Uspešno ste napustili beta-verziju.
Možete opet preći na nju u svako doba, klikom na „{{int:optin-try}}“ vezu u gornjem desnom uglu strane.',
	'optin-success-feedback' => 'Hvala Vam za povratnu informaciju!',
	'optin-accept-short' => 'Hajde da to uradimo!',
	'optin-accept-long' => 'Probajte beta-verziju',
	'optin-accept-long-anon' => 'Ulogujte se i probajte beta-verziju',
	'optin-deny-short' => 'Ne, hvala',
	'optin-deny-long' => 'Povratak na prethodnu stranu',
	'optin-submit-out' => 'Napusti beta-verziju',
	'optin-submit-feedback' => 'Pošalji komentar',
	'optin-survey-yes' => 'Da',
	'optin-survey-no' => 'Ne',
	'optin-survey-intro' => 'Hvala Vam za isprobavanje beta-verzije.
Voleli bismo da znamo šta mislite o našem novom interfejsu, tako da bi nam značilo da ispunite neobaveznu anketu ispod pre nego što kliknete „[[#leave|{{int:optin-submit-out}}]]“.',
	'optin-survey-question-likedislike' => 'Šta Vam se svidelo na beta-verziji? A šta vam se nije svidelo?',
	'optin-survey-question-whyoptout' => 'Zašto napuštate beta-verziju? (Molimo Vas da označite sve što je na to uticalo.)',
	'optin-survey-answer-whyoptout-hard' => 'Bio je pretežak za korišćenje.',
	'optin-survey-answer-whyoptout-didntwork' => 'Nije radio kako treba.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Nije radio predvidivo.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Nije mi se svidelo kako izgleda.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Nisu mi se svideli novi tabovi i izgled.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Nije mi se svideo novi tulbar.',
	'optin-survey-answer-whyoptout-other' => 'Drugi razlog:',
	'optin-survey-question-explain' => 'Molimo Vas da objasnite ili proširite Vaš odgovor iznad:',
	'optin-survey-question-techfail' => 'Da li ste se susreli sa tehničkim greškama dok ste koristili beta-verziju?',
	'optin-survey-question-techfail-ifyes' => 'Ako da, molimo Vas da nam o tome kažete nešto opširnije:',
	'optin-survey-question-usedtoolbar' => 'Da li ste koristili nov raširiv tulbar za izmenu strane?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Ako jeste, šta Vam se svidelo u vezi sa njim? A šta Vam se nije svidelo?',
	'optin-survey-question-usedtoolbar-ifno' => 'Ako ne, zašto niste? Da li ćete? Molimo Vas za objašnjenje.',
	'optin-survey-question-different' => 'Šta bismo mogli da uradimo, da biste nastavili da koristite beta-verziju?',
	'optin-survey-question-feedback' => 'Slobodno nam pišite, ako imate bilo kakvih drugih komentara, prmedbi ili razmišljanja — svaka povratna informacija je dobrodošla!',
	'optin-survey-question-browser' => 'Koji brauzer koristite?',
	'optin-survey-answer-browser-other' => 'Drugi brauzer:',
	'optin-survey-question-os' => 'Koji operativni sistem koristite?',
	'optin-survey-answer-os-other' => 'Drugi operativni sistem:',
	'optin-survey-question-res' => 'Koja je rezolucija Vašeg ekrana?',
	'optin-survey-question-changes' => 'Koje izmene biste voleli da vidite na beta-verziji u ubudćnosti (molimo Vas da izaberete sve što odgovara)?',
	'optin-survey-answer-changes-nav' => 'Pojednostavljenje celokupne navigacije sajtom.',
	'optin-survey-answer-changes-edittools' => 'Proširenje i poboljšavanje alata za izmenu članka.',
	'optin-survey-answer-changes-upload' => 'Poboljšanje interfejsa za slanje sadržaja i medija fajlova.',
	'optin-survey-answer-changes-richtext' => 'Razvoj alata za izmenu članaka u smeru editora za bogati tekst.',
	'optin-survey-answer-changes-lookfeel' => 'Izmena izgleda sajta.',
	'optin-survey-answer-changes-predictability' => 'Poboljšanje predvidivosti performansi.',
	'optin-survey-answer-changes-custom' => 'Omogućavanje veće prilagodljivosti korisniku.',
	'optin-survey-answer-changes-other' => 'Drugo:',
);

/** Sundanese (Basa Sunda)
 * @author Kandar
 */
$messages['su'] = array(
	'optin-title-optedout' => 'Cobaan Béta',
	'optin-title-optedin' => 'Tinggalkeun Béta',
	'optin-title-justoptedin' => 'Wilujeng sumping di Béta',
	'optin-title-justoptedout' => 'Ngahaturkeun nuhun tos nyobian Béta',
	'optin-needlogin' => 'Anjeun kudu [$1 asup log] pikeun nyobaan Béta.',
	'optin-accept-short' => 'Hayu!',
	'optin-accept-long' => 'Cobaan Béta',
	'optin-accept-long-anon' => 'Asup log jeung cobaan Béta',
	'optin-deny-short' => 'Moal, nuhun',
	'optin-deny-long' => 'Balik ka kaca saméméhna',
	'optin-submit-out' => 'Tinggalkeun Béta',
	'optin-survey-yes' => 'Enya',
	'optin-survey-no' => 'Teu',
	'optin-survey-question-whyoptout' => 'Naha bet ninggalkeun Béta? (mangga pilih sadaya nu cocog.)',
	'optin-survey-answer-whyoptout-hard' => 'Hésé makéna.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Teu resep tulbar anyarna.',
	'optin-survey-answer-whyoptout-other' => 'Alesan séjén:',
	'optin-survey-question-os' => 'Sistem operasi naon nu dipaké ku anjeun?',
	'optin-survey-answer-os-other' => 'Sistem operasi lianna:',
	'optin-survey-question-res' => 'Résolusi layar nu dipaké?',
	'optin-survey-answer-changes-other' => 'Lianna:',
);

/** Swedish (Svenska)
 * @author Ainali
 * @author Boivie
 * @author Hannibal
 * @author Micke
 * @author Rotsee
 * @author Skalman
 */
$messages['sv'] = array(
	'optin' => "Delta i ''Wikipedias initiativ för användarvänlighet''",
	'optin-desc' => "Tillåt användare att prova betaversionen, ''Wikipedias initiativ för användarvänlighet''s senaste förbättring.",
	'optin-title-optedout' => 'Prova betaversionen',
	'optin-title-optedin' => 'Lämna betatestet',
	'optin-title-justoptedin' => 'Välkommen till betaversionen',
	'optin-title-justoptedout' => 'Tack för att du deltar i vårt betatest',
	'optin-title-feedback' => 'Feedback för betaversionen',
	'optin-needlogin' => 'Du måste [$1 logga in] för att delta i betatestet.',
	'optin-intro' => "''Wikipedias initiativ för användarvänlighet'' har arbetat hårt för att förbättra Wikipedias användarvänlighet.
Skulle du vilja prova vår betaversion?",
	'optin-feedback-intro' => "Tack för att du deltar i betatestet. Vi vill veta vad du tycker om vårt nya gränssnitt, så vi skulle uppskatta om du fyllde i enkäten nedan.

'''Notera att eventuella kommentarer bör skrivas på engelska, för att hela testteamet skall kunna läsa dem!'''",
	'optin-feedback-back' => 'Om du inte vill fylla i enkäten kan du återgå till $1.',
	'optin-success-in' => 'Du använder nu betaversionen.
Du kan välja att gå tillbaka när som helst genom att klicka på "{{int:optin-leave}}"-länken högst upp på sidan.',
	'optin-leave-cancel' => 'Om du vill fortsätta använda betaversionen kan du återgå till $1.',
	'optin-success-out' => 'Du har nu lämnat betatestet.
Du kan prova betaversionen igen när som helst genom att klicka på "{{int:optin-try}}"-länken överst till höger på sidan.',
	'optin-success-feedback' => 'Tack för din feedback!',
	'optin-accept-short' => 'Gör det!',
	'optin-accept-long' => 'Prova betaversionen',
	'optin-accept-long-anon' => 'Logga in och prova betaversionen',
	'optin-deny-short' => 'Nej tack',
	'optin-deny-long' => 'Återgå till föregående sida',
	'optin-submit-out' => 'Lämna betatest',
	'optin-submit-feedback' => 'Ge feedback',
	'optin-survey-yes' => 'Ja',
	'optin-survey-no' => 'Nej',
	'optin-survey-intro' => "Tack för att du provar betaversionen. Vi vill veta vad du tycker om vårt nya gränssnitt, så vi skulle uppskatta om du svarar på enkäten nedan innan du klickar på \"[[#leave|{{int:optin-submit-out}}]]\".


'''Notera att eventuella kommentarer bör skrivas på engelska, för att hela testteamet skall kunna läsa dem!'''",
	'optin-survey-question-likedislike' => 'Vad tyckte du var bra i betaversionen? Vad tyckte du inte om?',
	'optin-survey-question-whyoptout' => 'Varför lämnar du betaversionen? (Välj allt som passar in.)',
	'optin-survey-answer-whyoptout-hard' => 'Den var för svårt att använda.',
	'optin-survey-answer-whyoptout-didntwork' => 'Den fungerade inte ordentligt.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Saker fungerade inte som jag väntade mig.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Jag tyckte inte om utseendet.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Jag tyckte inte om de nya flikarna och layouten.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Jag tyckte inte om det nya verktygsfältet.',
	'optin-survey-answer-whyoptout-other' => "Annan orsak ''(endast engelska)'':",
	'optin-survey-question-explain' => 'Förklara eller utveckla gärna ditt svar ovan:',
	'optin-survey-question-techfail' => 'Upplevde du några tekniska fel när du använde betaversionen?',
	'optin-survey-question-techfail-ifyes' => "Om ja, utveckla eller förklara ''(endast engelska)'':",
	'optin-survey-question-usedtoolbar' => 'Har du använt det nya expanderbara verktygsfältet för att redigera en sida?',
	'optin-survey-question-usedtoolbar-ifyes' => "Om ja, vad gjorde att du tyckte om det? Vad ogillade du med det ''(endast engelska)''?",
	'optin-survey-question-usedtoolbar-ifno' => "Om nej, varför inte? Kommer du göra det längre fram? Förklara ''(endast engelska)''.",
	'optin-survey-question-different' => "Vad kunde vi gjort annorlunda för att du skulle ha fortsatt att använda betaversionen ''(endast engelska)''?",
	'optin-survey-question-feedback' => "Låt oss gärna veta om du har några andra synpunkter, frågor eller funderingar - alla kommentarer är nyttiga ''(endast engelska)'':",
	'optin-survey-question-browser' => 'Vilken webbläsare använder du?',
	'optin-survey-answer-browser-other' => 'Andra webbläsare:',
	'optin-survey-question-os' => 'Vilket operativsystem använder du?',
	'optin-survey-answer-os-other' => 'Andra operativsystem:',
	'optin-survey-question-res' => 'Vilken upplösning har du på din skärm?',
	'optin-survey-question-changes' => 'Vilka förändringar skulle du vilja se i betaversionen i framtiden (välj alla som passar)?',
	'optin-survey-answer-changes-nav' => 'Gör den övergripande navigeringen för webbplatsen lättare att använda.',
	'optin-survey-answer-changes-edittools' => 'Utöka och förbättra redigeringsverktygen.',
	'optin-survey-answer-changes-upload' => 'Förbättra innehållet och filuppladdningen.',
	'optin-survey-answer-changes-richtext' => 'Utveckla redigeringsverktyg som liknar ordbehandlingsprogram.',
	'optin-survey-answer-changes-lookfeel' => 'Uppdatera webbplatsens utseende.',
	'optin-survey-answer-changes-predictability' => 'Förbättra förutsägbarheten för hur webbplatsen beter sig.',
	'optin-survey-answer-changes-custom' => 'Tillåt mer skräddarsydda inställningar.',
	'optin-survey-answer-changes-other' => "Övrigt ''(endast engelska)'':",
	'optin-improvements2' => '== Vad har förbättrats? == 
[[File:UsabilityToolbar.png|vänster|link=|Skärmdump av det förbättrade redigeringsverktygsfältet]] 
[[File:UsabilityDialogs.png|vänster|link=|Skärmdump av de nya dialogrutorna för att skapa innehåll]]
;Förbättrat verktygsfält
Det förbättrade verktygsfältet gör det enklare att redigera artiklar. 
Nya och förbättrade ikoner gör det tydligare vad de olika verktygen gör. 
Utfällbara avsnitt minskar röran, samtidigt som verktyg som används sällan finns ett enda klick bort.
;Dialogruta
Numera när du skapar länkar eller tabeller och klickar på verktygsikonen, så öppnas en dialogruta som hjälper dig att skapa länkar och tabeller genom att du bara kan fylla i ett formulär.
;Dessutom
Vi har infört en rad andra förbättringar såsom enklare navigering, utfällbara flikar, förbättrad sökning, med mera. 
== Så här kan du hjälpa till == 
Om du vill veta mer om användbarhetsinitiativet, besök vår [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative webbplats].
<div style="clear:both"></div>',
);

/** Swahili (Kiswahili)
 * @author Lloffiwr
 * @author Muddyb Blast Producer
 */
$messages['sw'] = array(
	'optin-title-optedout' => 'Jaribu Beta',
	'optin-title-optedin' => 'Ondoka Beta',
	'optin-title-justoptedin' => 'Karibu Beta',
	'optin-title-justoptedout' => 'Asante kwa kujaribu kutumia bidhaa pepe ya Beta',
	'optin-intro' => 'Mpango wa Matumizi ya Wikipedia umekuwa ukifanya kazi kwa juhudi kuboresha matumizi ya Wikipedia.
Ungependa kujaribu Beta yetu?',
	'optin-feedback-intro' => 'Ahsante kwa kujaribu Beta.
Tungependa kujua unafikiriaje kuhusu kusano zetu mpya, hivyo basi tungeshukuru zaidi iwapo utajaza utafiti wa hiari hapo chini.',
	'optin-feedback-back' => 'Ikiwa hutaki kujaza maoni yako, unaweza kurudi kwa $1.',
	'optin-success-in' => 'Sasa unaijaribu Beta.
Unaweza kuamua kurudi wakati wowote kwa kubonyeza kiungo cha "{{int:optin-leave|ondoka}}" juu ya ukurasa.',
	'optin-leave-cancel' => 'Ikiwa utapenda kuendelea kutumia Beta, basi unaweza kurudi kwa $1.',
	'optin-success-out' => 'Umefaulu kuondoka Beta.
Unaweza kujaribu tena wakati wowote kwa kubonyeza kiungo cha "{{int:optin-try|jaribu}}" juu kabisa ya ukurasa.',
	'optin-success-feedback' => 'Ahsante kwa maoni yako!',
	'optin-accept-short' => 'Hebu tufanye',
	'optin-accept-long' => 'Ujaribu kutumia Beta',
	'optin-accept-long-anon' => 'Ingia na ujaribu Beta',
	'optin-deny-short' => 'Hapana, ahsante',
	'optin-deny-long' => 'Rudi kwenye ukurasa wa awali',
	'optin-submit-out' => 'Ondoka Beta',
	'optin-submit-feedback' => 'Toa maoni',
	'optin-survey-yes' => 'Ndiyo',
	'optin-survey-no' => 'Siyo',
	'optin-survey-intro' => 'Ahsante kwa kujaribu Beta.
Tungependa kujua unafikiriaje kuhusu kusano zetu mpya, hivyo basi tungeshukuru iwapo utajaza utafiti wa hiari hapo chini kabla hujabonyeza "[[#leave|{{int:optin-submit-out|ondoka}}]]".',
	'optin-survey-question-likedislike' => 'Kipi umekipenda katika Beta? Kipi hujakipenda katika Beta?',
	'optin-survey-question-whyoptout' => 'Kwanini unaondoka Beta? (Tafadhali jaza vifungu hivyo vyote.)',
	'optin-survey-answer-whyoptout-hard' => 'Ilikuwa vigumu sana kutumia.',
	'optin-survey-answer-whyoptout-didntwork' => 'Haikufanya kazi vizuri.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Sikuipenda jinsi inavyoonekana.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Sikupenda tabo mpya na mpangilio wake.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Sijapenda mwambaa zana mpya.',
	'optin-survey-answer-whyoptout-other' => 'Sababu nyingine:',
	'optin-survey-question-explain' => 'Tafadhali elezea au fafanua juu ya majibu yako hapo juu:',
	'optin-survey-question-techfail' => 'Umepata tatizo lolote la kiufundi wakati unatumia Beta?',
	'optin-survey-question-techfail-ifyes' => 'Ikiwa ndiyo, tafadhali fafanua au elezea:',
);

/** Telugu (తెలుగు)
 * @author Kiranmayee
 * @author Ravichandra
 * @author Veeven
 */
$messages['te'] = array(
	'optin' => 'మెరుగైన వాడుక సౌలభ్యాన్ని ఎంచుకున్నారు',
	'optin-title-optedout' => 'బీటాని ప్రయత్నించండి',
	'optin-title-optedin' => 'బీటాను విడిచి వెళ్ళు',
	'optin-title-justoptedin' => 'బీటాకు స్వాగతం',
	'optin-title-justoptedout' => 'బీటాను ప్రయత్నిస్తున్నందుకు ధన్యవాదాలు',
	'optin-title-feedback' => 'బీటా పై అభిప్రాయాలు',
	'optin-needlogin' => 'బీటాను ప్రయత్నించడం కోసం [$1 log in] కావాలి',
	'optin-intro' => 'వికీపీడీయా యొక్క ఉపయోగశీలతను మెరుగుపరిచేందుకు కొన్ని ప్రయత్నాలు జరుగుతున్నాయి. దానికి సంబంధించిన బీటాను ప్రయత్నిస్తారా?',
	'optin-feedback-intro' => 'బీటాను ప్రయత్నించినందుకు ధన్యవాదాలు.
ఈ కొత్త ఇంటర్‌ఫేస్ గురించి మీ అభిప్రాయాలేమిటో మేము తెలుసుకొనదలచాము. క్రిందనున్న అభిప్రాయ సేకరణలో పాలుపంచుకోండి.',
	'optin-feedback-back' => 'మీకు ఈ అభిప్రాయ సేకరణలో పాలుపంచుకోవడం ఇష్టం లేకపోతే $1 కి తిరిగి వెళ్ళవచ్చు.',
	'optin-success-in' => 'మీరు ఇప్పుడు బీటాను ప్రయత్నిస్తున్నారు.
ఒకవేళ మీరు వెనక్కి వెళ్ళదలిస్తే తెర పైభాగాన ఉన్న "{{int:optin-leave}}" లింకును నొక్కండి.',
	'optin-leave-cancel' => 'బీటాను వాడడం మీకు సమ్మతం అయితే $1 కు తిరిగి వెళ్ళండి.',
	'optin-success-out' => 'బీటా నుంచి మీరు విజయవంతంగా వదిలివచ్చారు.
మీకు మళ్ళీ ఎప్పుడైనా ప్రయత్నించాలనిపిస్తే తెర పైభాగం కుడివైపున ఉన్న "{{int:optin-try}}" లింకును నొక్కండి.',
	'optin-success-feedback' => 'మీ అభిప్రాయానికి ధన్యవాదాలు!',
	'optin-accept-short' => 'అలాగే చేద్దాం!',
	'optin-accept-long' => 'బీటాని ప్రయత్నించండి',
	'optin-accept-long-anon' => 'ప్రవేశించి బీటాను ప్రయత్నించండి',
	'optin-deny-short' => 'వద్దు, ధన్యవాదాలు',
	'optin-deny-long' => 'మునుపటి పేజీకి వెళ్ళండి.',
	'optin-submit-out' => 'బీటాను విడిచి వెళ్ళు',
	'optin-submit-feedback' => 'అభిప్రాయాన్ని తెలుపండి',
	'optin-survey-yes' => 'అవును',
	'optin-survey-no' => 'కాదు',
	'optin-survey-intro' => 'బీటాను ప్రయత్నిస్తున్నందుకు ధన్యవాదాలు.
కొత్త ఇంటర్‌ఫేస్ పై మీ అభిప్రాయాలను తెలియగోరుచున్నాము. "[[#leave|{{int:optin-submit-out}}]]" పై నొక్కడానికి ముందు క్రింద ఇచ్చిన అభిప్రాయ సేకరణలో పాలుపంచుకుంటే అభినందిస్తాము.',
	'optin-survey-question-likedislike' => 'ఈ బీటాలో మీకు నచ్చినవేమిటి? నచ్చనివేమిటి?',
	'optin-survey-question-whyoptout' => 'మీరు బీటాని వదిలి వెళ్తున్నారా? (దయచేసి వర్తించేవన్నీ ఎంచుకోండి.)',
	'optin-survey-answer-whyoptout-hard' => 'వాడడానికి చాలా కష్టంగా ఉంది.',
	'optin-survey-answer-whyoptout-didntwork' => 'ఇది సరిగ్గా పనిచేయడం లేదు.',
	'optin-survey-answer-whyoptout-notpredictable' => 'ఇది అనుకున్నట్లుగా పనిచేయడం లేదు',
	'optin-survey-answer-whyoptout-didntlike-look' => 'దీని రూపు నాకు నచ్చలేదు',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'కొత్త ట్యాబులు, లే అవుట్ నాకు నచ్చలేదు',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'కొత్త టూల్‌బార్ నాకు నచ్చలేదు',
	'optin-survey-answer-whyoptout-other' => 'ఇతర కారణం:',
	'optin-survey-question-explain' => 'పైన ఇచ్చిన సమాధానాన్ని మరికొంత వివరించండి:',
	'optin-survey-question-techfail' => 'బీటాను ప్రయత్నించేటపుడు మీకేమైనా సాంకేతిక లోపాలు ఎదురయ్యాయా?',
	'optin-survey-question-techfail-ifyes' => 'నిజమే అయితే దానిని గురించి వివరణ రాయండి:',
	'optin-survey-question-usedtoolbar' => 'కొత్తగా వచ్చిన తెరవగలిగే టూల్‌బార్ ను పేజీ ను మార్చేందుకు వాడారా?',
	'optin-survey-question-usedtoolbar-ifyes' => 'వాడిఉంటే దానిలో మీకు నచ్చిన/నచ్చని అంశాలేమిటి?',
	'optin-survey-question-usedtoolbar-ifno' => 'వాడకపోయి ఉంటే ఎందుకు వాడలేదు? భవిష్యత్తులో వాడతారా? కొంచెం వివరణ ఇవ్వండి.',
	'optin-survey-question-different' => 'మీరు బీటాను వాడేటట్లుగా చేయాలంటే మేము ఏమి చేసి ఉండవచ్చునని మీరు భావిస్తున్నారు?',
	'optin-survey-question-feedback' => 'మీకు తోచిన వ్యాఖ్యలు, ఆలోచనలు ఇంకేమైనా ఉంటే మాకు తప్పక పంపించండి. మీ అభిప్రాయాలు మాకు అమూల్యమైనవి!:',
	'optin-survey-question-browser' => 'మీరు ఏ విహారిణిని వాడుతున్నారు?',
	'optin-survey-answer-browser-other' => 'ఇతర విహారిణి:',
	'optin-survey-question-os' => 'మీరు వాడుతున్న నిర్వాహక వ్యవస్థ ఏది?',
	'optin-survey-answer-os-other' => 'ఇతర నిర్వాహక వ్యవస్థ:',
	'optin-survey-question-res' => 'మీ స్క్రీన్ రిజల్యూషన్ ఎంత?',
	'optin-survey-question-changes' => 'భవిష్యత్తులో బీటాలో మీరు ఎటువంటి మార్పును చూడదలుచుకుంటున్నారు (మీరు చూడదలుచుకున్నవన్నీ ఎంచుకోండి)?',
	'optin-survey-answer-changes-nav' => 'మొత్తం నావిగేషన్ సులభంగా వాడుకునేలా తయారు చేయండి.',
	'optin-survey-answer-changes-edittools' => 'ఎడిటింగ్ టూల్స్ ను పెంచండి, అభివృద్ధి చేయండి',
	'optin-survey-answer-changes-upload' => 'కంటెంట్ ను మీడియాను పెంచండి.',
	'optin-survey-answer-changes-richtext' => 'రిచ్ టెక్స్ట్ ఎడిటర్ లాంటి ఎడిటింగ్ ఉపకరణాలను రూపొందించండి.',
	'optin-survey-answer-changes-lookfeel' => 'సైటు యొక్క లుక్ అండ్ ఫీల్ ను మార్చండి.',
	'optin-survey-answer-changes-other' => 'ఇతర:',
	'optin-improvements2' => '== ఏమేమి మెరుగయ్యాయి? ==
[[File:UsabilityToolbar.png|left|link=|మెరుగైన దిద్దుబాటు పనిముట్లపట్టీ యొక్క తెరపట్టు]]
[[File:UsabilityDialogs.png|left|link=|కొత్త విషయ తయారీ సంవాద పేటికలు]]
;మెరుగైన పనిముట్ల పట్టీ
మెరగుపరిచిన పనిముట్ల పట్టీ వ్యాసాలని దిద్దుబాటు చేయడాన్ని తేలికపరుస్తుంది.
కొత్త మరియు మెరగుపరిచిన ప్రతీకాలు ప్రతీ పనిముట్టూ ఏం చేస్తుందో స్పష్టంగా తెలియజేస్తాయి.
విస్తరిత విభాగాలు అరుదుగా ఉపయోగించే పనిముట్లని ఒక నొక్కు దూరంలో దాచి గత్తరని తగ్గిస్తాయి.
;సంవాద పేటికలు
లంకెలు లేదా పట్టికలని సృష్టించాలనుకున్నప్పుడు, ఆయా పనిముట్ల ప్రతీకాలపై నొక్కితే వచ్చే సంవాద పేటికలు సులభమైన ఫారాన్ని పూరించడం ద్వారా లంకెలను మరియు పట్టికలను సృష్టించగలిగేలా మీకు తోడ్పడతాయి.
;ఇంకా
మెరుగైన మార్గదర్శకం, కాస్కేడింగ్ ట్యాబులు, మెరుగైన అన్వేషణ లాంటి మరిన్ని ఇతర అంశాలను కూడా ప్రవేశపెట్టాం.

== పాలుపంచుకోవడం ఎలా ==
ఉపయోగశీలత గురించి మరింత తెలుసుకోడానికి, దయచేసి మా [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative ముఖపుట]ని సందర్శించండి.
<div style="clear:both"></div>',
);

/** Tajik (Cyrillic) (Тоҷикӣ (Cyrillic))
 * @author Ibrahim
 */
$messages['tg-cyrl'] = array(
	'optin-survey-yes' => 'Ҳа',
	'optin-survey-no' => 'Не',
);

/** Tajik (Latin) (Тоҷикӣ (Latin))
 * @author Liangent
 */
$messages['tg-latn'] = array(
	'optin-survey-yes' => 'Ha',
	'optin-survey-no' => 'Ne',
);

/** Thai (ไทย)
 * @author Ans
 * @author Octahedron80
 * @author Woraponboonkerd
 */
$messages['th'] = array(
	'optin' => 'ติดตามการริเริ่มการใช้งาน',
	'optin-title-optedout' => 'ลองใช้รุ่นทดสอบ',
	'optin-title-optedin' => 'ออกจากรุ่นทดสอบ',
	'optin-title-justoptedin' => 'ยินดีต้อนรับสู่รุ่นทดสอบ',
	'optin-title-justoptedout' => 'ขอบคุณสำหรับการทดลองใช้รุ่นทดสอบของเรา',
	'optin-title-feedback' => 'ตอบรับการใช้รุ่นทดสอบ',
	'optin-feedback-back' => 'ถ้าคุณไม่ต้องการกรอกผลสำรวจ คุณสามารถกลับไปยัง $1',
	'optin-leave-cancel' => 'ถ้าคุณต้องการใช้รุ่นทดสอบต่อไป คุณสามารถกลับไปยัง $1',
	'optin-success-feedback' => 'ขอบคุณสำหรับเสียงตอบรับจากคุณ!',
	'optin-accept-short' => 'ทำเลย!',
	'optin-accept-long' => 'ทดลองใช้รุ่นทดสอบ',
	'optin-accept-long-anon' => 'ลงชื่อเข้าใช้และทดลองใช้รุ่นทดลอง',
	'optin-deny-short' => 'ไม่ ขอบคุณ',
	'optin-deny-long' => 'กลับไปยังหน้าที่แล้ว',
	'optin-submit-out' => 'ออกจากรุ่นทดสอบ',
	'optin-survey-yes' => 'ใช่',
	'optin-survey-no' => 'ไม่ใช่',
	'optin-survey-intro' => 'ขอบคุณสำหรับการลองใช้รุ่นทดลอง
เราต้องการทราบความคิดเห็นของคุณที่มีต่อรูปแบบใหม่ ดังนั้น จะเป็นเกียรติอย่างยิ่งถ้าคุณกรอกในการสำรวจเพิ่มเติมด้านล่างนี้ก่อนโดยคลิก "[[#leave|{{int:optin-submit-out}}]]"',
	'optin-survey-question-likedislike' => 'คุณชอบอะไรเกี่ยวกับรุ่นปรับปรุงทดลอง (เบต้า)? คุณไม่ชอบอะไรเกี่ยวกับรุ่นปรับปรุงทดลอง?',
	'optin-survey-question-whyoptout' => 'ทำไมคุณถึงเลิกใช้รุ่นปรับปรุงทดลองนี้? (เลือกทั้งหมดที่คุณคิดว่าถูกต้อง)',
	'optin-survey-answer-whyoptout-hard' => 'ใช้งานยากเกินไป',
	'optin-survey-answer-whyoptout-didntwork' => 'มันไม่ได้ทำงานตามปกติ',
	'optin-survey-answer-whyoptout-notpredictable' => 'มันไม่ได้ทำงานตามที่คาดไว้',
	'optin-survey-answer-whyoptout-didntlike-look' => 'ฉันไม่ชอบสิ่งที่มันแสดงออกมา',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'ฉันไม่ชอบแท็บและการจัดวางแบบใหม่นี้',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'ฉันไม่ชอบกล่องเครื่องมือแบบใหม่นี้',
	'optin-survey-answer-whyoptout-other' => 'เหตุผลอื่น:',
	'optin-survey-question-explain' => 'กรุณาอธิบายหรือขยายความคำตอบด้านบน:',
	'optin-survey-question-techfail' => 'คุณเคยประสบกับความผิดพลาดทางเทคนิคระหว่างการใช้รุ่นทดลองนี้หรือไม่?',
	'optin-survey-question-techfail-ifyes' => 'ถ้าเคย กรุณาอธิบายหรือขยายความ:',
	'optin-survey-question-usedtoolbar' => 'คุณเคยใช้กล่องเครื่องมือรูปแบบใหม่เพื่อแก้ไขหน้าหรือไม่?',
	'optin-survey-question-usedtoolbar-ifyes' => 'ถ้าเคย สิ่งที่คุณชอบคืออะไร? สิ่งที่คุณไม่ชอบคืออะไร?',
	'optin-survey-question-usedtoolbar-ifno' => 'ถ้าไม่เคย ทำไม? แล้วในอนาคตจะใช้หรือไม่? กรุณาอธิบาย',
	'optin-survey-question-different' => 'สิ่งที่เราได้ทำแล้วทำให้คุณยังคงใช้รุ่นทดลองนี้คืออะไร?',
	'optin-survey-question-feedback' => 'กรุณาบอกเราถ้าคุณมีความคิดเห็นอื่นๆ ที่คุณมีสำหรับเรา - ทุกๆ การตอบกลับนั้นมีประโยชน์',
	'optin-survey-question-browser' => 'เบราเซอร์ที่คุณใช้คืออะไร?',
	'optin-survey-answer-browser-other' => 'เบราเซอร์อื่นๆ:',
	'optin-survey-question-os' => 'คุณใช้ระบบปฏิบัติการอะไร',
	'optin-survey-answer-os-other' => 'ระบบปฏิบัติการอื่น',
	'optin-survey-question-res' => 'คุณใช้หน้าจอความละเอียดเท่าไร',
	'optin-survey-question-changes' => 'การเปลี่ยนแปลงที่คุณต้องการจะเห็นสำหรับรุ่นทดลองนี้ในอนาคตคืออะไร? (เลือกทั้งหมดที่คุณคิดว่าถูกต้อง)',
	'optin-survey-answer-changes-nav' => 'ทำให้การนำทางโดยรวมของเว็บไซต์ง่ายขึ้น',
	'optin-survey-answer-changes-edittools' => 'ทำให้เครื่องมือสำหรับแก้ไขมีเครื่องมือมากขึ้นและดีขึ้น',
	'optin-survey-answer-changes-upload' => 'ปรับปรุงการอัพโหลดเนื้อหาและสื่อให้ดีขึ้น',
	'optin-survey-answer-changes-richtext' => 'พัฒนาเครื่องมือการแก้ไขให้คล้ายคลึงกับเครื่องมือแก้ไข Rich-text',
	'optin-survey-answer-changes-lookfeel' => 'ปรับเปลี่ยน "รูปแบบภายนอก" ของเว็บไซต์',
	'optin-survey-answer-changes-predictability' => 'พัฒนาประสิทธิภาพการใช้งานให้คาดการณ์ง่ายขึ้น',
	'optin-survey-answer-changes-custom' => 'เปิดโอกาสให้ผู้ใช้ปรับเปลี่ยนได้อิสระมากขึ้น',
	'optin-survey-answer-changes-other' => 'อื่นๆ :',
);

/** Turkmen (Türkmençe)
 * @author Hanberke
 */
$messages['tk'] = array(
	'optin' => 'Oňaýlylyk Başlangyjyna goşulyş',
	'optin-desc' => 'Ulanyjylara Oňaýlylyk Başlangyjynyň iň täze oňaýlylyk güýçlendirmelerinden ybarat bolan Betany synap görmeklige mümkinçilik berýär.',
	'optin-title-optedout' => 'Betany synap görüň',
	'optin-title-optedin' => 'Betadan çyk',
	'optin-title-justoptedin' => 'Beta hoş geldiňiz',
	'optin-title-justoptedout' => 'Betany synap görendigiňiz üçin sag boluň',
	'optin-title-feedback' => 'Beta seslenmesi',
	'optin-needlogin' => 'Betany synap görmek üçin [$1 sessiýa açmak zerur].',
	'optin-intro' => 'Oňaýlylyk Başlangyjy Wikipediýanyň oňaýlylygyny ýokarlandyrmak üçin tagalla baryny edýär.
Betany synap göresiňiz gelýärmi?',
	'optin-feedback-intro' => 'Betany synap göreniňiz üçin sag boluň.
Täze interfeýsimiz barada nähili pikiriňiziň bardygyny bilesimiz gelýär, şonuň üçinem aşakdaky soragnamany doldursaňyz minnetdar bolarys.',
	'optin-feedback-back' => 'Eger-de soragnamany doldurasyňyz gelmese, onda $1 sahypasyna gaýdyp bilersiňiz.',
	'optin-success-in' => 'Şu wagt Betany synap görýärsiňiz.
Ekranyň ýokarky bölegindäki "{{int:optin-leave}}" çykgydyna basyp, islän wagtyňyz çykyp bilersiňiz.',
	'optin-leave-cancel' => 'Eger Betany ulanmaga dowam etjek bolsaňyz, $1 sahypasyna gaýdyp bilersiňiz.',
	'optin-success-out' => 'Betadan sag-aman çykdyňyz.
Ekranyň ýokarky bölegindäki "{{int:optin-try}}" çykgydyna basyp, islän wagtyňyz gaýtadan synap bilersiňiz.',
	'optin-success-feedback' => 'Seslenmäňiz üçin sag boluň!',
	'optin-accept-short' => 'Elbetde!',
	'optin-accept-long' => 'Betany synap gör',
	'optin-accept-long-anon' => 'Sessiýa açyň we Betany synap görüň',
	'optin-deny-short' => 'Ýok, sag boluň',
	'optin-deny-long' => 'Öňki sahypa gaýdyp bar',
	'optin-submit-out' => 'Betadan çyk',
	'optin-submit-feedback' => 'Seslenme ýazyň',
	'optin-survey-yes' => 'Hawa',
	'optin-survey-no' => 'Ýok',
	'optin-survey-intro' => 'Betany synap göreniňiz üçin sag boluň.
Täze interfeýsimiz baradaky pikiriňizi bilesimiz gelýär, şonuň üçinem "[[#leave|{{int:optin-submit-out}}]]" düwmesine basmankaňyz aşakdaky islege bagly soragnamany doldursaňyz minnetdar bolarys.',
	'optin-survey-question-likedislike' => 'Betada nämäni haladyňyz? Betada nämäni halamadyňyz?',
	'optin-survey-question-whyoptout' => 'Betadan näme üçin çykýarsyňyz? (Belläp biläýjekleriňiziň ählisini belläň)',
	'optin-survey-answer-whyoptout-hard' => 'Ulanmak örän kyn.',
	'optin-survey-answer-whyoptout-didntwork' => 'Bolmalysy ýaly işlemýär.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Nähili işlejekdigini çak edip bolmaýar.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Maňa onuň görünişi ýaranok.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Täze tablary we bezegi halamadym.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Täze gural panelini halamadym.',
	'optin-survey-answer-whyoptout-other' => 'Başga sebäp:',
	'optin-survey-question-explain' => 'Ýokardaky jogabyňyzy has giňeldip düşündiriň:',
	'optin-survey-question-techfail' => 'Beta ulananyňyzda tehniki näsazlyk ýüze çykdymy?',
	'optin-survey-question-techfail-ifyes' => 'Hawa bolsa, onda anyk ýazyň ýa-da düşündiriň',
	'optin-survey-question-usedtoolbar' => 'Bir sahypa redaktirlemek üçin täze giňelýän gural panelini ulandyňyzmy?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Hawa bolsa, nämeleri haladyňyz? Nämeleri halamadyňyz?',
	'optin-survey-question-usedtoolbar-ifno' => 'Eger ýok bolsa, näme üçin? Geljekde ulanjakmysyňyz? Düşündiriň.',
	'optin-survey-question-different' => 'Betamyzy ulanmaga dowam etmegiňiz üçin nämeler edip bileris?',
	'optin-survey-question-feedback' => 'Ähli beýleki teswirleriňizi we pikirleriňizi aýdyň. Ähli seslenme peýdalydyr:',
	'optin-survey-question-browser' => 'Haýsy brauzeri ulanýarsyňyz?',
	'optin-survey-answer-browser-other' => 'Başga brauzer:',
	'optin-survey-question-os' => 'Haýsy operasion ulgamy ulanýarsyňyz?',
	'optin-survey-answer-os-other' => 'Başga operasion ulgam:',
	'optin-survey-question-res' => 'Ekran çözgüdiňiz näçe?',
	'optin-survey-question-changes' => 'Betada geljekde näme göresiňiz gelýär (laýyklaryň ählisini saýlaň)?',
	'optin-survey-answer-changes-nav' => 'Saýtyň umumy nawigasiýasyny has ýeňil ulanar ýaly ediň.',
	'optin-survey-answer-changes-edittools' => 'Redaktirleme gurallaryny köpeldiň we güýçlendiriň.',
	'optin-survey-answer-changes-upload' => 'Mazmuny we media ýüklemelerini gowulandyryň.',
	'optin-survey-answer-changes-richtext' => 'Baý tekst redaktorlaryna meňzeş ösdürme gurallaryny taýýarlaň.',
	'optin-survey-answer-changes-other' => 'Başga:',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'optin' => 'Paglahok sa Pagpapanimula ng Pagkanagagamit',
	'optin-desc' => 'Pahintulutan ang mga tagagamit na subukan ang Beta, ang pinakabagong mga pagpapaigi ng pagkanagagamit ng Pagpapanimula ng Pagkanagagamit',
	'optin-title-optedout' => 'Subukan ang Beta',
	'optin-title-optedin' => 'Umalis mula sa Beta',
	'optin-title-justoptedin' => 'Maligayang pagdating sa Beta',
	'optin-title-justoptedout' => 'Salamat sa pagsubok mo ng aming Beta',
	'optin-title-feedback' => 'Mga puna sa Beta',
	'optin-needlogin' => 'Kailangan mong [$1 lumagda] upang masubukan ang Beta.',
	'optin-intro' => 'Ang Pagpapanimula ng Pagkanagagamit ng Wikipedia ay nagsusumikap upang mapainam ang pagiging nagagamit ng Wikipedia. Nais mo bang subukan ang aming Beta?',
	'optin-feedback-intro' => 'Salamat sa pagsubok mo ng Beta. 
Nais namin na malaman kung ano ang tingin mo sa aming bagong hangganang-mukha, mamarapatin namin ang maaaring hindi gawing pagtugon mo sa mga katanungang nasa ibaba.',
	'optin-feedback-back' => 'Kung hindi mo nais na sagutin ang pagtatanong, maaari kang magbalik sa $1.',
	'optin-success-in' => 'Sinusubukan mo na ngayon ang Beta.
Maaari kang umatras at huwag makilahok anumang oras sa pamamagitan ng pagpindot sa kawing na "{{int:optin-leave}}" na nasa kawing na nasa itaas ng pahina.',
	'optin-leave-cancel' => 'Kung nais mong magpatuloy sa paggamit ng Beta, maaaring kang bumalik sa $1.',
	'optin-success-out' => 'Matagumpay ka nang nakaalis mula sa Beta.
Maaari mo itong subukang muli anumang oras sa pamamagitan ng pagpindot sa kawing na "{{int:optin-try}}" na nasa itaas ng pahina.',
	'optin-success-feedback' => 'Salamat sa iyong mga puna!',
	'optin-accept-short' => 'Iyan ang gawin natin!',
	'optin-accept-long' => 'Subukan ang Beta',
	'optin-accept-long-anon' => 'Lumagda at subukan ang Beta',
	'optin-deny-short' => 'Huwag, salamat na lang',
	'optin-deny-long' => 'Bumalik sa nakaraang pahina',
	'optin-submit-out' => 'Umalis mula sa Beta',
	'optin-submit-feedback' => 'Magbigay ng puna',
	'optin-survey-yes' => 'Oo',
	'optin-survey-no' => 'Huwag',
	'optin-survey-intro' => 'Salamat sa pagsubok mo ng Beta.
Nais naming malaman ang tingin mo sa aming bagong hangganang-mukha, mamarapatin namin ang maaaring huwag gawing pagtugon mo sa mga pagtatanong na nasa ibaba bago pindutin ang "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Ano ang naibigan mo tungkol sa Beta? Ano ang hindi mo nagustuhan tungkol sa Beta?',
	'optin-survey-question-whyoptout' => 'Bakit ka aalis mula sa Beta? (Pakipili ang lahat ng mga maaari.)',
	'optin-survey-answer-whyoptout-hard' => 'Mahirap itong gamitin.',
	'optin-survey-answer-whyoptout-didntwork' => 'Hindi maayos ang pag-andar nito.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Hindi maaasahan ang pagganap nito.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Hindi ko naibigan ang anyo nito.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Hindi ko naibigan ang bagong mga panglaylay at pagkakaayos.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Hindi ko gusto ang bagong kahon ng kasangkapan.',
	'optin-survey-answer-whyoptout-other' => 'Ibang dahilan:',
	'optin-survey-question-explain' => 'Mangyaring ipaliwanag o palawigin ang sagot mong nasa itaas:',
	'optin-survey-question-techfail' => 'Nakaranas ka ba ng anumang mga kabiguang teknikal habang ginagamit ang Beta?',
	'optin-survey-question-techfail-ifyes' => 'Kung oo, mangyaring palawigin o ipaliwanag:',
	'optin-survey-question-usedtoolbar' => 'Nagamit mo ang bagong napapalawak na kahon ng kasangkapan sa pamamatnugot ng isang pahina?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Kung oo, ano ang naibigan mo tungkol dito? Ano ang hindi mo nagustuhan tungkol dito?',
	'optin-survey-question-usedtoolbar-ifno' => 'Kung hindi, bakit hindi? Gagamitin mo ba ito sa hinaharap? Mangyaring ipaliwanag.',
	'optin-survey-question-different' => 'Ano ba ang magagawa naming naiiba upang mapanatili kang gumagamit ng Beta?',
	'optin-survey-question-feedback' => 'Mangyaring ipaalam sa amin ang tungkol sa iba pang mga pagpuna, mga alalahanin, o mga kaisipan mayroon ka para sa amin - magagamit ang lahat ng mga pabalik-sabi:',
	'optin-survey-question-browser' => 'Anong pambasa-basa ba ang ginagamit mo?',
	'optin-survey-answer-browser-other' => 'Ibang pambasa-basa:',
	'optin-survey-question-os' => 'Anong sistemang pampaandar ang ginagamit mo?',
	'optin-survey-answer-os-other' => 'Iba pang sistema ng pampaandar:',
	'optin-survey-question-res' => 'Ano ang resolusyon ng tanawan mo?',
	'optin-survey-question-changes' => 'Anong mga pagbabago ang nais mong makita sa loob ng Beta pagdating ng hinaharap (pakipili ang lahat ng maaari)?',
	'optin-survey-answer-changes-nav' => 'Gawing mas maginhawa ang paggamit na kabuoang paglilibot sa sayt.',
	'optin-survey-answer-changes-edittools' => 'Dagdagan at paigihin ang mga kasangkapang pampatnugot.',
	'optin-survey-answer-changes-upload' => 'Painamin ang mga paitaas na pagkakarga ng nilalaman at midya.',
	'optin-survey-answer-changes-richtext' => 'Magpaunlad ng mga kasangkapang pampatnugot na katulad ng sa mga patnugot ng mayamang teksto.',
	'optin-survey-answer-changes-lookfeel' => 'Isapanahon ang "wangis at hipo" ng sayt.',
	'optin-survey-answer-changes-predictability' => 'Painamin ang pagka naaasahan ng pagganap.',
	'optin-survey-answer-changes-custom' => 'Pahintulutan ang mas malaking pagpapasadya ng tagagamit.',
	'optin-survey-answer-changes-other' => 'Iba pa:',
	'optin-improvements2' => '== Ano ba ang napainam? ==
[[File:UsabilityToolbar.png|left|link=|Larawan ng pinainam na kahon ng kasangkapan na pampatnugot]]
[[File:UsabilityDialogs.png|left|link=|Larawan ng mga salitaan na para sa paglikha ng bagong nilalaman]]
;Pinainam na kahon ng kasangkapan
Nakapagpapaginhawa sa pamamatnugot ng mga lathalain ang pinainam na kahon ng kasangkapan.
Nakapagpapalinaw sa kung anong galaw ang gagampanan ng bawat isang kasangkapan ang bago at napainam na mga larawan.
Nakapagbabawas ng kalat ang pinalawak na mga seksyon habang pinananatiling mapipindot lang kaagad ang hindi gaanong ginagamit na mga kasangkapan.
;Diyalogo
Kapag lumilikha ng mga kawing o mga talahanayan, nagbubukas na ngayon ng isang kahon ng salitaan ang pagpindot sa larawan na pangkasangkapan na nakakatulong sa iyo na makalikha ng mga kawing at mga talahanayan sa pamamagitan ng pagpupuno sa isang payak na pormularyo.
;At marami pa
Nagpakilala kami ng isang bilan gng iba pang mga pagpapainam katulad ng pinaiging panglibot, dumudulas na mga panglaylay, pinainam na paghahanap, at marami pa.
== Paano makakalahok ==
Upang makatuklas ng iba pang tungkol sa Pampanimula ng Pagkanagagamit, mangyaring dalawin ang aming [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative bahay-pahina].
<div style="clear:both"></div>',
);

/** Turkish (Türkçe)
 * @author Bekiroflaz
 * @author Joseph
 * @author Vito Genovese
 */
$messages['tr'] = array(
	'optin' => 'Kullanılabilirlik Girişimi katılım',
	'optin-desc' => "Kullanıcıların Beta'yı, Kullanılabilirlik Girişimi'nin en son kullanılabilirlik geliştirmelerini, denemelerine izin verir",
	'optin-title-optedout' => "Beta'yı Deneyin",
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
	'optin-survey-question-feedback' => 'Lütfen tüm diğer yorum, endişe ve düşüncelerinizi bize bildirin - her türlü geribildirim yararlıdır:',
	'optin-survey-question-browser' => 'Hangi tarayıcıyı kullanıyorsunuz?',
	'optin-survey-answer-browser-other' => 'Diğer tarayıcı:',
	'optin-survey-question-os' => 'Hangi işletim sistemini kullanıyorsunuz?',
	'optin-survey-answer-os-other' => 'Diğer işletim sistemi:',
	'optin-survey-question-res' => 'Ekran çözünürlüğünüz nedir?',
	'optin-survey-question-changes' => "Beta'da gelecekte hangi değişiklikleri görmek istersiniz (uygun olanların tamamını seçin)?",
	'optin-survey-answer-changes-nav' => 'Sitenin genel dolaşımını kullanımı kolay yapın.',
	'optin-survey-answer-changes-edittools' => 'Düzenleme araçlarını çoğaltın ve geliştirin.',
	'optin-survey-answer-changes-upload' => 'Ortam yüklemelerini ve içeriği geliştirin.',
	'optin-survey-answer-changes-richtext' => 'Zengin-metin editörlerine benzer değiştirme araçlarını geliştirin.',
	'optin-survey-answer-changes-lookfeel' => 'Sitenin "görünüm ve hissini" güncelleyin.',
	'optin-survey-answer-changes-predictability' => 'Öngörülebilirlik performansını geliştirin.',
	'optin-survey-answer-changes-custom' => 'Daha fazla kullanıcı özelleştirmesine izin ver.',
	'optin-survey-answer-changes-other' => 'Diğer:',
	'optin-improvements2' => '==Ne gibi gelişmeler var? ==
[[File:UsabilityToolbar.png|left|link=|Screenshot of the enhanced edit toolbar]]
[[File:UsabilityDialogs.png|left|link=|Screenshot of the new content generation dialogs]]
;Geliştirilmiş Araç Çubuğu
Geliştirilmiş araç çubuğu maddeleri düzenlemeyi kolaylaştırır.
Yeni ve iyileştirilmiş simgeler her simgenin ne yaptığının anlaşılmasını kolaylaştırır.
Genişletilmiş bölümler, sık kullanılmayan araçları bir tık uzakta tutarak dağınıklığı azaltır.
;Diyalog kutusu
Bağlantı veya tablo oluşturmak için önce ilgili simgeye tıklarsınız. Açılan diyalog kutusu aracılığıyla kolay bir şekilde, formlar kullanarak sayfaya bağlantı veya tablo ekleyebilirsiniz.
;Ve Fazlası
Ayrıca bunların dışında; geliştirilmiş navigasyon, basamaklanmış sekmeler ve geliştirilmiş arama gibi yeni özellikler sizi bekliyor.
== Nasıl katkıda bulunurum? ==
Kullanışlılığı geliştirme çalışmalarına katılma hakkında bilgi için  [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative burayı] ziyaret edin.
<div style="clear:both"></div>',
);

/** Tatar (Cyrillic) (Татарча/Tatarça (Cyrillic))
 * @author Rinatus
 */
$messages['tt-cyrl'] = array(
	'optin-title-optedout' => 'Интерфейсның бета-версиясен карау',
	'optin-title-optedin' => 'Beta-версиядән чыгарга',
	'optin-survey-yes' => 'Әйе',
	'optin-survey-no' => 'Юк',
	'optin-survey-answer-whyoptout-other' => 'Башка сәбәп:',
	'optin-survey-answer-browser-other' => 'Башка браузер:',
);

/** Udmurt (Удмурт)
 * @author Kaganer
 */
$messages['udm'] = array(
	'optin-accept-long' => 'Бета-версиен ужаны',
);

/** Ukrainian (Українська)
 * @author A1
 * @author AS
 * @author Ickis
 * @author NickK
 * @author Prima klasy4na
 */
$messages['uk'] = array(
	'optin' => 'Участь в ініціативі зручності',
	'optin-desc' => 'Дозволити користувачам спробувати Beta, останні покращення, в рамках ініціативи зручності.',
	'optin-title-optedout' => 'Спробуйте Beta',
	'optin-title-optedin' => 'Залишити Beta',
	'optin-title-justoptedin' => 'Ласкаво просимо до Beta',
	'optin-title-justoptedout' => 'Дякуємо вам за випробування нашої Beta',
	'optin-title-feedback' => "Зворотний зв'язок",
	'optin-needlogin' => 'Вам необхідно [$1 увійти], щоб спробувати Beta.',
	'optin-intro' => 'В рамках ініціативи зручності була проведена робота щодо поліпшення зручності використання Вікіпедії.
Чи не бажаєте ви спробувати нашу Beta?',
	'optin-feedback-intro' => 'Дякуємо вам за випробування Beta.
Нам хотілося б знати, що ви думаєте про наш новий інтерфейс, так що ми будемо вдячні, якщо ви візьмете участь у наведеному нижче опитуванні.',
	'optin-feedback-back' => 'Якщо ви не хочете брати участь в опитуванні, ви можете повернутися до $1.',
	'optin-success-in' => 'Тепер ви берете участь у випробуванні Бета-версії інтерфейсу.
Ви можете відключитися від програми в будь-який час, натиснувши на посилання «{{int:optin-leave}}» у верхній частині екрана.',
	'optin-leave-cancel' => 'Якщо ви хочете завершити використання Beta, ви можете повернуться до $1.',
	'optin-success-out' => 'Ви успішно вийшли з режиму випробування Бета-версії інтерфейсу.
Ви можете ще раз спробувати цю версію, натиснувши посилання «{{int:optin-try}}» у правому верхньому куту екрана.',
	'optin-success-feedback' => 'Дякуємо за ваш відгук!',
	'optin-accept-short' => 'Вперед!',
	'optin-accept-long' => 'Випробувати Бета-версію',
	'optin-accept-long-anon' => 'Авторизуватися й спробувати Бета-версію інтерфейсу',
	'optin-deny-short' => 'Ні, дякую',
	'optin-deny-long' => 'Повернутися на попередню сторінку',
	'optin-submit-out' => 'Покинути Бета-версію',
	'optin-submit-feedback' => 'Відправити відгук',
	'optin-survey-yes' => 'Так',
	'optin-survey-no' => 'Ні',
	'optin-survey-intro' => 'Дякуємо вас за участь у випробуванні Бета-версії інтерфейсу.
Нам би хотілося знати, що ви думаєте про новий інтерфейс, тому, перед тим, як ви натиснете «[[#leave|{{int:optin-submit-out}}]]», ми були б вам вдячні за участь у наведеному нижче опитуванні.',
	'optin-survey-question-likedislike' => 'Що вам подобається в Beta? Що вам не подобається в Beta?',
	'optin-survey-question-whyoptout' => 'Чому ви покидаєте Бету? (Будь ласка, виберіть підходящі значення)',
	'optin-survey-answer-whyoptout-hard' => 'Він дуже складний у використанні.',
	'optin-survey-answer-whyoptout-didntwork' => 'Він не працює належним чином.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Вона працює непередбачувано.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Мені не подобається як вона виглядає.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Мені не сподобалися нові вкладки й верстка.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Мені не сподобалася нова панель редагування.',
	'optin-survey-answer-whyoptout-other' => 'Інша причина:',
	'optin-survey-question-explain' => 'Будь ласка, поясніть або уточніть Вашу відповідь вище:',
	'optin-survey-question-techfail' => 'Ви зіткнулися з якими-небудь технічними збоями при використанні Beta?',
	'optin-survey-question-techfail-ifyes' => 'Якщо так, то будь ласка уточніть або поясніть:',
	'optin-survey-question-usedtoolbar' => 'Чи використали Ви нову розгортувану панель інструментів для редагування сторінок?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Якщо так, те, що вам у ній сподобалося? Що не сподобалося?',
	'optin-survey-question-usedtoolbar-ifno' => 'Якщо ні, то чому?  Чи збираєтеся в майбутньому? Будь ласка, поясніть.',
	'optin-survey-question-different' => 'Що б ми могли зробити по-іншому, щоб ви продовжили використання Beta?',
	'optin-survey-question-feedback' => 'Будь ласка, висловіть своє судження:',
	'optin-survey-question-browser' => 'Яким оглядачем ви користуєтесь?',
	'optin-survey-answer-browser-other' => 'Інший:',
	'optin-survey-question-os' => 'Якою операційною системою ви користуєтесь?',
	'optin-survey-answer-os-other' => 'Інша операційна система:',
	'optin-survey-question-res' => 'Яка роздільність вашого монітора?',
	'optin-survey-question-changes' => 'Які зміни ви хотіли б бачити в Beta в майбутньому (виберіть все належне)?',
	'optin-survey-answer-changes-nav' => 'Зробити загальну навігацію сайтом простішою у використанні.',
	'optin-survey-answer-changes-edittools' => 'Розширити і вдосконалити інструменти редагування.',
	'optin-survey-answer-changes-upload' => 'Удосконалити завантаження контенту та медіа.',
	'optin-survey-answer-changes-richtext' => 'Розробити інструменти редагування, аналогічні багатофункціональним текстовим редакторам.',
	'optin-survey-answer-changes-lookfeel' => 'Оновити «зовнішній вигляд» сайту.',
	'optin-survey-answer-changes-predictability' => 'Підвищити передбачуваність роботи.',
	'optin-survey-answer-changes-custom' => 'Дозволити більше користувацьких налаштувань.',
	'optin-survey-answer-changes-other' => 'Інше:',
	'optin-improvements2' => '== Що вдосконалено? ==
[[File:UsabilityToolbar.png|left|link=|Знімок покращеної панелі засобів]]
[[File:UsabilityDialogs.png|left|link=|Знімок зручного діалогу вставки]]
;Покращена панель засобів
Нова панель інструментів полегшує редагування статей.
Нові іконки на кнопках краще відображають суть відповідних інструментів.
Розділи з можливістю згортання зменшують безлад, а найуживаніші інструменти лишаються під рукою.
;Діалог
Посилання чи таблиці можна створити, викликавши відповідні діалогові віконця, в яких достатньо заповнити прості форми.
;І навіть більше
Ми ввели немало інших поліпшень, таких як розширена навігація, каскадні верхні заладки, покращений пошук, тощо.
== Як приєднатися ==
Щоб дізнатися більше про Ініціативу зручності, відвідайте [http://usability.wikimedia.org/wiki/домашню сторінку Ініціативи зручності Вікіпедії].
<div style="clear:both"></div>',
);

/** Vèneto (Vèneto)
 * @author Candalua
 */
$messages['vec'] = array(
	'optin' => "Adesion a l'Inissiativa par l'Usabilità",
	'optin-title-optedout' => 'Próa la Beta',
	'optin-title-optedin' => 'Và fora da Beta',
	'optin-title-justoptedin' => 'Benvegnù in Beta',
	'optin-title-justoptedout' => 'Grassie de ver proà la nostra Beta',
	'optin-title-feedback' => 'Cossa ghe ne pénsito de la Beta',
	'optin-needlogin' => 'Te ghè da [$1 far el login] par proàr Beta',
	'optin-intro' => "Noaltri del Progeto Usabilità de Wikipedia semo drio laorar come i mussi par mejorar l'usabilità de Wikipedia.
Vuto proàr la nostra version Beta?",
	'optin-feedback-intro' => 'Grassie de ver proà la Beta.
Gavaressimo caro savér cossa te ghe ne pensi de la nostra interfacia nova, quindi ne piasarìa che te conpilessi el sondagio facoltativo qua soto.',
	'optin-feedback-back' => 'Se no te vol mia conpilar el sondagio, te pol tornar a $1.',
	'optin-success-in' => 'Desso te sì drio proàr la Beta.
Te pol \'ndar fora in qualunque momento strucando "{{int:optin-leave}}" ne la parte de sora del schermo.',
	'optin-leave-cancel' => "Se te vol 'ndar vanti a doparar Beta, te pol tornar a $1.",
	'optin-success-out' => 'Te sì \'ndà fora da Beta.
Te podi entrar da novo quando te vol strucando su "{{int:optin-try}}" in alto a destra de la pagina.',
	'optin-success-feedback' => 'Grassie de verne dito la to opinion!',
	'optin-accept-short' => 'Va ben, dai!',
	'optin-accept-long' => 'Próa la Beta',
	'optin-accept-long-anon' => 'Fà el login e próa la Beta',
	'optin-deny-short' => 'No, grassie',
	'optin-deny-long' => 'Torna a la pagina de prima',
	'optin-submit-out' => 'Và fora da la Beta',
	'optin-submit-feedback' => 'Dine cossa te ghe ne pensi',
	'optin-survey-yes' => 'Sì',
	'optin-survey-no' => 'No',
	'optin-survey-intro' => 'Grassie de ver proà la Beta.
Gavaressimo caro savér cossa te ghe ne pensi de la nostra interfacia nova, quindi ne piasarìa che te conpilessi el sondagio facoltativo qua soto prima de strucar su "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Cossa te ga piasso de la Beta? Cossa no te ga piasso?',
	'optin-survey-question-whyoptout' => "Com'èla che te vol 'ndar fora da la Beta? (Te pol selessionar anca pi de una risposta.)",
	'optin-survey-answer-whyoptout-hard' => 'Massa fadiga dopararla.',
	'optin-survey-answer-whyoptout-didntwork' => 'No la funsiona mia ben.',
	'optin-survey-answer-whyoptout-notpredictable' => 'No la funsiona come se se spetarìa.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'No me piase come la se vede.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'No me piase le schede nove e la dispoxission grafica.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'No me piase la nova bara dei strumenti.',
	'optin-survey-answer-whyoptout-other' => 'Altra motivassion:',
	'optin-survey-question-explain' => 'Par piaser spiega mejo la to risposta:',
	'optin-survey-question-techfail' => 'Se gà verificà qualche problema tecnico doparando Beta?',
	'optin-survey-question-techfail-ifyes' => 'Se sì, spiega:',
	'optin-survey-question-usedtoolbar' => 'Gheto doparà la nova bara dei strumenti espandibile par modificar na pagina?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Se sì, cossa te gà piasso? Cossa no te gà piasso?',
	'optin-survey-question-usedtoolbar-ifno' => 'Se no, parché de no? Podaressito dopararla in futuro? Spiega, par piaser.',
	'optin-survey-question-browser' => 'Che browser dòparito?',
	'optin-survey-answer-browser-other' => 'Altro browser:',
	'optin-survey-question-os' => 'Che sistema operativo dòparito?',
	'optin-survey-answer-os-other' => 'Altro sistema operativo:',
	'optin-survey-question-res' => 'Quala xea la risolussion del to schermo?',
	'optin-survey-answer-changes-other' => 'Altro:',
);

/** Veps (Vepsan kel')
 * @author Игорь Бродский
 */
$messages['vep'] = array(
	'optin-title-optedout' => 'Kodvi Betad',
	'optin-title-optedin' => 'Lähtta Betaspäi',
	'optin-title-justoptedin' => 'Tulgat tervhin Betaha!',
	'optin-accept-short' => 'Tehkam se!',
	'optin-survey-yes' => 'Ka',
	'optin-survey-no' => 'Ei',
	'optin-survey-answer-whyoptout-other' => 'Toine sü:',
	'optin-survey-answer-browser-other' => 'Toine kaclim:',
	'optin-survey-answer-changes-other' => 'Toine:',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'optin' => 'Tham gia Sáng kiến Khả dụng',
	'optin-desc' => 'Cho phép người dùng dùng thử bản Beta, bản nâng cao khả dụng mới nhất của Sáng kiến Khả dụng.',
	'optin-title-optedout' => 'Thử bản Beta',
	'optin-title-optedin' => 'Rời Beta',
	'optin-title-justoptedin' => 'Chào mừng đến với Beta',
	'optin-title-justoptedout' => 'Cảm ơn bạn đã thử bản Beta của chúng tôi',
	'optin-title-feedback' => 'Phản hồi Beta',
	'optin-needlogin' => 'Bạn cần phải [$1 đăng nhập] để thử bản Beta.',
	'optin-intro' => 'Sáng kiến Khả dụng Wikipedia đã bỏ rất nhiều công sức để tăng tính khả dụng của Wikipedia.
Bạn có muốn thử bản Beta của chúng tôi?',
	'optin-feedback-intro' => 'Cảm ơn bạn đã dùng thử bản Beta.
Chúng tôi muốn biết cảm nghĩ của bạn về giao diện mới của chúng ta, vì vậy chúng tôi rất cảm kích nếu bạn điền vào bản thăm dò ý kiến dưới đây.',
	'optin-feedback-back' => 'Nếu bạn không muốn điền vào bản thăm dò ý kiến, bạn có thể quay lại $1.',
	'optin-success-in' => 'Bạn hiện đang dùng thử bản Beta.
Bạn có thể ngừng sử dụng bất cứ lúc nào bằng cách nhấn vào liên kết “{{int:optin-leave}}” ở phía trên màn hình.',
	'optin-leave-cancel' => 'Nếu bạn muốn tiếp tục sử dụng Beta, bạn có thể quay lại $1.',
	'optin-success-out' => 'Bạn đã thoát khỏi Beta.
Bạn có thể dùng lại nó bất cứ lúc nào bằng cách nhấn vào liên kết “{{int:optin-try}}” ở phía trên bên phải màn hình.',
	'optin-success-feedback' => 'Cảm ơn vì những phản hồi của bạn!',
	'optin-accept-short' => 'Tiến hành thôi!',
	'optin-accept-long' => 'Thử bản Beta',
	'optin-accept-long-anon' => 'Đăng nhập và dùng thử bản Beta',
	'optin-deny-short' => 'Không cảm ơn',
	'optin-deny-long' => 'Quay trở lại trang trước',
	'optin-submit-out' => 'Rời khỏi Beta',
	'optin-submit-feedback' => 'Phản hồi',
	'optin-survey-yes' => 'Có',
	'optin-survey-no' => 'Không',
	'optin-survey-intro' => 'Cảm ơn bạn đã dùng thử Beta.
Chúng tôi muốn biết cảm nghĩ của bạn về giao diện mới của chúng ta, vì vậy chúng tôi sẽ rất cảm kích nếu bạn điền vào bản thăm dò dưới đây trước khi nhấn “[[#leave|{{int:optin-submit-out}}]]”.',
	'optin-survey-question-likedislike' => 'Bạn thấy thích thú điều gì về Beta? Bạn thấy không hài lòng điều gì về Beta?',
	'optin-survey-question-whyoptout' => 'Tại sao bạn rời Beta? (Xin chọn tất cả các ý phù hợp.)',
	'optin-survey-answer-whyoptout-hard' => 'Nó khó sử dụng quá.',
	'optin-survey-answer-whyoptout-didntwork' => 'Nó bị trục trặc.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Nó không hành động như tiên đoán.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Tôi không thích ngoại hình của nó.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Tôi không thích các thẻ và cách sắp xếp mới.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Tôi không thích thanh công cụ mới.',
	'optin-survey-answer-whyoptout-other' => 'Lý do khác:',
	'optin-survey-question-explain' => 'Xin hãy giải thích hoặc nói rõ hơn câu trả lời của bạn phía trên:',
	'optin-survey-question-techfail' => 'Bạn có gặp lỗi kỹ thuật khi sử dụng Beta không?',
	'optin-survey-question-techfail-ifyes' => 'Nếu có, xin nói rõ hoặc giải thích:',
	'optin-survey-question-usedtoolbar' => 'Bạn đã sử dụng thanh công cụ tùy biến để sửa đổi trang chưa?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Nếu có, bạn cảm thấy thích thú về điều gì? Bạn cảm thấy không hài lòng về điều gì?',
	'optin-survey-question-usedtoolbar-ifno' => 'Nếu không thì tại sao không? Bạn sẽ dùng trong tương lai chứ? Xin hãy giải thích.',
	'optin-survey-question-different' => 'Bạn sẽ sử dụng tiếp Beta nếu chúng tôi thay đổi điều gì?',
	'optin-survey-question-feedback' => 'Xin cho chúng tôi biết các ý kiến, lo ngại hoặc suy nghĩ khác của bạn dành cho chúng tôi - mọi phản hồi đều hữu ích!:',
	'optin-survey-question-browser' => 'Bạn sử dụng trình duyệt nào?',
	'optin-survey-answer-browser-other' => 'Trình duyệt khác:',
	'optin-survey-question-os' => 'Bạn sử dụng hệ điều hành nào?',
	'optin-survey-answer-os-other' => 'Hệ điều hành khác:',
	'optin-survey-question-res' => 'Màn hình của bạn có độ phân giải nào?',
	'optin-survey-question-changes' => 'Bạn muốn nhìn thấy sự thay đổi nào của Beta trong tương lai (xin chọn tất cả phù hợp)?',
	'optin-survey-answer-changes-nav' => 'Làm cho khả năng duyệt tìm chung trên trang dễ sử dụng hơn.',
	'optin-survey-answer-changes-edittools' => 'Tăng các công cụ sửa đổi về số lượng lẫn chất lượng.',
	'optin-survey-answer-changes-upload' => 'Cải tiến tải lên nội dung và phương tiện.',
	'optin-survey-answer-changes-richtext' => 'Phát triển các công cụ sửa đổi giống như trình sửa đổi giàu chức năng.',
	'optin-survey-answer-changes-lookfeel' => 'Cập nhật “bề ngoài và cảm nhận” cho trang.',
	'optin-survey-answer-changes-predictability' => 'Tăng cường khả năng dự đoán khi hoạt động.',
	'optin-survey-answer-changes-custom' => 'Cho phép người dùng tùy chỉnh nhiều hơn.',
	'optin-survey-answer-changes-other' => 'Khác:',
	'optin-improvements2' => '== Những điều gì đã được cải tiến? ==
[[File:UsabilityToolbar.png|left|link=|Hình chụp màn hình thanh công cụ soạn thảo cải tiến]]
[[File:UsabilityDialogs.png|left|link=|Hình chụp màn hình hộp hội thoại để tạo nội dung mới]]
;Thanh Công Cụ Cải Tiến
Thanh công cụ cải tiến giúp bạn chỉnh sửa bài viết dễ dàng hơn.
Các biểu tượng mới và đã cải tiến sẽ giúp bạn hiểu rõ tính năng của từng nút.
Các phần mở rộng sẽ giúp giảm bớt sự lộn xộn nhưng vẫn giữ lại các công cụ ít dùng chỉ bằng một cú nhấn chuột.
;Hộp Hội Thoại
Giờ đây khi tạo liên kết hoặc bảng biểu, nhấn vào biểu tượng công cụ sẽ mở một hộp hội thoại giúp bạn tạo liên kết và bảng biểu bằng cách điền vào một mẫu đơn đơn giản.
;Và Còn Hơn Thế Nữa
Chúng tôi đã giới thiệu một số các cải tiến khác như cải tiến duyệt trang, xếp chồng các thẻ, cải tiến tìm kiếm, và nhiều thứ khác.
== Làm sao để tham gia ==
Để tìm hiểu thêm về Sáng kiến Khả dụng, xin mời ghé thăm [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative trang chủ] của chúng tôi.
<div style="clear:both"></div>',
);

/** Volapük (Volapük)
 * @author Malafaya
 */
$messages['vo'] = array(
	'optin-deny-long' => 'Geikön lü pad büik',
	'optin-survey-yes' => 'Si!',
	'optin-survey-no' => 'Nö!',
	'optin-survey-answer-whyoptout-other' => 'Kod votik:',
	'optin-survey-answer-changes-other' => 'Votik:',
);

/** Yiddish (ייִדיש)
 * @author פוילישער
 */
$messages['yi'] = array(
	'optin' => 'זיך אײַנמעלדן צו דער ניצלעכקייט איניציאטיוו',
	'optin-desc' => "דערלויבן באניצער אויספרובירן בעטאַ, דער ניצלעכקייט איניציאטיוו'ס לעצטע ניצלעכקייט פֿארבעסערונגען.",
	'optin-title-optedout' => 'פרובירן בעטא',
	'optin-title-optedin' => 'פֿארלאזן בעטא',
	'optin-title-justoptedin' => 'ברוך־הבא צו בעטא',
	'optin-title-justoptedout' => 'ייש"כ פאר פרובירן אונזער בעטא',
	'optin-title-feedback' => 'בעטא פֿידבעק',
	'optin-needlogin' => 'איר דארפֿט [$1 אריינלאגירן] צו פרובירן בעטא.',
	'optin-intro' => 'די וויקיפעדיע ניצלעכקייט איניציאטיוו האט געארבעט שווער צו פארבעסערן די ניצלעכקייט פון וויקיפעדיע.
אפשר ווילט איר פרובירן אונזער בעטא?',
	'optin-feedback-intro' => "א דאַנק פֿאַרן אויספרובירן בעטאַ.
מיר ווילן וויסן וואס איר האַלט וועגן דעם באַניצער אייבערפֿלאַך, דערפֿאַר וועלן מיר אַנערקענען ווען איר וועט אָנ פֿילן דעם ברירה'דיקן אַרומפֿרעג אונטן.",
	'optin-feedback-back' => 'אַז איר ווילט נישט אויספֿילן דעם פֿרעגבויגן, קענט איר צוריקקערן צו $1.',
	'optin-success-in' => 'אַצינד פרובירט איר אויס בעטא.
איר קענט עס פֿאַרלאזן ווען איר ווילט, מען דאַרף נאר אָנקליקן דעם לינק "{{int:optin-leave}}" אויבן.',
	'optin-leave-cancel' => 'ווען איר ווילט ווײַטער ניצן בעטא, קענט איר צוריקקערן צו $1.',
	'optin-success-out' => 'איר האט דערפֿאלגרייך פֿאַרלאזט בעטא.
איר קענט עס אויספרובירן נאכאַמאָל ווען איר ווילט, דורך קליקן דעם "{{int:optin-try}}" לינק אין דער הייך פונעם בלאַט.',
	'optin-success-feedback' => 'א דאַנק פֿאַרן פֿידבעק!',
	'optin-accept-short' => 'לאמיר טון!',
	'optin-accept-long' => 'פרובירן בעטא',
	'optin-accept-long-anon' => 'ארײַנלאגירן און אויספרובירן בעטא',
	'optin-deny-short' => 'ניין א דאנק',
	'optin-deny-long' => 'צוריק צום פריערדיקן בלאט',
	'optin-submit-out' => 'פֿארלאזן בעטא',
	'optin-submit-feedback' => 'געבן פֿידבעק',
	'optin-survey-yes' => 'יא',
	'optin-survey-no' => 'ניין',
	'optin-survey-answer-whyoptout-hard' => "ס'איז געווען צו שווער צו ניצן.",
	'optin-survey-answer-whyoptout-didntwork' => 'עס האט נישט פֿונקציאָנירן געהעריג..',
	'optin-survey-answer-whyoptout-didntlike-look' => 'די אויסזע האט מיר נישט געפֿאלן',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'די נײַע טאַבן און צעשטעל האבן מיר נישט געפֿאלן.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'דער נײַער ווערקצייגפאַס האט מיר נישט געפֿאלן.',
	'optin-survey-answer-whyoptout-other' => 'אַנדער טעם:',
	'optin-survey-question-techfail-ifyes' => 'ווען יא, ביטע ערקלערן',
	'optin-survey-question-usedtoolbar-ifyes' => 'ווען יאָ, וואָס האט אייך געפֿאלן וועגן עס? וואס האט אייך נישט געפֿאלן וועגן עס?',
	'optin-survey-question-different' => 'וואָס וואלטן אונז געקענט טון אנדערש בכדי איר זאלט ווײַטער ניצן די בעטא?',
	'optin-survey-question-browser' => 'וועלכן בלעטערער ניצט איר?',
	'optin-survey-answer-browser-other' => 'אנדער בלעטערער:',
	'optin-survey-question-os' => 'וועלכע אפערירן סיסטעם ניצט איר?',
	'optin-survey-answer-os-other' => 'אנדער אפערירן סיסטעם:',
	'optin-survey-question-res' => 'וואָס איז די רעזאלוציע פון אייער עקראַן?',
	'optin-survey-answer-changes-edittools' => 'פֿאַרברייטערן און פאַרבעסערן די רעדאַקטירן געצייג.',
	'optin-survey-answer-changes-upload' => 'פֿאַרבעסערן אינהאַלט און מעדיע אַרויפֿלאָדן.',
	'optin-survey-answer-changes-custom' => 'דערלויבן גרעסערע באַניצער צופאַסונגען.',
	'optin-survey-answer-changes-other' => 'אנדער:',
	'optin-improvements2' => '== וואָס האט מען פֿאַרבעסערט? ==
[[File:UsabilityToolbar.png|left|link=|עקראַנבליד פון דעם פֿאַרברייטערטן רעדאַקטירן געצייגפאַס]]
[[File:UsabilityDialogs.png|left|link=|עקראַנבליד פון דעם נײַעם דיאַלאג קעסטל פֿאַר שאַפֿן אינהאָלט]]
; פֿאַרבייטערטער געצייגפאַס
מיטן פֿאַרבייטערטן געצייגפאַס איז גרינגער צו רעדאַקטירן אַרטיקלען.
נײַע און פֿאַרבעסערטע סימבאלן ווײַזן דײַטלעך די אַקציע פֿון יעדן סימבאל.
פֿאַרברייטבאַרע טיילן  רעדוצירן אָנגעלאַף און האַלטן די ווייניג געצוצטע געצייג ווײַט איין קליק.
;דיאַלאג
ווען איר שאַפֿט לינקען אדער טאַבעלעס, טוט קליקן אויף א סימבאל עפֿענען א דיאַלאג קעסטל וואָס העלפֿט שאַפֿן לינקען און טאַבעלעס מיט אָנפֿילן אן איינפֿאכער פֿארעם.
;און נאך
מיר האבן ארײַנגעלייגט נאך פֿאַרבעסערונגען למשל פֿאַרברייטערטע נאַוויגאַציע, גראַדירטע טאַבן, פֿאַרבעסערטן זוך, און נאך.
== מער אינפֿארמאַציע ==
צו דערגיין מער וועגן דעם ניצלעכקייט איניציאַאטיוו, באַזוכט אונזער [http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative היימבלאַט].
<div style="clear:both"></div>',
);

/** Yoruba (Yorùbá)
 * @author Demmy
 */
$messages['yo'] = array(
	'optin' => 'Ìkópa Ìṣeémúlò Abẹ̀rẹ̀fúnraẹni',
	'optin-desc' => 'Ẹ gba àwọn oníṣe láyè láti ṣèdánwò Beta, àwọn ìmúdára tuntun Ìṣeémúlò Abẹ̀rẹ̀fúnraẹni.',
	'optin-title-optedout' => 'Ẹ ṣèdánwò Beta',
	'optin-title-optedin' => 'Ẹ kúrò ní Beta',
	'optin-title-justoptedin' => 'Ẹ káábọ̀ sí Beta',
	'optin-title-justoptedout' => 'A dúpẹ́ lọ́wọ́ yín pé ẹ ṣèdánwò Beta wa',
	'optin-title-feedback' => 'Ìdáhùn sí Beta',
	'optin-needlogin' => 'Ẹ ní láti [$1 wọlé] láti dán Beta wò.',
	'optin-intro' => 'Ìṣeémúlò Abẹ̀rẹ̀fúnraẹni Wikipedia tí únṣiṣé kíkankíkan láti ṣèdára ìṣeémúlò ti Wikipedia.
Ṣé ẹ fẹ́ ṣèdánwò Beta wa?',
	'optin-feedback-intro' => 'A dúpẹ́ lọ́wọ́ yín pé ẹ ṣèdánwò Beta.
A ó fẹ́ mọ èrò yín nípa ìwojú wa tuntun, bíi bẹ́ẹ̀ yíò wù wá tí ẹ bá le dáhùn àwọn ìbérè àipọndandan ìsàlẹ̀ yí.',
	'optin-feedback-back' => 'Tí ẹ bá fẹ́ dáhùn àwọn ìbérè náà, ẹ le padà sí $1.',
	'optin-success-in' => 'Ẹ úndán Beta wò lọ́wọ́lọ́wọ́.
Ẹ le kúrò níbẹ̀ nígbàkúgbà nípa títẹ ìjápọ̀ "{{int:optin-leave}}" lórí ojúewé ọ̀hún.',
	'optin-leave-cancel' => 'Tí ẹ bá fẹ́ tẹ̀síwájú sí ní lo Beta, ẹ lè padà sí $1.',
	'optin-success-out' => 'Ẹ ti fi Beta sílẹ̀ níyọrísírere.
Ẹ lè tún dánwò nígbàkúgbà nípa títẹ ìjápọ̀ "{{int:optin-try}}" lórí ojúewé náà.',
	'optin-success-feedback' => 'A dúpẹ́ lọ́wọ́ yín fún ìdáhùn yín!',
	'optin-accept-short' => 'Ẹ jẹ́ ká ṣe é!',
	'optin-accept-long' => 'Ẹ dán Beta wò',
	'optin-accept-long-anon' => 'Ẹ wọlé láti ṣèdánwò Beta',
	'optin-deny-short' => 'Módúpẹ́ sùgbọ́n rárá',
	'optin-deny-long' => 'Ẹ padà sí ojúewé tókọjá',
	'optin-submit-out' => 'Ẹ kúrò ní Beta',
	'optin-submit-feedback' => 'Ẹ ṣèdáhùn',
	'optin-survey-yes' => 'Bẹ́ẹ̀ni',
	'optin-survey-no' => 'Bẹ́ẹ̀kọ́',
	'optin-survey-question-likedislike' => 'Kíni ẹ fẹ́ràn nípa Beta? Kíni ẹ kò fẹ́ràn nípa Beta?',
	'optin-survey-question-whyoptout' => 'Kílódé tí ẹ fi únfi Beta sílẹ̀? (Ẹ jọ̀wọ́ ẹ mú ìdáhùn tó yẹ.)',
	'optin-survey-answer-whyoptout-hard' => 'Ó ṣòro gan láti lòó.',
	'optin-survey-answer-whyoptout-didntwork' => 'Kó ṣiṣẹ́ dáadáa.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Kò ṣiṣẹ́ bóṣe yẹ.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Un kò fẹ́ràn bó ṣe rí.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Un kò fẹ́ràn pẹpẹ irinṣẹ́ tuntun náà.',
	'optin-survey-answer-whyoptout-other' => 'Ìdíẹ̀ míràn:',
	'optin-survey-question-browser' => 'Agbétakùn wo ni ẹ únlò?',
	'optin-survey-answer-browser-other' => 'Agbétakùn míràn:',
	'optin-survey-question-os' => 'Sístẹ̀mù ìṣiṣẹ́ kọ̀mpútà wo ni ẹ únlò?',
	'optin-survey-answer-os-other' => 'Sístẹ̀mù ìṣiṣẹ́ kọ̀mpútà mìràn:',
	'optin-survey-question-res' => 'Kíni iye ìgbéhàn ẹ̀rọ ojúìran yín?',
	'optin-survey-answer-changes-other' => 'Òmíràn:',
);

/** Cantonese (粵語)
 * @author Horacewai2
 * @author Shinjiman
 */
$messages['yue'] = array(
	'optin' => '可用性倡議選入',
	'optin-desc' => '容許用戶選擇性去加入可用性倡議嘅可用性加強',
	'optin-title-optedout' => '試吓測試版',
	'optin-title-optedin' => '離開測試版',
	'optin-title-justoptedin' => '歡迎來到測試版',
	'optin-title-justoptedout' => '多謝你去試用測試版',
	'optin-title-feedback' => '測試版反饋',
	'optin-needlogin' => '你需要[$1 登入]去試吓測試版。',
	'optin-intro' => '維基百科可用性小組開發咗一個新皮同埋一個新編輯工具去加強維基百科嘅可用性。
你係咪想試吓我哋嘅測試版？',
	'optin-feedback-intro' => '多謝去試用個測試版。
我哋想知道你對我哋嘅新界面有啲咩睇法，如果你完成下面可選嘅調查，我哋會好樂意接受。',
	'optin-feedback-back' => '如果你唔想完成下面嘅調查，你可以返去$1。',
	'optin-success-in' => '你已經成功噉選入到可用性小組嘅可用性加強。
你可以響任何時間撳上面嘅"{{int:optin-leave}}"去選出番去。',
	'optin-leave-cancel' => '如果你繼續去用測試版，你可以返去$1。',
	'optin-success-out' => '你已經成功噉選出到可用性小組嘅可用性加強。
你可以響任何時間撳上面嘅"{{int:optin-try}}"去選入番去。',
	'optin-success-feedback' => '多謝你嘅反饋！',
	'optin-accept-short' => '而家就去做！',
	'optin-accept-long' => '試吓我哋嘅測試版',
	'optin-accept-long-anon' => '登入試吓測試版',
	'optin-deny-short' => '唔使喇，多謝',
	'optin-deny-long' => '返去上一版',
	'optin-submit-out' => '離開測試版',
	'optin-submit-feedback' => '畀意見',
	'optin-survey-yes' => '係',
	'optin-survey-no' => '唔係',
	'optin-survey-intro' => '多謝你去試用可用性小組嘅可用性加強。
你可以響任何時間響呢版上面撳"{{int:optin-leave}}"去選出。

我哋想知道你對呢個新界面嘅諗法，因此我哋會好高興如果你撳"[[#leave|{{int:optin-submit-out}}]]"之前完成一個可選嘅調查。',
	'optin-survey-question-likedislike' => '你對測試版有啲咩鍾意？有啲咩唔鍾意？',
	'optin-survey-question-whyoptout' => '點解你會選出呢個界面？（揀全部可以用到嘅）',
	'optin-survey-answer-whyoptout-hard' => '太難去用。',
	'optin-survey-answer-whyoptout-didntwork' => '唔能夠正確噉操作。',
	'optin-survey-answer-whyoptout-notpredictable' => '唔能夠做到預期嘅嘢。',
	'optin-survey-answer-whyoptout-didntlike-look' => '我唔鍾意佢嘅外觀。',
	'optin-survey-answer-whyoptout-didntlike-layout' => '我唔鍾意個新標籤同埋排版。',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => '我唔鍾意條新工具列。',
	'optin-survey-answer-whyoptout-other' => '其它原因：',
	'optin-survey-question-explain' => '請解釋或詳述你上面嘅答案：',
	'optin-survey-question-techfail' => '你響用緊測試版嗰陣，有冇曾經遇過任何嘅技術問題？',
	'optin-survey-question-techfail-ifyes' => '如果有嘅話，請詳述或者解釋：',
	'optin-survey-question-usedtoolbar' => '你有冇用過新可擴展嘅工具列去編輯一版？',
	'optin-survey-question-usedtoolbar-ifyes' => '如果有嘅，你鍾意啲乜？你又唔鍾意啲乜？',
	'optin-survey-question-usedtoolbar-ifno' => '如果冇嘅，點解唔用？會唔會響將來去用？請解釋一下。',
	'optin-survey-question-different' => '你響用緊測試版嗰陣，我哋做咗啲乜嘢改變去令到你持續去用佢？',
	'optin-survey-question-feedback' => '請畀我哋知道你嘅反饋、睇法、同埋諗法 - 全部嘅反饋都係有用嘅！：',
	'optin-survey-question-browser' => '你用邊隻瀏覽器？',
	'optin-survey-answer-browser-other' => '其它瀏覽器：',
	'optin-survey-question-os' => '你用邊套操作系統？',
	'optin-survey-answer-os-other' => '其它操作系統：',
	'optin-survey-question-res' => '你個螢光幕嘅解像度有幾大？',
	'optin-survey-question-changes' => '你想響未來，期望對測試版有啲乜嘢改變（揀全部可以用到嘅）？',
	'optin-survey-answer-changes-nav' => '整到網站嘅整體導航更加容易使用。',
	'optin-survey-answer-changes-edittools' => '增加同加強編輯工具。',
	'optin-survey-answer-changes-upload' => '改善內容同媒體上載。',
	'optin-survey-answer-changes-richtext' => '開發同廣文字編輯器相似嘅編輯工具。',
	'optin-survey-answer-changes-lookfeel' => '更新網站嘅『樣同感』。',
	'optin-survey-answer-changes-predictability' => '改善效率預測。',
	'optin-survey-answer-changes-custom' => '容許更加有彈性嘅用戶自定。',
	'optin-survey-answer-changes-other' => '其它：',
	'optin-improvements2' => '==有咩改善？ ==
[[File:UsabilityToolbar.png|left|link=|新編輯工具列]][[File:UsabilityDialogs.png|left|link=|對話內容截圖]]
;增強嘅工具欄
增強工具欄上可以更容易去編輯文章。
新嘅同改進嘅圖標令每個工具更清楚做緊咩。
膨脹節減少混亂，同時保持不經常使用嘅工具只係一個點擊就可以。
;對話框 (創建鏈接或表格)
點擊工具圖標打開對話框，幫助您創建鏈接同表格，只需填寫一份簡單嘅表格。
;其他
例如增強嘅導航，級聯標籤，改進嘅搜索，同埋更多。
==點參與==
如果要了解更多關於可用性倡議，請訪問我們嘅[http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative 網頁]。 <div style="clear:both"></div>',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Bencmq
 * @author Liangent
 * @author PhiLiP
 * @author Shinjiman
 */
$messages['zh-hans'] = array(
	'optin' => '启用Usability Initiative',
	'optin-desc' => '允许用户使用Usability Initiative测试版提供的最新的可用性增强功能',
	'optin-title-optedout' => '试用测试版',
	'optin-title-optedin' => '离开测试版',
	'optin-title-justoptedin' => '欢迎使用测试版',
	'optin-title-justoptedout' => '感谢您试用我们的测试版',
	'optin-title-feedback' => '测试版反馈',
	'optin-needlogin' => '你需要[$1 登录]才能试用测试版。',
	'optin-intro' => '维基百科可用性小组正在努力改善维基百科的可用性。你想尝试下我们的测试版吗？',
	'optin-feedback-intro' => '感谢您试用测试版。
我们希望了解到您对我们新界面的意见，望阁下不吝笔墨填写下方的调查表。',
	'optin-feedback-back' => '如果您不愿意填写调查表，您可以返回$1。',
	'optin-success-in' => '您现在正在试用测试版。
您随时都可点击屏幕顶部的“{{int:optin-leave}}”返回。',
	'optin-leave-cancel' => '如果您想继续使用测试版，您可以返回$1。',
	'optin-success-out' => '您已成功退出测试版。
您随时都可点击屏幕右上角的“{{int:optin-try}}”再次试用。',
	'optin-success-feedback' => '多谢你的反馈！',
	'optin-accept-short' => '现在就用！',
	'optin-accept-long' => '试用测试版',
	'optin-accept-long-anon' => '登入试用测试版',
	'optin-deny-short' => '不用了，多谢',
	'optin-deny-long' => '回到上一页',
	'optin-submit-out' => '离开测试版',
	'optin-submit-feedback' => '反馈意见',
	'optin-survey-yes' => '是',
	'optin-survey-no' => '否',
	'optin-survey-intro' => '感谢您试用测试版。您可以随时点击上面的“{{int:optin-leave}}”退出。

我们希望了解到您对我们新界面的意见，希望您在单击“[[#leave|{{int:optin-submit-out}}]]”前不吝笔墨填写下方的调查表。',
	'optin-survey-question-likedislike' => '您对测试版有什么喜欢？有什么不喜欢？',
	'optin-survey-question-whyoptout' => '为什么您会退出测试版？（选全部应用到的）',
	'optin-survey-answer-whyoptout-hard' => '难以使用。',
	'optin-survey-answer-whyoptout-didntwork' => '运行不正常。',
	'optin-survey-answer-whyoptout-notpredictable' => '不能做到预期的事。',
	'optin-survey-answer-whyoptout-didntlike-look' => '我不喜欢它的外观。',
	'optin-survey-answer-whyoptout-didntlike-layout' => '我不喜欢它的新标签以及排版。',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => '我不喜欢它的新工具栏。',
	'optin-survey-answer-whyoptout-other' => '其它原因：',
	'optin-survey-question-explain' => '请解释或详述您以上的答案：',
	'optin-survey-question-techfail' => '您在使用测试版时，有没有遇到过什么技术问题？',
	'optin-survey-question-techfail-ifyes' => '如果有的话，请详述或者解释：',
	'optin-survey-question-usedtoolbar' => '您有没有使用新的可扩展工具栏去编辑页面？',
	'optin-survey-question-usedtoolbar-ifyes' => '如果有的话，您喜欢什么？您又不喜欢什么？',
	'optin-survey-question-usedtoolbar-ifno' => '如果没有的话，为什么不去用？会否在将来去用？请解释一下。',
	'optin-survey-question-different' => '我们需要做出什么改变来让你继续使用测试版？',
	'optin-survey-question-feedback' => '如果您对我们还有其他意见，也请让我们知晓——所有反馈都很有用处！',
	'optin-survey-question-browser' => '您使用的浏览器是？',
	'optin-survey-answer-browser-other' => '其它浏览器：',
	'optin-survey-question-os' => '您使用的操作系统是？',
	'optin-survey-answer-os-other' => '其他操作系统：',
	'optin-survey-question-res' => '您的屏幕分辨率有多大？',
	'optin-survey-question-changes' => '您想在未来里看到测试版作出什么改变（多选）？',
	'optin-survey-answer-changes-nav' => '让网站的整体导航更加易用。',
	'optin-survey-answer-changes-edittools' => '增加和加强编辑工具。',
	'optin-survey-answer-changes-upload' => '改善内容和媒体上传。',
	'optin-survey-answer-changes-richtext' => '开发类似于富文本编辑器的编辑工具。',
	'optin-survey-answer-changes-lookfeel' => '更新网站的“外观与感受”。',
	'optin-survey-answer-changes-predictability' => '改善效率预测。',
	'optin-survey-answer-changes-custom' => '允许更加有弹性的用户自定义。',
	'optin-survey-answer-changes-other' => '其他：',
	'optin-improvements2' => '==有什么改善？ ==
[[File:UsabilityToolbar.png|left|link=|新编辑工具列]][[File:UsabilityDialogs.png|left|link=|对话内容截图]]
;增强的工具栏
增强工具栏上可以更容易地编辑文章。
新的和改进的图标使每个工具更清楚什么样的行动执行。
膨胀节减少混乱，同时保持不经常使用的工具只是一个点击即可。
;对话框在创建链接或表格
点击工具图标现在打开对话框，帮助您创建链接和表格填写一份简单的表格。
;其他
更多我们采取了一些其他的改进，如增强的导航，级联标签，改进的搜索，以及更多。
==如何参与==
若要了解更多关于可用性倡议，请访问我们的[http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative 网页]。 <div style="clear:both"></div>',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Gaoxuewei
 * @author Horacewai2
 * @author Mark85296341
 * @author Shinjiman
 */
$messages['zh-hant'] = array(
	'optin' => '可用性倡議加入',
	'optin-desc' => '允許用戶使用Usability Initiative測試版提供的最新的可用性增強功能',
	'optin-title-optedout' => '試用測試版',
	'optin-title-optedin' => '離開測試版',
	'optin-title-justoptedin' => '歡迎來到測試版',
	'optin-title-justoptedout' => '多謝你去試用測試版',
	'optin-title-feedback' => '測試版反饋',
	'optin-needlogin' => '你需要[$1 登入]去試用測試版。',
	'optin-intro' => '維基百科可用性小組開發了一個新皮膚和一個新的編輯工具去加強維基百科的可用性。
您是否想試用我們的測試版？',
	'optin-feedback-intro' => '多謝去試用這個測試版。
我們想知道你對我們的新界面有何看法，如果你完成下面可選的調查，我們會好樂意接受。',
	'optin-feedback-back' => '如果你不想完成下面嘅調查，你可以回到$1。',
	'optin-success-in' => '您已經成功地加入到可用性小組的可用性增強。
您可以隨時點擊上面的"{{int:ooptin-leave}}"去退出。',
	'optin-leave-cancel' => '如果您想繼續使用測試版，您可以返回$1',
	'optin-success-out' => '您已經成功地退出自可用性小組的可用性增強。
您可以隨時點擊上面的"{{int:optin-try}}"去加入。',
	'optin-success-feedback' => '多謝你的反饋！',
	'optin-accept-short' => '現在就去做！',
	'optin-accept-long' => '試試我們的測試版',
	'optin-accept-long-anon' => '登入試用測試版',
	'optin-deny-short' => '不用了，多謝',
	'optin-deny-long' => '回到上一頁',
	'optin-submit-out' => '離開測試版',
	'optin-submit-feedback' => '給意見',
	'optin-survey-yes' => '是',
	'optin-survey-no' => '否',
	'optin-survey-intro' => '多謝您去試用可用性小組的可用性增強。
您可以隨時點擊上面的"{{int:optin-leave}}"去退出。

我們想知道您對這個新界面的想法，因此我們會好高興如果您在點擊"[[#leave|{{int:optin-submit-out}}]]"之前完成一個可選的調查。',
	'optin-survey-question-likedislike' => '您對測試版有什麼喜歡？有什麼不喜歡？',
	'optin-survey-question-whyoptout' => '為什麼您會退出這個界面？（選全部應用到的）',
	'optin-survey-answer-whyoptout-hard' => '太難去使用。',
	'optin-survey-answer-whyoptout-didntwork' => '不能正確地操作。',
	'optin-survey-answer-whyoptout-notpredictable' => '不能做到預期的事。',
	'optin-survey-answer-whyoptout-didntlike-look' => '我不喜歡它的外觀。',
	'optin-survey-answer-whyoptout-didntlike-layout' => '我不喜歡它的新標籤以及排版。',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => '我不喜歡它的新工具列。',
	'optin-survey-answer-whyoptout-other' => '其它原因：',
	'optin-survey-question-explain' => '請解釋或詳述您以上之答案：',
	'optin-survey-question-techfail' => '您在使用測試版時，有沒有曾經遇過任何的技術問題？',
	'optin-survey-question-techfail-ifyes' => '如果有的話，請詳述或者解釋：',
	'optin-survey-question-usedtoolbar' => '您有沒有用過新可擴展的工具列去編輯一個頁面？',
	'optin-survey-question-usedtoolbar-ifyes' => '如果有的話，您喜歡什麼？您又不喜歡什麼？',
	'optin-survey-question-usedtoolbar-ifno' => '如果沒有的話，為什麼不去用？會否在將來去用？請解釋一下。',
	'optin-survey-question-different' => '您在用測試版時，我們做了什麼事改變去令到你持續使用它？',
	'optin-survey-question-feedback' => '請給我們知道你的反饋、看法、以及想法 - 全部的反饋都是有用的！：',
	'optin-survey-question-browser' => '您用哪款瀏覽器？',
	'optin-survey-answer-browser-other' => '其它瀏覽器：',
	'optin-survey-question-os' => '您用哪套操作系統？',
	'optin-survey-answer-os-other' => '其它操作系統：',
	'optin-survey-question-res' => '您的屏幕解像度之大小有多大？',
	'optin-survey-question-changes' => '您想在未來中，期望對測試版有什麼改變（選全部應用到的）？',
	'optin-survey-answer-changes-nav' => '令網站的整體導航更加容易使用。',
	'optin-survey-answer-changes-edittools' => '增加和加強編輯工具。',
	'optin-survey-answer-changes-upload' => '改善內容和媒體上載。',
	'optin-survey-answer-changes-richtext' => '開發與廣文字編輯器相似的編輯工具。',
	'optin-survey-answer-changes-lookfeel' => '更新網站的『樣式與感受』。',
	'optin-survey-answer-changes-predictability' => '改善效率預測。',
	'optin-survey-answer-changes-custom' => '容許更加有彈性的用戶自定。',
	'optin-survey-answer-changes-other' => '其它：',
	'optin-improvements2' => '==有什麼改善？ ==
[[File:UsabilityToolbar.png|left|link=|新編輯工具列]][[File:UsabilityDialogs.png|left|link=|對話內容截圖]]
;增強的工具欄
增強工具欄上可以更容易地編輯文章。
新的和改進的圖標使每個工具更清楚什麼樣的行動執行。
膨脹節減少混亂，同時保持不經常使用的工具只是一個點擊即可。
;對話框在創建鏈接或表格
點擊工具圖標現在打開對話框，幫助您創建鏈接和表格填寫一份簡單的表格。
;其他
更多我們採取了一些其他的改進，如增強的導航，級聯標籤，改進的搜索，以及更多。
==如何參與==
若要了解更多關於可用性倡議，請訪問我們的[http://usability.wikimedia.org/wiki/Wikipedia_Usability_Initiative 網頁]。 <div style="clear:both"></div>',
);

/** Chinese (Taiwan) (‪中文(台灣)‬)
 * @author Mark85296341
 */
$messages['zh-tw'] = array(
	'optin-survey-question-os' => '您用哪套作業系統？',
	'optin-survey-question-res' => '您的螢幕解析度之大小有多大？',
	'optin-survey-answer-changes-custom' => '容許更加有彈性的使用者自定。',
);

