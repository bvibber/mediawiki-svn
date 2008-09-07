/*
Copyright © 2003,
Center for Intelligent Information Retrieval,
University of Massachusetts, Amherst.
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this
list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice,
this list of conditions and the following disclaimer in the documentation
and/or other materials provided with the distribution.

3. The names "Center for Intelligent Information Retrieval" and
"University of Massachusetts" must not be used to endorse or promote products
derived from this software without prior written permission. To obtain
permission, contact info@ciir.cs.umass.edu.

THIS SOFTWARE IS PROVIDED BY UNIVERSITY OF MASSACHUSETTS AND OTHER CONTRIBUTORS
"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
SUCH DAMAGE.
 */
package org.apache.lucene.analysis;

/**
 * <p>Title: </p>
 * <p>Description: This filter transforms an input word into its stemmed form
 * using Bob Krovetz' kstem algorithm.</p>
 * <p>Copyright: Copyright (c) 2003</p>
 * <p>Company: CIIR Umass Amherst (http://ciir.cs.umass.edu) </p>
 * @author Sergio Guzman-Lara
 * @version 1.0
 */

import java.io.IOException;

/** Transforms the token stream according to the KStem stemming algorithm.
 *  For more information about KStem see <a href="http://ciir.cs.umass.edu/pubfiles/ir-35.pdf">
    "Viewing Morphology as an Inference Process"</a>
    (Krovetz, R., Proceedings of the Sixteenth Annual International ACM SIGIR
    Conference on Research and Development in Information Retrieval, 191-203, 1993).

    Note: the input to the stemming filter must already be in lower case,
    so you will need to use LowerCaseFilter or LowerCaseTokenizer farther
    down the Tokenizer chain in order for this to work properly!
    <P>
    To use this filter with other analyzers, you'll want to write an
    Analyzer class that sets up the TokenStream chain as you want it.
    To use this with LowerCaseTokenizer, for example, you'd write an
    analyzer like this:
    <P>
    <PRE>
    class MyAnalyzer extends Analyzer {
      public final TokenStream tokenStream(String fieldName, Reader reader) {
        return new KStemStemFilter(new LowerCaseTokenizer(reader));
      }
    }
    </PRE>

 */

public final class KStemFilter extends TokenFilter {
	private KStemmer stemmer;

	/** Create a KStemmer with the given cache size.
	 * @param in The TokenStream whose output will be the input to KStemFilter.
	 *  @param cacheSize Maximum number of entries to store in the
	 *  Stemmer's cache (stems stored in this cache do not need to be
	 *  recomputed, speeding up the stemming process).
	 */
	public KStemFilter(TokenStream in, int cacheSize) {
		super(in);
		stemmer = new KStemmer(cacheSize);
	}

	/** Create a KStemmer with the default cache size of 20 000 entries.
	 * @param in The TokenStream whose output will be the input to KStemFilter.
	 */
	public KStemFilter(TokenStream in) {
		super(in);
		stemmer = new KStemmer();
	}

	/** Returns the next, stemmed, input Token.
	 *  @return The stemed form of a token.
	 *  @throws IOException
	 */
	public final Token next() throws IOException {
		Token token = input.next();
		if (token == null)
			return null;
		else {
			String s = stemmer.stem(token.termText());
			if (!s.equals(token.termText())) 
				return new Token(s, token.startOffset, token.endOffset, token.type);
			return token;
		}
	}
}
