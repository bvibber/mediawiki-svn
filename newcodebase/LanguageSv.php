<?

// NOTE: To turn off "Current Events" in the sidebar,
// set "currentevents" => "-"
// 
// The names of the namespaces can be set here, but the numbers
// are magical, so don't change or move them!  The Namespace class
// encapsulates some of the magic-ness.
// 

/* private */ $wgNamespaceNamesSv = array(
        -1      => "Special",
        0       => "",
        1       => "Diskussion",
        2       => "Anv&auml;ndare",
        3       => "Anv&auml;ndare_diskussion",
        4       => "Wikipedia",
        5       => "Wikipedia_diskussion",
        6       => "Bild",
        7       => "Bild_diskussion"
);

/* private */ $wgDefaultUserOptionsSv = array(
        "quickbar" => 1,
	"underline" => 1,
	"hover" => 1,
        "cols" => 80,
	"rows" => 25,
	"searchlimit" => 20,
        "contextlines" => 5,
	"contextchars" => 50,
        "skin" => 0,
	"rcdays" => 7,
	"rclimit" => 50,
        "highlightbroken" => 1,
	"stubthreshold" => 0
);

/* private */ $wgQuickbarSettingsSv = array(
        "Ingen", "Fast v&auml;nster", "Fast h&ouml;ger", "Flytande v&auml;nster"
);

/* private */ $wgSkinNamesSv = array(
        "Standard", "Nostalgi", "Cologne Blå"
);

/* private */ $wgUserTogglesSv = array(
        "hover"            => "Sv&auml;vande text &ouml;ver wikil&auml;nkar",
        "underline"        => "Understrukna l&auml;nkar",
        "highlightbroken"  => "R&ouml;da l&auml;nkar till tomma sidor",
        "justify"          => "Justera indrag",
        "hideminor"        => "G&ouml;m små redigeringar vid senaste &auml;ndring",
        "numberheadings"   => "Automatisk numrering av &ouml;verskrifter",
        "rememberpassword" => "Kom ihåg l&ouml;senord till n&auml;sta bes&ouml;k",
        "editwidth"        => "Redigeringsboxen har full bredd",
        "editondblclick"   => "Redigera sidor med dubbelklick (JavaScript)",
        "watchdefault"     => "&OUML;vervaka nya och &auml;ndrade artiklar",
        "minordefault"     => "Markera som standard alla &auml;ndringer som mindre"
);

/* private */ $wgBookstoreListSv = array(
        "AddALL"         => "http://www.addall.com/New/Partner.cgi?query=$1&type=ISBN",
        "PriceSCAN"      => "http://www.pricescan.com/books/bookDetail.asp?isbn=$1",
        "Barnes & Noble" => "http://shop.barnesandnoble.com/bookSearch/isbnInquiry.asp?isbn=$1",
        "Amazon.com"     => "http://www.amazon.com/exec/obidos/ISBN=$1"
);

/* Note: native names of languages are preferred where known to maximize
   ease of navigation -- people should be able to recognize their own
   languages! */
/* private */ $wgLanguageNamesSv = array(
        "aa" => "Afar",
        "ab" => "Abkhasiska",
        "af" => "Afrikaans",
        "am" => "Amhariska",
        "ar" => "Arabiska",
        "as" => "Assami",
        "ay" => "Aymara",
        "az" => "Aserbajdsjanska",
        "ba" => "Basjkiriska",
        "be" => "Vitryska",
        "bh" => "Bihari",
        "bi" => "Bislama",
        "bn" => "Bengali",
        "bo" => "Tibetanska",
        "br" => "Bretonska",
        "ca" => "Katalanska",
        "ch" => "Chamorro",
        "co" => "Korsikanska",
        "cs" => "Tjeckiska",
        "cy" => "Walesiska",
        "da" => "Danska", # Note two different subdomains. 
        "dk" => "Danska", # 'da' is correct for the language.
        "de" => "Tyska",
        "dz" => "Bhutanesiska",
        "el" => "Grekiska",
        "en" => "Engelska",
        "eo" => "Esperanto",
        "es" => "Spanska",
        "et" => "Estniska",
        "eu" => "Baskiska",
        "fa" => "Persiska",
        "fi" => "Finska",
        "fj" => "Fidjianska",
        "fo" => "F&auml;r&ouml;iska",
        "fr" => "Franska",
        "fy" => "Frisiska",
        "ga" => "Irl&auml;ndska",
        "gl" => "Galiciska",
        "gn" => "Guarani",
        "gu" => "Gujarati",
        "ha" => "Hausa",
        "he" => "Hebreiska",
        "hi" => "Hindi",
        "hr" => "Kroatiska",
        "hu" => "Ungerska",
        "hy" => "Armeniska",
        "ia" => "Interlingua",
        "id" => "Indonesiska",
        "ik" => "Inupiaq",
        "is" => "Isl&auml;ndska",
        "it" => "Italienska",
        "iu" => "Inuktitut",
        "ja" => "Japanska",
        "jv" => "Javanesiska",
        "ka" => "Georgiska",
        "kk" => "Kazakiska",
        "kl" => "Gr&ouml;nl&auml;ndska",
        "km" => "Kambodjanska",
        "kn" => "Kanaresiska",
        "ko" => "Koreanska",
        "ks" => "Kashmiri",
        "kw" => "Corniska",
        "ky" => "Kirgisiska",
        "la" => "Latin",
        "ln" => "Lingala",
        "lo" => "Laotiska",
        "lt" => "Litauiska",
        "lv" => "Lettiska",
        "mg" => "Malagassiska",
        "mi" => "Maori",
        "mk" => "Makedoniska",
        "ml" => "Malyalam",
        "mn" => "Mongoliska",
        "mo" => "Moldaviska",
        "mr" => "Marathi",
        "ms" => "Malajiska",
        "my" => "Burmesiska",
        "na" => "Nauriska",
        "ne" => "Nepaliska",
        "nl" => "Nederl&auml;ndska",
	"nn" => "Nynorska",
        "no" => "Norska",
        "oc" => "Occitanska",
        "om" => "Afan Oromoo",
        "or" => "Oriya",
        "pa" => "Punjabi",
        "pl" => "Polska",
        "ps" => "Pashto",
        "pt" => "Portugisiska",
        "qu" => "Quechua",
        "rm" => "R&auml;toromanska",
        "rn" => "Rundi",
        "ro" => "Rum&auml;nska",
        "ru" => "Ryska", 
        "rw" => "Rwanda",
        "sa" => "Sanskrit",
        "sd" => "Sindhi",
        "sg" => "Sango",
        "sh" => "Serbokroatiska",
        "si" => "Singalesiska",
        "simple" => "enkel Engelska",
        "sk" => "Slovakiska",
        "sl" => "Slovenska",
        "sm" => "Samoanska",
        "sn" => "Shona",
        "so" => "Somaliska",
        "sq" => "Albanska",
        "sr" => "Serbiska",
        "ss" => "Siswati",
        "st" => "Sotho",
        "su" => "Sudanesiska",
        "sv" => "Svenska",
        "sw" => "Swahili",
        "ta" => "Tamil",
        "te" => "Telugo",
        "tg" => "Tadjikiska",
        "th" => "Thail&auml;ndska",
        "ti" => "Tigrinya",
        "tk" => "Turkmeniska",
        "tl" => "Tagalog",
        "tn" => "Tswana",
        "to" => "Tonga",
        "tr" => "Turkiska",
        "ts" => "Tsonga",
        "tt" => "Tatariska",
        "tw" => "Twi",
        "ug" => "Uiguriska",
        "uk" => "Ukrainska",
        "ur" => "Urdu",
        "uz" => "Uzbekiska",
        "vi" => "Vietnamesiska",
        "vo" => "Volap&#252;k",
        "wo" => "Wolof",
        "xh" => "Xhosa",
        "yi" => "Jiddisch",
        "yo" => "Yoruba",
        "za" => "Zhuang",
        "zh" => "Kinesiska",
        "zu" => "Zulu"
);

/* private */ $wgWeekdayNamesSv = array(
        "s&ouml;ndag", "m&aring;ndag", "tisdag", "onsdag", "torsdag",
        "fredag", "l&ouml;rdag"
);

/* private */ $wgMonthNamesSv = array(
        "januari", "februari", "mars", "april", "maj", "juni",
        "juli", "augusti", "september", "oktober", "november",
        "december"
);

/* private */ $wgMonthAbbreviationsSv = array(
        "jan", "feb", "mar", "apr", "maj", "jun", "jul", "aug",
        "sep", "okt", "nov", "dec"
);

// All special pages have to be listed here: a description of ""
// will make them not show up on the "Special Pages" page, which
// is the right thing for some of them (such as the "targeted" ones).
//#

// private
$wgValidSpecialPagesSv = array(
        "Userlogin"     => "",
        "Userlogout"    => "",
        "Preferences"   => "Mina anv&auml;ndarinst&auml;llningar",
        "Watchlist"     => "Min &ouml;vervakningslista",
        "Recentchanges" => "Senaste &auml;ndringar",

        "Upload"        => "Ladda upp filer",
        "Imagelist"     => "Bildlista",
        "Listusers"     => "Registrerade anv&auml;ndare",
        "Statistics"    => "Sidstatistik",

        "Randompage"    => "Slumpm&auml;ssig artikel",
        "Lonelypages"   => "F&ouml;r&auml;ldral&ouml;s artikel",
        "Unusedimages"  => "F&ouml;r&auml;ldral&ouml;s filer",
        "Popularpages"  => "Popul&auml;ra artiklar",
        "Wantedpages"   => "Mest &ouml;nskade artiklar",
        "Shortpages"    => "Korta artiklar",
        "Longpages"     => "L&aring;nga artiklar",
        "Newpages"      => "De nyaste artiklarna",
        "Allpages"      => "Alla sidor efter titel",

        "Ipblocklist"   => "Blockerade IP adresser",
        "Maintenance"   => "Underh&aring;llssida",
        "Specialpages"  => "Specialsidor",
        "Contributions" => "",
        "Emailuser"     => "E-postanv&auml;ndare",
        "Whatlinkshere" => "",
        "Recentchangeslinked" => "",
        "Movepage"      => "",
        "Booksources"   => "Externa bokk&auml;llor"
);

/* private */ $wgSysopSpecialPagesSv = array(
        "Blockip"       => "Blockera en IP-adress",
        "Asksql"        => "G&ouml;r en s&ouml;kning i databasen",
        "Undelete"      => "Se och &aring;terst&auml;ll raderade sidor"
);

/* private */ $wgDeveloperSpecialPagesSv = array(
        "Lockdb"        => "Skrivskydda databasen",
        "Unlockdb"      => "&ARING;terst&auml;ll skrivning till databasen",
        "Debug"         => "Debuginformation"
);

/* private */ $wgAllMessagesSv = array(

// Bits of text used by many pages:
//	
"linktrail"             => "/^([a-z&auml;&ouml;&aring;]+)(.*)\$/sD",
"mainpage"              => "Huvudsida",
"about"                 => "Om",
"aboutwikipedia"        => "Om Wikipedia",
"aboutpage"             => "Wikipedia:Om",
"help"                  => "Hj&auml;lp",
"helppage"              => "Wikipedia:Hj&auml;lp",
"wikititlesuffix"       => "Wikipedia",
"bugreports"            => "Felrapporter",
"bugreportspage"        => "Wikipedia:Felrapporter",
"faq"                   => "FAQ",
"faqpage"               => "Wikipedia:FAQ",
"edithelp"              => "Redigeringshj&auml;lp",
"edithelppage"          => "Wikipedia:Hur_redigerar_jag_en_sida",
"cancel"                => "Avbryt",
"qbfind"                => "SnabbS&ouml;k",
"qbbrowse"              => "Genoms&ouml;k",
"qbedit"                => "Redigera",
"qbpageoptions"         => "Sidinst&auml;llningar",
"qbpageinfo"            => "Sidinformation",
"qbmyoptions"           => "Mina inst&auml;llingar",
"mypage"                => "Min sida",
"mytalk"                => "Min diskussion",
"currentevents"         => "-",
"errorpagetitle"        => "Fel",
"returnto"              => "Tillbaka till $1.",
"fromwikipedia"         => "Fr&aring;n Wikipedia, den fria encyklopedin.",
"whatlinkshere"         => "Vilka sidor l&auml;nkar hit?",
"help"                  => "Hj&auml;lp",
"search"                => "S&ouml;k",
"history"               => "Versionshistorik",
"printableversion"      => "Skrivarv&auml;nlig version",
"editthispage"          => "Redigera den h&auml;r sidan",
"deletethispage"        => "Radera den h&auml;r sidan",
"protectthispage"       => "Skydda den h&auml;r sida",
"unprotectthispage"     => "Ta bort skydd av denna sida",
"talkpage"              => "Diskussionssida",
"subjectpage"           => "&Auml;mnessida",
"userpage"              => "Visa anv&auml;ndarsida",
"wikipediapage"         => "Visa metasida",
"imagepage"             => "Visa bildsida",
"otherlanguages"        => "Andra spr&aring;k",
"redirectedfrom"        => "(Omdirigerad fr&aring;n $1)",
"lastmodified"          => "Denna sida blev senast &auml;ndrad $1.",
"viewcount"             => "Denna sida har visats $1 g&aring;nger.",
"gnunote"               => "Denna sida &auml;r publicerad under <a class=internal href='/wiki/GNU_FDL'>GNU FDL</a>.",
"printsubtitle"         => "(Fr&aring;n http://sv.wikipedia.org)",
"protectedpage"         => "Skyddad sida",
"administrators"        => "Wikipedia:Administrat&ouml;rer",
"sysoptitle"            => "Sysop-beh&ouml;righet kr&auml;vs",
"sysoptext"             => "Denna funktion kan bara utf&ouml;ras av anv&auml;ndare med \"sysop\" status.
Se $1.",
"developertitle"        => "Utvecklarbeh&ouml;righet kr&auml;vs",
"developertext"         => "Denna funktion kan bara utf&ouml;ras av anv&auml;ndare med \"developer\" status.
Se $1.",
"nbyte"                 => " bytes",
"go"                    => "Utf&ouml;r",
"ok"                    => "OK",
"sitetitle"             => "Wikipedia",
"sitesubtitle"          => "Den fria encyklopedin",
"retrievedfrom"         => "H&auml;mtat fr&aring;n \"$1\"",

// Main script and global functions
//
"nosuchaction"          => "Funktionen finns inte",
"nosuchactiontext"      => "Den funktion som specificerats i URL:en kan inte
hittas av Wikipediaprogramvaran",
"nosuchspecialpage"     => "S&aring;dan specialsida finns inte",
"nospecialpagetext"     => "Du har &ouml;nskat en specialsida som inte
hittas av Wikipediaprogramvaran.",

// General errors
//
"error"                 => "Fel",
"databaseerror"         => "Databasfel",
"dberrortext"           => "Ett syntaxfel i databasfr&aring;gan har uppst&aring;tt. Detta kan bero p&aring; en felaktig s&ouml;kfr&aring;ga (se $5) eller det kan bero p&aring; ett fel i programvaran.
Den senste utf&ouml;rda databasfr&aring;gan var:
<blockquote><tt>$1</tt></blockquote>
fr&aring;n funktionen \"<tt>$2</tt>\".
MySQL returnerade felen \"$3<tt>: $4</tt>\".",
"noconnect"             => "Kunde inte ansluta till databasen p&aring; $1",
"nodb"                  => "Kunde inte v&auml;lja databasen $1",
"readonly"              => "Databasen &auml;r skrivskyddad",
"enterlockreason"       => "Skriv en grund f&ouml;r skrivskyddet, inklusive 
en uppskattning p&aring; n&auml;r skrivskyddet skall upph&auml;vas",
"readonlytext"          => "Wikipediadatabasen &auml;r f&ouml;r &ouml;gonblicket skrivskyddad f&ouml;r 
nya sidor och andra modifikationer, beroende p&aring; rutinm&auml;ssigt 
underh&aring;ll av databasen, varefter den &aring;terg&aring;r till normalstatus.
Den administrat&ouml;r som skrivskyddade den har gett f&ouml;ljande f&ouml;rklaring:
<p>$1",
"missingarticle"        => "Databasen fann inte texten p&aring; en sida
som den skulle hitta, med namnet \"$1\".
Dette &auml;r inte ett databas fel, utan beror p&aring; ett fel i mjukvaran.
Skicka v&auml;nligen en rapport om detta till en administrat&ouml;r, d&auml;r du ocks&aring; n&auml;mner 
URL:en.",
"internalerror"         => "Internt fel",
"filecopyerror"         => "Kunde inte kopiera filen \"$1\" til \"$2\".",
"filerenameerror"       => "Kunde inte byta namn p&aring; filen \"$1\" til \"$2\".",
"filedeleteerror"       => "Kunde inte radera filen \"$1\".",
"filenotfound"          => "Kunde inte hitta filen \"$1\".",
"unexpected"            => "Ov&auml;ntat v&auml;rde: \"$1\"=\"$2\".",
"formerror"             => "Fel: Kunde inte s&auml;nda formul&auml;r",
"badarticleerror"       => "Denna funktion kan inte utf&ouml;ras p&aring; denna sida.",
"cannotdelete"          => "Kunde inte radera sidan, eller filen som specificerades.",
"badtitle"              => "Felaktig titel",
"badtitletext"          => "Den &ouml;nskade sidans titel var inte till&aring;ten, tom eller sidan
&auml;r felaktigt l&auml;nkad fr&aring;n en Wikipedia p&aring; ett annat spr&aring;k.",


// Login and logout pages
//
"logouttitle"           => "Logga ut Anv&auml;ndare",
"logouttext"            => "Du &auml;r nu utloggad.
Du kan forts&auml;tta som anonym Wikipediaanv&auml;ndare, eller s&aring; kan du logga in
igen som samma eller annan anv&auml;ndare.\n",

"welcomecreation"       => "<h2>V&auml;lkommen, $1!</h2><p>Ditt konto har skapats. Gl&ouml;m inte att anpassa dina Wikipediainst&auml;llningar.",

"loginpagetitle"        => "Logga in Anv&auml;ndare",
"yourname"              => "Ditt anv&auml;ndarnamn",
"yourpassword"          => "Ditt l&ouml;senord",
"yourpasswordagain"     => "Upprepa l&ouml;senord",
"newusersonly"          => " (bara f&ouml;r nya anv&auml;ndare)",
"remembermypassword"    => "Kom ih&aring;g mitt l&ouml;senord till n&auml;sta g&aring;ng.",
"loginproblem"          => "<b>Det var sv&aring;rt att logga in dig .</b><br>Pr&ouml;va igen!",
"alreadyloggedin"       => "<font color=red><b>Anv&auml;ndare $1, du &auml;r redan inloggad !</b></font><br>\n",

"areyounew"             => "Om du &auml;r ny p&aring; Wikipedia och &ouml;nskar ett anv&auml;ndarkonto, s&aring; skriv in ett anv&auml;ndarnamn, d&auml;refter skriver du ett l&ouml;senord som du sedan upprepar.

Observera att det finns b&auml;ttre och s&auml;mre val av anv&auml;ndarnamn, f&ouml;rs&ouml;k v&auml;lja n&aring;got unikt, och d&auml;r du l&ouml;per mindre risk att en dag beh&ouml;va dela detta anv&auml;ndarnamn med ett st&ouml;rre antal anv&auml;ndare som kan ha r&auml;tt till det. Exempel: Johansson, eller Peter. Allra helst ser vi attt du anv&auml;nder ditt eget f&ouml;r och efternamn, men du best&auml;mmer naturligtvis sj&auml;lv.

Din e-postadress &auml;r frivillig; men om du gl&ouml;mmer ditt l&ouml;senord kan du till exempel be om att f&aring; det tills&auml;nt till den adress du har anget.<br>\n",

"login"                 => "Logga in",
"userlogin"             => "Logga in",
"logout"                => "Logga ut",
"userlogout"            => "Logga ut",
"createaccount"         => "Skapa ett konto",
"badretype"             => "De l&ouml;senord du uppgett &ouml;verenst&auml;mmer inte med varandra.",
"userexists"            => "Detta anv&auml;ndarnamn anv&auml;nds redan. Ange ett annat anv&auml;ndarnamn.",
"youremail"             => "Din e-postadress",
"yournick"              => "Ditt smeknamn (till signaturer)",
"emailforlost"          => "Har du gl&ouml;mt ditt l&ouml;senord, s&aring; kan du f&aring; ett nytt l&ouml;senord skickat till din e-post",
"loginerror"            => "Inloggningsproblem",
"noname"                => "Det anv&auml;ndarnamn som du angett finns inte",
"loginsuccesstitle"     => "Inloggningen lyckades",
"loginsuccess"          => "Du &auml;r nu inloggad p&aring; wikipedia med anv&auml;ndarnamnet \"$1\".",
"nosuchuser"            => "Det finns ingen anv&auml;ndare med namnet \"$1\".
Kontrollera stavningen, eller anv&auml;nd formul&auml;ret nedan f&ouml;r att skapa ett nytt konto.",
"wrongpassword"         => "L&ouml;senordet du skrev &auml;r felaktigt. Pr&ouml;va igen",
"mailmypassword"        => "S&auml;nd mig ett nytt l&ouml;senord",
"passwordremindertitle" => "Nytt l&ouml;senord fr&aring;n Wikipedia",
"passwordremindertext"  => "N&aring;gon (f&ouml;rmodligen du, med IP-numret $1)
har bett oss s&auml;nda dig ett nytt l&ouml;senord f&ouml;r din Wikipedia-inloggning
L&ouml;senordet f&ouml;r anv&auml;ndare \"$2\" &auml;r nu \"$3\".
Du ska logga in p&aring; din anv&auml;ndare och byta l&ouml;senord.",
"noemail"               => "Det finns ingen e-postadress registrerad f&ouml;r anv&auml;ndare \"$1\".",
"passwordsent"          => "Ett nytt l&ouml;senord har skickats till e-posten registrerad av anv&auml;ndaren\"$1\".
Var sn&auml;ll och logga in igen n&auml;r du f&aring;tt meddelandet.",


// Edit pages
//
"summary"               => "Sammanfattning",
"minoredit"             => "Detta &auml;r en mindre &auml;ndring",
"watchthis"             => "Bevaka den här artikeln",
"savearticle"           => "Spara",
"preview"               => "F&ouml;rhandsgranska",
"showpreview"           => "Visa f&ouml;rhandgranskning",
"blockedtitle"          => "Anv&auml;ndaren &auml;r sp&auml;rrad",
"blockedtext"           => "Ditt anv&auml;ndarnamn har blivit sp&auml;rrat av $1.
Anledning &auml;r att:<br>''$2''<p>Ta kontakt med $1 eller en av de andra
[[Wikipedia:Administrat&ouml;rer|administrat&ouml;rerna]] f&ouml;r att diskutera varf&ouml;r du blivit sp&auml;rrad", // "
"newarticle"            => "(Ny)",
"newarticletext"        => "Skriv den nya sidan h&auml;r.",
"noarticletext"         => "(Det finns f&ouml;r tillf&auml;llet ingen text p&aring; den h&auml;r sidan.)",
"updated"               => "(Uppdaterad)",
"note"                  => "<strong>Notera:</strong> ",
"previewnote"           => "Observera att detta &auml;r en f&ouml;rhandsvisning, och att sidan &auml;nnu inte sparats!",
"previewconflict"       => "Denna f&ouml;rhandsvisning &auml;r resultatet av den 
redigerbara texten ovanf&ouml;r,
s&aring; som det kommer att se ut om du v&auml;ljer att spara.",
"editing"               => "Redigerar $1",
"editconflict"          => "Redigeringskonflikt: $1",
"explainconflict"       => "N&aring;gon har &auml;ndrat denna sida efter att du b&ouml;rjade att redigera den.
Det &ouml;versta text blocket inneh&aring;ller den nuvarande texten.
Dina &auml;ndringer syns i det nedersta blocket.
Du m&aring;ste infoga dina &auml;ndringar i den existerande texten.
<b>Bara</b> texten i den &ouml;versta textboxen sparas om du trycker \"Spara sida\".\n<p>",
"yourtext"              => "Din text",
"storedversion"         => "Din sparade version",
"editingold"            => "<strong>VARNING: Du redigerar en gammal version
av denna sida. Om du sparar den, kommer alla &auml;ndringar p&aring; denns sida f&ouml;reg&aring;ende revison att bli &ouml;verskrivna.</strong>\n",
"yourdiff"              => "Skillnader",
"copyrightwarning"      => "L&auml;gg m&auml;rke till att alla bidrag till Wikipedia &auml;r
att betrakta som utgivna under GNU Free Documentation License
(se $1 f&ouml;r detaljer).
Om du inte vill ha din text redigerad och kopierad efter andras gottfinnade s&aring; skall du inte skriva n&aring;gon text h&auml;r.<br>
Du lovar oss ocks&aring; att du skrev texten sj&auml;lv, eller kopierade fr&aring;n en
public domain eller liknande fri resurs.

<strong>
L&Auml;GG ALDRIG UT MATERIAL H&Auml;R SOM SKYDDAS AV UPPHOVSR&Auml;TTSLAGEN UTAN F&OUML;RFATTARENS TILL&ARING;TELSE!
</strong>", //'"


// History pages
//
"revhistory"            => "Versionshistoria",
"nohistory"             => "Det finns ingen versionshistoria f&ouml;r denna sida.",
"revnotfound"           => "Versionen hittades inte",
"revnotfoundtext"       => "Den gamla versionen av den sida du fr&aring;gade efter kan inte hittas. Kontrollera den URL du anv&auml;nde f&ouml;r att n&aring; denna sida.\n",
"loadhist"              => "L&auml;ser sidans versioner",
"currentrev"            => "Nuvarande version",
"revisionasof"          => "Versionen fr&aring;n",
"cur"                   => "nuvarande",
"next"                  => "n&auml;sta",
"last"                  => "f&ouml;reg&aring;ende",
"orig"                  => "original",
"histlegend"            => "F&ouml;rklaring: (nuvarande) = skillnad mot den nuvarande versionen,
 (f&ouml;reg&aring;ende) = skillnad mot den f&ouml;reg&aring;ende versionen, M = mindre &auml;ndring",


// Diffs
//
"difference"            => "(Skillnad mellan versioner)",
"loadingrev"            => "l&auml;ser version f&ouml;r att se skillnad",
"lineno"                => "Rad $1:",
"editcurrent"           => "Redigera den nuvarande versionen av denna sida",

// Search results
//
"searchresults"         => "S&ouml;kresultat",
"searchhelppage"        => "Wikipedia:S&ouml;kning",
"searchingwikipedia"    => "S&ouml;kning p&aring; Wikipedia",
"searchresulttext"      => "F&ouml;r mer information om s&ouml;kning p&aring; Wikipedia, se $1.",
"searchquery"           => "For query \"$1\"",
"badquery"              => "Felaktigt utformat s&ouml;kbegrepp",
"badquerytext"          => "Vi kunde inte utf&ouml;ra din s&ouml;kning.
Detta beror sannolikt p&aring; att du f&ouml;rs&ouml;kt s&ouml;ka efter ett ord med f&auml;rre &auml;n tre bokst&auml;ver, n&aring;got som f.n. inte st&ouml;ds. Det kan ocks&aring; vara s&aring; att du har anget en felaktig s&ouml;kning, till exempel \"fisk och och skaldjur\". Prova att formulera om s&ouml;kningen.",
"matchtotals"           => "S&ouml;kordet f&ouml;rekommer i \"$1\" &ouml;verensst&auml;mde med $2 artiklar titlar
och texter i $3 artiklar.",
"titlematches"          => "Artikel titlar som &ouml;verensst&auml;mmer med s&ouml;kordet",
"notitlematches"        => "Ingen artikel titlar &ouml;verensst&auml;mmer med s&ouml;kordet",
"textmatches"           => "Artikel texter som &ouml;verensst&auml;mmer med s&ouml;kordet",
"notextmatches"         => "Ingen artikel texter &ouml;verensst&auml;mmer med s&ouml;kordet",
"prevn"                 => "f&ouml;rra $1",
"nextn"                 => "n&auml;sta $2",
"viewprevnext"          => "Om ($1) ($2) ($3).",
"showingresults"        => "Nedan visas <b>$1</b> resultat som startar med nummer <b>$2</b>.",
"nonefound"             => "<strong>Note</strong>: Misslyckade s&ouml;kningar f&ouml;rorsakas ofta av
 att man s&ouml;ker efter vanliga ord som \"har\" och \"fr&aring;n\",
vilka inte indexeras, eller att specificera flera s&ouml;kord (bara 
sidor som inneh&aring;ller alla s&ouml;korden hittas).",
"powersearch"           => "S&ouml;k",
"powersearchtext"       => "
S&ouml;k i namnutrymme :<br>
$1<br>
$2 List redirects &nbsp; S&ouml;k efter $3 $9",


// Preferences page
//
"preferences"           => "Inst&auml;llningar",
"prefsnologin"          => "Du &auml;r inte inloggad",
"prefsnologintext"      => "Du m&aring;ste vara <a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">inloggad</a>
f&ouml;r att kunna &auml;ndra i inst&auml;llningar.",
"prefslogintext"        => "Du &auml;r inloggad som \"$1\".
Ditt IP-nummer &auml;r $2.",
"prefsreset"            => "Inst&auml;llningar har blivit &aring;terst&auml;llda fr&aring;n minne.",
"qbsettings"            => "Inst&auml;llningar f&ouml;r snabbmeny",
"changepassword"        => "Byt l&ouml;senord",
"skin"                  => "Utseende",
"saveprefs"             => "Spara inst&auml;llningar",
"resetprefs"            => "&ARING;terst&auml;ll inst&auml;llningar",
"oldpassword"           => "Gammalt l&ouml;senord",
"newpassword"           => "Nytt l&ouml;senord",
"retypenew"             => "Skriv om nytt l&ouml;senord",
"textboxsize"           => "Textbox dimensioner",
"rows"                  => "Rader",
"columns"               => "Kolumner",
"searchresultshead"     => "Inst&auml;llningar f&ouml;r s&ouml;kresultat",
"resultsperpage"        => "Resultat att visa per sida",
"contextlines"          => "Linjer att visa per sida",
"contextchars"          => "Antalet bokst&auml;ver per linje i resultatet",
"stubthreshold"         => "Gr&auml;nser f&ouml;r visning av stubs",
"recentchangescount"    => "Antalet aritklar i \"senaste &auml;ndringarna\" ",
"savedprefs"            => "Dina inst&auml;llningar har blivit sparade",
"timezonetext"          => "Skriv in antalet timmar som din lokal tid skiljer sig fr&aring;n
serverns klocka (UTC).
Den blir automatiskt inst&auml;lld efter svensk tid eller skulle man till exempel f&ouml;r svensk vintertid, endast ha \"1\" (och \"2\" n&auml;r vi har sommartid).",
"localtime"             => "Lokal tid",
"timezoneoffset"        => "Utj&auml;mna",
"emailflag"             => "Stoppa andra anv&auml;ndare att skicka e-post till dig",

// Recent changes
//
"recentchanges"         => "Senaste &auml;ndringarna",
"recentchangestext"     => "Se de senaste redigerade sidorna i Wikipedia p&aring; denna 
sida.

[[Wikipedia:V&auml;lkommen|V&auml;lkommen]]!
V&auml;nligen l&auml;s dessa sidor: [[wikipedia:FAQ|Ofta st&auml;llda 
fr&aring;gor]], [[Wikipedia:Policy|policy]]

(s&auml;rskilt [[wikipedia:Namngivning|namngivning]],
[[wikipedia:Skriv Wikipedia ifr&aring;n en neutral synpunkt|Skriv Wikipedia ifr&aring;n en neutral synpunkt]],
och [[wikipedia:Mest vanliga nyb&ouml;rjarfel p&aring; Wikipedia|Mest vanliga nyb&ouml;rjarfel p&aring; Wikipedia]]).

Det &auml;r mycket viktigt att du inte l&auml;gger material p&aring; 
Wikipedia som andra &auml;ger upphovsr&auml;tten till. De r&auml;ttsliga konsekvenserna kan bli 
mycket kostbara och skadligt f&ouml;r projektet, s&aring; v&auml;nligen l&aring;t bli, om du &auml;r os&auml;ker.
Se ocks&aring; [http://meta.wikipedia.org/wiki/Special:Recentchanges 
recent meta discussion] (oftast p&aring; engelska).",
"rcloaderr"             => "L&auml;ser senaste redigerade sidor",
"rcnote"                => "Nedanf&ouml;r &auml;r de senaste <strong>$1</strong> &auml;ndringarna under de 
sista <strong>$2</strong> dagarna.",
"rcnotefrom"            => "Nedanf&ouml;r &auml;r &auml;ndringerne fr&aring;n <b>$2</b> till <b>$1</b> visade.",
"rclistfrom"            => "Visa nya &auml;ndringer fr&aring;n och med $1",
"rclinks"               => "Visa de senaste $1 &auml;ndringarna under de senaste $2 timmarna / $3 dagarna",
// "rclinks"             => "Visa de senaste $1 &auml;ndringarna under de senaste $2 dagarna",
"rchide"                => "i $4 form; $1 mindre &auml;ndringar; $2 andra namnrum; $3 mer &auml;n en redigering.",
"diff"                  => "skilland",
"hist"                  => "historia",
"hide"                  => "g&ouml;m",
"show"                  => "visa",

"tableform"             => "tabell",
"listform"              => "lista",
"nchanges"              => "$1 &auml;ndringar",
"minoreditletter"       => "M",
"newpageletter"         => "N",

// Upload
//
"upload"                => "Ladda upp",
"uploadbtn"             => "Ladda upp fil",
"uploadlink"            => "Ladda upp bild",
"reupload"              => "&aring;teruppladdning",
"reuploaddesc"          => "Tillbaka till uppladdningsformul&auml;r.",
"uploadnologin"         => "Inte inloggad",
"uploadnologintext"     => "Du m&aring;ste vara <a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">inloggad</a>
f&ouml;r att kunna ladda upp filer.",
"uploadfile"            => "Ladda upp fil",
"uploaderror"           => "Uppladdnings fel",
"uploadtext"            => "<strong>STOPP!</strong> Innan du laddar upp h&auml;r,
s&aring; m&aring;ste du ha l&auml;st och f&ouml;lja Wikipedias <a href=\"" .
wfLocalUrlE( "Wikipedia:Policy om bruk av bilder" ) . "\">policy om hur 
bilder f&aring;r anv&auml;ndas</a>.
<p>F&ouml;r att visa eller s&ouml;ka tidigare uppladdade bilder g&aring; till
<a href=\"" . wfLocalUrlE( "Special:Imagelist" ) .
"\">lista &ouml;ver uppladdade bilder</a>.
Uppladdningar och borttagningar loggas i <a href=\"" .
wfLocalUrlE( "Wikipedia:Upload_log" ) . "\">uppladdnings logg</a>.
<p>Anv&auml;nd formul&auml;ret nedan f&ouml;r att ladda upp nya filer, som 
du kan illustrera dina artiklar med.
P&aring; de flesta webbl&auml;sare kommer du att se en \"Browse...\" knapp eller en 
\"&OUML;ppna...\" knapp, som startar ditt operativsystems dialogruta f&ouml;r att &ouml;ppna filer. N&auml;r du valt en fil kommer namnet p&aring; den filen att visas i textf&auml;ltet brevid knappen. Du m&aring;ste &auml;ven kryssa f&ouml;r rutan, f&ouml;r att du inte g&ouml;r n&aring;got som strider mot upphovsr&auml;tten av filen som laddas upp.
Tryck p&aring; \"Upload\" knappen f&ouml;r att ladda upp filen.
Detta kan dr&ouml;ja ett tag om du har en l&aring;ngsam internetf&ouml;rbindelse.
<p>Formaten p&aring; filerna ska helst vara JPEG f&ouml;r bilder, PNG f&ouml;r ritningar 
och andra ikonliknande bilder och OGG f&ouml;r ljud.
Var v&auml;nlig namnge filen med ett s&aring; beskrivande namn som m&ouml;jligt, f&ouml;r att undvika f&ouml;rvirring.
F&ouml;r att anv&auml;nda en fil i en artikel, skriv f&ouml;ljande om det &auml;r en bild: <b>[[bild:filnamn.jpg]]</b> eller <b>[[bild:filnamn.png|alternativ text]]</b>
eller <b>[[media:filnamn.ogg]]</b> om det &auml;r en ljudfil.
<p>Kom ih&aring;g att det h&auml;r &auml;r en wiki, vilket g&ouml;r att andra kan redigera eller ta bort dina uppladdningar om de tycker de inte passar i en artikel. Om du missbrukar systemet med uppladdningar kommer filen avl&auml;gsnas och du bli sp&auml;rrad fr&aring;n att ladda upp filer i framtiden.",
"uploadlog"             => "upload log",
"uploadlogpage"         => "Upload_log",
"uploadlogpagetext"     => "Nedan f&ouml;ljer en lista med de senaste uppladdade filerna.
Alla tider visas efter serverns tid (UTC).
<ul>
</ul>
",
"filename"              => "Filnamn",
"filedesc"              => "Beskrivning",
"affirmation"           => "Jag bekr&auml;ftar att &auml;garen till uppehovsr&auml;tten accepterar att licensiera enligt f&ouml;ljande avtal $1.",
"copyrightpage"         => "Wikipedia:copyright",
"copyrightpagename"     => "Wikipedia copyright",
"uploadedfiles"         => "Uppladdade filer",
"noaffirmation"         => "Du m&aring;ste bekr&auml;fta att uppladdningen inte kr&auml;nker n&aring;gon copyright",
"ignorewarning"         => "Ignorera varning och spara fil.",
"minlength"             => "Namnet p&aring; bildfilen ska vara minst tre bokst&auml;ver",
"badfilename"           => "Bildnamn har blivit &auml;ndrat till \"$1\".",
"badfiletype"           => "\".$1\" &auml;r inte ett rekomenderat bildformat.",
"largefile"             => "Bilder ska helst inte vara st&ouml;rre &auml;n 100k.",
"successfulupload"      => "Uppladdningen lyckades",
"fileuploaded"          => "Filen \"$1\" laddades upp korrekt.
F&ouml;lj denna l&auml;nk: ($2) till beskrivningssidan och fyll i
information om filen, som till exempel var den kommer ifr&aring;n, 
n&auml;r den skapades och vem som gjort den och allt annat du vet om den.",
"uploadwarning"         => "Uppladnings varning",
"savefile"              => "Spara fil",
"uploadedimage"         => "uppladdad \"$1\"",

// Image list
//
"imagelist"             => "Bildlista",
"imagelisttext"         => "Nedan &auml;r en lista med $1 bilder sorterad $2",
"getimagelist"          => "h&auml;mta bildlista",
"ilshowmatch"           => "Visa alla bilder med namn som matchar",
"ilsubmit"              => "S&ouml;k",
"showlast"              => "Visa de senaste $1 bilderna sorterad $2.",
"all"                   => "alla",
"byname"                => "efter namn",
"bydate"                => "efter datum",
"bysize"                => "efter storlek",
"imgdelete"             => "ta bort",
"imgdesc"               => "beskrivning",
"imglegend"             => "Legend: (beskrivning) = visa/redigera bildbeskrivning.",
"imghistory"            => "Bildhistoria",
"revertimg"             => "&aring;terg&aring;",
"deleteimg"             => "radera",
"imghistlegend"         => "Legend: (nuvarande) = detta &auml;r den nuvarande bilden, 
(ta bort) = ta bort den gamla version, (&aring;terg&aring;) = &aring;terg&aring; till en gammal version.
<br><i>Klicka p&aring; ett datum f&ouml;r att se bilden som laddades upp den dagen</i>.", //"
"imagelinks"            => "Bildl&auml;nk",
"linkstoimage"          => "De f&ouml;ljande sidorna l&auml;nkar till denna bild:",
"nolinkstoimage"        => "Det finns ingen sida som l&auml;nkar till denna bild.",

// Statistics
//
"statistics"            => "Statistik",
"sitestats"             => "Statistiksida",
"userstats"             => "Anv&auml;ndarstatistik",
"sitestatstext"         => "Det &auml;r <b>$1</b> sidor i databasen.
Detta inkluderer \"diskussion\"ssidorna, sidor om Wikipedia, mycket korta\"stub\"
sidor, omdirigeringssidor, och andra sidor som kvalificerar sig som artiklar.
Om man tar bort ovanst&aring;ende s&aring; &auml;r det <b>$2</b> sidor som anses som riktiga artiklar.<p>
Det har varit totalt <b>$3</b> sidvisningar och det har varit <b>$4</b> sidor som har &auml;ndrats
sedan uppdateringen av mjukvaran (1 december 2002).
Det vill s&auml;ga <b>$5</b> &auml;ndringar per sida genomsnittligt, 
och <b>$6</b> sidvisningar per &auml;ndring.",
"userstatstext"         => "Det &auml;r <b>$1</b> registrerade anv&auml;ndare.
<b>$2</b> av dem &auml;r administrat&ouml;rer (se $3).",

// Maintenance Page
//
"maintenance"           => "Underh&aring;llssida",
"maintnancepagetext"    => "Den h&auml;r sidan inneh&aring;ller flera verktyg f&ouml;r att sk&ouml;ta sidan. Vissa av dessa funktioner tenderar att stressa databasen (allt tar l&aring;ng tid), s&aring; var sn&auml;ll och  tryck inte p&aring; reloadknappen varje g&aring;ng du gjort en liten &auml;ndring.",
"maintenancebacklink"   => "Tillbaka till underh&aring;llssidorna",
"disambiguations"       => "Sidor med tvetydiga l&auml;nkar",
"disambiguationspage"   => "Wikipedia:L&auml;nkar till sidor med tvetydiga titlar",
"disambiguationstext"   => "F&ouml;ljande artiklar l&auml;nkar till en <i>sidor med tvetydliga titlar</i>. De ska l&auml;nka till en sidor med en korrekt titel.<br>En sida behandlar som tvetydig om den l&auml;nkar fr&aring;n $1. <br>L&auml;nkar fr&aring;n andra namngrupper &auml;r <i>inte</i> listade h&auml;r.",
"doubleredirects"       => "Dubbla omdirigeringar",
"doubleredirectstext"   => "<b>OBS:</b> Denna lista kan inneh&aring;lla falska resultat. Detta betyder normalt att det finns ytterligare text under den f&ouml;rsta #REDIRECT.<br>\n Varje rad inneh&aring;ller en l&auml;nk till den f&ouml;rsta och andra omdirigering och den f&ouml;rsta raden av den andra omdirigeringen ger oftast den \"riktiga\" artikeln, vilket egentligen den f&ouml;rsta omdirigeringen ska peka p&aring;.",
"brokenredirects"       => "D&aring;liga omdirigeringar",
"brokenredirectstext"   => "F&ouml;ljande l&auml;nkar omdirigerar till en artikel som inte existerar.",
"selflinks"             => "Sidor med l&auml;nkar till sig sj&auml;lva",
"selflinkstext"         => "F&ouml;ljande sidor inneh&aring;ller l&auml;nkar till sig sj&auml;lv, vilket de inte ska g&ouml;ra.",
"mispeelings"           => "Sidor med felstavningar",
"mispeelingstext"       => "F&ouml;ljande sidor innerh&aring;ller vanliga felstavningar, som visas i $1. Den korrekta stavningen kanske ska se ut s&aring;h&auml;r.",
"mispeelingspage"       => "Lista med vanliga stavfel",
"missinglanguagelinks"  => "Saknade spr&aring;kl&auml;nkar",
"missinglanguagelinksbutton"    => "S&ouml;k efter saknade spr&aring;kl&auml;nkar f&ouml;r",
"missinglanguagelinkstext"      => "De h&auml;r artiklarna &auml;r <i>inte</i> l&auml;nkade 
till deras i $1. Redirects och undersidor visas <i>inte</i>.",

// Miscellaneous special pages
//
"orphans"               => "F&ouml;r&auml;ldral&ouml;sa sidor",
"lonelypages"           => "F&ouml;r&auml;ldral&ouml;sa sidor",
"unusedimages"          => "Oanv&auml;nda bilder",
"popularpages"          => "Popul&auml;ra sidor",
"nviews"                => "$1 visningar",
"wantedpages"           => "&OUML;nskelista",
"nlinks"                => "$1 l&auml;nkar",
"allpages"              => "Alla sidor",
"randompage"            => "Slumpartikel",
"shortpages"            => "Korta sidor",
"longpages"             => "L&aring;nga sidor",
"listusers"             => "Anv&auml;ndarlista",
"specialpages"          => "Speciella sidor",
"spheading"             => "Speciella sidor",
"sysopspheading"        => "Speciella sidor f&ouml;r sysop",
"developerspheading"    => "Speciella sidor f&ouml;r utvecklare",
"protectpage"           => "Skydda sida",
"recentchangeslinked"   => "Relaterade &auml;ndringar",
"rclsub"                => "(till sidor som &auml;r l&auml;nkade fr&aring;n \"$1\")",
"debug"                 => "Debug",
"newpages"              => "Nya sidor",
"movethispage"          => "Flytta den h&auml;r sidan",
"unusedimagestext"      => "<p>L&auml;gg m&auml;rket till att andra hemsidor
som till exempel de internationella wikipedias kan l&auml;nka till bilder 
med en direkt URL, och kan d&auml;rf&ouml;r bli listade h&auml;r trots att de anv&auml;nds kontinuerligt.",
"booksources"           => "Bokk&auml;llor",
"booksourcetext"        => "Nedan f&ouml;ljer en lista &ouml;ver l&auml;nkar till hemsidor som s&auml;ljer
nya och begagnade b&ouml;cker, och mycket annan information om de b&ouml;cker du s&ouml;ker.
Wikipedia har <b>inget</b> aff&auml;rssamarbete med ovanst&aring;ende f&ouml;retag och ska inte heller tolkas som en uppmuntran.",

// Email this user
//
"mailnologin"           => "Ingen adress att skicka till",
"mailnologintext"       => "Du ska vara<a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">inloggad</a>
och ha angivit en korrekt epost-adress i dina <a href=\"" .
  wfLocalUrl( "Special:Preferences" ) . "\">anv&auml;ndarinst&auml;llningar</a>
f&ouml;r att kunna skicka e-post till andra anv&auml;ndare.",
"emailuser"             => "Skicka e-post till den h&auml;r anv&auml;ndaren",
"emailpage"             => "Skicka e-post till annan anv&auml;ndare",
"emailpagetext"         => "Om den h&auml;r anv&auml;ndaren har skrivit in en korrekt e-postadress, i sina
anv&auml;ndarinst&auml;llningar, kommer formul&auml;ret nedan skicka ett meddelande.
Den epost-adress du anget i dina anv&auml;ndarinst&auml;llningar kommer att skrivas
i \"Fr&aring;n\"f&auml;ltet i detta e-post, s&aring; mottagaren har m&ouml;jlighet att svara.",
"noemailtitle"          => "Ingen e-postadress",
"noemailtext"           => "Den h&auml;r anv&auml;ndaren har inte angivet en korrekt e-postadress eller
valt att inte ta emot n&aring;got mail fr&aring;n andra anv&auml;ndare.",
"emailfrom"             => "Fr&aring;n",
"emailto"               => "Till",
"emailsubject"          => "&Auml;mne",
"emailmessage"          => "Meddelande",
"emailsend"             => "Skickat",
"emailsent"             => "E-post sskickat",
"emailsenttext"         => "Din e-post har skickats.",

// Watchlist
//
"watchlist"             => "Min &ouml;vervakningslista",
"watchlistsub"          => "(f&ouml;r anv&auml;ndare \"$1\")",
"nowatchlist"           => "Du har inga sidor upptagna p&aring; din &ouml;vervakningslista.",
"watchnologin"          => "Du &auml;r inte inloggad",
"watchnologintext"      => "Du ska vara<a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">inloggad</a>
f&ouml;r att kunna g&ouml;ra &auml;ndringar p&aring; din &ouml;vervakningslista.",
"addedwatch"            => "Tillagd p&aring; &ouml;vervakningslistan",
"addedwatchtext"        => "Sidan \"$1\" har satts upp p&aring; din <a href=\"" .
  wfLocalUrl( "Special:Watchlist" ) . "\">&ouml;vervakningslista</a>.
Framtida &auml;ndringar av den h&auml;r sidan och dess diskussionssida vill listas d&auml;r, 

och sidan kommer att markeras med <b>fet stil</b> i <a href=\"" .
  wfLocalUrl( "Special:Recentchanges" ) . "\">listan &ouml;ver de senaste &auml;ndringarna
</a> f&ouml;r att l&auml;ttare kunna hittas</p>

<p>Om du vill ta bort denna sida fr&aring;n din &ouml;vervakningslista, s&aring; klicka 
\"Ta bort &ouml;vervakning\" ute i sidan.",
"removedwatch"          => "Borttagen fr&aring;n &ouml;vervakningslista",
"removedwatchtext"      => "Sidan \"$1\" har blivit borttagen fr&aring;n din &ouml;vervakningslista",
"watchthispage"         => "&OUML;vervaka sida",
"unwatchthispage"       => "Stoppa &ouml;vervakning",
"notanarticle"          => "Inte en artikel",

// Delete/protect/revert
//
"deletepage"            => "Ta bort sida",
"confirm"               => "Bekr&auml;fta",
"confirmdelete"         => "Bekr&auml;fta borttagning",
"deletesub"             => "(Tar bort \"$1\")",
"confirmdeletetext"     => "Du h&aring;ller p&aring; och permanent ta bort en sida
eller bild med all dess historia fr&aring;n databasen.
Bekr&auml;fta att du f&ouml;rst&aring;r vad du h&aring;ller p&aring; med och vilka konsekvenser
detta leder till, och att det f&ouml;ljer 
[[Wikipedia:Policy]].", 
"confirmcheck"          => "Ja, jag vill verkligen ta bort det h&auml;r.", //"
"actioncomplete"        => "Genomf&ouml;rt",
"deletedtext"           => "\"$1\" har blivit borttagen.
Se $2 f&ouml;r lista &ouml;ver senaste borttagningar",
"deletedarticle"        => "borttagen \"\$1\"",
"dellogpage"            => "Borttagningslogg",
"dellogpagetext"        => "Nedan &auml;r en lista &ouml;ver de senaste borttagningarna.
De tidsangivelser som anges f&ouml;ljer serverns klocka (UTC).
<ul>
</ul>
",
"deletionlog"           => "borttagningslogg",
"reverted"              => "&ARING;terg&aring;tt till yngre version",
"deletecomment"         => "Anledningen till borttagning",
"imagereverted"         => "&ARING;terst&auml;llandet av nyare artikelversion lyckades",

// Undelete
//
"undelete"              => "&ARING;terst&auml;ll borttagna sidor",
"undeletepage"          => "Visa och &aring;terst&auml;ll borttagna sidor",
"undeletepagetext"      => "F&ouml;ljande sidor har blivit borttagna, men &auml;r fortfarande i arkivet och kan anv&auml;ndas vid &aring;terst&auml;llning.
Arkivet kan ibland rensas p&aring; gamla versioner.",
"undeletearticle"       => "&ARING;terst&auml;ll borttagen artikel",
"undeleterevisions"     => "$1 versioner arkiverade",
"undeletehistory"       => "Om du &aring;terst&auml;ller sidan kommer allt tidigare versioner att sparas i versionshanteraren.
Om en ny sida med samma namn har blivit skapad sedan borttagningen s&aring; kommer den &aring;terst&auml;llda versionen att
hamna i den &auml;ldre versionshanteraren och den senaste versionen av sidan kommer inte blir automatiskt ersatt.",
"undeleterevision"      => "Tog bort version $1",
"undeletebtn"           => "&ARING;terst&auml;lld!",
"undeletedarticle"      => "&aring;terst&auml;lld \"$1\"",
"undeletedtext"         => "Artikeln [[$1]] har blivit &aring;terst&auml;lld
Se [[Wikipedia:Borttagningslogg]] f&ouml;r en lista &ouml;ver nyligen gjorda borttagningar och &aring;terst&auml;llningar",

// Contributions
//
"contributions"         => "Anv&auml;ndarbidrag",
"contribsub"            => "F&ouml;r $1",
"nocontribs"            => "Inga &auml;ndringar var funna som motsvarar dessa kriterier",
"ucnote"                => "Nedan visas dennes anv&auml;ndares senaste <b>$1</b> &auml;ndringar, under de senaste <b>$2</b> dagarna.",
"uclinks"               => "Visa de senaste $1 &auml;ndringarna. Visa de senaste $2 dagarna.",
"uctop"                 => " (top)",

// What links here
//
"whatlinkshere"         => "Vilka sidor l&auml;nkar hit",
"notargettitle"         => "Inget m&aring;l",
"notargettext"          => "Du har inte specificerat en sida eller anv&auml;ndare
f&ouml;r att genomf&ouml;ra den h&auml;r funktionen.",
"linklistsub"           => "(L&auml;nklista)",
"linkshere"             => "F&ouml;ljande sidor l&auml;nkas hit:",
"nolinkshere"           => "Inga sidor l&auml;nkar hit.",
"isredirect"            => "L&auml;nka vidare sida",

// Block/unblock IP
//
"blockip"               => "Blockera IP-adress",
"blockiptext"           => "Anv&auml;nd formul&auml;ret nedan f&ouml;r att blockera skriv&aring;tkomst 
fr&aring;n en viss IP-adress
Detta ska bara genomf&ouml;ras f&ouml;r att stoppa klotter och
&ouml;verst&auml;mma med [[Wikipedia:Politik|Wikipedia politik]].
Fyll i anledningen till blockering nedan (till exempel vilka artiklar som klottrats ner).",
"ipaddress"             => "IP-adress",
"ipbreason"             => "Anledning",
"ipbsubmit"             => "Blockera den h&auml;r IP-adressen",
"badipaddress"          => "Du har inte skrivit IP-adressen korrekt.",
"noblockreason"         => "Du m&aring;ste ange en anledning till varf&ouml;r du blockerar.",
"blockipsuccesssub"     => "Blockeringen lyckades",
"blockipsuccesstext"    => "IP-adressen \"$1\" har blockerats.
<br>Se [[Speciel:Ipblocklist|IP blockeringslistan]] f&ouml;r alla blockeringar.",
"unblockip"             => "Ta bort blockering av IP-adress",
"unblockiptext"         => "Anv&auml;nd nedanst&aring;ende formul&auml;r f&ouml;r att &aring;terst&auml;lla skrivr&auml;ttigheten f&ouml;r en tidigare blockerad IP-adress.",
"ipusubmit"             => "Ta bort blockering f&ouml;r denna adress",
"ipusuccess"            => "Blockeringen f&ouml;r IP-adressen \"$1\" har tagits bort",
"ipblocklist"           => "Lista &ouml;ver blockerade IP-adresser",
"blocklistline"         => "$1, $2 blockerade $3",
"blocklink"             => "blockera",
"unblocklink"           => "ta bort blockering",
"contribslink"          => "bidrag",

// Developer tools 
//
"lockdb"                => "L&aring;s databas",
"unlockdb"              => "L&aring;s upp databas",
"lockdbtext"            => "En l&aring;sning av databasen hindrar alla anv&auml;ndare fr&aring;n att redigera sidor, &auml;ndra inst&auml;llningar och andra saker som kr&auml;ver &auml;ndringar i databasen.
Bekr&auml;fta att du verkligen vill g&ouml;ra detta och att du kommer att l&aring;sa upp databasen n&auml;r underh&aring;llet &auml;r utf&ouml;rt.",
"unlockdbtext"          => "Genom att l&aring;sa upp databasen kommer alla anv&auml;ndare att kunna redigera sidor, &auml;ndra inst&auml;llningar etc. igen.
Bekr&auml;fta att du vill g&ouml;ra detta.",
"lockconfirm"           => "Ja, jag vill verkligen l&aring;sa databasen.",
"unlockconfirm"         => "Ja, jag vill verkligen l&aring;sa upp databasen.",
"lockbtn"               => "L&aring;s databasen",
"unlockbtn"             => "L&aring;s upp databasen",
"locknoconfirm"         => "Du har inte bekr&auml;ftat l&aring;sningen.",
"lockdbsuccesssub"      => "Databasen har l&aring;sts",
"unlockdbsuccesssub"    => "Databasen har l&aring;sts upp",
"lockdbsuccesstext"     => "Wikipediadatabasen &auml;r l&aring;st.
<br>Kom ih&aring;g att ta bort l&aring;sningen n&auml;r du &auml;r f&auml;rdig med ditt underh&aring;ll.",
"unlockdbsuccesstext"   => "Wikipediadatabasen &auml;r uppl&aring;st.",

// SQL query
//
"asksql"                => "SQL-fr&aring;ga",
"asksqltext"            => "Anv&auml;nd nedanst&aring;ende formul&auml;r f&ouml;r att st&auml;lla fr&aring;gor direkt till Wikipedias databas.
Anv&auml;nd enkla citationstecken ('s&aring; h&auml;r') f&ouml;r att markera str&auml;ngar.
Detta belastar ofta servern h&aring;rt, s&aring; anv&auml;nd denna funktion med omtanke.",
"sqlquery"              => "Skriv fr&aring;ga",
"querybtn"              => "Skicka fr&aring;ga",
"selectonly"            => "Andra fr&aring;gor &auml;n \"SELECT\" f&aring;r endast utf&ouml;ras av Wikipedias utvecklare.",
"querysuccessful"       => "Fr&aring;gan genomf&ouml;rdes korrekt",

// Move page
//
"movepage"              => "Flytta sida",
"movepagetext"          => "Formul&auml;ret nedan byter namn p&aring; sidan och flyttar hela dess historia till det nya namnet. Den gamla sidan blir en omdirigeringssida till den nya. L&auml;nkar till den gamla sidan kommer inte att &auml;ndras. Om det finns en diskussionssida kommer den inte att flyttas. <b>OBS!</b> Detta kan inneb&auml;ra en drastisk &auml;ndring p&aring; en popul&auml;r sida; var s&auml;ker p&aring; att du inser konsekvenserna i f&ouml;rv&auml;g.",
"movearticle"           => "Flytta sida",
"movenologin"           => "Ej inloggad",
"movenologintext"       => "Du m&aring;ste vara registrerad anv&auml;ndare och ha <a href=\"" .
  wfLocalUrl( "Speciel:Userlogin" ) . "\">loggat in</a>
f&ouml;r att kunna flytta en sida.",
"newtitle"              => "Till ny titel",
"movepagebtn"           => "Flytta sida",
"pagemovedsub"          => "Sidan har flyttats",
"pagemovedtext"         => "Sidan \"[[$1]]\" har flyttats till \"[[$2]]\".",
"articleexists"         => "Det finns redan en sida med detta namn eller s&aring; &auml;r namnet du angett ogiltigt. V&auml;lj ett annat namn.",
"talkexists"            => "Sidan  flyttades korrekt, men den tilh&ouml;rande diskussionssidan kunde inte flyttas, eftersom det redan existerar en sida med denna nya titel. Du m&aring;ste sammanfoga dem manuellt.",
"movedto"               => "flyttat till",
"movetalk"              => "Flytta &auml;ven \"diskussion\"ssidan, om den finns.",
"talkpagemoved"         => "Sidans diskussionssida flyttades ocks&aring;.",
"talkpagenotmoved"      => "Sidans diskussionssida flyttades <strong>inte</strong>.",

);

class LanguageSv extends Language {

        function getNamespaces() {
                global $wgNamespaceNamesSv;
                return $wgNamespaceNamesSv;
        }

        function getBookstoreList () {
                global $wgBookstoreListSv ;
                return $wgBookstoreListSv ;
        }

        function getDefaultUserOptions () {
                global $wgDefaultUserOptionsSv ;
                return $wgDefaultUserOptionsSv ;
                }

        function getNsText( $index ) {
                global $wgNamespaceNamesSv;
                return $wgNamespaceNamesSv[$index];
        }

        function getNsIndex( $text ) {
                global $wgNamespaceNamesSv;

                foreach ( $wgNamespaceNamesSv as $i => $n ) {
                        if ( 0 == strcasecmp( $n, $text ) ) { return $i; }
                }
		
                // Consider Special: and Speciel: equal... which is preferred?
                if ( 0 == strcasecmp( "speciel", $text ) ) { return -1; }
                return false;
        }

        // inherit specialPage()

        function getQuickbarSettings() {
                global $wgQuickbarSettingsSv;
                return $wgQuickbarSettingsSv;
        }

        function getSkinNames() {
                global $wgSkinNamesSv;
                return $wgSkinNamesSv;
        }

        function getUserToggles() {
                global $wgUserTogglesSv;
                return $wgUserTogglesSv;
        }

        function getLanguageNames() {
                global $wgLanguageNamesSv;
                return $wgLanguageNamesSv;
        }

        function getLanguageName( $code ) {
                global $wgLanguageNamesSv;
                if ( ! array_key_exists( $code, $wgLanguageNamesSv ) ) {
                        return "";
                }
                return $wgLanguageNamesSv[$code];
        }

        function getMonthName( $key )
        {
                global $wgMonthNamesSv;
                return $wgMonthNamesSv[$key-1];
        }

        function getMonthAbbreviation( $key )
        {
                global $wgMonthAbbreviationsSv;
                return $wgMonthAbbreviationsSv[$key-1];
        }

        function getWeekdayName( $key )
        {
                global $wgWeekdayNamesSv;
                return $wgWeekdayNamesSv[$key-1];
        }

        # Inherit userAdjust()

        function date( $ts, $adj = false )
        {
                if ( $adj ) { $ts = $this->userAdjust( $ts ); }

                $d = (0 + substr( $ts, 6, 2 )) . " " .
                  $this->getMonthName( substr( $ts, 4, 2 ) ) . " " .
                  substr( $ts, 0, 4 );
                return $d;
        }

	// "." is used as the character to separate the
	// hours from the minutes in the date output
        function time( $ts, $adj = false )
        {
                if ( $adj ) { $ts = $this->userAdjust( $ts ); }

                $t = substr( $ts, 8, 2 ) . "." . substr( $ts, 10, 2 );
                return $t;
        }

        function timeanddate( $ts, $adj = false )
        {
                return $this->date( $ts, $adj ) . " kl." . $this->time( $ts, $adj );
        }

        function getValidSpecialPages()
        {
                global $wgValidSpecialPagesSv;
                return $wgValidSpecialPagesSv;
        }

        function getSysopSpecialPages()
        {
                global $wgSysopSpecialPagesSv;
                return $wgSysopSpecialPagesSv;
        }

        function getDeveloperSpecialPages()
        {
                global $wgDeveloperSpecialPagesSv;
                return $wgDeveloperSpecialPagesSv;
        }

	function getMessage( $key )
        {
                global $wgAllMessagesSv;
                if( array_key_exists( $key, $wgAllMessagesSv ) )
                        return $wgAllMessagesSv[$key];
                else
                        return Language::getMessage($key);
        }

}

?>