/*
 * Created on Jan 25, 2007
 *
 */
package org.wikimedia.lsearch.analyzers;

/**
 * Article used for testing. 
 * 
 * @author rainman
 *
 */
public class TestArticle {
	public int namespace;
	public String title;
	public String content;
	
	TestArticle(){
		namespace = 0;
		title = "";
		content = "";
	}
	
	/* (non-Javadoc)
	 * @see java.lang.Object#toString()
	 */
	public String toString() {
		return "Article: "+namespace+":"+title+"\n"+content+"\n";
	}
}
