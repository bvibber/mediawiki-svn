// project created on 5/5/2005 at 5:02 AM
using System;
using System.IO;

using Lucene.Net.Analysis;

using MediaWiki.Search;
using MediaWiki.Search.Daemon;

class MainClass
{
	public static void Main(string[] args) {
		testEsperantoAnalyzer();
		testQueryString();
	}
	
	static void testEsperantoAnalyzer() {
		// Sample text from a 1906 speech by Zamenhof.
		// Should be public domain. ;)
		string testText = "Estimataj sinjorinoj kaj sinjoroj! Mi esperas, ke " +
			"mi plenumos la deziron de ĉiuj alestantoj, se en la momento de " +
			"la malfermo de nia dua kongreso mi esprimos en la nomo de vi " +
			"ĉiuj mian koran dankon al la brava Svisa lando por la gastameco, " +
			"kiun ĝi montris al nia kongreso, kaj al lia Moŝto la Prezidanto " +
			"de la Svisa Konfederacio, kiu afable akceptis antaŭ du monatoj " +
			"nian delegitaron. Apartan saluton al la urbo Ĝenevo, kiu jam " +
			"multajn fojojn glore enskribis sian nomon en la historion de " +
			"diversaj gravaj internaciaj aferoj.";
		Analyzer analyzer = new EsperantoAnalyzer();
		TokenStream tokens = analyzer.TokenStream("contents", new StringReader(testText));
		try {
			for (Token token = tokens.Next(); token != null; token = tokens.Next()) {
				Console.WriteLine(token.TermText());
			}
			Console.WriteLine("<end>");
			tokens.Close();
		} catch (IOException e) {
			Console.WriteLine(e);
		}
	}

	static void testQueryString() {
		try {
			testURI("/x");
			testURI("/x?foo=bar");
			testURI("/x?foo=bar&biz=bax");
			
			// The %26 should _not_ split 'foo' from 'bogo'
			testURI("/x?foo=bar+%26bogo&next=extreme");
			
			// UTF-8 good encoding
			testURI("/x?serveuse=%c3%a9nid");
			
			// bad encoding; you'll see replacement char
			testURI("/x?serveuse=%e9nid");
			
			// corner cases; missing params
			testURI("/x?foo");
			testURI("/x?foo&bar=baz");
			testURI("/x?foo&&bar");
			testURI("/x?&");
			testURI("/x?=");
			testURI("/x?==&");
		} catch (UriFormatException e) {
			Console.WriteLine(e.ToString());
		}
	}
	
	static void testURI(string uri) {
		QueryStringMap map = new QueryStringMap(new Uri("http://localhost" + uri));
		Console.WriteLine(uri);
		foreach (string key in map.Keys) {
			 Console.WriteLine("  \"" + key + "\" => \"" + map[key] + "\"");
		}
	}

}