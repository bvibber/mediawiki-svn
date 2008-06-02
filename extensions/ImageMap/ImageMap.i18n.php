<?php
/**
 * Internationalisation file for extension FindSpam.
 *
 * @addtogroup Extensions
*/

$messages = array();

/** English
 * @author Tim Starling
 */
$messages['en'] = array(
	'imagemap_desc'                 => 'Allows client-side clickable image maps using <tt><nowiki><imagemap></nowiki></tt> tag',
	'imagemap_no_image'             => '&lt;imagemap&gt;: must specify an image in the first line',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: image is invalid or non-existent',
	'imagemap_no_link'              => '&lt;imagemap&gt;: no valid link was found at the end of line $1',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: invalid title in link at line $1',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: not enough coordinates for shape at line $1',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: unrecognised shape at line $1, each line must start with one of: default, rect, circle or poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: at least one area specification must be given',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: invalid coordinate at line $1, must be a number',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: invalid desc specification, must be one of: <tt>$1</tt>',
	'imagemap_description'          => 'About this image',
	# Note to translators: keep the same order
	'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
);

/** Afrikaans (Afrikaans)
 * @author SPQRobin
 */
$messages['af'] = array(
	'imagemap_description' => 'Beeldinligting',
);

/** Aragonese (Aragonés)
 * @author Juanpabl
 */
$messages['an'] = array(
	'imagemap_desc'               => "Premite mapas d'imachens punchables en o client fendo serbir a etiqueta <tt><nowiki><imagemap></nowiki></tt>",
	'imagemap_no_image'           => "&lt;imagemap&gt;: ha d'endicar una imachen a primer ringlera",
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: a imachen no ye conforme u no esiste',
	'imagemap_no_link'            => "&lt;imagemap&gt;: no s'ha trobato garra binclo conforme á la fin d'a ringlera $1",
	'imagemap_invalid_title'      => "&lt;imagemap&gt;: títol no conforme en o binclo d'a ringlera $1",
	'imagemap_missing_coord'      => "&lt;imagemap&gt;: No bi'n ha prous de coordinadas ta definir a forma en a ringlera $1",
	'imagemap_unrecognised_shape' => "&lt;imagemap&gt;: no s'ha reconoixito a forma en a ringlera $1, cada linia ha de prenzipiar con una d'as siguients espresions: default, rect, circle u poly",
	'imagemap_no_areas'           => "&lt;imagemap&gt;: s'ha d'endicar á o menos una espezificazión d'aria",
	'imagemap_invalid_coord'      => "&lt;imagemap&gt;: coordinada no conforme en a ringlera $1, ha d'estar un numero",
	'imagemap_invalid_desc'       => "&lt;imagemap&gt;: A descripzión (desc) espezificata no ye conforme, ha d'estar una de: <tt>$1</tt>",
	'imagemap_description'        => 'Informazión sobre ista imachen',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'imagemap_desc'               => 'يسمح بخرائط قابلة للضغط عليها من طرف العميل باستخدام وسم <tt><nowiki><imagemap></nowiki></tt>',
	'imagemap_no_image'           => '&lt;imagemap&gt;: يجب تحديد صورة في الخط الأول',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: الصورة غير صحيحة أو غير موجودة',
	'imagemap_no_link'            => '&lt;imagemap&gt;: لم يتم العثور على وصلة صحيحة في نهاية السطر $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: عنوان غير صحيح في الوصلة في السطر $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: إحداثيات غير كافية للشكل عند الخط $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: شكل غير معروف عند الخط $1, كل خط يجب أن يبدأ بواحد من: default, rect, circle or poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: على الأقل محدد مساحة واحد يجب إعطاؤه',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: إحداثي غير صحيح عند الخط $1، يجب أن يكون رقما',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: محدد وصف غير صحيح، يجب أن يكون واحدا من: <tt>$1</tt>',
	'imagemap_description'        => 'حول هذه الصورة',
);

/** Asturian (Asturianu)
 * @author Esbardu
 */
$messages['ast'] = array(
	'imagemap_no_image'           => '&lt;imagemap&gt;: ha especificase una imaxe na primer llinia',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: la imaxe nun ye válida o nun esiste',
	'imagemap_no_link'            => '&lt;imagemap&gt;: atopóse un enllaz non válidu a lo cabero la llinia $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: títulu non válidu nel enllaz de la llinia $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: nun hai abondes coordenaes pa formar la figura de la llinia $1',
	'imagemap_unrecognised_shape' => "&lt;imagemap&gt;: figura non reconocida en llinia $1, cada llinia ha empecipiar con dalguna d'estes: default, rect, circle o poly",
	'imagemap_no_areas'           => "&lt;imagemap&gt;: ha conseñase a lo menos una especificación d'área",
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: coordenada non válida en llinia $1, ha ser un númberu',
	'imagemap_invalid_desc'       => "&lt;imagemap&gt;: parámetru 'desc' non válidu, ha ser ún d'estos: <tt>$1</tt>",
	'imagemap_description'        => 'Tocante a esta imaxe',
);

$messages['bcl'] = array(
	'imagemap_description'          => 'Manónongod sa retratong ini',
);

/** Bulgarian (Български)
 * @author Spiritia
 */
$messages['bg'] = array(
	'imagemap_no_image'           => '&lt;imagemap&gt;: трябва да се укаже изображение на първия ред',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: невалидно или липсващо изображение',
	'imagemap_no_link'            => '&lt;imagemap&gt;: липсва валидна препратка в края на ред $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: невалидно заглавие в препратка на ред $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: недостатъчно координати за фигура на ред $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: неразпозната фигура на ред $1; всеки ред трябва да са започва с някое от следните: default (по подразбиране), rect (правоъгълник), circle (кръг) или poly (многоъгълник)',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: невалидна координата на ред $1, трябва да бъде число',
	'imagemap_description'        => 'Информация за изображението',
);

/** Bengali (বাংলা)
 * @author Bellayet
 */
$messages['bn'] = array(
	'imagemap_description' => 'এই চিত্র সম্পর্কে',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'imagemap_desc'          => "Aotren a ra ar c'hartennoù skeudennoù arval klikadus, a-drugarez d'ar valizenn <tt><nowiki><imagemap></nowiki></tt>",
	'imagemap_no_image'      => '&lt;imagemap&gt;: rankout a rit spisaat ur skeudenn el linenn gentañ',
	'imagemap_invalid_image' => "&lt;imagemap&gt; : direizh eo ar skeudenn pe n'eus ket anezhi",
	'imagemap_no_link'       => "&lt;imagemap&gt;: n'eus bet kavet liamm reizh ebet e dibenn al linenn $1",
	'imagemap_invalid_title' => '&lt;imagemap&gt;: titl direizh el liamm el linenn $1',
	'imagemap_missing_coord' => '&lt;imagemap&gt;: diouer a zaveennoù zo evit stumm al linenn $1',
	'imagemap_description'   => 'Diwar-benn ar skeudenn-mañ',
);

/** Catalan (Català)
 * @author Paucabot
 * @author Toniher
 */
$messages['ca'] = array(
	'imagemap_desc'               => "Permet mapes d'imatges clicables des del costat del client fent servir l'etiqueta <tt><nowiki><imagemap></nowiki></tt>",
	'imagemap_no_image'           => '&lt;imagemap&gt;: cal especificar una imatge en la primera línia',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: la imatge no es vàlida o no existeix',
	'imagemap_no_link'            => "&lt;imagemap&gt;: no s'ha trobat cap enllaç vàlid al final de la línia $1",
	'imagemap_invalid_title'      => "&lt;imagemap&gt;: el títol no és vàlid a l'enllaç de la línia $1",
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: no hi ha coordenades suficients per a la forma de la línia $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: la forma de la línia $1 no és reconeixible, cada línia ha de començar amb una de les opcions següents: default, rect, circle or poly',
	'imagemap_no_areas'           => "&lt;imagemap&gt;: s'ha d'especificar com a mínim una àrea",
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: la coordenada a la línia $1 no és vàlida, ha de ser un nombre',
	'imagemap_invalid_desc'       => "&lt;imagemap&gt;: l'especificació de descripció no és vàlida, ha de ser una de: <tt>$1</tt>",
	'imagemap_description'        => 'Quant a la imatge',
);

/** Czech (Česky)
 * @author Li-sung
 */
$messages['cs'] = array(
	'imagemap_desc'               => 'Umožňuje vytvoření klikací mapy obrázku na straně klienta pomocí značky <tt><nowiki><imagemap></nowiki></tt>',
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
);

/* Danish (Wegge) */
$messages['da'] = array(
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
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'imagemap_desc'                 => "Ermöglicht die Erstellung von verweissensitiven Grafiken ''(image maps)'' mit Hilfe der <tt><nowiki><imagemap></nowiki></tt>-Syntax",
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
);

$messages['el'] = array(
	'imagemap_description'          => 'Σχετικά με αυτήν την εικόνα',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'imagemap_desc'          => 'Permesas klientflankajn klakeblajn bildmapojn uzante etikedon <tt><nowiki><imagemap></nowiki></tt>',
	'imagemap_invalid_image' => '&lt;imagemap&gt;: bildo estas aŭ nevalida aŭ neekzista',
	'imagemap_invalid_title' => '&lt;imagemap&gt;: nevalida titolo en ligilo ĉe linio $1',
	'imagemap_description'   => 'Pri ĉi tiu bildo',
);

/** Basque (Euskara)
 * @author SPQRobin
 */
$messages['eu'] = array(
	'imagemap_description' => 'Irudi honen inguruan',
);

$messages['ext'] = array(
	'imagemap_description'          => 'Al tentu esta imahin',
);

/** فارسی (فارسی)
 * @author Huji
 */
$messages['fa'] = array(
	'imagemap_desc'               => 'امکان ایجاد نقشه‌های تصویری قابل کلیک کردن در سمت کاربر را با استفاده از برچسب <tt><nowiki><imagemap></nowiki></tt> فراهم می‌آورد',
	'imagemap_no_image'           => '<imagemap>: باید در اولین سطر یک تصویر را مشخص کنید',
	'imagemap_invalid_image'      => '<imagemap>: تصویر غیرمجاز است یا وجود ندارد',
	'imagemap_no_link'            => '<imagemap>: هیچ پیوند مجازی تا انتهای سطر $1 پیدا نشد',
	'imagemap_invalid_title'      => '<imagemap>: عنوان غیرمجاز در پیوند سطر $1',
	'imagemap_missing_coord'      => '<imagemap>: تعداد مختصات در سطر $1 برای شکل کافی نیست',
	'imagemap_unrecognised_shape' => '<imagemap>: شکل ناشناخته در سطر $1، هر سطر باید با یکی از این دستورات آغاز شود: default، rect، circle یا poly',
	'imagemap_no_areas'           => '<imagemap>: دست کم یک تخصیص فضا باید وجود داشته باشد',
	'imagemap_invalid_coord'      => '<imagemap>: مختصات غیرمجاز در سطر $1، مختصات باید عدد باشد',
	'imagemap_invalid_desc'       => '<imagemap>: توضیحات غیرمجاز، باید یکی از این موارد باشد: <tt>$1</tt>',
	'imagemap_description'        => 'دربارهٔ این تصویر',

);

/** Finnish (Suomi)
 * @author Nike
 */
$messages['fi'] = array(
	'imagemap_desc'          => 'Mahdollistaa napsautettavien kuvakarttojen tekemisen <tt><nowiki><imagemap></nowiki></tt>-elementillä.',
	'imagemap_no_image'      => '&lt;imagemap&gt;: kuva pitää määritellä ensimmäisellä rivillä.',
	'imagemap_invalid_image' => '&lt;imagemap&gt;: kuva ei kelpaa tai sitä ei ole olemassa',
	'imagemap_no_areas'      => '&lt;imagemap&gt;: aluemäärittelyitä pitää olla ainakin yksi.',
	'imagemap_invalid_coord' => '&lt;imagemap&gt;: kelpaamaton koordinaatti rivillä $1. Koordinaatin täytyy olla numero.',
	'imagemap_description'   => 'Kuvan tiedot',
);

/** French (Français)
 * @author Grondin
 * @author Urhixidur
 */
$messages['fr'] = array(
	'imagemap_desc'               => 'Permet les cartes images clientes cliquables, grâce à la balise <tt><nowiki><imagemap></nowiki></tt>',
	'imagemap_no_image'           => '&lt;imagemap&gt; : vous devez spécifier une image dans la première ligne',
	'imagemap_invalid_image'      => '&lt;imagemap&gt; : l’image est invalide ou n’existe pas',
	'imagemap_no_link'            => '&lt;imagemap&gt; : aucun lien valide n’a été trouvé à la fin de la ligne $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt; : titre invalide dans le lien à la ligne  $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt; : pas assez de coordonnées pour la forme à la ligne  $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt; : forme non reconnue à la ligne $1, chaque ligne doit commencer avec un des mots suivants : default, rect, circle or poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt; : au moins une spécification d’aire doit être donnée',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt; : coordonnée invalide à la ligne $1, doit être un nombre',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt; : paramètre « desc » invalide, les paramètres possibles sont : $1',
	'imagemap_description'        => 'À propos de cette image',
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'imagemap_desc'               => 'Pèrmèt una mapa émâge clianta a étre clicâ en utilisent la balisa <tt><nowiki><imagemap></nowiki></tt>.',
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
);

/** Galician (Galego)
 * @author Alma
 * @author Xosé
 */
$messages['gl'] = array(
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
);

/** Gujarati (ગુજરાતી) */
$messages['gu'] = array(
	'imagemap_description' => 'આ ચિત્ર વિષે',
);

/* Hebrew (Rotem Liss) */
$messages['he'] = array(
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
);

/** Hindi (हिन्दी)
 * @author Kaustubh
 */
$messages['hi'] = array(
	'imagemap_desc'               => 'क्लायंटके चित्रनक्शे <tt><nowiki><imagemap></nowiki></tt> टॅग देकर इस्तेमाल किये जा सकतें हैं',
	'imagemap_no_image'           => '&lt;imagemap&gt;: पहली कतारमें चित्र देना जरूरी हैं',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: गलत या अस्तित्वमें ना होने वाला चित्र',
	'imagemap_no_link'            => '&lt;imagemap&gt;: $1 कतार के आखिर में वैध कड़ी मिली नहीं',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: $1 कतारमें दिये कड़ीका अवैध शीर्षक',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: $1 कतारपर आकार के लिये जरूरी कोऑर्डिनेट्स नहीं हैं',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: $1 कतारमें गलत आकार, हर कतार: default, rect, circle अथवा poly से शुरू होनी चाहियें',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: कमसे कम एक आकार देना चाहिये',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: $1 कतार में गलत कोऑर्डिनेट्स, संख्या चाहिये',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: गलत ज़ानकारी, इसमेंसे एक होनी चाहिये: <tt>$1</tt>',
	'imagemap_description'        => 'इस चित्र के बारे में',
);

$messages['hr'] = array(
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
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'imagemap_desc'               => 'Zmóžnja klikajomne wobrazowe mapy na klientowej stronje z pomocu taflički <tt><nowiki><imagemap></nowiki></tt>',
	'imagemap_no_image'           => '&lt;imagemap&gt; zmylk: Dyrbiš w prěnjej lince wobraz podać',
	'imagemap_invalid_image'      => '&lt;imagemap&gt; zmylk: Wobraz je njepłaćiwy abo njeeksistuje',
	'imagemap_no_link'            => '&lt;imagemap&gt; zmylk: Na kóncu linki $1 njebu płaćiwy wotkaz namakany',
	'imagemap_invalid_title'      => '&lt;imagemap&gt; zmylk: njepłaćiwy titul we wotkazu w lince $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt; zmylk: Přemało koordinatow w lince $1 za podobu',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt; zmylk: Njeznata podoba w lince $1, kóžda linka dyrbi so z jednym z tutych parametrow započeć: <tt>default, rect, circle</tt> abo <tt>poly</tt>',
	'imagemap_no_areas'           => '&lt;imagemap&gt; zmylk: Dyrbi so znajmjeńša přestrjeń definować.',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt; zmylk: njepłaćiwa koordinata w lince $1: su jenož ličby dowolene',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt; zmylk: Njepłaćiwy parameter "desc", móžne su: <tt>$1</tt>',
	'imagemap_description'        => 'Wo tutym wobrazu',
);

/** Hungarian (Magyar)
 * @author KossuthRad
 * @author Dani
 */
$messages['hu'] = array(
	'imagemap_desc'               => 'Lehetővé teszi kliensoldali imagemap-ek létrehozását a <tt><nowiki><imagemap></nowiki></tt> tag segítségével',
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
);

/** Indonesian (Bahasa Indonesia)
 * @author IvanLanin
 */
$messages['id'] = array(
	'imagemap_desc'                 => 'Menyediakan peta gambar yang dapat diklik dari klien dengan menggunakan tag <tt><nowiki><imagemap></nowiki></tt>',
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
);

/** Ido (Ido)
 * @author Malafaya
 */
$messages['io'] = array(
	'imagemap_description' => 'Pri ca imajo',
);

/** Icelandic (Íslenska)
 * @author SPQRobin
 */
$messages['is'] = array(
	'imagemap_description' => 'Um þessa mynd',
);

/** Italian (Italiano)
 * @author Anyfile
 * @author BrokenArrow
 */
$messages['it'] = array(
	'imagemap_desc'               => "Consente di realizzare ''image map'' cliccabili lato client con il tag <tt><nowiki><imagemap></nowiki></tt>",
	'imagemap_no_image'           => "&lt;imagemap&gt;: si deve specificare un'immagine nella prima riga",
	'imagemap_invalid_image'      => "&lt;imagemap&gt;: l'immagine non è valida o non esiste",
	'imagemap_no_link'            => '&lt;imagemap&gt;: non è stato trovato alcun collegamento valido alla fine della riga $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: titolo del collegamento non valido nella riga $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: non ci sono abbastanza coordinate per la forma specificata nella riga $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: Forma (shape) non riconosciuta nella riga $1, ogni riga deve iniziare con uno dei seguenti: default, rect, circle o poly',
	'imagemap_no_areas'           => "&lt;imagemap&gt;: deve essere specificata almeno un'area",
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: coordinata non valida nella riga $1, deve essere un numero',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: Valore non valido per il parametro desc, deve essere uno dei seguenti: $1',
	'imagemap_description'        => "Informazioni sull'immagine",
);

/** Japanese (日本語)
 * @author Kahusi
 * @author JtFuruhata
 */
$messages['ja'] = array(
	'imagemap_desc'               => '<tt><nowiki><imagemap></nowiki></tt>タグによるクライアントサイドのクリッカブルマップ機能を有効にする',
	'imagemap_no_image'           => '&lt;imagemap&gt;: 最初の行で画像を指定して下さい。',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: 画像が無効、又は存在しません。',
	'imagemap_no_link'            => '&lt;imagemap&gt;: 有効なリンクが$1行目の最後に存在しません。',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: $1行目のリンクのタイトルが無効です。',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: $1行目にある図形の座標指定が不足しています。',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: $1行目の図形は認められません。各行は次のどれかで始まる必要があります: default, rect, circle, poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: 図形の指定がありません。',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: $1行目の座標が無効です。数字を指定して下さい。',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: 無効なdescの指定です。次のどれかを指定して下さい: <tt>$1</tt>',
	'imagemap_description'        => '画像の詳細',
);

/** Javanese (Basa Jawa)
 * @author Meursault2004
 */
$messages['jv'] = array(
	'imagemap_desc'               => 'Nyedyakaké péta gambar sing bisa diklik saka klièn mawa nganggo tag <tt><nowiki><imagemap></nowiki></tt>',
	'imagemap_no_image'           => '&lt;imagemap&gt;: kudu mènèhi sawijining gambar ing baris kapisan',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: gambar ora absah utawa ora ditemokaké',
	'imagemap_no_link'            => '&lt;imagemap&gt;: ora ditemokaké pranala sing absah ing pungkasan baris kaping $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: irah-irahan ora absah ing pranala ing baris kaping $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: ora cukup koordinat kanggo wujud ing baris kaping $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: wujud ora ditepungi ing baris kaping $1, saben baris kudu diwiwiti mawa salah siji saka: default, rect, circle utawa poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: kudu diwènèhi spésifikasi area minimal sawiji',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: koordinat ora absah ing baris kaping $1, kudu awujud angka',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: spésifikasi desc ora absah, kudu salah siji saka: $1',
	'imagemap_description'        => 'Prekara gambar iki',
);

/* Kazakh Arabic (AlefZet) */
$messages['kk-arab'] = array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: بٸرٸنشٸ جولدا سۋرەتتٸ كٶرسەتۋ قاجەت',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: سۋرەت جارامسىز نەمەسە جوق',
	'imagemap_no_link'              => '&lt;imagemap&gt;: $1 جول اياعىندا جارامدى سٸلتەمە تابىلمادى',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: $1 جول اياعىنداعى سٸلتەمەدە جارامسىز اتاۋ',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: $1 جولداعى كەسكٸن ٷشٸن كوورديناتتار جەتٸكسٸز',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: $1 جولداعى كەسكٸن جارامسىز, ٵربٸر جول مىنانىڭ بٸرەۋٸنەن باستالۋ قاجەت: ',
									   'default, rect, circle or poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: ەڭ كەمٸندە بٸر اۋماق ماماندانىمى بەرٸلۋ قاجەت',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: $1 جولىندا جارامسىز كوورديناتا, سان بولۋى قاجەت',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: جارامسىز سيپاتتاما ماماندانىمى, مىنانىڭ بٸرەۋٸ بولۋى قاجەت: $1',
	'imagemap_description'          => 'بۇل سۋرەت تۋرالى',
);

/* Kazakh Cyrillic (AlefZet) */
$messages['kk-cyrl'] = array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: бірінші жолда суретті көрсету қажет',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: сурет жарамсыз немесе жоқ',
	'imagemap_no_link'              => '&lt;imagemap&gt;: $1 жол аяғында жарамды сілтеме табылмады',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: $1 жол аяғындағы сілтемеде жарамсыз атау',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: $1 жолдағы кескін үшін координаттар жетіксіз',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: $1 жолдағы кескін жарамсыз, әрбір жол мынаның біреуінен басталу қажет: ',
									   'default, rect, circle or poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: ең кемінде бір аумақ маманданымы берілу қажет',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: $1 жолында жарамсыз координата, сан болуы қажет',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: жарамсыз сипаттама маманданымы, мынаның біреуі болуы қажет: $1',
	'imagemap_description'          => 'Бұл сурет туралы',
);

/* Kazakh Latin (AlefZet) */
$messages['kk-latn'] = array(
	'imagemap_no_image'             => '&lt;imagemap&gt;: birinşi jolda swretti körsetw qajet',
	'imagemap_invalid_image'        => '&lt;imagemap&gt;: swret jaramsız nemese joq',
	'imagemap_no_link'              => '&lt;imagemap&gt;: $1 jol ayağında jaramdı silteme tabılmadı',
	'imagemap_invalid_title'        => '&lt;imagemap&gt;: $1 jol ayağındağı siltemede jaramsız ataw',
	'imagemap_missing_coord'        => '&lt;imagemap&gt;: $1 joldağı keskin üşin koordïnattar jetiksiz',
	'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: $1 joldağı keskin jaramsız, ärbir jol mınanıñ birewinen bastalw qajet: ',
									   'default, rect, circle or poly',
	'imagemap_no_areas'             => '&lt;imagemap&gt;: eñ keminde bir awmaq mamandanımı berilw qajet',
	'imagemap_invalid_coord'        => '&lt;imagemap&gt;: $1 jolında jaramsız koordïnata, san bolwı qajet',
	'imagemap_invalid_desc'         => '&lt;imagemap&gt;: jaramsız sïpattama mamandanımı, mınanıñ birewi bolwı qajet: $1',
	'imagemap_description'          => 'Bul swret twralı',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 * @author Lovekhmer
 */
$messages['km'] = array(
	'imagemap_description' => 'អំពីរូបភាពនេះ',
);

$messages['la'] = array(
	'imagemap_description'          => 'De hac imagine',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'imagemap_desc'               => 'Erlaabt et Biller ze benotzen déi een uklicke ka mat Hellëf vum Tag <tt><nowiki><imagemap></nowiki></tt>.',
	'imagemap_no_image'           => '&lt;imagemap&gt;-Feeler: Dir musst an der éischter Linn e Bild uginn',
	'imagemap_invalid_image'      => "&lt;imagemap&gt;-Feeler: d'Bild ass ongëltig oder net do",
	'imagemap_no_link'            => '&lt;imagemap&gt;-Feeler: Um Enn vun der Zeil $1 gouf kee gëltege Link fonnt',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;-Feeler: ongëltigen Titel am Link an der Zeil $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;-Feeler: Ze wéineg Koordinaten an der Zeil $1 fir den Ëmress',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;-Feeler: Onbekannte Form an der Zeil $1. All Zeile muss matt engem vun dëse Parameter ufänken: <tt>default, rect, circle</tt> oder <tt>poly</tt>',
	'imagemap_no_areas'           => '&lt;imagemap&gt;-Feeler: Dir musst mindestens eng Fläch definéieren',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;-Feeler: Ongëlteg Koordinaten an der Zeil $1: et sinn nëmmen Zuelen erlaabt',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;-Feeler: Ongëltegen „desc“-Parameter, méiglech sinn: <tt>$1</tt>',
	'imagemap_description'        => 'Iwwert dëst Bild',
);

/** Limburgish (Limburgs)
 * @author Matthias
 */
$messages['li'] = array(
	'imagemap_desc'               => 'Maakt aanklikbare imagemaps meugelijk met de tag <tt><nowiki><imagemap></nowiki></tt>',
	'imagemap_no_image'           => "&lt;imagemap&gt;: geef 'n afbeelding op in de eerste regel",
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: de afbeelding is corrupt of bestaat neet',
	'imagemap_no_link'            => '&lt;imagemap&gt;: er is geen geldige link aangetroffen aan het einde van regel $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: er staat een ongeldige titel in de verwijzing op regel $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: neet genoeg coördinaten veur vorm in regel $1',
	'imagemap_unrecognised_shape' => "&lt;imagemap&gt;: neet herkende vorm in regel $1, iedere regel mot beginne met éin van de commando's: default, rect, circle of poly",
	'imagemap_no_areas'           => '&lt;imagemap&gt;: er moet tenminste één gebied gespecificeerd worde',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: ongeldige coördinaten in regel $1, moet een getal zien',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: ongeldige beschrijvingsspecificatie, dit moet er één zijn uit de volgende lijst: $1',
	'imagemap_description'        => 'Euver deze aafbeelding',
);

/** Lithuanian (Lietuvių)
 * @author Matasg
 * @author Garas
 */
$messages['lt'] = array(
	'imagemap_no_image'           => '&lt;imagemap&gt;: privalote nurodyti paveikslėlį pirmoje linijoje',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: blogas arba neegzistuojantis paveikslėlis',
	'imagemap_no_link'            => '&lt;imagemap&gt;: nerasta tinkama nuoroda eilutės $1 pabaigoje',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: blogas pavadinimas nuorodoje $1 eilutėje',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: nėra pakankamai koordinačių figūrai $1 eilutėje',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: neatpažįstama figūra $1 eilutėje, kiekviena eilutė privalo prasidėti su vienu iš šių žodžių: default, rect, circle arba poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: privalo būti duoda mažiausiai viena vietos specifikacija',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: netinkama koordinatė $1 eilutėje, privalo būti skaičius',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: bloga aprašymo specifikacija, privalo būti viena iš: <tt>$1</tt>',
	'imagemap_description'        => 'Apie šį paveikslėlį',
);

/** Malayalam (മലയാളം)
 * @author Shijualex
 */
$messages['ml'] = array(
	'imagemap_no_image'           => '&lt;imagemap&gt;: ഒന്നാമത്തെ വരിയില്‍ ഒരു ചിത്രത്തിന്റെ പേരു വേണം',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: ചിത്രം അസാധുവാണ്‌ അല്ലെങ്കില്‍ നിലവിലില്ല',
	'imagemap_no_link'            => '&lt;imagemap&gt;: $1-മത്തെ വരിയുടെ അവസാനം സാധുവായ കണ്ണി കാണുന്നില്ല',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: $1-മത്തെ വരിയില്‍ അസാധുവായ തലക്കെട്ട്',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: $1-മത്തെ വരിയില്‍ രൂപത്തിനുള്ള നിര്‍ദ്ദേശാങ്കങ്ങള്‍ നിര്‍‌വചിച്ചിട്ടില്ല.',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: $1മത്തെ വരിയില്‍ മനസ്സിലാക്കാന്‍ പറ്റാത്ത രൂപം. ഓരോ വരിയും ഇനി പറയുന്ന ഒന്നു കൊണ്ടു വേണം തുടങ്ങാന്‍:default, rect, circle or poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: കുറഞ്ഞപക്ഷം ഒരു വിസ്തീര്‍ണ്ണ നിര്‍ദ്ദേശമെങ്കിലും കൊടുത്തിരിക്കണം',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: $1-മത്തെ വരിയില്‍ അസാധുവായ നിര്‍ദേശാങ്കം. നിര്‍ബന്ധമായും അത് ഒരു സംഖ്യയായിരിക്കണം.',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: അസാധുവായ വിവരണ നിര്‍ദ്ദേശം. ഇനി പറയുന്ന ഇനങ്ങളില്‍ ഒനായിരിക്കണം: <tt>$1</tt>',
	'imagemap_description'        => 'ഈ ചിത്രത്തെ കുറിച്ച്',
);

/** Marathi (मराठी)
 * @author Kaustubh
 */
$messages['mr'] = array(
	'imagemap_desc'               => 'क्लायंटकडील चित्रनकाशे <tt><nowiki><imagemap></nowiki></tt> टॅग देऊन वापरता येऊ शकतात.',
	'imagemap_no_image'           => '&lt;imagemap&gt;: पहिल्या ओळीत चित्र देणे गरजेचे आहे',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: चुकीचे अथवा अस्तित्वात नसलेले चित्र',
	'imagemap_no_link'            => '&lt;imagemap&gt;: $1 ओळीच्या शेवटी योग्य दुवा सापडलेला नाही',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: $1 ओळीतील दुव्याचे चुकीचे शीर्षक',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: $1 ओळीवरील आकारासाठी पुरेसे कोऑर्डिनेट्स नाहीत',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: $1 ओळीमध्ये चुकीचा आकार, प्रत्येक ओळ ही: default, rect, circle अथवा poly पासून सुरु व्हायला पाहिजे.',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: कमीतकमी एक आकार दिला पाहिजे',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;:  $1 ओळीवर चुकीचे कोऑर्डिनेट्स, संख्या हवी',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: चुकीची माहिती, यापैकी एक असायला हवी: <tt>$1</tt>',
	'imagemap_description'        => 'या चित्राबद्दल माहिती',
);

$messages['nds'] = array(
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
);

/** Nepali (नेपाली)
 * @author SPQRobin
 */
$messages['ne'] = array(
	'imagemap_description' => 'यो चित्रको बारेमा',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'imagemap_desc'               => 'Maakt aanklikbare imagemaps mogelijk met de tag <tt><nowiki><imagemap></nowiki></tt>',
	'imagemap_no_image'           => '&lt;imagemap&gt;: geef een afbeelding op in de eerste regel',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: de afbeelding is corrupt of bestaat niet',
	'imagemap_no_link'            => '&lt;imagemap&gt;: er is geen geldige link aangetroffen aan het einde van regel $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: er staat een ongeldige titel in de verwijzing op regel $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: niet genoeg coördinaten voor vorm in regel $1',
	'imagemap_unrecognised_shape' => "&lt;imagemap&gt;: niet herkende vorm in regel $1, iedere regel moet beginnen met één van de commando's: default, rect, circle of poly",
	'imagemap_no_areas'           => '&lt;imagemap&gt;: er moet tenminste één gebied gespecificeerd worden',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: ongeldige coördinaten in regel $1, moet een getal zijn',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: ongeldige beschrijvingsspecificatie, dit moet er één zijn uit de volgende lijst: $1',
	'imagemap_description'        => 'Over deze afbeelding',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'imagemap_desc'               => 'Gjør at man kan bruke klikkbare bilder ved hjelp av <tt><nowiki><imagemap></nowiki></tt>.',
	'imagemap_no_image'           => '&lt;imagemap&gt;: må angi et bilde i første linje',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: bilde er ugyldig eller ikke-eksisterende',
	'imagemap_no_link'            => '&lt;imagemap&gt;: ingen gyldig lenke ble funnet i slutten av linje $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: ugyldig tittel i lenke på linje $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: ikke nok koordinater for form på linje $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: ugjenkjennelig form på linje $1; hver linje må starte med enten: default, rect, circle eller poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: minst en områdespesifikasjon må gis',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: ugyldig koordinat i slutten av linje $1, må være et tall',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: ugyldig desc-spesifisering, må være enten: <tt>$1</tt>',
	'imagemap_description'        => 'Om dette bildet',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'imagemap_desc'               => "Permet qu'una mapa imatge clienta siá clicabla en utilizant la balisa <tt><nowiki><imagemap></nowiki></tt>",
	'imagemap_no_image'           => '&lt;imagemap&gt; : vos cal especificar un imatge dins la primièra linha',
	'imagemap_invalid_image'      => '&lt;imagemap&gt; : l’imatge es invalid o existís pas',
	'imagemap_no_link'            => '&lt;imagemap&gt; : cap de ligam valid es pas estat trobat a la fin de la linha $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt; : títol invalid dins lo ligam a la linha $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt; : pas pro de coordenadas per la forma a la linha $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt; : forma pas reconeguda a la linha $1, cada linha deu començar amb un dels mots seguents : default, rect, circle o poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt; : almens una especificacion d’aira deu èsser balhada',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt; : coordenada invalida a la linha $1, deu èsser un nombre',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt; : paramètre « desc » invalid, los paramètres possibles son : $1',
	'imagemap_description'        => "A prepaus d'aqueste imatge",
);

/** Ossetic (Иронау)
 * @author Amikeco
 */
$messages['os'] = array(
	'imagemap_description' => 'Ацы нывы тыххæй',
);

/** Polish (Polski)
 * @author Derbeth
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'imagemap_desc'               => 'Umożliwia stworzenie po stronie klienta klikalnej mapy z użyciem znacznika <tt><nowiki><imagemap></nowiki></tt>',
	'imagemap_no_image'           => '&lt;imagemap&gt;: należy wpisać grafikę w pierwszej linii',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: grafika jest niepoprawna lub nie istnieje',
	'imagemap_no_link'            => '&lt;imagemap&gt;: nie znaleziono poprawnego linku na końcu linii $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: niepoprawny tytuł linku w linii $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: niewystarczająca liczba współrzędnych dla kształtu zdefiniowanego w linii $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: nierozpoznany kształt w linii $1; każda linia musi zawierać tekst: default, rect, circle lub poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: należy podać przynajmniej jedną specyfikację pola',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: nieprawidłowa współrzędna w linii $1; należy podać liczbę',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: nieprawidłowa specyfikacja opisu; należy wpisać jeden z wariantów: <tt>$1</tt>',
	'imagemap_description'        => 'Informacje o tej grafice',
);

/* Piedmontese (Bèrto 'd Sèra) */
$messages['pms'] = array(
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
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'imagemap_description' => 'ددې انځور په اړه',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'imagemap_desc'               => 'Permite mapas de imagem clicáveis no lado do cliente usando a "tag" <tt><nowiki><imagemap></nowiki></tt>',
	'imagemap_no_image'           => '&lt;imagemap&gt;: é necessário especificar uma imagem na primeira linha',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: imagem inválida ou inexistente',
	'imagemap_no_link'            => '&lt;imagemap&gt;: não foi encontrado um link válido ao final da linha $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: título inválido no link da linha $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: coordenadas insuficientes para formar uma figura na linha $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: figura não reconhecida na linha $1. Cada linha precisa iniciar com: default, rect, circle ou poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: é necessário fornecer ao menos uma especificação de área',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: coordenada inválida na linha $1. 0 necessário que seja um número',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: especificação desc inválida. 0 necessário que seja uma dentre: <tt>$1</tt>',
	'imagemap_description'        => 'Sobre esta imagem',
);

/** Quechua (Runa Simi)
 * @author AlimanRuna
 */
$messages['qu'] = array(
	'imagemap_description' => 'Kay rikchamanta',
);

/** Russian (Русский)
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'imagemap_desc'               => 'Позволяет указывать срабатывающие на нажатие карты изображений на стороне клиента с помощью тега <tt><nowiki><imagemap></nowiki></tt>',
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
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'imagemap_desc'               => 'Бу <tt><nowiki><imagemap></nowiki></tt> тиэги туһанан клиент өттүгэр каартаны баттааһын үлэлиирин көҥүллүүр',
	'imagemap_no_image'           => '&lt;imagemap&gt;: бастакы строкатыгар ойуу баар буолуохтаах',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: ойуу сыыһа бэриллибит, эбэтэр отой суох',
	'imagemap_no_link'            => '&lt;imagemap&gt;: $1 строка бүтэһигэр сыыһа ыйынньык турбут',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: $1 строкаҕа ыйынньык баһа сыыһа суруллубут',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: недостаточно координат для фигуры в строке $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: неопознанная фигура в строке $1, каждая строка должна начинаться одним из ключевых слов: default, rect, circle или poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: саатар биир уобалас ыйыллыахтаах',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: $1 строкаҕа сыыһа координата суруллубут, чыыһыла буолуохтаах',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: desc суолтата сыыһа турбут, мантан талыахха наада: <tt>$1</tt>',
	'imagemap_description'        => 'Ойуу туһунан',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'imagemap_desc'               => 'Poskytuje klikateľné obrázkové mapy spracúvané na strane klienta pomocou značky <tt><nowiki><imagemap></nowiki></tt>',
	'imagemap_no_image'           => '&lt;imagemap&gt;: musí mať na prvom riadku uvedený obrázok',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: obrázok je neplatný alebo neexistuje',
	'imagemap_no_link'            => '&lt;imagemap&gt;: na konci riadka $1 nebol nájdený platný odkaz',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: neplatný nadpis v odkaze na riadku $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: nedostatok súradníc na vytvorenie tvaru na riadku $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: nerozpoznaný tvar na riadku $1, každý riadok musí začínať jedným z: default, rect, circle alebo poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: musí byť zadaná najmenej jedna špecifikácia oblasti',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: neplatná súradnica na riadku $1, musí to byť číslo',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: neplatný popis, musí byť jedno z nasledovných: $1',
	'imagemap_description'        => 'O tomto obrázku',
);

/** ћирилица (ћирилица)
 * @author Sasa Stefanovic
 */
$messages['sr-ec'] = array(
	'imagemap_description' => 'О овој слици',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'imagemap_desc'               => "Moaket dät muugelk ferwies-sensitive Grafike ''(image maps)'' tou moakjen mäd Hälpe fon ju <tt><nowiki><imagemap></nowiki></tt>-Syntax",
	'imagemap_no_image'           => '&lt;imagemap&gt;-Failer: In ju eerste Riege mout ne Bielde ounroat wäide',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;-Failer: Bielde is uungultich of is nit deer',
	'imagemap_no_link'            => '&lt;imagemap&gt;-Failer: Ap Eende fon Riege $1 wuude neen gultige Link fuunen',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;-Failer: uungultigen Tittel in dän Link in Riege $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;-Failer: Tou min Koordinate in Riege $1 foar dän Uumriet',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;-Failer: Uunbekoande Uumrietfoarm in Riege $1. Älke Riege mout mäd aan fon disse Parametere ounfange: <tt>default, rect, circle</tt> of <tt>poly</tt>',
	'imagemap_no_areas'           => '&lt;imagemap&gt;-Failer: Der mout mindestens een Gebiet definiert wäide',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;-Failer: Uungultige Koordinate in Riege $1: der sunt bloot Taale toulät',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;-Failer: Uungultigen „desc“-Parameter, muugelk sunt: <tt>$1</tt>',
	'imagemap_description'        => 'Uur disse Bielde',
);

/** Sundanese (Basa Sunda)
 * @author Kandar
 */
$messages['su'] = array(
	'imagemap_description' => 'Ngeunaan ieu gambar',
);

/** Swedish (Svenska)
 * @author M.M.S.
 * @author Lejonel
 */
$messages['sv'] = array(
	'imagemap_desc'               => 'Lägger till taggen <tt><nowiki><imagemap></nowiki></tt> för klickbara bilder',
	'imagemap_no_image'           => '&lt;imagemap&gt;: en bild måste anges på första raden',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: bilden är ogiltig eller existerar inte',
	'imagemap_no_link'            => '&lt;imagemap&gt;: ingen giltig länk fanns i slutet av rad $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: felaktig titel i länken på rad $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: koordinater saknas för området på rad $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: okänd områdesform på rad $1, varje rad måste börja med något av följande: <tt>default, rect, circle, poly</tt>',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: minst ett område måste specificeras',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: ogiltig koordinat på rad $1, koordinater måste vara tal',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: ogiltig specifikation av desc, den måste var en av följande: <tt>$1</tt>',
	'imagemap_description'        => 'Bildinformation',
);

/** Telugu (తెలుగు)
 * @author Veeven
 * @author Chaduvari
 */
$messages['te'] = array(
	'imagemap_desc'          => '<tt><nowiki><imagemap></nowiki></tt> ట్యాగును వాడటం ద్వారా క్లిక్కదగ్గ క్లయంటు-వైపు ఇమేజి మ్యాపులను అనుమతిస్తుంది',
	'imagemap_no_image'      => '&lt;imagemap&gt;: తప్పనిసరిగా మొదటి లైనులో ఓ బొమ్మని ఇవ్వాలి',
	'imagemap_invalid_image' => '&lt;imagemap&gt;: తప్పుడు లేదా ఉనికిలో లేని బొమ్మ',
	'imagemap_no_link'       => '&lt;imagemap&gt;: $1వ లైను చివర సరియైన లింకు కనబడలేదు',
	'imagemap_invalid_title' => '&lt;imagemap&gt;: $1వ లైనులో ఉన్న లింకులో తప్పుడు శీర్షిక',
	'imagemap_missing_coord' => '&lt;imagemap&gt;: ఆకృతికి తగినన్ని నిరూపకాలు $1వ లైనులో లేవు',
	'imagemap_no_areas'      => '&lt;imagemap&gt;: కనీసం ఒక్క areaని అయినా ఇచ్చితీరాలి',
	'imagemap_invalid_coord' => '&lt;imagemap&gt;: $1వ లైనులో తప్పుడు నిరూపకం, అది ఖచ్చితంగా సంఖ్య అయివుండాలి.',
	'imagemap_invalid_desc'  => '&lt;imagemap&gt;: descని తప్పుగా ఇచ్చారు, అది వీటిల్లో ఏదో ఒకటి అయివుండాలి: <tt>$1</tt>',
	'imagemap_description'   => 'ఈ బొమ్మ గురించి',
);

$messages['tet'] = array(
	'imagemap_description'          => 'Kona-ba imajen ne\'e',
);

/** Tajik (Тоҷикӣ)
 * @author Ibrahim
 */
$messages['tg-cyrl'] = array(
	'imagemap_desc'               => 'Имкони эҷоди нақшаҳои тасвирӣ қобили клик кардан дар самти корбарро бо истифода аз барчасби  <tt><nowiki><imagemap></nowiki></tt> фароҳам меоварад',
	'imagemap_no_image'           => '&lt;imagemap&gt;: бояд дар сатри аввал як аксро мушаххас кунед',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: акс ғайримиҷоз аст ё вуҷуд надорад',
	'imagemap_no_link'            => '&lt;imagemap&gt;: ҳеҷ пайванди миҷозе то интиҳои сатри $1 пайдо нашуд',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: унвони ғайримиҷоз дар пайванди сатри $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: теъдоди ҳамоҳангӣ дар сатри $1 барои шакл кофӣ нест',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: шакли ношинохта дар сатри $1, ҳар сатр бояд бо яке аз ин дастурот оғоз шавад: default, rect, circle ё poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: дасти кам бояд як мушаххасоти фазо бояд вуҷуд дошта бошад',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: баробарии ғайримиҷоз дар сатри $1, бояд адад бошад',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: тавзеҳоти ғайримиҷоз, бояд яке аз ин маворид бошад: <tt>$1</tt>',
	'imagemap_description'        => 'Дар бораи ин акс',
);

/** Turkish (Türkçe)
 * @author Karduelis
 */
$messages['tr'] = array(
	'imagemap_description' => 'Resim hakkında',
);

/** Ukrainian (Українська)
 * @author Ahonc
 */
$messages['uk'] = array(
	'imagemap_desc'               => 'Дозволяє створювати на боці клієнта карти зображень, які спрацьовують при натисканні, за допомогою тегу <tt><nowiki><imagemap></nowiki></tt>',
	'imagemap_no_image'           => '&lt;imagemap&gt;: у першому рядку має бути задане зображення',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: неправильне або відсутнє зображення',
	'imagemap_no_link'            => '&lt;imagemap&gt;: неправильне посилання в кінці рядка $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: неправильний заголовок посилання в рядку $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: недостатньо координат для фігури в рядку $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: нерозпізнана фігура в рядку $1, кожен рядок повинен починатися з одного з ключових слів: default, rect, circle або poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: повинна бути зазначена принаймні одна область',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: помилкова координата в рядку $1, має бути число',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: помилкове значення desc, має бути одне з наступних значень: <tt>$1</tt>',
	'imagemap_description'        => 'Опис зображення',
);

/** Vèneto (Vèneto)
 * @author Candalua
 */
$messages['vec'] = array(
	'imagemap_desc'               => "Parméte de realizar ''image map'' clicàbili lato client col tag <tt><nowiki><imagemap></nowiki></tt>",
	'imagemap_no_image'           => '&lt;imagemap&gt;: se gà da specificar na imagine ne la prima riga',
	'imagemap_invalid_image'      => "&lt;imagemap&gt;: l'imagine no la xe valida o no la esiste",
	'imagemap_no_link'            => '&lt;imagemap&gt;: no xe stà catà nissun colegamento valido a la fine de la riga $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: titolo del colegamento mìa valido ne la riga $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: no ghe xe coordinate in bisogno par la forma speçificada ne la riga $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: Forma (shape) mìa riconossiùa ne la riga $1, ogni riga la ga da scuminsiar con uno dei seguenti: default, rect, circle o poly',
	'imagemap_no_areas'           => "&lt;imagemap&gt;: gà da èssar speçificada almanco un'area",
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: coordinata mìa valida ne la riga $1, la gà da èssar un nùmaro',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: Valor mìa valido par el parametro desc, el gà da èssar uno dei seguenti: $1',
	'imagemap_description'        => 'Informazion su sta imagine',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'imagemap_desc'               => 'Thêm những bản đồ hình có liên kết dùng thẻ <tt><nowiki><imagemap></nowiki></tt>',
	'imagemap_no_image'           => '&lt;imagemap&gt;: phải đưa tên hình vào dòng đầu tiên',
	'imagemap_invalid_image'      => '&lt;imagemap&gt;: hình không hợp lệ hay không tồn tại',
	'imagemap_no_link'            => '&lt;imagemap&gt;: không có liên kết hợp lệ ở cuối dòng $1',
	'imagemap_invalid_title'      => '&lt;imagemap&gt;: văn bản liên kết không hợp lệ ở dòng $1',
	'imagemap_missing_coord'      => '&lt;imagemap&gt;: không có đủ tọa độ cho vùng ở dòng $1',
	'imagemap_unrecognised_shape' => '&lt;imagemap&gt;: không hiểu hình dạng ở dòng $1, mỗi dòng phải bắt đầu với một trong: default, rect, circle, hay poly',
	'imagemap_no_areas'           => '&lt;imagemap&gt;: phải định rõ ít nhất một vùng',
	'imagemap_invalid_coord'      => '&lt;imagemap&gt;: tọa độ không hợp lệ ở dòng $1, phải là số',
	'imagemap_invalid_desc'       => '&lt;imagemap&gt;: chọn desc không hợp lệ, phải là một trong: $1',
	'imagemap_description'        => 'Thông tin về hình này',
);

/** Volapük (Volapük)
 * @author Smeira
 * @author Malafaya
 */
$messages['vo'] = array(
	'imagemap_no_image'      => '&lt;imagemap&gt;: lien balid muton keninükön magodanemi',
	'imagemap_invalid_image' => '&lt;imagemap&gt;: magod no lonöfon u no dabinon',
	'imagemap_no_link'       => '&lt;imagemap&gt;: yüm lonöföl no petuvon finü lien: $1',
	'imagemap_invalid_title' => '&lt;imagemap&gt;: tiäd no lonöföl pö yüm su lien: $1',
	'imagemap_invalid_coord' => '&lt;imagemap&gt;: koordinats no lonöföls su lien $1: mutons binön num',
	'imagemap_description'   => 'Tefü magod at',
);

/* Cantonese
 * @author Shinjiman
 */
$messages['yue'] = array(
	'imagemap_desc'                 => '容許客戶端可以用<tt><nowiki><imagemap></nowiki></tt>標籤整可撳圖像地圖',
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
);

/* Chinese (Simplified)
 * @author Shinjiman
 */
$messages['zh-hans'] = array(
	'imagemap_desc'                 => '容许客户端可以使用<tt><nowiki><imagemap></nowiki></tt>标签整可点击图像地图',
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
);

/* Chinese (Traditional)
 * @author Shinjiman
 */
$messages['zh-hant'] = array(
	'imagemap_desc'                 => '容許客戶端可以使用<tt><nowiki><imagemap></nowiki></tt>標籤整可點擊圖像地圖',
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
);

