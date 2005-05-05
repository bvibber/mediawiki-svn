// project created on 5/5/2005 at 5:02 AM
using System;
using System.IO;

using Lucene.Net.Analysis;

using MediaWiki.Search;

class MainClass
{
	public static void Main(string[] args) {
		testEsperantoAnalyzer();
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
}