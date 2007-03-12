#ifndef ORA_H
#define ORA_H

#include <string>
#include <vector>

#include <oci.h>

#include "db.h"

namespace oracle {

struct result;
struct connection;

struct orarow {
	std::vector<std::string> content;
};

struct orafield {
	std::string name;
	ub2 width;
	unsigned isnull;
	std::vector<char> data;
	OCIDefine *define;
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

	std::vector<orafield> fields;
	connection *conn;
	OCIStmt *stmt;
	ub2 type;
	ub4 ncols;
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
	friend class result;

	OCIEnv *env;
	OCIError *err;
	OCISvcCtx *svc;

	std::string sid, user, password;
	unsigned last_error;
};

} // namespace oracle

#endif
