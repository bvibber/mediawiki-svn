/*
 * Display Oracle process list.
 * $Id$
 */

#include <iostream>
#include <iomanip>
#include <string>
#include <exception>
#include <sstream>
using std::stringstream;
using std::cout;
using std::cerr;
using std::string;
using std::setw;
using std::left;
using std::exception;

#include <sys/filio.h>
#include <unistd.h>
#include <signal.h>

#include <oci.h>
#include <occi.h>
using namespace oracle::occi;

#include "curswrap.h"

extern "C" void sig_exit	(int);
static void	handle_command	(void);
static void	clear_body	(void);
static void	clear_status	(void);
static string	read_input	(const string& prompt);
static void	set_status	(const string& text);
static string	fmttime		(unsigned long long etime);
static string	get_uptime	(void);

static string username, password, connid;
static string user_filter;
static bool do_exit = false;
static int update_delay = 1;
static bool show_nonactive = true;

static Environment *env;
static Connection *conn;
static Statement *stmt;
static ResultSet *rs;

int
main(int argc, char *argv[])
try
{
	int		 row = 0;
	OCIError	*errh;

	if (argc < 2) {
		cerr << "usage: " << argv[0] << " <username>[@connect identifier] [password]\n";
		return 1;
	}

	username = argv[1];
	string::size_type i;
	if ((i = username.find("@")) != string::npos) {
		connid = username.substr(i + 1);
		username = username.substr(0, i);
	}
	if (argv[2])
		password = argv[2];
	else
		password = getpass("* Enter password: ");

	cout << "Connecting to " << username << '@' << connid << "...\n";

	env = Environment::createEnvironment();
	conn = env->createConnection(username, password, connid);

	signal(SIGINT, sig_exit);
	signal(SIGTERM, sig_exit);

	curs_initscr();
	curs_cbreak();
	curs_noecho();

	curs_move(row++, 0);
	char vers[256];
	OCIHandleAlloc(env->getOCIEnvironment(), (void **)&errh, OCI_HTYPE_ERROR, 0, NULL);
	OCIServerVersion(conn->getOCIServiceContext(), errh, (text*) vers, sizeof(vers),
			 OCI_HTYPE_SVCCTX);

	string vers_str = "Server: " + string(vers);
	curs_addstr(vers_str.c_str());
	curs_move(row++, 0);
	curs_addstr("SID      USERNAME  TIME   STS   COMMAND");
	curs_move(row++, 0);
	curs_addstr("===      ========  ====   ===   =======");

	for (;;) {
		stringstream	buf;
		string		line;
		string		sid, username, sql_text, status;
		string		uptime;
		unsigned int	etime;
		int		nrow = row;
		int		nchars = 0;

		clear_body();

		ioctl(0, FIONREAD, &nchars);
		if (nchars)
			handle_command();

		uptime = "Up: " + get_uptime();
		curs_move(0, curs_cols() - uptime.length());
		curs_addstr(uptime.c_str());

		stmt = conn->createStatement(
				"SELECT sid, username, elapsed_time, sql_text, status "
				"FROM v$session, v$sql "
				"WHERE v$session.sql_id = v$sql.sql_id"
				+ user_filter
			);

		rs = stmt->executeQuery();
		while (rs->next()) {
			sid = rs->getString(1);
			username = rs->getString(2);
			sql_text = rs->getString(4);
			etime = rs->getUInt(3);
			status = rs->getString(5).substr(0, 3);

			if (status != "ACT") {
				if (!show_nonactive)
					continue;
				etime = 0;
				sql_text = "";
			}

			buf.str("");

			buf << left << setw(8) << sid << ' '
			    << left << setw(9) << username << ' '
			    << left << setw(6) << fmttime(etime / 1000000ULL) << ' '
			    << left << setw(5) << status << ' '
			    << sql_text << '\n';
			line = buf.str().substr(0, curs_cols());
			curs_move(nrow++, 0);
			if (status == "ACT")
				curs_attron(CURS_A_BOLD);
			curs_addstr(line.c_str());
			curs_attroff(CURS_A_BOLD);
		}
		curs_refresh();

		stmt->closeResultSet(rs);
		conn->terminateStatement(stmt);

		sleep(update_delay);
		if (do_exit)
			break;
		clear_status();
	}
	curs_endwin();

	env->terminateConnection(conn);
	return 0;
} catch (exception& e) {
	curs_endwin();
	cerr << e.what() << '\n';
}

void
sig_exit(int)
{
	do_exit = true;
}

void
handle_command(void)
{
	int	c = curs_getch();
	string	input;

	switch (c) {
	case 'q':
		do_exit = true;
		break;
	case 'u':
		input = read_input("Username: ");
		if (!input.empty())
			user_filter = " AND username = '" + input + "'";
		else
			user_filter = "";
		break;
	case 's':
		input = read_input("Update delay: ");
		update_delay = atoi(input.c_str());
		break;
	case 'i':
		show_nonactive = !show_nonactive;
		if (show_nonactive)
			set_status("* Non-ACTIVE threads shown");
		else
			set_status("* Non-ACTIVE threads hidden");
		break;
	default:
		curs_move(curs_lines() - 1, 0);
		curs_addstr("* Unknown command.");
	}
}

static string
read_input(const string& prompt)
{
	char input[1024] = {0};

	curs_move(curs_lines() - 1, 0);
	curs_addstr(("* " + prompt).c_str());
	curs_echo();
	curs_getnstr(input, sizeof(input) - 1);
	curs_noecho();
	clear_status();
	return input;
}

static void
set_status(const string& text)
{
	curs_move(curs_lines() - 1, 0);
	curs_addstr(text.c_str());
}

static void
clear_body(void)
{
	for (int i = 3; i < curs_lines() - 1; ++i) {
		curs_move(i, 0);
		curs_clrtoeol();
	}
}

static void
clear_status(void)
{
	curs_move(curs_lines() - 1, 0);
	curs_clrtoeol();
}

static string
fmttime(unsigned long long etime)
{
	stringstream	buf;
	int		secs = 0, mins = 0, hrs = 0;

	hrs = etime / (60*60);
	etime %= 60;
	mins = etime / 60;
	etime %= 60;
	secs = etime;

	if (hrs)
		buf << hrs << 'h';
	if (mins)
		buf << mins << 'm';
	buf << secs << 's';
	return buf.str();
}

static string
get_uptime(void)
{
	Statement *stmt = conn->createStatement("select to_char(systimestamp - startup_time) from v$instance");
	ResultSet *rs = stmt->executeQuery();
	if (!rs->next())
		return "unknown";
	string r = rs->getString(1);
	stmt->closeResultSet(rs);
	conn->terminateStatement(stmt);
	return r;
}
