<?php

/**
 * Internationalisation file for ImageMap extension
 */

function efImageMapMessages() {
	return array(

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
'imagemap_invalid_desc'         => '&lt;imagemap&gt;: invalid desc specification, must be one of: $1',
'imagemap_description'          => 'About this image',
# Note to translators: keep the same order
'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
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
'imagemap_invalid_desc'         => '&lt;imagemap&gt;-Fehler: Ungültiger „desc“-Parameter, möglich sind: $1',
'imagemap_description'          => 'Über dieses Bild',
# Note to translators: keep the same order
'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
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
# Note to translators: keep the same order
'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
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
# Note to translators: keep the same order
'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
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
# Note to translators: keep the same order
'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
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
# Note to translators: keep the same order
'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
#Since, as far I know, the alignment indicator are kept in English in the
#  image inclusion syntax, I kept them in English here too.
),

/* Kazakh default (AlefZet) */
'kk' => array(
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
# Note to translators: keep the same order
'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
),

/* Kazakh Cyrillic (AlefZet) */
'kk-kz' => array(
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
# Note to translators: keep the same order
'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
),

/* Kazakh Latin (AlefZet) */
'kk-tr' => array(
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
# Note to translators: keep the same order
'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
),

/* Kazakh Arabic (AlefZet) */
'kk-cn' => array(
'imagemap_no_image'             => '&lt;imagemap&gt;: بٴىرٴىنشٴى جولدا سۋرەتتٴى كٴورسەتۋ قاجەت',
'imagemap_invalid_image'        => '&lt;imagemap&gt;: سۋرەت جارامسىز نەمەسە جوق',
'imagemap_no_link'              => '&lt;imagemap&gt;: $1 جول اياعىندا جارامدى سٴىلتەمە تابىلمادى',
'imagemap_invalid_title'        => '&lt;imagemap&gt;: $1 جول اياعىنداعى سٴىلتەمەدە جارامسىز اتاۋ',
'imagemap_missing_coord'        => '&lt;imagemap&gt;: $1 جولداعى كەسكٴىن ٴۇشٴىن كوورديناتتار جەتٴىكسٴىز',
'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: $1 جولداعى كەسكٴىن جارامسىز, ٴاربٴىر جول مىنانىڭ بٴىرەۋٴىنەن باستالۋ قاجەت: ',

'imagemap_no_areas'             => '&lt;imagemap&gt;: ەڭ كەمٴىندە بٴىر اۋماق ماماندانىمى بەرٴىلۋ قاجەت',
'imagemap_invalid_coord'        => '&lt;imagemap&gt;: $1 جولىندا جارامسىز كوورديناتا, سان بولۋى قاجەت',
'imagemap_invalid_desc'         => '&lt;imagemap&gt;: جارامسىز سيپاتتاما ماماندانىمى, مىنانىڭ بٴىرەۋٴى بولۋى قاجەت: $1',
'imagemap_description'          => 'بۇل سۋرەت تۋرالى',
# Note to translators: keep the same order
'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
),

/* nld / Dutch (Siebrand Mazeland) */
'nl' => array(
'imagemap_no_image'             => '&lt;imagemap&gt;: geef een afbeelding op in de eerste regel',
'imagemap_invalid_image'        => '&lt;imagemap&gt;: de afbeelding is corrupt of bestaat niet',
'imagemap_no_link'              => '&lt;imagemap&gt;: er is geen geldige link aangetroffen aan het einde van regel $1',
'imagemap_invalid_title'        => '&lt;imagemap&gt;: er staat een ongeldige titel in de verwijzing op regel $1',
'imagemap_missing_coord'        => '&lt;imagemap&gt;: niet genoeg coördinaten voor vorm in regel $1',
'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: niet herkende vorm in regel $1, iedere regel moet beginnen met één van de commando\'s: '.
								   'default, rect, circle of poly',
'imagemap_no_areas'             => '&lt;imagemap&gt;: er moet tenminste één gebied gespecificeerd worden',
'imagemap_invalid_coord'        => '&lt;imagemap&gt;: ongeldige coördinaten in regel $1, moet een getal zijn',
'imagemap_invalid_desc'         => '&lt;imagemap&gt;: ongeldige beschrijvingsspecificatie, dit moet er één zijn uit de volgende lijst: $1',
'imagemap_description'          => 'Over deze afbeelding',
# Note to translators: keep the same order
'imagemap_desc_types'           => 'rechtsboven, rechtsonder, linksonder, linksboven, geen',
),

	);
}

?>
