package de.brightbyte.wikiword.builder.util;

import java.io.UnsupportedEncodingException;

import de.brightbyte.data.Functor;

public class StringEncoder implements Functor<byte[], String> {

	protected String encoding;
	
	public static final StringEncoder ISO_8859_15;
	public static final StringEncoder UTF_8;
	public static final StringEncoder UTF_16;
	
	static {
		try {
			ISO_8859_15 = new StringEncoder("ISO-8859-15");
			UTF_8 = new StringEncoder("UTF-8");
			UTF_16 = new StringEncoder("UTF-16");
		} catch (UnsupportedEncodingException e) {
			throw new RuntimeException("well known encoding failed", e);
		}
	}
	
	public StringEncoder(String encoding) throws UnsupportedEncodingException {
		if (encoding==null) throw new NullPointerException(); 
		this.encoding = encoding;
		"".getBytes(encoding);
	}

	public byte[] apply(String s) {
		try {
			return s.getBytes(encoding);
		} catch (UnsupportedEncodingException e) {
			throw new IllegalArgumentException(e);
		}
	}
	
	public String toString() {
		return getClass().getName()+"("+encoding+")";
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((encoding == null) ? 0 : encoding.hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final StringEncoder other = (StringEncoder) obj;
		if (encoding == null) {
			if (other.encoding != null)
				return false;
		} else if (!encoding.equals(other.encoding))
			return false;
		return true;
	}

	
}
