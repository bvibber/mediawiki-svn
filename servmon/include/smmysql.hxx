/* $Header$ */

#ifndef SMMYSQL_H
#define SMMYSQL_H

#include "smstdinc.hxx"
#include "smutl.hxx"

#include <mysql/mysql.h>

class mysqlclient;
typedef b::shared_ptr<mysqlclient> mysqlclientp;

struct mysqlerr : public std::runtime_error {
	mysqlerr(str s) : std::runtime_error(s) {}
};
	
class mysqlclient {
public:
	typedef std::map<std::string, std::string> row;
	typedef std::vector<row> resultset;

	mysqlclient(str host_, int port_ = 0);
	~mysqlclient();

	void connect(void);
	resultset query(str query);

	static mysqlclientp forhost(str host);

private:
	static std::map<std::string, mysqlclientp> clients;
	bool connected;
	
	std::string host;
	int port;
	MYSQL connection;
};

#endif
