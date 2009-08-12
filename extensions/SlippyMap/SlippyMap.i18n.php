<?php
/**
 * Internationalisation file for SlippyMap extension.
 *
 * @ingroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'slippymap_desc' => 'Adds a <tt>&lt;slippymap&gt;</tt> tag which allows for embedding of static & dynamic maps. Supports multiple map services including [http://openstreetmap.org OpenStreetMap] and NASA Worldwind',

	// The name of the extension, for use in error messages
	'slippymap_extname' => 'SlippyMap',
	'slippymap_tagname' => 'slippymap',

	/**
	 * User errors
	 */
	'slippymap_error' => "$1 error: $2",
	'slippymap_errors' => "$1 errors:",

	'slippymap_error_tag_content_given' => 'The <tt>&lt;$1&gt;</tt> tag only takes attribute arguments (&lt;$1 [...]/&gt;), not input text (&lt;$1&gt; ... &lt;/$1&gt;)',

	// Required parameters
	'slippymap_error_missing_arguments' => "You didn't supply any attributes to the &lt;$1&gt; tag, see [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax usage information] for how to call it.",

	// Required parameters
	'slippymap_error_missing_attribute_lat' => "Missing <tt>lat</tt> attribute (for the latitude).",
	'slippymap_error_missing_attribute_lon' => "Missing <tt>lon</tt> attribute (for the longitude).",
	'slippymap_error_missing_attribute_zoom' => "Missing <tt>zoom</tt> attribute (for the zoom level).",

	// Invalid value
	'slippymap_error_invalid_attribute_lat_value_nan' => "The value <tt>$1</tt> is not valid for the <tt>lat</tt> (latitude) attribute, the given value must be a valid number.",
	'slippymap_error_invalid_attribute_lon_value_nan' => "The value <tt>$1</tt> is not valid for the <tt>lon</tt> (longitude) attribute, the given value must be a valid number.",
	'slippymap_error_invalid_attribute_zoom_value_nan' => "The value <tt>$1</tt> is not valid for the <tt>zoom</tt> attribute, the given value must be a valid number.",
	'slippymap_error_invalid_attribute_width_value_nan' => "The value <tt>$1</tt> is not valid for the <tt>width</tt> attribute, the given value must be a valid number.",
	'slippymap_error_invalid_attribute_height_value_nan' => "The value <tt>$1</tt> is not valid for the <tt>height</tt> attribute, the given value must be a valid number.",
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => "The value <tt>$1</tt> is not valid for the <tt>mode</tt> attribute, valid modes are $2.",
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => "The value <tt>$1</tt> is not valid for the <tt>layer</tt> attribute, valid layers are $2.",
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => "The value <tt>$1</tt> is not valid for the <tt>marker</tt> attribute, valid markers are $2.",
	'slippymap_error_unknown_attribute' => "The attribute <tt>$1</tt> is unknown.",

	// Value out of range
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => "The value <tt>$1</tt> is not valid for the <tt>lat</tt> (latitude) attribute. Latitutes must be between -90 and 90 degrees.",
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => "The value <tt>$1</tt> is not valid for the <tt>lon</tt> (longitude) attribute. Longitudes must be between -180 and 180 degrees.",
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => "The value <tt>$1</tt> is not valid for the <tt>zoom</tt> attribute. Zoom levels must be between $2 and $3.",
	'slippymap_error_invalid_attribute_width_value_out_of_range' => "The value <tt>$1</tt> is not valid for the <tt>width</tt> attribute. Widths must be between $2 and $3.",
	'slippymap_error_invalid_attribute_height_value_out_of_range' => "The value <tt>$1</tt> is not valid for the <tt>height</tt> attribute. Heights must be between $2 and $3.",

	'slippymap_code'    => 'Wikicode for this map view:',
	'slippymap_button_code' => 'Get wikicode',
	'slippymap_resetview' => 'Reset view',
	'slippymap_clicktoactivate' => 'Click to activate map'
);

/** Message documentation (Message documentation)
 * @author EugeneZelenko
 * @author Fryed-peach
 * @author Purodha
 */
$messages['qqq'] = array(
	'slippymap_desc' => 'Short description of the Slippymap extension, shown in [[Special:Version]]. Do not translate or change links.',
	'slippymap_extname' => '{{Optional}}',
	'slippymap_tagname' => '{{Optional}}',
	'slippymap_error' => '* $1 is the name of the extension
* $2 is an error message

{{Identical|Error}}',
	'slippymap_errors' => '* $1 is the (untranslated?) name of the extension

{{Identical|Error}}',
);

/** Arabic (العربية)
 * @author Meno25
 * @author OsamaK
 */
$messages['ar'] = array(
	'slippymap_desc' => 'يسمح باستخدام وسم <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> لعرض خريطة OpenLayers لزقة. الخرائط من [http://openstreetmap.org openstreetmap.org]',
	'slippymap_error' => 'خطأ $1: $2',
	'slippymap_errors' => 'أخطاء $1:',
	'slippymap_error_missing_arguments' => 'لم تعطِ أي خاصية للوسم &lt;$1&gt;، راجع [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax معلومات الاستخدام] لكيفية استدعائه.',
	'slippymap_error_missing_attribute_lat' => 'الخاصية <tt>lat</tt> مفقودة (لخط العرض).',
	'slippymap_error_missing_attribute_lon' => 'الخاصية <tt>lon</tt> مفقودة (لخط الطول).',
	'slippymap_error_missing_attribute_zoom' => 'الخاصية <tt>zoom</tt> مفقودة (لمستوى التكبير).',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'القيمة <tt>$1</tt> ليست صالحة لخاصية <tt>lat</tt>، (خط العرض) يجب أن تكون القيمة المعطاة عددًا صحيحًا.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'القيمة <tt>$1</tt> ليست صالحة لخاصية <tt>lon</tt> (خط الطول)، يجب أن تكون القيمة المعطاة عددًا صحيحًا.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'القيمة <tt>$1</tt> ليست صالحة لخاصية <tt>zoom</tt>، يجب أن تكون القيمة المعطاة عددًا صحيحًا.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'القيمة <tt>$1</tt> ليست صالحة لخاصية <tt>width</tt>، يجب أن تكون القيمة المعطاة عددًا صحيحًا.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'القيمة <tt>$1</tt> ليست صالحة لخاصية <tt>height</tt>، يجب أن تكون القيمة المعطاة عددًا صحيحًا.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => 'القيمة <tt>$1</tt> ليست صالحة لخاصية <tt>mode</tt>، القيم الصالحة $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => 'القيمة <tt>$1</tt> ليست صالحة لخاصية <tt>layer</tt>، القيم الصالحة $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => 'القيمة <tt>$1</tt> ليست صالحة لخاصية <tt>marker</tt>، القيم الصالحة $2.',
	'slippymap_error_unknown_attribute' => 'الخاصية <tt>$1</tt> غير معروفة.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'القيمة <tt>$1</tt> ليست صالحة لخاصية <tt>lat</tt> (خط العرض). يجب أن تكون خطوط العرض بين -90 و 90 درجة.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'القيمة <tt>$1</tt> ليست صالحة لخاصية <tt>lon</tt> (خط الطول). يجب أن تكون خطوط الطول بين -180 و 180 درجة.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'القيمة <tt>$1</tt> ليست صالحة لخاصية <tt>zoom</tt>. يجب أن تكون مستويات التكبير بين $2 و $3.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'القيمة <tt>$1</tt> ليست صالحة لخاصية <tt>width</tt>. يجب أن تكون مستويات العرض بين $2 و $3.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'القيمة <tt>$1</tt> ليست صالحة لخاصية <tt>height</tt>. يجب أن تكون مستويات الارتفاع بين $2 و $3.',
	'slippymap_code' => 'كود الويكي لعرض الخريطة هذا:',
	'slippymap_button_code' => 'الحصول على كود ويكي',
	'slippymap_resetview' => 'إعادة ضبط الرؤية',
	'slippymap_clicktoactivate' => 'انقر لتنشّط الخريطة',
);

/** Aramaic (ܐܪܡܝܐ)
 * @author Basharh
 */
$messages['arc'] = array(
	'slippymap_error' => '$1 ܦܘܕܐ: $2',
	'slippymap_errors' => '$1 ܦܘܕ̈ܐ:',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Meno25
 */
$messages['arz'] = array(
	'slippymap_desc' => 'يسمح باستخدام وسم <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> لعرض خريطة OpenLayers لزقة. الخرائط من [http://openstreetmap.org openstreetmap.org]',
	'slippymap_code' => 'كود الويكى لعرض الخريطة هذا:',
	'slippymap_button_code' => 'الحصول على كود ويكي',
	'slippymap_resetview' => 'إعادة ضبط الرؤية',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 * @author Red Winged Duck
 */
$messages['be-tarask'] = array(
	'slippymap_desc' => 'Дадае тэг <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> для ўбудаваньня статычных і дынамічных мапаў. Падтрымлівае некалькі сэрвісаў мапаў уключаючы [http://openstreetmap.org OpenStreetMap] і NASA Worldwind',
	'slippymap_extname' => 'SlippyMap',
	'slippymap_tagname' => 'slippymap',
	'slippymap_error' => 'Памылка $1: $2',
	'slippymap_errors' => 'Памылкі $1:',
	'slippymap_error_tag_content_given' => 'Тэг  <tt>&lt;$1&gt;</tt> прымае толькі аргумэнты атрыбутаў (&lt;$1 [...]/&gt;), а не ўваходны тэкст (&lt;$1&gt; ... &lt;/$1&gt;)',
	'slippymap_error_missing_arguments' => 'Вы не пазначылі ніякіх атрыбутаў тэга &lt;$1&gt;, глядзіце як выклікаць у [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax інфармацыі па выкарыстаньню]',
	'slippymap_error_missing_attribute_lat' => 'Адсутнічае атрыбут <tt>lat</tt> (для шыраты).',
	'slippymap_error_missing_attribute_lon' => 'Адсутнічае атрыбут <tt>lon</tt> (для даўгаты).',
	'slippymap_error_missing_attribute_zoom' => 'Адсутнічае атрыбут <tt>zoom</tt> (для маштабу).',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'Няслушнае значэньне <tt>$1</tt> для атрыбуту <tt>lat</tt> (шырата), пададзенае значэньне павінна быць слушным лікам.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'Няслушнае значэньне <tt>$1</tt> для атрыбуту <tt>lon</tt> (даўгата), пададзенае значэньне павінна быць слушным лікам.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'Няслушнае значэньне <tt>$1</tt> для атрыбуту <tt>zoom</tt> (маштаб), пададзенае значэньне павінна быць слушным лікам.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'Няслушнае значэньне <tt>$1</tt> для атрыбуту <tt>width</tt> (шырыня), пададзенае значэньне павінна быць слушным лікам.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'Няслушнае значэньне <tt>$1</tt> для атрыбуту <tt>height</tt> (вышыня), пададзенае значэньне павінна быць слушным лікам.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => 'Няслушнае значэньне <tt>$1</tt> для атрыбуту <tt>mode</tt>, слушныя выгляды: $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => 'Няслушнае значэньне <tt>$1</tt> для атрыбуту <tt>layer</tt>, слушныя слаі: $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => 'Няслушнае значэньне <tt>$1</tt> для атрыбуту <tt>marker</tt>, слушныя маркеры: $2.',
	'slippymap_error_unknown_attribute' => 'Невядомы атрыбут <tt>$1</tt>.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'Няслушнае значэньне <tt>$1</tt> для атрыбуту <tt>lat</tt> (шырата). Значэньне шыраты павінна быць паміж -90 і 90 градусамі.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'Няслушнае значэньне <tt>$1</tt> для атрыбуту <tt>lon</tt> (даўгата). Значэньне даўгаты павінна быць паміж -180 і 180 градусамі.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'Няслушнае значэньне <tt>$1</tt> для атрыбуту <tt>zoom</tt> (маштаб). Значэньне маштабу павінна быць паміж $2 і $3.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'Няслушнае значэньне <tt>$1</tt> для атрыбуту <tt>width</tt> (шырыня). Значэньне шырыні павінна быць паміж $2 і $3.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'Няслушнае значэньне <tt>$1</tt> для атрыбуту <tt>height</tt> (вышыня). Значэньне шырыні павінна быць паміж $2 і $3.',
	'slippymap_code' => 'Вікікод для прагляду гэтай мапы:',
	'slippymap_button_code' => 'Атрымаць вікікод',
	'slippymap_resetview' => 'Першапачатковы выгляд',
	'slippymap_clicktoactivate' => 'Націсьніце, каб актывізаваць мапу',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'slippymap_desc' => 'Позволява използването на етикета <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> за показване на OpenLayers slippy карти. Картите са от [http://openstreetmap.org openstreetmap.org]',
	'slippymap_code' => 'Уикикод за тази карта:',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'slippymap_desc' => 'Dodaje oznaku <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> koja omogućuje uklapanje statičkih i dinamičkih mapa. Podržava usluge višestrukih mapa uključujući [http://openstreetmap.org openstreetmap.org] i NASA Worldwind',
	'slippymap_errors' => '$1 greške:',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'Vrijednost <tt>$1</tt> nije valjana za atribut <tt>lat</tt> (geografska širina). Geografske širini moraju biti između -90 i 90 stepeni.',
	'slippymap_code' => 'Wikikod za pogled ove mape:',
	'slippymap_button_code' => 'Preuzmi wikikod',
	'slippymap_resetview' => 'Poništi pogled',
	'slippymap_clicktoactivate' => 'Kliknite za aktivaciju mape',
);

/** Czech (Česky)
 * @author Danny B.
 */
$messages['cs'] = array(
	'slippymap_desc' => 'Umožňuje použití tagu <code><nowiki>&lt;slippymap&gt;</nowiki></code> pro zobrazení posuvné mapy OpenLayers. Mapy pocházejí z [http://openstreetmap.org openstreetmap.org].',
	'slippymap_code' => 'Wikikód tohoto pohledu na mapu:',
	'slippymap_button_code' => 'Zobrazit wikikód',
	'slippymap_resetview' => 'Obnovit zobrazení',
);

/** German (Deutsch)
 * @author Pill
 * @author Sebastian Wallroth
 * @author The Evil IP address
 * @author Umherirrender
 */
$messages['de'] = array(
	'slippymap_desc' => 'Ergänzt ein <tt>&lt;slippymap&gt;</tt>-Tag zum Einbinden von statischen und dynamischen Karten. Unterstützt werden mehrere Kartendienste einschließlich [http://openstreetmap.org OpenStreetMap] und NASA Worldwind',
	'slippymap_error' => '$1-Fehler: $2',
	'slippymap_errors' => '$1-Fehler:',
	'slippymap_error_tag_content_given' => 'Der <tt>&lt;$1&gt;</tt>-Tag kennt nur Attribut-Argumente (&lt;$1 […] /&gt;), keinen Eingabetext (&lt;$1&gt; … &lt;/$1&gt;)',
	'slippymap_error_missing_arguments' => 'Du hast mit dem &lt;$1&gt;-Tag keine Attribute übergeben. In der [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax Syntax-Hilfe] findest du Informationen darüber, wie man ihn aufruft.',
	'slippymap_error_missing_attribute_lat' => 'Fehlendes <tt>lat</tt>-Attribut (für die Breite).',
	'slippymap_error_missing_attribute_lon' => 'Fehlendes <tt>lon</tt>-Attribut (für die Länge).',
	'slippymap_error_missing_attribute_zoom' => 'Fehlendes <tt>zoom</tt>-Attribut (für den Zoomlevel).',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'Der Wert <tt>$1</tt> ist für das <tt>lat</tt>-Attribut (Länge) nicht zulässig. Der Wert muss eine Zahl sein.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'Der Wert <tt>$1</tt> ist für das <tt>lon</tt>-Attribut (Länge) nicht zulässig. Der Wert muss eine Zahl sein.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'Der Wert <tt>$1</tt> ist für das <tt>zoom</tt>-Attribut nicht zulässig. Der Wert muss eine Zahl sein.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'Der Wert <tt>$1</tt> ist für das <tt>width</tt>-Attribut nicht zulässig. Der Wert muss eine Zahl sein.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'Der Wert <tt>$1</tt> ist für das <tt>height</tt>-Attribut nicht zulässig. Der Wert muss eine Zahl sein.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => 'Der Wert <tt>$1</tt> ist für das <tt>mode</tt>-Attribut nicht zulässig. Zulässige Werte sind $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => 'Der Wert <tt>$1</tt> ist für das <tt>layer</tt>-Attribut nicht zulässig. Zulässige Werte sind $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => 'Der Wert <tt>$1</tt> ist für das <tt>marker</tt>-Attribut nicht zulässig. Zulässige Werte sind $2.',
	'slippymap_error_unknown_attribute' => 'Das Attribut <tt>$1</tt> ist unbekannt.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'Der Wert <tt>$1</tt> ist für das <tt>lat</tt>-Attribut (Breite) nicht zulässig. Breiten müssen zwischen −90 und 90 Grad liegen.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'Der Wert <tt>$1</tt> ist für das <tt>lat</tt>-Attribut (Länge) nicht zulässig. Längen müssen zwischen −180 und 180 Grad liegen.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'Der Wert <tt>$1</tt> ist für das <tt>zoom</tt>-Attribut nicht zulässig. Zoom-Level müssen zwischen $2 und $3 liegen.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'Der Wert <tt>$1</tt> ist für das <tt>width</tt>-Attribut nicht zulässig. Der Wert muss zwischen $2 und $3 liegen.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'Der Wert <tt>$1</tt> ist für das <tt>height</tt>-Attribut nicht zulässig. Der Wert muss zwischen $2 und $3 liegen.',
	'slippymap_code' => 'Wikitext für diese Kartenansicht:',
	'slippymap_button_code' => 'Zeige Wikicode',
	'slippymap_resetview' => 'Zurücksetzen',
	'slippymap_clicktoactivate' => 'Klicken, um die Karte zu aktivieren',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'slippymap_desc' => 'Pśidawa toflicku <tt>&lt;slippymap&gt;</tt>, kótaraž zmóžnja zasajźenje statiskich a dynamiskich geografiskich kórtow. Pódpěra někotare kórtowe słužby inkluziwnje [http://openstreetmap.org openstreetMap] a NASA Worldwind',
	'slippymap_error' => 'zmólka $1: $2',
	'slippymap_errors' => 'zmólki $1:',
	'slippymap_error_tag_content_given' => 'Toflicka <tt>&lt;$1&gt;</tt> jano pśiwzejo atributowe argumenty (&lt;$1 [...]/&gt;), žeden zapódawański tekst (&lt;$1&gt; ... &lt;/$1&gt;)',
	'slippymap_error_missing_arguments' => 'Njejsy žedne atributy za toflicku &lt;$1&gt; pódał, glědaj [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax informacije wó wužywanju], aby zgónił, kak se woła.',
	'slippymap_error_missing_attribute_lat' => 'Atribut <tt>lat</tt> (za šyrinu) felujo.',
	'slippymap_error_missing_attribute_lon' => 'Atribut <tt>lon</tt> (za dlininu) felujo.',
	'slippymap_error_missing_attribute_zoom' => 'Atribut <tt>zoom</tt> (za stopjeń skalowanja) felujo.',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'Gódnota <tt>$1</tt> njejo płaśiwa za atribut <tt>lat</tt> (šyrina), pódana gódnota dej płaśiwa licba byś.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'Gódnota <tt>$1</tt> njejo płaśiwa za atribut <tt>lon</tt> (dlinina), pódana gódnota dej płaśiwa licba byś.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'Gódnota <tt>$1</tt> njejo płaśiwa za atribut <tt>zoom</tt>, pódana gódnota dej płaśiwa licba byś.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'Gódnota <tt>$1</tt> njejo płaśiwa za atribut <tt>width</tt>, pódana gódnota dej płaśiwa licba byś.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'Gódnota <tt>$1</tt> njejo płaśiwa za atribut <tt>height</tt>, pódana gódnota dej płaśiwa licba byś.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => 'Gódnota <tt>$1</tt> njejo płaśiwa za atribut <tt>mode</tt>, płaśiwe modusy su $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => 'Gódnota <tt>$1</tt> njejo płaśiwa za atribut <tt>layer</tt>, płaśiwe warsty su $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => 'Gódnota <tt>$1</tt> njejo płaśiwa za atribut <tt>marker</tt>, płaśiwe marki su $2.',
	'slippymap_error_unknown_attribute' => 'Atribut <tt>$1</tt> jo njeznaty.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'Gódnota <tt>$1</tt> njejo płaśiwa za atribut <tt>lat</tt> (šyrina). Šyriny muse mjazy -90 a 90 stopnjow byś.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'Gódnota <tt>$1</tt> njejo płaśiwa za atribut <tt>lon</tt> (dlinina). Dlininy muse mjazy -180 a 180 stopnjow byś.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'Gódnota <tt>$1</tt> njejo płaśiwa za atribut <tt>zoom</tt>. Rowniny skalěrowanja muse mjazy $2 a $3 byś.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'Gódnota <tt>$1</tt> njejo płaśiwa za atribut <tt>width</tt>. Šyrokosći deje mjazy $2 a $3 byś.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'Gódnota <tt>$1</tt> njejo płaśiwa za atribut <tt>height</tt>. Wusokosći deje mjazy $2 a $3 byś.',
	'slippymap_code' => 'Wikikod za toś ten kórtowy naglěd:',
	'slippymap_button_code' => 'Wikikod pokazaś',
	'slippymap_resetview' => 'Naglěd slědk stajiś',
	'slippymap_clicktoactivate' => 'Kliknuś, aby se kórta aktiwěrowała',
);

/** Greek (Ελληνικά)
 * @author Omnipaedista
 * @author ZaDiak
 */
$messages['el'] = array(
	'slippymap_error' => '$1 σφάλμα: $2',
	'slippymap_errors' => '$1 λάθη:',
	'slippymap_button_code' => 'Αποκτήστε βικικώδικα',
	'slippymap_resetview' => 'Επαναφορά προβολής',
	'slippymap_clicktoactivate' => 'Κάντε "κλικ" για να ενεργοποιήσετε το χάρτη',
);

/** Spanish (Español)
 * @author Crazymadlover
 */
$messages['es'] = array(
	'slippymap_desc' => 'Agrega una etiqueta <tt>&lt;slippymap&gt;</tt> la cual permite el empotrado de mapas estáticos y dinámicos. Soporta múltiples servicios de mapas incluyendo [http://openstreetmap.org openstreetmap.org] y NASA Worldwind',
	'slippymap_extname' => 'SlippyMap',
	'slippymap_tagname' => 'slippymap',
	'slippymap_error' => '$1 error: $2',
	'slippymap_errors' => '$1 errores:',
	'slippymap_error_missing_arguments' => 'No proveíste ningún atributo a la etiqueta &lt;$1&gt; , ver [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax información de uso] para cómo llamarlo.',
	'slippymap_error_unknown_attribute' => 'El atributo <tt>$1</tt> es desconocido.',
	'slippymap_code' => 'Wikicode para esta vista de mapa:',
	'slippymap_button_code' => 'Obtener wikicode',
	'slippymap_resetview' => 'Reestablecer vista',
	'slippymap_clicktoactivate' => 'Haga clic para activar mapa',
);

/** Basque (Euskara)
 * @author An13sa
 * @author Kobazulo
 */
$messages['eu'] = array(
	'slippymap_errors' => '$1 errore:',
	'slippymap_button_code' => 'Wikikodea lortu',
	'slippymap_resetview' => 'Bista berrezarri',
);

/** Finnish (Suomi)
 * @author Str4nd
 * @author Vililikku
 */
$messages['fi'] = array(
	'slippymap_desc' => 'Mahdollistaa <tt><nowiki>&lt;slippymap&gt;</nowiki></tt>-elementin käytön OpenLayers slippy map -kartan näyttämiseen. Kartat ovat osoitteesta [http://openstreetmap.org openstreetmap.org].',
	'slippymap_error' => 'Laajennuksen $1 virhe: $2',
	'slippymap_errors' => 'Laajennuksen $1 virheet',
	'slippymap_code' => 'Wikikoodi tälle karttanäkymälle:',
	'slippymap_button_code' => 'Hae wikikoodi',
	'slippymap_resetview' => 'Palauta näkymä',
);

/** French (Français)
 * @author Crochet.david
 * @author Grondin
 * @author PieRRoMaN
 * @author Zetud
 */
$messages['fr'] = array(
	'slippymap_desc' => 'Ajoute une balise <tt>&lt;slippymap&gt;</tt> qui autorise l’affichage d’une carte statique & dynamique. Supportant plusieurs services de cartes tel que [http://openstreetmap.org OpenStreetMap] et NASA Worldwind',
	'slippymap_error' => 'Erreur $1 : $2',
	'slippymap_errors' => 'Erreurs $1 :',
	'slippymap_error_tag_content_given' => 'La balise <tt>&lt;$1&gt;</tt> ne prend que des arguments en attribut (&lt;$1 [...]/&gt;), pas de texte (&lt;$1&gt; ... &lt;/$1&gt;)',
	'slippymap_error_missing_arguments' => 'Vous n’avez fourni aucun attribut de la balise &lt;$1&gt;, voir les [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax informations d’utilisation ] pour savoir comment l’appeler.',
	'slippymap_error_missing_attribute_lat' => 'Attribut <tt>lat</tt> manquant (pour la latitude).',
	'slippymap_error_missing_attribute_lon' => 'Attribut <tt>lon</tt> manquant (pour la longitude).',
	'slippymap_error_missing_attribute_zoom' => 'Attribut <tt>zoom</tt> manquant (pour le niveau de zoom).',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'La valeur de <tt>$1</tt> n’est pas valable pour l’attribut <tt>lat</tt> (latitude), la valeur donnée doit être un nombre valide.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'La valeur de <tt>$1</tt> n’est pas valable pour l’attribut <tt>lon</tt> (longitude), la valeur donnée doit être un nombre valide.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'La valeur de <tt>$1</tt> n’est pas valable pour l’attribut <tt>zoom</tt>, la valeur donnée doit être un nombre valide.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'La valeur de <tt>$1</tt> n’est pas valable pour l’attribut <tt>width</tt>, la valeur donnée doit être un nombre valide.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'La valeur de <tt>$1</tt> n’est pas valable pour l’attribut <tt>height</tt>, la valeur donnée doit être un nombre valide.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => 'La valeur de <tt>$1</tt> n’est pas valable pour l’attribut <tt>mode</tt>, ceux valides sont $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => 'La valeur de <tt>$1</tt> n’est pas valable pour l’attribut <tt>layer</tt>, les couches valides sont $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => 'La valeur de <tt>$1</tt> n’est pas valable pour l’attribut <tt>marker</tt>, ceux valides sont $2.',
	'slippymap_error_unknown_attribute' => 'L’attribut <tt>$1</tt> est inconnue.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'La valeur de <tt>$1</tt> n’est pas valable pour l’attribut <tt>lat</tt> (latitude). Les latitudes doivent être comprise entre -90 et 90 degrés.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'La valeur de <tt>$1</tt> est invalide pour l’attribut <tt>lon</tt> (longitude). Les longitudes doivent être comprises entre -180 et 180 degrés.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'La valeur de <tt>$1</tt> n’est pas valable pour l’attribut <tt>zoom</tt>. Les niveaux de zoom doivent être comprises entre $2 et $3.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'La valeur de <tt>$1</tt> n’est pas valide pour l’attribut <tt>widht</tt>. Les largeurs doivent être comprises entre $2 et $3.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'La valeur de <tt>$1</tt> n’est pas valable pour l’attibut <tt>height</tt>. Les hauteurs doivent être comprises entre $2 et $3.',
	'slippymap_code' => 'Code Wiki pour le visionnement de cette cate :',
	'slippymap_button_code' => 'Obtenir le code wiki',
	'slippymap_resetview' => 'Réinitialiser le visionnement',
	'slippymap_clicktoactivate' => 'Cliquez pour activer la carte',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'slippymap_desc' => 'Engade unha etiqueta <tt>&lt;slippymap&gt;</tt> que permite engadir un mapa estático e dinámico. Soporta múltiples servizos de mapa como [http://openstreetmap.org OpenStreetMap] e NASA Worldwind',
	'slippymap_extname' => 'SlippyMap',
	'slippymap_tagname' => 'slippymap',
	'slippymap_error' => 'Erro na extensión $1: $2',
	'slippymap_errors' => 'Erros na extensión $1:',
	'slippymap_error_tag_content_given' => 'A etiqueta <tt>&lt;$1&gt;</tt> só toma argumentos como atributo (&lt;$1 [...]/&gt;), e non texto de entrada (&lt;$1&gt; ... &lt;/$1&gt;)',
	'slippymap_error_missing_arguments' => 'Non lle proporcionou ningún atributo á etiqueta &lt;$1&gt;, olle a [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax información de uso] para aprender a facer chamadas.',
	'slippymap_error_missing_attribute_lat' => 'Falta o atributo <tt>lat</tt> (para a latitude).',
	'slippymap_error_missing_attribute_lon' => 'Falta o atributo <tt>lon</tt> (para a lonxitude).',
	'slippymap_error_missing_attribute_zoom' => 'Falta o atributo <tt>zoom</tt> (para o nivel de zoom).',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'O valor <tt>$1</tt> non é válido para o atributo <tt>lat</tt> (latitude), o valor dado debe ser un número válido.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'O valor <tt>$1</tt> non é válido para o atributo <tt>lon</tt> (lonxitude), o valor dado debe ser un número válido.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'O valor <tt>$1</tt> non é válido para o atributo <tt>zoom</tt>, o valor dado debe ser un número válido.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'O valor <tt>$1</tt> non é válido para o atributo <tt>width</tt> (largo), o valor dado debe ser un número válido.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'O valor <tt>$1</tt> non é válido para o atributo <tt>height</tt> (altura), o valor dado debe ser un número válido.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => 'O valor <tt>$1</tt> non é válido para o atributo <tt>mode</tt>, os modos válidos son $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => 'O valor <tt>$1</tt> non é válido para o atributo <tt>layer</tt>, as capas válidas son $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => 'O valor <tt>$1</tt> non é válido para o atributo <tt>marker</tt>, os marcadores válidos son $2.',
	'slippymap_error_unknown_attribute' => 'Descoñécese o atributo <tt>$1</tt>.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'O valor <tt>$1</tt> non é válido para o atributo <tt>lat</tt> (latitude). As latitudes deben estar entre os -90 e 90 graos.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'O valor <tt>$1</tt> non é válido para o atributo <tt>lon</tt> (lonxitude). As lonxitudes deben estar entre os -180 e 180 graos.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'O valor <tt>$1</tt> non é válido para o atributo <tt>zoom</tt>. Os niveis de zoom deben estar entre $2 e $3.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'O valor <tt>$1</tt> non é válido para o atributo <tt>width</tt> (largo). Os largos deben estar entre $2 e $3.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'O valor <tt>$1</tt> non é válido para o atributo <tt>height</tt> (altura). As alturas deben estar entre $2 e $3.',
	'slippymap_code' => 'Código wiki para o visionado deste mapa:',
	'slippymap_button_code' => 'Obter o código wiki',
	'slippymap_resetview' => 'Axustar a vista',
	'slippymap_clicktoactivate' => 'Prema para activar o mapa',
);

/** Ancient Greek (Ἀρχαία ἑλληνικὴ)
 * @author Omnipaedista
 */
$messages['grc'] = array(
	'slippymap_error' => '$1 σφάλμα: $2',
	'slippymap_errors' => '$1 σφάλματα:',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'slippymap_desc' => 'Fiegt e <tt>&lt;slippymap&gt;</tt>-Tag zue, wu s megli macht, statistischi un dynamischi Charte yyzbinde. Unterstitzt vyyli Charte-Service, au [http://openstreetmap.org OpenStreetMap] un NASA Worldwind',
	'slippymap_extname' => 'SlippyMap',
	'slippymap_tagname' => 'slippymap',
	'slippymap_error' => '$1 Fähler: $2',
	'slippymap_errors' => '$1 Fähler:',
	'slippymap_error_tag_content_given' => 'Dr <tt>&lt;$1&gt;</tt>-Tag nimmt nume Eigeschafte-Argumänt (&lt;$1 [...]/&gt;), kei Yygabe-Tekscht (&lt;$1&gt; ... &lt;/$1&gt;)',
	'slippymap_error_missing_arguments' => 'Du hesch keini Eigeschafte zum &lt;$1&gt;-Tag zuegfiegt, lueg [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax Gebruchsinformatione] wie Du s chasch ufruefe.',
	'slippymap_error_missing_attribute_lat' => '<tt>lat</tt>-Eigeschaft fählt (fir d Breiti).',
	'slippymap_error_missing_attribute_lon' => '<tt>lon</tt>-Eigeschaft fählt (fir d Lengi).',
	'slippymap_error_missing_attribute_zoom' => '<tt>zoom</tt>-Eigeschaft fählt (fir s Zoomlevel).',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'Dr Wärt <tt>$1</tt> isch nit giltig fir d <tt>lat</tt>-Eigeschaft (Breiti), dr Wärt muess e giltigi Zahl syy.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'Dr Wärt <tt>$1</tt> isch nit giltig fir d <tt>lon</tt>-Eigeschaft (Lengi), dr Wärt muess e giltigi Zahl syy.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'Dr Wärt <tt>$1</tt> isch nit giltig fir d <tt>zoom</tt>-Eigeschaft, dr Wärt muess e giltigi Zahl syy.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'Dr Wärt <tt>$1</tt> isch nit giltig fir d <tt>width</tt>-Eigeschaft, dr Wärt muess e giltigi Zahl syy.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'Dr Wärt <tt>$1</tt> isch nit giltig fir d <tt>height</tt>-Eigeschaft, dr Wärt muess e giltigi Zahl syy.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => 'Dr Wärt <tt>$1</tt> isch nit giltig fir d <tt>mode</tt>-Eigeschaft, giltigi Modi sin $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => 'Dr Wärt <tt>$1</tt> isch nit giltig fir d <tt>layer</tt>-Eigeschaft, giltigi Layer sin $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => 'Dr Wärt <tt>$1</tt> isch nit giltig fir d <tt>marker</tt>-Eigeschaft, giltigi Marker sin $2.',
	'slippymap_error_unknown_attribute' => 'D Eigeschaft <tt>$1</tt> isch nit bekannt.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'Dr Wärt <tt>$1</tt> isch nit giltig fir <tt>lat</tt>-Eigeschaft (Breiti). Breitine mien zwische -90 un 90 Grad syy.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'Dr Wärt <tt>$1</tt> isch nit giltig fir <tt>lon</tt>-Eigeschaft (Lenig). Lengine mien zwische -180 un 180 Grad syy.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'Dr Wärt <tt>$1</tt> isch nit giltig fir <tt>zoom</tt>-Eigeschaft. Zoomlevel mien zwische $2 un $3 syy.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'Dr Wärt <tt>$1</tt> isch nit giltig fir <tt>width</tt>-Eigeschaft. Breitine mien zwische $2 un $3 syy.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'Dr Wärt <tt>$1</tt> isch nit giltig fir <tt>height</tt>-Eigeschaft. Hechine mien zwische $2 un $3 syy.',
	'slippymap_code' => 'Wikitäxt fir die Chartenaasicht:',
	'slippymap_button_code' => 'Zeig Wikicode',
	'slippymap_resetview' => 'Zruggsetze',
	'slippymap_clicktoactivate' => 'Zum Aktiviere vu dr Charte drucke',
);

/** Hebrew (עברית)
 * @author YaronSh
 */
$messages['he'] = array(
	'slippymap_desc' => 'מתן האפשרות לשימוש בתגית <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> המאפשרת הטמעת מפות סטאטיות ודינאמיות. קיימת תמיכה במספר שרותי מפות כולל [http://openstreetmap.org OpenStreetMap]ו־Worldwind של NASA',
	'slippymap_errors' => '$1 שגיאות:',
	'slippymap_code' => 'קוד הוויקי להצגת מפה זו:',
	'slippymap_button_code' => 'איחזור קוד הוויקי',
	'slippymap_resetview' => 'איפוס התצוגה',
	'slippymap_clicktoactivate' => 'לחצו כדי להפעיל את המפה',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'slippymap_desc' => 'Přidawa tafličku <tt>&lt;slippymap&gt;</tt>, kotraž zmóžnja zasadźenje statiskich a dynamiskich kartow. Podpěruje wjacore kartowe słužby inkluziwnje [http://openstreetmap.org OpenStreetMap] a NASA Worldwind',
	'slippymap_extname' => 'SlippyMap',
	'slippymap_tagname' => 'slippymap',
	'slippymap_error' => 'Zmylk $1: $2',
	'slippymap_errors' => 'Zmylki $1:',
	'slippymap_error_tag_content_given' => 'Taflička <tt>&lt;$1&gt;</tt> jenož argumenty (&lt;$1 [...]/&gt;) akceptuje, žadyn zapodatny tekst (&lt;$1&gt; ... &lt;/$1&gt;)',
	'slippymap_error_missing_arguments' => 'Njejsy atributy za tafličku &lt;$1&gt; podał, hlej [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax informacije wo wužiwanju], zo by zhonił, kak dadźa so zawołać.',
	'slippymap_error_missing_attribute_lat' => 'Atribut <tt>lat</tt> (za šěrinu) pobrachuje.',
	'slippymap_error_missing_attribute_lon' => 'Atribut <tt>lon</tt> (za geografisku dołhosć) pobrachuje.',
	'slippymap_error_missing_attribute_zoom' => 'Atribut <tt>zoom</tt> (za stopjeń skalowanja) pobrachuje.',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'Hódnota <tt>$1</tt> za atribut <tt>lat</tt> (šěrina) płaćiwa njeje, podata hódnota dyrbi płaćiwa ličba być.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'Hódnota <tt>$1</tt> za atribut <tt>lon</tt> (geografiska dołhosć) płaćiwa njeje, podata hódnota dyrbi płaćiwa ličba być.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'Hódnota <tt>$1</tt> za atribut <tt>zoom</tt> płaćiwa njeje, podata hódnota dyrbi płaćiwa ličba być.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'Hódnota <tt>$1</tt> za atribut <tt>width</tt> płaćiwa njeje, podata hódnota dyrbi płaćiwa ličba być.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'Hódnota <tt>$1</tt> za atribut <tt>height</tt> płaćiwa njeje, podata hódnota dyrbi płaćiwa ličba być.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => 'Hódnota <tt>$1</tt> za atribut <tt>mode</tt> płaćiwa njeje, płaćiwe modusy su $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => 'Hódnota <tt>$1</tt> za atribut <tt>layer</tt> płaćiwa njeje, płaćiwe woršty su $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => 'Hódnota <tt>$1</tt> za atribut <tt>marker</tt> płaćiwa njeje, płaćiwe marki su $2.',
	'slippymap_error_unknown_attribute' => 'Atribut <tt>$1</tt> je njeznaty.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'Hódnota <tt>$1</tt> za atribut <tt>lat</tt> (šěrina) płaćiwa njeje. Šěriny dyrbja mjez -90 a 90 stopnjow być.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'Hódnota <tt>$1</tt> za atribut <tt>lon</tt> (geografiska dołhosć) płaćiwa njeje. Geografiske dołhosće dyrbja mjez -180 a 180 stopnjow być.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'Hódnota <tt>$1</tt> za atribut <tt>zoom</tt> płaćiwa njeje. Skalowanske stopjenja dyrbja mjez $2 a $3 być.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'Hódnota <tt>$1</tt> za atribut <tt>width</tt> płaćiwa njeje. Šěrokosće dyrbja mjez $2 a $3 być.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'Hódnota <tt>$1</tt> za atribut <tt>height</tt> płaćiwa njeje. Wysokosće dyrbja mjez $2 a $3 być.',
	'slippymap_code' => 'Wikikod za tutón kartowy napohlad:',
	'slippymap_button_code' => 'Wikikod pokazać',
	'slippymap_resetview' => 'Napohlad wróćo stajić',
	'slippymap_clicktoactivate' => 'Kliknyć, zo by so karta aktiwizowała',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'slippymap_desc' => 'Adde un etiquetta <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> que permitte incastrar cartas static e dynamic. Supporta multiple servicios de cartas includente [http://openstreetmap.org OpenStreetMap] e NASA Worldwind',
	'slippymap_extname' => 'Carta glissante',
	'slippymap_tagname' => 'cartaglissante',
	'slippymap_error' => '$1 error: $2',
	'slippymap_errors' => '$1 errores:',
	'slippymap_error_tag_content_given' => 'Le etiquetta <tt>&lt;$1&gt;</tt> accepta como parametros solmente attributos (&lt;$1 [...]/&gt;), non texto de entrata (&lt;$1&gt; ... &lt;/$1&gt;)',
	'slippymap_error_missing_arguments' => 'Tu non forniva alcun attributo al etiquetta &lt;$1&gt;, vide [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax usage information] pro leger como appellar lo.',
	'slippymap_error_missing_attribute_lat' => 'Le attributo <tt>lat</tt> manca (pro le latitude).',
	'slippymap_error_missing_attribute_lon' => 'Le attributo <tt>lon</tt> manca (pro le longitude).',
	'slippymap_error_missing_attribute_zoom' => 'Le attributo <tt>zoom</tt> manca (pro le nivello de zoom).',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'Le valor <tt>$1</tt> non es valide pro le attributo <tt>lat</tt> (latitude), le valor date debe esser un numero valide.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'Le valor <tt>$1</tt> non es valide pro le attributo <tt>lon</tt> (longitude), le valor date debe esser un numero valide.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'Le valor <tt>$1</tt> non es valide pro le attributo <tt>zoom</tt>, le valor date debe esser un numero valide.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'Le valor <tt>$1</tt> non es valide pro le attributo <tt>width</tt>, le valor date debe esser un numero valide.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'Le valor <tt>$1</tt> non es valide pro le attributo <tt>height</tt>, le valor date debe esser un numero valide.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => 'Le valor <tt>$1</tt> non es valide pro le attributo <tt>mode</tt>, le modos valide es $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => 'Le valor <tt>$1</tt> non es valide pro le attributo <tt>layer</tt>, le stratos valide es $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => 'Le valor <tt>$1</tt> non es valide pro le attributo <tt>marker</tt>, le marcatores valide es $2.',
	'slippymap_error_unknown_attribute' => 'Le attributo <tt>$1</tt> es incognite.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'Le valor <tt>$1</tt> non es valide pro le attributo <tt>lat</tt> (latitude). Le latitudes debe esser inter -90 e 90 grados.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'Le valor <tt>$1</tt> non es valide pro le attributo <tt>lon</tt> (longitude). Le longitudes debe esser inter -180 e 180 grados.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'Le valor <tt>$1</tt> non es valide pro le attributo <tt>zoom</tt>. Le nivellos de zoom debe esser inter $2 e $3.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'Le valor <tt>$1</tt> non es valide pro le attributo <tt>width</tt>. Le nivellos de largor debe esser inter $2 e $3.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'Le valor <tt>$1</tt> non es valide pro le attributo <tt>height</tt>. Le nivellos de altor debe esser inter $2 e $3.',
	'slippymap_code' => 'Codice Wiki pro iste vista del carta:',
	'slippymap_button_code' => 'Obtener codice wiki',
	'slippymap_resetview' => 'Reinitialisar vista',
	'slippymap_clicktoactivate' => 'Clicca pro activar le carta',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 */
$messages['id'] = array(
	'slippymap_error' => 'Galat $1: $2',
	'slippymap_errors' => 'Galat $1:',
);

/** Italian (Italiano)
 * @author Darth Kule
 */
$messages['it'] = array(
	'slippymap_desc' => 'Aggiunge il tag <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> che permette di incorporare mappe statiche e dinamiche. Supporta diverse mappe, comprese quelle di [http://openstreetmap.org OpenStreetMap] e NASA Worldwind',
	'slippymap_code' => 'Codice wiki per visualizzare questa mappa:',
	'slippymap_button_code' => 'Ottieni codice wiki',
	'slippymap_resetview' => 'Reimposta visuale',
);

/** Japanese (日本語)
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'slippymap_desc' => '静的または動的な地図を埋め込めるようにする <tt>&lt;slippymap&gt;</tt> タグを追加する。[http://openstreetmap.org OpenStreetMap] や NASA World Wind を含む、複数の地図サービスに対応する',
	'slippymap_error' => '$1 のエラー: $2',
	'slippymap_errors' => '$1 のエラー:',
	'slippymap_error_tag_content_given' => '<tt>&lt;$1&gt;</tt> タグは引数として属性を受け付けるのみで (<$1 [...]/>)、テキストは受け付けません (&lt;$1&gt; ... &lt;/$1&gt;)',
	'slippymap_error_missing_arguments' => '&lt;$1&gt; タグに属性が1つも与えられていません。このタグの[http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax 使い方]を参照してください。',
	'slippymap_error_missing_attribute_lat' => '<tt>lat</tt> 属性（緯度）がありません。',
	'slippymap_error_missing_attribute_lon' => '<tt>lon</tt> 属性（経度）がありません。',
	'slippymap_error_missing_attribute_zoom' => '<tt>zoom</tt> 属性（拡大度）がありません。',
	'slippymap_error_invalid_attribute_lat_value_nan' => '値「<tt>$1</tt>」は <tt>lat</tt> 属性（緯度）として妥当な値ではありません。妥当な数値を指定してください。',
	'slippymap_error_invalid_attribute_lon_value_nan' => '値「<tt>$1</tt>」は <tt>lon</tt> 属性（経度）として妥当な値ではありません。妥当な数値を指定してください。',
	'slippymap_error_invalid_attribute_zoom_value_nan' => '値「<tt>$1</tt>」は <tt>zoom</tt> 属性として妥当な値ではありません。妥当な数値を指定してください。',
	'slippymap_error_invalid_attribute_width_value_nan' => '値「<tt>$1</tt>」は <tt>width</tt> 属性として妥当な値ではありません。妥当な数値を指定してください。',
	'slippymap_error_invalid_attribute_height_value_nan' => '値「<tt>$1</tt>」は <tt>height</tt> 属性として妥当な値ではありません。妥当な数値を指定してください。',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => '値「<tt>$1</tt>」は <tt>mode</tt> 属性として有効な値ではありません。有効なモードは $2 です。',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => '値「<tt>$1</tt>」は <tt>layer</tt> 属性として有効な値ではありません。有効なレイヤーは $2 です。',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => '値「<tt>$1</tt>」は <tt>marker</tt> 属性として有効な値ではありません。有効なマーカーは $2 です。',
	'slippymap_error_unknown_attribute' => '属性 <tt>$1</tt> は不明です。',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => '値「<tt>$1</tt>」は <tt>lat</tt> 属性（緯度）として妥当な値ではありません。緯度は-90度から90度の間でなければなりません。',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => '値「<tt>$1</tt>」は <tt>lon</tt> 属性（経度）として妥当な値ではありません。経度は-180度から180度の間でなければなりません。',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => '値「<tt>$1</tt>」は <tt>zoom</tt> 属性（拡大度）として妥当な値ではありません。拡大度は$2から$3の間でなければなりません。',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => '値「<tt>$1</tt>」は <tt>width</tt> 属性として妥当な値ではありません。横幅は$2から$3の間でなければなりません。',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => '値「<tt>$1</tt>」は <tt>height</tt> 属性として妥当な値ではありません。縦幅は$2から$3の間でなければなりません。',
	'slippymap_code' => 'この地図表示用のウィキマークアップ:',
	'slippymap_button_code' => 'ウィキマークアップを取得',
	'slippymap_resetview' => '表示を更新',
	'slippymap_clicktoactivate' => 'クリックして地図をアクティブにする',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'slippymap_desc' => 'Deit dä Befääl <tt>&lt;slippymap&gt;</tt> em Wiki dobei, öm en Landkaat faßt udder bewääschlesh aanzezeije. Deiht met ongerscheidlijje ßöövere för Kaate fungxjeneehre, doh dronger di fun <i lang="en">[http://openstreetmap.org OpenStreetMap]</i> un <i lang="en">NASA Worldwind</i>.',
	'slippymap_extname' => 'SlippyMap',
	'slippymap_tagname' => 'slippymap',
	'slippymap_error' => 'Fähler en $1: $2',
	'slippymap_errors' => 'Fähler en $1:',
	'slippymap_error_tag_content_given' => 'Dä Befähl <tt>&lt;$1&gt;</tt> nimmp bloß Parrameeter met Name un Wääte aan (<tt>&lt;$1 [</tt> … <tt>] /&gt;</tt>), ävver keine Täx dozwesche (<tt>&lt;$1&gt;</tt> … <tt>&lt;/$1&gt;</tt>)',
	'slippymap_error_missing_arguments' => 'Do häß keine Parrameeter aan dä Befähl <tt>&lt;$1&gt;</tt> jejovve.
Loor Der aan, wi dä [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax jebruch wääde] moß.',
	'slippymap_error_missing_attribute_lat' => 'De Eijeschaff <tt>lon</tt> för de Breed om Jloobus fäählt.',
	'slippymap_error_missing_attribute_lon' => 'De Eijeschaff <tt>lon</tt> för de Läng om Jloobus fäählt.',
	'slippymap_error_missing_attribute_zoom' => 'De Eijeschaff <tt>zoom</tt> för em Zoom sing Nivoh fäählt.',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>lat</tt> jeiht nit. De Breed om Jloobus moß met en reeschtijje Zahl aanjejovve wääde.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>lon</tt> jeiht nit. De Läng om Jloobus moß met en reeschtijje Zahl aanjejovve wääde.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>zoom</tt> jeiht nit. Et Nivvoh vum Zoom moß met en reeschtijje Zahl aanjejovve wääde.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>width</tt> jeiht nit. De Breede of Wickde moß met en reeschtijje Zahl aanjejovve wääde.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>height</tt> jeiht nit. De Hühde moß met en reeschtijje Zahl aanjejovve wääde.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>mode</tt> jeiht nit. De jöltijje Aate sin $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>Layer</tt> jeiht nit. De jöltijje Nivvohs sin $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>marker</tt> jeiht nit. De jöltijje Makeerunge sin: $2.',
	'slippymap_error_unknown_attribute' => 'En Eijeschaff <tt>$1</tt> känne mer nit.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>lat</tt> jeiht nit. De Breed om Jlohbus moß zwesche -90 un +90 Jrahd lijje.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>lon</tt> jeiht nit. De Läng om Jlohbus moß zwesche -180 un +180 Jrahd lijje.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>zoom</tt> jeiht nit. Et Nivoh vum Zoom moß zwesche $2 un $3 lijje.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>width</tt> jeiht nit. De Breed of Wickde moß zwesche $2 un $3 lijje.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>height</tt> jeiht nit. De Hühde moß zwesche $2 un $3 lijje.',
	'slippymap_code' => 'Dä Wiki-Kood för di Kaate-Aansesh es:',
	'slippymap_button_code' => 'Donn dä Wiki-Kood zeije',
	'slippymap_resetview' => 'Aansesh zeröcksetze',
	'slippymap_clicktoactivate' => 'Don klecke, öm di Kaat aanzemaache',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Les Meloures
 * @author Robby
 */
$messages['lb'] = array(
	'slippymap_desc' => 'Setzt eng Markéierung <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> derbäi déi et erlaabt statesch an dynamesch Kaarten  anzebannen. Ënnerstëtzt verschidde Kaarte-Servicer wéi [http://openstreetmap.org openstreetmap.org OpenStreetMap] an NASA Worldwind',
	'slippymap_error' => '$1-Feeler: $2',
	'slippymap_errors' => '$1-Feeler:',
	'slippymap_error_missing_attribute_lat' => 'Attribut <tt>lat</tt> ((fir déi geographesch Breet)) feelt.',
	'slippymap_error_missing_attribute_lon' => 'Attribut <tt>lon</tt> (fir déi geographesch Längt) feelt.',
	'slippymap_error_unknown_attribute' => 'Den Attribut <tt>$1</tt> ass onbekannt.',
	'slippymap_code' => 'Wikicode fir dës Kaart ze kucken:',
	'slippymap_button_code' => 'Wikicode weisen',
	'slippymap_resetview' => 'Zrécksetzen',
	'slippymap_clicktoactivate' => "Klickt fir d'Kaart z'aktivéieren",
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'slippymap_desc' => 'Voegt de tag <tt>&lt;slippymap&gt;</tt> toe waarmee statische en dynamische kaarten toegevoegd kunnen worden.
Biedt ondersteuning voor meerdere kaartdiensten zoals [http://openstreetmap.org OpenStreetMap] en NASA Worldwind',
	'slippymap_extname' => 'SlippyMap',
	'slippymap_tagname' => 'slippymap',
	'slippymap_error' => '$1-fout: $2',
	'slippymap_errors' => '$1-fouten:',
	'slippymap_error_tag_content_given' => 'De tag <tt>&lt;$1&gt;</tt> accepteert alleen attribuutargumenten (&lt;$1 [...]/&gt;), geen tekstinvoer (&lt;$1&gt; ... &lt;/$1&gt;)',
	'slippymap_error_missing_arguments' => 'U hebt niet geen attributen opgegeven voor de tag &lt;$1&gt;.
Zie de [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax gebruikershandleiding] voor meer informatie.',
	'slippymap_error_missing_attribute_lat' => 'Het attribuut <tt>lat</tt> mist (voor de breedtegraad)',
	'slippymap_error_missing_attribute_lon' => 'Het attribuut <tt>lon</tt> mist (voor de lengtegraad)',
	'slippymap_error_missing_attribute_zoom' => 'Het attribuut <tt>zoom</tt> mist (voor het zoomniveau)',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'De waarde <tt>$1</tt> is ongeldig voor het attribuut <tt>lat</tt> (breedtegraad).
De opgegeven waarde moet een geldig getal zijn.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'De waarde <tt>$1</tt> is ongeldig voor het attribuut <tt>lon</tt> (lengtegraad).
De opgegeven waarde moet een geldig getal zijn.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'De waarde <tt>$1</tt> is ongeldig voor het attribuut <tt>zoom</tt>.
De opgegeven waarde moet een geldig getal zijn.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'De waarde <tt>$1</tt> is ongeldig voor het attribuut <tt>width</tt> (kaartbreedte).
De opgegeven waarde moet een geldig getal zijn.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'De waarde <tt>$1</tt> is ongeldig voor het attribuut <tt>height</tt> (kaarthoogte).
De opgegeven waarde moet een geldig getal zijn.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => 'De waarde <tt>$1</tt> is ongeldig voor het attribuut <tt>mode</tt>.
Geldige waarden zijn $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => 'De waarde <tt>$1</tt> is ongeldig voor het attribuut <tt>layer</tt>.
Geldige lagen zijn $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => 'De waarde <tt>$1</tt> is ongeldig voor het attribuut <tt>marker</tt>.
Geldige markers zijn $2.',
	'slippymap_error_unknown_attribute' => 'Het attribuut <tt>$1</tt> is geen bekend attribuut.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'De waarde <tt>$1</tt> is ongeldig voor het attribuut <tt>lat</tt> (breedtegraad).
Lengtegraden moeten tussen -90 en 90 graden liggen.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'De waarde <tt>$1</tt> is ongeldig voor het attribuut <tt>lon</tt> (lengtegraad).
Breedtegraden moeten tussen -180 en 180 graden liggen.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'De waarde <tt>$1</tt> is ongeldig voor het attribuut <tt>zoom</tt>.
Het zoomniveau moet tussen $2 en $3 liggen.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'De waarde <tt>$1</tt> is ongeldig voor het attribuut <tt>width</tt>.
De kaartbreedte moet tussen $2 en $3 liggen.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'De waarde <tt>$1</tt> is ongeldig voor het attribuut <tt>height</tt>.
De kaarthoogte moet tussen $2 en $3 liggen.',
	'slippymap_code' => 'Wikicode voor deze kaart:',
	'slippymap_button_code' => 'Wikicode',
	'slippymap_resetview' => 'Terug',
	'slippymap_clicktoactivate' => 'Klik om de kaart te activeren',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Harald Khan
 */
$messages['nn'] = array(
	'slippymap_desc' => 'Tillét bruk av merket <tt>&lt;slippymap&gt;</tt> for å syna eit «slippy map» frå OpenLayers. Karti kjem frå [http://openstreetmap.org openstreetmap.org]',
	'slippymap_code' => 'Wikikode for denne kartvisingi:',
	'slippymap_button_code' => 'Hent wikikode',
	'slippymap_resetview' => 'Attendestill vising',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'slippymap_desc' => 'Tillater bruk av taggen <tt>&lt;slippymap&gt;</tt> for å vise et «slippy map» fra OpenLayers. Kartene kommer fra [http://openstreetmap.org openstreetmap.org]',
	'slippymap_code' => 'Wikikode for denne kartvisningen:',
	'slippymap_button_code' => 'Hent wikikode',
	'slippymap_resetview' => 'Tilbakestill visning',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'slippymap_desc' => "Apond una balisa <tt>&lt;slippymap&gt;</tt> qu'autoriza l’afichatge d’una mapa estatica & dinamica. Suportant mai d'un servici de mapas coma [http://openstreetmap.org OpenStreetMap] e NASA Worldwind",
	'slippymap_error' => 'Error $1 : $2',
	'slippymap_errors' => 'Errors $1 :',
	'slippymap_error_tag_content_given' => "La balisa <tt>&lt;$1&gt;</tt> pren pas que d'arguments en atribut (&lt;$1 [...]/&gt;), pas de tèxte (&lt;$1&gt; ... &lt;/$1&gt;)",
	'slippymap_error_missing_arguments' => "Avètz pas provesit cap d'atributs de la balisa &lt;$1&gt;, vejatz las [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax informacions d’utilizacion ] per saber cossí l’apelar.",
	'slippymap_error_missing_attribute_lat' => 'Atribut <tt>lat</tt> mancant (per la latitud).',
	'slippymap_error_missing_attribute_lon' => 'Atribut <tt>lon</tt> mancant (per la longitud).',
	'slippymap_error_missing_attribute_zoom' => 'Atribut <tt>zoom</tt> mancant (pel nivèl de zoom).',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'La valor de <tt>$1</tt> es pas valabla per l’atribut <tt>lat</tt> (latitud), la valor balhada deu èsser un nombre valid.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'La valor de <tt>$1</tt> es pas valabla per l’atribut <tt>lon</tt> (longitud), la valor balhada deu èsser un nombre valid.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'La valor de <tt>$1</tt> es pas valabla per l’atribut <tt>zoom</tt>, la valor balhada deu èsser un nombre valid.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'La valor de <tt>$1</tt> es pas valabla per l’atribut <tt>width</tt>, la valor balhada deu èsser un nombre valid.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'La valor de <tt>$1</tt> es pas valabla per l’atribut <tt>height</tt>, la valor balhada deu èsser un nombre valid.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => 'La valor de <tt>$1</tt> es pas valabla per l’atribut <tt>mode</tt>, los valids son $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => 'La valor de <tt>$1</tt> es pas valabla per l’atribut <tt>layer</tt>, los jaces valids son $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => 'La valor de <tt>$1</tt> es pas valabla per l’atribut <tt>marker</tt>, los valids son $2.',
	'slippymap_error_unknown_attribute' => 'L’atribut <tt>$1</tt> es desconegut.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'La valor de <tt>$1</tt> es pas valabla per l’atribut <tt>lat</tt> (latitud). Las latituds devon èsser compresas entre -90 e 90 graus.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'La valor de <tt>$1</tt> es pas valabla per l’atribut <tt>lon</tt> (longitud). Las longituds devon èsser compresas entre -180 e 180 graus.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'La valor de <tt>$1</tt> es pas valabla per l’atribut <tt>zoom</tt>. Los nivèls de zoom devon èsser compresas entre $2 e $3.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'La valor de <tt>$1</tt> es pas valida per l’atribut <tt>widht</tt>. Las largors devon èsser compresas entre $2 e $3.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'La valor de <tt>$1</tt> es pas valabla per l’atribut <tt>height</tt>. Las nautors devon èsser compresas entre $2 e $3.',
	'slippymap_code' => "Còde Wiki pel visionament d'aquesta mapa :",
	'slippymap_button_code' => 'Obténer lo còde wiki',
	'slippymap_resetview' => 'Tornar inicializar lo visionament',
	'slippymap_clicktoactivate' => 'Clicatz per activar la mapa',
);

/** Polish (Polski)
 * @author Leinad
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'slippymap_desc' => 'Pozwala na korzystanie ze znacznika <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> powodującego wyświetlenie statycznych oraz dynamicznych map. Wspierane są różne serwisy z mapami, w tym m.in. [http://openstreetmap.org OpenStreetMap] i NASA Worldwind.',
	'slippymap_code' => 'Kod wiki dla tego widoku mapy:',
	'slippymap_button_code' => 'Pobierz kod wiki',
	'slippymap_resetview' => 'Zresetuj widok',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'slippymap_desc' => 'Adiciona uma marca <tt>&lt;slippymap&gt;</tt> que permite a incorporação de mapas estáticos & dinâmicos. Suporta múltiplos serviços de mapas incluindo [http://openstreetmap.org OpenStreetMap] e NASA Worldwind',
	'slippymap_code' => 'Código wiki para esta vista do mapa:',
	'slippymap_button_code' => 'Buscar código wiki',
	'slippymap_resetview' => 'Repor vista',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Eduardo.mps
 * @author Heldergeovane
 */
$messages['pt-br'] = array(
	'slippymap_desc' => 'Permite o uso da marca <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> para apresentar um mapa corrediço OpenLayers. Os mapas provêm de [http://openstreetmap.org openstreetmap.org]',
	'slippymap_error' => '$1 erro: $2',
	'slippymap_errors' => '$1 erros:',
	'slippymap_code' => 'Código wiki para esta vista do mapa:',
	'slippymap_button_code' => 'Buscar código wiki',
	'slippymap_resetview' => 'Reiniciar vista',
	'slippymap_clicktoactivate' => 'Clique para ativar o mapa',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 */
$messages['ro'] = array(
	'slippymap_error_missing_attribute_lat' => 'Atributul <tt>lat</tt> lipseşte (pentru latitudine).',
	'slippymap_error_missing_attribute_lon' => 'Atributul <tt>lon</tt> lipseşte (pentru longitudine).',
	'slippymap_error_missing_attribute_zoom' => 'Atributul <tt>zoom</tt> lipseşte (pentru nivelul de zoom).',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'Valoarea <tt>$1</tt> nu este corectă pentru atributul <tt>lat</tt>, valoarea dată trebuie să fie un număr valid.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'Valoarea <tt>$1</tt> nu este corectă pentru atributul <tt>lon</tt>, valoarea dată trebuie să fie un număr valid.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'Valoarea <tt>$1</tt> nu este corectă pentru atributul <tt>zoom</tt>, valoarea dată trebuie să fie un număr valid.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'Valoarea <tt>$1</tt> nu este corectă pentru atributul <tt>width</tt>, valoarea dată trebuie să fie un număr valid.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'Valoarea <tt>$1</tt> nu este corectă pentru atributul <tt>height</tt>, valoarea dată trebuie să fie un număr valid.',
	'slippymap_error_unknown_attribute' => 'Atributul <tt>$1</tt> nu este cunoscut.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'Valoarea <tt>$1</tt> nu este corectă pentru atributul <tt>lat</tt>. Latitudinea trebuie să fie între -90 şi 90 grade.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'Valoarea <tt>$1</tt> nu este corectă pentru atributul <tt>lon</tt>. Longitudinea trebuie să fie între -180 şi 180 grade.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'Valoarea <tt>$1</tt> nu este corectă pentru atributul <tt>zoom</tt>. Nivelul de zoom trebuie să fie între $2 şi $3.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'Valoarea <tt>$1</tt> nu este corectă pentru atributul <tt>width</tt>. Lăţimea trebuie să fie între $2 şi $3.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'Valoarea <tt>$1</tt> nu este corectă pentru atributul <tt>height</tt>. Înălţimea trebuie să fie între $2 şi $3.',
	'slippymap_clicktoactivate' => 'Click pentru activarea hărţii',
);

/** Russian (Русский)
 * @author Ferrer
 * @author Lockal
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'slippymap_desc' => 'Добавляет тег <tt><nowiki>&lt;slippymap&gt;</nowiki></tt>, позволяющий включение статических и динамических карт. Поддерживаются различные сервисы карт, включая [http://openstreetmap.org openstreetmap.org] и NASA Worldwind',
	'slippymap_extname' => 'SlippyMap',
	'slippymap_tagname' => 'slippymap',
	'slippymap_error' => 'Ошибка $1: $2',
	'slippymap_errors' => 'Ошибки $1:',
	'slippymap_error_tag_content_given' => 'Тег <tt>&lt;$1&gt;</tt> принимает только аргументы атрибутов (&lt;$1 [...]/&gt;), не вводимый текст (&lt;$1&gt; ... &lt;/$1&gt;)',
	'slippymap_error_missing_arguments' => 'Вы не указали атрибуты для тега &lt;$1&gt;, подробнее см. [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax информацию об использовании].',
	'slippymap_error_missing_attribute_lat' => 'Отсутствует атрибут <tt>lat</tt> (для широты).',
	'slippymap_error_missing_attribute_lon' => 'Отсутствует атрибут <tt>lon</tt> (для долготы).',
	'slippymap_error_missing_attribute_zoom' => 'Отсутствует атрибут <tt>zoom</tt> (для масштаба).',
	'slippymap_error_invalid_attribute_lat_value_nan' => 'Значение <tt>$1</tt> неверно для атрибута <tt>lat</tt> (широта), данное значение должно быть корректным числом.',
	'slippymap_error_invalid_attribute_lon_value_nan' => 'Значение <tt>$1</tt> неверно для атрибута <tt>lon</tt> (долгота), данное значение должно быть корректным числом.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => 'Значение <tt>$1</tt> неверно для атрибута <tt>zoom</tt>, данное значение должно быть корректным числом.',
	'slippymap_error_invalid_attribute_width_value_nan' => 'Значение <tt>$1</tt> неверно для атрибута <tt>width</tt>, данное значение должно быть корректным числом.',
	'slippymap_error_invalid_attribute_height_value_nan' => 'Значение <tt>$1</tt> неверно для атрибута <tt>height</tt>, данное значение должно быть корректным числом.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => 'Значение <tt>$1</tt> неверно для атрибута <tt>mode</tt>, допустимые режимы: $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => 'Значение <tt>$1</tt> неверно для атрибута <tt>layer</tt>, допустимые слои: $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => 'Значение <tt>$1</tt> неверно для атрибута <tt>marker</tt>, допустимые маркеры: $2.',
	'slippymap_error_unknown_attribute' => 'Атрибут <tt>$1</tt> неизвестен.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => 'Значение <tt>$1</tt> неверно для атрибута <tt>lat</tt> (широта). Широта должна быть между -180 и 180 градусами.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => 'Значение <tt>$1</tt> неверно для атрибута <tt>lon</tt> (долгота). Долгота должна быть между -180 и 180 градусами.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => 'Значение <tt>$1</tt> неверно для атрибута <tt>zoom</tt>. Масштаб должен быть между $2 и $3.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'Значение <tt>$1</tt> неверно для атрибута <tt>width</tt>. Ширина должна быть между $2 и $3.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'Значение <tt>$1</tt> неверно для атрибута <tt>height</tt>. Высота должна быть между $2 и $3.',
	'slippymap_code' => 'Викикод для просмотра этой карты:',
	'slippymap_button_code' => 'Получить викикод',
	'slippymap_resetview' => 'Сбросить просмотр',
	'slippymap_clicktoactivate' => 'Нажмите, чтобы активировать карту',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'slippymap_desc' => 'Pridáva značku <tt><nowiki>&lt;slippymap&gt;</nowiki></tt>, ktorá umožňuje vkladanie statických a dynamických posuvných máp. Podporuje viacero mapovacích služieb vrátane [http://openstreetmap.org openstreetmap.org]
 a NASA Worldwind.',
	'slippymap_extname' => 'SlippyMap',
	'slippymap_tagname' => 'slippymap',
	'slippymap_error' => 'Chyba rozšírenia $1: $2',
	'slippymap_errors' => 'Chyby rozšírenia $1:',
	'slippymap_error_tag_content_given' => 'Značka <tt>&lt;$1&gt;</tt> berie vstup iba vo forme atribútov (&lt;$1 [...]/&gt;), nie textu v značke (&lt;$1&gt; ... &lt;/$1&gt;)',
	'slippymap_error_missing_arguments' => 'Nezadali ste žiadne atribúty značky &lt;$1&gt;. Na nasledovnej stránke nájdete [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax informácie o použití].',
	'slippymap_error_missing_attribute_lat' => 'Chýba atribút <tt>lat</tt> (zemepisná šírka).',
	'slippymap_error_missing_attribute_lon' => 'Chýba atribút <tt>lon</tt> (zemepisná dĺžka).',
	'slippymap_error_missing_attribute_zoom' => 'Chýba atribút <tt>zoom</tt> (úroveň priblíženia).',
	'slippymap_error_invalid_attribute_lat_value_nan' => '<tt>$1</tt> nie je platná hodnota atribútu <tt>lat</tt> (zemepisná šírka), zadaná hodnota musí byť platné číslo.',
	'slippymap_error_invalid_attribute_lon_value_nan' => '<tt>$1</tt> nie je platná hodnota atribútu <tt>lon</tt> (zemepisná dĺžka), zadaná hodnota musí byť platné číslo.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => '<tt>$1</tt> nie je platná hodnota atribútu <tt>zoom</tt> (úroveň priblíženia), zadaná hodnota musí byť platné číslo.',
	'slippymap_error_invalid_attribute_width_value_nan' => '<tt>$1</tt> nie je platná hodnota atribútu <tt>width</tt> (šírka), zadaná hodnota musí byť platné číslo.',
	'slippymap_error_invalid_attribute_height_value_nan' => '<tt>$1</tt> nie je platná hodnota atribútu <tt>height</tt> (výška), zadaná hodnota musí byť platné číslo.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => '<tt>$1</tt> nie je platná hodnota atribútu <tt>mode</tt> (režim), platné režimy sú $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => '<tt>$1</tt> nie je platná hodnota atribútu <tt>layer</tt> (vrstva), platné vrstvy sú $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => '<tt>$1</tt> nie je platná hodnota atribútu <tt>marker</tt> (značka), platné značky sú $2.',
	'slippymap_error_unknown_attribute' => 'Atribút <tt>$1</tt> nie je známy.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => '<tt>$1</tt> nie je platná hodnota atribútu <tt>lat</tt> (zemepisná šírka). Zemepisná šírka musí byť medzi -90 a 90 stupňami.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => '<tt>$1</tt> nie je platná hodnota atribútu <tt>lon</tt> (zemepisná dĺžka). Zemepisná dĺžka musí byť medzi -180 a 180 stupňami.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => '<tt>$1</tt> nie je platná hodnota atribútu <tt>zoom</tt> (úroveň priblíženia). Úroveň priblíženia musí byť medzi $2 a $3.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => '<tt>$1</tt> nie je platná hodnota atribútu <tt>width</tt> (šírka). Šírka musí byť medzi $2 a $3.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => '<tt>$1</tt> nie je platná hodnota atribútu <tt>height</tt> (dĺžka). Dĺžka musí byť medzi $2 a $3.',
	'slippymap_code' => 'Wikikód tohto pohľadu na mapu:',
	'slippymap_button_code' => 'Zobraziť zdrojový kód',
	'slippymap_resetview' => 'Obnoviť zobrazenie',
	'slippymap_clicktoactivate' => 'Mapu aktivujete kliknutím',
);

/** Swedish (Svenska)
 * @author Boivie
 * @author M.M.S.
 * @author Rotsee
 */
$messages['sv'] = array(
	'slippymap_desc' => 'Tillåter användning av taggen <tt>&lt;slippymap&gt;</tt> för att visa "slippy map"-kartor från exempelvis [http://openstreetmap.org OpenStreetMap] eller NASA Worldwind.',
	'slippymap_error' => '$1-fel: $2',
	'slippymap_errors' => 'fel i $1:',
	'slippymap_error_tag_content_given' => 'Taggen <tt>&lt;$1&gt;</tt> fungerar bara med attribut (&lt;$1 [...]/&gt;), inte med text (&lt;$1&gt; ... &lt;/$1&gt;)',
	'slippymap_error_missing_arguments' => 'Du måste ange attribut till  &lt;$1&gt;-taggen. Se vidare [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax usage information].',
	'slippymap_error_missing_attribute_lat' => 'Breddgrad saknas. Använd <tt>lat</tt>-attributet.',
	'slippymap_error_missing_attribute_lon' => 'Längdgrad saknas. Använd <tt>lon</tt>-attributet.',
	'slippymap_error_missing_attribute_zoom' => 'Zoomnivå saknas. Använd <tt>zoom</tt>-attributet.',
	'slippymap_error_invalid_attribute_lat_value_nan' => '<tt>$1</tt> är inte en giltig breddgrad. Värdet måste vara ett tal i engelskt format.',
	'slippymap_error_invalid_attribute_lon_value_nan' => '<tt>$1</tt> är inte en giltig längdgrad. Värdet måste vara ett tal i engelskt format.',
	'slippymap_error_invalid_attribute_zoom_value_nan' => '<tt>$1</tt> är inte en giltig zoomnivå. Värdet måste vara ett tal i engelskt format.',
	'slippymap_error_invalid_attribute_width_value_nan' => '<tt>$1</tt> är inte en giltig bredd. Värdet måste vara ett tal i engelskt format.',
	'slippymap_error_invalid_attribute_height_value_nan' => '<tt>$1</tt> är inte en giltig höjd. Värdet måste vara ett tal i engelskt format.',
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => '<tt>$1</tt> är inte ett giltigt värde i <tt>mode</tt>-attributet. Giltiga värden är $2.',
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => '<tt>$1</tt> är inte ett giltigt värde i <tt>layer</tt>-attributet. Giltiga värden är $2.',
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => '<tt>$1</tt> är inte ett giltigt värde i <tt>marker</tt>-attributet. Giltiga värden är $2.',
	'slippymap_error_unknown_attribute' => '<tt>$1</tt> är inte ett känt attribut.',
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => '<tt>$1</tt> är inte en giltig breddgrad. Breddgrader måste ligga mellan -90 och 90 grader.',
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => '<tt>$1</tt> är inte en giltig längdgrad. Längdgrader måste ligga mellan -180 och 180 grader.',
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => '<tt>$1</tt> är inte en giltig zoomnivå. Zoomnivån måste ligga mellan $2 och $3.',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => '<tt>$1</tt> är inte ett giltigt på <tt>width</tt>-attributet. Bredden måste vara mellan mellan $2 och $3.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => '<tt>$1</tt> är inte ett giltigt på <tt>height</tt>-attributet. Höjden måste vara mellan mellan $2 och $3.',
	'slippymap_code' => 'Wikikod för denna kartvisning:',
	'slippymap_button_code' => 'Hämta wikikod',
	'slippymap_resetview' => 'Återställ visning',
	'slippymap_clicktoactivate' => 'Klicka för att aktivera kartan',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'slippymap_desc' => "Nagpapahintulot sa paggamit ng tatak na <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> upang maipakita/mapalitaw ang isang pampuwesto/pangkinaroroonang (''slippy'') mapa ng OpenLayers.  Nanggaling ang mga mapa mula sa [http://openstreetmap.org openstreetmap.org]",
	'slippymap_code' => 'Kodigo ng wiki ("wiki-kodigo") para sa tanawin ng mapang ito:',
	'slippymap_button_code' => 'Kuhanin ang kodigo ng wiki',
	'slippymap_resetview' => 'Muling itakda ang tanawin',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'slippymap_desc' => 'Thêm thẻ <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> để nhúng bản đồ trơn OpenLayers. Các bản đồ do [http://openstreetmap.org openstreetmap.org] cung cấp.',
	'slippymap_code' => 'Mã wiki để nhúng phần bản đồ này:',
	'slippymap_button_code' => 'Xem mã wiki',
	'slippymap_resetview' => 'Mặc định lại bản đồ',
	'slippymap_clicktoactivate' => 'Nhấn để khởi động bản đồ',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Wrightbus
 */
$messages['zh-hant'] = array(
	'slippymap_error' => '$1錯誤：$2',
);

