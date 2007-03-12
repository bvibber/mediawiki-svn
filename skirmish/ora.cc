#include <iostream>
#include <boost/lexical_cast.hpp>
#include <boost/format.hpp>
#include <boost/algorithm/string/case_conv.hpp>
#include <unistd.h>
#include <oci.h>

#include "ora.h"

namespace oracle {

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
	if (!db.connect(sid, user, password))
		throw db::error(db.error());
}

void
connection::close(void)
{
	db.disconnect();
}

std::string
connection::error(void)
{
	return err;
}

execution_result *
connection::execute_sql(std::string const &sql)
{
	ORAPP::Query *q = db.query(sql.c_str());
	if (!q->execute()) {
		throw db::error(db.error());
	}

	return new execution_result(db, q);
}

execution_result::execution_result(ORAPP::Connection &conn, ORAPP::Query *q)
	: conn(conn)
	, q(q)
	, row(0)
{
	ORAPP::Row *r;
	while (r = q->fetch()) {
		orarow nr;
		for (int i = 0; i < r->width(); ++i) {
			if (rows.empty())
				names.push_back(r->name(i));
			nr.content.push_back((std::string) (*r)[i]);
		}

		rows.push_back(nr);
	}
}

execution_result::~execution_result()
{
}

bool
execution_result::has_data(void)
{
	return !rows.empty();
}

int
execution_result::num_fields(void)
{
	return names.size();
}

int
execution_result::affected_rows(void)
{
	return 0; /* XXX */
}

result_row *
execution_result::next_row(void)
{
	if (row == rows.size())
		return NULL;

	result_row *ret = new result_row(this, row);
	++row;
	return ret;
}

result_row::result_row(execution_result *er, int row)
	: row(row)
	, er(er)
{
}

std::string
result_row::string_value(int col)
{
	return er->rows[row].content[col];

}

std::string
execution_result::field_name(int col)
{
	return names[col];
}

std::vector<db::table>
connection::describe_tables(std::string const &schema)
{
	ORAPP::Query *q;

	if (schema.empty())
		q = db.query("SELECT owner, table_name FROM all_tables");
	else {
		q = db.query("SELECT owner, table_name FROM all_tables WHERE owner = :towner");
		std::string n = boost::algorithm::to_upper_copy(schema);
		q->bind(":towner", n.c_str());
	}

	std::vector<std::pair<std::string, std::string> > names;

	if (!q->execute()) {
		throw db::error(db.error());
	}

	ORAPP::Row *r;
	std::vector<db::table> ret;
	while (r = q->fetch()) {
		names.push_back(std::pair<std::string, std::string>(
			(std::string) (*r)[0], (std::string) (*r)[1]));
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

	ORAPP::Query *q = db.query(
		"SELECT column_name, data_type, nullable FROM all_tab_columns WHERE owner = :tabowner AND table_name = :name");
	std::string n = boost::algorithm::to_upper_copy(name);
	std::string o = boost::algorithm::to_upper_copy(schema);

	q->bind(":name", n.c_str());
	q->bind(":tabowner", o.c_str());

	if (!q->execute()) {
		throw db::error(db.error());
	}

	ORAPP::Row *r;
	while (r = q->fetch()) {
		db::column c;
		c.name = (std::string) (*r)[0];
		c.type = (std::string) (*r)[1];
		c.nullable = ((char) (*r)[2]) == 'Y';
		ret.columns.push_back(c);
	}

	return ret;
}

} // namespace pgsql
