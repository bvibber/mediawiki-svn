/*
 * Logwood SQL driver.
 */

#ifndef LWSQL
#define LWSQL

#include <string>
#include <vector>
#include <exception>
#include <stdexcept>

#include <mysql.h>

namespace sql {
using std::vector;
using std::string;
typedef long long int number;
typedef unsigned long long int unumber;

class statement;
class resultset;

struct invalid_type : std::runtime_error {
	invalid_type() : std::runtime_error("Invalid type for operation") {}
};

struct generic_mysql_error : std::runtime_error {
	generic_mysql_error (MYSQL *c)
	: std::runtime_error(mysql_error(c))
	{}
};

struct connection_error : generic_mysql_error {
	connection_error (MYSQL *c) : generic_mysql_error(c) {}
};

struct statement_error : std::runtime_error {
	statement_error (MYSQL_STMT *s) : std::runtime_error(mysql_stmt_error(s)) {};
};

class connection {
public:
static	connection*	create		(string const& dbname, string const& dbhost,
					 string const& dbuser, string const& dbpass);
	statement*	prepare		(string const& query);
			~connection	(void);

private:
			connection	(string const& dbname, string const& dbhost,
					 string const& dbuser, string const& dbpass);
	
	MYSQL		mconn;
	friend class	statement;
};

class statement {
public:
	template<typename T>
	void	bind		(int pos, T const &value) {
		throw invalid_type();
	}

	void		 execute	(void);
	resultset	*result		(void);
			~statement	(void);

private:
			statement	(connection *conn, string const &query);

	struct bind_data {
		number		bd_num;
		unumber		bd_unum;
		string		bd_string;
		unsigned long	bd_len;
	};

	MYSQL_STMT		*mstmt;
	MYSQL_RES		*metadata;
	unsigned long		 mparams;
	vector<MYSQL_BIND>	 mbinds;
	vector<bind_data>	 mbinddata;
	friend class		 connection;
};
template<> void	 statement::bind<number>	(int pos, number const &v);
template<> void	 statement::bind<unumber>	(int pos, unumber const &v);
template<> void	 statement::bind<string>	(int pos, string const &v);


class resultset {
public:
	bool	next		(void);
	template<typename ret>
	ret	get		(int pos) {
		throw invalid_type();
	}

		~resultset	(void);
private:
		resultset	(MYSQL_STMT *);

	struct bind_data {
		number		 bd_num;
		unumber		 bd_unum;
		char		*bd_string;
		my_bool		 bd_is_null;
		unsigned long	 bd_length;
	};

	MYSQL_STMT		*mstmt;
	MYSQL_RES		*metadata;
	unsigned long		 nfields;
	vector<MYSQL_BIND>	 mbinds;
	vector<bind_data>	 mdata;
	vector<MYSQL_FIELD*>	 mfields;
	friend class		 statement;
};

template<> number resultset::get<number> (int pos);
template<> unumber resultset::get<unumber> (int pos);
template<> string resultset::get<string> (int pos);

} // ns sql
#endif /* LWSQL */
