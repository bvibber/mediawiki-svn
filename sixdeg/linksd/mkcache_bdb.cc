/*
 * Six degrees of Wikipedia: Database cacher.
 * This source code is released into the public domain.
 */

// #pragma ident "@(#)mkcache.cc	1.1 05/11/21 21:00:29"

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

#define DB "/home/river/linksd-db"

void
mysql_query_ordie(MYSQL* mysql, char const *query)
{
	int i = mysql_query(mysql, query);
	if (i) {
		std::cerr << boost::format("mysql query failed: %s\n") % mysql_error(mysql);
		std::exit(8);
	}
}
int
main(int argc, char *argv[])
{
	MYSQL mysql;
	mysql_init(&mysql);
	mysql_options(&mysql, MYSQL_READ_DEFAULT_GROUP, "linksd");
 
	if (!mysql_real_connect(&mysql, NULL, NULL, NULL, argv[1], 0, NULL, 0)) {
		std::cerr << boost::format("mysql connect error: %s\n") % mysql_error(&mysql);
		return 1;
	}

	bdb_adjacency_store store;
	store.open(DB, bdb_adjacency_store::write_open);

	if (store.error()) {
		std::cout << boost::format("opening database: %s\n") % store.strerror();
		return 1;
	}

	bdb_adjacency_transaction trans(store);

	std::cout << "retrieving links table...\n";
	mysql_query_ordie(&mysql, "SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
	mysql_query_ordie(&mysql, "SET SESSION NET_READ_TIMEOUT = 3600");
	mysql_query_ordie(&mysql, "SET SESSION NET_WRITE_TIMEOUT = 3600");
	mysql_query_ordie(&mysql, 
                "SELECT page_id, pl_from FROM pagelinks,page "
                "WHERE pl_title=page_title and pl_namespace=page_namespace and page_namespace=0");
	MYSQL_RES *res = mysql_use_result(&mysql);

	MYSQL_ROW arow;
	int i = 0;
	while (arow = mysql_fetch_row(res)) {
		if ((i++ % 10000) == 0)
			std::cout << boost::format("%d...\n") % (i - 1);
		page_id_t from = boost::lexical_cast<page_id_t>(arow[1]);
		page_id_t to = boost::lexical_cast<page_id_t>(arow[0]);

		trans.add_adjacency(from, to);
		if (store.error()) {
			std::cout << boost::format("adding adjacency: %s\n") % store.strerror();
			return 1;
		}
	}
	mysql_free_result(res);

	std::cout << "ok\n";
	std::cout << "retrieving titles...\n";
	mysql_query_ordie(&mysql, "SELECT page_title,page_id FROM page WHERE page_namespace=0");
	res = mysql_use_result(&mysql);
	while (arow = mysql_fetch_row(res)) {
		trans.add_title(boost::lexical_cast<page_id_t>(arow[1]), arow[0]);
		if (store.error()) {
			std::cout << boost::format("adding title: %s\n") % store.strerror();
			return 1;
		}
	}
	mysql_free_result(res);
	mysql_close(&mysql);
	trans.commit();
	store.close();
}
