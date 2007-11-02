/*
 * Copyright 2004 Kate Turner
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
 * copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in 
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * $Id: EnglishAnalyzer.java 6911 2005-01-03 20:39:19Z vibber $
 */
package org.wikimedia.lsearch.test;

import java.io.Reader;
import java.util.HashMap;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.TokenStream;
import org.wikimedia.lsearch.analyzers.TokenizerOptions;
import org.wikimedia.lsearch.analyzers.WikiTokenizer;
import org.wikimedia.lsearch.config.IndexId;

/**
 * @author Kate Turner
 *
 */
@Deprecated
public class EnglishAnalyzer extends Analyzer {
	static org.apache.log4j.Logger log = Logger.getLogger(EnglishAnalyzer.class);
	protected HashMap<String,TokenStream> streams =  new HashMap<String,TokenStream>();
		
	/** 
	 * The token stream will be returned for the corresponding field via
	 * the <code>tokenStream</code> function  
	 * @param fieldName
	 * @param tokenStream
	 */
	public void registerTokenStream(String fieldName, TokenStream tokenStream){
		streams.put(fieldName,tokenStream);
	}
	
	public TokenStream tokenStream(String fieldName, Reader reader) {
		throw new UnsupportedOperationException("Use tokenStream(String,String)");
	}

	public final TokenStream tokenStream(String fieldName, String text) {
		if(streams.get(fieldName) != null)
			return streams.get(fieldName);
 
		return new AliasPorterStemFilter(new WikiTokenizer(text,IndexId.get("enwiki"),new TokenizerOptions(false)));		
	}
}
