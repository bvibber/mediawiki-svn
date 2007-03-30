#ifndef DB_H
#define DB_H

#include <string>
#include <exception>
#include <map>
#include <boost/shared_ptr.hpp>
#include <boost/noncopyable.hpp>
#include <boost/function.hpp>

namespace db {

struct result;
struct result_row;

struct resultset_iterator {
	result_row &operator *(void);
	result_row *operator ->(void);

	resultset_iterator &operator++(void);
	resultset_iterator operator++(int);

	bool operator== (resultset_iterator const &);
	bool operator!= (resultset_iterator const &);

	resultset_iterator();
	resultset_iterator(result *);
	resultset_iterator(resultset_iterator const &other);

private:
	result *er;
	bool isend;
	boost::shared_ptr<result_row> row;
	void fetch(void);
};

struct error : std::exception {
	std::string err;
	error(std::string const &err) : err(err) {}
	~error() throw() {}
	char const *what(void) const throw() { return err.c_str(); }
};

struct sqlerror : error {
	sqlerror(std::string const &err, std::string const &query = "", int where = -1)
		: error(err)
		, query(query)
		, where(where)
	{}
	~sqlerror() throw() {}

	std::string query;
	int where;
};

struct result_row : boost::noncopyable {
	virtual ~result_row();
	virtual std::string string_value(int col) = 0;
};

struct result : boost::noncopyable {
	typedef resultset_iterator iterator;
	typedef resultset_iterator const_iterator;

	virtual void bind(std::string const &, std::string const &) = 0;
	virtual void execute() = 0;

	iterator begin();
	iterator end();

	virtual ~result();

	virtual bool empty(void) = 0;
	virtual int affected_rows(void) = 0;
	virtual int num_fields(void) = 0;
	virtual std::string field_name(int col) = 0;

protected:
	friend class resultset_iterator;
	virtual result_row *next_row(void) = 0;
};
typedef boost::shared_ptr<result> resultptr;

struct column {
	std::string name;
	std::string type;
	bool nullable;
};

struct table {
	std::string schema;
	std::string name;
	std::vector<column> columns;
};

struct connection {
	typedef boost::function<boost::shared_ptr<connection> (std::string const &)> scheme_creator_t;
	typedef std::map<std::string, scheme_creator_t> schemelist_t;

	static boost::shared_ptr<connection> create(std::string const &);
	static void add_scheme(std::string const &name, scheme_creator_t);

	virtual void open(void) = 0;
	virtual void close(void) = 0;

	virtual ~connection();

	virtual resultptr execute_sql(std::string const &) = 0;
	virtual std::vector<table> describe_tables(std::string const & = "") = 0;
	virtual table describe_table(std::string const &, std::string const &) = 0;

protected:
	connection();
};

typedef boost::shared_ptr<connection> connectionptr;

}

#endif
