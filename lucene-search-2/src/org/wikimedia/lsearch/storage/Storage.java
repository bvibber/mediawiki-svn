package org.wikimedia.lsearch.storage;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.List;
import java.util.Map.Entry;

import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.related.CompactArticleLinks;
import org.wikimedia.lsearch.related.Related;
import org.wikimedia.lsearch.related.RelatedTitle;
@Deprecated
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
	
	/**
	 * Store some related mappings
	 */
	abstract public void storeRelatedPages(Collection<Related> related, String dbname) throws IOException;

	/**
	 * Get related mapping for a collection of titles
	 */
	abstract public HashMap<Title,ArrayList<RelatedTitle>> getRelatedPages(Collection<Title> titles, String dbname) throws IOException;

}
