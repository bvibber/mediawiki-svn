/* Loreley: Lightweight HTTP reverse-proxy.	*/
/* md5: MD5 digest implementation.		*/

/* @(#) $Id$ */

#ifndef MD5_H
#define MD5_H

/*
 **********************************************************************
 ** md5.h -- Header file for implementation of MD5                   **
 ** RSA Data Security, Inc. MD5 Message Digest Algorithm             **
 ** Created: 2/17/90 RLR                                             **
 ** Revised: 12/27/90 SRD,AJ,BSK,JT Reference C version              **
 ** Revised (for MD5): RLR 4/27/91                                   **
 **   -- G modified to have y&~z instead of y&z                      **
 **   -- FF, GG, HH modified to add in last register done            **
 **   -- Access pattern: round 2 works mod 5, round 3 works mod 3    **
 **   -- distinct additive constant for each step                    **
 **   -- round 4 added, working mod 7                                **
 **********************************************************************
 */

/*
 **********************************************************************
 ** Copyright (C) 1990, RSA Data Security, Inc. All rights reserved. **
 **                                                                  **
 ** License to copy and use this software is granted provided that   **
 ** it is identified as the "RSA Data Security, Inc. MD5 Message     **
 ** Digest Algorithm" in all material mentioning or referencing this **
 ** software or this function.                                       **
 **                                                                  **
 ** License is also granted to make and use derivative works         **
 ** provided that such works are identified as "derived from the RSA **
 ** Data Security, Inc. MD5 Message Digest Algorithm" in all         **
 ** material mentioning or referencing the derived work.             **
 **                                                                  **
 ** RSA Data Security, Inc. makes no representations concerning      **
 ** either the merchantability of this software or the suitability   **
 ** of this software for any particular purpose.  It is provided "as **
 ** is" without express or implied warranty of any kind.             **
 **                                                                  **
 ** These notices must be retained in any copies of any part of this **
 ** documentation and/or software.                                   **
 **********************************************************************
 */

#include <cstdlib>
#include <inttypes.h>
using std::size_t;

#include "loreley.h"
#include "mbuffer.h"

/*
 * An implementation of the basic MD5 algorithm.
 */
struct md5 {
	/* The binary digest type */
	typedef unsigned char const (&digest_t)[16];
	/* Size of the digest */
	static const size_t digest_size = 16;
	/* Block size */
	static const size_t block_size = 64;

	md5(void) : _final(false) {
		_md5init(&_ctx);
	}

	template<typename charT>
	md5(charT *begin, charT *end);

	template<typename charT>
	md5(charT *begin, size_t len);

	template<typename iterator>
	void update(iterator begin, iterator end);

	template<typename charT>
	typename enable_if<is_char_type<charT>, void>::type
	update(charT *begin, size_t len);

	digest_t digest(void) const;
	string   strdigest(void) const;

	digest_t digest(void);
	string   strdigest(void);

private:
	/* Data structure for MD5 (Message Digest) computation */
	struct ctx_t {
		uint32_t i[2];			/* number of _bits_ handled mod 2^64 */
		uint32_t buf[4];		/* scratch buffer */
		unsigned char in[64];		/* input buffer */
		unsigned char digest[16];	/* actual digest after MD5Final call */
	};


	static void	_md5init(ctx_t *);
	static void	_md5update(ctx_t *, unsigned char const *, size_t);
	static void	_md5final(ctx_t *);

	void _finalise(void);

	ctx_t	_ctx;
	bool	_final;
};

/*
 * An HMAC implementation that can be used with any hash function implementing
 * the previous API.
 */
template<typename hash>
struct hmac {
	static const size_t block_size = hash::block_size;
	static const size_t digest_size = hash::digest_size;

	typedef typename hash::digest_t digest_t;

	hmac(unsigned char const *key_, size_t keylen) 
		: _done(false) {
		init();
		key(key_, keylen);
	}

	void init(void) {
		memset(_ipad, 0x36, sizeof(_ipad));
		memset(_opad, 0x5C, sizeof(_opad));
	}

	void key(unsigned char const *nkey, size_t keylen) {
		if (keylen > block_size) {
		hash	h(nkey, keylen);
			memcpy(_key, h.digest(), digest_size);
			_keylen = digest_size;
		} else {
			memcpy(_key, nkey, keylen);
			_keylen = keylen;
		}

		while (_keylen < block_size)
			_key[_keylen++] = 0;
	}

	template<typename charT>
	typename enable_if<is_char_type<charT>, void>::type
	run(charT const *buf, size_t len) {
	vector<unsigned char>	ibuf(block_size + len);
		memcpy(&ibuf[0], _key, block_size);
		for (size_t i = 0; i < block_size; ++i)
			ibuf[i] ^= _ipad[i];
		memcpy(&ibuf[block_size], buf, len);
	hash	ihash(&ibuf[0], ibuf.size());

	vector<unsigned char>	obuf(block_size + digest_size);
		memcpy(&obuf[0], _key, block_size);
		for (size_t i = 0; i < block_size; ++i)
			obuf[i] ^= _opad[i];
		memcpy(&obuf[block_size], ihash.digest(), digest_size);
	hash	ohash(&obuf[0], obuf.size());
		memcpy(_result, ohash.digest(), digest_size);
		_done = true;
	}

	digest_t digest(void) const {
		if (!_done)
			throw logic_error("digest not computed");
		return _result;
	}

private:
	marshalling_buffer	_buf;
	hash			_hash;
	bool			_done;

	unsigned char		_key[block_size];
	size_t			_keylen;
	unsigned char		_ipad[block_size];
	unsigned char		_opad[block_size];
	unsigned char		_result[digest_size];
};

template<typename charT>
md5::md5(charT *begin, charT *end)
	: _final(false)
{
	_md5init(&_ctx);
	update(begin, end);
}

template<typename charT>
md5::md5(charT *begin, size_t len)
	: _final(false)
{
	_md5init(&_ctx);
	update(begin, len);
}

template<typename iterator>
void
md5::update(iterator begin, iterator end)
{
	update(begin, end - begin);
}

template<typename charT>
typename enable_if<is_char_type<charT>, void>::type
md5::update(charT *begin, size_t len)
{
vector<unsigned char> buf(begin, begin + len);
	_md5update(&_ctx, begin, len);
}
	
#endif
