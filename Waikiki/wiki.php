<?

function getGet ( $p )
	{
   $x = $_GET[$p] ;
   if ( $x != "" ) $x = " -{$p}={$x}" ;
   return $x ;
   }

$action = $_GET['action'] ;
$title = $_GET['title'] ;

$redirect = getGet ( "redirect" ) ;

if ( $action == "" ) $action = "view" ;
if ( $title == "" ) $title = "B" ;

if ( $_GET['go'] == "Go" ) $action="go" ;
#if ( isset ( $_GET['search'] ) ) $action="search" ;

$wd = "C:\\Programme\\Dev-Cpp\\Mine\\Waikiki" ;
$prg = "waikiki.exe" ;
$db = '-sqlite="test.sqlite"' ;

if ( $action == "view" )
	{
	$param = $db . ' -title="' . $title . '"' . $redirect ;
   }
else if ( $action == "go" || $action == "search" )
	{
   $title = $_GET['search'] ;
	$param = $db . ' -action=' . $action . ' -title="' . $title ;
   }


$exe = "{$prg} {$param}" ;
chdir ( $wd ) ;
print system ( $exe ) ;

?>