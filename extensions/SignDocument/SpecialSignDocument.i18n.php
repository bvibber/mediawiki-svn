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
	),
	'hsb' => array(
		'signdocument' => 'Dokument podpisać',
		'sign-nodocselected' => 'Prošu wubjer dokument, kotryž chceš podpisać.',
		'sign-selectdoc' => 'Dokument:',
		'sign-error-nosuchdoc' => 'Dokument, kotryž sy požadał ($1) njeeksistuje.',
		'sign-realname' => 'Mjeno:',
		'sign-address' => 'Hasa:',
		'sign-city' => 'Město:',
		'sign-state' => 'Stat:',
		'sign-zip' => 'Póstowe wodźenske čisło:',
		'sign-country' => 'Kraj:',
		'sign-phone' => 'Telefonowe čisło:',
		'sign-bday' => 'Staroba:',
		'sign-email' => 'E-mejlowa adresa:',
		'sign-indicates-req' => '<small><i><font color="red">*</font> trěbne polo poznamjenja.</i></small>',
		'sign-hide-note' => '<small><i><font color="red">**</font> Kedźbu: Njenalistowane informacije budu hišće moderatoram widźomne być.</i></small>',
		'sign-list-anonymous' => 'Anonymnje nalistować',
		'sign-list-hideaddress' => 'Njenalistuj adresu',
		'sign-list-hideextaddress' => 'Njenalistuj město, stat, póstowe wodźenske čisło abo kraj',
		'sign-list-hidephone' => 'Njenalistuj telefonowe čisło',
		'sign-list-hidebday' => 'Njenalistuj starobu',
		'sign-list-hideemail' => 'Njenalistuj e-mejlowu adresu',
		'sign-submit' => 'Dokument podpisać',
		'sig-success' => 'Sy dokument wuspěšnje podpisał.',
		'sign-view-selectfields' => '<b>Pola, kotrež maja so zwobraznić:</b>',
		'sign-viewfield-entryid' => 'ID zapiska',
		'sign-viewfield-timestamp' => 'Časowy kołk',
		'sign-viewfield-realname' => 'Mjeno',
		'sign-viewfield-address' => 'Adresa',
		'sign-viewfield-city' => 'Město',
		'sign-viewfield-state' => 'Stat',
		'sign-viewfield-country' => 'Kraj',
		'sign-viewfield-zip' => 'Póstowe wodźenske čisło',
		'sign-viewfield-ip' => 'IP-adresa',
		'sign-viewfield-phone' => 'Telefonowe čisło',
		'sign-viewfield-email' => 'E-mejl',
		'sign-viewfield-age' => 'Staroba',
		'sign-viewfield-options' => 'Opcije',
		'sign-sigadmin-currentlyopen' => 'Podpisanje je tuchwilu za tutón dokument zmóžnjene.',
		'sign-sigadmin-close' => 'Podpisanje znjemóžnić',
		'sign-sigadmin-currentlyclosed' => 'Podpisanje je tuchwilu za tutón dokument znjemóžnjene.',
		'sign-sigadmin-open' => 'Podpisanje zmóžnić',
		'sign-signatures' => 'Podpisy',
		'sign-sigadmin-closesuccess' => 'Podpisanje wuspěšnje znjemóžnjene.',
		'sign-sigadmin-opensuccess' => 'Podpisanje wuspěšnje zmóžnjene.',
		'sign-viewsignatures' => 'Podpisy sej wobhladać',
		'sign-closed' => 'začinjeny',
		'sign-error-closed' => 'Podpisanje tutoho dokumenta je tuchwilu znjemóžnjene.',
		'sig-anonymous' => '<i>Anonymny</i>',
		'sig-private' => '<i>Priwatny</i>',
		'sign-sigdetails' => 'Podrobnosće podpisanja',
		'sign-emailto' => '<a href="mailto:$1">$1</a>',#identical but defined
		'sign-viewfield-reviewedby' => 'Pruwowar',
		'sign-viewfield-reviewcomment' => 'Komentar',
		'sign-detail-uniquequery' => 'Podobne entity',
		'sign-detail-uniquequery-run' => 'Wotprašenje startować',
		'sign-detail-strike' => 'Podpis šmórnyć',
		'sign-reviewsig' => 'Podpis přepruwować',
		'sign-review-comment' => 'Komentar',
		'sign-uniquequery-similarname' => 'Podobne mjeno',
		'sign-uniquequery-similaraddress' => 'Podobna adresa',
		'sign-uniquequery-similarphone' => 'Podobne telefonowe čisło',
		'sign-uniquequery-similaremail' => 'Podobna e-mejlowa adresa',
	),
	'nl' => array( 
		'signdocument'         => 'Document ondertekenen',
		'sign-nodocselected'   => 'Selecteer alstublieft het document dat u wilt ondertekenen.',
		'sign-selectdoc'       => 'Document:',
		'sign-docheader'       => 
'<div class="noarticletext">Gebruik alstublieft dit formulier om het document "[[$1]]," ' . 
'dat heronder wordt getoond, te ondertekenen. Lees alstublieft het hele document en als u het wilt steunen '.
'vul dan alstublieft de verplichte velden in om het te ondertekenen.</div>',
		'sign-error-nosuchdoc' => 'Het opgegeven document ($1) bestaat niet.',
		'sign-realname'        => 'Naam:',
		'sign-address'         => 'Straat:',
		'sign-city'            => 'Plaats:',
		'sign-state'           => 'Staat:',
		'sign-zip'             => 'Postcode:',
		'sign-country'         => 'Land:',
		'sign-phone'           => 'Telefoonnummer:',
		'sign-bday'            => 'Leeftijd:',
		'sign-email'           => 'E-mailadres:',
		'sign-indicates-req'   => '<small><i><font color="red">*</font> geeft verplichte velden aan.</i></small>',
		'sign-hide-note'       => 
'<small><i><font color="red">**</font> Nota bene: Informatie die niet wordt weergegeven, blijft zichtbaar voor beheerders.</i></small>',
		'sign-list-anonymous'  => 'Neem anoniem deel',
		'sign-list-hideaddress'=> 'Verberg straat',
		'sign-list-hideextaddress'=>'Verberg plaats, staat, postcode en/of land',
		'sign-list-hidephone'  => 'Verberg telefoonnummer',
		'sign-list-hidebday'   => 'Verberg leeftijd',
		'sign-list-hideemail'  => 'Verberg e-mailadres',
		'sign-submit'          => 'Document ondertekenen',
		'sign-information'     =>
'<div class="noarticletext">Dank u wel voor het nemen van de tijd om dit document door te lezen. '.
'Als u ermee instemt, geef uw steun dan alstublieft aan door hieronder de benodigde velden in te vullen ' .
'en daar te klikken op "Dcoument ondertekenen." Zorg er alstublieft voor dat uw persoonlijke ' .
'informatie correct is en dat we op een of andre manier contact met u kunnen opnemen. ' .
'om uw indentiteit te bevestigen. Uw [[wikipedia:IP address|IP-adres]] en andere identificerende ' .
'informatie die via dit formulier woren opgeslagen, worden gebruikt voor beheerders om dubbele ' . 
'ondertekeningen te verwijderen en om de juistheid van uw persoonlijke informatie te toetsen. ' .
'Omdat het gebruik van open en anonimiserende proxy\'s voorkomt dat deze taak uitgevoerd kan worden, ' . 
'worden ondertekeningen via deze wegen waarschijnlij niet meegeteld. Als u op dit moment verbonden ' . 
'bent via een proxyserver, maak dan voor het ondertekenen een directe verbinding. '.
'<br /><br /> $1',
		'sig-success'               => 'U heeft het document succesvol ondertekend.',
		'sign-view-selectfields'    => '<b>Te tonen velden:</b>',
		'sign-viewfield-entryid'    => 'ID-nummer',
		'sign-viewfield-timestamp'  => 'Tijdstip',
		'sign-viewfield-realname'   => 'Naam',
		'sign-viewfield-address'    => 'Adres',
		'sign-viewfield-city'       => 'Plaats',
		'sign-viewfield-state'      => 'Staat',
		'sign-viewfield-country'    => 'Land',
		'sign-viewfield-zip'        => 'Postcode',
		'sign-viewfield-ip'         => 'IP-address',
		'sign-viewfield-agent'      => 'User agent',
		'sign-viewfield-phone'      => 'Telefoonnummer',
		'sign-viewfield-email'      => 'E-mailadres',
		'sign-viewfield-age'        => 'Leeftijd',
		'sign-viewfield-options'    => 'Opties',
		'sign-viewsigs-intro'       => 'Hieronder worden de ondertekeningen getoond voor ' .
		'<span class="plainlinks">[{{SERVER}}{{localurl: Special:SignDocument|doc=$2}} $1]</span>.',
		'sign-sigadmin-currentlyopen'=>'Ondertekenen is ingeschakeld voor dit document.',
		'sign-sigadmin-close'       => 'Onderteken uitschakelen',
		'sign-sigadmin-currentlyclosed'=>'Onderteken is uitgeschakeld voor dit document.',
		'sign-sigadmin-open'        => 'Ondertekenen inschakelen',
		'sign-signatures'           => 'Ondertekeningen',
		'sign-sigadmin-closesuccess'=> 'Ondertekenen uitgeschakeld.',
		'sign-sigadmin-opensuccess' => 'Ondertekenen ingeschakeld.',
		'sign-viewsignatures'       => 'ondertekeningen bekijken',
		'sign-closed'               => 'gesloten',
		'sign-error-closed'         => 'Onderteken eis uitgeschakeld voor dit document.',
		'sig-anonymous'             => '<i>Anoniem</i>',
		'sig-private'               => '<i>Privé</i>',
		'sign-sigdetails'           => 'Ondertekeningsdetails',
		'sign-iptools'              => '<span class="plainlinksneverexpand"><!--
		-->[[User:$1|$1]] ([[User talk:$1|ovelreg]] • <!--
		-->[[Special:Contributions/$1|bijdragen]] • <!--
		-->[http://www.dnsstuff.com/tools/whois.ch?domain={{urlencode:$1}}&cache=off&email=on WHOIS] • <!--
		-->[http://www.dnsstuff.com/tools/ptr.ch?ip={{urlencode:$1}}&cache=off&email=on RDNS] • <!--
		-->[http://www.robtex.com/rbls/$1.html RBL\'s] • <!--
		-->[[Special:Blockip/$1|blokkeer gebruiker]] • <!--
		-->[{{fullurl:Special:Log/block|page=User:{{urlencode:$1}}}} blokkerlogboek] • <!--
		-->[{{fullurl:Special:CheckUser|ip={{urlencode:$1}}}} IP controleren])<!--
		--></span>',
		'sign-viewfield-stricken'      => 'Doorgehaald',
		'sign-viewfield-reviewedby'    => 'Controleur',
		'sign-viewfield-reviewcomment' => 'Opmerking',
		'sign-detail-uniquequery'      => 'Gelijkaardige entiteiten',
		'sign-detail-uniquequery-run'  => 'Zoekopdracht uitvoeren',
		'sign-detail-strike'           => 'Ondertekening doorhalen',
		'sign-reviewsig'               => 'Ondertekening controleren',
		'sign-review-comment'          => 'Opmerking',
		'sign-submitreview'            => 'Controle opslaan',
		'sign-uniquequery-similarname' => 'Gelijkaardige naam',
		'sign-uniquequery-similaraddress'=> 'Gelijkaardige adres',
		'sign-uniquequery-similarphone'=> 'Gelijkaardige telefoonnummer',
		'sign-uniquequery-similaremail'=> 'Gelijkaardige e-mailadres',
	),
);
?>
