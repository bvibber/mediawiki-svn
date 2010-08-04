package net.psammead.commonist.util;

import net.psammead.util.DebugUtil;

/** text utility functions */
public final class TextUtil2 {
	/** fully static utility class, shall not be instantiated */
	private TextUtil2() {}
	
	/** encode a number of bytes into a human readable form */
	public static String human(long bytes) {
		final long[]	factors	= { 1024*1024*1024, 1024*1024, 1024 };
		final String[]	names	= { "G",            "M",       "K"  };
		for (int i=0; i<factors.length; i++) {
			if (bytes < factors[i])	continue;
			bytes	= bytes * 10 / factors[i];
			return (bytes / 10) + "." + (bytes % 10) + names[i];
		}
		return ""+bytes;
	}

//	/** returns the class name of an object without its package */
//	public static String shortClass(Object o) {
//		return o.getClass().getName().replaceAll(".*\\.", "");
//	}
	
	/** returns a single-line throwable description */
	public static String shortError(Throwable t) {
		return DebugUtil.shortClassName(t) + " " + feedToSpace(t.getMessage());
	}
	
	/** replaces every linefeeds with a space */
	public static String feedToSpace(String s) {
		return s.replaceAll("\r\n|\r|\n", " ");
	}
	
	/** removes double empty lines */
	public static String restrictEmptyLines(String s) {
		return s.replaceAll("\n\n\n+", "\n\n");
	}
	
	/** concatenates two Strings and inserts a separator if both are non-empty */
	public static String joinNonEmpty(String string1, String string2, String separator) {
		return string1.length() != 0 && string2.length() != 0
				? string1 + separator + string2
				: string1 + string2;
	}
}
