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
	'dpl2_debug_' . DPL2_ERR_WRONGNS => "ERROR: Wrong '$0' parameter: '$1'! Help:  <code>$0= <i>empty string</i> (Main)$3</code>. (Equivalents with magic words are allowed too.)",
	/**
	 * $0: 'linksto' (left as $0 just in case the parameter is renamed in the future)
	 * $1: wrong parameter given by user
	*/
	'dpl2_debug_' . DPL2_ERR_WRONGLINKSTO => "ERROR: Wrong '$0' parameter: '$1'! Help:  <code>$0= <i>full pagename</i></code>. (Magic words are allowed.)",
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
	/**
	 * $0: prefix_dpl_clview where 'prefix' is the prefix of your mediawiki table names
	 * $1: SQL query to create the prefix_dpl_clview on your mediawiki DB
	*/
	'dpl2_debug_' . DPL2_ERR_NOCLVIEW => "ERROR: Cannot perform logical operations on the Uncategorized pages (e.g. with the 'category' parameter) because the $0 view does not exist on the database! Help: have the DB admin execute this query: <code>$1</code>.",
	
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
	'dpl2_debug_' . DPL2_ERR_WRONGNS => "שגיאה: פרמטר '$0' שגוי: '$1'! עזרה: <code>$0= <i>מחרוזת ריקה</i> (ראשי)$3</code>. (ניתן להשתמש גם בשווי ערך באמצעות מילות קסם.)",
	/**
	 * $0: 'linksto' (left as $0 just in case the parameter is renamed in the future)
	 * $1: wrong parameter given by user
	*/
	'dpl2_debug_' . DPL2_ERR_WRONGLINKSTO => "שגיאה: פרמטר '$0' שגוי: '$1'! עזרה: <code>$0= <i>שם הדף המלא</i></code>. (ניתן להשתמש במילות קסם.)",
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
	/**
	 * $0: prefix_dpl_clview where 'prefix' is the prefix of your mediawiki table names
	 * $1: SQL query to create the prefix_dpl_clview on your mediawiki DB
	*/
	'dpl2_debug_' . DPL2_ERR_NOCLVIEW => "שגיאה: לא ניתן לבצע פעולות לוגיות על דפים ללא קטגוריות (למשל, עם הפרמטר 'קטגוריה') כיוון שתצוגת $0 אינה קיימת במסד הנתונים! עזרה: מנהל מסד הנתונים צריך להריץ את השאילתה: <code>$1</code>.",
	
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
	'dpl2_debug_' . DPL2_ERR_TOOMANYCATS => 'FOUT: Te veel categoriën! Maximum: $0. Hulp: verhoog <code>$wgDPL2MaxCategoryCount</code> om meer categorieën op te kunnen geven of stel geen limiet in met <code>$wgDPL2AllowUnlimitedCategories=true</code>. (Neem deze variabele op in <code>LocalSettings.php</code>, na het toevoegen van <code>DynamicPageList2.php</code>.)',
	/**
	 * $0: min number of categories that have to be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOFEWCATS => 'FOUT: Te weinig categorieën! Minimum: $0. Hulp: verlaag <code>$wgDPL2MinCategoryCount</code> om minder categorieën aan te hoeven geven. (Stel de variabele bij voorkeur in via <code>LocalSettings.php</code>, na het toevoegen van <code>DynamicPageList2.php</code>.)',
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTNOINCLUDEDCATS => "FOUT: U dient tenminste één categorie op te nemen als u 'addfirstcategorydate=true' of 'ordermethod=categoryadd' wilt gebruiken!",
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTMORETHAN1CAT => "FOUT: Als u meer dan één categorie opneemt, kunt u 'addfirstcategorydate=true' of 'ordermethod=categoryadd' niet gebruiken!",
	'dpl2_debug_' . DPL2_ERR_MORETHAN1TYPEOFDATE => 'FOUT: U kunt niet meer dan één type of datum tegelijk gebruiken!',
	/**
	 * $0: param=val that is possible only with $1 as last 'ordermethod' parameter
	 * $1: last 'ordermethod' parameter required for $0
	*/
	'dpl2_debug_' . DPL2_ERR_WRONGORDERMETHOD => "FOUT: U kunt '$0' alleen met 'ordermethod=[...,]$1' gebruiken!",
	/**
	 * $0: prefix_dpl_clview where 'prefix' is the prefix of your mediawiki table names
	 * $1: SQL query to create the prefix_dpl_clview on your mediawiki DB
	*/
	'dpl2_debug_' . DPL2_ERR_NOCLVIEW => $wgDPL2Messages['en']['dpl2_debug_' . DPL2_ERR_NOCLVIEW],
	
	// WARNINGS
	/**
	 * $0: unknown parameter given by user
	 * $1: list of DPL2 available parameters separated by ', '
	*/
	'dpl2_debug_' . DPL2_WARN_UNKNOWNPARAM => $wgDPL2Messages['en']['dpl2_debug_' . DPL2_WARN_UNKNOWNPARAM],
	/**
	 * $3: list of valid param values separated by ' | '
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM => "WAARSCHUWING: Verkeerde parameter '$0': '$1'! Nu wordt de standaard gebruikt: '$2'. Hulp: <code>$0= $3</code>.",
	/**
	 * $0: param name
	 * $1: wrong param value given by user
	 * $2: default param value used instead by program
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM_INT => $wgDPL2Messages['en']['dpl2_debug_' . DPL2_WARN_WRONGPARAM_INT],
	'dpl2_debug_' . DPL2_WARN_NORESULTS => 'WAARSCHUWING: Geen resultaten!',
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
	'dpl2_articlecount' => 'Er {{PLURAL:$1|is één pagina|zijn $1 pagina\'s}} onder deze kop.'
);
$wgDPL2Messages['ru'] = array(
	/*
		Debug
	*/
	// (FATAL) ERRORS
	/**
	 * $0: 'namespacenamespace' or 'notnamespace'
	 * $1: wrong parameter given by user
	 * $3: list of possible titles of namespaces (except pseudo-namespaces: Media, Special)
	 */
	'dpl2_debug_' . DPL2_ERR_WRONGNS => "ОШИБКА: неправильный «$0»-параметр: «$1»! Подсказка:  <code>$0= <i>пустая строка</i> (Основное)$3</code>.",
	/**
	 * $0: max number of categories that can be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOMANYCATS => 'ОШИБКА: слишком много категорий! Максимум: $0. Подсказка: увеличте <code>$wgDPL2MaxCategoryCount</code> чтобы разрешить больше категорий или установите <code>$wgDPL2AllowUnlimitedCategories=true</code> для снятия ограничения. (Устанавливайте переменные в <code>LocalSettings.php</code>, после подключения <code>DynamicPageList2.php</code>.)',
	/**
	 * $0: min number of categories that have to be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOFEWCATS => 'ОШИБКА: слишком мало категорий! Минимум: $0. Подсказка: уменьшите <code>$wgDPL2MinCategoryCount</code> чтобы разрешить меньше категорий. (Устанавливайте переменную в <code>LocalSettings.php</code>, после подключения <code>DynamicPageList2.php</code>.)',
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTNOINCLUDEDCATS => "ОШИБКА: вы должны включить хотя бы одну категорию, если вы хотите использовать «addfirstcategorydate=true» или «ordermethod=categoryadd»!",
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTMORETHAN1CAT => "ОШИБКА: если вы включаете больше одной категории, то вы не можете использовать «addfirstcategorydate=true» или «ordermethod=categoryadd»!",
	'dpl2_debug_' . DPL2_ERR_MORETHAN1TYPEOFDATE => 'ОШИБКА: вы не можете добавить более одного типа данных за раз!',
	/**
	 * $0: param=val that is possible only with $1 as last 'ordermethod' parameter
	 * $1: last 'ordermethod' parameter required for $0
	*/
	'dpl2_debug_' . DPL2_ERR_WRONGORDERMETHOD => "ОШИБКА: вы можете использовать «$0» только с «ordermethod=[...,]$1»!",
	/**
	 * $0: prefix_dpl_clview where 'prefix' is the prefix of your mediawiki table names
	 * $1: SQL query to create the prefix_dpl_clview on your mediawiki DB
	*/
	'dpl2_debug_' . DPL2_ERR_NOCLVIEW => $wgDPL2Messages['en']['dpl2_debug_' . DPL2_ERR_NOCLVIEW],
	
	// WARNINGS
	/**
	 * $0: unknown parameter given by user
	 * $1: list of DPL2 available parameters separated by ', '
	*/
	'dpl2_debug_' . DPL2_WARN_UNKNOWNPARAM => "ПРЕДУПРЕЖДЕНИЕ: неизвестный параметр «$0» проигнорирован. Подсказка: доступные параметры: <code>$1</code>.",
	/**
	 * $3: list of valid param values separated by ' | '
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM => "ПРЕДУПРЕЖДЕНИЕ: неправильный параметр «$0»: «$1»! Использование параметра по умолчанию: «$2». Подсказка: <code>$0= $3</code>.",
	/**
	 * $0: param name
	 * $1: wrong param value given by user
	 * $2: default param value used instead by program
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM_INT => "ПРЕДУПРЕЖДЕНИЕ: неправильный параметр «$0»: «$1»! Использование параметра по умолчанию: «$2» (без ограничений). Подсказка: <code>$0= <i>пустая строка</i> (без ограничений) | n</code>, с <code>n</code> равным положительному целому числу.",
	'dpl2_debug_' . DPL2_WARN_NORESULTS => 'ПРЕДУПРЕЖДЕНИЕ: не найдено!',
	'dpl2_debug_' . DPL2_WARN_CATOUTPUTBUTWRONGPARAMS => "ПРЕДУПРЕЖДЕНИЕ: Добавление* параметров («adduser», «addeditdate», и др.) не действительны с «mode=category». Только пространства имён или названия могут просматриваться в этом режиме.",
	/**
	 * $0: 'headingmode' value given by user
	 * $1: value used instead by program (which means no heading)
	*/
	'dpl2_debug_' . DPL2_WARN_HEADINGBUTSIMPLEORDERMETHOD => "ПРЕДУПРЕЖДЕНИЕ: «headingmode=$0» не действителен с «ordermethod» в одном компоненте. Использование: «$1». Подсказка: вы можете использоватьe не-$1 «headingmode» значения с «ordermethod» во множестве компонентов. Первый компонент используется для заголовков. Например, «ordermethod=category,<i>comp</i>» (<i>comp</i> является другим компонентом) для заголовков категорий.",
	/**
	 * $0: 'debug' value
	*/
	'dpl2_debug_' . DPL2_WARN_DEBUGPARAMNOTFIRST => "ПРЕДУПРЕЖДЕНИЕ: «debug=$0» не находится на первом месте в DPL-элементе. Новые настройки отладки не будут применены пока все предыдущие параметры не будут разобраны и проверены.",

	// OTHERS
	/**
	 * $0: SQL query executed to generate the dynamic page list
	*/
	'dpl2_debug_' . DPL2_QUERY => 'ЗАПРОС: <code>$0</code>',

	/*
	   Output formatting
	*/
	/**
	 * $1: number of articles
	*/
	'dpl2_articlecount' => 'В этом заголовке $1 {{PLURAL:$1|статья|статьи|статей}}.'
);
$wgDPL2Messages['sk'] = array(
	/*
		Debug
	*/
	// (FATAL) ERRORS
	/**
	 * $0: 'namespace' or 'notnamespace'
	 * $1: wrong parameter given by user
	 * $3: list of possible titles of namespaces (except pseudo-namespaces: Media, Special)
	 */
	'dpl2_debug_' . DPL2_ERR_WRONGNS => "CHYBA: nespr�vny parameter '$0': '$1'! Pomocn�k:  <code>$0= <i>pr�zdny re�azec</i> (Hlavn�)$3</code>. (Ekvivalenty s magick�mi slovami s� tie� povolen�.)",
	/**
	 * $0: 'linksto' (left as $0 just in case the parameter is renamed in the future)
	 * $1: wrong parameter given by user
	*/
	'dpl2_debug_' . DPL2_ERR_WRONGLINKSTO => "CHYBA: Zl� parameter '$0': '$1'! Pomocn�k:  <code>$0= <i>pln� n�zov str�nky</i></code>. (Magick� slov� s� zapnut�.)",
	/**
	 * $0: max number of categories that can be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOMANYCATS => 'CHYBA: Pr�li� ve�a kateg�ri�! Maximum: $0. Pomocn�k: zv��te <code>$wgDPL2MaxCategoryCount</code>, ��m �pecifikujete viac kateg�ri� alebo nastavte <code>$wgDPL2AllowUnlimitedCategories=true</code> pre vypnutie limitu. (Premenn� nastavte v <code>LocalSettings.php</code>, potom ako bol includovan� <code>DynamicPageList2.php</code>.)',
	/**
	 * $0: min number of categories that have to be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOFEWCATS => 'CHYBA: Pr�li� m�lo kateg�ri�! Minimum: $0. Pomocn�k: zn�te <code>$wgDPL2MinCategoryCount</code>, ��m �pecifikujete menej kateg�ri�. (Premenn� nastavte v <code>LocalSettings.php</code>, potom ako bol includovan� <code>DynamicPageList2.php</code>.)',
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTNOINCLUDEDCATS => "CHYBA: Mus�te zahrn�� aspo� jednu kateg�riu ak chcete pou�i� 'addfirstcategorydate=true' alebo 'ordermethod=categoryadd'!",
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTMORETHAN1CAT => "CHYBA: Ak zahrniete viac ako jednu kateg�riu, nem��ete pou�i� 'addfirstcategorydate=true' alebo 'ordermethod=categoryadd'!",
	'dpl2_debug_' . DPL2_ERR_MORETHAN1TYPEOFDATE => 'CHYBA: Nem��ete naraz prida� viac ako jeden typ d�tumu!',
	/**
	 * $0: param=val that is possible only with $1 as last 'ordermethod' parameter
	 * $1: last 'ordermethod' parameter required for $0
	*/
	'dpl2_debug_' . DPL2_ERR_WRONGORDERMETHOD => "CHYBA: '$0' m��ete pou�i� iba s 'ordermethod=[...,]$1'!",
	/**
	 * $0: prefix_dpl_clview where 'prefix' is the prefix of your mediawiki table names
	 * $1: SQL query to create the prefix_dpl_clview on your mediawiki DB
	*/
	'dpl2_debug_' . DPL2_ERR_NOCLVIEW => "CHYBA: Nie je mo�n� vykon�va� logick� oper�cie na nekategorizovan�ch str�nkach (napr. s parametrom 'Kateg�ria') lebo neexistuje na datab�zu poh�ad $0! Pomocn�k: nech admim datab�zy vykon� tento dotaz: <code>$1</code>.",
	
	// WARNINGS
	/**
	 * $0: unknown parameter given by user
	 * $1: list of DPL2 available parameters separated by ', '
	*/
	'dpl2_debug_' . DPL2_WARN_UNKNOWNPARAM => "VAROVANIE: Nezn�my parameter '$0' ignorovan�. Pomocn�k: dostupn� parametre: <code>$1</code>.",
	/**
	 * $3: list of valid param values separated by ' | '
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM => "VAROVANIE: Nespr�vny '$0' parameter: '$1'! Pou��vam �tandardn�: '$2'. Pomocn�k: <code>$0= $3</code>.",
	/**
	 * $0: param name
	 * $1: wrong param value given by user
	 * $2: default param value used instead by program
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM_INT => "VAROVANIE: Nespr�vny parameter  '$0': '$1'! Pou��vam �tandardn�: '$2' (bez obmedzenia). Pomocn�k: <code>$0= <i>pr�zdny re�azec</i> (bez obmedzenia) | n</code>, s <code>n</code> kladn�m cel�m ��slom.",
	'dpl2_debug_' . DPL2_WARN_NORESULTS => 'VAROVANIE: No results!',
	'dpl2_debug_' . DPL2_WARN_CATOUTPUTBUTWRONGPARAMS => "VAROVANIE: Parametre Add* ('adduser', 'addeditdate', at�.)' nepracuj� s 'mode=category'. V tomto re�ime je mo�n� prehliada� iba menn� priestor/titulok str�nky.",
	/**
	 * $0: 'headingmode' value given by user
	 * $1: value used instead by program (which means no heading)
	*/
	'dpl2_debug_' . DPL2_WARN_HEADINGBUTSIMPLEORDERMETHOD => "VAROVANIE: 'headingmode=$0' nepracuje s 'ordermethod' na jednom komponente. Pou��vam: '$1'. Pomocn�k: ��ete pou�i� not-$1 hodnoty 'headingmode' s 'ordermethod' na viacer� komponenty. Prv� komponent sa pou��va na nadpisy. Napr. 'ordermethod=category,<i>comp</i>' (<i>comp</i> je in� komponent) pre nadpisy kateg�rie.",
	/**
	 * $0: 'debug' value
	*/
	'dpl2_debug_' . DPL2_WARN_DEBUGPARAMNOTFIRST => "VAROVANIE: 'debug=$0' nie je prv� poz�cia v prvky DPL. Nov� debugovacie nastavenia nebud� pou�it� sk�r, ako bud� preparsovan� a skontrolovan� v�etky predch�dzaj�ce.",

	// OTHERS
	/**
	 * $0: SQL query executed to generate the dynamic page list
	*/
	'dpl2_debug_' . DPL2_QUERY => 'DOTAZ: <code>$0</code>',

	/*
	   Output formatting
	*/
	/**
	 * $1: number of articles
	*/
	'dpl2_articlecount' => 'V tomto nadpise {{PLURAL:$1|je jeden �l�nok|s� $1 �l�ny|je $1 �l�nkov}} in this heading.'
);
$wgDPL2Messages['zh-cn'] = array(
	/*
		Debug
	*/
	// (FATAL) ERRORS
	/**
	 * $0: 'namespace' or 'notnamespace'
	 * $1: wrong parameter given by user
	 * $3: list of possible titles of namespaces (except pseudo-namespaces: Media, Special)
	 */
	'dpl2_debug_' . DPL2_ERR_WRONGNS => "错误: 错误的 '$0' 参数: '$1'! 帮助:  <code>$0= <i>空白字符串</i> (主)$3</code>。",
	/**
	 * $0: max number of categories that can be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOMANYCATS => '错误: 过多分类! 最大值: $0。 帮助: 增加 <code>$wgDPL2MaxCategoryCount</code> 的值去指定更多的分类或设定 <code>$wgDPL2AllowUnlimitedCategories=true</code> 以解除限制。 (当加上 <code>DynamicPageList2.php</code>后，在<code>LocalSettings.php</code>中设定变量。)',
	/**
	 * $0: min number of categories that have to be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOFEWCATS => '错误: 过少分类! 最小值: $0。 帮助: 减少 <code>$wgDPL2MinCategoryCount</code> 的值去指定更少的分类。 (当加上 <code>DynamicPageList2.php</code>后，在<code>LocalSettings.php</code>中设定一个合适的变量。)',
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTNOINCLUDEDCATS => "错误: 如果您想用 'addfirstcategorydate=true' 或 'ordermethod=categoryadd' ，您需要包含最少一个分类!",
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTMORETHAN1CAT => "错误: 如果您包含多一个分类，您不可以用 'addfirstcategorydate=true' 或 'ordermethod=categoryadd'!",
	'dpl2_debug_' . DPL2_ERR_MORETHAN1TYPEOFDATE => '错误: 您不可以在一个时间里加入多于一种嘅日期!',
	/**
	 * $0: param=val that is possible only with $1 as last 'ordermethod' parameter
	 * $1: last 'ordermethod' parameter required for $0
	*/
	'dpl2_debug_' . DPL2_ERR_WRONGORDERMETHOD => "错误: 你只可以用 'ordermethod=[...,]$1' 在 '$0' 上!",
	/**
	 * $0: prefix_dpl_clview where 'prefix' is the prefix of your mediawiki table names
	 * $1: SQL query to create the prefix_dpl_clview on your mediawiki DB
	*/
	'dpl2_debug_' . DPL2_ERR_NOCLVIEW => $wgDPL2Messages['en']['dpl2_debug_' . DPL2_ERR_NOCLVIEW],
	
	// WARNINGS
	/**
	 * $0: unknown parameter given by user
	 * $1: list of DPL2 available parameters separated by ', '
	*/
	'dpl2_debug_' . DPL2_WARN_UNKNOWNPARAM => "警告: 不明的参数 '$0' 被忽略。 帮助: 可用的参数: <code>$1</code>。",
	/**
	 * $3: list of valid param values separated by ' | '
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM => "警告: 错误的 '$0' 参数: '$1'! 正在使用默认值: '$2'。 帮助: <code>$0= $3</code>。",
	/**
	 * $0: param name
	 * $1: wrong param value given by user
	 * $2: default param value used instead by program
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM_INT => "警告: 错误的 '$0' 参数: '$1'! 正在使用默认值: '$2' (没有上限)。 帮助: <code>$0= <i>空白字符串</i> (没有上限) | n</code>, <code>n</code>是一个正整数。",
	'dpl2_debug_' . DPL2_WARN_NORESULTS => '警告: 无结果!',
	'dpl2_debug_' . DPL2_WARN_CATOUTPUTBUTWRONGPARAMS => "警告: 加入* 参数 ('adduser', 'addeditdate', 等)' 是对 'mode=category' 没有作用。只有页面空间名／标题才可以在这个模式度看到。",
	/**
	 * $0: 'headingmode' value given by user
	 * $1: value used instead by program (which means no heading)
	*/
	'dpl2_debug_' . DPL2_WARN_HEADINGBUTSIMPLEORDERMETHOD => "警告: 在单一部件中， 'ordermethod' 用 'headingmode=$0' 是没有作用的。 正在使用: '$1'。 帮助: 你可以用非$1 'headingmode' 数值，在多个部件中用 'ordermethod' 。第一个部是用来作标题。例如在分类标题中用 'ordermethod=category,<i>comp</i>' (<i>comp</i>是另外一个部件) 。",
	/**
	 * $0: 'debug' value
	*/
	'dpl2_debug_' . DPL2_WARN_DEBUGPARAMNOTFIRST => "警告: 'debug=$0' 不是第一个在DPL元素嘅第一位置。新的除错设定在所有参数都能处理和检查前都不会应用。",

	// OTHERS
	/**
	 * $0: SQL query executed to generate the dynamic page list
	*/
	'dpl2_debug_' . DPL2_QUERY => '查訽: <code>$0</code>',

	/*
	   Output formatting
	*/
	/**
	 * $1: number of articles
	*/
	'dpl2_articlecount' => '在这个标题中有$1篇条目。'
);
$wgDPL2Messages['zh-tw'] = array(
	/*
		Debug
	*/
	// (FATAL) ERRORS
	/**
	 * $0: 'namespace' or 'notnamespace'
	 * $1: wrong parameter given by user
	 * $3: list of possible titles of namespaces (except pseudo-namespaces: Media, Special)
	 */
	'dpl2_debug_' . DPL2_ERR_WRONGNS => "錯誤: 錯誤的 '$0' 參數: '$1'! 說明:  <code>$0= <i>空白字串</i> (主)$3</code>。",
	/**
	 * $0: max number of categories that can be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOMANYCATS => '錯誤: 過多分類! 最大值: $0。 說明: 增加 <code>$wgDPL2MaxCategoryCount</code> 的值去指定更多的分類或設定 <code>$wgDPL2AllowUnlimitedCategories=true</code> 以解除限制。 (當加上 <code>DynamicPageList2.php</code>後，在<code>LocalSettings.php</code>中設定變數。)',
	/**
	 * $0: min number of categories that have to be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOFEWCATS => '錯誤: 過少分類! 最小值: $0。 說明: 減少 <code>$wgDPL2MinCategoryCount</code> 的值去指定更少的分類。 (當加上 <code>DynamicPageList2.php</code>後，在<code>LocalSettings.php</code>中設定一個合適的變數。)',
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTNOINCLUDEDCATS => "錯誤: 如果您想用 'addfirstcategorydate=true' 或 'ordermethod=categoryadd' ，您需要包含最少一個分類!",
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTMORETHAN1CAT => "錯誤: 如果您包含多一個分類，您不可以用 'addfirstcategorydate=true' 或 'ordermethod=categoryadd'!",
	'dpl2_debug_' . DPL2_ERR_MORETHAN1TYPEOFDATE => '錯誤: 您不可以在一個時間裡加入多於一種嘅日期!',
	/**
	 * $0: param=val that is possible only with $1 as last 'ordermethod' parameter
	 * $1: last 'ordermethod' parameter required for $0
	*/
	'dpl2_debug_' . DPL2_ERR_WRONGORDERMETHOD => "錯誤: 你只可以用 'ordermethod=[...,]$1' 在 '$0' 上!",
	/**
	 * $0: prefix_dpl_clview where 'prefix' is the prefix of your mediawiki table names
	 * $1: SQL query to create the prefix_dpl_clview on your mediawiki DB
	*/
	'dpl2_debug_' . DPL2_ERR_NOCLVIEW => $wgDPL2Messages['en']['dpl2_debug_' . DPL2_ERR_NOCLVIEW],
	
	// WARNINGS
	/**
	 * $0: unknown parameter given by user
	 * $1: list of DPL2 available parameters separated by ', '
	*/
	'dpl2_debug_' . DPL2_WARN_UNKNOWNPARAM => "警告: 不明的參數 '$0' 被忽略。 說明: 可用的參數: <code>$1</code>。",
	/**
	 * $3: list of valid param values separated by ' | '
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM => "警告: 錯誤的 '$0' 參數: '$1'! 正在使用預設值: '$2'。 說明: <code>$0= $3</code>。",
	/**
	 * $0: param name
	 * $1: wrong param value given by user
	 * $2: default param value used instead by program
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM_INT => "警告: 錯誤的 '$0' 參數: '$1'! 正在使用預設值: '$2' (沒有上限)。 說明: <code>$0= <i>空白字串</i> (沒有上限) | n</code>, <code>n</code>是一個正整數。",
	'dpl2_debug_' . DPL2_WARN_NORESULTS => '警告: 無結果!',
	'dpl2_debug_' . DPL2_WARN_CATOUTPUTBUTWRONGPARAMS => "警告: 加入* 參數 ('adduser', 'addeditdate', 等)' 是對 'mode=category' 沒有作用。只有頁面空間名／標題才可以在這個模式度看到。",
	/**
	 * $0: 'headingmode' value given by user
	 * $1: value used instead by program (which means no heading)
	*/
	'dpl2_debug_' . DPL2_WARN_HEADINGBUTSIMPLEORDERMETHOD => "警告: 在單一部件中， 'ordermethod' 用 'headingmode=$0' 是沒有作用的。 正在使用: '$1'。 說明: 你可以用非$1 'headingmode' 數值，在多個部件中用 'ordermethod' 。第一個部是用來作標題。例如在分類標題中用 'ordermethod=category,<i>comp</i>' (<i>comp</i>是另外一個部件) 。",
	/**
	 * $0: 'debug' value
	*/
	'dpl2_debug_' . DPL2_WARN_DEBUGPARAMNOTFIRST => "警告: 'debug=$0' 不是第一個在DPL元素嘅第一位置。新的除錯設定在所有參數都能處理和檢查前都不會應用。",

	// OTHERS
	/**
	 * $0: SQL query executed to generate the dynamic page list
	*/
	'dpl2_debug_' . DPL2_QUERY => '查訽: <code>$0</code>',

	/*
	   Output formatting
	*/
	/**
	 * $1: number of articles
	*/
	'dpl2_articlecount' => '在這個標題中有$1篇條目。'
);
$wgDPL2Messages['zh-yue'] = array(
	/*
		Debug
	*/
	// (FATAL) ERRORS
	/**
	 * $0: 'namespace' or 'notnamespace'
	 * $1: wrong parameter given by user
	 * $3: list of possible titles of namespaces (except pseudo-namespaces: Media, Special)
	 */
	'dpl2_debug_' . DPL2_ERR_WRONGNS => "錯誤: 錯嘅 '$0' 參數: '$1'! 幫助:  <code>$0= <i>空字串</i> (主)$3</code>。",
	/**
	 * $0: max number of categories that can be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOMANYCATS => '錯誤: 太多分類! 最大值: $0。 幫助: 增加 <code>$wgDPL2MaxCategoryCount</code> 嘅值去指定更多嘅分類或者設定 <code>$wgDPL2AllowUnlimitedCategories=true</code> 以解除限制。 (當加上 <code>DynamicPageList2.php</code>之後，響<code>LocalSettings.php</code>度設定變數。)',
	/**
	 * $0: min number of categories that have to be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOFEWCATS => '錯誤: 太少分類! 最小值: $0. 幫助: 減少 <code>$wgDPL2MinCategoryCount</code> 嘅值去指定更少嘅分類。 (當加上 <code>DynamicPageList2.php</code>之後，響<code>LocalSettings.php</code>度設定一個合適嘅變數。)',
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTNOINCLUDEDCATS => "錯誤: 如果你想去用 'addfirstcategorydate=true' 或者 'ordermethod=categoryadd' ，你需要包含最少一個分類!",
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTMORETHAN1CAT => "錯誤: 如果你包含多過一個分類，你唔可以用 'addfirstcategorydate=true' 或者 'ordermethod=categoryadd'!",
	'dpl2_debug_' . DPL2_ERR_MORETHAN1TYPEOFDATE => '錯誤: 你唔可以響一個時間度加入多個一種嘅日期!',
	/**
	 * $0: param=val that is possible only with $1 as last 'ordermethod' parameter
	 * $1: last 'ordermethod' parameter required for $0
	*/
	'dpl2_debug_' . DPL2_ERR_WRONGORDERMETHOD => "錯誤: 你只可以用 'ordermethod=[...,]$1' 響 '$0' 上!",
	/**
	 * $0: prefix_dpl_clview where 'prefix' is the prefix of your mediawiki table names
	 * $1: SQL query to create the prefix_dpl_clview on your mediawiki DB
	*/
	'dpl2_debug_' . DPL2_ERR_NOCLVIEW => $wgDPL2Messages['en']['dpl2_debug_' . DPL2_ERR_NOCLVIEW],
	
	// WARNINGS
	/**
	 * $0: unknown parameter given by user
	 * $1: list of DPL2 available parameters separated by ', '
	*/
	'dpl2_debug_' . DPL2_WARN_UNKNOWNPARAM => "警告: 不明嘅參數 '$0' 被忽略。 幫助: 可用嘅參數: <code>$1</code>。",
	/**
	 * $3: list of valid param values separated by ' | '
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM => "警告: 錯誤嘅 '$0' 參數: '$1'! 用緊預設嘅: '$2'。 幫助: <code>$0= $3</code>。",
	/**
	 * $0: param name
	 * $1: wrong param value given by user
	 * $2: default param value used instead by program
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM_INT => "警告: 錯誤嘅 '$0' 參數: '$1'! 用緊預設嘅: '$2' (冇上限)。 幫助: <code>$0= <i>空字串</i> (冇上限) | n</code>, <code>n</code>係一個正整數。",
	'dpl2_debug_' . DPL2_WARN_NORESULTS => '警告: 無結果!',
	'dpl2_debug_' . DPL2_WARN_CATOUTPUTBUTWRONGPARAMS => "警告: 加入* 參數 ('adduser', 'addeditdate', 等)' 係對 'mode=category' 冇作用嘅。只有頁空間名／標題至可以響呢個模式度睇到。",
	/**
	 * $0: 'headingmode' value given by user
	 * $1: value used instead by program (which means no heading)
	*/
	'dpl2_debug_' . DPL2_WARN_HEADINGBUTSIMPLEORDERMETHOD => "警告: 響單一部件中， 'ordermethod' 度用 'headingmode=$0' 係冇作用嘅。 用緊: '$1'。 幫助: 你可以用非$1 'headingmode' 數值，響多個部件中用 'ordermethod' 。第一個部件係用嚟做標題。例如響分類標題度用 'ordermethod=category,<i>comp</i>' (<i>comp</i>係另外一個部件) 。",
	/**
	 * $0: 'debug' value
	*/
	'dpl2_debug_' . DPL2_WARN_DEBUGPARAMNOTFIRST => "警告: 'debug=$0' 唔係第一個響DPL元素嘅第一位。新嘅除錯設定響所有參數都能夠處理同檢查之前都唔會應用。",

	// OTHERS
	/**
	 * $0: SQL query executed to generate the dynamic page list
	*/
	'dpl2_debug_' . DPL2_QUERY => '查訽: <code>$0</code>',

	/*
	   Output formatting
	*/
	/**
	 * $1: number of articles
	*/
	'dpl2_articlecount' => '響呢個標題度有$1篇文。'
);
$wgDPL2Messages['zh-hk'] = $wgDPL2Messages['zh-tw'];
$wgDPL2Messages['zh-sg'] = $wgDPL2Messages['zh-cn'];
?>
