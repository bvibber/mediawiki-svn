<?PHP

/* Messages for Special:SignDocument. Likely incomplete. Please do not translate yet. */
$allMessages = array(
	'en' => array( 
		'signdocument'         => 'Sign Document',
		'sign-nodocselected'   => 'Please select the document you wish to sign.',
		'sign-selectdoc'       => 'Document:',
		'sign-docheader'       => 
'<div class="noarticletext">Please use this form to sign the document "[[$1]]," ' . 
'shown below. Please read through the entire document, and if you wish to indicate your '.
'support for it, please fill in the required fields to sign it.</div>',
		'sign-error-nosuchdoc' => 'The document you requested ($1) does not exist.',
		'sign-realname'        => 'Name:',
		'sign-address'         => 'Street address:',
		'sign-city'            => 'City:',
		'sign-state'           => 'State:',
		'sign-zip'             => 'Zip code:',
		'sign-country'         => 'Country:',
		'sign-phone'           => 'Phone number:',
		'sign-bday'            => 'Age:',
		'sign-email'           => 'E-mail address:',
		'sign-indicates-req'   => '<small><i><font color="red">*</font> indicates required field.</i></small>',
		'sign-hide-note'       => 
'<small><i><font color="red">**</font> Note: Unlisted information will still be visible to moderators.</i></small>',
		'sign-list-anonymous'  => 'List anonymously',
		'sign-list-hideaddress'=> 'Do not list address',
		'sign-list-hideextaddress'=>'Do not list city, state, zip, or country',
		'sign-list-hidephone'  => 'Do not list phone',
		'sign-list-hidebday'   => 'Do not list age',
		'sign-list-hideemail'  => 'Do not list e-mail',
		'sign-submit'          => 'Sign document',
		'sign-information'     =>
'<div class="noarticletext">Thank you for taking the time to read through this document. '.
'If you agree with it, please indicate your support by filling in the required fields ' .
'below and clicking "Sign Document." Please ' .
'ensure that your personal information is correct and that we have some way to contact you ' .
'to verify your identity. Note that your [[wikipedia:IP address|IP address]] ' .
'and other identifying information will be recorded by this form and used by moderators ' . 
'to eliminate duplicate signatures and confirm the correctness of your personal information. ' .
'As the use of open and anonymizing proxies inhibits our ability to perform this task, ' . 
'signatures from such proxies will likely not be counted. If you are currently connected ' . 
'through a proxy server, please disconnect from it and use a standard connection while signing. '.
'<br /><br /> $1',
		'sig-success'               => 'You have successfully signed the document.',
		'sign-view-selectfields'    => '<b>Fields to display:</b>',
		'sign-viewfield-entryid'    => 'Entry id',
		'sign-viewfield-timestamp'  => 'Timestamp',
		'sign-viewfield-realname'   => 'Name',
		'sign-viewfield-address'    => 'Address',
		'sign-viewfield-city'       => 'City',
		'sign-viewfield-state'      => 'State',
		'sign-viewfield-country'    => 'Country',
		'sign-viewfield-zip'        => 'Zip',
		'sign-viewfield-ip'         => 'IP address',
		'sign-viewfield-agent'      => 'User agent',
		'sign-viewfield-phone'      => 'Phone',
		'sign-viewfield-email'      => 'Email',
		'sign-viewfield-age'        => 'Age',
		'sign-viewfield-options'    => 'Options',
		'sign-viewsigs-intro'       => 'Shown below are the signatures recorded for ' .
		'<span class="plainlinks">[{{SERVER}}{{localurl: Special:SignDocument|doc=$2}} $1]</span>.',
		'sign-sigadmin-currentlyopen'=>'Signing is currently enabled for this document.',
		'sign-sigadmin-close'       => 'Disable signing',
		'sign-sigadmin-currentlyclosed'=>'Signing is currently disabled for this document.',
		'sign-sigadmin-open'        => 'Enable signing',
		'sign-signatures'           => 'Signatures',
		'sign-sigadmin-closesuccess'=> 'Signing successfully disabled.',
		'sign-sigadmin-opensuccess' => 'Signing successfully enabled.',
		'sign-viewsignatures'       => 'view signatures',
		'sign-closed'               => 'closed',
		'sign-error-closed'         => 'Signing of this document is currently disabled.',
		'sig-anonymous'             => '<i>Anonymous</i>',
		'sig-private'               => '<i>Private</i>',
		'sign-sigdetails'           => 'Signature details',
		'sign-emailto'              => '<a href="mailto:$1">$1</a>',
		'sign-iptools'              => '<span class="plainlinksneverexpand"><!--
		-->[[User:$1|$1]] ([[User talk:$1|talk]] • <!--
		-->[[Special:Contributions/$1|contribs]] • <!--
		-->[http://www.dnsstuff.com/tools/whois.ch?domain={{urlencode:$1}}&cache=off&email=on WHOIS] • <!--
		-->[http://www.dnsstuff.com/tools/ptr.ch?ip={{urlencode:$1}}&cache=off&email=on RDNS] • <!--
		-->[http://www.robtex.com/rbls/$1.html RBLs] • <!--
		-->[[Special:Blockip/$1|block user]] • <!--
		-->[{{fullurl:Special:Log/block|page=User:{{urlencode:$1}}}} block log] • <!--
		-->[{{fullurl:Special:CheckUser|ip={{urlencode:$1}}}} checkip])<!--
		--></span>',
		'sign-viewfield-stricken'      => 'Stricken',
		'sign-viewfield-reviewedby'    => 'Reviewer',
		'sign-viewfield-reviewcomment' => 'Comment',
		'sign-detail-uniquequery'      => 'Similar entities',
		'sign-detail-uniquequery-run'  => 'Run query',
		'sign-detail-strike'           => 'Strike signature',
		'sign-reviewsig'               => 'Review signature',
		'sign-review-comment'          => 'Comment',
		'sign-submitreview'            => 'Submit review',
		'sign-uniquequery-similarname' => 'Similar name',
		'sign-uniquequery-similaraddress'=> 'Similar address',
		'sign-uniquequery-similarphone'=> 'Similar phone',
		'sign-uniquequery-similaremail'=> 'Similar email',
		'sign-uniquequery-1signed2'    => '[{{SERVER}}{{localurl: Special:SignDocument|doc=$4&viewsigs&detail=$3}} '.
		'$1] signed [{{SERVER}}{{localurl: Special:SignDocument|doc=$4&viewsigs}} $2].',
	)
);
?>
