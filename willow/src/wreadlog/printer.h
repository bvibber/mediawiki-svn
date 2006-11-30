/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* printer: log format printers.					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef PRINTER_H
#define PRINTER_H

#include <iostream>
#include <boost/format.hpp>
using std::ostream;
using boost::format;

#include "packet_decoder.h"

struct entry_printer {
	virtual ~entry_printer(void) {}
	virtual void print(logent const &e) = 0;
	virtual void update(void) {}
};

struct loreley_printer : entry_printer {
	loreley_printer(ostream &of_) : of(of_) {}

	void print(logent const &e) {
		of << format("[%s] %s %s \"%.*s\" %lu %d %.*s %s\n")
			% e.fmt_reqtime("%Y-%m-%d %H:%M:%S")
			% e.r_cliaddr
			% e.r_reqtype
			% e.r_path
			% e.r_docsize
			% e.r_status
			% e.r_beaddr
			% (e.r_cached ? "HIT" : "MISS");
	}

private:
	ostream	&of;
};

struct clf_printer : entry_printer {
	clf_printer(ostream &of_) : of(of_) {}

	void print(logent const &e) {
		of << format("%s - - [%s] \"%s %.*s HTTP/1.0\" %d %lu\n")
			% e.r_cliaddr
			% e.fmt_reqtime("%d/%b/%Y %H:%M:%S %z")
			% e.r_reqtype
			% e.r_path
			% e.r_status
			% e.r_docsize;
	}

private:
	ostream	&of;
};

struct squid_printer : entry_printer {
	squid_printer(ostream &of_) : of(of_) {}

	void print(logent const &e) {
		of << format("%lu.0      0 %s TCP_%s/%d %lu %s %s - ")
			% e.r_reqtime
			% e.r_cliaddr
			% (e.r_cached ? "HIT" : "MISS")
			% e.r_status
			% e.r_docsize
			% e.r_reqtype
			% e.r_path;
		if (!e.r_cached)
			of << format("PARENT_HIT/%s -\n") % e.r_beaddr;
		else	
			of << "NONE/- -\n";
	}

private:
	ostream	&of;
};

struct tp_impl;
struct topurl_printer : entry_printer {
	topurl_printer(int);
	~topurl_printer();
	void print(logent const &e);

private:
	tp_impl	*impl;
};

#endif
