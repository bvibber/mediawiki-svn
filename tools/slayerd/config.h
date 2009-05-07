/* Copyright (c) 2007-2009 River Tarnell <river@loreley.flyingparchment.org.uk>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#ifndef CONFIG_H
#define CONFIG_H

#include	<cstdlib>
#include	<set>
#include	<string>

extern struct config_t {
	std::size_t limit;
	std::size_t thresh;
	int delay;
	std::string mailmessage;
	std::string sendmail;
	std::string pidfile;
	std::set<uid_t> exempt;
	int minuid;
	bool debug;

	config_t()
		: limit(0)
		, minuid(-1)
		, thresh(0)
		, delay(60)
		, mailmessage(ETCDIR "/mailmessage")
		, sendmail("/usr/lib/sendmail -oi -bm --")
		, pidfile("/var/run/slayerd.pid")
		, debug(false)
	{}
} config;

bool configure(std::string const &);

#endif	/* !CONFIG_H */
