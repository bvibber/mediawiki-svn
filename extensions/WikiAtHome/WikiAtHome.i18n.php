<?php
/**
 * Internationalisation file for extension Wiki At Home.
 *
 * @addtogroup Extensions
*/

$messages = array();

/** English
 * @author Michael Dale
 * @author Purodha 	http://ksh.wikipedia.org/wiki/User:Purodha
 */
$messages['en'] = array(
	'specialwikiathome'	=> 'Wiki@Home',
	'wah-desc'			=> 'Enables distributing transcoding video jobs to clients using firefogg.',
	'wah-user-desc'		=> 'Wiki@Home enables community members to donate spare cpu cycles to help with resource intensive operations',
	'wah-short-audio'	=> '$1 sound file, $2',
	'wah-short-video'	=> '$1 video file, $2',
	'wah-short-general'	=> '$1 media file, $2',

	'wah-long-audio'       	=> '($1 sound file, length $2, $3)',
	'wah-long-video'       	=> '($1 video file, length $2, $4×$5 pixels, $3)',
	'wah-long-multiplexed' 	=> '(multiplexed audio/video file, $1, length $2, $4×$5 pixels, $3 overall)',
	'wah-long-general'     	=> '(media file, length $2, $3)',
	'wah-long-error'       	=> '(ffmpeg could not read this file: $1)',

	'wah-transcode-working' => 'This video is being processed, please try again later',
	'wah-transcode-helpout' => 'The clip is $1 percent done. You can help transcode this video by visiting [[Special:WikiAtHome|Wiki@Home]]',

	'wah-transcode-fail'	=> 'This file failed to transcode.',

	'wah-javascript-off'	=> 'You must have JavaScript enabled to participate in Wiki@Home',
	'wah-loading'		=> 'loading Wiki@Home interface <blink>...</blink>'
);

/** Message documentation (Message documentation)
 * @author Fryed-peach
 */
$messages['qqq'] = array(
	'wah-desc' => '{{desc}}',
	'wah-short-audio' => '* $1 is codec name(s)
* $2 is file length (time)',
	'wah-short-video' => '* $1 is codec name(s)
* $2 is file length (time)',
	'wah-short-general' => '* $1 is codec name(s)
* $2 is file length (time)',
	'wah-long-audio' => '* $1 is codec name(s)
* $2 is file length (time)
* $3 is bitrate',
	'wah-long-video' => '* $1 is codec name(s)
* $2 is file length (time)
* $3 is bitrate
* $4 is width
* $5 is height',
	'wah-long-multiplexed' => '* $1 is codec name(s)
* $2 is file length (time)
* $3 is bitrate
* $4 is width
* $5 is height',
	'wah-long-general' => '* $2 is file length (time)
* $3 is bitrate',
	'wah-long-error' => '* $1 is error message',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'wah-desc' => 'Дазваляе разьмяркаванньне працы перакадыроўкі відэа да кліентаў праз выкарыстаньне firefogg.',
	'wah-user-desc' => 'Wiki@Home дазваляе ўдзельнікам супольнасьці ахвяраваць не выкарыстоўваемую магутнасьць працэсараў на дапамогу з рэсурсаёмістымі апэрацыямі',
	'wah-short-audio' => 'Аўдыё-файл у фармаце $1, $2',
	'wah-short-video' => 'Відэа-файл у фармаце $1, $2',
	'wah-short-general' => 'Мэдыя-файл у фармаце $1, $2',
	'wah-long-audio' => '(Аўдыё-файл у фармаце $1, працягласьць $2, $3)',
	'wah-long-video' => '(Відэа-файл у фармаце $1, працягласьць $2, $4×$5 піксэляў, $3)',
	'wah-long-multiplexed' => '(Мультыплексны аўдыё/відэа-файл у фармаце $1, працягласьць $2, $4×$5 піксэляў, усяго $3)',
	'wah-long-general' => '(Мэдыя-файл, працягласьць $2, $3)',
	'wah-long-error' => '(ffmpeg ня можа прачытаць гэты файл: $1)',
	'wah-transcode-working' => 'Гэты відэа-файл зараз перакадыруецца, выканана $1%',
	'wah-transcode-helpout' => 'Вы можаце дапамагчы перакадыраваць гэты відэа-файл наведаўшы [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Немагчыма перакадаваць гэты файл.',
	'wah-javascript-off' => 'У Вас павінен быць уключаны JavaScript для ўдзелу ў Wiki@Home',
	'wah-loading' => 'загрузка інтэрфэйсу Wiki@Home <blink>...</blink>',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'wah-short-audio' => '$1 zvučna datoteka, $2',
);

/** Basque (Euskara)
 * @author Kobazulo
 */
$messages['eu'] = array(
	'wah-short-audio' => '$1 soinu fitxategia, $2',
	'wah-short-video' => '$1 bideo fitxategia, $2',
	'wah-short-general' => '$1 media fitxategia, $2',
);

/** French (Français)
 * @author IAlex
 */
$messages['fr'] = array(
	'wah-desc' => 'Permet de distribuer le travail de transcodage de vidéo aux clients en utilisant firefogg.',
	'wah-user-desc' => 'Wiki@Home permet aux membre de la communauté de donner des cycles processeur libres pour aider des opérations intensives en ressources.',
	'wah-short-audio' => 'fichier de son $1, $2',
	'wah-short-video' => 'fichier vidéo $1, $2',
	'wah-short-general' => 'fichier média $1, $2',
	'wah-long-audio' => '(fichier son $1, durée $2, $3)',
	'wah-long-video' => '(fichier son $1, durée $2, $4×$5 pixels, $3)',
	'wah-long-multiplexed' => '(fichier audio / vidéo multiplexé $1, durée $2, $4×$5 pixels, $3 total)',
	'wah-long-general' => '(fichier média, durée $2, $3)',
	'wah-long-error' => "(ffmpeg n'a pas pu lire ce fichier : $1)",
	'wah-transcode-working' => "Cette vidéo est en train d'être transcodée et $1 % ont été effectués",
	'wah-transcode-helpout' => 'Vous pouvez aider à transcoder cette vidéo en visitant [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => "Ce fichier n'a pas pu être transcodé.",
	'wah-javascript-off' => 'Vous devez activer JavaScript pour participer à Wiki@Home',
	'wah-loading' => "chargement de l'interface Wiki@Home <blink>...</blink>",
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'wah-desc' => 'Activa a distribución de postos de traballo de transcodificación de vídeo para os clientes que usen firefogg.',
	'wah-user-desc' => 'O Wiki@Home permite que os membros da comunidade doen ciclos CPU de recambio para axudar con operacións intensivas de recursos',
	'wah-short-audio' => 'Ficheiro de son $1, $2',
	'wah-short-video' => 'Ficheiro de vídeo $1, $2',
	'wah-short-general' => 'Ficheiro multimedia $1, $2',
	'wah-long-audio' => '(Ficheiro de son $1, duración $2, $3)',
	'wah-long-video' => '(Ficheiro de vídeo $1, duración $2, $4×$5 píxeles, $3)',
	'wah-long-multiplexed' => '(ficheiro multiplex de audio/vídeo, $1, duración $2, $4×$5 píxeles, $3 total)',
	'wah-long-general' => '(ficheiro multimedia, duración $2, $3)',
	'wah-long-error' => '(ffmpeg non puido ler este ficheiro: $1)',
	'wah-transcode-working' => 'Este vídeo está sendo transcodificado; feito ao $1%',
	'wah-transcode-helpout' => 'Pode axudar na transcodificación deste vídeo visitando o [[Special:WikiAtHome|Wiki@Home]]',
	'wah-javascript-off' => 'Debe ter o Javascript activado para participar no Wiki@Home',
	'wah-loading' => 'cargando a interface do Wiki@Home <blink>...</blink>',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'wah-desc' => 'Zmóžnja rozdźělenje nadawkow překodowanja widejow klientam z pomocu firefogg.',
	'wah-user-desc' => 'Wiki@Home zmóžnja čłonam zhromadźenstwa, zo bychu nadbytkowe cyklusy CPU darili, zo bychu při operacoje pomhali, kotrež wjele resursow přetrjebuja',
	'wah-short-audio' => 'zwukodataja $1, $2',
	'wah-short-video' => 'widejodataja $1, $2',
	'wah-short-general' => 'medijowa dataja $1, $2',
	'wah-long-audio' => '(zwukodataja $1, dołhosć $2, $3)',
	'wah-long-video' => '(widejodataja $1, dołhosć $2, $4×$5 pikselow, $3)',
	'wah-long-multiplexed' => '(multipleksowana awdio-/widejodatja, $1, dołhosć $2, $4×$5 pikselow, $3 dohromady)',
	'wah-long-general' => '(medijowa dataja, dołhosć $2, $3)',
	'wah-long-error' => '(ffmpeg njeje móhł tutu dataju čitać: $1)',
	'wah-transcode-working' => 'Widejo so překoduje, $1 % je přewjedźene',
	'wah-transcode-helpout' => 'Móžeš pomhać tute widejo přez wopytowanje [[Special:WikiAtHome|Wiki@Home]] překodować',
	'wah-transcode-fail' => 'Njeje so poradźiło tutu dataju překodować.',
	'wah-javascript-off' => 'Dyrbiš JavaScript zmóžnić, zo by so na Wiki@Home wobdźělił',
	'wah-loading' => 'Začitanje powjercha Wik@Home <blink> ... </blink>',
);

/** Japanese (日本語)
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'wah-desc' => '動画のトランスコード・ジョブを Firefogg を使ってクライアントに分散できるようにする。',
	'wah-user-desc' => 'Wiki@Home は、コミュニティ参加者が余った CPU サイクルを提供することで、リソース集約的な処理を手伝えるようにします',
	'wah-short-audio' => '$1音声ファイル、$2',
	'wah-short-video' => '$1動画ファイル、$2',
	'wah-short-general' => '$1メディアファイル、$2',
	'wah-long-audio' => '($1音声ファイル、長さ：$2、$3)',
	'wah-long-video' => '($1動画ファイル、長さ：$2、$4×$5ピクセル、$3)',
	'wah-long-multiplexed' => '(多重化された音声/動画ファイル、$1、長さ：$2、$4×$5ピクセル、全体で$3)',
	'wah-long-general' => '(メディアファイル、長さ：$2、$3)',
	'wah-long-error' => '(ffmpeg はこのファイルを読み取れませんでした: $1)',
	'wah-transcode-working' => 'この動画のトランスコードは$1%完了しています',
	'wah-transcode-helpout' => '[[Special:WikiAtHome|Wiki@Home]] を使用すると、この動画のトランスコードをあなたが手伝うことができます',
	'wah-transcode-fail' => 'このファイルはトランスコードに失敗しました。',
	'wah-javascript-off' => 'Wiki@Home に参加するには JavaScript を有効にする必要があります',
	'wah-loading' => 'Wiki@Home のインタフェースを読み込み中<blink>…</blink>',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'wah-desc' => 'Määt et müjjelesch, et Viddejos ömzekodeere met <code lang="en">firefogg</code> als en Aufjab aan Metmaacher ze verdeile.',
	'wah-user-desc' => 'Wiki@Home määt et müjjelesch för Metmaacher, Leistung vum eijene Kompjuter affzejävve — en Momänte, woh dä söns jraad nix ze donn hät — öm bei opwändeje Rääschnereije vum Wiki ze hellfe.',
	'wah-short-audio' => '$1 Tondattei, $2',
	'wah-short-video' => '$1 Viddejodattei, $2',
	'wah-short-general' => '$1 Meedijedattei, $2',
	'wah-long-audio' => '($1 Tondattei, Ömfang $2, $3)',
	'wah-long-video' => '($1 Viddejodattei, Ömfang $2, $4×$5 Pixele, $3)',
	'wah-long-multiplexed' => '(Multipläx- Ton- un Viddejodattei, $1, Ömfang $2, $4×$5 Pixele, $3 zosamme)',
	'wah-long-general' => '(Meedijedattei, Ömfang $2, $3)',
	'wah-long-error' => '(<code lang="en">ffmpeg</code> kunnt di Dattei nit lässe: $1)',
	'wah-transcode-working' => 'Dat Viddejo weed ömkodeet, un es zoh $1% jedonn',
	'wah-transcode-helpout' => 'Do kanns beim Ömkodeere hellfe för heh dä Viddejo, jangk doför noh de Sigg [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Di Dattei lehß sesch ömkodeere.',
	'wah-javascript-off' => 'Dinge Brauser moß JavaSkrep künne un ennjeschalldt han, domet De bei Wiki@Home metmaache kanns.',
	'wah-loading' => 'Ben wiki@home sing Schnetshtëll aam laade<blink>{{int:ellipsis}}</blink>',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'wah-desc' => "Erlaabten et fir d'Ëmschreiwe vu Video-Aarbechten op Client ze verdeelen déi Firefogg benotzen.",
	'wah-user-desc' => 'Wiki@Doheem erlaabt et Membere vun der Gemeinschaft fir spuersam CPU-Perioden ze spenden fir bäi resourcenintensiven Operatiounen ze hëllefen',
	'wah-short-audio' => '$1 Toun-Fichier, $2',
	'wah-short-video' => '$1 Video-Fichier, $2',
	'wah-short-general' => '$1 Medie-Fichier, $2',
	'wah-long-audio' => '($1 Tounfichier, Längt $2, $3)',
	'wah-long-general' => '(Mediefichier, Längt $2, $3)',
	'wah-long-error' => '(ffmpeg konnt de Fichier $1 net liesen)',
	'wah-transcode-helpout' => 'Dir kënnt hëllefen dëse Video ze transcdéieren wann Dir [[Special:WikiAtHome|Wiki@Home]] besicht',
	'wah-transcode-fail' => 'Dëse Fichier konnt net ëmgeschriwwe ginn.',
	'wah-javascript-off' => 'Dir musst JavaScript zouloossen fir bäi Wiki@Doheem matzemaachen',
	'wah-loading' => 'wiki@home Interface lueden <blink>...</blink>',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 */
$messages['nl'] = array(
	'wah-short-audio' => '$1-geluidsbestand, $2',
	'wah-short-video' => '$1-videobestand, $2',
	'wah-short-general' => '$1-mediabestand, $2',
	'wah-long-audio' => '($1-geluidsbestand, lengte $2, $3)',
	'wah-long-video' => '($1-videobestand, lengte $2, $4×$5 pixels, $3)',
	'wah-long-multiplexed' => '(gemultiplexed geluids/videobestand, $1, lengte $2, $4×$5 pixels, $3 totaal)',
	'wah-long-general' => '(mediabestand, lengte $2, $3)',
	'wah-long-error' => '(ffmpeg kon dit bestand niet lezen: $1)',
	'wah-transcode-working' => 'Deze video wordt getranscodeerd ($1% gedaan)',
	'wah-transcode-helpout' => 'U kunt helpen dit bestand te transcoderen door naar [[Special:WikiAtHome|Wiki@Home]] te gaan',
	'wah-transcode-fail' => 'Het transcoderen van dit bestand is mislukt.',
	'wah-javascript-off' => 'JavaScript moet ingeschakeld zijn om deel te nemen in Wiki@Home',
	'wah-loading' => 'Wiki@Home-interface aan het laden <blink>...</blink>',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'wah-desc' => 'Permet de distribuir lo trabalh de transcodatge de vidèo als clients en utilizant firefogg.',
	'wah-user-desc' => "Wiki@Home permet als membres de la comunautat de balhar de cicles processor liures per ajudar d'operacions intensivas en ressorsas.",
	'wah-short-audio' => 'fichièr de son $1, $2',
	'wah-short-video' => 'fichièr vidèo $1, $2',
	'wah-short-general' => 'fichièr mèdia $1, $2',
	'wah-long-audio' => '(fichièr son $1, durada $2, $3)',
	'wah-long-video' => '(fichièr vidèo $1, durada $2, $4×$5 pixèls, $3)',
	'wah-long-multiplexed' => '(fichièr àudio / vidèo multiplexada $1, durada $2, $4×$5 pixèls, $3 total)',
	'wah-long-general' => '(fichièr mèdia, durada $2, $3)',
	'wah-long-error' => '(ffmpeg a pas pogut legir aqueste fichièr : $1)',
	'wah-transcode-working' => 'Aquesta vidèo es a èsser transcodada e $1 % son estats efectuats',
	'wah-transcode-helpout' => 'Podètz ajudar a transcodar aquesta vidèo en visitant [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Aqueste fichièr a pas pogut èsser transcodat.',
	'wah-javascript-off' => 'Vos cal activar JavaScript per participar a Wiki@Home',
	'wah-loading' => "cargament de l'interfàcia Wiki@Home <blink>...</blink>",
);

/** Russian (Русский)
 * @author Ferrer
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'wah-desc' => 'Позволяет использовать распределённое перекодирование видео, с помощью firefogg.',
	'wah-user-desc' => 'Wiki@Home позволяет членам сообщества пожертвовать излишней мощностью процессоров, помогая с ресурсоёмкими операциями',
	'wah-short-audio' => '$1 звуковой файл, $2',
	'wah-short-video' => '$1 видео-файл, $2',
	'wah-short-general' => '$1 медиа-файл, $2',
	'wah-long-audio' => '($1 звуковой файл, продолжительность $2, $3)',
	'wah-long-video' => '($1 видео-файл, продолжительность $2, $4×$5 пикселов, $3)',
	'wah-long-multiplexed' => '(мультиплексированный аудио/видео-файл, $1, продолжительность $2, $4×$5 пикселов, всего $3)',
	'wah-long-general' => '(медиа-файл, продолжительность $2, $3)',
	'wah-long-error' => '(ffmpeg не может прочитать этот файл: $1)',
	'wah-transcode-working' => 'Это видео сейчас перекодируется, выполнено $1%.',
	'wah-transcode-helpout' => 'Вы можете помочь перекодировать это видео, посетите [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Не удалось перекодировать этот файл.',
	'wah-javascript-off' => 'У вас должен быть включён JavaScript, для возможности участия в Wiki@Home',
	'wah-loading' => 'Загрузка интерфейса Wiki@Home <blink>...</blink>',
);

/** Serbian Cyrillic ekavian (ћирилица)
 * @author Михајло Анђелковић
 */
$messages['sr-ec'] = array(
	'wah-short-audio' => '$1 звучни фајл, $2',
	'wah-short-video' => '$1 видео-фајл, $2',
	'wah-short-general' => '$1 медија-фајл, $2',
	'wah-long-audio' => '($1 звучни фајл, трајање $2, $3)',
	'wah-long-video' => '($1 видео-фајл, трајање $2, $3×$5 пиксела, $3)',
	'wah-long-multiplexed' => '(мултиплексовани аудио/видео фајл, $1, трајање $2, $4×$5 пиксела, $3 укупно)',
	'wah-long-general' => '(медија-фајл, трајање $2, $3)',
	'wah-long-error' => '(ffmpeg није могао да прочита овај фајл: $1)',
	'wah-transcode-working' => 'Овај видео се тренутно обрађује, и готово је $1% посла',
	'wah-javascript-off' => 'Морате омогућити JavaScript, да бисте учествовали у Wiki@Home',
	'wah-loading' => 'учитавање Wiki@Home интерфејса <blink>...</blink>',
);

