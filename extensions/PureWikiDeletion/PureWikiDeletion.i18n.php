<source lang="php">
<?php
$messages = array();
 
$messages['en'] = array( 
	'purewikideletion' => 'Pure wiki deletion',
        'randomexcludeblank' => 'Random page (exclude blank)',
        'populateblankedpagestable' => 'Populate blanked pages table',
        'purewikideletion-desc' => 'Among other things, causes blanked pages to be redlinked',
        'purewikideletion-pref-watchblank' => 'Add pages I blank to my watchlist',
        'purewikideletion-pref-watchunblank' => 'Add pages I unblank to my watchlist',
        'purewikideletion-blanked' => "A former version of this page was blanked by [[User:$1|$1]] ([[User talk:$1|talk]]) "
            ."([[Special:Contributions/$1|contribs]]) on $2 <br /> The reason given for blanking was: "
            ."''<nowiki>$3</nowiki>''.<br /> You may [{{fullurl:{{FULLPAGENAMEE}}|action=history}} view the article's "
            ."history], [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} edit the last version], or type new "
            ."page into the white space below.",
        'blank'         =>  'blank',
        'blank-log-name' => 'Blank log',
        'blank-log-header' => 'Below is a list of page blankings and unblankings.',
        'blank-log-entry-blank' => 'blanked $1',
        'blank-log-entry-unblank' => 'unblanked $1',
        'blanknologin' => 'Not logged in',
        'blanknologintext' => 'You must be a registered user and '
        .'[[Special:UserLogin|logged in]] to blank or unblank a page.'
);