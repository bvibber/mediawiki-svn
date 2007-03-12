#ifndef MYSQLDB_H
#define MYSQLDB_H

#include <string>
#include <vector>

#include <mysql.h>

#include "db.h"

namespace mysql {

struct result;

struct result_row : db::result_row {
	result_row(result *er, MYSQL_ROW row);
	
	std::string string_value(int col);

	MYSQL_ROW row;
	result *er;
};

struct result : db::result {
	result(MYSQL *, std::string const &sql);
	~result();

	void bind(std::string const &, std::string const &);
	void execute();

	bool empty(void);
	int num_fields(void);
	int affected_rows(void);
	std::string field_name(int col);

	result_row *next_row(void);

	MYSQL *conn;
	MYSQL_RES *res;
	std::vector<std::string> names;
	std::string sql;
};

struct connection : db::connection {
	connection(std::string const &desc);
	~connection();

	void open(void);
	void close(void);

	std::string error(void);

	db::resultptr execute_sql(std::string const &);
	db::resultptr prepare_sql(std::string const &);
	std::vector<db::table> describe_tables(std::string const &);
	db::table describe_table(std::string const &, std::string const &);

private:
	MYSQL *conn;
	std::string err;
	std::string host, db, user, password;
};

} // namespace mysql

#endif
