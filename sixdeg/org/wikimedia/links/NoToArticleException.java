/*
 * Six degrees of Wikipedia: Java client.
 * This source code is released into the public domain.
 *
 * $URL: file:///home/river/s2s/linksd/org/wikimedia/links/NoToArticleException.java $ %E% %U%
 */
package org.wikimedia.links;

public class NoToArticleException extends ErrorException {
	public NoToArticleException() {
		super("Target article does not exist.");
	}
}
