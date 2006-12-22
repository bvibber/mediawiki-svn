<?php

function fundcore_format_text($current)
{
	return '$' . number_format($current, 2);
}

function fundcore_render_meter($current)
{
	$meter_config =& wm_config('meter');

	// Load matching contribution info
	$dates_split = explode(',', $meter_config['matching_dates']);
	$matching = 0;
	foreach($dates_split as $date_split)
	{
		//echo $date_split . '<br />';
		$split = explode(':', $date_split);
		$match = wm_get_total($split[0], $split[1]);
		//echo $match . '<br />';
		if ($match > $split[2])
			$match = $split[2];
		$matching += $match; 
	}
	//die('Matching:' . $matching);

	// Cap current at maximum
	$real_current = $current;
	if ($current > $meter_config['max'])
		$current = $meter_config['max'];

	// Cap matching at maximum
	$real_matching = $matching;
	if ($matching + $current > $meter_config['max'])
		$matching = $meter_config['max'] - $current;
	
	// Establish image attributes
	$image		= imagecreatetruecolor($meter_config['width'], $meter_config['height']);
	$color_bg	= ImageColorAllocate($image, 255, 255, 255);
	$color_fg   = ImageColorAllocate($image, $meter_config['bar_r'], $meter_config['bar_g'], $meter_config['bar_b']);
	$color_match   = ImageColorAllocate($image, $meter_config['bar_r'] + 50, $meter_config['bar_g'] + 50, $meter_config['bar_b'] + 50);
	$color_text = ImageColorAllocate($image, 0, 0, 0);
	$color_tick_left = ImageColorAllocate($image, 71, 173, 122);
	$color_tick_right = ImageColorAllocate($image, 220, 220, 220);
	$bar_width  = ($meter_config['width'] * $current) / $meter_config['max'];
	$matching_bar_width  = ($meter_config['width'] * ($current + $matching)) / $meter_config['max'];
	
	// Create while background
	ImageFilledRectangle($image, 0, 0, $meter_config['width'], $meter_config['height'], $color_bg);
	
	// Draw matching bar
	ImageFilledRectangle($image, 0, 0, $matching_bar_width, $meter_config['height'], $color_match);

	// Draw bar
	ImageFilledRectangle($image, 0, 0, $bar_width, $meter_config['height'], $color_fg);

	// Outline the meter
	ImageRectangle($image, 0, 0, $meter_config['width'] - 1, $meter_config['height'] - 1, $color_fg);
	
	// Add $100,000 ticks
	$big_tick_width = ($meter_config['width'] / $meter_config['max']) * 100000;
	for($x = 0; $x < $meter_config['width']; $x += $big_tick_width)
	{
		$color_tick = $color_tick_right;
		if ($x < $matching_bar_width)
			$color_tick = $color_tick_left;
		imageline($image, $x, 1, $x,  $meter_config['height'] - 2, $color_tick);
	}

	// Add $50,000 ticks
	$big_tick_width = ($meter_config['width'] / $meter_config['max']) * 50000;
	for($x = 0; $x < $meter_config['width']; $x += $big_tick_width)
	{
		$color_tick = $color_tick_right;
		if ($x < $matching_bar_width)
			$color_tick = $color_tick_left;
		imageline($image, $x, 5, $x,  $meter_config['height'] - 6, $color_tick);
	}
	
	// Add the text
	$text =  wm_format_text($real_current + $real_matching);
	
	$attr = imagettfbbox($meter_config['font_size'], 0, $meter_config['font_file'], $text);
	$text_height = $attr[3] - $attr[7];
	$text_width = $attr[2] - $attr[6];
	
	$text_left = $matching_bar_width + 3;
	$text_color = $color_text;
	if ($text_left + $text_width >= $meter_config['width'])
	{
		$text_left = $bar_width - $text_width - 3;
		$text_color = $color_bg;
	}
	
	imagettftext($image, $meter_config['font_size'], 0, $text_left, $text_height, $text_color, $meter_config['font_file'], $text);
	
	return $image;
}

$meter_config =& wm_config('meter');
$current = wm_get_total($meter_config['start_date']);

if (isset($_REQUEST['current']) && is_numeric($_REQUEST['current']))
	$current = $_REQUEST['current'];

$image =  wm_render_meter($current);
header("content-type: image/png");
header('content-disposition: inline; filename="meter.png"');
imagepng($image);
