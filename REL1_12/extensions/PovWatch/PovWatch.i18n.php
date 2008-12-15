<?php
/**
 * Internationalisation file for extension PovWatch.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'povwatch' => 'PovWatch',
	'povwatch_desc' => 'Extension for [[Special:PovWatch|pushing pages on to the watchlists]] of other users',
	'povwatch_no_session' => 'Error: Could not submit form due to a loss of session data.',
	'povwatch_not_allowed_push' => 'You are not a PovWatch admin, you can\'t push pages to watchlists.',
	'povwatch_already_subscribed' => 'You are already subscribed to PovWatch',
	'povwatch_subscribed' => 'You are now subscribed to PovWatch',
	'povwatch_not_subscribed' => 'You are not subscribed to PovWatch, so you can\'t unsubscribe.',
	'povwatch_unsubscribed' => 'You have now unsubscribed from PovWatch',
	'povwatch_invalid_title' => 'The title specified was invalid',
	'povwatch_pushed' => '[[$1]] has successfully been pushed to $2 user watchlist(s)',
	'povwatch_intro' => 'PovWatch is a service which allows contentious pages to be discreetly pushed on to the watchlists of subscribing administrators.

A log of recent watchlist pushes is available at [[Special:PovWatch/log]].',
	'povwatch_subscriber_list' => 'A [[Special:PovWatch/subscribers|list of subscribers]] is available.',
	'povwatch_subscriber_list_intro' => '<strong>Subscriber list</strong>',
	'povwatch_not_allowed_subscribers' => 'You are not allowed to view the PovWatch subscriber list.',
	'povwatch_unknown_subpage' => 'Unknown subpage.',
	'povwatch_push' => 'Push',
	'povwatch_push_intro' => 'Use the form below to push pages on to the watchlists of subscribing users. Please be careful typing the title: even non-existent titles can be added, and there is no way to remove a title once it has been pushed out.',
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
	'povwatch_desc'                    => 'امتداد [[Special:PovWatch|لدفع الصفحات إلى قوائم مراقبة]] المستخدمين الآخرين',
	'povwatch_no_session'              => 'خطأ: لم يمكن تنفيذ الاستمارة نتيجة فقد في بيانات الجلسة.',
	'povwatch_not_allowed_push'        => 'أنت لست إداري مراقبة بي أو في، لا يمكنك دفع صفحات إلى قوائم مراقبة.',
	'povwatch_already_subscribed'      => 'أنت مشترك بالفعل في مراقبة بي أو في',
	'povwatch_subscribed'              => 'أنت الآن مشترك في مراقبة بي أو في',
	'povwatch_not_subscribed'          => 'أنت غير مشترك في مراقبة بي أو في، لذا فلا يمكنك إلغاء الاشتراك.',
	'povwatch_unsubscribed'            => 'أنت الآن ألغيت الاشتراك في مراقبة بي أو في',
	'povwatch_invalid_title'           => 'العنوان المحدد كان غير صحيح',
	'povwatch_pushed'                  => '[[$1]] تم دفعها بنجاح إلى $2 قائمة مراقبة مستخدم',
	'povwatch_intro'                   => 'مراقبة بي أو في هي خدمة تسمح بإضافة صفحات معينة إلى قوائم مراقبة الإداريين المشتركين.

السجل بعمليات دفع قوائم المراقبة الحديثة متوفر في [[Special:PovWatch/log]].',
	'povwatch_subscriber_list'         => '[[Special:PovWatch/subscribers|قائمة المشتركين]] متوفرة.',
	'povwatch_subscriber_list_intro'   => '<strong>قائمة المشتركين</strong>',
	'povwatch_not_allowed_subscribers' => 'أنت غير مسموح لك برؤية قائمة المشتركين في مراقبة بي أو في.',
	'povwatch_unknown_subpage'         => 'صفحة فرعية غير معروفة.',
	'povwatch_push'                    => 'دفع',
	'povwatch_push_intro'              => 'استخدم الاستمارة بالأسفل لدفع صفحات إلى قوائم مراقبة المستخدمين المشتركين. من فضلك كن حذرا عند كتابة العنوان: حتى العناوين غير الموجودة يمكن إضافتها، ولا توجد طريقة لإزالة عنوان ما متى تم دفعه.',
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

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'povwatch_invalid_title'   => 'Посоченото заглавие е невалидно',
	'povwatch_unknown_subpage' => 'Непозната подстраница.',
	'povwatch_title'           => 'Заглавие:',
	'povwatch_no_log'          => 'Дневникът не съдържа записи.',
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'povwatch'                         => 'PovWatch',
	'povwatch_desc'                    => 'Erweiterung, um [[Special:PovWatch|Seiten auf die Beobachtungsliste]] anderer Benutzer hinzuzufügen',
	'povwatch_no_session'              => 'Fehler: Formulardaten können nicht verarbeitet werden, da die Sizungsdaten verloren gegangen sind.',
	'povwatch_not_allowed_push'        => 'Du bist kein PovWatch-Administrator und kannst fremden Beobachtungslisten keine Seiten hinzufügen.',
	'povwatch_already_subscribed'      => 'Du bist bereits für PovWatch registriert',
	'povwatch_subscribed'              => 'Du bist nun für PovWatch registriert',
	'povwatch_not_subscribed'          => 'Du bist nicht für PovWatch registriert; eine Abmeldung ist daher nicht möglich.',
	'povwatch_unsubscribed'            => 'Du bist nun von PovWatch abgemeldet',
	'povwatch_invalid_title'           => 'Der angegebene Seitenname ist ungültig',
	'povwatch_pushed'                  => '[[$1]] wurde erfolgreich der Beobachtungsliste von $2 hinzugefügt.',
	'povwatch_intro'                   => 'PovWatch ist ein Service, um umstrittene Seiten diskret den Beobachtungslisten von registrierten Administratoren hinzuzufügen.

	Ein Logbuch der über mittels PovWatch hinzugefügten Seiten ist verfügbar unter [[{{ns:special}}:PovWatch/log]].',
	'povwatch_subscriber_list'         => 'Eine [[Special:PovWatch/subscribers|Liste der registrierten Benutzer]] ist verfügbar.',
	'povwatch_subscriber_list_intro'   => '<strong>Liste der registrierten Benutzer</strong>',
	'povwatch_not_allowed_subscribers' => 'Du hast keine Berechtigung, die PovWatch-Benutzerliste einzusehen.',
	'povwatch_unknown_subpage'         => 'Unbekannte Unterseite',
	'povwatch_push'                    => 'Hinzufügen',
	'povwatch_push_intro'              => 'Benutze das Formular, um Seiten den Beobachtungslisten der registrierten Benutzer hinzuzufügen.
	Bitte beachte: auch nicht vorhandene Seiten können hinzugefügt werde und es gibt keinen Weg, dies rückgängig zu machen.',
	'povwatch_title'                   => 'Seitenname:',
	'povwatch_comment'                 => 'Logbuch-Kommentar:',
	'povwatch_no_log'                  => 'Das Logbuch enthält keine Einträge.',
	'povwatch_no_subscribers'          => 'Es gibt keine registrierten Benutzer.',
	'povwatch_unsubscribe_intro'       => 'Du bist für PovWatch registriert. Klicke auf die Schaltfläche, um dich abzumelden.',
	'povwatch_unsubscribe'             => 'Abmelden',
	'povwatch_subscribe_intro'         => 'Du bist nicht für PovWatch registriert. Klicke auf die Schaltfläche, um dich anzumelden.',
	'povwatch_subscribe'               => 'Registrieren',
	'povwatch_added'                   => 'hinzugefügt',
);

/** Greek (Ελληνικά)
 * @author Consta
 */
$messages['el'] = array(
	'povwatch_title' => 'Τίτλος:',
);

/** French (Français)
 * @author Grondin
 * @author Urhixidur
 */
$messages['fr'] = array(
	'povwatch'                         => 'Surveillance des guerres d’éditions',
	'povwatch_desc'                    => 'Extension permettant d’[[Special:PovWatch|ajouter des pages à la liste de suivi]] d’autres utilisateurs',
	'povwatch_no_session'              => 'Erreur : Impossible de soumettre le formulaire à la suite de la perte des données de la session.',
	'povwatch_not_allowed_push'        => 'Vous n’êtes pas un administrateur pour la surveillance des guerres d’édition. Vous ne pouvez pas ajouter les articles dans la liste correspondante.',
	'povwatch_already_subscribed'      => 'Vous êtes déjà inscrit pour la surveillance des guerres d’édition.',
	'povwatch_subscribed'              => 'Vous êtes maintenant inscrit pour la surveillance des guerres d’édition.',
	'povwatch_not_subscribed'          => 'Vous n’êtes pas inscrit pour la surveillance des guerres d’édition. Par conséquent, vous ne pouvez pas résilier d’inscription.',
	'povwatch_unsubscribed'            => 'Votre inscription pour la surveillance des guerres d’édition est maintenant résiliée.',
	'povwatch_invalid_title'           => 'Le titre indiqué est invalide.',
	'povwatch_pushed'                  => '[[$1]] a été inscrite avec succès dans la liste de surveillance de l’utilisateur $2.',
	'povwatch_intro'                   => 'La surveillance des guerres d’édition est un service qui autorise la surveillance discrète des articles conflictuels. Ceux-ci peuvent être inscrits dans la liste de surveillance des administrateurs enregistrés.

Un journal de surveillance des articles inscrits est disponible sur [[Special:PovWatch/log]].',
	'povwatch_subscriber_list'         => 'Une [[Special:PovWatch/subscribers|liste des abonnés]] est disponible.',
	'povwatch_subscriber_list_intro'   => '<strong>Liste des abonnés</strong>',
	'povwatch_not_allowed_subscribers' => 'Vous n’avez pas la permission de visionner la liste des personnes inscrites pour la surveillance des guerres d’édition.',
	'povwatch_unknown_subpage'         => 'Sous-page inconnue.',
	'povwatch_push'                    => 'Inscrire',
	'povwatch_push_intro'              => 'Utilisez le formulaire ci-dessous pour inscrire les articles dans la liste de suivi affectée aux utilisateurs abonnés. Inscrivez scrupuleusement le titre : un article inexistant peut être spécifié, et il n’existe aucun moyen de retirer un titre une fois inscrit.',
	'povwatch_title'                   => 'Titre :',
	'povwatch_comment'                 => 'Commentaire du journal :',
	'povwatch_no_log'                  => 'Il n’existe aucune entrée dans le journal.',
	'povwatch_no_subscribers'          => 'Il n’existe aucune personne abonnée.',
	'povwatch_unsubscribe_intro'       => 'Vous êtes inscrit à la liste de surveillance des guerres d’édition. Cliquez sur le bouton ci-dessous pour vous désinscrire.',
	'povwatch_unsubscribe'             => 'Résilier',
	'povwatch_subscribe_intro'         => 'Vous n’êtes pas inscrit sur la liste de surveillance des guerres d’édition. Cliquez sur le bouton ci-dessous pour vous inscrire.',
	'povwatch_subscribe'               => 'Souscrire',
	'povwatch_added'                   => 'ajouté',
);

/** Galician (Galego)
 * @author Alma
 */
$messages['gl'] = array(
	'povwatch_already_subscribed'    => 'Vostede está aínda subscrito a PovWatch',
	'povwatch_subscribed'            => 'Vostede está agora subscrito a PovWatch',
	'povwatch_invalid_title'         => 'O título especificado foi non válido',
	'povwatch_subscriber_list'       => 'Unha [[Special:PovWatch/subscribers|listaxe de subscritores]] está dispoñíbel.',
	'povwatch_subscriber_list_intro' => '<strong>Listaxe dos subscritores</strong>',
	'povwatch_unknown_subpage'       => 'Subpáxina descoñecida.',
	'povwatch_push'                  => 'Empurrar',
	'povwatch_title'                 => 'Título:',
	'povwatch_comment'               => 'Rexistro de comentarios:',
	'povwatch_no_log'                => 'Non hai entradas no rexistro.',
	'povwatch_no_subscribers'        => 'Non hai subscritores.',
	'povwatch_unsubscribe'           => 'Darse de baixa',
	'povwatch_subscribe'             => 'Subscribir',
	'povwatch_added'                 => 'engadido',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'povwatch_desc'                    => 'Rozšěrjenje za [[Special:PovWatch|zasunjenje stronow do wobkedźbowankow]] druhich wužiwarjow',
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
	'povwatch_push_intro'              => 'Wužij formular deleka, zo by nastawki do wobkedźbowankow abonowacych wužiwarjow sunył. Prošu bjer na kedźbu z pisanjom titula: samo njeeksistowace titule hodźa so přidać a njeje žana móžnosć titul wotstronić, kotryž bu přesunjeny.',
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

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'povwatch'       => 'Iwwerwaachung vun Ännerungskonflikter',
	'povwatch_added' => 'derbäigesat',
);

/** Limburgish (Limburgs)
 * @author Ooswesthoesbes
 */
$messages['li'] = array(
	'povwatch'                         => 'POV-Beloer',
	'povwatch_no_session'              => "Fout: 't formeleer kós neet verwèrk waere ómdet de sessiegegaeves verlaore zeen gegange.",
	'povwatch_not_allowed_push'        => "Doe bös geine administrator van POV-Beloer en kins gein pazjena's op volglies zitte.",
	'povwatch_already_subscribed'      => 'Doe bös al geabonneertdj op POV-Beloer',
	'povwatch_subscribed'              => 'Doe bös noe geabonneerdj op POV-Beloer',
	'povwatch_not_subscribed'          => 'Doe bös neet geabonneerdj op POV-Beloer, dus doe kans neet oetsjrieve.',
	'povwatch_unsubscribed'            => 'Doe bös oetgesjreve van POV-Beloer',
	'povwatch_invalid_title'           => 'De opgegaeve pazjenanaam is óngeljig',
	'povwatch_pushed'                  => '[[$1]] is succesvol toegeweze aan $2 volglies van gebroekers.',
	'povwatch_intro'                   => "POV-Beloer is 'ne deens dae 't meugelik maak geveulige pazjena's discreet op de volglies van geabonneerdje administrators te zitte.

'n Logbook mit recènt toegeweze pazjena's op volglies is te bekieke op [[Special:PovWatch/log]].",
	'povwatch_subscriber_list'         => "d'r Is 'ne [[Special:PovWatch/subscribers|lies mit abonnees]] besjikbaar.",
	'povwatch_subscriber_list_intro'   => '<strong>Abonnees</strong>',
	'povwatch_not_allowed_subscribers' => 'Doe moogs de lies van abonnees op POV-Beloer neet bekieke.',
	'povwatch_unknown_subpage'         => 'Ónbekindje subpazjena.',
	'povwatch_push'                    => 'Toewieze',
	'povwatch_push_intro'              => "Gebroek 't óngerstäönde formeleer óm pazjena's op de volglies van abonnees te zitte. Bön veurzichtig bie 't inveure van de pazjena; zelfs neet bestäönde pazjena's kinne toegevoeg waere en doe kans de pazjena neet wisse es dae is toegeweze.",
	'povwatch_title'                   => 'Pazjena:',
	'povwatch_comment'                 => 'Logbookopmèrking:',
	'povwatch_no_log'                  => "'t Logbook is laeg.",
	'povwatch_no_subscribers'          => "d'r Is nemes geabonneerdj.",
	'povwatch_unsubscribe_intro'       => 'Doe bös noe geabonneerdj op POV-Beloer. Klik op de óngerstäönde knoep óm uch oet te sjrieve.',
	'povwatch_unsubscribe'             => 'Oetsjrieve',
	'povwatch_subscribe_intro'         => 'Doe bös neet ingesjreve veur POV-Beloer. Klik op de óngerstäönde knoep óm uch te abonnere.',
	'povwatch_subscribe'               => 'Abonnere',
	'povwatch_added'                   => 'toegevoeg',
);

/** Dutch (Nederlands)
 * @author Siebrand
 * @author SPQRobin
 */
$messages['nl'] = array(
	'povwatch'                         => 'PovWatch',
	'povwatch_desc'                    => "[[Special:PovWatch|Speciale pagina]] om pagina's op de volglijst van andere gebruikers te plaatsen",
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

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'povwatch_invalid_title'   => 'Den gitte tittelen var ugyldig',
	'povwatch_unknown_subpage' => 'Ukjent underside.',
	'povwatch_title'           => 'Tittel:',
	'povwatch_comment'         => 'Loggkommentar:',
	'povwatch_no_log'          => 'Det er ingen elementer i loggen.',
	'povwatch_added'           => 'lagt til',
);

/** Northern Sotho (Sesotho sa Leboa)
 * @author Mohau
 */
$messages['nso'] = array(
	'povwatch_title' => 'Thaetlele:',
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

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'povwatch_invalid_title'   => 'O título especificado é inválido',
	'povwatch_unknown_subpage' => 'Subpágina desconhecida.',
	'povwatch_title'           => 'Título:',
);

/** Russian (Русский)
 * @author .:Ajvol:.
 */
$messages['ru'] = array(
	'povwatch'                         => 'НтзНадзор',
	'povwatch_no_session'              => 'Ошибка. Невозможно отправить форму из-за потери данных сессии.',
	'povwatch_not_allowed_push'        => 'Вы не являетесь администратором НтзНадзора, вы не можете моиещать страницы в списки наблюдения.',
	'povwatch_already_subscribed'      => 'Вы уже подписаны на НтзНадзор',
	'povwatch_subscribed'              => 'Теперь вы подписаны на НтзНадзор',
	'povwatch_not_subscribed'          => 'Вы не подписаны на НтзНадзор, поэтому вы не можете отписаться.',
	'povwatch_unsubscribed'            => 'Вы отписались от НтзНадзора.',
	'povwatch_invalid_title'           => 'Указанный заголовок неверен',
	'povwatch_pushed'                  => '[[$1]] была успешно помещёна в список наблюдения $2 участника(ов)',
	'povwatch_intro'                   => 'НтзНадзор (PovWatch ) — служба, позволяющая скрытно помещать спорные страницы в списки наблюдения подписанных администраторов.

Журнал недавних помещений в списки наблюдения доступен на странице [[Special:PovWatch/log]].',
	'povwatch_subscriber_list'         => 'Доступен [[Special:PovWatch/subscribers|список подписчиков]].',
	'povwatch_subscriber_list_intro'   => '<strong>Список подписчиков</strong>',
	'povwatch_not_allowed_subscribers' => 'Вам не разрешено просматривать список подписчиков НтзНадзора.',
	'povwatch_unknown_subpage'         => 'Неизвестная подстраница.',
	'povwatch_push'                    => 'Поместить',
	'povwatch_push_intro'              => 'Используйте форму ниже, чтобы поместить страницы в списки наблюдения подписанных участников. Пожалуйста, будьте осторожны набирая название: даже несуществующие названия могут быть добавлены, и нет никакой возможности удалить название, если оно уже было добавлено.',
	'povwatch_title'                   => 'Название:',
	'povwatch_comment'                 => 'Примечание для журнала:',
	'povwatch_no_log'                  => 'Нет записей в журнале.',
	'povwatch_no_subscribers'          => 'Нет подписчиков.',
	'povwatch_unsubscribe_intro'       => 'Вы подписались на НтзНадзор. Нажмите на кнопку ниже, чтобы отписаться.',
	'povwatch_unsubscribe'             => 'Отписаться',
	'povwatch_subscribe_intro'         => 'Вы не подписаны на НтзНадзор. Нажмите кнопку ниже, чтобы подписаться.',
	'povwatch_subscribe'               => 'Подписаться',
	'povwatch_added'                   => 'добавлен',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'povwatch'                         => 'PovWatch',
	'povwatch_desc'                    => 'Rozšírenie na [[Special:PovWatch|pridávanie stránok na zoznamy sledovaných stránok]] ostatných používateľov',
	'povwatch_no_session'              => 'Error: nebolo možné odoslať formulár kvôli strate údajov prihlasovacej relácie.',
	'povwatch_not_allowed_push'        => 'Nie ste správca PovWatch, nemôžete pridávať stránky na zoznamy sledovaných stránok.',
	'povwatch_already_subscribed'      => 'Už ste sa prihlásili na odber PovWatch',
	'povwatch_subscribed'              => 'Teraz ste sa prihlásili na odber PovWatch',
	'povwatch_not_subscribed'          => 'Nie ste prihlásený na odber PovWatch, takže ho nemôžete odhlásiť.',
	'povwatch_unsubscribed'            => 'Teraz ste sa odhlásili z odberu PovWatch',
	'povwatch_invalid_title'           => 'Zadaný názov bol neplatný',
	'povwatch_pushed'                  => '[[$1]] bolo úspešne pridané na {{PLURAL:$1|zoznam sledovaných stránok jedného používateľa|zoznamy sledovaných stránok $1 používateľov}}',
	'povwatch_intro'                   => 'PovWatch je služba, ktorá umožňuje diskrétne pridávať obsažné stránky na zoznamy sledovaných stránok správcov, ktorí si to objednali.

Záznam posledných zoznamov sledovaných stránok sa nachádza na [[Special:PovWatch/log]].',
	'povwatch_subscriber_list'         => 'Je dostupný [[Special:PovWatch/subscribers|zoznam odoberateľov]].',
	'povwatch_subscriber_list_intro'   => '<strong>Zoznam odoberateľov</strong>',
	'povwatch_not_allowed_subscribers' => 'Nemáte oprávnenie prehliadať zoznam odoberateľov PovWatch.',
	'povwatch_unknown_subpage'         => 'Neznáma podstránka.',
	'povwatch_push'                    => 'Pridať',
	'povwatch_push_intro'              => 'Použite tento formulár na pridanie stránok na zoznamy sledovaných stránok používateľov, ktorí ich odoberajú. Prosím, buďte pozorní pri písaní názvu stránky, je možné pridať aj názvy neexistujúcich stránok a neexistuje spôsob ako ich odstrániť, keď raz boli pridané.',
	'povwatch_title'                   => 'Názov:',
	'povwatch_comment'                 => 'Komentár v zázname:',
	'povwatch_no_log'                  => 'Neexistujú žiadne položky záznamu.',
	'povwatch_no_subscribers'          => 'Neexistujú žiadni odoberatelia.',
	'povwatch_unsubscribe_intro'       => 'Prihlásili ste sa na odber PovWatch. Odhlásiť odber môžete kliknutím na tlačidlo dolu.',
	'povwatch_unsubscribe'             => 'Odhlásiť odber',
	'povwatch_subscribe_intro'         => 'Nie ste prihlásený na odber PovWatch. Prihlásiť odber môžete kliknutím na tlačidlo dolu.',
	'povwatch_subscribe'               => 'Prihlásiť odber',
	'povwatch_added'                   => 'pridaný',
);

/** Swedish (Svenska)
 * @author M.M.S.
 */
$messages['sv'] = array(
	'povwatch_unknown_subpage' => 'Okänd undersida.',
	'povwatch_push'            => 'Tryck',
	'povwatch_title'           => 'Titel:',
	'povwatch_comment'         => 'Logg kommentar:',
	'povwatch_added'           => 'tillaggd',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'povwatch_title' => 'శీర్షిక:',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'povwatch_title'       => 'Tên trang:',
	'povwatch_unsubscribe' => 'Ngừng theo dõi',
	'povwatch_subscribe'   => 'Theo dõi',
);

/** Volapük (Volapük)
 * @author Malafaya
 */
$messages['vo'] = array(
	'povwatch_title' => 'Tiäd:',
);

