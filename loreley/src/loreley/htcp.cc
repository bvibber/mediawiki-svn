/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* htcp: HTCP server support.						*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "stdinc.h"
using std::pair;

#include "loreley.h"
#include "net.h"
#include "config.h"
#include "mbuffer.h"
#include "htcp.h"
#include "cache.h"


namespace {

void add_htcp_listener(pair<string,string> const &ip);

struct htcp_handler_stru : noncopyable {
	void	callback (wsocket *, int);
};
htcp_handler_stru htcp_handler;

void
htcp_handler_stru::callback(wsocket *s, int)
{
char		buf[65535];
address		addr;
size_t		len;

	s->readback(bind(&htcp_handler_stru::callback, this, _1, _2), -1);

	if ((len = s->recvfrom(buf, sizeof(buf), addr)) < 1)
		return;

	WDEBUG(format("HTCP: received packet from %s, len %d")
		% s->straddr() % len);

htcp_decoder	ip(buf, len);
htcp_encoder	op;
	if (!ip.okay()) {
		if (ip.majorver() != 0 || ip.minorver() != 1) {
			/* send an appropriate error */
			WDEBUG(format("HTCP: wrong version, %d %d")
				% ip.majorver() % ip.minorver());
		} else
			/* packet was too malformed to send an error */
			WDEBUG("HTCP: packet mangled");
		return;
	}

	WDEBUG(format(
	"HTCP: packet length %d, packet declares length %d and data length %d")
			% len % ip.length() % ip.opdata()->length());

	/*
	 * If the packet is signed, verify the signature.
	 */
	if (!ip.keyname().empty()) {
	map<string,ustring>::iterator it;
		if ((it = config.htcp_keys.find(ip.keyname())) == config.htcp_keys.end()) {
			WDEBUG(format("HTCP: unknown key %s")
				% ip.keyname());
			return;
		}

		if (!ip.verify_signature(ip.keyname(), it->second,
			addr.addr(), s->address().addr())) {
			WDEBUG("HTCP: sig verify failed");
			return;
		}

		WDEBUG("HTCP: sig okay");
	} else if (config.htcp_sigrequired) {
		return;
	}

	op.rr(true);
	op.transid(ip.transid());

	switch (ip.opcode()) {
	case htcp_op_nop: {
		if (!ip.rd())
			break;

	htcp_opdata_nop odp;
		WDEBUG("HTCP: NOP");
		op.opcode(htcp_op_nop);
		op.opdata(&odp);
		op.build_packet(addr.addr(), s->address().addr());
		s->sendto(op.packet(), op.packet_length(), addr);
		return;
	}

	case htcp_op_tst: {
		if (!ip.rd())
			break;

	htcp_opdata_tst	const *opd = static_cast<htcp_opdata_tst const *>(ip.opdata());
		WDEBUG(format("HTCP: TST: url=[%s]")
			% opd->tst_specifier.hs_url);
	bool	cached = entitycache.cached(opd->tst_specifier.hs_url);
	htcp_opdata_tst_resp_found tf;
	htcp_opdata_tst_resp_notfound tnf;

		op.opcode(htcp_op_tst);
		if (cached) {
			op.response(0);
			op.opdata(&tf);
		} else {
			op.response(1);
			op.opdata(&tnf);
		}

		op.build_packet(addr.addr(), s->address().addr());
		s->sendto(op.packet(), op.packet_length(), addr);
		break;
	}

	case htcp_op_clr: {
	htcp_opdata_clr const *opd = static_cast<htcp_opdata_clr const *>(ip.opdata());
	htcp_opdata_clr_resp rd;
	bool	wascached = entitycache.purge(opd->clr_specifier.hs_url);

		if (!ip.rd())
			break;

		op.opcode(htcp_op_clr);
		op.opdata(&rd);

		if (wascached)
			op.response(htcp_clr_purged);
		else
			op.response(htcp_clr_notfound);
		op.build_packet(addr.addr(), s->address().addr());
		s->sendto(op.packet(), op.packet_length(), addr);
		break;
	}

	}
}

void
add_htcp_listener(pair<string,string> const &ip, string const &mif = "")
{
addrlist	*alist;
const char	*hstr = "", *pstr = DEFAULT_HTCP_PORT;
	if (!ip.first.empty())
		hstr = ip.first.c_str();
	if (!ip.second.empty())
		pstr = ip.second.c_str();

	try {
		alist = addrlist::resolve(hstr, pstr, st_dgram);
	} catch (socket_error &e) {
		wlog.warn(format("resolving [%s]:%s: %s")
			% hstr % pstr % e.what());
		return;
	}

addrlist::iterator	it = alist->begin(), end = alist->end();
	for (; it != end; ++it) {
	net::socket	*sock = NULL;
		try {
			sock = it->makesocket("HTCP listener", prio_stats);
			sock->nonblocking(true);
		} catch (socket_error &e) {
			wlog.warn(format("creating HTCP listener: %s:%s: %s")
				% ip.first % ip.second % e.what());
			delete sock;
			continue;
		}

		try {
			sock->bind();
		} catch (socket_error &e) {
			wlog.warn(format("binding HTCP listener %s: %s")
				% it->straddr() % e.what());
			delete sock;
			continue;
		}

		if (!mif.empty())
			sock->mcast_join(mif);

		sock->readback(bind(&htcp_handler_stru::callback, &htcp_handler, _1, _2), -1);
		wlog.notice(format("HTCP listener: %s")	% sock->straddr());
	}
}

} // anonymous namespace

bool
htcp_init(void)
{
vector<pair<string,string> >::iterator	it = config.htcp_hosts.begin(),
					end = config.htcp_hosts.end();
	for (; it != end; ++it) {
	string::size_type	i;
	string			addr, ifn;
		if ((i = it->second.find('%')) != string::npos) {
			ifn = it->second.substr(i + 1);
			it->second = it->second.substr(0, i);
		} else if ((i = it->first.find('%')) != string::npos) {
			ifn = it->first.substr(i + 1);
			it->first = it->first.substr(0, i);
		}
		WDEBUG(format("HTCP: mcast if: %s") % ifn);
		add_htcp_listener(*it, ifn);
	}

	return true;
}
