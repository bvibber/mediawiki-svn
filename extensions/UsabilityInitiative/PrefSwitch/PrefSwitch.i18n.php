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
	'prefswitch-survey-question-like' => 'What did you like about the new features?',
	'prefswitch-survey-question-dislike' => 'What did you dislike about the features?',
	'prefswitch-survey-question-whyoff' => 'Why are you turning off the new features?
Please select all that apply.',
	'prefswitch-survey-answer-whyoff-hard' => 'The features were too hard to use.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'The features did not function properly.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'The features did not perform predictably.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'I did not like the way the features looked.',
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
	'prefswitch-title-on' => 'New features',
	'prefswitch-title-switched-on' => 'Enjoy!',
	'prefswitch-title-off' => 'Turn new features off',
	'prefswitch-title-switched-off' => 'Thanks',
	'prefswitch-title-feedback' => 'Feedback',
	'prefswitch-success-on' => 'New features are now turned on. We hope you enjoy using the new features. You may always turn them back off by clicking on the "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" link at the top of the page.',
	'prefswitch-success-off' => 'New features are now turned off. Thanks for trying the new features. You may always turn them back on by clicking on the "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" link at the top of the page.',
	'prefswitch-success-feedback' => 'Your feedback has been sent.',
	'prefswitch-return' => '<hr style="clear:both">
Return to <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| A screenshot of Wikipedia's new navigation interface <small>[[Media:VectorNavigation-en.png|(enlarge)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| A screenshot of the basic page editing interface <small>[[Media:VectorEditorBasic-en.png|(enlarge)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| A screenshot of the new dialog box for entering links
|}
|}
The Wikimedia Foundation's User Experience Team has been working with volunteers from the community to make things easier for you. We are excited to share some improvements, including a new look and feel and simplified editing features. These changes are intended to make it easier for new contributors to get started, and are based on our [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study usability testing conducted over the last year]. Improving the usability of our projects is a priority of the Wikimedia Foundation and we will be sharing more updates in the future. For more details, visit the related Wikimedia [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia blog post].

=== Here's what we have changed ===
* '''Navigation:''' We have improved the navigation for reading and editing pages. Now, the tabs at the top of each page more clearly define whether you are viewing the page or discussion page, and whether you are reading or editing a page.
* '''Editing toolbar improvements:''' We have reorganized the editing toolbar to make it easier to use. Now, formatting pages is simpler and more intuitive.
* '''Link wizard:''' An easy-to-use tool allows you to add links to other wiki pages as well as links to external sites.
* '''Search improvements:''' We have improved search suggestions to get you to the page you are looking for more quickly.
* '''Other new features:''' We have also introduced a table wizard to make creating tables easier and a find and replace feature to simplify page editing.
* '''Wikipedia logo:''' We have updated our logo. Read more at the [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d Wikimedia blog].

===Feedback?===
We would love to hear from you. Please visit our [[$1|feedback page]] or, if you are interested in our ongoing efforts to improve the software, visit our [http://usability.wikimedia.org usability wiki] for more information.",
	'prefswitch-main-anon' => "===Take me back===
[$1 Click here to turn off the new features]. You will be asked to login or create an account first.",
	'prefswitch-main-on' => "===Take me back!===
[$2 Click here to turn off the new features].",
	'prefswitch-main-off' => "===Try them out!===
[$1 Click here to enable the new features].",
	'prefswitch-survey-intro-feedback' => 'We would love to hear from you.
Please fill out the optional survey below before clicking "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Thanks for trying out our new features.
To help us improve them, please fill out the optional survey below before clicking "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Project:User experience feedback',
);

/** Message documentation (Message documentation)
 * @author EugeneZelenko
 * @author Hamilton Abreu
 * @author Lloffiwr
 * @author McDutchie
 * @author Mormegil
 * @author Siebrand
 * @author Umherirrender
 */
$messages['qqq'] = array(
	'prefswitch-desc' => '{{desc}}',
	'prefswitch-survey-true' => 'Used in a form where it is a radio button label from the PrefSwitch questionnaire module to answer Yes or No to a question ([http://commons.wikimedia.org/w/index.php?title=Special:UsabilityInitiativePrefSwitch&mode=feedback example]).

It is not in used at the referred page, as of 16 May 2010.

{{Identical|Yes}}',
	'prefswitch-survey-false' => 'Used in a form where it is a radio button label from the PrefSwitch questionnaire module to answer Yes or No to a question ([http://commons.wikimedia.org/w/index.php?title=Special:UsabilityInitiativePrefSwitch&mode=feedback example]).

It is not in used at the referred page, as of 16 May 2010.

{{Identical|No}}',
	'prefswitch-survey-cancel-feedback' => '$1 is a link to the main page of the wiki in question.',
	'prefswitch-survey-answer-whyoff-hard' => 'Should be generic enough to be used as an option for questions:
*{{msg-mw|Optin-survey-question-whyoptout}}
*{{msg-mw|Prefswitch-survey-question-whyoff}}.',
	'prefswitch-survey-answer-whyoff-didntwork' => '{{MediaWiki:Prefswitch-survey-answer-whyoff-hard/qqq}}',
	'prefswitch-survey-answer-whyoff-notpredictable' => '{{MediaWiki:Prefswitch-survey-answer-whyoff-hard/qqq}}',
	'prefswitch-survey-answer-whyoff-other' => '{{Identical|Other reason}}',
	'prefswitch-feedbackpage' => '{{doc-important|The name of the user experience feedback page on this wiki. Should only be translated for ja, es, de, fr, it, ru, pl, pt, nl for now. Do not translate "Project:"}}',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'prefswitch-survey-true' => 'Ja',
	'prefswitch-survey-false' => 'Nee',
	'prefswitch-survey-answer-whyoff-other' => 'Ander rede:',
);

/** Gheg Albanian (Gegë)
 * @author Mdupont
 */
$messages['aln'] = array(
	'prefswitch' => 'Përdorshmërisë kaloni Nisma preferencë',
	'prefswitch-desc' => 'Lejo përdoruesit të kaloni grupe të preferencave',
	'prefswitch-survey-true' => 'Po',
	'prefswitch-survey-false' => 'Jo',
	'prefswitch-survey-submit-off' => 'Turn off karakteristika të reja',
	'prefswitch-survey-cancel-off' => 'Nëse dëshironi të vazhdoni përdorimin e tipare të reja, ju mund të ktheheni tek $1.',
	'prefswitch-survey-submit-feedback' => 'Send feedback',
	'prefswitch-survey-cancel-feedback' => 'Nëse ju nuk dëshironi të japin mendimet, ju mund të ktheheni tek $1.',
	'prefswitch-survey-question-like' => 'Çfarë ju pëlqen në lidhje me tipare të reja?',
	'prefswitch-survey-question-dislike' => 'Çfarë keni antipati për tiparet?',
	'prefswitch-survey-question-whyoff' => 'Pse jeni kthyer tek pjesa e tipare të reja? Ju lutemi zgjidhni të gjitha që aplikohet.',
	'prefswitch-survey-answer-whyoff-hard' => "Karakteristika ishin tepër të vështirë për t'u përdorur.",
	'prefswitch-survey-answer-whyoff-didntwork' => 'Karakteristika nuk funksionojnë siç duhet.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Karakteristika nuk ka kryer parashikueshme.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Unë nuk e pëlqen mënyra tiparet e shikuar.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Unë nuk e kam si skedat e reja dhe layout.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Unë nuk e kam si toolbar re.',
	'prefswitch-survey-answer-whyoff-other' => 'arsye të tjera:',
	'prefswitch-survey-question-browser' => 'Cili browser do you use?',
	'prefswitch-survey-answer-browser-other' => 'browser tjera:',
	'prefswitch-survey-question-os' => 'Cili sistem operativ do you use?',
	'prefswitch-survey-answer-os-other' => 'sistemit të tjera operative:',
	'prefswitch-survey-question-res' => 'Cila është zgjidhja e ekranit tuaj?',
	'prefswitch-title-on' => 'Tipare të reja',
	'prefswitch-title-switched-on' => 'Enjoy!',
	'prefswitch-title-off' => 'Turn off karakteristika të reja',
	'prefswitch-title-switched-off' => 'Falënderim',
	'prefswitch-title-feedback' => 'Reagim',
	'prefswitch-success-on' => 'Tipare të reja janë kthyer tani në. Ne shpresojmë që të gëzojnë duke përdorur tipare të reja. Ju mund gjithmonë të kthehet prapa atyre jashtë duke klikuar mbi "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" në krye të faqes.',
	'prefswitch-success-off' => 'Tipare të reja janë kthyer tani off. Faleminderit për përpjekjen tipare të reja. Ju mund gjithmonë të kthehet përsëri në ato duke klikuar mbi "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" në krye të faqes.',
	'prefswitch-success-feedback' => 'Your feedback është dërguar.',
	'prefswitch-return' => '<hr style="clear:both"> Kthehuni tek <span class="plainlinks">[$1 $2].</span>',
	'prefswitch-main' => "Ne kemi punuar shumë për të bërë gjërat më të lehtë për përdoruesit tanë. Ne jemi të ngazëllyer për të ndarë disa përmirësime, duke përfshirë një vështrim të ri dhe të ndjehen dhe të thjeshtuar karakteristika redaktimi. Përmirësimi i përdorshmërisë e projekteve tona është një përparësi e Fondacionit Wikimedia dhe ne do të jetë ndarja rejat më shumë në të ardhmen. Për më shumë detaje, vizitoni lidhur [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ Wikimedia blog] post.[[File:UsabilityNavigation.png|right|link=|Screenshot i navigacion ri]][[File:UsabilityToolbar.png|right|link=|pamjen e zgjeruar toolbar redakto]][[File:UsabilityDialogs.png|right|link=|pamjen e përmbajtjes dialogs brezi i ri]]=== Ja se çfarë ne kemi ndryshuar ==='''Navigation''': Ne kemi përmirësuar navigacion për lexim dhe të redaktoni. Tani, në skedat në krye të secilës faqe më të përcaktojë qartë nëse ju jeni duke shfletuar faqe apo faqe diskutimi, dhe nëse ju jeni duke lexuar ose redaktoni një faqe.*'''Redaktimi përmirësime toolbar''': Ne kemi riorganizuar toolbar editing për të bërë më të lehtë për t'u përdorur. Tani, formatimit faqe është e thjeshtë dhe më i kuptueshëm.",
	'prefswitch-main-anon' => '=== Merrni Me Kthehu ===
Nëse dëshironi të fikur tipare të reja, [$1 klikoni këtu]. Ju do të pyeteni për të identifikoheni ose krijoni një llogari të parë.',
	'prefswitch-main-on' => '=== Çoni përsëri! ===
Nëse dëshironi të fikur tipare të reja, ju lutem [$2 klikoni këtu].',
	'prefswitch-main-off' => '=== Provo ato! ===
Nëse ju dëshironi të kthehet në tipare të reja, ju lutem [$1 klikoni këtu].',
	'prefswitch-survey-intro-feedback' => 'Ne do të duan të dëgjojmë nga ju. Ju lutem plotësoni këtë anketë opsional më poshtë para se të klikoni "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Faleminderit për përpjekje të reja nga karakteristikat tona. Për të na ndihmuar në përmirësimin e tyre, ju lutemi plotësoni këtë anketë opsional më poshtë para se të klikoni "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
);

/** Aragonese (Aragonés)
 * @author Juanpabl
 */
$messages['an'] = array(
	'prefswitch-survey-true' => 'Sí',
	'prefswitch-survey-false' => 'No',
);

/** Arabic (العربية)
 * @author Meno25
 * @author OsamaK
 */
$messages['ar'] = array(
	'prefswitch' => 'مُبدّل تفضيلات مبادرة الاستخدامية',
	'prefswitch-desc' => 'اسمح للمستخدمين بتبديل أجزاء من تفضيلاتهم',
	'prefswitch-survey-true' => 'نعم',
	'prefswitch-survey-false' => 'لا',
	'prefswitch-survey-submit-off' => 'عطّل المزايا الجديدة',
	'prefswitch-survey-cancel-off' => 'إذا أردت الاستمرار في استخدام المزايا الجديدة، فإمكانك الرجوع إلى $1.',
	'prefswitch-survey-submit-feedback' => 'أرسل ملاحظات',
	'prefswitch-survey-cancel-feedback' => 'إذا لم ترغب في تقديم الملاحظات، فبإمكانك الرجوع إلى $1.',
	'prefswitch-survey-question-whyoff' => 'لماذا ستعطل المزايا الجديدة؟
من فضلك اختر كل ما يناسب.',
	'prefswitch-survey-answer-whyoff-hard' => 'كان استخدامها صعبًا جدًا.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'لم تؤدِ مهمتها كما ينبغي.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'لم تعمل كما توقّعت.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'لم يعجبني شكلها.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'لم تعجبني الألسنة الجديدة ولا التصميم الجديد.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'لم يعجبني شريط الأدوات الجديد.',
	'prefswitch-survey-answer-whyoff-other' => 'سبب آخر :',
	'prefswitch-survey-question-browser' => 'ما المتصفح الذي تستخدمه؟',
	'prefswitch-survey-answer-browser-other' => 'متصفح آخر:',
	'prefswitch-survey-question-os' => 'ما نظام التشغيل الذي تستخدمه؟',
	'prefswitch-survey-answer-os-other' => 'نظام تشغيل آخر:',
	'prefswitch-survey-question-res' => 'ما أبعاد شاشتك؟',
	'prefswitch-title-on' => 'ما الجديد؟',
	'prefswitch-title-switched-on' => 'استمتع!',
	'prefswitch-title-off' => 'عطّل المزايا الجديدة.',
	'prefswitch-title-switched-off' => 'شكرًا',
	'prefswitch-title-feedback' => 'ملاحظات',
	'prefswitch-success-on' => 'المزايا الجديدة مُفعّلة.',
	'prefswitch-success-off' => 'المزايا الجديد مُعطّلة.',
	'prefswitch-success-feedback' => 'لقد أُرسلت ملاحظاتك.',
	'prefswitch-return' => '<hr style="clear:both">
عُد إلى <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "لقد عملنا بجد على جعل الأمور أسهل لمستخدمينا، وإننا لنسعد بمشاركة بعض التحسينات بما فيها شكل جديد ومزايا تُبسّط التحرير. إن تسهيل استخدامية مشاريع ويكيميديا من أولويات المؤسسة وسوف نشارككم التطورات في المستقبل.

[[File:UsabilityNavigation.png|right|link=|لقطة شاشة للتصفح الجديد]]
[[File:UsabilityToolbar.png|right|link=|لقطة شاشة لشريط أدوات التحرير المُحسّن]]
[[File:UsabilityDialogs.png|right|link=|لقطة شاشة لحوارات توليد المحتويات الجديدة]]
=== أدناه ما غيرنا ===
* '''التصفح:''' لقد حسّنا التصفح عند قراءة وتحرير الصفحات. إن الألسنة الجديدة في أعلى كل صفحة توُضّح بشكل أفضل ما إذا كنت تشاهد الصفحة أو صفحة نقاشها؛ أو تقرؤها أو تحررها.
* '''تحسينات شريط أدوات التحرير:''' لقد أعدنا ترتيب شريط أدوات التحرير ليسهل استخدامه. أصبح تحرير الصفحات الآن أسهل وأوضح.
* '''معالج الوصلات:''' أداة سهلة الاستخدام تمكنك من إضافة وصلات إلى صفحات ويكي أخرى ووصلات إلى مواقع خارجية.
* '''تحسين البحث:''' لقد حسنا اقتراحات البحث لتصل إلى الصفحة التي تبحث عنها بشكل أسرع.
* '''مزايا أخرى جديدة:''' أضفنا أيضًا معالج جداول لتسهيل إنشاء الجداول وميزة بحث واستبدال تُسهّل تحرير الصفحات.",
	'prefswitch-main-on' => '=== أرجعني! ===
إذا أردت تعطيل المزايا الجديدة، [$2 فانقر هنا] من فضلك.',
	'prefswitch-main-off' => '=== جرّبها! ===
إذا أردت تفعيل المزايا الجديدة، [$1 فانقر هنا] من فضلك.',
	'prefswitch-survey-intro-feedback' => 'سوف يستعدنا سماع صوتك.
من فضلك عبّئ الاستبيان الاختياري أدناه.',
	'prefswitch-survey-intro-off' => 'شكرا لك على تجربة المزايا الجديدة.
لتساعدنا في تحسين هذه المزايا، من فضلك عبّئ الاستبيان الاختياري أدناه.',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 * @author Wizardist
 */
$messages['be-tarask'] = array(
	'prefswitch' => 'Пераключальнік установак Ініцыятывы па паляпшэньні зручнасьці і прастаты выкарыстаньня',
	'prefswitch-desc' => 'Дазваляе ўдзельнікам пераключаць наборы ўстановак',
	'prefswitch-survey-true' => 'Так',
	'prefswitch-survey-false' => 'Не',
	'prefswitch-survey-submit-off' => 'Выключыць новыя магчымасьці',
	'prefswitch-survey-cancel-off' => 'Калі Вы жадаеце працягваць выкарыстаньне новых магчымасьцяў, Вы можаце вярнуцца да $1.',
	'prefswitch-survey-submit-feedback' => 'Даслаць водгук',
	'prefswitch-survey-cancel-feedback' => 'Калі Вы не жадаеце дасылаць водгук, Вы можаце вярнуцца да $1.',
	'prefswitch-survey-question-like' => 'Што Вам спадабалася ў новых магчымасьцях?',
	'prefswitch-survey-question-dislike' => 'Што Вам не спадабалася ў магчымасьцях?',
	'prefswitch-survey-question-whyoff' => 'Чаму Вы выключаеце новыя магчымасьці?
Калі ласка, выберыце ўсе пасуючыя варыянты.',
	'prefswitch-survey-answer-whyoff-hard' => 'Занадта складаны ў выкарыстаньні.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Ён не працуе належным чынам.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Ён працуе не як чакалася.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Мне не спадабаўся зьнешні выгляд.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Мне не спадабаліся новыя закладкі і кампаноўка.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Мне не спадабалася новая панэль інструмэнтаў.',
	'prefswitch-survey-answer-whyoff-other' => 'Іншая прычына:',
	'prefswitch-survey-question-browser' => 'Якім браўзэрам Вы карыстаецеся?',
	'prefswitch-survey-answer-browser-other' => 'Іншы браўзэр:',
	'prefswitch-survey-question-os' => 'Якой апэрацыйнай сыстэмай Вы карыстаецеся?',
	'prefswitch-survey-answer-os-other' => 'Іншая апэрацыйная сыстэма:',
	'prefswitch-survey-question-res' => 'Якое разрозьненьне Вашага манітора?',
	'prefswitch-title-on' => 'Новыя магчымасьці',
	'prefswitch-title-switched-on' => 'Цешцеся!',
	'prefswitch-title-off' => 'Выключыць новыя магчымасьці',
	'prefswitch-title-switched-off' => 'Дзякуй',
	'prefswitch-title-feedback' => 'Зваротная сувязь',
	'prefswitch-success-on' => 'Новыя магчымасьці уключаныя. Мы спадзяемся, што Вам спадабаецца карыстацца новымі магчымасьцямі. Вы ў любы момант можаце іх адключыць, націснуўшы «[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]», якая знаходзіцца ўверсе старонкі.',
	'prefswitch-success-off' => 'Новыя магчымасьці выключаныя. Дзякуй, за спробу карыстаньня новымі магчымасьцямі. Вы ў любы момант можаце іх уключыць, націснуўшы «[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]», якая знаходзіцца ўверсе старонкі.',
	'prefswitch-success-feedback' => 'Ваш водгук дасланы.',
	'prefswitch-return' => '<hr style="clear:both">
Вярнуцца да <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "Мы вельмі стараемся, каб спрасьціць працу нашых удзельнікаў. Мы ўвялі некаторыя паляпшэньні, уключаючы новы зьнешні выгляд і спрошчаныя магчымасьці рэдагаваньня. Паляпшэньне зручнасьці і прастаты выкарыстаньня нашымі праектамі зьяўляецца галоўнай мэтай Фундацыі «Вікімэдыя» і мы працягнем над гэтым працаваць. Падрабязнасьці можна паглядзець у [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ блёге фундацыі «Вікімэдыя»].

[[File:UsabilityNavigation.png|right|link=|Выява новай навігацыі]]
[[File:UsabilityToolbar.png|right|link=|Выява палепшанай панэлі рэдагаваньня]]
[[File:UsabilityDialogs.png|right|link=|Выява новых дыялёгаў стварэньня]]
===Што мы зьмянілі===
* '''Навігацыя:''' Мы палепшылі навігацыю для чытаньня і рэдагаваньня старонак. Цяпер закладкі ў верхняй частцы кожнай старонкі дазваляюць дакладней зразумець ці Вы праглядаеце артыкул ці старонку абмеркаваньня, а таксама ці Вы чытаеце ці рэдагуеце старонку.
* '''Паляпшэньні панэлі рэдагаваньня:''' Мы перапрацавалі панэль рэдагаваньня для таго, каб зрабіць яе болей простай у выкарыстаньні.  Цяпер фарматаваньне старонак болей лёгкае і болей інтуітыўнае.
* '''Майстар стварэньня спасылак:''' Лёгкі ў выкарыстаньні інструмэнт дазваляе Вам дадаваць спасылкі на іншыя старонкі {{GRAMMAR:'''родны|{{SITENAME}}}} таксама як і на вонкавыя сайты.
* '''Паляпшэньні пошуку:''' Мы палепшылі пошукавыя падказкі, каб хутчэй паказаць неабходную Вам старонку.
* '''Іншыя магчымасьці:''' Мы таксама ўвялі майстар стварэньня табліцаў для палягчэньня стварэньня табліцаў і магчымасьць пошуку і замены для палягчэньня рэдагаваньня старонак.
* '''Лягатып Вікіпэдыі''': Мы зьмянілі лягатып. Падрабязнасьці глядзіце ў [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ блогу фундацыі «Вікімэдыя»].",
	'prefswitch-main-anon' => '===Вярнуцца===
Калі Вы жадаеце выключыць новыя магчымасьці, [$1 націсьніце тут]. Вас папросяць спачатку увайсьці ў сыстэму альбо стварыць новы рахунак.',
	'prefswitch-main-on' => '===Вярніце ўсё назад!===
[$2 націсьніце тут, каб выключыць новыя магчымасьці].',
	'prefswitch-main-off' => '===Паспрабуйце іх!===
Калі Вы жадаеце ўключыць новыя магчымасьці, калі ласка, [$1 націсьніце тут].',
	'prefswitch-survey-intro-feedback' => 'Мы жадаем даведацца пра Вашыя меркаваньні.
Калі ласка, адкажыце на некалькі пытаньняў ніжэй перад тым як націснуць «[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]».',
	'prefswitch-survey-intro-off' => 'Дзякуй за тое, што паспрабавалі новыя магчымасьці.
Каб дапамагчы нам іх палепшыць, калі ласка, адкажыце на некалькі пытаньняў ніжэй, перад тым як націснуць «[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]».',
	'prefswitch-feedbackpage' => 'Project:Водгукі ўдзельнікаў',
);

/** Bulgarian (Български)
 * @author DCLXVI
 * @author Spiritia
 * @author Stanqo
 */
$messages['bg'] = array(
	'prefswitch-survey-true' => 'Да',
	'prefswitch-survey-false' => 'Не',
	'prefswitch-survey-answer-whyoff-other' => 'Друга причина:',
	'prefswitch-survey-question-browser' => 'Кой браузър използвате?',
	'prefswitch-survey-answer-browser-other' => 'Друг браузър:',
	'prefswitch-survey-question-os' => 'Каква операционна система използвате?',
	'prefswitch-survey-answer-os-other' => 'Друга операционна система:',
	'prefswitch-title-on' => 'Нови функции',
	'prefswitch-title-switched-on' => 'Наслаждавайте се!',
	'prefswitch-title-switched-off' => 'Благодаря',
	'prefswitch-success-off' => 'Новите функции са изключени.',
);

/** Bengali (বাংলা)
 * @author Bellayet
 */
$messages['bn'] = array(
	'prefswitch-survey-true' => 'হ্যাঁ',
	'prefswitch-survey-false' => 'না',
	'prefswitch-survey-submit-off' => 'নতুন বৈশিষ্ট্য বন্ধ করো',
	'prefswitch-survey-submit-feedback' => 'প্রতিক্রিয়া পাঠান',
	'prefswitch-survey-cancel-feedback' => 'আপনি যদি প্রতিক্রিয়া দিতে না চান, তাহলে আপনি $1 -এ ফিরে যেতে পারেন।',
	'prefswitch-survey-question-whyoff' => 'আপনি কেন নতুন বৈশিষ্ট্যাবলী বন্ধ করছেন?
অনুগ্রহ করে যা প্রযোজ্য তা নির্বাচন করুন।',
	'prefswitch-survey-answer-whyoff-hard' => 'এতে কাজ করা কঠিন।',
	'prefswitch-survey-answer-whyoff-didntwork' => 'এটি ঠিক মত কাজ করে না।',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'এটি আন্দাজ মত কাজ করে না।',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'এটা দেখতে যেমন তা আমার পছন্দ নয়।',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'নতুন ট্যাব এবং বিন্যাস আমার পছন্দ হয়নি।',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'নতুন টুলবার আমার পছন্দ হয়নি।',
	'prefswitch-survey-answer-whyoff-other' => 'অন্য কারণ:',
	'prefswitch-survey-question-browser' => 'আপনি কোন ব্রাউজার ব্যবহার করেন?',
	'prefswitch-survey-answer-browser-other' => 'অন্য ব্রাউজার:',
	'prefswitch-survey-question-os' => 'আপনি কোন অপারেটিং সিস্টেম ব্যবহার করেন?',
	'prefswitch-survey-answer-os-other' => 'অন্য অপারেটিং সিস্টেম:',
	'prefswitch-survey-question-res' => 'আপনার পর্দার রেজ্যুলেশন কত?',
	'prefswitch-title-on' => 'নতুন বৈশিষ্ট্যাবলী',
	'prefswitch-title-switched-on' => 'উপভোগ করুন!',
	'prefswitch-title-off' => 'নতুন বৈশিষ্ট্যাবলী বন্ধ করো',
	'prefswitch-title-switched-off' => 'ধন্যবাদ',
	'prefswitch-title-feedback' => 'প্রতিক্রিয়া',
	'prefswitch-success-on' => 'নতুন বৈশিষ্ট্যাবলী চালু করো',
	'prefswitch-success-off' => 'নতুন বৈশিষ্ট্যাবলী বন্ধ করো',
	'prefswitch-success-feedback' => 'আপনার প্রতিক্রিয়া পাঠানো হয়েছে।',
	'prefswitch-return' => '<hr style="clear:both">
ফিরে যান <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main-anon' => '===আমাকে ফিরিয়ে নাও===
আপনি যদি নতুন বৈশিষ্ট্যাবলী বন্ধ করতে চান তাহলে, [$1 এখানে ক্লিক করুন]। এ কাজের প্রথমেই আপনাকে লগ-ইন বা নতুন অ্যাকাউন্ট তৈরি করতে বলা হবে।',
	'prefswitch-main-on' => '===আমাকে ফেরত নাও!===
আপনি নতুন বৈশিষ্ট্যাবলী বন্ধ করতে, অনুগ্রহ করে [$2 এখানে ক্লিক করুন]।',
	'prefswitch-main-off' => '===সেগুলো ব্যবহার করুন!===
আপনি যদি নতুন বৈশিষ্ট্যাবলী চালু করতে চান, অনুগ্রহ করে [$1 এখানে ক্লিক করুন]।',
	'prefswitch-survey-intro-feedback' => 'আমরা আপনার কাছ থেকে জানতে চাই।
"[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]" ক্লিক করার আগে অনুগ্রহ করে নিচের ঐচ্ছিক জরিপ ফরমটি পূরণ করুন।',
);

/** Breton (Brezhoneg)
 * @author Fulup
 * @author Y-M D
 */
$messages['br'] = array(
	'prefswitch' => 'Cheñch dibaboù an intrudu implijadusted',
	'prefswitch-desc' => "Talvezout a ra d'an implijerien da cheñch an holl benndibaboù",
	'prefswitch-survey-true' => 'Ya',
	'prefswitch-survey-false' => 'Nann',
	'prefswitch-survey-submit-off' => 'Implijout ar perzhioù nevez',
	'prefswitch-survey-cancel-off' => "Mar fell deoc'h kenderc'hel d'ober gant ar perzhioù nevez e c'hallit distreiñ da $1.",
	'prefswitch-survey-submit-feedback' => 'Roit ho soñj',
	'prefswitch-survey-cancel-feedback' => "Mar ne fell ket deoc'h reiñ ho soñj e c'hallit distreiñ da $1.",
	'prefswitch-survey-question-like' => "Petra en deus plijet deoc'h en arc'hweladurioù nevez ?",
	'prefswitch-survey-question-dislike' => "Petra n'en deus ket plijet deoc'h en arc'hweladurioù nevez ?",
	'prefswitch-survey-question-whyoff' => 'Perak goulenn paouez gant ar perzhioù nevez ?
Dibabit kement tra hag a zegouezh.',
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
	'prefswitch-title-on' => "Arc'hweladurioù nevez",
	'prefswitch-title-switched-on' => "Plijadur deoc'h !",
	'prefswitch-title-off' => 'Paouez gant ar perzhioù nevez',
	'prefswitch-title-switched-off' => 'Trugarez',
	'prefswitch-title-feedback' => 'Sonjoù',
	'prefswitch-success-on' => "War enaou emañ ar perzhioù nevez bremañ. Emichañs e plijint deoc'h. Mar fell deoc'h e c'hallit tennañ anezho en ur glikañ war al liamm \"[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]\" e laez ar bajenn.",
	'prefswitch-success-off' => 'Paouezet ez eus bet d\'ober gant ar perzhioù nevez. Ho trugarekaat evit bezañ amprouet anezho. Gallout a rit adenaouiñ anezho pa fell deoc\'h en ur glikañ war al liamm "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" e laez ar bajenn.',
	'prefswitch-success-feedback' => "Kaset eo bet hoc'h evezhiadennoù.",
	'prefswitch-return' => '<hr style="clear:both">
Distreiñ da <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "Start hon eus labouret evit aesaat ar jeu d'an implijerien. Stad zo ennomp bremañ o kinnig deoc'h tammoù gwellaennoù, en o zouez un tres nevez ha doareoù kemmañ pajennoù eeunaet. Aesaat implijadusted hor raktresoù zo ur priorite evit Diazezadur Wikimedia ha kenderc'hel a raimp da ginnig traoù nevez en amzer da zont. Evit muioc'h a ditouroù, kit da weladenniñ kemennadenn [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ blog Wikimedia]. 

[[File:UsabilityNavigation.png|right|link=|Skeudennoù skramm war an doare merdeiñ nevez]]
[[File:UsabilityToolbar.png|right|link=|Skeudennoù skramm eus ar varrenn ostilhoù gwellaet]]
[[File:UsabilityDialogs.png|right|link=|Skeudennoù skramm eus ar prenestroù krouiñ danvez nevez]]
===Setu ar pezh zo bet cheñchet===
* '''Merdeiñ :''' Gwellaet eo bet an doare merdeiñ evit lenn ha kemmañ pajennoù. Bremañ emañ kalz muioc'h war wel an ivinelloù e laez pep pajenn labour pe kaozeal, ha kemend-all pa vezit oc'h aozañ ur pennad pe o lenn anezhañ.
* '''Gwellaennoù er varrenn ostilhoù :''' Adframmet eo bet ar varrenn ostilhoù, dezhi da vezañ aesoc'h da implijout. Bremañ eo aesoc'h maketenniñ pajennoù ha kavout an doare d'en ober.
* '''Skoazeller liammoù :''' Un ostilh aes d'ober gantañ a dalvez deoc'h da ouzhpennañ liammoù ouzh pajennoù wiki all hag ouzh lec'hiennoù diavaez.
* '''Gwellaennoù klask :''' Gwellaet  eo bet ar c'hinnigoù klask, da gavout fonnusoc'h ar bajenn emaoc'h o klask.
* '''Perzhioù nevez all :'''  Degaset hon eus ivez un ostilh da sevel taolennoù aesoc'h hag ur vodulenn erlec'hiañ evit aesaat ar c'hemmañ pajennoù.",
	'prefswitch-main-anon' => "===Distreiñ===
Mar fell deoc'h diweredekaat ar perzhioù nevez [$1 klikit amañ]. Ret e vo deoc'h kevreañ pe krouiñ ur gont da gentañ.",
	'prefswitch-main-on' => "===Trawalc'h gant ar jeu-se !===
[$2 Klikit amañ evit diweredekaat an arc'hweladurioù nevez].",
	'prefswitch-main-off' => "===Un taol-esae !===
MAr fell deoc'h ober gant ar barregezhioù nevez, [$1 klikit amañ].",
	'prefswitch-survey-intro-feedback' => "Plijet-bras e vefemp o kaout keloù diganeoc'h.
Mar fell deoc'h e c'hallit respont d'an tamm sontadeg a-is a-raok klikañ war [[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]",
	'prefswitch-survey-intro-off' => "Ho trugarekaat da vezañ amprouet ar perzhioù nevez.
Mard eo mat deoc'h e c'hallit hor skoazellañ en ur respont d'an tamm sontadeg a-is, a-raok klikañ war \"[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]\".",
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'prefswitch-survey-true' => 'Da',
	'prefswitch-survey-false' => 'Ne',
	'prefswitch-survey-answer-whyoff-other' => 'Ostali razlozi:',
	'prefswitch-survey-answer-browser-other' => 'Ostali preglednici:',
);

/** Catalan (Català)
 * @author Paucabot
 * @author SMP
 * @author Vriullop
 */
$messages['ca'] = array(
	'prefswitch' => "Commutador de preferències de la Iniciativa d'Usabilitat",
	'prefswitch-desc' => 'Permet als usuaris canviar conjunts de preferències',
	'prefswitch-survey-true' => 'Sí',
	'prefswitch-survey-false' => 'No',
	'prefswitch-survey-submit-off' => 'Deshabilita les noves característiques',
	'prefswitch-survey-cancel-off' => 'Si voleu continuar utilitzant les noves característiques podeu tornar a $1.',
	'prefswitch-survey-submit-feedback' => 'Donau la vostra opinió',
	'prefswitch-survey-cancel-feedback' => 'Si no voleu fer cap comentari podeu tornar a $1.',
	'prefswitch-survey-question-like' => 'Què us ha agradat de les noves funcionalitats?',
	'prefswitch-survey-question-dislike' => 'Què és el que no us ha agradat de les funcionalitats?',
	'prefswitch-survey-question-whyoff' => 'Per què desactiveu les noves funcionalitats?
Si us plau, seleccioneu tot el que correspongui',
	'prefswitch-survey-answer-whyoff-hard' => "Ha estat massa difícil d'usar.",
	'prefswitch-survey-answer-whyoff-didntwork' => 'No funcionava correctament.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'No funcionava de manera predictible.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => "No m'ha agradat el seu aspecte.",
	'prefswitch-survey-answer-whyoff-didntlike-layout' => "No m'han agradat les noves pestanyes ni el nou format.",
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => "No m'ha agradat la nova barra d'eines.",
	'prefswitch-survey-answer-whyoff-other' => 'Una altra raó:',
	'prefswitch-survey-question-browser' => 'Quin navegador emprau?',
	'prefswitch-survey-answer-browser-other' => 'Un altre navegador:',
	'prefswitch-survey-question-os' => 'Quin sistema operatiu usau?',
	'prefswitch-survey-answer-os-other' => 'Un altre sistema operatiu:',
	'prefswitch-survey-question-res' => 'Quina és la resolució de la vostra pantalla?',
	'prefswitch-title-on' => 'Noves característiques',
	'prefswitch-title-switched-on' => 'Gaudiu-ne!',
	'prefswitch-title-off' => 'Deshabilita les noves característiques',
	'prefswitch-title-switched-off' => 'Gràcies',
	'prefswitch-title-feedback' => 'Avaluació',
	'prefswitch-success-on' => "S'han habilitat noves característiques. Esperem que les aprecieu. Sempre podeu tornar enrere clicant a l'enllaç «[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]» de dalt de tot de la pàgina.",
	'prefswitch-success-off' => "S'han deshabilitat les noves característiques. Gràcies per provar-les. Podeu sempre tornar-les a habilitar clicant en l'enllaç «[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]» de dalt de tot de la pàgina.",
	'prefswitch-success-feedback' => 'Els vostres comentaris han estat enviats.',
	'prefswitch-return' => '<hr style="clear:both">
Torna a <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| Mostra de la nova interfície de navegació de la Viquipèdia <small>[[Media:VectorNavigation-en.png|(amplia)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| Mostra de la interfície d'edició bàsica de pàgines <small>[[Media:VectorEditorBasic-en.png|(amplia)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| Mostra de la nova caixa de diàleg per introduir enllaços
|}
|}

L'equip d'experiència d'usuari (''User Experience Team'') de la Fundació Wikimedia ha estat treballant amb voluntaris de la comunitat per fer-vos les coses més senzilles. Estem ansiosos per compartir algunes millores, incloent-hi un nou aspecte i la simplificació de les funcions d'edició. Aquests canvis estan pensats per a que els nous col·laboradors ho tinguin més fàcil per començar i estan basats en les nostres [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study proves d'usabilitat fetes durant l'any anterior]. La millora de la usabilitat dels nostres projectes és una prioritat de la Fundació Wikimedia i compartirem més actualitzacions en el futur. Per a més informació vegeu el missatge publicat al [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ bloc de Wikimedia].

===Això és el que hem canviat===
* '''Navegació''': Hem millorat la navegació per la lectura i modificació de pàgines. Ara, les pestanyes de la part superior de cada pàgina defineixen més clarament si esteu veient la pàgina principal o la de discussió, i si esteu llegint o modificant una pàgina.
* '''Millores en la barra d'eines d'edició''': Hem reorganitzat la barra d'eines d'edició per a que sigui més fàcil d'utilitzar. Ara, donar format a les pàgines és més senzill i més intuïtiu.
* '''Assistent per a enllaços''': Una eina fàcil d'utilitzar us permet afegir enllaços a altres pàgines wiki, així com enllaços a llocs externs.
* '''Millores en la cerca''': Hem millorat els suggeriments de cerca per trobar més ràpidament la pàgina que esteu cercant.
* '''Altres característiques noves''': També hem introduït un assistent per fer més fàcil la creació de taules i una funció de cerca i reemplaça per simplificar la modificació de pàgines.
* '''Logotip de la Viquipèdia''': Hem actualitzat el nostre logotip. Vegeu més informació al [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ bloc de Wikimedia].

===Teniu comentaris a fer?===
Ens agradaria saber-los. Podeu visitar la nostra [[\$1|pàgina de comentaris]] o, si esteu interessats en les tasques en marxa per millorar el programari, visiteu el nostre [http://usability.wikimedia.org wiki d'usabilitat] per a més informació.",
	'prefswitch-main-anon' => '===Tornar enrere===
Si desitgeu desactivar les noves característiques [$1 cliqueu aquí]. Se us demanarà abans que us registreu o creeu un compte.',
	'prefswitch-main-on' => '===Tornar enrere!===
[$2 Cliqueu aquí per desactivar les noves característiques].',
	'prefswitch-main-off' => '===Proveu-les!===
Si desitgeu activar les noves funcionalitats [$1 cliqueu aquí].',
	'prefswitch-survey-intro-feedback' => "Ens agradaria saber les vostres impressions.
Si us plau, ompliu el formulari opcional d'aquí sota abans de clicar «[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]».",
	'prefswitch-survey-intro-off' => "Gràcies per provar les noves característiques.
Per ajudar-nos a millorar-les, si us plau ompliu el formulari opcional d'aquí sota abans de clicar «[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]».",
	'prefswitch-feedbackpage' => "Project:Comentaris sobre l'experiència d'usuari",
);

/** Czech (Česky)
 * @author Kuvaly
 * @author Mormegil
 */
$messages['cs'] = array(
	'prefswitch' => 'Přepínač nastavení Iniciativy použitelnosti',
	'prefswitch-desc' => 'Umožňuje uživatelům přepínat sady nastavení',
	'prefswitch-survey-true' => 'Ano',
	'prefswitch-survey-false' => 'Ne',
	'prefswitch-survey-submit-off' => 'Vypnout nové funkce',
	'prefswitch-survey-cancel-off' => 'Pokud chcete i nadále používat nové vlastnosti, můžete se vrátit na stránku $1.',
	'prefswitch-survey-submit-feedback' => 'Odeslat názor',
	'prefswitch-survey-cancel-feedback' => 'Pokud nám nechcete sdělit svůj názor, můžete se vrátit na $1.',
	'prefswitch-survey-question-like' => 'Co se vám z nových funkcí líbilo?',
	'prefswitch-survey-question-dislike' => 'Co se vám z nových funkcí nelíbilo?',
	'prefswitch-survey-question-whyoff' => 'Proč jste vypnuli nové funkce? 
Vyberte všechny relevantní možnosti.',
	'prefswitch-survey-answer-whyoff-hard' => 'Byla příliš složitá na používání.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Nefungovala správně.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Nechovala se předvídatelně.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Nelíbil se mi její vzhled.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Nelíbily se mi nové záložky a rozvržení.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Nelíbil se mi nový panel nástrojů.',
	'prefswitch-survey-answer-whyoff-other' => 'Jiný důvod:',
	'prefswitch-survey-question-browser' => 'Jaký prohlížeč používáte?',
	'prefswitch-survey-answer-browser-other' => 'Jiný prohlížeč:',
	'prefswitch-survey-question-os' => 'Jaký operační systém používáte?',
	'prefswitch-survey-answer-os-other' => 'Jiný operační systém:',
	'prefswitch-survey-question-res' => 'Jaké je rozlišení vaší obrazovky?',
	'prefswitch-title-on' => 'Nové funkce',
	'prefswitch-title-switched-on' => 'Užijte si to!',
	'prefswitch-title-off' => 'Vypnout nové funkce',
	'prefswitch-title-switched-off' => 'Děkujeme',
	'prefswitch-title-feedback' => 'Zpětná vazba',
	'prefswitch-success-on' => 'Nové funkce jsou nyní zapnuty. Doufáme, že se vám budou líbit. Kdykoli je můžete opět vypnout kliknutím na odkaz „[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]“ navrchu stránky.',
	'prefswitch-success-off' => 'Nové funkce jsou nyní vypnuty. Děkujeme za vyzkoušení nových funkcí. Kdykoli je můžete opět zapnout kliknutím na odkaz „[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]“ navrchu stránky.',
	'prefswitch-success-feedback' => 'Váš názor byla odeslán.',
	'prefswitch-return' => '<hr style="clear:both"> 
Zpět na <span class="plainlinks">[$1 $2].</span>',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-cs.png|401px|]]
|-
| Snímek obrazovky s novým navigačním rozhraním <small>[[Media:VectorNavigation-cs.png|(zvětšit)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-cs.png|401px|]]
|-
| Snímek obrazovky se základním editačním rozhraním <small>[[Media:VectorEditorBasic-cs.png|(zvětšit)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-cs.png|401px|]]
|-
| Snímek obrazovky s novým dialogem pro vkládání odkazů
|}
|}

Tým nadace Wikimedia pro uživatelskou přívětivost pracoval s dobrovolníky z komunity, aby vám zjednodušil práci. Jsme rádi, že se s vámi můžeme podělit o několik vylepšení včetně nového vzhledu a zjednodušené editace. Cílem těchto změn je zjednodušit začátky nováčkům a jsou založeny na našich [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study testech z minulého roku]. Vylepšování použitelnosti našich projektů je prioritou Wikimedia Foundation a i v budoucnu budeme nabízet další inovace. Podrobnosti můžete najít ve [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ zprávě na blogu Wikimedia].

=== Co jsme změnili === 
* '''Navigace:''' Máme lepší navigaci pro čtení a editaci stránek. Záložky v horní části každé stránky nyní zřetelněji zobrazují, zda si prohlížíte článek či diskusi a zda stránku čtete či editujete.
* '''Vylepšení panelu nástrojů:''' Přeorganizovali jsme editační panel nástrojů, aby se snadněji používal. Formátování stránek je teď jednodušší a intuitivnější.
* '''Průvodce odkazy:''' Jednoduše použitelný nástroj vám pomůže přidávat odkazy na jiné články wiki, jako i na externí stránky.
* '''Vylepšení vyhledávání:''' Zlepšili jsme našeptávač u vyhledávání, abyste se rychleji dostali na stránku, kterou hledáte.
* '''Další nové vlastnosti:''' Také jsme zavedli průvodce tabulkou, aby bylo vytváření tabulek snadnější, a také funkci vyhledávání a nahrazování pro jednodušší editaci stránek.
* '''Logo Wikipedie:''' Modernizovali jsme naše logo. Více se dozvíte na [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ blogu Wikimedia].

===Komentáře?===
Uvítáme vaše názory. Navštivte naši [[\$1|stránku pro komentáře]] nebo, pokud vás zajímá naše dlouhodobé úsilí o vylepšování softwaru, můžete nalézt více informací na [http://usability.wikimedia.org wiki projektu použitelnosti].",
	'prefswitch-main-anon' => '===Chci zpátky===
Pokud chcete, můžete si [$1 vypnout nové funkce]. Nejdříve se budete muset přihlásit nebo zaregistrovat.',
	'prefswitch-main-on' => '=== Chci zpátky! === 
[$2 Klikněte sem, pokud chcete vypnout nové funkce].',
	'prefswitch-main-off' => '=== Vyzkoušejte ji! === 
Pokud si chcete vyzkoušet nové funkce, prosím [$1 klikněte zde].',
	'prefswitch-survey-intro-feedback' => 'Budeme rádi, když se dozvíme váš názor.
Vyplňte prosím dotazník níže a poté klikněte na „[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]“.',
	'prefswitch-survey-intro-off' => 'Děkujeme za vyzkoušení našich nových funkcí.
Chcete-li nám pomoci zlepšit je, vyplňte nepovinný dotazník níže a poté klikněte na „[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]“.',
);

/** Church Slavic (Словѣ́ньскъ / ⰔⰎⰑⰂⰡⰐⰠⰔⰍⰟ)
 * @author ОйЛ
 */
$messages['cu'] = array(
	'prefswitch-survey-true' => 'да',
	'prefswitch-survey-false' => 'нѣ́тъ',
);

/** Welsh (Cymraeg)
 * @author Lloffiwr
 */
$messages['cy'] = array(
	'prefswitch' => 'Switsh y Dewisiadau ar y Cynllun Defnyddioldeb',
	'prefswitch-desc' => 'Yn gadael i ddefnyddwyr newid rhwng gwahanol setiau o ddewisiadau',
	'prefswitch-survey-submit-off' => 'Diffodd y nodweddion newydd',
	'prefswitch-survey-cancel-off' => "Os ydych am barhau i ddefnyddio'r nodweddion newydd, gallwch ddychwelyd at y dudalen $1.",
	'prefswitch-survey-submit-feedback' => 'Anfon adborth',
	'prefswitch-survey-cancel-feedback' => 'Os nad ydych am anfon adborth atom, gallwch ddychwelyd i $1.',
	'prefswitch-survey-question-whyoff' => "Pam ydych chi'n diffodd y nodweddion newydd?
Dewiswch yr holl resymau dros gwneud.",
	'prefswitch-survey-answer-whyoff-hard' => "Roedd y nodweddion yn rhy anodd i'w defnyddio.",
	'prefswitch-survey-answer-whyoff-didntwork' => "Nid oedd y nodweddion yn gweithio'n iawn.",
	'prefswitch-survey-answer-whyoff-notpredictable' => "Roedd y nodweddion yn gweithredu'n fympwyol.",
	'prefswitch-survey-answer-whyoff-didntlike-look' => "Doeddwn i ddim yn hoffi'r golwg newydd.",
	'prefswitch-survey-answer-whyoff-didntlike-layout' => "Doeddwn i ddim yn hoffi'r tabiau a'r gosodiad newydd.",
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => "Doeddwn i ddim yn hoffi'r bar offer newydd.",
	'prefswitch-survey-question-browser' => "Pa borwr gwe ydych chi'n ei ddefnyddio?",
	'prefswitch-survey-answer-browser-other' => 'Porwr arall:',
	'prefswitch-survey-question-os' => "Pa system weithredu ydych chi'n ei defnyddio?",
	'prefswitch-survey-answer-os-other' => 'System weithredu arall:',
	'prefswitch-survey-question-res' => "Pa gydraniad sydd i'ch sgrin?",
	'prefswitch-title-on' => 'Nodweddion newydd',
	'prefswitch-title-switched-on' => 'Mwynhewch!',
	'prefswitch-title-off' => 'Diffodd y nodweddion newydd',
	'prefswitch-title-switched-off' => 'Diolch',
	'prefswitch-title-feedback' => 'Adborth',
	'prefswitch-success-on' => 'Mae\'r nodweddion newydd ar waith. Gobeithiwn y byddwch yn mwynhau eu defnyddio. Gallwch eu diffodd drwy glicio ar y cyswllt "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" ar frig tudalen.',
	'prefswitch-success-off' => 'Diffoddwyd y nodweddion newydd. Diolch am roi tro arnynt. Gallwch eu rhoi ar waith eto trwy glicio\'r cyswllt "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" ar frig tudalen.',
	'prefswitch-success-feedback' => 'Anfonwyd eich adborth.',
	'prefswitch-return' => '<hr style="clear:both">
Dychwelyd i <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "Rydym wedi bod yn gweithio'n galed i wneud pethau'n haws i'n defnyddwyr. Rydym am rannu rhai gwelliannau gyda chi, gan gynnwys golwg newydd, naws gwahanol a nodweddion golygu symlach. Mae gwella defnyddioldeb ein prosiectau yn flaenoriaeth i Sefydliad Wikimedia, a byddwn yn rhannu rhagor o ddiweddariadau eto. Am fwy o fanylion, ewch i [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ flog Wikimedia].

[[File:UsabilityNavigation.png|right|link=|Ciplun o'r tabiau llywio]]
[[File:UsabilityToolbar.png|right|link=|Ciplun o'r bar offer golygu estynedig]]
[[File:UsabilityDialogs.png|right|link=|Ciplun o'r blychau deialog ar gyfer cynhyrchu cynnwys tudalennau]]
=== Dyma'r hyn sydd wedi newid ===
*'''Llywio''': Rydym wedi gwella'r llywio ar gyfer darllen tudalennau a'u golygu. Yn awr, mae'n haws gweld o'r tabiau ar frig tudalen p'un ai'r dudalen neu ei thudalen drafod sydd o'ch blaen, a ph'un ai ydych yn darllen neu yn golygu. 
*'''Gwelliannau i'r bar offer golygu''': Rydym wedi ad-drefnu'r bar offer golygu i'w wneud yn haws i'w ddefnyddio. Nawr, mae fformatio tudalennau yn symlach ac yn haws ei ddeall.
*'''Dewin cysylltu''': Teclyn hawdd i'w ddefnyddio yn eich galluogi i ychwanegu cysylltau i dudalennau eraill ar y wici yn ogystal â chysylltau i safleoedd allanol. 
*'''Gwelliannau wrth chwilio''': Rydym wedi gwella'r awgrymiadau chwilio i gael hyd i'r nod yn gyflymach.
*'''Nodweddion newydd eraill''': Rydym hefyd wedi gwneud dewin tablau i'w gwneud yn haws llunio tablau, ac wedi gwneud teclyn 'canfod a disodli' i'ch cynorthwyo wrth i chi olygu.
*'''Glôb pos Wicipedia''': Rydym wedi diweddaru'r glôb ar lun pos. Darllenwch fwy ar [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ flog Wikimedia].",
	'prefswitch-main-anon' => '===Ewch Nôl a Fi===
Os ydych am ddiffodd y nodweddion newydd, [$1 cliciwch fan hyn]. Fe gewch gynnig mewngofnodi neu greu cyfrif yn gyntaf.',
	'prefswitch-main-on' => '===Ewch â fi am nôl!===
Os hoffech ddiffodd y nodweddion newydd, cliciwch [$2 fan hyn].',
	'prefswitch-main-off' => '==Rhowch gynnig arni!==
Os yr hoffech roi cynnig ar y nodweddion newydd, pwyswch [$1 fan hyn].',
	'prefswitch-survey-intro-feedback' => 'Byddem yn falch o gael eich barn. 
Os y dymunwch, llenwch yr arolwg dewisol isod ac yna pwyswch ar "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Diolch am roi cynnig ar ein nodweddion newydd. 
I\'n helpu ni i\'w gwella, cwblhewch yr arolwg dewisol isod, yna pwyswch ar "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
);

/** German (Deutsch)
 * @author Als-Holder
 * @author Kghbln
 * @author Merlissimo
 * @author The Evil IP address
 */
$messages['de'] = array(
	'prefswitch' => 'Umschalter für die Usability Initiative-Einstellungen',
	'prefswitch-desc' => 'Erlaube Benutzern die Umschaltung zwischen verschiedenen Einstellungs-Sets',
	'prefswitch-survey-true' => 'Ja',
	'prefswitch-survey-false' => 'Nein',
	'prefswitch-survey-submit-off' => 'Neue Features abschalten',
	'prefswitch-survey-cancel-off' => 'Wenn du die neuen Features weiter verwenden willst, kannst du zu $1 zurückkehren.',
	'prefswitch-survey-submit-feedback' => 'Feedback geben',
	'prefswitch-survey-cancel-feedback' => 'Wenn du kein Feedback geben möchtest, kannst du zu $1 zurückkehren.',
	'prefswitch-survey-question-like' => 'Was gefiel dir an den Features?',
	'prefswitch-survey-question-dislike' => 'Was mochtest du an den Features nicht?',
	'prefswitch-survey-question-whyoff' => 'Warum schaltest du die neuen Features ab?
Bitte wähle alle zutreffenden Punkte aus.',
	'prefswitch-survey-answer-whyoff-hard' => 'Die Verwendung war zu kompliziert.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Es funktioniert nicht einwandfrei.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Es funktioniert nicht in vorhersehbarer Weise.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Ich mag das Aussehen nicht.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Ich mag die neuen Tabs und das Layout nicht.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Ich mag die neue Werkzeugleiste nicht.',
	'prefswitch-survey-answer-whyoff-other' => 'Anderer Grund:',
	'prefswitch-survey-question-browser' => 'Welchen Browser verwendest du?',
	'prefswitch-survey-answer-browser-other' => 'Anderer Browser:',
	'prefswitch-survey-question-os' => 'Welches Betriebssystem verwendest du?',
	'prefswitch-survey-answer-os-other' => 'Anderes Betriebssystem:',
	'prefswitch-survey-question-res' => 'Was ist deine Bildschirmauflösung?',
	'prefswitch-title-on' => 'Neue Features',
	'prefswitch-title-switched-on' => 'Viel Spaß!',
	'prefswitch-title-off' => 'Neue Features abschalten',
	'prefswitch-title-switched-off' => 'Danke',
	'prefswitch-title-feedback' => 'Feedback',
	'prefswitch-success-on' => 'Die neuen Funktionalitäten sind jetzt aktiv. Wir wünschen dir viel Freude bei deren Verwendung. Du kannst sie jederzeit deaktivieren, indem du auf den Link „[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]“ oben auf der Webseite klickst.',
	'prefswitch-success-off' => 'Die neuen Funktionalitäten sind jetzt inaktiv. Vielen Dank, dass du sie verwendet hast. Du kannst sie jederzeit aktivieren, indem du auf den Link „[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]“ oben auf der Webseite klickst.',
	'prefswitch-success-feedback' => 'Dein Feedback wurde versandt.',
	'prefswitch-return' => '<hr style="clear:both">
Zurück zu <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-de.png|401px|]]
|-
| Ein Screenshot von Wikipedias neuer Navigationsoberfläche <small>[[Media:VectorNavigation-de.png|(vergrößern)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-de.png|401px|]]
|-
| Ein Screenshot der einfachen Bearbeitungsoberfläche <small>[[Media:VectorEditorBasic-de.png|(vergrößern)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-de.png|401px|]]
|-
| Ein Screenshot der neuen Dialoge zum Einfügen von Links
|}
|}

Das User Experience Team der Wikimedia Foundation hat zusammen mit Freiwilligen aus der Gemeinschaft daran gearbeitet, die Sachen für dich einfacher zu machen. Wir freuen uns, einige Verbesserungen zu präsentieren, unter anderem ein neues Aussehen und vereinfachte Bearbeitungsfunktionen. Diese Veränderungen sind dazu da, neuen Benutzern einen einfacheren Start zu ermöglichen, und basieren auf unseren [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study über das gesamte letzte Jahr durchgeführten Benutzbarkeits-Tests]. Die Benutzbarkeit unserer Projekte zu erhöhen ist eine Priorität der Wikimedia Foundation, welche auch in Zukunft weitere Updates präsentieren wird. Für weitere Informationen, siehe den [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia Wikimedia-Blog-Eintrag].

=== Was sich verändert hat ===
* '''Navigation:''' Wir haben die Navigation zum Lesen und zur Bearbeitung der Seiten verbessert. Die Reiter am Kopf jeder Seite zeigen klarer, ob du eine Seite oder eine Diskussionsseite liest und ob du eine Seite liest oder bearbeitest.
* '''Werkzeugleiste:''' Wir haben die Werkzeugleiste einfacher bedienbar gemacht. Die Formatierung von Seiten ist nun einfacher und intuitiver.
* '''Link-Assistent:''' Ein einfach zu bedienender Dialog ermöglicht das Hinzufügen von Links sowohl zu anderen Wiki-Seiten als auch zu externen Seiten.
* '''Suche:''' Wir haben die Suchvorschläge verbessert, damit du schneller zu der von dir gesuchten Seite kommst.
* '''Weiteres:''' Ein Tabellen-Assistent ermöglicht das einfache Erstellen von Tabellen und ein Suchen-und-Ersetzen-Dialog vereinfacht die Seitenbearbeitung.
* '''Wikipedia-Puzzle-Globus''': Wir haben den Puzzle-Globus erneuert, mehr Informationen im [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ Wikimedia-Blog.]

===Feedback?===
Wir würden gerne von dir hören. Bitte benutze unsere [[\$1|Feedback-Seite]] oder, falls du an unserer momentanen Arbeit zur Verbesserung der Software interessiert bist, besuche das [http://usability.wikimedia.org Usability-Wiki] für weitere Informationen.",
	'prefswitch-main-anon' => '===Zurück===
Sofern du die neuen Funktionen deaktivieren möchtest, dann [$1 klicke hier]. Du wirst dann gebeten dich anzumelden oder zunächst ein neues Benutzerkonto zu erstellen.',
	'prefswitch-main-on' => '=== Bring mich zurück! ===
Wenn du die neuen Features abschalten möchtest, [$2 klicke hier].',
	'prefswitch-main-off' => '=== Probiere es aus! ===
Wenn du die neuen Features einschalten möchtest, [$1 klick hier].',
	'prefswitch-survey-intro-feedback' => 'Wir würden uns freuen, von dir zu hören.
Bitte fülle die freiwillige Umfrage aus, bevor du auf „[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]“ klickst.',
	'prefswitch-survey-intro-off' => 'Danke für das Ausprobieren unserer neuen Features.
Damit wir besser werden können, fülle bitte die freiwillige Umfrage aus, bevor du auf „[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]“ klickst.',
	'prefswitch-feedbackpage' => 'Project:Usability-Initiative/Feedback',
);

/** German (formal address) (Deutsch (Sie-Form))
 * @author Kghbln
 * @author The Evil IP address
 */
$messages['de-formal'] = array(
	'prefswitch-survey-cancel-off' => 'Wenn Sie die neuen Features weiter verwenden wollen, können Sie zu $1 zurückkehren.',
	'prefswitch-survey-cancel-feedback' => 'Wenn Sie kein Feedback geben möchten, können Sie zu $1 zurückkehren.',
	'prefswitch-survey-question-like' => 'Was gefiel Ihnen an den Features?',
	'prefswitch-survey-question-dislike' => 'Was mochten Sie an den Features nicht?',
	'prefswitch-survey-question-whyoff' => 'Warum schalten Sie die neuen Features ab?
Bitte wählen Sie alle zutreffenden Punkte aus.',
	'prefswitch-survey-question-browser' => 'Welchen Browser verwenden Sie?',
	'prefswitch-survey-question-os' => 'Welches Betriebssystem verwenden Sie?',
	'prefswitch-survey-question-res' => 'Was ist Ihre Bildschirmauflösung?',
	'prefswitch-success-on' => 'Die neuen Funktionalitäten sind jetzt aktiv. Wir wünschen Ihnen viel Freude bei deren Verwendung. Sie können sie jederzeit deaktivieren, indem Sie auf den Link „[[Special:UsabilityInitiativePrefSwitch|Beta verlassen]]“ oben auf der Webseite klicken.',
	'prefswitch-success-off' => 'Die neuen Funktionalitäten sind jetzt inaktiv. Vielen Dank, dass Sie sie verwendet haben. Sie können sie jederzeit aktivieren, indem Sie auf den Link „[[Special:UsabilityInitiativePrefSwitch|Beta aktivieren]]“ oben auf der Webseite klicken.',
	'prefswitch-success-feedback' => 'Ihr Feedback wurde versandt.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| Ein Screenshot von Wikipedias neuer Navigationsoberfläche <small>[[Media:VectorNavigation-en.png|(vergrößern)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| Ein Screenshot der einfachen Bearbeitungsoberfläche <small>[[Media:VectorEditorBasic-en.png|(vergrößern)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| Ein Screenshot der neuen Dialoge zum Einfügen von Links
|}
|}

Das User Experience Team der Wikimedia Foundation hat zusammen mit Freiwilligen aus der Gemenschaft daran gearbeitet, die Sachen für Sie einfacher zu machen. Wir freuen uns, einige Verbesserungen zu präsentieren, unter anderem ein neues Aussehen und vereinfachte Bearbeitungsfunktionen. Diese Veränderungen sind dazu da, neuen Benutzern einen einfacheren Start zu ermöglichen und sind basiert auf unseren [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study über das gesamte letzte Jahr durchgeführten Benutzbarkeits-Tests]. Die Benutzbarkeit unserer Projekte zu erhöhen ist eine Priorität der Wikimedia Foundation, welche auch in Zukunft weitere Updates präsentieren wird. Für weitere Informationen, siehe den [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia Wikimedia-Blog-Eintrag].

=== Was sich verändert hat ===
* '''Navigation:''' Wir haben die Navigation zum Lesen und zur Bearbeitung der Seiten verbessert. Die Reiter am Kopf jeder Seite zeigen klarer, ob Sie eine Seite oder eine Diskussionsseite lesen und ob Sie eine Seite lesen oder bearbeiten.
* '''Werkzeugleiste:''' Wir haben die Werkzeugleiste einfacher bedienbar gemacht. Die Formatierung von Seiten ist nun einfacher und intuitiver.
* '''Link-Assistent:''' Ein einfach zu bedienender Dialog ermöglicht das Hinzufügen von Links sowohl zu anderen Wiki-Seiten als auch zu externen Seiten.
* '''Suche:''' Wir haben die Suchvorschläge verbessert, damit Sie schneller zu der von dir gesuchten Seite kommen.
* '''Weiteres:''' Ein Tabellen-Assistent ermöglicht das einfache Erstellen von Tabellen und ein Suchen-und-Ersetzen-Dialog vereinfacht die Seitenbearbeitung.
* '''Wikipedia-Puzzle-Globus''': wir haben den Puzzle-Globus erneuert, mehr Informationen im [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ Wikimedia-Blog.]

===Feedback?===
Wir würden gerne von Ihnen hören. Bitte benutzen Sie unsere [[\$1|Feedback-Seite]] oder, falls Sie an unseren momentanen Arbeit zur Verbesserung der Software interessiert sind, besuchen Sie das [http://usability.wikimedia.org Usability-Wiki] für weitere Informationen.",
	'prefswitch-main-anon' => '===Zurück===
Sofern Sie die neuen Funktionen deaktivieren möchten, dann [$1 klicken Sie hier]. Sie werden dann gebeten sich anzumelden oder zunächst ein neues Benutzerkonto zu erstellen.',
	'prefswitch-main-on' => '=== Bring mich zurück! ===
Wenn Sie die neuen Features abschalten möchten, [$2 klicken Sie hier].',
	'prefswitch-survey-intro-feedback' => 'Wir würden uns freuen, von Ihnen zu hören.
Bitte füllen Sie die freiwillige Umfrage aus, bevor Sie auf „[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]“ klicken.',
	'prefswitch-survey-intro-off' => 'Danke für das Ausprobieren unserer neuen Features.
Damit wir besser werden können, füllen Sie bitte die freiwillige Umfrage aus, bevor Sie auf „[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]“ klicken.',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'prefswitch' => 'Pśešaltowanje nastajenjow iniciatiwy wužywajobnosći',
	'prefswitch-desc' => 'Wužywarjam dowóliś, sajźby nastajenjow pśešaltowaś',
	'prefswitch-survey-true' => 'Jo',
	'prefswitch-survey-false' => 'Ně',
	'prefswitch-survey-submit-off' => 'Nowe funkcije wótšaltowaś',
	'prefswitch-survey-cancel-off' => 'Jolic coš nowe funkcije dalej wužywaś, móžoš se k $1 wrośiś.',
	'prefswitch-survey-submit-feedback' => 'Měnjenje pósłaś',
	'prefswitch-survey-cancel-feedback' => 'Jolic njocoš měnjenje pósłaś, móžoš se do $1 wrośiś.',
	'prefswitch-survey-question-like' => 'Co spódoba se śi na nowych funkcijach?',
	'prefswitch-survey-question-dislike' => 'Co njespódoba se śi na nowych funkcijach?',
	'prefswitch-survey-question-whyoff' => 'Cogodla wótšaltujoš nowe funkcije?
Pšosym wubjeŕ wšykne, kótarež maju se nałožyś.',
	'prefswitch-survey-answer-whyoff-hard' => 'Wužywanje jo było pśekomplicěrowane.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Njejo pórědnje funkcioněrowało.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Njejo ako pśedwiźone funkcioněrowało.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Naglěd se mě njespódoba.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Nowe rejtarki a layout se mi njespódobaju.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Nowa rědowa kšoma se mi njespódoba.',
	'prefswitch-survey-answer-whyoff-other' => 'Druga pśicyna:',
	'prefswitch-survey-question-browser' => 'Kótary wobglědowak wužywaš?',
	'prefswitch-survey-answer-browser-other' => 'Drugi wobglědowak:',
	'prefswitch-survey-question-os' => 'Kótary źěłowy system wužywaš?',
	'prefswitch-survey-answer-os-other' => 'Drugi źěłowy system:',
	'prefswitch-survey-question-res' => 'Co jo rozeznaśe twójeje wobrazowki?',
	'prefswitch-title-on' => 'Nowe funkcije',
	'prefswitch-title-switched-on' => 'Wjele wjasela!',
	'prefswitch-title-off' => 'Nowe funkcije wótšaltowaś',
	'prefswitch-title-switched-off' => 'Źěkujomy se',
	'prefswitch-title-feedback' => 'Rezonanca',
	'prefswitch-success-on' => 'Nowe funkcije su něnto zašaltowane. Naźejamy se, až wjaseliš se nowym funkcijam. Móžoš je kuždy cas pśez kliknjenje na wótkaz "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" górjejce na bpku wušaltowaś.',
	'prefswitch-success-off' => 'Nowe funkcije su něnto wótšaltowane. Žěkujomy se, až sy nowe funkcije testował. Móžoš je kuždy cas pśez kliknjenje na wótkaz "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" górjejce na bpku zašaltowaś.',
	'prefswitch-success-feedback' => 'Twójo měnjenje jo se pósłało.',
	'prefswitch-return' => '<hr style="clear:both">
Slědk k <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-de.png|401px|]]
|-
| Wobrazkowe foto nowego nawigaciskego pówjercha Wikipedije <small>[[Media:VectorNavigation-de.png|(powětšyś)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-de.png|401px|]]
|-
| Wobrazkowe foto zakładnego pówjercha za wobźěłowanje bokow <small>[[Media:VectorEditorBasic-de.png|(powětyš)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-de.png|401px|]]
|-
| Wobrazkowe foto nowego dialogowego kašćika za zapódawanje wótkazow
|}
|}
Źěłamy śežko, aby my wólažcyli wěcy za swójich wužywarjow. My by se wjaselili, se wó pólěpšenjach wuměniś, inkluziwnje nowy naglěd a zjadnorjone wobźěłowańske funkcije. Pólěpšenje wužywajobnosći našych projektow jo priorita załožby Wikimedia foundation a rozdźělijomy wěcej aktualizacijow w pśichoźe. Za dalšne informacije, woglědaj se wótpowědny [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ Wikimedia blogowy zapisk].

===Tole smy změnili===
* '''Nawigacija''': Smy pólěpšyli nawigaciju za cytanje a wobźěłowanje bokow. Něnto rejtarki górjejce na boku mógu jasnjej definěrowaś, lěc woglědujoš se nastawk abo diskusijny bok, a lěc cytaš abo wobźěłujoš bok.
* '''Pólěpšenja wobźěłowańskeje lejstwy''': Smy pśeorganizowali wobźěłowańsku lejstwu, aby wužywanje wólažcyli. Něnto jo formatěrowanje bokow lažčejše a intuitiwnjejše.
* '''Wótkazowy asistent''': Rěd, kótaryž dajo se lažko wužywaś a śi dowólujo, wótkaze drugim wikibokam ako teke wótkaze eksternym sedłam pśidaś.
* '''Pytańske pólěpšenja''' Smy pytańske naraźenja pólěpšyli, aby śi wjadli malsnjej k tomu bokoju, kótaryž pytaš.
* '''Druge nowe funkcije''': Smy teke zawjadli tabelowy asistent, aby wólažcyli napóranje tabelow a funkciju za pytanje a narownanje, aby my zjadnorili wobźěłowanje bokow.
* '''Logo wikipedije:''' Smy našo logo zaktualizěrowali. Dalšne informacije na [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d blogu Wikimedije].

===Měnjenja?===
My by se wjaselili, wót tebje słyšaś. Pšosym woglědaj se [[\$1|bok měnjenjow]] abo, jolic zajmujoš se za naše běžne napinanja, aby se softwara pólěpšyła,  woglědaj se naš [http://usability.wikimedia.org wiki wužywajobnosći] za dalšne informacije.",
	'prefswitch-main-anon' => '===Slědk===
Jolic coš nowe funkcije znjemóžnis, [$1 klikni how].  Pšose śi se pśizjawiś abo nejpjerwjej konto załožyś.',
	'prefswitch-main-on' => '===Spóraj mě slědk!===
Jolic coš nowe funkcije wótšaltowaś, [$2 klikni pšosym how].',
	'prefswitch-main-off' => '===Wopytaj je!===
Jolic coš nowe funkcije zašaltowaś, [$1 klikni pšosym how].',
	'prefswitch-survey-intro-feedback' => 'My by se wjaselili, wót tebje słyšaś.
Pšosym wupołni slědujuce opcionalne napšašowanje, nježli až kliknjoš na "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Źěkujomy se, až sy wopytał naše nowe funkcije.
Aby nam pomogał, je pólěpšyś, wupołni pšosym slědujuce opcionalne napšašowanje, nježli až kliknjoš na "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Project:Komentary wó iniciatiwje wužywajobnosći',
);

/** Greek (Ελληνικά)
 * @author Flyax
 * @author Απεργός
 */
$messages['el'] = array(
	'prefswitch' => 'Πρωτοβουλία για τη Χρηστικότητα αλλαγή προτιμήσεων',
	'prefswitch-desc' => 'Να επιτρέπεται στους χρήστες να αλλάζουν ομάδες προτιμήσεων',
	'prefswitch-survey-true' => 'Ναι',
	'prefswitch-survey-false' => 'Όχι',
	'prefswitch-survey-submit-off' => 'Απενεργοποίηση καινούριων λειτουργιών',
	'prefswitch-survey-cancel-off' => 'Αν θέλετε να συνεχίσετε να χρησιμοποιείτε τις καινούριες λειτουργίες, μπορείτε να επιστρέψετε στο $1.',
	'prefswitch-survey-submit-feedback' => 'Αποστολή σχολίων',
	'prefswitch-survey-cancel-feedback' => 'Αν δεν θέλετε να δώσετε σχόλια, μπορείτε να επιστρέψετε στο $1.',
	'prefswitch-survey-question-like' => 'Τι σας άρεσε στις καινούριες λειτουργίες;',
	'prefswitch-survey-question-dislike' => 'Τι δεν σας άρεσε στις λειτουργίες;',
	'prefswitch-survey-question-whyoff' => 'Γιατί απενεργοποιείτε τις καινούριες λειτουργίες; Επιλέξτε όλα όσα ισχύουν.',
	'prefswitch-survey-answer-whyoff-hard' => 'Ήταν πολύ δύσχρηστες.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Δεν λειτουργούσαν σωστά.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Δεν συμπεριφέρονταν προβλέψιμα.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Δεν μου άρεσε η εμφάνισή τους.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Δεν μου άρεσαν οι καινούριες καρτέλες και η διάταξη.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Δεν μου άρεσε η καινούρια εργαλειοθήκη.',
	'prefswitch-survey-answer-whyoff-other' => 'Άλλος λόγος:',
	'prefswitch-survey-question-browser' => 'Ποιο φυλλομετρητή ιστοσελίδων χρησιμοποιείτε;',
	'prefswitch-survey-answer-browser-other' => 'Άλλο φυλλομετρητή:',
	'prefswitch-survey-question-os' => 'Ποιο λειτουργικό σύστημα χρησιμοποιείτε;',
	'prefswitch-survey-answer-os-other' => 'Άλλο λειτουργικό σύστημα:',
	'prefswitch-survey-question-res' => 'Ποια είναι η ανάλυση της οθόνης σας;',
	'prefswitch-title-on' => 'Καινούριες λειτουργίες',
	'prefswitch-title-switched-on' => 'Απολαύστε τες!',
	'prefswitch-title-off' => 'Απενεργοποίηση των καινούριων λειτουργιών',
	'prefswitch-title-switched-off' => 'Ευχαριστούμε',
	'prefswitch-title-feedback' => 'Σχόλια',
	'prefswitch-success-on' => 'Οι καινούριες λειτουργίες είναι τώρα ενεργοποιημένες. Ελπίζουμε ότι απολαμβάνετε τις καινούριες λειτουργίες.  Μπορείτε πάντα να τις απενεργοποιήσετε ξανά κάνοντας κλικ στο σύνδεσμο "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" στην κορυφή της σελίδας.',
	'prefswitch-success-off' => 'Οι καινούριες λειτουργίες απενεργοποιήθηκαν.  Ευχαριστούμε που δοκιμάσατε τις καινούριες λειτουργίες.  Μπορείτε πάντα να τις ενεργοποιήσετε ξανά κάνοντας κλικ στο σύνδεσμο "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" στην κορυφή της σελίδας.',
	'prefswitch-success-feedback' => 'Τα σχόλια σας αποστάλθηκαν.',
	'prefswitch-return' => '<hr style="clear:both">
Επιστροφή στο <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "Δουλεύουμε σκληρά για να κάνουμε πιο εύκολα τα πράγματα για τους χρήστες μας.  Είμαστε ενθουσιασμένοι που μοιραζόμαστε μαζί σας μερικές βελτιώσεις, που περιλαμβάνουν απλοποιημένες λειτουργίες επεξεργασίας καθώς και μια καινούρια εμφάνιση και αισθητική των σελίδων.  Η βελτίωση της χρηστικότητας των εγχειρημάτων μας είναι προτεραιότητα του Ιδρύματος Wikimedia και θα συνεχίσουμε να δημοσιεύουμε κι άλλες ενημερώσεις στο μέλλον.

[[File:UsabilityNavigation.png|right|link=|Στιγμιότυπο της καινούργιας πλοήγησης]]
[[File:UsabilityToolbar.png|right|link=|Στιγμιότυπο της εμπλουτισμένης εργαλειοθήκης επεξεργασίας]]
[[File:UsabilityDialogs.png|right|link=|Στιγμιότυπο των καινούργιων παραθύρων για τη δημιουργία περιεχομένου]]
===Αυτά που αλλάξαμε===
* '''Πλοήγηση:''' Έχουμε βελτιώσει την πλοήγηση για την ανάγνωση και την επεξεργασία σελίδων. Τώρα οι καρτέλες στο πάνω μέρος της κάθε σελίδας δείχνουν πιο καθαρά αν βλέπετε την ίδια τη σελίδα  ή τη σελίδα συζήτησης, και επίσης αν διαβάζετε ή αν επεξεργάζεστε μια σελίδα.
* '''Βελτιώσεις στην εργαλειοθήκη επεξεργασίας:''' Αναδιοργανώσαμε την εργαλειοθήκη επεξεργασίας ώστε να είναι πιο εύχρηστη.  Τώρα η μορφοποίηση σελίδων είναι πιο απλή και πιο διαισθητική. 
* '''Οδηγός για συνδέσμους:'''  Ένα εύχρηστο εργαλείο που σας επιτρέπει να προσθέσετε συνδέσμους προς άλλες σελίδες της Βικιπαίδειας καθώς και προς εξωτερικούς ιστότοπους.
* '''Βελτιώσεις στην αναζήτηση:''' Έχουμε βελτιώσει τις συμβουλές αναζήτησης, ώστε  να οδηγηθείτε στη σελίδα που ψάχνετε πιο γρήγορα. 
* '''Άλλες καινούργιες λειτουργίες:'''  Επιπλέον έχουμε εισαγάγει έναν οδηγό που διευκολύνει τη δημιουργία πινάκων, και μια λειτουργία για αναζήτηση-αντικατάσταση που απλοποιεί την επεξεργασία σελίδων.",
	'prefswitch-main-anon' => '===Επιστροφή στις παλιά ρυθμίσεις===
Αν θέλετε να απενεργοποιήσετε τις καινούριες λειτουργίες, [$1 πατήστε εδώ]. Θα σας ζητηθεί πρώτα να συνδεθείτε ή να δημιουργήσετε λογαριασμό.',
	'prefswitch-main-on' => '===Επιστροφή πίσω===
Αν θέλετε να απενεργοποιήσετε τις καινούριες λειτουργίες, παρακαλούμε [$2 πατήστε εδώ].',
	'prefswitch-main-off' => '===Δοκιμάστε τες!===
Αν θέλετε να ενεργοποιήσετε τις καινούριες λειτουργίες, παρακαλούμε [$1 πατήστε εδώ].',
	'prefswitch-survey-intro-feedback' => 'Θα χαιρόμασταν να μάθουμε τη γνώμη σας.
Σας παρακαλούμε να συμπληρώσετε το παρακάτω προαιρετικό ερωτηματολόγιο πριν πατήσετε την «[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]».',
	'prefswitch-survey-intro-off' => 'Ευχαριστούμε που δοκιμάσατε τις καινούριες μας λειτουργίες.
Για να μας βοηθήσετε να τις βελτιώσουμε, σας παρακαλούμε να συμπληρώσετε το παρακάτω προαιρετικό ερωτηματολόγιο πριν πατήσετε την «[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]».',
);

/** Esperanto (Esperanto)
 * @author ArnoLagrange
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
	'prefswitch-survey-question-like' => 'Kio plaĉas al vi de la novaj funkcioj?',
	'prefswitch-survey-question-dislike' => 'Kio malplaĉas al vi de la novaj funkcioj?',
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
	'prefswitch-title-on' => 'Novaj funkcioj',
	'prefswitch-title-switched-on' => 'Ĝuu!',
	'prefswitch-title-off' => 'Malŝalti novajn funkciojn',
	'prefswitch-title-switched-off' => 'Dankon',
	'prefswitch-title-feedback' => 'Komentoj',
	'prefswitch-success-on' => 'Novaj funkcioj nun estas ŝaltitaj. Ni esperas ke vi aprezas uzi ilin. Vi povas ĉiumomente malŝalti ilin alklakante la ligilon 
"[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]"  en la supro de la paĝo.',
	'prefswitch-success-off' => 'Novaj funkcioj nun estas malŝaltitaj. Dankon ke vi provas la novajn ecojn. Vi povas ĉiumomente reŝalti ilin  alklakante la ligilon "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" en la supro de la paĝo.',
	'prefswitch-success-feedback' => 'Viaj komento estis sendita.',
	'prefswitch-return' => '<hr style="clear:both">
Reiri al <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| Ekrankapto de la nova navigilo <small>[[Media:VectorNavigation-en.png|(pligrandigu)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| Ekrankapto de la nova progresa redakto-ilobreto <small>[[Media:VectorEditorBasic-en.png|(pligrandigu)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| Ekrankapto de la nova enhav-generadaj dialogujoj
|}
|}

Ni penis faciligi aferojn por niaj uzantoj. Ni ĝojas provizi iujn novajn plibonigojn, inkluzivante novan aspekton kaj simpligitan redaktilaron. Ĉi tiuj ŝanĝoj celas igi redaktadon de Vikipedio pli facila por novaj kontribuantoj kaj estas bazitaj sur nia [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study studo kaj testado de uzebleco] farita lastjare. Plibonigado de la uzebleco estas prioritato de la Vikimedia Fondaĵo kaj ni sendos pluajn ĝisdatigojn estonte. Por pluaj detaloj, bonvolu viziti [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ rilatan Vikimedia-blogeron].



===Jen kion ni ŝanĝis===
* '''Navigado:''' Ni plibonigis la navigadon por legi kaj redakti paĝojn. Nun la etikedoj ĉe la supro de ĉiu paĝo pli klare difinas ĉu vi vidas la paĝon aŭ la diskuto-paĝon, kaj ĉu vi legas aŭ redaktas la paĝon.
* '''Redakta ilobreto:''' Ni reorganizis la redaktan ilobreton por simpligi ĝin. Nun, formataj paĝoj estas pli simpla kaj pli intuicia.
* '''Ligila asistanto:''' Facila ilo por aldoni ligilojn al aliaj paĝoj de Vikipedio kaj ligiloj al eksteraj retejoj.
* '''Serĉaj plibonigoj:''' Ni plibonigis serĉsugestojn por pli rapide direkti vin al la paĝo kiun vi serĉas.
* '''Aliaj novaj funkcioj:''' Ni ankaŭ aldonis tabelan asistanton, por faciligi kreadon de tabeloj, kaj funkcio por anstataŭigi tekston en paĝoj.
* '''Vikipedia puzlo-globo''': Ni ĝisdatigis la puzloglobon. Legu plu ĉe la [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ Vikimedia-blogo].

===Reagoj?===
Ni ŝatus aŭdi pri vi. Bonvolu viziti nian [[\$1|reagopaĝon]] aŭ se vi interesiĝas pri niaj nunaj penoj por plibonigi la softvaron, vizitu nian [http://usability.wikimedia.org uzeblecovikion] por pliaj informoj.",
	'prefswitch-main-anon' => '===Revenigu min  al la antaŭa versio===
[$1 Alklaku ĉi tie por malŝalti la novajn ecojn]. Vi estos antaŭe petata esti ensalutinta aŭ kreinta konton.',
	'prefswitch-main-on' => '===Revenigu min al la antaŭa versio===
Se vi volus malŝalti la novajn funkciojn, bonvolu [$2 klaki ĉi tie].',
	'prefswitch-main-off' => '===Trovu ĝin nun!===
Se vi volus ŝalti la novajn funkciojn, bonvolu [$1 klaki ĉi tie].',
	'prefswitch-survey-intro-feedback' => 'Ni bonvenus vian opinion.
Bonvolu plenumi la jenan malnepran enketon antaŭ klakante "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Dankon pro provante niajn novajn funkciojn.
Helpi nin por plibonigi ilin, bonvolu plenumi la jenan malnepran enketon antaŭ klakante "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Projekto:Sciigoj pri uzantospertoj',
);

/** Spanish (Español)
 * @author Crazymadlover
 * @author Locos epraix
 * @author Muro de Aguas
 * @author Pertile
 * @author Translationista
 */
$messages['es'] = array(
	'prefswitch' => 'Conmutador de la preferencia de Iniciativa de Usabilidad',
	'prefswitch-desc' => 'Permitir a los usuarios cambiar los conjuntos de preferencias',
	'prefswitch-survey-true' => 'Sí',
	'prefswitch-survey-false' => 'No',
	'prefswitch-survey-submit-off' => 'Desactivar las nuevas características',
	'prefswitch-survey-cancel-off' => 'Si desea continuar utilizando las nuevas características puede volver a $1.',
	'prefswitch-survey-submit-feedback' => 'Enviar comentario',
	'prefswitch-survey-cancel-feedback' => 'Si no desea enviar sus comentarios puede volver a $1.',
	'prefswitch-survey-question-like' => 'Qué te gustó de las nuevas características?',
	'prefswitch-survey-question-dislike' => 'Qué no te gustó de las nuevas características',
	'prefswitch-survey-question-whyoff' => '¿Por qué está desactivando las nuevas características?
Por favor seleccione todas las opciones que correspondan.',
	'prefswitch-survey-answer-whyoff-hard' => 'Era muy difícil de utilizar.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'No funcionó correctamente.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Tuvo comportamientos impredecibles.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'No me gustó la forma en que se veía.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'No me gustaron las nuevas solapas ni el diseño.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'No me gustó la nueva barra de herramientas.',
	'prefswitch-survey-answer-whyoff-other' => 'Otras razones:',
	'prefswitch-survey-question-browser' => '¿Qué navegador utiliza?',
	'prefswitch-survey-answer-browser-other' => 'Otro navegador:',
	'prefswitch-survey-question-os' => '¿Qué sistema operativo utiliza?',
	'prefswitch-survey-answer-os-other' => 'Otro sistema operativo:',
	'prefswitch-survey-question-res' => '¿Cuál es la resolución de su pantalla?',
	'prefswitch-title-on' => 'Nuevas características',
	'prefswitch-title-switched-on' => '¡Disfrute!',
	'prefswitch-title-off' => 'Desactivar las nuevas características',
	'prefswitch-title-switched-off' => 'Gracias',
	'prefswitch-title-feedback' => 'Comentarios',
	'prefswitch-success-on' => 'Se han activado las nuevas características. Esperamos que disfrutes las nuevas características. Siempre puedes desactivarlas presionando "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" en la parte superior de la página.',
	'prefswitch-success-off' => 'Se han desactivado las nuevas características. Gracias por probarlas. Siempre puedes activarlas presionando presionando "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" en la parte superior de la página.',
	'prefswitch-success-feedback' => 'Sus comentarios han sido enviados.',
	'prefswitch-return' => '<hr style="clear:both">
Volver a <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-es.png|401px|]]
|-
| Una captura de pantalla de la nueva interfaz de navegación de Wikipedia <small>[[Media:VectorNavigation-es.png|(agrandar)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-es.png|401px|]]
|-
| Una captura de pantalla de la nueva interfaz de edición <small>[[Media:VectorEditorBasic-es.png|(agrandar)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-es.png|401px|]]
|-
| Una captura de pantalla del nuevo cuadro de dialogo para ingresar enlaces
|}
|}
El Equipo de experiencia de usuario de la fundación Wikimedia ha estado trabajando con voluntarios de la comunidad para hacer las cosa más fáciles para ti. Nos emociona poder compartir algunas de nuestras mejoras, incluyendo una nueva estética y características de edición simplificadas. Estos cambios intentan hacerlo más fácil para los contribuyentes que comienzan, y están basados en nuestro [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study test de usabilidad realizado durante el año anterior]. La usabilidad de nuestros proyectos es una de las prioridades de la Fundación Wikimedia y en un futuro estaremos compartiendo más actualizaciones al respecto. Para más información, visita la [http://blog.wikimedia.org/2010/a-new-look-for-wikipedia/ publicación en inglés] de la fundación Wikimedia.

===Aquí está lo que hemos cambiado===
* '''Navegación:''' hemos mejorado la navegación para la lectura y edición de páginas. Ahora las pestañas en la parte superior de cada página definen de forma más precisa si está viendo una página o una página de discusión, y si está leyendo o editando una página.
* '''Mejoras en la barra de herramientas de edición:''' hemos reorganizado la barra de herramientas de edición para que sea más sencilla de utilizar. Ahora dar formato a las páginas es más simple e intuitivo.
* '''Asistente de enlaces:''' una sencilla herramienta permite añadir enlaces ya sea a otras páginas de Wikipedia como a otros sitios externos.
* '''Mejoras en la búsqueda:''' hemos mejorado las sugerencias para llegar más rápido a la página que se está buscando.
* '''Otras nuevas características:''' también hemos introducido un asistente de tablas para hacer más sencilla la creación de tablas y una funcionalidad de buscar y reemplazar que simplifica la edición de páginas.
* '''Logo de Wikipedia''': Hemos actualizado nuestro logo. Encontrarás más información al respecto en el [http://blog.wikimedia.org/2010/wikipedia-in-3d/ blog Wikimedia en inglés].

===Retroalimentar?===
Nos encantaría escucharte. Por favor visita nuestra [[\$1|página de retroalimentación]] o, si estás interesado en nuestros actuales esfuerzos para mejorar el software, visita nuestra [http://usability.wikimedia.org wiki de usabilidad] para mayor información.",
	'prefswitch-main-anon' => '=== Volver a la versión anterior ===
Si deseas deshabilitar las nuevas características, haz clic [$1 aquí]. Necesitarás iniciar sesión o crear una cuenta primero.',
	'prefswitch-main-on' => '===Volverme a la versión anterior!===
[$2 Haz click aquí para desactivar las nuevas características].',
	'prefswitch-main-off' => '===Pruébalos!===
Si deseas activar las nuevas características, por favor haz [$1 click aquí].',
	'prefswitch-survey-intro-feedback' => 'Nos encantaría escucharlo.
Por favor llene la encuesta opcional de abajo presionando "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Gracias por probar nuestras nuevas características.
Para ayudarnos a mejorarlas, por favor llene la encuesta de abajo presionando "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Project:Feedback de experiencia de usuario',
);

/** Estonian (Eesti)
 * @author AivoK
 * @author Hendrik
 * @author Pikne
 */
$messages['et'] = array(
	'prefswitch-survey-true' => 'Jah',
	'prefswitch-survey-false' => 'Ei',
	'prefswitch-survey-submit-feedback' => 'Saada tagasiside',
	'prefswitch-survey-answer-whyoff-hard' => 'Seda oli liiga keeruline kasutada.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'See ei toiminud korralikult.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'See ei toiminud oodatult.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Mulle ei meeldinud selle välimus.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Mulle ei meeldinud uus tööriistariba.',
	'prefswitch-survey-answer-whyoff-other' => 'Muu põhus:',
	'prefswitch-survey-question-browser' => 'Millist internetilehitsejat sa kasutad?',
	'prefswitch-survey-answer-browser-other' => 'Muu brauser:',
	'prefswitch-survey-question-os' => 'Millist operatsioonisüsteemi sa kasutad?',
	'prefswitch-survey-answer-os-other' => 'Muu operatsioonisüsteem:',
	'prefswitch-survey-question-res' => 'Milline on su kuvari eraldusvõime?',
	'prefswitch-title-feedback' => 'Tagasiside',
	'prefswitch-survey-intro-feedback' => 'Meile meeldiks teada saada sinu arvamust.
Palun täida alljärgnev vabatahtlik küsitlus.',
);

/** Basque (Euskara)
 * @author Joxemai
 */
$messages['eu'] = array(
	'prefswitch-survey-true' => 'Bai',
	'prefswitch-survey-false' => 'Ez',
	'prefswitch-survey-answer-whyoff-other' => 'Bestelako arrazoiak:',
	'prefswitch-survey-question-browser' => 'Zer nabigatzaile erabiltzen duzu?',
	'prefswitch-survey-answer-browser-other' => 'Beste nabigatzaile bat:',
	'prefswitch-survey-question-os' => 'Zer sistema eragile erabiltzen duzu?',
	'prefswitch-survey-answer-os-other' => 'Beste sistema eragile bat:',
	'prefswitch-survey-question-res' => 'Zein da zure pantailaren erresoluzioa?',
	'prefswitch-title-on' => 'Zer berri?',
	'prefswitch-title-switched-on' => 'Ongi pasa!',
	'prefswitch-title-switched-off' => 'Eskerrik asko',
	'prefswitch-survey-intro-feedback' => 'Atsegin dugu zurekin harremanetan jartzea.
Mesedez borondatezko beheko inkesta bete ezazu.',
	'prefswitch-survey-intro-off' => 'Eskerrik asko gure aureka berria saiatzeagatik.
Horiek hobetzen laguntzeko, bere ezazu, mesedez, borondatezko beheko inkesta.',
);

/** Finnish (Suomi)
 * @author Crt
 * @author Str4nd
 */
$messages['fi'] = array(
	'prefswitch' => 'Käytettävyyshankkeen asetusvalinta',
	'prefswitch-desc' => 'Mahdollistaa käyttäjille asetussarjan vaihtamisen.',
	'prefswitch-survey-true' => 'Kyllä',
	'prefswitch-survey-false' => 'Ei',
	'prefswitch-survey-submit-off' => 'Poista käytöstä uudet omaisuudet',
	'prefswitch-survey-cancel-off' => 'Jos haluat jatkaa uusien ominaisuuksien käyttämistä, voit palata sivulle $1.',
	'prefswitch-survey-submit-feedback' => 'Lähetä palaute',
	'prefswitch-survey-cancel-feedback' => 'Jos et halua antaa palautetta, voit palata sivulle $1.',
	'prefswitch-survey-question-like' => 'Mistä pidit uusissa ominaisuuksissa?',
	'prefswitch-survey-question-dislike' => 'Mistä et pitänyt uusissa ominaisuuksissa?',
	'prefswitch-survey-question-whyoff' => 'Miksi olet poistamassa käytöstä uusia ominaisuuksia?
Valitse kaikki sopivat.',
	'prefswitch-survey-answer-whyoff-hard' => 'Se oli liian vaikeakäyttöinen.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Se ei toiminut kunnolla.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Se ei toiminut odotetusti.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'En pitänyt sen ulkonäöstä.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'En pitänyt uusista välilehdistä ja asettelusta.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'En pitänyt uudesta työkalupalkista.',
	'prefswitch-survey-answer-whyoff-other' => 'Muu syy',
	'prefswitch-survey-question-browser' => 'Mitä selainta käytät?',
	'prefswitch-survey-answer-browser-other' => 'Muu selain',
	'prefswitch-survey-question-os' => 'Mitä käyttöjärjestelmää käytät?',
	'prefswitch-survey-answer-os-other' => 'Muu käyttöjärjestelmä',
	'prefswitch-survey-question-res' => 'Mikä on näyttösi resoluutio?',
	'prefswitch-title-on' => 'Uudet ominaisuudet',
	'prefswitch-title-switched-on' => 'Nauti!',
	'prefswitch-title-off' => 'Poista käytöstä uudet omaisuudet',
	'prefswitch-title-switched-off' => 'Kiitos',
	'prefswitch-title-feedback' => 'Palaute',
	'prefswitch-success-on' => 'Uudet ominaisuudet ovat nyt käytössä. Toivomme, että nautit uusien ominaisuuksien käytöstä. Voit aina ottaa ne pois käytöstä napsauttamalla ”[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]” -linkkiä sivun yläreunasta.',
	'prefswitch-success-off' => 'Uudet ominaisuudet ovat nyt poistettu käytöstä. Kiitos uusien ominaisuuksien kokeilusta. Voit aina ottaa ne takaisin käyttöön napsauttamalla ”[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]” -linkkiä sivun yläreunasta.',
	'prefswitch-success-feedback' => 'Palautteesi on lähetetty.',
	'prefswitch-return' => '<hr style="clear:both">
Palaa sivulle <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[File:VectorNavigation-en.png|401px|]]
|-
| Kuvakaappaus uudesta navigaatiokäyttöliittymästä. <small>[[Media:VectorNavigation-en.png|(suurenna)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[File:VectorEditorBasic-en.png|401px|]]
|-
| Kuvakaappaus sivujen muokkauskäyttöliittymästä. <small>[[Media:VectorEditorBasic-en.png|(suurenna)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[File:VectorLinkDialog-en.png|401px|]]
|-
| Kuvakaappaus uudesta linkkien lisäys -valintaikkunasta.
|}
|}
Wikimedia Foundationin käyttäjäkokemusryhmä on työskennellyt yhteisön vapaaehtoisten kanssa tehdäkseen asioista helpompia sinulle. Olemme jakaneet joitain parannuksia, kuten uuden ilmeen ja yksinkertaistetut muokkaustoiminnot. Näiden muutoksien tarkoituksena on helpottaa uusien muokkaajien alkuun pääsemistä. Muutokset perustuvat [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study käytettävyystestaukseen, joka toteutettiin viime vuoden aikana]. Käytettävyyden parantaminen hankkeissamme on etusijalla Wikimedia Foundationissa ja tulemme jakamaan lisää päivityksiä tulevaisuudessa. Lisätietoja löytyy aiheeseen liittyvästä Wikimedian [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ blogiviestistä].

=== Mitä olemme muuttaneet ===
* '''Navigaatio:''' Paransimme navigaatiota sivujen lukemiseksi ja muokkaamiseksi. Nyt jokaisen sivun yläreunassa olevat välilehdet ovat selvempiä.
* '''Muokkauspalkin parannukset:''' Olemme järjestäneet muokkauspalkin uudelleen, jotta sitä olisi helpompi käyttää. Nyt sivujen muotoilu on yksinkertaisempaa ja havainnollisempaa.
* '''Ohjattu linkkitoiminto:''' Helppokäyttöinen työkalu antaa sinun lisätä linkkejä sekä muille wikisivuille ja ulkoisille sivustoille.
* '''Hakuparannukset:''' Olemme parantaneet hakuehdotuksia, jotta löytäisit etsimäsi sivun nopeammin.
* '''Muut uudet toiminnot:''' Olemme myös tuoneet esiin ohjatun taulukonlisäystoiminnon taulukoiden helpompaan lisäämiseen ja etsi ja korvaa -toiminnon yksinkertaisempaan muokkaamiseen.
* '''Wikipedian logo:''' Olemme päivittäneet logoa. Lue lisää [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d Wikimedian blogista].

=== Palautetta? ===
Otamme mielellämme palautetta vastaan. Käy [[\$1|palautesivulla]], tai jos olet kiinnostunut jatkuvasta ohjelmistokehityksestämme – vieraile [http://usability.wikimedia.org/ käytettävyyswikissä] saadaksesi lisätietoja.",
	'prefswitch-main-anon' => '=== Palaa takaisin ===
[$1 Napsauta tästä, jos haluat poistaa käytöstä uudet ominaisuudet]. Sinua pyydetään ensin kirjautumaan sisään tai luomaan tunnus.',
	'prefswitch-main-on' => '=== Palaa takaisin ===
[$2 Napsauta tästä, jos haluat poistaa käytöstä uudet ominaisuudet].',
	'prefswitch-main-off' => '=== Kokeile ominaisuuksia ===
[$1 Napsauta tästä, jos haluat ottaa uudet ominaisuudet käyttöön].',
	'prefswitch-survey-intro-feedback' => 'Otamme mielellämme palautetta vastaan.
Täytä alla oleva valinnainen kysely ennen kuin napsautat ”[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]”.',
	'prefswitch-survey-intro-off' => 'Kiitos, kun kokeilit uusia ominaisuuksia.
Auttaaksesi parantamaan niitä – täytä alla oleva valinnainen kysely ennen kuin napsautat ”[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]”.',
);

/** French (Français)
 * @author Coyau
 * @author IAlex
 * @author Jean-Frédéric
 * @author Kropotkine 113
 */
$messages['fr'] = array(
	'prefswitch' => "Commutateur de préférences de l'initiative d'utilisabilité",
	'prefswitch-desc' => 'Permet aux utilisateurs de modifier des ensembles de préférences',
	'prefswitch-survey-true' => 'Oui',
	'prefswitch-survey-false' => 'Non',
	'prefswitch-survey-submit-off' => 'Désactiver les nouvelles fonctionnalités',
	'prefswitch-survey-cancel-off' => 'Si vous voulez continuer à utiliser les nouvelles fonctionnalités, vous pouvez revenir à $1',
	'prefswitch-survey-submit-feedback' => 'Envoyer des commentaires',
	'prefswitch-survey-cancel-feedback' => 'Si vous ne voulez pas faire de commentaires, vous pouvez revenir à $1.',
	'prefswitch-survey-question-like' => "Qu'avez-vous apprécié dans les nouvelles fonctionnalités ?",
	'prefswitch-survey-question-dislike' => "Qu'est-ce que vous n'avez pas apprécié dans les fonctionnalités ?",
	'prefswitch-survey-question-whyoff' => 'Pourquoi voulez-vous désactiver les nouvelles fonctionnalités ?
Veuillez choisir tout ce qui convient.',
	'prefswitch-survey-answer-whyoff-hard' => 'Il était trop difficile de l’utiliser.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Ça ne fonctionne pas correctement.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Cela ne s’est pas passé comme prévu.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Je n’ai pas aimé son apparence.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Je n’ai pas aimé les nouveaux onglets et la nouvelle disposition.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Je n’ai pas aimé la nouvelle barre d’outils.',
	'prefswitch-survey-answer-whyoff-other' => 'Autre raison :',
	'prefswitch-survey-question-browser' => 'Quel navigateur utilisez-vous ?',
	'prefswitch-survey-answer-browser-other' => 'Autre navigateur :',
	'prefswitch-survey-question-os' => 'Quel système d’exploitation utilisez-vous ?',
	'prefswitch-survey-answer-os-other' => 'Autre système d’exploitation :',
	'prefswitch-survey-question-res' => 'Quelle est la résolution de votre écran ?',
	'prefswitch-title-on' => 'Nouvelles fonctionnalités',
	'prefswitch-title-switched-on' => 'Savourez !',
	'prefswitch-title-off' => 'Désactiver les nouvelles fonctionnalités',
	'prefswitch-title-switched-off' => 'Merci',
	'prefswitch-title-feedback' => 'Réaction',
	'prefswitch-success-on' => 'Les nouvelles fonctionnalités sont maintenant activées. Nous espérons que vous apprécierez les nouvelles fonctionnalités. Vous pouvez toujours faire marche arrière en cliquant sur le lien « [[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]] » en haut de la page.',
	'prefswitch-success-off' => "Les nouvelles fonctionnalités sont maintenant désactivées. Merci d'avoir essayé les nouvelles fonctionnalités. Vous pouvez toujours les remettre sur en cliquant sur le lien « [[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]] » en haut de la page.",
	'prefswitch-success-feedback' => 'Vos commentaires ont été envoyés.',
	'prefswitch-return' => '<hr style="clear:both">
Revenir à <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-fr.png|401px|]]
|-
| Capture d’écran de la nouvelle interface de navigation de Wikipédia <small>[[Media:VectorNavigation-fr.png|(agrandir)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-fr.png|401px|]]
|-
| Capture d’écran de l’interface simple de modification de page <small>[[Media:VectorEditorBasic-fr.png|(agrandir)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
|[[Image:UsabilityDialogs-fr.png|401px|]]
|-
| Capture d’écran de la nouvelle boîte de dialogue pour insérer des liens
|}
|}
L’équipe Expérience utilisateur de la Fondation Wikimedia (''User Experience Team'') a travaillé en collaboration avec des volontaires de la communauté pour vous rendre les choses plus simples. Nous sommes heureux de nous partageons avec vous ces améliorations, notamment une nouvelle apparence et une simplification des fonctions de modification. Ces changements sont effectués pour que les nouveaux contributeurs puissent débuter plus facilement, et ils sont basés sur notre [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study tests utilisateurs menés sur l’année passée]. Améliorer l’utilisabilité de nos projets est une priorité de la Fondation Wikimédia, et nous vous ferons part de nos futures avancées. Pour plus de détails, veuillez consulter le [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia billet de blog Wikimédia]. 

=== Les nouveautés ===

* '''Navigation :''' nous avons amélioré la navigation pour la lecture et la modification des pages. Maintenant, les onglets en haut de chaque page définissent plus clairement si vous voyez la page ou la page de discussion, et si vous consultez ou modifiez une page.
* '''Améliorations de la barre d’outils de modification :''' nous avons réorganisé la barre d’outils de modification pour la rendre plus facile à utiliser. Maintenant, la mise en page est plus simple et plus intuitive.
* '''Assistant de liens :''' un outil simple pour vous permettre d’ajouter des liens vers d’autres pages de Wikipédia ainsi que des liens vers des sites externes.
* '''Amélioration de la recherche :''' nous avons amélioré les suggestions de recherche pour vous aider à trouver la page que vous recherchez plus rapidement.
* '''Autres fonctionnalités nouvelles :''' nous avons également introduit un assistant de tableaux pour créer des tableaux plus facilement et une fonctionnalité de remplacement pour simplifier la modification de page.
* '''Logo Wikipédia''': nous avons mis à jour notre logo. Pour en savoir plus, consultez le [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ blog de Wikimedia].

=== Retours d’expérience ===
Nous aimerions beaucoup avoir des retours de votre part. Veuillez vous rendre sur la [[\$1|page de retours d’expérience]] ou bien, si vous êtes intéressés par nos efforts continus pour améliorer le logiciel, visitez notre [http://usability.wikimedia.org wiki utilisabilité] pour plus d’informations.",
	'prefswitch-main-anon' => '===Take Me Back===
Si vous souhaitez désactiver les nouvelles fonctionnalités, [$1 cliquez ici].  Il vous sera demandé de vous connecter ou de vous créer un compte.',
	'prefswitch-main-on' => '=== Sortez-moi de là ! ===
[$2 Cliquez ici pour désactiver les nouvelles fonctionnalités].',
	'prefswitch-main-off' => '=== Essayez-les ! ===
Si vous souhaitez activer les nouvelles  fonctionnalités, veuillez [$1 cliquer ici].',
	'prefswitch-survey-intro-feedback' => 'Nous aimerions connaître vos impressions.
Si vous le désirez, vous pouvez remplir le sondage ci-dessous avant de cliquer sur « [[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]] ».',
	'prefswitch-survey-intro-off' => "Merci d'avoir essayé nos nouvelles fonctionnalités.
Pour nous aider à les améliorer, vous pouvez remplir le sondage optionnel ci-dessous avant de cliquer sur « [[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]] ».",
	'prefswitch-feedbackpage' => "Project:Retours d'expérience sur l'utilisabilité",
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'prefswitch' => 'Boton de prèferences de l’iniciativa d’utilisabilitât',
	'prefswitch-desc' => 'Pèrmèt ux utilisators de changiér des ensemblos de prèferences.',
	'prefswitch-survey-true' => 'Ouè',
	'prefswitch-survey-false' => 'Nan',
	'prefswitch-survey-submit-off' => 'Dèsactivar les novèles fonccionalitâts',
	'prefswitch-survey-cancel-off' => 'Se vos voléd continuar a utilisar les novèles fonccionalitâts, vos pouede tornar a $1.',
	'prefswitch-survey-submit-feedback' => 'Balyéd voutron avis',
	'prefswitch-survey-cancel-feedback' => 'Se vos voléd pas balyér voutron avis, vos pouede tornar a $1.',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'prefswitch' => 'Conmutador de preferencias da Iniciativa de usabilidade',
	'prefswitch-desc' => 'Permitir aos usuarios cambiar conxuntos de preferencias',
	'prefswitch-survey-true' => 'Si',
	'prefswitch-survey-false' => 'Non',
	'prefswitch-survey-submit-off' => 'Desactivar as novas características',
	'prefswitch-survey-cancel-off' => 'Se quere seguir usando as novas características, pode volver a "$1".',
	'prefswitch-survey-submit-feedback' => 'Dea a súa opinión',
	'prefswitch-survey-cancel-feedback' => 'Se non quere dar a súa opinión, pode volver a "$1".',
	'prefswitch-survey-question-like' => 'Que é o que lle gustou das novas características?',
	'prefswitch-survey-question-dislike' => 'Que é o que non lle gustou das novas características?',
	'prefswitch-survey-question-whyoff' => 'Por que está a desactivar as novas características?
Por favor, seleccione o que sexa conveniente.',
	'prefswitch-survey-answer-whyoff-hard' => 'Foi moi difícil de usar.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Non funcionou correctamente.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Non funcionou de modo predicible.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Non me gustou o seu aspecto.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Non me gustaron as novas lapelas e a distribución.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Non me gustou a nova barra de ferramentas.',
	'prefswitch-survey-answer-whyoff-other' => 'Outro motivo:',
	'prefswitch-survey-question-browser' => 'Que navegador usa?',
	'prefswitch-survey-answer-browser-other' => 'Outro navegador:',
	'prefswitch-survey-question-os' => 'Que sistema operativo usa?',
	'prefswitch-survey-answer-os-other' => 'Outro sistema operativo:',
	'prefswitch-survey-question-res' => 'Cal é a resolución da súa pantalla?',
	'prefswitch-title-on' => 'Novas características',
	'prefswitch-title-switched-on' => 'Páseo ben!',
	'prefswitch-title-off' => 'Desactivar as novas características',
	'prefswitch-title-switched-off' => 'Grazas',
	'prefswitch-title-feedback' => 'Opinión',
	'prefswitch-success-on' => 'As novas características están agora activadas. Agardamos que lle gusten. Pode desactivalas premendo sobre a ligazón "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" que aparecerá no canto superior de calquera páxina.',
	'prefswitch-success-off' => 'As novas características están agora desactivadas. Grazas por probalas. Pode activalas de novo premendo sobre a ligazón "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" que aparecerá no canto superior de calquera páxina.',
	'prefswitch-success-feedback' => 'Enviouse a súa opinión.',
	'prefswitch-return' => '<hr style="clear:both">
Volver a "<span class="plainlinks">[$1 $2]</span>".',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| Captura de pantalla da nova interface de navegación da Wikipedia <small>[[Media:VectorNavigation-en.png|(ampliar)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| Captura de pantalla da interface de edición básica <small>[[Media:VectorEditorBasic-en.png|(ampliar)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| Captura de pantalla dos novos diálogos de xeración de ligazóns
|}
|}
O equipo de experiencia de usuario da Fundación Wikimedia traballou arreo con voluntarios da comunidade para facerlles as cousas máis doadas aos usuarios. Estamos encantados de compartir algunhas melloras, incluíndo entre elas unha nova aparencia e características de edición simplificadas. Estes cambios, derivados das nosas [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study probas de usabilidade feitas durante o ano pasado], teñen o obxectivo de facilitar as cousas aos novos colaboradores. A mellora da usabilidade dos nosos proxectos é unha prioridade para a Fundación Wikimedia e traeremos máis actualizacións no futuro. Para obter máis información, visite [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia esta entrada] do blogue da Wikimedia.

===Isto foi o que cambiamos===
* '''Navegación:''' melloramos a navegación para a lectura e edición de páxinas. Agora, as lapelas da parte superior de cada páxina definen máis claramente se está a ollar a páxina ou a conversa ou se está lendo ou editando a páxina.
* '''Melloras na barra de ferramentas de edición:''' fixemos unha reorganización da barra de ferramentas de edición para facer máis doado o seu uso. Agora, dar formato ás páxinas é máis sinxelo e intuitivo.
* '''Asistente para as ligazóns:''' trátase dunha simple ferramenta que permite engadir ligazóns cara a outras páxinas da Wikipedia, así como ligazóns a sitios web externos.
* '''Melloras nas procuras:''' melloramos as suxestións de busca para que dea coa páxina que está a procurar máis rapidamente.
* '''Outras novas características:''' tamén introducimos un asistente para as táboas, que fai a creación de táboas máis fácil, e unha característica para atopar e substituír elementos, que simplifica a edición da páxina.
* '''O logo da Wikipedia:''' tamén actualizamos o noso logo. Máis información no [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d blogue da Wikimedia].

===Opinións?===
Gustaríanos saber o que lle parece. Visite a nosa [[\$1|páxina de comentarios]] ou o noso [http://usability.wikimedia.org wiki de usabilidade] se o que quere é involucrarse na mellora do software.",
	'prefswitch-main-anon' => '===Volver atrás===
Se quere desactivar as novas características, [$1 prema aquí]. Pediráselle que primeiro acceda ao sistema ou que cree unha conta.',
	'prefswitch-main-on' => '===Quero volver!===
[$2 Prema aquí se quere desactivar as novas características].',
	'prefswitch-main-off' => '===Próbeas!===
Se quere activar as novas características, [$1 prema aquí].',
	'prefswitch-survey-intro-feedback' => 'Gustaríanos saber o que lle parece.
Por favor, encha a enquisa opcional que aparece a continuación antes de premer en "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Grazas por probar as novas características.
Para axudarnos a melloralas, encha a enquisa opcional que aparece a continuación antes de premer en "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Project:Comentarios dos usuarios',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'prefswitch' => 'Umschalter fir d Yystellige fir d Benutzerfrejndligkeits-Initiative',
	'prefswitch-desc' => 'Benutzer s Umschalte zwische verschidene Yystelligs-Sätsz erlaube',
	'prefswitch-survey-true' => 'Jo',
	'prefswitch-survey-false' => 'Nei',
	'prefswitch-survey-submit-off' => 'Neji Funktione abschalte',
	'prefswitch-survey-cancel-off' => 'Wänn di neje Funktione wyter bruche witt, chasch zu $1 zruckgoh.',
	'prefswitch-survey-submit-feedback' => 'Ruckmäldig gee',
	'prefswitch-survey-cancel-feedback' => 'Wänn kei Ruckmäldig witt gee, chasch zue $1 zruckgoh.',
	'prefswitch-survey-question-like' => 'Was het Dir an dr neje Funktione gfalle?',
	'prefswitch-survey-question-dislike' => 'Was het Dir an dr neje Funktione nit gfalle?',
	'prefswitch-survey-question-whyoff' => 'Wurum schaltsch di neje Funktione ab?
Bitte wehl alli Pinkt, wu zuedräffe uus.',
	'prefswitch-survey-answer-whyoff-hard' => 'D Verwändig isch z schwirig gsi.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Si het nit rächt funktioniert.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'S het nit eso funktioniert, wie s gheisse het.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Mir het s nit gfalle, wie s uussiht.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Mir gfalle di neje Tabs un s Layout nit.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Mir gfallt s nej Wärchzygchäschtli nit.',
	'prefswitch-survey-answer-whyoff-other' => 'Andere Grund:',
	'prefswitch-survey-question-browser' => 'Wele Browser bruchsch Du?',
	'prefswitch-survey-answer-browser-other' => 'Andere Browser:',
	'prefswitch-survey-question-os' => 'Wel Betribssyschtem bruchsch Du?',
	'prefswitch-survey-answer-os-other' => 'Anders Betribssyschtem:',
	'prefswitch-survey-question-res' => 'Was fir e Uflesig het Dyy Bildschirm?',
	'prefswitch-title-on' => 'Neji Funktione',
	'prefswitch-title-switched-on' => 'Vil Spaß!',
	'prefswitch-title-off' => 'Neji Funktione abschalte',
	'prefswitch-title-switched-off' => 'Dankschen',
	'prefswitch-title-feedback' => 'Ruckmäldig',
	'prefswitch-success-on' => 'Di neje Funktione sin jetz yygschalte. Mir winsche Dir vil Freid dermit. Du chasch si jederzyt abschalte dur e Klick uf s Gleich „[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]“ obe uf dr Websyte.',
	'prefswitch-success-off' => 'Di neje Funktione sin jetz abgschalte. Dankschen fir s Uusbrobiere. Du chasch si jederzyt wider aaschalte dur e Klick uf s Gleich „[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]“ obe uf dr Websyte.',
	'prefswitch-success-feedback' => 'Dyy Ruckmäldig isch gschickt wore.',
	'prefswitch-return' => '<hr style="clear:both">
Zruck zue <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| Di nej Navigation. <small>[[Media:VectorNavigation-en.png|(greßer)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| Di nej Bearbeitisgoberflechi. <small>[[Media:VectorEditorBasic-en.png|(greßer)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px]]
|-
| Screeenshot vum neje Dialogchäschtli zum Gleicher (Link) yygee.
|}
|}

D Bruchbarkeits-Arbetsgruppe het mit Frejwillige vu dr Gmeinschaft dra gschafft, d Sache fir unsri Benutzer eifacher z mache. Mir freien is, ass mir e baar Verbesserige chenne aabiete, derzue e nej Uussäh un vereifachti Bearbeitigsfunktione.  Die Änderige solle s Aafange un Mitmache eifacher mache fir neji Mitarbeiter un basiere uf unsre [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study Benutzerfrejndligkeits-Teschtstudie], wu in dr letschte 12 Monet duzrgfiert woren isch.
D Benutzerfrejndligkeit verbessere het Prioritet bi dr Wikimedia Foundation un mir stelle in dr Zuechumpft meh Update z Verfiegig. Fir meh Informatione, kueg dr [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ Wikimedia-Blog]-Yytrag. 

===Des hän mer gänderet===
* '''Navigation:''' Mir hän d Navigation verbesseret zum Läse un Bearbeite vu Syte. Jetz gän d Ryter obe an dr Syte klarer aa, eb Du d Syte aaluegsch oder e Diskussionssyte, un eb Du am Läse oder am Bearbeite vu dr Syte bisch.
* '''Verbesserige vum Wärchzyygchäschtli:''' Mir hän s Wärchzyygchäschtli umorganisiert, ass es cha eifacher brucht wäre. Jetz isch s Formatiere eifacher un intuitiver.
* '''Gleichhilf:'''  E eifach Wärchzyyg, wu Dir s megli macht, Gleicher zue andere Wikipediasyte un zue extärne Syte.
* '''Verbesserige vu dr Suechi:''' Mir hän d Suechvorschleg verbesseret, ass Du schnäller uf die Syte chunnsch, wu Du suechsch.
* '''Anderi neji Funktione:'''  Mir hän au ne Tabällehilf yygfiert, wu s Aalege vu Tabälle eifacher macht, un e Hilf zum Sueche un Ersetze, wu s Bearbeite vu Syte eifacher macht.
* '''Wikipedia-Logo''': wir hän unser Logo nej gmacht, meh Informatione im [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ Wikimedia-Blog.]

=== Ruckmäldig? ===
Mir deeten is freie, vu Dir z here.
Bitte bsuech unseri [\$1 Ruckmäldigs-Syte].",
	'prefswitch-main-anon' => '===Zruck===
Wänn Du di neje Funktione witt abschalte, no [$1 druck do]. Du wirsch derno bätte Di aazmälde oder zerscht e nej Benutzerkonto aazlege.',
	'prefswitch-main-on' => '=== Bring mi zruck! ===
Wänn Du di neje Funktione witt abschalte, [$2 druck do].',
	'prefswitch-main-off' => '=== Probier s uus! ===
Wänn Du di neje Funktione witt yyschalte, [$1 druck do].',
	'prefswitch-survey-intro-feedback' => 'Mir deeten is freie, vu Dir z here.
Bitte fill di frejwillig Umfrog uus, voreb Du uf „[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]“ drucksch.',
	'prefswitch-survey-intro-off' => 'Dankschen fir s Uusprobiere vu unsre neje Funktione.
Ass mir no besser chenne wäre, fill bitte di frejwillig Umfrog uus, voreb Du uf „[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]“ drucksch.',
	'prefswitch-feedbackpage' => 'Project:Benutzerfrejndligkeits-Initiative/Ruckmäldig',
);

/** Hebrew (עברית)
 * @author Amire80
 * @author Rotemliss
 */
$messages['he'] = array(
	'prefswitch' => 'שינוי העדפות במיזם השמישות',
	'prefswitch-desc' => 'הוספת אפשרות למשתמשים לשנות קבוצות של העדפות',
	'prefswitch-survey-true' => 'כן',
	'prefswitch-survey-false' => 'לא',
	'prefswitch-survey-submit-off' => 'ביטול המראה החדש',
	'prefswitch-survey-cancel-off' => 'אם תרצו להמשיך להשתמש בתכונות החדשות, באפשרותכם לחזור אל $1.',
	'prefswitch-survey-submit-feedback' => 'שליחת משוב',
	'prefswitch-survey-cancel-feedback' => 'אם אינכם רוצים לתת משוב, תוכלו לחזור אל $1.',
	'prefswitch-survey-question-like' => 'מה אהבתם בתכונות החדשות?',
	'prefswitch-survey-question-dislike' => 'מה לא אהבתם בתכונות החדשות?',
	'prefswitch-survey-question-whyoff' => 'למה אתם עוזבים את הגרסה החדשה?
אנא בחרו את כל האפשרויות המתאימות.',
	'prefswitch-survey-answer-whyoff-hard' => 'היא הייתה קשה מדי לשימוש.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'היא לא פעלה כראוי.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'היא פעלה באופן בלתי צפוי.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'לא אהבתי את המראה החדש.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'לא אהבתי את השינויים בלשוניות ובעיצוב.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'לא אהבתי את סרגל הכלים החדש.',
	'prefswitch-survey-answer-whyoff-other' => 'סיבה אחרת:',
	'prefswitch-survey-question-browser' => 'באיזה דפדפן אתם משתמשים?',
	'prefswitch-survey-answer-browser-other' => 'דפדפן אחר:',
	'prefswitch-survey-question-os' => 'באיזו מערכת הפעלה אתם משתמשים?',
	'prefswitch-survey-answer-os-other' => 'מערכת הפעלה אחרת:',
	'prefswitch-survey-question-res' => 'מהי רזולוציית המסך שלכם?',
	'prefswitch-title-on' => 'תכונות חדשות',
	'prefswitch-title-switched-on' => 'תיהנו!',
	'prefswitch-title-off' => 'ביטול המראה החדש',
	'prefswitch-title-switched-off' => 'תודה',
	'prefswitch-title-feedback' => 'משוב',
	'prefswitch-success-on' => 'התכונות החדשות מופעלות עכשיו. אנו מקווים שתיהנו מהשימוש בהן. תוכלו לכבות אותן בכל עת על ידי לחיצה על הקישור [[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]] בראש הדף.',
	'prefswitch-success-off' => 'התכונות החדשות מבוטלות עכשיו. אנו מודים לכם על כך שניסיתם אותן. תוכלו להפעיל אותן מחדש בכל עת על ידי לחיצה על הקישור "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" בראש הדף.',
	'prefswitch-success-feedback' => 'המשוב שלכם נשלח.',
	'prefswitch-return' => '<hr style="clear:both">
חזרה אל <span class="plainlinks">[$1 $2].</span>',
	'prefswitch-main' => "עבדנו קשה כדי שלמשתמשים שלנו יהיה קל יותר. אנו נרגשים לשתף אתכם במספר שיפורים, בהם מראה חדש ויכולות עריכה פשוטות יותר. שיפור השמישות במיזמים שלנו הוא משימה בעלת עדיפות גבוהה עבור קרן ויקימדיה ואנחנו נשתף אתכם בעדכונים נוספים בעתיד. לפרטים נוספים, בקרו בהודעה בנושא ב[http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ בלוג של ויקימדיה].

[[File:UsabilityNavigation.png|left|link=|צילום מסך של דף הניווט החדש]]
[[File:UsabilityToolbar.png|left|link=|צילום מסך של סרגל העריכה המורחב]]
[[File:UsabilityDialogs.png|left|link=|צילום מסך של תיבות דו שיח חדשות ליצירת תוכן]]
===אלה הדברים ששינינו===
* '''ניווט''': שיפרנו את הניווט לקריאה ולעריכה של דפים. עכשיו הלשוניות בראש כל דף מציגות באופן ברור יותר האם אתם צופים בדף או בדף שיחה והאם אתם קוראים או עורכים אותו.
* '''שיפורים בסרגל העריכה''': סידרנו מחדש את סרגל העריכה כדי שיהיה קל יותר להשתמש בו. עכשיו עיצוב דפים פשוט ואינטואיטיבי יותר.
* '''אשף קישורים''': כלי קל לשימוש שמאפשר לכם להוסיף קישורים לדפים אחרים בוויקיפדיה וגם קישורים לאתרים חיצוניים.
* '''שיפורים בחיפוש''': שיפרנו את ההשלמות בתיבת החיפוש כדי שתמצאו את הדף שאותו אתם מחפשים מהר יותר.
* '''תכונות חדשות אחרות''': הוספנו גם אשף טבלאות כדי שיהיה קל יותר ליצור טבלאות ויכולת חיפוש והחלפה כדי להקל על עריכת דפים.
* '''סמל פאזל הגלובוס של ויקיפדיה''': עדכנּו את הסמל של ויקיפדיה עם פאזל הגלובוס. למידע נוסף ראו את [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/  הבלוג של ויקימדיה] (באנגלית).",
	'prefswitch-main-anon' => '==תחזירו אותי==
אם תרצו לבטל את התכונות החדשות, [$1 לחצו כאן]. לפני כן תתבקשו להיכנס לחשבונכם או ליצור חשבון.',
	'prefswitch-main-on' => '===תחזירו אותי!===
אם תרצו לכבות את התכונות החדשות, אנא [$2 לחצו כאן].',
	'prefswitch-main-off' => '===נסו אותן!===
אם תרצו להפעיל את התכונות החדשות, אנא [$1 לחצו כאן].',
	'prefswitch-survey-intro-feedback' => 'נשמח לשמוע מכם.
אנא מלאו את הסקר שלהלן (לא חובה) לפני שאתם לוחצים על "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'תודה שניסיתם את התכונות החדשות שלנו.
כדי לעזור לנו לשפר אותן, אנא מלאו את הסקר שלהלן (לא חובה) לפני שאתם לוחצים על "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'prefswitch' => 'Přepinanje za nastajenja iniciatiwy wužiwajomnosće',
	'prefswitch-desc' => 'Wužiwarjam dowolić, sadźby nastajenjow přepinać',
	'prefswitch-survey-true' => 'Haj',
	'prefswitch-survey-false' => 'Ně',
	'prefswitch-survey-submit-off' => 'Nowe funkcije wupinyć',
	'prefswitch-survey-cancel-off' => 'Jeli by rady nowe funkcije dale wužiwał, móžeš so k $1 wróćić.',
	'prefswitch-survey-submit-feedback' => 'Měnjenje pósłać',
	'prefswitch-survey-cancel-feedback' => 'Jeli nochceš měnjenje dodać, móžeš so do $1 wróćić.',
	'prefswitch-survey-question-like' => 'Što sej ći na nowych funkcijach spodoba?',
	'prefswitch-survey-question-dislike' => 'Što sej ći na nowych funkcijach njespodoba?',
	'prefswitch-survey-question-whyoff' => 'Čehodla wupinaš nowe funkcije?
Prošu wubjer wšě, kotrež maja so nałožić.',
	'prefswitch-survey-answer-whyoff-hard' => 'Njeda so lochko wužiwać.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Njeje porjadnje fungowało.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Njefunguje na předwidźomne wašnje.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Napohlad so mi njespodoba.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Nowe rajtarki a wuhotowanje njejsu so mi lubili.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Nowa gratowa lajsta njeje so mi njespodobała.',
	'prefswitch-survey-answer-whyoff-other' => 'Druha přičina:',
	'prefswitch-survey-question-browser' => 'Kotry wobhladowak wužiwaš?',
	'prefswitch-survey-answer-browser-other' => 'Druhi wobhladowak:',
	'prefswitch-survey-question-os' => 'Kotry dźěłowy system wužiwaš?',
	'prefswitch-survey-answer-os-other' => 'Druhi dźěłowy system:',
	'prefswitch-survey-question-res' => 'Kotre je rozeznaće twojeje wobrazowki?',
	'prefswitch-title-on' => 'Nowe funkcije',
	'prefswitch-title-switched-on' => 'Wjesel so!',
	'prefswitch-title-off' => 'Nowe funkcije wupinyć',
	'prefswitch-title-switched-off' => 'Dźakujemy so',
	'prefswitch-title-feedback' => 'Rezonanca',
	'prefswitch-success-on' => 'Nowe funkcije su nětko zapinjene. Nadźijamy so, zo wjeseliš so nad nowymi funkcijemi. Móžeš je kóždy čas přez kliknjenje na wotkaz "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" horjeka na stronje wupinyć.',
	'prefswitch-success-off' => 'Nowe funkcije su wupinjene. Dźakujemy so, zo sy nowe funkcije testował. Móžeš je kóždy čas přez kliknjenje na wotkaz "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" horjeka na stronje zapinyć.',
	'prefswitch-success-feedback' => 'Twoje měnjenje je so pósłało.',
	'prefswitch-return' => '<hr style="clear:both">
Wróćo do <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-de.png|401px|]]
|-
| Wobrazowkowe foto nawigaciskeho powjercha Wikipedije <small>[[Media:VectorNavigation-de.png|(powjetšić)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-de.png|401px|]]
|-
| Wobrazkowe foto zakładneho wobdźěłowanskeho powjercha <small>[[Media:VectorEditorBasic-de.png|(powjetšić)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-de.png|401px|]]
|-
| Wobrazowkowe foto noweho dialogoweho kašćika za zapodawanje wotkazow
|}
|}
Dźěłamy ćežko, zo bychmy wěcy za swojich wužiwarjow wosnadnili. Bychmy so wjeselili, so wo polěpšenjach wuměnić, inkluziwnje nowy napohlad a zjednorjene wobdźěłowanske funkcije. Polěpšenje wužiwajomnosće našich projektow je priorita załožby Wikimedia Foundation a rozdźělimy wjace aktualizacijow w přichodźe. Za dalše podrobnosće wopytaj wotpowědny póst na [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ Wikimedia blogu]

===Tole smy změnili===
* '''Nawigacija:''' Smy nawigaciju za čitanje a wobdźěłowanje stronow polěpšili. Nětko rajtarki horjeka na stronje móžeja jasnišo definować, hač sej nastawk abo diskusijnu stronu wobhladuješ, a hač čitaš abo wobdźěłuješ stronu.
* '''Polěpšenja wobdźěłowanskeje lajsty:''' Smy wobdźěłowansku lajstu přeorganizował, zo by so dała lóšo wužiwać. Nětko je formatowanje stronow jednoriše a bóle intuitiwne.
* '''Wotkazowy asistent:''' Nastroj, kotryž da so lochko wužiwać a ći dowola, wotkazy druhim wikistronam  kaž tež eksternym sydłam přidać.
* '''Pytanske polěpšenja:''' Smy pytanske namjety polěpšili, zo bychmy će spěšnišo k tej stronje wjedli, kotruž pytaš.
* '''Druhe nowe funkcije:''' Smy tež tabelowy asistent zawjedli, zo bychmy wutworjenje tabelow wosnadnili a funkciju za pytanje a narunanje, zo bychmy wobdźěłowanje strony zjednorili.
* '''Logo wikipedije logo:''' Smy swoje logo zaktualizowali. Dalše informacije wo tym na [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d blogu Wikimedije].

===Měnjenja?===
Bychmy so wjeselili wot tebje słyšeć. Prošu wopytaj našu [[\$1|stronu komentarow]] abo, jeli zajimuješ za naše běžne napinanja softwaru polěpšić, wopytaj našu [http://usability.wikimedia.org wiki wužiwajomnosće] za dalše informacije.",
	'prefswitch-main-anon' => '===Wróćo===
Jeli chceće nowe funkcije znjemóžnić, [$1 klikńće tu].  Proša was, so přizjewić abo najprjedy konto załožić.',
	'prefswitch-main-on' => '===Přinjes mje wróćo!===
[$2 Klikń tu, zo by nowe funkcije wupinyć].',
	'prefswitch-main-off' => '===Wupruwuj je!===
Jeli chceš nowe funkcije zapinyć,  [$1 klikń prošu tu].',
	'prefswitch-survey-intro-feedback' => 'Bychmy so wjesleli, wot tebje słyšeć.
Prošu wupjelń slědowace opcionelne poszudźenje, prjedy hač kliknješ na "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Dźakujemy so za wupruwowanje našich nowych funkcijow.
Zo by nam pomhał, je polěpšić, wupjelń prošu slědowace opcionelne posudźenje, prjedy hač kliknješ na "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Project:Iniciatiwa wužiwajomnosće - komentary',
);

/** Hungarian (Magyar)
 * @author Glanthor Reviol
 * @author Tgr
 */
$messages['hu'] = array(
	'prefswitch' => 'Usability Initiative beállítás-váltó',
	'prefswitch-desc' => 'Lehetővé teszi a felhasználóknak a különböző beállítások közötti váltást',
	'prefswitch-survey-true' => 'Igen',
	'prefswitch-survey-false' => 'Nem',
	'prefswitch-survey-submit-off' => 'Új funkciók kikapcsolása',
	'prefswitch-survey-cancel-off' => 'Ha továbbra is szeretnéd használni az új funkciókat, akkor visszatérhetsz a(z) $1 lapra.',
	'prefswitch-survey-submit-feedback' => 'Visszajelzés küldése',
	'prefswitch-survey-cancel-feedback' => 'Ha nem akarod megosztani a tapasztalataidat, visszatérhetsz a(z) $1 lapra.',
	'prefswitch-survey-question-like' => 'Mi tetszett az új funkciókból?',
	'prefswitch-survey-question-dislike' => 'Mi nem tetszett az új funkciókból?',
	'prefswitch-survey-question-whyoff' => 'Miért kapcsolod ki az új funkciókat?
Jelöld be az összes indokodat.',
	'prefswitch-survey-answer-whyoff-hard' => 'Túl nehéz volt használni.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Nem működött megfelelően.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Nem működött kiszámíthatóan.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Nem tetszik, ahogy kinéz.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Nem tetszenek az új fülek és az elrendezés.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Nem tetszik az új szerkesztő-eszköztár.',
	'prefswitch-survey-answer-whyoff-other' => 'Egyéb indok:',
	'prefswitch-survey-question-browser' => 'Melyik böngészőt használod?',
	'prefswitch-survey-answer-browser-other' => 'Más böngésző:',
	'prefswitch-survey-question-os' => 'Melyik operációs rendszert használod?',
	'prefswitch-survey-answer-os-other' => 'Más operációs rendszer:',
	'prefswitch-survey-question-res' => 'Milyen felbontású a képernyőd?',
	'prefswitch-title-on' => 'Új funkciók',
	'prefswitch-title-switched-on' => 'Jó szórakozást!',
	'prefswitch-title-off' => 'Új funkciók kikapcsolása',
	'prefswitch-title-switched-off' => 'Köszönjük',
	'prefswitch-title-feedback' => 'Visszajelzés',
	'prefswitch-success-on' => 'Az új funkciók be vannak kapcsolva. Reméljük, hogy jó használni az újdonságokat. Bármikor kikapcsolhatod őket a lap tetején található „[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]” gombra kattintva.',
	'prefswitch-success-off' => 'Az új funkciók ki vannak kapcsolva. Köszönjük hogy kipróbáltad őket. Bármikor visszakapcsolhatod őket a lap tetején található „[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]” gombra kattintva.',
	'prefswitch-success-feedback' => 'A visszajelzésed el lett küldve.',
	'prefswitch-return' => '<hr style="clear:both">
Vissza a(z) <span class="plainlinks">[$1 $2]</span> lapra.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-hu.png|401px|]]
|-
| A Wikipédia új navigációs felületének képe <small>[[Media:VectorNavigation-hu.png|(nagyítás)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-hu.png|401px|]]
|-
| Az alap szerkesztőfelület képe <small>[[Media:VectorEditorBasic-hu.png|(nagyítás)]]</small>
|}
|-
| align=\"center\" |ám
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-hu.png|401px|]]
|-
| A hivatkozások megadására szolgáló új párbeszédablak képe
|}
|}
A Wikimédia Alapítány felhasználói élményért felelős csapata és az őket segítő önkéntesek azon dolgoztak, hogy könnyebbé tegyék számodra a wiki használatát. Örömünkre szolgál, hogy bemutathatjuk e munka néhány eredményt, köztük egy új kinézetet és egyszerűsített szerkesztőfelületet. A változások célja az új szerkesztők bekapcsolódásának megkönnyítése, [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study a tavalyi használhatósági tesztelés] tanulságai alapján. Weboldalaink könnyű használhatósága fontos cél a Wikimédia Alapítványnak, ezért a jövőben további változatásokra számíthatsz. Részletesebben [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia a kapcsolódó blogposztban] olvashatsz.

=== Here's what we have changed ===
* '''Navigation:''' We have improved the navigation for reading and editing pages. Now, the tabs at the top of each page more clearly define whether you are viewing the page or discussion page, and whether you are reading or editing a page.
* '''Editing toolbar improvements:''' We have reorganized the editing toolbar to make it easier to use. Now, formatting pages is simpler and more intuitive.
* '''Link wizard:''' An easy-to-use tool allows you to add links to other wiki pages as well as links to external sites.
* '''Search improvements:''' We have improved search suggestions to get you to the page you are looking for more quickly.
* '''Other new features:''' We have also introduced a table wizard to make creating tables easier and a find and replace feature to simplify page editing.
* '''Wikipedia logo:''' We have updated our logo. Read more at the [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d Wikimedia blog].

===Feedback?===
We would love to hear from you. Please visit our [[\$1|feedback page]] or, if you are interested in our ongoing efforts to improve the software, visit our [http://usability.wikimedia.org usability wiki] for more information.",
	'prefswitch-main-anon' => '===Vissza a régit===
[$1 Kattints ide az új funkciók kikapcsolásához]. Ehhez be kell jelentkezned, vagy regisztrálnod kell.',
	'prefswitch-main-on' => '===Vissza a régit===
[$2 Kattints ide az új funkciók kikapcsolásához].',
	'prefswitch-main-off' => '=== Próbáld ki! ===

Ha szeretnéd bekapcsolni az új funkciókat, [$1 kattints ide].',
	'prefswitch-survey-intro-feedback' => 'Örülnénk, ha elmondanád a véleményed.
Légyszíves töltsd ki az alábbi önkéntes kérdőívet, mielőtt a „[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]”  gombra kattintanál.',
	'prefswitch-survey-intro-off' => 'Köszönjük, hogy kipróbáltad az új funkciókat.
Légyszíves segíts a továbbfejlesztésükben az alábbi önkéntes kérdőív kitöltésével, mielőtt az „[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]”-ra kattintanál.',
	'prefswitch-feedbackpage' => 'Project:Felhasználói visszajelzés',
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
	'prefswitch-survey-question-like' => 'Que appreciava tu in le nove functionalitate?',
	'prefswitch-survey-question-dislike' => 'Que non appreciava tu in le nove functionalitate?',
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
	'prefswitch-title-on' => 'Nove functionalitate',
	'prefswitch-title-switched-on' => 'Bon divertimento!',
	'prefswitch-title-off' => 'Disactivar le nove functiones',
	'prefswitch-title-switched-off' => 'Gratias',
	'prefswitch-title-feedback' => 'Tu opinion',
	'prefswitch-success-on' => 'Le nove functiones es ora active. Nos spera que illos te placera. Tu pote sempre disactivar los con un clic super le ligamine "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" in alto del pagina.',
	'prefswitch-success-off' => 'Le nove functiones non es plus active. Gratias pro haber essayate los. Tu pote sempre reactivar los con un clic super le ligamine "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" in alto del pagina.',
	'prefswitch-success-feedback' => 'Tu opinion ha essite inviate.',
	'prefswitch-return' => '<hr style="clear:both">
Retornar a <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| Un captura del nove interfacie de navigation de Wikipedia <small>[[Media:VectorNavigation-en.png|(aggrandir)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| Un captura del interfacie basic pro modificar paginas <small>[[Media:VectorEditorBasic-en.png|(aggrandir)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| Un captura del nove fenestra de dialogo pro entrar ligamines
|}
|}
Le Equipa del Experientia de Usator del Fundation Wikimedia ha collaborate con voluntarios del communitate pro render le cosas plus facile pro te. Nos es enthusiasta de demonstrar alcun meliorationes, como un nove apparentia e functiones de modification simplificate. Iste cambios ha le scopo facer que le nove contributores pote comenciar plus facilemente, e es basate in nostre [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study essayos de usabilitate conducite durante le ultime anno]. Meliorar le usabilitate de nostre projectos es un prioritate del Fundation Wikimedia e nos monstrara altere actualisationes in le futuro. Pro ulterior detalios, visita le [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia articulo relevante] del blog de Wikimedia (in anglese).

===Ecce lo que nos ha cambiate===
* '''Navigation:''' Nos ha meliorate le systema de navigation pro leger e modificar paginas. Ora, le schedas in alto de cata pagina indica plus clarmente si tu vide le articulo o su pagina de discussion, e si tu lege o modifica le pagina.
* '''Meliorationes del instrumentario de modification:'''  Nos ha reorganisate le barra de instrumentos de modification pro render lo plus facile de usar. Ora, formatar paginas es plus simple e intuitive.
* '''Assistente pro ligamines:''' Un instrumento simple permitte adder ligamines a altere paginas de Wikipedia e ligamines a sitos externe.
* '''Meliorationes de recerca:''' Nos ha meliorate le suggestiones de recerca pro portar te plus rapidemente al pagina que tu cerca.
* '''Altere nove functiones:''' Nos ha etiam introducite un assistente pro facilitar le creation de tabellas, e un function de cercar e reimplaciar pro simplificar le modification de paginas.
* '''Le logotypo de Wikipedia''': Nos ha actualisate le logotypo. Lege plus in le [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ blog de Wikimedia] (in anglese).

===Commentarios?===
Nos amarea audir de te. Per favor visita nostre [[\$1|pagina pro dar tu opinion]] o, si tu ha interesse in nostre continue effortios pro meliorar le software, visita nostre [http://usability.wikimedia.org wiki de usabilitate] pro plus information.",
	'prefswitch-main-anon' => '===Porta me retro===
Si tu vole disactivar le nove functiones, [$1 clicca hic]. Il te essera demandate de primo aperir un session o crear un conto.',
	'prefswitch-main-on' => '===Porta me retro!===
[$2 Clicca hic pro disactivar le nove functiones].',
	'prefswitch-main-off' => '===Proba los!===
Si tu vole activar le nove functiones, per favor [$1 clicca hic].',
	'prefswitch-survey-intro-feedback' => 'Nos amarea cognoscer tu opinion.
Per favor completa le questionario facultative hic infra ante de cliccar super "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Gratias pro haber probate le nove functiones.
Pro adjutar nos a meliorar los, per favor completa le questionario facultative hic infra ante de cliccar super "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Project:Commentario re usabilitate',
);

/** Indonesian (Bahasa Indonesia)
 * @author Kenrick95
 */
$messages['id'] = array(
	'prefswitch-survey-true' => 'Ya',
	'prefswitch-survey-false' => 'Tidak',
	'prefswitch-survey-submit-off' => 'Matikan fitur baru',
	'prefswitch-survey-cancel-off' => 'Jika Anda ingin terus menggunakan fitur baru ini, Anda dapat kembali ke $1.',
	'prefswitch-survey-submit-feedback' => 'Kirim umpan balik',
	'prefswitch-survey-cancel-feedback' => 'Jika Anda tidak ingin memberikan umpan balik, Anda dapat kembali ke $1.',
	'prefswitch-survey-question-like' => 'Apa yang Anda sukai tentang fitur baru ini?',
	'prefswitch-survey-question-dislike' => 'Apa yang Anda tidak sukai tentang fitur baru ini?',
	'prefswitch-survey-question-whyoff' => 'Mengapa Anda mematikan fitur baru ini?
Harap pilih semua yang benar.',
	'prefswitch-survey-answer-whyoff-hard' => 'Terlalu sulit untuk digunakan.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Tidak berfungsi dengan baik.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Saya tidak menyukai kotak peralatan yang baru.',
	'prefswitch-survey-answer-whyoff-other' => 'Alasan lainnya:',
	'prefswitch-survey-question-browser' => 'Penjelajah web apa yang Anda gunakan?',
	'prefswitch-survey-answer-browser-other' => 'Penjelajah web lainnya:',
	'prefswitch-survey-question-os' => 'Sistem operasi apa yang Anda gunakan?',
	'prefswitch-survey-answer-os-other' => 'Sistem operasi lainnya:',
	'prefswitch-survey-question-res' => 'Berapa besar resolusi layar Anda?',
	'prefswitch-title-on' => 'Fitur baru',
	'prefswitch-title-switched-on' => 'Selamat menikmati!',
	'prefswitch-title-off' => 'Matikan fitur baru',
	'prefswitch-title-switched-off' => 'Terima kasih',
	'prefswitch-title-feedback' => 'Umpan balik',
	'prefswitch-success-on' => 'Fitur baru sekarang telah dihidupkan.',
	'prefswitch-success-off' => 'Fitur baru sekarang telah dimatikan.',
	'prefswitch-success-feedback' => 'Umpan balik Anda telah terkirim.',
	'prefswitch-return' => '<hr style="clear:both">
Kembali ke <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main-anon' => '=== Bawa saya kembali === 
[$1 Klik disini untuk menonaktifkan fitur baru]. Anda akan diminta untuk masuk log atau membuat akun terlebih dahulu.',
);

/** Ido (Ido)
 * @author Malafaya
 */
$messages['io'] = array(
	'prefswitch-survey-true' => 'Yes',
	'prefswitch-survey-false' => 'No',
	'prefswitch-survey-submit-off' => 'Supresez nova funcioni',
	'prefswitch-survey-answer-whyoff-other' => 'Altra motivo:',
	'prefswitch-title-on' => 'Nova funcioni',
	'prefswitch-title-switched-on' => 'Juez!',
	'prefswitch-title-off' => 'Supresez nova funcioni',
	'prefswitch-title-switched-off' => 'Danko',
);

/** Italian (Italiano)
 * @author Beta16
 * @author Nemo bis
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
	'prefswitch-survey-question-like' => 'Cosa ti è piaciuto delle nuove funzionalità?',
	'prefswitch-survey-question-dislike' => 'Cosa non ti è piaciuto delle nuove funzionalità?',
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
	'prefswitch-title-on' => 'nuove funzionalità',
	'prefswitch-title-switched-on' => 'Buon divertimento!',
	'prefswitch-title-off' => 'Disattiva le nuove funzioni',
	'prefswitch-title-switched-off' => 'Grazie',
	'prefswitch-title-feedback' => 'Feedback',
	'prefswitch-success-on' => 'Le nuove funzionalità sono attive. Ci auguriamo che ti piacciano. Puoi sempre disattivarle cliccando sul link "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" sulla parte superiore della pagina.',
	'prefswitch-success-off' => 'Le nuove funzionalità sono state disattivate. Grazie per averle provate. Puoi sempre riattivarle cliccando sul link "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" nella parte superiore della pagina.',
	'prefswitch-success-feedback' => 'Il tuo feedback è stato inviato.',
	'prefswitch-return' => '<hr style="clear:both">
Torna a <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-it.png|401px|]]
|-
| Immagine della nuova interfaccia Vector <small>[[Media:VectorNavigation-it.png|(ingrandisci)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-it.png|401px|]]
|-
| Immagine della nuova interfaccia di modifica di base <small>[[Media:VectorEditorBasic-it.png|(ingrandisci)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-it.png|401px|]]
|-
| Immagine delle nuove finestre per inserire collegamenti
|}
|}
Lo \"User Experience Team\" della Fondazione Wikimedia, insieme ai volontari della comunità, ha lavorato duramente per rendere le cose più semplici per te. Siamo entusiasti di annunciare alcuni miglioramenti, tra cui un nuovo aspetto grafico e delle funzioni di modifica semplificate.  Migliorare l'usabilità dei progetti wiki è una priorità della Fondazione Wikimedia, e daremo altri aggiornamenti in futuro. Per maggiori dettagli, visita il relativo articolo del [http://www.frontieredigitali.it/online/?p=1703 blog Wikimedia]. 
===Ecco cosa abbiamo cambiato===
* '''Navigazione''': Abbiamo migliorato il sistema di navigazione per leggere e modificare voci. Adesso, le schede nella parte superiore di ogni voce indicano più chiaramente se stai visualizzando la voce o la pagina di discussione, e se stai leggendo o modificando una voce.
* '''Miglioramenti alla barra degli strumenti''':  Abbiamo riorganizzato la barra degli strumenti di modifica per renderla più semplice da usare.  Adesso, formattare le voci è più semplice e intuitivo.
* '''Procedura guidata per i link''':  Uno strumento semplice da utilizzare ti permette di aggiungere link ad altre pagine di Wikipedia e link a siti esterni.
* '''Miglioramenti alla ricerca''': Abbiamo migliorato i suggerimenti della ricerca per portarti più velocemente alla pagina che stai cercando.
* '''Altre nuove funzioni''':  Abbiamo introdotto anche una procedura guidata per le tabelle per rendere la loro creazione più semplice e una funzione \"trova e sostituisci\" per semplificare la modifica delle pagine.
* '''Globo-puzzle di Wikipedia''': Abbiamo aggiornato il globo-puzzle. Leggi altre informazioni sul [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ blog Wikimedia].

===Commenti?===
Non vediamo l'ora di conoscere la tua opinione. Visita la nostra [[\$1|pagina di feedback]] oppure, se sei interessato nei nostri continui sforzi per migliorare la piattaforma MediaWiki, visita [http://usability.wikimedia.org la wiki del progetto usabilità] per ulteriori informazioni.",
	'prefswitch-main-anon' => '===Riportami indietro===
Se vuoi disattivare le nuove funzionalità, [$1 clicca qui]. Ti sarà chiesto di entrare o di creare un account.',
	'prefswitch-main-on' => '===Riportami indietro===
[$2 Clicca qui per disattivare le nuove funzionalità].',
	'prefswitch-main-off' => '===Provale!===
Se vuoi attivare le nuove funzioni, [$1 clicca qui].',
	'prefswitch-survey-intro-feedback' => 'Ci piacerebbe ascoltare il tuo parere.
Per favore, compila il seguente sondaggio facoltativo prima di cliccare "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Grazie per aver provato le nostre nuove funzioni.
Per aiutarci a migliorarle, per favore riempi il seguente questionario facoltativo prima di fare clic su "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Project:Coordinamento/Usabilità',
);

/** Japanese (日本語)
 * @author Aotake
 * @author Fryed-peach
 * @author 青子守歌
 */
$messages['ja'] = array(
	'prefswitch' => '使用性改善の設定スイッチ',
	'prefswitch-desc' => '利用者が個人設定の組み合わせを切り替えられるようにする',
	'prefswitch-survey-true' => 'はい',
	'prefswitch-survey-false' => 'いいえ',
	'prefswitch-survey-submit-off' => '新機能を停止する',
	'prefswitch-survey-cancel-off' => '新機能の使用を継続したい場合は、$1に戻ってください。',
	'prefswitch-survey-submit-feedback' => 'フィードバックを送る',
	'prefswitch-survey-cancel-feedback' => 'フィードバックを提供したくない場合は、$1に戻ってください。',
	'prefswitch-survey-question-like' => '新機能のどの点が気に入りましたか？',
	'prefswitch-survey-question-dislike' => '新機能のどの点が気に入りませんでしたか？',
	'prefswitch-survey-question-whyoff' => '新機能を停止する理由をお聞かせください。
あてはまるものを全てお選びください。',
	'prefswitch-survey-answer-whyoff-hard' => '使用方法が難しすぎた。',
	'prefswitch-survey-answer-whyoff-didntwork' => '正常に機能しなかった。',
	'prefswitch-survey-answer-whyoff-notpredictable' => '動作が予測不能だった。',
	'prefswitch-survey-answer-whyoff-didntlike-look' => '見た目が好きではなかった。',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => '新しいタブやレイアウトが好きではなかった。',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => '新しいツールバーが好きではなかった。',
	'prefswitch-survey-answer-whyoff-other' => 'その他の理由:',
	'prefswitch-survey-question-browser' => 'ご利用のブラウザをお答えください。',
	'prefswitch-survey-answer-browser-other' => 'その他のブラウザ:',
	'prefswitch-survey-question-os' => 'ご利用のOSをお答えください。',
	'prefswitch-survey-answer-os-other' => 'その他のオペレーティングシステム:',
	'prefswitch-survey-question-res' => 'ご使用中の画面の解像度をお答えください。',
	'prefswitch-title-on' => '新機能',
	'prefswitch-title-switched-on' => 'お楽しみください！',
	'prefswitch-title-off' => '新機能を停止する',
	'prefswitch-title-switched-off' => 'ありがとうございました',
	'prefswitch-title-feedback' => 'フィードバック',
	'prefswitch-success-on' => '新機能を有効にしました。新機能をお楽しみください。元に戻したいときは、ページ一番上の「[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]」をクリックしてください。',
	'prefswitch-success-off' => '新機能を停止しました。新機能をお試しいただきありがとうございました。元に戻したいときは、ページ一番上の「[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]」をクリックしてください。',
	'prefswitch-success-feedback' => 'フィードバックが送信されました。',
	'prefswitch-return' => '<hr style="clear:both">
<span class="plainlinks">[$1 $2]</span>に戻る。',
	'prefswitch-main' => "私たちは、より使いやすいサイトを利用者の皆さまにお届けするために努力してまいりました。そして新しい外観や使い勝手、わかりやすくなった編集機能などの改善を皆さまと共有できることを大変うれしく思っています。私たちのプロジェクトの使いやすさを改善することは、ウィキメディア財団の優先課題の一つであり、今後も更なるアップデートを実施していく予定です。詳細は、[http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ Wikimediaブログ]の関連投稿（英語）をご覧ください。

[[File:VectorNavigation-ja.png|right|link=|新しいナビゲーションのスクリーンショット]]
[[File:VectorEditorBasic-ja.png|right|link=|拡張編集ツールバーのスクリーンショット]]
[[File:VectorLinkDialog-ja.png|right|link=|新しいコンテンツ生成ダイアログのスクリーンショット]]
===今回の変更点は以下の通りです===
* '''ナビゲーション:''' ページの閲覧・編集時のナビゲーションを改善しました。各ページの上部のタブは、閲覧中の画面がページ本体なのかそのトークページ（ノートページ）なのか、あるいは現在閲覧中なのか編集中なのかをよりはっきりと示すようになりました。
* '''編集ツールバーの改善:''' 編集ツールバーを再編して、より使いやすくしました。ページの整形がより簡単に、かつ直感的に行なえるようになっています。
* '''リンクウィザード:''' ウィキペディア内の他のページや外部サイトへのリンクを追加できる、使いやすいツールを備えました。
* '''検索機能の改善:''' 検索結果の候補予想の提示を改善し、お探しのページにより素早くたどり着けるようにしました。
* '''その他の新機能:''' その他にも、ページ編集を簡潔化するために、表の作成を簡単にする表ウィザード、検索・置換機能を追加しました。
* '''ウィキペディアのパズル地球儀''': パズル地球儀が更新されました。詳細は[http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ Wikimedia blog]（英語）をご覧ください。",
	'prefswitch-main-anon' => '===以前の状態に戻す===
新機能の数々を停止したい場合、[$1 こちらをクリック]してください。まずログインするかアカウントを作るか尋ねられます。',
	'prefswitch-main-on' => '===もとに戻す===
新機能を停止したい場合は、[$2 ここをクリック]してください。',
	'prefswitch-main-off' => '===お試しください！===
新機能を有効にしたい場合は、[$1 ここをクリック]してください。',
	'prefswitch-survey-intro-feedback' => 'ご意見をぜひお聞かせください。
「[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]」をクリックする前に、下記の調査に任意でご協力ください。',
	'prefswitch-survey-intro-off' => '新機能をお試しいただきありがとうございます。
更なる改善のために、「[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]」をクリックする前に下記の調査に任意でご協力ください。',
	'prefswitch-feedbackpage' => 'Project:WUI/F',
);

/** Georgian (ქართული)
 * @author BRUTE
 * @author ITshnik
 * @author გიორგიმელა
 */
$messages['ka'] = array(
	'prefswitch' => 'გამოყენებადობის ინიციატივის კონფიგურაციის გადართვა',
	'prefswitch-desc' => 'აძლევს ნებართვას მომხმარებლებს გადართონ კონფიგურაცია',
	'prefswitch-survey-true' => 'ჰო',
	'prefswitch-survey-false' => 'არა',
	'prefswitch-survey-submit-off' => 'გათიშეთ ახალი შესაძლებლობები',
	'prefswitch-survey-cancel-off' => 'თუ გსურთ გააგრძელოთ ბეტას გამოყენება, შეგიძლიათ დაბრუნდეთ  $1-ზე.',
	'prefswitch-survey-submit-feedback' => 'გამოხმაურება',
	'prefswitch-survey-cancel-feedback' => 'თუ არ გსურთ პროტოტიპზე გამოხმაურების დატოვება, უბრალოდ დაბრუნდით $1.',
	'prefswitch-survey-question-like' => 'რა მოგეწონათ ახალ შესაძლებლობებში?',
	'prefswitch-survey-question-dislike' => 'რა არ მოგეწონათ ახალ შესაძლებლობებში?',
	'prefswitch-survey-question-whyoff' => 'რატომ თიშავთ ახალ შესაძლებლობებს? 
გთხოვთ აირჩიოთ პასუხი.',
	'prefswitch-survey-answer-whyoff-hard' => 'ძალიან რთული იყო გამოსაყენებლად.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'ის გაუმართავი იყო.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'იგი გაუთვალისწინებლად მოქმედებდა.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'არ მომწონდა მისი გარეგნობა.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'არ მომეწონა ახალი ყუები და განლაგება.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'მე არ მომეწონა რედაქტირების პანელი.',
	'prefswitch-survey-answer-whyoff-other' => 'სხვა მიზეზი:',
	'prefswitch-survey-question-browser' => 'რომელ ბრაუზერს იყენებთ?',
	'prefswitch-survey-answer-browser-other' => 'სხვა ბრაუზერი:',
	'prefswitch-survey-question-os' => 'რომელ ოპერაციულ სისტემას იყენებთ?',
	'prefswitch-survey-answer-os-other' => 'სხვა ოპერაციული სისტემა:',
	'prefswitch-survey-question-res' => 'თქვენი მონიტორის გაფართოება:',
	'prefswitch-title-on' => 'ახალი შესაძლებლობები',
	'prefswitch-title-switched-on' => 'ისიამოვნეთ!',
	'prefswitch-title-off' => 'გათიშეთ ახალი შესაძლებლობები',
	'prefswitch-title-switched-off' => 'მადლობა',
	'prefswitch-title-feedback' => 'კონტაქტი',
	'prefswitch-success-on' => 'ახალი შესაძლებლობები ჩაირთო. ვიმედოვნებთ, ისიამოვნებთ მათი გამოყენებით. თქვენ ყოველთვის შეგიძლიათ გამორთათ ისინი ბმულზე "[[სპეციალური: UsabilityInitiativePrefSwitch | ((int: prefswitch-link-on }}]]" დაწკაპუნებით, რომელიც ამ გვერდის თავში მდებარეობს.',
	'prefswitch-success-off' => 'ახალი ფუნქციები ამჟამად გამორთულია. მადლობთ ახალი შესაძლებლობების მოსინჯვისათვის. თქვენ ყოველთვის შეგიძლიათ ჩართათ ისინი ბმულზე "[[სპეციალური: UsabilityInitiativePrefSwitch | ((int: prefswitch-link-off }}]]" დაწკაპებით, რომელიც ამ გვერდის თავში მდებარეობს.',
	'prefswitch-success-feedback' => 'თქვენი გამოხმაურება გაგზავნილია.',
	'prefswitch-return' => '<hr style="clear:both">
დაბრუნება <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main-anon' => '===დაბრუნება===
თუ გსურთ გამორთოთ ბეტა, [$1 დააჭირეთ აქ]. შემდეგ კი გაიარეთ ავტორიზაცია ან შექმენით ანგარიში.',
	'prefswitch-main-off' => '=== მოსინჯეთ! === 
თუ გსურთ, რომ ჩართათ ახალი ფუნქციები, [$ 1 დააწკაპეთ აქ].',
	'prefswitch-feedbackpage' => 'ვიკიპედია:ბეტა/ახალი შესაძლებლობები/გამოხმაურებები',
);

/** Korean (한국어)
 * @author Devunt
 * @author Kwj2772
 */
$messages['ko'] = array(
	'prefswitch' => 'Usability Initiative 환경 설정 바꾸기',
	'prefswitch-desc' => '사용자가 여러 설정을 한번에 바꿀 수 있도록 함',
	'prefswitch-survey-true' => '예',
	'prefswitch-survey-false' => '아니오',
	'prefswitch-survey-submit-off' => '새로운 기능 끄기',
	'prefswitch-survey-cancel-off' => '새 기능을 계속 사용하시려면, $1로 돌아가실 수 있습니다.',
	'prefswitch-survey-submit-feedback' => '피드백 남기기',
	'prefswitch-survey-cancel-feedback' => '피드백을 제공하고 싶지 않다면 $1로 돌아가시면 됩니다.',
	'prefswitch-survey-question-like' => '새 기능에 대해서 어떤 점이 좋았습니까?',
	'prefswitch-survey-question-dislike' => '새 기능에 대해 어떤 점이 마음에 들지 않으셨습니까?',
	'prefswitch-survey-question-whyoff' => '새로운 기능을 끄는 이유가 무엇인가요?
해당하는 모든 항목을 선택해주세요.',
	'prefswitch-survey-answer-whyoff-hard' => '사용하기에 너무 어렵다.',
	'prefswitch-survey-answer-whyoff-didntwork' => '기능이 제대로 작동하지 않는다.',
	'prefswitch-survey-answer-whyoff-notpredictable' => '기능이 예상한 대로 동작하지 않는다.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => '전체적인 모양이 마음에 들지 않는다.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => '새 탭과 레이아웃이 마음에 들지 않는다.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => '새 툴바가 마음에 들지 않는다.',
	'prefswitch-survey-answer-whyoff-other' => '다른 이유:',
	'prefswitch-survey-question-browser' => '어떤 웹 브라우저를 사용하고 있나요?',
	'prefswitch-survey-answer-browser-other' => '다른 브라우저:',
	'prefswitch-survey-question-os' => '어떤 운영 체제(OS)를 사용하고 있나요?',
	'prefswitch-survey-answer-os-other' => '다른 운영 체제:',
	'prefswitch-survey-question-res' => '어느 정도의 모니터 해상도를 사용하고 있나요?',
	'prefswitch-title-on' => '새 기능',
	'prefswitch-title-switched-on' => '즐겁게 이용하십시오!',
	'prefswitch-title-off' => '새 기능 끄기',
	'prefswitch-title-switched-off' => '감사합니다.',
	'prefswitch-title-feedback' => '피드백',
	'prefswitch-success-on' => '새 기능이 켜졌습니다. 새 기능을 즐겁게 사용할 수 있기를 바랍니다. 페이지의 맨 위에 "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]"를 눌러 새 기능을 끌 수 있습니다.',
	'prefswitch-success-off' => '새 기능이 꺼졌습니다. 새 기능을 사용해 주셔서 감사합니다. 페이지의 맨 위에 있는 "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]"를 눌러 언제든지 새 기능을 켤 수 있습니다.',
	'prefswitch-success-feedback' => '당신의 피드백을 보냈습니다.',
	'prefswitch-return' => '<hr style="clear:both">
<span class="plainlinks">[$1 $2]</span>로 돌아갑니다.',
	'prefswitch-main' => "우리는 사용자를 더욱 편리하게 하기 위해 노력하고 있습니다. 새로운 스킨과 간단해진 편집 기능을 포함한 개선 사항을 보여 주게 되어 영광입니다. 프로젝트 이용을 편리하게 하는 것은 위키미디어 재단의 주 목표이며 나중에 더 많은 업데이트를 제공할 것입니다. 자세한 내용은 [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ 위키미디어 블로그의 게시글]을 참고하십시오.

[[File:UsabilityNavigation.png|right|link=|새로운 둘러보기 탭의 스크린샷]]
[[File:UsabilityToolbar.png|right|link=|향상된 편집 툴바의 스크린샷]]
[[File:UsabilityDialogs.png|right|link=|새로운 편집 대화상자의 스크린샷]]
===새롭게 바뀐 점===
* '''둘러보기''': 문서 읽기와 편집에 대한 둘러보기 메뉴가 향상되었습니다. 각 문서의 상단의 탭이 당신이 일반 문서를 보고 있는지 토론 문서를 보고 있는지, 문서를 읽고 있는지 편집하고 있는지 명확하게 나타내게 됩니다.
* '''편집 툴바 향상''':  편집 툴바를 더욱 사용하기 쉽게 재구성했습니다. 이제 문서를 꾸미는 것이 더욱 간단하고 쉬워질 것입니다.
* '''링크 마법사''':  사용하기 쉬운 도구가 다른 문서나 외부로 링크를 쉽게 걸 수 있도록 도와 줄 것입니다.
* '''검색 기능 향상''': 찾는 문서를 더욱 쉽게 찾을 수 있도록 검색어 제안 기능을 향상시켰습니다.
* '''다른 새로운 기능''':  표를 쉽게 만들 수 있도록 표 마법사와 문서 편집을 간단하게 하기 위해 찾아 바꾸기 기능을 도입했습니다.
* '''위키백과 로고의 퍼즐''': 위키백과 로고의 퍼즐을 업데이트했습니다. [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ 위키미디어 블로그]에서 자세히 알아보십시오.",
	'prefswitch-main-anon' => '===돌아갈래요===
새 기능을 끄기를 원하신다면 [$1 여기]를 클릭해주세요. 로그인하거나 계정을 먼저 생성하여야 합니다.',
	'prefswitch-main-on' => '===돌아갈래요!===
새 기능을 끄기를 원하신다면 [$2 여기]를 클릭해주세요.',
	'prefswitch-main-off' => '===새 기능을 써 보세요!===
새 기능을 켜려면 [$1 여기]를 클릭해주세요.',
	'prefswitch-survey-intro-feedback' => '당신에게 피드백을 받고자 합니다.
"[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]"을 누르기 전에 아래의 설문 조사에 답해주세요.',
	'prefswitch-survey-intro-off' => '새 기능을 사용해 주셔서 감사합니다.
기능을 향상시키는 것을 돕기 위해 "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]"를 누르기 전에 아래 설문 조사에 답해 주세요.',
);

/** Karachay-Balkar (Къарачай-Малкъар)
 * @author Iltever
 */
$messages['krc'] = array(
	'prefswitch-desc' => 'Къошулуучулагъа джарашдырыуланы тюрлендирирге къояды',
	'prefswitch-survey-true' => 'Хоу',
	'prefswitch-survey-false' => 'Огъай',
	'prefswitch-survey-submit-off' => 'Джангы амалланы джукълат',
	'prefswitch-survey-cancel-off' => 'Мындан ары да джангы амалланы хайырланыргъа излей эсегиз, къайтыргъа боллукъсуз: $1.',
	'prefswitch-survey-submit-feedback' => 'Оюмугъузну ийигиз',
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
	'prefswitch-survey-question-like' => 'Wat fannt Dir un den neie Fonctioune gutt?',
	'prefswitch-survey-question-dislike' => 'Wat fannt Dir un den neie Fonctiounen net gutt?',
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
	'prefswitch-title-on' => 'Nei Fonctiounen',
	'prefswitch-title-switched-on' => 'Vill Freed!',
	'prefswitch-title-off' => 'Déi nei Fonctiounen ausschalten',
	'prefswitch-title-switched-off' => 'Merci',
	'prefswitch-title-feedback' => 'Är Reaktioun',
	'prefswitch-success-on' => 'Déi nei Fonctioune sinn elo ageschalt. Mir hoffen Iech gefalen déi nei Fonctiounen. Dir kënnt se ëmmer ofschalten wann Dir op den "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" Link uewen op der Säit klickt.',
	'prefswitch-success-off' => 'Nei Fonctioune sinn elo ausgeschalt. Merci datt Dir déi nei Fonctiounen ausprobéiert hutt. Dir kënnt se ëmmer nees aschalten wann Dir op de Link "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" uewen op der Säit klickt.',
	'prefswitch-success-feedback' => 'Är Reaktioun gouf geschéckt.',
	'prefswitch-return' => '<hr style="clear:both">
Zréck op <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "Mir hu vill geschafft fir et méi einfach fir eis Benotzer ze maachen. Mir si frou fir e puer Verbesserungen ëmzesetzen besonnesch e neie ''look and feel'' a vereinfacht Ännerungsfonctiounen. Verbessere vun der Benotzerfrëndlechkeet vun eise Projete ass eng Prioritéit vun der Wikimedia Foundation a mir wäerte weider Verbesserungen an der Zukunft ëmsetzen. Fir méi Informatiounen, kuckt dëse [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ Wikimedia blog].

[[File:UsabilityNavigation.png|right|link=|Screenshot vun der neier Navigatioun]][[File:UsabilityToolbar.png|right|link=|Screenshot vun der erweiderter Ännerungstoolbar]][[File:UsabilityDialogs.png|right|link=|Screenshot vun den neien Dialogen beim generéieren vun Inhalter]]
===Hei ass dat wat geännert huet===
* '''Navigatioun:''' Mir hunn d'Navigatioun fir Säiten ze liesen an z'änneren verbessert. D'Ongleten uewen op all Säit definéiere méi kloer ob een eng Säit oder eng Diskussiounssäit kuckt an ob een eng Säit kuckt oder ännert.
* '''Verbesserunge vun der Ännerungstoolbar:''' Mir hunn d'Ännerungstoolbar reorganiséiert fir se méi einfach kënnen ze benotzen. Elo ass d'Formatéiere vu Säiten méi einfach a méi intuitiv.
* '''Linkwizard:''' En Tool den einfach ze benotzen ass fir Linken op aner Wikipedia-Säiten a Linken op aner Siten dobäizesetzen.
* '''Verbesserunge bei der Sich:''' Mir hunn d'Virschléi bei der Sich verbessert fir datt Dir déi Säit no där Dir sicht méi séier fannt.
* '''Aner nei Fonctiounen:''' Mir hunn och en Assistent fir Tabellen agefouert deen et méi einfach mécht fir Tabellen unzeleën an eng Sich- an Ersetzungs-Fonctioun fir d'Ännere vu Säiten ze vereinfachen.
* '''Wikipedia Puzzle Globus''': Mir hunn de Puzzle-Globus aktualiséiert, liest méi doriwwer am [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ Wikimedia Blog.]",
	'prefswitch-main-anon' => "=== Zréck ===
Wann Dir déi nei Fonctiounen ausschalte wëllt, 
[$1 klickt hei]. Dir gitt gefrot fir Iech d'éischt anzeloggen oder e Benotzerkont opzemaachen.",
	'prefswitch-main-on' => '===Bréngt mech zréck!===
[$2 klickt w.e.g. hei fir déi nei Fonctiounen auszeschalten].',
	'prefswitch-main-off' => '===Probéiert se aus!===
Wann Dir déi nei Fonctiounen ausprobéiere wëllt, da <span  class="plainlinks">[$1 klickt w.e.g. hei].',
	'prefswitch-survey-intro-feedback' => 'Mir wiere frou vun Iech ze héieren.
Fëllt w.e.g. déi fakultativ Ëmfro hei ënnendrënner aus éier Dir op "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]" klickt.',
	'prefswitch-survey-intro-off' => 'Merci datt dir déi nei Fonctiounen ausprobéiert hutt.
Fir eis ze hëllefen besser ze ginn, fëllt w.e.g. déi fakultativ Ëmfro hei ënnendrënner aus éier dir op "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]" klickt.',
);

/** Latvian (Latviešu)
 * @author Papuass
 */
$messages['lv'] = array(
	'prefswitch-survey-true' => 'Jā',
	'prefswitch-survey-false' => 'Paldies',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 * @author Brest
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
	'prefswitch-survey-question-like' => 'Што ви се допадна кај новите функции?',
	'prefswitch-survey-question-dislike' => 'Што не ви се допадна кај новите функции?',
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
	'prefswitch-title-on' => 'Нови функции',
	'prefswitch-title-switched-on' => 'Уживајте!',
	'prefswitch-title-off' => 'Исклучи нови функции',
	'prefswitch-title-switched-off' => 'Ви благодариме',
	'prefswitch-title-feedback' => 'Примедби',
	'prefswitch-success-on' => 'Новите функции се вклучени. Се надеваме дека ви беше пријатно да ги користите. Секогаш можете повторно да ги исклучите со кликнување на врската „[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]“ на врвот од страницата.',
	'prefswitch-success-off' => 'Новите функции се исклучени. Ви благодариме што ги испробавте. Секогаш можете повторно да ги вклучите со кликнување на врската „[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]“ на врвот од страницата.',
	'prefswitch-success-feedback' => 'Вашите примедби се испратени.',
	'prefswitch-return' => '<hr style="clear:both">
Назад кон <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| Изглед на новиот навигациски посредник на Википедија <small>[[Media:VectorNavigation.png|(зголеми)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| Изглед на основниот посредник за уредување на страници <small>[[Media:VectorEditorBasic.png|(зголеми)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| Изглед на новата кутија за внесување врски
|}
|}
Екипата за корисничко искуство на Фондацијата Викимедија работеше со доброволци од заедницата за да ви ги олесни нештата. Со задоволство ве известуваме за направените подобрувања, новиот изглед и чувство, како и упростените функции за уредување. Овие промени имаат за цел да им го олеснат почетокот на новите учесници, и се засноваат на [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study ланските испитувања на употребливоста]. Подобрувањето на употребливоста на проектите претставува приоритет за Фондацијата Викимедија и во иднина ќе продолжиме да ве известуваме за воведените новини. За повеќе информации, посетете го соодветниот [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia блог-напис на Викимедија].

===Еве што променивме===
* '''Навигација:''' Ја подобривме навигацијата за читање и уредување на страниците. Сега менијата над секоја страница појасно укажуваат на тоа дали ја гледате страницата или страница за разговор, и дали ја читате или уредувате страницата.
* '''Подобрен алатник за уредување:'''  Го реорганизиравме алатникот за полесна употреба. Сега форматирањето на страниците е упростено и поинтуитивно.
* '''Помошник за врски:'''  Едноставна алатка која овозможува додавање врски до други вики-страници како и до надворешни мрежни места.
* '''Подобрено пребарување:''' Ги подобривме предлозите при пребарување за што побрзо да ви ја најдеме страницата што ја барате.
* '''Други нови функции:''' Воведовме и помошник за табели со чија помош табелите се прават полесно, а има и можност за пронаоѓање и заменување, со што се упростува уредувањето на страницата.
* '''Лого на Википедија:''' Го подновивме и логото. Прочитахте повеќе на [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d блогот на Викимедија].

===Мислења?===
Со задоволство би сакале да го чуеме вашето мислење. Посетете ја [[\$1|страницата за мислења]], или пак, ако сте заинтересирани за нашите постојани напори за подобрување на програмот, одете на нашето [http://usability.wikimedia.org вики посветено на употребливоста] и дознајте повеќе.",
	'prefswitch-main-anon' => '===Врати ме===
Ако сакате да ги исклучите новите функции, [$1 кликнете тука]. Ќе ви биде побарано најпрвин да се најавите или да создадете сметка.',
	'prefswitch-main-on' => '===Врати ме!===
Ако сакате да ги исклучите новите функции, [$2 кликнете тука].',
	'prefswitch-main-off' => '===Испробајте ги!===
Ако сакате да ги вклучите новите функции, тогаш [$1 кликнете тука].',
	'prefswitch-survey-intro-feedback' => 'Со задоволство го очекуваме вашето мислење.
Пополнете ја анкетата подолу со тоа што ќе кликнете на „[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]“. (незадолжително)',
	'prefswitch-survey-intro-off' => 'Ви благодариме што ги испробавте новите функции.
За да ни помогнете да ги подобриме, пополнете ја анкетата подолу со тоа што ќе кликнете на „[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]“. (незадолжително)',
	'prefswitch-feedbackpage' => 'Project:Мислења за новиот изглед',
);

/** Malayalam (മലയാളം)
 * @author Junaidpv
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
	'prefswitch-survey-question-like' => 'പുതിയ സവിശേഷതകളിൽ എന്താണ് താങ്കൾക്ക് ഇഷ്ടപ്പെട്ടത്?',
	'prefswitch-survey-question-dislike' => 'സവിശേഷതകളിൽ എന്താണ് താങ്കൾക്ക് ഇഷ്ടപ്പെടാതിരുന്നത്?',
	'prefswitch-survey-question-whyoff' => 'പുതിയ സവിശേഷതകൾ എന്തുകൊണ്ടാണ് താങ്കൾ വേണ്ടന്നു വെയ്ക്കുന്നത്?
ബാധകമാകുന്ന എല്ലാം തിരഞ്ഞെടുക്കുക.',
	'prefswitch-survey-answer-whyoff-hard' => 'ഇത് ഉപയോഗിക്കാൻ ഏറെ ബുദ്ധിമുട്ടായിരുന്നു.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'ഇത് ശരിയായ വിധത്തിൽ പ്രവർത്തിച്ചില്ല.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'ഇത് വിചാരിക്കുന്നതുപോലെയല്ല പ്രവർത്തിക്കുന്നത്.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'ഇത് കാണാൻ ഒരു രസമില്ല.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'പുതിയ റ്റാബുകളും ദൃശ്യവിന്യാസവും എനിക്കിഷ്ടപ്പെട്ടില്ല.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'പുതിയ റ്റൂൾബാർ എനിക്കിഷ്ടപ്പെട്ടില്ല.',
	'prefswitch-survey-answer-whyoff-other' => 'മറ്റ് കാരണം:',
	'prefswitch-survey-question-browser' => 'ഏതു ബ്രൗസറാണ് താങ്കള്‍ ഉപയോഗിക്കുന്നത്?',
	'prefswitch-survey-answer-browser-other' => 'മറ്റ് ബ്രൗസർ:',
	'prefswitch-survey-question-os' => 'ഏത് ഓപറേറ്റിങ്ങ് സിസ്റ്റമാണ് താങ്കള്‍ ഉപയോഗിക്കുന്നത്?',
	'prefswitch-survey-answer-os-other' => 'മറ്റ് ഓപറേറ്റിങ് സിസ്റ്റം:',
	'prefswitch-survey-question-res' => 'താങ്കളുടെ സ്ക്രീന്‍ റെസ‌ല്യൂഷന്‍ എന്താണ്?',
	'prefswitch-title-on' => 'പുതിയ സവിശേഷതകൾ',
	'prefswitch-title-switched-on' => 'ആസ്വദിക്കൂ!',
	'prefswitch-title-off' => 'പുതിയ സവിശേഷതകൾ പ്രവർത്തനരഹിതമാക്കുക',
	'prefswitch-title-switched-off' => 'നന്ദി',
	'prefswitch-title-feedback' => 'അഭിപ്രായങ്ങൾ',
	'prefswitch-success-on' => 'പുതിയ സവിശേഷതകൾ ഇപ്പോൾ പ്രവർത്തനസജ്ജമാണ്. പുതിയ സവിശേഷതകൾ താങ്കളാസ്വദിക്കുന്നുണ്ടെന്ന് ഞങ്ങൾ വിശ്വസിക്കുന്നു. താങ്കൾക്കവ പ്രവർത്തനരഹിതമാക്കണമെന്നുണ്ടെങ്കിൽ താളിൽ മുകളിലായുള്ള "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" എന്ന കണ്ണി ഞെക്കുക.',
	'prefswitch-success-off' => 'പുതിയ സവിശേഷതകൾ ഇപ്പോൾ പ്രവർത്തനരഹിതമാണ്. പുതിയ സവിശേഷതകൾ പരീക്ഷിച്ചതിനു നന്ദി. താങ്കൾക്ക് അവ വീണ്ടും പ്രവർത്തനസജ്ജമാക്കണമെന്നുണ്ടെങ്കിൽ താളിൽ മുകളിലായുള്ള "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]] എന്ന കണ്ണി ഞെക്കുക.',
	'prefswitch-success-feedback' => 'താങ്കളുടെ അഭിപ്രായങ്ങൾ അയച്ചു.',
	'prefswitch-return' => '<hr style="clear:both">
<span class="plainlinks">[$1 $2]</span> എന്ന താളിലേയ്ക്ക് മടങ്ങുക.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| വിക്കിപീഡിയയുടെ പുതിയ സമ്പർക്കമുഖം, താളുകളിലേയ്ക്കെത്താൻ<br />എങ്ങനെ ഉപയോഗിക്കാം എന്നതിന്റെ സ്ക്രീൻഷോട്ട്<small>[[Media:VectorNavigation-en.png| (വലുതാക്കുക)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| താൾ തിരുത്താനുള്ള അടിസ്ഥാന സൗകര്യങ്ങളുടെ സ്ക്രീൻഷോട്ട് <small>[[Media:VectorEditorBasic-en.png| (വലുതാക്കുക)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| കണ്ണികൾ ഉൾപ്പെടുത്താനുള്ള പുതിയ സൗകര്യത്തിന്റെ സ്ക്രീൻഷോട്ട്
|}
|}
വിക്കിമീഡിയ ഫൗണ്ടേഷന്റെ ഉപയോക്തൃ സംതൃപ്തി സംഘം, വിക്കി സമൂഹത്തിൽ നിന്നുമുള്ള ഒരുകൂട്ടം സന്നദ്ധസേവകരോടോപ്പം താങ്കൾക്ക് കാര്യങ്ങൾ ലളിതമാക്കിത്തരാൻ പ്രയത്നിക്കുന്നുണ്ട്. പുതുക്കിയ ദൃശ്യാനുഭവവും ലളിതമാക്കിയ തിരുത്തൽ സൗകര്യവുമടക്കമുള്ള ചില പുതുക്കലുകൾ പങ്ക് വെയ്ക്കാൻ ഞങ്ങളാഗ്രഹിക്കുന്നു.  [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study കഴിഞ്ഞ കൊല്ലം നടത്തിയ പഠനത്തെ ആസ്പദമാക്കി] ഉപയോക്താക്കൾക്ക് കാര്യങ്ങൾ ചെയ്യൽ എളുപ്പമാക്കുക എന്ന ലക്ഷ്യത്തോടെയാണ് ഇവ ചെയ്തിരിക്കുന്നത്. നമ്മുടെ സംരംഭങ്ങളുടെ മെച്ചപ്പെട്ട ഉപയോഗ്യത വിക്കിമീഡിയ ഫൗണ്ടേഷന്റെ ലക്ഷ്യമാണ്, കൂടുതൽ മെച്ചപ്പെടുത്തലുകൾ ഭാവിയിൽ വരാനിരിക്കുന്നു. കൂടുതൽ വിവരങ്ങൾക്ക് [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ വിക്കിമീഡിയ ബ്ലോഗ്] പോസ്റ്റ് സന്ദർശിക്കുക

===ഞങ്ങൾ മാറ്റം വരുത്തിയവ===
* '''നാവിഗേഷൻ:''' താളുകൾ വായിക്കാനും തിരുത്തുവാനുമുള്ള സൗകര്യം മെച്ചപ്പെടുത്തി. ഇപ്പോൾ ഓരോ താളിന്റേയും മുകളിലുള്ള റ്റാബുകൾ താങ്കൾ താളാണോ സംവാദം താളാണോ കാണുന്നത് എന്നും, താങ്കൾ തിരുത്തുകയാണോ വായിക്കുകയാണോ എന്നും വ്യക്തമായി കാണിക്കുന്നു.
* '''തിരുത്തൽ ടൂൾബാർ മെച്ചപ്പെടുത്തലുകൾ:''' ലളിതമായി ഉപയോഗിക്കാവുന്ന വിധത്തിൽ ഞങ്ങൾ തിരുത്തൽ ടൂൾബാർ പുനഃക്രമീകരിച്ചിരിക്കുന്നു. ഇപ്പോൾ താൾ ശരിയായ വിധത്തിൽ വിന്യസിക്കുന്നത് ലളിതവും സ്വാഭാവികവുമായിരിക്കും.
* '''കണ്ണി ചേർക്കൽ:''' ലളിതമായി ഉപയോഗിക്കാവുന്ന ഉപകരണം കൊണ്ട് മറ്റ് വിക്കിപീഡിയ താളുകളിലേയ്ക്കോ പുറത്തുള്ള സൈറ്റുകളിലേയ്ക്കോ കണ്ണികൾ ചേർക്കാൻ താങ്കളെ സഹായിക്കുന്നു.
* '''തിരച്ചിൽ മെച്ചപ്പെടുത്തലുകൾ:''' താങ്കൾ തിരയുന്ന താളിലേയ്ക്ക് പെട്ടെന്ന് എത്തിച്ചേരാവുന്ന വിധത്തിൽ മെച്ചപ്പെടുത്തിയ തിരച്ചിൽ നിർദ്ദേശങ്ങൾ ഉൾപ്പെടുത്തിയിരിക്കുന്നു.
* '''മറ്റ് പുതിയ സവിശേഷതകൾ:''' പട്ടികകൾ ചേർക്കാനായി ഒരു സഹായിയും താൾ തിരുത്തൽ ലളിതമാക്കാൻ വാക്കുകളും മറ്റും കണ്ടെത്തി മാറ്റിച്ചേർക്കാനുള്ള സൗകര്യവും ഉൾപ്പെടുത്തിയിരിക്കുന്നു.
* '''വിക്കിപീഡിയ പസിൽ ഗ്ലോബ്:''' പസിൽ ഗ്ലോബ് പുതുക്കിയിരിക്കുന്നു. കൂടുതൽ [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ വിക്കിമീഡിയ ബ്ലോഗിൽ] വായിക്കുക.


===അഭിപ്രായങ്ങൾ?===
താങ്കളിൽ നിന്നവ കേൾക്കാൻ ഞങ്ങൾക്കതിയായ ആഗ്രഹമുണ്ട്. ദയവായി ഞങ്ങളുടെ [[\$1|അഭിപ്രായങ്ങൾക്കുള്ള താൾ]] കാണുക അല്ലെങ്കിൽ,  സോഫ്റ്റ്‌‌വേറിലുള്ള പുതിയ മെച്ചപ്പെടുത്തലുകളെ കുറിച്ചറിയാൻ [http://usability.wikimedia.org ഉപയോഗ്യത വിക്കി] സന്ദർശിക്കുക.",
	'prefswitch-main-anon' => '===എനിക്കിതു വേണ്ട===
പുതിയ സവിശേഷതകൾ താങ്കൾ ഒഴിവാക്കാൻ ആഗ്രഹിക്കുന്നുവെങ്കിൽ, [$1 ഇവിടെ ഞെക്കുക]. ഇതിനായി ആദ്യം ലോഗിൻ ചെയ്യുകയോ അംഗത്വമെടുക്കുകയോ ചെയ്യേണ്ടതാണ്.',
	'prefswitch-main-on' => '===എനിക്കിതു്‌ വേണ്ട!===
[$2 ഇവിടെ ഞെക്കി പുതിയ സവിശേഷതകൾ പ്രവർത്തനരഹിതമാക്കുക].',
	'prefswitch-main-off' => '===അവ പരീക്ഷിച്ചു നോക്കൂ!===
പുതിയ സവിശേഷതകൾ പരീക്ഷിച്ചു നോക്കാൻ താങ്കളാഗ്രഹിക്കുന്നുവെങ്കിൽ, ദയവായി [$1 ഇവിടെ ഞെക്കുക].',
	'prefswitch-survey-intro-feedback' => 'താങ്കളുടെ അഭിപ്രായമെന്തെന്നറിയാൻ ഞങ്ങൾക്കതിയായ ആഗ്രഹമുണ്ട്.
ദയവായി താഴെ കൊടുത്തിരിക്കുന്ന ഐച്ഛിക സർവേ പൂരിപ്പിച്ചതിനു ശേഷം "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]" ഞെക്കുക.',
	'prefswitch-survey-intro-off' => 'പുതിയ സവിശേഷതകൾ പരീക്ഷിച്ചതിനു നന്ദി.
അവ മെച്ചപ്പെടുത്തുവാൻ ഞങ്ങളെ സഹായിക്കുന്നതിനായി, ദയവായി താഴെ നൽകിയിരിക്കുന്ന ഐച്ഛിക സർവേ പൂരിപ്പിച്ച ശേഷം "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]" ഞെക്കുക.',
	'prefswitch-feedbackpage' => 'Project:ഉപയോക്തൃ അനുഭവ അഭിപ്രായങ്ങൾ',
);

/** Maltese (Malti)
 * @author Chrisportelli
 */
$messages['mt'] = array(
	'prefswitch' => "Tibdil tal-preferenzi tal-Inizjattiva ta' Użabilità",
	'prefswitch-desc' => "Ippermetti lill-utenti li jbiddlu settijiet ta' preferenzi",
	'prefswitch-survey-true' => 'Iva',
	'prefswitch-survey-false' => 'Le',
	'prefswitch-survey-submit-off' => 'Itfi l-funzjonijiet il-ġodda',
	'prefswitch-survey-cancel-off' => "Jekk tixtieq tkompli tuża' l-funzjonijiet l-ġodda, tista' tirritorna lejn $1.",
	'prefswitch-survey-submit-feedback' => 'Ibgħat ir-rispons tiegħek',
	'prefswitch-survey-cancel-feedback' => "Jekk ma tixtieq tibgħat ir-rispons tiegħek, tista' tirritorna lejn $1.",
	'prefswitch-survey-question-like' => "X'għoġbok mill-funzjonijiet il-ġodda?",
	'prefswitch-survey-question-dislike' => "X'ma għoġbokx mill-funzjonijiet il-ġodda?",
	'prefswitch-survey-question-whyoff' => 'Għaliex qiegħed titfi l-funzjonijiet l-ġodda? Jekk jogħġbok agħżel dak li japplikaw għalik.',
	'prefswitch-survey-answer-whyoff-hard' => 'Kienet wisq diffiċli biex tużaha.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Ma kinitx qed taħdem tajjeb.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Ma kinitx qed taħdem kif mixtieq.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => "M'għoġobnix l-aspett tagħha.",
	'prefswitch-survey-answer-whyoff-didntlike-layout' => "M'għoġbunix il-buttuni l-ġodda u t-tqassim tal-paġni.",
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => "M'għoġbitnix il-kaxxa tal-għodda l-ġdida.",
	'prefswitch-survey-answer-whyoff-other' => 'Raġuni oħra:',
	'prefswitch-survey-question-browser' => "Liema browżer tuża'?",
	'prefswitch-survey-answer-browser-other' => 'Browżers oħra:',
	'prefswitch-survey-question-os' => "Liema sistema operattiva tuża'?",
	'prefswitch-survey-answer-os-other' => 'Sistema operattiva oħra:',
	'prefswitch-survey-question-res' => "X'inhi r-riżoluzzjoni tal-iskrin tiegħek?",
	'prefswitch-title-on' => 'Funzjonijiet ġodda',
	'prefswitch-title-switched-on' => 'Ħu gost!',
	'prefswitch-title-off' => 'Itfi l-funzjonijiet l-ġodda',
	'prefswitch-title-switched-off' => 'Grazzi',
	'prefswitch-title-feedback' => 'Rispons',
	'prefswitch-success-on' => "Il-funzjonijiet il-ġodda ġew attivati. Nisperaw li tieħu gost tuża' dawn il-funzjonijiet il-ġodda. Tista' dejjem titfihom lura billi tagħfas fuq \"[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]\" fin-naħa ta' fuq tal-paġna.",
	'prefswitch-success-off' => 'Il-funzjonijiet il-ġodda ġew mitfija. Grazzi talli ppruvajt dawn il-funzjonijiet il-ġodda. Tista\' dejjem tattivahom lura billi tagħfas fuq "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" fin-naħa ta\' fuq tal-paġna.',
	'prefswitch-success-feedback' => 'Ir-rispons tiegħek intbagħat.',
	'prefswitch-return' => '<hr style="clear:both">
Irritorna lura lejn <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "Ħdimna ħafna sabiex nagħmlu l-affarijiet iktar faċli għall-utenti tagħna. Ninsabu entużjasti li naqsmu magħkom xi titjib li sar, fosthom aspett grafiku ġdid u funzjonijiet tal-immodifikar simplifikati. It-titjib fl-użabilità tal-proġetti tagħna hija prijorità tal-Fondazzjoni Wikimedia u fil-ġejjieni sejrin naqsmu magħkom aktar aġġornamenti. Għal aktar dettalji, żur il-post relatata fuq il-[[http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ blogg tal-Wikimedia].

[[File:UsabilityNavigation.png|right|link=|Screenshot tan-navigazzjoni l-ġdida]]
[[File:UsabilityToolbar.png|right|link=|Screenshot tal-kaxxa tal-għodda mtejba]]
[[File:UsabilityDialogs.png|right|link=|Screenshot tat-twieqi l-ġodda ta' djalogu għall-ġenerazzjoni tal-kontenut]]
===Dan hu dak li biddilna===
* '''Navigazzjoni:''' Tejjibna s-sistema ta' navigazzjoni għall-qari u l-immodifikar ta' paġni. Issa, il-kaxex fin-naħa ta' fuq ta' kull paġna juruk biċ-ċar jekk intix qed tara l-paġna jew il-paġna ta' diskussjoni, jew jekk intix qiegħed taqra jew timmodifika paġna.
* '''Titjib fil-kaxxa tal-għodda tal-immodifikar:''' Irranġajna l-kaxxa tal-għodda tal-immodifikar biex issir aktar faċli biex tiġi wżata. Issa, li tifformattja l-paġni hu aktar sempliċi u aktar intuwittiv.
* '''Proċedura gwidata għall-ħoloq:''' Strument faċli biex jiġi wżat li jgħinek iżżid ħolqa lejn paġni wiki oħra kif ukoll lejn siti esterni.
* '''Titjib fit-tfittxija:''' Tejjibna s-suġġerimenti tat-tfittxija sabiex iwassluk lejn il-paġna li qiegħed tfittex aktar malajr.
* '''Funzjonijiet oħra:''' Introduċejna wkoll proċedura gwidata għat-tabelli sabiex toħloq tabelli iktar faċli u funzjoni ta' \"Fittex u biddel\" biex tissimplifika l-immodifikar tal-paġna.
* '''Globu tal-''puzzle'' tal-Wikipedija''': aġġornajna l-globu tal-''puzzle'', aqra aktar fuq il-[http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/  blogg tal-Wikimedia].",
	'prefswitch-main-anon' => '===Ħudni lura===
Jekk tixtieq titfi l-funzjonijiet l-ġodda, [$1 agħfas hawnhekk]. Se tkun mistoqsi biex tagħmel il-login jew toħloq kont.',
	'prefswitch-main-on' => '===Ħudni lura===
Jekk tixtieq titfi l-funzjonijiet l-ġodda, [$2 agħfas hawnhekk].',
	'prefswitch-main-off' => '===Ippruvawhom!===
Jekk tixtieq tipprova l-funzjonijiet il-ġodda, [$1 agħfas hawnhekk].',
	'prefswitch-survey-intro-feedback' => 'Nieħdu gost nisimgħu mingħandek.
Jekk jogħġbok imla l-kwestjonarju opzjonali segwenti qabel ma tagħfas "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Grazzi talli għamilt użu mill-funzjonijiet il-ġodda.
Biex tgħinna ntejbuhom, jekk jogħġbok imla l-kwestjonarju opzjonali segwenti qabel ma tagħfas "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
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
	'prefswitch-survey-question-like' => 'Wat beviel u aan de nieuwe functionaliteit?',
	'prefswitch-survey-question-dislike' => 'Wat beviel u niet aan de nieuwe functionaliteit?',
	'prefswitch-survey-question-whyoff' => 'Waarom wilt u de nieuwe mogelijkheden uitschakelen?
Vink alstublieft alle mogelijkheden die van toepassing zijn aan.',
	'prefswitch-survey-answer-whyoff-hard' => 'Het gebruik van de nieuwe functionaliteit was te lastig.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'De nieuwe functionaliteit functioneerde niet correct.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'De nieuwe functionaliteit reageerde niet voorspelbaar.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Het zag er niet zo uit als ik wilde.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Ik vond de nieuwe tabbladen en het uiterlijk niet prettig.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Ik vond de nieuwe werkbalk niet prettig werken.',
	'prefswitch-survey-answer-whyoff-other' => 'Andere reden:',
	'prefswitch-survey-question-browser' => 'Welke browser gebruikt u?',
	'prefswitch-survey-answer-browser-other' => 'Andere browser:',
	'prefswitch-survey-question-os' => 'Welk besturingssysteem gebruikt u?',
	'prefswitch-survey-answer-os-other' => 'Ander besturingssysteem:',
	'prefswitch-survey-question-res' => 'Wat is uw beeldschermresolutie?',
	'prefswitch-title-on' => 'Nieuwe functionaliteit',
	'prefswitch-title-switched-on' => 'Geniet ervan!',
	'prefswitch-title-off' => 'Nieuwe mogelijkheden uitschakelen',
	'prefswitch-title-switched-off' => 'Bedankt',
	'prefswitch-title-feedback' => 'Terugkoppeling',
	'prefswitch-success-on' => 'De nieuwe mogelijkheden zijn nu ingeschakeld. We hopen dat u er veel plezier aan beleeft. U kunt ze altijd weer uitschakelen door te klikken op de verwijzing "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" bovenaan de pagina.',
	'prefswitch-success-off' => 'De nieuwe mogelijkheden zijn nu uitgeschakeld. Dank u wel voor het proberen. U kunt ze altijd weer inschakelen door te klikken op de verwijzing De nieuwe mogelijkheden zijn nu uitgeschakeld. We hopen dat u er veel plezier aan beleeft. U kunt ze altijd weer uitschakelen door te klikken op de verwijzing "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" bovenaan de pagina.',
	'prefswitch-success-feedback' => 'Uw terugkoppeling is verzonden.',
	'prefswitch-return' => '<hr style="clear:both">
Terug naar <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-nl.png|401px|]]
|-
| De nieuwe navigatie voor Wikipedia. <small>[[Media:VectorNavigation-nl.png|(vergroten)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-nl.png|401px|]]
|-
| Het eenvoudige bewerkingsvenster. <small>[[Media:VectorEditorBasic-nl.png|(vergroten)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-nl.png|401px]]
|-
| Het nieuwe dialoogvenster voor het toevoegen van verwijzingen.
|}
|}
Het Bruikbaarheidsteam van de Wikimedia Foundation hard gewerkt om dingen makkelijker te maken voor u. We zijn verheugd om een aantal verbeteringen met u te delen, inclusief een nieuw uiterlijk en een vereenvoudigde manier om pagina's te bewerken.
De wijzigingen beogen het eenvoudiger te maken voor nieuwelingen om bij te dragen en zijn gebaseerd op onze [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study bruikbaarheidstests van het afgelopen jaar].
Het verbeteren van de gebruiksvriendelijkheid van onze projecten is een prioriteit van de Wikimedia Foundation en we verwachten in de toekomst nog verder te kunnen gaan.
In het [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia blogbericht] kunt u meer lezen.

=== Dit hebben we veranderd ===
* '''Navigatie:''' We hebben de navigatie voor het lezen en bewerken van pagina's verbeterd.
De tabbladen bovenaan de pagina geven beter aan of u een pagina bekijkt of een overlegpagina, en of u een pagina aan het bekijken of aan het bewerken bent.
* '''Verbeteringen aan de werkbalk:''' We hebben de werkbalk volledig ontworpen zodat deze eenvoudiger te gebruiken is.
Nu is het bewerken van pagina's eenvoudiger en intuïtiever.
* '''Hulp bij verwijzingen:''' een hulpje voor het eenvoudig toevoegen van verwijzingen naar andere wikipagina's en externe websites.
* '''Verbeteringen in het zoeken:''' we hebben zoeksuggesties verbeterd zodat u de pagina die u zoekt sneller vindt.
* '''Andere nieuwe mogelijkheden:''' Wij hebben ook een tabelhulpmiddel toegevoegd om het maken van tabellen te vereenvoudigen en een hulpmiddel voor zoeken en vervangen om het bewerken van pagina's te vereenvoudigen.
* '''Wikipedia logo''': We hebben ons logo vernieuwd. Op de [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ Wikimedia blog] kunt u meer lezen.

=== Terugkoppeling? ===
Wij horen graag van u.
Bezoek onze [[\$1|pagina voor terugkoppeling]], of ga naar de [http://usability.wikimedia.org bruikbaarheidswiki] voor meer informatie als u geïnteresseerd bent in onze toekomstige plannen.",
	'prefswitch-main-anon' => '===Terug===
[$1 U kunt de nieuwe functionaliteit uitschakelen]. U wordt dan gevraagd om aan te melden of te registreren.',
	'prefswitch-main-on' => '=== Nee, bedankt! ===
[$2 Klik om de nieuwe mogelijkheden uit te schakelen].',
	'prefswitch-main-off' => '===Uitproberen!===
[$1 Klik hier om de nieuwe mogelijkheden in te schakelen].',
	'prefswitch-survey-intro-feedback' => 'We horen graag van u.
Vul alstublieft de optionele vragenlijst hieronder in voordat u klikt op "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Bedankt voor het uitproberen van de nieuwe mogelijkheden.
Vul alstublieft de onderstaande vragenlijst in om ons te helpen ze verder te verbeteren voordat u klikt op "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Project:Feedback voor het User Experience Team',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Harald Khan
 */
$messages['nn'] = array(
	'prefswitch-title-switched-off' => 'Takk',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 * @author Nghtwlkr
 */
$messages['no'] = array(
	'prefswitch' => 'Preferansebryter for Brukervennlighetsinitiativet',
	'prefswitch-desc' => 'Tillat brukere å bytte preferansesett',
	'prefswitch-survey-true' => 'Ja',
	'prefswitch-survey-false' => 'Nei',
	'prefswitch-survey-submit-off' => 'Slå av nye funksjoner',
	'prefswitch-survey-cancel-off' => 'Om du vil fortsette å bruke de nye funksjonene kan du gå tilbake til $1.',
	'prefswitch-survey-submit-feedback' => 'Send tilbakemelding',
	'prefswitch-survey-cancel-feedback' => 'Hvis du ikke ønsker å gi tilbakemelding kan du gå tilbake til $1.',
	'prefswitch-survey-question-like' => 'Hva likte du med de nye funksjonene?',
	'prefswitch-survey-question-dislike' => 'Hva mislikte du med funksjonene?',
	'prefswitch-survey-question-whyoff' => 'Hvorfor slår du av de nye funksjonene?
Velg alle som passer.',
	'prefswitch-survey-answer-whyoff-hard' => 'De var vanskelige å bruke.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'De fungerte ikke skikkelig.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'De oppførte seg ikke slik jeg skulle forvente.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Jeg likte ikke hvordan de så ut.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Jeg likte ikke de nye fanene og utseende.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Jeg likte ikke den nye verktøylinjen.',
	'prefswitch-survey-answer-whyoff-other' => 'Annen grunn:',
	'prefswitch-survey-question-browser' => 'Hvilken nettleser bruker du?',
	'prefswitch-survey-answer-browser-other' => 'Annen nettleser:',
	'prefswitch-survey-question-os' => 'Hvilket operativsystem bruker du?',
	'prefswitch-survey-answer-os-other' => 'Annet operativsystem:',
	'prefswitch-survey-question-res' => 'Hva er oppløsningen på skjermen din?',
	'prefswitch-title-on' => 'Nye funksjoner',
	'prefswitch-title-switched-on' => 'Kos deg',
	'prefswitch-title-off' => 'Slå av nye funksjoner',
	'prefswitch-title-switched-off' => 'Takk',
	'prefswitch-title-feedback' => 'Tilbakemelding',
	'prefswitch-success-on' => 'Nye funksjoner er nå slått på. Vi håper du liker å bruke de nye funksjonene. Du kan alltids slå dem av igjen ved å klikke på lenken «[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]» på toppen av siden.',
	'prefswitch-success-off' => 'Nye funksjoner er nå slått av. Takk for at du prøvde de nye funksjonene. Du kan alltids slå dem på igjen ved å klikke på lenken «[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]» på toppen av siden.',
	'prefswitch-success-feedback' => 'Tilbakemeldingen din er sendt.',
	'prefswitch-return' => '<hr style="clear:both">
Tilbake til <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "Vi har jobbet hardt for å gjøre ting lettere for brukerne våre. Vi er glade for å dele noen forbedringer, blant annet et nytt utseende og enklere redigeringsfunksjoner. Å forbedre brukervennligheten til prosjektene våre er en prioritet for Wikimedia Foundation og vi vil dele flere oppdateringer med dere i fremtiden. Se [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ Wikimedia-bloggen] (engelsk) for mer informasjon.

[[File:UsabilityNavigation.png|right|link=|Skjermbilde av den nye navigasjonen]]
[[File:UsabilityToolbar.png|right|link=|Skjermbilde av den nye verktøylinja]]
[[File:UsabilityDialogs.png|right|link=|Skjermbilde av de nye dialogboksene]]
===Dette har vi endret===
* '''Navigasjon:''' Vi har forbedret navigeringen for lesing og redigering av sider. Nå viser fanene på toppen av siden klarere om du ser på siden eller på en diskusjonsside, og hvorvidt du leser eller redigerer en side.
* '''Forbedring av redigeringsverktøylinja:''' Vi har omorganisert verktøyslinja for redigering for å gjøre den lettere å bruke. Nå er det lettere og mer intuitivt å formatere sider.
* '''Lenkehjelp:''' Et verktøy som er lett å bruke tillater deg å legge til lenker til andre wikisider og eksterne nettsteder.
* '''Forbedring av søk:''' Vi har forbedret søkeforslagene for å hjelpe deg til siden du leter etter raskere.
* '''Andre nye funksjoner:''' Vi har også introdusert en tabellhjelper for å gjøre det lettere å opprette tabeller og en finn og erstatt-funksjon for å gjøre sideredigering lettere.
* '''Logoen:''' Vi har oppgradert logoen. Les mer på [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ Wikimedia-bloggen] (engelsk).",
	'prefswitch-main-anon' => '===Ta meg tilbake===
Om du vil slå av de nye funksjonene, [$1 klikk her]. Du vil bli spurt om å logge inn eller opprette en konto først.',
	'prefswitch-main-on' => '===Ta meg tilbake===
Om du vil slå av de nye funksjonene kan du [$2 klikke her].',
	'prefswitch-main-off' => '===Prøv dem===
Om du vil slå på de nye funksjonene kan du [$1 klikke her].',
	'prefswitch-survey-intro-feedback' => 'Vi vil gjerne høre fra deg.
Vennligst fyll ut den valgfrie undersøkelsen under før du klikker på «[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]».',
	'prefswitch-survey-intro-off' => 'Takk for at du prøvde de nye funksjonene.
For å hjelpe oss med å forbedre dem kan du fylle ut det valgfrie skjemaet under før du klikker på «[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]».',
);

/** Deitsch (Deitsch)
 * @author Xqt
 */
$messages['pdc'] = array(
	'prefswitch-survey-answer-whyoff-other' => 'Annerer Grund:',
);

/** Polish (Polski)
 * @author Nux
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
	'prefswitch-survey-question-like' => 'Co Ci się podoba w najnowszych zmianach?',
	'prefswitch-survey-question-dislike' => 'Co Ci się nie podoba w najnowszych zmianach?',
	'prefswitch-survey-question-whyoff' => 'Dlaczego rezygnujesz z korzystania z nowych funkcjonalności?
Należy wybrać wszystkie pasujące odpowiedzi.',
	'prefswitch-survey-answer-whyoff-hard' => 'Korzystanie było zbyt trudne.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Nie działało poprawnie.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Działało w sposób nieprzewidywalny.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Nie podoba mi się wygląd.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Nie podobają mi się nowe zakładki i układ.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Nie podoba mi się nowy pasek narzędzi.',
	'prefswitch-survey-answer-whyoff-other' => 'Inny powód',
	'prefswitch-survey-question-browser' => 'Z jakiej korzystasz przeglądarki?',
	'prefswitch-survey-answer-browser-other' => 'Inna przeglądarka',
	'prefswitch-survey-question-os' => 'Z jakiego systemu operacyjnego korzystasz?',
	'prefswitch-survey-answer-os-other' => 'Inny system operacyjny',
	'prefswitch-survey-question-res' => 'Z ekranu o jakiej rozdzielczości korzystasz?',
	'prefswitch-title-on' => 'Nowe funkcjonalności',
	'prefswitch-title-switched-on' => 'Super!',
	'prefswitch-title-off' => 'Wyłącz nowe funkcjonalności',
	'prefswitch-title-switched-off' => 'Dziękujemy',
	'prefswitch-title-feedback' => 'Opinia',
	'prefswitch-success-on' => 'Nowe funkcjonalności są obecnie włączone. Mamy nadzieję, że spodobają Ci się najnowsze zmiany. W każdej chwili możesz wrócić do poprzedniej wersji klikając na link „[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]” znajdujący się na górze strony.',
	'prefswitch-success-off' => 'Nowe funkcjonalności są obecnie wyłączone. Dziękujemy za ich wypróbowanie. Jeśli zechcesz możesz z nich ponownie skorzystać klikając na link „[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]” znajdujący się na górze strony.',
	'prefswitch-success-feedback' => 'Twoja opinia została przesłana.',
	'prefswitch-return' => '<hr style="clear:both">
Powrót do <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| Widok ekranu nowego interfejsu nawigacyjnego Wikipedii <small>[[Media:VectorNavigation-en.png|(enlarge)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| Podstawowy interfejs edycji stron <small>[[Media:VectorEditorBasic-en.png|(enlarge)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| Nowe okienko dialogowe do wprowadzania linków
|}
|}
Zespół Fundacji Wikimedia zbierania doświadczeń użytkowników współpracuje z wolontariuszami ze społeczności aby ułatwić Ci korzystanie. Cieszymy się, że możemy zaprezentować część udoskonaleń, wliczając w to nowy wygląd i nowe, uproszczone funkcjonalności edycyjne. Wprowadzone zmiany mają ułatwić rozpoczęcie pracy nowym użytkownikom i są oparte na [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study doświadczenia zebranych na przestrzeni ostatniego roku]. Zwiększanie użyteczności naszych projektów jest priorytetem dla Fundacji Wikimedia – z pewności w przyszłości przygotujemy więcej nowych funkcjonalności. Więcej (w języku angielskim) można przeczytać na [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ blogu Wikimedia].

===Oto co zmieniliśmy===
* '''Układ strony''' – Poprawiliśmy układ strony zarówno przy edycji jak i przeglądaniu. Zakładki na górze strony w bardziej czytelny sposób informują o tym czy oglądasz właściwą stronę czy stronę dyskusji oraz czy tylko przeglądasz czy edytujesz daną stronę.
* '''Udoskonalenia paska narzędziowego''' – Przeorganizowaliśmy pasek narzędziowy, aby łatwiej było się nim posługiwać. Formatowanie stron powinno być teraz prostsze i bardziej intuicyjne.
* '''Kreator linków''' – Łatwe w użyciu narzędzie pozwala na tworzenie linków zarówno do stron wiki, jak i zewnętrznych serwisów.
* '''Udoskonalenia wyszukiwania''' – Dzięki nowym, udoskonalonym podpowiedziom szybko znajdziesz to czego szukasz.
* '''Inne nowe funkcjonalności''' – Nowy kreator tworzenia tabel oraz okno wyszukiwania i zamiany ułatwia edycję stron.
* '''Wikipedyczny globus z puzzli''' – Zaktualizowaliśmy globus z puzzli będący logo Wikipedii. Więcej informacji (w języku angielskim) znajdziesz na [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ blogu Wikimedia].",
	'prefswitch-main-anon' => '=== Chcę wrócić ===
[$1 Kliknij tutaj], jeśli chcesz wyłączyć nowe funkcjonalności. Przed powrotem do starego wyglądu interfejsu musisz utworzyć konto lub zalogować się.',
	'prefswitch-main-on' => '===Chcę to wyłączyć!===
[$2 Kliknij tutaj aby wyłączyć nowe funkcjonalności].',
	'prefswitch-main-off' => '===Wypróbuj!===
Jeśli chcesz przetestować nowe funkcjonalności po prostu [$1 kliknij tutaj].',
	'prefswitch-survey-intro-feedback' => 'Chcielibyśmy poznać Twoją opinię.
Będziemy wdzięczni za wypełnienie poniższej ankiety zanim klikniesz „[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]”.',
	'prefswitch-survey-intro-off' => 'Dziękujemy za wypróbowanie nowych funkcjonalności.
Jeśli chcesz nam pomóc je udoskonalić, przed kliknięciem „[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]” wypełnij poniższą ankietę.',
	'prefswitch-feedbackpage' => 'Project:Użyteczność – księga skarg, zażaleń i pomysłów',
);

/** Piedmontese (Piemontèis)
 * @author Borichèt
 * @author Dragonòt
 */
$messages['pms'] = array(
	'prefswitch' => "Cangiament dij gust ëd l'inissiativa d'utilisassion còmoda",
	'prefswitch-desc' => "Përmëtte a j'utent ëd cangé j'ansema dij gust",
	'prefswitch-survey-true' => 'É',
	'prefswitch-survey-false' => 'Nò',
	'prefswitch-survey-submit-off' => 'Disativa le funsionalità neuve',
	'prefswitch-survey-cancel-off' => "S'a veul continué a dovré le possibilità neuve, a peul torné a $1.",
	'prefswitch-survey-submit-feedback' => 'Mandé dij sugeriment',
	'prefswitch-survey-cancel-feedback' => "S'a veul pa dé ëd sugeriment, a peul torné a $1.",
	'prefswitch-survey-question-like' => "Lòn ch'at pias ëd le neuve funsionalità?",
	'prefswitch-survey-question-dislike' => "Lòn ch'at pias pa ëd le neuve funsionalità?",
	'prefswitch-survey-question-whyoff' => "Përchè a veul disativé le possibilità neuve?
Për piasì, ch'a selession-a tute le motivassion.",
	'prefswitch-survey-answer-whyoff-hard' => "A l'era tròp malfé dovrelo.",
	'prefswitch-survey-answer-whyoff-didntwork' => 'A marciava nen bin.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'A marciava pa coma spetà.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Am piasìa nen sò aspet.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'A son nen piasume ij neuv quàder e la neuva disposission.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => "A l'é nen piasume la neuva bara dj'utiss.",
	'prefswitch-survey-answer-whyoff-other' => 'Àutra rason:',
	'prefswitch-survey-question-browser' => "Che navigador ch'a deuvra?",
	'prefswitch-survey-answer-browser-other' => 'Àutr navigador:',
	'prefswitch-survey-question-os' => "Che sistema operativ ch'a deuvra?",
	'prefswitch-survey-answer-os-other' => 'Àutr sistema operativ:',
	'prefswitch-survey-question-res' => "Cola ch'a l'é l'arzolussion ëd tò scren?",
	'prefswitch-title-on' => 'Neuve funsionalità',
	'prefswitch-title-switched-on' => 'Fate gòj!',
	'prefswitch-title-off' => 'Disativa le possibilità neuve',
	'prefswitch-title-switched-off' => 'Mersì',
	'prefswitch-title-feedback' => 'Sugeriment',
	'prefswitch-success-on' => 'Le neuve funsionalità a son adess ativà. I speroma ch\'a-j piasa dovré le neuve funsionalità. A peul sempe gaveje via an sgnacand dzora al colegament "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" an cò dla pàgina.',
	'prefswitch-success-off' => 'Le neuve funsionalità a son adess disativà. Mersì për avèj provà le neuve funsionalità. A peul sempe torna buteje an sgnacand dzora al colegament "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" an cò dla pàgina.',
	'prefswitch-success-feedback' => 'Ij sò coment a son ëstàit mandà.',
	'prefswitch-return' => '<hr style="clear:both">
Artorna a <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "I l'oma travajà sech për fé le ròbe belfé për ij nòstri utent. I l'oma gòj ëd condivide chèich ameliorament, comprèis na presentassion neuva e dle possibilità ëd modìfica neuve. Amelioré la comodità d'utilisassion dij nòstri proget a l'é na priorità dla Fondassion Wikimedia e noi i condivideroma d'àutre modìfiche ant l'avnì. Për savèjne ëd pi, ch'a vìsita ël sit colegà [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ scartari ëd Wikimedia]

[[File:UsabilityNavigation.png|right|link=|Scren ëd la neuva navigassion]]
[[File:UsabilityToolbar.png|right|link=|Scren ëd la bara dj'utiss ëd modìfica ameliorà]]
[[File:UsabilityDialogs.png|right|link=|Scren dij diàlogh neuv ëd generassion ëd contnù]]
===Ambelessì a-i é lòn ch'i l'oma cangià===
* '''Navigassion:''' I l'oma ameliorà la navigassion për lese e modifiché le pàgine. Adess, ij test an cò ëd minca pàgina a definisso ëd fasson pì ciàira s'a l'é an camin ch'a vëd na pàgina o na pàgina ëd discussion, e s'a l'é an camin a lese o a modifiché na pàgina.
* '''Ameliorament ëd la bara dj'utiss ëd modìfica:''' I l'oma riorganisà la bara dj'utiss ëd modìfica për ch'a fussa pi belfé dovrela. Adess, l'ampaginassion a l'é pi sempia e pi intuitiva.
* '''Assistent dij colegament:''' N'utiss bel da dovré a-j përmët ëd gionté d'anliure a d'àutre pàgine ëd Wikipedia e ëd colegament a d'àutri sit.
* '''Ameliorament ëd l'arserca:''' I l'oma ameliorà ij sugeriment d'arserca për portelo pi an pressa a la pàgina ch'a sërca.
* '''Àutre possibilità neuve:''' I l'oma ëdcò giontà n'assistent për le tàule për rende pì bel fé creé e trové le tàule e na possibilità ëd rimpiass për semplifiché la modìfica ëd le pàgine.
* '''Wikipedia puzzle globe''': I l'oma modificà ël puzzle globe. Les ëd pi an sël [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ blog ëd Wikimedia].",
	'prefswitch-main-anon' => "===Pòrtme andré===
S'it veule disabilité le neuve funsionalità, [$1 sgnaca sì]. At sarà ciamà d'intré o ëd creé prima un cont.",
	'prefswitch-main-on' => "===Porteme andré===
[$2 Ch'a sgnaca ambelessì për disativé le neuve fonsionalità].",
	'prefswitch-main-off' => "===Preuvje!===
S'it veule ativé le possibilità neuve, për piasì [$1 sgnaca ambelessì].",
	'prefswitch-survey-intro-feedback' => 'An piasrìa sente soa opinion.
Për piasì, ch\'a ampinissa ël sondagi opsional sì-sota an sgnacand "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Mersì ëd prové nòstre neuve funsionalità.
Për giutene a amelioreje, për piasì ch\'a ampinissa ël sondagi opsional sì-sota an sgnacand "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'prefswitch-survey-true' => 'هو',
	'prefswitch-survey-false' => 'نه',
	'prefswitch-survey-answer-whyoff-other' => 'بل سبب:',
	'prefswitch-survey-question-browser' => 'تاسې کوم کتنمل کاروۍ؟',
	'prefswitch-survey-answer-browser-other' => 'بل کتنمل:',
	'prefswitch-title-switched-off' => 'مننه',
);

/** Portuguese (Português)
 * @author Giro720
 * @author Hamilton Abreu
 */
$messages['pt'] = array(
	'prefswitch' => 'Preferências da Iniciativa de Usabilidade',
	'prefswitch-desc' => 'Permitir que os utilizadores mudem conjuntos de preferências',
	'prefswitch-survey-true' => 'Sim',
	'prefswitch-survey-false' => 'Não',
	'prefswitch-survey-submit-off' => 'Desligar as funcionalidades novas',
	'prefswitch-survey-cancel-off' => 'Se quiser continuar a usar as funcionalidades novas, pode voltar à $1.',
	'prefswitch-survey-submit-feedback' => 'Enviar comentário',
	'prefswitch-survey-cancel-feedback' => 'Se não quiser fazer um comentário, pode voltar à $1.',
	'prefswitch-survey-question-like' => 'De que coisas gostou nas funcionalidades novas?',
	'prefswitch-survey-question-dislike' => 'De que coisas não gostou nas funcionalidades novas?',
	'prefswitch-survey-question-whyoff' => 'Por que é que quer desligar as funcionalidades novas?
Seleccione todas as opções que se aplicam, por favor.',
	'prefswitch-survey-answer-whyoff-hard' => 'As funcionalidades foram demasiado difíceis de utilizar.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'As funcionalidades não funcionaram correctamente.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'As funcionalidades não tiveram o comportamento esperado.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Não gostei da aparência.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Não gostei dos novos separadores e da disposição dos elementos na página.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Não gostei da nova barra de ferramentas.',
	'prefswitch-survey-answer-whyoff-other' => 'Outro motivo:',
	'prefswitch-survey-question-browser' => 'Qual é o browser que usa?',
	'prefswitch-survey-answer-browser-other' => 'Outro browser:',
	'prefswitch-survey-question-os' => 'Qual é o sistema operativo que usa?',
	'prefswitch-survey-answer-os-other' => 'Outro sistema operativo:',
	'prefswitch-survey-question-res' => 'Qual é a resolução do seu monitor?',
	'prefswitch-title-on' => 'Funcionalidades novas',
	'prefswitch-title-switched-on' => 'Desfrute!',
	'prefswitch-title-off' => 'Desligar funcionalidades novas',
	'prefswitch-title-switched-off' => 'Obrigado',
	'prefswitch-title-feedback' => 'Comentário',
	'prefswitch-success-on' => 'As funcionalidades novas estão agora activadas. Esperamos que goste de usá-las. Pode voltar a desactivá-las em qualquer altura, clicando o link "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" no topo da página.',
	'prefswitch-success-off' => 'As funcionalidades novas estão agora desactivadas. Obrigado por tê-las experimentado. Pode voltar a activá-las em qualquer altura, clicando o link "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" no topo da página.',
	'prefswitch-success-feedback' => 'O seu comentário foi enviado.',
	'prefswitch-return' => '<hr style="clear:both">
Voltar para <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-pt.png|401px|]]
|-
| Imagem da nova interface de navegação da Wikipédia <small>[[Media:VectorNavigation-pt.png|(aumentar)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-pt.png|401px|]]
|-
| Imagem da interface de edição básica de páginas <small>[[Media:VectorEditorBasic-pt.png|(aumentar)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-pt.png|401px|]]
|-
| Imagem da nova caixa de diálogo para inserir links
|}
|}
A Equipa de Experiência de Utilização (User Experience Team) da Wikimedia Foundation tem trabalhado em conjunto com voluntários da comunidade para tornar mais fácil a utilização do nosso software. É com prazer que agora partilhamos alguns melhoramentos, incluindo uma nova aparência e a simplificação das funcionalidades de edição. Estas alterações têm por objectivo tornar as primeiras edições mais fáceis para os novos utilizadores e têm por base o nosso [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study teste de usabilidade conduzido durante o ano passado]. Melhorar a usabilidade dos nossos projectos é uma prioridade para a Wikimedia Foundation e mais alterações serão comunicadas no futuro. Para mais detalhes, visite esta [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia publicação no blogue] Wikimedia.

===O que foi alterado===
* '''Navegação:''' A navegação para leitura e edição de páginas foi melhorada. Agora, os separadores no topo de cada página definem mais claramente se está a visionar a página em si ou a respectiva página de discussão, e se está a ler ou a editar a página.
* '''Melhorias na barra das ferramentas de edição:''' A barra de edição foi reorganizada para ser mais fácil de usar. Agora, a formatação de páginas é mais simples e intuitiva.
* '''Assistente para links:''' Uma ferramenta de fácil utilização permite-lhe criar tanto links para outras páginas da Wikipédia como links para outros sites externos.
* '''Melhoramentos da pesquisa:''' Melhorámos as sugestões da pesquisa para levá-lo mais rapidamente à página que procura.
* '''Outras funcionalidades novas:''' Também introduzimos um assistente para facilitar a criação de tabelas, e uma funcionalidade de procura e substituição para simplificar a edição de páginas.
* '''Logótipo da Wikipédia:''' Actualizámos o nosso logótipo. Leia mais sobre a actualização no [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d blogue Wikimedia].

===O seu comentário?===
Gostariamos de conhecer a sua opinião. Visite a nossa [[\$1|página de comentários]], por favor. Se tiver interesse em acompanhar os esforços continuados de melhoria do software, visite a nossa [http://usability.wikimedia.org wiki da usabilidade] para mais informação.",
	'prefswitch-main-anon' => '===Voltar atrás===
[$1 Clique aqui para desactivar as funcionalidades novas]. Será pedido que se autentique ou crie uma conta.',
	'prefswitch-main-on' => '===Voltar atrás!===
[$2 Clique aqui para desligar as funcionalidades novas].',
	'prefswitch-main-off' => '===Experimente-as!===
[$1 Clique aqui para activar as funcionalidades novas].',
	'prefswitch-survey-intro-feedback' => 'Gostariamos de saber a sua opinião.
Preencha o questionário opcional abaixo, antes de clicar "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]", por favor.',
	'prefswitch-survey-intro-off' => 'Obrigado por ter experimentado as funcionalidades novas.
Para ajudar-nos a melhorá-las preencha, por favor, o questionário opcional abaixo, antes de clicar "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Project:Comentário sobre a experiência de utilização',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Giro720
 */
$messages['pt-br'] = array(
	'prefswitch' => 'Preferências da Iniciativa de Usabilidade',
	'prefswitch-desc' => 'Permitir que os utilizadores mudem conjuntos de preferências',
	'prefswitch-survey-true' => 'Sim',
	'prefswitch-survey-false' => 'Não',
	'prefswitch-survey-submit-off' => 'Desligar as funcionalidades novas',
	'prefswitch-survey-cancel-off' => 'Se quiser continuar a usar as novas funcionalidades, você pode voltar à $1.',
	'prefswitch-survey-submit-feedback' => 'Enviar comentário',
	'prefswitch-survey-cancel-feedback' => 'Se não quiser fazer um comentário, pode voltar à $1.',
	'prefswitch-survey-question-like' => 'De que coisas gostou nas funcionalidades novas?',
	'prefswitch-survey-question-dislike' => 'De que coisas não gostou nas funcionalidades novas?',
	'prefswitch-survey-question-whyoff' => 'Por que você está desligando as novas funcionalidades?
Selecione todas as opções que se aplicam, por favor.',
	'prefswitch-survey-answer-whyoff-hard' => 'As funcionalidades eram muito difíceis de utilizar.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'As funcionalidades não funcionaram corretamente.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'As funcionalidades não tiveram o comportamento esperado.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Não gostei da aparência.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Não gostei dos novos separadores e da disposição dos elementos na página.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Não gostei da nova barra de ferramentas.',
	'prefswitch-survey-answer-whyoff-other' => 'Outro motivo:',
	'prefswitch-survey-question-browser' => 'Qual é o navegador que você utiliza?',
	'prefswitch-survey-answer-browser-other' => 'Outro navegador:',
	'prefswitch-survey-question-os' => 'Qual é o sistema operacional que você usa?',
	'prefswitch-survey-answer-os-other' => 'Outro sistema operacional:',
	'prefswitch-survey-question-res' => 'Qual é a resolução do seu monitor?',
	'prefswitch-title-on' => 'Funcionalidades novas',
	'prefswitch-title-switched-on' => 'Desfrute!',
	'prefswitch-title-off' => 'Desligar funcionalidades novas',
	'prefswitch-title-switched-off' => 'Obrigado',
	'prefswitch-title-feedback' => 'Comentários',
	'prefswitch-success-on' => 'As funcionalidades novas estão agora ativadas. Esperamos que goste de usá-las. Você pode desativá-las a qualquer momento, clicando no link "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" no topo da página.',
	'prefswitch-success-off' => 'As funcionalidades novas estão agora desativadas. Obrigado por tê-las experimentado. Pode voltar a ativá-las em qualquer momento, clicando no link "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" no topo da página.',
	'prefswitch-success-feedback' => 'O seu comentário foi enviado.',
	'prefswitch-return' => '<hr style="clear:both">
Voltar para <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| Imagem da nova interface de navegação da Wikipédia <small>[[Media:VectorNavigation-en.png|(aumentar)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| Imagem da interface de edição básica de páginas <small>[[Media:VectorEditorBasic-en.png|(aumentar)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| Imagem da nova caixa de diálogo para inserir links
|}
|}
A Equipe de Experiência de Utilização (User Experience Team) da Wikimedia Foundation tem trabalhado em conjunto com voluntários da comunidade para tornar mais fácil a utilização do nosso software. É com prazer que agora partilhamos alguns melhoramentos, incluindo uma nova aparência e a simplificação das funcionalidades de edição. Estas alterações têm por objectivo tornar as primeiras edições mais fáceis para os novos utilizadores e têm por base o nosso [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study teste de usabilidade conduzido durante o ano passado]. Melhorar a usabilidade dos nossos projetos é uma prioridade para a Wikimedia Foundation e mais alterações serão comunicadas no futuro. Para mais detalhes, visite esta [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia publicação no blogue] Wikimedia.

===O que foi alterado===
* '''Navegação:''' A navegação para leitura e edição de páginas foi melhorada. Agora, os separadores no topo de cada página definem mais claramente se está a visionar a página em si ou a respectiva página de discussão, e se está a ler ou a editar a página.
* '''Melhorias na barra das ferramentas de edição:''' A barra de edição foi reorganizada para ser mais fácil de usar. Agora, a formatação de páginas é mais simples e intuitiva.
* '''Assistente para links:''' Uma ferramenta de fácil utilização permite-lhe criar tanto links para outras páginas da Wikipédia como links para outros sites externos.
* '''Melhoramentos da pesquisa:''' Melhoramos as sugestões da pesquisa para levá-lo mais rapidamente à página que procura.
* '''Outras funcionalidades novas:''' Também introduzimos um assistente para facilitar a criação de tabelas, e uma funcionalidade de procura e substituição para simplificar a edição de páginas.
* '''Logotipo da Wikipédia:''' Atualizamos o nosso logotipo. Leia mais sobre a atualização no [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d blogue Wikimedia].

===O seu comentário?===
Gostaríamos de conhecer a sua opinião. Visite a nossa [[\$1|página de comentários]], por favor. Se tiver interesse em acompanhar os esforços continuados de melhoria do software, visite a nossa [http://usability.wikimedia.org wiki da usabilidade] para mais informação.",
	'prefswitch-main-anon' => '===Voltar atrás===
[$1 Clique aqui para desativar as funcionalidades novas]. Será pedido que se autentique ou crie uma conta.',
	'prefswitch-main-on' => '===Voltar atrás!===
[$2 Clique aqui para desligar as funcionalidades novas].',
	'prefswitch-main-off' => '===Experimente-as!===
[$1 Clique aqui para ativar as funcionalidades novas].',
	'prefswitch-survey-intro-feedback' => 'Gostaríamos de saber a sua opinião.
Preencha o questionário opcional abaixo, antes de clicar "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]", por favor.',
	'prefswitch-survey-intro-off' => 'Obrigado por ter experimentado as funcionalidades novas.
Para ajudar-nos a melhorá-las preencha, por favor, o questionário opcional abaixo, antes de clicar "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Project:Comentário sobre a experiência de utilização',
);

/** Russian (Русский)
 * @author Kaganer
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
	'prefswitch-survey-question-like' => 'Что вам понравилось в новых возможностях?',
	'prefswitch-survey-question-dislike' => 'Что вам не понравилось в новых возможностях?',
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
	'prefswitch-title-on' => 'Новые возможности',
	'prefswitch-title-switched-on' => 'Наслаждайтесь!',
	'prefswitch-title-off' => 'Выключить новые возможности',
	'prefswitch-title-switched-off' => 'Спасибо',
	'prefswitch-title-feedback' => 'Обратная связь',
	'prefswitch-success-on' => 'Новые возможности включены. Надеемся, вам понравится использовать новые функции. Вы всегда можете отключить их, нажав на ссылку «[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]» в верхней части страницы.',
	'prefswitch-success-off' => 'Новые возможности отключены. Спасибо за проверку новых функций. Вы всегда можете включить их обратно, нажав на ссылку «[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]» в верхней части страницы.',
	'prefswitch-success-feedback' => 'Ваш отзыв отправлен.',
	'prefswitch-return' => '<hr style="clear:both">
Вернуться к <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-ru.png|401px|]]
|-
| Вид нового интерфейса навигации Википедии <small>[[Media:VectorNavigation-ru.png|(увеличить)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-ru.png|401px|]]
|-
| Вид основного интерфейса редактирования страниц <small>[[Media:VectorEditorBasic-ru.png|(увеличить)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-ru.png|401px|]]
|-
| Вид нового диалога создания ссылок
|}
|}
В «Фонде Викимедиа» работает группа анализа использования сайта, совместно с добровольцами из сообщества она старается упростить вашу работу с Википедией и другими вики-проектами. Мы рады поделиться некоторыми улучшениями, в том числе новым интерфейсом и упрощёнными функциями редактирования. Эти изменения предназначены для упрощения работы новых редакторов, они основаны на [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study юзабилити-тестировании, проведённом в прошедшем году]. Задача создания более удобного интерфейса наших сайтов рассматривается «Фондом Викимедиа» как приоритетная, мы продолжим совершенствовать проект и в дальнейшем. Подробности можно узнать в [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ блоге Викимедии].

=== Что мы изменили === 
* '''Навигация.''' Мы улучшили навигацию, сделав её более удобной для чтения и редактирования страниц. Теперь вкладки в верхней части каждой страницы позволяют более чётко определить, чем вы сейчас занимаетесь: просматриваете страницу или её обсуждение, читаете или редактируете страницу.
* '''Панель редактирования.''' Мы переделали панель инструментов редактирования, чтобы упростить её использование. Теперь форматирование страниц стало проще и понятнее.
* '''Мастер ссылок.''' Простой в использовании инструмент позволяет добавлять ссылки, ведущие как на другие вики-страницы, так и на внешние сайты.
* '''Поиск.''' Мы улучшили поисковые подсказки, чтобы у вас была возможность быстрее найти требуемую страницу.
* '''Другие новые функции.''' Мы сделали мастер таблиц, позволяющий легко создавать таблицы, а также функцию поиска и замены, упрощающую редактирование.
* '''Логотип.''' Мы обновили вид шарика-пазла, подробнее см. [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ блог «Фонда Викимедиа»].

=== Обратная связь ===
Мы хотели бы услышать ваши отзывы. Пожалуйста, посетите нашу [[\$1|страницу обратной связи]]. Если вам интересны наши дальнейшие работы по улучшению программного обеспечения, посетите [http://usability.wikimedia.org вики юзабилити-проекта].",
	'prefswitch-main-anon' => '=== Вернуть как было ===
Если вы хотите отключить новые возможности, [$1 нажмите здесь]. Вам будет предложено сначала представиться или зарегистрировать учётную запись.',
	'prefswitch-main-on' => '=== Верните всё обратно! ===
Если вы хотите отключить новые возможности, пожалуйста, [$2 нажмите здесь].',
	'prefswitch-main-off' => '=== Опробуйте их! ===
Если вы хотите включить новые возможности, пожалуйста, [$1 нажмите здесь].',
	'prefswitch-survey-intro-feedback' => 'Мы хотели бы получить отзывы.
Пожалуйста, ответьте на несколько необязательных вопросов ниже, прежде чем нажмёте «[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]».',
	'prefswitch-survey-intro-off' => 'Спасибо, что опробовали новые возможности.
Чтобы помочь нам улучшить их, пожалуйста, ответьте на несколько необязательных вопросов, прежде чем нажмёте «[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]».',
	'prefswitch-feedbackpage' => 'Project:Отзывы о новом оформлении',
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'prefswitch' => 'Юзабилити инициативатын туруорууларын уларытыы',
	'prefswitch-desc' => 'Туруоруулары талары хааччыйар',
	'prefswitch-survey-true' => 'Сөп',
	'prefswitch-survey-false' => 'Суох',
	'prefswitch-survey-submit-off' => 'Саҥа туруоруулары араар',
	'prefswitch-survey-cancel-off' => 'Саҥа туруоруулары салгыы туттуоххун баҕардаххына манна $1 төннүөххүн сөп.',
	'prefswitch-survey-submit-feedback' => 'Сыанабылы ыытарга',
	'prefswitch-survey-cancel-feedback' => 'Сыанабыл биэриэххин баҕарбат буоллаххына, манна $1 төннүөххүн сөп.',
	'prefswitch-survey-question-like' => 'Саҥа туруорууларга тугу сөбүлээтиҥ?',
	'prefswitch-survey-question-dislike' => 'Саҥа туруорууларга тугу сөбүлээбэтиҥ?',
	'prefswitch-survey-question-whyoff' => 'Тоҕо саҥа туруоруулары араарыаххын баҕараҕын?
Бука диэн сөп түбэһэр хоруйу тал.',
	'prefswitch-survey-answer-whyoff-hard' => 'Туттарга наһаа ыарахан.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Сөпкө үлэлээбэт.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Күүтүллүбүтүн курдук үлэлээбэт.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Көстүүтүн сөбүлээбэтим.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Саҥа кыбытыктары уонна верстканы соччо сөбүлээбэтим.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Эрэдээксийэлиир хаптаһын саҥа барылын сөбүлээбэтим.',
	'prefswitch-survey-answer-whyoff-other' => 'Атын төрүөт:',
	'prefswitch-survey-question-browser' => 'Ханнык брааузеры туһанаҕыный?',
	'prefswitch-survey-answer-browser-other' => 'Атын брааузер:',
	'prefswitch-survey-question-os' => 'Ханнык операционнай систиэмэни туһанаҕын?',
	'prefswitch-survey-answer-os-other' => 'Атын ОС:',
	'prefswitch-survey-question-res' => 'Эн мониторуҥ разрешениета төһөнүй?',
	'prefswitch-title-on' => 'Саҥа туруоруулар',
	'prefswitch-title-switched-on' => 'Астын!',
	'prefswitch-title-off' => 'Саҥа туруоруулары араар',
	'prefswitch-title-switched-off' => 'Махтал',
	'prefswitch-title-feedback' => 'Айааччылардыын алтыһыы',
	'prefswitch-success-on' => 'Саҥа туруоруулар холбоннулар. Саҥа кыахтары туһанаргын сөбүлүөҥ дии саныыбыт. Сирэй үөһээ өттүгэр баар бу сигэни баттаан хаһан баҕарар араарыаххын сөп:  «[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]».',
	'prefswitch-success-off' => 'Саҥа туруоруулар араҕыстылар. Тургутан көрбүккэр махтал. Хаһан баҕарар сирэй үөһээ өттүгэр баар бу сигэни баттаан төттөрү холбуоххун сөп: «[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]».',
	'prefswitch-success-feedback' => 'Сыанабылыҥ ыытылынна.',
	'prefswitch-return' => '<hr style="clear:both">
Төннөргө <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-ru.png|401px|]]
|-
| Бикипиэдьийэ навигациятын саҥа интерфейса <small>[[Media:VectorNavigation-ru.png|(увеличить)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-ru.png|401px|]]
|-
| Сирэйдэри эрэдээксийэлээһин саҥа сүрүн интерфейса <small>[[Media:VectorEditorBasic-ru.png|(улаатыннар)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-ru.png|401px|]]
|-
| Сигэлэри оҥорооһун саҥа барыла
|}
|}
«Викимедиа Фондатыгар» саайты туһаныыны анаалыстыыр бөлөх үлэлиир. Кини саайт волонтердарын кытта Бикипиэдьийэҕэ уонна атын биики-бырайыактарга үлэлээһини тупсара сатыыр.  Биһиги сорох тупсарыылары, ол иһигэр саҥа интерфейсы уонна эрэдээксийэлээһини судургутутууну үөрүүнү кытта көрдөрөбүт. Бу уларытыылар саҥа кыттааччылар үлэлэрин судургу гынарга аналлаахтар, уонна [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study былырыын ыытыллыбыт тургутууга] өйөнөллөр. Биһиги саайтарбыт өссө табыгастаах буолалларыгар «Викимедиа Фондата» улахан суолтаны биэрэр, онон өссө да тупсара туруохпут. Сиһилии манна  [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ Викимедия блогуттан] билиэххитин сөп.

=== Туох уларыйбыта === 
* '''Навигация.''' Навигацияны тупсардыбыт, онон ааҕарга уонна уларытарга өссө ордук буолла. Билигин хас биирдии сирэй үөһээ өттүнээҕи кыбытыктара тугу гына олороргун ордук чопчу көрдөрөр буоллулар: сирэйи эбэтэр кини ырытыытын көрөргүн, сирэйи ааҕа эбэтэр көннөрө олороргун.
* '''Эрдээксийэ хаптаһына (панель).''' Туттарга судургу гынан биэрдибит. Билигин сирэйдэри формааттааһын ордук судургу уонна өйдөнүмтүө буолла. 
* '''Сигэнии маастара.''' Судургу үнүстүрүмүөн сигэлэри атын биики-сирэйдэргэ да, атын саайтарга да, туруорары хааччыйаллар.
* '''Көрдөөһүн.''' Көрдөөһүн полсказкаларын тупсардыбыт, онон наадыйар сирэйгин түргэнник булуоҥ. 
* '''Атын саҥа кыахтар.''' Табылыыссалары оҥорор маастар олортубут. Эбии эрдээксийэни тупсарар көрдөөһүн уонна уларытыы үнүстүрүмүөнүн олортубут. 
* '''Логотип.''' Шарик-пазл саҥа барылын олортубут, сиһилии манна көр: [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ «Викимедиа Фондатын» блога].

=== Айааччылардыын алтыһыы ===
Эн санааҕын истиэхпитин баҕарабыт. Бука диэн, биһиги [[\$1|онно аналлаах сирэйбитигэр]] киирэ сырыт эрэ. Өскө салгыы тугу гыныахпытын баҕарарбытын билиэххин баҕарар буоллаххына, манна [http://usability.wikimedia.org биики юзабилити-бырайыагар] киирэ сырыт.",
	'prefswitch-main-anon' => '=== Уруккутугар төннөрүү ===
Саҥа интерфейсы араарыаххын баҕарар буоллаххына, [$1 маны баттаа]. Оччоҕо ааккын этэргин эбэтэр бэлиэтэнэргин көрдөһүөхтэрэ.',
	'prefswitch-main-on' => '=== Барытын төннөрүҥ! ===
Саҥа интерфейсы араарарга, бука диэн,  [$2 маны баттаа].',
	'prefswitch-main-off' => '=== Тургутан көр! ===
Саҥа интерфейсы холбуурга, бука диэн,  [$1 маны баттаа].',
	'prefswitch-survey-intro-feedback' => 'Эн санааҕын билиэхпитин баҕарабыт.
Бука диэн, манна баар аҕыйах булгуччута суох ыйытыыларга хоруйдаа эрэ, онтон маны баттаар «[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]».',
	'prefswitch-survey-intro-off' => 'Саҥа интерфейсы тургутан көрбүккэр махтал.
Тупсарарга көмөлөһүөххүн баҕарар буоллаххына, аҕыйах булгуччута суох ыйытыыларга хоруйдаа, онтон маны баттаар: «[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]».',
	'prefswitch-feedbackpage' => 'Project:Саҥа интерфейс туһунан дьон санаата',
);

/** Sardinian (Sardu)
 * @author Andria
 */
$messages['sc'] = array(
	'prefswitch-survey-true' => 'Eja',
	'prefswitch-survey-answer-whyoff-other' => 'Àteru motivu:',
	'prefswitch-survey-answer-browser-other' => 'Àteru browser:',
	'prefswitch-title-switched-off' => 'Gràtzias',
);

/** Sinhala (සිංහල)
 * @author බිඟුවා
 */
$messages['si'] = array(
	'prefswitch-survey-true' => 'ඔව්',
	'prefswitch-survey-false' => 'නැත',
	'prefswitch-survey-answer-whyoff-other' => 'වෙනත් හේතුවක්:',
	'prefswitch-survey-question-browser' => 'ඔබ භාවිතා කරන්නේ කුමන බ්‍රවුසරයද?',
	'prefswitch-survey-answer-browser-other' => 'අනෙක් බ්‍රවුසරය:',
	'prefswitch-title-switched-off' => 'ස්තුතියි',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'prefswitch-survey-true' => 'Áno',
	'prefswitch-survey-false' => 'Nie',
);

/** Slovenian (Slovenščina)
 * @author Dbc334
 */
$messages['sl'] = array(
	'prefswitch' => 'Stikalo nastavitev pobude za uporabnost',
	'prefswitch-desc' => 'Dovoli uporabnikom preklapljanje med nabori nastavitev',
	'prefswitch-survey-true' => 'Da',
	'prefswitch-survey-false' => 'Ne',
	'prefswitch-survey-submit-off' => 'Izklopi nove funkcije',
	'prefswitch-survey-cancel-off' => 'Če bi radi še naprej uporabljali nove funkcije, se lahko vrnete na $1.',
	'prefswitch-survey-submit-feedback' => 'Pošlji povratne informacije',
	'prefswitch-survey-cancel-feedback' => 'Če ne želite podati povratnih informacij, se lahko vrnete na $1.',
	'prefswitch-survey-question-like' => 'Kaj vam je pri novih funkcijah všeč?',
	'prefswitch-survey-question-dislike' => 'Česa pri novih funkcijah ne marate?',
	'prefswitch-survey-question-whyoff' => 'Zakaj izklapljate nove funkcije?
Prosimo, izberite vse kar ustreza.',
	'prefswitch-survey-answer-whyoff-hard' => 'Bilo je pretežko uporabljati.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Ni delovalo pravilno.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Ni delovalo predvidljivo.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Izgled mi ni všeč.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Novi zavihki in postavitev mi niso všeč.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Nova orodna vrstica mi ni všeč.',
	'prefswitch-survey-answer-whyoff-other' => 'Drug razlog:',
	'prefswitch-survey-question-browser' => 'Kateri brskalnik uporabljate?',
	'prefswitch-survey-answer-browser-other' => 'Drug brskalnik:',
	'prefswitch-survey-question-os' => 'Kateri operacijski sistem uporabljate?',
	'prefswitch-survey-answer-os-other' => 'Drug operacijski sistem:',
	'prefswitch-survey-question-res' => 'Kakšna je ločljivost vašega zaslona?',
	'prefswitch-title-on' => 'Nove funkcije',
	'prefswitch-title-switched-on' => 'Uživajte!',
	'prefswitch-title-off' => 'Izklopi nove funkcije',
	'prefswitch-title-switched-off' => 'Hvala',
	'prefswitch-title-feedback' => 'Povratne informacije',
	'prefswitch-success-on' => 'Nove funkcije so sedaj vklopljene. Upamo, da boste ob uporabi novih funkcij uživali. Vedno jih lahko nazaj izklopite s klikom na povezavo »[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]« na vrhu strani.',
	'prefswitch-success-off' => 'Nove funkcije so sedaj izklopljene. Zahvaljujemo se vam za uporabo novih funkcij. Vedno jih lahko nazaj vklopite s klikom na povezavo »[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]« na vrhu strani.',
	'prefswitch-success-feedback' => 'Vaše povratne informacije so bile poslane.',
	'prefswitch-return' => '<hr style="clear:both">
Vrnitev na <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main-anon' => '===Vodi me nazaj===
[$1 Za izklop novih funkcij kliknite tukaj]. Najprej se boste morali prijaviti ali ustvariti račun.',
	'prefswitch-main-on' => '===Vodi me nazaj!===
[$2 Za izklop novih funkcij kliknite tukaj].',
	'prefswitch-main-off' => '===Preizkusite jih!===
Če želite vklopiti nove funkcije, prosimo [$1 kliknite tukaj].',
	'prefswitch-survey-intro-feedback' => 'Radi bi slišali vaše mnenje.
Prosimo, izpolnite neobvezno anketo spodaj, preden kliknete »[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]«.',
	'prefswitch-survey-intro-off' => 'Zahvaljujemo se vam za preizkušanje naših novih funkcij.
Da nam jih pomagate izboljšati, prosimo izpolnite neobvezno anketo spodaj, preden kliknete »[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]«.',
	'prefswitch-feedbackpage' => 'Project:Povratne informacije o uporabniški izkušnji',
);

/** Serbian Cyrillic ekavian (Српски (ћирилица))
 * @author Михајло Анђелковић
 */
$messages['sr-ec'] = array(
	'prefswitch-survey-true' => 'Да',
	'prefswitch-survey-false' => 'Не',
	'prefswitch-survey-answer-whyoff-other' => 'Други разлог:',
	'prefswitch-survey-question-browser' => 'Који браузер користите?',
	'prefswitch-survey-answer-browser-other' => 'Други браузер:',
	'prefswitch-survey-question-os' => 'Који оперативни систем користите?',
	'prefswitch-survey-answer-os-other' => 'Други оперативни систем:',
	'prefswitch-survey-question-res' => 'Која је резолуција Вашег екрана?',
	'prefswitch-title-on' => 'Нове могућности',
	'prefswitch-title-switched-on' => 'Уживајте!',
	'prefswitch-title-off' => 'Искључи нове могућности',
	'prefswitch-title-switched-off' => 'Хвала',
);

/** Serbian Latin ekavian (Srpski (latinica)) */
$messages['sr-el'] = array(
	'prefswitch-survey-true' => 'Da',
	'prefswitch-survey-false' => 'Ne',
	'prefswitch-survey-answer-whyoff-other' => 'Drugi razlog:',
	'prefswitch-survey-question-browser' => 'Koji brauzer koristite?',
	'prefswitch-survey-answer-browser-other' => 'Drugi brauzer:',
	'prefswitch-survey-question-os' => 'Koji operativni sistem koristite?',
	'prefswitch-survey-answer-os-other' => 'Drugi operativni sistem:',
	'prefswitch-survey-question-res' => 'Koja je rezolucija Vašeg ekrana?',
	'prefswitch-title-on' => 'Nove mogućnosti',
	'prefswitch-title-switched-on' => 'Uživajte!',
	'prefswitch-title-off' => 'Isključi nove mogućnosti',
	'prefswitch-title-switched-off' => 'Hvala',
);

/** Swedish (Svenska)
 * @author Ainali
 * @author Boivie
 * @author Dafer45
 */
$messages['sv'] = array(
	'prefswitch' => 'Preferensväljare för Användbarhetsiniativet.',
	'prefswitch-desc' => 'Tillåt användare att byta uppsättningar av preferenser',
	'prefswitch-survey-true' => 'Ja',
	'prefswitch-survey-false' => 'Nej',
	'prefswitch-survey-submit-off' => 'Stäng av de nya funktionerna',
	'prefswitch-survey-cancel-off' => 'Om du vill fortsätta att använda de nya funktionerna kan du återgå till $1.',
	'prefswitch-survey-submit-feedback' => 'Skicka feedback',
	'prefswitch-survey-cancel-feedback' => 'Om du inte vill ge feedback kan du återgå till $1.',
	'prefswitch-survey-question-like' => 'Vad gillade du med de nya funktionerna?',
	'prefswitch-survey-question-dislike' => 'Vad tyckte du inte om med de nya funktionerna?',
	'prefswitch-survey-question-whyoff' => 'Varför stänger du av de nya funktionerna?
Välj alla som stämmer.',
	'prefswitch-survey-answer-whyoff-hard' => 'Det var för svårt att använda.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Det fungerade inte korrekt.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Det betedde sig inte förutsägbart.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Jag tyckte inte om hur det såg ut.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Jag tyckte inte om den nya flikarna och layouten.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Jag gillade inte det nya verktygsfältet.',
	'prefswitch-survey-answer-whyoff-other' => 'Annan orsak:',
	'prefswitch-survey-question-browser' => 'Vilken webbläsare använder du?',
	'prefswitch-survey-answer-browser-other' => 'Andra webbläsare:',
	'prefswitch-survey-question-os' => 'Vilket operativsystem använder du?',
	'prefswitch-survey-answer-os-other' => 'Annat operativsystem:',
	'prefswitch-survey-question-res' => 'Vad är din skärmupplösning?',
	'prefswitch-title-on' => 'Nya funktioner',
	'prefswitch-title-switched-on' => 'Njut!',
	'prefswitch-title-off' => 'Stäng av de nya funktionerna',
	'prefswitch-title-switched-off' => 'Tack',
	'prefswitch-title-feedback' => 'Feedback',
	'prefswitch-success-on' => 'Nya funktioner är nu på. Vi hoppas att du gillar att använda de nya funktionerna. Du kan alltid stänga av dem genom att klicka på "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" längst upp på sidan.',
	'prefswitch-success-off' => 'De nya funktionerna är nu avstängda. Tack för att du provade dessa. Du kan alltid sätta på dem igen genom att klicka på "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" längst upp på sidan.',
	'prefswitch-success-feedback' => 'Dina kommentarer har skickats.',
	'prefswitch-return' => '<hr style="clear:both"> Återgå till <span class="plainlinks">[$1 $2].</span>',
	'prefswitch-main' => "Vi har arbetat hårt för att göra det lättare för våra användare. Vi är glada över att kunna dela vissa förbättringar, inklusive ett nytt utseende och enklare redigeringsfunktioner. Att förbättra användbarheten för våra projekt är en prioritet för Wikimedia Foundation och vi kommer att göra fler uppdateringar i framtiden. För mer information, besök den relaterade [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ Wikimedia blogg]-inlägget.

[[File:UsabilityNavigation.png|right|link=|Skärmdump av den nya navigeringen]]
[[File:UsabilityToolbar.png|right|link=|Skärmdump av det utökade redigeringsverktygsfältet]]
[[File:UsabilityDialogs.png|right|link=|Skärmdump av de nya dialogrutorna för innehållsskapande]]
===Detta är vad vi har ändrat===
* '''Navigering''': Vi har förbättrat navigeringen för att läsa och redigera sidor. Nu definierar flikarna högst upp på varje sida tydligare om du tittar på sidan eller diskussionssidan, och om du läser eller redigerar en sida.
* '''Förbättringar av redigeringsverktygsfältet''': Vi har omorganiserat redigeringsverktygsfältet att göra det lättare att använda. Nu är sidformatering enklare och mer intuitiv.
* '''Länk-guiden''': Ett lättanvänt verktyg låter dig lägga till länkar till andra wiki-sidor samt länkar till externa webbplatser.
* '''Sökningsförbättringar''': Vi har förbättrat sökförslag att snabbare få dig till den sida du söker.
* '''Andra nya funktioner''': Vi har också infört en tabellguide för att göra tabellskapande lättare och en sök-och-ersätt-funktion för att förenkla sidredigering.
* '''Wikipedia pussel-jordglob''': Vi har uppdaterat pusselgloben. Läs mer på [ttp://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ Wikimedia-bloggen].",
	'prefswitch-main-anon' => '=== Ta Mig Tillbaka ===
Om du vill stänga av de nya funktionerna, [$1 klicka här]. Du blir ombedd att logga in eller skapa ett konto först.',
	'prefswitch-main-on' => '===Ta mig tillbaka!===
[$2 Klicka här för att stänga av de nya funktioner].',
	'prefswitch-main-off' => '===Testa dem!===
Om du vill slå på de nya funktioner, vänligen [$1 klicka här].',
	'prefswitch-survey-intro-feedback' => 'Vi vill gärna höra din åsikt.
Vänligen fyll i den frivilliga undersökningen nedan innan du klickar på "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Tack för att du testar de nya funktionerna.
För att hjälpa oss förbättra dem, var vänlig och fyll i den frivilliga undersökningen nedan innan du klickar på "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Project:Feedback från användarerfarenhet',
);

/** Swahili (Kiswahili)
 * @author Lloffiwr
 */
$messages['sw'] = array(
	'prefswitch-survey-true' => 'Ndiyo',
	'prefswitch-survey-false' => 'Siyo',
	'prefswitch-survey-submit-off' => 'Kuzima zana mpya',
	'prefswitch-survey-submit-feedback' => 'Tuma mrejeresho',
	'prefswitch-title-off' => 'Zima zana mpya',
	'prefswitch-title-switched-off' => 'Asante',
	'prefswitch-title-feedback' => 'Mrejeresho',
	'prefswitch-success-feedback' => 'Mrejeresho wako ulitumwa',
	'prefswitch-return' => '<hr style="clear:both">
Urudi <span class="plainlinks">[$1 $2]</span>.',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'prefswitch-desc' => 'అభిరుచుల సమితులని మార్చుకోడానికి వాడుకరులకు వీలుకల్పిస్తుంది',
	'prefswitch-survey-true' => 'అవును',
	'prefswitch-survey-false' => 'కాదు',
	'prefswitch-survey-submit-off' => 'కొత్త సౌలభ్యాలని నిలిపివేయి',
	'prefswitch-survey-cancel-off' => 'మీరు కొత్త సౌలభ్యాలను ఉపయోగించడం కొనసాగించాలనుకుంటే, మీరు తిరిగి $1కి వెళ్ళవచ్చు.',
	'prefswitch-survey-submit-feedback' => 'ప్రతిస్పందని పంపించు',
	'prefswitch-survey-cancel-feedback' => 'మీ ప్రతిస్పందనని తెలియజేయకూడదనుకుంటే, మీరు తిరిగి $1కి వెళ్ళవచ్చు.',
	'prefswitch-survey-question-like' => 'కొత్త సౌలభ్యాలలో మీకు ఏం నచ్చింది?',
	'prefswitch-survey-question-dislike' => 'కొత్త సౌలభ్యాలలో మీకు నచ్చనిదేమిటి?',
	'prefswitch-survey-question-whyoff' => 'మీరు కొత్త సౌలభ్యాలను ఎందుకు నిలిపివేసుకుంటున్నారు?
దయచేసి వర్తించేవన్నీ ఎంచుకోండి.',
	'prefswitch-survey-answer-whyoff-hard' => 'వాడడానికి చాలా కష్టంగా ఉంది.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'ఇది సరిగ్గా పనిచేయడం లేదు.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'ఇది అనుకున్నట్లుగా పనిచేయడం లేదు',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'దీని రూపు నాకు నచ్చలేదు.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'కొత్త ట్యాబులు మరియు అమరిక నాకు నచ్చలేదు.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'కొత్త పనిముట్లపట్టీ నాకు నచ్చలేదు',
	'prefswitch-survey-answer-whyoff-other' => 'ఇతర కారణం:',
	'prefswitch-survey-question-browser' => 'మీరు ఏ విహారిణిని వాడుతున్నారు?',
	'prefswitch-survey-answer-browser-other' => 'ఇతర విహారిణి:',
	'prefswitch-survey-question-os' => 'మీరు వాడుతున్న నిర్వాహక వ్యవస్థ ఏది?',
	'prefswitch-survey-answer-os-other' => 'ఇతర నిర్వాహక వ్యవస్థ:',
	'prefswitch-survey-question-res' => 'మీ తెర వైశాల్యం ఎంత?',
	'prefswitch-title-on' => 'కొత్త సౌలభ్యాలు',
	'prefswitch-title-switched-on' => 'ఆనందించండి!',
	'prefswitch-title-off' => 'కొత్త సౌలభ్యాలని నిలిపివేయి',
	'prefswitch-title-switched-off' => 'ధన్యవాదాలు',
	'prefswitch-title-feedback' => 'ప్రతిస్పందన',
	'prefswitch-success-on' => 'కొత్త సౌలభ్యాలని ఇప్పుడు చేతనం చేసాం. కొత్త సొలభ్యాలని మీరు ఆనందిస్తారని ఆశిస్తున్నాం. పుట పైన ఉండే "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" అనే లంకె ద్వారా మీరు వాటిని ఎప్పుడైనా నిలిపివేసుకోవచ్చు.',
	'prefswitch-success-off' => 'కొత్త సౌలభ్యాలని ఇప్పుడు నిలిపివేశాం. కొత్త సొలభ్యాలని ప్రయత్నించినందుకు ధన్యవాదాలు. పుట పైన ఉండే "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" అనే లంకె ద్వారా మీరు వాటిని ఎప్పుడైనా తిరిగి పొందవచ్చు.',
	'prefswitch-success-feedback' => 'మీ ప్రతిస్పందనని పంపించాం.',
	'prefswitch-return' => '<hr style="clear:both">
తిరిగి <span class="plainlinks">[$1 $2]</span>కి.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| వికీపీడియా కొత్త మార్గదర్శకం యొక్క తెరపట్టు <small>[[Media:VectorNavigation-en.png|(పెద్ద చిత్రం)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| పుట దిద్దుబాటు అంతవర్తి యొక్క తెరపట్టు <small>[[Media:VectorEditorBasic-en.png|(పెద్ద చిత్రం)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| లంకెలను చేర్చడానికై కొత్త సంవాద పేటిక యొక్క తెరపట్టు
|}
|}
వికీని మీకు సులభతరం చేయడానికి వికీమీడియా ఫౌండేషను యొక్క User Experience జట్టు సమాజంలోని ఇతర ఔత్సాహికులతో కలిసి పనిచేస్తూంది. కొత్త రూపురేఖలు మరియు సరళీకృత దిద్దుబాటు సౌలభ్యాలతో కూడిన కొన్ని మెరుగులను మీతో పంచుకోడానికి ఆనందిస్తున్నాం. [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study గత సంవత్సరం మేం నిర్వహించిన ఉపయోగ్యతా పరీక్షల]పై ఆధారపడిన ఈ మార్పులు కొత్త వాడుకరులు తేలికగా మొదలుపెట్టడానికి ఉద్దేశించినవి. మా ప్రాజెక్టుల ఉపయోగ్యతని మెరుగుపరచడం అన్నది వికీమీడియా ఫౌండేషను యొక్క ఒక ప్రాధాన్యత మరియు భవిష్యత్తులో మేం మరిన్ని తాజాకరణలని అందిస్తాం. మరిన్ని వివరాలకై, సంబంధిత వికీమీడియా [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia బ్లాగు టపా]ని చూడండి.

=== ఇవీ మేం మార్చినవి ===
* '''మార్గదర్శకం:''' పుటలని చదవడానికి మరియు మార్చడానికి మార్గదర్శకాన్ని మెరుగుపరిచాం. ఇప్పుడు, పుటలకి పైన ఉండే ట్యాబులు మీరు పుటలో ఉన్నారా లేదా చర్చా పుటలో ఉన్నారా, మరియు పుటని చదువుతున్నారా లేదా మారుస్తున్నారా అన్న విషయాన్ని మరింత స్పష్టంగా చూపిస్తాయి.
* '''దిద్దుబాటు పనిముట్ల పట్టీకి మెరుగులు:''' మరింత తేలికగా ఉపయోగించడానికి గానూ దిట్టుబాటు పనిముట్ల పట్టీని మేం పునర్వవస్థీకరించాం. ఇప్పుడు, పుటలని రూపుదిద్దడం తేలిక మరియు సహజం.
* '''లంకెల విజార్డ్:''' ఇతర వికీ పుటలకు మరియు బయటి సైట్లకి లంకెలు చేర్చడానికి తేలికైన పనిముట్టు.
* '''అన్వేషణ మెరుగులు:''' మీరు వెతుకుతున్న పుటలకి తొందరగా చేరుకునేందుకు గానూ మేం అన్వేషణ సూచనలని మెరుగుపరిచాం.
* '''ఇతర కొత్త సౌలభ్యాలు:''' పట్టికలని సృష్టించడాన్ని సులభతరం చేయానికి ఒక పట్టికల విజార్డ్ మరియు పుటల దిద్దుబాటుని సరళం చేయడానికి వెతుకు మరియు మార్చు సౌలభ్యాన్ని కూడా ప్రవేశపెట్టాం.
* '''వికీపీడియా చిహ్నం:''' మా చిహ్నాన్ని తాజాకరించాం. [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d వికీమీడియా బ్లాగు]లో మరింత చదవండి.

===సలహాలూ, సూచనలూ?===
మీ నుండి వినాలనుకుంటున్నాం. దయచేసి మా [[\$1|ప్రతిస్పందన పుట]]ని సందర్శించండి లేదా, సాఫ్ట్‌వేరుకి మెరుగుపరిచే మా నిరంతర ప్రయత్నాల గురించి మీకు ఆసక్తి ఉంటే, మరింత సమాచారానికై మా [http://usability.wkimedia.org ఉపయోగ్యత వికీ]ని సందర్శించండి.",
	'prefswitch-main-anon' => '===నన్ను వెనక్కి తీసుకెళ్ళు===
[$1 కొత్త సౌలభ్యాలను నిలిపివేసుకోడానికి ఇక్కడ నొక్కండి]. మీరు ముందుగా ప్రవేశించాలి లేదా ఖాతాని సృష్టించుకోవాలి.',
	'prefswitch-main-on' => '===నన్ను వెనక్కి తీసుకెళ్ళు!===
ఒకవేళ మీరు కొత్త సౌలభ్యాలని నిలిపివేసుకోవాలనుకుంటే, దయచేసి [$2 ఇక్కడ నొక్కండి].',
	'prefswitch-main-off' => '===వాటిని ఉపయోగించి చూడండి!===
మీరు కొత్త సౌలభ్యాలని చూడాలనుకుంటే, దయచేసి [$1 ఇక్కడ నొక్కండి].',
	'prefswitch-survey-intro-feedback' => 'మేం మీ నుండి వినాలనుకుంటున్నాం.
"[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]"ని నొక్కే ముందు దయచేసి ఈ ఐచ్ఛిక సర్వేని పూరించండి.',
	'prefswitch-survey-intro-off' => 'కొత్త సౌలభ్యాలని ప్రయత్నించి చూసినందుకు ధన్యవాదాలు.
"[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]"ని నొక్కే ముందు, ఈ సౌలభ్యాలని మరింత మెరుగుపరిచేలా మాకు తోడ్పడటానికి దయచేసి ఈ ఐచ్ఛిక సర్వేని పూరించండి.',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'prefswitch' => 'Pindutan ng nais sa Pampanimula ng Pagkanagagamit',
	'prefswitch-desc' => 'Pahintulutan ang mga tagagamit na magpalit ng mga pangkat ng mga nais',
	'prefswitch-survey-true' => 'Oo',
	'prefswitch-survey-false' => 'Huwag',
	'prefswitch-survey-submit-off' => 'Huwag buhayin ang bagong mga katasangkapang-katangian',
	'prefswitch-survey-cancel-off' => 'Kung nais mong magpatuloy sa paggamit ng bagong mga kasangkapang-katangian, maaaring kang magbalik sa $1.',
	'prefswitch-survey-submit-feedback' => 'Magpadala ng pabalik-sabi',
	'prefswitch-survey-cancel-feedback' => 'Kung ayaw mong magbigay ng balik-puna, maaaring kang magbalik sa $1.',
	'prefswitch-survey-question-like' => 'Ano ang nagustuhan mo tungkol sa bagong mga kasangkapang-katangian?',
	'prefswitch-survey-question-dislike' => 'Ano ang hindi mo naibigan tungkol sa mga kasangkapang-katangian?',
	'prefswitch-survey-question-whyoff' => 'Bakit mo hindi binubuhay ang bagong mga kasangkapang-katangian?
Mangyaring piliin ang lahat ng maaari.',
	'prefswitch-survey-answer-whyoff-hard' => 'Napakahirap gamitin ng mga kasangkapang-katangian.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Hindi gumandar ng maayos ang mga kasangkapang-katangian.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Hindi maaasahan ang pagganap ng mga kasangkapang-katangian.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Hindi ko nagustuhan ang hitsura ng mga kasangkapang-katangian.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Hindi ko gusto ang bagong mga panglaylay at pagkakaayos.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Hindi ko nagustuhan ang bagong kahong ng kasangkapan.',
	'prefswitch-survey-answer-whyoff-other' => 'Ibang dahilan:',
	'prefswitch-survey-question-browser' => 'Anong pantingin-tingin ang ginagamit mo?',
	'prefswitch-survey-answer-browser-other' => 'Ibang pantingin-tingin:',
	'prefswitch-survey-question-os' => 'Anong sistema ng pagpapaandar ang ginagamit mo?',
	'prefswitch-survey-answer-os-other' => 'Ibang sistemang pampaandar:',
	'prefswitch-survey-question-res' => 'Ano ang resolusyon ng iyong tanawan?',
	'prefswitch-title-on' => 'Bagong mga kasangkapang-katangian',
	'prefswitch-title-switched-on' => 'Lasapin!',
	'prefswitch-title-off' => 'Huwag buhayin ang bagong mga katasangkapang-katangian',
	'prefswitch-title-switched-off' => 'Salamat',
	'prefswitch-title-feedback' => 'Balik-sabi',
	'prefswitch-success-on' => 'Binuhay na ang bagong mga kasangkapang-katangian.  Umaasa kaming masisiyahan ka sa paggamit ng bagong mga tampok.  Palaging maaari mong huwag buhayin ang mga ito sa pamamagitan ng pagpindot sa kawing na "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" na nasa itaas ng pahina.',
	'prefswitch-success-off' => 'Hindi na binuhay ang mga kasangkapang-katangian.  Salamat sa pagsubok mo ng bagong mga tampok.  Palaging maaari mong silang buhaying muli sa pamamagitan ng pagpindot ng kawing na "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" na nasa itaas ng pahina.',
	'prefswitch-success-feedback' => 'Naipadala na ang iyong pabalik-sabi.',
	'prefswitch-return' => 'Bumalik sa <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| Isang larawan ng bagong ugnayang-mukha ng Wikipedia <small>[[Media:VectorNavigation-en.png|(palakihin)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| Isang larawan ng payak na ugnayang-mukha na pampatnugot ng pahina <small>[[Media:VectorEditorBasic-en.png|(palakihin)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| Isang larawan ng bagong kahon ng salitaan para sa pagpapasok ng mga kawing
|}
|}
Ang Pangkat na Pangkaranasan ng Tagagamit ng Pundasyong Wikimedia ay nakikilahok sa mga nagkukusang-loob mula sa pamayanan upang mapadali ang mga bagay-bagay para sa iyo.  Nasasabik kaming ipamahagi ang ilang mga pagpapainam, kabilang ang isang bagong pagmumukha at pinapayak na mga kasangkapang-katangian sa pamamatnugot.  Ang mga pagbabago ay naglalayong magawang madali para sa bagong mga tagapag-ambag ang pagsisimula, at nakabatay sa aming [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study isinagawang pagsubok ng pagkanagagamit sa loob ng nakaraang taon].  Isang priyoridad ng Pundasyong Wikimedia ang pagpapainam ng pagkanagagamit ng aming mga proyekto at magpapamahagi kami ng mas marami pang mga pagsasapanahon sa hinaharap.  Para sa mas marami pang mga detalye, dalawin ang kaugnay na [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia ipinaskil na blog].

===Naririto ang mga binago namin===
* '''Panglibot''': Pinainam namin ang nabigasyon para sa pagbasa at pagbago ng mga pahina. Ngayon, mas malinaw na nilalarawan ng mga panglaylay na nasa itaas ng bawat pahina kung tinitingnan ba ang pahina o isang pahina ng usapan, at kung binabasa mo ba o binabago ang isang pahina.
* '''Mga pagpapainam ng kahon ng kasangkapan na pampatnugot''':  Muli naming inayos ang kahon ng kasangkapan na pampatnugot upang maging mas maginhawa ang paggamit nito.  Ngayon, mas payak na ang pag-aayos ng pahina at mas mapangpadama.
* '''Mahiwagang kawing''':  Isang kasangkapang madaling gamitin na nagpapahintulot sa iyo na magdagdag ng mga kawing sa iba pang mga pahina ng wiki pati na mga kawing sa mga sityong nasa labas.
* '''Mga pagpapainam sa paghahanap''': Pinainam namin ang mga mungkahi sa paghahanap upang mas mabilis kang makapunta sa pahinang hinahanap mo.
* '''Iba pang bagong mga kasangkapang-katangian''':  Ipinakilala rin namin ang isang mahiwagang tabla upang maging maginhawa ang paggawa ng mga tabla at kasangkapang-katangiang panghanap at pampalit upang mapapayak ang pagbago sa pahina.
* '''Logo ng Wikipedia''': Isinapanahon namin ang logo namin. Magbasa pa ng marami sa [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/ blog ng Wikimedia].

===Balik-tugon?===
Nais naming makarinig mula sa iyo.  Pakidalaw ang aming [[\$1|pahina ng balik-tugon]] o, kung interesado ka sa aming nagaganap na mga gawain sa pagpapaigi ng sopwer, dalawin ang aming [http://usability.wikimedia.org wiki ng pagkanagagamit] para sa mas marami pang kabatiran.",
	'prefswitch-main-anon' => '===Ibalik ako===
Kung nais mong patayin ang bagong mga kasangkapang-katangian, [$1 pindutin dito]. Hihilingin sa iyong lumagda ka o lumikha muna ng isang akawnt.',
	'prefswitch-main-on' => '===Ibalik ako!===
[$2 Pindutin dito upang huwag buhayin ang bagong mga kasangkapang-katangian].',
	'prefswitch-main-off' => '===Subukin ang mga ito!=== 
Kung nais mong buhayin ang mga bagong kasangkapang-katangian, mangyaring [$1 pindutin  dito].',
	'prefswitch-survey-intro-feedback' => 'Ibig naming marinig ka.
Mangyaring sulatan ang maaaring hindi saguting pangangalap na nasa ibaba bago pindutin ang "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Salamat sa pagsubok ng bago naming mga kasangkapang-katangian.
Upang makatulong sa pagpapainam ng mga ito, mangyaring sulatan ang maaaring walang pangangalap na nasa ibaba bago pindutin ang "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Project: Tugon sa karanasan ng tagagamit',
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
	'prefswitch-survey-question-like' => 'Yeni özellikler hakkında neleri sevdiniz?',
	'prefswitch-survey-question-dislike' => 'Özellikler hakkında neleri sevmediniz?',
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
	'prefswitch-title-on' => 'Yeni özellikler',
	'prefswitch-title-switched-on' => 'Tadını çıkarın!',
	'prefswitch-title-off' => 'Yeni özellikleri kapat',
	'prefswitch-title-switched-off' => 'Teşekkürler',
	'prefswitch-title-feedback' => 'Geribildirim',
	'prefswitch-success-on' => 'Yeni özellikler şimdi açıldı. Yeni özellikleri kullanmayı seveceğinizi umuyoruz. Her zaman sayfanın en üstündeki "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" bağlantısına tıklayarak kapatabilirsiniz.',
	'prefswitch-success-off' => 'Yeni özellikler şimdi kapalı. Yeni özellikleri denediğiniz için teşekkürler. Her zaman sayfanın en üstündeki "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" bağlantısına tıklayarak yeniden açabilirsiniz.',
	'prefswitch-success-feedback' => 'Geribildiriminiz gönderildi.',
	'prefswitch-return' => '<hr style="clear:both">
<span class="plainlinks">[$1 $2]</span> sayfasına geri dön.',
	'prefswitch-main' => "Kullanıcılarımıza işleri kolaylaştırmak için çok çalışıyoruz. Yeni bir görünüm ve his ve basitleşitirlmiş değişiklik özelliklerini de içeren bazı geliştirmeleri paylaşmaktan heyecan duyuyoruz. Projelerimizi kullanılabilirliğini geliştirmek Vikimedya Kuruluşunun önceliği ve gelecekte de daha fazla güncelleme paylaşacağız. Daha fazla detay için, ilgili [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia/ Vikimedya blog] gönderisini ziyaret edin.

[[File:UsabilityNavigation.png|right|link=|Yeni dolaşım ekran görüntüsü]]
[[File:UsabilityToolbar.png|right|link=|Geliştirilmiş değişiklik araç çubuğu ekran görüntüsü]]
[[File:UsabilityDialogs.png|right|link=|Yeni içerik oluşturma diyalogları ekran görüntüsü]]
===İşte değiştirdiklerimiz===
* '''Dolaşım''': Sayfaları okurken ve değiştirirken dolaşımı iyileştirdik. Artık her sayfanın en üstündeki sekme sayfayı mı tartışma sayfasını mı görüntülediğinizi ya da değiştirdiğinizi açıkça belirtiyor.
* '''Değişiklik araç çubuğu geliştirmeleri''':  Değişiklik araç çubuğunu kolay kullanım için yeniden düzenledik. Artık sayfalar daha basit ve sezgili.
* '''Bağlantı sihirbazı''':  Diğer viki sayfalarına ve dış sitelere bağlantı eklemenizi sağlayan kolay kullanılabilir bir araç.
* '''Arama geliştirmeleri''': Aradığınız sayfaya daha çabuk ulaşmanız için arama önerilerini iyileştirdik.
* '''Diğer yeni özellikler''':  Kolay tablo oluşturmak için bir tablo sihirbazı ve sayfa değiştirmeyi kolaylaştıran bul ve değiştir özelliği sunuyoruz.
* '''Vikipedi bulmaca küresi''': Bulmaca küresini güncelleştirdik. [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/  Vikimedya bloğunda] daha fazlasını okuyun.",
	'prefswitch-main-anon' => '===Beni geri götür===
Eğer yeni özellikleri kapatmak isterseniz, [$1 buraya tıklayın]. Öncelikle giriş yapmanız veya bir hesap oluşturmanız istenecektir.',
	'prefswitch-main-on' => '===Beni geri götür!===
Eğer yeni özellikleri kapatmak isterseniz, lütfen [$2 buraya tıklayın].',
	'prefswitch-main-off' => '===Deneyin!===
Yeni özellikleri açmak için lütfen [$1 buraya tıklayın].',
	'prefswitch-survey-intro-feedback' => 'Sizi dinlemek isteriz.
Lütfen "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]" bağlantısına tıklamadan önce aşağıdaki isteğe bağlı anketimizi doldurun.',
	'prefswitch-survey-intro-off' => 'Yeni özellikleri denediğiniz için teşekkürler.
Bunları geliştirmemize yardımcı olmak için, lütfen "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]" bağlantısına tıklamadan önce aşağıdaki isteğe bağlı anketimizi doldurun.',
);

/** Tatar (Cyrillic) (Татарча/Tatarça (Cyrillic))
 * @author Рашат Якупов
 */
$messages['tt-cyrl'] = array(
	'prefswitch' => 'Юзабилити инициативасы көйләүләрен күчерү',
	'prefswitch-desc' => 'Кулланучыларга көйләүләр наборларын күчерергә мөмкинлек бирә',
	'prefswitch-survey-true' => 'Әйе',
	'prefswitch-survey-false' => 'Юк',
	'prefswitch-survey-submit-off' => 'Яңа мөмкинчелекләрне сүндерергә',
	'prefswitch-survey-cancel-off' => 'Сез алга таба да яңа мөмкинчелекләрне кулланырга теләсәгез, Сез $1 кайта аласыз.',
	'prefswitch-survey-submit-feedback' => 'Фикерләрегезне җибәрү',
	'prefswitch-survey-cancel-feedback' => 'Сез прототиа турында фикерләрегезне җибәрә алмасагыз, $1 кайта аласыз.',
	'prefswitch-survey-question-like' => 'Сезгә яңа мөмкинчелекләрдә нәрсә ошады?',
	'prefswitch-survey-question-dislike' => 'Сезгә яңа мөмкинчелекләрдә нәрсә ошамады?',
	'prefswitch-survey-question-whyoff' => 'Сез яңа мөмкинчелекләрне нишләп сүндерәсез?
Зинһар, бөтен уңайлы вариантларны сайлагыз.',
);

/** Ukrainian (Українська)
 * @author AS
 * @author Тест
 */
$messages['uk'] = array(
	'prefswitch-survey-true' => 'Так',
	'prefswitch-survey-false' => 'Ні',
	'prefswitch-survey-submit-off' => 'Увімкнути нові можливості',
	'prefswitch-survey-answer-whyoff-other' => 'Інша причина:',
	'prefswitch-survey-answer-os-other' => 'Інша операційна система:',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'prefswitch' => 'Chuyển đổi tùy chọn Sáng kiến Khả dụng',
	'prefswitch-desc' => 'Cho phép những người dùng đổi qua lại giữa các bộ tùy chọn.',
	'prefswitch-survey-true' => 'Có',
	'prefswitch-survey-false' => 'Không',
	'prefswitch-survey-submit-off' => 'Tắt các tính năng mới',
	'prefswitch-survey-cancel-off' => 'Để tiếp tục sử dụng các tính năng mới, hãy trở về $1.',
	'prefswitch-survey-submit-feedback' => 'Gửi phản hồi',
	'prefswitch-survey-cancel-feedback' => 'Nếu bạn không muốn phản hồi, bạn có thể trở lại $1.',
	'prefswitch-survey-question-like' => 'Các tính năng mới có điểm tốt nào?',
	'prefswitch-survey-question-dislike' => 'Các tính năng mới có điều nào cần sửa không?',
	'prefswitch-survey-question-whyoff' => 'Tại sao bạn lại tắt các tính năng mới?
Xin hãy chọn tất cả các ý thích hợp.',
	'prefswitch-survey-answer-whyoff-hard' => 'Nó khó sử dụng quá.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'Nó không hoạt động tốt.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'Nó không vận hành như kỳ vọng.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'Tôi không thích bề ngoài của nó.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'Tôi không thích những thẻ và cách trình bày mới.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'Tôi không thích thanh công cụ mới.',
	'prefswitch-survey-answer-whyoff-other' => 'Lý do khác:',
	'prefswitch-survey-question-browser' => 'Bạn hay sử dụng trình duyệt nào?',
	'prefswitch-survey-answer-browser-other' => 'Trình duyệt khác:',
	'prefswitch-survey-question-os' => 'Bạn hay sử dụng hệ điều hành nào?',
	'prefswitch-survey-answer-os-other' => 'Hệ điều hành khác:',
	'prefswitch-survey-question-res' => 'Độ phân giải màn hình của bạn là bao nhiêu?',
	'prefswitch-title-on' => 'Các tính năng mới',
	'prefswitch-title-switched-on' => 'Mời thưởng thức!',
	'prefswitch-title-off' => 'Tắt các tính năng mới',
	'prefswitch-title-switched-off' => 'Cám ơn',
	'prefswitch-title-feedback' => 'Phản hồi',
	'prefswitch-success-on' => 'Mong là bạn sẽ thích sử dụng các tính năng mới vừa được bật lên. Lúc nào có thể tắt các tính năng mới dùng liên kết “[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]” ở trên cùng trang.',
	'prefswitch-success-off' => 'Cám ơn bạn đã thử các tính năng mới vừa được tắt. Lúc nào có thể bật lên các tính năng này dùng liên kết “[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]” ở trên cùng trang.',
	'prefswitch-success-feedback' => 'Phản hồi của bạn đã được gửi.',
	'prefswitch-return' => '<hr style="clear:both">
Trở về <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| Hình chụp màn hình giao diện duyệt trang mới của Wikipedia <small>[[Media:VectorNavigation-en.png|(phóng lớn)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| Hình chụp màn hình giao diện sửa đổi trang cơ bản <small>[[Media:VectorEditorBasic-en.png|(phóng lớn)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| Hình chụp màn hình hộp thoại mới để chèn liên kết
|}
|}

Nhóm Trải nghiệm Người dùng của Wikimedia Foundation đã làm việc cùng với các tình nguyện viên từ cộng đồng để làm cho bạn cảm thấy mọi thứ trở nên dễ dàng hơn. Chúng tôi cảm thấy háo hức muốn chia sẻ một số cải tiến, bao gồm một giao diện hoàn toàn mới và các tính năng sửa đổi đã được đơn giản hóa. Những thay đổi này dự kiến sẽ giúp những người mới dễ làm quen hơn, và được dựa trên [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study nghiên cứu về tỉnh khả dụng được thực hiện năm ngoái]. Cải tiến tính khả dụng của các dự án là ưu tiên của Quỹ Wikimedia và chúng tôi sẽ chia sẻ thêm nhiều cập nhật nữa trong tương lai. Đọc thêm chi tiết tại [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia blog của Quỹ Wikimedia].

===Dưới đây là những thay đổi===
* '''Duyệt trang:''' Chúng tôi đã cải tiến việc duyệt các mục khi đọc và sửa trang. Giờ đây, các thẻ nằm ở đầu trang đã ghi một cách rõ ràng là bạn đang xem trang hay trang thảo luận, và bạn đang đọc hay đang sửa một trang.
* '''Cải tiến thanh công cụ sửa đổi:''' Chúng tôi đã sắp xếp lại thanh công cụ sửa đổi để giúp nó dễ sử dụng hơn. Giờ đây, việc định dạng trang đã đơn giản hơn và trực giác hơn.
* '''Hướng dẫn tạo liên kết:''' Một công cụ rất dễ sử dụng giúp bạn thêm liên kết đến các trang wiki khác cũng như liên kết ra các trang bên ngoài.
* '''Cải tiến tìm kiếm:''' Chúng tôi đã cải tiến những gợi ý tìm kiếm để giúp bạn tìm được trang mình muốn nhanh chóng hơn.
* '''Các tính năng mới khác:''' Chúng tôi cũng đã giới thiệu hướng dẫn tạo bảng để giúp tạo bảng dễ dàng hơn, rồi tính năng tìm kiếm và thay thế để đơn giản hóa việc sửa trang.
* '''Biểu trưng Wikipedia:''' Chúng tôi đã cập nhập biểu trưng của chúng ta. Đọc thêm chi tiết tại [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d/  blog của Wikimedia Foundation].

===Phản hồi?===
Chúng tôi muốn nghe lời phản hồi từ bạn. Xin mời xem [[\$1|trang phản hồi]] của chúng tôi hoặc, nếu bạn thích thú với những nỗ lực sắp tới để cải tiến phần mềm, xin mời xem [http://usability.wikimedia.org wiki khả dụng] để biết thêm thông tin",
	'prefswitch-main-anon' => '===Đưa tôi trở lại===
Bạn có thể [$1 tắt các tính năng mới]. Bạn sẽ cần phải mở tài khoản hay đăng nhập trước tiên.',
	'prefswitch-main-on' => '===Đưa tôi trở lại!===
[$2 Nhấn vào đây để tắt các tính năng mới].',
	'prefswitch-main-off' => '===Dùng thử!===
Nếu bạn muốn bật các tính năng mới, xin [$1 nhấn vào đây].',
	'prefswitch-survey-intro-feedback' => 'Chúng tôi mong muốn được nghe bạn nói.
Xin vui lòng điền vào bảng điều tra phía dưới trước khi bấm “[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]”.',
	'prefswitch-survey-intro-off' => 'Cảm ơn bạn đã dùng thử những tính năng mới của chúng tôi.
Để giúp chúng tôi cải tiến chúng hơn nữa, xin vui lòng điền vào bảng điều tra phía dưới trước khi bấm “[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]”.',
	'prefswitch-feedbackpage' => 'Project:Phản hồi về trải nghiệm của người dùng',
);

/** Yiddish (ייִדיש)
 * @author פוילישער
 */
$messages['yi'] = array(
	'prefswitch-survey-true' => 'יא',
	'prefswitch-survey-false' => 'ניין',
	'prefswitch-survey-answer-whyoff-other' => 'אַנדער טעם:',
	'prefswitch-survey-question-browser' => 'וועלכן בלעטערער ניצט איר?',
	'prefswitch-survey-answer-browser-other' => 'אנדער בלעטערער:',
	'prefswitch-survey-question-os' => 'וועלכע אפערירן סיסטעם ניצט איר?',
	'prefswitch-survey-answer-os-other' => 'אנדער אפערירן סיסטעם:',
	'prefswitch-survey-question-res' => 'וואָס איז די רעזאלוציע פֿון אײַער עקראַן?',
	'prefswitch-title-on' => 'נײַע אייגנשאַפֿטן',
	'prefswitch-title-switched-on' => 'האט הנאה!',
	'prefswitch-title-off' => 'אַנולירן נ ײַע אייגנקייטן',
	'prefswitch-title-switched-off' => 'א דאַנק',
	'prefswitch-title-feedback' => 'פֿידבעק',
	'prefswitch-success-on' => 'די נײַע אייגנקייטן זענען איצט אַקטיוו. מיר האָפן איר וועט האָבן הנאה פֿון ניצן די נײַע אייגנקייטן. איר קענט שטענדיק צוריקווענדן דורך געבן אַ קליק אויף דעם "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" לינק אין דער הייך פונעם בלאַט.',
	'prefswitch-success-feedback' => "מ'האט געשיקט אײַער פֿידבעק.",
	'prefswitch-return' => '<hr style="clear:both">
צוריק צו <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-survey-intro-off' => 'א דאַנק פֿאַרן אויספרובירן אונזערע נײַע אייגנקייטן.
בכדי צו העלפֿן אונז פֿאַרבעסערן זיי, זײַט אזוי גוט אויספֿילן דעם אפציאנאַלן אומפֿרעג אונטער איידער איר קליקט "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Liangent
 */
$messages['zh-hans'] = array(
	'prefswitch' => '可用性倡议偏好设定',
	'prefswitch-desc' => '允许用户设定参数设定',
	'prefswitch-survey-true' => '是',
	'prefswitch-survey-false' => '否',
	'prefswitch-survey-submit-off' => '关闭新特色',
	'prefswitch-survey-cancel-off' => '如果您想继续使用新特色，您可以返回$1。',
	'prefswitch-survey-submit-feedback' => '发送意见',
	'prefswitch-survey-cancel-feedback' => '如果你不想提供意见，你可以回到$1继续。',
	'prefswitch-survey-question-like' => '你喜欢什么新特点？',
	'prefswitch-survey-question-dislike' => '你不喜欢什么特点？',
	'prefswitch-survey-question-whyoff' => '你为什么关闭新的功能？请选择所有适用的选择。',
	'prefswitch-survey-answer-whyoff-hard' => '新特点很难去使用。',
	'prefswitch-survey-answer-whyoff-didntwork' => '新特色无法正常工作。',
	'prefswitch-survey-answer-whyoff-notpredictable' => '新特点不及我预期的好。',
	'prefswitch-survey-answer-whyoff-didntlike-look' => '我不喜欢这些新特点的外观。',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => '我不喜欢它的新标签以及排版。',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => '我不喜欢它的新工具列。',
	'prefswitch-survey-answer-whyoff-other' => '其他原因：',
	'prefswitch-survey-question-browser' => '您用哪款浏览器？',
	'prefswitch-survey-answer-browser-other' => '其它浏览器：',
	'prefswitch-survey-question-os' => '您用哪套操作系统？',
	'prefswitch-survey-answer-os-other' => '其它操作系统：',
	'prefswitch-survey-question-res' => '您的屏幕解像度之大小有多大？',
	'prefswitch-title-on' => '新功能',
	'prefswitch-title-switched-on' => '享受！',
	'prefswitch-title-off' => '关闭新特色',
	'prefswitch-title-switched-off' => '谢谢',
	'prefswitch-title-feedback' => '反馈',
	'prefswitch-success-on' => '新功能已经打开了。我们希望你能享受使用这些新功能，你可以关闭这些新功能，只需要按本页页顶的[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]便可以了。',
	'prefswitch-success-off' => '新功能已经关闭了。多谢你试用，你可以随时开启这些新功能，只需要按本页页顶的[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]便可以了。',
	'prefswitch-success-feedback' => '您的反馈已发送。',
	'prefswitch-return' => '<hr style="clear:both">
返回<span class="plainlinks">[$1 $2]</span>。',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| 维基百科新的导航界面的截图 <small>[[Media:VectorNavigation-en.png|(放大)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| 基本编辑界面的截图 <small>[[Media:VectorEditorBasic-en.png|(放大)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| 插入链接对话框的截图
|}
|}

维基媒体基金会的用户体验团队同社群的志愿者一起在努力令你更方便的使用。我们很高兴能够分享一些改进，包括一个全新的外观和简化的编辑功能。这些变化都是为了让新的参与者更容易进行贡献，同时我们[http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study 在过去一年里进行了大量的可用性测试]。提高我们项目的可用性是维基媒体基金会的重要目标，我们将在未来分享更多的更新。欲了解更多详情，请访问相关的维基媒体[http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia 博客上的帖子]。

=== 下面是我们的改变 ===

* '''导航'''：我们已经改善了阅读和编辑网页时的导航。现在，每一页顶部上的标签更明确地界定你是正在查看页面还是讨论页，以及你是否正在阅读或编辑一个页面。
* '''编辑工具栏上的改善'''：我们已经重组了编辑工具栏，以便于更容易使用。现在，格式化网页更简单，更直观。
* '''链接向导'''：一个易于使用的工具可以让你添加链接到其他维基页面以及链接到外部网站。
* '''搜索的改进'''：我们改进了搜索建议，让你寻找网页更迅速。
* '''其他的新功能'''：我们也推出了表格向导，使创建表格更容易，同时寻找和替换功能来简化页面的编辑。
* '''维基百科logo'''：我们已经更新了我们的logo。详情见[http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d Wikimedia blog]。

===意见?===

我们希望收到你们的意见，请造访我们的[[\$1|意见反馈页面]]，或者如果你对我们的软件改善有兴趣，可到[http://usability.wikimedia.org usability wiki]了解详情。",
	'prefswitch-main-anon' => '=== 取消新功能 ===
如果你想关闭的新特点，请[$1 按此]。你将需要先登入或是注册户口。',
	'prefswitch-main-on' => '=== 取消新功能 ===
[$2 按此回到旧版]',
	'prefswitch-main-off' => '===尝试新功能===
如果你想打开新的功能，请[$1 点击这里]。',
	'prefswitch-survey-intro-feedback' => "我们将非常乐意听取您的意见。请填写下面的'''可选'''的调查，然后点击[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]\"。",
	'prefswitch-survey-intro-off' => '感谢您试用我们的新功能。为了帮助我们改进，请填写下面的可选的调查，然后点击"[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]"。',
	'prefswitch-feedbackpage' => 'Project:Vector用户反馈',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Horacewai2
 * @author Liangent
 */
$messages['zh-hant'] = array(
	'prefswitch' => '可用性倡議偏好設定',
	'prefswitch-desc' => '允許用戶設定參數設定',
	'prefswitch-survey-true' => '是',
	'prefswitch-survey-false' => '否',
	'prefswitch-survey-submit-off' => '關閉新特色',
	'prefswitch-survey-cancel-off' => '如果您想繼續使用新特色，您可以返回$1。',
	'prefswitch-survey-submit-feedback' => '發送意見',
	'prefswitch-survey-cancel-feedback' => '如果你不想提供意見，你可以回到$1繼續。',
	'prefswitch-survey-question-like' => '你喜歡什麼新特點？',
	'prefswitch-survey-question-dislike' => '你不喜歡什麼特點？',
	'prefswitch-survey-question-whyoff' => '你為什麼關閉新的功能？請選擇所有適用的選擇。',
	'prefswitch-survey-answer-whyoff-hard' => '新特點很難去使用。',
	'prefswitch-survey-answer-whyoff-didntwork' => '新特色無法正常工作。',
	'prefswitch-survey-answer-whyoff-notpredictable' => '新特點不及我預期的好。',
	'prefswitch-survey-answer-whyoff-didntlike-look' => '我不喜歡這些新特點的外觀。',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => '我不喜歡它的新標籤以及排版。',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => '我不喜歡它的新工具列。',
	'prefswitch-survey-answer-whyoff-other' => '其他原因：',
	'prefswitch-survey-question-browser' => '您用哪款瀏覽器？',
	'prefswitch-survey-answer-browser-other' => '其它瀏覽器：',
	'prefswitch-survey-question-os' => '您用哪套操作系統？',
	'prefswitch-survey-answer-os-other' => '其它操作系統：',
	'prefswitch-survey-question-res' => '您的屏幕解像度之大小有多大？',
	'prefswitch-title-on' => '新功能',
	'prefswitch-title-switched-on' => '享受！',
	'prefswitch-title-off' => '關閉新特色',
	'prefswitch-title-switched-off' => '謝謝',
	'prefswitch-title-feedback' => '反饋',
	'prefswitch-success-on' => '新功能已經打開了。我們希望你能享受使用這些新功能，你可以關閉這些新功能，只需要按本頁頁頂的[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]便可以了。',
	'prefswitch-success-off' => '新功能已經關閉了。多謝你試用，你可以隨時開啟這些新功能，只需要按本頁頁頂的[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]便可以了。',
	'prefswitch-success-feedback' => '您的反饋已發送。',
	'prefswitch-return' => '<hr style="clear:both">
返回<span class="plainlinks">[$1 $2]</span>。',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\" 
| [[Image:VectorNavigation-en.png|401px|]]
|-
| 維基百科新的導航界面的截圖 <small>[[Media:VectorNavigation-en.png|(放大)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| 基本編輯界面的截圖 <small>[[Media:VectorEditorBasic-en.png|(放大)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| 插入鏈接對話框的截圖
|}
|}

維基媒體基金會的用戶體驗團隊同社群的志願者一起在努力令你更方便的使用。我們很高興能夠分享一些改進，包括一個全新的外觀和簡化的編輯功能。這些變化都是為了讓新的參與者更容易進行貢獻，同時我們[http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study 在過去一年裡進行了大量的可用性測試]。提高我們項目的可用性是維基媒體基金會的重要目標，我們將在未來分享更多的更新。欲了解更多詳情，請訪問相關的維基媒體[http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia 博客上的帖子]。

=== 下面是我們的改變 ===

* '''導航'''：我們已經改善了閱讀和編輯網頁時的導航。現在，每一頁頂部上的標籤更明確地界定你是正在查看頁面還是討論頁，以及你是否正在閱讀或編輯一個頁面。
* '''編輯工具欄上的改善'''：我們已經重組了編輯工具欄，以便於更容易使用。現在，格式化網頁更簡單，更直觀。
* '''鏈接嚮導'''：一個易於使用的工具可以讓你添加鏈接到其他維基頁面以及鏈接到外部網站。
* '''搜索的改進'''：我們改進了搜索建議，讓你尋找網頁更迅速。
* '''其他的新功能'''：我們也推出了表格嚮導，使創建表格更容易，同時尋找和替換功能來簡化頁面的編輯。
* '''維基百科logo'''：我們已經更新了我們的logo。詳情見[http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d Wikimedia blog]。

===意見?===

我們希望收到你們的意見，請造訪我們的[[\$1|意見反饋頁面]]，或者如果你對我們的軟件改善有興趣，可到[http://usability.wikimedia.org usability wiki]了解詳情。",
	'prefswitch-main-anon' => '=== 取消新功能 ===
如果你想關閉的新特點，請[$1 按此]。你將需要先登入或是註冊戶口。',
	'prefswitch-main-on' => '=== 取消新功能 ===
[$2 按此回到舊版]',
	'prefswitch-main-off' => '===嘗試新功能===
如果你想打開新的功能，請[$1 點擊這裡]。',
	'prefswitch-survey-intro-feedback' => "我們將非常樂意聽取您的意見。請填寫下面的'''可選'''的調查，然後點擊[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]\"。",
	'prefswitch-survey-intro-off' => '感謝您試用我們的新功能。為了幫助我們改進，請填寫下面的可選的調查，然後點擊"[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]"。',
	'prefswitch-feedbackpage' => 'Project:Vector用戶反饋',
);

