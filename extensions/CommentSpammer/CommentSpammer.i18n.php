<?php
/**
 * Internationalisation file for the CommentSpammer extension.
 * @addtogroup Extensions
 */

/** 
 * @return array Messages.
 */
function efCommentSpammerMessages() {
        $messages = array(

/* English */
'en' => array(
	'commentspammer-save-blocked' => 'Your IP address is a suspected comment spammer, so the page has not been saved. [[Special:Userlogin|Log in or create an account]] to avoid this.',
	'commentspammer-log-msg'      => 'edit from [[Special:Contributions/$1|$1]] to [[$2]]. ',
	'commentspammer-log-msg-info' => 'Last spammed $1 {{PLURAL:$1|day|days}} ago, threat level is $2, and offence code is $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 View details], or [[Special:Blockip/$4|block]].',
	'cspammerlogpagetext'         => 'Record of edits that have been allowed or denied based on whether the source was a known comment spammer.',
	'cspammer-log-page'           => 'Comment Spammer log',
),

/* Other languages here */
'ar' => array(
	'commentspammer-save-blocked' => 'عنوان الأيبي الخاص بك هو معلق سبام مشتبه، لذا لم يتم حفظ الصفحة. [[Special:Userlogin|ادخل أو سجل حسابا]] لتجنب هذا.',
	'commentspammer-log-msg' => 'تعديل من [[Special:Contributions/$1|$1]] ل[[$2]].',
	'commentspammer-log-msg-info' => 'آخر سبام منذ $1 {{PLURAL:$1|يوم|يوم}} ، مستوى التهديد هو $2، و كود الإساءة هو $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 عرض التفاصيل]، أو [[Special:Blockip/$4|منع]].',
	'cspammerlogpagetext' => 'سجل التعديلات التي تم السماح بها أو رفضها بناء على ما إذا كان المصدر معلق سبام معروف.',
	'cspammer-log-page' => 'سجل تعليق السبام',
),

        );

        return $messages;
}
