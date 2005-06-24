/* $Header$ */

#ifndef SMSERVER_H
#define SMSERVER_H

#include "smstdinc.hxx"
#include "smutl.hxx"
#include "smmysql.hxx"

class xomitr {
public:
	xomitr();
	uint32_t val(uint32_t newval);
private:
	uint32_t v;
	std::time_t l;
};

struct server {
	server(str name_);
	virtual std::string type(void) const = 0;
	void check(void);
	virtual void _check(void) = 0;
	void _stdchecks(void);
	virtual std::string fmt4irc(void) const = 0;
	virtual ~server() {}
	std::string name, cluster;
	enum state_t {
		state_up,        /* server is completely up                        */
		state_down,      /* server is completely down                      */
		state_fast_flap, /* server is partially down                       */
		state_slow_flap, /* server exceeded state transition flap interval */
		state_unknown    /* server state is not checked yet or we don't
				    know how to check it                           */
	};
	state_t state;
	int nups, ndowns;
	bool is(state_t s) const;
	int flapstate;
	void markup(void);
	void markdown(void);
	static std::string statestring(state_t s);
	std::set<std::time_t> flaps;
};

struct noneserver : public server {
	noneserver(str name) : server(name) {}
	std::string type(void) const { return "none"; };
	std::string fmt4irc(void) const { return ""; };
	void _check(void) {}
};

typedef b::shared_ptr<server> serverp;
struct squidserver : public server {
	squidserver(str name) : server(name), rpsv(0), hpsv(0) {}
	std::string type(void) const { return "Squid"; }
	std::string fmt4irc(void) const;
	void _check();
	xomitr rps, hps;
	uint32_t rpsv, hpsv;
};

struct mysqlserver : public server {
	mysqlserver(str name) : server(name), qpsv(0) {}
	std::string type(void) const { return "MySQL"; }
	std::string fmt4irc(void) const;
	void _check();
	xomitr qps;
	uint32_t qpsv, procv;
	std::time_t replag;

	uint32_t getqueries(void);
	uint32_t getnumprocesses(void);
	std::time_t getmasterpos(void);
	std::time_t getmypos(void);
	std::time_t getreplag(void);

	mysqlclientp getconn(void);
	mysqlclientp clnt;
};

#endif

