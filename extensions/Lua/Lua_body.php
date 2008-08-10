<?php
/**
 * Lua parser extensions for MediaWiki - Body
 *
 * @author Fran Rogers
 * @package MediaWiki
 * @addtogroup Extensions
 * @license See 'COPYING'
 * @file
 */

function efLua_ParserInit() {
	global $wgParser;
	$wgParser->setHook( 'lua', 'efLua_Render' );
	return true;
}

function efLua_FunctionSetup() {
	global $wgParser;
	$wgParser->setFunctionHook('luaexpr', 'efLua_RenderExpr');
}

function efLua_Magic(&$magicWords, $langCode) {
	$magicWords['luaexpr'] = array(0, 'luaexpr');
	return true;
}

function efLua_BeforeTidy(&$parser, &$text) {
	if (!isset($wgLua)) {
		efLua_Cleanup();
	}
	return TRUE;
}

function efLua_Render($input, $args, &$parser) {
	$arglist = '';
	foreach ($args as $key => $value)
	$arglist .= (preg_replace('/\W/', '', $key) . '=\'' .
		addslashes($parser->recursiveTagParse($value)) .
		'\';');
	if ($arglist) {
		try {
			efLua_Eval($arglist);
			} catch (LuaError $e) {
				return $e->getMessage();
			}
		}

		try {
			return $parser->recursiveTagParse(efLua_Eval($input));
			} catch (LuaError $e) {
				return $e->getMessage();
			}
		}

function efLua_RenderExpr(&$parser, $param1 = FALSE) {
	if ($param1 == FALSE)
		return '';
	try {
		return efLua_Eval("io.write($param1)");
	} catch (LuaError $e) {
		return $e->getMessage();
	}
}


/**
 * @todo Nearly all of these evaluate() calls should be replaced, as bugs
 *       in the Lua PECL extension are fixed.
 */
function efLua_Eval($input) {
	global $wgLuaExternalInterpreter, $wgLuaDefunct,
		$wgLua, $wgLuaSandbox, $wgLuaWrapperFile,
		$wgLuaMaxLines, $wgLuaMaxCalls;
	if (isset($wgLuaDefunct) && $wgLuaDefunct) {
		return '';
	} else if ($wgLuaExternalInterpreter != FALSE) {
		return efLua_EvalExternal($input);
	} else if (!class_exists('lua')) {
		$wgLuaDefunct = true;
		throw new LuaError('extension_notfound');
	}

	if (!isset($wgLua)) {
		$wgLua = new lua;
		try {
			$wgLua->evaluatefile($wgLuaWrapperFile);
			$wgLua->evaluate("sandbox = make_sandbox()");
			$wgLua->evaluate("function _die(reason)".
					 "  _G._DEAD = reason; end");
			$wgLua->evaluate("hook = make_hook($wgLuaMaxLines, ".
					 "$wgLuaMaxCalls, _die)");
			// @todo Especially these ones. :/
		} catch (Exception $e) {
			throw new LuaError('error_internal');
		}
	}

	$wgLua->input = $input;
	$wgLua->evaluate('chunk, err = loadstring(input)');
	if ($err = $wgLua->err) {
		$err = preg_replace('/^\[.+?\]:(.+?):/', '$1:', $err);
		throw new LuaError('error', $err);
	}
	$wgLua->res = $wgLua->err = NULL;

	$wgLua->evaluate('res, err = wrap(chunk, sandbox, hook)');

	if (($err = $wgLua->_DEAD) || ($err = $wgLua->err)) {
		if ($err == 'RECURSION_LIMIT') {
			efLua_CleanupExternal();
			throw new LuaError('overflow_recursion');
		} else if ($err == 'LOC_LIMIT') {
			efLua_CleanupExternal();
			throw new LuaError('overflow_loc');
		} else {
			$err = preg_replace('/^\[.+?\]:(.+?):/', '$1:', $err);
			throw new LuaError('error', $err);
		}
	}

	$wgLua->evaluate('_OUTPUT = sandbox._OUTPUT'); // ugh, please fix!
	$out = $wgLua->_OUTPUT;
	return (trim($out) != '') ? $out : '';
}

function efLua_Cleanup() {
	global $wgLuaExternalInterpreter, $wgLuaDefunct, $wgLua;
	if (isset($wgLuaDefunct) && $wgLuaDefunct) {
		return FALSE;
	} else if (isset($wgLuaExternalInterpreter)) {
		return efLua_CleanupExternal();
	} else if (isset($wgLua)) {
		$wgLuaDefunct = TRUE;
		$wgLua = FALSE;
		return TRUE;
	}
}

function efLua_EvalExternal($input) {
	global $wgLuaExternalInterpreter, $wgLuaProc, $wgLuaPipes,
		$wgLuaWrapperFile, $wgLuaMaxLines, $wgLuaMaxCalls,
		$wgLuaMaxTime;
	if (!isset($wgLuaProc)) {
		$wgLua = TRUE;
		$luacmd = "$wgLuaExternalInterpreter $wgLuaWrapperFile $wgLuaMaxLines $wgLuaMaxCalls";
		$wgLuaProc = proc_open($luacmd,
			array(0 => array('pipe', 'r'),
			1 => array('pipe', 'w')),
			$wgLuaPipes, NULL, NULL);
		if (!is_resource($wgLuaProc)) {
			$wgLuaDefunct = true;
			throw new LuaError('interp_notfound');
		}
		stream_set_blocking($wgLuaPipes[0], 0);
		stream_set_blocking($wgLuaPipes[1], 0);
		stream_set_write_buffer($wgLuaPipes[0], 0);
		stream_set_write_buffer($wgLuaPipes[1], 0);
	}

	$input = trim(preg_replace('/(?<=\n|^)\.(?=\n|$)/', '. --', $input));
	fwrite($wgLuaPipes[0], "$input\n.\n");
	fflush($wgLuaPipes[0]);

	$res = '';
	$read   = array($wgLuaPipes[1]);
	$write  = NULL;
	$except = NULL;
	while (!feof($wgLuaPipes[1])) {
		if (false === ($num_changed_streams =
				@stream_select($read, $write, $except,
				$wgLuaMaxTime))) {
			efLua_CleanupExternal();
			throw new LuaError('overflow_time');
		}
		$line = fgets($wgLuaPipes[1]);
		if ($line == ".\n")
			break;
		$res .= $line;
	}

	if (preg_match('/^\'(.*)\', (true|false)$/s', trim($res), $match) != 1) {
		efLua_CleanupExternal();
		throw new LuaError('error_internal');
	}

	$out = $match[1];
	if ($match[2] == 'true') {
		return (trim($out) != '') ? $out : '';
	} else {
		if ($out == 'RECURSION_LIMIT') {
			efLua_CleanupExternal();
			throw new LuaError('overflow_recursion');
		} else if ($out == 'LOC_LIMIT') {
			efLua_CleanupExternal();
			throw new LuaError('overflow_loc');
		} else {
			$out = preg_replace('/^\[.+?\]:(.+?):/', '$1:', $out);
			throw new LuaError('error', $out);
		}
	}
}

function efLua_CleanupExternal() {
	global $wgLuaExternalInterpreter, $wgLuaDefunct,
	       $wgLuaProc, $wgLuaPipes;
	if (isset($wgLuaProc)) {
		fclose($wgLuaPipes[0]);
		fclose($wgLuaPipes[1]);
		proc_close($wgLuaProc);
	}
	$wgLuaDefunct = TRUE;
	return TRUE;
}

class LuaError extends Exception {
	public function __construct($msg, $parameter = ''){
		wfLoadExtensionMessages( 'Lua' );
		$this->message = '<strong class="error">' . wfMsgForContent( "lua_$msg", htmlspecialchars( $parameter ) ) . '</strong>';
	}
}
