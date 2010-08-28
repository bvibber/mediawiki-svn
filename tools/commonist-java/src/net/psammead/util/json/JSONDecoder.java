package net.psammead.util.json;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import net.psammead.util.JavaLiteral;
import net.psammead.util.StringUtil;

/** 
 * deserializes JSON strings
 * 
 * @see <a href="http://www.ietf.org/rfc/rfc4627.txt?number=4627">http://www.ietf.org/rfc/rfc4627.txt?number=4627</a> 
 * @see <a href="http://www.json.org/">http://www.json.org/<a>
 */
public final class JSONDecoder {
	public static Object decode(String text) throws JSONDecodeException {
		return new JSONDecoder(text).decode();
	}
	
	private static final int NO_CHAR	= -1;
	
	private final String text;
	
	private int	offset;
	
	private JSONDecoder(String text) {
		this.text	= text;
		offset	= 0;
	}
	
	//-------------------------------------------------------------------------
	
	private Object decode() throws JSONDecodeException {
		final Object	value	= decodeNext();
		ws();
		if (!finished())	throw expected("end of input");
		return value;
	}

	private Object decodeNext() throws JSONDecodeException {
		ws();
		if (finished())		throw expected("any char");
		if (is("null"))		return null;
		if (is("true"))		return true;
		if (is("false"))	return false;
		if (is('[')) {
			final List<Object>	out	= new ArrayList<Object>();
			ws();
			if (is(']'))	return out;
			for (;;) {
				Object	val	= decodeNext();
				out.add(val);
				ws();
				if (is(','))	continue;
				if (is(']'))	return out;
				throw expectedClass(",]");
			}
		}
		if (is('{')) {
			final Map<Object,Object>	out	= new HashMap<Object, Object>();
			ws();
			if (is('}'))	return out;
			for (;;) {
				final Object	key	= decodeNext();
				if (!(key instanceof String))	throw expected("string key");
				ws();
				if (!is(':'))	throw expectedClass(":");
				final Object	val	= decodeNext();
				out.put(key, val);
				ws();
				if (is(','))	continue;
				if (is('}'))	return out;
				throw expectedClass(",}");
			}
		}
		if (is('"')) {
			final StringBuilder	out	= new StringBuilder();
			for (;;) {
				if (is('\\')) {
					if (finished())	throw expected("escape continuation");
						 if (is('"'))	out.append('"');
					else if (is('\\'))	out.append('\\');
					else if (is('/'))	out.append('/');
					else if (is('t'))	out.append('\t');
					else if (is('r'))	out.append('\r');
					else if (is('n'))	out.append('\n');
					else if (is('f'))	out.append('\f');
					else if (is('b'))	out.append('\b');
					else if (is('u')) {
						if (offset+4 > text.length())	throw expected("4 hex digits");
						final int	before	= offset;
						offset += 4;
						try {
							out.append((char)(Integer.parseInt(from(before), 16)));
						}
						catch (NumberFormatException e) {
							offset -= 4;
							throw expected("4 hex digits");
						}
					}
					else throw expectedClass("\"\\/trnfbu");
				}
				else if (is('"')) {
					return out.toString();
				}
				else if (rng('\u0000', '\u001f')) {
					offset--;
					throw expected("no control character");
				}
				else {
					if (finished())	throw expected("more chars");
					out.append((char)next());
					consume();
				}
			}
		}
		int	before	= offset;
		is('-');
		// TODO json: leading zeroes are not allowed!
		if (!digits())		throw expected("digits");
		if (!is('.'))		return Long.parseLong(from(before));
		if (!digits())		throw expected("digits");
		boolean	exp	= is('e') || is('E');
		if (!exp)			return Double.parseDouble(from(before));
		if (!is('+'))	is('-');
		if (!digits())		throw expected("digits");
		return Double.parseDouble(from(before));
	}
	
	private JSONDecodeException expected(String what) {
		return new JSONDecodeException(text, offset, what);
	}
	
	private JSONDecodeException expectedClass(String charClass) {
		final List<String>	strs	= new ArrayList<String>();
		for (char c : charClass.toCharArray())	strs.add(JavaLiteral.encodeChar(c));
		final String	what	= StringUtil.join(strs, " or ");
		return new JSONDecodeException(text, offset, what);
	}

	//-------------------------------------------------------------------------
	//## tokens

	private boolean digits() {
		final int	before	= offset;
		while (!finished()) {
			int	c	= next();
			if (c >= '0' 
			&& c <= '9')	consume();
			else			break;
		}
		return offset != before;
	}
	
	private void ws() {
		while (!finished()) {
			final int	c	= next();
			if (c == ' '  
			|| c == '\t' 
			|| c == '\r' 
			|| c == '\n')	consume();
			else			break;
		}
	}
	
//	private boolean ws() {
//		int	before	= offset;
//		for (;;) {
//			if (is(' '))	continue;
//			if (is('\t'))	continue;
//			if (is('\r'))	continue;
//			if (is('\n'))	continue;
//			break;
//		}
//		return offset != before;
//	}
	
	private boolean rng(char start, char end) {
		if (finished())		return false;
		final int	c	= next();
		if (c < start 
		|| c > end)			return false;
		consume();			return true;
	}
	
	private boolean is(char c) {
		if (finished())		return false;
		if (c != next())	return false;
		consume();			return true;
	}
	
	private boolean is(String s) {
		final int	end	= offset + s.length();
		if (end > text.length())					return false;
		if (!s.equals(text.substring(offset, end)))	return false;
		offset	= end;
		return true;
	}
	
	//-------------------------------------------------------------------------
	//## chars
	
	private String from(int before) {
		return text.substring(before, offset);
	}
	
	private int next() {
		if (finished())	return NO_CHAR;
		return text.charAt(offset);
	}
	
	private void consume() {
		if (finished())	throw new RuntimeException("already finished");
		offset++;
	}

	private boolean finished() {
		return offset == text.length();
	}
}
