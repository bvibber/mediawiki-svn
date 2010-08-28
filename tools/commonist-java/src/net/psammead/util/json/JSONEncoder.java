package net.psammead.util.json;

import java.util.*;

/**
 * serializes certain values to JSON strings 
 *
 * @see <a href="http://www.ietf.org/rfc/rfc4627.txt?number=4627">http://www.ietf.org/rfc/rfc4627.txt?number=4627</a> 
 * @see <a href="http://www.json.org/">http://www.json.org/<a>
 */
public final class JSONEncoder {
	/** encodes without any special mappers */
	public static String encode(Object o) { return new JSONEncoder().json(o); }
	
	private Map<Class<?>, JSONMapper>	mappers;
	
	/** function collection */
	public JSONEncoder() {
		mappers	= new IdentityHashMap<Class<?>, JSONMapper>();
	}
	
	/** registers a mapper to be used to convert arbitraty objects into JSONifyable values */
	public void register(Class<?> type, JSONMapper mapper) {
		mappers.put(type, mapper);
	}
	
	/** 
	 * turns primitive values, Strings, Maps, Lists and null values into JSON code.
	 *   
	 * the object to serialize may not contain circular references. 
	 * multiple references to the same object are interpreted as multiple equal objects. 
	 */
	public String json(Object o) {
		// null
		if (o == null)				return "null";
		// mapped types
		final Class<?>		type	= o.getClass();
		final JSONMapper	mapper	= mappers.get(type);
		if (mapper != null)			return json(mapper.map(o));
		// simple types
		if (o instanceof Boolean)	return o.toString();
		if (o instanceof Number)	return o.toString();
		if (o instanceof String)	return stringLiteral((String)o);
		// structure types
		if (o instanceof List<?>)	return jsonList((List<?>)o);
		if (o instanceof Map<?,?>)	return jsonMap((Map<?,?>)o);
		// problem
		throw new JSONMappingException("cannot JSONify type: " + type.getName());
	}
	
	/** convert a List into [ value, value ... ] */
	private String jsonList(List<?> elements) {
		final StringBuilder	out	= new StringBuilder();
		for (Object element : elements) {
			out.append(json(element));
			out.append(",\n");
		}
		if (elements.size() != 0) {
			out.setLength(out.length() - ",\n".length());
			out.append("\n");
		}
		return "[\n" + indent(out.toString()) + "]";
	}
	
	/** convert a Map into { key: value, key: value ... } */
	private String jsonMap(Map<?,?> elements) {
		final StringBuilder	out	= new StringBuilder();
		for (Object key : elements.keySet()) {
			out.append(json(key));
			out.append(": ");
			out.append(json(elements.get(key)));
			out.append(",\n");
		}
		if (elements.size() != 0) {
			out.setLength(out.length() - ",\n".length());
			out.append("\n");
		}
		return "{\n" + indent(out.toString()) + "}";
	}
	
	/** indents a multi-line string */
	private String indent(String s) {
		return s.replaceAll("(?m)^", "  ");
	}
	
	/**
	 * converts a String into a double-quoted JavaScript literal
	 *	\b		Backspace	
	 *	\f		Form feed
	 *	\n		New line
	 *	\r		Carriage return
	 *	\t		Tab
	 *	\"		Double quote
	 *	\\		Backslash character (\)
	 *	\ uXXXX	The Unicode character specified by the four hexadecimal digits XXXX. 
	 */
	public String stringLiteral(String s) {
		final char[]			in	= s.toCharArray();
		final StringBuilder	out	= new StringBuilder();
		out.append('"');
		for (int i=0; i<in.length; i++) {
			final char	c	= in[i];
			switch (c) {
				// these two are mandatory
				case '"':	out.append("\\\"");	break;
				case '\\':	out.append("\\\\");	break;
				// this would be allowed but is ugly
				//case '/':	out.append("\\/");	break;
				// these are optional
				case '\b':	out.append("\\b");	break;
				case '\f':	out.append("\\f");	break;
				case '\n':	out.append("\\n");	break;
				case '\r':	out.append("\\r");	break;
				case '\t':	out.append("\\t");	break;
				default:
					// these are mandatory
					if (c<32) {
						out.append("\\u");
						final String	hex	= Integer.toHexString(c);
						for (int j=hex.length(); j<4; j++)	out.append('0');
						out.append(hex);
					}
					// these could be escaped
					else {
						out.append(c);
					}
			}
		}
		out.append('"');
		return out.toString();
	}
}
