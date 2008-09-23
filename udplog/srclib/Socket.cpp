#include "Socket.h"

void Socket::RaiseError(const char* msg)
{
	if (ignoreErrors.size() == 0) {
		// Ignore none
		throw libc_error(msg);
	}

	std::set<int> * curIgnore = ignoreErrors.back().get();
	if (!curIgnore) {
		// Ignore all
		return;
	}

	if (!curIgnore->count(errno)) {
		// Don't ignore this one
		throw libc_error(msg);
	}
}

