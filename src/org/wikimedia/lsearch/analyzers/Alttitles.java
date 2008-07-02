package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.Set;

import org.apache.lucene.analysis.Analyzer;
import org.wikimedia.lsearch.analyzers.Aggregate.Flags;
import org.wikimedia.lsearch.analyzers.ExtToken.Position;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.Redirect;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.util.Buffer;
import org.wikimedia.lsearch.util.Utf8Set;

/**
 * Highlighting info on titles, redirects and sections in an article.
 * This object gets serialized/deserialized in the highlight index. 
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
		protected ArrayList<ExtToken> tokens; // highlight tokens
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
	/**
	 * Serialize alttitle for highlighting, serializies titles, redirects, sections.
	 * Writes original names + highlight tokens. 
	 * 
	 * @param article  
	 * @param iid      target iid
	 * @param sections sections from the tokenizer
	 * @param analyzer highlight analyzer
	 * @param field    field name on which data is analyzed 
	 * @return
	 * @throws IOException
	 */
	public static byte[] serializeAltTitle(Article article, IndexId iid, Collection<String> sections, Analyzer analyzer, String field) throws IOException{		
		Buffer b = new Buffer();
		
		// add title
		String title = article.getTitle();
		String titleKey = article.getTitleObject().getKey();
		// type 0 : title
		b.writeAlttitleInfo(titleKey,new Aggregate(title,article.getRank(),iid,analyzer,field,Flags.ALTTITLE),0);
		// add all redirects
		ArrayList<Redirect> redirects = article.getRedirectsSorted();
		ArrayList<Integer> ranks = article.getRedirectKeywordRanks();
		for(int i=0;i<redirects.size();i++){
			// type 1: redirect
			b.writeAlttitleInfo(redirects.get(i).getKey(),new Aggregate(redirects.get(i).getTitle(),ranks.get(i),iid,analyzer,field,Flags.ALTTITLE),1);
		}
		
		// type 2: sections
		for(String s : sections){
			String cs = canonizeHeadline(s);
			if(cs != null)
				b.writeAlttitleInfo(cs,new Aggregate(cs,1,iid,analyzer,field,Flags.SECTION),2);
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
	
	/**
	 * Deserialize alttitle highlight info
	 *  
	 * @param serialized
	 * @param terms terms to destub
	 * @param posMap used to deserialize positions
	 * @return
	 */
	public static Alttitles deserializeAltTitle(byte[] serialized, Utf8Set terms, HashMap<Integer,Position> posMap){
		Buffer b = new Buffer(serialized);
		Alttitles t = new Alttitles();
		while(b.hasMore()){
			Object[] ret = b.readAlttitleInfo(terms,posMap);
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
