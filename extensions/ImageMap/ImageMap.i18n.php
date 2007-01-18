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

	);
}

?>