#include <iostream>
#include <boost/lexical_cast.hpp>
#include <boost/format.hpp>
#include <boost/algorithm/string/case_conv.hpp>
#include <unistd.h>

#include "sqlite.h"

namespace sqlite {

connection::connection(std::string const &desc)
	: filename(desc)
{
}

connection::~connection()
{
	close();
}

void
connection::open(void)
{
	if (sqlite3_open(filename.c_str(), &db)) {
		std::string err = sqlite3_errmsg(db);
		sqlite3_close(db);
		throw db::error(err);
	}
}

void
connection::close(void)
{
	sqlite3_close(db);
	db = 0;
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
	: conn(conn)
	, sql(q)
{
	char const *tail;
	if (sqlite3_prepare(conn->db, sql.c_str(), sql.size(), &stmt, &tail))
		throw db::error(sqlite3_errmsg(conn->db));

	int ncols;
	ncols = sqlite3_column_count(stmt);
	if (ncols == 0)
		return;
	
	fields.resize(ncols);
	for (int i = 0; i < ncols; ++i) {
		fields[i].name = sqlite3_column_name(stmt, i);
	}
}

void
result::execute(void)
{
	switch (step = sqlite3_step(stmt)) {
		case SQLITE_BUSY:
			throw db::error("database is locked");
		case SQLITE_ROW:
		case SQLITE_DONE:
			return;
		default:
			throw db::error(sqlite3_errmsg(conn->db));
	}
}

void
result::bind(std::string const &key, std::string const &value)
{
	throw db::error("binding not supported for sqlite");
}

result::~result()
{
	sqlite3_finalize(stmt);
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
	if (step == SQLITE_DONE)
		return NULL;
	else if (step == -1) {
		switch (step = sqlite3_step(stmt)) {
		case SQLITE_DONE:
			return NULL;
		case SQLITE_BUSY:
			throw db::error("database is locked");
		case SQLITE_ROW:
			step = -1;
			break;
		default:
			throw db::error(sqlite3_errmsg(conn->db));
		}
	} else
		step = -1;
	return new result_row(this);
}

result_row::result_row(result *er)
	: er(er)
{
}

std::string
result_row::string_value(int col) 
{
	char const *c = (char const *) sqlite3_column_text(er->stmt, col);
	if (c)
		return c;
	return "NULL";

}

std::string
result::field_name(int col)
{
	return fields[col].name;
}

std::vector<db::table>
connection::describe_tables(std::string const &schema)
{
	db::resultptr r = execute_sql("SELECT name FROM sqlite_master");

	std::vector<std::string> names;

	std::vector<db::table> ret;
	result::iterator it = r->begin(), end = r->end();
	for (; it != end; ++it) {
std::cout << "got row\n";
		names.push_back(it->string_value(0));
	}

	for (int i = 0; i < names.size(); ++i) {
		ret.push_back(describe_table("", names[i]));
	}
	return ret;
}

db::table
connection::describe_table(std::string const &schema, std::string const &name)
{
	db::table ret;
	ret.name = name;
	ret.schema = schema;
	return ret;
}

} // namespace sqlite
