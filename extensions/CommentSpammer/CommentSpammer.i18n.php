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
'commentspammer-save-blocked' => 'Your IP address is a suspected comment spammer - save blocked. Create an account to avoid this.',
'commentspammer-log-msg'      => 'edit from [[Special:Contributions/$1|$1]] to [[$2]]. ',
'commentspammer-log-msg-info' => 'Last spammed $1 {{PLURAL:$1|day|days}} ago, threat level is $2, and offence code is $3. [http://www.projecthoneypot.org/search_ip.php?ip=$4 View details], or [[Special:Blockip/$4|block]].',
'cspammerlogpagetext'         => 'Record of edits that have been allowed or denied based on whether the source was a known comment spammer.',
'cspammer-log-page'           => 'Comment Spammer log',
),

/* Other languages here */

        );

        return $messages;
}
