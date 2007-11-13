<?PHP

require_once ( "mediawiki_converter.php" ) ;

function treat ( $s ) {
  $s = htmlentities ( $s ) ;
#  $s = "<pre>$s</pre>" ;
  $s = str_replace ( "\n" , "<br/>\n" , $s ) ;
  return $s ;
}

$lines = explode ( "\n" , str_replace ( "\r" , "" , file_get_contents ( "../../phase3/maintenance/parserTests.txt" ) ) ) ; # This path is for trunk

$tests = array () ;
$articles = array () ;
$data = array () ;
$cmds = array () ;
$command = '' ;
$d = '' ;
foreach ( $lines AS $l ) {
  if ( $command == '' ) {
    if ( $l == '' ) continue ;
    if ( substr ( $l , 0 , 1 ) == '#' ) continue ;
  }
  if ( substr ( $l , 0 , 2 ) == '!!' ) {
    $new_command = strtolower ( trim ( substr ( $l , 2 ) ) ) ;
    if ( $new_command == 'end' || $new_command == 'endarticle' ) $new_command = '' ; # Simplify
    
    if ( $new_command == '' ) {
      if ( $cmds[1] == 'article') {
        $articles[trim($data['article'])] = $d ;
      } else if ( $cmds[1] == 'test') {
        $t = '' ;
        $t->name = trim ( $data['test'] ) ;
        $t->input = $data['input'] ;
        if ( isset ( $data['options'] ) ) $t->options = $data['options'] ;
        else $t->options = '' ;
        $t->result = $d ;
        $tests[] = $t ;
      }
      $data = array () ;
      $cmds = array () ;
    } else {
      $data[$command] = $d ;
      $cmds[] = $command ;
    }
    $d = '' ;
    $command = $new_command ;
  } else {
    if ( $d != '' ) $d .= "\n" ;
    $d .= $l ;
  }
}

# Run tests
$xmlg["useapi"] = false ;
$xmlg["book_title"] = 'Title';
$xmlg["site_base_url"] = 'en.wikipedia.org/w' ;
$xmlg["resolvetemplates"] = 'all' ;
$xmlg['templates'] = array () ;
$xmlg['add_gfdl'] = false ;
$xmlg['keep_interlanguage'] = true ;
$xmlg['keep_categories'] = true ;

$xmlg['xhtml_justify'] = false ;
$xmlg['xhtml_logical_markup'] = false ;
$xmlg['xhtml_source'] = false ;


$cnt = 1 ;
print "<table border=1 width='100%'><tr><th>Test</th><th>Result</th><th>wiki2xml</th><th>Input</th></tr>" ;
foreach ( $tests AS $t ) {
  $res = $t->result ;
  $col = '' ;
  if ( $cnt > 10 ) $nr = '' ;
  else {
    $converter = new MediaWikiConverter ;
    $xml = $converter->article2xml ( "" , $t->input , $xmlg ) ;
    $nr = $converter->articles2xhtml ( $xml , $xmlg ) ;
    $nr = array_pop ( explode ( '<body>' , $nr , 2 ) ) ;
    $nr = array_shift ( explode ( '</body>' , $nr , 2 ) ) ;
    
    # Fixing things to compare to the stupid parser test formatting
    $res = trim ( $res ) ;
    $res = str_replace ( " \n" , "\n" , $res ) ;
    
    $arr = array ( 'li','p','dd' ) ;
    foreach ( $arr AS $a ) $nr = str_replace ( "</$a>" , "\n</$a>" , $nr ) ;
    
    $arr = array ( 'li' ) ;
    foreach ( $arr AS $a ) $nr = str_replace ( "<$a>" , "<$a> " , $nr ) ;


    # Indicator color
    $col = 'red' ;
    if ( $res == $nr ) $col = 'green' ;
#    $nr = str_replace ( '</' , "\n</" , $xml ) ;
  }

  
  
  print "<tr><th bgcolor='$col'>" . treat ( $t->name ) . "</th>" ;
  print "<td>" . treat ( $res ) . "</td>" ;
  print "<td>" . treat ( $nr ) . "</td>" ;
  print "<td>" . treat ( $t->input ) . "</td>" ;

  $cnt++ ;
  if ( $cnt > 10 ) break ;
}
print "</table>" ;

?>