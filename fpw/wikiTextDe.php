<?
# ATTENTION:
# To fit your local settings, PLEASE edit wikiSettings.php and wikiLocalSettings.php ONLY!
# Change settings here ONLY if they're to become global in all wikipedias!

$wikiLanguage = "de" ;
include_once ( "utf8Case.php" ) ;

# This contains the strings for international/specialized wikis
#---------------------------------------------------------------

# Basic character set and locale settings
$wikiCharset = "utf-8" ;

# Misc stuff
$wikiMainPage = "Hauptseite" ; # This is actual the title of the article in the database
$wikiErrorPageTitle = "Verflixt! Ein Fehler!" ;
$wikiErrorMessage = "<h2>$1!</h2>Zurück zur [[:Hauptseite|Hauptseite]]!" ;
$wikiAllowedSpecialPages = array("userlogin","userlogout","newpages","recentchanges","upload","statistics","lonelypages","popularpages","wantedpages","allpages","randompage","shortpages","longpages","listusers","watchlist","special_pages","editusersettings","deletepage","movepage","protectpage","contributions","whatlinkshere","recentchangeslinked","sqldump","vote");
$wikiRecentChangesText = "Diese Seite wird beim Laden automatisch aktualisiert. Angezeigt werden Seiten, die zuletzt bearbeitet bzw. geändert wurden, sowie die Zeit und der Name des Autors. Falls Du neu hier bist, dann schau Dir bitte die [[Wikipedia Willkommen|Willkommensseite]] der Wikipedia an, dort bekommst Du eine erste Einführung. Falls Du willst, dass Wikipedia zu einem Erfolg wird, dann füge bitte keine Texte hinzu, die dem Urheberrecht anderer unterliegen. Dies könnte dem Projekt sonst schweren Schaden zufügen." ;
$wikiMetaDescription = "$1... Lesen Sie mehr hier auf Wikipedia, der freien Enzyklopädie!" ;

# Used in header/footer
$wikiWikipediaHelp = "wikipedia:Hilfe" ;
$wikiMainPageTitle = "Wikipedia : Die freie Enzyklopädie" ; # This is the title that is displayed on the main page
$wikiHeaderSubtitle = "Die freie Enzyklopädie" ;
$wikiArticleSubtitle = "Von Wikipedia, der freien Enzyklopädie." ;
$wikiPrintable = "Druckversion" ;
$wikiWatch = "Diesen Artikel für mich beobachten." ;
$wikiNoWatch = "Diesen Artikel nicht mehr für mich beobachten." ;
$wikiTitleTag = "$1: Enzyklopädieartikel von Wikipedia" ;
$wikiLogIn = "Einloggen" ;
$wikiLogOut = "Ausloggen" ;
$wikiHelp = "Hilfe" ;
$wikiHelpLink = "wikipedia:Hilfe" ;
$wikiPreferences = "Einstellungen" ;
$wikiWhatLinksHere = "Was linkt hierhin" ;
$wikiPrintLinksMarkup = "i" ; # will be used as <$wikiPrintLinksMarkup> and </$wikiPrintLinksMarkup>
#$wikiAllowedNamespaces = array ( "wikipedia" , "diskussion" , "benutzer" , "" , "wikipedia diskussion" , "benutzer diskussion" ) ;
$wikiTalk = "diskussion" ;
$wikiUser = "benutzer" ;
$wikiNamespaceTalk = "$1 Diskussion" ;
$wikiWikipedia = "wikipedia" ;
$wikiAllowedNamespaces = array ( $wikiWikipedia , $wikiTalk , $wikiUser , "" , "wikipedia $wikiTalk" , "$wikiUser $wikiTalk" ) ;
$wikiSkins = array ( "Normal" => "" , "Star Trek" => "Star Trek" , "Nostalgie" => "Nostalgy" , "Cologne Blue" => "Cologne Blue" ) ;
$wikiMyOptions = "Meine Optionen" ;
$wikiMySettings = "Meine Einstellungen" ;
$wikiMyself = "Meine Seite" ;
$wikiShortPages = "Kurze Seiten" ;
$wikiLongPages = "Lange Seiten" ;
$wikiUserList = "Benutzerverzeichnis" ;
$wikiEditingHistory = "Revisionen" ;
$wikiAddToWatchlist = "Beobachten" ;
$wikiEditPage = "Jetzt bearbeiten" ;
$wikiHome = "Hauptseite" ;
$wikiAbout = "Über" ;
$wikiFAQ = "FAQ" ;
$wikiPageInfo = "Seiteninformation" ;
$wikiLinkedPages = "Verlinkte Seiten" ;
$wikiShowDiff = "zeige Unterschiede" ;
$wikiRequests = "Anfragen: $1" ;
$wikiEdit = "Bearbeiten" ;
$wikiPageOptions = "Seitenoptionen" ;
$wikiBrowse = "Blättern" ;
$wikiFind = "Finden" ;
$wikiOK = "OK" ;
$wikiFindMore = "Mehr finden";
$wikiWikipediaHome = "Hauptseite" ;
$wikiAboutWikipedia = "Über Wikipedia" ;
$wikiAutoWikify = "Auto-wikifikation (umsichtig benutzen!)" ;
$wikiTopics = "Themen" ;
$wikiWikipediaFAQ = "wikipedia:FAQ" ;
$wikiVoteForPage = "Abstimmen" ;

# Editing
$wikiEditingHelp = "Hilfe zum Bearbeiten" ;
$wikiWikipediaEditingHelp = "wikipedia:Wie bearbeite ich einen Artikel" ;
$wikiEditTitle = "Bearbeiten von $1" ;
$wikiCannotEditPage = "<h3>Sie können diese Seite nicht bearbeiten!</h3>" ;
$wikiEditConflictMessage = "<h1>Edit conflict!</h1>\n<b>Jemand hat eine neue Version dieser Seite gespeichert, während Sie sie bearbeitet haben. Der obere Kasten enthält die aktuelle Version, der untere Ihre Änderungen. Nur der obere Text wird gespeichert werden!</b><br>\n" ;
$wikiPreviewAppend = "<hr>\n<h2>Voransicht :</h2>\n$1<hr><h3>Achtung: Dies ist nur eine Voransicht, der Text wurde noch nicht gespeichert!</h3>" ;
$wikiSummary = "Zusammenfassung:" ;
$wikiDescription = "Beschreibung:" ;
$wikiMinorEdit = "Es wurden nur Kleinigkeiten verändert." ;
$wikiCopyrightNotice = "Bitte beachte, dass alle Beiträge zur Wikipedia unter der \"GNU Freie Dokumentationslizenz\" stehen. Falls Du nicht willst, dass Dein Geschriebenes von anderen verändert und weiterverbreitet wird, dann solltest Du nicht auf \"Speichern\" drücken. Du gibst uns außerdem Deine Zusage, dass Du den Text selbst verfasst hast, oder es von einer Quelle kopiert hast, die sich in öffentlichem Besitz befindet (public domain). <b>BENUTZE KEINE URHEBERRECHTLICH GESCHÜTZTEN WERKE OHNE ERLAUBNIS DES AUTORS!</b>";
$wikiSave = "Speichern" ;
$wikiPreview = "Vorschau" ;
$wikiReset = "Zurücksetzten" ;
$wikiDontSaveChanges = "<i>Abbrechen</i>" ;
$wikiDescribePage = "Beschreibe hier die neue Seite." ;

# wikiUser
$wikiNoSuchUser = "<font color=red>Unbekannter Benutzer '$1'!</font>" ;
$wikiWrongPassword = "<font color=red>Falsches Passwort für Benutzer '$1'!</font>" ;
$wikiYouAreLoggedIn = "$1, sie sind eingeloggt!" ; 
$wikiUserError = "Problem mit '$1'" ;

# wikiPage
$wikiNoSuchSpecialPage = "<h1>Es gibt keine Spezielle Seite \"$1\"!</h1>" ;
$wikiOldVersion = "<br><font size=\"-1\">Dies ist die alte Version #$1; die aktuelle Version befindet sich <a href=\"$THESCRIPT?title=$2\">hier</a></font>" ;
$wikiRedirectFrom = "(weitergeleitet von $1)" ;
$wikiRecentChanges = "Letzte Änderungen" ;
$wikiRecentChangesLink = "RecentChanges" ; # "special:" is added automatically
$wikiRecentLinked = "Geänderte Verweise" ;
$wikiRecentLinkedLink = "Recentchangeslinked" ;
$wikiSpecialPagesLink = "Special_pages" ; # "special:" is added automatically
$wikiEditThisPage = "Jetzt bearbeiten" ;
$wikiMoveThisPage = "Seite veschieben" ;
$wikiDeleteThisPage = "Seite löschen" ;
$wikiUpload = "Dateien hochladen" ;
$wikiHistory = "Revisionen" ;
$wikiRandomPage = "Zufällige Seite" ;
$wikiSpecialPages = "Spezielle Seiten" ;
$wikiEditHelp = "<i>Hilfe zum Bearbeiten</i>" ;
$wikiEditHelpLink = "wikipedia:Wie_bearbeite_ich_einen_Artikel" ;
$wikiStatistics = "Statistik" ;
$wikiNewPages = "Neue Seiten" ;
$wikiOrphans = "Verwaiste Seiten" ;
$wikiMostWanted = "Gewünschte Seiten" ;
$wikiAllPages = "Alle Seiten" ;
$wikiStubs = "Kurze Seiten" ;
$wikiLongPages = "Lange Seiten" ;
$wikiListUsers = "Benutzerverzeichnis" ;
$wikiMyWatchlist = "Meine watchlist" ;
$wikiBeginDiff = "ANFANG DER UNTERSCHIEDE" ;
$wikiEndDiff = "ENDE DER UNTERSCHIEDE" ;
$wikiDiffLegend = "<font color=#2AAA2A>Text in Grün</font> wurde hinzugefügt oder verändert, <font color=#AAAA00>Text in Gelb</font> wurde verändert oder gelöscht." ;
$wikiDiffFirstVersion = "Dies ist die erste Revision dieses Artikels. Der Text ist komplett neu!<br>\n" ;
$wikiDiffImpossible = "Dies ist die erste Revision dieses Artikels. Der Text ist komplett neu!<br>\n" ;
$wikiSearch = "Suchen" ;
$wikiOtherNamespaces = "<b>Andere Namensräume :</b> " ;
$wikiCategories = "<b>Kategorien :</b> " ;
$wikiThisCategory = "Artikel in dieser Kategorie" ;
$wikiCounter = "Auf diese Seite wurde $1mal zugegriffen." ;
$wikiBlockIPTitle = "IP-Adresse blockieren (nur für Systemadministratoren)" ;
$wikiBlockIPText = "IP $1 wurde von $2 blockiert" ;
$wikiIPblocked = "<font color=red size='+1'>Ihre IP-Adresse wurde blockiert! Ihre Änderungen werden nicht gespeichert werden. Bitte kontaktieren die einen Systemadministrator, um die Blockierung aufzuheben, oder versuchen Sie es später noch einmal.</font>" ;
$wikiBugReports = "Beobachtete Fehler" ;
$wikiBugReportsLink = "wikipedia:Beobachtete Fehler" ;
$wikiPrintFooter = "<hr>Dieser Artikel stammt von <b>Wikipedia</b> (<a href=\"$1\">$1</a>), der freien Online-Enzylopädie. Sie können diesen Artikel online finden unter <a href=\"$2\">$2</a>" ;

$wikiOtherLanguagesText = "Andere Sprachen : $1" ;

# date
$wikiDate = array () ;
$wikiDate["monday"] = "Montag" ;
$wikiDate["tuesday"] = "Dienstag" ;
$wikiDate["wednesday"] = "Mittwoch" ;
$wikiDate["thursday"] = "Donnerstag" ;
$wikiDate["friday"] = "Freitag" ;
$wikiDate["saturday"] = "Sonnabend" ;
$wikiDate["sunday"] = "Sontag" ;
$wikiDate["january"] = "Januar" ;
$wikiDate["february"] = "Februar" ;
$wikiDate["march"] = "März" ;
$wikiDate["april"] = "April" ;
$wikiDate["may"] = "Mai" ;
$wikiDate["june"] = "Juni" ;
$wikiDate["july"] = "Juli" ;
$wikiDate["august"] = "August" ;
$wikiDate["september"] = "September" ;
$wikiDate["october"] = "Oktober" ;
$wikiDate["november"] = "November" ;
$wikiDate["december"] = "Dezember" ;

#-----------------------------------------------------------------------
## strings for specialFunctions.php

# Moving
$wikiMoveRedirectMessage = "Verschoben nach $1" ;
$wikiMoveMoved = "$1 wurde erfolgreich nach $2 verschoben." ;
$wikiMoveRedirected = " EIne Weiterleitung wurde erzeugt." ;
$wikiMoveWarning = "<font color=red><b>'$1' existiert bereits! Bitte wählen Sie einen anderen Namen.</b></font><br><br>\n" ;
$wikiMoveForm = "
<h2>Sie sind dabei, '$1' und alle Revisionen zu einem neuen Titel zu verschieben.</h2>\n
<FORM method=post>\n
Neuer Titel : <INPUT type=text value='$2' name=newname size=40 maxlength=250><br><br>\n
<INPUT type=checkbox$3 name=doredirect>Eine Weiterleitung von '$1' zum neuen Titel generieren<br><br>\n
<INPUT type=submit name=doit value='Verschieben'>\n
</FORM>\n" ;

# Log out / log in
$wikiGoodbye = "<h1>Bis bald, $1!</h1>" ;
$wikiWelcomeCreation = "<h1>Willkommen, $1!</h1><font color=red>Vergessen Sie nicht, Ihre persönlichen Einstellungen anzupassen!</font><br>Ihr Benutzerkonto wurde erzeugt. Bitte klicken Sie auf \"Einloggen\", um endgültig einzuloggen!" ; ;
$wikiLoginPageTitle = "Einloggen" ;
$wikiYourName = "Ihr Benutzername&nbsp; : " ;
$wikiYourPassword = "Ihr Passwort&nbsp; : " ;
$wikiYourPasswordAgain = "Passwort (erneut) : " ;
$wikiNewUsersOnly = " (nur neue Benutzer)" ;
$wikiRememberMyPassword = "Mein Passwort als Cookie speichern." ;
$wikiLoginProblem = "<b>Es gab ein Problem beim Einloggen.</b><br>Bitte versuchen Sie es noch einmal!" ;
$wikiAlreadyLoggedIn = "<font color=red><b>$1, Sie sind bereits eingeloggt!</b></font><br>\n" ;
$wikiPleaseLogIn = "<h1>Bitte einloggen:</h1>\n" ;
$wikiAreYouNew = "Wenn Sie neu bei EIkipedia sind und ein Benutzerkonto möchten, geben Sie bitte einen Benutzernamen und ein Passwort ein. Wiederholen Sie das Passwort.
Die Angabe einer email-Adresse ist freiwillig; falls Sie Ihr Passwort verlieren, kann Ihnen ein neues zu dieser Adresse geschickt werden.<br>\n" ;
$wikiLogIn = "Einloggen" ;
$wikiCreateAccount = "Ein neues Benutzerkonto anlegen" ;

# User preferences
$wikiUserSettings = "Benutzereinstellungen" ;
$wikiUserSettingsError = "Sie sind nicht eingeloggt! [[special:userLogin|Loggen Sie sich ein]] oder gehen sie zur [[:Hauptseite|Hauptseite]]" ;
$wikiUserSettingsNewPasswordError = "<h1><font color=red>Die beiden Passwörter sind verschieden. DAS PASSWORT WURDE NICHT GEÄNDERT!</font></h1>\n" ;
$wikiUserSettingsSaved = "<font color=red size=\"+1\">Ihre Einstellungen wurden gespeichert!</font>" ;
$wikiLoggedInAs = "<b>Sie sind eingeloggt als [[benutzer:$1|$1]]. ";
$wikiID_Help = "Ihre interne ID ist $1.</b> [[wikipedia:Hilfe/Benutzereinstellungen|Hier]] werden Sie geholfen :)" ;
$wikiQuickBarSettings = "Seitenleiste :" ;
$wikiSettingsStandard = "Standard" ;
$wikiSettingsNone = "Keine" ;
$wikiSettingsLeft = "Links" ;
$wikiSettingsRight = "Rechts" ;
$wikiOldPassword = "Altes Passwort&nbsp; &nbsp; : " ;
$wikiNewPassword = "Neues Passwort&nbsp; &nbsp; : " ;
$wikiSkin = "Skin :" ;
$wikiStarTrek = "Star Trek" ;
$wikiShowHoverBox = "Schwebender Titel über Wikipedia-Verweisen" ;
$wikiUnderlineLinks = "Verweise unterstreichen" ;
$wikiNewTopicsRed = "Nicht existierende Themen in Rot zeigen" ;
$wikiJustifyParagraphs = "Bündige Absätze" ;
$wikiShowRecentChangesTable = "<i>Letzte Änderungen</i> als Tabelle zeigen" ;
$wikiHideMinorEdits = "Kleinere Änderungen nicht in den <i>letzten Änderungen</i> anzeigen" ;
$wikiDoNumberHeadings = "Überschriften automatisch nummerieren" ;
$wikiViewWithFrames = "Seiten als Frames anzeigen <i>(experimentell, NUR im Linux-Konqueror!)</i>" ;
$wikiTurnedOn = "ja" ;
$wikiTurnedOff = "nein" ;
$wikiTextboxDimensions = "Größe des Textrahmens beim Bearbeiten :" ;
$wikiCols = "Spalten : " ;
$wikiRows = "Zeilen&nbsp; : " ;
$wikiYourEmail = "Ihre email : " ;
$wikiResultsPerPage = "Bis zu $1 Suchergebnisse pro Seite anzeigen" ;
$wikiTimeDiff = "Zeitunterschied zum Server : $1 Stunden" ;
$wikiViewRecentChanges = "Die letzten $1 ''letzten Änderungen'' anzeigen" ;
$wikiOutputEncoding = "Ausgabeeinstellungen : ";

# Search Page
$wikiSearchTitle = "Suche" ;
$wikiSearchedVoid = "Da Sie nach nichts gesucht haben, haben Sie nichts gefunden." ;
$wikiNoSearchResult = "Es wurden keine Artikel mit \"$1\" im Titel oder Text gefunden." ;
$wikiSearchHelp = "Bitte beachten Sie den [[wikipedia:Suchen|Hilfstext zum Suchen]]." ;
$wikiFoundHeading = "Artikel im der deutschen Wikipedia" ;
$wikiFoundText = "Die Anfrage ''$2'' ergab $1 Artikel. Für jeden dieser Artikel sehen Sie den ersten Absatz und einen Absatz, der mindestens einen der Suchbegriffe enthält." ;
# keywords used for boolean search operators
# note: these must consist of character, no symbols allowed
$and = "und";
$or = "oder";
$not = "nicht";
# syntax errors for parser of boolean search queries
$srchSyntErr = array (
                    "SYNTAXFEHLER: fehlendes '$1'; eingefügt",
                    "SYNTAXFEHLER: unerwartetes '$1'; ignoriert",
                    "SYNTAXFEHLER: nicht erlaubtes Symbol '$1'; ignoriert",
                    "SYNTAXFEHLER: das Wort '$1' ist zu kurz, der Index enthält nur Worte ab $2 Zeichen Länge",
                    "SYNTAXFEHLER: fehlendes Suchwort; eingefügt"
               ) ;   
$wikiSearchError = "Entschuldigung, Ihre Suchanfrage enthielt die folgenden Fehler : " ;

# Misc
$wikiLonelyPagesTitle = "Verwaiste Artikel" ;
$wikiLonelyPagesText = "'''Diese Artikel existieren, aber kein anderer Artikel verweist auf sie!'''<br>''Diskussionsseiten, leere Seiten und #REDIRECT-Seiten werden hier '''nicht''' angezeigt.''\n\n" ;
$wikiAllPagesTitle = "Alle Seiten" ;
$wikiAllPagesText = "'''Dies sind alle Seiten in der Datenbank!'''\n\n" ;
$wikiUnsuccessfulSearch = "Ergebnislose Suche nach '$1'" ;
$wikiUnsuccessfulSearches = "wikipedia:Ergebnislose Suchanfragen ($1)" ; # $1 becomes "YEAR-MONTH"
$wikiUserlistTitle = "Benutzerverzeichnis" ;
$wikiUserlistText = "'''Dies sind alle eingetragenen Benutzer der deutschen Wikipedia!'''" ;
$wikiRecentChangesTitle = "Letzte Änderungen" ;
$wikiRecentChangesLastDays = "Dies sind die letzten <b>$1</b> Änderungen, die in den letzten <b>$2</b> Tagen hier gemacht wurden." ;
$wikiRecentChangesSince = "Dies sind die letzten <b>$1</b> Änderungen seit dem <b>$2</b>." ;
$wikiEditTypes = array ( "0"=>"" , "1"=>"<font color=cyan>K</font>" , "2"=>"<font color=green>N</font>" ) ; #Abbreviations for minor edits (1) and new pages (2) to be used on RecentChanges
$wikiViewLastDays = "Zeige die letzten $1 Tage an" ;
$wikiViewMaxNum = "Zeige die letzten $1 Änderungen an" ;
$wikiListOnlyNewChanges = "Nur neue Änderungen zeigen" ;
$wikiNewPagesTitle = "Neue Seiten" ;
$wikiNewPagesText = "Dies sind die letzten <b>$1</b> neuen Seiten, die in den letzten <b>$2</b> Tagen erstellt wurden." ;
$wikiRCLegend = " <b>Legende :</b> $1=Kleine Änderung ; $2=Neuer Artikel." ;
$wikiDiff = "(Unterschiede)" ;
$wikiChange = "Änderung" ;
$wikiChanges = "Änderungen" ;
$wikiWatchYes = "\"$1\" wurde zu Ihrer [[special:WatchList|watchlist]] hinzugefügt." ;
$wikiWatchNo = "\"$1\" wurde von Ihrer [[special:WatchList|watch list]] entfernt." ;
$wikiWatchlistTitle = "Watchlist" ;
$wikiWatchlistExistText = "'''Im Moment beobachtet das System die folgenden Artikel für Sie :'''" ;
$wikiWatchlistNotExistText = "'''Im Moment beobachten Sie die folgenden nichtexistierenden Artikel :'''" ;

# Statistics
$wikiStatisticsTitle = "Artikelstatistik" ;
$wikiStatTotalPages = "Es gibt $1 Seiten in der Datenbank." ;
$wikiStatTalkPages = "Es gibt $1 Diskussions-Seiten." ;
$wikiStatCommaPages = "Es gibt $1 Seiten, die ein Komma enthalten und ''keine'' '''Diskussion'''s-Seiten sind." ;
$wikiStatWikipediaNoTalk = "Es gibt $1 Seiten, die \"ikipedi\" im Titel haben und ''keine'' '''Diskussion'''s-Seiten sind." ;
$wikiStatSubNoTalk = "Es gibt $1 Unterseiten, die ''keine'' '''Diskussion'''s-Seiten sind." ;
$wikiStatNoTalk = "Das heisst, es gibt ca. $1 Artikel, Unterseiten eingeschlossen." ;
$wikiStatArticles = "Oder, es gibt ca. $1 Artikel (ohne Unterseiten)!" ;
$wikiStatRedirect = "Es gibt etwa $1 #REDIRECT-Seiten." ;
$wikiStatSkin = "<font color=red>$1</font> von ihnen benutzen das \"$2\"-Skin." ;
$wikiStatJunk = "Schliesslich gibt es etwa $1 Schrottseiten :-(" ;
$wikiStatOld = "Und, es gibt $1 alte Versionen von Artikel in der Datenbank, oder etwa $2 alte auf eine aktuelle Seite." ;
$wikiUserStatistics = "Benutzerstatistik" ;
$wikiStatUsers = "Es gibt $1 [[special:ListUsers|eingetragene Benutzer]]." ;
$wikiStatSysops = "$1 von ihnen sind Systemadministratoren." ;

# Upload
$wikiUploadTitle = "Auf dieser Seite können Sie Dateien ins System hochladen" ;
$wikiUploadDenied = "Sie sind weder ein Systemadministrator noch ein Editor. Kehren sie zur <a href=\"$THESCRIPT?action=upload\">Upload-Seite</a> zurück." ;
$wikiUploadDeleted = "Datei <b>$1</b> wurde gelöscht!" ;
$wikiUploadDelMsg1 = "*Benutzer [[user:$1|$1]] hat die Datei '''$2''' gelöscht ($3)\n" ;
$wikiUploadDelMsg2 = "'$1' wurde gelöscht" ;
$wikiUploadAffirm = "<nowiki>Sie müssen angeben, dass diese Datei kein Copyright verletzt. Kehren Sie zur <a href=\"$THESCRIPT?title=special:upload\">Upload-Seite</a> zurück</nowiki>" ;
$wikiUploadRestrictions = "<nowiki>Um eine Datei hichzuladen, müssen Sie sich einloggen!</nowiki>" ;
$wikiUploadFull = "Die Platte ist voll :(" ;
$wikiUploadSuccess = "Die Datei <b>$1</b> wurde erfolgreich hochgeladen!" ;
$wikiUploadSuccess1 = "*Benutzer $2 hat die Datei '''$3'''$4 hochgeladen ($1)\n" ;
$wikiUploadSuccess2 = "Datei '$1' wurde hochgeladen" ;
$wikiUploadText = "<h2>Anleitung:</h2><ul>\n" ;
$wikiUploadText .= "<li><strong>Benutzen Sie dieses Formular, um Dateien hochzuladen</strong></li>\n";
$wikiUploadText .= "<li>Um eine alte Datei zu ersetzen, können Sie einfach eine\n";
$wikiUploadText .= "neue Datei mit dem selben Namen hochladen.\n";
$wikiUploadText .= "<li><strong>Und so funktioniert es. </strong>Click\n";
$wikiUploadText .= "&quot;Finden...&quot; Sie die Datei auf ihrer Festpolatte \n";
$wikiUploadText .= "in einem Dateidialog.</li>\n";
$wikiUploadText .= "<li>Klicken Sie dann auf &quot;Öffnen.&quot;\n";
$wikiUploadText .= "<li>Vergessen Sie nicht, das Copyright zu bestätigen!</li>\n";
$wikiUploadText .= "<li>Klicken Sie dann auf &quot;Hochladen.&quot; Das kann eine Weile dauern.</li>\n";
$wikiUploadText .= "<li>Das System wird das erfolgreiche Hochladen bestätigen.</li>\n";
$wikiUploadText .= "<li>Sie können so viele Dateien hochladen wie Sie wollen.</li>\n";
$wikiUploadText .= "<li>Jedes Hochladen und Löschen wird im <a href=\"$THESCRIPT?title=Log:Uploads\">Uploads-Log</a> vermerkt.</li>\n";
$wikiUploadText .= "</ul>\n";
$wikiUploadAffirmText = "Hiermit bestätige ich, dass diese Datei <b>keinem Copyright unterliegt</b>, oder dass ich das Copyright für diese Datei besitzt und diese hiermit unter GFDL-Lizenz stelle." ;
$wikiUploadButton = "Hochladen" ;
$wikiUploadPrev = "Bereits hochgeladene Dateien:" ;
$wikiUploadSize = "Größe (byte)" ;
$wikiFileRemoval = "Datei löschen" ;
$wikiUploadRemove = "Hier klicken, um $1 zu löschen." ;


# Misc
$wikiHistoryTitle = "Revisionen von $1" ;
$wikiHistoryHeader = "Dies sind alle Revisionen von <a href=\"$THESCRIPT?title=$1\">$2</a>" ;
$wikiCurrentVersion = "aktuell" ;
$wikiSpecialTitle = "Spezielle Seiten" ;
$wikiSpecialText = "<b>Dies ist eine Liste spezieller Seiten.</b> Einige von ihnen stehen nur eingeloggten Benutzern zur Verfügung. Wenn Sie eingeloggt sind, können Sie einen Teil dieser Liste stets als Seitenleiste eingeblendet lassen.<br><br>" ;
$wikiStubTitle = "Kurze Artikel" ;
$wikiLongPagesTitle = "Die längsten Artikel" ;
$wikiStubText = "'''Dies sind alle Artikel in der Datenbank, sortiert nach Länge, mit den kürzesten zuerst.'''<br>''Weitergeleitete (#REDIRECTed) Seiten und Seiten in Namensräumen (z.B. Diskussion:) werden hier '''nicht''' angezeigt!''\n\n" ;
$wikiLongPagesText = "'''Dies sind alle Artikel in der Datenbank, sortiert nach Länge, mit den längsten zuerst.'''<br>''Weitergeleitete (#REDIRECTed) Seiten und Seiten in Namensräumen (z.B. Diskussion:) werden hier '''nicht''' angezeigt!''\n\n" ;
$wikiStubChars = "$1 Zeichen" ;
$wikiAskSQL = "MySQL-Zugang" ;
$wikiAskSQLtext = "Diese Funktion ist nur für Systemadministratoren zugänglich.<br>''Bitte nicht Eingabe drücken, '''immer''' nur mit der Maus auf den Knopf klicken!''" ;
$wikiSQLSafetyMessage = "Da Sie keinen Entwicklerstatus haben, können Sie nur SELECT-Kommandos ausführen." ;
$wikiStubDelete = "<b>Diese Seite löschen!</b>" ;
$wikiStubLinkHere = "$1 Artikel verweisen auf diesen." ;
$wikiStubShowLinks = "Die \"Verweisanzeige\" umschalten" ;
$wikiShowLinks = "Zeige Seiten, die nach \"$1\" verweisen" ;
$wikiRecentChangesLinkedTitle = "Die letzten Änderungen auf Seiten, zu denen '$1' verweist" ;
$wikiDeleteTitle = "Löschen des Artikels '$1'" ;
$wikiDeleteDenied = "<font size=\"+3\">Sie sind nicht berechtigt, diese Seite zu löschen!</font>" ;
$wikiDeleteSuccess = "'$1' wurde gelöscht." ;
$wikiDeleteMsg1 = "*[[user:$2|$2]] hat die Seite '''$3''' permanent gelöscht ($1)\n" ;
$wikiDeleteMsg2 = "Permanente Löschung von '$1'" ;
$wikiDeleteAsk = "Sie sind dabei, den Artikel \"$1\" und alle seine Revisionen zu löschen!<br>\nWenn Sie genau wissen, was Sie tun, klicken Sie bitte <a href=\"$2&iamsure=yes\">hier</a>." ;
$wikiProtectTitle = "Schützen des Artikels '$1'" ;
$wikiProtectDenied = "<font size=\"+3\">Sie sind nicht berechtigt, diese Seite zu schützen!</font>" ;
$wikiProtectNow = "Die Seite '$1' ist nun geschützt ($2)." ;
$wikiProtectText = "<font size=\"+2\">Sie können nun die Seite '$1' schützen</font><br><i>Zum Bleistift, \"is_sysop\" wird verhindern, dass jemand anderes als ein Systemadministrator diese Seite ändert. Mehrere Berechtigungen werden durch \",\" getrennt.</i>" ;
$wikiProtectCurrent = "Gegenwärtiger Schutz : " ;
$wikiContribTitle = "Beiträge von $1" ;
$wikiContribText = "<h1>Beiträge von $1' :</h1>\n(Ausnahmen: Kleine Veränderungen sowie ''Disskussion'' und ''Log''-Seiten)" ;
$wikiContribDenied = "Geben Sie einen Benutzernamen an!" ;
$wikiLinkhereTitle = "Seiten, die nach '$1' verweisen" ;
$wikiLinkhereBacklink = "[[$1]] verweist zurück auf diese Artikel:" ;
$wikiLinkhereNoBacklink = "[[$1]] verweist ''nicht'' zurück auf diese Artikel:" ;
$wikiBacklinkNolink = "Es gibt keine Artikel, die nach [[$1]] verweisen!" ;
$wikiBacklinkFollowing = "Die folgenden Artikel verweisen auf [[$1]]:" ;
$wikiWantedTitle = "Die am meisten gewünschten Seiten" ;
$wikiWantedText = "'''Diese Artikel existieren noch nicht, aber andere Artikel verweisen auf sie!''' (die \"Top 50\")<br>\n" ;
$wikiWantedToggleNumbers = "Hier klicken, um die Anzeige von Artikeln, die mit Ziffern beginnen, umzuschalten (momentan $1)" ;
$wikiWantedLine = "<b>$2</b> Artikel verweisen auf $1 <nowiki>(<a href=\"$3\">diese Seiten verweisen auf \"$4\"</a></nowiki>)" ;
$wikiLastChange = "Letzte Änderung: $1" ;
$wikiLastChangeCologne = "Letzte Änderung dieser Seite: $1" ;
$wikiShowLastChange = "Die letzten Änderungen" ;
$wikiProtectThisPage = "Diese Seite schützen" ;
$wikiPopularPages = "Populäre Seiten" ;
$wikiPopularTitle = "Die populärsten Seiten" ;
$wikiRefreshThisPage = "Seite neu laden" ;
$wikiResourcesWarning = "(Bitte nur, wenn notwendig, da diese Funktion viel Rechenkapazität benötigt)" ;
$wikiNoRefresh = "(Diese Seite wurde erst von $1 Minuten aufrefrischt; bitte warten sie noch $2 Minuten und versuchen Sie es dann erneut.)" ;
$wikiLastRefreshed = "Zuletzt aufgefrischt: $1" ;
$wikiValidate = "Den HTML-Code dieser Seite überprüfen" ;
$wikiBlockIP = "Diese IP-Adresse blockieren" ;
$wikiNostalgy = "Nostalgie" ;
$wikiCologneBlue = "Cologne Blue" ;
$wikiUndiff = "Unterschiede ausblenden" ;
$wikiReadOnlyText = "Diese Kopie von Wikipedia ist nur zum Lesen gedacht. Selbstverständlich können Sie diesen Artikel in der <a href=\"http://de.wikipedia.com/wiki/$1&action=edit\">deutschen Wikipedia</a> bearbeiten!" ;

# Vote
$wikiVoteReason = "Grund für diese Stimme : " ;
$wikiVoteBecause = ", denn : <i>$1</i>" ;
$wikiVoteMessage = "Stimme von $1 für $2" ;
$wikiVoteWarn = "<font size=+2>Sie haben nicht gesagt, wofür Sie stimmen möchten! <a href=\"$1\">Nochmal versuchen</a>.</font>" ;
$wikiVotes = array ( "delete"=>"Stimmen für 'Löschen'" , "rewrite"=>"Stimmen für 'Neu Schreiben'" , "wikify"=>"Stimmen für 'wikifizieren'" , "NPOV"=>"Stimmen für 'NPOV'" , "aotd"=>"Stimmen für 'Artikel des Tages'" ) ;
$wikiVoteAdded = "<font size=+2>$1 wurde zu <a href=\"$2\">$3</a> hinzugefügt!</font>" ;
$wikiVoteError = "<font size=+2>Hier ist was richtig schiefgelaufen!</font>" ;
$wikiVoteChoices = "
<input type=radio value=delete name=voted>löschen<br>
<input type=radio value=rewrite name=voted>neu schreiben<br>
<input type=radio value=NPOV name=voted>NPOV<br>
<input type=radio value=wikify name=voted>wikifizieren<br>
<input type=radio value=aotd name=voted>Artikel des Tages<br><br>
Grund für diese Stimme : <input type=text value=\"\" name=CommentBox size=35> <input type=submit value=\"Vote\" name=doVote>
" ;




#---------------------------
#Functions
function wikiGetDateDe ( $x ) { # Used in RecentChangesLayout in special_functions.php
    global $wikiDate ;
    $dayName = $wikiDate [ strtolower ( date ( "l" , $x ) ) ];
    $monthName = $wikiDate [ strtolower ( date ( "F" , $x ) ) ];
    $dayNumber = date ( "j" , $x ) ;
    $year = date ( "Y" , $x ) ;
    return "$dayName, $dayNumber. $monthName, $year" ;
    }
function wikiGetBriefDateDe () { #Brief date for link in sidebar
    global $wikiDate ;
    $monthName = $wikiDate [ strtolower ( date ( "F" ) ) ];
    $dayNumber = date ( "j" ) ;
    $year = date ( "Y" ) ;
    return "$dayNumber. $monthName, $year" ;
    }
$wikiGetDate = 'wikiGetDateDe';
$wikiGetBriefDate = 'wikiGetBriefDateDe' ;

# In theory, this could be expanded to allow general conversion of the
# character encoding used in the database to another encoding optionally
# used on the browser end.

# Define these arrays if you need to set up conversion.
# $wikiEncodingCharsets = array("iso-8859-1");
# $wikiEncodingNames = array("Latin-1"); # Localised names

function wikiRecodeOutputDe($text) {
  # Stub
  # global $user;  # $user->options["encoding"] is an index into the above arrays
  return $text;
}

function wikiRecodeInputDe($text) {
  # Stub
  return $text;
}

$wikiRecodeOutput = 'wikiRecodeOutputDe';
$wikiRecodeInput = 'wikiRecodeInputDe';
?>
