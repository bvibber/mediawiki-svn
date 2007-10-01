<?php
/**
 * SpecialForm.i18n.php -- I18N for form-based interface to start new articles
 * Copyright 2007 Vinismo, Inc. (http://vinismo.com/)
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @author Evan Prodromou <evan@vinismo.com>
 * @addtogroup Extensions
 */

if (!defined('MEDIAWIKI')) {
	exit( 1 );
}

$SpecialFormMessages =array(
	'en' => array(
		'form' => 'Form',
		'formnoname' => 'No Form Name',
		'formnonametext' => 'You must provide a form name, like "Special:Form/Nameofform".',
		'formbadname' => 'Bad Form Name',
		'formbadnametext' => 'There is no form by that name.',
		'formpattern' => '$1-form',
		'formtemplatepattern' => '$1',
		'formtitlepattern' => 'Add New $1',
		'formsave' => 'Save',
		'formarticleexists' => 'Article Exists',
		'formarticleexiststext' => 'The article [[$1]] already exists.',
		'formsavesummary' => 'New article using [[Special:Form/$1]]',
		'formsaveerror' => 'Error Saving Form',
		'formsaveerrortext' => 'There was an unknown error saving article \'$1\'.',
	),

	'fr' => array(
		'form' => 'Formule',
	),

	'nl' => array(
		'form' => 'Formulier',
		'formnoname' => 'Geen formuliernaam',
	),
);

?>