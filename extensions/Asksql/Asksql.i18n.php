<?php

/**
 * Internationalisation file for Asksql extension
 *
 * @addtogroup Extensions
 * @author Bertrand Grondin <bertrand.grondin@tiscali.fr>
 */

function efAsksqlMessages() {
	return array(

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
'querysuccessful' => 'Query successful',
),

/*French (Bertrand Grondin) */
'fr' => array(
'asksql' => 'Requête SQL',
'asksqltext' => "Utilisez ce formulaire pour faire une requête directe dans la base de donnée.
Utilisez les apostrophes ('comme ceci') pour les chaînes de caractères. Ceci peut souvent surcharger le serveur. Aussi, utilisez cette fonction avec parcimonie.",
'sqlislogged' => 'Notez bien que toutes les requêtes sont journalisées.',
'sqlquery' => 'Entrez la requête',
'querybtn' => 'Soumettre la requête',
'selectonly' => 'Seules les requêtes en lectures seules sont permises.',
'querysuccessful' => 'La requête a été exécutée avec succès.',
),

/* Indonesian (Ivan Lanin) */
'id' => array(
'asksql' => 'Kueri SQL',
'asksqltext' => "Gunakan isian berikut untuk melakukan kueri langsung ke basis data. Gunakan kutip tunggal ('seperti ini') untuk membatasi literal string. Hal ini cukup membebani server, jadi gunakanlah fungsi ini secukupnya.",
'sqlislogged' => 'Ingatlah bahwa semua kueri akan dicatat.',
'sqlquery' => 'Masukkan kueri',
'querybtn' => 'Kirim',
'selectonly' => 'Hanya kueri baca-saja yang diijinkan.',
'querysuccessful' => 'Kueri berhasil',
),

/* nld / Dutch (Siebrand Mazeland) */
'nl' => array(
'asksql' => 'SQL query',
'asksqltext' => "Gebruik het onderstaande formulier om direct een query op de database te maken.
Gebruik apostrofs ('zo dus') als delimiter voor strings.
Dit kan zorgen voor zware belasting van de server, gebruik deze functie dus spaarzaam.",
'sqlislogged' => 'Alle query\'s worden in een logboek opgeslagen.',
'sqlquery' => 'Voer query in',
'querybtn' => 'Voer query uit',
'selectonly' => 'U kunt slechts alleen-lezen query\'s uitvoeren.',
'querysuccessful' => 'Query uitgevoerd',
),

	);
}

?>
