package org.wikimedia.lsearch.storage;

import java.io.IOException;
import java.util.Collection;

import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.ranks.CompactArticleLinks;

abstract public class Storage {
	static protected Storage instance = null;
	
	/** Get instance of Storage singleton class */
	public static synchronized Storage getInstance(){
		if(instance == null)
			instance = new MySQLStorage();
		return instance;
	}
	
	/**
	 * Store a complete array of page references
	 */
	abstract public void storePageReferences(Collection<CompactArticleLinks> refs, String dbname) throws IOException;
	
	/**
	 * Fetch page references for number of titles 
	 */
	abstract public Collection<CompactArticleLinks> getPageReferences(Collection<Title> titles, String dbname) throws IOException;

}
