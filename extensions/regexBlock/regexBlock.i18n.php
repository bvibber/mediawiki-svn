<?php
/**
 * Internationalisation file for extension regexBlock.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'regexblock'                      => 'Regex block',
	'regexblock-desc'                 => 'Extension used for blocking users names and IP addresses with regular expressions. Contains both the blocking mechanism and a [[Special:Regexblock|special page]] to add/manage blocks',
	'regexblock-special-desc'         => 'alternate user block (by given name, using regular expressions)',
	'regexblock-stat-desc'            => 'Displays [[Special:Regexblockstats|block statistics]] for the regexblock extension',
	'regexblock-page-title'           => 'Regular expression name block',
	'regexblockstats'                 => 'Regex block statistics',
	'regexblock-reason-ip'            => 'This IP address is prevented from editing due to vandalism or other disruption by you or by someone who shares your IP address.
If you believe this is in error, please $1' ,
	'regexblock-reason-name'          => 'This username is prevented from editing due to vandalism or other disruption.
If you believe this is in error, please $1',
	'regexblock-reason-regex'         => 'This username is prevented from editing due to vandalism or other disruption by a user with a similar name.
Please create an alternate user name or $1 about the problem',
	'regexblock-help'                 => 'Use the form below to block write access from a specific IP address or username.
This should be done only only to prevent vandalism, and in accordance with policy.
\'\'This page will allow you to block even non-existing users, and will also block users with names similar to given, i.e. "Test" will be blocked along with "Test 2" etc.
You can also block full IP addresses, meaning that no one logging from them will be able to edit pages.
Note: partial IP addresses will be treated by usernames in determining blocking.
If no reason is specified, a default generic reason will be used.\'\'',
	'regexblock-page-title-1'         => 'Block address using regular expressions',
	'regexblock-unblock-success'      => 'Unblock succeeded',
	'regexblock-unblock-log'          => 'User name or IP address \'\'\'$1\'\'\' has been unblocked.',
	'regexblock-unblock-error'        => 'Error unblocking $1.
Probably there is no such user.',
	'regexblock-form-username'        => 'IP address or username:',
	'regexblock-form-reason'          => 'Reason:',
	'regexblock-form-expiry'          => 'Expiry:',
	'regexblock-form-match'           => 'Exact match',
	'regexblock-form-account-block'   => 'Block creation of new accounts',
	'regexblock-form-submit'          => 'Block this user',
	'regexblock-block-log'            => 'User name or IP address \'\'\'$1\'\'\' has been blocked.',
	'regexblock-block-success'        => 'Block succedeed',
	'regexblock-form-submit-empty'    => 'Give a user name or an IP address to block.',
	'regexblock-form-submit-regex'    => 'Invalid regular expression.',
	'regexblock-form-submit-expiry'   => 'Please specify an expiration period.',
	'regexblock-already-blocked'      => '$1 is already blocked.',
	'regexblock-stats-title'          => 'Regex block statistics',
	'regexblock-stats-username'       => 'For $1',
	'regexblock-stats-times'          => 'was blocked on',
	'regexblock-stats-logging'        => 'logging from address',
	'regexblock-currently-blocked'    => 'Currently blocked addresses:',
	'regexblock-view-blocked'         => 'View blocked by:',
	'regexblock-view-all'             => 'All',
	'regexblock-view-go'              => 'Go',
	'regexblock-view-match'           => '(exact match)',
	'regexblock-view-regex'           => '(regex match)',
	'regexblock-view-account'         => '(account creation block)',
	'regexblock-view-reason'          => 'reason: $1',
	'regexblock-view-reason-default'  => 'generic reason',
	'regexblock-view-block-infinite'  => 'permanent block',
	'regexblock-view-block-temporary' => 'expires on ',
	'regexblock-view-block-expired'   => 'EXPIRED on ',
	'regexblock-view-block-by'        => 'blocked by ',
	'regexblock-view-block-unblock'   => 'unblock',
	'regexblock-view-stats'           => '(stats)',
	'regexblock-view-empty'           => 'The list of blocked names and addresses is empty.',
	'regexblock-view-time'            => 'on $1',
);

/** Message documentation (Message documentation)
 * @author Jon Harald Søby
 * @author SPQRobin
 */
$messages['qqq'] = array(
	'regexblock-reason-ip' => 'Parameter $1 is <tt>$wgContactLink</tt>, which is by default "<tt><nowiki>[[Special:Contact|contact Wikia]]</nowiki></tt>".',
	'regexblock-reason-name' => 'Parameter $1 is <tt>$wgContactLink</tt>, which is by default "<tt><nowiki>[[Special:Contact|contact Wikia]]</nowiki></tt>".',
	'regexblock-reason-regex' => 'Parameter $1 is <tt>$wgContactLink</tt>, which is by default "<tt><nowiki>[[Special:Contact|contact Wikia]]</nowiki></tt>".',
	'regexblock-form-reason' => '{{Identical|Reason}}',
	'regexblock-form-expiry' => '{{Identical|Expiry}}',
	'regexblock-form-match' => '{{Identical|Exact match}}',
	'regexblock-already-blocked' => '{{Identical|$1 is already blocked}}',
	'regexblock-stats-username' => '{{Identical|For $1}}',
	'regexblock-view-all' => '{{Identical|All}}',
	'regexblock-view-go' => '{{Identical|Go}}',
	'regexblock-view-match' => '{{Identical|Exact match}}',
	'regexblock-view-block-temporary' => '{{Identical|Expires on}}',
);

/** Eastern Mari (Олык Марий)
 * @author Сай
 */
$messages['mhr'] = array(
	'regexblock-stats-username' => '$1 лан',
);

/** Niuean (ko e vagahau Niuē)
 * @author Jose77
 */
$messages['niu'] = array(
	'regexblock-form-reason' => 'Kakano:',
	'regexblock-view-go' => 'Fano',
);

/** Afrikaans (Afrikaans)
 * @author Arnobarnard
 * @author Naudefj
 * @author SPQRobin
 */
$messages['af'] = array(
	'regexblock-form-reason' => 'Rede:',
	'regexblock-already-blocked' => '$1 is reeds geblok.',
	'regexblock-stats-username' => 'Vir $1',
	'regexblock-view-all' => 'Alles',
	'regexblock-view-go' => 'Gaan',
	'regexblock-view-reason' => 'rede: $1',
	'regexblock-view-time' => 'op $1',
);

/** Aragonese (Aragonés)
 * @author Juanpabl
 */
$messages['an'] = array(
	'regexblock-form-reason' => 'Razón:',
	'regexblock-already-blocked' => '$1 ya yera bloqueyato.',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'regexblock' => 'تعبير منتظم لمنع',
	'regexblock-desc' => 'امتداد يستخدم لمنع أسماء المستخدمين وعناوين الأيبي باستخدام تعبيرات منتظمة. يحتوي على ميكانيكية المنع و [[Special:Regexblock|صفحة خاصة]] لإضافة/التحكم بعمليات المنع',
	'regexblock-special-desc' => 'منع مستخدم بديل (بواسطة الاسم المعطى، باستخدام تعبيرات منتظمة)',
	'regexblock-stat-desc' => 'يعرض [[Special:Regexblockstats|إحصاءات المنع]] لامتداد المنع بالتعبيرات المنتظمة',
	'regexblock-page-title' => 'منع الاسم بواسطة تعبير منتظم',
	'regexblockstats' => 'إحصاءات تعبيرات المنع المنتظمة',
	'regexblock-reason-ip' => 'عنوان الأيبي هذا ممنوع نتيجة للتخريب أو إساءة أخرى بواسطتك أو بواسطة شخص يشارك في عنوان الأيبي الخاص بك.
لو كنت تعتقد أن هذا خطأ، من فضلك $1',
	'regexblock-reason-name' => 'اسم المستخدم هذا ممنوع من التحرير نتيجة للتخريب أو إساءة أخرى.
لو كنت تعتقد أن هذا خطأ، من فضلك $1',
	'regexblock-reason-regex' => 'اسم المستخدم هذا ممنوع من التحرير نتيجة للتخريب أو إساءة أخرى بواسطة مستخدم باسم مشابه.
من فضلك أنشيء اسم مستخدم بديل أو $1 حول المشكلة',
	'regexblock-help' => 'استخدم الاستمارة بالأسفل لمنع التحرير من عنوان أيبي أو اسم مستخدم محدد.
هذا ينبغي أن يتم فقط لمنع التخريب، وبالتوافق مع السياسة.
\'\'هذه الصفحة ستسمح لك بمنع حتى المستخدمين غير الموجودين، وستمنع أيضا المستخدمين بأسماء مشابهة للمعطاة،أي أن "Test" سيتم منعها بالإضافة إلى "Test 2"إلى آخره.
يمكنك أيضا منع عناوين أيبي كاملة، مما يعني أنه لا أحد مسجلا للدخول منها سيمكنه تعديل الصفحات.
ملاحظة: عناوين الأيبي الجزئية سيتم معاملتها بواسطة أسماء مستخدمين في تحديد المنع.
لو لم يتم تحديد سبب، سيتم استخدام سبب افتراضي تلقائي.\'\'',
	'regexblock-page-title-1' => 'منع عنوان باستخدام تعبيرات منتظمة',
	'regexblock-unblock-success' => 'رفع المنع نجح',
	'regexblock-unblock-log' => "اسم المستخدم أو عنوان الأيبي '''$1''' تم رفع المنع عنه.",
	'regexblock-unblock-error' => 'خطأ أثناء رفع المنع عن $1.
على الأرجح لا يوجد مستخدم بهذا الاسم.',
	'regexblock-form-username' => 'عنوان الأيبي أو اسم المستخدم:',
	'regexblock-form-reason' => 'السبب:',
	'regexblock-form-expiry' => 'الانتهاء:',
	'regexblock-form-match' => 'تطابق تام',
	'regexblock-form-account-block' => 'منع إنشاء الحسابات الجديدة',
	'regexblock-form-submit' => 'منع هذا المستخدم',
	'regexblock-block-log' => "اسم المستخدم أو عنوان الأيبي '''$1''' تم منعه.",
	'regexblock-block-success' => 'المنع نجح',
	'regexblock-form-submit-empty' => 'أعط اسم مستخدم أو عنوان أيبي للمنع.',
	'regexblock-form-submit-regex' => 'تعبير منتظم غير صحيح.',
	'regexblock-form-submit-expiry' => 'من فضلك حدد تاريخ انتهاء.',
	'regexblock-already-blocked' => '$1 ممنوع بالفعل.',
	'regexblock-stats-title' => 'إحصاءات تعبيرات المنع المنتظمة',
	'regexblock-stats-username' => 'ل$1',
	'regexblock-stats-times' => 'تم منعه في',
	'regexblock-stats-logging' => 'دخول من العنوان',
	'regexblock-currently-blocked' => 'العناوين الممنوعة حاليا:',
	'regexblock-view-blocked' => 'عرض الممنوع بواسطة:',
	'regexblock-view-all' => 'الكل',
	'regexblock-view-go' => 'اذهب',
	'regexblock-view-match' => '(تطابق تام)',
	'regexblock-view-regex' => '(تطابق تعبير منتظم)',
	'regexblock-view-account' => '(منع إنشاء حساب)',
	'regexblock-view-reason' => 'السبب: $1',
	'regexblock-view-reason-default' => 'سبب تلقائي',
	'regexblock-view-block-infinite' => 'منع دائم',
	'regexblock-view-block-temporary' => 'ينتهي في',
	'regexblock-view-block-expired' => 'انتهى في',
	'regexblock-view-block-by' => 'ممنوع بواسطة',
	'regexblock-view-block-unblock' => 'رفع المنع',
	'regexblock-view-stats' => '(إحصاءات)',
	'regexblock-view-empty' => 'قائمة الأسماء والعناوين الممنوعة فارغة.',
	'regexblock-view-time' => 'في $1',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 */
$messages['be-tarask'] = array(
	'regexblock-form-reason' => 'Прычына:',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'regexblock-reason-ip' => 'На това потребителско име не е позволено да редактира заради вандализъм или други разрушаващи действия от потребител, който споделя същия IP-адрес. Ако смятате, че това е грешка, $1',
	'regexblock-reason-name' => 'На това потребителско име не е позволено да редактира заради вандализъм или други разрушаващи действия. Ако смятате, че това е грешка, $1',
	'regexblock-reason-regex' => 'На това потребителско име не е позволено да редактира заради вандализъм или други разрушаващи действия от потребител със сходно име. Можете да създадете друга потребителска сметка или да $1 за проблема',
	'regexblock-page-title-1' => 'Блокиране на адреси чрез регулярни изрази',
	'regexblock-unblock-success' => 'Отблокирането беше успешно',
	'regexblock-unblock-log' => "Потребителят или IP адресът '''$1''' беше отблокиран.",
	'regexblock-unblock-error' => 'Грешка при отблокиране на $1.
Вероятно не съществува такъв потребител.',
	'regexblock-form-username' => 'IP адрес или потребителско име:',
	'regexblock-form-reason' => 'Причина:',
	'regexblock-form-expiry' => 'Срок на изтичане:',
	'regexblock-form-match' => 'Пълно съвпадение',
	'regexblock-form-account-block' => 'Блокиране създаването на нови сметки',
	'regexblock-form-submit' => 'Блокиране на този потребител',
	'regexblock-block-log' => "Потребител или IP адрес '''$1''' беше блокиран.",
	'regexblock-block-success' => 'Блокирането беше успешно',
	'regexblock-form-submit-regex' => 'Невалиден регулярен израз.',
	'regexblock-form-submit-expiry' => 'Необходимо е да бъде посочен срок на изтичане.',
	'regexblock-already-blocked' => '$1 е вече блокиран.',
	'regexblock-stats-username' => 'За $1',
	'regexblock-currently-blocked' => 'Текущо блокирани адреси:',
	'regexblock-view-blocked' => 'Преглед на блокираните по:',
	'regexblock-view-all' => 'Всички',
	'regexblock-view-match' => '(пълно съвпадение)',
	'regexblock-view-reason' => 'причина: $1',
	'regexblock-view-block-infinite' => 'перманентно блокиране',
	'regexblock-view-block-temporary' => 'изтича на',
	'regexblock-view-block-by' => 'блокиран от',
	'regexblock-view-block-unblock' => 'отблокиране',
	'regexblock-view-stats' => '(статистика)',
	'regexblock-view-empty' => 'Списъкът на блокирани имена и адреси е празен.',
	'regexblock-view-time' => 'на $1',
);

/** Catalan (Català)
 * @author SMP
 */
$messages['ca'] = array(
	'regexblock-already-blocked' => '$1 ja està blocat.',
	'regexblock-view-go' => 'Vés-hi',
);

/** Chamorro (Chamoru)
 * @author Jatrobat
 */
$messages['ch'] = array(
	'regexblock-view-go' => 'Hånao',
);

/** Welsh (Cymraeg)
 * @author Lloffiwr
 */
$messages['cy'] = array(
	'regexblock-view-all' => 'Oll',
);

/** Danish (Dansk)
 * @author Jon Harald Søby
 */
$messages['da'] = array(
	'regexblock-form-reason' => 'Begrundelse:',
	'regexblock-stats-username' => 'For $1',
);

/** German (Deutsch)
 * @author Purodha
 */
$messages['de'] = array(
	'regexblock-form-reason' => 'Grund:',
);

/** Greek (Ελληνικά)
 * @author Consta
 */
$messages['el'] = array(
	'regexblock-form-reason' => 'Λόγος:',
	'regexblock-stats-username' => 'Για $1',
	'regexblock-view-reason' => 'Λόγος: $1',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'regexblock-unblock-success' => 'Malforbaro sukcesis',
	'regexblock-unblock-log' => "Salutnomo aŭ IP-adreso '''$1''' estis restarigita.",
	'regexblock-form-username' => 'IP Adreso aŭ salutnomo:',
	'regexblock-form-reason' => 'Kialo:',
	'regexblock-form-expiry' => 'Findato:',
	'regexblock-form-submit' => 'Forbari ĉi tiun uzanton',
	'regexblock-block-success' => 'Forbaro sukcesis',
	'regexblock-already-blocked' => '$1 jam estas forbarita.',
	'regexblock-stats-username' => 'Por $1',
	'regexblock-stats-times' => 'estis forbarita je',
	'regexblock-currently-blocked' => 'Nune forbaritaj adresoj:',
	'regexblock-view-all' => 'Ĉiuj',
	'regexblock-view-go' => 'Ek!',
	'regexblock-view-reason' => 'kialo: $1',
	'regexblock-view-reason-default' => 'malspecifa kialo',
	'regexblock-view-block-by' => 'forbarita de',
	'regexblock-view-block-unblock' => 'restarigu',
	'regexblock-view-time' => 'je $1',
);

/** Spanish (Español)
 * @author Jatrobat
 * @author Piolinfax
 */
$messages['es'] = array(
	'regexblock-view-all' => 'Todos',
	'regexblock-view-go' => 'Ir',
);

/** French (Français)
 * @author Urhixidur
 */
$messages['fr'] = array(
	'regexblock' => 'Expressions régulières pour bloquer un utilisateur ou une IP',
	'regexblock-desc' => 'Extension utilisée pour bloquer des utilisateurs ou des adresses IP avec des expressions régulières. Contient à la fois un mécanisme de blocage ainsi qu’[[Special:Regexblock|une page]] pouvant ajouter et gérer les blocages',
	'regexblock-special-desc' => 'Blocage alternatif d’un utilisateur (par un nom donné ou par des expressions régulières)',
	'regexblock-stat-desc' => 'Affiche les [[Special:Regexblockstats|statistiques de blocages]] pour l’extension regexblock.',
	'regexblock-page-title' => 'Blocage d’un nom par une expression régulière',
	'regexblockstats' => 'Statistiques sur les blocages par expressions régulières',
	'regexblock-reason-ip' => 'Cette adresse IP est écartée de toute édition pour cause de vandalisme ou autres faits analogues par vous ou quelqu’un d’autre partageant votre adresse IP. Si vous êtes persuadé qu’il s’agit d’une erreur, $1',
	'regexblock-reason-name' => 'Cet utilisateur est écarté de toute édition pour cause de vandalisme ou autres faits analogues. Si vous êtes persuadé qu’il s’agit d’une erreur, $1.',
	'regexblock-reason-regex' => 'Cet utilisateur est écarté de toute édition pour cause de vandalisme ou autres faits analogues par un utilisateur ayant un nom similaire. Veuillez créer un autre compte ou $1 pour signaler le problème.',
	'regexblock-help' => "Utilisez le formulaire ci-dessous pour bloquer l’accès en écriture d’une adresse IP ou d’un nom d’utilisateur. Ceci doit être fait uniquement pour éviter tout vandalisme et conformément aux règles prescrites sur le projet. ''Cette page vous autorise même à bloquer des utilisateurs non enregistrés et permet aussi de bloquer des utilisateur présentant des noms similaires. Par exemple, « Test » sera bloqué en même temps que « Test 2 » etc. Vous pouvez aussi bloquer des adresses IP entières, ce qui signifie que personne travaillant depuis celles-ci ne pourra éditer des pages. Note : des adresses IP partielles seront considérées comme des noms d’utilisateur lors du blocage. Si aucun motif n’est indiqué en commentaire, un motif par défaut sera indiqué.''",
	'regexblock-page-title-1' => 'Blocage d’une adresse utilisant une expression régulière',
	'regexblock-unblock-success' => 'Le déblocage a réussi',
	'regexblock-unblock-log' => "L’utilisateur ou l’adresse IP '''$1''' a été débloqué.",
	'regexblock-unblock-error' => 'Erreur de déblocage de $1. L’utilisateur n’existe probablement pas.',
	'regexblock-form-username' => 'Adresse IP ou Utilisateur :',
	'regexblock-form-reason' => 'Motif :',
	'regexblock-form-expiry' => 'Expiration :&#160;',
	'regexblock-form-match' => 'Terme exact',
	'regexblock-form-account-block' => 'Interdire la création d’un nouveau compte.',
	'regexblock-form-submit' => 'Bloquer cet utilisateur',
	'regexblock-block-log' => "L’Utilisateur ou l’adresse IP '''$1''' a été bloqué.",
	'regexblock-block-success' => 'Le blocage a réussi',
	'regexblock-form-submit-empty' => 'Indiquez un nom d’utilisateur ou une adresse IP à bloquer.',
	'regexblock-form-submit-regex' => 'Expression régulière incorrecte.',
	'regexblock-form-submit-expiry' => 'Précisez une période d’expiration.',
	'regexblock-already-blocked' => '$1 est déjà bloqué.',
	'regexblock-stats-title' => 'Statistiques des blocages par expressions régulières',
	'regexblock-stats-username' => 'Pour $1',
	'regexblock-stats-times' => 'a été bloqué le',
	'regexblock-stats-logging' => 'enregistré depuis l’adresse',
	'regexblock-currently-blocked' => 'Adresses actuellement bloquées :',
	'regexblock-view-blocked' => 'Voir les blocages par :',
	'regexblock-view-all' => 'Tous',
	'regexblock-view-go' => 'Lancer',
	'regexblock-view-match' => '(terme exact)',
	'regexblock-view-regex' => '(expression régulière)',
	'regexblock-view-account' => '(création des comptes bloquée)',
	'regexblock-view-reason' => 'motif : $1',
	'regexblock-view-reason-default' => 'aucun motif indiqué',
	'regexblock-view-block-infinite' => 'blocage permanent',
	'regexblock-view-block-temporary' => 'expire le',
	'regexblock-view-block-expired' => 'EXPIRÉ le',
	'regexblock-view-block-by' => 'bloqué par',
	'regexblock-view-block-unblock' => 'débloquer',
	'regexblock-view-stats' => '(statistiques)',
	'regexblock-view-empty' => 'La liste des utilisateurs et des adresses IP bloqués est vide.',
	'regexblock-view-time' => 'le $1',
);

/** Western Frisian (Frysk)
 * @author Snakesteuben
 */
$messages['fy'] = array(
	'regexblock-form-expiry' => 'Ferrint nei:',
	'regexblock-already-blocked' => '$1 is al útsluten.',
	'regexblock-view-all' => 'Alles',
);

/** Galician (Galego)
 * @author Alma
 * @author Toliño
 * @author Xosé
 */
$messages['gl'] = array(
	'regexblock' => 'Bloqueo Regex',
	'regexblock-desc' => 'Extensión usada para bloquear nomes de usuario e mais enderezos IP con expresións regulares. Contén o mecanismo de bloqueo e unha [[Special:Regexblock|páxina especial]] para engadir/xestionar bloqueos',
	'regexblock-special-desc' => 'bloqueo alternativo a usuario (dando o seu nome, usando exprecións regulares)',
	'regexblock-stat-desc' => 'Amosa as [[Special:Regexblockstats|estatísticas de bloqueo]] para a extensión Regex Block',
	'regexblock-page-title' => 'Bloqueo do nome da expresión regular',
	'regexblockstats' => 'Estatísticas do bloqueo Regex',
	'regexblock-reason-ip' => 'A este enderezo IP estalle prohibido editar debido a vandalismo ou outras actividades negativas realizadas por vostede ou por alguén que comparte o seu enderezo IP. Se pensa que se trata dun erro, $1',
	'regexblock-reason-name' => 'A este nome de usuario estalle prohibido editar debido a vandalismo ou outras actividades negativas. Se pensa que se trata dun erro, $1',
	'regexblock-reason-regex' => 'A este nome de usuario prohíbeselle editar debido a vandalismo ou outras actividades negativas por parte dun usuario cun nome semellante. Cree un nome de usuario diferente ou $1 sobre o problema',
	'regexblock-help' => "Use o formulario de embaixo para bloquear o acceso de escritura desde un determinado enderezo IP ou nome de usuario.
Isto debería facerse só para previr vandalismo, e segundo a política e normas de bloqueo.
''Esta páxina permitiralle bloquear incluso usuarios que non existen, e usuarios con nomes semellantes ao dado, é dicir, «Test» será bloqueado xunto con «Test 2», etc. Tamén pode bloquear enderezos IP completos, no sentido de que ninguén rexistrado nos mesmos será capaz de editar páxinas. Nota: os enderezos IP parciais serán tratados polos nomes de usuarios na determinación do bloqueo. Se non se especifica a razón, será usado por defecto un motivo xenérico.''",
	'regexblock-page-title-1' => 'Bloquear un enderezo usando expresións regulares',
	'regexblock-unblock-success' => 'O desbloqueo foi un éxito',
	'regexblock-unblock-log' => "O nome de usuario ou o enderezo IP '''$1''' foi desbloqueado.",
	'regexblock-unblock-error' => 'Erro desbloqueando $1. Probabelmente non existe tal usuario.',
	'regexblock-form-username' => 'Enderezo IP ou nome de usuario:',
	'regexblock-form-reason' => 'Razón:',
	'regexblock-form-expiry' => 'Remate:',
	'regexblock-form-match' => 'Procura exacta',
	'regexblock-form-account-block' => 'Bloqueada a creación de novas contas',
	'regexblock-form-submit' => 'Bloquear este usuario',
	'regexblock-block-log' => "O nome de usuario ou o enderezo IP '''$1''' foi bloqueado.",
	'regexblock-block-success' => 'Bloqueo con éxito',
	'regexblock-form-submit-empty' => 'Dar un nome de usuario ou un enderezo IP para bloquear.',
	'regexblock-form-submit-regex' => 'Expresión regular non válida.',
	'regexblock-form-submit-expiry' => 'Especifique un período de expiración.',
	'regexblock-already-blocked' => '$1 xa está bloqueado.',
	'regexblock-stats-title' => 'Estatíticas do bloqueo Regex',
	'regexblock-stats-username' => 'De $1',
	'regexblock-stats-times' => 'foi bloqueado en',
	'regexblock-stats-logging' => 'rexistrarse desde o enderezo',
	'regexblock-currently-blocked' => 'Enderezos actualmente bloqueados:',
	'regexblock-view-blocked' => 'Ver bloqueado por:',
	'regexblock-view-all' => 'Todo',
	'regexblock-view-go' => 'Adiante',
	'regexblock-view-match' => '(procura exacta)',
	'regexblock-view-regex' => '(coincidencia regex)',
	'regexblock-view-account' => '(bloqueo de creación de contas)',
	'regexblock-view-reason' => 'razón: $1',
	'regexblock-view-reason-default' => 'razón xenérica',
	'regexblock-view-block-infinite' => 'bloqueo permanente',
	'regexblock-view-block-temporary' => 'expira o',
	'regexblock-view-block-expired' => 'EXPIRADO o',
	'regexblock-view-block-by' => 'bloqueado por',
	'regexblock-view-block-unblock' => 'desbloquear',
	'regexblock-view-stats' => '(estatísticas)',
	'regexblock-view-empty' => 'A listaxe dos nomes e enderezos bloqueados está baleira.',
	'regexblock-view-time' => 'en $1',
);

/** Gothic (������������������������������������)
 * @author Jocke Pirat
 */
$messages['got'] = array(
	'regexblock-form-reason' => 'Faírina',
);

/** Manx (Gaelg)
 * @author MacTire02
 */
$messages['gv'] = array(
	'regexblock-form-username' => 'Enmys IP ny ennym ymmydeyr:',
	'regexblock-form-reason' => 'Fa:',
	'regexblock-view-go' => 'Gow',
	'regexblock-view-reason' => 'fa: $1',
);

/** Hawaiian (Hawai`i)
 * @author Kalani
 * @author Singularity
 */
$messages['haw'] = array(
	'regexblock-form-reason' => 'Kumu:',
	'regexblock-stats-username' => 'No $1',
);

/** Hindi (हिन्दी)
 * @author Kaustubh
 */
$messages['hi'] = array(
	'regexblock' => 'रेजएक्सब्लॉक',
	'regexblock-page-title' => 'रेग्युलर एक्स्प्रेशन नाम ब्लॉक',
	'regexblockstats' => 'रेजएक्स ब्लॉक सांख्यिकी',
	'regexblock-page-title-1' => 'रेग्युलर एक्स्प्रेशनका इस्तेमाल करके एड्रेस ब्लॉक करें',
	'regexblock-unblock-success' => 'अनब्लॉक यशस्वी',
	'regexblock-unblock-log' => "सदस्यनाम या आइपी एड्रेस '''$1''' को अनब्लॉक किया।",
	'regexblock-unblock-error' => '$1 को अनब्लॉक करनेमें समस्या।
शायद ऐसा सदस्य अस्तित्वमें नहीं।',
	'regexblock-form-username' => 'आइपी एड्रेस या सदस्यनाम:',
	'regexblock-form-reason' => 'कारण:',
	'regexblock-form-expiry' => 'समाप्ती:',
	'regexblock-form-match' => 'सही मैच',
	'regexblock-form-account-block' => 'नया खाता खोलने के लिये प्रतिबंधित करें',
	'regexblock-form-submit' => 'इस सदस्यको ब्लॉक करें',
	'regexblock-block-log' => "'''$1''' इस सदस्य नाम अथव आयपी एड्रेसको ब्लॉक कर दिया हैं।",
	'regexblock-block-success' => 'ब्लॉक यशस्वी',
	'regexblock-form-submit-empty' => 'ब्लॉक करनेके लिये एक सदस्य नाम या आइपी एड्रेस दें।',
	'regexblock-form-submit-regex' => 'गलत रेग्युलर एक्स्प्रेशन।',
	'regexblock-form-submit-expiry' => 'कृपया समाप्ति कालावधि दें।',
	'regexblock-already-blocked' => '$1 को पहलेसे ब्लॉक किया हुआ हैं।',
	'regexblock-stats-title' => 'रेजएक्स ब्लॉक सांख्यिकी',
	'regexblock-stats-username' => '$1 के लिये',
	'regexblock-stats-times' => 'को ब्लॉक किया',
	'regexblock-stats-logging' => 'एड्रेससे सूची बना रहें हैं',
	'regexblock-currently-blocked' => 'अभी ब्लॉक किये हुए एड्रेस:',
	'regexblock-view-blocked' => 'जिसने ब्लॉक किया उसके अनुसार सूची देखें:',
	'regexblock-view-all' => 'सभी',
	'regexblock-view-go' => 'जायें',
	'regexblock-view-match' => '(सही मैच)',
	'regexblock-view-regex' => '(रेजएक्स मैच)',
	'regexblock-view-account' => '(खाता खोलने पर ब्लॉक)',
	'regexblock-view-reason' => 'कारण: $1',
	'regexblock-view-reason-default' => 'सर्वसाधारण कारण',
	'regexblock-view-block-infinite' => 'हमेशा के लिये ब्लॉक',
	'regexblock-view-block-temporary' => 'समाप्ती',
	'regexblock-view-block-expired' => 'समाप्त हुआ, समय',
	'regexblock-view-block-by' => 'ब्लॉक कर्ता',
	'regexblock-view-block-unblock' => 'अनब्लॉक',
	'regexblock-view-stats' => '(सांख्यिकी)',
	'regexblock-view-empty' => 'ब्लॉक किये सदस्यनाम और आइपी एड्रेसोंकी सूची खाली हैं।',
	'regexblock-view-time' => '$1 पर',
);

/** Hiligaynon (Ilonggo)
 * @author Jose77
 */
$messages['hil'] = array(
	'regexblock-form-reason' => 'Rason:',
	'regexblock-view-go' => 'Lakat',
);

/** Croatian (Hrvatski)
 * @author Dnik
 * @author SpeedyGonsales
 */
$messages['hr'] = array(
	'regexblock' => 'Blokiranje pomoću regularnih izraza',
	'regexblock-page-title' => 'Blokiranje pomoću regularnih izraza',
	'regexblockstats' => 'Statistika blokiranja regularnim izrazima',
	'regexblock-reason-ip' => 'Ova IP adresa je blokirana (tj. nemoguće je uređivati stranice) zbog vandalizma ili nekog drugog vašeg prekršaja (ili nekog s kim dijelite IP adresu). Ukoliko mislite da je posrijedi greška, molimo $1',
	'regexblock-reason-name' => 'Ovo suradničko ime je blokirano (tj. spriječeno mu je uređivanje članaka) zbog vandalizma ili nekog drugog prekršaja. Ukoliko mislite da se radi o grešci, molimo $1',
	'regexblock-reason-regex' => 'Ovo suradničko ime je blokirano (tj. spriječeno mu je uređivanje članaka) zbog vandalizma ili nekog drugog prekršaja suradnika s istim (ili sličnem) imenom. Ukoliko mislite da se radi o grešci, molimo $1',
	'regexblock-help' => "Rabite donju formu za blokiranje određenih IP adresa ili suradnika. TO treba činiti samo radi sprječavanja vandalizama, u skladu s pravilima.

''Ova stranica omogućava vam blokiranje suradničkih imena prema uzorku (postojećih i novih), npr. ako blokirate « Test 2», blokirat ćete i « Test » itd. Možete također blokirati IP adrese, što znači da nitko tko se prijavi s njih neće moći uređivati. Napomena: djelomične IP adrese bit će analizirane prema suradničkim imenima u određivanju trajanja bloka. Ukoliko razlog nije dan, bit će navedeno generičko objašnjenje.''",
	'regexblock-page-title-1' => 'Blokiraj adresu koristeći regularni izraz',
	'regexblock-unblock-success' => 'Deblokiranje uspjelo',
	'regexblock-unblock-log' => "Suradnik ili IP adresa '''$1''' je deblokiran.",
	'regexblock-unblock-error' => 'Greška prilikom deblokiranja $1. Taj suradnik vjerojatno ne postoji.',
	'regexblock-form-username' => 'IP-adresa ili ime suradnika:',
	'regexblock-form-reason' => 'Razlog:',
	'regexblock-form-expiry' => 'Istek bloka:',
	'regexblock-form-match' => 'Točno podudaranje',
	'regexblock-form-account-block' => 'Blokiraj stvaranje novih računa',
	'regexblock-form-submit' => 'Blokiraj ovog suradnika',
	'regexblock-block-log' => "Suradnik ili IP-adresa '''$1''' su blokirani.",
	'regexblock-block-success' => 'Blokiranje uspjelo',
	'regexblock-form-submit-empty' => 'Unesite ime suradnika ili IP-adresu za blokiranje.',
	'regexblock-form-submit-regex' => 'Pogrešan regularni izraz.',
	'regexblock-form-submit-expiry' => 'Molimo odredite razdoblje isteka.',
	'regexblock-already-blocked' => '$1 je već blokiran.',
	'regexblock-stats-title' => 'Statistika blokiranja reg. izrazima',
	'regexblock-stats-username' => 'Za $1',
	'regexblock-stats-times' => 'je blokiran u',
	'regexblock-stats-logging' => 'prijava s adrese',
	'regexblock-currently-blocked' => 'Trenutno blokirane adrese:',
	'regexblock-view-blocked' => 'Pregled po onom tko je blokirao:',
	'regexblock-view-all' => 'Svi',
	'regexblock-view-go' => 'Kreni',
	'regexblock-view-match' => '(točno podudaranje)',
	'regexblock-view-regex' => '(podudaranje reg. izrazom)',
	'regexblock-view-account' => '(blokiranje otvaranja računa)',
	'regexblock-view-reason' => 'razlog: $1',
	'regexblock-view-reason-default' => 'uobičajeni razlog',
	'regexblock-view-block-infinite' => 'trajna blokada',
	'regexblock-view-block-temporary' => 'ističe u',
	'regexblock-view-block-expired' => 'ISTEKLO u',
	'regexblock-view-block-by' => 'blokiran od',
	'regexblock-view-block-unblock' => 'deblokiraj',
	'regexblock-view-stats' => '(statistika)',
	'regexblock-view-empty' => 'Popis blokiranih imena i adresa je prazan.',
	'regexblock-view-time' => 'u $1',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'regexblock-desc' => 'Rozšěrjenje wužiwane za blokowanje wužiwarskich mjenow a IP-adresow z regularnymi wurazami. Wobsahuje blokowanski mechanizm kaž tež [[Special:Regexblock|specialnu stronu]] za přidaće/zrjadowanje blokowanjow',
	'regexblock-special-desc' => 'alternatiwne wužiwarske blokowanje (přez date mjeno, z pomocu regularnych wurazow)',
	'regexblock-stat-desc' => 'Zwobraznja [[Special:Regexblockstats|statistiku blokowanjow]] za rozšěrjenje regexblock',
	'regexblock-page-title' => 'Blokowanje mjenow regularnych wurazow',
	'regexblockstats' => 'Regex Block Statistika',
	'regexblock-reason-ip' => 'Tuta IP-adresa so dla wandalizma abo mylenje přez tebje abo někoho druheho, kiž IP-adresu z tobu dźěli, za wobdźěłowanje zawěra. Jeli mysliš, zo to je zmylk, prošu $1',
	'regexblock-reason-name' => 'Tute wužiwarske mjeno so dla wandalizma abo druheho mylenja za wobdźěłowanje zawěra. Jerli mysliš, zo to je zmylk, prošu $1',
	'regexblock-reason-regex' => 'Tute wužiwarske mjeno so dla wandalizma abo druheho mylenja přez wužiwarja z podobnym mjenom zawěra. Prošu wutwor druhe wužiwarske mjeno abo $1 wo tutym problemje',
	'regexblock-help' => 'Wužij formular deleka, zo by pisanski přistup ze specifiskeje adresy abo wužiwarskeho mjena blokował. To měło so jenož činić, zo by wandalizmej zadźěwało a wotpowědujo prawidłam. \'\'Tuta strona budźe će dowoleć, samo njeeksistowacych wužiwarjow blokować a budźe tež wužiwarjow z mjenom, kotrež je datemu podobne, blokować, t.r. "test" budźe so runje tak blokować kaž "test 2" atd. Móžeš dospołne OP-adresy blokować, zo by něchtó, kiž so z nich přizjewja, strony wobdźěłać móhł. Kedźbu: dźělne IP-adresy so přez wužiwarske mjeno wužiwaja, zo by blokowanje postajiło. Jeli přičina njeje podata, budźe so powšitkowna přičina wužiwać.\'\'',
	'regexblock-page-title-1' => 'Adresu z pomocu regularnych wurazow blokować',
	'regexblock-unblock-success' => 'Wotblokowanje wuspěšne',
	'regexblock-unblock-log' => "Wužiwarske mjeno abo IP-adresa '''$1''' wotblokowana.",
	'regexblock-unblock-error' => 'Zmylk při wotblokowanju $1. Najskerje tajki wužiwar njeje.',
	'regexblock-form-username' => 'IP-adresa abo wužiwarske mjeno:',
	'regexblock-form-reason' => 'Přičina:',
	'regexblock-form-expiry' => 'Spadnjenje:',
	'regexblock-form-match' => 'Eksaktny wotpowědnik',
	'regexblock-form-account-block' => 'Wutworjenje nowych kontow blokować',
	'regexblock-form-submit' => 'Tutoho wužiwarja blokować',
	'regexblock-block-log' => "Wužiwarske mjeno abo IP-adresa '''$1''' je so blokowało/blokowała.",
	'regexblock-block-success' => 'Blokowanje wuspěšne',
	'regexblock-form-submit-empty' => 'Podaj wužiwarske mjeno abo IP-adresu za blokowanje.',
	'regexblock-form-submit-regex' => 'Njepłaćiwy regularny wuraz.',
	'regexblock-form-submit-expiry' => 'Podaj prošu periodu spadnjenja.',
	'regexblock-already-blocked' => '$1 je hižo zablokowany.',
	'regexblock-stats-title' => 'Regex Block Statistiske podaća',
	'regexblock-stats-username' => 'Za $1',
	'regexblock-stats-times' => 'bu blokowane',
	'regexblock-stats-logging' => 'protokolowanje z adresy',
	'regexblock-currently-blocked' => 'Tuchwilu zablokowane adresy:',
	'regexblock-view-blocked' => 'Wobhladanje zablokowane wot:',
	'regexblock-view-all' => 'Wšě',
	'regexblock-view-go' => 'Dźi',
	'regexblock-view-match' => '(eksaktny wotpowědnik)',
	'regexblock-view-regex' => '(regularny wuraz wotpowědnik)',
	'regexblock-view-account' => '(wutworjenje konta blokować)',
	'regexblock-view-reason' => 'přičina: $1',
	'regexblock-view-reason-default' => 'powšitkowna přičina',
	'regexblock-view-block-infinite' => 'trajne blokowanje',
	'regexblock-view-block-temporary' => 'spadnje',
	'regexblock-view-block-expired' => 'SPADNJENY',
	'regexblock-view-block-by' => 'zablokowany wot',
	'regexblock-view-block-unblock' => 'wotblokować',
	'regexblock-view-stats' => '(statistiske podaća)',
	'regexblock-view-empty' => 'Lisćina zablokowanych mjenow a adresow je prózdna.',
	'regexblock-view-time' => '$1',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'regexblock-form-reason' => 'Motivo:',
	'regexblock-form-expiry' => 'Expiration:',
	'regexblock-already-blocked' => '"$1" es ja blocate.',
	'regexblock-stats-username' => 'Pro $1',
	'regexblock-view-all' => 'Totes',
);

/** Indonesian (Bahasa Indonesia)
 * @author Rex
 */
$messages['id'] = array(
	'regexblock-form-reason' => 'Alasan:',
	'regexblock-view-all' => 'Semua',
);

/** Icelandic (Íslenska)
 * @author S.Örvarr.S
 */
$messages['is'] = array(
	'regexblock-form-reason' => 'Ástæða:',
	'regexblock-stats-username' => 'Fyrir $1',
);

/** Italian (Italiano)
 * @author Darth Kule
 */
$messages['it'] = array(
	'regexblock-form-reason' => 'Motivo:',
	'regexblock-form-expiry' => 'Scadenza del blocco:',
	'regexblock-view-go' => 'Vai',
);

/** Javanese (Basa Jawa)
 * @author Meursault2004
 */
$messages['jv'] = array(
	'regexblockstats' => 'Statistik pamblokiran regex',
	'regexblock-help' => "Nganggoa formulir ing ngisor iki kanggo mblokir aksès panulisan saka sawijining alamat IP tartamtu utawa jeneng panganggo.
Iki nanging namung kudu dilakokaké waé kanggo menggak vandalisme, lan miturut kawicaksanan sing ana.
''Nganggo kaca iki panjenengan uga bisa mblokir panganggo-panganggo sing durung ana, lan uga mblokir panganggo sing duwé jeneng mèmper karo jeneng sing wis ana. Contoné « Test » bakal diblokir karo « Test 2 » lsp.
Panjenengan uga bisa mblokir alamat-alamat IP sacara pol-polan, dadi tegesé ora ana sing bisa log mlebu saka kana lan nyunting kaca-kaca.
Cathetan: alamat IP parsial bakal dianggep miturut jeneng panganggo yèn arep diblokir.
Yèn ora ana alesan sing diwènèhaké, sawijining alesan umum baku bakal dienggo.",
	'regexblock-form-username' => 'Alamat IP utawa jeneng panganggo:',
	'regexblock-form-reason' => 'Alesan:',
	'regexblock-form-match' => 'Persis cocog',
	'regexblock-block-log' => "Panganggo utawa alamat IP '''$1''' wis diblokir.",
	'regexblock-already-blocked' => '$1 wis diblokir',
	'regexblock-stats-username' => 'Kanggo $1',
	'regexblock-stats-times' => 'wis diblokir ing',
	'regexblock-view-blocked' => 'Ndeleng diblokir déning:',
	'regexblock-view-all' => 'Kabèh',
	'regexblock-view-go' => 'Golèk',
	'regexblock-view-reason' => 'alesan: $1',
	'regexblock-view-reason-default' => 'alesan umum',
	'regexblock-view-block-infinite' => 'blokade permanèn',
	'regexblock-view-block-temporary' => 'kadaluwarsa ing',
	'regexblock-view-block-expired' => 'KADALUWARSA ing',
	'regexblock-view-block-by' => 'diblokir déning',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 * @author Lovekhmer
 * @author គីមស៊្រុន
 */
$messages['km'] = array(
	'regexblock-unblock-success' => 'បានឈប់ហាមឃាត់ដោយជោគជ័យ',
	'regexblock-unblock-log' => "ឈ្មោះអ្នកប្រើប្រាស់ ឬ អាស័យដ្ឋាន IP '''$1''' បានត្រូវ លែងរាំងខ្ទប់។",
	'regexblock-form-username' => 'អាសយដ្ឋានIP ឬឈ្មោះអ្នកប្រើប្រាស់៖',
	'regexblock-form-reason' => 'មូលហេតុ៖',
	'regexblock-form-expiry' => 'ផុតកំណត់:',
	'regexblock-form-submit' => 'ហាមឃាត់អ្នកប្រើប្រាស់នេះ',
	'regexblock-block-log' => "ឈ្មោះអ្នកប្រើប្រាស់ ឬ អាស័យដ្ឋាន IP '''$1''' បានត្រូវ រាំងខ្ទប់។",
	'regexblock-block-success' => 'រាំងខ្ទប់ដោយជោគជ័យ',
	'regexblock-form-submit-empty' => 'ផ្តល់ឈ្មោះអ្នកប្រើប្រាស់ឬអាសយដ្ឋានIPដើម្បីហាមឃាត់។',
	'regexblock-already-blocked' => '$1ត្រូវបានហាមឃាត់រួចហើយ។',
	'regexblock-stats-username' => 'សំរាប់ $1',
	'regexblock-stats-logging' => 'ពិនិត្យចូល ពី អាស័យដ្ឋាន',
	'regexblock-currently-blocked' => 'អាសយដ្ឋានដែលត្រូវបានហាមឃាត់បច្ចុប្បន្ន៖',
	'regexblock-view-all' => 'ទាំងអស់',
	'regexblock-view-go' => 'ទៅ',
	'regexblock-view-reason' => 'មូលហេតុ៖ $1',
	'regexblock-view-block-temporary' => 'ផុតកំនត់នៅ',
	'regexblock-view-block-expired' => 'បានផុតកំនត់នៅ',
	'regexblock-view-block-by' => 'ត្រូវបានហាមឃាត់ដោយ',
	'regexblock-view-block-unblock' => 'ឈប់ហាមឃាត់',
	'regexblock-view-empty' => 'បញ្ជីឈ្មោះអ្នកប្រើប្រាស់និងអាសយដ្ឋានIPគឺទទេ។',
	'regexblock-view-time' => 'នៅ $1',
);

/** Kinaray-a (Kinaray-a)
 * @author Jose77
 */
$messages['krj'] = array(
	'regexblock-view-go' => 'Agto',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'regexblock-form-reason' => 'Aanlass:',
	'regexblock-view-all' => 'All',
	'regexblock-view-go' => 'Loß Jonn!',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'regexblock' => 'Begrëffer fir e Benotzer oder eng IP-Adress ze spären',
	'regexblock-reason-name' => 'Dëse Benotzer ass wéinst Vandalismus oder ähnlech Ursaache gespaart an däerf keng Ännerunge maachen. Wann Dir iwwerzeecht sidd datt et sech ëm ee Feeler handelt, $1',
	'regexblock-unblock-success' => 'Spär opgehuewen',
	'regexblock-unblock-log' => "D'Spär vum Benotzer oder vun der IP-Adress '''$1'''' gouf opgehuewen.",
	'regexblock-form-username' => 'IP-Adress oder Benotzer:',
	'regexblock-form-reason' => 'Grond:',
	'regexblock-form-account-block' => 'Uleeë vun neie Benotzerkonte spären',
	'regexblock-form-submit' => 'Dëse Benotzer spären',
	'regexblock-block-log' => "De Benotzer oder d'IP-Adress '''$1''' gouf gespaart.",
	'regexblock-block-success' => "D'Spär ass elo ageschalt",
	'regexblock-already-blocked' => '$1 ass scho gespaart.',
	'regexblock-stats-username' => 'Fir $1',
	'regexblock-stats-times' => 'gespaart den',
	'regexblock-currently-blocked' => 'Aktuell gespaarten Adressen:',
	'regexblock-view-blocked' => "Weis d'Späre vum:",
	'regexblock-view-all' => 'Alleguer',
	'regexblock-view-go' => 'Lass',
	'regexblock-view-reason' => 'Grond: $1',
	'regexblock-view-block-infinite' => 'permanent Spär',
	'regexblock-view-block-by' => 'gespaart vum',
	'regexblock-view-block-unblock' => 'Spär ophiewen',
	'regexblock-view-stats' => '(Statistik)',
	'regexblock-view-empty' => "D'Lëscht vun de gespaarte Benotzer an IP-Adressen ass eidel.",
	'regexblock-view-time' => 'den $1',
);

/** Moksha (Мокшень)
 * @author Khazar II
 */
$messages['mdf'] = array(
	'regexblock-already-blocked' => '"$1" сёлкфоль ни',
);

/** Malayalam (മലയാളം)
 * @author Shijualex
 */
$messages['ml'] = array(
	'regexblock-reason-ip' => 'താങ്കളോ അല്ലെങ്കില്‍ മറ്റോരോ നടത്തിയ നശീകരണ പ്രവര്‍ത്തനം മൂലം താങ്കള്‍ ഇപ്പോള്‍ ഉപയോഗിക്കുന്ന ഐപി വിലാസം തിരുത്തല്‍ നടത്തുന്നതില്‍ നിന്നു തടയപ്പെട്ടിരിക്കുന്നു.
ഇത് ഒരു പിഴവാണെന്നു താങ്കള്‍ കരുതുന്നെങ്കില്‍ ദയവായി $1',
	'regexblock-reason-name' => 'നശീകരണ പ്രവര്‍ത്തനങ്ങള്‍ മൂലം താങ്കളുടെ ഉപയോക്തൃനാമത്തെ തിരുത്തല്‍ നടത്തുന്നതില്‍ നിന്നു തടഞ്ഞിരിക്കുന്നു. ഇത് ഒരു പിഴവാണെന്നു താങ്കള്‍ കരുതുന്നെങ്കില്‍ ദയവായി $1',
	'regexblock-reason-regex' => 'ഈ ഉപയോക്തൃനാമത്തോടു സാമ്യമുള്ള ഉപയോക്താവിന്റെ നശീകരണ പ്രവര്‍ത്തനങ്ങള്‍ മൂലം ഈ ഉപയോക്തൃനാമത്തെ തിരുത്തല്‍ നടത്തുന്നതില്‍ നിന്നു തടഞ്ഞിരിക്കുന്നു. 
ഒന്നുകില്‍ പുതിയൊരു ഉപയോക്തൃനാമം ഉണ്ടാക്കുക അല്ലെങ്കില്‍ ഈ പ്രശ്നത്തെക്കുറിച്ച് $1',
	'regexblock-unblock-success' => 'സ്വതന്ത്രമാക്കല്‍ വിജയിച്ചിരിക്കുന്നു',
	'regexblock-unblock-log' => "'''$1''' എന്ന ഉപയോക്തൃനാമം അല്ലെങ്കില്‍ ഐപിവിലാസം സ്വതന്ത്രമാക്കിയിരിക്കുന്നു.",
	'regexblock-unblock-error' => '$1നെ സ്വതന്ത്രമാക്കുന്നതില്‍ പിശക്. അങ്ങനെയൊരു ഉപയോക്താവ് നിലവിലില്ലായിരിക്കും എന്നതാവും കാരണം.',
	'regexblock-form-username' => 'ഐപി വിലാസം അല്ലെങ്കില്‍ ഉപയോക്തൃനാമം:',
	'regexblock-form-reason' => 'കാരണം:',
	'regexblock-form-expiry' => 'കാലാവധി:',
	'regexblock-form-match' => 'കൃത്യമായി യോജിക്കുന്നവ',
	'regexblock-form-account-block' => 'പുതിയ അക്കൗണ്ടുകള്‍ സൃഷ്ടിക്കുന്നതു തടയുക',
	'regexblock-form-submit' => ' ഈ  ഉപയോക്താവിനെ തടയുക',
	'regexblock-block-log' => "'''$1''' എന്ന ഉപയോക്തൃനാമം അല്ലെങ്കില്‍ ഐപി വിലാസം തടയപ്പെട്ടിരിക്കുന്നു.",
	'regexblock-block-success' => 'തടയല്‍ വിജയിച്ചിരിക്കുന്നു',
	'regexblock-form-submit-empty' => 'തടയുവാന്‍ വേണ്ടിയുള്ള ഉപയോക്തൃനാമമോ ഐപി വിലാസമോ ചേര്‍ക്കുക.',
	'regexblock-form-submit-expiry' => 'ദയവായി തടയലിനു ഒരു കാലാവധി തിരഞ്ഞെടുക്കുക.',
	'regexblock-already-blocked' => '$1 ഇതിനകം തന്നെ തടയപ്പെട്ടിരിക്കുന്നു.',
	'regexblock-stats-username' => '$1നു വേണ്ടി',
	'regexblock-stats-times' => 'തടയപ്പെട്ടത്',
	'regexblock-currently-blocked' => 'നിലവില്‍ തടയപ്പെട്ട വിലാസങ്ങള്‍:',
	'regexblock-view-all' => 'എല്ലാം',
	'regexblock-view-go' => 'പോകൂ',
	'regexblock-view-match' => '(കൃത്യമായി യോജിക്കുന്നവ)',
	'regexblock-view-account' => '(അക്കൗണ്ട് സൃഷ്ടിക്കുന്നതു തടയല്‍)',
	'regexblock-view-reason' => 'കാരണം: $1',
	'regexblock-view-reason-default' => 'സാമാന്യമായ കാരണം',
	'regexblock-view-block-infinite' => 'സ്ഥിരമായ തടയല്‍',
	'regexblock-view-block-temporary' => 'കാലാവധി തീരുന്നത്',
	'regexblock-view-block-expired' => 'കാലാവധി തീരുന്നത്',
	'regexblock-view-block-by' => 'തടഞ്ഞത്',
	'regexblock-view-block-unblock' => 'സ്വതന്ത്രമാക്കുക',
	'regexblock-view-time' => '$1 ന്‌',
);

/** Marathi (मराठी)
 * @author Kaustubh
 */
$messages['mr'] = array(
	'regexblock' => 'रेजएक्सब्लॉक',
	'regexblock-desc' => 'नेहमीची एक्स्प्रेशन्स वापरून सदस्य नावे व आयपी अंकपत्ते ब्लॉक करण्यासाठीचे एक्स्टेंशन. यामध्ये ब्लॉक करणे तसेच ब्लॉकचे व्यवस्थापन करण्यासाठीचे [[Special:Regexblock|विशेष पान]], दोघांचा समावेश आहे',
	'regexblock-special-desc' => 'दुसरा सदस्य ब्लॉक (दिलेल्या नावाने, नेहमीची एक्स्प्रेशन्स वापरून)',
	'regexblock-stat-desc' => 'regexblock एक्स्टेंशनसाठीची [[Special:Regexblockstats|ब्लॉक सांख्यिकी]] दर्शविते',
	'regexblock-page-title' => 'नेहमीचे एक्स्प्रेशन नाव ब्लॉक',
	'regexblockstats' => 'रेजएक्स ब्लॉक सांख्यिकी',
	'regexblock-reason-ip' => 'ह्या आयपी अंकपत्त्याला संपादनांपासून रोखण्यात आलेले आहे कारण तुम्ही अथवा इतर कोणीतरी या आयपी अंकपत्त्यावरून केलेला उत्पात.
जर तुमच्या मते हे चुकून झाले आहे, तर $1 करा',
	'regexblock-reason-name' => 'ह्या सदस्यनावाला उत्पात अथवा इतर कारणांमुळे संपादनांपासून रोखण्यात आलेले आहे.
तुमच्या मते हे चुकून झाले आहे, तर $1 करा',
	'regexblock-reason-regex' => 'ह्या सदस्यनावाशी साम्य असणार्‍या सदस्यनावावरून झालेला उत्पात अथवा इतर कारणांमुळे या सदस्यनावाला संपादनांपासून रोखण्यात आलेले आहे.
कृपया दुसरे सदस्यनाव तयार करा किंवा या संदेशाबद्दल विकियाशी संपर्क ($1) करा',
	'regexblock-help' => "खालील अर्ज विशिष्ट आयपी अंकपत्ता किंवा सदस्यनाव यांना ब्लॉक करण्यासाठी वापरता येईल.
हे फक्त उत्पात रोखण्यासाठीच वापरायचे आहे, तसेच नीतीला धरून असल्याची खात्री करा.
''हे पान वापरुन तुम्हाला अस्तित्वात नसलेले सदस्य सुद्धा ब्लॉक करता येतील, तसेच दिलेल्या नावाशी साधर्म्य राखणारी सदस्य नावे सुद्धा ब्लॉक केली जातील. उदा. « Test 2 » सोबत « Test » सुद्धा ब्लॉक होईल.
तुम्ही संपूर्ण आयपी अंकपत्ता सुद्धा ब्लॉक करू शकता, यामुळे त्या अंकपत्त्यावरून प्रवेश करणार्‍या कुणालाही संपादने करता येणार नाहीत.
सूचना: ब्लॉक ठरविण्यासाठी अर्धे आयपी अंकपत्ते सदस्यनावाने वापरले जातील.
जर कारण दिले नसेल तर एक अविचर साधारण कारण लिहिले जाईल.''",
	'regexblock-page-title-1' => 'नेहमीच्या एक्स्प्रेशन्सचा वापर करुन अंकपत्ता ब्लॉक करा',
	'regexblock-unblock-success' => 'अनब्लॉक यशस्वी',
	'regexblock-unblock-log' => "सदस्य नाव किंवा आयपी अंकपत्ता '''$1''' अनब्लॉक केलेला आहे.",
	'regexblock-unblock-error' => '$1 ला अनब्लॉक करण्यात त्रुटी.
कदाचित असा सदस्य अस्तित्वात नाही.',
	'regexblock-form-username' => 'आयपी अंकपत्ता किंवा सदस्यनाव:',
	'regexblock-form-reason' => 'कारण:',
	'regexblock-form-expiry' => 'समाप्ती:',
	'regexblock-form-match' => 'तंतोतंत जुळणी',
	'regexblock-form-account-block' => 'नवीन खाते तयार करणे अवरुद्ध करा',
	'regexblock-form-submit' => 'या सदस्याला ब्लॉक करा',
	'regexblock-block-log' => "'''$1''' या सदस्य नाव अथव आयपी अंकपत्त्याला ब्लॉक केलेले आहे.",
	'regexblock-block-success' => 'ब्लॉक यशस्वी',
	'regexblock-form-submit-empty' => 'ब्लॉक करण्यासाठी एक सदस्य नाव किंवा आयपी अंकपत्ता द्या.',
	'regexblock-form-submit-regex' => 'चुकीचे रेग्युलर एक्स्प्रेशन.',
	'regexblock-form-submit-expiry' => 'कृपया समाप्तीचा कालावधी द्या.',
	'regexblock-already-blocked' => '$1 ला अगोदरच ब्लॉक केलेले आहे.',
	'regexblock-stats-title' => 'रेजएक्स ब्लॉक सांख्यिकी',
	'regexblock-stats-username' => '$1 करिता',
	'regexblock-stats-times' => 'ला ब्लॉक केले',
	'regexblock-stats-logging' => 'अंकपत्त्यावरून नोंदणी करीत आहे',
	'regexblock-currently-blocked' => 'सध्या ब्लॉक केलेले पत्ते:',
	'regexblock-view-blocked' => 'ज्याने ब्लॉक केले त्याप्रमाणे यादी पहा:',
	'regexblock-view-all' => 'सर्व',
	'regexblock-view-go' => 'चला',
	'regexblock-view-match' => '(तंतोतंत जुळणी)',
	'regexblock-view-regex' => '(रेजएक्स जुळणी)',
	'regexblock-view-account' => '(खाते तयार करणे अवरुद्ध केले)',
	'regexblock-view-reason' => 'कारण: $1',
	'regexblock-view-reason-default' => 'सर्वसाधारण कारण',
	'regexblock-view-block-infinite' => 'कायमस्वरूपी ब्लॉक',
	'regexblock-view-block-temporary' => 'समाप्ती',
	'regexblock-view-block-expired' => 'संपल्याची वेळ',
	'regexblock-view-block-by' => 'ब्लॉक कर्ता',
	'regexblock-view-block-unblock' => 'अनब्लॉक',
	'regexblock-view-stats' => '(सांख्यिकी)',
	'regexblock-view-empty' => 'ब्लॉक केलेल्या सदस्यनाव तसेच आयपी अंकपत्त्यांची यादी रिकामी आहे.',
	'regexblock-view-time' => '$1 वर',
);

/** Maltese (Malti)
 * @author Roderick Mallia
 */
$messages['mt'] = array(
	'regexblock-already-blocked' => '"$1" diġà bblokkjat',
);

/** Nahuatl (Nāhuatl)
 * @author Fluence
 */
$messages['nah'] = array(
	'regexblock-form-reason' => 'Īxtlamatiliztli:',
	'regexblock-stats-username' => '$1 ītechcopa',
	'regexblock-view-all' => 'Mochīntīn',
	'regexblock-view-go' => 'Yāuh',
);

/** Dutch (Nederlands)
 * @author Meno25
 * @author SPQRobin
 * @author Siebrand
 * @author Tvdm
 */
$messages['nl'] = array(
	'regexblock' => 'RegexBlokkeren',
	'regexblock-desc' => 'Uitbreiding die wordt gebruikt voor het blokkeren van gebruikers en IP-adress met een reguliere expressie. Bevat zowel een blokkademechanisme als een [[Special:Regexblock|speciale pagina]] voor het toevoegen en beheren van blokkades',
	'regexblock-special-desc' => 'alternatieve gebruikersblokkade (via gebruikersnaam met reguliere expressies)',
	'regexblock-stat-desc' => "Biedt [[Special:Regexblockstats|blokkadestatistieken]] voor de uitbreiding ''regexblock''",
	'regexblock-page-title' => 'Namen blokkeren met reguliere uitdrukkingen',
	'regexblockstats' => 'Statistieken van regex-blokkeren',
	'regexblock-reason-ip' => 'Dit IP-adres is door u of door iemand met hetzelfde IP-adres geblokkeerd van bewerken door vandalisme of een andere reden. Als u gelooft dat dit een fout is, gelieve $1',
	'regexblock-reason-name' => 'Deze gebruikersnaam is geblokkeerd van bewerken door vandalisme of een andere reden. Als u gelooft dat dit een fout is, gelieve $1',
	'regexblock-reason-regex' => 'Deze gebruikersnaam is door een gebruiker met dezelfde naam geblokkeerd van bewerken door vandalisme of een andere reden. Gelieve een andere gebruikersnaam te kiezen of $1 over het probleem',
	'regexblock-help' => "Gebruik het onderstaande formulier om schrijftoegang voor een IP-adres of gebruiker te ontzeggen. Dit hoort eigenlijk alleen te gebeuren om vandalisme te voorkomen, en dient in overeenstemming te zijn met het beleid. ''U kunt zelfs gebruikers die nog niet bestaan blokkeren. Daarnaast worden ook gebruikers met gelijkende namen geblokkeerd. \"Test\" wordt samen met \"Test 2\", enzovoort geblokkeerd. U kunt ook een IP-adres blokkeren, wat betekent dat niemand van dat IP-adres pagina's kan bewerken. Opmerking: IP-adressen worden behandeld als gebruikersnamen bij het bepalen van blokkades. Als er geen reden is opgegeven, dan wordt er een standaard reden gebruikt.''",
	'regexblock-page-title-1' => 'IP-adres blokkeren met behulp van reguliere uitdrukkingen',
	'regexblock-unblock-success' => 'Het deblokkeren is gelukt',
	'regexblock-unblock-log' => "Gebruikersnaam of IP-adres '''$1''' zijn gedeblokkeerd.",
	'regexblock-unblock-error' => 'Een fout bij het deblokkeren van $1. Waarschijnlijk bestaat er geen gebruiker met die naam.',
	'regexblock-form-username' => 'IP-adres of gebruikersnaam:',
	'regexblock-form-reason' => 'Reden:',
	'regexblock-form-expiry' => 'Verloopt:',
	'regexblock-form-match' => 'Voldoet precies',
	'regexblock-form-account-block' => 'Het aanmaken van nieuwe gebruikers blokkeren',
	'regexblock-form-submit' => 'Deze gebruiker blokkeren',
	'regexblock-block-log' => "Gebruikersnaam of IP-adres '''$1''' is geblokkeerd.",
	'regexblock-block-success' => 'Het blokkeren is gelukt',
	'regexblock-form-submit-empty' => 'Geef een gebruikersnaam of een IP-adres om te blokkeren.',
	'regexblock-form-submit-regex' => 'Ongeldige reguliere uitdrukking.',
	'regexblock-form-submit-expiry' => 'Geef alstublieft een verlooptermijn op.',
	'regexblock-already-blocked' => '$1 is al geblokkeerd.',
	'regexblock-stats-title' => 'Regex-blokkeringsstatistieken',
	'regexblock-stats-username' => 'Voor $1',
	'regexblock-stats-times' => 'is geblokkeerd op',
	'regexblock-stats-logging' => 'aangemeld van IP-adres',
	'regexblock-currently-blocked' => 'Op dit moment geblokkeerde IP-adressen:',
	'regexblock-view-blocked' => 'Blokkades weergeven door:',
	'regexblock-view-all' => 'Alles',
	'regexblock-view-go' => 'Gaan',
	'regexblock-view-match' => '(voldoet precies)',
	'regexblock-view-regex' => '(voldoet aan regex)',
	'regexblock-view-account' => '(blokkade aanmaken gebruikers)',
	'regexblock-view-reason' => 'reden: $1',
	'regexblock-view-reason-default' => 'algemene reden',
	'regexblock-view-block-infinite' => 'permanente blokkade',
	'regexblock-view-block-temporary' => 'verloopt op',
	'regexblock-view-block-expired' => 'VERLOPEN op',
	'regexblock-view-block-by' => 'geblokkeerd door',
	'regexblock-view-block-unblock' => 'deblokkeren',
	'regexblock-view-stats' => '(statistieken)',
	'regexblock-view-empty' => 'De lijst van geblokkeerde namen en IP-adressen is leeg.',
	'regexblock-view-time' => 'op $1',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Jon Harald Søby
 */
$messages['nn'] = array(
	'regexblock-form-reason' => 'Årsak:',
	'regexblock-stats-username' => 'For $1',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'regexblock' => 'Regex-blokkering',
	'regexblock-desc' => 'Utvidelse som brukes for å blokkere brukernavn og IP-adresser ved hjelp av regulære uttrykk. Inneholder både blokkeringsmekanismen og en [[Special:Regexblock|spesialside]] for å legge til og endre blokkeringer',
	'regexblock-special-desc' => 'alternativ brukerblokkering (ved gitt navn, med hjelp av regulære uttrykk)',
	'regexblock-stat-desc' => "Viser [[Special:Regexblockstats|blokkeringsstatistikk]] for utvidelsen ''regexblock''",
	'regexblock-page-title' => 'Tittelblokkering ved hjelp av regulære uttrykk',
	'regexblockstats' => 'Regex-blokkeringsstatistikk',
	'regexblock-reason-ip' => 'Denne IP-adressen er hindret fra å redigere på grunn av hærverk eller annen forstyrrelse av deg eller noen andre som bruker samme IP-adresse. Om du mener dette er en feil, vennligst $1',
	'regexblock-reason-name' => 'Dette brukernavnet er hindret fra å redigere på grunn av hærverk eller annen forstyrrelse. Om du mener dette er en feil, vennligst $1',
	'regexblock-reason-regex' => 'Dette brukernavnet er forhindret fra redigering på grunn av hærverk eller annen forstyrrelse av en bruker med lignende navn. Vennligst opprett et annet brukernavn eller $1 om problemet.',
	'regexblock-help' => "Bruk skjemaet nedenfor for å blokkere visse IP-adresser eller brukernavn fra å redigere. Dette burde gjøres kun for å forhindre hærverk, og i følge med retningslinjene. ''Denne siden vil la deg blokkere også ikke-eksisterende brukere, og vil også blokkere brukere med navn som ligner det som blir gitt. F.eks. vil «Test» blokkeres sammen med «Test 2» osv. Du kan også blokkere fulle IP-adresser, hvilket betyr at ingen som logger på via disse kan redigere sider. Merk delvise IP-adresser vil behandles som brukernavn under blokkering. Om ingen begrunnelse oppgis vil en standardbegrunnelse bli brukt.''",
	'regexblock-page-title-1' => 'Blokker adresse ved hjelp av regulære uttrykk',
	'regexblock-unblock-success' => 'Avblokkering lyktes',
	'regexblock-unblock-log' => "Brukernavnet eller IP-adressen '''$1''' er blitt avblokkert",
	'regexblock-unblock-error' => 'Feil under avblokkering av $1. Det er trolig ingen brukere med det navnet.',
	'regexblock-form-username' => 'IP-adresse eller brukernavn:',
	'regexblock-form-reason' => 'Årsak:',
	'regexblock-form-expiry' => 'Varighet:',
	'regexblock-form-match' => 'Nøyaktig treff',
	'regexblock-form-account-block' => '{{int:ipbcreateaccount}}',
	'regexblock-form-submit' => 'Blokker denne brukeren',
	'regexblock-block-log' => "Brukeren '''$1''' har blitt blokkert.",
	'regexblock-block-success' => 'Blokkering lyktes',
	'regexblock-form-submit-empty' => 'Angi et brukernavn eller en IP-adresse å blokkere.',
	'regexblock-form-submit-regex' => 'Ugyldig regulært uttrykk',
	'regexblock-form-submit-expiry' => 'Angi en utløpstid.',
	'regexblock-already-blocked' => '«$1» er allerede blokkert',
	'regexblock-stats-title' => 'Statistikk for blokkering med regulære uttrykk',
	'regexblock-stats-username' => 'For $1',
	'regexblock-stats-times' => 'ble blokkert',
	'regexblock-stats-logging' => 'logging fra adresse',
	'regexblock-currently-blocked' => 'Nåværende blokkerte adresser:',
	'regexblock-view-blocked' => 'Vis de blokkerte etter:',
	'regexblock-view-all' => 'Alle',
	'regexblock-view-go' => '{{int:Go}}',
	'regexblock-view-match' => '(nøyaktig treff)',
	'regexblock-view-regex' => '(regex-treff)',
	'regexblock-view-account' => '(kontooppretting slått av)',
	'regexblock-view-reason' => 'begrunnelse: $1',
	'regexblock-view-reason-default' => 'generisk grunn',
	'regexblock-view-block-infinite' => 'permanent blokkering',
	'regexblock-view-block-temporary' => 'utgår',
	'regexblock-view-block-expired' => 'UTGIKK',
	'regexblock-view-block-by' => 'blokkert av',
	'regexblock-view-block-unblock' => 'avblokker',
	'regexblock-view-stats' => '(statistikk)',
	'regexblock-view-empty' => 'listen over blokkerte navn og adresser er tom.',
	'regexblock-view-time' => '$1',
);

/** Northern Sotho (Sesotho sa Leboa)
 * @author Mohau
 */
$messages['nso'] = array(
	'regexblock-form-username' => 'IP Atrese goba leina la mošomiši:',
	'regexblock-form-reason' => 'Lebaka:',
	'regexblock-block-log' => "Leina la mošomiši goba IP atrese '''$1''' e thibilwe.",
	'regexblock-form-submit-empty' => 'Efa leina la mošomiši goba IP atrese go thiba.',
	'regexblock-stats-username' => 'Ya $1',
	'regexblock-stats-times' => 'e thibilwe ka',
	'regexblock-view-all' => 'Kamoka',
	'regexblock-view-go' => 'Sepela',
	'regexblock-view-reason' => 'lebaka: $1',
	'regexblock-view-block-by' => 'thibilwe ke',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'regexblock' => 'Expressions regularas per blocar un utilizaire o una IP',
	'regexblock-desc' => "Extension utilizada per blocar d'utilizaires o d'adreças IP amb d'expressions regularas. Conten a l'encòp un mecanisme de blocatge e mai [[Special:Regexblock|una pagina]] podent apondre e gerir los blocatges",
	'regexblock-special-desc' => "Blocatge alternatiu d’un utilizaire (per un nom balhat o per d'expressions regularas)",
	'regexblock-stat-desc' => 'Aficha las [[Special:Regexblockstats|estatisticas de blocatges]] per l’extension regexblock.',
	'regexblock-page-title' => 'Blocatge d’un nom per una expression regulara',
	'regexblockstats' => 'Estatisticas suls blocatges per expressions regularas',
	'regexblock-reason-ip' => 'Aquesta adreça IP es apartat de tota edicon per causa de vandalisme o autres faches analògs per vos o qualqu’un d’autre partejant vòstra adreça IP. Se sètz persuadit(-ida) que s’agís d’una error, $1',
	'regexblock-reason-name' => 'Aqueste utilizaire es apartat de tota edicion per causa de vandalisme o autres faches analògs. Se sètz persuadit(-ida) que s’agís d’una error, $1.',
	'regexblock-reason-regex' => "Aqueste utilizaire es apartat de tota edicion per causa de vandalisme o autres faches analògs per un utilizaire qu'a un nom similar. Creatz un autre compte o $1 per senhalar lo problèma.",
	'regexblock-help' => "Utilizatz lo formulari çaijós per blocar l’accès en escritura una adreça IP o un nom d’utilizaire. Aquò deu èsser fach unicament per evitar tot vandalisme e conformadament a las règlas prescrichas sul projècte. ''Aquesta pagina vos autoriza quitament a blocar d'utilizaires pas enregistrats e permet tanben de blocar d'utilizaires que presentan de noms similars. Per exemple, « Tèst » serà blocada al meteis temps que « Tèst 2 » etc. Tanben podètz blocar d'adreças IP entièras, çò que significa que degun que trabalha pas dempuèi elas poirà pas editar de paginas. Nòta : d'adreças IP parcialas seràn consideradas coma de noms d’utilizaire al moment del blocatge. Se cap de motiu es pas indicat en comentari, un motiu per defaut serà indicat.''",
	'regexblock-page-title-1' => 'Blocatge d’una adreça utilizant una expression regulara',
	'regexblock-unblock-success' => 'Lo desblocatge a capitat',
	'regexblock-unblock-log' => "L’utilizaire o l’adreça IP '''$1''' es estat desblocat.",
	'regexblock-unblock-error' => 'Error de deblocatge de $1. L’utilizaire existís probablament pas.',
	'regexblock-form-username' => 'Adreça IP o Utilizaire :',
	'regexblock-form-reason' => 'Motiu :',
	'regexblock-form-expiry' => 'Expiracion :',
	'regexblock-form-match' => 'Tèrme exacte',
	'regexblock-form-account-block' => 'Interdire la creacion d’un compte novèl.',
	'regexblock-form-submit' => 'Blocar aqueste Utilizaire',
	'regexblock-block-log' => "L’Utilizaire o l’adreça IP '''$1''' es estat blocat.",
	'regexblock-block-success' => 'Lo blocatge a capitat',
	'regexblock-form-submit-empty' => 'Indicatz un nom d’utilizaire o una adreça IP de blocar.',
	'regexblock-form-submit-regex' => 'Expression regulara incorrècta.',
	'regexblock-form-submit-expiry' => 'Precisatz un periòde d’expiracion.',
	'regexblock-already-blocked' => '$1 ja es blocat.',
	'regexblock-stats-title' => 'Estatisticas dels blocatges per expressions regularas',
	'regexblock-stats-username' => 'Per $1',
	'regexblock-stats-times' => 'es estat blocat lo',
	'regexblock-stats-logging' => 'enregistrat dempuèi l’adreça',
	'regexblock-currently-blocked' => 'Adreças actualament blocadas :',
	'regexblock-view-blocked' => 'Veire los blocatges per :',
	'regexblock-view-all' => 'Totes',
	'regexblock-view-go' => 'Amodar',
	'regexblock-view-match' => '(tèrme exacte)',
	'regexblock-view-regex' => '(expression regulara)',
	'regexblock-view-account' => '(creacion dels comptes blocada)',
	'regexblock-view-reason' => 'motiu : $1',
	'regexblock-view-reason-default' => 'cap de motiu indicat',
	'regexblock-view-block-infinite' => 'blocatge permanent',
	'regexblock-view-block-temporary' => 'expira lo',
	'regexblock-view-block-expired' => 'EXPIRAT lo',
	'regexblock-view-block-by' => 'blocat per',
	'regexblock-view-block-unblock' => 'desblocar',
	'regexblock-view-stats' => '(estatisticas)',
	'regexblock-view-empty' => 'La lista dels utilizaires e de las adreças IP blocats es voida.',
	'regexblock-view-time' => 'lo $1',
);

/** Punjabi (ਪੰਜਾਬੀ)
 * @author Gman124
 */
$messages['pa'] = array(
	'regexblock-view-all' => 'ਸਭ',
);

/** Polish (Polski)
 * @author Maikking
 * @author McMonster
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'regexblock' => 'BlokadaRegEx',
	'regexblock-desc' => 'Rozszerzenie umożliwiające blokowanie nazw użytkowników oraz adresów IP opisanych wyrażeniami regularnymi. Zawiera mechanizm blokujący oraz [[Special:Regexblock|stronę specjalną]] dla dodawania i zarządzania blokadami',
	'regexblock-reason-ip' => 'Ten adres IP został zablokowany z powodu wandalizmu lub innego naruszenia zasad przez Ciebie lub przez kogoś, z kim współdzielisz ten adres IP.
Jeżeli uważasz, że nastąpiła pomyłka, $1',
	'regexblock-reason-name' => 'Nazwa użytkownika została zablokowana z powodu wandalizmu lub innego naruszenia zasad.
Jeżeli uważasz, że nastąpiła pomyłka, $1',
	'regexblock-reason-regex' => 'Nazwa użytkownika została zablokowana z powodu wandalizmu lub innego naruszenia zasad, wykonanych przez użytkownika o bardzo podobnej nazwie.
Utwórz nową nazwę użytkownika lub skontaktuj się z $1 w celu rozwiązania problemu.',
	'regexblock-unblock-success' => 'Odblokowano',
	'regexblock-unblock-log' => "Użytkownik lub adres IP '''$1''' został odblokowany.",
	'regexblock-unblock-error' => 'Błąd przy odblokowaniu $1.
Prawdopodobnie brak takiego użytkownika.',
	'regexblock-form-username' => 'Adres IP lub nazwa użytkownika:',
	'regexblock-form-reason' => 'Powód:',
	'regexblock-form-expiry' => 'Czas blokady:',
	'regexblock-form-account-block' => 'Zablokuj możliwość tworzenia nowych kont',
	'regexblock-form-submit' => 'Zablokuj użytkownika',
	'regexblock-block-log' => "Nazwa użytkownika lub adres IP '''$1''' zostały zablokowane.",
	'regexblock-block-success' => 'Pomyślnie zablokowano',
	'regexblock-form-submit-regex' => 'Nieprawidłowe wyrażenie regularne',
	'regexblock-already-blocked' => '$1 jest już zablokowany',
	'regexblock-stats-username' => 'Dla $1',
	'regexblock-currently-blocked' => 'Zablokowane adresy:',
	'regexblock-view-blocked' => 'Pokaż zablokowanych, posortowanych według',
	'regexblock-view-block-by' => 'zablokowany przez',
	'regexblock-view-block-unblock' => 'odblokuj',
	'regexblock-view-stats' => '(statystyki)',
	'regexblock-view-empty' => 'Lista zablokowanych nazw i adresów jest pusta.',
	'regexblock-view-time' => '$1',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'regexblock-form-reason' => 'سبب:',
	'regexblock-view-all' => 'ټول',
	'regexblock-view-go' => 'ورځه',
	'regexblock-view-reason' => 'سبب: $1',
);

/** Portuguese (Português)
 * @author Lijealso
 * @author Malafaya
 */
$messages['pt'] = array(
	'regexblock-desc' => 'Extensão usada para bloquear nomes de usuário ou endereços IP através de expressões regulares. Contém o mecanismo e uma [[Special:Regexblock|página especial]] para adicionar/gerir os bloqueios',
	'regexblock-reason-name' => 'Este nome de usuário está impedido de editar devido a vandalismo ou outro tipo de disrupção. Se julgar tratar-se de um erro, por favor $1',
	'regexblock-page-title-1' => 'Bloquear endereço utilizando expressões regulares',
	'regexblock-unblock-success' => 'Desbloqueio bem sucedido',
	'regexblock-unblock-log' => "O nome de utilizador ou endereço IP '''$1''' foi desbloqueado.",
	'regexblock-unblock-error' => 'Erro ao desbloquear $1. Provavelmente não existe esse usuário.',
	'regexblock-form-username' => 'Endereço IP ou nome de utilizador:',
	'regexblock-form-reason' => 'Motivo:',
	'regexblock-form-account-block' => 'Bloquear criação de novas contas',
	'regexblock-form-submit' => 'Bloquear este Utilizador',
	'regexblock-block-log' => "Nome de usuário ou endereço IP '''$1''' foi bloqueado.",
	'regexblock-block-success' => 'Bloqueio com sucesso',
	'regexblock-form-submit-empty' => 'Forneça um nome de usuário ou um endereço IP para bloquear.',
	'regexblock-form-submit-regex' => 'Expressão regular inválida.',
	'regexblock-form-submit-expiry' => 'Por favor, seleccione um período de expiração.',
	'regexblock-already-blocked' => '$1 já está bloqueada.',
	'regexblock-stats-username' => 'Para $1',
	'regexblock-stats-times' => 'foi bloqueado em',
	'regexblock-stats-logging' => 'Acedendo através do endereço',
	'regexblock-currently-blocked' => 'Endereços actualmente bloqueados:',
	'regexblock-view-blocked' => 'Ver bloqueios por:',
	'regexblock-view-all' => 'Todos',
	'regexblock-view-go' => 'Ir',
	'regexblock-view-account' => '(bloqueio de criação de conta)',
	'regexblock-view-reason' => 'motivo: $1',
	'regexblock-view-reason-default' => 'motivo genérico',
	'regexblock-view-block-infinite' => 'bloqueio permanente',
	'regexblock-view-block-temporary' => 'expira em',
	'regexblock-view-block-expired' => 'EXPIRADO em',
	'regexblock-view-block-by' => 'bloqueado por',
	'regexblock-view-block-unblock' => 'desbloquear',
	'regexblock-view-stats' => '(estatísticas)',
	'regexblock-view-empty' => 'Esta lista de nomes e endereços bloqueados está vazia.',
	'regexblock-view-time' => 'em $1',
);

/** Tarifit (Tarifit)
 * @author Jose77
 */
$messages['rif'] = array(
	'regexblock-view-all' => 'Maṛṛa',
	'regexblock-view-go' => 'Raḥ ɣa',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 */
$messages['ro'] = array(
	'regexblock-form-reason' => 'Motiv:',
	'regexblock-already-blocked' => '$1 este deja blocat.',
	'regexblock-stats-username' => 'Pentru $1',
);

/** Russian (Русский)
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'regexblock' => 'RegexBlock',
	'regexblock-desc' => 'Расширение, использующееся для блокировки имён участников и IP-адресов с помощью регулярных выражений. Содержит механизм блокирования и [[Special:Regexblock|служебную страницу]] для добавления и управления блокировками',
	'regexblock-special-desc' => 'альтернативная блокировка участника (по имени, с помощью регулярных выражений)',
	'regexblock-stat-desc' => 'Показывает [[Special:Regexblockstats|статистику блокировок]] для расширения RegexBlock',
	'regexblock-page-title' => 'Блокирование имени по регулярному выражению',
	'regexblockstats' => 'Статистика блокировок по регулярным выражениям',
	'regexblock-reason-ip' => 'Этот IP-адрес отстранён от редактирования из-за вандализма или других нарушений, которые могли совершить вы или кто-то другой, использующий такой же IP-адрес.
Если вы считаете, что это ошибка, пожалуйста, $1',
	'regexblock-reason-name' => 'Это имя участника отстранёно от редактирования из-за вандализма или других нарушений.
Если вы считаете, что это ошибка, пожалуйста, $1',
	'regexblock-reason-regex' => 'Это имя участника отстранёно от редактирования из-за вандализма или других нарушений, которые совершил участник с похожим именем.
Пожалуйста, создайте другое имя участника или $1 о проблеме',
	'regexblock-help' => "Используйте приведённую ниже форму для запрета возможности записи для определённого IP-адреса или имени участника.
Это следует делать только для предотвращения вандализма, руководствуясь правилами.
''Эта страница позволит вам заблокировать даже несуществующих участников, а также заблокирует участников с похожими именами, т. е. вместе с «Test» будет заблокирован и «Test 2» и т. д.
Вы также можете заблокировать полный IP-адрес, что означает, что его больше нельзя быдет использовать для редактирования страниц.
Замечание: частично IP-адреса будут очищены именами участников при определённых блокировках.
Если не указана причина, то будет использовано общее описание по умолчанию.''",
	'regexblock-page-title-1' => 'Блокировка адресов с помощью регулярных выражений',
	'regexblock-unblock-success' => 'Разблокировка выполнена успешно',
	'regexblock-unblock-log' => "Имя участника или IP-адрес '''$1''' заблокирован.",
	'regexblock-unblock-error' => 'Ошибка разблокировки $1.
Возможно, такого участника не существует.',
	'regexblock-form-username' => 'IP-адрес или имя участника:',
	'regexblock-form-reason' => 'Причина:',
	'regexblock-form-expiry' => 'Истекает:',
	'regexblock-form-match' => 'Точное соответствие',
	'regexblock-form-account-block' => 'Запретить создание новых учётных записей',
	'regexblock-form-submit' => 'Заблокировать этого участника',
	'regexblock-block-log' => "Имя участника или IP-адрес '''$1''' заблокирован.",
	'regexblock-block-success' => 'Блокировка выполнена успешно',
	'regexblock-form-submit-empty' => 'Укажите имя участника или IP-адрес для блокировки.',
	'regexblock-form-submit-regex' => 'Ошибочное регулярное выражение.',
	'regexblock-form-submit-expiry' => 'Пожалуйста, укажите время действия.',
	'regexblock-already-blocked' => '$1 уже заблокирован.',
	'regexblock-stats-title' => 'Статистика RegexBlock',
	'regexblock-stats-username' => 'Для $1',
	'regexblock-stats-times' => 'был заблокирован',
	'regexblock-stats-logging' => 'вошёл с адреса',
	'regexblock-currently-blocked' => 'Заблокированные сейчас адреса:',
	'regexblock-view-blocked' => 'Просмотреть заблокированных:',
	'regexblock-view-all' => 'Все',
	'regexblock-view-go' => 'Выполнить',
	'regexblock-view-match' => '(точное соответствие)',
	'regexblock-view-regex' => '(соответствие рег. выр.)',
	'regexblock-view-account' => '(запрет создания учётных записей)',
	'regexblock-view-reason' => 'причина: $1',
	'regexblock-view-reason-default' => 'общая причина',
	'regexblock-view-block-infinite' => 'бессрочная блокировка',
	'regexblock-view-block-temporary' => 'истекает',
	'regexblock-view-block-expired' => 'ИСТЕКЛА',
	'regexblock-view-block-by' => 'заблокирован',
	'regexblock-view-block-unblock' => 'разблокировать',
	'regexblock-view-stats' => '(статистика)',
	'regexblock-view-empty' => 'Список заблокированных имён и адресов пуст.',
	'regexblock-view-time' => '$1',
);

/** Sassaresu (Sassaresu)
 * @author Felis
 */
$messages['sdc'] = array(
	'regexblock-already-blocked' => '$1 è già broccaddu.',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'regexblock' => 'RegexBlock',
	'regexblock-desc' => 'Rozšírenie na blokovanie používateľských mien a IP adries na základe regulárnych výrazov. Obsahuje mechanizmus blokovania a [[Special:Regexblock|špeciálnu stránku]] na pridávanie a správu blokovaní',
	'regexblock-special-desc' => 'alternatívny spôsob blokovania používateľov (podľa mena, pomocou regulárnych výrazov)',
	'regexblock-stat-desc' => 'Zobrazuje [[Special:Regexblockstats|štatistiku blokovaní]] pomocou rozšírenia regexblock',
	'regexblock-page-title' => 'Blokovanie mena na základe regulárneho výrazu',
	'regexblockstats' => 'Štatistika regex blokovaní',
	'regexblock-reason-ip' => 'Tejto IP adrese bolo zakázané upravovanie kvôli vandalizmu alebo inej rušivej činnosti, ktorú ste vykonával vy alebo niekto, s kým máte spoločnú vašu IP adresu. Ak veríte, že toto je omyl, prosím $1',
	'regexblock-reason-name' => 'Tomuto používateľskému menu bolo zakázané upravovanie kvôli vandalizmu alebo inej rušivej činnosti. Ak veríte, že toto je omyl, prosím $1',
	'regexblock-reason-regex' => 'Tomuto používateľskému menu bolo zakázané upravovanie kvôli vandalizmu alebo inej rušivej činnosti používateľa s podobným menom. Prosím, vytvorte si alternatívny používateľský účet alebo o probléme $1',
	'regexblock-help' => "Použite tento formulár na zablokovanie úprav z určitej IP adresy alebo používateľského mena. Toto by sa malo využívať iba na predchádzanie vandalizmu a v súlade so zásadami blokovania. ''Táto stránka vým umožní zablokovať aj momentálne neexistujúcich používateľov a používateľov s podobnými menami ako bolo zadané, t.j. okrem « Test » bude zablokovaný aj « Test 2 » atď. Môžete tiež zablokovať celé IP adresy, čo znamená, že nikto, kto z nich pristupuje nebude môcť upravovať stránky. Pozn.: čiastočné IP adresy budú považované za používateľské mená. Ak nebude uvedený dôvod, použije sa štandardný všeobecný dôvod.''",
	'regexblock-page-title-1' => 'Zablokovať adresu na základe regulárneho výrazu',
	'regexblock-unblock-success' => 'Odblokovanie úspešné',
	'regexblock-unblock-log' => "Používateľské meno alebo IP adresa '''$1''' bolo odblokované",
	'regexblock-unblock-error' => 'Chyba pri odblokovaní $1. Taký používateľ pravdepodobne neexistuje.',
	'regexblock-form-username' => 'IP adresa alebo meno používateľa:',
	'regexblock-form-reason' => 'Dôvod:',
	'regexblock-form-expiry' => 'Vyprší:',
	'regexblock-form-match' => 'Presná zhoda',
	'regexblock-form-account-block' => 'Zablokovať možnosť tvorby nových účtov',
	'regexblock-form-submit' => 'Zablokovať tohto používateľa',
	'regexblock-block-log' => "Používateľské meno alebo IP adresa '''$1''' bolo zablokované.",
	'regexblock-block-success' => 'Blokovanie úspešné',
	'regexblock-form-submit-empty' => 'Zadajte používateľské meno alebo IP adresu, ktorá sa má zablokovať.',
	'regexblock-form-submit-regex' => 'Neplatný regulárny výraz.',
	'regexblock-form-submit-expiry' => 'Prosím zadajte, kedy má blokovanie skončiť.',
	'regexblock-already-blocked' => '$1 je už zablokovaný.',
	'regexblock-stats-title' => 'Štatistiky regex blokovaní',
	'regexblock-stats-username' => 'Pre $1',
	'regexblock-stats-times' => 'bol zablokovaný',
	'regexblock-stats-logging' => 'pristupujúci z adresy',
	'regexblock-currently-blocked' => 'Momentálne zablokované adresy:',
	'regexblock-view-blocked' => 'Zobraziť blokovania od:',
	'regexblock-view-all' => 'Všetci',
	'regexblock-view-go' => 'Vykonať',
	'regexblock-view-match' => '(presná zhoda)',
	'regexblock-view-regex' => '(vyhovuje reg. výrazu)',
	'regexblock-view-account' => '(blokovanie tvorby účtov)',
	'regexblock-view-reason' => 'dôvod: $1',
	'regexblock-view-reason-default' => 'všeobecný dôvod',
	'regexblock-view-block-infinite' => 'trvalé blokovanie',
	'regexblock-view-block-temporary' => 'vyprší',
	'regexblock-view-block-expired' => 'VYPRŠALO',
	'regexblock-view-block-by' => 'zablokoval ho',
	'regexblock-view-block-unblock' => 'odblokovať',
	'regexblock-view-stats' => '(štatistiky)',
	'regexblock-view-empty' => 'Zoznam blokovaných mien a IP adries je prázdny.',
	'regexblock-view-time' => '$1',
);

/** Serbian Cyrillic ekavian (ћирилица)
 * @author Sasa Stefanovic
 */
$messages['sr-ec'] = array(
	'regexblock-form-reason' => 'Разлог:',
);

/** Sundanese (Basa Sunda)
 * @author Irwangatot
 */
$messages['su'] = array(
	'regexblock-form-reason' => 'Alesan:',
	'regexblock-already-blocked' => '$1 geus dipeungpeuk.',
);

/** Swedish (Svenska)
 * @author M.M.S.
 */
$messages['sv'] = array(
	'regexblock' => 'Regex-blockering',
	'regexblock-desc' => 'Tillägg som används för att blockera användarnamn och IP-adresser med hjälp av reguljära uttryck. Innehåller både blockeringsmekansimen och en [[Special:Regexblock|specialsida]] för att lägga till och ändra blockeringar',
	'regexblock-special-desc' => 'alternativ användarblockering (med angivet namn, med hjälp av reguljära uttryck)',
	'regexblock-stat-desc' => "Visar [[Special:Regexblockstats|blockeringstatistik]] för tillägget ''regexblock''",
	'regexblock-page-title' => 'Blockering med hjälp av reguljära uttryck',
	'regexblockstats' => 'Regex-blockeringsstatistik',
	'regexblock-reason-ip' => 'Den här IP-adressen är hindrad från att redigera på grund av vandalism eller annan förstörelse av dig eller någon annan som använder samma IP-adress.
Om du menar att detta är ett fel, var god $1',
	'regexblock-reason-name' => 'Det här användarnamnet är hindrad från att redigera på grund av vandalism eller annan förstörelse.
Om du menar att detta är ett fel, var god $1',
	'regexblock-reason-regex' => 'Den här IP-adressen är hindrad från att redigera på grund av vandalism eller annan förstörelse av en användare med liknande namn. 
Var god skapa ett annat användarnamn eller $1 om problemet.',
	'regexblock-help' => 'Använd formuläret nedan för att blockera vissa IP-adresser eller användarnamn från att redigera. 
Det här borde endast göras för att förhindra vandalism, i följd med riktlinjerna.
\'\'Den här sidan låter dig även blockera ej existerande användare, och kommer också blockera användare med liknande namn. t.ex. kommer "Test" blockeras samtidigt med "Test 2" o.s.v.
Du kan också blockera fulla IP-adresser, vilket betyder att ingen som loggar in via dessa kan redigera sidor.
Notera att delvisa IP-adresser kommer behandlas som användarnamn under blockering.
Om ingen beskrivning uppges kommer en standardbeskrivning användas.\'\'',
	'regexblock-page-title-1' => 'Blockera adress med hjälp av reguljära uttryck',
	'regexblock-unblock-success' => 'Avblockering lyckades',
	'regexblock-unblock-log' => "Användarnamnet eller IP-adressen '''$1''' har avblockerats",
	'regexblock-unblock-error' => 'Fel under avblockering av $1.
Troligen så finns det ingen användare med det namnet.',
	'regexblock-form-username' => 'IP adress eller användarnamn:',
	'regexblock-form-reason' => 'Anledning:',
	'regexblock-form-expiry' => 'Utgång:',
	'regexblock-form-match' => 'Exakt träff',
	'regexblock-form-account-block' => 'Blockera skapandet av nya konton',
	'regexblock-form-submit' => 'Blockera den här användaren',
	'regexblock-block-log' => "Användarnamnet eller IP-adressen '''$1''' har blockerats.",
	'regexblock-block-success' => 'Blockering lyckades',
	'regexblock-form-submit-empty' => 'Ange ett användarnamn eller en IP-adress att blockera.',
	'regexblock-form-submit-regex' => 'Ogiltigt reguljärt uttryck',
	'regexblock-form-submit-expiry' => 'Var god ange en utgångstid.',
	'regexblock-already-blocked' => '"$1" är redan blockerad.',
	'regexblock-stats-title' => 'Regex-blockeringsstatistik',
	'regexblock-stats-username' => 'För $1',
	'regexblock-stats-times' => 'blev blockerad',
	'regexblock-stats-logging' => 'loggar in från adressen',
	'regexblock-currently-blocked' => 'Nuvarande blockerade adresser:',
	'regexblock-view-blocked' => 'Visa de blockerade efter:',
	'regexblock-view-all' => 'Alla',
	'regexblock-view-go' => 'Gå',
	'regexblock-view-match' => '(exakt träff)',
	'regexblock-view-regex' => '(regex-träff)',
	'regexblock-view-account' => '(kontoskapande blockerat)',
	'regexblock-view-reason' => 'anledning: $1',
	'regexblock-view-reason-default' => 'generisk grund',
	'regexblock-view-block-infinite' => 'permanent blockering',
	'regexblock-view-block-temporary' => 'utgår på',
	'regexblock-view-block-expired' => 'GICK UT på',
	'regexblock-view-block-by' => 'blockerad av',
	'regexblock-view-block-unblock' => 'avblockera',
	'regexblock-view-stats' => '(statistik)',
	'regexblock-view-empty' => 'Listan över blockerade namn och adresser är tom.',
	'regexblock-view-time' => 'på $1',
);

/** Silesian (Ślůnski)
 * @author Herr Kriss
 */
$messages['szl'] = array(
	'regexblock-form-reason' => 'Čymu:',
	'regexblock-form-expiry' => 'Wygaso:',
);

/** Tamil (தமிழ்)
 * @author Trengarasu
 */
$messages['ta'] = array(
	'regexblock-view-all' => 'அனைத்து',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'regexblock-unblock-success' => 'నిరోధపు ఎత్తివేత విజయవంతమైంది',
	'regexblock-unblock-log' => "'''$1''' అనే వాడుకరి పేరు లేదా ఐపీ చిరునామాపై నిరోధం ఎత్తివేసారు.",
	'regexblock-form-username' => 'IP చిరునామా లేదా వాడుకరి పేరు:',
	'regexblock-form-reason' => 'కారణం:',
	'regexblock-form-expiry' => 'కాలపరిమితి:',
	'regexblock-form-account-block' => 'కొత్త ఖాతాలు సృష్టించడం నిరోధించు',
	'regexblock-form-submit' => 'ఈ వాడుకరిని నిరోధించండి',
	'regexblock-block-log' => "'''$1''' అనే వాడుకరిపేరు లేదా ఐపీ చిరునామాని నిరోధించాం.",
	'regexblock-block-success' => 'నిరోధం విజయవంతమైంది',
	'regexblock-form-submit-empty' => 'నిరోధించాల్సిన వాడుకరి పేరు లేదా ఐపీ చిరునామా ఇవ్వండి.',
	'regexblock-form-submit-regex' => 'తప్పుడు రెగ్యులర్ ఎక్స్&zwnj;ప్రెషన్.',
	'regexblock-form-submit-expiry' => 'దయచేసి ఓ కాలపరిమితి ఇవ్వండి.',
	'regexblock-already-blocked' => '$1ని ఇదివరకే నిషేధించారు.',
	'regexblock-stats-username' => '$1 కొరకు',
	'regexblock-currently-blocked' => 'ప్రస్తుతం నిరోధంలో ఉన్న చిరునామాలు:',
	'regexblock-view-all' => 'అన్నీ',
	'regexblock-view-go' => 'వెళ్ళు',
	'regexblock-view-account' => '(ఖాతా సృష్టింపు నిరోధం)',
	'regexblock-view-reason' => 'కారణం: $1',
	'regexblock-view-reason-default' => 'సాధారణ కారణం',
	'regexblock-view-block-infinite' => 'శాశ్వత నిరోధం',
	'regexblock-view-block-temporary' => 'కాలంచెల్లు తేదీ',
	'regexblock-view-block-expired' => 'కాలంచెల్లింది',
	'regexblock-view-block-by' => 'నిరోధించినది',
	'regexblock-view-block-unblock' => 'నిరోధం ఎత్తివేయండి',
	'regexblock-view-stats' => '(గణాంకాలు)',
	'regexblock-view-empty' => 'నిరోధించిన పేర్లు మరియు చిరునామాల జాబితా ఖాళీగా ఉంది.',
	'regexblock-view-time' => '$1 నాడు',
);

/** Tetum (Tetun)
 * @author MF-Warburg
 */
$messages['tet'] = array(
	'regexblock-stats-username' => 'Ba $1',
	'regexblock-view-all' => 'Hotu',
	'regexblock-view-go' => 'Bá',
);

/** Tajik (Cyrillic) (Тоҷикӣ (Cyrillic))
 * @author Ibrahim
 */
$messages['tg-cyrl'] = array(
	'regexblock-unblock-success' => 'Боз кардан аз бастан муваффақ шуд',
	'regexblock-unblock-log' => "Номи корбарӣ ё нишонаи IP '''$1''' аз бастан боз шуд.",
	'regexblock-unblock-error' => 'Хато дар боз кардани $1.
Эҳтимолан чунин корбаре нест.',
	'regexblock-form-username' => 'Нишонаи IP ё номи корбарӣ:',
	'regexblock-form-reason' => 'Далел:',
	'regexblock-form-match' => 'Мутобиқати дақиқ',
	'regexblock-form-account-block' => 'Эҷоди бастани ҳисобҳои ҷадид',
	'regexblock-form-submit' => 'Бастани ин Корбар',
	'regexblock-block-log' => "Номи корбарӣ ё нишонаи '''$1''' баста шуд.",
	'regexblock-block-success' => 'Бастан муваффақ шуд',
	'regexblock-form-submit-empty' => 'Барои бастан номи корбарӣ ё нишонаи IP диҳед.',
	'regexblock-form-submit-regex' => 'Ибораи оддии номӯътабар.',
	'regexblock-form-submit-expiry' => 'Лутфан давраи ба хотимарасиро мушаххас кунед.',
	'regexblock-already-blocked' => '"$1" аллакай баста шудааст.',
	'regexblock-stats-title' => 'Омори Бастани Regex',
	'regexblock-stats-username' => 'Барои $1',
	'regexblock-stats-times' => 'басташуда буд дар',
	'regexblock-stats-logging' => 'вурудшави аз нишонаи',
	'regexblock-currently-blocked' => 'Нишонаҳои басташудаи кунунӣ:',
	'regexblock-view-all' => 'Ҳама',
	'regexblock-view-go' => 'Бирав',
	'regexblock-view-match' => '(мутобиқати дақиқ)',
	'regexblock-view-account' => '(бастани эҷоди ҳисоби ҷадид)',
	'regexblock-view-reason' => 'далел: $1',
	'regexblock-view-reason-default' => 'далели умумӣ',
	'regexblock-view-block-infinite' => 'бастани доимӣ',
	'regexblock-view-block-temporary' => 'сипарӣ мешавад дар',
	'regexblock-view-block-by' => 'баста шуд тавассути',
	'regexblock-view-block-unblock' => 'боз кардан',
	'regexblock-view-stats' => '(омор)',
	'regexblock-view-empty' => 'Феҳристи номҳо ва нишонаҳои баста шуда холӣ аст.',
	'regexblock-view-time' => 'дар $1',
);

/** Turkish (Türkçe)
 * @author Suelnur
 */
$messages['tr'] = array(
	'regexblock-form-reason' => 'Neden:',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'regexblock' => 'Cấm bằng biểu thức chính quy',
	'regexblock-desc' => 'Gói mở rộng dùng để cấm những tên người dùng và địa chỉ IP bằng biểu thức chính quy. Có cả cơ chế cấm và một [[Special:Regexblock|trang đặc biệt]] để thêm/quản lý việc cấm',
	'regexblock-special-desc' => 'thay thế cấm thành viên (theo tên cho trước, sử dụng biểu thức chính quy)',
	'regexblock-stat-desc' => 'Hiển thị [[Special:Regexblockstats|thống kê cấm]] đối với gói mở rộng regexblock',
	'regexblock-page-title' => 'Cấm tên theo Biểu thức chính quy',
	'regexblockstats' => 'Thống kê cấm Regex',
	'regexblock-reason-ip' => 'Địa chỉ IP này bị chặn không được sửa đổi do phá hoại hoặc vi phạm của bạn hoặc bởi ai đó dùng chung địa chỉ IP với bạn.
Nếu bạn tin rằng đây là nhầm lẫn, xin hãy $1',
	'regexblock-reason-name' => 'Tên người dùng này bị chặn không được sửa đổi do phá hoại hoặc hành vi vi phạm khác.
Nếu bạn tin rằng đây là nhầm lẫn, xin hãy $1',
	'regexblock-reason-regex' => 'Tên người dùng này bị chặn không được sửa đổi do phá hoại hoặc hành vi vi phạm khác của một thành viên có tên tương tự như thế này.
Xin hãy tạo một tên người dùng thay thế hoặc $1 về vấn đề này',
	'regexblock-help' => "Hãy dùng mẫu dưới đây để cấm quyền viết bài của một địa chỉ IP hoặc tên người dùng cụ thể.
Điều này chỉ nên thực hiện để ngăn chặn phá hoại, và theo đúng với quy định.
''Trang này sẽ cho phép bạn thậm chí cấm cả những thành viên không tồn tại, và cũng sẽ cấm những thành viên có tên tương tự với tên đưa ra, nghĩa là « Test » sẽ bị cấm cùng với « Test 2 » v.v.
Bạn cũng có thể cấm các dải địa chỉ IP đầy đủ, nghĩa là không ai đăng nhập từ các IP đó có thể thực hiện sửa đổi trang.
Chú ý: các địa chỉ IP bán phần sẽ được đối xử như tên người dùng trong việc xác định cấm.
Nếu không đưa ra lý do gì, một lý do chung chung mặc định sẽ được sử dụng.''",
	'regexblock-page-title-1' => 'Cấm địa chỉ sử dụng biểu thức chính quy',
	'regexblock-unblock-success' => 'Bỏ cấm thành công',
	'regexblock-unblock-log' => "Tên người dùng hoặc địa chỉ IP '''$1''' đã được bỏ cấm.",
	'regexblock-unblock-error' => 'Lỗi khi bỏ cấm $1.
Có thể không có thành viên nào như vậy.',
	'regexblock-form-username' => 'Địa chỉ IP hoặc tên người dùng:',
	'regexblock-form-reason' => 'Lý do:',
	'regexblock-form-expiry' => 'Thời hạn:',
	'regexblock-form-match' => 'Khớp chính xác',
	'regexblock-form-account-block' => 'Cấm mở tài khoản mới',
	'regexblock-form-submit' => 'Cấm người dùng này',
	'regexblock-block-log' => "Tên người dùng hoặc địa chỉ IP '''$1''' đã bị cấm.",
	'regexblock-block-success' => 'Cấm thành công',
	'regexblock-form-submit-empty' => 'Cung cấp một tên người dùng hoặc một địa chỉ IP để cấm.',
	'regexblock-form-submit-regex' => 'Biểu thức chính quy không hợp lệ.',
	'regexblock-form-submit-expiry' => 'Xin xác định thời hạn cấm.',
	'regexblock-already-blocked' => '“$1” đã bị cấm rồi.',
	'regexblock-stats-title' => 'Thống kê cấm regex',
	'regexblock-stats-username' => 'Đối với $1',
	'regexblock-stats-times' => 'bị cấm vào',
	'regexblock-stats-logging' => 'đăng nhập từ địa chỉ',
	'regexblock-currently-blocked' => 'Các địa chỉ hiện đang bị cấm:',
	'regexblock-view-blocked' => 'Xem những lần cấm do:',
	'regexblock-view-all' => 'Tất cả',
	'regexblock-view-go' => 'Xem',
	'regexblock-view-match' => '(khớp chính xác)',
	'regexblock-view-regex' => '(khớp chính xác)',
	'regexblock-view-account' => '(cấm mở tài khoản)',
	'regexblock-view-reason' => 'lý do: $1',
	'regexblock-view-reason-default' => 'lý do chung chung',
	'regexblock-view-block-infinite' => 'cấm vĩnh viễn',
	'regexblock-view-block-temporary' => 'hết hạn vào',
	'regexblock-view-block-expired' => 'HẾT HẠN vào',
	'regexblock-view-block-by' => 'bị cấm bởi',
	'regexblock-view-block-unblock' => 'bỏ cấm',
	'regexblock-view-stats' => '(thống kê)',
	'regexblock-view-empty' => 'Danh sách các tên và địa chỉ bị cấm đang trống.',
	'regexblock-view-time' => 'vào $1',
);

/** Volapük (Volapük)
 * @author Malafaya
 */
$messages['vo'] = array(
	'regexblock-form-reason' => 'Kod:',
	'regexblock-view-all' => 'Valik',
);

