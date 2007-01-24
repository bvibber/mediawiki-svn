/*
 * Six degrees of Wikipedia: Java client.
 * This source code is released into the public domain.
 *
 * $URL: file:///home/river/s2s/linksd/org/wikimedia/links/ConnectException.java $ %E% %U%
 */
package org.wikimedia.links;

public class ConnectException extends ErrorException {
	public ConnectException() {
		super("Could not connect to links server.");
	}
}
