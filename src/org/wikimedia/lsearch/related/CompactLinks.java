package org.wikimedia.lsearch.related;

import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.Map.Entry;

import org.wikimedia.lsearch.beans.ArticleLinks;

/**
 * Abstraction of links retrieval and other operations related to
 * CompactArticleLinks.
 * 
 * @author rainman
 *
 */
public class CompactLinks {
	protected HashMap<CompactArticleLinks,CompactArticleLinks> links = new HashMap<CompactArticleLinks,CompactArticleLinks>();
	
	public CompactLinks() {		
	}
	
	public CompactLinks(Collection<CompactArticleLinks> col){
		for(CompactArticleLinks c : col){
			links.put(c,c);
		}
	}
	
	/** Add new page with key and ref */
	public void add(String key, int ref){
		CompactArticleLinks cs = new CompactArticleLinks(key,ref);
		links.put(cs,cs);	
	}

	/** Setup redirect key -> tokey */
	public void setRedirect(String key, String tokey){
		CompactArticleLinks from = links.get(new CompactArticleLinks(key));
		CompactArticleLinks to = links.get(new CompactArticleLinks(tokey));
		from.redirectsTo = to;		
	}
	
	/** Setup redirect key -> to */
	public void setRedirect(String key, CompactArticleLinks to){
		CompactArticleLinks from = links.get(new CompactArticleLinks(key));
		from.redirectsTo = to;		
	}
	
	/** Get links object from key */
	public CompactArticleLinks get(String key){
		return links.get(new CompactArticleLinks(key));
	}
	
	/** Get collection of all links objects */
	public Collection<CompactArticleLinks> getAll(){
		return links.values();
	}
	
	/** Get number of references (links) to article */
	public int getLinks(String key){
		CompactArticleLinks c = links.get(new CompactArticleLinks(key));
		if(c == null)
			return 0;
		else 
			return c.links;
	}
	
	/** Generate "redirects here" lists for each article */
	public void generateRedirectLists(){
		for(CompactArticleLinks r : links.values()){
			if(r.redirectsTo != null && r != r.redirectsTo){
				r.redirectsTo.addRedirect(r);
			}
		}
	}

	/** Delete any unnecessary allocated space */
	public void compactAll() {
		for(CompactArticleLinks r : links.values()){
			r.compact();
		}		
	}
	
}
