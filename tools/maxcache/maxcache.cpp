#include "boost-config.h"
#include <boost/asio.hpp>
#include <iostream>
#include "App.h"

int main(int argc, char** argv)
{
	try {
		MaxCache::App app;
		app.run(argc, argv);
	} catch (std::exception & e) {
		std::cerr << "Exception: " << e.what() << "\n";
		return 1;
	}
	return 0;
}
