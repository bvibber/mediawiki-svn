<?php

$text = <<<EOT
<p>This request has been identified as coming from a remote-loading website. This 
is not <a href="http://www.wikipedia.org/">Wikipedia</a>, please update your 
bookmarks. Access Wikipedia only through *.wikipedia.org.
</p>
<p>
A remote loader is a website that loads content from another site on each request.
The content is typically filtered, framed with ads, and then displayed to the user. 
</p>
<p>The remote loader either:</p>
<ul>
<li>Pretends to be the source website, perhaps using a deceptive domain name; or</li>
<li>Converts all instances of the name of the source website to some other name.</li>
</ul>
<p>
We consider remote loading websites to be an unfair drain on our server resources, 
and so they are systematically blocked, as this one has been.
</p>
EOT;

for ( $i = 0; $i < strlen( $text ); $i++ ) {
	echo '%' . bin2hex( $text[$i] );
}

?>
