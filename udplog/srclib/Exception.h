#ifndef EXCEPTION_H_______________
#define EXCEPTION_H_______________

#include <stdexcept>
#include <string>
#include <cerrno>
#include <cstring>

class libc_error : public std::runtime_error {
public:
	libc_error(const std::string & what_arg)
		: std::runtime_error(std::string(what_arg).append(": ").append(std::strerror(errno)))
	{}

	const char* what() {
		return msg.c_str();
	}

	virtual ~libc_error() throw() {}

	std::string msg;
};
#endif
