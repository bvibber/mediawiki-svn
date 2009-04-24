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
	'rt-badlimit'     => 'Invalid LIMIT (l) arg: must be a number. You tried: <b>$1</b>',
	'rt-badorderby'   => 'Invalid ORDER BY (ob) arg: must be a standard field (see documentation). You tried: <b>$1</b>',
	'rt-badstatus'    => 'Invalid status (s) arg: must be a standard field (see documentation). You tried: <b>$1</b>',
	'rt-badqueue'     => 'Invalid queue (q) arg: must be a simple word. You tried: <b>$1</b>',
	'rt-badowner'     => 'Invalid owner (o) arg: must be a valud username. You tried: <b>$1</b>',
	'rt-nomatches'    => 'No matching RT tickets were found',
);
