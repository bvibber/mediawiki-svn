<?php

/*Internationalizaton file of TodoTask extension*/

$messages = array();
$messages['en'] = array(
	'tasklist'                => 'Task List',
	'tasklistbyproject'       => 'Task List By Project',
	'tasklistunknownproject'  => 'Unknown project',
	'tasklistunspecuser'      => 'Unspecified user',
	'tasklistincorrectuser'   => 'Incorrect username',
	'tasklistemail'           => 'Dear %s',
	'tasklistemailsubject'    => '[%s] Task List Change',
	'tasklistmytasks'         => 'My tasks',
	'tasklistbyprojectbad'    => "Project '''%s''' is not a valid project. For a list of valid projects, see [[MediaWiki:TodoTasksValidProjects]].",
	'tasklistbyprojname'      => "Assigned Tasks for '''%s'''",
	'tasklistchooseproj'      => 'Select Project: ',
	'tasklistprojdisp'        => 'Display',
	'tasklistbyname'          => '== Todo List for %s ==',
	'tasklistnoprojects'      => "ERROR: It looks like you enabled '''\$wgUseProjects''', but did not create [[MediaWiki:TodoTasksValidProjects]]. See [http://www.mediawiki.org/wiki/Extension:Todo_Tasks#Step_8 Installation Instructions] for more details.",
	'tasklistemailbody'       => ",

Someone has assigned a new Task for you on %s.

To see your complete Task List go to %s.

Your friendly %s notification system",
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'tasklist'               => 'قائمة المهام',
	'tasklistbyproject'      => 'قائمة المهام حسب المشروع',
	'tasklistunknownproject' => 'مشروع غير معروف',
	'tasklistunspecuser'     => 'مستخدم غير محدد',
	'tasklistincorrectuser'  => 'اسم مستخدم غير صحيح',
	'tasklistemail'          => 'عزيزي %s',
	'tasklistemailsubject'   => 'التغيير في قائمة مهام [%s]',
	'tasklistmytasks'        => 'مهامي',
	'tasklistbyprojectbad'   => "المشروع '''%s''' ليس مشروعا صحيحا. لقائمة بالمشاريع الصحيحة، انظر [[MediaWiki:TodoTasksValidProjects]].",
	'tasklistbyprojname'     => "المهام الموكلة ل'''%s'''",
	'tasklistchooseproj'     => 'اختر المشروع:',
	'tasklistprojdisp'       => 'عرض',
	'tasklistbyname'         => '== قائمة العمل ل%s ==',
	'tasklistnoprojects'     => "خطأ: يبدو أنك فعلت '''\$wgUseProjects'''، لكن لم تنشيء [[MediaWiki:TodoTasksValidProjects]]. انظر [http://www.mediawiki.org/wiki/Extension:Todo_Tasks#Step_8 تعليمات التنصيب] لمزيد من التفاصيل.",
	'tasklistemailbody'      => '،

شخص ما أضاف مهمة جديدة لك في %s.

لرؤية قائمة مهامك الكاملة اذهب إلى %s.

نظام إخطار %s الصديق',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'tasklist'             => 'Списък със задачи',
	'tasklistbyproject'    => 'Списък със задачи по проект',
	'tasklistmytasks'      => 'Моите задачи',
	'tasklistbyprojectbad' => "Проектът '''%s''' не е валиден проект. За списък с проекти, вижте [[MediaWiki:TodoTasksValidProjects]].",
	'tasklistchooseproj'   => 'Избор на проект:',
	'tasklistprojdisp'     => 'Показване',
	'tasklistbyname'       => '== Списък със задачи за %s ==',
);

$messages['de'] = array(
	'tasklist'                => 'Aufgabenliste',
	'tasklistbyproject'       => 'Aufgabenliste pro Projekt',
	'tasklistunknownproject'  => 'Unbekanntes Projekt',
	'tasklistunspecuser'      => 'Unbestimmter Benutzername',
	'tasklistincorrectuser'   => 'Falscher Benutzername',
	'tasklistemail'           => 'Hallo %s',
	'tasklistemailsubject'    => '[%s]-Aufgabenliste Änderungen',
	'tasklistmytasks'         => 'Meine Aufgaben',
	'tasklistbyprojectbad'    => "Projekt '''%s''' ist nicht vorhanden. Für eine Liste gültiger Projekt siehe [[MediaWiki:TodoTasksValidProjects]].",
	'tasklistbyprojname'      => "Zugewiesene Aufgaben für '''%s'''",
	'tasklistchooseproj'      => 'Projekt: auswählen:',
	'tasklistprojdisp'        => 'Anzeigen',
	'tasklistbyname'          => '== Aufgabenliste für %s ==',
	'tasklistnoprojects'      => "Fehler: Es sieht so aus, als wenn '''\$wgUseProjects''' aktiviert wäre, aber es wurde keine Seiten [[MediaWiki:TodoTasksValidProjects]] erstellt. Siehe die [http://www.mediawiki.org/wiki/Extension:Todo_Tasks#Step_8 Installationsanweisungen] für weitere Details.",
);

/** French (Français)
 * @author Sherbrooke
 * @author Grondin
 */
$messages['fr'] = array(
	'tasklist'               => 'Liste de tâches',
	'tasklistbyproject'      => 'Liste de tâches par projet',
	'tasklistunknownproject' => 'Projet inconnu',
	'tasklistunspecuser'     => 'Contributeur inconnu',
	'tasklistincorrectuser'  => 'Pseudonyme incorrect',
	'tasklistemail'          => 'Cher %s',
	'tasklistemailsubject'   => '[%s] Changement à la liste de tâches',
	'tasklistmytasks'        => 'Mes tâches',
	'tasklistbyprojectbad'   => "Le projet '''%s''' n'est pas valide. Consulter la [[MediaWiki:TodoTasksValidProjects|liste des projets]].",
	'tasklistbyprojname'     => "Tâches assignées pour '''%s'''.",
	'tasklistchooseproj'     => 'Projet sélectionné :',
	'tasklistprojdisp'       => 'Afficher',
	'tasklistbyname'         => '== Liste de tâches à faire pour %s ==',
	'tasklistnoprojects'     => "Erreur : il semble que vous ayez activé '''\$wgUseProjects''', mais sans avoir créé [[MediaWiki:TodoTasksValidProjects]]. Prière de lire les [http://www.mediawiki.org/wiki/Extension:Todo_Tasks#Step_8 instructions d'installation] pour plus de détails.",
	'tasklistemailbody'      => ",

Quelqu'un vous a assigné une nouvelle tâche pour vous sur %s.

Pour voir votre liste complète des tâches à effectuer, allez sur %s.

Votre bien aimable système de notification de %s",
);

$messages['gl'] = array(
	'tasklist'                => 'Listaxe de Tarefas',
	'tasklistbyproject'       => 'Listaxe de Tarefas por Proxecto',
	'tasklistunknownproject'  => 'Proxecto descoñecido',
	'tasklistunspecuser'      => 'Usuario sen especificar',
	'tasklistincorrectuser'   => 'Nome de usuario incorrecto',
	'tasklistemail'           => 'Querido %s',
	'tasklistemailsubject'    => '[%s] Cambio na Listaxe de Tarefas',
	'tasklistmytasks'         => 'As miñas tarefas',
	'tasklistbyprojectbad'    => 'O Proxecto \'\'\'%s\'\'\' non é un proxecto válido. Para unha listaxe de proxectos válidos, vexa
[[MediaWiki:TodoTasksValidProjects]].',
	'tasklistbyprojname'      => 'Tarefas asignadas a \'\'\'%s\'\'\'',
	'tasklistchooseproj'      => 'Seleccionar Proxecto:',
	'tasklistprojdisp'        => 'Pantalla',
	'tasklistbyname'          => '== Lista de cousas por facer de %s ==',
);

$messages['he'] = array(
	'tasklist'                => 'רשימת מטלות',
	'tasklistbyproject'       => 'רשימת מטלות לפי פרוייקטים',
	'tasklistunknownproject'  => 'פרוייקט לא ידוע',
	'tasklistunspecuser'      => 'משתמש לא מוגדר',
	'tasklistincorrectuser'   => 'משתמש לא נכון',
	'tasklistemail'           => '%s היקר והחביב',
	'tasklistemailsubject'    => '[%s] שיוני ברשימת המטלות',
	'tasklistmytasks'         => 'המטלות שלי',
	'tasklistbyprojectbad'    => "פרוייקט '''%s''' איננו קיים. לקבלת רשימת הפרוייקטים, צפה כאן [[MediaWiki:TodoTasksValidProjects]].",
	'tasklistbyprojname'      => "מטלה הוגדרה ל'''%s'''",
	'tasklistchooseproj'      => 'בחר פרוייקט: ',
	'tasklistprojdisp'        => 'תצוגה',
	'tasklistbyname'          => '== רשימת מטלות עבור %s ==',
	'tasklistnoprojects'      => "שגיאה: נראה שאפשרת את '''\$wgUseProjects''', אבל לא יצרת [[MediaWiki:TodoTasksValidProjects]]. ראה [http://www.mediawiki.org/wiki/Extension:Todo_Tasks#Step_8 Installation Instructions] for more details.",
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'tasklist'               => 'Lisćina nadawkow',
	'tasklistbyproject'      => 'Lisćina nadawkow po projekće',
	'tasklistunknownproject' => 'Njeznaty projekt',
	'tasklistunspecuser'     => 'Wužiwar njepodaty',
	'tasklistincorrectuser'  => 'Njekorektne wužiwarske mjeno',
	'tasklistemail'          => 'Luby %s',
	'tasklistemailsubject'   => '[%s] Změna lisćiny nadawkow',
	'tasklistmytasks'        => 'Moje nadawki',
	'tasklistbyprojectbad'   => "Projekt '''%s''' płaćiwy projekt njeje. Za lisćinu płaćiwych projektow, hlej [[MediaWiki:TodoTasksValidProjects]].",
	'tasklistbyprojname'     => "Nadawki so za '''%s''' připokazachu.",
	'tasklistchooseproj'     => 'Wubjer projekt:',
	'tasklistprojdisp'       => 'Pokazać',
	'tasklistbyname'         => '== Nadawkowa lisćina za %s ==',
	'tasklistnoprojects'     => "ZMYLK: Zda so, zo sy '''\$wgUseProjects''' aktiwizował, ale njejsy [[MediaWiki:TodoTasksValidProjects]] wutworił. Hlej [http://www.mediawiki.org/wiki/Extension:Todo_Tasks#Step_8 Installation Instructions] za dalše podrobnosće.",
	'tasklistemailbody'      => ',

Něchtó je nowy nadawk za tebje na %s připokazal.

Zo by swoju dospołnu lisćinu nadawkow widźał, dźi k %s.

Twój přećelny zdźělenski system %s.',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 */
$messages['km'] = array(
	'tasklistincorrectuser' => 'ឈ្មោះអ្នកប្រើប្រាស់ មិនត្រឹមត្រូវ',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'tasklist'               => 'Lëscht vun den Aufgaben',
	'tasklistbyproject'      => 'Lëscht vun den Aufgabe pro Projet',
	'tasklistunknownproject' => 'Onbekannte Projet',
	'tasklistunspecuser'     => 'Onbestemmte Benotzer',
	'tasklistincorrectuser'  => 'Falsche Benotzernumm',
	'tasklistemail'          => 'Léiwe %s',
	'tasklistemailsubject'   => '[%s] Ännerunge vun der Lëscht vun den Aufgaben',
	'tasklistmytasks'        => 'Meng Aufgaben',
	'tasklistbyprojname'     => "Aufgaben déi dem '''%s''' zougedeelt sinn.",
	'tasklistchooseproj'     => 'Projet auswielen:',
	'tasklistprojdisp'       => 'Weisen',
	'tasklistbyname'         => '== Lëscht vun den Aufgabe fir %s ==',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'tasklist'               => 'Takenlijst',
	'tasklistbyproject'      => 'Takenlijst per project',
	'tasklistunknownproject' => 'Onbekend project',
	'tasklistunspecuser'     => 'Gebruiker niet aangegeven',
	'tasklistincorrectuser'  => 'Gebruiker bestaat niet',
	'tasklistemail'          => 'Beste %s',
	'tasklistemailsubject'   => '[%s] verandering in takenlijst',
	'tasklistmytasks'        => 'Mijn taken',
	'tasklistbyprojectbad'   => "Project '''%s''' is geen geldige projectnaam. Een lijst met projecten is te vinden op [[MediaWiki:TodoTasksValidProjects]].",
	'tasklistbyprojname'     => "Toegewezen taken voor '''%s'''",
	'tasklistchooseproj'     => 'Project selecteren:',
	'tasklistprojdisp'       => 'Tonen',
	'tasklistbyname'         => '== Takenlijst voor %s ==',
	'tasklistnoprojects'     => "FOUT: het lijkt alsof u '''\$wgUseProjects''' hebt ingeschakeld, maar [[MediaWiki:TodoTasksValidProjects]] niet heeft aangemaakt. Zie de  [http://www.mediawiki.org/wiki/Extension:Todo_Tasks#Step_8 installatie-instructies] voor meer details.",
	'tasklistemailbody'      => ',

Iemand heeft een nieuwe taak aan u toegewezen op %s.

Op %s kan u uw complete takenlijst bekijken.

Het waarschuwingssysteem',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'tasklist'               => 'Oppgaveliste',
	'tasklistbyproject'      => 'Oppgaveliste etter prosjekt',
	'tasklistunknownproject' => 'Ukjent prosjekt',
	'tasklistunspecuser'     => 'Bruker ikke angitt',
	'tasklistincorrectuser'  => 'Ukorrekt brukernavn',
	'tasklistemail'          => 'Kjære %s',
	'tasklistemailsubject'   => '[%s] Oppgavelisteendring',
	'tasklistmytasks'        => 'Mine oppgaver',
	'tasklistbyprojectbad'   => "'''%s''' er ikke et gyldig prosjekt. For en liste over gyldige prosjekter, se [[MediaWiki:TodoTasksValidProjects]].",
	'tasklistbyprojname'     => "Tildelte oppgaver for '''%s'''",
	'tasklistchooseproj'     => 'Velg prosjekt:',
	'tasklistprojdisp'       => 'Vis',
	'tasklistbyname'         => '== Oppgaveliste for %s ==',
	'tasklistnoprojects'     => "FEIL: Det ser ut som om du har slått på '''\$wgUseProjects''' uten å opprette [[MediaWiki:TodoTasksValidProjects]]. Se [http://www.mediawiki.org/wiki/Extension:Todo_Tasks#Step_8 installasjonsintruksjonene] for flere detaljer.",
	'tasklistemailbody'      => ',

Noen har gitt deg en ny oppgave på %s.

For å se den fullstendige oppgavelisten din, gå til %s.

Fra %ss varslingssystem',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'tasklist'               => 'Lista de prètzfaches',
	'tasklistbyproject'      => 'Lista de prètzfaches per projècte',
	'tasklistunknownproject' => 'Projècte desconegut',
	'tasklistunspecuser'     => 'Contributor desconegut',
	'tasklistincorrectuser'  => 'Pseudonim incorrècte',
	'tasklistemail'          => 'Car(-a) %s',
	'tasklistemailsubject'   => '[%s] Cambiament a la lista de prètzfaches',
	'tasklistmytasks'        => 'Mos prètzfaches',
	'tasklistbyprojectbad'   => "Lo projècte '''%s''' es pas valid. Consultatz la [[MediaWiki:TodoTasksValidProjects|lista dels projèctes]].",
	'tasklistbyprojname'     => "Prètzfaches assignats per '''%s'''.",
	'tasklistchooseproj'     => 'Projècte seleccionat :',
	'tasklistprojdisp'       => 'Afichar',
	'tasklistbyname'         => '== Lista de prètzfaches de far per %s ==',
	'tasklistnoprojects'     => "Error : sembla qu'avètz activat '''\$wgUseProjects''', mas sens aver creat [[MediaWiki:TodoTasksValidProjects]]. Legissètz las [http://www.mediawiki.org/wiki/Extension:Todo_Tasks#Step_8 instruccions d'installacion] per mai de detalhs.",
	'tasklistemailbody'      => ",

Qualqu'un vos a assignat un prètzfach novèl sus %s.

Per veire vòstra lista completa dels prètzfaches d'efectuar, anatz sus %s.

Vòstre plan amable sistèma de notificacion de %s",
);

$messages['pl'] = array(
	'tasklist'                => 'Lista zadań',
	'tasklistbyproject'       => 'Listy zadań według projektu',
	'tasklistunknownproject'  => 'Nieznany projekt',
	'tasklistunspecuser'      => 'Nie określono użytkownika',
	'tasklistincorrectuser'   => 'Niepoprawna nazwa użytkownika',
	'tasklistemail'           => '%s',
	'tasklistemailsubject'    => '[%s] Zmiana listy zadań',
	'tasklistmytasks'         => 'Moje zadania',
	'tasklistbyprojectbad'    => 'Projekt \'\'\'%s\'\'\' nie jest poprawnym projektem. By zobaczyć listę poprawnych projektów, odwiedź stronę [[MediaWiki:TodoTasksValidProjects]].',
	'tasklistbyprojname'      => 'Przypisano zadania do \'\'\'%s\'\'\'',
	'tasklistchooseproj'      => 'Wybierz projekt:',
	'tasklistprojdisp'        => 'Pokaż',
	'tasklistbyname'          => '== Lista do zrobienia dla %s ==',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'tasklistemail'      => 'Caro %s',
	'tasklistbyprojname' => "Tarefas atribuídas a '''%s'''",
	'tasklistchooseproj' => 'Seleccione Projecto:',
	'tasklistprojdisp'   => 'Mostrar',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'tasklist'               => 'Zoznam úloh',
	'tasklistbyproject'      => 'Zoznam úloh podľa projektov',
	'tasklistunknownproject' => 'Neznámy projekt',
	'tasklistunspecuser'     => 'Nešpecifikovaný používateľ',
	'tasklistincorrectuser'  => 'Nesprávne používateľské meno',
	'tasklistemail'          => 'Milý %s',
	'tasklistemailsubject'   => '[%s] Zmena zoznamu úloh',
	'tasklistmytasks'        => 'Moje úlohy',
	'tasklistbyprojectbad'   => "Projekt '''%s''' nie je platný projekt. Zoznam platných projektov nájdete na [[MediaWiki:TodoTasksValidProjects]].",
	'tasklistbyprojname'     => "Pridelené úlohy pre '''%s'''",
	'tasklistchooseproj'     => 'Vyberte projekt:',
	'tasklistprojdisp'       => 'Zobraziť',
	'tasklistbyname'         => '== Zoznam úloh pre %s ==',
	'tasklistnoprojects'     => "CHYBA: Zdá sa, že ste zapli  '''\$wgUseProjects''', ale nevytvorili ste [[MediaWiki:TodoTasksValidProjects]]. Pozri podrobnosti v [http://www.mediawiki.org/wiki/Extension:Todo_Tasks#Step_8 Inštalačných inštrukciách].",
	'tasklistemailbody'      => ',

Niekto vám %s priradil novú úlohu.

Svoj kompletný Zoznam úloh si môžete pozrieť na %s.

Váš priateľský upozorňovací systém %s',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'tasklist'               => 'Apgoawenlieste',
	'tasklistbyproject'      => 'Apgoawenlieste pro  Projekt',
	'tasklistunknownproject' => 'Uunbekoand Projekt',
	'tasklistunspecuser'     => 'Uunbestimde Benutsernoome',
	'tasklistincorrectuser'  => 'Falsken Benutsernoome',
	'tasklistemail'          => 'Moin %s',
	'tasklistemailsubject'   => '[%s]-Apgoawenlieste Annerengen',
	'tasklistmytasks'        => 'Mien Apgoawen',
	'tasklistbyprojectbad'   => "Projekt '''%s''' is nit deer. Foar ne Lieste fon gultige Projekte sjuch [[MediaWiki:TodoTasksValidProjects]].",
	'tasklistbyprojname'     => "Touwiesde Apgoawen foar '''%s'''",
	'tasklistchooseproj'     => 'Projekt uutwääle:',
	'tasklistprojdisp'       => 'Anwiese',
	'tasklistbyname'         => '== Apgoawenlieste foar %s ==',
	'tasklistnoprojects'     => "Failer: Dät sjucht so uut, as wan '''\$wgUseProjects''' aktivierd waas, man der wuuden neen Sieden [[MediaWiki:TodoTasksValidProjects]] moaked. Sjuch do
[http://www.mediawiki.org/wiki/Extension:Todo_Tasks#Step_8 Installationsanwiesengen] foar wiedere Details.",
);

/** Swedish (Svenska)
 * @author Lejonel
 * @author Max sonnelid
 */
$messages['sv'] = array(
	'tasklistunknownproject' => 'Okänt projekt',
	'tasklistincorrectuser'  => 'Felaktigt användarnamn',
	'tasklistchooseproj'     => 'Välj projekt:',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'tasklistincorrectuser' => 'తప్పుడు వాడుకరిపేరు',
	'tasklistemail'         => 'ప్రియమైన %s',
	'tasklistmytasks'       => 'నా పనులు',
);

/** Turkish (Türkçe)
 * @author Karduelis
 */
$messages['tr'] = array(
	'tasklist'         => 'Görev listesi',
	'tasklistmytasks'  => 'Görevlerim',
	'tasklistprojdisp' => 'Gösteri',
);

/** Volapük (Volapük)
 * @author Malafaya
 */
$messages['vo'] = array(
	'tasklistemail' => 'O %s löfik',
);

$messages['zh-cn'] = array(         //Simple Chinese
	'tasklist'                => '任务列表',  //Task List
	'tasklistbyproject'       => '依专案列出任务', //Task List By Project
	'tasklistunknownproject'  => '未知的专案', //Unknown project
	'tasklistunspecuser'      => '未指定用户', //Unspecified user
	'tasklistincorrectuser'   => '用户名称错误',  //Incorrect username
	'tasklistemail'           => '%s您好',  //Dear %s
	'tasklistemailsubject'    => '[%s] 任务列表变更', //Task List Change
	'tasklistmytasks'         => '我的任务',  //My tasks
	'tasklistbyprojectbad'    => "专案「'''%s'''」并非是个有效的专案项目.请参考[[MediaWiki:TodoTasksValidProjects]]页面以察看专案列表",
	'tasklistbyprojname'      => "'''%s'''项下的任务", //Assigned Tasks for '''%s'''
	'tasklistchooseproj'      => '选取专案：', //Select Project:
	'tasklistprojdisp'        => '显示',  //Display
	'tasklistbyname'          => '==  名称为「%s」的任务 ==', //== Todo List for %s ==
);

/** ‪中文(台灣)‬ (‪中文(台灣)‬)
 * @author Roc michael
 */
$messages['zh-tw'] = array(
	'tasklist'               => '任務清單',
	'tasklistbyproject'      => '依專案列出任務',
	'tasklistunknownproject' => '未知的專案',
	'tasklistunspecuser'     => '未指定用戶',
	'tasklistincorrectuser'  => '用戶名稱錯誤',
	'tasklistemail'          => '%s您好',
	'tasklistemailsubject'   => '[%s] 任務清單變更',
	'tasklistmytasks'        => '我的任務',
	'tasklistbyprojectbad'   => "專案「'''%s'''」並非是個有效的專案項目.請參考[[MediaWiki:TodoTasksValidProjects]]頁面以察看專案清單",
	'tasklistbyprojname'     => "'''%s'''項下的任務",
	'tasklistchooseproj'     => '選取專案：',
	'tasklistprojdisp'       => '顯示',
	'tasklistbyname'         => '==  名稱為「%s」的任務 ==',
	'tasklistnoprojects'     => "錯誤：您似乎設定了使'''\$wgUseProjects'''生效，但卻尚未建立[[MediaWiki:TodoTasksValidProjects]]此一頁面，請參見 [http://www.mediawiki.org/wiki/Extension:Todo_Tasks#Step_8 Installation Instructions]此一頁面以獲得更詳細的說明。",
	'tasklistemailbody'      => ',

有人在%s指定了一項新任務給您。

您可前往%s查看所有任務的清單。

您最好的幫手 %s 任務通報系統',
);

