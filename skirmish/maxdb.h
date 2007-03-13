#ifndef MAXDB_H
#define MAXDB_H

#include <string>
#include <vector>

#include "db.h"
#include <SQLDBC.h>

namespace maxdb {

struct result;
struct connection;

struct maxdbfield {
	std::string name;
	SQLDBC_Length size;
	SQLDBC_Length len;
	std::vector<char> data;
};

struct result_row : db::result_row {
	result_row(result *er);
	
	std::string string_value(int col);

	result *er;
};

struct param {
	std::string name;
	int pos;
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
	std::vector<maxdbfield> fields;
	connection *conn;
	SQLDBC::SQLDBC_PreparedStatement *stmt;
	SQLDBC::SQLDBC_ResultSet *res;
	std::vector<param> params;
};

struct connection : db::connection {
	connection(std::string const &desc);
	~connection();

	void open(void);
	void close(void);

	std::string error(SQLDBC::SQLDBC_ErrorHndl &);

	db::resultptr execute_sql(std::string const &);
	db::resultptr prepare_sql(std::string const &);

	std::vector<db::table> describe_tables(std::string const &);
	db::table describe_table(std::string const &, std::string const &);

private:
	friend class result;

	SQLDBC_IRuntime *runtime;
	SQLDBC::SQLDBC_Environment *env;
	SQLDBC::SQLDBC_Connection *conn;
	
	std::string host, dbname, user, password;
};

} // namespace oracle

#endif
