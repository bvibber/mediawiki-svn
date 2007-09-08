package org.wikimedia.lsearch.storage;

import java.util.Collection;

import org.wikimedia.lsearch.related.Related;

/**
 * Various link analysis info about the article
 *  
 * @author rainman
 *
 */
public class ArticleAnalytics {
	String key;
	int references; 
	String redirectTarget;
	Collection<String> anchorText;
	Collection<Related> related;
	Collection<String> redirectKeys;
	
	/**
	 * @param key -  article key (ns:title)
	 * @param references - number of links to article
	 * @param redirectTarget - if article is redirect - target article key, otherwise null
	 * @param anchorText - anchor texts 
	 * @param relatedKeys - related articles (ns:title)
	 * @param redirectKeys - articles that redirect here (ns:title)
	 * 
	 */
	public ArticleAnalytics(String key, int references, String redirectTarget, Collection<String> anchorText, Collection<Related> related, Collection<String> redirectKeys) {
		this.key = key;
		this.references = references;
		this.redirectTarget = redirectTarget;
		this.anchorText = anchorText;
		this.related = related;
		this.redirectKeys = redirectKeys;
	}
	
	@Override
	public String toString() {
		return key+" : ref="+references+", redirect_to="+redirectTarget+", anchor="+anchorText+", redirects="+redirectKeys+", related="+related;
	}

	public boolean isRedirect(){
		return redirectTarget != null;
	}
	
	public int getRedirectTargetNamespace(){
		if(redirectTarget != null)
			return Integer.parseInt(redirectTarget.substring(0,redirectTarget.indexOf(':')));
		return 0;
			
	}


	public Collection<String> getAnchorText() {
		return anchorText;
	}

	public void setAnchorText(Collection<String> anchorText) {
		this.anchorText = anchorText;
	}

	public String getKey() {
		return key;
	}

	public void setKey(String key) {
		this.key = key;
	}

	public Collection<String> getRedirectKeys() {
		return redirectKeys;
	}

	public void setRedirectKeys(Collection<String> redirectKeys) {
		this.redirectKeys = redirectKeys;
	}

	public String getRedirectTarget() {
		return redirectTarget;
	}

	public void setRedirectTarget(String redirectTarget) {
		this.redirectTarget = redirectTarget;
	}

	public int getReferences() {
		return references;
	}

	public void setReferences(int references) {
		this.references = references;
	}

	public Collection<Related> getRelated() {
		return related;
	}

	public void setRelated(Collection<Related> related) {
		this.related = related;
	}

	
	
}
