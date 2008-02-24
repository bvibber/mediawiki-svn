<?php

/**
 * Internationalisation file for the Username Blacklist extension
 *
 * @author Rob Church <robchur@gmail.com>
 * @addtogroup Extensions
 */

$messages = array();

/** English
 * @author Rob Church
 */
$messages['en'] = array(
	'usernameblacklist-desc'  => 'Adds a [[MediaWiki:Usernameblacklist|username blacklist]] to restrict the creation of user accounts matching one or more regular expressions',
	'blacklistedusername'     => 'Blacklisted username',
	'blacklistedusernametext' => 'The user name you have chosen matches the [[MediaWiki:Usernameblacklist|list of blacklisted usernames]]. Please choose another name.',
	'usernameblacklist'       => '<pre>
# Entries in this list will be used as part of a regular expression when
# blacklisting usernames from registration. Each item should be part of
# a bulleted list, e.g.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'The following {{PLURAL:$1|line|lines}} in the username blacklist {{PLURAL:$1|is|are}} invalid; please correct {{PLURAL:$1|it|them}} before saving:',
);

/** Aragonese (Aragonés)
 * @author Juanpabl
 */
$messages['an'] = array(
	'usernameblacklist-desc'          => "Usa una [[MediaWiki:Usernameblacklist|lista negra de nombres d'usuario]] ta restrinchir a creyazión de cuentas d'usuario que consonen con una u más espresions regulars",
	'blacklistedusername'             => "Nombre d'usuario en a lista negra",
	'blacklistedusernametext'         => "O nombre d'usuario que ha trigato concuerda con belún d'os nombre en a [[MediaWiki:Usernameblacklist|lista negra]]. Por fabor, eslicha un atro nombre.",
	'usernameblacklist'               => "<pre>
# As linias d'ista lista se ferán serbir como espresions regulars (regexp)
# ta pribar o rechistro de bels nombres d'usuario.
# Cada ítem ha d'estar aintro d'una lista no ordenata. Por exemplo:
#
# * Falso
# * [Pp]reba
</pre>",
	'usernameblacklist-invalid-lines' => "{{PLURAL:$1|A linia|As linias}} siguients d'a lista negra de nombres d'usuarios no {{PLURAL:$1|ye|son}} correutas; por fabor, corricha-{{PLURAL:$1|lo|los}} antes d'alzar-la:",
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'usernameblacklist-desc'          => 'يضيف [[MediaWiki:Usernameblacklist|قائمة سوداء لأسماء المستخدمين]] لتحديد إنشاء حسابات المستخدم التي تطابق تعبيرا منتظما أو أكثر',
	'blacklistedusername'             => 'اسم مستخدم في القائمة السوداء',
	'blacklistedusernametext'         => 'اسم المستخدم الذي اخترته يطابق [[MediaWiki:Usernameblacklist|
قائمة أسماء المستخدمين السوداء]]. من فضلك اختر اسما آخر.',
	'usernameblacklist'               => '<pre>
# المدخلات في هذه القائمة ستستخدم كجزء من تعبير منتظم عند
# منع أسماء المستخدمين في القائمة السوداء من التسجيل. كل مدخلة يجب أن تكون جزءا من
# قائمة مرقمة، كمثال.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|السطر التالي|السطور التالية}} في قائمة اسم المستخدم السوداء {{PLURAL:$1|غير صحيح|غير صحيحة}} ؛ من فضلك {{PLURAL:$1|صححه|صححها}} قبل الحفظ:',
);

/** Asturian (Asturianu)
 * @author Esbardu
 */
$messages['ast'] = array(
	'blacklistedusername'             => "Nome d'usuariu na llista negra",
	'blacklistedusernametext'         => "El nome d'usuariu qu'escoyisti ta na [[MediaWiki:Usernameblacklist|llista negra de nomes d'usuariu]]. Por favor, escueyi otru nome.",
	'usernameblacklist'               => "<pre>
# Les entraes d'esta llista sedrán usaes como parte d'una espresión regular
# pa impidir la identificación d'usuarios de la llista negra. Cada elementu habría
# ser parte d'una llista de marcadores, p. ex.
#
# * Foo
# * [Bb]ar
</pre>",
	'usernameblacklist-invalid-lines' => "{{PLURAL:$1|La siguiente llinia|Les siguientes llinies}} na llista blanca de nomes d'usuariu nun {{PLURAL:$1|ye válida|son válides}}; por favor {{PLURAL:$1|corríxila enantes de guardala:|corríxiles enantes de guardales:}}",
);

/** Kotava (Kotava)
 * @author Wikimistusik
 */
$messages['avk'] = array(
	'blacklistedusername'             => 'Favesikyolt moe ebeltavexala',
	'blacklistedusernametext'         => 'Rinon kiblan favesikyolt va tan moe [[MediaWiki:Usernameblacklist|favesikafa ebeltavexala]] vadjer. Va ar vay kiblal !',
	'usernameblacklist-invalid-lines' => 'Vlevef {{PLURAL:$1|conha|conha se}} moe favesikafa ebeltavexala {{PLURAL:$1|tir|tid}} meenafa; abdi giwara va {{PLURAL:$1|in|sin}} tuenal !',
);

$messages['bcl'] = array(
	'blacklistedusername' => 'Blacklisted na username',
);

/** Belarusian (Беларуская)
 * @author Yury Tarasievich
 */
$messages['be'] = array(
	'blacklistedusername'             => 'Імя ўдзельніка ў "чорным спісе"',
	'blacklistedusernametext'         => 'Імя ўдзельніка, выбранае вамі, знаходзіцца ў [[MediaWiki:Usernameblacklist|"чорным спісе" імёнаў]]. Выберыце іншае імя ўдзельніка.',
	'usernameblacklist'               => '<pre>
# Складнікі гэтага пераліку будуць выкарыстаны як частка рэгулярнага выразу для забароны рэгістрацыі з пэўнымі імёнамі ўдзельніка.
# Кожны складнік павінен быць часткай пунктаванага спісу,
# напрыклад:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Наступны{{PLURAL:$1| радок|я радкі}} ў "чорным спісе" ўдзельнікаў некарэктны{{PLURAL:$1||я}}; папраўце {{PLURAL:$1|яго|іх}} перад запісваннем:',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'blacklistedusernametext'         => 'Избраното потребителско име съвпада със запис от [[MediaWiki:Usernameblacklist|списъка с непозволени имена]]. Изберете друго.',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|Следният ред|Следните редове}} в черния списък за потребителски имена {{PLURAL:$1|е невалиден|са невалидни}}; Необходимо е да {{PLURAL:$1|бъде поправен|бъдат поправени}} преди да {{PLURAL:$1|бъде съхранен|бъдат съхранени}}:',
);

/** Bengali (বাংলা)
 * @author Bellayet
 * @author Zaheen
 */
$messages['bn'] = array(
	'usernameblacklist-desc'          => 'একটি [[MediaWiki:Usernameblacklist|ব্যবহারকারী কালোতালিকা]] যোগ করে যাতে এক বা একাধিক রেগুলার এক্সপ্রেশনের সাথে মিলে যায় এমন নামের ব্যবহারকারী অ্যাকাউন্ট সৃষ্টি করা না যায়',
	'blacklistedusername'             => 'নিষিদ্ধ ঘোষিত ব্যবহারকারী নাম',
	'blacklistedusernametext'         => 'ব্যবহারকারীর নাম [[MediaWiki:Usernameblacklist|কালতালিকাভুক্ত ব্যবহারকারীর নাম সমূহের]] সাথে মিলেছে। দয়াকরে অন্য নাম পছন্দ করুন।',
	'usernameblacklist'               => '<pre> # এই তালিকায় ভুক্তি সমূহ রেগুলার এক্সপ্রেশনের অংশ হিসেবে ব্যবহৃত হবে যেখানে # রেজিষ্ট্রশন থেকে নিষিদ্ধ ব্যবহারকারী নামসমূহ। প্রতিটি উপাদান # একটি বুলেট তালিকার অংশ হয়ে থাকবে, অর্থাৎ # # * Foo # * [Bb]ar </pre>',
	'usernameblacklist-invalid-lines' => 'এই {{PLURAL:$1|লাইন|লাইনসমূহ}} নিষিদ্ধ ব্যবহারকারী নাম তালিকাভুক্ত {{PLURAL:$1|নাম|নামসমূহ}} অসিদ্ধ; দয়াকরে সংরক্ষণ করার পূর্বে {{PLURAL:$1|এটি|এগুলো}} ঠিক করুন:',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'usernameblacklist-desc'          => 'Ouzhpennañ a ra ul [[MediaWiki:Usernameblacklist|listenn zu an implijerien]] evit mirout ouzh kontoù implijer da glotañ gant ul lavarenn pe lavarennoù reizh zo',
	'blacklistedusername'             => 'Anvioù implijerien war al listenn zu',
	'blacklistedusernametext'         => "Emañ an anv hoc'h eus dibabet war al [[MediaWiki:Usernameblacklist|listenn zu]]. Dibabit un anv all mar plij.",
	'usernameblacklist'               => "<pre>
# Implijet e vo ar penngerioù er roll-mañ evel elfennoù eus lavarennoù reizh
# a-benn mirout ouzh an anvioù berzet d'en em enrollañ. Dleout a rafe pep elfenn bezañ en ur 
# roll poentaouet, da sk.
#
# * Foo
# * [Bb]ar
</pre>",
	'usernameblacklist-invalid-lines' => 'Fall {{PLURAL:$1|eo|eo}} al {{PLURAL:$1|linenn|linennoù}} da-heul eus listenn zu an anvioù implijerien; reizhit {{PLURAL:$1|anezhañ|anezho}} a-raok enrollañ mar plij :',
);

/** Catalan (Català)
 * @author SMP
 */
$messages['ca'] = array(
	'blacklistedusername'             => 'Nom no permès',
	'blacklistedusernametext'         => "El nom d'usuari que heu escollit forma part de la [[MediaWiki:Usernameblacklist|llista de noms no permesos]]. Escolliu-ne un altre de diferent, si us plau.",
	'usernameblacklist'               => "<pre>
# Les línies d'aquesta lliste seran usades com a expressió regular (regexp)
# per a prohibir el registre de certs noms d'usuari.
# Cada ítem ha d'estar dins una llista no ordenada. Per exemple:
#
# * Aquesta
# * [Pp]rova
</pre>",
	'usernameblacklist-invalid-lines' => "{{PLURAL:$1|La següent línia|Les següents línies}} de la llista negra de noms d'usuari no {{PLURAL:$1|és vàlida|són vàlides}}; si us plau, corregiu{{PLURAL:$1|-la|-les}} abans de desar-ho:",
);

/** Czech (Česky)
 * @author Danny B.
 * @author Matěj Grabovský
 */
$messages['cs'] = array(
	'usernameblacklist-desc'          => 'Přidává [[MediaWiki:Usernameblacklist|černou listinu uživatelských jmen]], aby se omezila tvorba uživatelských účtů odpovídajících jednomu nebo více regulárním výrazům',
	'blacklistedusername'             => 'Nepovolené uživatelské jméno',
	'blacklistedusernametext'         => 'Vámi vybrané uživatelské jméno se shoduje s&nbsp;některým ze [[MediaWiki:Usernameblacklist|seznamu nepovolených uživatelských jmen]]. Prosíme, vyberte si jiné jméno.',
	'usernameblacklist'               => '<pre>
# Položky v&nbsp;tomto seznamu budou použity jako části regulárního výrazu
# při kontrole nepovolených uživatelských jmen při registraci.
# Každý výraz by měl být označen jako položka nečíslovaného seznamu, např.:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Následující {{plural:$1|řádka|řádky|řádky}} v&nbsp;seznamu nepovolených uživatelských jmen {{plural:$1|je neplatná|jsou neplatné|jsou neplatné}}; prosíme, opravte {{plural:$1|ji|je|je}} před uložením:',
);

/** Danish (Dansk)
 * @author Morten LJ
 */
$messages['da'] = array(
	'blacklistedusername'             => 'Sortlistet brugernavn',
	'blacklistedusernametext'         => 'Du har valgt et brugernavn som findes på [[MediaWiki:Usernameblacklist|{{SITENAME}}s sorte liste]], vælg venligst et andet.',
	'usernameblacklist'               => "<pre>
# Elementerne i denne liste bliver brugt som del af en ''regular expression''
# når brugernavne sortlistes fra oprettelse. Hvert element bør være en del en
# punktopstilling, fx
#
# * Foo
# * [Bb]ar
</pre>",
	'usernameblacklist-invalid-lines' => 'Nedenstående {{PLURAL:$1|linje|linjer}} i den sorte liste over brugernavne er ugyldige, ret {{PLURAL:$1|den|dem}} venligst før du gemmer:',
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'usernameblacklist-desc'          => 'Ergänzt eine [[MediaWiki:Usernameblacklist|Liste unerwünschter Benutzernamen]], deren Erstellung auf Basis regulärer Ausdrücke unterbunden wird',
	'blacklistedusername'             => 'Benutzername auf der Sperrliste',
	'blacklistedusernametext'         => 'Der gewählte Benutzername steht auf der [[MediaWiki:Usernameblacklist|Liste der unerwünschten Benutzernamen]]. Bitte wähle einen anderen.',
	'usernameblacklist'               => '<pre>
# Einträge in dieser Liste sind Teil eines regulären Ausdrucks,
# der bei der Prüfung von Neuanmeldungen auf unerwünschte Benutzernamen angewendet wird.
# Jede Zeile muss mit einem * beginnen, z.B.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Die {{PLURAL:$1|folgende Zeile|folgenden Zeilen}} in der Liste unerwünschter Benutzernamen {{PLURAL:$1|ist|sind}} ungültig; bitte korrigiere sie vor dem Speichern:',
);

/** German - formal address (Deutsch - formale Anrede)
 * @author Raimond Spekking
 */
$messages['de-formal'] = array(
	'blacklistedusernametext'         => 'Der gewählte Benutzername steht auf der [[MediaWiki:Usernameblacklist|Liste der unerwünschten Benutzernamen]]. Bitte wählen Sie einen anderen.',
	'usernameblacklist-invalid-lines' => 'Die {{PLURAL:$1|folgende Zeile|folgenden Zeilen}} in der Liste unerwünschter Benutzernamen {{PLURAL:$1|ist|sind}} ungültig; bitte korrigieren Sie diese vor dem Speichern:',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'usernameblacklist-desc' => 'Aldonas [[MediaWiki:Usernameblacklist|Forbarlisto de uzantonomoj]] por malpermesi la kreadon de uzantokontoj laŭ unu aŭ pluraj regulesprimoj.',
	'blacklistedusername'    => 'Malpermesita uzantnomo',
);

$messages['eu'] = array(
	'blacklistedusername' => 'Zerrenda beltzeko erabiltzaile izena',
	'blacklistedusernametext' => 'Hautatu duzun erabiltzaile izena [[MediaWiki:Usernameblacklist|zerrenda beltzean]] ageri da. Aukeratu ezazu beste bat.',
);

/** Extremaduran (Estremeñu)
 * @author Better
 */
$messages['ext'] = array(
	'blacklistedusername'     => "Nombri d'usuáriu ena lista negra",
	'blacklistedusernametext' => "El nombri d'usuáriu qu'as lihiu s'alcuentra ena [[MediaWiki:Usernameblacklist|lista negra]]. Pol favol, descohi otru nombri.",
);

/** فارسی (فارسی)
 * @author Huji
 */
$messages['fa'] = array(
	'usernameblacklist-desc'          => 'یک [[MediaWiki:Usernameblacklist|فهرست سیاه نام کاربری]] اضافه می‌کند که برای جلوگیری از ساختن حساب‌های کاربری با الگوهای مشخص به کار می‌رود',
	'blacklistedusername'             => 'نام کاربری غیر مجاز',
	'blacklistedusernametext'         => 'نام کاربری مورد نظر شما در با [[MediaWiki:Usernameblacklist|فهرست سیاه نام‌های کاربری]] مطابقت دارد. لطفاً یک نام کاربری دیگر انتخاب کنید.',
	'usernameblacklist'               => '<pre>
# مدخل‌های این صفحه به عنوان یک الگوی regular expression برای 
# فهرست سیاه هنگام ثبت نام کاربری به کار می‌روند. هر مورد باید
# در یک سطر جدا که با علامت * آغاز شده باشد تعریف گردد، مانند:
#
# * فلان
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|سطر|سطرهای}} زیر از فهرست سیاه نام کاربری غیر مجاز {{PLURAL:$1|است|هستند}}؛ لطفاً {{PLURAL:$1|آن|آن‌ها}} را قبل از ذخیره کردن صفحه اصلاح کنید:',

);

/** Finnish (Suomi)
 * @author Nike
 */
$messages['fi'] = array(
	'blacklistedusername'             => 'Kielletty tunnus',
	'blacklistedusernametext'         => 'Haluamasi tunnus on [[MediaWiki:Usernameblacklist|kiellettyjen tunnusten listalla]]. Valitse toinen nimi.',
	'usernameblacklist'               => '<pre>
# Listan rivit ovat säännöllisiä lausekkeita, jotka estävät niihin sopivien tunnusten luomisen.
# Jokaisen rivin pitää olla järjestelemättömän listan jäseniä. Esimerkikki:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|Seuraava listan rivi ei ole kelvollinen|Seuraavat listan rivit eivät ole kelvollisia}}. Korjaa {{PLURAL:$1|se|ne}} ennen tallentamista.',
);

/** French (Français)
 * @author Seb35
 * @author Sherbrooke
 * @author SPQRobin
 */
$messages['fr'] = array(
	'usernameblacklist-desc'          => "Ajoute une [[MediaWiki:Usernameblacklist|liste noire des noms d'utilisateur]] pour restreindre la création des comptes d'utilisateurs faisant partie d'une ou plusieurs expressions régulières.",
	'blacklistedusername'             => 'Noms d’utilisateurs en liste noire',
	'blacklistedusernametext'         => 'Le nom d’utilisateur que vous avez choisi se trouve sur la
[[MediaWiki:Usernameblacklist|liste des noms interdits]]. Veuillez choisir un autre nom.',
	'usernameblacklist'               => "<pre>
# Les entrées de cette liste seront utilisées en tant qu'expressions rationnelles
# afin d'empêcher la création de noms d'utilisateurs interdits. Chaque item doit
# faire partie d'une liste à puces, par exemple
#
# * Foo
# * [Bb]ar
</pre>",
	'usernameblacklist-invalid-lines' => "{{PLURAL:$1|La ligne suivante|Les lignes suivantes}} de la liste noire des noms d'utilisateurs {{PLURAL:$1|est invalide|sont invalides}} ; veuillez {{PLURAL:$1|la|les}} corriger avant d'enregistrer :",
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'usernameblacklist-desc'          => 'Apond una [[MediaWiki:Usernameblacklist|lista nêre des noms d’utilisator]] por rètrendre la crèacion des comptos d’utilisators étent dens yona ou plusiors èxprèssions règuliéres.',
	'blacklistedusername'             => 'Noms d’utilisator en lista nêre',
	'blacklistedusernametext'         => 'Lo nom d’utilisator que vos éd chouèsi/cièrdu sè trove sur la [[MediaWiki:Usernameblacklist|lista des noms dèfendus]]. Volyéd chouèsir/cièrdre un ôtro nom.',
	'usernameblacklist'               => '<pre>
# Les entrâs de ceta lista seront utilisâs a titro d’èxprèssions règuliéres
# por empachiér la crèacion de noms d’utilisator dèfendus. Châque èlèment dêt
# étre dens una lista de puges, per ègzemplo
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|La legne siuventa|Les legnes siuventes}} de la lista nêre des noms d’utilisator {{PLURAL:$1|est envalida|sont envalides}} ; volyéd {{PLURAL:$1|la|les}} corregiér devant qu’enregistrar :',
);

/** Irish (Gaeilge)
 * @author Alison
 */
$messages['ga'] = array(
	'blacklistedusername'             => 'Ainm úsáideoir sa liosta dubh',
	'blacklistedusernametext'         => "Tá an ainm úsáideoira roghnaítear agat sa [[MediaWiki:Usernameblacklist|liosta na ainm úsáideora toirmiscthe]]. Togh ceann eile, le d'thoil.",
	'usernameblacklist'               => '<pre>
# Beidh na hiontrálacha sa liosta seo in úsáid mar cuid den "slonn rialta" nuair a
# coiseceann ainm úsáideoira as clárúchán. Tá gach mír sonraí cuid den liosta 
# le hurchair, m.sh.
#
# * Fú
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => "Tá {{PLURAL:$1|líne|na líonta}} seo a leanas neamhbhailí sa liosta na ainm úsáideoira; ceartaigh {{PLURAL:$1|é|iad}}  le d'thoil roimh a shábháil:",
);

$messages['gl'] = array(
	'blacklistedusername' => 'Nome de usuario non permitido',
	'blacklistedusernametext' => 'O nome de usuario que elixiu está na [[MediaWiki:Usernameblacklist| lista de nomes de usuario non permitidos]]. Por favor escolla outro nome.',
	'usernameblacklist' => '<pre>
# As entradas desta listaxe empregaranse como parte dunha expresión regular
# ao incluír os nomes de usuario nunha lista negra de rexistro. Cada elemento
# deberá incluírse nunha listaxe sen numerar, p.ex.:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|A liña seguinte|As liñas seguintes}} na listaxe negra de nomes de usuario {{PLURAL:$1|non é válida|non son válidas}}; corríxa{{PLURAL:$1|a|as}} antes de gardar:',
);

$messages['hak'] = array(
	'blacklistedusername' => 'Lie̍t-ngi̍p het-miàng-tân ke yung-fu-miàng',
	'blacklistedusernametext' => 'Ngì só sién-chet ke yung-fu-miàng he lâu [[MediaWiki:Usernameblacklist|Yung-fu-miàng het-miàng-tân lie̍t-péu]] fù-ha̍p. Chhiáng sién-chet nang-ngoi yit-ke miàng-chhṳ̂n.',
);

$messages['hr'] = array(
	'blacklistedusername' => 'Nedozvoljeno suradničko ime',
	'blacklistedusernametext' => 'Ime koje ste izabrali je na popisu [[MediaWiki:Usernameblacklist|nedozvoljenih imena]]. Molimo izaberite drugo ime.',
	'usernameblacklist' => '<pre>
# Zapisi u ovom popisu će biti rabljeni kao dio regularnog izraza pri
# provjeravanju suradničkih imena pri prijavljivanju/registraciji. Svako ime treba navesti kao dio
# popisa, npr:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|$1 slijedeći redak|Slijedeća $1 retka|Slijedećih $1 redova}} u popisu zabranjenih suradničkih imena {{PLURAL:$1|je nevaljan|su nevaljana|je nevaljano}}; molimo ispravite {{PLURAL:$1|ga|ih|ih}} prije snimanja:',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'usernameblacklist-desc'          => 'Přidawa [[MediaWiki:Usernameblacklist|čorneje lisćiny wužiwarjow]], zo by so wutworjenje wužiwarskich kontow wobmjezowało, kotrež jednomu regularnemu wurazej abo wjacorym regularnym wurazom wotpowěduja',
	'blacklistedusername'             => 'Tute wužiwarske mjeno steji na čornej lisćinje.',
	'blacklistedusernametext'         => 'Wubrane wužiwarske mjeno steji na [[MediaWiki:Usernameblacklist|čornej lisćinje wužiwarskich mjenow]]. Prošu wubjer druhe mjeno.',
	'usernameblacklist'               => '<pre>
# Zapiski w tutej lisćinje budu so jako dźěl regularneho wuraza wužiwać, 
# hdyž so wužiwarske mjena z registracije blokuja. Kóždy zapisk měł dźěl
# nječisłowaneje lisćiny być, na př.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|slědowaca linka|slědowacej lince|slědowace linki|slědowacych linkow}} {{PLURAL:$1|je|stej|su|je}}w lisćinje njewitanych wužiwarskich mjenow je {{PLURAL:$1|njepłaćiwa|njepłaćiwje|njepłaćiwe|njepłaćiwe}}; prošu skoriguj {{PLURAL:$1|ju|jej|je|je}} před składowanjom:',
);

/** Hungarian (Magyar)
 * @author Bdanee
 */
$messages['hu'] = array(
	'usernameblacklist-desc'          => 'Egy [[MediaWiki:Usernameblacklist|feketelista]] segítségével megakadályozhatjuk a listán szereplő reguláris kifejezésekre illeszkedő felhasználói nevek létrehozását.',
	'blacklistedusername'             => 'Feketelistás felhasználónév',
	'blacklistedusernametext'         => 'Az általad választott felhasználónév megegyezik a egyik [[MediaWiki:Usernameblacklist|feketelistán lévővel]]. Válassz másikat.',
	'usernameblacklist'               => '<pre>
# Az ebben a listában található bejegyzések egy reguláris kifejezés részei lesznek
# adott felhasználónevek tiltására regisztrációkor. Mindegyik elemnek felsorolásban kell
# lennie, pl.
#
# * Polgár Jenő
# * Kovács [Jj]ános
</pre>',
	'usernameblacklist-invalid-lines' => 'Az alábbi {{PLURAL:$1|sor hibás|sorok hibásak}} a felhasználói nevek feketelistájában; {{PLURAL:$1|javítsd|javítsd őket}} mentés előtt:',
);

$messages['hy'] = array(
	'blacklistedusername' => 'Արգելված մասնակցի անուն',
	'blacklistedusernametext' => 'Ձեր ընտրած մասնակցի անունը գտնվում է [[MediaWiki:Usernameblacklist|արգելված մասնակիցների անունների ցանկում]]։ Խնդրում ենք ընտրել մեկ այլ անուն։',
	'usernameblacklist' => '<pre>
# Այս ցանկում ընդգրկված բառերը կօգտագործվեն որպես սովորական արտահայտության մաս
# մասնակցային անունները գրանցման արգելման ժամանակ։ Յուրաքանչյուր բառ պետք է լինի
# գնդիկներով ցանկի կետ, օրինակ.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Մասնակցայի անունների արգելման ցանկի հետևյալ {{PLURAL:$1|տողը|տողերը}} անթույլատրելի են; խնդրում ենք ուղղել {{PLURAL:$1|դա|դրանք}} էջը հիշելուց առաջ.',
);

/* Indonesian (Ivan Lanin) */
	$messages['id'] = array(
	'blacklistedusername' => 'Daftar hitam nama pengguna',
	'blacklistedusernametext' => 'Nama pengguna yang Anda pilih berada dalam [[MediaWiki:Usernameblacklist|
daftar hitam nama pengguna]]. Harap pilih nama lain.',
);

$messages['is'] = array(
	'blacklistedusername' => 'Bannað notendanafn',
	'blacklistedusernametext' => 'Þetta notendanafn sem þú hefur valið passar við [[MediaWiki:Usernameblacklist|listann með bönnuðum notendanöfnum]]. Vinsamlegast veldu annað nafn.',
);

/** Italian (Italiano)
 * @author BrokenArrow
 */
$messages['it'] = array(
	'usernameblacklist-desc'          => 'Aggiunge una [[MediaWiki:Usernameblacklist|blacklist dei nomi utente]] per impedire la creazione di account corrispondenti a una o più espressioni regolari',
	'blacklistedusername'             => 'Nome utente non consentito',
	'blacklistedusernametext'         => 'Il nome utente scelto è inserito nella [[MediaWiki:Usernameblacklist|lista dei nomi non consentiti]]. Si prega di scegliere un altro nome.',
	'usernameblacklist'               => '<pre>
# Le voci contenute in questa lista verranno utilizzate per costruire una
# espressione regolare dei nomi utente ai quali non è consentita la registrazione.
# Ciascun elemento deve essere nella forma di un elenco puntato, ad es.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => "{{PLURAL:$1|La seguente riga|Le seguenti righe}} dell'elenco dei nomi utente non consentiti {{PLURAL:$1|non è valida|non sono valide}}; si prega di correggere {{PLURAL:$1|l'errore|gli errori}} prima di salvare la pagina.",
);

/** Japanese (日本語)
 * @author JtFuruhata
 */
$messages['ja'] = array(
	'usernameblacklist-desc'          => '正規表現の禁止ワードに一つ以上一致する場合は利用者アカウントの作成を制限する[[MediaWiki:Usernameblacklist|利用者名ブラックリスト]]を追加',
	'blacklistedusername'             => 'ブラックリストに掲載されている利用者名です',
	'blacklistedusernametext'         => 'あなたが申請した利用者名は、[[MediaWiki:Usernameblacklist|ブラックリストに掲載されているもの]]と一致しました。違う利用者名を選んでください。',
	'usernameblacklist'               => '<pre>
# このリストに記載する正規表現は、利用者アカウント作成の際、ブラックリストに掲載されている
# 利用者名かどうかを判断するために用いられます。各アイテムは、箇条書きの一部として記述する
# 必要があります。例えば以下の通りです。
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '以下の{{PLURAL:$1|行|行}}に記載された利用者ブラックリスト{{PLURAL:$1|は|は}}正しく記述できていません。保存する前に{{PLURAL:$1|これ|これら}}を修正してください:',
);

/* Kazakh Arabic (kk:AlefZet) */
$messages['kk-arab'] = array(
	'blacklistedusername' => 'قارا تىزىمدەگى قاتىسۋشى اتى',
	'blacklistedusernametext' => 'تانداعان قاتىسۋشى اتىڭىز [[{{ns:mediawiki}}:Usernameblacklist| قاتىسۋشى اتى قارا تىزىمىنە]] كىرەدى.
باسقا اتاۋ تالعاڭىز.',
	'usernameblacklist' => '<pre>
# قارا تىزىمدەگى قاتىسۋشى اتىن تىركەلگى جاساۋدان ساقتاپ قالۋ ٴۇشىن بۇل تىزىمدەگى دانالار
# جۇيەلى ايتىلىم (regular expression) بولىگى بوپ پايدالانىلادى. ارقايسى دانا بايراقشامەن
# پىشىمدەلگەن ٴتىزىمدىڭ بولىگى بولۋى قاجەت, مىسالى:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'قاتىسۋشى اتى قارا تىزىمىندەگى كەلەسى {{PLURAL:$1|جول|جولدار}} جارامسىز {{PLURAL:$1|بولدى|بولدى}}; ساقتاۋدىڭ الدىندا {{PLURAL:$1|بۇنى|بۇلاردى}} دۇرىستاپ شىعىڭىز:',
);
/* Kazakh Cyrillic (kk:AlefZet) */
$messages['kk-cyrl'] = array(
	'blacklistedusername' => 'Қара тізімдегі қатысушы аты',
	'blacklistedusernametext' => 'Тандаған қатысушы атыңыз [[{{ns:mediawiki}}:Usernameblacklist| қатысушы аты қара тізіміне]] кіреді.
Басқа атау талғаңыз.',
	'usernameblacklist' => '<pre>
# Қара тізімдегі қатысушы атын тіркелгі жасаудан сақтап қалу үшін бұл тізімдегі даналар
# жүйелі айтылым (regular expression) бөлігі боп пайдаланылады. Әрқайсы дана байрақшамен
# пішімделген тізімдің бөлігі болуы қажет, мысалы:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Қатысушы аты қара тізіміндегі келесі {{PLURAL:$1|жол|жолдар}} жарамсыз {{PLURAL:$1|болды|болды}}; сақтаудың алдында {{PLURAL:$1|бұны|бұларды}} дұрыстап шығыңыз:',
);
/* Kazakh Latin (kk:AlefZet) */
$messages['kk-latn'] = array(
	'blacklistedusername' => 'Qara tizimdegi qatıswşı atı',
	'blacklistedusernametext' => 'Tandağan qatıswşı atıñız [[{{ns:mediawiki}}:Usernameblacklist| qatıswşı atı qara tizimine]] kiredi.
Basqa ataw talğañız.',
	'usernameblacklist' => '<pre>
# Qara tizimdegi qatıswşı atın tirkelgi jasawdan saqtap qalw üşin bul tizimdegi danalar
# jüýeli aýtılım (regular expression) böligi bop paýdalanıladı. Ärqaýsı dana baýraqşamen
# pişimdelgen tizimdiñ böligi bolwı qajet, mısalı:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Qatıswşı atı qara tizimindegi kelesi {{PLURAL:$1|jol|joldar}} jaramsız {{PLURAL:$1|boldı|boldı}}; saqtawdıñ aldında {{PLURAL:$1|bunı|bulardı}} durıstap şığıñız:',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 */
$messages['km'] = array(
	'blacklistedusername' => 'ឈ្មោះអ្នកប្រើប្រាស់ ត្រូវបានដាក់ ក្នុងបញ្ជីខ្មៅ',
);

/** Korean (한국어)
 * @author Klutzy
 */
$messages['ko'] = array(
	'blacklistedusername'             => '금지된 사용자 이름',
	'blacklistedusernametext'         => '사용자 이름에 [[MediaWiki:Usernameblacklist|사용이 금지된 문장]]이 들어 있습니다. 다른 이름으로 가입해주세요.',
	'usernameblacklist'               => '<pre>
# 이 목록은 가입할 때 사용자 이름에 문제가 있는지를 검사하는 데에 쓰이고,
# 각 항목에는 정규식이 들어갑니다.
# 각 항목은 다음과 같이 별표 목록으로 작성되어 있어야 합니다.
# 예:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '사용자 이름 블랙리스트 목록에서 다음 줄이 잘못되었습니다. 저장하기 전에 올바르게 고쳐 주세요:',
);

/* Kurdi */
$messages['ku'] = array(
	'blacklistedusernametext' => 'Wê navî yê te hilbijart li ser [[MediaWiki:Usernameblacklist|lîstêya navên nebaş]] e. Xêra xwe navekî din hilbijêre.',
);

/** Kurdish (Latin) (Kurdî / كوردی (Latin))
 * @author Bangin
 */
$messages['ku-latn'] = array(
	'blacklistedusername'     => 'Nav di nav lîsteya navên qedexe da ye',
	'blacklistedusernametext' => 'Navî te yê bikarhêner, yê te xastî, di nav [[MediaWiki:Usernameblacklist|lîsteya navên qedexe]] da ye. Xêra xwe navekî din bibe.',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'usernameblacklist-desc'          => "Eng [[MediaWiki:Usernameblacklist|Lëscht vun onerwënswchte Benotzernimm]] kompletéieren, fir d'Opmaache vu Benotzerkonten ze verhënneren déi aus enger oder méi regulären Ausdréck zesummegesat sinn.",
	'blacklistedusername'             => 'Verbuede Benotzernimm',
	'blacklistedusernametext'         => 'De gewielte Benotzernumm steet op der [[MediaWiki:Usernameblacklist|Lëscht vun de verbuedene Benotzernimm]]. Sicht iech w.e.g en anere Benotzernumm.',
	'usernameblacklist'               => "<pre>
# D'Elementer an dëser Lëscht sinn Deel vun engem regulären Ausdrock,
# dee bei der Iwwerpréifung von neien Umeldungen op ''verbuede Benotzernimm'' applizéiert gëtt.
# All Zeil muss matt engem * ufénken, z.Bsp.
#
# * Foo
# * [Bb]ar
</pre>",
	'usernameblacklist-invalid-lines' => 'Déi folgend {{PLURAL:$1|Linn|Linnen}} an der Lëscht vun de verbuedene Benotzernimm {{PLURAL:$1|ass|sinn}} ongültig; w.e.g virum Ofspäichere verbesseren:',
);

/** Limburgish (Limburgs)
 * @author Ooswesthoesbes
 */
$messages['li'] = array(
	'usernameblacklist-desc'          => "Voeg 'n [[MediaWiki:Usernameblacklist|zwarte lijst veur gebroekersname]] toe om 't make van gebroekers die voldoen aan ein of meer reguliere expressies te veurkomme",
	'blacklistedusername'             => 'Zwarteliesgebroekersnaam',
	'blacklistedusernametext'         => "De gebroekersnaam daese höbs gekaoze steit oppe [[MediaWiki:Usernameblacklist|zwarte gebroekersnamelies]]. Kees estebleef 'ne anger naam.",
	'usernameblacklist'               => "<pre>
# Regels in dees lies waere gebroek es reguliere oetdrukking veur
# gebroekersname oppe zwartelies bie insjrieving. Edere regel mót
# óngerdeil zeen van 'ne óngenómmerde lies, wie:
#
# * Foo
# * [Bb]ar
</pre>",
	'usernameblacklist-invalid-lines' => "De volgende {{PLURAL:$1|regel|regel}} inne zwarte gebroekersnamelies {{PLURAL:$1|is|zeen}} ónzjuus; corrigeer {{PLURAL:$1|'m|die}} estebleef veurdetse de pazjena opsleis:",
);

/* Lao */
$messages['lo'] = array(
	'blacklistedusername' => 'ຊື່ຜູ້ໃຊ້ ໃນ ບັນຊີດຳ',
);

/** Lithuanian (Lietuvių)
 * @author Matasg
 */
$messages['lt'] = array(
	'blacklistedusername'             => 'Juodajame sąraše esantis naudotojo vardas',
	'blacklistedusernametext'         => 'Naudotojo vardas, kurį pasirinkote sutampa su [[MediaWiki:Usernameblacklist|vardu juodajame sąraše]]. Prašome pasirinkti kitą.',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|Ši|Šios}} {{PLURAL:$1|eilutė|eilutės}} juodajame naudotojų vardų sąraše yra {{PLURAL:$1|bloga|blogos}}; prašome {{PLURAL:$1|ją|jas}} pataisyti prieš išsaugant:',
);

/** Malayalam (മലയാളം)
 * @author Jacob.jose
 */
$messages['ml'] = array(
	'blacklistedusername' => 'കരിമ്പട്ടികയില്‍പ്പെട്ട ഉപയോക്തൃനാമം',
);

$messages['nds'] = array(
	'blacklistedusername' => 'Brukernaam op de swarte List',
	'blacklistedusernametext' => 'De Brukernaam den du utsöcht hest, liekt en Naam vun de [[{{ns:8}}:Usernameblacklist|swarte List för Brukernaams]]. Söök di en annern ut.',
	'usernameblacklist' => '<pre>
# Indrääg in disse List warrt as Deel vun en regulären Utdruck bruukt,
# bi dat Blocken vun Brukernaams bi dat Anmellen över de swarte List. Jeder Indrag schall Deel vun
# ene List mit # dor vör wesen, to’n Bispeel
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Disse {{PLURAL:$1|Reeg|Regen}} in de swarte List för Brukernaams {{PLURAL:$1|is|sünd}} nich bi de Reeg; korrigeer dat doch bevör du spiekerst:',
);

/** Nepali (नेपाली)
 * @author SPQRobin
 */
$messages['ne'] = array(
	'blacklistedusername' => 'कालोसूचीमा परेको प्रयोगकर्ता नाम',
);

/** Dutch (Nederlands)
 * @author Siebrand
 * @author SPQRobin
 */
$messages['nl'] = array(
	'blacklistedusername'             => 'Gebruikersnaam op zwarte lijst',
	'blacklistedusernametext'         => 'De gebruikersnaam die u heeft gekozen staat op de [[MediaWiki:Usernameblacklist|
zwarte lijst van gebruikersnamen]]. Kies alstublieft een andere naam.',
	'usernameblacklist'               => '<pre>
# Regels in deze lijst worden gebruikt als reguliere uitdrukking voor
# gebruikersnamen op de zwarte lijst bij inschrijving. Iedere regel moet
# onderdeel zijn van een ongenummerde lijst, bijvoorbeeld:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'De volgende {{PLURAL:$1|regel|regels}} in de zwarte lijst met gebruikersnamen {{PLURAL:$1|is|zijn}} onjuist; corrigeer {{PLURAL:$1|hem|ze}} alstublieft voordat u de pagina opslaat:',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Eirik
 */
$messages['nn'] = array(
	'usernameblacklist-desc'          => 'Legg til ei [[MediaWiki:Usernameblacklist|liste over svartelista brukarnamn]] for å hindre bruk av brukarnamn som inneheld eit eller fleire regulære uttrykk.',
	'blacklistedusername'             => 'Svartelista brukarnamn',
	'blacklistedusernametext'         => 'Brukarnamnet du har valt er [[MediaWiki:Usernameblacklist|svartelista]]. Ver venleg og vel eit anna namn.',
	'usernameblacklist'               => '<pre>
# Punkta på denne lista vert brukte som ein del av eit regulært uttrykk
# når ein svartelistar brukarnamn frå registrering. Kvart punkt
# bør vere ein del av ei punktliste, til dømes
#
# * Arne
# * [Bb]jarne
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|Denne lina|Desse linene}} i lista over svartelista brukarnamn er {{PLURAL:$1|ugyldig|ugyldige}}, ver venleg og rett {{PLURAL:$1|henne|dei}} før du lagrar:',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'usernameblacklist-desc'          => 'Legger til en [[MediaWiki:Usernameblacklist|svarteliste for brukernavn]] for å forhindre opprettelsen av brukernavn som tilsvarer ett eller flere regulære uttrykk',
	'blacklistedusername'             => 'Svartelistet brukernavn',
	'blacklistedusernametext'         => 'Brukernavnet du har valgt står på [[MediaWiki:Usernameblacklist|listen over svartelistede brukernavn]]. Velg et annet navn.',
	'usernameblacklist'               => '<pre>
# Punkter på denne lista vil bruke som del av et regulært uttrykk
# når man svartelister brukernavn fra registrering. Hvert punkt
# burde være del av en punktliste, f.eks.
#
# * Arne
# * [Bb]jarne
</pre>',
	'usernameblacklist-invalid-lines' => 'Følgende {{PLURAL:$1|linje|linjer}} i brukernavnsvartelista er {{PLURAL:$1|ugyldig|ugyldige}}; vennligst rett {{PLURAL:$1|den|dem}} før du lagrer:',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'usernameblacklist-desc'          => "Ajusta una [[MediaWiki:Usernameblacklist|lista negra dels noms d'utilizaire]] per restrenher la creacion dels comptes d'utilizaires fasent partida d'una o mantuna expression regulara.",
	'blacklistedusername'             => 'Noms d’utilizaires en lista negra',
	'blacklistedusernametext'         => "Lo nom d’utilizaire qu'avètz causit se tròba sus la [[MediaWiki:Usernameblacklist|lista dels noms interdiches]]. Causissètz un autre nom.",
	'usernameblacklist'               => "<pre> # Las dintradas d'aquesta lista seràn utilizadas en tant qu'expressions regularas # per empachar la creacion de noms d'utilizaires interdiches. Cada item deu # far partida d'una lista de piuses, per exemple # # * Foo # * [Bb]ar </pre>",
	'usernameblacklist-invalid-lines' => "{{PLURAL:$1|La linha seguenta|Las linhas seguentas}} de la lista negra dels noms d'utilizaires {{PLURAL:$1|es invalida|son invalidas}} ; corregissetz-{{PLURAL:$1|la|las}} abans d'enregistrar :",
);

/** Polish (Polski)
 * @author Derbeth
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'usernameblacklist-desc'          => 'Dodaje funkcjonalność [[MediaWiki:Usernameblacklist|czarnej listy użytkowników]] do ograniczania możliwości tworzenia kont użytkowników odpowiadających jednemu lub wielu wyrażeniom regularnym',
	'blacklistedusername'             => 'Nazwa użytkownika na czarnej liście',
	'blacklistedusernametext'         => 'Wybrana przez ciebie nazwa użytkownika pasuje do [[MediaWiki:Usernameblacklist|czarnej listy]]. Prosimy o wybranie innej nazwy.',
	'usernameblacklist'               => '<pre>
# Wpisy na tej liście będą użyte jako części wyrażenia regularnego stanowiącego czarną listę
# nazw użytkowników zakazanych przy rejestracji. Każdy element powinien być częścią
# listy wypunktowanej, np.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|Następująca linia|Następujące linie}} na czarnej liście użytkowników {{PLURAL:$1|jest niepoprawna|są niepoprawne}} ; popraw {{PLURAL:$1|ją|je}} przed zapisaniem:',
);

/* Piedmontese (Bèrto 'd Sèra) */
$messages['pms'] = array(
	'blacklistedusername' => 'Stranòm vietà',
	'blacklistedusernametext' => 'Lë stranòm ch\'a l\'ha sërnusse a l\'é ant la [[MediaWiki:Usernameblacklist|lista djë stranòm vietà]]. Për piasì, ch\'as në sërna n\'àotr.',
	'usernameblacklist' => '<pre> # Le vos dë sta lista a saran dovrà coma part ëd n\'espression regolar quand # as buto an lista nèira djë stranòm për la registrassion. Minca vos a la dovrìa fé part ëd na # lista a balin, pr\'es. # # * Ciào # * [Nn]ineta </pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|La riga|Le righe}} ant la lista nèira dë stranòm ambelessì sota a {{PLURAL:$1|l\'é nen bon-a|son nen bon-a}}; për piasì, ch\'a-j daga deuit anans che salvé:',
);

/** Portuguese (Português)
 * @author Malafaya
 * @author SPQRobin
 */
$messages['pt'] = array(
	'usernameblacklist-desc'          => 'Adiciona uma [[MediaWiki:Usernameblacklist|lista negra de nomes de utilizador]] para restringir a criação de contas de utilizador que obedeçam a uma ou mais expressões regulares',
	'blacklistedusername'             => 'Nome de utilizador na lista negra',
	'blacklistedusernametext'         => 'O nome de utilizador seleccionado é semelhante a um presente na [[MediaWiki:Usernameblacklist|lista negra de nomes de utilizadores]]. Por favor, escolha outro nome.',
	'usernameblacklist'               => '<pre>
# As entradas nesta lista são usadas como parte de uma expressão regular
# ao impedir utilizadores de se registarem. Cada item deverá ser parte
# de uma lista com marcadores. Exemplo:
#
# * Algo
# * [Ff]ulano
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|A seguinte linha|As seguintes linhas}} da lista negra de nomes de utilizadores {{PLURAL:$1|é inválida|são inválidas}}; por favor,  corrija-{{PLURAL:$1|a|as}} antes de gravar as alterações:',
);

/** Quechua (Runa Simi)
 * @author AlimanRuna
 */
$messages['qu'] = array(
	'blacklistedusername'             => 'Mana allin sutisuyupi ruraqpa sutin',
	'blacklistedusernametext'         => 'Akllasqayki ruraqpa sutiykiqa [[MediaWiki:Usernameblacklist|mana allin sutisuyu]] nisqapim ruraqpa sutin. Ama hina kaspa, huk sutita akllay.',
	'usernameblacklist'               => "<pre>
# Kay sutisuyupi qumusqakunaqa chiqan nisqap rakinpim llamk'achisqa kanqa,
# ruraqkunap sutinkuna mana allin sutisuyuman qillqamusqa kaptin, chay ruraqpa
# sutinta rakiqunapaq mana llamk'achinapaq. Kay hinam qillqasqa kachun:
#
# * Foo
# * [Bb]ar
</pre>",
	'usernameblacklist-invalid-lines' => "Ruraqpa sutinpaq mana allin sutisuyupiqa kay qatiq {{PLURAL:$1|siq'i|siq'ikuna}} manam {{PLURAL:$1|allinchu|allinchu}}; ama hina kaspa, manaraq waqaycharqaspa {{PLURAL:$1|allinchay|allinchay}}:",
);

/** Russian (Русский)
 * @author .:Ajvol:.
 * @author HalanTul
 */
$messages['ru'] = array(
	'usernameblacklist-desc'          => 'Добавляет [[MediaWiki:Usernameblacklist|чёрный список имён участников]], позволяющий запретить создание учётных записей, соответствующих указанным регулярным выражениям',
	'blacklistedusername'             => 'Запрещённое имя пользователя',
	'blacklistedusernametext'         => 'Имя пользователя, которое вы выбрали, находится в [[MediaWiki:Usernameblacklist|
списке запрещённых имён]]. Пожалуйста, выберите другое имя.',
	'usernameblacklist'               => '<pre>
# Записи этого списка будут использоваться как части регулярных выражений
# для отсеивания нежелательных имён участников во время регистрации. Каждая запись должна быть частью
# маркированного списка, например:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|Следующая строка чёрного списка имён участников ошибочна, пожалуйста, исправьте её|Следующие строки чёрного списка имён участников ошибочны, пожалуйста, исправьте их}} перед сохранением:',
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'usernameblacklist-desc'          => '[[MediaWiki:Usernameblacklist|Кыттааччылар бобуллубут ааттарын]] эбэр, ол оннук ааттары бэлиэтиири көҥүллээбэт.',
	'blacklistedusername'             => 'Бобуллубут аат',
	'blacklistedusernametext'         => 'Талбыт аатыҥ [[MediaWiki:Usernameblacklist|бобуллубут ааттар испииһэктэригэр]] киирэр эбит. Атын ааты таларыҥ буоллар.',
	'usernameblacklist'               => '<pre>
# Записи этого списка будут использоваться как части регулярных выражений
# для отсеивания нежелательных имён участников во время решистрации. Каждая запись должна быть частью
# маркированного списка, например:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Хара испииһэк бу {{PLURAL:$1|строкаата сыыһалаах|строкалара сыыһалаахтар}}; уларытыаҥ иннинэ ону көннөр:',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'usernameblacklist-desc'          => 'Pridáva [[MediaWiki:Usernameblacklist|čiernu listinu mien používateľov]], aby sa obmedzila tvorba používateľských účtov zodpovedajúcich jednému alebo viacerým regulárnym výrazom',
	'blacklistedusername'             => 'Používateľské meno na čiernej listine',
	'blacklistedusernametext'         => 'Používateľské meno, ktoré ste si zvolili sa nachádza na [[MediaWiki:Usernameblacklist|
čiernej listine používateľských mien]]. Prosím, zvoľte si iné.',
	'usernameblacklist'               => '<pre>
# Položky z tohto zoznamu sa použijú ako časť regulárneho výrazu pre
# zamedzenie vytvorenia účtu s daným používateľským menom. Každá položka 
# musí byť ako odrážka v zozname, napr.:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|Nasledovný riadok|Nasledovné riadky}} čiernej listiny používateľských mien {{PLURAL:$1|je neplatný|sú neplatné}} a je potrebné {{PLURAL:$1|ho|ich}} opraviť pred uložením stránky:',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'blacklistedusername'             => 'Benutsernoome ap ju Speerlieste',
	'blacklistedusernametext'         => 'Die wäälde Benutsernoome stoant ap ju [[MediaWiki:Usernameblacklist|Lieste fon do speerde Benutsernoomen]]. Wääl n uur Noome.',
	'usernameblacklist'               => '<pre>
# Iendraage in disse Lieste sunt Deel fon n regulären Uutdruk,
# die der bie ju Wröich fon Näianmäldengen ap nit wonskede Benutsernoomen anwoand wäd.
# Älke Siede mout mäd n * ounfange, t.B.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|Ju foulgjende Riege|Do foulgjende Riegen}} in ju Lieste fon nit-wonskede Benutsernoomen {{PLURAL:$1|is|sunt}} uungultich; korrigier do foar dät Spiekerjen:',
);

/* Sundanese (Irwangatot via BetaWiki) */
$messages['su'] = array(
	'blacklistedusername' => 'Ngaran pamaké nu dicorét:',
	'blacklistedusernametext' => 'Ngaran pamaké nu dipilih cocog jeung [[MediaWiki:Usernameblacklist|ngaran pamaké nu dicorét]]. Mangga pilih ngaran séjén.',
);

/** Swedish (Svenska)
 * @author SPQRobin
 */
$messages['sv'] = array(
	'usernameblacklist-desc'          => 'Lägger till en [[MediaWiki:Usernameblacklist|svart lista för användarnamn]] som hindrar användarkonton från att skapas om de matchar ett eller flera reguljära uttryck',
	'blacklistedusername'             => 'Svartlistat användarnamn',
	'blacklistedusernametext'         => 'Det användarnamn du vill använda är [[MediaWiki:Usernameblacklist|svartlistat]]. Välj ett annat namn.',
	'usernameblacklist'               => '<pre>
# Innehållet i den här listan används som del i ett reguljärt uttryck
# för att förhindra användarnamn från att registreras.
# Varje post i listan måste inledas med en asterisk (*); t.ex.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Följande {{PLURAL:$1|rad|rader}} i listan är {{PLURAL:$1|ogiltig|ogiltiga}}; rätta {{PLURAL:$1|den|dem}} innan du sparar:',
);

/** Telugu (తెలుగు)
 * @author Mpradeep
 * @author Veeven
 */
$messages['te'] = array(
	'usernameblacklist-desc'          => 'రెగ్యులర్ ఎక్స్&zwnj;ప్రెషన్లతో సరిపోలే వాడుకరి ఖాతాలను సృష్టించడాన్ని నిరోధించడానికి గాను [[MediaWiki:Usernameblacklist|వాడుకరి పేర్ల నిరోధపు జాబితా]]ని చేరుస్తుంది',
	'blacklistedusername'             => 'అనుమతిలేని పేరు',
	'blacklistedusernametext'         => 'మీరు ఎంచుకున్న సభ్యనామం, [[MediaWiki:Usernameblacklist|అనుమతించని పేర్ల జాబితా]]లో ఉంది. దయచేసి ఇంకో పేరుని ఎంచుకోండి.',
	'usernameblacklist'               => '<pre>
# కొత్త ఖాతాలకు సృష్టించుకునేటప్పుడు వాటికి కింద జాబితాలో ఉన్న regular exressionకు సరిపోయే
# పేర్లను అనుమతించరు. ఏదయినా కొత్త పేరును సభ్యనామంగా అనుమతించకూడాదని అనుకుంటే దానిని
# ఒక నక్షత్రం గుర్తుతో పాటుగా చేర్చండి. ఉదాహరణ:
# 
# * ఫలానా
# * అత[డును]
</pre>',
	'usernameblacklist-invalid-lines' => 'అనుమతించని పేర్లజాబితాలో ఈ కింది {{PLURAL:$1|లైను|లైన్‌లు}} అర్ధంకాకుండా {{PLURAL:$1|ఉంది|ఉన్నాయి}}; దాయచేసి {{PLURAL:$1|అందులో|వాటిలో}} ఉన్న తప్పులను సరిచేసి ఆ తరువాత భద్రపరచండి.',
);

/** Tajik (Тоҷикӣ)
 * @author Ibrahim
 */
$messages['tg'] = array(
	'blacklistedusername' => 'Номи корбарии ғайри миҷоз',
);

/** Tagalog (Tagalog)
 * @author Felipe Aira
 */
$messages['tl'] = array(
	'blacklistedusername'             => 'Ipinagbawal na bansag',
	'usernameblacklist-invalid-lines' => 'Ang {{PLURAL:$1|sumusunod|mga sumusunod}} na hanay sa mga ipinagbabawal na bansag ay inbalido; pakitama ang {{PLURAL:$1|iyon|mga iyon}} bago magligtas:',
);

/** Turkish (Türkçe)
 * @author SPQRobin
 */
$messages['tr'] = array(
	'blacklistedusername'     => 'Kara listedeki kullanıcılar',
	'blacklistedusernametext' => 'Seçtiğiniz isim [[MediaWiki:Usernameblacklist|Kara listedeki kullanıcılar]] listesinde sıralanan bir kullanıcı adıyla aynı isme sahiptir. Lütfen başka bir kullanıcı adı seçiniz.',
);

/** Vietnamese (Tiếng Việt)
 * @author Vinhtantran
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'usernameblacklist-desc'          => 'Thêm [[MediaWiki:Usernameblacklist|danh sách đen về tên người dùng]] để cấm không được mở tài khoản dùng tên trùng với một biểu thức chính quy',
	'blacklistedusername'             => 'Danh sách đen về tên người dùng',
	'blacklistedusernametext'         => 'Tên người dùng mà bạn chọn trùng khớp với [[MediaWiki:Usernameblacklist|danh sách đen về tên người dùng]]. Xin hãy chọn một tên khác.',
	'usernameblacklist'               => '<pre>
# Các mục trong danh sách này sẽ được dùng để liệt các tên người dùng vào danh sách 
# đen không cho đăng ký, dùng biểu thức chính quy. Mỗi mục sẽ bắt đầu bằng
# danh sách chấm đầu dòng, ví dụ
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|Dòng|Những dòng}} sau đây trong Danh sách đen về tên người dùng bị sai; xin hãy sửa chữa {{PLURAL:$1|nó|chúng}} trước khi lưu:',
);

/* Cantonese (Shinjiman) */
$messages['yue'] = array(
	'blacklistedusername' => '列入黑名單嘅用戶名',
	'blacklistedusernametext' => '你所揀嘅用戶名係同[[MediaWiki:Usernameblacklist|用戶名黑名單一覽]]符合。請揀過另一個名喇。',
	'usernameblacklist' => '<pre>
# 響呢個表嘅項目，當將註冊嗰陣嘅用戶名會用來做黑名單時，
# 會成為標準表示式嘅一部份。每一個項目都應該要係點列嘅一部份，好似
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '下面響用戶名黑名單嘅{{PLURAL:$1|一行|咁多行}}唔正確；請響儲存之前改正{{PLURAL:$1|佢|佢哋}}:',
);

/* Chinese (Simplified) (Shinjiman) */
$messages['zh-hans'] = array(
	'blacklistedusername' => '列入黑名单的用户名',
	'blacklistedusernametext' => '您所选择的用户名是与[[MediaWiki:Usernameblacklist|用户名黑名单列表]]匹配。请选择另一个名称。',
	'usernameblacklist' => '<pre>
# 在这个表中的项目，当将注册时的用户名会用来做黑名单的时候，
# 会成为标准表示式的一部份。每一个项目都应该要是点列的一部份，好像
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '以下在用户名黑名单中{{PLURAL:$1|一行|多行}}不正确；请于保存之前改正{{PLURAL:$1|它|它们}}:',
);

/* Chinese (Traditional) (Shinjiman) */
$messages['zh-hant'] = array(
	'blacklistedusername' => '列入黑名單的用戶名',
	'blacklistedusernametext' => '您所選擇的用戶名是與[[MediaWiki:Usernameblacklist|用戶名黑名單列表]]符合。請選擇另一個名稱。',
	'usernameblacklist' => '<pre>
# 在這個表中的項目，當將註冊時的用戶名會用來做黑名單的時候，
# 會成為標準表示式的一部份。每一個項目都應該要是點列的一部份，好像
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '以下在用戶名黑名單中{{PLURAL:$1|一行|多行}}不正確；請於保存之前改正{{PLURAL:$1|它|它們}}:',
);

