/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* htcp: HTCP handling helpers.						*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef HTCP_H
#define HTCP_H

#include <sys/types.h>
#include <limits>
#include <cstddef>
#include <algorithm>
#include <stdexcept>
using std::size_t;
using std::numeric_limits;
using std::min;
using std::logic_error;

#include "mbuffer.h"
#include "util.h"

enum {
	htcp_authentication_required	= 0,
	htcp_authentication_failed,
	htcp_opcode_not_implemented,
	htcp_major_unsupported,
	htcp_minor_unsupported,
	htcp_opcode_disallowed
};

enum {
	htcp_op_nop = 0,
	htcp_op_tst = 1,
	htcp_op_mon = 2,
	htcp_op_set = 3,
	htcp_op_clr = 4
};

enum {
	htcp_clr_purged = 0,
	htcp_clr_refused,
	htcp_clr_notfound
};

struct htcp_specifier {
	bool	decode (marshalling_buffer &);
	void	build  (marshalling_buffer &) const;
	size_t	length (void) const;

	string	hs_method;
	string	hs_url;
	string	hs_version;
	string	hs_headers;
};

struct htcp_detail {
	bool	decode (marshalling_buffer &);
	void	build  (marshalling_buffer &) const;
	size_t	length (void) const;

	vector<string>	hd_resphdrs;
	vector<string>	hd_enthdrs;
	vector<string>	hd_cachehdrs;
};

struct htcp_header {
	htcp_header() : hh_major(0), hh_minor(1) {}
	bool	decode(marshalling_buffer &);
	void	build(marshalling_buffer &) const;
	size_t	length (void) const;

	uint16_t	 hh_packet_length;
	uint8_t		 hh_major, hh_minor;
};

struct htcp_opheader {
	htcp_opheader() 
		: oh_length(0)
		, oh_opresp(0)
		, oh_opcode(0)
		, oh_response(0)
		, oh_transid(0)
		, oh_rdmo(0)
		, oh_rr(0)
	{}

	bool	decode(marshalling_buffer &);
	void	build(marshalling_buffer &)const;
	size_t	length (void) const;

	uint16_t	 oh_length;
	uint8_t		 oh_opresp;
	int		 oh_opcode;
	int		 oh_response;
	uint32_t	 oh_transid;
	int		 oh_rdmo;
	int		 oh_rr;
};
	
struct htcp_opdata {
	virtual ~htcp_opdata();
	virtual void	build  (marshalling_buffer &) const = 0;
	virtual bool	decode (marshalling_buffer &) = 0;
	virtual size_t	length (void) const = 0;
};

struct htcp_opdata_nop : htcp_opdata {
	void	build(marshalling_buffer &buf) const {
		buf.append<uint16_t>(htons(2));
	}
	bool	decode(marshalling_buffer &buf) { 
	uint16_t	len;
		if (!buf.extract<uint16_t>(len))
			return false;
		if (!buf.discard_bytes(ntohs(len) - 2))
			return false;
		return true;
	}

	size_t	length(void) const { return 2; }
};

struct htcp_opdata_clr : htcp_opdata {
	void	build(marshalling_buffer &) const;
	bool	decode(marshalling_buffer &);
	size_t	length(void) const;

	int		clr_reason;
	htcp_specifier	clr_specifier;
};

struct htcp_opdata_clr_resp : htcp_opdata {
	void	build(marshalling_buffer &buf) const {
		buf.append<uint16_t>(htons(2));
	}

	bool	decode(marshalling_buffer &buf) { 
	uint16_t	len;
		if (!buf.extract<uint16_t>(len))
			return false;
		if (!buf.discard_bytes(ntohs(len) - 2))
			return false;
		return true;
	}
	size_t	length(void) const { return 2; }
};

struct htcp_opdata_tst : htcp_opdata {
	void	build(marshalling_buffer &) const;
	bool	decode(marshalling_buffer &);
	size_t	length(void) const;

	htcp_specifier	tst_specifier;
};

struct htcp_opdata_tst_resp_found : htcp_opdata
{
	void	build(marshalling_buffer &) const;
	bool	decode(marshalling_buffer &);
	size_t	length(void) const;

	htcp_detail	tf_detail;
};

struct htcp_opdata_tst_resp_notfound : htcp_opdata
{
	void	build(marshalling_buffer &) const;
	bool	decode(marshalling_buffer &);
	size_t	length(void) const;

	vector<string>	tn_cachehdrs;
};

struct htcp_auth {
	void	build(marshalling_buffer &) const;
	bool	decode(marshalling_buffer &);
	size_t	length(void) const;

	uint16_t	ha_length;
	uint32_t	ha_sigtime;
	uint32_t	ha_sigexpire;
	string		ha_keyname;
	ustring		ha_signature;
};

struct htcp_encoder {
	htcp_encoder();

	uint32_t	 transid (void) const;
	void		 transid (uint32_t);
	void		 opcode	 (int);
	void		 response(int);
	void		 rd	 (bool);
	void		 mo	 (bool);
	void		 rr	 (bool);
	htcp_opdata	*opdata  (void);
	void		 opdata	 (htcp_opdata *);
	void		 key	 (string const &, ustring const &);

	bool		 build_packet	(sockaddr const *, sockaddr const *);
	char const	*packet		(void) const;
	size_t		 packet_length	(void) const;

	template<typename charT, typename traits, typename alloc>
	static typename enable_if<is_char_type<charT>, void>::type
	encode_countstr(marshalling_buffer &,
			basic_string<charT, traits, alloc> const &);

	static void encode_headerlist(marshalling_buffer &, vector<string> const &);
	static void encode_sockaddr(marshalling_buffer &, sockaddr const *);

private:
	marshalling_buffer	 _buf;
	bool			 _okay;
	bool			 _sign;
	ustring			 _key;

	htcp_header		 _header;
	htcp_opheader		 _opheader;
	htcp_opdata 		*_opdata;
	htcp_auth		 _auth;

	char	*_built;
	size_t	 _builtsz;
};

template<typename charT, typename traits, typename alloc>
typename enable_if<is_char_type<charT>, void>::type
htcp_encoder::encode_countstr(marshalling_buffer &buf,
	basic_string<charT, traits, alloc> const &s)
{
int	len = min((size_t) numeric_limits<uint16_t>::max(), s.size());
	buf.append<uint16_t>(htons(len));
	buf.append_bytes(s.data(), len);
}

struct sockaddr;

struct htcp_decoder {
	htcp_decoder(char const *buf, size_t sz);
	~htcp_decoder();

	size_t			 length    (void) const;
	uint32_t		 transid   (void) const;
	int			 opcode	   (void) const;
	int			 response  (void) const;
	bool			 rd	   (void) const;
	bool			 mo	   (void) const;
	bool			 rr	   (void) const;
	htcp_opdata const	*opdata	   (void) const;
	string			 keyname   (void) const;
	ustring			 signature (void) const;

	bool		okay		(void) const;
	int		majorver	(void) const;
	int		minorver	(void) const;
	
	bool verify_signature(string const &keyname, ustring const &key,
			sockaddr const *source, sockaddr const *dest);

	template<typename charT, typename traits, typename allocator>
	static bool decode_countstr(marshalling_buffer &,
			basic_string<charT, traits, allocator> &);
	static bool decode_headerlist(marshalling_buffer &, vector<string> &);

private:
	marshalling_buffer	_buf;
	bool			_okay;

	htcp_header	 _header;
	htcp_opheader	 _opheader;
	htcp_opdata	*_opdata;
	htcp_auth	 _auth;
};

template<typename charT, typename traits, typename allocator>
bool htcp_decoder::decode_countstr(marshalling_buffer &buf,
		basic_string<charT, traits, allocator> &ret)
{
uint16_t	sz;
vector<charT>	data;
	if (!buf.extract<uint16_t>(sz))
		return false;
	sz = ntohs(sz);
	if (!buf.extract_bytes(data, sz))
		return false;
	ret.assign(data.begin(), data.end());
	return true;
}


bool htcp_init(void);

#endif
