<?php
/**
 * Internationalization file for DynamicPageList2 extension.
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author m:User:Dangerman <cyril.dangerville@gmail.com>
*/

$wgDPL2Messages = array();

/**
 * To translate messages into your language, create a $wgDPL2Messages['lang'] array where 'lang' is your language code and take $wgDPL2Messages['en'] as a model. Replace values with appropriate translations.
 */

$wgDPL2Messages['en'] = array(
	/*
		Debug
	*/
	// (FATAL) ERRORS
	/**
	 * $0: 'namespace' or 'notnamespace'
	 * $1: wrong parameter given by user
	 * $3: list of possible titles of namespaces (except pseudo-namespaces: Media, Special)
	 */
	'dpl2_debug_' . DPL2_ERR_WRONGNS => "ERROR: Wrong '$0' parameter: '$1'! Help:  <code>$0= <i>empty string</i> (Main)$3</code>.",
	/**
	 * $0: max number of categories that can be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOMANYCATS => 'ERROR: Too many categories! Maximum: $0. Help: increase <code>$wgDPL2MaxCategoryCount</code> to specify more categories or set <code>$wgDPL2AllowUnlimitedCategories=true</code> for no limitation. (Set the variable in <code>LocalSettings.php</code>, after including <code>DynamicPageList2.php</code>.)',
	/**
	 * $0: min number of categories that have to be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOFEWCATS => 'ERROR: Too few categories! Minimum: $0. Help: decrease <code>$wgDPL2MinCategoryCount</code> to specify fewer categories. (Set the variable preferably in <code>LocalSettings.php</code>, after including <code>DynamicPageList2.php</code>.)',
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTNOINCLUDEDCATS => "ERROR: You need to include at least one category if you want to use 'addfirstcategorydate=true' or 'ordermethod=categoryadd'!",
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTMORETHAN1CAT => "ERROR: If you include more than one category, you cannot use 'addfirstcategorydate=true' or 'ordermethod=categoryadd'!",
	'dpl2_debug_' . DPL2_ERR_MORETHAN1TYPEOFDATE => 'ERROR: You cannot add more than one type of date at a time!',
	/**
	 * $0: param=val that is possible only with $1 as last 'ordermethod' parameter
	 * $1: last 'ordermethod' parameter required for $0
	*/
	'dpl2_debug_' . DPL2_ERR_WRONGORDERMETHOD => "ERROR: You can use '$0' with 'ordermethod=[...,]$1' only!",
	
	// WARNINGS
	/**
	 * $0: unknown parameter given by user
	 * $1: list of DPL2 available parameters separated by ', '
	*/
	'dpl2_debug_' . DPL2_WARN_UNKNOWNPARAM => "WARNING: Unknown parameter '$0' is ignored. Help: available parameters: <code>$1</code>.",
	/**
	 * $3: list of valid param values separated by ' | '
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM => "WARNING: Wrong '$0' parameter: '$1'! Using default: '$2'. Help: <code>$0= $3</code>.",
	/**
	 * $0: param name
	 * $1: wrong param value given by user
	 * $2: default param value used instead by program
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM_INT => "WARNING: Wrong '$0' parameter: '$1'! Using default: '$2' (no limit). Help: <code>$0= <i>empty string</i> (no limit) | n</code>, with <code>n</code> a positive integer.",
	'dpl2_debug_' . DPL2_WARN_NORESULTS => 'WARNING: No results!',
	'dpl2_debug_' . DPL2_WARN_NOINCLUDEDCATSORNS => "WARNING: It is strongly recommended to either limit the number of results with the 'count' parameter or include at least one category / namespace. If not, the generation of the page list can be quite resource and time-consuming.",
	'dpl2_debug_' . DPL2_WARN_CATOUTPUTBUTWRONGPARAMS => "WARNING: Add* parameters ('adduser', 'addeditdate', etc.)' have no effect with 'mode=category'. Only the page namespace/title can be viewed in this mode.",
	/**
	 * $0: 'headingmode' value given by user
	 * $1: value used instead by program (which means no heading)
	*/
	'dpl2_debug_' . DPL2_WARN_HEADINGBUTSIMPLEORDERMETHOD => "WARNING: 'headingmode=$0' has no effect with 'ordermethod' on a single component. Using: '$1'. Help: you can use not-$1 'headingmode' values with 'ordermethod' on multiple components. The first component is used for headings. E.g. 'ordermethod=category,<i>comp</i>' (<i>comp</i> is another component) for category headings.",
	/**
	 * $0: 'debug' value
	*/
	'dpl2_debug_' . DPL2_WARN_DEBUGPARAMNOTFIRST => "WARNING: 'debug=$0' is not in first position in the DPL element. The new debug settings are not applied before all previous parameters have been parsed and checked.",

	// OTHERS
	/**
	 * $0: SQL query executed to generate the dynamic page list
	*/
	'dpl2_debug_' . DPL2_QUERY => 'QUERY: <code>$0</code>',

	/*
	   Output formatting
	*/
	/**
	 * $1: number of articles
	*/
	'dpl2_articlecount' => 'There {{PLURAL:$1|is one article|are $1 articles}} in this heading.'
);
$wgDPL2Messages['he'] = array(
	/*
		Debug
	*/
	// (FATAL) ERRORS
	/**
	 * $0: 'namespace' or 'notnamespace'
	 * $1: wrong parameter given by user
	 * $3: list of possible titles of namespaces (except pseudo-namespaces: Media, Special)
	 */
	'dpl2_debug_' . DPL2_ERR_WRONGNS => "שגיאה: פרמטר '$0' שגוי: '$1'! עזרה: <code>$0= <i>מחרוזת ריקה</i> (ראשי)$3</code>.",
	/**
	 * $0: max number of categories that can be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOMANYCATS => 'שגיאה: קטגוריות רבות מדי! מקסימום: $0. עזרה: העלו את <code>$wgDPL2MaxCategoryCount</code> כדי לציין עוד קטגוריות או הגדירו <code>$wgDPL2AllowUnlimitedCategories=true</code> כדי לבטל את ההגבלה. (הגידרו את המשתנה בקובץ <code>LocalSettings.php</code>, לאחר הכללת <code>DynamicPageList2.php</code>.)',
	/**
	 * $0: min number of categories that have to be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOFEWCATS => 'שגיאה: קטגוריות מעטות מדי! מינימום: $0. עזרה: הורידו את <code>$wgDPL2MinCategoryCount</code> כדי לציין פחות קטגוריות. (הגידרו את המשתנה בקובץ <code>LocalSettings.php</code>, לאחר הכללת <code>DynamicPageList2.php</code>.)',
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTNOINCLUDEDCATS => "שגיאה: עליכם להכליל לפחות קטגוריה אחת אם ברצונכם להשתמש ב־'addfirstcategorydate=true' או ב־'ordermethod=categoryadd'!",
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTMORETHAN1CAT => "שגיאה: אם אתם מכלילים יותר מקטגוריה אחת, אינכם יכולים להשתמש ב־'addfirstcategorydate=true' או ב־'ordermethod=categoryadd'!",
	'dpl2_debug_' . DPL2_ERR_MORETHAN1TYPEOFDATE => 'שגיאה: אינכם יכולים להוסיף יותר מסוג אחד של תאריך בו זמנית!',
	/**
	 * $0: param=val that is possible only with $1 as last 'ordermethod' parameter
	 * $1: last 'ordermethod' parameter required for $0
	*/
	'dpl2_debug_' . DPL2_ERR_WRONGORDERMETHOD => "שגיאה: באפשרותכם להשתמש ב־'$0' עם 'ordermethod=[...,]$1' בלבד!",
	
	// WARNINGS
	/**
	 * $0: unknown parameter given by user
	 * $1: list of DPL2 available parameters separated by ', '
	*/
	'dpl2_debug_' . DPL2_WARN_UNKNOWNPARAM => "אזהרה: בוצעה התעלמות מהפרמטר הלא ידוע '$0'. עזרה: פרמטרים זמינים: <code>$1</code>.",
	/**
	 * $3: list of valid param values separated by ' | '
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM => "אזהרה: פרמטר '$0' שגוי: '$1'! משתמש בברירת המחדל: '$2'. עזרה: <code>$0= $3</code>.",
	/**
	 * $0: param name
	 * $1: wrong param value given by user
	 * $2: default param value used instead by program
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM_INT => "אזהרה: פרמטר '$0' שגוי: '$1'! משתמש בברירת המחדל: '$2' (ללא הגבלה). עזרה: <code>$0= <i>מחרוזת ריקה</i> (ללא הגבלה) | n</code>, עם <code>n</code> כמספר שלם וחיובי.",
	'dpl2_debug_' . DPL2_WARN_NORESULTS => 'אזהרה: אין תוצאות!',
	'dpl2_debug_' . DPL2_WARN_NOINCLUDEDCATSORNS => "אזהרה: מומלץ ביותר או להגביל את מספר התוצאות עם הפרמטר 'count' או להכליל לפחות קטגוריה אחת או מרחב שם אחד. אם לא, היצירה של רשימת הדפים עלולה לקחת זמן ולבזבז משאבים.",
	'dpl2_debug_' . DPL2_WARN_CATOUTPUTBUTWRONGPARAMS => "אזהרה: להוספת* הפרמטרים ('adduser',‏ 'addeditdate' וכדומה) אין השפעה עם 'mode=category'. ניתן לצפות רק במרחב השם או בכותרת הדף במצב זה.",
	/**
	 * $0: 'headingmode' value given by user
	 * $1: value used instead by program (which means no heading)
	*/
	'dpl2_debug_' . DPL2_WARN_HEADINGBUTSIMPLEORDERMETHOD => "אזהרה: ל־'headingmode=$0' אין השפעה עם 'ordermethod' על פריט יחיד. משתמש ב: '$1'. עזרה: באפשרותכם להשתמש בערכים של 'headingmode' שאינם $1 עם 'ordermethod' על פריטים מרובים. משתמשים בפריט הראשון לכותרת. למשל, 'ordermethod=category,<i>comp</i>' (<i>comp</i> הוא פריט אחר) לכותרות הקטגוריה.",
	/**
	 * $0: 'debug' value
	*/
	'dpl2_debug_' . DPL2_WARN_DEBUGPARAMNOTFIRST => "אזהרה: 'debug=$0w הוא לא במקום הראשון ברכיב ה־DPL. הגדרות ניפוי השגיאות החדשות לא יחולו לפני שכל הפרמטרים הקודמים ינותחו וייבדקו.",

	// OTHERS
	/**
	 * $0: SQL query executed to generate the dynamic page list
	*/
	'dpl2_debug_' . DPL2_QUERY => 'שאילתה: <code>$0</code>',

	/*
	   Output formatting
	*/
	/**
	 * $1: number of articles
	*/
	'dpl2_articlecount' => '{{plural:$1|ישנם $1 דפים|ישנו דף אחד}} תחת כותרת זו.'
);
$wgDPL2Messages['nl'] = array(
	/*
		Debug
	*/
	// (FATAL) ERRORS
	/**
	 * $0: 'namespace' or 'notnamespace'
	 * $1: wrong parameter given by user
	 * $3: list of possible titles of namespaces (except pseudo-namespaces: Media, Special)
	 */
	'dpl2_debug_' . DPL2_ERR_WRONGNS => "FOUT: Verkeerde parameter '$0': '$1'! Hulp:  <code>$0= <i>lege string</i> (Main)$3</code>.",
	/**
	 * $0: max number of categories that can be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOMANYCATS => 'FOUT: Te veel categori�! Maximum: $0. Hulp: verhoog <code>$wgDPL2MaxCategoryCount</code> om meer categorie� op te kunnen geven of stel geen limiet in met <code>$wgDPL2AllowUnlimitedCategories=true</code>. (Neem deze variabele op in <code>LocalSettings.php</code>, na het toevoegen van <code>DynamicPageList2.php</code>.)',
	/**
	 * $0: min number of categories that have to be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOFEWCATS => 'FOUT: Te weinig categorie�! Minimum: $0. Hulp: verlaag <code>$wgDPL2MinCategoryCount</code> om minder categorie� aan te hoeven geven. (Stel de variabele bij voorkeur in via <code>LocalSettings.php</code>, na het toevoegen van <code>DynamicPageList2.php</code>.)',
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTNOINCLUDEDCATS => "FOUT: U dient tenminste �n categorie op te nemen als u 'addfirstcategorydate=true' of 'ordermethod=categoryadd' wilt gebruiken!",
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTMORETHAN1CAT => "FOUT: Als u meer dan �n categorie opneemt, kunt u 'addfirstcategorydate=true' of 'ordermethod=categoryadd' niet gebruiken!",
	'dpl2_debug_' . DPL2_ERR_MORETHAN1TYPEOFDATE => 'FOUT: U kunt niet meer dan �n type of datum tegelijk gebruiken!',
	/**
	 * $0: param=val that is possible only with $1 as last 'ordermethod' parameter
	 * $1: last 'ordermethod' parameter required for $0
	*/
	'dpl2_debug_' . DPL2_ERR_WRONGORDERMETHOD => "FOUT: U kunt '$0' alleen met 'ordermethod=[...,]$1' gebruiken!",
	
	// WARNINGS
	/**
	 * $0: param name
	 * $1: wrong param value given by user
	 * $2: default param value used instead by program
	*/
	#'dpl2_debug_' . DPL2_WARN_WRONGCOUNT => "WAARSCHUWING: Verkeerde parameter '$0': '$1'! Nu wordt de standaard gebruikt: '$2' (geen limiet in aantal). Hulp: <code>$0= <i>lege string</i> (geen limiet in aantal) | n</code>, waar <code>n</code> een positief heel getal is.",
	/**
	 * $3: list of valid param values separated by ' | '
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM => "WAARSCHUWING: Verkeerde parameter '$0': '$1'! Nu wordt de standaard gebruikt: '$2'. Hulp: <code>$0= $3</code>.",
	'dpl2_debug_' . DPL2_WARN_NORESULTS => 'WAARSCHUWING: Geen resultaten!',
	'dpl2_debug_' . DPL2_WARN_NOINCLUDEDCATSORNS => 'WAARSCHUWING: Het is sterk aan te bevelen om tenminste �n categorie of naamruimte op te nemen. Zo niet, dan is het samenstellen van de paginalijst een redelijk zware belasting voor systeembronnen en kan dit proces redelijk lang duren.',
	'dpl2_debug_' . DPL2_WARN_CATOUTPUTBUTWRONGPARAMS => "WAARSCHUWING: Add* parameters ('adduser', 'addeditdate', etc.)' heeft geen effect bij 'mode=category'. Alleen de paginanaamruimte/titel is in deze modus te bekijken.",
	/**
	 * $0: 'headingmode' value given by user
	 * $1: value used instead by program (which means no heading)
	*/
	'dpl2_debug_' . DPL2_WARN_HEADINGBUTSIMPLEORDERMETHOD => "WAARSCHUWING: 'headingmode=$0' heeft geen effect met 'ordermethod' op een enkele component. Nu wordt gebruikt: '$1'. Hulp: u kunt een niet-$1 'headingmode'-waarde gebruiken met 'ordermethod' op meerdere componenten. De eerste component wordt gebruikt als kop. Bijvoorbeeld 'ordermethod=category,<i>comp</i>' (<i>comp</i> is een ander component) voor categoriekoppen.",
	/**
	 * $0: 'debug' value
	*/
	'dpl2_debug_' . DPL2_WARN_DEBUGPARAMNOTFIRST => "WAARSCHUWING: 'debug=$0' is niet de eerste positie in het DPL-element. De nieuwe debuginstellingen zijn niet toegepast voor alle voorgaande parameters zijn verwerkt en gecontroleerd.",

	// OTHERS
	/**
	 * $0: SQL query executed to generate the dynamic page list
	*/
	'dpl2_debug_' . DPL2_QUERY => 'QUERY: <code>$0</code>',

	/*
	   Output formatting
	*/
	/**
	 * $1: number of articles
	*/
	'dpl2_articlecount1' => 'Er is $1 artikel onder deze kop.',
	'dpl2_articlecount' => 'Er zijn $1 artikelen onder deze kop.'
);

?>
