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

#include <unistd.h>
#include <mysql.h>

#include "linksc.h"
#include "defs.h"

void
mysql_query_ordie(MYSQL* mysql, char const *query)
{
	int i = mysql_query(mysql, query);
	if (i) {
		std::printf("mysql query failed: %s\n", mysql_error(mysql));
		std::exit(8);
	}
}
int
main(int, char *argv[])
{
	MYSQL mysql;
	mysql_init(&mysql);
	mysql_options(&mysql, MYSQL_READ_DEFAULT_GROUP, "linksd");
 
	if (!mysql_real_connect(&mysql, NULL, NULL, NULL, argv[1], 0, NULL, 0)) {
		std::printf("mysql connect error: %s\n", mysql_error(&mysql));
		return 1;
	}

	unlink(CACHE);
	std::ofstream out(CACHE);
	if (!out.good()) {
		std::printf("Cannot open cache file: %s\n", std::strerror(errno));
		return 1;
	}

	std::printf("retrieving links table...\n");
	mysql_query_ordie(&mysql, "SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
	mysql_query_ordie(&mysql, 
                "SELECT page_id, pl_from FROM pagelinks,page "
                "WHERE pl_title=page_title and pl_namespace=page_namespace and page_namespace=0");
	MYSQL_RES *res = mysql_use_result(&mysql);

	MYSQL_ROW arow;
	int i = 0;
	while ((arow = mysql_fetch_row(res)) != NULL) {
		if ((i++ % 10000) == 0)
			std::printf("%d...\n", i - 1);
		out << arow[1] << ' ' << arow[0] << '\n';
	}
	mysql_free_result(res);
	out << '\n';

	std::printf("ok\n");
	std::printf("retrieving titles...\n");
	mysql_query_ordie(&mysql, "SELECT page_title,page_id FROM page WHERE page_namespace=0");
	res = mysql_use_result(&mysql);
	while ((arow = mysql_fetch_row(res)) != NULL) {
		out << arow[1] << ' ' << arow[0] << '\n';
	}
	mysql_free_result(res);
	mysql_close(&mysql);
	out.close();
	std::printf("all done\n");
}
