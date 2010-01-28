<?php
/**
 * Internationalisation file for extension PagedTiffHandler.
 *
 * @addtogroup Extensions
 */

$messages = array();

/** English (English)
 * @author Hallo Welt! - Medienwerkstatt GmbH
 */
$messages['en'] = array(
	'tiff-desc' => 'Handler for viewing TIFF files in image mode',
	'tiff_no_metadata' => 'Cannot get metadata from TIFF',
	'tiff_page_error' => 'Page number not in range',
	'tiff_too_many_embed_files' => 'The image contains too many embedded files.',
	'tiff_sourcefile_too_large' => 'The resolution of the source file is too large. No thumbnail will be generated.',
	'tiff_file_too_large' => 'The uploaded file is too large and was rejected.',
	'tiff_out_of_service' => 'The uploaded file could not be processed. ImageMagick is not available.',
	'tiff_too_much_meta' => 'Metadata uses too much space.',
	'tiff_error_cached' => 'This file can only be rerendered after the the caching interval.',
	'tiff_size_error' => 'The reported file size does not match the actual file size.',
	'tiff_script_detected' => 'The uploaded file contains scripts.',
	'tiff_bad_file' => 'The uploaded file contains errors.',
	'tiff-file-info-size' => '(page $5, $1 × $2 pixel, file size: $3, MIME type: $4)',
 );

/** Message documentation (Message documentation)
 * @author Hallo Welt! - Medienwerkstatt GmbH
 */
$messages['qqq'] = array(
	'tiff-desc' => 'Short description of the extension, shown in [[Special:Version]]. Do not translate or change links.',
	'tiff_no_metadata' => 'Error message shown when no metadata extraction is not possible',
	'tiff_page_error' => 'Error message shown when page number is out of range',
	'tiff_too_many_embed_files' => 'Error message shown when the uploaded image contains too many embedded files.',
	'tiff_sourcefile_too_large' => 'Error message shown when the resolution of the source file is too large.',
	'tiff_file_too_large' => 'Error message shown when the uploaded file is too large.',
	'tiff_out_of_service' => 'Error message shown when the uploaded file could not be processed by external renderer (ImageMagick).',
	'tiff_too_much_meta' => 'Error message shown when the metadata uses too much space.',
	'tiff_error_cached' => 'Error message shown when a error occurres and it is cached.',
	'tiff_size_error' => 'Error message shown when the reported file size does not match the actual file size.',
	'tiff_script_detected' => 'Error message shown when the uploaded file contains scripts.',
	'tiff_bad_file' => 'Error message shown when the uploaded file contains errors.',
	'tiff-file-info-size' => 'Information about the image dimensions etc. on image page. Extended by page information',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'tiff-desc' => 'Hanteerder vir die besigtiging van TIFF-lêers in die beeld-modus',
	'tiff_no_metadata' => 'Kan nie metadata vanuit TIFF kry nie',
	'tiff_page_error' => 'Bladsynommer kom nie in dokument voor nie',
	'tiff_too_many_embed_files' => 'Die beeld bevat te veel ingebedde lêers.',
	'tiff_sourcefile_too_large' => 'Die resolusie van die bronlêer is te groot. Geen duimnael sal gegenereer word nie.',
	'tiff_file_too_large' => 'Die opgelaaide lêer is te groot en is verwerp.',
	'tiff_out_of_service' => 'Die opgelaaide lêer kon nie verwerk word nie. ImageMagick is nie beskikbaar is nie.',
	'tiff_too_much_meta' => 'Metadata gebruik te veel spasie.',
	'tiff_error_cached' => 'Hierdie lêer kan slegs na die die kas-interval gerendeer word.',
	'tiff_size_error' => 'Die gerapporteerde lêergrootte stem nie met die lêer se werklike grootte ooreen nie.',
	'tiff_script_detected' => 'Die opgelaaide lêer bevat skrips.',
	'tiff_bad_file' => 'Die opgelaaide lêer bevat foute.',
	'tiff-file-info-size' => '(bladsy $5, $1 × $2 spikkels, lêergrootte: $3, MIME-tipe: $4)',
);

/** Breton (Brezhoneg)
 * @author Fohanno
 */
$messages['br'] = array(
	'tiff_no_metadata' => "Ne c'haller ket tapout metaroadennoù eus TIFF",
	'tiff_too_many_embed_files' => 'Re a restroù enklozet zo er skeudenn.',
	'tiff_file_too_large' => 'Re vras eo ar restr karget ha distaolet eo bet.',
	'tiff_too_much_meta' => "Ar metaroadennoù a implij re a lec'h.",
	'tiff_script_detected' => 'Skriptoù zo er restr karget.',
	'tiff_bad_file' => 'Fazioù zo er restr karget.',
	'tiff-file-info-size' => '(pajenn $5, $1 × $2 piksel, ment ar restr : $3, seurt MIME : $4)',
);

/** German (Deutsch)
 * @author Hallo Welt! - Medienwerkstatt GmbH
 */
$messages['de'] = array(
	'tiff-desc' => 'Schnittstelle für die Ansicht von TIFF-Dateien im Bilder-Modus',
	'tiff_no_metadata' => 'Keine Metadaten im TIFF vorhanden.',
	'tiff_page_error' => 'Seitenzahl außerhalb des Dokumentes.',
	'tiff_too_many_embed_files' => 'Die Datei enthält zu viele eingebettete Dateien.',
	'tiff_sourcefile_too_large' => 'Die Quelldatei hat eine zu hohe Auflösung. Es wird kein Thumbnail generiert.',
	'tiff_file_too_large' => 'Die hochgeladene Datei ist zu groß und wurde abgewiesen.',
	'tiff_out_of_service' => 'Die hochgeladene Datei konnte nicht verarbeitet werden. ImageMagick ist nicht verfügbar.',
	'tiff_too_much_meta' => 'Die Metadaten benötigen zu viel Speicherplatz.',
	'tiff_error_cached' => 'Dies Datei kann erst nach Ablauf der Caching-Periode neu gerendert werden.',
	'tiff_size_error' => 'Die errechnete Größe der Datei stimmt nicht mit der tatsächlichen überein.',
	'tiff_script_detected' => 'Die hochgeladene Datei enthält Skripte.',
	'tiff_bad_file' => 'Die hochgeladene Datei ist fehlerhaft.',
	'tiff-file-info-size' => '(Seite $5, $1 × $2 Pixel, Dateigröße: $3, MIME-Typ: $4)',
);

/** French (Français)
 * @author Jagwar
 */
$messages['fr'] = array(
	'tiff_size_error' => 'La taille de fichier indiqué ne correspond pas à la taille réelle du fichier.',
	'tiff_script_detected' => 'Le fichier téléchargé contient des scripts.',
	'tiff_bad_file' => 'Le fichier téléchargé contient des erreurs.',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'tiff-desc' => 'Manipulador para ver ficheiros TIFF no modo de imaxe',
	'tiff_no_metadata' => 'Non se puideron obter os metadatos do TIFF',
	'tiff_page_error' => 'O número da páxina non está no rango',
	'tiff_too_many_embed_files' => 'A imaxe contén moitos ficheiros incorporados.',
	'tiff_sourcefile_too_large' => 'A resolución do ficheiro de orixe é moi grande. Non se xerará ningunha miniatura.',
	'tiff_file_too_large' => 'O ficheiro cargado é moi grande e foi rexeitado.',
	'tiff_out_of_service' => 'O ficheiro cargado non se puido procesar. ImageMagick non está dispoñible.',
	'tiff_too_much_meta' => 'Os metadatos empregan moito espazo.',
	'tiff_error_cached' => 'O ficheiro só se pode renderizar despois do intervalo da caché.',
	'tiff_size_error' => 'O tamaño do ficheiro do que se informou non se corresponde co tamaño real do ficheiro.',
	'tiff_script_detected' => 'O ficheiro cargado contén escrituras.',
	'tiff_bad_file' => 'O ficheiro cargado contén erros.',
	'tiff-file-info-size' => '(páxina $5, $1 × $2 píxeles, tamaño do ficheiro: $3, tipo MIME: $4)',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'tiff-desc' => 'Gestor pro visualisar files TIFF in modo de imagine',
	'tiff_no_metadata' => 'Non pote obtener metadatos ab TIFF',
	'tiff_page_error' => 'Numero de pagina foras del intervallo',
	'tiff_too_many_embed_files' => 'Le imagine contine troppo de files incastrate.',
	'tiff_sourcefile_too_large' => 'Le resolution del file de fonte es troppo alte. Nulle miniatura essera generate.',
	'tiff_file_too_large' => 'Le file incargate es troppo grande e ha essite rejectate.',
	'tiff_out_of_service' => 'Le file incargate non poteva esser processate. ImageMagick non es disponibile.',
	'tiff_too_much_meta' => 'Le metadatos usa troppo de spatio.',
	'tiff_error_cached' => 'Iste file pote solmente esser re-rendite post le expiration de su copia in cache.',
	'tiff_size_error' => 'Le grandor reportate del file non corresponde al grandor real del file.',
	'tiff_script_detected' => 'Le file incargate contine scripts.',
	'tiff_bad_file' => 'Le file incargate contine errores.',
	'tiff-file-info-size' => '(pagina $5, $1 × $2 pixel, grandor del file: $3, typo MIME: $4)',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'tiff-desc' => 'Ракувач за прегледување на TIFF податотеки во сликовен режим',
	'tiff_no_metadata' => 'Не можам да добијам метаподатоци од TIFF',
	'tiff_page_error' => 'Бројот на страница е надвор од опсег',
	'tiff_too_many_embed_files' => 'Сликата содржи премногу вградени податотеки.',
	'tiff_sourcefile_too_large' => 'Резолуцијата на изворната податотека е преголема. Минијатурата нема да биде создадена.',
	'tiff_file_too_large' => 'Подигнатата податотека е преголема и затоа беше одбиена.',
	'tiff_out_of_service' => 'Подигнатата податотека не може да се обработи. ImageMagick не е достапен.',
	'tiff_too_much_meta' => 'Метаподатоците заземаат премногу простор.',
	'tiff_error_cached' => 'Оваа податотека може да се оформи само по кеширање на интервалот.',
	'tiff_size_error' => 'Пријавената големина на податотеката не се совпаѓа со фактичката.',
	'tiff_script_detected' => 'Подигнатата податотека содржи скрипти.',
	'tiff_bad_file' => 'Подигнатата податотека содржи грешки.',
	'tiff-file-info-size' => '(страница $5, $1 × $2 пиксели, големина на податотеката: $3, MIME-тип: $4)',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'tiff-desc' => 'Uitbreiding voor het bekijken van TIFF-bestanden in beeldmodus',
	'tiff_no_metadata' => 'De metadata van het TIFF-bestand kan niet uitgelezen worden',
	'tiff_page_error' => 'Het paginanummer ligt niet binnen het bereik',
	'tiff_too_many_embed_files' => 'De afbeelding bevat te veel ingesloten bestanden.',
	'tiff_sourcefile_too_large' => 'De resolutie van het bronbestand is te groot.
Er kan geen miniatuur worden aangemaakt.',
	'tiff_file_too_large' => 'Het geüploade bestand is te groot en kan niet verwerkt worden.',
	'tiff_out_of_service' => 'Het geüploade bestand kan niet worden verwerkt.
ImageMagick is niet beschikbaar.',
	'tiff_too_much_meta' => 'De metadata gebruikt te veel ruimte.',
	'tiff_error_cached' => 'Dit bestand kan alleen worden verwerkt na de cachinginterval.',
	'tiff_size_error' => 'De gerapporteerde bestandsgrootte komt niet overeen met de werkelijke bestandsgrootte.',
	'tiff_script_detected' => 'Het geüploade bestand bevat scripts.',
	'tiff_bad_file' => 'Het geüploade bestand bevat fouten.',
	'tiff-file-info-size' => '(pagina $5, $1 × $2 pixels, bestandsgrootte: $3, MIME-type: $4)',
);

/** Piedmontese (Piemontèis)
 * @author Dragonòt
 */
$messages['pms'] = array(
	'tiff-desc' => 'Gëstor për vëdde file TIFF an manera figure',
	'tiff_no_metadata' => 'As peul pa pijesse metadat dal TIFF',
	'tiff_page_error' => "Nùmer pàgina pa ant l'antërval",
	'tiff_too_many_embed_files' => 'La figura a conten tròpi file an drinta.',
	'tiff_sourcefile_too_large' => "L'arzolussion dël file sorziss a l'é tròp gròssa. Pa gnun-e figurin-e a saran generà.",
	'tiff_file_too_large' => "Ël file carià a l'é tròp gròss e a l'é stàit arfudà.",
	'tiff_out_of_service' => "Ël file carià a peul pa esse prossessà. ImageMagick a l'é pa disponìbil.",
	'tiff_too_much_meta' => 'Ij Metadat a dòvro tròp spassi.',
	'tiff_error_cached' => "Sto file-sì a peul mach esse rerendù d'apress ëd l'antërval ëd cache.",
	'tiff_size_error' => "La dimension arportà dël file a l'é pa istessa a la dimension atual dël file.",
	'tiff_script_detected' => 'Ël file carià a conten dë script.',
	'tiff_bad_file' => "Ël file carià a conten d'eror.",
	'tiff-file-info-size' => '(pàgina $5, $1 x $2 pixel, dimension dël file: $3, sòrt MIME: $4)',
);

/** Russian (Русский)
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'tiff-desc' => 'Обработчик для просмотра TIFF-файлов в виде изображений',
	'tiff_no_metadata' => 'Невозможно получить метаданные из TIFF',
	'tiff_page_error' => 'Номер страницы вне диапазона',
	'tiff_too_many_embed_files' => 'Изображение содержит слишком много встроенных файлов.',
	'tiff_sourcefile_too_large' => 'Разрешение исходного файла слишком велико. Миниатюры создаваться не будут.',
	'tiff_file_too_large' => 'Размер загружаемого файла слишком велик, файл отклонён.',
	'tiff_out_of_service' => 'Загруженный файл не может быть обработан. ImageMagick недоступен.',
	'tiff_too_much_meta' => 'Метаданные занимают слишком много места.',
	'tiff_error_cached' => 'Этот файл может быть повторно перерисован только после кэширующего промежутка.',
	'tiff_size_error' => 'Указанный размер файла не совпадает с фактическим размером файла.',
	'tiff_script_detected' => 'Загруженный файл содержит сценарии.',
	'tiff_bad_file' => 'Загруженный файл содержит ошибки.',
	'tiff-file-info-size' => '(страница $5, $1 × $2 пикселов, размер файла: $3, MIME-тип: $4)',
);

/** Yiddish (ייִדיש)
 * @author פוילישער
 */
$messages['yi'] = array(
	'tiff_too_much_meta' => 'מעטאַדאַטן באַניצן צו פֿיל פלאַץ.',
	'tiff-file-info-size' => '(בלטַט $5, $1 × $2 פיקסעל, טעקע גרייס: $3, טיפ MIME: $4)',
);

