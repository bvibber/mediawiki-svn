/* $Id$ */
/* 
 * Six degrees of Wikipedia: Front-end client (command-line)
 *
 * Linux version: modified to use AF_LOCAL sockets.
 */


#include <iostream>
#include <string>
#include <vector>

#include "linksc.h"

int
main(int, char *[])
{
	std::string src, dst;
	std::getline(std::cin, src);
	std::getline(std::cin, dst);
	std::vector<std::string> result;
	int status = linksc_findpath(result, src, dst, false);
	switch (status) {
	case 0:
		std::cout << "ERROR\nNO_FROM\n";
		return 0;
	case 1:
		std::cout << "ERROR\nNO_TO\n";
		return 0;
	case 3:
		std::cout << "ERROR\nNO_CONNECT\n";
		return 0;
	}
	std::cout << "OK\n";
	for (std::size_t i = 0; i < result.size(); ++i)
		std::cout << result[i] << '\n';
}

