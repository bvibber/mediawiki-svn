// created on 9/6/2005 at 12:20 AM
namespace MediaWiki.Blocker

import System
import System.IO
import System.Net
import System.Net.Sockets

public class CheckSSH(CheckPort):
	public def constructor(port as int):
		super(port)
	
	def Test():
		line = _reader.ReadLine()
		Log("...read line '" + SafeString(line) + "'")
		if line.StartsWith("SSH-"):
			Log("...found SSH")
			return true
		else:
			Log("...not SSH")
			return false
