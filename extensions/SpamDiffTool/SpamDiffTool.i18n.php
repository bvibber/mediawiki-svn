<?php
/**
 * Internationalisation file for extension SpamDiffTool.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'spamdifftool' => 'Manage Spam Blacklist',
	'spamdifftool_cantedit' => 'Sorry - you don\'t have permission to edit the Spam Blacklist.',
	'spamdifftool_notext' => 'There is no text to add to the Spam Blacklist. Click <a href=\'$1\'>here</a> to continue.',
	'spamdifftool_confirm' => 'Confirm that you want to add these entries to the Spam Blacklist. (Click <a href=\'$1\' target=\'new\'>here</a> to report a problem.)',
	'spamdifftool_summary' => 'Adding to Spam Blacklist',
	'spamdifftool_urls_detected' => 'The following URLs were detected in the edit(s), which ones would you like to add to the Spam Blacklist? These options order from more restrictive to less restrictive, blocking the entire domain will block all links to anything coming from that domain. <br /><br />Be sure not to block entire domains that host user accounts, like blogpost.com, geocities.com, etc.',
	'spamdifftool_no_urls_detected' => 'No urls were detected. Click <a href=\'$1\'>here</a> to return.',
	'spamdifftool_spam_link_text' => 'add to spam',
	'spamdifftool_option_domain' => 'all from this domain',
	'spamdifftool_option_subdomain' => 'all from this subdomain',
	'spamdifftool_option_directory' => 'this subdomain and directory',
	'spamdifftool_option_none' => 'nothing',
	'spamdifftool_block' => 'Block:',
	'spamdifftool_submit_buttom' => 'Submit',
	);

$messages['ar'] = array(
	'spamdifftool' => 'التحكم في قائمة السبام السوداء',
	'spamdifftool_cantedit' => 'عذرا - أنت لا تمتلك الصلاحية لتعديل قائمة السبام السوداء.',
	'spamdifftool_notext' => 'لا يوجد نص لإضافته إلى قائمة السبام السوداء. اضغط <a href=\'$1\'>هنا</a> للمتابعة.',
	'spamdifftool_confirm' => 'أكد أنك تريد إضافة هذه المدخلات إلى قائمة السبام السوداء. (اضغط <a href=\'$1\' target=\'new\'>هنا</a> للإبلاغ عن مشكلة.)',
	'spamdifftool_summary' => 'جاري الإضافة إلى قائمة السبام السوداء',
	'spamdifftool_urls_detected' => 'المسارات التالية تم التعرف عليها في التعديل(ات)، أيها تود إضافتها إلى قائمة السبام السوداء؟ هذه الخيارات مرتبة من الأكثر منعا إلى الأقل منعا، منع النطاق بأكمله سيمنع كل الوصلات لأي شيء من هذا النطاق. <br/><br/>تأكد من عدم منع كل النطاقات التي تستضيف حسابات مستخدمين، مثل blogpost.com، geocities.com، إلى آخره.',
	'spamdifftool_no_urls_detected' => 'لم يتم التعرف على أية مسارات. اضغط <a href=\'$1\'>هنا</a> للعودة.',
	'spamdifftool_spam_link_text' => 'الكل إلى السبام',
	'spamdifftool_option_domain' => 'الكل من هذا النطاق',
	'spamdifftool_option_subdomain' => 'الكل من هذا النطاق الفرعي',
	'spamdifftool_option_directory' => 'هذا النطاق الفرعي والمجلد',
	'spamdifftool_option_none' => 'لا شيء',
	'spamdifftool_block' => 'منع:',
	'spamdifftool_submit_buttom' => 'تنفيذ',
);

$messages['bg'] = array(
	'spamdifftool_summary' => 'Добавяне към черния списък със спам',
	'spamdifftool_option_domain' => 'всичко от този домейн',
	'spamdifftool_option_subdomain' => 'всичко от този поддомейн',
	'spamdifftool_option_none' => 'нищо',
	'spamdifftool_block' => 'Блокиране:',
	'spamdifftool_submit_buttom' => 'Съхранение',
);

$messages['de'] = array(
	'spamdifftool'                  => 'Spam-Blacklist bearbeiten',
	'spamdifftool_cantedit'         => 'Du hast keine Berechitung zur Bearbeitung der Spam-Blacklist.',
	'spamdifftool_notext'           => 'Es gibt keinen Text, welcher der Spam-Blacklist hinzugefügt werden könnte. Klicke <a href=\'$1\'>hier</a> zum Forfahren.',
	'spamdifftool_confirm'          => 'Bestätige, dass du diese Einträge der Spam-Blacklist hinzufügen möchtest. (Klicke <a href=\'http://www.mediawiki.org/w/index.php?title=Talk:SpamDiffTool_Extension&action=edit&section=new\' target=\'new\'>hier</a>, um ein Problem zu melden.)',
	'spamdifftool_summary'          => 'Zur Spam-Blacklist hinzufügen',
	'spamdifftool_urls_detected'    => 'Die folgenden URLs wurden in der Bearbeitung gefunden, welche davon möchtest du der Spam-Blacklist hinzufügen? 
	Die Reihenfolge geht von sehr einschränkend bis weniger einschränkend, das Eintragen einer ganzen Domain blockiert alle Links, die von dieser Domain kommen.<br /><br />
	Stelle sicher, dass du nicht komplette Domains blockirst, die separate Benutzerinhalte bereitstellen, wie z. B. blogpost.com, geocities.com usw.',
	'spamdifftool_no_urls_detected' => 'Es wurden keine URLs gefunden. Klicke <a href=\'$1\'>hier</a>, um zurückzugehen.',
	'spamdifftool_spam_link_text'   => 'zu Spam hinzufügen',
	'spamdifftool_option_domain'    => 'alle von dieser Domain',
	'spamdifftool_option_subdomain' => 'alle von dieser Subdomain',
	'spamdifftool_option_directory' => 'diese Subdomain und das Verzeichnis',
	'spamdifftool_option_none'      => 'nichts',
	'spamdifftool_block'            => 'Sperre:',
	'spamdifftool_submit_buttom'    => 'Speichern',
	);

$messages['fr'] = array(
	'spamdifftool' => 'Gestion de la Liste Noire des Spams',
	'spamdifftool_cantedit' => 'Désolé - Vous n’avez pas la permission d’éditer la Liste Noire des Spams.',
	'spamdifftool_notext' => 'Il n’y a pas de texte à ajouter à la Liste Noire des Spams. Cliquez <a href=\'$1\'>ici</a> pour continuer. ',
	'spamdifftool_confirm' => 'Confirmez que vous voulez ajouter ces entrées dans la Liste Noire des Spams. (Cliquez <a href=\'$1\' target=\'new\'>ici</a> pour signaler tout problème.)',
	'spamdifftool_summary' => 'Ajouté à la Liste Noire des Spams',
	'spamdifftool_urls_detected' => 'Les URLs suivantes ont été détectées dans ces éditions. Quelles sont lesquelles que vous voulez ajouter à la Liste Noire des Spams ? Ces options vont des plus restrictives vers les moins restrictives. Le blocage d’un nom de domaine entier bloquera tous les liens provenant de celui-ci.<br/><br/>Assurez-vous de ne pas bloquer des domaines entiers que détiennent certains compteurs utilisateurs tels que blogpost.com, geocities.com, etc. ',
	'spamdifftool_no_urls_detected' => 'Aucune URL n’a été détectée. Cliquez <a href=\'$1\'>ici</a> pour revenir en arrière.',
	'spamdifftool_spam_link_text' => 'ajouter aux spams',
	'spamdifftool_option_domain' => 'tout depuis ce domaine',
	'spamdifftool_option_subdomain' => 'tout depuis ce sous-domaine',
	'spamdifftool_option_directory' => 'ce sous-domaine et ce répertoire',
	'spamdifftool_option_none' => 'néant',
	'spamdifftool_block' => 'Bloquer :',
	'spamdifftool_submit_buttom' => 'Soumettre',
);

/** Galician (Galego)
 * @author Alma
 */
$messages['gl'] = array(
	'spamdifftool'                  => 'Administrar a Listaxe Negra de Spam',
	'spamdifftool_cantedit'         => 'Sentímolo - vostede non ten permisos para editar na Listaxe Negra de Spam.',
	'spamdifftool_notext'           => "Non hai texto para engadir a Listaxe negra de Spam. Prema <a href='$1'>here</a> para continuar.",
	'spamdifftool_summary'          => 'Engadindo a Listaxe Negra de Spam',
	'spamdifftool_spam_link_text'   => 'engadir a spam',
	'spamdifftool_option_domain'    => 'todo desde este dominio',
	'spamdifftool_option_subdomain' => 'todo desde este subdominio',
	'spamdifftool_option_none'      => 'nada',
	'spamdifftool_block'            => 'Bloqueo:',

);

$messages['hsb'] = array(
	'spamdifftool' => 'Spamowu čornu lisćinu zrjadować',
	'spamdifftool_cantedit' => 'Bohužel nimaš dowolenje spamowu čornu lisćinu wobdźěłować.',
	'spamdifftool_notext' => 'Njeje žadyn tekst, kotryž móhł so spamowej čornej lisćinje přidać. Klikń <href=\'$1\'>sem</a>, zo by pokročował.',
	'spamdifftool_confirm' => 'Potwjerdź, zo chceš tute zapiski spamowej čornej lisćinje přidać. (Klikń <a href=\'$1\' target=\'new\'>sem</a>, zo by wo problemje rozprawjał.)',
	'spamdifftool_summary' => 'Spamowej čornej lisćinje přidać',
	'spamdifftool_urls_detected' => 'Slědowace URL buchu w změnach wotkryte, kotre z nich chceš rady spamowej čornej lisćinje přidać? Tute opcije rjaduja wot bóle restriktiwne do mjenje restriktiwne, blokowanje cyłeje domejny budźe wšě wotkazy k něčemu, štož z tuteje domejny přińdźe, blokować.<br /><br />Zawěsć so, zo njeby cyle domejny blokował, kotrež wužiwarske konta hospoduja, kaž blogpost.com, geocities.com atd.',
	'spamdifftool_no_urls_detected' => 'Njebuchu žane url wotkryte. Klikń <a href=\'$1\'>sem</a>, zo by so wróćił.',
	'spamdifftool_spam_link_text' => 'k spamej přidać',
	'spamdifftool_option_domain' => 'wšo z tuteje domejny',
	'spamdifftool_option_subdomain' => 'wšě z tuteje poddomejny',
	'spamdifftool_option_directory' => 'tutu poddomejnu a tutón zapis',
	'spamdifftool_option_none' => 'ničo',
	'spamdifftool_block' => 'Blokować:',
	'spamdifftool_submit_buttom' => 'Wotesłać',
);

$messages['nl'] = array(
	'spamdifftool' => 'Zwarte Lijst beheren',
	'spamdifftool_cantedit' => 'Sorry - u heeft geen toelating om de Zwarte Lijst tegen Spam te bewerken.',
	'spamdifftool_notext' => 'Er is geen tekst om toe te voegen aan de Zwarte Lijst tegen spam. Klik <a href=\'$1\'>hier</a> om door te gaan.',
	'spamdifftool_confirm' => 'Bevestig dat u deze namen aan de Zwarte Lijst tegen spam  wil toevoegen. (Klik <a href=\'$1\' target=\'new\'>hier</a> om een probleem te melden.)',
	'spamdifftool_summary' => 'Toevoegen aan de Zwarte Lijst tegen spam',
	'spamdifftool_no_urls_detected' => 'Er werden geen URL\'s gevonden. Klik <a href=\'$1\'>hier</a> om terug te keren.',
	'spamdifftool_spam_link_text' => 'toevoegen aan spam',
	'spamdifftool_option_domain' => 'alles van dit domein',
	'spamdifftool_option_subdomain' => 'alles van dit subdomein',
	'spamdifftool_option_directory' => 'dit subdomein en deze map',
	'spamdifftool_option_none' => 'niets',
	'spamdifftool_block' => 'Blokkeren:',
	'spamdifftool_submit_buttom' => 'OK',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'spamdifftool'                  => 'Gestion de la Lista Negra dels Spams',
	'spamdifftool_cantedit'         => 'O planhèm - Avètz pas la permission d’editar la Lista Negra dels Spams.',
	'spamdifftool_notext'           => "I a pas de tèxt d'ajustar a la Lista Negra dels Spams. Clicatz <a href='$1'>aicí</a> per contunhar.",
	'spamdifftool_confirm'          => "Confirmatz que volètz ajustar aquestas entradas dins la Lista Negra dels Spams. (Clicatz <a href='$1' target='new'>aicí</a> per senhalar tot problèma.)",
	'spamdifftool_summary'          => 'Ajustat a la Lista Negra dels Spams',
	'spamdifftool_urls_detected'    => "Las URLs seguentas son estadas detectadas dins aquestas edicions. Qualas son las que volètz ajustar a la Lista Negra dels Spams ? Aquestas opcions van de las mai restrictivas vèrs las mens restrictivas. Lo blocatge d’un nom de domeni entièr blocarà totes los ligams provenent d'aqueste.<br/><br/>Asseguratz-vos de blocar pas de domenis entièrs que detenon cèrts comptaires d'utilizaires tals coma blogpost.com, geocities.com, etc.",
	'spamdifftool_no_urls_detected' => "Cap d'URL es pas estada detectada. Clicatz <a href='$1'>aicí</a> per tornar en rèire",
	'spamdifftool_spam_link_text'   => 'ajustar als spams',
	'spamdifftool_option_domain'    => 'tot dempuèi aqueste domeni',
	'spamdifftool_option_subdomain' => 'tot dempuèi aqueste sosdomeni',
	'spamdifftool_option_directory' => 'aqueste sosdomeni e aqueste repertòri',
	'spamdifftool_option_none'      => 'Nonrés',
	'spamdifftool_block'            => 'Blocar :',
	'spamdifftool_submit_buttom'    => 'Sometre',
);
