package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashSet;
import java.util.Set;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;

/**
 * Filter that outputs phrases and words mixed, e.g.
 * novi sad is a city -> novi, sad, novi_sad, is, sad_is, a, is_a, city, a_city
 * Note: outputs only unique tokens
 * 
 * @author rainman
 *
 */
public class PhraseFilter extends TokenFilter {
	protected Set<String> stopWords = null;
	protected FilterFactory filters = null;
	/** 
	 * since we are only concerned about term document frequencies we will output
	 * only unique tokens to help reduce index size 
	 */
	protected HashSet<String> processed = new HashSet<String>();
	
	public PhraseFilter(TokenStream input) {
		super(input);
	}
	
	/** phrases, begin and end in non-stop word */
	protected Token phrase1 = null, phrase2 = null;
	protected boolean phraseReady = false;
	protected String gap = "_";
	/** pairs of words, two adjecent words */
	protected Token pair1 = null, pair2 = null;
	protected boolean pairReady = false;
	protected Token nextToken = null;
	protected String field = null;
	protected Token canonized = null;
	
	protected boolean forPhrase(Token t){
		if(stopWords!=null && stopWords.contains(t.termText()))
			return false;
		else
			return true;
	}
	
	@Override
	public Token next() throws IOException {
		for(;;){ // output only unique
			if(canonized != null){
				if(!processed.contains(canonized.termText())){
					processed.add(canonized.termText());
					Token t = canonized;
					canonized = null;
					return t;
				}
			}
			
			Token t = nextPhraseOrWord();
			if(t == null)
				return null; // EOS
			if(filters != null)
				canonized = filters.canonizeToken(t);
			
			if(!processed.contains(t.termText())){
				processed.add(t.termText());
				return t;
			}
		}
	}
	
	/**
	 * Outputs both pair of words, and longer phrases, e.g. for a stream (where "for" is stop word)
	 * test for something will produce: test, for, something, test_for, for_something and test_for_something
	 * 
	 * @return
	 * @throws IOException
	 */
	public Token nextPhraseOrWord() throws IOException {
		if(pairReady && pair1!=null && pair2!=null && stopWords!=null && 
				(stopWords.contains(pair1.termText()) || stopWords.contains(pair2.termText()))){
			pairReady = false;
			return new Token(pair1.termText()+"_"+pair2.termText(),pair1.startOffset(),pair2.endOffset());
		}
		if(phraseReady){
			phraseReady = false;
			String g = gap;
			gap = "_";
			return new Token(phrase1.termText()+g+phrase2.termText(),phrase1.startOffset(),phrase2.endOffset());
		}
		Token t = input.next();
		if(t == null)
			return null; // EOS
		// don't put transliterations and aliases into phrases
		if(t.getPositionIncrement()==0)
			return t;

		// keep pairs of successive words (only for titles!!)
		if( "title".equals(field) ){
			if(pair1 == null)
				pair1 = t;
			else if(pair2 == null){
				pair2 = t;
				pairReady = true;
			} else{
				pair1 = pair2;
				pair2 = t;
				pairReady = true;
			}		
		}
		if(!forPhrase(t)){
			if(phrase1 != null){
				gap = gap+t.termText()+"_";
			}
						
			return t; // stop word, return as word only
		}
		
		if(phrase1 == null){
			phrase1 = t;		
			return t;
		}
		else if(phrase2 == null){
			phrase2 = t;
			phraseReady = true;
			return t;
		} else{			
			phrase1 = phrase2;
			phrase2 = t;
			phraseReady = true;
			
			return t; // prepared phrase, return word, phrase in next call
		}
	}

	public Set<String> getStopWords() {
		return stopWords;
	}

	public void setStopWords(Set<String> stopWords) {
		this.stopWords = stopWords;
	}

	public FilterFactory getFilters() {
		return filters;
	}

	public void setFilters(FilterFactory filters) {
		this.filters = filters;
	}

	public void setField(String field) {
		this.field = field;
	}

	
	

}
