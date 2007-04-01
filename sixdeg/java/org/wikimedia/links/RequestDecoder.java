/* $Id$ */
/*
 * Six degrees of Wikipedia: request decoder.
 */

package org.wikimedia.links;

import java.util.Map;
import java.util.HashMap;
import java.io.UnsupportedEncodingException;

/*
 * See encode_decode.cc for wire protocol description.
 */
public class RequestDecoder {
	public Map<String, String> decodeRequest(byte[] data) {
		Map<String, String> result = new HashMap<String, String>();

		int sz;
		int curpos = 0;

		for (;;) {
			sz = 
				  (int) data[curpos + 0] << 24
				| (int) data[curpos + 1] << 16
				| (int) data[curpos + 2] << 8
				| (int) data[curpos + 3];
			curpos += 4;
			
			if (sz == 0)
				break;

			String datum = null;
			try {
				datum = new String(data, curpos, sz, "UTF-8");
			} catch (UnsupportedEncodingException e) {
				/* can never happen */
				System.exit(1);
			}
			curpos += sz;

			String[] r = datum.split("=", 2);
			result.put(r[0], r[1]);
		}

		return result;
	}
}
