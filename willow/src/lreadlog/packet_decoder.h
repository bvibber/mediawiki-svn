/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* wreadlog: Read UDP log packets and print human-readable log.		*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef PACKET_DECODER_H
#define PACKET_DECODER_H

#include <sys/socket.h>

#include <string>
using std::string;

#include "acl.h"

struct logent {
	logent(bool usegmt) : _usegmt(usegmt) {}

	time_t		r_reqtime;
	string		r_cliaddr;
	string		r_reqtype;
	string		r_path;
	uint16_t	r_status;
	string		r_beaddr;
	bool		r_cached;
	uint32_t	r_docsize;

	string const &fmt_reqtime(const char *fmt) const;

private:
	bool	_usegmt;
};

struct packet_decoder {
	packet_decoder(bool dodns = true);

	void usedns	(bool);

	bool decode	(logent &, sockaddr *, socklen_t,
			 char const *buf, char const *end);
	bool add_acl	(string const &);

private:
	bool	_dodns;
	acl	_acl4
#ifdef AF_INET6
		, _acl6
#endif
	;


	string resolve(string const &host);
};

#endif
