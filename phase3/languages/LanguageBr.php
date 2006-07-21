<?php
/**
  * @package MediaWiki
  * @subpackage Language
  */

class LanguageBr extends Language {
	/**
	 * $format and $timecorrection are for compatibility with Language::date
	 */
	function date( $ts, $adj = false, $format = true, $timecorrection = false ) {
		if ( $adj ) { $ts = $this->userAdjust( $ts ); }

		$d = (0 + substr( $ts, 6, 2 )) . " " .
		  $this->getMonthAbbreviation( substr( $ts, 4, 2 ) ) .
		  " " . substr( $ts, 0, 4 );
		return $d;
	}

	/**
	 * $format and $timecorrection are for compatibility with Language::date
	 */
	function timeanddate( $ts, $adj = false, $format = true, $timecorrection = false ) {
		return $this->date( $ts, $adj ) . " da " . $this->time( $ts, $adj );
	}
}

?>
