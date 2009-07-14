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
 * @author Purodha
 */
$messages['qqq'] = array(
	'slippymap_desc' => 'Short description of the Slippymap extension, shown in [[Special:Version]]. Do not translate or change links.',
	'slippymap_error' => '* $1 is the name of the extension
* $2 is an error message

{{Identical|Error}}',
	'slippymap_errors' => '* $1 is the (untranslated?) name of the extension

{{Identical|Error}}',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'slippymap_desc' => 'يسمح باستخدام وسم <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> لعرض خريطة OpenLayers لزقة. الخرائط من [http://openstreetmap.org openstreetmap.org]',
	'slippymap_code' => 'كود الويكي لعرض الخريطة هذا:',
	'slippymap_button_code' => 'الحصول على كود ويكي',
	'slippymap_resetview' => 'إعادة ضبط الرؤية',
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

/** German (Deutsch) */
$messages['de'] = array(
	'slippymap_desc' => 'Ermöglicht die Nutzung des <tt><nowiki>&lt;slippymap&gt;</nowiki></tt>-Tags zur Anzeige einer OpenLayer-SlippyMap. Die Karten stammen von [http://openstreetmap.org openstreetmap.org]',
	'slippymap_code' => 'Wikitext für diese Kartenansicht:',
	'slippymap_button_code' => 'Zeige Wikicode',
	'slippymap_resetview' => 'Zurücksetzen',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'slippymap_desc' => 'Zmóžnja wužywanje toflicki <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> za zwobraznjenje pśesuwajobneje kórty OpenLayer. Kórty su z [http://openstreetmap.org openstreetmap.org]',
	'slippymap_code' => 'Wikikod za toś ten kórtowy naglěd:',
	'slippymap_button_code' => 'Wikikod pokazaś',
	'slippymap_resetview' => 'Naglěd slědk stajiś',
	'slippymap_clicktoactivate' => 'Kliknuś, aby se kórta aktiwěrowała',
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

/** Finnish (Suomi)
 * @author Vililikku
 */
$messages['fi'] = array(
	'slippymap_desc' => 'Mahdollistaa <tt><nowiki>&lt;slippymap&gt;</nowiki></tt>-elementin käytön OpenLayers slippy map -kartan näyttämiseen. Kartat ovat osoitteesta [http://openstreetmap.org openstreetmap.org].',
	'slippymap_code' => 'Wikikoodi tälle karttanäkymälle:',
	'slippymap_button_code' => 'Hae wikikoodi',
	'slippymap_resetview' => 'Palauta näkymä',
);

/** French (Français)
 * @author Crochet.david
 * @author Grondin
 */
$messages['fr'] = array(
	'slippymap_desc' => 'Autorise l’utilisation de la balise <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> pour afficher une carte glissante d’OpenLayers. Les cartes proviennent de [http://openstreetmap.org openstreetmap.org]',
	'slippymap_code' => 'Code Wiki pour le visionnement de cette cate :',
	'slippymap_button_code' => 'Obtenir le code wiki',
	'slippymap_resetview' => 'Réinitialiser le visionnement',
	'slippymap_clicktoactivate' => 'Cliquez pour activer la carte',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'slippymap_desc' => 'Permite o uso da etiqueta <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> para amosar un mapa slippy. Os mapas son de [http://openstreetmap.org openstreetmap.org]',
	'slippymap_extname' => 'SlippyMap',
	'slippymap_tagname' => 'slippymap',
	'slippymap_error' => 'Erro na extensión $1: $2',
	'slippymap_errors' => 'Erros na extensión $1:',
	'slippymap_error_missing_attribute_lat' => 'Falta o atributo <tt>lat</tt> (para a latitude).',
	'slippymap_error_missing_attribute_lon' => 'Falta o atributo <tt>lon</tt> (para a lonxitude).',
	'slippymap_error_missing_attribute_zoom' => 'Falta o atributo <tt>zoom</tt> (para o nivel de zoom).',
	'slippymap_error_unknown_attribute' => 'Descoñécese o atributo <tt>$1</tt>.',
	'slippymap_code' => 'Código wiki para o visionado deste mapa:',
	'slippymap_button_code' => 'Obter o código wiki',
	'slippymap_resetview' => 'Axustar a vista',
	'slippymap_clicktoactivate' => 'Prema para activar o mapa',
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
	'slippymap_desc' => 'מתן האפשרות לשימוש בתגית <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> להצגת מפת OpenLayers רדומה. המפות הן מהאתר [http://openstreetmap.org openstreetmap.org]',
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
	'slippymap_desc' => 'Deit dä Befääl <tt> <nowiki>&lt;slippymap&gt;</nowiki> </tt> em Wiki dobei, öm en <i lang="en">OpenLayers slippy map</i> Kaat aanzezeije. De Landkaate-Date kumme dobei fun <i lang="en">[http://openstreetmap.org openstreetmap.org]</i> her.',
	'slippymap_extname' => 'SlippyMap',
	'slippymap_tagname' => 'slippymap',
	'slippymap_error_invalid_attribute_width_value_out_of_range' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>width</tt> jeiht nit. De Breede ier Nivvohs möße zwesche $2 un $3 lijje.',
	'slippymap_error_invalid_attribute_height_value_out_of_range' => 'Dä Wäät <tt>$1</tt> för de Eijeschaff <tt>height</tt> jeiht nit. De Hühde ier Nivvohs möße zwesche $2 un $3 lijje.',
	'slippymap_code' => 'Dä Wiki-Kood för di Kaate-Aansesh es:',
	'slippymap_button_code' => 'Donn dä Wiki-Kood zeije',
	'slippymap_resetview' => 'Aansesh zeröcksetze',
	'slippymap_clicktoactivate' => 'Don klecke, öm di Kaat aanzemaache',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'slippymap_desc' => 'Setzt en Tag <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> derbäi deen et erlaabt fit statesch an dynamesch Kaarten  anzebannen. Ënnerstetzt verschidde Kaarte-Servicer wéi [http://openstreetmap.org openstreetmap.org OpenStreetMap] an NASA Worldwind',
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
	'slippymap_desc' => 'Autoriza l’utilizacion de la balisa <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> per afichar una mapa lisanta d’OpenLayers. Las mapas provenon de [http://openstreetmap.org openstreetmap.org]',
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
	'slippymap_desc' => 'Permite o uso da marca <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> para apresentar um mapa corrediço OpenLayers. Os mapas provêm de [http://openstreetmap.org openstreetmap.org]',
	'slippymap_code' => 'Código wiki para esta vista do mapa:',
	'slippymap_button_code' => 'Buscar código wiki',
	'slippymap_resetview' => 'Repor vista',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Eduardo.mps
 */
$messages['pt-br'] = array(
	'slippymap_desc' => 'Permite o uso da marca <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> para apresentar um mapa corrediço OpenLayers. Os mapas provêm de [http://openstreetmap.org openstreetmap.org]',
	'slippymap_code' => 'Código wiki para esta vista do mapa:',
	'slippymap_button_code' => 'Buscar código wiki',
	'slippymap_resetview' => 'Reiniciar vista',
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
 */
$messages['sv'] = array(
	'slippymap_desc' => 'Tillåter användning av taggen <tt>&lt;slippymap&gt;</tt> för att visa "slippy map" från OpenLayers. Kartorna kommer från [http://openstreetmap.org openstreetmap.org]',
	'slippymap_code' => 'Wikikod för denna kartvisning:',
	'slippymap_button_code' => 'Hämta wikikod',
	'slippymap_resetview' => 'Återställ visning',
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

