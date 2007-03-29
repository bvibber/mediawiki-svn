#include <iostream>
#include <boost/lexical_cast.hpp>
#include <boost/format.hpp>
#include <boost/algorithm/string/case_conv.hpp>
#include <unistd.h>
#include <SQLDBC.h>

#include "db.h"

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


struct register_ {
	static db::connectionptr create(std::string const &d) {
		return db::connectionptr(new connection(d));
	}

	register_() {
		db::connection::add_scheme("maxdb", create);
	}
} register_;

connection::connection(std::string const &desc)
	: runtime(0)
	, env(0)
	, conn(0)
{
	/* desc is "user[/password]@[host[/database]]" */
	std::string::size_type i;
	std::string userpart;
	std::string dbpart;
	std::string d;

	d = desc.substr(desc.find(':') + 1);

	if ((i = d.find('@')) != std::string::npos) {
		userpart = d.substr(0, i);
		dbpart = d.substr(i + 1);
	} else
		userpart = d;

	if ((i = userpart.find('/')) != std::string::npos) {
		user = userpart.substr(0, i);
		password = userpart.substr(i + 1);
	} else {
		user = userpart;
		char *p = getpass("Enter password: ");
		if (p)
			password = p;
	}

	if ((i = dbpart.find('/')) != std::string::npos) {
		host = dbpart.substr(0, i);
		dbname = dbpart.substr(i + 1);
	} else
		host = dbpart;
}

connection::~connection()
{
	close();
}

void
connection::open(void)
{
	char err[1024];
	if ((runtime = SQLDBC::GetClientRuntime(err, sizeof(err))) == NULL)
		throw db::error(err);
	env = new SQLDBC::SQLDBC_Environment(runtime);
	conn = env->createConnection();
	if (conn->connect(host.c_str(), dbname.c_str(), user.c_str(), password.c_str()) != SQLDBC_OK)
		throw db::error(error(conn->error()));
}

void
connection::close(void)
{
	delete env;
}

std::string
connection::error(SQLDBC::SQLDBC_ErrorHndl &err)
{
	return str(boost::format("%d:%s:%s") % err.getErrorCode() % err.getSQLState() % err.getErrorText());
}

db::resultptr
connection::execute_sql(std::string const &sql)
{
	db::resultptr r = prepare_sql(sql);
	r->execute();
	return r;
}

db::resultptr
connection::prepare_sql(std::string const &sql)
{
	return db::resultptr(new result(this, sql));
}

result::result(connection *conn, std::string const &q)
	: stmt(0)
	, conn(conn)
	, sql(q)
{
	if ((stmt = conn->conn->createPreparedStatement()) == NULL)
		throw db::error(conn->error(conn->conn->error()));
	if (stmt->prepare(q.c_str(), q.size(), SQLDBC_StringEncodingUTF8) != SQLDBC_OK)
		throw db::error(conn->error(stmt->error()));
	if (!stmt->isQuery())
		return;

	SQLDBC::SQLDBC_ParameterMetaData *pmd = stmt->getParameterMetaData();
	SQLDBC_Int2 nparams = pmd->getParameterCount();
	SQLDBC_Length namelen;
	for (int i = 1; i <= nparams; ++i) {
#if 0
		if (pmd->getParameterName(i, NULL, SQLDBC_StringEncodingUTF8, 0, &namelen) != SQLDBC_DATA_TRUNC)
			throw db::error("could not retrieve parameter length");
#endif
		std::vector<char> paramname(namelen + 1);
		if (pmd->getParameterName(i, &paramname[0], SQLDBC_StringEncodingUTF8, paramname.size() + 1, &namelen) != SQLDBC_OK)
			throw db::error("could not retrieve parameter name");
		param p;
		p.name.assign(&paramname[0], &paramname[0] + namelen);
		p.pos = i;
		params.push_back(p);
	}
}

void
result::execute(void)
{
	if (stmt->execute() != SQLDBC_OK)
		throw db::error(conn->error(stmt->error()));

	SQLDBC::SQLDBC_ResultSetMetaData *md = stmt->getResultSetMetaData();
	if (md == NULL)
		return;

	int ncols = md->getColumnCount();
	fields.resize(ncols);
	for (int i = 0; i < ncols; ++i) {
		int colsize = md->getColumnLength(i + 1);
		fields[i].size = colsize;
		fields[i].data.resize(colsize + 1);
		
		SQLDBC_Retcode r;
		std::vector<char> name(256);
		SQLDBC_Length namelen;	
		r = md->getColumnName(i + 1, &name[0], SQLDBC_StringEncodingUTF8, name.size(), &namelen);
		if (r != SQLDBC_OK && r != SQLDBC_DATA_TRUNC)
			throw db::error(conn->error(stmt->error()));
		if (r == SQLDBC_DATA_TRUNC) {
			name.resize(namelen + 1);
			if (md->getColumnName(i + 1, &name[0], SQLDBC_StringEncodingUTF8, name.size(), &namelen)
			    != SQLDBC_OK)
				throw db::error(conn->error(stmt->error()));
		}
		fields[i].name.assign(&name[0], &name[0] + namelen);
	}

	res = stmt->getResultSet();
}

void
result::bind(std::string const &key, std::string const &value)
{
	for (int i = 0; i < params.size(); ++i) {
		if (params[i].name != key)
			return;
		SQLDBC_Length len = value.size();
		if (stmt->bindParameter(params[i].pos, SQLDBC_HOSTTYPE_UTF8,
				(void *)value.data(), &len, value.size(), SQLDBC_FALSE) != SQLDBC_OK)
			throw db::error(conn->error(stmt->error()));
		return;
	}
	throw db::error(str(boost::format("parameter \"%s\" does not exist in statement") % key));
}

result::~result()
{
}

bool
result::empty(void)
{
	return fields.size() == 0;
}

int
result::num_fields(void)
{
	return fields.size();
}

int
result::affected_rows(void) 
{
	return 0; /* XXX */
}

result_row *
result::next_row(void)
{
	switch (res->next()) {
	case SQLDBC_OK:
		break;
	case SQLDBC_NO_DATA_FOUND:
		return NULL;
	case SQLDBC_NOT_OK:
		throw db::error(conn->error(res->error()));
	}

	for (int i = 0; i < fields.size(); ++i) {
		if (res->getObject(i + 1, SQLDBC_HOSTTYPE_UTF8, &fields[i].data[0], 
				&fields[i].len, fields[i].size + 1, SQLDBC_TRUE) != SQLDBC_OK)
			throw db::error(conn->error(res->error()));
	}
	return new result_row(this);
}

result_row::result_row(result *er)
	: er(er)
{
}

std::string
result_row::string_value(int col) 
{
	if (er->fields[col].len == SQLDBC_NULL_DATA)
		return "NULL";
	return std::string(&er->fields[col].data[0], &er->fields[col].data[er->fields[col].len]);

}

std::string
result::field_name(int col)
{
	return fields[col].name;
}

std::vector<db::table>
connection::describe_tables(std::string const &schema)
{
	std::string n = boost::algorithm::to_upper_copy(schema);

	db::resultptr r;
	if (schema.empty())
		r = prepare_sql("SELECT owner, table_name FROM all_tables");
	else {
		r = prepare_sql("SELECT owner, table_name FROM all_tables WHERE owner = :towner");
		r->bind(":towner", n);
	}
	r->execute();

	std::vector<std::pair<std::string, std::string> > names;

	std::vector<db::table> ret;
	result::iterator it = r->begin(), end = r->end();
	for (; it != end; ++it) {
		names.push_back(std::pair<std::string, std::string>(
			it->string_value(0), it->string_value(1)));
	}

	for (int i = 0; i < names.size(); ++i) {
		ret.push_back(describe_table(names[i].first, names[i].second));
	}

	return ret;
}

db::table
connection::describe_table(std::string const &schema, std::string const &name)
{
	db::table ret;
	ret.name = name;
	ret.schema = schema;

	db::resultptr r = prepare_sql(
		"SELECT column_name, data_type, nullable FROM all_tab_columns WHERE owner = :tabowner AND table_name = :name");
	std::string n = boost::algorithm::to_upper_copy(name);
	std::string o = boost::algorithm::to_upper_copy(schema);

	r->bind(":name", n);
	r->bind(":tabowner", o);
	r->execute();

	result::iterator it = r->begin(), end = r->end();
	for (; it != end; ++it) {
		db::column c;
		c.name = it->string_value(0);
		c.type = it->string_value(1);
		c.nullable = it->string_value(2) == "Y";
		ret.columns.push_back(c);
	}

	return ret;
}

} // namespace maxdb
