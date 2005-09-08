// created on 9/8/2005 at 12:45 AM
namespace MediaWiki.Blocker

import System
import System.Collections
import System.IO

import Nini.Config

class Config:
	static _conf = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "mwblocker.conf")
	static _source as IConfigSource
	
	static Source as IConfigSource:
		get:
			if _source is null:
				_source = IniConfigSource(_conf)
			return _source
	
	static def Get(section as string, key as string) as string:
		return Get(section, key, "")
	
	static def Get(section as string, key as string, default as string) as string:
		sec = Source.Configs[section]
		if sec:
			return sec.Get(key, default)
		else:
			return default
	
	static def CommaList(section as string, key as string) as IEnumerable:
		return CommaList(section, key, "")
	
	static def CommaList(section as string, key as string, default as string) as IEnumerable:
		return /\s*,\s*/.Split(Get(section, key, default))
