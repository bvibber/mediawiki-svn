#ifndef DB_H
#define DB_H

#include <string>
#include <exception>
#include <boost/shared_ptr.hpp>
#include <boost/noncopyable.hpp>

namespace db {

struct error : std::exception {
	std::string err;
	error(std::string const &err) : err(err) {}
	~error() throw() {}
	char const *what(void) const throw() { return err.c_str(); }
};

struct result_row : boost::noncopyable {
	virtual ~result_row();
	virtual std::string string_value(int col) = 0;
};

struct execution_result : boost::noncopyable {
	virtual ~execution_result();

	virtual bool has_data(void) = 0;
	virtual int affected_rows(void) = 0;
	virtual int num_fields(void) = 0;
	virtual std::string field_name(int col) = 0;

	virtual result_row *next_row(void) = 0;
};

struct connection {
	static boost::shared_ptr<connection> create(std::string const &);

	virtual void open(void) = 0;
	virtual void close(void) = 0;

	virtual std::string error(void) = 0;

	virtual ~connection();

	virtual execution_result *execute_sql(std::string const &) = 0;

protected:
	connection();
};

typedef boost::shared_ptr<connection> connectionptr;

}

#endif
