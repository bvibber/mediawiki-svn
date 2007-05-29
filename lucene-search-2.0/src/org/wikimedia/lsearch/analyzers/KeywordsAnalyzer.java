package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;
import java.util.ArrayList;
import java.util.HashSet;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;

/** 
 * Analyzer that builds a field with an array of keywords,
 * each keyword is separated by a large token gap, so it's
 * convenient to run SpanNearQueries on the field. Keywords
 * themselves are tokenized. E.g. 
 * 
 *  ("something different", "other") ->
 *  "something" +1 "different" +201 "other"
 * 
 * Currently used for fields "redirect" and "keyword"
 * 
 * @author rainman
 *
 */
public class KeywordsAnalyzer extends Analyzer{
	static Logger log = Logger.getLogger(KeywordsAnalyzer.class);
	protected ArrayList<String> keywords;
	protected FilterFactory filters;
	protected KeywordsTokenStream tokens;

	public KeywordsAnalyzer(HashSet<String> keywords, FilterFactory filters){
		ArrayList<String> k = new ArrayList<String>();
		k.addAll(keywords);
		tokens = new KeywordsTokenStream(k,filters);
	}
	
	public KeywordsAnalyzer(ArrayList<String> keywords, FilterFactory filters){
		tokens = new KeywordsTokenStream(keywords,filters);
	}
	/** positional increment between different redirects */
	public static final int tokenGap = 201;
	
	@Override
	public TokenStream tokenStream(String fieldName, Reader reader) {
		return tokens;		
	}
	@Override
	public TokenStream tokenStream(String fieldName, String text) {
		return tokens;
	}
	
	class KeywordsTokenStream extends TokenStream {
		protected Analyzer analyzer;
		protected ArrayList<String> keywords;
		protected int index;
		protected String keyword;
		protected TokenStream tokens;
		
		public KeywordsTokenStream(ArrayList<String> keywords, FilterFactory filters){
			this.analyzer = new QueryLanguageAnalyzer(filters);
			this.keywords = keywords;
			this.index = 0;
			this.keyword = null;
			this.tokens = null;
		}
		@Override
		public Token next() throws IOException {
			if(keywords == null)
				return null; // nothing to do
			Token t;
			if(keyword == null){
				t = openNext();
				return t;
			}
			if(keyword != null && tokens!=null){
				t = tokens.next();
				if(t == null){
					t = openNext();
					if(t != null)
						t.setPositionIncrement(tokenGap);
				}
				return t;
			} else{
				log.warn("Inconsistent state: key="+keyword+", tokens="+tokens);
			}
			return null;
		}
		
		protected Token openNext() throws IOException {
			Token t;
			if(index >= keywords.size())
				return null; // processed all keywords
			// try subsequent keyword titles until find one with
			// title that can be tokenized
			do{
				// next keyword title
				keyword = keywords.get(index++);
				tokens = analyzer.tokenStream("",keyword);
				// try to tokenize
				t = tokens.next();
				if(t == null && index == keywords.size())
					return null; // last token
				else if(t!=null)
					return t;
			} while(keyword == null);
			return null;
		}
		
	}

}
