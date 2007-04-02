/*
 * Six degrees of Wikipedia: Database cacher.
 * This source code is released into the public domain.
 */
/* $Id$ */

#ifndef BDB_ADJACENCY_STORE_H
#define BDB_ADJACENCY_STORE_H

#include <string>
#include <set>

#include <db.h>

#include "linksc.h"

struct bdb_adjacency_transaction;

struct bdb_adjacency_store {
	enum open_mode {
		read_open,
		update_open,
		write_open
	};

	bdb_adjacency_store(void);
	void open(std::string const &, open_mode);
	void close(void);

	int error(void) const;
	std::string strerror(void) const;

	boost::optional<std::string> name_for_id(std::string const &wiki, page_id_t);
	boost::optional<page_id_t> id_for_name(std::string const &wiki, std::string const &);
	boost::optional<text_id_t> text_id_for_page(std::string const &wiki, page_id_t);

private:
	friend struct bdb_adjacency_transaction;

	DB_ENV *env;
	DB *adjacencies;
	DB *titles;
	DB *titles_byname;
	DB *text_ids;
	int last_error;
};

struct bdb_adjacency_transaction {
	bdb_adjacency_transaction(bdb_adjacency_store &);
	~bdb_adjacency_transaction();
	
	void add_adjacency(std::string const &wiki, page_id_t from, page_id_t to);
	void add_title(std::string const &wiki, page_id_t page, std::string const &name, text_id_t text_id);

	std::set<page_id_t> get_adjacencies(std::string const &wiki, page_id_t);
	void set_adjacencies(std::string const &wiki, page_id_t, std::set<page_id_t> const &);

	void commit(void);
	void rollback(void);

private:
	bdb_adjacency_store &store;
	DB_TXN *txn;
};

#endif
