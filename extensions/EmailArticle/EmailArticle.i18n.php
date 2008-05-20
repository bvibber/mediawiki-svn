<?php
/**
 * Internationalisation file for extension EmailArticle.
 *
 * @addtogroup Extensions
*/

$messages = array();

/** English
 * @author Nad
 */
$messages['en'] = array(
	'emailarticle'        => 'E-mail page',
	'ea-desc'             => 'Send rendered HTML page to an e-mail address or list of addresses using [http://phpmailer.sourceforge.net phpmailer].',
	'ea-heading'          => "=== E-mailing [[$1]] page ===",
	'ea-fromgroup'        => 'From group:',
	'ea-articlesend'      => 'article sent from $1',
	'ea-noarticle'        => "Please specify a page to send, for example [[Special:EmailArticle/Main Page]].",
	'ea-norecipients'     => "No valid e-mail addresses found!",
	'ea-listrecipients'   => "=== List of $1 {{PLURAL:$1|recipient|recipients}} ===",
	'ea-error'            => "'''Error sending [[$1]]:''' ''$2''",
	'ea-denied'           => 'Permission denied',
	'ea-sent'             => "Page [[$1]] sent successfully to '''$2''' {{PLURAL:$2|recipient|recipients}} by [[User:$3|$3]].",
	'ea-selectrecipients' => 'Select recipients',
	'ea-compose'          => 'Compose content',
	'ea-selectlist'       => "Additional recipients as page titles or e-mail addresses
*''separate items with , ; * \\n
*''list can contain templates and parser-functions''",
	'ea-show'             => 'Show recipients',
	'ea-send'             => 'Send!',
	'ea-subject'          => 'Enter a subject line for the e-mail',
	'ea-header'           => 'Prepend content with optional message (wikitext)',
	'ea-selectcss'        => 'Select a CSS stylesheet',
);

/** French (Français)
 * @author Grondin
 */
$messages['fr'] = array(
	'emailarticle'        => 'Envoyer l’article par courriel',
	'ea-desc'             => 'Envoie le rendu d’une page HTML à une adresse électronique où à une liste d’adresses en utilisant [http://phpmailer.sourceforge.net phpmailer]',
	'ea-heading'          => '=== Envoi de la page [[$1]] par courrier électronique ===',
	'ea-noarticle'        => 'Veuillez spécifier une page à envoyer, par exemple [[Special:EmailArticle/Accueil]]',
	'ea-norecipients'     => 'Aucune adresse courriel de trouvée !',
	'ea-listrecipients'   => '=== Liste de $1 {{PLURAL:$1|destinataire|destinataires}} ===',
	'ea-error'            => "'''Erreur de l’envoi de [[$1]] :''' ''$2''",
	'ea-sent'             => "L'article [[$1]] a été envoyé avec succès à '''$2''' {{PLURAL:$2|destinataire|destinataires}} par [[User:$3|$3]].",
	'ea-selectrecipients' => 'Sélectionner les destinataires',
	'ea-compose'          => 'Composer le contenu',
	'ea-selectlist'       => "Destinataires supplémentaires comme les titres d'articles ou les adresses courriel
* ''séparer les articles avec , : * \\n''
* ''la liste peut contenir des modèles et des fonctions parseurs''",
	'ea-show'             => 'Visionner les destinataires',
	'ea-send'             => 'Envoyer !',
	'ea-subject'          => 'Entrer une ligne « objet » pour le courriel',
	'ea-header'           => 'Ajouter le contenu au début avec un message facultatif (texte wiki)',
	'ea-selectcss'        => 'Sélectionner une feuille de style CSS',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'emailarticle'        => 'Säit per Mail schécken',
	'ea-heading'          => '=== Säit [[$1]] peer E-Mail verschécken ===',
	'ea-norecipients'     => 'Keng gëlteg E-Mailadress fonnt',
	'ea-selectrecipients' => 'Adressaten erauswielen',
	'ea-show'             => 'Adressate weisen',
	'ea-send'             => 'Schécken!',
	'ea-selectcss'        => "Een ''CSS Stylesheet'' auswielen",
);

/** Malayalam (മലയാളം)
 * @author Shijualex
 */
$messages['ml'] = array(
	'emailarticle'        => 'ഇമെയില്‍ താള്‍',
	'ea-heading'          => '=== [[$1]] എന്ന താള്‍ ഇമെയില്‍ ചെയ്യുന്നു ===',
	'ea-noarticle'        => 'അയക്കുവാന്‍ വേണ്ടി ഒരു താള്‍ തിരഞ്ഞെടുക്കുക. ഉദാ: [[Special:EmailArticle/Main Page]]',
	'ea-norecipients'     => 'സാധുവായ ഇമെയില്‍ വിലാസങ്ങള്‍ കണ്ടില്ല!',
	'ea-listrecipients'   => '=== $1 {{PLURAL:$1|സ്വീകര്‍ത്താവിന്റെ|സ്വീകര്‍ത്താക്കളുടെ}} പട്ടിക ===',
	'ea-sent'             => "[[User:$3|$3]] എന്ന ഉപയോക്താവ് [[$1]] എന്ന താള്‍ വിജയകരമായി '''$2''' {{PLURAL:$2|സ്വീകര്‍ത്താവിനു|സ്വീകര്‍ത്താക്കള്‍ക്ക്}} അയച്ചിരിക്കുന്നു.",
	'ea-selectrecipients' => 'സ്വീകര്‍ത്താക്കളെ‍ തിരഞ്ഞെടുക്കുക',
	'ea-compose'          => 'ഉള്ളടക്കം ചേര്‍ക്കുക',
	'ea-show'             => 'സ്വീകര്‍ത്താക്കളെ പ്രദര്‍ശിപ്പിക്കുക',
	'ea-send'             => 'അയക്കൂ!',
	'ea-subject'          => 'ഇമെയിലിനു ഒരു വിഷയം/ശീര്‍ഷകം ചേര്‍ക്കുക',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'emailarticle'        => 'Pagina e-mailen',
	'ea-desc'             => 'Stuur een gerenderde pagina naar een e-mailadres of een lijst van adressen met behulp van [http://phpmailer.sourceforge.net phpmailer].',
	'ea-heading'          => '=== Pagina [[$1]] e-mailen ===',
	'ea-noarticle'        => 'Geef een pagina op om te versturen, bijvoorbeeld [[Special:EmailArticle/Hoofdpagina]].',
	'ea-norecipients'     => 'Er is geen geldig e-mailadres opgegeven!',
	'ea-listrecipients'   => '=== Lijst met $1 {{PLURAL:$1|ontvanger|ontvangers}} ===',
	'ea-error'            => "'''Fout bij het versturen van [[$1]]:''' ''$2''",
	'ea-sent'             => "Pagina [[$1]] is verstuurd naar '''$2''' {{PLURAL:$2|ontvanger|ontvangers}} door [[User:$3|$3]].",
	'ea-selectrecipients' => 'Ontvangers selecteren',
	'ea-compose'          => 'Inhoud samenstellen',
	'ea-selectlist'       => 'Meer ontvangers als paginanamen of e-mailadressen
*\'\'u kunt adressen scheiden met  ",", ";", "*", of "\\n"
*\'\'de lijst mag sjablonen en parserfuncties bevatten\'\'',
	'ea-show'             => 'Ontvangers weergeven',
	'ea-send'             => 'Versturen',
	'ea-subject'          => 'Voer een onderwerp in voor de e-mail',
	'ea-header'           => 'Laat de pagina-inhoud vooraf gaan door een bericht (in wikitekst)',
	'ea-selectcss'        => 'Selecteer een CSS',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'emailarticle'        => 'Mandar l’article per corrièr electronic',
	'ea-desc'             => 'Manda lo rendut d’una pagina HTML a una adreça electronica o a una tièra d’adreças en utilizant [http://phpmailer.sourceforge.net phpmailer]',
	'ea-heading'          => '=== Mandadís de la pagina [[$1]] per corrièr electronic ===',
	'ea-noarticle'        => 'Especificatz una pagina de mandar, per exemple [[Special:EmailArticle/Acuèlh]]',
	'ea-norecipients'     => "Cap d'adreça de corrièr electronic pas trobada !",
	'ea-listrecipients'   => '=== Tièra de $1 {{PLURAL:$1|destinatari|destinataris}} ===',
	'ea-error'            => "'''Error del mandadís de [[$1]] :''' ''$2''",
	'ea-sent'             => "L'article [[$1]] es estat mandat amb succès a '''$2''' {{PLURAL:$2|destinatari|destinataris}} per [[User:$3|$3]].",
	'ea-selectrecipients' => 'Seleccionar los destinataris',
	'ea-compose'          => 'Compausar lo contengut',
	'ea-selectlist'       => "Destinataris suplementaris coma los títols d'articles o las adreças de corrièr electronic
* ''separar los articles amb , : * \\n''
* ''la tièra pòt conténer de modèls e de foncions parsaires''",
	'ea-show'             => 'Visionar los destinataris',
	'ea-send'             => 'Mandar !',
	'ea-subject'          => 'Entrar una linha « objècte » pel corrièr electronic',
	'ea-header'           => 'Apondre lo contengut al començament amb un messatge facultatiu (tèxt wiki)',
	'ea-selectcss'        => "Seleccionar un fuèlh d'estil CSS",
);

