#include <iostream>
#include <boost/lexical_cast.hpp>
#include <boost/format.hpp>
#include <boost/algorithm/string/case_conv.hpp>
#include <unistd.h>
#include <oci.h>

#include "db.h"

namespace {

bool
ora_success(int e)
{
	return e == OCI_SUCCESS || e == OCI_SUCCESS_WITH_INFO;
}

}

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

struct register_ {
	static db::connectionptr create(std::string const &d) {
		return db::connectionptr(new connection(d));
	}

	register_() {
		db::connection::add_scheme("oracle", create);
	}
} register_;

connection::connection(std::string const &desc)
{
	/* desc is "user[/password]@SID" */
	std::string::size_type i;
	std::string userpart;
	std::string d;

	d = desc.substr(desc.find(':') + 1);

	if ((i = d.find('@')) != std::string::npos) {
		userpart = d.substr(0, i);
		sid = d.substr(i + 1);
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
}

connection::~connection()
{
	close();
}

void
connection::open(void)
{
	if (!ora_success(OCIEnvCreate(&env, OCI_DEFAULT,
						NULL, NULL, NULL, NULL, 0, NULL)))
		throw db::error(error());
	
	if (!ora_success(OCIHandleAlloc(env, (void **)&err, OCI_HTYPE_ERROR, 0, NULL)))
		throw db::error(error());

	if (!ora_success(OCILogon(env, err, &svc,
					(text *)user.c_str(), user.size(),
					(text *)password.c_str(), password.size(),
					(text *)sid.c_str(), sid.size())))
		throw db::error(error());
}

void
connection::close(void)
{
	OCILogoff(svc, err);
	svc = NULL;
	OCIHandleFree(err, OCI_HTYPE_ERROR);
	err = NULL;
	OCIHandleFree(env, OCI_HTYPE_ENV);
	env = NULL;
}

std::string
connection::error(void)
{
	if (last_error == OCI_SUCCESS || last_error == OCI_SUCCESS_WITH_INFO)
		return "no error";

	char buf[1024];
	sb4 errno;
	OCIErrorGet(err, 1, NULL, &errno, (text *)buf, sizeof(buf), OCI_HTYPE_ERROR);
	std::string ret = buf;
	ret.resize(ret.size() - 1);	
	return ret;
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
{
	if (!ora_success(OCIHandleAlloc(conn->env, (void **)&stmt, OCI_HTYPE_STMT, 0, NULL)))
		throw db::error(conn->error());

	if (!ora_success(OCIStmtPrepare(stmt, conn->err, (text *)q.c_str(), q.size(), OCI_NTV_SYNTAX, OCI_DEFAULT)))
		throw db::error(conn->error());

	ub4 sz;

	sz = sizeof(type);
	if (!ora_success(OCIAttrGet(stmt, OCI_HTYPE_STMT, &type, &sz, OCI_ATTR_STMT_TYPE, conn->err)))
		throw db::error(conn->error());
}

void
result::execute(void)
{
	conn->last_error = OCIStmtExecute(conn->svc, stmt, conn->err,
				(type == OCI_STMT_SELECT) ? 0 : 1,
				0, NULL, NULL, OCI_COMMIT_ON_SUCCESS);
	if (!ora_success(conn->last_error))
		throw db::error(conn->error());

	if (type != OCI_STMT_SELECT)
		return;

	ub4 sz;

	sz = sizeof(ncols);
	if (!ora_success(OCIAttrGet(stmt, OCI_HTYPE_STMT, &ncols, &sz, OCI_ATTR_PARAM_COUNT, conn->err)))
		throw db::error(conn->error());

	OCIParam *p;
	fields.resize(ncols);
	for (int i = 0; i < ncols; ++i) {
		if (!ora_success(OCIParamGet(stmt, OCI_HTYPE_STMT, conn->err, (void **)&p, i + 1)))
			throw db::error(conn->error());

		char *colname;
		unsigned namelen;
		if (!ora_success(OCIAttrGet(p, OCI_DTYPE_PARAM, &colname, &namelen, OCI_ATTR_NAME, conn->err)))
			throw db::error(conn->error());

		fields[i].name.assign(colname, colname + namelen);

		ub2 width;
		if (!ora_success(OCIAttrGet(p, OCI_DTYPE_PARAM, &width, 0, OCI_ATTR_DATA_SIZE, conn->err)))
			throw db::error(conn->error());

		fields[i].width = width;

		fields[i].data.resize(width + 1);
		ub2 rcode, len;
		if (!ora_success(OCIDefineByPos(stmt, &fields[i].define, conn->err, i + 1, &fields[i].data[0], width + 1,
				SQLT_STR, &fields[i].isnull, &len, &rcode, OCI_DEFAULT)))
			throw db::error(conn->error());
	}
}

void
result::bind(std::string const &key, std::string const &value)
{
	OCIBind *bind;
	if (!ora_success(OCIBindByName(stmt, &bind, conn->err,
				(text *)key.c_str(), -1, (void *)value.c_str(), value.size() + 1, SQLT_STR,
				NULL, NULL, NULL, 0, NULL, OCI_DEFAULT)))
		throw db::error(conn->error());
}

result::~result()
{
	OCIHandleFree(stmt, OCI_HTYPE_STMT);
}

bool
result::empty(void)
{
	return type != OCI_STMT_SELECT;
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
	conn->last_error = OCIStmtFetch(stmt, conn->err, 1, OCI_FETCH_NEXT, OCI_DEFAULT);
	if (conn->last_error == OCI_NO_DATA)
		return NULL;
	if (!ora_success(conn->last_error))
		throw db::error(conn->error());

	return new result_row(this);
}

result_row::result_row(result *er)
	: er(er)
{
}

std::string
result_row::string_value(int col) 
{
	return std::string(&er->fields[col].data[0]);

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

} // namespace oracle
