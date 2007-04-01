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
}

bdb_adjacency_store::bdb_adjacency_store(void)
	: env(0)
{
}

int
extract_title(DB *, DBT const *, DBT const *pdata, DBT *skey)
{
	memset(skey, 0, sizeof(*skey));
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
	std::cout << "set cache\n";
	last_error = env->set_cachesize(env, 0, 256 * 1024 * 1024, 1);
	if (last_error != 0) {
		env = 0;
		return;
	}

	std::cout << "set flags\n";
	//last_error = env->set_flags(env, DB_LOG_INMEMORY, 1);
	if (last_error != 0) {
		env = 0;
		return;
	}

	std::cout << "open\n";

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

	std::cout << "flags2\n";
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
#if 0
	DB_MUTEX_STAT *stat;
	store.env->mutex_stat(store.env, &stat, 0);
	std::cout << boost::format("start trans %d mutex avail, %d in use, %d total, %d max\n")
			% stat->st_mutex_free % stat->st_mutex_inuse % stat->st_mutex_cnt % stat->st_mutex_inuse_max;
	std::free(stat);

	DB_TXN_STAT *tstat;
	store.env->txn_stat(store.env, &tstat, 0);
	std::cout << boost::format("%d txn active\n") % tstat->st_nactive;
	std::free(tstat);

	DB_LOCK_STAT *lstat;
	store.env->lock_stat(store.env, &lstat, 0);
	std::cout << boost::format("%d locks active max=%d\n") % lstat->st_nlocks % lstat->st_maxnlocks;
	std::free(lstat);
#endif

	store.env->txn_begin(store.env, NULL, &txn, 0);
}

bdb_adjacency_transaction::~bdb_adjacency_transaction(void)
{
	if (txn)
		rollback();
}

void
bdb_adjacency_transaction::add_title(page_id_t page, std::string const &name, text_id_t text_id)
{
	std::vector<unsigned char> buf(4 + name.size());
	buf[0] = (page & 0xFF000000) >> 24;
	buf[1] = (page & 0x00FF0000) >> 16;
	buf[2] = (page & 0x0000FF00) >> 8;
	buf[3] = (page & 0x000000FF);

	for (std::size_t i = 0; i < name.size(); ++i)
		buf[4 + i] = name[i];
	
	DBT key, value;
	memset(&key, 0, sizeof(key));
	memset(&value, 0, sizeof(value));

	key.size = 4;
	key.data = &buf[0];
	value.size = buf.size();
	value.data = &buf[0];

	store.last_error = store.titles->put(store.titles, txn, &key, &value, 0);
	if (store.last_error != 0)
		return;

	std::vector<unsigned char> tbuf(4);
	tbuf[0] = (text_id & 0xFF000000) >> 24;
	tbuf[1] = (text_id & 0x00FF0000) >> 16;
	tbuf[2] = (text_id & 0x0000FF00) >> 8;
	tbuf[3] = (text_id & 0x000000FF);

	memset(&value, 0, sizeof(value));
	value.size = 4;
	value.data = &tbuf[0];
	store.last_error = store.text_ids->put(store.text_ids, txn, &key, &value, 0);
}

boost::optional<std::string>
bdb_adjacency_store::name_for_id(page_id_t page)
{
	std::vector<unsigned char> buf(4);
	buf[0] = (page & 0xFF000000) >> 24;
	buf[1] = (page & 0x00FF0000) >> 16;
	buf[2] = (page & 0x0000FF00) >> 8;
	buf[3] = (page & 0x000000FF);

	DBT key, value;
	memset(&key, 0, sizeof(key));
	memset(&value, 0, sizeof(value));
	key.size = 4;
	key.data = &buf[0];
	value.flags = DB_DBT_MALLOC;

	int i = titles->get(titles, 0, &key, &value, 0);
	if (i == DB_NOTFOUND)
		return boost::optional<std::string>();

	std::string ret((char *)value.data + 4, (char *)value.data + value.size);
	std::free(value.data);
	return ret;
}

boost::optional<text_id_t>
bdb_adjacency_store::text_id_for_page(page_id_t page)
{
	std::vector<unsigned char> buf(4);
	buf[0] = (page & 0xFF000000) >> 24;
	buf[1] = (page & 0x00FF0000) >> 16;
	buf[2] = (page & 0x0000FF00) >> 8;
	buf[3] = (page & 0x000000FF);

	DBT key, value;
	memset(&key, 0, sizeof(key));
	memset(&value, 0, sizeof(value));
	key.size = 4;
	key.data = &buf[0];
	value.flags = DB_DBT_MALLOC;

	int i = text_ids->get(text_ids, 0, &key, &value, 0);
	if (i == DB_NOTFOUND)
		return boost::optional<text_id_t>();
	char *p = (char *) value.data;
	text_id_t ret =   (static_cast<unsigned char>(*(p + 0)) << 24) 
			| (static_cast<unsigned char>(*(p + 1)) << 16) 
			| (static_cast<unsigned char>(*(p + 2)) << 8) 
			|  static_cast<unsigned char>(*(p + 3));
	std::free(value.data);
	return ret;
}

boost::optional<page_id_t>
bdb_adjacency_store::id_for_name(std::string const &name)
{
	std::vector<char> buf(name.begin(), name.end());
	DBT key, value;
	memset(&key, 0, sizeof(key));
	memset(&value, 0, sizeof(value));
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
#if 0
	DB_MUTEX_STAT *stat;
	store.env->mutex_stat(store.env, &stat, 0);
	std::cout << boost::format("commit trans %d mutex avail, %d in use, %d total, %d max\n")
			% stat->st_mutex_free % stat->st_mutex_inuse % stat->st_mutex_cnt % stat->st_mutex_inuse_max;
	std::free(stat);
#endif

	store.last_error = txn->commit(txn, 0);
	txn = 0;
	if (store.last_error != 0)
		return;

#if 0
	store.last_error = store.env->txn_checkpoint(store.env, 0, 0, 0);
	if (store.last_error != 0)
		std::cout << "cannot checkpoint\n";
#endif
}

void
bdb_adjacency_transaction::rollback(void)
{
	store.last_error = txn->abort(txn);
	txn = 0;
	std::cout << "aborting\n";
}

void
bdb_adjacency_transaction::add_adjacency(page_id_t from, page_id_t to)
{
	std::set<page_id_t> adj = get_adjacencies(from);
	if (adj.find(to) != adj.end())
		return;
	adj.insert(to);
	set_adjacencies(from, adj);
}

std::set<page_id_t>
bdb_adjacency_transaction::get_adjacencies(page_id_t from)
{
	std::set<page_id_t> ret;
	unsigned char keyd[4];
	keyd[0] = (from & 0xFF000000) >> 24;
	keyd[1] = (from & 0x00FF0000) >> 16;
	keyd[2] = (from & 0x0000FF00) >> 8;
	keyd[3] = (from & 0x000000FF);

	DBT key, value;
	memset(&key, 0, sizeof(key));
	memset(&value, 0, sizeof(value));
	key.size = 4;
	key.data = keyd;

	memset(&value, 0, sizeof(value));
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
bdb_adjacency_transaction::set_adjacencies(page_id_t from, std::set<page_id_t> const &adj)
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

	unsigned char keyd[4];
	keyd[0] = (from & 0xFF000000) >> 24;
	keyd[1] = (from & 0x00FF0000) >> 16;
	keyd[2] = (from & 0x0000FF00) >> 8;
	keyd[3] = (from & 0x000000FF);

	memset(&key, 0, sizeof(key));
	memset(&value, 0, sizeof(value));
	key.size = 4;
	key.data = keyd;
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
