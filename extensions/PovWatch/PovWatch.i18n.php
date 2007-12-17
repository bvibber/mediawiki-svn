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

/** Greek (Ελληνικά)
 * @author Consta
 */
$messages['el'] = array(
	'povwatch_title' => 'Τίτλος:',
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

/** Galician (Galego)
 * @author Alma
 */
$messages['gl'] = array(
	'povwatch_invalid_title'         => 'O título especificado foi non válido',
	'povwatch_subscriber_list'       => 'Unha [[Special:PovWatch/subscribers|listaxe de subscritores]] está dispoñíbel.',
	'povwatch_subscriber_list_intro' => '<strong>Listaxe dos subscritores</strong>',
	'povwatch_unknown_subpage'       => 'Subpáxina descoñecida.',
	'povwatch_push'                  => 'Empurrar',
	'povwatch_title'                 => 'Título:',
	'povwatch_comment'               => 'Rexistro de comentarios:',
	'povwatch_no_log'                => 'Non hai entradas no rexistro.',
	'povwatch_no_subscribers'        => 'Non hai subscritores.',
	'povwatch_subscribe'             => 'Subscribir',
	'povwatch_added'                 => 'engadido',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'povwatch_no_session'              => 'Zmylk: Formular njeda so straty datow dla wotesłać.',
	'povwatch_not_allowed_push'        => 'Njejsy administrator za PovWatch, njemóžeš nastawki do wobkedźbowankow sunyć.',
	'povwatch_already_subscribed'      => 'Sy PovWatch hižo abonował',
	'povwatch_subscribed'              => 'Sy nětko PovWatch abonował',
	'povwatch_not_subscribed'          => 'Njejsy PovWatch abonował, tohodla njemóžeš jón wotskazać.',
	'povwatch_unsubscribed'            => 'Sy nětko PovWatch wotskazał',
	'povwatch_invalid_title'           => 'Podaty titul je njepłaćiwy',
	'povwatch_pushed'                  => '[[$1]] bu wuspěšnje do wobkedźbowankow wužiwarja $2 sunjeny.',
	'povwatch_intro'                   => 'PovWatch je słužba, kotraž dowola zwadne nastawki diskretnje do wobkedźbowankow abonowacych administratorow sunyć.

Protokol aktualnych wobkedźbowankow steji na [[Special:PovWatch/log]] k dispoziciji.',
	'povwatch_subscriber_list'         => '[[Special:PovWatch/subscribers|Lisćina abonentow]] steji k dispoziciji.',
	'povwatch_subscriber_list_intro'   => '<strong>Lisćina abonentow</strong>',
	'povwatch_not_allowed_subscribers' => 'Nimaš dowolnosć sej lisćinu abonentow PovWatch wobhladać.',
	'povwatch_unknown_subpage'         => 'Njeznata podstrona.',
	'povwatch_push'                    => 'Sunyć',
	'povwatch_push_intro'              => 'Wužij formular deleka, zo by nastawki do wobkedźbowankow abonowacych wužiwarjow sunyli. Prošu bjer na kedźbu z pisanjom titula: samo njeksistowace titule hodźa so přidać a njeje žana móžnosć titul wotstronić, kotryž bu přesunjeny.',
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

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'povwatch'                         => 'PovWatch',
	'povwatch_no_session'              => 'Fout: het formulier kon niet verwerkt worden omdat de sessiegegevens verloren zijn gegaan.',
	'povwatch_not_allowed_push'        => "U bent geen beheerder van PovWatch en kan geen pagina's op volglijsten zetten.",
	'povwatch_already_subscribed'      => 'U bent al geabonneerd op PovWatch',
	'povwatch_subscribed'              => 'U bent nu geabonneerd op PovWatch',
	'povwatch_not_subscribed'          => 'U bent niet geabonneerd op PovWatch, dus u kan niet uitschrijven.',
	'povwatch_unsubscribed'            => 'U bent uitgeschreven van PovWatch',
	'povwatch_invalid_title'           => 'De opgegeven paginanaam is ongeldig',
	'povwatch_pushed'                  => '[[$1]] is succesvol toegewezen aan $2 volglijsten van gebruikers.',
	'povwatch_intro'                   => "PovWatch is een dienst die het mogelijk maakt gevoelige pagina's discreet op de volglijst van geabonneerde beheerders te zetten.

Een logboek met recent toegewezen pagina's op volglijsten is te bekijken op [[Special:PovWatch/log]].",
	'povwatch_subscriber_list'         => 'Er is een [[Special:PovWatch/subscribers|lijst met abonnees]] beschikbaar.',
	'povwatch_subscriber_list_intro'   => '<strong>Abonnees</strong>',
	'povwatch_not_allowed_subscribers' => 'U mag de lijst van abonnees op PovWatch niet bekijken.',
	'povwatch_unknown_subpage'         => 'Onbekende subpagina.',
	'povwatch_push'                    => 'Toewijzen',
	'povwatch_push_intro'              => "Gebruik het onderstaande formulier om pagina's op de volglijst van abonnees te zetten. Wees voorzichtig bij het invoeren van de pagina: zelfs niet-bestaande pagina's kunnen toegevoegd worden en u kan de pagina niet verwijderen als die is toegewezen.",
	'povwatch_title'                   => 'Pagina:',
	'povwatch_comment'                 => 'Logboekopmerking:',
	'povwatch_no_log'                  => 'Het logboek is leeg.',
	'povwatch_no_subscribers'          => 'Er is niemand geabonneerd.',
	'povwatch_unsubscribe_intro'       => 'U bent nu geabonneerd op PovWatch. Klik op de onderstaande knop om u uit te schrijven.',
	'povwatch_unsubscribe'             => 'Uitschrijven',
	'povwatch_subscribe_intro'         => 'U bent niet ingeschreven voor PovWatch. Klik op de onderstaande knop om u te abonneren.',
	'povwatch_subscribe'               => 'Abonneren',
	'povwatch_added'                   => 'toegevoegd',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'povwatch'                         => 'Susvelhança de las guèrras d’edicions',
	'povwatch_no_session'              => 'Error : Impossible de sometre lo formulari en seguida de la pèrda de las donadas de la session.',
	'povwatch_not_allowed_push'        => 'Sètz pas un administrator per la susvelhança de las guèrras d’edicion. Podètz pas ajustar los articles dins la lista correspondenta.',
	'povwatch_already_subscribed'      => 'Ja sètz inscrich(-a) per la susvelhança de las guèrras d’edicion.',
	'povwatch_subscribed'              => 'Ara sètz inscrich(-a) per la susvelhança de las guèrras d’edicion.',
	'povwatch_not_subscribed'          => 'Sètz pas inscrich(-a) per la susvelhança de las guèrras d’edicion. Atal doncas, podètz pas vos desincriure.',
	'povwatch_unsubscribed'            => 'Vòstra inscripcion per la susvelhança de las guèrras d’edicion ara es resiliada.',
	'povwatch_invalid_title'           => 'Lo títol indicat es invalid.',
	'povwatch_pushed'                  => '[[$1]] es estada inscricha amb succès dins la lista de susvelhança de l’utilizaire $2.',
	'povwatch_intro'                   => "La susvelhança de las guèrras d’edicion es un servici qu'autoriza la susvelhança discrèta dels articles conflictuals. Aquestes pòdon èsser inscriches dins la lista de susvelhança dels administrators enregistrats. Un jornal de susvelhança dels articles inscriches es disponible sus [[Special:PovWatch/log]].",
	'povwatch_subscriber_list'         => 'Una [[Special:PovWatch/subscribers|lista dels abonats]] es disponibla.',
	'povwatch_subscriber_list_intro'   => '<strong>Lista dels abonats</strong>',
	'povwatch_not_allowed_subscribers' => 'Avètz pas la permission de visionar la lista de las personas inscrichas per la susvelhança de las guèrras d’edicions.',
	'povwatch_unknown_subpage'         => 'Sospagina desconeguda.',
	'povwatch_push'                    => 'Inscriure',
	'povwatch_push_intro'              => "Utilizatz lo formulari çaijós per inscriure los articles dins la lista de susvelhança dels utilizaires abonats. Inscrivissetz escrupulosament lo títol : los articles pòdon quitament èsser ajustats, e existís pas cap d'eissida per o levar un còp inscrich.",
	'povwatch_title'                   => 'Títol:',
	'povwatch_comment'                 => 'Comentari del jornal :',
	'povwatch_no_log'                  => "Existís pas cap d'entrada dins lo jornal.",
	'povwatch_no_subscribers'          => 'Existís pas cap de persona abonada.',
	'povwatch_unsubscribe_intro'       => 'Sètz inscrich(-a) a la lista de susvelhança de las guèrras d’edicion. Clicatz sul boton çaijós per vos desinscriure.',
	'povwatch_unsubscribe'             => 'Resiliar',
	'povwatch_subscribe_intro'         => 'Sètz pas inscrich(-a) sus la lista de susvelhança de las guèrras d’edicion. Clicatz sul boton çaijós per vos inscriure.',
	'povwatch_subscribe'               => 'Soscriure',
	'povwatch_added'                   => 'ajustat',
);
