<?php
/**
 * Internationalisation file for Review extension.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'review_sidebar_title' => "Review",
	'review_topics' => "# The following is the list of topics and ranges for the review extension.
# Each topic is defined by a line of five values, separated by ':' (all other lines are ignored).
# The order is: numerical key (>0), name, max range (>1; 2=yes/no), meaning of lowest value (e.g., 'bad'), meaning of highest value (e.g., 'good').
1:Quality:5:bad:good
2:Ready for 1.0:2:no:yes",
	'review_topic_page' => "Review topics",
	'review_sidebar_explanation' => "The leftmost checkbox always means 'no opinion'.",
	'review_save' => "Store review",
	'review_your_review' => "Your review of this page/revision:",
	'review_sidebar_you_have_other_reviews_for_this_article' => "You have already reviewed other versions of this page. Your other ratings will be merged into this one where you selected 'no opinion'.",
	'review_page_link' => "review statistics",
	'review_sidebar_final' => "For review data of this page view, see its $1",
	'review_for_page' => "Review for page \"$1\"",
	'review_for_user' => "Review for user \"$1\"",
	'review_error' => "Something's wrong!",
	'review_no_reviews_for_page' => "There are currently no reviews for \"$1\".",
	'review_total_statistics' => "Total",
	'review_statistics_left_corner' => "Revision",
	'review_version_link' => "Revision #$1",
	'review_statistic_cell' => "Average: $1 of $2<br />($4 users, $5 anons)",
	'review_version_statistic_cell' => "$1 of $2",
	'review_version_reviews_link' => "<small>(version reviews)</small>",
	'review_concerns_page' => "This review is about the page \"$1\".",
	'review_concerns_user' => "This is about reviews by user \"$1\".",
	'review_user_reviews' => "<small>(reviews by this user)</small>",
	'review_user_page_list' => "The user reviewed the following pages:",
	'review_user_details_link' => "(details)",
	'review_do_merge' => "Merge my reviews of other revisions of this page into this one",
	'review_has_been_stored' => "<font id='review_has_been_stored'>Your review has been stored!</font>",
	'revision_review_this_page_version_link' => "Review this version of the page.",
	'review_page_review' => "Review of page \"$1\"",
	'review_blocked' => "You're blocked, go away.",
	'review_wrong_namespace' => "Pages in this namespace cannot be reviewed!",
	'review_topic' => "Topic",
	'review_no_opinion' => "No opinion",
	'review_rating' => "Rating",
	'review_comment' => "Comment",
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'review_sidebar_title'                                   => 'مراجعة',
	'review_topics'                                          => "# التالي هو قائمة المواضيع والنطاقات لامتداد المراجعة.
# كل موضوع معرف بواسطة سطر من خمس قيم، مفصولة بواسطة ':' (كل السطور الأخرى سيتم تجاهلها).
# الترتيب هو: مفتاح رقمي (>0)، اسم، أقصى نطاق (>1؛ 2=نعم/لا)، معنى أقل قيمة (مثال، 'سيء')، معنى أعلى قيمة (مثال، 'جيد').
1:الجودة:5:سيء:جيد
2:مستعد ل 1.0:2:لا:نعم",
	'review_topic_page'                                      => 'مراجعة المواضيع',
	'review_sidebar_explanation'                             => "الصندوق في أقصى اليسار دائما يعني 'لا رأي'.",
	'review_save'                                            => 'خزن المراجعة',
	'review_your_review'                                     => 'مراجعتك لهذه الصفحة/النسخة:',
	'review_sidebar_you_have_other_reviews_for_this_article' => "لقد راجعت بالفعل نسخا أخرى من هذه الصفحة. تقييماتك الأخرى ستدمج إلى هذه المراجعة حيث اخترت 'لا رأي'.",
	'review_page_link'                                       => 'مراجعة الإحصاءات',
	'review_sidebar_final'                                   => 'لبيانات مراجعة رؤية هذه الصفحة، انظر $1',
	'review_for_page'                                        => 'المراجعة للصفحة "$1"',
	'review_for_user'                                        => 'المراجعة للمستخدم "$1"',
	'review_error'                                           => 'هناك شيء ما خطأ!',
	'review_no_reviews_for_page'                             => 'لا توجد حاليا مراجعات ل"$1".',
	'review_total_statistics'                                => 'الإجمالي',
	'review_statistics_left_corner'                          => 'النسخة',
	'review_version_link'                                    => 'النسخة #$1',
	'review_statistic_cell'                                  => 'المتوسط: $1 من $2<br />($4 مستخدم، $5 مجهول)',
	'review_version_statistic_cell'                          => '$1 من $2',
	'review_version_reviews_link'                            => '<small>(مراجعات النسخة)</small>',
	'review_concerns_page'                                   => 'هذه المراجعة هي حول الصفحة "$1".',
	'review_concerns_user'                                   => 'هذا حول المراجعات بواسطة المستخدم "$1".',
	'review_user_reviews'                                    => '<small>(المراجعات بواسطة هذا المستخدم)</small>',
	'review_user_page_list'                                  => 'المستخدم راجع الصفحات التالية:',
	'review_user_details_link'                               => '(تفاصيل)',
	'review_do_merge'                                        => 'ادمج مراجعاتي للنسخ الأخرى لهذه الصفحة إلى هذه المراجعة',
	'review_has_been_stored'                                 => "<font id='review_has_been_stored'>مراجعتك تم تخزينها!</font>",
	'revision_review_this_page_version_link'                 => 'مراجعة هذه النسخة من الصفحة.',
	'review_page_review'                                     => 'مراجعة صفحة "$1"',
	'review_blocked'                                         => 'أنت ممنوع، اذهب بعيدا.',
	'review_wrong_namespace'                                 => 'الصفحات في هذا النطاق لا يمكن مراجعتها!',
	'review_topic'                                           => 'الموضوع',
	'review_no_opinion'                                      => 'لا رأي',
	'review_rating'                                          => 'التقييم',
	'review_comment'                                         => 'تعليق',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'review_total_statistics'  => 'Общо',
	'review_user_details_link' => '(детайли)',
	'review_comment'           => 'Коментар',
);

/** Greek (Ελληνικά)
 * @author Consta
 */
$messages['el'] = array(
	'review_no_opinion' => 'Καμία γνώμη',
	'review_rating'     => 'Αξιολόγηση',
	'review_comment'    => 'Σχόλιο',
);

/** French (Français)
 * @author Sherbrooke
 * @author Dereckson
 * @author Grondin
 */
$messages['fr'] = array(
	'review_sidebar_title'                                   => 'Révision',
	'review_topics'                                          => "# Liste de sujets pour l'extension « review » de MediaWiki.
# ....
# Chaque sujet est défini par une liste de données séparées par « : » (toute autre liste est ignorée).
# ....
# Les données dans l'ordre sont :
## identifiant numérique (>0)
## nom
## étendue (>1)
## signification de la plus petite valeur (ex. « mauvais »)
## signification de la plus grande valeur (ex. « bon »)
# ....
# Par exemple, s'il faut seulement choisir « non » ou « oui », l'étendue vaut 2.
# Par exemple, s'il faut choisir entre « médiocre », « mauvais », « à améliorer », « bien » ou « très bien », l'étendue vaut 5.
# ....
1:Qualité:5:mauvais:bon
2:Prêt pour 1.0:2:non:oui",
	'review_topic_page'                                      => 'Sujets à révision',
	'review_sidebar_explanation'                             => 'La boîte à cocher à la gauche toute signifie toujours « sans opinion ».',
	'review_save'                                            => 'Sauvegarder la révision',
	'review_your_review'                                     => 'Votre révision de cette page ou révision :',
	'review_sidebar_you_have_other_reviews_for_this_article' => 'Vous avez déjà révisé les autres versions de cette page. Vos autres évaluations seront fusionnées avec celle-ci si vous avez sélectionné « sans opinion ».',
	'review_page_link'                                       => 'Statistiques de révisions',
	'review_sidebar_final'                                   => 'Pour voir les données de révision de la vue de cette page, aller à $1',
	'review_for_page'                                        => 'Révision pour la page « $1 »',
	'review_for_user'                                        => 'Révision pour contributeur « $1 »',
	'review_error'                                           => 'Quelque chose est brisé.',
	'review_no_reviews_for_page'                             => "Il n'y a pas de révisions pour « $1 ».",
	'review_total_statistics'                                => 'Total',
	'review_statistics_left_corner'                          => 'Révision',
	'review_version_link'                                    => 'Révision #$1',
	'review_statistic_cell'                                  => 'Moyenne : $1 de $2<br/>(contributeurs : $4 inscrits, $5 anonymes)',
	'review_version_statistic_cell'                          => '$1 de $2',
	'review_version_reviews_link'                            => '<small>(révisions de version)</small>',
	'review_concerns_page'                                   => 'Cete révision est à propos de la page « $1 ».',
	'review_concerns_user'                                   => "C'est à propos des revisions faites par le contributeur « $1 ».",
	'review_user_reviews'                                    => '<small>(révisions par ce contributeur)</small>',
	'review_user_page_list'                                  => 'Le contributeur a révisé les pages suivantes :',
	'review_user_details_link'                               => '(détails)',
	'review_do_merge'                                        => 'Fusionner mes passages en revue des autres révisions de cette page avec celle-ci',
	'review_has_been_stored'                                 => "<font id='review_has_been_stored'>Votre révision a été sauvegardée.</font>",
	'revision_review_this_page_version_link'                 => 'Réviser cette version de la page',
	'review_page_review'                                     => 'Révision de la page « $1 »',
	'review_blocked'                                         => 'Vous êtes bloqué.',
	'review_wrong_namespace'                                 => 'Les pages dans cet espace de nom ne peuvent être révisées.',
	'review_topic'                                           => 'Sujet',
	'review_no_opinion'                                      => 'Sans opinion',
	'review_rating'                                          => 'Évaluation',
	'review_comment'                                         => 'Commentaire',
);

/** Galician (Galego)
 * @author Alma
 */
$messages['gl'] = array(
	'review_sidebar_title'                   => 'Revisado',
	'review_topic_page'                      => 'Revisados os temas',
	'review_sidebar_explanation'             => "O extremo esquerdo da caixiña sempre significa 'sen opinión'.",
	'review_your_review'                     => 'O seu examen desta páxina/revisión:',
	'review_page_link'                       => 'revisar estatísticas',
	'review_sidebar_final'                   => 'Para revisar os datos desta páxina, véxase $1',
	'review_for_page'                        => 'Revisión para a páxina "$1"',
	'review_for_user'                        => 'Revisión para o usuario "$1"',
	'review_error'                           => 'Algo está fallando!',
	'review_no_reviews_for_page'             => 'Actualmente non hai revisións para "$1".',
	'review_total_statistics'                => 'Total',
	'review_statistics_left_corner'          => 'Revisión',
	'review_version_link'                    => 'Revisión #$1',
	'review_statistic_cell'                  => 'Promedio: $1 de $2<br />($4 usuarios, $5 anónimos)',
	'review_version_statistic_cell'          => '$1 de $2',
	'review_version_reviews_link'            => '<small>(versión revisada)</small>',
	'review_concerns_page'                   => 'Esta revisión é acerca da páxina "$1".',
	'review_concerns_user'                   => 'Isto é acerca das revisións polo usuario "$1".',
	'review_user_reviews'                    => '<small>(revisións por este usuario)</small>',
	'review_user_page_list'                  => 'O usuario revisou as seguintes páxinas:',
	'review_user_details_link'               => '(detalles)',
	'review_do_merge'                        => 'Fusionar as miñas revisións coas outras revisións desta páxina dentro desta',
	'review_has_been_stored'                 => "<font id='review_has_been_stored'>A súa revisión foi almacenada!</font>",
	'revision_review_this_page_version_link' => 'Examinar esta versión desta páxina.',
	'review_page_review'                     => 'Revisión da páxina "$1"',
	'review_blocked'                         => 'Vostede está bloqueado, desaparecerá.',
	'review_wrong_namespace'                 => 'As páxinas no espazo de nomes non poden ser revisadas!',
	'review_topic'                           => 'Tema',
	'review_no_opinion'                      => 'Sen opinión',
	'review_rating'                          => 'Avaliación',
	'review_comment'                         => 'Comentario',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'review_sidebar_title'                                   => 'Přepruwować',
	'review_topics'                                          => "# Deleka je lisćina temow a wobwodow za rozšěrjenje Review.
# Kóžda tema definuje so přez linku pjeć hódnotow wotdźělenych přez ':' (wšě druhe linki ignoruja so).
# Porjad je: numeriski kluč (>0), mjeno, maksimalny wobwod (>1; 2=haj/ně), woznam najnišeje hódnoty (na př. 'špatny'), woznam najwyšeje hódnoty (na př. 'dobry').
1:Kajkosć:5:špatny:dobry
2:Hotowy za 1.0:2:ně:haj",
	'review_topic_page'                                      => 'Temy přepruwować',
	'review_sidebar_explanation'                             => "Lěwy kontrolowy kašćik přeco 'žane měnjenje' woznamjenja.",
	'review_save'                                            => 'Přepruwowanje składować',
	'review_your_review'                                     => 'Waše přepruwowanje tuteje strony/wersije:',
	'review_sidebar_you_have_other_reviews_for_this_article' => "Sy hižo druhe wersije tutoho nastawka přepruwował. Twoje druhe pohódnoćenja budu so z tutym zjednoćeć, hdźež sy 'žane měnjenje' wubrał.",
	'review_page_link'                                       => 'Statistiku přepruwować',
	'review_sidebar_final'                                   => 'Za přepruwowanske daty tutoho napohlada strony, hlej jeho $1',
	'review_for_page'                                        => 'Přepruwowanje za stronu "$1"',
	'review_for_user'                                        => 'Přepruwowanje za wužiwarja "$1"',
	'review_error'                                           => 'Je někajki zmylk!',
	'review_no_reviews_for_page'                             => 'Tuchwilu žane přepruwowanja za "$1" njejsu.',
	'review_total_statistics'                                => 'Dohromady',
	'review_statistics_left_corner'                          => 'Wersija',
	'review_version_link'                                    => 'Wersija #$1',
	'review_statistic_cell'                                  => 'Přerězk: $1 z $2<br />($4 {{PLURAL:$4|wužiwar|wužiwarjej|wužiwarjo|wužiwarjow}}, $5 {{PLURAL:$5|anonymny|anonymnej|anonymne|anonymnych}})',
	'review_version_statistic_cell'                          => '$1 z $2',
	'review_version_reviews_link'                            => '<small>(wersijowe přepruwowanja)</small>',
	'review_concerns_page'                                   => 'Tute přepruwowanje je za stronu "$1".',
	'review_concerns_user'                                   => 'To je wo přepruwowanjach wot wužiwarja "$1".',
	'review_user_reviews'                                    => '<small>(přepruwowanja wot tutoho wužiwarja)</small>',
	'review_user_page_list'                                  => 'Wužiwar je slědowace strony přepruwował:',
	'review_user_details_link'                               => '(podrobnosće)',
	'review_do_merge'                                        => 'Moje přepruwowanja druhich wersijow tuteje strony z tutym zjednoćić',
	'review_has_been_stored'                                 => "<font id='review_has_been_stored'>Waše přepruwowanje je so składowało!</font>",
	'revision_review_this_page_version_link'                 => 'Přepruwuj tutu wersiju strony.',
	'review_page_review'                                     => 'Přepruwowanje strony "$1"',
	'review_blocked'                                         => 'Sy blokowany, dźi preč.',
	'review_wrong_namespace'                                 => 'Strony w tutym mjenowym rumje njehodźa so přepruwować!',
	'review_topic'                                           => 'Tema',
	'review_no_opinion'                                      => 'Žane měnjenje',
	'review_rating'                                          => 'Pohódnoćenje',
	'review_comment'                                         => 'Komentar',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 * @author Siebrand
 */
$messages['nl'] = array(
	'review_sidebar_title'                                   => 'Beoordelen',
	'review_topics'                                          => "# The following is the list of topics and ranges for the review extension.
# Each topic is defined by a line of five values, separated by ':' (all other lines are ignored).
# The order is: numerical key (>0), name, max range (>1; 2=yes/no), meaning of lowest value (e.g., 'bad'), meaning of highest value (e.g., 'good').
1:Kwaliteit:5:slecht:goed
2:Klaar voor 1.0:2:nee:ja",
	'review_topic_page'                                      => 'Onderwerpen beoordelen',
	'review_sidebar_explanation'                             => "Het meest linkse vakje betekent altijd 'geen mening'.",
	'review_save'                                            => 'Beoordeling opslaan',
	'review_your_review'                                     => 'Uw beoordeling van deze pagina/versie:',
	'review_sidebar_you_have_other_reviews_for_this_article' => "U heeft al een beoordeling gedaan voor andere versies van deze pagina. Uw andere waarderingen worden samengevoegd met deze waar u 'geen mening' heeft opgegeven.",
	'review_page_link'                                       => 'beoordelingsstatistieken',
	'review_sidebar_final'                                   => 'Zie $1 voor beoordelingsgegevens van deze pagina',
	'review_for_page'                                        => 'Beoordeling voor pagina "$1"',
	'review_for_user'                                        => 'Beoordeling voor gebruiker "$1"',
	'review_error'                                           => 'Er is iets niet in orde!',
	'review_no_reviews_for_page'                             => 'Er zijn op dit moment geen beoordelingen voor "$1".',
	'review_total_statistics'                                => 'Totaal',
	'review_statistics_left_corner'                          => 'Versie',
	'review_version_link'                                    => 'Versie #$1',
	'review_statistic_cell'                                  => 'Gemiddelde: $1 van $2<br />($4 gebruikers, $5 anomienen)',
	'review_version_statistic_cell'                          => '$1 van $2',
	'review_version_reviews_link'                            => '<small>(versiebeoordelingen)</small>',
	'review_concerns_page'                                   => 'Deze beoordeling gaat over de pagina "$1".',
	'review_concerns_user'                                   => 'Dit gaat over beoordelingen van gebruiker "$1".',
	'review_user_reviews'                                    => '<small>(beoordelingen van deze gebruiker)</small>',
	'review_user_page_list'                                  => "De gebruiker beoordeelde de volgende pagina's:",
	'review_user_details_link'                               => '(details)',
	'review_do_merge'                                        => 'Mijn beoordelingen van andere versies van deze pagina met deze versie samenvoegen',
	'review_has_been_stored'                                 => "<font id='review_has_been_stored'>Uw beoordeling is opgeslagen!</font>",
	'revision_review_this_page_version_link'                 => 'Deze versie van de pagina beoordelen.',
	'review_page_review'                                     => 'Beoordeling van pagina "$1"',
	'review_blocked'                                         => 'U bent geblokkeerd. Ga weg.',
	'review_wrong_namespace'                                 => "Pagina's in deze naamruimte kunnen niet beoordeeld worden!",
	'review_topic'                                           => 'Onderwerp',
	'review_no_opinion'                                      => 'Geen mening',
	'review_rating'                                          => 'Waardering',
	'review_comment'                                         => 'Opmerking',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'review_sidebar_title'                                   => 'Revision',
	'review_topics'                                          => "# Lista de subjèctes per l'extension « review » de MediaWiki. 
# .... 
# Cada subjècte es definit per una lista de donadas separadas per « : » (tota autra lista es ignorada).
# ....
# Las donadas dins l'òrdre son : ## identificant numeric (>0) ## nom ## espandida (>1) ## significacion de la valor mai pichona (ex. « marrit ») ## significacion de la valor mai granda (ex. « bon ») # .... # Per exemple, se cal solament causir « non » o « òc », l'espandida val 2. # Per exemple, se cal causir entre « mejancièr », « marrit », « de melhorar », « plan » o « fòrt plan », l'espandida val 5. # .... 1:Qualitat:5:marrit:bon 2:Prèst per 1.0:2:non:òc",
	'review_topic_page'                                      => 'Subjèctes a revision',
	'review_sidebar_explanation'                             => 'La boita de marcar a esquèrra tota significa totjorn « sens opinion ».',
	'review_save'                                            => 'Salvagardar la revision',
	'review_your_review'                                     => "Vòstra revision d'aquesta pagina o revision :",
	'review_sidebar_you_have_other_reviews_for_this_article' => "Ja avètz revisat las autras versions d'aquesta pagina. Vòstras autras evaluacions seràn fusionadas amb aquesta se avètz seleccionat « sens opinion ».",
	'review_page_link'                                       => 'Estatisticas de revisions',
	'review_sidebar_final'                                   => "Per veire las donadas de revision de la vista d'aquesta pagina, anatz a $1",
	'review_for_page'                                        => 'Revision per la pagina « $1 »',
	'review_for_user'                                        => 'Revision per contributor « $1 »',
	'review_error'                                           => 'Quicòm es copat!',
	'review_no_reviews_for_page'                             => 'I a pas de revisions per « $1 ».',
	'review_statistic_cell'                                  => 'Mejana : $1 de $2<br/>(contributors : $4 inscriches, $5 anonims)',
	'review_version_statistic_cell'                          => '$1 de $2',
	'review_version_reviews_link'                            => '<small>(revisions de version)</small>',
	'review_concerns_page'                                   => 'Aquesta revision es a prepaus de la pagina « $1 ».',
	'review_concerns_user'                                   => 'Es a prepaus de las revisions fachas pel contributor « $1 ».',
	'review_user_reviews'                                    => '<small>(revisions per aqueste contributor)</small>',
	'review_user_page_list'                                  => 'Lo contributor a revisat las paginas seguentas :',
	'review_user_details_link'                               => '(detalhs)',
	'review_has_been_stored'                                 => "<font id='review_has_been_stored'>Vòstra revision es estada salvagardada.</font>",
	'revision_review_this_page_version_link'                 => 'Revisar aquesta version de la pagina',
	'review_page_review'                                     => 'Revision de la pagina « $1 »',
	'review_blocked'                                         => 'Sètz blocat(-ada).',
	'review_wrong_namespace'                                 => 'Las paginas dins aqueste espaci de nom pòdon pas èsser revisadas.',
	'review_topic'                                           => 'Subjècte',
	'review_no_opinion'                                      => 'Sens opinion',
	'review_rating'                                          => 'Evaluacion',
	'review_comment'                                         => 'Comentari',
);

