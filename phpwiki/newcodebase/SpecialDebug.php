<?

function wfSpecialDebug()
{
	global $wgUser, $wgOut;

	$wgOut->addWikiText( "

== Heading ==

: Indent

* List
* List2

 pre
 section

[http://www.piclab.com Piclab] xxx

[http://www.piclab.com] xxx

[mailto:lee@piclab.com Email]

[http://www.piclab.com]

[[Main Page]] xxx

[[Non-existent page]] xxx

[[Main Page|New title]]xxx

[[Raise (poker)|]] xxx

[[talk:poker]] xxx

ISBN 8574638209

''Italic'' '''Bold''' '''''Bold Italic'''''

<nowiki>
== Heading ==

: Indent

* List
* List2

ISBN 8574638209

''Italic'' '''Bold''' '''''Bold Italic'''''
</nowiki>

" );

}

?>
