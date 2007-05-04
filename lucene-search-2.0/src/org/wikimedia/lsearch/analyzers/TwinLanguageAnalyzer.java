package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;
import java.util.ArrayList;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.wikimedia.lsearch.analyzers.LanguageAnalyzer.ArrayTokens;

/**
 * Class similar to {@link LanguageAnalyzer}, except it produces
 * stemmed words in a seperate field "stemmed".
 * 
 * @author rainman
 *
 */
public class TwinLanguageAnalyzer extends LanguageAnalyzer {
	ArrayList<Token> stemmed = new ArrayList<Token>();

	public TwinLanguageAnalyzer(FilterFactory filters, WikiTokenizer wikitokenizer) {
		super(filters, wikitokenizer);
	}
	
	/**
	 * Takes two field: contents and stemmed. First has the original
	 * filtered text. The other only stemmed words.
	 */
	@Override
	public TokenStream tokenStream(String fieldName, Reader reader) {
		wikitokenizer.resetIterator();
		ArrayList<Token> tokens = wikitokenizer.getTokens();
		if(filters.hasCustomFilter())
			tokens = applyCustomFilter(tokens);
		if(filters.hasStemmer()){
			stemmed = getStemmedWords(tokens);
		}
		
		if(fieldName.equals("contents"))
			return new ArrayTokens(tokens);
		else if(fieldName.equals("stemmed"))
			return new ArrayTokens(stemmed);
		else{
			log.warn("Unrecognized field: "+fieldName);
			return null;
		}
		
	}

	protected ArrayList<Token> getStemmedWords(ArrayList<Token> tokens) {
		ArrayList<Token> ret = new ArrayList<Token>();
		TokenStream input = new ArrayTokens(tokens);
		TokenStream stemmer = filters.makeStemmer(new ArrayTokens(tokens));
		try{
			Token original=null,stemmed=null;
			do{
				original = input.next();
				stemmed = stemmer.next();
				if(original != stemmed){ // need to be same object if unstemmed
					stemmed.setPositionIncrement(1);
					ret.add(stemmed);
				}
			} while(stemmed != null && original != null);
		} catch(IOException e){
			log.warn("I/O error processing stemmed words");
		}
		return ret;
	}

}
