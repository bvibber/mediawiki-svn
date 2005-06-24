/* $Header$ */

#include "smstdinc.hxx"
#include "smmysql.hxx"
#include "smcfg.hxx"

#include <mysql/mysql.h>

std::map<std::string, mysqlclientp> mysqlclient::clients;

mysqlclient::mysqlclient(str host_, int port_)
: connected(false)
, host(host_)
, port(port_)
{}
	
mysqlclient::~mysqlclient() {
	if (connected)
		mysql_close(&connection);
}
	
void
mysqlclient::connect(void) {
	mysql_init(&connection);
	mysql_options(&connection, MYSQL_READ_DEFAULT_GROUP, "servmon");
	unsigned int tm = 5;
	mysql_options(&connection, MYSQL_OPT_CONNECT_TIMEOUT, reinterpret_cast<char const *>(&tm));
	std::string user, pass;
	try {
		user = SMI(smcfg::cfg)->fetchstr("/monit/mysql/username");
		pass = SMI(smcfg::cfg)->fetchstr("/monit/mysql/password");
	} catch (smcfg::nokey&) {
		throw mysqlerr("username/password not specified");
	}
	if (!mysql_real_connect(&connection, host.c_str(), user.c_str(), pass.c_str(),
				NULL, port, NULL, 0)) {
		throw mysqlerr(mysql_error(&connection));
	}
	connected = true;
}

mysqlclient::resultset
mysqlclient::query(str query) {
	if (!connected)
		connect();
	int ret = mysql_real_query(&connection, query.c_str(), query.size());
	if (ret) {
		connected = false;
		std::string error = mysql_error(&connection);
		mysql_close(&connection);
		throw mysqlerr(error);
	}
	MYSQL_RES *res = mysql_store_result(&connection);
	if (!res) return resultset();
	MYSQL_ROW mr;
	MYSQL_FIELD *fields = mysql_fetch_fields(res);
	resultset resset;
	int numrows = mysql_num_fields(res);
	while ((mr = mysql_fetch_row(res)) != NULL) {
		row r;
		unsigned long *lengths = mysql_fetch_lengths(res);
		for (int i = 0; i < numrows; ++i) {
			std::string cname = fields[i].name;
			std::string data;
			if (mr[i])
				data.assign(mr[i], mr[i] + lengths[i]);
			r[cname] = data;
		}
		resset.push_back(r);
	}
			
	mysql_free_result(res);
	return resset;
}

mysqlclientp
mysqlclient::forhost(str host)
{
	std::map<std::string, mysqlclientp>::iterator it = clients.find(host);
	if (it != clients.end()) return it->second;
	return clients[host] = mysqlclientp(new mysqlclient(host));
}
