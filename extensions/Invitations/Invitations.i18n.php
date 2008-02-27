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

/** French (Français)
 * @author Grondin
 */
$messages['fr'] = array(
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

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
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

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
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

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'invitations-inviteform-submit' => 'Mời',
);

