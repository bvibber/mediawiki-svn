<?php
/**
 * Internationalisation file for extension AbsenteeLandlord.
 */

$messages = array();

/** English
 */
$messages['en'] = array(
	'purewikideletion' => 'Pure wiki deletion',
	'randomexcludeblank' => 'Random page (exclude blank)',
	'populateblankedpagestable' => 'Populate blanked pages table',
	'purewikideletion-desc' => 'Among other things, causes blanked pages to be redlinked',
	'purewikideletion-pref-watchblank' => 'Add pages I blank to my watchlist',
	'purewikideletion-pref-watchunblank' => 'Add pages I unblank to my watchlist',
	'purewikideletion-blanked' => "A former version of this page was blanked by [[User:$1|$1]] ([[User talk:$1|talk]]) ([[Special:Contributions/$1|contribs]]) on $5 at $6.

The reason given for blanking was: ''<nowiki>$3</nowiki>''.

You may [{{fullurl:{{FULLPAGENAMEE}}|action=history}} view the page's history], [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} edit the last version], or type new page into the white space below.",
	'blank-log' => 'blank',
	'blank-log-name' => 'Blank log',
	'blank-log-header' => 'Below is a list of page blankings and unblankings.',
	'blank-log-entry-blank' => 'blanked $1',
	'blank-log-entry-unblank' => 'unblanked $1',
	'blank-log-link' => '[[{{#Special:Log}}/blank|blank log]]',
	'purewikideletion-blanknologin' => 'Not logged in',
	'purewikideletion-blanknologintext' => 'You must be a registered user and [[Special:UserLogin|logged in]] to blank a page.',
	'purewikideletion-unblanknologintext' => 'You must be a registered user and [[Special:UserLogin|logged in]] to unblank a page.',
	'purewikideletion-blankedtext' => '[[$1]] has been blanked.
See $2 for a record of recent blankings.',
	'purewikideletion-population-done' => 'Done populating blanked_page table.',
	'right-purewikideletion' => '[[Special:PopulateBlankedPagesTable|Populate]] the blanked pages table',
);

/** Arabic (العربية) */
$messages['ar'] = array(
	'purewikideletion' => 'حذف الويكي النقي',
	'randomexcludeblank' => 'صفحة عشوائية (لا يشمل ذلك الفارغة)',
	'populateblankedpagestable' => 'املأ جدول الصفحات الفارغة',
	'purewikideletion-desc' => 'من ضمن أشياء أخرى، يؤدي إلى أن تكون الصفحات الفارغة ذات وصلات حمراء',
	'purewikideletion-pref-watchblank' => 'أضف الفصفحات التي أفرغها إلى قائمة مراقبتي',
	'purewikideletion-pref-watchunblank' => 'أضف الصفحات التي أملؤها إلى قائمة مراقبتي',
	'purewikideletion-blanked' => "نسخة سابقة من هذه الصفحة تم إفراغها بواسطة [[User:$1|$1]] ([[User talk:$1|نقاش]]) ([[Special:Contributions/$1|مساهمات]]) في $2

السبب المعطى للإفراغ كان: ''<nowiki>$3</nowiki>''.

يمكنك [{{fullurl:{{FULLPAGENAMEE}}|action=history}} رؤية تاريخ المقالة], [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} تعديل آخر نسخة]،
أو كتابة صفحة جديدة في الفراغ الأبيض بالأسفل.",
	'blank-log' => 'فارغة',
	'blank-log-name' => 'سجل الإفراغ',
	'blank-log-header' => 'بالأسفل قائمة بعمليات إفراغ وملأ الصفحات.',
	'blank-log-entry-blank' => 'أفرغ $1',
	'blank-log-entry-unblank' => 'ملأ $1',
	'purewikideletion-blanknologin' => 'غير مسجل الدخول',
	'purewikideletion-blanknologintext' => 'يجب أن تكون مستخدما مسجلا و [[Special:UserLogin|تسجل الدخول]] لإفراغ أو ملأ صفحة.',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'purewikideletion' => 'Строгае вікі-выдаленьне',
	'randomexcludeblank' => 'Выпадковая старонка (за выключэньнем пустых)',
	'populateblankedpagestable' => 'Запоўніць табліцу пустых старонак',
	'purewikideletion-desc' => 'Сярод іншых наступстваў, спасылкі на пустыя старонкі будуць выдзяляцца чырвоным колерам',
	'purewikideletion-pref-watchblank' => 'Дадаваць у мой сьпіс назіраньня старонкі, якія я буду ачышчаць',
	'purewikideletion-pref-watchunblank' => 'Дадаваць у мой сьпіс назіраньня старонкі, у якіх я буду адмяняць ачыстку',
	'purewikideletion-blanked' => "Папярэдняя вэрсія гэтай старонкі была ачышчаная [[User:$1|$1]] ([[User talk:$1|гутаркі]]) ([[Special:Contributions/$1|унёсак]]) $2

Пададзеная прычына ачысткі была: ''<nowiki>$3</nowiki>''.

Вы можаце [{{fullurl:{{FULLPAGENAMEE}}|action=history}} праглядзець гісторыю старонкі], [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} рэдагаваць апошнюю вэрсію], альбо ўвесьці тэкст новай старонкі ў белае поле ніжэй.",
	'blank-log' => 'ачыстка',
	'blank-log-name' => 'Журнал ачыстак',
	'blank-log-header' => 'Ніжэй пададзены сьпіс ачышчаных старонак і ў якіх ачыстка была адмененая.',
	'blank-log-entry-blank' => 'ачышчаная $1',
	'blank-log-entry-unblank' => 'адмененая ачыстка $1',
);

/** Breton (Brezhoneg)
 * @author Y-M D
 */
$messages['br'] = array(
	'purewikideletion-blanknologin' => 'Digevreet',
);

/** German (Deutsch)
 * @author Kghbln
 * @author The Evil IP address
 */
$messages['de'] = array(
	'purewikideletion' => 'Leerung von Seiten',
	'randomexcludeblank' => 'Zufällige Seite (geleerte Seiten ausgenommen)',
	'populateblankedpagestable' => 'Tabelle mit geleerten Seiten erzeugen',
	'purewikideletion-desc' => 'Sorgt unter anderem dafür, dass geleerte Seiten als roter Link erscheinen',
	'purewikideletion-pref-watchblank' => 'Selbst geleerte Seiten automatisch beobachten',
	'purewikideletion-pref-watchunblank' => 'Seiten mit von mir zurückgenommenen Leerungen automatisch beobachten',
	'purewikideletion-blanked' => "Eine ehemalige Version dieser Seite wurde am $2 von [[User:$1|$1]] ([[User talk:$1|talk]]) ([[Special:Contributions/$1|contribs]]) geleert.

Angegebene Begründung für die Leerung: ''<nowiki>$3</nowiki>''.

Du kannst [{{fullurl:{{FULLPAGENAMEE}}|action=history}} die Versionsgeschichte betrachten], [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} dessen letzte Version bearbeiten] oder unten im Bearbeitungsfeld eine neue Seite erfassen.",
	'blank-log' => 'leeren',
	'blank-log-name' => 'Leerungs-Logbuch',
	'blank-log-header' => 'Es folgt eine Liste von Seitenleerungen und zurückgenommenen Entleerungen.',
	'blank-log-entry-blank' => 'leerte „$1“',
	'blank-log-entry-unblank' => 'entleerte „$1“',
	'blank-log-link' => '[[Special:Log/blank|Leerungs-Logbuch]]',
	'purewikideletion-blanknologin' => 'Nicht angemeldet',
	'purewikideletion-blanknologintext' => 'Du musst ein registrierter Benutzer und [[Special:UserLogin|angemeldet sein]], um eine Seite zu leeren.',
	'purewikideletion-unblanknologintext' => 'Du musst ein registrierter Benutzer und [[Special:UserLogin|angemeldet sein]], um die Leerung einer Seite zurücknehmen zu können.',
	'purewikideletion-blankedtext' => '„[[$1]]“ wurde geleert.
Siehe das $2 für eine Liste der letzten Leerungen.',
	'purewikideletion-population-done' => 'Tabelle „blanked_page“ mit geleerten Seiten erzeugt',
	'right-purewikideletion' => '[[Special:PopulateBlankedPagesTable|Erzeuge]] Tabelle mit geleerten Seiten',
);

/** Spanish (Español)
 * @author Crazymadlover
 */
$messages['es'] = array(
	'blank-log' => 'en blanco',
);

/** French (Français)
 * @author IAlex
 */
$messages['fr'] = array(
	'purewikideletion' => 'Pure suppression wiki',
	'randomexcludeblank' => 'Page au hasard (exclure les pages blanches)',
	'populateblankedpagestable' => 'Remplir la table des pages blanchies',
	'purewikideletion-desc' => 'Entre autres choses, met les pages blanchies en lien rouge',
	'purewikideletion-pref-watchblank' => 'Ajouter les pages que je blanchis à ma liste de suivi',
	'purewikideletion-pref-watchunblank' => 'Ajouter les pages dé-blanchis à ma liste de suivi',
	'purewikideletion-blanked' => "Une ancienne version de cette page a été blanchie par [[User:$1|$1]] ([[User talk:$1|discuter]]) ([[Special:Contributions/$1|contributions]]) le $2

La raison du blanchissement était : ''<nowiki>$3</nowiki>''.

Vous pouvez [{{fullurl:{{FULLPAGENAMEE}}|action=history}} voir l'historique de cet article], [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} modifier la dernière version] ou taper une nouvelle page en remplissant le vide ci-dessous.",
	'blank-log' => 'blanchissements',
	'blank-log-name' => 'Journal des blanchissements',
	'blank-log-header' => 'Une liste des pages blanchies et dé-blanchies est affichée ci-dessous.',
	'blank-log-entry-blank' => 'a blanchi $1',
	'blank-log-entry-unblank' => 'a dé-blanchi $1',
	'blank-log-link' => '[[Special:Log/blank|journal des blanchissements]]',
	'purewikideletion-blanknologin' => 'Non connecté',
	'purewikideletion-blanknologintext' => 'Vous devez être enregistré et [[Special:UserLogin|connecté]] pour blanchir des pages.',
	'purewikideletion-unblanknologintext' => 'Vous devez être enregistré et [[Special:UserLogin|connecté]] pour dé-blanchir des pages.',
	'purewikideletion-blankedtext' => '[[$1]] a été blanchi.
Voyez le $2 pour un journal des blanchissements récents.',
	'purewikideletion-population-done' => 'Remplissage de la table blanked_page effectué.',
	'right-purewikideletion' => '[[Special:PopulateBlankedPagesTable|Remplir]] la table des pages blanchies',
);

/** Galician (Galego)
 * @author Gallaecio
 * @author Toliño
 */
$messages['gl'] = array(
	'randomexcludeblank' => 'Páxina ao chou (excluíndo as baleiras)',
	'populateblankedpagestable' => 'Encher a táboa de páxinas baleiradas',
	'purewikideletion-desc' => 'Entre outras cousas, provoca que as páxinas baleiras se convertan en ligazóns vermellas',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'purewikideletion' => 'Dospołna wikizhašenje',
	'randomexcludeblank' => 'Připadna strona (prózdne wuzamknyć)',
	'populateblankedpagestable' => 'Tabelu wuprózdnjenych stronow pjelnić',
	'purewikideletion-desc' => 'Wuskutkuje mj. dr., zo prózdne strony jewja so jako čerwjene wotkazy',
	'purewikideletion-pref-watchblank' => 'Strony, kotrež prózdnju, mojim wobkedźbowankam přidać',
	'purewikideletion-pref-watchunblank' => 'Strony, kotrež pjelnju, mojim wobkedźbowankam přidać',
	'purewikideletion-blanked' => "Prjedawša wersija tuteje strony bu wot [[User:$1|$1]] ([[User talk:$1|diskusija]]) ([[Special:Contributions/$1|přinoški]])  $2 wuprózdnjena.

Přičina za wuprózdnjenje: ''<nowiki>$3</nowiki>''.

Móžeš sej  [{{fullurl:{{FULLPAGENAMEE}}|action=history}} wersijowe stawizny nastawka wobhladać],  [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} poslednju wersiju wobdźěłać] abo nowu stronu zapodać.",
	'blank-log' => 'wuprózdnić',
	'blank-log-name' => 'Protokol wuprózdnjenjow',
	'blank-log-header' => 'Deleka je lisćina wuprózdnjenjow a napjelnjenjow stronow.',
	'blank-log-entry-blank' => 'je $1 wuprózdnił',
	'blank-log-entry-unblank' => 'je $1 napjelnił',
	'blank-log-link' => '[[Special:Log/blank|protokol wuprózdnjenjow]]',
	'purewikideletion-blanknologin' => 'Njepřizjewjeny',
	'purewikideletion-blanknologintext' => 'Dyrbiš zregistrowany wužiwar a  [[Special:UserLogin|přizjewjeny]] być, zo by stronu wuprózdnił.',
	'purewikideletion-unblanknologintext' => 'Dyrbiš zregistrowany wužiwar a  [[Special:UserLogin|přizjewjeny]] być, zo by stronu napjelnił.',
	'purewikideletion-blankedtext' => 'Strona [[$1]] je so wuprózdniła.
Hlej $2 za datowu sadźbu najnowšich wuprózdnjenjow.',
	'purewikideletion-population-done' => 'Pjelnjenje tabele wuprózdnjenych stronow skónčene.',
	'right-purewikideletion' => 'Tabelu wuprózdnjenych stronow [[Special:PopulateBlankedPagesTable|napjelnić]]',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'purewikideletion-pref-watchblank' => 'Säiten déi ech eidelmaachen op meng Iwwerwaachungslëscht derbäisetzen',
	'purewikideletion-pref-watchunblank' => 'Säiten déi ech eidelmaachen op meng Iwwerwaachungslëscht derbäisetzen',
	'blank-log' => 'eidel maachen',
	'blank-log-entry-blank' => 'huet $1 eidegemaacht',
	'purewikideletion-blanknologin' => 'Net ageloggt',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'purewikideletion' => 'Чисто вики-бришење',
	'randomexcludeblank' => 'Случајна страница (без празни)',
	'populateblankedpagestable' => 'Исполни ја табелата со испразнети страници',
	'purewikideletion-desc' => 'Меѓу останатите функции, ги брише испразнетите страници',
	'purewikideletion-pref-watchblank' => 'Додавај ги страниците што ги празнам во мојата листа на набљудувања',
	'purewikideletion-pref-watchunblank' => 'Додавај ги испразнетите страници што ги враќам во мојата листа на набљудувања',
	'purewikideletion-blanked' => "Претходна верзија на оваа страница беше испразнета од [[User:$1|$1]] ([[User talk:$1|разговор]]) ([[Special:Contributions/$1|придонеси]]) на $2

За испразнувањето беше наведена следнава причина: ''<nowiki>$3</nowiki>''.

Можете да ја [{{fullurl:{{FULLPAGENAMEE}}|action=history}} погледате историјата на статијата], да ја [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} уредите најновата верзија], или пак да напишете нова содржина за страницата во просторот подолу.",
	'blank-log' => 'испразнета',
	'blank-log-name' => 'Дневник на празнења',
	'blank-log-header' => 'Подолу е наведен список на празнења и враќања на испразнети страници.',
	'blank-log-entry-blank' => 'испразнета $1',
	'blank-log-entry-unblank' => 'вратено празнење на $1',
	'blank-log-link' => '[[Special:Log/blank|дневник на празнења]]',
	'purewikideletion-blanknologin' => 'Не сте најавени',
	'purewikideletion-blanknologintext' => 'Мора да сте регистриран корисник и да сте [[Special:UserLogin|најавени]] за да можете да празните страници.',
	'purewikideletion-unblanknologintext' => 'Мора да сте регистриран корисник и да сте [[Special:UserLogin|најавени]] за да можете да враќате испразнети страници.',
	'purewikideletion-blankedtext' => '[[$1]] е испразнета.
Видете $2 за евиденција на скорешни празнења.',
	'purewikideletion-population-done' => 'Завршив со исполнувањето на табелата blanked_page.',
	'right-purewikideletion' => '[[Special:PopulateBlankedPagesTable|Исополни]] ја тебалата со испразнети страници',
);

/** Portuguese (Português)
 * @author Hamilton Abreu
 */
$messages['pt'] = array(
	'purewikideletion' => 'Eliminação pura',
	'randomexcludeblank' => 'Página aleatória (excluir vazias)',
	'populateblankedpagestable' => 'Preencher tabela de páginas esvaziadas',
	'purewikideletion-desc' => 'Entre outras coisas, causa que links para páginas esvaziadas apareçam a vermelho',
	'purewikideletion-pref-watchblank' => 'Adicionar as páginas que eu esvaziar às minhas páginas vigiadas',
	'purewikideletion-pref-watchunblank' => 'Adicionar as páginas cujo esvaziamento eu reverter, às minhas páginas vigiadas',
	'purewikideletion-blanked' => "Uma versão anterior desta página foi esvaziada por [[User:$1|$1]] ([[User talk:$1|discussão]]) ([[Special:Contributions/$1|contribs]]) em $2

O motivo apresentado para o esvaziamento da página, foi: ''<nowiki>$3</nowiki>''.

Pode [{{fullurl:{{FULLPAGENAMEE}}|action=history}} ver o historial da página], [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} editar a última versão], ou escrever a página nova no espaço em branco abaixo.",
	'blank-log' => 'esvaziada',
	'blank-log-name' => 'Registo de esvaziamento de páginas',
	'blank-log-header' => 'Encontra abaixo uma lista de esvaziamentos e reversões de esvaziamentos de páginas.',
	'blank-log-entry-blank' => 'esvaziou $1',
	'blank-log-entry-unblank' => 'reverteu esvaziamento de $1',
	'blank-log-link' => '[[Special:Log/blank|registo de esvaziamento de páginas]]',
	'purewikideletion-blanknologin' => 'Não está autenticado',
	'purewikideletion-blanknologintext' => 'Tem de ser um utilizador registado e estar [[Special:UserLogin|autenticado]] para esvaziar uma página.',
	'purewikideletion-unblanknologintext' => 'Tem de ser um utilizador registado e estar [[Special:UserLogin|autenticado]] para reverter o esvaziamento de uma página.',
	'purewikideletion-blankedtext' => '[[$1]] foi esvaziada.
Consulte $2 para ver um registo dos esvaziamentos recentes de páginas.',
	'purewikideletion-population-done' => 'O preenchimento da tabela de páginas esvaziadas foi terminado.',
	'right-purewikideletion' => '[[Special:PopulateBlankedPagesTable|Preencher]] a tabela de páginas esvaziadas',
);

/** Russian (Русский)
 * @author Grigol
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'purewikideletion' => '«Чистое» вики-удаление',
	'randomexcludeblank' => 'Случайная страница (кроме очищенных)',
	'populateblankedpagestable' => 'Заполнить таблицу очищенных страниц',
	'purewikideletion-desc' => 'Среди прочего, делает красными ссылки на пустые страницы',
	'purewikideletion-pref-watchblank' => 'Добавлять очищенные мной страницы в список наблюдения',
	'purewikideletion-pref-watchunblank' => 'Добавлять возвращённые после очистки мной страницы в список наблюдения',
	'purewikideletion-blanked' => "Предыдущая версия этой страницы была очищена участником [[User:$1|$1]] ([[User talk:$1|обсуждение]]) ([[Special:Contributions/$1|вклад]]) $2

Указанная причина очистки: ''<nowiki>$3</nowiki>''.

Вы можете [{{fullurl:{{FULLPAGENAMEE}}|action=history}} просмотреть историю страницы], [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} исправить последнюю версию] или ввести текст новой страницы в расположенное ниже пустое поле.",
	'blank-log' => 'очистка',
	'blank-log-name' => 'Журнал очисток',
	'blank-log-header' => 'Ниже приведён список очищенных страниц и страниц, возвращённых после очистки.',
	'blank-log-entry-blank' => 'очищена $1',
	'blank-log-entry-unblank' => 'возвращена после очистки $1',
	'blank-log-link' => '[[Special:Log/blank|журнал очисток]]',
	'purewikideletion-blanknologin' => 'Вы не представились системе',
	'purewikideletion-blanknologintext' => 'Вы должны [[Special:UserLogin|представиться системе]], чтобы иметь возможность очищать страницы.',
	'purewikideletion-unblanknologintext' => 'Вы должны [[Special:UserLogin|представиться системе]], чтобы иметь возможность возвращать страницы после очистки.',
	'purewikideletion-blankedtext' => 'Страница [[$1]] была очищена.
Список недавних очисток см. на $2.',
	'purewikideletion-population-done' => 'Закончено заполнение таблицы blanked_page.',
	'right-purewikideletion' => '[[Special:PopulateBlankedPagesTable|заполнение]] таблицы очищенных страниц',
);

