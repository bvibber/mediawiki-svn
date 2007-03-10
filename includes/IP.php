<?php
/*
 * Collection of public static functions to play with IP address
 * and IP blocks.
 *
 * @Author "Ashar Voultoiz" <hashar@altern.org>
 * @License GPL v2 or later
 */

// Some regex definition to "play" with IP address and IP address blocks

// An IP is made of 4 bytes from x00 to xFF which is d0 to d255
define( 'RE_IP_BYTE', '(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|0?[0-9]?[0-9])');
define( 'RE_IP_ADD' , RE_IP_BYTE . '\.' . RE_IP_BYTE . '\.' . RE_IP_BYTE . '\.' . RE_IP_BYTE );
// An IP block is an IP address and a prefix (d1 to d32)
define( 'RE_IP_PREFIX', '(3[0-2]|[12]?\d)');
define( 'RE_IP_BLOCK', RE_IP_ADD . '\/' . RE_IP_PREFIX);
// For IPv6 canonicalization (NOT for strict validation; these are quite lax!)
define( 'RE_IPV6_WORD', '([0-9A-Fa-f]{1,4})' );
define( 'RE_IPV6_GAP', ':(?:0+:)*(?::(?:0+:)*)?' );
define( 'RE_IPV6_V4_PREFIX', '0*' . RE_IPV6_GAP . '(?:ffff:)?' );

class IP {

	/**
	 * Given an IP address in dotted-quad notation, returns an IPv6 octed.
	 * See http://www.answers.com/topic/ipv4-compatible-address
	 * IPs with the first 92 bits as zeros are reserved from IPv6
	 * @param $ip quad-dotted IP address.
	 */
	function IPv4toIPv6( $ip ) {
		if ( !$ip ) return null;
		// Convert only if needed
		if ( strpos($ip,':') !==false ) return $ip;
		$ip_int = IP::toUnsigned( $ip );
		return IP::toOctet( $ip_int );
	}

	/**
	 * Given an IPv6 address in octet notation, returns an unsigned integer.
	 * @param $ip octet ipv6 IP address.
	 */
	function toUnsigned6( $ip )
   	{
    	$ip = IP::expandIPv6( $ip );

       	$ip = explode(':', $ip);
       	$r_ip = '';
       	foreach ($ip as $v) {
			$r_ip .= str_pad(base_convert($v, 16, 2), 16, 0, STR_PAD_LEFT);
        }
        $r_ip = IP::base_convert($r_ip, 2, 10);
       	return $r_ip;
	}
	
	/**
	 * Given an IPv6 address in octet notation, returns the expanded octed.
	 * @param $ip octet ipv6 IP address.
	 */	
	function expandIPv6( $ip ) {
   		// Expand zero abbreviations
		if (substr_count($ip, '::')) {
    		$ip = str_replace('::', str_repeat(':0000', 8 - substr_count($ip, ':')) . ':', $ip);
    	}
    	return "$ip";
	}
	
	/**
	 * Given an unsigned integer, returns an IPv6 address in dotted-quad notation
	 * Comes from ProxyTools.php
	 * @param $ip integer ipv6 IP address.
	 */
	function toOctet( $ip_int ) {
		$ip_int = strval( $ip_int );
   		// Convert integer to binary
   		$ip = str_pad(IP::base_convert($ip_int, 10, 2), 128, 0, STR_PAD_LEFT);
   		// Seperate into 8 octets
   		$ip_oct = base_convert( substr( $ip, 0, 16 ), 2, 16 );
   		for ($n=1; $n < 8; $n++) {
   			// Convert to hex, and add ":" marks
   			$ip_oct .= ':' . base_convert( substr($ip, 16*$n, 16), 2, 16 );
   		}
       	return $ip_oct;
	}

	/**
	 * Convert a network specification in CIDR notation to an integer network and a number of bits
	 */
	public static function parseCIDR6( $range ) {
		$parts = explode( '/', $range, 2 );
		if ( count( $parts ) != 2 ) {
			return array( false, false );
		}
		$network = IP::toUnsigned6( $parts[0] );
		if ( $network !== false && is_numeric( $parts[1] ) && $parts[1] >= 0 && $parts[1] <= 128 ) {
			$bits = $parts[1];
			if ( $bits == 0 ) {
				$network = 0;
			} else {
			# Truncate the last (128-$bits) bits, turn them into zeros
			# Native 32 bit functions WONT work here!!!
				$network = IP::base_convert( $network, 10, 2 );
				$network = str_pad( substr( $network, 0, (128 - $bits) ), 128, 0, STR_PAD_RIGHT );
				$network = IP::base_convert( $network, 2, 10 );
			}
		} else {
			$network = false;
			$bits = false;
		}
		
		return array( $network, $bits );
	}
	
	/**
	 * Given a string range in a number of formats, return the start and end of 
	 * the range in hexadecimal.
	 *
	 * Formats are:
	 *     2001:0db8:85a3::7344/96/70          CIDR
	 *     2001:0db8:85a3::7344/96/70 - 2001:0db8:85a3::7344/96/70   Explicit range
	 *     2001:0db8:85a3::7344/96/70             Single IP
	 */
	public static function parseRange6( $range ) {
		if ( strpos( $range, '/' ) !== false ) {
			# CIDR
			list( $network, $bits ) = IP::parseCIDR6( $range );
			if ( $network === false ) {
				$start = $end = false;
			} else {
				$start = sprintf( '%08X', $network );
				$end = sprintf( '%08X', $network + pow( 2, (128 - $bits) ) - 1 );
			}
		} elseif ( strpos( $range, '-' ) !== false ) {
			# Explicit range
			list( $start, $end ) = array_map( 'trim', explode( '-', $range, 2 ) );
			if ( $start > $end ) {
				$start = $end = false;
			} else {
				$start = IP::toHex6( $start );
				$end = IP::toHex6( $end );
			}
		} else {
			# Single IP
			$start = $end = IP::toHex6( $range );
		}
		if ( $start === false || $end === false ) {
			return array( false, false );
		} else {				
			return array( $start, $end );
		}
    }
	
	/**
	 * Covert a number from a base to another
	 * This is more expensive but handles very large numbers
	 */
	function base_convert($numstring, $frombase, $tobase) {
		$chars = "0123456789abcdefghijklmnopqrstuvwxyz";
   		$tostring = substr($chars, 0, $tobase);

   		$length = strlen($numstring);
   		$result = '';
   		for ($i = 0; $i < $length; $i++) {
   		    $number[$i] = strpos($chars, $numstring{$i});
   		}
   		do {
   		    $divide = 0;
   		    $newlen = 0;
   		    for ($i = 0; $i < $length; $i++) {
        		   $divide = $divide * $frombase + $number[$i];
         		  if ($divide >= $tobase) {
         		      $number[$newlen++] = (int)($divide / $tobase);
        		       $divide = $divide % $tobase;
       		    } elseif ($newlen > 0) {
               $number[$newlen++] = 0;
        		   }
      		 }
      		 $length = $newlen;
      		 $result = $tostring{$divide} . $result;
  		 }
  		 while ($newlen != 0);
  		 return strval($result);
	}
	
	/**
	 * Validate an IP address.
	 * @return boolean True if it is valid.
	 */
	public static function isValid( $ip ) {
		return preg_match( '/^' . RE_IP_ADD . '$/', $ip) ;
	}

	/**
	 * Validate an IP Block.
	 * @return boolean True if it is valid.
	 */
	public static function isValidBlock( $ipblock ) {
		return ( count(self::toArray($ipblock)) == 1 + 5 );
	}

	/**
	 * Determine if an IP address really is an IP address, and if it is public,
	 * i.e. not RFC 1918 or similar
	 * Comes from ProxyTools.php
	 */
	public static function isPublic( $ip ) {
		$n = IP::toUnsigned( $ip );
		if ( !$n ) {
			return false;
		}

		// ip2long accepts incomplete addresses, as well as some addresses
		// followed by garbage characters. Check that it's really valid.
		if( $ip != long2ip( $n ) ) {
			return false;
		}

		static $privateRanges = false;
		if ( !$privateRanges ) {
			$privateRanges = array(
				array( '10.0.0.0',    '10.255.255.255' ),   # RFC 1918 (private)
				array( '172.16.0.0',  '172.31.255.255' ),   #     "
				array( '192.168.0.0', '192.168.255.255' ),  #     "
				array( '0.0.0.0',     '0.255.255.255' ),    # this network
				array( '127.0.0.0',   '127.255.255.255' ),  # loopback
			);
		}

		foreach ( $privateRanges as $r ) {
			$start = IP::toUnsigned( $r[0] );
			$end = IP::toUnsigned( $r[1] );
			if ( $n >= $start && $n <= $end ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Split out an IP block as an array of 4 bytes and a mask,
	 * return false if it can't be determined
	 *
	 * @parameter $ip string A quad dotted IP address
	 * @return array
	 */
	public static function toArray( $ipblock ) {
		$matches = array();
		if(! preg_match( '/^' . RE_IP_ADD . '(?:\/(?:'.RE_IP_PREFIX.'))?' . '$/', $ipblock, $matches ) ) {
			return false;
		} else {
			return $matches;
		}
	}

	/**
	 * Return a zero-padded hexadecimal representation of an IP address.
	 *
	 * Hexadecimal addresses are used because they can easily be extended to
	 * IPv6 support. To separate the ranges, the return value from this 
	 * function for an IPv6 address will be prefixed with "v6-", a non-
	 * hexadecimal string which sorts after the IPv4 addresses.
	 *
	 * @param $ip Quad dotted IP address.
	 */
	public static function toHex( $ip ) {
		$n = self::toUnsigned( $ip );
		if ( $n !== false ) {
			$n = sprintf( '%08X', $n );
		}
		return $n;
	}
	
	// For IPv6
	public static function toHex6( $ip ) {
		$n = self::toUnsigned6( $ip );
		if ( $n !== false ) {
			$n = sprintf( '%08X', $n );
		}
		return $n;
	}

	/**
	 * Given an IP address in dotted-quad notation, returns an unsigned integer.
	 * Like ip2long() except that it actually works and has a consistent error return value.
	 * Comes from ProxyTools.php
	 * @param $ip Quad dotted IP address.
	 */
	public static function toUnsigned( $ip ) {
		if ( $ip == '255.255.255.255' ) {
			$n = -1;
		} else {
			$n = ip2long( $ip );
			if ( $n == -1 || $n === false ) { # Return value on error depends on PHP version
				$n = false;
			}
		}
		if ( $n < 0 ) {
			$n += pow( 2, 32 );
		}
		return $n;
	}

	/**
	 * Convert a dotted-quad IP to a signed integer
	 * Returns false on failure
	 */
	public static function toSigned( $ip ) {
		if ( $ip == '255.255.255.255' ) {
			$n = -1;
		} else {
			$n = ip2long( $ip );
			if ( $n == -1 ) {
				$n = false;
			}
		}
		return $n;
	}

	/**
	 * Convert a network specification in CIDR notation to an integer network and a number of bits
	 */
	public static function parseCIDR( $range ) {
		$parts = explode( '/', $range, 2 );
		if ( count( $parts ) != 2 ) {
			return array( false, false );
		}
		$network = IP::toSigned( $parts[0] );
		if ( $network !== false && is_numeric( $parts[1] ) && $parts[1] >= 0 && $parts[1] <= 32 ) {
			$bits = $parts[1];
			if ( $bits == 0 ) {
				$network = 0;
			} else {
				$network &= ~((1 << (32 - $bits)) - 1);
			}
			# Convert to unsigned
			if ( $network < 0 ) {
				$network += pow( 2, 32 );
			}
		} else {
			$network = false;
			$bits = false;
		}
		return array( $network, $bits );
	}

	/**
	 * Given a string range in a number of formats, return the start and end of 
	 * the range in hexadecimal.
	 *
	 * Formats are:
	 *     1.2.3.4/24          CIDR
	 *     1.2.3.4 - 1.2.3.5   Explicit range
	 *     1.2.3.4             Single IP
	 */
	public static function parseRange( $range ) {
		if ( strpos( $range, '/' ) !== false ) {
			# CIDR
			list( $network, $bits ) = IP::parseCIDR( $range );
			if ( $network === false ) {
				$start = $end = false;
			} else {
				$start = sprintf( '%08X', $network );
				$end = sprintf( '%08X', $network + pow( 2, (32 - $bits) ) - 1 );
			}
		} elseif ( strpos( $range, '-' ) !== false ) {
			# Explicit range
			list( $start, $end ) = array_map( 'trim', explode( '-', $range, 2 ) );
			if ( $start > $end ) {
				$start = $end = false;
			} else {
				$start = IP::toHex( $start );
				$end = IP::toHex( $end );
			}
		} else {
			# Single IP
			$start = $end = IP::toHex( $range );
		}
		if ( $start === false || $end === false ) {
			return array( false, false );
		} else {				
			return array( $start, $end );
		}
    }

    /**
     * Determine if a given integer IPv4 address is in a given CIDR network
     * @param $addr The address to check against the given range.
     * @param $range The range to check the given address against.
     * @return bool Whether or not the given address is in the given range.
     */
    public static function isInRange( $addr, $range ) {
        $unsignedIP = IP::toUnsigned($addr);
        list( $start, $end ) = IP::parseRange($range);

		$start = hexdec($start);
		$end   = hexdec($end);

        return (($unsignedIP >= $start) && ($unsignedIP <= $end));
    }

    /**
     * Convert some unusual representations of IPv4 addresses to their
     * canonical dotted quad representation.
     *
     * This currently only checks a few IPV4-to-IPv6 related cases.  More
     * unusual representations may be added later.
     *
     * @param $addr something that might be an IP address
     * @return valid dotted quad IPv4 address or null
     */
    public static function canonicalize( $addr ) {
	if ( IP::isValid( $addr ) )
	    return $addr;

	// IPv6 loopback address
	$m = array();
	if ( preg_match( '/^0*' . RE_IPV6_GAP . '1$/', $addr, $m ) )
	    return '127.0.0.1';

	// IPv4-mapped and IPv4-compatible IPv6 addresses
	if ( preg_match( '/^' . RE_IPV6_V4_PREFIX . '(' . RE_IP_ADD . ')$/i', $addr, $m ) )
	    return $m[1];
	if ( preg_match( '/^' . RE_IPV6_V4_PREFIX . RE_IPV6_WORD . ':' . RE_IPV6_WORD . '$/i', $addr, $m ) )
	    return long2ip( ( hexdec( $m[1] ) << 16 ) + hexdec( $m[2] ) );

	return null;  // give up
    }
}

?>
