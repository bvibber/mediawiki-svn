/* Copyright (c) 2007-2009 River Tarnell <river@loreley.flyingparchment.org.uk>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include	<iostream>
#include	<fstream>
#include	<cerrno>
#include	<cstring>
#include	<sstream>
#include	<boost/format.hpp>

#include	"config.h"
#include	"util.h"

config_t config;

bool
configure(std::string const &file)
{
	std::ifstream conffile(file.c_str());
	if (!conffile) {
		std::string err = str(boost::format("cannot open %s: %s") % file % std::strerror(errno));
		std::cerr << err << '\n';
		return false;
	}

	std::string line;
	int lineno = 0;
	while (std::getline(conffile, line)) {
		++lineno;

		if (line[0] == '#')
			continue;
		if (line.empty())
			continue;

		std::istringstream l(line);
		std::string d;
		if (!(l >> d))
			continue;

		if (d == "limit") {
			if (!(l >> config.limit)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid limit") % file % lineno);
				std::cerr << err << '\n';
				return false;
			}
			config.limit *= 1024 * 1024;
		} else if (d == "thresh") {
			if (!(l >> config.thresh)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid threshold") % file % lineno);
				std::cerr << err << '\n';
				return false;
			}
			config.thresh *= 1024 * 1024;
		} else if (d == "exempt") {
			std::string username;
			if (!(l >> username)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid username") % file % lineno);
				std::cerr << err << '\n';
				return false;
			}

			uid_t id = uid(username);
			if (id == -1) {
				std::string err = str(boost::format("\"%s\", line %d: user \"%s\" does not exist") 
						% file % lineno % username);
				std::cerr << err << '\n';
				return false;
			}
			config.exempt.insert(id);
		} else if (d == "delay") {
			if (!(l >> config.delay)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid delay") % file % lineno);
				std::cerr << err << '\n';
				return false;
			}
		} else if (d == "mailmessage") {
			if (!(l >> config.mailmessage)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid mail message") % file % lineno);
				std::cerr << err << '\n';
				return false;
			}
		} else if (d == "sendmail") {
			if (!(l >> config.sendmail)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid sendmail command") % file % lineno);
				std::cerr << err << '\n';
				return false;
			}
		} else if (d == "pidfile") {
			if (!(l >> config.pidfile)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid pid file") % file % lineno);
				std::cerr << err << '\n';
				return false;
			}
		} else if (d == "minuid") {
			if (!(l >> config.minuid) || config.minuid < 0) {
				std::string err = str(boost::format("\"%s\", line %d: invalid minimum uid") % file % lineno);
				std::cerr << err << '\n';
				return false;
			}
		} else {
			std::string err = str(boost::format("\"%s\", line %d: invalid directive") % file % lineno);
			std::cerr << err << '\n';
			return false;
		}
	}

	return true;
}

