<?php

global 
	$beginTime, $progressBarMaximum, $progressBarCurrent, $lastTimeDisplayed, $lastProgressDisplayed, $progressBarRefreshRate;

function durationToString($seconds) {
	$hours = floor($seconds / 3600);
	$seconds -= $hours * 3600;
	$minutes = floor($seconds / 60);
	$seconds -= $minutes * 60;
	
	return str_pad($hours, 2, "0", STR_PAD_LEFT) .":". str_pad($minutes, 2, "0", STR_PAD_LEFT) .":". str_pad($seconds, 2, "0", STR_PAD_LEFT);
}

function initializeProgressBar($maximum, $refreshRate) {
	global
		$progressBarMaximum, $progressBarCurrent, $lastProgressDisplayed, $progressBarRefreshRate;
		
	$progressBarMaximum = $maximum;
	$progressBarCurrent = 0;
	$lastProgressDisplayed = 0;
	$progressBarRefreshRate = $refreshRate;
	
	displayProgressBar();
}

function displayProgressBar() {
	global	
		$beginTime, $progressBarMaximum, $progressBarCurrent, $lastTimeDisplayed, $lastProgressDisplayed, $progressBarRefreshRate;
		
	if ($progressBarCurrent == 0 || $progressBarCurrent >= $lastProgressDisplayed + $progressBarRefreshRate) {
		$lastProgressDisplayed = $progressBarCurrent;
		$timeElapsed = time() - $beginTime;
		$barWidth = 45;
	
		if ($progressBarMaximum > 0) {
			$percentage = floor(100 * $progressBarCurrent / $progressBarMaximum);
			$barFull = floor($barWidth * $progressBarCurrent / $progressBarMaximum);
		}
		else {
			$percentage = 100;
			$barFull = $barWidth;
		}	
		
		echo "\r " . str_pad($percentage, 3, " ", STR_PAD_LEFT) . "% of $progressBarMaximum [". str_repeat("=", $barFull) . str_repeat(" ", $barWidth - $barFull) .
				"] " . durationToString($timeElapsed);
	}	
}

function advanceProgressBar($amount) {
	global	
		$progressBarCurrent;

	$progressBarCurrent += $amount;
	displayProgressBar();		
}

function setProgressBarPosition($position) {
	global	
		$progressBarCurrent;

	$progressBarCurrent = $position;
	displayProgressBar();		
}

function clearProgressBar() {
	echo "\r" . str_repeat(" ", 79) . "\r";
}
	
?>
