package net.psammead.util;

import net.psammead.util.annotation.FullyStatic;


/** {@link Object} utility functions */
@FullyStatic 
public final class ObjectUtil {
	private ObjectUtil() {}
	
	public static String toString(Object o) {
		return ToString.stringifyAny(o);
    }
	
	/** treats null as a normal value where null == null and null != anything else */
	public static <T> boolean equals(T a, T b) {
		if (a == b)					return true;
		if (a == null || b == null)	return false;
		return a.equals(b);
	}
	
	public static int hashCode(Object... objects) {
		int	code	= 0;
		for (Object object : objects) {
			if (object == null)	continue;
			code	= code * 17 + object.hashCode();
		}
		return code;
	}
}
