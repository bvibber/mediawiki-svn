<?
# This contains the strings for international/specialized wikis
#
# wikiTextEo.php -- Esperanto
#
#---------------------------------------------------------------

#$wikiSQLServer = "wikieo" ; # The name of the database, actually...
$wikiLogoFile = "/vikio.png" ;
$wikiCharset = "utf-8" ;
$wikiLanguage = "eo" ;	# For Content-Language header, LANG= param, etc.
#$wikiLocale = "eo_X.UTF-8" ; # If it doesn't exist on the local machine, en_US.UTF-8 should do
# For case conversion:
include_once ( "utf8Case.php" ) ;


# Misc stuff
$wikiMainPage = "Ĉefpaĝo" ; # This is actual the title of the article in the database
$wikiErrorPageTitle = "Fuŝ'! Problemo okazis!" ;
$wikiErrorMessage = "<h2>$1!</h2>Revenu al la [[:Main Page|Ĉefpaĝo]]!" ;
#$wikiAllowedSpecialPages = array("userlogin","userlogout","newpages","recentchanges","upload","statistics","lonelypages","popularpages","wantedpages","allpages","randompage","shortpages","listusers","watchlist","special_pages","editusersettings","deletepage","protectpage","contributions","whatlinkshere");
$wikiRecentChangesText = "Sekvu la plej lastaj ŝanĝoj al Vikipedio per ĉi tiu paĝo. [[Bonvenon, novuloj]]!
Bonvolu legi ĉi tiuj paĝojn: [[vikipedio:Oftaj demandoj|Oftaj demandoj]], [[vikipedio:Konsiletoj|Konsiletoj]] (aparte [[vikipedio:Nomoj de titoloj|Nomoj de titoloj]]
kaj [[vikipedio:Neŭtrala vidpunkto|Neŭtrala vidpunkto]]), kaj [[vikipedio:Oftaj eraroj|Oftaj eraroj ĉe Vikipedio]].<br>
Se vi volas, ke la Vikipedio sukcesu, tre gravas ke vi ne aldonu aferojn limigitaj laŭ la [[kopirajto]] de aliuloj.
La leĝa respondeco vere povus malhelpegi la projekton, do bonvolu ne fari tion.<br>
Ankaŭ vidu la [http://meta.wikipedia.com/wiki.phtml?title=special:RecentChanges lastatempan pri-vikipedian diskuton] (angle)" ;
$wikiMetaDescription = "$1... Legu pli ĉi tie ĉe Vikipedio, la libera enciklopedio!" ;

# Used in header/footer
$wikiMainPageTitle = "Wikipedia : La libera enciklopedio" ; # This is the title that is displayed on the main page
$wikiArticleSubtitle = "Ĉe Wikipedia, la libera enciklopedio." ;
$wikiHeaderSubtitle = "La libera enciklopedio" ;
$wikiPrintable = "Presebla versio" ;
$wikiWatch = "Atentigu min pri ŝanĝoj en la artikolo" ;
$wikiNoWatch = "Ne atentigu min pri ŝanĝoj en la artikolo" ;
$wikiTitleTag = "$1: enciklopedia artikolo el Wikipedia" ;
$wikiLogIn = "Salutu" ;
$wikiLogOut = "Adiaŭu" ;
$wikiHelp = "Helpo" ;
$wikiHelpLink = "vikipedio:Helpo" ;
$wikiBlockedIPsLink = "Vikipedio:Forbaritaj_Uzantoj" ;
$wikiTalkBlockedIPsLink = "Priparolu_vikipedion:Forbaritaj_Uzantoj" ;
$wikiPreferences = "Preferoj" ;
$wikiWhatLinksHere = "Paĝoj ligantaj ĉi tien" ;
$wikiPrintLinksMarkup = "i" ; # will be used as <$wikiPrintLinksMarkup> and </$wikiPrintLinksMarkup>
#$wikiAllowedNamespaces = array ( "wikipedia" , "talk" , "user" , "" , "wikipedia talk" , "user talk" ) ;
$wikiTalk = "priparolu" ;
$wikiUser = "uzanto" ;
$wikiWikipedia = "vikipedio" ;
$wikiNamespaceTalk = "Priparolu $1n" ;
$wikiAllowedNamespaces = array ( "vikipedio" , $wikiTalk , $wikiUser , "" , "priparolu vikipedion" , "priparolu uzanton" ) ;
$wikiMyOptions = "Personaĵoj" ;
$wikiMySettings = "Miaj preferoj" ;
$wikiMyself = "Mia paĝo" ;
$wikiShortPages = "Mallongaj paĝoj";
$wikiLongPages = "Longaj paĝoj" ;
$wikiUserList = "Listo de uzantoj" ;
$wikiEditingHistory = "Historio de redaktoj" ;
$wikiAddToWatchlist = "Aldonu al mia atentaro" ;
$wikiEditPage = "Redaktu la paĝon" ;
$wikiHome = "Hejmo" ;
$wikiAbout = "Enkonduko" ;
$wikiFAQ = "Oftaj demandoj" ;
$wikiPageInfo = "Paĝinformo" ;
$wikiLinkedPages = "Alligitaj paĝoj" ;
$wikiShowDiff = "montru ŝanĝojn" ;
$wikiRequests = "Legata $1foje" ;
$wikiEdit = "Redaktado" ;
$wikiPageOptions = "Paĝagadoj" ;
$wikiBrowse = "Foliumado" ;
$wikiFind = "Serĉu" ;
$wikiOK = "Ek!" ;
$wikiFindMore = "Serĉu";
$wikiWikipediaHome = "Ĉefpaĝo" ;
$wikiAboutWikipedia = "Pri Vikipedio" ;
$wikiAutoWikify = "Aŭto-vikiigado (eksperimenta!)" ;
$wikiTopics = "Temoj" ; #??
$wikiWikipediaFAQ = "vikipedio:Oftaj demandoj" ;
$wikiVoteForPage = "Voĉdonu pri la paĝo" ; #??

$wikiNamespaceBackground[$wikiTalk] = "#eeFFFF" ;
$wikiNamespaceBackground[strtr(" ", "_", str_replace("$1", $wikiUser, $wikiNamespaceTalk))] = $wikiNamespaceBackground[$wikiTalk] ;
$wikiNamespaceBackground[strtr(" ", "_", str_replace("$1", $wikiUser, $wikiNamespaceTalk))] = $wikiNamespaceBackground[$wikiTalk] ;
$wikiNamespaceBackground[$wikiWikipedia] = "#eeFFee" ;
$wikiNamespaceBackground[$wikiUser] = "#FFeeee" ;
$wikiNamespaceBackground["log"] = "#FFFFcc" ;
$wikiNamespaceBackground["special"] = "#eeeeee" ;


# Editing
$wikiEditingHelp = "Helpo: kiel redakti?" ;
$wikiWikipediaEditingHelp = "vikipedio:Kiel oni redaktas paĝon" ;
$wikiEditTitle = "Redaktante $1" ;
$wikiCannotEditPage = "<h3>Vi ne rajtas redakti ĉi tiun paĝon!</h3>" ;
$wikiEditConflictMessage = "<h1>Redakta konflikto!</h1>\n<b>Iu ŝanĝis la paĝon post kiam vi ekskribis. La supra tekstujo havas la konservitan tekston. Nur la teksto en la supra tekstujo estos konservita.</b><br>\nRulumu malsupren por vidi vian tekston redaktitan kaj la malsamojn inter la du versioj.<br>\n" ;
$wikiPreviewAppend = "<hr>\n<h2>Antaŭrigardo :</h2>\n$1<hr><h3>Memoru, ĉi tiu estas nur la antaŭrigardo kaj ne jam konservita!</h3>" ;
$wikiSummary = "Resumo:" ;
$wikiDescription = "Priskribo:" ;
$wikiMinorEdit = "Ĉi tiu ŝanĝo estas redakteto." ;
$wikiCopyrightNotice = "Bonvolu noti, ke ĉiu kontribuaĵo al la Vikipedio estu konsiderata eldonita laŭ la
<a href=\"$THESCRIPT?title=GFDL\">GNU Free Documentation License</a>.
Se vi volas, ke via verkaĵo ne estu redaktota senkompate kaj disvastigota laŭvole, ne alklaku \"Konservu\".
Vi ankaŭ ĵuras, ke vi mem verkis ĉi tiun, aŭ ke vi kopiis ĝin el fonto senkopirajta. <b>NE UZU KOPIRAJTITAJN VERKOJN SENPERMESE!</b>" ;
$wikiSave = "Konservu" ;
$wikiPreview = "Antaŭrigardu" ;
$wikiReset = "Restarigu apriorajn" ;
$wikiDontSaveChanges = "<i>Revenu sen konservi la ŝanĝojn</i>" ;
$wikiDescribePage = "Priskribu la novan paĝon ĉiloke." ;

# wikiUser
$wikiNoSuchUser = "<font color=red>Nekonata uzanto $1!</font>" ;
$wikiWrongPassword = "<font color=red>Malĝusta pasvorto por uzanto $1!</font>" ;
$wikiYouAreLoggedIn = "$1, vi estas salutinta!" ;
$wikiUserError = "Eraro okazis pri \"$1\"" ;

# wikiPage
$wikiNoSuchSpecialPage = "<h1>Ne ekzistas speciala paĝo \"$1\"!</h1>" ;
$wikiOldVersion = "<br><font size=-1>Ĉi tiu estas la malnova version #$1; vidu la <a href=\"$THESCRIPT?title=$2\">aktualan version</a></font>" ;
$wikiRedirectFrom = "(alidirektita el $1)" ;
$wikiRecentChanges = "Lastaj Ŝanĝoj" ;
$wikiRecentChangesLink = "RecentChanges" ; # "special:" is added automatically
$wikiSpecialPagesLink = "Special_pages" ; # "special:" is added automatically
$wikiEditThisPage = "Redaktu ĉi tiun paĝon" ;
$wikiDeleteThisPage = "Forigu ĉi tiun paĝon" ;
$wikiUpload = "Alŝutu dosieron" ;
$wikiHistory = "Historio" ;
$wikiRandomPage = "Hazarda Paĝo" ;
$wikiSpecialPages = "Specialaj Paĝoj" ;
$wikiEditHelp = "<i>Vi povas legi helptekston ĉi tie.</i>" ;
$wikiStatistics = "Statistiko" ;
$wikiNewPages = "Novaj paĝoj" ;
$wikiOrphans = "Orfoj" ;
$wikiMostWanted = "Plej dezirataj" ;
$wikiAllPages = "Ĉiu paĝo" ;
$wikiStubs = "Malplenaj artikoloj" ;
$wikiLongPages = "Longaj artikoloj" ;
$wikiListUsers = "Listigu uzantojn" ;
$wikiMyWatchlist = "Mia Atentataro" ;
$wikiBeginDiff = "MALSAMOJ KOMENCAS" ;
$wikiEndDiff = "MALSAMOJ FINAS" ;
$wikiDiffLegend = "<font color=#2AAA2A>Verdan tekston</font> oni aldonis aŭ ŝanĝis, <font color=#AAAA00>flavan tekston</font> oni ŝanĝis aŭ forigis." ;
$wikiDiffFirstVersion = "Ĉi tiu estas la unua versio de la artikolo. Ĉiu teksto estas nova!<br>\n" ;
$wikiDiffImpossible = "Ĉi tiu estas la unua versio de la artikolo. Ĉiu teksto estas nova!<br>\n" ;
$wikiSearch = "Serĉu" ;
$wikiOtherNamespaces = "<b>Aliaj funkcioj :</b> " ; # Not sure what to call these
$wikiCategories = "<b>Kategorioj :</b> " ;
$wikiThisCategory = "Artikoloj en ĉi tiu kategorio" ;
$wikiCounter = "$1 foje oni jam legis ĉi tiun paĝon." ;
$wikiBlockIPTitle = "Forbaru IP-on (nur sistemestroj)" ;
$wikiBlockIPText = "IP $1 estis forbarita de $2" ;
$wikiBlockInvalidIPAddress = "\"$1\" estas malvalida IP-adreso; neeblas forbari tiun." ;
$wikiBlockExplain = "Vi forbaros la IP-adreson
[$wikiCurrentServer$THESCRIPT?title=special:contributions&theuser=\$1 \$1]. Depost vi forbaros ĝin,
Vikipedio-vizitantoj el tiu IP-adreso ne povos redakti artikolojn, krom se sistemestro
poste fortranĉus ĝin el la listo de forbaritoj ĉe [[$wikiBlockedIPsLink]].
Se vi certas, ke vi volas forbari ĉi tiun uzanton, bonvolu enskribi noteton pri la kialo
en la malsupran skatolon kaj alklaku &quot;Forbaru&quot;. Vi povas skribi pli longan
klarigon en [[$wikiTalkBlockedIPsLink]]." ;
$wikiIPblocked = "<font color=\"red\" size=\"+1\">Vian IP-on oni forbaris! Viajn redaktojn la sistemo ne konservos. Bonvolu kontakti sistemestron por forigi la baradon, aŭ reprovu antaŭ iom de tempo.</font>" ;
$wikiBugReports = "Raportu Teknikajn Problemojn" ;
$wikiBugReportsLink = "vikipedio:Teknikaj Problemoj" ;
$wikiPrintFooter = "<hr>Ĉi tiu artikolo estis ĉerpita el <b>Wikipedia</b> (<a href=\"$1\">$1</a>),
la libera, senpaga encikopedia surreta. La artikolo troviĝas ĉe <a href=\"$2\">$2</a>" ;

# Language interwiki names
$wikiOtherLanguagesText = "Aliaj lingvoj : $1" ;

# date
$wikiDate = array () ;
$wikiDate["monday"] = "lundo" ;
$wikiDate["tuesday"] = "mardo" ;
$wikiDate["wednesday"] = "merkredo" ;
$wikiDate["thursday"] = "ĵaŭdo" ;
$wikiDate["friday"] = "vendredo" ;
$wikiDate["saturday"] = "sabato" ;
$wikiDate["sunday"] = "dimanĉo" ;
$wikiDate["january"] = "januaro" ;
$wikiDate["february"] = "februaro" ;
$wikiDate["march"] = "marto" ;
$wikiDate["april"] = "aprilo" ;
$wikiDate["may"] = "majo" ;
$wikiDate["june"] = "junio" ;
$wikiDate["july"] = "julio" ;
$wikiDate["august"] = "aŭgusto" ;
$wikiDate["september"] = "septembro" ;
$wikiDate["october"] = "oktobro" ;
$wikiDate["november"] = "novembro" ;
$wikiDate["december"] = "decembro" ;

#-----------------------------------------------------------------------
## strings for specialFunctions.php

# Log out / log in
$wikiGoodbye = "<h1>Adiaŭ, $1!</h1>" ;
$wikiWelcomeCreation = "<h1>Bonvenon, $1!</h1><font color=red>Ne forgesu proprigi viajn Vikipedio-preferojn!</font><br>Via konto estas kreita. Bonvolu alklaku \"Salutu\" refoje por saluti!" ;
$wikiLoginPageTitle = "Salutu uzanton" ;
$wikiYourName = "Via salutnomo&nbsp; : " ;
$wikiYourPassword = "Via pasvorto&nbsp;&nbsp; : " ;
$wikiYourPasswordAgain = "Retajpu pasvorton : " ;
$wikiNewUsersOnly = " (nur novaj uzantoj)" ;
$wikiRememberMyPassword = "Memoru mian pasvorton (per kuketo)." ;
$wikiLoginProblem = "<b>Okazis problemon pri via salutado.</b><br>Bonvolu reprovu!" ;
$wikiAlreadyLoggedIn = "<font color=red><b>Uzanto $1, via estas jam salutinta!</b></font><br>\n" ;
$wikiPleaseLogIn = "<h1>Bonvolu saluti:</h1>\n" ;
$wikiAreYouNew = "Se vi estas nove ĉe vikipedio kaj volas akiri uzantokonton, bonvolu tajpu salutonomon, pasvorton, kaj refoje pasvorton.<br>\n
Laŭvole vi rajtas enmeti vian retpoŝtadreson por ke ni povu sendu al vi novan pasvorton se vi perdas la nunan." ;
$wikiLogIn = "Salutu" ;

# User preferences
$wikiUserSettings = "Preferoj laŭ Uzanto" ;
$wikiUserSettingsError = "Vi ne estas salutinta! Bonvolu [[special:userLogin|saluti]] aŭ iri al la  [[:Main Page|Ĉefpaĝo]]" ;
$wikiUserSettingsNewPasswordError = "<h1><font color=red>La novaj pasvortoj ne egalas. PASVORTO RESTAS NEŜANĜITA!</font></h1>\n" ;
$wikiUserSettingsSaved = "<font color=red size=+1>Viaj preferoj estas konservitaj!</font>" ;
$wikiLoggedInAs = "<b>Vi salutis kiel [[$wikiUser:$1|$1]]. ";
$wikiID_Help = "Via interna identigilo estas $1.</b> Vi povas trovi helpon [[vikipedio:Helpo/Preferoj|ĉi tie]]." ;
$wikiQuickBarSettings = "Preferoj pri ilobreto :" ;
$wikiSettingsStandard = "norma" ;
$wikiSettingsNone = "Neniu" ;
$wikiSettingsLeft = "Maldekstre" ;
$wikiSettingsRight = "Dekstre" ;
$wikiOldPassword = "Malnova pasvorto&nbsp; : " ;
$wikiNewPassword = "Nova pasvorto &nbsp; &nbsp; : " ;
$wikiSkin = "Etoso :" ;
$wikiStarTrek = "Star Trek" ;
$wikiShowHoverBox = "Montru ŝpruchelpilon super viki-ligiloj" ;
$wikiUnderlineLinks = "Substreku ligilojn" ;
$wikiNewTopicsRed = "Ruĝigu novajn paĝnomojn" ;
$wikiJustifyParagraphs = "Alkadrigu liniojn" ;
$wikiShowRecentChangesTable = "Montru <i>Lastajn Ŝangojn</i> kiel tabelo" ;
$wikiHideMinorEdits = "Kaŝu malgrandajn redaktetojn ĉe <i>Lastaj Ŝanĝoj</i>" ;
$wikiDoNumberHeadings = "Aŭtomate nombrigu sekciojn" ;
$wikiViewWithFrames = "Montru paĝojn per kadroj <i>(eksperimenta, NUR kun Konkeranto!)</i>" ;
$wikiTurnedOn = "jes" ;
$wikiTurnedOff = "ne" ;
$wikiTextboxDimensions = "Grando de redaktejo  :" ;
$wikiCols = "Kolumnoj : " ;
$wikiRows = "Linioj &nbsp; : " ;
$wikiYourEmail = "Via retpoŝtadreso : " ;
$wikiResultsPerPage = "Montru ĝis $1 da serĉrezultoj po unu paĝo" ;
$wikiTimeDiff = "Horzono : $1 horoj post la servilo" ;
$wikiOutputEncoding = "Montru supersignoj per : ";

# Misc
$wikiLonelyPagesTitle = "La Orfoj" ;
$wikiLonelyPagesText = "'''La jenaj artikoloj ekzistas, sed neniu paĝo ligas al ilin!''' (la unuaj 50)<br>
''Priparolu: paĝoj, malplenaj paĝoj, kaj alidirektoj '''ne''' listiĝas ĉi tie.''\n\n" ;
$wikiAllPagesTitle = "Indekso de Ĉiu Paĝo" ;
$wikiAllPagesText = "'''Jen ĉiu artikolo en la datumaro!'''\n\n" ;
$wikiSearchTitle = "Serĉu" ;
$wikiSearchedVoid = "Ĉar vi serĉis nenion, vi ĵus trovis tion." ;
$wikiNoSearchResult = "Bedaŭrinde, ni ne povis trovi artikolon, kiu enhavas \"$1\" en titolo aŭ enhavo.</h2>" ;
$wikiUnsuccessfulSearch = "Malsukcesa serĉo de $1" ;
$wikiUnsuccessfulSearches = "vikipedio:Malsukcesaj serĉoj ($1)" ; # $1 becomes "YEAR-MONTH"
$wikiSearchHelp = "Bonvolu legi la [[vikipedio:Kiel serĉi|helppaĝon pri serĉado]]." ;
$wikiFoundHeading = "Artikoloj ĉe Vikipedio" ;
$wikiFoundText = "Trovis $1ope ''$2''. Po ĉiu artikolo, montriĝas la unua paragrafo, kaj la plej frua paragrafo enhavanta ''$2''." ;
$wikiUserlistTitle = "Listo de Uzantoj" ;
$wikiUserlistText = "'''Jen ĉiu uzanto de vikipedio (kiu havas konton)!'''" ;
$wikiRecentChangesTitle = "Lastaj Ŝanĝoj" ;
$wikiRecentChangesLastDays = "Jen la <b>$1</b> lastaj ŝanĝoj ĉe Vikipedio dum la <b>$2</b> lastaj tagoj." ;
$wikiRecentChangesSince = "Jen la <b>$1</b> lastaj ŝanĝoj ĉe Vikipedio ekde <b>$2</b>." ;
$wikiViewLastDays = "Montru la $1 lastajn tagojn" ;
$wikiViewMaxNum = "Montru la $1 lastajn ŝanĝojn" ;
$wikiListOnlyNewChanges = "Montru nur novajn ŝanĝojn" ;
$wikiNewPagesTitle = "Novaj Paĝoj" ;
$wikiNewPagesText = "Jen la plej lastaj <b>$1</b> novaj paĝoj ĉe Vikipedio dum la <b>$2</b> lastaj tagoj." ;
$wikiRCLegend = " <b>Simboloj :</b> $1=Malgranda redakteto ; $2=Nova artikolo." ;
$wikiDiff = "(malsamoj)" ;
$wikiChange = "ŝanĝo" ;
$wikiChanges = "ŝanĝoj" ;
$wikiWatchYes = "\"$1\" aldonita al via [[special:WatchList|atentejo]]." ;
$wikiWatchNo = "\"$1\" forigita el via [[special:WatchList|atentejo]]." ;
$wikiWatchlistTitle = "Atentejo" ;
$wikiWatchlistText = "'''Nuntempe, vi priatentas la jenajn artikolojn :''' " ;

# Statistics
$wikiStatisticsTitle = "Statistiko pri Artikoloj" ;
$wikiStatTotalPages = "Estas $1 paĝoj en la datumaro." ;
$wikiStatTalkPages = "Estas $1 '''Priparolu''' paĝoj." ;
$wikiStatCommaPages = "Estas $1 paĝoj kun komo kiuj ''ne'' estas '''Priparolu''' paĝoj." ;
$wikiStatWikipediaNoTalk = "Estas $1 kiu enhavas \"ikipedi\" en la titolo kaj ''ne'' estas '''Priparolu''' paĝoj." ;
$wikiStatSubNoTalk = "Estas $1 subpaĝoj kiuj ''ne'' estas '''Priparolu''' paĝoj." ;
$wikiStatNoTalk = "Estas do proksimume $1 artikoloj, inkluzive de subpaĝoj (krom '''Priparolu''')." ;
$wikiStatArticles = "Aŭ, estas proksimume $1 artikoloj, ignorante ĉiun subpaĝon!" ;
$wikiStatJunk = "Finfine, estas proksimume $1 forĵetindaj paĝoj :-(" ;
$wikiStatOld = "Kaj, estas $1 malnovaj versioj de paĝoj en la datumaro, do meznombre $2 malnovaj paĝoj po unu aktiva paĝo." ;
$wikiUserStatistics = "Statistiko pri Uzantoj" ;
$wikiStatUsers = "Nun estas $1 [[special:ListUsers|uzantoj]] subskribintaj." ;
$wikiStatSysops = "$1 el ili estas sistemestroj." ;

# Upload
$wikiUploadTitle = "Alŝutejo" ;
$wikiUploadDenied = "Vi estas nek editoro nek sistemestro. Revenu al la <a href=\"$THESCRIPT?action=upload\">Alŝutejo</a>" ;
$wikiUploadDeleted = "Dosiero <b>$1</b> forigita!" ;
$wikiUploadDelMsg1 = "*Je $3, [[$wikiUser:$1|$1]] forigis dosieron '''$2'''\n" ;
$wikiUploadDelMsg2 = "Forigi dosieron $1" ;
$wikiUploadAffirm = "<nowiki>Vi devas aserti, ke la dosiero ne malsekvas kopirajtleĝon. Revenu al la <a href=\"$THESCRIPT?title=special:upload\">Alŝutejo</a></nowiki>" ;
$wikiUploadFull = "Bedaŭrinde, nia disko preskaŭ plenas. Ni ne povas lasi vin alŝuti dosierojn nuntempe." ;
$wikiUploadSuccess = "Dosiero <b>$1</b> estas sukcese alŝutita!" ;
$wikiUploadSuccess1 = "*Je $1, $2 alŝutis dosieron '''$3'''$4\n" ;
$wikiUploadSuccess2 = "Alŝuto de dosiero $1" ;
#$wikiUploadText = "<h2>Instrukcio:</h2>\n" ;
$wikiUploadText = "<ul><li><strong>Uzu ĉi tiun formulon por alŝuti diversajn dosierojn</strong></li>
<li>Por anstataŭigi jam-alŝutitan dosieron (ekz., novan version de la artikolo),
nur re-alŝuti la saman dosieron. Sed antaŭe, trarigardu la liston malsupran kaj
certiĝu, ĉu vi ne ŝanĝis la nomon.</li>
<li><strong>Jen kiel alŝuti vian dosieron.</strong>Alklaku &quot;Foliumu...&quot; (<i>Browse</i> ktp) por serĉi tiun dosieron,
kiun vi volas alŝuti, en via disko. Ĉi tiu malfermiĝos &quot;Elektu Dosieron&quot; fenestron.</li>
<li>Kiam vi trovis la dosieron, alklaku &quot;Malfermu&quot;.
Ĉi tiu elektos la dosieron kaj malfermos la &quot;Elektu Dosieron&quot; fenestron.</li>
<li>Ne forgesu, jesi la aserton pri kopirajto!</li>
<li>Tiam, alklaku &quot;Alutu&quot;. La dosiero komencos alŝutiĝi.
Tio ĉi eble daŭros iom, se ĝi estas granda dosiero kaj via Interret-konektado estas malrapida.</li>
<li>Mesaĝo informos vin, kiam la dosiero estas sukcese alŝutita.</li>
<li>Vi povas alŝuti iom ajn da dosieroj, kiom vi volas. Bonvolu ne provu paneigi nian servilon, ha ha.</li>
<li>Ĉiu alŝuto kaj forigo estas registrata en la <a href=\"$THESCRIPT?title=Log:Uploads\">alŝuta loglibro</a>.</li>
</ul>\n";
$wikiUploadAffirmText = "Mi ĉi tiel asertas tion, ke ĉi tiu dosiero <strong>ne estas kopirajtita</strong>,
aŭ tion, ke mi propras la kopirajton de ĉi tiu dosiero, kaj donas ĝian uzrajton laŭ la permisilo <a href=\"$THESCRIPT?title=GFDL\">GFDL</a>." ;
$wikiUploadButton = "Alŝutu" ;
$wikiUploadPrev = "Jam alŝutitaj dosieroj:" ;
$wikiUploadSize = "Grando (bitokoj)" ;
$wikiFileRemoval = "Forigado de dosiero" ;
$wikiUploadRemove = "Klaku ĉi tie por forigi $1." ;

# Misc
$wikiHistoryTitle = "Historio de $1" ;
$wikiHistoryHeader = "Jen la historio de <a href=\"$THESCRIPT?title=$1\">$2</a>" ;
$wikiCurrentVersion = "aktuala" ;
$wikiSpecialTitle = "Specialaj Paĝoj" ;
$wikiSpecialText = "<b>This is a list of special pages.</b> Some of them are only available if you are logged in. If you are logged in, you can have this list automatically displayed on the right or left of each page as a QuickBar.<br><br>" ;
$wikiStubTitle = "Mallongaj 'stumpartikoloj'" ;
$wikiLongPagesTitle = "La plej longaj artikoloj" ;
$wikiStubText = "'''Jen ĉiuj artikoloj en la datumaro, ordigitaj laŭ longo ekde la malplej longa.'''<br>
''Alidirektaj paĝoj kaj flankfunkciaj paĝoj (ekzemple Priparolu:) '''ne''' listiĝas ĉi tie!''\n\n" ;
$wikiLongPagesText = "'''Jen cxiuj artikoloj en la datumaro, ordigitaj laŭ longo ekde la plej longa.'''<br>
''Alidirektaj paĝoj kaj flankfunkciaj paĝoj (ekzemple Priparolu:) '''ne''' listiĝas ĉi tie!''\n\n" ;
$wikiStubChars = "$1 literoj" ;
$wikiStubDelete = "<b>Forigu ĉi tiun paĝon!</b>" ;
$wikiStubLinkHere = "$1 artikoloj ligas ĉi tien." ;
$wikiStubShowLinks = "Sxalti \"montru ligilojn\" opcion" ;
$wikiShowLinks = "Montru paĝojn ligantajn al $1" ;
$wikiDeleteTitle = "Forigante artikolon '$1'" ;
$wikiDeleteDenied = "<font size=+3>Vi ne rajtas forigi ĉi tiun paĝon!</font>" ;
$wikiDeleteSuccess = "'$1' estas forigita." ;
$wikiDeleteMsg1 = "*$1, [[$wikiUser:$2|$2]] plene forigis paĝon '''$3'''\n" ;
$wikiDeleteMsg2 = "Plena forigo de $1" ;
$wikiDeleteAsk = "Vi forigos la artikolon \"$1\" kaj ĝian tutan historion!<br>
Se vi tutkore certas, ke vi volas fari tion, <a href=\"$THESCRIPT?title=special:deletepage&target=$1&iamsure=yes\">klaku ĉi tie</a>." ;
$wikiProtectTitle = "Protektante artikolon '$1'" ;
$wikiProtectDenied = "<font size=+3>Vi ne rajtas protekti ĉi tiun paĝon!</font>" ;
$wikiProtectNow = "Paĝo '$1' nun estas protektata kiel $2." ;
$wikiProtectText = "<font size=+2>Vi nun povas modifi la protektadon de '$target'</font><br>
<i>Ekzemple, enmetu \"is_sysop\" por malebligi iun krom sistemestroj redakti tiun paĝon. Apartigi plurajn permesojn per komo \",\"</i>" ;
$wikiProtectCurrent = "Aktuala protektado : " ;
$wikiContribTitle = "Kontribuaĵoj de $1" ;
$wikiContribText = "<h1>Kontribuaĵoj de $1 :</h1>\n(Krom ''priparolu'' kaj ''log'' paĝoj)" ;
$wikiContribDenied = "Deklaru uzantonomon!" ;
$wikiLinkhereTitle = "Paĝoj kiuj ligas al $1" ;
$wikiLinkhereBacklink = "Ĉi tiuj artikoloj estas ligataj el [[$1]]:" ;
$wikiLinkhereNoBacklink = "Ĉi tiuj artikoloj ''ne'' estas ligataj el [[$1]]:" ;
$wikiBacklinkNolink = "Neniu artikoloj ligas al [[$1]]!" ;
$wikiBacklinkFollowing = "La jenaj artikoloj ligas al [[$1]]:" ;
$wikiRedirectLinks = "La jenaj artikoloj ligas per alidirektoj:" ;
$wikiLinkVia = "(per $1)" ;
$wikiWantedTitle = "La Plej Dezirataj Paĝoj" ;
$wikiWantedText = "'''La jenaj artikoloj ne ekzistas, sed aliaj artikoloj ligas al ili!''' (la unuaj 50)<br>\n" ;
$wikiWantedToggleNumbers = "Klaku ĉi tien por ŝalti montri paĝtitolojn kiuj komencas per cifroj (aktuale $1)" ;
$wikiWantedLine = "$1 deziras de <b>$2</b> artikoloj <nowiki>(vidu tiuj <a href=\"$3\">paĝojn kiuj ligas al \"$4\"</a></nowiki>)" ;
$wikiLastChange = "Laste redaktita $1" ;
$wikiLastChangeCologne = "Laste redaktita:<br>\n<font size=-2>&nbsp;&nbsp;$1</font>" ;
$wikiPopularPages = "Plej vizitataj" ;
$wikiPopularTitle = "Plej Vizitataj Paĝoj" ;
$wikiRefreshThisPage = "Rekalkulu ĉi tiun liston" ;
$wikiResourcesWarning = "(Bonvolu faru ĉi tion nur malofte, ĉar tiu funkcio alte taksas la sistemrisorcojn)" ;
$wikiNoRefresh = "(La paĝon estis rekalkulita antaŭ nur $1 minutoj; bonvolu atendi kaj reprovi post $2 minutoj.)" ;
$wikiLastRefreshed = "Laste rekalkulita $1" ;
$wikiValidate = "Kontroli la laŭnormecon de ĉi tiu paĝo" ;

$wikiBlockIP = "Forbaru ĉi tiun retuzanton" ;
$wikiNostalgy = "Nostalgio" ;
$wikiCologneBlue = "Kolonja Bluo" ;

# Vote
$wikiVoteReason = "Kialo por voĉdoni : " ;


#---------------------------
#Functions
function wikiGetDateEo ( $x ) { # Used in RecentChangesLayout in special_functions.php
	global $wikiDate ;
	$dayName = $wikiDate [ strtolower ( date ( "l" , $x ) ) ];
	$monthName = $wikiDate [ strtolower ( date ( "F" , $x ) ) ];
	$dayNumber = date ( "j" , $x ) ;
	$year = date ( "Y" , $x ) ;
	return "${dayName}n, la ${dayNumber}a de $monthName, $year" ;
	}
$wikiGetDate = wikiGetDateEo;

# In theory, this could be expanded to allow general conversion of the
# character encoding used in the database to another encoding optionally
# used on the browser end.
# For now, hardcoded to convert Esperanto text in "X-Sistemo" to/from UTF-8.

$wikiEncodingCharsets = array("utf-8", "utf-8");
$wikiEncodingNames = array("Unikodo", "X-Sistemo"); # Local names

# callback function for RecodeOutput
function wikiRecodeOutputRaw ( $textregs ) {
	$ux = array (
		"x" => "xx" , "X" => "Xx" ,
		"\xc4\x88" => "Cx" , "\xc4\x89" => "cx" ,
		"\xc4\x9c" => "Gx" , "\xc4\x9d" => "gx" ,
		"\xc4\xa4" => "Hx" , "\xc4\xa5" => "hx" ,
		"\xc4\xb4" => "Jx" , "\xc4\xb5" => "jx" ,
		"\xc5\x9c" => "Sx" , "\xc5\x9d" => "sx" ,
		"\xc5\xac" => "Ux" , "\xc5\xad" => "ux"
		) ;
	# Double Xs only if they follow cxapelutaj literoj or other Xs. It's not canon, but it's a lot prettier
	$text = strtr ( $textregs[2] , $ux ) ;
	return $textregs[1] . preg_replace ( "/((?:^|[^cghjsux])x)x(?!x)/i" , "\$1" , $text ) ;
	}

function wikiRecodeOutputEo($text) {
	# Don't convert anything inside a tag (URLs, etc)
	# $pageText =~ s/(^|>)((.|\n)*?)(<|$)/$1.&RecodeOutput($2).$4/ige;
	global $user;
	if ( $user->options["encoding"] == 1 ) {
		return preg_replace_callback ( "/(^|>)((?:.|\n)*?)(?=<|$)/" , "wikiRecodeOutputRaw" , $text ) ;
	} else {
		# We have to escape Xs in <textarea> and <input> to keep the input conversion working
		#$fixXs = "\"\$1\" . strtr(\"\$2\" , array ( \"x\" => \"xx\" , \"X\" => \"Xx\", \"\\'\" => \"'\") )" ;
		$text = preg_replace_callback ( "/(<textarea[^>]*>)((?:.|\n)*?)(?=<\/textarea)/i", "wikiRecodeOutputRaw" , $text ) ;
		return preg_replace_callback ( "/(<input[^>]+type\=\"?text\"?[^>]+value\=\")([^\"]*)(?=\")/i", "wikiRecodeOutputRaw" , $text ) ;
	}
	return $text;
	}
	$wikiRecodeOutput = wikiRecodeOutputEo ;

function wikiRecodeInputEo($text) {
	# Note that we *always* convert double-Xs into single Xs.
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

	return preg_replace ( "/([cghjsux]x)(?=(?:xx)*[^x\$])/ei" , "\$xu[\"\$1\"]" , $text ) ;
	}
	$wikiRecodeInput = wikiRecodeInputEo ;

?>
