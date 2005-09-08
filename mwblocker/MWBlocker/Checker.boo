// created on 9/4/2005 at 7:54 PM

namespace MediaWiki.Blocker

import System
import System.IO
import System.Net
import System.Net.Sockets
import System.Web.Mail
import System.Text

class Checker:
	_suspect as Suspect
	_log = StringBuilder()
	
	def constructor(suspect as Suspect):
		_suspect = suspect
	
	def Check():
		for sig as string in Config.CommaList("blocker", "signatures"):
			if Check(sig):
				return Guilty(sig)
		return Innocent()
	
	def Check(signature as string):
		Log("Running ${signature} check on ${_suspect}")
		checkCount = 0
		for command in Config.CommaList(signature, "check"):
			try:
				check = ParseCheck(command)
				checkCount++
				if not check.IsMatch(_suspect.IP, self):
					return false
			except e:
				Log("Bad check command '${command}': " + e.Message)
		if checkCount > 0:
			return true
		else:
			Log("No valid checks for ${signature}; ignoring.")
			return false
	
	def ParseCheck(commandString as string) as CheckPort:
		re = @/^(?<command>[a-zA-Z_]+)\s*\(\s*(?<port>\d+)\s*\)$/
		match = re.Match(commandString)
		if match:
			command = match.Groups["command"].ToString()
			port = int.Parse(match.Groups["port"].ToString())
			typemap = {
				"open": { port | return CheckPort(port) },
				"http": { port | return CheckHTTP(port) },
				"ssh": { port | return CheckSSH(port) } }
			if typemap.Contains(command):
				builder = cast(ICallable, typemap[command])
				return builder(port)
			else:
				raise ArgumentException("Unknown command '${command}'")
		else:
			raise ArgumentException("Invalid command format '${commandString}")
	
	def Log(text as string):
		print text
		_log.Append(text)
		_log.Append('\n')
	
	def Guilty(sig as string):
		Log("${_suspect} matches scan signature ${sig}")
		block = false
		mail = false
		for action in Config.CommaList(sig, "action"):
			if action == "block":
				Log("Blocking IP.")
				block = true
			if action == "mail":
				mail = true
				MailReport()
		try:
			Recorder.Record(_suspect, block, _log.ToString())
		except e:
			Log("Failed to record to block database: ${e}")
		if mail:
			MailReport()
		return true
	
	def Innocent():
		Log("${_suspect} is clear of known signatures")
		try:
			Recorder.Record(_suspect, false, _log.ToString())
		except e:
			Log("Failed to record to check log database: ${e}")
		return false
	
	def MailReport():
		try:
			message = MailMessage()
			message.From = Config.Get("mail", "from", "mwblocker@localhost")
			message.To = Config.Get("mail", "to", "root@localhost")
			message.Subject = "Wiki IP checker hit: " + _suspect
			message.Body = \
"""The wiki IP checker has found ${_suspect.IP} to be suspicious.
Check was prompted by: ${_suspect.Note}

Probe log:
${_log}

To look up the owner of the IP block:
* http://ws.arin.net/cgi-bin/whois.pl?queryinput=${_suspect.IP}
* http://www.ripe.net/whois?searchtext=${_suspect.IP}
* http://www.apnic.net/apnic-bin/whois.pl?searchtext=${_suspect.IP}

hugs and kisses,
  the MWBlocker daemon
"""
			SmtpMail.SmtpServer = Config.Get("mail", "smtp", "localhost")
			SmtpMail.Send(message)
		except e:
			print e
