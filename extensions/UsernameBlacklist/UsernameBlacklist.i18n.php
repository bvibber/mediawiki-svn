<?php

/**
 * Internationalisation file for the Username Blacklist extension
 *
 * @author Rob Church <robchur@gmail.com>
 * @addtogroup Extensions
 */

function efUsernameBlacklistMessages( $single = false ) {
	$messages = array(

/* English (Rob Church) */
'en' => array(
	'blacklistedusername'     => 'Blacklisted username',
	'blacklistedusernametext' => 'The user name you have chosen matches the [[MediaWiki:Usernameblacklist|list of blacklisted usernames]]. Please choose another name.',
	'usernameblacklist' => '<pre>
# Entries in this list will be used as part of a regular expression when
# blacklisting usernames from registration. Each item should be part of
# a bulleted list, e.g.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'The following {{PLURAL:$1|line|lines}} in the username blacklist {{PLURAL:$1|is|are}} invalid; please correct {{PLURAL:$1|it|them}} before saving:',
),

/* Arabic (Meno25) */
'ar' => array(
	'blacklistedusername' => 'اسم مستخدم في القائمة السوداء',
	'blacklistedusernametext' => 'اسم المستخدم الذي اخترته يطابق [[MediaWiki:Usernameblacklist|
قائمة أسماء المستخدمين السوداء]]. من فضلك اختر اسما آخر.',
	'usernameblacklist' => '<pre>
# المدخلات في هذه القائمة ستستخدم كجزء من تعبير منتظم عند
# منع أسماء المستخدمين في القائمة السوداء من التسجيل. كل مدخلة يجب أن تكون جزءا من
# قائمة مرقمة، كمثال.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|السطر التالي|السطور التالية}} في قائمة اسم المستخدم السوداء {{PLURAL:$1|غير صحيح|غير صحيحة}} ؛ من فضلك {{PLURAL:$1|صححه|صححها}} قبل الحفظ:',
),

'bcl' => array(
	'blacklistedusername' => 'Blacklisted na username',
),

'bn' => array(
	'blacklistedusername' => 'নিষিদ্ধ ঘোষিত ব্যবহারকারী নাম',
	'blacklistedusernametext' => 'ব্যবহারকারীর নাম [[MediaWiki:Usernameblacklist|কালতালিকাভুক্ত ব্যবহারকারীর নাম সমূহের]] সাথে মিলেছে। দয়াকরে অন্য নাম পছন্দ করুন।',
	'usernameblacklist' => '<pre> # এই তালিকায় ভুক্তি সমূহ রেগুলার এক্সপ্রেশনের অংশ হিসেবে ব্যবহৃত হবে যেখানে # রেজিষ্ট্রশন থেকে নিষিদ্ধ ব্যবহারকারী নামসমূহ। প্রতিটি উপাদান # একটি বুলেট তালিকার অংশ হয়ে থাকবে, অর্থাৎ # # * Foo # * [Bb]ar </pre>',
	'usernameblacklist-invalid-lines' => 'এই {{PLURAL:$1|লাইন|লাইনসমূহ}} নিষিদ্ধ ব্যবহারকারী নাম তালিকাভুক্ত {{PLURAL:$1|নাম|নামসমূহ}} অসিদ্ধ; দয়াকরে সংরক্ষণ করার পূর্বে {{PLURAL:$1|এটি|এগুলো}} ঠিক করুন:',
),

'ca' => array(
	'blacklistedusername' => 'Nom no permès',
	'blacklistedusernametext' => 'El nom d\'usuari que heu escollit forma part de la [[MediaWiki:Usernameblacklist|llista de noms no permesos]]. Escolliu-ne un altre de diferent, si us plau.',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|La següent línia|Les següents línies}} de la llista negra de noms d\'usuari no {{PLURAL:$1|és vàlida|són vàlides}}; si us plau, corregiu{{PLURAL:$1|-la|-les}} abans de desar-ho:',
),

'cs' => array(
	'blacklistedusername' => 'Nepovolené uživatelské jméno',
	'blacklistedusernametext' => 'Vámi vybrané uživatelské jméno se shoduje s&nbsp;některým ze [[MediaWiki:Usernameblacklist|seznamu nepovolených uživatelských jmen]]. Prosíme, vyberte si jiné jméno.',
	'usernameblacklist' => '<pre>
# Položky v&nbsp;tomto seznamu budou použity jako části regulárního výrazu
# při kontrole nepovolených uživatelských jmen při registraci.
# Každý výraz by měl být označen jako položka nečíslovaného seznamu, např.:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Následující {{plural:$1|řádka|řádky|řádky}} v&nbsp;seznamu nepovolených uživatelských jmen {{plural:$1|je neplatná|jsou neplatné|jsou neplatné}}; prosíme, opravte {{plural:$1|ji|je|je}} před uložením:',
),

/* German (Raymond) */
'de' => array(
	'blacklistedusername'             => 'Benutzername auf der Sperrliste',
	'blacklistedusernametext'         => 'Der gewählte Benutzername steht auf der [[MediaWiki:Usernameblacklist|Liste der gesperrten Benutzernamen]]. Bitte einen anderen wählen.',
	'usernameblacklist'               => '<pre>
# Einträge in dieser Liste sind Teil eines regulären Ausdrucks,
# der bei der Prüfung von Neuanmeldungen auf unerwünschte Benutzernamen angewendet wird.
# Jede Zeile muss mit einem * beginnen, z.B.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Die {{PLURAL:$1|folgende Zeile|folgenden Zeilen}} in der Liste unerwünschter Benutzernamen {{PLURAL:$1|ist|sind}} ungültig; bitte korrigiere sie vor dem Speichern:',
),

'fa' => array(
	'blacklistedusername' => 'نام کاربری غیر مجاز',
	'blacklistedusernametext' => 'نام کاربری مورد نظر شما در با [[MediaWiki:Usernameblacklist|فهرست سیاه نام‌های کاربری]] مطابقت دارد. لطفاً یک نام کاربری دیگر انتخاب کنید.',
	'usernameblacklist' => '<pre>
# مدخل‌های این صفحه به عنوان یک الگوی regular expression برای 
# فهرست سیاه هنگام ثبت نام کاربری به کار می‌روند. هر مورد باید
# در یک سطر جدا که با علامت * آغاز شده باشد تعریف گردد، مانند:
#
# * فلان
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|سطر|سطرهای}} زیر از فهرست سیاه نام کاربری غیر مجاز {{PLURAL:$1|است|هستند}}؛ لطفاً {{PLURAL:$1|آن|آن‌ها}} را قبل از ذخیره کردن صفحه اصلاح کنید:',
),

/* French */
'fr' => array(
	'blacklistedusername' => 'Noms d’utilisateurs en liste noire',
	'blacklistedusernametext' => 'Le nom d’utilisateur que vous avez choisi se trouve sur la
[[MediaWiki:Usernameblacklist|liste des noms interdits]]. Veuillez choisir un autre nom.',
	'usernameblacklist' => '<pre>
# Les entrées de cette liste seront utilisées en tant qu\'expressions régulières
# afin d\'empêcher la création de noms d\'utilisateurs interdits. Chaque item doit
# faire partie d\'une liste à puces, par exemple
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|La ligne suivante|Les lignes suivantes}} de la liste noire des noms d\'utilisateurs {{PLURAL:$1|est invalide|sont invalides}} ; veuillez {{PLURAL:$1|la|les}} corriger avant d\'enregistrer :',
),

'gl' => array(
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
),

'hak' => array(
	'blacklistedusername' => 'Lie̍t-ngi̍p het-miàng-tân ke yung-fu-miàng',
	'blacklistedusernametext' => 'Ngì só sién-chet ke yung-fu-miàng he lâu [[MediaWiki:Usernameblacklist|Yung-fu-miàng het-miàng-tân lie̍t-péu]] fù-ha̍p. Chhiáng sién-chet nang-ngoi yit-ke miàng-chhṳ̂n.',
),

'hsb' => array(
	'blacklistedusername' => 'Tute wužiwarske mjeno steji na čornej lisćinje.',
	'blacklistedusernametext' => 'Wubrane wužiwarske mjeno steji na [[MediaWiki:Usernameblacklist|čornej lisćinje wužiwarskich mjenow]]. Prošu wubjer druhe mjeno.',
	'usernameblacklist' => '<pre>
# Zapiski w tutej lisćinje budu so jako dźěl regularneho wuraza wužiwać, 
# hdyž so wužiwarske mjena z registracije blokuja. Kóždy zapisk měł dźěl
# nječisłowaneje lisćiny być, na př.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|slědowaca linka|slědowacej lince|slědowace linki|slědowacych linkow}} {{PLURAL:$1|je|stej|su|je}}w lisćinje njewitanych wužiwarskich mjenow je {{PLURAL:$1|njepłaćiwa|njepłaćiwje|njepłaćiwe|njepłaćiwe}}; prošu skoriguj {{PLURAL:$1|ju|jej|je|je}} před składowanjom:',
),

'hu' => array(
	'blacklistedusername' => 'Feketelistás felhasználónév',
	'blacklistedusernametext' => 'Az általad választott felhasználónév megegyezik a egyik [[MediaWiki:Usernameblacklist|feketelistán lévővel]]. Válassz másikat.',
),

'hy' => array(
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
),

/* Indonesian (Ivan Lanin) */
	'id' => array(
	'blacklistedusername' => 'Daftar hitam nama pengguna',
	'blacklistedusernametext' => 'Nama pengguna yang Anda pilih berada dalam [[MediaWiki:Usernameblacklist|
daftar hitam nama pengguna]]. Harap pilih nama lain.',
),

'is' => array(
	'blacklistedusername' => 'Bannað notendanafn',
	'blacklistedusernametext' => 'Þetta notendanafn sem þú hefur valið passar við [[MediaWiki:Usernameblacklist|listann með bönnuðum notendanöfnum]]. Vinsamlegast veldu annað nafn.',
),

/* Italian (BrokenArrow) */
'it' => array(
	'blacklistedusername' => 'Nome utente non consentito',
	'blacklistedusernametext' => 'Il nome utente scelto è inserito nella [[MediaWiki:Usernameblacklist|lista dei nomi non consentiti]]. Si prega di scegliere un altro nome.',
),

/* Kazakh Cyrillic (kk:AlefZet) */
	'kk-kz' => array(
	'blacklistedusername' => 'Қара тізімдегі қатысушы аты',
	'blacklistedusernametext' => 'Тандаған қатысушы атыңыз [[{{ns:mediawiki}}:Usernameblacklist| қатысушы аты қара тізіміне]] кіреді.
Басқа атау талғаңыз.',
	'usernameblacklist' => '<pre>
# Қара тізімдегі қатысушы атын тіркелгі жасаудан сақтап қалу үшін бұл тізімдегі даналар
# қайталаулы кәлам (regular expression) бөлігі боп пайдаланылады. Әрқайсы дана байрақшамен
# пішімделген тізімдің бөлігі болуы қажет, мысалы:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Қатысушы аты қара тізіміндегі келесі {{PLURAL:$1|жол|жолдар}} жарамсыз {{PLURAL:$1|болды|болды}}; сақтаудың алдында {{PLURAL:$1|бұны|бұларды}} дұрыстап шығыңыз:',
),

/* Kazakh Latin (kk:AlefZet) */
'kk-tr' => array(
	'blacklistedusername' => 'Qara tizimdegi qatıswşı atı',
	'blacklistedusernametext' => 'Tandağan qatıswşı atıñız [[{{ns:mediawiki}}:Usernameblacklist| qatıswşı atı qara tizimine]] kiredi.
Basqa ataw talğañız.',
	'usernameblacklist' => '<pre>
# Qara tizimdegi qatıswşı atın tirkelgi jasawdan saqtap qalw üşin bul tizimdegi danalar
# qaýtalawlı kälam (regular expression) böligi bop paýdalanıladı. Ärqaýsı dana baýraqşamen
# pişimdelgen tizimdiñ böligi bolwı qajet, mısalı:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Qatıswşı atı qara tizimindegi kelesi {{PLURAL:$1|jol|joldar}} jaramsız {{PLURAL:$1|boldı|boldı}}; saqtawdıñ aldında {{PLURAL:$1|bunı|bulardı}} durıstap şığıñız:',
),

/* Kazakh Arabic (kk:AlefZet) */
'kk-cn' => array(
	'blacklistedusername' => 'قارا تٸزٸمدەگٸ قاتىسۋشى اتى',
	'blacklistedusernametext' => 'تانداعان قاتىسۋشى اتىڭىز [[{{ns:mediawiki}}:Usernameblacklist| قاتىسۋشى اتى قارا تٸزٸمٸنە]] كٸرەدٸ.
باسقا اتاۋ تالعاڭىز.',
	'usernameblacklist' => '<pre>
# قارا تٸزٸمدەگٸ قاتىسۋشى اتىن تٸركەلگٸ جاساۋدان ساقتاپ قالۋ ٷشٸن بۇل تٸزٸمدەگٸ دانالار
# قايتالاۋلى كٵلام (regular expression) بٶلٸگٸ بوپ پايدالانىلادى. ٵرقايسى دانا بايراقشامەن
# پٸشٸمدەلگەن تٸزٸمدٸڭ بٶلٸگٸ بولۋى قاجەت, مىسالى:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'قاتىسۋشى اتى قارا تٸزٸمٸندەگٸ كەلەسٸ {{PLURAL:$1|جول|جولدار}} جارامسىز {{PLURAL:$1|بولدى|بولدى}}; ساقتاۋدىڭ الدىندا {{PLURAL:$1|بۇنى|بۇلاردى}} دۇرىستاپ شىعىڭىز:',
),

/* Kurdi */
'ku' => array(
	'blacklistedusernametext' => 'Wê navî yê te hilbijart li ser [[MediaWiki:Usernameblacklist|lîstêya navên nebaş]] e. Xêra xwe navekî din hilbijêre.',
),

/* Lao */
'lo' => array(
	'blacklistedusername' => 'ຊື່ຜູ້ໃຊ້ ໃນ ບັນຊີດຳ',
),

'nds' => array(
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
),

/* nld / Dutch (Siebrand Mazeland) */
'nl' => array(
	'blacklistedusername' => 'Gebruikersnaam op zwarte lijst',
	'blacklistedusernametext' => 'De gebruikersnaam die u heeft gekozen staat op de [[MediaWiki:Usernameblacklist|
zwarte lijst van gebruikersnamen]]. Kies alstublieft een andere naam.',
	'usernameblacklist' => '<pre>
# Regels in deze lijst worden gebruikt als reguliere expressie voor
# gebruikersnamen op de zwarte lijst bij inschrijving. Iedere regel moet
# onderdeel zijn van een ongenummerde lijst, bijvoorbeeld:
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'De volgende {{PLURAL:$1|regel|regels}} in de zwarte lijst met gebruikersnamen {{PLURAL:$1|is|zijn}} onjuist; corrigeer {{PLURAL:$1|hem|ze}} alstublieft voordat u de pagina opslaat:',
),

/* Norwegian (Jon Harald Søby) */
'no' => array(
	'blacklistedusername' => 'Svartelistet brukernavn',
	'blacklistedusernametext' => 'Brukernavnet du har valgt tilsvarer et navn på [[MediaWiki:Usernameblacklist|listen over svartelistede brukernavn]]. Velg et annet navn.',
),

/* Occitan (Cedric31) */
'oc' => array(
	'blacklistedusername' => 'Noms d’utilizaires en lista negra',
	'blacklistedusernametext' => 'Lo nom d’utilizaire qu\'avètz causit se tròba sus la [[MediaWiki:Usernameblacklist|lista dels noms interdiches]]. Causissètz un autre nom.',
	'usernameblacklist' => '<pre> # Las dintradas d\'aquesta lista seràn utilizadas en tant qu\'expressions regularas # per empachar la creacion de noms d\'utilizaires interdiches. Cada item deu # far partida d\'una lista de piuses, per exemple # # * Foo # * [Bb]ar </pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|La linha seguenta|Las linhas seguentas}} de la lista negra dels noms d\'utilizaires {{PLURAL:$1|es invalida|son invalidas}} ; corregissetz-{{PLURAL:$1|la|las}} abans d\'enregistrar :',
),

'pl' => array(
	'blacklistedusername' => 'Nazwa użytkownika na czarnej liście',
	'blacklistedusernametext' => 'Wybrana przez ciebie nazwa użytkownika pasuje do [[MediaWiki:Usernameblacklist|czarnej listy]]. Prosimy o wybranie innej nazwy.',
	'usernameblacklist' => '<pre>
# Wpisy na tej liście będą użyte jako części wyrażenia regularnego stanowiącego czarną listę
# nazw użytkowników zakazanych przy rejestracji. Każdy element powinien być częścią
# listy wypunktowanej, np.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|Następująca linia|Następujące linie}} na czarnej liście użytkowników {{PLURAL:$1|jest|są}} niepoprawne; popraw {{PLURAL:$1|ją|je}} przed zapisaniem:',
),

/* Piedmontese (Bèrto 'd Sèra) */
'pms' => array(
	'blacklistedusername' => 'Stranòm vietà',
	'blacklistedusernametext' => 'Lë stranòm ch\'a l\'ha sërnusse a l\'é ant la [[MediaWiki:Usernameblacklist|lista djë stranòm vietà]]. Për piasì, ch\'as në sërna n\'àotr.',
	'usernameblacklist' => '<pre> # Le vos dë sta lista a saran dovrà coma part ëd n\'espression regolar quand # as buto an lista nèira djë stranòm për la registrassion. Minca vos a la dovrìa fé part ëd na # lista a balin, pr\'es. # # * Ciào # * [Nn]ineta </pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|La riga|Le righe}} ant la lista nèira dë stranòm ambelessì sota a {{PLURAL:$1|l\'é nen bon-a|son nen bon-a}}; për piasì, ch\'a-j daga deuit anans che salvé:',
),

/* Portuguese (Lugusto) */
'pt' => array(
	'blacklistedusername' => 'Nome de utilizador na lista negra',
	'blacklistedusernametext' => 'O nome de utilizador selecionado é similar a um presente na [[MediaWiki:Usernameblacklist|
lista negra de nomes de utilizadores]]. Por gentileza, escolha outro.',
	'usernameblacklist' => '<pre>
# As entradas nesta lista são usadas como parte de uma expressão regular
# ao impedir utilizadores de se registarem. Cada item deverá ser parte
# de uma lista com marcadores. Exemplo:
#
# * Algo
# * [Ff]ulano
</pre>',
	'usernameblacklist-invalid-lines' => '{{PLURAL:$1|A seguinte linha|As seguintes linhas}} da lista negra de nomes de utilizadores {{PLURAL:$1|é inválida|são inválidas}}; por gentileza, {{PLURAL:$1|a|as}} corrija antes de salvar as alterações:',
),

/* Russian */
'ru' => array(
	'blacklistedusername' => 'Запрещённое имя пользователя',
	'blacklistedusernametext' => 'Имя пользователя, которое вы выбрали, находится в [[MediaWiki:Usernameblacklist|
списке запрещённых имён]]. Пожалуйста, выберите другое имя.',
),

/* Slovak (helix84) */
'sk' => array(
	'blacklistedusername' => 'Používateľské meno na čiernej listine',
	'blacklistedusernametext' => 'Používateľské meno, ktoré ste si zvolili sa nachádza na [[MediaWiki:Usernameblacklist|
čiernej listine používateľských mien]]. Prosím, zvoľte si iné.',
	'usernameblacklist' => '<pre>
# Položky z tohto zoznamu sa použijú ako časť regulárneho výrazu pre
# zamedzenie vytvorenia účtu s daným používateľským menom. Každá položka 
# musí byť ako odrážka v zozname, napr.:
#
# * Foo
# * [Bb]ar
</pre>',
),

/* Sundanese (Irwangatot via BetaWiki) */
'su' => array(
	'blacklistedusername' => 'Ngaran pamaké nu dicorét:',
	'blacklistedusernametext' => 'Ngaran pamaké nu dipilih cocog jeung [[MediaWiki:Usernameblacklist|ngaran pamaké nu dicorét]]. Mangga pilih ngaran séjén.',
),

/* Swedish */
'sv' => array(
	'blacklistedusername' => 'Svartlistat användarnamn',
	'blacklistedusernametext' => 'Det användarnamn du vill använda är [[MediaWiki:Usernameblacklist|svartlistat]]. Välj ett annat namn.',
	'usernameblacklist' => '<pre>
# Innehållet i den här listan används som del i ett reguljärt uttryck
# för att förhindra användarnamn från att registreras.
# Varje post i listan måste inledas med en asterisk (*), t.ex.
#
# * Foo
# * [Bb]ar
</pre>',
	'usernameblacklist-invalid-lines' => 'Följande {{PLURAL:$1|rad|rader}} i listan är {{PLURAL:$1|ogiltig|ogiltiga}}; rätta {{PLURAL:$1|den|dem}} innan du sparar:',
),

/* Cantonese (Shinjiman) */
'yue' => array(
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
),

/* Chinese (Simplified) (Shinjiman) */
'zh-hans' => array(
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
),

/* Chinese (Traditional) (Shinjiman) */
'zh-hant' => array(
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
),

	);

	/* Kazakh default, fallback to kk-kz */
	$messages['kk'] = $messages['kk-kz'];

	/* Chinese defaults, fallback to zh-hans or zh-hant */
	$messages['zh'] = $messages['zh-hans'];
	$messages['zh-cn'] = $messages['zh-hans'];
	$messages['zh-hk'] = $messages['zh-hant'];
	$messages['zh-sg'] = $messages['zh-hans'];
	$messages['zh-tw'] = $messages['zh-hant'];

	/* Cantonese default, fallback to yue */
	$messages['zh-yue'] = $messages['yue'];

	return $single ? $messages['en'] : $messages;
}
