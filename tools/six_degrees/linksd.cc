/*
 * $Header$
 *
 * Links path finder daemon.
 * This source code is in the public domain.
 *
 */ 

#include <sys/types.h>
#include <sys/socket.h>

#include <netinet/in.h>

#include <arpa/inet.h>

#include <map>
#include <list>
#include <set>
#include <vector>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <cassert>
#include <queue>
#include <string>

#include <unistd.h>

#include <mysql.h>

#define PORT 7584 /* KT :-) */

#define dbhost "whatever"
#define dbuser "whatever"
#define dbpass "whatever"

std::vector<std::string> names;
std::map<std::string, int> ids;
 
std::vector< std::vector< int > > adjacency;
std::vector< int > back;
std::deque< int > next;
 
/* dijkstra implementation by zorbathutt@efnet #c++, not copyrighted */
std::vector< int > findPath( int src, int dst ) {
	back.clear();
	back.resize( adjacency.size(), -1 );
	next.clear();
	back[ src ] = -2;
	next.push_back( src );
	while( next.size() ) {
		int ts = next[ 0 ];
		next.pop_front();
		if( ts == dst ) {
			std::vector< int > path;
			path.push_back( dst );
			int lastlink = back[ dst ];
			while( lastlink != -2 ) {
				assert( lastlink != -1 );
				path.push_back( lastlink );
				lastlink = back[ lastlink ];
			}
			reverse( path.begin(), path.end() );
			return path;
		}
		for( int i = 0; i < adjacency[ ts ].size(); i++ ) {
			if( back[ adjacency[ ts ][ i ] ] == -1 ) {
				back[ adjacency[ ts ][ i ] ] = ts;
				next.push_back( adjacency[ ts ][ i ] );
			}
		}
	}
	return std::vector< int >();
}

void
ioloop(void)
{
	int sfd;
	struct sockaddr_in servaddr, cliaddr;
	if ((sfd = socket(AF_INET, SOCK_STREAM, 0)) < 0) {
		perror("socket");
		exit(8);
	}
	memset(&servaddr, 0, sizeof(servaddr));
	servaddr.sin_family = AF_INET;
	servaddr.sin_addr.s_addr = htonl(INADDR_ANY);
	servaddr.sin_port = htons(PORT);
	int one = 1;
	setsockopt(sfd, SOL_SOCKET, SO_REUSEADDR, &one, sizeof(one));
	if (bind(sfd, (struct sockaddr *) &servaddr, sizeof(servaddr)) < 0) {
		perror("bind");
		exit(8);
	}
	if (listen(sfd, 5) < 0) {
		perror("listen");
		exit(8);
	}
	int cfd;
	socklen_t clilen = sizeof(cliaddr);
	while ((cfd = accept(sfd, (sockaddr *) &cliaddr, &clilen)) > 0) {
		std::string from, to;
		uint32_t len;
		if (read(cfd, &len, sizeof(len)) < 0) {
			perror("read");
			close(cfd);
			continue;
		}
#define nofrom "ERROR\nNO_FROM\n"
#define noto "ERROR\nNO_TO\n"
		if (len > 255) {
			write(cfd, nofrom, sizeof(nofrom));
			close(cfd);
			continue;
		}
		std::vector<u_char> b(len);
		if (read(cfd, &b[0], len) < 0) {
			perror("read");
			close(cfd);
			continue;
		}
		from.assign(b.begin(), b.end());
		if (read(cfd, &len, sizeof(len)) < 0) {
			perror("read");
			close(cfd);
			continue;
		}
		if (len > 255) {
			write(cfd, noto, sizeof(noto));
			close(cfd);
			continue;
		}
		b.resize(len);
		if (read(cfd, &b[0], len) < 0) {
			perror("read");
			close(cfd);
			continue;
		}
		to.assign(b.begin(), b.end());
		int fromid, toid;
		if (ids.find(from) == ids.end()) {
			write(cfd, nofrom, sizeof(nofrom));
			close(cfd);
			continue;
		}
		fromid = ids[from];
		if (ids.find(to) == ids.end()) {
			write(cfd, noto, sizeof(noto));
			close(cfd);
			continue;
		}
		toid = ids[to];
		std::vector<int> links = findPath(fromid, toid);
#define ok "OK\n"
		write(cfd, ok, sizeof(ok));
		for (std::vector<int>::const_iterator it = links.begin(), end = links.end(); it != end; ++it)
		{
			std::string s = names[*it] + '\n';
			b.assign(s.begin(), s.end());
			write(cfd, &b[0], b.size());
		}
		close(cfd);
	}
	exit(0);
}


void
mysql_query_ordie(MYSQL* mysql, char const *query)
{
	int i = mysql_query(mysql, query);
	if (i) {
		printf("mysql query failed: %s\n", mysql_error(mysql));
		exit(8);
	}
}
int
main(int argc, char *argv[])
{
	MYSQL mysql;
	mysql_init(&mysql);
	mysql_options(&mysql, MYSQL_READ_DEFAULT_GROUP, "linksd");
 
	if (!mysql_real_connect(&mysql, dbhost, dbuser, dbpass, argv[1], 0, NULL, 0)) {
		printf("mysql connect error: %s\n", mysql_error(&mysql));
		return 1;
	}

	printf("retrieving links table...\n");
	mysql_query_ordie(&mysql, "SELECT l_to, l_from FROM links");
	MYSQL_RES *res = mysql_use_result(&mysql);

	MYSQL_ROW arow;
	while (arow = mysql_fetch_row(res)) {
		int l_from = atoi(arow[1]);
		int l_to = atoi(arow[0]);
		if (l_from >= adjacency.size())
			adjacency.resize(l_from + 1);
		std::vector<int>& l = adjacency[l_from];
		l.insert(l.end(), l_to);
	}
	mysql_free_result(res);

	printf("ok\n");
	printf("retrieving titles...\n");
	mysql_query_ordie(&mysql, "SELECT cur_title,cur_id FROM cur WHERE cur_namespace=0");
	res = mysql_use_result(&mysql);
	while (arow = mysql_fetch_row(res)) {
		std::string title = arow[0];
		int id = atoi(arow[1]);
		if (id >= names.size())
			names.resize(id + 1);
		names[id] = title;
		ids[title] = id;
	}
	printf("ok, %d links, %d titles\n", adjacency.size(), names.size());
	mysql_free_result(res);
	printf("filtering links...\n");
	for (int i = 1; i < adjacency.size(); ++i) {
		if (i >= names.size() || names[i].empty()) {
			adjacency[i].clear();
			continue;
		}
		for (std::vector<int>::iterator it = adjacency[i].begin(); it != adjacency[i].end();)
			if (*it >= names.size() || names[*it].empty())
				it = adjacency[i].erase(it);
			else ++it;
	}
	printf("ok\n");

	mysql_close(&mysql);
	ioloop();
}
