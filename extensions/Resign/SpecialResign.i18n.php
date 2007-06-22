<?php

/**
 * Internationalisation file for Resign extension
 *
 * @addtogroup Extensions
*/

function efResignMessages() {
	$messages = array(
	
'en' => array(
'resign'           => 'Resign',
'resign-text'      => "You can '''remove your permissions''' (e.g. sysop) in this page. Please check the permissions you would like to remove.

Remember: '''You cannot get your permissions back this way!''' You will have to convince a bureaucrat or steward to grant you back the permissions. Please use this page very carefully.",
'resign-reason'    => 'Reason:',
'resign-confirm'   => 'I confirm that I would like to remove all the permissions I checked.',
'resign-noconfirm' => 'Please check the confirm checkbox if you would like to remove your permissions.',
'resign-nogroups'  => 'Please check the permissions you would like to remove.',
'resign-success'   => "'''The permissions were successfully removed.'''",
'resign-logentry'  => 'removed permissions $2 from himself',
),

'he' => array(
'resign'           => 'התפטרות',
'resign-text'      => "בדף זה תוכלו '''להסיר את ההרשאות שלכם''' (למשל, הרשאות מפעיל מערכת). אנא סמנו את ההרשאות שברצונכם להסיר.

להזכירכם: '''אינכם יכולים לקבל בחזרה את ההרשאות בדרך זו!''' יהיה עליכם לשכנע ביורוקרט או דייל להעניק לכם את הרשאותיכם בחזרה. אנא השתמשו בדף זה בזהירות רבה.",
'resign-reason'    => 'סיבה:',
'resign-confirm'   => 'אני מאשר שברצוני להסיר את כל ההרשאות שלי שסימנתי, וידוע לי שלא אוכל לקבל אותן בחזרה בדרך זו.',
'resign-noconfirm' => 'אנא סמנו את תיבת הסימון כדי לאשר את ההסרה.',
'resign-nogroups'  => 'אנא בחרו את ההרשאות שברצונכם להסיר.',
'resign-success'   => "'''ההרשאות הוסרו בהצלחה.'''",
'resign-logentry'  => 'הסיר את ההרשאות $2 שלו',
),

	);
	return $messages;
}

?>
