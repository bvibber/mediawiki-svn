<?php

/**
 * Messages file for the InputBox extension
 *
 * @addtogroup Extensions
 */

/**
 * Get all extension messages
 *
 * @return array
 */
function efInputBoxMessages() {
	$messages = array(

'en' => array(
	'inputbox-error-no-type'  => 'You have not specified the type of input box to create.',
	'inputbox-error-bad-type' => 'Input box type "$1" not recognised. Please specify "create", "comment", "search" or "search2".',
	'tryexact'                => 'Try exact match',
	'searchfulltext'          => 'Search full text',
	'createarticle'           => 'Create article',
),

'ar' => array(
	'inputbox-error-no-type'  => 'لم تقم بتحديد نوع صندوق الإدخال للإنشاء.',
	'inputbox-error-bad-type' => 'نوع صندوق الإدخال "$1" لم يتم التعرف عليه. من فضلك حدد "create"، "comment"، "search" أو "search2".',
	'tryexact'                => 'ابحث عن عنوان مطابق',
	'searchfulltext'          => 'ابحث في النص الكامل',
	'createarticle'           => 'إنشاء مقالة',
),

'az' => array(
	'createarticle'  => 'Məqalə yarat',
),

'be' => array(
	'createarticle'  => 'Пачаць артыкул',
),

'bg' => array(
	'tryexact'       => 'Пълно и точно съвпадение',
	'searchfulltext' => 'Претърсване на целия текст',
	'createarticle'  => 'Създаване на статия',
),

'bn' => array(
	'inputbox-error-no-type'  => 'আপনি ইনপুট বক্স তৈরির জন্য ইনপুট বক্সের ধরণ নির্ধারণ করেননি।',
	'inputbox-error-bad-type' => '"$1" ধরণের ইনপুট বক্স নেই। দয়া করে "create", "comment", "search" অথবা "search2" নির্ধারণ করুন।',
	'tryexact'                => 'ঠিক এই নামের নিবন্ধে যান',
	'searchfulltext'          => 'সব বিষয়বস্তুতে খুঁজুন',
	'createarticle'           => 'নিবন্ধ শুরু করুন',
),

'br' => array(
	'tryexact'       => 'Klask ma klotfe rik',
	'searchfulltext' => 'Klask an destenn a-bezh',
	'createarticle'  => 'Krouiñ pennad',
),

'ca' => array(
	'tryexact'       => 'Prova una coincidència exacta',
	'searchfulltext' => 'Cerca un text sencer',
	'createarticle'  => 'Crea un article',
),

'cs' => array(
	'tryexact'       => 'Vyzkoušet přesné hledání',
	'searchfulltext' => 'Plnotextové hledání',
	'createarticle'  => 'Vytvořit článek',
),

'cv' => array(
	'createarticle' => 'Çĕнĕ статья çыр',
),

'da' => array(
	'tryexact'       => 'Forsøg eksakt søgning:',
	'searchfulltext' => 'Gennemsøge hele teksten',
	'createarticle'  => 'Oprette side',
),

'de' => array(
	'inputbox-error-no-type'  => 'Du hast keinen Inputbox-Typ angegeben.',
	'inputbox-error-bad-type' => 'Der Inputbox-Typ „$1“ ist unbekannt. Bitte gib einen gültigen Typ an: „create“, „comment“, „search“ oder „search2“.',
	'tryexact'                => 'Versuche exakte Suche:',
	'searchfulltext'          => 'Gesamten Text durchsuchen',
	'createarticle'           => 'Seite anlegen',
),

'dsb' => array(
	'tryexact'                => 'Nawłos pytaś:',
	'searchfulltext'          => 'Ceły tekst pytaś',
	'createarticle'           => 'Nastawk natworiś',
),

'el' => array(
	'tryexact'       => 'Δοκιμάστε την επακριβή αντιστοιχία.',
	'searchfulltext' => 'Αναζήτηση με το πλήρες κείμενο',
	'createarticle'  => 'Δημιουργία άρθρου',
),

'eo' => array(
	'tryexact'       => 'Provu ekzaktan trafon',
	'searchfulltext' => 'Serĉu plentekste',
	'createarticle'  => 'Kreu artikolon',
),
'es' => array(
	'tryexact'                => 'Buscar título exacto',
	'searchfulltext'          => 'Buscar por texto completo',
	'createarticle'           => 'Crear artículo',
),

'eu' => array(
	'tryexact'       => 'Izenburu zehatza bilatu',
	'searchfulltext' => 'Testu osoa bilatu',
	'createarticle'  => 'Artikulua sortu',
),

'ext' => array(
	'createarticle'           => 'Creal artículu',
),

'fa' => array(
	'tryexact'       => 'مطابقت نظیر به نظیر را بیازما',
	'searchfulltext' => 'جستجوی کل متن',
	'createarticle'  => 'ایجاد مقاله',
),

'fi' => array(
	'tryexact'       => 'Yritä tarkkaa osumaa',
	'searchfulltext' => 'Etsi koko tekstiä',
	'createarticle'  => 'Luo sivu',
),

'fiu-vro' => array(
	'tryexact'       => 'Täpsä otsminõ',
	'searchfulltext' => 'Otsiq terveq tekst',
	'createarticle'  => 'Luuq leht',
),

'fo' => array(
	'createarticle'           => 'Stovna grein',
),

'fr' => array(
	'inputbox-error-no-type'  => 'Vous n’avez pas précisé le type de la boîte d’entrée à créer.',
	'inputbox-error-bad-type' => 'Type de boîte entrée $1 non reconnue. Indiquez l\'option \'\'create\'\', \'\'comment\'\', \'\'search\'\' ou \'\'searche2\'\'.',
	'tryexact'                => 'Essayez la correspondance exacte.',
	'searchfulltext'          => 'Recherche en texte intégral',
	'createarticle'           => 'Créer l’article',
),

'fur' => array(
	'createarticle' => 'Cree vôs',
),

'ga' => array(
	'tryexact'       => 'Déan iarracht ar meaitseáil cruinn',
	'searchfulltext' => 'Cuardaigh sa téacs iomlán',
	'createarticle'  => 'Cruthaigh alt',
),

'gl' => array(
	'inputbox-error-no-type'  => 'Non se especificou o tipo de caixa de entrada para crear.',
	'inputbox-error-bad-type' => 'A caixa de entrada de tipo "$1" non se recoñece. Especifique "crear", "comentario", "procurar" ou "procurar2".',
	'tryexact'                => 'Tentar coincidencias exactas',
	'searchfulltext'          => 'Buscar o texto completo',
	'createarticle'           => 'Crear artigo',
),

'hak' => array(
	'tryexact'       => 'Sòng-chhṳ chîn-khok phit-phi',
	'searchfulltext' => 'Chhiòn vùn-kiám chhìm-cháu',
	'createarticle'  => 'Kien-li̍p vùn-chông',
),

'he' => array(
	'tryexact'       => 'לדף בשם זה',
	'searchfulltext' => 'חיפוש בתוכן הדפים',
	'createarticle'  => 'יצירת הדף',
),

'hr' => array(
	'tryexact'       => 'Pokušaj naći točan pogodak',
	'searchfulltext' => 'Traži po cjelokupnom tekstu',
	'createarticle'  => 'Stvori članak',
),

'hsb' => array(
	'inputbox-error-no-type'  => 'Njesy typ zapodatneho kašćika podał.',
	'inputbox-error-bad-type' => 'Typ zapodatneho kašćika "$1" je njeznaty. Prošu podaj płaćiwy typ: "create", "comment", "search" abo "search2".',
	'tryexact'                => 'Dokładne pytanje spytać',
	'searchfulltext'          => 'Dospołny tekst pytać',
	'createarticle'           => 'Nastawk wutworić',
),

'hu' => array(
	'searchfulltext'          => 'Keresés a teljes szövegben',
	'createarticle'           => 'Szócikk létrehozása',
),

'hy' => array(
	'createarticle'           => 'Հոդված ստեղծել',
),

'id' => array(
	'tryexact'       => 'Coba pencocokan eksak',
	'searchfulltext' => 'Cari di teks lengkap',
	'createarticle'  => 'Buat artikel',
),

'is' => array(
	'createarticle'           => 'Búa til grein',
),

'it' => array(
	'tryexact'                => 'Cerca corrispondenza esatta',
	'searchfulltext'          => 'Ricerca nel testo',
	'createarticle'           => 'Crea voce',
),

'ja' => array(
	'tryexact'       => '一致する項目を検索',
	'searchfulltext' => '全文検索',
	'createarticle'  => '項目を作成',
),

'jv' => array(
	'searchfulltext' => 'Golèk ing tèks jangkep',
	'createarticle'  => 'Damel artikel',
),

'ka' => array(
	'tryexact'       => 'სცადეთ ზუსტი ძიება',
	'searchfulltext' => 'სრული ტექსტის ძიება',
	'createarticle'  => 'სტატიის შექმნა',
),

'kaa' => array(
	'createarticle'           => 'Bet jaratıw',
),

'kab' => array(
	'tryexact'       => 'Nadi ɣef uzwel kif-kif',
	'searchfulltext' => 'Nadi aḍris ettmam',
	'createarticle'  => 'Xleq amagrad',
),

'kk-kz' => array(
	'inputbox-error-no-type'  => 'Жасалатын енгізу жолағының түрін келтірмепсіз.',
	'inputbox-error-bad-type' => 'Енгізу жолақтың «$1» түрі танылмады. Тек «create», «comment», «search» не «search2» деген түрлерді келтіріңіз.',
	'tryexact'                => 'Дәл сәйкесін сынап көріңіз',
	'searchfulltext'          => 'Толық мәтінімен іздеу',
	'createarticle'           => 'Бетті бастау',
),

'kk-tr' => array(
	'inputbox-error-no-type'  => 'Jasalatın engizw jolağınıñ türin keltirmepsiz.',
	'inputbox-error-bad-type' => 'Engizw jolaqtıñ «$1» türi tanılmadı. Tek «create», «comment», «search» ne «search2» degen türlerdi keltiriñiz.',
	'tryexact'                => 'Däl säýkesin sınap köriñiz',
	'searchfulltext'          => 'Tolıq mätinimen izdew',
	'createarticle'           => 'Betti bastaw',
),

'kk-cn' => array(
	'inputbox-error-no-type'  => 'جاسالاتىن ەنگٸزۋ جولاعىنىڭ تٷرٸن كەلتٸرمەپسٸز.',
	'inputbox-error-bad-type' => 'ەنگٸزۋ جولاقتىڭ «$1» تٷرٸ تانىلمادى. تەك «create», «comment», «search» نە «search2» دەگەن تٷرلەردٸ كەلتٸرٸڭٸز.',
	'tryexact'                => 'دٵل سٵيكەسٸن سىناپ كٶرٸڭٸز',
	'searchfulltext'          => 'تولىق مٵتٸنٸمەن ٸزدەۋ',
	'createarticle'           => 'بەتتٸ باستاۋ',
),

'ko' => array(
	'searchfulltext' => '전체 글 검색',
	'createarticle'  => '문서 만들기',
),

'ksh' => array(
	'tryexact'       => 'Versök en akkurate Üvvereinstimmung:',
	'searchfulltext' => 'Sök durch dä janze Tex',
	'createarticle'  => 'Atikkel enrichte',
),

'ku-latn' => array(
	'createarticle' => 'Gotarê biafirîne',
),

'la' => array(
	'createarticle'           => 'Paginam creare',
),

'lg' => array(
	'createarticle' => 'Wandika omuko',
),

'li' => array(
	'tryexact'                => 'Perbeer exacte euvereinkoms',
	'searchfulltext'          => 'Zeuk dèr volledige tèks',
	'createarticle'           => 'Maak \'n artikel aan',
),

'lo' => array(
	'createarticle' => 'ສ້າງບົດຄວາມ',
),

'lt' => array(
	'tryexact'       => 'Mėginti tikslų atitikimą',
	'searchfulltext' => 'Ieškoti pilno teksto',
	'createarticle'  => 'Kurti straipsnį',
),

'lv' => array(
	'createarticle' => 'Izveidot rakstu',
),

'mk' => array(
	'tryexact'       => 'Обиди се точно',
	'searchfulltext' => 'Барај низ целиот текст',
	'createarticle'  => 'Создади статија',
),

'nan' => array(
	'searchfulltext'          => 'Chhiau choan-bûn',
),

'nds' => array(
	'tryexact'       => 'exakte Söök versöken',
	'searchfulltext' => 'in’n Vulltext söken',
	'createarticle'  => 'Artikel anleggen',
),

'nl' => array(
	'inputbox-error-no-type'  => 'U heeft het type inputbox niet aangegeven. Zie [http://www.mediawiki.org/wiki/Extension:Inputbox MediaWiki.org] voor meer informatie.',
	'inputbox-error-bad-type' => 'Inputbox-type "$1" niet herkend. Gebruik "create", "comment", "search" of "search2".',
	'tryexact'                => 'Zoeken op exacte overeenkomst',
	'searchfulltext'          => 'Volledige tekst doorzoeken',
	'createarticle'           => 'Nieuwe pagina maken',
),

'nn' => array(
	'tryexact'       => 'Prøv nøyaktig treff',
	'searchfulltext' => 'Søk i all tekst',
	'createarticle'  => 'Lag side',
),

'no' => array(
	'inputbox-error-no-type'  => 'Du har ikke oppgitt hva slags inputboks du vil lage.',
	'inputbox-error-bad-type' => 'Inputboks av typen «$1» gjenkjennes ikke. Vennligst velg «create», «comment», «search» eller «search2».',
	'tryexact'                => 'Prøv nøyaktig treff',
	'searchfulltext'          => 'Søk full tekst',
	'createarticle'           => 'Opprett artikkel',
),

'oc' => array(
	'inputbox-error-no-type'  => 'Avètz pas precisat lo tipe de la boita de dintrada de crear.',
	'inputbox-error-bad-type' => 'Tipe de boita dintradad $1 pas reconeguda. Indicatz l\'opcion \'\'create\'\', \'\'comment\'\', \'\'search\'\' o \'\'searche2\'\'.',
	'tryexact'                => 'Ensajatz la correspondéncia exacta',
	'searchfulltext'          => 'Recèrca en tèxt integral',
	'createarticle'           => 'Crear l’article',
),

'pl' => array(
	'inputbox-error-no-type'  => 'Typ pola wejściowego nie został określony',
	'inputbox-error-bad-type' => 'Typ "$1" pola wejściowego nie został rozpoznany. Proszę wybrać "create", "comment", "search" lub "search2".',
	'tryexact'                => 'Użyj dokładnego wyrażenia',
	'searchfulltext'          => 'Szukaj w całych tekstach',
	'createarticle'           => 'Utwórz artykuł',
),

'pms' => array(
	'inputbox-error-no-type'  => 'A l\'ha nen dit che sòrt ëd quàder ëd caria dat ch\'a debia fesse.',
	'inputbox-error-bad-type' => 'La sòrt ëd quàder "$1" a l\'é nen conossùa. Për piasì, ch\'a sërna antra "create", "comment", "search" ò pura "search2".',
	'tryexact'                => 'Sërca che a sia pròpe parej',
	'searchfulltext'          => 'Sërca an tut ël test',
	'createarticle'           => 'Crea n\'artìcol',
),

'pt' => array(
	'inputbox-error-no-type'  => 'Você não especificou o tipo de box de inserção a ser criado.',
	'inputbox-error-bad-type' => 'O box de inserção de tipo "$1" não foi reconhecido. Por gentileza, especifique "create", "comment", "search" ou "search2".',
	'tryexact'       => 'Tentar a exata expressão',
	'searchfulltext' => 'Pesquisar no texto completo',
	'createarticle'  => 'Criar página',
),

'ro' => array(
	'tryexact'       => 'Încearcă varianta exactă',
	'searchfulltext' => 'Caută textul întreg',
	'createarticle'  => 'Crează articol',
),

'roa-rup' => array(
	'createarticle' => 'Adrats articlu',
),

'ru' => array(
	'tryexact'       => 'Строгий поиск',
	'searchfulltext' => 'Полнотекстовый поиск',
	'createarticle'  => 'Создать статью',
),

'sd' => array(
	'createarticle'           => 'نئون مضمون لکو',
),

'sk' => array(
	'tryexact'       => 'Skúste presné vyhľadávanie',
	'searchfulltext' => 'Fulltextové vyhľadávanie',
	'createarticle'  => 'Vytvoriť stránku',
),

'sl' => array(
	'tryexact'       => 'Poskusite z natančnim zadetkom',
	'searchfulltext' => 'Preišči vse besedilo',
	'createarticle'  => 'Ustvarite stran',
),

'sq' => array(
	'tryexact'       => 'Kërko përputhje të plotë',
	'searchfulltext' => 'Kërko tekstin e plotë',
	'createarticle'  => 'Krijo artikull',
),

'sr-ec' => array(
	'tryexact'       => 'Покушај тачно',
	'searchfulltext' => 'Претражи цео текст',
	'createarticle'  => 'Направи чланак',
),

'sr-el' => array(
	'tryexact'       => 'Pokušaj tačno',
	'searchfulltext' => 'Pretraži ceo tekst',
	'createarticle'  => 'Napravi članak',
),

'ss' => array(
	'createarticle'           => 'Kúdála intfo',
),

'su' => array(
	'searchfulltext'          => 'Sungsi dina téks lengkap',
	'createarticle'           => 'Jieun artikel',
),

'sv' => array(
	'inputbox-error-no-type'  => 'Du har inte angivit vilken typ av inputbox som ska skapas..',
	'inputbox-error-bad-type' => '"$1" är inte en känd typ av inputbox. Giltiga typer är "create", "comment", "search" och "search2".',
	'tryexact'       => 'Försök hitta exakt matchning',
	'searchfulltext' => 'Fulltextsökning',
	'createarticle'  => 'Skapa artikel',
),

'te' => array(
	'tryexact'      => 'ఖచ్చితమైన పోలిక కొరకు ప్రయత్నించు',
	'createarticle' => 'వ్యాసాన్ని సృష్టించు',
),

'th' => array(
	'tryexact'       => 'ค้นหาตรงทุกตัวอักษร',
	'searchfulltext' => 'ค้นหาข้อมูล',
	'createarticle'  => 'สร้างเนื้อหา',
),

'tn' => array(
	'createarticle' => 'Kwala mokwalo',
),

'tr' => array(
	'createarticle' => 'Sayfayı oluştur',
),

'uk' => array(
	'tryexact'       => 'Строгий пошук',
	'searchfulltext' => 'Повнотекстовий пошук',
	'createarticle'  => 'Створити статтю',
),

'ur' => array(
	'searchfulltext' => 'تلاش ِکل متن',
	'createarticle'  => 'نیا مضمون',
),

'uz' => array(
	'createarticle' => 'Maqola kiritish',
),

'vec' => array(
	'createarticle' => 'Crea voçe',
),

'vi' => array(
	'tryexact'       => 'Thử tìm đoạn văn khớp chính xác với từ khóa',
	'searchfulltext' => 'Tìm toàn văn',
	'createarticle'  => 'Viết bài mới',
),

'wa' => array(
	'createarticle' => 'Ahiver årtike',
),

'yue' => array(
	'inputbox-error-no-type'  => '你重未指定開輸入盒嘅指定類型。',
	'inputbox-error-bad-type' => '輸入盒類型"$1"認唔到。請指定"create"、"comment"、"search"或"search2"。',
	'tryexact'                => '試吓精確嘅比較',
	'searchfulltext'          => '搵全文',
	'createarticle'           => '建立文章',
),

'zh-classical' => array(
	'inputbox-error-no-type'  => '汝未定輸入盒之類也。',
	'inputbox-error-bad-type' => '輸入盒之類"$1"無認耳。指"create"、"comment"、"search"或"search2"之。',	
	'tryexact'       => '查全合',
	'searchfulltext' => '尋全文',
	'createarticle'  => '撰文',
),

'zh-hans' => array(
	'inputbox-error-no-type'  => '您尚未指定创建输入箱的指定类型。',
	'inputbox-error-bad-type' => '输入箱类型"$1"无法辨识。请指定"create"、"comment"、"search"或"search2"。',
	'tryexact'       => '尝试精确匹配',
	'searchfulltext' => '全文搜索',
	'createarticle'  => '建立文章',
),

'zh-hant' => array(
	'inputbox-error-no-type'  => '您尚未指定創建輸入箱的指定類型。',
	'inputbox-error-bad-type' => '輸入箱類型"$1"無法辨識。請指定"create"、"comment"、"search"或"search2"。',
	'tryexact'       => '嘗試精確匹配',
	'searchfulltext' => '全文檢索',
	'createarticle'  => '建立文章',
),

);

	/* Kazakh default, fallback to kk-kz */
	$messages['kk'] = $messages['kk-kz'];
	/* Min nan default, fallback to nan */
	$messages['zh-min-nan'] = $messages['nan'];
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
