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

namespace MediaWiki.Search {

	/*
	import java.io.IOException;
	import java.io.Reader;
	import java.io.StringReader;

	import org.apache.lucene.analysis.Analyzer;
	import org.apache.lucene.analysis.LowerCaseTokenizer;
	import org.apache.lucene.analysis.Token;
	import org.apache.lucene.analysis.TokenStream;
	*/
	using System;
	using System.IO;
	using System.Text.RegularExpressions;
	
	using Lucene.Net.Analysis;

	public class EsperantoAnalyzer : Analyzer {
		public override TokenStream TokenStream(string fieldName, TextReader reader) {
			return new EsperantoStemFilter(new LowerCaseTokenizer(reader));
		}
	}
	
	public class EsperantoStemFilter : TokenFilter {
		public EsperantoStemFilter(TokenStream tokenizer) : base(tokenizer) {
		}
		
		public override Token Next() {
			Token token = input.Next();
			if (token == null)
				return token;
			string stripped = StripWord(token.TermText());
			if (stripped.Equals(token.TermText()))
				return token;
			else
				return new Token(stripped, token.StartOffset(), token.EndOffset());
		}
		
		private static Regex[] patterns;
		static EsperantoStemFilter() {
			/*
			 * Malgrandaj kaj specialaj vortoj 
			 */
			Regex particles = new Regex(
					"^(ajn|da|de|du|tri|kaj|ke|ho|la|ne|pri|pro" +
					"|se|tra|tri|tre|tro|unu|ve|\u0109e|\u0109i)$");
			
			/*
			 * Pronomoj.
			 * Posedaj formoj (ekz 'mian') kaptigxos poste per 'suffixes'.
			 */
			Regex pronouns = new Regex(
					"^(mi|ni|ci|vi|si|li|\u011di|\u015di)n?$");
			
			/*
			 * Ho diable... la korelativoj!
			 * -a, -o, -e formoj kaptigxos en 'suffixes'.
			 * Eta konflikto staras inter partiklo 'cxi' kaj 'cxi-' korelativo.
			 */
			Regex correlatives = new Regex(
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
			Regex suffixes = new Regex("^(.+)(i|[iao]s|u|[ao]j?n?|en?)$");
			
			patterns = new Regex[] {particles, pronouns, correlatives, suffixes};
		}
		
		private static string StripWord(string word) {
			foreach (Regex pattern in patterns) {
				Match matcher = pattern.Match(word);
				if (matcher.Success) {
					return matcher.Groups[1].ToString();
				}
			}
			return word;
		}
	}

}
