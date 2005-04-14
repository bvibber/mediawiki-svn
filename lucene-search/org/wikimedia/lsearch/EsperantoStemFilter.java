/*
 * Copyright 2005 Brion Vibber
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
 * $Id$
 */

package org.wikimedia.lsearch;

import java.io.IOException;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.analysis.TokenFilter;


public class EsperantoStemFilter extends TokenFilter {
	public EsperantoStemFilter(TokenStream tokenizer) {
		super(tokenizer);
	}
	
	public Token next() throws IOException {
		Token token = input.next();
		if (token == null)
			return token;
		String stripped = stripWord(token.termText());
		if (stripped.equals(token.termText()))
			return token;
		else
			return new Token(stripped, token.startOffset(), token.endOffset());
	}
	
	private static Pattern[] patterns;
	static {
		/*
		 * Malgrandaj kaj specialaj vortoj 
		 */
		Pattern particles = Pattern.compile(
				"^(ajn|da|de|du|tri|kaj|ke|ho|la|ne|pri|pro" +
				"|se|tra|tri|tre|tro|unu|ve|\u0109e|\u0109i)$");
		
		/*
		 * Pronomoj.
		 * Posedaj formoj (ekz 'mian') kaptigxos poste per 'suffixes'.
		 */
		Pattern pronouns = Pattern.compile(
				"^(mi|ni|ci|vi|si|li|\u011di|\u015di)n?$");
		
		/*
		 * Ho diable... la korelativoj!
		 * -a, -o, -e formoj kaptigxos en 'suffixes'.
		 * Eta konflikto staras inter partiklo 'cxi' kaj 'cxi-' korelativo.
		 */
		Pattern correlatives = Pattern.compile(
				"^((?:k|t|\u0109|nen|)i)(?:uj?n?|es|el|al)$");
		
		/*
		 * Forprenu la finajxojn el oftspeca vorto:
		 *  -i   -- infinitivo
		 *  -is  \
		 *  -as   | verbo
		 *  -os   |
		 *  -u   /
		 *  -a   \
		 *  -an   | adjektivo
		 *  -aj   |
		 *  -ajn /
		 *  -o   \
		 *  -on   | substantivo
		 *  -oj   |
		 *  -ojn /
		 *  -e   \ -- adverbo
		 *  -en  /
		 */
		Pattern suffixes = Pattern.compile("^(.+)(i|[iao]s|u|[ao]j?n?|en?)$");
		
		patterns = new Pattern[] {particles, pronouns, correlatives, suffixes};
	}
	
	private static String stripWord(String word) {
		for (int i = 0; i < patterns.length; i++) {
			Matcher matcher = patterns[i].matcher(word);
			if (matcher.matches()) {
				return matcher.group(1);
			}
		}
		return word;
	}
}
