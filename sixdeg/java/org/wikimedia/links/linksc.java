/* $Id$ */
/*
 * Six degrees of Wikipedia: Java client.
 * This source code is released into the public domain.
 */
package org.wikimedia.links;

public class linksc {
	public native String[] findPath(String from, String to, boolean ignoreDates) throws ErrorException;
	static {
		System.loadLibrary("linksc_jni");
	}

	public static void main(String[] args) {
		linksc c = new linksc();
		String[] result = null;
		try {
			result = c.findPath(args[0], args[1], false);
		} catch (ErrorException e) {
			System.out.printf("Error: %s\n", e.geterror());
			return;
		}
		for (String s: result)
			System.out.println(s);
	}
}
