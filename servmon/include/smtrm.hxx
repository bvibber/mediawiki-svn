/* @(#) $Header$ */
#ifndef SM_SMTRM_HXX_INCLUDED_
#define SM_SMTRM_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"
#include "smnet.hxx"

namespace smtrm {

class handler_node;
	
/* base terminal type */
class terminal {
public:
	typedef boost::function<void(terminal&, str)> readline_cb_t;

	virtual bool is_interactive(void) const = 0;
	virtual void echo(bool) = 0;
	virtual void wrtln(str = "") = 0;
	virtual void wrt(u_char) = 0;
	virtual void wrt(str) = 0;
	virtual void chgrt(handler_node* newrt) = 0;
	virtual void readline(readline_cb_t) = 0;
	virtual void error(str msg) {
		wrtln("%% [E] " + msg);
	}
	virtual void warn(str msg) {
		wrtln("%% [W] " + msg);
	}
	virtual void inform(str msg) {
		wrtln("%% [I] " + msg);
	}

	virtual str getdata(void) const {
		return m_data;
	}
	virtual void setdata(str d) {
		m_data = d;
	}
	virtual int getlevel(void) const {
		return m_level;
	}
	virtual void setlevel(int level) {
		m_level = level;
	}
	virtual ~terminal(void) {
	}
	virtual void setprmbase(str prmbase) = 0;
private:
	std::string m_data;
	int m_level;
};
	
class handler_node;

struct non_interactive_terminal : std::runtime_error {
	non_interactive_terminal() : std::runtime_error("terminal does not support interaction") {}
};
	
class comdat {
public:
	comdat(terminal& term_)
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
	void chgrt (handler_node* newrt) const {
		term.chgrt(newrt);
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
	terminal& term; //XXX
private:
	std::vector<std::string> ps;
};

class handler {
public:
	virtual ~handler() {}
	virtual bool execute (comdat const&) = 0;
};

struct handler_node {
	handler_node()
	: terminal(NULL)
	, level(0)
	{}

	std::map<std::string, handler_node> childs;
	handler *terminal;
	std::string help;
	std::string name; /* this shouldn't really be here.. */
	int level;
	
	bool 	add_child(int level, std::string cmd, handler *h, std::string const & desc) {
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
		for (std::map<std::string, handler_node>::iterator it = childs.begin(),
			end = childs.end(); it != end; ++it) {
			if (it->first.substr(0, word.size()) == word) {
				if (it->second.level > level)
					continue;
				
				/* if its an exact match, just return it */
				if (it->first == word) {
					result.erase(result.begin(), result.end());
					result.push_back(&it->second);
					return result;
				}
				result.push_back(&it->second);
			}
		}
		if (result.empty()) {
			std::map<std::string, handler_node>::iterator it;
			
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
	bool _install (int level, std::string const &command, handler *h, std::string const &desc) {
		return add_child(level, command, h, desc); 
	}
};

struct tmcmds : public smutl::singleton<tmcmds> {
	tmcmds();

	handler_node stdrt;
	handler_node cfgrt;
	handler_node ircrt;
	handler_node monrt;
	handler_node memrt;
	handler_node qbrt, qbrrt;
};

class trmsrv : noncopyable, public terminal {
public:
	trmsrv(smnet::tnsrvp sckt_)
	: intf(sckt_)
	, cmds_root(SMI(tmcmds)->stdrt)
	, prmbase("%s [%d]>")
	, cd(*this)
	, doecho(true)
	, rlip(false)
	, destroyme(false)
	{
		setlevel(2);
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
		setlevel(2);
		wrt(prm);
	}
	void gd_cb(smnet::clntp, u_char c) {
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
	void chgrt(handler_node* newrt) {
		cmds_root = *newrt;
		mkprm();
	}
	void readline(readline_cb_t cb) {
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
		std::vector<handler_node *> matches;
		std::string precar, word;
		handler_node *here = &cmds_root;
		int herelen = prm.size();
		for (;;) {
			precar = ln;
			word = smutl::car(ln);
			if (!word.size()) break;
			matches = here->find_matches(getlevel(), word, wild);
			
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
		std::vector<handler_node *> matches;
		std::string word;
		handler_node *here = &cmds_root;
		int wild;
		for (;;) {
			word = smutl::car(l2);
			/* if we're at the last word, and they want matches for a partial
			   command, break now */
			matches = here->find_matches(getlevel(), word, wild);
			
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
		for (std::vector<handler_node *>::iterator it = matches.begin(),
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

	void setlevel(int level) {
		terminal::setlevel(level);
		mkprm();
	}
	void mkprm(void) {
		try {
			prm = boost::io::str(format(prmbase) % "servmon" % getlevel());
		} catch (std::exception&) {
			prm = "[exception while creating prompt] servmon>";
		}
	}
	void setprmbase(str base) {
		prmbase = base;
		mkprm();
	}
	bool is_interactive(void) const {
		return true;
	}
private:
	smnet::tnsrvp intf;
	std::map<u_char, boost::function<bool (char)> > binds;
	std::string usrnam;
	// bookkeeping for parser
	readline_cb_t rl_cb;
	std::string ln;
	handler_node cmds_root;
	std::string prm, prmbase;
	comdat cd;
	bool doecho;
	bool rlip;
	bool destroyme;
};
typedef shared_ptr<trmsrv> trmsrvp;

} // namespace smtrm
#endif
