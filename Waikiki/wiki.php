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

$wd = "C:\\Programme\\Dev-Cpp\\Mine\\Waikiki" ;
$prg = "waikiki.exe" ;
$param = '-sqlite="test.sqlite" -title="' . $title . '"' . $redirect ;


$exe = "{$prg} {$param}" ;
chdir ( $wd ) ;
print system ( $exe ) ;

?>