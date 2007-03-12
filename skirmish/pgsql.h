#ifndef PGSQL_H
#define PGSQL_H

#include <string>
#include <vector>

#include <libpq-fe.h>

#include "db.h"

namespace postgres {

struct execution_result;

struct result_row : db::result_row {
	result_row(execution_result *er, PGresult *, int);
	
	std::string string_value(int col);

	int row;
	PGresult *res;
	execution_result *er;
};

struct execution_result : db::execution_result {
	execution_result(PGconn*, PGresult *);
	~execution_result();

	bool has_data(void);
	int num_fields(void);
	int affected_rows(void);
	std::string field_name(int col);

	result_row *next_row(void);

	PGconn *conn;
	PGresult *res;
	std::vector<std::string> names;
	int row;
};

struct connection : db::connection {
	connection(std::string const &desc);
	~connection();

	void open(void);
	void close(void);

	std::string error(void);

	execution_result *execute_sql(std::string const &);

private:
	PGconn *conn;
	std::string err;
	std::string host, db, user, password;
};

} // namespace mysql

#endif
