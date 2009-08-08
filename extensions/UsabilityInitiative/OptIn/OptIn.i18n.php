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
	'optin-feedback' => 'Beta feedback',
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
	'optin-survey-question-changes' => 'What changes would you like to see in the Beta in the future (please select all that apply)?',
	'optin-survey-answer-changes-nav' => 'Make the overall navigation of the site easier to use.',
	'optin-survey-answer-changes-edittools' => 'Increase and enhance the editing tools.',
	'optin-survey-answer-changes-upload' => 'Improve content and media uploads.',
	'optin-survey-answer-changes-richtext' => 'Develop editing tools similar to rich-text editors.',
	'optin-survey-answer-changes-lookfeel' => 'Update the "look and feel" of the site.',
	'optin-survey-answer-changes-predictability' => 'Improve predictability of performance.',
	'optin-survey-answer-changes-custom' => 'Allow greater user customization.',
	'optin-survey-answer-changes-other' => 'Other:',
	'optin-improvements' => "== What has been improved? ==
[[File:UsabilityNavigation.png|left|link=]]
;Enhanced navigation
The new navigation system makes it easier to see where you are and what you are doing, as well as where you can go and how to get there. With the search box located in the top right corner, the search feature is easier to find and quicker to use.
<div style=\"clear:both;\"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Edit toolbar improvements
New-and-improved icons make it clearer what action each tool performs. Expandable sections reduce clutter while keeping infrequently used tools just a click away.
The help section provides quick access to a reference for common uses of wiki-markup.
<div style=\"clear:both;\"></div>
== How to get involved ==
To learn more about the project or to get involved, please visit the [http://usability.wikimedia.org Usability Initiative's wiki].  There you will find information about our grant, research, design + development, and releases.  To ask questions or leave general feedback, please use our [http://usability.wikimedia.org/wiki/Talk:Prototype prototype discussion page].  To address particular subjects, please use the associated discussion pages.  If you find bugs, please leave your comments on the [http://usability.wikimedia.org/wiki/Talk:Releases/Acai Acai release discussion page] or by using [https://bugzilla.wikimedia.org Bugzilla].

Thanks and we look forward to hearing from you!",
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

/** Arabic (العربية)
 * @author ;Hiba;1
 * @author OsamaK
 */
$messages['ar'] = array(
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
نود الإطلاع على رأيك في الواجهة الجديدة، لذا فإننا سنقدر ملأك للاستطلاع الاختياري أدناه.',
	'optin-feedback-back' => 'اذا لم ترغب بملىء الاستطلاع, يمكنك الرجوع إلى $1 .',
	'optin-success-in' => 'تقوم الآن بتجريب البيتا.
تستطيع العودة في أي وقت بنقر وصلة "{{int:optin-leave}}" في أعلى الشاشة.',
	'optin-leave-cancel' => 'إن كنت تريد الاستمرار في استخدام بيتا, فيمكنك الرجوع إلى $1.',
	'optin-success-out' => 'لقد تركت البيتا بنجاح.
تستطيع تجريبها مرة أخرى في أي وقت بنقر وصلة "{{int:optin-try}}" في أعلى يسار الشاشة.',
	'optin-success-feedback' => 'شكرًا لك على ملاحظاتك!',
	'optin-accept-short' => 'لنجربها!',
	'optin-accept-long' => 'جرب بيتا',
	'optin-accept-long-anon' => 'انشىء حسابا و قم بتجريب بيتا',
	'optin-deny-short' => 'لا, شكرا',
	'optin-deny-long' => 'الرجوع للصفحة السابقة',
	'optin-submit-out' => 'غادر البيتا',
	'optin-submit-feedback' => 'أعطِ ملاحظاتك',
	'optin-try' => 'جرّب البيتا',
	'optin-leave' => 'غادر البيتا',
	'optin-feedback' => 'ملاحظات البيتا',
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
	'optin-survey-question-browser' => 'ما هو المتصفح الذي تستخدمه؟',
	'optin-survey-answer-browser-other' => 'متصفح آخر:',
	'optin-survey-question-os' => 'ما نظام التشغيل الذي تستخدمه؟',
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
);

/** Aramaic (ܐܪܡܝܐ)
 * @author Basharh
 */
$messages['arc'] = array(
	'optin-survey-answer-os-other' => '',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Ramsis II
 */
$messages['arz'] = array(
	'optin-title-optedout' => 'تجريب بيتا',
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
	'optin-title-feedback' => 'Зваротная сувязь',
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
	'optin-success-feedback' => 'Дзякуй за зваротную сувязь!',
	'optin-accept-short' => 'Зрабіць гэта!',
	'optin-accept-long' => 'Паспрабаваць Beta',
	'optin-accept-long-anon' => 'Увайсьці ў сыстэму і паспрабаваць Beta',
	'optin-deny-short' => 'Не, дзякуй',
	'optin-deny-long' => 'вярнуцца да папярэдняй старонкі',
	'optin-submit-out' => 'Пакінуць Beta',
	'optin-submit-feedback' => 'Даслаць',
	'optin-try' => 'Паспрабаваць Beta',
	'optin-leave' => 'Пакінуць Beta',
	'optin-feedback' => 'Зваротная сувязь',
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
	'optin-survey-question-changes' => 'Якія зьмены Вы жадаеце бачыць у Beta ў будучыці (выберыце ўсё што падыходзіць)?',
	'optin-survey-answer-changes-nav' => 'Зрабіць навігацыю па сайту больш простай у выкарыстаньні.',
	'optin-survey-answer-changes-edittools' => 'Пашырыць і палепшыць інструмэнты рэдагаваньня.',
	'optin-survey-answer-changes-upload' => 'Палепшыць загрузку зьместу і мэдыя-файлаў.',
	'optin-survey-answer-changes-richtext' => 'Распрацаваць прылады рэдагаваньня, падобныя на тэкставыя рэдактары.',
	'optin-survey-answer-changes-lookfeel' => 'Абнавіць «зьнешні выгляд і запаўненьне» сайта.',
	'optin-survey-answer-changes-predictability' => 'Палепшыць прадказальнасьць хуткадзеяньня.',
	'optin-survey-answer-changes-custom' => 'Дазволіць удзельнікам пашыраную настройку.',
	'optin-survey-answer-changes-other' => 'Іншыя:',
	'optin-improvements' => '== Што было палепшана? ==
[[File:UsabilityNavigation.png|left|link=]]
;Паляпшэньне навігацыі
Новая навігацыйная сыстэма дазвяляе лягчэй зразумець дзе Вы знаходзіцеся і што робіце, а так сама куды Вы можаце перайсьці і як гэта зрабіць. Поле пошуку перанесена з левай навігацыйнай панэлі ў верхні правы вугал для палягчэньня і паскарэньня пошуку.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Паляпшэньне панэлі рэдагаваньняў
Новыя і палепшаныя значкі дазваляюць лягчэй зразумець якія дзеяньні выконвае кожная прылада. Адчыняемыя сэктары зьмяньшаюць тлум, захоўваючы рэдка выкарыстоўваемыя прылады для кліку мышкай. Дапаможны падзел забясьпечвае хуткі доступ да прыкладаў па найбольш выкарыстоўваемым тыпам вікі-метак.
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
	'optin-try' => 'Isprobajte Beta',
	'optin-leave' => 'Napusti Beta',
	'optin-feedback' => 'Pošaljite povratne informacije',
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
	'optin-improvements' => '== Šta je poboljšano? ==
[[File:UsabilityNavigation.png|left|link=]]
;Poboljšana navigacija
Jasna naznaka stanja "čitanja" i "uređivanja" kada ste na stranici članka ili stranici za razgovor. Kutija za pretaživanje je uklonjena iz navigacione trake sa lijeve strane u gornji desni ugao zbog lakšeg pristupa i bržeg traženja.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Poboljšanja trake za uređivanje
Traka za uređivanje koja se može sakriti može sakriti ikone alata koji se ne upotrebljavaju često. Potvrđeni korisnici mogu proširiti alatnu traku i pristupiti posebnim ikonama.
Pomoćne informacije (prečice) su ugrađene u alatnu traku.
Ikone alatne trake su nanovo dizajnirane koristeći ikone Tango i Gnome.
<div style="clear:both;"></div>',
);

/** Catalan (Català)
 * @author Paucabot
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
	'optin-success-in' => 'Ara estau provant Beta.
Podeu sortir en qualsevol moment clicant sobre l\'enllaç "{{int:optin-leave}}" que teniu al cantó superior dret.',
	'optin-leave-cancel' => 'Si voleu continuar usant Beta, podeu tornar a $1.',
	'optin-success-out' => 'Heu sortit de Beta.
Podeu provar-la una altra vegada en qualsevol moment clicant sobre l\'enllaç "{{int:optin-try}}" que teniu al cantó superior dret de la pantalla.',
	'optin-success-feedback' => 'Gràcies per la vostra opinió!',
	'optin-accept-short' => 'Fem-ho!',
	'optin-accept-long' => 'Provau Beta',
	'optin-accept-long-anon' => 'Iniciau una sessió i provau Beta',
	'optin-deny-short' => 'No, gràcies',
	'optin-deny-long' => 'Torna a la pàgina anterior',
	'optin-submit-out' => 'Surt de Beta',
	'optin-submit-feedback' => 'Donau la vostra opinió',
	'optin-try' => 'Provau Beta',
	'optin-leave' => 'Sortiu de Beta',
	'optin-feedback' => 'Doneu la vostra opinió',
	'optin-survey-yes' => 'Sí',
	'optin-survey-no' => 'No',
	'optin-survey-intro' => 'Gràcies per provar Beta.
Volem saber què n\'opinau de la nova interfície, així que us agrairíem que responguéssiu l\'enquesta voluntària que teniu a continuació abans de clicar "[[#leave|{{int:optin-submit-out}}]]".',
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
	'optin-improvements' => "== Què s'ha millorat? ==
[[File:UsabilityNavigation.png|left|link=]]
;Navegació millorada
El nou sistema de navegació fa més fàcil veure on sou i què estau fent, així com on podeu anar i com arribar-hi. Amb la caixa de cerques situada al cantó superior dret, l'opció de cercar és més fàcil de trobar i més ràpida d'usar.
<div style=\"clear:both;\"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Millores a la barra d'edició
Noves icones fan més clar quina acció realitza cada eina. Les seccions desplegables redueixen els ecos paràsits i a la vegada mantenen les eines poc usades a només un clic.
La secció d'ajuda dóna un ràpid accés a una guia dels usos més comuns del marcatge wiki.
<div style=\"clear:both;\"></div>

== Com podeu col·laborar ==
Per a saber més o col·laborar amb el projecte, visiteu [http://usability.wikimedia.org la wiki de la ''Usability Initiative'']. Allà trobareu informació sobre el nostre projecte, recerca, disseny i desenvolupament i llançaments .  Per fer preguntes o deixar comentaris generals, per favor, usau la nostra [http://usability.wikimedia.org/wiki/Talk:Prototype Pàgina de discussió del prototipus].  Per demanar temes concrets, per favor, usau les pàgines de discussió associades. Si trobau errors en el programari, deixau els vostres comentaris a [http://usability.wikimedia.org/wiki/Talk:Releases/Acai la pàgina de discussió del llançament Acai] o usau [https://bugzilla.wikimedia.org Bugzilla].

Moltes gràcies!",
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
	'optin-try' => 'Vyzkoušet Betu',
	'optin-leave' => 'Opustit Betu',
	'optin-survey-yes' => 'Ano',
	'optin-survey-no' => 'Ne',
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
	'optin-improvements' => '== Co jsme vylepšili? ==
[[File:UsabilityNavigation.png|left|link=]]
;Zdokonalená navigace
Díky novému systému navigace snáze vidíte, kde jste a co děláte, stejně tak i to, kam můžete jít a jak se tam dostanete. Vyhledávání umístěné v pravém horním rohu lze jednodušeji nalézt a rychleji používat.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Vylepšení editačního panelu nástrojů
Nové a vylepšené ikonky zřetelněji označují, co který nástroj dělá. Díky rozbalovacím částem je panel jednodušší, přitom méně používané nástroje jsou dostupné jediným kliknutím.
Část s nápovědou poskytuje rychlý přístup k přehledu základního použití wikikódu.
<div style="clear:both;"></div>
== Jak se zapojit ==
Pokud se chcete o projektu dozvědět více nebo se zapojit, navštivte [http://usability.wikimedia.org wiki Iniciativy použitelnosti]. Najdete tam informace o našem grantu, výzkumu, návrhu a vývoji i vydaných verzích. Pokud se chcete na něco zeptat nebo nám něco sdělit, použijte naši [http://usability.wikimedia.org/wiki/Talk:Prototype diskusi k prototypu]. Připomínky ke konkrétním tématům můžete psát na příslušné diskusní stránky. Pokud najdete nějakou chybu, můžete ji oznámit v [http://usability.wikimedia.org/wiki/Talk:Releases/Acai diskusi k vydání verze Acai] nebo pomocí [https://bugzilla.wikimedia.org Bugzilly].

Děkujeme a budeme se těšit na vaše názory!',
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
	'optin-survey-question-changes' => 'Welche Änderungen an der Beta würdest du gerne in Zukunft sehen (wähle alles Zutreffende aus)?',
	'optin-survey-answer-changes-nav' => 'Die Gesamtnavigation der Site einfacher machen.',
	'optin-survey-answer-changes-edittools' => 'Bearbeitungswerkzeuge erweitern und verbessern.',
	'optin-survey-answer-changes-upload' => 'Inhalt und Medienuploads verbessern.',
	'optin-survey-answer-changes-richtext' => 'Bearbeitungswerkzeuge ähnlich wie bei Rich-Text-Editoren entwickeln.',
	'optin-survey-answer-changes-lookfeel' => 'Erscheinungsbild der Site aktualisieren.',
	'optin-survey-answer-changes-predictability' => 'Vorhersehbarkeit der Leistung verbessern.',
	'optin-survey-answer-changes-custom' => 'Größere Benutzeranpassungen erlauben.',
	'optin-survey-answer-changes-other' => 'Andere:',
	'optin-improvements' => '== Was wurde verbessert? ==
[[File:UsabilityNavigation.png|left|link=]]
;Erweiterte Navigation
Durch das neue Nagivationssystem wird es einfacher zu bestimmen, wo man sich gerade befindet, was man tut und welche anderen Seiten man wie erreichen kann. Um das Auffinden von Information noch schneller und einfacher zu gestalten, wurde beispielsweise das Suchfeld nach oben rechts verschoben.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Verbesserungen der Werkzeugleiste
Neue und verbesserte Symbole tragen dazu bei, dass man die gewünschte Funktion einfach und schnell erreichen kann. Mithilfe ausklappbarer Abschnitte konnte die Menüstruktur einerseits übersichtlich gehalten werden, andererseits sind so auch selten verwendete Werkzeuge nur einen Klick entfernt.
Der Hilfeabschnitt erlaubt den schnellen Zugriff auf häufig verwendete Wikisyntax.
<div style="clear:both;"></div>

== Wie kann ich mich daran beteiligen? ==
Um mehr über das Projekt zu erfahren oder selbst daran teilzunehmen, finden sich im [http://usability.wikimedia.org Wiki des Usability-Projekts] weitere Informationen, zum Beispiel über unsere Spende, Forschung, Design und Entwicklung sowie über neue Versionen. Für Fragen oder allgemeine Rückmeldungen steht die [http://usability.wikimedia.org/wiki/Talk:Prototype Diskussionsseite des Prototyps] zur Verfügung. Für spezielle Themen eignen sich die entsprechenden dazugehörigen Diskussionsseiten. Solltest du Fehler („Bugs“) entdecken, hinterlasse bitte unter http://usability.wikimedia.org/wiki/Talk:Releases/Acai einen entsprechenden Hinweis oder melde die Fehler über [https://bugzilla.wikimedia.org Bugzilla].

Vielen Dank, wir freuen uns auf dein Feedback!',
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
	'optin-needlogin' => 'Musyš se [$1 pśizjawiś], aby Betu wopytał.',
	'optin-intro' => 'Iniciatiwa wužywajobnosći Wikipedije jo južo žurnje źěłała, aby pólěpšyła wužywajobnosć Wikipedije. By ty rady Betu wopytaś?',
	'optin-success-in' => 'Wopytujoš něnto Betu.
Móžoš kuždy cas pśez kliknjenje na wótkaz "{{int:optin-leave}}" górjejce na wobrazowce wótwóliś.',
	'optin-success-out' => 'Sy wuspěšnje Betu spušćił.
Móžoš ju hyšći raz kuždy cas pśez kliknjenje na wótkaz "{{int:optin-try}}" górjejce napšawo na wobrazowce wopytaś.',
	'optin-success-feedback' => 'Žěkujomy se za twójo wótegrono!',
	'optin-accept-long' => 'Betu wopytaś',
	'optin-accept-long-anon' => 'Pśizjawiś se a Betu wopytaś',
	'optin-deny-short' => 'Ně, źěkujomy se',
	'optin-submit-out' => 'Betu spušćiś',
	'optin-try' => 'Betu wopytaś',
	'optin-leave' => 'Betu spušćiś',
	'optin-survey-yes' => 'Jo',
	'optin-survey-no' => 'Ně',
	'optin-survey-intro' => 'Źěkujomy se za wopytanje Bety.
My by rady wěźeli, co mysliš wó našem nowem wužywarskem pówjerchu, togodla my by se wjaselili, jolic ty by wupołnił opcionalny napšašnik dołojce, nježli až kliknjoš na "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-whyoptout' => 'Cogodla spušćaś Betu? (Pšosym wubjeŕ wšykne, kótarež pśitrjefje.)',
	'optin-survey-answer-whyoptout-hard' => 'Wužywanje jo było pśekomplicěrowane.',
	'optin-survey-answer-whyoptout-didntwork' => 'Njejo pórědnje funkcioněrowało.',
	'optin-survey-answer-whyoptout-other' => 'Druga pśicyna:',
	'optin-survey-question-feedback' => 'Pšosym daj nam swóje měnjenja k wěsći:',
	'optin-survey-question-browser' => 'Kótary wobglědowak wužywaš?',
	'optin-survey-answer-browser-other' => 'Drugi wobglědowak:',
	'optin-survey-question-os' => 'Kótary źěłowy system wužywaš?',
	'optin-survey-answer-os-other' => 'Drugi źěłowy system:',
	'optin-survey-question-res' => 'Kótare jo rozeznaśe twójeje wobrazowki?',
	'optin-survey-answer-changes-other' => 'Druge:',
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
	'optin-survey-answer-changes-other' => 'Άλλο:',
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
[[File:UsabilityNavigation.png|left|link=]]
;Navegación mejorada
El nuevo sistema de navegación hace más fácil ver en donde estás y qué estás haciendo, también como dónde puedes ir y cómo ir. Con la tabla de búsqueda localizada en la esquina superior izquierda la búsqueda es más fácil de encontrar y más rápida de usar.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Mejoramientos de la barra de herramientas de edición
Nuevos-y-mejorados íconos hacen más claro qué acción cada herramienta realiza. Secciones expandibles reducen abarrotamiento mientras mantienen herramientas usuadas infrecuentemente solo a un clic de distancia.
La sección de ayuda provee acceso rápido a una referencia para usos comunes del lenguaje wiki.
<div style="clear:both;"></div>',
);

/** Estonian (Eesti)
 * @author Pikne
 */
$messages['et'] = array(
	'optin-title-optedout' => 'Proovi beetat',
	'optin-try' => 'Proovi beetat',
);

/** Basque (Euskara)
 * @author An13sa
 * @author Kobazulo
 */
$messages['eu'] = array(
	'optin-title-optedout' => 'Beta probatu',
	'optin-title-optedin' => 'Beta utzi',
	'optin-title-justoptedin' => 'Ongi etorri Betara',
	'optin-title-justoptedout' => 'Mila esker Beta probatzeagatik',
	'optin-title-feedback' => 'Beta feedbacka',
	'optin-needlogin' => '[$1 saioa hasi] behar duzu Beta probatzeko.',
	'optin-intro' => 'Wikipediaren Erabilgarritasun Iniziatiba lanean aritu da Wikipediaren erabilgarritasuna hobetzeko.
Nahiko al zenuke gure Beta probatu?',
	'optin-feedback-back' => 'Ez baduzu gure inkesta bete nahi, $1-(e)ra itzul zenezake.',
	'optin-success-out' => 'Betarik arrakastatsuko atera zara.
Nahi duzunean proba dezakezu berriro "{{int:optin-try}}" loturan klik eginda pantailaren goian eskuinaldean.',
	'optin-success-feedback' => 'Milesker zure feedbackarengatik!',
	'optin-accept-short' => 'Proba dezagun!',
	'optin-accept-long' => 'Beta probatu',
	'optin-accept-long-anon' => 'Saioa hasi eta Beta probatu',
	'optin-deny-short' => 'Ez, mila esker',
	'optin-deny-long' => 'Itzuli aurreko orrialdera',
	'optin-submit-out' => 'Beta utzi',
	'optin-submit-feedback' => 'Eman feedbacka',
	'optin-try' => 'Beta probatu',
	'optin-leave' => 'Beta utzi',
	'optin-feedback' => 'Beta feedbacka',
	'optin-survey-yes' => 'Bai',
	'optin-survey-no' => 'Ez',
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
	'optin-survey-question-different' => 'Zer egin genezakeen ezberdina zuk Beta erabiltzen jarraitzeko?',
	'optin-survey-question-browser' => 'Zein nabigatzaile erabiltzen duzu?',
	'optin-survey-answer-browser-other' => 'Beste nabigatzailerik:',
	'optin-survey-question-os' => 'Zein sistema eragile erabiltzen duzu?',
	'optin-survey-answer-os-other' => 'Beste sistema eragilerik:',
	'optin-survey-question-res' => 'Zein da zure pantailaren bereizmena?',
	'optin-survey-answer-changes-upload' => 'Edukiak eta artxiboen igoera hobetu.',
	'optin-survey-answer-changes-other' => 'Bestelakoak:',
	'optin-improvements' => '== Zer hobetu da? ==
[[File:UsabilityNavigation.png|left|link=]]
;Nabigazioa hobetua
Nabigazio sistema berriak non zauden ikusteko eta ezer egiten ari zaren jakiteko erraztasun handiagoa ematen du, hala nola nahi duzun lekura joan eta nahi duzuna lortzeko. Goian eskuinean dagoen bilaketa kutxarekin, bilaketak egitea errazagoa izango da.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Editatzeko tresna-barrako hobekuntzak
Ikono berria eta hobetuek erabili behar duzun tresna argiago erakusten dute. Gainera, gehien erabiltzen dituzun tresnak klik batera edukiko dituzu.
Laguntza atalak tresna hauen erabilera amankomunetarako erreferentzia azkar batera eramango zaitu .
<div style="clear:both;"></div>
== Gehiago jakiteko ==
Proiektuari buruz gehiago jakiteko edo parte hartzeko, joan zaitez [http://usability.wikimedia.org Erabilgarritasun Iniziatibaren wikira].  Orri horretan gure beka, ikerketa, diseinu + garapena, eta argitaratutako berriak ikusiko dituzu.  Galderak egiteko edo zure feedback orokorra uzteko, joan zaitez gure [http://usability.wikimedia.org/wiki/Talk:Prototype prototype eztabaida orrira]. Gai zehatzak helbideratzeko, erabili atxikitutako eztabaida orria.  Akatsik aurkitzen baduzu, utzi zure iruzkina [http://usability.wikimedia.org/wiki/Talk:Releases/Acai Acai release eztabaida orrian] edo [https://bugzilla.wikimedia.org Bugzilla] erabiliz.

Milesker eta zure berri izateko irrikan gaude!',
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
	'optin-feedback-back' => 'Jos et halua täyttää kyselylomaketta, voit palata kohteeseen $1.',
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
	'optin-survey-question-changes' => 'Mitä muutoksia haluaisit nähdä Betassa tulevaisuudessa (valitse kolme sopivaa)?',
	'optin-survey-answer-changes-nav' => 'Helpottaa sivuston navigointia ylipäätään.',
	'optin-survey-answer-changes-edittools' => 'Lisää ja tehosta mukkaustyökaluita.',
	'optin-survey-answer-changes-upload' => 'Paranna sisällön ja median lähettämistä.',
	'optin-survey-answer-changes-richtext' => 'Kehitä muokkaustyökaluita tekstinkäsittelyohjelmia muistuttavaksi.',
	'optin-survey-answer-changes-lookfeel' => 'Päivitä sivuston ulkonäköä ja käytettävyyttä.',
	'optin-survey-answer-changes-predictability' => 'Paranna esityksen ennustettavuutta.',
	'optin-survey-answer-changes-custom' => 'Sallii laajemman käyttäjäkohtaisen kustomoinnin.',
	'optin-survey-answer-changes-other' => 'Muu',
	'optin-improvements' => '== Mitä on uudistettu? ==
[[File:UsabilityNavigation.png|left|link=]]
;Paranneltu navigointi
Uudesta valikkojärjestelmästä näkee helpommin, missä olet ja mitä teet. Näet myös helpommin, minne voit mennä ja kuinka pääset sinne. Oikeassa yläkulmassa olevalla hakulaatikolla hakutoimintoa on helpompi ja nopeampi käyttää.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Muokkauspalkin parannukset
Uusista ja parannetuista kuvakkeista näkee helpommin, mitä mikäkin työkalu tekee. Laajennettavat osiot vähentävät epäjärjestystä, kun harvoin käytetyt työkalut ovat vain klikkauksen päässä.
Ohje-osiosta näkyy esimerkit wikimerkinnän yleiseen käyttöön nopeasti.
<div style="clear:both;"></div>',
);

/** French (Français)
 * @author IAlex
 * @author Kropotkine 113
 * @author Louperivois
 * @author PieRRoMaN
 * @author Urhixidur
 */
$messages['fr'] = array(
	'optin' => 'Participer à l’initiative d’utilisabilité',
	'optin-desc' => 'Permet aux utilisateurs d’essayer la bêta Açai, les améliorations de l’initiative d’utilisabilité',
	'optin-title-optedout' => 'Essayer la bêta Açai',
	'optin-title-optedin' => 'Quitter la bêta Açai',
	'optin-title-justoptedin' => 'Bienvenue à la bêta Açai',
	'optin-title-justoptedout' => 'Merci pour votre participation à la bêta Açai',
	'optin-title-feedback' => 'Donnez votre avis',
	'optin-needlogin' => 'Vous devez vous [$1 connecter] pour essayer la bêta Açai',
	'optin-intro' => 'L’initiative d’utilisabilité de Wikipédia a travaillé dur pour améliorer l’utilisabilité des projets Wikimédia.
Voulez-vous essayer la bêta Açai ?',
	'optin-feedback-intro' => 'Merci d’essayer la Bêta.
Nous aimerions savoir ce que vous pensez de notre nouvelle interface, nous apprécierions que vous remplissiez le questionnaire ci-dessous.',
	'optin-feedback-back' => 'Si vous ne voulez pas remplir le questionnaire, vous pouvez revenir à $1.',
	'optin-success-in' => 'Vous êtes en train de tester la Bêta.
Vous pouvez interrompre la session à tout moment en cliquant sur le lien « {{int:optin-leave}} » en haut de l’écran.',
	'optin-leave-cancel' => 'Si vous voulez continuer à utiliser la bêta Acai, vous pouvez revenir à $1',
	'optin-success-out' => 'Vous avez quitté la Bêta.
Vous pouvez de nouveau participer à tout moment en cliquant sur le lien « {{int:optin-try}} » en haut à droite de l’écran.',
	'optin-success-feedback' => 'Merci pour votre avis !',
	'optin-accept-short' => 'Allons-y !',
	'optin-accept-long' => 'Essayer la bêta Açai',
	'optin-accept-long-anon' => 'Connectez-vous et essayez la Bêta',
	'optin-deny-short' => 'Non merci',
	'optin-deny-long' => 'Revenir à la page précédente',
	'optin-submit-out' => 'Quitter la bêta Açai',
	'optin-submit-feedback' => 'Donnez votre avis',
	'optin-try' => 'Essayer la bêta Açai',
	'optin-leave' => 'Quitter la bêta Açai',
	'optin-feedback' => 'Donnez votre avis',
	'optin-survey-yes' => 'Oui',
	'optin-survey-no' => 'Non',
	'optin-survey-intro' => 'Merci pour votre essai de la bêta Açai.

Nous aimerions savoir ce que vous pensez de notre nouvelle interface, nous apprécierions que vous remplissiez le sondage optionnel avant de cliquer sur « [[#leave|{{int:optin-submit-out}}]] ».',
	'optin-survey-question-likedislike' => 'Qu’avez-vous aimé dans la bêta Acai ? Qu’est-ce que vous n’avez pas aimé dans la bêta Acai ?',
	'optin-survey-question-whyoptout' => 'Pourquoi quittez-vous la Bêta ? (sélectionnez tout ce qui convient)',
	'optin-survey-answer-whyoptout-hard' => 'Il était trop difficile de l’utiliser.',
	'optin-survey-answer-whyoptout-didntwork' => 'Ça ne fonctionne pas correctement.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Cela ne s’est pas passé comme prévu.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Je n’ai pas aimé son apparence.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Je n’ai pas aimé les nouveaux onglets et la nouvelle disposition.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Je n’ai pas aimé la nouvelle barre d’outils.',
	'optin-survey-answer-whyoptout-other' => 'Autre raison :',
	'optin-survey-question-explain' => 'Expliquez ou élaborez votre réponse ci-dessus :',
	'optin-survey-question-techfail' => 'Avez-vous rencontré des problèmes techniques en utilisant la bêta Açai ?',
	'optin-survey-question-techfail-ifyes' => 'Si oui, expliquez ou élaborez :',
	'optin-survey-question-usedtoolbar' => 'Avez-vous utilisé la nouvelle barre d’outils déroulable pour modifier une page ?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Si oui, qu’avez vous aimé et que n’avez-vous pas aimé à son propos ?',
	'optin-survey-question-usedtoolbar-ifno' => 'Si non, pourquoi ? L’utiliserez-vous dans le futur ? Expliquez.',
	'optin-survey-question-different' => 'Qu’est-ce qui aurait dû être différent pour que vous continuiez à utiliser notre Bêta ?',
	'optin-survey-question-feedback' => 'Donnez-nous votre réaction :',
	'optin-survey-question-browser' => 'Quel navigateur utilisez-vous ?',
	'optin-survey-answer-browser-other' => 'Autre navigateur :',
	'optin-survey-question-os' => 'Quel système d’exploitation utilisez-vous ?',
	'optin-survey-answer-os-other' => 'Autre système d’exploitation :',
	'optin-survey-question-res' => 'Quelle est la résolution de votre écran ?',
	'optin-survey-question-changes' => 'Qu’aimeriez-vous voir dans la Bêta dans le futur (sélectionnez tout ce qui convient) ?',
	'optin-survey-answer-changes-nav' => 'Faire que la navigation générale du site soit plus simple à utiliser.',
	'optin-survey-answer-changes-edittools' => 'Améliorer et augmenter les outils d’édition.',
	'optin-survey-answer-changes-upload' => 'Améliorer l’importation de contenu et de média.',
	'optin-survey-answer-changes-richtext' => 'Développer des outils de modification similaires aux éditeurs en texte enrichi.',
	'optin-survey-answer-changes-lookfeel' => 'Changer l’apparence du site.',
	'optin-survey-answer-changes-predictability' => 'Améliorer la prévisibilité des modifications.',
	'optin-survey-answer-changes-custom' => 'Permettre une plus grande personnalisation de l’interface.',
	'optin-survey-answer-changes-other' => 'Autre :',
	'optin-improvements' => '== Qu’est-ce qui a été amélioré ? ==
[[File:UsabilityNavigation.png|left|link=]]
;Navigation enrichie
Le nouveau système de navigation vous permet de voir plus facilement où vous êtes et ce que vous faites, de même que où vous pouvez aller et comment vous y rendre. Avec la boîte de recherche située dans le coin supérieur droit, la fonctionnalité de recherche est plus facile à trouver et plus rapide à utiliser.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Améliorations de la barre d’outils
De toutes nouvelles icônes clarifient l’action effectuée par chaque outil. Des sections expansibles réduisent le désordre tout en gardant l’accès aux outils plus rarement utilisés à portée de clic.
La section d’aide fournit un accès rapide à la documentation sur l’utilisation courante des balises wiki.
<div style="clear:both;"></div>

== Comment s’impliquer ==
Pour en savoir davantage à propos du projet ou pour s’y impliquer, visitez [http://usability.wikimedia.org le wiki de l’initiative d’utilisabilité]. Vous y trouverez des informations sur notre subvention, la recherche, la conception et le développement. Pour poser des questions ou pour laisser des commentaires, merci d’utiliser notre [http://usability.wikimedia.org/wiki/Talk:Prototype page de discussion sur le prototype]. Pour aborder des sujets particuliers, utilisez les pages de discussion associées. Si vous trouvez des bogues, merci de laisser un message sur [http://usability.wikimedia.org/wiki/Talk:Releases/Acai la page de discussion de la version Açai] ou via [https://bugzilla.wikimedia.org Bugzilla].

Merci, nous nous réjouissons de votre intérêt !',
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
	'optin-survey-question-changes' => 'Que cambios lle gustaría ver en Beta no futuro (seleccione todas as que cumpran)?',
	'optin-survey-answer-changes-nav' => 'Facer a navegación xeral do sitio máis doada de usar.',
	'optin-survey-answer-changes-edittools' => 'Aumentar e mellorar as ferramentas de edición.',
	'optin-survey-answer-changes-upload' => 'Mellorar o contido e as cargas multimedia.',
	'optin-survey-answer-changes-richtext' => 'Desenvolver ferramentas de edición similares aos editores de texto.',
	'optin-survey-answer-changes-lookfeel' => 'Actualizar o aspecto e apariencia do sitio.',
	'optin-survey-answer-changes-predictability' => 'Mellorar a previsibilidade dos resultados.',
	'optin-survey-answer-changes-custom' => 'Permitir unha maior personalización por parte dos usuarios.',
	'optin-survey-answer-changes-other' => 'Outros:',
	'optin-improvements' => '== Que se mellorou? ==
[[File:UsabilityNavigation.png|left|link=]]
;Navegación realzada
O novo sistema de navegación fai máis doado ver onde se está e que se está facendo, así como onde se pode ir e como chegar ata alí. Co cadro de procuras localizado no canto superior dereito obtense un acceso máis fácil para así poder realizar unha busca máis rápida.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Melloras na barra de ferramentas de edición
As novas e melloradas iconas fan máis clara a identificación da acción que realiza cada unha. As seccións expansibles reducen a desorde á vez que manteñen as ferramentas menos usadas a só un clic de distancia.
A sección de axuda proporciona un acceso rápido a unha referencia cos usos comúns do formato wiki.
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
	'optin-survey-question-changes' => 'Weli Änderige wottsch gärn säh bi Beta in Zuechumpft (wehl bitte alli uus, wu s zuetrifft)?',
	'optin-survey-answer-changes-nav' => 'D Gsamtnavigation vu dr Site eifacher mache.',
	'optin-survey-answer-changes-edittools' => 'Bearbeitigswärchzyyg erwytere un verbessere.',
	'optin-survey-answer-changes-upload' => 'Inhalt un s Uffelade vu Medie verbessere.',
	'optin-survey-answer-changes-richtext' => 'Bearbeitigswärchzyyg entwickle, wu ähnlig sin wie d Rich-Text-Editore.',
	'optin-survey-answer-changes-lookfeel' => 'Erschynigsbild vu dr Site aktualisiere.',
	'optin-survey-answer-changes-predictability' => 'Vorhärsähbarkeit vu dr Leischtig verbessere.',
	'optin-survey-answer-changes-custom' => 'Greßeri Benutzeraapassige erlaube.',
	'optin-survey-answer-changes-other' => 'Anderi:',
	'optin-improvements' => '== Was isch verbesseret wore? ==
[[File:UsabilityNavigation.png|left|link=]]
;Verbessereti Navigation
Di nej Navigation macht s eifacher z säh, wu du grad bisch un was du machsch un au wu Du chasch ane goh un wie du dert ane chunnsch. S Suechfäld isch jetz im obere rächte Ecke un eso hscnäller z finde un eifacher z bruche.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Verbesserige am Wärchzygchäschtli

Fortgschritteni Benutzer chenne s Wärchzygchäschtli uusklappe go speziälleri Wärchzyg uuswehle.
Hilfe (Spickzettel) sin in s Wärchzygchäschtli dryygsetzt wore.
D Grafike vum Wärchzygchäschtli sin nej designed wore mit Hilf vu Tango- un Gnome-Grafike.
Neji un verbessereti Grafike mache s klarer, was du chasch mache mit dr einzelne Wärchzyg. Uusklappbari Abschnitt verringere s Durenander, di wenig bruchte Wärchzyg sin aber nume ei Klick ewäg.
D Hilf-Sektion stellt e schnölle zuegang z Verfiegig zuen ere Quälle vu gängige Aawändige vu Wiki-Zueschläg 
<div style="clear:both;"></div>',
);

/** Hebrew (עברית)
 * @author Rotem Liss
 * @author Rotemliss
 * @author YaronSh
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
	'optin-success-feedback' => 'תודה על המשוב!',
	'optin-deny-short' => 'לא תודה',
	'optin-submit-out' => 'ביטול הרשמה',
	'optin-survey-yes' => 'כן',
	'optin-survey-no' => 'לא',
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
	'optin-survey-answer-changes-other' => 'אחר:',
);

/** Croatian (Hrvatski)
 * @author Suradnik13
 */
$messages['hr'] = array(
	'optin-title-optedout' => 'Isprobajte Beta',
	'optin-title-optedin' => 'Izađite iz Beta',
	'optin-title-justoptedin' => 'Dobrodošli na Beta',
	'optin-title-justoptedout' => 'Hvala što ste isprobali našu Beta',
	'optin-title-feedback' => 'Beta komentari',
	'optin-needlogin' => 'Morate se [$1 prijaviti] kako biste isprobali Beta.',
	'optin-success-feedback' => 'Hvala Vam na Vašem komentaru!',
	'optin-accept-short' => 'Idemo probati!',
	'optin-accept-long' => 'Isprobajte Beta',
	'optin-accept-long-anon' => 'Prijavite se i isprobajte Beta',
	'optin-deny-short' => 'Ne hvala',
	'optin-deny-long' => 'Povratak na prethodnu stranicu',
	'optin-submit-out' => 'Izađite iz Beta',
	'optin-submit-feedback' => 'Ostavite komentare',
	'optin-try' => 'Isprobajte Beta',
	'optin-survey-yes' => 'Da',
	'optin-survey-no' => 'Ne',
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
	'optin-title-feedback' => 'Powěsć dać',
	'optin-needlogin' => 'Dyrbiš so [$1 přizjewić], zo by Beta wupruwował',
	'optin-intro' => 'Iniciatiwa wužiwajomnosće Wikipedije intensiwnje na polěpšenju wužiwajomnosće Wikipedije dźěła. By rady Betu wupruwował?',
	'optin-feedback-intro' => 'Dźakujemy so za wupruwowanje Bety.
Bychmy rady wědźeli, što mysliš wo našim nowym wužiwarskim powjerchu, bychmy so wjeselili, jeli by opcionelny naprašnik deleka wupjelnił.',
	'optin-success-in' => 'Wupruwuješ nětko Betu. Móžeš ju kóždy čas přez kliknjenja na wotkaz  kóždy čas přez kliknjenje na "{{int:optin-leave}}" horjeka na wobrazowce wopušćić.',
	'optin-leave-cancel' => 'Jeli rady chceš Beta dale wužiwać, móžeš so k $1 wróćić.',
	'optin-success-out' => 'Sy Betu wuspěšnje wopušćił. Móžeš ju kóždy čas přez kliknjenje na wotkaz "{{int:optin-try}}" horjeka naprawo na wobrazowce wupruwować.',
	'optin-success-feedback' => 'Dźakujemy so za twoju powěsć!',
	'optin-accept-short' => 'Čińmy to!',
	'optin-accept-long' => 'Betu wupruwować',
	'optin-accept-long-anon' => 'Přizjewić so a Betu wupruwować',
	'optin-deny-short' => 'Ně, dźakujemy so',
	'optin-deny-long' => 'wróćo k předchadnej stronje',
	'optin-submit-out' => 'Beta wopušćić',
	'optin-submit-feedback' => 'Powěsć dać',
	'optin-try' => 'Betu wupruwować',
	'optin-leave' => 'Beta wopušćić',
	'optin-feedback' => 'Powěsć dać',
	'optin-survey-yes' => 'Haj',
	'optin-survey-no' => 'Ně',
	'optin-survey-intro' => 'Dźakujemy so za wupruwowanje Bety.

Bychmy rady wědźeli, što mysliš wo našim nowym wužiwarskim powjerchu, bychmy so wjeselili, jeli by opcionelny naprašnik deleka wupjelnił, prjedy hač kliknješ na  "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Što so ći na Beće lubi? Što so ći na Beće njelubi?',
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
	'optin-survey-answer-changes-other' => 'Druhe:',
);

/** Hungarian (Magyar)
 * @author Dani
 */
$messages['hu'] = array(
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
	'optin-accept-short' => 'Kipróbálom!',
	'optin-accept-long' => 'A béta kipróbálása',
	'optin-accept-long-anon' => 'Jelentkezz be, és próbáld ki a bétát',
	'optin-deny-long' => 'Visszatérés az előző lapra',
	'optin-try' => 'Bétateszt',
	'optin-feedback' => 'Visszajelzés a bétáról',
	'optin-improvements' => '== Mit fejlesztettünk? ==
[[File:UsabilityNavigation.png|left|link=]]
;Fejlettebb navigáció
Az új navigációs rendszer könnyebbé teszi, hogy lásd, hol vagy és mit csinálsz éppen, továbbá azt is, hogy hová mehetsz továb, és hogy hogyan juthatsz el oda. A keresőmező jobb felső sarokba való áthelyezésével könnyebbé válik a keresés.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Kibővített szerkesztőeszköztár
Az új ikonok sokkal jobban reprezentálják, hogy melyik gomb mire való. A kinyitható részek elrejtik a ritkábban használt eszközöket, átláthatóbbá téve az eszköztárat, miközben továbbra is egyetlen kattintásra elérhetőek.
A Segítség részben a wiki-jelölőnyelv leggyakrabban használt elemeinek leírását találhatod meg.
<div style="clear:both;"></div>
== Hogyan vehetsz részt? ==
Ha többet szeretnél tudni a projektről, vagy be szeretnél kapcsolódni, látogasd meg a [http://usability.wikimedia.org Usability Initiative wikiét].  Itt információkat találsz az indulásunkról, kutatásról, tervezésről és fejlesztéstől, valamint a kiadásainkról.  Kérdéseidet vagy visszajelzéseidet a [http://usability.wikimedia.org/wiki/Talk:Prototype prototípus megbeszélési lapján] várjuk.  Konkrét probléma orvosolásához keresd fel a megfelelő vitalapot.  Ha hibával találkoznál, hagyj üzenetet az [http://usability.wikimedia.org/wiki/Talk:Releases/Acai Acai kiadás vitalapján] vagy a [https://bugzilla.wikimedia.org Bugzillán].

Köszönjük, és várjuk a visszajelzésedet!',
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
Tu pote disactivar lo a omne tempore con le ligamine "{{int:optin-leave}}" in alto a derecta del schermo.',
	'optin-leave-cancel' => 'Si tu vole continuar a usar Beta, tu pote retornar a $1',
	'optin-success-out' => 'Tu ha con successo quitate Beta.
Tu pote reactivar lo a omne tempore con le ligamine "{{int:optin-try}}" in alto a derecta del schermo.',
	'optin-success-feedback' => 'Gratias pro dar nos tu opinion!',
	'optin-accept-short' => 'In avante!',
	'optin-accept-long' => 'Probar Beta',
	'optin-accept-long-anon' => 'Aperir session e essayar Beta',
	'optin-deny-short' => 'No, gratias',
	'optin-deny-long' => 'retornar al previe pagina',
	'optin-submit-out' => 'Quitar Beta',
	'optin-submit-feedback' => 'Dar opinion',
	'optin-try' => 'Probar Beta',
	'optin-leave' => 'Quitar Beta',
	'optin-feedback' => 'Da nos tu opinion',
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
	'optin-survey-question-feedback' => 'Da nos tu reaction:',
	'optin-survey-question-browser' => 'Qual navigator usa tu?',
	'optin-survey-answer-browser-other' => 'Altere navigator:',
	'optin-survey-question-os' => 'Qual systema de operation usa tu?',
	'optin-survey-answer-os-other' => 'Altere systema de operation:',
	'optin-survey-question-res' => 'Qual es le resolution de tu schermo?',
	'optin-survey-question-changes' => 'Que cambios vole tu vider in le Beta in le futuro (selige tote le optiones applicabile)?',
	'optin-survey-answer-changes-nav' => 'Rende le navigation general del sito plus facile a usar.',
	'optin-survey-answer-changes-edittools' => 'Augmenta e meliora le instrumentos de modification.',
	'optin-survey-answer-changes-upload' => 'Meliora le cargamento de contento e multimedia.',
	'optin-survey-answer-changes-richtext' => 'Disveloppa instrumentos de modification similar al editores de texto inricchite.',
	'optin-survey-answer-changes-lookfeel' => 'Renova le apparentia general del sito.',
	'optin-survey-answer-changes-predictability' => 'Meliorar le previsibilitate del prestationes.',
	'optin-survey-answer-changes-custom' => 'Permitter un personalisation plus ample al usator.',
	'optin-survey-answer-changes-other' => 'Altere:',
	'optin-improvements' => '== Que ha essite meliorate? ==
[[File:UsabilityNavigation.png|left|link=]]
;Navigation plus efficace
Le nove systema de navigation rende plus facile vider ubi tu es e lo que tu face, e tamben a ubi tu pote ir e como arrivar. Con le quadro de recerca situate in alto a derecta, le facilitate de recerca es plus facile de trovar e plus rapide de usar.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Meliorationes del instrumentario de modification
Le icones nove e melior clarifica le action que cata instrumento exeque. Le sectiones expansibile simplifica le apparentia, durante que le instrumentos usate infrequentemente resta disponibile a solo un clic de distantia.
Le section de adjuta provide accesso rapide a un referentia pro usos commun del marcation wiki.
<div style="clear:both;"></div>
== Como participar ==
Pro ulterior informationes super le projecto o pro participar, per favor visita le [http://usability.wikimedia.org wiki del Initiativa de Usabilitate]. Ibi tu trovara informationes super nostre subvention, recerca, conception + disveloppamento, e edition de versiones. Pro poner questiones o lassar commentarios general, per favor usa nostre [http://usability.wikimedia.org/wiki/Talk:Prototype pagina de discussion super le prototypo]. Pro discuter subjectos particular, per favor usa le paginas de discussion dedicate a istes. Si tu trova defectos, lassa tu commentos in le [http://usability.wikimedia.org/wiki/Talk:Releases/Acai pagina de discussion super editiones de Acai] o invia los a [https://bugzilla.wikimedia.org Bugzilla].

Gratias e nos spera audir de te!',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 * @author Rex
 */
$messages['id'] = array(
	'optin' => 'Bergabung dengan Proyek Inisiatif Kebergunaan',
	'optin-desc' => 'Memungkinkan pengguna untuk bergabung dengan peningkatan kebergunaan dalam Proyek Inisiatif Kebergunaan',
	'optin-title-optedout' => 'Coba Beta',
	'optin-title-optedin' => 'Tinggalkan Beta',
	'optin-title-justoptedin' => 'Selamat datang di Beta',
	'optin-title-justoptedout' => 'Terima kasih telah mencoba Beta',
	'optin-title-feedback' => 'Umpan balik Beta',
	'optin-needlogin' => 'Anda harus [$1 masuk log] untuk dapat bergabung dengan peningkatan kebergunaan dari Proyek Inisiatif Kebergunaan.',
	'optin-intro' => 'Proyek Inisiatif Kebergunaan Wikipedia telah mengembangkan sebuah skin baru dan batang alat penyuntingan baru yang ditujukan untuk meningkatkan kebergunaan Wikipedia. Peningkatan ini belum diaktifkan untuk semua pengguna, tapi Anda dapat bergabung dengan Proyek ini dengan mengklik "{{int:optin-submit-in}}" di bawah ini.',
	'optin-success-in' => 'Anda telah berhasil bergabung dengan peningkatan kebergunaan dari Proyek Inisiatif Kebergunaan.
Anda dapat keluar kapan saja dengan mengklik "{{int:optin-submit-out}}" di bawah ini.',
	'optin-success-out' => 'Anda telah berhasil keluar dari Proyek Inisiatif Kebergunaan.
Anda dapat bergabung kembali kapan saja dengan mengklik "{{int:optin-submit-in}}" di bawah ini.',
	'optin-success-feedback' => 'Terima kasih atas umpan balik Anda!',
	'optin-accept-short' => 'Ayo lakukan!',
	'optin-accept-long' => 'Coba Beta',
	'optin-accept-long-anon' => 'Masuk log dan coba Beta',
	'optin-deny-short' => 'Tidak, terima kasih',
	'optin-deny-long' => 'Kembali ke halaman sebelum',
	'optin-submit-out' => 'Tinggalkan Beta',
	'optin-submit-feedback' => 'Berikan umpan balik',
	'optin-try' => 'Coba Beta',
	'optin-leave' => 'Tinggalkan Beta',
	'optin-feedback' => 'Umpan balik Beta',
	'optin-survey-yes' => 'Ya',
	'optin-survey-no' => 'Tidak',
	'optin-survey-intro' => 'Terima kasih untuk menguji coba peningkatan kebergunaan dari Proyek Inisiatif Kebergunaan.
Anda dapat keluar dengan mengklik "{{int:optin-submit-out}}" pada bagian bawah halaman ini.

Kami ingin mengetahui bagaimana pendapat Anda mengenai antarmuka baru kami, karenanya kami akan sangat menghargai jika Anda mengisi survei opsional berikut sebelum mengklik "{{int:optin-submit-out}}".',
	'optin-survey-question-whyoptout' => 'Mengapa Anda ingin meninggalkan Beta? (Pilihlah semua yang Anda anggap benar.)',
	'optin-survey-answer-whyoptout-hard' => 'Terlalu sulit untuk digunakan.',
	'optin-survey-answer-whyoptout-didntwork' => 'Tidak berfungsi dengan baik.',
	'optin-survey-answer-whyoptout-other' => 'Alasan lain:',
	'optin-survey-question-feedback' => 'Beritahukan kami tanggapan Anda:',
	'optin-survey-question-browser' => 'Penjelajah web apa yang Anda gunakan?',
	'optin-survey-answer-browser-other' => 'Penjelajah web lainnya:',
	'optin-survey-question-os' => 'Sistem operasi apa yang Anda gunakan?',
	'optin-survey-answer-os-other' => 'Sistem operasi lain:',
	'optin-survey-question-res' => 'Berapa besar resolusi layar Anda?',
	'optin-survey-answer-changes-other' => 'Lain-lain:',
);

/** Italian (Italiano)
 * @author Darth Kule
 * @author Stefano-c
 */
$messages['it'] = array(
	'optin-desc' => "Consente agli utenti di provare Beta, gli ultimi miglioramenti dell'iniziativa di usabilità.",
	'optin-title-optedout' => 'Prova la Beta',
	'optin-title-optedin' => 'Lascia Beta',
	'optin-title-justoptedin' => 'Benvenuto in Beta',
	'optin-title-justoptedout' => 'Grazie per aver provato Beta',
	'optin-title-feedback' => 'Feedback della Beta',
	'optin-needlogin' => "È necessario [$1 effettuare l'accesso] per provare Beta",
	'optin-intro' => "L'iniziativa di usabilità di Wikipedia sta lavorando intensamente per migliorare l'usabilità di Wikipedia.
Si desidera provare Beta?",
	'optin-feedback-intro' => 'Ti ringraziamo per aver provato la Beta.
Vorremmo sapere cosa ne pensi della nostra nuova interfaccia, quindi apprezzeremmo se compilassi il sondaggio facoltativo qui sotto.',
	'optin-feedback-back' => 'Se non si desidera compilare il sondaggio, si può tornare a $1.',
	'optin-success-in' => 'Ora stai provando la Beta.
Puoi uscirne in qualsiasi momento facendo clic sul link "{{int:optin-leave}}" nella parte superiore dello schermo.',
	'optin-leave-cancel' => 'Se si desidera continuare a usare Beta è possibile tornare a $1',
	'optin-success-out' => 'Beta è stato lasciato con successo.
È possibile provarlo nuovamente in qualsiasi momento facendo clic sul collegamento "{{int:optin-try}}" in alto a destra dello schermo.',
	'optin-success-feedback' => 'Grazie per il tuo feedback!',
	'optin-accept-short' => 'Facciamolo!',
	'optin-accept-long' => 'Prova la Beta',
	'optin-accept-long-anon' => 'Accedi e prova la Beta',
	'optin-deny-short' => 'No grazie',
	'optin-deny-long' => 'Torna alla pagina precedente',
	'optin-submit-out' => 'Lascia Beta',
	'optin-submit-feedback' => 'Lascia un feedback',
	'optin-try' => 'Prova Beta',
	'optin-leave' => 'Lascia Beta',
	'optin-feedback' => 'Feedback della Beta',
	'optin-survey-yes' => 'Sì',
	'optin-survey-no' => 'No',
	'optin-survey-intro' => 'Ti ringraziamo per aver provato il Beta.
Vorremmo sapere cosa ne pensi della nostra nuova interfaccia, quindi apprezzeremmo se compilassi il sondaggio facoltativo qui sotto prima di fare clic su "[[#leave|{{int:optin-submit-out}}]]".',
	'optin-survey-question-likedislike' => 'Cosa ti è piaciuto della Beta? Cosa non ti è piaciuto della Beta?',
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
	'optin-survey-question-usedtoolbar-ifyes' => 'Se sì, cosa ti è piaciuto? Cosa non ti è piaciuto?',
	'optin-survey-question-browser' => 'Quale browser usi?',
	'optin-survey-answer-browser-other' => 'Altri browser:',
	'optin-survey-question-os' => 'Quale sistema operativo usi?',
	'optin-survey-answer-os-other' => 'Altro sistema operativo:',
	'optin-survey-question-res' => 'Qual è la risoluzione dello schermo?',
	'optin-survey-question-changes' => 'Quali cambiamenti vorresti vedere in Beta in futuro (selezionare tutte le risposte pertinenti)?',
	'optin-survey-answer-changes-nav' => 'Rendere la navigazione del sito più semplice da usare.',
	'optin-survey-answer-changes-edittools' => 'Aumentare e migliorare gli strumenti di modifica.',
	'optin-survey-answer-changes-richtext' => 'Sviluppare strumenti di modifica simili a un editor rich-text.',
	'optin-survey-answer-changes-lookfeel' => "Aggiornare l'aspetto del sito.",
	'optin-survey-answer-changes-predictability' => 'Migliorare la prevedibilità delle prestazioni.',
	'optin-survey-answer-changes-custom' => "Consentire una maggiore personalizzazione all'utente.",
	'optin-survey-answer-changes-other' => 'Altro:',
	'optin-improvements' => '== Cosa è stato migliorato? ==
[[File:UsabilityNavigation.png|left|link=]]
;Navigazione potenziata
Il nuovo sistema di navigazione rende più semplice vedere dove ti trovi e cosa stai facendo, oltre a dove puoi andare e come puoi arrivarci. Con il box di ricerca situato nell\'angolo in alto a destra, la funzione di ricerca è più facile per trovare e più veloce da usare.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Miglioramenti della barra degli strumenti
Le icone nuove e migliorate rendono più chiare le azioni che ciascuno strumento esegue. Le sezioni espandibili riducono l\'ingombro mantenendo gli strumenti usati di rado a un clic di distanza. La sezione di aiuto fornisce un rapido accesso a un riferimento per gli usi comuni del wiki-markup.
<div style="clear:both;"></div>
== Come impegnarsi ==
Per saperne di più sul progetto o impegnarti, visita il [http://usability.wikimedia.org sito dell\'iniziativa di usabilità]. Lì potrai trovare informazioni sulla nostra sovvenzione, ricerca, design e sviluppo e rilasci. Per porre domande o lasciare un feedback generale, usa la nostra [http://usability.wikimedia.org/wiki/Talk:Prototype pagina di discussione sui prototipi]. Per discutere di particolari argomenti, usa le pagine di discussione associate. Se trovi dei bug, lascia un commento nella [http://usability.wikimedia.org/wiki/Talk:Releases/Acai pagina di discussione dei rilasci di Acai] o usa [https://bugzilla.wikimedia.org Bugzilla].

Grazie e attendiamo con ansia di sentirti.',
);

/** Japanese (日本語)
 * @author Aotake
 * @author Fryed-peach
 * @author Hosiryuhosi
 * @author Whym
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
	'optin-survey-question-changes' => '今後、ベータ版にどのような点の改善を望みますか？（当てはまるものをすべて選択してください）',
	'optin-survey-answer-changes-nav' => 'サイト全体の操作方法をより使いやすくしてほしい。',
	'optin-survey-answer-changes-edittools' => '編集ツールを増やし、機能強化してほしい。',
	'optin-survey-answer-changes-upload' => 'コンテンツやメディアアップロードを改善してほしい。',
	'optin-survey-answer-changes-richtext' => 'リッチテキストエディタのような編集ツールを開発してほしい。',
	'optin-survey-answer-changes-lookfeel' => 'サイトの「見た感じ」を更新してほしい。',
	'optin-survey-answer-changes-predictability' => '動作の安定性を改善してほしい。',
	'optin-survey-answer-changes-custom' => 'より強力なカスタマイズを可能にしてほしい。',
	'optin-survey-answer-changes-other' => 'その他:',
	'optin-improvements' => '== 何が改良されたのですか？ ==
[[File:UsabilityNavigation.png|left|link=]]
;ナビゲーションの向上
新しいナビゲーションシステムでは、現在見ているページがどこか、現在行っている作業なにかがわかりやすくなりました。また、どのページにアクセスでき、どうやってそれらのページにアクセスすればよいかも分かりやすくなりました。画面右上に検索ボックスを置くことで、検索バーを見つけやすく、より素早く利用できるようになりました。
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;編集ツールバーの改良
新しく改良されたアイコンにより、各ツールの機能をより明確に表現できるようになりました。編集ツールバーの各部分について表示・非表示を切り替えられるようにすることで、ツールバーのデザインを簡潔に保ちつつ、散発的に使用される機能にもすぐアクセスできるようにしました。ツールバーにヘルプを組み込むことで、一般的なウィキ・マークアップの説明を素早く参照できるようにしました。
<div style="clear:both;"></div>
== 参加するには ==
このプロジェクトについてより詳しいことが知りたい、あるいは参加したい場合は、[http://usability.wikimedia.org Usability Initiative のウィキ]を訪れてみてください。我々の助成、研究、設計・開発、およびリリースについての情報が得られます。質問をしたり全般的なフィードバックを伝えたい場合は、[http://usability.wikimedia.org/wiki/Talk:Prototype プロトタイプ議論ページ]を使ってください。特定の問題を解決したい場合は、該当する議論ページを使ってください。バグを発見した場合は、[http://usability.wikimedia.org/wiki/Talk:Releases/Acai Acai リリース議論ページ]か [https://bugzilla.wikimedia.org Bugzilla]を使ってコメントを伝えてください。

あなたからの意見を楽しみに待っています。',
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
	'optin-try' => 'მოსინჯეთ ბეტა',
	'optin-leave' => 'ბეტადან გასვლა',
	'optin-feedback' => 'გამოხმაურება',
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
	'optin-improvements' => '== რა გაუმჯობესდა? ==
[[File:UsabilityNavigation.png|left|link=]]
;გაუმჯობესებული ნავიგაცია
ახალი ნავიგაციის სისტემა საშუალებას გაძლევთ უფრო მარტივად გაერკვეთ სად ხართ და რას აკეთებთ, ასევე სად ან როგორ შეგიძლიათ გადახვიდეთ. ეკრანის მარჯვენა ზედა კუთხეში მოთავსებული საძიებო ველით ძიების ფუნქცია ადვილი მისაგნებია და მოხერხებულია.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;გაუმჯობესდა რედაქტირების პანელი
ახალი და გაუმჯობესებული ხატულები უფრო მკაფიოდ მიუთითებს ინსტრუმენტების დანიშნულებაზე. გახსნადი განყოფილებები საშუალებას იძლევა ნაკლებად გადაიტვირთოს პანელი, ხოლო ნაკლებად გამოყენებული ხელსაწყოების მიგნება მხოლოდ ერთი დაწკაპუნებით არის შესაძლებელი.
დახმარების სექცია სწრაფად მიგითითებთ ვიკი-მარკაპის გამოყენების დეტალებზე.
<div style="clear:both;"></div>
== მონაწილეობის მიღება ==
პროექტზე მეტი ინფორმაციის ან მასში მონაწილეობის მისაღებად, გთხოვთ ეწვიოთ [http://usability.wikimedia.org გამოყენებადობის ინიციატივის ვიკის].  აქ თქვენ იპოვით ინფორმაციას ჩვენს გრანტზე, კვლევაზე, დიზაინსა და დამუშავებაზე. შეკითხვებისთვის ან ზოგადი მოსაზრებებისთვის, გთხოვთ გამოიყენოთ ჩვენი [http://usability.wikimedia.org/wiki/Talk:Prototype განხილვის გვერდის პროტოტიპი].  კონკრეტული თემების განხილვისთვის, გთხოვთ გამოიყენოთ მათთან დაკავშირებული განხილვის გვერდები. თუ პროგრამაში შეცდომებს იპოვით, გთხოვთ კომენტარი დატოვოთ [http://usability.wikimedia.org/wiki/Talk:Releases/Acai Acai რელიზის განხილვის გვერზე] ან [https://bugzilla.wikimedia.org ბაგზილას] გამოყენებით.

გმადლობთ და ველით თქვენს გამოხმაურებებს!',
);

/** Korean (한국어)
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
	'optin-intro' => '위키백과에서는 사용자들의 편의성을 높이기 위해 새로운 스킨을 만들고 있습니다. 체험해 보세요!',
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
	'optin-try' => '베타 버전 체험하기',
	'optin-leave' => '베타 버전 체험 종료하기',
	'optin-feedback' => '피드백 남기기',
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
	'optin-improvements' => '== 어떤 부분이 향상되었나요? ==
[[File:UsabilityNavigation.png|left|link=]]
;디자인 구성이 바뀌었습니다
새로운 디자인을 통해 현재 어떤 문서를 보고 있는지, 어떤 동작을 하고 있는지, 어떤 문서를 어떻게 갈 수 있는지 등을 파악하기가 쉬워졌습니다. 또한, 검색 상자를 오른쪽 위로 옮겨 더욱 사용하기 편리해졌습니다.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;편집 툴바가 더 편리해졌습니다
툴바 버튼이 더 직관적인 모습으로 바뀌었습니다. 자주 쓰지 않는 기능은 숨겨 두어 더 사용하기 쉬워졌고, 숨겨진 기능은 클릭 한 번으로 꺼낼 수 있습니다.
또한 위키 문법을 사용하기 쉽도록 도움말 링크를 추가했습니다.
<div style="clear:both;"></div>
== 참여하기 ==

위키미디어 사용성 향상 프로젝트에 참여하려면 [http://usability.wikimedia.org 프로젝트 위키](영어)에 방문해주세요. 프로젝트에서 만든 연구 결과, 디자인, 개발 제품 등을 볼 수 있습니다. 일반적인 질문이나 조언이 있다면 [http://usability.wikimedia.org/wiki/Talk:Prototype 스킨 토론란](영어)를 이용해주시면 됩니다. 특정 주제에 대해서는 해당 토론란을 이용해주세요.
버그를 발견했다면  [http://usability.wikimedia.org/wiki/Talk:Releases/Acai Acai 토론란](영어)나 [https://bugzilla.wikimedia.org Bugzilla](영어)에 해당 사실을 알려주시면 됩니다.',
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
	'optin-survey-question-changes' => 'Wat för Änderunge dääts De en De Zohkunnf jähn en de Bata hann? Nemm alle, di paße.',
	'optin-survey-answer-changes-nav' => 'Maat et eröm Navvijeere en heh dä Web_ßait eijfaacher.',
	'optin-survey-answer-changes-edittools' => 'Maat mieh Wärkzüsch för et Beärbeide, un maat et beßer.',
	'optin-survey-answer-changes-upload' => 'Maat et Huhlaade, för Meedije un Enhallde, beßer.',
	'optin-survey-answer-changes-richtext' => 'Doht Wärkzüsh en dä Aat vun <i lang="en">„rich-text“ editors</i> för et Täxte-Ändere entweckele.',
	'optin-survey-answer-changes-lookfeel' => 'Doht Ußsinn un de Jeschtalldung vun dä Web-ßait neuer maache.',
	'optin-survey-answer-changes-predictability' => 'Maat de Leistung mieh afseebaar.',
	'optin-survey-answer-changes-custom' => 'Jrüßer Aanpassonge aan der einzelne Metmaacher müjjelesch maache.',
	'optin-survey-answer-changes-other' => 'Söns jet:',
	'optin-improvements' => '== Wat es bäßer jemaat woode? ==
[[File:UsabilityNavigation.png|left|link=]]
; verbäßert Eöm_jonn
Dat neu Süßteem zom eröm jonn määd_et leischter ze märke, woh De bes, un wat De jraad aam Donn bes, un woh De henjonn kann, un wi De doh hen küß. Dä Kaßte zom Söhke bove räähß es bäßer ze fenge un mer es flöcker mem Söhke. 
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
; bäßer Wärkzüsch zom Ändere
Neu un bäßer Mini-Beldsche maache kloorer, wat e Wärkzüsch deiht. Verjrüüßerbaa Afschnedde maache winnijer Dorjeneein un hallde sällde jebruch Wärkzüsch nur doch eine Klick wick.
<div style="clear:both;"></div>

== Wi mer metmaache kann ==
Öm mieh do drövver ze liehre, wat uns Projäk deiht, un wi mer doh beidraare kann, jangk op et [http://usability.wikimedia.org Wiki <i lang="en">Usability Initiative</i> ier Wiki]. Doh fengkß De Aanjabe üvver uns Finanze, Fochschung, Jeschtalldung un Äntwecklung, un Veröffentleschunge. Öm Froore loßzewääde odder alljemein Röckmäldunge affzejävve, jangk övver uns [http://usability.wikimedia.org/wiki/Talk:Prototype Prottotüpe-Klaafsigg]. Öm beschtemmpte Saache aanzeschpäsche, nämm de zopaß Klaafsigge. Wann de Fähler fengks, schrief Ding Beobachtunge op de [http://usability.wikimedia.org/wiki/Talk:Releases/Acai <i lang="pt-BR">Acai</i> Klaafsigg] udder nämm dr [https://bugzilla.wikimedia.org <i lang="en">Bugzilla</i>] doför.

Mer freue uns, vun Üsch ze hüüre!',
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
	'optin-deny-short' => 'Nee Merci',
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
	'optin-survey-question-likedislike' => 'Wat hutt dir bäi Beta gutt finnt? Wat huet Iech u Beta net gefall?',
	'optin-survey-question-whyoptout' => 'Firwat Verloost Dir dës Beta? (wielt w.e.g. alles aus wat zoutrëfft)',
	'optin-survey-answer-whyoptout-hard' => 'Et war ze komplizéiert fir ze benotzen.',
	'optin-survey-answer-whyoptout-didntwork' => 'Et huet net richteg fonctionnéiert.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Et huet net esou fonctionnéiert wéi virgesinn.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Et huet mir net gefall wéi et ausgesäit.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Ech hat déi nei Ongleten an den neie Layout net gär.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Ech hunn déi nei Toolbar net gär.',
	'optin-survey-answer-whyoptout-other' => 'Anere Grond:',
	'optin-survey-question-explain' => 'Erklärt oder preziséiert Är Äntwert w.e.g.:',
	'optin-survey-question-techfail' => 'Hat Dir technesch Schwieregkeete beim Benotze vu Beta?',
	'optin-survey-question-techfail-ifyes' => 'Wa jo, da begrënnt oder erklärt et:',
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
	'optin-improvements' => '== Wat gouf verbessert? ==
[[File:UsabilityNavigation.png|left|link=]]
;Erweidert Navigatioun
Dat neit Navigatiounssystem mécht et net nëmme méi einfacher fir ze gesinn, wou een ass a wat ee mécht, mee e weist och wuer ee goe kann. Mat der Sichbox uewe riets am Eck gëtt d\'Fanne méi einfach a méi séier.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Verbesserunge vun der Ännerungs-Toolbar
Nei a verbessert Symboler maachen et méi kloer, watfireng Aktioun hanner all Tool stécht. Abschnitter déi opgeklappt kënne ginn reduzéieren den Duercherneen, während sele benotzten Toolen nëmmen ee Klick ewech sinn.
Den Abschnitt mat der Hëllef erlaabt e séieren Zougrëff mat déi am dackste benotzte Wikisyntax.
<div style="clear:both;"></div>

== Wéi Dir matmaache kënnt ==
Fir méi iwwer dëse Projet gewuer ze ginn oder fir matzemaachen, gitt w.e.g. op d\'[http://usability.wikimedia.org Benotzerfrëndlechkeets-Initiative Wiki]. Do fannt Dir Informatiounen iwwer eise Financement, eis Recherche, eisen Design, eis Entwécklung an déi verschidde Versiounen. Fir Froen ze stellen oder en allgemenge Feedback ze hannerloossen benotzt w.e.g. eis [http://usability.wikimedia.org/wiki/Talk:Prototype Prototyp-Diskussiouns-Säit].  Fir méi prezies Sujeten ze diskutéieren benotzt w.e.g. déi jeweileg Diskussiounssäiten. Wann Dir Feeler fannt da mellt eis déi w.e.g. op der [http://usability.wikimedia.org/wiki/Talk:Releases/Acai Acai release Diskussiounssäit] oder andeem Dir [https://bugzilla.wikimedia.org Bugzilla] benotzt.

Merci a mir freën eis fir vun Iech ze héieren!',
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

/** Malayalam (മലയാളം)
 * @author Junaidpv
 */
$messages['ml'] = array(
	'optin-deny-short' => 'വേണ്ട, നന്ദി',
	'optin-survey-yes' => 'ശരി',
	'optin-survey-no' => 'അല്ല',
	'optin-survey-answer-whyoptout-other' => 'മറ്റു കാരണം:',
);

/** Maltese (Malti)
 * @author Chrisportelli
 */
$messages['mt'] = array(
	'optin-title-optedout' => 'Ipprova l-Beta',
	'optin-title-optedin' => 'Ħalli l-Beta',
	'optin-title-justoptedin' => 'Merħba fil-Beta',
	'optin-title-justoptedout' => 'Grazzi talli qed tipprova l-verżjoni Beta',
	'optin-needlogin' => 'Huwa neċessarju li l-ewwel [$1 tidħol fil-kont tiegħek] qabel ma tipprova l-Beta.',
	'optin-success-in' => 'Bħalissa qiegħed tipprova l-Beta.
Tista\' toħroġ barra minn din il-verżjoni billi tagħfas il-ħolqa "{{int:optin-leave}}" fil-quċċata tal-iskrin.',
	'optin-leave-cancel' => "Jekk tixtieq tkompli tuża' l-Beta, tista' tirritorna lejn $1.",
	'optin-success-out' => "Tħalliet b'suċċess il-verżjoni Beta.
Huwa possibbli li terġa' tipprova mill-ġdid f'kwalunkwe mument li tixtieq billi tagħfas il-ħolqa \"{{int:optin-try}}\" fin-naħa leminija tal-quċċata tal-iskrin.",
	'optin-success-feedback' => 'Grazzi għall-fehmiet tiegħek!',
	'optin-accept-short' => 'Nagħmluha!',
	'optin-accept-long' => 'Ipprova l-Beta',
	'optin-accept-long-anon' => 'Illogja u ipprova l-Beta',
	'optin-deny-short' => 'Le grazzi',
	'optin-deny-long' => 'Irritorna lejn il-paġna preċedenti',
	'optin-submit-out' => 'Ħalli l-Beta',
	'optin-submit-feedback' => 'Ħalli l-fehmiet tiegħek',
	'optin-try' => 'Ipprova l-Beta',
	'optin-leave' => 'Ħalli l-Beta',
	'optin-feedback' => 'Rispons tal-Beta',
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
);

/** Nedersaksisch (Nedersaksisch)
 * @author Servien
 */
$messages['nds-nl'] = array(
	'optin-survey-answer-whyoptout-other' => 'Aandere rejen:',
	'optin-survey-answer-os-other' => 'Aanders:',
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
	'optin-survey-question-changes' => 'Welke wijzigingen wilt u in de toekomst in Beta zien? (selecteer alle mogelijkheden die van toepassing zijn)',
	'optin-survey-answer-changes-nav' => 'Maak de navigatie van de site eenvoudiger.',
	'optin-survey-answer-changes-edittools' => 'Meer en betere bewerkingshulpmiddelen.',
	'optin-survey-answer-changes-upload' => 'Verbeter het toevoegen van inhoud en mediauploads.',
	'optin-survey-answer-changes-richtext' => 'Ontwikkel bewerkingshulpmiddelen vergelijkbaar met uitgebreide tekstverwerkers.',
	'optin-survey-answer-changes-lookfeel' => 'Werkt het uiterlijk en de werking van de bij naar de laatste inzichten.',
	'optin-survey-answer-changes-predictability' => 'Verbeter de voorspelbaarheid van de prestaties.',
	'optin-survey-answer-changes-custom' => 'Meer gebruikersaanpassingen mogelijk maken.',
	'optin-survey-answer-changes-other' => 'Overig:',
	'optin-improvements' => '== Wat is er verbeterd?==
[[File:UsabilityNavigation.png|left|link=]]
;Verbeterde navigatie
Het nieuwe navigatiesysteem maakt het makkelijker om te zien waar u bent en op wat u doet, als ook waar u heen kunt gaan en hoe er te komen.
Met het zoekvak in de rechterbovenhoek is de zoekfunctie makkelijker te vinden en sneller te gebruiken.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Verbeterde bewerkingswerkbalk
Nieuwe en verbeterde pictogrammen maken het duidelijker wat ieder hulpmiddel doet.
Uitklapbare onderdelen verminderen een rommelig uiterlijk, terwijl minder vaak gebruikte functies wel bereikbaar blijven.
De hulpfunctie biedt snelle toegang tot uitleg over veelgebruikte functies van de wikiopmaaktaal.
<div style="clear:both;"></div>
== Hoe meedoen ==
Om meer over het project te weten te komen, of om mee te doen, kunt u naar de [http://usability.wikimedia.org wiki van het Bruikbaarheidsinitiatief] gaan.
Daar treft u informatie aan over schenking, het onderzoek, ontwerp en ontwikkeling en de uitkomsten.
Voor vragen of terugkoppeling, kunt u de [http://usability.wikimedia.org/wiki/Talk:Prototype overlegpagina over het prototype] gebruiken.
Om specifieke punten aan de orde te stellen, kunt u de bijbehorende overlegpagina\'s gebruiken.
Als u softwarefouten aantreft, maak dan gebruik van de [http://usability.wikimedia.org/wiki/Talk:Releases/Acai overlegpagina voor Acai] of gebruik[https://bugzilla.wikimedia.org Bugzilla].

Dank u wel en we zien uit naar uw mening!',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Gunnernett
 * @author Harald Khan
 */
$messages['nn'] = array(
	'optin-needlogin' => 'Du må [$1 logga inn] for å prøva ut Beta.',
	'optin-survey-no' => 'Nei',
	'optin-survey-question-whyoptout' => 'Kvifor går du ut av Beta! (Ver venleg å vél alle aktuelle grunnar.)',
	'optin-survey-answer-whyoptout-hard' => 'Det var for vanskeleg å bruka.',
	'optin-survey-answer-whyoptout-didntwork' => 'Det fungerte ikkje på rett vis.',
	'optin-survey-answer-whyoptout-other' => 'Anna årsak:',
	'optin-survey-question-feedback' => 'Ver venleg og send ei tilbakemelding:',
	'optin-survey-question-browser' => 'Kva for ein nettlesar nyttar du?',
	'optin-survey-answer-browser-other' => 'Annan nettlesar:',
	'optin-survey-question-os' => 'Kva for operativsystem nyttar du?',
	'optin-survey-answer-os-other' => 'Anna operativsystem:',
	'optin-survey-question-res' => 'Kva er oppløysinga på skjermen din?',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 * @author Laaknor
 * @author Nghtwlkr
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
	'optin-accept-long' => 'Forsøk Beta',
	'optin-accept-long-anon' => 'Logg inn og forsøk Beta',
	'optin-deny-short' => 'Neitakk',
	'optin-deny-long' => 'Gå tilbake til forrige side',
	'optin-submit-out' => 'Forlat Beta',
	'optin-submit-feedback' => 'Gi tilbakemelding',
	'optin-try' => 'Forsøk Beta',
	'optin-leave' => 'Forlat Beta',
	'optin-feedback' => 'Beta tilbakemelding',
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
	'optin-survey-question-feedback' => 'Vennligst gi oss dine tilbakemeldinger:',
	'optin-survey-question-browser' => 'Hvilken nettleser bruker du?',
	'optin-survey-answer-browser-other' => 'Annen nettleser:',
	'optin-survey-question-os' => 'Hvilket operativsystem bruker du?',
	'optin-survey-answer-os-other' => 'Annet operativsystem:',
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
	'optin-survey-question-changes' => 'Qué vos agradariá veire dins la Beta dins lo futur (seleccionatz tot çò que conven) ?',
	'optin-survey-answer-changes-nav' => "Fa que la navigacion generala del site es mai simpla d'utilizar.",
	'optin-survey-answer-changes-edittools' => "Melhora e augmenta los espleches d'edicion.",
	'optin-survey-answer-changes-upload' => "Melhora l'impòrt de contengut e de mèdia.",
	'optin-survey-answer-changes-richtext' => "Desvolopa d'espleches de modificacions similars als editors en tèxte enriquit.",
	'optin-survey-answer-changes-lookfeel' => "Cambia l'aparéncia del site.",
	'optin-survey-answer-changes-predictability' => 'Melhora la previzibilitat de las performàncias.',
	'optin-survey-answer-changes-custom' => "Permet una màger personalizacion de l'interfàcia.",
	'optin-survey-answer-changes-other' => 'Autre :',
	'optin-improvements' => "== De qué es es estat melhorat ? ==
[[File:UsabilityNavigation.png|left|link=]]
;Navigacion enriquida
Lo sistèma de navigacion novèl vos permet de veire mau aisidament ont sètz e çò que fasètz, e mai ont podètz anar e cossí vos i rendre. Amb la boita de recèrca situada dins lo canton superior drech, la foncionalitat de recèrca es de melhor trobar e de melhor utilizar.
<div style=\"clear:both;\"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Melhoraments de la barra d'espleches
D'icòna tota novèlas clarifican l'accion efectuada per cada esplech. De seccions extensibles redusisson lo desòrdre tot en gardant l'accès als espleches utilizats mai raremant en un sol clic.
La seccion d'ajuda provesís un accès rapid a la documentacion sus l'utilizacion correnta de las balisas wiki.
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
	'optin-desc' => 'Pozwala użytkownikom testować wersję beta zrealizowaną w ramach inicjatywy użyteczności.',
	'optin-title-optedout' => 'Wypróbuj wersję beta',
	'optin-title-optedin' => 'Rezygnacja z wersji beta',
	'optin-title-justoptedin' => 'Witaj w wersji beta',
	'optin-title-justoptedout' => 'Dziękujemy za wypróbowanie wersji beta',
	'optin-title-feedback' => 'Opinie o wersji beta',
	'optin-needlogin' => 'Musisz [$1 zalogować się] by wypróbować wersję beta.',
	'optin-intro' => 'Podjęliśmy inicjatywę mającą poprawić użyteczność Wikipedii.
Czy chcesz wypróbować wersję beta?',
	'optin-feedback-intro' => 'Dziękujemy za wypróbowanie wersji beta.
Chcielibyśmy wiedzieć co myślisz o nowym interfejsie. Będziemy wdzięczni, jeśli wypełnisz poniższą ankietę.',
	'optin-feedback-back' => 'Jeśli nie chcesz wypełnić ankiety, możesz powrócić do $1.',
	'optin-success-in' => 'Rozpocząłeś testowanie wersji beta.
W każdej chwili możesz je wyłączyć poprzez kliknięcie linku „{{int:optin-submit-out}}” umieszczonego na górze strony.',
	'optin-leave-cancel' => 'Jeśli chcesz nadal używać wersji beta, powróć do $1.',
	'optin-success-out' => 'Udało Ci się wyłączyć wersję Beta.
W każdej chwili możesz ją przywrócić poprzez kliknięcie linku „{{int:optin-try}}” znajdującego się na górze, po prawej stronie.',
	'optin-success-feedback' => 'Dziękujemy za przesłaną opinię!',
	'optin-accept-short' => 'Zrób to!',
	'optin-accept-long' => 'Wypróbuj Beta',
	'optin-accept-long-anon' => 'Zaloguj się i wypróbuj Beta',
	'optin-deny-short' => 'Nie, dziękuję',
	'optin-deny-long' => 'Powrót do poprzedniej strony',
	'optin-submit-out' => 'Rezygnuję z Beta',
	'optin-submit-feedback' => 'Prześlij opinię',
	'optin-try' => 'Wypróbuj Beta',
	'optin-leave' => 'Rezygnacja z Beta',
	'optin-feedback' => 'Opinia o Beta',
	'optin-survey-yes' => 'Tak',
	'optin-survey-no' => 'Nie',
	'optin-survey-intro' => 'Dziękujemy za włączenie wersji Beta.

Chcielibyśmy poznać Twoją opinię dotyczącą nowego interfejsu, w związku z tym będziemy wdzięczni za wypełnienie poniższej nieobowiązkowej ankiety, zanim klikniesz „[[#leave|{{int:optin-submit-out}}]]”.',
	'optin-survey-question-likedislike' => 'Co Ci się spodobało w Beta? Co Ci się nie podoba w Beta?',
	'optin-survey-question-whyoptout' => 'Dlaczego rezygnujesz z Beta? (zaznacz wszystkie, które pasują)',
	'optin-survey-answer-whyoptout-hard' => 'To było zbyt trudne, aby z tego korzystać.',
	'optin-survey-answer-whyoptout-didntwork' => 'Nie działał poprawnie.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Działa w sposób nieprzewidywalny.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Nie podoba mi się jej wygląd.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Nie podobają mi się nowe zakładki i układ.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Nie podoba mi się nowy pasek narzędzi.',
	'optin-survey-answer-whyoptout-other' => 'Inny powód',
	'optin-survey-question-explain' => 'Wyjaśnij lub omów swoją powyższą odpowiedź',
	'optin-survey-question-techfail' => 'Czy przydarzyło Ci się zauważyć jakąś awarię techniczną podczas korzystania z wersji beta?',
	'optin-survey-question-techfail-ifyes' => 'Jeśli tak, wyjaśnij proszę',
	'optin-survey-question-usedtoolbar' => 'Czy korzystałeś z nowego rozszerzalnego paska narzędzi, aby edytować stronę?',
	'optin-survey-question-usedtoolbar-ifyes' => 'Jeśli tak, to co Ci się podobało? Co Ci się nie podobało?',
	'optin-survey-question-usedtoolbar-ifno' => 'Jeśli nie, to dlaczego nie? Może jednak kiedyś? Wyjaśnij prosimy.',
	'optin-survey-question-different' => 'Co mamy poprawić, aby przekonać Cię do korzystania z wersji beta?',
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
	'optin-survey-question-changes' => 'Jakie zmiany chciałbyś zobaczyć w wersji beta w przyszłości? Zaznacz wszystkie prawidłowe odpowiedzi.',
	'optin-survey-answer-changes-nav' => 'Poprawienie ogólne nawigacji wewnątrz witryny, aby stała się łatwiejsza w użyciu.',
	'optin-survey-answer-changes-edittools' => 'Zwiększenie liczby i rozszerzenie funkcjonalności narzędzi edycyjnych.',
	'optin-survey-answer-changes-upload' => 'Poprawienie przesyłania treści i plików.',
	'optin-survey-answer-changes-richtext' => 'Opracowanie narzędzi edycyjnych zbliżonych do tych z profesjonalnych edytorów tekstu.',
	'optin-survey-answer-changes-lookfeel' => 'Zaktualizować wygląd i styl witryny.',
	'optin-survey-answer-changes-predictability' => 'Poprawić przewidywalność zmian.',
	'optin-survey-answer-changes-custom' => 'Umożliwienie lepszego dostosowania do indywidualnych potrzeb użytkownika.',
	'optin-survey-answer-changes-other' => 'Inne',
	'optin-improvements' => '== Co zostało poprawione? ==
[[File:UsabilityNavigation.png|left|link=]]
;Poprawiono nawigację
Nowy sposób nawigacji umożliwia łatwiejsze zorientowanie się o aktualnym położeniu oraz wykonywanej czynności, jak również gdzie można się udać i jak tam dotrzeć. Umieszczenie wyszukiwania w prawym górnym rogu strony ułatwiło i przyspieszyło do niego dostęp.
<div style="clear:both;"></div> 

[[File:UsabilityToolbar.png|left|link=]]
;Poprawiono pasek narzędzi edycyjnych
Nowe ikony, czytelniejsze i lepiej dopasowane do narzędzi. Rozwijalny kliknięciem pasek narzędzi zmniejsza bałagan ukrywając ikony rzadko wykorzystywanych narzędzi.
Sekcja pomocy  umożliwia szybki dostęp do pomocy kontekstowej dla często wykorzystywanych znaczników wiki.
<div style="clear:both;"></div>

== Jak można pomóc? ==
Aby dowiedzieć się więcej o projekcie lub wziąć w nim udział odwiedź witrynę [http://usability.wikimedia.org projektu użyteczności wiki]. Odnajdziesz tam informacje o tym jak nas wesprzeć finansowo, badaniach, w projektowaniu, rozwoju oraz wydawaniu wersji. Pytania można zadawać poprzez ogólny system opiniowania, używając [http://usability.wikimedia.org/wiki/Talk:Prototype prototypowej strony dyskusji]. Konkretne problemy należy poruszać na powiązanych stronach dyskusji. Odnalezione błędy należy zgłaszać pozostawiając komentarz na [http://usability.wikimedia.org/wiki/Talk:Releases/Acai stronie dyskusji wersji Acai] lub korzystając z [https://bugzilla.wikimedia.org Bugzilli].

Dziękujemy i zapraszamy!',
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
 * @author Capmo
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
	'optin-survey-question-changes' => 'Que mudanças você gostaria de ver futuramente no Beta? (favor selecionar as opções pertinentes)',
	'optin-survey-answer-changes-nav' => 'Tornar a navegação geral do site mais fácil de usar.',
	'optin-survey-answer-changes-edittools' => 'Criar mais e melhores ferramentas de edição.',
	'optin-survey-answer-changes-upload' => 'Melhorar o carregamento de conteúdo e multimédia.',
	'optin-survey-answer-changes-richtext' => 'Desenvolver ferramentas de edição semelhantes a editores avançados de texto.',
	'optin-survey-answer-changes-lookfeel' => 'Renovar o aspecto geral do site.',
	'optin-survey-answer-changes-predictability' => 'Melhorar a consistência dos resultados.',
	'optin-survey-answer-changes-other' => 'Outros:',
	'optin-improvements' => '== O que foi melhorado? ==
[[File:UsabilityNavigation.png|left|link=]]
;Melhorada a navegação
O novo sistema de navegação torna mais fácil ver onde você está e o que você está a fazer, bem como onde pode ir e como chegar lá. Com a caixa de pesquisa localizada no canto superior direito, a funcionalidade de pesquisa é mais fácil de encontrar e mais rápida de usar.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Melhoramentos na barra de ferramentas de edição
Novos e melhorados ícones tornam mais claro que acção que cada ferramenta realiza. Secções ampliáveis reduzem o empilhamento de botões, continuando a manter ferramentas menos utilizadas a apenas um clique de distância.
A secção de ajuda fornece acesso rápido a uma referência para usos comuns de código wiki.
<div style="clear:both;"></div>',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 */
$messages['ro'] = array(
	'optin-survey-answer-whyoptout-other' => 'Alt motiv:',
	'optin-survey-answer-browser-other' => 'Alt browser:',
);

/** Tarandíne (Tarandíne)
 * @author Joetaras
 */
$messages['roa-tara'] = array(
	'optin-title-optedout' => 'Pruève Beta',
	'optin-title-optedin' => 'Lasse Beta',
	'optin-survey-yes' => 'Sìne',
	'optin-survey-no' => 'None',
	'optin-survey-answer-whyoptout-other' => 'Otre mutive:',
	'optin-survey-answer-changes-other' => 'Otre:',
);

/** Russian (Русский)
 * @author Ferrer
 * @author Lockal
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'optin' => 'Участие в Инициативе юзабилити',
	'optin-desc' => 'Позволяет участникам опробовать Beta, последнее улучшение, сделанное в рамках Инициативы юзабилити',
	'optin-title-optedout' => 'Опробование Beta',
	'optin-title-optedin' => 'Покинуть Beta',
	'optin-title-justoptedin' => 'Добро пожаловать в Beta',
	'optin-title-justoptedout' => 'Благодарим вас за проверку Beta',
	'optin-title-feedback' => 'Отправить отзыв',
	'optin-needlogin' => 'Вам необходимо [$1 представиться], чтобы опробовать Beta.',
	'optin-intro' => 'В рамках Инициативы юзабилити была проведена работа по улучшению удобства использования Википедии.
Вы хотите опробовать Beta?',
	'optin-feedback-intro' => 'Благодарим вас за опробование Beta.
Нам хотелось бы знать, что вы думаете о нашем новом интерфейсе, так что мы были бы признательны, если бы вы приняли участие в приведённом ниже опросе.',
	'optin-feedback-back' => 'Если вы не хотите участвовать в опросе, вы можете вернуться к $1.',
	'optin-success-in' => 'Теперь вы принимаете участие в опробовании Beta.
В можете отключиться от программы в любое время, нажав  на ссылку «{{int:optin-leave}}» в верхней части экрана.',
	'optin-leave-cancel' => 'Если вы хотите завершить использовать Beta, вы можете вернуться к $1.',
	'optin-success-out' => 'Вы успешно покинули Beta.
Вы можете ещё раз попробовать эту систему, нажав ссылку «{{int:optin-try}}» в правом верхнем углу экрана.',
	'optin-success-feedback' => 'Спасибо за отзыв!',
	'optin-accept-short' => 'Поехали!',
	'optin-accept-long' => 'Опробовать Beta',
	'optin-accept-long-anon' => 'Представиться системе и попробовать Beta',
	'optin-deny-short' => 'Нет, спасибо',
	'optin-deny-long' => 'Вернуться на предыдущую страницу',
	'optin-submit-out' => 'Покинуть Beta',
	'optin-submit-feedback' => 'Отправить отзыв',
	'optin-try' => 'Опробовать Beta',
	'optin-leave' => 'Покинуть Beta',
	'optin-feedback' => 'Отправить отзыв',
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
	'optin-survey-question-changes' => 'Какие изменения вы хотите увидеть в Beta в будущем (выберите все подходящие пункты)?',
	'optin-survey-answer-changes-nav' => 'Сделать навигацию по всему сайту более простой в использовании.',
	'optin-survey-answer-changes-edittools' => 'Расширить и усовершенствовать инструменты редактирования.',
	'optin-survey-answer-changes-upload' => 'Усовершенствовать загрузку содержания и медиа.',
	'optin-survey-answer-changes-richtext' => 'Разработать инструменты редактирования, аналогичные текстовым редакторам.',
	'optin-survey-answer-changes-lookfeel' => 'Обновить «внешний вид» сайта.',
	'optin-survey-answer-changes-predictability' => 'Улучшить предсказуемость работы.',
	'optin-survey-answer-changes-custom' => 'Позволить производить больше индивидуальных настроек.',
	'optin-survey-answer-changes-other' => 'Иное:',
	'optin-improvements' => '== Что было улучшено? ==
[[File:UsabilityNavigation.png|left]]
; Усовершенствование навигации 
Новая навигационная система позволяет проще понять где вы находитесь и что делаете, а также куда вы можете перейти и как туда попасть. С полем поиска, расположенным в верхнем правом углу, искать становится проще и быстрее.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left]]
; Улучшение панели редактирования
Новые и улучшенные значки более точно показывают какие инструменты какие действия выполняют. Раскрываемые разделы уменьшают беспорядок, сохраняя при этом редко используемые инструменты на расстоянии щелчка мыши. Справочный раздел обеспечивает быстрый доступ к примерам по наиболее используемым типам вики-разметки.
<div style="clear:both;"></div>
== Как подключиться ==

Чтобы узнать больше о проекте, принять в нём участие, пожалуйста, посетите [http://usability.wikimedia.org вики-сайт Инициативы юзабилити]. Там вы найдёте информацию о наших грантах, научных исследованиях, проектировании, выпусках. Чтобы задать вопрос или оставить общий отзыв, воспользуйтесь нашей [http://usability.wikimedia.org/wiki/Talk:Prototype страницей обсуждения прототипа]. Для решения конкретных вопросов, пожалуйста, используйте соответствующие страницы обсуждения. Если вы нашли ошибку, пожалуйста, оставьте свои замечания на [http://usability.wikimedia.org/wiki/Talk:Releases/Acai странице обсуждения выпуска Acai] или с помощью [https://bugzilla.wikimedia.org Bugzilla].

Спасибо, мы ждём ваших отзывов!',
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'optin-title-optedout' => 'Beta-ны тургутуу',
	'optin-title-optedin' => 'Beta-ттан тахсыы',
	'optin-title-justoptedin' => 'Beta-ҕа нөрүөн нөргүй!',
	'optin-title-justoptedout' => 'Beta-ны тургутан көрбүккэр махтал',
	'optin-title-feedback' => 'Сыанабылы ыытарга',
	'optin-needlogin' => 'Beta тургутарга [$1 ааккын билиһиннэриэххин] наада.',
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
	'optin-try' => 'Beta-ны тургутуу',
	'optin-leave' => 'Beta-ттан тахсыы',
	'optin-feedback' => 'Сыанабылы ыытарга',
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
	'optin-improvements' => '== Туох тупсарыллыбытый? ==
[[File:UsabilityNavigation.png|left|link=]]
; Навигацията тупсуута  
Саҥа навигация систиэмэтэ ханна билигин сылдьаргын, тугу гынаргын, уонна ханна хайдах барыаххын сөбүн кэбэҕэстик өйдүүргэ көмөлөһөр. Үөһээ уҥа муннукка баар көрдүүр түннүгү туттарга ордук табыгастаах.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
; Эрэдээксийэлиир панел тупсуута
Саҥа тупсарыллыбыт значоктар ханнык үнүстүрүмүөн ханнык үлэни оҥорорун ордук үчүгэйдик көрдөрөллөр. Аһыллар салаалар булкууру аҕыйаталлар, ол да буоллар сэдэхтик туттуллар үнүстүрүмүөннэри чугас илдьэ сылдьарга көмөлөһөллөр. Көмө салаата биики-бэлиэлэри сөпкө туһанар туһунан холобурдары түргэнник көрөргө табыгастаах. 
<div style="clear:both;"></div>

== Хайдах кыттабыный ==

Бу бырайыак туһунан сиһилии билиэххитин баҕарар буоллаххытына бу [http://usability.wikimedia.org биики-саайка] киириҥ. Онно биһиги грааннарбыт, чинчийиилэрбит, бырайыактарбыт уонна бырагырааммаларбыт барылларын туһунан информацияны булуоххут. Тугу эрэ ыйытаары гыннаххытына эбэтэр бородууксуйабыт туһунан тугу эрэ этиэххитин баҕардаххытына [http://usability.wikimedia.org/wiki/Talk:Prototype ырытыы сирэйигэр] киириҥ. Чопчу боппуруостары аналлаах ырытар сирэйдэргэ быһаарсыҥ. Туох эмит алҕаһы булбут буоллаххытына бука диэн   [http://usability.wikimedia.org/wiki/Talk:Releases/Acai Acai тахсыытын ырытыытыгар суруйуҥ] эбэтэр [https://bugzilla.wikimedia.org Bugzilla] көмөтүнэн биллэриҥ.

Махтанабыт, суруккутун кэтэһэбит!',
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
	'optin-try' => 'Skúsiť Beta',
	'optin-leave' => 'Opustiť Beta',
	'optin-feedback' => 'Vaše komentáre',
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
	'optin-improvements' => '== Čo bolo zlepšené? ==
[[File:UsabilityNavigation.png|left|link=]]
;Rozšírená navigácia
Vďaka novému navigačnému systému je jednoduchšie rozlíšiť, kde sa nachádzate a tiež kam a ako sa môžete dostať. Vďaka vyhľadávaciemu poľu v pravom hornom rohu je prístup jednoduchší a vyhľadávanie rýchlejšie.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Vylepšenia panelu úprav
Nové a vylepšené ikony vyjasňujú čo ktorý nástroj robí.
Rozbaliteľné časti skrývajú zriedka používané nástroje, pričom sú stále len o jedno kliknutie ďalej.
Časť Pomocník poskytuje rýchly prístup do referenčnej dokumentácie bežného ppoužitia wiki syntaxe.
<div style="clear:both;"></div>

== Ako sa zapojiť ==
Ak sa chcete dozvedieť viac o projekte alebo sa zapojiť, navštívte prosím [http://usability.wikimedia.org wiki Iniciatívy použiteľnosti]. Nájdete na nej informácie o našom grante, výskume, dizajne a vývoji a vydaniach. Ak sa chcete pýtať otázky alebo napísať všeobecné komentáre, prosím, použite našu [http://usability.wikimedia.org/wiki/Talk:Prototype diskusnú stránku prototypu]. Ak sa týkajú konkrétnych problémov, komentujte na [http://usability.wikimedia.org/wiki/Talk:Releases/Acai diskusnej stránke vydania Acai] alebo pomocou [https://bugzilla.wikimedia.org Bugzilla].

Ďakujeme a tešíme sa na vašu odozvu!',
);

/** Serbian Cyrillic ekavian (ћирилица)
 * @author Михајло Анђелковић
 */
$messages['sr-ec'] = array(
	'optin-survey-yes' => 'Да',
	'optin-survey-no' => 'Не',
	'optin-survey-answer-whyoptout-hard' => 'Био је претежак за коришћење.',
	'optin-survey-answer-whyoptout-didntwork' => 'Није радио како треба.',
	'optin-survey-answer-whyoptout-notpredictable' => 'Није радио предвидиво.',
	'optin-survey-answer-whyoptout-didntlike-look' => 'Није ми се свидело како изгледа.',
	'optin-survey-answer-whyoptout-didntlike-layout' => 'Нису ми се свидели нови табови и изглед.',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => 'Није ми се свидео нови тулбар.',
	'optin-survey-answer-whyoptout-other' => 'Други разлог:',
	'optin-survey-question-browser' => 'Који браузер користите?',
	'optin-survey-answer-browser-other' => 'Други браузер:',
	'optin-survey-question-os' => 'Који оперативни систем користите?',
	'optin-survey-answer-os-other' => 'Други оперативни систем:',
	'optin-survey-question-res' => 'Која је резолуција Вашег екрана?',
	'optin-survey-answer-changes-other' => 'Друго:',
);

/** Telugu (తెలుగు)
 * @author Kiranmayee
 * @author Veeven
 */
$messages['te'] = array(
	'optin-title-optedout' => 'బీటాని ప్రయత్నించండి',
	'optin-accept-long' => 'బీటాని ప్రయత్నించండి',
	'optin-survey-yes' => 'అవును',
	'optin-survey-no' => 'కాదు',
	'optin-survey-answer-whyoptout-hard' => 'వాడడానికి చాలా కష్టంగా ఉంది.',
	'optin-survey-answer-whyoptout-other' => 'ఇతర కారణం:',
	'optin-survey-question-browser' => 'మీరు ఏ విహారిణిని వాడుతున్నారు?',
	'optin-survey-answer-browser-other' => 'ఇతర విహారిణి:',
	'optin-survey-question-os' => 'మీరు వాడుతున్న నిర్వాహక వ్యవస్థ ఏది?',
	'optin-survey-answer-os-other' => 'ఇతర నిర్వాహక వ్యవస్థలు:',
	'optin-survey-answer-changes-other' => 'ఇతర:',
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
	'optin-survey-answer-changes-other' => 'Diğer:',
	'optin-improvements' => '== Neler geliştirildi? ==
[[File:UsabilityNavigation.png|left|link=]]
;Geliştirilmiş dolaşım
Yeni dolaşım sistemi, nerede olduğunuzu ve ne yaptığınızı görmenizi, ayrıca nereye gidebileceğinizi ve oraya nasıl ulaşacağınızı kolaylaştırır. Sağ üst köşede yer alan yeni arama kutusu ile bulmak daha kolay ve kullanmak daha hızlı.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Değiştirme araç çubuğu geliştirmeleri
Yeni-ve-geliştirilmiş simgeler her aracın ne eylemi yaptığını daha netleştirir. Genişletilebilir bölümler dağınıklığı azaltırken az kullanılan araçları sadece bir tık ötesinde saklar.
Yardım bölümü, viki-biçimlendirmesinin genel kullanımı için bir referansa kolay erişim sağlar.
<div style="clear:both;"></div>',
);

/** Ukrainian (Українська)
 * @author AS
 * @author Ickis
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
	'optin-survey-question-whyoptout' => 'Чому ви покидаєте Бету? (Будь ласка, виберіть підходящі значення)',
	'optin-survey-answer-whyoptout-hard' => 'Він дуже складний у використанні.',
	'optin-survey-answer-whyoptout-didntwork' => 'Він не працює належним чином.',
	'optin-survey-answer-whyoptout-other' => 'Інша причина:',
	'optin-survey-question-feedback' => 'Будь ласка, висловіть своє судження:',
	'optin-survey-question-browser' => 'Яким оглядачем ви користуєтесь?',
	'optin-survey-answer-browser-other' => 'Інший:',
	'optin-survey-question-os' => 'Якою операційною системою ви користуєтесь?',
	'optin-survey-answer-os-other' => 'Інша операційна система:',
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
	'optin-try' => 'Thử bản Beta',
	'optin-leave' => 'Rời Beta',
	'optin-feedback' => 'Phản hồi Beta',
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
	'optin-improvements' => '== Những cải tiến ==
[[File:UsabilityNavigation.png|left|link=]]
;Duyệt trang tốt hơn
Hệ thống duyệt trang giúp bạn dễ nhận thấy mình đang ở đâu và làm gì hơn, cũng như nơi bạn có thể đến và cách làm điều đó. Với hộp tìm kiếm đặt ở góc trên bên phải, tính năng tìm kiếm sẽ dễ tìm thấy và dễ sử dụng hơn.
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;Cải tiến thanh công cụ sửa đổi
Các biểu tượng mới và được cải tiến để làm rõ hơn các chức năng của từng nút công cụ. Nhiều phần có thể mở rộng giúp giảm bớt sự lộn xộn nhưng vẫn giữ lại được những công cụ thường dùng chỉ bằng một cú nhấn chuột.
Đề mục trợ giúp giúp truy cập nhanh vào hướng dẫn sử dụng mã wiki.
<div style="clear:both;"></div>
== Cách tham gia ==
Để tìm hiểu thêm về dự án hoặc tham gia vào dự án, xin mời thăm [http://usability.wikimedia.org wiki của Sáng kiến Khả dụng].  Tại đó bạn sẽ tìm thấy những thông tin về số tiền được cấp, nghiên cứu, thiết kế + phát triển, và các bản phát hành. Để đặt câu hỏi hoặc để lại hồi đáp, xin hãy sử dụng [http://usability.wikimedia.org/wiki/Talk:Prototype trang thảo luận prototype của chúng tôi].  Để bàn về các chủ đề cụ thể, hãy sử dụng các trang thảo luận đi kèm. Nếu bạn nhận thấy có lỗi phần mềm, xin để lại lời nhắn tại  [http://usability.wikimedia.org/wiki/Talk:Releases/Acai trang thảo luận bản phát hành Acai] hoặc sử dụng [https://bugzilla.wikimedia.org Bugzilla].

Cảm ơn và mong hồi âm từ bạn!',
);

/** Yue (粵語)
 * @author Shinjiman
 */
$messages['yue'] = array(
	'optin' => '可用性倡議選入',
	'optin-desc' => '容許用戶選擇性去加入可用性倡議嘅可用性加強',
	'optin-title-optedout' => '試吓測試版',
	'optin-title-optedin' => '離開測試版',
	'optin-title-justoptedin' => '歡迎來到測試版',
	'optin-title-justoptedout' => '多謝你去試用測試版',
	'optin-title-feedback' => '測試版回饋',
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
	'optin-success-feedback' => '多謝你嘅回饋！',
	'optin-accept-short' => '而家就去做！',
	'optin-accept-long' => '試吓我哋嘅測試版',
	'optin-accept-long-anon' => '登入試吓測試版',
	'optin-deny-short' => '唔使喇，多謝',
	'optin-deny-long' => '返去上一版',
	'optin-submit-out' => '離開測試版',
	'optin-submit-feedback' => '畀意見',
	'optin-try' => '試吓測試版',
	'optin-leave' => '離開測試版',
	'optin-feedback' => '測試版回饋',
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
	'optin-improvements' => '== 有啲乜改善咗？ ==
[[File:UsabilityNavigation.png|left|link=]]
;導航加強
個新嘅導航系統會更加容易噉令你知道你響邊同做緊啲乜嘢，你可以去邊同埋點樣去嗰度。搵嘢盒放咗響右上角，個搵嘢功能更加容易搵到，又用得快啲。
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;編輯欄改進
新同改善嘅圖示更加清楚睇到每一個工具去做啲乜嘢。可以擴展嘅小節減少混亂，撳一個掣就可以收起啲唔常用嘅工具。
個幫手小節提供常用wiki語法嘅快捷途徑。
<div style="clear:both;"></div>
== 點樣加入 ==
要知更多有關專案或者加入，請睇吓[http://usability.wikimedia.org Usability Initiative wiki]。
嗰度你就會搵到我哋嘅准許、研究、設計開發，同埋發佈。
去問問題或者畀一般回饋，請用我哋嘅[http://usability.wikimedia.org/wiki/Talk:Prototype 模型討論頁]。
去解決特定嘅標題，請用有關嘅討論版。
如果你搵到臭蟲，請響[http://usability.wikimedia.org/wiki/Talk:Releases/Acai Acai發佈討論版]留言或者去用[https://bugzilla.wikimedia.org Bugzilla]。

多謝，我哋會等待你嘅意見！',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Liangent
 * @author PhiLiP
 * @author Shinjiman
 */
$messages['zh-hans'] = array(
	'optin' => '可用性倡议加入',
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
	'optin-feedback-back' => '如果您不愿意填写调查表，您可以返回到$1。',
	'optin-success-in' => '您现在正在试用测试版。
您随时都可点击屏幕顶部的“{{int:optin-leave}}”返回。',
	'optin-leave-cancel' => '如果你想继续使用Beta，你可以返回到$1。',
	'optin-success-out' => '您已成功退出测试版。
您随时都可点击屏幕右上角的“{{int:optin-try}}”再次试用。',
	'optin-success-feedback' => '多谢你的反馈！',
	'optin-accept-short' => '现在就去做！',
	'optin-accept-long' => '试试我们的测试版',
	'optin-accept-long-anon' => '登入试用测试版',
	'optin-deny-short' => '不用了，多谢',
	'optin-deny-long' => '回到上一页',
	'optin-submit-out' => '离开测试版',
	'optin-submit-feedback' => '给意见',
	'optin-try' => '试试测试版',
	'optin-leave' => '离开测试版',
	'optin-feedback' => '测试版反馈',
	'optin-survey-yes' => '是',
	'optin-survey-no' => '否',
	'optin-survey-intro' => '感谢您试用测试版。
您可以随时点击上面的"{{int:optin-leave}}"去退出。

我们希望了解到您对我们新界面的意见，希望您在单击“[[#leave|{{int:optin-submit-out}}]]”前不吝笔墨填写下方的调查表。',
	'optin-survey-question-likedislike' => '您对测试版有什么喜欢？有什么不喜欢？',
	'optin-survey-question-whyoptout' => '为什么您会退出测试版？（选全部应用到的）',
	'optin-survey-answer-whyoptout-hard' => '难以使用。',
	'optin-survey-answer-whyoptout-didntwork' => '运行不正常。',
	'optin-survey-answer-whyoptout-notpredictable' => '不能做到预期的事。',
	'optin-survey-answer-whyoptout-didntlike-look' => '我不喜欢它的外观。',
	'optin-survey-answer-whyoptout-didntlike-layout' => '我不喜欢它的新标签以及排版。',
	'optin-survey-answer-whyoptout-didntlike-toolbar' => '我不喜欢它的新工具列。',
	'optin-survey-answer-whyoptout-other' => '其它原因：',
	'optin-survey-question-explain' => '请解释或详述您以上之答案：',
	'optin-survey-question-techfail' => '您在使用测试版时，有没有曾经遇过任何的技术问题？',
	'optin-survey-question-techfail-ifyes' => '如果有的话，请详述或者解释：',
	'optin-survey-question-usedtoolbar' => '您有没有用过新可扩展的工具列去编辑一个页面？',
	'optin-survey-question-usedtoolbar-ifyes' => '如果有的话，您喜欢什么？您又不喜欢什么？',
	'optin-survey-question-usedtoolbar-ifno' => '如果没有的话，为什么不去用？会否在将来去用？请解释一下。',
	'optin-survey-question-different' => '您在用测试版时，我们做了什么事改变去令到你持续使用它？',
	'optin-survey-question-feedback' => '如果您对我们还有其他意见，也请让我们知晓——所有反馈都很有用处！：',
	'optin-survey-question-browser' => '您使用的浏览器是？',
	'optin-survey-answer-browser-other' => '其它浏览器：',
	'optin-survey-question-os' => '您使用的操作系统是？',
	'optin-survey-answer-os-other' => '其他操作系统：',
	'optin-survey-question-res' => '您的屏幕分辨率有多大？',
	'optin-survey-question-changes' => '您想在未来中，期望对测试版有什么改变（选全部应用到的）？',
	'optin-survey-answer-changes-nav' => '令网站的整体导航更加容易使用。',
	'optin-survey-answer-changes-edittools' => '增加和加强编辑工具。',
	'optin-survey-answer-changes-upload' => '改善内容和媒体上载。',
	'optin-survey-answer-changes-richtext' => '开发与广文字编辑器相似的编辑工具。',
	'optin-survey-answer-changes-lookfeel' => '更新网站的『样式与感受』。',
	'optin-survey-answer-changes-predictability' => '改善效率预测。',
	'optin-survey-answer-changes-custom' => '容许更加有弹性的用户自定。',
	'optin-survey-answer-changes-other' => '其它：',
	'optin-improvements' => '== 有什么改善什？ ==
[[File:UsabilityNavigation.png|left|link=]]
;导航加强
新的导航系统会更加容易地令您知道您在哪里以及正在做什么，您可以去哪里以及怎样哪里。搜寻方块放在右上角，令它的功能更加容易找到，使用得更快。
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;编辑栏改进
新和改善的图标更加清楚看见每一个工具去做什么。可以扩展的小节减少混乱，点击一个按钮就可以隐藏一些不常用的工具。
而帮助小节提供常用wiki语法的快捷途径。
<div style="clear:both;"></div>
== 怎样加入 ==
要知更多有关专案或者加入，请参阅[http://usability.wikimedia.org Usability Initiative wiki]。
在哪里您就会找到我们的准许、研究、设计开发，以及发布。
去问问题或者给予一般反馈，请用我们的[http://usability.wikimedia.org/wiki/Talk:Prototype 模型讨论页面]。
去解决特定的主题，请用有关的讨论页面。
如果您找到臭虫，请在[http://usability.wikimedia.org/wiki/Talk:Releases/Acai Acai发布讨论版]留言或者去用[https://bugzilla.wikimedia.org Bugzilla]。

多谢，我们会等待您的意见！',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Shinjiman
 */
$messages['zh-hant'] = array(
	'optin' => '可用性倡議加入',
	'optin-desc' => '容許用戶選擇性去加入可用性倡議的可用性增強',
	'optin-title-optedout' => '試用測試版',
	'optin-title-optedin' => '離開測試版',
	'optin-title-justoptedin' => '歡迎來到測試版',
	'optin-title-justoptedout' => '多謝你去試用測試版',
	'optin-title-feedback' => '測試版回饋',
	'optin-needlogin' => '你需要[$1 登入]去試用測試版。',
	'optin-intro' => '維基百科可用性小組開發了一個新皮膚和一個新的編輯工具去加強維基百科的可用性。這些增強還未對全部用戶開放，但是您可以點擊下面的"{{int:optin-submit-in}}"加入。',
	'optin-feedback-intro' => '多謝去試用這個測試版。
我們想知道你對我們的新界面有何看法，如果你完成下面可選的調查，我們會好樂意接受。',
	'optin-feedback-back' => '如果你不想完成下面嘅調查，你可以回到$1。',
	'optin-success-in' => '您已經成功地加入到可用性小組的可用性增強。
您可以隨時點擊上面的"{{int:ooptin-leave}}"去退出。',
	'optin-success-out' => '您已經成功地退出自可用性小組的可用性增強。
您可以隨時點擊上面的"{{int:optin-try}}"去加入。',
	'optin-success-feedback' => '多謝你的回饋！',
	'optin-accept-short' => '現在就去做！',
	'optin-accept-long' => '試試我們的測試版',
	'optin-accept-long-anon' => '登入試用測試版',
	'optin-deny-short' => '不用了，多謝',
	'optin-deny-long' => '回到上一頁',
	'optin-submit-out' => '離開測試版',
	'optin-submit-feedback' => '給意見',
	'optin-try' => '試試測試版',
	'optin-leave' => '離開測試版',
	'optin-feedback' => '測試版回饋',
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
	'optin-improvements' => '== 有什麼改善什？ ==
[[File:UsabilityNavigation.png|left|link=]]
;導航加強
新的導航系統會更加容易地令您知道您在哪裏以及正在做什麼，您可以去哪裏以及怎樣哪裏。搜尋方塊放在右上角，令它的功能更加容易找到，使用得更快。
<div style="clear:both;"></div>

[[File:UsabilityToolbar.png|left|link=]]
;編輯欄改進
新和改善的圖示更加清楚看見每一個工具去做什麼。可以擴展的小節減少混亂，點擊一個按鈕就可以隱藏一些不常用的工具。
而幫助小節提供常用wiki語法的快捷途徑。
<div style="clear:both;"></div>
== 怎樣加入 ==
要知更多有關專案或者加入，請參閱[http://usability.wikimedia.org Usability Initiative wiki]。
在那裏您就會找到我們的准許、研究、設計開發，以及發佈。
去問問題或者給予一般回饋，請用我們的[http://usability.wikimedia.org/wiki/Talk:Prototype 模型討論頁面]。
去解決特定的主題，請用有關的討論頁面。
如果您找到臭蟲，請在[http://usability.wikimedia.org/wiki/Talk:Releases/Acai Acai發佈討論版]留言或者去用[https://bugzilla.wikimedia.org Bugzilla]。

多謝，我們會等待您的意見！',
);

