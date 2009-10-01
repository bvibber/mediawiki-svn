<?php
/**
 * Internationalisation file for extension Wiki At Home.
 *
 * @addtogroup Extensions
*/

require_once( dirname(__FILE__) . '/WikiAtHome.i18n.magic.php' );

$messages = array();

/** English
 * @author Michael Dale
 * @author Purodha 	http://ksh.wikipedia.org/wiki/User:Purodha
 */
$messages['en'] = array(
	'specialwikiathome' => 'Wiki@Home',
	'wah-desc' => 'Enables distributing transcoding video jobs to clients using Firefogg',
	'wah-user-desc' => 'Wiki@Home enables community members to donate spare cpu cycles to help with resource intensive operations',
	'wah-short-audio' => '$1 sound file, $2',
	'wah-short-video' => '$1 video file, $2',
	'wah-short-general' => '$1 media file, $2',

	'wah-long-audio' => '($1 sound file, length $2, $3)',
	'wah-long-video' => '($1 video file, length $2, $4×$5 pixels, $3)',
	'wah-long-multiplexed' => '(multiplexed audio/video file, $1, length $2, $4×$5 pixels, $3 overall)',
	'wah-long-general' => '(media file, length $2, $3)',
	'wah-long-error' => '(ffmpeg could not read this file: $1)',

	'wah-transcode-working' => 'This video is being processed, please try again later',
	'wah-transcode-helpout' => 'The clip is $1 percent done.
You can help transcode this video by visiting [[Special:WikiAtHome|Wiki@Home]]',

	'wah-transcode-fail' => 'This file failed to transcode.',

	'wah-javascript-off' => 'You must have JavaScript enabled to participate in Wiki@Home',
	'wah-loading' => 'loading Wiki@Home interface <blink>...</blink>'
);

/** Message documentation (Message documentation)
 * @author Fryed-peach
 * @author Purodha
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
	'wah-transcode-helpout' => 'You can use PLURAL on $1.',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'wah-desc' => "Maak dit moontlik om die transkodering van video's na kliënte te versprei via firefogg",
	'wah-user-desc' => 'Wiki@Home maak dit vir gemeenskapslede moontlik om rekenaartyd te skenk om sodoende te help met die uitvoer van moeilike take',
	'wah-short-audio' => '$1-klanklêer, $2',
	'wah-short-video' => '$1-videolêer, $2',
	'wah-short-general' => '$1-medialêer, $2',
	'wah-long-audio' => '($1-klanklêer, lengte $2, $3)',
	'wah-long-video' => '($1-videolêer, lengte $2, $4×$5 pixsels, $3)',
	'wah-long-multiplexed' => '(gemultiplekseerde klank/videolêer, $1, lengte $2, $4×$5 pixels, $3 totaal)',
	'wah-long-general' => '(medialêer, lengte $2, $3)',
	'wah-long-error' => '(ffmpeg kon die lêer nie lees nie: $1)',
	'wah-transcode-working' => 'Hierdie video word tans verwerk.
Probeer later weer.',
	'wah-transcode-helpout' => 'Die klip is $1 persent voltooi.
U kan help om die lêer te transkodeer deur na [[Special:WikiAtHome|Wiki@Home]] te gaan',
	'wah-transcode-fail' => 'Die transkodering van die lêer het misluk.',
	'wah-javascript-off' => 'JavaScript moet geaktiveer wees om aan Wiki@Home deel te neem',
	'wah-loading' => 'Die Wiki@Home-koppelvlak is besig om te laai <blink>...</blink>',
);

/** Arabic (العربية)
 * @author OsamaK
 */
$messages['ar'] = array(
	'wah-short-audio' => 'ملف صوتي $1، $2',
	'wah-short-video' => 'ملف فيديو $1، $2',
	'wah-short-general' => 'ملف وسائط $1، $2',
	'wah-long-general' => '(ملف وسائط طوله $2، $3)',
	'wah-long-error' => '(لم يتمكن ffmpeg من قراءة هذا الملف: $1)',
	'wah-transcode-working' => 'تتم الآن معالجة الفيديو، من فضلك حاول لاحقًا مرة أخرى',
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
	'wah-transcode-working' => 'Гэты відэа-файл зараз апрацоoўваецца. Калі ласка, паспрабуйце ізноў пазьней',
	'wah-transcode-helpout' => 'Гэтая частка файла апрацаваная на $1 {{PLURAL:$1|працэнт|працэнты|працэнтаў}}. Вы можаце дапамагчы перакадыраваць гэты відэа-файл наведаўшы [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Немагчыма перакадаваць гэты файл.',
	'wah-javascript-off' => 'У Вас павінен быць уключаны JavaScript для ўдзелу ў Wiki@Home',
	'wah-loading' => 'загрузка інтэрфэйсу Wiki@Home <blink>...</blink>',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'wah-desc' => "Talvezout a ra da zasparzh al labour treuzkodañ videoioù d'ar c'hliantoù dre firefogg",
	'wah-user-desc' => "Talvezout a ra Wiki@Home da izili ar gumuniezh da reiñ kelc'hioù prosesor diac'hub evit harpañ oberadurioù pounner da gas da benn",
	'wah-short-audio' => 'restr son $1, $2',
	'wah-short-video' => 'restr video $1, $2',
	'wah-short-general' => 'restr media $1, $2',
	'wah-long-audio' => '(restr son $1, pad $2, $3)',
	'wah-long-video' => '(restr video $1, pad $2, $4×$5 piksel, $3)',
	'wah-long-multiplexed' => '(restr klevet/video liesplezhet $1, pad $2, $4×$5 piksel, $3 hollad)',
	'wah-long-general' => '(restr media, pad $2, $3)',
	'wah-long-error' => "(n'eo ket bet ffmpeg evit lenn ar restr-mañ : $1)",
	'wah-transcode-working' => "Emeur o treuzkodañ ar video, klaskit en-dro diwezhatoc'hik",
	'wah-transcode-helpout' => "Savet ez eus $1 % eus ar c'hlip-mañ. Skoazellañ da dreuzkodañ ar video-mañ a c'hallit ober en ur vont war [[Special:WikiAtHome|Wiki@Home]]",
	'wah-transcode-fail' => "C'hwitet eo treuzkodañ ar restr.",
	'wah-javascript-off' => 'Rekis eo bezañ gweredekaet JavaScript evit kemer perzh e Wiki@Home',
	'wah-loading' => 'o kargañ etrefas Wiki@Home <blink>...</blink>',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'wah-desc' => 'Omogućava distribuciju transkodiranih video poslova klijentima putem Firefogga',
	'wah-user-desc' => 'Wiki@Home omogućava članovima zajednice da doniraju neiskorištene cpu cikluse za pomoć pri operacijama koje zahtjevaju dosta resursa',
	'wah-short-audio' => '$1 zvučna datoteka, $2',
	'wah-short-video' => '$1 video datoteka, $2',
	'wah-short-general' => '$1 medijalna datoteka, $2',
	'wah-long-audio' => '($1 zvučna datoteka, dužina $2, $3)',
	'wah-long-video' => '($1 video datoteka, dužina $2, $4×$5 piksela, $3)',
	'wah-long-multiplexed' => '(multipleksirana audio/video datoteka, $1, dužina $2, $4×$5 piksela, $3 sveukupno)',
	'wah-long-general' => '(medijalna datoteka, dužina $2, $3)',
	'wah-long-error' => '(ffmpeg nije mogao pročitati ovu datoteku: $1)',
	'wah-transcode-working' => 'Ovaj video se obrađuje, molimo pokušajte kasnije',
	'wah-transcode-helpout' => 'Isječak je završen $1 posto.
Možete pomoći pri transkodiranju ovog videa ako posjetite [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Ova datoteka se nije uspjela transkodirati.',
	'wah-javascript-off' => 'Morate imati omogućenu JavaScript za učestvovanje u Wiki@Home',
	'wah-loading' => 'učitavam interfejs Wiki@Home <blink>...</blink>',
);

/** German (Deutsch)
 * @author Pill
 * @author Sebastian Wallroth
 * @author Umherirrender
 */
$messages['de'] = array(
	'wah-desc' => 'Ermöglicht das Verteilen von Video-Transkodier-Jobs an Clients mit firefogg',
	'wah-user-desc' => 'Wiki@Home ermöglicht Community-Mitgliedern freie CPU-Zeiten zu spenden, um bei ressourcenintensiven Operationen zu helfen',
	'wah-short-audio' => '$1-Audiodatei, $2',
	'wah-short-video' => '$1-Videodatei, $2',
	'wah-short-general' => '$1-Mediadatei, $2',
	'wah-long-audio' => '($1-Audiodatei, Länge: $2, $3)',
	'wah-long-video' => '($1-Videodatei, Länge: $2, $4×$5 Pixel, $3)',
	'wah-long-multiplexed' => '(Multiplex-Audio-/Video-Datei, $1, Länge: $2, $4×$5 Pixel, $3)',
	'wah-long-general' => '(Mediadatei, Länge: $2, $3)',
	'wah-long-error' => '(ffmpeg konnte diese Datei nicht lesen: $1)',
	'wah-transcode-working' => 'Das Video wird verarbeitet, bitte versuche es später wieder',
	'wah-transcode-helpout' => 'Der Clip ist zu $1 % verarbeitet.
Du kannst dabei helfen dieses Video zu verarbeiten, indem du [[Special:WikiAtHome|Wiki@Home]] besuchst',
	'wah-transcode-fail' => 'Diese Datei konnte nicht transkodiert werden.',
	'wah-javascript-off' => 'Du musst JavaScript aktiviert haben, um bei Wiki@Home teilnehmen zu können',
	'wah-loading' => 'Lade Wiki@Home-Benutzeroberfläche <blink>…</blink>',
);

/** German (formal address) (Deutsch (Sie-Form))
 * @author Imre
 */
$messages['de-formal'] = array(
	'wah-transcode-helpout' => 'Der Clip ist zu $1 % verarbeitet.
Sie können dabei helfen dieses Video zu verarbeiten, indem Sie [[Special:WikiAtHome|Wiki@Home]] besuchen',
	'wah-javascript-off' => 'Sie müssen JavaScript aktiviert haben, um bei Wiki@Home teilnehmen zu können',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'wah-desc' => 'Zmóžnja rozdźělenje nadawkow wideopśekoděrowanja klientam z pomocu Firefogg',
	'wah-user-desc' => 'Wiki@Home zmóžnja cłonkam zgromaźenstwa liche CPU-cykluse dariś, aby pomagał pśi operacijach, kótarež pśetrjebuju wjele resursow',
	'wah-short-audio' => 'Awdiodataja $1, $2',
	'wah-short-video' => 'Wideodataja $1, $2',
	'wah-short-general' => 'Medijowa dataja $1, $2',
	'wah-long-audio' => '(Awdiodataja $1, dłujkosć $2, $3)',
	'wah-long-video' => '(Wideodataja $1, dłujkosć $2, $4×$5 pikselow, $3)',
	'wah-long-multiplexed' => '(multipleksna awdio/wideodataja, $1, dłujkosć $2, $4×$5 pikselow, $3 dogromady)',
	'wah-long-general' => '(medijowa dataja, dłujkosć $2, $3)',
	'wah-long-error' => '(ffmpeg njejo mógł toś tu dataju cytaś: $1)',
	'wah-transcode-working' => 'Wideo se pśeźěłujo, pšosym wopytaj póznjej hyšći raz',
	'wah-transcode-helpout' => 'Klip jo na $1 {{PLURAL:$1|procent|procenta|procenty|procentow}} gótowy.
Móžoš pomagaś toś te wideo pśekoděrowaś, z tym až woglědujoš k [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Toś ta dataja njejo se dała pśekoděrowaś.',
	'wah-javascript-off' => 'Musyš JavaScript zmóžniś, aby se na Wiki@Home wobźělił',
	'wah-loading' => 'Pówjerch Wiki@Home se zacytujo <blink>...</blink>',
);

/** Greek (Ελληνικά)
 * @author ZaDiak
 */
$messages['el'] = array(
	'wah-short-audio' => '$1 αρχείο ήχου, $2',
	'wah-short-video' => '$1 αρχείο βίντεο, $2',
	'wah-short-general' => '$1 αρχείο μέσου, $2',
	'wah-long-audio' => '($1 αρχείο ήχου, διάρκεια $2, $3)',
	'wah-long-video' => '($1 αρχείο βίντεο, διάρκεια $2, $4×$5 πίξελ, $3)',
	'wah-long-general' => '(αρχείο μέσου, διάρκεια $2, $3)',
	'wah-long-error' => '(το ffmpeg δεν μπορούσε να διαβάσει αυτό το αρχείο: $1)',
	'wah-transcode-working' => 'Αυτό το βίντεο προωθείται, παρακαλώ δοκιμαστε ξανά αργότερα',
);

/** Spanish (Español)
 * @author Antur
 * @author Crazymadlover
 * @author Imre
 */
$messages['es'] = array(
	'wah-desc' => 'Permitir la distribución de videos convertidos a los clientes utilizando firefogg',
	'wah-user-desc' => 'Wiki@Home permite a los miembros de la comunidad donar ciclos ociosos de cpu para ayudar en operaciones intensivas',
	'wah-short-audio' => 'Archivo de sonido $1, $2',
	'wah-short-video' => 'Archivo de vídeo $1, $2',
	'wah-short-general' => 'Archivo de media $1, $2',
	'wah-long-audio' => '( archivo de sonido $1, tamaño $2, $3)',
	'wah-long-video' => '(archivo de video $1, tamaño $2, $4x$5 pixels, $3)',
	'wah-long-multiplexed' => '(archivo mutiplexado de audio/video, $1, largo $2, $4x$5 pixeles, total $3)',
	'wah-long-general' => '(archivo de media, tamaño $2, $3)',
	'wah-long-error' => '(ffmpeg no puede leer el archivo: $1)',
	'wah-transcode-working' => 'Este video está siendo procesado, por favor intente de nuevo mas tarde.',
	'wah-transcode-helpout' => 'El fragmento está $1 por ciento terminado.
Ud. puede ayudar a convertir este video visitando [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Falló la conversión del archivo.',
	'wah-javascript-off' => 'Ud. debe tener JavaScript activo para participar en Wiki@Home',
	'wah-loading' => 'cargando interfaz Wiki@Home <blink>...</blink>',
);

/** Basque (Euskara)
 * @author Kobazulo
 */
$messages['eu'] = array(
	'wah-short-audio' => '$1 soinu fitxategia, $2',
	'wah-short-video' => '$1 bideo fitxategia, $2',
	'wah-short-general' => '$1 media fitxategia, $2',
	'wah-long-audio' => '($1 soinu fitxategia, luzeera $2, $3)',
	'wah-long-video' => '($1 bideo fitxategia, luzeera $2, $4×$5 pixel, $3)',
	'wah-long-general' => '(multimedia fitxategia, iraupena $2, $3)',
	'wah-long-error' => '(ffmpeg-ek ezin du fitxategi hau irakurri: $1)',
	'wah-transcode-working' => 'Bideo hau prozesatzen ari da, mesedez, saia zaitez beranduago',
);

/** Finnish (Suomi)
 * @author Cimon Avaro
 * @author Crt
 * @author Silvonen
 */
$messages['fi'] = array(
	'wah-user-desc' => 'Wiki@Homen avulla yhteisön jäsenet voivat lahjoittaa käyttämätöntä suoritinaikaa paljon resursseja kuluttaviin operaatioihin.',
	'wah-short-audio' => 'Äänitiedosto $1, $2',
	'wah-short-video' => 'Videotiedosto $1, $2',
	'wah-short-general' => 'Mediatiedosto $1, $2',
	'wah-long-audio' => '(äänitiedosto $1, pituus $2, $3)',
	'wah-long-video' => '($1 videotiedosto, pituus $2, $4×$5 pikseliä, $3)',
	'wah-long-general' => '(mediatiedosto, pituus $2, $3)',
	'wah-long-error' => '(ffmpeg ei kyennyt lukemaan tätä tiedostoa: $1)',
	'wah-transcode-working' => 'Tätä videota käsitellään parhaillaan, yritä myöhemmin uudelleen',
	'wah-transcode-fail' => 'Tämä tiedosto ei transkoodautunut.',
	'wah-javascript-off' => 'JavaScriptin on oltava käytössä, jotta voit osallistua Wiki@Homeen',
	'wah-loading' => 'ladataan Wiki@Home-käyttöliittymää <blink>...</blink>',
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
	'wah-transcode-working' => "Cette vidéo est en train d'être transcodée, ressayez plus tard",
	'wah-transcode-helpout' => 'Ce clip est achevé à $1 %. Vous pouvez aider à transcoder cette vidéo en visitant [[Special:WikiAtHome|Wiki@Home]]',
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
	'wah-transcode-working' => 'Este vídeo está sendo procesado, por favor, inténteo máis tarde',
	'wah-transcode-helpout' => 'O vídeo está feito ao $1%.
Pode axudar na transcodificación deste vídeo visitando o [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Fallou a transcodificación do ficheiro.',
	'wah-javascript-off' => 'Debe ter o Javascript activado para participar no Wiki@Home',
	'wah-loading' => 'cargando a interface do Wiki@Home <blink>...</blink>',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'wah-desc' => 'Macht s Verteile vu Video-Transkodier-Jobs an Clients mit firefogg megli',
	'wah-user-desc' => 'Wiki@Home macht s Gmeinschafts-Mitglider megli freji CPU-Zyte z spände go bi ressourceintensive Operatione z hälfe',
	'wah-short-audio' => '$1-Audiodatei, $2',
	'wah-short-video' => '$1-Videodatei, $2',
	'wah-short-general' => '$1-Mediadatei, $2',
	'wah-long-audio' => '($1-Audiodatei, Lengi: $2, $3)',
	'wah-long-video' => '($1-Videodatei, Lengi: $2, $4×$5 Pixel, $3)',
	'wah-long-multiplexed' => '(Multiplex-Audio-/Video-Datei, $1, Lengi: $2, $4×$5 Pixel, $3 insgsamt)',
	'wah-long-general' => '(Mediadatei, Lengi: $2, $3)',
	'wah-long-error' => '(ffmpeg het die Datei nit chenne läse: $1)',
	'wah-transcode-working' => 'S Video wird verarbeitet, bitte versuech s speter nomol',
	'wah-transcode-helpout' => 'Dr Clip isch zue $1 Prozänt verarbeitet.
Du chasch hälfe des Video z verarbeite, indäm Du [[Special:WikiAtHome|Wiki@Home]] bsuechsch',
	'wah-transcode-fail' => 'Die Datei het nit chenne transkodiert wäre.',
	'wah-javascript-off' => 'Du muesch JavaScript megli mache go bi Wiki@Home mitmache',
	'wah-loading' => 'Am Lade vu dr Wiki@Home-Benutzeroberflächi <blink>…</blink>',
);

/** Hebrew (עברית)
 * @author Rotemliss
 * @author YaronSh
 */
$messages['he'] = array(
	'wah-desc' => 'מתן האפשרות להפצת עבודות לקידוד וידאו אל לקוחות באמצעות firefogg',
	'wah-user-desc' => 'ההרחבה Wiki@Home מאפשרת לחברי הקהילה לתרום כוחות עיבוד עודפים על מנת לעזור לפעולות הדורשות משאבים רבים',
	'wah-short-audio' => 'קובץ שמע מסוג $1, $2',
	'wah-short-video' => 'קובץ וידאו מסוג $1, $2',
	'wah-short-general' => 'קובץ מדיה מסוג $1, $2',
	'wah-long-audio' => '(קובץ שמע מסוג $1, באורך $2, $3)',
	'wah-long-video' => '(קובץ וידאו מסוג $1, באורך $2, $4×$5 פיקסלים, $3)',
	'wah-long-multiplexed' => '(קובץ שמע/וידאו מרובב, $1, באורך $2, $4×$5 פיקסלים, $3 בסך הכול)',
	'wah-long-general' => '(קובץ מדיה, באורך $2, $3)',
	'wah-long-error' => '(ffmpeg לא הצליח לקרוא קובץ זה: $1)',
	'wah-transcode-working' => 'וידאו זה נמצא בתהליכי עיבוד, נא לנסות שוב מאוחר יותר',
	'wah-transcode-helpout' => 'עיבוד סרטון זה הושלם ב־$1 אחוזים.
ניתן לעזור בקידוד הווידאו הזה על ידי ביקור בדף [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'קידוד קובץ זה נכשל.',
	'wah-javascript-off' => 'עליכם להפעיל את תכונת ה־JavaScript כדי לקחת חלק ב־Wiki@Home',
	'wah-loading' => 'ממשק Wiki@Home נטען כעת <blink>...</blink>',
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
	'wah-transcode-working' => 'Tute widejo so wobdźěłuje, prošu spytaj pozdźišo hišće raz',
	'wah-transcode-helpout' => 'Klip je na $1 {{PLURAL:$1|procent|procentaj|procenty|procentow}} hotowy. Móžeš pomhać tute widejo přez wopyt na [[Special:WikiAtHome|Wiki@Home]] překodować',
	'wah-transcode-fail' => 'Njeje so poradźiło tutu dataju překodować.',
	'wah-javascript-off' => 'Dyrbiš JavaScript zmóžnić, zo by so na Wiki@Home wobdźělił',
	'wah-loading' => 'Začitanje powjercha Wik@Home <blink> ... </blink>',
);

/** Hungarian (Magyar)
 * @author Glanthor Reviol
 */
$messages['hu'] = array(
	'wah-desc' => 'Lehetővé teszi a videó-átkódolási feladatok elosztott feldolgozását a Firefoggot használó klienseken',
	'wah-user-desc' => 'A Wiki@Home segítségével a közösség tagjai felajánlhatják a felesleges CPU idejüket az erőforrásigényes műveletekhez',
	'wah-short-audio' => '$1 hangfájl, $2',
	'wah-short-video' => '$1 videofájl, $2',
	'wah-short-general' => '$1 médiafájl, $2',
	'wah-long-audio' => '($1 hangfájl, hossza: $2, bitsűrűsége: $3)',
	'wah-long-video' => '($1 videófájl, hossza $2, $4×$5 képpont, bitsűrűsége: $3)',
	'wah-long-multiplexed' => '(multiplexelt audió/videó fájl, $1, hossza: $2, $4×$5 képpont, $3 bitsűrűség)',
	'wah-long-general' => '(médiafájl, hossza: $2, bitsűrűsége: $3)',
	'wah-long-error' => '(az ffmpeg nem tudja olvasni a következő fájlt: $1)',
	'wah-transcode-working' => 'Ez a videó feldolgozás alatt van, kérlek próbáld később',
	'wah-transcode-helpout' => 'A klip $1 százaléka kész.
Segíthetsz ennek a videónak az átkódolásában a [[Special:WikiAtHome|Wiki@Home]] lap felkeresésével.',
	'wah-transcode-fail' => 'A fájl átkódolása meghiúsult.',
	'wah-javascript-off' => 'A Wiki@Home-ban való részvételhez engedélyezned kell a JavaScriptet',
	'wah-loading' => 'Wiki@Home interfész betöltése <blink>…</blink>',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'wah-desc' => 'Permitte le division del carga de transcodification de video inter computatores-clientes per medio de firefogg',
	'wah-user-desc' => 'Wiki@Home permitte al membros del communicate donar cyclos libere del processator pro adjutar operationes intensive in ressources.',
	'wah-short-audio' => 'file audio $1, $2',
	'wah-short-video' => 'file video $1, $2',
	'wah-short-general' => 'file multimedia $1, $2',
	'wah-long-audio' => '(file audio $1, durata $2, $3)',
	'wah-long-video' => '(file video $1, durata $2, $4×$5 pixels, $3)',
	'wah-long-multiplexed' => '(file audio/video multiplexate, $1, durata $2, $4×$5 pixels, $3 in total)',
	'wah-long-general' => '(file multimedia, durata $2, $3)',
	'wah-long-error' => '(ffmpeg non poteva leger le file: $1)',
	'wah-transcode-working' => 'Iste video es in le processo de esser transcodificate. Per favor reproba plus tarde.',
	'wah-transcode-helpout' => 'Le clip es complete a $1 per cento.
Tu pote adjutar a transcodificar iste video per visitar [[Special:WikiAtHome|Wiki@Home]].',
	'wah-transcode-fail' => 'Le transcodification de iste file ha fallite.',
	'wah-javascript-off' => 'Tu debe activar JavaScript pro participar in Wiki@Home.',
	'wah-loading' => 'carga interfacie de Wiki@Home <blink>...</blink>',
);

/** Indonesian (Bahasa Indonesia)
 * @author Irwangatot
 * @author Kandar
 */
$messages['id'] = array(
	'wah-desc' => '

Memungkinkan mendistribusikan pekerjaan video transcoding untuk klien yang menggunakan Firefogg',
	'wah-user-desc' => 'Wiki@Home memungkinkan anggotan komunitas untuk mendonasikan cadangan CPU untuk membantu beroperasi intensif sumber daya',
	'wah-short-audio' => '$1 berkas suara, $2',
	'wah-short-video' => '$1 berkas video, $2',
	'wah-short-general' => '$1 berkas media, $2',
	'wah-long-audio' => '($1 berkas suara, panjang $2, $3)',
	'wah-long-video' => '($1 berkas video, panjang $2, $4×$5 piksel, $3)',
	'wah-long-multiplexed' => '(Berkas multiplexed audio/video, $1, lama $2, $4x$5 piksel, $3 keseluruhan)',
	'wah-long-general' => '(berkas media, panjang $2, $3)',
	'wah-long-error' => '(ffmpeg tak bisa membaca berkas ini: $1)',
	'wah-transcode-working' => 'Video ini sedang diolah, silahkan coba lagi nanti',
	'wah-transcode-helpout' => 'Klip, $1 persen selesai. 
Anda dapat membantu video transcode ini dengan mengunjungi [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Berkas ini gagal untuk di transcode.',
	'wah-javascript-off' => 'Anda harus mengaktifkan JavaScript agar dapat berpartisipasi di Wiki@Home',
	'wah-loading' => 'Memuat Antarmuka Wiki@Home <blink>...</blink>',
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
	'wah-transcode-working' => 'この動画は現在処理中です。後でまた試してください。',
	'wah-transcode-helpout' => 'このクリップは$1%完了しています。[[Special:WikiAtHome|Wiki@Home]] を使用すると、この動画のトランスコードをあなたが手伝うことができます',
	'wah-transcode-fail' => 'このファイルはトランスコードに失敗しました。',
	'wah-javascript-off' => 'Wiki@Home に参加するには JavaScript を有効にする必要があります',
	'wah-loading' => 'Wiki@Home のインタフェースを読み込み中<blink>…</blink>',
);

/** Khmer (ភាសាខ្មែរ)
 * @author វ័ណថារិទ្ធ
 */
$messages['km'] = array(
	'wah-short-audio' => '$1 ឯកសារ​សំលេង​, $2',
	'wah-short-video' => '$1 ឯកសារ​វីដេអូ​, $2',
	'wah-short-general' => '$1 ឯកសារ​មេឌា​, $2',
	'wah-long-audio' => '($1 ឯកសារសំឡេង, រយៈពេល$2, $3)',
	'wah-long-video' => '($1 ឯកសារវីដេអូ, រយៈពេល $2, $4×$5 pixels, $3)',
	'wah-long-general' => '(ឯកសារមេឌា, រយៈពេល$2, $3)',
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
	'wah-transcode-working' => 'Heh dä Viddejo weed ömkodeet, versöhk et schpääder norr_ens',
	'wah-transcode-helpout' => 'Dä Viddejo es zo $1 Prozent jedonn. Do kanns beim Ömkodeere hellfe för heh dä Viddejo, jangk doför noh de Sigg [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Di Dattei lehß sesch ömkodeere.',
	'wah-javascript-off' => 'Dinge Brauser moß JavaSkrep künne un ennjeschalldt han, domet De bei Wiki@Home metmaache kanns.',
	'wah-loading' => 'Ben wiki@home sing Schnetshtëll aam laade<blink>{{int:ellipsis}}</blink>',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Les Meloures
 * @author Robby
 */
$messages['lb'] = array(
	'wah-desc' => "Erlaabten et fir d'Ëmschreiwe vu Video-Aarbechten op Client ze verdeelen déi Firefogg benotzen.",
	'wah-user-desc' => 'Wiki@Doheem erlaabt et Membere vun der Gemeinschaft fir spuersam CPU-Perioden ze spenden fir bäi resourcenintensiven Operatiounen ze hëllefen',
	'wah-short-audio' => '$1 Toun-Fichier, $2',
	'wah-short-video' => '$1 Video-Fichier, $2',
	'wah-short-general' => '$1 Medie-Fichier, $2',
	'wah-long-audio' => '($1 Tounfichier, Längt $2, $3)',
	'wah-long-video' => '($1 Video-Fichier, Längt $2, $4x$5 Pixel, $3)',
	'wah-long-multiplexed' => '(Multiplex-Audio-/Video-Fichier, $1, Längt: $2, $4×$5 Pixel, $3 am Ganzen)',
	'wah-long-general' => '(Mediefichier, Längt $2, $3)',
	'wah-long-error' => '(ffmpeg konnt de Fichier $1 net liesen)',
	'wah-transcode-working' => 'Dëse Video gëtt elo ëmgewandelt, versicht et spéider w.e.g. nach eng kéier',
	'wah-transcode-helpout' => 'De Clip ass zu $1 % fäerdeg. Dir kënnt hëllefen dëse Video ze transcodéiere wann Dir [[Special:WikiAtHome|Wiki@Home]] besicht',
	'wah-transcode-fail' => 'Dëse Fichier konnt net ëmgeschriwwe ginn.',
	'wah-javascript-off' => 'Dir musst JavaScript zouloossen fir bäi Wiki@Doheem matzemaachen',
	'wah-loading' => 'wiki@home Interface lueden <blink>...</blink>',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'wah-desc' => 'Овозможува дистрибуирање на задачи за видео транскодирање на клиенти со помош на firefogg',
	'wah-user-desc' => 'Wiki@Home им овозможува на членовите на заедницата да донираат одвишок процесорски циклуси како помош во операции кои бараат доста ресурси',
	'wah-short-audio' => '$1 аудиоснимка, $2',
	'wah-short-video' => '$1 видеоснимка, $2',
	'wah-short-general' => '$1 снимка, $2',
	'wah-long-audio' => '($1 снимка, времетраење $2, $3)',
	'wah-long-video' => '($1 видеоснимка, времетраење $2, $4×$5 пиксели, $3)',
	'wah-long-multiplexed' => '(мултиплексирана аудио/видео снимка, $1, времетраење $2, $4×$5 пиксели, $3 долж снимката)',
	'wah-long-general' => '(снимка, времетраење $2, $3)',
	'wah-long-error' => '(ffmpeg не можеше да ја прочита оваа податотека: $1)',
	'wah-transcode-working' => 'Оваа видеоснимка се обработува, обидете се подоцна',
	'wah-transcode-helpout' => 'Снимката е $1 завршена.
Можете да помогнете со транскодирање на оваа видеоснимка ако ја посетите страницата [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Оваа податотека не успеа да се транскодира.',
	'wah-javascript-off' => 'Мора да имате овозможено JavaScript за да учествувате во Wiki@Home',
	'wah-loading' => 'се вчитува интерфејсот на Wiki@Home <blink>...</blink>',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 * @author Siebrand
 */
$messages['nl'] = array(
	'wah-desc' => 'Maakt het mogelijk videotranscoderingwerk te distribueren via firefogg',
	'wah-user-desc' => 'Wiki@Home maakt het voor gemeenschapsleden mogelijk computertijd te doneren om zo mee te helpen aan het uitvoeren van rekenintensieve taken',
	'wah-short-audio' => '$1-geluidsbestand, $2',
	'wah-short-video' => '$1-videobestand, $2',
	'wah-short-general' => '$1-mediabestand, $2',
	'wah-long-audio' => '($1-geluidsbestand, lengte $2, $3)',
	'wah-long-video' => '($1-videobestand, lengte $2, $4×$5 pixels, $3)',
	'wah-long-multiplexed' => '(gemultiplexed geluids/videobestand, $1, lengte $2, $4×$5 pixels, $3 totaal)',
	'wah-long-general' => '(mediabestand, lengte $2, $3)',
	'wah-long-error' => '(ffmpeg kon dit bestand niet lezen: $1)',
	'wah-transcode-working' => 'Deze video wordt verwerkt.
Probeer het later nog eens.',
	'wah-transcode-helpout' => 'De clip is $1 procent voltooid.
U kunt helpen dit bestand te transcoderen door naar [[Special:WikiAtHome|Wiki@Home]] te gaan',
	'wah-transcode-fail' => 'Het transcoderen van dit bestand is mislukt.',
	'wah-javascript-off' => 'JavaScript moet ingeschakeld zijn om deel te nemen aan Wiki@Home',
	'wah-loading' => 'Wiki@Home-interface aan het laden <blink>...</blink>',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Gunnernett
 */
$messages['nn'] = array(
	'wah-transcode-working' => 'Videoen vert arbeidd med, ver venleg og prøv igjen seinare',
	'wah-transcode-fail' => 'Kunde ikkje konvertera denne fila.',
	'wah-javascript-off' => 'Du må ha JavaScript aktivert for kunna delta i Wiki@Home',
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
	'wah-transcode-working' => 'Aquesta vidèo es a èsser transcodada, ensajatz tornamai mai tard',
	'wah-transcode-helpout' => 'Aqueste clip es acabat a $1 %. Podètz ajudar a transcodar aquesta vidèo en visitant [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Aqueste fichièr a pas pogut èsser transcodat.',
	'wah-javascript-off' => 'Vos cal activar JavaScript per participar a Wiki@Home',
	'wah-loading' => "cargament de l'interfàcia Wiki@Home <blink>...</blink>",
);

/** Polish (Polski)
 * @author Sp5uhe
 * @author ToSter
 */
$messages['pl'] = array(
	'wah-desc' => 'Włącza rozsyłanie zadań przekodowywania wideo do klientów za pomocą firefogg',
	'wah-user-desc' => 'Wiki@Home umożliwia członkom społeczności na dzielenie się wolnymi cyklami procesora, aby pomóc w operacjach intensywnie wykorzystujących zasoby',
	'wah-short-audio' => 'Plik dźwiękowy $1, $2',
	'wah-short-video' => 'Plik wideo $1, $2',
	'wah-short-general' => 'Plik multimedialny $1, $2',
	'wah-long-audio' => '(plik dźwiękowy $1, długość $2, $3)',
	'wah-long-video' => '(plik wideo $1, długość $2, $4×$5 pikseli, $3)',
	'wah-long-multiplexed' => '(multipleksowany plik audio i wideo, $1, długość $2, $4×$5 pikseli, w sumie $3)',
	'wah-long-general' => '(plik multimedialny, długość $2, $3)',
	'wah-long-error' => '(ffmpeg nie mógł odczytać pliku – $1)',
	'wah-transcode-working' => 'Ten plik wideo jest przetwarzany, spróbuj ponownie później',
	'wah-transcode-helpout' => 'Klip został przetworzony w $1 procentach.
Możesz pomóc w przekodowywaniu wideo, odwiedzając stronę [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Nie udało się przekodować tego pliku.',
	'wah-javascript-off' => 'Musisz mieć włączony JavaScript, aby brać udział w Wiki@Home',
	'wah-loading' => 'ładowanie interfejsu Wiki@Home <blink>...</blink>',
);

/** Piedmontese (Piemontèis)
 * @author Dragonòt
 */
$messages['pms'] = array(
	'wah-desc' => "A abìlita dij job ëd distribussion ëd video transcoding a client ch'a dòvro Firefogg",
	'wah-user-desc' => 'Wiki@Home a përmëtt a member ëd la comunità ëd doné dla potensa CPU për giuté con arzorse dle operassion intensive',
	'wah-short-audio' => '$1 file ëd son, $2',
	'wah-short-video' => '$1 file ëd video, $2',
	'wah-short-general' => '$1 file multimoien, $2',
	'wah-long-audio' => '($1 file ëd son, lunghëssa $2, $3)',
	'wah-long-video' => '($1 file ëd video, lunghëssa $2, $4x$5 pixel, $3)',
	'wah-long-multiplexed' => '(file audio/video multiplexà, $1, lunghëssa $2, $4×$5 pixel, $3 an tut)',
	'wah-long-general' => '(file multimoien, lunghëssa $2, $3)',
	'wah-long-error' => '(ffmpeg a peul pa lese sto file-sì: $1)',
	'wah-transcode-working' => "Sto video-sì a l'é stàit tratà, për piasì preuva pì tard",
	'wah-transcode-helpout' => "Ë clip a l'é fàit al $1 për sent.
It peule giuté a trascodifiché sto video-sì an visitand [[Special:WikiAtHome|Wiki@Home]]",
	'wah-transcode-fail' => "Sto file-sì a l'é pa podusse trascodifiché.",
	'wah-javascript-off' => 'It deuve avèj JavaScript abilità për partessipé an Wiki@Home',
	'wah-loading' => "carié l'antërfassa ëd Wiki@Home <blink>...</blink>",
);

/** Portuguese (Português)
 * @author Lijealso
 */
$messages['pt'] = array(
	'wah-transcode-working' => 'Este vídeo está a ser processado. Por favor, tente mais tarde',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Eduardo.mps
 * @author Heldergeovane
 */
$messages['pt-br'] = array(
	'wah-desc' => 'Permite a distribuição de tarefas de transcodificação para clientes utilizando firefogg',
	'wah-user-desc' => 'Wiki@Home permite que membros da comunidade doem ciclos de cpu ociosos para ajudar em operações com uso intensivo de recursos.',
	'wah-short-audio' => 'Arquivo de áudio $1, $2',
	'wah-short-video' => 'Arquivo de vídeo $1, $2',
	'wah-short-general' => 'Arquivo multimídia $1, $2',
	'wah-long-audio' => 'Arquivo de Áudio $1, $2 de duração, $3',
	'wah-long-video' => '(Arquivo de vídeo $1, $2 de duração, $4×$5 pixels, $3)',
	'wah-long-multiplexed' => '(Arquivo de áudio/vídeo multifacetado, $1, $2 de duração, $4×$5 pixels, $3 no todo)',
	'wah-long-general' => '(Arquivo multimídia, $2 de duração, $3)',
	'wah-long-error' => '(ffmpeg não pode ler este arquivo: $1)',
	'wah-transcode-working' => 'Este vídeo está sendo processado, por favor tente novamente mais tarde',
	'wah-transcode-helpout' => 'Este clipe está $1 porcento completo.
Você pode ajudar a transcodificar este vídeo visitando [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Falha na transcodificação do arquivo',
	'wah-javascript-off' => 'Você precisa ter habilitado JavaScript para participar de Wiki@Home',
	'wah-loading' => 'carregando interface Wiki@Home <blink>...</blink>',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 */
$messages['ro'] = array(
	'wah-short-audio' => 'Fişier audio $1, $2',
	'wah-short-video' => 'Fişier video $1, $2',
	'wah-short-general' => 'Fişier media $1, $2',
	'wah-long-audio' => '(fişier sunet $1, lungime $2, $3)',
	'wah-long-video' => '(fişier video $1, lungime $2, $4×$5 pixeli, $3)',
	'wah-long-general' => '(fişier media, lungime $2, $3)',
	'wah-transcode-working' => 'Acest video este procesat, încercaţi mai târziu',
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
	'wah-transcode-working' => 'Это видео сейчас перекодируется, пожалуйста, обратитесь позднее',
	'wah-transcode-helpout' => 'Ролик выполнен на $1 процентов. Вы можете помочь перекодировать это видео, посетите [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Не удалось перекодировать этот файл.',
	'wah-javascript-off' => 'У вас должен быть включён JavaScript, для возможности участия в Wiki@Home',
	'wah-loading' => 'Загрузка интерфейса Wiki@Home <blink>...</blink>',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'wah-desc' => 'Umožňuje šírenie úloh prekódovania videa klientom pomocou firefogg',
	'wah-user-desc' => 'Wiki@Home umožnuje členom komunity venovať nevyužitý výpočtový čas procesora na pomoc pri operáciách náročných na zdroje',
	'wah-short-audio' => '$1 zvukový súbor, $2',
	'wah-short-video' => '$1 videosúbor, $2',
	'wah-short-general' => '$1 multimediálny súbor, $2',
	'wah-long-audio' => '($1 zvukový súbor, dĺžka $2, $3)',
	'wah-long-video' => '($1 videosúbor, dĺžka $2, $4×$5 pixlov, $3)',
	'wah-long-multiplexed' => '(multiplexovaný zvukový/videosúbor, $1, dĺžka $2, $4×$5 pixlov, $3 celkom)',
	'wah-long-general' => '(multimediálny súbor, dĺžka $2, $3)',
	'wah-long-error' => '(ffmpeg nedokázal načítať nasledovný súbor: $1)',
	'wah-transcode-working' => 'Toto video sa spracováva, skúste to prosím neskôr',
	'wah-transcode-helpout' => 'Klip je hotový na $1 percent.
Môžete pomôcť s prekódovaním tohto videa po navštívení [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Tento súbor sa nepodarilo prekódovať.',
	'wah-javascript-off' => 'Aby ste sa mohli zúčastniť Wiki@Home musíte mať zapnutý JavaScript',
	'wah-loading' => 'načítava sa rozhranie Wiki@Home <blink>...</blink>',
);

/** Serbian Cyrillic ekavian (Српски (ћирилица))
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

/** Serbian Latin ekavian (Srpski (latinica))
 * @author Michaello
 */
$messages['sr-el'] = array(
	'wah-short-audio' => '$1 zvučni fajl, $2',
	'wah-short-video' => '$1 video-fajl, $2',
	'wah-short-general' => '$1 medija-fajl, $2',
	'wah-long-audio' => '($1 zvučni fajl, trajanje $2, $3)',
	'wah-long-video' => '($1 video-fajl, trajanje $2, $3×$5 piksela, $3)',
	'wah-long-multiplexed' => '(multipleksovani audio/video fajl, $1, trajanje $2, $4×$5 piksela, $3 ukupno)',
	'wah-long-general' => '(medija-fajl, trajanje $2, $3)',
	'wah-long-error' => '(ffmpeg nije mogao da pročita ovaj fajl: $1)',
	'wah-javascript-off' => 'Morate omogućiti JavaScript, da biste učestvovali u Wiki@Home',
	'wah-loading' => 'učitavanje Wiki@Home interfejsa <blink>...</blink>',
);

/** Swedish (Svenska)
 * @author Fluff
 */
$messages['sv'] = array(
	'wah-short-audio' => '$1-ljudfil, $2',
	'wah-short-video' => '$1-videofil, $2',
	'wah-short-general' => '$1-mediafil, $2',
	'wah-long-audio' => '($1-ljudfil, längd $2, $3)',
	'wah-long-video' => '($1-videofil, längd $2, $4×$5 pixlar, $3)',
	'wah-long-multiplexed' => '(multiplexad ljud-/video-fil, $1, längd $2, $4×$5 pixlar, $3 totalt)',
	'wah-long-general' => '(mediafil, längd $2, $3)',
	'wah-long-error' => '(ffmpeg kunde inte läsa filen: $1)',
	'wah-transcode-working' => 'Videon bearbetas just ju, vänligen försök igen senare',
	'wah-transcode-helpout' => 'Filen är $1 procent klar. 
Du kan hjälpa till att konvertera den här videon genom att besöka [[Special:WikiAtHome|Wiki@Home]]',
	'wah-transcode-fail' => 'Kunde inte konvertera den här filen.',
	'wah-javascript-off' => 'Du måste ha JavaScript aktiverat för att delta i Wiki@Home',
	'wah-loading' => 'laddar gränssnittet för Wiki@Home <blink>...</blink>',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Liangent
 */
$messages['zh-hans'] = array(
	'wah-short-audio' => '$1声音文件，$2',
	'wah-short-video' => '$1视频文件，$2',
	'wah-short-general' => '$1媒体文件，$2',
	'wah-long-audio' => '（$1声音文件，长度$2，$3）',
	'wah-long-video' => '（$1视频文件，长度$2，$4×$5像素，$3）',
	'wah-long-general' => '（媒体文件，长度$2，$3）',
	'wah-long-error' => '（ffmpeg不能读取这个文件：$1）',
	'wah-transcode-working' => '这个视频正在被处理，请稍后再试',
	'wah-transcode-fail' => '这个文件转码失败',
	'wah-javascript-off' => '你必须启用JavaScript以参与Wiki@Home',
	'wah-loading' => '正在载入Wiki@Home界面<blink>……</blink>',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Wrightbus
 */
$messages['zh-hant'] = array(
	'wah-transcode-working' => '本影片正在處理中，請稍後再試',
	'wah-loading' => '正在載入Wiki@Home介面<blink>...</blink>',
);

