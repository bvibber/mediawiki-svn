<?php
/**
 * Internationalization file for the DeleteBatch extension.
 *
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Bartek Łapiński
 */
$messages['en'] = array(
	'deletebatch' => 'Delete batch of pages',
	'deletebatch-desc' => '[[Special:DeleteBatch|Delete a batch of pages]]',
	'deletebatch-button' => 'Delete',
	'deletebatch-here' => '<b>here</b>',
	'deletebatch-help' => 'Delete a batch of pages. You can either perform a single delete, or delete pages listed in a file.
Choose a user that will be shown in deletion logs.
Uploaded file should contain page name and optional reason separated by a "|" character in each line.',
	'deletebatch-caption' => 'Page list',
	'deletebatch-title' => 'Delete batch',
	'deletebatch-link-back' => 'Go back to the special page ',
	'deletebatch-as' => 'Run the script as',
	'deletebatch-both-modes' => 'Please choose either one specified page or a given list of pages.',
	'deletebatch-or' => '<b>or</b>',
	'deletebatch-page' => 'Pages to be deleted',
	'deletebatch-reason' => 'Reason for deletion',
	'deletebatch-processing' => 'deleting pages ',
	'deletebatch-from-file' => 'from file list',
	'deletebatch-from-form' => 'from form',
	'deletebatch-success-subtitle' => 'for $1',
	'deletebatch-link-back' => 'You can go back to the extension ',
	'deletebatch-omitting-nonexistant' => 'Omitting non-existing page $1.',
	'deletebatch-omitting-invalid' => 'Omitting invalid page $1.',
	'deletebatch-file-bad-format' => 'The file should be plain text',
	'deletebatch-file-missing' => 'Unable to read given file',
	'deletebatch-select-script' => 'delete page script',
	'deletebatch-select-yourself' => 'you',
	'deletebatch-no-page' => 'Please specify at least one page to delete OR choose a file containing page list.',
);

/** Message documentation (Message documentation)
 * @author Jon Harald Søby
 * @author Purodha
 */
$messages['qqq'] = array(
	'deletebatch-desc' => 'Short description of the :Deletebatch extension, shown in [[Special:Version]]. Do not translate or change links.',
	'deletebatch-button' => '{{Identical|Delete}}',
	'deletebatch-reason' => '{{Identical|Reason for deletion}}',
	'deletebatch-success-subtitle' => '{{Identical|For $1}}',
);

/** Goanese Konkani (Latin) (कोंकणी/Konknni  (Latin))
 * @author Deepak D'Souza
 */
$messages['gom-latn'] = array(
	'deletebatch-success-subtitle' => '$1 khatir',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'deletebatch' => 'حذف باتش من الصفحات',
	'deletebatch-desc' => '[[Special:DeleteBatch|حذف باتش من الصفحات]]',
	'deletebatch-button' => 'حذف',
	'deletebatch-here' => '<b>هنا</b>',
	'deletebatch-help' => 'حذف باتش من الصفحات. يمكنك إما عمل عملية حذف واحدة، أو حذف الصفحات المرتبة في ملف.
اختر مستخدما ليتم عرضه في سجلات الحذف.
الملف المرفوع ينبغي أن يحتوي على اسم الصفحة وسبب اختياري مفصولين بواسطة حرف "|" في كل سطر.',
	'deletebatch-caption' => 'قائمة الصفحات',
	'deletebatch-title' => 'حذف الباتش',
	'deletebatch-link-back' => 'يمكنك العودة إلى الامتداد',
	'deletebatch-as' => 'تشغيل السكريبت ك',
	'deletebatch-both-modes' => 'من فضلك اختر إما صفحة واحدة أو قائمة معطاة من الصفحات.',
	'deletebatch-or' => '<b>أو</b>',
	'deletebatch-page' => 'الصفحات للحذف',
	'deletebatch-reason' => 'سبب الحذف',
	'deletebatch-processing' => 'جاري حذف الصفحات',
	'deletebatch-from-file' => 'من قائمة ملف',
	'deletebatch-from-form' => 'من استمارة',
	'deletebatch-success-subtitle' => 'ل$1',
	'deletebatch-omitting-nonexistant' => 'إزالة صفحة غير موجودة $1.',
	'deletebatch-omitting-invalid' => 'إزالة صفحة غير صحيحة $1.',
	'deletebatch-file-bad-format' => 'الملف ينبغي أن يكون نصا خالصا',
	'deletebatch-file-missing' => 'غير قادر على قراءة الملف المعطى',
	'deletebatch-select-script' => 'سكريبت حذف الصفحات',
	'deletebatch-select-yourself' => 'أنت',
	'deletebatch-no-page' => 'من فضلك اختر على الأقل صفحة واحدة للحذف أو اختر ملفا يحتوي على قائمة الصفحات.',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Meno25
 */
$messages['arz'] = array(
	'deletebatch' => 'حذف باتش من الصفحات',
	'deletebatch-desc' => '[[Special:DeleteBatch|حذف باتش من الصفحات]]',
	'deletebatch-button' => 'حذف',
	'deletebatch-here' => '<b>هنا</b>',
	'deletebatch-help' => 'حذف باتش من الصفحات. يمكنك إما عمل عملية حذف واحدة، أو حذف الصفحات المرتبة فى ملف.
اختر مستخدما ليتم عرضه فى سجلات الحذف.
الملف المرفوع ينبغى أن يحتوى على اسم الصفحة وسبب اختيارى مفصولين بواسطة حرف "|" فى كل سطر.',
	'deletebatch-caption' => 'قائمة الصفحات',
	'deletebatch-title' => 'حذف الباتش',
	'deletebatch-link-back' => 'يمكنك العودة إلى الامتداد',
	'deletebatch-as' => 'تشغيل السكريبت ك',
	'deletebatch-both-modes' => 'من فضلك اختر إما صفحة واحدة أو قائمة معطاة من الصفحات.',
	'deletebatch-or' => '<b>أو</b>',
	'deletebatch-page' => 'الصفحات للحذف',
	'deletebatch-reason' => 'سبب الحذف',
	'deletebatch-processing' => 'جارى حذف الصفحات',
	'deletebatch-from-file' => 'من قائمة ملف',
	'deletebatch-from-form' => 'من استمارة',
	'deletebatch-success-subtitle' => 'ل$1',
	'deletebatch-omitting-nonexistant' => 'إزالة صفحة غير موجودة $1.',
	'deletebatch-omitting-invalid' => 'إزالة صفحة غير صحيحة $1.',
	'deletebatch-file-bad-format' => 'الملف ينبغى أن يكون نصا خالصا',
	'deletebatch-file-missing' => 'غير قادر على قراءة الملف المعطى',
	'deletebatch-select-script' => 'سكريبت حذف الصفحات',
	'deletebatch-select-yourself' => 'أنت',
	'deletebatch-no-page' => 'من فضلك اختر على الأقل صفحة واحدة للحذف أو اختر ملفا يحتوى على قائمة الصفحات.',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'deletebatch-button' => 'ИЗТРИВАНЕ',
	'deletebatch-here' => '<b>тук</b>',
	'deletebatch-as' => 'Стартиране на скрипта като',
	'deletebatch-or' => '<b>ИЛИ</b>',
	'deletebatch-page' => 'Страници за изтриване',
	'deletebatch-reason' => 'Причина за изтриването',
	'deletebatch-processing' => 'изтриване на страниците',
	'deletebatch-from-file' => 'от списък във файл',
	'deletebatch-from-form' => 'от формуляр',
	'deletebatch-success-subtitle' => 'за $1',
	'deletebatch-file-bad-format' => 'Необходимо е файлът да съдържа само текст',
	'deletebatch-file-missing' => 'Предоставеният файл не може да бъде прочетен',
);

/** German (Deutsch)
 * @author Leithian
 * @author Revolus
 */
$messages['de'] = array(
	'deletebatch' => 'Eine Reihe von Seiten löschen',
	'deletebatch-desc' => '[[Special:DeleteBatch|Lösche eine Reihe von Seiten]]',
	'deletebatch-button' => 'Löschen',
	'deletebatch-here' => '<b>hier</b>',
	'deletebatch-help' => 'Lösche eine Reihe von Seiten. Du kannst einerseits eine einzelne Seite löschen, aber auch mehrere Seiten, die du in einer Datei aufzählst.
Wähle einen Benutzer, der im Löschlogbuch angezeigt werden soll.
Die hochzuladende Datei sollte pro Zeile einen Seitentitel und kann optional einen mit einem senkrechten Stich („|“) abgetrennten Löschgrund enthalten.',
	'deletebatch-caption' => 'Dateiliste',
	'deletebatch-as' => 'Das Skript ausführen als',
	'deletebatch-both-modes' => 'Bitte wähle entweder eine spezifische Seite oder eine gegebene Liste von Seiten.',
	'deletebatch-or' => '<b>oder</b>',
	'deletebatch-page' => 'Zu löschende Seiten',
	'deletebatch-reason' => 'Löschgrund',
	'deletebatch-processing' => 'Lösche Seiten',
	'deletebatch-from-file' => 'von Dateiliste',
	'deletebatch-from-form' => 'von Eingabe',
	'deletebatch-success-subtitle' => 'für $1',
	'deletebatch-omitting-nonexistant' => 'Überspringe nicht vorhandene Seite $1.',
	'deletebatch-omitting-invalid' => 'Überspringe ungültige Seite $1.',
	'deletebatch-file-bad-format' => 'Die Datei sollte Klartext enthalten.',
	'deletebatch-file-missing' => 'Übergebene Datei konnte nicht gelesen werden',
	'deletebatch-select-yourself' => 'du',
	'deletebatch-no-page' => 'Bitte gebe entweder zumindest eine zu löschende Seite oder wähle eine Datei, die eine Liste von zu löschenden Seiten enthält.',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'deletebatch' => 'Forigi aron de paĝoj',
	'deletebatch-desc' => '[[Special:DeleteBatch|Forigi aron de paĝoj]]',
	'deletebatch-button' => 'FORIGI',
	'deletebatch-here' => '<b>ĉi tie</b>',
	'deletebatch-help' => 'Por forigi aron de paĝoj. Vi povas aŭ fari unuopan forigon, aŭ forigi paĝojn listitajn en dosiero.
Selektu uzanton kiu estos montrata en forigadaj protokoloj.
Alŝutita dosiero enhavu paĝan nomon kaj nedevigan kialon apartigita de signo "|" en ĉiu linio.',
	'deletebatch-caption' => 'Paĝlisto',
	'deletebatch-title' => 'Forigi aron',
	'deletebatch-link-back' => 'Vi povas reiri al la etendilo',
	'deletebatch-as' => 'Voki la skripton kiel',
	'deletebatch-both-modes' => 'Bonvolu selekti aŭ unu specifan paĝon aŭ donatan liston de paĝoj.',
	'deletebatch-or' => '<b>AŬ</b>',
	'deletebatch-page' => 'Forigotaj paĝoj',
	'deletebatch-reason' => 'Kialo por forigo',
	'deletebatch-processing' => 'forigante paĝojn',
	'deletebatch-from-file' => 'de dosierlisto',
	'deletebatch-from-form' => 'de paĝo',
	'deletebatch-success-subtitle' => 'por $1',
	'deletebatch-omitting-nonexistant' => 'Pasante neekzistan paĝon $1.',
	'deletebatch-omitting-invalid' => 'Pasante nevalidan paĝon $1.',
	'deletebatch-file-bad-format' => 'La dosiero estu norma teksto',
	'deletebatch-file-missing' => 'Ne eblas legi donatan dosieron',
	'deletebatch-select-script' => 'skripto por forigi paĝon',
	'deletebatch-select-yourself' => 'vi',
	'deletebatch-no-page' => 'Bonvolu specifigi almenaŭ unu paĝon por forigi AŬ selekti dosieron enhavantan paĝliston.',
);

/** Spanish (Español)
 * @author Sanbec
 */
$messages['es'] = array(
	'deletebatch-button' => 'Borrar',
);

/** Finnish (Suomi)
 * @author Jack Phoenix
 */
$messages['fi'] = array(
	'deletebatch' => 'Poista useita sivuja',
	'deletebatch-button' => 'POISTA',
	'deletebatch-here' => '<b>täällä</b>',
	'deletebatch-help' => 'Poista useita sivuja. Voit joko tehdä yhden poiston tai poistaa tiedostossa listatut sivut. Valitse käyttäjä, joka näytetään poistolokeissa. Tallennetun tiedoston tulisi sisältää sivun nimi ja vapaaehtoinen syy | -merkin erottamina joka rivillä.',
	'deletebatch-caption' => 'Sivulista',
	'deletebatch-title' => 'Poista useita sivuja',
	'deletebatch-link-back' => 'Voit palata lisäosaan',
	'deletebatch-as' => 'Suorita skripti käyttäjänä',
	'deletebatch-both-modes' => 'Valitse joko määritelty sivu tai annettu lista sivuista.',
	'deletebatch-or' => '<b>TAI</b>',
	'deletebatch-page' => 'Poistettavat sivut',
	'deletebatch-reason' => 'Poiston syy',
	'deletebatch-processing' => 'poistetaan sivuja',
	'deletebatch-from-file' => 'tiedostolistasta',
	'deletebatch-from-form' => 'lomakkeesta',
	'deletebatch-omitting-nonexistant' => 'Ohitetaan olematon sivu $1.',
	'deletebatch-omitting-invalid' => 'Ohitetaan kelpaamaton sivu $1.',
	'deletebatch-file-bad-format' => 'Tiedoston tulisi olla raakatekstiä',
	'deletebatch-file-missing' => 'Ei voi lukea annettua tiedostoa',
	'deletebatch-select-script' => 'sivunpoistoskripti',
	'deletebatch-select-yourself' => 'sinä',
	'deletebatch-no-page' => 'Määrittele ainakin yksi poistettava sivu TAI valitse tiedosto, joka sisältää sivulistan.',
);

/** French (Français)
 * @author Grondin
 * @author McDutchie
 */
$messages['fr'] = array(
	'deletebatch' => 'Supprimer lot de pages',
	'deletebatch-desc' => '[[Special:DeleteBatch|Supprime un lot de pages]]',
	'deletebatch-button' => 'SUPPRIMER',
	'deletebatch-here' => '<b>ici</b>',
	'deletebatch-help' => 'Supprime un lot de pages. Vous pouvez soit lancer une simple suppression, soit supprimer des pages listées dans un fichier.
Choisissez un utilisateur qui sera affiché dans le journal des suppressions.
Un fichier importé pourra contenir un nom de la page et un motif facultatif séparé par un « | » dans chaque ligne.',
	'deletebatch-caption' => 'Liste de la page',
	'deletebatch-title' => 'Supprimer en lot',
	'deletebatch-link-back' => 'Vous pouvez revenir à l’extension',
	'deletebatch-as' => 'Lancer le script comme',
	'deletebatch-both-modes' => 'Veuillez choisir, soit une des pages indiquées, soit une liste donnée de pages.',
	'deletebatch-or' => '<b>OU</b>',
	'deletebatch-page' => 'Pages à supprimer',
	'deletebatch-reason' => 'Motif de la suppression',
	'deletebatch-processing' => 'suppression des pages',
	'deletebatch-from-file' => 'depuis la liste d’un fichier',
	'deletebatch-from-form' => 'à partir du formulaire',
	'deletebatch-success-subtitle' => 'pour « $1 »',
	'deletebatch-omitting-nonexistant' => 'Omission de la page « $1 » inexistante.',
	'deletebatch-omitting-invalid' => 'Omission de la page « $1 » incorrecte.',
	'deletebatch-file-bad-format' => 'Le fichier doit être en texte simple',
	'deletebatch-file-missing' => 'Impossible de lire le fichier donné',
	'deletebatch-select-script' => 'script pour supprimer pages',
	'deletebatch-select-yourself' => 'vous',
	'deletebatch-no-page' => 'Veuillez indiquer au moins une page à supprimer OU un fichier donné contenant une liste de pages.',
);

/** Western Frisian (Frysk)
 * @author Snakesteuben
 */
$messages['fy'] = array(
	'deletebatch-button' => 'Wiskje',
	'deletebatch-reason' => 'Reden foar it wiskjen',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'deletebatch' => 'Borrar un conxunto de páxinas',
	'deletebatch-desc' => '[[Special:DeleteBatch|Borrar un conxunto de páxinas]]',
	'deletebatch-button' => 'BORRAR',
	'deletebatch-here' => '<b>aquí</b>',
	'deletebatch-help' => 'Borrar un conxunto de páxinas. Pode levar a cabo un borrado único ou borrar as páxinas listadas nun ficheiro.
Escolla o usuario que será amosado nos rexistros de borrado.
O ficheiro cargado debería conter o nome da páxina e unha razón opcional separados por un carácter de barra vertical ("|") en cada liña.',
	'deletebatch-caption' => 'Lista da páxina',
	'deletebatch-title' => 'Borrar un conxunto',
	'deletebatch-link-back' => 'Pode voltar á extensión',
	'deletebatch-as' => 'Executar o guión como',
	'deletebatch-both-modes' => 'Por favor, escolla unha páxina específica ou unha lista de páxinas dadas.',
	'deletebatch-or' => '<b>OU</b>',
	'deletebatch-page' => 'Páxinas para ser borradas',
	'deletebatch-reason' => 'Razón para o borrado',
	'deletebatch-processing' => 'borrando a páxina',
	'deletebatch-from-file' => 'da lista de ficheiros',
	'deletebatch-from-form' => 'do formulario',
	'deletebatch-success-subtitle' => 'de $1',
	'deletebatch-omitting-nonexistant' => 'Omitindo a páxina $1, que non existe.',
	'deletebatch-omitting-invalid' => 'Omitindo a páxina inválida $1.',
	'deletebatch-file-bad-format' => 'O ficheiro debería ser un texto sinxelo',
	'deletebatch-file-missing' => 'Non se pode ler o ficheiro dado',
	'deletebatch-select-script' => 'borrar o guión dunha páxina',
	'deletebatch-select-yourself' => 'vostede',
	'deletebatch-no-page' => 'Por favor, especifique, polo menos, unha páxina para borrar OU escolla un ficheiro que conteña unha lista de páxinas.',
);

/** Croatian (Hrvatski)
 * @author Dalibor Bosits
 */
$messages['hr'] = array(
	'deletebatch' => 'Izbriši skupinu stranica',
	'deletebatch-desc' => '[[Special:DeleteBatch|Izbriši skupinu stranica]]',
	'deletebatch-button' => 'Izbriši',
	'deletebatch-here' => '<b>ovdje</b>',
	'deletebatch-help' => 'Brisanje skupine stranica. Možete izbrisati samo jednu stranicu, ili izbrisati stranice s popisa.
Odaberite suradnika koje će biti prikazan u evidencijama.
Postavljena datoteka treba sadržavati nazive stranica, a dodatno razlog odvojen kosom crtom "|", u svakom redu.',
	'deletebatch-caption' => 'Popis stranica',
	'deletebatch-title' => 'Skupno brisanje',
	'deletebatch-link-back' => 'Možete se vratiti nazad na ekstenziju',
	'deletebatch-as' => 'Pokreni skriptu kao',
	'deletebatch-both-modes' => 'Molimo vas odaberiti ili jednu određenu stranicu ili popis stranica.',
	'deletebatch-or' => '<b>ili</b>',
	'deletebatch-page' => 'Stranice za brisanje',
	'deletebatch-reason' => 'Razlog za brisanje',
	'deletebatch-processing' => 'brišem stranice',
	'deletebatch-from-file' => 's popisa iz datoteke',
	'deletebatch-from-form' => 'iz obrasca',
	'deletebatch-success-subtitle' => 'za $1',
	'deletebatch-omitting-nonexistant' => 'Izostavljanje nepostojeće stranice $1.',
	'deletebatch-omitting-invalid' => 'Izostavljanje neispravne stranice $1.',
	'deletebatch-file-bad-format' => 'U datoteci bi trebao biti čisti tekst',
	'deletebatch-file-missing' => 'Datoteka se ne može pročitati',
	'deletebatch-select-script' => 'skripta za brisanje',
	'deletebatch-select-yourself' => 'vi',
	'deletebatch-no-page' => 'Molimo vas odredite barem jednu stranicu za brisanje ILI odaberite datoteku koja sadrži popis.',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'deletebatch' => 'Deler lot de paginas',
	'deletebatch-desc' => '[[Special:DeleteBatch|Deler un lot de paginas]]',
	'deletebatch-button' => 'Deler',
	'deletebatch-here' => '<b>hic</b>',
	'deletebatch-help' => 'Deler un lot de paginas. Tu pote executar un deletion singule, o deler paginas listate in un file.
Selige un usator que se monstrara in le registro de deletiones.
Le file cargate debe continer in cata linea un nomine de pagina e un motivo facultative separate per un character "|".',
	'deletebatch-caption' => 'Lista de paginas',
	'deletebatch-title' => 'Deler in lot',
	'deletebatch-link-back' => 'Tu pote retornar al extension',
	'deletebatch-as' => 'Executar le script como',
	'deletebatch-both-modes' => 'Per favor selige, o un del paginas specificate, o un lista date de paginas.',
	'deletebatch-or' => '<b>o</b>',
	'deletebatch-page' => 'Paginas a deler',
	'deletebatch-reason' => 'Motivo pro deletion',
	'deletebatch-processing' => 'deletion de paginas',
	'deletebatch-from-file' => 'a partir del lista in un file',
	'deletebatch-from-form' => 'a partir del formulario',
	'deletebatch-success-subtitle' => 'pro $1',
	'deletebatch-omitting-nonexistant' => 'Omission del pagina non existente "$1".',
	'deletebatch-omitting-invalid' => 'Omission del pagina invalide "$1".',
	'deletebatch-file-bad-format' => 'Le file debe esser in texto simple',
	'deletebatch-file-missing' => 'Non pote leger le file date',
	'deletebatch-select-script' => 'script pro deler paginas',
	'deletebatch-select-yourself' => 'tu',
	'deletebatch-no-page' => 'Per favor, o specifica al minus un pagina a deler, o selige un file continente un lista de paginas.',
);

/** Italian (Italiano)
 * @author Darth Kule
 * @author Melos
 */
$messages['it'] = array(
	'deletebatch-button' => 'Cancella',
	'deletebatch-reason' => 'Motivo della cancellazione',
);

/** Japanese (日本語)
 * @author Fievarsty
 */
$messages['ja'] = array(
	'deletebatch' => 'ページを一括削除する',
	'deletebatch-desc' => '[[Special:DeleteBatch|ページを一括削除する]]',
	'deletebatch-button' => '削除',
	'deletebatch-caption' => 'ページリスト',
	'deletebatch-title' => '一括削除',
	'deletebatch-or' => '<b>または</b>',
	'deletebatch-page' => '削除するページ',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Lovekhmer
 */
$messages['km'] = array(
	'deletebatch-reason' => 'មូលហេតុនៃការលុប',
	'deletebatch-processing' => 'ការលុបទំព័រ',
	'deletebatch-success-subtitle' => 'សំរាប់$1',
	'deletebatch-select-yourself' => 'អ្នក',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'deletebatch-desc' => 'Extension description displayed on [[Special:Version]].',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'deletebatch' => 'Rei vu Säite läschen',
	'deletebatch-desc' => '[[Special:DeleteBatch|Läscht eng Rei Säiten]]',
	'deletebatch-button' => 'LÄSCHEN',
	'deletebatch-here' => '<b>hei</b>',
	'deletebatch-caption' => 'Lëscht vun der Säit',
	'deletebatch-title' => 'Zesumme läschen',
	'deletebatch-link-back' => "Dir kënnt op d'Erweiderung zréckgoen",
	'deletebatch-or' => '<b>ODER</b>',
	'deletebatch-page' => 'Säite fir ze läschen',
	'deletebatch-reason' => 'Grond fir ze läschen',
	'deletebatch-processing' => "d'Säite gi geläscht",
	'deletebatch-from-file' => 'vun der Lëscht vun engem Fichier',
	'deletebatch-from-form' => 'vum Formulaire',
	'deletebatch-success-subtitle' => 'fir $1',
	'deletebatch-omitting-nonexistant' => "D'Säit $1 déi et net gëtt iwwersprangen.",
	'deletebatch-omitting-invalid' => 'Déi ongëlteg Säit $1 iwwersprangen.',
	'deletebatch-select-script' => 'de Script vun der Säit läschen',
	'deletebatch-select-yourself' => 'Dir',
);

/** Nahuatl (Nāhuatl)
 * @author Fluence
 */
$messages['nah'] = array(
	'deletebatch-button' => 'Ticpolōz',
	'deletebatch-reason' => 'Tlapololiztli īxtlamatiliztli',
	'deletebatch-success-subtitle' => '$1 ītechcopa',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'deletebatch' => 'Paginareeks verwijderen',
	'deletebatch-desc' => '[[Special:DeleteBatch|Paginareeks verwijderen]]',
	'deletebatch-button' => 'VERWIJDEREN',
	'deletebatch-here' => '<b>hier</b>',
	'deletebatch-help' => 'Een lijst pagina\'s verwijderen.
U kunt een enkele pagina verwijderen of een lijst van pagina\'s in een bestand.
Kies een gebruiker die in het verwijderlogboek wordt genoemd.
Het bestand dat u uploadt moet op iedere regel een paginanaam en een reden bevatten (optioneel), gescheiden door het karakter "|".',
	'deletebatch-caption' => 'Paginalijst',
	'deletebatch-title' => 'Reeks verwijderen',
	'deletebatch-link-back' => 'Teruggaan naar de uitbreiding',
	'deletebatch-as' => 'Script uitvoeren als',
	'deletebatch-both-modes' => "Kies een bepaalde pagina of geef een list met pagina's op.",
	'deletebatch-or' => '<b>OF</b>',
	'deletebatch-page' => "Te verwijderen pagina's",
	'deletebatch-reason' => 'Reden voor verwijderen',
	'deletebatch-processing' => "bezig met het verwijderen van pagina's",
	'deletebatch-from-file' => 'van een lijst uit een bestand',
	'deletebatch-from-form' => 'uit het formulier',
	'deletebatch-success-subtitle' => 'voor $1',
	'deletebatch-omitting-nonexistant' => 'Niet-bestaande pagina $1 is overgeslagen.',
	'deletebatch-omitting-invalid' => 'Ongeldige paginanaam $1 is overgeslagen.',
	'deletebatch-file-bad-format' => 'Het bestand moet platte tekst bevatten',
	'deletebatch-file-missing' => 'Het bestnad kan niet gelezen worden',
	'deletebatch-select-script' => "script pagina's verwijderen",
	'deletebatch-select-yourself' => 'u',
	'deletebatch-no-page' => "Geef tenminste één te verwijderen pagina op of kies een bestand dat de lijst met pagina's bevat.",
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'deletebatch' => 'Slett mange sider',
	'deletebatch-desc' => '[[Special:DeleteBatch|Slett mange sider]]',
	'deletebatch-button' => 'Slett',
	'deletebatch-here' => '<b>her</b>',
	'deletebatch-help' => 'Slett en serie av sider. Du kan også utføre en enkel sletting, eller slette sider listet opp i en fil.
Velg en bruker som skal vises i slettingsloggen.
En opplastet fil må inneholde navnet på siden, og kan også ha en valgfri slettingsgrunn skilt fra tittelen med «|».',
	'deletebatch-caption' => 'Sideliste',
	'deletebatch-title' => 'Slett serie',
	'deletebatch-link-back' => 'Du kan gå tilbake til utvidelsen',
	'deletebatch-as' => 'Kjør skriptet som',
	'deletebatch-both-modes' => 'Velg én side eller en liste over sider.',
	'deletebatch-or' => '<b>eller</b>',
	'deletebatch-page' => 'Sider som skal slettes',
	'deletebatch-reason' => 'Slettingsårsak',
	'deletebatch-processing' => 'sletter sider',
	'deletebatch-from-file' => 'fra filliste',
	'deletebatch-from-form' => 'fra skjema',
	'deletebatch-success-subtitle' => 'for $1',
	'deletebatch-omitting-nonexistant' => 'Utelater den ikke-eksisterende siden $1.',
	'deletebatch-omitting-invalid' => 'Utelater den ugyldige siden $1.',
	'deletebatch-file-bad-format' => 'Filen bør inneholde ren tekst',
	'deletebatch-file-missing' => 'Kunne ikke lese filen',
	'deletebatch-select-script' => 'slett sideskript',
	'deletebatch-select-yourself' => 'du',
	'deletebatch-no-page' => 'Vennligst oppgi minst én side å slette eller velg en fil med en liste av sider.',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'deletebatch' => 'Lòt de supression de las paginas',
	'deletebatch-desc' => '[[Special:DeleteBatch|Suprimís un lòt de paginas]]',
	'deletebatch-button' => 'SUPRIMIR',
	'deletebatch-here' => '<b>aicí</b>',
	'deletebatch-help' => 'Suprimís un lòt de paginas. Podètz siá aviar una supression simpla, siá suprimir de paginas listadas dins un fichièr.
Causissètz un utilizaire que serà afichat dins lo jornal de las supressions.
Un fichièr importat poirà conténer un nom de la pagina e un motiu facultatiu separat per un « | » dins cada linha.',
	'deletebatch-caption' => 'Tièra de la pagina',
	'deletebatch-title' => 'Suprimir en lòt',
	'deletebatch-link-back' => 'Podètz tornar a l’extension',
	'deletebatch-as' => "Aviar l'escript coma",
	'deletebatch-both-modes' => 'Causissètz, siá una de las paginas indicadas, siá una tièra donada de paginas.',
	'deletebatch-or' => '<b>o</b>',
	'deletebatch-page' => 'Paginas de suprimir',
	'deletebatch-reason' => 'Motiu de la supression',
	'deletebatch-processing' => 'supression de las paginas',
	'deletebatch-from-file' => 'dempuèi la tièra d’un fichièr',
	'deletebatch-from-form' => 'a partir del formulari',
	'deletebatch-success-subtitle' => 'per « $1 »',
	'deletebatch-omitting-nonexistant' => 'Omission de la pagina « $1 » inexistenta.',
	'deletebatch-omitting-invalid' => 'Omission de la pagina « $1 » incorrècta.',
	'deletebatch-file-bad-format' => 'Lo fichièr deu èsser en tèxt simple',
	'deletebatch-file-missing' => 'Impossible de legir lo fichièr donat',
	'deletebatch-select-script' => "suprimir l'escript de la pagina",
	'deletebatch-select-yourself' => 'vos',
	'deletebatch-no-page' => 'Indicatz al mens una pagina de suprimir O un fichièr donat que conten una tièra de paginas.',
);

/** Polish (Polski)
 * @author Airwolf
 * @author Maikking
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'deletebatch' => 'Usuń grupę stron',
	'deletebatch-desc' => '[[Special:DeleteBatch|Usuń grupę stron]]',
	'deletebatch-button' => 'Usuń',
	'deletebatch-here' => '<b>tutaj</b>',
	'deletebatch-help' => 'Usuwanie grupy stron. Strony możesz usuwać pojedynczo lub poprzez usunięcie grupy stron, wymienionych w pliku.
Wybierz użytkownika, który będzie widoczny w logu stron usuniętych.
Przesyłany plik powinien zawierać nazwę strony i powód usunięcia w jednej linii tekstu, przedzielone symbolem "|".',
	'deletebatch-caption' => 'Lista stron',
	'deletebatch-title' => 'Usuń grupę stron',
	'deletebatch-link-back' => 'Cofnij do usuwania grup stron',
	'deletebatch-as' => 'Uruchom skrypt jako',
	'deletebatch-both-modes' => 'Wybierz jedną stronę albo grupę stron.',
	'deletebatch-or' => '<b>lub</b>',
	'deletebatch-page' => 'Lista stron do usunięcia',
	'deletebatch-reason' => 'Powód usunięcia',
	'deletebatch-processing' => 'usuwanie stron',
	'deletebatch-from-file' => 'z listy zawartej w pliku',
	'deletebatch-from-form' => 'z',
	'deletebatch-success-subtitle' => 'dla $1',
	'deletebatch-omitting-nonexistant' => 'Pominięto nieistniejącą stronę $1.',
	'deletebatch-omitting-invalid' => 'Pominięto niewłaściwą stronę $1.',
	'deletebatch-file-bad-format' => 'Plik powinien zawierać wyłącznie tekst',
	'deletebatch-file-missing' => 'Nie można odczytać pliku',
	'deletebatch-select-script' => 'usuwanie stron',
	'deletebatch-select-yourself' => 'Ty',
	'deletebatch-no-page' => 'Wybierz pojedynczą stronę do usunięcia ALBO wybierz plik zawierający listę stron.',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'deletebatch-here' => '<b>aqui</b>',
	'deletebatch-caption' => 'Lista de páginas',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 */
$messages['ro'] = array(
	'deletebatch-button' => 'Şterge',
	'deletebatch-here' => '<b>aici</b>',
	'deletebatch-or' => '<b>sau</b>',
	'deletebatch-page' => 'Pagini de şters',
	'deletebatch-reason' => 'Motiv pentru ştergere',
	'deletebatch-processing' => 'ştergere pagini',
	'deletebatch-from-form' => 'din formular',
	'deletebatch-success-subtitle' => 'pentru $1',
	'deletebatch-file-missing' => 'Nu se poate citi fişierul dat',
);

/** Russian (Русский)
 * @author Innv
 */
$messages['ru'] = array(
	'deletebatch-button' => 'Удалить',
	'deletebatch-success-subtitle' => 'для $1',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'deletebatch' => 'Zmazanie viacerých stránok',
	'deletebatch-desc' => '[[Special:DeleteBatch|Zmazanie viacerých stránok]]',
	'deletebatch-button' => 'ZMAZAŤ',
	'deletebatch-here' => '<b>tu</b>',
	'deletebatch-help' => 'Zmazanie viacerých stránok. Môžete buď vykonať jedno zmazanie alebo zmazať stránky uvedené v súbore.
Vyberte, ktorý používateľ sa zobrazí v záznamoch zmazania.
Nahraný súbor by mal na každom riadku obsahovať názov stránky a nepovinne aj dôvod zmazania oddelený znakom „|”.',
	'deletebatch-caption' => 'Zoznam stránok',
	'deletebatch-title' => 'Zmazať dávku',
	'deletebatch-link-back' => 'Môžete sa vrátiť späť na rozšírenie',
	'deletebatch-as' => 'Spustiť skript ako',
	'deletebatch-both-modes' => 'Prosím, vyberte buď zadanú stránku alebo zadaý zoznam stránok.',
	'deletebatch-or' => '<b>ALEBO</b>',
	'deletebatch-page' => 'Stránky, ktoré budú zmazané',
	'deletebatch-reason' => 'Dôvod zmazania',
	'deletebatch-processing' => 'mažú sa stránky',
	'deletebatch-from-file' => 'zo zoznamu v súbore',
	'deletebatch-from-form' => 'z formulára',
	'deletebatch-success-subtitle' => 'z $1',
	'deletebatch-omitting-nonexistant' => 'Vynecháva sa neexistujúca stránka $1.',
	'deletebatch-omitting-invalid' => 'Vynecháva sa neplatná stránka $1.',
	'deletebatch-file-bad-format' => 'Súbor by mal byť textovom formáte',
	'deletebatch-file-missing' => 'Nebolo možné prečítať zadaný súbor',
	'deletebatch-select-script' => 'skript na zmazanie stránok',
	'deletebatch-select-yourself' => 'vy',
	'deletebatch-no-page' => 'Prosím, zadajte aspoň jednu stránku, ktorá sa má zmazať ALEBO súbor obsahujúci zoznam stránok.',
);

/** Swedish (Svenska)
 * @author M.M.S.
 */
$messages['sv'] = array(
	'deletebatch' => 'Radera serier av sidor',
	'deletebatch-desc' => '[[Special:DeleteBatch|Radera en serie av sidor]]',
	'deletebatch-button' => 'RADERA',
	'deletebatch-here' => '<b>här</b>',
	'deletebatch-help' => 'Radera en serie av sidor. Du kan också utföra en ensam radering, eller radera sidor listade i en fil.
Välj en användare som kommer att visas i raderingsloggen.
En uppladdad fil ska innehålla sidnamn och en valfri anledning separerade med ett "|"-tecken på varje rad.',
	'deletebatch-caption' => 'Sidlista',
	'deletebatch-title' => 'Radera serie',
	'deletebatch-link-back' => 'Du kan gå tillbaka till tillägget',
	'deletebatch-as' => 'Kör skriptet som',
	'deletebatch-both-modes' => 'Var god välj antingen en specifierad sida eller en lista över sidor.',
	'deletebatch-or' => '<b>ELLER</b>',
	'deletebatch-page' => 'Sidor som ska raderas',
	'deletebatch-reason' => 'Anledning för radering',
	'deletebatch-processing' => 'raderar sidor',
	'deletebatch-from-file' => 'från fillistan',
	'deletebatch-from-form' => 'från formulär',
	'deletebatch-success-subtitle' => 'för $1',
	'deletebatch-omitting-nonexistant' => 'Utelämna ej existerande sida $1.',
	'deletebatch-omitting-invalid' => 'Utelämna ogiltig sida $1.',
	'deletebatch-file-bad-format' => 'Filen ska innehålla ren text',
	'deletebatch-file-missing' => 'Kan inte läsa filen',
	'deletebatch-select-script' => 'radera sidskript',
	'deletebatch-select-yourself' => 'du',
	'deletebatch-no-page' => 'Var god specifiera minst en sida för att radera ELLER välj en fil innehållande en sidlista.',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'deletebatch-here' => '<b>ఇక్కడ</b>',
	'deletebatch-caption' => 'పేజీల జాబితా',
	'deletebatch-or' => '<b>లేదా</b>',
	'deletebatch-page' => 'తొలగించాల్సిన పేజీలు',
	'deletebatch-reason' => 'తొలగింపునకు కారణం',
);

/** Turkish (Türkçe)
 * @author Mach
 */
$messages['tr'] = array(
	'deletebatch-button' => 'Sil',
	'deletebatch-caption' => 'Sayfa listesi',
);

/** Ukrainian (Українська)
 * @author AS
 */
$messages['uk'] = array(
	'deletebatch' => 'Вилучення сторінок групами',
	'deletebatch-desc' => '[[Special:DeleteBatch|Вилучення сторінок групами]]',
	'deletebatch-button' => 'Вилучити',
	'deletebatch-here' => '<b>тут</b>',
	'deletebatch-help' => 'Вилучення групи сторінок. Також ви можете зробити окреме вилучення, або вилучити сторінки, перераховані у файлі.
Виберіть користувача, який згадуватиметься у журналі вилучень.
Завантажений файл повинен містити у кожному рядку назву сторінки та необов\'язкову причину вилучення, відокремлену символом "|".',
	'deletebatch-caption' => 'Перелік сторінок',
	'deletebatch-title' => 'Вилучити групу',
	'deletebatch-link-back' => 'Ви можете повернутися до розширення',
	'deletebatch-as' => 'Запустити скрипт як',
	'deletebatch-both-modes' => 'Виберіть або одну вказану сторінку, або наданий список сторінок.',
	'deletebatch-or' => '<b>або</b>',
	'deletebatch-page' => 'Сторінки до вилучення',
	'deletebatch-reason' => 'Причина вилучення',
	'deletebatch-processing' => 'вилучення сторінок',
	'deletebatch-from-file' => 'із списку файла',
	'deletebatch-success-subtitle' => 'для $1',
	'deletebatch-file-missing' => 'Не в змозі прочитати наданий файл',
	'deletebatch-select-yourself' => 'ви',
);

