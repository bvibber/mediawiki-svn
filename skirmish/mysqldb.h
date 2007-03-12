#ifndef MYSQLDB_H
#define MYSQLDB_H

#include <string>
#include <vector>

#include <mysql.h>

#include "db.h"

namespace mysql {

struct execution_result;

struct result_row : db::result_row {
	result_row(execution_result *er, MYSQL_ROW row);
	
	std::string string_value(int col);

	MYSQL_ROW row;
	execution_result *er;
};

struct execution_result : db::execution_result {
	execution_result(MYSQL *);
	~execution_result();

	bool has_data(void);
	int num_fields(void);
	int affected_rows(void);
	std::string field_name(int col);

	result_row *next_row(void);

	MYSQL *conn;
	MYSQL_RES *res;
	std::vector<std::string> names;
};

struct connection : db::connection {
	connection(std::string const &desc);
	~connection();

	void open(void);
	void close(void);

	std::string error(void);

	execution_result *execute_sql(std::string const &);
	std::vector<db::table> describe_tables(std::string const &);
	db::table describe_table(std::string const &, std::string const &);

private:
	MYSQL *conn;
	std::string err;
	std::string host, db, user, password;
};

} // namespace mysql

#endif
