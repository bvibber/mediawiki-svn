/* $Header$ */
#ifndef SM_SMTRM_HXX_INCLUDED_
#define SM_SMTRM_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smcfg.hxx"
#include "smirc.hxx"
#include "smutl.hxx"
#include "smauth.hxx"
#include "smmon.hxx"
#include "smqb.hxx"

namespace smtrm {

template<class tt>
class handler_node;

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
	{}

	typedef handler<tt> handler_t;

	std::map<std::string, handler_node> childs;
	handler_t *terminal;
	std::string help;
	std::string name; /* this shouldn't really be here.. */

	bool 	add_child(std::string cmd, handler_t *h, std::string const & desc) {
		if (cmd.empty()) {
			terminal = h;
			help = desc;
			return true;
		}
		std::string node = smutl::car(cmd);
		childs[node].add_child(cmd, h, desc);
		childs[node].name = node;
		return true;
	}

	std::vector<handler_node *>
	find_matches(std::string const& word, int& waswild) {
		std::vector<handler_node *> result;
		waswild = 0;
		for (typename std::map<std::string, handler_node>::iterator it = childs.begin(),
			end = childs.end(); it != end; ++it) {
			if (it->first.substr(0, word.size()) == word) {
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
			if (childs.find("%s") != childs.end()) {
				waswild = 1;
				result.push_back(&childs.find("%s")->second);
			} else if (childs.find("%S") != childs.end()) {
				waswild = 2;
				result.push_back(&childs.find("%S")->second);
			}
		}
		return result;
	}

	template<class T>
	bool install (std::string const &command, T cb, std::string const &desc)
	{
		T *h;
		try {
			h = new T (cb);
		} catch (std::bad_alloc&) {
			return false;
		}
		return _install(command, h, desc);
	}
	bool install (std::string const &command, std::string const & desc) {
		return add_child(command, NULL, desc); 
	}
	bool _install (std::string const &command, handler_t *h, std::string const &desc) {
		return add_child(command, h, desc); 
	}
};


template<class tt>
struct tmcmds : public smutl::singleton<tmcmds<tt> > {
#include "../smstdrt.cxx"
	tmcmds() {
/* standard mode commands */
stdrt.install("show version", cmd_show_version(), "Show software version");
stdrt.install("exit", cmd_exit(), "End session");
stdrt.install("show irc server %s", cfg_irc_showserver(), "Describe a configured server");
stdrt.install("show irc server", cfg_irc_showserver(), "Describe all configured servers");
stdrt.install("show irc channels", cfg_irc_showchannels(), "Show configured channels");
stdrt.install("show monitor server", cfg_monit_showservers(), "Show monitored servers");
stdrt.install("show monitor server %s", cfg_monit_showservers(), "Show information for a particular server");
stdrt.install("show monitor intervals", cfg_monit_showintervals(), "Show monitoring intervals");
stdrt.install("show querybane rule %s", cfg_qb_show_rule(), "Show QueryBane rule information");
stdrt.install("show querybane rules", cfg_qb_show_rule(), "Show all QueryBane rules");
eblrt = stdrt;
stdrt.install("enable", cmd_enable(), "Enter privileged mode");

/* 'enable' mode commands */
eblrt.install("disable", chg_parser(stdrt, "%s> "), "Return to non-privileged mode");
eblrt.install("configure", chg_parser(cfgrt, "%s(conf)# "), "Configure servmon");

/* 'configure' mode commands */
cfgrt.install("exit", chg_parser(eblrt, "%s# "), "Exit configure mode");
cfgrt.install("enable password", cfg_eblpass(), "Change enable password");
cfgrt.install("function irc", chg_parser(ircrt, "%s(conf-irc)# "), "Configure Internet Relay Chat connections");
cfgrt.install("function monitor", chg_parser(monrt, "%s(conf-monit)# "), "Configure server monitoring");
cfgrt.install("user %s password", cfg_userpass(), "Create a new account");
cfgrt.install("no user %s", cfg_no_user(), "Remove a user account");
cfgrt.install("function querybane", chg_parser(qbrt, "%s(conf-qb)# "), "Configure QueryBane operation");

/* 'function irc' mode commands */
ircrt.install("exit", chg_parser(cfgrt, "%s(conf)# "), "Exit IRC configuration mode");
ircrt.install("server %s primary-nickname %s", cfg_irc_servnick(), "Set primary nickname for IRC server");
ircrt.install("server %s secondary-nickname %s", cfg_irc_servsecnick(),	"Set secondary nickname for IRC server");
ircrt.install("no server %s", cfg_irc_noserver(), "Remove a configured server");
ircrt.install("show server %s", cfg_irc_showserver(), "Describe a configured server");
ircrt.install("show server", cfg_irc_showserver(), "Describe all configured servers");
ircrt.install("show channels", cfg_irc_showchannels(), "Show configured channels");
ircrt.install("no server %s enable", cfg_irc_noenableserver(), "Disable a server");
ircrt.install("server %s enable", cfg_irc_enableserver(), "Enable connection to a server");
ircrt.install("channel %s", cfg_irc_channel(), "Specify a channel to join");
ircrt.install("no channel %s", cfg_irc_nochannel(), "Remove a channel");

/* 'function monitor' mode commands */
monrt.install("server %s type %s", cfg_monit_server_type(), "Monitor a server");
monrt.install("server %s mysql-master", cfg_monit_server_mysql_master(), "Set server as MySQL master");
monrt.install("mysql username %s", cfg_monit_mysql_username(), "Set MySQL username");
monrt.install("mysql password %s", cfg_monit_mysql_password(), "Set MySQL password");
monrt.install("monitor-interval %s", cfg_monit_monitor_interval(), "Monitor interval in seconds");
monrt.install("irc-status-interval %s", cfg_monit_ircinterval(), "IRC status interval in seconds");
monrt.install("exit", chg_parser(cfgrt, "%s(conf)# "), "Exit monitor configuration mode");

/* 'function querybane' mode commands */
qbrt.install("rule %s", cfg_qb_rule(), "Define a new rule");
qbrt.install("exit", chg_parser(cfgrt, "%s(conf)# "), "Exit querybane configuration mode");

/* querybane 'rule' mode commands */
qbrrt.install("exit", chg_parser(qbrt, "%s(conf-qb)# "), "Exit rule configuration mode");
qbrrt.install("description %S", cfg_qbr_description(), "Rule description");
qbrrt.install("match-if min-threads %s", cfg_qbr_matchif_minthreads(), "Miminum thread count");
qbrrt.install("match-if min-last-threads %s", cfg_qbr_matchif_minlastthreads(), "Minimum thread count previous check");
qbrrt.install("match-if lowest-position %s", cfg_qbr_matchif_lowestpos(), "Only match if Nth longest running thread");
qbrrt.install("match-if user %S", cfg_qbr_matchif_user(), "Match threads owned by user");
qbrrt.install("match-if command %s", cfg_qbr_matchif_command(), "Match command type");
qbrrt.install("match-if min-run-time %s", cfg_qbr_matchif_minruntime(), "Only match after specified run time (seconds)");
qbrrt.install("match-if query-string %S", cfg_qbr_matchif_querystring(), "Match specified query text");
qbrrt.install("enable", cfg_qbr_enable(), "Enable rule");
	}
	handler_node<tt> stdrt;
	handler_node<tt> eblrt;
	handler_node<tt> cfgrt;
	handler_node<tt> ircrt;
	handler_node<tt> monrt;
	handler_node<tt> qbrt, qbrrt;
};

template<class intft>
class trmsrv : noncopyable {
public:
	typedef boost::function<void(trmsrv&, std::string const&)> rl_cb_t;

	trmsrv(intft sckt_)
	: intf(sckt_)
	, cmds_root(SMI(tmcmds<trmsrv>)->stdrt)
	, prm("servmon> ")
	, cd(*this)
	, doecho(true)
	, rlip(false)
	, destroyme(false)
	{
		stb_nrml();
	}

	virtual ~trmsrv(void) {
		std::cerr << "trmsrv dtor\n";
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
		std::string user, pass;
		wrtln();
		wrt("Username: ");
		readline(boost::bind(&trmsrv::gt_usr_cb, this, _2));
	}
	void gt_usr_cb(std::string const& user) {
		usrnam = user;
		echo(false);
		wrt("Password: ");
		readline(boost::bind(&trmsrv::gt_psw_cb, this, _2));
	}
	void gt_psw_cb(std::string const& pass) {
		echo(true);
		wrtln();
		if (!smauth::login_usr(usrnam, pass)) {
			wrtln("% [E] Username or password incorrect.");
			disconnect();
			return;
		}
		stb_nrml();
	}
	void gd_cb(smnet::inetclntp, u_char c) {
		std::cerr << "read data: [" << c << "]\n";
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
		prm = b::str(format(prompt) % "servmon");
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
		std::cerr << "read: [" << ln << "]\n";
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
			matches = here->find_matches(word, wild);
			
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
			
			if (wild == 1)
				cd.add_p(word);
			else if (wild == 2) {
				cd.add_p(precar);
				break;
			}
			here = matches[0];
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
			std::cerr<<"in loop: word now ["<<word<<"]\n";
			/* if we're at the last word, and they want matches for a partial
			   command, break now */
			matches = here->find_matches(word, wild);
			
			if (!showall && l2.empty()) break;
			//matches = here->find_matches(word, wild);
			/* reached the end of the line anyway */
			if (word.empty()) break;
			if (matches.size() > 1 && !word.empty()) {
				wrtln("% [E] Ambiguous command.");
				return true;
			} else if (matches.size() == 0) {
				wrtln("% [E] Unknown command.");
				return true;
			}
			word = "";
			here = matches[0];
		}
		std::cerr << "word: ["<<word<<"] showall: "<<showall<<"\n";
		for (typename std::vector<handler_node_t *>::iterator it = matches.begin(),
			 end = matches.end(); it != end; ++it) {
			std::cerr << "name: ["<<(**it).name<<"]\n";
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
private:
	intft intf;
	std::map<u_char, boost::function<bool (char)> > binds;
	std::string usrnam;
	// bookkeeping for parser
	rl_cb_t rl_cb;
	std::string ln;
	typedef handler_node<trmsrv> handler_node_t;
	handler_node_t cmds_root;
	std::string prm;
	comdat<trmsrv> cd;
	bool doecho;
	bool rlip;
	bool destroyme;
	std::string data;
};
typedef trmsrv<smnet::inettnsrvp> inettrmsrv;
typedef shared_ptr<inettrmsrv> inettrmsrvp;

} // namespace smtrm
#endif
