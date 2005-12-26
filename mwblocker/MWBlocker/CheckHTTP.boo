// created on 9/6/2005 at 12:20 AM
namespace MediaWiki.Blocker

import System
import System.IO
import System.Net
import System.Net.Sockets

public class CheckHTTP(CheckPort):
	public def constructor(port as int):
		super(port)
	
	def Test():
		Log("...sending HTTP headers")
		_writer.WriteLine("HEAD /robots.txt HTTP/1.0");
		_writer.WriteLine("User-Agent: MediaWiki proxy checker (www.mediawiki.org)")
		_writer.WriteLine("");
		_writer.Flush()
		
		line = _reader.ReadLine()
		Log("...read line '" + SafeString(line) + "'")
		
		if line.StartsWith("HTTP/1."):
			Log("...found HTTP")
			return true
		else:
			Log("...not HTTP")
			return false
