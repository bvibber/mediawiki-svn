/* @(#) $Header$ */
#ifndef SM_SMTRM_HXX_INCLUDED_
#define SM_SMTRM_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smcfg.hxx"
#include "smirc.hxx"
#include "smutl.hxx"
#include "smauth.hxx"
#include "smmon.hxx"
#include "smqb.hxx"
#include "smmc.hxx"
#include "smalrm.hxx"

namespace smtrm {

template<class tt>
class handler_node;

struct non_interactive_terminal : std::runtime_error {
	non_interactive_terminal() : std::runtime_error("terminal does not support interaction") {}
};
	
template<class tt>
class comdat {
public:
	comdat(tt& term_)
	: term(term_)
	{}
 
	int num_params() const { 
		return ps.size();
	}
 
	std::string const & p (unsigned int n) const {
		return ps[n];
	}
	void add_p (std::string const & p) { 
		ps.push_back(p); 
	}
	void warn (std::string const & msg) const {
		term.warn(msg);
	}
	void inform (std::string const & msg) const {
		term.inform(msg); 
	}
	void error (std::string const & msg) const {
		term.error(msg);
	}
	void wrtln (std::string const & msg = "") const {
		term.wrtln(msg);
	}
	void wrt (std::string const & msg) const {
		term.wrt(msg);
	}
	void chgrt (handler_node<tt>* newrt, std::string const& prm) const {
		term.chgrt(newrt, prm);
	}
	std::string read_string (std::string const& prompt) const {
		term.wrt(prompt);
		std::string s;
		term.read_echo(s);
		return s;
	}
	std::string getpass (std::string const& prompt) const
	{
		term.wrt(prompt);
		term.echo(false);
		std::string s;// = term.read(); /* XXX is this used anywhere? */
		term.echo(true);
		wrtln();
		return s;
	}
	void rst(void) {
		ps.resize(0);
	}
	void setdata(str data) const {
		term.setdata(data);
	}
	str getdata(void) const {
		return term.getdata();
	}
	tt& term; //XXX
private:
	std::vector<std::string> ps;
};

template<class tt>
class handler {
public:
	virtual ~handler() {}
	virtual bool execute (comdat<tt> const&) = 0;
};

template<class tt>
struct handler_node {
	handler_node()
	: terminal(NULL)
	, level(0)
	{}

	typedef handler<tt> handler_t;

	std::map<std::string, handler_node> childs;
	handler_t *terminal;
	std::string help;
	std::string name; /* this shouldn't really be here.. */
	int level;
	
	bool 	add_child(int level, std::string cmd, handler_t *h, std::string const & desc) {
		if (cmd.empty()) {
			terminal = h;
			help = desc;
			this->level = level;
			return true;
		}
		std::string node = smutl::car(cmd);
		childs[node].add_child(level, cmd, h, desc);
		childs[node].name = node;
		return true;
	}

	std::vector<handler_node *>
	find_matches(int level, std::string const& word, int& waswild) {
		std::vector<handler_node *> result;
		waswild = 0;
		for (typename std::map<std::string, handler_node>::iterator it = childs.begin(),
			end = childs.end(); it != end; ++it) {
			if (it->first.substr(0, word.size()) == word) {
				if (it->second.level > level)
					continue;
				
				// if its an exact match, just return it
				if (it->first == word) {
					result.erase(result.begin(), result.end());
					result.push_back(&it->second);
					return result;
				}
				result.push_back(&it->second);
			}
		}
		if (result.empty()) {
			typename std::map<std::string, handler_node>::iterator it;
			
			if ((it = childs.find("%s")) != childs.end() && it->second.level <= level) {
				waswild = 1;
				result.push_back(&it->second);
			} else if ((it = childs.find("%S")) != childs.end() && it->second.level <= level) {
				waswild = 2;
				result.push_back(&it->second);
			}
		}
		return result;
	}

	template<class T>
	bool install (int level, std::string const &command, T cb, std::string const &desc)
	{
		T *h;
		try {
			h = new T (cb);
		} catch (std::bad_alloc&) {
			return false;
		}
		return _install(level, command, h, desc);
	}
	bool install (int level, std::string const &command, std::string const & desc) {
		return add_child(level, command, NULL, desc); 
	}
	bool _install (int level, std::string const &command, handler_t *h, std::string const &desc) {
		return add_child(level, command, h, desc); 
	}
};


template<class tt>
struct tmcmds : public smutl::singleton<tmcmds<tt> > {
#include "../smstdrt.cxx"
	tmcmds() {
/* restricted non-logged commands */
stdrt.install(1, "login", cmd_login(), "Authenticate to servmon");
stdrt.install(1, "exit", cmd_exit(), "End session");
/* standard mode commands (includes non-restricted non-logged in users) */
stdrt.install(2, "show", "Show operational information");
stdrt.install(2, "show version", cmd_show_version(), "Show software version");
stdrt.install(2, "show irc", "Show IRC-related information");
stdrt.install(2, "show irc server %s", cmd_irc_showserver(), "Describe a configured server");
stdrt.install(2, "show irc server", cmd_irc_showserver(), "Describe all configured servers");
stdrt.install(2, "show monitor", "Show monitoring information");
stdrt.install(2, "show monitor server", cmd_monit_showservers(), "Show monitored servers");
stdrt.install(2, "show monitor server %s", cmd_monit_showservers(), "Show information for a particular server");
stdrt.install(2, "show monitor intervals", cmd_monit_showintervals(), "Show monitoring intervals");
stdrt.install(2, "show querybane", "Show QueryBane information");
stdrt.install(2, "show querybane rule", "Show a specific rule");
stdrt.install(2, "show querybane rule %s", cmd_qb_show_rule(), "Rule name");
stdrt.install(2, "show querybane rules", cmd_qb_show_rule(), "Show all QueryBane rules");
stdrt.install(2, "show memcache", "Show memcache client information");
stdrt.install(2, "show memcache server-list-command", cmd_mc_show_server_list_command(), "Show server list command");
stdrt.install(2, "show parser", "Show MediaWiki parser-related information");
stdrt.install(2, "show parser cache-statistics", cmd_mc_show_parser_cache(), "Show parser cache hit statistics");
stdrt.install(3, "show irc channels", cmd_irc_showchannels(), "Show configured channels");
stdrt.install(3, "enable", cmd_enable(), "Enter privileged mode");

/* 'enable' mode commands */
stdrt.install(16, "disable", cmd_disable(), "Return to non-privileged mode");
stdrt.install(16, "configure", chg_parser(cfgrt, "%s[%d](conf)#"), "Configure servmon");
stdrt.install(16, "debug", "Runtime debugging functions");
stdrt.install(16, "debug mysql", "Debug MySQL functions");
stdrt.install(16, "debug mysql connect", cmd_debug_mysql_connect(), "Debug MySQL connections");
stdrt.install(16, "debug mysql query", cmd_debug_mysql_query(), "Debug MySQL queries");
stdrt.install(16, "debug mysql monitoring", cmd_debug_mysql_monitoring(), "Debug MySQL server monitoring");
stdrt.install(16, "debug irc", cmd_debug_irc(), "Debug IRC connections");
stdrt.install(16, "no debug", "Runtime debugging functions");
stdrt.install(16, "no debug mysql", "Debug MySQL functions");
stdrt.install(16, "no debug mysql connect", cmd_no_debug_mysql_connect(), "Debug MySQL connections");
stdrt.install(16, "no debug mysql query", cmd_no_debug_mysql_query(), "Debug MySQL queries");
stdrt.install(16, "no debug mysql monitoring", cmd_no_debug_mysql_monitoring(), "Debug MySQL server monitoring");
stdrt.install(16, "no debug irc", cmd_no_debug_irc(), "Debug IRC connections");

/* 'configure' mode commands */
cfgrt.install(16, "exit", cmd_enable(), "Exit configure mode");
cfgrt.install(16, "enable password", cfg_eblpass(), "Change enable password");
cfgrt.install(16, "function", "Configure a specific function");
cfgrt.install(16, "function irc", chg_parser(ircrt, "%s[%d](conf-irc)#"), "Configure Internet Relay Chat connections");
cfgrt.install(16, "function monitor", chg_parser(monrt, "%s[%d](conf-monit)#"), "Configure server monitoring");
cfgrt.install(16, "function memcache", chg_parser(memrt, "%s[%d](conf-memcache)#"), "Configure memcached client");
cfgrt.install(16, "user", "Define users");
cfgrt.install(16, "user %s", "Username");
cfgrt.install(16, "user %s password", cfg_userpass(), "Create a new account");
cfgrt.install(16, "no", "Negate a setting");
cfgrt.install(16, "no user", "Remove a user account");
cfgrt.install(16, "no user %s", cfg_no_user(), "User name");
cfgrt.install(16, "function querybane", chg_parser(qbrt, "%s[%d](conf-qb)#"), "Configure QueryBane operation");

/* 'function irc' mode commands */
ircrt.install(16, "exit", chg_parser(cfgrt, "%s[%d](conf)#"), "Exit IRC configuration mode");
ircrt.install(16, "server", "Configure IRC servers");
ircrt.install(16, "server %s primary-nickname %s", cfg_irc_servnick(), "Set primary nickname for IRC server");
ircrt.install(16, "server %s secondary-nickname %s", cfg_irc_servsecnick(),	"Set secondary nickname for IRC server");
ircrt.install(16, "no", "Negate a setting");
ircrt.install(16, "no server %s", cfg_irc_noserver(), "Remove a configured server");
ircrt.install(16, "no server %s enable", cfg_irc_noenableserver(), "Disable a server");
ircrt.install(16, "server %s enable", cfg_irc_enableserver(), "Enable connection to a server");
ircrt.install(16, "channel", "Configure channels");
ircrt.install(16, "channel %s", cfg_irc_channel(), "Specify a channel to join");
ircrt.install(16, "channel %s level", "Notification level for channel");
ircrt.install(16, "channel %s level %s", cfg_irc_channel_level(), "Level (1-16)");
ircrt.install(16, "no channel %s", cfg_irc_nochannel(), "Remove a channel");

/* 'function monitor' mode commands */
monrt.install(16, "server", "Configure servers to monitor");
monrt.install(16, "server %s", "Server name");
monrt.install(16, "server %s type", "Specify server type");
monrt.install(16, "server %s type %s", cfg_monit_server_type(), "Create new server");
monrt.install(16, "server %s mysql-master", cfg_monit_server_mysql_master(), "Set server as MySQL master");
monrt.install(16, "mysql", "Configure global MySQL parameters");
monrt.install(16, "mysql username", "MySQL username");
monrt.install(16, "mysql password", "MySQL password");
monrt.install(16, "mysql username %s", cfg_monit_mysql_username(), "Set MySQL username");
monrt.install(16, "mysql password %s", cfg_monit_mysql_password(), "Set MySQL password");
monrt.install(16, "monitor-interval", "Monitor interval in seconds");
monrt.install(16, "monitor-interval %s", cfg_monit_monitor_interval(), "Monitor interval in seconds");
monrt.install(16, "irc-status-interval", "IRC status interval in seconds");
monrt.install(16, "irc-status-interval %s", cfg_monit_ircinterval(), "IRC status interval in seconds");
monrt.install(16, "threshold", "Set alarm thresholds");
monrt.install(16, "threshold mysql", "MySQL-related alarm thresholds");
monrt.install(16, "threshold mysql replication-lag", "Maximum replication lag from master");
monrt.install(16, "threshold mysql replication-lag %s", cfg_monit_alarm_mysql_replag(), "Maximum replication lag in seconds");
monrt.install(16, "threshold mysql running-threads", "Maximum number of running threads");
monrt.install(16, "threshold mysql running-threads %s", cfg_monit_alarm_mysql_threads(), "Maximum number of threads");
monrt.install(16, "exit", chg_parser(cfgrt, "%s[%d](conf)#"), "Exit monitor configuration mode");

/* 'function querybane' mode commands */
qbrt.install(16, "rule", "Define a new rule");
qbrt.install(16, "rule %s", cfg_qb_rule(), "Rule name");
qbrt.install(16, "no", "Negate a setting");
qbrt.install(16, "no rule", "Delete a rule");
qbrt.install(16, "no rule %s", cfg_qb_norule(), "Rule name");
qbrt.install(16, "exit", chg_parser(cfgrt, "%s[%d](conf)#"), "Exit querybane configuration mode");

/* querybane 'rule' mode commands */
qbrrt.install(16, "exit", chg_parser(qbrt, "%s[%d](conf-qb)#"), "Exit rule configuration mode");
qbrrt.install(16, "description %S", cfg_qbr_description(), "Rule description");
qbrrt.install(16, "match-if", "Specify parameters to match this rule");
qbrrt.install(16, "match-if min-threads", "Match on miminum thread count");
qbrrt.install(16, "match-if min-threads %s", cfg_qbr_matchif_minthreads(), "Miminum thread count");
qbrrt.install(16, "match-if min-last-threads", "Match on minimum thread count previous check");
qbrrt.install(16, "match-if min-last-threads %s", cfg_qbr_matchif_minlastthreads(), "Minimum thread count previous check");
qbrrt.install(16, "match-if lowest-position", "Match on position");
qbrrt.install(16, "match-if lowest-position %s", cfg_qbr_matchif_lowestpos(), "Only match if Nth longest running thread");
qbrrt.install(16, "match-if user", "Match on username");
qbrrt.install(16, "match-if user %S", cfg_qbr_matchif_user(), "Match threads owned by user");
qbrrt.install(16, "match-if command", "Match command type");
qbrrt.install(16, "match-if command %s", cfg_qbr_matchif_command(), "Match command type");
qbrrt.install(16, "match-if min-run-time", "Match on minimum run time");
qbrrt.install(16, "match-if min-run-time %s", cfg_qbr_matchif_minruntime(), "Only match after specified run time (seconds)");
qbrrt.install(16, "match-if query-string", "Match on query string");
qbrrt.install(16, "match-if query-string %S", cfg_qbr_matchif_querystring(), "Match specified query text");
qbrrt.install(16, "enable", cfg_qbr_enable(), "Enable rule");

/* 'function memcache' commands */
memrt.install(16, "server-list-command", "Set command used to obtain server list");
memrt.install(16, "server-list-command %S", cfg_mc_server_list_command(), "Command name");
memrt.install(16, "exit", chg_parser(cfgrt, "%s[%d](conf)#"), "Exit memcache configuration mode");

	}
	handler_node<tt> stdrt;
	handler_node<tt> cfgrt;
	handler_node<tt> ircrt;
	handler_node<tt> monrt;
	handler_node<tt> memrt;
	handler_node<tt> qbrt, qbrrt;
};

template<class intft>
class trmsrv : noncopyable {
public:
	typedef boost::function<void(trmsrv&, std::string const&)> rl_cb_t;

	trmsrv(intft sckt_)
	: intf(sckt_)
	, cmds_root(SMI(tmcmds<trmsrv>)->stdrt)
	, prmbase("%s [%d]>")
	, cd(*this)
	, doecho(true)
	, rlip(false)
	, destroyme(false)
	, level(2)
	{
		mkprm();
		stb_nrml();
	}

	virtual ~trmsrv(void) {
	}
	void stb_nrml(void) {
		for (int i = 0; i < 32; ++i)
			binds[i] = boost::bind(&trmsrv::prc_ign, this, _1);
		for (int i = 32; i <= 255; ++i)
			binds[i] = boost::bind(&trmsrv::prc_char, this, _1);
		binds['\r'] = boost::bind(&trmsrv::prc_nl, this, _1);
		binds[' '] = boost::bind(&trmsrv::prc_spc, this, _1);
		binds['?'] = boost::bind(&trmsrv::prc_help, this, _1);
		binds[0x0C] = boost::bind(&trmsrv::prc_redraw, this, _1); // ^L
		binds[0177] = binds['\b'] = boost::bind(&trmsrv::prc_del, this, _1);
		binds[0x15] = boost::bind(&trmsrv::prc_erase, this, _1); // ^U
	}
	void stb_readline(void) {
		binds[' '] = binds['?'] =
			boost::bind(&trmsrv::prc_char, this, _1);
	}

	void start(void) {
		init();
		intf->cb(boost::bind(&trmsrv::gd_cb, this, _1, _2));
		stb_nrml();
		level = 2;
		wrt(prm);
	}
	void gd_cb(smnet::inetclntp, u_char c) {
		if (!binds[c](c))
			disconnect();
		if (destroyme) delete this;
	}
	void echo(bool doecho_) {
		intf->echo(doecho = doecho_);
	}

	void wrtln(std::string const& s = "") {
		wrt(s); wrt("\r\n");
	}
	void wrt(u_char c) {
		if (destroyme) return;
		intf->wrt(&c, 1);
	}
	void wrt(std::string const& s) {
		if (destroyme) return;
		intf->wrt(s);
	}
	void chgrt(handler_node<trmsrv>* newrt, std::string const& prompt) {
		cmds_root = *newrt;
		mkprm();
	}
	void readline(rl_cb_t cb) {
		rl_cb = cb;
		rlip = true;
		stb_readline();
	}
	bool prc_ign(char) {
		return true;
	}
	bool prc_nl(char) {
		if (rlip) {
			stb_nrml();
			rlip = false;
			wrtln();
			rl_cb(*this, ln);
			ln = "";
			if (!rlip) wrt(prm);
			return true;
		}
		if (!ln.size()) {
			wrtln();
			wrt(prm);
			return true;
		}
		int wild;
		bool b = true;
		std::vector<handler_node_t *> matches;
		std::string precar, word;
		handler_node_t *here = &cmds_root;
		int herelen = prm.size();
		for (;;) {
			precar = ln;
			word = smutl::car(ln);
			if (!word.size()) break;
			matches = here->find_matches(level, word, wild);
			
			if (matches.size() > 1) {
				wrtln();
				wrtln(std::string(herelen, ' ') + '^');
				wrtln("% [E] Ambiguous command.");
				goto end;
			} else if (matches.size() == 0) {
				wrtln();
				wrtln(std::string(herelen, ' ') + '^');
				wrtln("% [E] Unknown command.");
				goto end;
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
			wrtln();
			wrtln(std::string(herelen, ' ') + '^');
			wrtln("% [E] Incomplete command.");
			goto end;
		}
		wrtln();
		b = matches[0]->terminal->execute(cd);
	  end:
		init();
		if (b) {
			if (!rlip) wrt(prm + ln);
		}
		return b;
	}
	bool prc_char(char c) {
		if (rlip) {
			ln += c;
			if (doecho) wrt(c);
			return true;
		}
		ln += c;
		if (doecho) wrt(c);
		return true;
	}
	bool prc_spc(char c) {
		if (ln.empty() || (ln[ln.size() - 1] == ' '))
			return true;

		if (doecho) wrt(' ');
		ln += + c;
		return true;
	}
	bool prc_help(char) {
		if (doecho) wrtln("?");
		std::string l2 = ln;
		bool showall = l2.empty() || (!l2.empty() && l2[l2.size()-1]==' ');
		std::vector<handler_node_t *> matches;
		std::string word;
		handler_node_t *here = &cmds_root;
		int wild;
		for (;;) {
			word = smutl::car(l2);
			/* if we're at the last word, and they want matches for a partial
			   command, break now */
			matches = here->find_matches(level, word, wild);
			
			if (!showall && l2.empty()) break;
			//matches = here->find_matches(word, wild);
			/* reached the end of the line anyway */
			if (word.empty()) break;
			if (matches.size() > 1 && !word.empty()) {
				wrtln("% [E] Ambiguous command.");
				wrt(prm + ln);
				return true;
			} else if (matches.size() == 0) {
				wrtln("% [E] Unknown command.");
				wrt(prm + ln);
				return true;
			}
			word = "";
			here = matches[0];
		}
		for (typename std::vector<handler_node_t *>::iterator it = matches.begin(),
			 end = matches.end(); it != end; ++it) {
			if (showall || (**it).name.substr(0,word.size())==word)
				wrtln(b::str(format("  %s %s") % 
					     boost::io::group(std::left, std::setw(20), (**it).name)
					     % (**it).help));
		}
		if (here && here->terminal)
			wrtln("  <cr>");
		wrt(prm + ln);
		return true;
	}
	bool prc_redraw(char) {
		wrtln();
		wrt(prm + ln);
		return true;
	}
	bool prc_del(char) {
		if (ln.empty())
			return true;
		wrt("\b \b");
		ln.erase(boost::prior(ln.end()));
		return true;
	}
	bool prc_erase(char) {
		for (int i = ln.size(); i; --i)
			wrt("\b \b");
		init();
		return true;
	}

	void init(void) {
		cd.rst();
		ln = "";
	}

	void disconnect(void) {
		destroyme = true;
	}
	void warn (std::string const & msg) {
		wrtln(b::str(format("%% [W] %s") % msg));
	}
	void inform (std::string const & msg) {
		wrtln(b::str(format("%% [I] %s") % msg)); 
	}
	void error (std::string const & msg) {
		wrtln(b::str(format("%% [E] %s") % msg));
	}
	void setdata(str data_) {
		data = data_;
	}
	str getdata(void) {
		return data;
	}
	void setlevel(int level_) {
		level = level_;
		mkprm();
	}
	int getlevel(void) const {
		return level;
	}
	void mkprm(void) {
		prm = boost::io::str(format(prmbase) % "servmon" % level);
	}
private:
	intft intf;
	std::map<u_char, boost::function<bool (char)> > binds;
	std::string usrnam;
	// bookkeeping for parser
	rl_cb_t rl_cb;
	std::string ln;
	typedef handler_node<trmsrv> handler_node_t;
	handler_node_t cmds_root;
	std::string prm, prmbase;
	comdat<trmsrv> cd;
	bool doecho;
	bool rlip;
	bool destroyme;
	std::string data;
	int level;
};
typedef trmsrv<smnet::inettnsrvp> inettrmsrv;
typedef shared_ptr<inettrmsrv> inettrmsrvp;

} // namespace smtrm
#endif
