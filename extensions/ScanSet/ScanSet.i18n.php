<?php
/**
 * Internationalisation file for extension Scan Set.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
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

$messages['ar'] = array(
	'scanset_no_name' => 'سكان مجموعة: يجب عليك تحديد اسم سكان مجموعة، مثال &lt;scanset name="EB1911" /&gt;.',
	'scanset_invalid_name' => 'سكان مجموعة: سكان مجموعة غير صحيحة أو ناقصة.',
	'scanset_unrecognised_index_format' => 'سكان مجموعة: صيغة فهرس غير معروفة',
	'scanset_opendir_error' => 'سكان مجموعة: خطأ، لا يمكن فتح المجلد $1',
	'scanset_no_files' => 'سكان مجموعة: لا توجد ملفات في المجلد المحدد.',
	'scanset_no_volumes' => 'سكان مجموعة: لا توجد مجلدات حجم',
	'scanset_missing_index_file' => 'سكان مجموعة: ملف الفهرس $1 لم يتم العثور عليه',
	'scanset_index_file_error' => 'سكان مجموعة: خطأ في صيغة ملف الفهرس عند السطر $1',
	'scanset_invalid_volume' => 'سكان مجموعة: حجم غير صحيح',
	'scanset_next' => 'التالي &gt;',
	'scanset_prev' => '&lt; السابق',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'scanset_next' => 'Следващи &gt;',
	'scanset_prev' => '&lt; Предишни',
);

# فارسی (Huji)
$messages['fa'] = array(
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

$messages['hsb'] = array(
	'scanset_no_name' => 'ScanSet: Dyrbiš mjeno za skanowansku sadźbu podać, na př. &lt;mjeno skanowanskeje sadźby="EB1911" /&gt;',
	'scanset_invalid_name' => 'ScanSet: Njepłaćiwa abo falowaca skanowanska sadźba.',
	'scanset_unrecognised_index_format' => 'ScanSet: Njeznaty indeksowy format',
	'scanset_opendir_error' => 'ScanSet: Zmylk, zapis $1 njeda so wočinjeć',
	'scanset_no_files' => 'ScanSet: Žane dataje w podatym zapisu.',
	'scanset_no_volumes' => 'ScanSet: Žane zapisy za zwjazki namakane',
	'scanset_missing_index_file' => 'ScanSet: Indeksowa dataja $1 njenamakana',
	'scanset_index_file_error' => 'ScanSet: Zmylk we formaće indeksoweje dataje w lince $1',
	'scanset_invalid_volume' => 'ScanSet: Njepłaćiwy zwjazk',
	'scanset_next' => 'Přichodny &gt;',
	'scanset_prev' => '&lt; Předchadny',
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

/** Galician (Galego)
 * @author Alma
 */
$messages['gl'] = array(
	'scanset_opendir_error' => 'ScanSet: Erro, non se pode abrir o directorio $1',
	'scanset_next'          => 'Seguinte &gt;',
	'scanset_prev'          => '&lt; Anterior',

);

/** Dutch (Nederlands)
 * @author Siebrand
 * @author SPQRobin
 */
$messages['nl'] = array(
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
	'scanset_next' => 'Seguent &gt;',
	'scanset_prev' => '&lt; Precedent',
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'scanset_next' => 'Nast. &gt;',
	'scanset_prev' => '&lt; Poprz.',
);

/** Russian (Русский)
 * @author .:Ajvol:.
 */
$messages['ru'] = array(
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

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
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

/** Swedish (Svenska)
 * @author Lejonel
 */
$messages['sv'] = array(
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

/** Volapük (Volapük)
 * @author Malafaya
 */
$messages['vo'] = array(
	'scanset_next' => 'Sököl &gt;',
	'scanset_prev' => '&lt; Büik',
);

