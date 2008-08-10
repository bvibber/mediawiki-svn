<?php
/**
 * Lua parser extensions for MediaWiki - Hooks
 *
 * @author Fran Rogers
 * @package MediaWiki
 * @addtogroup Extensions
 * @license See 'COPYING'
 * @file
 */

class LuaHooks {
	public static function parserInit() {
		global $wgParser;
		$wgParser->setHook( 'lua', 'LuaHooks::renderTag' );
		$wgParser->setFunctionHook('luaexpr', 'LuaHooks::renderExpr');
		return true;
	}

	public static function magic(&$magicWords, $langCode) {
		$magicWords['luaexpr'] = array(0, 'luaexpr');
		return true;
	}

	public static function beforeTidy(&$parser, &$text) {
		global $wgLua;
		if (isset($wgLua)) {
			$wgLua->kill();
		}
		return TRUE;
	}

	public static function renderTag($input, $args, &$parser) {
		global $wgLua;
		if (!isset($wgLua))
			$wgLua = LuaWrapper::create();

		$arglist = '';
		foreach ($args as $key => $value)
			$arglist .= (preg_replace('/\W/', '', $key) . '=\'' .
				     addslashes($parser->recursiveTagParse($value)) .
				     '\';');
		if ($arglist) {
			try {
				$wgLua->run($arglist);
			} catch (LuaError $e) {
				return $e->getMessage();
			}
		}

		try {
			return $parser->recursiveTagParse($wgLua->run($input));
		} catch (LuaError $e) {
			return $e->getMessage();
		}
	}
	
	public static function renderExpr(&$parser, $param1 = FALSE) {
		global $wgLua;
		if (!isset($wgLua))
			$wgLua = LuaWrapper::create();
		
		if ($param1 == FALSE)
			return '';
		try {
			return $wgLua->run("io.write($param1)");
		} catch (LuaError $e) {
			return $e->getMessage();
		}
	}
}
