<?php
/**
*Class for some IPTC functions.

*/
class IPTC {

        /**
        * This takes the results of iptcparse() and puts it into a
        * form that can be handled by mediawiki. Generally called from
        * BitmapMetadataHandler::doApp13.
        *
        * At the moment this is more of an outline, and is definitly
        * not complete.
        * @todo finish for other iptc values
        * @see http://www.iptc.org/std/IIM/4.1/specification/IIMV4.1.pdf
        *
        * @param String $data app13 block from jpeg containg iptc/iim data
        * @return Array iptc metadata array
        */
        static function parse( $rawData ) {
                // TODO: This is nowhere near complete yet.
                $parsed = iptcparse( $rawData );
                $data = Array();
                if (!is_array($parsed)) {
                        return $data;
                }

                $c = '?';
		//charset info contained in tag 1:90.
		if (isset($parsed['1#090']) && isset($parsed['1#090'][0])) {
			$c = self::getCharset($parsed['1#090'][0]);
		}

		foreach ( $parsed as $tag => $val ) {
			switch( $tag ) {
				case '2#120': /*IPTC caption*/
					$data['ImageDescription'] = self::convIPTC( $val, $c );
					break;
				case '2#116': /* copyright */
					$data['Copyright'] = self::convIPTC( $val, $c );
					break;
				case '2#080': /* byline */
					$data['Artist'] = self::convIPTC( $val, $c );
					break;
				/* there are many many more that should be done */

			}

		}
		return $data;
	}

	/**
	* Helper function to convert charset for iptc values.
	* @param $data Mixed String or Array: The iptc string
	* @param $charset String: The charset
	*/
	private static function convIPTC ( $data, $charset ) {
		global $wgLang;
		if ( is_array( $data ) ) {
			foreach ($data as &$val) {
				$val = self::convIPTCHelper( $val, $charset );
			}

			// for now. Probably should keep it as an array perhaps
			$data = $wgLang->commaList( $data );
		} else {
			$data = self::convIPTCHelper ( $data, $charset );
		}
		return $data;
	}
	/**
	* Helper function of a helper function to convert charset for iptc values.
	* @param $data Mixed String or Array: The iptc string
	* @param $charset String: The charset
	*/
	private static function convIPTCHelper ( $data, $charset ) {
		if ( $charset !== '?' ) {
			$data = iconv($charset, "UTF-8//IGNORE", $data);
			if ($data === false) {
				$data = "";
				wfDebug(__METHOD__ . " Error converting iptc data charset $charset to utf-8");
			}
		} else {
			//treat as utf-8 if is valid utf-8. otherwise pretend its iso-8859-1
			// most of the time if there is no 1:90 tag, it is either ascii, latin1, or utf-8
			$oldData = $data;
			UtfNormal::quickIsNFCVerify( $data ); //make $data valid utf-8
			if ($data === $oldData) return $data;
			else return convIPTCHelper ( $data, 'ISO-8859-1' ); //should this be windows-1252?
		}
		return $data;
	}

	/**
	* take the value of 1:90 tag and returns a charset
	* @param String $tag 1:90 tag. 
	* @return charset name or "?"
	* Warning, this function does not (and is not intended to) detect
	* all iso 2022 escape codes. In practise, the code for utf-8 is the
	* only code that seems to have wide use. It does detect that code.
	*/
	static function getCharset($tag) {

		//Acording to iim standard, charset is defined by the tag 1:90.
		//in which there are iso 2022 escape sequences to specify the character set.
		//the iim standard seems to encourage that all neccesary escape sequences are
		//in the 1:90 tag, but says it doesn't have to be.

		//This is in need of more testing probably. This is definitly not complete.
		//however reading the docs of some other iptc software, it appears that most iptc software
		//only recognizes utf-8. If 1:90 tag is not present content is
		// usually ascii or iso-8859-1 (and sometimes utf-8), but no garuntees.

		//This also won't work if there are more than one escape sequence in the 1:90 tag
		//or if something is put in the G2, or G3 charsets, etc. It will only reliably recognize utf-8.

		// This is just going through the charsets mentioned in appendix C of the iim standard.

		//  \x1b = ESC.
		switch ( $tag ) {
			case "\x1b%G": //utf-8
			//Also call things that are compatible with utf-8, utf-8 (e.g. ascii)
			case "\x1b(B": // ascii
			case "\x1b(@": // iso-646-IRV (ascii in latest version, $ different in older version)
				$c = 'UTF-8';
				break;
			case "\x1b(A": //like ascii, but british.
				$c = 'ISO646-GB';
				break;
			case "\x1b(C": //some obscure sweedish/finland encoding
				$c = 'ISO-IR-8-1';
				break;
			case "\x1b(D":
				$c = 'ISO-IR-8-2';
				break;
			case "\x1b(E": //some obscure danish/norway encoding
				$c = 'ISO-IR-9-1';
				break;
			case "\x1b(F":
				$c = 'ISO-IR-9-2';
				break;
			case "\x1b(G":
				$c = 'SEN_850200_B'; // aka iso 646-SE; ascii-like
				break;
			case "\x1b(I":
				$c = "ISO646-IT";
				break;
			case "\x1b(L":
				$c = "ISO646-PT";
				break;
			case "\x1b(Z":
				$c = "ISO646-ES";
				break;
			case "\x1b([":
				$c = "GREEK7-OLD";
				break;
			case "\x1b(K":
				$c = "ISO646-DE";
				break;
			case "\x1b(N":  //crylic
				$c = "ISO_5427";
				break;
			case "\x1b(`": //iso646-NO
				$c = "NS_4551-1";
				break;
			case "\x1b(f": //iso646-FR
				$c = "NF_Z_62-010"; 
				break;
			case "\x1b(g":
				$c = "PT2"; //iso646-PT2
				break;
			case "\x1b(h":
				$c = "ES2";
				break;
			case "\x1b(i": //iso646-HU
				$c = "MSZ_7795.3";
				break;
			case "\x1b(w":
				$c = "CSA_Z243.4-1985-1";
				break;
			case "\x1b(x":
				$c = "CSA_Z243.4-1985-2";
				break;
			case "\x1b$(B":
			case "\x1b$B":
			case "\x1b&@\x1b$B":
			case "\x1b&@\x1b$(B":
				$c = "JIS_C6226-1983";
				break;
			case "\x1b-A": // iso-8859-1. at least for the high code characters.
			case "\x1b(@\x1b-A":
			case "\x1b(B\x1b-A":
				$c = 'ISO-8859-1';
				break;
			case "\x1b-B": // iso-8859-2. at least for the high code characters.
				$c = 'ISO-8859-2';
				break;
			case "\x1b-C": // iso-8859-3. at least for the high code characters.
				$c = 'ISO-8859-3';
				break;
			case "\x1b-D": // iso-8859-4. at least for the high code characters.
				$c = 'ISO-8859-4';
				break;
			case "\x1b-E": // iso-8859-5. at least for the high code characters.
				$c = 'ISO-8859-5';
				break;
			case "\x1b-F": // iso-8859-6. at least for the high code characters.
				$c = 'ISO-8859-6';
				break;
			case "\x1b-G": // iso-8859-7. at least for the high code characters.
				$c = 'ISO-8859-7';
				break;
			case "\x1b-H": // iso-8859-8. at least for the high code characters.
				$c = 'ISO-8859-8';
				break;
			case "\x1b-I": // CSN_369103. at least for the high code characters.
				$c = 'CSN_369103';
				break;
			default:
				wfDebug(__METHOD__ . 'Unkown charset in iptc 1:90: ' . bin2hex( $tag ) ); 
				//at this point should we give up and refuse to parse iptc?
				$c = '?';
		}
		return $c;
	}
}
