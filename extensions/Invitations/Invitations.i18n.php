<?php
/**
 * Internationalisation file for extension Invitations
 *
 * @addtogroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'invite-logpage' => 'Invitation Log',
	'invite-logpagetext' => 'This is a log of users inviting each other to use various software features.',
	'invite-logentry' => 'invited $1 to use the <i>$2</i> feature.',
	'invitations' => 'Manage invitations to software features',
	'invitations-desc' => 'Allows [[Special:Invitations|management of new features]] by restricting them to an invitation-based system',
	'invitations-invitedlist-description' => 'You have access to the following invitation-only software features. To manage invitations for an individual feature, click on its name.',
	'invitations-invitedlist-none' => 'You have not been invited to use any invitation-only software features.',
	'invitations-invitedlist-item' => '<b>$1</b> ($2 invitations available)',
	'invitations-pagetitle' => 'Invite-only software features',
	'invitations-uninvitedlist-description' => 'You do not have access to these other invitation-only software features.',
	'invitations-uninvitedlist-item' => '<b>$1</b>',
	'invitations-uninvitedlist-none' => 'At this time, no other software features are designated invitation-only.',
	'invitations-feature-pagetitle' => 'Invitation Management - $1',
	'invitations-feature-access' => 'You currently have access to use <i>$1</i>.',
	'invitations-feature-numleft' => 'You still have <b>$1</b> out of your $2 invitations left.',
	'invitations-feature-noneleft' => 'You have used all of your allocated invitations for this feature',
	'invitations-feature-noneyet' => 'You have not yet received your allocation of invitations for this feature.',
	'invitations-feature-notallowed' => 'You do not have access to use <i>$1</i>.',
	'invitations-inviteform-title' => 'Invite a user to use $1',
	'invitations-inviteform-username' => 'User to invite',
	'invitations-inviteform-submit' => 'Invite',
	'invitations-error-baduser' => 'The user you specified does not appear to exist.',
	'invitations-error-alreadyinvited' => 'The user you specified already has access to this feature!',
	'invitations-invite-success' => 'You have successfully invited $1 to use this feature!',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'invite-logpage'                  => 'Дневник на поканите',
	'invite-logentry'                 => 'покани $1 да използва <i>$2</i>.',
	'invitations'                     => 'Управление на поканите за различните услуги',
	'invitations-pagetitle'           => 'Възможности на софтуера, достъпни с покана',
	'invitations-feature-pagetitle'   => 'Управление на поканите - $1',
	'invitations-feature-notallowed'  => 'Нямате достъп да използвате <i>$1</i>.',
	'invitations-inviteform-title'    => 'Изпращане на покана на потребител да използва $1',
	'invitations-inviteform-username' => 'Потребител',
	'invitations-inviteform-submit'   => 'Изпращане на покана',
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'invite-logpage'                        => 'Einladungs-Logbuch',
	'invite-logpagetext'                    => 'Dies ist das Logbuch der einladungsbasierten Softwarefunktionen.',
	'invite-logentry'                       => 'hat $1 eingeladen, um die Softwarefunktionen <i>$2</i> zu nutzen.',
	'invitations'                           => 'Manage invitations to software features',
	'invitations-desc'                      => 'Ermöglicht die [[Special:Invitations|Verwaltung von Softwarefunktionen]] auf Basis von Einladungen',
	'invitations-invitedlist-description'   => 'Du hast Zugang zu den folgenden einladungsbasierten Softwarefunktionen. Um Einladungen für eine bestimmte Softwarefunktion zu verwalten, klicke auf ihren Namen.',
	'invitations-invitedlist-none'          => 'Du hast bisher keine Einladung zur Nutzung von einladungsbasierten Softwarefunktionen erhalten.',
	'invitations-invitedlist-item'          => '<b>$1</b> ($2 Einladungen verfügbar)',
	'invitations-pagetitle'                 => 'Softwarefunktionen auf Einladungs-Basis',
	'invitations-uninvitedlist-description' => 'Du hast keinen Zugang zu anderen einladungsbasierten Softwarefunktionen.',
	'invitations-uninvitedlist-none'        => 'Zur Zeit sind keine weiteren Softwarefunktionen einladungsbasiert.',
	'invitations-feature-pagetitle'         => 'Einladungs-Verwaltung - $1',
	'invitations-feature-access'            => 'Du hast Zugang zur Nutzung von <i>$1</i>.',
	'invitations-feature-numleft'           => 'Dir stehen noch <b>$1</b> von insgesamt $2 Einladungen zur Verfügung.',
	'invitations-feature-noneleft'          => 'Du hast alle dir zugewiesenen Einladungen für diese Softwarefunktion verbraucht.',
	'invitations-feature-noneyet'           => 'Dir wurden noch keine Einladungen für diese Softwarefunktion zugewiesen.',
	'invitations-feature-notallowed'        => 'Du hast keine Berechtigung, um <i>$1</i> zu nutzen.',
	'invitations-inviteform-title'          => 'Lade einen Benutzer zu der Funktion $1 ein',
	'invitations-inviteform-username'       => 'Einzuladender Benutzer',
	'invitations-inviteform-submit'         => 'Einladen',
	'invitations-error-baduser'             => 'Der angegebene Benutzer ist nicht vorhanden.',
	'invitations-error-alreadyinvited'      => 'Der angegebene Benutzer hat bereits Zugang zu dieser Softwarefunktion!',
	'invitations-invite-success'            => 'Du hast erfolgreich $1 zu dieser Softwarefunktion eingeladen!',
);

/** Greek (Ελληνικά)
 * @author Απεργός
 */
$messages['el'] = array(
	'invite-logpage'                        => 'Αρχείο καταγραφών προσκλήσεων',
	'invite-logpagetext'                    => 'Καταγραφές των προσκλήσεων σε χρήστες να χρησιμοποιήσουν διάφορες λειτουργίες λογισμικού.',
	'invite-logentry'                       => 'προσκάλεσε τον/την $1 να χρησιμοποιήσει τη λειτουργία <i>$2</i>.',
	'invitations'                           => 'Διαχείριση προσκλήσεων σε λειτουργίες λογισμικού',
	'invitations-desc'                      => 'Επιτρέπει τη [[Special:Invitations|διαχείριση καινούργιων λειτουργιών]] μέσω του περιορισμού τους σε ένα σύστημα βασισμένο σε προσκλήσεις',
	'invitations-invitedlist-description'   => 'Έχετε πρόσβαση στις ακόλουθες λειτουργίες λογισμικού που χρειάζονται πρόσκληση. Για να διαχειριστείτε προσκλήσεις για μια μεμονωμένη λειτουργία, κάντε κλικ στο όνομά της.',
	'invitations-invitedlist-none'          => 'Δεν προσκληθήκατε να χρησιμοποιήσετε καμία λειτουργία λογισμικού που χρειάζεται πρόσκληση.',
	'invitations-invitedlist-item'          => '<b>$1</b> ($2 προσκλήσεις διαθέσιμες)',
	'invitations-pagetitle'                 => 'Λειτουργίες λογισμικού που χρειάζονται πρόσκληση',
	'invitations-uninvitedlist-description' => 'Δεν έχετε πρόσβαση σε αυτές τις άλλες λειτουργίες λογισμικού που χρειάζονται πρόσκληση.',
	'invitations-uninvitedlist-none'        => 'Αυτή τη στιγμή, καμία άλλη λειτουργία λογισμικού δεν ορίζεται ως λειτουργία που χρειάζεται πρόσκληση.',
	'invitations-feature-pagetitle'         => 'Διαχείριση Προσκλήσεων - $1',
	'invitations-feature-access'            => 'Έχετε τώρα πρόσβαση να χρησιμοποιήσετε <i>$1</i>.',
	'invitations-feature-numleft'           => 'Ακόμη σας μένουν <b>$1</b> από τις $2 προσκλήσεις σας.',
	'invitations-feature-noneleft'          => 'Έχετε χρησιμοποιήσει όλες τις κατανεμημένες προσκλήσεις σας για αυτή τη λειτουργία.',
	'invitations-feature-noneyet'           => 'Δεν έχετε πάρει τη δική σας κατανομή των προσκλήσεων για αυτή τη λειτουργία.',
	'invitations-feature-notallowed'        => 'Δεν έχετε πρόσβαση να χρησιμοποιήσετε <i>$1</i>.',
	'invitations-inviteform-title'          => 'Πρόσκληση σε ένα χρήστη να χρησιμοποιήσει $1',
	'invitations-inviteform-username'       => 'Χρήστης προς πρόσκληση',
	'invitations-inviteform-submit'         => 'Πρόσκληση',
	'invitations-error-baduser'             => 'Ο χρήστης που καθορίσατε δεν φαίνεται να υπάρχει.',
	'invitations-error-alreadyinvited'      => 'Ο χρήστης που καθορίσατε έχει πρόσβαση ήδη σε αυτή τη λειτουργία!',
	'invitations-invite-success'            => 'Έχετε προκαλέσει επιτυχώς τον/την $1 να χρησιμοποιήσει αυτή τη λειτουργία!',
);

/** Spanish (Español)
 * @author Dmcdevit
 */
$messages['es'] = array(
	'invitations-desc'                      => 'Permite [[Especial:Invitaciones|control de funciones nuevas]] por restringirlas a un sistema basado en invitaciones.',
	'invitations-invitedlist-description'   => 'Tiene acceso a los siguientes funciones sólo por invitación. Para manejar invitaciones a una función específica, haga clic en su nombre.',
	'invitations-invitedlist-none'          => 'No ha sido invitado usar ninguna función sólo por invitación.',
	'invitations-invitedlist-item'          => '<b>$1</b> ($2 invitaciones disponibles)',
	'invitations-pagetitle'                 => 'Funciones sólo por invitación',
	'invitations-uninvitedlist-description' => 'No tiene acceso a estas otras functiones sólo por invitación.',
	'invitations-uninvitedlist-none'        => 'Ahora mismo, ninguna otra función está designado sólo por invitación.',
	'invitations-feature-pagetitle'         => 'Gestión de invitaciones - $1',
	'invitations-feature-access'            => 'Tiene permiso para usar <i>$1</i>.',
	'invitations-feature-numleft'           => 'Todavía tiene <b>$1</b> de sus $1 invitaciones.',
	'invitations-feature-noneleft'          => 'Ha usado todo de sus invitaciones destinado a esta función.',
	'invitations-feature-noneyet'           => 'No ha recibido su cuota de invitaciones para esta función.',
	'invitations-feature-notallowed'        => 'No tiene permiso para usar <i>$1</i>.',
	'invitations-inviteform-title'          => 'Invitar a un usuario a usar $1',
	'invitations-inviteform-username'       => 'Usuario para invitar',
	'invitations-inviteform-submit'         => 'Invitar',
	'invitations-error-baduser'             => 'El usuario que usted eligió no existe.',
	'invitations-error-alreadyinvited'      => 'El usuario que usted eligío ya tiene acceso a esta función!',
	'invitations-invite-success'            => 'Ha invitado con éxito a $1 usar esta función.',
);

/** French (Français)
 * @author Grondin
 */
$messages['fr'] = array(
	'invite-logpage'                        => 'Journal des invitations',
	'invite-logpagetext'                    => 'Voici un journal des utilisateurs en invitant d’autres pour utiliser les fonctionnalités de divers programmes',
	'invite-logentry'                       => 'a invité $1 à utiliser la fonctionnalité de <i>$2</i>.',
	'invitations'                           => 'Gère les invitations des fonctionnalités logicielles',
	'invitations-desc'                      => 'Permet [[Special:Invitations|la gestion des nouvelles fonctionnalités]] en les restreignant par une système basé sur l’invitation.',
	'invitations-invitedlist-description'   => "Vous avez l'accès aux caractéristiques suivantes du logiciel d’invite seule. Pour gérer les invitations pour une catactéristique individuelle, cliquez sur son nom.",
	'invitations-invitedlist-none'          => 'Vous n’avez pas été invité pour utiliser les fonctionnalités du logiciel d’invite seule.',
	'invitations-invitedlist-item'          => '<b>$1</b> ($2 invitations disponibles)',
	'invitations-pagetitle'                 => 'Fonctionnalités du logiciel d’invite seule',
	'invitations-uninvitedlist-description' => 'Vous n’avez pas l’accès à ces autres caractéristiques du programme d’invite seule.',
	'invitations-uninvitedlist-none'        => 'À cet instant, aucune fonctionnalité logicielle n’a été désignée par l’invite seule.',
	'invitations-feature-pagetitle'         => 'Gestion de l’invitation - $1',
	'invitations-feature-access'            => 'Vous avez actuellement l’accès pour utiliser <i>$1</i>.',
	'invitations-feature-numleft'           => 'Vous avez encore <b>$1</b> de vos $2 invitations de laissées.',
	'invitations-feature-noneleft'          => "Vous avez utilisé l'ensemble de vos invitations permises pour cette fonctionnalité",
	'invitations-feature-noneyet'           => 'Vous n’avez pas cependant reçu votre assignation des invitations pour cette fonctionnalité.',
	'invitations-feature-notallowed'        => 'Vous n’avez pas l’accès pour utiliser <i>$1</i>.',
	'invitations-inviteform-title'          => 'Inviter un utilisateur pour utiliser $1',
	'invitations-inviteform-username'       => 'Utilisateur à inviter',
	'invitations-inviteform-submit'         => 'Inviter',
	'invitations-error-baduser'             => 'L’utilisateur que vous avez indiqué ne semble pas exister.',
	'invitations-error-alreadyinvited'      => 'L’utilisateur que vous avez indiqué dispose déjà de l’accès à cette fonctionnalité !',
	'invitations-invite-success'            => 'Vous invité $1 avec succès pour utiliser cette fonctionnalité !',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 */
$messages['km'] = array(
	'invite-logpage'                => 'កំណត់ហេតុ នៃ ការអញ្ជើញ',
	'invitations-inviteform-submit' => 'អញ្ជើញ',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'invitations-feature-pagetitle'    => 'Gestioun vun der Invitatioun - $1',
	'invitations-feature-numleft'      => 'Dir hutt nach <b>$1</b> vun ären $2 Invitatiounen iwwreg.',
	'invitations-feature-noneleft'     => 'Dir hutt all är Invitatiounen fir dës Fonctioun benotzt.',
	'invitations-feature-noneyet'      => 'Dir hutt är Invitatioune fir dës Fonctioun nach net kritt.',
	'invitations-feature-notallowed'   => 'Dir hutt keen Zougang fir <i>$1</i> ze benotzen.',
	'invitations-inviteform-title'     => 'Ee Benotzer alueden fir $1 ze benotzen',
	'invitations-inviteform-username'  => 'Benotzer fir anzelueden',
	'invitations-inviteform-submit'    => 'Alueden',
	'invitations-error-baduser'        => 'De Benotzer deen Dir uginn huet schéngt et net ze ginn.',
	'invitations-error-alreadyinvited' => 'Dee Benotzer deen Dir uginn huet huet schonn Accès op déi Fonctioun!',
	'invitations-invite-success'       => 'Dir hutt de(n) $1 mat Succès invitéiert fir dës Fonctioun ze benotzen!',
);

/** Dutch (Nederlands)
 * @author Siebrand
 * @author SPQRobin
 */
$messages['nl'] = array(
	'invite-logpage'                        => 'Uitnodigingslogboek',
	'invite-logpagetext'                    => 'Dit is een logboek van gebruikers die elkaar uitnodigen om verschillende softwarefuncties te gebruiken.',
	'invite-logentry'                       => 'heeft $1 uitgenodigd om de functie <i>$2</i> te gebruiken.',
	'invitations'                           => 'Uitnodigingen voor softwarefuncties beheren',
	'invitations-desc'                      => 'Maakt het mogelijk het gebruik van [[Special:Invitations|nieuwe functionaliteit te beheren]] door deze alleen op uitnodiging beschikbaar te maken',
	'invitations-invitedlist-description'   => 'U hebt toegang tot de volgende alleen op uitnodiging beschikbare functionaliteit. Om te verzenden uitnodigingen per functionaliteit te beheren, kunt u op de naam van de functionaliteit klikken.',
	'invitations-invitedlist-none'          => 'U bent niet uitgenodigd om alleen op uitnodiging beschikbare functionaliteit te gebruiken.',
	'invitations-invitedlist-item'          => '<b>$1</b> ($2 uitnodigingen beschikbaar)',
	'invitations-pagetitle'                 => 'Functionaliteit alleen op uitnodiging beschikbaar',
	'invitations-uninvitedlist-description' => 'U hebt geen toegang tot deze andere alleen op uitnodiging beschikbare functionaliteit.',
	'invitations-uninvitedlist-none'        => 'Er is op dit moment geen functionaliteit aangewezen die alleen op uitnodiging beschikbaar is.',
	'invitations-feature-pagetitle'         => 'Uitnodigingenbeheer - $1',
	'invitations-feature-access'            => 'U hebt op dit moment toestemming om <i>$1</i> te gebruiken.',
	'invitations-feature-numleft'           => 'U hebt nog <b>$1</b> van uw $2 uitnodigingen over.',
	'invitations-feature-noneleft'          => 'U hebt alle uitnodigingen voor deze functionaliteit gebruikt',
	'invitations-feature-noneyet'           => 'U hebt nog geen te verdelen uitnodigingen gekregen voor deze functionaliteit.',
	'invitations-feature-notallowed'        => 'U hebt geen toestemming om <i>$1</i> te gebruiken.',
	'invitations-inviteform-title'          => 'Een gebruiker uitnodigen om $1 te gebruiken',
	'invitations-inviteform-username'       => 'Uit te nodigen gebruiker',
	'invitations-inviteform-submit'         => 'Uitnodigen',
	'invitations-error-baduser'             => 'De opgegeven gebruiker lijkt niet te bestaan.',
	'invitations-error-alreadyinvited'      => 'De opgegeven gebruiker heeft al toegang tot deze functionaliteit.',
	'invitations-invite-success'            => '$1 is uitgenodigd voor het gebruiken van deze functionaliteit!',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'invite-logpage'                        => 'Invitasjonslogg',
	'invite-logpagetext'                    => 'Dette er en logg over hvilke brukere som har invitert hverandre til å bruke diverse programvarefunksjoner.',
	'invite-logentry'                       => 'inviterte $1 til å bruke funksjonen <i>$2</i>',
	'invitations'                           => 'Behandling av intiasjoner til programvarefunksjoner',
	'invitations-desc'                      => 'Muliggjør [[Special:Invitations|behandling av nye funksjoner]] ved å begrense dem til et invitasjonsbasert system',
	'invitations-invitedlist-description'   => 'Du har tilgang til følgende funksjoner som krever invitasjon. For å behandle invitasjoner for individuelle funksjoner, klikk på funksjonens navn.',
	'invitations-invitedlist-none'          => 'Du har ikke blitt invitert til å bruke noen funksjoner som krever invitasjon.',
	'invitations-invitedlist-item'          => '<b>$1</b> ($2 invitasjoner tilgjengelig)',
	'invitations-pagetitle'                 => 'Funksjoner som krever invitasjon',
	'invitations-uninvitedlist-description' => 'Du har ikke tilgang til disse funksjonene som krever invitasjon.',
	'invitations-uninvitedlist-none'        => 'Ingen programvarefunksjoner krever invitasjon.',
	'invitations-feature-pagetitle'         => 'Invitasjonsbehandling – $1',
	'invitations-feature-access'            => 'Du har tilgang til å bruke <i>$1</i>.',
	'invitations-feature-numleft'           => 'Av dine $2 invitasjoner har du fortsatt <b>$1</b> igjen.',
	'invitations-feature-noneleft'          => 'Du har brukt alle dine invitasjoner for denne funksjonen',
	'invitations-feature-noneyet'           => 'Du har ikke fått tildelt din andel invitasjoner for denne funksjonen.',
	'invitations-feature-notallowed'        => 'Du har ikke tilgang til å bruke <i>$1</i>.',
	'invitations-inviteform-title'          => 'Inviter en bruker til å bruke $1',
	'invitations-inviteform-username'       => 'Bruker som skal inviteres',
	'invitations-inviteform-submit'         => 'Inviter',
	'invitations-error-baduser'             => 'Brukeren du oppga finnes ikke.',
	'invitations-error-alreadyinvited'      => 'Brukeren du oppga har allerede tilgang til denne funksjonen!',
	'invitations-invite-success'            => 'Du har invitert $1 til å bruke denne funksjonen!',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'invite-logpage'                        => 'Jornal dels convits',
	'invite-logpagetext'                    => 'Vaquí un jornal dels utilizaires en convidant d’autres per utilizar las foncionalitats de divèrses programas',
	'invite-logentry'                       => 'a convidat $1 a utilizar la foncionalitat de <i>$2</i>.',
	'invitations'                           => 'Gerís los convits de las foncionalitats logicialas',
	'invitations-desc'                      => 'Permet [[Special:Invitations|la gestion de las foncionalitats novèlas]] en las restrenhent per un sistèma basat sul convit.',
	'invitations-invitedlist-description'   => 'Avètz accès a las caracteristicas seguentas del logicial de convit sol. Per gerir los convits per una catacteristica individuala, clicatz sus son nom.',
	'invitations-invitedlist-none'          => 'Sètz pas estat convidat per utilizar las foncionalitats del logicial de convit sol.',
	'invitations-invitedlist-item'          => '<b>$1</b> ($2 convits disponibles)',
	'invitations-pagetitle'                 => 'Foncionalitats del logiciel de convit sol',
	'invitations-uninvitedlist-description' => 'Avètz pas accès a aquestas autras caracteristicas del programa de convit sol.',
	'invitations-uninvitedlist-none'        => 'En aqueste moment, cap de foncionalitat logiciala es pas estada designada pel convit sol.',
	'invitations-feature-pagetitle'         => 'Gestion del convit - $1',
	'invitations-feature-access'            => "Actualament, avètz l'accès per utilizar <i>$1</i>.",
	'invitations-feature-numleft'           => 'Avètz encara <b>$1</b> de vòstres $2 convits que son daissats.',
	'invitations-feature-noneleft'          => "Avètz utilizat l'ensemble de vòstres convits permeses per aquesta foncionalitat",
	'invitations-feature-noneyet'           => 'Çaquelà, avètz pas recebut vòstra assignacion dels convits per aquesta foncionalitat.',
	'invitations-feature-notallowed'        => 'Avètz pas l’accès per utilizar <i>$1</i>.',
	'invitations-inviteform-title'          => 'Convidar un utilizaire per utilizar $1',
	'invitations-inviteform-username'       => 'Utilizaire de convidar',
	'invitations-inviteform-submit'         => 'Convidar',
	'invitations-error-baduser'             => "L’utilizaire qu'avètz indicat sembla pas existir.",
	'invitations-error-alreadyinvited'      => "L’utilizaire qu'avètz indicat ja dispausa de l’accès a aquesta foncionalitat !",
	'invitations-invite-success'            => 'Avètz convidat $1 amb succès per utilizar aquesta foncionalitat !',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'invite-logpage'                        => 'Registo de Convites',
	'invitations-desc'                      => 'Permite [[{{ns:special}}:Invitations|a gestão de novas funcionalidades]] através da sua restrição a um sistema baseado em convites',
	'invitations-invitedlist-description'   => 'Você tem acesso às seguintes funcionalidades do software atribuídas apenas por convite. Para gerir convites para uma funcionalidade individual, clique no seu nome.',
	'invitations-invitedlist-none'          => 'Você não foi convidado a usar nenhuma funcionalidade do software atribuída apenas por convite.',
	'invitations-invitedlist-item'          => '<b>$1</b> ($2 convites disponíveis)',
	'invitations-pagetitle'                 => 'Funcionalidades do software atribuídas apenas por convite',
	'invitations-uninvitedlist-description' => 'Você não tem acesso a estas outras funcionalidades do software atribuídas apenas por convite.',
	'invitations-uninvitedlist-none'        => 'Neste momento, mais nenhumas funcionalidades do software são atribuídas apenas por convite.',
	'invitations-feature-pagetitle'         => 'Gestão de Convites - $1',
	'invitations-feature-access'            => 'Actualmente não possui acesso ao uso de <i>$1</i>.',
	'invitations-feature-numleft'           => 'Ainda lhe restam <b>$1</b> dos seus $2 convites.',
	'invitations-feature-noneleft'          => 'Você já utilizou toda a sua quota de convites para esta funcionalidade',
	'invitations-feature-noneyet'           => 'Você ainda não recebeu a sua quota de convites para esta funcionalidade.',
	'invitations-feature-notallowed'        => 'Não tem acesso ao uso de <i>$1</i>.',
	'invitations-inviteform-title'          => 'Convidar um utilizador a usar $1',
	'invitations-inviteform-username'       => 'Utilizador a convidar',
	'invitations-inviteform-submit'         => 'Convidar',
	'invitations-error-baduser'             => 'O utilizador que especificou parece não existir.',
	'invitations-error-alreadyinvited'      => 'O utilizador que especificou já tem acesso a esta funcionalidade!',
	'invitations-invite-success'            => 'Convidou $1 para usar esta funcionalidade com sucesso!',
);

/** Russian (Русский)
 * @author .:Ajvol:.
 */
$messages['ru'] = array(
	'invite-logpage'                        => 'Журнал приглашений',
	'invite-logpagetext'                    => 'Это журнал приглашений использовать возможности программного обеспечения',
	'invite-logentry'                       => 'пригласил $1 использовать возможность <i>$2</i>',
	'invitations'                           => 'Управление приглашениями на возможности ПО',
	'invitations-desc'                      => 'Позволяет [[Special:Invitations|управлять новыми возможностями]], ограничивая к ним доступ с помощью системы приглашений',
	'invitations-invitedlist-description'   => 'Вы имеете доступ к следующим возможностям программного обеспечения, доступным только по приглашениям. Чтобы управлять приглашениями каждой возможности ПО, щёлкните по её имени.',
	'invitations-invitedlist-none'          => 'Вы не были приглашены использовать какую-либо возможность программы, из доступных только по приглашениям.',
	'invitations-invitedlist-item'          => '<b>$1</b> ($2 приглашений доступно)',
	'invitations-pagetitle'                 => 'Возможность ПО только по приглашению',
	'invitations-uninvitedlist-description' => 'У вас нет доступа к другим возможностям ПО, доступным только по приглашениям.',
	'invitations-uninvitedlist-none'        => 'В настоящее время нет других возможностей ПО, доступных только по приглашениям.',
	'invitations-feature-pagetitle'         => 'Управление приглашениями — $1',
	'invitations-feature-access'            => 'Сейчас вы имеете доступ к использованию <i>$1</i>.',
	'invitations-feature-numleft'           => 'У вас остаётся <b>$1</b> из $2 приглашений.',
	'invitations-feature-noneleft'          => 'Вы использовали все выделенные вам приглашения для этой возможности',
	'invitations-feature-noneyet'           => 'Вам ещё не было выделено приглашений для рассылки для этой возможности',
	'invitations-feature-notallowed'        => 'У вас нет доступа к использованию <i>$1</i>.',
	'invitations-inviteform-title'          => 'Приглашение участника использовать $1',
	'invitations-inviteform-username'       => 'Участник',
	'invitations-inviteform-submit'         => 'Пригласить',
	'invitations-error-baduser'             => 'Участника, которого вы указали, по-видимому не существует.',
	'invitations-error-alreadyinvited'      => 'Участник, которого вы указали, уже имеет доступ к этой возможности!',
	'invitations-invite-success'            => 'Вы успешно пригласили участника $1 использовать эту возможность!',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'invite-logpage'                        => 'Záznam pozvánok',
	'invite-logpagetext'                    => 'Toto je záznam používateľov pozývajúcich sa navzájom používať rozličné možnosti softvéru.',
	'invite-logentry'                       => 'pozval $1 používať možnosť <i>$2</i>.',
	'invitations'                           => 'Spravovať pozvánky možností softvéru',
	'invitations-desc'                      => 'Umožňuje [[Special:Invitations|správu nových možností]] obmedzením prístupu k nim na báze pozvánok',
	'invitations-invitedlist-description'   => 'Máte prístup k nasledovným možnostiam softvéru, ktoré sú prístupné iba na báze pozvánok. Spravovať pozvánky jednotlivých možností môžete po kliknutí na jej názov.',
	'invitations-invitedlist-none'          => 'Neboli ste pozvaný používať žiadnu z možností softvéru s prístupom len na pozvanie.',
	'invitations-invitedlist-item'          => '<b>$1</b> ($2 dostupných pozvánok)',
	'invitations-pagetitle'                 => 'Možnosti softvéru s prístupom len na pozvanie.',
	'invitations-uninvitedlist-description' => 'Nemáte prístup k týmto ostatným možnostiam softvéru s prístupom len na pozvanie.',
	'invitations-uninvitedlist-none'        => 'Momentálne nie je prístup k žiadnym iným možnostiam softvéru určený len na pozvanie.',
	'invitations-feature-pagetitle'         => 'Správa pozvánok - $1',
	'invitations-feature-access'            => 'Momentálne máte prístup na používanie <i>$1</i>.',
	'invitations-feature-numleft'           => 'Zostáva vám <b>$1</b> z vašich $2 pozvánok.',
	'invitations-feature-noneleft'          => 'Využili ste všetky z vašich vyhradených pozvánok na prístup k tejto možnosti',
	'invitations-feature-noneyet'           => 'Zatiaľ ste nedostali svoj podiel pozvánok na prístup k tejto možnosti.',
	'invitations-feature-notallowed'        => 'Nemáte právo na prístup k <i>$1</i>.',
	'invitations-inviteform-title'          => 'Pozvať používateľa na používanie $1',
	'invitations-inviteform-username'       => 'Pozvať používateľa',
	'invitations-inviteform-submit'         => 'Pozvať',
	'invitations-error-baduser'             => 'Zdá sa, že používateľ, ktorého ste uviedli neexistuje.',
	'invitations-error-alreadyinvited'      => 'Používateľ, ktorého ste uviedli, už má prístup k tejto možnosti.',
	'invitations-invite-success'            => 'Úspešne ste pozvali používateľa $1 využívať túto možnosť!',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'invite-logpage'                        => 'Ienleedengs-Logbouk',
	'invite-logpagetext'                    => 'Dit is dät Logbouk fon do ienleedengsbasierde Softwarefunktione.',
	'invite-logentry'                       => 'häd $1 ienleeden, uum do Softwarefunktione <i>$2</i> tou bruuken.',
	'invitations-desc'                      => 'Moaket ju [[Special:Invitations|Ferwaltenge fon Softwarefunktione]] ap Gruund fon Ienleedengen muugelk',
	'invitations-invitedlist-description'   => 'Du hääst Tougoang tou do foulgjende ienleedengsbasierde Softwarefunktione. Uum Ienleedengen foar ne bestimde Softwarefunktion tou ferwaltjen, klik ap hieren Noome.',
	'invitations-invitedlist-none'          => 'Du hääst tou nu tou neen Ienleedengen foar dät Bruuken fon ienleedengsbasierde Softwarefunktione kriegen.',
	'invitations-invitedlist-item'          => '<b>$1</b> ($2 Ienleedengen ferföigboar)',
	'invitations-pagetitle'                 => 'Softwarefunktione ap Ienleedengs-Basis',
	'invitations-uninvitedlist-description' => 'Du hääst naan Tougoang tou uur ienleedengsbasierde Softwarefunktione',
	'invitations-uninvitedlist-none'        => 'Apstuuns sunt neen wiedere Softwarefunktione ienleedengsbasierd',
	'invitations-feature-pagetitle'         => 'Ienleedengsferwaltenge - $1',
	'invitations-feature-access'            => 'Du hääst Tougoang toun Gebruuk fon <i>$1</i>.',
	'invitations-feature-numleft'           => 'Die stounde noch <b>$1</b> fon mädnunner $2 Ienleedengen tou Ferföigenge.',
	'invitations-feature-noneleft'          => 'Du hääst aal die touwiesde Ienleedengen foar disse Softwarefunktion ferbruukt.',
	'invitations-feature-noneyet'           => 'Die wuuden noch neen Ienleedengen foar disse Softwarefunktion touwiesd.',
	'invitations-feature-notallowed'        => 'Du hääst neen Begjuchtigenge, uum <i>$1</i> tou bruuken.',
	'invitations-inviteform-title'          => 'Leede n Benutser tou ju Funktion $1 ien',
	'invitations-inviteform-username'       => 'Ientouleedenden Benutser',
	'invitations-inviteform-submit'         => 'Ienleede',
	'invitations-error-baduser'             => 'Dät lät, as wan die anroate Benutser nit bestoant.',
	'invitations-error-alreadyinvited'      => 'Die anroate Benutser häd al Tougoang tou disse Softwarefunktion!',
	'invitations-invite-success'            => 'Du hääst mäd Ärfoulch $1 tou disse Softwarefunktion ienleeden!',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'invite-logpage'                => 'ఆహ్వానాల దినచర్య',
	'invitations-invitedlist-item'  => '<b>$1</b> ($2 ఆహ్వానాలు మిగిలివున్నాయి)',
	'invitations-feature-pagetitle' => 'ఆహ్వాన నిర్వహణ - $1',
	'invitations-inviteform-submit' => 'ఆహ్వానించు',
	'invitations-error-baduser'     => 'మీరు చెప్పిన ఆ వాడుకరి లేనేలేరు.',
);

/** Vietnamese (Tiếng Việt)
 * @author Vinhtantran
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'invite-logpage'                        => 'Nhật trình thư mời',
	'invite-logpagetext'                    => 'Đây là nhật trình ghi lại những lời mời từ người dùng này tới người dùng khác để sử dụng những tính năng phần mềm khác nhau.',
	'invite-logentry'                       => 'đã mời $1 dùng tính năng <i>$2</i>.',
	'invitations'                           => 'Quản lý thư mời đối với các tính năng phần mềm',
	'invitations-desc'                      => 'Cho phép [[Special:Invitations|quản lý các tính năng mới]] bằng cách hạn chế họ dựa vào hệ thống thư mời',
	'invitations-invitedlist-description'   => 'Bạn đã truy cập vào các tính năng phần mềm chỉ cho phép thư mời sau đây. Để quản lú các thư mời cho một tính năng riêng lẻ, hãy nhấn vào tên của nó.',
	'invitations-invitedlist-none'          => 'Bạn chưa được mời sử dụng tính năng phần mềm chỉ dành cho thư mời nào.',
	'invitations-invitedlist-item'          => '<b>$1</b> (hiện có $2 thư mời)',
	'invitations-pagetitle'                 => 'Các tính năng phần mềm chỉ cho phép thư mời',
	'invitations-uninvitedlist-description' => 'Bạn không có quyền truy cập vào những tính năng phần mềm chỉ cho phép thư mời sau.',
	'invitations-uninvitedlist-none'        => 'Vào lúc này, không có tính năng phần mềm nào khác được chỉ định chỉ cho phép thư mời.',
	'invitations-feature-pagetitle'         => 'Quản lý Thư mời - $1',
	'invitations-feature-access'            => 'Bạn hiện có quyền sử dụng <i>$1</i>.',
	'invitations-feature-numleft'           => 'Bạn vẫn còn lại <b>$1</b> trong tổng số $2 lời mời.',
	'invitations-feature-noneleft'          => 'Bạn đã dùng tất cả các lời mời cho phép dành cho tính năng này',
	'invitations-feature-noneyet'           => 'Bạn chưa nhận được lượng thư mời cung cấp dành cho tính năng này.',
	'invitations-feature-notallowed'        => 'Bạn không có quyền sử dụng <i>$1</i>.',
	'invitations-inviteform-title'          => 'Mời một thành viên sử dụng $1',
	'invitations-inviteform-username'       => 'Thành viên được mời',
	'invitations-inviteform-submit'         => 'Mời',
	'invitations-error-baduser'             => 'Thành viên bạn chỉ định dường như không tồn tại.',
	'invitations-error-alreadyinvited'      => 'Thành viên bạn chỉ định đã có quyền sử dụng tính năng này rồi!',
	'invitations-invite-success'            => 'Bạn đã mời $1 sử dụng tính năng này!',
);

