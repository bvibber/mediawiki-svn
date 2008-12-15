<?php
/**
 * Internationalisation file for extension Patroller.
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0
 */

$messages = array();

/** English
 * @author Rob Church
 */
$messages['en'] = array(
	'patrol' => 'Patrol edits',
	'patrol-desc' => 'Enhanced patrolling interface with workload sharing',
	'patrol-endorse' => 'Endorse',
	'patrol-revert' => 'Revert',
	'patrol-revert-reason' => 'Reason:',
	'patrol-skip' => 'Skip',
	'patrol-reverting' => 'Reverting: $1',
	'patrol-nonefound' => 'No suitable edits could be found for patrolling.',
	'patrol-endorsed-ok' => 'The edit was marked patrolled.',
	'patrol-endorsed-failed' => 'The edit could not be marked patrolled.',
	'patrol-reverted-ok' => 'The edit was reverted.',
	'patrol-reverted-failed' => 'The edit could not be reverted.',
	'patrol-skipped-ok' => 'Ignoring edit.',
	'patrol-reasons' => "* Simple vandalism\n* Newbie test\n* See talk page",
	'patrol-another' => 'Show another edit, if available.',
	'patrol-stopped' => 'You have opted not to patrol another edit. $1',
	'patrol-resume' => 'Click here to resume.',
	'group-patroller' => 'Patrollers',
	'group-patroller-member' => 'Patroller',
	'grouppage-patroller' => '{{ns:project}}:Patroller',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'patrol'                 => 'تعديلات مراجعة',
	'patrol-desc'            => 'واجهة مراجعة محسنة مع تشارك ضغط العمل',
	'patrol-endorse'         => 'تأكيد',
	'patrol-revert'          => 'استرجاع',
	'patrol-revert-reason'   => 'السبب:',
	'patrol-skip'            => 'تجاهل',
	'patrol-reverting'       => 'استرجاع: $1',
	'patrol-nonefound'       => 'لم يتم العثور على تعديلات مناسبة للمراجعة.',
	'patrol-endorsed-ok'     => 'تم تعليم التعديل كمراجع.',
	'patrol-endorsed-failed' => 'هذا التعديل لم يمكن تعليمه كمراجع.',
	'patrol-reverted-ok'     => 'التعديل تم استرجاعه.',
	'patrol-reverted-failed' => 'لم يمكن استرجاع التعديل.',
	'patrol-skipped-ok'      => 'تجاهل التعديل.',
	'patrol-reasons'         => '* تخريب بسيط
* تجربة مستخدم جديد
* انظر صفحة النقاش',
	'patrol-another'         => 'اعرض تعديلا آخر، إن أمكن.',
	'patrol-stopped'         => 'لقد اخترت عدم مراجعة تعديل آخر. $1',
	'patrol-resume'          => 'اضغط هنا للمتابعة.',
	'group-patroller'        => 'مراجعون',
	'group-patroller-member' => 'مراجع',
	'grouppage-patroller'    => '{{ns:project}}:مراجع',
);

$messages['bcl'] = array(
	'patrol-endorse' => 'Iindorso',
	'patrol-revert-reason' => 'Rason:',
	'patrol-skip' => 'Laktawan',
	'patrol-reverting' => 'Pigbabalik: $1',
	'patrol-resume' => 'Lagatik digdi tangarig magpadagos.',
);

/** Bulgarian (Български)
 * @author DCLXVI
 * @author Spiritia
 */
$messages['bg'] = array(
	'patrol-revert'          => 'Възвръщане',
	'patrol-revert-reason'   => 'Причина:',
	'patrol-skip'            => 'Пропускане',
	'patrol-reverting'       => 'Възвръщане: $1',
	'patrol-endorsed-ok'     => 'Редакцията беше отбелязана като проверена.',
	'patrol-endorsed-failed' => 'Редакцията не може да бъде отбелязана като проверена.',
	'patrol-reverted-ok'     => 'Редакцията е възвърната',
	'patrol-reverted-failed' => 'Редакцията не може да бъде възвърната.',
	'patrol-reasons'         => '* Обикновен вандализъм
* Упражнения извън пясъчника
* Виж беседата',
	'patrol-another'         => 'Показване на други евентуални редакции.',
	'group-patroller'        => 'Патрулиращи',
	'group-patroller-member' => 'Патрулиращ',
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'patrol'                 => 'Kontrolle der Bearbeitungen',
	'patrol-desc'            => 'Erweiterte Schnittstelle für die Kontrolle von Bearbeitungen',
	'patrol-endorse'         => 'Bestätigen',
	'patrol-revert'          => 'Zurücksetzen',
	'patrol-revert-reason'   => 'Grund:',
	'patrol-skip'            => 'Überspringen',
	'patrol-reverting'       => 'Zurücksetzen: $1',
	'patrol-nonefound'       => 'Es wurden keine passenden Bearbeitungen zum Kontrollieren gefunden.',
	'patrol-endorsed-ok'     => 'Diese Bearbeitung wurde bereits als kontrolliert markiert.',
	'patrol-endorsed-failed' => 'Diese Bearbeitung kann nicht als kontrolliert markiert werden.',
	'patrol-reverted-ok'     => 'Die Bearbeitung wurde zurückgesetzt.',
	'patrol-reverted-failed' => 'Die Bearbeitung kann nicht zurückgesetzt werden.',
	'patrol-skipped-ok'      => 'Bearbeitung wird ignoriert.',
	'patrol-reasons'         => "* Einfacher Vandalismus\n* Test eines Neulings\n* Siehe Diskussionsseite",
	'patrol-another'         => 'Zeige eine weitere Bearbeitung, sofern verfügbar.',
	'patrol-stopped'         => 'Sie haben entschieden, keine weitere Bearbeitung zu kontrollieren. $1',
	'patrol-resume'          => 'Klicken Sie hier zum fortsetzen.',
	'group-patroller'        => 'Kontrolleure',
	'group-patroller-member' => 'Kontrolleur',
	'grouppage-patroller'    => '{{ns:project}}:Kontrolleur',
);

/** Greek (Ελληνικά)
 * @author Consta
 */
$messages['el'] = array(
	'patrol-revert-reason' => 'Λόγος:',
);

/* Spanish (Titoxd) */
$messages['es'] = array(
	'patrol' => 'Revisar ediciones',
	'patrol-endorse' => 'Aprovar',
	'patrol-revert' => 'Revertir',
	'patrol-revert-reason' => 'Razón:',
	'patrol-skip' => 'Omitir',
	'patrol-reverting' => 'Revirtiendo: $1',
	'patrol-nonefound' => 'No hay ediciones disponibles para revisar.',
	'patrol-endorsed-ok' => 'La edición fue marcada como revisada.',
	'patrol-endorsed-failed' => 'La edición no se pudo marcar como revisada.',
	'patrol-reverted-ok' => 'The edición fue revertida.',
	'patrol-reverted-failed' => 'La edición no pudo ser revertida.',
	'patrol-skipped-ok' => 'Ignorando la edición.',
	'patrol-reasons' => "*Vandalismo simple\n* Prueba de usuario novato\n* Ver la página de discusión",
	'patrol-another' => 'Mostrar otra edición (si disponible).',
	'patrol-stopped' => 'Has optado no marcar otra edición como revisada. $1',
	'patrol-resume' => 'Haz click aquí para continuar.',
);

/* French */
$messages['fr'] = array(
	'patrol' => 'Vérification des modifications',
	'patrol-desc' => 'Interface mettant en valeur le patrouillage collaboratif.',
	'patrol-endorse' => 'Accepter',
	'patrol-revert' => 'Annuler',
	'patrol-revert-reason' => 'Motif :',
	'patrol-skip' => 'Sauter',
	'patrol-reverting' => 'Annulation : $1',
	'patrol-nonefound' => 'Aucune édition suivie n’a pu être trouvée pour la vérification.',
	'patrol-endorsed-ok' => 'L’édition a été marquée comme vérifiée.',
	'patrol-endorsed-failed' => 'L’édition n’a pu être vérifiée.',
	'patrol-reverted-ok' => 'L’édition a été annulée.',
	'patrol-reverted-failed' => 'L’édition n’a pu être annulée.',
	'patrol-skipped-ok' => 'Ignorer l’édition.',
	'patrol-reasons' => "* Simple vandalisme\n* Test de débutant\n* Voir page de discussion",
	'patrol-another' => 'Voir une nouvelle édition, si elle est disponible.',
	'patrol-stopped' => 'Vous avez choisi pour ne plus vérifier une autre édition. $1',
	'patrol-resume' => 'Cliquer ici pour reprendre.',
	'group-patroller' => 'Patrouilleurs',
	'group-patroller-member' => 'Patrouilleur',
	'grouppage-patroller' => '{{ns:project}}:Patrouilleur',
);

/** Galician (Galego)
 * @author Xosé
 * @author Toliño
 * @author Alma
 */
$messages['gl'] = array(
	'patrol'                 => 'Patrullar edicións',
	'patrol-endorse'         => 'Apoiar',
	'patrol-revert'          => 'Desfacer',
	'patrol-revert-reason'   => 'Motivo:',
	'patrol-skip'            => 'Saltar',
	'patrol-reverting'       => 'A desfacer: $1',
	'patrol-nonefound'       => 'Non se atoparon edicións adecuadas para patrullar.',
	'patrol-endorsed-ok'     => 'Marcouse esta edición como patrullada.',
	'patrol-endorsed-failed' => 'Non se puido marcar a edición como patrullada.',
	'patrol-reverted-ok'     => 'Desfíxose esta edición.',
	'patrol-reverted-failed' => 'Non se puido desfacer esta edición.',
	'patrol-skipped-ok'      => 'Ignórase a edición.',
	'patrol-reasons'         => '* Simple vandalismo
* Probas de novato
* Ver a páxina de conversa',
	'patrol-another'         => 'Mostrar outra edición, se existe.',
	'patrol-stopped'         => 'Optou por non patrullar outra edición. $1',
	'patrol-resume'          => 'Prema aquí para recomezar.',
	'group-patroller'        => 'Patrulleiros',
	'group-patroller-member' => 'Patrulleiro',
	'grouppage-patroller'    => '{{ns:project}}:Patrulleiro',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'patrol'                 => 'Dohladowanje změnow',
	'patrol-desc'            => 'Rozšěrjeny kontrolowy interfejs za dźělenje dźěłowych ćežow',
	'patrol-endorse'         => 'Přihłosować',
	'patrol-revert'          => 'Cofnyć',
	'patrol-revert-reason'   => 'Přičina:',
	'patrol-skip'            => 'Přeskočić',
	'patrol-reverting'       => 'Cofnjenje: $1',
	'patrol-nonefound'       => 'Za dohladowanje njebuchu přihódne změny namakane.',
	'patrol-endorsed-ok'     => 'Tuta změna bu jako dohladowana markěrowana.',
	'patrol-endorsed-failed' => 'Tuta změna njemóže so jako dohladowana markěrować.',
	'patrol-reverted-ok'     => 'Změna bu cofnjena.',
	'patrol-reverted-failed' => 'Změna njeda so cofnyć',
	'patrol-skipped-ok'      => 'Změna so ignoruje.',
	'patrol-reasons'         => '* Jednory wandalizm
* Test nowačka
* Hlej diskusijna strona',
	'patrol-another'         => 'Dalšu změnu pokazać, jeli k dispoziciji stejaca.',
	'patrol-stopped'         => 'Sy rozsudźił, zo njeby žane dalše změny dohladował.',
	'patrol-resume'          => 'Klikń sem, zo by pokročował.',
	'group-patroller'        => 'Dohladowarjo',
	'group-patroller-member' => 'Dohladowar',
	'grouppage-patroller'    => '{{ns:project}}:Dohladowar',
);

/** Hungarian (Magyar)
 * @author Bdanee
 */
$messages['hu'] = array(
	'patrol'                 => 'Szerkesztések járőrözése',
	'patrol-endorse'         => 'Jóváhagyás',
	'patrol-revert'          => 'Visszaállítás',
	'patrol-revert-reason'   => 'Ok:',
	'patrol-skip'            => 'Kihagyás',
	'patrol-reverting'       => 'Visszaállítás: $1',
	'patrol-nonefound'       => 'Nincs járőrzésre váró szerkesztés.',
	'patrol-endorsed-ok'     => 'A szerkesztést ellenőrzöttnek jelöltem.',
	'patrol-endorsed-failed' => 'A szerkesztést nem sikerült ellenőrzöttnek jelölnöm.',
	'patrol-reverted-ok'     => 'A szerkesztést visszaállítottam.',
	'patrol-reverted-failed' => 'A szerkesztést nem sikerült visszaállítanom.',
	'patrol-skipped-ok'      => 'Szerkesztés kihagyása.',
	'patrol-reasons'         => '* Egyszerű vandalizmus
* Újonc tesztje
* Lásd a vitalapot',
	'patrol-another'         => 'Mutass más szerkesztést, ha lehetséges.',
	'patrol-stopped'         => 'Nem jelölted be, hogy szeretnél további szerkesztést járőrözni. $1',
	'patrol-resume'          => 'Kattints ide a folytatáshoz.',
	'group-patroller'        => 'járőrök',
	'group-patroller-member' => 'Járőr',
	'grouppage-patroller'    => '{{ns:project}}:Járőr',
);

/* Indonesian (Ivan Lanin) */
$messages['id'] = array(
	'patrol'                 => 'Patroli suntingan',
	'patrol-endorse'         => 'Setujui',
	'patrol-revert'          => 'Kembalikan',
	'patrol-revert-reason'   => 'Alasan:',
	'patrol-skip'            => 'Lewati',
	'patrol-reverting'       => 'Mengembalikan: $1',
	'patrol-nonefound'       => 'Tidak ada suntingan yang dapat dipatroli.',
	'patrol-endorsed-ok'     => 'Suntingan ini ditandai telah dipatroli.',
	'patrol-endorsed-failed' => 'Suntingan ini tidak dapat ditandai telah dipatroli.',
	'patrol-reverted-ok'     => 'Suntingan dikembalikan.',
	'patrol-reverted-failed' => 'Suntingan tidak dapat dikembalikan.',
	'patrol-skipped-ok'      => 'Suntingan diabaikan.',
	'patrol-reasons'         => "* Vandalisme sederhana\n* Pengujian pemula\n* Lihat halaman pembicaraan",
	'patrol-another'         => 'Tampilkan suntingan lain jika tersedia.',
	'patrol-stopped'         => 'Anda telah memilih untuk tidak mengontrol suntingan lain. $1',
	'patrol-resume'          => 'Klik di sini untuk melanjutkan.',
	'group-patroller'        => 'Pematroli',
	'group-patroller-member' => 'Pematroli',
	'grouppage-patroller'    => '{{ns:project}}:Pematroli',
);

/** Icelandic (Íslenska)
 * @author Jóna Þórunn
 */
$messages['is'] = array(
	'patrol-revert'        => 'Taka aftur',
	'patrol-revert-reason' => 'Ástæða:',
	'patrol-skip'          => 'Sleppa',
	'patrol-reverting'     => 'Tek aftur: $1',
	'patrol-skipped-ok'    => 'Hunsa breytingu.',
	'patrol-reasons'       => '* Smávægilegt skemmdarverk
* Tilraun nýliða
* Sjá spjallsíðu',
	'patrol-resume'        => 'Smelltu hér til að halda áfram.',
);

/* Italian (BrokenArrow) */
$messages['it'] = array(
	'patrol' => 'Verifica delle modifiche',
	'patrol-endorse' => 'Approva',
	'patrol-revert' => 'Ripristina',
	'patrol-revert-reason' => 'Motivo:',
	'patrol-skip' => 'Salta',
	'patrol-reverting' => 'Ripristino: $1',
	'patrol-nonefound' => 'Non vi sono modifiche da verificare.',
	'patrol-endorsed-ok' => 'La modifica è stata segnata come verificata.',
	'patrol-endorsed-failed' => 'Impossibile segnare la modifica come verificata.',
	'patrol-reverted-ok' => 'La modifica è stata annullata.',
	'patrol-reverted-failed' => 'Impossibile annullare la modifica.',
	'patrol-skipped-ok' => 'Modifica ignorata.',
	'patrol-reasons' => "* Vandalismo semplice\n* Prova di nuovo utente\n* Vedi pagina di discussione",
	'patrol-another' => 'Mostra un\'altra modifica, se disponibile.',
	'patrol-stopped' => 'Si è scelto di non verificare altre modifiche. $1',
	'patrol-resume' => 'Fare clic qui per riprendere.',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 */
$messages['km'] = array(
	'patrol-revert-reason' => 'ហេតុផល ៖',
);

$messages['la'] = array(
	'patrol-revert-reason' => 'Causa:',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'patrol'               => 'Kontroll vun den Ännerungen',
	'patrol-endorse'       => 'Bestätegen',
	'patrol-revert'        => 'Zrécksetzen',
	'patrol-revert-reason' => 'Grond:',
	'patrol-skip'          => 'Iwwersprangen',
	'patrol-reverting'     => 'Zrécksetzen: $1',
	'patrol-reverted-ok'   => "D'Ännerung gouf zréckgesat.",
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'patrol'                 => 'Wijzigingen controleren',
	'patrol-desc'            => 'Verbeterde interface voor controle van wijzigingen met mogelijkheden om werklast te delen',
	'patrol-endorse'         => 'Goedkeuren',
	'patrol-revert'          => 'Terugdraaien',
	'patrol-revert-reason'   => 'Reden:',
	'patrol-skip'            => 'Overslaan',
	'patrol-reverting'       => '$1 aan het terugdraaien',
	'patrol-nonefound'       => 'Er zijn geen te controleren wijzigingen gevonden.',
	'patrol-endorsed-ok'     => 'De bewerking is gemarkteerd als gecontroleerd.',
	'patrol-endorsed-failed' => 'De bewerking kon niet gemarkeerd worden als gecontroleerd.',
	'patrol-reverted-ok'     => 'De bewerking is teruggedraaid.',
	'patrol-reverted-failed' => 'De bewerking kon niet teruggedraaid worden.',
	'patrol-skipped-ok'      => 'Bewerking wordt genegeerd.',
	'patrol-reasons'         => '* Eenvoudig vandalisme
* Zandbakken
* Zie overlegpagina',
	'patrol-another'         => 'Toon andere bewerking als beschikbaar.',
	'patrol-stopped'         => 'U heeft gekozen geen andere bewerking te controleren. $1',
	'patrol-resume'          => 'Klik hier om verder te gaan.',
	'group-patroller'        => 'Controleurs',
	'group-patroller-member' => 'Controleur',
	'grouppage-patroller'    => '{{ns:project}}:Controleur',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'patrol'                 => 'Patruljer redigeringer',
	'patrol-desc'            => 'Forbedret patruljeringsgrensesnitt med arbeidsfordeling',
	'patrol-endorse'         => 'Godkjenn',
	'patrol-revert'          => 'Tilbakestill',
	'patrol-revert-reason'   => 'Grunn:',
	'patrol-skip'            => 'Hopp over',
	'patrol-reverting'       => 'Tilbakestiller: $1',
	'patrol-nonefound'       => 'Ingen redigeringer passende for patruljering kunne finnes.',
	'patrol-endorsed-ok'     => 'Redigeringen ble merket som patruljert.',
	'patrol-endorsed-failed' => 'Redigeringen kunne ikke merkes som patruljert.',
	'patrol-reverted-ok'     => 'Redigeringen ble tilbakestilt.',
	'patrol-reverted-failed' => 'Redigeringen kunne ikke tilbakestilles.',
	'patrol-skipped-ok'      => 'Ignorerer redigering.',
	'patrol-reasons'         => '* Enkel vandalisme
* Nybegynnertest
* Se diskusjonsside',
	'patrol-another'         => 'Vis en annen redigering, om tilgjenglig.',
	'patrol-stopped'         => 'Du har valgt å ikke patruljere en annen redigering. $1',
	'patrol-resume'          => 'Klikk her for å fortsette.',
	'group-patroller'        => 'Patruljører',
	'group-patroller-member' => 'Patruljør',
	'grouppage-patroller'    => '{{ns:project}}:Patruljør',
);

/* Occitan (Cedric31) */
$messages['oc'] = array(
	'patrol' => 'Verificacion de las modificacions',
	'patrol-endorse' => 'Acceptar',
	'patrol-revert' => 'Anullar',
	'patrol-revert-reason' => 'Motiu:',
	'patrol-skip' => 'Sautar',
	'patrol-reverting' => 'Anullacion: $1',
	'patrol-nonefound' => 'Cap d\'edicion seguida a pas pugut èsser trobada per la verificacion.',
	'patrol-endorsed-ok' => 'L’edicion es estada marcada coma verificada.',
	'patrol-endorsed-failed' => 'L’edicion a pas pogut èsser verificada.',
	'patrol-reverted-ok' => 'L’edicion es estada anullada.',
	'patrol-reverted-failed' => 'L’edicion a pas pogut èsser anullada.',
	'patrol-skipped-ok' => 'Ignorar l’edicion.',
	'patrol-reasons' => '* Simple vandalisme
* Tèst de debutant
* Vejatz pagina de discussion',
	'patrol-another' => 'Veire una edicion novèla, se es disponibla.',
	'patrol-stopped' => 'Avètz causit per verificar pas mai una autra edicion. $1',
	'patrol-resume' => 'Clicar aicí per reprener.',
	'group-patroller' => 'Patrolhaires',
	'group-patroller-member' => 'Patrolhaire',
	'grouppage-patroller' => '{{ns:project}}:Patrolhaire',
);

$messages['pl'] = array(
	'patrol' => 'Patrolowanie edycji',
	'patrol-revert' => 'Cofnij',
	'patrol-revert-reason' => 'Powód:',
	'patrol-skip' => 'Pomiń',
	'patrol-reverting' => 'Wycofywanie: $1',
	'patrol-nonefound' => 'Nie znaleziono edycji do patrolowania.',
	'patrol-endorsed-ok' => 'Edycja została oznaczona jako sprawdzona.',
	'patrol-endorsed-failed' => 'Nie udało się oznaczyć edycji jako sprawdzonej.',
	'patrol-reverted-ok' => 'Edycja została cofnięta.',
	'patrol-reverted-failed' => 'Nie udało się cofnąć edycji.',
	'patrol-skipped-ok' => 'Ignorowanie edycji.',
	'patrol-reasons' => '* Prosty wandalizm * Test * Zobacz stronę dyskusji',
	'patrol-another' => 'Pokaż inną edycję, jeśli istnieje.',
	'patrol-stopped' => 'Wybrano, by nie patrolować więcej edycji. $1',
	'patrol-resume' => 'Kliknij tu by kontynuować',
	'group-patroller' => 'Patrolujący',
	'group-patroller-member' => 'Patrolujący',
	'grouppage-patroller' => '{{ns:project}}:Patrolujący',
);

/* Piedmontese (Bèrto 'd Sèra) */
$messages['pms'] = array(
	'patrol' => 'Patojé le modìfiche',
	'patrol-endorse' => 'Aprové',
	'patrol-revert' => 'Buté com a l\'era',
	'patrol-revert-reason' => 'Rason:',
	'patrol-skip' => 'Sàota',
	'patrol-reverting' => 'Ën tirand andré: $1',
	'patrol-nonefound' => 'Gnun-a modìfica da patojé.',
	'patrol-endorsed-ok' => 'La modìfica a l\'é staita marcà "patojà".',
	'patrol-endorsed-failed' => 'La modìfica a l\'é nen podusse marché coma "patojà"',
	'patrol-reverted-ok' => 'La modìfica a l\'é staita tirà andré com a l\'era.',
	'patrol-reverted-failed' => 'Sta modìfica-sì as peul pa buté andré com a l\'era.',
	'patrol-skipped-ok' => 'Lassa beuje la modìfica.',
	'patrol-reasons' => '* vandalism sempi
* preuva ëd n\'utent neuv
* ch\'a varda la pàgina ëd discussion',
	'patrol-another' => 'Smon n\'àotra modìfica, s\'a-i n\'a-i é un-a da smon-e.',
	'patrol-stopped' => 'A l\'ha sërnù dë nen patojé n\'àotra modìfica. $1',
	'patrol-resume' => 'Ch\'a bata ambelessì për andé anans da andova a l\'era.',
	'group-patroller' => 'Gent ëd patoja',
	'group-patroller-member' => 'ëd patoja',
	'grouppage-patroller' => '{{ns:project}}:patoja',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'patrol-endorse'         => 'Aprovar',
	'patrol-revert'          => 'Reverter',
	'patrol-revert-reason'   => 'Motivo:',
	'patrol-skip'            => 'Saltar',
	'patrol-reverting'       => 'Revertendo: $1',
	'group-patroller'        => 'Patrulhadores',
	'group-patroller-member' => 'Patrulhador',
	'grouppage-patroller'    => '{{ns:project}}:Patrulhador',
);

$messages['rm'] = array(
	'patrol-revert-reason' => 'Motiv:',
);

/** Russian (Русский)
 * @author Illusion
 * @author .:Ajvol:.
 */
$messages['ru'] = array(
	'patrol'                 => 'Патрулирование',
	'patrol-endorse'         => 'Подтвердить',
	'patrol-revert'          => 'Откатить',
	'patrol-revert-reason'   => 'Причина:',
	'patrol-skip'            => 'Отложить',
	'patrol-reverting'       => 'Откат: $1',
	'patrol-nonefound'       => 'Невозможно найти соответствующих правок для патрулирования.',
	'patrol-endorsed-ok'     => 'Правка подтверждена.',
	'patrol-endorsed-failed' => 'Не удалось подтвердить правку.',
	'patrol-reverted-ok'     => 'Правка откачена.',
	'patrol-reverted-failed' => 'Не удалось откатить правку.',
	'patrol-skipped-ok'      => 'Правка отложена.',
	'patrol-reasons'         => '* Просто вандализм
* Тест новичка
* См. страницу обсуждения',
	'patrol-another'         => 'Вывести ещё одну правку, если имеется.',
	'patrol-stopped'         => 'Вы решили не патрулировать другую правку. $1',
	'patrol-resume'          => 'Нажмите здесь для продолжения.',
	'group-patroller'        => 'Патрулирующие',
	'group-patroller-member' => 'патрулирующий',
	'grouppage-patroller'    => '{{ns:project}}:Патрулирование',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'patrol'                 => 'Strážiť úpravy',
	'patrol-desc'            => 'Rozšírené rozhranie na stráženie s rozkladaním pracovnej záťaže',
	'patrol-endorse'         => 'Schváliť',
	'patrol-revert'          => 'Vrátiť',
	'patrol-revert-reason'   => 'Dôvod:',
	'patrol-skip'            => 'Preskočiť',
	'patrol-reverting'       => 'Vraciam: $1',
	'patrol-nonefound'       => 'Neboli nájdené vhodné úpravy na stráženie.',
	'patrol-endorsed-ok'     => 'Úprava bola označená ako strážená.',
	'patrol-endorsed-failed' => 'Úpravu nebolo možné označiť ako stráženú',
	'patrol-reverted-ok'     => 'Úprava bola vrátená.',
	'patrol-reverted-failed' => 'Úpravu nebolo možné vrátiť.',
	'patrol-skipped-ok'      => 'Ignorujem úpravu.',
	'patrol-reasons'         => '* Jednoduchý vandaizmus
* Nováčikovský test
* Pozri diskusiu',
	'patrol-another'         => 'Zobraz ďalšiu úpravu, ak existuje.',
	'patrol-stopped'         => 'Zvolili ste nestrážiť ďalšiu úpravu. $1',
	'patrol-resume'          => 'Kliknutím sem pokračujte.',
	'group-patroller'        => 'Strážcovia',
	'group-patroller-member' => 'Strážca',
	'grouppage-patroller'    => '{{ns:project}}:Strážca',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'patrol'                 => 'Kontrolle fon do Beoarbaidengen',
	'patrol-endorse'         => 'Bestäätigje',
	'patrol-revert'          => 'Touräächsätte',
	'patrol-revert-reason'   => 'Gruund:',
	'patrol-skip'            => 'Uurspringe',
	'patrol-reverting'       => 'Touräächsätte: $1',
	'patrol-nonefound'       => 'Der wuuden neen paasjende Beoarbaidengen toun Kontrollierjen fuunen.',
	'patrol-endorsed-ok'     => 'Disse Beoarbaidenge wuude al as kontrollierd markierd.',
	'patrol-endorsed-failed' => 'Disse Beoarbaidenge kon nit as kontrollierd markierd wäide.',
	'patrol-reverted-ok'     => 'Ju Beoarbaidenge wuude touräächsät.',
	'patrol-reverted-failed' => 'Ju Beoarbaidenge kon nit touräächsät wäide.',
	'patrol-skipped-ok'      => 'Beoarbaidenge wäd ignorierd.',
	'patrol-reasons'         => '* Eenfacher Vandalismus
* Test fon n Näiling
* Sjuch Diskussionssiede',
	'patrol-another'         => 'Wies ne wiedere Beoarbaidenge, sofier ferföichboar.',
	'patrol-stopped'         => 'Jie hääbe äntscheeden, neen wiedere Beoarbaidenge tou kontrollierjen. $1',
	'patrol-resume'          => 'Klik hier toun Foutsätten.',
	'group-patroller'        => 'Kontrolleure',
	'group-patroller-member' => 'Kontrolleur',
	'grouppage-patroller'    => '{{ns:project}}:Kontrolleur',
);

/* Sundanese (Kandar via BetaWiki) */
$messages['su'] = array(
	'patrol' => 'Ronda éditan',
	'patrol-endorse' => 'Widian',
	'patrol-revert' => 'Balikkeun',
	'patrol-revert-reason' => 'Alesan:',
	'patrol-skip' => 'Liwat',
	'patrol-reverting' => 'Malikkeun: $1',
	'patrol-nonefound' => 'Euweuh éditan nu perlu dironda.',
	'patrol-endorsed-ok' => 'Éditan geus ditandaan geus dironda.',
	'patrol-endorsed-failed' => 'Éditan teu bisa dicirian geus dironda.',
	'patrol-reverted-ok' => 'Éditan geus dibalikkeun.',
	'patrol-reverted-failed' => 'Éditanana teu bisa dibalikkeun.',
	'patrol-skipped-ok' => 'Ngantepkeun éditan.',
	'patrol-reasons' => '* Vandalismeu basajan
* Uji coba nu anyar datang
* Tempo kaca obrolan',
	'patrol-another' => 'Mun bisa, témbongkeun éditan lianna.',
	'patrol-stopped' => 'Anjeun geus milih teu ngaronda éditan séjén. $1',
	'patrol-resume' => 'Klik di dieu pikeun neruskeun.',
	'group-patroller' => 'Nu ngaronda',
	'group-patroller-member' => 'Nu ngaronda',
	'grouppage-patroller' => '{{ns:project}}:Nu ngaronda',
);

/** Swedish (Svenska)
 * @author M.M.S.
 */
$messages['sv'] = array(
	'patrol-revert-reason'   => 'Anledning:',
	'patrol-another'         => 'Visa andra redigeringar, om det är tillgängligt.',
	'group-patroller'        => 'Patrullerare',
	'group-patroller-member' => 'Patrullerare',
	'grouppage-patroller'    => '{{ns:project}}:Patrullerare',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'patrol-revert-reason' => 'కారణం:',
	'patrol-resume'        => 'కొనసాగించడానికి ఇక్కడ నొక్కండి.',
);

/* Cantonese (Hillgentleman, Shinjiman) */
$messages['yue'] = array(
	'patrol' => '巡啲編輯',
	'patrol-endorse' => '簽署支持',
	'patrol-revert' => '打回頭',
	'patrol-revert-reason' => '理由:',
	'patrol-skip' => '跳',
	'patrol-reverting' => '打回頭：$1',
	'patrol-nonefound' => '揾唔到好嘅版本來簽。',
	'patrol-endorsed-ok' => '嘜咗哩版做「巡過」',
	'patrol-endorsed-failed' => '哩版唔嘜得做「巡過」。',
	'patrol-reverted-ok' => '打咗版嘢回頭。',
	'patrol-reverted-failed' => '打唔到哩版嘢回頭。',
	'patrol-skipped-ok' => '唔理哩次編輯。',
	'patrol-reasons' => "* 塗鴉\n* 生手試玩\n* 見討論頁",
	'patrol-another' => '試揾另一版睇吓。',
	'patrol-stopped' => '你决定唔再巡。 $1',
	'patrol-resume' => '撳呢度繼續。',
	'group-patroller' => '巡邏員',
	'group-patroller-member' => '巡邏員',
	'grouppage-patroller' => '{{ns:project}}:巡邏員',
);

/* Chinese (Simplified) (下一次登录) */
$messages['zh-hans'] = array(
	'patrol' => '编辑巡逻',
	'patrol-endorse' => '批注',
	'patrol-revert' => '回退',
	'patrol-revert-reason' => '原因：',
	'patrol-skip' => '跳过',
	'patrol-reverting' => '正在回退：$1',
	'patrol-nonefound' => '没有找到可巡逻编辑。',
	'patrol-endorsed-ok' => '此修改已标记巡逻记录。',
	'patrol-endorsed-failed' => '此修改无法标记巡逻记录。',
	'patrol-reverted-ok' => '编辑被回退。',
	'patrol-reverted-failed' => '编辑不能回退。',
	'patrol-skipped-ok' => '忽略编辑。',
	'patrol-reasons' => "* 纯粹破坏\n* 新手测试\n* 参见讨论页",
	'patrol-another' => '显示其他存在的编辑。',
	'patrol-stopped' => '您选择不巡逻其他编辑。$1',
	'patrol-resume' => '点击这里继续。',
	'group-patroller' => '巡逻员',
	'group-patroller-member' => '巡逻员',
	'grouppage-patroller' => '{{ns:project}}:巡逻员',
);

/* Chinese (Traditional) (KilluaZaoldyeck, Shinjiman) */
$messages['zh-hant'] = array(
	'patrol' => '覆查編輯',
	'patrol-endorse' => '批註',
	'patrol-revert' => '回退',
	'patrol-revert-reason' => '原因：',
	'patrol-skip' => '略過',
	'patrol-reverting' => '正在回退：$1',
	'patrol-nonefound' => '沒有覆查修改的記錄。',
	'patrol-endorsed-ok' => '此編輯已標記覆查記錄。',
	'patrol-endorsed-failed' => '此編輯無法標記覆查記錄。',
	'patrol-reverted-ok' => '編輯被回退。',
	'patrol-reverted-failed' => '編輯無法回退。',
	'patrol-skipped-ok' => '忽略編輯。',
	'patrol-reasons' => "* 純粹破壞\n* 新人測試\n* 參看討論頁",
	'patrol-another' => '列出其他存在的編輯。',
	'patrol-stopped' => '你選擇不覆查其他編輯。$1',
	'patrol-resume' => '點擊這裡繼續。',
	'group-patroller' => '巡邏員',
	'group-patroller-member' => '巡邏員',
	'grouppage-patroller' => '{{ns:project}}:巡邏員',
);

