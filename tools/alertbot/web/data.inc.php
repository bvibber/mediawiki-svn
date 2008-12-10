<?PHP
//never use duplicate array keys through the arrays!
$acceptable_projects=array(	"wp"=>"Wikipedia","com"=>"Wikimedia Commons","ws"=>"Wikisource",
							"wq"=>"Wikiquote","wn"=>"Wikinews","wb"=>"Wikibooks",
							"ow"=>"Other Wikimedia project (Wikimania wikis, Metawiki, Incubator)",
							"bugzilla"=>"Wikimedia bugtracker",
							"OT" => "Some site NOT operated by the WikiMedia foundation" );
$acceptable_problems=array(	"sitedown"=>"Site totally down","slow"=>"Site abormally slow",
							"replag"=>"Excessive replication lag", "parts"=>"Parts of site do not work (e.g. thumbs broken, but reading works), please specify",
							"other"=>"Other, please specify" );
$acceptable_levels=array(	"justme"=>"Just me","dontknow"=>"I don't know",
							"manyother"=>"Many other people (e.g. you got reports on IRC, but not everyone has the problem)",
							"everyone"=>"everyone" );
$acceptable_languages=array(	"de" );
$level_correspond=array(	"justme"=>"low","dontknow"=>"low", "manyother"=>"high","everyone"=>"urgent" );
$problem_correspond=array(	"sitedown"=>"Site totally down","slow"=>"Site abormally slow",
							"replag"=>"Excessive replication lag", "parts"=>"Parts of site do not work:",
							"other"=>"" );
$states=array(				0=>"fresh",1=>"acknowledged",2=>"sms sent",3=>"fixed",4=>"spam" );
?>