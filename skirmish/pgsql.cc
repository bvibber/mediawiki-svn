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

db::resultptr
connection::execute_sql(std::string const &sql)
{
	assert(conn);
	db::resultptr r = prepare_sql(sql);
	r->execute();
	return r;
}

db::resultptr
connection::prepare_sql(std::string const &sql)
{
	return db::resultptr(new result(conn, sql));
}

result::result(PGconn *c, std::string const &sql)
	: conn(c)
	, row(0)
	, sql(sql)
{
}

void
result::execute(void)
{
	PGresult *res;
	if ((res = PQexec(conn, sql.c_str())) == NULL)
		throw db::error(PQerrorMessage(conn));

	switch (PQresultStatus(res)) {
		case PGRES_COMMAND_OK:
			return;
		case PGRES_TUPLES_OK:
			break;
		default:
			throw db::error(PQresultErrorMessage(res));
	}

	int nfields = PQnfields(res);
	for (int i = 0; i < nfields; ++i)
		names.push_back(PQfname(res, i));
}

void
result::bind(std::string const &, std::string const &)
{
	throw db::error("bound variables not supported for PostgreSQL");
}

result::~result()
{
	if (res)
		PQclear(res);
}

bool
result::empty(void) 
{
	return PQresultStatus(res) != PGRES_TUPLES_OK;
}

int
result::num_fields(void)
{
	return PQnfields(res);
}

int
result::affected_rows(void)
{
	return boost::lexical_cast<int>(PQcmdTuples(res));
}

result_row *
result::next_row(void)
{
	if (row == PQntuples(res))
		return NULL;

	return new result_row(this, res, row++);
}

result_row::result_row(result *er, PGresult *res, int row)
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
result::field_name(int col)
{
	return names[col];
}

std::vector<db::table>
connection::describe_tables(std::string const &schema)
{
	PGresult *r;
	if (schema.empty()) {
		std::string query = "select nspname, relname from pg_namespace, pg_class where relnamespace = pg_namespace.oid and relkind='r'";
		r = PQexec(conn, query.c_str());
	} else {
		std::string query = 
			"select nspname, relname from pg_namespace, pg_class where relnamespace = pg_namespace.oid and relkind='r' "
			"and nspname=$1";
		r = PQprepare(conn, "", query.c_str(), 1, NULL);
		if (PQresultStatus(r) != PGRES_COMMAND_OK)
			throw db::error(PQerrorMessage(conn));

		char const *params[] = {
			schema.c_str()
		};
		r = PQexecPrepared(conn, "", 1, params, NULL, NULL, 0);
	}

	if (PQresultStatus(r) != PGRES_TUPLES_OK)
		throw db::error(PQerrorMessage(conn));

	int nrows = PQntuples(r);

	std::vector<std::pair<std::string, std::string> > names;
	for (int i = 0; i < nrows; ++i) {
		names.push_back(std::pair<std::string, std::string>(
			PQgetvalue(r, i, 0), PQgetvalue(r, i, 1)));
	}
	PQclear(r);

	std::vector<db::table> res;
	for (int i = 0; i < names.size(); ++i) {
		res.push_back(describe_table(names[i].first, names[i].second));
	}

	return res;
}

db::table
connection::describe_table(std::string const &schema, std::string const &table)
{
	db::table t;
	t.schema = schema;
	t.name = table;

	std::string query =
		"select attname, typname, attnotnull from pg_namespace, pg_class, pg_attribute, pg_type "
		"where pg_class.relnamespace=pg_namespace.oid and pg_attribute.attrelid=pg_class.oid and pg_type.oid=atttypid "
		"and nspname=$1 and relname=$2 and attnum > 0";
	char const *params[] = {
		schema.c_str(),
		table.c_str()
	};

	int lengths[2] = {};
	PGresult *r = PQprepare(conn, "", query.c_str(), 2, NULL);
	if (PQresultStatus(r) != PGRES_COMMAND_OK)
		throw db::error(PQerrorMessage(conn));

	r = PQexecPrepared(conn, "", 2, params, lengths, NULL, 0);
	if (PQresultStatus(r) != PGRES_TUPLES_OK)
		throw db::error(PQerrorMessage(conn));

	int nrows = PQntuples(r);
	for (int i = 0; i < nrows; ++i) {
		db::column c;
		c.name = PQgetvalue(r, i, 0);
		c.type = PQgetvalue(r, i, 1);
		c.nullable = !strcmp(PQgetvalue(r, i, 2), "f");
		t.columns.push_back(c);
	}
	PQclear(r);

	if (t.columns.empty())
		throw db::error("table does not exist");
	return t;
}

} // namespace pgsql
