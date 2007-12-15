<?php
/**
 * Internationalisation file for extension PovWatch.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'povwatch' => 'PovWatch',
	'povwatch_no_session' => 'Error: Could not submit form due to a loss of session data.',
	'povwatch_not_allowed_push' => 'You are not a PovWatch admin, you can\'t push articles to watchlists.',
	'povwatch_already_subscribed' => 'You are already subscribed to PovWatch',
	'povwatch_subscribed' => 'You are now subscribed to PovWatch',
	'povwatch_not_subscribed' => 'You are not subscribed to PovWatch, so you can\'t unsubscribe.',
	'povwatch_unsubscribed' => 'You have now unsubscribed from PovWatch',
	'povwatch_invalid_title' => 'The title specified was invalid',
	'povwatch_pushed' => '[[$1]] has successfully been pushed to $2 user watchlist(s)',
	'povwatch_intro' => 'PovWatch is a service which allows contentious articles to be discreetly pushed on to the watchlists of subscribing administrators.

A log of recent watchlist pushes is available at [[Special:PovWatch/log]].',
	'povwatch_subscriber_list' => 'A [[Special:PovWatch/subscribers|list of subscribers]] is available.',
	'povwatch_subscriber_list_intro' => '<strong>Subscriber list</strong>',
	'povwatch_not_allowed_subscribers' => 'You are not allowed to view the PovWatch subscriber list.',
	'povwatch_unknown_subpage' => 'Unknown subpage.',
	'povwatch_push' => 'Push',
	'povwatch_push_intro' => 'Use the form below to push articles on to the watchlists of subscribing users. Please be careful typing the title: even non-existent titles can be added, and there is no way to remove a title once it has been pushed out.',
	'povwatch_title' => 'Title:',
	'povwatch_comment' => 'Log comment:',
	'povwatch_no_log' => 'There are no log entries.',
	'povwatch_no_subscribers' => 'There are no subscribers.',
	'povwatch_unsubscribe_intro' => 'You are subscribed to PovWatch. Click the button below to unsubscribe.',
	'povwatch_unsubscribe' => 'Unsubscribe',
	'povwatch_subscribe_intro' => 'You are not subscribed to PovWatch. Click the button below to subscribe.',
	'povwatch_subscribe' => 'Subscribe',
	'povwatch_added' => 'added',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'povwatch'                         => 'مراقبة بي أو في',
	'povwatch_no_session'              => 'خطأ: لم يمكن تنفيذ الاستمارة نتيجة فقد في بيانات الجلسة.',
	'povwatch_not_allowed_push'        => 'أنت لست إداري مراقبة بي أو في، لا يمكنك دفع المقالات إلى قوائم المراقبة.',
	'povwatch_already_subscribed'      => 'أنت مشترك بالفعل في مراقبة بي أو في',
	'povwatch_subscribed'              => 'أنت الآن مشترك في مراقبة بي أو في',
	'povwatch_not_subscribed'          => 'أنت غير مشترك في مراقبة بي أو في، لذا فلا يمكنك إلغاء الاشتراك.',
	'povwatch_unsubscribed'            => 'أنت الآن ألغيت الاشتراك في مراقبة بي أو في',
	'povwatch_invalid_title'           => 'العنوان المحدد كان غير صحيح',
	'povwatch_pushed'                  => '[[$1]] تم دفعها بنجاح إلى $2 قائمة مراقبة مستخدم',
	'povwatch_intro'                   => 'مراقبة بي أو في هي خدمة تسمح بإضافة مقالات معينة إلى قوائم مراقبة الإداريين المشتركين.

السجل بعمليات دفع قوائم المراقبة الحديثة متوفر في [[Special:PovWatch/log]].',
	'povwatch_subscriber_list'         => '[[Special:PovWatch/subscribers|قائمة المشتركين]] متوفرة.',
	'povwatch_subscriber_list_intro'   => '<strong>قائمة المشتركين</strong>',
	'povwatch_not_allowed_subscribers' => 'أنت غير مسموح لك برؤية قائمة المشتركين في مراقبة بي أو في.',
	'povwatch_unknown_subpage'         => 'صفحة فرعية غير معروفة.',
	'povwatch_push'                    => 'دفع',
	'povwatch_push_intro'              => 'استخدم الاستمارة بالأسفل لدفع مقالات إلى قوائم مراقبة المستخدمين المشتركين. من فضلك كن حذرا عند كتابة العنوان: حتى العناوين غير الموجودة يمكن إضافتها، ولا توجد طريقة لإزالة عنوان ما متى تم دفعه.',
	'povwatch_title'                   => 'العنوان:',
	'povwatch_comment'                 => 'تعليق السجل',
	'povwatch_no_log'                  => 'لا توجد مدخلات سجل.',
	'povwatch_no_subscribers'          => 'لا يوجد مشتركون.',
	'povwatch_unsubscribe_intro'       => 'أنت مشترك في مراقبة بي أو في. اضغط الزر بالأسفل لإلغاء الاشتراك.',
	'povwatch_unsubscribe'             => 'إنهاء الاشتراك',
	'povwatch_subscribe_intro'         => 'أنت غير مشترك في مراقبة بي أو في. اضغط الزر بالأسفل للاشتراك.',
	'povwatch_subscribe'               => 'اشتراك',
	'povwatch_added'                   => 'تمت الإضافة',

);

#French Translation by Bertrand GRONDIN
$messages ['fr'] = array(
	'povwatch' => 'Surveillance des guerres d’éditions',
	'povwatch_no_session' => 'Erreur : Impossible de soumettre le formulaire à la suite de la perte des données de la session.',
	'povwatch_not_allowed_push' => 'Vous n’êtes pas un administrateur pour la surveillance des guerres d’édition. Vous ne pouvez pas ajouter les articles dans la liste correspondante.',
	'povwatch_already_subscribed' => 'Vous êtes déjà inscrit pour la surveillance des guerre d’édition.',
	'povwatch_subscribed' => 'Vous êtes maintenant inscrit pour la surveillance des guerres d’édition.',
	'povwatch_not_subscribed' => 'Vous n’êtes pas inscrit pour la surveillance des guerres d’édition. Ainsi, vous ne pouvez pas vous désincrire.',
	'povwatch_unsubscribed' => 'Votre inscription pour la surveillance des guerres d’édition est maintenant résiliée.',
	'povwatch_invalid_title' => 'Le titre indiqué est invalide.',
	'povwatch_pushed' => '[[$1]] a été inscrite avec succès dans la liste de surveillance de l’utilisateur $2.',
	'povwatch_intro' => 'La surveillance des guerre d’édition est un service qui autorise la surveillance discrète des articles conflictuels. Ceux-ci peuvent être inscrits dans la liste de surveillance des administrateurs enregistrés.

Un journal de surveillance des articles inscrits est disponible sur [[Special:PovWatch/log]].',
	'povwatch_subscriber_list' => 'Une [[Special:PovWatch/subscribers|liste des abonnés]] est disponible.',
	'povwatch_subscriber_list_intro' => '<strong>Liste des abonnés</strong>',
	'povwatch_not_allowed_subscribers' => 'Vous n’avez pas la permission de visionner la liste des personnes inscrite pour la surveillance des guerre d’édition.',
	'povwatch_unknown_subpage' => 'Sous-pages inconnue.',
	'povwatch_push' => 'Inscrire',
	'povwatch_push_intro' => 'Utilisez le formulaire ci-dessous pour inscrire les articles dans la liste de surveillance des utilisateurs abonés. Inscrivez scrupuleusement le titre : les articles peuvent même être ajoutés, et il n’existe aucune issue pour le retirer une fois inscrit.',
	'povwatch_title' => 'Titre :',
	'povwatch_comment' => 'Commentaire du journal :',
	'povwatch_no_log' => 'Il n’existe aucune entrée dans le journal.',
	'povwatch_no_subscribers' => 'Il n’existe aucune personne abonnée.',
	'povwatch_unsubscribe_intro' => 'Vous êtes inscrit à la liste de surveillance des guerres d’édition. Cliquez sur le bouton ci-dessous pour vous désinscrire.',
	'povwatch_unsubscribe' => 'Résilier',
	'povwatch_subscribe_intro' => 'Vous n’êtes pas inscrit sur la liste de surveillance des guerres d’édition. Cliquez sur le bouton ci-dessous pour vous inscrire.',
	'povwatch_subscribe' => 'Souscrire',
	'povwatch_added' => 'ajouté',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'povwatch_no_session'              => 'Zmylk: Formular njeda so straty datow dla wotesłać.',
	'povwatch_already_subscribed'      => 'Sy PovWatch hižo abonował',
	'povwatch_subscribed'              => 'Sy nětko PovWatch abonował',
	'povwatch_not_subscribed'          => 'Njejsy PovWatch abonował, tohodla njemóžeš jón wotskazać.',
	'povwatch_unsubscribed'            => 'Sy nětko PovWatch wotskazał',
	'povwatch_invalid_title'           => 'Podaty titul je njepłaćiwy',
	'povwatch_subscriber_list'         => '[[Special:PovWatch/subscribers|Lisćina abonentow]] steji k dispoziciji.',
	'povwatch_subscriber_list_intro'   => '<strong>Lisćina abonentow</strong>',
	'povwatch_not_allowed_subscribers' => 'Nimaš dowolnosć sej lisćinu abonentow PovWatch wobhladać.',
	'povwatch_unknown_subpage'         => 'Njeznata podstrona.',
	'povwatch_title'                   => 'Titul:',
	'povwatch_comment'                 => 'Komentar protokolować:',
	'povwatch_no_log'                  => 'Protokolowe zapiski njejsu.',
	'povwatch_no_subscribers'          => 'Abonenća njejsu.',
	'povwatch_unsubscribe_intro'       => 'Sy PovWatch abonował. Klikń na tłóčatko deleka, zo by jón wotskazał.',
	'povwatch_unsubscribe'             => 'Wotskazać',
	'povwatch_subscribe_intro'         => 'Njejsy PovWatch abonował. Klikń na tłóčatko deleka, zo by jón abonował.',
	'povwatch_subscribe'               => 'Abonować',
	'povwatch_added'                   => 'přidaty',

);
