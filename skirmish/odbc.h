#ifndef ODBC_H
#define ODBC_H

#include <string>
#include <vector>

#include <sql.h>

#include "db.h"

namespace odbc {

struct result;
struct connection;

struct odbcfield {
	std::string name;
	SQLUINTEGER size;
	std::vector<char> data;
};

struct result_row : db::result_row {
	result_row(result *er);
	
	std::string string_value(int col);

	result *er;
};

struct result : db::result {
	result(connection *, std::string const &);
	~result();

	void bind(std::string const &, std::string const &);
	void execute(void);

	bool empty(void);
	int num_fields(void);
	int affected_rows(void);
	std::string field_name(int col);

	result_row *next_row(void);

	std::string sql;
	std::vector<odbcfield> fields;
	connection *conn;
	SQLHANDLE stmt;
};

struct connection : db::connection {
	connection(std::string const &desc);
	~connection();

	void open(void);
	void close(void);

	std::string error(SQLHANDLE, int);

	db::resultptr execute_sql(std::string const &);
	db::resultptr prepare_sql(std::string const &);

	std::vector<db::table> describe_tables(std::string const &);
	db::table describe_table(std::string const &, std::string const &);

private:
	friend class result;

	SQLHENV env;
	SQLHDBC dbc;
	SQLRETURN err;

	std::string sid, user, password;
};

} // namespace oracle

#endif
