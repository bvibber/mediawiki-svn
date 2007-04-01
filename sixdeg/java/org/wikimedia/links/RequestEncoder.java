/* $Id$ */
/*
 * Six degrees of Wikipedia: Request protocol encoder.
 */

package org.wikimedia.links;

import java.util.Map;
import java.io.UnsupportedEncodingException;

/*
 * See encode_decode.cc for a description of the wire protocol.
 */
public class RequestEncoder {
	static final byte[] trailer = { 0, 0, 0, 0 };

	public byte[] encodeRequest(Map<String, String> args) {
		/*
		 * We need 5 bytes for each argument + the size of the args.
		 */
		int req_size = 4; /* 4 for trailer */
		for (Map.Entry<String, String> elem : args.entrySet()) {
			req_size += 5 + elem.getKey().length() + elem.getValue().length();
		}

		byte[] req = new byte[req_size + 4];
		int curpos = 0;

		try {
			for (Map.Entry<String, String> elem : args.entrySet()) {
				byte[] key = elem.getKey().getBytes("UTF-8");
				byte[] value = elem.getValue().getBytes("UTF-8");
				int thissize = key.length + value.length + 1;

				byte[] encsize = {
					(byte) ((thissize & 0xFF000000) >> 24),
					(byte) ((thissize & 0x00FF0000) >> 16),
					(byte) ((thissize & 0x0000FF00) >> 8),
					(byte)  (thissize & 0x000000FF)
				};

				System.arraycopy(encsize, 0, req, curpos, encsize.length);
				curpos += 4;

				System.arraycopy(key, 0, req, curpos, key.length);
				curpos += key.length;
				req[curpos] = "=".getBytes("UTF-8")[0];
				curpos++;
				System.arraycopy(value, 0, req, curpos, value.length);
				curpos += value.length;
			}

			System.arraycopy(trailer, 0, req, curpos, trailer.length);
		} catch (UnsupportedEncodingException e) {
			/* can never happen */
			System.exit(1);
		}

		return req;
	}
}
