<?php
/**
 * Internationalisation file for the MassBlank extension
 *
 * @file
 * @ingroup Extensions
 * @author Tisane
 */

$messages = array();

/** English
 * @author Tisane
 */
$messages['en'] = array(
	'massblank'               => 'Mass blank',
	'massblank-desc'          => 'Gives administrators the ability to [[Special:MassBlank|mass blank]] pages',
	'massblank-nopages'       => 'No new pages by [[Special:Contributions/$1|$1]] in recent changes.',
	'massblank-list'          => 'The following pages were recently created by [[Special:Contributions/$1|$1]];
put in a comment and hit the button to blank them.',
	'massblank-defaultreason' => 'Mass blanking of pages added by $1',
	'massblank-tools'         => 'This tool allows for mass blanking of pages recently added by a given user or an IP address.
Input the username or IP address to get a list of pages to blank.',
	'massblank-submit-user'   => 'Go',
	'massblank-submit-blank'  => 'Blank selected',
	'massblank-blankcomment'  => 'Reason:',
	'right-massblank'         => 'Mass blank pages',
);

/** Message documentation (Message documentation)
 * @author EugeneZelenko
 */
$messages['qqq'] = array(
	'massblank-desc' => 'Short description of the MassBlank extension, shown in [[Special:Version]]. Do not translate or change links.',
	'massblank-submit-user' => '{{Identical|Go}}',
	'massblank-blankcomment' => '{{Identical|Reason}}',
	'right-massblank' => '{{doc-right}}',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'massblank-blankcomment' => 'Rede:',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 * @author Wizardist
 */
$messages['be-tarask'] = array(
	'massblank' => 'Масавае ачышчэньне',
	'massblank-desc' => 'Дае адміністратарам магчымасьць [[Special:MassBlank|масавага ачышчэньня]] старонак',
	'massblank-nopages' => 'У апошніх зьменах няма новых старонак, створаных [[Special:Contributions/$1|$1]].',
	'massblank-list' => 'Наступныя старонкі былі нядаўна створаныя [[Special:Contributions/$1|$1]];
дадайце камэнтар і націсьніце кнопку для іх ачысткі.',
	'massblank-defaultreason' => 'Масавае ачышчэньне старонак, дададзеных $1',
	'massblank-tools' => 'Гэты інструмэнт дазваляе рабіць масавыя ачышчэньні старонак, дададзеных пэўным удзельнікам альбо з IP-адрасу. Увядзіце імя ўдзельніка альбо IP-адрас для таго, каб атрымаць сьпіс старонак для ачысткі.',
	'massblank-submit-user' => 'Выканаць',
	'massblank-submit-blank' => 'Ачысьціць выбраныя',
	'massblank-blankcomment' => 'Прычына:',
	'right-massblank' => 'масавая ачыстка старонак',
);

/** Breton (Brezhoneg)
 * @author Y-M D
 */
$messages['br'] = array(
	'massblank' => 'Gwenner a-vras',
	'massblank-desc' => "Reiñ a ra an tu d'ar verourien da [[Special:MassBlank|stankañ pajennoù a-vras]]",
	'massblank-nopages' => "Pajenn nevez ebet bet krouet gant [[Special:Contributions/$1|$1]] er c'hemmoù diwezhañ.",
	'massblank-list' => "Nevez zo eo bet krouet ar pajennoù da-heul gant [[Special:Contributions/$1|$1]];
merkañ un tamm notenn ha klikañ war ar bouton d'o gwennañ.",
	'massblank-defaultreason' => 'Gwennañ a-vras ar pajennoù bet ouzhpennet gant $1',
	'massblank-tools' => "Talvezout a ra an ostilh-mañ da wennañ a-vras pajennoù bet ouzhpennet nevez zo gant un implijer enrollet pe gant ur chomlec'h IP. 
Merkañ ar c'homlec'h IP pe anv an implijer a-benn kaout roll ar pajennoù da wennañ :",
	'massblank-submit-user' => 'Mont',
	'massblank-submit-blank' => "N'eo bet diuzet tra ebet",
	'massblank-blankcomment' => 'Abeg ar stankañ :',
	'right-massblank' => 'Gwennañ pajennoù a-vras',
);

/** Chechen (Нохчийн) */
$messages['ce'] = array(
	'massblank-blankcomment' => 'Бахьан:',
);

/** German (Deutsch)
 * @author Kghbln
 */
$messages['de'] = array(
	'massblank' => 'Massenleerung',
	'massblank-desc' => 'Ermöglicht Administratoren die [[Special:MassBlank|Massenleerung]] von Seiten',
	'massblank-nopages' => 'Es gibt in den „Letzten Änderungen“ keine neuen Seiten von [[Special:Contributions/$1|$1]].',
	'massblank-list' => 'Die folgenden Seiten wurden von [[Special:Contributions/$1|$1]] erzeugt.
Gib einen Kommentar ein und drücke auf den Knopf zum Leeren der Seiten.',
	'massblank-defaultreason' => 'Massenleerung der Seiten, die von „$1“ angelegt wurden',
	'massblank-tools' => 'Diese Arbeitshilfe ermöglicht die Massenleerung von Seiten, die von einer IP-Adresse oder einem Benutzer angelegt wurden.
Gib die IP-Adresse oder den Benutzernamen ein, um eine Liste der zu leerenden Seiten zu erhalten.',
	'massblank-submit-user' => 'Hole die Liste',
	'massblank-submit-blank' => 'Ausgewählte Seiten leeren',
	'massblank-blankcomment' => 'Grund:',
	'right-massblank' => 'Massenleerung von Seiten',
);

/** German (formal address) (Deutsch (Sie-Form))
 * @author Kghbln
 */
$messages['de-formal'] = array(
	'massblank-list' => 'Die folgenden Seiten wurden von [[Special:Contributions/$1|$1]] erzeugt.
Geben Sie einen Kommentar ein und drücken Sie auf den Knopf zum Leeren der Seiten.',
	'massblank-tools' => 'Diese Arbeitshilfe ermöglicht die Massenleerung von Seiten, die von einer IP-Adresse oder einem Benutzer angelegt wurden.
Geben Sie die IP-Adresse oder den Benutzernamen ein, um eine Liste der zu leerenden Seiten zu erhalten.',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'massblank' => 'Masowe wuproznjenje',
	'massblank-desc' => 'Dajo administratoram móžnosć [[Special:MassBlank|masowego wuproznjenja]] bokow',
	'massblank-nopages' => 'Žedne nowe boki wót [[Special:Contributions/$1|$1]] w aktualnych změnach.',
	'massblank-list' => 'Slědujuce boki su se rowno wót [[Special:Contributions/$1|$1]] napórali;
zapódaj komentar a klikni na tłocašk, aby je wótpórał.',
	'massblank-defaultreason' => 'Masowe wuproznjenje bokow, kótarež $1 jo pśidał',
	'massblank-tools' => 'Toś ten rěd zmóžnja masowe wuproznjenja bokow, kótarež wěsty wužywaŕ abo IP jo rowno pśidał. Zapódaj wužywarske mě abo IP-adresu, aby dostał lisćinu bokow, kótarež maju se wuprozniś.',
	'massblank-submit-user' => 'W pórědku',
	'massblank-submit-blank' => 'Wubrane boki wuprozniś',
	'massblank-blankcomment' => 'Pśicyna:',
	'right-massblank' => 'Masowe wuproznjenje bokow',
);

/** Spanish (Español)
 * @author Crazymadlover
 * @author Translationista
 */
$messages['es'] = array(
	'massblank' => 'Vaciar en masa',
	'massblank-desc' => 'Proporciona a los administradores la capacidad de [[Special:MassBlank|vaciar en masa]] páginas',
	'massblank-nopages' => 'No hay páginas nuevas hechas por [[Special:Contributions/$1|$1]] en cambios recientes.',
	'massblank-list' => 'Las siguientes páginas han sido creadas recientemente por [[Special:Contributions/$1|$1]];
añade un comentario y haz clic sobre el botón para vaciarlas.',
	'massblank-defaultreason' => 'Vaciado en masa de páginas añadidas por $1',
	'massblank-tools' => 'Esta herramienta permite el vaciado de páginas añadidas recientemente por un usuario o dirección IP dados.
Introduce el nombre de usuario o la dirección de IP para obtener un listado de las páginas a vaciar.',
	'massblank-submit-user' => 'Ir',
	'massblank-submit-blank' => 'Vaciar la selección',
	'massblank-blankcomment' => 'Razón para dejar en blanco:',
	'right-massblank' => 'Vaciar páginas en masa',
);

/** French (Français)
 * @author IAlex
 * @author Litlok
 * @author McDutchie
 */
$messages['fr'] = array(
	'massblank' => 'Blanchissement en masse',
	'massblank-desc' => 'Donne aux administrateurs la possibilité de [[Special:MassBlank|blanchir en masse]] des pages',
	'massblank-nopages' => 'Aucune nouvelle page par [[Special:Contributions/$1|$1]] dans les modifications récentes.',
	'massblank-list' => 'Les pages suivantes ont été créées récemment par [[Special:Contributions/$1|$1]] ;
entrez un commentaire puis cliquez sur le bouton pour les blanchir.',
	'massblank-defaultreason' => 'Blanchissement en masse des pages ajoutées par $1',
	'massblank-tools' => "Cet outil permet de blanchir en masse des pages ajoutées récemment par un utilisateur ou adresse IP donné.
Entrez le nom d'utilisateur ou l'adresse IP pour obtenir une liste de pages à blanchir.",
	'massblank-submit-user' => 'Aller',
	'massblank-submit-blank' => 'Blanchir les pages sélectionnées',
	'massblank-blankcomment' => 'Raison du blanchissage :',
	'right-massblank' => 'Blanchir en masse des pages',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'massblank' => 'Baleirar en masa',
	'massblank-desc' => 'Dá aos administradores a posibilidade de [[Special:MassBlank|baleirar páxinas]] masivamente',
	'massblank-nopages' => 'Non hai novas páxinas feitas por [[Special:Contributions/$1|$1]] nos cambios recentes.',
	'massblank-list' => '[[Special:Contributions/$1|$1]] creou nos últimos intres as seguintes páxinas;
escriba un comentario e prema o botón para baleiralas.',
	'massblank-defaultreason' => 'Baleirar en masa as páxinas engadidas por $1',
	'massblank-tools' => 'Esta ferramenta permite baleirar en masa as páxinas engadidas recentemente por un determinado usuario ou enderezo IP.
Introduza o nome do usuario ou enderezo IP para obter unha lista das páxinas para baleirar.',
	'massblank-submit-user' => 'Ir',
	'massblank-submit-blank' => 'Baleirar a selección',
	'massblank-blankcomment' => 'Motivo:',
	'right-massblank' => 'Baleirar páxinas en masa',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'massblank' => 'Masowa wuprózdnjenje',
	'massblank-desc' => 'Zmóžnja administratoram [[Special:MassBlank|masowe wuprózdnjenje]] stronow',
	'massblank-nopages' => 'W aktualnych změnach žane nowe strony wot [[Special:Contributions/$1|$1]] njejsu.',
	'massblank-list' => 'Slědowace strony buchu runje přez [[Special:Contributions/$1|$1]] wutworjene; zapodaj komentar a klikń na tłóčatko, zo by je wuprózdnił.',
	'massblank-defaultreason' => 'Masowe wuprózdnjenje  stronow, kotrež buchu wot $1 přidate',
	'massblank-tools' => 'Tutón nastroj dowoluje masowe wuprózdnjenje stronow, kotrež buchu runje wot wěsteho wužiwarja abo wěsteje IP-adresy přidate.
Zapodaj wužiwarske mjeno abo IP-adresu, zo by lisćinu stronow dóstak, kotrež maja so wuprózdnić.',
	'massblank-submit-user' => 'Start',
	'massblank-submit-blank' => 'Wubrane strony wuprózdnić',
	'massblank-blankcomment' => 'Přičina za wuprózdnjenje:',
	'right-massblank' => 'Masowe wuprózdnjenje stronow',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'massblank' => 'Vacuation in massa',
	'massblank-desc' => 'Da le possibilitate al administratores de [[Special:MassBlank|vacuar paginas in massa]]',
	'massblank-nopages' => 'Nulle nove paginas per [[Special:Contributions/$1|$1]] trovate in le modificationes recente.',
	'massblank-list' => 'Le sequente paginas esseva recentemente create per [[Special:Contributions/$1|$1]];
entra un commento e clicca le button pro vacuar los.',
	'massblank-defaultreason' => 'Vacuation in massa de paginas addite per $1',
	'massblank-tools' => 'Iste instrumento permitte le vacuation in massa de paginas recentemente addite per un usator o IP specific.
Entra le nomine de usator o adresse IP pro obtener un lista de paginas a vacuar.',
	'massblank-submit-user' => 'Ir',
	'massblank-submit-blank' => 'Vacuar le paginas seligite',
	'massblank-blankcomment' => 'Motivo:',
	'right-massblank' => 'Vacuar paginas in massa',
);

/** Japanese (日本語)
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'massblank' => '一括白紙化',
	'massblank-desc' => '管理者が複数のページを[[Special:MassBlank|一括して白紙化]]できるようにする',
	'massblank-nopages' => '最近の更新に[[Special:Contributions/$1|$1]]による新規ページはありません。',
	'massblank-list' => '以下のページが[[Special:Contributions/$1|$1]]によって最近作成されました。コメントを入力し、ボタンを押して白紙化を行います。',
	'massblank-defaultreason' => '$1が追加したページの一括白紙化',
	'massblank-tools' => 'このツールを使うと、指定された利用者や IP アドレスにより最近追加されたページの、一括白紙化を行うことができるようになります。白紙化するページの一覧を得るには利用者名か IP アドレスを入力してください。',
	'massblank-submit-user' => '実行',
	'massblank-submit-blank' => '選択したページを白紙化',
	'massblank-blankcomment' => '理由:',
	'right-massblank' => 'ページの一括白紙化',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'massblank' => 'Vill Säiten eidelmaachen',
	'massblank-desc' => "Gëtt Administrateuren d'Méiglechkeet fir [[Special:MassBlank|vill Säite mateneen eidelzemaachen]]",
	'massblank-nopages' => 'Keng nei Säite vum [[Special:Contributions/$1|$1]] an de rezenten Ännerungen.',
	'massblank-list' => 'Dës Säite goufe viru kuerzem vum [[Special:Contributions/$1|$1]] nei ugeluecht; gitt w.e.g. eng Bemierkung an, an dréckt op de Knäppche vir se eidelzemaachen.',
	'massblank-defaultreason' => 'Massen-Eidelmaache vu Säiten déi vum $1 ugefaang goufen',
	'massblank-tools' => "Dësen Tool erlaabt vill Säite mateneen eidelzemaachen déi vun engem Benotzer oder vun enger IP-Adresse ugeluecht goufen.
Gitt w.e.g. d'IP-Adress respektiv de Benotzernumm u fir eng Lescht vu Säiten ze kréien fir eidelzemaachen:",
	'massblank-submit-user' => 'Lass',
	'massblank-submit-blank' => 'Erausgesichte Säiten eidelmaachen',
	'massblank-blankcomment' => 'Grond:',
	'right-massblank' => 'Vill Säite mateneen eidelmaachen',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'massblank' => 'Масовно празнење',
	'massblank-desc' => 'Им овозможува на администраторите да вршат [[Special:MassBlank|масовно празнење]] на страници',
	'massblank-nopages' => 'Нема нови страници од [[Special:Contributions/$1|$1]] во скорешните промени.',
	'massblank-list' => 'Следниве страници се неодамна создадени од [[Special:Contributions/$1|$1]];
за да ги избришете, впишете коментар и притиснете го копчето.',
	'massblank-defaultreason' => 'Масовно празнење на страници од $1',
	'massblank-tools' => 'Оваа алатка овозможува масовно празнење на страници неодамна создадени од даден корисник или IP-адреса.
Внесете корисничко име или IP-адреса за да добиете список на страници за празнење.',
	'massblank-submit-user' => 'Оди',
	'massblank-submit-blank' => 'Испразни одбрани',
	'massblank-blankcomment' => 'Причина за празнење:',
	'right-massblank' => 'Масовно празнење на страници',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'massblank' => 'Massaal leegmaken',
	'massblank-desc' => "Geeft beheerders de mogelijkheid om pagina's [[Special:MassBlank|massaal leeg te maken]]",
	'massblank-nopages' => "Er staan geen nieuwe pagina's door [[Special:Contributions/$1|$1]] in de recente wijzigingen.",
	'massblank-list' => "De volgende pagina's zijn recentelijk aangemaakt door [[Special:Contributions/$1|$1]].
Voer een reden in en klik op de knop om ze leeg te maken.",
	'massblank-defaultreason' => "Pagina's toegevoegd door $1 massaal leeggemaakt",
	'massblank-tools' => "Dit hulpmiddel maakt het mogelijk pagina's die recentelijk zijn aangemaakt door een gebruiker of IP-adres massaal leeg te maken.
Voer de gebruikersnaam of het IP-adres in voor een lijst van leeg te maken pagina's.",
	'massblank-submit-user' => 'OK',
	'massblank-submit-blank' => "Geselecteerde pagina's leegmaken",
	'massblank-blankcomment' => 'Reden:',
	'right-massblank' => "Pagina's massaal leegmaken",
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Nghtwlkr
 */
$messages['no'] = array(
	'massblank-nopages' => 'Ingen nye sider av [[Special:Contributions/$1|$1]] i siste endringer.',
	'massblank-blankcomment' => 'Årsak:',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'massblank-submit-user' => 'ورځه',
	'massblank-blankcomment' => 'سبب:',
);

/** Portuguese (Português)
 * @author Hamilton Abreu
 */
$messages['pt'] = array(
	'massblank' => 'Esvaziar múltiplas páginas',
	'massblank-desc' => 'Dá a administradores a capacidade de [[Special:MassBlank|esvaziar múltiplas páginas]] (remover o seu conteúdo)',
	'massblank-nopages' => 'Não existem páginas novas criadas por [[Special:Contributions/$1|$1]] nas mudanças recentes.',
	'massblank-list' => 'As páginas seguintes foram criadas recentemente por [[Special:Contributions/$1|$1]];
insira um comentário e carregue no botão para esvaziá-las.',
	'massblank-defaultreason' => 'Esvaziamento das páginas adicionadas por $1',
	'massblank-tools' => 'Esta ferramenta permite remover o conteúdo de múltiplas páginas adicionadas recentemente por um utilizador ou endereço IP.
Insira o nome do utilizador ou o endereço IP para obter a lista das páginas cujo conteúdo será removido.',
	'massblank-submit-user' => 'Prosseguir',
	'massblank-submit-blank' => 'Esvaziar seleccionadas',
	'massblank-blankcomment' => 'Motivo:',
	'right-massblank' => 'Esvaziar (remover o conteúdo de) múltiplas páginas',
);

/** Russian (Русский)
 * @author Grigol
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'massblank' => 'Массовая очистка',
	'massblank-desc' => 'Даёт возможность администраторам [[Special:MassBlank|массово очищать]] страницы',
	'massblank-nopages' => 'В свежих правках нет страниц, созданных [[Special:Contributions/$1|$1]]',
	'massblank-list' => 'Следующие страницы были недавно созданы [[Special:Contributions/$1|$1]].
Оставьте примечание и нажмите кнопку, чтобы очистить их.',
	'massblank-defaultreason' => 'Массовая очистка страниц, добавленных $1',
	'massblank-tools' => 'Эта страница позволяет массово очищать страницы, добавленные определённым участником или IP.
Введите имя участника или IP, чтобы получить список страниц для очистки.',
	'massblank-submit-user' => 'Выполнить',
	'massblank-submit-blank' => 'Очистка выбрана',
	'massblank-blankcomment' => 'Причина очистки:',
	'right-massblank' => 'массовая очистка страниц',
);

/** Serbian Cyrillic ekavian (Српски (ћирилица))
 * @author Михајло Анђелковић
 */
$messages['sr-ec'] = array(
	'massblank-submit-user' => 'Иди',
	'massblank-blankcomment' => 'Разлог:',
);

/** Serbian Latin ekavian (Srpski (latinica)) */
$messages['sr-el'] = array(
	'massblank-submit-user' => 'Idi',
	'massblank-blankcomment' => 'Razlog:',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'massblank' => 'Buntunang pagpapatlang',
	'massblank-desc' => 'Nagbibigay sa mga tagapangasiwa ng kakayanang [[Special:MassBlank|makapagbuntunang pagpapatlang]] ng mga pahina',
	'massblank-nopages' => 'Walang bagong mga pahinang ginawa ni [[Special:Contributions/$1|$1]] na nasa loob ng kamakailang mga pagbabago.',
	'massblank-list' => 'Ang sumusunod na mga pahina ay nilikha kamakailan lamang ni [[Special:Contributions/$1|$1]];
maglagay ng isang puna at pindutin ang pindutan upang mabura ang mga ito.',
	'massblank-defaultreason' => 'Idinagdag ni $1 ang malawakang pagbubura ng mga pahina',
	'massblank-tools' => 'Nagpapahintulot ang kagamitang ito upang malawakang makapagbura ng mga pahinang idinagdag kamakailan ng isang ibinigay na tagagamit o adres ng IP.
Ipasok ang pangalan ng tagagamit o adres ng IP upang makakuha ng isang talaan ng mga pahinang buburahin.',
	'massblank-submit-user' => 'Tuparin',
	'massblank-submit-blank' => 'Napili na ang pagpapatlang',
	'massblank-blankcomment' => 'Dahilan ng pagpapatlang:',
	'right-massblank' => 'Malawakang tanggalan ng laman ang mga pahina',
);

