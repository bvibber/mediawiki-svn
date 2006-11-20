/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * htcp: HTCP server support.
 */

#if defined __SUNPRO_CC || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <utility>
using std::pair;

#include "willow.h"
#include "wnet.h"
#include "wconfig.h"
#include "mbuffer.h"
#include "htcp.h"
#include "cache.h"

static void add_htcp_listener(pair<string,string> const &ip);

struct htcp_handler_stru : noncopyable {
	void	callback (wsocket *, int);
};
static htcp_handler_stru htcp_handler;

void
htcp_handler_stru::callback(wsocket *s, int)
{
char		buf[65535];
address		addr;
size_t		len;

	s->readback(polycaller<wsocket *, int>(*this, &htcp_handler_stru::callback), 0);

	if ((len = s->recvfrom(buf, sizeof(buf), addr)) < 1)
		return;

	WDEBUG((WLOG_DEBUG, format("HTCP: received packet from %s, len %d")
		% s->straddr() % len));

htcp_decoder	ip(buf, len);
htcp_encoder	op;
	if (!ip.okay()) {
		if (ip.major() != 0 || ip.minor() != 1) {
			/* send an appropriate error */
			WDEBUG((WLOG_DEBUG, format("HTCP: wrong version, %d %d")
				% ip.major() % ip.minor()));
		} else
			/* packet was too malformed to send an error */
			WDEBUG((WLOG_DEBUG, "HTCP: packet mangled"));
		return;
	}

	WDEBUG((WLOG_DEBUG, 
		format("HTCP: packet length %d, packet declares length %d and data length %d")
			% len % ip.length() % ip.opdata()->length()));

	/*
	 * If the packet is signed, verify the signature.
	 */
	if (!ip.keyname().empty()) {
	map<string,ustring>::iterator it;
		if ((it = config.htcp_keys.find(ip.keyname())) == config.htcp_keys.end()) {
			WDEBUG((WLOG_DEBUG, format("HTCP: unknown key %s")
				% ip.keyname()));
			return;
		}

		if (!ip.verify_signature(ip.keyname(), it->second,
			addr.addr(), s->address().addr())) {
			WDEBUG((WLOG_DEBUG, "HTCP: sig verify failed"));
			return;
		}

		WDEBUG((WLOG_DEBUG, "HTCP: sig okay"));
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
		WDEBUG((WLOG_DEBUG, "HTCP: NOP"));
		op.opcode(htcp_op_nop);
		op.opdata(&odp);
		op.build_packet(addr.addr(), s->address().addr());
		s->sendto(op.packet(), op.packet_length(), addr);
		return;
	}

	case htcp_op_tst: {
		if (!ip.rd())
			break;

	htcp_opdata_tst	*opd = (htcp_opdata_tst *)ip.opdata();
		WDEBUG((WLOG_DEBUG, format("HTCP: TST: url=[%s]")
			% opd->tst_specifier.hs_url));
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
	htcp_opdata_clr *opd = (htcp_opdata_clr *)ip.opdata();
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

bool
htcp_init(void)
{
vector<pair<string,string> >::iterator	it = config.htcp_hosts.begin(),
					end = config.htcp_hosts.end();
	for (; it != end; ++it)
		add_htcp_listener(*it);
	return true;
}

static void
add_htcp_listener(pair<string,string> const &ip)
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
		wlog(WLOG_WARNING, format("resolving [%s]:%s: %s")
			% hstr % pstr % e.what());
		return;
	}

addrlist::iterator	it = alist->begin(), end = alist->end();
	for (; it != end; ++it) {
	wnet::socket	*sock = NULL;
		try {
			sock = it->makesocket("HTCP listener", prio_stats);
			sock->nonblocking(true);
		} catch (socket_error &e) {
			wlog(WLOG_WARNING,
				format("creating HTCP listener: %s:%s: %s")
				% ip.first % ip.second % e.what());
			delete sock;
			continue;
		}

		try {
			sock->bind();
		} catch (socket_error &e) {
			wlog(WLOG_WARNING,
				format("binding HTCP listener %s: %s")
				% it->straddr() % e.what());
			delete sock;
			continue;
		}

		sock->readback(polycaller<wsocket *, int>(htcp_handler, 
			&htcp_handler_stru::callback), 0);
		wlog(WLOG_NOTICE, format("HTCP listener: %s")
			% sock->straddr());
	}
}
