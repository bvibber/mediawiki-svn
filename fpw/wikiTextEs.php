<?

# Esto contiene las cadenas para wikis internacionales o especializados
# ---------------------------------------------------------------------

# Juego de caracteres básicos y ajustes locales
$wikiCharset = "utf-8" ;
include_once ( "utf8Case.php" ) ;

# Miscelanea
$wikiMainPage = "Portada" ; # Título del artículo en la base de datos
$wikiErrorPageTitle = "¡Vaya! ¡Un error!" ;
$wikiErrorMessage = "<h2>$1!</h2>Volver a la [[:Portada|Portada]]!" ;
$wikiAllowedSpecialPages = array("userlogin","userlogout","newpages","recentchanges","upload","statistics","lonelypages","popularpages","wantedpages","allpages","randompage","shortpages","longpages","listusers","watchlist","special_pages","editusersettings","deletepage","movepage","protectpage","contributions","whatlinkshere","recentchangeslinked","sqldump","vote");
$wikiRecentChangesText = "Sigue las novedades y los cambios en wikipedia desde esta página. ¡[[Bienvenido, recien llegado]]! Por favor hecha un vistazo a estas páginas: [[Preguntas más frecuentes de wikipedia]], [[Política de wikipedia]] (especialmente [[convenciones para nombrar páginas]] y [[punto de vista neutral]]).<br> Es muy importante que no añadas material sujeto a [[derechos de autor]]. Los problemas legales podrían dañar el proyecto, así que te pedimos que no lo hagas." ;
$wikiMetaDescription = "$1... Lee más en wikipedia" ;

# Usado en cabeceras y pies
$wikiWikipediaHelp = "wikipedia:Ayuda" ;
$wikiMainPageTitle = "wikipedia" ; # Este es el título que se mostrará en la página principal
$wikiHeaderSubtitle = "wikipedia" ;
$wikiArticleSubtitle = "La Enciclopedia Libre." ;
$wikiPrintable = "Versión para imprimir" ;
$wikiWatch = "Realizar seguimiento" ;
$wikiNoWatch = "No realizar seguimiento" ;
$wikiTitleTag = "$1: artículo de wikipedia" ;
$wikiLogIn = "Iniciar sesión" ;
$wikiLogOut = "Cerrar sesión" ;
$wikiHelp = "Ayuda" ;
$wikiHelpLink = "wikipedia:Ayuda" ;
$wikiBlockedIPsLink = "wikipedia:IPs_bloqueadas" ; # ¡No olvides el subrayado si la traducción también tiene un espacio!
$wikiTalkBlockedIPsLink = "discusión_wikipedia:IPs_bloqueadas" ; # ¡No olvides el subrayado si la traducción también tiene un espacio!
$wikiPreferences = "Preferencias" ;
$wikiWhatLinksHere = "Paginas que enlazan aquí" ;
$wikiPrintLinksMarkup = "i" ; # será usado como <$wikiPrintLinksMarkup> y </$wikiPrintLinksMarkup> (?)
#$wikiAllowedNamespaces = array ( "wikipedia" , "discusión" , "usuario" , "" , "discusión wikipedia" , "discusión usuario" ) ;
$wikiTalk = "discusión" ;
$wikiUser = "usuario" ;
$wikiNamespaceTalk = "$1 Discusión" ;
$wikiWikipedia = "Wikipedia" ;
$wikiAllowedNamespaces = array ( $wikiWikipedia , $wikiTalk , $wikiUser , "" , "wikipedia $wikiTalk" , "$wikiUser $wikiTalk" ) ;
$wikiSkins = array ( "Normal" => "" , "Star Trek" => "Star Trek" , "Nostalgia" => "Nostalgy" , "Cologne Blue" => "Cologne Blue" ) ;
$wikiMyOptions = "Mis opciones" ;
$wikiMySettings = "Mis ajustes" ;
$wikiMyself = "Mi página" ;
$wikiShortPages = "Páginas cortas";
$wikiLongPages = "Páginas largas" ;
$wikiUserList = "Lista de usuarios" ;
$wikiEditingHistory = "Editando histórico" ;
$wikiAddToWatchlist = "Añadir a lista de seguimiento" ;
$wikiEditPage = "Editar esta página" ;
$wikiHome = "Portada" ;
$wikiAbout = "Acerca de" ;
$wikiFAQ = "FAQ" ;
$wikiPageInfo = "Información de página" ;
$wikiLinkedPages = "Páginas enlazadas" ;
$wikiShowDiff = "mostrar diferencias" ;
$wikiRequests = "Peticiones: $1" ;
$wikiEdit = "Editar" ;
$wikiPageOptions = "Opciones de página" ;
$wikiBrowse = "Buscar" ;
$wikiFind = "Encontrar" ;
$wikiOK = "Correcto" ;
$wikiFindMore = "Encontrar más";
$wikiWikipediaHome = "Portada" ;
$wikiAboutWikipedia = "Acerca de wikipedia" ;
$wikiAutoWikify = "Auto-wikificación (¡usar con cuidado!)" ;
$wikiTopics = "Tópicos" ;
$wikiWikipediaFAQ = "wikipedia:FAQ" ;
$wikiVoteForPage = "Vota por esta página" ;

# Editando
$wikiEditingHelp = "Editando ayuda" ;
$wikiWikipediaEditingHelp = "wikipedia:Como se edita una página" ;
$wikiEditTitle = "Editando $1" ;
$wikiCannotEditPage = "<h3>¡No puedes editar esta página!</h3>" ;
$wikiEditConflictMessage = "<h1>¡Conflicto de edición!</h1>\n<b>Alguien guardó esta página
después de que tú comenzaras a editarla. La caja de texto de arriba contiene el texto guardado. Sólo el texto de esa caja será guardado.</b><br>\nMira más abajo para ver el texto que tú has editado y las diferencias entre las dos versiones.<br>\n" ;
$wikiPreviewAppend = "<hr>\n<h2>Vista previa :</h2>\n$1<hr><h3>¡Recuerda, esto sólo es una vista previa, todavía no ha sido guardada!</h3>" ;
$wikiSummary = "Resumen:" ;
$wikiDescription = "Descripción:" ;
$wikiMinorEdit = "Esto es un cambio menor." ;
$wikiCopyrightNotice = "Por favor, ten en cuenta que todas las contribuciones a wikipedia
se consideran publicadas bajo la licencia GNU para documentación libre. Si no quieres que tus escritos sean editados sin misericordia y redistribuidos a voluntad, no pulses 'guardar'. También nos garantizas que has escrito esto tu mismo o lo has copiado de un recurso de dominio público. <b>¡NO USES MATERIAL CON DERECHOS DE AUTOR SIN PERMISO!</b>" ;
$wikiSave = "Guardar" ;
$wikiPreview = "Vista previa" ;
$wikiReset = "Cancelar" ;
$wikiDontSaveChanges = "<i>Cancelar</i>" ;
$wikiDescribePage = "Escribe el nuevo artículo aquí." ;

# wikiUser
$wikiNoSuchUser = "<font color=red>Usuario desconocido $1!</font>" ;
$wikiWrongPassword = "<font color=red>Contraseña incorrecta para el usuario $1!</font>" ;
$wikiYouAreLoggedIn = "¡$1, has iniciado sesión!" ;
$wikiUserError = "Error con \"$1\"" ;

# wikiPage
$wikiNoSuchSpecialPage = "<h1>¡No existe esta página especial \"$1\"!</h1>" ;
$wikiOldVersion = "<br><font size=\"-1\">Esta es la versión anterior #$1; mira la <a href=\"$THESCRIPT?title=$2\">versión actualizada</a></font>" ;
$wikiRedirectFrom = "(redirigido desde $1)" ;
$wikiRecentChanges = "Cambios recientes" ;
$wikiRecentChangesLink = "RecentChanges" ; # "special:" es añadido automáticamente
$wikiRecentLinked = "Enlaces a páginas vigiladas" ;
$wikiRecentLinkedLink = "Cambios_recientes_enlazados" ;
$wikiSpecialPagesLink = "Páginas_especiales" ; # "special:" es añadido automáticamente
$wikiEditThisPage = "Editar esta página" ;
$wikiMoveThisPage = "Mueve esta página" ;
$wikiDeleteThisPage = "Borra esta página" ;
$wikiUpload = "Enviar archivos" ;
$wikiHistory = "Histórico" ;
$wikiRandomPage = "Página aleatoria" ;
$wikiSpecialPages = "Páginas especiales" ;
$wikiEditHelp = "<i>Edita ayuda</i>" ;
$wikiEditHelpLink = "wikipedia:Como_se_edita_una_página" ;
$wikiStatistics = "Estadísticas" ;
$wikiNewPages = "Páginas nuevas" ;
$wikiOrphans = "Páginas huérfanas" ;
$wikiMostWanted = "Más buscadas" ;
$wikiAllPages = "Todas las páginas" ;
$wikiStubs = "Artículos cortos" ;
$wikiLongPages = "Artículos largos" ;
$wikiListUsers = "Lista de usuarios" ;
$wikiMyWatchlist = "Mi lista de seguimiento" ;
$wikiBeginDiff = "COMIENZA DIFERENCIA" ;
$wikiEndDiff = "FINALIZA DIFERENCIA" ;
$wikiDiffLegend = "<font color=#2AAA2A>El texto en verde</font> fue añadido o cambiado, <font color=#AAAA00>el texto en amarillo</font> fue cambiado o borrado." ;
$wikiDiffFirstVersion = "Esta es la primera versión del artículo. ¡Todo el texto es nuevo!<br>\n" ;
$wikiDiffImpossible = "Esta es la primera versión de este artículo. ¡Todo el texto es nuevo!<br>\n" ;
$wikiSearch = "Buscar" ;
$wikiOtherNamespaces = "<b>Otros espacios de nombres:</b> " ;
$wikiCategories = "<b>Categorias:</b> " ;
$wikiThisCategory = "Artículos en esta categoría" ;
$wikiCounter = "Esta página ha sido visitada $1 veces." ;
$wikiBlockIPTitle = "Bloquear una IP (sólo operadores)" ;
$wikiBlockIPText = "IP $1 fue bloqueada por $2" ;
$wikiBlockInvalidIPAddress = "\"$1\" no es una dirección IP válida; no se puede bloquear." ;
$wikiBlockExplain = "Vas a bloquear la dirección IP [$wikiCurrentServer$THESCRIPT?title=special:contributions&theuser=\$1 \$1]. Una vez bloqueada quien visite wikipedia desde esta dirección IP no podrán editar artículos a menos que un operador elimine la IP de la lista de direcciones bloqueadas en [[$wikiBlockedIPsLink]].
Si estás seguro de que quieres bloquear a este usuario, se tan amable de dejar una nota breve de las razones que tienes para hacerlo en la caja de texto más abajo y pulsa &quot;Bloquear esta IP&quot;. Puedes escribir una explicación más larga en [[$wikiTalkBlockedIPsLink]]." ;
$wikiIPblocked = "<font color=red size='+1'>¡Tu IP ha sido bloqueada! No podrás guardar lo que edites. Por favor, contacta con un operador para que este bloqueo sea eliminado o inténtalo más tarde.</font>" ;
$wikiBugReports = "Informes de error" ;
$wikiBugReportsLink = "wikipedia:Informes de error" ;
$wikiPrintFooter = "<hr>Este artículo procede de <b>Wikipedia</b> (<a href=\"$1\">$1</a>). Puedes encontrar este artículo en <a href=\"$2\">$2</a>" ;

# Interwiki links
# Don't need to inclide the inter-wiki link tables

$wikiOtherLanguagesText = "Otros lenguajes: $1" ;

# date
$wikiDate = array () ;
$wikiDate["monday"] = "Lunes" ;
$wikiDate["tuesday"] = "Martes" ;
$wikiDate["wednesday"] = "Miércoles" ;
$wikiDate["thursday"] = "Jueves" ;
$wikiDate["friday"] = "Viernes" ;
$wikiDate["saturday"] = "Sábado" ;
$wikiDate["sunday"] = "Domingo" ;
$wikiDate["january"] = "Enero" ;
$wikiDate["february"] = "Febrero" ;
$wikiDate["march"] = "Marzo" ;
$wikiDate["april"] = "Abril" ;
$wikiDate["may"] = "Mayo" ;
$wikiDate["june"] = "Junio" ;
$wikiDate["july"] = "Julio" ;
$wikiDate["august"] = "Agosto" ;
$wikiDate["september"] = "Septiembre" ;
$wikiDate["october"] = "Octubre" ;
$wikiDate["november"] = "Noviembre" ;
$wikiDate["december"] = "Diciembre" ;

#-----------------------------------------------------------------------
## strings for specialFunctions.php

# Moving
$wikiMoveRedirectMessage = "Movido a $1" ;
$wikiMoveMoved = "$1 fue movido con éxito a $2" ;
$wikiMoveRedirected = " Se ha redirigido una página." ;
$wikiMoveWarning = "<font color=red><b>¡'$1' ya existe! Por favor elige otro nombre.</b></font><br><br>\n" ;
$wikiMoveForm = "
<h2>Vas a mover '$1' y su histórico a un nuevo nombre.</h2>\n
<FORM method=post>\n
Nuevo nombre: <INPUT type=text value='$2' name=newname size=40 maxlength=250><br><br>\n
<INPUT type=checkbox$3 name=doredirect>Crear una redirección (#REDIRECT) de '$1' al nuevo título<br><br>\n
<INPUT type=submit name=doit value='Move'>\n
</FORM>\n" ;

# Log out / log in
$wikiGoodbye = "<h1>¡Hasta pronto, $1!</h1>" ;
$wikiWelcomeCreation = "<h1>¡Bienvenido, $1!</h1><font color=red>No olvides personalizar tus preferencias en wikipedia.</font><br>Tu cuenta ha sido creada. Por favor pulsa \"Iniciar sesión\" una vez más para entrar" ; ;
$wikiLoginPageTitle = "Inicio de sesión" ;
$wikiYourName = "Tu nombre de usuario&nbsp; : " ;
$wikiYourPassword = "Tu contraseña&nbsp;&nbsp; : " ;
$wikiYourPasswordAgain = "Reescribe contraseña: " ;
$wikiNewUsersOnly = " (sólo usuarios nuevos)" ;
$wikiRememberMyPassword = "Recuerda mi contraseña (como una cookie)." ;
$wikiLoginProblem = "<b>Hubo un problema con tu inicio de sesión.</b><br>¡Inténtalo de nuevo!" ;
$wikiAlreadyLoggedIn = "<font color=red><b>Usuario $1, ya has entrado.</b></font><br>\n" ;
$wikiPleaseLogIn = "<h1>Por favor, inicia sesión:</h1>\n" ;
$wikiAreYouNew = "Si eres nuevo en wikipedia y quieres una cuenta de usuario, entra un nombre de usuario y teclea dos veces una contraseña.
La dirección de correo es opcional; si pierdes tu contraseña puedes pedir que se te mande una nueva a tu dirección de correo.<br>\n" ;
$wikiLogIn = "Inicio de sesión" ;
$wikiCreateAccount = "Crea una nueva cuenta" ;

# User preferences
$wikiUserSettings = "Ajustes de usuario" ;
$wikiUserSettingsError = "¡No has iniciado sesión! [[special:userLogin|Inicia sesión]] o ve a la  [[:Main Page|Portada]]" ;
$wikiUserSettingsNewPasswordError = "<h1><font color=red>La nueva contraseña no coincide. ¡LA CONTRASEÑA NO SE HA CAMBIADO!</font></h1>\n" ;
$wikiUserSettingsSaved = "<font color=red size=\"+1\">Tus ajustes han sido guardados.</font>" ;
$wikiLoggedInAs = "<b>Has entrado como [[usuario:$1|$1]]. ";
$wikiID_Help = "Tu ID interno es $1.</b> Puedes obtener ayuda [[wikipedia:Ayuda/Preferencias de usuario|aquí]]." ;
$wikiQuickBarSettings = "Menú rápido de ajustes:" ;
$wikiSettingsStandard = "Estandar" ;
$wikiSettingsNone = "Ninguno" ;
$wikiSettingsLeft = "Izquierda" ;
$wikiSettingsRight = "Derecha" ;
$wikiOldPassword = "Contraseña anterior&nbsp; &nbsp; : " ;
$wikiNewPassword = "Contraseña nueva&nbsp; &nbsp; : " ;
$wikiSkin = "Apariencia :" ;
$wikiStarTrek = "Star Trek" ;
$wikiShowHoverBox = "Muestra recuadro sobre los enlaces wiki" ;
$wikiUnderlineLinks = "Enlaces subrayados" ;
$wikiNewTopicsRed = "Muestra enlaces vacios en rojo" ;
$wikiJustifyParagraphs = "Párrafos justificados" ;
$wikiShowRecentChangesTable = "Muestra <i>cambios recientes</i> en forma de tabla" ;
$wikiHideMinorEdits = "Oculta cambios menores en <i>cambios recientes</i>" ;
$wikiDoNumberHeadings = "Numera títulos automáticamente" ;
$wikiViewWithFrames = "Ver páginas usando marcos (experimental, SÓLO en Konqueror!)</i>" ;
$wikiTurnedOn = "si" ;
$wikiTurnedOff = "no" ;
$wikiTextboxDimensions = "Dimensiones del cuadro de texto :" ;
$wikiCols = "Columnas: " ;
$wikiRows = "Filas: " ;
$wikiYourEmail = "Tu correo: " ;
$wikiResultsPerPage = "Devuelve hasta $1 resultados de búsqueda por página" ;
$wikiTimeDiff = "Diferencia horaria: $1 horas" ;
$wikiViewRecentChanges = "Ver los últimos $1 cambios en ''Cambios recientes''" ;
$wikiOutputEncoding = "Codificación de salida: ";

# Search Page
$wikiSearchTitle = "Búsqueda" ;
$wikiSearchedVoid = "Buscaste el vacío y lo encontraste." ;
$wikiNoSearchResult = "Lo sentimos, no pudimos encontrar un artículo que se ajuste a la consulta \"$1\" en el título o en el contenido." ;
$wikiSearchHelp = "Por favor mira  [wikipedia:Búsqueda|la ayuda sobre búsquedas]]." ;
$wikiFoundHeading = "Artículos de wikipedia" ;
$wikiFoundText = "La consulta ''$2'' arrojó $1 artículos. Para cada artículo encontrado puedes ver su primer párrafo y el primer párrafo que contiene alguna de las palabras implicadas en la consulta." ;
# keywords used for boolean search operators
# note: these must consist of character, no symbols allowed
$and = "y";
$or = "o";
$not = "no";
# syntax errors for parser of boolean search queries
$srchSyntErr = array (
                    "ERROR SINTÁCTICO: perdido '$1'; insertado",
                    "ERROR SINTÁCTICO: inesperado '$1'; ignorado",
                    "ERROR SINTÁCTICO SINTÁCTICO: caracter no permitido '$1'; ignorado",
                    "ERROR SINTÁCTICO: la palabra '$1' es demasiado corta, el índice requiere al menos $2 caracteres",
                    "ERROR SINTÁCTICO: palabra de búsqueda perdida; insertada"
               ) ;
$wikiSearchError = "Lo sentimos, tu búsqueda booleana contiene los siguientes errores: " ;

# Misc
$wikiLonelyPagesTitle = "Páginas huérfanas" ;
$wikiLonelyPagesText = "'''Estos artículos existen, pero ningún artículo apunta a ellos'''<br>''Páginas de discusión, páginas vacias y páginas redirigidas (#REDIRECT) '''no''' están listadas aquí.''\n\n" ;
$wikiAllPagesTitle = "Índice de todas las páginas" ;
$wikiAllPagesText = "'''Estos son todos los artículos existentes en wikipedia'''\n\n" ;
$wikiUnsuccessfulSearch = "Búsqueda sin éxito para $1" ;
$wikiUnsuccessfulSearches = "wikipedia:Búsquedas sin éxito ($1)" ; # $1 becomes "YEAR-MONTH"
$wikiUserlistTitle = "Lista de usuarios" ;
$wikiUserlistText = "'''Estos son todos los usuarios de wikipedia (que tienen una cuenta)'''" ;
$wikiRecentChangesTitle = "Cambios Recientes" ;
$wikiRecentChangesLastDays = "Estos son los últimos <b>$1</b> cambios hechos en wikipedia en los últimos <b>$2</b> días." ;
$wikiRecentChangesSince = "Estos son los últimos <b>$1</b> cambios hechos en wikipedia desde <b>$2</b>." ;
$wikiEditTypes = array ( "0"=>"" , "1"=>"<font color=cyan>M</font>" , "2"=>"<font color=green>N</font>" ) ; #Abbreviations for minor edits (1) and new pages (2) to be used on RecentChanges
$wikiViewLastDays = "Ver los últimos $1 días" ;
$wikiViewMaxNum = "Ver los últimos $1 cambios" ;
$wikiListOnlyNewChanges = "Listar sólo nuevos cambios" ;
$wikiNewPagesTitle = "Páginas nuevas" ;
$wikiNewPagesText = "Estas son las últimas <b>$1</b> páginas nuevas en wikipedia en los últimos <b>$2</b> dias." ;
$wikiRCLegend = " <b>Legenda :</b> $1=Cambio menor ; $2=Nuevo artículo." ;
$wikiDiff = "(diferencias)" ;
$wikiChange = "cambio" ;
$wikiChanges = "cambios" ;
$wikiWatchYes = "\"$1\" fue añadido a tu [[special:WatchList|lista de seguimiento]]." ;
$wikiWatchNo = "\"$1\" fue eliminado de tu [[special:WatchList|lista de seguimiento]]." ;
$wikiWatchlistTitle = "Lista de seguimiento" ;
$wikiWatchlistExistText = "'''Actualmente, estás haciendo un seguimiento de los siguientes artículos existentes:'''" ;
$wikiWatchlistNotExistText = "'''Actualmente, estás haciendo un seguimiento de los siguientes artículos no existentes:'''" ;

# Statistics
$wikiStatisticsTitle = "Estadísticas de artículos" ;
$wikiStatTotalPages = "Hay $1 páginas en wikipedia." ;
$wikiStatTalkPages = "Hay  $1 páginas de '''Discusión'''." ;
$wikiStatCommaPages = "Hay $1 páginas con coma que ''no'' son páginas de '''Discusión'''." ;
$wikiStatWikipediaNoTalk = "Hay $1 que tienen  \"enciclopedi\" en el título y ''no'' son páginas de '''Discusión'''." ;
$wikiStatSubNoTalk = "Hay $1 subpáginas que ''no'' son páginas de '''Discusión'''." ;
$wikiStatNoTalk = "Lo que significa que hay unos $1 articles, incluyendo subpáginas (excepto '''Discusión''')." ;
$wikiStatArticles = "O que hay unos $1 artículos, sin contar ninguna subpágina" ;
$wikiStatRedirect = "Hay unas $1 páginas redirigidas (#REDIRECT)." ;
$wikiStatSkin = "<font color=red>$1</font> de ellos usan el skin \"$2\"." ;
$wikiStatJunk = "Finalmente, hay unas $1 páginas basura :-(" ;
$wikiStatOld = "Y hay $1 versiones anteriores de páginas en la base de datos, dando una media de $2 versiones anteriores por cada página activa." ;
$wikiUserStatistics = "Estadísticas de usuarios" ;
$wikiStatUsers = "Actualmente existen $1 [[special:UsersList|usuarios]] suscritos." ;
$wikiStatSysops = "$1 de ellos tienen el rango de operadores." ;

# Upload
$wikiUploadTitle = "Enviar archivos al servidor" ;
$wikiUploadDenied = "No eres editor ni operador. Vuelve a la página para <a href=\"$THESCRIPT?action=upload\">Enviar archivos</a>" ;
$wikiUploadDeleted = "Archivo <b>$1</b> eliminado" ;
$wikiUploadDelMsg1 = "*El $3, [[usuario:$1|$1]] eliminó el archivo '''$2'''\n" ;
$wikiUploadDelMsg2 = "Eliminación del archivo $1" ;
$wikiUploadAffirm = "<nowiki>Debes confirmar que el contenido del archivo no viola derechos de autor. Vuelve a la página para <a href=\"$THESCRIPT?title=special:upload\">Enviar archivos</a></nowiki>" ;
$wikiUploadRestrictions = "<nowiki>¡Debes iniciar sesión antes de enviar un archivo!</nowiki>" ;
$wikiUploadFull = "Lo sentimos, tenemos poco espacio de disco. No podemos permitir que envíes archivos ahora." ;
$wikiUploadSuccess = "El archivo <b>$1</b> fue enviado con éxito" ;
$wikiUploadSuccess1 = "*El $1, $2 envió el archivo '''$3'''$4\n" ;
$wikiUploadSuccess2 = "Envío del archivo $1" ;
$wikiUploadText = "<h2>Instruciones:</h2><ul>\n" ;
$wikiUploadText .= "<li><strong>Usa este formulario para enviar varios archivos</strong></li>\n";
$wikiUploadText .= "<li>Para reemplazar un archivo enviado anteriormente (p.e., una\n";
$wikiUploadText .= "nueva versión del artículo), simplemente manda de nuevo al servidor el\n";
$wikiUploadText .= "mismo archivo. Pero primero asegurate de que \n";
$wikiUploadText .= "no has cambiado el nombre.</li>\n";
$wikiUploadText .= "<li><strong>Aquí se explica como enviar un archivo. </strong>Click\n";
$wikiUploadText .= "&quot;Busca...&quot; para encontrar el archivo que\n";
$wikiUploadText .= "quieres enviar desde tu disco duro. Esto abrirá\n";
$wikiUploadText .= "la ventana de diálogo &quot;Elegir archivo&quot;.</li>\n";
$wikiUploadText .= "<li>Cuando hayas encontrado el archivo, pulsa &quot;Abrir.&quot;\n";
$wikiUploadText .= "Con esto se seleccionará el archivo y se cerrará el cuadro de diálogo\n";
$wikiUploadText .= "&quot;Elegir archivo&quot;.</li>\n";
$wikiUploadText .= "<li>¡No olvides comprobar cualquier advertencia sobre derechos de autor!</li>\n";
$wikiUploadText .= "<li>Entonces pulsa &quot;Enviar.&quot; El archivo comenzará a enviarse. Esto podrá tardar un poco\n";
$wikiUploadText .= "si el archivo es grande y tu conexión a Internet es lenta.</li>\n";
$wikiUploadText .= "<li>Un mensaje te avisará cuando el archivo se haya recibido correctamente.</li>\n";
$wikiUploadText .= "<li>Puedes enviar cuantos archivos quieras. Pero, por favor, no intentes reventar nuestro servidor, je je.</li>\n";
$wikiUploadText .= "<li>Todos los envíos y eliminaciones son registradas en el <a href=\"$THESCRIPT?title=Log:Uploads\">registro de envíos</a>.</li>\n";
$wikiUploadText .= "</ul>\n";
$wikiUploadAffirmText = "Por la presente afirmo que este archivo carece de <b>derechos de autor</b>, o que poseo los derechos para este archivo y lo coloco aquí bajo la licencia GFDL." ;
$wikiUploadButton = "Enviar" ;
$wikiUploadPrev = "Archivos envíados previamente:" ;
$wikiUploadSize = "Tamaño (bytes)" ;
$wikiFileRemoval = "Eliminación de archivo" ;
$wikiUploadRemove = "Pulsa aquí para eliminar $1." ;

# Misc
$wikiHistoryTitle = "Historico de $1" ;
$wikiHistoryHeader = "Este es el histórico de <a href=\"$THESCRIPT?title=$1\">$2</a>" ;
$wikiCurrentVersion = "actual" ;
$wikiSpecialTitle = "Páginas especiales" ;
$wikiSpecialText = "<b>Esta es una lista de páginas especiales.</b> Algunas de ellas sólo están disponibles si has iniciado sesión. Si has iniciado sesión, puedes tener esta lista abierta a izquierda o derecha de tu pantalla en forma de barra rápida.<br><br>" ;
$wikiStubTitle = "Artículos cortos" ;
$wikiLongPagesTitle = "Los mayores artículos" ;
$wikiStubText = "'''Estos son todos los artículos de wikipedia, ordenados por tamaño, los más pequeños primero.'''<br>''Páginas redirigidas (#REDIRECT) y páginas dentro de un espacio de nombres (como Discusión:) '''no''' están listadas aquí.''\n\n" ;
$wikiLongPagesText = "'''Estos son todos los artículos de wikipedia, ordenados por tamaño, los más grandes primeros.'''<br>''Las páginas redirigidas y las páginas dentro de un espacio de nombres (como Discusión:) '''no''' están listadas aquí.''\n\n" ;
$wikiStubChars = "$1 caracteres" ;
$wikiAskSQL = "Acceso MySQL" ;
$wikiAskSQLtext = "Esta función sólo está disponible para operadores.<br>''¡Por favor no pulse 'enter', '''siempre''' haga clic en el botón 'Ask'!''" ;
$wikiSQLSafetyMessage = "Lo sentimos, a menos que seas un desarrollador sólo puedes hacer consultas con SELECT." ;
$wikiStubDelete = "<b>¡Eliminar esta página!</b>" ;
$wikiStubLinkHere = "$1 artículos enlazan aquí." ;
$wikiStubShowLinks = "Conmutar opción \"mostrar enlaces\"" ;
$wikiShowLinks = "Mostrar páginas que enlazan a  \"$1\"" ;
$wikiRecentChangesLinkedTitle = "Cambios recientes en páginas enlazadas desde $1" ;
$wikiDeleteTitle = "Eliminando artículo '$1'" ;
$wikiDeleteDenied = "<font size=\"+3\">¡No te está permitido borrar esta página!</font>" ;
$wikiDeleteSuccess = "'$1' ha sido eliminado." ;
$wikiDeleteMsg1 = "*El $1, [[usuario:$2|$2]] eliminó permanentemente la página '''$3'''\n" ;
$wikiDeleteMsg2 = "Eliminación permanente de $1" ;
$wikiDeleteAsk = "¡Estás a punto de borrar el artículo \"$1\" y su histórico completo!<br>\nSi no estás completamente seguro de querer hacer esto, <a href=\"$2&iamsure=yes\">pulsa aquí</a>." ;
$wikiProtectTitle = "Protegiendo artículo '$1'" ;
$wikiProtectDenied = "<font size=\"+3\">¡No te está permitido proteger este artículo!</font>" ;
$wikiProtectNow = "La página '$1' está ahora protegida como $2." ;
$wikiProtectText = "<font size=\"+2\">Ahora puedes editar la protección para '$1'</font><br><i>Por ejemplo, usa \"is_sysop\" para prevenir que nadie que no sea operador pueda editar esta página. Separa distintos permisos con \",\"</i>" ;
$wikiProtectCurrent = "Protección actual : " ;
$wikiContribTitle = "Contribuciones de $1" ;
$wikiContribText = "<h1>Contribuciones de $1 :</h1>\n(Con la excepción de cambios menores y cambios en páginas ''discusión'' y ''log'')" ;

$wikiContribDenied = "¡Declara un nombre de usuario!" ;
$wikiLinkhereTitle = "Páginas que enlazan a $1" ;
$wikiLinkhereBacklink = "Estos artículos están enlazados desde [[$1]]:" ;
$wikiLinkhereNoBacklink = "Estos artículos ''no'' están enlazados desde [[$1]]:" ;
$wikiBacklinkNolink = "No hay artículos que enlacen a [[$1]]!" ;
$wikiBacklinkFollowing = "Los siguientes artículos enlazan a [[$1]]:" ;
$wikiWantedTitle = "Las páginas más buscadas" ;
$wikiWantedText = "'''Estos artículos no existen,pero otros artículos enlazan a ellos!''' (los 50 más enlazados)<br>\n" ;
$wikiWantedToggleNumbers = "Pulsa aquí para conmutar la visualización de títulos de artículos que comiencen con un número (actualmente $1)" ;
$wikiWantedLine = "$1 es requerido por <b>$2</b> artículos <nowiki>(ver las <a href=\"$3\">páginas que enlacen a \"$4\"</a></nowiki>)" ;
$wikiLastChange = "Ultima edición: $1" ;
$wikiLastChangeCologne = "Ultima página modificada: $1" ;
$wikiShowLastChange = "Mostrar último cambio" ;
$wikiProtectThisPage = "Proteger esta página" ;
$wikiPopularPages = "Las más populares" ;
$wikiPopularTitle = "Las páginas más populares" ;
$wikiRefreshThisPage = "Actualizar esta página" ;
$wikiResourcesWarning = "(Por favor, haga esto sólo cuando sea necesario, esta función consume gran cantidad de recursos del sistema)" ;
$wikiNoRefresh = "(La página fue actualizada hace $1 minutos; por favor espere otros $2 minutos e inténtelo de nuevo.)" ;
$wikiLastRefreshed = "Última actualización $1" ;
$wikiValidate = "Validar esta página" ;
$wikiBlockIP = "Bloquear esta IP" ;
$wikiNostalgy = "Nostalgia" ;
$wikiCologneBlue = "Cologne Blue" ;
$wikiUndiff = "Sin diferencias" ;

# Vote
$wikiVoteReason = "Explicación del voto : " ;
$wikiVoteBecause = ", explicación : <i>$1</i>" ;
$wikiVoteMessage = "Voto por $2 para $1" ;
$wikiVoteWarn = "<font size=+2>¡No dijiste para que votabas! <a href=\"$1\">Inténtalo de nuevo</a>.</font>" ;
$wikiVotes = array ( "delete"=>"Voto para eliminar" , "rewrite"=>"Voto para reescribir" , "wikify"=>"Voto para wikificación" , "NPOV"=>"Voto para PDVNar" , "aotd"=>"Voto para artículo-del-día" ) ;
$wikiVoteAdded = "<font size=+2>$1 ha sido añadido a <a href=\"$2\">$3</a>!</font>" ;
$wikiVoteError = "<font size=+2>¡Algo fue realmente mal aquí!</font>" ;
$wikiVoteChoices = "
<input type=radio value=delete name=voted>eliminado<br>
<input type=radio value=rewrite name=voted>reescrito<br>
<input type=radio value=NPOV name=voted>PDVNado<br>
<input type=radio value=wikify name=voted>wikificado<br>
<input type=radio value=aotd name=voted>artículo-del-día<br><br>
Explicación del voto : <input type=text value=\"\" name=CommentBox size=20> <input type=submit value=\"Votar\" name=doVote>
" ;


#---------------------------
#Functions
function wikiGetDateEs ( $x ) { # Used in RecentChangesLayout in special_functions.php
    global $wikiDate ;
    $dayName = $wikiDate [ strtolower ( date ( "l" , $x ) ) ];
    $monthName = $wikiDate [ strtolower ( date ( "F" , $x ) ) ];
    $dayNumber = date ( "j" , $x ) ;
    $year = date ( "Y" , $x ) ;
    return "$dayName, $dayNumber $monthName $year" ;
    }
function wikiGetBriefDateEs () { #Brief date for link in sidebar
    global $wikiDate ;
    $monthName = $wikiDate [ strtolower ( date ( "F" ) ) ];
    $dayNumber = date ( "j" ) ;
    $year = date ( "Y" ) ;
    return "$dayNumber $monthName $year" ;
    }
$wikiGetDate = 'wikiGetDateEs';
$wikiGetBriefDate = 'wikiGetBriefDateEs' ;

# In theory, this could be expanded to allow general conversion of the
# character encoding used in the database to another encoding optionally
# used on the browser end.

# Define these arrays if you need to set up conversion.
 #$wikiEncodingCharsets = array("iso-8859-1");
 #$wikiEncodingNames = array("Latin-1"); # Localised names
 
function wikiRecodeOutputEs($text) {
  # Stub
  # global $user;  # $user->options["encoding"] is an index into the above arrays
  return $text;
}

function wikiRecodeInputEs($text) {
  # Stub
  return $text;
} 

$wikiRecodeOutput = 'wikiRecodeOutputEs';
$wikiRecodeInput = 'wikiRecodeInputEs';
?>
