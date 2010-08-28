package net.psammead.util;

import java.io.PrintWriter;
import java.io.StringWriter;

import net.psammead.util.annotation.FullyStatic;

/** miscellaneuos utility functions */
@FullyStatic 
public final class DebugUtil {
	/** function collection, shall not be instantiated */
    private DebugUtil() {}
    
    /** return the last component of the class name of an Object */
    public static String shortClassName(Object o) {
    	if (o == null)	return "null";
        return shortType(o.getClass());
    }
    
    /** return the last component of the class name of an Object */
    public static String shortType(Class<?> c) {
        return shortType(c.getName());
    }

    /** returns the name of a fully qualified class name */
    public static String shortType(String type) {
        return type.replaceAll(".*\\.", "");
    } 
	
	/** convert the StackTrace of a Throwable into a String */
	public static String extractStackTrace(Throwable t) {
		final StringWriter    sw  = new StringWriter();
		final PrintWriter     pw  = new PrintWriter(sw);
        t.printStackTrace(pw);  pw.close();
        return sw.toString();
    }
}
