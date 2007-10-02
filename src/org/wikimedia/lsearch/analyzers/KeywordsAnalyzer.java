package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;
import java.util.ArrayList;
import java.util.HashSet;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.wikimedia.lsearch.config.IndexId;

/** 
 * Analyzer that builds a field with an array of keywords,
 * each keyword is separated by a large token gap, so it's
 * convenient to run SpanNearQueries on the field. Keywords
 * themselves are tokenized. E.g. 
 * 
 *  ("something different", "other") ->
 *  "something" +1 "different" +201 "other"
 * 
 * Currently used for field "keyword"
 * 
 * @author rainman
 *
 */
public class KeywordsAnalyzer extends Analyzer{
	static Logger log = Logger.getLogger(KeywordsAnalyzer.class);	
	protected KeywordsTokenStream[] tokensBySize = null;
	protected String prefix;
	protected IndexId iid;
	
	/** number of field to be generated, e.g. keyword1 for single-word keywords, 
	 * keyword2 for two-word keywords, etc ... the last field has all the remaining keys
	 */
	public static final int KEYWORD_LEVELS = 5;
	/** positional increment between different redirects */
	public static final int TOKEN_GAP = 201;

	protected KeywordsAnalyzer(){}
	
	public KeywordsAnalyzer(HashSet<String> keywords, FilterFactory filters, String prefix, boolean exactCase){
		ArrayList<String> k = new ArrayList<String>();
		if(keywords != null)
			k.addAll(keywords);
		init(k,filters,prefix,exactCase);
	}
	public KeywordsAnalyzer(ArrayList<String> keywords, FilterFactory filters, String prefix, boolean exactCase){
		init(keywords,filters,prefix,exactCase);
	}	
	
	protected void init(ArrayList<String> keywords, FilterFactory filters, String prefix, boolean exactCase) {
		this.prefix = prefix;
		this.iid = filters.getIndexId();
		tokensBySize = new KeywordsTokenStream[KEYWORD_LEVELS];
		if(keywords == null){
			// init empty token streams
			for(int i=0; i< KEYWORD_LEVELS; i++){
				tokensBySize[i] = new KeywordsTokenStream(null,filters,exactCase,TOKEN_GAP);			
			}	
			return;
		}
		ArrayList<ArrayList<String>> keywordsBySize = new ArrayList<ArrayList<String>>();
		for(int i=0;i<KEYWORD_LEVELS;i++)
			keywordsBySize.add(new ArrayList<String>());
		// arange keywords into a list by token number 
		for(String k : keywords){
			ArrayList<Token> parsed = new FastWikiTokenizerEngine(k,iid,exactCase).parse();
			if(parsed.size() == 0)
				continue;
			else if(parsed.size() < KEYWORD_LEVELS)
				keywordsBySize.get(parsed.size()-1).add(k);
			else
				keywordsBySize.get(KEYWORD_LEVELS-1).add(k);
		}		
		for(int i=0; i< KEYWORD_LEVELS; i++){
			tokensBySize[i] = new KeywordsTokenStream(keywordsBySize.get(i),filters,exactCase,TOKEN_GAP);			
		}
	}
	
	@Override
	public TokenStream tokenStream(String fieldName, Reader reader) {
		if(fieldName.startsWith(prefix)){
			int inx = Integer.parseInt(fieldName.substring(prefix.length()));
			return tokensBySize[inx-1];
		} else{
			log.error("Trying to get tokenStream for wrong field "+fieldName+", expecting "+prefix);
			return null;
		}
	}
	@Override
	public TokenStream tokenStream(String fieldName, String text) {
		return tokenStream(fieldName,(Reader)null);
	}
	
	class KeywordsTokenStream extends TokenStream {
		protected Analyzer analyzer;
		protected ArrayList<String> keywords;
		protected int index;
		protected String keyword;
		protected TokenStream tokens;
		protected int tokenGap;
		
		public KeywordsTokenStream(ArrayList<String> keywords, FilterFactory filters, boolean exactCase, int tokenGap){
			this.analyzer = new QueryLanguageAnalyzer(filters,exactCase);
			this.keywords = keywords;
			this.index = 0;
			this.keyword = null;
			this.tokens = null;
			this.tokenGap = tokenGap;
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
