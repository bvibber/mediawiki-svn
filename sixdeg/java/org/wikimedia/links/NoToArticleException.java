/* $Id$ */
/*
 * Six degrees of Wikipedia: Java client.
 * This source code is released into the public domain.
 */
package org.wikimedia.links;

public class NoToArticleException extends ErrorException {
	public NoToArticleException() {
		super("Target article does not exist.");
	}
}
