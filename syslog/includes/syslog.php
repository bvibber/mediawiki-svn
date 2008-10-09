<?php
/**
 * A class to send BSD syslog messages to a remote host.
 *
 * @see http://tools.ietf.org/html/rfc3164
 * @author Ashar Voultoiz <hashar _at_ free _dot_ fr>
 *
 */

class mw_syslog {
	/**
	 * Used to get/set the severity:
	 *   0    Emergency: system is unusable
	 *   1    Alert: action must be taken immediately
	 *   2    Critical: critical conditions
	 *   3    Error: error conditions
	 *   4    Warning: warning conditions
	 *   5    Notice: normal but significant condition
	 *   6    Informational: informational messages
	 *   7    Debug: debug-level messages
	*/

	/** Internal class default options */
	private $allowed_options = array(
		'facility' => 1, # user-level
		'severity' => 5, # LOG_NOTICE
		'server'   => 'localhost',
		'port'     => '514',
		'timeout'  => '0',
		'prefix'   => null,
	);
	private $options = array();

	/** An UDP socket */
	private $_socket = null;

### PUBLIC METHODS ###
	/**
	 * Create a syslog object with initial options.
	 * @param array $initial_options An array of options to initialize the object with.
	*/
	public function __construct( $initial_options = null ) {
		$this->options = $this->build_options_with( $this->allowed_options, $initial_options );
	}

	/**
	 * Send a message to the remote syslog.
	 * You can temporarly override the initial options.
	 */
	public function send($msg, $options = null ) {
		$this->reallySend($msg, $this->with_options( $options ) );
	}

### STATIC METHODS ###
	/**
	 * Given a unix timestamp format the date according to RFC 3164 (syslog)
	 * A date looks like 'Dec  8 20:39:14', with a space padded day number.
	 *
	 * @param mixed $an_unix_timestamp Unix epoch timestamp
	 * @return Date formatted according to RFC 3164
	 */
	public static function rfc3164_timestamp_of( $an_unix_timestamp ) {
		# Per RFC syslog message MUST use localtime
		$time = time();

		# Pad day with a space:
		$padded_day = str_pad(date('j', $time ), 2, ' ', STR_PAD_LEFT);

		$timestamp = date( 'M ' . $padded_day. ' H:i:s', $time );

		return $timestamp;
	}


### PRIVATE METHODS ###
	/**
	 * Merge two array of options but only retains allowed one.
	 *
	 * @param array $base_opt Default options.
	 * @param array $new_opt Options we want to override.
	**/
	private static function build_options_with( $base_opt, $new_opt = null) {
		if( is_null( $new_opt ) ) {
			return $base_opt;
		}
	
		$build_up = array();

		foreach( $this->allowed_options as $name => $default_value ) {
			if( isset($new_opt[$name]) ) {
				$build_up[$name] = $new_opt[$name];
			} else {
				$build_up[$name] = $default_value;
			}
		}
		return $build_up;
	}

	/**
	 * Compute the priority based on facility and severity.
	 * The priority is a simple arithmetic operation but this function also
	 * sanitize the parameters according to RFC 3164 :
	 *
	 *   Facility is >= 0 and <= 23
	 *   Severity is >= 0 and <= 7
     *
	 * @param Integer $facility
	 * @param $severity
	 * @return priority
	*/
	private static function priority_of( $facility, $severity ) {

		$facility = ($facility <  0) ?  0 : $facility;
		$facility = ($facility > 23) ? 23 : $facility;

		$severity = ($severity <  0) ?  0 : $severity;
		$severity = ($severity >  7) ?  7 : $severity;

		return (int) ($facility * 8 + $severity);
	}

	private function with_options( $given_options ) {
		return self::build_options_with( $this->options, $given_options );
	}


	/**
	 * Build a packet using the RECOMMENDED RFC 3164 format.
	 * A packet is simply made of three parts: PRI, HEADER and MSG. For more
	 * details, have a look at http://tools.ietf.org/html/rfc3164 .
	 *
	 * @param integer $facility 0-23
	 * @param integer $severity 0-7
	 * @param integer $unix_ts An UNIX timestamp, will be represented in localtime
	 * @param string $hostname SHOULD be a hostname without FQDN but MAY be IPv4 or IPv6
	 * @param string $msg Freeform message. Please avoid stranges chars such as \\r ;)
	 *
	 * @return string The packet !
	 */
	private static function build_packet(
			$facility,
			$severity,
			$unix_ts,
			$hostname,
			$msg
		) {


		####################################################
		#     PRI, § 4.1.1
		####################################################
		// MUST be 3 to 5 chars, enclosed by angle brackets
		$priority = self::priority_of( $facility, $severity );
		$PRI = "<$priority>";


		####################################################
		#     HEADER, § 4.1.2
		####################################################
		$timestamp = self::rfc3164_timestamp_of( $unix_ts );
		// FIXME hostname MUST be without fqdn!
		$hostname  = $hostname;
		$HEADER = "$timestamp $hostname "; // fields MUST be terminated with a space


		####################################################
		#     MSG, § 4.1.3
		####################################################
		$tag = 'MediaWiki'; // MUST NOT exceed 32 alpha numerics characters
		$content = $msg;
 		# TAG and CONTENT are separated by a non alpha numeric char usually
		# followed by a space.
		# Also see § 5.3 if you want to add PID. Prefix should be in content
		$MSG = "$tag: $content";

		// Send back the build packet
		return $PRI.$HEADER.$MSG ;
		
	}

	/**
	 * @Param string $server
	 * @Param integer $port
	 * @Param integer $timeout
	 */
	private function openSocket( $server, $port, $timeout ) {
		$_erno = $_erstr = 0;
		$this->_socket = fsockopen( "udp://$server", $port, $_erno, $_erstr, $timeout );
		if( !$this->_socket ) {
			die("failed to open socket : $_erno, $_erstr\n"); # FIXME
		}
		return (true === $this->_socket) ;
	}

	/** Close the socket */
	private function closeSocket() {
		if( $this->_socket ) {
			fclose( $this->_socket );
			$this->_socket = null;
		}
	}

	/**
	 * @param string $msg
	 * @param $opt Our syslog options.
	 */
	private function reallySend( $msg, $opt ) {
		$packet = self::build_packet(
			$opt['facility'],
			$opt['severity'],
			time(),
			wfHostname(), # FIXME is this always correct AND cached ?
			$msg
		);

		# Open socket, send message and close it. If you ever change it to use
		# a persistent file handle, beware !! server and port can be overriden
		# on a per message basis.
		$this->openSocket( $opt['server'], $opt['port'], $opt['timeout'] );
		fwrite( $this->_socket, $packet ); 
		$this->closeSocket();
	}
}
