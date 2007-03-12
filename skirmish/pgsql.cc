#include <iostream>
#include <boost/lexical_cast.hpp>
#include <boost/format.hpp>
#include <unistd.h>
#include <libpq-fe.h>

#include "pgsql.h"

namespace postgres {

connection::connection(std::string const &desc)
	: conn(0)
{
	/* desc is "user[/password]@host[/database]" */
	std::string::size_type i;
	std::string userpart, dbpart;
	std::string d;

	d = desc.substr(desc.find(':') + 1);

	if ((i = d.find('@')) != std::string::npos) {
		userpart = d.substr(0, i);
		dbpart = d.substr(i + 1);
	} else
		userpart = d;

	if ((i = userpart.find('/')) != std::string::npos) {
		user = userpart.substr(0, i);
		password = userpart.substr(i + 1);
	} else {
		user = userpart;
		char *p = getpass("Enter password: ");
		if (p)
			password = p;
	}

	if ((i = dbpart.find('/')) != std::string::npos) {
		host = dbpart.substr(0, i);
		db = dbpart.substr(i + 1);
	} else
		host = dbpart;	
}

connection::~connection()
{
	if (conn)
		close();
}

void
connection::open(void)
{
	std::string connstr;
	if (!db.empty())
		connstr += str(boost::format("dbname='%s' ") % db);
	if (!host.empty())
		connstr += str(boost::format("host='%s' ") % host);
	if (!user.empty())
		connstr += str(boost::format("user='%s' ") % user);
	if (!password.empty())
		connstr += str(boost::format("password='%s' ") % password);

	conn = PQconnectdb(connstr.c_str());
	if (PQstatus(conn) == CONNECTION_OK)
		return;
	err = PQerrorMessage(conn);
	PQfinish(conn);
	conn = 0;
	throw db::error(err);
}

void
connection::close(void)
{
	PQfinish(conn);
	conn = 0;
}

std::string
connection::error(void)
{
	return err;
}

execution_result *
connection::execute_sql(std::string const &sql)
{
	assert(conn);
	PGresult *res;
	if ((res = PQexec(conn, sql.c_str())) == NULL)
		throw db::error(PQerrorMessage(conn));

	switch (PQresultStatus(res)) {
		case PGRES_COMMAND_OK:
		case PGRES_TUPLES_OK:
			return new execution_result(conn, res);
		default:
			throw db::error(PQresultErrorMessage(res));
	}
}

execution_result::execution_result(PGconn *c, PGresult *res)
	: conn(c)
	, res(res)
	, row(0)
{
	if (PQresultStatus(res) == PGRES_COMMAND_OK)
		return;

	int nfields = PQnfields(res);
	for (int i = 0; i < nfields; ++i)
		names.push_back(PQfname(res, i));
}

execution_result::~execution_result()
{
	if (res)
		PQclear(res);
}

bool
execution_result::has_data(void)
{
	return PQresultStatus(res) != PGRES_COMMAND_OK;
}

int
execution_result::num_fields(void)
{
	return PQnfields(res);
}

int
execution_result::affected_rows(void)
{
	return boost::lexical_cast<int>(PQcmdTuples(res));
}

result_row *
execution_result::next_row(void)
{
	if (row == PQntuples(res))
		return NULL;

	return new result_row(this, res, row++);
}

result_row::result_row(execution_result *er, PGresult *res, int row)
	: row(row)
	, res(res)
	, er(er)
{
}

std::string
result_row::string_value(int col)
{
	if (PQgetisnull(res, row, col))
		return "NULL";
	return PQgetvalue(res, row, col);

}

std::string
execution_result::field_name(int col)
{
	return names[col];
}

} // namespace pgsql
