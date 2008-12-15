<?php
/**
 * Internationalisation file for extension CommentSpammer
 *
 * @addtogroup Extensions
 */

$messages = array();

/* English
 * @author Nick Jenkins
 */
$messages['en'] = array(
	'commentspammer-save-blocked' => 'Your IP address is a suspected comment spammer, so the page has not been saved. [[Special:Userlogin|Log in or create an account]] to avoid this.',
	'commentspammer-desc'         => 'Rejects edits from suspected comment spammers on a DNS blacklist',
	'commentspammer-log-msg'      => 'edit from [[Special:Contributions/$1|$1]] to [[:$2]]. ',
	'commentspammer-log-msg-info' => 'Last spammed $1 {{PLURAL:$1|day|days}} ago, threat level is $2, and offence code is $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 View details], or [[Special:Blockip/$4|block]].',
	'cspammerlogpagetext'         => 'Record of edits that have been allowed or denied based on whether the source was a known comment spammer.',
	'cspammer-log-page'           => 'Comment Spammer log',
);

/** Arabic (العربية)
 * @author Meno25
 * @author Siebrand
 */
$messages['ar'] = array(
	'commentspammer-save-blocked' => 'عنوان الأيبي الخاص بك هو معلق سبام مشتبه، لذا لم يتم حفظ الصفحة. [[Special:Userlogin|ادخل أو سجل حسابا]] لتجنب هذا.',
	'commentspammer-desc'         => 'يرفض التعديلات من معلقي السبام المشتبه فيهم على قائمة DNS سوداء',
	'commentspammer-log-msg'      => 'تعديل من [[Special:Contributions/$1|$1]] ل[[:$2]].',
	'commentspammer-log-msg-info' => 'آخر سبام منذ $1 {{PLURAL:$1|يوم|يوم}} ، مستوى التهديد هو $2، وكود الإساءة هو $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 عرض التفاصيل]، أو [[Special:Blockip/$4|منع]].',
	'cspammerlogpagetext'         => 'سجل التعديلات التي تم السماح بها أو رفضها بناء على ما إذا كان المصدر معلق سبام معروف.',
	'cspammer-log-page'           => 'سجل تعليق السبام',
);

/** Czech (Česky)
 * @author Matěj Grabovský
 */
$messages['cs'] = array(
	'commentspammer-save-blocked' => 'Existuje podezření, že vaše IP adresa je adresa podezřelého spammera obsahu, proto stránka nebyla uložena. Vyhněte se tomu tím, že [[Special:Userlogin|se přihlásíte]] nebo [[Special:Createaccount|si vytvoříte účet]].',
	'commentspammer-desc'         => 'Odmítá úpravy od podezřelých spamerů z černé listiny DNS',
	'commentspammer-log-msg'      => 'úprava [[:$2]] od [[Special:Contributions/$1|$1]].',
	'cspammerlogpagetext'         => 'Záznam úprav, které byly povoleny nebo zamítnuty na základě toho, že zdroj byl známý spammer obsahu.',
	'cspammer-log-page'           => 'Záznam spamerů obsahu',
);

/* German */
$messages['de'] = array(
	'commentspammer-save-blocked' => 'Deine IP-Adresse stammt mutmaßlich von einem Kommentar-Spammer. Die Seite wurde nicht gespeichert. [[Special:Userlogin|Melde dich an oder erstelle ein Benutzerkonto]], um diese Warnung zu unterbinden.',
	'commentspammer-log-msg'      => 'Bearbeitung von [[Special:Contributions/$1|$1]] für [[:$2]]. ',
	'commentspammer-log-msg-info' => 'Letztes Spamming vor $1 {{PLURAL:$1|Tag|Tagen}}, der "threat level" ist $2 und der and "offence code" is $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 Details ansehen] oder [[Special:Blockip/$4|sperren]].',
	'cspammerlogpagetext'         => 'Liste der Bearbeitungen, die genehmigt oder abgelehnt wurden auf der Basis, ob die Quelle ein bekannter Kommentar-Spammer war.',
	'cspammer-log-page'           => 'Kommentar-Spammer Logbuch',
);

/** French (Français)
 * @author Sherbrooke
 * @author Grondin
 * @author Siebrand
 * @author Urhixidur
 */
$messages['fr'] = array(
	'commentspammer-save-blocked' => "Votre adresse IP est celle d'une personne suspectée de créer du pourriel : la page n'a donc pas été sauvegardée. Veuillez vous [[Special:Userlogin|connecter ou créer un compte]] pour contourner cette interdiction.",
	'commentspammer-desc'         => 'Rejette les modifications soupçonnées de pourriel à partir d’une liste noire figurant dans le projet HoneyPot DNS',
	'commentspammer-log-msg'      => 'Modifications de [[Special:Contributions/$1|$1]] à [[:$2]].',
	'commentspammer-log-msg-info' => "Le dernier pourriel remonte à {{PLURAL:$1|$1 jour|$1 jours}}, le niveau d'alerte est à $2 et le code d'attaque est $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 Voir détails] ou [[Special:Blockip/$4|bloquer]].",
	'cspammerlogpagetext'         => 'Journal des modifications acceptées ou rejetées selon que la source était un créateur de pourriels connu.',
	'cspammer-log-page'           => 'Journal du créateur de pourriels',
);

$messages['frp'] = array(
	'commentspammer-save-blocked' => 'Voutra adrèce IP est cela d’una pèrsona soupçonâ de crèar de spame, la pâge at pas étâ sôvâ. Volyéd vos [[Special:Userlogin|conèctar ou crèar un compto]] por contornar ceta dèfensa.',
	'commentspammer-log-msg' => 'Modificacions de [[Special:Contributions/$1|$1]] a [[:$2]].',
	'commentspammer-log-msg-info' => 'Lo dèrriér spame remonte a {{PLURAL:$1|$1 jorn|$1 jorns}}, lo nivô d’alèrta est a $2 et lo code d’ataca est $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 Vêre los dètalys] ou ben [[Special:Blockip/$4|blocar]].',
	'cspammerlogpagetext' => 'Jornal de les modificacions accèptâs ou refusâs d’aprés que la sôrsa ére un crèator de spame cognu.',
	'cspammer-log-page' => 'Jornal du crèator de spame',
);

/** Galician (Galego)
 * @author Alma
 * @author Siebrand
 * @author Xosé
 */
$messages['gl'] = array(
	'commentspammer-save-blocked' => 'O seu enderezo IP é sospeitoso de facer comentarios spam, de maneira que non se gardou a páxina. [[Special:Userlogin|Rexístrese ou cree unha conta]] para evitalo.',
	'commentspammer-log-msg'      => 'editar de [[Special:Contributions/$1|$1]] a [[:$2]].',
	'commentspammer-log-msg-info' => 'Último correo spam $1 {{PLURAL:$1|día|días}} atrás, nivel de ameaza é de $2, e código de delito é de $3. 
[http://www.projecthoneypot.org/search_ip.php?ip=$4 ver detalles], ou [[Special:Blockip/$4|bloqueo]].',
	'cspammerlogpagetext'         => 'Historial das edicións que se permitiron ou denegaron sobre a base de si a fonte foi un coñecido comentario spam.',
	'cspammer-log-page'           => 'Rexistro dos comentarios Spam',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 * @author Siebrand
 */
$messages['hsb'] = array(
	'commentspammer-save-blocked' => 'Twoja IP-adresa je podhladny komentarne spamowar - składowanje zablokowane. Wutwor konto, zo by to wobešoł.',
	'commentspammer-desc'         => 'Wotpokazuje změny wot podhladnych spamowarjow komentarow na čornej lisćinje DNS.',
	'commentspammer-log-msg'      => 'změna wot [[Special:Contributions/$1|$1]] k [[:$2]]',
	'commentspammer-log-msg-info' => 'Posledni spam před $1 {{PLURAL:$1|dnjom|dnjomaj|dnjemi|dnjemi}}, stopjeń hroženja je $2 a nadpadowy kod je $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 Hlej podrobnosće] abo [[Special:Blockip/$4|blokowanje]].',
	'cspammerlogpagetext'         => 'Datowa sadźba změnow, kotrež buchu dowolene abo wotpokazane, po tym hač žórło je znaty spamowar abo nic.',
	'cspammer-log-page'           => 'Protokol komentarnych spamowarjow',
);

/** Hungarian (Magyar)
 * @author Bdanee
 */
$messages['hu'] = array(
	'commentspammer-save-blocked' => 'Az IP-címed feltételezett tartalomspammer, ezért az oldal nem lett elmentve. [[Special:Userlogin|Jelentkezz be]] ennek kiküszöböléséhez.',
	'commentspammer-log-msg'      => '[[Special:Contributions/$1|$1]] szerkesztése a(z) [[:$2]] lapon.',
	'commentspammer-log-msg-info' => 'Utoljára $1 napja spammelt, veszélyességi szintje $2, támadókódja $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 Részletek megtekintése], vagy [[Special:Blockip/$4|blokkolás]].',
	'cspammerlogpagetext'         => 'Azon szerkesztések listája, melyek engedélyezve vagy tiltva lettek attól függően, hogy a szerző ismert tartalomspammer volt-e.',
	'cspammer-log-page'           => 'Tartalomspammer napló',
);

/** Japanese (日本語)
 * @author JtFuruhata
 */
$messages['ja'] = array(
	'commentspammer-save-blocked' => 'あなたのIPアドレスはスパム投稿に用いられているとの疑いがあるため、ページは保存されませんでした。[[{{ns:Special}}:Userlogin|ログインまたはアカウントの作成]]を行ってください。',
	'commentspammer-desc'         => 'DNSブラックリストに記載されたコメントスパム投稿容疑IPアドレスからの編集を拒絶する',
	'commentspammer-log-msg'      => '利用者 [[{{ns:Special}}:Contributions/$1|$1]] による [[:$2]] の編集',
	'commentspammer-log-msg-info' => '最後のスパム行為は $1{{PLURAL:$1|日|日}}前 / 脅威レベル $2 / 防御コード $3 / [http://www.projecthoneypot.org/search_ip.php?ip=$4 詳細表示] / [[{{ns:Special}}:Blockip/$4|ブロック状況]]',
	'cspammerlogpagetext'         => 'この編集履歴は、判明しているコメントスパマーによる投稿の許可/拒否状況を示します。',
	'cspammer-log-page'           => 'スパム投稿ログ',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'commentspammer-log-msg' => 'Ännerunge vun [[Special:Contributions/$1|$1]] fir [[:$2]].',
	'cspammer-log-page'      => 'Bemierkung Spammer Logbuch',
);

/** Dutch (Nederlands)
 * @author Siebrand
 * @author SPQRobin
 */
$messages['nl'] = array(
	'commentspammer-save-blocked' => 'Uw IP-adres wordt verdacht van spammen - opslaan is geweigerd. Maak een gebruiker aan om dit te voorkomen.',
	'commentspammer-desc'         => 'Voorkomt bewerkingen van spammers via een DNS blacklist',
	'commentspammer-log-msg'      => 'bewerking van [[Special:Contributions/$1|$1]] aan [[:$2]].',
	'commentspammer-log-msg-info' => 'Spamde voor het laatst $1 {{PLURAL:$1|dag|dagen}} geleden. Dreigingsniveau is $2 en de overtredingscode is $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 Details bekijken] of [[Special:Blockip/$4|blokkeren]].',
	'cspammerlogpagetext'         => 'Logboek met bewerkingen die toegestaan of geweigerd zijn omdat de bron een bekende spammer was.',
	'cspammer-log-page'           => 'Spamlogboek',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'commentspammer-save-blocked' => 'IP-adressa di mistenkes for å være en kommentarforsøpler, så siden kan ikke lagres. [[Special:Userlogin|Logg inn eller opprett en konto]] for å unngå dette.',
	'commentspammer-desc'         => 'Avviser endringer fra mistenkte spammere på en DNS-svarteliste.',
	'commentspammer-log-msg'      => 'redigering på [[:$2]] av [[Special:Contributions/$1|$1]].',
	'commentspammer-log-msg-info' => 'Sist forsøplet for $1 {{PLURAL:$1|dag|dager}} siden, trusselnivået er $2, og fornærmelseskoden er $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 Se detaljer] eller [[Special:Blockip/$4|blokkert]].',
	'cspammerlogpagetext'         => 'Register over redigeringer som har blitt godtatt eller nektet basert på hvorvidt kilden var en kjent kommentarforsøpler.',
	'cspammer-log-page'           => 'Kommentarforsøplerlogg',
);

/** Occitan (Occitan)
 * @author Cedric31
 * @author Siebrand
 */
$messages['oc'] = array(
	'commentspammer-save-blocked' => "Vòstra adreça IP es la d'una persona sospechada de crear de spams, la pagina es pas estada salvagardada. [[Special:Userlogin|Conectatz-vos o creatz un compte]] per contornar aqueste interdich.",
	'commentspammer-desc'         => 'Regèta las modificacions suspectadas de spams a partir d’una lista negra figurant dins lo projècte HoneyPot DNS',
	'commentspammer-log-msg'      => 'Modificacions de [[Special:Contributions/$1|$1]] a [[:$2]].',
	'commentspammer-log-msg-info' => "Lo darrièr spam remonta a {{PLURAL:$1|$1 jorn|$1 jorns}}, lo nivèl d'alèrta es a $2 e lo còde d'atac es $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 Vejatz los detalhs] o [[Special:Blockip/$4|blocatz]].",
	'cspammerlogpagetext'         => 'Jornal de las modificacions acceptadas o rejetadas segon que la font èra un creator de spams conegut.',
	'cspammer-log-page'           => 'Jornal del creator de spams',
);

$messages['pl'] = array(
	'commentspammer-save-blocked' => 'Twój adres IP jest podejrzewany o bycie spammerem - zapisywanie stron jest zablokowane. Utwórz konto, aby uniknąć tego komunikatu.',
	'commentspammer-log-msg' => 'edycja [[Special:Contributions/$1|$1]] w [[:$2]].',
	'commentspammer-log-msg-info' => 'Ostatni spam $1 {{PLURAL:$1|dzień|dni}} temu, poziom zagrożenia $2, kod naruszenia $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 Zobacz szczegóły] lub [[Special:Blockip/$4|zablokuj]].',
	'cspammerlogpagetext' => 'Zapis edycji, które zostały dozwolone lub zakazane na podstawie tego, czy dokonała ich osoba znana jako spammer.',
	'cspammer-log-page' => 'Rejestr spammerów',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'commentspammer-save-blocked' => 'O seu endereço IP é um suspeito "spammer" de comentários, consequentemente a página não foi guardada.
[[{{ns:special}}:Userlogin|Autentique-se ou crie uma conta]] para evitar isto.',
	'commentspammer-desc'         => 'Rejeita edições de suspeitos "spammers" de comentários numa lista negra de DNS',
	'commentspammer-log-msg'      => 'edição de [[{{ns:special}}:Contributions/$1|$1]] a [[:$2]].',
	'commentspammer-log-msg-info' => 'Último "spam" $1 {{PLURAL:$1|dia|dias}} atrás, nível de ameaça é $2, e código de ofensa é $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 Ver detalhes], ou [[{{ns:special}}:Blockip/$4|bloquear]].',
	'cspammerlogpagetext'         => 'Registo de edições que foram permitidas ou negadas baseado no facto de a fonte ser um "spammer" de comentários conhecido.',
	'cspammer-log-page'           => 'Registo de "Spammers" de Comentários',
);

/** Russian (Русский)
 * @author .:Ajvol:.
 */
$messages['ru'] = array(
	'commentspammer-save-blocked' => 'Подозревается, что ваш IP-адрес использовался для размещения спам-комментариев. Странице не может быть сохранена. [[Special:Userlogin|Представьтесь системе]], чтобы продолжить работу.',
	'commentspammer-desc'         => 'Отвергает правки подозреваемых в спаме комментариев на основе чёрного списка DNS',
	'commentspammer-log-msg'      => 'правка с [[Special:Contributions/$1|$1]] [[:$2]].',
	'commentspammer-log-msg-info' => 'Последний случай спама $1 {{PLURAL:$1|день|дня|дней}} назад, уровень угрозы — $2, код нарушения — $3.
[http://www.projecthoneypot.org/search_ip.php?ip=$4 Подробности], [[Special:Blockip/$4|заблокировать]].',
	'cspammerlogpagetext'         => 'Рекорд правок, которые были разрешены или отклонены на основе того, был ли источник известен как спаммер комментариев.',
	'cspammer-log-page'           => 'Журнал спам-комментариев',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'commentspammer-save-blocked' => 'Existuje podozrenie, že vaša IP adresa je adresa podozrivého spammera obsahu, preto stránka nebola uložená. Vyhnete sa tomu tým, že [[Special:Userlogin|sa prihlásite alebo si vytvoríte učet]].',
	'commentspammer-desc'         => 'Odmieta úpravy od podozrivých spamerov z DNS blacklistu',
	'commentspammer-log-msg'      => 'Úprava [[:$2]] od [[Special:Contributions/$1|$1]].',
	'commentspammer-log-msg-info' => 'Naposledy spamoval pred $1 {{PLURAL:$1|dňom|dňami}}, úroveň ohrozenia je $2 a kód prehrešku je $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 Zobraziť podrobnosti] alebo [[Special:Blockip/$4|zablokovať]].',
	'cspammerlogpagetext'         => 'Záznam úprav, ktoré boli povolené alebo zamietnuté na základe toho, že zdroj bol známy spammer obsahu.',
	'cspammer-log-page'           => 'Záznam spammerov obsahu',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'commentspammer-save-blocked' => 'Dien IP-Adresse stamt fermoudelk fon n Kommentoar-Spammer. Ju Siede wuude nit spiekerd.
[[Special:Userlogin|Mäldje die an of moak n Benutserkonto]], uum disse Woarschauenge tou ferhinnerjen.',
	'commentspammer-log-msg'      => 'Beoarbaidenge fon [[Special:Contributions/$1|$1]] foar [[:$2]].',
	'commentspammer-log-msg-info' => 'Lääste Spammenge foar $1 {{PLURAL:$1|Dai|Deege}}, die "threat level" is $2 un die "offence code" is $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 Details ankiekje] of [[Special:Blockip/$4|speere]].',
	'cspammerlogpagetext'         => 'Lieste fon Beoarbaidengen, do der ferlööwed of ouliend wuuden ap dän Gruund, of ju Wälle n bekoanden Kommentoar-Spammer waas.',
	'cspammer-log-page'           => 'Kommentoar-Spammer Logbouk',
);

/** Swedish (Svenska)
 * @author Lejonel
 */
$messages['sv'] = array(
	'commentspammer-save-blocked' => 'Din IP-adress misstänks vara en kommentarspammare. Därför har sidan inte sparats. [[Special:Userlogin|Logga in eller skapa ett användarkonto]] för att undvika detta.',
	'commentspammer-desc'         => 'Stoppar redigeringar som misstänks komma från kommentarspammare som finns på en svart lista',
	'commentspammer-log-msg'      => 'redigering av [[:$2]] från [[Special:Contributions/$1|$1]].',
	'commentspammer-log-msg-info' => 'Spammade senast för $1 {{PLURAL:$1|dag|dagar}} sedan, hotnivån är $2 och förbrytelsekoden är $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 Se mer information] eller [[Special:Blockip/$4|blockera]].',
	'cspammerlogpagetext'         => 'Det här är en logg över redigeringar som har tillåtits eller stoppats beroende på om källan är en känd kommentarspammare.',
	'cspammer-log-page'           => 'Kommentarspamslogg',
);

/** Telugu (తెలుగు)
 * @author వైజాసత్య
 */
$messages['te'] = array(
	'cspammer-log-page' => 'వ్యాఖ్యల స్పామింగు లాగ్',
);

