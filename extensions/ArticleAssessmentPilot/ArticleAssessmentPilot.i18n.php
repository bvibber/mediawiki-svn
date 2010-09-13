<?php
$messages = array();

/** English
 * @author Nimish Gautam
 * @author Sam Reed
 * @author Brandon Harris
 */
$messages['en'] = array(
	'articleassessment' => 'Article assessment',
	'articleassessment-desc' => 'Article assessment (pilot version)',
	'articleassessment-yourfeedback' => 'Your feedback',
	'articleassessment-pleaserate' => 'Please take a moment to rate this page below.',
	'articleassessment-submit' => 'Submit',
	'articleassessment-rating-wellsourced' => 'Well-Sourced:',
	'articleassessment-rating-neutrality' => 'Neutral:',
	'articleassessment-rating-completeness' => 'Complete:',
	'articleassessment-rating-readability' => 'Readable:',
	'articleassessment-rating-wellsourced-tooltip' => 'Do you feel this article has sufficient citations and that those citations come from trustworthy sources?',
	'articleassessment-rating-neutrality-tooltip' => 'Do you feel that this article shows a fair representation of all perspectives on the issue?',
	'articleassessment-rating-completeness-tooltip' => 'Do you feel that this article covers the essential topic areas that it should?',
	'articleassessment-rating-readability-tooltip' => 'Do you feel that this article is well-organized and well written?',
	'articleassessment-articlerating' => 'Article rating',
	'articleassessment-error' => 'An error has occurred.
Please try again later.',
	'articleassessment-thanks' => 'Thanks! Your ratings have been saved.',

	# This special page doesn't exist yet, but it will soon.
	'articleassessment-featurefeedback' => 'Give us [[Special:Article Assessment Feedback|feedback]] about this feature.',
	'articleassessment-noratings' => '$1 ($2 {{PLURAL:$2|rating|ratings}})',
	# Some of these messages are unused in the code as it is but may be in the future depending on performance.  They should still be translated.
	'articleassessment-stalemessage-revisioncount' => "This article has been ''revised'' '''$1 times''' since you last reviewed it.
You may wish to re-rate it.",
	'articleassessment-stalemessage-greaterthancount' => "This article has been ''revised'' more than $1 times since you last reviewed it.
You may wish to re-rate it.",
	'articleassessment-stalemessage-norevisioncount' => "This article has been ''revised'' since you last reviewed it.
You may wish to re-rate it.",

	# Links get rewritten in javascript.
	'articleassessment-results-show' => "(Results hidden. [[#|Show]] them.)",
	'articleassessment-results-hide' => "([[#|Hide results]])",
);

/** Message documentation (Message documentation)
 * @author Brandon Harris
 * @author EugeneZelenko
 * @author Sam Reed
 */
$messages['qqq'] = array(
	'articleassessment' => 'The title of the feature. It is about reader feedback.
	
Please visit http://prototype.wikimedia.org/articleassess/Main_Page for a prototype installation.',
	'articleassessment-desc' => '{{desc}}',
	'articleassessment-yourfeedback' => 'This is a box or section header.  It indicates that the contents of the box are personal to the user.',
	'articleassessment-pleaserate' => 'This is a call to action for the user to provide their ratings about the page.',
	'articleassessment-submit' => '{{Identical|Submit}}',
	'articleassessment-rating-wellsourced' => 'This is a rating metric label. The metric is for measuring how researched the article is.',
	'articleassessment-rating-neutrality' => "This is a rating metric label. The metric is for measuring an article's NPOV.",
	'articleassessment-rating-completeness' => 'This is a rating metric label. The metric is for measuring how comprehensive the article is.',
	'articleassessment-rating-readability' => 'This is a rating metric label. The metric is for measuring how well written the article is.',
	'articleassessment-rating-wellsourced-tooltip' => 'This is a tool tip that is designed to explain what the "well-sourced" metric means.',
	'articleassessment-rating-neutrality-tooltip' => 'This is a tool tip that is designed to explain what the "neutrality" metric means.',
	'articleassessment-rating-completeness-tooltip' => 'This is a tool tip that is designed to explain what the "completeness" metric means.',
	'articleassessment-rating-readability-tooltip' => 'This is a tool tip that is designed to explain what the "readability" metric means.',
	'articleassessment-articlerating' => 'This is a box or section header. It indicates that the contents of the box are the average ratings for the article.',
	'articleassessment-error' => 'A generic error message to display on any error.',
	'articleassessment-thanks' => 'The message to display when the user has successfully submitted a rating.',
	'articleassessment-featurefeedback' => 'This is a call to action link for users to provide feedback about the feature.  It takes them to a survey.',
	'articleassessment-noratings' => 'This indicates the number of ratings that the article has received.
Note that PLURAL does not currently work in this message but defaults to the zero; it will work properly in the near future, so keep the calls in.',
	'articleassessment-stalemessage-revisioncount' => 'This is a message shown to the user when their ratings are "stale" and includes the number of revisions since.',
	'articleassessment-stalemessage-greaterthancount' => 'This is a message shown to the user when their ratings are "stale". It allows for not looking up the total number of revisions.',
	'articleassessment-stalemessage-norevisioncount' => 'This is a message shown to the user when their ratings are "stale" and does NOT include the number of revisions. This is an ambiguous reason, and allows for us to have complicated staleness patterns. This is the preferred message.',
	'articleassessment-results-show' => 'This is an explanatory control that, when clicked, will display hidden aggregate ratings.
The incomplete looking wikilinks get rewritten in javascript.',
	'articleassessment-results-hide' => 'This is a control that, when clicked, will hide the aggregate ratings.
The incomplete looking wikilinks get rewritten in javascript.',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'articleassessment-yourfeedback' => 'U terugvoer',
	'articleassessment-submit' => 'Dien in',
	'articleassessment-rating-wellsourced' => 'Goed van bronne voorsien:',
	'articleassessment-rating-neutrality' => 'Neutraal:',
	'articleassessment-rating-completeness' => 'Volledig:',
	'articleassessment-rating-readability' => 'Leesbaar:',
	'articleassessment-articlerating' => 'Artikel gradering',
	'articleassessment-results-show' => '(Resultate versteek. [[#|Wys]] hulle.)',
	'articleassessment-results-hide' => '([[#|Versteek resultate]])',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'articleassessment' => 'Адзнака артыкулаў',
	'articleassessment-desc' => 'Адзнака артыкулаў (пачатковая вэрсія)',
	'articleassessment-yourfeedback' => 'Ваш водгук',
	'articleassessment-pleaserate' => 'Калі ласка, знайдзіце час, каб адзначыць старонку ўнізе.',
	'articleassessment-submit' => 'Даслаць',
	'articleassessment-rating-wellsourced' => 'Спасылкі на крыніцы:',
	'articleassessment-rating-neutrality' => 'Нэўтральнасьць:',
	'articleassessment-rating-completeness' => 'Скончанасьць:',
	'articleassessment-rating-readability' => 'Лёгкасьць чытаньня:',
	'articleassessment-rating-wellsourced-tooltip' => 'Вы лічыце, што гэты артыкул мае дастаткова цытатаў, і яны спасылаюцца на крыніцы, якія заслугоўваюць даверу?',
);

/** German (Deutsch)
 * @author Kghbln
 */
$messages['de'] = array(
	'articleassessment' => 'Artikeleinschätzung',
	'articleassessment-desc' => 'Ermöglicht die Einschätzung von Artikeln (Pilotversion)',
	'articleassessment-yourfeedback' => 'Deine Rückmeldung',
	'articleassessment-pleaserate' => 'Bitte nehme dir kurz Zeit diesen Artikel unten auf dieser Seite einzuschätzen.',
	'articleassessment-submit' => 'Speichern',
	'articleassessment-rating-wellsourced' => 'Gut belegt:',
	'articleassessment-rating-neutrality' => 'Neutral:',
	'articleassessment-rating-completeness' => 'Vollständig:',
	'articleassessment-rating-readability' => 'Verständlich:',
	'articleassessment-rating-wellsourced-tooltip' => 'Hast du den Eindruck, dass dieser Artikel über genügend Quellenangaben verfügt und diese zudem aus vertrauenswürdigen Quellen stammen?',
	'articleassessment-rating-neutrality-tooltip' => 'Hast du den Eindruck, dass dieser Artikel eine ausgewogene Darstellung aller mit dessen Inhalt verbundenen Aspekte enthält?',
	'articleassessment-rating-completeness-tooltip' => 'Hast du den Eindruck, dass dieser Artikel alle wichtigen Aspekte enthält, die mit dessen Inhalt zusammenhängen?',
	'articleassessment-rating-readability-tooltip' => 'Hast du den Eindruck, dass dieser Artikel gut strukturiert sowie geschrieben wurde?',
	'articleassessment-articlerating' => 'Einschätzung des Artikels',
	'articleassessment-error' => 'Ein Fehler ist aufgetreten.
Bitte versuche es später erneut.',
	'articleassessment-thanks' => 'Vielen Dank! Deine Einschätzung wurde gespeichert.',
	'articleassessment-featurefeedback' => 'Gebe uns bitte eine [[Special:Article Assessment Feedback|Rückmeldung]] zu dieser Funktion zur Einschätzung eines Artikels.',
	'articleassessment-noratings' => '$1 ($2 {{PLURAL:$2|Einschätzung|Einschätzungen}})',
	'articleassessment-stalemessage-revisioncount' => "Dieser Artikel wurde seit deiner letzten Einschätzung „$1-mal“ ''bearbeitet''.
Vielleicht möchtest du ihn erneut einschätzen.",
	'articleassessment-stalemessage-greaterthancount' => "Dieser Artikel wurde seit deiner letzten Einschätzung mehr als „$1-mal“ ''bearbeitet''.
Vielleicht möchtest du ihn erneut einschätzen.",
	'articleassessment-stalemessage-norevisioncount' => "Dieser Artikel wurde seit deiner letzten Einschätzung ''bearbeitet''.
Vielleicht möchtest du ihn erneut einschätzen.",
	'articleassessment-results-show' => '(Ergebnisse sind ausgeblendet. [[#|Einblenden]].)',
	'articleassessment-results-hide' => '([[#|Ergebnisse ausblenden]])',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'articleassessment' => 'Avaliación do artigo',
	'articleassessment-desc' => 'Versión piloto da avaliación dos artigos',
	'articleassessment-yourfeedback' => 'Os seus comentarios',
	'articleassessment-submit' => 'Enviar',
	'articleassessment-rating-wellsourced' => 'Ben documentado:',
	'articleassessment-rating-neutrality' => 'Neutral:',
	'articleassessment-rating-completeness' => 'Completo:',
	'articleassessment-rating-readability' => 'Lexible:',
	'articleassessment-articlerating' => 'Avaliación do artigo',
	'articleassessment-error' => 'Houbo un erro.
Inténteo de novo máis tarde.',
	'articleassessment-featurefeedback' => 'Déanos [[Special:Article Assessment Feedback|a súa opinión]] sobre esta característica.',
	'articleassessment-noratings' => '$1 ($2 {{PLURAL:$2|avaliación|avaliacións}})',
	'articleassessment-results-show' => '(Resultados agochados. [[#|Mostralos]].)',
	'articleassessment-results-hide' => '([[#|Agochar os resultados]])',
);

/** Japanese (日本語)
 * @author Yanajin66
 */
$messages['ja'] = array(
	'articleassessment' => '記事の評価',
	'articleassessment-desc' => '記事の評価（パイロット版）',
	'articleassessment-yourfeedback' => 'あなたのフィードバック',
	'articleassessment-pleaserate' => 'このページの評価を算出していますので、少しお待ちください。',
	'articleassessment-submit' => '送信',
	'articleassessment-rating-wellsourced' => '良くソース化できたもの:',
	'articleassessment-rating-neutrality' => '中立的なもの:',
	'articleassessment-rating-completeness' => '完了:',
	'articleassessment-rating-readability' => '読み込み可能なもの:',
	'articleassessment-rating-wellsourced-tooltip' => 'あなたはこの記事が十分な引用を含んでいて、それらの引用は信憑性のあるソースからのものだと感じますか？',
	'articleassessment-rating-neutrality-tooltip' => 'あなたはこの記事が問題点における全ての見解の中で公正な表現だと感じますか？',
	'articleassessment-rating-completeness-tooltip' => 'あなたはこの記事が不可欠な話題の領域をカバーしていると感じますか？',
	'articleassessment-rating-readability-tooltip' => 'あなたはこの記事が良く整理され、良く書かれていると感じますか？',
	'articleassessment-articlerating' => '記事の評価',
	'articleassessment-error' => 'エラーが発生しました。
後でもう一度試みてください。',
	'articleassessment-thanks' => 'ありがとうございます！あなたの評価は保存されました。',
	'articleassessment-stalemessage-revisioncount' => "この記事はあなたが最後にレビューしてから、'''$1 回'''''修正''されました。
あなたは再評価したいかもしれません。",
	'articleassessment-stalemessage-greaterthancount' => "この記事はあなたが最後にレビューしてから、'''$1 回以上'''''修正''されました。
あなたは再評価したいかもしれません。",
	'articleassessment-stalemessage-norevisioncount' => "この記事はあなたが最後にレビューしてから、''修正''されました。
あなたは再評価したいかもしれません。",
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'articleassessment' => 'Artikelaschätzung',
	'articleassessment-desc' => 'Artikelaschätzung Pilotversioun',
	'articleassessment-yourfeedback' => 'Äre Feedback',
	'articleassessment-pleaserate' => 'Huelt Iech w.e.g. een Ament fir déi Säit hei drënner ze bewäerten.',
	'articleassessment-submit' => 'Späicheren',
	'articleassessment-rating-wellsourced' => 'Gudd dokumentéiert:',
	'articleassessment-rating-neutrality' => 'Neutral:',
	'articleassessment-rating-completeness' => 'Komplett:',
	'articleassessment-rating-readability' => 'Verständlech:',
	'articleassessment-articlerating' => 'Bewäertung vum Artikel',
	'articleassessment-error' => 'Et ass e Feeler geschitt.
Probéiert w.e.g. méi spéit nach emol.',
	'articleassessment-thanks' => 'Merci! Är Bewäertung gouf gespäichert.',
	'articleassessment-noratings' => '$1 ($2 {{PLURAL:$2|Bewäertung|Bewäertungen}})',
	'articleassessment-results-show' => "(D'Resultater si verstopp. Resultater [[#|weisen]].)",
	'articleassessment-results-hide' => '([[#|Resultater verstoppen]])',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'articleassessment' => 'Оценување на статија',
	'articleassessment-desc' => 'Пилотна верзија на Оценување на статија',
	'articleassessment-yourfeedback' => 'Ваше мислење',
	'articleassessment-pleaserate' => 'Одвојте момент за да ја оцените страницава подолу',
	'articleassessment-submit' => 'Поднеси',
	'articleassessment-rating-wellsourced' => 'Доволно извори:',
	'articleassessment-rating-neutrality' => 'Неутрална:',
	'articleassessment-rating-completeness' => 'Исцрпна:',
	'articleassessment-rating-readability' => 'Читлива:',
	'articleassessment-rating-wellsourced-tooltip' => 'Дали сметате дека статијава има доволно наводи и дека се преземени од доверливи извори?',
	'articleassessment-rating-neutrality-tooltip' => 'Дали сметате дека статијава на праведен начин ги застапува сите гледишта на оваа проблематика?',
	'articleassessment-rating-completeness-tooltip' => 'Дали сметате дека статијава ги обработува најважните основни теми што треба да се обработат?',
	'articleassessment-rating-readability-tooltip' => 'Дали сметате дека статијава е добро организирана и убаво напишана?',
	'articleassessment-articlerating' => 'Оценки за статијата',
	'articleassessment-error' => 'Се појави грешка.
Обидете се подоцна.',
	'articleassessment-thanks' => 'Ви благодариме! Вашите оценки се зачувани.',
	'articleassessment-featurefeedback' => 'Дајте ваше [[Special:Article Assessment Feedback|мислење]] за оваа функција.',
	'articleassessment-noratings' => '$1 ($2 {{PLURAL:$2|оценка|оценки}})',
	'articleassessment-stalemessage-revisioncount' => "Статијава е ''преработена'' '''$1 пати''' од последниот преглед наваму.
Ви предлагаме да ја преоцените.",
	'articleassessment-stalemessage-greaterthancount' => "Статијава е ''преработена'' повеќе од $1 пати од последниот преглед наваму.
Ви предлагаме да ја преоцените.",
	'articleassessment-stalemessage-norevisioncount' => "Статијава е ''преработена'' од последниот преглеед наваму.
Ви предлагаме да ја преоцените.",
	'articleassessment-results-show' => '(Резултатите се скриени. [[#|Прикажи]] ги.)',
	'articleassessment-results-hide' => '([[#|Сокриј резултати]])',
);

/** Piedmontese (Piemontèis)
 * @author Dragonòt
 */
$messages['pms'] = array(
	'articleassessment' => "Valutassion ëd j'artìcoj",
	'articleassessment-desc' => "Vërsion pilòta dla valutassion ëd j'artìcoj",
	'articleassessment-yourfeedback' => 'Tò artorn',
	'articleassessment-pleaserate' => 'Për piasì pija un moment për valuté sta pàgina sota.',
	'articleassessment-submit' => 'Spediss',
	'articleassessment-rating-wellsourced' => 'Bon-e-Sorgiss:',
	'articleassessment-rating-neutrality' => 'Neutral:',
	'articleassessment-rating-completeness' => 'Completa:',
	'articleassessment-rating-readability' => 'Lesìbil:',
	'articleassessment-rating-wellsourced-tooltip' => "Pensës-to che sto artìcol a l'abia basta citassion e che ste citassion a rivo da sorziss sigure?",
	'articleassessment-rating-neutrality-tooltip' => 'Pensës-to che sto artìcol a mosta na giusta rapresentassion ëd tute le prospetive ant sua edission?',
	'articleassessment-rating-completeness-tooltip' => "Pensës-to che sto artìcol a coata le aire essensial ëd l'argoment com a dovrìa?",
	'articleassessment-rating-readability-tooltip' => 'Pensës-to che sto artìcol a sia bin-organisà e bin scrivù?',
	'articleassessment-articlerating' => "Valutassion ëd l'artìcol",
	'articleassessment-error' => "Un eror a l'é capità.
Për piasì preuva torna pi tard.",
	'articleassessment-thanks' => 'Mersì! Toe valutassion a son ëstàite salvà.',
	'articleassessment-featurefeedback' => 'Dane [[Special:Article Assessment Feedback|artorn]] su sta funsion.',
	'articleassessment-noratings' => '$1 ($2 {{PLURAL:$2|valutassion|valutassion}})',
	'articleassessment-stalemessage-revisioncount' => "Sto artìcol a l'é stàit ''revisionà'' '''$1 vire''' da l'ùltima vira ch'it l'has revisionalo.
It podrìe vorejlo revaluté.",
	'articleassessment-stalemessage-greaterthancount' => "Sto artìcol a l'é stàit ''revisionà'' pi che $1 vire da l'ùltima vira ch'it l'has revisionalo.
It podrìe vorejlo revaluté.",
	'articleassessment-stalemessage-norevisioncount' => "Sto artìcol a l'é stàit ''revisionà'' da quand ch'it l'has revisionalo.
It podrìe vorejlo revaluté.",
	'articleassessment-results-show' => '(Arzultà stërmà. [[#|Mostlo]].)',
	'articleassessment-results-hide' => '([[#|Stërma arzultà]])',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'articleassessment-pleaserate' => 'Xin hãy dành một chút thì giờ để đánh giá trang này ở dưới:',
	'articleassessment-submit' => 'Đánh giá',
	'articleassessment-rating-wellsourced' => 'Đầy đủ nguồn:',
	'articleassessment-rating-neutrality' => 'Trung lập:',
	'articleassessment-rating-completeness' => 'Đầy đủ:',
	'articleassessment-rating-readability' => 'Dễ đọc:',
	'articleassessment-rating-wellsourced-tooltip' => 'Bạn có cảm thấy rằng bày này chú thích nguồn gốc đầy đủ và đáng tin các nguồn?',
	'articleassessment-rating-neutrality-tooltip' => 'Bạn có cảm thấy rằng bài này đại diện công bằng cho tất cả các quan điểm về các vấn đề?',
	'articleassessment-rating-completeness-tooltip' => 'Bạn có cảm thấy rằng bài này bao gồm các đề tài cần thiết?',
	'articleassessment-rating-readability-tooltip' => 'Bạn có cảm thấy rằng bài này được sắp xếp đàng hoàng có văn bản hay?',
	'articleassessment-articlerating' => 'Đánh giá bài',
	'articleassessment-error' => 'Đã gặp lỗi.
Xin hãy thử lại sau.',
	'articleassessment-thanks' => 'Cám ơn! Đánh giá của bạn đã được lưu.',
	'articleassessment-noratings' => '$1 ($2 đánh giá)',
	'articleassessment-stalemessage-revisioncount' => "Bài này đã được ''chỉnh sửa'' '''$1 lần''' sau lần cuối bạn xem xét nó.
Bạn có thể muốn đánh giá nó một lần nữa.",
	'articleassessment-stalemessage-greaterthancount' => "Bài này đã được ''chỉnh sửa'' hơn $1 lần sau lần cuối bạn xem xét nó.
Bạn có thể muốn đánh giá nó một lần nữa.",
	'articleassessment-stalemessage-norevisioncount' => "Bài này đã được ''chỉnh sửa'' sau lần cuối bạn xem xét nó.
Bạn có thể muốn đánh giá nó một lần nữa.",
	'articleassessment-results-show' => '(Các kết quả được ẩn. [[#|Hiện]] kết quả.)',
	'articleassessment-results-hide' => '([[#|Ẩn kết quả]])',
);

