<?php
/**
 * Internationalisation file for CrossNamespaceLinks extension.
 *
 * @package MediaWiki
 * @subpackage Extensions
*/

$wgCrossNamespaceLinksMessages = array();

$wgCrossNamespaceLinksMessages['en'] = array(
	'crossnamespacelinks'         => 'Cross-namespace links',
	'crossnamespacelinks-summary' => 'This page provides a list of links from a page in the main namespace to a page in other namespaces (except for {{ns:special}}, {{ns:talk}}, {{ns:project}} and {{ns:template}} namespaces), which are not advisable to use.',
	'crossnamespacelinkstext'     => '$1: $2 {{PLURAL:$2|link|links}} to $3'
);
$wgCrossNamespaceLinksMessages['cs'] = array(
	'crossnamespacelinks'     => 'Odkazy napříč jmennými prostory',
	'crossnamespacelinkstext' => '$1: $2 {{PLURAL:$2|odkaz|odkazy|odkazů}} do $3'
);
$wgCrossNamespaceLinksMessages['de'] = array(
	'crossnamespacelinks'     => 'Artikel mit Links in andere Namensräume',
	'crossnamespacelinks-summary' => 'Diese Liste zeigt Artikel, die Verweise auf Seiten anderer Namensräume enthalten. Ausgenommen sind dabei die Namensräume {{ns:special}}, {{ns:talk}}, {{ns:project}} and {{ns:template}}.',
 	'crossnamespacelinkstext' => '$1: {{PLURAL:$2|1 Link verweist|$2 Links verweisen}} in den $3-Namensraum'
);
$wgCrossNamespaceLinksMessages['fi'] = array(
	'crossnamespacelinks'         => 'Nimiavaruuksienväliset linkit',
	'crossnamespacelinks-summary' => 'Alla on lista linkeistä, jotka osoittavat päänimiavaruudesta toiseen nimiavaruuteen — pois lukien linkit {{ns:special}}-, {{ns:talk}}-, {{ns:project}}- ja {{ns:template}}nimiavaruuksiin. Linkkejä muihin nimiavaruuksiin tulisi välttää.',
	'crossnamespacelinkstext'     => '$1: $2 {{PLURAL:$2|linkki|linkkiä}} nimiavaruuteen $3'
);
$wgCrossNamespaceLinksMessages['fr'] = array(
	'crossnamespacelinks'         => 'Pages contenant des liens vers un autre espace de nom',
	'crossnamespacelinks-summary' => 'Cette page fournit une liste des pages de l’espace de nom principal qui ont un lien vers un autre espace de nom (excepté pour les espaces de nom {{ns:special}}, {{ns:talk}}, {{ns:project}} et {{ns:template}}), ce qui n’est recommandé.',
	'crossnamespacelinkstext'     => '$1 : possède $2 {{PLURAL:$2|lien|liens}} vers l’espace « $3 »'
);
$wgCrossNamespaceLinksMessages['he'] = array(
	'crossnamespacelinks'         => 'קישורים מדפי תוכן למרחבי שם אחרים',
	'crossnamespacelinks-summary' => 'דף זה מספק רשימה של קישורים מדפים במרחב השם הראשי לדפים במרחבי שם אחרים (למעט מרחבי השם {{ns:special}}, {{ns:talk}}, {{ns:project}} ו{{ns:template}}), שאינם רצויים לשימוש.',
	'crossnamespacelinkstext'     => '$1: {{plural:$2|קישור אחד|$2 קישורים}} למרחב $3'
);
$wgCrossNamespaceLinksMessages['id'] = array(
	'crossnamespacelinks'     => 'Pranala lintas ruang nama',
	'crossnamespacelinks-summary' => 'Halaman ini memberikan daftar pranala dari suatu halaman di ruang nama utama ke halaman lain di ruang nama lain (kecuali {{ns:special}}, {{ns:talk}}, {{ns:project}} dan {{ns:template}} namespaces), yang tidak dianjurkan untuk digunakan.',
	'crossnamespacelinkstext' => '$1: $2 terpaut ke $3'
);
$wgCrossNamespaceLinksMessages['it'] = array(
	'crossnamespacelinks'     => 'Collegamenti tra namespace',
	'crossnamespacelinkstext' => '$1: $2 {{PLURAL:$2|collegamento|collegamenti}} al namespace $3'
);
$wgCrossNamespaceLinksMessages['ja'] = array(
	'crossnamespacelinks'         => '名前空間をまたぐリンク',
	'crossnamespacelinks-summary' => '通常名前空間から他の名前空間（ {{ns:special}}, {{ns:talk}}, {{ns:project}}, {{ns:template}} を除く）のページへとリンクしているページの一覧です。',
	'crossnamespacelinkstext'     => '$1: $2 個 の $3 へのリンク'
);
$wgCrossNamespaceLinksMessages['kk-kz'] = array(
	'crossnamespacelinks'         => 'Басқа есім аясына сілтейтін беттер',
	'crossnamespacelinks-summary' => 'Бұл бетте негізгі есім аясындағы беттегі басқа есім аялырындағы ({{ns:special}}, {{ns:talk}}, {{ns:project}} және {{ns:template}} есім аяларынан тыс) беттерге сілтеме тізімі беріледі. Осындай сілтемелерді қолдануға ұсынылмайды.',
	'crossnamespacelinkstext'     => '«$1» беті: «$3» есім аясына $2 сілтеме '
);
$wgCrossNamespaceLinksMessages['kk-tr'] = array(
	'crossnamespacelinks'         => 'Basqa esim ayasına silteýtin better',
	'crossnamespacelinks-summary' => 'Bul bette negizgi esim ayasındağı bettegi basqa esim ayalırındağı ({{ns:special}}, {{ns:talk}}, {{ns:project}} jäne {{ns:template}} esim ayalarınan tıs) betterge silteme tizimi beriledi. Osındaý siltemelerdi qoldanwğa usınılmaýdı.',
	'crossnamespacelinkstext'     => '«$1» beti: «$3» esim ayasına $2 silteme '
);
$wgCrossNamespaceLinksMessages['kk-cn'] = array(
	'crossnamespacelinks'         => 'باسقا ەسٴىم اياسىنا سٴىلتەيتٴىن بەتتەر',
	'crossnamespacelinks-summary' => 'بۇل بەتتە نەگٴىزگٴى ەسٴىم اياسىنداعى بەتتەگٴى باسقا ەسٴىم ايالىرىنداعى ({{ns:special}}, {{ns:talk}}, {{ns:project}} جٴانە {{ns:template}} ەسٴىم ايالارىنان تىس) بەتتەرگە سٴىلتەمە تٴىزٴىمٴى بەرٴىلەدٴى. وسىنداي سٴىلتەمەلەردٴى قولدانۋعا ۇسىنىلمايدى.',
	'crossnamespacelinkstext'     => '«$1» بەتٴى: «$3» ەسٴىم اياسىنا $2 سٴىلتەمە '
);
$wgCrossNamespaceLinksMessages['kk'] = $wgCrossNamespaceLinksMessages['kk-kz'];
$wgCrossNamespaceLinksMessages['nl'] = array(
	'crossnamespacelinks'     => 'Pagina\'s met verwijzingen naar andere naamruimten',
	'crossnamespacelinkstext' => '$1: $2 {{PLURAL:$2|verwijzing|verwijzingen}} naar $3'
);
$wgCrossNamespaceLinksMessages['pl'] = array(
	'crossnamespacelinks'     => 'Linki między przestrzeniami nazw',
	'crossnamespacelinkstext' => '$1: $2 {{PLURAL:$2|link|linki}} do $3'
);
$wgCrossNamespaceLinksMessages['pt'] = array(
	'crossnamespacelinks'         => 'Saltos de Espaços Nominais',
	'crossnamespacelinks-summary' => 'Esta página proporciona uma lista de links provenientes a partir de uma página no espaço nominal principal para outra alocada em outro espaço nominal (exceção feita para os espaços nominais {{ns:special}}, {{ns:talk}}, {{ns:project}} e {{ns:template}}), os quais costuma-se não ser aconselhável de existirem.',
	'crossnamespacelinkstext'     => '$1: $2 {{PLURAL:$2|linca|lincam}} para $3'
);
$wgCrossNamespaceLinksMessages['pt-br'] = $wgCrossNamespaceLinksMessages['pt'];
$wgCrossNamespaceLinksMessages['sk'] = array(
	'crossnamespacelinks'     => 'Odkazy medzi mennými priestormi',
	'crossnamespacelinkstext' => '$1: $2 {{PLURAL:$2|odkaz|odkazy|odkazov}} na $3'
);
$wgCrossNamespaceLinksMessages['sr-ec'] = array(
	'crossnamespacelinks'         => 'Везе ка именским просторима',
	'crossnamespacelinks-summary' => 'Ова страница пружа списак веза са странице у главном именском простору ка страници у неком другом именском простору (осим за {{ns:special}}, {{ns:talk}}, {{ns:project}} и {{ns:template}} именске просторе), чија се употреба не препоручује.',
	'crossnamespacelinkstext'     => '$1: $2 {{PLURAL:$2|веза|везе|веза}} ка $3 именском простору'
);
$wgCrossNamespaceLinksMessages['sr-el'] = array(
	'crossnamespacelinks'         => 'Veze ka imenskim prostorima',
	'crossnamespacelinks-summary' => 'Ova stranica pruža spisak veza sa stranice u glavnom imenskom prostoru ka stranici u nekom drugom imenskom prostoru (osim za {{ns:special}}, {{ns:talk}}, {{ns:project}} i {{ns:template}} imenske prostore), čija se upotreba ne preporučuje.',
	'crossnamespacelinkstext'     => '$1: $2 {{PLURAL:$2|veza|veze|veza}} ka $3 imenskom prostoru'
);
$wgCrossNamespaceLinksMessages['sr'] = $wgCrossNamespaceLinksMessages['sr-ec'];
$wgCrossNamespaceLinksMessages['zh-cn'] = array(
	'crossnamespacelinks'     => '跨名字空间的链接',
	'crossnamespacelinkstext' => '$1: $2 个链接到 $3'
);
$wgCrossNamespaceLinksMessages['zh-tw'] = array(
	'crossnamespacelinks'     => '跨名字空間的連結',
	'crossnamespacelinkstext' => '$1: $2 個連結到 $3'
);
$wgCrossNamespaceLinksMessages['zh-yue'] = array(
	'crossnamespacelinks'     => '跨空間名連結',
	'crossnamespacelinkstext' => '$1: $2 個連結到 $3'
);
$wgCrossNamespaceLinksMessages['zh-hk'] = $wgCrossNamespaceLinksMessages['zh-tw'];
$wgCrossNamespaceLinksMessages['zh-sg'] = $wgCrossNamespaceLinksMessages['zh-cn'];
?>
