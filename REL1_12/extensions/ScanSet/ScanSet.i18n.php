<?php
/**
 * Internationalisation file for extension Scan Set.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'scanset-desc' => 'View scanned page images from Andreas Grosz\'s CD/DVD sets',
	'scanset_no_name' => 'ScanSet: You must specify a scan set name, e.g. &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name' => 'ScanSet: Invalid or missing scan set.',
	'scanset_unrecognised_index_format' => 'ScanSet: Unknown index format',
	'scanset_opendir_error' => 'ScanSet: Error, cannot open directory $1',
	'scanset_no_files' => 'ScanSet: No files present in the specified directory.',
	'scanset_no_volumes' => 'ScanSet: No volume directories found',
	'scanset_missing_index_file' => 'ScanSet: Index file $1 not found',
	'scanset_index_file_error' => 'ScanSet: Error in index file format at line $1',
	'scanset_invalid_volume' => 'ScanSet: Invalid volume',
	'scanset_next' => 'Next &gt;',
	'scanset_prev' => '&lt; Prev',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'scanset-desc'                      => 'عرض صور الصفحات المنسوخة من مجموعات أندرياس جروز CD/DVD',
	'scanset_no_name'                   => 'سكان مجموعة: يجب عليك تحديد اسم سكان مجموعة، مثال &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'سكان مجموعة: سكان مجموعة غير صحيحة أو ناقصة.',
	'scanset_unrecognised_index_format' => 'سكان مجموعة: صيغة فهرس غير معروفة',
	'scanset_opendir_error'             => 'سكان مجموعة: خطأ، لا يمكن فتح المجلد $1',
	'scanset_no_files'                  => 'سكان مجموعة: لا توجد ملفات في المجلد المحدد.',
	'scanset_no_volumes'                => 'سكان مجموعة: لا توجد مجلدات حجم',
	'scanset_missing_index_file'        => 'سكان مجموعة: ملف الفهرس $1 لم يتم العثور عليه',
	'scanset_index_file_error'          => 'سكان مجموعة: خطأ في صيغة ملف الفهرس عند السطر $1',
	'scanset_invalid_volume'            => 'سكان مجموعة: حجم غير صحيح',
	'scanset_next'                      => 'التالي &gt;',
	'scanset_prev'                      => '&lt; السابق',
);

/** Asturian (Asturianu)
 * @author Esbardu
 */
$messages['ast'] = array(
	'scanset_no_name'                   => 'ScanSet: Tienes qu\'especificar un nome pal scan set, p. ex. &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet: Scan set non válidu o ausente.',
	'scanset_unrecognised_index_format' => "ScanSet: Formatu d'índiz desconocíu",
	'scanset_opendir_error'             => 'ScanSet: Error, nun se pue abrir el direutoriu $1',
	'scanset_no_files'                  => 'ScanSet: Nun hai archivos nel direutoriu especificáu.',
	'scanset_no_volumes'                => "ScanSet: Nun s'atoparon direutorios nesti volume",
	'scanset_missing_index_file'        => "ScanSet: Nun s'atopó l'archivu d'índiz $1",
	'scanset_index_file_error'          => "ScanSet: Error nel formatu d'archivu d'índiz na llinia $1",
	'scanset_invalid_volume'            => 'ScanSet: Volume non válidu',
	'scanset_next'                      => 'Sig &gt;',
	'scanset_prev'                      => '&lt; Ant',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'scanset_next' => 'Следващи &gt;',
	'scanset_prev' => '&lt; Предишни',
);

/** Bengali (বাংলা)
 * @author Zaheen
 */
$messages['bn'] = array(
	'scanset-desc'                      => 'Andreas Grosz-এর CD/DVD সেটগুলি থেকে স্ক্যান করা পাতার ছবিগুলি দেখুন',
	'scanset_no_name'                   => 'স্ক্যানসেট: আপনাকে অবশ্যই একটি স্ক্যান সেট নাম দিতে হবে, যেমন &lt;scanset name="EB1911" /&gt;।',
	'scanset_invalid_name'              => 'স্ক্যানসেট: অবৈধ বা অবর্তমান স্ক্যান সেট।',
	'scanset_unrecognised_index_format' => 'স্ক্যানসেট: ইন্ডেক্স ফরম্যাট অজানা',
	'scanset_opendir_error'             => 'স্ক্যানসেট: ত্রুটি, $1 ডিরেক্টরি খোলা যাচ্ছে না',
	'scanset_no_files'                  => 'স্ক্যানসেট: প্রদত্ত ডিরেক্টরিতে কোন ফাইল নেই।',
	'scanset_no_volumes'                => 'স্ক্যানসেট: কোন ভলিউম ডিরেক্টরি খুঁজে পাওয়া যায়নি',
	'scanset_missing_index_file'        => 'স্ক্যানসেট: $1 ইনডেক্স ফাইলটি খুঁজে পাওয়া যায়নি',
	'scanset_index_file_error'          => 'স্ক্যানসেট: $1 নং লাইনে ইন্ডেক্স ফাইল ফরম্যাটে ত্রুটি',
	'scanset_invalid_volume'            => 'স্ক্যানসেট: অবৈধ ভলিউম',
	'scanset_next'                      => 'পরবর্তী &gt;',
	'scanset_prev'                      => '&lt; পূর্ববর্তী',
);

/** Czech (Česky)
 * @author Matěj Grabovský
 * @author Danny B.
 */
$messages['cs'] = array(
	'scanset-desc'                      => 'Zobrazuje stránky s naskenovanými obrázky z CD/DVD Andrease Grosza',
	'scanset_no_name'                   => 'ScanSet: Musíte uvést název skenované množiny, např. &lt;scanset name="EB1911" /&gt;',
	'scanset_invalid_name'              => 'ScanSet: Neplatná nebo chybějící skenovaná množina',
	'scanset_unrecognised_index_format' => 'ScanSet: Neznámý formát indexu',
	'scanset_opendir_error'             => 'ScanSet: Chyba, není možné otevřít adresář $1',
	'scanset_no_files'                  => 'ScanSet: V uvedeném adresáři se nenacházejí žádné soubory.',
	'scanset_no_volumes'                => 'ScanSet: Nebyly nalezeny žádné adresáře svazků',
	'scanset_missing_index_file'        => 'ScanSet: Indexový soubor $1 nebyl nalezen',
	'scanset_index_file_error'          => 'ScanSet: Chyba ve formátu indexového souboru na řádku $1',
	'scanset_invalid_volume'            => 'ScanSet: Neplatný svazek',
	'scanset_next'                      => 'Další →',
	'scanset_prev'                      => '← Předchozí',
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'scanset-desc'                      => 'Darstellung gescannter Seiten von Andreas Groszs CD/DVD-Set',
	'scanset_no_name'                   => 'ScanSet: Es muss ein Scan-Set-Name angegeben werden, z. B. &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet: Ungültiges oder fehlendes Scan-Set.',
	'scanset_unrecognised_index_format' => 'ScanSet: Unbekanntes Indexformat',
	'scanset_opendir_error'             => 'ScanSet: Fehler, Verzeichnis $1 kann nicht geöffnet werden',
	'scanset_no_files'                  => 'ScanSet: Im angegebenen Verzeichnis sind keine Dateien vorhanden.',
	'scanset_no_volumes'                => 'ScanSet: Kein Volume-Verzeichnis gefunden',
	'scanset_missing_index_file'        => 'ScanSet: Indexdatei $1 nicht gefunden',
	'scanset_index_file_error'          => 'ScanSet: Fehler im Format der Indexdatei in Zeile $1',
	'scanset_invalid_volume'            => 'ScanSet: Ungültiges Volume',
	'scanset_next'                      => 'Nächster →',
	'scanset_prev'                      => '← Vorheriger',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'scanset_next' => 'Sekva &gt;',
	'scanset_prev' => '&lt; Antaŭa',
);

/** فارسی (فارسی)
 * @author Huji
 */
$messages['fa'] = array(
	'scanset-desc'                      => 'مشاهدهٔ تصاویر پویش‌شده از لوح فشردهٔ Andreas Grosz',
	'scanset_no_name'                   => 'ScanSet: شما باید نام مجموعه را وارد کنید، مثلاً <scanset name="EB1911" />.',
	'scanset_invalid_name'              => 'ScanSet: مجموعهٔ غیرمجاز یا گمشده',
	'scanset_unrecognised_index_format' => 'ScanSet: بافت نامشخص اندیس',
	'scanset_opendir_error'             => 'ScanSet: خطا، شاخهٔ $1 را نمی‌توان باز کرد.',
	'scanset_no_files'                  => 'ScanSet: هیچ پرونده‌ای در شاخه مشخص‌شده وجود ندارد.',
	'scanset_no_volumes'                => 'ScanSet: فهرست جلدها پیدا نشد',
	'scanset_missing_index_file'        => 'ScanSet: پرونده اندیس $1 پیدا نشد',
	'scanset_index_file_error'          => 'ScanSet: خطا در بافت پرونده اندیس در سطر $1',
	'scanset_invalid_volume'            => 'ScanSet: مجلد غیرمجاز',
	'scanset_next'                      => 'بعدی >',
	'scanset_prev'                      => '< قبلی',
);


/** Finnish (Suomi)
 * @author Nike
 */
$messages['fi'] = array(
	'scanset_next' => 'Seuraava →',
	'scanset_prev' => '← Edellinen',
);

/** French (Français)
 * @author Dereckson
 */
$messages['fr'] = array(
	'scanset-desc'                      => 'Affiche les images numérisées à partir des paramétrages CD/DVD d’Andréas Grosz',
	'scanset_no_name'                   => 'ScanSet : Vous devez spécifier un nom pour le scanset, par exemple &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet : set invalide ou manquant',
	'scanset_unrecognised_index_format' => "ScanSet : Format d'index inconnu",
	'scanset_opendir_error'             => "ScanSet : Erreur, impossible d'ouvrir le répertoire $1",
	'scanset_no_files'                  => 'ScanSet : Le répertoire spécifié est vide.',
	'scanset_no_volumes'                => 'ScanSet : Aucun répertoire trouvé sur ce disque',
	'scanset_missing_index_file'        => "ScanSet : Fichier d'index $1 manquant",
	'scanset_index_file_error'          => "ScanSet : Erreur à la ligne $1 du fichier d'index",
	'scanset_invalid_volume'            => 'ScanSet : Volume non valide',
	'scanset_next'                      => 'Suivant &gt;',
	'scanset_prev'                      => '&lt; Précédent',
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'scanset-desc'                      => 'Afiche les émâges numerisâs dês los paramètrâjos CD/DVD d’Andréas Grosz.',
	'scanset_no_name'                   => 'ScanSet : vos dête spècefiar un nom por lo scansèt, per ègzemplo &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet : scansèt envalido ou manquent',
	'scanset_unrecognised_index_format' => 'ScanSet : format d’endèxe encognu',
	'scanset_opendir_error'             => 'ScanSet : èrror, empossiblo d’uvrir lo rèpèrtouèro $1',
	'scanset_no_files'                  => 'ScanSet : lo rèpèrtouèro spècefiâ est vouedo.',
	'scanset_no_volumes'                => 'ScanSet : nion rèpèrtouèro trovâ sur cél disco',
	'scanset_missing_index_file'        => 'ScanSet : fichiér d’endèxe $1 manquent',
	'scanset_index_file_error'          => 'ScanSet : èrror a la legne $1 du fichiér d’endèxe',
	'scanset_invalid_volume'            => 'ScanSet : volumo envalido',
	'scanset_next'                      => 'Siuvent &gt;',
	'scanset_prev'                      => '&lt; Prècèdent',
);

/** Galician (Galego)
 * @author Alma
 */
$messages['gl'] = array(
	'scanset_opendir_error' => 'ScanSet: Erro, non se pode abrir o directorio $1',
	'scanset_next'          => 'Seguinte &gt;',
	'scanset_prev'          => '&lt; Anterior',

);

/** Croatian (Hrvatski)
 * @author Dnik
 * @author SpeedyGonsales
 */
$messages['hr'] = array(
	'scanset_no_name'                   => 'SkeniranaKnjiga: Morate zadati naziv skenirane knjige, npr. &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'SkeniranaKnjiga: Nevažeća ili nepostojeća skenirana knjiga.',
	'scanset_unrecognised_index_format' => 'SkeniranaKnjiga: Nepoznat format indeksa',
	'scanset_opendir_error'             => 'SkeniranaKnjiga: Greška, ne mogu otvoriti direktorij $1',
	'scanset_no_files'                  => 'SkeniranaKnjiga: Nema datoteka u zadanom direktoriju.',
	'scanset_no_volumes'                => 'SkeniranaKnjiga: Nisu nađeni direktoriji sa svescima (volumenima)',
	'scanset_missing_index_file'        => 'SkeniranaKnjiga: Indeksna datoteka $1 nije pronađena',
	'scanset_index_file_error'          => 'SkeniranaKnjiga: Greška u formatu indeksne datoteke, u retku $1',
	'scanset_invalid_volume'            => 'Skenirana knjiga: Loš svezak (volumen)',
	'scanset_next'                      => 'Slijedeći &gt;',
	'scanset_prev'                      => '&lt; Prethodni',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'scanset-desc'                      => 'Skanowane wobrazy strony z CD/DVD-sadźbow Andreasa Großa wobhladać',
	'scanset_no_name'                   => 'ScanSet: Dyrbiš mjeno za skanowansku sadźbu podać, na př. &lt;mjeno skanowanskeje sadźby="EB1911" /&gt;',
	'scanset_invalid_name'              => 'ScanSet: Njepłaćiwa abo falowaca skanowanska sadźba.',
	'scanset_unrecognised_index_format' => 'ScanSet: Njeznaty indeksowy format',
	'scanset_opendir_error'             => 'ScanSet: Zmylk, zapis $1 njeda so wočinjeć',
	'scanset_no_files'                  => 'ScanSet: Žane dataje w podatym zapisu.',
	'scanset_no_volumes'                => 'ScanSet: Žane zapisy za zwjazki namakane',
	'scanset_missing_index_file'        => 'ScanSet: Indeksowa dataja $1 njenamakana',
	'scanset_index_file_error'          => 'ScanSet: Zmylk we formaće indeksoweje dataje w lince $1',
	'scanset_invalid_volume'            => 'ScanSet: Njepłaćiwy zwjazk',
	'scanset_next'                      => 'Přichodny &gt;',
	'scanset_prev'                      => '&lt; Předchadny',
);

/** Hungarian (Magyar)
 * @author Bdanee
 * @author KossuthRad
 */
$messages['hu'] = array(
	'scanset_no_name'                   => 'ScanSet: meg kell adnod a sorozat nevét, pl. &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet: érvénytelen vagy hiányzó sorozat.',
	'scanset_unrecognised_index_format' => 'ScanSet: ismeretlen indexformátum',
	'scanset_opendir_error'             => 'ScanSet: hiba, a(z) $1 könyvtár nem nyitható meg',
	'scanset_no_files'                  => 'ScanSet: nincsenek fájlok a megadott könyvtárban.',
	'scanset_no_volumes'                => 'ScanSet: nem található egyetlen kötetkönyvtár sem',
	'scanset_missing_index_file'        => 'ScanSet: a(z) $1 indexfájl nem található',
	'scanset_index_file_error'          => 'ScanSet: hiba az indexfájl $1. sorában',
	'scanset_invalid_volume'            => 'ScanSet: érvénytelen kötet',
	'scanset_next'                      => 'Következő &gt;',
	'scanset_prev'                      => '← Előző',
);

/** Italian (Italiano)
 * @author BrokenArrow
 */
$messages['it'] = array(
	'scanset-desc'                      => 'Mostra le immagini delle scansioni provenienti dai CD/DVD di Andreas Grosz',
	'scanset_no_name'                   => 'ScanSet: è necessario indicare un nome per il set di scansioni, ad es. &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet: set di scansioni errato o mancante.',
	'scanset_unrecognised_index_format' => 'ScanSet: formato indice sconosciuto',
	'scanset_opendir_error'             => 'ScanSet: errore, impossibile aprire la directory $1',
	'scanset_no_files'                  => 'Scanset: nella directory indicata non è presente alcun file.',
	'scanset_no_volumes'                => 'ScanSet: directory di volume non trovate',
	'scanset_missing_index_file'        => 'ScanSet: file indice $1 non trovato',
	'scanset_index_file_error'          => 'ScanSet: errore nel formato del file indice alla riga $1',
	'scanset_invalid_volume'            => 'ScanSet: volume non valido',
	'scanset_next'                      => 'Successivo &gt;',
	'scanset_prev'                      => '&lt; Precedente',
);

/** Japanese (日本語)
 * @author JtFuruhata
 */
$messages['ja'] = array(
	'scanset-desc'                      => 'スキャンページ画像を、Andreas Grosz のCD/DVD索引形式で表示する',
	'scanset_no_name'                   => 'ScanSet: スキャンセット名を指定する必要があります　（例：&lt;scanset name="EB1911" /&gt;）',
	'scanset_invalid_name'              => 'ScanSet: スキャンセットが見つからないか不正です',
	'scanset_unrecognised_index_format' => 'ScanSet: 対応していない索引形式です',
	'scanset_opendir_error'             => 'ScanSet: ディレクトリ $1 を開くことができません',
	'scanset_no_files'                  => 'ScanSet: 指定されたディレクトリに該当するファイルはありません',
	'scanset_no_volumes'                => 'ScanSet: 文献ディレクトリが見つかりません',
	'scanset_missing_index_file'        => 'ScanSet: 索引ファイル $1 が見つかりません',
	'scanset_index_file_error'          => 'ScanSet: 索引ファイル $1行目の書式に誤りがあります',
	'scanset_invalid_volume'            => 'ScanSet: 文献の指定が不正です',
	'scanset_next'                      => '次へ &gt;',
	'scanset_prev'                      => '&lt; 前へ',
);

/** Limburgish (Limburgs)
 * @author Ooswesthoesbes
 * @author Matthias
 */
$messages['li'] = array(
	'scanset-desc'                      => "Maakt 't mogelijk gescande aafbeeldinge van de cd's/dvd's van Andreas Grosz te bekijke",
	'scanset_no_name'                   => 'ScanSet: geer mót \'ne naam veure scanset opgaeve, wie beveurbeild &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet: óngeljige of missende scanset',
	'scanset_unrecognised_index_format' => 'ScanSet: indeksopmaak wuuertj neet herkintj',
	'scanset_opendir_error'             => 'ScanSet: fout, kin map $1 neet äöpene',
	'scanset_no_files'                  => 'ScanSet: gein bestenj aanwezig inne opgegaeve map.',
	'scanset_no_volumes'                => 'ScanSet: gein mappe veur volume aangetróffe',
	'scanset_missing_index_file'        => 'ScanSet: indeksbestandj $1 neet aangetróffe',
	'scanset_index_file_error'          => 'ScanSet: fout in op,aal indeksbestandj in regel $1',
	'scanset_invalid_volume'            => 'ScanSet: óngeljige volume',
	'scanset_next'                      => 'Volgende &gt;',
	'scanset_prev'                      => '&lt; Vörge',
);

/** Lithuanian (Lietuvių)
 * @author Matasg
 */
$messages['lt'] = array(
	'scanset_next' => 'Toliau &gt;',
	'scanset_prev' => '&lt; Ankstesnis',
);

/** Dutch (Nederlands)
 * @author Siebrand
 * @author SPQRobin
 */
$messages['nl'] = array(
	'scanset-desc'                      => "Maakt het mogelijk gescande afbeeldingen van de cd's/dvd's van Andreas Grosz te bekijken",
	'scanset_no_name'                   => 'ScanSet: u moet een naam voor de scanset opgeven, zoals bijvoorbeeld &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet: ongeldige of missende scanset',
	'scanset_unrecognised_index_format' => 'ScanSet: indexopmaak wordt niet herkend',
	'scanset_opendir_error'             => 'ScanSet: fout, can map $1 niet openen',
	'scanset_no_files'                  => 'ScanSet: geen bestanden aanwezig in de opgegeven map.',
	'scanset_no_volumes'                => 'ScanSet: geen mappen voor volumen aangetroffen',
	'scanset_missing_index_file'        => 'ScanSet: indexbestand $1 niet aangetroffen',
	'scanset_index_file_error'          => 'ScanSet: fout in opmaak indexbestand in regel $1',
	'scanset_invalid_volume'            => 'ScanSet: ongeldig volumen',
	'scanset_next'                      => 'Volgende &gt;',
	'scanset_prev'                      => '&lt; Vorige',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'scanset-desc'                      => 'Vis bilder av skannede sider fra Andreas Grosz’ CD-/DVD-sett',
	'scanset_no_name'                   => 'ScanSet: Du må angi navnet på et ScanSet, f.eks. &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet: Ugyldig eller manglende ScanSet.',
	'scanset_unrecognised_index_format' => 'ScanSet: Ukjent indeksformat',
	'scanset_opendir_error'             => 'ScanSet: Feil, kan ikke åpne mappen $1',
	'scanset_no_files'                  => 'ScanSet: Ingen filer i mappen som ble oppgitt.',
	'scanset_no_volumes'                => 'ScanSet: Ingen volummapper funnet',
	'scanset_missing_index_file'        => 'ScanSet: Indeksfil $1 ikke funnet',
	'scanset_index_file_error'          => 'ScanSet: Feil i indeksfilformat på linje $1',
	'scanset_invalid_volume'            => 'ScanSet: Ugyldig volum',
	'scanset_next'                      => 'Neste &gt;',
	'scanset_prev'                      => '&lt; Forrige',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'scanset_no_name'                   => 'ScanSet : Devètz especificar un nom pel scanset, per exemple &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet : set invalid o mancant',
	'scanset_unrecognised_index_format' => "ScanSet : Format d'indèx desconegut",
	'scanset_opendir_error'             => 'ScanSet : Error, impossible de dobrir lo repertòri $1',
	'scanset_no_files'                  => 'ScanSet : Lo repertòri especificat es void.',
	'scanset_no_volumes'                => 'ScanSet : Cap de repertòri pas trobat sus aqueste disc',
	'scanset_missing_index_file'        => "ScanSet : Fichièr d'indèx $1 mancant",
	'scanset_index_file_error'          => "ScanSet : Error a la linha $1 del fichièr d'indèx",
	'scanset_invalid_volume'            => 'ScanSet : Volum invalid',
	'scanset_next'                      => 'Seguent &gt;',
	'scanset_prev'                      => '&lt; Precedent',
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'scanset_next' => 'Nast. &gt;',
	'scanset_prev' => '&lt; Poprz.',
);

/** Portuguese (Português)
 * @author 555
 * @author Malafaya
 */
$messages['pt'] = array(
	'scanset-desc'                      => 'Ver imagens de páginas digitalizadas do conjunto de CD/DVD de Andreas Grosz',
	'scanset_no_name'                   => 'ScanSet: É necessário especificar um nome de conjunto de scans (por exemplo, &lt;scanset name="EB1911" /&gt;).',
	'scanset_invalid_name'              => 'ScanSet: conjunto de scans inválido ou não encontrado.',
	'scanset_unrecognised_index_format' => 'ScanSet: formato de índice desconhecido',
	'scanset_opendir_error'             => 'ScanSet: não é possível abrir o diretório $1',
	'scanset_no_files'                  => 'ScanSet: não há ficheiros no diretório especificado.',
	'scanset_no_volumes'                => 'ScanSet: não foram encontrados diretórios contendo livros',
	'scanset_missing_index_file'        => 'ScanSet: o ficheiro de índice $1 não foi encontrado',
	'scanset_index_file_error'          => 'ScanSet: erro no formato de ficheiro-índice na linha $1',
	'scanset_invalid_volume'            => 'ScanSet: livro inválido',
	'scanset_next'                      => 'Próximo &gt;',
	'scanset_prev'                      => '&lt; Anterior',
);

/** Russian (Русский)
 * @author .:Ajvol:.
 * @author HalanTul
 */
$messages['ru'] = array(
	'scanset-desc'                      => 'Просмотр изображений отсканированных страниц из CD/DVD коллекций Андреаса Гроса (Andreas Grosz)',
	'scanset_no_name'                   => 'ScanSet: вы должны указать название коллекции снимков, например &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet: ошибка в названии набора снимков, или указанный набор отсутствует',
	'scanset_unrecognised_index_format' => 'ScanSet: неизвестный формат индекса',
	'scanset_opendir_error'             => 'ScanSet: ошибка, невозможно открыть директорию $1',
	'scanset_no_files'                  => 'ScanSet: не найдено файлов в указанной директории.',
	'scanset_no_volumes'                => 'ScanSet: не найдено директорий для томов',
	'scanset_missing_index_file'        => 'ScanSet: файл индекса $1 не найден',
	'scanset_index_file_error'          => 'ScanSet: ошибка в файле индекса в строке $1',
	'scanset_invalid_volume'            => 'ScanSet: ошибочный том',
	'scanset_next'                      => 'Следующая &gt;',
	'scanset_prev'                      => '&lt; Предыдущая',
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'scanset-desc'                      => 'Андреас Грос (Andreas Grosz) CD/DVD коллекциятыттан сканердаммыт ойуулары көрүү',
	'scanset_no_name'                   => 'ScanSet: ойуулар/хаартыскалар уопсай ааттарын суруйуохтааххын, холобур &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet: ойуу/хаартыска бөлөҕө суох эбэтэр аата сыыһа суруллубут.',
	'scanset_unrecognised_index_format' => 'ScanSet: Индекс биллибэт формата',
	'scanset_opendir_error'             => 'ScanSet: сатаммат, $1 директория кыайан арыллыбат',
	'scanset_no_files'                  => 'ScanSet: Сатаммата, ыйыллыбыт директорияҕа билэ суох.',
	'scanset_no_volumes'                => 'ScanSet: не найдено директорий для томов',
	'scanset_missing_index_file'        => 'ScanSet: $1 Индекс билэтэ көстүбэтэ',
	'scanset_index_file_error'          => 'ScanSet: Индекс билэтигэр $1 строкаҕа сыыһалаах',
	'scanset_invalid_volume'            => 'ScanSet: ошибочный том',
	'scanset_next'                      => 'Аныгыскы &gt;',
	'scanset_prev'                      => '&lt; Иннинээҕи',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'scanset-desc'                      => 'Zobraziť stránky s naskenovanými obrázkami z CD/DVD Andreasa Grosza',
	'scanset_no_name'                   => 'ScanSet: Musíte uviesť názov skenovanej množiny, napr. &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet: neplatná alebo chýbajúca skenovaná množina.',
	'scanset_unrecognised_index_format' => 'ScanSet: Neznámy formát indexu',
	'scanset_opendir_error'             => 'ScanSet: Chyba, nie je možné otvoriť adresár $1',
	'scanset_no_files'                  => 'ScanSet: V uvedenom adresári sa nenachádzajú žiadne súbory.',
	'scanset_no_volumes'                => 'ScanSet: Neboli nájdené žiadne adresáre zväzkov',
	'scanset_missing_index_file'        => 'ScanSet: Indexový súbor $1 nebol nájdený',
	'scanset_index_file_error'          => 'ScanSet: Chyba vo formáte indexového súboru na riadku $1',
	'scanset_invalid_volume'            => 'ScanSet: Neplatný zväzok',
	'scanset_next'                      => 'Ďalej &gt;',
	'scanset_prev'                      => '&lt; Späť',
);

/** ћирилица (ћирилица)
 * @author Sasa Stefanovic
 */
$messages['sr-ec'] = array(
	'scanset_next' => 'След. &gt;',
	'scanset_prev' => '&lt; Прет.',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'scanset_no_name'                   => 'ScanSet: Der mout n Scan-Set-Noome anroat wäide, t.B. &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet: Uungultich of failjend Scan-Set.',
	'scanset_unrecognised_index_format' => 'ScanSet: Uunbekoand Indexformoat',
	'scanset_opendir_error'             => 'ScanSet: Failer, Ferteeknis $1 kon nit eepend wäide',
	'scanset_no_files'                  => 'ScanSet: In dät anroate Ferteeknis sunt neen Doatäie deer.',
	'scanset_no_volumes'                => 'ScanSet: Neen Volume-Ferteeknis fuunen',
	'scanset_missing_index_file'        => 'ScanSet: Indexdoatäi $1 nit fuunen',
	'scanset_index_file_error'          => 'ScanSet: Failer in dät Formoat fon ju Indexdoatäi in Riege $1',
	'scanset_invalid_volume'            => 'ScanSet: Uungultich Volume',
	'scanset_next'                      => 'Naiste →',
	'scanset_prev'                      => '← Foarige',
);

/** Swedish (Svenska)
 * @author Lejonel
 * @author M.M.S.
 */
$messages['sv'] = array(
	'scanset-desc'                      => "Visa skannade sid bilder från Andreas Grosz's CD/DVD grupper",
	'scanset_no_name'                   => 'ScanSet: Du måste ange ett namn på ett scan-set, t.ex. &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name'              => 'ScanSet: Angivet scan-set är felaktigt eller finns inte.',
	'scanset_unrecognised_index_format' => 'ScanSet: Okänt indexformat',
	'scanset_opendir_error'             => 'ScanSet: Fel, kan inte öppna katalogen $1',
	'scanset_no_files'                  => 'ScanSet: Det finns inga filer i den angivna katalogen.',
	'scanset_no_volumes'                => 'ScanSet: Inga volymkataloger hittades',
	'scanset_missing_index_file'        => 'ScanSet: Indexfilen $1 hittades inte',
	'scanset_index_file_error'          => 'ScanSet: Fel i indexfilsformatet på rad $1',
	'scanset_invalid_volume'            => 'ScanSet: Ogiltig volym',
	'scanset_next'                      => 'Nästa &gt;',
	'scanset_prev'                      => '&lt; Föregående',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'scanset_no_files'           => 'ScanSet: మీరు చెప్పిన డైరెక్టరీలో ఫైళ్ళు ఏమీ లేవు.',
	'scanset_missing_index_file' => 'ScanSet: $1 అనే సూచిక ఫైలు కనబడలేదు',
	'scanset_next'               => 'తర్వాత &gt;',
	'scanset_prev'               => '&lt; గత',
);

/** Tajik (Тоҷикӣ)
 * @author Ibrahim
 */
$messages['tg'] = array(
	'scanset_next' => 'Баъдӣ &gt;',
	'scanset_prev' => '&lt; Қаблӣ',
);

/** Turkish (Türkçe)
 * @author Erkan Yilmaz
 */
$messages['tr'] = array(
	'scanset_next' => 'Sonraki &gt;',
	'scanset_prev' => '&lt; Önceki',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'scanset_next' => 'Sau →',
	'scanset_prev' => '← Trước',
);

/** Volapük (Volapük)
 * @author Malafaya
 */
$messages['vo'] = array(
	'scanset_next' => 'Sököl &gt;',
	'scanset_prev' => '&lt; Büik',
);

