<?php
/**
 * SpecialForm.i18n.php -- I18N for form-based interface to start new pages
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
		'formarticleexists' => 'Page Exists',
		'formarticleexiststext' => 'The page [[$1]] already exists.',
		'formbadpagename' => 'Bad Page Name',
		'formbadpagenametext' => 'The form data you entered makes a bad page name, "$1".',
		'formrequiredfieldpluralerror' => 'The fields $1 are required for this form. Please fill them in.',				  
		'formrequiredfielderror' => 'The field $1 is required for this form. Please fill it in.',
		'formsavesummary' => 'New page using [[Special:Form/$1]]',
		'formsaveerror' => 'Error Saving Form',
		'formsaveerrortext' => 'There was an unknown error saving page \'$1\'.',
	),

	'ar' => array(
		'form' => 'استمارة',
		'formnoname' => 'لا اسم استمارة',
		'formnonametext' => 'يجب أن توفر اسم استمارة، مثل "Special:Form/Nameofform".',
		'formbadname' => 'اسم استمارة سيء',
		'formbadnametext' => 'لا توجد استمارة بهذا الاسم.',
		'formpattern' => '$1-استمارة',
		'formtemplatepattern' => '$1',#identical but defined
		'formtitlepattern' => 'أضف $1 جديدا',
		'formsave' => 'حفظ',
		'formarticleexists' => 'الصفحة موجودة',
		'formarticleexiststext' => 'الصفحة [[$1]] موجودة بالفعل.',
		'formbadpagename' => 'اسم صفحة سيء',
		'formbadpagenametext' => 'بيانات الاستمارة التي أدخلتها تصنع اسم صفحة سيئا، "$1".',
		'formrequiredfieldpluralerror' => 'الحقول $1 مطلوبة لهذه الاستمارة. من فضلك املأهم.',
		'formrequiredfielderror' => 'الحقل $1 مطلوب لهذه الاستمارة. من فضلك املأه.',
		'formsavesummary' => 'صفحة جديدة باستخدام [[Special:Form/$1]]',
		'formsaveerror' => 'خطأ في حفظ الاستمارة',
		'formsaveerrortext' => 'حدث خطأ غير معروف أثناء حفظ الاستمارة \'$1\'.',
	),

	'bcl' => array(
		'formsave' => 'Itagama',
	),

	'de' => array(
		'form'                         => 'Formular',
		'formnoname'                   => 'Kein Formularname',
		'formnonametext'               => 'Du musst einen Formularnamen angeben, z.B. „{{ns:Special}}:Form/Formularname“.',
		'formbadname'                  => 'Falscher Formularname',
		'formbadnametext'              => 'Es gibt kein Formular mit diesem Namen',
		'formpattern'                  => '$1-Formular',
		'formtitlepattern'             => 'Füge neue $1 hinzu',
		'formsave'                     => 'Speichern',
		'formarticleexists'            => 'Seite bereits vorhanden',
		'formarticleexiststext'        => 'Die Seite „[[$1]]“ ist bereits vorhanden.',
		'formbadpagename'              => 'Unzulässiger Seitenname',
		'formbadpagenametext'          => 'Die eingegebenen Formulardaten erzeugen einen unzulässigen Seitennamen: „$1“.',
		'formrequiredfieldpluralerror' => 'Die Felder $1 sind Pflichtfelder. Bitte fülle sie aus.',				  
		'formrequiredfielderror'       => 'Das Feld $1 ist ein Pfichtfeld. Bitte fülle es aus.',
		'formsavesummary'              => 'Neue Seite, die auf [[{{ns:Special}}:Form/$1]] basiert',
		'formsaveerror'                => 'Fehler beim Speichern des Formulares',
		'formsaveerrortext'            => 'Es gab einen unbekannten Fehler beim Speichern der Seite „$1“.',
	),

	'fr' => array(
        'form' => 'Formulaire',
		'formnoname' => 'Aucun nom',
		'formnonametext' => 'Veuillez spécifier le nom du formulaire, sous la forme "Special:Formulaire/NomDuFormulaire".',
		'formbadname' => 'Nom incorrect',
		'formbadnametext' => 'Le nom choisi pour le formulaire est incorrect. Aucun formulaire n\'existe sous ce nom.',
		'formpattern' => 'formulaire-$1',
		'formtemplatepattern' => '$1',
		'formtitlepattern' => 'Ajouter un(e) $1',
		'formsave' => 'Sauvegarder',
		'formarticleexists' => 'L\'article existe déjà',
		'formarticleexiststext' => 'L\'article nommé [[$1]] existe déjà.',
		'formsavesummary' => 'Créer un nouvel article à l\'aide de [[Special:Formulaire/$1]]',
		'formsaveerror' => 'Une erreur s\'est produite pendant la sauvegarde.',
		'formsaveerrortext' => 'Une erreur inconnue s\'est produite pendant la sauvegarde de l\'article \'$1\'.'
	),

	'hsb' => array(
		'form' => 'Formular',
		'formnoname' => 'Žane formularne mjeno',
		'formnonametext' => 'Dyrbiš formularne mjeno podać, na př. „{{ns:Special}}:Form/Formularnemjeno“.',
		'formbadname' => 'Wopačne formularne mjeno',
		'formbadnametext' => 'Njeje formular z tutym mjenom',
		'formpattern' => '$1 formular',
		'formtemplatepattern' => '$1',#identical but defined
		'formtitlepattern' => 'Nowe $1 přidać',
		'formsave' => 'Składować',
		'formarticleexists' => 'Nastawk hižo eksistuje',
		'formarticleexiststext' => 'Nastawk [[$1]] hižo eksistuje.',
		'formbadpagename' => 'Njedowolene mjeno strony',
		'formbadpagenametext' => 'Zapodate formularne daty tworja njedowolene mjeno strony: "$1".',
		'formrequiredfieldpluralerror' => 'Pola $1 su trěbne pola. Prošu wupjelń je.',
		'formrequiredfielderror' => 'Polo $1 je trěbne polo. Prošu wupjelń je.',
		'formsavesummary' => 'Nowy nastawk, kotryž na [{{ns:Special}}:Form/$1]] bazěruje.',
		'formsaveerror' => 'Zmylk při składowanju formulara',
		'formsaveerrortext' => 'Bě njeznaty zmylk při składowanju nastawka "$1".',
	),

	'la' => array(
		'formsave' => 'Servare',
		'formarticleexiststext' => 'Pagina [[$1]] iam existit.',
	),

	'nl' => array(
		'form' => 'Formulier',
		'formnoname' => 'Geen formuliernaam',
		'formnonametext' => 'Geef een formuliernaam op, bijvoorbeeld "Special:Form/Formuliernaam".',
		'formbadname' => 'Ongeldige formuliernaam',
		'formbadnametext' => 'Er bestaat geen formulier met die naam.',
		'formpattern' => '$1-form',#identical but defined
		'formtemplatepattern' => '$1',#identical but defined
		'formtitlepattern' => 'Voeg nieuw $1 toe',
		'formsave' => 'Opslaan',
		'formarticleexists' => 'Pagina bestaat al',
		'formarticleexiststext' => 'De pagina [[$1]] bestaat al.',
		'formbadpagename' => 'Onjuiste paginanaam',
		'formbadpagenametext' => 'De formuliergegevens die u hebt opgegeven zorgen voor een onjuiste pagina, "$1".',
		'formrequiredfieldpluralerror' => 'De velden $1 zijn verplicht voor dit formulier. Vul ze alstublieft in.',
		'formrequiredfielderror' => 'Het veld $1 is verplicht voor dit formulier. Vul het alstublieft in.',
		'formsavesummary' => 'Nieuwe pagina via [[Special:Form/$1]]',
		'formsaveerror' => 'Fout bij opslaan formulier',
		'formsaveerrortext' => 'Er is een onbekende fout opgetreden bij het opslaan van pagina \'$1\'.',
	),
);