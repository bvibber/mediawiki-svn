/* @(#) $Header$ */
#include "smstdinc.hxx"
#include "smirc.hxx"
#include "smutl.hxx"
#include "smcfg.hxx"
#include "smnet.hxx"
#include "smtrm.hxx"
#include "smlog.hxx"

namespace smirc {

	struct irctrmsrv : public smtrm::terminal {
		ircclnt& client;

		smtrm::comdat cd;
		typedef smtrm::handler_node handler_node_t;
		handler_node_t cmds_root;

		irctrmsrv(ircclnt& client_)
			: client(client_)
			, cd(*this)
			, cmds_root(SMI(smtrm::tmcmds)->stdrt)
			{
			}

		void chgrt(smtrm::handler_node* newrt) {
			cmds_root = *newrt;
		}

		bool is_interactive(void) const {
			return false;
		}

		bool prefer_short_output(void) const {
			return true;
		}
		
		void parse(str line) {
			std::string ln = line;
			if (!ln.size()) return;
			cd.rst();
			int wild;
			bool b = true;
			std::vector<handler_node_t *> matches;
			std::string precar, word;
			handler_node_t *here = &cmds_root;
			int herelen = 0;
			ln = remove_modifiers(ln);
			for (;;) {
				precar = ln;
				word = smutl::car(ln);
				if (!word.size()) break;
				if (word == "?") {
					matches = here->find_matches(2, "", wild);
					for (std::vector<handler_node_t *>::iterator it = matches.begin(),
						     end = matches.end(); it != end; ++it) {
						wrtln(b::str(format("  %s %s") %
							     boost::io::group(std::left, std::setw(20), (**it).name)
							     % (**it).help));
					}
					return;
				}
				matches = here->find_matches(2, word, wild);

				if (matches.size() > 1) {
					wrtln(b::io::str(b::format("%% Ambiguous command: %s\037%s\037%s")
							 % line.substr(0, herelen)
							 % line.substr(herelen, 1)
							 % line.substr(herelen + 1)));
					return;
				} else if (matches.size() == 0) {
					wrtln(b::io::str(b::format("%% Unknown command: %s\037%s\037%s")
							 % line.substr(0, herelen)
							 % line.substr(herelen, 1)
							 % line.substr(herelen + 1)));
					return;
				}
				herelen += word.size() + 1;
				here = matches[0];

				if (wild == 1)
					cd.add_p(word);
				else if (wild == 2) {
					cd.add_p(precar);
					break;
				}
			}

			if (!here->terminal) {
				wrtln(b::io::str(b::format("%% Incomplete command: %s") % line));
				return;
			}
			b = matches[0]->terminal->execute(cd);
		}
		
		/* parser bookkeeping */
		str getdata(void) {
			static std::string nulldata = "";
			return nulldata;
		}
		void setdata(str) {}
		void echo(bool) {}

		void wrt(u_char c, bool force) {
			/*
			 * hm... this doesn't make much sense in a line-oriented,
			 * non-interactive environment.  maybe we should queue up
			 * chars and send them either at the end of the request,
			 * or before the next line output.  however, no-one actually
			 * uses this yet, so this'll do for now.
			 */
			client.command_reply(std::string(1, c));
		}
		void wrt(str msg, bool force = false) {
			if (!force && !includematch(msg))
				return;
			client.command_reply(msg);
		}
		void wrtln(str msg, bool force = false) {
			wrt(msg, force);
		}
		void readline(readline_cb_t) {
			throw smtrm::non_interactive_terminal();
		}
		int getlevel(void) const {
			return 2;
		}
		void setlevel(int) {
			/* no-op */
		}
		void setprmbase(str) {
			/* no-op */
		}
	};
	
void
ircclnt::nick(str pnick_)
{
	pnick = pnick_;
}

void
ircclnt::nick(str pnick_, str snick_)
{
	pnick = pnick_;
	snick = snick_;
}

bool
ircclnt::rdline(strr l) {
	char c;
	try {
		for (;;) {
			c = sckt->rd1();
			if (c != '\r' && c != '\n') linebuf += c;
			else { l = linebuf; linebuf = ""; return true; }
		}
	} catch (smnet::wouldblock&) {
		return false;
	} catch (smnet::sckterr& e) {
		std::string err = "IRC read error: ";
		SMI(smlog::log)->logmsg(0, err + e.what());
	}
	return false;
}

void
ircclnt::connected()
{
	doregister();
}

void
ircclnt::doregister()
{
	sckt->wrt("USER servmon servmon servmon :servmon\r\n");
	sckt->wrt(b::io::str(b::format("NICK %s\r\n") % pnick));
}

void
ircclnt::join(str channel)
{
	sckt->wrt("JOIN " + channel + "\r\n");
}

void
ircclnt::part(str channel)
{
	sckt->wrt("PART " + channel + "\r\n");
}

void
ircclnt::cb_001(cbdata& cd)
{
	if (cd.args.size() < 1) return; /* XXX broken server .. error? */
	mynick = cd.args[0];
	try {
		std::set<std::string> chans = SMI(smcfg::cfg)->fetchlist("/irc/channels");
		FE_TC_AS(std::set<std::string>, chans, i)
			join(*i);
	} catch (smcfg::nokey&) {
	}
}

void
ircclnt::cb_ping(cbdata& cd)
{
	sckt->wrt("PONG :" + cd.args[0]);
}

void
ircclnt::cb_privmsg(cbdata& cd)
{
	if (cd.args.size() < 2) return;
	std::string target = cd.args[0];
	std::string text = cd.args[1];
	std::string firstword = smutl::car(text);
	replyto = target;
	if (firstword != mynick && firstword != "servmon")
		return;
	trmpimpl->parse(text);
}

void
ircclnt::command_reply(str msg)
{
	if (!replyto.size()) return;
	if (!sckt) return;
	sckt->wrt("PRIVMSG " + replyto + " :" + msg + "\r\n");
}
	
void
ircclnt::msg(str channel, str message)
{
	if (!sckt) return;
	sckt->wrt("PRIVMSG " + channel + " :" + message + "\r\n");
}

void
ircclnt::msg(int level, str message)
{
	std::set<std::string> chans;
	try {
		chans = SMI(smcfg::cfg)->fetchlist("/irc/channels");
	} catch (smcfg::nokey&) {
		/* no channels */
		return;
	}
	FE_TC_AS(std::set<std::string>, chans, i) {
		int thislevel = 10;
		try {
			thislevel = SMI(smcfg::cfg)->fetchint("/irc/channel/"+*i+"/level");
		} catch (smcfg::nokey&) {}
		if (level < thislevel)
			continue;
		msg(*i, message);
	}
}

void
ircclnt::data_cb(int what)
{
	if (what == smnet::smpx::srd) {
		std::string line;
		if (!rdline(line)) return;
		/* "empty" lines are caused by \r\n.  we could handle this
		   further down by treating \r\n properly, but some servers
		   (batamut) are broken and will only send \n in some circumstances.
		*/
		if (line.empty()) return;
                parseline(line);
	}
}

void
ircclnt::parseline(std::string line)
{
	SMI(smlog::log)->debug(smlog::irc, "Parse line: [" + line + "]");
	/* an IRC message has three basic parts: prefix, command and arguments.
	   the first argument for numerics is the target (i.e. us). */
	cbdata cbd;

	/* prefix? */
	if (line[0] == ':') {
		cbd.prefix = smutl::car(line).substr(1);
	}
	cbd.command = smutl::car(line);
	/* last argument */
	std::size_t lpartp = line.find(" :");
	std::string lpart;
	if (lpartp != std::string::npos) {
		lpart = line.substr(lpartp + 2);
		line = line.substr(0, lpartp);
	}
	std::string s;
	while ((s = smutl::car(line)) != "") {
		cbd.args.push_back(s);
	}
	if (!lpart.empty()) cbd.args.push_back(lpart);
	std::map<std::string, cbtype>::const_iterator it = cbs.find(cbd.command);
	if (it == cbs.end()) return;
	it->second(cbd);
}

ircclnt::ircclnt(std::string const& serv, int port)
	: trmpimpl(new irctrmsrv(*this))
{
	name = serv;
	cbs["001"] = b::bind(&ircclnt::cb_001, this, _1);
	cbs["PING"] = b::bind(&ircclnt::cb_ping, this, _1);
	cbs["PRIVMSG"] = b::bind(&ircclnt::cb_privmsg, this, _1);
	pnick = SMI(smcfg::cfg)->fetchstr("/irc/server/"+serv+"/nickname");
	SMI(smlog::log)->debug(smlog::irc, "ircclnt: connecting to " + serv);
	sckt = smnet::clntp(new smnet::clnt);
	sckt->svc(lexical_cast<std::string>(port));
	sckt->node(serv);
	cip = true;
	if (sckt->connect()) { cip = false; connected(); }
	boost::function<void(smnet::scktp, int)> f =
			boost::bind(&ircclnt::data_cb, this, _2);
	SMI(smnet::smpx)->add(f, static_pointer_cast<smnet::sckt>(sckt), smnet::smpx::srd /*| smnet::smpx::swr*/);
}

ircclnt::~ircclnt()
{
	delete trmpimpl;
}

ircclnt::ircclnt(void)
{
	cip = false;
}

void
cfg::initialise(void)
{
	SMI(smtmr::evthdlr)->install(smtmr::evtp(
			new smtmr::evt("IRC: connection status", 5, true, b::bind(&cfg::chk, SMI(cfg)))));
}

void
cfg::chk(void)
{
	SMI(smlog::log)->debug(smlog::irc,
			       b::io::str(b::format("Check IRC status: cip=%d connected=%d")
					  % cip % connected));
	if (cip or connected) return; cip = true;

	if (next_server()) {
		connect();
	}
	cip = false;
}

bool
cfg::next_server(void)
{
	if (servers.empty()) get_servers();
	if (servers.empty()) return false;
	return true;
}

void
cfg::get_servers(void)
{
	try {
		servers = SMI(smcfg::cfg)->fetchlist("/irc/servers");
		srv_iter = servers.begin();
	} catch (smcfg::nokey&) {}
}

void
cfg::connect(void)
{
	if (srv_iter == servers.end()) return;
	try {
		connection = ircclntp(new ircclnt(*srv_iter, getkeyint(*srv_iter, "port")));
		connected = true;
	} catch (smcfg::nokey&) {}
	return;
}

ircclntp
cfg::conn(void)
{
	return connection ? connection : ircclntp(new ircclnt);
}

void
cfg::newserv_or_chgnick(str server, str nick)
{
	if (!SMI(smcfg::cfg)->listhas("/irc/servers", server)) {
		SMI(smcfg::cfg)->addlist("/irc/servers", server);
		setkeyint(server, "port", 6667);
	}
	setkeystr(server, "nickname", nick);
}

void 
cfg::server_set_secnick(str server, str nick)
{
	if (!SMI(smcfg::cfg)->listhas("/irc/servers", server))
		return;
	setkeystr(server, "secnickname", nick);
}

bool 
cfg::server_exists(str server)
{
	return SMI(smcfg::cfg)->listhas("/irc/servers", server);
}

void 
cfg::remove_server(str server)
{
	try {
		SMI(smcfg::cfg)->dellist("/irc/servers", server);
	} catch (smcfg::nokey&) {
	}
}

void 
cfg::enable_server(str server, bool ebl) 
{
	setkeybool(server, "enabled", ebl);
}

bool 
cfg::server_enabled(str server)
{
	try {
		return getkeybool(server, "enabled");
	} catch (smcfg::nokey&) {
		return false;
	}
}

void
cfg::channel(str channel)
{
	if (SMI(smcfg::cfg)->listhas("/irc/channels", channel))
		return;
	SMI(smcfg::cfg)->addlist("/irc/channels", channel);
	conn()->join(channel);
}

bool
cfg::nochannel(str channel)
{
	if (!SMI(smcfg::cfg)->listhas("/irc/channels", channel))
		return false;
	SMI(smcfg::cfg)->dellist("/irc/channels", channel);
	conn()->part(channel);
	return true;
}

void
cfg::channel_level(str chan, int level)
{
	if (!SMI(smcfg::cfg)->listhas("/irc/channels", chan))
		channel(chan);
	SMI(smcfg::cfg)->storeint("/irc/channel/"+chan+"/level", level);
}

int 
cfg::getkeyint(str server, str key)
{
	return SMI(smcfg::cfg)->fetchint(b::str(format("/irc/server/%s/%s")
		% server % key));
}

bool 
cfg::getkeybool(str server, str key)
{
	return SMI(smcfg::cfg)->fetchbool(b::str(format("/irc/server/%s/%s")
		% server % key));
}

str
cfg::getkeystr(str server, str key)
{
	return SMI(smcfg::cfg)->fetchstr(b::str(format("/irc/server/%s/%s")
		% server % key));
}

void
cfg::setkeyint(str server, str key, int value)
{
	SMI(smcfg::cfg)->storeint(b::str(format("/irc/server/%s/%s")
		% server % key), value);
}

void
cfg::setkeystr(str server, str key, str value)
{
	SMI(smcfg::cfg)->storestr(b::str(format("/irc/server/%s/%s")
		% server % key), value);
}

void
cfg::setkeybool(str server, str key, bool value)
{
	SMI(smcfg::cfg)->storebool(b::str(format("/irc/server/%s/%s")
		% server % key), value);
}

} // namespace smirc
