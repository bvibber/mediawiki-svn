/*
 * $Header$
 *
 * Realtime recent changes feed.
 * This code is in the public domain.
 */

#include <sys/types.h>
#include <sys/socket.h>

#include <netinet/in.h>

#include <arpa/inet.h>

#include <map>
#include <list>
#include <set>
#include <vector>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <queue>
#include <string>
#include <iostream>
#include <fstream>
#include <sstream>
#include <ctime>

#include <unistd.h>

#include <mysql.h>

std::string messages_en[] = {
	"Show bots",
	"Hide bots",
	"Show logged in users",
	"Hide logged in users",
	"Show minor edits",
	"Hide minor edits",
	"Feedback",
	"Key: N = new; B = bot; m = minor; 0-9 = number of reverts last 24 hrs",
	"diff",
	"Talk",
	"hist",
	"contribs",
	"moved to",
	"N", "B", "m"
};

std::string messages_sv[] = {
	"Visa robotredigeringar",
	"Göm robotredigeringar",
	"Visa inloggade användare",
	"Göm inloggade användare",
	"Visa mindre redigeringar",
	"Göm mindre redigeringar",
	"Respons",
	"Key: N = ny; B = robot; m = mindre redigering; 0-9 = antal återställningar de senaste 24 timmarna",
	"skillnad",
	"Diskussion",
	"historik",
	"contribs",
	"moved to",
	"N", "B", "m"
};

std::string messages_fi[] = {
	"Näytä botit",
	"Piilota botit",
	"Näytä kirjautuneiden käyttäjien muokkaukset",
	"Piilota kirjautuneiden käyttäjien muokkaukset",
	"Näytä pienet muokkaukset",
	"Piilota pienet muokkaukset",
	"Palaute",
	"Tiedot: U = uusi, B = botti, p = pieni muutos, 0-9 = palautukset 24 tunnin sisällä.",
	"ero",
	"Keskustelu",
	"historia",
	"contribs",
	"moved to",
	"U", "B", "p"
};
std::string messages_nl[] = {
	"Toon robots",
	"Verberg robots",
	"Toon aangemelde gebruikers",
	"Verberg aangemelde gebruikers",
	"Toon kleine wijzigingen",
	"Verberg kleine wijzigingen",
	"Reageer",
	"Legende: N = nieuw, B = robot, K = kleine wijziging, 0-9 = aantal herstellingen in de laatste 24 uur.",
	"wijz",
	"Overleg",
	"gesch",
	"contribs", 
	"moved to",
	"N", "B", "K"
};

std::string messages_fr[] = {
	"montrer robots",
	"cacher robots",
	"montrer utilisateurs enregistrés",
	"cacher utilisateurs enregistrés",
	"montrer modifications mineures",
	"cacher modifications mineures",
	"Commentaires",
	"Légende: N = nouveau, B = Bot, m = Modifications mineures, 0-9 = nombre de réversions dans les dernières 24 heures",
	"diff",
	"Discuter",
	"hist",
	"contribs",
	"moved to",
	"N", "B", "m"
};
std::string messages_it[] = {
	"Mostra i bot",
	"Nascondi i bot",
	"Mostra gli utenti connessi",
	"Nascondi gli utenti connessi",
	"Mostra le modifiche minori",
	"Nascondi le modifiche minori",
	"Feedback",
	"Key: N = nuovo; B = bot; m = modifica minore; 0-9 = numbero di rollback nelle 24 ore",
	"diff",
	"Discussioni",
	"cron",
	"contribs",
	"moved to",
	"N", "B", "m"
};

std::string messages_de[] = {
	"Bots zeigen",
	"Bots verstecken",
	"Angemeldete Benutzer zeigen",
	"Angemeldete Benutzer verstecken",
	"Kleine Änderungen zeigen",
	"Kleine Änderungen verstecken",
	"Feedback",
	"Legende: N = neu, B = Bot, K = kleine Änderung, 0-9 = Zahl der Reverts in den letzten 24 Std",
	"Unterschiede",
	"Diskussion",
	"Versionen",
	"Benutzerbeiträge",
	"verschoben nach",
	"N", "B", "K"
};

std::string messages_ja[] = {
	"ボットを表示する",
	"ボットを隠す",
	"ログインユーザを表示する",
	"ログインユーザを隠す",
	"細部の編集を表示する",
	"細部の編集を隠す",
	"フィードバックを書く",
	"記号: N = 新しい記事; B = ボット; m = 細部の編集; 0-9 最近24時間のリバート数",
	"差分",
	"ノート",
	"履歴",
	"投稿記録",
	"moved to",
	"N", "B", "m"
};

std::string *messages = messages_en;

#define M_SHOW_BOTS 0
#define M_HIDE_BOTS 1
#define M_SHOW_LOGGED 2
#define M_HIDE_LOGGED 3
#define M_SHOW_MINOR 4
#define M_HIDE_MINOR 5
#define M_FEEDBACK 6
#define M_KEY 7
#define M_DIFF 8
#define M_TALK 9
#define M_HIST 10
#define M_CONTRIBS 11
#define M_MOVED_TO 12
#define M_NEW 13
#define M_BOT 14
#define M_MINOR 15


std::string db;
MYSQL mysql;

template<class to, class from>
to lexical_cast(from const& f)
{
	std::stringstream ss;
	to t;
	ss << f;
	ss >> t;
	return t;
}

std::string spaces(std::string const& s) {
	std::string res;
	for (std::string::const_iterator i = s.begin(), end = s.end(); i != end; ++i)
		switch(*i) {
			case '_': res += " "; break;
			default: res += *i; break;
		}
	return res;
}

std::string underscores(std::string const& s) {
	std::string res;
	for (std::string::const_iterator i = s.begin(), end = s.end(); i != end; ++i)
		switch(*i) {
			case ' ': res += "_"; break;
			default: res += *i; break;
		}
	return res;
}

std::string sqlsafe(std::string const& s) {
	std::string res;
	for (std::string::const_iterator i = s.begin(), end = s.end(); i != end; ++i)
		switch(*i) {
			case '\\': res += "\\\\"; break;
			case '\'': res += "\\'"; break;
			case '"': res += "\\\""; break;
			default: res += *i; break;
		}
	return res;
}

void
mysql_query_ordie(MYSQL* mysql, std::string const& query)
{
	int i = mysql_query(mysql, query.c_str());
	if (i) {
		printf("mysql query failed: %s\n", mysql_error(mysql));
		exit(8);
	}
}

std::string namespaces[] = {
	"",
	"Talk:",
	"User:",
	"User_talk:",
	"Wikipedia:",
	"Wikipedia_talk:",
	"Image:",
	"Image_talk:",
	"MediaWiki:",
	"MediaWiki_talk:",
	"Template:",
	"Template_talk:",
	"Help:",
	"Help_talk:",
	"Category:",
	"Category_talk:"
};

std::string ns2name(int ns) {
	if (ns < -2 || ns > 15) return "Unknown namespace:";
	mysql_query_ordie(&mysql, "SELECT ns_name FROM katesdb.ns_name WHERE ns_num = " + lexical_cast<std::string>(ns)
			+ " AND ns_db = '" + sqlsafe(db) + "'");
	MYSQL_RES *res = mysql_store_result(&mysql);
	MYSQL_ROW arow;
	if (arow = mysql_fetch_row(res))
		return mysql_free_result(res), arow[0];
	mysql_free_result(res);
	if (ns < 0) {
		if (ns == -1) return "Special:";
		else if (ns == -2) return "Media:";
		else return "[Unknown namespace]:";
	}
	return namespaces[ns];
}

int name2ns(std::string const& name) {
	if (name == "") return 0;
	mysql_query_ordie(&mysql, "SELECT ns_num FROM katesdb.ns_name WHERE ns_name = '" + sqlsafe(name)
			+ "' AND ns_db = '" + sqlsafe(db) + "'");
	MYSQL_RES *res = mysql_store_result(&mysql);
	MYSQL_ROW arow;
	if (arow = mysql_fetch_row(res))
		return mysql_free_result(res), lexical_cast<int>(arow[0]);
	mysql_free_result(res);
	for (unsigned int i = 0; i < sizeof namespaces / sizeof *namespaces; ++i)
		if (name + ":" == namespaces[i]) return i;
	return 0;
}

std::string fmtdate(std::string const& date) {
	return date.substr(8, 2) + ":" + date.substr(10, 2) + ":" + date.substr(12, 2);
}

std::string htmlsafe(std::string const& s) {
	std::string res;
	for (std::string::const_iterator i = s.begin(), end = s.end(); i != end; ++i) {
		switch(*i) {
			case '&': res += "&amp;"; break;
			case '<': res += "&lt;"; break;
			case '>': res += "&gt;"; break;
			case '"': res += "&#34;"; break;
			case '\'': res += "&#39;"; break;
			case '?': res += "&#63;"; break;
			default: res += *i; break;
		}
	}
	return res;
}

std::string urlsafe(std::string const& s) {
	std::string res;
	for (std::string::const_iterator i = s.begin(), end = s.end(); i != end; ++i) {
		if ((*i >= 'a' && *i <= 'z') || (*i >= 'A' && *i <= 'Z') || (*i >= '0' && *i <= '9') || strchr("/:.", *i))
			res += *i;
		else {
			res += '%';
			char *s; asprintf(&s, "%2x", (unsigned int)(unsigned char)*i);
			res += s;
			std::free(s);
		}
	}
	return res;
}


std::string
getdbname()
{
	char *e;
	if ((e = getenv("QUERY_STRING")) == NULL) return "enwiki";
	std::string s = e;
	std::string d = "enwiki", lang;
	std::string thispar;
	std::string *thisval;
	/* foo=bar&baz=quux */
	int doing = 0; /* 0 = name / 1 = par */
	for (std::string::const_iterator i = s.begin(), end = s.end(); i != end; ++i) {
		switch (*i) {
			case '=':
				doing = 1;
				if (thispar == "d") thisval = &d;
				else thisval = &lang;
				*thisval = "";
				thispar = "";
				break;
			case '&':
				doing = 0;
				thispar = "";
				break;
			default:
				if (doing == 0) {
					if (thispar.size() > 255) {
						std::cout << "Content-Type: text/plain\r\n\r\n...\r\n";
						std::exit(0);
					}
					thispar += *i;
				} else {
					if (thisval->size() > 255) {
						std::cout << "Content-Type: text/plain\r\n\r\n...\r\n";
						std::exit(0);
					}
					*thisval += *i;
				}
		}
	}
	//if (s.substr(0, 2) == "d=")
	//	d = s.substr(2);
	//else
	//	return "enwiki";
	if (lang == "ja") messages = messages_ja;
	else if (lang == "de") messages = messages_de;
	else if (lang == "it") messages = messages_it;
	else if (lang == "fi") messages = messages_fi;
	else if (lang == "nl") messages = messages_nl;
	else if (lang == "fr") messages = messages_fr;
	else if (lang == "sv") messages = messages_sv;
	std::ifstream f("/home/wikipedia/common/all.dblist");
	while (std::getline(f, s))
		if (s == d)
			return d;
	return "";
}

std::string latin1dbs[] = {
	"enwiki", "dawiki", "svwiki", "nlwiki"
};

std::string
getencoding(std::string const& db) {
	for (int i = 0; i < 4; ++i)
		if (db == latin1dbs[i])
			return "ISO_8859-1";
	return "UTF-8";
}

std::string
maybeutf8(std::string const& s) {
	if (getencoding(db) == "UTF-8")
		return s;
	char const *q = s.c_str(), *t = q;
	size_t qs = s.size();
	static bool init = false;
	static iconv_t ic;
	if (!init) {
		++init;
		ic = iconv_open("UTF-8", getencoding(db).c_str());
	}
	size_t rs = s.size() * 3;
	char *r = new char[rs + 1], *z = r;
	size_t i = iconv(ic, const_cast<char**>(&t), &qs, &z, &rs);
	std::string p(r, z);
	delete[] r;
	if (i == (size_t)-1) {
		return "!Conversion error: " + std::string(strerror(errno));
	}
	return p;
}
		
char const* projects[][2] = {
	{"wiki",		"wikipedia.org"},
	{"wiktionary",		"wiktionary.org"},
	{"wikibooks",		"wikibooks.org"},
	{"wikiquote",		"wikiquote.org"},
	{"wikinews",		"wikinews.org"}
};

std::string
gethost(std::string const& db)
{
	if (db == "commonswiki") return "commons.wikimedia.org";
	for (unsigned int i = 0; i < sizeof projects / sizeof *projects; ++i)
		if (db.substr(db.size() - strlen(projects[i][0])) == projects[i][0])
			return db.substr(0, db.size() - strlen(projects[i][0])) + "." + projects[i][1];
	return "unknown";
}

std::string sane(std::string const& article) {
	return article[0] == ':' ? article.substr(1) : article;
}

bool articleexists(int ns, std::string article)
{
	if (ns < 0) return true;
	mysql_query_ordie(&mysql, "SELECT 1 FROM cur WHERE cur_namespace="+lexical_cast<std::string>(ns)+
			" AND cur_title='"+sqlsafe(underscores(sane(article)))+"'");
	MYSQL_RES *res = mysql_store_result(&mysql);
	if (mysql_fetch_row(res))
		return mysql_free_result(res), true;
	mysql_free_result(res);
	return false;
}

std::string
fmtart(int ns, std::string link, std::string desc)
{
	link = sane(link);
	std::string clas = articleexists(ns, link) ? "article" : "new";
	return "<a href='http://"+gethost(db)+"/wiki/"+urlsafe(ns2name(ns)+link)+"' class='"+clas+"'>"+
		htmlsafe(desc.size()?desc:spaces(link))+"</a>";
}

std::string
link2url(std::string link, bool safe=true)
{
	link = sane(link);
	std::string art = link, desc;
	std::string::size_type i = art.find('|');
	if (i != art.npos) {
		desc = spaces((i+1 == art.size() ? art : art.substr(i + 1)));
		art = art.substr(0, i);
		if (desc == "") desc = art;
	} else desc = art;
	std::string titlebit;
	int ns = 0;
	std::string q = sane(underscores(art));
	i = q.find(':');
	if (i != q.npos)
		ns = name2ns((i+1 == q.size() ? q : q.substr(0, i)));
	std::string clas = articleexists(ns, q.substr(i==q.npos?0:i+1)) ? "article" : "new";
	return "<a href='http://"+gethost(db)+"/wiki/"+
		(safe?urlsafe(q):q)+"' class='"+clas+"'>"+(safe?htmlsafe(desc):desc)+"</a>";
}

std::string 
parsesummary(std::string s)
{
	std::string::size_type i,j;
	while ((i = s.find("[[")) != s.npos) {
		if ((j = s.find("]]", i)) == s.npos) break;
		s = s.substr(0, i) + link2url(s.substr(i + 2, j - i - 2), false) + s.substr(j + 2);
	}
	while (i = s.find("/* ") != s.npos) {
		if ((j = s.find(" */", i)) == s.npos) break;
		s = s.substr(0, i - 1) + "<span style='color: gray'>" + s.substr(i + 2, j - i - 2) + "</span>" 
			+ s.substr(j + 3);
	}
	return s;
}

std::string
whatwasthedateyesterday() {
	std::time_t t = std::time(NULL);
	t -= 86400;
	struct tm *gt = gmtime(&t);
	char b[256];
	std::strftime(b, sizeof b, "%Y%m%d%H%M%S", gt);
	return b;
}

template<typename it>
it next(it i)
{
	return ++i;
}

std::string
mkrvtxt(std::set<std::string> const& rvers)
{
	if (rvers.empty()) return "";
	std::string s = " [";
	for (std::set<std::string>::const_iterator it = rvers.begin(), end = rvers.end(); it != end; ++it) {
		s += fmtart(2, *it, *it);
		if (next(it) != end)
			s += ", ";
	}
	return s + "]";
}

int
main(int argc, char *argv[])
{
	db = getdbname();

	if (db.empty()) {
		std::cout << "Content-Type: text/plain\r\n\r\nincorrect dbname predicts future losses\n";
		return 0;
	}

	mysql_init(&mysql);
	mysql_options(&mysql, MYSQL_READ_DEFAULT_GROUP, "rcdumper");
 
 	std::string dbhost, dbuser, dbpass, s;
	std::vector<std::string> v;
	std::ifstream f("/home/kate/dbconfig");
	while (std::getline(f, s)) {
		v.push_back(s);
	}
	f.close();
	if (v.size() < 3) {
		std::cerr << "Content-Type: text/plain\r\n\r\nsorry, broken\r\n"; 
		std::exit(0);
	}
	dbhost = v[0];
	dbuser = v[1];
	dbpass = v[2];

	if (!mysql_real_connect(&mysql, dbhost.c_str(), dbuser.c_str(), dbpass.c_str(), db.c_str(), 0, NULL, 0)) {
		printf("mysql connect error: %s\n", mysql_error(&mysql));
		return 1;
	}

	//std::cout << "HTTP/1.0 200 OK\r\n" << std::flush;
	std::cout << "Content-type: text/html; charset=UTF-8\r\n" << std::flush;
	std::cout << "\r\n" << std::flush;
	std::cout << "<html><head><title>live recent changes feed</title>"
"<style type='text/css'>"
"body { padding: 0em; margin: 0em; }"
"a { text-decoration: none; }"
"a.new { color: red; }"
"</style>"
"<script language='javascript'>"
"var showbots = false;"
"var showminor = true;"
"var showlogin = true;"
"function toggleminor() {"
" showminor = !showminor;"
" v = document.getElementById('minortog');"
" if (showminor) v.innerHTML = '"+messages[M_HIDE_MINOR]+"';"
" else v.innerHTML = '"+messages[M_SHOW_MINOR]+"';"
"}"
"function togglelogin() {"
" showlogin = !showlogin;"
" v = document.getElementById('logintog');"
" if (showlogin) v.innerHTML = '"+messages[M_HIDE_LOGGED]+"';"
" else v.innerHTML = '"+messages[M_SHOW_LOGGED]+"';"
"}"
"function togglebots() {"
" showbots = !showbots;"
" v = document.getElementById('bottog');"
" if (showbots) v.innerHTML = '"+messages[M_HIDE_BOTS]+"';"
" else v.innerHTML = '"+messages[M_SHOW_BOTS]+"';"
"}"
"</script>"
"</head><body><ul>\n"
"<div style='width: 90%; height: 8%; padding-left: 2em; padding-right: 2em; border-bottom: solid 1px black; text-align: center'>"
"<a id='bottog' href='javascript:togglebots()'>"+messages[M_SHOW_BOTS]+"</a> "
"| <a id='logintog' href='javascript:togglelogin()'>"+messages[M_HIDE_LOGGED]+"</a> "
"| <a id='minortog' href='javascript:toggleminor()'>"+messages[M_HIDE_MINOR]+"</a>"
"| <a id='feedback' href='http://meta.wikimedia.org/wiki/Realtime_recent_changes'>"+messages[M_FEEDBACK]+"</a>"
"<br/>" +messages[M_KEY]+ "</div>"
"<div style='padding-left: 2em; padding-right: 2em; height: 85%; overflow: scroll;' id='contentbit'>\n"
	<< std::flush;

	mysql_query_ordie(&mysql, "SELECT MAX(rc_id) FROM recentchanges");
	MYSQL_ROW arow;
	MYSQL_RES *res;
	res = mysql_store_result(&mysql);
	arow = mysql_fetch_row(res);
	std::string lastrcid = arow[0];
	mysql_free_result(res);

	while (true) {
		mysql_query_ordie(&mysql, "SELECT rc_id, rc_timestamp, rc_user_text, rc_namespace, rc_title, "
		"rc_comment, rc_bot, rc_minor, rc_new, rc_user, rc_this_oldid, rc_cur_id, rc_moved_to_ns, "
		"rc_moved_to_title,cur_text "
		"FROM recentchanges LEFT OUTER JOIN cur ON cur_id=rc_cur_id WHERE rc_id > " + lastrcid + " ORDER BY rc_id");
		res = mysql_store_result(&mysql);
		while (arow = mysql_fetch_row(res)) {
			std::string	rc_timestamp	= arow[1],
					rc_user_text	= arow[2],
					rc_comment	= arow[5],
					rc_cur_id	= arow[11],
					rc_moved_title	= arow[13] ? arow[13] : "",
					cur_text	= arow[14] ? arow[14] : "",
					rc_title	= arow[4];
			int		rc_namespace	= atoi(arow[3]),
					rc_bot		= atoi(arow[6]),
					rc_new		= atoi(arow[8]),
					rc_user		= atoi(arow[9]),
					rc_oldid	= atoi(arow[10]),
					rc_minor_edit	= atoi(arow[7]),
					rc_moved_ns	= arow[12] ? atoi(arow[12]) : 0;
			mysql_query_ordie(&mysql, "SELECT COUNT(*) FROM hashs WHERE hs_nstitle=MD5(CONCAT('"
				+ lexical_cast<std::string>(rc_namespace) + "','+','"
				+ sqlsafe(rc_title) + "')) AND hs_hash=MD5('"
				+ sqlsafe(cur_text) + "') AND hs_timestamp > " + whatwasthedateyesterday());
			MYSQL_RES *res2 = mysql_store_result(&mysql);
			MYSQL_ROW brow = mysql_fetch_row(res2);
			int 		rc_reverts	= atoi(brow[0]);
			if (rc_reverts > 9) rc_reverts = 9;
			mysql_free_result(res2);
			std::set<std::string> reverters;
			mysql_query_ordie(&mysql, "SELECT DISTINCT hs_user_text FROM hashs WHERE hs_nstitle=MD5(CONCAT('"
				+ lexical_cast<std::string>(rc_namespace) + "','+','"
				+ sqlsafe(rc_title) + "')) AND hs_hash=MD5('"
				+ sqlsafe(cur_text) + "') AND hs_timestamp > " + whatwasthedateyesterday());
			res2 = mysql_store_result(&mysql);
			while (brow = mysql_fetch_row(res2)) {
				if (reverters.find(brow[0]) == reverters.end())
					reverters.insert(brow[0]);
			}
			std::string	reverter_text	= mkrvtxt(reverters);
			mysql_free_result(res2);

			lastrcid = arow[0];
			if (rc_bot) std::cout << 
				"<script language='javascript'>"
				"if (showbots == false) document.write('<span style=\"display:none\">')</script>";
			if (rc_minor_edit) std::cout << 
				"<script language='javascript'>"
				"if (showminor == false) document.write('<span style=\"display:none\">')</script>";
			if (rc_user != 0) std::cout << 
				"<script language='javascript'>"
				"if (showlogin == false) document.write('<span style=\"display:none\">')</script>";
			std::cout << fmtdate(rc_timestamp) + " "
				  "<tt>"
				+ std::string(rc_minor_edit ? messages[M_MINOR] : "&nbsp;")
				+ std::string(rc_bot ? messages[M_BOT] : "&nbsp;")
				+ std::string(rc_new ? messages[M_NEW] : "&nbsp;")
				+ std::string(rc_reverts ? lexical_cast<std::string>(rc_reverts) : "&nbsp;")
				+ "&nbsp;</tt>"
				  "<a href='http://"+gethost(db)+"/wiki/"
				+ urlsafe(ns2name(rc_namespace) + rc_title)
				+ "'>" + spaces(maybeutf8(ns2name(rc_namespace) + htmlsafe(rc_title))) + "</a>" 
				+ std::string(" (")
				+ (rc_moved_title.size()
				   ? (messages[M_MOVED_TO] + " " +
				      fmtart(rc_moved_ns, rc_moved_title, maybeutf8(ns2name(rc_moved_ns) + rc_moved_title)))
				   : (!rc_new ?
				      ("<a href='http://"+gethost(db)+"/wiki/"
				       + urlsafe(ns2name(rc_namespace) + rc_title)
				       + "?curid="+rc_cur_id+"&amp;diff=0'>" + messages[M_DIFF] + "</a>")
				     :(messages[M_DIFF]))
				  )
				+ "; "
				"<a href='http://"+gethost(db)+"/wiki/"
				+ urlsafe(ns2name(rc_namespace) + rc_title)
				+ "?action=history'>"+messages[M_HIST]+"</a>) . . "
				+ fmtart(2, rc_user_text, maybeutf8(rc_user_text))
				+ " (" + fmtart(3, rc_user_text, messages[M_TALK]) + " | "
				+ fmtart(-1, "Contributions/" + underscores(rc_user_text), messages[M_CONTRIBS]) + ")"
				+ ((rc_moved_title.empty()  && !rc_comment.empty())
					? " (" + parsesummary(htmlsafe(rc_comment)) + ")"
					: std::string()
				  )
				+ maybeutf8(reverter_text)
				+ "<br/>\n" << std::endl << std::flush;
			if (rc_user != 0) std::cout << 
				"<script language='javascript'>if (showlogin == false) document.write('</span>')</script>\n";
			if (rc_minor_edit) std::cout << 
				"<script language='javascript'>if (showminor == false) document.write('</span>')</script>\n";
			if (rc_bot) std::cout << 
				"<script language='javascript'>if (showbots == false) document.write('</span>')</script>\n";
			std::cout << 
"<script language='javascript'>"
"  var v = document.getElementById('contentbit');"
"  v.scrollTop = v.scrollTop + 100;"
"</script>\n" << std::flush;
		}
		mysql_free_result(res);
		usleep(250000);
	}
}
