<?php
/**
 * Internationalisation file for extension ListEditor.
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Jan Paul Posma
 */
$messages['en'] = array(
	'list-editor-desc' => 'Adds the "Lists" edit mode for the InlineEditor',

	'list-editor-editmode-caption' => "Lists",
	'list-editor-editmode-description' => "
Lists work like shown below. 
You can also make a new list by going in any other mode, like the '''Text''' mode,
and add a list like the one below. 

{| width=\"100%\" style=\"background-color: inherit\"
! Code
! Output
|-
|
<code><nowiki>* Lists are easy to do:</nowiki></code><br/>
<code><nowiki>** start every line</nowiki></code><br/>
<code><nowiki>* with a star</nowiki></code><br/>
<code><nowiki>** more stars mean</nowiki></code><br/>
<code><nowiki>*** deeper levels</nowiki></code><br/>
|
* Lists are easy to do:
** start every line
* with a star
** more stars mean
*** deeper levels
|}

[http://meta.wikimedia.org/wiki/Help:List More information]
	",
);