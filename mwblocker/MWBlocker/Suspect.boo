// created on 9/7/2005 at 1:18 AM

namespace MediaWiki.Blocker

import System.Net

class Suspect:
	public IP as IPAddress
	public Note as string
	
	def constructor(ip as IPAddress, note as string):
		IP = ip
		Note = note

	def constructor(ip as string, note as string):
		IP = IPAddress.Parse(ip)
		Note = note

	public def ToString() as string:
		return IP.ToString()
