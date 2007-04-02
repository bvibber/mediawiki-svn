/* $Id$ */
/*
 * Six degrees of Wikipedia: Database cacher.
 * This source code is released into the public domain.
 */

#include <iostream>
#include <cstdio>

#include <boost/format.hpp>

#include <pthread.h>

#include "bdb_adjacency_store.h"

#define DB_TYPE DB_HASH

void *chkp(void *arg)
{
	DB_ENV *env = static_cast<DB_ENV *>(arg);
	for (;;) {
		sleep(40);
		env->txn_checkpoint(env, 0, 0, 0);
	}
	return 0;
}

bdb_adjacency_store::bdb_adjacency_store(void)
	: env(0)
{
}

int
extract_title(DB *, DBT const *, DBT const *pdata, DBT *skey)
{
	std::memset(skey, 0, sizeof(*skey));
	skey->data = (char *)pdata->data + 4;
	skey->size = pdata->size - 4;
	return 0;
}

void
bdb_adjacency_store::open(std::string const &path, bdb_adjacency_store::open_mode mode)
{
	last_error = db_env_create(&env, 0);
	if (last_error != 0) {
		env = 0;
		return;
	}

	env->set_errfile(env, stdout);
	last_error = env->set_cachesize(env, 0, 256 * 1024 * 1024, 1);
	if (last_error != 0) {
		env = 0;
		return;
	}

	//last_error = env->set_flags(env, DB_LOG_INMEMORY, 1);
	if (last_error != 0) {
		env = 0;
		return;
	}

	env->set_lg_bsize(env, 32 * 1024 * 1024);
	env->set_lk_max_locks(env, 10000);
	env->set_lk_max_lockers(env, 10000);
	env->set_lk_max_objects(env, 10000);

	uint32_t flags = DB_INIT_LOCK | DB_INIT_LOG | DB_INIT_TXN | DB_INIT_MPOOL | DB_THREAD /*| DB_PRIVATE*/;
	if (mode == write_open)
		flags |= DB_CREATE | DB_INIT_LOCK | DB_RECOVER;
	last_error = env->open(env, path.c_str(), flags, 0);
	if (last_error != 0) {
		env->close(env, 0);
		env = 0;
		return;
	}

	last_error = env->set_flags(env, /* DB_DIRECT_DB | DB_DIRECT_LOG |*/ DB_TXN_NOSYNC | DB_LOG_AUTOREMOVE, 1);
	if (last_error != 0) {
		env->close(env, 0);
		env = 0;
		return;
	}

	DB_TXN *txn;
	last_error = env->txn_begin(env, NULL, &txn, 0);
	if (last_error != 0) {
		env->close(env, 0);
		env = 0;
		return;
	}


	last_error = db_create(&adjacencies, env, 0);
	if (last_error != 0) {
		env->close(env, 0);
		env = 0;
		return;
	}

	adjacencies->set_pagesize(adjacencies, 16384);
	last_error = adjacencies->open(adjacencies, txn, "adjacencies", NULL, DB_TYPE,
	                         DB_THREAD | DB_CREATE, 0);
	if (last_error != 0) {
		env->close(env, 0);
		env = 0;
		return;
	}

	last_error = db_create(&text_ids, env, 0);
	if (last_error != 0) {
		env->close(env, 0);
		env = 0;
		return;
	}

	text_ids->set_pagesize(text_ids, 16384);
	last_error = text_ids->open(text_ids, txn, "text_id", NULL, DB_TYPE,
	                         DB_THREAD | DB_CREATE, 0);
	if (last_error != 0) {
		env->close(env, 0);
		env = 0;
		return;
	}

	last_error = db_create(&titles, env, 0);
	if (last_error != 0) {
		env->close(env, 0);
		env = 0;
		return;
	}

	titles->set_pagesize(titles, 16384);
	last_error = titles->open(titles, txn, "titles", NULL, DB_TYPE,
				DB_THREAD | DB_CREATE, 0);
	if (last_error != 0) {
		env->close(env, 0);
		env = 0;
		return;
	}

	last_error = db_create(&titles_byname, env, 0);
	if (last_error != 0) {
		env->close(env, 0);
		env = 0;
		return;
	}
	titles_byname->set_pagesize(titles_byname, 16384);
	titles_byname->set_flags(titles_byname, DB_DUP | DB_DUPSORT);
	last_error = titles_byname->open(titles_byname, txn, "titles_byname", NULL,
			DB_TYPE, DB_THREAD | DB_CREATE, 0);
	if (last_error != 0) {
		env->close(env, 0);
		env = 0;
		return;
	}
	last_error = titles->associate(titles, txn, titles_byname, extract_title, 0);
	if (last_error != 0) {
		env->close(env, 0);
		env = 0;
		return;
	}
	txn->commit(txn, 0);

	pthread_t tid;
	pthread_create(&tid, NULL, chkp, env);
}

void
bdb_adjacency_store::close(void)
{
	text_ids->close(text_ids, 0);
	adjacencies->close(adjacencies, 0);
	titles_byname->close(titles_byname, 0);
	titles->close(titles, 0);
	env->close(env, 0);
}

bdb_adjacency_transaction::bdb_adjacency_transaction(bdb_adjacency_store &s)
	: store(s)
{
	store.env->txn_begin(store.env, NULL, &txn, 0);
}

bdb_adjacency_transaction::~bdb_adjacency_transaction(void)
{
	if (txn)
		rollback();
}

void
bdb_adjacency_transaction::add_title(std::string const &wiki, page_id_t page, std::string const &name, text_id_t text_id)
{
	std::vector<unsigned char> buf(4 + 1 + wiki.size() + name.size());
	buf[0] = (page & 0xFF000000) >> 24;
	buf[1] = (page & 0x00FF0000) >> 16;
	buf[2] = (page & 0x0000FF00) >> 8;
	buf[3] = (page & 0x000000FF);
	buf[4] = (unsigned char) wiki.size();

	std::memcpy(&buf[5], wiki.data(), wiki.size());
	std::memcpy(&buf[5 + wiki.size()], name.data(), name.size());
	
	DBT key, value;
	std::memset(&key, 0, sizeof(key));
	std::memset(&value, 0, sizeof(value));

	key.size = 5 + wiki.size();
	key.data = &buf[0];
	value.size = buf.size();
	value.data = &buf[0];

	store.last_error = store.titles->put(store.titles, txn, &key, &value, 0);
	if (store.last_error != 0)
		return;

	std::vector<unsigned char> tbuf(5 + wiki.size());
	tbuf[0] = (text_id & 0xFF000000) >> 24;
	tbuf[1] = (text_id & 0x00FF0000) >> 16;
	tbuf[2] = (text_id & 0x0000FF00) >> 8;
	tbuf[3] = (text_id & 0x000000FF);
	tbuf[4] = (unsigned char) wiki.size();
	std::memcpy(&tbuf[5], wiki.data(), wiki.size());

	std::memset(&value, 0, sizeof(value));
	value.size = tbuf.size();
	value.data = &tbuf[0];
	store.last_error = store.text_ids->put(store.text_ids, txn, &key, &value, 0);
}

boost::optional<std::string>
bdb_adjacency_store::name_for_id(std::string const &wiki, page_id_t page)
{
	std::vector<unsigned char> buf(5 + wiki.size());
	buf[0] = (page & 0xFF000000) >> 24;
	buf[1] = (page & 0x00FF0000) >> 16;
	buf[2] = (page & 0x0000FF00) >> 8;
	buf[3] = (page & 0x000000FF);
	buf[4] = (unsigned char) wiki.size();
	std::memcpy(&buf[5], wiki.data(), wiki.size());

	DBT key, value;
	std::memset(&key, 0, sizeof(key));
	std::memset(&value, 0, sizeof(value));
	key.size = buf.size();
	key.data = &buf[0];
	value.flags = DB_DBT_MALLOC;

	int i = titles->get(titles, 0, &key, &value, 0);
	if (i == DB_NOTFOUND)
		return boost::optional<std::string>();

	char *d = (char *)value.data + 4;
	int offs = (int)*d;
	d += offs + 1;
	std::string ret(d, value.size - (offs + 5));
	std::free(value.data);
	return ret;
}

boost::optional<text_id_t>
bdb_adjacency_store::text_id_for_page(std::string const &wiki, page_id_t page)
{
	std::vector<unsigned char> buf(5 + wiki.size());
	buf[0] = (page & 0xFF000000) >> 24;
	buf[1] = (page & 0x00FF0000) >> 16;
	buf[2] = (page & 0x0000FF00) >> 8;
	buf[3] = (page & 0x000000FF);
	buf[4] = (unsigned char)wiki.size();
	std::memcpy(&buf[5], wiki.data(), wiki.size());

	DBT key, value;
	std::memset(&key, 0, sizeof(key));
	std::memset(&value, 0, sizeof(value));
	key.size = buf.size();
	key.data = &buf[0];
	value.flags = DB_DBT_MALLOC;

	int i = text_ids->get(text_ids, 0, &key, &value, 0);
	if (i == DB_NOTFOUND)
		return boost::optional<page_id_t>();
	char *p = (char *)value.data;
	page_id_t ret =   (static_cast<unsigned char>(*(p + 0)) << 24) 
			| (static_cast<unsigned char>(*(p + 1)) << 16) 
			| (static_cast<unsigned char>(*(p + 2)) << 8) 
			|  static_cast<unsigned char>(*(p + 3));
	std::free(value.data);
	return ret;
}

boost::optional<page_id_t>
bdb_adjacency_store::id_for_name(std::string const &wiki, std::string const &name)
{
	std::vector<char> buf(1 + wiki.size() + name.size());
	buf[0] = wiki.size();
	std::memcpy(&buf[1], wiki.data(), wiki.size());
	std::memcpy(&buf[1 + wiki.size()], name.data(), name.size());

	DBT key, value;
	std::memset(&key, 0, sizeof(key));
	std::memset(&value, 0, sizeof(value));
	key.size = buf.size();
	key.data = &buf[0];
	value.flags = DB_DBT_MALLOC;

	int i = titles_byname->get(titles_byname, 0, &key, &value, 0);
	if (i == DB_NOTFOUND)
		return boost::optional<page_id_t>();
	char *p = (char *)value.data;
	page_id_t ret =   (static_cast<unsigned char>(*(p + 0)) << 24) 
			| (static_cast<unsigned char>(*(p + 1)) << 16) 
			| (static_cast<unsigned char>(*(p + 2)) << 8) 
			|  static_cast<unsigned char>(*(p + 3));
	std::free(value.data);
	return ret;
}

void
bdb_adjacency_transaction::commit(void)
{
	store.last_error = txn->commit(txn, 0);
	txn = 0;
	if (store.last_error != 0)
		return;
}

void
bdb_adjacency_transaction::rollback(void)
{
	store.last_error = txn->abort(txn);
	txn = 0;
	std::cout << "aborting\n";
}

void
bdb_adjacency_transaction::add_adjacency(std::string const &wiki, page_id_t from, page_id_t to)
{ 
	std::set<page_id_t> adj = get_adjacencies(wiki, from);
	if (adj.find(to) != adj.end())
		return;
	adj.insert(to);
	set_adjacencies(wiki, from, adj);
}

std::set<page_id_t>
bdb_adjacency_transaction::get_adjacencies(std::string const &wiki, page_id_t from)
{
	std::set<page_id_t> ret;
	std::vector<unsigned char> keyd(5 + wiki.size());
	keyd[0] = (from & 0xFF000000) >> 24;
	keyd[1] = (from & 0x00FF0000) >> 16;
	keyd[2] = (from & 0x0000FF00) >> 8;
	keyd[3] = (from & 0x000000FF);
	keyd[4] = (unsigned char) wiki.size();
	std::memcpy(&keyd[5], wiki.data(), wiki.size());

	DBT key, value;
	std::memset(&key, 0, sizeof(key));
	std::memset(&value, 0, sizeof(value));
	key.size = keyd.size();
	key.data = &keyd[0];

	std::memset(&value, 0, sizeof(value));
	value.flags = DB_DBT_MALLOC;

	store.last_error = store.adjacencies->get(store.adjacencies, txn, &key, &value, 0);
	if (store.last_error == DB_NOTFOUND) {
		store.last_error = 0;
		return std::set<page_id_t>();
	}
	if (store.last_error != 0) {
		std::cerr << "error getting page: " << store.strerror() << '\n';
		return std::set<page_id_t>();
	}

	int nadj = value.size / 4;
	unsigned char *p = static_cast<unsigned char *>(value.data);
	while (nadj--) {
		page_id_t id =    (static_cast<unsigned char>(*(p + 0)) << 24) 
				| (static_cast<unsigned char>(*(p + 1)) << 16) 
				| (static_cast<unsigned char>(*(p + 2)) << 8) 
				|  static_cast<unsigned char>(*(p + 3));
		p += 4;
		ret.insert(id);
	}
	std::free(value.data);

	return ret;
}

void
bdb_adjacency_transaction::set_adjacencies(std::string const &wiki, page_id_t from, std::set<page_id_t> const &adj)
{
	std::vector<unsigned char> buf(adj.size() * sizeof(page_id_t));
	unsigned char *pos = &buf[0];
	for (std::set<page_id_t>::iterator it = adj.begin(), end = adj.end(); it != end; ++it) {
		*(pos + 0) = (*it & 0xFF000000) >> 24;
		*(pos + 1) = (*it & 0x00FF0000) >> 16;
		*(pos + 2) = (*it & 0x0000FF00) >> 8;
		*(pos + 3) = (*it & 0x000000FF);
		pos += sizeof(page_id_t);
	}

	DBT key;
	DBT value;

	std::vector<unsigned char> keyd(5 + wiki.size());
	keyd[0] = (from & 0xFF000000) >> 24;
	keyd[1] = (from & 0x00FF0000) >> 16;
	keyd[2] = (from & 0x0000FF00) >> 8;
	keyd[3] = (from & 0x000000FF);
	keyd[4] = wiki.size();
	std::memcpy(&keyd[5], wiki.data(), wiki.size());

	std::memset(&key, 0, sizeof(key));
	std::memset(&value, 0, sizeof(value));
	key.size = keyd.size();
	key.data = &keyd[0];
	value.size = buf.size();
	value.data = &buf[0];

	store.last_error = store.adjacencies->put(store.adjacencies, txn, &key, &value, 0);
}

int
bdb_adjacency_store::error(void) const
{
	return last_error;
}

std::string
bdb_adjacency_store::strerror(void) const
{
	return db_strerror(last_error);
}
