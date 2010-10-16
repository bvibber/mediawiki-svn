<?php
/**
 * Internationalisation file for ArticleComments extension.
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'article-comments-desc' => 'Enables comment sections on content pages',
	'article-comments-title-string' => 'title',
	'article-comments-name-string' => 'Name',
	'article-comments-name-field' => 'Name (required):',
	'article-comments-url-field' => 'Website:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Comment',
	'article-comments-comment-field' => 'Comment:',
	'article-comments-submit-button' => 'Submit',
	'article-comments-leave-comment-link' => 'Leave a comment ...',
	'article-comments-invalid-field' => 'The $1 provided <nowiki>[$2]</nowiki> is invalid.',
	'article-comments-required-field' => '$1 field is required.',
	'article-comments-submission-failed' => 'Comment submission failed',
	'article-comments-failure-reasons' => 'Sorry, your comment submission failed for the following reason(s):',
	'article-comments-no-comments' => 'Sorry, the page "[[$1]]" is not accepting comments at this time.',
	'article-comments-talk-page-starter' => "<noinclude>Comments on [[$1]]\n<comments />\n----- __NOEDITSECTION__</noinclude>\n",
	'article-comments-commenter-said' => '$1 said ...',
	'article-comments-summary' => 'Comment provided by $1 - via ArticleComments extension',
	'article-comments-submission-succeeded' => 'Comment submission succeeded',
	'article-comments-submission-success' => 'You have successfully submitted a comment for "[[$1]]"',
	'article-comments-submission-view-all' => 'You may view [[$1|all comments on that page]]',
	'article-comments-prefilled-comment-text' => '',
	'article-comments-user-is-blocked' => 'Your user account is currently blocked from editing "[[$1]]".',
	'article-comments-new-comment-heading' => "\n== {{int:article-comments-commenter-said|\$1}} ==\n\n",
	'article-comments-comment-bad-mode' => 'Invalid mode given for comment.
Available ones are "plain", "normal" and "wiki".',
	'article-comments-comment-contents' => "<div class='commentBlock'><small>$4</small>$5--\$3</div>\n",
	'article-comments-comment-missing-name-parameter' => 'Missing name',
	'article-comments-comment-missing-date-parameter' => 'Missing comment date',
	'article-comments-no-spam' => 'At least one of the submitted fields was flagged as spam.',
	'processcomment' => 'Process article comment',
);

/** Message documentation (Message documentation)
 * @author Hamilton Abreu
 * @author Siebrand
 */
$messages['qqq'] = array(
	'article-comments-invalid-field' => 'Shown as a list below {{msg-mw|article-comments-failure-reasons}}. With $1 being {{msg-mw|article-comments-title-string}} or {{msg-mw|article-comments-url-string}} messages, and $2 the wrong value.',
	'article-comments-required-field' => 'Shown as a list below article-comments-failure-reasons. With $1 being one of article-comments-*-string messages.',
	'article-comments-submission-failed' => 'Page title when there are errors in the comment submission',
	'article-comments-talk-page-starter' => 'Keep the wikisyntax as is.',
	'article-comments-comment-bad-mode' => '{{doc-important|Do not translate the words "plain", "normal" and "wiki".}}',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'article-comments-desc' => 'Maak kommentaar-afdelings op artikel-bladsye beskikbaar',
	'article-comments-title-string' => 'titel',
	'article-comments-name-string' => 'Naam',
	'article-comments-name-field' => 'Naam (verpligtend):',
	'article-comments-url-field' => 'Webwerf:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Kommentaar',
	'article-comments-comment-field' => 'Kommentaar:',
	'article-comments-submit-button' => 'Dien in',
	'article-comments-leave-comment-link' => "Los 'n opmerking...",
	'article-comments-invalid-field' => 'Die $1 verskafde <nowiki>[$2]</nowiki> is ongeldig.',
	'article-comments-required-field' => 'Die veld $1 is verpligtend.',
	'article-comments-submission-failed' => 'Indien van kommentaar het gefaal',
	'article-comments-failure-reasons' => 'Jammer, u kommentaar was om die volgende rede(s) onsuksesvol:',
	'article-comments-no-comments' => 'Jammer, die artikel "[[$1]]" aanvaar nie tans kommentaar nie.',
	'article-comments-talk-page-starter' => '<noinclude> Kommentaar op [[$1]] 
<comments />
 ----- __NOEDITSECTION__ </noinclude>',
	'article-comments-commenter-said' => '$1 het gesê...',
	'article-comments-summary' => 'Kommentaar deur $1 - via die ArticleComments-uitbreiding',
	'article-comments-submission-succeeded' => 'Indien van kommentaar was suksesvol',
	'article-comments-submission-success' => 'U het suksesvol \'n kommentaar vir "[[$1]]" ingedien',
	'article-comments-submission-view-all' => 'U kan al die antwoorde op hierdie artikel [[$1|hier]] sien',
	'article-comments-user-is-blocked' => 'U gebruiker is tans teen die redigering van "[[$1]]" geblokkeer.',
	'article-comments-comment-bad-mode' => 'Ongeldige modes is vir kommentaar verskaf.
Beskikbare modusse is: "plain", "normal" en "wiki".',
	'article-comments-comment-missing-name-parameter' => 'Naam ontbreek',
	'article-comments-comment-missing-date-parameter' => 'Geen datum vir kommentaar',
	'article-comments-no-spam' => 'Ten minste een van die voorgelegde velde is as spam gemerk.',
	'processcomment' => 'Verwerk kommentaar op artikel',
);

/** Breton (Brezhoneg)
 * @author Gwendal
 * @author Y-M D
 */
$messages['br'] = array(
	'article-comments-title-string' => 'titl',
	'article-comments-name-string' => 'Anv',
	'article-comments-name-field' => 'Anv (ret) :',
	'article-comments-url-field' => "Lec'hienn web :",
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Evezhiadenn',
	'article-comments-comment-field' => 'Evezhiadenn :',
	'article-comments-submit-button' => 'Kas',
	'article-comments-leave-comment-link' => 'Lezel un evezhiadenn...',
	'article-comments-invalid-field' => 'An $1 roet <nowiki>[$2]</nowiki> zo direizh.',
	'article-comments-talk-page-starter' => '<noinclude>Evezhiadennoù war [[$1]]
<comments />
----- __NOEDITSECTION__</noinclude>',
	'article-comments-commenter-said' => '$1 en deus laret ...',
	'article-comments-comment-missing-name-parameter' => 'Anv a vank',
);

/** German (Deutsch)
 * @author Kghbln
 * @author The Evil IP address
 * @author 青子守歌
 */
$messages['de'] = array(
	'article-comments-desc' => 'Ermöglicht das Kommentieren von Inhaltsseiten',
	'article-comments-title-string' => 'Titel',
	'article-comments-name-string' => 'Name',
	'article-comments-name-field' => 'Name (erforderlich):',
	'article-comments-url-field' => 'Website:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Kommentar',
	'article-comments-comment-field' => 'Kommentar:',
	'article-comments-submit-button' => 'Speichern',
	'article-comments-leave-comment-link' => 'Hinterlasse einen Kommentar …',
	'article-comments-invalid-field' => 'Die Eingabe <nowiki>[$2]</nowiki> als $1 ist ungültig.',
	'article-comments-required-field' => '$1 ist ein Pflichtfeld.',
	'article-comments-submission-failed' => 'Die Kommentierung ist fehlgeschlagen.',
	'article-comments-failure-reasons' => 'Die Kommentierung ist fehlgeschlagen weil:',
	'article-comments-no-comments' => 'Für die Seite „[[$1]]“ können momentan keine Kommentare abgegeben werden.',
	'article-comments-talk-page-starter' => '<noinclude>Kommentare zur Seite „[[$1]]“
<comments />
----- __NOEDITSECTION__</noinclude>',
	'article-comments-commenter-said' => '$1 meinte …',
	'article-comments-summary' => 'Der Kommentar wurde von $1 abgegeben.',
	'article-comments-submission-succeeded' => 'Die Kommentierung wurde durchgeführt.',
	'article-comments-submission-success' => 'Du hast erfolgreich einen Kommentar für Seite „[[$1]]“ abgegeben.',
	'article-comments-submission-view-all' => 'Du kannst [[$1|alle Kommentare zu dieser Seite]] einsehen',
	'article-comments-user-is-blocked' => 'Du darfst die Seite „[[$1]]“ derzeit nicht bearbeiten.',
	'article-comments-comment-bad-mode' => 'Für die Kommentierung wurde ein ungültiger Modus angegeben.
Möglich sind die Modi „plain“, „normal“ und „wiki“.',
	'article-comments-comment-missing-name-parameter' => 'Fehlender Name',
	'article-comments-comment-missing-date-parameter' => 'Fehlendes Datum',
	'article-comments-no-spam' => 'Mindestens eine der Angaben wurde als Spam identifiziert.',
	'processcomment' => 'Kommentieren von Inhaltsseiten verarbeiten',
);

/** Spanish (Español)
 * @author Danke7
 */
$messages['es'] = array(
	'article-comments-title-string' => 'título',
	'article-comments-name-string' => 'Nombre',
	'article-comments-name-field' => 'Nombre (requerido):',
	'article-comments-url-field' => 'Sitio Web:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Comentario',
	'article-comments-comment-field' => 'Comentario:',
	'article-comments-talk-page-starter' => '<noinclude> Comentarios en [[$ 1]] 
<comments />
 ----- __NOEDITSECTION__ </noinclude>',
);

/** French (Français)
 * @author Peter17
 * @author 青子守歌
 */
$messages['fr'] = array(
	'article-comments-desc' => 'Active les sections de commentaires sur les pages d’articles',
	'article-comments-title-string' => 'titre',
	'article-comments-name-string' => 'Nom',
	'article-comments-name-field' => 'Nom (obligatoire) :',
	'article-comments-url-field' => 'Site web :',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Commentaire',
	'article-comments-comment-field' => 'Commentaire :',
	'article-comments-submit-button' => 'Soumettre',
	'article-comments-leave-comment-link' => 'Ajouter un commentaire...',
	'article-comments-invalid-field' => 'Le $1 fourni <nowiki>[$2]</nowiki> est invalide.',
	'article-comments-required-field' => 'Le champ $1 est obligatoire.',
	'article-comments-submission-failed' => 'L’envoi du commentaire a échoué',
	'article-comments-failure-reasons' => 'Désolé, l’envoi de votre commentaire a échoué pour la (les) raison(s) suivante(s) :',
	'article-comments-no-comments' => 'Désolé, l’article « [[$1]] » n’accepte pas les commentaires pour le moment.',
	'article-comments-talk-page-starter' => '<noinclude>Commentaires sur [[$1]]
<comments />
----- __NOEDITSECTION__</noinclude>',
	'article-comments-commenter-said' => '$1 a dit...',
	'article-comments-summary' => 'Commentaires apportés par $1 - via l’extension ArticleComments',
	'article-comments-submission-succeeded' => 'L’envoi du commentaire a réussi',
	'article-comments-submission-success' => 'Vous avez envoyé avec succès un commentaire sur « [[$1]] »',
	'article-comments-submission-view-all' => 'Vous pouvez voir tous les commentaires sur cet article [[$1|ici]]',
	'article-comments-user-is-blocked' => 'Votre compte utilisateur est actuellement bloqué en écriture sur « [[$1]] ».',
	'article-comments-comment-bad-mode' => 'Le mode fourni pour le commentaire est invalide.
Les modes disponibles sont «plain», « normal » et « wiki ».',
	'article-comments-comment-missing-name-parameter' => 'Nom manquant',
	'article-comments-comment-missing-date-parameter' => 'Date du commentaire manquante',
	'article-comments-no-spam' => 'Au moins un des champs soumis a été marqué comme spam.',
	'processcomment' => 'Traitement du commentaire sur l’article',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'article-comments-desc' => 'Activa os comentarios nas seccións dos artigos',
	'article-comments-title-string' => 'título',
	'article-comments-name-string' => 'Nome',
	'article-comments-name-field' => 'Nome (obrigatorio):',
	'article-comments-url-field' => 'Páxina web:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Comentario',
	'article-comments-comment-field' => 'Comentario:',
	'article-comments-submit-button' => 'Enviar',
	'article-comments-leave-comment-link' => 'Deixe un comentario...',
	'article-comments-required-field' => 'O campo "$1" é obrigatorio.',
	'article-comments-commenter-said' => '$1 dixo...',
	'article-comments-comment-missing-name-parameter' => 'Falta o nome',
	'article-comments-comment-missing-date-parameter' => 'Fata a data do comentario',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'article-comments-desc' => 'Activa sectiones de commentos in paginas de articulos',
	'article-comments-title-string' => 'titulo',
	'article-comments-name-string' => 'Nomine',
	'article-comments-name-field' => 'Nomine (obligatori):',
	'article-comments-url-field' => 'Sito web:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Commento',
	'article-comments-comment-field' => 'Commento:',
	'article-comments-submit-button' => 'Submitter',
	'article-comments-leave-comment-link' => 'Lassar un commento ...',
	'article-comments-invalid-field' => 'Le $1 fornite <nowiki>[$2]</nowiki> es invalide.',
	'article-comments-required-field' => 'Le campo $1 es obligatori.',
	'article-comments-submission-failed' => 'Submission de commento fallite',
	'article-comments-failure-reasons' => 'Regrettabilemente, le submission de tu commento ha fallite pro le sequente ration(es):',
	'article-comments-no-comments' => 'Regrettabilemente, le articulo "[[$1]]" non accepta commentos pro le momento.',
	'article-comments-talk-page-starter' => '<noinclude>Commentos super [[$1]]
<comments />
----- __NOEDITSECTION__</noinclude>',
	'article-comments-commenter-said' => '$1 diceva ...',
	'article-comments-summary' => 'Commento fornite per $1 - via le extension ArticleComments',
	'article-comments-submission-succeeded' => 'Submission de commento succedite',
	'article-comments-submission-success' => 'Tu ha submittite un commento super "[[$1]]" con successo.',
	'article-comments-submission-view-all' => 'Tu pote vider [[$1|hic]] tote le commentos super iste articulo',
	'article-comments-user-is-blocked' => 'Tu conto de usator es actualmente blocate contra modificar "[[$1]]".',
	'article-comments-comment-bad-mode' => 'Modo invalide date pro commento.
Disponibile es "plain", "normal" e "wiki".',
	'article-comments-comment-missing-name-parameter' => 'Nomine mancante',
	'article-comments-comment-missing-date-parameter' => 'Data de commento mancante',
	'article-comments-no-spam' => 'Al minus un del campos submittite esseva marcate como spam.',
	'processcomment' => 'Tractar commento de articulo',
);

/** Japanese (日本語)
 * @author 青子守歌
 */
$messages['ja'] = array(
	'article-comments-desc' => 'コンテンツのページで、コメント節を有効化する',
	'article-comments-title-string' => '題名',
	'article-comments-name-string' => '名前',
	'article-comments-name-field' => '名前（必須）：',
	'article-comments-url-field' => 'ウェブサイト：',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'コメント',
	'article-comments-comment-field' => 'コメント：',
	'article-comments-submit-button' => '送信',
	'article-comments-leave-comment-link' => 'コメントを残す・・・',
	'article-comments-invalid-field' => '$1に入力された値<nowiki>[$2]</nowiki>が不正です。',
	'article-comments-required-field' => '$1項目は必須です。',
	'article-comments-submission-failed' => 'コメント投稿に失敗しました',
	'article-comments-failure-reasons' => '申し訳ありませんが、コメントの投稿が、次の理由により失敗しました：',
	'article-comments-no-comments' => '申し訳ありませんが、ページ「[[$1]]」は、現在、コメントの投稿を受け付けていません。',
	'article-comments-talk-page-starter' => '<noinclude>[[$1]]へのコメント
<comments />
----- __NOEDITSECTION__</noinclude>',
	'article-comments-commenter-said' => '$1いわく・・・',
	'article-comments-summary' => '$1によるコメント（記事コメント拡張機能による）',
	'article-comments-submission-succeeded' => 'コメント投稿に成功しました',
	'article-comments-submission-success' => '「[[$1]]」へのコメントの投稿に成功しました',
	'article-comments-submission-view-all' => '[[$1|そのページのすべてのコメント]]を見ることができます',
	'article-comments-user-is-blocked' => '利用者アカウントが、現在、「[[$1]]」の編集をブロックされています。',
	'article-comments-comment-bad-mode' => 'コメントに対して、無効な形式です。
「plain」「normal」あるいは「wiki」が有効です。',
	'article-comments-comment-missing-name-parameter' => '名前がありません',
	'article-comments-comment-missing-date-parameter' => 'コメントの日付がありません',
	'article-comments-no-spam' => '投稿された項目のうち、少なくとも1つがスパムとしてフラグが設定されました。',
	'processcomment' => '記事コメントの処理',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'article-comments-desc' => 'Erméiglecht Abschnitter mat Bemierkungen op Artikelsäiten',
	'article-comments-title-string' => 'Titel',
	'article-comments-name-string' => 'Numm',
	'article-comments-name-field' => 'Numm (obligatoresch):',
	'article-comments-url-field' => 'Internetsite:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Bemierkung',
	'article-comments-comment-field' => 'Bemierkung:',
	'article-comments-submit-button' => 'Schécken',
	'article-comments-leave-comment-link' => 'Eng Bemierkung derbäisetzen ...',
	'article-comments-invalid-field' => 'Den $1 <nowiki>[$2]</nowiki> deen Dir uginn hutt ass net valabel.',
	'article-comments-required-field' => "D'Feld $1 ass obligatoresch.",
	'article-comments-commenter-said' => '$1 huet gesot ...',
	'article-comments-submission-success' => 'Är Bemierkung fir [[$1]] ass gespäichert.',
	'article-comments-comment-missing-name-parameter' => 'Den Numm feelt',
	'article-comments-comment-missing-date-parameter' => 'Datum vun der Bemierkung feelt',
	'processcomment' => "D'Bemierkung zum Artikel gëtt verschafft",
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 * @author 青子守歌
 */
$messages['mk'] = array(
	'article-comments-desc' => 'Дава пасуси за коментари во статиите',
	'article-comments-title-string' => 'наслов',
	'article-comments-name-string' => 'Име',
	'article-comments-name-field' => 'Име (задолжително):',
	'article-comments-url-field' => 'Мреж.место:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Коментар',
	'article-comments-comment-field' => 'Коментар:',
	'article-comments-submit-button' => 'Поднеси',
	'article-comments-leave-comment-link' => 'Напишете коментар ...',
	'article-comments-invalid-field' => 'Наведеното „$1“ е <nowiki>[$2]</nowiki> неважечко.',
	'article-comments-required-field' => 'Се бара полето $1.',
	'article-comments-submission-failed' => 'Поднесувањето на коментарот не успеа.',
	'article-comments-failure-reasons' => 'Нажалост, поднесувањето на коментарот не успеа, и тоа од следниве причини:',
	'article-comments-no-comments' => 'Нажалост, статијата „[[$1]]“ моментално не прифаќа коментари.',
	'article-comments-talk-page-starter' => '<noinclude>Коментари за [[$1]]
<comments />
----- __NOEDITSECTION__</noinclude>',
	'article-comments-commenter-said' => '$1 рече ...',
	'article-comments-summary' => 'Коментар на $1 - преку додатокот ArticleComments',
	'article-comments-submission-succeeded' => 'Поднесувањето на коментарот успеа.',
	'article-comments-submission-success' => 'Успешно поднесовте коментар за „[[$1]]“',
	'article-comments-submission-view-all' => 'Сите коментари за таа статија можете да ги погледате [[$1|тука]]',
	'article-comments-user-is-blocked' => 'На вашата корисничка моментално не ѝ е дозволено да ја уредува страницата „[[$1]]“.',
	'article-comments-comment-bad-mode' => 'Зададен е неважечки режим за коментарот.
На располагање ви се „plain“, „normal“ и „wiki“.',
	'article-comments-comment-missing-name-parameter' => 'Недостасува име',
	'article-comments-comment-missing-date-parameter' => 'Недостасува датум на коментарот',
	'article-comments-no-spam' => 'Барем едно од поднесените полиња е означено како спам.',
	'processcomment' => 'Обработи го коментарот',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'article-comments-desc' => "Maakt het mogelijk om opmerkingen te plaatsen bij paragrafen op pagina's",
	'article-comments-title-string' => 'titel',
	'article-comments-name-string' => 'Naam',
	'article-comments-name-field' => 'Naam (verplicht):',
	'article-comments-url-field' => 'Website:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Opmerking',
	'article-comments-comment-field' => 'Opmerking:',
	'article-comments-submit-button' => 'Opslaan',
	'article-comments-leave-comment-link' => 'Opmerking plaatsen...',
	'article-comments-invalid-field' => 'De opgegeven $1 <nowiki>[$2]</nowiki> is ongeldig.',
	'article-comments-required-field' => 'Het veld $1 is verplicht.',
	'article-comments-submission-failed' => 'Het opslaan van de opmerking is mislukt.',
	'article-comments-failure-reasons' => 'Het opslaan van uw opmerking is mislukt om de volgende reden(en):',
	'article-comments-no-comments' => 'Bij de pagina "[[$1]]" kunnen op het moment geen opmerkingen geplaatst worden.',
	'article-comments-talk-page-starter' => '<noinclude>Opmerkingen bij [[$1]]
<comments />
----- __NOEDITSECTION__</noinclude>',
	'article-comments-commenter-said' => '$1 schreef ...',
	'article-comments-summary' => 'Opmerking van $1',
	'article-comments-submission-succeeded' => 'De opmerking is opgeslagen',
	'article-comments-submission-success' => 'U hebt een opmerking bij "[[$1]]" opgeslagen',
	'article-comments-submission-view-all' => 'U kunt [[$1|alle opmerkingen bij die pagina]] bekijken',
	'article-comments-user-is-blocked' => 'Uw gebruiker kan op dit moment "[[$1]]" niet bewerken.',
	'article-comments-comment-bad-mode' => 'Er is een ongeldige modus opgegeven voor de opmerking.
Beschikbaar zijn "plain", "normal" en "wiki".',
	'article-comments-comment-missing-name-parameter' => 'De naam mist',
	'article-comments-comment-missing-date-parameter' => 'De datum voor de opmerking mist',
	'article-comments-no-spam' => 'Tenminste een van de opgeslagen velden had een inhoud die als spam is aangemerkt.',
	'processcomment' => 'Opmerking verwerken',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'article-comments-desc' => 'Slå på kommentarseksjoner på innholdssider',
	'article-comments-title-string' => 'tittel',
	'article-comments-name-string' => 'Navn',
	'article-comments-name-field' => 'Navn (påkrevd):',
	'article-comments-url-field' => 'Nettsted:',
	'article-comments-url-string' => 'URL',
	'article-comments-submit-button' => 'Lagre',
	'article-comments-leave-comment-link' => 'Legg igjen en kommentar ...',
	'article-comments-required-field' => '$1-feltet er påkrevd.',
	'article-comments-submission-failed' => 'Kommentering mislyktes',
	'article-comments-failure-reasons' => 'Beklager, kommentaren din mislyktes på grunn av:',
	'article-comments-no-comments' => 'Beklager, siden «[[$1]]» er ikke åpen for kommentarer nå',
	'article-comments-talk-page-starter' => '<noinclude>Kommentarer på [[$1]]
<comments />
---- __NOEDITSECTION__</noinclude>',
	'article-comments-commenter-said' => '$1 sa ...',
	'article-comments-summary' => 'Kommentar av $1 – via ArticleComments-utvidelsen',
	'article-comments-submission-succeeded' => 'Kommentering lyktes',
	'article-comments-submission-success' => 'Du har kommentert «[[$1]]»',
	'article-comments-submission-view-all' => 'Du kan vise [[$1|alle kommentarer på den siden]]',
	'article-comments-user-is-blocked' => 'Kontoen din er blokkert fra å redigere «[[$1]]».',
	'article-comments-comment-bad-mode' => 'Ugyldig modis for kommentarer.
Tilgjengelige moduser er «plain», «normal» og «wiki».',
	'article-comments-comment-missing-name-parameter' => 'Mangler navn',
	'article-comments-comment-missing-date-parameter' => 'Mangler kommentardato',
	'article-comments-no-spam' => 'Minst ett av feltene ble merket som spam.',
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'article-comments-desc' => 'Umożliwia komentowanie poszczególnych sekcji na stronach treści',
	'article-comments-title-string' => 'tytuł',
	'article-comments-name-string' => 'Nazwa',
	'article-comments-name-field' => 'Nazwa (wymagane)',
	'article-comments-url-field' => 'Strona internetowa',
	'article-comments-url-string' => 'adres URL',
	'article-comments-comment-string' => 'Komentarz',
	'article-comments-comment-field' => 'Komentarz',
	'article-comments-submit-button' => 'Zapisz',
	'article-comments-leave-comment-link' => 'Zostaw komentarz...',
	'article-comments-invalid-field' => 'dla „$1” wartość <nowiki>[$2]</nowiki> jest nieprawidłowa',
	'article-comments-required-field' => 'wypełnienie pola „$1” jest obowiązkowe',
	'article-comments-submission-failed' => 'Nieudane zapisanie komentarza',
	'article-comments-failure-reasons' => 'Komentarz nie może zostać zapisany ponieważ',
	'article-comments-no-comments' => 'W tej chwili nie można komentować strony „[[$1]]”.',
	'article-comments-talk-page-starter' => '<noinclude>Komentarze do [[$1]]
<comments />
----- __NOEDITSECTION__</noinclude>',
	'article-comments-commenter-said' => '$1 napisał...',
	'article-comments-summary' => 'Komentarz dodany przez $1 za pomocą rozszerzenia ArticleComments',
	'article-comments-submission-succeeded' => 'Komentarz zapisano',
	'article-comments-submission-success' => 'Zapisano Twój komentarz dla „[[$1]]”',
	'article-comments-submission-view-all' => 'Możesz zobaczyć [[$1|wszystkie komentarze do tej strony]]',
	'article-comments-user-is-blocked' => 'Twoje konto użytkownika ma obecnie zablokowaną możliwość edycji „[[$1]]”.',
	'article-comments-comment-bad-mode' => 'Nieprawidłowy tryb dla komentarza.
Dostępne tryby to: „plain”, „normal” i „wiki”.',
	'article-comments-comment-missing-name-parameter' => 'Brak nazwy',
	'article-comments-comment-missing-date-parameter' => 'Brak daty dodania komentarza',
	'article-comments-no-spam' => 'Co najmniej treść jednego z pól rozpoznano jako spam.',
	'processcomment' => 'Komentowanie artykułu',
);

/** Portuguese (Português)
 * @author Hamilton Abreu
 */
$messages['pt'] = array(
	'article-comments-desc' => 'Possibilita secções de comentários nas páginas de conteúdo',
	'article-comments-title-string' => 'título',
	'article-comments-name-string' => 'Nome',
	'article-comments-name-field' => 'Nome (obrigatório):',
	'article-comments-url-field' => 'Site:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Comentário',
	'article-comments-comment-field' => 'Comentário:',
	'article-comments-submit-button' => 'Enviar',
	'article-comments-leave-comment-link' => 'Deixe um comentário ...',
	'article-comments-invalid-field' => 'O valor que forneceu como $1, <nowiki>[$2]</nowiki>, é inválido',
	'article-comments-required-field' => 'O campo "$1" é obrigatório.',
	'article-comments-submission-failed' => 'O envio do comentário falhou',
	'article-comments-failure-reasons' => 'O envio do seu comentário falhou pelas seguintes razões:',
	'article-comments-no-comments' => 'Neste momento, a página "[[$1]]" não aceita comentários.',
	'article-comments-talk-page-starter' => '<noinclude>Comentários a [[$1]]
<comments />
----- __NOEDITSECTION__</noinclude>',
	'article-comments-commenter-said' => '$1 comentou ...',
	'article-comments-summary' => 'Comentário de $1 - através da extensão ArticleComments',
	'article-comments-submission-succeeded' => 'Comentário enviado',
	'article-comments-submission-success' => 'Enviou um comentário a "[[$1]]"',
	'article-comments-submission-view-all' => 'Pode ver [[$1|todos os comentários dessa página]]',
	'article-comments-user-is-blocked' => 'Edições a "[[$1]]" estão neste momento bloqueadas para a sua conta.',
	'article-comments-comment-bad-mode' => 'O modo do comentário é inválido.
Os modos disponíveis são "plain" (simples), "normal" e "wiki".',
	'article-comments-comment-missing-name-parameter' => 'Falta o nome',
	'article-comments-comment-missing-date-parameter' => 'Falta a data do comentário',
	'article-comments-no-spam' => 'Pelo menos um dos campos enviados foi identificado como spam.',
	'processcomment' => 'Processar o comentário ao artigo',
);

/** Romanian (Română)
 * @author Firilacroco
 */
$messages['ro'] = array(
	'article-comments-title-string' => 'titlu',
	'article-comments-name-string' => 'Nume',
	'article-comments-name-field' => 'Nume (obligatoriu):',
	'article-comments-url-field' => 'Website:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Comentariu',
	'article-comments-comment-field' => 'Comentariu:',
	'article-comments-submit-button' => 'Trimite',
	'article-comments-leave-comment-link' => 'Lăsaţi un comentariu ...',
	'article-comments-required-field' => 'Câmpul $1 este obligatoriu.',
	'article-comments-submission-failed' => 'Trimiterea comentariului a eșuat',
	'article-comments-commenter-said' => '$1 a spus ...',
	'article-comments-submission-succeeded' => 'Trimiterea comentariului s-a efectuat cu succes',
	'article-comments-comment-missing-name-parameter' => 'Lipseşte numele',
	'article-comments-comment-missing-date-parameter' => 'Lipseşte data comentariului',
);

/** Russian (Русский)
 * @author MaxSem
 */
$messages['ru'] = array(
	'article-comments-name-string' => 'Имя',
	'article-comments-url-field' => 'Сайт:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Комментарий',
	'article-comments-comment-field' => 'Комментарий:',
	'article-comments-leave-comment-link' => 'Написать комментарий...',
	'article-comments-submission-succeeded' => 'Комментарий успешно отправлен',
	'article-comments-submission-success' => 'Комментарий к «[[$1]]» успешно отправлен',
	'article-comments-comment-missing-name-parameter' => 'Отсутствует имя',
	'article-comments-comment-missing-date-parameter' => 'Отсутствует дата написания комментария',
);

