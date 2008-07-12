/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<cstring>
using std::strerror;	/* for asio */

#include	<boost/format.hpp>
#include	<log4cxx/logger.h>

#include	"process.h"
#include	"timed_connect.h"

#include	"fcgi_socket.h"

using asio::ip::tcp;
using boost::shared_ptr;
using boost::format;

process::process(
		sbcontext &context,
		uid_t uid, gid_t gid,
		std::string const &bindpath)
	: context_(context)
	, bindpath_(bindpath)
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
	struct sockaddr_un addr;
	std::memset(&addr, 0, sizeof(addr));
	addr.sun_family = AF_UNIX;
	std::strcpy(addr.sun_path, bindpath.c_str());

	LOG4CXX_DEBUG(logger, "binding...");
	if (bind(sock, (struct sockaddr *) &addr, sizeof(addr)) == -1)
		throw creation_failure("startup failed");

	chmod(bindpath.c_str(), 0777);

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
process::connect(
	fcgi_socket_unixp socket,
	boost::function<void (asio::error_code)> func)
{
	LOG4CXX_DEBUG(logger, format("connecting to process socket... %s") % bindpath_);
	asio::local::stream_protocol::endpoint ep(bindpath_);
	async_timed_connect(socket, ep, 
			boost::posix_time::seconds(5),
			func);
}
