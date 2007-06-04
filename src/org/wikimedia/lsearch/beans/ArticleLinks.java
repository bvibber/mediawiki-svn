package org.wikimedia.lsearch.beans;

import java.util.ArrayList;

/** 
 * Class used by XML Importer to keep track of links between
 * articles. This class is a descriptor of links, should be a
 * value for some key (e.g. prefixed article name) in the hashtable. 
 * 
 * @author rainman
 *
 */
public class ArticleLinks {
	/** Number of linking articles */
	public int links;
	/** if this is redirect, point to the target title */
	public ArticleLinks redirectsTo;
	/** all the pages that get redirected here */
	public ArrayList<String> redirected;
	
	public ArticleLinks(int links) {
		this.links = links;
		redirectsTo = null;
	}

	public ArticleLinks(int links, ArticleLinks redirect) {
		this.links = links;
		this.redirectsTo = redirect;
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + links;
		result = PRIME * result + 0;
		return result;
	}
	
	
}
