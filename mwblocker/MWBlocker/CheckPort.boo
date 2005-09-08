// created on 9/6/2005 at 12:14 AM

namespace MediaWiki.Blocker

import System
import System.IO
import System.Net
import System.Net.Sockets
import System.Text.RegularExpressions

public class CheckPort:
	_parent as Checker
	_port as int
	_client as TcpClient
	_writer as StreamWriter
	_reader as StreamReader
	
	public def constructor(port as int):
		_port = port
	
	public def IsMatch(suspect as IPAddress, parent as Checker) as bool:
		_parent = parent
		try:
			Connect(suspect)
			return Test()
		except e as SocketException:
			Log("...connection timeout.")
		except e as IOException:
			Log("...read timeout.")
		except e:
			Log("...unexpected error: " + e)
		ensure:
			Close()
		return false
	
	def Log(text as string):
		_parent.Log(text)
	
	def SafeString(text as string):
		// re = @/[^\x20-\x7e]/ // boo parser bug in 0.6.0 w/ hex
		re = Regex("[^\\x20-\\x7e]")
		return re.Replace(text, ".")
	
	def Connect(suspect as IPAddress):
		Log("Connecting to port " + _port)
		iface = Config.Get("blocker", "interface")
		if iface:
			// Bind to a particular local IP address
			endpoint = IPEndPoint(IPAddress.Parse(iface), 0)
			_client = TcpClient(endpoint)
		else:
			// Bind to default interface
			_client = TcpClient()
		timeout = int.Parse(Config.Get("blocker", "timeout", "2000")) // milliseconds
		_client.SendTimeout = timeout
		_client.ReceiveTimeout = timeout
		_client.Connect(suspect, _port)
		
		stream = _client.GetStream()
		_writer = StreamWriter(stream)
		_reader = StreamReader(stream)
	
	virtual def Test():
	"""
	Run the check and return true if it's a match.
	For CheckPort, Just checks if it's open.
	"""
		Log("...port is open")
		return true
		
	def Close():
		try:
			_writer.Close()
		except:
			pass
		_writer = null
		
		try:
			_reader.Close()
		except:
			pass
		_reader = null
		
		try:
			_client.Close()
		except:
			pass
		_client = null
		_parent = null
