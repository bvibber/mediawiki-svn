#include <iostream>
#include <boost/lexical_cast.hpp>
#include <boost/format.hpp>
#include <boost/algorithm/string/case_conv.hpp>
#include <unistd.h>
#include <sql.h>
#include <sqlext.h>

#include "odbc.h"

namespace odbc {

connection::connection(std::string const &desc)
	: env(0)
	, dbc(0)
{
	/* desc is "user[/password]@SID" */
	std::string::size_type i;
	std::string userpart;
	std::string d;

	d = desc.substr(desc.find(':') + 1);

	if ((i = d.find('@')) != std::string::npos) {
		userpart = d.substr(0, i);
		sid = d.substr(i + 1);
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
}

connection::~connection()
{
	close();
}

void
connection::open(void)
{
	if (!SQL_SUCCEEDED(SQLAllocHandle(SQL_HANDLE_ENV, SQL_NULL_HANDLE, &env)))
		throw db::error("cannot create ODBC environment");

	if (!SQL_SUCCEEDED(SQLSetEnvAttr(env, SQL_ATTR_ODBC_VERSION, (void *)SQL_OV_ODBC3, 0)))
		throw db::error(error(env, SQL_HANDLE_ENV));

	if (!SQL_SUCCEEDED(SQLAllocHandle(SQL_HANDLE_DBC, env, &dbc)))
		throw db::error(error(env, SQL_HANDLE_ENV));
	
	std::string conn = "DSN=" + sid;
	if (!user.empty())
		conn += ";UID=" + user;
	if (!password.empty())
		conn += ";PWD=" + password;

	if (!SQL_SUCCEEDED(SQLDriverConnect(dbc, 0, (SQLCHAR *)conn.c_str(), conn.size(), 0, 0, 0, SQL_DRIVER_NOPROMPT)))
		throw db::error(error(dbc, SQL_HANDLE_DBC));
}

void
connection::close(void)
{
	if (dbc) {
		SQLDisconnect(dbc);
		SQLFreeHandle(SQL_HANDLE_DBC, dbc);
		dbc = 0;
	}
	if (env) {
		SQLFreeHandle(SQL_HANDLE_ENV, env);
		env = 0;
	}
}

std::string
connection::error(SQLHANDLE handle, int type)
{
	SQLCHAR state[7];
	SQLCHAR text[1024];
	SQLSMALLINT len;
	SQLINTEGER native;
	int i = 0;
	std::string ret;

	while (SQL_SUCCEEDED(SQLGetDiagRec(type, handle, ++i, state, &native, text, sizeof(text), &len))) {
		ret += str(boost::format("%s:%d") % state % text);
	}

	return ret;
}

db::resultptr
connection::execute_sql(std::string const &sql)
{
	db::resultptr r = prepare_sql(sql);
	r->execute();
	return r;
}

db::resultptr
connection::prepare_sql(std::string const &sql)
{
	return db::resultptr(new result(this, sql));
}

result::result(connection *conn, std::string const &q)
	: stmt(0)
	, conn(conn)
	, sql(q)
{
	if (!SQL_SUCCEEDED(SQLAllocHandle(SQL_HANDLE_STMT, conn->dbc, &stmt)))
		throw db::error(conn->error(conn->dbc, SQL_HANDLE_DBC));
}

void
result::execute(void)
{
	if (!SQL_SUCCEEDED(SQLExecDirect(stmt, (SQLCHAR *)sql.c_str(), sql.size())))
		throw db::error(conn->error(stmt, SQL_HANDLE_STMT));

	SQLSMALLINT ncols;
	SQLNumResultCols(stmt, &ncols);
	if (ncols == 0)
		return;

	SQLCHAR name[128];
	SQLSMALLINT namelen;
	SQLUINTEGER colsize;
	fields.resize(ncols);
	for (int i = 0; i < ncols; ++i) {
		if (!SQL_SUCCEEDED(SQLDescribeCol(stmt, i + 1, 
					name, sizeof(name), &namelen,
					NULL, &colsize, NULL, NULL)))
			throw db::error(conn->error(stmt, SQL_HANDLE_STMT));
		fields[i].name.assign(name, name + namelen);
		fields[i].size = colsize;
		fields[i].data.resize(colsize + 1);
	}
}

void
result::bind(std::string const &key, std::string const &value)
{
	throw db::error("prepared statements not supported for ODBC");
}

result::~result()
{
	SQLFreeHandle(SQL_HANDLE_STMT, stmt);
}

bool
result::empty(void)
{
	return fields.size() == 0;
}

int
result::num_fields(void)
{
	return fields.size();
}

int
result::affected_rows(void) 
{
	return 0; /* XXX */
}

result_row *
result::next_row(void)
{
	SQLRETURN r;
	r = SQLFetch(stmt);
	if (!SQL_SUCCEEDED(r)) {
		if (r == SQL_NO_DATA)
			return NULL;
		throw db::error(conn->error(stmt, SQL_HANDLE_STMT));
	}

	for (int i = 0; i < fields.size(); ++i) {
		SQLINTEGER indicator;
		r = SQLGetData(stmt, i + 1, SQL_C_CHAR,
			&fields[i].data[0], fields[i].data.size(),
			&indicator);
		if (!SQL_SUCCEEDED(r))
			throw db::error(conn->error(stmt, SQL_HANDLE_STMT));
		if (indicator == SQL_NULL_DATA) {
			fields[i].data.resize(5);
			std::strcpy(&fields[i].data[0], "NULL");
		}
	}
	return new result_row(this);
}

result_row::result_row(result *er)
	: er(er)
{
}

std::string
result_row::string_value(int col) 
{
	return std::string(&er->fields[col].data[0]);

}

std::string
result::field_name(int col)
{
	return fields[col].name;
}

std::vector<db::table>
connection::describe_tables(std::string const &schema)
{
	SQLHANDLE stmt;
	SQLAllocHandle(SQL_HANDLE_STMT, dbc, &stmt);
	SQLTables(stmt, NULL, 0, (SQLCHAR *)schema.c_str(), schema.size(), NULL, 0, NULL, 0);

	SQLRETURN r;
	std::vector<std::pair<std::string, std::string> > names;
	for (;;) {
		r = SQLFetch(stmt);
		if (!SQL_SUCCEEDED(r)) {
			if (r == SQL_NO_DATA)
				break;
			
			std::string e = error(stmt, SQL_HANDLE_STMT);
			SQLFreeHandle(SQL_HANDLE_STMT, stmt);
			throw db::error(e);
		}

		SQLINTEGER ind;
		char name[256];
		char schema[256];
		SQLGetData(stmt, 2, SQL_C_CHAR, schema, sizeof(schema), &ind);
		SQLGetData(stmt, 3, SQL_C_CHAR, name, sizeof(name), &ind);
		names.push_back(std::pair<std::string, std::string>(
			schema, name));
	}
	SQLFreeHandle(SQL_HANDLE_STMT, stmt);

	std::vector<db::table> ret;
	for (int i = 0; i < names.size(); ++i) {
		ret.push_back(describe_table(names[i].first, names[i].second));
	}

	return ret;
}

db::table
connection::describe_table(std::string const &schema, std::string const &name)
{
	db::table ret;
	ret.name = name;
	ret.schema = schema;

	SQLHANDLE stmt;
	SQLAllocHandle(SQL_HANDLE_STMT, dbc, &stmt);
	SQLColumns(stmt, NULL, 0, (SQLCHAR *)schema.c_str(), schema.size(),
			(SQLCHAR *)name.c_str(), name.size(), NULL, 0);
	SQLRETURN r;
	for (;;) {
		r = SQLFetch(stmt);
		if (!SQL_SUCCEEDED(r)) {
			if (r == SQL_NO_DATA)
				break;
			
			std::string e = error(stmt, SQL_HANDLE_STMT);
			SQLFreeHandle(SQL_HANDLE_STMT, stmt);
			throw db::error(e);
		}

		db::column c;
		SQLINTEGER ind;
		char name[256];
		char type[256];
		char nullable[5];
		SQLGetData(stmt, 4, SQL_C_CHAR, name, sizeof(name), &ind);
		SQLGetData(stmt, 6, SQL_C_CHAR, type, sizeof(type), &ind);
		SQLGetData(stmt, 18, SQL_C_CHAR, nullable, sizeof(nullable), &ind);
		c.name = name;
		c.type = type;
		c.nullable = !strcmp(nullable, "YES");
		ret.columns.push_back(c);
	}
	SQLFreeHandle(SQL_HANDLE_STMT, stmt);

	return ret;
}

} // namespace odbc
