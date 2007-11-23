package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;

import org.apache.lucene.analysis.Analyzer;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.util.Buffer;

/**
 * Titles and redirects, serialization/deserialization 
 * for highlighting, etc.. 
 * 
 * @author rainman
 *
 */
public class Alttitles {
	protected Info title;
	protected ArrayList<Info> redirects = new ArrayList<Info>();
	protected ArrayList<Info> sections = new ArrayList<Info>();
	
	public static class Info {
		protected String title;
		protected int rank;
		protected ArrayList<ExtToken> tokens;
		public Info(String title, int rank, ArrayList<ExtToken> tokens){
			this.title = title;
			this.rank = rank;
			this.tokens = tokens;
		}
		public int getRank() {
			return rank;
		}
		public void setRank(int rank) {
			this.rank = rank;
		}
		public String getTitle() {
			return title;
		}
		public void setTitle(String title) {
			this.title = title;
		}
		public ArrayList<ExtToken> getTokens() {
			return tokens;
		}
		public void setTokens(ArrayList<ExtToken> tokens) {
			this.tokens = tokens;
		}
		
	}
	
	public static byte[] serializeAltTitle(Article article, IndexId iid, Collection<String> sections, Analyzer analyzer, String field) throws IOException{
		WikiIndexModifier.transformArticleForIndexing(article);
		Buffer b = new Buffer();
		
		// add title
		String title = article.getTitle();
		// type 0 : title
		b.writeAlttitleInfo(title,new Aggregate(title,article.getRank(),iid,analyzer,field),0);
		// add all redirects
		ArrayList<String> redirects = article.getRedirectKeywords();
		ArrayList<Integer> ranks = article.getRedirectKeywordRanks();
		for(int i=0;i<redirects.size();i++){
			// type 1: redirect
			b.writeAlttitleInfo(redirects.get(i),new Aggregate(redirects.get(i),ranks.get(i),iid,analyzer,field),1);
		}
		
		// type 2: sections
		for(String s : sections){
			String cs = canonizeHeadline(s);
			if(cs != null)
				b.writeAlttitleInfo(cs,new Aggregate(cs,1,iid,analyzer,field),2);
		}

		return b.getBytes();
	}
	
	/** 
	 * Try to emulate the MediaWiki section headline canonization (for anchors)
	 * If cleanup fails (i.e. if there are templates in heading), returns null 
	 * @return
	 */
	public static String canonizeHeadline(String heading){
		if((heading.contains("{{") && heading.contains("}}"))
			|| (heading.contains("<ref") && heading.contains("</ref>")))
			return null;

		return heading.replaceAll("\\[\\[([^|]+?)\\]\\]", "$1")
		.replaceAll("\\[\\[([^|]+\\|)(.*?)\\]\\]", "$2")
		.replaceAll("<math>.*?</math>","")
		.replaceAll("'{2,10}","")
		.replaceAll("<.*?>","");		
	}
	
	public static Alttitles deserializeAltTitle(byte[] serialized){
		Buffer b = new Buffer(serialized);
		Alttitles t = new Alttitles();
		while(b.hasMore()){
			Object[] ret = b.readAlttitleInfo();
			int type = (Integer)ret[0];
			Info info = (Info)ret[1];
			if(type == 0)
				t.title = info;
			else if(type == 1)
				t.redirects.add(info);
			else if(type == 2)
				t.sections.add(info);
			else
				throw new RuntimeException("Wrong type for serialized alttitle "+type);
		}
		return t;		
	}

	public ArrayList<Info> getRedirects() {
		return redirects;
	}

	public void setRedirects(ArrayList<Info> redirects) {
		this.redirects = redirects;
	}

	public Info getTitle() {
		return title;
	}

	public void setTitle(Info title) {
		this.title = title;
	}

	public ArrayList<Info> getSections() {
		return sections;
	}

	public void setSections(ArrayList<Info> sections) {
		this.sections = sections;
	}
	
	
	
	

}
