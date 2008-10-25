import irclib
import time
import adminlog

target="#wikimedia-tech"
nickserv="nickserv"
nickpassword="..."

def on_connect(con, event):
	con.privmsg(nickserv,"identify "+nickpassword)
	time.sleep(1)
	con.join(target)

def on_msg(con, event):
	if event.target() != target: return
	author,rest=event.source().split('!')
	line=event.arguments()[0]
	if line.startswith("!log "):
		undef,message=line.split(" ",1);
		try: adminlog.log(message,author)
		except: server.privmsg(target,"I failed :(")
		

irc = irclib.IRC()
server = irc.server()
server.connect("irc.freenode.net",6667,"morebots")
server.add_global_handler("welcome", on_connect)
server.add_global_handler("pubmsg",on_msg)

irc.process_forever()
