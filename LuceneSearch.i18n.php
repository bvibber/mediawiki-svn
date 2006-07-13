<?php
/**
 * Internationalisation file for LuceneSearch extension.
 *
 * @package MediaWiki
 * @subpackage Extensions
*/

$wgLuceneSearchMessages = array();

$wgLuceneSearchMessages['en'] = array(
	'searchnumber'          => "<strong>Results $1-$2 of $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Prev</span>",
	'searchnext'            => "<span style='font-size: small'>Next</span> &#x00BB;",
	'searchscore'           => "Relevance: $1",
	'searchsize'            => "$1KB ($2 words)",
	'searchdidyoumean'      => "Did you mean: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Sorry, there were no exact matches to your query.",
	'searchnearmatches'     => "<b>These pages have similar titles to your query:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
Search in namespaces:\n
$1\n
Search for $3 $9",
	'lucenefallback'        => "There was a problem with the wiki search.
This is probably temporary; try again in a few moments,
or you can search the wiki through an external search service:\n"
);
$wgLuceneSearchMessages['he'] = array(
	'searchnumber'          => "<strong>תוצאות $1-$2 מתוך $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Prev</span>",
	'searchnext'            => "<span style='font-size: small'>הבא</span> &#x00BB;",
	'searchscore'           => "קשר: $1",
	'searchsize'            => "$1 קילובייטים ($2 מילים)",
	'searchdidyoumean'      => "האם התכוונתם ל: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "מצטערים, אין דפים עם הכותרת המדויקת שחיפשת.",
	'searchnearmatches'     => "<b>דפים אלו הם בעלי כותרת דומה למבוקשת:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
חיפוש במרחבי השם:\n
$1\n
חיפוש של $3 $9",
	'lucenefallback'        => "יש בעייה עם מנוע הוויקי.
סביר להניח שהיא זמנית; אנא נסו שנית בעוד מספר דקות.
באפשרותכם גם לחפש בוויקי באמצעות שירותי חיפוש חיצוניים:\n"
);
$wgLuceneSearchMessages['id'] = array(
	'searchnumber'          => "<strong>Hasil pencarian $1-$2 dari $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Sebelumnya</span>",
	'searchnext'            => "<span style='font-size: small'>Selanjutnya</span> &#x00BB;",
	'searchscore'           => "Relevansi: $1",
	'searchsize'            => "$1KB ($2 kata)",
	'searchdidyoumean'      => "Apakah maksud Anda: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Maaf, tidak ditemukan hasil yang tepat sama dengan permintaan Anda.",
	'searchnearmatches'     => "<b>Halaman-halaman berikut mempunyai judul yang mirip dengan permintaan Anda:</b>\n",
	'lucenepowersearchtext' => "
Pencarian di namespace:\n
$1\n
Pencarian terhadap $3 $9",
	'lucenefallback'        => "Ada masalah pada pencarian wiki.
Masalah ini mungkin hanya sementara; silakan coba lagi dalam beberapa saat,
atau gunakan layanan pencari eksternal:\n"
);
?>
