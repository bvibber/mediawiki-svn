<?php
/** Chuvash (Чăвашла)
 *
 * @package MediaWiki
 * @subpackage Language
 */

# Chuvash stub localization; default to Russian instead of English.

# Cyrillic chars:   Ӑӑ Ӗӗ Ҫҫ Ӳӳ
# Latin substitute: Ăă Ĕĕ Çç Ÿÿ
# Where are latin substitute in this file because of font problems.

require_once( "LanguageRu.php" );

class LanguageCv extends LanguageRu {
	function date( $ts, $adj = false, $format = true, $timecorrection = false ) {
		if ( $adj ) { $ts = $this->userAdjust( $ts, $timecorrection ); }

		$datePreference = $this->dateFormat( $format );
		if( $datePreference == MW_DATE_DEFAULT ) {
			$datePreference = MW_DATE_YMD;
		}

		$month = $this->formatMonth( substr( $ts, 4, 2 ), $datePreference );
		$day = $this->formatDay( substr( $ts, 6, 2 ), $datePreference );
		$year = $this->formatNum( substr( $ts, 0, 4 ), true );

		switch( $datePreference ) {
			case MW_DATE_DMY: return "$day $month $year";
			case MW_DATE_YMD: return "$year, $month, $day";
			case MW_DATE_ISO: return substr($ts, 0, 4). '-' . substr($ts, 4, 2). '-' .substr($ts, 6, 2);
			default: return "$year, $month, $day";
		}
	}
}
?>
