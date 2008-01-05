<?php

/**
 * Internationalisation file for ImageMap extension
 */

function efImageMapMessages() {
	$messages = array(

/* English (Tim Starling) */
'en' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: must specify an image in the first line',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: image is invalid or non-existent',
	'imagemap_no_link'              => '&lt;imagemap&gt;: no valid link was found at the end of line $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: invalid title in link at line $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: not enough coordinates for shape at line $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: unrecognised shape at line $1, each line must start with one of: '.
									   'default, rect, circle or poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: at least one area specification must be given',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: invalid coordinate at line $1, must be a number',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: invalid desc specification, must be one of: <tt>$1</tt>',
	'imagemap_description'          => 'About this image',
	# Note to translators: keep the same order
	'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
),

'ar' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: يجب تحديد صورة في الخط الأول',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: الصورة غير صحيحة أو غير موجودة',
	'imagemap_no_link'              => '&lt;imagemap&gt;: لم يتم العثور على وصلة صحيحة في نهاية السطر $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: عنوان غير صحيح في الوصلة في السطر $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: إحداثيات غير كافية للشكل عند الخط $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: شكل غير معروف عند الخط $1, كل خط يجب أن يبدأ بواحد من: default, rect, circle or poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: على الأقل محدد مساحة واحد يجب إعطاؤه',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: إحداثي غير صحيح عند الخط $1، يجب أن يكون رقما',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: محدد وصف غير صحيح، يجب أن يكون واحدا من: <tt>$1</tt>',
	'imagemap_description'          => 'حول هذه الصورة',
),

'bcl' => array(
	'imagemap_description'          => 'Manónongod sa retratong ini',
),

/** Bulgarian (Български)
 * @author Spiritia
 */
'bg' => array(
	'imagemap_no_image'           => '&lt;imagemap&gt;: трябва да се укаже изображение на първия ред',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: невалидно или липсващо изображение',
	'imagemap_no_link'            => '&lt;imagemap&gt;: липсва валидна препратка в края на ред $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: невалидно заглавие в препратка на ред $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: недостатъчно координати за фигура на ред $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: неразпозната фигура на ред $1; всеки ред трябва да са започва с някое от следните: default (по подразбиране), rect (правоъгълник), circle (кръг) или poly (многоъгълник)',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: невалидна координата на ред $1, трябва да бъде число',
	'imagemap_description'        => 'Информация за изображението',
),

/** Czech (Česky)
 * @author Li-sung
 */
'cs' => array(
	'imagemap_no_image'           => '&lt;imagemap&gt;: na první řádce musí být určen obrázek',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: soubor není platný nebo neexistuje',
	'imagemap_no_link'            => '&lt;imagemap&gt;: nebyl nalezen žádný platný odkaz na konci řádku $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: neplatný název v odkazu na řádku $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: chybějící souřadnice tvaru na řádku $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: nerozpoznaný tvar na řádku $1, každá řádka musí začínat definicí tvaru: default, rect, circle nebo poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: musí být určena alespoň jedna oblast',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: neplatné souřadnice na řádku $1, je očekáváno číslo',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: neplatné určení oblasti desc, je očekávána jedna z možností: <tt>$1</tt>',
	'imagemap_description'        => 'O tomto obrázku',
),

/* Danish (Wegge) */
'da' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: Der skal angives et billednavn i første linje',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: Billedet er ugyldigt eller findes ikke',
	'imagemap_no_link'              => '&lt;imagemap&gt;: Fandt ikke en brugbar henvisning i slutningen af linje $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: Ugyldig titel i henvisning på linje $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: Utilstrækkeligt antal koordinater til omridset i linje $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: Ukendt omridstype i linje $1. Alle linjer skal starte med en af:'.
								   'default, rect, circle or poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: Der skal angives omrids af mindst et område',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: Ugyldig koordinat på linje $1, koordinater skal være tal',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: Ugyldig specifikation af desc, skal være en af: <tt>$1</tt>',
	'imagemap_description'          => 'Om dette billede',
	# Note to translators: keep the same order
	'imagemap_desc_types'           => 'top-højre, bund-højre, bund-venstre, top-venstre, ingen',
),

/* German (Raymond) */
'de' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;-Fehler: In der ersten Zeile muss ein Bild angegeben werden',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;-Fehler: Bild ist ungültig oder nicht vorhanden',
	'imagemap_no_link'              => '&lt;imagemap&gt;-Fehler: Am Ende von Zeile $1 wurde kein gültiger Link gefunden',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;-Fehler: ungültiger Titel im Link in Zeile $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;-Fehler: Zu wenige Koordinaten in Zeile $1 für den Umriss',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;-Fehler: Unbekannte Umrissform in Zeile $1. Jede Zeile muss mit einem dieser Parameter beginnen: '.
								   '<tt>default, rect, circle</tt> oder <tt>poly</tt>',
	'imagemap_no_areas'             => '&lt;imagemap&gt;-Fehler: Es muss mindestens ein Gebiet definiert werden',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;-Fehler: Ungültige Koordinate in Zeile $1: es sind nur Zahlen erlaubt',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;-Fehler: Ungültiger „desc“-Parameter, möglich sind: <tt>$1</tt>',
	'imagemap_description'          => 'Über dieses Bild',
	# Note to translators: keep the same order
	'imagemap_desc_types'           => 'oben rechts, unten rechts, unten links, oben links, keine',
),

'el' => array(
	'imagemap_description'          => 'Σχετικά με αυτήν την εικόνα',
),

'ext' => array(
	'imagemap_description'          => 'Al tentu esta imahin',
),

/* French */
'fr' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt; : vous devez spécifier une image dans la première ligne',
	'imagemap_invalid_image'        => '&lt;imagemap&gt; : l’image est invalide ou n’existe pas',
	'imagemap_no_link'              => '&lt;imagemap&gt; : aucun lien valide n’a été trouvé à la fin de la ligne $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt; : titre invalide dans le lien à la ligne  $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt; : pas assez de coordonnées pour la forme à la ligne  $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt; : forme non reconnue à la ligne $1, chaque ligne doit commencer avec un des mots suivants : '.
								   'default, rect, circle or poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt; : au moins une spécification d’aire doit être donnée',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt; : coordonnée invalide à la ligne $1, doit être un nombre',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt; : paramètre « desc » invalide, les paramètres possibles sont : $1',
	'imagemap_description'          => 'À propos de cette image',
),

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
'frp' => array(
	'imagemap_no_image'           => '&lt;imagemap&gt; : vos dête spècefiar una émâge dens la premiére legne',
	'imagemap_invalid_image'      => '&lt;imagemap&gt; : l’émâge est envalida ou ègziste pas',
	'imagemap_no_link'            => '&lt;imagemap&gt; : nion lim valido at étâ trovâ a la fin de la legne $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt; : titro envalido dens lo lim a la legne $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt; : pas prod de coordonâs por la fôrma a la legne $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt; : fôrma pas recognua a la legne $1, châque legne dêt comenciér avouéc yon des mots siuvents : default, rect, circle ou ben poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt; : u muens yona spèceficacion de surface dêt étre balyê',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt; : coordonâ envalida a la legne $1, dêt étre un nombro',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt; : paramètre « dèsc » envalido, los paramètres possiblos sont : <tt>$1</tt>',
	'imagemap_description'        => 'A propôs de ceta émâge',
),

/** Galician (Galego)
 * @author Alma
 * @author Xosé
 */
'gl' => array(
	'imagemap_no_image'           => '&lt;imagemap&gt;: debe especificar unha imaxe na primeira liña',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: a imaxe non é válida ou non existe',
	'imagemap_no_link'            => '&lt;imagemap&gt;: foi atopada unha ligazón non válida ao final da liña $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: título non válido na ligazón na liña $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: non abondan as coordenadas para crear un polígono, na liña $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: descoñecida forma na liña $1, cada liña debe comezar con un dos seguintes: por defecto, rectángulo, círculo ou polígono',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: polo menos debe darse unha zona de especificación',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: coordinada non válida na liña $1, debe ser un número',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: especificación desc non válida, debe ser un de: <tt>$1</tt>',
	'imagemap_description'        => 'Sobre esta imaxe',
),

/** Gujarati (ગુજરાતી) */
'gu' => array(
	'imagemap_description' => 'આ ચિત્ર વિષે',
),

/* Hebrew (Rotem Liss) */
'he' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: יש לציין תמונה בשורה הראשונה',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: התמונה שגויה או שאינה קיימת',
	'imagemap_no_link'              => '&lt;imagemap&gt;: לא נמצא קישור תקף בסוף שורה $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: כותרת שגויה בקישור בשורה $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: לא מספיק קוארדינאטות לצורה בשורה $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: צורה בלתי מזוהה בשורה $1, כל שורה חייבת להתחיל עם אחת האפשרויות הבאות: '.
								   'default, rect, circle or poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: יש לציין לפחות אזור אחד',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: קוארדינאטה שגויה בשורה $1, היא חייבת להיות מספר',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: הגדרת פרמטר desc שגויה, צריך להיות אחד מהבאים: $1',
	'imagemap_description'          => 'אודות התמונה',
),

'hr' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: morate navesti ime slike koju rabite u prvom retku',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: slika ne postoji ili je krivog tipa',
	'imagemap_no_link'              => '&lt;imagemap&gt;: nema (ispravne) poveznice na kraju retka $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: loš naziv u poveznici u retku $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: nedovoljan broj koordinata za oblik u retku $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: oblik u retku $1 nije prepoznat, svaki redak mora početi s jednim od oblika: default, rect, circle ili poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: najmanje jedna specifikacija područja mora biti zadana',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: nevaljane koordinate u retku $1, mora biti broj',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: nevaljan opis, mora biti jedan od: <tt>$1</tt>',
	'imagemap_description'          => 'Ovo je slika/karta s poveznicama (\'\'imagemap\'\')',
),

'hsb' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt; zmylk: Dyrbiš w prěnjej lince wobraz podać',
	'imagemap_invalid_image'        => '&lt;imagemap&gt; zmylk: Wobraz je njepłaćiwy abo njeeksistuje',
	'imagemap_no_link'              => '&lt;imagemap&gt; zmylk: Na kóncu linki $1 njebu płaćiwy wotkaz namakany',
	'imagemap_invalid_title'        => '&lt;imagemap&gt; zmylk: njepłaćiwy titul we wotkazu w lince $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt; zmylk: Přemało koordinatow w lince $1 za podobu',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt; zmylk: Njeznata podoba w lince $1, kóžda linka dyrbi so z jednym z tutych parametrow započeć: <tt>default, rect, circle</tt> abo <tt>poly</tt>',
	'imagemap_no_areas'             => '&lt;imagemap&gt; zmylk: Dyrbi so znajmjeńša přestrjeń definować.',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt; zmylk: njepłaćiwa koordinata w lince $1: su jenož ličby dowolene',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt; zmylk: Njepłaćiwy parameter "desc", móžne su: <tt>$1</tt>',
	'imagemap_description'          => 'Wo tutym wobrazu',
),

/** Hungarian (Magyar)
 * @author KossuthRad
 * @author Bdanee
 */
'hu' => array(
	'imagemap_no_image'           => '&lt;imagemap&gt;: kell egy előírt kép az első sorban',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: érvénytelen vagy nem létező kép',
	'imagemap_no_link'            => '&lt;imagemap&gt;: nincs érvényes link a(z) $1. sor végén',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: érvénytelen cím a linkben a $1 vonalban',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: nincs elég koordináta az alakításhoz a $1 sorban',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: ismeretlen alakzat a(z) $1. sorban, mindegyiknek ezek valamelyikével kell kezdődnie: default, rect, circle vagy poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: Legalább egy terület előírást hozzá kell adni',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: érvénytelen koordináta a $1 vonalban, számnak kell lennie',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: hibás desc leírás, ezek egyike kell: <tt>$1</tt>',
	'imagemap_description'        => 'Kép leírása',
),

/* Indonesian (Ivan Lanin) */
'id' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: harus memberikan suatu gambar di baris pertama',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: gambar tidak sah atau tidak ditemukan',
	'imagemap_no_link'              => '&lt;imagemap&gt;: tidak ditemukan pranala yang sah di akhir baris ke $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: judul tidak sah pada pranala di baris ke $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: tidak cukup koordinat untuk bentuk pada baris ke $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: bentuk tak dikenali pada baris ke $1, tiap baris harus dimulai dengan salah satu dari: '.
								   'default, rect, circle atau poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: harus diberikan paling tidak satu spesifikasi area',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: koordinat tidak sah pada baris ke $1, haruslah berupa angka',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: spesifikasi desc tidak sah, harus salah satu dari: $1',
	'imagemap_description'          => 'Tentang gambar ini',
),

/** Icelandic (Íslenska)
 * @author SPQRobin
 */
'is' => array(
	'imagemap_description' => 'Um þessa mynd',
),

/* Italian (AnyFile, fixed typos by BrokenArrow) */
'it' => array(
	'imagemap_no_image'             => "&lt;imagemap&gt;: si deve specificare un'immagine nella prima riga",
	'imagemap_invalid_image'        => "&lt;imagemap&gt;: l'immagine non è valida o non esiste",
	'imagemap_no_link'              => '&lt;imagemap&gt;: non è stato trovato alcun collegamento valido alla fine della riga $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: titolo del collegamento non valido nella riga $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: non ci sono abbastanza coordinate per la forma specificata nella riga $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: Forma (shape) non riconosciuta nella riga $1, ogni riga deve iniziare con uno dei seguenti: default, rect, circle o poly',
	'imagemap_no_areas'             => "&lt;imagemap&gt;: deve essere specificata almeno un'area",
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: coordinata non valida nella riga $1, deve essere un numero',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: Valore non valido per il parametro desc, deve essere uno dei seguenti: $1',
	'imagemap_description'          => 'Informazioni sull\'immagine',
),

/* Kazakh Cyrillic (AlefZet) */
'kk-cyrl' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: бірінші жолда суретті көрсету қажет',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: сурет жарамсыз немесе жоқ',
	'imagemap_no_link'              => '&lt;imagemap&gt;: $1 жол аяғында жарамды сілтеме табылмады',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: $1 жол аяғындағы сілтемеде жарамсыз атау',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: $1 жолдағы кескін үшін координаттар жетіксіз',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: $1 жолдағы кескін жарамсыз, әрбір жол мынаның біреуінен басталу қажет: ',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: ең кемінде бір аумақ маманданымы берілу қажет',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: $1 жолында жарамсыз координата, сан болуы қажет',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: жарамсыз сипаттама маманданымы, мынаның біреуі болуы қажет: $1',
	'imagemap_description'          => 'Бұл сурет туралы',
),

/* Kazakh Latin (AlefZet) */
'kk-latn' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: birinşi jolda swretti körsetw qajet',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: swret jaramsız nemese joq',
	'imagemap_no_link'              => '&lt;imagemap&gt;: $1 jol ayağında jaramdı silteme tabılmadı',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: $1 jol ayağındağı siltemede jaramsız ataw',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: $1 joldağı keskin üşin koordïnattar jetiksiz',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: $1 joldağı keskin jaramsız, ärbir jol mınanıñ birewinen bastalw qajet: ',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: eñ keminde bir awmaq mamandanımı berilw qajet',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: $1 jolında jaramsız koordïnata, san bolwı qajet',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: jaramsız sïpattama mamandanımı, mınanıñ birewi bolwı qajet: $1',
	'imagemap_description'          => 'Bul swret twralı',
),

/* Kazakh Arabic (AlefZet) */
'kk-arab' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: بٸرٸنشٸ جولدا سۋرەتتٸ كٶرسەتۋ قاجەت',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: سۋرەت جارامسىز نەمەسە جوق',
	'imagemap_no_link'              => '&lt;imagemap&gt;: $1 جول اياعىندا جارامدى سٸلتەمە تابىلمادى',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: $1 جول اياعىنداعى سٸلتەمەدە جارامسىز اتاۋ',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: $1 جولداعى كەسكٸن ٷشٸن كوورديناتتار جەتٸكسٸز',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: $1 جولداعى كەسكٸن جارامسىز, ٵربٸر جول مىنانىڭ بٸرەۋٸنەن باستالۋ قاجەت: ',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: ەڭ كەمٸندە بٸر اۋماق ماماندانىمى بەرٸلۋ قاجەت',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: $1 جولىندا جارامسىز كوورديناتا, سان بولۋى قاجەت',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: جارامسىز سيپاتتاما ماماندانىمى, مىنانىڭ بٸرەۋٸ بولۋى قاجەت: $1',
	'imagemap_description'          => 'بۇل سۋرەت تۋرالى',
),

'la' => array(
	'imagemap_description'          => 'De hac imagine',
),

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
'lb' => array(
	'imagemap_description' => 'Iwwert dëst Bild',
),

'nds' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: in de eerste Reeg mutt en Bild angeven wesen',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: Bild geiht nich oder dat gifft dat gornich',
	'imagemap_no_link'              => '&lt;imagemap&gt;: an dat Enn vun Reeg $1 weer keen Lenk',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: in Reeg $1 is de Titel in’n Lenk nich bi de Reeg',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: Form in Reeg $1 hett nich noog Koordinaten',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: Form in Reeg $1 nich kennt, jede Reeg mutt mit \'\'default\'\', \'\'rect\'\', \'\'circle\'\' oder \'\'poly\'\' anfangen',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: opminnst een Areal mutt angeven wesen',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: Koordinaat in Reeg $1 nich bi de Reeg, mutt en Tall wesen',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: Beschrieven nich bi de Reeg, mutt een vun disse wesen: <tt>$1</tt>',
	'imagemap_description'          => 'Över dit Bild',
),

/* nld / Dutch (Siebrand Mazeland) */
'nl' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: geef een afbeelding op in de eerste regel',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: de afbeelding is corrupt of bestaat niet',
	'imagemap_no_link'              => '&lt;imagemap&gt;: er is geen geldige link aangetroffen aan het einde van regel $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: er staat een ongeldige titel in de verwijzing op regel $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: niet genoeg coördinaten voor vorm in regel $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: niet herkende vorm in regel $1, iedere regel moet beginnen met één van de commando\'s: default, rect, circle of poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: er moet tenminste één gebied gespecificeerd worden',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: ongeldige coördinaten in regel $1, moet een getal zijn',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: ongeldige beschrijvingsspecificatie, dit moet er één zijn uit de volgende lijst: $1',
	'imagemap_description'          => 'Over deze afbeelding',
	# Note to translators: keep the same order
	'imagemap_desc_types'           => 'rechtsboven, rechtsonder, linksonder, linksboven, geen',
),

/* Norwegian (Jon Harald Søby) */
'no' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: må angi et bilde i første linje',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: bilde er ugyldig eller ikke-eksisterende',
	'imagemap_no_link'              => '&lt;imagemap&gt;: ingen gyldig lenke ble funnet i slutten av linje $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: ugyldig tittel i lenke på linje $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: ikke nok koordinater for form på linje $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: ugjenkjennelig form på linje $1; hver linje må starte med enten: default, rect, circle eller poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: minst en områdespesifikasjon må gis',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: ugyldig koordinat i slutten av linje $1, må være et tall',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: ugyldig desc-spesifisering, må være enten: <tt>$1</tt>',
	'imagemap_description'          => 'Om dette bildet',
),

/* Occitan (Cedric31) */
'oc' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt; : devètz especificar un imatge dins la primièra linha',
	'imagemap_invalid_image'        => '&lt;imagemap&gt; : l’imatge es invalid o existís pas',
	'imagemap_no_link'              => '&lt;imagemap&gt; : cap de ligam valid es pas estat trobat a la fin de la linha $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt; : títol invalid dins lo ligam a la linha $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt; : pas pro de coordenadas per la forma a la linha $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt; : forma pas reconeguda a la linha $1, cada linha deu començar amb un dels mots seguents : default, rect, circle o poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt; : al mens una especificacion d’aira deu èsser balhada',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt; : coordenada invalida a la linha $1, deu èsser un nombre',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt; : paramètre « desc » invalid, los paramètres possibles son : $1',
	'imagemap_description'          => 'A prepaus d\'aqueste imatge',
),

'pl' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: należy wpisać grafikę w pierwszej linii',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: grafika jest niepoprawna lub nie istnieje',
	'imagemap_no_link'              => '&lt;imagemap&gt;: nie znaleziono poprawnego linku na końcu linii $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: niepoprawny tytuł linku w linii $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: niewystarczająca liczba współrzędnych dla kształtu zdefiniowanego w linii $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: nierozpoznany kształt w linii $1; każda linia musi zawierać tekst: default, rect, circle lub poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: należy podać przynajmniej jedną specyfikację pola',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: nieprawidłowa współrzędna w linii $1; należy podać liczbę',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: nieprawidłowa specyfikacja opisu; należy wpisać jeden z wariantów: <tt>$1</tt>',
	'imagemap_description'          => 'Informacje o tej grafice',
),

/* Piedmontese (Bèrto 'd Sèra) */
'pms' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: ant la prima riga a venta ch\'a-i sia la specìfica ëd na figura',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: la figura ò ch\'a l\'ha chèich-còs ch\'a va nen, ò ch\'a-i é nen d\'autut',
	'imagemap_no_link'              => '&lt;imagemap&gt;: pa gnun-a anliura bon-a a la fin dla riga $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: tìtol nen bon ant l\'anliura dla riga $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: pa basta ëd coordinà për fé na forma a la riga $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: forma nen arconossùa a la riga $1, minca riga a la dev anandiesse con un ëd: default, rect, circle ò poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: almanch n\'area a venta ch\'a sia specificà',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: coordinà nen bon-a a la riga $1, a l\'ha da esse un nùmer',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: specìfica dla descrission nen bon-a, a l\'ha da esse un-a ëd coste-sì: <tt>$1</tt>',
	'imagemap_description'          => 'Rësgoard a sta figura-sì',
),

/* Portuguese (Lugusto) */
'pt' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: é necessário especificar uma imagem na primeira linha',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: imagem inválida ou inexistente',
	'imagemap_no_link'              => '&lt;imagemap&gt;: não foi encontrado um link válido ao final da linha $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: título inválido no link da linha $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: coordenadas insuficientes para formar uma figura na linha $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: figura não reconhecida na linha $1. Cada linha precisa iniciar com: '.
								   'default, rect, circle ou poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: é necessário fornecer ao menos uma especificação de área',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: coordenada inválida na linha $1. 0 necessário que seja um número',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: especificação desc inválida. 0 necessário que seja uma dentre: <tt>$1</tt>',
	'imagemap_description'          => 'Sobre esta imagem',
),

/** Russian (Русский)
 * @author .:Ajvol:.
 */
'ru' => array(
	'imagemap_no_image'           => '&lt;imagemap&gt;: в первой строке должно быть задано изображение',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: неверное или отсутствующее изображение',
	'imagemap_no_link'            => '&lt;imagemap&gt;: неверная ссылка в конце строки $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: неверный заголовок ссылки в строке $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: недостаточно координат для фигуры в строке $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: неопознанная фигура в строке $1, каждая строка должна начинаться одним из ключевых слов: default, rect, circle или poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: должна быть указана хотя бы одна область',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: ошибочная координата в строке $1, ожидается число',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: ошибочное значение desc, ожидается одно из следующих значений: <tt>$1</tt>',
	'imagemap_description'        => 'Описание изображения',
),

/* Slovak (helix84) */
'sk' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: musí mať na prvom riadku uvedený obrázok',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: obrázok je neplatný alebo neexistuje',
	'imagemap_no_link'              => '&lt;imagemap&gt;: na konci riadka $1 nebol nájdený platný odkaz',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: neplatný nadpis v odkaze na riadku $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: nedostatok súradníc na vytvorenie tvaru na riadku $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: nerozpoznaný tvar na riadku $1, každý riadok musí začínať jedným z: '.
								   'default, rect, circle alebo poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: musí byť zadaná najmenej jedna špecifikácia oblasti',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: neplatná súradnica na riadku $1, musí to byť číslo',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: neplatný popis, musí byť jedno z nasledovných: $1',
	'imagemap_description'          => 'O tomto obrázku',
),

/** ћирилица (ћирилица)
 * @author Sasa Stefanovic
 */
'sr-ec' => array(
	'imagemap_description' => 'О овој слици',
),

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
'stq' => array(
	'imagemap_no_image' => '&lt;imagemap&gt;-Failer: In ju eerste Riege mout ne Bielde ounroat wäide',
),

/* Swedish */
'sv' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: en bild måste anges på första raden',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: bilden är ogiltig eller existerar inte',
	'imagemap_no_link'              => '&lt;imagemap&gt;: ingen giltig länk fanns i slutet av rad $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: felaktig titel i länken på rad $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: koordinater saknas för området på rad $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: okänd områdesform på rad $1, varje rad måste börja med något av följande: '.
								   '<tt>default, rect, circle, poly</tt>',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: minst ett område måste specificeras',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: ogiltig koordinat på rad $1, koordinater måste vara tal',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: ogiltig specifikation av desc, den måste var en av följande: <tt>$1</tt>',
	'imagemap_description'          => 'Bildinformation',
	# Note to translators: keep the same order
	'imagemap_desc_types'           => 'uppe till höger, nere till höger, nere till vänster, uppe till vänster, ingen',
),

'tet' => array(
	'imagemap_description'          => 'Kona-ba imajen ne\'e',
),

/** Volapük (Volapük)
 * @author Malafaya
 */
'vo' => array(
	'imagemap_description' => 'Tefü magod at',
),

/* Cantonese (Shinjiman) */
'yue' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: 一定要響第一行指定一幅圖像',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: 圖像唔正確或者唔存在',
	'imagemap_no_link'              => '&lt;imagemap&gt;: 響第$1行結尾度搵唔到一個正式嘅連結',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: 響第$1行度嘅標題連結唔正確',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: 響第$1行度未有足夠嘅座標組成一個形狀',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: 響第$1行度有未能認出嘅形狀，每一行一定要用以下其中一樣開始: '.
								   'default, rect, circle 或者係 poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: 最少要畀出一個指定嘅空間',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: 響第$1行度有唔正確嘅座標，佢一定係一個數字',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: 唔正確嘅 desc 參數，一定係要以下嘅其中之一: $1',
	'imagemap_description'          => '關於呢幅圖像',
	'imagemap_desc_types'           => '右上, 右下, 左下, 左上, 無',
),

/* Chinese (Simplified) (Shinjiman) */
'zh-hans' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: 必须要在第一行指定一幅图像',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: 图像不正确或者不存在',
	'imagemap_no_link'              => '&lt;imagemap&gt;: 在第$1行结尾中找不到一个正式的链接',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: 在第$1行中的标题链接不正确',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: 在第$1行中未有足够的座标组成一个形状',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: 在第$1行中有未能认出的形状，每一行必须使用以下其中一组字开始: '.
								   'default, rect, circle 或者是 poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: 最少要给出一个指定的空间',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: 在第$1行中有不正确的座标，它必须是一个数字',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: 不正确的 desc 参数，必须是以下的其中之一: $1',
	'imagemap_description'          => '关于这幅图像',
	'imagemap_desc_types'           => '右上, 右下, 左下, 左上, 无',
),

/* Chinese (Traditional) (Shinjiman) */
'zh-hant' => array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: 必須要在第一行指定一幅圖像',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: 圖像不正確或者不存在',
	'imagemap_no_link'              => '&lt;imagemap&gt;: 在第$1行結尾中找不到一個正式的連結',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: 在第$1行中的標題連結不正確',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: 在第$1行中未有足夠的座標組成一個形狀',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: 在第$1行中有未能認出的形狀，每一行必須使用以下其中一組字開始: '.
								   'default, rect, circle 或者是 poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: 最少要給出一個指定的空間',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: 在第$1行中有不正確的座標，它必須是一個數字',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: 不正確的 desc 參數，必須是以下的其中之一: $1',
	'imagemap_description'          => '關於這幅圖像',
	'imagemap_desc_types'           => '右上, 右下, 左下, 左上, 無',
),

	);

	/* Kazakh fallbacks */
	$messages['kk-kz'] = $messages['kk-cyrl'];
	$messages['kk-tr'] = $messages['kk-latn'];
	$messages['kk-cn'] = $messages['kk-arab'];
	$messages['kk'] = $messages['kk-cyrl'];
	/* Chinese defaults, fallback to zh-hans or zh-hant */
	$messages['zh'] = $messages['zh-hans'];
	$messages['zh-cn'] = $messages['zh-hans'];
	$messages['zh-hk'] = $messages['zh-hant'];
	$messages['zh-tw'] = $messages['zh-hans'];
	$messages['zh-sg'] = $messages['zh-hant'];
	/* Cantonese default, fallback to yue */
	$messages['zh-yue'] = $messages['yue'];

	return $messages;
}
