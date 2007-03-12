#include <iostream>
#include <boost/lexical_cast.hpp>
#include <boost/format.hpp>
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
			if (names.empty()) {
				names.push_back(r->name(i));
			}
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

} // namespace pgsql
