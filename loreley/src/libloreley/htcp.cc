/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* htcp: HTCP helper routines						*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "htcp.h"
#include "md5.h"

namespace {

atomic<uint32_t> transid_counter (0);

uint32_t
get_transid(void)
{
	return transid_counter++;
}

size_t
socklen(sockaddr const *addr)
{
	switch (addr->sa_family) {
	case AF_INET:
		return 6; /* 4 IP + 2 port */
	case AF_INET6:
		return 18; /* 16 IP + 2 port */
	default:
		return 0;
	}
}

};

bool
htcp_opdata_clr::decode(marshalling_buffer &buf)
{
uint16_t	i;
	if (!buf.extract<uint16_t>(i))
		return false;
	clr_reason = ntohs(i) & 0xF;
	if (!clr_specifier.decode(buf))
		return false;
	return true;
}

htcp_encoder::htcp_encoder()
	: _sign(false)
	, _opdata(NULL)
{
	_opheader.oh_transid = get_transid();
	_auth.ha_length = 2;
}

htcp_decoder::htcp_decoder(char const *buf, size_t sz)
	: _buf(buf, sz)
	, _okay(false)
	, _opdata(NULL)
{
	if (!_header.decode(_buf))
		return;

size_t	n = _buf.size();
	if (!_opheader.decode(_buf))
		return;

	/*
	 * The rest of the data is OP-DATA.
	 */
	switch (_opheader.oh_opcode) {
	case htcp_op_nop:
		_opdata = new htcp_opdata_nop;
		break;

	case htcp_op_clr:
		if (rr() == 0)
			_opdata = new htcp_opdata_clr;
		else
			_opdata = new htcp_opdata_clr_resp;
		break;

	case htcp_op_tst:
		if (rr() == 0) {
			_opdata = new htcp_opdata_tst;
		} else {
			if (response() == 0)
				_opdata = new htcp_opdata_tst_resp_found;
			else
				_opdata = new htcp_opdata_tst_resp_notfound;
		}
		break;

	default:
		return;
	}

	if (!_opdata->decode(_buf))
		return;
size_t	pad = _opheader.oh_length - (_buf.size() - n);

	if (!_buf.discard_bytes(pad))
		return;

	if (!_auth.decode(_buf))
		return;

	_okay = true;
	return;
}

bool
htcp_decoder::okay(void) const 
{
	return _okay;
}

int
htcp_decoder::majorver(void) const 
{
	return _header.hh_major;
}

int
htcp_decoder::minorver(void) const 
{
	return _header.hh_minor;
}

uint32_t
htcp_decoder::transid(void) const 
{
	return _opheader.oh_transid;
}

int
htcp_decoder::opcode(void) const 
{
	return _opheader.oh_opcode;
}

int
htcp_decoder::response(void) const 
{
	return _opheader.oh_response;
}

bool
htcp_decoder::rd(void) const 
{
	return _opheader.oh_rdmo;
}

bool
htcp_decoder::mo(void) const 
{
	return _opheader.oh_rdmo;
}

bool
htcp_decoder::rr(void) const 
{
	return _opheader.oh_rr;
}

size_t
htcp_decoder::length(void) const
{
	return _header.hh_packet_length;
}

string
htcp_decoder::keyname(void) const
{
	return _auth.ha_keyname;
}

void
htcp_encoder::rr(bool v)
{
	_opheader.oh_rr = v;
}

void
htcp_encoder::rd(bool v)
{
	_opheader.oh_rdmo = v;
}

void
htcp_encoder::mo(bool v)
{
	_opheader.oh_rdmo = v;
}

void
htcp_encoder::opcode(int v)
{
	_opheader.oh_opcode = v;
}

void
htcp_encoder::response(int v)
{
	_opheader.oh_response = v;
}

void
htcp_encoder::transid(uint32_t v)
{
	_opheader.oh_transid = v;
}

void
htcp_encoder::opdata(htcp_opdata *d)
{
	_opdata = d;
}

uint32_t
htcp_encoder::transid(void) const
{
	return _opheader.oh_transid;
}

void
htcp_encoder::key(string const &keyname, ustring const &nkey)
{
	_sign = true;
	_auth.ha_keyname = keyname;
	_key = nkey;
}

htcp_opdata *
htcp_encoder::opdata(void)
{
	return _opdata;
}

bool
htcp_encoder::build_packet(sockaddr const *src, sockaddr const *dest)
{
int		headerlen, oplen, authlen;

	headerlen = _header.length();
	oplen = _opheader.length() + _opdata->length();
	authlen = _auth.length();

	_header.hh_packet_length = headerlen + oplen + authlen;
	_buf.reserve(headerlen + oplen + authlen);
	_header.build(_buf);
	_opheader.oh_length = oplen;
	_opheader.build(_buf);
	_opdata->build(_buf);

	if (_sign && src && dest) {
	marshalling_buffer	buf;
		/*
		 * IP SRC ADDR                           [4 octets]
		 * IP SRC PORT                           [2 octets]
		 * IP DST ADDR                           [4 octets]
		 * IP DST PORT                           [2 octets]
		 * HTCP MAJOR version number             [1 octet]
		 * HTCP MINOR version number             [1 octet]
		 * SIG-TIME                              [4 octets]
		 * SIG-EXPIRE                            [4 octets]
		 * HTCP DATA                             [variable]
		 * KEY-NAME (the whole COUNTSTR [3.1])   [variable]
		 */
		_auth.ha_sigtime = time(0);
		_auth.ha_sigexpire = _auth.ha_sigtime + 10;

		buf.reserve(22 + oplen + socklen(src) + socklen(dest));
		encode_sockaddr(buf, src);
		encode_sockaddr(buf, dest);
		buf.append<uint8_t>(_header.hh_major);
		buf.append<uint8_t>(_header.hh_minor);
		buf.append<uint32_t>(_auth.ha_sigtime);
		buf.append<uint32_t>(_auth.ha_sigexpire);
		buf.append_bytes(_buf.buffer() + headerlen, oplen);
	hmac<md5>	mac(_key.data(), _key.size());
		mac.run(buf.buffer(), buf.size());

	hmac<md5>::digest_t digest = mac.digest();
		_auth.ha_signature.assign(digest, digest + sizeof(digest));
	}

	_auth.build(_buf);
	return true;
}

bool
htcp_decoder::verify_signature(string const &nkeyname, ustring const &key,
		sockaddr const *src, sockaddr const *dst)
{
marshalling_buffer	buf;
ustring			sig;
int			oplen = _opheader.length() + _opdata->length();

	if (nkeyname != _auth.ha_keyname)
		return false;

	buf.reserve(22 + oplen + socklen(src) + socklen(dst));
	htcp_encoder::encode_sockaddr(buf, src);
	htcp_encoder::encode_sockaddr(buf, dst);
	buf.append<uint8_t>(_header.hh_major);
	buf.append<uint8_t>(_header.hh_minor);
	buf.append<uint32_t>(_auth.ha_sigtime);
	buf.append<uint32_t>(_auth.ha_sigexpire);
	buf.append_bytes(_buf.buffer() + _header.length(), oplen);
hmac<md5>	mac(key.data(), key.size());
	mac.run(buf.buffer(), buf.size());

hmac<md5>::digest_t digest = mac.digest();
	sig.assign(digest, digest + sizeof(digest));

	if (sig != _auth.ha_signature)
		return false;

	if ((time_t)_auth.ha_sigexpire <= time(0))
		return false;

	return true;
}

char const *
htcp_encoder::packet(void) const
{
	return _buf.buffer();
}

size_t
htcp_encoder::packet_length(void) const
{
	return _buf.size();
}

htcp_opdata const *
htcp_decoder::opdata(void) const
{
	return _opdata;
}

htcp_opdata::~htcp_opdata()
{
}

bool
htcp_specifier::decode(marshalling_buffer &buf)
{
	/*
	 *   3.2.  SPECIFIER is used with the TST and CLR request messages,
	 *   defined below.  Its format is:
	 *
	 *      +---------------------+
	 *      |        METHOD       | : COUNTSTR
	 *      +---------------------+
	 *      |         URI         | : COUNTSTR
	 *      +---------------------+
	 *      |       VERSION       | : COUNTSTR
	 *      +---------------------+
	 *      |       REQ-HDRS      | : COUNTSTR
	 *      +---------------------+
	 */
	if (!htcp_decoder::decode_countstr(buf, hs_method))
		return false;
	if (!htcp_decoder::decode_countstr(buf, hs_url))
		return false;
	if (!htcp_decoder::decode_countstr(buf, hs_version))
		return false;
	if (!htcp_decoder::decode_countstr(buf, hs_headers))
		return false;
	return true;
}

void
htcp_specifier::build(marshalling_buffer &buf) const
{
	htcp_encoder::encode_countstr(buf, hs_method);
	htcp_encoder::encode_countstr(buf, hs_url);
	htcp_encoder::encode_countstr(buf, hs_version);
	htcp_encoder::encode_countstr(buf, hs_headers);
}

htcp_decoder::~htcp_decoder()
{
	delete _opdata;
}

size_t
htcp_header::length(void) const
{
	return 4;
}

void
htcp_header::build(marshalling_buffer &buf) const
{
	buf.append<uint16_t>(htons(hh_packet_length));
	buf.append<uint8_t>(hh_major);
	buf.append<uint8_t>(hh_minor);
}

bool
htcp_header::decode(marshalling_buffer &buf)
{
	if (!buf.extract<uint16_t>(hh_packet_length))
		return false;
	hh_packet_length = ntohs(hh_packet_length);

	if (!buf.extract<uint8_t>(hh_major))
		return false;
	if (!buf.extract<uint8_t>(hh_minor))
		return false;

	if (hh_major || (hh_minor != 1))
		return false;

	return true;
}


size_t
htcp_opheader::length(void) const
{
	return 8;
}

bool
htcp_opheader::decode(marshalling_buffer &buf)
{
	if (!buf.extract<uint16_t>(oh_length))
		return false;
	oh_length = ntohs(oh_length);

	if (!buf.extract<uint8_t>(oh_opresp))
		return false;
	oh_response = oh_opresp & 0xF;
	oh_opcode = oh_opresp >> 4;

uint8_t	flags;
	if (!buf.extract<uint8_t>(flags))
		return false;
	oh_rr = flags & 0x1;
	oh_rdmo = flags & 0x2;

	if (!buf.extract<uint32_t>(oh_transid))
		return false;
	oh_transid = htonl(oh_transid);

	return true;
}

void
htcp_opheader::build(marshalling_buffer &buf) const
{
	/*
	 *	                 +0 (MSB)                            +1 (LSB)
	 *      +---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+
	 *   0: |                             LENGTH                            |
	 *      +---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+
	 *   2: |    OPCODE     |   RESPONSE    |        RESERVED       |F1 |RR |
	 *      +---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+
	 *   4: |                           TRANS-ID                            |
	 *      +   +   +   +   +   +   +   +   +   +   +   +   +   +   +   +   +
	 *   6: |                           TRANS-ID                            |
	 *      +---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+
	 */
	buf.append<uint16_t>(htons(oh_length));
	buf.append<uint8_t>((oh_opcode << 4) | (oh_response & 0xF));
	buf.append<uint8_t>((oh_rdmo << 1) | oh_rr);
	buf.append<uint32_t>(htonl(oh_transid));
}


void
htcp_opdata_clr::build(marshalling_buffer &buf) const
{
uint16_t	i;
	i = clr_reason & 0xF;
	buf.append<uint16_t>(htons(i));
	clr_specifier.build(buf);
}

size_t
htcp_opdata_clr::length(void) const
{
	return 2 + clr_specifier.length();
}

size_t
htcp_specifier::length(void) const
{
	return 8 +
		hs_headers.length() +
		hs_method.length() +
		hs_url.length() +
		hs_version.length();
}

void
htcp_opdata_tst::build(marshalling_buffer &buf) const
{
	tst_specifier.build(buf);
}

size_t
htcp_opdata_tst::length(void) const
{
	return tst_specifier.length();
}

bool
htcp_opdata_tst::decode(marshalling_buffer &buf)
{
	return tst_specifier.decode(buf);
}

void
htcp_opdata_tst_resp_found::build(marshalling_buffer &buf) const
{
	tf_detail.build(buf);
}

size_t
htcp_opdata_tst_resp_found::length(void) const
{
	return tf_detail.length();
}

bool
htcp_opdata_tst_resp_found::decode(marshalling_buffer &buf)
{
	return tf_detail.decode(buf);
}

bool
htcp_detail::decode(marshalling_buffer &buf)
{
	if (!htcp_decoder::decode_headerlist(buf, hd_resphdrs))
		return false;
	if (!htcp_decoder::decode_headerlist(buf, hd_enthdrs))
		return false;
	if (!htcp_decoder::decode_headerlist(buf, hd_cachehdrs))
		return false;
	return true;
}

size_t
htcp_detail::length(void) const
{
	return 6 +
		hd_resphdrs.size() +
		hd_enthdrs.size() +
		hd_cachehdrs.size();
}

void
htcp_detail::build(marshalling_buffer &buf) const
{
	htcp_encoder::encode_headerlist(buf, hd_resphdrs);
	htcp_encoder::encode_headerlist(buf, hd_enthdrs);
	htcp_encoder::encode_headerlist(buf, hd_cachehdrs);
}

void
htcp_opdata_tst_resp_notfound::build(marshalling_buffer &buf) const
{
	htcp_encoder::encode_headerlist(buf, tn_cachehdrs);
}

size_t
htcp_opdata_tst_resp_notfound::length(void) const
{
	return 2 + tn_cachehdrs.size();
}

bool
htcp_opdata_tst_resp_notfound::decode(marshalling_buffer &buf)
{
	return htcp_decoder::decode_headerlist(buf, tn_cachehdrs);
}

bool
htcp_decoder::decode_headerlist(marshalling_buffer &buf,
	vector<string> &v)
{
string	headers;
	if (!decode_countstr(buf, headers))
		return false;

char const	*data = headers.data(), *end = headers.data() + headers.size();
char const	*rn;
	while ((rn = find_rn(data, end)) != NULL) {
		v.push_back(string(data, rn));
		data = rn + 2;
	}
	if (data < end)
		v.push_back(string(data, end));
	return true;
}

void
htcp_encoder::encode_headerlist(marshalling_buffer &buf, vector<string> const &v)
{
vector<string>::const_iterator	it = v.begin(), end = v.end();
string	res;
	for (; it != end; ++it) {
		res += *it;
		res += "\r\n";
	}
	encode_countstr(buf, res);
}

bool
htcp_auth::decode(marshalling_buffer &buf)
{
	if (!buf.extract<uint16_t>(ha_length))
		return false;
	ha_length = ntohs(ha_length);

	if (ha_length < 2)
		return false;
	if (ha_length == 2)
		return true;	/* no auth	*/
	if (!buf.extract<uint32_t>(ha_sigtime))
		return false;
	if (!buf.extract<uint32_t>(ha_sigexpire))
		return false;
	if (ha_sigexpire < ha_sigtime)
		return false;
	if (!htcp_decoder::decode_countstr(buf, ha_keyname))
		return false;
	if (!htcp_decoder::decode_countstr(buf, ha_signature))
		return false;
	return true;
}

size_t
htcp_auth::length(void) const
{
	if (ha_keyname.empty())
		return 2;
	return 2 + 4 + 4 + 2 + ha_keyname.size() + 2 + 16;
}

void
htcp_auth::build(marshalling_buffer &buf) const
{
	if (ha_keyname.empty()) {
		buf.append<uint16_t>(htons(2));
		return;
	}

	buf.append<uint16_t>(htons(static_cast<uint16_t>(length())));
	buf.append<uint32_t>(ha_sigtime);
	buf.append<uint32_t>(ha_sigexpire);
	htcp_encoder::encode_countstr(buf, ha_keyname);
	htcp_encoder::encode_countstr(buf, ha_signature);
}

void
htcp_encoder::encode_sockaddr(marshalling_buffer &buf, sockaddr const *addr)
{
	switch (addr->sa_family) {
	case AF_INET: {
	sockaddr_in const *in = sockaddr_cast<sockaddr_in const *>(addr);
		buf.append<uint32_t>(in->sin_addr.s_addr);
		buf.append<uint16_t>(in->sin_port);
		return;
	}

	case AF_INET6: {
	sockaddr_in6 const *in = sockaddr_cast<sockaddr_in6 const *>(addr);
		buf.append_bytes(in->sin6_addr.s6_addr, 16);
		buf.append<uint16_t>(in->sin6_port);
		return;
	}
	}
}
