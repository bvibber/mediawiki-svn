<?php
/**
 * Internationalisation file for extension EmergencyDeSysop.
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

$messages['en'] = array(
	//About the Extension
	'emergencydesysop' => 'Emergency DeSysop',
	'emergencydesysop-desc' => 'Allows a sysop to sacrifice their own privileges, in order to desysop another',

	//Extension Messages
	'emergencydesysop-title' => 'Remove sysop access from both current user and another sysop',
	'emergencydesysop-otheradmin' => 'Other sysop to degroup',
	'emergencydesysop-reason' => 'Reason for removal',
	'emergencydesysop-submit' => 'Submit',
	'emergencydesysop-incomplete' => 'All form fields are required, please try again.',
	'emergencydesysop-notasysop' => 'The target user is not in the sysop group.',
	'emergencydesysop-nogroups' => 'None',
	'emergencydesysop-done' => 'Action complete, both you and [[$1]] have been desysopped.',
	'emergencydesysop-invalidtarget' => 'The target user does not exist.',
	'emergencydesysop-blocked' => 'You cannot access this page while blocked',
	'emergencydesysop-noright' => 'You do not have sufficient permissions to access this page',

	//Rights Messages
	'right-emergencydesysop' => 'Able to desysop another user, mutually',
);

/** Message documentation (Message documentation)
 * @author SPQRobin
 */
$messages['qqq'] = array(
	'emergencydesysop-nogroups' => '{{Identical|None}}',
);

/** German (Deutsch)
 * @author Metalhead64
 */
$messages['de'] = array(
	'emergencydesysop' => 'Not-DeSysop',
	'emergencydesysop-title' => 'Entfernt den Sysop-Status des aktuellen und eines anderen Benutzers',
	'emergencydesysop-otheradmin' => 'Anderen Sysop degruppieren',
	'emergencydesysop-reason' => 'Grund für die Entfernung',
	'emergencydesysop-submit' => 'Übertragen',
	'emergencydesysop-incomplete' => 'Es werden Eingaben in allen Feldern benötigt. Bitte erneut versuchen.',
	'emergencydesysop-notasysop' => "Der gewählte Benutzer ist nicht in der Gruppe ''Sysop''.",
	'emergencydesysop-nogroups' => 'Keine',
	'emergencydesysop-done' => 'Aktion erfolgreich. Du und [[$1]] wurden degruppiert.',
	'emergencydesysop-invalidtarget' => 'Der gewählte Benutzer existiert nicht.',
	'emergencydesysop-blocked' => 'Du kannst nicht auf diese Seite zugreifen, während du gesperrt bist',
	'emergencydesysop-noright' => 'Du hast keine ausreichenden Berechtigungen für diese Seite',
	'right-emergencydesysop' => 'Ermöglicht die gegenseitige Degruppierung von Sysops',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'emergencydesysop-reason' => 'Kialo por forigo',
	'emergencydesysop-nogroups' => 'Neniu',
);

/** French (Français)
 * @author Grondin
 */
$messages['fr'] = array(
	'emergencydesysop' => 'Désysopage d’urgence',
	'emergencydesysop-desc' => 'Permert à un administrateur de renoncer à ses propres limites, en ordre pour désysoper en autre',
	'emergencydesysop-title' => 'Retire les accès d’administreur, ensemble l’utilisateur actuel puis un autre.',
	'emergencydesysop-otheradmin' => 'Autre administrateur à dégrouper',
	'emergencydesysop-reason' => 'Motif du retrait',
	'emergencydesysop-submit' => 'Soumettre',
	'emergencydesysop-incomplete' => 'Tous les champs doivent être renseignés, veuillez essayer à nouveau.',
	'emergencydesysop-notasysop' => 'L’utilisateur visé n’est pas dans le groupe des administrateurs.',
	'emergencydesysop-nogroups' => 'Néant',
	'emergencydesysop-done' => 'Action terminée, vous et [[$1]] avez eu ensemble vos droits d’administrateur de retirés.',
	'emergencydesysop-invalidtarget' => 'L’utilisateur visé n’existe pas.',
	'emergencydesysop-blocked' => 'Vous ne pouvez pas accéder à cette page tant que vous êtes bloqué',
	'emergencydesysop-noright' => 'Vous n’avez pas les permissions suffisantes pour accéder à cette page',
	'right-emergencydesysop' => 'Possible de désysoper mutuellement un autre utilisateur.',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'emergencydesysop-submit' => 'Enviar',
	'emergencydesysop-nogroups' => 'Ningún',
);

/** Ancient Greek (Ἀρχαία ἑλληνικὴ)
 * @author Crazymadlover
 */
$messages['grc'] = array(
	'emergencydesysop-nogroups' => 'Οὐδέν',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'emergencydesysop-nogroups' => 'Keen',
	'emergencydesysop-blocked' => 'Dir kënnt net op dës Säit goen esoulaang wann Dir gespaart sidd',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 * @author Siebrand
 */
$messages['nl'] = array(
	'emergencydesysop' => 'Beheerdersrechten snel intrekken',
	'emergencydesysop-desc' => 'Stelt een beheerder in staat de eigen beheerdersrechten op te offeren om die van een andere beheerder in te trekken',
	'emergencydesysop-title' => 'De beheerdersrechten van zowel de huidige gebruiker als een andere beheerder intrekken',
	'emergencydesysop-otheradmin' => 'Beheerdersschap intrekken van',
	'emergencydesysop-reason' => 'Reden',
	'emergencydesysop-submit' => 'OK',
	'emergencydesysop-incomplete' => 'Alle velden zijn verplicht.',
	'emergencydesysop-notasysop' => 'De opgegeven gebruiker is geen beheerder.',
	'emergencydesysop-nogroups' => 'Geen',
	'emergencydesysop-done' => 'Handeling voltooid, de beheerdersrechten van zowel u als [[$1]] is ingetrokken.',
	'emergencydesysop-invalidtarget' => 'De opgegeven gebruiker bestaat niet.',
	'emergencydesysop-blocked' => 'U kunt deze pagina niet gebruiken omdat u geblokkeerd bent',
	'emergencydesysop-noright' => 'U hebt niet de nodige rechten om deze pagina te kunnen gebruiken',
	'right-emergencydesysop' => 'Heeft de mogelijkheid om de beheerdersrechten van een andere gebruiker en zichzelf in te trekken',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'emergencydesysop' => 'Desysopatge d’urgéncia',
	'emergencydesysop-desc' => 'Permet a un administrator de renonciar a sos pròpris limits, en òrdre per desysopar en autre',
	'emergencydesysop-title' => 'Leva los accèsses d’administrer, ensemble l’utilizaire actual puèi un autre.',
	'emergencydesysop-otheradmin' => 'Autre administrator de desgropar',
	'emergencydesysop-reason' => 'Motiu de levament',
	'emergencydesysop-submit' => 'Sometre',
	'emergencydesysop-incomplete' => 'Totes los camps devon èsser entresenhats, ensajatz tornamai.',
	'emergencydesysop-notasysop' => 'L’utilizaire visat es pas dins lo grop dels administrators.',
	'emergencydesysop-nogroups' => 'Nonrés',
	'emergencydesysop-done' => "Accion acabada, vos e [[$1]] avètz agut vòstres dreches d’administrator levats a l'encòp.",
	'emergencydesysop-invalidtarget' => 'L’utilizaire visat existís pas.',
	'emergencydesysop-blocked' => 'Podètz pas accedir a aquesta page tant que sètz blocat(ada)',
	'emergencydesysop-noright' => 'Avètz pas las permissions sufisentas per accedir a aquesta pagina',
	'right-emergencydesysop' => 'Possible de desysopar mutualament un autre utilizaire.',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'emergencydesysop' => 'Núdzové odobratie správcovských práv',
	'emergencydesysop-desc' => 'Umožňuje správcovi obetovať vlastné práva, aby mohol odobrať práva inému',
	'emergencydesysop-title' => 'Odobrať práva správcu aktuálneho používateľa a iného správcu zároveň',
	'emergencydesysop-otheradmin' => 'Druhý správca',
	'emergencydesysop-reason' => 'Dôvod odstránenia',
	'emergencydesysop-submit' => 'Odoslať',
	'emergencydesysop-incomplete' => 'Je potrebné vyplniť všetky polia formulára, skúste to prosím znova.',
	'emergencydesysop-notasysop' => 'Cieľový používateľ nie je v skupine správcov.',
	'emergencydesysop-nogroups' => 'Žiadny',
	'emergencydesysop-done' => 'Operácia dokončená, vy aj [[$1]] ste boli zbavení správcovských práv.',
	'emergencydesysop-invalidtarget' => 'Cieľový používateľ neexistuje.',
	'emergencydesysop-blocked' => 'Nemáte prístup k tejto stránke, kým ste zablokovaný',
	'emergencydesysop-noright' => 'Nemáte dostatočné oprávnenie na prístup k tejto stránke',
	'right-emergencydesysop' => 'Dokáže odstrániť správcovské práva iného používateľa zároveň so svojimi',
);

/** Swedish (Svenska)
 * @author Micke
 */
$messages['sv'] = array(
	'emergencydesysop' => 'Nödfallsavsättning av administratör',
	'emergencydesysop-desc' => 'Möjliggör för en administratör att offra sina egna användarrättigheter för att avsätta en annan administratör',
	'emergencydesysop-title' => 'Ta bort administratörs-rättigheter från såväl den aktuella användaren som från en annan administratör',
	'emergencydesysop-otheradmin' => 'Annan administratör att avsätta',
	'emergencydesysop-reason' => 'Anledning till avsättandet',
	'emergencydesysop-submit' => 'Skicka',
	'emergencydesysop-incomplete' => 'Alla formulärfält måste fyllas i, försök igen.',
	'emergencydesysop-notasysop' => 'Målanvändaren är inte medlem i gruppen administratörer.',
	'emergencydesysop-nogroups' => 'Ingen',
	'emergencydesysop-done' => 'Handlingen är genomförd, både du och [[$1]] har tagits bort från gruppen "administratörer".',
	'emergencydesysop-invalidtarget' => 'Målanvändaren finns inte.',
	'emergencydesysop-blocked' => 'Du har inte tillgång till denna sida så länge du är blockerad',
	'emergencydesysop-noright' => 'Du har inte tillräckliga rättigheter för att få tillgång till denna sida',
	'right-emergencydesysop' => 'Möjlighet att ömsesidigt avsätta en annan användare',
);

