#include <string>
#include <map>
#include <boost/format.hpp>
#include <boost/function.hpp>
#include <boost/assign/list_of.hpp>

#include "db.h"

#ifdef SKIRMISH_MYSQL
# include "mysqldb.h"
#endif

#ifdef SKIRMISH_POSTGRES
# include "pgsql.h"
#endif

#ifdef SKIRMISH_ORACLE
# include "ora.h"
#endif

#ifdef SKIRMISH_ODBC
# include "odbc.h"
#endif

#ifdef SKIRMISH_MAXDB
# include "maxdb.h"
#endif

#ifdef SKIRMISH_SQLITE
# include "sqlite.h"
#endif

namespace db {

connection::connection()
{
}

connection::~connection()
{
}

namespace {
template<typename T>
connectionptr
construct(std::string const &desc)
{
	return connectionptr(new T(desc));
}
}

connectionptr
connection::create(std::string const &desc)
{
	std::string type;
	std::string::size_type i;

	if ((i = desc.find(':')) == std::string::npos)
		throw db::error("invalid scheme in description");

	type = desc.substr(0, i);

	typedef std::map<std::string, boost::function<connectionptr (std::string const &)> > schemelist_t;

	static schemelist_t schemes = boost::assign::map_list_of
#ifdef SKIRMISH_MYSQL
			("mysql",	construct<mysql::connection>)
#endif
#ifdef SKIRMISH_POSTGRES
			("postgres",	construct<postgres::connection>)
#endif
#ifdef SKIRMISH_ORACLE
			("oracle",	construct<oracle::connection>)
#endif
#ifdef SKIRMISH_ODBC
			("odbc",	construct<odbc::connection>)
#endif
#ifdef SKIRMISH_MAXDB
			("maxdb",	construct<maxdb::connection>)
			("sapdb",	construct<maxdb::connection>)
#endif
#ifdef SKIRMISH_SQLITE
			("sqlite",	construct<sqlite::connection>)
#endif
		;

	schemelist_t::iterator it = schemes.find(type);
	if (it == schemes.end())
		throw db::error(str(boost::format("unknown scheme \"%s\" in description") % desc));
	return it->second(desc);
}

result::~result()
{
}

result_row::~result_row()
{
}

resultset_iterator::resultset_iterator()
	: isend(true)
{
}

resultset_iterator::resultset_iterator(result *er)
	: er(er)
	, isend(false)
{
	fetch();
}

void
resultset_iterator::fetch(void)
{
	row.reset(er->next_row());
	if (!row)
		isend = true;
}

resultset_iterator &
resultset_iterator::operator++(void)
{
	if (isend)
		throw db::error("resultset_iterator incremented past end");
	fetch();
	return *this;
}

resultset_iterator
resultset_iterator::operator++(int)
{
	resultset_iterator n(*this);
	return ++n;
}

resultset_iterator::resultset_iterator(resultset_iterator const &other)
	: er(other.er)
	, isend(other.isend)
	, row(other.row)
{
}

bool
resultset_iterator::operator==(resultset_iterator const &other)
{
	if (isend && other.isend)
		return true;
	return false;
}

bool
resultset_iterator::operator!=(resultset_iterator const &other)
{
	return !(*this == other);
}

result_row *
resultset_iterator::operator->(void)
{
	return row.get();
}

resultset_iterator
result::begin(void)
{
	return resultset_iterator(this);
}

resultset_iterator
result::end(void)
{
	return resultset_iterator();
}

} // namespace db
