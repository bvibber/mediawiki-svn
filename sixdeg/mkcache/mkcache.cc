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
#include <map>

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

bool
do_mysql_query(MYSQL* mysql, char const *query)
{
	int i = mysql_query(mysql, query);
	if (i) {
		std::cerr << "mysql query failed: " << mysql_error(mysql) << '\n';
		return false;
	}
	return true;
}


static bdb_adjacency_store store;

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
	
struct page_entry {
	text_id_t text;
	std::string name;
	std::set<page_id_t> adj;
};

void
build_for(MYSQL &mysql, std::string const &db)
{
	if (!do_mysql_query(&mysql, ("USE " + db).c_str()))
		return;

	if (!do_mysql_query(&mysql, 
                "SELECT p2.page_id, pl_from, p1.page_title, p1.page_latest FROM pagelinks,page p1,page p2 "
                "WHERE pl_from=p1.page_id AND pl_title=p2.page_title and pl_namespace=p2.page_namespace and p2.page_namespace=0"))
		return;

	MYSQL_RES *res = mysql_use_result(&mysql);

	/*
	 * First we cache the data for this wiki in RAM, then commit all once.  
	 * This avoids constantly (over)writing in the database.
	 */
	std::map<page_id_t, page_entry> cache;
	MYSQL_ROW arow;
	int i = 0;
	std::cout << db << ": 0" << std::flush;
	while ((arow = mysql_fetch_row(res)) != NULL) {
		if ((i++ % 10000) == 0)
			std::cout << '\r' << db << ": " << (i - 1) << std::flush;

		page_id_t from = boost::lexical_cast<page_id_t>(arow[1]);
		page_id_t to = boost::lexical_cast<page_id_t>(arow[0]);
		std::map<page_id_t, page_entry>::iterator it = cache.find(from);
		if (it == cache.end()) {
			page_entry e;
			e.name = arow[2];
			e.text = boost::lexical_cast<text_id_t>(arow[3]);
			it = cache.insert(std::make_pair(from, e)).first;
		}

		it->second.adj.insert(to);
	}
	mysql_free_result(res);

	bdb_adjacency_transaction *trans = new bdb_adjacency_transaction(store);
	std::cout << " flush to storage... " << std::flush;
	i = 0;
	for (std::map<page_id_t, page_entry>::iterator
			it = cache.begin(), end = cache.end();
			it != end; ++it) {
		if (++i == 10000) {
			trans->commit();
			delete trans;
			trans = new bdb_adjacency_transaction(store);
			std::cout << '.' << std::flush;
			i = 0;
		}

		trans->add_title(db, it->first, it->second.name, it->second.text);
		trans->set_adjacencies(db, it->first, it->second.adj);
	}
	trans->commit();
	delete trans;

	std::cout << " checkpoint..." << std::flush;
	store.checkpoint();
	
	std::cout << "\n";

#if 0
	if (!do_mysql_query(&mysql, "SELECT page_title,page_id,page_latest FROM page WHERE page_namespace=0"))
		return;

	std::cout << db << ": titles: 0" << std::flush;
	res = mysql_use_result(&mysql);
	bdb_adjacency_transaction ttrans(store);
	while ((arow = mysql_fetch_row(res)) != NULL) {
		if ((i++ % 10000) == 0)
			std::cout << "\r" << db << ": titles: " << (i - 1) << "..." << std::flush;
#if 0
		if ((i % 10000) == 0)
			flush_titles();

		title_entry t;
		t.wiki = db;
		t.page = boost::lexical_cast<page_id_t>(arow[1]);
		t.name = arow[0];
		t.text = boost::lexical_cast<text_id_t>(arow[2]);
		pending_titles.push_back(t);
#endif
		page_id_t page = boost::lexical_cast<page_id_t>(arow[1]);
		text_id_t text = boost::lexical_cast<text_id_t>(arow[2]);
		ttrans.add_title(db, page, arow[0], text);
	}
	std::cout << " flush to storage..." << std::flush;
	ttrans.commit();
	std::cout << '\n';
	flush_titles();
	mysql_free_result(res);
#endif
}
