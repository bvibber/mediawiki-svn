#include <iostream>
#include <boost/format.hpp>
#include <mysql.h>

int
main(int argc, char **argv)
{
	if (!argv[1]) {
		std::cerr << boost::format("usage: %s <hash>\n") % argv[0];
		return 1;
	}

	std::string hash(argv[1]);

	MYSQL *conn;
	if ((conn = mysql_init(NULL)) == NULL) {
		std::cerr << "out of memory\n";
		return 1;
	}

	if (mysql_options(conn, MYSQL_READ_DEFAULT_GROUP, "client")) {
		std::cerr << "cannot set default group\n";
		return 1;
	}

	if (mysql_real_connect(conn, NULL, NULL, NULL, NULL, 0, NULL, 0) == NULL) {
		std::cerr << boost::format("Cannot connect to MySQL: %s\n")
				% mysql_error(conn);
		return 1;
	}

	MYSQL_STMT *stmt(mysql_stmt_init(conn));
	if (stmt == NULL) {
		std::cerr << "out of memory\n";
		return 1;
	}

	static std::string stmtstr("SELECT xbt_tracker.add_torrent(?)");
	if (mysql_stmt_prepare(stmt, stmtstr.c_str(), stmtstr.size())) {
		std::cerr << boost::format("Cannot prepare statement: %s\n")
				% mysql_stmt_error(stmt);
		return 1;
	}

	MYSQL_BIND values[1];
	std::memset(values, 0, sizeof(values));
	my_bool isnull = 0;
	unsigned long hlen = hash.size();
	values[0].buffer_type = MYSQL_TYPE_STRING;
	values[0].buffer = (void *) hash.data();
	values[0].buffer_length = hash.size();
	values[0].length = &hlen;
	values[0].is_null = &isnull;

	if (mysql_stmt_bind_param(stmt, values)) {
		std::cerr << boost::format("Cannot bind value: %s\n")
				% mysql_stmt_error(stmt);
		return 1;
	}

	char errstr[256];
	unsigned long errlen;
	my_bool trunc;
	MYSQL_BIND result;
	std::memset(&result, 0, sizeof(result));
	result.buffer_type = MYSQL_TYPE_STRING;
	result.buffer = errstr;
	result.buffer_length = sizeof(errstr);
	result.length = &errlen;
	result.is_null = &isnull;
	result.error = &trunc;

	if (mysql_stmt_bind_result(stmt, &result)) {
		std::cerr << boost::format("Cannot bind result: %s\n")
				% mysql_stmt_error(stmt);
		return 1;
	}

	if (mysql_stmt_execute(stmt)) {
		std::cerr << boost::format("Cannot execute statement: %s\n")
				% mysql_stmt_error(stmt);
		return 1;
	}

	switch (mysql_stmt_fetch(stmt)) {
	case 0:
	case MYSQL_DATA_TRUNCATED:
		if (strlen(errstr)) {
			std::cerr << boost::format("Error adding torrent: %s\n")
					% errstr;
			return 1;
		}
		return 0;
	case 1:
		std::cerr << boost::format("Cannot fetch result: %s\n")
				% mysql_stmt_error(stmt);
		return 1;
	case MYSQL_NO_DATA:
		std::cerr << "Unexpectedly got no result from adding torrent!\n";
		return 1;
	}

	mysql_stmt_close(stmt);
	mysql_close(conn);
}
