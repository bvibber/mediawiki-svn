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
	'tiff_targetfile_too_large' => 'The resolution of the target file is too large. No thumbnail will be generated.',	
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
	'tiff_targetfile_too_large' => 'Error message shown when the resolution of the target file is too large.',	
	'tiff_file_too_large' => 'Error message shown when the uploaded file is too large.',
	'tiff_out_of_service' => 'Error message shown when the uploaded file could not be processed by external renderer (ImageMagick).',
	'tiff_too_much_meta' => 'Error message shown when the metadata uses too much space.',
	'tiff_error_cached' => 'Error message shown when a error occurres and it is cached.',
	'tiff_size_error' => 'Error message shown when the reported file size does not match the actual file size.',
	'tiff_script_detected' => 'Error message shown when the uploaded file contains scripts.',
	'tiff_bad_file' => 'Error message shown when the uploaded file contains errors.',
	'tiff-file-info-size' => 'Information about the image dimensions etc. on image page. Extended by page information',
);

/** Message documentation (Message documentation) */
$messages['qqq'] = array(
	'tiff-desc' => 'Short description of the extension, shown in [[Special:Version]]. Do not translate or change links.',
	'tiff_no_metadata' => 'Error message shown when no metadata extraction is not possible',
	'tiff_page_error' => 'Error message shown when page number is out of range',
	'tiff_too_many_embed_files' => 'Error message shown when the uploaded image contains too many embedded files.',
	'tiff_sourcefile_too_large' => 'Error message shown when the resolution of the source file is too large.',
	'tiff_targetfile_too_large' => 'Error message shown when the resolution of the target file is too large.',
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

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 * @author Wizardist
 */
$messages['be-tarask'] = array(
	'tiff-desc' => 'Апрацоўшчык для прагляду TIFF-файлаў у выглядзе выяваў',
	'tiff_no_metadata' => 'Немагчыма атрымаць мэта-зьвесткі з TIFF-файла',
	'tiff_page_error' => 'Нумар старонкі паза дыяпазонам',
	'tiff_too_many_embed_files' => 'Выява ўтрымлівае зашмат убудаваных файлаў.',
	'tiff_sourcefile_too_large' => 'Разрозьненьне крынічнага файла занадта вялікае. Мініятуры стварацца ня будуць.',
	'tiff_targetfile_too_large' => 'Разрозьненьне файла занадта вялікае. Выява для папярэдняга прагляду ня будзе створаная.',
	'tiff_file_too_large' => 'Памер загружанага файла — занадта вялікі, файл быў адхілены.',
	'tiff_out_of_service' => 'Загружаны файл ня можа быць апрацаваны. ImageMagick недаступная.',
	'tiff_too_much_meta' => 'Мэта-зьвесткі займаюць зашмат месца.',
	'tiff_error_cached' => 'Гэты файл можа быць паўторна згенэраваны толькі пасьля інтэрвалу для кэшаваньня.',
	'tiff_size_error' => 'Пададзены памер файла не супадае з фактычным памерам файла.',
	'tiff_script_detected' => 'Загружаны файл утрымлівае скрыпты.',
	'tiff_bad_file' => 'Загружаны файл утрымлівае памылкі.',
	'tiff-file-info-size' => '(старонка $5, $1 × $2 піксэляў, памер файла: $3, тып MIME: $4)',
);

/** Breton (Brezhoneg)
 * @author Fohanno
 * @author Fulup
 * @author Y-M D
 */
$messages['br'] = array(
	'tiff-desc' => 'Maveg evit gwelet ar restroù TIFF e mod skeudenn',
	'tiff_no_metadata' => "Ne c'haller ket tapout metaroadennoù eus TIFF",
	'tiff_page_error' => "N'emañ ket niverenn ar bajenn er skeuliad",
	'tiff_too_many_embed_files' => 'Re a restroù enklozet zo er skeudenn.',
	'tiff_sourcefile_too_large' => 'Re vras eo spister ar rest mammenn. Ne vo ket krouet a skeudennig.',
	'tiff_targetfile_too_large' => 'Re vras eo spister ar rest sibl. Ne vo ket krouet a skeudennig.',
	'tiff_file_too_large' => 'Re vras eo ar restr karget ha distaolet eo bet.',
	'tiff_out_of_service' => "N'eus ket bet gellet tretiñ ar restr pellgarget. Dizimplijadus eo ImageMagick.",
	'tiff_too_much_meta' => "Ar metaroadennoù a implij re a lec'h.",
	'tiff_error_cached' => "N'hall ar restr-mañ bezañ adderaouekaet nemet goude termen ar grubuilh.",
	'tiff_size_error' => 'Ne glot ket ment ar restr meneget gant ment gwir ar restr.',
	'tiff_script_detected' => 'Skriptoù zo er restr karget.',
	'tiff_bad_file' => 'Fazioù zo er restr karget.',
	'tiff-file-info-size' => '(pajenn $5, $1 × $2 piksel, ment ar restr : $3, seurt MIME : $4)',
);

/** German (Deutsch)
 * @author Als-Holder
 * @author Hallo Welt! - Medienwerkstatt GmbH
 */
$messages['de'] = array(
	'tiff-desc' => 'Schnittstelle für die Ansicht von TIFF-Dateien im Bilder-Modus',
	'tiff_no_metadata' => 'Keine Metadaten im TIFF vorhanden.',
	'tiff_page_error' => 'Seitenzahl außerhalb des Dokumentes.',
	'tiff_too_many_embed_files' => 'Die Datei enthält zu viele eingebettete Dateien.',
	'tiff_sourcefile_too_large' => 'Die Quelldatei hat eine zu hohe Auflösung. Es wird kein Thumbnail generiert.',
	'tiff_targetfile_too_large' => 'Die Zieldatei hat eine zu hohe Auflösung. Es wird kein Thumbnail generiert.',
	'tiff_file_too_large' => 'Die hochgeladene Datei ist zu groß und wurde abgewiesen.',
	'tiff_out_of_service' => 'Die hochgeladene Datei konnte nicht verarbeitet werden. ImageMagick ist nicht verfügbar.',
	'tiff_too_much_meta' => 'Die Metadaten benötigen zu viel Speicherplatz.',
	'tiff_error_cached' => 'Diese Datei kann erst nach Ablauf der Caching-Periode neu gerendert werden.',
	'tiff_size_error' => 'Die errechnete Größe der Datei stimmt nicht mit der tatsächlichen überein.',
	'tiff_script_detected' => 'Die hochgeladene Datei enthält Skripte.',
	'tiff_bad_file' => 'Die hochgeladene Datei ist fehlerhaft.',
	'tiff-file-info-size' => '(Seite $5, $1 × $2 Pixel, Dateigröße: $3, MIME-Typ: $4)',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'tiff-desc' => 'Rěd za woglědowanje TIFF-datajow we wobrazowem modusu',
	'tiff_no_metadata' => 'Njedaju se žedne metadaty z TIFF ekstrahěrowaś',
	'tiff_page_error' => 'Bokowa licba njejo we wobcerku',
	'tiff_too_many_embed_files' => 'Wobraz wopśimujo pśewjele zasajźonych datajow.',
	'tiff_sourcefile_too_large' => 'Rozeznaśe žrědłoweje dataje jo pśewjelike. Miniaturny wobraz se njenapórajo.',
	'tiff_targetfile_too_large' => 'Rozeznaśe celoweje dataje jo pśewjelike. Miniaturny wobraz so njenapórajo.',
	'tiff_file_too_large' => 'Nagrata dataja jo pśewjelika a jo se wótpokazała.',
	'tiff_out_of_service' => 'Nagrata dataja njedajo se pśeźěłaś. ImageMagick njestoj k dispoziciji.',
	'tiff_too_much_meta' => 'Metadaty wužywa pśewjele ruma.',
	'tiff_error_cached' => 'Toś ta dataja dajo se akle pó puferowańskem interwalu znowego wuceriś.',
	'tiff_size_error' => 'K wěsći dana datajowa wjelikosć njewótpowědujo wopšawdnej datajowej wjelikosći.',
	'tiff_script_detected' => 'Nagrata dataja wopśimujo skripty.',
	'tiff_bad_file' => 'Nagrata dataja wopśimujo zmólki.',
	'tiff-file-info-size' => '(bok $5, $1 × $2 pikselow, datajowa wjelikosć: $3, typ MIME: $4)',
);

/** Greek (Ελληνικά)
 * @author Dada
 */
$messages['el'] = array(
	'tiff_no_metadata' => 'Αδύνατη η ανάκτηση μεταδεδομένων από το TIFF',
	'tiff_page_error' => 'Αριθμός σελίδας εκτός ορίου',
	'tiff_file_too_large' => 'Το μεταφορτωμένο αρχείο είναι πολύ μεγάλο και απορρίφθηκε.',
);

/** Spanish (Español)
 * @author Pertile
 * @author Translationista
 */
$messages['es'] = array(
	'tiff-desc' => 'Controlador para ver archivos TIFF en modo de imagen',
	'tiff_no_metadata' => 'No se pudo obtener los metadatos de TIFF',
	'tiff_page_error' => 'Número de página fuera de rango',
	'tiff_too_many_embed_files' => 'La imagen contiene demasiados archivos incrustados.',
	'tiff_sourcefile_too_large' => 'La resolución del archivo fuente es muy grande. No se generará miniaturas.',
	'tiff_targetfile_too_large' => 'La resolución del archivo destino es muy grande. No se generará ninguna miniatura.',
	'tiff_file_too_large' => 'El archivo subido es muy grande y ha sido rechazado.',
	'tiff_out_of_service' => 'El archivo subido no pudo ser procesado. ImageMagick no está disponible.',
	'tiff_too_much_meta' => 'Los metadatos utilizan demasiado espacio.',
	'tiff_error_cached' => 'Este archivo sólo puede ser reprocesado tras el intervalo de cacheo.',
	'tiff_size_error' => 'El tamaño del archivo reportado no coincide con el tamaño real del archivo.',
	'tiff_script_detected' => 'El archivo cargado contiene scripts.',
	'tiff_bad_file' => 'El archivo cargado contiene errores.',
	'tiff-file-info-size' => '(Página $5, $1 × $2 píxeles, tamaño de archivo: $3, tipo de MIME: $4)',
);

/** Finnish (Suomi)
 * @author Centerlink
 * @author Crt
 */
$messages['fi'] = array(
	'tiff_no_metadata' => 'Metatietojen hakeminen TIFF-tiedostosta epäonnistui',
	'tiff_too_many_embed_files' => 'Kuvassa on liian monta upotettua tiedostoa.',
	'tiff_file_too_large' => 'Palvelimelle kopioitu tiedosto on liian suuri ja torjuttiin.',
	'tiff_out_of_service' => 'Palvelimelle kopioitua tiedostoa ei voitu käsitellä. ImageMagick ei ollut käytettävissä.',
	'tiff_too_much_meta' => 'Metatiedot vievät liikaa tilaa.',
	'tiff_script_detected' => 'Palvelimelle kopioitu tiedosto sisältää skriptejä.',
	'tiff_bad_file' => 'Palvelimelle kopioitu tiedosto sisältää virheitä.',
);

/** French (Français)
 * @author IAlex
 * @author Jagwar
 * @author Jean-Frédéric
 * @author Urhixidur
 */
$messages['fr'] = array(
	'tiff-desc' => 'Gestionnaire pour visionner les fichiers TIFF en mode image',
	'tiff_no_metadata' => "Impossible d'obtenir les métadonnées depuis TIFF",
	'tiff_page_error' => 'Le numéro de page n’est pas dans la plage.',
	'tiff_too_many_embed_files' => "L'image contient trop de fichiers embarqués.",
	'tiff_sourcefile_too_large' => 'La résolution du fichier source est trop élevée. Aucune miniature ne sera générée.',
	'tiff_targetfile_too_large' => 'La résolution de l’image cible est trop importante. Aucun aperçu ne sera généré.',
	'tiff_file_too_large' => 'Le fichier téléversé est trop grand et a été rejeté.',
	'tiff_out_of_service' => "Le fichier téléversé n'a pas pu être traité. ImageMagick n'est pas disponible.",
	'tiff_too_much_meta' => "Les métadonnées utilisent trop d'espace.",
	'tiff_error_cached' => "Ce fichier ne peut être régénéré qu'après l'expiration du cache.",
	'tiff_size_error' => 'La taille de fichier indiquée ne correspond pas à la taille réelle du fichier.',
	'tiff_script_detected' => 'Le fichier téléchargé contient des scripts.',
	'tiff_bad_file' => 'Le fichier téléchargé contient des erreurs.',
	'tiff-file-info-size' => '(page $5, $1 × $2 pixels, taille du fichier : $3, Type MIME : $4)',
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
	'tiff_targetfile_too_large' => 'A resolución do ficheiro de destino é moi grande. Non se xerará ningunha miniatura.',
	'tiff_file_too_large' => 'O ficheiro cargado é moi grande e foi rexeitado.',
	'tiff_out_of_service' => 'O ficheiro cargado non se puido procesar. ImageMagick non está dispoñible.',
	'tiff_too_much_meta' => 'Os metadatos empregan moito espazo.',
	'tiff_error_cached' => 'O ficheiro só se pode renderizar despois do intervalo da caché.',
	'tiff_size_error' => 'O tamaño do ficheiro do que se informou non se corresponde co tamaño real do ficheiro.',
	'tiff_script_detected' => 'O ficheiro cargado contén escrituras.',
	'tiff_bad_file' => 'O ficheiro cargado contén erros.',
	'tiff-file-info-size' => '(páxina $5, $1 × $2 píxeles, tamaño do ficheiro: $3, tipo MIME: $4)',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'tiff-desc' => 'Funktion zum Aaluege vu TIFF-Dateie im Bildmodus',
	'tiff_no_metadata' => 'Cha d Metadate vum TIFF nit läse',
	'tiff_page_error' => 'Sytenummere lyt nit im Beryych',
	'tiff_too_many_embed_files' => 'Im Bild het s zvil yybundeni Dateie',
	'tiff_sourcefile_too_large' => 'D Uflesig vu dr Quälldatei isch z hoch. S wird kei Vorschaubild generiert.',
	'tiff_targetfile_too_large' => 'D Uflesig vu dr Ziildatei isch z hoch. S wird kei Miniaturbild generiert.',
	'tiff_file_too_large' => 'D Datei, wu uffeglade woren isch, isch z groß un isch abgwise wore.',
	'tiff_out_of_service' => 'D Datei, wu uffeglade woren isch, het nit chenne verarbeitet wäre. ImageMagick isch nit verfiegbar.',
	'tiff_too_much_meta' => 'D Metadate bruch zvil Spycherplatz.',
	'tiff_error_cached' => 'Die Datei cha erscht no Ablauf vu dr Caching-Periode nej grenderet wäre.',
	'tiff_size_error' => 'Di brichtet Greßi vu dr Datei stimmt nit zue dr tatsächlige.',
	'tiff_script_detected' => 'In dr Datei, wu uffeglade woren isch, het s Skript din.',
	'tiff_bad_file' => 'D Datei, wu uffeglade woren isch, isch fählerhaft.',
	'tiff-file-info-size' => '(Syte $5, $1 × $2 Pixel, Dateigreßi: $3, MIME-Typ: $4)',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'tiff-desc' => 'Rozšěrjenje za wobhladowanje TIFF-datajow we wobrazowym modusu',
	'tiff_no_metadata' => 'Z TIFF njedadźa so metadaty wućahać.',
	'tiff_page_error' => 'Čisło strony we wobłuku njeje',
	'tiff_too_many_embed_files' => 'Wobraz wobsahuje přewjele zapřijatych datajow.',
	'tiff_sourcefile_too_large' => 'Rozeznaće žórłoweje dataje je přewulke. Přehladowy wobraz njebudźe so płodźić.',
	'tiff_targetfile_too_large' => 'Rozeznaće ciloweje dataje je přewulke. Přehledowy wobrazk njeje so wutworił.',
	'tiff_file_too_large' => 'Nahrata dataja je přewulka a bu wotpokazana.',
	'tiff_out_of_service' => 'Nahrata dataja njeda so předźěłać. ImageMagick njesteji k dispoziciji.',
	'tiff_too_much_meta' => 'Metadaty wužiwaja přewjele ruma.',
	'tiff_error_cached' => 'Tuta dataja da so hakle po pufrowanskim interwalu znowa rysować.',
	'tiff_size_error' => 'Zdźělena wulkosć dataje njewotpowěduje woprawdźitej wulkosći dataje.',
	'tiff_script_detected' => 'Nahrata dataja wobsahuje skripty.',
	'tiff_bad_file' => 'Nahrata dataja wobsahuje zmylki.',
	'tiff-file-info-size' => '(strona $5, $1 × $2 pikselow, wulkosć dataje: $3, MIME-typ: $4)',
);

/** Hungarian (Magyar)
 * @author Glanthor Reviol
 */
$messages['hu'] = array(
	'tiff_no_metadata' => 'Nem sikerült lekérni a TIFF metaadatait',
	'tiff_page_error' => 'Az oldalszám a tartományon kívül esik',
	'tiff_too_many_embed_files' => 'A kép túl sok beágyazott fájlt tartalmaz.',
	'tiff_targetfile_too_large' => 'A célfájl felbontása túl nagy. Nem fog bélyegkép készülni hozzá.',
	'tiff_file_too_large' => 'A feltöltött fájl túl nagy, vissza lett utasítva.',
	'tiff_too_much_meta' => 'A metaadatok túl sok helyet foglalnak.',
	'tiff_script_detected' => 'A feltöltött fájl parancsfájlokat tartalmaz.',
	'tiff_bad_file' => 'A feltöltött fájl hibákat tartalmaz.',
	'tiff-file-info-size' => '($5 oldal, $1 × $2 képpont, fájlméret: $3, MIME-típus: $4)',
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
	'tiff_targetfile_too_large' => 'Le resolution del file de destination es troppo alte. Nulle miniatura essera generate.',
	'tiff_file_too_large' => 'Le file incargate es troppo grande e ha essite rejectate.',
	'tiff_out_of_service' => 'Le file incargate non poteva esser processate. ImageMagick non es disponibile.',
	'tiff_too_much_meta' => 'Le metadatos usa troppo de spatio.',
	'tiff_error_cached' => 'Iste file pote solmente esser re-rendite post le expiration de su copia in cache.',
	'tiff_size_error' => 'Le grandor reportate del file non corresponde al grandor real del file.',
	'tiff_script_detected' => 'Le file incargate contine scripts.',
	'tiff_bad_file' => 'Le file incargate contine errores.',
	'tiff-file-info-size' => '(pagina $5, $1 × $2 pixel, grandor del file: $3, typo MIME: $4)',
);

/** Japanese (日本語)
 * @author Aotake
 * @author Naohiro19
 * @author 青子守歌
 */
$messages['ja'] = array(
	'tiff-desc' => 'TIFFファイルの画像モードを表示するためのハンドラ',
	'tiff_no_metadata' => 'TIFFからのメタデータが取得できません',
	'tiff_page_error' => '範囲にないページ番号',
	'tiff_too_many_embed_files' => 'この画像には埋め込みファイルが多すぎます。',
	'tiff_sourcefile_too_large' => 'ソースファイルの解像度が大きすぎます。サムネイルは生成されません。',
	'tiff_targetfile_too_large' => 'ターゲットファイルの解像度が大きすぎます。サムネイルは生成されません。',
	'tiff_file_too_large' => 'アップロードされたファイルは容量が大きすぎるために拒否されました。',
	'tiff_out_of_service' => 'アップロードされたファイルを処理できませんでした。ImageMagick が利用できません。',
	'tiff_too_much_meta' => 'メタデータが使用する容量が大きすぎます。',
	'tiff_error_cached' => 'このファイルはキャッシュの有効期限が切れてからでなければレンダリングできません。',
	'tiff_size_error' => '報告されたファイルサイズが実際のサイズと一致しません。',
	'tiff_script_detected' => 'アップロードされたファイルに、スクリプトが含まれます。',
	'tiff_bad_file' => 'アップロードされたファイルに、エラーが含まれます。',
	'tiff-file-info-size' => '(ページ $5、$1 × $2ピクセル、ファイルサイズ:$3、MIME:$4)',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'tiff_page_error' => "D'Nummer vun der Säit ass net am Beräich",
	'tiff_file_too_large' => 'Den eropgeluedene Fichier ass ze grouss a gouf net akzeptéiert.',
	'tiff_out_of_service' => 'Den eropgeluedene Fichier konnt net verschafft ginn. ImageMagick ass net disponibel.',
	'tiff_too_much_meta' => "D'Metadate benotzen zevill Späicherplaz.",
	'tiff_size_error' => "Déi berechent Gréisst vum Fichier ass net d'selwëscht wéi déi wierklech Gréisst vum Fichier.",
	'tiff_script_detected' => 'Am eropgeluedene Fichier si Skripten dran.',
	'tiff_bad_file' => 'Am eropgeluedene Fichier si Feeler.',
	'tiff-file-info-size' => '(Säit $5, $1 × $2 Pixel, Gréisst vum Fichier: $3, MIME-Typ: $4)',
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
	'tiff_targetfile_too_large' => 'Резолуцијата на целната податотека е преголема. Нема да биде направена минијатура.',
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
	'tiff_targetfile_too_large' => 'De resolutie van het doelbestand is te groot.
Er wordt geen miniatuur aangemaakt.',
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

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Nghtwlkr
 */
$messages['no'] = array(
	'tiff-desc' => 'Håndterer for visning av TIFF-filer i bildemodus',
	'tiff_no_metadata' => 'Kan ikke hente metadata fra TIFF',
	'tiff_page_error' => 'Sidenummer er utenfor sideintervallet',
	'tiff_too_many_embed_files' => 'Bildet inneholder for mange innebygde filer.',
	'tiff_sourcefile_too_large' => 'Oppløsningen til kildefilen er for stor. Miniatyrbilde vil ikke bli opprettet.',
	'tiff_targetfile_too_large' => 'Oppløsningen på målfilen er for stor. Inget miniatyrbilde vil bli generert.',
	'tiff_file_too_large' => 'Den opplastede filen var for stor og ble avvist.',
	'tiff_out_of_service' => 'Den opplastede filen kunne ikke behandles. ImageMagick er ikke tilgjengelig.',
	'tiff_too_much_meta' => 'Metadata bruker for mye plass.',
	'tiff_error_cached' => 'Filen kan bare gjengis på nytt etter hurtiglagerintervallet.',
	'tiff_size_error' => 'Den rapporterte filstørrelsen samsvarer ikke med den faktiske filstørrelsen.',
	'tiff_script_detected' => 'Den opplastede filen inneholder skript.',
	'tiff_bad_file' => 'Den opplastede filen inneholder feil.',
	'tiff-file-info-size' => '(side $5, $1 x $2 piksler, filstørrelse: $3, MIME-type: $4)',
);

/** Piedmontese (Piemontèis)
 * @author Borichèt
 * @author Dragonòt
 */
$messages['pms'] = array(
	'tiff-desc' => 'Gestor për vëdde archivi TIFF an manera figure',
	'tiff_no_metadata' => 'As riess nen a pijé ij metadat dal TIFF',
	'tiff_page_error' => "Nùmer ëd pàgina pa ant l'antërval",
	'tiff_too_many_embed_files' => 'La figura a conten andrinta tròpi archivi.',
	'tiff_sourcefile_too_large' => "L'arzolussion dl'archivi sorgiss a l'é tròp gròssa. Gnun-e figurin-e a saran generà.",
	'tiff_targetfile_too_large' => "L'arzolussion ëd l'archivi ëd destinassion a l'é tròp gròssa. Gnun-e figurin-e a saran generà.",
	'tiff_file_too_large' => "L'archivi carià a l'é tròp gròss e a l'é stàit arfudà.",
	'tiff_out_of_service' => "L'archivi carià a l'ha pa podù esse processà. ImageMagick a l'é nen disponìbil.",
	'tiff_too_much_meta' => 'Ij Metadat a deuvro tròp dë spassi.',
	'tiff_error_cached' => "Cost archivi-sì a peul mach esse rendù apress l'antërval ëd memorisassion local.",
	'tiff_size_error' => "La dimension diciairà dl'archivi a l'é pa l'istessa ëd soa dimension vera.",
	'tiff_script_detected' => "L'archivi carià a conten ëd senari.",
	'tiff_bad_file' => "L'archivi carià a conten d'eror.",
	'tiff-file-info-size' => "(pàgina $5, $1 x $2 pontin, dimension dl'archivi: $3, sòrt MIME: $4)",
);

/** Portuguese (Português)
 * @author Hamilton Abreu
 */
$messages['pt'] = array(
	'tiff-desc' => 'Permite o visionamento de ficheiros TIFF em modo de imagem',
	'tiff_no_metadata' => 'Não foi possível extrair metadados do TIFF',
	'tiff_page_error' => 'Número de página fora do intervalo',
	'tiff_too_many_embed_files' => 'A imagem tem demasiados ficheiros embutidos.',
	'tiff_sourcefile_too_large' => "A resolução do ficheiro de origem é demasiado grande. Não será gerada uma miniatura ''(thumbnail)''.",
	'tiff_targetfile_too_large' => "A resolução do ficheiro de destino é demasiado grande. Não será gerada uma miniatura ''(thumbnail)''.",
	'tiff_file_too_large' => 'O ficheiro transferido é demasiado grande e foi rejeitado.',
	'tiff_out_of_service' => 'Não foi possível processar o ficheiro transferido. O ImageMagick não está disponível.',
	'tiff_too_much_meta' => 'Os metadados usam demasiado espaço.',
	'tiff_error_cached' => 'Só será possível voltar a renderizar o ficheiro após o intervalo de caching, porque o erro foi colocado na cache.',
	'tiff_size_error' => 'O tamanho reportado do ficheiro não corresponde ao tamanho real.',
	'tiff_script_detected' => "O ficheiro transferido tem ''scripts''.",
	'tiff_bad_file' => 'O ficheiro transferido tem erros.',
	'tiff-file-info-size' => '(página $5, $1 × $2 pixels, tamanho do ficheiro: $3, tipo MIME: $4)',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Luckas Blade
 */
$messages['pt-br'] = array(
	'tiff_file_too_large' => 'O arquivo carregado é muito grande e foi recusado.',
	'tiff_out_of_service' => 'O arquivo carregado não pôde ser processado. ImageMagick não está disponível.',
	'tiff_bad_file' => 'O arquivo carregado contém erros.',
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
	'tiff_targetfile_too_large' => 'Разрешение целевого файла слишком велико. Миниатюра не будет создана.',
	'tiff_file_too_large' => 'Размер загружаемого файла слишком велик, файл отклонён.',
	'tiff_out_of_service' => 'Загруженный файл не может быть обработан. ImageMagick недоступен.',
	'tiff_too_much_meta' => 'Метаданные занимают слишком много места.',
	'tiff_error_cached' => 'Этот файл может быть повторно перерисован только после кэширующего промежутка.',
	'tiff_size_error' => 'Указанный размер файла не совпадает с фактическим размером файла.',
	'tiff_script_detected' => 'Загруженный файл содержит сценарии.',
	'tiff_bad_file' => 'Загруженный файл содержит ошибки.',
	'tiff-file-info-size' => '(страница $5, $1 × $2 пикселов, размер файла: $3, MIME-тип: $4)',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'tiff-desc' => 'Tagapaghawak para sa pagtanaw ng mga talaksang TIFF na nasa modalidad na panglarawan',
	'tiff_no_metadata' => 'Hindi makuha ang metadata mula sa TIFF',
	'tiff_page_error' => 'Wala sa sakop ang bilang ng pahina',
	'tiff_too_many_embed_files' => 'Naglalaman ang larawan ng napakaraming ibinaong mga talaksan.',
	'tiff_sourcefile_too_large' => 'Napakalaki ng resolusyon ng pinagmulang talaksan.  Walang malilikhang maliit na larawan.',
	'tiff_targetfile_too_large' => 'Napakalaki ng resolusyon ng puntiryang talaksan. Walang malilikhang maliit na larawan.',
	'tiff_file_too_large' => "Napakalaki ng ikinargang-paitaas na talaksan kaya't tinanggihan.",
	'tiff_out_of_service' => 'Hindi maaasikaso ang talaksang ikinargang pataas.  Hindi kasi makuha ang ImageMagick.',
	'tiff_too_much_meta' => 'Gumagamit ng labis na puwang ang metadata.',
	'tiff_error_cached' => 'Maaari lamang muling ibigay ang talaksan pagkatapos ng tagal ng agwat ng pagkukubli.',
	'tiff_size_error' => 'Hindi tumutugma ang inulat na sukat ng talaksan sa talagang sukat ng talaksan.',
	'tiff_script_detected' => 'Naglalaman ng mga baybayin ang ikinargang talaksan.',
	'tiff_bad_file' => 'Naglalaman ng mga kamalian ang ikinargang talaksan.',
	'tiff-file-info-size' => '(pahina $5, $1 × $2 piksel, sukat ng talaksan: $3, uri ng MIME: $4)',
);

/** Yiddish (ייִדיש)
 * @author פוילישער
 */
$messages['yi'] = array(
	'tiff_too_much_meta' => 'מעטאַדאַטן באַניצן צו פֿיל פלאַץ.',
	'tiff-file-info-size' => '(בלטַט $5, $1 × $2 פיקסעל, טעקע גרייס: $3, טיפ MIME: $4)',
);

