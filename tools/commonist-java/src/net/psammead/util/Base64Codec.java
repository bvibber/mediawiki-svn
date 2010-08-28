package net.psammead.util;

import net.psammead.util.annotation.FullyStatic;

/** encodes and decodes Base64 */
@FullyStatic 
public final class Base64Codec {
	/** function collection, shall not be instantiated */
	private Base64Codec() {}
	
	/** encode for String */
	public static String encodeStr(String data) { return encode(data.getBytes());  }
	
	/** decode for String */
	public static String decodeStr(String data) { return new String(decode(data)); }
	
	/** encode a byte[] to a String */
	public static String encode(byte [] data) {
		final int 	len		= data.length;
		final int 	packets	= len / 3;	// volle packete
		String	s		= "";
		int 	idx		= 0;
		// erstmal komplette packets schreiben
		for (int i=0; i<packets; i++) {
			s += EN_TABLE[ ((data[idx+0] >> 2) & 0x3f) ];
			s += EN_TABLE[ ((data[idx+0] << 4) & 0x30) | ((data[idx+1] >> 4) & 0x0f) ];
			s += EN_TABLE[ ((data[idx+1] << 2) & 0x3c) | ((data[idx+2] >> 6) & 0x03) ];
			s += EN_TABLE[ ((data[idx+2]     ) & 0x3f) ];
			idx += 3;
		}
		switch (len % 3) {
			case 1:	// 1 byte noch	=>	2 terminatoren
					s += EN_TABLE[ ((data[idx+0] >> 2) & 0x3f) ];
					s += EN_TABLE[ ((data[idx+0] << 4) & 0x30) ];
					s += TERMINATOR;
					s += TERMINATOR;
					break;
			case 2:	// 2 byte noch	=>	1 terminator
					s += EN_TABLE[ ((data[idx+0] >> 2) & 0x3f) ];
					s += EN_TABLE[ ((data[idx+0] << 4) & 0x30) | ((data[idx+1] >> 4) & 0x0f) ];
					s += EN_TABLE[ ((data[idx+1] << 2) & 0x3c) ];
					s += TERMINATOR;
					break;
		}
		return s;
	}

	/** decodes a String to a byte[] */
	public static byte[] decode(String text) {
		if (text.length() == 0) return new byte[0];
		final char[]	in		= text.toCharArray();
		int		ilen	= in.length;
		int		olen	= ((ilen + 3 ) / 4 ) * 3;
		if (in[ilen-1] == TERMINATOR) olen --;
		if (in[ilen-2] == TERMINATOR) olen --;
		final byte[]	out		= new byte[olen];
		int		shift	= 0;
		int		akku	= 0;
		int		op		= 0;
		for (int i=0; i<ilen; i++) {
			int value = DE_TABLE[ in[i] ];
			if (value < 0) continue;
			shift	 +=	6;
			akku	<<=	6;
			akku	 |=	value;
			if (shift >= 8) {
				shift -= 8;
				out[op++] = (byte)((akku >> shift) & 0xff);
			}
		}
		return out;
	}
	
	/** strips irrelevant characters for base64 */
	public static String stripGarbage(String data) {
		final int	len	= data.length();
		String	str	= "";
		int		i;
		char	c	= 0;
		int		x	= -1;
		for (i=0; i<len; i++) {
			c = data.charAt(i);
			x = DE_TABLE[c];
			     if (x >= 0)	str += c;
			else if(x == -2)	break;
		}
		while ((i < len) && (x == -2)) {
			str	+= c;
			c	= data.charAt(++i);
			x	= DE_TABLE[c];
		}
		return str;
	}

	/** adds CRLFs to an encoded String to enhance readability */
	public static String insertCRLF(String code) {
		final int 	len		= code.length();
		final int	lines	= len / WIDTH;
		String	s 		= "";
		int		index	= 0;
		for (int i=0; i<lines; i++) {
    		s += code.substring(index, index+WIDTH) + "\r\n";
			index += WIDTH;
		}
		final int	left	= len % WIDTH;
		if (left > 0) {
			s += code.substring(len - left) + "\r\n";
		}
		return s;
	}
	
	private static final char	TERMINATOR	= '=';
	private	static final int	WIDTH		= 76;
	private static final char[] EN_TABLE 	= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=".toCharArray();
	private static final byte[] DE_TABLE		= new byte[256];
	static {
		for (int i=0; i<256; i++) DE_TABLE[i] = -1;
		for (int i = 'A'; i <= 'Z'; i++) DE_TABLE[i] = (byte)(     i - 'A');
		for (int i = 'a'; i <= 'z'; i++) DE_TABLE[i] = (byte)(26 + i - 'a');
		for (int i = '0'; i <= '9'; i++) DE_TABLE[i] = (byte)(52 + i - '0');
		DE_TABLE['+'] = 62;
		DE_TABLE['/'] = 63;
		DE_TABLE['='] = -2;
	}
}
