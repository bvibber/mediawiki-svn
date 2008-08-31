<?php
/*
 * Internationalization for CloseWikis extension.
 */

$messages = array();

/**
 * English
 * @author Victor Vasiliev
 */
$messages['en'] = array(
	'closewikis-desc'           => 'Allows to close wiki sites in wiki farms',
	'closewikis-closed'         => '$1',
	'closewikis-closed-default' => 'This wiki is closed',
	'closewikis-page'           => 'Close wiki',

	'closewikis-page-close' => 'Close wiki',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Reason (displayed):',
	'closewikis-page-close-reason' => 'Reason (logged):',
	'closewikis-page-close-submit' => 'Close',
	'closewikis-page-close-success' => 'Wiki successfully closed',
	'closewikis-page-reopen' => 'Reopen wiki',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Reason:',
	'closewikis-page-reopen-submit' => 'Reopen',
	'closewikis-page-reopen-success' => 'Wiki successfully reopened',
	'closewikis-page-err-nowiki' => 'Invalid wiki specified',
	'closewikis-page-err-closed' => 'Wiki is already closed',
	'closewikis-page-err-opened' => 'Wiki is not closed',

	'closewikis-log'         => 'Wikis closure log',
	'closewikis-log-header'  => 'Here is a log of all wiki closures and reopenings made by stewards',
	'closewikis-log-close'   => 'closed $2',
	'closewikis-log-reopen'  => 'reopened $2',
	'right-editclosedwikis'  => 'Edit closed wikis',
);

/** Message documentation (Message documentation)
 * @author Darth Kule
 */
$messages['qqq'] = array(
	'right-editclosedwikis' => '{{doc-right}}',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'closewikis-desc' => 'يسمح بغلق مواقع الويكي في مزارع الويكي',
	'closewikis-closed-default' => 'هذا الويكي مغلق',
	'closewikis-page' => 'إغلاق الويكي',
	'closewikis-page-close' => 'إغلاق الويكي',
	'closewikis-page-close-wiki' => 'الويكي:',
	'closewikis-page-close-dreason' => 'السبب (المعروض):',
	'closewikis-page-close-reason' => 'السبب (المسجل):',
	'closewikis-page-close-submit' => 'إغلاق',
	'closewikis-page-close-success' => 'الويكي تم إغلاقه بنجاح',
	'closewikis-page-reopen' => 'إعادة فتح الويكي',
	'closewikis-page-reopen-wiki' => 'الويكي:',
	'closewikis-page-reopen-reason' => 'السبب:',
	'closewikis-page-reopen-submit' => 'إعادة فتح',
	'closewikis-page-reopen-success' => 'الويكي تمت إعادة فتحه بنجاح',
	'closewikis-page-err-nowiki' => 'ويكي غير صحيح تم تحديده',
	'closewikis-page-err-closed' => 'الويكي مغلق بالفعل',
	'closewikis-page-err-opened' => 'الويكي ليس مغلقا',
	'closewikis-log' => 'سجل إغلاق الويكيات',
	'closewikis-log-header' => 'هنا يوجد سجل بكل عمليات إغلاق وإعادة فتح الويكيات بواسطة المضيفين',
	'closewikis-log-close' => 'أغلق $2',
	'closewikis-log-reopen' => 'أعاد فتح $2',
	'right-editclosedwikis' => 'تعديل الويكيات المغلقة',
);

/** Czech (Česky)
 * @author Li-sung
 */
$messages['cs'] = array(
	'closewikis-desc' => 'Umožňuje uzavřít jednotlivé wiki na wikifarmách',
	'closewikis-closed-default' => 'Tato wiki je uzavřena',
	'closewikis-page' => 'Zavření wiki',
	'closewikis-page-close' => 'Zavřít wiki',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Důvod (k zobrazení):',
	'closewikis-page-close-reason' => 'Důvod (k zapsání do knihy):',
	'closewikis-page-close-submit' => 'Zavřít',
	'closewikis-page-close-success' => 'Wiki byla úspěšně zavřena',
	'closewikis-page-reopen' => 'Znovu otevřít wiki',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Důvod:',
	'closewikis-page-reopen-submit' => 'Otevřít',
	'closewikis-page-reopen-success' => 'Wiki byla úspěšně otevřena',
	'closewikis-page-err-nowiki' => 'Chybné určení wiki',
	'closewikis-page-err-closed' => 'Wiki již je zavřena',
	'closewikis-page-err-opened' => 'Wiki není zavřená',
	'closewikis-log' => 'Kniha zavření wiki',
	'closewikis-log-header' => 'Tato kniha zachycuje všechna zavření a znovuotevření wiki provedená stevardy',
	'closewikis-log-close' => 'uzavírá $2',
	'closewikis-log-reopen' => 'opět otevírá $2',
	'right-editclosedwikis' => 'Editování uzavřených wiki',
);

/** Italian (Italiano)
 * @author Pietrodn
 */
$messages['it'] = array(
	'closewikis-desc' => 'Permette di chiudere i siti wiki nelle famiglie wiki',
	'closewikis-closed-default' => 'Questa wiki è chiusa',
	'closewikis-page' => 'Chiudi wiki',
	'closewikis-page-close' => 'Chiudi wiki',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Motivo (visualizzato):',
	'closewikis-page-close-reason' => 'Motivo (registrato):',
	'closewikis-page-close-submit' => 'Chiudi',
	'closewikis-page-close-success' => 'Wiki chiusa con successo',
	'closewikis-page-reopen' => 'Riapri wiki',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Motivo:',
	'closewikis-page-reopen-submit' => 'Riapri',
	'closewikis-page-reopen-success' => 'Wiki riaperta con successo',
	'closewikis-page-err-nowiki' => 'Specificata una wiki non valida',
	'closewikis-page-err-closed' => 'La wiki è già chiusa',
	'closewikis-page-err-opened' => 'La wiki non è chiusa',
	'closewikis-log' => 'Registro di chiusura delle wiki',
	'closewikis-log-header' => 'Ecco un log di tutte le chiusure e riaperture delle wiki eseguite dagli steward',
	'closewikis-log-close' => 'chiusa $2',
	'closewikis-log-reopen' => 'riaperta $2',
	'right-editclosedwikis' => 'Modifica le wiki chiuse',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'closewikis-desc' => 'Erlaabt et Wiki-Siten a Wiki-Farmen zouzemaachen',
	'closewikis-closed-default' => 'Dës Wiki ass zougemaach',
	'closewikis-page' => 'Wiki zoumaachen',
	'closewikis-page-close' => 'Wiki zoumaachen',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Grond (ugewisen):',
	'closewikis-page-close-reason' => 'Grond (geloggt):',
	'closewikis-page-close-submit' => 'Zoumaachen',
	'closewikis-page-close-success' => 'Wiki gouf zougemaach',
	'closewikis-page-reopen' => 'Wiki nees opmaachen',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Grond:',
	'closewikis-page-reopen-submit' => 'Nees opmaachen',
	'closewikis-page-reopen-success' => 'Wiki nees opgemaach',
	'closewikis-page-err-nowiki' => 'Ongëlteg Wiki uginn',
	'closewikis-page-err-closed' => 'Wiki ass schonn zougemaach',
	'closewikis-page-err-opened' => 'Wiki ass net zougemaach',
	'closewikis-log' => 'Lëscht vun den zougemaachte Wikien',
	'closewikis-log-header' => "Hei ass d'Lëscht vun alle Wikien déi vu Stewarden opgemaach oder zougemaach goufen",
	'closewikis-log-close' => 'huet $2 zougemaach',
	'closewikis-log-reopen' => 'huet $2 nees opgemaach',
	'right-editclosedwikis' => 'Zougemaachte Wikien änneren',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 * @author Siebrand
 */
$messages['nl'] = array(
	'closewikis-desc' => "Maakt het sluiten en heropenen van wiki's in een wikifarm mogelijk",
	'closewikis-closed-default' => 'Deze wiki is gesloten',
	'closewikis-page' => 'Wiki sluiten',
	'closewikis-page-close' => 'Wiki sluiten',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Reden (weergegeven op wiki):',
	'closewikis-page-close-reason' => 'Reden (voor logboek):',
	'closewikis-page-close-submit' => 'Sluiten',
	'closewikis-page-close-success' => 'De wiki is nu gesloten',
	'closewikis-page-reopen' => 'Wiki heropenen',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Reden:',
	'closewikis-page-reopen-submit' => 'Heropenen',
	'closewikis-page-reopen-success' => 'De wiki is nu heropend',
	'closewikis-page-err-nowiki' => 'Ongeldige naam van wiki opgegeven',
	'closewikis-page-err-closed' => 'Deze wiki is al gesloten',
	'closewikis-page-err-opened' => 'Deze wiki was niet gesloten',
	'closewikis-log' => 'Wikisluitingslogboek',
	'closewikis-log-header' => "Dit is een logboek van alle sluitingen en heropeningen van wiki's uitgevoerd door stewards",
	'closewikis-log-close' => 'heeft $2 gesloten',
	'closewikis-log-reopen' => 'heeft $2 heropend',
	'right-editclosedwikis' => 'Gesloten wikis bewerken',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'closewikis-page-reopen-wiki' => 'Wiki :',
	'closewikis-page-reopen-reason' => 'Motiu :',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'closewikis-desc' => 'Umožňuje zatvoriť wiki vo wiki farmách',
	'closewikis-closed' => '$1',
	'closewikis-closed-default' => 'Táto wiki je zatvorená',
	'closewikis-page' => 'Zatvoriť wiki',
	'closewikis-page-close' => 'Zatvoriť wiki',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Dôvod (zobrazí sa):',
	'closewikis-page-close-reason' => 'Dôvod (do záznamu):',
	'closewikis-page-close-submit' => 'Zatvoriť',
	'closewikis-page-close-success' => 'Wiki bola úspešne zatvorená',
	'closewikis-page-reopen' => 'Znovu otvoriť wiki',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Dôvod:',
	'closewikis-page-reopen-submit' => 'Znovu otvoriť',
	'closewikis-page-reopen-success' => 'Wiki bola úspešne znovu otvorená',
	'closewikis-page-err-nowiki' => 'Bola zadaná neplatná wiki',
	'closewikis-page-err-closed' => 'Wiki je už zatvorená',
	'closewikis-page-err-opened' => 'Wiki nie je zatvorená',
	'closewikis-log' => 'Záznam zatvorení wiki',
	'closewikis-log-header' => 'Toto je záznam všetkých zatvorení a znovu otvorení wiki, ktoré vykonali stewardi',
	'closewikis-log-close' => 'zatvoril $2',
	'closewikis-log-reopen' => 'znovu otvoril $2',
	'right-editclosedwikis' => 'Upravovať zatvorené wiki',
);

/** Swedish (Svenska)
 * @author M.M.S.
 */
$messages['sv'] = array(
	'closewikis-closed-default' => 'Den här wikin är stängd',
	'closewikis-page' => 'Stäng wiki',
	'closewikis-page-close' => 'Stäng wiki',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-submit' => 'Stäng',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'closewikis-desc' => 'Cho phép đóng cửa các wiki trong mạng wiki',
	'closewikis-closed-default' => 'Wiki này bị đóng cửa',
	'closewikis-page' => 'Đóng cửa wiki',
	'closewikis-page-close' => 'Đóng cửa wiki',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Lý do (để trình bày):',
	'closewikis-page-close-reason' => 'Lý do (trong nhật trình):',
	'closewikis-page-close-submit' => 'Đóng cửa',
	'closewikis-page-close-success' => 'Đóng cửa wiki thành công',
	'closewikis-page-reopen' => 'Mở cửa lại wiki',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Lý do:',
	'closewikis-page-reopen-submit' => 'Mở cửa lại',
	'closewikis-page-reopen-success' => 'Mở cửa lại wiki thành công',
	'closewikis-page-err-nowiki' => 'Định rõ wiki không hợp lệ',
	'closewikis-page-err-closed' => 'Wiki đã bị đóng cửa',
	'closewikis-page-err-opened' => 'Wiki chưa bị đóng cửa',
	'closewikis-log' => 'Nhật trình đóng cửa wiki',
	'closewikis-log-header' => 'Đây là danh sách các tác vụ đóng cửa wiki và mở cửa lại wiki được thực hiện bởi tiếp viên.',
	'closewikis-log-close' => 'đóng cửa $2',
	'closewikis-log-reopen' => 'mở cửa lại $2',
	'right-editclosedwikis' => 'Sửa đổi các wiki bị đóng cửa',
);

