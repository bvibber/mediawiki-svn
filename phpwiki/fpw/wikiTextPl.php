<?
# ATTENTION:
# To fit your local settings, PLEASE edit wikiSettings.php and wikiLocalSettings.php ONLY!
# Change settings here ONLY if they're to become global in all wikipedias!

# This contains the strings for international/specialized wikis
#---------------------------------------------------------------

# Basic character set and locale settings

$wikiCharset = "utf-8" ;
$wikiLanguage = "pl";	# For Content-Language header, LANG= param, etc.
# $wikiLocale = "pl_PL.UTF-8" ; # If it doesn't exist on the local machine, en_US.UTF-8 should do
include_once ( "utf8Case.php" ) ;

# Misc stuff
$wikiMainPage = "Polska Wikipedia" ; # This is actual the title of the article in the database
$wikiErrorPageTitle = "Ups! Błąd!" ;
$wikiErrorMessage = "<h2>$1!</h2>Powrót na [[:Polska Wikipedia|Stronę główną]]!" ;
$wikiRecentChangesText = "Na tej automatycznie generowanej stronie można objerzeć historię zmian na Polskiej Wikipedii." ;
$wikiMetaDescription = "$1... Read more here at wikipedia, the free encyclopedia!" ;

# Used in header/footer
$wikiMainPageTitle = "Wikipedia : Wolna encyklopedia" ; # This is the title that is displayed on the main page
$wikiArticleSubtitle = "Z Wikipedii, wolnej encyklopedii." ;
$wikiPrintable = "Wresja dla drukarki" ;
$wikiWatch = "Obserwuj dla mnie ten artykuł" ;
$wikiNoWatch = "Skończ obserwować dla mnie ten atykuł" ;
$wikiTitleTag = "$1: artykuł z Wikipedii" ;
$wikiLogIn = "Zaloguj się" ;
$wikiLogOut = "Wyloguj się" ;
$wikiHelp = "Pomoc" ;
$wikiHelpLink = "wikipedia:Pomoc" ;
$wikiPreferences = "Opcje" ;
$wikiWhatLinksHere = "Strony które tu linkują" ;
$wikiPrintLinksMarkup = "i" ; # will be used as <$wikiPrintLinksMarkup> and </$wikiPrintLinksMarkup>
$wikiTalk = "dyskusja" ;
$wikiUser = "użytkownik" ;
$wikiNamespaceTalk = "$1 Talk" ;
$wikiWikipedia = "wikipedia" ;
$wikiAllowedNamespaces = array ( $wikiWikipedia , $wikiTalk , $wikiUser , "" , "wikipedia $wikiTalk" , "$wikiUser $wikiTalk" ) ;
$wikiSkins = array ( "Normalna" => "" , "Star Trek" => "Star Trek" , "Nostalgia" => "Nostalgy" ) ;

# Editing
$wikiEditTitle = "Edycja $1" ;
$wikiCannotEditPage = "<h3>Nie możesz edytować tej strony!</h3>" ;
$wikiEditConflictMessage = "<h1>Konflikt edycji!</h1>\n<b>Ktoś zachował tą stronę po tym jak zaczęłeś edytować.Górna ramka zawiera zachowany tekst. Tylko treść górnej ramki będzie zachowana.</b><br>\nPrzewiń w dół żeby zobaczyć swój tekst.<br>\n" ;
$wikiPreviewAppend = "<hr>\n<h2>Podgląd :</h2>\n$1<hr><h3>Pamiętaj, że to jest tylko podgląd i nie jest jeszcze zachowany!</h3>" ;
$wikiSummary = "Opis zmian:" ;
$wikiMinorEdit = "Dokonano tylko drobnych modyfikacji." ;
$wikiCopyrightNotice = "Wszystkie kontrybucje do Wikipedii są uważane za udostępnione na zasadach GNU Free Documentation License. Jeśli nie chcesz żeby twoje dzieło było bezlitośnie edytowane i dowolnie redystrybutowane, nie naciskaj przycisku zachowaj. Zachowując obiecujesz też nam, że sam to napisałeś lub skopiowałeś z materiałów nie objętych prawem autorskim (public domain). <b>NIE UŻYWAJ BEZ POZWOLENIA MATERIAŁÓW OBJĘTYCH PRAWEM AUTORSKIM!</b>" ;
$wikiSave = "Zachowaj" ;
$wikiPreview = "Podgląd" ;
$wikiReset = "Reset" ;
$wikiDontSaveChanges = "<i>Anuluj</i>" ;
$wikiDescribePage = "Tutaj wpisz tekst artykułu." ;

# wikiUser
$wikiNoSuchUser = "<font color=red>Nieznany użytkownik $1!</font>" ;
$wikiWrongPassword = "<font color=red>Złe hasło dla użytkownika $1!</font>" ;
$wikiYouAreLoggedIn = "$1, jesteś zalogowany!" ; 
$wikiUserError = "Błąd z \"$1\"" ;

# wikiPage
$wikiNoSuchSpecialPage = "<h1>Nie ma takiej strony specjalnej \"$1\"!</h1>" ;
$wikiOldVersion = "<br><font size=\"-1\">To jest stara wersja #$1; zobacz <a href=\"$THESCRIPT?title=$2\">nową wersję</a></font>" ;
$wikiRedirectFrom = "(przekierowanie z $1)" ;
$wikiRecentChanges = "Ostatnie Zmiany" ;
$wikiRecentChangesLink = "RecentChanges" ; # "special:" is added automatically
$wikiRecentLinked = "Obserwuj linkowane stąd strony" ;
$wikiRecentLinkedLink = "Recentchangeslinked" ;
$wikiSpecialPagesLink = "Special_pages" ; # "special:" is added automatically
$wikiEditThisPage = "Edytuj ten artykuł" ;
$wikiDeleteThisPage = "Skasuj ten artykuł" ;
$wikiUpload = "Uploaduj pliki" ;
$wikiHistory = "Historia" ;
$wikiRandomPage = "Losowa Strona" ;
$wikiSpecialPages = "Strony Specjalne" ;
$wikiEditHelp = "<i>Pomoc w sprawie edycji</i>" ;
$wikiStatistics = "Statystyka" ;
$wikiNewPages = "Nowe Strony" ;
$wikiOrphans = "Porzucone" ;
$wikiMostWanted = "Najbardziej pożądane" ;
$wikiAllPages = "Wszystkie Strony" ;
$wikiStubs = "Krótkie artykuły" ;
$wikiLongPages = "Długie artykuły" ;
$wikiListUsers = "Lista użytkowników" ;
$wikiMyWatchlist = "Moja lista obserwowanych" ;
$wikiBeginDiff = "POCZĄTEK RÓŻNIC" ;
$wikiEndDiff = "KONIEC RÓŻNIC" ;
$wikiDiffLegend = "<font color=#2AAA2A>Green text</font> was added or changed, <font color=#AAAA00>yellow text</font> was changed or deleted." ;
$wikiDiffFirstVersion = "To jest pierwsza wersja tego artykułu. Cały tekst jest nowy!<br>\n" ;
$wikiDiffImpossible = "To jest pierwsza wersja tego artykułu. Cały tekst jest nowy<br>\n" ;
$wikiSearch = "Szukaj" ;
$wikiOtherNamespaces = "<b>Inne przestrzenie nazw :</b> " ;
$wikiCategories = "<b>Kategorie :</b> " ;
$wikiThisCategory = "Artykuły w tej kategorii" ;
$wikiCounter = "Ta strona była oglądana $1 razy." ;
$wikiBlockIPTitle = "Zablokuj IP (tylko admin)" ;
$wikiBlockIPText = "IP $1 został zablokowany przez użytkownika $2" ;
$wikiIPblocked = "<font color=red size='+1'>Twój IP został zablokowany! Twoje edycje nie będą zachowane. Proszę skontaktować się z adminem, żeby usunąć blokadę, lub spróbować później.</font>" ;
$wikiBugReports = "Raporty o błędach" ;
$wikiBugReportsLink = "wikipedia:Raporty o błędach" ;

# Interwiki links
$wikiInterwiki = array ( "w"=>"http://www.wikipedia.com/wiki/$1" , "m"=>"http://meta.wikipedia.com/wiki.phtml?title=$1" ) ;

# Language interwiki links
$wikiOtherLanguages = array (
    "ar"=>"http://ar.wikipedia.com/wiki.cgi?$1" ,
    "ca"=>"http://ca.wikipedia.com/wiki.cgi?$1" ,
    "zh"=>"http://zh.wikipedia.com/wiki.cgi?$1" ,
    "dk"=>"http://dk.wikipedia.com/wiki.cgi?$1" ,
    "nl"=>"http://nl.wikipedia.com/wiki.cgi?$1" ,
    "de"=>"http://de.wikipedia.com/wiki.cgi?$1" ,
    "eo"=>"http://eo.wikipedia.com/wiki/$1" ,
    "fr"=>"http://fr.wikipedia.com/wiki.cgi?$1" ,
    "he"=>"http://he.wikipedia.com/wiki.cgi?$1" ,
    "hu"=>"http://hu.wikipedia.com/wiki.cgi?$1" ,
    "it"=>"http://it.wikipedia.com/wiki.cgi?$1" ,
    "ja"=>"http://ja.wikipedia.com/wiki/$1" ,
    "pl"=>"http://pl.wikipedia.com/wiki.cgi?$1" ,
    "pt"=>"http://pt.wikipedia.com/wiki.cgi?$1" ,
    "ru"=>"http://ru.wikipedia.com/wiki.cgi?$1" ,
    "simple"=>"http://simple.wikipedia.com/wiki.cgi?$1" ,
    "es"=>"http://es.wikipedia.com/wiki.cgi?$1" ,
    "sv"=>"http://sv.wikipedia.com/wiki.cgi?$1" ,
    "en"=>"http://www.wikipedia.com/wiki/$1"
    ) ;

# Language interwiki names
$wikiLanguageNames = array (
    "ar"=>"Arabska (Araby)" ,
    "ca"=>"Kataleńska (Catala)" ,
    "zh"=>"Chińska (Hanyu)" ,
    "dk"=>"Duńska (Dansk)" ,
    "nl"=>"Niderlandzka (Nederlands)" ,
    "de"=>"Niemiecka (Deutsch)" ,
    "eo"=>"Esperanto" ,
    "fr"=>"Francuska (Francais)" ,
    "he"=>"Hebrajska (Ivrit)" ,
    "hu"=>"Węgierska (Magyar)" ,
    "it"=>"Włoska (Italiano)" ,
    "ja"=>"Japońska (Nihongo)" ,
    "pl"=>"Polska" ,
    "pt"=>"Portugalska (Portuguese)" ,
    "ru"=>"Rosyjska (Russkiy)" ,
    "simple"=>"Uproszczony Angielski" ,
    "es"=>"Hiszpańska (Castellano)" ,
    "sv"=>"Szwedzka (Svenska)" ,
    "en"=>"Angielska (English)"
    ) ;
$wikiOtherLanguagesText = "Other languages : $1" ;

# date
$wikiDate = array () ;
$wikiDate["monday"] = "Poniedziałek" ;
$wikiDate["tuesday"] = "Wtorek" ;
$wikiDate["wednesday"] = "Środa" ;
$wikiDate["thursday"] = "Czwartek" ;
$wikiDate["friday"] = "Piątek" ;
$wikiDate["saturday"] = "Sobota" ;
$wikiDate["sunday"] = "Niedziela" ;
$wikiDate["january"] = "Styczeń" ;
$wikiDate["february"] = "Luty" ;
$wikiDate["march"] = "Marzec" ;
$wikiDate["april"] = "Kwiecień" ;
$wikiDate["may"] = "Maj" ;
$wikiDate["june"] = "Czerwiec" ;
$wikiDate["july"] = "Lipiec" ;
$wikiDate["august"] = "Sierpień" ;
$wikiDate["september"] = "Wrzesień" ;
$wikiDate["october"] = "Październik" ;
$wikiDate["november"] = "Listopad" ;
$wikiDate["december"] = "Grudzień" ;
$wikiDateGenetive["january"] = "stycznia" ;
$wikiDateGenetive["february"] = "lutego" ;
$wikiDateGenetive["march"] = "marca" ;
$wikiDateGenetive["april"] = "kwietnia" ;
$wikiDateGenetive["may"] = "maja" ;
$wikiDateGenetive["june"] = "czerwca" ;
$wikiDateGenetive["july"] = "lipca" ;
$wikiDateGenetive["august"] = "sierpnia" ;
$wikiDateGenetive["september"] = "września" ;
$wikiDateGenetive["october"] = "października" ;
$wikiDateGenetive["november"] = "listopada" ;
$wikiDateGenetive["december"] = "grudnia" ;

#-----------------------------------------------------------------------
## strings for specialFunctions.php

# Log out / log in
$wikiGoodbye = "<h1>Żegnaj, $1!</h1>" ;
$wikiWelcomeCreation = "<h1>Welcome, $1!</h1><font color=red>Don't forget to personalize your wikipedia preferences!</font><br>Your account has been created. Please press \"Log in\" once more to log in!" ; ;
$wikiLoginPageTitle = "Login" ;
$wikiYourName = "Twój login&nbsp; : " ;
$wikiYourPassword = "Twoje hasło&nbsp;&nbsp; : " ;
$wikiYourPasswordAgain = "Powtórz hasło : " ;
$wikiNewUsersOnly = " (tylko nowi użytkownicy)" ;
$wikiRememberMyPassword = "Zapamiętaj moje hasło (jako ciasteczko)." ;
$wikiLoginProblem = "<b>Są problemy z twoim logowaniem.</b><br>Spróbuj ponownie!" ;
$wikiAlreadyLoggedIn = "<font color=red><b>Użytkownik $1 już jest zalogowany!</b></font><br>\n" ;
$wikiPleaseLogIn = "<h1>Proszę się zalogować:</h1>\n" ;
$wikiAreYouNew = "Jeśli nie jesteś jeszcze zarejestrowany a chcesz się zarejestrować wpisz nazwe użytkownika oraz dwukrotnie hasło.<br>\n" ;
$wikiLogIn = "Zaloguj się" ;

# User preferences
$wikiUserSettings = "Zmiana Ustawień" ;
$wikiUserSettingsError = "Nie jesteś zalogowany! [[special:userLogin|Zaloguj się]] lub wróć na [[:Polska Wikipedia|Stronę główną]]" ;
$wikiUserSettingsNewPasswordError = "<h1><font color=red>Nowe hasła nie są zgodne. HASŁO NIE ZOSTAŁO ZMIENIONE!</font></h1>\n" ;
$wikiUserSettingsSaved = "<font color=red size=\"+1\">Twoje ustawienia zostały zachowane!</font>" ;
$wikiLoggedInAs = "<b>Jesteś zalogowany jako [[user:$1|$1]]. ";
$wikiID_Help = "Twój wewnętrzny identyfikator to $1.</b> Możesz uzyskać [[wikipedia:Opcje użytkownika|here]] pomoc." ;
$wikiQuickBarSettings = "Ustawienia QuickBar :" ;
$wikiSettingsStandard = "standardowo" ;
$wikiSettingsNone = "Brak" ;
$wikiSettingsLeft = "Z Lewej" ;
$wikiSettingsRight = "Z Prawej" ;
$wikiOldPassword = "Stare hasło&nbsp; &nbsp; : " ;
$wikiNewPassword = "Nowe hasło&nbsp; &nbsp; : " ;
$wikiSkin = "Skóra :" ;
$wikiStarTrek = "Star Trek" ;
$wikiShowHoverBox = "Show hoverbox over wiki links" ;
$wikiUnderlineLinks = "Podkreśl linki" ;
$wikiNewTopicsRed = "Pokazuj nowe tematy na czerwono" ;
$wikiJustifyParagraphs = "Justify paragraphs" ;
$wikiShowRecentChangesTable = "Pokazuj <i>Ostatnie zmiany</i> jako tablicę" ;
$wikiHideMinorEdits = "Ukryj drobne zmiany w <i>Ostatnie Zmiany</i>" ;
$wikiDoNumberHeadings = "Automatycznie numeruj nagłówki" ;
$wikiViewWithFrames = "Wyświetlaj strony za pomocą ramek <i>(eksperymentalne, TYLKO Konqueror!)</i>" ;
$wikiTurnedOn = "tak" ;
$wikiTurnedOff = "nie" ;
$wikiTextboxDimensions = "Rozmiary okna tekstowego :" ;
$wikiCols = "Kolumny : " ;
$wikiRows = "Rzędy : " ;
$wikiYourEmail = "Twój email : " ;
$wikiResultsPerPage = "Zwróć do $1 wyników wyszukiwania na stronę" ;
$wikiTimeDiff = "Różnica czasowa: $1 godzin" ;
$wikiViewRecentChanges = "Zobacz ostatnich $1 zmian w ''Ostatnie Zmiany''" ;
$wikiOutputEncoding = "Kodowanie Wyjściowe : ";

# Search Page
$wikiSearchTitle = "Szukaj" ;
$wikiSearchedVoid = "Nie wpisałeś co chcesz znaleźć, więc nic nie zostało znalezione." ;
$wikiNoSearchResult = "Przykro nam, ale nie możemy znaleźć artykułu odpowiadającego zapytaniu \"$1\" w tytule lub treści." ;
$wikiSearchHelp = "Proszę zobaczyć [[wikipedia:Wyszukiwanie|pomoc dotyczącą wyszukiwania]]." ;
$wikiFoundHeading = "Artykuły Wikipedii" ;
$wikiFoundText = "Zapytanie ''$2'' zwróciło $1 artykułów. Dla każdego artykułu możesz tu zobaczyć pierwszy akapit i następny zawierający jedno z wyszukiwanych słów." ;
# keywords used for boolean search operators
# note: these must consist of character, no symbols allowed
$and = "and";
$or = "or";
$not = "not";
# syntax errors for parser of boolean search queries
$srchSyntErr = array (
                    "SYNTAX ERROR: brak '$1'; wstawione",
                    "SYNTAX ERROR: nieoczekiwane '$1'; zignoromane",
                    "SYNTAX ERROR: illegal symbol '$1'; zignoromane",
                    "SYNTAX ERROR: słowo '$1' jest za krótkie, indeks wymag przynajmniej $2 znaków",
                    "SYNTAX ERROR: missing search word; wstawione"
               ) ;   
$wikiSearchError = "Sorry, your boolean search query contains the following errors: " ;

# Misc
$wikiLonelyPagesTitle = "Porzucone" ;
$wikiLonelyPagesText = "'''Te artykuły istnieją, ale żaden artykuł do nich nie linkuje!'''<br>''Strony dyskusyjne, puste i przekierowania '''nie''' są tu wyświetlone.''\n\n" ;
$wikiAllPagesTitle = "Indeks wszystkich stron" ;
$wikiAllPagesText = "'''Oto wszystkie artykuły w bazie danych!'''\n\n" ;
$wikiUnsuccessfulSearch = "Nieudane wyszukiwanie $1" ;
$wikiUnsuccessfulSearches = "wikipedia:Nieudane wyszukiwania ($1)" ; # $1 becomes "YEAR-MONTH"
$wikiUserlistTitle = "Lista Użytkowników" ;
$wikiUserlistText = "'''To są wszyscy zarejestrowani wikipediści!'''" ;
$wikiRecentChangesTitle = "Ostatnie Zmiany" ;
$wikiRecentChangesLastDays = "To ostatnie <b>$1</b> zmian dokonanych na Wikipedii w ciągu ostatnich <b>$2</b> dni." ;
$wikiRecentChangesSince = "To ostatnie <b>$1</b> zmian dokonanych na Wikipedii od <b>$2</b>." ;
$wikiEditTypes = array ( "0"=>"" , "1"=>"<font color=cyan>M</font>" , "2"=>"<font color=green>N</font>" ) ; #Abbreviations for minor edits (1) and new pages (2) to be used on RecentChanges
$wikiViewLastDays = "Wyświetl ostatnie $1 dni" ;
$wikiViewMaxNum = "Wyświetl ostatnie $1 zmian" ;
$wikiListOnlyNewChanges = "Wyświetl tylko nowe zmiany" ;
$wikiNewPagesTitle = "Nowe strony" ;
$wikiNewPagesText = "Oto <b>$1</b> najnowszych stron na Wikipedii, pochodzących z ostatnich <b>$2</b> dni." ;
$wikiRCLegend = " <b>Legenda :</b> $1=Drobne zmiany ; $2=Nowy artykuł." ;
$wikiDiff = "(różnice)" ;
$wikiChange = "zmiana" ;
$wikiChanges = "zmiany" ;
$wikiWatchYes = "\"$1\" został dodany do twojej [[special:WatchList|listy obserwowanych artykułów]]." ;
$wikiWatchNo = "\"$1\" został usunięty z twojej [[special:WatchList|listy obserwowanych artykułów]]." ;
$wikiWatchlistTitle = "Lista obserwowanych" ;
$wikiWatchlistExistText = "'''Aktualnie obserwujesz istniejące artykuły :'''" ;
$wikiWatchlistNotExistText = "'''Aktualnie obserwujesz nieistniejące artykuły :'''" ;

# Statistics
$wikiStatisticsTitle = "Statystyki Artykułów" ;
$wikiStatTotalPages = "W bazie danych znajduje się $1 stron." ;
$wikiStatTalkPages = "Jest $1 stron dyskusyjnych." ;
$wikiStatCommaPages = "Jest $1 stron zawierających przecinek które nie są stronami dyskusyjnymi." ;
$wikiStatWikipediaNoTalk = "Jest $1 stron zawierających w tytule \"ikipedi\" które ''nie'' są stronami dyskusyjnymi." ;
$wikiStatSubNoTalk = "Jest $1 podstron nie będących stronami dyskusyjnymi." ;
$wikiStatNoTalk = "Co znaczy że jest około $1 artykułów, włączając w to podstrony (oprócz '''Dyskusja''')." ;
$wikiStatArticles = "Lub około $1, bez podstron!" ;
$wikiStatRedirect = "Jest około $1 stron #REDIRECT." ;
$wikiStatSkin = "<font color=red>$1</font> z nich używa skory \"$2\"." ;
$wikiStatJunk = "Ostatecznie, jest około $1 junk pages :-(" ;
$wikiStatOld = "W bazie danych jest $1 starych wersji, co daje średnio $2 starych wersji na stronę." ;
$wikiUserStatistics = "Statystyki użytkowników" ;
$wikiStatUsers = "Jest $1 zarejestrowanych [[special:ListaUżytkowników|użytkowników]]." ;
$wikiStatSysops = "$1 z nich ma status admina." ;

# Upload
$wikiUploadTitle = "Upload" ;
$wikiUploadDenied = "Nie jesteś edytorem ani adminem. Powróć na <a href=\"$THESCRIPT?action=upload\">stronę Upload</a>" ;
$wikiUploadDeleted = "Plik <b>$1</b> skasowany!" ;
$wikiUploadDelMsg1 = "*$3, [[użytkownik:$1|$1]] skasował plik '''$2'''\n" ;
$wikiUploadDelMsg2 = "Deletion of file $1" ;
$wikiUploadAffirm = "<nowiki>Musisz poświadczyć, że uploadując plik nie łamiesz praw autorskich. Powróć na <a href=\"$THESCRIPT?title=special:upload\">stronę Upload</a></nowiki>" ;
$wikiUploadRestrictions = "<nowiki>Musisz się zalogować żeby uploadować plik!</nowiki>" ;
$wikiUploadFull = "Przepraszamy ale prawie skończył nam się wolny obszar na dyskach. Możliwość uploadu plików jest na razie wyłączona." ;
$wikiUploadSuccess = "Plik <b>$1</b> został uploadowany!" ;
$wikiUploadSuccess1 = "*$1, $2 uploadował plik '''$3'''\n" ;
$wikiUploadSuccess2 = "Upload pliku $1" ;
#$wikiUploadText = "<h2>Instrukcje:</h2><ul>\n" ;
$wikiUploadText = "<li><strong>Użyj tego formularza do uploadu różnych plików</strong></li>\n";
$wikiUploadText .= "<li>Żeby zastąpić poprzednio uploadowany plik (np.\n";
$wikiUploadText .= "nowszą lub poprawioną wersją), po prostu uploaduj go\n";
$wikiUploadText .= "ponownie. Upewnij się tylko że nazwa pliku\n";
$wikiUploadText .= "jest taka sama.</li>\n";
$wikiUploadText .= "<li><strong>Instrukcja uploadu plików.</strong>Kliknij\n";
$wikiUploadText .= "&quot;Browse...&quot; żeby znaleźć plik który chcesz\n";
$wikiUploadText .= "uploadować na swoim dysku. Otworzy się okno dialogowe\n";
$wikiUploadText .= "&quot;Wybierz Plik&quot;.</li>\n";
$wikiUploadText .= "<li>Kiedy go znajdziesz, kliknij &quot;Otwórz.&quot;\n";
$wikiUploadText .= "Plik zostanie w ten sposób wybrany, a okno dialogowe zamknięte.</li>\n";
$wikiUploadText .= "<li>Nie zapomnij sprawdzić informacji o prawach autorskich!</li>\n";
$wikiUploadText .= "<li>Jak już sprawdzisz, kliknij &quot;Upload.&quot; Plik zacznie się uploadować. Może to zająć trochę czasu jeśli jest\n";
$wikiUploadText .= "plik jest duży a twoje połączenie z Internetem powolne.</li>\n";
$wikiUploadText .= "<li>Wiadomość poinformuje cię czy upload się powiódł.</li>\n";
$wikiUploadText .= "<li>Możesz uploadować tyle plików ile chcesz. Prosimy jednak o nie przepełnianie naszego serwera ;).</li>\n";
$wikiUploadText .= "<li>Wszystkie uploady i skasowania są logowane w <a href=\"$THESCRIPT?title=Log:Uploady\">logu uploadów</a>.</li>\n";
$wikiUploadText .= "</ul>\n";
$wikiUploadAffirmText = "Niniejszym zaświadczam że plik ten <b>nie jest objęty prawem autoskim</b>, jest udostępniony na licencji zgodnej z GFDL, lub że posiadając prawa autorskie do tego pliku umieszam go an licencji GFDL." ;
$wikiUploadButton = "Upload" ;
$wikiUploadPrev = "Uploadowane pliki:" ;
$wikiUploadSize = "Rozmiar (bajty)" ;
$wikiFileRemoval = "Usunięcie pliku" ;
$wikiUploadRemove = "Kliknij tu żeby usunąć $1." ;

# Misc
$wikiHistoryTitle = "Historia $1" ;
$wikiHistoryHeader = "To jest historia artykułu <a href=\"$THESCRIPT?title=$1\">$2</a>" ;
$wikiCurrentVersion = "aktualne" ;
$wikiSpecialTitle = "Strony Specjalne" ;
$wikiSpecialText = "<b>To jest lista stron specjalnych.</b> Niektóre z nich są dostępne tylko jeśli jesteś zalogowany. Jeśli jesteś zalogowany możesz ustawić automatyczne wyświetlanie tej listyz prawej bądź lewej strony każdego artykułu jako QuickBar.<br><br>" ;
$wikiStubTitle = "Krótkie artykuły" ;
$wikiLongPagesTitle = "Najdłuższe artykuły" ;
$wikiStubText = "'''Oto wszystkie artykuły z bazy danych, sortowane według długości - najpierw najkrótsze.'''<br>''Przekierowania i strony w przestrzeniach nazw (takich jak Dyskusja:) '''nie są''' wyświetlane!''\n\n" ;
$wikiLongPagesText = "'''Oto wszystkie artykuły z bazy danych, sortowane według długości - najpierw najdłuższe.'''<br>''Przekierowania i strony w przestrzeniach nazw (takich jak Dyskusja:) '''nie są''' wyświetlane''\n\n" ;
$wikiStubChars = "$1 znaków" ;
$wikiAskSQL = "MySQL access" ;
$wikiAskSQLtext = "Ta funkcja jest dostępna tylko dla adminów.<br>''Please, do not press return/enter, '''always''' click on the ask button!''" ;
$wikiStubDelete = "<b>Skasuj tą stronę!</b>" ;
$wikiStubLinkHere = "Linkuje tu $1 artykułów." ;
$wikiStubShowLinks = "Przełącz opcje \"pokaż linki\"" ;
$wikiShowLinks = "Pokaż strony które linkują do \"$1\"" ;
$wikiRecentChangesLinkedTitle = "Ostatnie zmiany na stronach linkowanych z $1" ;
$wikiDeleteTitle = "Kasowanie artykułu '$1'" ;
$wikiDeleteDenied = "<font size=\"+3\">You are not allowed to delete this page!</font>" ;
$wikiDeleteSuccess = "'$1' został usunięty." ;
$wikiDeleteMsg1 = "*On $1, [[user:$2|$2]] permanently deleted page '''$3'''\n" ;
$wikiDeleteMsg2 = "Nieodwracalne usunięcie $1" ;
$wikiDeleteAsk = "Zamierzasz usunąć artykuł \"$1\" i jego całą historię!<br>\nJeśli jesteś całkowicie pewien że chcesz to zrobić, <a href=\"$2&iamsure=yes\">kliknij tutaj</a>." ;
$wikiProtectTitle = "Ochrona artykułu '$1'" ;
$wikiProtectDenied = "<font size=\"+3\">Nie masz uprawnień do ustawienia ochrony tej strony!</font>" ;
$wikiProtectNow = "Strona '$1' jest teraz chroniona jako $2." ;
$wikiProtectText = "<font size=\"+2\">Możesz teraz edytować ochronę dla '$target'</font><br><i>Na przykład użyj \"is_sysop\" żeby zabronić wszystkim poza adminami edycji tej strony. Oddzielaj pozwolenia przecinkami.</i>" ;
$wikiProtectCurrent = "Aktualna ochrona : " ;
$wikiContribTitle = "Kontrybucje użytkownika $1" ;
$wikiContribText = "<h1>Kontrybucje użytkownika $1 :</h1>\n(Z wyjątkiem drobnych zmian i zmian do stron dyskusyjnych i logów)" ;
$wikiContribDenied = "Podja nazwę użytkownika!" ;
$wikiLinkhereTitle = "Strony które linkują do $1" ;
$wikiLinkhereBacklink = "Te artykuły są linkowane powrotnie z [[$1]]:" ;
$wikiLinkhereNoBacklink = "Te artykuły '''nie''' są linkowane powrotnie z [[$1]]:" ;
$wikiBacklinkNolink = "Żadne artykuły nie linkują do [[$1]]!" ;
$wikiBacklinkFollowing = "Artykuły które linkują do [[$1]]:" ;
$wikiWantedTitle = "Najbardziej Pożądane Strony" ;
$wikiWantedText = "'''Te artykuły nie istnieją, ale inne artykuły do nich linkują!''' (top 50)<br>\n" ;
$wikiWantedToggleNumbers = "Click here to toggle the display of page titles starting with numbers (currently $1)" ;
$wikiWantedLine = "<b>$2</b> artykułów chce $1 <nowiki>(zobacz <a href=\"$3\">strony które linkują do \"$4\"</a></nowiki>)" ;
$wikiLastChange = "Last edited $1" ;
$wikiPopularPages = "Najpopularniejsze" ;
$wikiPopularTitle = "Najpopularniejsze Artykuły" ;
$wikiRefreshThisPage = "Odśwież tą stronę" ;
$wikiResourcesWarning = "(Proszę robić to tylko jeśli to konieczne, ponieważ ta funkcja pochłania dużo zasobów systemowych)" ;
$wikiNoRefresh = "(The page was last refreshed just $1 minutes ago; please wait another $2 minutes and try again.)" ;
$wikiLastRefreshed = "Last refreshed $1" ;
$wikiValidate = "Sprawdź zgodność tej strony ze standardami W3C" ;
$wikiBlockIP = "Zablokuj ten IP" ;
$wikiNostalgy = "Nostalgia" ;

#---------------------------
#Functions
function wikiGetDatePl ( $x ) { # Used in RecentChangesLayout in special_functions.php
    global $wikiDate ;
    global $wikiDateGenetive ;
    $dayName = $wikiDate [ strtolower ( date ( "l" , $x ) ) ];
    $monthName = $wikiDateGenetive [ strtolower ( date ( "F" , $x ) ) ];
    $dayNumber = date ( "j" , $x ) ;
    $year = date ( "Y" , $x ) ;
    return "$dayName, $dayNumber $monthName, $year" ;
}
$wikiGetDate = wikiGetDatePl;

# In theory, this could be expanded to allow general conversion of the
# character encoding used in the database to another encoding optionally
# used on the browser end.

# Define these arrays if you need to set up conversion.
# $wikiEncodingCharsets = array("iso-8859-2");
# $wikiEncodingNames = array("Latin-2"); # Localised names

function wikiRecodeOutputPl($text) {
  # Stub
  # global $user;  # $user->options["encoding"] is an index into the above arrays
  return $text;
}

function wikiRecodeInputPl($text) {
  # Stub
  return $text;
}

$wikiRecodeOutput = wikiRecodeOutputPl;
$wikiRecodeInput = wikiRecodeInputPl;
?>
