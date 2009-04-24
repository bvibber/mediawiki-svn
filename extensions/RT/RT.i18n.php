<?php
/**
 * Internationalisation file for the RT extension.
 *
 * @ingroup Extensions
 */

/**
 * Get all extension messages
 *
 * @return array
 */

$messages = array();

/** English
 *  Greg Sabino Mullane <greg@endpoint.com>
 */
$messages['en'] = array(
	'rt-desc'         => 'Fancy interface to RT (Request Tracker)',
	'rt-inactive'     => 'The RT extension is not active',
	'rt-badquery'     => 'The RT extension encountered an error when talking to the RT database',
	'rt-badlimit'     => "Invalid LIMIT (l) arg: must be a number.
You tried: '''\$1'''",
	'rt-badorderby'   => "Invalid ORDER BY (ob) arg: must be a standard field (see documentation).
You tried: '''\$1'''",
	'rt-badstatus'    => "Invalid status (s) arg: must be a standard field (see documentation).
You tried: '''\$1'''",
	'rt-badqueue'     => "Invalid queue (q) arg: must be a simple word.
You tried: '''\$1'''",
	'rt-badowner'     => "Invalid owner (o) arg: must be a valid username.
You tried: '''\$1'''",
	'rt-nomatches'    => 'No matching RT tickets were found',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'rt-desc' => 'Interface naar RT (Request Tracker)',
	'rt-inactive' => 'De uitbreiding RT is niet actief',
	'rt-badquery' => 'In de uitbreiding RT is een fout opgetreden in de communicatie met de RT-database',
	'rt-badlimit' => "Ongeldige LIMIET (l) arg: moet een getal zijn.
U hebt het volgende geprobeerd: '''$1'''",
	'rt-badorderby' => "Ongeldige ORDER BY (ob) arg: moet een standaard veld zijn (zie documentatie).
U hebt het volgende geprobeerd: '''$1'''",
	'rt-badstatus' => "Ongeldige status (s) arg: moet een standaard veld zijn (zie documentatie).
U hebt het volgende geprobeerd: '''$1'''",
	'rt-badqueue' => "Ongeldige wachtrij (q) arg: moet een eenvoudig woord zijn.
U hebt het volgende geprobeerd: '''$1'''",
	'rt-badowner' => "Ongeldige eigenaar (o) arg: moet een geldige gebruikersnaam zijn.
U hebt het volgende geprobeerd: '''$1'''",
	'rt-nomatches' => 'Er zijn geen RT-meldingen gevonden die aan de critera voldoen',
);

