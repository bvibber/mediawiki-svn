<?php
/**
 * Internationalization file for the Storyboard extension.
 *
 * @file Storyboard.i18n.php
 * @ingroup Storyboard
 *
 * @author Jeroen De Dauw
 */

$messages = array();

/** English
 * @author Jeroen De Dauw
 */
$messages['en'] = array(
	// General
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Provides a [[Special:Story|landing page for donors]], a page where stories can be [[Special:StorySubmission|submitted]] and a [[Special:StoryReview|story moderation interface]]',
	'right-storyreview' => 'Review, edit, publish, and hide stories',

	// Story states
	'storyboard-unpublished' => 'Unpublished',
	'storyboard-published' => 'Published',
	'storyboard-hidden' => 'Hidden',
	'storyboard-unpublish' => 'Unpublish',
	'storyboard-publish' => 'Publish',
	'storyboard-hide' => 'Hide',

	'storyboard-option-unpublished' => 'unpublished',
	'storyboard-option-published' => 'published',
	'storyboard-option-hidden' => 'hidden',

	// Special:Story
	'story' => 'Story',
	'storyboard-submittedbyon' => 'Submitted by $1 on $2, $3.',
	'storyboard-viewstories' => 'View stories',
	'storyboard-nosuchstory' => 'The story you requested does not exist.
It might have been removed.',
	'storyboard-storyunpublished' => 'The story you requested has not been published yet.',
	'storyboard-nostorytitle' => 'You need to specify the title or ID of the story you want to view.',
	'storyboard-cantedit' => 'You are not allowed to edit stories.',
	'storyboard-canedit' => 'You can [$1 edit] and publish this story.',
	'storyboard-createdandmodified' => 'Created on $1, $2 and last modified on $3, $4',
	'storyboard-authorname' => 'Author name',
	'storyboard-authorlocation' => 'Author location',
	'storyboard-authoroccupation' => 'Author occupation',
	'storyboard-authoremail' => 'Author e-mail address',
	'storyboard-thestory' => 'The story',
	'storyboard-storystate' => 'State:',

	// Storyboard tag
	'storyboard-storymetadata' => 'Submitted by $1 from $2 on $3, $4.' ,

	// Special:StorySubmission
	'storyboard-submissioncomplete' => 'Submission complete',

	// Story review
	'storyreview' => 'Story review',
	'storyboard-hideimage' => 'Hide image',
	'storyboard-unhideimage' => 'Show image',
	'storyboard-deleteimage' => 'Delete image',

	// Story submission
	'storyboard-yourname' => 'Your name',
	'storyboard-location' => 'Your location',
	'storyboard-occupation' => 'Your occupation',
	'storyboard-story' => 'Your story',
	'storyboard-photo' => 'Have a photo of yourself?
Why not share it?',
	'storyboard-email' => 'Your e-mail address',
	'storyboard-storytitle' => 'A short, descriptive title',
	'storyboard-agreement' => 'I agree with the publication and use of this story under the terms of the [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution/Share-Alike License].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|character|characters}} left)',
	'storyboard-cannotbelonger' => 'Your story is <b>$1</b> {{PLURAL:$1|character|characters}} too long!',
	'storyboard-charsneeded' => '($1 more {{PLURAL:$1|character|characters}} needed)',
	'storyboard-needtoagree' => 'You need to agree to the publication of your story to submit it.',
	'storyboard-createdsucessfully' => 'Thank you for sharing your story with us!
We will review it shortly.
You can [$1 read published stories].',
);

/** Message documentation (Message documentation)
 * @author EugeneZelenko
 */
$messages['qqq'] = array(
	'storyboard-hidden' => '{{Identical|Hidden}}',
	'storyboard-publish' => '{{Identical|Publish}}',
	'storyboard-hide' => '{{Identical|Hide}}',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'storyboard-name' => 'Storiebord',
	'storyboard-desc' => "Bied 'n landingsbladsy vir skenkers, 'n bladsy waar verhale ingestuur kan word en 'n koppelvlak om stories te beheer",
	'right-storyreview' => 'Hersien, wysig, publiseer en verberg stories',
	'storyboard-publish' => 'Publiseer',
);

/** Belarusian (TaraÅ¡kievica orthography) (Ð‘ÐµÐ»Ð°Ñ€ÑƒÑ�ÐºÐ°Ñ� (Ñ‚Ð°Ñ€Ð°ÑˆÐºÐµÐ²Ñ–Ñ†Ð°))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'storyboard-name' => 'Ð”Ð¾ÑˆÐºÐ° Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ�Ñž',
	'storyboard-desc' => 'ÐŸÑ€Ð°Ð´Ñ�Ñ‚Ð°ÑžÐ»Ñ�Ðµ [[Special:Story|Ñ�Ñ‚Ð°Ñ€Ð¾Ð½ÐºÑƒ]] Ð´Ð»Ñ� [[Special:StorySubmission|Ñ€Ð°Ð·ÑŒÐ¼Ñ�ÑˆÑ‡Ñ�Ð½ÑŒÐ½Ñ�]] Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ�Ñž Ð°Ñ…Ð²Ñ�Ñ€Ð°Ð²Ð°Ð»ÑŒÐ½Ñ–ÐºÐ°Ñž, Ð° Ñ‚Ð°ÐºÑ�Ð°Ð¼Ð° [[Special:StoryReview|Ñ–Ð½Ñ‚Ñ�Ñ€Ñ„Ñ�Ð¹Ñ� Ñ�Ðµ Ð¼Ð°Ð´Ñ�Ñ€Ð°Ñ†Ñ‹Ñ–]].',
	'right-storyreview' => 'Ñ€Ñ�Ñ†Ñ�Ð½Ð·Ð°Ð²Ð°Ð½ÑŒÐ½Ðµ, Ñ€Ñ�Ð´Ð°Ð³Ð°Ð²Ð°Ð½ÑŒÐ½Ðµ, Ð¿ÑƒÐ±Ð»Ñ–ÐºÐ°Ñ†Ñ‹Ñ� Ñ– Ñ…Ð°Ð²Ð°Ð½ÑŒÐ½Ðµ Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ�Ñž',
	'storyboard-unpublished' => 'Ð�ÐµÐ°Ð¿ÑƒÐ±Ð»Ñ–ÐºÐ°Ð²Ð°Ð½Ñ‹Ñ�',
	'storyboard-published' => 'Ð�Ð¿ÑƒÐ±Ð»Ñ–ÐºÐ°Ð²Ð°Ð½Ñ‹Ñ�',
	'storyboard-hidden' => 'Ð¡Ñ…Ð°Ð²Ð°Ð½Ñ‹Ñ�',
	'storyboard-unpublish' => 'ÐŸÑ€Ñ‹Ð±Ñ€Ð°Ñ†ÑŒ',
	'storyboard-publish' => 'Ð�Ð¿ÑƒÐ±Ð»Ñ–ÐºÐ°Ð²Ð°Ñ†ÑŒ',
	'storyboard-hide' => 'Ð¡Ñ…Ð°Ð²Ð°Ñ†ÑŒ',
	'story' => 'Ð“Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ�',
	'storyboard-submittedbyon' => 'Ð�Ð´Ð¿Ñ€Ð°ÑžÐ»ÐµÐ½Ð°Ñ� $1 $2, $3.',
	'storyboard-viewstories' => 'ÐŸÐ°ÐºÐ°Ð·Ð°Ñ†ÑŒ Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ–',
	'storyboard-nosuchstory' => 'Ð“Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ�, Ñ�ÐºÑƒÑŽ Ð’Ñ‹ Ð·Ð°Ð¿Ñ‹Ñ‚Ð°Ð»Ñ–, Ð½Ðµ Ñ–Ñ�Ð½ÑƒÐµ.
Ð’ÐµÑ€Ð°Ð³Ð¾Ð´Ð½Ð°, Ñ�Ð½Ð° Ð±Ñ‹Ð»Ð° Ð²Ñ‹Ð´Ð°Ð»ÐµÐ½Ð°Ñ�.',
	'storyboard-storyunpublished' => 'Ð“Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ�, Ñ�ÐºÑƒÑŽ Ð’Ñ‹ Ð·Ð°Ð¿Ñ‹Ñ‚Ð°Ð»Ñ–, Ñ�ÑˆÑ‡Ñ� Ð½Ðµ Ð±Ñ‹Ð»Ð° Ð°Ð¿ÑƒÐ±Ð»Ñ–ÐºÐ°Ð²Ð°Ð½Ð°Ñ�.',
	'storyboard-nostorytitle' => 'Ð’Ð°Ð¼ Ð½ÐµÐ°Ð±Ñ…Ð¾Ð´Ð½Ð° Ð¿Ð°Ð´Ð°Ñ†ÑŒ Ð½Ð°Ð·Ð²Ñƒ Ð°Ð»ÑŒÐ±Ð¾ Ñ–Ð´Ñ�Ð½Ñ‚Ñ‹Ñ„Ñ–ÐºÐ°Ñ‚Ð°Ñ€ Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ–, Ñ�ÐºÑƒÑŽ Ð’Ñ‹ Ð¶Ð°Ð´Ð°ÐµÑ†Ðµ Ð¿Ñ€Ð°Ð³Ð»Ñ�Ð´Ð·ÐµÑ†ÑŒ.',
	'storyboard-cantedit' => 'Ð’Ð°Ð¼ Ð½Ðµ Ð´Ð°Ð·Ð²Ð¾Ð»ÐµÐ½Ð° Ñ€Ñ�Ð´Ð°Ð³Ð°Ð²Ð°Ñ†ÑŒ Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ–.',
	'storyboard-canedit' => 'Ð’Ñ‹ Ð¼Ð¾Ð¶Ð°Ñ†Ðµ [$1 Ñ€Ñ�Ð´Ð°Ð³Ð°Ð²Ð°Ñ†ÑŒ] Ñ– Ð°Ð¿ÑƒÐ±Ð»Ñ–ÐºÐ°Ð²Ð°Ñ†ÑŒ Ð³Ñ�Ñ‚ÑƒÑŽ Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹ÑŽ.',
	'storyboard-createdandmodified' => 'Ð¡Ñ‚Ð²Ð¾Ñ€Ð°Ð½Ð°Ñ� $1, $2 Ñ– Ð°Ð¿Ð¾ÑˆÐ½Ñ– Ñ€Ð°Ð· Ð·ÑŒÐ¼Ñ�Ð½Ñ�Ð»Ð°Ñ�Ñ� $3, $4',
	'storyboard-authorname' => 'Ð†Ð¼Ñ� Ð°ÑžÑ‚Ð°Ñ€Ð°',
	'storyboard-authorlocation' => 'ÐœÐµÑ�Ñ†Ð°Ð·Ð½Ð°Ñ…Ð¾Ð´Ð¶Ð°Ð½ÑŒÐ½Ðµ Ð°ÑžÑ‚Ð°Ñ€Ð°',
	'storyboard-authoroccupation' => 'Ð Ð¾Ð´ Ð·Ð°Ð½Ñ�Ñ‚ÐºÐ°Ñž Ð°ÑžÑ‚Ð°Ñ€Ð°',
	'storyboard-authoremail' => 'Ð�Ð´Ñ€Ð°Ñ� Ñ�Ð»ÐµÐºÑ‚Ñ€Ð¾Ð½Ð½Ð°Ð¹ Ð¿Ð¾ÑˆÑ‚Ñ‹ Ð°ÑžÑ‚Ð°Ñ€Ð°',
	'storyboard-thestory' => 'Ð“Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ�',
	'storyboard-storystate' => 'Ð¡Ñ‚Ð°Ð½:',
	'storyboard-submissioncomplete' => 'Ð�Ð´Ð¿Ñ€Ð°ÑžÐºÐ° Ñ�ÐºÐ¾Ð½Ñ‡Ð°Ð½Ð°Ñ�',
	'storyreview' => 'Ð Ñ�Ñ†Ñ�Ð½Ð·Ð°Ð²Ð°Ð½ÑŒÐ½Ðµ Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ–',
	'storyboard-hideimage' => 'Ð¡Ñ…Ð°Ð²Ð°Ñ†ÑŒ Ð²Ñ‹Ñ�Ð²Ñƒ',
	'storyboard-unhideimage' => 'ÐŸÐ°ÐºÐ°Ð·Ð°Ñ†ÑŒ Ð²Ñ‹Ñ�Ð²Ñƒ',
	'storyboard-deleteimage' => 'Ð’Ñ‹Ð´Ð°Ð»Ñ–Ñ†ÑŒ Ð²Ñ‹Ñ�Ð²Ñƒ',
	'storyboard-yourname' => 'Ð’Ð°ÑˆÐ° Ñ–Ð¼Ñ�',
	'storyboard-location' => 'Ð’Ð°ÑˆÐ° Ð¼ÐµÑ�Ñ†Ð°Ð·Ð½Ð°Ñ…Ð¾Ð´Ð¶Ð°Ð½ÑŒÐ½Ðµ',
	'storyboard-occupation' => 'Ð’Ð°Ñˆ Ñ€Ð¾Ð´ Ð·Ð°Ð½Ñ�Ñ‚ÐºÐ°Ñž',
	'storyboard-story' => 'Ð’Ð°ÑˆÐ° Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ�',
	'storyboard-photo' => 'Ð’Ñ‹ Ð¼Ð°ÐµÑ†Ðµ Ñ�Ð²Ð°Ñ‘ Ñ„Ð¾Ñ‚Ð°?
Ð§Ð°Ð¼Ñƒ Ð± Ñ�Ð³Ð¾ Ð½Ðµ Ñ€Ð°Ð·ÑŒÐ¼Ñ�Ñ�ÑŒÑ†Ñ–Ñ†ÑŒ?',
	'storyboard-email' => 'Ð�Ð´Ñ€Ð°Ñ� Ð’Ð°ÑˆÐ°Ð¹ Ñ�Ð»ÐµÐºÑ‚Ñ€Ð¾Ð½Ð½Ð°Ð¹ Ð¿Ð¾ÑˆÑ‚Ñ‹',
	'storyboard-storytitle' => 'ÐšÐ°Ñ€Ð¾Ñ‚ÐºÑ–, Ð°Ð¿Ñ–Ñ�Ð²Ð°ÑŽÑ‡Ñ‹ Ð·Ð°Ð³Ð°Ð»Ð¾Ð²Ð°Ðº',
	'storyboard-agreement' => 'Ð¯ Ð·Ð³Ð¾Ð´Ð½Ñ‹ Ð· Ð¿ÑƒÐ±Ð»Ñ–ÐºÐ°Ñ†Ñ‹Ñ�Ð¹ Ñ– Ð²Ñ‹ÐºÐ°Ñ€Ñ‹Ñ�Ñ‚Ð°Ð½ÑŒÐ½ÐµÐ¼ Ð³Ñ�Ñ‚Ð°Ð¹ Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ– Ð½Ð° ÑžÐ¼Ð¾Ð²Ð°Ñ… Ð»Ñ–Ñ†Ñ�Ð½Ð·Ñ–Ñ– [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution/Share-Alike License].',
	'storyboard-charsleft' => '({{PLURAL:$1|Ð·Ð°Ñ�Ñ‚Ð°ÑžÑ�Ñ� $1 Ñ�Ñ‹Ð¼Ð±Ð°Ð»ÑŒ|Ð·Ð°Ñ�Ñ‚Ð°Ð»Ð¾Ñ�Ñ� $1 Ñ�Ñ‹Ð¼Ð±Ð°Ð»Ñ–|Ð·Ð°Ñ�Ñ‚Ð°Ð»Ð¾Ñ�Ñ� $1 Ñ�Ñ‹Ð¼Ð±Ð°Ð»Ñ�Ñž}})',
	'storyboard-cannotbelonger' => 'Ð’Ð°ÑˆÐ° Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ� Ð´Ð°ÑžÐ¶Ñ�Ð¹ Ð½Ð° <b>$1</b> {{PLURAL:$1|Ñ�Ñ‹Ð¼Ð±Ð°Ð»ÑŒ|Ñ�Ñ‹Ð¼Ð±Ð°Ð»Ñ–|Ñ�Ñ‹Ð¼Ð±Ð°Ð»Ñ�Ñž}}!',
	'storyboard-charsneeded' => '({{PLURAL:$1|Ð½ÐµÐ°Ð±Ñ…Ð¾Ð´Ð½Ñ‹ Ñ�ÑˆÑ‡Ñ� $1 Ñ�Ñ‹Ð¼Ð±Ð°Ð»ÑŒ|Ð½ÐµÐ°Ð±Ñ…Ð¾Ð´Ð½Ñ‹Ñ� Ñ�ÑˆÑ‡Ñ� $1 Ñ�Ñ‹Ð¼Ð±Ð°Ð»Ñ–|Ð½ÐµÐ°Ð±Ñ…Ð¾Ð´Ð½Ñ‹Ñ� Ñ�ÑˆÑ‡Ñ� $1 Ñ�Ñ‹Ð¼Ð±Ð°Ð»Ñ�Ñž}})',
	'storyboard-needtoagree' => 'Ð’Ð°Ð¼ Ð½ÐµÐ°Ð±Ñ…Ð¾Ð´Ð½Ð° Ð¿Ð°Ð³Ð°Ð´Ð·Ñ–Ñ†Ñ†Ð° Ð½Ð° Ð¿ÑƒÐ±Ð»Ñ–ÐºÐ°Ñ†Ñ‹ÑŽ Ð’Ð°ÑˆÐ°Ð¹ Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ– Ð¿ÐµÑ€Ð°Ð´ Ñ�Ðµ Ð°Ð´Ð¿Ñ€Ð°ÑžÐºÐ°Ð¹.',
	'storyboard-createdsucessfully' => 'Ð”Ð·Ñ�ÐºÑƒÐ¹ Ð’Ð°Ð¼ Ð·Ð° Ñ‚Ð¾Ðµ, ÑˆÑ‚Ð¾ Ð¿Ð°Ð´Ð·Ñ�Ð»Ñ–Ð»Ñ–Ñ�Ñ� Ð· Ð½Ð°Ð¼Ñ– Ð’Ð°ÑˆÐ°Ð¹ Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ�Ð¹!
ÐœÑ‹ Ñ€Ð°Ð·Ð³Ð»ÐµÐ´Ð·Ñ–Ð¼ Ñ�Ðµ Ñž Ð±Ð»Ñ–Ð¶Ñ�Ð¹ÑˆÑ‹ Ñ‡Ð°Ñ�.
Ð’Ñ‹ Ð¼Ð¾Ð¶Ð°Ñ†Ðµ [$1 Ð¿Ð°Ñ‡Ñ‹Ñ‚Ð°Ñ†ÑŒ ÑƒÐ¶Ð¾ Ð°Ð¿ÑƒÐ±Ð»Ñ–ÐºÐ°Ð²Ð°Ð½Ñ‹Ñ� Ð³Ñ–Ñ�Ñ‚Ð¾Ñ€Ñ‹Ñ–].',
);

/** Breton (Brezhoneg)
 * @author Y-M D
 */
$messages['br'] = array(
	'storyboard-name' => 'Storyboard',
	'right-storyreview' => 'Adlenn, kemmaÃ±, embann, ha kuzhat an istorioÃ¹',
	'storyboard-unpublished' => 'Diembannet',
	'storyboard-published' => 'Embannet',
	'storyboard-hidden' => 'Kuzhet',
	'storyboard-unpublish' => 'Diembann',
	'storyboard-publish' => 'Embann',
	'storyboard-hide' => 'Kuzhat',
	'story' => 'Istor',
	'storyboard-submittedbyon' => "Kinniget gant $1 d'an $2, $3.",
	'storyboard-viewstories' => 'Gwelet an istorioÃ¹',
	'storyboard-authorname' => 'Anv an oberour',
	'storyboard-authorlocation' => "Lec'hiadur an oberour",
	'storyboard-authoroccupation' => 'Oberiantiz an oberour',
	'storyboard-authoremail' => "Chomlec'h postel an oberour",
	'storyboard-thestory' => 'An istor',
	'storyboard-storystate' => 'Stad :',
	'storyboard-hideimage' => 'Kuzhat ar skeudenn',
	'storyboard-unhideimage' => 'Diskouez ar skeudenn',
	'storyboard-deleteimage' => 'Dilemel ar skeudenn',
	'storyboard-yourname' => "Hoc'h anv",
	'storyboard-location' => "Ho lec'hiadur",
	'storyboard-occupation' => 'Ho micher',
	'storyboard-story' => 'Ho istor',
	'storyboard-photo' => "Ur poltred ouzhoc'h o peus ?
Perak chom hep rannaÃ± anezhi ?",
	'storyboard-email' => "Ho chomlec'h postel",
);

/** German (Deutsch)
 * @author Kghbln
 */
$messages['de'] = array(
	'storyboard-name' => 'Schwarzes Brett fÃ¼r Botschaften',
	'storyboard-desc' => 'Stellt eine [[Special:Story|Anlaufstelle]] fÃ¼r FÃ¶rderer, eine Seite auf der Botschaften [[Special:StorySubmission|eingereicht]], sowie eine Seite mit der diese [[Special:StoryReview|betreut]] werden kÃ¶nnen, zur VerfÃ¼gung.',
	'right-storyreview' => 'ÃœberprÃ¼fen, Bearbeiten, VerÃ¶ffentlichen und Verbergen von Botschaften',
	'storyboard-unpublished' => 'UnverÃ¶ffentlicht',
	'storyboard-published' => 'VerÃ¶ffentlicht',
	'storyboard-hidden' => 'Verborgen',
	'storyboard-unpublish' => 'VerÃ¶ffentlichung zurÃ¼ckziehen',
	'storyboard-publish' => 'VerÃ¶ffentlichen',
	'storyboard-hide' => 'Verbergen',
	'story' => 'Botschaft',
	'storyboard-submittedbyon' => 'Eingereicht von $1 am $2, $3.',
	'storyboard-viewstories' => 'Botschaften lesen',
	'storyboard-nosuchstory' => 'Die Botschaft, die du aufrufen wolltest, existiert nicht. Vielleicht wurde sie gelÃ¶scht.',
	'storyboard-storyunpublished' => 'Die Botschaft, die du aufrufen wolltest, wurde bislang noch nicht verÃ¶ffentlicht.',
	'storyboard-nostorytitle' => 'Du musst den Titel oder die Kennung der Botschaft angeben, die du lesen mÃ¶chtest.',
	'storyboard-cantedit' => 'Du hast nicht die Berechtigung Botschaften zu bearbeiten.',
	'storyboard-canedit' => 'Du kannst diese Botschaft [$1 bearbeiten] und verÃ¶ffentlichen.',
	'storyboard-createdandmodified' => 'Am $1, $2 erstellt und letztmalig am $3, $4 bearbeitet.',
	'storyboard-authorname' => 'Name des Autors',
	'storyboard-authorlocation' => 'Standort des Autors',
	'storyboard-authoroccupation' => 'Beruf des Autors',
	'storyboard-authoremail' => 'E-Mail-Adresse des Autors',
	'storyboard-thestory' => 'Die Botschaft',
	'storyboard-storystate' => 'Land:',
	'storyboard-submissioncomplete' => 'Die Einreichung ist abgeschlossen',
	'storyreview' => 'Botschaft Ã¼berprÃ¼fen',
	'storyboard-hideimage' => 'Bild verbergen',
	'storyboard-unhideimage' => 'Bild anzeigen',
	'storyboard-deleteimage' => 'Bild lÃ¶schen',
	'storyboard-yourname' => 'Dein Name',
	'storyboard-location' => 'Dein Standort',
	'storyboard-occupation' => 'Dein Beruf',
	'storyboard-story' => 'Deine Botschaft',
	'storyboard-photo' => 'Gibt es ein Foto von Dir? Was spricht dagegen es zu verÃ¶ffentlichen?',
	'storyboard-email' => 'Deine E-Mail-Adresse:',
	'storyboard-storytitle' => 'Ein kurzer, aussagekrÃ¤ftiger Titel',
	'storyboard-agreement' => 'Ich stimme der VerÃ¶ffentlichung und Nutzung dieser Botschaft unter den Bedingungen der Lizenz [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Namensnennung-Weitergabe unter gleichen Bedingungen] zu.',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|Anschlag|AnschlÃ¤ge}} verfÃ¼gbar)',
	'storyboard-cannotbelonger' => "Deine Botschaft ist '''$1''' {{PLURAL:$1|Anschlag|AnschlÃ¤ge}} zu lang!",
	'storyboard-charsneeded' => '($1 {{PLURAL:$1|weiterer|weitere}} {{PLURAL:$1|Anschlag|AnschlÃ¤ge}} notwendig)',
	'storyboard-needtoagree' => 'Du musst der VerÃ¶ffentlichung deiner Botschaft zustimmen, um sie einreichen zu kÃ¶nnen.',
	'storyboard-createdsucessfully' => 'Vielen Dank, dass du uns deine Botschaft mitgeteilt hast! Wir werden sie in KÃ¼rze Ã¼berprÃ¼fen.
Du kannst bereits verÃ¶ffentlichte Botschaften [$1 hier] lesen.',
);

/** German (formal address) (Deutsch (Sie-Form))
 * @author Kghbln
 */
$messages['de-formal'] = array(
	'storyboard-nosuchstory' => 'Die Botschaft, die Sie aufrufen wollten, existiert nicht. Vielleicht wurde sie gelÃ¶scht.',
	'storyboard-storyunpublished' => 'Die Botschaft, die Sie aufrufen wollten, wurde bislang noch nicht verÃ¶ffentlicht.',
	'storyboard-nostorytitle' => 'Sie mÃ¼ssen den Titel oder die Kennung der Botschaft angeben, die Sie lesen mÃ¶chten.',
	'storyboard-cantedit' => 'Sie haben nicht die Berechtigung Botschaften zu bearbeiten.',
	'storyboard-canedit' => 'Sie kÃ¶nnen diese Botschaft [$1 bearbeiten] und verÃ¶ffentlichen.',
	'storyboard-yourname' => 'Ihre Name',
	'storyboard-location' => 'Ihr Standort',
	'storyboard-occupation' => 'Ihr Beruf',
	'storyboard-story' => 'Ihre Botschaft',
	'storyboard-photo' => 'Gibt es ein Foto von Ihnen? Was spricht dagegen es zu verÃ¶ffentlichen?',
	'storyboard-email' => 'Ihre E-Mail-Adresse:',
	'storyboard-cannotbelonger' => "Ihre Botschaft ist '''$1''' {{PLURAL:$1|Anschlag|AnschlÃ¤ge}} zu lang!",
	'storyboard-needtoagree' => 'Sie mÃ¼ssen der VerÃ¶ffentlichung Ihrer Botschaft zustimmen, um sie einreichen zu kÃ¶nnen.',
	'storyboard-createdsucessfully' => 'Vielen Dank, dass Sie uns Ihre Botschaft mitgeteilt haben! Wir werden sie in KÃ¼rze Ã¼berprÃ¼fen.
Sie kÃ¶nnen bereits verÃ¶ffentlichte Botschaften [$1 hier] lesen.',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Bitujo [[Special:Story|bok pÅ›idostaÅ›a za pÃ³sÄ‡iwarjow]], bok, ÅºoÅ¾ tÅ¡ojenja daju se [[Special:StorySubmission|zawostajiÅ›]] a [[Special:StoryReview|pÃ³wjerch za moderaciju tÅ¡ojenjow]]',
	'right-storyreview' => 'TÅ¡ojenja pÅ›eglÄ›daÅ›, wobÅºÄ›Å‚aÅ›, wÃ³zjawiÅ› a schowaÅ›',
	'storyboard-unpublished' => 'NjewÃ³zjawjony',
	'storyboard-published' => 'WÃ³zjawjony',
	'storyboard-hidden' => 'Schowany',
	'storyboard-unpublish' => 'WÃ³zjawjenje slÄ›dk Å›Ä›gnuÅ›',
	'storyboard-publish' => 'WÃ³zjawiÅ›',
	'storyboard-hide' => 'SchowaÅ›',
	'storyboard-option-unpublished' => 'njewÃ³zjawjony',
	'storyboard-option-published' => 'wÃ³zjawjony',
	'storyboard-option-hidden' => 'schowany',
	'story' => 'TÅ¡ojenje',
	'storyboard-submittedbyon' => 'WÃ³t $1 dnja $2, $3 zawÃ³stajony.',
	'storyboard-viewstories' => 'TÅ¡ojenja se woglÄ›daÅ›',
	'storyboard-nosuchstory' => 'TÅ¡ojenje, kÃ³tareÅ¾ sy pominaÅ‚, njeeksistÄ›rujo.
MÃ³Å¾no, aÅ¾ jo se wÃ³tporaÅ‚o.',
	'storyboard-storyunpublished' => 'TÅ¡ojenje, kÃ³tareÅ¾ sy pominaÅ‚, hyÅ¡Ä‡i njejo wÃ³zjawjone.',
	'storyboard-nostorytitle' => 'MusyÅ›o titel abo ID tÅ¡ojenja, kÃ³tareÅ¾ coÅ›o se woglÄ›daÅ›, pÃ³daÅ›.',
	'storyboard-cantedit' => 'NjesmÄ›joÅ›o tÅ¡ojenja wobÅºÄ›Å‚as.',
	'storyboard-canedit' => 'MÃ³Å¾oÅ›o tÅ¡ojenje [$1 wobÅºÄ›Å‚aÅ›] a wÃ³zjawiÅ›.',
	'storyboard-createdandmodified' => 'Dnja $1, $2 napÃ³rane a dnja $3, $4 slÄ›dny raz zmÄ›njone.',
	'storyboard-authorname' => 'MÄ› awtora',
	'storyboard-authorlocation' => 'MÄ›stno awtora',
	'storyboard-authoroccupation' => 'PÃ³woÅ‚anje awtora',
	'storyboard-authoremail' => 'E-mailowa adresa awtora',
	'storyboard-thestory' => 'TÅ¡ojenje',
	'storyboard-storystate' => 'Stat:',
	'storyboard-submissioncomplete' => 'ZapÃ³daÅ›e dopoÅ‚ne',
	'storyreview' => 'PÅ›eglÄ›danje tÅ¡ojenja',
	'storyboard-hideimage' => 'Wobraz schowaÅ›',
	'storyboard-unhideimage' => 'Wobraz pokazaÅ›',
	'storyboard-deleteimage' => 'Wobraz wulaÅ¡owaÅ›',
	'storyboard-yourname' => 'WaÅ¡o mÄ›',
	'storyboard-location' => 'WaÅ¡o mÄ›stno',
	'storyboard-occupation' => 'WaÅ¡o pÃ³woÅ‚anje',
	'storyboard-story' => 'WaÅ¡o tÅ¡ojenje',
	'storyboard-photo' => 'MaÅ›o foto wÃ³t sebje?
Cogodla  njestajaÅ›o jo k naÅ¡ej dispoziciji?',
	'storyboard-email' => 'WaÅ¡a e-mailowa adresa',
	'storyboard-storytitle' => 'Krotki, wugroniwy titel',
	'storyboard-agreement' => 'ZwÃ³lijom do wÃ³zjawjenja a wuÅ¾ywanja toÅ› togo tÅ¡ojenja pÃ³d wumÄ›njenjami licence [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution/Share-Alike License].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|znamuÅ¡ko|znamuÅ¡ce|znamuÅ¡ka|znamuÅ¡kow}} wuÅ¡ej)',
	'storyboard-cannotbelonger' => 'WaÅ¡o tÅ¡ojenjo jo <b>$1</b> {{PLURAL:$1|znamuÅ¡ko|znamuÅ¡ce|znamuÅ¡ka|znamuÅ¡kow}} pÅ›edÅ‚ujko!',
	'storyboard-charsneeded' => '($1 {{PLURAL:$1|dalÅ¡ne znamuÅ¡ko trÄ›bne|dalÅ¡nej znamuÅ¡ce trÄ›bnej|dalÅ¡ne znamuÅ¡ka trÄ›bne|dalÅ¡nych znamuÅ¡kow trÄ›bnych}})',
	'storyboard-needtoagree' => 'MusyÅ›o do wÃ³zjawjenja swÃ³jogo tÅ¡ojenja zwÃ³liÅ›, aby wy jo zapÃ³daÅ‚.',
	'storyboard-createdsucessfully' => 'Å¹Ä›kujomy se wam, aÅ¾ sÄ‡o nam swÃ³jo tÅ¡ojenje k dispoziciji stajiÅ‚!
BuÅºomy se skÃ³ro pÅ›eglÄ›dowaÅ›.
MÃ³Å¾oÅ›o [$1 wÃ³zjawjone tÅ¡ojenja cytaÅ›].',
);

/** Spanish (EspaÃ±ol)
 * @author Crazymadlover
 * @author Tempestas
 */
$messages['es'] = array(
	'storyboard-name' => 'Panel histÃ³rico',
	'storyboard-desc' => 'Proporciona una [[Especial:Historia | PÃ¡gina de destino para los donantes]], una pÃ¡gina donde las historias pueden [[Especial:PresentacionHistoria | Ser presentadas]] y un [[Especial:RevisionHistoria | historia de la moderaciÃ³n de la interfaz]]',
	'right-storyreview' => 'Revisar, editar, publicar y ocultar historias.',
	'storyboard-unpublished' => 'InÃ©dito',
	'storyboard-published' => 'Publicado',
	'storyboard-hidden' => 'Oculto',
	'storyboard-unpublish' => 'No publicar',
	'storyboard-publish' => 'Publicar',
	'storyboard-hide' => 'Ocultar',
	'storyboard-option-unpublished' => 'Sin publicar',
	'storyboard-option-published' => 'Publicado',
	'storyboard-option-hidden' => 'Oculto',
	'story' => 'Historia',
	'storyboard-submittedbyon' => 'Enviado por $1 en $2, $3.',
	'storyboard-viewstories' => 'Ver historias.',
	'storyboard-nosuchstory' => 'La historia solicitada no existe.
Puede haber sido eliminada.',
	'storyboard-storyunpublished' => 'La historia solicitada aÃºn no ha sido publicada.',
	'storyboard-nostorytitle' => 'Necesita especificar el titulo o la ID de la historia que desea ver.',
	'storyboard-cantedit' => 'No tiene permiso para editar historias.',
	'storyboard-canedit' => 'Puede [$1 editar] y publicar esta historia.',
	'storyboard-createdandmodified' => 'Creado en $1, $2 y Ãºltima modificaciÃ³n en $3, $4',
	'storyboard-authorname' => 'Nombre de autor',
	'storyboard-authorlocation' => 'UbicaciÃ³n de autor',
	'storyboard-authoroccupation' => 'OcupaciÃ³n de autor',
	'storyboard-authoremail' => 'DirecciÃ³n de correo electrÃ³nico de autor',
	'storyboard-thestory' => 'La historia',
	'storyboard-storystate' => 'Estado:',
	'storyboard-submissioncomplete' => 'PresentaciÃ³n completada.',
	'storyreview' => 'RevisiÃ³n de historia.',
	'storyboard-hideimage' => 'Ocultar imagen',
	'storyboard-unhideimage' => 'Mostrar imagen',
	'storyboard-deleteimage' => 'Borrar imagen',
	'storyboard-yourname' => 'Tu nombre',
	'storyboard-location' => 'Tu ubicaciÃ³n',
	'storyboard-occupation' => 'Tu ocupaciÃ³n',
	'storyboard-story' => 'Su historia.',
	'storyboard-photo' => 'Â¿Tiene una foto propia?
Â¿Por quÃ© no compartirla?',
	'storyboard-email' => 'Tu direcciÃ³n de correo electrÃ³nico',
	'storyboard-storytitle' => 'Un tÃ­tulo corto y descriptivo',
	'storyboard-agreement' => 'Estoy de acuerdo con la publicaciÃ³n y el uso de esta historia bajo los tÃ©rminos de [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution/Share-Alike License].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1 | caracter | caracteres}} izquierda)',
	'storyboard-cannotbelonger' => 'Su historia es <b>$1</b> {{PLURAL: $1 | caracter |caracteres}} Â¡demasiado largo!',
	'storyboard-charsneeded' => '($ 1 mÃ¡s {{PLURAL: $1 | caracter | caracteres}} Necesario)',
	'storyboard-needtoagree' => 'Necesita llegar a un acuerdo para la publicaciÃ³n de la presentaciÃ³n de su historia.',
	'storyboard-createdsucessfully' => 'Â¡Gracias por compartir su historia con nosotros!
La revisaremos en breve.
Puede [$1 Leer historias publicadas]',
);

/** Finnish (Suomi)
 * @author Centerlink
 * @author Crt
 */
$messages['fi'] = array(
	'right-storyreview' => 'Tarkistaa, muokata, julkaista ja piilotaa tarinoita',
);

/** French (FranÃ§ais)
 * @author IAlex
 * @author Jean-FrÃ©dÃ©ric
 * @author Peter17
 * @author PieRRoMaN
 */
$messages['fr'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Fournit une [[Special:Story|page cible pour les donateurs]], une page pour [[Special:StorySubmission|proposer une histoire]], et une [[Special:StoryReview|interface de modÃ©ration des histoires]]',
	'right-storyreview' => 'Relire, modifier, publier, et masquer les histoires',
	'storyboard-unpublished' => 'Non publiÃ©',
	'storyboard-published' => 'PubliÃ©',
	'storyboard-hidden' => 'MasquÃ©',
	'storyboard-unpublish' => 'DÃ©publier',
	'storyboard-publish' => 'Publier',
	'storyboard-hide' => 'Masquer',
	'story' => 'Histoire',
	'storyboard-submittedbyon' => 'ProposÃ©e par $1 le $2, $3',
	'storyboard-viewstories' => 'Voir les histoires',
	'storyboard-nosuchstory' => 'Lâ€™histoire que vous avez demandÃ©e nâ€™existe pas. Elle a peut-Ãªtre Ã©tÃ© supprimÃ©e.',
	'storyboard-storyunpublished' => 'Lâ€™histoire que vous avez demandÃ©e nâ€™a pas encore Ã©tÃ© publiÃ©e.',
	'storyboard-nostorytitle' => 'Vous devez indiquer le titre ou lâ€™identifiant de lâ€™histoire que vous voulez afficher.',
	'storyboard-cantedit' => 'Vous nâ€™avez pas les droits pour modifier des histoires.',
	'storyboard-canedit' => 'Vous pouvez [$1 modifier] et publier cette histoire.',
	'storyboard-createdandmodified' => 'CrÃ©Ã©e le $1, $2 et derniÃ¨re modification le $3, $4',
	'storyboard-authorname' => 'Nom de lâ€™auteur',
	'storyboard-authorlocation' => 'Localisation de lâ€™auteur',
	'storyboard-authoroccupation' => 'ActivitÃ© de lâ€™auteur',
	'storyboard-authoremail' => 'Adresse de courriel de lâ€™auteur',
	'storyboard-thestory' => 'Lâ€™histoire',
	'storyboard-storystate' => 'Ã‰tat :',
	'storyboard-submissioncomplete' => 'Proposition achevÃ©e',
	'storyreview' => 'Critique de lâ€™histoire',
	'storyboard-hideimage' => 'Masquer lâ€™image',
	'storyboard-unhideimage' => 'Afficher lâ€™image',
	'storyboard-deleteimage' => 'Supprimer lâ€™image',
	'storyboard-yourname' => 'Votre nom',
	'storyboard-location' => 'Votre localisation',
	'storyboard-occupation' => 'Votre mÃ©tier',
	'storyboard-story' => 'Votre histoire',
	'storyboard-photo' => 'Vous avez une photo de vous-mÃªme ? Pourquoi ne pas la partager ?',
	'storyboard-email' => 'Votre adresse de courriel',
	'storyboard-storytitle' => 'Un titre, court et descriptif',
	'storyboard-agreement' => 'Jâ€™accepte la publication et lâ€™utilisation de cette histoire sous les termes de la [http://creativecommons.org/licenses/by-sa/3.0/ licence Creative Commons PaternitÃ© â€“ Partage des conditions initiales Ã  lâ€™identique].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|signe|signes}} {{PLURAL:$1|restant|restants}})',
	'storyboard-cannotbelonger' => 'Votre histoire est trop longue de <b>$1</b> {{PLURAL:$1|signe|signes}} !',
	'storyboard-charsneeded' => '($1 {{PLURAL:$1|signe supplÃ©mentaire|signes supplÃ©mentaires}} requis)',
	'storyboard-needtoagree' => 'Vous devez approuver la publication de votre histoire pour pouvoir la proposer.',
	'storyboard-createdsucessfully' => 'Merci dâ€™avoir partagÃ© votre histoire avec nous !
Nous allons lâ€™examiner sous peu.
Vous pouvez [$1 lire des histoires publiÃ©es].',
);

/** Galician (Galego)
 * @author Tempestas
 * @author ToliÃ±o
 */
$messages['gl'] = array(
	'storyboard-name' => 'Taboleiro de historias',
	'storyboard-desc' => 'Proporciona unha [[Special:Story|pÃ¡xina de chegada para os doantes]], unha pÃ¡xina desde a que se poden [[Special:StorySubmission|enviar]] historias e unha [[Special:StoryReview|interface para moderar o seu envÃ­o]]',
	'right-storyreview' => 'Revisar, editar, publicar e agochar historias',
	'storyboard-unpublished' => 'Sen publicar',
	'storyboard-published' => 'Publicada',
	'storyboard-hidden' => 'Agochada',
	'storyboard-unpublish' => 'Retirar a publicaciÃ³n',
	'storyboard-publish' => 'Publicar',
	'storyboard-hide' => 'Agochar',
	'storyboard-option-unpublished' => 'non publicada',
	'storyboard-option-published' => 'publicada',
	'storyboard-option-hidden' => 'agochada',
	'story' => 'Historia',
	'storyboard-submittedbyon' => 'Enviado por $1 o $2, $3',
	'storyboard-viewstories' => 'Ver as historias',
	'storyboard-nosuchstory' => 'A historia solicitada non existe.
Pode ter sido eliminada.',
	'storyboard-storyunpublished' => 'A historia que solicitou aÃ­nda non foi publicada.',
	'storyboard-nostorytitle' => 'Ten que especificar o tÃ­tulo ou a ID da historia que desexa ver.',
	'storyboard-cantedit' => 'Non ten os permisos necesarios para editar historias.',
	'storyboard-canedit' => 'Pode [$1 editar] e publicar esta historia.',
	'storyboard-createdandmodified' => 'Creada o $1, $2 e modificada por Ãºltima vez o $3, $4',
	'storyboard-authorname' => 'Nome do autor',
	'storyboard-authorlocation' => 'LocalizaciÃ³n do autor',
	'storyboard-authoroccupation' => 'ProfesiÃ³n do autor',
	'storyboard-authoremail' => 'Enderezo de correo electrÃ³nico do autor',
	'storyboard-thestory' => 'A historia',
	'storyboard-storystate' => 'Estado:',
	'storyboard-submissioncomplete' => 'EnvÃ­o completado',
	'storyreview' => 'RevisiÃ³n da historia',
	'storyboard-hideimage' => 'Agochar a imaxe',
	'storyboard-unhideimage' => 'Amosar a imaxe',
	'storyboard-deleteimage' => 'Borrar a imaxe',
	'storyboard-yourname' => 'O seu nome',
	'storyboard-location' => 'A sÃºa localizaciÃ³n',
	'storyboard-occupation' => 'A sÃºa profesiÃ³n',
	'storyboard-story' => 'A sÃºa historia',
	'storyboard-photo' => 'Ten unha foto de si mesmo?
Por que non compartila?',
	'storyboard-email' => 'O seu enderezo de correo electrÃ³nico',
	'storyboard-storytitle' => 'Un tÃ­tulo corto e descritivo',
	'storyboard-agreement' => 'Acepto a publicaciÃ³n e o uso desta historia baixo os termos da [http://creativecommons.org/licenses/by-sa/3.0/deed.gl licenza Creative Commons recoÃ±ecemento compartir igual].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|carÃ¡cter restante|caracteres restantes}})',
	'storyboard-cannotbelonger' => 'A sÃºa historia ten {{PLURAL:$1|<b>un</b> carÃ¡cter|<b>$1</b> caracteres}} de mÃ¡is!',
	'storyboard-charsneeded' => '({{PLURAL:$1|necesÃ­tase un carÃ¡cter mÃ¡is|necesÃ­tanse $1 caracteres mÃ¡is}})',
	'storyboard-needtoagree' => 'Ten que estar de acordo coa publicaciÃ³n da sÃºa historia para enviala.',
	'storyboard-createdsucessfully' => 'Grazas por compartir a sÃºa historia connosco!
AnalizarÃ©mola en breve.
Entrementres, pode [$1 ler outras historias publicadas].',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Stellt e Ziilsyte z Verfiegig fir SpÃ¤nder, wu Gschichte chenne yygee wÃ¤re un e Benutzeroberflechi fir d Moderation vu Gschichte',
	'right-storyreview' => 'Gschichte priefe, bearbeite un uusblÃ¤nde',
	'storyboard-unpublish' => 'Vereffetlichung zruckneh',
	'storyboard-publish' => 'Vereffetlige',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'SkiÄ‡a [[Special:Story|pÅ™ichadnu stronu za dariÄ‡elow]], strona, hdÅºeÅ¾ so hodÅºa powÄ›danÄ�ka [[Special:StorySubmission|zawostajiÄ‡]] a [[Special:StoryReview|powjerch za moderaciju powÄ›danÄ�kow]]',
	'right-storyreview' => 'PowÄ›danÄ�ka pÅ™ehladaÄ‡, wobdÅºÄ›Å‚aÄ‡, wozjewiÄ‡ a schowaÄ‡',
	'storyboard-unpublished' => 'Njewozjewjena',
	'storyboard-published' => 'Wozjewjeny',
	'storyboard-hidden' => 'Schowany',
	'storyboard-unpublish' => 'Wozjewjenje cofnyÄ‡',
	'storyboard-publish' => 'WozjewiÄ‡',
	'storyboard-hide' => 'SchowaÄ‡',
	'storyboard-option-unpublished' => 'njewozjewjeny',
	'storyboard-option-published' => 'wozjewjeny',
	'storyboard-option-hidden' => 'schowany',
	'story' => 'PowÄ›danÄ�ko',
	'storyboard-submittedbyon' => 'Zawostajene wot $1 dnja $2, $3.',
	'storyboard-viewstories' => 'PowÄ›danÄ�ka pokazaÄ‡',
	'storyboard-nosuchstory' => 'PowÄ›danÄ�ko, kotreÅ¾ sÄ‡e poÅ¾adaÅ‚, njeeksistuje.
MÃ³Å¾no, zo je so wotstroniÅ‚o.',
	'storyboard-storyunpublished' => 'PowÄ›danÄ�ko, kotreÅ¾ sÄ‡e poÅ¾adaÅ‚, hiÅ¡Ä‡e njeje wozjewjene.',
	'storyboard-nostorytitle' => 'DyrbiÄ‡e titul abo ID powÄ›danÄ�ka podaÄ‡, kotreÅ¾ chceÄ‡e sej wobhladaÄ‡.',
	'storyboard-cantedit' => 'NjesmÄ›Ä‡e powÄ›danÄ�ka wobdÅºÄ›Å‚aÄ‡.',
	'storyboard-canedit' => 'MÃ³Å¾eÄ‡e tute pwÄ›danÄ�ko [$1 wobdÅºÄ›Å‚aÄ‡] a wozjewiÄ‡.',
	'storyboard-createdandmodified' => 'Dnja $1, $2 wutworjene a dnja $3, $4 posledni raz zmÄ›njene',
	'storyboard-authorname' => 'Mjeno awtora',
	'storyboard-authorlocation' => 'MÄ›stno awtora',
	'storyboard-authoroccupation' => 'PowoÅ‚anje awtora',
	'storyboard-authoremail' => 'E-mejlowa adresa awtora',
	'storyboard-thestory' => 'PowÄ›danÄ�ko',
	'storyboard-storystate' => 'Stat:',
	'storyboard-submissioncomplete' => 'ZapodaÄ‡e dospoÅ‚ne',
	'storyreview' => 'PÅ™epruwowanje powÄ›danÄ�ka',
	'storyboard-hideimage' => 'Wobraz schowaÄ‡',
	'storyboard-unhideimage' => 'Wobraz pokazaÄ‡',
	'storyboard-deleteimage' => 'Wobraz zhaÅ¡eÄ‡',
	'storyboard-yourname' => 'WaÅ¡e mjeno',
	'storyboard-location' => 'WaÅ¡e mÄ›stno',
	'storyboard-occupation' => 'WaÅ¡e powoÅ‚anje',
	'storyboard-story' => 'WaÅ¡e powÄ›danÄ�ko',
	'storyboard-photo' => 'MaÄ‡e foto wot sebje?
ÄŒehodla njedaÄ‡e druhich na njo dÅºÄ›l mÄ›Ä‡?',
	'storyboard-email' => 'Twoja e-mejlowa adresa',
	'storyboard-storytitle' => 'KrÃ³tki, wuprajiwy titul',
	'storyboard-agreement' => 'Zwolim do wozjewjenja a wuÅ¾iwanja tutoho powÄ›danÄ�ka pod wumÄ›njenjemi licency [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution/Share-Alike License].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|znamjeÅ¡ko|znamjeÅ¡ce|znamjeÅ¡ka|znamjeÅ¡kow}} wyÅ¡e)',
	'storyboard-cannotbelonger' => 'WaÅ¡e powÄ›danÄ�ko je <b>$1</b> {{PLURAL:$1|znamjeÅ¡ko|znamjeÅ¡ce|znamjeÅ¡ka|znamjeÅ¡kow}} pÅ™edoÅ‚ho!',
	'storyboard-charsneeded' => '($1 {{PLURAL:$1|dalÅ¡e znamjeÅ¡ko trÄ›bne|dalÅ¡ej znamjeÅ¡ce trÄ›bnej|dalÅ¡e znamjeÅ¡ka trÄ›bne|dalÅ¡ich znamjeÅ¡kow trÄ›bnych}})',
	'storyboard-needtoagree' => 'DyrbiÄ‡e do wozjewjenja waÅ¡eho powÄ›danÄ�ka zwoliÄ‡, zo byÅ¡Ä‡e jo zapodaÅ‚.',
	'storyboard-createdsucessfully' => 'DÅºakujemy so wam, zo sÄ‡e swoje powÄ›danÄ�ko nam k dispoziciji stajiÅ‚!
BudÅºemy jo bÃ³rze pÅ™epruwowaÄ‡.
MÃ³Å¾eÄ‡e [$1 wozjewjene powÄ›danÄ�ka Ä�itaÄ‡].',
);

/** Hungarian (Magyar)
 * @author Glanthor Reviol
 */
$messages['hu'] = array(
	'storyboard-unpublish' => 'KÃ¶zzÃ©tÃ©tel visszavonÃ¡sa',
	'storyboard-publish' => 'KÃ¶zzÃ©tÃ©tel',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Provide un [[Special:Story|pagina de arrivata pro donatores]], un pagina ubi historias pote esser [[Special:StorySubmission|submittite]] e un [[Special:StoryReview|interfacie pro moderation de historias]].',
	'right-storyreview' => 'Revider, modificar, publicar e celar historias',
	'storyboard-unpublished' => 'Non publicate',
	'storyboard-published' => 'Publicate',
	'storyboard-hidden' => 'Celate',
	'storyboard-unpublish' => 'Dispublicar',
	'storyboard-publish' => 'Publicar',
	'storyboard-hide' => 'Celar',
	'storyboard-option-unpublished' => 'non publicate',
	'storyboard-option-published' => 'publicate',
	'storyboard-option-hidden' => 'celate',
	'story' => 'Historia',
	'storyboard-submittedbyon' => 'Submittite per $1 le $2 a $3.',
	'storyboard-viewstories' => 'Vider historias',
	'storyboard-nosuchstory' => 'Le historia que tu ha demandate non existe.
Illo pote haber essite removite.',
	'storyboard-storyunpublished' => 'Le historia que tu ha demandate non ha ancora essite publicate.',
	'storyboard-nostorytitle' => 'Tu debe specificar le titulo o ID del historia que tu vole vider.',
	'storyboard-cantedit' => 'Tu non ha le permission de modificar historias.',
	'storyboard-canedit' => 'Tu pote [$1 modificar] e publicar iste historia.',
	'storyboard-createdandmodified' => 'Creation: le $1 a $2; ultime modification: le $3 a $4',
	'storyboard-authorname' => 'Nomine del autor',
	'storyboard-authorlocation' => 'Loco del autor',
	'storyboard-authoroccupation' => 'Occupation del autor',
	'storyboard-authoremail' => 'Adresse de e-mail del autor',
	'storyboard-thestory' => 'Le historia',
	'storyboard-storystate' => 'Stato:',
	'storyboard-submissioncomplete' => 'Submission complete',
	'storyreview' => 'Revision del historia',
	'storyboard-hideimage' => 'Celar imagine',
	'storyboard-unhideimage' => 'Monstrar imagine',
	'storyboard-deleteimage' => 'Deler imagine',
	'storyboard-yourname' => 'Tu nomine',
	'storyboard-location' => 'Tu loco',
	'storyboard-occupation' => 'Tu occupation',
	'storyboard-story' => 'Tu historia',
	'storyboard-photo' => 'Ha tu un photo de te?
Proque non facer vider lo?',
	'storyboard-email' => 'Tu adresse de e-mail',
	'storyboard-storytitle' => 'Un titulo curte e descriptive',
	'storyboard-agreement' => 'Io accepta le publication e le uso de iste historia sub le conditiones del [http://creativecommons.org/licenses/by-sa/3.0/ licentia Creative Commons Attribution/Share-Alike].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|character|characteres}} restante)',
	'storyboard-cannotbelonger' => 'Tu historia es troppo longe de <b>$1</b> {{PLURAL:$1|character|characteres}}!',
	'storyboard-charsneeded' => '($1 plus {{PLURAL:$1|character|characteres}} necessari)',
	'storyboard-needtoagree' => 'Tu debe approbar le publication de tu historia pro submitter lo.',
	'storyboard-createdsucessfully' => 'Gratias pro partir tu historia con nos!
Nos lo revidera tosto.
Tu pote [$1 leger le historias ja publicate].',
);

/** Indonesian (Bahasa Indonesia)
 * @author Kenrick95
 */
$messages['id'] = array(
	'storyboard-hide' => 'Sembunyikan',
);

/** Luxembourgish (LÃ«tzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Stellt eng [[Special:Story|EntrÃ©essÃ¤it fir Donateuren]] zur VerfÃ¼gung, eng SÃ¤it wou Anekdoten [[Special:StorySubmission|presentÃ©iert]] kÃ«nne ginn, an e [[Special:StoryReview|Moderatiouns-Interface fir Anekdoten]]',
	'right-storyreview' => 'Anekdoten nokucken, Ã¤nneren, publizÃ©ieren a verstoppen',
	'storyboard-unpublished' => 'Net verÃ«ffentlecht',
	'storyboard-published' => 'VerÃ«ffentlecht',
	'storyboard-hidden' => 'Verstoppt',
	'storyboard-unpublish' => 'VerÃ«ffentlecung zrÃ©ckzÃ©ien',
	'storyboard-publish' => 'VerÃ«ffentlechen',
	'storyboard-hide' => 'Verstoppen',
	'story' => 'Anekdot',
	'storyboard-submittedbyon' => 'Vum $1 den $2, $3 geschÃ©ckt',
	'storyboard-viewstories' => 'Anekdote weisen',
	'storyboard-nosuchstory' => "D'Anekdot dÃ©i Dir ugefrot hutt gÃ«tt et net.
Et ka sinn datt se ewechgeholl gouf.",
	'storyboard-storyunpublished' => "D'Anekdot dÃ©i Dir ugefrot hutt gouf nach net verÃ«ffentlecht.",
	'storyboard-nostorytitle' => "Dir musst den Titel oder d'ID vun der Anekdot uginn dÃ©i gewise soll ginn.",
	'storyboard-cantedit' => 'Dir dÃ¤erft Anekdote net Ã¤nneren.',
	'storyboard-canedit' => 'Dir kÃ«nnt dÃ«s Anekdot [$1 Ã¤nneren] a verÃ«ffentlechen.',
	'storyboard-authorname' => 'Numm vum Auteur',
	'storyboard-authoremail' => 'E-Mailadress vum Auteur',
	'storyboard-thestory' => "D'Anekdot",
	'storyboard-storystate' => 'Staat',
	'storyreview' => 'Anekdot nokucken',
	'storyboard-hideimage' => 'Bild verstoppen',
	'storyboard-unhideimage' => 'Bild weisen',
	'storyboard-deleteimage' => 'Bild lÃ¤schen',
	'storyboard-yourname' => 'Ã„ren Numm',
	'storyboard-location' => 'Plaz wou Dir sidd',
	'storyboard-occupation' => 'Ã„re Beruff',
	'storyboard-story' => 'Ã„r Anekdot',
	'storyboard-photo' => 'Hutt dir eng Photo vun Iech?
Firwat se net hei weisen?',
	'storyboard-email' => 'Ã„r E-Mailadress',
	'storyboard-storytitle' => 'E kuerzen Titel de beschreift wourÃ«ms et geet',
	'storyboard-needtoagree' => "Dir musst d'accord sinn datt Ã„r Anekdot verÃ«ffentlecht gÃ«tt fir se ze schÃ©cken.",
	'storyboard-createdsucessfully' => 'Merci datt dir Ã„r Anekdot mat eis deelt!
Mir kucken se demnÃ¤chst no.
Dir kÃ«nnt [$1 publizÃ©iert Anekdote liesen].',
);

/** Macedonian (ÐœÐ°ÐºÐµÐ´Ð¾Ð½Ñ�ÐºÐ¸)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'storyboard-name' => 'Ð Ð°Ñ�ÐºÐ°Ð¶ÑƒÐ²Ð°Ñ‡Ð½Ð¸Ñ†Ð°',
	'storyboard-desc' => 'Ð”Ð°Ð²Ð° [[Special:Story|Ñ�Ñ‚Ñ€Ð°Ð½Ð¸Ñ†Ð° Ð·Ð° Ð´Ð°Ñ€Ð¸Ñ‚ÐµÐ»Ð¸Ñ‚Ðµ]], ÐºÐ°Ð´ÐµÑˆÑ‚Ð¾ Ñ‚Ð¸Ðµ Ð¼Ð¾Ð¶Ð°Ñ‚ [[Special:StorySubmission|Ð´Ð° Ð¾Ð±Ñ˜Ð°Ð²Ð°Ñ‚]] Ñ�Ð²Ð¾Ñ˜Ð° Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð°, ÐºÐ°ÐºÐ¾ Ð¸ [[Special:StoryReview|Ð¸Ð½Ñ‚ÐµÑ€Ñ„ÐµÑ˜Ñ� Ð·Ð° Ð¼Ð¾Ð´ÐµÑ€Ð¸Ñ€Ð°ÑšÐµ Ð½Ð° Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð¸Ñ‚Ðµ]]',
	'right-storyreview' => 'ÐŸÑ€ÐµÐ³Ð»ÐµÐ´ÑƒÐ²Ð°ÑšÐµ, ÑƒÑ€ÐµÐ´ÑƒÐ²Ð°ÑšÐµ, Ð¾Ð±Ñ˜Ð°Ð²ÑƒÐ²Ð°ÑšÐµ Ð¸ Ñ�Ð¾ÐºÑ€Ð¸Ð²Ð°ÑšÐµ Ð½Ð° Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð¸',
	'storyboard-unpublished' => 'Ð�ÐµÐ¾Ð±Ñ˜Ð°Ð²ÐµÐ½Ð¾',
	'storyboard-published' => 'ÐžÐ±Ñ˜Ð°Ð²ÐµÐ½Ð¾',
	'storyboard-hidden' => 'Ð¡Ð¾ÐºÑ€Ð¸ÐµÐ½Ð¾',
	'storyboard-unpublish' => 'Ð¢Ñ€Ð³Ð½Ð¸',
	'storyboard-publish' => 'ÐžÐ±Ñ˜Ð°Ð²Ð¸',
	'storyboard-hide' => 'Ð¡Ð¾ÐºÑ€Ð¸Ñ˜',
	'storyboard-option-unpublished' => 'Ð½ÐµÐ¾Ð±Ñ˜Ð°Ð²ÐµÐ½Ð¸',
	'storyboard-option-published' => 'Ð¾Ð±Ñ˜Ð°Ð²ÐµÐ½Ð¸',
	'storyboard-option-hidden' => 'Ñ�ÐºÑ€Ð¸ÐµÐ½Ð¸',
	'story' => 'ÐŸÑ€Ð¸ÐºÐ°Ð·Ð½Ð°',
	'storyboard-submittedbyon' => 'ÐŸÐ¾Ð´Ð½ÐµÑ�ÐµÐ½Ð¾ Ð¾Ð´ $1 Ð½Ð° $2, $3.',
	'storyboard-viewstories' => 'ÐŸÑ€ÐµÐ³Ð»ÐµÐ´ Ð½Ð° Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð¸',
	'storyboard-nosuchstory' => 'ÐŸÑ€Ð¸ÐºÐ°Ð·Ð½Ð°Ñ‚Ð° ÑˆÑ‚Ð¾ Ñ˜Ð° Ð¿Ð¾Ð±Ð°Ñ€Ð°Ð²Ñ‚Ðµ Ð½Ðµ Ð¿Ð¾Ñ�Ñ‚Ð¾Ð¸.
ÐœÐ¾Ð¶Ðµ Ð´Ð° Ð±Ð¸Ð»Ð° Ð¾Ñ‚Ñ�Ñ‚Ñ€Ð°Ð½ÐµÑ‚Ð°.',
	'storyboard-storyunpublished' => 'ÐŸÑ€Ð¸ÐºÐ°Ð·Ð½Ð°Ñ‚Ð° ÑˆÑ‚Ð¾ Ñ˜Ð° Ð¿Ð¾Ð±Ð°Ñ€Ð°Ð²Ñ‚Ðµ Ñ�Ã¨ ÑƒÑˆÑ‚Ðµ Ð½Ðµ Ðµ Ð¾Ð±Ñ˜Ð°Ð²ÐµÐ½Ð°.',
	'storyboard-nostorytitle' => 'Ð¢Ñ€ÐµÐ±Ð° Ð´Ð° Ð½Ð°Ð·Ð½Ð°Ñ‡Ð¸Ñ‚Ðµ Ð½Ð°Ñ�Ð»Ð¾Ð² Ð¸Ð»Ð¸ ID Ð½Ð° Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð°Ñ‚Ð° ÑˆÑ‚Ð¾ Ñ�Ð°ÐºÐ°Ñ‚Ðµ Ð´Ð° Ñ˜Ð° Ð²Ð¸Ð´Ð¸Ñ‚Ðµ.',
	'storyboard-cantedit' => 'Ð�Ðµ Ð²Ð¸ Ðµ Ð´Ð¾Ð·Ð²Ð¾Ð»ÐµÐ½Ð¾ Ð´Ð° Ð³Ð¸ Ð¼ÐµÐ½ÑƒÐ²Ð°Ñ‚Ðµ Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð¸Ñ‚Ðµ.',
	'storyboard-canedit' => 'ÐœÐ¾Ð¶ÐµÑ‚Ðµ Ð´Ð° Ñ˜Ð° [$1 ÑƒÑ€ÐµÐ´Ð¸Ñ‚Ðµ] Ð¸ Ð¾Ð±Ñ˜Ð°Ð²Ð¸Ñ‚Ðµ Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð°Ð²Ð°.',
	'storyboard-createdandmodified' => 'Ð¡Ð¾Ð·Ð´Ð°Ð´ÐµÐ½Ð¾ Ð½Ð° $1, $2, Ð° Ð¿Ð¾Ñ�Ð»ÐµÐ´Ð½Ð¾ Ð¸Ð·Ð¼ÐµÐ½ÐµÑ‚Ð¾ Ð½Ð° $3, $4',
	'storyboard-authorname' => 'Ð˜Ð¼Ðµ Ð½Ð° Ð°Ð²Ñ‚Ð¾Ñ€Ð¾Ñ‚',
	'storyboard-authorlocation' => 'ÐœÐµÑ�Ñ‚Ð¾ Ð½Ð° Ð¶Ð¸Ð²ÐµÐµÑšÐµ Ð½Ð° Ð°Ð²Ñ‚Ð¾Ñ€Ð¾Ñ‚',
	'storyboard-authoroccupation' => 'Ð—Ð°Ð½Ð¸Ð¼Ð°ÑšÐµ Ð½Ð° Ð°Ð²Ñ‚Ð¾Ñ€Ð¾Ñ‚',
	'storyboard-authoremail' => 'Ð•-Ð¿Ð¾ÑˆÑ‚Ð° Ð½Ð° Ð°Ð²Ñ‚Ð¾Ñ€Ð¾Ñ‚',
	'storyboard-thestory' => 'ÐŸÑ€Ð¸ÐºÐ°Ð·Ð½Ð°Ñ‚Ð°',
	'storyboard-storystate' => 'Ð¡Ð¾Ñ˜ÑƒÐ·. Ð´Ñ€Ð¶Ð°Ð²Ð°:',
	'storyboard-submissioncomplete' => 'ÐŸÐ¾Ð´Ð½ÐµÑ�ÑƒÐ²Ð°ÑšÐµÑ‚Ð¾ Ðµ Ð·Ð°Ð²Ñ€ÑˆÐµÐ½Ð¾',
	'storyreview' => 'ÐŸÑ€ÐµÐ³Ð»ÐµÐ´ Ð½Ð° Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð°',
	'storyboard-hideimage' => 'Ð¡Ð¾ÐºÑ€Ð¸Ñ˜ Ñ�Ð»Ð¸ÐºÐ°',
	'storyboard-unhideimage' => 'ÐŸÑ€Ð¸ÐºÐ°Ð¶Ð¸ Ñ�Ð»Ð¸ÐºÐ°',
	'storyboard-deleteimage' => 'Ð˜Ð·Ð±Ñ€Ð¸ÑˆÐ¸ Ñ�Ð»Ð¸ÐºÐ°',
	'storyboard-yourname' => 'Ð’Ð°ÑˆÐµÑ‚Ð¾ Ð¸Ð¼Ðµ Ð¸ Ð¿Ñ€ÐµÐ·Ð¼Ðµ',
	'storyboard-location' => 'ÐœÐµÑ�Ñ‚Ð¾ Ð½Ð° Ð¶Ð¸Ð²ÐµÐµÑšÐµ',
	'storyboard-occupation' => 'Ð’Ð°ÑˆÐµÑ‚Ð¾ Ð·Ð°Ð½Ð¸Ð¼Ð°ÑšÐµ',
	'storyboard-story' => 'Ð’Ð°ÑˆÐ°Ñ‚Ð° Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð°',
	'storyboard-photo' => 'Ð˜Ð¼Ð°Ñ‚Ðµ Ð²Ð°ÑˆÐ° Ñ„Ð¾Ñ‚Ð¾Ð³Ñ€Ð°Ñ„Ð¸Ñ˜Ð°?
Ð—Ð¾ÑˆÑ‚Ð¾ Ð½Ðµ Ñ˜Ð° Ñ�Ð¿Ð¾Ð´ÐµÐ»Ð¸Ñ‚Ðµ?',
	'storyboard-email' => 'Ð’Ð°ÑˆÐ° Ðµ-Ð¿Ð¾ÑˆÑ‚Ð°',
	'storyboard-storytitle' => 'ÐšÑ€Ð°Ñ‚Ð¾Ðº Ð¸ Ð¾Ð¿Ð¸Ñ�ÐµÐ½ Ð½Ð°Ñ�Ð»Ð¾Ð²',
	'storyboard-agreement' => 'Ð¡Ðµ Ñ�Ð¾Ð³Ð»Ð°Ñ�ÑƒÐ²Ð°Ð¼ Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð°Ð²Ð° Ð´Ð° Ñ�Ðµ Ð¾Ð±Ñ˜Ð°Ð²Ð¸ Ð¸ ÐºÐ¾Ñ€Ð¸Ñ�Ñ‚Ð¸ Ñ�Ð¾Ð³Ð»Ð°Ñ�Ð½Ð¾ ÑƒÑ�Ð»Ð¾Ð²Ð¸Ñ‚Ðµ Ð½Ð° Ð»Ð¸Ñ†ÐµÐ½Ñ†Ð°Ñ‚Ð° [http://creativecommons.org/licenses/by-sa/3.0/deed.mk Creative Commons Ð�Ð°Ð²ÐµÐ´Ð¸ Ð¸Ð·Ð²Ð¾Ñ€/Ð¡Ð¿Ð¾Ð´ÐµÐ»Ð¸ Ð¿Ð¾Ð´ Ð¸Ñ�Ñ‚Ð¸ ÑƒÑ�Ð»Ð¾Ð²Ð¸].',
	'storyboard-charsleft' => '({{PLURAL:$1|Ð’Ð¸ Ð¿Ñ€ÐµÐ¾Ñ�Ñ‚Ð°Ð½ÑƒÐ²Ð° ÑƒÑˆÑ‚Ðµ|Ð’Ð¸ Ð¿Ñ€ÐµÐ¾Ñ�Ñ‚Ð°Ð½ÑƒÐ²Ð°Ð°Ñ‚ ÑƒÑˆÑ‚Ðµ}} $1 {{PLURAL:$1|Ð·Ð½Ð°Ðº|Ð·Ð½Ð°Ñ†Ð¸}})',
	'storyboard-cannotbelonger' => 'Ð’Ð°ÑˆÐ°Ñ‚Ð° Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð° Ðµ Ð·Ð° <b>$1</b> {{PLURAL:$1|Ð·Ð½Ð°Ðº|Ð·Ð½Ð°Ñ†Ð¸}} Ð¿Ð¾Ð´Ð¾Ð»Ð³Ð° Ð¾Ð´ Ð´Ð¾Ð·Ð²Ð¾Ð»ÐµÐ½Ð¾Ñ‚Ð¾!',
	'storyboard-charsneeded' => '({{PLURAL:$1|Ð¿Ð¾Ñ‚Ñ€ÐµÐ±ÐµÐ½ Ðµ|Ð¿Ð¾Ñ‚Ñ€ÐµÐ±Ð½Ð¸ Ñ�Ðµ}} ÑƒÑˆÑ‚Ðµ $1 {{PLURAL:$1|Ð·Ð½Ð°Ðº|Ð·Ð½Ð°Ñ†Ð¸}})',
	'storyboard-needtoagree' => 'ÐœÐ¾Ñ€Ð° Ð´Ð° Ñ�Ðµ Ñ�Ð¾Ð³Ð»Ð°Ñ�Ð¸Ñ‚Ðµ Ð´Ð° Ñ˜Ð° Ð¾Ð±Ñ˜Ð°Ð²Ð¸Ñ‚Ðµ Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð°Ñ‚Ð° Ð·Ð° Ð´Ð° Ñ˜Ð° Ð¿Ð¾Ð´Ð½ÐµÑ�ÐµÑ‚Ðµ.',
	'storyboard-createdsucessfully' => 'Ð’Ð¸ Ð±Ð»Ð°Ð³Ð¾Ð´Ð°Ñ€Ð¸Ð¼Ð½Ðµ ÑˆÑ‚Ð¾ Ñ˜Ð° Ñ�Ð¿Ð¾Ð´ÐµÐ»Ð¸Ð²Ñ‚Ðµ Ð’Ð°ÑˆÐ°Ñ‚Ð° Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð° Ñ�Ð¾ Ð½Ð°Ñ�!
Ð�Ð°Ð±Ñ€Ð³Ñƒ ÑœÐµ Ñ˜Ð° Ð¿Ñ€ÐµÐ³Ð»ÐµÐ´Ð°Ð¼Ðµ.
ÐœÐ¾Ð¶ÐµÑ‚Ðµ Ð´Ð° Ð³Ð¸ [$1 Ð¿Ñ€Ð¾Ñ‡Ð¸Ñ‚Ð°Ñ‚Ðµ Ð¾Ð±Ñ˜Ð°Ð²ÐµÐ½Ð¸Ñ‚Ðµ Ð¿Ñ€Ð¸ÐºÐ°Ð·Ð½Ð¸].',
);

/** Dutch (Nederlands)
 * @author McDutchie
 * @author Siebrand
 */
$messages['nl'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Biedt een [[Special:Story|aankomstpagina voor donateurs]], een [[Special:StorySubmission|pagina waar verhalen ingezonden kunnen worden]] en een [[Special:StoryReview|interface voor beoordeling van verhalen]]',
	'right-storyreview' => 'Verhalen beoordelen, bewerken, publiceren en verbergen',
	'storyboard-unpublished' => 'Ongepubliceerd',
	'storyboard-published' => 'Gepubliceerd',
	'storyboard-hidden' => 'Verborgen',
	'storyboard-unpublish' => 'Publicatie terugtrekken',
	'storyboard-publish' => 'Publiceren',
	'storyboard-hide' => 'Verbergen',
	'storyboard-option-unpublished' => 'ongepubliceerd',
	'storyboard-option-published' => 'gepubliceerd',
	'storyboard-option-hidden' => 'verborgen',
	'story' => 'Verhaal',
	'storyboard-submittedbyon' => 'Ingezonden door $1 op $2 om $3.',
	'storyboard-viewstories' => 'Verhalen bekijken',
	'storyboard-nosuchstory' => 'Het door u opgevraagde verhaal bestaat niet.
Mogelijk is het verwijderd.',
	'storyboard-storyunpublished' => 'Het verhaal dat u heeft opgevraagd is nog niet gepubliceerd.',
	'storyboard-nostorytitle' => 'U moet de naam of het ID van het verhaal dat u wilt bekijken opgeven.',
	'storyboard-cantedit' => 'U mag verhalen niet bewerken.',
	'storyboard-canedit' => 'U kunt dit verhaal [$1 bewerken] en publiceren.',
	'storyboard-createdandmodified' => 'Aangemaakt op $1 om $2 en voor het laatst bewerkt op $3 om $4',
	'storyboard-authorname' => 'Naam auteur',
	'storyboard-authorlocation' => 'Locatie auteur',
	'storyboard-authoroccupation' => 'Beroep auteur',
	'storyboard-authoremail' => 'E-mailadres auteur',
	'storyboard-thestory' => 'Het verhaal',
	'storyboard-storystate' => 'Status:',
	'storyboard-submissioncomplete' => 'Verzenden is voltooid',
	'storyreview' => 'Verhalen beoordelen',
	'storyboard-hideimage' => 'Afbeelding verbergen',
	'storyboard-unhideimage' => 'Afbeelding weergeven',
	'storyboard-deleteimage' => 'Afbeelding verwijderen',
	'storyboard-yourname' => 'Uw naam',
	'storyboard-location' => 'Uw locatie',
	'storyboard-occupation' => 'Uw beroep',
	'storyboard-story' => 'Uw verhaal',
	'storyboard-photo' => 'Wilt u een foto van uzelf toevoegen?',
	'storyboard-email' => 'Uw e-mailadres',
	'storyboard-storytitle' => 'Een korte, beschrijvende titel',
	'storyboard-agreement' => 'Ik ga akkoord met de publicatie van dit verhaal onder de voorwaarden van de licentie [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Naamsvermelding-Gelijk delen].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|teken|tekens}} over)',
	'storyboard-cannotbelonger' => 'Uw verhaal is <b>$1</b> {{PLURAL:$1|teken|tekens}} te lang!',
	'storyboard-charsneeded' => '(er {{PLURAL:$1|is nog 1 teken|zijn nog $1 tekens}} meer nodig)',
	'storyboard-needtoagree' => 'U moet akkoord gaan met het publiceren van uw verhaal voordat u het kunt inzenden.',
	'storyboard-createdsucessfully' => 'Bedankt voor het delen van uw verhaal!
We gaan het snel beoordelen.
U kunt [$1 gepubliceerde verhalen lezen].',
);

/** Norwegian (bokmÃ¥l)â€¬ (â€ªNorsk (bokmÃ¥l)â€¬)
 * @author Nghtwlkr
 */
$messages['no'] = array(
	'storyboard-publish' => 'Publiser',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => "OfrÃ­s una pagina d'aterrissatge pels donators ont las istÃ²rias pÃ²don Ã¨sser somesas e una interfÃ cia de moderacion de las istÃ²rias",
	'right-storyreview' => 'Relegir, modificar, publicar, e amagar las istÃ²rias',
	'storyboard-unpublish' => 'Despublicar',
	'storyboard-publish' => 'Publicar',
);

/** Piedmontese (PiemontÃ¨is)
 * @author BorichÃ¨t
 * @author DragonÃ²t
 */
$messages['pms'] = array(
	'storyboard-name' => 'Disegn Ã«d la senegiadura',
	'storyboard-desc' => "A dÃ  na pÃ gina d'ariv pÃ«r ij donator, na pÃ gina andoa le stÃ²rie a peulo esse butÃ , e n'antÃ«rfacia Ã«d moderassion dle stÃ²rie",
	'right-storyreview' => 'Lese torna, modifichÃ©, publichÃ© e stÃ«rmÃ© le stÃ²rie',
	'storyboard-unpublish' => 'PÃ¹blica pa',
	'storyboard-publish' => 'PublichÃ©',
);

/** Portuguese (PortuguÃªs)
 * @author Hamilton Abreu
 */
$messages['pt'] = array(
	'storyboard-name' => 'HistÃ³rias',
	'storyboard-desc' => 'Fornece uma [[Special:Story|pÃ¡gina de destino para benemÃ©ritos]], uma pÃ¡gina onde se podem [[Special:StorySubmission|submeter]] histÃ³rias e uma [[Special:StoryReview|interface de moderaÃ§Ã£o das histÃ³rias]]',
	'right-storyreview' => 'Rever, editar, publicar e ocultar histÃ³rias',
	'storyboard-unpublished' => 'NÃ£o publicada',
	'storyboard-published' => 'Publicada',
	'storyboard-hidden' => 'Ocultada',
	'storyboard-unpublish' => 'Retirar de publicaÃ§Ã£o',
	'storyboard-publish' => 'Publicar',
	'storyboard-hide' => 'Ocultar',
	'storyboard-option-unpublished' => 'nÃ£o publicada',
	'storyboard-option-published' => 'publicada',
	'storyboard-option-hidden' => 'oculta',
	'story' => 'HistÃ³ria',
	'storyboard-submittedbyon' => 'Enviada por $1 em $2, Ã s $3.',
	'storyboard-viewstories' => 'Ver histÃ³rias',
	'storyboard-nosuchstory' => 'A histÃ³ria que solicitou nÃ£o existe.
Pode ter sido ocultada.',
	'storyboard-storyunpublished' => 'A histÃ³ria que solicitou ainda nÃ£o foi publicada.',
	'storyboard-nostorytitle' => 'Tem de especificar o tÃ­tulo ou a identificaÃ§Ã£o da histÃ³ria que pretende ver.',
	'storyboard-cantedit' => 'NÃ£o lhe Ã© permitido editar histÃ³rias.',
	'storyboard-canedit' => 'Pode [$1 editar] e publicar esta histÃ³ria.',
	'storyboard-createdandmodified' => 'Criada em $1, Ã s $2 e modificada pela Ãºltima vez em $3, Ã s $4',
	'storyboard-authorname' => 'Nome do autor',
	'storyboard-authorlocation' => 'LocalizaÃ§Ã£o do autor',
	'storyboard-authoroccupation' => 'ProfissÃ£o do autor',
	'storyboard-authoremail' => 'EndereÃ§o de correio electrÃ³nico do autor',
	'storyboard-thestory' => 'A histÃ³ria',
	'storyboard-storystate' => 'Estado:',
	'storyboard-submissioncomplete' => 'Envio finalizado',
	'storyreview' => 'RevisÃ£o da histÃ³ria',
	'storyboard-hideimage' => 'Ocultar imagem',
	'storyboard-unhideimage' => 'Mostrar imagem',
	'storyboard-deleteimage' => 'Apagar imagem',
	'storyboard-yourname' => 'O seu nome',
	'storyboard-location' => 'A sua localizaÃ§Ã£o',
	'storyboard-occupation' => 'A sua profissÃ£o',
	'storyboard-story' => 'A sua histÃ³ria',
	'storyboard-photo' => 'Tem uma fotografia sua?
Que tal partilhÃ¡-la?',
	'storyboard-email' => 'O seu endereÃ§o de correio electrÃ³nico',
	'storyboard-storytitle' => 'Um tÃ­tulo curto e descritivo',
	'storyboard-agreement' => 'Concordo com a publicaÃ§Ã£o e uso desta histÃ³ria nos termos da licenÃ§a [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons AtribuiÃ§Ã£o-Partilha nos Termos da Mesma LicenÃ§a]',
	'storyboard-charsleft' => '(restam $1 {{PLURAL:$1|carÃ¡cter|caracteres}})',
	'storyboard-cannotbelonger' => 'A sua histÃ³ria tem <b>$1</b> {{PLURAL:$1|carÃ¡cter|caracteres}} a mais!',
	'storyboard-charsneeded' => '(Ã© preciso mais $1 {{PLURAL:$1|carÃ¡cter|caracteres}})',
	'storyboard-needtoagree' => 'Para enviar a sua histÃ³ria tem de concordar com a sua publicaÃ§Ã£o.',
	'storyboard-createdsucessfully' => 'Obrigado por partilhar connosco a sua histÃ³ria.
Iremos revÃª-la em breve.
Pode [$1 ler histÃ³rias publicadas].',
);

/** Russian (Ð ÑƒÑ�Ñ�ÐºÐ¸Ð¹)
 * @author Ð�Ð»ÐµÐºÑ�Ð°Ð½Ð´Ñ€ Ð¡Ð¸Ð³Ð°Ñ‡Ñ‘Ð²
 */
$messages['ru'] = array(
	'storyboard-name' => 'Ð”Ð¾Ñ�ÐºÐ° Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ð¹',
	'storyboard-desc' => 'ÐŸÑ€ÐµÐ´Ð¾Ñ�Ñ‚Ð°Ð²Ð»Ñ�ÐµÑ‚ [[Special:Story|Ñ�Ñ‚Ñ€Ð°Ð½Ð¸Ñ†Ñƒ]] Ð´Ð»Ñ� [[Special:StorySubmission|Ñ€Ð°Ð·Ð¼ÐµÑ‰ÐµÐ½Ð¸Ñ� Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ð¹]] Ð¶ÐµÑ€Ñ‚Ð²Ð¾Ð²Ð°Ñ‚ÐµÐ»ÐµÐ¹, Ð° Ñ‚Ð°ÐºÐ¶Ðµ [[Special:StoryReview|Ð¸Ð½Ñ‚ÐµÑ€Ñ„ÐµÐ¹Ñ� Ð¼Ð¾Ð´ÐµÑ€Ð°Ñ†Ð¸Ð¸]] Ñ�Ñ‚Ð¸Ñ… Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ð¹',
	'right-storyreview' => 'Ð¿Ñ€Ð¾Ð²ÐµÑ€ÐºÐ°, Ð¿Ñ€Ð°Ð²ÐºÐ°, Ð¿ÑƒÐ±Ð»Ð¸ÐºÐ°Ñ†Ð¸Ñ� Ð¸ Ñ�Ð¾ÐºÑ€Ñ‹Ñ‚Ð¸Ðµ Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ð¹',
	'storyboard-unpublished' => 'Ð�ÐµÐ¾Ð¿ÑƒÐ±Ð»Ð¸ÐºÐ¾Ð²Ð°Ð½Ð°',
	'storyboard-published' => 'ÐžÐ¿ÑƒÐ±Ð»Ð¸ÐºÐ¾Ð²Ð°Ð½Ð°',
	'storyboard-hidden' => 'Ð¡ÐºÑ€Ñ‹Ñ‚Ð°',
	'storyboard-unpublish' => 'Ð£Ð±Ñ€Ð°Ñ‚ÑŒ',
	'storyboard-publish' => 'ÐžÐ¿ÑƒÐ±Ð»Ð¸ÐºÐ¾Ð²Ð°Ñ‚ÑŒ',
	'storyboard-hide' => 'Ð¡ÐºÑ€Ñ‹Ñ‚ÑŒ',
	'story' => 'Ð˜Ñ�Ñ‚Ð¾Ñ€Ð¸Ñ�',
	'storyboard-submittedbyon' => 'ÐžÑ‚Ð¿Ñ€Ð°Ð²Ð»ÐµÐ½Ð° $1 $2 $3.',
	'storyboard-viewstories' => 'ÐŸÑ€Ð¾Ñ�Ð¼Ð¾Ñ‚Ñ€ Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ð¹',
	'storyboard-nosuchstory' => 'Ð—Ð°Ð¿Ñ€Ð¾ÑˆÐµÐ½Ð½Ð¾Ð¹ Ð²Ð°Ð¼Ð¸ Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ð¸ Ð½Ðµ Ñ�ÑƒÑ‰ÐµÑ�Ñ‚Ð²ÑƒÐµÑ‚.
Ð’Ð¾Ð·Ð¼Ð¾Ð¶Ð½Ð¾, Ð¾Ð½Ð° Ð±Ñ‹Ð»Ð° ÑƒÐ´Ð°Ð»ÐµÐ½Ð°.',
	'storyboard-storyunpublished' => 'Ð—Ð°Ð¿Ñ€Ð°ÑˆÐ¸Ð²Ð°ÐµÐ¼Ð°Ñ� Ð²Ð°Ð¼Ð¸ Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ñ� ÐµÑ‰Ñ‘ Ð½Ðµ Ð±Ñ‹Ð»Ð° Ð¾Ð¿ÑƒÐ±Ð»Ð¸ÐºÐ¾Ð²Ð°Ð½Ð°.',
	'storyboard-nostorytitle' => 'Ð’Ñ‹ Ð´Ð¾Ð»Ð¶Ð½Ñ‹ ÑƒÐºÐ°Ð·Ð°Ñ‚ÑŒ Ð½Ð°Ð·Ð²Ð°Ð½Ð¸Ðµ Ð¸Ð»Ð¸ Ð¸Ð´ÐµÐ½Ñ‚Ð¸Ñ„Ð¸ÐºÐ°Ñ‚Ð¾Ñ€ Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ð¸, ÐºÐ¾Ñ‚Ð¾Ñ€ÑƒÑŽ Ð²Ñ‹ Ñ…Ð¾Ñ‚Ð¸Ñ‚Ðµ Ð¿Ñ€Ð¾Ñ�Ð¼Ð¾Ñ‚Ñ€ÐµÑ‚ÑŒ.',
	'storyboard-cantedit' => 'Ð’Ñ‹ Ð½Ðµ Ð¼Ð¾Ð¶ÐµÑ‚Ðµ Ñ€ÐµÐ´Ð°ÐºÑ‚Ð¸Ñ€Ð¾Ð²Ð°Ñ‚ÑŒ Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ð¸.',
	'storyboard-canedit' => 'Ð’Ñ‹ Ð¼Ð¾Ð¶ÐµÑ‚Ðµ [$1 Ð¸Ð·Ð¼ÐµÐ½Ð¸Ñ‚ÑŒ] Ð¸ Ð¾Ð¿ÑƒÐ±Ð»Ð¸ÐºÐ¾Ð²Ð°Ñ‚ÑŒ Ñ�Ñ‚Ñƒ Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸ÑŽ.',
	'storyboard-createdandmodified' => 'Ð¡Ð¾Ð·Ð´Ð°Ð½Ð° $1 $2, Ð¸Ð·Ð¼ÐµÐ½ÐµÐ½Ð° $3 $4',
	'storyboard-authorname' => 'Ð˜Ð¼Ñ� Ð°Ð²Ñ‚Ð¾Ñ€Ð°',
	'storyboard-authorlocation' => 'ÐœÐµÑ�Ñ‚Ð¾Ð½Ð°Ñ…Ð¾Ð¶Ð´ÐµÐ½Ð¸Ðµ Ð°Ð²Ñ‚Ð¾Ñ€Ð°',
	'storyboard-authoroccupation' => 'Ð Ð¾Ð´ Ð·Ð°Ð½Ñ�Ñ‚Ð¸Ð¹ Ð°Ð²Ñ‚Ð¾Ñ€Ð°',
	'storyboard-authoremail' => 'Ð�Ð´Ñ€ÐµÑ� Ñ�Ð». Ð¿Ð¾Ñ‡Ñ‚Ñ‹ Ð°Ð²Ñ‚Ð¾Ñ€Ð°',
	'storyboard-thestory' => 'Ð˜Ñ�Ñ‚Ð¾Ñ€Ð¸Ñ�',
	'storyboard-storystate' => 'Ð¡Ð¾Ñ�Ñ‚Ð¾Ñ�Ð½Ð¸Ðµ:',
	'storyboard-submissioncomplete' => 'ÐŸÐ¾Ð´Ð°Ñ‡Ð° Ð¿Ñ€Ð¾Ð¸Ð·Ð²ÐµÐ´ÐµÐ½Ð°',
	'storyreview' => 'ÐŸÑ€Ð¾Ð²ÐµÑ€ÐºÐ° Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ð¸',
	'storyboard-hideimage' => 'Ð¡ÐºÑ€Ñ‹Ñ‚ÑŒ Ð¸Ð·Ð¾Ð±Ñ€Ð°Ð¶ÐµÐ½Ð¸Ðµ',
	'storyboard-unhideimage' => 'ÐŸÐ¾ÐºÐ°Ð·Ð°Ñ‚ÑŒ Ð¸Ð·Ð¾Ð±Ñ€Ð°Ð¶ÐµÐ½Ð¸Ðµ',
	'storyboard-deleteimage' => 'Ð£Ð´Ð°Ð»Ð¸Ñ‚ÑŒ Ð¸Ð·Ð¾Ð±Ñ€Ð°Ð¶ÐµÐ½Ð¸Ðµ',
	'storyboard-yourname' => 'Ð’Ð°ÑˆÐµ Ð¸Ð¼Ñ�',
	'storyboard-location' => 'Ð’Ð°ÑˆÐµ Ð¼ÐµÑ�Ñ‚Ð¾Ð¿Ð¾Ð»Ð¾Ð¶ÐµÐ½Ð¸Ðµ',
	'storyboard-occupation' => 'Ð’Ð°Ñˆ Ñ€Ð¾Ð´ Ð·Ð°Ð½Ñ�Ñ‚Ð¸Ð¹',
	'storyboard-story' => 'Ð’Ð°ÑˆÐ° Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ñ�',
	'storyboard-photo' => 'Ð£ Ð²Ð°Ñ� ÐµÑ�Ñ‚ÑŒ Ð²Ð°ÑˆÐ° Ñ„Ð¾Ñ‚Ð¾Ð³Ñ€Ð°Ñ„Ð¸Ñ�?
ÐŸÐ¾Ñ‡ÐµÐ¼Ñƒ Ð±Ñ‹ Ð½Ðµ Ñ€Ð°Ð·Ð¼ÐµÑ�Ñ‚Ð¸Ñ‚ÑŒ ÐµÑ‘?',
	'storyboard-email' => 'Ð’Ð°Ñˆ Ð°Ð´Ñ€ÐµÑ� Ñ�Ð». Ð¿Ð¾Ñ‡Ñ‚Ñ‹',
	'storyboard-storytitle' => 'ÐšÐ¾Ñ€Ð¾Ñ‚ÐºÐ¸Ð¹ Ð¾Ð¿Ð¸Ñ�Ð°Ñ‚ÐµÐ»ÑŒÐ½Ñ‹Ð¹ Ð·Ð°Ð³Ð¾Ð»Ð¾Ð²Ð¾Ðº',
	'storyboard-agreement' => 'Ð¯ Ñ�Ð¾Ð³Ð»Ð°Ñ�ÐµÐ½ Ñ� Ð¿ÑƒÐ±Ð»Ð¸ÐºÐ°Ñ†Ð¸ÐµÐ¹ Ð¸ Ð¸Ñ�Ð¿Ð¾Ð»ÑŒÐ·Ð¾Ð²Ð°Ð½Ð¸ÐµÐ¼ Ñ�Ñ‚Ð¾Ð¹ Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ð¸ Ð² Ñ�Ð¾Ð¾Ñ‚Ð²ÐµÑ‚Ñ�Ñ‚Ð²Ð¸Ð¸ Ñ� ÑƒÑ�Ð»Ð¾Ð²Ð¸Ñ�Ð¼Ð¸ [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution/Share-Alike License].',
	'storyboard-charsleft' => '({{PLURAL:$1|Ð¾Ñ�Ñ‚Ð°Ð»cÑ� $1 Ñ�Ð¸Ð¼Ð²Ð¾Ð»|Ð¾Ñ�Ñ‚Ð°Ð»Ð¾Ñ�ÑŒ $1 Ñ�Ð¸Ð¼Ð²Ð¾Ð»Ð°|Ð¾Ñ�Ñ‚Ð°Ð»Ð¾Ñ�ÑŒ $1 Ñ�Ð¸Ð¼Ð²Ð¾Ð»Ð¾Ð²}})',
	'storyboard-cannotbelonger' => 'Ð’Ð°ÑˆÐ° Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ñ� Ð´Ð»Ð¸Ð½Ð½ÐµÐµ Ð½Ð° <b>$1</b> {{PLURAL:$1|Ñ�Ð¸Ð¼Ð²Ð¾Ð»|Ñ�Ð¸Ð¼Ð²Ð¾Ð»Ð°|Ñ�Ð¸Ð¼Ð²Ð¾Ð»Ð¾Ð²}}!',
	'storyboard-charsneeded' => '({{PLURAL:$1|Ð½ÐµÐ¾Ð±Ñ…Ð¾Ð´Ð¸Ð¼ ÐµÑ‰Ñ‘ $1 Ñ�Ð¸Ð¼Ð²Ð¾Ð»|Ð½ÐµÐ¾Ð±Ñ…Ð¾Ð´Ð¸Ð¼Ð¾ ÐµÑ‰Ñ‘ $1 Ñ�Ð¸Ð¼Ð²Ð¾Ð»Ð°|Ð½ÐµÐ¾Ð±Ñ…Ð¾Ð´Ð¸Ð¼Ð¾ ÐµÑ‰Ñ‘ $1 Ñ�Ð¸Ð¼Ð²Ð¾Ð»Ð¾Ð²}})',
	'storyboard-needtoagree' => 'Ð’Ñ‹ Ð´Ð¾Ð»Ð¶Ð½Ñ‹ Ð´Ð°Ñ‚ÑŒ Ñ�Ð¾Ð³Ð»Ð°Ñ�Ð¸Ðµ Ð½Ð° Ð¿ÑƒÐ±Ð»Ð¸ÐºÐ°Ñ†Ð¸ÑŽ Ñ�Ð²Ð¾ÐµÐ¹ Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ð¸ Ð¿ÐµÑ€ÐµÐ´ ÐµÑ‘ Ð¾Ñ‚Ð¿Ñ€Ð°Ð²ÐºÐ¾Ð¹.',
	'storyboard-createdsucessfully' => 'Ð¡Ð¿Ð°Ñ�Ð¸Ð±Ð¾, Ñ‡Ñ‚Ð¾ Ñ€Ð°Ñ�Ñ�ÐºÐ°Ð·Ð°Ð»Ð¸ Ð½Ð°Ð¼ Ñ�Ð²Ð¾ÑŽ Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸ÑŽ!
ÐœÑ‹ Ñ€Ð°Ñ�Ñ�Ð¼Ð¾Ñ‚Ñ€Ð¸Ð¼ ÐµÑ‘ Ð² Ð±Ð»Ð¸Ð¶Ð°Ð¹ÑˆÐµÐµ Ð²Ñ€ÐµÐ¼Ñ�.
Ð’Ñ‹ Ð¼Ð¾Ð¶ÐµÑ‚Ðµ [$1 Ð¿Ñ€Ð¾Ñ‡Ð¸Ñ‚Ð°Ñ‚ÑŒ ÑƒÐ¶Ðµ Ð¾Ð¿ÑƒÐ±Ð»Ð¸ÐºÐ¾Ð²Ð°Ð½Ð½Ñ‹Ðµ Ð¸Ñ�Ñ‚Ð¾Ñ€Ð¸Ð¸].',
);

/** Telugu (à°¤à±†à°²à±�à°—à±�)
 * @author Veeven
 */
$messages['te'] = array(
	'storyboard-publish' => 'à°ªà±�à°°à°šà±�à°°à°¿à°‚à°šà±�',
);

