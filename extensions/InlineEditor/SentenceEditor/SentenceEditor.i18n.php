<?php
/**
 * Internationalisation file for extension SentenceEditor.
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Jan Paul Posma
 */
$messages['en'] = array(
	'sentence-editor-desc' => 'Adds the "Text" edit mode for the InlineEditor',

	'sentence-editor-editmode-caption' => "Text",
/*
	'sentence-editor-editmode-description' => "
There are a few [http://en.wikipedia.org/wiki/Wikipedia:Simplified_ruleset guidelines] for editing an article:<br/>
* Write what you think is best for the article, or as we say here: [http://en.wikipedia.org/wiki/Wikipedia:Be_bold be bold when updating pages]! If you feel that a rule prevents you from improving Wikipedia, [http://en.wikipedia.org/wiki/Wikipedia:Ignore_all_rules ignore it].
* [http://en.wikipedia.org/wiki/Wikipedia:What_Wikipedia_is_not Wikipedia is an encyclopedia.] Someone else should be able to [http://en.wikipedia.org/wiki/Wikipedia:Verifiability verify] what you've written, for example in books or online.
* Write from a [http://en.wikipedia.org/wiki/Wikipedia:Neutral_point_of_view neutral point of view], and use your [http://en.wikipedia.org/wiki/Wikipedia:Copyrights own words].
	",
*/
	'sentence-editor-editmode-description' => "
Edit sentences by clicking on them. You can use wiki syntax to format the text. Some examples:
{| width=\"100%\" style=\"background-color: inherit\"
! Code
! Output
|-
| <code><nowiki>Here's a link to the [[Main Page]].</nowiki></code>
| Here's a link to the [[Main Page]].
|-
| <code><nowiki>This is ''italic text'' and this is '''bold text'''.</nowiki></code>
| This is ''italic text'' and this is '''bold text'''.
|- 
| <code><nowiki>[http://meta.wikimedia.org/wiki/Help:Editing More information]</nowiki></code>
| [http://meta.wikimedia.org/wiki/Help:Editing More information]
|}
	",
);