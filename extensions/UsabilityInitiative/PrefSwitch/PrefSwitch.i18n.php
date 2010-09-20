<?php
/**
 * Internationalisation for Usability Initiative PrefSwitch extension
 *
 * @file
 * @ingroup Extensions
 */
$messages = array();

/** English
 * @author Roan Kattouw
 */
$messages['en'] = array(
	'prefswitch' => 'Usability Initiative preference switch',
	'prefswitch-desc' => 'Allow users to switch sets of preferences',
	/* User Tools Links */
	'prefswitch-link-anon' => 'New features',
	'tooltip-pt-prefswitch-link-anon' => 'Learn about new features',
	'prefswitch-link-on' => 'Take me back',
	'tooltip-pt-prefswitch-link-on' => 'Disable new features',
	'prefswitch-link-off' => 'New features',
	'tooltip-pt-prefswitch-link-off' => 'Try out new features',
	/* Page Content */
	'prefswitch-jswarning' => 'Remember that with the skin change, your [[User:$1/$2.js|$2 JavaScript]] will need to be copied to [[{{ns:user}}:$1/vector.js]] <!-- or [[{{ns:user}}:$1/common.js]]--> to continue working.',
	'prefswitch-csswarning' => 'Your [[User:$1/$2.css|custom $2 styles]] will no longer be applied. You can add custom CSS for vector in [[{{ns:user}}:$1/vector.css]].',
	'prefswitch-survey-true' => 'Yes',
	'prefswitch-survey-false' => 'No',
	'prefswitch-survey-submit-off' => 'Turn new features off',
	'prefswitch-survey-cancel-off' => 'If you would like to continue using the new features, you can return to $1.',
	'prefswitch-survey-submit-feedback' => 'Send feedback',
	'prefswitch-survey-cancel-feedback' => 'If you do not want to provide feedback, you can return to $1.',
	'prefswitch-survey-question-like' => 'What did you like about the new features?',
	'prefswitch-survey-question-dislike' => 'What did you dislike about the features?',
	'prefswitch-survey-question-whyoff' => 'Why are you turning off the new features?
Please select all that apply.',
	'prefswitch-survey-question-globaloff' => 'Do you want the features turned off globally?',
	'prefswitch-survey-answer-whyoff-hard' => 'The features were too hard to use.',
	'prefswitch-survey-answer-whyoff-didntwork' => 'The features did not function properly.',
	'prefswitch-survey-answer-whyoff-notpredictable' => 'The features did not perform predictably.',
	'prefswitch-survey-answer-whyoff-didntlike-look' => 'I did not like the way the features looked.',
	'prefswitch-survey-answer-whyoff-didntlike-layout' => 'I did not like the new tabs and layout.',
	'prefswitch-survey-answer-whyoff-didntlike-toolbar' => 'I did not like the new toolbar.',
	'prefswitch-survey-answer-whyoff-other' => 'Other reason:',
	'prefswitch-survey-question-browser' => 'Which browser do you use?',
	'prefswitch-survey-answer-browser-ie5' => 'Internet Explorer 5',
	'prefswitch-survey-answer-browser-ie6' => 'Internet Explorer 6',
	'prefswitch-survey-answer-browser-ie7' => 'Internet Explorer 7',
	'prefswitch-survey-answer-browser-ie8' => 'Internet Explorer 8',
	'prefswitch-survey-answer-browser-ie9' => 'Internet Explorer 9',
	'prefswitch-survey-answer-browser-ffb' => 'Firefox Beta',
	'prefswitch-survey-answer-browser-ff1' => 'Firefox 1',
	'prefswitch-survey-answer-browser-ff2' => 'Firefox 2',
	'prefswitch-survey-answer-browser-ff3' => 'Firefox 3',
	'prefswitch-survey-answer-browser-cb' => 'Google Chrome Beta',
	'prefswitch-survey-answer-browser-cd' => 'Google Chrome Dev',
	'prefswitch-survey-answer-browser-c1' => 'Google Chrome 1',
	'prefswitch-survey-answer-browser-c2' => 'Google Chrome 2',
	'prefswitch-survey-answer-browser-c3' => 'Google Chrome 3',
	'prefswitch-survey-answer-browser-c4' => 'Google Chrome 4',
	'prefswitch-survey-answer-browser-c5' => 'Google Chrome 5',
	'prefswitch-survey-answer-browser-s3' => 'Safari 3',
	'prefswitch-survey-answer-browser-s4' => 'Safari 4',
	'prefswitch-survey-answer-browser-s5' => 'Safari 5',
	'prefswitch-survey-answer-browser-o9' => 'Opera 9',
	'prefswitch-survey-answer-browser-o9.5' => 'Opera 9.5',
	'prefswitch-survey-answer-browser-o10' => 'Opera 10',
	'prefswitch-survey-answer-browser-other' => 'Other browser:',
	'prefswitch-survey-question-os' => 'Which operating system do you use?',
	'prefswitch-survey-answer-os-windows' => 'Windows',
	'prefswitch-survey-answer-os-windowsmobile' => 'Windows Mobile',
	'prefswitch-survey-answer-os-macos' => 'Mac OS',
	'prefswitch-survey-answer-os-iphoneos' => 'iPhone OS',
	'prefswitch-survey-answer-os-ios' => 'iOS',
	'prefswitch-survey-answer-os-linux' => 'Linux',
	'prefswitch-survey-answer-os-other' => 'Other operating system:',
	'prefswitch-survey-answer-globaloff-yes' => 'Yes, turn the features off on all wikis',
	'prefswitch-survey-question-res' => 'What is the resolution of your screen?',
	'prefswitch-title-on' => 'New features',
	'prefswitch-title-switched-on' => 'Enjoy!',
	'prefswitch-title-off' => 'Turn new features off',
	'prefswitch-title-switched-off' => 'Thanks',
	'prefswitch-title-feedback' => 'Feedback',
	'prefswitch-success-on' => 'New features are now turned on. We hope you enjoy using the new features. You may always turn them back off by clicking on the "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-on}}]]" link at the top of the page.',
	'prefswitch-success-off' => 'New features are now turned off. Thanks for trying the new features. You may always turn them back on by clicking on the "[[Special:UsabilityInitiativePrefSwitch|{{int:prefswitch-link-off}}]]" link at the top of the page.',
	'prefswitch-success-feedback' => 'Your feedback has been sent.',
	'prefswitch-return' => '<hr style="clear:both">
Return to <span class="plainlinks">[$1 $2]</span>.',
	'prefswitch-main' => "{| border=\"0\" align=\"right\" style=\"margin-left:1em\"
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorNavigation-en.png|401px|]]
|-
| A screenshot of Wikipedia's new navigation interface <small>[[Media:VectorNavigation-en.png|(enlarge)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorEditorBasic-en.png|401px|]]
|-
| A screenshot of the basic page editing interface <small>[[Media:VectorEditorBasic-en.png|(enlarge)]]</small>
|}
|-
| align=\"center\" |
{| border=\"0\" style=\"background:#F3F3F3;border: 1px solid #CCCCCC;padding:10px;\"
| [[Image:VectorLinkDialog-en.png|401px|]]
|-
| A screenshot of the new dialog box for entering links
|}
|}
The Wikimedia Foundation's User Experience Team has been working with volunteers from the community to make things easier for you. We are excited to share some improvements, including a new look and feel and simplified editing features. These changes are intended to make it easier for new contributors to get started, and are based on our [http://usability.wikimedia.org/wiki/Usability,_Experience,_and_Evaluation_Study usability testing conducted over the last year]. Improving the usability of our projects is a priority of the Wikimedia Foundation and we will be sharing more updates in the future. For more details, visit the related Wikimedia [http://blog.wikimedia.org/2010/05/13/a-new-look-for-wikipedia blog post].

=== Here's what we have changed ===
* '''Navigation:''' We have improved the navigation for reading and editing pages. Now, the tabs at the top of each page more clearly define whether you are viewing the page or discussion page, and whether you are reading or editing a page.
* '''Editing toolbar improvements:''' We have reorganized the editing toolbar to make it easier to use. Now, formatting pages is simpler and more intuitive.
* '''Link wizard:''' An easy-to-use tool allows you to add links to other wiki pages as well as links to external sites.
* '''Search improvements:''' We have improved search suggestions to get you to the page you are looking for more quickly.
* '''Other new features:''' We have also introduced a table wizard to make creating tables easier and a find and replace feature to simplify page editing.
* '''Wikipedia logo:''' We have updated our logo. Read more at the [http://blog.wikimedia.org/2010/05/13/wikipedia-in-3d Wikimedia blog].",
	'prefswitch-main-logged-changes' => "* The '''{{int:watch}} tab''' is now a star.
* The '''{{int:move}} tab''' is now in the dropdown next to the search bar.",
	'prefswitch-main-feedback' => "===Feedback?===
We would love to hear from you. Please visit our [[$1|feedback page]] or, if you are interested in our ongoing efforts to improve the software, visit our [http://usability.wikimedia.org usability wiki] for more information.",
	'prefswitch-main-anon' => "===Take me back===
[$1 Click here to turn off the new features]. You will be asked to login or create an account first.",
	'prefswitch-main-on' => "===Take me back!===
[$2 Click here to turn off the new features].",
	'prefswitch-main-off' => "===Try them out!===
[$1 Click here to enable the new features].",
	'prefswitch-survey-intro-feedback' => 'We would love to hear from you.
Please fill out the optional survey below before clicking "[[#prefswitch-survey-submit-feedback|{{int:Prefswitch-survey-submit-feedback}}]]".',
	'prefswitch-survey-intro-off' => 'Thanks for trying out our new features.
To help us improve them, please fill out the optional survey below before clicking "[[#prefswitch-survey-submit-off|{{int:Prefswitch-survey-submit-off}}]]".',
	'prefswitch-feedbackpage' => 'Project:User experience feedback',
);
