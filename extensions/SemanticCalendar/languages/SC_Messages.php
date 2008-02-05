<?php
/**
 * Internationalization file for the Semantic Calendar extension
 *
 * @addtogroup Extensions
*/

$messages = array();

/** English
 * @author Yaron Koren
 */
$messages['en'] = array(
	// user messages
        'sc_previousmonth' => 'Previous month',
        'sc_nextmonth' => 'Next month',
	'sc_error_year2038' => 'Error: This system cannot handle dates after 2038, due to the [http://en.wikipedia.org/wiki/Year_2038_problem year 2038 problem]',
	'sc_error_beforeyear' => 'Error: This system cannot handle dates before $1',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'sc_previousmonth'    => 'Vorige maand',
	'sc_nextmonth'        => 'Volgende maand',
	'sc_error_year2038'   => 'Fout: dit systeem kan geen datums verwerken na 2038 vanwege het [http://en.wikipedia.org/wiki/Year_2038_problem 2038-probleem]',
	'sc_error_beforeyear' => 'Fout: dit systeem kan geen datums verwerken voor $1',
);

