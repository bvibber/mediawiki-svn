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
	'wah-desc'		=> 'Enables distributing transcoding video jobs to clients using firefogg.',
	'wah-user-desc'		=> 'Wiki@Home enables community members to donate spare cpu cycles to help with resource intensive operations',
	'wah-short-audio'	=> '$1 sound file, $2',
	'wah-short-video'	=> '$1 video file, $2',
	'wah-short-general'	=> '$1 media file, $2',

	'wah-long-audio'       	=> '($1 sound file, length $2, $3)',
	'wah-long-video'       	=> '($1 video file, length $2, $4×$5 pixels, $3)',
	'wah-long-multiplexed' 	=> '(multiplexed audio/video file, $1, length $2, $4×$5 pixels, $3 overall)',
	'wah-long-general'     	=> '(media file, length $2, $3)',
	'wah-long-error'       	=> '(ffmpeg could not read this file: $1)',

	'wah-transcode-working' => 'This video is being transcoded its $1% done',
	'wah-transcode-helpout' => 'You can help transcode this video by visiting [[Special:WikiAtHome|Wiki@Home]]',

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
	'wah-javascript-off' => 'У Вас павінен быць уключаны JavaScript для ўдзелу ў Wiki@Home',
	'wah-loading' => 'загрузка інтэрфэйсу Wiki@Home <blink>...</blink>',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'wah-short-audio' => '$1 zvučna datoteka, $2',
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
	'wah-javascript-off' => 'Dyrbiš javascript zmóžnić, zo by so na Wiki@Home wobdźělił',
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
	'wah-javascript-off' => 'Dinge Brauser moß JavaSkrep künne un ennjeschalldt han, domet De bei Wiki@Home metmaache kanns.',
	'wah-loading' => 'Ben wiki@home sing Schnetshtëll aam laade<blink>{{int:ellipsis}}</blink>',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'wah-short-audio' => '$1 Toun-Fichier, $2',
	'wah-short-video' => '$1 Video-Fichier, $2',
	'wah-short-general' => '$1 Medie-Fichier, $2',
	'wah-javascript-off' => 'Dir musst Javascript zouloossen fir bäi Wiki@Home matzemaachen',
	'wah-loading' => 'wiki@home Interface lueden <blink>...</blink>',
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
	'wah-javascript-off' => 'У вас должен быть включён JavaScript, для возможности участия в Wiki@Home',
	'wah-loading' => 'Загрузка интерфейса Wiki@Home <blink>...</blink>',
);

