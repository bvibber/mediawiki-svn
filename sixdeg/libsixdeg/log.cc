/* Six degrees of Wikipedia						*/
/* Copyright (c) 2007 River Tarnell <river@attenuate.org>.		*/
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include <iostream>
#include <string>
#include <ctime>
#include <unistd.h>

#include <boost/format.hpp>

#include "log.h"

namespace {

	pid_t pid = getpid();

	void
	dolog(std::string const &sev, std::string const &text)
	{
		std::time_t now = std::time(0);
		std::tm *t = std::localtime(&now);
		char tbuf[256];
		std::strftime(tbuf, sizeof tbuf, "%d-%b-%Y %H:%M:%S", t);

		std::cerr << boost::format("%s/%d/%s:%s\n") % tbuf % pid % sev % text;
	}

}

namespace logger {

	void
	info(std::string const &text)
	{
		dolog("info", text);
	}

	void
	warning(std::string const &text)
	{
		dolog("warning", text);
	}

	void
	error(std::string const &text)
	{
		dolog("error", text);
	}

}


