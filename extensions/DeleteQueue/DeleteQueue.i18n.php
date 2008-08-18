<?php
/**
 * Internationalisation file for extension DeleteQueue.
 *
 * @addtogroup Extensions
 */

$messages = array();

/** English
 * @author Andrew Garrett
 */
$messages['en'] = array(
	// General
	'deletequeue-desc' => 'Creates a [[Special:DeleteQueue|queue-based system for managing deletion]]',

	// Landing page
	'deletequeue-action' => 'Suggest deletion',
	'deletequeue-action-title' => "Suggest deletion of \"$1\"",
	'deletequeue-action-text' => "{{SITENAME}} has a number of processes for deleting pages:
*If you believe that this page warrants ''speedy deletion'', you may suggest that [{{fullurl:{{FULLPAGENAME}}|action=delnom&queue=speedy}} here].
*If this page does not warrant speedy deletion, but ''deletion will likely be uncontroversial'', you should [{{fullurl:{{FULLPAGENAME}}|action=delnom&queue=prod}} propose uncontested deletion].
*If this page's deletion is ''likely to be contested'', you should [{{fullurl:{{FULLPAGENAME}}|action=delnom&queue=deletediscuss}} open a discussion].",

	// Permissions errors
	'deletequeue-permissions-noedit' => "You must be able to edit a page to be able to affect its deletion status.",

	// Nomination forms
	'deletequeue-generic-reasons' => "* Generic reasons\n ** Vandalism\n ** Spam\n ** Maintenance\n ** Out of project scope",

	// Speedy deletion
	'deletequeue-speedy-title' => 'Mark "$1" for speedy deletion',
	'deletequeue-speedy-text' => "You can use this form to mark the page \"'''$1'''\" for speedy deletion.

An administrator will review this request, and, if it is well-founded, delete the page.
You must select a reason for deletion from the drop-down list below, and add any other relevant information.",
	'deletequeue-speedy-reasons' => "-",

	// Proposed deletion
	'deletequeue-prod-title' => "Propose deletion of \"$1\"",
	'deletequeue-prod-text' => "You can use this form to propose that \"'''$1'''\" is deleted.\n
	If, after five days, nobody has contested this page's deletion, it will be deleted after final review by an administrator.",
	'deletequeue-prod-reasons' => '-',

	'deletequeue-delnom-reason' => 'Reason for nomination:',
	'deletequeue-delnom-otherreason' => 'Other reason',
	'deletequeue-delnom-extra' => 'Extra information:',
	'deletequeue-delnom-submit' => 'Submit nomination',

	// Log entries
	'deletequeue-log-nominate' => "nominated [[$1]] for deletion in the '$2' queue.",
	'deletequeue-log-rmspeedy' => "declined to speedily delete [[$1]].",
	'deletequeue-log-requeue' => "transferred [[$1]] to a different deletion queue: from '$2' to '$3'.",
	'deletequeue-log-dequeue' => "removed [[$1]] from the deletion queue '$2'.",

	// Rights
	'right-speedy-nominate' => 'Nominate pages for speedy deletion',
	'right-speedy-review' => 'Review nominations for speedy deletion',
	'right-prod-nominate' => 'Propose page deletion',
	'right-prod-review' => 'Review uncontested deletion proposals',
	'right-deletediscuss-nominate' => 'Start deletion discussions',
	'right-deletediscuss-review' => 'Close deletion discussions',

	// Queue names
	'deletequeue-queue-speedy' => 'Speedy deletion',
	'deletequeue-queue-prod' => 'Proposed deletion',
	'deletequeue-queue-deletediscuss' => 'Deletion discussion',

	// Display of status in page body
	'deletequeue-page-speedy' => "This page has been nominated for speedy deletion.
The reason given for this deletion is ''$1''.",
	'deletequeue-page-prod' => "It has been proposed that this page is deleted.
The reason given was ''$1''.
If this proposal is uncontested at ''$2'', this page will be deleted.
You can contest this page's deletion by [{{fullurl:{{FULLPAGENAME}}|action=delvote}} objecting to deletion].",
	'deletequeue-page-deletediscuss' => "This page has been proposed for deletion, and that proposal has been contested.
The reason given was ''$1''.
A discussion is ongoing at [[$3]], which will conclude at ''$2''.",

	// Review
	//Generic
	'deletequeue-notqueued' => 'The page you have selected is currently not queued for deletion',
	'deletequeue-review-action' => "Action to take:",
	'deletequeue-review-delete' => "Delete the page.",
	'deletequeue-review-change' => "Delete this page, but with a different rationale.",
	'deletequeue-review-requeue' => "Transfer this page to the following queue:",
	'deletequeue-review-dequeue' => "Take no action, and remove the page from the deletion queue.",
	'deletequeue-review-reason' => 'Comments:',
	'deletequeue-review-newreason' => 'New reason:',
	'deletequeue-review-newextra' => 'Extra information:',
	'deletequeue-review-submit' => 'Save Review',
	'deletequeue-review-original' => "Reason for nomination",
	'deletequeue-actiondisabled-involved' => 'The following action is disabled because you have taken part in this deletion case in the roles $1:',
	'deletequeue-actiondisabled-notexpired' => 'The following action is disabled because the deletion nomination has not yet expired:',
	'deletequeue-review-badaction' => 'You specified an invalid action',
	'deletequeue-review-actiondenied' => 'You specified an action which is disabled for this page',
	"deletequeue-review-objections" => "'''Warning''': The deletion of this page has [{{fullurl:{{FULLPAGENAME}}|action=delvoteview&votetype=object}} objections].
Please ensure that you have considered these objections before deleting this page.",
	//Speedy deletion
	'deletequeue-reviewspeedy-tab' => 'Review speedy deletion',
	'deletequeue-reviewspeedy-title' => 'Review speedy deletion nomination of "$1"',
	'deletequeue-reviewspeedy-text' => "You can use this form to review the nomination of \"'''$1'''\" for speedy deletion.
Please ensure that this page can be speedily deleted in accordance with policy.",
	//Proposed deletion
	'deletequeue-reviewprod-tab' => 'Review proposed deletion',
	'deletequeue-reviewprod-title' => 'Review proposed deletion of "$1"',
	'deletequeue-reviewprod-text' => "You can use this form to review the uncontested proposal for the deletion of \"'''$1'''\".",
	// Discussions
	'deletequeue-reviewdeletediscuss-tab' => 'Review deletion',
	'deletequeue-reviewdeletediscuss-title' => "Review deletion discussion for \"$1\"",
	'deletequeue-reviewdeletediscuss-text' => "You can use this form to review the deletion discussion of \"'''$1'''\".

A [{{fullurl:{{FULLPAGENAME}}|action=delviewvotes}} list] of endorsements and objections of this deletion is available, and the discussion itself can be found at [[$2]].
Please ensure that you make a decision in accordance with the consensus on the discussion.",

	// Deletion discussions
	'deletequeue-deletediscuss-discussionpage' => "This is the discussion page for the deletion of [[$1]].
There are currently $2 {{PLURAL:$2|user|users}} endorsing deletion, and $3 {{PLURAL:$3|user|users}} objecting to deletion.
You may [{{fullurl:$1|action=delvote}} endorse or object] to deletion, or [{{fullurl:$1|action=delviewvotes}} view all endorsements and objections].",
	'deletequeue-discusscreate-summary' => 'Creating discussion for deletion of [[$1]].',
	'deletequeue-discusscreate-text' => 'Deletion proposed for the following reason: $2',

	// Roles
	'deletequeue-role-nominator' => 'original nominator for deletion',
	'deletequeue-role-vote-endorse' => 'endorser of deletion',
	'deletequeue-role-vote-object' => 'objector to deletion',

	// Endorsement and objection
	'deletequeue-vote-tab' => 'Endorse/Object to deletion',
	'deletequeue-vote-title' => 'Endorse or object to deletion of "$1"',
	'deletequeue-vote-text' => "You may use this form to endorse or object to the deletion of \"'''$1'''\".
This action will override any previous endorsements/objections you have given to deletion of this page.
You can [{{fullurl:{{FULLPAGENAME}}|action=delviewvotes}} view] the existing endorsements and objections.
The reason given in the nomination for deletion was ''$2''.",
	'deletequeue-vote-legend' => 'Endorse/Object to deletion',
	'deletequeue-vote-action' => 'Recommendation:',
	'deletequeue-vote-endorse' => 'Endorse deletion.',
	'deletequeue-vote-object' => 'Object to deletion.',
	'deletequeue-vote-reason' => 'Comments:',
	'deletequeue-vote-submit' => 'Submit',
	'deletequeue-vote-success-endorse' => 'You have successfully endorsed the deletion of this page.',
	'deletequeue-vote-success-object' => 'You have successfully objected to the deletion of this page.',
	'deletequeue-vote-requeued' => 'You have successfully objected to the deletion of this page.
Due to your objection, the page has been moved to the $1 queue.',

	// View all votes
	'deletequeue-showvotes' => "Endorsements and objections to deletion of \"$1\"",
	'deletequeue-showvotes-text' => "Below are the endorsements and objections made to the deletion of the page \"'''$1'''\".
You can register your own endorsement of, or objection to this deletion [{{fullurl:{{FULLPAGENAME}}|action=delvote}} here].",
	'deletequeue-showvotes-restrict-endorse' => "Show endorsements only",
	'deletequeue-showvotes-restrict-object' => "Show objections only",
	'deletequeue-showvotes-restrict-none' => "Show all endorsements and objections",
	'deletequeue-showvotes-vote-endorse' => "'''Endorsed''' deletion at $1 $2",
	'deletequeue-showvotes-vote-object' => "'''Objected''' to deletion at $1 $2",
	'deletequeue-showvotes-showingonly-endorse' => "Showing only endorsements",
	'deletequeue-showvotes-showingonly-object' => "Showing only objections",
	'deletequeue-showvotes-none' => "There are no endorsements or objections to the deletion of this page.",
	'deletequeue-showvotes-none-endorse' => "There are no endorsements of the deletion of this page.",
	'deletequeue-showvotes-none-object' => "There are no objections to the deletion of this page.",

	// List of queued pages
	'deletequeue' => 'Deletion queue',
	'deletequeue-list-text' => "This page displays all pages which are in the deletion system.",
	'deletequeue-list-search-legend' => 'Search for pages',
	'deletequeue-list-queue' => 'Queue:',
	'deletequeue-list-status' => 'Status:',
	'deletequeue-list-expired' => 'Show only nominations requiring closing.',
	'deletequeue-list-search' => 'Search',
	'deletequeue-list-anyqueue' => '(any)',
	'deletequeue-list-votes' => 'List of votes',
	'deletequeue-list-votecount' => '$1 {{PLURAL:$1|endorsement|endorsements}}, $2 {{PLURAL:$2|objection|objections}}',
	'deletequeue-list-header-page' => 'Page',
	'deletequeue-list-header-queue' => 'Queue',
	'deletequeue-list-header-votes' => 'Endorsements and objections',
	'deletequeue-list-header-expiry' => 'Expiry',
	'deletequeue-list-header-discusspage' => 'Discussion page',
);

/** Message documentation (Message documentation)
 * @author Jon Harald Søby
 * @author Siebrand
 */
$messages['qqq'] = array(
	'deletequeue-permissions-noedit' => '* $1 is a list of formatted error messages.',
	'deletequeue-generic-reasons' => 'Delete reasons in a dropdown menu. Lines prepended with "*" are a category separator. Lines prepended with "**" can be used as a reason. Please do not add additional reasons. This should be customised on wikis where the extension is actually being used.',
	'deletequeue-delnom-otherreason' => '{{Identical|Other reason}}',
	'deletequeue-delnom-extra' => '{{Identical|Extra information}}',
	'deletequeue-review-reason' => '{{Identical|Comments}}',
	'deletequeue-review-newextra' => '{{Identical|Extra information}}',
	'deletequeue-vote-reason' => '{{Identical|Comments}}',
	'deletequeue-list-queue' => '{{Identical|Queue}}',
	'deletequeue-list-search' => '{{Identical|Search}}',
	'deletequeue-list-header-page' => '{{Identical|Page}}',
	'deletequeue-list-header-queue' => '{{Identical|Queue}}',
	'deletequeue-list-header-expiry' => '{{Identical|Expiry}}',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'deletequeue-desc' => 'ينشئ [[Special:DeleteQueue|نظاما معتمدا على طابور للتحكم بالحذف]]',
	'deletequeue-action' => 'اقتراح الحذف',
	'deletequeue-action-title' => 'اقتراح الحذف ل"$1"',
	'deletequeue-delnom-otherreason' => 'سبب آخر',
	'deletequeue-delnom-extra' => 'معلومات إضافية:',
	'deletequeue-delnom-submit' => 'تنفيذ الترشيح',
	'right-prod-nominate' => 'اقتراح حذف الصفحة',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'deletequeue-speedy-title' => 'Отбелязване на „$1“ за бързо изтриване',
	'deletequeue-delnom-otherreason' => 'Друга причина',
	'deletequeue-review-delete' => 'Изтриване на страницата.',
	'deletequeue-review-newreason' => 'Нова причина:',
	'deletequeue-review-newextra' => 'Допълнителна информация:',
	'deletequeue-list-queue' => 'Опашка:',
	'deletequeue-list-status' => 'Статут:',
	'deletequeue-list-search' => 'Търсене',
	'deletequeue-list-header-page' => 'Страница',
	'deletequeue-list-header-queue' => 'Опашка',
	'deletequeue-list-header-discusspage' => 'Дискусионна страница',
);

/** French (Français)
 * @author Grondin
 */
$messages['fr'] = array(
	'deletequeue-desc' => 'Crée un [[Special:DeleteQueue|système de queue pour gérer les suppression]]',
	'deletequeue-action' => 'Suggère la suppression',
	'deletequeue-action-title' => 'Suggère la suppression le « $1 »',
	'deletequeue-action-text' => "{{SITENAME}} dispose d'un nombre de processus pour la suppression des pages :
*Si vous croyez que cette page doit passer par une ''suppression immédiate'', vous pouvez en faire la demande [{{fullurl:{{FULLPAGENAME}}|action=delnom&queue=speedy}} ici].
*Si cette page ne relève pas de la suppression immédiate, mais ''que cette suppression ne posera aucune controverse pour'', vous devrez [{{fullurl:{{FULLPAGENAME}}|action=delnom&queue=prod}} proposer une suppression non contestable].
*Si la suppression de la page est ''sujète à controverses'', vous devrez [{{fullurl:{{FULLPAGENAME}}|action=delnom&queue=deletediscuss}} ouvrir une discussion].",
	'deletequeue-permissions-noedit' => 'Vous devez être capable de modifier une page pour pourvoir affecter son statut de suppression.',
	'deletequeue-generic-reasons' => '*Motifs les plus courants
** Vandalisme
** Pourriel
** Maintenance
** Hors critères',
	'deletequeue-speedy-title' => 'Marquer « $1 » pour une suppression immédiate',
	'deletequeue-speedy-text' => "Vous pouvez utiliser ce formulaire pour parquer la page « '''$1''' » pour une suppression immédiate.

Un administrateur étudiera cette requête et, si elle est bien fondée, supprimera la page.
Vous devez sélectionner un motif à partir de la liste déroulante ci-dessous, et ajouter d’autres information y afférentes.",
	'deletequeue-prod-title' => 'Proposer la suppression de « $1 »',
	'deletequeue-prod-text' => "Vous pouvez utiliser ce formulaire pour propose que « '''$1''' » soit supprimée.

Si, après cinq jours, personne n’a émis d’objection pour cela, elle sera supprimée, après un examen final, par un administrateur.",
	'deletequeue-delnom-reason' => 'Motif pour la nomination :',
	'deletequeue-delnom-otherreason' => 'Autre raison',
	'deletequeue-delnom-extra' => 'Informations supplémentaires :',
	'deletequeue-delnom-submit' => 'Soumettre la nomination',
	'deletequeue-log-nominate' => '[[$1]] nominé pour la suppression dans la queue « $2 ».',
	'deletequeue-log-rmspeedy' => 'refusé pour la suppression immédiate de [[$1]].',
	'deletequeue-log-requeue' => '[[$1]] transféré vers une queue de suppression différente : de « $2 » vers « $3 ».',
	'deletequeue-log-dequeue' => '[[$1]] enlevé depuis la queue de suppression « $2 ».',
	'right-speedy-nominate' => 'Nomine les pages pour une suppression immédiate.',
	'right-speedy-review' => 'Revoir les nominations pour la suppression immédiate',
	'right-prod-nominate' => 'Proposer la suppression de la page',
	'right-prod-review' => 'Revoir les propositions de suppression non contestées',
	'right-deletediscuss-nominate' => 'Commencer les discussions sur la suppression',
	'right-deletediscuss-review' => 'Clôturer les discussions sur la suppression',
	'deletequeue-queue-speedy' => 'Suppression immédiate',
	'deletequeue-queue-prod' => 'Suppression proposée',
	'deletequeue-queue-deletediscuss' => 'Discussion sur la suppression',
	'deletequeue-page-speedy' => "Cette page a été nominée pour une suppression immédiate.
La raison invoquée pour cela est ''« $1 »''.",
	'deletequeue-page-prod' => "Il a été proposé la suppression de cette page.
La raison invoquée est ''« $1 »''.
Si la proposition ne rencontre aucune objection sur ''$2'', la page sera supprimée.
Vous pouvez contester cette suppression en [{{fullurl:{{fullpagename}}|action=delvote}} vous y opposant].",
	'deletequeue-page-deletediscuss' => "Cette page a été proposé à la suppression, celle-ci a été contestée.
Le motif invoqué était ''« $1 »''
Une discussion est intervenue sur [[$3]], laquelle sera conclue le ''$2''.",
	'deletequeue-notqueued' => 'La page que vous avez sélectionnée n’est pas dans la queue des suppression',
	'deletequeue-review-action' => 'Action à prendre :',
	'deletequeue-review-delete' => 'Supprimer la page.',
	'deletequeue-review-change' => 'Supprimer cette page, mais avec une autre raison.',
	'deletequeue-review-requeue' => 'Transférer cette page vers la queue suivante :',
	'deletequeue-review-dequeue' => 'Ne rien faire et retirer la page de la queue de suppression.',
	'deletequeue-review-reason' => 'Commentaires :',
	'deletequeue-review-newreason' => 'Nouveau motif :',
	'deletequeue-review-newextra' => 'Information supplémentaire :',
	'deletequeue-review-submit' => 'Sauvegarder la relecture',
	'deletequeue-review-original' => 'Motif de la nomination',
	'deletequeue-actiondisabled-involved' => 'L’action suivante est désactivée car vous avez pris par dans ce cas de suppresion dans le sens de $1 :',
	'deletequeue-actiondisabled-notexpired' => 'L’action suivante a été désactivée car le délai pour la nomination à la suppression n’est pas encore expiré :',
	'deletequeue-review-badaction' => 'Vous avez indiqué une action incorrecte',
	'deletequeue-review-actiondenied' => 'Vous avez indiqué une action qui est désactivée pour cette page.',
	'deletequeue-review-objections' => "'''Attention''' : la suppression de cette page est [{{FULLURL:{{FULLPAGENAME}}|action=delvoteview|votetype=object}} contestée]. Assurez-vous que vous ayez examiné ces objections avant sa suppression.",
	'deletequeue-reviewspeedy-tab' => 'Revoir la suppression immédiate',
	'deletequeue-reviewspeedy-title' => 'Revoir la suppression immédiate de « $1 »',
	'deletequeue-reviewspeedy-text' => "Vous pouvez utiliser ce formulaire pour revoir la nommination de « '''$1''' » en suppression immédiate.
Veuillez vous assurer que cette page peut être supprimée de la sorte en conformité des règles du projet.",
	'deletequeue-reviewprod-tab' => 'Revoir les suppressions proposées',
	'deletequeue-reviewprod-title' => 'Revoir la suppression proposée pour « $1 »',
	'deletequeue-reviewprod-text' => "Vous pouvez utiliser ce formulaire pour revoir la proposition non contestée pour supprimer « '''$1''' ».",
	'deletequeue-reviewdeletediscuss-tab' => 'Revoir la suppression',
	'deletequeue-reviewdeletediscuss-title' => 'Revoir la discussion de la suppression pour « $1 »',
	'deletequeue-reviewdeletediscuss-text' => "Vous pouvez utiliser ce formulaire pour revoir la discussion concernant la suppression de « ''$1''».

Une [{{FULLURL:{{FULLPAGENAME}}|action=delviewvotes}} liste] des « pour » et des « contre » est disponible, la discussion par elle-même disponible sur [[$2]].
Veuillez vous assurez que vous ayez pris une décision en conformité du consensus issus de la discussion.",
	'deletequeue-deletediscuss-discussionpage' => 'Ceci est la page de discussion concernant la suppression de [[$1]].
Il y a actuellement $2 {{PLURAL:$2|utilisateur|utilisateurs}} en faveur, et $3 {{PLURAL:$3|utilisateur|utilisateurs}} qui y sont opposés.
Vous pouvez [{{FULLURL:$1|action=delvote}} appuyez ou refuser] la suppression, ou [{{FULLURL:$1|action=delviewvotes}} voir tous les « pour » et les « contre »].',
	'deletequeue-discusscreate-summary' => 'Création de la discussion concernant la suppression de [[$1]].',
	'deletequeue-discusscreate-text' => 'Suppression proposée pour la raison suivante : $2',
	'deletequeue-role-nominator' => 'initiateur original de la suppression',
	'deletequeue-role-vote-endorse' => 'Partisan pour la suppression',
	'deletequeue-role-vote-object' => 'Opposant à la suppression',
	'deletequeue-vote-tab' => 'Appuyer/Refuser la suppression',
	'deletequeue-vote-title' => 'Appuyer ou refuser la suppression de « $1 »',
	'deletequeue-vote-text' => "Vous pouvez utiliser ce formulaire pour appuyer ou refuser la suppression de « '''$1''' ».
Cette action écrasera les avis que vous avez émis auparavant dans cette discussion.
Vous pouvez [{{FULLURL:{{FULLPAGENAME}}|action=delviewvotes}} voir] les différents avis déjà émis.
Le motif indiqué pour la nomination à la suppression était ''« $2 »''.",
	'deletequeue-vote-legend' => 'Appuyer/Refuser la suppression',
	'deletequeue-vote-action' => 'Recommandation :',
	'deletequeue-vote-endorse' => 'Appuie la suppression',
	'deletequeue-vote-object' => 'Objet pour la suppression.',
	'deletequeue-vote-reason' => 'Commentaires :',
	'deletequeue-vote-submit' => 'Soumettre',
	'deletequeue-vote-success-endorse' => 'Vous avez appuyé, avec succès, la demande de suppression de cette page.',
	'deletequeue-vote-success-object' => 'Vous avez refusé, avec succès, la demande de suppression de cette page.',
	'deletequeue-vote-requeued' => 'Vous avez rejeté, avec succès, la demande de suppression de cette page.
Par votre refus, la page été déplacée dans la queue $1.',
	'deletequeue-showvotes' => 'Accords et refus concernant la suppression de « $1 »',
	'deletequeue-showvotes-text' => "Voici, ci-dessous, les accords et les désaccords émis en vue de la suppression de la page « '''$1''' ».
Vous pouvez enregistrer [{{FULLURL:{{FULLPAGENAME}}|action=delvote}} ici] votre propre accord ou désaccord sur cette suppression.",
	'deletequeue-showvotes-restrict-endorse' => 'Affiche uniquement les partisans',
	'deletequeue-showvotes-restrict-object' => 'Voir uniquement les oppositions',
	'deletequeue-showvotes-restrict-none' => 'Visionner tous les accords et les refus.',
	'deletequeue-showvotes-vote-endorse' => "'''Pour''' la suppression le $2 à $1",
	'deletequeue-showvotes-vote-object' => "'''Contre''' la suppression le $2 à $1",
	'deletequeue-showvotes-showingonly-endorse' => 'Ne voir que les accords',
	'deletequeue-showvotes-showingonly-object' => 'Ne voir que les refus',
	'deletequeue-showvotes-none' => 'Il n’existe ni « pour », ni « contre » la suppression de cette page.',
	'deletequeue-showvotes-none-endorse' => 'Personne ne s’est prononcé en faveur de la suppression de cette page.',
	'deletequeue-showvotes-none-object' => 'Personne ne s’est prononcé contre la suppression de cette page.',
	'deletequeue' => 'Queue de la suppression',
	'deletequeue-list-text' => 'Cette page affiche toutes les pages qui sont dans le système de suppression.',
	'deletequeue-list-search-legend' => 'Rechercher des pages',
	'deletequeue-list-queue' => 'Queue :',
	'deletequeue-list-status' => 'Statut :',
	'deletequeue-list-expired' => 'Ne voir que les clôture des nominations requises.',
	'deletequeue-list-search' => 'Rechercher',
	'deletequeue-list-anyqueue' => '(plusieurs)',
	'deletequeue-list-votes' => 'Liste des votes',
	'deletequeue-list-votecount' => '$1 {{PLURAL:$1|accord|accords}}, $2 {{PLURAL:$2|refus|refus}}',
	'deletequeue-list-header-page' => 'Page',
	'deletequeue-list-header-queue' => 'Queue',
	'deletequeue-list-header-votes' => 'Accords et refus',
	'deletequeue-list-header-expiry' => 'Expiration',
	'deletequeue-list-header-discusspage' => 'Page de discussion',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 * @author Siebrand
 */
$messages['nl'] = array(
	'deletequeue-desc' => "Voegt een [[Special:DeleteQueue|wachtrij voor het beheren van te verwijderen pagina's]] toe",
	'deletequeue-action' => 'Ter verwijdering voordragen',
	'deletequeue-action-title' => '"$1" ter verwijdering voordragen',
	'deletequeue-action-text' => "{{SITENAME}} heeft een aantal processen voor het verwijderen van pagina's:
* Als u denkt dat deze pagina ''direct verwijderd'' kan worden, kunt u deze pagina voor [{{fullurl:{{FULLPAGENAME}}|action=delnom&queue=speedy}} direct verwijdering] voordragen.
* Als deze pagina niet in aanmerking komt voor directe verwijdering, maar het verwijderen ''waarschijnlijk niet tot discussie leidt'', dan kunt u deze [{{fullurl:{{FULLPAGENAME}}|action=delnom&queue=prod}} voor verwijdering nomineren].
* Als het verwijderen van deze pagina ''waarschijnlijk bezwaar oplevert'', dan kunt u [{{fullurl:{{FULLPAGENAME}}|action=delnom&queue=deletediscuss}} verwijderoverleg starten].",
	'deletequeue-permissions-noedit' => 'U moet rechten hebben een pagina te bewerken om de verwijderstatus te kunnen veranderen.',
	'deletequeue-generic-reasons' => '* Algemene redenen
** Vandalisme
** Spam
** Onderhoud
** Buiten projectscope',
	'deletequeue-speedy-title' => '"$1" voordragen voor directe verwijdering',
	'deletequeue-speedy-text' => "U kunt dit formulier gebruiken om \"'''\$1'''\" voor te dragen voor directe verwijdering.

Een beheerder bekijkt dit verzoek, en verwijdert de pagina's als het verzoek terecht is.
U moet een reden voor verwijdering opgeven uit de onderstaande uitklaplijst en overige relevante informatie invoeren.",
	'deletequeue-prod-title' => '"$1" ter verwijdering voordragen',
	'deletequeue-prod-text' => "U kunt dit formulier gebruiken om \"'''\$1'''\" voor verwijdering voor te dragen.

Als na vijf dagen niemand protest heeft aangetekend tegen de verwijdernominatie, wordt deze na beoordeling door een beheerder verwijderd.",
	'deletequeue-delnom-reason' => 'Reden voor nominatie:',
	'deletequeue-delnom-otherreason' => 'Andere reden',
	'deletequeue-delnom-extra' => 'Extra informatie:',
	'deletequeue-delnom-submit' => 'Nominatie opslaan',
	'deletequeue-log-nominate' => "heeft [[$1]] voor verwijdering voorgedragen in de wachtrij '$2'.",
	'deletequeue-log-rmspeedy' => 'heeft snelle verwijdering van [[$1]] geweigerd.',
	'deletequeue-log-requeue' => "heeft [[$1]] naar een andere verwijderingswachtrij verplaatst: van '$2' naar '$3'.",
	'deletequeue-log-dequeue' => "heeft [[$1]] uit de verwijderingswachtrij '$2' verwijderd.",
	'right-speedy-nominate' => "Pagina's voordragen voor directe verwijdering",
	'right-speedy-review' => 'Nominaties voor directe verwijdering beoordelen',
	'right-prod-nominate' => "Pagina's voor verwijdering voordragen",
	'right-prod-review' => 'Verwijderingsnominaties zonder bezwaar beoordelen',
	'right-deletediscuss-nominate' => 'Verwijderoverleg starten',
	'right-deletediscuss-review' => 'Verwijderoverleg sluiten',
	'deletequeue-queue-speedy' => 'Snelle verwijdering',
	'deletequeue-queue-prod' => 'Verwijderingsvoorstel',
	'deletequeue-queue-deletediscuss' => 'Verwijderoverleg',
	'deletequeue-page-speedy' => "Deze pagina is genomineerd voor snelle verwijdering. De opgegeven reden is: ''$1''.",
	'deletequeue-page-prod' => "Deze pagina is voor verwijdering voorgedragen.
De opgegeven reden is: ''$1''.
Als er geen bezwaar is tegen dit voorstel op ''$2'', wordt deze pagina verwijderd.
U kunt [{{fullurl:{{FULLPAGENAME}}|action=delvote}} bezwaar maken] tegen de verwijdernominatie.",
	'deletequeue-page-deletediscuss' => "Deze pagina is genomineerd voor verwijdering, en tegen dat voorstel is bezwaar gemaakt.
De opgegeven reden is: ''$1''.
Overleg over dit voorstel wordt gevoerd op [[$3]], en loopt af op ''$2''.",
	'deletequeue-notqueued' => 'De door u geselecteerde pagina is niet genomineerd voor verwijdering',
	'deletequeue-review-action' => 'Te nemen actie:',
	'deletequeue-review-delete' => 'De pagina verwijderen.',
	'deletequeue-review-change' => 'Deze pagina om een andere reden verwijderen.',
	'deletequeue-review-requeue' => 'Deze pagina naar een andere wachtrij verplaatsen:',
	'deletequeue-review-dequeue' => 'Geen verwijdering uitvoeren, en de pagina weghalen van de verwijderingswachtrij.',
	'deletequeue-review-reason' => 'Opmerkingen:',
	'deletequeue-review-newreason' => 'Nieuwe reden:',
	'deletequeue-review-newextra' => 'Extra informatie:',
	'deletequeue-review-submit' => 'Beoordeling opslaan',
	'deletequeue-review-original' => 'Reden voor nominatie',
	'deletequeue-actiondisabled-involved' => 'De volgende handeling is uitgeschakeld omdat u in de volgende rollen aan deze verwijdernominatie hebt deelgenomen: $1',
	'deletequeue-actiondisabled-notexpired' => 'De volgende handeling is uitgeschakeld omdat de verwijdernominatie is nog niet verlopen:',
	'deletequeue-review-badaction' => 'U hebt een niet-bestaande handeling opgegeven',
	'deletequeue-review-actiondenied' => 'U hebt een handeling opgegeven die voor deze pagina is uigeschakeld',
	'deletequeue-review-objections' => "'''Waarschuwing''': er is [{{FULLURL:{{FULLPAGENAME}}|action=delvoteview|votetype=object}} bezwaar] gemaakt tegen de verwijdernominatie voor deze pagina.
Zorg er alstublieft voor dat u deze overweegt voordat u deze pagina verwijdert.",
	'deletequeue-reviewspeedy-tab' => 'Snelle verwijdering beoordelen',
	'deletequeue-reviewspeedy-title' => 'De snelle verwijderingsnominatie voor "$1" beoordelen',
	'deletequeue-reviewspeedy-text' => "U kunt dit formulier gebruiken om de nominatie voor snelle verwijdering van \"'''\$1'''\" te beoordelen.
Zorg er alstublieft voor dat u in lijn met het geldende beleid handelt.",
	'deletequeue-reviewprod-tab' => 'Voorgestelde verwijdering nakijken',
	'deletequeue-reviewprod-title' => 'Voorgestelde verwijdering van "$1" nakijken',
	'deletequeue-reviewprod-text' => "U kunt dit formulier gebruiken om de verwijdernominatie van \"'''\$1'''\" te beoordelen.",
	'deletequeue-reviewdeletediscuss-tab' => 'Verwijdernominatie beoordelen',
	'deletequeue-reviewdeletediscuss-title' => "Verwijderoverleg voor \"'''\$1'''\" beoordelen",
	'deletequeue-reviewdeletediscuss-text' => 'U kunt dit formulier gebruiken om de verwijderingsdiscussie voor "$1" na te kijken.

Een [{{FULLURL:{{FULLPAGENAME}}|action=delviewvotes}} lijst] met ondersteuningen en bezwaren voor deze verwijdering is beschikbaar, en de discussie zelf kunt u terugvinden op [[$2]].
Wees zeker dat u een beslissing maakt in overeenstemming met de consensus van de discussie.',
	'deletequeue-deletediscuss-discussionpage' => 'Dit is het verwijderoverleg voor [[$1]].
Er {{PLURAL:$2|is|zijn}} op dit moment {{PLURAL:$2|één gebruiker|$2 gebruikers}} die de verwijdernominatie steunen en {{PLURAL:$3|één gebruiker|$3 gebruikers}} die bezwaart {{PLURAL:$3|heeft|hebben}} tegen de verwijdernominatie.
U kunt [{{FULLURL:$1|action=delvote}} steun of bezwaar] bij de verwijdernominatie aangeven of [{{FULLURL:$1|action=delviewvotes}} alle steun en bezwaar bekijken].',
	'deletequeue-discusscreate-summary' => 'Bezig met het starten van een discussie voor de verwijdering van [[$1]].',
	'deletequeue-discusscreate-text' => 'Verwijdering voorgesteld voor de volgende reden: $2',
	'deletequeue-role-nominator' => 'indiener verwijdervoorstel',
	'deletequeue-role-vote-endorse' => 'ondersteunt verwijdervoorstel',
	'deletequeue-role-vote-object' => 'maakt bezwaar tegen verwijdervoorstel',
	'deletequeue-vote-tab' => 'Bezwaar maken/Steun geven aan de verwijdernominatie',
	'deletequeue-vote-title' => 'Bezwaar maken tegen of steun geven aan de verwijdernominatie voor "$1"',
	'deletequeue-vote-text' => "U kunt dit formulier gebruiken om bezwaar te maken tegen de verwijdernominatie voor \"'''\$1'''\" of deze te steunen.
Deze handeling komt in de plaats van eventuele eerdere uitspraken van steun of bezwaar bij de verwijdernominatie van deze pagina.
U kunt [{{FULLURL:{{FULLPAGENAME}}|action=delviewvotes}} alle steun en bezwaar bekijken].
De reden voor de verwijdernominatie is ''\$2''.",
	'deletequeue-vote-legend' => 'Bezwaar en ondersteuning verwijdervoorstel',
	'deletequeue-vote-action' => 'Aanbeveling:',
	'deletequeue-vote-endorse' => 'Verwijdervoorstel steunen.',
	'deletequeue-vote-object' => 'Bezwaar maken tegen verwijdervoorstel.',
	'deletequeue-vote-reason' => 'Opmerkingen:',
	'deletequeue-vote-submit' => 'Opslaan',
	'deletequeue-vote-success-endorse' => 'Uw steun voor de verwijdernominatie van deze pagina is opgeslagen.',
	'deletequeue-vote-success-object' => 'Uw bezwaar tegen de verwijdernominatie van deze pagina is opgeslagen.',
	'deletequeue-vote-requeued' => 'Uw bezwaar tegen de verwijdernominatie van deze pagina is opgeslagen.
Vanwege uw bezwaar, is de pagina verplaatst naar de wachtrij "$1".',
	'deletequeue-showvotes' => 'Steun en bezwaar bij de verwijdernominatie van "$1"',
	'deletequeue-showvotes-text' => "Hieronder worden steun en bezwaar bij de verwijdernominatie van de pagin \"'''\$1'''\" weergegeven.
U kunt ook [{{FULLURL:{{FULLPAGENAME}}|action=delvote}} steun of bezwaar] aangegeven bij deze verwijdernominatie.",
	'deletequeue-showvotes-restrict-endorse' => 'Alleen steun weergeven',
	'deletequeue-showvotes-restrict-object' => 'Alleen bezwaren weergeven',
	'deletequeue-showvotes-restrict-none' => 'Alle steun en bezwaar weergeven',
	'deletequeue-showvotes-vote-endorse' => "Heeft '''steun''' gegeven voor verwijdering op $1 om $2",
	'deletequeue-showvotes-vote-object' => "Heeft '''bezwaar''' gemaakt tegen verwijdering op $1 om $2",
	'deletequeue-showvotes-showingonly-endorse' => 'Alleen steun wordt weergegeven',
	'deletequeue-showvotes-showingonly-object' => 'Alleen bezwaar wordt weergegeven',
	'deletequeue-showvotes-none' => 'Is is geen steun of bezwaar bij de verwijdernominatie van deze pagina.',
	'deletequeue-showvotes-none-endorse' => 'Er is geen steun voor de verwijdernominatie van deze pagina.',
	'deletequeue-showvotes-none-object' => 'Er is geen bezwaar tegen de verwijdermoninatie van deze pagina.',
	'deletequeue' => 'Verwijderingswachtrij',
	'deletequeue-list-text' => "Deze pagina toont alle pagina's die in het verwijderingssysteem zijn.",
	'deletequeue-list-search-legend' => "Zoeken naar pagina's",
	'deletequeue-list-queue' => 'Wachtrij:',
	'deletequeue-list-status' => 'Status:',
	'deletequeue-list-expired' => 'Alleen verwijdernominaties weergeven die gesloten moeten worden.',
	'deletequeue-list-search' => 'Zoeken',
	'deletequeue-list-anyqueue' => '(alle)',
	'deletequeue-list-votes' => 'Stemmen',
	'deletequeue-list-votecount' => '$1 {{PLURAL:$1|steunbetuiging|steunbetuigingen}}, $2 {{PLURAL:$2|bezwaar|bezwaren}}',
	'deletequeue-list-header-page' => 'Pagina',
	'deletequeue-list-header-queue' => 'Wachtrij',
	'deletequeue-list-header-votes' => 'Steun en bezwaar',
	'deletequeue-list-header-expiry' => 'Verloopdatum',
	'deletequeue-list-header-discusspage' => 'Overlegpagina',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'deletequeue-desc' => 'Skaper et [[Spcial:DeleteQueue|købasert system for å håndtere sletting]]',
	'deletequeue-action' => 'Foreslå sletting',
	'deletequeue-action-title' => 'Foreslå sletting av «$1»',
	'deletequeue-action-text' => "{{SITENAME}} har flere prosesser for sletting av sider:
* Om du mener at denne siden kvalifiserer for ''hurtigsletting'', kan du foreslå det [{{fullurl:{{FULLPAGENAMEE}}|action=delnom&queue=speedy}} her].
* Om siden ikke kvalifserer for hurtigsletting, men ''sletting likevel vil være ukontroversielt'', kan du [{{fullurl:{{FULLPAGENAMEE}}|action=delnom&queue=prod}} foreslå sletting her].
* Om det er sannsynlig at sletting av siden ''vil bli omdiskutert'', burde du [{{fullurl:{{FULLPAGENAMEE}}|action=delnom&queue=deletediscuss}} åpne en diskusjon].",
	'deletequeue-permissions-noedit' => 'Du må kunne redigere en side for kunne påvirke dens slettingsstatus.',
	'deletequeue-generic-reasons' => '* Vanlige reasons
  ** Hæverk
  ** Søppel
  ** Reklame
  ** Vedlikehold
  ** Ikke relevant for prosjektet',
	'deletequeue-speedy-title' => 'Merk «$1» for hurtigsletting',
	'deletequeue-speedy-text' => "Du kan bruke dette skjemaet for å merke siden «'''$1'''» for hurtigsletting.

En administrator vil se gjennom forespørselen, og om den er rimelig, slette siden.
Du må velge en årsak fra lista nedenfor, og legge til annen relevant informasjon.",
	'deletequeue-prod-title' => 'Foreslå sletting av «$1»',
	'deletequeue-prod-text' => "Du kan bruke dette skjemaet for å foreslå at «'''$1'''» slettes.

Om ingen har motsetninger mot slettingen innen fem dager, vil slettingen vurderes av en administrator.",
	'deletequeue-delnom-reason' => 'Nomneringsårsak:',
	'deletequeue-delnom-otherreason' => 'Annen grunn',
	'deletequeue-delnom-extra' => 'Ekstra informasjon:',
	'deletequeue-delnom-submit' => 'Nominer',
	'deletequeue-log-nominate' => 'nominerte [[$1]] for sletting i køen «$2».',
	'deletequeue-log-rmspeedy' => 'avviste hurtigsletting av [[$1]].',
	'deletequeue-log-requeue' => 'overførte [[$1]] til fra slettingskøen «$2» til «$3».',
	'deletequeue-log-dequeue' => 'fjernet [[$1]] fra slettingskøen «$2».',
	'right-speedy-nominate' => 'Nominere sider til hurtigsletting',
	'right-speedy-review' => 'Behandle nominasjoner til hurtigsletting',
	'right-prod-nominate' => 'Foreslå sletting av sider',
	'right-prod-review' => 'Behandle ukontroversielle slettingsforslag',
	'right-deletediscuss-nominate' => 'Starte slettingsdiskusjoner',
	'right-deletediscuss-review' => 'Avslutte slettingsdiskusjoner',
	'deletequeue-queue-speedy' => 'Hurtigsletting',
	'deletequeue-queue-prod' => 'Slettingsforslag',
	'deletequeue-queue-deletediscuss' => 'Slettingsdiskusjon',
	'deletequeue-page-speedy' => "Denne siden har blitt nominert for hurtigsletting.
Årsaken som ble oppgitt var ''$1''.",
	'deletequeue-page-prod' => "Denne siden har blitt foreslått for sletting.
Årsaken som ble oppgitt var ''$1''.
Om dette forslaget ikke er motsagt innen ''$2'', vil siden bli slettet.
Du kan bestride sletting av siden ved å [{{fullurl:{{FULLPAGENAME}}|action=delvote}} motsi sletting].",
	'deletequeue-page-deletediscuss' => "Denne siden har blitt foreslått slettet, men forslaget har blitt bestridt.
Den oppgitte slettingsgrunnen var ''$1''.
En diskusjon foregår på [[$3]]; den vil slutte ''$2''.",
	'deletequeue-notqueued' => 'Siden du har valgt er ikke foreslått slettet',
	'deletequeue-review-action' => 'Handling:',
	'deletequeue-review-delete' => 'Slette siden.',
	'deletequeue-review-change' => 'Slette siden, men med annen begrunnelse.',
	'deletequeue-review-requeue' => 'Overføre siden til følgende kø:',
	'deletequeue-review-dequeue' => 'Ikke gjøre noe, og fjerne siden fra slettingskøen.',
	'deletequeue-review-reason' => 'Kommentarer:',
	'deletequeue-review-newreason' => 'Ny årsak:',
	'deletequeue-review-newextra' => 'Ekstra informasjon:',
	'deletequeue-review-submit' => 'Lagre gjennomgang',
	'deletequeue-review-original' => 'Nominasjonsårsak',
	'deletequeue-actiondisabled-involved' => 'Følgende handling kan ikke gjøres av deg, fordi du har tatt del i slettingen som $1:',
	'deletequeue-actiondisabled-notexpired' => 'Følgende handling kan ikke gjennomføres, fordi slettingsforslaget ikke har utgått:',
	'deletequeue-review-badaction' => 'Du oppga en ugyldig handling',
	'deletequeue-review-actiondenied' => 'Du oppga en handling som er slått av for denne siden',
	'deletequeue-review-objections' => "'''Advarsel''': Det er [{{fullurl:{{FULLPAGENAME}}|action=delvoteview&votetype=object}} motsigelser] til sletting av denne siden.
Forsikre deg om at du har tatt disse til hensyn før du sletter siden.",
	'deletequeue-reviewspeedy-tab' => 'Behandle hurtigsletting',
	'deletequeue-reviewspeedy-title' => 'Behandle hurtigsletting av «$1»',
	'deletequeue-reviewspeedy-text' => "Du kan bruke denne skjemaet for å vurdere hurtigsletting av «'''$1'''».
Forsikre deg om at siden kan hurtigslettes ifm. retningslinjene.",
	'deletequeue-reviewprod-tab' => 'Behandle slettingsforslag',
	'deletequeue-reviewprod-title' => 'Behandle slettingsforslag av «$1»',
	'deletequeue-reviewprod-text' => "Du kan bruke dette skjamet for å behandle sletting av «'''$1'''».",
	'deletequeue' => 'Slettingskø',
	'deletequeue-list-queue' => 'Kø:',
	'deletequeue-list-status' => 'Status:',
	'deletequeue-list-search' => 'Søk',
	'deletequeue-list-anyqueue' => '(noen)',
	'deletequeue-list-votes' => 'Liste over stemmer',
	'deletequeue-list-header-page' => 'Side',
	'deletequeue-list-header-queue' => 'Kø',
	'deletequeue-list-header-expiry' => 'Varighet',
	'deletequeue-list-header-discusspage' => 'Diskusjonsside',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'deletequeue-generic-reasons' => '*Motius mai corrents
** Vandalisme
** Spam
** Mantenença
** Fòra de critèris',
	'deletequeue-vote-text' => "Podètz utilizar aqueste formulari per apiejar o refusar la supression de « '''$1''' ».
Aquesta accion espotirà los vejaires qu'avètz emeses deperabans dins aquesta discussion.
Podètz [{{FULLURL:{{FULLPAGENAME}}|action=delviewvotes}} veire] los diferents vejaires ja emeses.
Lo motiu indicat per la nominacion a la supression èra ''« $2 »''.",
);

/** Swedish (Svenska)
 * @author Leo Johannes
 * @author M.M.S.
 */
$messages['sv'] = array(
	'deletequeue-desc' => 'Skapar en [[Special:DeleteQueue|köbaserat system för att hantera raderingar]]',
	'deletequeue-action' => 'Föreslå radering',
	'deletequeue-action-title' => 'Föreslå radering av "$1"',
	'deletequeue-speedy-title' => 'Märk "$1" för snabbradering',
	'deletequeue-prod-title' => 'Föreslå radering av "$1"',
	'deletequeue-delnom-reason' => 'Anledning till nominering:',
	'deletequeue-delnom-otherreason' => 'Annan anledning',
	'deletequeue-delnom-extra' => 'Extrainformation:',
	'deletequeue-queue-speedy' => 'Snabbradering',
	'deletequeue-queue-prod' => 'Föreslagen radering',
	'deletequeue-queue-deletediscuss' => 'Raderingsdiskussion',
	'deletequeue-page-speedy' => "Denna sida har nominerats för snabbradering.
Anledningen som givits för denna radering är ''$1''.",
	'deletequeue-review-delete' => 'Radera sidan.',
	'deletequeue-review-reason' => 'Kommentarer:',
	'deletequeue-review-newreason' => 'Ny anledning:',
	'deletequeue-review-newextra' => 'Extrainformation:',
	'deletequeue-discusscreate-text' => 'Radering föreslagen på grund av följande anledning: $2 \'\'\'[[User:M.M.S.|<span style="color:red;">M.</span>]][[User_talk:M.M.S.|<span style="color:green;">M.</span>]][[Special:Contributions/M.M.S.|<span style="color:blue;">S.</span>]]\'\'\' 19:22, 16 August 2008 (UTC)',
	'deletequeue-vote-reason' => 'Kommentarer:',
	'deletequeue-vote-submit' => 'Skicka',
	'deletequeue' => 'Raderingskö',
	'deletequeue-list-search-legend' => 'Sök efter sidor',
	'deletequeue-list-queue' => 'Kö:',
	'deletequeue-list-status' => 'Status:',
	'deletequeue-list-search' => 'Sök',
	'deletequeue-list-anyqueue' => '(någon)',
	'deletequeue-list-votes' => 'Lista över röster',
	'deletequeue-list-header-page' => 'Sida',
	'deletequeue-list-header-queue' => 'Kö',
	'deletequeue-list-header-expiry' => 'Utgår',
	'deletequeue-list-header-discusspage' => 'Diskussionssida',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'deletequeue-desc' => 'Tạo [[Special:DeleteQueue|hệ thống hàng đợi xóa]]',
	'deletequeue-action' => 'Đề nghị xóa',
	'deletequeue-action-title' => 'Đề nghị xóa “$1”',
	'deletequeue-prod-title' => 'Đề nghị xóa “$1”',
	'deletequeue-delnom-reason' => 'Lý do đề nghị',
	'deletequeue-delnom-otherreason' => 'Lý do khác',
	'deletequeue-delnom-extra' => 'Bổ sung:',
	'deletequeue-delnom-submit' => 'Đề nghị',
	'deletequeue-log-nominate' => 'đã đề nghị xóa [[$1]] trong hàng “$2”.',
	'deletequeue-log-rmspeedy' => 'từ chối xóa nhanh [[$1]].',
	'deletequeue-log-requeue' => 'chuyển [[$1]] qua hàng đợi xóa khác, từ “$2” đến “$3”.',
	'deletequeue-log-dequeue' => 'dời [[$1]] khỏi hàng đợi xóa “$2”.',
	'right-speedy-nominate' => 'Đề nghị xóa nhanh trang',
	'right-speedy-review' => 'Duyệt các trang chờ xóa nhanh',
	'right-prod-nominate' => 'Đề nghị xóa trang',
	'right-prod-review' => 'Duyệt trang chờ xóa',
	'right-deletediscuss-nominate' => 'Bắt đầu thảo luận về trang chờ xóa',
	'right-deletediscuss-review' => 'Kết thúc thảo luận về trang chờ xóa',
	'deletequeue-queue-speedy' => 'Xóa nhanh',
	'deletequeue-queue-prod' => 'Đề nghị xóa',
	'deletequeue-queue-deletediscuss' => 'Thảo luận về trang chờ xóa',
	'deletequeue-review-delete' => 'Xóa trang này.',
	'deletequeue-review-change' => 'Xóa trang này nhưng vì lý do khác.',
	'deletequeue-review-requeue' => 'Chuyển trang này qua hàng sau:',
	'deletequeue-review-dequeue' => 'Không làm gì và dời trang khỏi hàng đợi xóa.',
	'deletequeue-review-reason' => 'Ghi chú:',
	'deletequeue-review-newreason' => 'Lý do mới:',
	'deletequeue-review-newextra' => 'Bổ sung:',
	'deletequeue-review-submit' => 'Lưu thông tin',
	'deletequeue-review-original' => 'Lý do đề nghị',
	'deletequeue-reviewspeedy-tab' => 'Duyệt đề nghị xóa nhanh',
	'deletequeue-reviewspeedy-title' => 'Duyệt đề nghị xóa nhanh “$1”',
	'deletequeue-reviewprod-tab' => 'Duyệt đề nghị xóa',
	'deletequeue-reviewprod-title' => 'Duyệt đề nghị xóa “$1”',
	'deletequeue-reviewdeletediscuss-tab' => 'Duyệt đề nghị xóa',
	'deletequeue-reviewdeletediscuss-title' => 'Duyệt thảo luận về việc xóa “$1”',
	'deletequeue-discusscreate-summary' => 'Đang tạo trang thảo luận về việc xóa [[$1]].',
	'deletequeue-discusscreate-text' => 'Trang bị đề nghị xóa vì lý do sau: $2',
	'deletequeue-role-nominator' => 'người đầu tiên đề nghị xóa',
	'deletequeue-role-vote-endorse' => 'người ủng hộ việc xóa',
	'deletequeue-role-vote-object' => 'người phản đối việc xóa',
	'deletequeue-vote-tab' => 'Ủng hộ/phản đối xóa',
	'deletequeue-vote-title' => 'Ủng hộ hay phản đối việc xóa “$1”',
	'deletequeue-vote-legend' => 'Ủng hộ/phản đối xóa',
	'deletequeue-vote-action' => 'Lựa chọn:',
	'deletequeue-vote-endorse' => 'Ủng hộ việc xóa.',
	'deletequeue-vote-object' => 'Phản đối việc xóa.',
	'deletequeue-vote-reason' => 'Ghi chú:',
	'deletequeue-vote-submit' => 'Bỏ phiếu',
	'deletequeue-showvotes-vote-endorse' => "'''Ủng hộ''' xóa $1 $2",
	'deletequeue-showvotes-vote-object' => "'''Phản đối''' xóa $1 $2",
	'deletequeue' => 'Hàng đợi xóa',
	'deletequeue-list-text' => 'Trang này liệt kê các trang đang chờ xóa.',
	'deletequeue-list-search-legend' => 'Tìm kiếm trang',
	'deletequeue-list-queue' => 'Hàng:',
	'deletequeue-list-status' => 'Tình trạng:',
	'deletequeue-list-search' => 'Tìm kiếm',
	'deletequeue-list-anyqueue' => '(tất cả)',
	'deletequeue-list-votes' => 'Danh sách lá phiếu',
	'deletequeue-list-votecount' => '$1 phiếu ủng hộ, $2 phiếu phản đối',
	'deletequeue-list-header-page' => 'Trang',
	'deletequeue-list-header-queue' => 'Hàng',
	'deletequeue-list-header-votes' => 'Số phiếu',
	'deletequeue-list-header-expiry' => 'Thời hạn',
	'deletequeue-list-header-discusspage' => 'Trang thảo luận',
);

