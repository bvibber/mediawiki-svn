<?php

/**
 * Internationalisation file for CountEdits extension
 *
 * @addtogroup Extensions
 * @author Bertrand Grondin <bertrand.grondin@tiscali.fr>
 */

function efAsksqlMessages() {
	$messages = array(

/* English (Rob Church) */
'en' => array(
	'asksql' => 'SQL query',
	'asksqltext' => "Use the form below to make a direct query of the
database.
Use single quotes ('like this') to delimit string literals.
This can often add considerable load to the server, so please use
this function sparingly.",
	'sqlislogged' => 'Please note that all queries are logged.',
	'sqlquery' => 'Enter query',
	'querybtn' => 'Submit query',
	'selectonly' => 'Only read-only queries are allowed.',
	'querysuccessful' => 'Query successful',),

/*French (Bertrand Grondin) */
'fr' => array(
	'asksql' => 'Requête SQL',
	'asksqltext' => "Utilisez ce formulaire pour faire une requête directe dans la base de donnée.
Utilisez les apostrophes ('comme ceci') pour les chaînes de caractères. Ceci peut souvent surcharger le serveur. Aussi, utilisez cette fonction avec parcimonie.",
	'sqlislogged' => 'Notez bien que toutes les requêtes sont journalisées.',
	'sqlquery' => 'Entrez la requête',
	'querybtn' => 'Soumettre la requête',
	'selectonly' => 'Seules les requêtes en lectures seules sont permises.',
	'querysuccessful' => 'La requête a été exécutée avec succès.',),
	);

	return $messages;
}

?>
