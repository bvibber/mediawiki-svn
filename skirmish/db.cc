#include <string>
#include <boost/format.hpp>

#include "db.h"
#include "mysqldb.h"
#include "pgsql.h"
#include "ora.h"

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
	if (type == "mysql")
		return connectionptr(new mysql::connection(desc));
	else if (type == "postgres")
		return connectionptr(new postgres::connection(desc));
	else if (type == "oracle")
		return connectionptr(new oracle::connection(desc));
	else
		throw db::error(str(boost::format("unknown scheme \"%s\" in description") % desc));
}

execution_result::~execution_result()
{
}

result_row::~result_row()
{
}

} // namespace db
