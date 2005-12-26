// created on 9/6/2005 at 7:21 PM

namespace MediaWiki.Blocker

import System
import System.IO
import System.Runtime.Remoting

import CookComputing.XmlRpc

class BlockDaemon(MarshalByRefObject):
"""XML-RPC daemon; app configuration is set for port 8126.
This is a network interface only, real work is in CheckerThread."""

	static def Run():
		configFile = Path.Combine(AppDomain.CurrentDomain.BaseDirectory,
			"MWBlocker.exe.config")
		RemotingConfiguration.Configure(configFile)
		RemotingConfiguration.RegisterWellKnownServiceType(
			typeof(BlockDaemon),
			"Blocker",
			WellKnownObjectMode.Singleton)
		CheckerThread.Run()
	
	[XmlRpcMethod("blocker.queueCheck")]
	def QueueCheck(ip as string, note as string) as bool:
		CheckerThread.Enqueue(Suspect(ip, note))
		return true

	[XmlRpcMethod("blocker.getStatus")]
	def GetStatus() as string:
		return CheckerThread.Status;
