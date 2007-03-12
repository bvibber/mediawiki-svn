#include <boost/format.hpp>
#include <mysql.h>

#include "mysqldb.h"

namespace mysql {

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
	conn = new MYSQL;
	mysql_init(conn);

	if (mysql_real_connect(conn, 
			host.empty() ? NULL : host.c_str(),
			user.empty() ? NULL : user.c_str(),
			password.empty() ? NULL : password.c_str(),
			db.empty() ? NULL :db.c_str(), 0, NULL, 0) == NULL) {
		err = mysql_error(conn);
		delete conn;
		conn = 0;
		throw db::error(err);
	}
}

void
connection::close(void)
{
	mysql_close(conn);
	delete conn;
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
	if (mysql_real_query(conn, sql.data(), sql.size()) != 0)
		throw db::error(mysql_error(conn));

	return new execution_result(conn);
}

execution_result::execution_result(MYSQL *c)
	: conn(c)
	, res(0)
{
	if (!mysql_field_count(c))
		return;

	if ((res = mysql_use_result(conn)) == NULL)
		throw db::error(mysql_error(conn));

	MYSQL_FIELD *f;
	while (f = mysql_fetch_field(res)) {
		names.push_back(f->name);
	}
}

execution_result::~execution_result()
{
	if (res)
		mysql_free_result(res);
}

bool
execution_result::has_data(void)
{
	return mysql_field_count(conn);
}

int
execution_result::num_fields(void)
{
	return mysql_field_count(conn);
}

int
execution_result::affected_rows(void)
{
	return mysql_affected_rows(conn);
}

result_row *
execution_result::next_row(void)
{
	MYSQL_ROW r;

	if ((r = mysql_fetch_row(res)) == NULL)
		return NULL;

	return new result_row(this, r);
}

result_row::result_row(execution_result *er, MYSQL_ROW row)
	: row(row)
	, er(er)
{
}

std::string
result_row::string_value(int col)
{
	if (row[col])
		return row[col];

	return "NULL";
}

std::string
execution_result::field_name(int col)
{
	return names[col];
}

std::vector<db::table>
connection::describe_tables(std::string const &schema)
{
	std::string query = "SHOW TABLES";
	if (!schema.empty())
		query += str(boost::format(" FROM `%s`") % schema);

	if (mysql_real_query(conn, query.c_str(), query.size()) != 0)
		throw db::error(mysql_error(conn));

	MYSQL_RES *res;
	if ((res = mysql_use_result(conn)) == NULL)
		throw db::error(mysql_error(conn));

	std::vector<std::string> names;

	std::vector<db::table> ret;
	MYSQL_ROW r;
	while (r = mysql_fetch_row(res)) {
		names.push_back(r[0]);
	}
	mysql_free_result(res);

	for (int i = 0; i < names.size(); ++i) {
		ret.push_back(describe_table(schema, names[i]));
	}

	return ret;
}

db::table
connection::describe_table(std::string const &schema, std::string const &table)
{
	std::string query;
	if (schema.empty())
		query = str(boost::format("DESCRIBE `%s`") % table);
	else
		query = str(boost::format("DESCRIBE `%s`.`%s`") % schema % table);

	if (mysql_real_query(conn, query.c_str(), query.size()) != 0)
		throw db::error(mysql_error(conn));

	MYSQL_RES *res;
	if ((res = mysql_use_result(conn)) == NULL)
		throw db::error(mysql_error(conn));

	db::table ret;
	ret.name = table;

	MYSQL_ROW r;
	while (r = mysql_fetch_row(res)) {
		db::column c;
		c.name = r[0];
		c.type = r[1];
		c.nullable = !strcmp(r[2], "YES");
		ret.columns.push_back(c);
	}

	mysql_free_result(res);
	return ret;
}


} // namespace mysql
