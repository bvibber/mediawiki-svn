/* $Id$ */
/*
 * Six degrees of Wikipedia: Database cacher.
 * This source code is released into the public domain.
 */

#include <iostream>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <string>
#include <fstream>
#include <cerrno>

#include <boost/format.hpp>
#include <boost/lexical_cast.hpp>

#include <unistd.h>
#include <mysql.h>

#include "linksc.h"
#include "bdb_adjacency_store.h"
#include "defs.h"

void build_for(MYSQL &mysql, std::string const &db);

void
mysql_query_ordie(MYSQL* mysql, char const *query)
{
	int i = mysql_query(mysql, query);
	if (i) {
		std::cerr << "mysql query failed: " << mysql_error(mysql) << '\n';
		std::exit(8);
	}
}

struct link_entry {
	std::string wiki;
	page_id_t from, to;
};

static std::vector<link_entry> pending;
static bdb_adjacency_store store;

static void flush()
{
	bdb_adjacency_transaction trans(store);
	for (std::size_t i = 0, end = pending.size(); i < end; ++i) {
		trans.add_adjacency(pending[i].wiki, pending[i].from, pending[i].to);
	}
	trans.commit();

	if (store.error()) {
		std::cout << "adding adjacency: " << store.strerror() << '\n';
		std::exit(1);
	}
	pending.clear();
}

struct title_entry {
	std::string wiki;
	page_id_t page;
	text_id_t text;
	std::string name;
};

static std::vector<title_entry> pending_titles;
static void flush_titles()
{
	bdb_adjacency_transaction trans(store);

	for (std::size_t i = 0, end = pending_titles.size(); i < end; ++i) {
		trans.add_title(pending_titles[i].wiki,
				pending_titles[i].page, pending_titles[i].name, pending_titles[i].text);
	}

	trans.commit();
	if (store.error()) {
		std::cout << "adding title: " << store.strerror() << '\n';
		std::exit(1);
	}
	pending_titles.clear();
}

int
main(int, char *argv[])
{
	MYSQL_ROW arow;
	MYSQL mysql;
	mysql_init(&mysql);
	mysql_options(&mysql, MYSQL_READ_DEFAULT_GROUP, "linksd");
 
	if (!mysql_real_connect(&mysql, NULL, NULL, NULL, argv[1], 0, NULL, 0)) {
		std::cerr << "mysql connect error: " << mysql_error(&mysql) << '\n';
		return 1;
	}

	store.open(DB, bdb_adjacency_store::write_open);

	if (store.error()) {
		std::cout << "opening database: " << store.strerror() << '\n';
		return 1;
	}

	mysql_query_ordie(&mysql, "SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
	mysql_query_ordie(&mysql, "SET SESSION NET_READ_TIMEOUT = 3600");
	mysql_query_ordie(&mysql, "SET SESSION NET_WRITE_TIMEOUT = 3600");
	std::vector<std::string> databases;

	if (argv[1])
		build_for(mysql, argv[1]);
	else {
		mysql_query_ordie(&mysql, "SHOW DATABASES LIKE '%\\_p'");
		MYSQL_RES *res = mysql_use_result(&mysql);
		while ((arow = mysql_fetch_row(res)) != NULL) {
			databases.push_back(arow[0]);
		}
		mysql_free_result(res);

		for (std::size_t i = 0, end = databases.size(); i < end; ++i)
			build_for(mysql, databases[i]);

		store.close();
	}
}
	

void
build_for(MYSQL &mysql, std::string const &db)
{
	mysql_query_ordie(&mysql, ("USE " + db).c_str());
	mysql_query_ordie(&mysql, 
                "SELECT page_id, pl_from FROM pagelinks,page "
                "WHERE pl_title=page_title and pl_namespace=page_namespace and page_namespace=0");
	MYSQL_RES *res = mysql_use_result(&mysql);

	MYSQL_ROW arow;
	int i = 0;
	while ((arow = mysql_fetch_row(res)) != NULL) {
		if ((i++ % 10000) == 0)
			std::cout << '\r' << db << ": " << ": " << (i - 1) << std::flush;

		if ((i % 500) == 0)
			flush();

		link_entry e;
		e.wiki = db;
		e.from = boost::lexical_cast<page_id_t>(arow[1]);
		e.to = boost::lexical_cast<page_id_t>(arow[0]);
		pending.push_back(e);
	}
	std::cout << '\n';
	flush();

	mysql_free_result(res);

	mysql_query_ordie(&mysql, "SELECT page_title,page_id,page_latest FROM page WHERE page_namespace=0");
	res = mysql_use_result(&mysql);
	i = 0;
	while ((arow = mysql_fetch_row(res)) != NULL) {
		if ((i++ % 10000) == 0)
			std::cout << '\r' << db << ": " << (i - 1) << "..." << std::flush;
		if ((i % 500) == 0)
			flush_titles();

		title_entry t;
		t.wiki = db;
		t.page = boost::lexical_cast<page_id_t>(arow[1]);
		t.name = arow[0];
		t.text = boost::lexical_cast<text_id_t>(arow[2]);
		pending_titles.push_back(t);
	}
	std::cout << '\n';
	flush_titles();
	mysql_free_result(res);
}
