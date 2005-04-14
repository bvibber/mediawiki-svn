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
import java.io.Reader;
import java.io.StringReader;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.LowerCaseTokenizer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;

public class EsperantoAnalyzer extends Analyzer{
	public final TokenStream tokenStream(String fieldName, Reader reader) {
		return new EsperantoStemFilter(new LowerCaseTokenizer(reader));
	}
	
	public static void main(String[] args) {
		// Sample text from a 1906 speech by Zamenhof.
		// Should be public domain. ;)
		String testText = "Estimataj sinjorinoj kaj sinjoroj! Mi esperas, ke " +
			"mi plenumos la deziron de ĉiuj alestantoj, se en la momento de " +
			"la malfermo de nia dua kongreso mi esprimos en la nomo de vi " +
			"ĉiuj mian koran dankon al la brava Svisa lando por la gastameco, " +
			"kiun ĝi montris al nia kongreso, kaj al lia Moŝto la Prezidanto " +
			"de la Svisa Konfederacio, kiu afable akceptis antaŭ du monatoj " +
			"nian delegitaron. Apartan saluton al la urbo Ĝenevo, kiu jam " +
			"multajn fojojn glore enskribis sian nomon en la historion de " +
			"diversaj gravaj internaciaj aferoj.";
		Analyzer analyzer = new EsperantoAnalyzer();
		TokenStream tokens = analyzer.tokenStream("contents", new StringReader(testText));
		try {
			for (Token token = tokens.next(); token != null; token = tokens.next()) {
				System.out.println(token.termText());
			}
			System.out.println("<end>");
			tokens.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}
}
