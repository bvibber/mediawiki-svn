<?php
/**
 * Internationalisation file for LinkSearch extension.
 *
 * @addtogroup Extensions
*/

$wgLinkSearchMessages = array();

$wgLinkSearchMessages['en'] = array(
	'linksearch'       => 'Search web links',
	'linksearch-text'  => 'Wildcards such as "*.wikipedia.org" may be used.',
	'linksearch-line'  => '$1 linked from $2',
	'linksearch-error' => 'Wildcards may appear only at the start of the hostname.',
);
$wgLinkSearchMessages['cs'] = array(
	'linksearch'       => 'Hledání externích odkazů',
	'linksearch-text'  => 'Lze používat zástupné znaky, např. „*.wikipedia.org“.',
	'linksearch-line'  => '$2 odkazuje na $1',
	'linksearch-error' => 'Zástupné znaky lze použít jen na začátku doménového jména.',
);
$wgLinkSearchMessages['de'] = array(
	'linksearch'       => 'Weblink-Suche',
	'linksearch-text'  => 'Diese Spezialseite ermöglicht die Suche nach Seiten, in denen bestimmte Weblinks enthalten sind. Dabei können Wildcards wie beispielsweise <tt>*.example.com</tt> benutzt werden. ',
	'linksearch-line'  => '$1 ist verlinkt von $2',
	'linksearch-error' => 'Wildcards können nur am Anfang der URL verwendet werden.',
);
$wgLinkSearchMessages['fi'] = array(
	'linksearch'       => 'Etsi ulkoisia linkkejä',
	'linksearch-text'  => 'Asteriskia (*) voi käyttää jokerimerkkinä, esimerkiksi ”*.wikipedia.org”.',
	'linksearch-line'  => '$1 on linkitetty sivulta $2',
	'linksearch-error' => 'Jokerimerkkiä voi käyttää ainoastaan osoitteen alussa.',
);
$wgLinkSearchMessages['he'] = array(
	'linksearch'       => 'חיפוש קישורים חיצוניים',
	'linksearch-text'  => 'ניתן להשתמש בתווים כללים, לדוגמה "‎*.wikipedia.org".',
	'linksearch-line'  => '$1 מקושר מהדף $2',
	'linksearch-error' => 'תווים כלליים יכולים להופיע רק בתחילת שם השרת.',
);
$wgLinkSearchMessages['id'] = array(
	'linksearch'       => 'Cari pranala luar',
	'linksearch-text'  => 'Bentuk pencarian \'\'wildcards\'\' seperti "*.wikipedia.org" dapat digunakan.',
	'linksearch-line'  => '$1 terpaut dari $2',
	'linksearch-error' => '\'\'Wildcards\'\' hanya dapat digunakan di bagian awal dari nama host.'
);
$wgLinkSearchMessages['it'] = array(
	'linksearch'       => 'Ricerca collegamenti esterni',
	'linksearch-text'  => 'È possibile fare uso di metacaratteri, ad es. "*.example.org".',
	'linksearch-line'  => '$1 presente nella pagina $2',
	'linksearch-error' => 'I metacaratteri possono essere usati solo all\'inizio del nome dell\'host.',
);
$wgLinkSearchMessages['ja'] = array(
	'linksearch'       => '外部リンクの検索',
	'linksearch-text'  => '"*.wikipedia.org" のようにワイルドカードを使うことができます。',
	'linksearch-line'  => '$1 が $2 からリンクされています',
	'linksearch-error' => 'ワイルドカードはホスト名の先頭でのみ使用できます。',
);
$wgLinkSearchMessages['kk-kz'] = array(
	'linksearch'       => 'Веб сілтемелерін іздеу',
	'linksearch-text'  => '«*.wikipedia.org» атауына ұқсасты бәдел нышандарды қолдануға болады. ',
	'linksearch-line'  => '$2 дегеннен $1 сілтеген',
	'linksearch-error' => 'Бәдел нышандар тек сервер жайы атауының бастауында болуы мүмкін.',
);
$wgLinkSearchMessages['kk-tr'] = array(
	'linksearch'       => 'Veb siltemelerin izdew',
	'linksearch-text'  => '«*.wikipedia.org» atawına uqsastı bädel nışandardı qoldanwğa boladı. ',
	'linksearch-line'  => '$2 degennen $1 siltegen',
	'linksearch-error' => 'Bädel nışandar tek server jaýı atawınıñ bastawında bolwı mümkin.',
);
$wgLinkSearchMessages['kk-cn'] = array(
	'linksearch'       => 'ۆەب سٴىلتەمەلەرٴىن ٴىزدەۋ',
	'linksearch-text'  => '«*.wikipedia.org» اتاۋىنا ۇقساستى بٴادەل نىشانداردى قولدانۋعا بولادى. ',
	'linksearch-line'  => '$2 دەگەننەن $1 سٴىلتەگەن',
	'linksearch-error' => 'بٴادەل نىشاندار تەك سەرۆەر جايى اتاۋىنىڭ باستاۋىندا بولۋى مٴۇمكٴىن.',
);
$wgLinkSearchMessages['kk'] = $wgLinkSearchMessages['kk-kz'];
$wgLinkSearchMessages['nl'] = array(
	'linksearch'       => 'Zoek externe links',
	'linksearch-text'  => 'Wildcards zoals "*.wikipedia.org" of "*.org" zijn toegestaan.',
	'linksearch-line'  => '$1 gelinkt vanaf $2',
	'linksearch-error' => 'Wildcards zijn alleen toegestaan aan het begin van een hostnaam.'
);
$wgLinkSearchMessages['pt'] = array(
	'linksearch'       => 'Procurar por links da web',
	'linksearch-text'  => 'É possível utilizar "caracteres mágicos" como em "*.wikipedia.org".',
	'linksearch-line'  => '$1 está lincado em $2',
	'linksearch-error' => '"Caracteres mágicos" Wildcards podem ser utilizados apenas no início do endereço.',
);
// Brazillian portuguese inherits portuguese.
$wgLinkSearchMessages['pt-br'] = $wgLinkSearchMessages['pt'];

$wgLinkSearchMessages['fr'] = array(
	'linksearch'	   => 'Rechercher des liens internet',
	'linksearch-text'  => 'Cette page spéciale permet de rechercher les pages dans lesquelles un lien externe apparaît.<br />Des caractères « jokers » peuvent être utilisés, par exemple "*.wikipedia.org".',
	'linksearch-line'  => '$1 avec un lien à partir de $2',
	'linksearch-error' => 'Les caractères « jokers » ne peuvent être utilisés qu’au début du nom de domaine.'
);
$wgLinkSearchMessages['ru'] = array(
	'linksearch'       => 'Поиск внешних ссылок',
	'linksearch-text'  => 'Можно использовать подстановочные символы, например: «*.wikipedia.org».',
	'linksearch-line'  => 'Из $2 ссылка на $1',
	'linksearch-error' => 'Подстановочные символы могут использоваться только в начале адресов.',
);
$wgLinkSearchMessages['sk'] = array(
	'linksearch'       => 'Hľadať webové odkazy',
	'linksearch-text'  => 'Je možné použiť zástupné znaky ako "*.wikipedia.org".',
	'linksearch-line'  => 'Na $1 odkazuje $2',
	'linksearch-error' => 'Zástupné znaky je možné použiť iba na začiatku názvu domény.',
);
$wgLinkSearchMessages['sr-ec'] = array(
	'linksearch'	   => 'Претрага интернет веза',
	'linksearch-text'  => 'Џокери као што су "*.wikipedia.org" могу да се користе.',
	'linksearch-line'  => '$1 повезана са $2',
	'linksearch-error' => 'Џокери могу да се појављују само на почетку домена.'
);
$wgLinkSearchMessages['sr-el'] = array(
	'linksearch'	   => 'Pretraga internet veza',
	'linksearch-text'  => 'Džokeri kao što su "*.wikipedia.org" mogu da se koriste.',
	'linksearch-line'  => '$1 povezana sa $2',
	'linksearch-error' => 'Džokeri mogu da se pojavljuju samo na početku domena.'
);
$wgLinkSearchMessages['sr'] = $wgLinkSearchMessages['sr-ec'];
$wgLinkSearchMessages['zh-cn'] = array(
	'linksearch'       => '搜索网页链接',
	'linksearch-text'  => '可能使用了类似"*.wikipedia.org"的通配符。',
	'linksearch-line'  => '$1 链自 $2',
	'linksearch-error' => '通配符仅可在主机名称的开头使用。',
);
$wgLinkSearchMessages['zh-tw'] = array(
	'linksearch'       => '搜尋網頁連結',
	'linksearch-text'  => '可能使用了類似"*.wikipedia.org"的萬用字元。',
	'linksearch-line'  => '$1 連自 $2',
	'linksearch-error' => '萬用字元僅可在主機名稱的開頭使用。',
);
$wgLinkSearchMessages['zh-yue'] = array(
	'linksearch'       => '搜尋網頁連結',
	'linksearch-text'  => '可能用咗類似"*.wikipedia.org"嘅萬用字元。',
	'linksearch-line'  => '$1 連自 $2',
	'linksearch-error' => '萬用字元只可以響主機名嘅開頭度用。',
);
$wgLinkSearchMessages['zh-hk'] = $wgLinkSearchMessages['zh-tw'];
$wgLinkSearchMessages['zh-sg'] = $wgLinkSearchMessages['zh-cn'];
?>
