package net.psammead.mwapi.ui.action.response;

import java.util.regex.Pattern;

import net.psammead.util.StringUtil;
import net.psammead.util.XMLCodec;

/** a pattern matching responses */
public final class ResponsePattern {
	public static final int		WILD_CODE		= -1;
	public static final Pattern	WILD_PATTERN	= null;
	
	public final int		code;
	public final Pattern	pattern;
	
//	public ResponsePattern() {
//		this(WILD_CODE, WILD_PATTERN);
//	}
	
	public ResponsePattern(int code) {
		this(code, WILD_PATTERN);
	}
	
	public ResponsePattern(String pattern) {
		this(WILD_CODE, Pattern.compile("(?m)" + ".*" + StringUtil.escapeRegexp(pattern) + ".*"));
	}
	
	public ResponsePattern(int code, Pattern pattern) {
		this.code		= code;
		this.pattern	= pattern;
	}
	
	public boolean match(int responseCode, String responseBody) {
		return (code    == WILD_CODE    || code == responseCode)
			&& (pattern == WILD_PATTERN || match(responseBody));
	}

	private boolean match(String responseBody) {
		// TODO: decoding entities here is a bit of a hack..
		final String	decoded	= XMLCodec.decode(responseBody, false, false);
		//System.err.println("??? ResponsePattern match: " + pattern);
		final boolean	matches	= pattern.matcher(decoded).matches();
		//System.err.println("??? ResponsePattern result: " + matches);
		return matches;
//		return XMLCodec.decode(responseBody, false, false)
//						.matches(pattern);
	}
	
	/**  for debugging purposes only */
	@Override
	public String toString() {
		return "ResponsePattern"
			+ "{ code="	+ code
			+ ", pattern="	+ pattern
			+ " }";
	}
}