<?
require_once ( "geo_functions.php" ) ;

# geo paramater class
# this class is used for parameter storage, data access and caching, etc.
class geo_params
	{
	var $min_x = 1000000 ;
	var $max_x = -1000000 ;
	var $min_y = 1000000 ;
	var $max_y = -1000000 ;
	var $labels = array () ; # The text labels
	var $languages = array ( "en" ) ; # Default language
	var $styles = array ( "default" => "fill:#EEEEEE;" ) ; # All the styles
	var $starters = array () ; # The objects to start drawing with
	var $fits = array () ; # Which objects to fit into the viewport
	var $object_tree = array () ; # The current object(s) being rendered
	var $geo_cache = array () ; # The article cache
	
	function settings ( $sets )
		{
		$sets = explode ( "\n" , strtolower ( $sets ) ) ;
		foreach ( $sets AS $s )
			{
			$s = explode ( ":" , $s , 2 ) ;
			if ( count ( $s ) == 2 ) # key = value
				{
				$key = trim ( $s[0] ) ;
				$value = trim ( $s[1] ) ;
				if ( $key == "languages" )
					{
					$this->languages = explode ( ";" , str_replace ( "," , ";" , $value ) ) ; # "," and ";" are valid separators
					}
				else if ( $key == "style" )
					{
					$a = explode ( "=" , $value , 2 ) ;
					if ( count ( $a ) == 2 )
						{
						$b = explode ( ";" , str_replace ( "," , ";" , $a[0] ) ) ;
						foreach ( $b AS $c )
							{
							if ( isset ( $this->styles[$c] ) )
								$this->styles[$c] .= ";" . $a[1] ;
							else
								$this->styles[$c] = $a[1] ;
							}
						}
					}
				else if ( $key == "show" )
					{
					$a = explode ( ";" , str_replace ( "," , ";" , $value ) ) ;
					$this->starters = array_merge ( $this->starters , $a ) ;
					}
				else if ( $key == "fit" )
					{
					$a = explode ( ";" , str_replace ( "," , ";" , $value ) ) ;
					$this->fits = array_merge ( $this->fits , $a ) ;
					}
				}
			}
			
		# Cleanup
		foreach ( $this->starters AS $k => $v ) $this->starters[$k] = trim ( $v ) ;
		foreach ( $this->fits AS $k => $v ) $this->fits[$k] = trim ( $v ) ;
		}

	# The actual SVG collecting and "rendering"
	function getSVG ()
		{
		foreach ( $this->starters AS $s )
			{
			$g = new geo ;
			$g->set_from_id ( $s , $this ) ;
			$svg = $g->draw ( $this ) ;
			}
#		return "" ; # TESTING!
		$svg .= $this->get_svg_labels () ;

		# Finalizing
		$viewBox = $this->get_view_box () ;
		$svg = 
		'<?xml version="1.0" encoding="iso-8859-1" standalone="no"?>
		<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/SVG/DTD/svg10.dtd">
		<svg viewbox="' . $viewBox .
		'" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve">
			<g id="mainlayer">
		'
			. $svg .
			'</g>
		</svg>
		' ;

		return $svg ;
		}

	# This can read the data directly as an article from the database
	function read_from_article ( $id )
		{
		$t = Title::newFromText ( "Geo:" . $id ) ;
		$a = new Article ( $t ) ;
		return $a->getContent ( true ) ;
		}
	
	# This can read the data from a mediawiki installation via URL
	# *Much* slower than the function above
	function read_from_url ( $id )
		{
		$indedx = "http://127.0.0.1/phase3/index.php" ;
		$filename = "{$index}?title=Geo:{$id}&action=raw" ;
		$handle = fopen($filename, "r");
		$contents = '';
		while (!feof($handle))
			$contents .= fread($handle, 8192);
		fclose($handle);
		return $contents ;
		}
	
	# This reads the data and manages the cache
	function get_raw_text ( $id )
		{
		global $geo_cache ;
		
		if ( isset ( $geo_cache[$id] ) ) # Try the cache first...
			return $geo_cache[$id] ;
	
		if ( MEDIAWIKI )
			$contents = $this->read_from_article ( $id ) ;
		else
			$contents = $this->read_from_url ( $id ) ;
	
		# Return text
		$geo_cache[$id] = $contents ; # Cache the result
		return $contents ;
		}

	function match_object_style ( $object , $type )
		{
		$ret = array () ;
		if ( isset ( $this->styles["[{$type}]"] ) )
			$ret[] = $this->styles["[{$type}]"] ;
		if ( isset ( $this->styles["{$object}[{$type}]"] ) )
			$ret[] = $this->styles["{$object}[{$type}]"] ;
		return implode ( "; " , $ret ) ;
		}
		
	function get_styles ( $id , $type )
		{
		# Universal constants; rivers are blue!
		if ( $type == "river" )
			return "fill:none; stroke:blue; stroke-width:2" ;

		if ( isset ( $this->styles[$id] ) )
			return $this->styles[$id] ;
		
		$ret = $this->styles["default"] ; # Default, if not overwritten by anything more specific
		foreach ( $this->object_tree AS $object )
			{
			$o = explode ( ";" , $object ) ;
			$s = $this->match_object_style ( $o[0] , $o[1] ) ;
			if ( $s != "" ) $ret .= "; " . $s ;
			$s = $this->match_object_style ( $o[0] , $type ) ;
			if ( $s != "" ) $ret .= "; " . $s ;
			}

		return $ret ;
		}

	function data_to_real ( &$x , &$y )
		{
		$x = coordinate_to_number ( coordinate_take_apart ( $x ) ) ;
		$y = coordinate_to_number ( coordinate_take_apart ( $y ) ) ;
	
		$z = $x ; $x = $y ; $y = $z ; # Switching y/x to x/y
		$y = 90 * 3600 - $y ; # displaying from north to south

		# Recording min and max
		$this->min_x = min ( $this->min_x , $x ) ;
		$this->min_y = min ( $this->min_y , $y ) ;
		$this->max_x = max ( $this->max_x , $x ) ;
		$this->max_y = max ( $this->max_y , $y ) ;
		}

	function get_view_box ()
		{
		$min_x = $this->min_x ;
		$max_x = $this->max_x ;
		$min_y = $this->min_y ;
		$max_y = $this->max_y ;
		$width = $max_x - $min_x ;
		$height = $max_y - $min_y ;
		$min_x -= $width / 10 ;
		$min_y -= $height / 10 ;
		$max_x += $width / 10 ;
		$max_y += $height / 10 ;
		
		$max_x -= $min_x ;
		$max_y -= $min_y ;
		return "{$min_x} {$min_y} {$max_x} {$max_y}" ;
		}

	function add_label ( $text_array )
		{
		$this->labels[] = $text_array ;
		}

	function get_svg_labels ()
		{
		$ret = "" ;
		$medium_font_size = floor ( ( $this->max_x - $this->min_x ) / 50 ) ;
		foreach ( $this->labels AS $l )
			{
			$text = $l['text'] ;
			$x = $l['x'] ;
			$y = $l['y'] ;
			$s = "<text style='" ;
			$fs = $l['font-size'] ;
			if ( $fs == "medium" ) $fs = $medium_font_size ;
			if ( $fs == "" ) $fs = $medium_font_size * 8 / 10 ;
			
			$p = array() ;
			$p[] = "text-anchor:middle" ;
			$p[] = "fill-opacity:0.7" ;
			$p[] = "font-size:{$fs}pt" ;
			$s .= implode ( ";" , $p ) ;
			
			$s .= "' x='{$x}' y='{$y}'>{$text}</text>" ;
			if ( isset ( $l['href'] ) )
				{
				$href = $l['href'] ;
				$s = "<a xlink:href={$href}>{$s}</a>" ;
				}
			$ret .= $s."\n" ;
			}
		return $ret ;
		}
	}

# "geo" class
class geo
	{
	var $id ;
	var $data = array () ;
	var $xsum , $ysum , $count ;

	function geo_get_text ( $id , &$params )
		{
		$id = trim ( strtolower ( $id ) ) ;
		
		$parts = explode ( "#" , $id ) ;
		if ( count ( $parts ) == 2 )
			{
			$id = array_shift ( $parts ) ;
			$subid = array_shift ( $parts ) ;
			}
		else $subid = "" ;
		
		$ret = "\n" . $params->get_raw_text ( $id ) ;
		$ret = explode ( "\n==" , $ret ) ;
		
		if ( $subid == "" ) return $ret[0] ; # Default
		
		array_shift ( $ret ) ;
		foreach ( $ret AS $s )
			{
			$s = explode ( "\n" , $s , 2 ) ;
			$heading = array_shift ( $s ) ;
			$heading = strtolower ( trim ( str_replace ( "=" , "" , $heading ) ) ) ;
			if ( $heading == $subid ) return array_shift ( $s ) ;
			}
	#	print "Not found : {$id}#{$subid}\n" ;
		return "" ; # Query not found
		}

	function set_from_id ( $id , &$params )
		{
		$this->id = $id ;
		$t = explode ( "\n;" , "\n".$this->geo_get_text ( $id , $params ) ) ;
		$this->data = array () ;
		foreach ( $t AS $x )
			{
			$b = explode ( ":" , $x , 2 ) ;
			while ( count ( $b ) < 2 ) $b[] = "" ;
			$key = strtolower ( str_replace ( " " , "" , array_shift ( $b ) ) ) ;
			$key = str_replace ( "\n" , "" , $key ) ;
			$value = trim ( str_replace  ( "\n" , "" , array_shift ( $b ) ) ) ;
			$value = explode ( ";" , $value ) ;
			if ( $key != "" ) $this->data[$key] = $value ;
			}
		}

	function scan_raw_data ( &$data , &$ret , &$params )
		{
		$data = explode ( " " , $data ) ;
		foreach ( $data AS $a )
			{
			$a = explode ( "," , $a ) ;
			if ( count ( $a ) == 2 )
				{
				$x = trim ( array_shift ( $a ) ) ;
				$y = trim ( array_shift ( $a ) ) ;
				$params->data_to_real ( $x , $y ) ;
				$ret[] = array ( $x , $y ) ;
				}
			}
		}

	function get_data ( &$params )
		{
		$ret = array () ;
		if ( !isset ( $this->data["data"] ) ) return $ret ; # No data in this set
		$data = $this->data["data"] ;
		$data = array_shift ( $data ) ;
		
		$sets = explode ( ";" , $data ) ;
		foreach ( $sets AS $data )
			{
			$data = trim ( strtolower ( $data ) ) ;
			if ( substr ( $data , 0 , 9 ) == "reference" )
				{
				$data = trim ( substr ( $data , 9 ) ) ;
				$data = trim ( substr ( $data , 1 , strlen ( $data ) - 2 ) ) ;
				$data = explode ( "," , $data ) ;
				foreach ( $data AS $v )
					{
					$v = $this->fullid ( $v ) ;
					$ng = new geo ;
					$ng->set_from_id ( $v , $params ) ;
					$b = $ng->get_data ( $params ) ;
					$this->add_reordered_data ( $ret , $b ) ;
					}
				}
			else $this->scan_raw_data ( $data , $ret , $params ) ;
			}
		return $ret ;
		}

	function add_reordered_data ( &$original , &$toadd )
		{
		if ( count ( $toadd ) == 0 ) return ; # Nothing to add
		if ( count ( $original ) == 0 )
			{
			$original = $toadd ;
			return ;
			}
		
		$o_last = array_pop ( $original ) ; array_push ( $original , $o_last ) ; # Get last one and restore
		$t_last = array_pop ( $toadd ) ; array_push ( $toadd , $t_last ) ; # Get last one and restore
		$t_first = array_shift ( $toadd ) ; array_unshift ( $toadd , $t_first ) ; # Get first one and restore
		
		$dist_to_first =	( $o_last[0] - $t_first[0] ) * ( $o_last[0] - $t_first[0] ) +
								( $o_last[1] - $t_first[1] ) * ( $o_last[1] - $t_first[1] ) ;

		$dist_to_last =	( $o_last[0] - $t_last[0] ) * ( $o_last[0] - $t_last[0] ) +
								( $o_last[1] - $t_last[1] ) * ( $o_last[1] - $t_last[1] ) ;

		if ( $dist_to_last < $dist_to_first ) # If the last point of toadd is closer than the fist one,
			$toadd = array_reverse ( $toadd ) ; # add in other direction

		$original = array_merge ( $original , $toadd ) ;
		}

	function get_specs ( $base , $modes )
		{
		foreach ( $modes AS $x )
			{
			if ( isset ( $this->data["{$base}[{$x}]"] ) )
				return "{$base}[{$x}]" ;
			}
		if ( isset ( $this->data[$base] ) )
			return $base ;
		return "" ;
		}
		
	function get_current_type ( &$params ) # params may override native type
		{
		$t = $this->get_specs ( "type" , array ( "political" ) ) ;
		if ( $t != "" ) $t = $this->data[$t][0] ;
		return $t ;
		}

	function get_current_style ( &$params )
		{
		$t = trim ( strtolower ( $this->get_current_type ( $params ) ) ) ;
		$s = $params->get_styles ( $this->id , $t ) ;
		return "style=\"{$s}\"" ;
		}
	
	function fullid ( $id )
		{
		$id = trim ( strtolower ( $id ) ) ;
		if ( substr ( $id , 0 , 1 ) == "#" )
 			$id = $this->id . $id ;
		return $id ;
		}

	function draw_line ( $line , &$params )
		{
		$ret = "" ;
		$a = explode ( "(" , $line , 2 ) ;
		while ( count ( $a ) < 2 ) $a[] = "" ;
		$command = trim ( strtolower ( array_shift ( $a ) ) ) ;
		$values = trim ( str_replace ( ")" , "" , array_shift ( $a ) ) ) ;
		if ( $command == "addregs" || $command == "include" )
			{
			$values = explode ( "," , $values ) ;
			foreach ( $values AS $v )
				{
				$v = $this->fullid ( $v ) ;
				$ng = new geo ;
				$ng->set_from_id ( $v , $params ) ;
				$ret .= $ng->draw ( $params ) ;
				}
			}
		else if ( $command == "polygon" || $command == "polyline" )
			{
			$data = array () ;
			$values = explode ( "," , $values ) ;
			foreach ( $values AS $v )
				{
				$v = $this->fullid ( $v ) ;
				$ng = new geo ;
				$ng->set_from_id ( $v , $params ) ;
				$b = $ng->get_data ( $params ) ;
				$this->add_reordered_data ( $data , $b ) ;
				}

			$style = $this->get_current_style ( $params ) ;
			if ( $command == "polygon" ) $ret .= "<polygon {$style} points=\"" ;
			if ( $command == "polyline" ) $ret .= "<polyline {$style} points=\"" ;
			foreach ( $data AS $a )
				{
				$x = $a[0] ;
				$y = $a[1] ;
				$this->xsum += $x ;
				$this->ysum += $y ;
				$this->count++ ;
				$ret .= "{$x},{$y} " ;
				}
			$ret = trim ( $ret ) . "\"/>\n" ;
			
			}
		return $ret ;
		}

	function add_label ( $x , $y , &$params )
		{
		$text = $this->get_specs ( "name" , $params->languages ) ;
		if ( $text == "" ) return "" ; # No label found
		$text = utf8_decode ( $this->data[$text][0] ) ;
		if ( $text == "" ) return "" ; # No point in showing an empty label
		$x = floor ( $x ) ;
		$y = floor ( $y ) ;
		
		$a = array ( "text" => $text , "x" => $x , "y" => $y , "font-size" => "medium" ) ;
		$params->add_label ( $a ) ;
		}
	
	function draw ( &$params )
		{
		array_push ( $params->object_tree , $this->id.";" /*. $this->get_current_type ( $params )*/ ) ; # This needs tweaking
		$ret = "" ;
		$this->xsum = $this->ysum = $this->count = 0 ;
		$match = $this->get_specs ( "region" , array ( "political" ) ) ;
		if ( $match != "" )
			{
			$a = $this->data[$match] ;
			foreach ( $a AS $line )
				$ret .= $this->draw_line ( $line , $params ) ;
			}
		if ( $this->count > 0 )
			{
			$x = $this->xsum / $this->count ;
			$y = $this->ysum / $this->count ;
			$this->add_label ( $x , $y , $params ) ;
			}
		array_pop ( $params->object_tree ) ;
		return $ret ;
		}
	}

?>