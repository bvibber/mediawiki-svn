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
	terminal();
	typedef boost::function<void(terminal&, str)> readline_cb_t;

	virtual bool is_interactive(void) const = 0;
	virtual bool prefer_short_output(void) const;
	virtual void echo(bool) = 0;
	virtual void wrtln(str = "", bool = false) = 0;
	virtual void wrt(u_char, bool = false) = 0;
	virtual void wrt(str, bool = false) = 0;
	virtual void chgrt(handler_node* newrt) = 0;
	virtual void readline(readline_cb_t) = 0;
	virtual void error(str msg);
	virtual void warn(str msg);
	virtual void inform(str msg);

	virtual str getdata(void) const;
	virtual void setdata(str d);
	virtual int getlevel(void) const;
	virtual void setlevel(int level);
	virtual ~terminal(void);
	virtual void setprmbase(str prmbase) = 0;

	virtual void setinclude(str incl);
	virtual void delinclude();
	virtual bool includematch(str s) const;
	virtual std::string remove_modifiers(str s);
	
private:
	std::string m_data;
	int m_level;
	regex *incl_reg;
};
	
class handler_node;

struct non_interactive_terminal : std::runtime_error {
	non_interactive_terminal() : std::runtime_error("terminal does not support interaction") {}
};
	
class comdat {
public:
	comdat(terminal& term_);
 
	int num_params() const;
	std::string const &p (unsigned int n) const;
	void add_p (std::string const & p);
	void rst(void);
	terminal& term;
	
private:
	std::vector<std::string> ps;
};

class handler {
public:
	virtual ~handler();
	virtual bool execute (comdat const&) = 0;
};

struct handler_node {
	handler_node();

	std::map<std::string, handler_node> childs;
	handler *terminal;
	std::string help;
	std::string name; /* this shouldn't really be here.. */
	int level;
	
	bool 	add_child(int level, std::string cmd, handler *h, std::string const & desc);

	std::vector<handler_node *>
	find_matches(int level, std::string const& word, int& waswild);

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
	bool install (int level, std::string const &command, std::string const & desc);
	bool _install (int level, std::string const &command, handler *h, std::string const &desc);
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
	trmsrv(smnet::tnsrvp sckt_);
	virtual ~trmsrv(void);
	void stb_nrml(void);
	void stb_readline(void);
	void start(void);
	void gd_cb(smnet::clntp, u_char c);
	void cls_cb(smnet::clntp, smnet::sckterr&);
	void echo(bool doecho_);
	void wrtln(std::string const& s = "", bool force = false);
	void wrt(u_char c, bool = false);
	void wrt(std::string const& s, bool force = false);
	void chgrt(handler_node* newrt);
	void readline(readline_cb_t cb);
	bool prc_ign(char);
	bool prc_nl(char);
	bool prc_char(char);
	bool prc_spc(char);
	bool prc_help(char);
	bool prc_redraw(char);
	bool prc_del(char);
	bool prc_erase(char);
	void init(void);
	void disconnect(void);
	void setlevel(int level);
	void mkprm(void);
	void setprmbase(str base);
	bool is_interactive(void) const;

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
