#include <string>
#include <map>
#include <boost/format.hpp>
#include <boost/function.hpp>
#include <boost/assign/list_of.hpp>

#include "db.h"

namespace db {

connection::connection()
{
}

connection::~connection()
{
}

connection::schemelist_t &
get_schemelist()
{
	static connection::schemelist_t list;
	return list;
}

connectionptr
connection::create(std::string const &desc)
{
	std::string type;
	std::string::size_type i;

	if ((i = desc.find(':')) == std::string::npos)
		throw db::error("invalid scheme in description");

	type = desc.substr(0, i);

	schemelist_t::iterator it = get_schemelist().find(type);
	if (it == get_schemelist().end())
		throw db::error(str(boost::format("unknown scheme \"%s\" in description") % type));
	return it->second(desc);
}

void
connection::add_scheme(std::string const &name, scheme_creator_t creator)
{
	get_schemelist()[name] = creator;
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
