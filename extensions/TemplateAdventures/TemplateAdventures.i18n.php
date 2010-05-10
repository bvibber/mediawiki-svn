<?php

$messages = array();

/** English */
$messages['en'] = array (
	# citation separators
	'ta-citesep-section'     => ',',               # separator between sections
	'ta-citesep-name'        => ',&#32;',          # separator between surname and givenname
	'ta-citesep-author'      => '&#059;&#32;',     # separator between authors
	'ta-citesep-authorlast'  => '&#32;&amp;&#32;', # last separator between authors
	'ta-citesep-beforepublication' => ':',         # separator between periodical
	                                               # and publication
	
	# citation messages
	'ta-citeetal'            => "$1 ''et al''.",   # $1 = list of authors
	'ta-citecoauthors'       => '$1$2 $3',          # $1 = authors, $2 = separator
	'ta-citeauthordate'      => '$1 ($2)',         # $1 = authors or separator
	'ta-citeauthoryearnote'  => '$1 [$2]',         # $1 = date
	'ta-citeeditorsplural'   => '$1, eds',         # $1 = editors
	'ta-citeeditorssingular' => '$1, ed',          # $1 = editor
	'ta-includedworktitle'   => "''$1''",
	'ta-citepubmed-url'      => 'http://www.pubmedcentral.nih.gov/articlerender.fcgi?tool=pmcentrez&artid=$1',
	'ta-citetranstitle-render'  => '&#91;$1&#93;',
	'ta-citewrittenat'       => '$1 written at $2', # $1 = separator
	'ta-citeother'               => '$1 $2',           # $1 = separator
	'ta-citeinlanguage'      => '$1 (in $2)',      # $1 = title/link
	'ta-citeformatrender'    => '$1 ($2)',         # $1 = title/link
	'ta-citeperiodical'      => "''$1''",
	'ta-citeperiodicaltitle' => "''$1''",
	'ta-series'              => '$1 $2',           # $1 = separator
	'ta-citepublicationplaceandpublisher'   => '$1 ($2: $3)', # $1 = separator
	'ta-citepublicationplace'   => '$1 ($2)',      # $1 = separator
	'ta-citevolumerender'    => "$1 '''$2'''",     # $1 = separator
	'ta-citeissuerender'     => '$1 ($2)',         # $1 = volume
	'ta-citeatrender'        => '$1: $2',          # $1 = title info
	'ta-citeatseparated'     => '$1 $2',           # $1 = separator
	'ta-citetitletyperender' => '$1 ($2)',         # $1 = title/link
	'ta-citepublisherrender' => '$1 $2',           # $1 = separator
	'ta-citepublished'       => '$1 (published $2)', # $1 = title/link
	'ta-citeeditionrender'   => '$1',
	'ta-citepublication'     => '$1 $2',           # $1 = separator
	'ta-citepublicationdate' => '$1 $2',           # $1 = separator
	'ta-citeretrievedupper'  => '$1 Retrieved $2', # $1 = separator
	'ta-citeretrievedlower'  => '$1 retrieved $2', # $1 = separator
	
	# citation span messages
	'ta-citeprintonlyspan'   => '<span class="printonly">$1</span>',
	'ta-citeaccessdatespan'  => '<span class="reference-accessdate">$1</span>',
);

