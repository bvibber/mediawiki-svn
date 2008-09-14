<?php
/**
 * Internationalisation file for the RefreshSpecial extension.
 *
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Bartek Łapiński
 */
$messages['en'] = array (
	'refreshspecial' => 'Refresh special pages',
	'refreshspecial-desc' => 'Allows [[Special:RefreshSpecial|manual special page refresh]] of special pages',
	'refreshspecial-title' => 'Refresh special pages',
	'refreshspecial-help' =>  'This special page provides means to manually refresh special pages. When you have chosen all pages that you want to refresh, click on the Refresh button below to make it go. Warning: the refresh may take a while on larger wikis.',
	'refreshspecial-button' => 'Refresh selected',
	'refreshspecial-fail' => 'Please check at least one special page to refresh.',
	'refreshspecial-refreshing' => 'refreshing special pages',
	'refreshspecial-skipped' => 'cheap, skipped',
	'refreshspecial-success-subtitle' => 'refreshing special pages',
	'refreshspecial-choice' => 'refreshing special pages',
	'refreshspecial-js-disabled' => '(<i>You cannot select all pages when JavaScript is disabled</i>)',
	'refreshspecial-select-all-pages' => ' select all pages ',
	'refreshspecial-link-back' => 'Go back to extension ',
	'refreshspecial-here' => '<b>here</b>',
	'refreshspecial-none-selected' => 'You have not selected any special pages. Reverting to default selection.',
	'refreshspecial-db-error' => 'Failed: database error',
	'refreshspecial-no-page' => 'No such special page',
	'refreshspecial-slave-lagged' => 'Slave lagged, waiting...',
	'refreshspecial-reconnected' => 'Reconnected.',
	'refreshspecial-reconnecting' => 'Connection failed, reconnecting in 10 seconds...',
	'refreshspecial-total-display' => '<br />Refreshed $1 pages totaling $2 rows in time $3 (complete time of the script run is $4)',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'refreshspecial' => 'تحديث الصفحات الخاصة',
	'refreshspecial-desc' => 'يسمح [[Special:RefreshSpecial|بتحديث يدوي]] للصفحات الخاصة',
	'refreshspecial-title' => 'تحديث الصفحات الخاصة',
	'refreshspecial-help' => 'هذه الصفحة الخاصة توفر الوسيلة لتحديث الصفحات الخاصة يدويا. عندما تختار كل الصفحات التي تريد تحديثها، اضغط على زر التحديث بالأسفل للبدء. تحذير: التحديث ربما يأخذ وقتا في الويكيات الكبيرة.',
	'refreshspecial-button' => 'تحديث المختارة',
	'refreshspecial-fail' => 'من فضلك علم على صفحة خاصة واحدة على الأقل للتحديث.',
	'refreshspecial-refreshing' => 'جاري تحديث الصفحات الخاصة',
	'refreshspecial-skipped' => 'رخيصة، تم تجاوزها',
	'refreshspecial-success-subtitle' => 'جاري تحديث الصفحات الخاصة',
	'refreshspecial-choice' => 'تحديث الصفحات الخاصة',
	'refreshspecial-js-disabled' => '(<i>أنت لا يمكنك اختيار كل الصفحات عندما تكون الجافاسكريبت معطلة</i>)',
	'refreshspecial-select-all-pages' => '  اختر كل الصفحات',
	'refreshspecial-link-back' => 'رجوع إلى الامتداد',
	'refreshspecial-here' => '<b>هنا</b>',
	'refreshspecial-none-selected' => 'أنت لم تختر أي صفحة خاصة. استرجاع إلى الاختيار الافتراضي.',
	'refreshspecial-db-error' => 'فشل: خطأ قاعدة بيانات',
	'refreshspecial-no-page' => 'لا توجد صفحة خاصة كهذه',
	'refreshspecial-slave-lagged' => 'الخادم التابع تأخر، جاري الانتظار...',
	'refreshspecial-reconnected' => 'تمت إعادة التوصيل.',
	'refreshspecial-reconnecting' => 'التوصيل فشل، إعادة التوصيل خلال 10 ثواني...',
	'refreshspecial-total-display' => '<br />حدث $1 صفحة بإجمالي $2 صف في وقت $3 (الزمن الإجمالي لعمل السكريبت هو $4)',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Meno25
 */
$messages['arz'] = array(
	'refreshspecial' => 'تحديث الصفحات الخاصة',
	'refreshspecial-desc' => 'يسمح [[Special:RefreshSpecial|بتحديث يدوي]] للصفحات الخاصة',
	'refreshspecial-title' => 'تحديث الصفحات الخاصة',
	'refreshspecial-help' => 'هذه الصفحة الخاصة توفر الوسيلة لتحديث الصفحات الخاصة يدويا. عندما تختار كل الصفحات التى تريد تحديثها، اضغط على زر التحديث بالأسفل للبدء. تحذير: التحديث ربما يأخذ وقتا فى الويكيات الكبيرة.',
	'refreshspecial-button' => 'تحديث المختارة',
	'refreshspecial-fail' => 'من فضلك علم على صفحة خاصة واحدة على الأقل للتحديث.',
	'refreshspecial-refreshing' => 'جارى تحديث الصفحات الخاصة',
	'refreshspecial-skipped' => 'رخيصة، تم تجاوزها',
	'refreshspecial-success-subtitle' => 'جارى تحديث الصفحات الخاصة',
	'refreshspecial-choice' => 'تحديث الصفحات الخاصة',
	'refreshspecial-js-disabled' => '(<i>أنت لا يمكنك اختيار كل الصفحات عندما تكون الجافاسكريبت معطلة</i>)',
	'refreshspecial-select-all-pages' => '  اختر كل الصفحات',
	'refreshspecial-link-back' => 'رجوع إلى الامتداد',
	'refreshspecial-here' => '<b>هنا</b>',
	'refreshspecial-none-selected' => 'أنت لم تختر أى صفحة خاصة. استرجاع إلى الاختيار الافتراضي.',
	'refreshspecial-db-error' => 'فشل: خطأ قاعدة بيانات',
	'refreshspecial-no-page' => 'لا توجد صفحة خاصة كهذه',
	'refreshspecial-slave-lagged' => 'الخادم التابع تأخر، جارى الانتظار...',
	'refreshspecial-reconnected' => 'تمت إعادة التوصيل.',
	'refreshspecial-reconnecting' => 'التوصيل فشل، إعادة التوصيل خلال 10 ثواني...',
	'refreshspecial-total-display' => '<br />حدث $1 صفحة بإجمالى $2 صف فى وقت $3 (الزمن الإجمالى لعمل السكريبت هو $4)',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'refreshspecial-select-all-pages' => ' selekti ĉiujn paĝojn',
);

/** Finnish (Suomi)
 * @author Jack Phoenix
 */
$messages['fi'] = array(
	'refreshspecial' => 'Päivitä toimintosivuja',
	'refreshspecial-title' => 'Päivitä toimintosivuja',
	'refreshspecial-help' =>  'Tämä toimintosivu tarjoaa keinoja päivittää toimintosivuja manuaalisesti. Kun olet valinnut kaikki sivut, jotka haluat päivittää, napsauta "Päivitä"-nappia alapuolella päivittääksesi valitut. Varoitus: päivittäminen saattaa kestää jonkin aikaa isommissa wikeissä.',
	'refreshspecial-button' => 'Päivitä valitut',
	'refreshspecial-fail' => 'Valitse ainakin yksi päivitettävä toimintosivu.',
	'refreshspecial-refreshing' => 'päivitetään toimintosivuja',
	'refreshspecial-skipped' => 'halpa, ohitettu',
	'refreshspecial-success-subtitle' => 'päivitetään toimintosivuja',
	'refreshspecial-choice' => 'päivitetään toimintosivuja',
	'refreshspecial-js-disabled' => '(<i>Et voi valita kaikkia sivuja kun JavaScript on pois käytöstä</i>)',
	'refreshspecial-select-all-pages' => ' valitse kaikki sivut ',
	'refreshspecial-link-back' => 'Palaa lisäosaan ',
	'refreshspecial-here' => '<b>täällä</b>',
	'refreshspecial-none-selected' => 'Et ole valinnut yhtään toimintosivua. Palataan oletusasetuksiin.',
	'refreshspecial-db-error' => 'EPÄONNISTUI: tietokantavirhe',
	'refreshspecial-no-page' => 'Kyseistä toimintosivua ei ole',
	'refreshspecial-reconnected' => 'Yhdistetty uudelleen.',
	'refreshspecial-reconnecting' => 'Yhteys epäonnistui, yritetään uudelleen 10 sekunnin kuluttua...',
	'refreshspecial-total-display' => '<br />Päivitettiin $1 sivua; yhteensä $2 riviä ajassa $3 (yhteensä skriptin suorittamiseen meni aikaa $4)',
);

/** French (Français)
 * @author Grondin
 * @author Zetud
 */
$messages['fr'] = array(
	'refreshspecial' => 'Rafraichir les pages spéciales',
	'refreshspecial-desc' => 'Permet [[Special:RefreshSpecial|l’actualisation manuelle]] des pages spéciales',
	'refreshspecial-title' => 'Rafraichir les pages spéciales',
	'refreshspecial-help' => 'Cette page spéciale fournit les moyens de rafraichir manuellement les pages spéciales. Quand vous avez choisi toutes les pages que vous voulez actualiser, cliquer sur le bouton Actualiser ci-dessous pour lancer la procédure. Attention : l’actualisation peut prendre un certain temps sur des wikis disposant d’une grande taille.',
	'refreshspecial-button' => 'Actualisation sélectionnée',
	'refreshspecial-fail' => 'Veuillez cocher au moins une page spéciale à rafraichir.',
	'refreshspecial-refreshing' => 'Actualisation des pages spéciales',
	'refreshspecial-skipped' => 'superficiel, sauté',
	'refreshspecial-success-subtitle' => 'actualisation des pages spéciales',
	'refreshspecial-choice' => 'actualisation des pages spéciales',
	'refreshspecial-js-disabled' => '(<i>Vous ne pouvez sélectionnez toutes les pages quand JavaScript est désactivé</i>)',
	'refreshspecial-select-all-pages' => 'sélectionner toutes les pages',
	'refreshspecial-link-back' => 'Revenir à l’extension',
	'refreshspecial-here' => '<b>ici</b>',
	'refreshspecial-none-selected' => 'Vous n’avez pas sélectionné de pages spéciales. Retour vers la sélection par défaut.',
	'refreshspecial-db-error' => 'Échec : erreur de la base de données',
	'refreshspecial-no-page' => 'Aucune page spéciale',
	'refreshspecial-slave-lagged' => 'Travail retardé, en cours…',
	'refreshspecial-reconnected' => 'Reconnecté.',
	'refreshspecial-reconnecting' => 'Échec de la connection, reconnection dans 10 secondes…',
	'refreshspecial-total-display' => '<br />$1 {{PLURAL:$1|page actualisée|pages actualisées}} totalisant $2 {{PLURAL:$2|ligne|lignes}} sur une durée de $3 (la durée complète de l’action du script est de $4)',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'refreshspecial' => 'Refrescar a páxina especial',
	'refreshspecial-desc' => 'Permite [[Special:RefreshSpecial|refrescar páxinas especiais manualmente]]',
	'refreshspecial-title' => 'Refrescar as páxinas especiais',
	'refreshspecial-help' => 'Esta páxina especial proporciona medios para refrescar manualmente as páxinas especiais. Cando escolla todas as páxinas que quere refrescar, prema no botón "Refrescar" para levar a cabo a acción. Aviso: o refrescado pode levar uns intres nos wikis grandes.',
	'refreshspecial-button' => 'Actualizar o seleccionado',
	'refreshspecial-fail' => 'Por favor, comprobe polo menos unha páxina especial para refrescar.',
	'refreshspecial-refreshing' => 'actualizando as páxinas especiais',
	'refreshspecial-skipped' => 'superficial, saltado',
	'refreshspecial-success-subtitle' => 'actualizando as páxinas especiais',
	'refreshspecial-choice' => 'actualizando as páxinas especiais',
	'refreshspecial-js-disabled' => '(<i>Non pode seleccionar todas as páxinas cando o JavaScript está deshabilitado</i>)',
	'refreshspecial-select-all-pages' => '  seleccionar todas as páxinas',
	'refreshspecial-link-back' => 'Voltar á extensión',
	'refreshspecial-here' => '<b>aquí</b>',
	'refreshspecial-none-selected' => 'Non seleccionou ningunha páxina especial. Revertendo á selección por defecto.',
	'refreshspecial-db-error' => 'Fallou: erro da base de datos',
	'refreshspecial-no-page' => 'Non existe tal páxina especial',
	'refreshspecial-slave-lagged' => 'Intervalo de retraso, agardando...',
	'refreshspecial-reconnected' => 'Reconectado.',
	'refreshspecial-reconnecting' => 'Fallou a conexión, reconectando en 10 segundos...',
	'refreshspecial-total-display' => '<br />$1 páxinas refrescadas dun total de $2 liñas dunha duración de $3 (a duración completa  da escritura é de $4)',
);

/** Italian (Italiano)
 * @author Darth Kule
 */
$messages['it'] = array(
	'refreshspecial' => 'Aggiorna pagine speciali',
	'refreshspecial-desc' => "Permette l'[[Special:RefreshSpecial|aggiornamento manuale]] di pagine speciali",
	'refreshspecial-title' => 'Aggiorna pagine speciali',
	'refreshspecial-help' => "Questa pagina speciale permette di aggiornare manualmente le pagine speciali. Quando hai scelto tutte le pagine che vuoi aggiornare, fai clic sul pulsante Aggiorna per effettuarlo. Attenzione: l'aggiornamento potrebbe richiedere un po' di tempo sulle wiki più grandi.",
	'refreshspecial-button' => 'Aggiorna pagine selezionate',
	'refreshspecial-fail' => 'Seleziona almeno una pagina speciale da aggiornare.',
	'refreshspecial-refreshing' => 'aggiornamento pagine speciali',
	'refreshspecial-success-subtitle' => 'aggiornamento pagine speciali',
	'refreshspecial-choice' => 'aggiornamento pagine speciali',
	'refreshspecial-js-disabled' => '(<i>Non è possibile selezionare tutte le pagine se JavaScript è disattivato</i>)',
	'refreshspecial-select-all-pages' => 'seleziona tutte le pagine',
	'refreshspecial-link-back' => "Torna all'estensione",
	'refreshspecial-here' => '<b>qui</b>',
	'refreshspecial-none-selected' => 'Non è stata selezionata alcuna pagina speciale. Ripristino alla selezione di default.',
	'refreshspecial-db-error' => 'Fallito: errore del database',
	'refreshspecial-no-page' => 'Pagina speciale inesistente',
	'refreshspecial-reconnected' => 'Riconnesso.',
	'refreshspecial-reconnecting' => 'Connessione fallita, prossimo tentativo fra 10 secondi...',
	'refreshspecial-total-display' => '<br />$1 pagine aggiornate per un totale di $2 linee in un tempo di $3 (il tempo totale di esecuzione dello script è di $4)',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'refreshspecial' => 'Spezialsäiten aktualiséieren',
	'refreshspecial-desc' => 'Erlaabt et [[Special:RefreshSpecial|manuell Aktualiséierung]] vu Spezialsäiten',
	'refreshspecial-title' => 'Spezialsäiten aktualiséieren',
	'refreshspecial-button' => 'Déi gewielten aktualiséieren',
	'refreshspecial-fail' => "Wielt mindestens eng Spezialsäit aus fir z'aktualiséieren.",
	'refreshspecial-refreshing' => 'Spezialsäiten aktualiséieren',
	'refreshspecial-skipped' => 'bëlleg, iwwersprong',
	'refreshspecial-success-subtitle' => 'Aktualisatioun vu Spezialsäiten',
	'refreshspecial-choice' => 'Aktualisatioun vu Spezialsäiten',
	'refreshspecial-js-disabled' => "(<i>dir kënnt net all d'Säiten auswielen, wa JavaScript ausgeschalt ass</i>)",
	'refreshspecial-select-all-pages' => 'all Säiten auswielen',
	'refreshspecial-link-back' => "Zréck op d'Erweiderung",
	'refreshspecial-here' => '<b>hei</b>',
	'refreshspecial-none-selected' => "Dir hutt keng Spezialssäiten ausgewielt. Zrèck op d'Astellung 'par défaut'",
	'refreshspecial-db-error' => 'Et geet net: Feeler vun der Datebank',
	'refreshspecial-no-page' => 'Et gëtt keng esou Spezialsäit',
	'refreshspecial-slave-lagged' => 'Aarbecht déi nach usteet, an der Maach ...',
	'refreshspecial-reconnected' => 'Nees verbonn',
	'refreshspecial-reconnecting' => "D'Verbindung koum net zustan, nei Verbindung an 10 Sekonnen ...",
	'refreshspecial-total-display' => "<br />$1 Säite mat am Ganzen $2 Reien an $3 (Dauer) (d'Gesamtzäit déi de Script brauch ass $4)",
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'refreshspecial' => "Speciale pagina's verversen",
	'refreshspecial-desc' => "Maakt het mogelijk om [[Special:RefreshSpecial|handmatig speciale pagina's te verversen]]",
	'refreshspecial-title' => "Speciale pagina's verversen",
	'refreshspecial-help' => "Via deze pagina kunt u speciale pagina's handmatig verversen.
Nadat u alle gewenste pagina's hebt aangevinkt, kunt u 'Verversen' kiezen.
Waarschuwing: op grotere wiki's kan dit enige tijd duren.",
	'refreshspecial-button' => "Geselecteerde pagina's verversen",
	'refreshspecial-fail' => 'Vink tenminste één te verversen pagina aan.',
	'refreshspecial-refreshing' => "bezig met het verversen van speciale pagina's",
	'refreshspecial-skipped' => 'goedkoop, overgeslagen',
	'refreshspecial-success-subtitle' => "bezig met het verversen van speciale pagina's",
	'refreshspecial-choice' => "bezig met het verversen van speciale pagina's",
	'refreshspecial-js-disabled' => "(<i>U kunt alle pagina's niet selecteren als JavaScript is uitgeschakeld</i>)",
	'refreshspecial-select-all-pages' => "alle pagina's selecteren",
	'refreshspecial-link-back' => 'Terug naar uitbreiding',
	'refreshspecial-here' => '<b>hier</b>',
	'refreshspecial-none-selected' => "U hebt geen speciale pagina's geselecteerd.
De standaardinstellingen zijn hersteld.",
	'refreshspecial-db-error' => 'Fout: databasefout',
	'refreshspecial-no-page' => 'De speciale pagina bestaat niet',
	'refreshspecial-slave-lagged' => 'Slaveserver loopt achter. Bezig met wachten...',
	'refreshspecial-reconnected' => 'Weer verbonden.',
	'refreshspecial-reconnecting' => 'Verbinding kon niet gemaakt worden.
Over 10 seconden wordt weer geprobeerd verbinding te maken...',
	'refreshspecial-total-display' => "<br />Er zijn $1 pagina's ververst met $2 regels in $3 tijd (totale duur van de verwerking was $4)",
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'refreshspecial' => 'Refrescar las paginas especialas',
	'refreshspecial-desc' => 'Permet [[Special:RefreshSpecial|l’actualizacion de las paginas especialas del manual]] de las paginas concernidas',
	'refreshspecial-title' => 'Refrescar las paginas especialas',
	'refreshspecial-help' => 'Aquesta pagina especiala provesís los mejans de refrescar manualament las paginas especialas. Quand avètz causit totas las paginas que volètz actualizar, clicatz sul bouton Actualizar çaijós per aviar la procedura. Atencion : l’actualizacion pòt préne un cèrt temps sus de wikis que dispausan d’una talha bèla.',
	'refreshspecial-button' => 'Actualizacion seleccionada',
	'refreshspecial-fail' => 'Marcatz al mens una pagina especiala de refrescar.',
	'refreshspecial-refreshing' => 'Actualizacion de las paginas especialas',
	'refreshspecial-skipped' => 'superficial, sautat',
	'refreshspecial-success-subtitle' => 'actualizacion de las paginas especialas',
	'refreshspecial-choice' => 'actualizacion de las paginas especialas',
	'refreshspecial-js-disabled' => '(<i>Podètz pas seleccionar totas las paginas quand JavaScript es desactivat</i>)',
	'refreshspecial-select-all-pages' => 'seleccionar totas las paginas',
	'refreshspecial-link-back' => 'Tornar a l’extension',
	'refreshspecial-here' => '<b>aicí</b>',
	'refreshspecial-none-selected' => 'Avètz pas seleccionat cap de paginas especialas. Retorn cap a la seleccion per defaut.',
	'refreshspecial-db-error' => 'Fracàs : error de la banca de donada',
	'refreshspecial-no-page' => 'Pas cap de pagina especiala',
	'refreshspecial-slave-lagged' => 'Trabalh retardat, en cors…',
	'refreshspecial-reconnected' => 'Reconnectat.',
	'refreshspecial-reconnecting' => 'Fracàs de la connexion, reconnexion dins 10 segondas…',
	'refreshspecial-total-display' => "<br />$1 {{PLURAL:$1|pagina actualizada|paginas actualizadas}} totalizant $2 {{PLURAL:$2|linha|linhas}} sus una durada de $3 (la durada completa de l’accion de l'escript es de $4)",
);

/** Polish (Polski)
 * @author Jwitos
 */
$messages['pl'] = array(
	'refreshspecial-here' => '<b>tutaj</b>',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'refreshspecial' => 'Refrescar páginas especiais',
	'refreshspecial-desc' => 'Permite o [[Special:RefreshSpecial|refrescamento manual]] das páginas especiais',
	'refreshspecial-title' => 'Refrescar páginas especiais',
	'refreshspecial-help' => 'Esta página especial providencia forma de refrescar páginas especiais. Quando tiver escolhido todas as páginas que pretende refrescar, clique no botão Refrescar abaixo para iniciar o processo. Aviso: o refrescamento pode demorar bastante tempo em wikis grandes.',
	'refreshspecial-fail' => 'Por favor, seleccione pelo menos uma página especial para refrescar.',
	'refreshspecial-js-disabled' => '(<i>Não pode seleccionar todas as páginas quando o JavaScript está desactivado</i>)',
	'refreshspecial-link-back' => 'Voltar à extensão',
	'refreshspecial-here' => '<b>aqui</b>',
	'refreshspecial-db-error' => 'Falha: erro de base de dados',
	'refreshspecial-no-page' => 'Página especial inexistente',
	'refreshspecial-slave-lagged' => 'Servidor escravo com atraso, aguardando...',
	'refreshspecial-reconnected' => 'Reconectado.',
	'refreshspecial-reconnecting' => 'Conexão falhada, reconectando em 10 segundos...',
	'refreshspecial-total-display' => '<br />$1 páginas refrescadas, totalizando $2 linhas em tempo $3 (tempo total de execução do script é $4)',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'refreshspecial' => 'Obnoviť špeciálne stránky',
	'refreshspecial-desc' => 'Umožňuje manuálne [[Special:RefreshSpecial|obnovenie špeciálnych stránok]]',
	'refreshspecial-title' => 'Obnoviť špeciálne stránky',
	'refreshspecial-help' => 'Táto špeciálna stránka slúži na manuálne obnovenie špeciálnych stránok. Po vybraní všetkých stránok, ktoré chcete obnoviť, kliknite na tlačidlo Obnoviť. Upozornenie: na väčších wiki môže obnovenie chvíľu trvať.',
	'refreshspecial-button' => 'Obnoviť vybrané',
	'refreshspecial-fail' => 'Prosím, vyberte aspoň jednu špeciálnu stránku, ktorá sa má obnoviť',
	'refreshspecial-refreshing' => 'obnovujú sa špeciálne stránky',
	'refreshspecial-skipped' => 'lacné, preskočené',
	'refreshspecial-success-subtitle' => 'obnovujú sa špeciálne stránky',
	'refreshspecial-choice' => 'obnovujú sa špeciálne stránky',
	'refreshspecial-js-disabled' => '(<i>Nie je možné použiť funkciu výberu všetkých stránok, keď máte vypnutý JavaScript.</i>)',
	'refreshspecial-select-all-pages' => '  vybrať všetky stránky',
	'refreshspecial-link-back' => 'Späť na rozšírenie',
	'refreshspecial-here' => '<b>tu</b>',
	'refreshspecial-none-selected' => 'Nevybrali ste žiadne špeciálne stránky. Vracia sa pôvodný výber.',
	'refreshspecial-db-error' => 'Chyba: chyba databázy',
	'refreshspecial-no-page' => 'Taká špeciálna stránka neexistuje',
	'refreshspecial-slave-lagged' => 'Spojenie s databázovým slave je pomalé, čaká sa...',
	'refreshspecial-reconnected' => 'Znovu pripojený.',
	'refreshspecial-reconnecting' => 'Spojenie zlyhalo, opätovné pripojenie o 10 sekúnd...',
	'refreshspecial-total-display' => '<br />Obnovených $1 stránok, čo činí $2 riadkov za čas $3 (celkový čas behu skriptu je $4)',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'refreshspecial' => 'Làm mới trang đặc biệt',
	'refreshspecial-desc' => 'Cho phép [[Special:RefreshSpecial|người dùng làm mới trang đặc biệt]]',
	'refreshspecial-title' => 'Làm mới trang đặc biệt',
	'refreshspecial-help' => 'Trang đặc biệt này là phương tiện để làm mới (refresh) các trang đặc biệt. Khi bạn đã chọn các trang bạn muốn làm mới, nhấn vào nút Làm mới phía dưới để thực hiện. Cảnh báo: việc làm mới có thể sẽ mất một lúc nếu wiki khá lớn.',
	'refreshspecial-button' => 'Làm mới các trang đã chọn',
	'refreshspecial-fail' => 'Xin hãy chọn ít nhất một trang đặc biệt để làm mới.',
	'refreshspecial-refreshing' => 'đang làm mới trang đặc biệt',
	'refreshspecial-skipped' => 'không quan trọng, bỏ qua',
	'refreshspecial-success-subtitle' => 'đang làm mới trang đặc biệt',
	'refreshspecial-choice' => 'đang làm mới trang đặc biệt',
	'refreshspecial-js-disabled' => '(<i>Bạn không thể chọn tất cả các trang trong khi JavaScript bị tắt</i>)',
	'refreshspecial-select-all-pages' => '  chọn tất cả các trang',
	'refreshspecial-link-back' => 'Quay về bộ mở rộng',
	'refreshspecial-here' => '<b>tại đây</b>',
	'refreshspecial-none-selected' => 'Bạn chưa chọn trang đặc biệt nào. Đang quay về lựa chọn mặc định.',
	'refreshspecial-db-error' => 'Thất bại: lỗi cơ sở dữ liệu',
	'refreshspecial-no-page' => 'Không có trang đặc biệt như vậy',
	'refreshspecial-slave-lagged' => 'Máy phụ bị trễ, đang chờ…',
	'refreshspecial-reconnected' => 'Đã kết nối lại.',
	'refreshspecial-reconnecting' => 'Kết nối thất bại, đang kết nối lại trong 10 giây nữa…',
	'refreshspecial-total-display' => '<br />Đã làm mới $1 trang, tổng cộng là $2 hàng trong thời gian $3 (thời gian để hoàn thành chạy mã kịch bản là $4)',
);

