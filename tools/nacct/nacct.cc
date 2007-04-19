/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

/*
 * nacct: Read BSD accounting data and print aggregated statistics by user and 
 * command name.
 */
#include <sys/types.h>
#include <sys/acct.h>
#include <fcntl.h>
#include <unistd.h>
#include <pwd.h>

#include <iostream>
#include <ios>
#include <iomanip>
#include <map>
#include <string>
#include <cerrno>

#include <boost/format.hpp>
#include <boost/lexical_cast.hpp>

/*
 * Entry to a single command.  user_entry contains a list of these.  There is 
 * only one cmd_entry per command; multiple invocations are aggregated.
 */
struct cmd_entry {
	cmd_entry()
		: utime(0)
		, stime(0)
		, mem(0)
		, count(0)
	{}

	std::string name;
	uint64_t utime;
	uint64_t stime;
	uint64_t mem;
	int count;
};

struct user_entry {
	uint16_t uid;
	uint64_t utime;
	uint64_t stime;
	uint64_t mem;
	int count;

	user_entry()
		: uid(-1)
		, utime(0)
		, stime(0)
		, mem(0)
		, count(0)
	{}

	std::map<std::string, cmd_entry> commands;

	cmd_entry &
	get_cmd_entry(std::string const &cmd) {
		std::map<std::string, cmd_entry>::iterator it;
		if ((it = commands.find(cmd)) == commands.end())  {
			it = commands.insert(std::make_pair(cmd, cmd_entry())).first;
			it->second.name = cmd;
		}
		return it->second;
	}
};

namespace {
	static std::string const acctfile = "/var/log/account/pacct";
	std::map<uint16_t, user_entry> users;

	uint64_t decode_comp_t(comp_t comp);

	user_entry &
	get_user_entry(uint16_t uid) {
		std::map<uint16_t, user_entry>::iterator it;
		if ((it = users.find(uid)) == users.end())  {
			it = users.insert(std::make_pair(uid, user_entry())).first;
			it->second.uid = uid;
		}
		return it->second;
	}

	std::string
	user_name(uint16_t uid) {
		passwd *pwd;
		if ((pwd = getpwuid(uid)) == NULL)
			return boost::lexical_cast<std::string>(uid);
		return pwd->pw_name;
	}
}

int
main(int argc, char *argv[])
{
	int fd, r;
	std::string username;

	if ((fd = open(acctfile.c_str(), O_RDONLY)) == -1) {
		std::cerr << boost::format("nacct: cannot open %s: %s\n")
			% acctfile % std::strerror(errno);
		return 1;
	}

	if (argv[1])
		username = argv[1];

	struct acct ent;
	while ((r = read(fd, &ent, sizeof(ent))) == sizeof(ent)) {
		user_entry &u = get_user_entry(ent.ac_uid);
		u.count++;
		u.stime += decode_comp_t(ent.ac_stime);
		u.utime += decode_comp_t(ent.ac_utime);
		u.mem += decode_comp_t(ent.ac_mem);

		cmd_entry &c = u.get_cmd_entry(ent.ac_comm);
		c.count++;
		c.stime += decode_comp_t(ent.ac_stime);
		c.utime += decode_comp_t(ent.ac_utime);
		c.mem += decode_comp_t(ent.ac_mem);
	}

	close(fd);

	std::cout << "username           user    sys    mem\n";
	std::cout << "   command              user    sys    mem\n";

	for (std::map<uint16_t, user_entry>::iterator
			it = users.begin(), end = users.end();
			it != end; ++it) {
		user_entry &u = it->second;
		std::string un = user_name(u.uid);
		if (!username.empty() && !(un == username))
			continue;

		std::string uname = un.substr(0, 16);
		std::cout << boost::format("%-16s %f %f\n") 
			% uname 
			% boost::io::group(std::setw(7), std::setprecision(2), (u.utime / (double) AHZ))
			% boost::io::group(std::setw(7), std::setprecision(2), (u.stime / (double) AHZ));

		for (std::map<std::string, cmd_entry>::iterator
				cit = u.commands.begin(), cend = u.commands.end();
				cit != cend; ++cit) {
			cmd_entry &c = cit->second;
			std::string cname = c.name.substr(0, 16);

			std::cout << boost::format("   %-16s  %f %f %d\n") 
				% cname 
				% boost::io::group(std::setw(7), std::setprecision(2), (c.utime / (double) AHZ))
				% boost::io::group(std::setw(7), std::setprecision(2), (c.stime / (double) AHZ))
				% (c.mem / c.count);
		}
	}
}

/*
 * Copyright (c) 1994 Christopher G. Demetriou
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *      This product includes software developed by Christopher G. Demetriou.
 * 4. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace {

uint64_t
decode_comp_t(comp_t comp)
{
	uint64_t rv;

	/*
	 * for more info on the comp_t format, see:
	 *	/usr/src/sys/kern/kern_acct.c
	 *	/usr/src/sys/sys/acct.h
	 *	/usr/src/usr.bin/lastcomm/lastcomm.c
	 */
	rv = comp & 0x1fff;	/* 13 bit fraction */
	comp >>= 13;		/* 3 bit base-8 exponent */
	while (comp--)
		rv <<= 3;

	return (rv);
}

}

