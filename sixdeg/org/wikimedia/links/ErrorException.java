/*
 * Six degrees of Wikipedia: Java client.
 * This source code is released into the public domain.
 *
 * $URL: file:///home/river/s2s/linksd/org/wikimedia/links/ErrorException.java $ %E% %U%
 */
package org.wikimedia.links;

public class ErrorException extends Exception {
	String err;

	public ErrorException(String what) {
		err = what;
	}

	public String geterror() {
		return err;
	}
}
