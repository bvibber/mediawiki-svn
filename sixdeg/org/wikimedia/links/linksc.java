/*
 * Six degrees of Wikipedia: Java client.
 * This source code is released into the public domain.
 *
 * @(#)linksc.java	1.1 05/11/21 21:02:32
 */
package org.wikimedia.links;

public class linksc {
	public native String[] findPath(String from, String to) throws ErrorException;
	static {
		System.loadLibrary("linksc_jni");
	}

	public static void main(String[] args) {
		linksc c = new linksc();
		String[] result = null;
		try {
			result = c.findPath(args[0], args[1]);
		} catch (ErrorException e) {
			System.out.printf("Error: %s\n", e.geterror());
			return;
		}
		for (String s: result)
			System.out.println(s);
	}
}
