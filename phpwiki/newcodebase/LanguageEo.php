<?

# See language.doc

# The names of the namespaces can be set here, but the numbers
# are magical, so don't change or move them!  The Namespace class
# encapsulates some of the magic-ness.
#
/* private */ $wgNamespaceNamesEo = array(
	-1	=> "Speciala", # FIXME Is it safe to change this?
	0	=> "",
	1	=> "Diskuto", # au diskutu?
	2	=> "Vikipediisto",
	3	=> "Vikipediista_diskuto",
	4	=> "Vikipedio",
	5	=> "Vikipedia_diskuto",
	6	=> "Dosiero", #FIXME: Check the magic for Image: and Media:
	7	=> "Dosiera_diskuto"
);

# Heredu apriorajn preferojn: wgDefaultUserOptionsEn

/* private */ $wgQuickbarSettingsEo = array(
	"Nenia", "Fiksiĝas maldesktre", "Fiksiĝas dekstre", "Ŝvebas maldekstre"
);

/* private */ $wgSkinNamesEo = array(
	"Norma", "Nostalĝio", "Kolonja Bluo"
);

/* private */ $wgUserTogglesEo = array(
	"hover"		=> "Montru ŝpruchelpilon super viki-ligiloj",
	"underline" => "Substreku ligilojn",
	"highlightbroken" => "Ruĝigu ligilojn al neekzistantajn paĝojn",
	"justify"	=> "Alkadrigu liniojn",
	"hideminor" => "Kasu malgrandajn redaktetojn ce <i>Lastaj Sangoj</i>",
	"numberheadings" => "Aŭtomate nombrigu sekciojn",
	"rememberpassword" => "Rememoru mian pasvorton de seanco al seanco",
	"editwidth" => "Redaktilo estu plenlarĝa",
	"editondblclick" => "Redaktu per duobla alklako (JavaScript)",
	"watchdefault" => "Priatentu paĝojn de vi redaktintajn",
	"altencoding" => "Montru supersignojn per X-sistemo"
);

# Se eble, trovu Esperantajn libroservoj traserĉeblaj laŭ ISBN
# $wgBookstoreListEo = ..

# Tie, kie eble, uzu la memnomo de la lingvo
# El listo de Landoj kaj Lingvoj ce http://www.bertilow.com/lanlin/isoling.php
/* private */ $wgLanguageNamesEo = array(
	"ab"	=> "Abĥaza",
	"aa"	=> "Afara",
    "af"    => "Afrikansa",
    "ay"    => "Ajmara",
    "tw"    => "Akana",
	"sq"	=> "Albana",
	"am"	=> "Amhara",
    "en"    => "Angla",
	"w"	=> "Angla", # FIXME - Cu guste en la listo?
	"simple" => "Simpligita angla",
	"ar"	=> "Araba",
	"hy"	=> "Armena",
	"as"	=> "Asama",
	"az"	=> "Azerbajĝana",
	"ba"	=> "Baŝkira",
	"be"	=> "Belorusa",
	"bn"	=> "Bengala",
	"bh"	=> "Bihara", #FIXME - Bogpura, Majtila, Magaha
    "my"    => "Birma",
	"bi"	=> "Bislamo",
    "rn"    => "Burunda",
    "ts"    => "Conga",
    "tn"    => "Cvana",
	"zh"	=> "Ĉina",
	"cs"	=> "Ĉeĥa",
	"da"	=> "Dana", # Nepre uzu "da" por la lingvo
	"dk"	=> "Dana",
    "dz"    => "Dzonko",
    "ik"    => "Eskima", #FIXME - Cu?
	"eo"	=> "Esperanto",
	"et"	=> "Estona",
    "eu"    => "Eŭska",
	"fo"	=> "Feroa",
	"fj"	=> "Fiĝia",
    "tl"    => "Filipina",
	"fi"	=> "Finna", # FIXME - Cu suoma?
	"fr"	=> "Franca",
	"fy"	=> "Frisa",
	"gl"	=> "Galega",
	"de"	=> "Germana",
	"el"	=> "Greka",
	"kl"	=> "Gronlanda", # FIXME Cu?
	"gu"	=> "Guĝarata",
    "gn"    => "Gvarania",
    "za"    => "Ĝuanga",
	"ha"	=> "Haŭsa",
	"he"	=> "Hebrea",
	"hi"	=> "Hinda", #FIXME Cu?
    "es"    => "Hispana",
	"hu"	=> "Hungara",
	"id"	=> "Indonezia",
	"ia"	=> "Interlingvao", #FIXME - Cu interlingua?
	"iu"	=> "Inuita", #FIXME - Cu?
    "ga"    => "Irlanda",
    "is"    => "Islanda",
	"it"	=> "Itala",
	"ja"	=> "Japana",
	"jv"	=> "Java", #FIXME - Cu kodo "jw"?
    "yi"    => "Jida",
	"yo"	=> "Joruba",
	"kn"	=> "Kanara",
    "ka"    => "Kartvela",
	"ks"	=> "Kaŝmira",
    "ca"    => "Kataluna",
	"kk"	=> "Kazaĥa",
    "qu"    => "Keĉua",
    "cy"    => "Kimra",
    "ky"    => "Kirgiza",
    "km"    => "Kmera",
    "ko"    => "Korea",
	"co"	=> "Korsika",
	"hr"	=> "Kroata",
    "xh"    => "Ksosa",
	"la"	=> "Latino",
	"lv"	=> "Latva",
    "lo"    => "Laŭa",
	"ln"	=> "Lingala",
	"lt"	=> "Litova",
	"mk"	=> "Makedona",
	"mg"	=> "Malagasa",
	"ms"	=> "Malaja",
	"ml"	=> "Malajalama",
	"mi"	=> "Maoria",
	"mr"	=> "Marata",
	"mo"	=> "Moldava", # " =ro rumana"
	"mn"	=> "Mongola",
	"na"	=> "Nauru",
    "nl"    => "Nederlanda",
	"ne"	=> "Nepala",
	"no"	=> "Norvega",
	"oc"	=> "Okcitana",
	"or"	=> "Orijo",
	"om"	=> "Oroma",
    "pa"    => "Panĥaba",
	"ps"	=> "Paŝtua",
	"fa"	=> "Persa",
	"pl"	=> "Pola",
	"pt"	=> "Portugala",
	"rm"	=> "Romanĉa",
    "rw"    => "Ruanda",
	"ro"	=> "Rumana",
	"ru"	=> "Rusa",
	"sm"	=> "Samoa",
	"sg"	=> "Sangoa",
	"sa"	=> "Sanskrito",
	"sr"	=> "Serba",
	"sh"	=> "Serbo-Kroata",
	"st"	=> "Sota",
	"sn"	=> "Ŝona",
	"sd"	=> "Sinda",
	"si"	=> "Sinhala",
	"ss"	=> "Svazia",
	"sk"	=> "Slovaka",
	"sl"	=> "Slovena",
	"so"	=> "Somala",
	"su"	=> "Sunda",
	"sw"	=> "Svahila",
	"sv"	=> "Sveda",
	"tg"	=> "Taĝika",
    "th"    => "Taja",
	"ta"	=> "Tamila",
	"tt"	=> "Tatara",
	"te"	=> "Telugua",
	"bo"	=> "Tibeta",
	"ti"	=> "Tigraja",
	"to"	=> "Tongaa",
	"tr"	=> "Turka",
	"tk"	=> "Turkmena",
	"ug"	=> "Ujgura",
	"uk"	=> "Ukrajna",
	"ur"	=> "Urduo",
	"uz"	=> "Uzbeka",
	"vi"	=> "Vjetnama",
	"vo"	=> "Volapuko",
	"wo"	=> "Volofa",
	"zu"	=> "Zulua"
);

# Note capitalization; also some uses may require addition of final -n
/* private */ $wgWeekdayNamesEo = array(
	"dimanĉo", "lundo", "mardo", "merkredo" , "ĵaŭdo",
	"vendredo", "sabato"
);

# Double-check capitalization
/* private */ $wgMonthNamesEo = array(
	"januaro", "februaro", "marto", "aprilo", "majo", "junio",
	"julio", "aŭgusto", "septembro", "oktobro", "novembro",
	"decembro"
);

# Hmm
/* private */ $wgMonthAbbreviationsEo = array(
	"Jan", "Feb", "Mar", "Apr", "Maj", "Jun", "Jul", "Aŭg",
	"Sep", "Okt", "Nov", "Dec"
);

# All special pages have to be listed here: a description of ""
# will make them not show up on the "Special Pages" page, which
# is the right thing for some of them (such as the "targeted" ones).
#
# *Ne ŝanĝu* la nomojn en la maldekstra kolumno, ili estas internaj
# nomoj de programfunkcioj. La dekstra kolumno enhavas kelkajn
# malplenaĵojn; ili restu tiel, por ke tiuj funkcioj ne listiĝu
# en la listo da specialaj paĝoj.
/* private */ $wgValidSpecialPagesEo = array(
	"Userlogin"		=> "",
	"Userlogout"	=> "",
	"Preferences"	=> "Ŝanĝu miajn preferojn",
	"Watchlist"		=> "Mian atentaron", # Listo de paĝoj, kiujn la uzulo elektis por atenti
	"Recentchanges" => "Lastaj ŝanĝoj al paĝoj",
	"Upload"		=> "Alŝutu bildojn kaj dosierojn",
	"Imagelist"		=> "Alŝutitaj dosieroj",
	"Listusers"		=> "Enskribitaj uzuloj",
	"Statistics"	=> "Statistiko pri la paĝaro",
	"Randompage"	=> "Hazarda paĝo",

	"Lonelypages"	=> "Paĝoj orfaj",
	"Unusedimages"	=> "Bildoj orfaj",
	"Popularpages"	=> "Plej vizitataj paĝoj",
	"Wantedpages"	=> "Plej alligitaj paĝoj",
	"Shortpages"	=> "Mallongaj artikoloj",
	"Longpages"		=> "Longegaj artikoloj",
	"Newpages"		=> "Novkreitaj artikoloj",
	"Allpages"		=> "Ĉiu paĝo laŭ titolo",

	"Ipblocklist"	=> "Forbaritaj IP-adresoj",
    "Maintenance" => "Ripariloj kaj zorgiloj", # angle "Maintenance page"
	"Specialpages"  => "",
	"Contributions" => "",
    "Emailuser"     => "",
	"Whatlinkshere" => "",
	"Recentchangeslinked" => "",
	"Movepage"		=> "",
	"Booksources"	=> "Eksteraj libroservoj"
);

/* private */ $wgSysopSpecialPagesEo = array(
	"Blockip"		=> "Forbaru fiIP-adreson",
	"Asksql"		=> "Informmendu je la datumbazo",
	"Undelete"		=> "Restarigu forigitan paĝon"
);

# FIXME
/* private */ $wgDeveloperSpecialPagesEo = array(
	"Lockdb"		=> "Forŝlosi datumaron",
	"Unlockdb"		=> "Repermesu ŝanĝon al datumaro",
	"Debug"			=> "Montru servilinformon"
);

/* private */ $wgAllMessagesEo = array(

# Teksteroj uzataj far diversaj paĝoj:
#
# Tiuj literoj, kiuj aperu kiel parto de la ligilo en formo "[[lingvo]]jn" ktp:
"linktrail"     => "/^([a-z]+)(.*)\$/sD",
"mainpage"		=> "Ĉefpaĝo",
"about"			=> "Enkonduko",
"aboutwikipedia" => "Pri Vikipedio", #FIXME
"aboutpage"		=> "Vikipedio:Enkonduko",
"help"			=> "Helpo",
"helppage"		=> "Vikipedio:Helpo",
"wikititlesuffix" => "Vikipedio",
"bugreports"	=> "Raportu cimojn",
"bugreportspage" => "Vikipedio:Raportu_cimojn",
"faq"			=> "Oftaj demandoj",
"faqpage"		=> "Vikipedio:Oftaj demandoj",
"edithelp"		=> "Helpo pri redaktado",
"edithelppage"	=> "Vikipedio:Kiel_redakti_paĝon", #FIXME: Kontrolu
"cancel"		=> "Rezignu",
"qbfind"		=> "Serĉu",
"qbbrowse"		=> "Foliumado", # FIXME
"qbedit"		=> "Redaktado", #FIXME
"qbpageoptions" => "Paĝagado", #FIXME
"qbpageinfo"	=> "Paĝinform", #FIXME
"qbmyoptions"	=> "Personaĵoj", #FIXME
"mypage"		=> "Mia paĝo", #FIXME
"mytalk"        => "Mia diskuto",
"currentevents" => "Aktualaĵoj", #FIXME - Novaĵoj? Aktualaj novaĵoj? Aktualaj eventoj?
"errorpagetitle" => "Eraro", #FIXME - Arero? ;)
"returnto"		=> "Revenu al $1.",
"fromwikipedia"	=> "El Vikipedio, la libera enciklopedio.",
"whatlinkshere"	=> "Ligantoj ĉi tien", #FIXME: ĉu ligantaj?
"help"			=> "Helpo",
"search"		=> "Serĉu",
"history"		=> "Historio", #FIXME
"printableversion" => "Presebla versio", # FIXME: ĉu printebla?
"editthispage"	=> "Redaktu la paĝon",
"deletethispage" => "Forigu la paĝon",
"protectthispage" => "Protektu la paĝon", #FIXME: Ĉu 'gardu'
"unprotectthispage" => "Malprotektu la paĝon", #FIXME: ĉu 'malgardu', 'ne plu', ktp?
"talkpage"		=> "Diskuta paĝo",
"subjectpage"	=> "Subjekta paĝo", #FIXME: ?
"otherlanguages" => "Aliaj lingvoj",
"redirectedfrom" => "(Alidirektita el $1)",
"lastmodified"	=> "La paĝo laste redaktiĝis je $1.",
"viewcount"		=> "La paĝo montriĝis $1-foje.",
"printsubtitle" => "(El http://eo.wikipedia.org)",
"protectedpage" => "Protektita paĝo", #FIXME: ĉu "gardita" ktp?
"administrators" => "Vikipedio:Sistemestroj", # FIXME?
"sysoptitle"	=> "Sistemestro konto nepras",
"sysoptext"		=> "La ago kiun vi petis fari estas
farebla nur de uzuloj agnoskitaj kiel \"sistemestroj\".
Bonvolu legi $1.", #FIXME
"developertitle" => "Sistemestra konto nepras",
"developertext"	=> "La ago kiun vi petis fari estas
farebla nur de programistoj de la projekto por eviti neintencitan difektiĝon al la datumaro.
Bonvolu legi $1.",
"nbytes"		=> "$1 bitokoj",
"go"			=> "Ek!", #FIXME
"ok"			=> "Ek", #FIXME
"sitetitle"		=> "Vikipedio", # Wikipedia
"sitesubtitle"	=> "Vikipedio: La Libera Enciklopedio",
"retrievedfrom" => "Citita el \"$1\"", #FIXME: Aperas post presita paĝo

# Main script and global functions
#
"nosuchaction"	=> "Nenia tia ago",
"nosuchactiontext" => "La agon ('action') nomitan de la URL
ne agnoskas la programaro de Vikipedio",
"nosuchspecialpage" => "Nenia tia speciala paĝo",
"nospecialpagetext" => "Vi petis specialan paĝon kiun
ne agnoskas la programaro de Vikipedio",

# General errors
#
"error"         => "Fuŝo",
"databaseerror" => "Datumbaza fuŝo",
"dberrortext"	=> "Sintaksfuŝo okazis en informpeto kontraŭ la datumaro.
Eble kaŭzis tion malpermesita serĉomendo (vidu je $5),
aŭ eble tio indikas cimon ĉe la programaro.
Jen la plej laste provita informmendo:
<blockquote><tt>$1</tt></blockquote>
el la funkcio \"<tt>$2</tt>\". 
MySQL redonis eraron  \"<tt>$3: $4</tt>\".",
"noconnect"		=> "Neeblis konekti al la datumbazo je $1",
"nodb"			=> "Neeblis elekti datumaron $1",
"readonly"		=> "Datumaro ŝlosita, nurlega",
"enterlockreason" => "Bonvolu klarigi, kial oni ŝlosas la datumaron, kaj
estimata tempo de malŝlosado.",
"readonlytext"	=> "La datumaro de Vikipedio estas nun ŝlosita kontraŭ
novajn aldonajn kaj aliaj ŝanĝoj, probable pro laŭkutima flegado je la datumaro.
Bonvolu reprovu post iom da tempo.
La ŝlosinto lasis la jenan mesaĝon:
<p>$1\n",
"missingarticle" => "La datumbazo ne trovis la tekson de
artikolo, kiun ĝi devus trovi, nomita \"$1\".
Ĉi tio ne estas eraro de la datumbazo, sed probable cimo en la programo.
Bonvolu raporti ĉi tion al iu sistemestro, kaj rimarkigi la URL.",
"internalerror" => "Interna fuŝo",
"filecopyerror" => "Neeblis kopii dosieron  \"$1\" al \"$2\".",
"filerenameerror" => "Neeblis alinomi dosieron \"$1\" al \"$2\".",
"filedeleteerror" => "Neeblis forigi dosieron \"$1\".",
"filenotfound"	=> "Neeblis trovi dosieron \"$1\".",
"unexpected"	=> "Neatendita valuto: \"$1\"=\"$2\".",
"formerror"		=> "Fuŝo: neeblis liveri formulon",	
"badarticleerror" => "Tiu ago ne povas apliki je tiu artikolo.",
"cannotdelete"  => "Neeblis forigi la elektitan paĝon aŭ dosieron.",
"badtitle"		=> "Nevalida titolo",
"badtitletext"	=> "La petita paĝtitolo estis nevalida, malplena, aŭ
malĝuste ligita interlingva aŭ intervikia titolo.",

# Login and logout pages
#
"logouttitle"	=> "Adiaŭ!",
"logouttext"	=> "Vi adiaŭis kaj finis vian seancon.
Vi rajtas daŭre Vikipediumi sennome, aŭ vi povas resaluti
kiel la sama aŭ iu alia uzulo.\n", #FIXME

"welcomecreation" => "<h2>Bonvenon, $1!</h2> Via konto estas kreita.
<font color=\"red\">Ne forgesu proprigi viajn Vikipedio-preferojn!</font>",

"loginpagetitle" => "Salutu / enskribu", #FIXME
"yourname"		=> "Via kontonomo", #FIXME ĉu kaŝnomo ĉu uzantonomo ĉu kontonomo ktp?
"yourpassword"	=> "Via pasvorto",
"yourpasswordagain" => "Retajpu pasvorton",
"newusersonly"	=> " (nur novaj uzuloj)",
"remembermypassword" => "Rememoru mian pasvorton de seanco al seanco.",
"loginproblem"	=> "<b>Okazis problemon pri via salutado.</b><br>Bonvolu reprovi!",
"alreadyloggedin" => "<font color=\"red\"><b>Uzulo $1, via estas jam salutinta!</b></font><br>\n",

"areyounew"		=> "Se vi estas nove ĉe vikipedio kaj volas akiri uzulokonton,
bonvolu tajpu salutonomon, pasvorton, kaj refoje pasvorton.
Laŭvole vi rajtas enmeti vian retpoŝtadreson por ke ni povu
sendu al vi novan pasvorton se vi perdas la nunan.<br>\n",

"login"			=> "Salutu", #FIXME, what exactly do the following go to?
"userlogin"		=> "Salutu",
"logout"		=> "Adiaŭu",
"userlogout"	=> "Adiaŭu",
"createaccount"	=> "Kreu novan konton",
"badretype"		=> "La pasvortoj kiujn vi tapjis ne egalas.",
"userexists"	=> "Jam estas uzulo kun la nomo de vi elektita. Bonvolu elektu alian nomon.",
"youremail"		=> "Via retpoŝtadreso",
"yournick"		=> "Via ĉjonomo (por subskriboj)", #FIXME - ĉu kaŝnomo, plumnomo, ktp?
"emailforlost"	=> "Se vi forgesos vian pasvorton, vi povas peti ke novan estu retpoŝtita al via adreso.",
"loginerror"	=> "Salutada fuŝo", #FIXME
"noname"		=> "Vi nepre indiku kontonomo.",
"loginsuccesstitle" => "Salutado sukcesis",
"loginsuccess"	=> "Vi nun estas salutinta en la Vikipedion kiel uzulo \"$1\".",
"nosuchuser"	=> "Neniu uzulo nomiĝas \"$1\".
Bonvlu kontroli ĉu vi ĝuste literumadis, aŭ kreu novan konton per la ĉi-sube formulo.",
"wrongpassword"	=> "La pasvorto de vi entajpita malĝustas. Bonvolu provi denove.",
"mailmypassword" => "Retpoŝtu al mi novan pasvorton",
"passwordremindertitle" => "Rememorigo el Vikipedio pri perdita pasvorto", #FIXME
"passwordremindertext" => "Oni (probable vi, el IP-adreso $1)
petis, ke ni sendu al vi novan pasvorton por saluti al Vikipedio.
La pasvorto por uzulo \"$2\" nun estas \"$3\".
Ni rekomendas, ke vi nun salutu kaj ŝanĝu la pasvorton laŭprefere.", #FIXME
"noemail"		=> "Retpoŝtadreso ne estas registrita por uzulo \"$1\".",
"passwordsent"	=> "Oni sendis novan pasvorton al la retpoŝtadreso
registrita por \"$1\".
Bonvolu saluti denove post vi ricevos ĝin.",

# Edit pages
#
"summary"		=> "Resumo",
"minoredit"		=> "Ĉi tiu ŝanĝo estas redakteto",
"watchthis"		=> "Priatentu la paĝon",
"savearticle"	=> "Konservu ŝanĝojn",
"preview"		=> "Antaŭrigardu",
"showpreview"	=> "Montru antaŭrigardon", #FIXME eh?
"blockedtitle"	=> "Uzulo forbarita", #FIXME ĉu 'Konto forbarita'?
"blockedtext"	=> "Via konto aŭ IP-adreso estis forbarita fare de $1,
kiu priskribis la kialon jene:<br>$2<p>Vi rajtas kontakti tiun sistemestron
por pridiskuti la forbaradon.", #FIXME - sistemestro?
"newarticle"	=> "(Nova)",
"newarticletext" => "Enmetu tekston por la nova artikolo ĉi tien.", #FIXME?
"noarticletext" => "(La artikolo nun ne enhavas tekston)", #FIXME
"updated"		=> "(Ŝanĝo registrita)", #FIXME: ?
"note"			=> "<strong>Noto:</strong> ", #FIXME: Where does this come from?
"previewnote"	=> "Memoru, ke ci tiu estas nur antaurigardo kaj ne jam konservita!",
"previewconflict" => "La jena antaŭrigardo montras la tekston el la supra tekstujo,
kiel ĝi aperos se vi elektas konservi la paĝon.", #FIXME
"editing"		=> "Redaktas vi $1",
"editconflict"	=> "Redakta konflikto: $1",
"explainconflict" => "Iu ŝanĝis la paĝon post kiam vi ekskribis.
La supra tekstujo enhavas la aktualan tekston de la artikolo.
Viaj ŝanĝoj enestas la malsupran tekstujon.
Vi devas mem kunfandigi viajn ŝanĝojn kaj la jaman tekston.
<b>Nur</b> la teksto en la supra tekstujo estos konservita kiam
vi alpremas \"Konservu\".\n<p>" , #FIXME - double-check that this makes sense
"yourtext"		=> "Via teksto",
"storedversion" => "Registrita versio",
"editingold"	=> "<strong>AVERTO: Vi nun redaktas malnovan revizion de tiu ĉi artikolo.
Se vi konservos ĝin, ĉiu ŝanĝo farita post tiu revizio perdiĝos.</strong>\n",
"yourdiff"		=> "Malsamoj",
"copyrightwarning" => "Bonvolu noti, ke ĉiu kontribuaĵo al la Vikipedio
estu konsiderata eldonita laŭ la <i>GNU Free Documentation License</i> (vidu je $1).
Se vi volas, ke via verkaĵo ne estu redaktota senkompate kaj disvastigota
laŭvole, ne alklaku \"Konservu\".
Vi ankaŭ ĵuras, ke vi mem verkis ĉi tiun, aŭ ke vi kopiis ĝin el
fonto senkopirajta. <strong>NE UZU KOPIRAJTITAJN VERKOJN SENPERMESE!</strong>",


# History pages
#
"revhistory"	=> "Historio de redaktoj",
"nohistory"		=> "Ne estas historio de redaktoj pri ĉi tiu paĝo.", #FIXME
"revnotfound"	=> "Ne trovis revizion", #fixme
"revnotfoundtext" => "Ne povis trovi la malnovan revizion kiun vi mendis.
Bonvolu kontroli la URL kiun vi uzis por atingi la paĝon.\b",
"loadhist"		=> "Ŝarĝas redaktohistorion", #FIXME Apparently not used
"currentrev"	=> "Aktuala revizio", #FIXME ĉu "plej lasta"?
"revisionasof"	=> "Kiel regisitrite je $1",
"cur"			=> "nun",
"next"			=> "sekv",
"last"			=> "ant",
"orig"			=> "orig",
"histlegend"	=> "Klarigo: (nun) = vidu malsamojn kontraŭ la nuna versio,
(ant) = malsamojn kontraŭ la antaŭa versio, M = malgranda redakteto",

# Diffs
#
"difference"	=> "(Malsamoj inter revizioj)",
"loadingrev"	=> "ŝarĝas revizion por malsamoj", #FIXME Apparently not used
"lineno"		=> "Linio $1:",
"editcurrent"	=> "Redaktu la nunan version de la paĝo",

# Search results
#
"searchresults" => "Serĉrezulto",
"searchhelppage" => "Vikipedio:Serĉado",
"searchingwikipedia" => "Serĉado ĉe Vikipedio",
"searchresulttext" => "Por helpo pri kiel ekife serĉi tra la vikion, legu je $1.",
"searchquery"	=> "Serĉmendo \"$1\"",
"badquery"		=> "Misformita serĉmendo",
"badquerytext"	=> "Via serĉmendo ne estis plenumebla.
Eble tion kaŭzis, ke vi provis serĉi vorton malpli longa ol tri literoj,
kion ne jam povas fari la programo. Ankaŭ eblas, ke vi mistajpis la
esprimon".
#", ekzemple \"fiŝoj kaj kaj skaloj\"".   # FIXME ? eblas
". Bonvolu reserĉi per alia mendo.",
"matchtotals"	=> "La serĉmendo \"$1\" liveris $2 artikolojn laŭ titolo
kaj $3 artikolojn laŭ enhavo.",
"titlematches"	=> "Trovitoj laŭ titolo",
"notitlematches" => "Neniu trovito laŭ titolo",
"textmatches"	=> "Trovitoj laŭ enhavo",
"notextmatches"	=> "Neniu trovito laŭ enhavo",
"prevn"			=> "$1 antaŭajn",
"nextn"			=> "$1 sekvajn",
"viewprevnext"	=> "Montru ($1) ($2) ($3).",
"showingresults" => "Montras <b>$1</b> trovitojn ekde la <b>$2</b>-a.",
"nonefound"		=> "<strong>Noto</strong>: malsukcesan serĉon ofte
kaŭzas pro serĉi tro oftajn vortojn, kiujn ne enhavas la indekso,
aŭ pro peti tro da serĉvortojn (nur paĝoj kiuj enhavas ĉiun serĉvorto
montriĝos en la rezulto).",
"powersearch" => "Serĉu",
"powersearchtext" => "
Serĉu en sekcioj: :<br>
$1<br>
$2 Kun alidirektiloj &nbsp; Serĉu $3 $9",

# Preferences page
#
"preferences"	=> "Preferoj",
"prefsnologin" => "Ne jam salutis!",
"prefsnologintext"	=> "Vi nepre <a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">salutu</a>
antaŭ ol eblas ŝanĝo viajn preferojn.",
"prefslogintext" => "Vi estas salutinta kiel \"$1\".
Via interna identonombro estas $2.",
"prefsreset"	=> "Preferoj reprenita el la registro.", #FIXME: Hmm...
"qbsettings"	=> "Preferoj pri ilobreto", 
"changepassword" => "Ŝanĝu pasvorton",
"skin"			=> "Etoso",
"saveprefs"		=> "Konservu preferojn",
"resetprefs"	=> "Restarigi antaŭajn preferojn",
"oldpassword"	=> "Malnova pasvorto",
"newpassword"	=> "Nova pasvorto",
"retypenew"		=> "Rejaptu novan pasvorton",
"textboxsize"	=> "Grando de redakta tekstujo",
"rows"			=> "Linioj",
"columns"		=> "Kolumnoj",
"searchresultshead" => "Agordaĵoj pri serĉorezulto",
"resultsperpage" => "Montru trovitojn po",
"contextlines"	=> "Montru liniojn el paĝoj po",
"contextchars"	=> "Montru literojn el linioj ĝis po",
"stubthreshold" => "Indiku paĝojn malpli grandajn ol",
"recentchangescount" => "Montru kiom da titoloj en Lastaj Ŝanĝoj",
"savedprefs"	=> "Viaj preferoj estas registritaj.",
"timezonetext"	=> "Indiku, per kiom da horoj via
loka horzono malsamas je tiu de la servilo (UTC).
Ekzemple, por la Centra Eŭropa Horzono, indiku \"1\" vintre aŭ \"2\" dum somertempo.",
"localtime"	=> "Loka horzono",
"timezoneoffset" => "Malsamo", #FIXME (?)
"emailflag"     => "Malakceptu retpoŝtojn el aliaj vikipediistoj",

# Recent changes
#
"recentchanges" => "Lastaj ŝanĝoj",
"recentchangestext" => "Sekvu la plej lastaj ŝanĝoj al Vikipedio per ĉi tiu paĝo.
[[Bonvenon, novuloj]]!
Bonvolu legi ĉi tiuj paĝojn: [[vikipedio:Oftaj demandoj|Oftaj demandoj]],
[[vikipedio:Konsiletoj|Konsiletoj]]
(aparte [[vikipedio:Nomoj de titoloj|Nomoj de titoloj]]
kaj [[vikipedio:Neŭtrala vidpunkto|Neŭtrala vidpunkto]]),
kaj [[vikipedio:Oftaj eraroj|Oftaj eraroj ĉe Vikipedio]].

Se vi volas, ke la Vikipedio sukcesu, tre gravas ke vi ne aldonu
aferojn limigitaj laŭ la [[kopirajto]] de aliuloj.
La leĝa respondeco vere povus malhelpegi la projekton,
do bonvolu ne fari tion.

Ankaŭ vidu la [http://meta.wikipedia.org/wiki/Special:Recentchanges lastatempan pri-vikipedian diskuton]
(plurlingve)",
"rcloaderr"		=> "Ŝarĝas lastajn ŝanĝojn",
"rcnote"		=> "Jen la plej lastaj <b>$1</b> ŝanĝoj dum la lastaj <b>$2</b> tagoj.",
"rclinks"		=> "Montru $1 lastajn ŝanĝojn; montru la ŝanĝojn dum la $2 lastajn tagojn.",
"diff"			=> "malsamoj",
"hist"			=> "historio",
"hide"			=> "kaŝu",
"show"			=> "montru",
"tableform"		=> "tabelo",
"listform"		=> "listo",
"nchanges"		=> "$1 ŝanĝoj",
"minoreditletter" => "M",
"newpageletter" => "N",

# Upload
#
"upload"		=> "Alŝutu",
"uploadbtn"		=> "Alŝutu dosieron",
"uploadlink"	=> "Alŝutu dosieron", # Ĉu neuzata?
"reupload"		=> "Realŝutu",
"reuploaddesc"	=> "Revenu al la alŝuta formulo.",
"uploadnologin" => "Ne salutinta",
"uploadnologintext"	=> "Vi nepre havu konton kaj <a href=\"" .
  wfLocalUrl( "Speciala:Userlogin" ) . "\">salutu</a>
por alŝuti dosierojn.",
"uploadfile"	=> "Alŝutu dosieron",
"uploaderror"	=> "Fuŝo okazis dum alŝuto",
"uploadtext"	=> "Por okulumi aŭ serĉi jam alŝutitajn dosierojn,
aliru la <a href=\"" . wfLocalUrl( "Special:Imagelist" ) .
"\">listo de alŝutaĵoj</a>.
Ĉiu alŝuto kaj forigo estas registrata en la <a href=\"" .
wfLocalUrl( "Vikipedio:Loglibro de alŝutaĵoj" ) ."\">alŝuta loglibro</a>.</p>

<p>Uzu ĉi tiun formulon por alŝuti novajn bildojn kaj aliajn dosierojn
por ilustrado de viaj artikoloj.
Ĉe kutimaj retumiloj, vi vidos ĉi-sube butonon \"Foliumi...\" aŭ simile;
tiu malfermas la dosierelektilo de via operaciumo.
Kiam vi elektos dosieron, ĝia nomo plenigos la tekstujon apud la butono.
Vi ankaŭ nepre devas klakjesi la skatolon por aserti, ke vi ne
malobeas la leĝan kopirajton de aliuloj per alŝuto de la dosiero.
Por realigi la alŝutadon, alklaku la butono \"Alŝutu\".
Tio ĉi eble iomete longdaŭros, se ĝi estas granda dosiero kaj via interret-konekto malrapidas.</p>

<p>La dosiertipoj preferataj ĉe Vikipedio estas JPEG por fotografoj,
PNG por grafeo, diagramoj, ktp; kaj OGG por sonrekordoj.
Bonvolu doni al via dosiero nomon informigan, por eviti konfuzadon.
Por enmeti la dosieron en artikolon, skribi ligilon laŭ la formo
<b>[[bildo:dosiero.jpg]]</b> aŭ <b>[[image:bildo.png|teksto por retumiloj negrafikaj]]</b>,
aŭ <b>[[dosiero:doisero.ogg]]</b> por sounds.</p>

<p>Bonvolu rimarki, ke same kiel artikoloj en Vikipedio, aliaj Vikipediistoj
rajtas redakti, anstataŭigi, aŭ forigi viajn alŝutaĵojn se ili kredas, ke
tio servus la enciklopedio. Se vi aĉe abuzas la sistemon, eblas ke vi estos
forbarita.</p>",
"uploadlog"		=> "loglibro de alŝutaĵoj",
"uploadlogpage" => "Loglibro_de_alŝutaĵoj",
"uploadlogpagetext" => "Jen la plej laste alŝutitaj dosieroj.
Ĉiu tempoj montriĝas laŭ la horzono UTC.
<ul>
</ul>
",
"filename"		=> "Dosiero",
"filedesc"		=> "Priskribo",
"affirmation"	=> "Mi asertas, ke la laŭleĝa posedanto de la kopirajto
de ĉi tiu dosiero konsentas eldoni ĝin laŭ la $1.",
"copyrightpage" => "Vikipedio:Kopirajto",
"copyrightpagename" => "permesilo GFDL uzata ĉe Vikipedio",
"uploadedfiles"	=> "Alŝutitaj dosieroj",
"noaffirmation" => "Vi nepre devas aserti, ke via alŝutaĵo ne malobeas la leĝojn pri kopirajto.",
"ignorewarning"	=> "Malatentu averton kaj konservu la dosieron malgraŭ ĝi.",
"minlength"		=> "Dosiernomo devas konsisti el almenaŭ tri leteroj.",
"badfilename"	=> "Dosiernomo estis ŝanĝita al \"$1\".",
"badfiletype"	=> "\".$1\" estas dosiertipo malrekomendata.",
"largefile"		=> "Oni rekomendas, ke dosieroj ne superu grandon de 100 milbitokojn.",
"successfulupload" => "Alŝuto sukcesis!",
"fileuploaded"	=> "Vi sukcese alŝutis dosieron \"$1\".
Bonvolu sekvu la jenan ligilo: ($2) al la priskribo-paĝo kaj
verki iom da informo pri la dosiero. Ekzemple, kie ĝi devenas;
kiam kaj kiu kreis ĝin; kaj iu ajn, kiun vi scias pri ĝi.",
"uploadwarning" => "Averto",
"savefile"		=> "Konservu dosieron",
"uploadedimage" => "alŝutis \"$1\"",

# Image list
#
"imagelist"		=> "Listo de alŝutitaj dosieroj",
"imagelisttext"	=> "Jen listo de $1 dosieroj, ordigitaj laŭ $2.",
"getimagelist"	=> "akiras dosierliston",
"ilshowmatch"	=> "Montru tiujn dosierojn kies nomojn trafas",
"ilsubmit"		=> "Serĉu",
"showlast"		=> "Montru la $1 lastajn dosierojn laŭ $2.",
"all"			=> "ĉiuj",
"byname"		=> "nomo",
"bydate"		=> "dato",
"bysize"		=> "grando",
"imgdelete"		=> "forigu",
"imgdesc"		=> "pri",
"imglegend"		=> "(pri) = montru/redaktu priskribon de dosiero.",
"imghistory"	=> "Historio de alŝutoj",
"revertimg"		=> "res",
"deleteimg"		=> "for",
"imghistlegend" => "(nun) = ĉi tiu estas la nuna versio de la dosiero, (for) = forigu
ĉi tiun malnovan version, (res) = restarigi ĉi tiun malnovan version.
<br><i>Click on date to see image uploaded on that date</i>.",
"imagelinks"	=> "Ligiloj al la dosiero",
"linkstoimage"	=> "La jenaj paĝoj ligas al ĉi tiu dosiero:",
"nolinkstoimage" => "Neniu paĝo ligas al ĉi tiu dosiero.",

# Statistics
#
"statistics"	=> "Statistiko",
"sitestats"		=> "Pri la retejo",
"userstats"		=> "Pri la uzularo",
"sitestatstext" => "Troviĝas en nia datumaro sume <b>$1</b> paĝoj.
Tiu cifro enhavas \"diskuto-paĝoj\", paĝoj pri Vikipedio, etetaj
\"stubartikoloj\", alidirektiloj, kaj aliaj, kiaj eble ne estas vere
artikoloj. Malatenante ilin, troviĝas <b>$2</b> probable-ĝustaj
artikoloj.</p>

<p>Oni vidis sume <b>$3</b> paĝojn, kaj redaktis sume <b>$4</b> paĝojn
ekde la starigo de la nuna vikiprogramo (Aŭgusto 2002).
Tio estas meznombre unu paĝo po <b>$5</b> okulumoj, kaj po <b>$6</b> redaktoj.",
"userstatstext" => "Enskribis <b>$1</b> uzuloj. El tiuj, <b>$2</b> estas kunestroj
(vidu $3).",

# Maintenance Page
#
"maintenance"		=> "Ripariloj kaj zorgiloj",
"maintnancepagetext"	=> "Jen diversaj iloj por riparado kaj ĝenerala zorgado de la datumaro.
Kelkaj funkcioj povas stresi la datumbazon, do bonvolu ne reŝuti post ĉiu riparita ero!",
"maintenancebacklink"	=> "Revenu al la ilaro",
"disambiguations"	=> "Misligitaj apartigiloj",
"disambiguationspage"	=> "Vikipedio:Apartigiloj",
"disambiguationstext"	=> "La jenaj paĝoj alligas <i>paĝon apartigilon</i>. Ili devus anstataŭe alligi la ĝustan subjekton.<br>Oni konsideras tiujn paĝojn, kiujn alligas $1 apartigiloj.<br>Ligado el ne-artikolaj sekcioj <i>ne</i> listiĝas ĉi tie.",
"doubleredirects"	=> "Duoblaj alidirektoj",
"doubleredirectstext"	=> "<b>Atentu:</b> Eblas, ke la jena listo enhavus falsaj pozitivoj. Ĝenerale, tiu signifas, ke estas plua teksto kun ligiloj post la #REDIERCT.<br>
Ĉiu linio montras ligilojn je la unua kaj dua alidirektiloj, plus la unua linio de la teksto de la dua alidirektilo, kiu ĝenerale montras la \"veran\" celartikolon, kiun devus celi la unua alidirektilo.",
"brokenredirects"	=> "Rompitaj alidirektoj",
"brokenredirectstext"	=> "La jenaj alidirektiloj alligas artikolojn, kiuj ne ekzistas.",
"selflinks"		=> "Paĝoj memligantaj",
"selflinkstext"		=> "La jenaj paĝoj enhavas ligon al si mem, kiu estas neutila.",
"mispeelings"           => "Paĝoj kun misliterumoj",
"mispeelingstext"               => "La jenaj paĝoj enhavas unu el la oftaj misliterumadoj listitaj en $1. La ĝusta literumado montriĝos (ĉi tiel).",
"mispeelingspage"       => "Listo de oftaj misliterumadoj",
"missinglanguagelinks"  => "Mankantaj interlingvaj ligiloj",
"missinglanguagelinksbutton"    => "Montru manktantajn interlingvajn ligilojn por",
"missinglanguagelinkstext"      => "La jenaj artikoloj <i>ne</i> alligas sian ekvivalenton en la lingvo $1. Alidirektiloj kaj subpaĝoj <i>ne</i> montriĝas.",


# Miscellaneous special pages
#
"orphans"		=> "Orfoj",
"lonelypages"	=> "Orfoj",
"unusedimages"	=> "Neuzataj bildoj kaj dosieroj",
"popularpages"	=> "Plej vizitataj paĝoj",
"nviews"		=> "$1 foje",
"wantedpages"	=> "Dezirataj paĝoj",
"nlinks"		=> "$1 ligiloj",
"allpages"		=> "Tuta paĝaro",
"randompage"	=> "Hazarda paĝo",
"shortpages"	=> "Etaj paĝoj",
"longpages"		=> "Longaj paĝoj",
"listusers"		=> "Uzularo",
"specialpages"	=> "Specialaj paĝoj",
"spheading"		=> "Specialaj paĝoj",
"sysopspheading" => "Specialaj paĝoj por uzado de kunestroj",
"developerspheading" => "Specialaj paĝoj nur por uzado de programistoj",
"protectpage"	=> "Protektu paĝon",
"recentchangeslinked" => "Montru ligitajn",
"rclsub"		=> "(al paĝoj ligitaj de \"$1\")",
"debug"			=> "Kontraŭcima",
"newpages"		=> "Novaj paĝoj",
"movethispage"	=> "Movu la paĝon",
"unusedimagestext" => "<p>Notu, ke aliaj TTT-ejoj, ekzemple
la alilingvaj Vikipedioj, povas rekte ligi al dosier per URL.
Tio ne estus enkalkutita en la jena listo.",
"booksources"	=> "Libroservoj",
"booksourcetext" => "Jen ligilaro al aliaj TTT-ejoj, kiuj vendas librojn,
kaj/aŭ informumos pri la libro ligita.
Vikipedio ne estas komerce ligita je tiuj vendejoj, kaj la listo ne estu
komprenata kvazaŭ rekomendo aŭ reklamo.", 

# Email this user
#
"mailnologin"	=> "Neniu alsendota adreso",
"mailnologintext" => "Vi nepre estu <a href=\"" .
  wfLocalUrl( "Speciala:Userlogin" ) . "\">salutinta</a>
kaj havanta validan retpoŝtadreson en viaj <a href=\"" .
  wfLocalUrl( "Speciala:Preferences" ) . "\">preferoj</a>
por retpoŝti al aliaj Vikipediistoj.",
"emailuser"		=> "Retpoŝtu",
"emailpage"		=> "Retpoŝtu",
"emailpagetext"	=> "Se la alsendota vikipediisto donis validan retpoŝtadreson
en la preferoj, vi povas sendi unuopan mesaĝon per la jena formulo.
La retpoŝtadreso de vi donita en la preferoj aperos kiel \"De\"-adreso
de la poŝto, por ke la alsendinto povos respondi.",
"noemailtitle"	=> "Neniu retpoŝtadreso",
"noemailtext"	=> "Ĉi tiu vikipediistoj aŭ ne donis validan retpoŝtadreson
aŭ elektis ne ricevi retpoŝton de aliaj vikipediistoj.",
"emailfrom"		=> "De",
"emailto"		=> "Al",
"emailsubject"	=> "Subjekto",
"emailmessage"	=> "Mesaĝo",
"emailsend"		=> "Sendu",
"emailsent"		=> "Retpoŝto sendita",
"emailsenttext" => "Via retpoŝto estas sendita.",



# Watchlist
#
"watchlist"		=> "Atentaro",
"watchlistsub"	=> "(de uzulo \"$1\")",
"nowatchlist"	=> "Vi ne jam elektis priatenti iujn ajn paĝojn.",
"watchnologin"	=> "Ne salutinta",
"watchnologintext"	=> "Nepras <a href=\"" .
  wfLocalUrl( "Speciala:Userlogin" ) . "\">saluti</a>
por umi vian atentaron.",
"addedwatch"	=> "Aldonis al atentaro",
"addedwatchtext" => "La paĝo \"$1\" estas aldonita al via <a href=\"" .
  wfLocalUrl( "Speciala:Watchlist" ) . "\">atentaro</a>.
Estontaj ŝanĝoj al tiu paĝo aperos en <b>dika tipo</b> en la <a href=\"" .
  wfLocalUrl( "Speciala:Recentchanges" ) . "\">listo de Lastaj Ŝanĝoj</a>,
kaj estos kalkulita en la listo de via Atentaro.

<p>Se vi poste volos eksigi la paĝon el via atentaro, alklaku \"Malatentu paĝon\" en la ilobreto.",
"removedwatch"	=> "Eltiris el atentaro",
"removedwatchtext" => "La paĝo \"$1\" estas tirita el via atentaro.",
"watchthispage"	=> "Priatentu paĝon",
"unwatchthispage" => "Malatentu paĝon",
"notanarticle"	=> "Ne estas artikolo",

# Delete/protect/revert
#
"deletepage"	=> "Forigu paĝon",
"confirm"		=> "Konfirmu",
"confirmdelete" => "Konfirmu forigadon",
"deletesub"		=> "(Forigas \"$1\")",
"confirmdeletetext" => "Vi forigos la artikolon aŭ dosieron kaj
forviŝos ĝian tutan historion el la datumaro.<br>
Bonvolu konfirmi, ke vi vere intencas tion, kaj ke vi komprenas
la konsekvencon, kaj ke vi ja sekvas la [[Vikipedio:Reguloj pri forigado|regulojn pri forigado]].",
"confirmcheck"	=> "Jes, mi tutkore certas ke mi volas forigi ĉi tion.",
"actioncomplete" => "Ago farita",
"deletedtext"	=> "\"$1\" estas forigita.
Vidu je $2 por rekordo de lastatempaj forigoj.",
"deletedarticle" => "forigis \"$1\"",
"dellogpage"	=> "Loglibro_de_forigoj",
"dellogpagetext" => "Jen listo de la plej lastaj forigoj el la datumaro.
Ĉiuj tempoj sekvas la horzonon de la servilo. (UTC)
<ul>
</ul>
",
"deletionlog"	=> "loglibro de forigoj",
"reverted"		=> "Restarigis antaŭan version",
"deletecomment"	=> "Kialo por forigo",
"imagereverted" => "Restarigo de antaŭa versio sukcesis.",

# Undelete
"undelete" => "Restarigu forigitan paĝon",
"undeletepage" => "Montru kaj restarigu forigitajn paĝojn",
"undeletepagetext" => "La jenaj paĝoj estas forigitaj, sed ankoraŭ restas arkivitaj,
kaj oni povas restarigi ilin. La arkivo povas esti malplenigita periode.",
"undeletearticle" => "Restarigu forigitan artikolon",
"undeleterevisions" => "$1 revizioj arkivitaj",
"undeletehistory" => "Se vi restarigos la paĝon, ĉiuj revizioj estos restarigitaj
en la historio. Se nova paĝo kun la sama nomo estas kreita de la forigo, la restarigitaj
revizioj aperos antaŭe en la historio, kaj la aktuala versio ne estos anstataŭigita.",
"undeleterevision" => "Forigita revizio de $1", # ($1 estas tempo)
"undeletebtn" => "Restarigu!",
"undeletedarticle" => "restarigis \"$1\"",
"undeletedtext"   => "La artikolo [[$1]] estas sukcese restarigita.
Vidu je [[Vikipedio:Loglibro de forigoj]] por rekordo de lastatempaj forigoj kaj restarigoj.",

# Contributions
#
"contributions"	=> "Kontribuoj de Vikipediisto",
"contribsub"	=> "De $1",
"nocontribs"	=> "Trovis neniajn redaktojn laŭ tiu kriterio.",
"ucnote"		=> "Jen la <b>$1</b> lastaj redaktoj de tiu Vikipediisto dum la <b>$2</b> lastaj tagoj.",
"uclinks"		=> "Montru la $1 lastajn redaktojn; montru la $2 lastajn tagojn.",

# What links here
#
"whatlinkshere"	=> "Ligantoj ĉi tien",
"notargettitle" => "Sen celpaĝo",
"notargettext"	=> "Vi ne precizigis, kiun paĝon aŭ uzulon priumi.",
"linklistsub"	=> "(Listo de ligoj)",
"linkshere"		=> "La jenaj paĝoj ligas ĉi tien:",
"nolinkshere"	=> "Neniu paĝo ligas ĉi tien.",
"isredirect"	=> "alidirektilo",

# Block/unblock IP
#
"blockip"		=> "Forbaru IP-adreson",
"blockiptext"	=> "Per la jena formulo vi povas forbari iun IP-adreson
je la rajto enskribi en la vikio.
Oni tion faru ''nur'' por eviti vandalismon, kaj sekve la
[[Vikipedio:Reguloj pri forbarado|regulojn pri forbarado]].
Klarigu precizan kialon malsupre (ekzemple, citu paĝojn, kiuj estis
vandalumitaj).",
"ipaddress"		=> "IP-adreso",
"ipbreason"		=> "Kialo",
"ipbsubmit"		=> "Forbaru la adreson",
"badipaddress"	=> "La IP-adreso estas misformita.",
"noblockreason" => "Vi nepre klarigu kialon pri la forbaro.",
"blockipsuccesssub" => "Sukcesis forbari",
"blockipsuccesstext" => "La IP-adreso \"$1\" estas forbarita.
<br>Vidu la [[Special:Ipblocklist|liston de IP-forbaroj]].",
"unblockip"		=> "Malforbari IP-adreson",
"unblockiptext"	=> "Per la jena formulo vi povas repovigi al iu
forbarita IP-adreso la povon enskribi en la vikio.",
"ipusubmit"		=> "Malforbaru la adreson",
"ipusuccess"	=> "IP-adreso \"$1\" estas malforbarita",
"ipblocklist"	=> "Listo de forbaritaj IP-adresoj",
"blocklistline"	=> "Je $1, $2 forbaris $3",
"blocklink"		=> "forbaru",
"unblocklink"	=> "malforbaru",
"contribslink"	=> "kontribuoj",

# Developer tools
#
"lockdb"		=> "Ŝlosi datumaron",
"unlockdb"		=> "Malŝlosi datumaron",
"lockdbtext"	=> "Se vi ŝlosos la datumaron, tio neebligos al ĉiuj uzuloj
redakti paĝojn, ŝanĝi preferojn, priumi atentarojn, kaj diversaj aliaj
aferoj, por kiuj nepras ŝanĝi al la datumaro.
Bonvolu certigu, ke vi efektive intencas tion fari, kaj ke vi ja malŝlosos
la datumaron post ol vi finos vian riparadon.",
"unlockdbtext"	=> "Se malŝlosos la datumaron, tio reebligos al ĉiuj uzuloj
redakti paĝojn, ŝanĝi preferojn, priumi la atentaron, kaj aliaj aferoj,
por kiuj nepras ŝanĝi al la datumaro.
Bonvolu certigu, ke vi efektive intencas tion fari.",
"lockconfirm"	=> "Jes, mi vere volas ŝlosi la datumaron.",
"unlockconfirm"	=> "Jes, mi vere volas malŝlosi la datumaron.",
"lockbtn"		=> "Ŝlosi datumaron",
"unlockbtn"		=> "Malŝlosi datumaron",
"locknoconfirm" => "Vi ne konfirmis.",
"lockdbsuccesssub" => "Datumaro ŝlosita",
"unlockdbsuccesssub" => "Datumaro malŝlosita",
"lockdbsuccesstext" => "La datumaro de Vikipedio estas ŝlosita.
<br>Ne forgesu malŝlosi ĝin post ol vi finos la riparadon.",
"unlockdbsuccesstext" => "La datumaro de Vikipedio estas malŝlosita.",

# SQL query
#
"asksql"		=> "SQL-informpeto",
"asksqltext"	=> "Per la jena formulo vi povas rekte peti la datumbazon
per informpeto SQL.
Tio povas ege stresi la servilon, do bonvolu uzi ĝin ŝpare kaj singarde.",
"sqlquery"		=> "Tajpu informpeton",
"querybtn"		=> "Petu!",
"selectonly"	=> "Informpetojn krom \"SELECT\" estas limigitaj je
Vikipedio-programistoj.",
"querysuccessful" => "Informpeto sukcesis",

# Move page
#
"movepage"		=> "Movu paĝon",
"movepagetext"	=> "Per la jena formulo vi povas ŝanĝi la nomon de iu paĝo, kunportante
ĝian historion de redaktoj je la nova nomo.
La antaŭa titolo fariĝos alidirektilo al la nova titolo.
Ligiloj al la antaŭa titolo <i>ne</i> estos ŝanĝitaj; uzu
la [[Speciala:Maintenance|riparilojn kaj zorgilojn]] por certigi,
ke ne restos duoblaj aŭ fuŝitaj alidirektiloj.
Kiel movanto, vi respondecas pri ĝustigado de fuŝitaj ligiloj.

Notu, ke la paĝo '''ne''' estos movita se jam ekzistas paĝo
ĉe la nova titolo, krom se ĝi estas malplena aŭ alidirektilo
al ĉi tiu paĝo, kaj sen antaŭa redaktohistorio. Pro tio, vi ja
povos removi la paĝon je la antaŭa titolo se vi mistajpus, kaj
neeblas ke vi neintence forviŝus ekzistantan paĝon per movo.

<b>AVERTO!</b>
Tio povas esti drasta kaj neatendita ŝanĝo por populara paĝo;
bonvolu certigi vin, ke vi komprenas ties konsekvencojn antaŭ
ol vi antaŭeniru.",

"movepagetalktext" => "La movo aŭtomate kunportos la diskuto-paĝon, se tia ekzistas, '''krom se:'''
*Vi movas la paĝon tra sekcioj (ekz de ''Nomo'' je ''Vikipediisto:Nomo''),
*Ne malplena diskuto-paĝo jam ekzistas je la nova nomo, aŭ
*Vi malelektas la suban ŝaltilon.

Tiujokaze, vi nepre permane kunigu la diskuto-paĝojn se vi tion deziras.",
"movearticle"	=> "Movu paĝon",
"movenologin"	=> "Ne salutinta",
"movenologintext" => "Vi nepre estu registrita uzulo kaj <a href=\"" .
  wfLocalUrl( "Speciala:Userlogin" ) . "\">salutu</a>
por rajti movi paĝojn.",
"newtitle"		=> "Al nova titolo",
"movepagebtn"	=> "Movu paĝon",
"pagemovedsub"	=> "Sukcesis movi",
"pagemovedtext" => "Paĝo \"[[$1]]\" estas movita al \"[[$2]]\".",
"articleexists" => "Aŭ tiel titolita paĝo jam ekzistas
aŭ la de vi elektita titolo malvalidas.
Bonvolu elekti alian nomon.",
"talkexists"	=> "Oni ja sukcesis movi la paĝon mem, sed
ne movis la diskuto-paĝon ĉar jam ekzistas tia ĉe la nova titolo.
Bonvolu permane kunigi ilin.",
"movedto"		=> "movis al",
"movetalk"		=> "Movu ankaŭ la \"diskuto\"-paĝon, se tia ekzistas.",
"talkpagemoved" => "La diskuto-paĝo estas ankaŭ movita.",
"talkpagenotmoved" => "La diskuto-paĝo <strong>ne</strong> estas movita."

);


class LanguageEo extends Language {

    function getNamespaces() {
		global $wgNamespaceNamesEo;
		return $wgNamespaceNamesEo;
	}

	function getNsText( $index ) {
		global $wgNamespaceNamesEo;
		return $wgNamespaceNamesEo[$index];
	}

	function getNsIndex( $text ) {
		global $wgNamespaceNamesEo;

		foreach ( $wgNamespaceNamesEo as $i => $n ) {
			if ( 0 == strcasecmp( $n, $text ) ) { return $i; }
		}
		if( 0 == strcasecmp( "Special", $text ) ) return -1;
		if( 0 == strcasecmp( "Wikipedia", $text ) ) return 4;
		return false;
	}

	function specialPage( $name ) {
		return $this->getNsText( Namespace::getSpecial() ) . ":" . $name;
	}

	function getQuickbarSettings() {
		global $wgQuickbarSettingsEo;
		return $wgQuickbarSettingsEo;
	}

	function getSkinNames() {
		global $wgSkinNamesEo;
		return $wgSkinNamesEo;
	}

	function getUserToggles() {
		global $wgUserTogglesEo;
		return $wgUserTogglesEo;
	}

	function getLanguageName( $code ) {
		global $wgLanguageNamesEo;
		if ( ! array_key_exists( $code, $wgLanguageNamesEo ) ) {
			return "";
		}
		return $wgLanguageNamesEo[$code];
	}

	function getMonthName( $key )
	{
		global $wgMonthNamesEo;
		return $wgMonthNamesEo[$key-1];
	}

	function getMonthAbbreviation( $key )
	{
		global $wgMonthAbbreviationsEo;
		return $wgMonthAbbreviationsEo[$key-1];
	}

	function getWeekdayName( $key )
	{
		global $wgWeekdayNamesEo;
		return $wgWeekdayNamesEo[$key-1];
	}

	# Heredu userAdjust()
 
	# La dato- kaj tempo-funkciojn oni povas precizigi laŭ lingvo
	function date( $ts, $adj = false )
	{
		if ( $adj ) { $ts = $this->userAdjust( $ts ); }

		$d = (0 + substr( $ts, 6, 2 )) . ". " .
		$this->getMonthAbbreviation( substr( $ts, 4, 2 ) ) .
		  " " . 
		  substr( $ts, 0, 4 );
		return $d;
	}

	function time( $ts, $adj = false )
	{
		if ( $adj ) { $ts = $this->userAdjust( $ts ); }

		$t = substr( $ts, 8, 2 ) . ":" . substr( $ts, 10, 2 );
		return $t;
	}

	function timeanddate( $ts, $adj = false )
	{
		return $this->time( $ts, $adj ) . ", " . $this->date( $ts, $adj );
	}

	# Heredu rfs1123()

	function getValidSpecialPages()
	{
		global $wgValidSpecialPagesEo;
		return $wgValidSpecialPagesEo;
	}

	function getSysopSpecialPages()
	{
		global $wgSysopSpecialPagesEo;
		return $wgSysopSpecialPagesEo;
	}

	function getDeveloperSpecialPages()
	{
		global $wgDeveloperSpecialPagesEo;
		return $wgDeveloperSpecialPagesEo;
	}

	function getMessage( $key )
	{
		global $wgAllMessagesEo;
		if(array_key_exists($key, $wgAllMessagesEo))
			return $wgAllMessagesEo[$key];
		else
			return Language::getMessage($key);
	}

	function iconv( $in, $out, $string ) {
		# For most languages, this is a wrapper for iconv
		# Por multaj lingvoj, ĉi tiu nur voku la sisteman funkcion iconv()
		# Ni ankaŭ konvertu X-sistemajn surogotajn
		if( $in == "X" ) {
			$xu = array (
				"xx" => "x" , "xX" => "x" ,
				"Xx" => "X" , "XX" => "XX" ,
				"Cx" => "\xc4\x88" , "CX" => "\xc4\x88" ,
				"cx" => "\xc4\x89" , "cX" => "\xc4\x89" ,
				"Gx" => "\xc4\x9c" , "GX" => "\xc4\x9c" ,
				"gx" => "\xc4\x9d" , "gX" => "\xc4\x9d" ,
				"Hx" => "\xc4\xa4" , "HX" => "\xc4\xa4" ,
				"hx" => "\xc4\xa5" , "hX" => "\xc4\xa5" ,
				"Jx" => "\xc4\xb4" , "JX" => "\xc4\xb4" ,
				"jx" => "\xc4\xb5" , "jX" => "\xc4\xb5" ,
				"Sx" => "\xc5\x9c" , "SX" => "\xc5\x9c" ,
				"sx" => "\xc5\x9d" , "sX" => "\xc5\x9d" ,
				"Ux" => "\xc5\xac" , "UX" => "\xc5\xac" ,
				"ux" => "\xc5\xad" , "uX" => "\xc5\xad"
				) ;
			return preg_replace ( "/([cghjsux]x)(?=(?:xx)*[^x\$])/ei" , "\$xu[\"\$1\"]" , $string ) ;
		} else if( $out == "X" ) {
			# FIXME: For output
			$ux = array (
				#"x" => "xx" , "X" => "Xx" ,
				"\xc4\x88" => "Cx" , "\xc4\x89" => "cx" ,
				"\xc4\x9c" => "Gx" , "\xc4\x9d" => "gx" ,
				"\xc4\xa4" => "Hx" , "\xc4\xa5" => "hx" ,
				"\xc4\xb4" => "Jx" , "\xc4\xb5" => "jx" ,
				"\xc5\x9c" => "Sx" , "\xc5\x9d" => "sx" ,
				"\xc5\xac" => "Ux" , "\xc5\xad" => "ux"
			) ;
			# Double Xs only if they follow cxapelutaj literoj or other Xs. It's not canon, but it's a lot prettier
			return strtr ( $string , $ux ) ;
			#return $textregs[1] . preg_replace ( "/((?:^|[^cghjsux])x)x(?!x)/i" , "\$1" , $text ) ;
		}
		return iconv( $in, $out, $string );
	}
	
	function ucfirst( $string ) {
		# For most languages, this is a wrapper for ucfirst()
		# But that doesn't work right in a UTF-8 locale
		include("utf8Case.php");
        return preg_replace (
        	"/^([\\x00-\\x7f]|[\\xc0-\\xff][\\x80-\\xbf]*)/e",
        	"strtr ( \"\$1\" , \$wikiUpperChars )",
        	$string );
	}

    function checkTitleEncoding( $s ) {
        global $wgInputEncoding;
        
        # Check for X-system backwards-compatibility URLs
		$ishigh = preg_match( '/[\x80-\xff]/', $s);
		$isutf = preg_match( '/^([\x00-\x7f]|[\xc0-\xdf][\x80-\xbf]|' .
                '[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xf7][\x80-\xbf]{3})+$/', $s );
        
        if($ishigh and !$isutf) {
        	# Assume Latin1
        	$s = utf8_encode( $s );
        } else {
        	if( preg_match( '/(\xc4[\x88\x89\x9c\x9d\xa4\xa5\xb4\xb5]'.
        		'|\xc5[\x9c\x9d\xac\xad])/', $s ) )
        		return $s;
        }
        
        if( preg_match( '/[cghjsu]x/i', $s ) )
        	return $this->iconv( "X", "UTF-8", $s );
        
        return $s;
	}

	function getAltEncoding() {
		return "X";
	}

}

?>
