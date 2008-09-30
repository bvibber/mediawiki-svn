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
 * @author Purodha
 */
$messages['qqq'] = array(
	'closewikis-desc' => 'Short description of this extension, shown on [[Special:Version]]. Do not translate or change links.',
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

/** Egyptian Spoken Arabic (مصرى)
 * @author Meno25
 */
$messages['arz'] = array(
	'closewikis-desc' => 'يسمح بغلق مواقع الويكى فى مزارع الويكي',
	'closewikis-closed-default' => 'هذا الويكى مغلق',
	'closewikis-page' => 'إغلاق الويكي',
	'closewikis-page-close' => 'إغلاق الويكي',
	'closewikis-page-close-wiki' => 'الويكي:',
	'closewikis-page-close-dreason' => 'السبب (المعروض):',
	'closewikis-page-close-reason' => 'السبب (المسجل):',
	'closewikis-page-close-submit' => 'إغلاق',
	'closewikis-page-close-success' => 'الويكى تم إغلاقه بنجاح',
	'closewikis-page-reopen' => 'إعادة فتح الويكي',
	'closewikis-page-reopen-wiki' => 'الويكي:',
	'closewikis-page-reopen-reason' => 'السبب:',
	'closewikis-page-reopen-submit' => 'إعادة فتح',
	'closewikis-page-reopen-success' => 'الويكى تمت إعادة فتحه بنجاح',
	'closewikis-page-err-nowiki' => 'ويكى غير صحيح تم تحديده',
	'closewikis-page-err-closed' => 'الويكى مغلق بالفعل',
	'closewikis-page-err-opened' => 'الويكى ليس مغلقا',
	'closewikis-log' => 'سجل إغلاق الويكيات',
	'closewikis-log-header' => 'هنا يوجد سجل بكل عمليات إغلاق وإعادة فتح الويكيات بواسطة المضيفين',
	'closewikis-log-close' => 'أغلق $2',
	'closewikis-log-reopen' => 'أعاد فتح $2',
	'right-editclosedwikis' => 'تعديل الويكيات المغلقة',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'closewikis-closed-default' => 'Това уики е затворено',
	'closewikis-page' => 'Затваряне на уикито',
	'closewikis-page-close' => 'Затваряне на уикито',
	'closewikis-page-close-wiki' => 'Уики:',
	'closewikis-page-close-dreason' => 'Причина (публична):',
	'closewikis-page-close-submit' => 'Затваряне',
	'closewikis-page-close-success' => 'Уикито беше затворено успешно',
	'closewikis-page-reopen-wiki' => 'Уики:',
	'closewikis-page-reopen-reason' => 'Причина:',
	'closewikis-page-err-nowiki' => 'Посоченото уики е невалидно',
	'closewikis-page-err-closed' => 'Уикито вече беше затворено',
	'closewikis-page-err-opened' => 'Уикито не беше затворено',
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

/** German (Deutsch)
 * @author ChrisiPK
 */
$messages['de'] = array(
	'closewikis-desc' => 'Ermöglicht das Schließen einzelner Wikis in einer Wikifarm',
	'closewikis-closed-default' => 'Dieses Wiki ist geschlossen.',
	'closewikis-page' => 'Wiki schließen.',
	'closewikis-page-close' => 'Wiki schließen',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Angezeigter Grund:',
	'closewikis-page-close-reason' => 'Grund, der ins Logbuch eingetragen wird:',
	'closewikis-page-close-submit' => 'Schließen',
	'closewikis-page-close-success' => 'Wiki erfolgreich geschlossen.',
	'closewikis-page-reopen' => 'Wiki wieder öffnen',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Grund:',
	'closewikis-page-reopen-submit' => 'Wieder öffnen',
	'closewikis-page-reopen-success' => 'Wiki erfolgreich wieder geöffnet',
	'closewikis-page-err-nowiki' => 'Ungültiges Wiki angegeben',
	'closewikis-page-err-closed' => 'Wiki ist bereits geschlossen',
	'closewikis-page-err-opened' => 'Wiki ist nicht geschlossen',
	'closewikis-log' => 'Wikischließungs-Logbuch',
	'closewikis-log-header' => 'Dieses Logbuch zeigt alle Schließungen und Wiederöffnungen von Wikis durch Stewards an.',
	'closewikis-log-close' => 'schloss $2',
	'closewikis-log-reopen' => 'öffnete $2 wieder',
	'right-editclosedwikis' => 'Geschlossene Wikis bearbeiten',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'closewikis-closed-default' => 'Ĉi tiu vikio estas fermita',
	'closewikis-page' => 'Fermi vikion',
	'closewikis-page-close' => 'Fermi vikion',
	'closewikis-page-close-wiki' => 'Vikio:',
	'closewikis-page-close-dreason' => 'Kialo (montrota):',
	'closewikis-page-close-reason' => 'Kialo (protokolota):',
	'closewikis-page-close-submit' => 'Fermi',
	'closewikis-page-close-success' => 'Vikio estis sukcese fermita',
	'closewikis-page-reopen' => 'Remalfermi vikion',
	'closewikis-page-reopen-wiki' => 'Vikio:',
	'closewikis-page-reopen-reason' => 'Kialo:',
	'closewikis-page-reopen-submit' => 'Remalfermi',
	'closewikis-page-reopen-success' => 'Vikio estis sukcese remalfermita',
	'closewikis-page-err-nowiki' => 'Nevalida vikio estis specifita',
	'closewikis-page-err-closed' => 'Vikio estas jam fermita',
	'closewikis-page-err-opened' => 'Vikio ne estas fermita',
	'closewikis-log' => 'Protokolo pri vikia fermado',
	'closewikis-log-close' => 'fermis $2',
	'closewikis-log-reopen' => 'remalfermis $2',
	'right-editclosedwikis' => 'Redakti fermitajn vikiojn',
);

/** French (Français)
 * @author Grondin
 * @author Zetud
 */
$messages['fr'] = array(
	'closewikis-desc' => 'Permet de clôturer les sites wiki dans ce gestionnaire de wiki',
	'closewikis-closed-default' => 'Ce wiki est clôturé',
	'closewikis-page' => 'Clôturer le wiki',
	'closewikis-page-close' => 'Clôturer le wiki',
	'closewikis-page-close-wiki' => 'Wiki :',
	'closewikis-page-close-dreason' => 'Motif (affiché) :',
	'closewikis-page-close-reason' => 'Motif (enregistré) :',
	'closewikis-page-close-submit' => 'Clôturer',
	'closewikis-page-close-success' => 'Wiki clôturé avec succès',
	'closewikis-page-reopen' => 'Réouvrir le wiki',
	'closewikis-page-reopen-wiki' => 'Wiki :',
	'closewikis-page-reopen-reason' => 'Motif :',
	'closewikis-page-reopen-submit' => 'Réouvrir',
	'closewikis-page-reopen-success' => 'Wiki réouvert avec succès',
	'closewikis-page-err-nowiki' => 'Le wiki indiqué est incorrect',
	'closewikis-page-err-closed' => 'Ce wiki est déjà clôturé',
	'closewikis-page-err-opened' => 'Wiki non clôturé',
	'closewikis-log' => 'Journal de clôture des wiki',
	'closewikis-log-header' => 'Voici un journal de toutes les fermetures et réouvertures de wiki faites par les stewards',
	'closewikis-log-close' => 'a clôturé $2',
	'closewikis-log-reopen' => 'a réouvert $2',
	'right-editclosedwikis' => 'Modifier les wikis clôturés',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'closewikis-desc' => 'Permite pechar wikis nas granxas wiki',
	'closewikis-closed-default' => 'Este wiki está pechado',
	'closewikis-page' => 'Pechar o wiki',
	'closewikis-page-close' => 'Pechar o wiki',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Motivo (amosado):',
	'closewikis-page-close-reason' => 'Motivo (rexistro):',
	'closewikis-page-close-submit' => 'Pechar',
	'closewikis-page-close-success' => 'O wiki foi pechado con éxito',
	'closewikis-page-reopen' => 'Volver abrir o wiki',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Motivo:',
	'closewikis-page-reopen-submit' => 'Volver abrir',
	'closewikis-page-reopen-success' => 'O wiki foi aberto de novo con éxito',
	'closewikis-page-err-nowiki' => 'Especificou un wiki inválido',
	'closewikis-page-err-closed' => 'O wiki xa está pechado',
	'closewikis-page-err-opened' => 'O wiki non está pechado',
	'closewikis-log' => 'Rexistro de peches de wikis',
	'closewikis-log-header' => 'Aquí hai un rexistro de todos os peches e reaperturas de wikis feitos polos stewards',
	'closewikis-log-close' => 'pechou "$2"',
	'closewikis-log-reopen' => 'volveu abrir "$2"',
	'right-editclosedwikis' => 'Editar wikis pechados',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'closewikis-desc' => 'Zmóžnja začinjenje wikijowych sydłow we wikijowych farmach',
	'closewikis-closed-default' => 'Tutón wikij je začinjeny',
	'closewikis-page' => 'Wiki začinić',
	'closewikis-page-close' => 'Wiki začinić',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Zwobraznjena přičina:',
	'closewikis-page-close-reason' => 'Protokolowana přičina:',
	'closewikis-page-close-submit' => 'Začinić',
	'closewikis-page-close-success' => 'Wiki wuspěšnje začinjeny',
	'closewikis-page-reopen' => 'Wiki zaso wočinić',
	'closewikis-page-reopen-wiki' => 'wiki:',
	'closewikis-page-reopen-reason' => 'Přičina:',
	'closewikis-page-reopen-submit' => 'Zaso wočinić',
	'closewikis-page-reopen-success' => 'Wiki wuspěšnje zaso wočinjeny',
	'closewikis-page-err-nowiki' => 'Njepłaćiwy wiki podaty',
	'closewikis-page-err-closed' => 'Wiki je hižo začinjeny',
	'closewikis-page-err-opened' => 'Wiki njeje začinjeny',
	'closewikis-log' => 'Protokol začinjenjow wikijow',
	'closewikis-log-header' => 'To je protokol wšěch začinjenjow a zasowočinjenjow wikijow, kotrež su stewardźa činili.',
	'closewikis-log-close' => 'je $2 začinił',
	'closewikis-log-reopen' => 'je $2 zaso wočinił',
	'right-editclosedwikis' => 'Začinjene wikije wobdźěłać',
);

/** Interlingua (Interlingua)
 * @author Malafaya
 * @author McDutchie
 */
$messages['ia'] = array(
	'closewikis-desc' => 'Permitte clauder sitos wiki in fermas de wikis.',
	'closewikis-closed-default' => 'Iste wiki es claudite',
	'closewikis-page' => 'Clauder wiki',
	'closewikis-page-close' => 'Clauder wiki',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Motivo (monstrate):',
	'closewikis-page-close-reason' => 'Motivo (registrate):',
	'closewikis-page-close-submit' => 'Clauder',
	'closewikis-page-close-success' => 'Wiki claudite con successo',
	'closewikis-page-reopen' => 'Reaperir wiki',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Motivo:',
	'closewikis-page-reopen-submit' => 'Reaperir',
	'closewikis-page-reopen-success' => 'Wiki reaperite con successo',
	'closewikis-page-err-nowiki' => 'Le wiki specificate es invalide',
	'closewikis-page-err-closed' => 'Iste wiki es ja claudite',
	'closewikis-page-err-opened' => 'Le wiki non es claudite',
	'closewikis-log' => 'Registro de clausura de wikis',
	'closewikis-log-header' => 'Ecce un registro de tote le clausuras e reaperturas de wikis facite per stewards',
	'closewikis-log-close' => 'claudeva $2',
	'closewikis-log-reopen' => 'reaperiva $2',
	'right-editclosedwikis' => 'Modificar wikis claudite',
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

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'closewikis-desc' => 'Määt et müjjelesch, enkel Wikis en ene Wiki-Farm zohzemaache.',
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

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'closewikis-desc' => 'Tillater stenging av wikier i wikisamlinger',
	'closewikis-closed-default' => 'Denne wikien er stengt',
	'closewikis-page' => 'Steng wiki',
	'closewikis-page-close' => 'Steng wiki',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Årsak (vises):',
	'closewikis-page-close-reason' => 'Årsak (logges):',
	'closewikis-page-close-submit' => 'Steng',
	'closewikis-page-close-success' => 'Wiki stengt',
	'closewikis-page-reopen' => 'Åpne wiki',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Årsak:',
	'closewikis-page-reopen-submit' => 'Åpne',
	'closewikis-page-reopen-success' => 'Wiki åpnet',
	'closewikis-page-err-nowiki' => 'Ugyldig wiki oppgitt',
	'closewikis-page-err-closed' => 'Wikien er allerede stengt',
	'closewikis-page-err-opened' => 'Wikien er ikke stengt',
	'closewikis-log' => 'Logg for stenging av wikier',
	'closewikis-log-header' => 'Her er en logg over alle wikistenginger og -åpninger gjort av forvaltere',
	'closewikis-log-close' => 'stengte $2',
	'closewikis-log-reopen' => 'åpnet $2',
	'right-editclosedwikis' => 'Redigere stengte wikier',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'closewikis-desc' => 'Permet de clausurar los sits wiki dins aqueste gestionari de wiki',
	'closewikis-closed-default' => 'Aqueste wiki es claus',
	'closewikis-page' => 'Clausar lo wiki',
	'closewikis-page-close' => 'Clausurar lo wiki',
	'closewikis-page-close-wiki' => 'Wiki :',
	'closewikis-page-close-dreason' => 'Motiu (afichat) :',
	'closewikis-page-close-reason' => 'Motiu (enregistrat) :',
	'closewikis-page-close-submit' => 'Clausurar',
	'closewikis-page-close-success' => 'Wiki claus amb succès',
	'closewikis-page-reopen' => 'Tornar dobrir lo wiki',
	'closewikis-page-reopen-wiki' => 'Wiki :',
	'closewikis-page-reopen-reason' => 'Motiu :',
	'closewikis-page-reopen-submit' => 'Tornar dobrir',
	'closewikis-page-reopen-success' => 'Lo wiki es estat redobert amb succès',
	'closewikis-page-err-nowiki' => 'Lo wiki indicat es incorrècte',
	'closewikis-page-err-closed' => 'Aqueste wiki ja es estat clausurat',
	'closewikis-page-err-opened' => 'Wiki pas clausurat',
	'closewikis-log' => 'Jornal de clausura dels wiki',
	'closewikis-log-header' => 'Vaquí un jornal de totas las tampaduras e redoberturas de wiki fachas pels stewards',
	'closewikis-log-close' => 'a clausurat $2',
	'closewikis-log-reopen' => 'a redobert $2',
	'right-editclosedwikis' => 'Modificar los wikis clausurats',
);

/** Polish (Polski)
 * @author Jwitos
 */
$messages['pl'] = array(
	'closewikis-page-close-submit' => 'Zamknij',
);

/** Portuguese (Português)
 * @author Sir Lestaty de Lioncourt
 */
$messages['pt'] = array(
	'closewikis-desc' => 'Permite fechar uma wiki em sites com múltiplos wikis',
	'closewikis-closed-default' => 'Esta wiki está fechada',
	'closewikis-page' => 'Fechar wiki',
	'closewikis-page-close' => 'Fechar wiki',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Razão (exibida):',
	'closewikis-page-close-reason' => 'Razão (registrada):',
	'closewikis-page-close-submit' => 'Fechar',
	'closewikis-page-close-success' => 'Wiki foi fechada com sucesso',
	'closewikis-page-reopen' => 'Reabrir wiki',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Razão:',
	'closewikis-page-reopen-submit' => 'Reabrir',
	'closewikis-page-reopen-success' => 'Wiki reaberta com sucesso',
	'closewikis-page-err-nowiki' => 'A wiki especificada é inválida',
	'closewikis-page-err-closed' => 'Wiki já está fechada',
	'closewikis-page-err-opened' => 'Esta wiki não está fechada',
	'closewikis-log' => 'Registro de Wikis fechadas',
	'closewikis-log-header' => 'Aqui está um registro de todas as wikis que foram fechadas ou reabertas por stewards',
	'closewikis-log-close' => 'fechada $2',
	'closewikis-log-reopen' => 'reaberta $2',
	'right-editclosedwikis' => 'Editar wikis fechadas',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 */
$messages['ro'] = array(
	'closewikis-page-close-submit' => 'Închide',
	'closewikis-page-reopen' => 'Redeschide wiki',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Motiv:',
	'closewikis-page-reopen-submit' => 'Redeschide',
);

/** Russian (Русский)
 * @author Ferrer
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'closewikis-closed-default' => 'Эта вики закрыта',
	'closewikis-page' => 'Закрыть вики',
	'closewikis-page-close' => 'Закрыть вики',
	'closewikis-page-close-wiki' => 'Вики:',
	'closewikis-page-close-dreason' => 'Причина (отображаемая):',
	'closewikis-page-close-reason' => 'Причина (для журнала):',
	'closewikis-page-close-submit' => 'Закрыть',
	'closewikis-page-reopen-wiki' => 'Вики:',
	'closewikis-page-reopen-reason' => 'Причина:',
	'closewikis-log' => 'Журнал закрытия вики',
	'closewikis-log-close' => 'закрыто $2',
	'right-editclosedwikis' => 'Править закрытые вики',
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

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'closewikis-desc' => 'Moaket dät Sluuten muugelk fon eenpelde Wikis in ne Wikifarm',
	'closewikis-closed' => '$1',
	'closewikis-closed-default' => 'Dit Wiki is sleeten',
	'closewikis-page' => 'Wiki sluute',
	'closewikis-page-close' => 'Wiki sluute',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Anwieseden Gruund:',
	'closewikis-page-close-reason' => 'Gruund, die der in dät Logbouk iendrain wäd:',
	'closewikis-page-close-submit' => 'Sluute',
	'closewikis-page-close-success' => 'Wiki mäd Ärfoulch sleeten.',
	'closewikis-page-reopen' => 'Wiki wier eepenje',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Gruund:',
	'closewikis-page-reopen-submit' => 'Wier eepenje',
	'closewikis-page-reopen-success' => 'Wiki mäd Ärfoulch wier eepend',
	'closewikis-page-err-nowiki' => 'Uungultich Wiki anroat',
	'closewikis-page-err-closed' => 'Wiki is al sleeten',
	'closewikis-page-err-opened' => 'Wiki is nit sleeten',
	'closewikis-log' => 'Wikisluutengs-Logbouk',
	'closewikis-log-header' => 'Dit Logbouk wiest aal Sluutengen un Wiereepengen fon Wikis truch Stewards oun.',
	'closewikis-log-close' => 'sloot $2',
	'closewikis-log-reopen' => 'eepende $2 wier',
	'right-editclosedwikis' => 'Sleetene Wikis beoarbaidje',
);

/** Swedish (Svenska)
 * @author M.M.S.
 * @author Najami
 */
$messages['sv'] = array(
	'closewikis-closed-default' => 'Den här wikin är stängd',
	'closewikis-page' => 'Stäng wiki',
	'closewikis-page-close' => 'Stäng wiki',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Anledning (visas):',
	'closewikis-page-close-submit' => 'Stäng',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Anledning:',
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

