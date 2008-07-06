/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<boost/format.hpp>
#include	<log4cxx/logger.h>

#include	"process.h"

namespace asio = boost::asio;
using asio::ip::tcp;
using boost::shared_ptr;
using boost::format;

process::process(
		sbcontext &context,
		uid_t uid, gid_t gid,
		std::string const &bindpath)
	: context_(context)
	, uid_(uid)
	, gid_(gid)
	, logger(log4cxx::Logger::getLogger("switchboard.process"))
{
	LOG4CXX_DEBUG(logger, format("creating new process; uid=%d bindpath=%s")
		% uid % bindpath);
	int sock;
	char uids[64], gids[64];

	/*
	 * Create a listening socket that we'll use to connect to the child.
	 */
	if ((sock = ::socket(AF_UNIX, SOCK_STREAM, 0)) == -1)
		throw creation_failure("socket failed");

	unlink(bindpath.c_str());
	memset(&addr_, 0, sizeof(addr_));
	addr_.sun_family = AF_UNIX;
	strcpy(addr_.sun_path, bindpath.c_str());

	LOG4CXX_DEBUG(logger, "binding...");
	if (bind(sock, (struct sockaddr *) &addr_, sizeof(addr_)) == -1)
		throw creation_failure("startup failed");

	LOG4CXX_DEBUG(logger, "listening...");
	if (listen(sock, 1) == -1)
		throw creation_failure("listen failed");

	/*
	 * Spawn a new php-cgi process.
	 */
	switch(pid_ = fork()) {
	case -1:
		throw creation_failure("can't fork");

	case 0:
		snprintf(uids, sizeof(uids), "%lu", (unsigned long) uid);
		snprintf(gids, sizeof(gids), "%lu", (unsigned long) gid);

		close(0);
		close(1);
		close(2);
		dup2(sock, 0);
		close(sock);
		execl(PREFIX "/lib/switchboard/swexec", "swexec", uids, gids, NULL);
		_exit(1);
	
	default:
		close(sock);
		break;
	}
	LOG4CXX_DEBUG(logger, format("process created; pid=%d") % pid_);
}

process::~process()
{
    /*
     * Because the process is setuid, trying to kill it normally won't work.
     * We use the swkill wrapper, which is similar to swexec, except it kills
     * a process.
     */
    char uids[64];
    char gids[64];
    char pids[64];

	LOG4CXX_DEBUG(logger, format("process@%p destroyed, killing pid %d") % this % pid_);

	switch(fork()) {
	case -1:
        return;

	case 0:
		snprintf(uids, sizeof(uids), "%lu", (unsigned long) uid_);
		snprintf(gids, sizeof(gids), "%lu", (unsigned long) gid_);
		snprintf(pids, sizeof(pids), "%lu", (unsigned long) pid_);

		execl(PREFIX "/lib/switchboard/swkill", "swkill", uids, gids, pids, NULL);
		_exit(1);
    }
}

void
process::connect(tcp::socket &socket)
{
	LOG4CXX_DEBUG(logger, format("connecting to process socket %s...")
			% addr_.sun_path);
	int sock;
	if ((sock = ::socket(AF_UNIX, SOCK_STREAM, 0)) == -1)
		throw connect_failure("socket failed");

	if (fcntl(sock, F_SETFD, FD_CLOEXEC) == -1)
		throw connect_failure("fcntl(FD_CLOEXEC) failed");

	if (::connect(sock, (struct sockaddr *) &addr_, sizeof(addr_)) == -1)
		throw connect_failure("connect failed");

	LOG4CXX_DEBUG(logger, "connected okay");
	boost::system::error_code err;
	socket.assign(tcp::v4(), sock, err);
}
