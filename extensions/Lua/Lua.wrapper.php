<?php
/**
 * Lua parser extensions for MediaWiki - Wrapper classes
 *
 * @author Fran Rogers
 * @package MediaWiki
 * @addtogroup Extensions
 * @license See 'COPYING'
 * @file
 */

class LuaError extends Exception {
	public function __construct($msg, $parameter = ''){
		wfLoadExtensionMessages( 'Lua' );
		$this->message = '<strong class="error">' . wfMsgForContent( "lua_$msg", htmlspecialchars( $parameter ) ) . '</strong>';
	}
}

class LuaWrapper {
	protected $defunct;

	static function create() {
		global $wgLuaExternalInterpreter;
		return $wgLuaExternalInterpreter ? 
			new LuaWrapperExternal : 
			new LuaWrapper;
	}

	private $lua;

	protected function __construct() {
		global $wgLuaWrapperFile, $wgLuaMaxLines, $wgLuaMaxCalls;
		if (!class_exists('lua')) {
			$this->defunct = TRUE;
			throw new LuaError('extension_notfound');
		}
		$this->lua = new lua;
		try {
			$this->lua->evaluatefile($wgLuaWrapperFile);
			$this->lua->evaluate("sandbox = make_sandbox()");
			$this->lua->evaluate("function _die(reason) _G._DEAD = reason; end");
			$this->lua->evaluate("hook = make_hook($wgLuaMaxLines, $wgLuaMaxCalls, _die)");
			$this->defunct = FALSE;
		} catch (Exception $e) {
			$this->kill();
			throw new LuaError('error_internal');
		}
	}

	public function run($input) {
		if ($this->defunct)
			return '';

		$this->lua->input = $input;
		$this->lua->evaluate('chunk, err = loadstring(input)');
		if ($err = $this->lua->err) {
			$err = preg_replace('/^\[.+?\]:(.+?):/', '$1:', $err);
			throw new LuaError('error', $err);
		}
		$this->lua->res = $this->lua->err = NULL;

		$this->lua->evaluate('res, err = wrap(chunk, sandbox, hook)');

		if (($err = $this->lua->_DEAD) || ($err = $this->lua->err)) {
			if ($err == 'RECURSION_LIMIT') {
				$this->kill();
				throw new LuaError('overflow_recursion');
			} else if ($err == 'LOC_LIMIT') {
				$this->kill();
				throw new LuaError('overflow_loc');
			} else {
				$err = preg_replace('/^\[.+?\]:(.+?):/', '$1:', $err);
				throw new LuaError('error', $err);
			}
		}

		$this->lua->evaluate('_OUTPUT = sandbox._OUTPUT'); // ugh, please fix!
		$out = $this->lua->_OUTPUT;
		return (trim($out) != '') ? $out : '';
	}

	public function kill() {
		if ($this->defunct)
			return FALSE;
		$this->lua = NULL;
		$this->defunct = TRUE;
		return TRUE;
	}
}

class LuaWrapperExternal extends LuaWrapper {
	private $proc, $pipes;
	protected function __construct() {
		global $wgLuaExternalInterpreter, $wgLuaWrapperFile,
			$wgLuaMaxLines, $wgLuaMaxCalls;
		$luacmd = "$wgLuaExternalInterpreter $wgLuaWrapperFile $wgLuaMaxLines $wgLuaMaxCalls";
		$this->proc = proc_open($luacmd,
			array(0 => array('pipe', 'r'),
			1 => array('pipe', 'w')),
			$this->pipes, NULL, NULL);
		if (!is_resource($this->proc)) {
			$this->defunct = TRUE;
			throw new LuaError('interp_notfound');
		}
		stream_set_blocking($this->pipes[0], 0);
		stream_set_blocking($this->pipes[1], 0);
		stream_set_write_buffer($this->pipes[0], 0);
		stream_set_write_buffer($this->pipes[1], 0);
		$this->defunct = FALSE;
	}

	public function run($input) {
		global $wgLuaMaxTime;
		if ($this->defunct)
			return '';

		$input = trim(preg_replace('/(?<=\n|^)\.(?=\n|$)/', '. --', $input));
		fwrite($this->pipes[0], "$input\n.\n");
		fflush($this->pipes[0]);

		$res    = '';
		$read   = array($this->pipes[1]);
		$write  = NULL;
		$except = NULL;
		while (!feof($this->pipes[1])) {
			if (false === ($num_changed_streams =
				       @stream_select($read, $write, $except,
						      $wgLuaMaxTime))) {
				efLua_CleanupExternal();
				throw new LuaError('overflow_time');
			}
			$line = fgets($this->pipes[1]);
			if ($line == ".\n")
				break;
			$res .= $line;
		}

		if (preg_match('/^\'(.*)\', (true|false)$/s', trim($res), $match) != 1) {
			$this->kill();
			throw new LuaError('error_internal');
		}

		$out = $match[1];
		if ($match[2] == 'true') {
			return (trim($out) != '') ? $out : '';
		} else {
			if ($out == 'RECURSION_LIMIT') {
				$this->kill();
				throw new LuaError('overflow_recursion');
			} else if ($out == 'LOC_LIMIT') {
				$this->kill();
				throw new LuaError('overflow_loc');
			} else {
				$out = preg_replace('/^\[.+?\]:(.+?):/', '$1:', $out);
				throw new LuaError('error', $out);
			}
		}
	}

	public function kill() {
		if ($this->defunct)
			return FALSE;

		if (isset($this->proc)) {
			fclose($this->pipes[0]);
			fclose($this->pipes[1]);
			proc_close($this->proc);
		}
		$this->defunct = TRUE;
		return TRUE;
	}
}

