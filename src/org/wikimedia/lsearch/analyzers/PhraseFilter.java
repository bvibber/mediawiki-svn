package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
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
 * 
 * @author rainman
 *
 */
public class PhraseFilter extends TokenFilter {
	protected Set<String> stopWords = null;
	
	public PhraseFilter(TokenStream input) {
		super(input);
	}
	
	protected Token phrase1 = null, phrase2 = null;
	protected boolean phraseReady = false;
	
	protected boolean forPhrase(Token t){
		if(stopWords!=null && stopWords.contains(t.termText()))
			return false;
		else
			return true;
	}
	
	@Override
	public Token next() throws IOException {
		if(phraseReady){
			phraseReady = false;
			return new Token(phrase1.termText()+"_"+phrase2.termText(),phrase1.startOffset(),phrase2.endOffset());
		}
		Token t = input.next();
		if(t == null)
			return null; // EOS
		if(!forPhrase(t))
			return t; // stop word, return as word only
		
		if(phrase1 == null){
			phrase1 = t;			
			return t;
		}
		if(phrase2 == null){
			phrase2 = t;
			phraseReady = true;
			return t;
		}
		
		phrase1 = phrase2;
		phrase2 = t;
		phraseReady = true;
		
		return t; // prepared phrase, return word, phrase in next call
	}

	public Set<String> getStopWords() {
		return stopWords;
	}

	public void setStopWords(Set<String> stopWords) {
		this.stopWords = stopWords;
	}

}
