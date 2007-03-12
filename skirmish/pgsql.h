#ifndef PGSQL_H
#define PGSQL_H

#include <string>
#include <vector>

#include <libpq-fe.h>

#include "db.h"

namespace postgres {

struct result;

struct result_row : db::result_row {
	result_row(result *er, PGresult *, int);
	
	std::string string_value(int col);

private:
	int row;
	PGresult *res;
	result *er;
};

struct result : db::result {
	result(PGconn*, std::string const &);
	~result();

	void bind(std::string const &, std::string const &);
	void execute(void);

	bool empty(void);
	int num_fields(void);
	int affected_rows(void);
	std::string field_name(int col);

protected:
	result_row *next_row(void);

private:
	PGconn *conn;
	PGresult *res;
	std::vector<std::string> names;
	int row;
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
	PGconn *conn;
	std::string err;
	std::string host, db, user, password;
};

} // namespace mysql

#endif
