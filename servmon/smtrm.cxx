/* @(#) $Header$ */

#include "smstdinc.hxx"
#include "smutl.hxx"
#include "smtrm.hxx"

#include "msgtab.hxx"

namespace smtrm {

int terminal::idseq = 0;
std::map<int, terminal *> terminal::terms;
	
terminal::terminal(void)
	: lastact(std::time(0))
	, mode("exec")
	, incl_reg(NULL)
	, id(++idseq)
{
	terms[id] = this;
}

terminal::~terminal(void)
{
	terms.erase(id);
}

std::map<int, terminal *> const&
terminal::getterms(void)
{
	return terms;
}

void
terminal::broadcast(str message)
{
	for(std::map<int, terminal*>::const_iterator it = terms.begin(),
		    end = terms.end(); it != end; ++it)
		it->second->do_broadcast(message);
}

std::string
terminal::fmtidle(void) const
{
	return smutl::tdiff2(lastact, true);
}

std::time_t
terminal::getlastactive(void) const
{
	return lastact;
}

int
terminal::getid(void) const
{
	return id;
}
	
void
terminal::setusername(str u)
{
	username = u;
}

str
terminal::getusername(void) const
{
	return username;
}

void
terminal::setmode(str m)
{
	mode = m;
}

str
terminal::getmode(void) const
{
	return mode;
}
	
bool
terminal::prefer_short_output(void) const
{
	return false;
}

void
terminal::message(int fac, int msg, sm$msgarg a1, sm$msgarg a2, sm$msgarg a3)
{
	wrtln(sm$getmsg(fac, msg, a1, a2, a3));
}
		
str
terminal::getdata(str v) const
{
	static std::string const nulld = "";
	std::map<std::string, std::string>::const_iterator it =
		m_data.find(v);
	return (it == m_data.end()) ? nulld : it->second;
}

void
terminal::ersdata(str v)
{
	m_data.erase(v);
}

void
terminal::setdata(str v, str d)
{
	m_data[v] = d;
}

int
terminal::getlevel(void) const
{
	return m_level;
}

void
terminal::setlevel(int l)
{
	m_level = l;
}

void
terminal::setinclude(str incl)
{
	delete incl_reg;
	incl_reg = new regex(smutl::lower(incl));
}

void
terminal::delinclude(void)
{
	delete incl_reg;
	incl_reg = NULL;
}

bool
terminal::includematch(str s) const
{
	return !incl_reg || regex_search(smutl::lower(s), *incl_reg);
}

std::string
terminal::remove_modifiers(str s)
{
	delinclude();
	std::string::size_type t = s.find('|'), nt;
	if (t == std::string::npos)
		return s;
	nt = t + 1;
	do {
		--t;
	} while (t && s[t] == ' ');
	if (t < 1)
		return s;
	while (nt < s.size() && s[nt] == ' ')
		++nt;
	std::string n = s.substr(0, t + 1), mod = s.substr(nt);
	std::string what = smutl::car(mod);
	if (what.empty())
		return n;
	else if (what == "include")
		setinclude(mod);
	else {
		wrtln("", true);
		message(SM$FAC_TRM, SM$MSG_UNKMOD, what);
	}
	return n;
}

comdat::comdat(terminal& term_)
	: term(term_)
{
}
	
int
comdat::num_params(void) const
{
	return ps.size();
}

std::string const&
comdat::p(u_int n) const
{
	return ps[n];
}

void
comdat::add_p(str p)
{
	ps.push_back(p);
}

void
comdat::rst(void)
{
	ps.resize(0);
}

handler::~handler()
{
}

handler_node::handler_node(void)
	: terminal(NULL)
	, level(0)
{
}

bool
handler_node::add_child(int level, std::string cmd, handler *h, str desc)
{
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
handler_node::find_matches(int level, str word, int& waswild)
{
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

bool
handler_node::install(int level, str command, str desc)
{
	return add_child(level, command, NULL, desc);
}

bool
handler_node::_install(int level, str command, handler *h, str desc)
{
	return add_child(level, command, h, desc);
}

trmsrv::trmsrv(smnet::tnsrvp sckt_)
	: intf(sckt_)
	, cmds_root(SMI(tmcmds)->stdrt)
	, prmbase("%s [%d] %s>")
	, cd(*this)
	, doecho(true)
	, rlip(false)
	, destroyme(false)
{
	setlevel(2);
	mkprm();
	stb_nrml();
}
	
trmsrv::~trmsrv(void)
{
}

void
trmsrv::stb_nrml(void)
{
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

void
trmsrv::stb_readline(void)
{
	binds[' '] = binds['?'] =
		boost::bind(&trmsrv::prc_char, this, _1);
}

void
trmsrv::start(void)
{
	init();
	intf->cb(boost::bind(&trmsrv::gd_cb, this, _1, _2));
	stb_nrml();
	setlevel(2);
	wrt(prm);
}

void
trmsrv::gd_cb(smnet::clntp, u_char c) {
	if (!binds[c](c))
		disconnect();
	if (destroyme) delete this;
}

void
trmsrv::cls_cb(smnet::clntp, smnet::sckterr&)
{
	delete this;
}

void
trmsrv::echo(bool doecho_)
{
	intf->echo(doecho = doecho_);
}

void
trmsrv::wrtln(str s, bool force)
{
	wrt(s + "\r\n", force);
}

void
trmsrv::wrt(u_char c, bool)
{
	intf->wrt(std::string(1, c));
}

void
trmsrv::wrt(str s, bool force)
{
	if (destroyme || (!force && !includematch(s))) return;
	intf->wrt(s);
}

void
trmsrv::chgrt(handler_node *newrt)
{
	cmds_root = *newrt;
}

void
trmsrv::readline(readline_cb_t cb)
{
	rl_cb = cb;
	rlip = true;
	stb_readline();
}

bool
trmsrv::prc_ign(char)
{
	return true;
}

bool
trmsrv::prc_nl(char)
{
	lastact = std::time(0);
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
	ln = remove_modifiers(ln);
	for (;;) {
		precar = ln;
		word = smutl::car(ln);
		if (!word.size()) break;
		matches = here->find_matches(getlevel(), word, wild);
		
		if (matches.size() > 1) {
			wrt("\r\n", true);
			wrtln(std::string(herelen, ' ') + '^');
			wrtln(sm$getmsg(SM$FAC_TRM, SM$MSG_AMBIG));
			goto end;
		} else if (matches.size() == 0) {
			wrt("\r\n", true);
			wrtln(std::string(herelen, ' ') + '^');
			wrtln(sm$getmsg(SM$FAC_TRM, SM$MSG_UNKCMD));
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
		wrt("\r\n", true);
		wrtln(std::string(herelen, ' ') + '^');
		wrtln(sm$getmsg(SM$FAC_TRM, SM$MSG_INCOMP));
		goto end;
	}
	
	wrtln("", true);
	b = matches[0]->terminal->execute(cd);
  end:
	delinclude();
	init();
	if (b) {
		if (!rlip) wrt(prm + ln, true);
	}
	return b;
}

bool
trmsrv::prc_char(char c)
{
	if (ln.size() > 65535) return true;
	if (rlip) {
		ln += c;
		if (doecho) wrt(c);
		return true;
	}
	ln += c;
	if (doecho) wrt(c);
	return true;
}

bool
trmsrv::prc_spc(char c)
{
	if (ln.empty() || (ln[ln.size() - 1] == ' '))
		return true;

	if (doecho) wrt(' ');
	ln += + c;

	return true;
}

bool
trmsrv::prc_help(char)
{
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
		if ((showall || (**it).name.substr(0,word.size())==word) && (**it).level <= getlevel())
			wrtln(b::str(format("  %s %s") % 
				     boost::io::group(std::left, std::setw(20), (**it).name)
				     % (**it).help));
	}
	if (here && here->terminal)
		wrtln("  <cr>");
	wrt(prm + ln);
	return true;
}

bool
trmsrv::prc_redraw(char)
{
	wrtln();
	wrt(prm + ln);
	return true;
}

bool
trmsrv::prc_del(char)
{
	if (ln.empty())
		return true;
	if (doecho)
		wrt("\b \b");
	ln.erase(boost::prior(ln.end()));
	return true;
}

bool
trmsrv::prc_erase(char)
{
	if (doecho)
		for (int i = ln.size(); i; --i)
			wrt("\b \b");
	init();
	return true;
}

void
trmsrv::init(void)
{
	cd.rst();
	ln = "";
}

void
trmsrv::disconnect(void)
{
	destroyme = true;
}

void
trmsrv::setlevel(int level)
{
	terminal::setlevel(level);
	mkprm();
}

void
trmsrv::mkprm(void)
{
	try {
		prm = boost::io::str(format(prmbase) % "servmon" % getlevel() % getmode());
	} catch (std::exception&) {
		prm = "[exception while creating prompt] servmon>";
	}
}
	
void
trmsrv::setmode(str mode)
{
	terminal::setmode(mode);
	mkprm();
}

bool
trmsrv::is_interactive(void) const
{
	return true;
}

std::string
trmsrv::remote(void) const
{
	return intf->remote();
}

void
trmsrv::do_broadcast(str message)
{
	f_bol();
	f_ceol();
	wrtln("% " + smutl::fmttime() + ": " + message);
	wrt(prm + ln);
}

void
trmsrv::f_bol(void)
{
	wrt("\x18[0G");
}

void
trmsrv::f_ceol(void)
{
	wrt("\x18[K");
}
} // namespace smtrm
