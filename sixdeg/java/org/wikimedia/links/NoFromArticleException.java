/*
 * Six degrees of Wikipedia: Java client.
 * This source code is released into the public domain.
 *
 * $URL: file:///home/river/s2s/linksd/org/wikimedia/links/NoFromArticleException.java $ %E% %U%
 */
package org.wikimedia.links;

public class NoFromArticleException extends ErrorException {
	public NoFromArticleException() {
		super("Source article does not exist.");
	}
}
