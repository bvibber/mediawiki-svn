/* $Id$ */
/*
 * Six degrees of Wikipedia: Database cacher.
 * This source code is released into the public domain.
 */
/*
 * Keys in the database are all strings.
 *
 * Adjacency key:     "enwiki_p/123456"
 * Title key:         "enwiki_p/123456"
 * Text id key:       "enwiki_p/123456"
 * Title-by-name key: "enwiki_p/Main_Page"
 */

#include <iostream>
#include <cstdio>

#include <boost/format.hpp>
#include <boost/lexical_cast.hpp>

#include <pthread.h>

#include "bdb_adjacency_store.h"
#include "log.h"

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

/*
 * Given a wiki/page_id key for titles, create a titles_byname key, wiki/name.
 */
int
extract_title(DB *, DBT const *pkey, DBT const *pdata, DBT *skey)
{
	std::string okey((char *)pkey->data, pkey->size);
	std::string::size_type i;
	if ((i = okey.find('/')) == std::string::npos)
		return 1;

	std::string newkey = str(boost::format("%s/%s") % okey.substr(0, i) % 
					std::string((char *)pdata->data, pdata->size));
	std::memset(skey, 0, sizeof(*skey));
	skey->data = std::malloc(newkey.size());
	std::memcpy(skey->data, newkey.data(), newkey.size());
	skey->size = newkey.size();
	skey->flags = DB_DBT_APPMALLOC;
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
	std::string keys = str(boost::format("%s/%s") % wiki % page);
	
	DBT key, value;
	std::memset(&key, 0, sizeof(key));
	std::memset(&value, 0, sizeof(value));

	key.size = keys.size();
	key.data = &keys[0];
	value.size = name.size();
	value.data = (void *)name.data();

	store.last_error = store.titles->put(store.titles, txn, &key, &value, 0);
	if (store.last_error != 0)
		return;

	std::string tidval = boost::lexical_cast<std::string>(text_id);
	std::memset(&value, 0, sizeof(value));
	value.size = tidval.size();
	value.data = (void *)tidval.data();
	store.last_error = store.text_ids->put(store.text_ids, txn, &key, &value, 0);
}

boost::optional<std::string>
bdb_adjacency_store::name_for_id(std::string const &wiki, page_id_t page)
{
	std::string keys = str(boost::format("%s/%d") % wiki % page);

	DBT key, value;
	std::memset(&key, 0, sizeof(key));
	std::memset(&value, 0, sizeof(value));
	key.size = keys.size();
	key.data = (char *)keys.data();
	value.flags = DB_DBT_MALLOC;

	int i = titles->get(titles, 0, &key, &value, 0);
	if (i == DB_NOTFOUND)
		return boost::optional<std::string>();

	std::string name((char *)value.data, value.size);
	std::free(value.data);
	return name;
}

boost::optional<text_id_t>
bdb_adjacency_store::text_id_for_page(std::string const &wiki, page_id_t page)
{
	std::string keys = str(boost::format("%s/%d") % wiki % page);

	DBT key, value;
	std::memset(&key, 0, sizeof(key));
	std::memset(&value, 0, sizeof(value));
	key.size = keys.size();
	key.data = (void *)keys.data();
	value.flags = DB_DBT_MALLOC;

	int i = text_ids->get(text_ids, 0, &key, &value, 0);
	if (i == DB_NOTFOUND)
		return boost::optional<page_id_t>();

	text_id_t ret = boost::lexical_cast<text_id_t>(
				std::string((char *)value.data, value.size));

	std::free(value.data);
	return ret;
}

boost::optional<page_id_t>
bdb_adjacency_store::id_for_name(std::string const &wiki, std::string const &name)
{
	std::string keys = str(boost::format("%s/%s") % wiki % name);

	DBT key, value, pkey;
	std::memset(&key, 0, sizeof(key));
	std::memset(&pkey, 0, sizeof(pkey));
	std::memset(&value, 0, sizeof(value));
	key.size = keys.size();
	key.data = (void *)keys.data();
	pkey.flags = DB_DBT_MALLOC;
	value.flags = DB_DBT_MALLOC;

	int i = titles_byname->pget(titles_byname, 0, &key, &pkey, &value, 0);
	if (i == DB_NOTFOUND)
		return boost::optional<page_id_t>();

	std::string okey((char *)pkey.data, pkey.size);
	std::string ret = okey.substr(okey.find('/') + 1);
	std::free(value.data);
	std::free(pkey.data);
	return boost::lexical_cast<page_id_t>(ret);
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
	std::string keys = str(boost::format("%s/%d") % wiki % from);
	std::set<page_id_t> ret;

	DBT key, value;
	std::memset(&key, 0, sizeof(key));
	std::memset(&value, 0, sizeof(value));
	key.size = keys.size();
	key.data = (char *)keys.data();

	std::memset(&value, 0, sizeof(value));
	value.flags = DB_DBT_MALLOC;

	store.last_error = store.adjacencies->get(store.adjacencies, txn, &key, &value, 0);
	if (store.last_error == DB_NOTFOUND) {
		store.last_error = 0;
		return std::set<page_id_t>();
	}
	if (store.last_error != 0) {
		logger::error(str(boost::format("error getting adjacencies for /%s/: %s") % store.strerror()));
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
	std::string keys = str(boost::format("%s/%d") % wiki % from);
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

	std::memset(&key, 0, sizeof(key));
	std::memset(&value, 0, sizeof(value));
	key.size = keys.size();
	key.data = (char *)keys.data();
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
