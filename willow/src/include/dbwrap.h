/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * dbwrap: C++ Berkeley DB wrapper.
 */

#ifndef DBWRAP_H
#define DBWRAP_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <sys/types.h>

#include <algorithm>
using std::back_inserter;

#include <db.h>

#include "willow.h"
#include "util.h"

namespace db {

template<typename T>
struct marshaller {
};

template<>
struct marshaller<char> {
	pair<char const *, uint32_t> marshall(char c) {
		return make_pair(&c, sizeof(c));
	}
};

template<>
struct marshaller<int> {
	pair<char const *, uint32_t> marshall(int c) {
		return make_pair((char const *)&c, sizeof(c));
	}
};

template<>
struct marshaller<long> {
	pair<char const *, uint32_t> marshall(long c) {
		return make_pair((char const *)&c, sizeof(c));
	}
};

template<>
struct marshaller<unsigned long> {
	pair<char const *, uint32_t> marshall(unsigned long c) {
		return make_pair((char const *)&c, sizeof(c));
	}
};

template<>
struct marshaller<unsigned int> {
	pair<char const *, uint32_t> marshall(unsigned int c) {
		return make_pair((char const *)&c, sizeof(c));
	}
};

template<>
struct marshaller<string> {
	pair<char const *, uint32_t> marshall(string const &s) {
		return make_pair(s.data(), s.size());
	}
};

template<typename T>
struct inline_data_store {
	pair<char const *, uint32_t> store(T &o) {
	marshaller<T>	m;
		return m.marshall(o);
	}

	void unstore(T const &o) {}

	T *retrieve(pair<char const *, uint32_t> const &d) {
	marshaller<T>	m;
		return m.unmarshall(d);
	}
};


template<typename Key, typename Value, typename Datastore = inline_data_store<Value> >
struct database;
struct transaction;

struct environment : noncopyable {
	static environment	*open(string const &path);
	static environment	*create(string const &path);
	~environment();

	template<typename Key, typename Value>
	database<Key, Value>  *open_database(string const &path);
	template<typename Key, typename Value, typename Datastore>
	database<Key, Value, Datastore>  *open_database(string const &);
	template<typename Key, typename Value, typename Datastore>
	database<Key, Value, Datastore>  *open_database(string const &, Datastore *);

	template<typename Key, typename Value>
	database<Key, Value> *create_database(string const &path);
	template<typename Key, typename Value, typename Datastore>
	database<Key, Value, Datastore> *create_database(string const &path);
	template<typename Key, typename Value, typename Datastore>
	database<Key, Value, Datastore> *create_database(string const &, Datastore *);

	int	error		(void) const;
	string	strerror	(void) const;
	void	close		(void);

	struct transaction *transaction(void);

private:
	template<typename Key, typename Value, typename Datastore>
	friend struct database;
	friend struct transaction;

	explicit environment(string const &path, uint32_t flags);

	DB_ENV	*_env;
	int	 _error;
};

template<typename Key, typename Value, typename Datastore>
struct database : noncopyable {
	int	error		(void) const;
	string	strerror	(void) const;
	void	close		(void);

	~database();

	bool	 put(Key const &key, Value &value, transaction *);
	bool	 put(Key const &key, Value &value);
	Value	*get(Key const &key, transaction *);
	Value	*get(Key const &key);
	bool	 del(Key const &key, transaction *);
	bool	 del(Key const &key);

private:
	friend struct environment;

	explicit database(environment *, string const &, uint32_t, Datastore *);
	static void errcall(DB_ENV const *, char const *pfx, char const *msg);

	DB		*_db;
	environment	*_env;
	int		 _error;
	Datastore	*_store;
};

struct transaction {
	~transaction();

	bool	 commit(void);
	bool	 rollback(void);

	int	 error(void) const;
	string	 strerror(void) const;

private:
	friend struct environment;
	template<typename Key, typename Value, typename Datastore>
	friend struct database;

	transaction(environment *);
	DB_TXN	*_txn;
	int	 _error;
};

struct marshalling_buffer {
	marshalling_buffer()
		: _buf(NULL)
		, _size(0)
		, _bufsz(0)
	{}

	marshalling_buffer(char const *buf, uint32_t sz)
		: _buf(const_cast<char *>(buf))
		, _size(0)
		, _bufsz(sz)
	{}
		
	~marshalling_buffer(void) {
	}

	void reserve(size_t size) {
		_bufsz = size;
		_buf = new char[size];
	}

	template<typename T>
	void append(T const &);
	
	template<typename charT, typename traits, typename allocator>
	void append(basic_string<charT, traits, allocator> const &);

	void append_bytes(char const *buf, size_t s) {
		assert(_size + s <= _bufsz);
		memcpy(_buf + _size, buf, s);
		_size += s;
	}

	char const *buffer(void) const {
		return _buf;
	}

	size_t size(void) const {
		return _size;
	}

	template<typename T>
	bool extract(T &);

	template<typename charT, typename traits, typename allocator>
	bool extract(basic_string<charT, traits, allocator> &);
	
	bool extract_bytes(char *b, size_t s) {
		if (_size + s > _bufsz)
			return false;
		memcpy(b, _buf + _size, s);
		_size += s;
		return true;
	}

private:
	char	*_buf;
	size_t	 _size;
	size_t	 _bufsz;
	bool	 _delete;
};

template<>
void
marshalling_buffer::append<imstring>(imstring const &);

template<>
bool
marshalling_buffer::extract<imstring>(imstring &);

template<typename T>
void
marshalling_buffer::append(T const &o)
{
	append_bytes((char const *)&o, sizeof(o));
}

template<typename T>
bool
marshalling_buffer::extract(T &o)
{
	return extract_bytes((char *) &o, sizeof(o));
}

template<typename Key, typename Value>
database<Key, Value> *
environment::open_database(string const &name)
{
	return new database<Key, Value>(this, name, 0, new inline_data_store<Value>);
}

template<typename Key, typename Value, typename Datastore>
database<Key, Value, Datastore> *
environment::open_database(string const &name)
{
	return new database<Key, Value, Datastore>(this, name, 0, new Datastore);
}

template<typename Key, typename Value, typename Datastore>
database<Key, Value, Datastore> *
environment::open_database(string const &name, Datastore *d)
{
	return new database<Key, Value, Datastore>(this, name, 0, d);
}

template<typename Key, typename Value>
database<Key, Value> *
environment::create_database(string const &name)
{
	return new database<Key, Value>(this, name, DB_CREATE, new inline_data_store<Value>);
}

template<typename Key, typename Value, typename Datastore>
database<Key, Value, Datastore> *
environment::create_database(string const &name)
{
	return new database<Key, Value, Datastore>(this, name, DB_CREATE,
		new Datastore);
}

template<typename Key, typename Value, typename Datastore>
database<Key, Value, Datastore> *
environment::create_database(string const &name, Datastore *d)
{
	return new database<Key, Value, Datastore>(this, name, DB_CREATE, d);
}

template<typename Key, typename Value, typename Datastore>
database<Key, Value, Datastore>::database(environment *env, string const &path,
		uint32_t flags, Datastore *d)
	: _env(env)
	, _store(d)
{
	_error = db_create(&_db, env->_env, 0);
	if (_error != 0) {
		_db->close(_db, 0);
		_db = NULL;
		return;
	}

	_error = _db->open(_db, NULL, path.c_str(), NULL, DB_HASH,
			DB_THREAD | DB_AUTO_COMMIT | flags, 0);
	if (_error != 0) {
		_db->close(_db, 0);
		_db = NULL;
		return;
	}

	_db->set_errcall(_db, &database::errcall);
}

template<typename Key, typename Value, typename Datastore>
void
database<Key, Value, Datastore>::errcall(DB_ENV const *, char const *pfx, char const *msg)
{
	if (pfx)
		wlog(WLOG_WARNING, format("%s: %s") % pfx % msg);
	else
		wlog(WLOG_WARNING, msg);
}

template<typename Key, typename Value, typename Datastore>
int
database<Key, Value, Datastore>::error(void) const
{
	return _error;
}

template<typename Key, typename Value, typename Datastore>
string
database<Key, Value, Datastore>::strerror(void) const
{
	return db_strerror(_error);
}

template<typename Key, typename Value, typename Datastore>
database<Key, Value, Datastore>::~database(void)
{
	delete _store;
	if (_db)
		_db->close(_db, 0);
}

template<typename Key, typename Value, typename Datastore>
bool
database<Key, Value, Datastore>::put(Key const &key, Value &value)
{
	return put (key, value, NULL);
}

template<typename Key, typename Value, typename Datastore>
bool
database<Key, Value, Datastore>::put(Key const &key, Value &value, transaction *txn)
{
pair<char const *, uint32_t>	mkey, mvalue;
DBT				dbkey, dbvalue;
marshaller<Key>			keymarsh;
	memset(&dbkey, 0, sizeof(dbkey));
	memset(&dbvalue, 0, sizeof(dbvalue));
	mkey = keymarsh.marshall(key);
	mvalue = _store->store(value);
	dbkey.data = (void *) mkey.first;
	dbkey.size = mkey.second;
	dbvalue.data = (void *) mvalue.first;
	dbvalue.size = mvalue.second;
	
	_error = _db->put(_db, txn ? txn->_txn : NULL, &dbkey, &dbvalue,
		DB_NOOVERWRITE | (txn ? 0 : DB_AUTO_COMMIT));
	delete[] mkey.first;
	delete[] mvalue.first;
	if (_error != 0) {
		_store->unstore(value);
		return false;
	}
	return true;
}

template<typename Key, typename Value, typename Datastore>
Value *
database<Key, Value, Datastore>::get(Key const &key)
{
	return get(key, NULL);
}

template<typename Key, typename Value, typename Datastore>
Value *
database<Key, Value, Datastore>::get(Key const &key, transaction *txn)
{
pair<char const *, uint32_t>	mkey;
DBT				dbkey, dbvalue;
marshaller<Key>			keymarsh;
	memset(&dbkey, 0, sizeof(dbkey));
	memset(&dbvalue, 0, sizeof(dbvalue));
	mkey = keymarsh.marshall(key);
	dbkey.data = (void *) mkey.first;
	dbkey.size = mkey.second;
	dbvalue.flags = DB_DBT_MALLOC;
	_error = _db->get(_db, txn ? txn->_txn : NULL, &dbkey, &dbvalue, 0);
	if (_error != 0)
		return NULL;
Value	*ret;
	ret = _store->retrieve(pair<char const *, uint32_t>(
			(char const *) dbvalue.data, dbvalue.size));
	free(dbvalue.data);
	return ret;
}

template<typename Key, typename Value, typename Datastore>
bool
database<Key, Value, Datastore>::del(Key const &key)
{
	return del(key, NULL);
}

template<typename Key, typename Value, typename Datastore>
bool
database<Key, Value, Datastore>::del(Key const &key, transaction *txn)
{
pair<char const *, uint32_t>	mkey;
DBT				dbkey;
marshaller<Key>			keymarsh;
	memset(&dbkey, 0, sizeof(dbkey));
	mkey = keymarsh.marshall(key);
	dbkey.data = (void *)mkey.first;
	dbkey.size = mkey.second;
	_error = _db->del(_db, txn ? txn->_txn : NULL, &dbkey, 0);
	if (_error != 0)
		return false;
	return true;
}

template<typename Key, typename Value, typename Datastore>
void
database<Key, Value, Datastore>::close(void)
{
	_db->close(_db, 0);
	_db = NULL;
}

} // namespace db

#endif
