/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* dbwrap: C++ Berkeley DB wrapper.					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "stdinc.h"
#ifdef __INTEL_COMPILER
# pragma hdrstop
#endif

#include "dbwrap.h"
#include "mbuffer.h"

namespace db {

environment *
environment::open(string const &path)
{
	return new environment(path, 0);
}

environment *
environment::create(string const &path)
{
	return new environment(path, DB_CREATE);
}

environment::environment(string const &path, uint32_t flags)
	: _env(NULL)
	, _error(0)
{
	_error = db_env_create(&_env, 0);
	if (_error != 0) {
		_env = NULL;
		return;
	}

	_env->set_errfile(_env, stderr);

	_error = _env->open(_env, path.c_str(), 
		DB_INIT_LOCK | DB_INIT_LOG | DB_INIT_TXN | DB_RECOVER |
		DB_INIT_MPOOL | DB_THREAD | DB_CREATE | DB_RECOVER | flags, 0);
	if (_error != 0) {
		_env = NULL;
		return;
	}
}

int
environment::error(void) const
{
	return _error;
}

string
environment::strerror(void) const
{
	return db_strerror(_error);
}

environment::~environment(void)
{
	if (_env)
		_env->close(_env, 0);
}

void
environment::close(void)
{
	_env->close(_env, 0);
	_env = NULL;
}

transaction *
environment::transaction(void)
{
	return new struct transaction(this);
}

transaction::transaction(environment *env)
{
	_error = env->_env->txn_begin(env->_env, NULL, &_txn, 0);
	if (_error != 0) {
		wlog.warn(format("error starting transaction: %s")
			% strerror());
		_txn = NULL;
	}
}

bool
transaction::commit(void)
{
	_error = _txn->commit(_txn, 0);
	_txn = NULL;
	return _error == 0;
}

bool
transaction::rollback(void)
{
	_error = _txn->abort(_txn);
	_txn = NULL;
	return _error == 0;
}

transaction::~transaction(void)
{
	assert(!_txn);
}

int
transaction::error(void) const
{
	return _error;
}

string
transaction::strerror(void) const
{
	return db_strerror(_error);
}

} // namespace db
