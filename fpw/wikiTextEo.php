<?
# This contains the strings for international/specialized wikis
#
# wikiTextEo.php -- Esperanto
#
#---------------------------------------------------------------

$wikiSQLServer = "wikieo" ; # The name of the database, actually...
$wikiLogoFile = "/vikio.png" ;
$wikiCharset = "utf-8" ;
$wikiLanguage = "eo" ;	# For Content-Language header, LANG= param, etc.
$wikiLocale = "eo_XX.UTF-8" ; # If it doesn't exist on the local machine, en_US.UTF-8 should do

# Misc stuff
$wikiMainPage = "Cxefpagxo" ; # This is actual the title of the article in the database
$wikiErrorPageTitle = "Fusx'! Problemo okazis!" ;
$wikiErrorMessage = "<h2>$1!</h2>Revenu al la [[:Main Page|Cxefpagxo]]!" ;
$wikiAllowedSpecialPages = array("userlogin","userlogout","newpages","recentchanges","upload","statistics","lonelypages","popularpages","wantedpages","allpages","randompage","shortpages","listusers","watchlist","special_pages","editusersettings","deletepage","protectpage","contributions","whatlinkshere");
$wikiRecentChangesText = "Sekvu la plej lastaj sxangxoj al Vikipedio per cxi tiu pagxo. [[Bonvenon, novuloj]]!
Bonvolu legi cxi tiuj pagxojn: [[wikipedia:Oftaj demandoj|Oftaj demandoj]], [[wikipedia:Konsiletoj|Konsiletoj]] (aparte [[wikipedia:Nomoj de titoloj|Nomoj de titoloj]]
kaj [[wikipedia:Neuxtrala vidpunkto|Neuxtrala vidpunkto]]), kaj [[wikipedia:Oftaj eraroj|Oftaj eraroj cxe Vikipedio]].<br>
Se vi volas, ke la Vikipedio sukcesu, tre gravas ke vi ne aldonu aferojn limigitaj laux la [[kopirajto]] de aliuloj.
La legxa respondeco vere povus malhelpegi la projekton, do bonvolu ne fari tion.<br>
Ankaux vidu la [http://meta.wikipedia.com/wiki.phtml?title=special:RecentChanges lastatempa pri-vikipedian diskuton] (angle)" ;
$wikiMetaDescription = "$1... Legu pli cxi tie cxe Vikipedio, la libera enciklopedio!" ;

# Used in header/footer
$wikiMainPageTitle = "Wikipedia : La libera enciklopedio" ; # This is the title that is displayed on the main page
$wikiArticleSubtitle = "Cxe Wikipedia, la libera enciklopedio." ;
$wikiPrintable = "Presebla versio" ;
$wikiWatch = "Atentigu min pri sxangxoj en la artikolo" ;
$wikiNoWatch = "Ne atentigu min pri sxangxoj en la artikolo" ;
$wikiTitleTag = "$1: enciklopedia artikolo el Wikipedia" ;
$wikiLogIn = "Salutu" ;
$wikiLogOut = "Adiauxu" ;
$wikiHelp = "Helpo" ;
$wikiHelpLink = "Helpo" ; # "special:" is added automatically
$wikiPreferences = "Preferoj" ;
$wikiWhatLinksHere = "Pagxoj kiuj ligas cxi tien" ;
$wikiPrintLinksMarkup = "i" ; # will be used as <$wikiPrintLinksMarkup> and </$wikiPrintLinksMarkup>
#$wikiAllowedNamespaces = array ( "wikipedia" , "talk" , "user" , "" , "wikipedia talk" , "user talk" ) ;
$wikiTalk = "priparolu" ;
$wikiUser = "uzanto" ;
$wikiNamespaceTalk = "Priparolu $1n" ;
$wikiAllowedNamespaces = array ( "wikipedia" , $wikiTalk , $wikiUser , "" , "priparolu wikipedian" , "priparolu uzanton" ) ;

# Editing
$wikiEditTitle = "Redaktante $1" ;
$wikiCannotEditPage = "<h3>Vi ne rajtas redakti cxi tiun pagxon!</h3>" ;
$wikiEditConflictMessage = "<h1>Redakta konflikto!</h1>\n<b>Iu sxangxis la pagxon post kiam vi ekskribis. La supra tekstujo havas la konservitan tekston. Nur la teksto en la supra tekstujo estos konservita.</b><br>\nRulumu malsupren por vidi vian tekston redaktitan.<br>\n" ;
$wikiPreviewAppend = "<hr>\n<h2>Antauxrigardo :</h2>\n$1<hr><h3>Memoru, cxi tiu estas nur la antauxrigardo kaj ne jam konservita!</h3>" ;
$wikiSummary = "Resumo:" ;
$wikiMinorEdit = "Cxi tiu sxangxo estas redakteto." ;
$wikiCopyrightNotice = "Bonvolu noti, ke cxiun kontribuajxon al la Vikipedio estu konsiderata eldonita laux la
<a href=\"$THESCRIPT?title=GFDL\">GNU Free Documentation License</a>.
Se vi volas, ke via verkajxo ne estu redaktota senkompate kaj disvastigota lauxvole, ne presu \"Konservu\".
Vi ankaux jxuras, ke vi mem verkis cxi tiun, aux ke vi kopiis gxin el fonto senkopirajta. <b>NE UZU KOPIRAJTITAJN VERKOJN SENPERMESE!</b>" ;
$wikiSave = "Konservu" ;
$wikiPreview = "Antauxrigardu" ;
$wikiReset = "Restarigu apriorajn" ;
$wikiDontSaveChanges = "<i>Revenu sen konservi la sxangxojn</i>" ;
$wikiDescribePage = "Priskribu la novan pagxon cxiloke." ;

# wikiUser
$wikiNoSuchUser = "<font color=red>Nekonata uzanto $1!</font>" ;
$wikiWrongPassword = "<font color=red>Malgxusta pasvorto por uzanto $1!</font>" ;
$wikiYouAreLoggedIn = "$1, vi estas salutinta!" ;
$wikiUserError = "Eraro okazis pri \"$1\"" ;

# wikiPage
$wikiNoSuchSpecialPage = "<h1>Ne ekzistas speciala pagxo \"$1\"!</h1>" ;
$wikiOldVersion = "<br><font size=-1>Cxi tiu estas la malnova version #$1; vidu la <a href=\"$THESCRIPT?title=$2\">aktualan version</a></font>" ;
$wikiRedirectFrom = "(alidirektita el $1)" ;
$wikiRecentChanges = "Lastaj Sxangxoj" ;
$wikiRecentChangesLink = "RecentChanges" ; # "special:" is added automatically
$wikiSpecialPagesLink = "Special_pages" ; # "special:" is added automatically
$wikiEditThisPage = "Redaktu cxi tiun pagxon" ;
$wikiDeleteThisPage = "Forigu cxi tiun pagxon" ;
$wikiUpload = "Alsxutu dosieron" ;
$wikiHistory = "Historio" ;
$wikiRandomPage = "Hazarda Pagxo" ;
$wikiSpecialPages = "Specialaj Pagxoj" ;
$wikiEditHelp = "<i>Vi povas legi helptekston cxi tie.</i>" ;
$wikiStatistics = "Statistiko" ;
$wikiNewPages = "Novaj pagxoj" ;
$wikiOrphans = "Orfoj" ;
$wikiMostWanted = "Plej dezirataj" ;
$wikiAllPages = "Cxiu pagxo" ;
$wikiStubs = "Malplenaj artikoloj" ;
$wikiListUsers = "Listigu uzantojn" ;
$wikiMyWatchlist = "Mia Atentataro" ;
$wikiBeginDiff = "MALSAMOJ KOMENCAS" ;
$wikiEndDiff = "MALSAMOJ FINAS" ;
$wikiDiffLegend = "<font color=#2AAA2A>Verdan tekston</font> oni aldonis aux sxangxis, <font color=#AAAA00>flavan tekston</font> oni sxangxis aux forigis." ;
$wikiDiffFirstVersion = "Cxi tiu estas la unua versio de la artikolo. Cxiu teksto estas nova!<br>\n" ;
$wikiDiffImpossible = "Cxi tiu estas la unua versio de la artikolo. Cxiu teksto estas nova!<br>\n" ;
$wikiSearch = "Sercxu" ;
$wikiOtherNamespaces = "<b>Aliaj funkcioj :</b> " ; # Not sure what to call these
$wikiCategories = "<b>Kategorioj :</b> " ;
$wikiThisCategory = "Artikoloj en cxi tiu kategorio" ;

# Language interwiki names
$wikiLanguageNames = array (
	"ar"=>"Araba (Araby)" ,
	"ca"=>"Kataluna (Catalá)" ,
	"zh"=>"Cxina (Hányǔ)" ,
	"dk"=>"Dana (Dansk)" ,
	"nl"=>"Nederlanda (Nederlands)" ,
	"de"=>"Germana (Deutsch)" ,
	"eo"=>"Esperanto" ,
	"fr"=>"Franca (Français)" ,
	"he"=>"Hebrea (Ivrit)" ,
	"hu"=>"Hungara (Magyar)" ,
	"it"=>"Itala (Italiano)" ,
	"ja"=>"Japana (Nihongo)" ,
	"pl"=>"Pola (Polska)" ,
	"pt"=>"Portugala (Português)" ,
	"ru"=>"Rusa (Russkiy)" ,
	"simple"=>"Simpligita Angla (Simplified English)" ,
	"es"=>"Hispana (Castellano)" ,
	"sv"=>"Sveda (Svenska)" ,
	"en"=>"Angla (English)"
	) ;
$wikiOtherLanguagesText = "Aliaj lingvoj : $1" ;

# date
$wikiDate = array () ;
$wikiDate["monday"] = "lundo" ;
$wikiDate["tuesday"] = "mardo" ;
$wikiDate["wednesday"] = "merkredo" ;
$wikiDate["thursday"] = "jxauxdo" ;
$wikiDate["friday"] = "vendredo" ;
$wikiDate["saturday"] = "sabato" ;
$wikiDate["sunday"] = "dimancxo" ;
$wikiDate["january"] = "januaro" ;
$wikiDate["february"] = "februaro" ;
$wikiDate["march"] = "marto" ;
$wikiDate["april"] = "aprilo" ;
$wikiDate["may"] = "majo" ;
$wikiDate["june"] = "junio" ;
$wikiDate["july"] = "julio" ;
$wikiDate["august"] = "auxgusto" ;
$wikiDate["september"] = "septembro" ;
$wikiDate["october"] = "oktobro" ;
$wikiDate["november"] = "novembro" ;
$wikiDate["december"] = "decembro" ;

#-----------------------------------------------------------------------
## strings for specialFunctions.php

# Log out / log in
$wikiGoodbye = "<h1>Adiaux, $1!</h1>" ;
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
$wikiAreYouNew = "Se vi estas nove cxe vikipedio kaj volas akiri uzantokonton, bonvolu tajpu salutonomon, pasvorton, kaj refoje pasvorton.<br>\n" ;
$wikiLogIn = "Salutu" ;

# User preferences
$wikiUserSettings = "Preferoj laux Uzanto" ;
$wikiUserSettingsError = "Vi ne estas salutinta! Bonvolu [[special:userLogin|saluti]] aux iri al la  [[:Main Page|Cxefpagxo]]" ;
$wikiUserSettingsNewPasswordError = "<h1><font color=red>La novaj pasvortoj ne egalas. PASVORTO RESTAS NESXANGXITA!</font></h1>\n" ;
$wikiUserSettingsSaved = "<font color=red size=+1>Viaj preferoj estas konservitaj!</font>" ;
$wikiLoggedInAs = "<b>Vi salutis kiel [[$wikiUser:$1|$1]]. ";
$wikiID_Help = "Via interna identigilo estas $1.</b> Vi povas trovi helpon [[wikipedia:Helpo/Preferoj|cxi tie]]." ;
$wikiQuickBarSettings = "Preferoj pri ilobreto :" ;
$wikiSettingsStandard = "norma" ;
$wikiSettingsNone = "Neniu" ;
$wikiSettingsLeft = "Maldekstre" ;
$wikiSettingsRight = "Dekstre" ;
$wikiOldPassword = "Malnova pasvorto&nbsp; : " ;
$wikiNewPassword = "Nova pasvorto &nbsp; &nbsp; : " ;
$wikiSkin = "Etoso :" ;
$wikiStarTrek = "Star Trek" ;
$wikiShowHoverBox = "Montru sxpruchelpilon super viki-ligiloj" ;
$wikiUnderlineLinks = "Substreku ligilojn" ;
$wikiNewTopicsRed = "Rugxigu novajn pagxnomojn" ;
$wikiJustifyParagraphs = "Alkadrigu liniojn" ;
$wikiShowRecentChangesTable = "Montru <i>Lastajn Sxangojn</i> kiel tabelo" ;
$wikiHideMinorEdits = "Kasxu malgrandajn redaktetojn cxe <i>Lastaj Sxangxoj</i>" ;
$wikiDoNumberHeadings = "Auxtomate nombrigu sekciojn" ;
$wikiViewWithFrames = "Montru pagxojn per kadroj <i>(eksperimenta, NUR kun Konkeranto!)</i>" ;
$wikiTurnedOn = "jes" ;
$wikiTurnedOff = "ne" ;
$wikiTextboxDimensions = "Grando de redaktejo  :" ;
$wikiCols = "Kolumnoj : " ;
$wikiRows = "Linioj &nbsp; : " ;
$wikiYourEmail = "Via retposxtadreso : " ;
$wikiResultsPerPage = "Montru gxis $1 da sercxrezultoj po unu pagxo" ;
$wikiTimeDiff = "Horzono : $1 horoj post la servilo" ;
$wikiOutputEncoding = "Montru supersignoj per : ";

# Misc
$wikiLonelyPagesTitle = "La Orfoj" ;
$wikiLonelyPagesText = "'''La jenaj artikoloj ekzistas, sed neniu pagxo ligas al ilin!''' (la unuaj 50)<br>
''Priparolu: pagxoj, malplenaj pagxoj, kaj alidirektoj '''ne''' listigxas cxi tie.''\n\n" ;
$wikiAllPagesTitle = "Indekso de Cxiu Pagxo" ;
$wikiAllPagesText = "'''Jen cxiu artikolo en la datumaro!'''\n\n" ;
$wikiSearchTitle = "Sercxu" ;
$wikiSearchedVoid = "Cxar vi sercxis nenion, vi jxus trovis tion." ;
$wikiNoSearchResult = "Bedauxrinde, ni ne povis trovi artikolon, kiu enhavas \"$1\" en titolo aux enhavo.</h2>" ;
$wikiUnsuccessfulSearch = "Malsukcesa sercxo de $1" ;
$wikiUnsuccessfulSearches = "wikipedia:Malsukcesaj sercxoj ($1)" ; # $1 becomes "YEAR-MONTH"
$wikiFoundHeading = "Artikoloj cxe Vikipedio" ;
$wikiFoundText = "Trovis $1ope ''$2''. Po cxiu artikolo, montrigxas la unua paragrafo, kaj la plej frua paragrafo enhavanta ''$2''." ;
$wikiUserlistTitle = "Listo de Uzantoj" ;
$wikiUserlistText = "'''Jen cxiu uzanto de vikipedio (kiu havas konton)!'''" ;
$wikiRecentChangesTitle = "Lastaj Sxangxoj" ;
$wikiRecentChangesLastDays = "Jen la <b>$1</b> lastaj sxangxoj cxe Vikipedio dum la <b>$2</b> lastaj tagoj." ;
$wikiRecentChangesSince = "Jen la <b>$1</b> lastaj sxangxoj cxe Vikipedio ekde <b>$2</b>." ;
$wikiViewLastDays = "Montru la $1 lastajn tagojn" ;
$wikiViewMaxNum = "Montru la $1 lastajn sxangxojn" ;
$wikiListOnlyNewChanges = "Montru nur novajn sxangxojn" ;
$wikiNewPagesTitle = "Novaj Pagxoj" ;
$wikiNewPagesText = "Jen la plej lastaj <b>$1</b> novaj pagxoj cxe Vikipedio dum la <b>$2</b> lastaj tagoj." ;
$wikiRCLegend = " <b>Simboloj :</b> $1=Malgranda redakteto ; $2=Nova artikolo." ;
$wikiDiff = "(malsamoj)" ;
$wikiChange = "sxangxo" ;
$wikiChanges = "sxangxoj" ;
$wikiWatchYes = "\"$1\" aldonita al via [[special:WatchList|atentejo]]." ;
$wikiWatchNo = "\"$1\" forigita el via [[special:WatchList|atentejo]]." ;
$wikiWatchlistTitle = "Atentejo" ;
$wikiWatchlistText = "'''Nuntempe, vi priatentas la jenajn artikolojn :''' " ;

# Statistics
$wikiStatisticsTitle = "Statistiko pri Artikoloj" ;
$wikiStatTotalPages = "Estas $1 pagxoj en la datumaro." ;
$wikiStatTalkPages = "Estas $1 '''Priparolu''' pagxoj." ;
$wikiStatCommaPages = "Estas $1 pagxoj kun komo kiuj ''ne'' estas '''Priparolu''' pagxoj." ;
$wikiStatWikipediaNoTalk = "Estas $1 kiu enhavas \"ikipedia\" en la titolo kaj ''ne'' estas '''Priparolu''' pagxoj." ;
$wikiStatSubNoTalk = "Estas $1 subpagxoj kiuj ''ne'' estas '''Priparolu''' pagxoj." ;
$wikiStatNoTalk = "Estas do proksimume $1 artikoloj, inkluzive de subpagxoj (krom '''Priparolu''')." ;
$wikiStatArticles = "Aux, estas proksimume $1 artikoloj, ignorante cxiun subpagxon!" ;
$wikiStatJunk = "Finfine, estas proksimume $1 forjxetindaj pagxoj :-(" ;
$wikiStatOld = "Kaj, estas $1 malnovaj versioj de pagxoj en la datumaro, do meznombre $2 malnovaj pagxoj po unu aktiva pagxo." ;
$wikiStatUsers = "Nun estas $1 [[special:ListUsers|uzantoj]] subskribintaj." ;
$wikiStatSysops = "$1 el ili estas sistemestroj." ;

# Upload
$wikiUploadTitle = "Alsxutejo" ;
$wikiUploadDenied = "Vi estas nek editoro nek sistemestro. Revenu al la <a href=\"$THESCRIPT?action=upload\">Alsxutejo</a>" ;
$wikiUploadDeleted = "Dosiero <b>$1</b> forigita!" ;
$wikiUploadDelMsg1 = "*Je $now, [[$wikiUser:$1|$1]] forigis dosieron '''$2'''\n" ;
$wikiUploadDelMsg2 = "Forigi dosieron $1" ;
$wikiUploadAffirm = "<nowiki>Vi devas aserti, ke la dosiero ne malsekvas kopirajtlegxon. Revenu al la <a href=\"$THESCRIPT?title=special:upload\">Alsxutejo</a></nowiki>" ;
$wikiUploadFull = "Bedauxrinde, nia disko preskaux plenas. Ni ne povas lasi vin alsxuti dosierojn nuntempe." ;
$wikiUploadSuccess = "Dosiero <b>$1</b> estas sukcese alsxutita!" ;
$wikiUploadSuccess1 = "*Je $1, $2 alsxutis dosieron '''$3'''\n" ;
$wikiUploadSuccess2 = "Alsxuto de dosiero $1" ;
#$wikiUploadText = "<h2>Instrukcio:</h2>\n" ;
$wikiUploadText = "<ul><li><strong>Uzu cxi tiun formulon por alsxuti diversajn dosierojn</strong></li>
<li>Por anstatauxigi jam-alsxutitan dosieron (ekz., novan version de la artikolo),
nur re-alsxuti la saman dosieron. Sed antauxe, trarigardu la liston malsupran kaj
certigxu, cxu vi ne sxangxis la nomon.</li>
<li><strong>Jen kiel alsxuti vian dosieron.</strong>Alklaku &quot;Foliumu...&quot; (<i>Browse</i> ktp) por sercxi tiun dosieron,
kiun vi volas alsxuti, en via disko. Cxi tiu malfermigxos &quot;Elektu Dosieron&quot; fenestron.</li>
<li>Kiam vi trovis la dosieron, alklaku &quot;Malfermu&quot;.
Cxi tiu elektos la dosieron kaj malfermos la &quot;Elektu Dosieron&quot; fenestron.</li>
<li>Ne forgesu, jesi la aserton pri kopirajto!</li>
<li>Tiam, alklaku &quot;Alsxutu&quot;. La dosiero komencos alsxutigxi.
Tio cxi eble dauxros iom, se gxi estas granda dosiero kaj via Interret-konektado estas malrapida.</li>
<li>Mesagxo informos vin, kiam la dosiero estas sukcese alsxutita.</li>
<li>Vi povas alsxuti iom ajn da dosieroj, kiom vi volas. Bonvolu ne provu paneigi nian servilon, ha ha.</li>
<li>Cxiu alsxuto kaj forigo estas registrata en la <a href=\"$THESCRIPT?title=Log:Uploads\">alsxuta loglibro</a>.</li>
</ul>\n";
$wikiUploadAffirmText = "Mi cxi tiel asertas tion, ke cxi tiu dosiero <strong>ne estas kopirajtita</strong>,
aux tion, ke mi propras la kopirajton de cxi tiu dosiero, kaj donas gxian uzrajton laux la permisilo <a href=\"$THESCRIPT?title=GFDL\">GFDL</a>." ;
$wikiUploadButton = "Alsxutu" ;
$wikiUploadPrev = "Jam alsxutitaj dosieroj:" ;
$wikiUploadSize = "Grando (bitokoj)" ;
$wikiFileRemoval = "Forigado de dosiero" ;
$wikiUploadRemove = "Klaku cxi tie por forigi $1." ;

# Misc
$wikiHistoryTitle = "Historio de $1" ;
$wikiHistoryHeader = "Jen la historio de <a href=\"$THESCRIPT?title=$1\">$2</a>" ;
$wikiCurrentVersion = "aktuala" ;
$wikiSpecialTitle = "Specialaj Pagxoj" ;
$wikiSpecialText = "<b>This is a list of special pages.</b> Some of them are only available if you are logged in. If you are logged in, you can have this list automatically displayed on the right or left of each page as a QuickBar.<br><br>" ;
$wikiStubTitle = "Mallongaj 'stumpartikoloj'" ;
$wikiStubText = "'''Jen cxiuj artikoloj en la datumaro, ordigitaj laux longo.'''<br>''
Alidirektaj pagxoj kaj pagxoj en (namespace) (ekzemple Priparolu:) '''ne''' listigxas cxi tie!''\n\n" ;
$wikiStubChars = "$1 literoj" ;
$wikiStubDelete = "<b>Forigu cxi tiun pagxon!</b>" ;
$wikiStubLinkHere = "$1 artikoloj ligas cxi tien." ;
$wikiDeleteTitle = "Forigante artikolon '$1'" ;
$wikiDeleteDenied = "<font size=+3>Vi ne rajtas forigi cxi tiun pagxon!</font>" ;
$wikiDeleteSuccess = "'$1' estas forigita." ;
$wikiDeleteMsg1 = "*$1, [[$wikiUser:$2|$2]] plene forigis pagxon '''$3'''\n" ;
$wikiDeleteMsg2 = "Plena forigo de $1" ;
$wikiDeleteAsk = "Vi forigos la artikolon \"$1\" kaj gxian tutan historion!<br>
Se vi tutkore certas, ke vi volas fari tion, <a href=\"$THESCRIPT?title=special:deletepage&target=$1&iamsure=yes\">klaku cxi tie</a>." ;
$wikiProtectTitle = "Protektante artikolon '$1'" ;
$wikiProtectDenied = "<font size=+3>Vi ne rajtas protekti cxi tiun pagxon!</font>" ;
$wikiProtectNow = "Pagxo '$1' nun estas protektata kiel $2." ;
$wikiProtectText = "<font size=+2>Vi nun povas modifi la protektadon de '$target'</font><br>
<i>Ekzemple, enmetu \"is_sysop\" por malebligi iun krom sistemestroj redakti tiun pagxon. Apartigi plurajn permesojn per komo \",\"</i>" ;
$wikiProtectCurrent = "Aktuala protektado : " ;
$wikiContribTitle = "Kontribuajxoj de $1" ;
$wikiContribText = "<h1>Kontribuajxoj de $1 :</h1>\n(Krom ''priparolu'' kaj ''log'' pagxoj)" ;
$wikiContribDenied = "Deklaru uzantonomon!" ;
$wikiLinkhereTitle = "Pagxoj kiuj ligas al $1" ;
$wikiLinkhereBacklink = "Cxi tiuj artikoloj estas ligataj el [[$1]]:" ;
$wikiLinkhereNoBacklink = "Cxi tiuj artikoloj ''ne'' estas ligataj el [[$1]]:" ;
$wikiBacklinkNolink = "Neniu artikoloj ligas al [[$1]]!" ;
$wikiBacklinkFollowing = "La jenaj artikoloj ligas al [[$1]]:" ;
$wikiWantedText = "'''La jenaj artikoloj ne ekzistas, sed aliaj artikoloj ligas al ili!''' (la unuaj 50)<br>\n" ;
$wikiWantedToggleNumbers = "Klaku cxi tien por sxalti montri pagxtitolojn kiuj komencas per cifroj (aktuale $1)" ;
$wikiWantedLine = "$1 deziras de <b>$2</b> artikoloj <nowiki>(vidu tiuj <a href=\"$3\">pagxojn kiuj ligas al \"$4\"</a></nowiki>)" ;
$wikiLastChange = "Laste redaktita $1" ;
$wikiPopularPages = "Plej vizitataj" ;
$wikiRefreshThisPage = "Rekalkulu cxi tiun liston" ;
$wikiResourcesWarning = "(Bonvolu faru cxi tion nur malofte, cxar tiu funkcio alte taksas la sistemrisorcojn)" ;
$wikiNoRefresh = "(La pagxon estis rekalkulita antaux nur $1 minutoj; bonvolu atendi kaj reprovi post $2 minutoj.)" ;
$wikiLastRefreshed = "Laste rekalkulita $1" ;


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
function wikiRecodeOutputRaw($text) {
  $x = array(
  	"Cx", "cx",
	"Gx", "gx",
	"Hx", "hx",
	"Jx", "jx",
	"Sx", "sx",
	"Ux", "ux",
  	"CX", "cX",
	"GX", "gX",
	"HX", "hX",
	"JX", "jX",
	"SX", "sX",
	"UX", "uX");
  $u = array(
	"\xc4\x88", "\xc4\x89",
	"\xc4\x9c", "\xc4\x9d",
	"\xc4\xa4", "\xc4\xa5",
	"\xc4\xb4", "\xc4\xb5",
	"\xc5\x9c", "\xc5\x9d",
	"\xc5\xac", "\xc5\xad",
	"\xc4\x88", "\xc4\x89",
	"\xc4\x9c", "\xc4\x9d",
	"\xc4\xa4", "\xc4\xa5",
	"\xc4\xb4", "\xc4\xb5",
	"\xc5\x9c", "\xc5\x9d",
	"\xc5\xac", "\xc5\xad");
    
  return $text[1] . str_replace($x, $u, $text[2]) . $text[4];
}

function wikiRecodeOutputEo($text) {
  # Don't convert anything inside a tag (URLs, etc)
  # $pageText =~ s/(^|>)((.|\n)*?)(<|$)/$1.&RecodeOutput($2).$4/ige;
  global $user;
  if($user->options["encoding"] == 1) {
    return $text;
  } else {
    return preg_replace_callback("/(^|>)((.|\n)*?)(<|$)/", "wikiRecodeOutputRaw", $text);
#    return preg_replace ( "/(^|>)((.|\n)*?)(<|$)/e", "\"\$1\".str_replace(\$x,\$u,\"\$3\").\"\$4\"", $text);
  }
}

function wikiRecodeInputEo($text) {
  $x = array(
  	"Cx", "cx",
	"Gx", "gx",
	"Hx", "hx",
	"Jx", "jx",
	"Sx", "sx",
	"Ux", "ux");
  $u = array(
	"\xc4\x88", "\xc4\x89",
	"\xc4\x9c", "\xc4\x9d",
	"\xc4\xa4", "\xc4\xa5",
	"\xc4\xb4", "\xc4\xb5",
	"\xc5\x9c", "\xc5\x9d",
	"\xc5\xac", "\xc5\xad");
    
  return $text = str_replace($u, $x, $text);
}

$wikiRecodeInput = wikiRecodeInputEo;
$wikiRecodeOutput = wikiRecodeOutputEo;

?>
