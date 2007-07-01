<?PHP
$allMessages = array(
	'en' => array( 
		'createsigndocument'         => 'Enable Document Signing',
		'createsigndoc-head'         => 
"Use this form to create a 'Sign Document' page for the provided article, such that
users will be able to sign it via [[Special:SignDocument]]. Please specify the name
of the article on which you wish to enable digital signing, members of which 
usergroup should be allowed to sign it, which fields you wish to be visible to users 
and which should be optional, a minimum age to require users to be to sign the 
document (no minimum if omitted), and a brief introductory text describing the 
document and providing instructions to users.

<b>There is presently no way to delete or modify signature documents after they are
created</b> without direct database access. Additionally, the text of the article 
displayed on the signature page will be the ''current'' text of the page, regardless
of changes made to it after today. Please be absolutely positive that the document
is to a point of stability for signing, and please also be sure that you specify
all fields exactly as they should be, ''before submitting this form''.",
		'createsigndoc-pagename'     => 'Page:',
		'createsigndoc-allowedgroup' => 'Allowed group:',
		'createsigndoc-email'        => 'E-mail address:',
		'createsigndoc-address'      => 'House Address:',
		'createsigndoc-extaddress'   => 'City, State, Country:',
		'createsigndoc-phone'        => 'Phone number:',
		'createsigndoc-bday'         => 'Birthdate:',
		'createsigndoc-minage'       => 'Minimum age:',
		'createsigndoc-introtext'    => 'Introduction:',
		'createsigndoc-hidden'       => 'Hidden',
		'createsigndoc-optional'     => 'Optional',
		'createsigndoc-create'       => 'Create',
		'createsigndoc-error-generic'=> 'Error: $1',
		'createsigndoc-error-pagenoexist' => 'Error: The page [[$1]] does not exist.',
		'createsigndoc-success'      => 'Document signing has been successfully enabled
on [[$1]]. To test it, please visit [{{SERVER}}{{localurl: Special:SignDocument|doc=$2}} this page].',
	)
);
?>
