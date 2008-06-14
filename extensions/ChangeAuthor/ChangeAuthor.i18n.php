<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Roan Kattouw <roan.kattouw@home.nl>
 * @copyright Copyright (C) 2007 Roan Kattouw
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 *
 * An extension that allows changing the author of a revision
 * Written for the Bokt Wiki <http://www.bokt.nl/wiki/> by Roan Kattouw <roan.kattouw@home.nl>
 * For information how to install and use this extension, see the README file.
 *
 */
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the extension file directly.
if (!defined('MEDIAWIKI')) {
	echo <<<EOT
To install the ChangeAuthor extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/ChangeAuthor/ChangeAuthor.setup.php" );
EOT;
	exit(1);
}

$messages = array();

$messages['en'] = array(
	'changeauthor' => 'Change revision author',
	'changeauthor-desc' => 'Allows changing a revision\'s author',
	'changeauthor-short' => 'ChangeAuthor', # don't translate or duplicate this message to other languages
	'changeauthor-title' => 'Change the author of a revision',
	'changeauthor-search-box' => 'Search revisions',
	'changeauthor-pagename-or-revid' => 'Page name or revision ID:',
	'changeauthor-pagenameform-go' => 'Go',
	'changeauthor-comment' => 'Comment:',
	'changeauthor-changeauthors-multi' => 'Change author(s)',
	'changeauthor-explanation-multi' => 'With this form you can change revision authors. Simply change one or more usernames in the list below, add a comment (optional) and click the \'Change author(s)\' button.',
	'changeauthor-changeauthors-single' => 'Change author',
	'changeauthor-explanation-single' => 'With this form you can change a revision author. Simply change the username below, add a comment (optional) and click the \'Change author\' button.',
	'changeauthor-invalid-username' => 'Invalid username "$1".',
	'changeauthor-nosuchuser' => 'No such user "$1".',
	'changeauthor-revview' => 'Revision #$1 of $2',
	'changeauthor-nosuchtitle' => 'There is no page called "$1".',
	'changeauthor-weirderror' => 'A very strange error occurred. Please retry your request. If this error keeps showing up, the database is probably broken.',
	'changeauthor-invalidform' => 'Please use the form provided by Special:ChangeAuthor rather than a custom form.',
	'changeauthor-success' => 'Your request has been processed successfully.',
	'changeauthor-logentry' => 'Changed author of $2 of $1 from $3 to $4',
	'changeauthor-logpagename' => 'Author change log',
	'changeauthor-logpagetext' => '',
	'changeauthor-rev' => 'r$1',
);

/** Aragonese (Aragonés)
 * @author Juanpabl
 */
$messages['an'] = array(
	'changeauthor'                      => "Cambiar l'autor d'a edizión",
	'changeauthor-title'                => "Cambiar l'autor d'una edizión",
	'changeauthor-search-box'           => 'Mirar edizions',
	'changeauthor-pagename-or-revid'    => "Nombre d'a pachina u ID d'a bersión:",
	'changeauthor-pagenameform-go'      => 'Ir-ie',
	'changeauthor-comment'              => 'Comentario:',
	'changeauthor-changeauthors-multi'  => 'Cambiar autor(s)',
	'changeauthor-explanation-multi'    => "Con iste formulario puede cambiar os autors d'a edizión. Nomás ha de cambiar uno u más nombres d'usuarios en lista que s'amuestra contino, adibir-ie un comentario (opzional) y punchar en o botón de 'Cambiar autor(s)'",
	'changeauthor-changeauthors-single' => 'Cambiar autor',
	'changeauthor-explanation-single'   => "Con iste formulario puede cambiar l'autor una edizión. Nomás ha de cambiar o nombre d'usuario que s'amuestra contino, adibir-ie un comentario (opzional) y punchar en o botón 'Cambiar autor'.",
	'changeauthor-invalid-username'     => 'Nombre d\'usuario "$1" no conforme.',
	'changeauthor-nosuchuser'           => 'No esiste l\'usuario "$1"',
	'changeauthor-revview'              => 'Edizión #$1 de $2',
	'changeauthor-nosuchtitle'          => 'No bi ha garra pachina tetulata "$1".',
	'changeauthor-weirderror'           => 'Ha escaizito una error á saber que estrania. Por fabor, torne á fer a demanda. Si ista error contina amaneixendo, talment a base de datos sía estricallata.',
	'changeauthor-invalidform'          => 'Por fabor, faiga serbir o formulario furnito en Special:ChangeAuthor millor que no atro presonalizato.',
	'changeauthor-success'              => "A suya demanda s'ha prozesato correutament.",
	'changeauthor-logentry'             => "S'ha cambiato l'autor d'a edizión $2 de $1 de $3 á $4",
	'changeauthor-logpagename'          => "Rechistro de cambeos d'autor",
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'changeauthor'                      => 'غير مؤلف النسخة',
	'changeauthor-desc'                 => 'يسمح بتغيير مؤلف نسخة',
	'changeauthor-title'                => 'غير مؤلف نسخة',
	'changeauthor-search-box'           => 'ابحث في النسخ',
	'changeauthor-pagename-or-revid'    => 'اسم الصفحة أو رقم النسخة:',
	'changeauthor-pagenameform-go'      => 'اذهب',
	'changeauthor-comment'              => 'تعليق:',
	'changeauthor-changeauthors-multi'  => 'غير المؤلف(ين)',
	'changeauthor-explanation-multi'    => "باستخدام هذه الاستمارة يمكنك تغيير مؤلفي النسخ. ببساطة غير واحدا أو أكثر من أسماء المستخدمين في القائمة بالأسفل ، أضف تعليقا (اختياري) واضغط على زر 'غير المؤلف(ين)'.",
	'changeauthor-changeauthors-single' => 'غير المؤلف',
	'changeauthor-explanation-single'   => "باستخدام هذه الاستمارة يمكنك تغيير مؤلف نسخة. ببساطة غير اسم اسم المستخدم بالأسفل، أضف تعليقا (اختياري) واضغط على زر 'غير المؤلف'.",
	'changeauthor-invalid-username'     => 'اسم مستخدم غير صحيح "$1".',
	'changeauthor-nosuchuser'           => 'لا يوجد مستخدم بالاسم "$1".',
	'changeauthor-revview'              => 'النسخة #$1 من $2',
	'changeauthor-nosuchtitle'          => 'لا توجد صفحة بالاسم "$1".',
	'changeauthor-weirderror'           => 'حدث خطأ غريب للغاية. من فضلك حاول القيام بطلبك مرة ثانية. لو استمر هذا الخطأ، إذا فقاعدة البيانات على الأرجح مكسورة.',
	'changeauthor-invalidform'          => 'من فضلك استخدم الاستمارة الموفرة بواسطة Special:ChangeAuthor بدلا من استمارة معدلة.',
	'changeauthor-success'              => 'طلبك تم الانتهاء منه بنجاح.',
	'changeauthor-logentry'             => 'غير مؤلف $2 ل$1 من $3 إلى $4',
	'changeauthor-logpagename'          => 'سجل تغيير المؤلفين',
	'changeauthor-rev'                  => 'ن$1',
);

/** Bulgarian (Български)
 * @author DCLXVI
 * @author Spiritia
 */
$messages['bg'] = array(
	'changeauthor'                      => 'Промяна на автора на редакция',
	'changeauthor-desc'                 => 'Позволява промяна на автора на редакцията',
	'changeauthor-title'                => 'Промяна на автора на редакция',
	'changeauthor-search-box'           => 'Търсене на редакция',
	'changeauthor-pagename-or-revid'    => 'Име на страница или номер на редакция:',
	'changeauthor-comment'              => 'Коментар:',
	'changeauthor-changeauthors-multi'  => 'Промяна на автор(ите)',
	'changeauthor-explanation-multi'    => "Формулярът по-долу служи за промяна на авторите на отделни редакции. Необходимо е да се промени едно или повече потребителско име от списъка по-долу, да се въведе коментар (незадължително) и натисне бутона 'Промяна на автор(ите)'.",
	'changeauthor-changeauthors-single' => 'Промяна на автора',
	'changeauthor-explanation-single'   => "Формулярът по-долу се използва за промяна на автора на редакция. Необходимо е да се промени потребителското име, да се въведе коментар (незадължително) и да се натисне бутона 'Промяна на автор(ите)'.",
	'changeauthor-invalid-username'     => 'Невалидно потребителско име "$1".',
	'changeauthor-nosuchuser'           => 'Не съществува потребител "$1".',
	'changeauthor-revview'              => 'Редакция #$1 от $2',
	'changeauthor-nosuchtitle'          => 'Не съществува страница "$1".',
	'changeauthor-weirderror'           => 'Възникна странна грешка. Опитайте отново; ако грешката се повтори, вероятно базата данни е повредена.',
	'changeauthor-success'              => 'Заявката беше изпълнена успешно.',
	'changeauthor-rev'                  => 'р$1',
);

/** Bengali (বাংলা)
 * @author Zaheen
 */
$messages['bn'] = array(
	'changeauthor'                      => 'সংশোধন লেখক পরিবর্তন',
	'changeauthor-desc'                 => 'কোন সংশোধনের লেখক পরিবর্তন করার সুযোগ দেয়',
	'changeauthor-title'                => 'কোন সংশোধনের লেখক পরিবর্তন করুন',
	'changeauthor-search-box'           => 'সংশোধনগুলিতে অনুসন্ধান',
	'changeauthor-pagename-or-revid'    => 'পাতার নাম বা সংশোধন আইডি:',
	'changeauthor-pagenameform-go'      => 'চলো',
	'changeauthor-comment'              => 'মন্তব্য:',
	'changeauthor-changeauthors-multi'  => 'লেখক(দের) পরিবর্তন করুন',
	'changeauthor-explanation-multi'    => "এই ফর্মটির সাহায্যে আপনি সংশোধনের লেখকদের পরিবর্তন করতে পারবেন। নিচের তালিকার এক বা একাধিক ব্যবহারকারী নাম পরিবর্তন করুন, একটি মন্তব্য যোগ করুন (ঐচ্ছিক) এবং 'লেখক(গণ) পরিবর্তন করা হোক' বোতামটিতে ক্লিক করুন।",
	'changeauthor-changeauthors-single' => 'লেখক পরিবর্তন',
	'changeauthor-explanation-single'   => "এই ফর্মটির সাহায্যে আপনি একটি সংশোধনের লেখক পরিবর্তন করতে পারবেন। নিচের ব্যবহারকারী নামটি পরিবর্তন করুন, একটি মন্তব্য যোগ করুন (ঐচ্ছিক) এবং 'লেখক পরিবর্তন করা হোক' বোতামটিতে ক্লিক করুন।",
	'changeauthor-invalid-username'     => '"$1" ব্যবহারকারী নামটি অবৈধ।',
	'changeauthor-nosuchuser'           => '"$1" নামে কোন ব্যবহারকারী নেই।',
	'changeauthor-revview'              => '$2-এর সংশোধন নং $1',
	'changeauthor-nosuchtitle'          => '"$1" শিরোনামের কোন পাতা নেই।',
	'changeauthor-weirderror'           => 'একটি খুবই অদ্ভুত ত্রুটি ঘটেছে। দয়া করে আপনার অনুরোধটি দিয়ে আবার চেষ্টে করুন। এই ত্রুটিটি যদি বারবার দেখাতে থাকে, তবে সম্ভবত ডাটাবেজ কাজ করছে না।',
	'changeauthor-invalidform'          => 'কাস্টম ফর্মের পরিবর্তে অনুগ্রহ করে Special:ChangeAuthor-এর দেয়া ফর্মটি ব্যবহার করুন।',
	'changeauthor-success'              => 'আপনার অনুরোধটি সফলভাবে প্রক্রিয়া করা হয়েছে।',
	'changeauthor-logentry'             => '$3 থেকে $1-এর $2-এর লেখক পরিবর্তন করে $4 করা হয়েছে',
	'changeauthor-logpagename'          => 'লেখক পরিবর্তন লগ',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'changeauthor-pagenameform-go' => 'Mont',
	'changeauthor-comment'         => 'Notenn :',
);

/** Czech (Česky)
 * @author Matěj Grabovský
 */
$messages['cs'] = array(
	'changeauthor'                      => 'Změnit autora revize',
	'changeauthor-desc'                 => 'Umožňuje změnit autora revize',
	'changeauthor-title'                => 'Změnit autora revize',
	'changeauthor-search-box'           => 'Hledat revize',
	'changeauthor-pagename-or-revid'    => 'Název stránky nebo ID revize:',
	'changeauthor-pagenameform-go'      => 'Vykonat',
	'changeauthor-comment'              => 'Komentář:',
	'changeauthor-changeauthors-multi'  => 'Změnit autora (autory)',
	'changeauthor-explanation-multi'    => 'Pomocí tohoto formuláře můžete změnit autora revize stránky. Jednoduše změňte jedno nebo více uživatelských jmen v seznamu níže, přidejte komentář (nepovinné) a klikněte na tlačítko „Změnit autora“.',
	'changeauthor-changeauthors-single' => 'Změnit autora',
	'changeauthor-explanation-single'   => 'Pomocí tohoto formuláře můžete změnit autora revize stránky. Jednoduše změňte jméno uživatele v seznamu níže, přidejte komentář (nepovinné) a klikněte na tlačítko „Změnit autora“.',
	'changeauthor-invalid-username'     => 'Neplatné uživatelské jméno: „$1“.',
	'changeauthor-nosuchuser'           => 'Uživatel „$1“ neexistuje.',
	'changeauthor-revview'              => 'Revize #$1 {{plural:$2|z|ze|z}} $2',
	'changeauthor-nosuchtitle'          => 'Stránka s názvem „$1“ neexistuje.',
	'changeauthor-weirderror'           => 'Vyskytla se velmi zvláštní chyba. Prosím, opakujte váš požadavek. Pokud se tato chyba bude vyskytovat i nadále, databáze je poškozená.',
	'changeauthor-invalidform'          => 'Prosím, použijte formulář Special:ChangeAuthor raději než vlastní formulář.',
	'changeauthor-success'              => 'Vaše požadavky byly úspěšně zpracovány.',
	'changeauthor-logentry'             => 'Autor $2 z $1 byl změněn z $3 na $4',
	'changeauthor-logpagename'          => 'Záznam změn autorů',
);

/** Danish (Dansk)
 * @author M.M.S.
 */
$messages['da'] = array(
	'changeauthor-pagenameform-go' => 'Gå',
);

$messages['de'] = array(
	'changeauthor' => 'Autor einer Version ändern',
	'changeauthor-title' => 'Autor einer Version ändern',
	'changeauthor-search-box' => 'Version suchen',
	'changeauthor-pagename-or-revid' => 'Seitenname oder Versionsnummer:',
	'changeauthor-pagenameform-go' => 'Suche',
	'changeauthor-comment' => 'Kommentar:',
	'changeauthor-changeauthors-multi' => 'Ändere Autor(en)',
	'changeauthor-explanation-multi' => 'Mit diesem Formular kannst du die Autoren der Versionen ändern. Ändere einfach einen oder mehrerer Autorenname in der Liste, ergänze einen Kommentar (optional) und klicke auf die „Autor ändern“-Schaltfläche',	
	'changeauthor-changeauthors-single' => 'Autor ändern',
	'changeauthor-explanation-single' => 'Mit diesem Formular kannst du den Autoren einer Version ändern. Ändere einfach den Autorenname in der Liste, ergänze einen Kommentar (optional) und klicke auf die „Autor ändern“-Schaltfläche',
	'changeauthor-invalid-username' => 'Ungültiger Benutzername „$1“.',
	'changeauthor-nosuchuser' => 'Es gibt keinen Benutzer „$1“.',
	'changeauthor-revview' => 'Version #$1 von $2',
	'changeauthor-nosuchtitle' => 'Es gibt keine Seite „$1“.',
	'changeauthor-weirderror' => 'Ein sehr seltener Fehler ist aufgetreten. Bitte wiederhole deine Änderung. Wenn dieser Fehler erneut auftritt, ist vermutlich die Datenbank zerstört.',
	'changeauthor-invalidform' => 'Bitte benutzer das Formular unter Special:ChangeAuthor.',
	'changeauthor-success' => 'Deine Änderung wurde erfolgreich durchgeführt.',
	'changeauthor-logentry' => 'änderte Autorenname der $2 von $1 von $3 auf $4',
	'changeauthor-logpagename' => 'Autorenname-Änderungslogbuch',
	'changeauthor-logpagetext' => '',
	'changeauthor-rev' => 'Version $1',
);

/** Ewe (Eʋegbe)
 * @author M.M.S.
 */
$messages['ee'] = array(
	'changeauthor-pagenameform-go' => 'Yi',
);

$messages['el'] = array(
	'changeauthor-comment' => 'Σχόλιο:',
	'changeauthor-invalid-username' => 'Άκυρο όνομα-χρήστη  "$1".',
	'changeauthor-rev' => 'r$1',#identical but defined
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'changeauthor-nosuchtitle' => 'Estas neniu pagxo titolata "$1".',
);

/** Finnish (Suomi)
 * @author Crt
 * @author Cimon Avaro
 * @author Siebrand
 */
$messages['fi'] = array(
	'changeauthor'                      => 'Muuta muokkausversion tekijä',
	'changeauthor-desc'                 => 'Mahdollistaa muokkausversion tekijän muuttamisen',
	'changeauthor-title'                => 'Muuta muokkausversion tekijä',
	'changeauthor-search-box'           => 'Hae muokkausversioita',
	'changeauthor-pagenameform-go'      => 'Siirry',
	'changeauthor-comment'              => 'Kommentti:',
	'changeauthor-changeauthors-single' => 'Muuta tekijä',
	'changeauthor-nosuchtitle'          => 'Sivua nimeltä ”$1” ei ole.',
	'changeauthor-success'              => 'Pyyntö on suoritettu onnistuneesti.',
);

/** French (Français)
 * @author Sherbrooke
 * @author Dereckson
 * @author Grondin
 * @author Urhixidur
 */
$messages['fr'] = array(
	'changeauthor'                      => "Changer l'auteur des révisions",
	'changeauthor-desc'                 => 'Permet de changer le nom de l’auteur d’une ou plusieurs modifications',
	'changeauthor-title'                => "Changer l'auteur d'une révision",
	'changeauthor-search-box'           => 'Rechercher des révisions',
	'changeauthor-pagename-or-revid'    => "Titre de l'article ou numéro de la révision :",
	'changeauthor-pagenameform-go'      => 'Aller',
	'changeauthor-comment'              => 'Commentaire :',
	'changeauthor-changeauthors-multi'  => 'Changer auteur(s)',
	'changeauthor-explanation-multi'    => "Avec ce formulaire, vous pouvez changer les auteurs des révisions. Modifiez un ou plusieurs noms d'usager dans la liste, ajoutez un commentaire (facultatif) et cliquez le bouton ''Changer auteur(s)''.",
	'changeauthor-changeauthors-single' => "Changer l'auteur",
	'changeauthor-explanation-single'   => "Avec ce formulaire, vous pouvez changer l'auteur d'une révision. Changez le nom d'auteur ci-dessous, ajoutez un commentaire (facultatif) et cliquez sur le bouton ''Changer l'auteur''.",
	'changeauthor-invalid-username'     => "Nom d'utilisateur « $1 » invalide",
	'changeauthor-nosuchuser'           => "Pas d'utilisateur « $1 »",
	'changeauthor-revview'              => 'Révision #$1 de $2',
	'changeauthor-nosuchtitle'          => "Il n'existe aucun article intitulé « $1 »",
	'changeauthor-weirderror'           => "Une erreur s'est produite. Prière d'essayer à nouveau. Si cette erreur est apparue à plusieurs reprises, la base de données est probablement corrompue.",
	'changeauthor-invalidform'          => "Prière d'utiliser le formulaire généré par Special:ChangeAuthor plutôt qu'un formulaire personnel",
	'changeauthor-success'              => 'Votre requête a été traitée avec succès.',
	'changeauthor-logentry'             => "Modification de l'auteur de $2 de $1 depuis $3 vers $4",
	'changeauthor-logpagename'          => "Journal des changements faits par l'auteur",
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'changeauthor'                      => 'Changiér l’ôtor de les vèrsions',
	'changeauthor-title'                => 'Changiér l’ôtor d’una vèrsion',
	'changeauthor-search-box'           => 'Rechèrchiér des vèrsions',
	'changeauthor-pagename-or-revid'    => 'Titro de la pâge ou numerô de la vèrsion :',
	'changeauthor-pagenameform-go'      => 'Alar',
	'changeauthor-comment'              => 'Comentèro :',
	'changeauthor-changeauthors-multi'  => 'Changiér ôtor(s)',
	'changeauthor-changeauthors-single' => 'Changiér l’ôtor',
	'changeauthor-invalid-username'     => 'Nom d’utilisator « $1 » envalido.',
	'changeauthor-nosuchuser'           => 'Pas d’utilisator « $1 ».',
	'changeauthor-revview'              => 'Vèrsion #$1 de $2',
	'changeauthor-nosuchtitle'          => 'Ègziste gins d’articllo avouéc lo titro « $1 ».',
	'changeauthor-success'              => 'Voutra requéta at étâ trètâ avouéc reusséta.',
	'changeauthor-logpagename'          => 'Jornal des changements fêts per l’ôtor',
	'changeauthor-rev'                  => 'v$1',
);

/** Galician (Galego)
 * @author Alma
 * @author Xosé
 */
$messages['gl'] = array(
	'changeauthor'                      => 'Mudar a revisión do autor',
	'changeauthor-title'                => 'Cambiar ao autor da revisión',
	'changeauthor-search-box'           => 'Procurar revisións',
	'changeauthor-pagename-or-revid'    => 'Nome da páxina ou ID da revisión:',
	'changeauthor-pagenameform-go'      => 'Adiante',
	'changeauthor-comment'              => 'Comentario:',
	'changeauthor-changeauthors-multi'  => 'Mudar autor(es)',
	'changeauthor-explanation-multi'    => "Con este formulario pode cambiar as revisións dos autores. Simplemente cambie un ou máis dos nomes dos usuarios na listaxe de embaixo, engada un comentario (opcional) e prema no botón de 'Mudar autor(es)'",
	'changeauthor-changeauthors-single' => 'Cambiar autor',
	'changeauthor-explanation-single'   => "Con este formulario pode cambiar a revisión do autor. Simplemente mude o nome do usuario embaixo, engada un comentario (opcional) e prema o botón de 'Mudar autor'",
	'changeauthor-invalid-username'     => 'Nome de usuario non válido "$1".',
	'changeauthor-nosuchuser'           => 'Non hai tal usuario "$1".',
	'changeauthor-revview'              => 'Revisión nº$1 de $2',
	'changeauthor-nosuchtitle'          => 'Non hai ningunha páxina que se chame "$1".',
	'changeauthor-weirderror'           => 'Produciuse un erro moi estraño. Realice outra vez a consulta. Se este erro sigue aparecendo, probabelmente a base de datos está mal.',
	'changeauthor-invalidform'          => 'Por favor, utilice o formulario fornecido por Especial:ChangeAuthor en vez dun formulario personalizado.',
	'changeauthor-success'              => 'A súa petición foi procesada con éxito.',
	'changeauthor-logentry'             => 'Cambie autor de $2 de $1 a $3 de $4',
	'changeauthor-logpagename'          => 'Rexistro dos cambios do autor',
	'changeauthor-rev'                  => 'r$1',
);

/** Croatian (Hrvatski)
 * @author Dnik
 * @author SpeedyGonsales
 */
$messages['hr'] = array(
	'changeauthor'                      => 'Promijenite autora inačice',
	'changeauthor-title'                => 'Promijeni autora inačice',
	'changeauthor-search-box'           => 'Pretraži inačice',
	'changeauthor-pagename-or-revid'    => 'Ime članka ili oznaka (ID) inačice:',
	'changeauthor-pagenameform-go'      => 'Kreni',
	'changeauthor-comment'              => 'Napomena:',
	'changeauthor-changeauthors-multi'  => 'Promijeni autora(e)',
	'changeauthor-explanation-multi'    => "Ovaj obrazac omogućava promjenu autora inačica. Jednostavno promijenite jedno iii više korisničkih imena u donjem popisu, dodajte neobaveznu napomenu i pritisnite tipku 'Promijeni autora(e)'.",
	'changeauthor-changeauthors-single' => 'Promijeni autora',
	'changeauthor-explanation-single'   => "Ovaj obrazac omogućava promjenu autora inačice. Jednostavno korisničko ime, dodajte neobaveznu napomenu i pritisnite tipku 'Promijeni autora'.",
	'changeauthor-invalid-username'     => 'Pogrešno ime suradnika "$1".',
	'changeauthor-nosuchuser'           => 'Ne postoji suradnik "$1".',
	'changeauthor-revview'              => 'Inačica $1 str. $2',
	'changeauthor-nosuchtitle'          => 'Nema članka koji se zove "$1".',
	'changeauthor-weirderror'           => 'Dogodila se vrlo čudna greška. Molimo, ponovite zahtjev. Ako se greška ponovi, baza podataka je vjerojatno oštećena.',
	'changeauthor-invalidform'          => 'Molimo koristite obrazac na Special:ChangeAuthor umjesto vlastitog (custom) obrasca.',
	'changeauthor-success'              => 'Vaš zahtjev je uspješno obrađen.',
	'changeauthor-logentry'             => 'Promijenjen autor $2 stranice $1 iz $3 u $4',
	'changeauthor-logpagename'          => 'Evidencija promijena autora',
	'changeauthor-rev'                  => 'r$1',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'changeauthor'                      => 'Wersijoweho awtora změnić',
	'changeauthor-desc'                 => 'Dowola awtora wersije změnić',
	'changeauthor-title'                => 'Awtora wersije změnić',
	'changeauthor-search-box'           => 'Wersije pytać',
	'changeauthor-pagename-or-revid'    => 'Mjeno strony abo ID wersije:',
	'changeauthor-pagenameform-go'      => 'Dźi',
	'changeauthor-comment'              => 'Komentar:',
	'changeauthor-changeauthors-multi'  => '{{PLURAL:$1|awtora|awtorow|awtorow|awtorow}} změnić',
	'changeauthor-explanation-multi'    => "Z tutym formularom móžeš awtorow wersijow změnić. Změń prosće jedne wužiwarske mjeno abo wjacore wužiwarske mjena w lisćinje deleka, přidaj komentar (opcionalny) a klikń na tłóčatko '{{PLURAL:$1|Awtora|Awtorow|Awtorow|Awtorow}} zmenić'.",
	'changeauthor-changeauthors-single' => 'Awtora změnić',
	'changeauthor-explanation-single'   => "Z tutym formularom móžeš awtora wersije změnić. Změń prosće wužiwarske mjeno deleka, přidaj komentar (opcionalny) a klikń na tłóčatko 'Awtora změnić'.",
	'changeauthor-invalid-username'     => 'Njepłaćiwe wužiwarske mjeno "$1".',
	'changeauthor-nosuchuser'           => 'Wužiwar "$1" njeje.',
	'changeauthor-revview'              => 'Wersija #$1 wot $2',
	'changeauthor-nosuchtitle'          => 'Strona z mjenom "$1" njeeksistuje.',
	'changeauthor-weirderror'           => 'Jara dźiwny zmylk je wustupił. Prošu spytaj swoje požadanje znowa. Jeli so tutón zmylk zaso a zaso jewi, je najskerje datowa banka poškodźena.',
	'changeauthor-invalidform'          => 'Prošu wužij radšo formular z Special:ChangeAuthor hač wužiwarski formular.',
	'changeauthor-success'              => 'Waše požadanje je so wuspěšnje wobdźěłało.',
	'changeauthor-logentry'             => 'Změni so awtor wot $2 wot $1 z $3 do $4',
	'changeauthor-logpagename'          => 'Protokol wo změnach awtorow',
);

/** Hungarian (Magyar)
 * @author Bdanee
 * @author Dorgan
 */
$messages['hu'] = array(
	'changeauthor'                      => 'Változat szerzőjének megváltoztatása',
	'changeauthor-title'                => 'Adott változat szerzőjének megváltoztatása',
	'changeauthor-search-box'           => 'Változatok keresése',
	'changeauthor-pagename-or-revid'    => 'Oldalnév vagy változat-azonosító',
	'changeauthor-pagenameform-go'      => 'Menj',
	'changeauthor-comment'              => 'Megjegyzés:',
	'changeauthor-changeauthors-multi'  => 'Szerző(k) megváltoztatása',
	'changeauthor-explanation-multi'    => "Ezen a lapon megváltoztathatod adott változatok szerzőjét. Egyszerűen írd át a kívánt felhasználói neveket a lenti listában, írj megjegyzést (nem kötelező), majd kattints a 'Szerző(k) megváltoztatása' gombra.",
	'changeauthor-changeauthors-single' => 'Szerző megváltoztatása',
	'changeauthor-explanation-single'   => "Ezen a lapon megváltoztathatod a változat szerzőjét. Egyszerűen írd át a lenti felhasználói nevet, írj megjegyzést (nem kötelező), majd kattints a 'Szerző(k) megváltoztatása' gombra.",
	'changeauthor-invalid-username'     => 'A(z) "$1" egy érvénytelen felhasználónév.',
	'changeauthor-nosuchuser'           => 'Nincs „$1” nevű felhasználó',
	'changeauthor-revview'              => '$2 #$1 azonosítójú változata',
	'changeauthor-nosuchtitle'          => 'Nem létezik „$1” nevű oldal.',
	'changeauthor-weirderror'           => 'Egy nagyon furcsa hiba lépett fel. Próbáld újra a kérést. Ha a hiba továbbra is fennáll, az adatbázis valószínűleg hibás.',
	'changeauthor-invalidform'          => 'Kérlek saját űrlap helyett használd a Special:ChangeAuthor lapon található változatot.',
	'changeauthor-success'              => 'A kérésedet sikeresen végrehajtottam.',
	'changeauthor-logentry'             => '$1 $2 azonosítójú változatának szerzőjét $3 felhasználóról $4 felhasználóra cserélte',
	'changeauthor-logpagename'          => 'Szerző-változtatási napló',
);

/** Icelandic (Íslenska)
 * @author S.Örvarr.S
 */
$messages['is'] = array(
	'changeauthor-pagenameform-go'  => 'Áfram',
	'changeauthor-comment'          => 'Athugasemd:',
	'changeauthor-invalid-username' => 'Rangt notandanafn „$1“.',
	'changeauthor-nosuchuser'       => 'Notandi ekki til „$1“.',
	'changeauthor-nosuchtitle'      => 'Engin síða er nefnd „$1“.',
);

/** Japanese (日本語)
 * @author JtFuruhata
 */
$messages['ja'] = array(
	'changeauthor'                      => '特定版編集者の変更',
	'changeauthor-desc'                 => '特定版の編集者を変更できるようになります',
	'changeauthor-title'                => '特定版の編集者を変更',
	'changeauthor-search-box'           => '特定版の検索',
	'changeauthor-pagename-or-revid'    => 'ページ名または特定版ID:',
	'changeauthor-pagenameform-go'      => '検索',
	'changeauthor-comment'              => '編集内容の要約:',
	'changeauthor-changeauthors-multi'  => '変更',
	'changeauthor-explanation-multi'    => "このフォームから特定版編集者を変更することができます。一人または複数の利用者名を下のリストから選択し、編集内容の要約を付記し（任意です）、'変更' ボタンを押してください。",
	'changeauthor-changeauthors-single' => '変更',
	'changeauthor-explanation-single'   => "このフォームから特定版編集者を変更することができます。利用者名を下のリストから選択し、編集内容の要約を付記し（任意です）、'変更' ボタンを押してください。",
	'changeauthor-invalid-username'     => '"$1" は不正な利用者名です。',
	'changeauthor-nosuchuser'           => '"$1" という利用者は存在しません。',
	'changeauthor-revview'              => '$2 の特定版 #$1',
	'changeauthor-nosuchtitle'          => '"$1" という名前のページはありません。',
	'changeauthor-weirderror'           => '予測不能なエラーが発生しました。もう一度操作してください。それでもエラーが発生する場合は、恐らくデータベースが破壊されています。',
	'changeauthor-invalidform'          => '独自のフォームではなく、[[Special:ChangeAuthor|特定版編集者の変更]]が提供するフォームを利用してください。',
	'changeauthor-success'              => '要求された処理が完了しました。',
	'changeauthor-logentry'             => '編集者の変更 $1 の特定版 $2、$3 から $4 へ',
	'changeauthor-logpagename'          => '編集者の変更ログ',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 */
$messages['km'] = array(
	'changeauthor-pagenameform-go'      => 'ទៅ',
	'changeauthor-comment'              => 'វិចារ ៖',
	'changeauthor-changeauthors-multi'  => 'ផ្លាស់ប្តូរ អ្នកនិពន្ធ',
	'changeauthor-changeauthors-single' => 'ផ្លាស់ប្តូរ អ្នកនិពន្ធ',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'changeauthor-title'                => 'Auteur vun enger Versioun änneren',
	'changeauthor-search-box'           => 'Versioune sichen',
	'changeauthor-pagename-or-revid'    => 'Säitenumm oder Versiounsnummer:',
	'changeauthor-pagenameform-go'      => 'Lass',
	'changeauthor-comment'              => 'Bemierkung:',
	'changeauthor-changeauthors-multi'  => 'Auteur(en) änneren',
	'changeauthor-changeauthors-single' => 'Auteur änneren',
	'changeauthor-invalid-username'     => 'Benotzernumm „$1“ ass net gëlteg!',
	'changeauthor-nosuchuser'           => 'Et gëtt kee Benotzer "$1".',
	'changeauthor-revview'              => 'Versioun #$1 vun $2',
	'changeauthor-nosuchtitle'          => 'Et gëtt keng Säit mam Numm "$1".',
	'changeauthor-success'              => 'Är Ufro gouf mat succès duerchgefouert.',
	'changeauthor-rev'                  => 'Versioun $1',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 * @author Siebrand
 */
$messages['nl'] = array(
	'changeauthor'                      => 'Auteur versie wijzigen',
	'changeauthor-desc'                 => 'Maakt het mogelijk de auteur van een versie te wijzigen',
	'changeauthor-title'                => 'De auteur van een bewerkingsversie wijzigen',
	'changeauthor-search-box'           => 'Versies zoeken',
	'changeauthor-pagename-or-revid'    => 'Paginanaam of versienummer:',
	'changeauthor-pagenameform-go'      => 'Gaan',
	'changeauthor-comment'              => 'Toelichting:',
	'changeauthor-changeauthors-multi'  => 'Auteur(s) wijzigen',
	'changeauthor-explanation-multi'    => "Met dit formulier kunt u de auteur van een bewerkingsversie wijzigen. Wijzig simpelweg één of meer gebruikersnamen in de lijst hieronder, voeg een toelichting toe (niet verplicht) en klik op de knop 'Auteur(s) wijzigen'.",
	'changeauthor-changeauthors-single' => 'Auteur wijzigen',
	'changeauthor-explanation-single'   => "Met dit formulier kunt u de auteur van een bewerkingsversie wijzigen. Wijzig simpelweg de gebruikersnaam in het tekstvak hieronder, voeg een toelichting toe (niet verplicht) en klik op de knop 'Auteur wijzigen'.",
	'changeauthor-invalid-username'     => 'Ongeldige gebruikersnaam "$1".',
	'changeauthor-nosuchuser'           => 'Gebruiker "$1" bestaat niet.',
	'changeauthor-revview'              => 'Bewerking no. $1 van $2',
	'changeauthor-nosuchtitle'          => 'Er is geen pagina "$1".',
	'changeauthor-weirderror'           => 'Er is een erg vreemde fout opgetreden. Probeer het a.u.b. nogmaals. Als u deze foutmelding elke keer weer ziet, is er waarschijnlijk iets mis met de database.',
	'changeauthor-invalidform'          => 'Gebruik a.u.b. het formulier van Special:ChangeAuthor, in plaats van een aangepast formulier.',
	'changeauthor-success'              => 'Uw verzoek is succesvol verwerkt.',
	'changeauthor-logentry'             => 'Auteur van $2 van $1 gewijzigd van $3 naar $4',
	'changeauthor-logpagename'          => 'Auteurswijzigingenlogboek',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'changeauthor'                      => 'Endre revisjonens opphavsperson',
	'changeauthor-desc'                 => 'Tillater endring av revisjonsforfatter',
	'changeauthor-title'                => 'Endre en revisjons opphavsmann',
	'changeauthor-search-box'           => 'Søk i revisjoner',
	'changeauthor-pagename-or-revid'    => 'Sidenavn eller revisjons-ID:',
	'changeauthor-pagenameform-go'      => 'Gå',
	'changeauthor-comment'              => 'Kommentar:',
	'changeauthor-changeauthors-multi'  => 'Endre opphavsperson(er)',
	'changeauthor-explanation-multi'    => 'Med dette skjemaet kan du endre hvem som angis som opphavspersoner til revisjoner. Bare endre ett eller flere av brukernavnene i listen nedenfor, legg til en (valgfri) kommentar, og klikk knappen «Endre opphavsperson(er)».',
	'changeauthor-changeauthors-single' => 'Endre opphavsperson',
	'changeauthor-explanation-single'   => 'Med dette skjemaet kan du endre på hvem som angis som opphavspersonen til en revisjon. Bare endre brukernavnet nedenfor, legg til en (valgfri) kommentar, og klikk knappen «Endre opphavsperson».',
	'changeauthor-invalid-username'     => 'Ugyldig brukernavn «$1».',
	'changeauthor-nosuchuser'           => 'Ingen bruker ved navnet «$1».',
	'changeauthor-revview'              => 'Revisjon #$1 av $2',
	'changeauthor-nosuchtitle'          => 'Det er ingen side ved navn «$1».',
	'changeauthor-weirderror'           => 'En merkelig feil oppsto. Vennligst prøv igjen. Om denne feilen fortsetter, er det trolig noe gærent med databasen.',
	'changeauthor-invalidform'          => 'Bruk skjemaet på Special:ChangeAuthor i stedet for å bruke et egendefinert skjema.',
	'changeauthor-success'              => 'Forespørselen har blitt utført.',
	'changeauthor-logentry'             => 'Endret opphavsperson til $2 av $1 fra $3 til $4',
	'changeauthor-logpagename'          => 'Logg for opphavspersonsendringer',
);

/** Northern Sotho (Sesotho sa Leboa)
 * @author Mohau
 */
$messages['nso'] = array(
	'changeauthor'                   => 'Fetola  poeletšo ya mongwadi',
	'changeauthor-title'             => 'Fetola mongwadi wa poeletšo',
	'changeauthor-search-box'        => 'Fetleka dipoeletšo',
	'changeauthor-pagename-or-revid' => 'Leina la letlaka goba ID ya poeletšo:',
	'changeauthor-pagenameform-go'   => 'Sepela',
	'changeauthor-comment'           => 'Ahlaahla:',
	'changeauthor-revview'           => 'Poeletšo #$1 ya $2',
	'changeauthor-nosuchtitle'       => 'Gago letlakala lago bitšwa  "$1".',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'changeauthor'                      => "Cambiar l'autor de las revisions",
	'changeauthor-desc'                 => 'Permet de cambiar lo nom de l’autor d’una o mantuna modificacions',
	'changeauthor-title'                => "Cambiar l'autor d'una revision",
	'changeauthor-search-box'           => 'Recercar de revisions',
	'changeauthor-pagename-or-revid'    => "Títol de l'article o ID de revision :",
	'changeauthor-pagenameform-go'      => 'Anar',
	'changeauthor-comment'              => 'Comentari :',
	'changeauthor-changeauthors-multi'  => 'Cambiar autor(s)',
	'changeauthor-explanation-multi'    => "Amb aqueste formulari, podètz cambiar los autors de las revisions. Modificat un o mantun nom d'utilizaire dins la lista, ajustatz un comentari (facultatiu) e clicatz sul boton ''Cambiar autor(s)''.",
	'changeauthor-changeauthors-single' => "Cambiar l'autor",
	'changeauthor-explanation-single'   => "Amb aqueste formulari, podètz cambiar l'autor d'una revision. Cambiatz lo nom d'autor çaijós, ajustatz un comentari (facultatiu) e clicatz sul boton ''Cambiar l'autor''.",
	'changeauthor-invalid-username'     => "Nom d'utilizaire « $1 » invalid.",
	'changeauthor-nosuchuser'           => "Pas d'utilizaire « $1 »",
	'changeauthor-revview'              => 'Revision #$1 de $2',
	'changeauthor-nosuchtitle'          => 'Pas d\'article intitolat "$1"',
	'changeauthor-weirderror'           => "Una error s'es producha. Ensajatz tornamai. Se aquesta error es apareguda mantun còp, la banca de donadas es probablament corrompuda.",
	'changeauthor-invalidform'          => "Utilizatz lo formulari generit per Special:ChangeAuthor puslèu qu'un formulari personal",
	'changeauthor-success'              => 'Vòstra requèsta es estada tractada amb succès.',
	'changeauthor-logentry'             => "Modificacion de l'autor de $2 de $1 dempuèi $3 vèrs $4",
	'changeauthor-logpagename'          => "Jornal dels cambiaments faches per l'autor",
);

/** Polish (Polski)
 * @author Equadus
 */
$messages['pl'] = array(
	'changeauthor-changeauthors-single' => 'Zmień autora',
	'changeauthor-invalid-username'     => 'Niepoprawna nazwa użytkownika "$1".',
	'changeauthor-nosuchuser'           => 'Brak użytkownika "$1".',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'changeauthor-pagenameform-go' => 'ورځه',
	'changeauthor-nosuchtitle'     => 'داسې هېڅ کوم مخ نشته چې نوم يې "$1" وي.',
);

/** Portuguese (Português)
 * @author Malafaya
 * @author 555
 */
$messages['pt'] = array(
	'changeauthor'                      => 'Alterar autor de revisão',
	'changeauthor-desc'                 => 'Permite alterar o autor de uma revisão',
	'changeauthor-title'                => 'Alterar o autor de uma revisão',
	'changeauthor-search-box'           => 'Pesquisar revisões',
	'changeauthor-pagename-or-revid'    => 'Nome da página ou ID da revisão:',
	'changeauthor-pagenameform-go'      => 'Ir',
	'changeauthor-comment'              => 'Comentário:',
	'changeauthor-changeauthors-multi'  => 'Alterar autor(es)',
	'changeauthor-explanation-multi'    => "Através deste formulário, pode alterar os autores de revisões. Simplesmente mude um ou mais nomes de utilizador na lista abaixo, adicione um comentário (opcional) e clique no botão 'Alterar autor(es)'.",
	'changeauthor-changeauthors-single' => 'Alterar autor',
	'changeauthor-explanation-single'   => "Através deste formulário, pode alterar o autor de uma revisão. Simplesmente mude o nome de utilizador abaixo, adicione um comentário (opcional) e clique no botão 'Alterar autor'.",
	'changeauthor-invalid-username'     => 'Nome de utilizador "$1" inválido.',
	'changeauthor-nosuchuser'           => 'Utilizador "$1" não existe.',
	'changeauthor-revview'              => 'Revisão #$1 de $2',
	'changeauthor-nosuchtitle'          => 'Não existe nenhuma página chamada "$1".',
	'changeauthor-weirderror'           => 'Ocorreu um erro muito estranho. Por favor, tente o seu pedido de novo. Se este erro persistir, provavelmente a base de dados não está em boas condições.',
	'changeauthor-invalidform'          => 'Por favor, utilize o formulário fornecido em {{ns:special}}:ChangeAuthor em vez de um formulário personalizado.',
	'changeauthor-success'              => 'O seu pedido foi processado com sucesso.',
	'changeauthor-logentry'             => 'Alterado autor de $2 de $1, de $3 para $4',
	'changeauthor-logpagename'          => 'Registo de alterações de autor',
);

/** Russian (Русский)
 * @author .:Ajvol:.
 */
$messages['ru'] = array(
	'changeauthor'                      => 'Изменение автора правки',
	'changeauthor-desc'                 => 'Позволяет изменять автора правки',
	'changeauthor-title'                => 'Изменение автора правки',
	'changeauthor-search-box'           => 'Поиск правок',
	'changeauthor-pagename-or-revid'    => 'Название статьи или идентификатор правки:',
	'changeauthor-pagenameform-go'      => 'Поехали',
	'changeauthor-comment'              => 'Примечание:',
	'changeauthor-changeauthors-multi'  => 'Изменение автора(ов)',
	'changeauthor-explanation-multi'    => 'С помощью данной формы можно изменить авторов правок. Просто измените ниже одно или несколько имён участников, укажите пояснение (необязательно) и нажмите кнопку «Изменить автора(ов)».',
	'changeauthor-changeauthors-single' => 'Изменение автора',
	'changeauthor-explanation-single'   => 'С помощью данной формы можно изменить автора правки. Просто измените ниже имя участника, укажите пояснение (необязательно) и нажмите кнопку «Изменить автора».',
	'changeauthor-invalid-username'     => 'Недопустимое имя участника: $1',
	'changeauthor-nosuchuser'           => 'Отсутствует участник $1.',
	'changeauthor-revview'              => 'Версия #$1 из $2',
	'changeauthor-nosuchtitle'          => 'Не существует статьи с названием «$1».',
	'changeauthor-weirderror'           => 'Произошла очень странная ошибка. Пожалуйста, повторите ваш запрос. Если ошибка снова возникнет, то вероятно это означает, что база данных испорчена.',
	'changeauthor-invalidform'          => 'Пожалуйста, используйте форму на странице Special:ChangeAuthor, а не какую-либо другую.',
	'changeauthor-success'              => 'Запрос успешно обработан.',
	'changeauthor-logentry'             => 'Изменён автор $2 $1 с $3 на $4',
	'changeauthor-logpagename'          => 'Журнал изменения авторов',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'changeauthor'                      => 'Zmeniť autora revízie',
	'changeauthor-desc'                 => 'Umožňuje zmeniť autora revízie',
	'changeauthor-title'                => 'Zmeniť autora revízie',
	'changeauthor-search-box'           => 'Hľadať revízie',
	'changeauthor-pagename-or-revid'    => 'Názov stránky alebo ID revízie:',
	'changeauthor-pagenameform-go'      => 'Vykonať',
	'changeauthor-comment'              => 'Komentár:',
	'changeauthor-changeauthors-multi'  => 'Zmeniť autora (autorov)',
	'changeauthor-explanation-multi'    => 'Pomocou tohto formulára môžete zmeniť autora revízie stránky. Jednoducho zmeňte jedno alebo viac mien používateľov v zozname nižšie, pridajte komentár (nepovinné) a kliknite na tlačidlo „Zmeniť autora“.',
	'changeauthor-changeauthors-single' => 'Zmeniť autora',
	'changeauthor-explanation-single'   => 'Pomocou tohto formulára môžete zmeniť autora revízie stránky. Jednoducho zmeňte meno používateľa v zozname nižšie, pridajte komentár (nepovinné) a kliknite na tlačidlo „Zmeniť autora“.',
	'changeauthor-invalid-username'     => 'Neplatné meno používateľa: „$1“.',
	'changeauthor-nosuchuser'           => 'Taký používateľ neexistuje: „$1“.',
	'changeauthor-revview'              => 'Revízia #$1 z $2',
	'changeauthor-nosuchtitle'          => 'Stránka s názvom „$1“ neexistuje.',
	'changeauthor-weirderror'           => 'Vyskytla sa veľmi zvláštna chyba. Prosím, skúste vašu požiadavku znova. Ak sa táto chyba bude vyskytovať opakovane, databáza je zrejme poškodená.',
	'changeauthor-invalidform'          => 'Prosím, použite formulár Special:ChangeAuthor radšej ako vlastný formulár.',
	'changeauthor-success'              => 'Vaša požiadavka bola úspešne spracovaná.',
	'changeauthor-logentry'             => 'Autor $2 z $1 bol zmenený z $3 na $4',
	'changeauthor-logpagename'          => 'Záznam zmien autorov',
);

/** ћирилица (ћирилица)
 * @author Sasa Stefanovic
 */
$messages['sr-ec'] = array(
	'changeauthor-pagename-or-revid'    => 'Име странице или ИД ревизије:',
	'changeauthor-pagenameform-go'      => 'Иди',
	'changeauthor-comment'              => 'Коментар:',
	'changeauthor-changeauthors-multi'  => 'Промени аутора/ауторе',
	'changeauthor-changeauthors-single' => 'Промени аутора',
	'changeauthor-invalid-username'     => 'Погрешно корисничко име "$1".',
	'changeauthor-nosuchuser'           => 'Нема корисника "$1".',
	'changeauthor-revview'              => 'Ревизија #$1 или $2',
	'changeauthor-nosuchtitle'          => 'Не постоји страница под називом "$1".',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'changeauthor'                      => 'Autor fon ne Version annerje',
	'changeauthor-title'                => 'Autor fon ne Revision annerje',
	'changeauthor-search-box'           => 'Version säike',
	'changeauthor-pagename-or-revid'    => 'Siedennoome of Versionsnummer',
	'changeauthor-pagenameform-go'      => 'Säik',
	'changeauthor-comment'              => 'Kommentoar:',
	'changeauthor-changeauthors-multi'  => 'Uur Autor(e)',
	'changeauthor-explanation-multi'    => 'Mäd dit Formular koast du do Autore fon do Versione annerje. Annere eenfach aan of moor Autorennoomen in ju Lieste, moak n Kommentoar (optionoal) un klik ap dän „Autor annerje“-Knoop.',
	'changeauthor-changeauthors-single' => 'Autor annerje',
	'changeauthor-explanation-single'   => 'Mäd dit Formular koast du do Autoren fon ne Version annerje. Annerje eenfach dän Autorennoome in ju Lieste, beoarbaidje n Kommentoar (optionoal) un klik ap dän „Autor annerje“-Knoop.',
	'changeauthor-invalid-username'     => 'Uungultige Benutsernoome „$1“.',
	'changeauthor-nosuchuser'           => 'Dät rakt naan Benutser „$1“.',
	'changeauthor-revview'              => 'Version #$1 fon $2',
	'changeauthor-nosuchtitle'          => 'Dät rakt neen Siede „$1“.',
	'changeauthor-weirderror'           => 'N gjucht säildenen Failer is aptreeden. Wierhoal dien Annerenge. Wan dissen Failer fonnäien apträt, is fermoudelk ju Doatenboank fernäild.',
	'changeauthor-invalidform'          => 'Benutsje dät Formular unner Special:ChangeAuthor.',
	'changeauthor-success'              => 'Dian Annerenge wuude mäd Ärfoulch truchfierd.',
	'changeauthor-logentry'             => 'annerde Autorennoome fon ju $2 fon $1 fon $3 ap $4',
	'changeauthor-logpagename'          => 'Autorennoome-Annerengslogbouk',
	'changeauthor-rev'                  => 'Version $1',
);

/** Swedish (Svenska)
 * @author M.M.S.
 * @author Lejonel
 * @author Max sonnelid
 */
$messages['sv'] = array(
	'changeauthor'                      => 'Ändra upphovsman för sidversion',
	'changeauthor-desc'                 => 'Gör det möjligt att ändra upphovsman för sidversioner',
	'changeauthor-title'                => 'Ändra upphovsman för en sidversion',
	'changeauthor-search-box'           => 'Välj sidversion eller sida',
	'changeauthor-pagename-or-revid'    => 'Sidnamn eller versions-ID:',
	'changeauthor-pagenameform-go'      => 'Gå',
	'changeauthor-comment'              => 'Kommentar:',
	'changeauthor-changeauthors-multi'  => 'Ändra',
	'changeauthor-explanation-multi'    => 'Med hjälp av det här formuläret kan du ändra upphovsmännen för sidversioner. Byt ut ett eller flera av användarnamnen i listan härunder, skriv (om du vill) en kommentar och tryck sedan på knappen "Ändra".',
	'changeauthor-changeauthors-single' => 'Ändra',
	'changeauthor-explanation-single'   => 'Med hjälp av det här formuläret kan du ändra upphovsmannen för en sidversion. Byt ut användarnamnet härunder, skriv (om du vill) en kommentar och tryck sedan på knappen "Ändra".',
	'changeauthor-invalid-username'     => 'Användarnamnet "$1" är ogiltigt.',
	'changeauthor-nosuchuser'           => 'Det finns ingen användare med namnet "$1".',
	'changeauthor-revview'              => 'Version #$1 av $2',
	'changeauthor-nosuchtitle'          => 'Det finns ingen sida med namnet "$1".',
	'changeauthor-weirderror'           => 'Ett mycket konstigt fel inträffade. Försök en gång till. Om samma fel upprepas så är databasen förmodligen trasig.',
	'changeauthor-invalidform'          => 'Var vänlig använd formuläret som finns på [[Special:ChangeAuthor]], istället för ett formulär som någon annan skapat.',
	'changeauthor-success'              => 'Upphovsmansändringen är genomförd.',
	'changeauthor-logentry'             => 'ändrade upphovsman för $2 av $1 från $3 till $4',
	'changeauthor-logpagename'          => 'Upphovsmansändringslogg',
);

/** Telugu (తెలుగు)
 * @author Veeven
 * @author వైజాసత్య
 */
$messages['te'] = array(
	'changeauthor-search-box'           => 'కూర్పులను వెతకండి',
	'changeauthor-pagename-or-revid'    => 'పేజీ పేరు లేదా కూర్పు ఐడీ:',
	'changeauthor-pagenameform-go'      => 'వెళ్ళు',
	'changeauthor-comment'              => 'వ్యాఖ్య:',
	'changeauthor-changeauthors-multi'  => 'రచయిత(లు) ను మార్చు',
	'changeauthor-changeauthors-single' => 'రచయితను మార్చు',
	'changeauthor-invalid-username'     => '"$1" అనేది తప్పుడు వాడుకరి పేరు.',
	'changeauthor-nosuchuser'           => '"$1" అనే పేరుతో సభ్యులెవరూ లేరు.',
	'changeauthor-revview'              => '$2 యొక్క #$1వ కూర్పు',
	'changeauthor-nosuchtitle'          => '"$1" అనే పేరుతో పేజీ లేదు.',
	'changeauthor-success'              => 'మీ అభ్యర్థనని విజయవంతంగా పూర్తిచేసాం.',
);

/** Turkish (Türkçe)
 * @author Karduelis
 */
$messages['tr'] = array(
	'changeauthor-pagenameform-go'  => 'Git',
	'changeauthor-comment'          => 'Yorum:',
	'changeauthor-invalid-username' => '"$1" geçersiz kullanıcı.',
);

/** Volapük (Volapük)
 * @author Malafaya
 */
$messages['vo'] = array(
	'changeauthor-comment'              => 'Küpet:',
	'changeauthor-changeauthors-multi'  => 'Votükön lautani(s)',
	'changeauthor-changeauthors-single' => 'Votükön lautani',
);

