/* $Id$ */
/*
 * Six degrees of Wikipedia: Java client.
 * This source code is released into the public domain.
 */
package org.wikimedia.links;

public class ConnectException extends ErrorException {
	public ConnectException() {
		super("Could not connect to links server.");
	}
}
