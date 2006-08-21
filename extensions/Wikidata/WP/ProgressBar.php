<?php

global 
	$beginTime;

function durationToString($seconds) {
	$hours = floor($seconds / 3600);
	$seconds -= $hours * 3600;
	$minutes = floor($seconds / 60);
	$seconds -= $minutes * 60;
	
	return str_pad($hours, 2, "0", STR_PAD_LEFT) .":". str_pad($minutes, 2, "0", STR_PAD_LEFT) .":". str_pad($seconds, 2, "0", STR_PAD_LEFT);
}

function progressBar($current, $maximum) {
	global	
		$beginTime;
		
	$timeElapsed = time() - $beginTime;
	$barWidth = 45;

	if ($maximum > 0) {
		$percentage = round(100 * $current / $maximum);
		$barFull = round($barWidth * $current / $maximum);
	}
	else {
		$percentage = 100;
		$barFull = $barWidth;
	}	
	
	echo "\r " . str_pad($percentage, 3, " ", STR_PAD_LEFT) . "% of $maximum [". str_repeat("=", $barFull) . str_repeat(" ", $barWidth - $barFull) .
			"] " . durationToString($timeElapsed);	
}

function clearProgressBar() {
	echo "\r" . str_repeat(" ", 79) . "\r";
}
	
?>
