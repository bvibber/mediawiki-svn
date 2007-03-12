#ifndef ORA_H
#define ORA_H

#include <string>
#include <vector>

#include "db.h"
#include "row.hh"
#include "query.hh"
#include "conn.hh"

namespace oracle {

struct execution_result;

struct orarow {
	std::vector<std::string> content;
};

struct result_row : db::result_row {
	result_row(execution_result *er, int);
	
	std::string string_value(int col);

	int row;
	execution_result *er;
};

struct execution_result : db::execution_result {
	execution_result(ORAPP::Connection &, ORAPP::Query *);
	~execution_result();

	bool has_data(void);
	int num_fields(void);
	int affected_rows(void);
	std::string field_name(int col);

	result_row *next_row(void);

	ORAPP::Connection &conn;
	ORAPP::Query *q;
	std::vector<std::string> names;
	std::vector<orarow> rows;
	int row;
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
	ORAPP::Connection db;

	std::string err;
	std::string sid, user, password;
};

} // namespace oracle

#endif
