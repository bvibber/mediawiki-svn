/*
 * $Header$
 *
 * Links path finder client.
 * This source code is in the public domain.
 *
 */

#include <sys/types.h>
#include <sys/socket.h>

#include <netinet/in.h>

#include <arpa/inet.h>

#include <iostream>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <string>

#include <unistd.h>

#define PORT 7584

int
main(int argc, char *argv[])
{
	std::string src, dst;
	std::getline(std::cin, src);
	std::getline(std::cin, dst);
	int s;
	if ((s = socket(AF_INET, SOCK_STREAM, 0)) < 0) {
		printf("ERROR\nNO_CONNECT\n");
		exit(8);
	}
	struct sockaddr_in addr;
	memset(&addr, 0, sizeof(addr));
	addr.sin_family = AF_INET;
	addr.sin_addr.s_addr = inet_addr("127.0.0.1");
	addr.sin_port = htons(PORT);
	if (connect(s, (sockaddr *) &addr, sizeof(addr)) < 0) {
		printf("ERROR\nNO_CONNECT\n");
		exit(8);
	}
	uint32_t i;
	i = src.size();
	write(s, &i, sizeof(i));
	write(s, src.data(), src.size());
	i = dst.size();
	write(s, &i, sizeof(i));
	write(s, dst.data(), dst.size());
	char buf[512];
	int j;
	while ((j = read(s, buf, sizeof(buf))) > 0) {
		for (char *s = buf; s < buf + j; ++s)
			if (*s != 0) /* work around old linksd bug */
				write(STDOUT_FILENO, s, 1);
	}
}

