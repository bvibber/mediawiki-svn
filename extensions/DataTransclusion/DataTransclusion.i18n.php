<?php
/**
 * Internationalisation file for the extension DataTransclusion
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Daniel Kinzler for Wikimedia Deutschland
 * @copyright © 2010 Wikimedia Deutschland (Author: Daniel Kinzler)
 * @licence GNU General Public Licence 2.0 or later
 */

$messages = array();

/** English
 */
$messages['en'] = array(
	'datatransclusion-desc'         => 'Import and rendering of data records from external data sources',

	'datatransclusion-missing-source'            => 'no data source specified (first argument is required)',
	'datatransclusion-unknown-source'            => 'bad data source specified ($1 is not known)',
	'datatransclusion-bad-argument-by'           => 'bad key field specified ($2 is not a key field in data source $1, valid keys are: $3)',
	'datatransclusion-missing-argument-key'      => 'no key value specified (second or "key" argument is required)',
	'datatransclusion-missing-argument-template' => 'no template specified (third or "template" argument is required)',
	'datatransclusion-record-not-found'          => 'no record matching $2 = $3 was found in data source $1',
	'datatransclusion-bad-template-name'         => 'bad template name: $1',
	'datatransclusion-unknown-template'          => '<nowiki>{{</nowiki>[[Template:$1|$1]]<nowiki>}}</nowiki> does not exist.',
);

/** Message documentation (Message documentation)
 */
$messages['qqq'] = array(
	'pageby-desc' => 'Shown in [[Special:Version]] as a short description of this extension. Do not translate links.',

	'datatransclusion-missing-source'            => 'issued if no data source was specified.',
	'datatransclusion-unknown-source'            => 'issued if an unknown data source was specified. $1 is the name of the data source.',
	'datatransclusion-bad-argument-by'           => 'issued if a bad value was specified for the "by" argument, that is, an unknown key field was selected. $1 is the name of the data source, $2 is the value of the by argument, $3 is a list of all valid keys for this data source.',
	'datatransclusion-missing-argument-key'      => 'issued if no "key" or second positional argument was given provided. A key value is always required.',
	'datatransclusion-missing-argument-template' => 'issued if no "template" or third positional argument was given provided. A target template is always required.',
	'datatransclusion-record-not-found'          => 'issued if the record specified using the "by" and "key" arguments was nout found in the data source. $1 is the name of the data source, $2 is the key filed used, and $3 is the key value to select by.',
	'datatransclusion-bad-template-name'         => 'issued if the template name specified is not valid. $1 is the given template name.',
	'datatransclusion-unknown-template'          => 'issued if the template specified does not exist. $1 is the given template name.',
);
