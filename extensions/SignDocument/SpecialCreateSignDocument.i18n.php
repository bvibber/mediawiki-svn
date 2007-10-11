<?PHP
$allMessages = array(
	'en' => array( 
		'createsigndocument'         => 'Enable Document Signing',
		'createsigndoc-head'         => 
"Use this form to create a 'Sign Document' page for the provided article, such that
users will be able to sign it via [[Special:SignDocument]]. Please specify the name
of the article on which you wish to enable digital signing, members of which 
usergroup should be allowed to sign it, which fields you wish to be visible to users 
and which should be optional, a minimum age to require users to be to sign the 
document (no minimum if omitted), and a brief introductory text describing the 
document and providing instructions to users.

<b>There is presently no way to delete or modify signature documents after they are
created</b> without direct database access. Additionally, the text of the article 
displayed on the signature page will be the ''current'' text of the page, regardless
of changes made to it after today. Please be absolutely positive that the document
is to a point of stability for signing, and please also be sure that you specify
all fields exactly as they should be, ''before submitting this form''.",
		'createsigndoc-pagename'     => 'Page:',
		'createsigndoc-allowedgroup' => 'Allowed group:',
		'createsigndoc-email'        => 'E-mail address:',
		'createsigndoc-address'      => 'House Address:',
		'createsigndoc-extaddress'   => 'City, State, Country:',
		'createsigndoc-phone'        => 'Phone number:',
		'createsigndoc-bday'         => 'Birthdate:',
		'createsigndoc-minage'       => 'Minimum age:',
		'createsigndoc-introtext'    => 'Introduction:',
		'createsigndoc-hidden'       => 'Hidden',
		'createsigndoc-optional'     => 'Optional',
		'createsigndoc-create'       => 'Create',
		'createsigndoc-error-generic'=> 'Error: $1',
		'createsigndoc-error-pagenoexist' => 'Error: The page [[$1]] does not exist.',
		'createsigndoc-success'      => 'Document signing has been successfully enabled
on [[$1]]. To test it, please visit [{{SERVER}}{{localurl: Special:SignDocument|doc=$2}} this page].',
	),
	'ar' => array(
		'createsigndocument' => 'فعل توقيع الوثيقة',
		'createsigndoc-head' => 'استخدم هذه الوثيقة لإنشاء صفحة \'Sign Document\' للمقالة المعطاة، بحيث
يمكن للمستخدمين توقيعها من خلال [[Special:SignDocument]]. من فضلك حدد اسم
المقالة التي تود تفعيل التوقيع الرقمي عليها، أعضاء أي 
مجموعة مستخدم مسموح لهم بتوقيعها، أي حقول تود أن تكون مرئية للمستخدمين 
وأي يجب أن تكون اختيارية، عمر أدنى لمستخدمين ليمكن لهم توقيع 
الوثيقة (لا حد أدنى لو حذفت)، ونص تقديمي مختصر يصف 
الوثيقة ويوفر التعليمات للمستخدمين.

<b>لا توجد حاليا أية طريقة لحذف أو تعديل توقيعات الوثائق بعد
إنشائها</b> بدون دخول قاعدة البيانات مباشرة. إضافة إلى ذلك، نص المقالة 
المعروض في صفحة التوقيع سيكون النص \'\'الحالي\'\' للصفحة، بغض النظر عن
التغييرات بها بعد اليوم. من فضلك كن متأكدا تماما من أن الوثيقة
وصلت لنقطو ثبات للتوقيع، ومن فضلك أيضا تأكد أنك حددت
كل الحقول تماما كما يجب أن تكون، \'\'قبل تنفيذ هذه الاستمارة\'\'.',
		'createsigndoc-pagename' => 'صفحة:',
		'createsigndoc-allowedgroup' => 'المجموعة المسموحة:',
		'createsigndoc-email' => 'عنوان البريد الإلكتروني:',
		'createsigndoc-address' => 'عنوان المنزل:',
		'createsigndoc-extaddress' => 'المدينة، الولاية، البلد:',
		'createsigndoc-phone' => 'رقم الهاتف:',
		'createsigndoc-bday' => 'تاريخ الميلاد:',
		'createsigndoc-minage' => 'العمر الأدنى:',
		'createsigndoc-introtext' => 'مقدمة:',
		'createsigndoc-hidden' => 'مخفية',
		'createsigndoc-optional' => 'اختياري',
		'createsigndoc-create' => 'أنشيء',
		'createsigndoc-error-generic' => 'خطأ: $1',
		'createsigndoc-error-pagenoexist' => 'خطأ: الصفحة [[$1]] غير موجودة.',
		'createsigndoc-success' => 'توقيع الوثيقة تم تفعيله بنجاح على [[$1]]. لاختباره، من فضلك زر [{{SERVER}}{{localurl: Special:SignDocument|doc=$2}} هذه الصفحة].',
	),
	'bcl' => array(
		'createsigndoc-pagename' => 'Páhina:',
		'createsigndoc-bday' => 'Kamondágan:',
		'createsigndoc-create' => 'Maggibo',
		'createsigndoc-error-generic' => 'Salâ: $1',
	),
	'ext' => array(
		'createsigndoc-pagename' => 'Páhina:',
		'createsigndoc-allowedgroup' => 'Alabán premitiu:',
		'createsigndoc-optional' => 'Ocional',
		'createsigndoc-create' => 'Creal',
		'createsigndoc-error-pagenoexist' => 'Marru: La páhina [[$1]] nu desisti.',
	),
	'hsb' => array(
		'createsigndocument' => 'Podpisanje dokumentow zmóžnić',
		'createsigndoc-head' => 'Wužij tutón formular, zo by stronu \'Podpisny dokument\' za wotpowědny nastawk wutworił, zo by wužiwarjo přez [[Special:Signdocument]] podpisać móhli. Prošu podaj mjeno nastawka, na kotrymž chceš digatalny podpis zmóžnił, kotři čłonojo kotreje wužiwarskeje skupiny smědźa tam podpisać, kotre pola wužiwarjo smědźa widźeć a kotre měli opcionalne być, trěbnu minimalnu starobu za podpisanje dokumenta (njeje minimum, jeli žane podaće njeje) a krótki zawodny tekst, kotryž tutón dokumement wopisuje a wužiwarjam pokiwy poskića.

<b>Tuchwilu bjez přistupa k datowej bance žana móžnosć njeje, zo bychu so podpisne dokumenty zničili abo změnili, po tym zo su wutworjene.</b> Nimo toho budźe tekst, kotryž so na podpisnej stronje zwobraznja, \'\'aktualny\'\' tekst strony, njedźiwajo na změny ščinjene pozdźišo. Prošu budźe tebi absolutnje wěsty, zo je tutón dokument za podpisanje stabilny dosć, a zawěsć so tež, zo sy wšě pola takle kaž trjeba wupjelnił, \'\'prjedy hač tutón formular wotesćele\'\'.',
		'createsigndoc-pagename' => 'Strona:',
		'createsigndoc-allowedgroup' => 'Dowolena skupina:',
		'createsigndoc-email' => 'E-mejlowa adresa:',
		'createsigndoc-address' => 'Bydlenska adresa:',
		'createsigndoc-extaddress' => 'Město, stat, kraj:',
		'createsigndoc-phone' => 'Telefonowe čisło:',
		'createsigndoc-bday' => 'Narodniny:',
		'createsigndoc-minage' => 'Minimalna staroba:',
		'createsigndoc-introtext' => 'Zawod:',
		'createsigndoc-hidden' => 'Schowany',
		'createsigndoc-optional' => 'Opcionalny',
		'createsigndoc-create' => 'Wutworić',
		'createsigndoc-error-generic' => 'Zmylk: $1',
		'createsigndoc-error-pagenoexist' => 'Zmylk: Strona [[$1]] njeeksistuje.',
		'createsigndoc-success' => 'Podpisanje dokumentow bu wuspěšnje na [[$1]]aktiwizowane. Zo by je testował, wopytaj prošu [{{SERVER}}{{localurl: Special:SignDocument|doc=$2}} tutu stronu:].',
	),
	'la' => array(
		'createsigndoc-pagename' => 'Pagina:',
		'createsigndoc-error-pagenoexist' => 'Error: Pagina [[$1]] non existit.',
	),
	'nl' => array( 
		'createsigndocument'         => 'Documentondertekening inschakelen',
		'createsigndoc-head'         => 
"Gebruik dit formulier om een pagina 'Document ondertekenen' voor een gegeven
pagina te maken, zodat gebruikers het kunnen ondertekenen via
[[Special:SignDocument]]. Geef alstublieft op voor welke pagina u digitaal
ondertekenen wilt inschakelen, welke gebruikersgroepen kunnen ondertekeken,
welke velden zichtbaar moeten zijn voor gebruikers en welke optioneel zijn,
een minimale leeftijd waaraan gebruikers moeten voldoen alvorens te kunnen
ondertekenen (geen beperkingen als leeg gelaten), en een korte inleidende
tekst over het document en instructies voor de gebruikers.

<b>Er is op het moment geen mogelijkheid om te ondertekenen documenten te
verwijderen of te wijzigen nadat ze zijn aangemaakt</b> zonder directe
toegang tot de database. Daarnaast is de tekst van de pagina die wordt
weergegeven op de ondertekeningspagina de ''huidige'' tekst van de pagina,
ongeacht de wijzigingen die erna gemaakt worden. Zorg er alstublieft voor
dat het document een stabiele versie heeft voordat u ondertekenen inschakelt,
en zorg er alstublieft voor dat alle velden de juiste waarden hebben
''voordat u het formulier instuurt''.",
		'createsigndoc-pagename'     => 'Pagina:',
		'createsigndoc-allowedgroup' => 'Toegelaten groep:',
		'createsigndoc-email'        => 'E-mailadres:',
		'createsigndoc-address'      => 'Adres:',
		'createsigndoc-extaddress'   => 'Stad, staat, land:',
		'createsigndoc-phone'        => 'Telefoonnummer:',
		'createsigndoc-bday'         => 'Geboortedatum:',
		'createsigndoc-minage'       => 'Minimum leeftijd:',
		'createsigndoc-introtext'    => 'Inleiding:',
		'createsigndoc-hidden'       => 'Verborgen',
		'createsigndoc-optional'     => 'Optioneel',
		'createsigndoc-create'       => 'Aanmaken',
		'createsigndoc-error-generic'=> 'Fout: $1',
		'createsigndoc-error-pagenoexist' => 'Error: De pagina [[$1]] bestaat niet.',
		'createsigndoc-success'      => 'Documentondertekening is ingeschakeld op
[[$1]]. Ga alstublieft naar [{{SERVER}}{{localurl: Special:SignDocument|doc=$2}} deze pagina] om het te testen.',
	),
);
?>
