#include <string>
#include <boost/format.hpp>

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

namespace db {

connection::connection()
{
}

connection::~connection()
{
}

connectionptr
connection::create(std::string const &desc)
{
	std::string type;
	std::string::size_type i;

	if ((i = desc.find(':')) == std::string::npos)
		throw db::error("invalid scheme in description");

	type = desc.substr(0, i);
#ifdef SKIRMISH_MYSQL
	if (type == "mysql")
		return connectionptr(new mysql::connection(desc));
	else
#endif
#ifdef SKIRMISH_POSTGRES
	if (type == "postgres")
		return connectionptr(new postgres::connection(desc));
	else
#endif
#ifdef SKIRMISH_ORACLE
	if (type == "oracle")
		return connectionptr(new oracle::connection(desc));
	else
#endif
		throw db::error(str(boost::format("unknown scheme \"%s\" in description") % desc));
}

execution_result::~execution_result()
{
}

result_row::~result_row()
{
}

} // namespace db
